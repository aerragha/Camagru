<?php
    session_start();
    require_once("config/setup.php");
    if(isset($_SESSION["id"]))
    {
        if (isset($_POST["id_post"]))
        {
            function get_nb_like($id_post, $cn)
            {
                $cmd = $cn->prepare("SELECT COUNT(id_post) FROM Likes WHERE id_post = :id_post");
                $cmd->bindParam(':id_post', $id_post);
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

            $cmd = $cn->prepare("DELETE FROM Likes WHERE id_post = :id_post AND id_user = :id_user");
            $cmd->bindParam(':id_user', $_SESSION["id"]);
            $cmd->bindParam(':id_post', $_POST["id_post"]);
            try
            {
                $cmd->execute();
            }
            catch (PDOException $ex)
            {
                echo 'query error!';
            }

            echo get_nb_like($_POST["id_post"], $cn);
        }
        else
			header("Location: index.php");
    }
    else
        header("Location: index.php");
?>