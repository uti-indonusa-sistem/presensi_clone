<?php
// Compatibility shim to emulate old mysql_* functions using mysqli
// Allows legacy code to keep working while we migrate to prepared statements.
if (!function_exists('mysql_query')) {
    function mysql_query($query) {
        global $connection;
        return mysqli_query($connection, $query);
    }
}

if (!function_exists('mysql_fetch_array')) {
    function mysql_fetch_array($result) {
        return mysqli_fetch_array($result);
    }
}

if (!function_exists('mysql_num_rows')) {
    function mysql_num_rows($result) {
        return mysqli_num_rows($result);
    }
}

if (!function_exists('mysql_insert_id')) {
    function mysql_insert_id() {
        global $connection;
        return mysqli_insert_id($connection);
    }
}

if (!function_exists('mysql_real_escape_string')) {
    function mysql_real_escape_string($str) {
        global $connection;
        return mysqli_real_escape_string($connection, $str);
    }
}

if (!function_exists('mysql_error')) {
    function mysql_error() {
        global $connection;
        return mysqli_error($connection);
    }
}

?>