<?php
// define('DB_HOST', 'localhost');
// define('DB_USER', 'purity.moraa');
// define('DB_PASS', 'Mbw@wewenam1m1');
// define('DB_NAME', 'webtech_fall2024_purity_moraa');
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'buddyup');

try {
    // PDO connection
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // mysqli connection for save-quiz.php
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

} catch(Exception $e) {
    die("Connection failed: " . $e->getMessage());
}
?>