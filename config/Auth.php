<?php
/**
 * Authentication Helper Class
 * Handles password hashing and verification
 */

class Auth {
    /**
     * Hash password using bcrypt (PHP 5.5+)
     * @param string $password Plain text password
     * @return string Hashed password
     */
    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    /**
     * Verify password against hash
     * @param string $password Plain text password
     * @param string $hash Stored hash
     * @return bool True if password matches
     */
    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }

    /**
     * Check if password needs rehashing (for future-proofing)
     * @param string $hash Stored hash
     * @return bool True if hash should be updated
     */
    public static function needsRehash($hash) {
        return password_needs_rehash($hash, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    /**
     * Generate secure random token
     * @param int $length Token length
     * @return string Random token
     */
    public static function generateToken($length = 32) {
        return bin2hex(random_bytes($length));
    }

    /**
     * Validate session security
     * @return bool True if session is secure
     */
    public static function validateSession() {
        // Check if user agent hasn't changed (basic check)
        if (!isset($_SESSION['_user_agent'])) {
            $_SESSION['_user_agent'] = hash('sha256', $_SERVER['HTTP_USER_AGENT'] ?? '');
            return true;
        }

        if ($_SESSION['_user_agent'] !== hash('sha256', $_SERVER['HTTP_USER_AGENT'] ?? '')) {
            session_destroy();
            return false;
        }

        return true;
    }

    /**
     * Secure logout
     */
    public static function logout() {
        // Unset all session variables
        $_SESSION = [];
        
        // Destroy session
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }
}
?>
