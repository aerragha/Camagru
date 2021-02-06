<?
	require_once("config/setup.php");
	session_start();
    if (isset($_SESSION["id"]))
		header("location: main.php");
	function check_format_input($email, $first_name, $last_name, $user_name, $password, $Conf_password)
	{
		$uppercase = preg_match('@[A-Z]@', $password);
		$lowercase = preg_match('@[a-z]@', $password);
		$number    = preg_match('@[0-9]@', $password);
		if(!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 150)
			return "Email Format incorrect!";
		if(!preg_match("/^[a-zA-Z ]{2,20}$/", $first_name))
			return "Firstname Format incorrect!";
		if (!preg_match("/^[a-zA-Z ]{2,20}$/", $last_name)) 
			return "Lastname Format incorrect!";
		if(!preg_match("/^[a-zA-Z0-9]{2,20}$/", $user_name))
			return "Username Format incorrect!";
		if($password != $Conf_password)
			return "Password is not like Confirmation!!";
		if (strlen($password) < 8 || strlen($password) > 100)
			return "Password must be between 8 and 100 char";
		if (!$uppercase || !$lowercase || !$number)
			return "Password Format incorrect!!";
		return "Done";
	}

	function check_email($email, $cn)
	{
		$cmd = $cn->prepare("SELECT COUNT(*) FROM USERS WHERE email = :email");
		$cmd->bindParam(':email', $email);
		try
		{
			$cmd->execute();
			return($cmd->fetchColumn());
		}
		catch (PDOException $ex)
		{
			echo 'query error!';
		}
	}
	
	function check_username($user_name, $cn)
	{
		$cmd = $cn->prepare("SELECT COUNT(*) FROM USERS WHERE username = :username");
		$cmd->bindParam(':username', $user_name);
		try
		{
			$cmd->execute();
			return($cmd->fetchColumn());
		}
		catch (PDOException $ex)
		{
			echo 'query error!';
		}
	}

	
	if (isset($_POST["first_name"]) && isset($_POST['last_name']) && isset($_POST["user_name"]) && 
		isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["Conf_password"]))
	{
		$first_name = '';
		$last_name = '';
		$last_name = '';
		$user_name = '';
		$email = '';

		$first_name = trim($_POST["first_name"]);
		$last_name = trim($_POST["last_name"]);
		$user_name = trim($_POST["user_name"]);
		$email = trim($_POST["email"]);
		$password = $_POST["password"];
		$Conf_password = $_POST["Conf_password"];
		$err = check_format_input($email, $first_name, $last_name, $user_name, $password, $Conf_password);
		if($err == "Done")
		{
			if (check_email($email, $cn) != 0)
                $msg = "This Email already exists!";
            else if (check_username($user_name, $cn) != 0)
				$msg = "This username already exists!";
			else
			{
				$ValidateCode = hash('whirlpool', trim($_POST["email"]));
			
				$cmd = $cn->prepare("INSERT INTO USERS(first_name, Last_name, username, email, pass, activationCode) 
				VALUES (:first_name, :Last_name, :username, :email, :pass, :activationCode)");
				$cmd->bindParam(':first_name', $first_name);
				$cmd->bindParam(':Last_name', $last_name );
				$cmd->bindParam(':username', $user_name);
				$cmd->bindParam(':email', $email);
				$cmd->bindParam(':pass', hash('whirlpool', $password));
				$cmd->bindParam(':activationCode', $ValidateCode);
				try
				{
					$cmd->execute();
					$valid_sub = "Activate your account!";
					$valid_msg = "Hello " . $_POST["user_name"] .
					"<br>Thank you for your registration<br>" .
					"To validate your account Click this link : <a href='http://localhost/validateAccount.php?code=" . $ValidateCode . "'>Click Here!</a> <br>Have a nice Day!";
					//$headers = "FROM : Camagru@gmail.com" . "\r\n";
					$headers .=  "Content-Type: text/html". "\r\n";
					if(mail($email, $valid_sub, $valid_msg, $headers))
					{
						$signup = 1;
					}
					else {
						echo $email;
					}
					// 
				}
				catch (PDOException $ex)
				{
					echo 'query error!';
				}
			}
		}
		else 
			$msg = $err;
	}
	require_once("header.php");
?>
		<div class="container text-center registre-container" id="registre-container">
			<form name="frm" method="post">
            <div class="row mt-4 text-center">
				<div class="offset-lg-3 col-lg-6 col-xs-12">
					<h1 class="mb-5 font-weight-light text-uppercase tit">Sign up</h1>
					<p class="msg_err"><?php if(isset($msg)) echo $msg; ?></p>
				</div>
			</div>
			<div class="row mt-4">
				<div class="offset-lg-3 col-lg-6 col-xs-12">
					<input type="text" class="form-control rounded-pill form-control-lg" name="first_name" value="<?  echo $first_name; ?>" pattern="[a-zA-Z ]{2,20}" placeholder="First name" required>
				</div>
			</div>
			<br>
			<div class="row mt-4">
				<div class="offset-lg-3 col-lg-6 col-xs-12">
					<input type="text" class="form-control rounded-pill form-control-lg" name="last_name" value="<? echo $last_name; ?>" pattern="[a-zA-Z ]{2,20}" placeholder="Last name" required>
				</div>
			</div>
			<br>
			<div class="row mt-4">
				<div class="offset-lg-3 col-lg-6 col-xs-12">
					<input type="text" class="form-control rounded-pill form-control-lg" name="user_name" value="<? echo $user_name; ?>" pattern="[a-zA-Z0-9]{2,20}" placeholder="UserName" required>
				</div>
			</div>
			<br>
			<div class="row mt-4">
				<div class="offset-lg-3 col-lg-6 col-xs-12">
					<input type="email" class="form-control rounded-pill form-control-lg" name="email" value="<? echo $email; ?>" maxlength="150" placeholder="Email" required>
				</div>
			</div>
			<br>
			<div class="row mt-4">
				<div class="offset-lg-3 col-lg-6 col-xs-12">
					<input type="password" class="form-control rounded-pill form-control-lg" name="password" pattern=".{8,100}" placeholder="Password" required>
				</div>
			</div>
			<br>
			<div class="row mt-4">
				<div class="offset-lg-3 col-lg-6 col-xs-12">
					<input type="password" class="form-control rounded-pill form-control-lg" name="Conf_password" pattern=".{8,100}" placeholder="Confirm Password" required>
				</div>
			</div>
			<br>
			<div class="row">
				<div class="offset-lg-3 col-lg-6 col-xs-12">
					<button type="submit" class="btn btn-custom mt-4 btn-block rounded-pill" id="signup_btn">Sign Up</button>
				</div>
			</div> 
			<div class="row mt-3">
				<div class="offset-lg-3 col-lg-6 col-xs-12">
					<p class="mt-3 font-weight-normal">Do you have an account ? <a href="index.php" id="log_link"><strong>Login</strong></a></p>
				</div>
			</div>     
        </form>
        </div>
        <div class="active_div">
            <div class="container text-center activi-container">
                <div class=" activi_txt">
                    
                    <img src="img/5a142d709c.png">
                    <h2 class="mt-4">Activation Mail Was sent To <span id="email"></span> </h2>
                    <a href="index.php" class="btn btn-info mt-4">Login</a>
                </div>
            </div>
        </div>
    </body>
	<footer class="footer text-center">
			© Copyright <a href="https://profile.intra.42.fr/users/aerragha" target="_blank">aerragha</a> 2019
	</footer>
</html>

<?php
	if ($signup == 1)
	{
		echo "<script>document.querySelector(\"#email\").innerHTML = \" " . $email . "\";</script>";
		echo "<script>document.querySelector(\".active_div\").style.display = \"inline\";</script>";
	}
	
?>