<?php

require_once "./api/get_request.php";
require_once "./api/post_request.php";

session_start();

if (isset($_POST['login'])) {
    $arr = array(
        "email" => $_POST['email'],
        "password" => $_POST['password']
    );

    $login = json_decode(post_request("https://account.lumintulogic.com/api/login.php", json_encode($arr)));
    $access_token = $login->{'data'}->{'accessToken'};
    $expiry = $login->{'data'}->{'expiry'};

    if ($login->{'success'}) {
        $userData = json_decode(http_request_with_auth("https://account.lumintulogic.com/api/user.php", $access_token));
        $_SESSION['user_data'] = $userData;
		$_SESSION['expiry']=$expiry;
        setcookie('X-LUMINTU-REFRESHTOKEN', $access_token, strtotime($expiry));

        switch ($userData->{'user'}->{'role_id'}) {
            case 1:
                // Admin
                break;
            case 2:
                // Mentor
                // var_dump($_SESSION['user_data']->{'user'}->{'role_id'});
                header("location: ./Role/Mentor/");
                break;
            case 3:
                // Student
                header("location: ./Role/Student/");
                break;
            default:
                break;
        }

        // var_dump($_SESSION['user_data']);
        // var_dump($_COOKIE['X-LUMINTU-REFRESHTOKEN']);
    }
}

?>


<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="css/swiper-bundle.min.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <link rel="stylesheet" href="CSS/main.css">

    <title>Form Login</title>
    <link rel="icon" type="image/x-icon" href="./Img/logo/logo_lumintu1.png">

    <style>
    .forgot:hover
    {
        color:#CCA274 !important;
    }
    .btn-primary
    {
        background-color: #DDB07F !important;
        border-color: #DDB07F !important;
    }
    .btn-primary:hover
    {
        background-color: #CCA274 !important;
        border-color: #CCA274 !important;
    }
    .btn-primary:active
    {
        background-color: #CCA274 !important;
        border-color: #CCA274 !important;
    }
    .btn-primary:visited
    {
        background-color: #CCA274 !important;
        border-color: #CCA274 !important;
    }

    </style>
  </head>
  <body class="gradient-background">

    <section class="form-login">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6 d-flex align-items-center mt-5 mt-lg-0">
                    <div class="container custom--px">    
                        <div class="logo text-center">
                          <h4>FORM LOGIN</h4>
                          <img class="w-[150px] logo-incareer" src="./Img/logo/logo_primary.svg" alt="Logo In Career">
                        </div>
                        <div class="header-title text-center">
                          <h2>Join for the Bright Future</h2>
                          <small class="text-muted">In Career is an LMS that focuses on career development in IT from Lumintu Logic with participants aged 25 years and over, who want to develop their careers in the IT field. </small>
                        </div>
                        <form method="post" action="#" class="mt-5">
                          <div class="container d-flex justify-content-between align-content-center form-group">
                            <div class="input-group">
                              <input type="email" name="email" id="email" required >
                              <label for="email">Enter your Email</label>
                            </div>
                            <!-- <div class="box d-flex align-items-center">
                              <i class="fas fa-at"></i>
                            </div> -->
                          </div>
                          <br>
                          <div class="container d-flex justify-content-between align-content-center form-group">
                            <div class="input-group">
                              <input type="password" name="password" id="password" required>
                              <label for="password">Enter your Password</label>
                            </div> 
                          </div>
                          <!-- <div class="container d-flex justify-content-between align-content-center form-group  mt-4">
                            <select class="form-select" aria-label="Default select example">
                              <option selected>Select Role</option>
                              <option value="Students">Student</option>
                              <option value="Company">Company</option>
                              <option value="Mentor">Mentor</option>
                            </select>
                          </div> -->
                          <div class="container d-flex justify-content-between align-content-center form-group mt-3">
                              <div class="">
                              <a href="register.php"style="color:#DDB07F;" class="forgot">Create an Account!</a>
                              </div>
                              <!-- <div class="check-group">
                                <input type="checkbox" name="remember"> &nbsp; Remember Me
                              </div> -->
                              <a href="forgotpassword.html"style="color:#DDB07F;" class="forgot">Forgot the Password?</a>
                          </div>
                          <div class="container mt-5">
                            <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
                            <div class="my-2">
                            <a href="index.html"style="color:#DDB07F;" class="forgot">Back to Landing Page</a>
                            </div>
                          </div>
                          <!-- <div class="text-left">
                            <a href="index.html"style="color:#DDB07F;" class="forgot">Back to Landing Page</a>
                          </div> -->
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <script src="js/swiper-bundle.min.js"></script>
    <script src="js/main.js"></script>
  </body>
</html>