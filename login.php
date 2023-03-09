<?php


// Include config file
require_once "config.php";

// Initialize the session
session_start();
 
// Check if the user is already logged in, if yes then redirect to welcome page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true && isset($_SESSION["token"])){
    header("location: index.php");
    exit;
}
 
// Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = $login_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Check if username is empty
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter username.";
    } else{
        $username = trim($_POST["username"]);
    }
    
    // Check if password is empty
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter your password.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate credentials
    if(empty($username_err) && empty($password_err)){

        // Prepare POST request
        $endpoint = $link . "/api/v1/login";
        $username = trim($_POST["username"]);

        // The JSON body to send to the API
        $postData = array(
            'username' => $username,
            'password' => base64_encode($password)
        );

        // Request headers combined with the JSON body
        $opts = array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Content-Type: application/json',
                'content' => json_encode($postData)
            ),
            'ssl' => [
                'allow_self_signed'=> true
            ]
        );

        // Execute the request
        $context = stream_context_create($opts);
        $resource = file_get_contents($endpoint, false, $context);
        echo $resource;
        $data = json_decode($resource);

        //var_dump($data);
  
        // Check if username exists, if yes then verify password
        if($data > 0){         
        
            if($data->ok == true){
                // Did the credentials authenticate
                session_start();
                            
                // Store data in session variables
                $_SESSION["loggedin"] = true;
                $_SESSION["userid"] = $data->user_id;
                $_SESSION["username"] = $username; 
                $_SESSION["token"] = $data->token; 
                            
                // Redirect user to welcome page
                header("location: index.php");
            } else{
                // Password is not valid, display a generic error message
                $login_err = "Invalid username or password.";
            }

        } else{
            // Username doesn't exist, display a generic error message
            $login_err = "Invalid username or password.";
        }
    } else {
        echo "Oops! Something went wrong. Please try again later.";
    }
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }

      .b-example-divider {
        height: 3rem;
        background-color: rgba(0, 0, 0, .1);
        border: solid rgba(0, 0, 0, .15);
        border-width: 1px 0;
        box-shadow: inset 0 .5em 1.5em rgba(0, 0, 0, .1), inset 0 .125em .5em rgba(0, 0, 0, .15);
      }

      .b-example-vr {
        flex-shrink: 0;
        width: 1.5rem;
        height: 100vh;
      }

      .bi {
        vertical-align: -.125em;
        fill: currentColor;
      }

      .nav-scroller {
        position: relative;
        z-index: 2;
        height: 2.75rem;
        overflow-y: hidden;
      }

      .nav-scroller .nav {
        display: flex;
        flex-wrap: nowrap;
        padding-bottom: 1rem;
        margin-top: -1px;
        overflow-x: auto;
        text-align: center;
        white-space: nowrap;
        -webkit-overflow-scrolling: touch;
      }
    </style>
    <link href="sign-in.css" rel="stylesheet">
</head>
<body class="text-center">
    
    <main class="form-signin w-100 m-auto">
    
        <?php 
        if(!empty($login_err)){
            echo '<div class="alert alert-danger">' . $login_err . '</div>';
        }        
        ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <img class="mb-4" src="media/mindful_logo.jpg" alt="" width="72" height="57">
        <h1 class="h3 mb-3 fw-normal">Please sign in</h1>    
        <div class="form-floating">
            <input type="text" name="username" placeholder="Username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
            <span class="invalid-feedback"><?php echo $username_err; ?></span>
        </div>    
        <div class="form-floating">
            <input type="password" name="password" placeholder="Password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
            <span class="invalid-feedback"><?php echo $password_err; ?></span>
        </div>
        <div class="w-100 btn btn-lg btn-primary">
            <input type="submit" class="btn btn-primary" value="Sign in">
        </div>
        <p class="mt-3 mb-3" >Don't have an account? <a href="register.php">Sign up now</a>.</p>
        <p class="mt-5 mb-3 text-muted">© Neil Rutherford 2023</p>
        </form>

    </main>
</body>
</html>