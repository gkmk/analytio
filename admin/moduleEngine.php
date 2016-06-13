<?php
require("../session_check.php");

if ($theUser['access'] < 10) header("Location: ../login.php"); 

require( "../config.php" );	//	vklucuvanje na bazata
DB_CONN();

$Module = $_POST['module'];

$res = mysql_query("SELECT * FROM `modules` WHERE `id`='".$Module."' LIMIT 1");
if ($res)
{
$row = mysql_fetch_assoc($res);

switch ($_POST['action']) {
	case "Check" :
		$moduleName = $SETTINGS['MODULE_DIR'].$row['Module'].'.jar';
		if (!file_exists($moduleName)) die("3");
		$modulePID = $SETTINGS['MODULE_DIR']."pidof-".$row['Module'].".txt";
		if (!file_exists($modulePID)) die("1");
		die("2");
	case "Start" :
		$moduleName = $SETTINGS['MODULE_DIR'].$row['Module'].'.jar';
		if (!file_exists($moduleName)) die("Module not found! Maybe module is still not uploaded?");
		$modulePID = $SETTINGS['MODULE_DIR']."pidof-".$row['Module'].".txt";

		$opts = $moduleName." & echo $! > ".$modulePID;
		//$jMod.runCom("/usr/bin/java -jar ". $opts);
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
		//if (pcntl_exec("/usr/bin/java -jar ". $opts) == false) die("Unable to start module!"); 

		//echo "Module Started!";
		//exec("touch ".$modulePID);
		/*$pid = pcntl_fork();
		if(!$pid) {
			exec("java -jar ". $moduleName." & echo $! > ".$modulePID);
			echo "Module Started!";
		}*/
		/*echo pcntl_exec ("java -jar ". $moduleName." & echo $! > ".$modulePID);
		echo "Module Started!";*/
		break;
	case "Stop":
		$modulePID = $SETTINGS['MODULE_DIR']."pidof-".$row['Module'].".txt";
		if (!file_exists($modulePID)) die("Module not started!".$modulePID);
		$PID = file_get_contents($modulePID);
		if (!unlink ($modulePID)) die("Unable to delete: ".$modulePID);
		if (!posix_kill(trim($PID), 9)) die("Unable to kill PID: ".$PID);
		echo "Module Stopped!";
		break;
}
} else {
	echo "Error: Module not found!";
}
?>

