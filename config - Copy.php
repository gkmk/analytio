<?php
//	root
$SETTINGS = array
(
	"USER_DIR" => "users", // do not change this value
	"TMP_UPLOAD" => "FULL_PATH_TO_MODULES_DIR"
);

function DB_CONN() {
	$con = mysql_connect("DB_HOST", "DB_USER", "DB_PASS");
			
	if (!$con) die(mysql_error());
		
	mysql_select_db("DB_NAME") or die(mysql_error());
	mysql_set_charset("utf8");
}
?>