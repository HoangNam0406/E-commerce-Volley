<?php
/**
 * User Model
 */
class User extends Model {
    protected $table = 'users';

    /**
     * Find user by email
     */
    public function findByEmail($email) {
        return $this->findOne(['email' => $email]);
    }

    /**
     * Find user by username
     */
    public function findByUsername($username) {
        return $this->findOne(['username' => $username]);
    }

    /**
     * Create new user
     */
    public function register($data) {
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        return $this->create($data);
    }

    /**
     * Verify password
     */
    public function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }

    /**
     * Get sellers
     */
    public function getSellers($limit = null, $offset = 0) {
        return $this->findBy(['role' => 'seller', 'status' => 'active'], $limit, $offset);
    }

    /**
     * Get customers
     */
    public function getCustomers($limit = null, $offset = 0) {
        return $this->findBy(['role' => 'customer', 'status' => 'active'], $limit, $offset);
    }

    /**
     * Count sellers
     */
    public function countSellers() {
        return $this->count(['role' => 'seller']);
    }

    /**
     * Count customers
     */
    public function countCustomers() {
        return $this->count(['role' => 'customer']);
    }

    /**
     * Update user profile
     */
    public function updateProfile($userId, $data) {
        unset($data['password']); // Don't update password here
        return $this->update($userId, $data);
    }

    /**
     * Change password
     */
    public function changePassword($userId, $newPassword) {
        return $this->update($userId, [
            'password' => password_hash($newPassword, PASSWORD_BCRYPT)
        ]);
    }
}
?>
