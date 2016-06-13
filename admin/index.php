<?php
require("../session_check.php");

if ($theUser['access'] < 10) header("Location: ../login.php");

//print_r($theUser);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Admin Panel</title>
<style>
.bigButton {
	padding:10px;
	font-size:16px;
	width: 125px;
}
</style>
</head>

<body>
<div style="width:400px; margin: 10px auto; text-align:center">
<h1>Analytio SaaS<br />
Administrators and Users<br />
Management Panel</h1><br />
<h3>Welcome <?php echo $theUser['user']; ?>, <a href="../logout.php">[Logout]</a></h3>
<hr/>
<button class="bigButton" onclick="window.location='userPanel.php'">Manage Users</button>
<button class="bigButton" onclick="window.location='adminPanel.php'">Manage Administrators</button>
<hr/>
</div>
</body>
</html>