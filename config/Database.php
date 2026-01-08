<?php
/**
 * Secure Database Connection Class
 * Handles all database operations safely
 */

class Database {
    private $host;
    private $db;
    private $user;
    private $pass;
    private $connection;

    public function __construct() {
        // Load from environment or .env file
        $this->host = getenv('DB_HOST') ?: 'localhost';
        $this->user = getenv('DB_USER') ?: 'root';
        $this->pass = getenv('DB_PASS') ?: '';
        $this->db = getenv('DB_NAME') ?: 'siakaddb';
        
        $this->connect();
    }

    private function connect() {
        $this->connection = mysqli_connect(
            $this->host,
            $this->user,
            $this->pass,
            $this->db
        );

        if (!$this->connection) {
            error_log('Database connection failed: ' . mysqli_connect_error());
            die('Database connection error. Contact administrator.');
        }

        // Set charset to utf8mb4 for better security and emoji support
        mysqli_set_charset($this->connection, 'utf8mb4');
    }

    /**
     * Get the connection object
     */
    public function getConnection() {
        return $this->connection;
    }

    /**
     * Execute a prepared statement safely
     * 
     * @param string $query SQL query with ? placeholders
     * @param string $types Parameter types (s=string, i=integer, d=double, b=blob)
     * @param array $params Parameter values
     * @return mysqli_result|bool Query result or false
     */
    public function prepare($query, $types = '', $params = []) {
        $stmt = $this->connection->prepare($query);
        
        if (!$stmt) {
            error_log('Prepare statement failed: ' . $this->connection->error);
            return false;
        }

        if (!empty($types) && !empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        if (!$stmt->execute()) {
            error_log('Execute statement failed: ' . $stmt->error);
            return false;
        }

        return $stmt->get_result();
    }

    /**
     * Execute insert/update/delete with prepared statement
     */
    public function execute($query, $types = '', $params = []) {
        $stmt = $this->connection->prepare($query);
        
        if (!$stmt) {
            error_log('Prepare statement failed: ' . $this->connection->error);
            return false;
        }

        if (!empty($types) && !empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        return $stmt->execute();
    }

    /**
     * Get last insert ID
     */
    public function lastInsertId() {
        return $this->connection->insert_id;
    }

    /**
     * Get affected rows count
     */
    public function affectedRows() {
        return $this->connection->affected_rows;
    }

    /**
     * Close connection
     */
    public function close() {
        if ($this->connection) {
            $this->connection->close();
        }
    }

    public function __destruct() {
        $this->close();
    }
}
?>
