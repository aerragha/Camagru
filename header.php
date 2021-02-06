<?php
    require_once("config/setup.php");
    session_start();
?>

<html>
<head>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
		<link rel="stylesheet" href="css/style.css">
		<link href="https://fonts.googleapis.com/css?family=Poppins&display=swap" rel="stylesheet">
		<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Fredoka+One|Hammersmith+One&display=swap" rel="stylesheet">
        <link rel="icon" href="img/icon.png">
</head>
<body>
    <div class="header" id="myHeader">
				<div class="row">
					<div class="col-lg-6 col-md-4 col-sm-4 col-12">
							<a href="main.php" class="title_link"><p id="title">Camagru</p></a>
					</div>
					<div class="col-lg-6 col-md-8 col-sm-8 col-12 btns text-lg-right text-md-right text-sm-right">
                        <?php
                        if(isset($_SESSION["id"]))
                        {
                            echo '<ul>
                            <li ><a href="camera.php" class="btn btn-custom btns-si"><i class="fa fa-camera"></i> Camera</a></li>
                            <li class="dropdown">
                                    <a href="#" id="user" class="list"><span class="mr-2">Hi ' . $_SESSION["username"] . ' </span> <img src="' . $_SESSION["image"] . '" class="user_log_img mr-1"> <i class="fa fa-sort-desc"></i></a>
                                    <div class="dropdown-content text-left">
                                        <a href="profile.php"><i class="fa fa-user"></i> View Profile</a>
                                        <a href="edite_profile.php"><i class="fa fa-pencil-square-o"></i> Edit Profile</a>
                                        <a href="logout.php"><i class="fa fa-sign-out"></i> Logout</a>
                                    </div>
                            
                            </li>
                                 </ul>';
                        }
                        else
                        {
                            
                            echo '<ul>
                            <li class="mr-3"><a href="index.php" class="btn btn-custom btns-si"><i class="fa fa-sign-in"></i> Login</a></li>
                            <li><a href="signup.php" class="btn btn-custom btns-si"><i class="fa fa-user-plus"></i> Sign Up</a></li>
                                 </ul>';
                        }
                        ?>
							
					</div>
				</div>
    </div>
<script src="js/headerscript.js"></script>

