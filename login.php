<?php
    session_start();

    if(isset($_SESSION['username']) || !empty($_SESSION['username'])){
        header('Location: ' . dirname($_SERVER['REQUEST_URI']) . "/admin.php");
        exit(0);
    }

    $logging_in = false;
    $valid_login = false;

    if(isset($_POST['password']) && !empty($_POST['password'])
        && isset($_POST['username']) && !empty($_POST['username'])){
        $logging_in = true;
        $valid_database = false;
        $database_key = file_get_contents('/api-keys/database.key');

        $mysqli_con = new mysqli("localhost","http",$database_key,"universalcd");

        if(!mysqli_connect_errno()){
            $valid_database = true;

            $username = $_POST['username'];
            $password = $_POST['password'];

            $sql = "SELECT password_hash FROM users WHERE user_name = ?;";

            if($stmt = $mysqli_con->prepare($sql)){
                $stmt->bind_param('s',$username);
                $stmt->execute();
                $stmt->store_result();
                $stmt->bind_result($password_hash);
                
                if($stmt->num_rows == 1){
                    $stmt->fetch();
                    if(password_verify($password,$password_hash)){
                        $valid_login = true;
                        $_SESSION['username'] = $username;
                    }
                }
                $stmt->close();
            } else {
                $valid_database = false;
            }
        } else {
            $valid_database = false;
        }
        $mysqli_con->close();
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

        <title>Universal Community Developers - Login</title>

        <link rel="stylesheet" href="css/UniversalCD.org.css?v=0.2">
        <link href="https://fonts.googleapis.com/css?family=Roboto|Roboto+Condensed" rel="stylesheet">

        <link rel="shortcut icon" href="favicon.ico">
    </head>
    <body>
        <div id="content-container">
            <div class="content-page" id="Login">
                <div class="content-wrapper no-background">
                    <div class="content-header">
                        <?php
                            if($logging_in){
                                if($valid_login){
                                    echo("You have logged in!");
                                } else {
                                    echo("Invalid username and/or password!");
                                }
                            } else {
                                echo("Log in");
                            }
                        ?>
                    </div>
                    <div class="content-content">
                        <br>
                        <div class="content-item center">
                            <?php
                                if(!$logging_in){
                            ?>
                                    <form class="form form-login" id="form-login" action="login.php" method="POST">
                                        <input class="input-text input-username input-invalid" id="input-username" name="username" type="text" placeholder="Username">
                                        <input class="input-text input-password input-invalid" id="input-password" name="password" type="password" placeholder="Password">
                                        <input class="button button-submit" id="button-submit" type="submit" value="Submit" disabled>
                                        <script type="text/javascript">
                                            var input_username = document.getElementById('input-username');
                                            var input_password = document.getElementById('input-password');
                                            var button_submit  = document.getElementById('button-submit' );

                                            if(input_username.value.length > 0 && input_password.value.length > 0)
                                            {
                                                button_submit.disabled = false;
                                            }

                                            input_username.onchange = function(){
                                                if(this.value.length === 0){
                                                    button_submit.disabled = true;
                                                    if ( !this.className.match(/(?:^|\s)input-invalid(?!\S)/) ){
                                                        this.className += " input-invalid";
                                                    }
                                                } else {
                                                    if ( this.className.match(/(?:^|\s)input-invalid(?!\S)/) ){
                                                        this.className = this.className.replace( /(?:^|\s)input-invalid(?!\S)/g , '');
                                                    }
                                                    if(input_username.value.length > 0 && input_password.value.length > 0)
                                                    {
                                                        button_submit.disabled = false;
                                                    } else {
                                                        button_submit.disabled = true;
                                                    } 
                                                }
                                            }; 
                                            input_username.onkeypress = input_username.onchange;
                                            input_username.onpaste = input_username.onchange;
                                            input_username.oninput = input_username.onchange;

                                            input_password.onchange = input_username.onchange;
                                            input_password.onkeypress = input_password.onchange;
                                            input_password.onpaste = input_password.onchange;
                                            input_password.oninput = input_password.onchange;
                                        </script>
                                    </form>
                            <?php
                                } else {
                            ?>
                                    You will be automatically redirected in <div id="redirect-counter" class="counter">10</div> seconds.
                                    <br>
                                    Please <a href="<?php echo(dirname($_SERVER['REQUEST_URI']) . ($valid_login ? "/admin.php" : "/login.php") );?>">click here</a> if you are not automatically redirected.
                                    <script type="text/javascript">function timer(){if(count-=1,document.getElementById("redirect-counter").innerHTML=count,count<=0)return clearInterval(counter),void(window.location=document.URL.substr(0,document.URL.lastIndexOf("/")) + "<?php echo(($valid_login ? "/admin.php" : "/login.php")); ?>" )}var count=10,counter=setInterval(timer,1e3);</script>
                            <?php
                                }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
