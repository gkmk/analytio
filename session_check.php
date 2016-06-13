<?php
error_reporting(E_ERROR | E_PARSE);

session_start();
$theUser = array();
if (empty($_SESSION['SES_user'])) {
		header("Location: login.php");
	}
else {
	$theUser = unserialize($_SESSION['SES_user']);
}
?>
