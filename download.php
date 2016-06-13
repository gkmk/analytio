<?php
require("session_check.php");

if(empty($_POST['filename'])){
	exit;
}

$fileNs = $_POST['filename'];

require_once( "config.php" );
	global $theUser;
	
	if ($_SESSION['ASN'])	$fileName = $SETTINGS['USER_DIR']."/".$theUser['user']."/".$_SESSION['ASN']."/" .$fileNs;
	else $fileName = $SETTINGS['USER_DIR']."/".$theUser['user']."/" .$fileNs;
header("Cache-Control: ");
header("Content-type: text/plain");
header('Content-Disposition: attachment; filename="'.$fileNs.'"');

echo file_get_contents($fileName);

?>
