<?php
session_start();

if (isset($_SESSION['SES_user'])) {
		$theUser = unserialize($_SESSION['SES_user']);
} else header("Location: index.php");

require( "config.php" );	//	vklucuvanje na bazata
		DB_CONN();
		
mysql_query("UPDATE `login` SET `status`='out' WHERE `id`=".$theUser['id']);

session_destroy();

header("Location: index.php");
?>