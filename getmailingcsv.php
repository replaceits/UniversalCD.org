<?php
    session_start();
    
    if(!isset($_SESSION['username']) || empty($_SESSION['username'])){
        header('Location: ' . dirname($_SERVER['REQUEST_URI']));
        exit(0);
    }

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=data.csv');

    $output = fopen('php://output', 'w');

    fputcsv($output, array('Email', 'join_date'));

    $valid_database = false;
    $database_key = file_get_contents('/api-keys/database.key');

    $mysqli_con = new mysqli("localhost","http",$database_key,"universalcd");
    if(!mysqli_connect_errno()){
        $valid_database = true;
        $sql = "SELECT email_address, join_date FROM mailing_list;";

        if($stmt = $mysqli_con->prepare($sql)){
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($email, $join_date);
            while( $stmt->fetch()){
                fputcsv($output, [$email, $join_date]);
            }
            $stmt->close();
        } else {
            $valid_database = false;
        }
    } else {
        $valid_database = false;
    }
    $mysqli_con->close();
?>
