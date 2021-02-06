<?php
session_start();
require_once("config/setup.php");
if(isset($_SESSION["id"]))
{
	if (isset($_POST["imgUrl"]) && isset($_POST["effect"]) && isset($_POST["coord_x"]) && isset($_POST["coord_y"]))
	{
		if (in_array($_POST["effect"], array("effect/cat.png", "effect/emoji.png", "effect/lion.png")))
		{
			if ($_POST["coord_x"] >= 10 && $_POST["coord_y"] >= 0 && $_POST["coord_y"] <= 190)
			{
				$img = $_POST["imgUrl"];
				$img = str_replace('data:image/png;base64,', '', $img);
				$img = str_replace(' ', '+', $img);
				$post_img = base64_decode($img);
				$file = "posts_img/" . uniqid() . '.png';
				if (!file_exists("posts_img/"))
					mkdir("posts_img/");
				file_put_contents($file, $post_img);

				$dest = imagecreatefrompng($file);
				$src = imagecreatefrompng($_POST["effect"]);

				list($src_Width, $src_Height) = getimagesize($_POST["effect"]);
				if (isset($src_Width) && isset($src_Height))
				{
					$src_xPosition = $_POST["coord_x"];
					$src_yPosition = $_POST["coord_y"];

					$src_cropXposition = 0;
					$src_cropYposition = 0;

					imagecopy($dest, $src, $src_xPosition, $src_yPosition, $src_cropXposition, $src_cropYposition, $src_Width, $src_Height);

					imagejpeg($dest, $file, 100);

					$cmd = $cn->prepare("INSERT INTO POSTS(id_user, image, date_creation) 
						VALUES (:id_user, :image, NOW())");
					$cmd->bindParam(':id_user', $_SESSION["id"]);
					$cmd->bindParam(':image', $file);
					try
					{
						$cmd->execute();
					}
					catch (PDOException $ex)
					{
						echo 'query error!';
					}

					echo '<div class="containere mt-2">
						<img class="post_img_2" src="' . $file . '" alt="Notebook" style="width:100%;">
						<div class="content">
						<div class="cmt_like_ic">
						<span class="mr-2"><i class="fa fa-heart" aria-hidden="true"></i> 0 </span>
						<span><i class="fa fa-comment" aria-hidden="true"></i> 0 </span> <br>
						<a class="delete" href="camera.php?post=' . $file . '"><span><i class="fa fa-trash" aria-hidden="true"></i> Delete </span></a>
						</div>
						</div>
						</div> ';
				}
			}
		}
	}
	else
		header("Location: index.php");
}
else
	header('Location: index.php');
?>
