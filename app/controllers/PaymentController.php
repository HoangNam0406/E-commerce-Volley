<?php
/**
 * Payment Controller - VNPay Integration
 */
class PaymentController extends Controller {

    /**
     * Redirect to VNPay
     */
    public function vnpay() {
        $this->requireAuth();

        $orderIds = $_SESSION['order_ids'] ?? [];

        if (empty($orderIds)) {
            redirectWithMessage(APP_URL . '/orders', 'No orders to pay', 'error');
        }

        // Calculate total amount to pay
        $orderModel = new Order();
        $totalAmount = 0;

        foreach ($orderIds as $orderId) {
            $order = $orderModel->findById($orderId);
            if ($order) {
                $totalAmount += $order['total_amount'];
            }
        }

        // Create VNPay request
        $vnpayUrl = $this->buildVNPayUrl($totalAmount, $orderIds);

        $this->redirect($vnpayUrl);
    }

    /**
     * Build VNPay URL
     */
    private function buildVNPayUrl($amount, $orderIds) {
        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => VNPAY_TMNCODE,
            "vnp_Amount" => $amount * 100, // VNPay uses cents
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $_SERVER['REMOTE_ADDR'],
            "vnp_Locale" => "vn",
            "vnp_OrderInfo" => "Order: " . implode(',', $orderIds),
            "vnp_OrderType" => "other",
            "vnp_ReturnUrl" => VNPAY_RETURN_URL,
            "vnp_TxnRef" => date('YmdHis') . rand(1000, 9999),
        );

        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";

        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = VNPAY_URL . "?" . $query;
        if (isset($inputData['vnp_SecureHash'])) {
            $vnp_Url .= 'vnp_SecureHash=' . $inputData['vnp_SecureHash'];
        }

        $vnpSecureHash = hash_hmac('sha512', $hashdata, VNPAY_HASHSECRET);
        $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;

        return $vnp_Url;
    }

    /**
     * VNPay return callback
     */
    public function vnpayReturn() {
        $this->requireAuth();

        $inputData = array();
        foreach ($_GET as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }

        $vnpSecureHash = $inputData["vnp_SecureHash"];
        unset($inputData["vnp_SecureHash"]);

        ksort($inputData);
        $hashdata = "";
        $i = 0;

        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashdata, VNPAY_HASHSECRET);
        $vnpTranId = $inputData["vnp_TransactionNo"];
        $vnpResponseCode = $inputData["vnp_ResponseCode"];

        if ($secureHash == $vnpSecureHash) {
            if ($vnpResponseCode == "00") {
                // Payment successful
                $this->processPaymentSuccess($inputData);
            } else {
                // Payment failed
                redirectWithMessage(APP_URL . '/orders', 'Payment failed', 'error');
            }
        } else {
            redirectWithMessage(APP_URL . '/orders', 'Payment verification failed', 'error');
        }
    }

    /**
     * Process successful payment
     */
    private function processPaymentSuccess($inputData) {
        $orderIds = explode(',', str_replace('Order: ', '', $inputData['vnp_OrderInfo']));
        $orderModel = new Order();
        $walletModel = new Wallet();

        try {
            $db = Database::getInstance()->getConnection();
            $db->beginTransaction();

            foreach ($orderIds as $orderId) {
                $orderId = (int)trim($orderId);
                $order = $orderModel->findById($orderId);

                if ($order) {
                    // Update order payment status
                    $orderModel->updatePaymentStatus($orderId, 'paid');
                    $orderModel->updateStatus($orderId, 'confirmed');

                    // Record VNPay transaction
                    $this->recordVNPayTransaction($orderId, $inputData);

                    // Add commission to seller wallet
                    $walletModel->addCommission(
                        $order['seller_id'],
                        $order['seller_amount'],
                        $orderId
                    );
                }
            }

            $db->commit();

            $_SESSION['order_ids'] = $orderIds;
            $this->redirect(APP_URL . '/orders/success');
        } catch (Exception $e) {
            $db->rollBack();
            redirectWithMessage(APP_URL . '/orders', 'Error processing payment', 'error');
        }
    }

    /**
     * Record VNPay transaction
     */
    private function recordVNPayTransaction($orderId, $inputData) {
        $db = Database::getInstance()->getConnection();
        $query = "
            INSERT INTO vnpay_transactions (order_id, amount, transaction_ref, bank_code, status, response_code)
            VALUES (:order_id, :amount, :transaction_ref, :bank_code, 'success', :response_code)
        ";

        $stmt = $db->prepare($query);
        $stmt->execute([
            ':order_id' => $orderId,
            ':amount' => (int)$inputData['vnp_Amount'] / 100,
            ':transaction_ref' => $inputData['vnp_TransactionNo'],
            ':bank_code' => $inputData['vnp_BankCode'] ?? '',
            ':response_code' => $inputData['vnp_ResponseCode']
        ]);
    }
}
?>
