<?php 
	require("../session_check.php");

	if ($theUser['access'] < 10) header("Location: ../login.php"); 
	require( "../config.php" );	//	vklucuvanje na bazata
			DB_CONN();

	if (!empty($_GET['ID']) && !empty($_POST['user']) && !empty($_POST['pass']) && !empty($_POST['language']) && !empty($_POST['company'])
		&& !empty($_POST['name']) && !empty($_POST['telephone']) && !empty($_POST['address'])&& !empty($_POST['PostCode']) && 
		!empty($_POST['town']) && !empty($_POST['country']) && !empty($_POST['date'])) {

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
		
			$res = mysql_query("UPDATE `login` SET `user` = '".$user."', `pass`= '".md5($pass)."', `Language`='".$language."', `Company`='".$company."', `Name`= '".$name."', `Telephone`= '".$telephone."',  `Address`='".$address."', `PostCode`= '".$PostCode."',  `Town`='".$town."', `Country`='".$country."', `Date`= '".$date."' WHERE `id` = '".$ID."'");
		
			if (!$res) die(mysql_error());
			$ERROR = "User info updated!";
			mysql_query("INSERT INTO `log` (`info`) VALUES ('User updated: ".$user."')");
		}
	} else $ERROR = "Please fill in all fields!";
	if (!empty($_GET['ID']))
	{
		$theId = $_GET['ID'];
		if (is_numeric($theId))
		{
			
		$result = mysql_query("SELECT * FROM login WHERE `id`='".mysql_real_escape_string($theId)."'");
		$row = mysql_fetch_array($result);

                  $_POST['user'] = ($row['user']);	// cistenje na vleznite podatoci
				//  $pass = mysql_real_escape_string($row['pass']);
				  $_POST['company'] = ($row['Company']);	
				  $_POST['name'] = ($row['Name']);	
				  $_POST['telephone'] = ($row['Telephone']);
				  $_POST['address'] = ($row['Address']);
				  $_POST['PostCode'] = ($row['PostCode']);	
				  $_POST['town'] = ($row['Town']);
				  $_POST['country'] = ($row['Country']);	
				  $_POST['date'] = ($row['Date']); 
		}
	}
	else $ERROR="No user selected!";
	
	if ($_GET['action'] == "do")
	if ($ERROR != "") die($ERROR);
	else die("Unknown error");
?>
<script>
$(function() {
		$( "#date" ).datepicker();
	});
function UPDUser() 
{
	formdata = $(":input").serialize();
	$.post('update.php?action=do&ID=<?php echo $_GET['ID']; ?>', formdata, function(data) {
  	ShowAlert("Info", data);
	if (data == "User info updated!") window.location.reload();
	else if (data =="No user selected!") $("#dialog").dialog( "close" );
});
return;
}
</script>

<div style="margin:0 auto; width:450px;">

<h2>Manage User</h2>

<table>
<form action="/" id="upfrm">
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
<tr><td colspan="2" align="right"><button id="bcreate" onClick="UPDUser()">Update</button></td></tr>
</table>

</div>
