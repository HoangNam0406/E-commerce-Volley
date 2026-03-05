<?php
/**
 * Wishlist Model
 */
class Wishlist extends Model {
    protected $table = 'wishlists';

    /**
     * Get customer wishlist
     */
    public function getCustomerWishlist($customerId) {
        $db = Database::getInstance()->getConnection();
        $query = "
            SELECT p.* FROM wishlists w
            JOIN products p ON w.product_id = p.id
            WHERE w.customer_id = :customer_id
            AND p.status = 'active'
            ORDER BY w.created_at DESC
        ";
        $stmt = $db->prepare($query);
        $stmt->execute([':customer_id' => $customerId]);
        return $stmt->fetchAll();
    }

    /**
     * Add to wishlist
     */
    public function addToWishlist($customerId, $productId) {
        $existing = $this->findOne([
            'customer_id' => $customerId,
            'product_id' => $productId
        ]);

        if ($existing) {
            return true;
        }

        return $this->create([
            'customer_id' => $customerId,
            'product_id' => $productId
        ]);
    }

    /**
     * Remove from wishlist
     */
    public function removeFromWishlist($customerId, $productId) {
        $db = Database::getInstance()->getConnection();
        $query = "DELETE FROM wishlists WHERE customer_id = :customer_id AND product_id = :product_id";
        $stmt = $db->prepare($query);
        return $stmt->execute([
            ':customer_id' => $customerId,
            ':product_id' => $productId
        ]);
    }

    /**
     * Check if product is in wishlist
     */
    public function isInWishlist($customerId, $productId) {
        return $this->findOne([
            'customer_id' => $customerId,
            'product_id' => $productId
        ]) !== false;
    }

    /**
     * Count wishlist items
     */
    public function countItems($customerId) {
        return $this->count(['customer_id' => $customerId]);
    }
}
?>
