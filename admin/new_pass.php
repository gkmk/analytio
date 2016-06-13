<?php 
require("../session_check.php");

	if ($theUser['access'] < 10) header("Location: ../login.php"); 
	
	if (!empty($_POST['user']) && !empty($_POST['pass']) ) {
		require( "../config.php" );	//	vklucuvanje na bazata
		DB_CONN();
		$user = mysql_real_escape_string($_POST['user']);	// cistenje na vleznite podatoci
		$pass = mysql_real_escape_string($_POST['pass']);
		$pass1 = mysql_real_escape_string($_POST['pass1']);

		if ($pass != $pass1) $ERROR = "Passwords don't match";
		else {
		$testRes = mysql_query("SELECT * FROM `login` WHERE `user` = '".$user."'");
		
		if (mysql_num_rows($testRes))
		{
			$rez = mysql_fetch_assoc($testRes);

				$res = mysql_query("UPDATE `login` SET `pass` = '".md5($pass)."'");
				if (!$res) die(mysql_error());
				$ERROR = "The password is changed!";
				mysql_query("INSERT INTO `log` (`info`) VALUES ('Changed password for user: ".$user."')");
		}
		else {
			 $ERROR = "Username doesn't exist: ".$user;
			
		}
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Promeni lozinka</title>
<style type="text/css">
input {
	border: 1px solid #999;
	margin: 0 5px;
	}
.password_strength {
	padding: 0 5px;
	display: inline-block;
	}
.password_strength_1 {
	background-color: #fcb6b1;
	}
.password_strength_2 {
	background-color: #fccab1;
	}
.password_strength_3 {
	background-color: #fcfbb1;
	}
.password_strength_4 {
	background-color: #dafcb1;
	}
.password_strength_5 {
	background-color: #bcfcb1;
	}
</style>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.0/jquery.min.js"></script>
<script type="text/javascript" src="../jquery.password_strength.js"></script>
</head>

<body>
<?php if ($ERROR == "The password is changed!") die($ERROR); 
else echo "<h1 style='color:#900; margin:10px auto;'>".$ERROR."</h1>"; 
?>

<div style="margin:10px auto; width:550px; height:288px; padding:20px 40px;">

<h2>New Password</h2>
<form action="" method="post">
<table>
<tr><td width="150">
<label for="user">Username:</label></td><td width="250"><input type="text" name="user" id="user" size="40" value="<?php echo $_POST['user']; ?>" /></td></tr>
<tr><td>
<label for="pass">New password:</label></td><td width="250"><input type="password" name="pass" id="pass" size="40" value="<?php echo $_POST['pass']; ?>" /></td></tr>
<tr><td>
<label for="pass1">Re-enter password:</label></td><td width="250"><input type="password" name="pass1" id="pass1" size="40" /></td></tr>

<tr><td colspan="2" align="right"><input type="submit" value="Change" /></td></tr>
</table>
</form>
<script type="text/javascript">
$('#pass').password_strength();
</script>
</body>
</html>