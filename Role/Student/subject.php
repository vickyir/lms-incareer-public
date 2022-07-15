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

// var_dump($_SESSION['user_data']);

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

require_once "../../api/get_api_data.php";

$subjectData = array();
$courseData = array();

$modulJSON = json_decode(http_request("https://lessons.lumintulogic.com/api/modul/read_modul_rows.php"));

for ($i = 0; $i < count($modulJSON->{'data'}); $i++) {
    if ($modulJSON->{'data'}[$i]->{'parent_id'} == $_GET['course_id']) {
        array_push($subjectData, $modulJSON->{'data'}[$i]);
    }
}

for ($i = 0; $i < count($modulJSON->{'data'}); $i++) {
    if ($modulJSON->{'data'}[$i]->{'id'} == $_GET['course_id']) {
        array_push($courseData, $modulJSON->{'data'}[$i]);
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
    <script src="https://cdn.tailwindcss.com?plugins=line-clamp"></script>
    <!-- <link rel="stylesheet" href="https://unpkg.com/flowbite@1.4.1/dist/flowbite.min.css" /> -->
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
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        /* Hide scrollbar for IE, Edge and Firefox */
        .no-scrollbar {
            -ms-overflow-style: none;  /* IE and Edge */
            scrollbar-width: none;  /* Firefox */
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

                <hr class="border-[1px] border-opacity-50 border-[#93BFC1]"/>

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
        <div class="bg-cgray w-full h-screen px-10 py-6 flex flex-col gap-y-6 overflow-y-scroll">
            <!-- Header / Profile -->
            <div class="items-center gap-x-4 justify-end hidden sm:flex">
                <img class="w-10" src="../../Img/icons/default_profile.svg" alt="Profile Image">
                <p class="text-dark-green font-semibold"><?= $_SESSION['user_data']->{'user'}->{'user_first_name'} . " " . $_SESSION['user_data']->{'user'}->{'user_last_name'} ?></p>
            </div>

            <!-- Breadcrumb -->
            <div class="p-2 lg:p-4">
                <ul class="flex items-center gap-x-4 text-xs lg:text-base">
                    <!-- NAVIGATOR HALAMAN HOME -->

                    <li>

                        <a class="text-light-green hover:text-dark-green hover:font-semibold" href="#">Beranda</a>
                    </li>
                    <li>
                        <span class="text-light-green">/</span>
                    </li>
                    <!-- NAVIGATOR HALAMAN COURSES -->

                    <li>
                        <a class="text-light-green hover:text-dark-green hover:font-semibold" href="index.php">Batch</a>
                    </li>
                    <li>
                        <span class="text-light-green">/</span>
                    </li>
                    <!-- NAVIGATOR HALAMAN SUB TOPIC -->

                    <li>
                        <a class="text-dark-green font-semibold" href="#">Materi</a>
                    </li>
                </ul>
            </div>
            <!-- TITTLE -->

            <div class="p-2 lg:p-4">
                <p class="text-lg md:text-xl lg:text-2xl xl:text-4xl text-dark-green font-semibold">Daftar Materi dari Batch <?= $courseData[0]->{'modul_name'}; ?></p>
            </div>

            <div class="p-2 lg:p-4 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                <!-- MEMANGGIL DAN MENAMPILKAN SELURUH SUB TOPIC YANG ADA ATAU SUDAH DIBUAT PADA COURSE YANG SEDANG DIBUKA-->

                <?php foreach ($subjectData as $row => $subject) : ?>
                    <a href="assignment.php?subject_id=<?= $subject->{'id'}; ?>&course_id=<?= $_GET['course_id'] ?>" class="block p-6 max-w-sm bg-white rounded-lg border border-gray-200 shadow-md hover:bg-gray-100" data-tooltip-target="tooltip-default<?= $subject->{'id'}; ?>">
                        <h5 class="mb-2 text-lg lg:text-2xl font-bold tracking-tight text-gray-900 line-clamp-1 sm:line-clamp-2 truncate..."><?= $subject->{'modul_name'}; ?></h5>
                        <p class="font-normal text-gray-700 flex flex-row items-center gap-4 mt-5">
                            <img src="../../Img/icons/dokumen_icon.svg" alt="dokumen"><?= $subject->{'id'}; ?>
                        </p>
                    </a>
                    <div id="tooltip-default<?= $subject->{'id'}; ?>" role="tooltip" class="inline-block absolute invisible z-10 py-2 px-3 text-sm font-medium text-gray-700 bg-gray-300 rounded-lg opacity-0 transition-opacity duration-300 tooltip max-w-[150px] shadow-lg shadow-gray-400">
                        <?= $subject->{'modul_name'}; ?>
                        <div class="tooltip-arrow" data-popper-arrow></div>
                    </div>
                <?php endforeach ?>
            </div>
        </div>
    </div>
    <!-- CDN FLOWBITE -->

    <script src="https://unpkg.com/flowbite@1.4.1/dist/flowbite.js"></script>
    <!-- FUNGSI TOMBOL BUTTON TOGGLE SIDEBAR -->

    <script>
        let btnToggle = document.getElementById('btnToggle');
        let btnToggle2 = document.getElementById('btnToggle2');
        let sidebar = document.querySelector('.sidebar');
        let leftNav = document.getElementById("left-nav");
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
    </script>


</body>

</html>