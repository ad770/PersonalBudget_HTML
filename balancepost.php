<?php

session_start();

if (!isset($_POST['balance'])) {
    header('Location: balance.php');
    $e_balance = "Błąd przesyłania danych! Spróbuj ponownie.";
    exit();
} else {
    $begin_of_current_month = date('Y-m-d', strtotime("first day of this month"));
    $end_of_current_month = date('Y-m-d', strtotime("last day of this month"));
    $begin_of_previous_month = date('Y-m-d', strtotime("first day of last month"));
    $end_of_previous_month = date('Y-m-d', strtotime("last day of last month"));
    $begin_of_current_year = date('Y-m-d', strtotime("first day of january"));
    $end_of_current_year = date('Y-m-d', strtotime("last day of december"));
    $today = date('d-m-Y');
    $state = $_POST['balance'];
    switch ($state) {
        case "current_month":
            $_SESSION['begin_date'] = $begin_of_current_month;
            $_SESSION['end_date'] = $end_of_current_month;
            break;
        case "previous_month":
            $_SESSION['begin_date'] = $begin_of_previous_month;
            $_SESSION['end_date'] = $end_of_previous_month;
            break;
        case "current_year":
            $_SESSION['begin_date'] = $begin_of_current_year;
            $_SESSION['end_date'] = $end_of_current_year;
            break;
        case "undenify":
            $_SESSION['begin_date'] = $_POST['begin_date'];
            $_SESSION['end_date'] = $_POST['end_date'];
            break;
        default:
            break;
    }
    header('Location: balance.php');
}
