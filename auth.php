<?php


function validate_param()
{
    return isset($_GET["token"]) && $_GET["token"] && isset($_GET["expiry"]) && $_GET["expiry"] && isset($_GET["page"]) && $_GET["page"];
}
//Var_dump($_GET);
//die();
if (!validate_param()) {
    echo "
    <script>
        alert('Parameter tidak valid');
        history.back();
    </script>";
    die;
}
require_once('api/get_request.php');
$userData = json_decode(http_request_with_auth("https://account.lumintulogic.com/api/user.php", $_GET["token"]));
if (!$userData) {
    echo "
    <script>
        alert('Gagal mendapatkan data user');
        history.back();
    </script>";
    die;
}
session_start();
// var_dump($userData);
// die();
$_SESSION['user_data'] = $userData;
$_SESSION['expiry'] = $_GET['expiry'];
setcookie('X-LUMINTU-REFRESHTOKEN', $_GET['token'], strtotime($_GET['expiry']));
 //var_dump($_COOKIE);
 //die();
if (!isset($_SESSION['user_data'])) {
    echo "
    <script>
        alert('Gagal set Session');
        history.back();
    </script>";
    die;
}

//if (!isset($_COOKIE['X-LUMINTU-REFRESHTOKEN'])) {
    //echo "
    //<script>
        //alert('Gagal set Cookie');
        //history.back();
    //</script>";
    //die;
//}
switch ($_SESSION["user_data"]->{'user'}->{'role_id'}) {
    case 1:
        // Admin
        break;
    case 2:
        // Mentor
        switch ($_GET['page']) {
            case 'index':
                # code...
                header("location: Role/Mentor/index.php");
                break;
            case 'assignment':
                header("location: Role/Mentor/assignment.php?subject_id=" . $_GET['subject_id']);
                break;
            default:
                echo "
            <script>
                history.back();
            </script>";
                break;
        }

        break;
    case 3:
        // Student
        switch ($_GET['page']) {
            case 'index':
                # code...
                header("location: Role/Student/index.php");
                break;
            case 'score':
                header("location: Role/Student/score.php");
                break;
            default:
                # code...
                break;
        }
        break;
    default:
        echo "
    <script>
        history.back();
    </script>";
        break;
}
