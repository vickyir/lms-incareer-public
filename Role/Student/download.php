<?php
session_start();

$loginPath = "../../login.php";

if (!isset($_SESSION['user_data'])) {
    header("location: " . $loginPath);
    die;
}

if (!isset($_COOKIE['X-LUMINTU-REFRESHTOKEN'])) {
    unset($_SESSION['user_data']);
    header("location: " . $loginPath);
}

switch ($_GET['type']) {
    case 'q':
        $path = "../../Upload/Assignment/Questions/";
        if (isset($_GET['file'])) {
            //Read the filename
            $filename = $path . $_GET['file'];
            //Check the file exists or not
            if (file_exists($filename)) {

                //Define header information
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header("Cache-Control: no-cache, must-revalidate");
                header("Expires: 0");
                header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
                header('Content-Length: ' . filesize($filename));
                header('Pragma: public');

                //Clear system output buffer
                flush();

                //Read the size of the file
                readfile($filename);

                //Terminate from the script
                die();
            } else {
                echo "File does not exist.";
            }
        } else {
            echo "Filename is not defined.";
        }

        break;

    case 's':
        $path = "../../Upload/Assignment/Submission/";
        if (isset($_GET['file'])) {
            //Read the filename
            $filename = $path . $_GET['file'];
            //Check the file exists or not
            if (file_exists($filename)) {

                //Define header information
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header("Cache-Control: no-cache, must-revalidate");
                header("Expires: 0");
                header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
                header('Content-Length: ' . filesize($filename));
                header('Pragma: public');

                //Clear system output buffer
                flush();

                //Read the size of the file
                readfile($filename);

                //Terminate from the script
                die();
            } else {
                echo "File does not exist.";
            }
        } else {
            echo "Filename is not defined.";
        }

        break;
    default:
        break;
}
