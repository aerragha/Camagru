<?php
require_once("config/setup.php");
session_start();
if (!isset($_SESSION["id"]))
    header("location: index.php");

    function logout()
    {
        unset($_SESSION["id_user"]);
        unset($_SESSION["email"]);
        unset($_SESSION["username"]);
        unset($_SESSION["image"]);
        unset($_SESSION["first_name"]);
        unset($_SESSION["Last_name"]);
        unset($_SESSION["NotifActiv"]);
        session_destroy();
    }
    
    function update_session($cn)
    {
        $cmd = $cn->prepare("SELECT * FROM USERS WHERE id_user = :id_user");
        $cmd->bindParam(':id_user', $_SESSION["id"]);
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
            $_SESSION["email"] = $result["email"];
            $_SESSION["username"] = $result["username"];
            $_SESSION["image"] = $result["image"];
            $_SESSION["first_name"] = $result["first_name"];
            $_SESSION["Last_name"] = $result["Last_name"];
            $_SESSION["NotifActiv"] = $result["NotifActiv"];
        }    
    }

    function update_infos($first_name, $last_name, $user_name, $email, $notif, $cn)
    {
        $cmd = $cn->prepare("UPDATE USERS 
        SET first_name = :first_name, Last_name = :Last_name, username = :username, email = :email, NotifActiv = :NotifActiv  
        WHERE id_user = :id_user");
		$cmd->bindParam(':first_name', $first_name);
		$cmd->bindParam(':Last_name', $last_name );
		$cmd->bindParam(':username', $user_name);
		$cmd->bindParam(':email', $email);
		$cmd->bindParam(':NotifActiv', $notif, PDO::PARAM_INT);
        $cmd->bindParam(':id_user', $_SESSION["id"]);
        try
        {
            $cmd->execute();
        }
        catch (PDOException $ex)
        {
            echo 'query error!';
        }
        if ($email != $_SESSION["email"])
        {
            $ValidateCode = hash('whirlpool', trim($email));
            $valid_sub = "Activate your account!";
			$valid_msg = "Hello " . $user_name .
			"<br>You Edite your email<br>" .
			"To Active your account Click this link : <br> http://localhost/validateAccount.php?code=" . $ValidateCode .
            "<br>Have a nice Day!";
            $headers = "FROM : Camagru@gmail.com" . "\r\n";
			$headers .=  "Content-Type: text/html". "\r\n";
            mail($email, $valid_sub, $valid_msg, $headers);
            $cmd = $cn->prepare("UPDATE USERS SET activationCode = :activationCode, isActif = 0
            WHERE id_user = :id_user");
            $cmd->bindParam(':activationCode', $ValidateCode);
            $cmd->bindParam(':id_user', $_SESSION["id"]);
            try
            {
                $cmd->execute();
            }
            catch (PDOException $ex)
            {
                echo 'query error!';
            }
            logout();
            $GLOBALS["affich"] = 1;
        }
    }

    function update_password($New_password, $cn)
    {
        $cmd = $cn->prepare("UPDATE USERS 
        SET pass = :pass 
        WHERE id_user = :id_user");
        $cmd->bindParam(':id_user', $_SESSION["id"]);
        $cmd->bindParam(':pass', hash('whirlpool', $New_password));
        try
        {
            $cmd->execute();
        }
        catch (PDOException $ex)
        {
            echo 'query error!';
        }
    }

    function update_img($image, $cn)
    {
        $cmd = $cn->prepare("UPDATE USERS SET image = :image 
        WHERE id_user = :id_user");
        $cmd->bindParam(':id_user', $_SESSION["id"]);
        $cmd->bindParam(':image', $image);
        try
        {
            $cmd->execute();
        }
        catch (PDOException $ex)
        {
            echo 'query error!';
        }
    }

    function check_format_input($email, $first_name, $last_name, $user_name)
	{
		if(!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 150)
			return "Email Format incorrect!";
		if(!preg_match("/^[a-zA-Z ]{2,20}$/", $first_name))
			return "Firstname Format incorrect!";
		if (!preg_match("/^[a-zA-Z ]{2,20}$/", $last_name)) 
			return "Lastname Format incorrect!";
		if(!preg_match("/^[a-zA-Z0-9]{2,20}$/", $user_name))
			return "Username Format incorrect!";
		return "Done";
    }
    
    function check_email($email, $cn)
	{
		$cmd = $cn->prepare("SELECT COUNT(*) FROM USERS WHERE email = :email AND id_user != :id_user");
		$cmd->bindParam(':email', $email);
        $cmd->bindParam(':id_user', $_SESSION["id"]);
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
		$cmd = $cn->prepare("SELECT COUNT(*) FROM USERS WHERE username = :username AND id_user != :id_user");
        $cmd->bindParam(':username', $user_name);
        $cmd->bindParam(':id_user', $_SESSION["id"]);
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
    function check_pass($pass, $cn)
    {
        $cmd = $cn->prepare("SELECT COUNT(*) FROM USERS WHERE pass = :pass AND id_user = :id_user");
        $cmd->bindParam(':pass', hash('whirlpool', $pass));
        $cmd->bindParam(':id_user', $_SESSION["id"]);
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

    function check_image()
    {
        if (!in_array($_FILES['image']['type'], array("image/png", "image/jpeg", "image/gif")))
            return "Please choose an image!";
        else if ($_FILES['image']['size'] > 2000000)
            return "The Image is too big!";
        else
            return "Done";
    }
    
    function random_string() {
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $chars_len = strlen($chars) - 1;
        $ch = "";
        for ($i = 0; $i < 15; $i++) {
            $ch .= $chars[rand(0, $chars_len - 1)];
        }
        return $ch;
    }

    if (isset($_POST["first_name"]) && isset($_POST['last_name']) && isset($_POST["user_name"]) && 
    isset($_POST["email"]))
    {
        $GLOBALS["affich"] = 0;
        $first_name = trim($_POST["first_name"]);
		$last_name = trim($_POST["last_name"]);
		$user_name = trim($_POST["user_name"]);
		$email = trim($_POST["email"]);

        if (empty($_POST["notif"]))
            $notif = 0;
        else
            $notif = 1;

        $err = check_format_input($email, $first_name, $last_name, $user_name);
        if ($err == "Done")
        {
            if (check_email($email, $cn) != 0)
                $msg = "This Email already exists!";
            else if (check_username($user_name, $cn) != 0)
                $msg = "This username already exists!";
            else
            {
                if ($_POST["Cur_password"] != "" && $_POST["New_password"] != "")
                {
                    $Cur_password = $_POST["Cur_password"];
                    $New_password = $_POST["New_password"];
                    $uppercase = preg_match('/[A-Z]/', $New_password);
                    $lowercase = preg_match('/[a-z]/', $New_password);
                    $number    = preg_match('/[0-9]/', $New_password);
                    if (strlen($New_password) < 8 || strlen($New_password) > 100 || !$uppercase || !$lowercase || !$number)
                        $msg = "New Password Format incorrect!!";
                    else if (check_pass($Cur_password, $cn) == 0)
                        $msg = "Current Password incorrect!";
                    else
                    {
                        update_password($New_password, $cn);
                        update_infos($first_name, $last_name, $user_name, $email, $notif, $cn);
                        $msg_succ = "Your profile update was successful";
                    }
                }
                else if ($_FILES['image']['name'] != "")
                {
                    $imag_err = check_image();
                    if ($imag_err == "Done")
                    {
                        $path = "users_img/";
                        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                        $name = $path . random_string() . '.' . $ext;
                        if (move_uploaded_file($_FILES['image']['tmp_name'], $name))
                        {
                            list($img_Width, $img_Height) = getimagesize($name);
                            if (isset($img_Width) && isset($img_Height))
                                update_img($name, $cn);
                        }
                        update_infos($first_name, $last_name, $user_name, $email, $notif, $cn);
                        $msg_succ = "Your profile update was successful";
                    }
                    else
                        $msg = $imag_err;
                }
                else
                {
                    update_infos($first_name, $last_name, $user_name, $email, $notif, $cn);
                    $msg_succ = "Your profile update was successful";
                }
                update_session($cn);
            }
        }
        else
            $msg = $err;
    }
    
require_once("header.php");
?>

<div class="prof_content">
			<div class="row text-center">
				<div class="offset-lg-3 col-lg-6 offset-md-3 col-md-6 offset-sm-3 col-sm-6 offset-3 col-6">
					<img src="<? echo $_SESSION['image'] ?>" class="usr_profil_img mt-5">
				</div>
			</div>
			<div class="row text-center">
				<div class="offset-lg-3 col-lg-6 offset-md-3 col-md-6 offset-sm-3 col-sm-6 offset-3 col-6">
					<h1 class="user_name_txt text-uppercase mt-2 mb-2"><? echo $_SESSION['first_name'] . " " . $_SESSION['Last_name']; ?></h1>
				</div>
			</div>
        </div>
        
        <div class="container mt-5">
			<form name="frm" method="post" enctype="multipart/form-data">
                <div class="row mt-4 text-center">
                    <div class="offset-lg-3 col-lg-6 col-xs-12">
                        <h1 class="mb-3 font-weight-light text-uppercase tit">Edit Profile</h1>
                        <p class="msg_err"><?php if(isset($msg)) echo $msg; ?></p>
                        <p class="msg_succ"><?php if(isset($msg_succ)) echo $msg_succ; ?></p>
                    </div>
                </div>
                <div class="row text-center">
                    <div class="offset-lg-1 col-lg-5 offset-md-1 col-md-5 col-sm-12 col-12 mt-2">
                        <input type="text" class="form-control rounded" placeholder="First name" name="first_name" value="<? echo $_SESSION["first_name"] ?>" pattern="[a-zA-Z ]{2,20}" required>
                    </div>
                    <div class="col-lg-5 col-md-5  col-sm-12  col-12 mt-2">
                        <input type="text" class="form-control rounded" placeholder="Last name" name="last_name" value="<? echo $_SESSION["Last_name"] ?>" pattern="[a-zA-Z]{2,20}" required>
                    </div>
                </div>
                <div class="row text-center">
                        <div class="offset-lg-1 col-lg-5 offset-md-1 col-md-5 col-sm-12 col-12 mt-2">
                                <input type="text" class="form-control form-control-lg" placeholder="User Name" name="user_name" value="<? echo $_SESSION["username"] ?>" pattern="[a-zA-Z0-9]{2,20}" required>
                        </div>
                        <div class="col-lg-5 col-md-5  col-sm-12  col-12 mt-2">
                                <input type="email" class="form-control form-control-lg" placeholder="Email" name="email" value="<? echo $_SESSION["email"] ?>" required>
                        </div>
                    </div>
                    <div class="row text-center">
                            <div class="offset-lg-1 col-lg-5 offset-md-1 col-md-5 col-sm-12 col-12 mt-2">
                                    <input type="password" class="form-control form-control-lg" placeholder="Current Password" name="Cur_password">
                            </div>
                            <div class="col-lg-5 col-md-5  col-sm-12  col-12 mt-2">
                                    <input type="password" class="form-control form-control-lg" placeholder="New Password" name="New_password">
                            </div>
                    </div>
                    <div class="row text-center">
                            <div class="offset-lg-1 col-lg-5 offset-md-1 col-md-5 col-sm-12 col-12 mt-2">
                                <input type="file" name="image" id="image" accept="image/*" class="form-control">
                            </div>
                            <div class="col-lg-5 col-md-5 col-sm-12 col-12 mt-2 p-3">
                            <input type="checkbox" name="notif" id="notif" <? if($_SESSION["NotifActiv"] == 1) echo "checked" ?> > <label for="notif" class="ml-2 mt-1"> Notifications</label>
                            </div>
                    </div>
                    <div class="row text-center">
                        <div class="offset-lg-4 col-lg-4 offset-md-4 col-md-4 col-sm-12  col-12 mt-2 ">
                            <input type="submit" class="btn btn-custom btn-block" name="submit" value="Edit Profile" id="edit_btn">
                        </div>
                    </div>
            </form>
        </div>
        <div class="active_div">
            <div class="container text-center activi-container">
                <div class="activi_txt">
                    
                    <img src="https://www.witopia.com/wp-content/uploads/flat-email-icon.png">
                    <h2 class="mt-4">You need to confirm your new email <span id="email"></span> </h2>
                    <a href="index.php" class="btn btn-info mt-4">Login</a>
                </div>
            </div>
        </div>
        <footer class="footer text-center">
			© Copyright <a href="https://profile.intra.42.fr/users/aerragha" target="_blank">aerragha</a> 2019
		</footer>
    </body>
</html>
    <?php
    if ($GLOBALS["affich"] == 1)
	{
		echo "<script>document.querySelector(\".active_div\").style.display = \"inline\";</script>";
	}
    ?>
