<?php
require_once("config/setup.php");
session_start();
if (!isset($_SESSION["id"]))
    header("location: index.php");
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
			<div class="row text-center">
				<div class="offset-lg-3 col-lg-6 offset-md-3 col-md-6 offset-sm-3 col-sm-6 offset-3 col-6">
					<a href="edite_profile.php" class="btn btn-light rounded text-uppercase mb-4"> Edit Profile</a>
				</div>
			</div>
		</div>
		<div class="container" id="container">
			<div class="row mt-4">
                <?php
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


                    $cmd = $cn->prepare("SELECT id_post, image FROM POSTS WHERE id_user = :id_user ORDER BY id_post DESC");
                    $cmd->bindParam(':id_user', $_SESSION["id"]);
                    try
                    {
                        $cmd->execute();
                    }
                    catch (PDOException $ex)
                    {
                        echo 'query error!';
                    }
                    while ($result = $cmd->fetch())
                    {
                        echo '<div class="col-lg-4 col-md-6 col-sm-12 col-12">
                            <div class="containere mt-2">
                                <img class="post_img_2" src="' . $result["image"] . '" style="width:100%;">
                                <div class="content">
                                    <div class="cmt_like_ic">
                                        <span class="mr-2"><i class="fa fa-heart" aria-hidden="true"></i> ' . get_nb_like($result["id_post"], $cn) . ' </span>
                                        <span><i class="fa fa-comment" aria-hidden="true"></i> ' . get_nb_comment($result["id_post"], $cn) . ' </span>
                                    </div>
                                </div>
                            </div> 
                        </div>';
                    }
                ?>
			</div>
        </div>
        <footer class="footer text-center">
			© Copyright <a href="https://profile.intra.42.fr/users/aerragha" target="_blank">aerragha</a> 2019
	</footer>
    </body>
</html>