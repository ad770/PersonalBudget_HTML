<?php

session_start();

if (isset($_SESSION['successful_registration'])) {
	$email = $_SESSION['email'];
	require_once 'database.php';

	$result = $db->prepare("SELECT id FROM users WHERE email='$email'");
	$result->execute();
	$row = $result->fetch(PDO::FETCH_ASSOC);
	$user_id = $row['id'];

	$statement = $db->prepare("INSERT INTO expenses_category_assigned_to_users SELECT null, '$user_id', name FROM expenses_category_default");
	$statement->execute();
	$statement = $db->prepare("INSERT INTO incomes_category_assigned_to_users SELECT null, '$user_id', name FROM incomes_category_default");
	$statement->execute();
	$statement = $db->prepare("INSERT INTO payment_methods_assigned_to_users SELECT null, '$user_id', name FROM payment_methods_default");
	$statement->execute();

	header('Location: index.php');
} else {
	unset($_SESSION['successful_registration']);
}

//Usuwanie zmiennych pamiętających wartości wpisane do formularza
if (isset($_SESSION['fr_username'])) unset($_SESSION['fr_username']);
if (isset($_SESSION['fr_email'])) unset($_SESSION['fr_email']);
if (isset($_SESSION['fr_password'])) unset($_SESSION['fr_password']);
if (isset($_SESSION['fr_check_password'])) unset($_SESSION['fr_check_password']);

//Usuwanie błędów rejestracji
if (isset($_SESSION['e_username'])) unset($_SESSION['e_username']);
if (isset($_SESSION['e_email'])) unset($_SESSION['e_email']);
if (isset($_SESSION['e_password'])) unset($_SESSION['e_password']);
