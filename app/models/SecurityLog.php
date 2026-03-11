<?php
/**
 * Security Log Model
 */
class SecurityLog extends Model
{
    protected $table = 'ai_security_logs';

    /**
     * Log the AI decision or rule-based decision
     */
    public function logAction($ip, $requestsCount, $avgInterval, $action)
    {
        return $this->create([
            'ip_address' => $ip,
            'requests_count' => $requestsCount,
            'avg_interval' => $avgInterval,
            'action' => $action
        ]);
    }

    /**
     * Get total number of blocked attempts
     */
    public function getTotalBlocked()
    {
        return $this->count(['action' => 'block']);
    }

    /**
     * Get total number of traffic requests analyzed
     */
    public function getTotalAnalyzed()
    {
        return $this->count();
    }

    /**
     * Get the most recent blocked attempts
     */
    public function getRecentBlocks($limit = 5)
    {
        $query = "SELECT * FROM {$this->table} WHERE action = 'block' ORDER BY created_at DESC LIMIT :limit";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
?>
