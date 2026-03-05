<?php
/**
 * Order Model
 */
class Order extends Model {
    protected $table = 'orders';

    /**
     * Generate order number
     */
    public function generateOrderNumber() {
        return 'ORD-' . date('YmdHis') . '-' . rand(1000, 9999);
    }

    /**
     * Create order with items
     */
    public function createOrderWithItems($orderData, $items) {
        $db = Database::getInstance()->getConnection();
        
        try {
            $db->beginTransaction();

            // Create order
            $this->create($orderData);
            $orderId = $db->lastInsertId();

            // Add order items
            $query = "INSERT INTO order_items (order_id, product_id, quantity, price, discount_percentage) 
                      VALUES (:order_id, :product_id, :quantity, :price, :discount_percentage)";
            $stmt = $db->prepare($query);

            foreach ($items as $item) {
                $stmt->execute([
                    ':order_id' => $orderId,
                    ':product_id' => $item['product_id'],
                    ':quantity' => $item['quantity'],
                    ':price' => $item['price'],
                    ':discount_percentage' => $item['discount_percentage'] ?? 0
                ]);
            }

            // Update product stock
            $productModel = new Product();
            foreach ($items as $item) {
                $product = $productModel->findById($item['product_id']);
                $productModel->update($item['product_id'], [
                    'stock_quantity' => $product['stock_quantity'] - $item['quantity']
                ]);
            }

            $db->commit();
            return $orderId;
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    /**
     * Get customer orders
     */
    public function getCustomerOrders($customerId, $limit = null, $offset = 0) {
        return $this->findBy(['customer_id' => $customerId], $limit, $offset);
    }

    /**
     * Get seller orders
     */
    public function getSellerOrders($sellerId, $limit = null, $offset = 0) {
        $query = "SELECT o.*, u.full_name as customer_name 
                  FROM {$this->table} o
                  JOIN users u ON o.customer_id = u.id
                  WHERE o.seller_id = :seller_id
                  ORDER BY o.created_at DESC";
        
        if ($limit) {
            $query .= " LIMIT :limit OFFSET :offset";
        }

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':seller_id', $sellerId);
        
        if ($limit) {
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get order items
     */
    public function getOrderItems($orderId) {
        $db = Database::getInstance()->getConnection();
        $query = "
            SELECT oi.*, p.name, p.image, u.id as seller_id, u.full_name as seller_name
            FROM order_items oi
            JOIN products p ON oi.product_id = p.id
            JOIN users u ON p.seller_id = u.id
            WHERE oi.order_id = :order_id
        ";
        $stmt = $db->prepare($query);
        $stmt->execute([':order_id' => $orderId]);
        return $stmt->fetchAll();
    }

    /**
     * Update order status
     */
    public function updateStatus($orderId, $status) {
        return $this->update($orderId, ['status' => $status]);
    }

    /**
     * Update payment status
     */
    public function updatePaymentStatus($orderId, $paymentStatus) {
        return $this->update($orderId, ['payment_status' => $paymentStatus]);
    }

    /**
     * Cancel order
     */
    public function cancelOrder($orderId, $reason = null) {
        $db = Database::getInstance()->getConnection();
        
        try {
            $db->beginTransaction();

            // Get order
            $order = $this->findById($orderId);
            
            // Update order status
            $this->update($orderId, [
                'status' => 'cancelled',
                'cancellation_reason' => $reason
            ]);

            // Refund stock
            $orderItems = $this->getOrderItems($orderId);
            $productModel = new Product();
            
            foreach ($orderItems as $item) {
                $product = $productModel->findById($item['product_id']);
                $productModel->update($item['product_id'], [
                    'stock_quantity' => $product['stock_quantity'] + $item['quantity']
                ]);
            }

            // Process refund if payment was made
            if ($order['payment_status'] === 'paid') {
                $walletModel = new Wallet();
                $walletModel->refund($order['customer_id'], $order['total_amount']);
            }

            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    /**
     * Count orders by status
     */
    public function countByStatus($status) {
        return $this->count(['status' => $status]);
    }

    /**
     * Get revenue
     */
    public function getTotalRevenue() {
        $db = Database::getInstance()->getConnection();
        $query = "
            SELECT SUM(total_amount) as total 
            FROM orders 
            WHERE status IN ('confirmed', 'shipped', 'delivered')
            AND payment_status = 'paid'
        ";
        $stmt = $db->query($query);
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }

    /**
     * Get seller revenue
     */
    public function getSellerRevenue($sellerId) {
        $db = Database::getInstance()->getConnection();
        $query = "
            SELECT SUM(seller_amount) as total 
            FROM orders 
            WHERE seller_id = :seller_id
            AND status IN ('confirmed', 'shipped', 'delivered')
            AND payment_status = 'paid'
        ";
        $stmt = $db->prepare($query);
        $stmt->execute([':seller_id' => $sellerId]);
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }
}
?>
