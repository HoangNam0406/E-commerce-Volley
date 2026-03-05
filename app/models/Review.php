<?php
/**
 * Review Model
 */
class Review extends Model {
    protected $table = 'reviews';

    /**
     * Get product reviews
     */
    public function getProductReviews($productId) {
        $db = Database::getInstance()->getConnection();
        $query = "
            SELECT r.*, u.full_name, u.avatar
            FROM reviews r
            JOIN users u ON r.customer_id = u.id
            WHERE r.product_id = :product_id
            AND r.status = 'approved'
            ORDER BY r.created_at DESC
        ";
        $stmt = $db->prepare($query);
        $stmt->execute([':product_id' => $productId]);
        return $stmt->fetchAll();
    }

    /**
     * Get average rating
     */
    public function getAverageRating($productId) {
        $db = Database::getInstance()->getConnection();
        $query = "
            SELECT AVG(rating) as average
            FROM reviews
            WHERE product_id = :product_id
            AND status = 'approved'
        ";
        $stmt = $db->prepare($query);
        $stmt->execute([':product_id' => $productId]);
        $result = $stmt->fetch();
        return round($result['average'] ?? 0, 1);
    }

    /**
     * Get pending reviews
     */
    public function getPending() {
        return $this->findBy(['status' => 'pending']);
    }

    /**
     * Approve review
     */
    public function approve($reviewId) {
        return $this->update($reviewId, ['status' => 'approved']);
    }

    /**
     * Reject review
     */
    public function reject($reviewId) {
        return $this->update($reviewId, ['status' => 'rejected']);
    }
}
?>
