<?php
    session_start();
    session_unset();
    session_destroy();
    if(isset($_SESSION['username'])){
        unset($_SESSION['username']);
    }
    header('Location: ' . dirname($_SERVER['REQUEST_URI']) . "/login.php");
    exit(0);
?>