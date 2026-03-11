<?php

// $server = "localhost";
// $port = 3306;
// $user = "root";
// $password = "";
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

// try{
//     $conn = new PDO("mysql:host=$server; port=$port; dbname=ecom_website",$user,$password);
//     $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
// }

// catch(PDOException $e){
//     echo $e->getMessage();
// }
// ?>





