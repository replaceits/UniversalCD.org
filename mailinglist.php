<?php
    if(!isset($_POST['g-recaptcha-response']) || !isset($_POST['email'])
        || empty($_POST['g-recaptcha-response']) || empty($_POST['email'])){
        header('Location: ' . dirname($_SERVER['REQUEST_URI']));
        exit(0);
    }
    $email = stripslashes(
                htmlspecialchars(
                    $_POST['email']
            ));
    $valid_captcha = false;
    $valid_email = false;
    $api_key = file_get_contents('/api-keys/recaptcha.key');

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
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

		<title>Universal Community Developers</title>

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
                            if($valid_email && $valid_captcha){
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
                                if($valid_email && $valid_captcha){
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
                            <script type="text/javascript">
                                var count = 10;
                                function timer()
                                {
                                    count = count - 1;
                                    document.getElementById('redirect-counter').innerHTML = count;
                                    if (count <= 0)
                                    {
                                        clearInterval(counter);
                                        window.location = document.URL.substr(0,document.URL.lastIndexOf('/'));
                                        return;
                                    }
                                }
                                var counter=setInterval(timer, 1000);
                            </script>
                        </div>
                    </div>
				</div>
			</div>
        </div>
    </body>
</html>
