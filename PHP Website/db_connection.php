<!-- <?php

$server = "localhost";
$port = 3306;
$user = "root";
$password = "";
$conn = mysqli_connect(
    getenv("DB_HOST"),
    getenv("DB_USER"),
    getenv("DB_PASS"),
    getenv("DB_NAME"),
    getenv("DB_PORT")
);


if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

try{
    $conn = new PDO("mysql:host=$server; port=$port; dbname=ecom_website",$user,$password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}

catch(PDOException $e){
    echo $e->getMessage();
}
?>




 -->
<?php

$host = getenv("DB_HOST");
$user = getenv("DB_USER");
$pass = getenv("DB_PASS");
$db   = getenv("DB_NAME");
$port = getenv("DB_PORT");

$conn = mysqli_connect($host, $user, $pass, $db, $port);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

?>