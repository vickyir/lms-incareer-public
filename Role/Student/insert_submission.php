<?php
$data = json_decode($_POST['data']);
// print_r($data);
// die();
$arr = array();
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
    "application/x-zip-compressed", //Zip Compressed
    "application/octet-stream", //zip
    "multipart/x-zip", //zip
    "application/x-rar", // rar
    "application/x-rar-compressed" //rar
];
$is_ok = false;
$msg = "";

$token = md5(uniqid());

require_once "../../Model/AssignmentSubmission.php";
require_once "../../Model/Assignments.php";

$assignment = new Assignments;
$deadline = $assignment->getAssignmentById($data->assigId);


$assign = new AssignmentSubmission;
$assign->setAssignmentId($data->assigId);
$assign->setStudentId($data->studId);
$now = $assign->getCurrentDate();
$sub = $assign->getSubmissionByAssignIdAndStudentId();
$subcount = $assign->getSubmissionByAssignIdAndStudentIdGroupBy();
if ((strtotime($now['now()']) > strtotime($deadline['assignment_end_date']))) {
    $arr = [[
        "is_ok" => false,
        "msg" => 'Sudah Melebihi Batas Waktu Pengumpulan',
    ]];
    print_r(json_encode($arr));
    die();
} else if ((strtotime($now['now()']) < strtotime($deadline['assignment_start_date']))) {
    $arr = [[
        "is_ok" => false,
        "msg" => 'Tugas belum dimulai',
    ]];
    print_r(json_encode($arr));
    die();
} else if (count($subcount) >= 3) {
    $arr = [[
        "is_ok" => false,
        "msg" => 'Sudah Melebihi Batas Jumlah Pengumpulan',
    ]];
    print_r(json_encode($arr));
    die();
}
for ($i = 0; $i < count($_FILES['files']['type']); $i++) {
    if (!in_array($_FILES['files']['type'][$i], $validTypeFile)) {
        $arr = [[
            "is_ok" => $is_ok,
            "msg" => 'Ekstensi salah',
        ]];
        print_r(json_encode($arr));
        die();
    }

    if ($_FILES['files']['size'][$i] > 2097152) {
        $arr = [[
            "is_ok" => $is_ok,
            "msg" => 'File tidak boleh lebih dari 2mb',
        ]];
        print_r(json_encode($arr));
        die();
    }
}
if (!empty($sub) and (strtotime($now['now()']) < strtotime($deadline['assignment_end_date']))) {
    // if (strtotime($deadline['assignment_start_date']) < strtotime($now['now()'])) {
    //     print_r('sudah dimulai');
    // } else if (strtotime($deadline['assignment_start_date']) > strtotime($now['now()'])) {
    //     print_r('belum dimulai');
    // }
    // die();
    if ($sub[0]['is_finish'] == 0) { //untuk pertama kali submit file
        $assign->setSubmissionFileName('N/A');
        $del = $assign->deleteNAassignmentSubmission();

        for ($i = 0; $i < $data->count; $i++) {
            $objAssg = new AssignmentSubmission;
            date_default_timezone_set("Asia/Bangkok");
            $dateupload = date("Y-m-d H:i:s");

            $objAssg->setSubmissionFileName("");
            $objAssg->setSubmissionUploadDate($dateupload);
            $objAssg->setAssignmentId($data->assigId);
            $objAssg->setStudentId($data->studId);
            $objAssg->setSubmissionToken($token);
            $objAssg->setSubmissionStatus(1);
            $objAssg->setIsFinished(0);

            $save = $objAssg->saveSubmission();

            array_push($arr, $save);
        }
    } else if ($sub[0]['is_finish'] == 1 and (strtotime($now['now()']) < strtotime($deadline['assignment_end_date']))) { //untuk update file

        date_default_timezone_set('Asia/Jakarta');
        $now =  date("Y-m-d h:i:s");
        require_once "../../Model/AssignmentSubmission.php";
        $objsubmit = new AssignmentSubmission;
        $objsubmit->setStudentId($data->studId);
        $objsubmit->setAssignmentId($data->assigId);
        $csub = $objsubmit->getSubmissionByAssignIdAndStudentIdGroupBy();
        if (count($csub) < 3) {
            $assign->setSubmissionStatus('nonaktif');
            $assign->setIsFinished(0);
            $assign->updateStatusAssignmentSubmission();
            for ($i = 0; $i < $data->count; $i++) {
                $objAssg = new AssignmentSubmission;
                date_default_timezone_set("Asia/Bangkok");
                $dateupload = date("Y-m-d H:i:s");

                $objAssg->setSubmissionFileName("");
                $objAssg->setSubmissionUploadDate($dateupload);
                $objAssg->setAssignmentId($data->assigId);
                $objAssg->setStudentId($data->studId);
                $objAssg->setSubmissionToken($token);
                $objAssg->setSubmissionStatus(1);
                $objAssg->setIsFinished(0);


                $save = $objAssg->saveSubmission();

                array_push($arr, $save);
            }
        }
    }
} else if (empty($sub) and (strtotime($now['now()']) < strtotime($deadline['assignment_end_date']))) {
    // print_r('Kondisi apabila terhapus');
    // die();
    for ($i = 0; $i < $data->count; $i++) {
        // print_r('else');

        $objAssg = new AssignmentSubmission;
        date_default_timezone_set("Asia/Bangkok");
        $dateupload = date("Y-m-d H:i:s");

        $objAssg->setSubmissionFileName("");
        $objAssg->setSubmissionUploadDate($dateupload);
        $objAssg->setAssignmentId($data->assigId);
        $objAssg->setStudentId($data->studId);
        $objAssg->setSubmissionToken($token);
        $objAssg->setSubmissionStatus(1);
        $objAssg->setIsFinished(0);

        $save = $objAssg->saveSubmission();

        array_push($arr, $save);
    }
}

print_r(json_encode($arr));
