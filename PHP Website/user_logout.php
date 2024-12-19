<?php
if(!isset($_SESSION))
{
    session_start();
}

if($_SESSION['user_logged_in'])
{
    session_destroy();
    header("Location: user_login.php");
}