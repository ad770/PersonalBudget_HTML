    <?php
	
		session_start();
		
		if(!isset($_POST['email']) || (!isset($_POST['password'])))
		{
			header('Location: index.php');
			exit();
		}
		
		require_once "connect.php";
		
		$connection = @new mysqli($host, $db_user, $db_password, $db_name);
		
		if ($connection->connect_errno!=0)
		{
				echo "Error:".$connection->connect_errno;
		}
		else
		{
        $email = $_POST['email'];
        $password = $_POST['password'];
		
		$email = htmlentities($email, ENT_QUOTES, "UTF-8");		
		
		if ($result = @$connection->query(
		sprintf("SELECT * FROM users WHERE email='%s'",
		mysqli_real_escape_string($connection,$email))))
			{
				$user_amount = $result->num_rows;
				if ($user_amount>0)
				{
					$row = $result->fetch_assoc();
					if (password_verify($password, $row['password']))
					{
						$_SESSION['logged'] = true;
						$_SESSION['id'] = $row['id'];
						$_SESSION['username'] = $row['username'];
						$_SESSION['email'] = $row['email'];
						
						unset($_SESSION['alert']);
						$result->close();
						header('Location: main.php');
					}
					else {
					$_SESSION['alert'] = '<span style="color:red">Nieprawidłowy login lub hasło!</span>';
					header('Location: index.php');
					} 
				} else {
					
					$_SESSION['alert'] = '<span style="color:red">Nieprawidłowy login lub hasło!</span>';
					header('Location: index.php');
				}
			}
		$connection->close();
		}
    ?>
