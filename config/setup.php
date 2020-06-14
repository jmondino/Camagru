<?php
require("database.php");

try {
	$conn = connect();
	create_table_users($conn);
	create_table_photos($conn);
	create_table_comments($conn);
	create_table_likes($conn);
}
catch(Exception $e) {
	echo "Error : ".$e->getMessage();
}
header("location: ../index.php");

?>
