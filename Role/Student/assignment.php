<?php
// MEMULAI SESSION

session_start();
// KEAMANAN HALAMAN BACK-END IDENTIFIKASI USER [START]
// PENGECEKAN USER APAKAH SUDAH LOGIN DENGAN AKUN YANG SESUAI ATAU BELUM SEBELUM MEMASUKI HALAMAN INI

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
        // KONDISI KETIKA USER MEMASUKI HALAMAN NAMUN LOGIN SEBAGAI ADMIN

    case 1:
        echo "
        <script>
            alert('Akses ditolak!');
            location.replace('../Admin/');
        </script>
        ";
        break;
        // KONDISI KETIKA USER MEMASUKI HALAMAN NAMUN LOGIN SEBAGAI MENTOR

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
// KEAMANAN HALAMAN BACK-END IDENTIFIKASI USER [FINISH]

// MEMANGGIL FUNGSI DAN API
require_once('../../Model/Assignments.php');
// MENGAMBIL ASSIGNMENT SESUAI DENGAN TOPIK DAN SUB-TOPIK TERTENTU

$objAssign = new Assignments;

// $allAssignments = $objAssign->getAssignmentBySubjectId($_GET['subject_id']);
$allAssignments = $objAssign->getAssignmentBySubjectIdForStudent($_GET['subject_id']);
// var_dump($allAssignments);

require "../../api/get_api_data.php";
require_once "../../api/get_request.php";

// MENYIMPAN DATA KEDALAM ARRAY

// $subModulData = json_decode(http_request("https://lessons.lumintulogic.com/api/modul/read_modul_rows.php"));
$lectureData = array();
$modulJSON = json_decode(http_request("https://lessons.lumintulogic.com/api/modul/read_modul_rows.php"));
// $userBatchJSON = json_decode(http_request("https://i0ifhnk0.directus.app/items/user_batch"));
// $userJSON = json_decode(http_request("https://i0ifhnk0.directus.app/items/user"));
$token = $_COOKIE['X-LUMINTU-REFRESHTOKEN'];
$usersData = json_decode(http_request_with_auth("https://account.lumintulogic.com/api/users.php", $token));

$batchId = $usersData->{'user'}[0]->{'batch_id'};

for ($i = 0; $i < count($usersData->{'user'}); $i++) {
    if ($usersData->{'user'}[$i]->{'role_id'} == 2) {
        array_push($lectureData, $usersData->{'user'}[$i]);
    }
}

$subModul = array();

for ($i = 0; $i < count($modulJSON->{'data'}); $i++) {
    if ($modulJSON->{'data'}[$i]->{'id'} == $_GET['subject_id']) {
        array_push($subModul, $modulJSON->{'data'}[$i]);
    }
}

echo "<input type='hidden' id='student_id' value='" . $_SESSION['user_data']->{'user'}->{'user_id'} . "'/>";
// var_dump($_SESSION['user_data']->{'user'}->{'user_id'});
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lumintu Logic</title>

    <!-- Favicon -->
    <link rel="icon" href="../../Img/logo/logo_lumintu1.ico" type="image/x-icon">
    <link rel="shortcut icon" href="../../Img/logo/logo_lumintu1.ico" type="image/x-icon" />

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
    <!-- <link rel="stylesheet" href="https://unpkg.com/flowbite@1.4.1/dist/flowbite.min.css" /> -->
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
        .sidebar #username_logo {
            display: none;
        }

        /* #profil_image {
            display: none !important;
        } */

        /* .responsive-top {
            display: none;
        } */

        .active {
            color: #DDB07F !important;
            border-bottom: solid 4px #DDB07F;
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
    <!-- LIST MENU SIDEBAR [START]-->
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
                        <li>
                            <a href="https://account.lumintulogic.com/home.php" class="flex items-center gap-x-4 h-[50px] rounded-xl px-4 hover:bg-cream text-dark-green hover:text-white">
                                <img class="w-5" src="../../Img/icons/home_icon.svg" alt="Dashboard Icon">
                                <p class="font-semibold">Beranda</p>
                            </a>
                        </li>
                        <!-- <li>
                                <a href="#" class="flex items-center gap-x-4 h-[50px] rounded-xl px-4 bg-cream">
                                    <img class="w-5" src="../../Img/icons/course_icon.svg" alt="Course Icon">
                                    <p class="text-white font-semibold">Courses</p>
                                </a>
                            </li> -->
                        <!-- Icon Assignment -->
                        <li>
                            <a href="./index.php" class="flex items-center gap-x-4 h-[50px] rounded-xl px-4 bg-cream">
                                <img class="w-5" src="../../Img/icons/assignment_white_icon.svg" alt="Assignment Icon">
                                <p class="text-white font-semibold">Penugasan</p>
                            </a>
                        </li>
                        <li>
                            <a href="score.php" class="flex items-center gap-x-4 h-[50px] rounded-xl px-4 hover:bg-cream text-dark-green hover:text-white">
                                <img class="w-5" src="../../Img/icons/score_icon.svg" alt="Score Icon">
                                <p class="font-semibold">Nilai</p>
                            </a>
                        </li>
                        <!-- <li>
                                <a href="" class="flex items-center gap-x-4 h-[50px] rounded-xl px-4 hover:bg-cream text-dark-green hover:text-white">
                                    <img class="w-5" src="../../Img/icons/discussion_icon.svg" alt="Forum Icon">
                                    <p class="font-semibold">Forum Dicussion</p>
                                </a>
                            </li> -->
                        <li>
                            <a href="https://consultation.lumintulogic.com/auth.php?token=<?= $_COOKIE['X-LUMINTU-REFRESHTOKEN']; ?>&expiry=<?= $_SESSION['expiry']; ?>" class="flex items-center gap-x-4 h-[50px] rounded-xl px-4 hover:bg-cream text-dark-green hover:text-white">
                                <img class="w-5" src="../../Img/icons/consult_icon.svg" alt="Consult Icon">
                                <p class="font-semibold">Konsultasi</p>
                            </a>
                        </li>
                        <li>
                            <a href="https://schedule.lumintulogic.com/auth.php?token=<?php echo ($_COOKIE['X-LUMINTU-REFRESHTOKEN']); ?>&expiry=<?php echo $_SESSION["expiry"]; ?>" class="flex items-center gap-x-4 h-[50px] rounded-xl px-4 hover:bg-cream text-dark-green hover:text-white">
                                <img class="w-5" src="../../Img/icons/schedule_icon.svg" alt="Schedule Icon">
                                <p class="font-semibold">Jadwal</p>
                            </a>
                        </li>
                        <!-- <li>
                                <a href="" class="flex items-center gap-x-4 h-[50px] rounded-xl px-4 hover:bg-cream text-dark-green hover:text-white">
                                    <img class="w-5" src="../../Img/icons/attendance_icon.svg" alt="Attendance Icon">
                                    <p class="font-semibold">Attendance</p>
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

        <!-- Mobile navbar -->
        <div id="left-nav" class="bg-opacity-50 bg-gray-500 fixed top-[130px] bottom-0 overflow-y-scroll inset-x-0 hidden z-10 transition-all ease-in-out duration-500 sm:hidden">

            <div class="bg-white w-[250px] h-screen px-6 py-6 ">
                <!-- Top nav -->
                <div class="flex flex-col gap-y-6">

                    <!-- List Menus -->
                    <ul class="flex flex-col gap-y-1">
                        <li>
                            <a href="" class="flex items-center gap-x-4 h-[50px] rounded-xl px-4 hover:bg-cream text-dark-green hover:text-white" id="profil_image">
                                <img class="w-5" src="../../Img/icons/default_profile.svg" alt="Profile Image">
                                <p class="font-semibold"><?= $_SESSION['user_data']->{'user'}->{'user_first_name'} . " " . $_SESSION['user_data']->{'user'}->{'user_last_name'} ?></p>
                                <!-- <p class="font-semibold"></p> -->
                            </a>
                            <!-- ICON DAN TEXT DASHBOARD -->
                        </li>
                        <li>
                            <a href="https://account.lumintulogic.com/home.php" class="flex items-center gap-x-4 h-[50px] rounded-xl px-4 hover:bg-cream text-dark-green hover:text-white">
                                <img class="w-5" src="../../Img/icons/home_icon.svg" alt="Dashboard Icon">
                                <p class="font-semibold">Beranda</p>
                            </a>
                        </li>
                        <!-- <li>
                            <a href="#" class="flex items-center gap-x-4 h-[50px] rounded-xl px-4 bg-cream">
                                <img class="w-5" src="../../Img/icons/course_icon.svg" alt="Course Icon">
                                <p class="text-white font-semibold">Courses</p>
                            </a>
                        </li> -->
                        <!-- Icon Assignment -->
                        <li>
                            <a href="./index.php" class="flex items-center gap-x-4 h-[50px] rounded-xl px-4 bg-cream">
                                <img class="w-5" src="../../Img/icons/assignment_white_icon.svg" alt="Assignment Icon">
                                <p class="text-white font-semibold">Penugasan</p>
                            </a>
                        </li>
                        <li>
                            <a href="score.php" class="flex items-center gap-x-4 h-[50px] rounded-xl px-4 hover:bg-cream text-dark-green hover:text-white">
                                <img class="w-5" src="../../Img/icons/score_icon.svg" alt="Score Icon">
                                <p class="font-semibold">Nilai</p>
                            </a>
                        </li>
                        <li>
                            <a href="https://consultation.lumintulogic.com/auth.php?token=<?= $_COOKIE['X-LUMINTU-REFRESHTOKEN']; ?>&expiry=<?= $_SESSION['expiry']; ?>" class="flex items-center gap-x-4 h-[50px] rounded-xl px-4 hover:bg-cream text-dark-green hover:text-white">
                                <img class="w-5" src="../../Img/icons/consult_icon.svg" alt="Consult Icon">
                                <p class="font-semibold">Konsultasi</p>
                            </a>
                        </li>
                        <li>
                            <a href="https://schedule.lumintulogic.com/auth.php?token=<?php echo ($_COOKIE['X-LUMINTU-REFRESHTOKEN']); ?>&expiry=<?php echo $_SESSION["expiry"]; ?>" class="flex items-center gap-x-4 h-[50px] rounded-xl px-4 hover:bg-cream text-dark-green hover:text-white">
                                <img class="w-5" src="../../Img/icons/schedule_icon.svg" alt="Schedule Icon">
                                <p class="font-semibold">Jadwal</p>
                            </a>
                        </li>
                        <!-- <li>
                            <a href="" class="flex items-center gap-x-4 h-[50px] rounded-xl px-4 hover:bg-cream text-dark-green hover:text-white">
                                <img class="w-5" src="../../Img/icons/discussion_icon.svg" alt="Forum Icon">
                                <p class="font-semibold">Forum Dicussion</p>
                            </a>
                        </li> -->

                        <!-- <li>
                            <a href="" class="flex items-center gap-x-4 h-[50px] rounded-xl px-4 hover:bg-cream text-dark-green hover:text-white">
                                <img class="w-5" src="../../Img/icons/attendance_icon.svg" alt="Attendance Icon">
                                <p class="font-semibold">Attendance</p>
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


        <!-- Right side -->
        <div class="bg-cgray w-full h-screen px-10 py-6 flex flex-col gap-y-6 overflow-y-scroll rightbar">
            <!-- Header / Profile -->
            <div class="items-center gap-x-4 justify-end hidden sm:flex" id="profil_image2">
                <img class="w-10" src="../../Img/icons/default_profile.svg" alt="Profile Image">
                <p class="text-dark-green font-semibold"><?= $_SESSION['user_data']->{'user'}->{'user_first_name'} . " " . $_SESSION['user_data']->{'user'}->{'user_last_name'} ?></p>
            </div>

            <!-- Breadcrumb -->
            <div class="p-2 lg:p-4">
                <ul class="flex items-center gap-x-4 text-xs lg:text-base">
                    <li class="flex items-center space-x-2">
                        <a class="text-light-green hover:text-dark-green hover:font-semibold" href="#">Beranda</a>
                    </li>
                    <li>
                        <span class="text-light-green">/</span>
                    </li>
                    <li class="flex items-center space-x-2">
                        <a class="text-light-green hover:text-dark-green hover:font-semibold" href="index.php">Batch</a>
                    </li>
                    <li>
                        <span class="text-light-green">/</span>
                    </li>
                    <li class="flex items-center space-x-2">
                        <a class="text-light-green" href="subject.php?course_id=<?= $_GET['course_id']; ?>">Materi</a>
                    </li>
                    <li>
                        <span class="text-light-green">/</span>
                    </li>
                    <li>
                        <a class="text-dark-green font-semibold" href="#">Penugasan</a>
                    </li>
                </ul>
            </div>

            <!-- Topic Title -->
            <div class="p-2 lg:p-4 topic-title">
                <p class="text-sm sm:text-lg lg:text-2xl xl:text-4xl text-dark-green font-semibold">Pertemuan#1 <?= $subModul[0]->{'modul_name'}; ?></p>
            </div>

            <!-- Mentor -->
            <div class="p-2 lg:p-4 flex items-center gap-x-4 w-full bg-white py-4 px-5 lg:px-10 rounded-xl mentor-profile">
                <img class="w-8 lg:w-14" src="../../Img/icons/default_profile.svg" alt="Profile Image">
                <div class="">
                    <p class="text-dark-green text-sm lg:text-base font-semibold"><?= $lectureData[0]->{'user_first_name'} . " " . $lectureData[0]->{'user_last_name'} ?></p>
                    <!-- <p class="text-light-green">Mentor Specialization</p> -->
                </div>
            </div>

            <!-- Tab -->
            <div class="bg-white w-full h-[50px] flex content-center px-10 tab-menu">
                <ul class="flex items-center gap-x-8 text-sm lg:text-base">
                    <li class="text-dark-green hover:text-cream hover:border-b-4 hover:border-cream h-[50px] flex items-center font-semibold  cursor-pointer active">
                        <p>Penugasan</p>
                    </li>
                </ul>
            </div>

            <!-- Direction -->
            <div class="bg-white w-full p-6 direction course-title">
                <p class="text-dark-green font-semibold text-sm lg:text-base">Deskripsi :</p>
                <p class="text-sm lg:text-base"><?= htmlspecialchars_decode($subModul[0]->{'modul_description'}); ?></p>
            </div>

            <!-- Table Assignment -->
            <div class="relative ">
                <table class="shadow-lg bg-white" style="width: 100%">
                    <colgroup>
                        <col span="1" style="width: 30%">
                        <col span="1" style="width: 10%">
                        <col span="1" style="width: 10%">
                        <col span="1" style="width: 15%">

                    </colgroup>
                    <thead class="thead">
                        <tr class="text-dark-green text-sm lg:text-base">
                            <th class="border-b text-left px-4 py-2">Judul</th>
                            <th class="border-b text-center px-4 py-2">Batas Tanggal</th>
                            <th class="border-b text-center px-4 py-2">Batas Waktu</th>
                            <th class="border-b text-center px-4 py-2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($allAssignments as $row => $assignment) : ?>
                            <?php
                            $arrStartDate = explode(" ", $assignment['assignment_start_date']);
                            $arrEndDate = explode(" ", $assignment['assignment_end_date']);

                            // var_dump($arrEndDate);
                            $startDate = $arrStartDate[0];
                            $dueDate = $arrEndDate[0];
                            $dueTime = date("H:i", strtotime($arrEndDate[1]));
                            ?>
                            <tr class="text-sm lg:text-base">
                                <td class="border-b px-4 py-2 ">
                                    <div class="flex items-center gap-x-2">
                                        <p class="truncate max-w-[300px]" data-tooltip-target="tooltipassignment<?= $assignment['assignment_id'] ?>"><?= $assignment['assignment_name']; ?></p>
                                        <a href="#">
                                            <img class="Desc w-3 sm:w-5 cursor-pointer" data-tooltip-target="tooltipDesc" src="../../Img/icons/detail_icon.svg" alt="Download Icon" type="button" data-modal-toggle="medium-modal<?= "medium-modal" . $assignment['assignment_id'] ?>" id="showDesc" data-desc="<?= $assignment['assignment_desc'] ?>">
                                        </a>
                                    </div>
                                    <div id="tooltipassignment<?= $assignment['assignment_id'] ?>" role="tooltip" class="inline-block absolute invisible z-10 py-2 px-3 text-sm font-medium text-gray-700 bg-gray-300 rounded-lg shadow-sm opacity-0 transition-opacity duration-300 tooltip dark-bg-cream ">
                                        <?= $assignment['assignment_name']; ?>
                                        <div class="tooltip-arrow" data-popper-arrow></div>
                                    </div>

                                    <div id="tooltipDesc" role="tooltip" class="inline-block absolute invisible z-10 py-2 px-3 text-sm font-medium text-white bg-gray-600 rounded-lg shadow-sm opacity-0 transition-opacity duration-300 tooltip dark-bg-cream ">
                                        Deskripsi Tugas
                                        <div class="tooltip-arrow" data-popper-arrow></div>
                                    </div>
                                </td>
                                <td class="border-b px-4 py-2 text-center"><?= $dueDate; ?></td>
                                <td class="border-b px-4 py-2 text-center"><?= $dueTime . " WIB"; ?></td>
                                <td class="border-b px-4 py-2 flex flex-wrap items-center justify-center gap-x-2 ">

                                    <?php
                                    require_once('../../Model/AssignmentQuestion.php');
                                    $asq = new AssignmentQuestion;
                                    $asq->setAssignmentId($assignment['assignment_id']);
                                    $question = $asq->getQuestionsByAssignmentId();
                                    ?>
                                    <a href="download.php?file=<?= $question['question_filename'] . '&type=q'; ?>">
                                        <img class="Download w-5 sm:w-7 cursor-pointer" data-tooltip-target="tooltipDownload" src="../../Img/icons/download_icon.svg" alt="Download Icon">
                                    </a>
                                    <div id="tooltipDownload" role="tooltip" class="inline-block absolute invisible z-10 py-2 px-3 text-sm font-medium text-gray-700 bg-gray-300 rounded-lg shadow-sm opacity-0 transition-opacity duration-300 tooltip dark-bg-cream ">
                                        Unduh Soal
                                        <div class="tooltip-arrow" data-popper-arrow></div>
                                    </div>
                                    <?php
                                    require_once "../../Model/AssignmentSubmission.php";
                                    $objsubmit = new AssignmentSubmission;
                                    $objsubmit->setStudentId($_SESSION['user_data']->{'user'}->{'user_id'});
                                    $objsubmit->setAssignmentId($assignment['assignment_id']);
                                    $initsubmit = $objsubmit->getInitSubmit();
                                    $now = $objsubmit->getCurrentDate();
                                    $msg = '';
                                    $csub = $objsubmit->getSubmissionByAssignIdAndStudentIdGroupBy();
                                    // echo ($_SESSION['user']->{'user_id'});
                                    // var_dump($initsubmit);
                                    if (count($initsubmit) == 1) {
                                        // print('Init Submit');
                                        if ((strtotime($now['now()']) > strtotime($assignment['assignment_end_date']))) { ?>
                                            <!-- print('Melebihi deadline'); -->

                                            <img class="Upload w-5 sm:w-7 " data-tooltip-target="tooltip-default1" src="../../Img/icons/create_iconred.svg" alt="Create Icon" name="btnup" id="btnup">
                                            <div id="tooltip-default1" role="tooltip" class="inline-block absolute invisible z-10 py-2 px-3 text-sm font-medium text-white bg-red-900 rounded-lg shadow-sm opacity-0 transition-opacity duration-300 tooltip dark:bg-red-700">
                                                Tugas ini sudah melewati batas waktu pengumpulan
                                                <div class="tooltip-arrow" data-popper-arrow></div>
                                            </div>
                                        <?php } else { ?>
                                            <!-- print('Upload tugas pertama'); -->
                                            <img class="Upload w-5 sm:w-7 cursor-pointer modalUpload" data-tooltip-target="tooltipUpload" src="../../Img/icons/create_icon.svg" alt="Create Icon" type="button" data-modal-toggle="modalAdd" data-assignid="<?= $assignment["assignment_id"]; ?>" id="openModal">
                                            <div id="tooltipUpload" role="tooltip" class="inline-block absolute invisible z-10 py-2 px-3 text-sm font-medium text-gray-700 bg-gray-300 rounded-lg shadow-sm opacity-0 transition-opacity duration-300 tooltip dark-bg-cream ">
                                                Upload Tugas
                                                <div class="tooltip-arrow" data-popper-arrow></div>
                                            </div>
                                        <?php }
                                    } else {
                                        if ((strtotime($now['now()']) > strtotime($assignment['assignment_end_date']))) { ?>
                                            <!-- print('Melebihi deadline'); -->

                                            <img class="Upload w-5 sm:w-7 " data-tooltip-target="tooltip-default1" src="../../Img/icons/create_iconred.svg" alt="Create Icon" name="btnup" id="btnup">
                                            <div id="tooltip-default1" role="tooltip" class="inline-block absolute invisible z-10 py-2 px-3 text-sm font-medium text-white bg-red-900 rounded-lg shadow-sm opacity-0 transition-opacity duration-300 tooltip dark:bg-red-700">
                                                Tugas ini sudah melewati batas waktu pengumpulan
                                                <div class="tooltip-arrow" data-popper-arrow></div>
                                            </div>
                                        <?php } else if (count($csub) >= 1 and count($csub) < 3) { ?>
                                            <!-- print('sudah mengumpulkan'); -->
                                            <img class="Upload w-5 sm:w-7 cursor-pointer modalUpload" data-tooltip-target="tooltip-default" src="../../Img/icons/create_icon.svg" alt="Create Icon" type="button" data-modal-toggle="modalAdd" data-assignid="<?= $assignment["assignment_id"]; ?>" id="openModal">
                                            <div id="tooltip-default" role="tooltip" class="inline-block absolute invisible z-10 py-2 px-3 text-sm font-medium text-gray-700 bg-gray-300 rounded-lg shadow-sm opacity-0 transition-opacity duration-300 tooltip ">
                                                Sudah Mengumpulkan Tugas
                                                <div class="tooltip-arrow" data-popper-arrow></div>
                                            </div>
                                        <?php } else if (count($csub) >= 3) { ?>
                                            <!-- print('Sudah melebihi batas'); -->
                                            <img class="Upload w-5 sm:w-7 " data-tooltip-target="tooltip-default1" src="../../Img/icons/create_iconred.svg" alt="Create Icon" name="btnup" id="btnup">
                                            <div id="tooltip-default1" role="tooltip" class="inline-block absolute invisible z-10 py-2 px-3 text-sm font-medium text-white bg-red-900 rounded-lg shadow-sm opacity-0 transition-opacity duration-300 tooltip dark:bg-red-700">
                                                Tugas ini sudah melewati batas jumlah pengumpulan
                                                <div class="tooltip-arrow" data-popper-arrow></div>
                                            </div>
                                        <?php } else if (count($csub) >= 1 and count($csub) < 3 and (strtotime($now['now()']) > strtotime($assignment['assignment_end_date']))) { ?>
                                            <!-- print('Mengumpulkan dan melebihi deadline'); -->
                                            <img class="Upload w-5 sm:w-7 " data-tooltip-target="tooltip-default1" src="../../Img/icons/create_iconred.svg" alt="Create Icon" name="btnup" id="btnup">
                                            <div id="tooltip-default1" role="tooltip" class="inline-block absolute invisible z-10 py-2 px-3 text-sm font-medium text-white bg-red-900 rounded-lg shadow-sm opacity-0 transition-opacity duration-300 tooltip dark:bg-red-700">
                                                Sudah mengumpulkan namun sekarang sudah melewati batas waktu pengumpulan
                                                <div class="tooltip-arrow" data-popper-arrow></div>
                                            </div>
                                        <?php } else { ?>
                                            <img class="Upload w-5 sm:w-7 cursor-pointer modalUpload" data-tooltip-target="tooltipUpload" src="../../Img/icons/create_icon.svg" alt="Create Icon" type="button" data-modal-toggle="modalAdd" data-assignid="<?= $assignment["assignment_id"]; ?>" id="openModal">
                                            <div id="tooltipUpload" role="tooltip" class="inline-block absolute invisible z-10 py-2 px-3 text-sm font-medium text-gray-700 bg-gray-300 rounded-lg shadow-sm opacity-0 transition-opacity duration-300 tooltip dark-bg-cream ">
                                                Upload Tugas
                                                <div class="tooltip-arrow" data-popper-arrow></div>
                                            </div>
                                    <?php }
                                    } ?>

                                    <img class="History w-5 sm:w-7 cursor-pointer" data-tooltip-target="tooltipHistory" src="../../Img/icons/history_icon.svg" alt="History Icon" type="button" data-modal-toggle="historymodal<?= $assignment['assignment_id']; ?>">
                                    <div id="tooltipHistory" role="tooltip" class="inline-block absolute invisible z-10 py-2 px-3 text-sm font-medium text-gray-700 bg-gray-300 rounded-lg shadow-sm opacity-0 transition-opacity duration-300 tooltip dark-bg-cream ">
                                        Lihat Riwayat Upload
                                        <div class="tooltip-arrow" data-popper-arrow></div>
                                    </div>
                                </td>
                            </tr>
                            <!-- modal ASSIGNMENT HISTORY -->
                            <div id="historymodal<?= $assignment['assignment_id']; ?>" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 w-full md:inset-0 h-modal md:h-full">
                                <div class="relative p-4 w-full max-w-xl h-full md:h-auto">
                                    <!-- Modal content -->
                                    <div class="relative bg-white rounded-lg shadow ">
                                        <!-- Modal header -->
                                        <div class="flex justify-center items-start p-5 rounded-t ">
                                            <h3 class="text-xl font-bold  lg:text-2xl text-dark-green">
                                                RIWAYAT UPLOAD
                                            </h3>
                                        </div>
                                        <!-- Modal body -->
                                        <div class="px-6 space-y-6">
                                            <div class="mb-6 relative overflow-x-auto sm:rounded-lg border border-gray-400 px-4 py-2">
                                                <div class="relative w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                                    <ul class="grid grid-cols-12 border-b border-gray-400 py-2">
                                                        <li><b>No</b></li>
                                                        <li class="col-span-5"><b>Waktu Pengumpulan</b></li>
                                                        <li class="col-span-5"><b>Judul</b></li>
                                                        <li><b>Files</b></li>
                                                    </ul>
                                                    <?php
                                                    require_once "../../Model/AssignmentSubmission.php";
                                                    $objSub = new AssignmentSubmission;
                                                    $objSub->setStudentId($_SESSION['user_data']->{'user'}->{'user_id'});
                                                    $objSub->setAssignmentId($assignment['assignment_id']);
                                                    $submissions = $objSub->getSubmissionByAssignIdAndStudentId();

                                                    $i = 1;

                                                    foreach ($submissions as $val => $submission) :
                                                    ?>
                                                        <ul class="grid grid-cols-12 border-b border-gray-400 py-2">
                                                            <li><?= $i; ?></li>
                                                            <li class="col-span-5"><?= $submission['submitted_date']; ?></li>
                                                            <li class="col-span-5 truncate"><?= $submission['submission_filename']; ?></li>
                                                            <li><a href="download.php?file=<?= $submission['submission_filename'] . '&type=s'; ?>"><img class=" w-7 mx-auto cursor-pointer" src="../../Img/icons/download_icon.svg" alt="Download Icon"></a></li>
                                                        </ul>

                                                    <?php
                                                        $i++;
                                                    endforeach
                                                    ?>

                                                </div>
                                            </div>
                                            <div class="flex justify-end p-6 space-x-3 rounded-b ">
                                                <button data-modal-toggle="historymodal<?= $assignment['assignment_id']; ?>" class="w-32 bg-cream text-center py-1 text-white rounded-md hover:bg-gray-600" type="button">Tutup</button>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- END MODAL -->
                            <!-- Description Modal -->
                            <div id="medium-modal<?= "medium-modal" . $assignment['assignment_id'] ?>" tabindex="-1" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 w-full md:inset-0 h-modal md:h-full">
                                <div class="relative p-4 w-full max-w-lg h-full md:h-auto">
                                    <!-- Modal content -->
                                    <div class="relative bg-white rounded-lg shadow ">
                                        <!-- Modal header -->
                                        <div class="flex justify-between items-center p-5 rounded-t border-b dark:border-gray-600">
                                            <h3 class="text-xl font-medium text-center">
                                                Deskripsi
                                            </h3>
                                            <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-toggle="medium-modal<?= "medium-modal" . $assignment['assignment_id'] ?>">
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                                </svg>
                                            </button>
                                        </div>
                                        <!-- Modal body -->
                                        <div class="p-6 space-y-6">
                                            <p class="text-base leading-relaxed">
                                                <?= $assignment['assignment_desc'] ?>
                                            </p>
                                        </div>
                                        <!-- Modal footer -->
                                        <div class="flex justify-end p-6 space-x-2 rounded-b border-gray-200 dark:border-gray-600">
                                            <button data-modal-toggle="medium-modal<?= "medium-modal" . $assignment['assignment_id'] ?>" type="button" class="text-gray bg-cream focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-transparent hover:text-white hover:bg-gray-600 dark:focus:ring-dark-800">Tutup</button>
                                            <!-- <button data-modal-toggle="medium-modal" type="button" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">Decline</button> -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- END MODAL -->
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
            <!-- Main modal -->
            <div id="modalAdd" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 w-full md:inset-0 h-modal md:h-full">
                <div class="relative w-full max-w-sm max-h-sm h-full lg:h-auto items-center">
                    <!-- Modal content -->
                    <div class="relative  bg-white rounded-lg shadow ">
                        <!-- Modal header -->
                        <div class="flex justify-center items-start p-5 rounded-t ">
                            <h3 class="text-xl font-semibold text-gray-900 lg:text-2xl dark:text-dark">
                                Upload Tugas
                            </h3>
                        </div>
                        <!-- Modal body -->
                        <div class="px-6 space-y-6" id="modalbdy">
                            <form class="flex flex-col" action="" method="POST" enctype="multipart/form-data">
                                <div class="mt-1 flex justify-center px-3 pt-4 pb-5 border-2 border-gray-300 rounded-md gap-y-4 lg:py-[30px]">
                                    <div class="space-y-2 text-center">
                                        
                                        <!-- UPLOAD ICON -->
                                        <svg xmlns="http://www.w3.org/2000/svg" id="downloadIcon" class="mx-auto h-20 w-20 text-cream" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                        </svg>
                                        <!-- SELECTED ICON -->
                                        <svg xmlns="http://www.w3.org/2000/svg" id="prevDoc" class="mx-auto h-20 w-20 hidden" viewBox="0 0 20 20" fill="#DDB07F">
                                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" />
                                        </svg>
                                        <!-- TEXT ICON -->
                                        <p class="text-gray-600" id="countFile"></p>
                                        
                                        <div class="flex text-lg text-gray-600">
                                            <label for="fileInput" class="relative cursor-pointer bg-white rounded-md font-medium hover:text-gray-500 flex justify-items-center mx-auto">
                                                <span class="font-semibold text-cream border-2 border-cream border-opacity-60 py-1 px-5 rounded-lg hover:shadow-md hover:font-bold hover:bg-opacity-60">Pilih file</span>
                                                <input id="fileInput" name="fileInput" type="file" class="sr-only dropzone" data-assid="" onchange="readFile(event)" multiple>
                                                <input type="hidden" name="assignId" id="inputasignid">
                                                <input type="hidden" name="cf" id="cf">
                                            </label>
                                            <!-- <p class="pl-1">atau seret kesini</p> -->
                                        </div>
                                    </div>
                                </div>
                                <!-- CRITERIA FILE UPLOAD -->
                                <p class="text-sm text-gray-400 font-base">*Format File .png .jpg .jpeg .txt .pdf .doc .xls .ppt .docx .xlsx .pptx .zip .rar</p>
                                <p class="text-sm text-gray-400 font-base">*Maksimum File 2 MB</p>
                                <p class="text-sm text-gray-400 font-base">*Maksimum 3 Kali Upload</p>

                                <div class="flex justify-end p-6 space-x-2 rounded-b border-gray-200 dark:border-gray-600">
                                    <button data-modal-toggle="modalAdd" type="button" class="text-gray-500 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded text-sm px-5 py-2.5 text-center hover:ring-2 hover:ring-gray-400" id="closeModal">Tutup</button>
                                    <button class=" bg-cream text-white w-[120px] py-2 rounded font-medium ml-auto hover:bg-gray-600" type="submit" name="submit" id="uploadSubmission">Kirim</button>
                                    <button disabled type="button" id="loading" class="hidden text-white bg-cream rounded font-medium ml-auto py-2 px-2 items-center">
                                        <svg role="status" class="inline w-4 h-4 mr-3 text-white animate-spin" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="#E5E7EB" />
                                            <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentColor" />
                                        </svg>
                                        Uploading...
                                    </button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
            <!-- END MAIN MODAL -->
        </div>
    </div>


    <script src="https://unpkg.com/flowbite@1.4.1/dist/flowbite.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
    <script>
        let btnToggle = document.getElementById('btnToggle');
        let btnToggle2 = document.getElementById('btnToggle2');
        let sidebar = document.querySelector('.sidebar');
        let leftNav = document.getElementById('left-nav');
        // let listMenu = document.getElementById('dropdownMenu');
        // let listContainer = document.getElementById('dropdownRightStart');
        console.log("asd");

        btnToggle.onclick = function() {
            sidebar.classList.toggle('in-active');
            console.log("asd");
        }

        btnToggle2.onclick = function() {
            leftNav.classList.toggle('hidden');
        }

        // listMenu.onclick = () => {
        //     listContainer.classList.toggle("hidden");
        // }

        function readFile(e) {
            let documentPrev = document.getElementById("prevDoc");
            let downloadIcon = document.getElementById("downloadIcon");
            let file = document.getElementById("fileInput");
            let countFile = document.getElementById("countFile");
            let cf = document.getElementById("cf");
            cf.value = file.files.length;
            downloadIcon.classList.add("hidden");
            documentPrev.classList.remove("hidden");

            countFile.innerHTML = "Selected " + file.files.length;
        }
        $(document).ready(function() {

            $('#btnHelp').click(function() {
                introJs().setOptions({
                    steps: [{
                            intro: "Hello Selamat Datang Di Halaman Assignment Students"
                        }, {
                            element: document.querySelector('.topic-title'),
                            intro: "Ini merupakan halaman assignment dimana students akan melihat, dan mengupload assignments yang di berikan oleh mentor"
                        }, {
                            element: document.querySelector('.course-title'),
                            intro: "Ini adalah deksripsi dari course untuks students"
                        },
                        {
                            element: document.querySelector('.thead'),
                            intro: "Ini merupakan tabel dimana data assignment yang sudah di buat akan di tampilkan ke students"
                        }, {
                            element: document.querySelector('.Desc'),
                            intro: "Ini merupakan button untuk melihat deskripsi assignment"
                        }, {
                            element: document.querySelector('.Download'),
                            intro: "Ini merupakan button untuk mendownload soal dari assignment"
                        }, {
                            element: document.querySelector('.Upload'),
                            intro: "Ini merupakan button untuk melakukan multiple upload jawaban soal dari assignment yang di berikan ke students"
                        }, {
                            title: 'Modal Add Assignment Students',
                            intro: '<img src="../../Img/assets/modal_students.png" onerror="this.onerror=null;this.src=\'https://i.giphy.com/ujUdrdpX7Ok5W.gif\';" alt="" data-position="top">'
                        }, {
                            element: document.querySelector('.History'),
                            intro: "Ini merupakan button untuk melihat Assigment history yang telah di upload oleh students"
                        }

                    ]
                }).start();
            })

            $(document).on('click', '#openModal', function() {
                let asid = document.getElementById('inputasignid');
                let assigmentId = $(this).data('assignid');
                assignid = $(this).data('assignid');
                let inp = $(asid).val(assignid);
                console.log(assignid);
            })



            let studentId = document.getElementById("student_id");
            let student_id = studentId.value;


            let fileData = document.getElementById("fileInput");

            $(document).on("click", "#closeModal", function(evt) {
                evt.preventDefault();
                // fileData.value = '';
                let inputFile = $("#fileInput");
                // var $el = $('#infileid');
                inputFile.wrap('<form>').closest('form').get(0).reset();
                inputFile.unwrap();
                console.log(fileData);
                let documentPrev = document.getElementById("prevDoc");
                let downloadIcon = document.getElementById("downloadIcon");
                let file = document.getElementById("fileInput");
                let countFile = document.getElementById("countFile");
                let cf = document.getElementById("cf");
                cf.value = file.files.length;
                downloadIcon.classList.remove("hidden");
                documentPrev.classList.add("hidden");

                countFile.innerHTML = "";
            })

            function checkEkstension() {
                let reval;
                let validTypeFile = [
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
                    "application/x-zip-compressed",
                    "application/zip", //zip
                    "application/octet-stream", //zip
                    "multipart/x-zip", //zip
                    "application/x-rar", // rar
                    "application/x-rar-compressed", //rar
                    ""
                ];
                $.each(fileData.files, function(index, value) {
                    console.log(value.type);
                    if (jQuery.inArray(value.type, validTypeFile) == -1) {
                        console.log('EKSTENSI SALAH');
                        reval = false;
                        return false;
                    } else {
                        console.log('ekstensi benar');
                        reval = true;
                        return true;
                    }
                })
                return reval;
            }

            function checkSize() {
                let reval;
                $.each(fileData.files, function(index, value) {
                    // console.log(index, value);
                    if (value.size > 2097152) {
                        // console.log('File lebih besar dari 2mb');
                        reval = false;
                        return false;
                    } else {
                        // console.log('Sudah sesuai');
                        reval = true;
                        return true;
                    }
                })
                return reval;
            }
            $(document).on("click", "#uploadSubmission", function(evt) {
                // evt.preventDefault();
                // console.log('aaaa');
                if ($('#fileInput').val() != '') {
                    console.log('terisi');
                    let asid = document.getElementById('inputasignid');
                    let assignment_id = $(asid).val();
                    // if (checkEkstension() == false) {
                    //     alert('Ekstensi tidak sesuai !!');
                    //     evt.preventDefault();
                    // } else 
                    if (checkSize() == false) {
                        alert('file tidak boleh lebih dari 2mb');
                        evt.preventDefault();
                    } else {
                        $('#loading').removeClass('hidden');
                        $('#uploadSubmission').hide();
                        evt.preventDefault();
                        $('#closeModal').removeClass('hover:ring-2 hover:ring-gray-400');
                        $('#closeModal').attr("disabled", "disabled");
                        let cf = document.getElementById("cf");
                        let fileSize = [];
                        let fileType = [];
                        for (let i = 0; i < fileData.files.length; i++) {
                            fileSize.push(fileData.files[i]['size']);
                            fileType.push(fileData.files[i]['type']);
                        }
                        let cfile = cf.value;
                        let data = {
                            assigId: assignment_id,
                            studId: student_id,
                            count: cfile,
                            filetype: fileType,
                            filesize: fileSize,
                        }
                        // console.log(data);
                        // console.log(fileData.files);
                        let fd = new FormData();
                        let arrFile = [];

                        for (let i = 0; i < fileData.files.length; i++) {
                            arrFile.push(fileData.files[i]);
                            fd.append("files[]", fileData.files[i]);
                        }
                        fd.append("data", JSON.stringify(data));

                        insertSubmission(fileData, data, fd);
                    }
                } else {
                    evt.preventDefault();
                    alert('tidak boleh kosong');
                }
            })


            function insertSubmission(fileData, data, fd) {
                let countSuccess = 0;
                $.ajax({
                    url: "insert_submission.php",
                    type: "post",
                    // data: data,
                    data: fd,
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(data) {
                        console.log('HASIL AJAX INSERT SUBMIT');
                        console.log(data);
                        let dataJson = JSON.parse(data);
                        console.log(dataJson);
                        if (dataJson != null || dataJson != '') {
                            if (dataJson[0].is_ok == true) {
                                let formData = new FormData();
                                let arrFile = [];

                                for (let i = 0; i < fileData.files.length; i++) {
                                    arrFile.push(fileData.files[i]);
                                    formData.append("files[]", fileData.files[i]);
                                }

                                formData.append("data", JSON.stringify(dataJson));
                                let statUpdate = updateFileSubmission(formData);
                            } else {
                                alert(dataJson[0].msg);
                                $('#loading').addClass('hidden');
                                $('#uploadSubmission').show();
                                $('#closeModal').addClass('hover:ring-2 hover:ring-gray-400');
                                $('#closeModal').removeAttr("disabled", "");
                            }

                        } else {
                            alert('something wrong');
                            $('#loading').addClass('hidden');
                            $('#uploadSubmission').show();
                            $('#closeModal').addClass('hover:ring-2 hover:ring-gray-400');
                            $('#closeModal').removeAttr("disabled", "");
                        }
                    },
                    error: function(xhr) {
                        $('#loading').addClass('hidden');
                        $('#uploadSubmission').show();
                        $('#closeModal').addClass('hover:ring-2 hover:ring-gray-400');
                        $('#closeModal').removeAttr("disabled", "");
                        alert('Error! Check your connection!');
                    }
                })
            }

            function updateFileSubmission(formData) {
                $.ajax({
                    url: "upload_submission.php",
                    type: "post",
                    data: formData,
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(data) {
                        console.log(data);
                        let val = JSON.parse(data);
                        if (val.is_ok) {
                            alert(val.msg);
                            location.reload();
                        } else {
                            $('#loading').addClass('hidden');
                            $('#uploadSubmission').show();
                            $('#closeModal').addClass('hover:ring-2 hover:ring-gray-400');
                            $('#closeModal').removeAttr("disabled", "");
                            alert("Error! " + val.msg);
                            location.reload();
                        }
                    },
                    error: function(xhr) {
                        $('#loading').addClass('hidden');
                        $('#uploadSubmission').show();
                        $('#closeModal').addClass('hover:ring-2 hover:ring-gray-400');
                        $('#closeModal').removeAttr("disabled", "");
                        alert('Error! Check your connection!');
                    }
                })
            }

            function deleteSubmission(id) {
                $.ajax({
                    url: "delete_submission.php",
                    data: id,
                    type: post,
                    success: function(data) {
                        // success
                    }
                })
            }
        });
    </script>


</body>

</html>