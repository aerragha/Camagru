<?php
    session_start();
    require_once("config/setup.php");
		if (isset($_POST["limit_start"]))
        {
			if (is_numeric($_POST["limit_start"]))
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

			function get_if_like($id_post, $id_user, $cn)
			{
				$cmd = $cn->prepare("SELECT COUNT(*) FROM Likes WHERE id_post = :id_post AND id_user = :id_user");
				$cmd->bindParam(':id_post', $id_post);
				$cmd->bindParam(':id_user', $id_user);
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

        $cmd = $cn->prepare("SELECT u.id_user, u.image as 'u_image', u.username, p.id_post, p.image as 'p_image', p.date_creation 
		from POSTS p, USERS U
		WHERE p.id_user = u.id_user
		ORDER by p.id_post DESC
		LIMIT :limit_start, 5");

		$cmd->bindValue(':limit_start', (int)($_POST["limit_start"]), PDO::PARAM_INT);
		try
		{
			$cmd->execute();
		}
		catch (PDOException $ex)
		{
			echo 'query error!';
		}
		$ch = "";
		if ($cmd->rowCount() == 0)
			$ch = "0";
		else
		{
			while ($result = $cmd->fetch())
			{
				$ch .=  '<div class="row mt-3">
				<div class="offset-lg-3 col-lg-6 offset-1 col-10 offset-md-2 col-md-8 post_div">
					<div class="row">
					<div class="col-lg-9 col-9 col-md-8 mb-2">
						<img src="' . $result["u_image"] . '" class="user_img mb-1">
						<span class="user_name">' . $result["username"] . '</span>
					</div>
					<div class="col-lg-3 col-3 col-md-4">
						<span class="date_pub mt-2">' . $result["date_creation"] . '</span>
					</div>
				</div>
				<img src="' . $result["p_image"] . '" class="post_img">
				<div class="like_div mt-1 ml-1">
					<div class="row">
						<div class="col-lg-6 col-12 col-1">';
								if (isset($_SESSION["id"]))
								{
									if (get_if_like($result["id_post"], $_SESSION["id"], $cn) == 0)
                                        $ch .=  '<i class="fa fa-heart-o li" id="like_ic" data-id="' . $result["id_post"] . '"></i>';
									else
                                        $ch .= '<i class="fa fa-heart li" id="like_ic" data-id="' . $result["id_post"] . '"></i>';
								}
								else
                                    $ch .= '<a href="index.php"><i class="fa fa-heart-o" id="like_ic" data-id="' . $result["id_post"] . '"></i></a>';
                                $ch .= '<span class="like_com_txt ml-1"> Likes by <span class="count_like" id="like-' . $result["id_post"] . '">' . get_nb_like($result["id_post"], $cn) . '</span> presone</span>
						</div>
						<div class="col-lg-6 col-12 col-1">
							<i class="fa fa-comments-o" id="comment_ic"></i>
							<span class="like_com_txt ml-1"><span class="count_comment" id="cmt-' . $result["id_post"] . '">' . get_nb_comment($result["id_post"], $cn) . '</span> Comments</span>
						</div>
					</div>
				</div>
				<hr>';
				if (isset($_SESSION["id"]))
                    $ch .= '<div class="comm_zone">
							<form method="" action="">
								<div class="row">
									<div class="col-lg-10">
										<textarea rows="1" id="comm_txt" required name="comm_txt" maxlength="50" placeholder="Add a comment…" data-id="' . $result["id_post"] . '" class="form-control text_com" autocomplete="off" autocorrect="off" ></textarea>	
									</div>
									<div class="col-lg-2">
										<button class="btn btn-custom btn_comm" data-id="' . $result["id_post"] . '">Add</button>
									</div>
								</div>
							</form>
						</div>';
				$cmd2 = $cn->prepare("SELECT u.image, u.username, c.description FROM USERS u, COMMENTS c
				where u.id_user = c.id_user
				AND c.id_post = :id_post
				ORDER BY c.id_comment DESC");
				$cmd2->bindParam(':id_post', $result["id_post"]);
				$cmd2->execute();
				
				if (get_nb_comment($result["id_post"], $cn) == 0)
					$ch .= '<div class="aff_cmt mb-2" style="display:none;" id="div_cmt-' . $result["id_post"] . '">';
				else
					$ch .= '<div class="aff_cmt mb-2" id="div_cmt-' . $result["id_post"] . '">';
					$ch .= '<table class="table-striped table_cmt" data-id="' . $result["id_post"] . '">';
					while ($result2 = $cmd2->fetch())
					{
						$ch .= '<tr>
								<td>
										<div class="mt-1 ml-1">
											<img src="' . $result2["image"] . '" class="user_cmt_img">
											<span class="user_cmt_name ml-1">' . $result2["username"] . '</span>
											<br>
											<span class="user_cmt ml-4.5">
												' . htmlspecialchars($result2["description"]) . '
											</span>
										</div>
								</td>
							</tr>';
					}
					$ch .= '</table></div>';
				//}
                $ch .= '</div> </div>';
            }
            
		}
		echo $ch;
	}
	else
		echo "0";
    }
	else
		header("Location: index.php");
?>