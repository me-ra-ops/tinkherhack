<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if(!isset($_SESSION['role'])){
    header("Location: login.php");
    exit();
}

switch($_SESSION['role']) {

    case 'class_rep':
    case 'execom':
        header("Location: student/dashboard.php");
        break;

    case 'staff':
        header("Location: staff/dashboard.php");
        break;

    case 'dean':
        header("Location: dean/dashboard.php");
        break;

    case 'admin':
        header("Location: admin/dashboard.php");
        break;

    default:
        echo "Invalid role.";
}

exit();
?>