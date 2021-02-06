<?
	require_once("config/setup.php");
	session_start();
    if (isset($_SESSION["id"]))
		header("location: index.php");

	function check_if_exist($email, $cn)
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
	function check_if_active($email, $cn)
	{
		$cmd = $cn->prepare("SELECT isActif FROM USERS WHERE email = :email");
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
    
    function update_code($email, $code, $cn)
    {
        $cmd = $cn->prepare("UPDATE USERS SET forgetPass = :code WHERE email = :email");
		$cmd->bindParam(':email', $email);
		$cmd->bindParam(':code', $code);
		try
		{
			$cmd->execute();
		}
		catch (PDOException $ex)
		{
			echo 'query error!';
		}
    }

	if (isset($_POST["email"]))
	{
        $email = trim($_POST["email"]);
        if ($email != "" && filter_var($email, FILTER_VALIDATE_EMAIL))
		{
            if (check_if_exist($email, $cn) != 0)
            {
                if (check_if_active($email, $cn) == 1)
                {
                    $recoverCode = str_shuffle(hash('whirlpool', trim($email)));
                    update_code($email, $recoverCode, $cn);
                    $valid_sub = "Recover your Password!";
					$valid_msg = "Hello " . $_POST["user_name"] .
					"<br>To Recover your Password Click this link : <br> http://localhost/recoverPass.php?code=" . $recoverCode.
					"<br>Have a nice Day!";
					$headers =  "Content-Type: text/html". "\r\n";
                    mail($_POST["email"], $valid_sub, $valid_msg, $headers);
                    $succ_msg = "Password recovry sent to your email!";
                }
                else
                    $msg = "Your Account is inactive!";
            }
            else
                $msg = "Your email not found!";
        }
        else
            $msg = "Please enter a valid email!";
	}
	require_once("header.php");
?>
        <form name="frm" method="post">
        <div class="container text-center login-container">
			<div class="row mt-4 text-center">
				<div class="offset-lg-3 col-lg-6 col-xs-12">
					<h1 class="mb-5 font-weight-light text-uppercase forget">Forget Password</h1>
					<p class="msg_err"><?php if(isset($msg)) echo $msg; ?></p>
				</div>
			</div>
			<div class="row">
				<div class="offset-lg-3 col-lg-6 col-xs-12">
					<input type="email" class="form-control rounded-pill form-control-lg" name="email" pattern=".{6,100}" placeholder="Your Email" required>
				</div>
			</div>  
			<div class="row">
				<div class="offset-lg-3 col-lg-6 col-xs-12">
					<button type="submit" class="btn btn-custom mt-4 btn-block rounded-pill">Recover Password</button>
				</div>
			</div> 
			<div class="row mt-3">
				<div class="offset-lg-3 col-lg-6 col-xs-12">
					<p class="mt-3 font-weight-normal"><?php if(isset($succ_msg)) echo $succ_msg; ?></p>
				</div>
			</div> 
		</div> 
		</form>
		<footer class="footer text-center">
		© Copyright <a href="https://profile.intra.42.fr/users/aerragha" target="_blank">aerragha</a> 2019
	</footer>
    </body>
    
</html>
	
