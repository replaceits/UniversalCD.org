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
        $mysqli_con = new mysqli("localhost","http",$database_key,"universalcd");

        if(!mysqli_connect_errno()){
            $valid_database = true;

            $sql = "INSERT IGNORE INTO mailing_list (email_address, join_date) VALUES ( ? , '" . date("Y-m-d") . "');";

            if($stmt = $mysqli_con->prepare($sql)){
                $stmt->bind_param('s',$email);
                $stmt->execute();
                $stmt->store_result();
                $stmt->close();
            } else {
                $valid_database = false;
            }
        }

        $mysqli_con->close();
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <meta name="description" content="Universal Community Developers is a community based non-profit organization. We are an alliance of concerned citizens geared towards giving back, paying it forward and creating change in one youth, one family and one community at a time, while fostering a universal development process and creating safe environments for all.">
		<meta name="subject" content="Non-profit">
		<meta name="author" content="Universal Community Developers">
		<meta name="rating" content="General">
        <meta name="url" content="https://www.universalcd.org/mailinglist.php">

        <meta property="og:title" content="Universal Community Developers">
		<meta property="og:description" content="Universal Community Developers is a community based non-profit organization. We are an alliance of concerned citizens geared towards giving back, paying it forward and creating change in one youth, one family and one community at a time, while fostering a universal development process and creating safe environments for all.">
		<meta property="og:locale" content="en_US">
		<meta property="og:type" content="website">
		<meta property="og:url" content="https://www.universalcd.org/">
		<meta property="og:image" content="https://www.universalcd.org/images/preview.png">
		<meta property="og:image:type" content="image/png">
		<meta property="og:image:height" content="409">
		<meta property="og:image:width" content="793">

		<meta name="twitter:card" content="summary_large_image">
		<meta name="twitter:site" content="@replaceits">
		<meta name="twitter:creator" content="@replaceits">
		<meta name="twitter:title" content="Universal Community Developers">
		<meta name="twitter:description" content="Universal Community Developers is a community based non-profit organization. We are an alliance of concerned citizens geared towards giving back, paying it forward and creating change in one youth, one family and one community at a time, while fostering a universal development process and creating safe environments for all.">
		<meta name="twitter:image" content="https://www.universalcd.org/images/preview.png">
		<meta name="twitter:image:alt" content="Universal Community Developers">

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
                            Please <a href="<?php echo(dirname($_SERVER['REQUEST_URI']) . "#Contact");?>">click here</a> if you are not automatically redirected.
                            <script type="text/javascript">function timer(){if(count-=1,document.getElementById("redirect-counter").innerHTML=count,count<=0)return clearInterval(counter),void(window.location=document.URL.substr(0,document.URL.lastIndexOf("/"))+"#Contact")}var count=10,counter=setInterval(timer,1e3);</script>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
