<?php
/**
 * Category Model
 */
class Category extends Model {
    protected $table = 'categories';

    /**
     * Get active categories
     */
    public function getActive($limit = null, $offset = 0) {
        return $this->findBy(['status' => 'active'], $limit, $offset);
    }

    /**
     * Find by slug
     */
    public function findBySlug($slug) {
        return $this->findOne(['slug' => $slug]);
    }

    /**
     * Count products in category
     */
    public function countProducts($categoryId) {
        $db = Database::getInstance()->getConnection();
        $query = "SELECT COUNT(*) as total FROM products WHERE category_id = :id AND status = 'active'";
        $stmt = $db->prepare($query);
        $stmt->execute([':id' => $categoryId]);
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }
}
?>
