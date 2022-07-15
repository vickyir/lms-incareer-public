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


require_once "../../api/get_api_data.php";
require_once "../../api/get_request.php";

$userData = array();
$modulJSON = json_decode(http_request("https://lessons.lumintulogic.com/api/modul/read_modul_rows.php"));
$token = $_COOKIE['X-LUMINTU-REFRESHTOKEN'];
$usersData = json_decode(http_request_with_auth("https://account.lumintulogic.com/api/users.php", $token));


for ($i = 0; $i < count($modulJSON->{'data'}); $i++) {
    if ($modulJSON->{'data'}[$i]->{'id'} == (int)$_GET['course_id']) {
        for ($j = 0; $j < count($usersData->{'user'}); $j++) {
            if ($modulJSON->{'data'}[$i]->{'batch_id'} == $usersData->{'user'}[$j]->{'batch_id'} && $usersData->{'user'}[$j]->{'role_id'} == 3) {
                array_push($userData, $usersData->{'user'}[$j]);
            }
        }
    }
}


require_once('../../Model/AssignmentSubmission.php');
require_once('../../Model/Assignments.php');

$objSubmission = new AssignmentSubmission;
$objSubmission->setAssignmentId($_GET['assignment_id']);
$submissionData = $objSubmission->getSubmissionAndScoreByAssignmentId();

$objAssignment = new Assignments;
$objAssignment->setAssignmentId($_GET['assignment_id']);
$assignData = $objAssignment->getAssignmentByAssignmentId($_GET['assignment_id']);


$submissionStudent = array();

for ($i = 0; $i < count($userData); $i++) {
    for ($j = 0; $j < count($submissionData); $j++) {
        if ($userData[$i]->{'user_id'} == $submissionData[$j]['student_id']) {
            array_push($submissionStudent, array(
                "student_id" => $userData[$i]->{'user_id'},
                "assignment_id" => $submissionData[$j]['assignment_id'],
                "student_name" => $userData[$i]->{'user_username'},
                "submitted_date" => $submissionData[$j]['submitted_date'],
                "submission_token" => $submissionData[$j]['submission_token'],
                "submission_filename" => $submissionData[$j]['submission_filename'],
                // "mentor_id" => $submissionData[$j]['mentor_id'],
                // "score_value" => $submissionData[$j]['score_value']
            ));
        }
    }
}

// var_dump($submissionStudent);

$notSubmitted = array();
$notSubmittedData = $objSubmission->getNotSubmitSubmission();
// var_dump($notSubmittedData);


for ($i = 0; $i < count($userData); $i++) {
    for ($j = 0; $j < count($notSubmittedData); $j++) {
        if ($userData[$i]->{'user_id'} == $notSubmittedData[$j]['student_id']) {

            array_push($notSubmitted, array(
                "student_id" => $userData[$i]->{'user_id'},
                "assignment_id" => $notSubmittedData[$j]['assignment_id'],
                "student_name" => $userData[$i]->{'user_username'},
                "submitted_date" => $notSubmittedData[$j]['submitted_date'],
                "submission_token" => $notSubmittedData[$j]['submission_token'],
                "submission_filename" => $notSubmittedData[$j]['submission_filename']
                // "score_id" => $notSubmittedData[$j]['score_id'],
                // "score_value" => $notSubmittedData[$j]['score_value']
            ));
        }
    }
}

// var_dump($submissionStudent);
// var_dump($userData);

if (isset($_POST['submit'])) {
    require_once('../../Model/Scores.php');
    $score = new Scores;
    $score->setScoreId($_POST['sid']);
    $score->setScoreValue($_POST['score']);
    $score->setMentorId($_SESSION['user_data']->{'user'}->{'user_id'});

    if ($_POST['score'] == "") {
        echo "
        <script>
            alert('Tidak Boleh Kosong');
        </script>";
    } else if ($_POST['score'] < 0 || $_POST['score'] > 100) {
        echo "
        <script>
            alert('Score dari 0-100');
        </script>";
    } else {
        if ($score->updateScore()) {
            echo "
            <script>
                alert('Berhasil Menambahkan score');
                location.replace('assignment_collection.php?course_id=" . $_GET['course_id'] . "&assignment_id=" . $_GET['assignment_id'] . "&subject_id=" . $_GET['subject_id'] . "');
            </script>";
        } else {
            echo "
            <script>
                alert('Gagal');
                location.replace('assignment_collection.php?course_id=" . $_GET['course_id'] . "&assignment_id=" . $_GET['assignment_id'] . "&subject_id=" . $_GET['subject_id'] . "');
            </script>";
        }
    }
}
if (isset($_POST['submit1'])) {
    require_once('../../Model/Scores.php');
    $score = new Scores;
    $score->setScoreId($_POST['sid1']);
    $score->setScoreValue($_POST['score1']);
    $score->setMentorId($_SESSION['user_data']->{'user'}->{'user_id'});
    // $update  = $score->updateScore();
    if ($_POST['score1'] == "") {
        echo "
        <script>
            alert('Tidak Boleh Kosong');
        </script>";
    } else if ($_POST['score1'] < 0 || $_POST['score1'] > 100) {
        echo "
        <script>
            alert('Score dari 0-100');
        </script>";
    } else {
        if ($score->updateScore()) {
            echo "
            <script>
                alert('Berhasil Menambahkan score');
                location.replace('assignment_collection.php?course_id=" . $_GET['course_id'] . "&assignment_id=" . $_GET['assignment_id'] . "&subject_id=" . $_GET['subject_id'] . "');
            </script>";
        } else {
            echo "
            <script>
                alert('Gagal');
                location.replace('assignment_collection.php?course_id=" . $_GET['course_id'] . "&assignment_id=" . $_GET['assignment_id'] . "&subject_id=" . $_GET['subject_id'] . "');
            </script>";
        }
    }
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lumintu Logic</title>

    <!-- Favicon -->
    <link rel="icon" href="../../Img/logo/logo_lumintu1.ico">

    <!-- Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Jqueey -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">


    <!-- CSS -->
    <!-- <link rel="stylesheet" href="./CSS/UploafField.css"> -->


    <!-- Tailwindcss -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://unpkg.com/flowbite@1.4.1/dist/flowbite.min.css" />

    <!-- Intro Js -->
    <link rel="stylesheet" href="https://unpkg.com/intro.js/minified/introjs.min.css">
    <script src="https://unpkg.com/intro.js/minified/intro.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        montserrat: ["Montserrat"],
                    },
                    colors: {
                        "dark-green": "#1E3F41",
                        "light-green": "#659093",
                        "cream": "#DDB07F",
                        "cgray": "#F5F5F5",
                    }
                }
            }
        }
    </script>
    <style>
        p.note {
            font-size: 1rem;
            color: red;
        }

        label.error {
            color: red;
            font-size: 1rem;
            display: block;
            margin-top: 5px;
        }

        label.error.fail-alert {

            line-height: 1;
            padding: 2px 0 6px 6px;

        }

        .in-active {
            width: 80px !important;
            padding: 20px 15px !important;
            transition: .5s ease-in-out;
        }

        .in-active ul li p {
            display: none !important;
        }

        .in-active ul li a {
            padding: 15px !important;
        }

        .in-active h2,
        .in-active h4,
        .in-active .logo-incareer {
            display: none !important;
        }

        /* .hidden {
            display: none !important;
        } */

        .sidebar {
            transition: .5s ease-in-out;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.slim.js" integrity="sha256-HwWONEZrpuoh951cQD1ov2HUK5zA5DwJ1DNUXaM6FsY=" crossorigin="anonymous"></script>
</head>

<body>
    <div class="responsive-top sticky top-0 z-30 bg-white p-5 sm:hidden">
        <div class="flex justify-center bg-gray-300 p-2 rounded-lg">
            lms in-career
        </div>
        <div class="container flex flex-column justify-between mt-4 mb-4">
            <img class="w-[150px] logo-incareer" src="../../Img/logo/logo_lumintu.png" alt="Logo Lumintu Logic">
            <img src="../../Img/icons/toggle_icons.svg" alt="toggle_dashboard" class="w-8 cursor-pointer" id="btnToggle2">
        </div>
    </div>
    <div class="flex items-center">
        <!-- Left side (Sidebar) -->
        <div class="bg-white w-[350px] h-screen px-8 py-6 sm:flex flex-col justify-between sidebar in-active hidden">
            <!-- Top nav -->
            <div class="flex flex-col gap-y-6">
                <!-- Header -->
                <div class="flex items-center space-x-4 px-2">
                    <img src="../../Img/icons/toggle_icons.svg" alt="toggle_dashboard" class="w-8 cursor-pointer" id="btnToggle">
                    <img class="w-[150px] logo-incareer" src="../../Img/logo/logo_lumintu.png" alt="Logo Lumintu Logic">
                </div>

                <hr class="border-[1px] border-opacity-50 border-[#93BFC1]" />

                <!-- List Menus -->
                <div>
                    <ul class="flex flex-col gap-y-1">
                        <!-- ICON DAN TEXT DASHBOARD -->

                        <li>
                            <a href="https://account.lumintulogic.com/dashboard.php" class="flex items-center gap-x-4 h-[50px] rounded-xl px-4 hover:bg-cream text-dark-green hover:text-white">
                                <img class="w-5" src="../../Img/icons/home_icon.svg" alt="Dashboard Icon">
                                <p class="font-semibold">Beranda</p>
                            </a>
                        </li>
                        <!-- ICON DAN TEXT FORUM COURSES -->
                        <li>
                            <a href="http://lessons.lumintulogic.com/auth.php?token=<?php echo ($_COOKIE['X-LUMINTU-REFRESHTOKEN']); ?>&expiry=<?php echo $_SESSION["expiry"]; ?>" class="flex items-center gap-x-4 h-[50px] rounded-xl px-4 hover:bg-cream text-dark-green hover:text-white">
                                <img class="w-5" src="../../Img/icons/course_dark_icon.svg" alt="Course Icon">
                                <p class="font-semibold">Materi</p>
                            </a>
                        </li>
                        <!-- Icon Assignment -->
                        <li>
                            <a href="./index.php" class="flex items-center gap-x-4 h-[50px] rounded-xl px-4 bg-cream">
                                <img class="w-5" src="../../Img/icons/assignment_white_icon.svg" alt="Assignment Icon">
                                <p class="text-white font-semibold">Penugasan</p>
                            </a>
                        </li>
                        <!-- ICON DAN TEXT CONSULT -->
                        <li>
                            <a href="https://consultation.lumintulogic.com/auth.php?token=<?= $_COOKIE['X-LUMINTU-REFRESHTOKEN']; ?>&expiry=<?= $_SESSION['expiry']; ?>" class="flex items-center gap-x-4 h-[50px] rounded-xl px-4 hover:bg-cream text-dark-green hover:text-white">
                                <img class="w-5" src="../../Img/icons/consult_icon.svg" alt="Consult Icon">
                                <p class="font-semibold">Konsultasi</p>
                            </a>
                        </li>
                        <!-- ICON DAN TEXT SCHEDULE -->
                        <li>
                            <a href="https://schedule.lumintulogic.com/auth.php?token=<?php echo ($_COOKIE['X-LUMINTU-REFRESHTOKEN']); ?>&expiry=<?php echo $_SESSION["expiry"]; ?>" class="flex items-center gap-x-4 h-[50px] rounded-xl px-4 hover:bg-cream text-dark-green hover:text-white">
                                <img class="w-5" src="../../Img/icons/schedule_icon.svg" alt="Schedule Icon">
                                <p class="font-semibold">Jadwal</p>
                            </a>
                        </li>
                        <!-- ICON DAN TEXT ATTENDANCE -->
                        <!-- <li>
                            <a href="" class="flex items-center gap-x-4 h-[50px] rounded-xl px-4 hover:bg-cream text-dark-green hover:text-white">
                                <img class="w-5" src="../../Img/icons/attendance_icon.svg" alt="Attendance Icon">
                                <p class="font-semibold">Attendance</p>
                            </a>
                        </li> -->
                        <!-- ICON DAN TEXT SCORE -->
                        <!-- <li>
                            <a href="" class="flex items-center gap-x-4 h-[50px] rounded-xl px-4 hover:bg-cream text-dark-green hover:text-white">
                                <img class="w-5" src="../../Img/icons/score_icon.svg" alt="Score Icon">
                                <p class="font-semibold">Score</p>
                            </a>
                        </li> -->

                    </ul>
                </div>
            </div>

            <!-- Bottom nav -->
            <div>
                <ul class="flex flex-col ">
                    <!-- ICON DAN TEXT HELP -->
                    <li>
                        <a id="btnHelp" href="#" class="flex items-center gap-x-4 h-[50px] rounded-xl px-4 hover:bg-cream text-dark-green hover:text-white">
                            <img class="w-5" src="../../Img/icons/help_icon.svg" alt="Help Icon">
                            <p class="font-semibold">Bantuan</p>
                        </a>
                    </li>
                    <!-- ICON DAN TEXT LOG OUT -->
                    <li>
                        <a href="../../logout.php" class="flex items-center gap-x-4 h-[50px] rounded-xl px-4 hover:bg-cream text-dark-green hover:text-white" onclick=" return confirm('Anda yakin ingin keluar?')">
                            <img class="w-5" src="../../Img/icons/logout_icon.svg" alt="Log out Icon">
                            <p class="font-semibold">Keluar</p>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Mobile navbar -->
        <div id="left-nav" class="bg-opacity-50 bg-gray-500 fixed top-[130px] bottom-0 overflow-y-scroll inset-x-0 hidden z-10 transition-all ease-in-out duration-500 sm:hidden">

            <div class="bg-white w-[250px] h-screen px-6 py-6 ">
                <!-- Top nav -->
                <div class="flex flex-col gap-y-6">

                    <!-- List Menus -->
                    <div>
                        <ul class="flex flex-col gap-y-1">
                            <li>
                                <a href="" class="flex items-center gap-x-4 h-[50px] px-4" id="profil_image">
                                    <img class="w-5" src="../../Img/icons/default_profile.svg" alt="Profile Image">
                                    <p class="font-semibold"><?= $_SESSION['user_data']->{'user'}->{'user_first_name'} . " " . $_SESSION['user_data']->{'user'}->{'user_last_name'} ?></p>
                                    <!-- <p class="font-semibold"></p> -->
                                </a>
                                <!-- ICON DAN TEXT DASHBOARD -->
                            </li>
                            <li>
                                <a href="https://account.lumintulogic.com/dashboard.php" class="flex items-center gap-x-4 h-[50px] rounded-xl px-4 hover:bg-cream text-dark-green hover:text-white">
                                    <img class="w-5" src="../../Img/icons/home_icon.svg" alt="Dashboard Icon">
                                    <p class="font-semibold">Beranda</p>
                                </a>
                            </li>
                            <!-- ICON DAN TEXT FORUM COURSES -->
                            <li>
                                <a href="http://lessons.lumintulogic.com/auth.php?token=<?php echo ($_COOKIE['X-LUMINTU-REFRESHTOKEN']); ?>&expiry=<?php echo $_SESSION["expiry"]; ?>" class="flex items-center gap-x-4 h-[50px] rounded-xl px-4 hover:bg-cream text-dark-green hover:text-white">
                                    <img class="w-5" src="../../Img/icons/course_dark_icon.svg" alt="Course Icon">
                                    <p class="font-semibold">Materi</p>
                                </a>
                            </li>
                            <!-- Icon Assignment -->
                            <li>
                                <a href="./index.php" class="flex items-center gap-x-4 h-[50px] rounded-xl px-4 bg-cream">
                                    <img class="w-5" src="../../Img/icons/assignment_white_icon.svg" alt="Assignment Icon">
                                    <p class="text-white font-semibold">Penugasan</p>
                                </a>
                            </li>
                            <!-- ICON DAN TEXT CONSULT -->
                            <li>
                                <a href="https://consultation.lumintulogic.com/auth.php?token=<?= $_COOKIE['X-LUMINTU-REFRESHTOKEN']; ?>&expiry=<?= $_SESSION['expiry']; ?>" class="flex items-center gap-x-4 h-[50px] rounded-xl px-4 hover:bg-cream text-dark-green hover:text-white">
                                    <img class="w-5" src="../../Img/icons/consult_icon.svg" alt="Consult Icon">
                                    <p class="font-semibold">Konsultasi</p>
                                </a>
                            </li>
                            <!-- ICON DAN TEXT SCHEDULE -->
                            <li>
                                <a href="https://schedule.lumintulogic.com/auth.php?token=<?php echo ($_COOKIE['X-LUMINTU-REFRESHTOKEN']); ?>&expiry=<?php echo $_SESSION["expiry"]; ?>" class="flex items-center gap-x-4 h-[50px] rounded-xl px-4 hover:bg-cream text-dark-green hover:text-white">
                                    <img class="w-5" src="../../Img/icons/schedule_icon.svg" alt="Schedule Icon">
                                    <p class="font-semibold">Jadwal</p>
                                </a>
                            </li>
                            <!-- ICON DAN TEXT ATTENDANCE -->
                            <!-- <li>
                                <a href="" class="flex items-center gap-x-4 h-[50px] rounded-xl px-4 hover:bg-cream text-dark-green hover:text-white">
                                    <img class="w-5" src="../../Img/icons/attendance_icon.svg" alt="Attendance Icon">
                                    <p class="font-semibold">Attendance</p>
                                </a>
                            </li> -->
                            <!-- ICON DAN TEXT SCORE -->
                            <!-- <li>
                                <a href="" class="flex items-center gap-x-4 h-[50px] rounded-xl px-4 hover:bg-cream text-dark-green hover:text-white">
                                    <img class="w-5" src="../../Img/icons/score_icon.svg" alt="Score Icon">
                                    <p class="font-semibold">Score</p>
                                </a>
                            </li> -->

                            <!-- ICON DAN TEXT HELP -->
                            <li>
                                <a href="#" class="flex items-center gap-x-4 h-[50px] rounded-xl px-4 hover:bg-cream text-dark-green hover:text-white">
                                    <img class="w-5" src="../../Img/icons/help_icon.svg" alt="Help Icon">
                                    <p class="font-semibold">Bantuan</p>
                                </a>
                            </li>
                            <!-- ICON DAN TEXT LOG OUT -->
                            <li>
                                <a href="../../logout.php" class="flex items-center gap-x-4 h-[50px] rounded-xl px-4 hover:bg-cream text-dark-green hover:text-white" onclick=" return confirm('Anda yakin ingin keluar?')">
                                    <img class="w-5" src="../../Img/icons/logout_icon.svg" alt="Log out Icon">
                                    <p class="font-semibold">Keluar</p>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>


        <!-- Right side -->
        <div class="bg-cgray w-full h-screen px-8 lg:px-10 py-6 flex flex-col gap-y-6 overflow-y-scroll">
            <!-- Header / Profile -->
            <div class="items-center gap-x-4 justify-end hidden sm:flex">
                <img class="w-10" src="../../Img/icons/default_profile.svg" alt="Profile Image">
                <p class="text-dark-green font-semibold"><?= $_SESSION['user_data']->{'user'}->{'user_first_name'} . " " . $_SESSION['user_data']->{'user'}->{'user_last_name'} ?></p>
            </div>



            <!-- Topic Title -->

            <div class="flex flex-col gap-y-2 topic-title">
                <p class="text-xl text-center sm:text-2xl xl:text-4xl text-dark-green font-semibold">Assignment Collection</p>
                <p class="text-base sm:text-lg lg:text-xl xl:text-2xl text-center text-dark-green">Tugas# <?= $assignData['assignment_name']; ?></p>
            </div>

            <a href="assignment.php?course_id=<?= $_GET['course_id']; ?>&subject_id=<?= $_GET['subject_id']; ?>" class="text-dark-green flex items-center font-medium text-sm space-x-2 mb-8">
                <img class="w-4 lg:w-5" src="../../Img/icons/back_icons.svg" alt="Back Image">
                <p class="ml-2">Kembali</p>
            </a>

            <div id="table-finished">
                <p class="text-lg sm:text-xl lg:text-2xl xl:text-3xl text-dark-green font-semibold">Tugas Selesai</p>
            </div>

            <!-- Table Assignment -->
            <div class="container-lg">
                <div class="relative overflow-x-auto">
                    <table class="shadow-lg bg-white rounded-xl" style="width: 100%">
                        <colgroup>
                            <col span="1" style="width: 5%">
                            <col span="1" style="width: 20%">
                            <col span="1" style="width: 15%">
                            <col span="1" style="width: 20%">
                            <col span="1" style="width: 10%">
                            <col span="1" style="width: 10%">

                        </colgroup>
                        <thead>
                            <tr class="text-dark-green text-sm lg:text-base">
                                <th class="border-b text-left px-4 py-2">No</th>
                                <th class="border-b text-center px-4 py-2">Nama</th>
                                <th class="border-b text-center px-4 py-2">Tanggal Pengumpulan</th>
                                <th class="border-b text-center px-4 py-2">File</th>
                                <th class="border-b text-center px-4 py-2">Nilai</th>
                                <th class="border-b text-center px-4 py-2">Aksi</th>

                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            foreach ($submissionStudent as $key => $item) {

                            ?>
                                <tr class="text-sm lg:text-base">
                                    <td class="border-b px-4 py-2"><?= $no ?></td>
                                    <td class="border-b px-4 py-2 text-center"><?= $item['student_name']  ?></td>
                                    <td class="border-b px-4 py-2 text-center"><?= $item['submitted_date']  ?></td>
                                    <td class="border-b px-4 py-2 filename">
                                        <?php
                                        require_once('../../Model/AssignmentSubmission.php');
                                        $multipleup = new AssignmentSubmission;
                                        $multipleup->setSubmissionToken($item['submission_token']);
                                        $file = $multipleup->getSubmissionByToken();
                                        // $jumlahfile = $multipleup->getRowSubmissionByToken();


                                        foreach ($file as $key => $val) { ?>
                                            <a href="download.php?file=<?= $val['submission_filename']; ?>">
                                                <p class="hover:text-cream"><?= $val['submission_filename']; ?></p>
                                            </a>

                                        <?php
                                        } ?>
                                    </td>

                                    <?php
                                    require_once "../../Model/Scores.php";
                                    $objScores = new Scores;
                                    $objScores->setStudentId($item['student_id']);
                                    $objScores->setAssignmentId($item['assignment_id']);
                                    $score = $objScores->getScoreByStudentIdAndAssignmentId();
                                    ?>
                                    <td class="border-b px-4 py-2 text-center score"><?= $score['score_value']  ?></td>

                                    <td class="border-b px-4 py-2"><img class="w-5 lg:w-7 mx-auto cursor-pointer" src="../../Img/icons/edit_icon.svg" data-modal-toggle="defaultModal" data-username="<?= $item['student_name']; ?>" data-tooltip-target="tooltip-default<?= $item['student_name']; ?>" data-scoreid="<?= $score['score_id'] ?>" data-student-id="<?= $item['student_id'] ?>" data-scorevalue="<?= $score['score_value']  ?>" alt="Edit Icon" type="button" data-target="#defaultModal" id="editbtn"></td>
                                    <?php if ($score['mentor_id'] == 0) {
                                    ?>
                                        <div id="tooltip-default<?= $item['student_name']; ?>" role="tooltip" class="inline-block absolute invisible z-10 py-2 px-3 text-sm font-medium text-white bg-black rounded-lg shadow-sm opacity-0 transition-opacity duration-300 tooltip ">
                                            Belum mengubah score
                                            <div class="tooltip-arrow" data-popper-arrow></div>
                                        </div>
                                    <?php
                                    } elseif ($score['score_value'] != 0) {
                                    ?>
                                        <div id="tooltip-default<?= $item['student_name']; ?>" role="tooltip" class="inline-block absolute invisible z-10 py-2 px-3 text-sm font-medium text-white bg-black rounded-lg shadow-sm opacity-0 transition-opacity duration-300 tooltip ">
                                            Sudah mengubah score
                                            <div class="tooltip-arrow" data-popper-arrow></div>
                                        </div>
                                    <?php
                                    }
                                    ?>
                                </tr>

                            <?php $no++;
                            } ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-8" id="table-unfinished">
                <p class="text-lg sm:text-xl lg:text-2xl xl:text-3xl text-dark-green font-semibold">Tugas Belum Selesai</p>
            </div>

            <!-- Table unfinished assignment -->
            <div class="container-lg">
                <div class="relative overflow-x-auto">
                    <table class="shadow-lg bg-white rounded-xl" style="width: 100%">
                        <colgroup>
                            <col span="1" style="width: 10%">
                            <col span="1" style="width: 25%">
                            <col span="1" style="width: 10%">
                            <col span="1" style="width: 20%">
                            <col span="1" style="width: 15%">
                        </colgroup>
                        <thead>
                            <tr class="text-dark-green text-sm lg:text-base">
                                <th class="border-b text-left px-4 py-2">No</th>
                                <th class="border-b text-center px-4 py-2">Nama</th>
                                <th class="border-b text-center px-4 py-2">Tanggal Pengumpulan</th>
                                <th class="border-b text-center px-4 py-2">File</th>
                                <th class="border-b text-center px-4 py-2">Nilai</th>
                                <th class="border-b text-center px-4 py-2">Aksi</th>

                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            foreach ($notSubmitted as $key => $item) {

                            ?>
                                <tr class="text-sm lg:text-base">
                                    <td class="border-b px-4 py-2 text-red-600"><?= $no  ?></td>
                                    <td class="border-b px-4 py-2 text-center text-red-600"><?= $item['student_name']  ?></td>
                                    <td class="border-b px-4 py-2 text-center text-red-600">-</td>
                                    <td class="border-b px-4 py-2 text-center text-red-600"><?= $item['submission_filename']; ?></td>

                                    <?php
                                    require_once "../../Model/Scores.php";
                                    $objScores = new Scores;
                                    $objScores->setStudentId($item['student_id']);
                                    $objScores->setAssignmentId($item['assignment_id']);
                                    $score = $objScores->getScoreByStudentIdAndAssignmentId();
                                    ?>
                                    <td class="border-b px-4 py-2 text-center text-red-600"><?= $score['score_value'] ?></td>

                                    <td class="border-b px-4 py-2">
                                        <img class="w-5 lg:w-7 mx-auto cursor-pointer" src="../../Img/icons/edit_icon.svg" alt="Edit Icon" type="button" data-scoreid="<?= $score['score_id']; ?>" data-username="<?= $item['student_name']  ?>" data-scorevalue="<?= $score['score_value']; ?>" data-target="#defaultModal1" id="editbtn1">
                                    </td>
                                </tr>
                            <?php $no++;
                            } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- modal FINISHED ASSIGNMENT -->
    <div id="defaultModal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 w-full md:inset-0 h-modal md:h-full">
        <div class="relative p-4 w-full max-w-xl h-full md:h-auto">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow ">
                <!-- Modal header -->
                <div class="flex justify-center items-start p-5 rounded-t ">
                    <h3 class="text-xl font-bold  lg:text-2xl text-dark-green">
                        Edit Nilai
                    </h3>
                </div>
                <!-- Modal body -->
                <div class="px-6 space-y-6">
                    <form class="flex flex-col gap-y-4" id="scoreform" action="" method="POST">
                        <div class="mb-6">
                            <input type="hidden" id="sid" name="sid">
                            <input type="hidden" id="studentId" name="studentId">
                            <input type="number" id="score" name="score" min="0" max="100" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 ">
                            <li class="font-semibold text-dark-green text-xs mt-2">Masukan nilai dari 0-100</li>
                        </div>
                        <div class="flex justify-end p-6 space-x-3 rounded-b ">
                            <button data-modal-toggle="defaultModal" class="w-24" type="button">Batal</button>
                            <button class="bg-cream text-white  font-semibold justify-end text-center py-2 rounded-lg w-24 ml-auto" type="submit" name="submit">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- END MODAL -->

    <!-- modal UNFINISHED ASSIGNMENT -->
    <div id="defaultModal1" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 w-full md:inset-0 h-modal md:h-full">
        <div class="relative p-4 w-full max-w-xl h-full md:h-auto">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow ">
                <!-- Modal header -->
                <div class="flex justify-center items-start p-5 rounded-t ">
                    <h3 class="text-xl font-bold  lg:text-2xl text-dark-green">
                        Edit Nilai
                    </h3>
                </div>
                <!-- Modal body -->
                <div class="px-6 space-y-6">
                    <form class="flex flex-col gap-y-4" id="scoreform1" action="" method="POST">
                        <div class="mb-6">
                            <input type="hidden" id="sid1" name="sid1">
                            <input type="hidden" id="studentId1" name="studentId1">
                            <input type="hidden" id="studentId1" name="studentId1">
                            <input type="hidden" id="mentorid" name="mentorid" value="<?= $_SESSION['user_data']->{'user'}->{'user_id'} ?>">
                            <input type="number" id="score1" min="0" max="100" name="score1" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 ">
                            <li class="font-semibold text-dark-green text-xs mt-2">Masukan nilai dari 0-100</li>
                        </div>
                        <div class="flex justify-end p-6 space-x-3 rounded-b ">
                            <button id="btnBatal" class="w-24" type="button">Batal</button>
                            <button class="bg-cream text-white  font-semibold justify-end text-center py-2 rounded-lg w-24 ml-auto" type="submit" name="submit1">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- END MODAL -->



    <script src="https://unpkg.com/flowbite@1.4.1/dist/flowbite.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.js"></script>

    <script>
        let btnToggle = document.getElementById('btnToggle');
        let btnToggle2 = document.getElementById('btnToggle2');
        let sidebar = document.querySelector('.sidebar');
        let leftNav = document.getElementById("left-nav");
        let targetModal = document.getElementById('defaultModal1');
        let btnBatal = document.getElementById('btnBatal')
        const options = {
            placement: 'center',
            backdropClasses: 'bg-gray-900 bg-opacity-50 dark:bg-opacity-80 fixed inset-0 z-40',
            onHide: () => {
                console.log('modal is hidden');
            },
            onShow: () => {
                console.log('modal is shown');
            },
            onToggle: () => {
                console.log('modal has been toggled');
            }
        };
        let modal = new Modal(targetModal, options);

        btnToggle.onclick = function() {
            sidebar.classList.toggle('in-active');
        }

        btnToggle2.onclick = function() {
            leftNav.classList.toggle('hidden');
        }



        // Bug on click mobile navbar
        // leftNav.onclick = function() {
        //     leftNav.classList.toggle('hidden');
        // }

        $(document).ready(function() {
            $('#btnHelp').click(function() {
                introJs().setOptions({
                    steps: [{
                            intro: "Hello Selamat Datang Di Halaman Assignment Collection"
                        }, {
                            element: document.querySelector('.topic-title'),
                            intro: "Ini merupakan halaman assignment collection dimana mentor akan memberikan score terhadap assignment yang di kumpulkan oleh students"
                        }, {
                            element: document.querySelector('#table-finished'),
                            intro: "Ini merupakan tabel students yang telah mengumpulkan assignments"
                        },
                        {
                            element: document.querySelector('#table-unfinished'),
                            intro: "Ini merupakan tabel students yang telah belum/tidak mengumpulkan assignments"
                        },
                        {
                            element: document.querySelector('.filename'),
                            intro: "Untuk mendownload file assignment students click pada filename assignments collection"
                        }, {
                            element: document.querySelector('#editbtn'),
                            intro: "Ini adalah tombol action untuk memberikan score pada Assignments Students"
                        }, {
                            title: 'Modal Add Score Assignment',
                            intro: '<img src="../../Img/assets/modal_score.png" onerror="this.onerror=null;this.src=\'https://i.giphy.com/ujUdrdpX7Ok5W.gif\';" alt="" data-position="top">'
                        }, {
                            element: document.querySelector('.score'),
                            intro: "Setelah mentor memberi score, score nanti akan tampil seperti ini"
                        }

                    ]
                }).start();
            })

            $('#editbtn1').click(function() {
                if (confirm("Apakah yakin akan mengedit score")) {
                    modal.show()
                }
            })

            $('#btnBatal').click(function() {
                modal.hide()
            })


            $(document).on('click', '#editbtn', function() {
                let username = $(this).data('username');
                let scoreID = $(this).data('scoreid');
                // let studentId = $(this).data('student-id');
                let scoreValue = $(this).data('scorevalue');
                $('#sid').val(scoreID);
                $('#studentId').val(studentId);
                // console.log(userID);

                $('#score').attr('placeholder', 'Input score for ' + username);
            })
            $(document).on('click', '#editbtn1', function() {
                let username = $(this).data('username');
                let scoreID = $(this).data('scoreid');
                // let studentId = $(this).data('student-id');
                let scoreValue = $(this).data('scorevalue');
                $('#sid1').val(scoreID);
                $('#studentId1').val(studentId);
                // console.log(userID);

                $('#score1').attr('placeholder', 'Input score for ' + username);
            })
            $('#scoreform').validate({
                errorClass: "error fail-alert",
                validClass: "valid success-alert",
                rules: {
                    score: {
                        number: true,
                        min: 0,
                        max: 100,
                    }
                },
                messages: {
                    score: {
                        number: 'Score must be number',
                        min: 'Min score is 0 ',
                        max: 'max score is 100'
                    }
                }
            })
            $('#scoreform1').validate({
                errorClass: "error fail-alert",
                validClass: "valid success-alert",
                rules: {
                    score: {
                        number: true,
                        min: 0,
                        max: 100,
                    }
                },
                messages: {
                    score: {
                        number: 'Score must be number',
                        min: 'Min score is 0 ',
                        max: 'max score is 100'
                    }
                }
            })

        })
    </script>


</body>

</html>