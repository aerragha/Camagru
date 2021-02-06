<?php

	require_once("database.php");

	try
	{
		$cn = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
		$cn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$cmd = $cn->prepare("CREATE TABLE IF NOT EXISTS `USERS` (
			id_user int AUTO_INCREMENT PRIMARY KEY NOT NULL,
			first_name varchar(255) NOT NULL,
			Last_name varchar(255) NOT NULL,
			username varchar(255) NOT NULL,
			email varchar(255) NOT NULL,
			pass varchar(255) NOT NULL,
			image varchar(255) DEFAULT 'users_img/default.png' NOT NULL,
			isActif bit DEFAULT 0,
			activationCode varchar(255),
			NotifActiv bit DEFAULT 1,
			forgetPass varchar(255)
		);");

		$cmd->execute();

		$cmd = $cn->prepare("CREATE TABLE IF NOT EXISTS `POSTS` (
			id_post int AUTO_INCREMENT PRIMARY KEY NOT NULL,
			id_user int,
			FOREIGN KEY(id_user) REFERENCES USERS(id_user) ON DELETE CASCADE,
			image varchar(255),
			date_creation date,
			isActif bit DEFAULT 1
		);");

		$cmd->execute();

		$cmd = $cn->prepare("CREATE TABLE IF NOT EXISTS `COMMENTS` (
			id_comment int AUTO_INCREMENT PRIMARY KEY NOT NULL,
			id_user int,
			id_post int,
			FOREIGN KEY(id_user) REFERENCES USERS(id_user) ON DELETE CASCADE,
			FOREIGN KEY(id_post) REFERENCES POSTS(id_post) ON DELETE CASCADE,
			description varchar(255) NOT NULL,
			date_comment date
		);");

		$cmd->execute();

		$cmd = $cn->prepare("CREATE TABLE IF NOT EXISTS `Likes` (
			id_user int,
			id_post int,
			FOREIGN KEY(id_user) REFERENCES USERS(id_user) ON DELETE CASCADE,
			FOREIGN KEY(id_post) REFERENCES POSTS(id_post) ON DELETE CASCADE,
			PRIMARY KEY (id_user, id_post)
		);");

		$cmd->execute();

		
	}
	catch (PDOException $ex)
	{
		echo "Connection failed: " . $ex->getMessage();
	}
?>
