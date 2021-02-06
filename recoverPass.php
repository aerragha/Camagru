<?
	require_once("config/setup.php");
	session_start();
    if (isset($_SESSION["id"]) || !isset($_GET["code"]))
        header("location: main.php");
     
    function check_code($recoverCode, $cn)
    {
        $cmd = $cn->prepare("SELECT COUNT(*) FROM USERS WHERE forgetPass = :forgetPass");
        $cmd->bindParam(':forgetPass', $recoverCode);
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
    function update_code($code, $cn)
    {
        $cmd = $cn->prepare("UPDATE USERS SET forgetPass = NULL WHERE forgetPass = :code");
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

    function update_password($pass, $code, $cn)
    {
        $cmd = $cn->prepare("UPDATE USERS SET pass = :pass WHERE forgetPass = :code");
		$cmd->bindParam(':pass', hash("whirlpool", $pass));
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

    if (check_code($_GET["code"], $cn) != 0)
    {
        $_SESSION["rec_code"] = $_GET["code"];
        $recoverCode = $_SESSION["rec_code"];
        if (isset($_POST["password"]) && isset($_POST["Confpassword"]))
        {
            $pass = $_POST["password"];
            $confpass = $_POST["Confpassword"];
            if ($pass == $confpass)
            {
                $uppercase = preg_match('/[A-Z]/', $pass);
                $lowercase = preg_match('/[a-z]/', $pass);
                $number    = preg_match('/[0-9]/', $pass);
                if (strlen($pass) >= 8 && strlen($pass) <= 100 && $uppercase && $lowercase && $number)
                {
                    update_password($pass, $recoverCode, $cn);
                    update_code($recoverCode, $cn);
                    $et = 1;
                    unset($_SESSION["rec_code"]);
                }
                else
                    $msg = "Password Format incorrect!!";
            }
            else
                $msg = "Password is not like Confirmation!!";
        }
    }
    else
        header("location: index.php");

    require_once("header.php");
?>
        <form name="frm" method="post">
        <div class="container text-center login-container">
			<div class="row mt-4 text-center">
				<div class="offset-lg-3 col-lg-6 col-xs-12">
					<h1 class="mb-5 font-weight-light text-uppercase forget">Recover Password</h1>
					<p class="msg_err"><?php if(isset($msg)) echo $msg; ?></p>
				</div>
			</div>
			<div class="row mt-4">
				<div class="offset-lg-3 col-lg-6 col-xs-12">
					<input type="password" class="form-control rounded-pill form-control-lg" name="password" pattern=".{6,100}" placeholder="Password" required>
				</div>
			</div>
			<br>
			<div class="row">
				<div class="offset-lg-3 col-lg-6 col-xs-12">
					<input type="password" class="form-control rounded-pill form-control-lg" name="Confpassword" pattern=".{6,100}" placeholder="Confirm Password" required>
				</div>
			</div>  
			<div class="row">
				<div class="offset-lg-3 col-lg-6 col-xs-12">
					<button type="submit" class="btn btn-custom mt-4 btn-block rounded-pill">Recover Password</button>
				</div>
			</div> 
		</div> 
        </form>
        <div class="active_div">
            <div class="container text-center activi-container">
                <div class="activi_txt">
                    <img src="https://png.pngtree.com/svg/20170608/5a142d709c.png">
                    <h2 class="mt-4">Your Password has been changed!<span id="email"></span> </h2>
                    <a href="index.php" class="btn btn-info mt-4">Login</a>
                </div>
            </div>
        </div>
    </body>
    <?php
  
     if ($et == 1)
       echo "<script>document.querySelector(\".active_div\").style.display = \"inline\";</script>"; 
     ?>
</html>
	
