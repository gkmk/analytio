<?php
require("session_check.php");

require( "config.php" );	//	vklucuvanje na bazata

if (isset($_GET['ID']))
{
	DB_CONN();
	if (!isset($_POST['comment']) || empty($_POST['comment'])) die("No comment posted!");
	if (!is_numeric($_GET['ID'])) die("No module selected!"); 
	
	$ID = mysql_real_escape_string($_GET['ID']);
	$comment = mysql_real_escape_string($_POST['comment']);
	
	$res = mysql_query("UPDATE `session` SET `comment`='".$comment."' WHERE `id`='".$ID."'");
	
	if (!$res) die(mysql_error());
	die("Session updated successfully!");
}
?>