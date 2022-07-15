<?php

session_start();
// print_r($_FILES);
// die();
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
    case 2:
        echo "
        <script>
            alert('Akses ditolak!');
            location.replace('../Mentor/');
        </script>
        ";
        break;
    default:
        break;
}

// print_r($_FILES['files']);
// print_r(json_decode($_POST['data']));
// // print_r(json_decode($_POST['file']));
// die;

$submission_list = json_decode($_POST['data']);
$countSuccess = 0;

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
    "application/x-gzip", // zip
    "application/x-zip-compressed", //zip compressed
    "application/zip", //zip
    "application/octet-stream", //zip
    "multipart/x-zip", //zip
    "application/x-rar", // rar
    "application/x-rar-compressed" //rar
];

$is_ok = false;
$msg = "";

for ($i = 0; $i < count($_FILES['files']['name']); $i++) {
    if (!in_array($_FILES['files']['type'][$i], $validTypeFile)) {
        $msg = "Format file tidak didukung!";
        goto out;
    }

    if ($_FILES['files']['size'][$i] > 2097152) {
        $msg = "Batas maksimal upload file 2 MB!";
        goto out;
    }

    $path = '../../Upload/Assignment/Submission/';
    $token = uniqid();
    $fn = $token . '_' . $_FILES['files']['name'][$i];
    $move = move_uploaded_file($_FILES['files']['tmp_name'][$i], $path . $fn);

    if ($move) {
        $countSuccess++;
        require_once "../../Model/AssignmentSubmission.php";
        $objAssig = new AssignmentSubmission;
        $now = $objAssig->getCurrentDate();
        date_default_timezone_set("Asia/Jakarta");
        $dateupload = date("Y-m-d H:i:s");

        $objAssig->setSubmissionFileName($fn);
        $objAssig->setSubmissionUploadDate($now['now()']);
        $objAssig->setAssignmentSubmissionId((int) $submission_list[$i]->{'submission_id'});
        $objAssig->setIsFinished(1);

        $update = $objAssig->updateSubmission();
    }
}

if ($countSuccess == count($_FILES['files']['name'])) {
    $msg = "Berhasil mengirim tugas!";
    $is_ok = true;
    goto out;
} else {
    $msg = "Gagal mengirim tugas!";
    // for ($i = 0; $i < count($_FILES['files']['name']); $i++) {
    //     require "../../Model/AssignmentSubmission.php";

    //     $objSub = new AssignmentSubmission;
    //     $objSub->setAssignmentSubmissionId((int) $submission_list[$i]->{'submission_id'});
    //     $objSub->deleteSubmissionById();
    // }
    goto out;
}

out: {
    $data = [
        "is_ok" => $is_ok,
        "msg" => $msg,
    ];

    print_r(json_encode($data));
}
