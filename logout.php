<?php
    session_start();
    unset($_SESSION["id_user"]);
    unset($_SESSION["email"]);
    unset($_SESSION["username"]);
    unset($_SESSION["image"]);
    unset($_SESSION["first_name"]);
    unset($_SESSION["Last_name"]);
    unset($_SESSION["NotifActiv"]);
    session_destroy();
    header("location: index.php");
?>