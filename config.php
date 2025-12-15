<?php
// Database configuration
// EDIT THESE CREDENTIALS TO MATCH YOUR LOCAL SETUP
define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3307');
define('DB_USER', 'root');
define('DB_PASS', 'root'); // Default often empty in local setups like XAMPP
define('DB_NAME', 'sql_injection_lab');

// Attempt connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, (int)DB_PORT);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error . " <br> Please check config.php and ensure the database exists.");
}
?>
