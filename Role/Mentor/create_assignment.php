<?php
session_start();

$loginPath = "../../login.php";
if (!isset($_COOKIE['X-LUMINTU-REFRESHTOKEN'])) {
    unset($_SESSION['user_data']);
    header("location: " . $loginPath);
}

if (!isset($_SESSION['user_data'])) {
    header("location: " . $loginPath);
    die;
}


switch ($_SESSION['user_data']->{'user'}->{'role_id'}) {
    case 1:
        echo "
        <script>
            alert('Akses ditolak!');
            location.replace('../Admin/');
        </script>
        ";
        break;
    case 3:
        echo "
        <script>
            alert('Akses ditolak!');
            location.replace('../Student/');
        </script>
        ";
        break;
    default:
        break;
}
$is_ok = false;
$msg = "";

$resp = array();

if (isset($_POST['data'])) {
    $data = json_decode($_POST['data']);
    $arrayData = array(
        "title" => $data->{'title'},
        "desc" => $data->{'description'},
        "start-date" => $data->{'startDate'},
        "end-date" => $data->{'dueDate'},
        "assign_type" => $data->{'assgType'}
    );


    require_once "../../api/get_api_data.php";
    require_once "../../api/get_request.php";


    $userData = array();
    $modulData = array();
    $modulJSON = json_decode(http_request("https://lessons.lumintulogic.com/api/modul/read_modul_rows.php"));
    $token = $_COOKIE['X-LUMINTU-REFRESHTOKEN'];
    $usersData = json_decode(http_request_with_auth("https://account.lumintulogic.com/api/users.php", $token));
    //print_r($usersData);
    //die();


    for ($i = 0; $i < count($modulJSON->{'data'}); $i++) {
        if ($modulJSON->{'data'}[$i]->{'id'} == (int)$_GET['course_id']) {
            for ($j = 0; $j < count($usersData->{'user'}); $j++) {
                if ($modulJSON->{'data'}[$i]->{'batch_id'} == $usersData->{'user'}[$j]->{'batch_id'} && $usersData->{'user'}[$j]->{'role_id'} == 3) {
                    array_push($userData, $usersData->{'user'}[$j]);
                }
            }
        }
    }

    //print_r($usersData);
    //die();
    $validTypeFile = [
        "image/png", // png
        "image/jpg", // jpg
        "image/jpeg", // jpeg
        "text/plain", // txt or html
        "application/pdf", // pdf
        "application/vnd.ms-powerpoint",
        "application/vnd.openxmlformats-officedocument.wordprocessingml.document", // docx
        "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet", // xlsx
        "application/vnd.openxmlformats-officedocument.presentationml.presentation", // pptx
        "application/vnd.ms-excel", // xls
        "application/msword", // doc
        "application/zip", // zip
        "application/x-rar",
        "application/x-gzip", // zip
        "application/x-zip-compressed", // rar
        "application/octet-stream", //zip
        "application/x-rar-compressed", //rar
    ];
    for ($i = 0; $i < count($modulJSON->{'data'}); $i++) {
        if ($modulJSON->{'data'}[$i]->{'id'} == $_GET['subject_id']) {
            array_push($modulData, $modulJSON->{'data'}[$i]);
        }
    }
    $date_start = explode(" ", date("Y-m-d H:i:s", strtotime($arrayData['start-date'])));
    $date_end = explode(" ", date("Y-m-d H:i:s", strtotime($arrayData['end-date'])));

    $start_date = $date_start[0] . "T" . $date_start[1];
    $end_date = $date_end[0] . "T" . $date_end[1];

    // Validasi start date dan end date
    if ((strtotime($date_start[0])) < strtotime(date('D'))) {
        $is_ok = false;
        $msg = "Data start date tidak dapat kurang dari hari ini";
    } else if ((strtotime($date_end[0])) < strtotime(date('D'))) {
        $is_ok = false;
        $msg = "Data end date tidak dapat kurang dari hari ini";
    } else if ((strtotime($date_start[0])) > strtotime($date_end[0])) {
        $is_ok = false;
        $msg = "Data start date tidak dapat lebih dari end date";
    } else if (empty($arrayData['title']) || empty($start_date) || empty($end_date) || empty($arrayData['desc']) || empty($_FILES)) {
        $is_ok = false;
        $msg = "Tidak Boleh Kosong";
    } else if (!in_array($_FILES['file']['type'], $validTypeFile)) {
        $is_ok = false;
        $msg = "Format Tidak Didukung";
    } else if ($_FILES['file']['size'] > 2097152) {
        $is_ok = false;
        $msg = "file tidak boleh lebih dari 2mb";
    } else {
        $arr = [
            "event_type_id" => 2,
            "created_by" => $_SESSION['user_data']->{'user'}->{'user_first_name'} . " " . $_SESSION['user_data']->{'user'}->{'user_last_name'},
            "event_start_time" => $start_date,
            "event_name" => $arrayData['title'],
            "event_end_time" => $end_date,
            "event_description" => $arrayData['desc'],
            "batch_id" => $_SESSION['user_data']->{'user'}->{'batch_id'},
            "modul_id" => $modulData[0]->{'id'}
        ];

        $payload = json_encode($arr);

        $api_schedule = 'https://q4optgct.directus.app/items/events';

        $ch = curl_init($api_schedule);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

        // Set HTTP Header for POST request 
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($payload)
            )
        );

        // Submit the POST request
        $result = curl_exec($ch);
        $res = json_decode($result);
        $event_id = $res->{'data'}->{"event_id"};
        $arrayData['event_id'] = $event_id;

        // var_dump($result);

        // Close cURL session handle
        curl_close($ch);
        // print_r($result);

        require "../../Model/Assignments.php";
        $objAsign = new Assignments;
        // $create = $objAsign->createAssignment($_POST, $_FILES, $_GET['subject_id'], $_SESSION['user']->{'user_id'}, $userData);
        $create = $objAsign->createAssignment($arrayData, $_FILES, $_GET['subject_id'], $_SESSION['user_data']->{'user'}->{'user_id'}, $userData);

        $create_status = $create['is_ok'] ? "true" : "false";

        if ($create['is_ok'] && $event_id != null) {

            $is_ok = true;
            $msg = $create['msg'];
        } else {
            $msg = $create['msg'];
        }
    }

    $resp = array(
        "is_ok" => $is_ok,
        "msg" => $msg
    );
} else {
    $resp = array(
        "is_ok" => false,
        "msg" => "You don't have access to this file !!!"
    );
}

print_r(json_encode($resp));
// print_r($arrayData);
