<?php
//	root
$SETTINGS = array
(
	"USER_DIR" => "users",
	"MODULE_DIR" => "FULL_PATH_TO_MODULES_DIR"
);

function DB_CONN() {
	$con = mysql_connect("localhost", "root", "s@gitarius666");
			
	if (!$con) die(mysql_error());
		
	mysql_select_db("analytio") or die(mysql_error());
	mysql_set_charset("utf8");
}
?>
