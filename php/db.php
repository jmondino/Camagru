<?php

function create_table_users($conn) {
	$user_table = "users";
	try {
		$conn->query("CREATE TABLE IF NOT EXISTS $user_table (
                      id INT UNIQUE AUTO_INCREMENT PRIMARY KEY,
                      firstname VARCHAR(30) NOT NULL,
                      lastname VARCHAR(30) NOT NULL,
                      email VARCHAR(50) UNIQUE NOT NULL,
                      birth VARCHAR(30) NOT NULL,
                      age INT NOT NULL,
                      login VARCHAR(30) NOT NULL UNIQUE,
                      password VARCHAR(30) NOT NULL,
                      active INT NOT NULL DEFAULT 0,
											notif INT NOT NULL DEFAULT 1,
											token VARCHAR(50) DEFAULT NULL,
											recover VARCHAR(30) DEFAULT NULL,
                      reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");
	}
	catch(PDOException $e) {
		echo $user_table." failed:".$e->getMessage();
	}
}

function create_table_likes($conn) {
	$likes_table = "likes";
	try {
		$conn->query("CREATE TABLE IF NOT EXISTS $likes_table (
											id INT UNIQUE AUTO_INCREMENT PRIMARY KEY,
											pictureid INT NOT NULL,
											userid INT NOT NULL,
											FOREIGN KEY (userid) REFERENCES users(id),
											FOREIGN KEY (pictureid) REFERENCES pictures(id))");
	}
	catch(PDOException $e) {
		echo $picture_table." failed:".$e->getMessage();
	}
}

function create_table_photos($conn) {
	$picture_table = "pictures";
	try {
		$conn->query("CREATE TABLE IF NOT EXISTS $picture_table (
											id INT UNIQUE AUTO_INCREMENT PRIMARY KEY,
											title VARCHAR(50) DEFAULT NULL,
											description VARCHAR(150) DEFAULT NULL,
											link VARCHAR(100) NOT NULL,
											userid INT NOT NULL,
											FOREIGN KEY (userid) REFERENCES users(id))");
	}
	catch(PDOException $e) {
		echo $picture_table." failed:".$e->getMessage();
	}
}

function create_table_comments($conn) {
	$comments_table = "comments";
	try {
		$conn->query("CREATE TABLE IF NOT EXISTS $comments_table (
											id INT UNIQUE AUTO_INCREMENT PRIMARY KEY,
											comm VARCHAR(150) NOT NULL,
											userid INT NOT NULL,
											imgid INT NOT NULL,
											FOREIGN KEY (userid) REFERENCES users(id),
											FOREIGN KEY (imgid) REFERENCES pictures(id))");
	}
	catch(PDOException $e) {
		echo $comments_table." failed:".$e->getMessage();
	}
}

function make_query($query, $method) {
	global $conn;
	return $conn->$method($query);
}

$host = "localhost";
$port = "3306";
$dbname = "mydb";
$user = "camagru";
$paswd = "123456";
try {
	$conn = new PDO("mysql:host=$host", $user, $paswd);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$conn->query("CREATE DATABASE IF NOT EXISTS $dbname");
	$conn->query("use $dbname");
	create_table_users($conn);
	create_table_photos($conn);
	create_table_comments($conn);
	create_table_likes($conn);
}
catch(Exception $e) {
	echo "Error : ".$e->getMessage();
}
?>
