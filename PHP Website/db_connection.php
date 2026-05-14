<?php
mysqli_report(MYSQLI_REPORT_OFF);

$host = getenv("DB_HOST") ?: "localhost";
$user = getenv("DB_USER") ?: "root";
$pass = getenv("DB_PASS") ?: "";
$db   = getenv("DB_NAME") ?: "ecom_website";
$port = (int)(getenv("DB_PORT") ?: 3306);

$conn = mysqli_connect($host, $user, $pass, $db, $port);

if (!$conn) {
    error_log(
        "Database connection failed. host={$host}, user={$user}, db={$db}, port={$port}, error=" .
        mysqli_connect_error()
    );
    http_response_code(503);
    exit("Database connection failed. Please check the Render logs.");
}

try {
    $pdo = new PDO(
        "mysql:host={$host};dbname={$db};port={$port};charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $exception) {
    error_log(
        "PDO connection failed. host={$host}, user={$user}, db={$db}, port={$port}, error=" .
        $exception->getMessage()
    );
    http_response_code(503);
    exit("Database connection failed. Please check the Render logs.");
}
?>
