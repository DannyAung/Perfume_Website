<?php

$server = "localhost";
$port = 3306;
$user = "root";
$password = "";

try{
    $conn = new PDO("mysql:host=$server; port=$port; dbname=ecom_website",$user,$password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}

catch(PDOException $e){
    echo $e->getMessage();
}
?>





