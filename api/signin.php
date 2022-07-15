<?php
require_once "get_request.php";
require_once "post_request.php";

session_start();
$arr = array();
$method = $_SERVER['REQUEST_METHOD'];
if ($method == 'POST') {
    if (isset($_POST['token'])) {
        $userData = json_decode(http_request_with_auth("https://account.lumintulogic.com/api/user.php", $_POST['token']));
        // $userData = json_decode(http_request_with_auth("http://192.168.18.84:8000/api/user.php", $_POST['token']));
        $_SESSION['user_data'] = $userData;
        $expiry = $_POST['expiry'];
        setcookie('X-LUMINTU-REFRESHTOKEN', $_POST['token'], strtotime($expiry));
        // print_r($_SESSION);
        $arr = [
            'status' => 200,
            'msg' => 'Berhasil',
            'token' => $_POST['token'],
            'user' => $_SESSION
        ];
    } else {
        $arr = [
            'status' => 400,
            'msg' => 'Gagal',
        ];
    }
} else {
    $arr = [
        'status' => 400,
        'msg' => 'Gagal',
    ];
}
header("Access-Control-Allow-Origin: * ");
header("Access-Control-Allow-Headers: * ");
print_r(json_encode($arr));
// $userData = json_decode(http_request_with_auth("https://account.lumintulogic.com/api/user.php", $access_token));
