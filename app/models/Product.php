<?php
/**
 * Product Model
 */
class Product extends Model
{
    protected $table = 'products';

    /**
     * Get active products with pagination
     */
    public function getActive($limit = null, $offset = 0)
    {
        return $this->findBy(['status' => 'active'], $limit, $offset);
    }

    /**
     * Get products by category
     */
    public function getByCategory($categoryId, $limit = null, $offset = 0)
    {
        return $this->findBy([
            'category_id' => $categoryId,
            'status' => 'active'
        ], $limit, $offset);
    }

    /**
     * Get products by seller
     */
    public function getBySeller($sellerId, $limit = null, $offset = 0)
    {
        return $this->findBy([
            'seller_id' => $sellerId,
            'status' => 'active'
        ], $limit, $offset);
    }

    /**
     * Get bestsellers (by order count)
     */
    public function getBestsellers($limit = 10)
    {
        $db = Database::getInstance()->getConnection();
        $query = "
            SELECT p.* FROM products p
            INNER JOIN order_items oi ON p.id = oi.product_id
            WHERE p.status = 'active'
            GROUP BY p.id
            ORDER BY COUNT(oi.id) DESC
            LIMIT :limit
        ";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get products on sale (with active discount)
     */
    public function getOnSale($limit = 10)
    {
        $db = Database::getInstance()->getConnection();
        $query = "
            SELECT * FROM products
            WHERE status = 'active' 
            AND discount_percentage > 0
            AND discount_start_date <= NOW()
            AND discount_end_date >= NOW()
            LIMIT :limit
        ";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Search products
     */
    public function search($keyword, $limit = null, $offset = 0)
    {
        $db = Database::getInstance()->getConnection();
        $keyword = "%{$keyword}%";
        $query = "
            SELECT * FROM products
            WHERE status = 'active' 
            AND (name LIKE :keyword_name OR description LIKE :keyword_desc)
        ";

        if ($limit) {
            $query .= " LIMIT :limit OFFSET :offset";
        }

        $stmt = $db->prepare($query);
        $stmt->bindValue(':keyword_name', $keyword);
        $stmt->bindValue(':keyword_desc', $keyword);

        if ($limit) {
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Count products
     */
    public function countActive()
    {
        return $this->count(['status' => 'active']);
    }

    /**
     * Get discount price
     */
    public static function getDiscountedPrice($price, $discountPercentage)
    {
        return $price - ($price * ($discountPercentage / 100));
    }

    /**
     * Check if product has active discount
     */
    public function hasActiveDiscount($productData)
    {
        return !empty($productData['discount_percentage'])
            && $productData['discount_percentage'] > 0
            && strtotime($productData['discount_start_date']) <= time()
            && strtotime($productData['discount_end_date']) >= time();
    }

    /**
     * Get product images
     */
    public function getImages($productId)
    {
        $db = Database::getInstance()->getConnection();
        $query = "SELECT * FROM product_images WHERE product_id = :id ORDER BY is_primary DESC";
        $stmt = $db->prepare($query);
        $stmt->execute([':id' => $productId]);
        return $stmt->fetchAll();
    }

    /**
     * Add product image
     */
    public function addImage($productId, $imagePath, $isPrimary = false)
    {
        $db = Database::getInstance()->getConnection();
        $query = "INSERT INTO product_images (product_id, image_path, is_primary) VALUES (:product_id, :image_path, :is_primary)";
        $stmt = $db->prepare($query);
        return $stmt->execute([
            ':product_id' => $productId,
            ':image_path' => $imagePath,
            ':is_primary' => $isPrimary ? 1 : 0
        ]);
    }

    /**
     * Delete all images for a product
     */
    public function deleteImages($productId)
    {
        $db = Database::getInstance()->getConnection();
        $query = "DELETE FROM product_images WHERE product_id = :id";
        $stmt = $db->prepare($query);
        return $stmt->execute([':id' => $productId]);
    }

    /**
     * Get seller information
     */
    public function getSeller($productId)
    {
        $product = $this->findById($productId);
        if (!$product)
            return null;

        $userModel = new User();
        return $userModel->findById($product['seller_id']);
    }
}
?>
