<?php
    if(!isset($_POST['g-recaptcha-response']) || !isset($_POST['email'])
        || empty($_POST['g-recaptcha-response']) || empty($_POST['email'])){
        header('Location: ' . dirname($_SERVER['REQUEST_URI']));
        exit(0);
    }
    $email = stripslashes(
                htmlspecialchars(
                    trim(
                        $_POST['email']
             )));
    $valid_captcha = false;
    $valid_email = false;
    $valid_database = false;
    $api_key = file_get_contents('/api-keys/recaptcha.key');
    $database_key = file_get_contents('/api-keys/database.key');

    if (strlen($email) <= 255 && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $valid_email = true;
        try {
            $url = 'https://www.google.com/recaptcha/api/siteverify';
            $data = ['secret'   => $api_key,
                    'response' => $_POST['g-recaptcha-response'],
                    'remoteip' => $_SERVER['REMOTE_ADDR']];

            $options = [
                'http' => [
                    'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                    'method'  => 'POST',
                    'content' => http_build_query($data) 
                ]
            ];

            $context  = stream_context_create($options);
            $result = file_get_contents($url, false, $context);
            $valid_captcha = json_decode($result)->success;
        }
        catch (Exception $e) {
            $valid_captcha = false;
        }
    } else {
        $valid_email = false;
    }
    if($valid_email && $valid_captcha){
        $mysql_con = mysqli_connect("localhost","http",$database_key,"universalcd");
        if($mysql_con){
            $valid_database = true;
            $email_sql = mysqli_real_escape_string($mysql_con, $email);
            $sql = "INSERT IGNORE INTO mailing_list (email_address, join_date) VALUES ('" . $email_sql . "', '" . date("Y-m-d") . "');";

            if (!mysqli_query($mysql_con, $sql)) {
                $valid_database = false;
            }
        }
        mysqli_close($mysql_con);
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
        <meta name="url" content="https://www.UniversalCD.org/mailinglist.php">

        <title>Universal Community Developers - Mailing List</title>

        <link rel="stylesheet" href="css/UniversalCD.org.css?v=0.2">
        <link href="https://fonts.googleapis.com/css?family=Roboto|Roboto+Condensed" rel="stylesheet">

        <link rel="shortcut icon" href="favicon.ico">
    </head>

    <body>
        <div id="content-container">
            <div class="content-page" id="Mail">
                <div class="content-wrapper">
                    <div class="content-header">
                        <?php
                            if(!$valid_database){
                                echo("Woops, something went wrong on our end!");
                            } elseif($valid_email && $valid_captcha){
                                echo("Thank you for joining!");
                            } elseif(!$valid_email) {
                                echo("We're sorry, your email is invalid.");
                            } elseif(!$valid_captcha) {
                                echo("We're sorry, your captcha is invalid.");
                            }
                        ?>
                    </div>
                    <div class="content-content">
                        <br>
                        <div class="content-item center">
                            <?php
                                if(!$valid_database){
                                    echo("Please try again in a minute or two while we solve the issue.");
                                } elseif($valid_email && $valid_captcha){
                                    echo($email . " has been added to our mailing list.");
                                } else {
                                    echo("Please try again.");
                                }
                            ?>
                        </div>
                        <div class="content-item center">
                            You will be automatically redirected in <div id="redirect-counter" class="counter">10</div> seconds.
                            <br>
                            Please <a href="<?php echo(dirname($_SERVER['REQUEST_URI']));?>">click here</a> if you are not automatically redirected.
                            <script type="text/javascript">function timer(){if(count-=1,document.getElementById("redirect-counter").innerHTML=count,count<=0)return clearInterval(counter),void(window.location=document.URL.substr(0,document.URL.lastIndexOf("/")))}var count=10,counter=setInterval(timer,1e3);</script>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
