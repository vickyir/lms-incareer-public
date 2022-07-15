<?php

session_start();

//$loginPath = "../../login.php";
$loginPath = "https://account.lumintulogic.com/login.php";

if (!isset($_SESSION['user_data'])) {
    header("location: " . $loginPath);
    die;
}

if (!isset($_COOKIE['X-LUMINTU-REFRESHTOKEN'])) {
    unset($_SESSION['user_data']);
    header("location: " . $loginPath);
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


require_once '../../api/get_api_data.php';
require_once '../../api/get_request.php';
$data = json_decode(http_request("https://lessons.lumintulogic.com/api/modul/read_modul_rows.php"));
$token = $_COOKIE['X-LUMINTU-REFRESHTOKEN'];
$usersData = json_decode(http_request_with_auth("https://account.lumintulogic.com/api/users.php", $token));
$batchJson = json_decode(http_request_with_auth("https://account.lumintulogic.com/api/batch.php", $token));
$courseData = array();
$batchData = array();
$allBatch = array();


for ($i = 0; $i < count($usersData->{'user'}); $i++) {
    if ($usersData->{'user'}[$i]->{'user_id'} == $_SESSION['user_data']->{'user'}->{'user_id'}) {
        array_push($batchData, $usersData->{'user'}[$i]);
    }
}
for ($i = 0; $i < count($data->{'data'}); $i++) {
    if ($data->{'data'}[$i]->{'parent_id'} == null) {
        for ($j = 0; $j < count($batchData); $j++) {
            if ($data->{'data'}[$i]->{'batch_id'} == $batchData[$j]->{'batch_id'}) {
                array_push($courseData, $data->{'data'}[$i]);
            }
        }
    }
}

for ($i = 0; $i < count($batchJson->{'batch'}); $i++) {
    if ($batchJson->{'batch'}[$i]->{'batch_id'} == $_SESSION['user_data']->{'user'}->{'batch_id'}) {
        array_push($allBatch, $batchJson->{'batch'}[$i]);
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
                            <a href="./index.php" class="flex items-center gap-x-4 h-[50px] rounded-xl px-4 hover:bg-cream text-dark-green hover:text-white">
                                <img class="w-5" src="../../Img/icons/assignment_icon.svg" alt="Assignment Icon">
                                <p class="font-semibold">Penugasan</p>
                            </a>
                        </li>
                        <li>
                            <a href="score.php" class="flex items-center gap-x-4 h-[50px] rounded-xl px-4 bg-cream">
                                <img class="w-5" src="../../Img/icons/score_white_icon.svg" alt="Score Icon">
                                <p class="text-white font-semibold">Nilai</p>
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
                            <a href="./index.php" class="flex items-center gap-x-4 h-[50px] rounded-xl px-4 hover:bg-cream text-dark-green hover:text-white">
                                <img class="w-5" src="../../Img/icons/assignment_icon.svg" alt="Assignment Icon">
                                <p class="font-semibold">Penugasan</p>
                            </a>
                        </li>
                        <li>
                            <a href="score.php" class="flex items-center gap-x-4 h-[50px] rounded-xl px-4 bg-cream">
                                <img class="w-5" src="../../Img/icons/score_white_icon.svg" alt="Score Icon">
                                <p class="text-white font-semibold">Nilai</p>
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
        <div class="bg-cgray w-full h-screen px-10 py-6 flex flex-col gap-y-6 overflow-y-scroll">
            <!-- Header / Profile -->
            <div class="items-center gap-x-4 justify-end hidden sm:flex">
                <img class="w-10" src="../../Img/icons/default_profile.svg" alt="Profile Image">
                <p class="text-dark-green font-semibold"><?= $_SESSION['user_data']->{'user'}->{'user_first_name'} . " " . $_SESSION['user_data']->{'user'}->{'user_last_name'} ?></p>
            </div>

            <!-- Breadcrumb -->
            <div>
                <ul class="flex items-center gap-x-4">
                    <li>
                        <a class="text-light-green" href="https://account.lumintulogic.com/home.php">Beranda</a>
                    </li>
                    <li>
                        <span class="text-light-green">/</span>
                    </li>
                    <li>
                        <a class="text-dark-green font-semibold" href="#">Nilai</a>
                    </li>
                </ul>
            </div>
            <div class="container- bg-white p-4 rounded score-box">
                <div class="container p-2 lg:p-4 rounded">
                    <div class="container" style="display: table;">
                        <div class="flex flex-col md:flex-row md:items-center md:space-x-2 gap-y-2">
                            <p class="font-bold">Periode :</p>
                            <!-- CONTENT DROPDOWN -->

                            <select id="countries" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block flex-1  p-2.5">
                                <option selected></option>
                                <?php for ($i = 0; $i < count($allBatch); $i++) : ?>
                                    <option value="<?= $allBatch[$i]->{'batch_id'}; ?>"><?= $allBatch[$i]->{'batch_name'}; ?></option>
                                <?php endfor ?>
                                <!-- <option value="US">Filter 2</option>
                                <option value="US">Filter 3</option> -->
                            </select>
                        </div>
                    </div>
                </div>
                <div class="container-lg">
                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <colgroup>
                                <col span="1" style="width: 10%">
                                <col span="1" style="width: 10%">
                                <col span="1" style="width: 10%">
                                <col span="1" style="width: 10%">
                                <col span="1" style="width: 10%">
                                <col span="1" style="width: 10%">

                            </colgroup>
                            <thead class="text-xs text-gray-700 uppercase bg-cream">
                                <tr>
                                    <th scope="col" class="px-6 py-3">
                                        Batch
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Course
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Bobot
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Nilai
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Total
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Predikat
                                    </th>

                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($courseData as $item) : ?>
                                    <tr class="bg-white border-b">
                                        <td class="px-6 py-2 font-medium text-gray-900 batch">
                                            <?= $item->modul_name ?>
                                        </td>
                                        <td class="px-6 py-4 font-medium text-gray-900 course">
                                            <table>
                                                <?php
                                                $dataSub = json_decode(http_request("https://lessons.lumintulogic.com/api/modul/read_childs.php?id=$item->id"));
                                                $dataSub = $dataSub->data;
                                                ?>
                                                <?php foreach ($dataSub as $itemSub) { ?>
                                                    <tr>
                                                        <td>
                                                            <p class="py-2"><?= $itemSub->modul_name ?></p>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            </table>

                                        </td>
                                        <td class="px-6 py-4 font-medium text-gray-900 weight">
                                            <table>
                                                <?php
                                                $dataWeg = json_decode(http_request("https://lessons.lumintulogic.com/api/modul/read_childs.php?id=$item->id"));
                                                $dataWeg = $dataWeg->data;
                                                $jmhCourse = count($dataWeg) ?>

                                                <?php foreach ($dataWeg as $itemWeg) { ?>
                                                    <tr>
                                                        <td>
                                                            <p class="py-2"><?= round(($item->modul_weight * 100) / $jmhCourse, 2)  ?>%</p>
                                                        </td>
                                                    </tr>
                                                <?php } ?>

                                            </table>
                                        </td>
                                        <td class="px-6 py-4 font-medium text-gray-900 score">
                                            <table>
                                                <?php
                                                $dataSub = json_decode(http_request("https://lessons.lumintulogic.com/api/modul/read_childs.php?id=$item->id"));
                                                $dataSub = $dataSub->data;
                                                $modul_weight = 0;
                                                $jmh_score = 0
                                                ?>

                                                <?php foreach ($dataSub as $itemSub) {
                                                ?>
                                                    <tr>
                                                        <td>
                                                            <?php

                                                            require_once "../../Model/Scores.php";
                                                            $sc = new Scores;
                                                            $sc->setStudentId($_SESSION['user_data']->{'user'}->{'user_id'});
                                                            $s = $sc->getScoreByModulIdAndAssignmentId($itemSub->id);
                                                            $jmh_task = count($s);
                                                            $totalScore = array();
                                                            if (count($s) == 0) {
                                                                echo (0);
                                                            } else {
                                                                foreach ($s as $key => $value) {
                                                                    $jmh_score += $value['score_value'];
                                                                    $dataModulWeg = json_decode(http_request("https://lessons.lumintulogic.com/api/modul/read_childs.php?id=$item->id"));
                                                                    $dataWeight = $dataModulWeg->data;
                                                                    foreach ($dataWeight as $weight) {
                                                                        $modul_weight += $weight->modul_weight;
                                                                    }
                                                                }
                                                                echo $jmh_score / $jmh_task;
                                                            }

                                                            ?>
                                                            <p class="py-2"></p>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            </table>
                                        </td>
                                        <td class="px-6 py-4 font-medium text-gray-900 total">
                                            <table>
                                                <?php
                                                $dataSub = json_decode(http_request("https://lessons.lumintulogic.com/api/modul/read_childs.php?id=$item->id"));
                                                $dataSub = $dataSub->data;
                                                $modul_weight = 0;
                                                $jumlah = 0;
                                                $jmh_score = 0;
                                                ?>
                                                <?php foreach ($dataSub as $itemSub) {
                                                ?>
                                                    <tr>
                                                        <td>
                                                            <?php

                                                            require_once "../../Model/Scores.php";
                                                            $sc = new Scores;
                                                            $sc->setStudentId($_SESSION['user_data']->{'user'}->{'user_id'});
                                                            $s = $sc->getScoreByModulIdAndAssignmentId($itemSub->id);
                                                            $jmh_task = count($s);
                                                            foreach ($s as $key => $value) {
                                                                $jmh_score += $value['score_value'];
                                                                $jumlah = ($jmh_score / $jmh_task) * (round(($item->modul_weight * 100) / $jmhCourse, 2) / 100);
                                                                $dataModulWeg = json_decode(http_request("https://lessons.lumintulogic.com/api/modul/read_childs.php?id=$item->id"));
                                                                $dataWeight = $dataModulWeg->data;
                                                                foreach ($dataWeight as $weight) {
                                                                    $modul_weight += $weight->modul_weight;
                                                                }
                                                            }

                                                            $totalScore = $jumlah;

                                                            ?>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                                <p class="py-2"><?= round($totalScore, 1) . "<br />"; ?></p>
                                            </table>
                                        </td>
                                        <td class="px-6 py-4 font-medium text-gray-900 grade">
                                            <table>
                                                <?php
                                                $dataSub = json_decode(http_request("https://lessons.lumintulogic.com/api/modul/read_childs.php?id=$item->id"));
                                                $dataSub = $dataSub->data;
                                                $modul_weight = 0;
                                                $jumlah = 0;
                                                $totalScore = 0;
                                                ?>
                                                <?php foreach ($dataSub as $itemSub) {
                                                ?>
                                                    <tr>
                                                        <td>
                                                            <?php

                                                            require_once "../../Model/Scores.php";
                                                            $sc = new Scores;
                                                            $sc->setStudentId($_SESSION['user_data']->{'user'}->{'user_id'});
                                                            $s = $sc->getScoreByModulIdAndAssignmentId($itemSub->id);
                                                            foreach ($s as $key => $value) {
                                                                $jumlah = $jumlah + ($value['score_value'] * $itemSub->modul_weight);
                                                                $dataModulWeg = json_decode(http_request("https://lessons.lumintulogic.com/api/modul/read_childs.php?id=$item->id"));
                                                                $dataWeight = $dataModulWeg->data;
                                                                foreach ($dataWeight as $weight) {
                                                                    $modul_weight += $weight->modul_weight;
                                                                }
                                                            }

                                                            $totalScore = $jumlah;

                                                            ?>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                                <p class="py-2">
                                                    <?php
                                                    if ($totalScore >= 85) {
                                                        echo "A";
                                                    } elseif ($totalScore >= 80 && $totalScore <= 84) {
                                                        echo "A-";
                                                    } elseif ($totalScore >= 75 && $totalScore <= 79) {
                                                        echo "B+";
                                                    } elseif ($totalScore >= 70 && $totalScore <= 74) {
                                                        echo "B";
                                                    } elseif ($totalScore >= 65 && $totalScore <= 69) {
                                                        echo "C+";
                                                    } elseif ($totalScore >= 60 && $totalScore <= 64) {
                                                        echo "C";
                                                    } elseif ($totalScore >= 55 && $totalScore <= 59) {
                                                        echo "D";
                                                    } elseif ($totalScore >= 0 && $totalScore <= 54) {
                                                        echo "E";
                                                    } else {
                                                        echo "Not Found";
                                                    }
                                                    ?>
                                                </p>
                                            </table>
                                        </td>
                                    </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>



            <script src="https://unpkg.com/flowbite@1.4.1/dist/flowbite.js"></script>
            <script>
                let btnToggle = document.getElementById('btnToggle');
                let btnToggle2 = document.getElementById('btnToggle2');
                let sidebar = document.querySelector('.sidebar');
                let leftNav = document.getElementById('left-nav');
                // let listMenu = document.getElementById('dropdownMenu');
                // let listContainer = document.getElementById('dropdownRightStart');

                btnToggle.onclick = function() {
                    sidebar.classList.toggle('in-active');
                }

                btnToggle2.onclick = function() {
                    leftNav.classList.toggle('hidden');
                }

                $(document).ready(function() {
                    $('#btnHelp').click(function() {
                        introJs().setOptions({
                            steps: [{
                                    intro: "Halo Selamat Datang Di Halaman Score Students"
                                }, {
                                    element: document.querySelector('.score-box'),
                                    intro: "Ini merupakan halaman score dimana students akan melihat nilai mereka"
                                }, {
                                    element: document.querySelector('.batch'),
                                    intro: "Di kolom pertama ini merupakan batch yang di ikutin oleh students"
                                },
                                {
                                    element: document.querySelector('.course'),
                                    intro: "Di kolom kedua ini merupakan course yang bakal di ikuti oleh students"
                                }, {
                                    element: document.querySelector('.weight'),
                                    intro: "Di kolom ketiga ini merupakan bobot nilai dari tiap - tiap course"
                                }, {
                                    element: document.querySelector('.score'),
                                    intro: "Di kolom keempat ini merupakan nilai/score dari tiap - tiap course"
                                },
                                {
                                    element: document.querySelector('.total'),
                                    intro: "Di kolom kelima ini merupakan total kalkulasi nilai/score dari tiap - tiap course"
                                },
                                {
                                    element: document.querySelector('.grade'),
                                    intro: "Di kolom keenam ini merupakan nilai/score huruf dari tiap - tiap course"
                                }

                            ]
                        }).start();
                    })

                })
            </script>


</body>

</html>