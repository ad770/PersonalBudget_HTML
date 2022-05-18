<?php

	session_start();
	
	if (isset($_POST['email']))
	{
		$is_good = true;
		$username = $_POST['username'];
		
		if ((strlen($username)<3) || (strlen($username)>50))
		{
			$is_good = false;
			$_SESSION['e_username'] = "Nick musi posiadać od 3 do 50 znaków!";
		}
		
		$email=$_POST['email'];
		$emailB = filter_var($email, FILTER_SANITIZE_EMAIL);
		
		if ((filter_var($emailB, FILTER_VALIDATE_EMAIL)==false) || ($emailB!=$email))
		{
			$is_good = false;
			$_SESSION['e_email'] = "Podaj poprawny adres e-mail";
		}
		
		$password = $_POST['password'];
		$check_password = $_POST['check_password'];
		
		if (strlen($password)<8)
		{
			$is_good = false;
			$_SESSION['e_password']="Hasło musi posiadać conajmniej 8 znaków!";
		}
		
		if ($password!=$check_password)
		{
			$is_good = false;
			$_SESSION['e_password']="Podane hasła muszą być identyczne!";
		}
		
		$password_hash = password_hash($password, PASSWORD_DEFAULT);
		
		$_SESSION['fr_username'] = $username;
		$_SESSION['fr_email']= $email;
		
		require_once "connect.php";
		mysqli_report(MYSQLI_REPORT_STRICT);
		try
		{
			$connection = new mysqli($host, $db_user, $db_password, $db_name);
				if ($connection->connect_errno!=0)
			{
				throw new Exception(mysqli_connect_errno());
			}
			else
			{
				$result = $connection->query("SELECT id FROM users WHERE email='$email'");
				if (!$result) throw new Exception($connection->error);
				
				$emails_amount = $result->num_rows;
				if ($emails_amount>0)
				{
					$is_good=false;
					$_SESSION['e_email']="Istnieje już konto o takim adresie email";
				}
				
				if($is_good==true)
				{
					//Wszystkie testy zaliczone, dodajemy gracza do bazy
					if ($connection->query("INSERT INTO users VALUES (NULL, '$username', '$password_hash','$email')"))
					{
						$_SESSION['email']=$email;
						$_SESSION['successful_registration']=true;
						header('Location: welcome.php');
					}
					else
					{
						throw new Exception($connection->error);
					}
				}
				$connection->close();
			}
		}
		catch(Exception $e)
		{
				echo '<span style="color:red;">Błąd serwera! Przepraszamy za niedogodności i prosimy o rejestrację w innym terminie!</span>';
				echo '<br/>Informacja developerska: '.$e;
		}	
	}
	
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
	<link rel="stylesheet" href="my_styles.css">
</head>

<body>
        <nav class="navbar navbar-dark bg-dark justify-content-center text-center p-3">
                <a class="navbar-brand text-center" href="register.php"><svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor" class="bi bi-bank2" viewBox="0 0 1em 1em">
                    <path d="M8.277.084a.5.5 0 0 0-.554 0l-7.5 5A.5.5 0 0 0 .5 6h1.875v7H1.5a.5.5 0 0 0 0 1h13a.5.5 0 1 0 0-1h-.875V6H15.5a.5.5 0 0 0 .277-.916l-7.5-5zM12.375 6v7h-1.25V6h1.25zm-2.5 0v7h-1.25V6h1.25zm-2.5 0v7h-1.25V6h1.25zm-2.5 0v7h-1.25V6h1.25zM8 4a1 1 0 1 1 0-2 1 1 0 0 1 0 2zM.5 15a.5.5 0 0 0 0 1h15a.5.5 0 1 0 0-1H.5z"/>
                  </svg> System zarządzania budżetem osobisym</a>
        </nav>
    <div class="container">
        <div class="row align-middle">
            <div class="col-10 col-sm-8 col-md-6 col-lg-5 col-xl-4 col-xxl-3 loginPanel p-1 pt-3 mb-3 mx-auto">
                <form method="post">
                    <div
                        class="row text-center justify-content-center border rounded-3 bg-success p-4 text-dark bg-opacity-10">
                        <div class="col-12 text-center m-auto">
							<div class="input-group my-3 mx-auto">
                                <span class="input-group-text" id="basic-addon1"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person" viewBox="0 0 16 16">
							  <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z"/>
							</svg></span>
                                <input 
										type="text" name="username"	class="form-control" placeholder="Username"  
										value="<?php
											if (isset($_SESSION['fr_username']))
											{
												echo $_SESSION['fr_username'];
												unset($_SESSION['fr_username']);
											}?>"
										aria-label="Username" aria-describedby="basic-addon1">
										<br/>
										<?php
											if(isset($_SESSION['e_username']))
											{
												echo '<div class="error">'.$_SESSION['e_username'].'</div>';
												unset($_SESSION['e_username']);
											}
										?>
                            </div>                            
							<div class="input-group my-3 mx-auto">
                                <span class="input-group-text" id="basic-addon1"><svg xmlns="http://www.w3.org/2000/svg"
                                        width="16" height="16" fill="currentColor" class="bi bi-envelope"
                                        viewBox="0 0 16 16">
                                        <path
                                            d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4Zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1H2Zm13 2.383-4.708 2.825L15 11.105V5.383Zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741ZM1 11.105l4.708-2.897L1 5.383v5.722Z" />
                                    </svg></span>
                                <input 
										type="email" name="email" class="form-control" placeholder="Email" 
										value="<?php
											if (isset($_SESSION['fr_email']))
											{
												echo $_SESSION['fr_email'];
												unset($_SESSION['fr_email']);
											}?>"
										aria-label="Email" aria-describedby="basic-addon1">
										<br/>
										<?php
											if(isset($_SESSION['e_email']))
											{
												echo '<div class="error">'.$_SESSION['e_email'].'</div>';
												unset($_SESSION['e_email']);
											}
										?>
                            </div>
							<div class="input-group my-3 mx-auto">
                                <span class="input-group-text" id="basic-addon1"><svg xmlns="http://www.w3.org/2000/svg"
                                        width="16" height="16" fill="currentColor" class="bi bi-lock"
                                        viewBox="0 0 16 16">
                                        <path
                                            d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2zM5 8h6a1 1 0 0 1 1 1v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V9a1 1 0 0 1 1-1z" />
                                    </svg></span>
                                <label for="password" class="form-label"></label>
                                <input type="password" name="password" class="form-control" id="password" aria-describedby="passwordHelp"
                                    placeholder="Password">
									<?php
											if(isset($_SESSION['e_password']))
											{
												echo '<div class="error">'.$_SESSION['e_password'].'</div>';
												unset($_SESSION['e_password']);
											}
										?>
                            </div>
							<div class="input-group mb-1 mx-auto">
                                <span class="input-group-text" id="basic-addon1"><svg xmlns="http://www.w3.org/2000/svg"
                                        width="16" height="16" fill="currentColor" class="bi bi-lock"
                                        viewBox="0 0 16 16">
                                        <path
                                            d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2zM5 8h6a1 1 0 0 1 1 1v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V9a1 1 0 0 1 1-1z" />
                                    </svg></span>
                                <label for="check_password" class="form-label"></label>
                                <input type="password" name="check_password" class="form-control" id="check_password" aria-describedby="passwordHelp"
                                    placeholder="Confirm Password">
                            </div>

                            <div class="input-group mb-3 w-50 mx-auto justify-content-center">
                                <button type="submit" class="btn btn-primary mt-3 rounded-pill">Rejestracja</button>
                            </div>
                        </div>
                        <footer class="mb-1">Masz już konto? <a href="index.php">Zaloguj się</a></footer>
                    </div>
            </div>
            </form>
        </div>
    </div>

    <footer class="page-footer fixed-bottom text-center bg-dark text-white">2022 &#169; Adrian Żuchowski</footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"
        integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"
        integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13"
        crossorigin="anonymous"></script>
</body>

</html>