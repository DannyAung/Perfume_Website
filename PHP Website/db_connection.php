<?php
$host = getenv("DB_HOST") ?: "localhost";
$user = getenv("DB_USER") ?: "root";
$pass = getenv("DB_PASS") ?: "";
$db   = getenv("DB_NAME") ?: "ecom_website";
$port = (int)(getenv("DB_PORT") ?: 3306);

$conn = mysqli_connect($host, $user, $pass, $db, $port);

$pdo = new PDO(
    "mysql:host={$host};dbname={$db};port={$port};charset=utf8mb4",
    $user,
    $pass,
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]
);
?>