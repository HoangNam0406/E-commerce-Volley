<?php
/**
 * Cart Model
 */
class Cart extends Model {
    protected $table = 'carts';

    /**
     * Get cart items for customer
     */
    public function getCustomerCart($customerId) {
        $db = Database::getInstance()->getConnection();
        $query = "
            SELECT c.*, p.*, p.id as product_id, c.id as cart_id,
                   CASE 
                       WHEN p.discount_percentage > 0 
                            AND p.discount_start_date <= NOW() 
                            AND p.discount_end_date >= NOW()
                       THEN p.price - (p.price * p.discount_percentage / 100)
                       ELSE p.price
                   END as current_price
            FROM carts c
            JOIN products p ON c.product_id = p.id
            WHERE c.customer_id = :customer_id AND p.status = 'active'
            ORDER BY c.created_at DESC
        ";
        $stmt = $db->prepare($query);
        $stmt->execute([':customer_id' => $customerId]);
        return $stmt->fetchAll();
    }

    /**
     * Add to cart
     */
    public function addToCart($customerId, $productId, $quantity = 1) {
        $existing = $this->findOne([
            'customer_id' => $customerId,
            'product_id' => $productId
        ]);

        if ($existing) {
            return $this->update($existing['id'], [
                'quantity' => $existing['quantity'] + $quantity
            ]);
        } else {
            return $this->create([
                'customer_id' => $customerId,
                'product_id' => $productId,
                'quantity' => $quantity
            ]);
        }
    }

    /**
     * Remove from cart
     */
    public function removeFromCart($cartId, $customerId) {
        $db = Database::getInstance()->getConnection();
        $query = "DELETE FROM carts WHERE id = :id AND customer_id = :customer_id";
        $stmt = $db->prepare($query);
        return $stmt->execute([
            ':id' => $cartId,
            ':customer_id' => $customerId
        ]);
    }

    /**
     * Update cart item quantity
     */
    public function updateQuantity($cartId, $customerId, $quantity) {
        if ($quantity <= 0) {
            return $this->removeFromCart($cartId, $customerId);
        }

        $db = Database::getInstance()->getConnection();
        $query = "UPDATE carts SET quantity = :quantity WHERE id = :id AND customer_id = :customer_id";
        $stmt = $db->prepare($query);
        return $stmt->execute([
            ':quantity' => $quantity,
            ':id' => $cartId,
            ':customer_id' => $customerId
        ]);
    }

    /**
     * Clear cart
     */
    public function clearCart($customerId) {
        $db = Database::getInstance()->getConnection();
        $query = "DELETE FROM carts WHERE customer_id = :customer_id";
        $stmt = $db->prepare($query);
        return $stmt->execute([':customer_id' => $customerId]);
    }

    /**
     * Get cart total
     */
    public function getCartTotal($customerId) {
        $cartItems = $this->getCustomerCart($customerId);
        $total = 0;

        foreach ($cartItems as $item) {
            $total += $item['current_price'] * $item['quantity'];
        }

        return $total;
    }

    /**
     * Count cart items
     */
    public function countItems($customerId) {
        return $this->count(['customer_id' => $customerId]);
    }
}
?>
