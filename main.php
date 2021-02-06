<?php
	require_once("config/setup.php");
	require_once("header.php");
?>
<div class="Container" id="container">
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
		LIMIT 0, 5");

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
			echo '<div class="row mt-3">
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
										echo '<i class="fa fa-heart-o li" id="like_ic" data-id="' . $result["id_post"] . '"></i>';
									else
										echo '<i class="fa fa-heart li" id="like_ic" data-id="' . $result["id_post"] . '"></i>';
								}
								else
									echo '<a href="index.php"><i class="fa fa-heart-o" id="like_ic" data-id="' . $result["id_post"] . '"></i></a>';
							echo '<span class="like_com_txt ml-1"> Likes by <span class="count_like" id="like-' . $result["id_post"] . '">' . get_nb_like($result["id_post"], $cn) . '</span> presone</span>
						</div>
						<div class="col-lg-6 col-12 col-1">
							<i class="fa fa-comments-o" id="comment_ic"></i>
							<span class="like_com_txt ml-1"><span class="count_comment" id="cmt-' . $result["id_post"] . '">' . get_nb_comment($result["id_post"], $cn) . '</span> Comments</span>
						</div>
					</div>
				</div>
				<hr>';
				if (isset($_SESSION["id"]))
					echo '<div class="comm_zone" data-id="' . $result["id_post"] . '">
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
					echo '<div class="aff_cmt mb-2" style="display:none;" id="div_cmt-' . $result["id_post"] . '">';
				else
					echo '<div class="aff_cmt mb-2" id="div_cmt-' . $result["id_post"] . '">';
					echo '<table class="table-striped table_cmt" data-id="' . $result["id_post"] . '">';
					while ($result2 = $cmd2->fetch())
					{
						echo '<tr>
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
					echo '</table></div>';
			echo '</div> </div>';
		}
	?>
</div>
<script>
function like()
                {
                    var like_ic = document.getElementsByClassName("li");
                    for (var i =0; i < like_ic.length; i++)
					{
					like_ic[i].addEventListener("click", function(){
						var id_post = this.getAttribute('data-id');
						var nbr_like = document.getElementById('like-'+id_post);

						if (this.classList == "fa fa-heart li")
						{
							var xhttp = new XMLHttpRequest();
							var params = "id_post="+id_post;
							xhttp.open("POST", "unlike.php", true);
							xhttp.withCredentials = true;
							xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
							xhttp.onreadystatechange = function()
							{
								if (this.readyState == 4 && this.status == 200) 
								{
									nbr_like.innerHTML = this.responseText;
								}
							}
							xhttp.send(params);
							this.setAttribute("class", "fa fa-heart-o li");
						}
							
						else if (this.classList == "fa fa-heart-o li")
						{
							var xhttp = new XMLHttpRequest();
							var params = "id_post="+id_post;
							xhttp.open("POST", "like.php", true);
							xhttp.withCredentials = true;
							xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
							xhttp.onreadystatechange = function()
							{
								if (this.readyState == 4 && this.status == 200) 
								{
									nbr_like.innerHTML = this.responseText;
								}
							}
							xhttp.send(params);
							this.setAttribute("class", "fa fa-heart li");
						}
		
					})
				}   
                }
				
			function comment()
			{
				var btn_cmt = document.getElementsByClassName("btn_comm");
				for (var i = 0; i < btn_cmt.length; i++)
				{
					btn_cmt[i].addEventListener("click", function(e)
					{
						var id_post = this.getAttribute('data-id');
						var text_cmt = document.querySelector('textarea[data-id="' + id_post + '"]');
						
						if (text_cmt.value.trim() != "" && text_cmt.value.length <= 50)
						{
							var post_div = document.querySelector('table[data-id="' + id_post + '"]');
							var xhttp = new XMLHttpRequest();
							var params = "id_post="+id_post+"&text_cmt="+text_cmt.value;
							xhttp.open("POST", "comment.php", true);
							xhttp.withCredentials = true;
							xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
							xhttp.onreadystatechange = function()
							{
								if (this.readyState == 4 && this.status == 200) 
								{
									var nbr_cmt = document.getElementById('cmt-'+id_post);
									var div_cmt = document.getElementById('div_cmt-'+id_post);
									
									var res = this.responseText.split("&nbr_coment&");
									post_div.innerHTML = res[0] + post_div.innerHTML;
									nbr_cmt.innerHTML = res[1];
									div_cmt.style.display = "block";
								}
							}
							xhttp.send(params);	
						}
						else
							alert("Please Add a correct comment!");
						text_cmt.value = "";
						e.preventDefault();
					})
				}
			}
			like();
			comment();
			
			var limit_start = 5;
			var nb;
			window.addEventListener("scroll", function(){
				if (nb != "0")
				{
					var con = document.getElementById("container");
					if (window.scrollY + window.innerHeight >= con.clientHeight)
					{
						var xhttp = new XMLHttpRequest();
						var params = "limit_start="+limit_start;
						xhttp.open("POST", "get_posts.php", true);
						xhttp.withCredentials = true;
						xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
						xhttp.onreadystatechange = function()
						{
							if (this.readyState == 4 && this.status == 200) 
							{
								if (this.responseText == "0")
									nb = this.responseText;
								else
								{
									con.innerHTML += this.responseText;
									like();
									comment();
								}
							}
						}
						xhttp.send(params);
						limit_start += 5;
					}
				}
				
			})
	</script>
	<footer class="footer text-center">
			© Copyright <a href="https://profile.intra.42.fr/users/aerragha" target="_blank">aerragha</a> 2019
	</footer>
</body>
</html>
		