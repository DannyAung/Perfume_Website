<?php
mysqli_report(MYSQLI_REPORT_OFF);

function env_first(array $names, string $default = ''): string
{
    foreach ($names as $name) {
        $value = getenv($name);
        if ($value !== false && $value !== '') {
            return trim($value);
        }
    }

    return $default;
}

$host = env_first(["DB_HOST", "MYSQLHOST"], "localhost");
$user = env_first(["DB_USER", "MYSQLUSER"], "root");
$pass = env_first(["DB_PASS", "DB_PASSWORD", "MYSQLPASSWORD"]);
$db   = env_first(["DB_NAME", "DB_DATABASE", "MYSQLDATABASE"], "ecom_website");
$port = (int)env_first(["DB_PORT", "MYSQLPORT"], "3306");

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
