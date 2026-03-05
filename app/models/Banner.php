<?php
/**
 * Banner Model
 */
class Banner extends Model {
    protected $table = 'banners';

    /**
     * Get active banners
     */
    public function getActive() {
        $db = Database::getInstance()->getConnection();
        $query = "
            SELECT * FROM banners
            WHERE is_active = TRUE
            AND (start_date IS NULL OR start_date <= NOW())
            AND (end_date IS NULL OR end_date >= NOW())
            ORDER BY position ASC
        ";
        $stmt = $db->query($query);
        return $stmt->fetchAll();
    }
}
?>
