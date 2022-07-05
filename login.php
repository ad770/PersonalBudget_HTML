<?php
session_start();

require_once 'database.php';

if (!isset($_SESSION['logged'])) {

	if (isset($_POST['email']) || (isset($_POST['password']))) {
		$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
		$password = filter_input(INPUT_POST, 'password');

		if (empty($email)) {
			$_SESSION['given_email'] = $_POST['email'];
			header('Location: index.php');
			exit();
		} else {

			$user_query = $db->prepare('SELECT * FROM users WHERE email=:email');
			$user_query->bindValue(':email', $email, PDO::PARAM_STR);
			$user_query->execute();
			$user = $user_query->fetch();

			if ($user && password_verify($password, $user['password'])) {
				$_SESSION['logged'] = true;
				$_SESSION['id'] = $user['id'];
				$_SESSION['username'] = $user['username'];
				$_SESSION['email'] = $user['email'];
				unset($_SESSION['bad_attempt']);
			} else {
				$_SESSION['bad_attempt'] = true;
				header('Location: index.php');
				exit();
			}
			header('Location: main.php');
		}
	} else {
		header('Location: index.php');
		exit();
	}
}
