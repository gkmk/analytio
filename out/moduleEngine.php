<?php
/*require("../session_check.php");

if ($theUser['access'] < 10) header("Location: ../login.php"); 
*/
require( "../config.php" );	//	vklucuvanje na bazata
DB_CONN();

$Module = $_POST['module'];

$res = mysql_query("SELECT * FROM `modules` WHERE `id`='".$Module."' LIMIT 1");
if ($res)
{
$row = mysql_fetch_assoc($res);

switch ($_POST['action']) {
	case "Start" :
		$moduleName = $SETTINGS['MODULE_DIR'].$row['Module'].'.jar';
		if (!file_exists($moduleName)) die("Module not found! Maybe module is still not uploaded?");
		$modulePID = $SETTINGS['MODULE_DIR']."pidof-".$row['Module'].".txt";

		$opts = $moduleName." & echo $! > ".$modulePID;

		switch (pcntl_fork()) {
		  case 0:
			$cpid = pcntl_fork();
			if (!$cpid) exec("java -jar ". $opts, $out=array());
		   /* $cmd = "/usr/bin/java";
		    $args = array("jar ".$moduleName, "echo $! > ".$modulePID);
		    if (pcntl_exec($cmd, $args) == false) die("Unable to start module!"); */
		    // the child will only reach this point on exec failure,
		    // because execution shifts to the pcntl_exec()ed command
		    exit(0);
		  default:
		    break;
		}
		echo "Module Started!";
		break;
}
} else {
	echo "Error: Module not found!";
}
?>

