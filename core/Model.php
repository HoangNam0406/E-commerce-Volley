<?php
/**
 * Base Model Class
 */
abstract class Model {
    protected $table;
    protected $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Find record by ID
     */
    public function findById($id) {
        $query = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Get all records
     */
    public function getAll($limit = null, $offset = 0) {
        $query = "SELECT * FROM {$this->table}";
        if ($limit) {
            $query .= " LIMIT :limit OFFSET :offset";
        }
        $stmt = $this->db->prepare($query);
        if ($limit) {
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Find by condition
     */
    public function findBy($conditions = [], $limit = null, $offset = 0) {
        $query = "SELECT * FROM {$this->table}";
        
        if (!empty($conditions)) {
            $where = [];
            foreach ($conditions as $key => $value) {
                $where[] = "$key = :$key";
            }
            $query .= " WHERE " . implode(' AND ', $where);
        }

        if ($limit) {
            $query .= " LIMIT :limit OFFSET :offset";
        }

        $stmt = $this->db->prepare($query);
        
        foreach ($conditions as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        
        if ($limit) {
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Find one record by condition
     */
    public function findOne($conditions = []) {
        $query = "SELECT * FROM {$this->table}";
        
        if (!empty($conditions)) {
            $where = [];
            foreach ($conditions as $key => $value) {
                $where[] = "$key = :$key";
            }
            $query .= " WHERE " . implode(' AND ', $where);
        }

        $stmt = $this->db->prepare($query);
        
        foreach ($conditions as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Create new record
     */
    public function create($data) {
        $fields = array_keys($data);
        $placeholders = array_map(fn($f) => ":$f", $fields);
        
        $query = "INSERT INTO {$this->table} (" . implode(',', $fields) . ") VALUES (" . implode(',', $placeholders) . ")";
        
        $stmt = $this->db->prepare($query);
        
        foreach ($data as $field => $value) {
            $stmt->bindValue(":$field", $value);
        }
        
        return $stmt->execute();
    }

    /**
     * Update record
     */
    public function update($id, $data) {
        $sets = [];
        foreach ($data as $field => $value) {
            $sets[] = "$field = :$field";
        }
        
        $query = "UPDATE {$this->table} SET " . implode(', ', $sets) . " WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        
        foreach ($data as $field => $value) {
            $stmt->bindValue(":$field", $value);
        }
        $stmt->bindValue(':id', $id);
        
        return $stmt->execute();
    }

    /**
     * Delete record
     */
    public function delete($id) {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Count records
     */
    public function count($conditions = []) {
        $query = "SELECT COUNT(*) as total FROM {$this->table}";
        
        if (!empty($conditions)) {
            $where = [];
            foreach ($conditions as $key => $value) {
                $where[] = "$key = :$key";
            }
            $query .= " WHERE " . implode(' AND ', $where);
        }

        $stmt = $this->db->prepare($query);
        
        foreach ($conditions as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }

    /**
     * Execute custom query
     */
    public function query($query, $params = []) {
        $stmt = $this->db->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        $stmt->execute();
        return $stmt;
    }
}
?>
