<?php

session_start();
unset($_SESSION['user_data']);
setcookie('X-LUMINTU-REFRESHTOKEN', '', time() - 3600);
header("location: https://account.lumintulogic.com/logout.php");
