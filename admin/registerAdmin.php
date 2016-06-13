<?php 
	require("../session_check.php");

	if ($theUser['access'] < 10) header("Location: ../login.php"); 
	
	if (!empty($_POST['user']) && !empty($_POST['pass']) && !empty($_POST['language']) && !empty($_POST['company'])
		&& !empty($_POST['name']) && !empty($_POST['telephone']) && !empty($_POST['address'])&& !empty($_POST['PostCode']) && 
		!empty($_POST['town']) && !empty($_POST['country']) && !empty($_POST['date'])) {
		require( "../config.php" );	//	vklucuvanje na bazata
		DB_CONN();
		$user = mysql_real_escape_string($_POST['user']);	// cistenje na vleznite podatoci
		$pass = mysql_real_escape_string($_POST['pass']);
		$pass1 = mysql_real_escape_string($_POST['pass1']);
		$language = mysql_real_escape_string($_POST['language']);	
		$company = mysql_real_escape_string($_POST['company']);
		$name = mysql_real_escape_string($_POST['name']);	
		$telephone = mysql_real_escape_string($_POST['telephone']);
		$address = mysql_real_escape_string($_POST['address']);
		$PostCode = mysql_real_escape_string($_POST['PostCode']);	
		$town = mysql_real_escape_string($_POST['town']);
		$country = mysql_real_escape_string($_POST['country']);	
		$date = mysql_real_escape_string($_POST['date']);
		
		if ($pass != $pass1) $ERROR = "Passwords don't match!";
		else {
		$testRes = mysql_query("SELECT * FROM `login` WHERE `user` = '".$user."'");
		
		if (mysql_num_rows($testRes)) $ERROR = "Username already exists: ".$user;
		else {
			$res = mysql_query("INSERT INTO `login` (`user`, `pass`, `Language`, `Company`, `Name`, `Telephone`, `Address`, `PostCode`, `Town`, `Country`, `Date`, `access`) VALUES( '".$user."', '".md5($pass)."', '".$language."', '".$company."', '".$name."', '".$telephone."', '".$address."', '".$PostCode."', '".$town."', '".$country."', '".$date."', '10' )");
		
			if (!$res) die(mysql_error());
			$ERROR = "New administrator added!";
			mysql_query("INSERT INTO `log` (`info`) VALUES ('New user created: ".$user."')");
		}
		}
	} else $ERROR = "Fill in all fields.";
	
	if ($_GET['action'] == "do")
	if ($ERROR != "") die($ERROR); else die("Unknown error");
?>
<script>
$(function() {
		$( "#date" ).datepicker();
	});
function CreateAdmin() 
{
	formdata = $(":input").serialize();
	$.post('registerAdmin.php?action=do', formdata, function(data) {
		ShowAlert("Info", data);
		if (data == "New administrator added!") window.location.reload();
	});
	return;
}
</script>

<div style="width:450px; margin:0 auto; ">

<h2>Add New Administrator</h2>

<table>
<form action="/" id="adminReg" >
<tr><td width="150">
<label for="user">Username:</label></td><td width="250"><input type="text" name="user" id="user" size="40" value="<?php echo $_POST['user']; ?>" /></td></tr>
<tr><td>
<label for="pass">Password:</label></td><td width="250"><input type="password" name="pass" id="pass" size="40" value="<?php echo $_POST['pass']; ?>" /></td></tr>
<tr><td>
<label for="pass1">Re-enter password:</label></td><td width="250"><input type="password" name="pass1" id="pass1" size="40" /></td></tr>
<tr><td>
<label for="prasanje">Language:</label></td><td width="250">  <select name="language" id="language">
  <option value="ENG">ENG</option>
  </select></td></tr>
<tr><td>
<label for="sa">Company:</label></td><td width="250"><input type="text" name="company" id="company" size="40" value="<?php echo $_POST['company']; ?>" /></td></tr>
<tr><td>
<label for="name">Name & Surname:</label></td><td width="250"><input type="text" name="name" id="name" size="40" value="<?php echo $_POST['name']; ?>" /></td></tr>
<tr><td>
<label for="telephone">Telephone:</label></td><td width="250"><input type="text" name="telephone" id="telephone" size="40" value="<?php echo $_POST['telephone']; ?>" /></td></tr>
<tr><td>
<label for="address">Address:</label></td><td width="250"><input type="text" name="address" id="address" size="40" value="<?php echo $_POST['address']; ?>" /></td></tr>
<tr><td>
<label for="PostCode">Post code:</label></td><td width="250"><input type="text" name="PostCode" id="PostCode" size="40" value="<?php echo $_POST['PostCode']; ?>" /></td></tr>
<tr><td>
<label for="town">Town:</label></td><td width="250"><input type="text" name="town" id="town" size="40" value="<?php echo $_POST['town']; ?>" /></td></tr>
<tr><td>
<label for="country">Country:</label></td><td width="250"><input type="text" name="country" id="country" size="40" value="<?php echo $_POST['country']; ?>" /></td></tr>
<tr><td>
<label for="date">Date:</label></td><td width="250"><input type="text" name="date" id="date" size="40" value="<?php echo $_POST['date'] ? $_POST['date']: date('d/m/Y'); ?>" /></td></tr>
</form>
<tr><td colspan="2" align="right"><button id="bcreate" onClick="CreateAdmin()">Create</button></td></tr>
</table>

</div>
