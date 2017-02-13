<?php
    session_start();

    if(!isset($_SESSION['username']) || empty($_SESSION['username'])){
        header('Location: ' . dirname($_SERVER['REQUEST_URI']) . "/login.php");
        exit(0);
    }

    $mailing_list_users = 0;
    $mailing_list_last_date = "";

    $valid_database = false;
    $database_key = file_get_contents('/api-keys/database.key');

    $mysqli_con = new mysqli("localhost","http",$database_key,"universalcd");

    if(!mysqli_connect_errno()){
        $valid_database = true;

        $sql = "SELECT COUNT(email_address) FROM mailing_list;";

        if($stmt = $mysqli_con->prepare($sql)){
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($mailing_list_users);
            
            if($stmt->num_rows == 1){
                $stmt->fetch();
            } else {
                $valid_database = false;
            }
            $stmt->close();
        } else {
            $valid_database = false;
        }

        $sql = "SELECT join_date FROM mailing_list ORDER BY ABS(DATEDIFF(join_date,NOW())) LIMIT 1;";
        if($stmt = $mysqli_con->prepare($sql)){
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($mailing_list_last_date);
            
            if($stmt->num_rows == 1){
                $stmt->fetch();
            } else {
                $valid_database = false;
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
        <div class="color-bar">
			<div class="color-bar-block color-bar-block-1"></div>
			<div class="color-bar-block color-bar-block-2"></div>
			<div class="color-bar-block color-bar-block-3"></div>
			<div class="color-bar-block color-bar-block-4"></div>
			<div class="color-bar-block color-bar-block-5"></div>
		</div>
		<div id="header">
			<div id="header-logo">
				<a href="#Home">
					<img src="images/LogoPlainBorderBlackFillBlack.svg.png">
				</a>
			</div>
			<div class="header-item">
				<a href="#About">
					
				</a>
			</div>
			<div class="header-item">
				<a href="#Community">
					
				</a>
			</div>
			<div class="header-item">
				<a href="#Mail">
					MAILING LIST
				</a>
			</div>
			<a href="logout.php" class="header-item header-item-highlight">
					LOGOUT
			</a>
		</div>
        <div id="content-container">
            <div class="content-page" id="Home">
				<div class="content-wrapper no-background">
					<div class="content-header" id="welcome">
						Welcome <?php echo($_SESSION['username']); ?>
					</div>
				</div>
			</div>
			<div class="content-page" id="Mail">
				<div class="content-wrapper">
					<div class="content-header" id="welcome">
						Mailing list
					</div>
                    <div class="content-content">
                        <div class="content-item center">
                            <div class="content-subheader">
                                Statistics
                            </div>
                            <?php
                                if($valid_database){
                            ?>
                                    <?php echo($mailing_list_users); ?> users have joined the mailing list.
                                    <br>
                                    The last time someone joined was on <?php echo($mailing_list_last_date); ?>
                            <?php
                                } else {
                            ?>
                                    Whoops, something went wrong with the database.
                            <?php
                                }
                            ?>
                        </div>
                        <div class="content-item center">
                            <a href="getmailingcsv.php" class="button button-submit" id="button-submit">Get CSV</a>
                        </div>
                    </div>
				</div>
			</div>
        </div>
    </body>
</html>
