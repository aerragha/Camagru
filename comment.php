<?php
    session_start();
    require_once("config/setup.php");
    if(isset($_SESSION["id"]))
    {
        if (isset($_POST["text_cmt"]) && isset($_POST["id_post"]))
        {
            if (trim($_POST["text_cmt"]) != "")
            {
                function get_nb_comment($id_post, $cn)
                {
                    $cmd = $cn->prepare("SELECT COUNT(id_post) FROM COMMENTS WHERE id_post = :id_post");
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
                
                function check_notification($id_post, $cn)
                {
                    $cmd = $cn->prepare("SELECT u.NotifActiv FROM USERS u, POSTS p
                    WHERE u.id_user = p.id_user
                    AND p.id_post = :id_post");
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
                function get_email($id_post, $cn)
                {
                    $cmd = $cn->prepare("SELECT u.email FROM USERS u, POSTS p
                    WHERE u.id_user = p.id_user
                    AND p.id_post = :id_post");
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
                $cmd = $cn->prepare("INSERT INTO COMMENTS(id_user, id_post, description, date_comment) VALUES (:id_user, :id_post, :description, NOW())");
                $cmd->bindParam(':id_user', $_SESSION["id"]);
                $cmd->bindParam(':id_post', $_POST["id_post"]);
                $cmd->bindParam(':description', trim($_POST["text_cmt"]));
                try
                {
                    $cmd->execute();
                }
                catch (PDOException $ex)
                {
                    echo 'query error!';
                }
                if (check_notification($_POST["id_post"], $cn) == 1)
                {
                    $valid_sub = "Your post have a new comment";
					$valid_msg = "<br> Hello <br>" .
					"Your post have a new comment from " . $_SESSION["username"] .
                    "<br>Have a nice Day!";
                    $email = get_email($_POST["id_post"], $cn);
                    $headers = "FROM : Camagru@gmail.com" . "\r\n";
					$headers .=  "Content-Type: text/html". "\r\n";
					mail($email, $valid_sub, $valid_msg, $email);
                }
                echo '<tr>
                        <td>
                            <div class="row">
                                <div class="col-lg-12 col-12  mt-1 ml-1">
                                    <img src="' . $_SESSION["image"] . '" class="user_cmt_img">
                                    <span class="user_cmt_name ml-1">' . $_SESSION["username"] . '</span>
                                    <br>
                                    <span class="user_cmt ml-4.5">
                                        ' . htmlspecialchars(trim($_POST["text_cmt"])) . '
                                    </span>
                                </div>
                            </div>
                        </td>
                    </tr>&nbr_coment&'. get_nb_comment($_POST["id_post"], $cn);
            }
        }
        else
        header('Location: index.php');
    }
    else
        header('Location: index.php');
?>
