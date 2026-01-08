<?php
/**
 * Security Helper Functions
 * Safe wrappers for common database operations
 */

/**
 * Safe GET parameter
 * @param string $name Parameter name
 * @param string $default Default value
 * @return string Safe parameter value
 */
function getSafeGet($name, $default = '') {
    if (!isset($_GET[$name])) {
        return $default;
    }
    $value = $_GET[$name];
    if (is_array($value)) {
        return $default;
    }
    return trim(strval($value));
}

/**
 * Safe POST parameter
 * @param string $name Parameter name
 * @param string $default Default value
 * @return string Safe parameter value
 */
function getSafePost($name, $default = '') {
    if (!isset($_POST[$name])) {
        return $default;
    }
    $value = $_POST[$name];
    if (is_array($value)) {
        return $default;
    }
    return trim(strval($value));
}

/**
 * Safe COOKIE parameter
 * @param string $name Cookie name
 * @param string $default Default value
 * @return string Safe cookie value
 */
function getSafeCookie($name, $default = '') {
    if (!isset($_COOKIE[$name])) {
        return $default;
    }
    return trim(strval($_COOKIE[$name]));
}

/**
 * Execute select query with prepared statement
 * @param mysqli $conn Database connection
 * @param string $query Query with ? placeholders
 * @param string $types Parameter types
 * @param array $params Parameter values
 * @return mysqli_result|null Query result
 */
function executeSelect($conn, $query, $types = '', $params = []) {
    try {
        $stmt = $conn->prepare($query);
    } catch (mysqli_sql_exception $e) {
        error_log('Query prepare exception: ' . $e->getMessage());
        return null;
    }

    if (!$stmt) {
        error_log('Query prepare failed: ' . ($conn->error ?? 'unknown'));
        return null;
    }

    if (!empty($types) && !empty($params)) {
        // bind_param requires variables; create references for unpacking
        $refs = [];
        foreach ($params as $k => $v) {
            $refs[$k] = &$params[$k];
        }
        $stmt->bind_param($types, ...$refs);
    }

    try {
        if (!$stmt->execute()) {
            error_log('Query execute failed: ' . $stmt->error);
            return null;
        }
    } catch (mysqli_sql_exception $e) {
        error_log('Query execute exception: ' . $e->getMessage());
        return null;
    }

    // get_result may not be available everywhere; try and fall back
    try {
        return $stmt->get_result();
    } catch (Throwable $e) {
        // Fallback: build a simple array of rows
        $meta = $stmt->result_metadata();
        if (!$meta) {
            return null;
        }
        $fields = [];
        while ($f = $meta->fetch_field()) {
            $fields[] = $f->name;
        }
        $meta->free();
        $row = array_fill_keys($fields, null);
        $refs = [];
        foreach ($row as $k => &$v) { $refs[] = &$v; }
        $stmt->bind_result(...$refs);
        $results = [];
        while ($stmt->fetch()) {
            $r = [];
            foreach ($fields as $i => $name) {
                $r[$name] = $row[$name];
            }
            $results[] = $r;
        }
        return $results;
    }
}

/**
 * Check if user is authenticated
 * @return bool True if user is logged in
 */
function isAuthenticated() {
    return isset($_SESSION['user_id']);
}

/**
 * Require authentication
 * Redirect to login if not authenticated
 */
function requireAuth($redirectUrl = 'login.php') {
    if (!isAuthenticated()) {
        header('Location: ' . $redirectUrl);
        exit;
    }
}

/**
 * HTML escape output
 * @param string $text Text to escape
 * @return string Escaped text safe for HTML
 */
function escape($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

/**
 * Validate email
 * @param string $email Email to validate
 * @return bool True if valid email
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate ID (only alphanumeric and dash)
 * @param string $id ID to validate
 * @param int $minLen Minimum length
 * @param int $maxLen Maximum length
 * @return bool True if valid
 */
function isValidId($id, $minLen = 1, $maxLen = 50) {
    if (strlen($id) < $minLen || strlen($id) > $maxLen) {
        return false;
    }
    return preg_match('/^[a-zA-Z0-9_\-]+$/', $id) === 1;
}

// Admin session helpers
if (!function_exists('isAdmin')) {
    function isAdmin(): bool {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
        if (!empty($_SESSION['role']) && $_SESSION['role'] === 'admin') {
            return true;
        }
        if (!empty($_COOKIE['simpreskul_admin']) && $_COOKIE['simpreskul_admin'] === '1') {
            return true;
        }
        return false;
    }
}

if (!function_exists('requireAdmin')) {
    function requireAdmin(string $redirect = 'login.php'): void {
        if (!isAdmin()) {
            // If a base URL is available, use it
            $target = $redirect;
            if (isset($GLOBALS['base_url']) && $GLOBALS['base_url']) {
                $target = rtrim($GLOBALS['base_url'], '/') . '/admin/' . ltrim($redirect, '/');
            }
            header('Location: ' . $target);
            exit;
        }
    }
}

if (!function_exists('setAdminSession')) {
    function setAdminSession(int $userId, string $username): void {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
        $_SESSION['user_id'] = $userId;
        $_SESSION['username'] = $username;
        $_SESSION['role'] = 'admin';
        // persistent cookie for legacy pages (30 days)
        setcookie('simpreskul_admin', '1', time() + 60*60*24*30, '/', $_SERVER['HTTP_HOST'] ?? '', (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'), true);
    }
}
?>
