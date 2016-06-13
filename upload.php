<?php
require("session_check.php");

require( "config.php" );	

$fn = (isset($_SERVER['HTTP_X_FILENAME']) ? $_SERVER['HTTP_X_FILENAME'] : false);

if ($fn) {

	// AJAX call
	$fileName = "";
	if ($_SESSION['ASN']) {
		$fileName = $SETTINGS['USER_DIR']."/".$theUser['user']."/".$_SESSION['ASN']."/" . $fn;
	} else {
		$fileName = $SETTINGS['USER_DIR']."/".$theUser['user']."/" . $fn;
		if ($_SESSION['FC']) $_SESSION['FC'] .= "|".$fn;
		else $_SESSION['FC'] = $fn;
	}
	if (copy('php://input', $fileName))
/*	if (file_put_contents(
		$fileName,
		file_get_contents('php://input')
	))*/
	echo "File uploaded!";
	else echo "$fn failed to upload!";
	exit();

}
else {

	// form submit
	$files = $_FILES['fileselect'];

	foreach ($files['error'] as $id => $err) {
		if ($err == UPLOAD_ERR_OK) {
			$fn = $files['name'][$id];
			
			$fileName = "";
			if ($_SESSION['ASN']) {
				$fileName = $SETTINGS['USER_DIR']."/".$theUser['user']."/".$_SESSION['ASN']."/" . $fn;
			} else {
				$fileName = $SETTINGS['USER_DIR']."/".$theUser['user']."/" . $fn;
				if ($_SESSION['FC']) $_SESSION['FC'] .= "|".$fn;
				else $_SESSION['FC'] = $fn;
			}
			if (move_uploaded_file(
				$files['tmp_name'][$id],
				$fileName
			))
			echo "File uploaded!";
			else echo "$fn failed to upload!";
		}
	}

}