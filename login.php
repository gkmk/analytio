<?php 
session_start();
$theUser = array();
$ERROR="";
if (isset($_SESSION['SES_user'])) {
		$theUser = unserialize($_SESSION['SES_user']);
		if ($theUser['access'] < 10) header("Location: index.php"); 
	else header("Location: admin/");
	}

	
	if (isset($_POST['user']) && isset($_POST['pass'])) {
		require( "config.php" );	//	vklucuvanje na bazata
		DB_CONN();
		require("capture/securimage.php");
		$imgTest = new Securimage();
	  	$valid = $imgTest->check($_POST['the_vault_code']);
		if($valid == true) 
		{
		$user = mysql_real_escape_string($_POST['user']);	// cistenje na vleznite podatoci
		$pass = md5(mysql_real_escape_string($_POST['pass']));
		
		$res = mysql_query("SELECT * FROM `login` WHERE `user`='".$user."' AND `pass`='".$pass."'");
		
		if (!mysql_num_rows($res)) 
		{
			$ERROR = "Wrong username/password!";
			
			$res1 = mysql_query("SELECT * FROM `login` WHERE `user`='".$user."'");
			if (!mysql_num_rows($res1)) $ERROR = "Username doesn't exist!";
			else {
				mysql_query("INSERT INTO `log` (`info`) VALUES ('The user: ".$user." has tried wrong password!')");
				
				$rez = mysql_fetch_assoc($res1);
				if (($rez['test'] >= 3) && ($rez['log'] > time()-1800)) 
				{
					$ERROR = "This account is locked for 30 minutes because of wrong password!";
					mysql_query("INSERT INTO `log` (`info`) VALUES ('Blocked user: ".$user."! (wrong password 3times)')");
				}
				else 
				{
					if ($rez['log'] < time()-1800)
					{
						$rez['test']=1;
						$rez['log']=time();
					}
					else $rez['test']++;
					
					mysql_query("UPDATE `login` SET `test` = '".$rez['test']."', `log` = '".$rez['log']."' WHERE `id`=".$rez['id']);
				}
			}
			
		}
		else {
			$rez = mysql_fetch_assoc($res);
			if ( ($rez['log'] > time()-1800) && ($rez['test'] >= 3) )
			{
				mysql_query("INSERT INTO `log` (`info`) VALUES ('Blocked user: ".$user."! (wrong password 3times, cant login)')");
				$ERROR = "This account is locked for 30 minutes because of wrong password!";
			}
			else 
			{
			  if ($rez['status'] !=  "out" && $rez['log'] > time()-900) {
				   $ERROR = "This user is already logged in! Note: After 15min of user's inactivity, it will be automatically logged off !";
			  }
			  else {
				mysql_query("INSERT INTO `log` (`info`) VALUES ('The user has logged in: ".$user."')");
				mysql_query("UPDATE `login` SET `status`='loggedin', `test` = '0', `log` = '".time()."' WHERE `id`=".$rez['id']);
				$_SESSION['SES_user'] = serialize($rez);
				header("Location: index.php");
			  }
			}
		}
	}
	else $ERROR = "Invalid captcha code!";
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="shortcut icon" type="image/x-icon" href="favicon.ico" />
<link rel="icon" type="image/x-icon" href="favicon.ico" />
<title>Login</title>
<style>
body {
	font-family:Calibri;
}
input[type="submit"] {
	padding:10px;
	width:100px;
}
</style>
</head>

<body background="images/bck.jpg">
<h1 style="color:#900; margin:10px auto;"><?php echo $ERROR; ?></h1>
<div style="margin:10px auto; width:350px; padding:20px 40px; text-align:center">
<img src="images/Analytio-logo-1s.png" width="138" height="40" alt="logo" />
<h1>Analytio SaaS for<br />
Pattern Matching &<br />
Forecasting Analysis</h1><br />
<hr/>
<h3>Login</h3>
<hr/>
<div style="margin:10px auto; width:250px">
<form action="" method="post">
<table width="100%">
<tr><td align="left">
<label for="user">Username:</label></td></tr><tr><td align="left"><input type="text" name="user" id="user" maxlength="40" /></td></tr>
<tr><td align="left">
<label for="pass">Password:</label></td></tr><tr><td align="left"><input type="password" name="pass" id="pass" maxlength="40" /></td></tr>
<tr><td align="left"><?php require("capture/render_cap.php"); ?></td></tr>
<tr><td align="center"><br />
<input type="submit" value="Sign In" /></td></tr>
</table>
</form>
</div>
<hr/>
</div>
</body>
</html>
