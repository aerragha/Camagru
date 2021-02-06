<?
	require_once("config/setup.php");
	session_start();
    if (isset($_SESSION["id"]))
		header("location: main.php");

		function check_if_exist($user_name, $password, $cn)
		{
			$cmd = $cn->prepare("SELECT COUNT(*) FROM USERS WHERE username = :username AND pass = :pass");
			$cmd->bindParam(':username', $user_name);
			$cmd->bindParam(':pass', $password);
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
		function check_if_active($user_name, $cn)
		{
			$cmd = $cn->prepare("SELECT isActif FROM USERS WHERE username = :username");
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

	if (isset($_POST["user_name"]) && isset($_POST["password"]))
	{
		$user_name = trim($_POST["user_name"]);
		$password = hash('whirlpool', $_POST["password"]);
		if (check_if_exist($user_name, $password, $cn) != 0)
		{
			if (check_if_active($user_name, $cn) == 1)
			{
				$cmd = $cn->prepare("SELECT * FROM USERS WHERE username = :username");
				$cmd->bindParam(':username', $user_name);
				try
				{
					$cmd->execute();
				}
				catch (PDOException $ex)
				{
					echo 'query error!';
				}
				if ($result = $cmd->fetch())
				{
					$_SESSION["id"] = $result["id_user"];
					$_SESSION["email"] = $result["email"];
					$_SESSION["username"] = $result["username"];
					$_SESSION["image"] = $result["image"];
					$_SESSION["first_name"] = $result["first_name"];
					$_SESSION["Last_name"] = $result["Last_name"];
					$_SESSION["NotifActiv"] = $result["NotifActiv"];
					header("location: main.php");
				}
			}
			else
				$msg = "Please Activate Your Account !";
		}
		else
			$msg = "Login and/or Password incorrect!";

	}
	require_once("header.php");
?>
        <form name="frm" method="post">
        <div class="container text-center login-container">
			<div class="row mt-4 text-center">
				<div class="offset-lg-3 col-lg-6 col-xs-12">
					<h1 class="mb-5 font-weight-light text-uppercase tit">Login</h1>
					<p class="msg_err"><?php if(isset($msg)) echo $msg; ?></p>
				</div>
			</div>
			<div class="row mt-4">
				<div class="offset-lg-3 col-lg-6 col-xs-12">
					<input type="text" class="form-control rounded-pill form-control-lg" name="user_name" maxlength="150" placeholder="UserName" required>
				</div>
			</div>
			<br>
			<div class="row">
				<div class="offset-lg-3 col-lg-6 col-xs-12">
					<input type="password" class="form-control rounded-pill form-control-lg" name="password" pattern=".{6,100}" placeholder="Password" required>
				</div>
			</div>  
			<div class="row mt-4">
				<div class="offset-lg-4 col-lg-4">
					<div class="text-right">	
						<a href="forgetPass.php">Forget Password?</a>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="offset-lg-3 col-lg-6 col-xs-12">
					<button type="submit" class="btn btn-custom mt-4 btn-block rounded-pill">Login</button>
				</div>
			</div> 
			<div class="row mt-3">
				<div class="offset-lg-3 col-lg-6 col-xs-12">
					<p class="mt-3 font-weight-normal">Don't have an account ? <a href="signup.php" id="reg_link"><strong>Registre Now</strong></a></p>
				</div>
			</div> 
		</div> 
        </form>
		<footer class="footer text-center">
			© Copyright <a href="https://profile.intra.42.fr/users/aerragha" target="_blank">aerragha</a> 2019
		</footer>
    </body>
</html>
	
