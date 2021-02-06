<?
    require_once("config/setup.php");
    session_start();
    if (isset($_SESSION["id"]) || !isset($_GET["code"]))
        header("location: index.php");

        function check_code($activationCode, $cn)
        {
            $cmd = $cn->prepare("SELECT COUNT(*) FROM USERS WHERE activationCode = :activationCode");
            $cmd->bindParam(':activationCode', $activationCode);
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
    
        function check_isActive($activationCode, $cn)
        {
            $cmd = $cn->prepare("SELECT isActif FROM USERS WHERE activationCode = :activationCode");
            $cmd->bindParam(':activationCode', $activationCode);
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

        $activationCode = $_GET["code"];
        if (check_code($activationCode, $cn) != 0)
        {
            if (check_isActive($activationCode, $cn) == 0)
            {
                $cmd = $cn->prepare("UPDATE USERS SET isActif = 1, activationCode = NULL WHERE activationCode = :activationCode");
                $cmd->bindParam(':activationCode', $activationCode);
                try
                {
                    $cmd->execute();
                }
                catch (PDOException $ex)
                {
                    echo 'query error!';
                }
                $hea = 0;
            }
            else
                $hea = 1;
                
        }
        else
            header("location: index.php");
    require_once("header.php");
?>
<div class="container text-center">
    <div>
            <div class="container text-center activi-container">
                <div class="activi_txt">
                    <div id="activer">
                        <img src="https://png.pngtree.com/svg/20170608/5a142d709c.png">
                        <h2 class="mt-4">Your Account is Active Now</h2>
                    </div>
                    <div id="deja_activer">
                        <img src="https://i.pinimg.com/originals/36/a8/ab/36a8abc485bb6b74e36d476710b4c3ac.png">
                        <h2 class="mt-4">Your Account is already Active !!</h2>
                    </div>
                    <a href="index.php" class="btn btn-info mt-4">Login</a>
                </div>
            </div>
    </div>
</div>

<?php
    if ($hea == 0)
        echo "<script>document.querySelector(\"#activer\").style.display = \"inline\";</script>";
    else
        echo "<script>document.querySelector(\"#deja_activer\").style.display = \"inline\";</script>";
?>