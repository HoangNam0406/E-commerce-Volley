<?php
/**
 * Wallet Model
 */
class Wallet extends Model {
    protected $table = 'wallets';

    /**
     * Get wallet by user ID
     */
    public function getByUserId($userId) {
        return $this->findOne(['user_id' => $userId]);
    }

    /**
     * Deposit money
     */
    public function deposit($userId, $amount, $description = null) {
        $wallet = $this->getByUserId($userId);
        if (!$wallet) {
            return false;
        }

        return $this->addTransaction(
            $wallet['id'],
            'deposit',
            $amount,
            $description,
            $wallet['balance'],
            $wallet['balance'] + $amount
        );
    }

    /**
     * Withdraw money
     */
    public function withdraw($userId, $amount, $description = null) {
        $wallet = $this->getByUserId($userId);
        if (!$wallet || $wallet['balance'] < $amount) {
            return false;
        }

        return $this->addTransaction(
            $wallet['id'],
            'withdrawal',
            $amount,
            $description,
            $wallet['balance'],
            $wallet['balance'] - $amount
        );
    }

    /**
     * Commission/settlement from order
     */
    public function addCommission($userId, $amount, $orderId) {
        $wallet = $this->getByUserId($userId);
        if (!$wallet) {
            return false;
        }

        $result = $this->addTransaction(
            $wallet['id'],
            'commission',
            $amount,
            "Order settlement #$orderId",
            $wallet['balance'],
            $wallet['balance'] + $amount,
            $orderId,
            'order'
        );

        if ($result) {
            // Update wallet balance
            $this->update($wallet['id'], [
                'balance' => $wallet['balance'] + $amount
            ]);
        }

        return $result;
    }

    /**
     * Refund
     */
    public function refund($userId, $amount, $orderId = null) {
        $wallet = $this->getByUserId($userId);
        if (!$wallet) {
            return false;
        }

        $result = $this->addTransaction(
            $wallet['id'],
            'refund',
            $amount,
            "Order refund" . ($orderId ? " #$orderId" : ""),
            $wallet['balance'],
            $wallet['balance'] + $amount,
            $orderId,
            'order'
        );

        if ($result) {
            // Update wallet balance
            $this->update($wallet['id'], [
                'balance' => $wallet['balance'] + $amount
            ]);
        }

        return $result;
    }

    /**
     * Add transaction
     */
    private function addTransaction(
        $walletId,
        $type,
        $amount,
        $description = null,
        $balanceBefore = 0,
        $balanceAfter = 0,
        $referenceId = null,
        $referenceType = null
    ) {
        $db = Database::getInstance()->getConnection();
        $query = "
            INSERT INTO wallet_transactions 
            (wallet_id, type, amount, description, balance_before, balance_after, reference_id, reference_type, status)
            VALUES (:wallet_id, :type, :amount, :description, :balance_before, :balance_after, :reference_id, :reference_type, 'completed')
        ";
        
        $stmt = $db->prepare($query);
        return $stmt->execute([
            ':wallet_id' => $walletId,
            ':type' => $type,
            ':amount' => $amount,
            ':description' => $description,
            ':balance_before' => $balanceBefore,
            ':balance_after' => $balanceAfter,
            ':reference_id' => $referenceId,
            ':reference_type' => $referenceType
        ]);
    }

    /**
     * Get transactions
     */
    public function getTransactions($walletId, $limit = null, $offset = 0) {
        $db = Database::getInstance()->getConnection();
        $query = "SELECT * FROM wallet_transactions WHERE wallet_id = :wallet_id ORDER BY created_at DESC";
        
        if ($limit) {
            $query .= " LIMIT :limit OFFSET :offset";
        }

        $stmt = $db->prepare($query);
        $stmt->bindValue(':wallet_id', $walletId);
        
        if ($limit) {
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get wallet balance
     */
    public function getBalance($userId) {
        $wallet = $this->getByUserId($userId);
        return $wallet['balance'] ?? 0;
    }
}
?>
