<?php
    session_start();

    if(!isset($_SESSION['username']) || empty($_SESSION['username'])){
        header('Location: ' . dirname($_SERVER['REQUEST_URI']) . "/login.php");
        exit(0);
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <meta name="description" content="">
        <meta name="subject" content="">
        <meta name="author" content="Sidney Williams">
        <meta name="rating" content="General">
        <meta name="url" content="https://www.UniversalCD.org/login.php">

        <title>Universal Community Developers - Admin</title>

        <link rel="stylesheet" href="css/UniversalCD.org.css?v=0.2">
        <link href="https://fonts.googleapis.com/css?family=Roboto|Roboto+Condensed" rel="stylesheet">

        <link rel="shortcut icon" href="favicon.ico">
    </head>
    <body>
    </body>
</html>