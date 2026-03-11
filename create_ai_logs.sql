CREATE TABLE IF NOT EXISTS ai_security_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45) NOT NULL,
    requests_count INT NOT NULL,
    avg_interval FLOAT NOT NULL,
    action ENUM('allow', 'block') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
