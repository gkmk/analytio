<?php
require("session_check.php");

if ($theUser['access'] == 10) header("Location: admin/");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="shortcut icon" type="image/x-icon" href="favicon.ico" />
<link rel="icon" type="image/x-icon" href="favicon.ico" />
<title>User Choose section</title>
<style>
.bigButton {
	padding:10px;
	font-size:16px;
	width: 145px;
	height:100px;
	margin:10px
}
.fix {
	position:relative;
	top:-10px !important;
}
</style>
</head>

<body background="images/bck.jpg">
<div style="width:400px; margin: 100px auto; text-align:center">
<img src="images/Analytio-logo-1s.png" width="138" height="40" alt="logo" /><br />
<br /><br />
<h3>Welcome <?php echo $theUser['user']; ?>, <a href="logout.php">[Logout]</a></h3>
<h1 style="background-color:#CCC">Choose section</h1><br />
<br />
<hr/>
<button class="bigButton" onclick="window.location='pattern_recognition.php'">Pattern Recognition</button>
<?php
$DONT=true;
$resModule = mysql_query("SELECT * FROM `modules` WHERE `link`='".$theUser['id']."'");
if (mysql_num_rows($resModule) < 1) $DONT=true;
else {
	while ($rowModule = mysql_fetch_assoc($resModule)) {
		if ($rowModule['modul_type'] == "forecast") {
			 $DONT=false;
			 break;
		}
	}
}
if (!$DONT){
?>
<button class="bigButton fix" disabled="disabled"  onclick="">Forecasting</button>
<?php } ?>
<hr/>
</div>
</body>
</html>