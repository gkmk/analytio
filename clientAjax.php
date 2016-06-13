<?php
require("session_check.php");
//-----------------------------------------------------------------


function SetActiveModule($params)
{
	global $theUser;
	require( "config.php" );	//	vklucuvanje na bazata
	DB_CONN();
	mysql_query("UPDATE `login` SET `status`='loggedin', `test` = '0', `log` = '".time()."' WHERE `id`=".$theUser['id']);
	$Name = mysql_real_escape_string($params[0]);
	$Modul = mysql_real_escape_string($params[1]);
	
	$res = mysql_query("SELECT * FROM `session` WHERE `name`='$Name' AND `link`='".$theUser['id']."'");
	
	if (mysql_num_rows($res) < 1) die("No session selected!");
	else {
		$tmp = mysql_fetch_assoc($res);
		if (!empty($tmp['modul'])) die("There is a module already attached to this session! Create new session to select a new module.");
	}
	if (mysql_query("UPDATE `session` SET `modul`='$Modul' WHERE `name`='$Name' AND `link`='".$theUser['id']."'")) {
		$_SESSION['ASN'] = $Name;
		die("Module activated!");
	}
	else die( mysql_error() );
}
//-----------------------------------------------------------------

function SaveSession($Name)
{
	global $theUser;
	require( "config.php" );	//	vklucuvanje na bazata
	DB_CONN();
	mysql_query("UPDATE `login` SET `status`='loggedin', `test` = '0', `log` = '".time()."' WHERE `id`=".$theUser['id']);
	$Name = mysql_real_escape_string($Name);
	
	$res = mysql_query("SELECT * FROM `session` WHERE `name`='$Name' AND `link`='".$theUser['id']."'");
	
	if (mysql_num_rows($res) > 0) die("Input name for session (which is not existing in the database)");
	
	if (!is_dir($SETTINGS['USER_DIR']."/".$theUser['user']."/".$Name))
	if (!mkdir($SETTINGS['USER_DIR']."/".$theUser['user']."/".$Name)) die("Unable to create the new session!");
	
	if (mysql_query("INSERT INTO `session` (`name`, `link`) VALUES ('$Name', '".$theUser['id']."')")) {
		$_SESSION['ASN'] = $Name;
		$res = mysql_query("SELECT `id` FROM `session` WHERE `name`='$Name' AND `link`='".$theUser['id']."'");
		$rez = mysql_fetch_row($res);
		$_SESSION['ASID'] = $rez[0];
		if ($_SESSION['FC']) {
			$copy_files = explode("|", $_SESSION['FC']);
			foreach ($copy_files as $file) {
				$src =  $SETTINGS['USER_DIR']."/".$theUser['user']."/" .$file;
				$dst = $SETTINGS['USER_DIR']."/".$theUser['user']."/".$Name."/" .$file;
				copy ($src, $dst);
				unlink($src);
			}
			$_SESSION['FC']="";
			unset($_SESSION['FC']);
		}
		
		die($rez[0]);
	}
	else die( mysql_error() );
}
//-----------------------------------------------------------------

function UpdateSession($params)
{
	global $theUser;
	require( "config.php" );	//	vklucuvanje na bazata
	DB_CONN();
	mysql_query("UPDATE `login` SET `status`='loggedin', `test` = '0', `log` = '".time()."' WHERE `id`=".$theUser['id']);
	$Name = mysql_real_escape_string($params[0]);
	$Modul = mysql_real_escape_string($params[1]);
	$Files = mysql_real_escape_string($params[2]);
	
	$res = mysql_query("SELECT * FROM `session` WHERE `name`='$Name' AND `link`='".$theUser['id']."'");
	
	if (mysql_num_rows($res) < 1) die( "Session $Name doesn\'t exist!");
	
	$tmprz = mysql_fetch_assoc($res);
	
	$_SESSION['ASID'] = $tmprz['id'];
	if (mysql_query("UPDATE `session` SET `modul`='$Modul', `files`='$Files' WHERE `name`='$Name' AND `link`='".$theUser['id']."'")) {
		$_SESSION['ASN'] = $Name;
		die( "Session Saved!");
	}
	else die( mysql_error());
	
}
//-----------------------------------------------------------------

function LoadSession($ID)
{
	global $theUser;
	require( "config.php" );	//	vklucuvanje na bazata
	DB_CONN();
	mysql_query("UPDATE `login` SET `status`='loggedin', `test` = '0', `log` = '".time()."' WHERE `id`=".$theUser['id']);
	$ID = mysql_real_escape_string($ID);
	
	$res = mysql_query("SELECT * FROM `session` WHERE `id`='".$ID."'");
	if (mysql_num_rows($res) < 1) die( "Invalid Session!");
	$rezS = mysql_fetch_assoc($res);
	
	$_SESSION['ASID'] = $ID;
	
	$resM = mysql_query("SELECT * FROM `modules` WHERE `id`='".$rezS['modul']."'");
	if (mysql_num_rows($resM) < 1) {
		$resM = mysql_query("SELECT * FROM `modules` WHERE `link`='".$theUser['id']."'");
	}
	$tmpM = array();
	while ($rezM = mysql_fetch_assoc($resM)) $tmpM[] = $rezM;
	$_SESSION['ASN'] = $rezS['name'];
	$out = array($rezS, $tmpM);
	echo json_encode($out);
}
//-----------------------------------------------------------------

function SaveFileDB($params)
{
	global $theUser;
	require( "config.php" );	//	vklucuvanje na bazata
	DB_CONN();
	mysql_query("UPDATE `login` SET `status`='loggedin', `test` = '0', `log` = '".time()."' WHERE `id`=".$theUser['id']);
	$Name = mysql_real_escape_string($params[0]);
	$File = mysql_real_escape_string($params[1]);
	
	$res = mysql_query("SELECT * FROM `session` WHERE `name`='$Name' AND `link`='".$theUser['id']."'");
	
	if (mysql_num_rows($res) < 1) die( "The session is not saved yet !");
	
	$rez = mysql_fetch_assoc($res);
	if ($rez['files'] != "") {
		if (preg_match("/".$File."/i", $rez['files']) == 0)
			$Files = $rez['files'].",".$File;
		else die("File already saved in database.");
	}
	else $Files = $File;
	
	if (mysql_query("UPDATE `session` SET `modul`='$Modul', `files`='$Files' WHERE `name`='$Name' AND `link`='".$theUser['id']."'")) {
		$_SESSION['ASN'] = $Name;
		die( "File saved!");
	}
	else die( mysql_error());
}
//-----------------------------------------------------------------

function DelFileDB($params)
{
	require( "config.php" );	//	vklucuvanje na bazata
	global $theUser;
	DB_CONN();
	mysql_query("UPDATE `login` SET `status`='loggedin', `test` = '0', `log` = '".time()."' WHERE `id`=".$theUser['id']);
	$Name = mysql_real_escape_string($params[0]);
	$File = mysql_real_escape_string($params[1]);
	
	$res = mysql_query("SELECT * FROM `session` WHERE `name`='$Name' AND `link`='".$theUser['id']."'");
	
	if (mysql_num_rows($res) < 1) die( "File is not saved yet / The session is not saved yet !");
	
	$rez = mysql_fetch_assoc($res);
	$Files = $rez['files'];

	$FilesT = preg_replace("/".$File.",/i", "", $Files);
	if ($Files == $FilesT) $FilesT = preg_replace("/,".$File."/i", "", $Files);
	if ($Files == $FilesT) $FilesT = preg_replace("/".$File."/i", "", $Files);

	if ($Files == $FilesT) die("The file is not saved at all / Invalid file!");
	
	if (mysql_query("UPDATE `session` SET `modul`='$Modul', `files`='$FilesT' WHERE `name`='$Name' AND `link`='".$theUser['id']."'")) {
		$_SESSION['ASN'] = $Name;
		if (file_exists($SETTINGS['USER_DIR']."/".$theUser['user']."/".$_SESSION['ASN']."/" .$File))
			unlink($SETTINGS['USER_DIR']."/".$theUser['user']."/".$_SESSION['ASN']."/" .$File);
		mysql_query("UPDATE `session` SET `lastFile`='' WHERE `id`='".$_SESSION['ASID']."'");
		$other = array(".res", ".wrk", ".xml", "PIE.xml", "RESULT.xml", substr($File, 0, strlen($File)-4)."-patrec.csv" );
		foreach($other as $probFile) {
			if ($probFile == substr($File, 0, strlen($File)-4)."-patrec.csv" &&
			  file_exists($SETTINGS['USER_DIR']."/".$theUser['user']."/".$_SESSION['ASN']."/" .substr($File, 0, strlen($File)-4)."-patrec.csv"))
			  {
				  unlink($SETTINGS['USER_DIR']."/".$theUser['user']."/".$_SESSION['ASN']."/" .substr($File, 0, strlen($File)-4)."-patrec.csv");
			  }
			else if (file_exists($SETTINGS['USER_DIR']."/".$theUser['user']."/".$_SESSION['ASN']."/" .$File.$probFile))
			{
				 unlink($SETTINGS['USER_DIR']."/".$theUser['user']."/".$_SESSION['ASN']."/" .$File.$probFile);
			}
		}
		die( "File removed!");
	}
	else die( mysql_error());
}
//-----------------------------------------------------------------

function TransferFile($File)
{
	require( "config.php" );	//	vklucuvanje na bazata
	global $theUser;
	$handle = @fopen($SETTINGS['USER_DIR']."/".$theUser['user']."/".$_SESSION['ASN']."/" .$File, "r");
	
	$comAll=1;
	if (!file_exists($SETTINGS['USER_DIR']."/".$theUser['user']."/".$_SESSION['ASN']."/" .$File."RESULT.xml")) $comAll=0;
	else {
		$resName = $SETTINGS['USER_DIR']."/".$theUser['user']."/".$_SESSION['ASN']."/" .$File.".res";
		$REScontents = @file_get_contents($resName);
		if (!$REScontents) $comAll=0;
		else $REScontents = unserialize($REScontents);
	}
	
  if ($handle) {
	  echo $comAll."<table>";
	  $inp = fgets($handle);
	  $out = fgets($handle);
	  $cols = $inp + $out;
	  $treta = true; $rowN=1;
	  while (($buffer = fgets($handle)) !== false) {
		  if ($rowN %2 == 0) echo"<tr id='r".($treta ? ($rowN-1) : $rowN)."' class='ln1'>";
		  else  echo"<tr id='r".($treta ? ($rowN-1) : $rowN)."'>";
		  $buffer = explode(",",$buffer);
		  
		  if ($treta)
		  {
			  echo"<th>Row N.</th>";
			  if (!is_numeric($buffer[0])) {
				  for ($i=0; $i<$cols; $i++) {
					  if($buffer[$i])
						  echo"<th>".trim($buffer[$i])."</th>";
					  else {
						  if ($i >= $inp)
						  	echo"<th>Out".(($i%$out)+1)."</th>";
						  else echo"<th>Inp".(($i%$inp)+1)."</th>";
					  }
				  }
			  } else {
				  $treta = false;
				  for ($i=0; $i<$cols; $i++) {
					  if ($i >= $inp)
						  	echo"<th>Out".(($i%$out)+1)."</th>";
					  else echo"<th>Inp".(($i%$inp)+1)."</th>";
				  }
				  echo"<th>Pattern_recognition_result</th></tr><tr id='r".($rowN)."' >";
			  }
		  }
		  if (!$treta) {
			  echo"<th>$rowN</th>";
			  $rowN++;
			for ($i=0; $i<$cols; $i++) {
				try {
					echo"<td>".trim($buffer[$i])."</td>";
				}
				catch(Exception $E) {
					echo"<td>0.0000</td>";
				}
			}
		  }
		  if ($treta)  echo"<th>Pattern_recognition_result</th></tr>";
		  else echo"<td id='pdr".($rowN-1)."'>".($comAll?$REScontents[$rowN-2]:"")."</td></tr>";
		  $treta=false;
	  }
	  if (!feof($handle)) {
		  echo "Error: unexpected fgets() fail\n";
	  }
	  fclose($handle);
	 
	  $resName = $SETTINGS['USER_DIR']."/".$theUser['user']."/".$_SESSION['ASN']."/" .$File.".wrk";
	  $contents = @file_get_contents($resName);
	  $resFile = fopen($resName, "w+");
	  $contents = unserialize($contents);
	  $contents['lines'] = ($rowN-1);
	  fwrite($resFile, serialize($contents));
	  fclose($resFile);
	  
	  DB_CONN();
	  mysql_query("UPDATE `login` SET `status`='loggedin', `test` = '0', `log` = '".time()."' WHERE `id`=".$theUser['id']);
	  mysql_query("UPDATE `session` SET `lastFile`='$File' WHERE `id`='".$_SESSION['ASID']."'");
	 
	  echo "</table>";//$outBuf . 
  } else echo "Invalid file!";
}
//-----------------------------------------------------------------

function PMatchLine($params)
{
	require( "config.php" );	//	vklucuvanje na bazata
	global $theUser;
	DB_CONN();
	mysql_query("UPDATE `login` SET `status`='loggedin', `test` = '0', `log` = '".time()."' WHERE `id`=".$theUser['id']);
	$fileName = strip_tags($params[0]);
	$line =  strip_tags($params[1]);
	$modul =  strip_tags($params[2]);
	$bufer = $line.",";
	$curTxt="";
	$file = new SplFileObject($SETTINGS['USER_DIR']."/".$theUser['user']."/".$_SESSION['ASN']."/" .$fileName);
	$inputs=	$file->current();
	$file->next();
	$outputs=	$file->current();
	$file->next();
	
	$inOut = ($inputs+$outputs);
	
	
	if (is_numeric(substr( $file->current(), 0, 1)))
	{
		$file->seek($line+1);
		$curTxt = $file->current();
	}
	else {
		$file->seek($line+2);
		$curTxt = $file->current();
	}
	
	$bufer .= trim($curTxt);
	require( "clientSocket.php" );
	
	$res = mysql_query("SELECT * FROM `modules` WHERE `id`='$modul'");
	if (!mysql_num_rows($res)) die ("Invalid module!");
	$rez = mysql_fetch_assoc($res);
	
	$stressTest = explode(",", $bufer);
		if (count($stressTest) < ($inOut+1)) {
			die("Module failed to process line: ".$line."<br/>Error in file data !");
		}
	$bufer = implode(",",array_slice($stressTest, 0, ($inOut+1))); 
	$error=0;
	$CS = new CSocket();

	$err = $CS->open($rez['ip'], $rez['port']);
	if ($err != "OK") die($err);
	
	$CS->writeln(trim($bufer));
	$CS->read($bufer);
	if (preg_replace("/".$line.",/i", "", $bufer) == $bufer){ $error=1; $bufer = "Server error: Invalid line returned."; }
	else $bufer = preg_replace("/".$line.",/i", "", $bufer);
	$CS->writeln("-9,".trim($curTxt));
	$CS->close();
	if ($error) die ($bufer);
	$resName = $SETTINGS['USER_DIR']."/".$theUser['user']."/".$_SESSION['ASN']."/" .$fileName.".res";
	$contents = file_get_contents($resName);
	$resFile = fopen($resName, "w+");
	$outBuf = unserialize($contents);
	$outBuf[$line] = substr($bufer, 0, 1);
	fwrite($resFile, serialize($outBuf));
	fclose($resFile);
	
	$resultPie=0;
	foreach ($outBuf as $tmpr) {
		if ($tmpr == 1) $resultPie++;
	}
	$resName = $SETTINGS['USER_DIR']."/".$theUser['user']."/".$_SESSION['ASN']."/" .$fileName.".wrk";
	  $contents = @file_get_contents($resName);
	  $contents = unserialize($contents);
	  
	include("gXML.php");
	$myPie = new GXML("charts/pie.xml", true);
	$fnd = &$myPie->AttrExists("set", "name", "Matching");
	$fnd["value"] = $resultPie;
	$myPie->AttrReset();
	$fnd = &$myPie->AttrExists("set", "name", "Not matching");
	$fnd["value"] = ($contents['lines']-$resultPie);
	$myPie->SaveXml($SETTINGS['USER_DIR']."/".$theUser['user']."/".$_SESSION['ASN']."/" .$fileName."PIE.xml");
	mysql_query("UPDATE `session` SET `lastFile`='$File' WHERE `id`='".$_SESSION['ASID']."'");
	die(substr($bufer, 0, 1));
}
//-----------------------------------------------------------------

function PMatchAll($params)
{
	require_once( "config.php" );	//	vklucuvanje na bazata
	global $theUser;

	DB_CONN();
	  mysql_query("UPDATE `login` SET `status`='loggedin', `test` = '0', `log` = '".time()."' WHERE `id`=".$theUser['id']);
	$fileName = strip_tags($params[0]);
	$modul =  strip_tags($params[1]);
	$width =  strip_tags($params[2]);
	
	if ($_SESSION['ASN'])	$resNameWRK = $SETTINGS['USER_DIR']."/".$theUser['user']."/".$_SESSION['ASN']."/" .$fileName.".wrk";
	else $resNameWRK = $SETTINGS['USER_DIR']."/".$theUser['user']."/" .$fileName.".wrk";
	
	mysql_query("UPDATE `session` SET `lastFile`='$fileName' WHERE `id`='".$_SESSION['ASID']."'");
	include("gXML.php");
	$xmlFile = $SETTINGS['USER_DIR']."/".$theUser['user']."/".$_SESSION['ASN']."/" .$fileName."RESULT.xml";
	/*if (!file_exists($xmlFile))
		copy("charts/default.xml", $xmlFile);*/
	$myXml = new GXML("charts/default.xml", true);

	require( "clientSocket.php" );
	
	$res = mysql_query("SELECT * FROM `modules` WHERE `id`='$modul'");
	if (!mysql_num_rows($res)) die ("Invalid module!");
	$rez = mysql_fetch_assoc($res);
	
	$CS = new CSocket();
	$err = $CS->open($rez['ip'], $rez['port']);
	if ($err != "OK") die("Socket Error: " . $err);
	
	$file = new SplFileObject($SETTINGS['USER_DIR']."/".$theUser['user']."/".$_SESSION['ASN']."/" .$fileName);
	$inputs=	$file->current();
	$file->next();
	$outputs=	$file->current();
	
	$inOut = ($inputs+$outputs);
	$file->seek(2);
	$outBuf = array();
	
	$resultPie=0;
	$errorsCnt=0;
	$curLine = 0;
	$bufer = "";
	while (!$file->eof()) {

		if (!$errorsCnt) {
			$curTxt = $file->current();
			$curLine++;
		}
		
		if (!is_numeric(substr( $curTxt, 0, 1))) {
			$file->next();
			$curTxt = $file->current();
		}
		$bufer = trim($curLine . "," . $curTxt);
		
		$wbuf = $bufer;		
		$stressTest = explode(",", $bufer);
		if (count($stressTest) < ($inOut+1)) {
			$CS->writeln("-9,".$curTxt);
			$CS->close();
			die("Module failed to process line: ".$curLine."<br/>Error in file data !");
		}
		$bufer = implode(",",array_slice($stressTest, 0, ($inOut+1))); 
		$CS->writeln(trim($bufer));
		$CS->read($bufer);

		if (preg_replace("/".$curLine.",/i", "", $bufer) == $bufer)  $bufer = "FAIL";
		else $bufer = trim(preg_replace("/".$curLine.",/i", "", $bufer));
		
		if ($bufer != "FAIL")  {
			$errorsCnt=0; 
			$file->next();
			$tmpr = substr($bufer, 0, 1);
			if ($tmpr)	$resultPie++;
			$myXml->AddElem("set", "", "chart", array("name"=>$curLine,"value"=>$tmpr));
			//die($myXml->PrintAll());
			$outBuf[] = $tmpr;
			}
		else $errorsCnt++;

		//echo "Processing line: ".$curLine;
		
		if ($errorsCnt > 5) {
			
			$CS->write("-9,".$curTxt);
			die("Module failed to process line: ".$curLine);
		}
	}
	
	$myXml->SetAttr("chartScene", "width", $width);
	$myXml->SaveXml($xmlFile);
	
	$myPie = new GXML("charts/pie.xml", true);
	$fnd = &$myPie->AttrExists("set", "name", "Matching");
	$fnd["value"] = $resultPie;
	$myPie->AttrReset();
	$fnd = &$myPie->AttrExists("set", "name", "Not matching");
	$fnd["value"] = ($curLine-$resultPie);
	$myPie->SaveXml($SETTINGS['USER_DIR']."/".$theUser['user']."/".$_SESSION['ASN']."/" .$fileName."PIE.xml");
	
	$CS->write("-9,".$curTxt);
	$CS->close();
	$resFile = fopen($SETTINGS['USER_DIR']."/".$theUser['user']."/".$_SESSION['ASN']."/" .$fileName.".res", "w+");
	fwrite($resFile, serialize($outBuf));
	fclose($resFile);
	
	$contents = @file_get_contents($resNameWRK);
	  $resFile = fopen($resNameWRK, "w+");
	  $contents = unserialize($contents);
	  $contents['PMA'] = "1";
	  fwrite($resFile, serialize($contents));
	  fclose($resFile);
	
	die( implode("", $outBuf) );
}
//-----------------------------------------------------------------

function SetChartW($params)
{
	require_once( "config.php" );	//	vklucuvanje na bazata
	global $theUser;
	
	$FN = strip_tags($params[0]);
	$width = strip_tags($params[1]);
	//$dolength = strip_tags($params[2]);
	
	if (empty($FN)) $FN = "default";
	
	$FN .= '.xml';
	
	if ($_SESSION['ASN'])	$FN = $SETTINGS['USER_DIR']."/".$theUser['user']."/".$_SESSION['ASN']."/" .$FN;
	else  $FN = $SETTINGS['USER_DIR']."/".$theUser['user']."/" .$FN;
	
	include("gXML.php");
	$myXml = new GXML($FN, true);
	$myXml->SetAttr("chartScene", "width", $width);
	
	echo $myXml->SaveXml($FN);
}
//-----------------------------------------------------------------

function SetChartLength($file)
{
	if (empty($file)) $file = "default";
	
	$file .= '.xml';
	
	include("gXML.php");
	$myXml = new GXML($file, true);
	$myXml->SetAttr("x-axis", "end", $_SESSION['lines']);
	echo $myXml->SaveXml($file);
}
//-----------------------------------------------------------------

function PRedrawAll($file, $selected, $width, $colors,$skip=false)
{
	require_once( "config.php" );	//	vklucuvanje na bazata
	global $theUser;
	
	$selected= explode("|",$selected);
	if (count($selected) == 1)if (!$skip) die("No columns to display!");
	$colors = explode(",", $colors);
	
	if ($skip)
		$SETTINGS = $skip;
	
	if ($_SESSION['ASN'])	$resName = $SETTINGS['USER_DIR']."/".$theUser['user']."/".$_SESSION['ASN']."/" .$file.".wrk";
	else $resName = $SETTINGS['USER_DIR']."/".$theUser['user']."/" .$file.".wrk";
	  $contents = @file_get_contents($resName);
	  $resFile = fopen($resName, "w+");
	  $contents = unserialize($contents);
	  $contents['selected'] = $selected;
	   $contents['colors'] = $colors;
	   $contents['rows'] = "ALL";
	  fwrite($resFile, serialize($contents));
	  fclose($resFile);
	
	if ($_SESSION['ASN'])	$fileName = $SETTINGS['USER_DIR']."/".$theUser['user']."/" .$_SESSION['ASN']."/".$file;
	else $fileName = $SETTINGS['USER_DIR']."/".$theUser['user']."/".$file;
	
	if (empty($file) || !file_exists($fileName))if (!$skip) die("Invalid input file!");

	$resName = $fileName.".res";
	$contents = @file_get_contents($resName);
	if (!$contents) if (!$skip) die("Make 'Compute All' first!");
	$contents = unserialize($contents);
	include("gXML.php");
	if (!file_exists($fileName."RESULT.xml"))if (!$skip) die("Make 'Compute All' first!");
	$myXml = new GXML("charts/default.xml", true);

//	if ($myXml->AttrExists("set", "name", count($contents)))
//	{
		$SPLfile = new SplFileObject($fileName);
		$inps = $SPLfile->current();
		$SPLfile->next();
		$outs = $SPLfile->current();
		$SPLfile->next();
		$curTxt = $SPLfile->current();
		$names=array();
		if (!is_numeric(substr( $curTxt, 0, 1))) {
			$names = explode(",",$curTxt);
			foreach ($names as $tmpkey => $tmpname)
			{
				$names[$tmpkey] = trim($tmpname);
			}
			$SPLfile->next();
		} 
		if ((count($names)) < ($inps+$outs)) {
			for ($i=(count($names)); $i<($inps+$outs); $i++) {	
				if ($i >= $inps)
				  $names[] = "Out".(($i%$outs)+1);
				else $names[] = "Inp".(($i%$inps)+1);
			}
		}
		//	kreiranje na kolonite vo xml
		$tmpLines=array();
		foreach ($selected as $kolona) {
			$kolona=trim($kolona);
			if ($kolona) {
			$tmpStyle = &$myXml->AddElem("chartstyle", 0, 0, array("name"=>$kolona));
			$tmpStyle->addChild("type", "line");
			$tmpStyle->addChild("font", "FontChart");
			$tmpStyle->addChild("name", $kolona);
			$tmpStyle->addChild("tips", "on");
			$tmpStyle->addChild("pointTitles", "on");
			$rndColor = "";
			if (strtolower($kolona) == "pattern_recognition_result") {
				$rndColor="0020C2";
				$names[] = $kolona;
			}

			$colIndex = array_search($kolona, $names);
			if ( $colors[$colIndex]) $rndColor = $colors[$colIndex];
			
			if ($rndColor == "") {
					for ($i=0; $i<6; $i++) {
						$rndColor .= dechex ( rand (0, 15) );
					}
			}
			
			$tmpStyle->addChild("lineColor", $rndColor);
			$tmpStyle->addChild("color", "ffffff");
			$tmpStyle->addChild("opacity", "50");
			$tmpStyle->addChild("knotType", "circle");
			$tmpStyle->addChild("knotSize", "10");
			$tmpStyle->addChild("line", "5");
			$tmpStyle->addChild("nullLine", "2");	
			$tmpStyle->addChild("columnTargetColor", "FFFFFFF");
			$tmpStyle->addChild("columnWidth", "30");
			
			$tmpLines[] = &$myXml->AddElem("chart", 0, 0, array("chartstyle"=>$kolona));
			}
		}
		
		//	dodavanje na vrednosti za kolonite
		$curRow=0;
		//echo (print_r($names).print_r($selected));
		while (!$SPLfile->eof())
		{
			$curTxt = $SPLfile->current();
			$curTxt = explode(",", $curTxt);
			$tcnt=0; $tmpl=0; 
			foreach($names as $kolons)
			{
				if (in_array($kolons, $selected))
				{
					//die ("adding $kolons");
					if (strtolower($kolons) == "pattern_recognition_result")
					{
						$tmpVal = $contents[$curRow];
					}
					else $tmpVal = $curTxt[$tcnt];
				  $tmpLine = $tmpLines[$tmpl];
				  $child = $tmpLine->addChild("set");
				  $child->addAttribute("name", $kolons);
				  $child->addAttribute("value", trim($tmpVal));
				  $tmpl++;
				}
				$tcnt++;
				//echo ("skipping $kolons");
			}
			$curRow++;
			$SPLfile->next();
		}
		$myXml->SetAttr("chartScene", "width", $width);

		$myXml->SaveXml($fileName.".xml");
		
		DB_CONN();
		mysql_query("UPDATE `login` SET `status`='loggedin', `test` = '0', `log` = '".time()."' WHERE `id`=".$theUser['id']);
		mysql_query("UPDATE `session` SET `lastFile`='$file' WHERE `id`='".$_SESSION['ASID']."'");
		
		if (!$skip) die("@".$fileName.".xml");
//	}
//	else die("Make 'Compute All' first!");
}
//-----------------------------------------------------------------

function PRedrawSelected($file, $selected, $rows, $width, $colors, $skip=false)
{
	require_once( "config.php" );	//	vklucuvanje na bazata
	global $theUser;
	if ($skip)
		$SETTINGS = $skip;
	$selected= explode("|",$selected);
	if (count($selected) == 1) if (!$skip) die("No columns to display!");
	$colors= explode(",",$colors);
	$rows= explode("|",$rows);
	
	DB_CONN();
	mysql_query("UPDATE `login` SET `status`='loggedin', `test` = '0', `log` = '".time()."' WHERE `id`=".$theUser['id']);
		mysql_query("UPDATE `session` SET `lastFile`='$file' WHERE `id`='".$_SESSION['ASID']."'");
		
	if ($_SESSION['ASN'])	 $resName = $SETTINGS['USER_DIR']."/".$theUser['user']."/".$_SESSION['ASN']."/" .$file.".wrk";
	else  $resName = $SETTINGS['USER_DIR']."/".$theUser['user']."/".$file.".wrk";
	  $contents = @file_get_contents($resName);
	  $resFile = fopen($resName, "w+");
	  $contents = unserialize($contents);
	  $contents['selected'] = $selected;
	   $contents['colors'] = $colors;
	   $contents['rows'] = $rows;
	  fwrite($resFile, serialize($contents));
	  fclose($resFile);
	
	if ($_SESSION['ASN'])	$fileName = $SETTINGS['USER_DIR']."/".$theUser['user']."/".$_SESSION['ASN']."/" .$file;
	else $fileName = $SETTINGS['USER_DIR']."/".$theUser['user']."/" .$file;
	
	if (empty($file) || !file_exists($fileName))if (!$skip) die("Invalid file!");

	$resName = $fileName.".res";
	$contents = @file_get_contents($resName);
	if (!$contents)if (!$skip) die("Invalid file!");
	$contents = unserialize($contents);
	include("gXML.php");
	if (!file_exists("charts/default.xml"))if (!$skip) die("Critical error! Contact administrator!");
	$myXml = new GXML("charts/default.xml", true);

	foreach ($rows as $rown) {
		if ($rown && !is_numeric($contents[$rown]))
			if (!$skip) die("Selected rows are not computed!");
	}
		$file = new SplFileObject($fileName);
		$ofset=2;
		$inps = $file->current();
		$file->next();
		$outs = $file->current();
		$file->next();
		$curTxt = $file->current();
		$names=array();
		if (!is_numeric(substr( $curTxt, 0, 1))) {
			$names = explode(",",$curTxt);
			foreach ($names as $tmpkey => $tmpname)
			{
				$names[$tmpkey] = trim($tmpname);
			}
			$ofset++;
		}
		if ((count($names)) < ($inps+$outs)) {
			for ($i=(count($names)); $i<($inps+$outs); $i++) {	
				if ($i >= $inps)
				  $names[] = "Out".(($i%$outs)+1);
				else $names[] = "Inp".(($i%$inps)+1);
			}
		}
		//	kreiranje na kolonite vo xml
		$tmpLines=array();
		foreach ($selected as $kolona) {
			if ($kolona){
				$kolona = trim($kolona);
			  $tmpStyle = &$myXml->AddElem("chartstyle", 0, 0, array("name"=>$kolona));
			  $tmpStyle->addChild("type", "line");
			  $tmpStyle->addChild("font", "FontChart");
			  $tmpStyle->addChild("name", $kolona);
			  $tmpStyle->addChild("tips", "on");
			  $tmpStyle->addChild("pointTitles", "on");
			  $rndColor = "";
			  if (strtolower($kolona) == "pattern_recognition_result") {
				   $rndColor="0020C2";
				   $names[] = $kolona;
				   //die("yea found it!");
			  }
			  $colIndex = array_search($kolona, $names);
			   if ( $colors[$colIndex]) $rndColor = $colors[$colIndex];
			  if ($rndColor == "") {

					for ($i=0; $i<6; $i++) {
						$rndColor .= dechex ( rand (0, 15) );
					}

			  }
  
			  $tmpStyle->addChild("lineColor", $rndColor);
			  $tmpStyle->addChild("color", "ffffff");
			  $tmpStyle->addChild("opacity", "50");
			  $tmpStyle->addChild("knotType", "circle");
			  $tmpStyle->addChild("knotSize", "10");
			  $tmpStyle->addChild("line", "5");
			  $tmpStyle->addChild("nullLine", "2");	
			  $tmpStyle->addChild("columnTargetColor", "FFFFFFF");
			  $tmpStyle->addChild("columnWidth", "30");
			  
			  $tmpLines[] = &$myXml->AddElem("chart", 0, 0, array("chartstyle"=>$kolona));
			}
		}
		
		//	dodavanje na vrednosti za kolonite
		foreach ($rows as $rown) 
		{
			if ($rown)
			{
			$file->seek($ofset+$rown-1);
			$curTxt = $file->current();
			$curTxt = explode(",", $curTxt);
			$tcnt=0; $tmpl=0;
			foreach($names as $kolons)
			{
				if (in_array($kolons, $selected))
				{
					if (strtolower($kolons) == "pattern_recognition_result")
					{
						$tmpVal = $contents[$rown];
					}
					else $tmpVal = $curTxt[$tcnt];
				  $tmpLine = $tmpLines[$tmpl];
				  $child = $tmpLine->addChild("set");
				  $child->addAttribute("name", $kolons);
				  $child->addAttribute("value", trim($tmpVal));
				  $tmpl++;
				}
			  $tcnt++;
			}
			}
		}
		$myXml->SetAttr("chartScene", "width", $width);
		
		$myXml->SaveXml($fileName."SEL.xml");
		if (!$skip)die("@".$fileName."SEL.xml");
}
//-----------------------------------------------------------------

function  ExportCSV($fileNs)
{
	require_once( "config.php" );
	global $theUser;
	
	if ($_SESSION['ASN'])	$fileName = $SETTINGS['USER_DIR']."/".$theUser['user']."/".$_SESSION['ASN']."/" .$fileNs;
	else $fileName = $SETTINGS['USER_DIR']."/".$theUser['user']."/" .$fileNs;
	
	if (!file_exists($fileName)) die("Invalid file!");
	
	$resName = $fileName.".res";
	$contents = @file_get_contents($resName);
	if (!$contents) die("Invalid file!");
	$contents = unserialize($contents);
	
	$file = new SplFileObject($fileName);
	$inps = $file->current();
	$file->next();
	$outs = $file->current();
	$file->next();
	$curTxt = $file->current();
	$names=array();
	if (!is_numeric(substr( $curTxt, 0, 1))) {
		$names = explode(",",$curTxt);
		$file->next();
		foreach ($names as $tmpkey => $tmpname)
			{
				$names[$tmpkey] = trim($tmpname);
			}
		}
	if ((count($names)) < ($inps+$outs)) {
			for ($i=(count($names)); $i<($inps+$outs); $i++) {	
				if ($i >= $inps)
				  $names[] = "Out".(($i%$outs)+1);
				else $names[] = "Inp".(($i%$inps)+1);
			}
		}
	
	array_push($names,"Pattern_recognition_result");
	
	$csvFN = substr($fileName, 0, strlen($fileName)-4)."-patrec.csv";
	$csvFNs = substr($fileNs, 0, strlen($fileNs)-4)."-patrec.csv";
	
	$csv = fopen($csvFN, "w+");
	//die (implode(",",$names));
	fwrite($csv, implode(",",$names)."\r\n");
	
	$line=0;
	while (!$file->eof())
	{
		$curTxt = explode(",", trim($file->current()));
		while (count($curTxt) > count($names)-1)
			array_pop($curTxt);
		$curTxt = implode(",",$curTxt);
		$curTxt .= ",".$contents[$line];
		
	   fwrite($csv, $curTxt."\r\n");

		$line++;
		$file->next();
	}
	fclose($csv);
	
	DB_CONN();
	mysql_query("UPDATE `login` SET `status`='loggedin', `test` = '0', `log` = '".time()."' WHERE `id`=".$theUser['id']);
		mysql_query("UPDATE `session` SET `lastFile`='$fileNs' WHERE `id`='".$_SESSION['ASID']."'");
	die("@".$csvFNs);
}
//-----------------------------------------------------------------

function deleteDir($dirPath) {
    if (! is_dir($dirPath)) {
        throw new InvalidArgumentException('$dirPath must be a directory');
    }
    if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
        $dirPath .= '/';
    }
    $files = glob($dirPath . '*', GLOB_MARK);
    foreach ($files as $file) {
        if (is_dir($file)) {
            deleteDir($file);
        } else {
            unlink($file);
        }
    }
    rmdir($dirPath);
}
//-----------------------------------------------------------------

function DelSession($SesName)
{
	require_once( "config.php" );
	global $theUser;
	DB_CONN();
	mysql_query("UPDATE `login` SET `status`='loggedin', `test` = '0', `log` = '".time()."' WHERE `id`=".$theUser['id']);
	if (mysql_query("DELETE FROM `session` WHERE `name`='".mysql_real_escape_string($SesName)."' AND `link`='".$theUser['id']."' LIMIT 1"))
	{
		$dirPath = $SETTINGS['USER_DIR']."/".$theUser['user']."/".$SesName;
		try { 	deleteDir($dirPath); } catch (Exception $E) {}
		if ($SesName == $_SESSION['ASN']) $_SESSION['ASN']="";
		die("Session Deleted!");
	}
	else die (mysql_error());
}
//-----------------------------------------------------------------

function LoadResults($File, $width)
{
	require_once( "config.php" );
	global $theUser;
	
	$fileName = $SETTINGS['USER_DIR']."/".$theUser['user']."/".$_SESSION['ASN']."/" .$File;
	
	if (empty($File) || !file_exists($fileName)|| !file_exists($fileName.".res")
	|| !file_exists($fileName.".wrk")) die();
	
	$resName = $fileName.".wrk";
	  $contents = @file_get_contents($resName);
	  $contents = unserialize($contents);
	  $which="A";
	  if ( $contents['rows'] == "ALL" ) {
		  $which="A";
				  include("gXML.php");
			$myXml = new GXML($fileName.".xml", true);
			$myXml->SetAttr("chartScene", "width", $width);
			$myXml->SaveXml($fileName.".xml");
	  }
			// PRedrawAll($File, $contents['selected'], $width, $contents['colors'], $SETTINGS);
		else {
			 $which="B";
			 include("gXML.php");
			$myXml = new GXML($fileName."SEL.xml", true);
			$myXml->SetAttr("chartScene", "width", $width);
			$myXml->SaveXml($fileName.".xml");
			//PRedrawSelected($File, $contents['selected'], $contents['rows'], $width, $contents['colors'], $SETTINGS);
		}

	$colors = $contents['colors'];
	$compAll =0;
	if ($contents['PMA'])  $compAll=1;
	$resName = $fileName.".res";
	$contents = @file_get_contents($resName);
	if (!$contents) die();
	$contents = unserialize($contents);
	die ($compAll. $which. implode("", $colors). implode("", $contents));
}
//-----------------------------------------------------------------

function CheckSessionName($SESNAME) 
{
	require_once( "config.php" );
	global $theUser;
	DB_CONN();
	mysql_query("UPDATE `login` SET `status`='loggedin', `test` = '0', `log` = '".time()."' WHERE `id`=".$theUser['id']);
	$res = mysql_query("SELECT * FROM `session` WHERE `name`='".mysql_real_escape_string($SESNAME)."' AND `link`='".$theUser['id']."' LIMIT 1");
	if (!mysql_num_rows($res)) {
		if (!is_dir($SETTINGS['USER_DIR']."/".$theUser['user']."/".$SESNAME)) {
		 		$_SESSION['ASN']=$SESNAME;  
				mkdir($SETTINGS['USER_DIR']."/".$theUser['user']."/".$SESNAME);
			}
		 die("OK"); 
		 }
	else die("Such session name already exists. Please enter another name for a session !");
}
//-----------------------------------------------------------------

function LoadLastIOS($File) 
{
	require_once( "config.php" );
	global $theUser;
	
	$fileName = $SETTINGS['USER_DIR']."/".$theUser['user']."/".$_SESSION['ASN']."/" .$File;
	
	if (empty($File) || !file_exists($fileName)) die();
	
	$resName = $fileName.".wrk";
	  $contents = @file_get_contents($resName);
	  $contents = unserialize($contents);
	  
	  die (implode("|", $contents['selected']));
}
//-----------------------------------------------------------------

function GetFileSize($File)
{
	if (file_exists($File)) die ("".filesize ($File));
	else die ("0");
}
//-----------------------------------------------------------------



//-----------------------------------------------------------------
//-----------------------------------------------------------------
//-----------------------------------------------------------------

$akcija = strip_tags($_POST['action']);
$params = explode("^", $_POST['params']);

switch ($akcija)
{	
	case "GetFileSize" : GetFileSize(strip_tags($params[0]));
		break;
		
	case "LoadLastIOS" : LoadLastIOS(strip_tags($params[0]));
	break;
	
	case "CheckSessionName" : CheckSessionName(strip_tags($params[0]));
	break;
	
	case "LoadResults": LoadResults(strip_tags($params[0]), strip_tags($params[1]));
	break;
	
	case "DelSession" : DelSession(strip_tags($params[0]));
	break;
	
	case "ExportCSV" : ExportCSV(strip_tags($params[0]));
	break;
	
	case "PRedrawSelected": PRedrawSelected(strip_tags($params[0]), strip_tags($params[1]), strip_tags($params[2]), strip_tags($params[3]), strip_tags($params[4]));
	break;
	
	case "PRedrawAll": PRedrawAll(strip_tags($params[0]), strip_tags($params[1]), strip_tags($params[2]), strip_tags($params[3]));
	break;
	
	case "SetChartLength" : SetChartLength(strip_tags($params[0]));
	break;
	
	case "SetChartW" : SetChartW($params);
	break;
	
	case "PMatchAll" : PMatchAll($params);
	break;
	
	case "PMatchLine" : PMatchLine($params);
	break;
	
	case "TransferFile" : TransferFile(strip_tags($params[0]));
	break;
	
	case "DelFileDB": DelFileDB($params);
	break;
	
	case "SaveFileDB": SaveFileDB($params);
	break;
	
	case "SetModule": SetActiveModule($params);
	break;
	
	case "SaveSession": SaveSession(strip_tags($params[0]));
	break;
	
	case "UpdateSession": UpdateSession($params);
	break;
	
	case "LoadSession": LoadSession(strip_tags($params[0]));
	break;
}
//-----------------------------------------------------------------

?>
