<?php
// Database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'emp');
define('DB_PORT', 3306); // optional: for reuse

try {
    // Create a PDO instance with correct port
    $dbh = new PDO("mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));

    // Set error mode to exceptions
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // echo "Database connection established successfully!";
} catch (PDOException $e) {
    exit("Database connection failed: " . $e->getMessage());
}
?>
