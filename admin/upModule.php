<?php 
	require("../session_check.php");

	if ($theUser['access'] < 10) header("Location: ../login.php"); 
	require_once( "../config.php" );	//	vklucuvanje na bazata
	DB_CONN();
			
	if (!empty($_GET['ID']) && !empty($_POST['company']) && !empty($_POST['modul']) && !empty($_POST['modul_type']) && !empty($_POST['noi']) && !empty($_POST['noo'])
		&& !empty($_POST['nos']) && !empty($_POST['recog']) && !empty($_POST['nost'])&& !empty($_POST['recogt']) && 
		!empty($_POST['noc']) && !empty($_POST['tolerance']) && !empty($_POST['nofv'])&& 
		!empty($_POST['ip']) && !empty($_POST['port']) && !empty($_POST['date'])) {

		if (!is_numeric($_GET['ID'])) $ERROR="No module selected!"; 
		$ID = mysql_real_escape_string($_GET['ID']);
		$company = mysql_real_escape_string($_POST['company']);	// cistenje na vleznite podatoci
		$modul = mysql_real_escape_string($_POST['modul']);
		$modul_type = mysql_real_escape_string($_POST['modul_type']);
		$noi = mysql_real_escape_string($_POST['noi']);	
		$noo = mysql_real_escape_string($_POST['noo']);
		$nos = mysql_real_escape_string($_POST['nos']);	
		$recog = mysql_real_escape_string($_POST['recog']);
		$nost = mysql_real_escape_string($_POST['nost']);
		$recogt = mysql_real_escape_string($_POST['recogt']);	
		$noc = mysql_real_escape_string($_POST['noc']);
		$tolerance = mysql_real_escape_string($_POST['tolerance']);	
		$nofv = mysql_real_escape_string($_POST['nofv']);
		$ip = mysql_real_escape_string($_POST['ip']);
		$port = mysql_real_escape_string($_POST['port']);	
		$date = mysql_real_escape_string($_POST['date']);
		
			$res = mysql_query("UPDATE `modules` SET `Company`='".$company."', `Module`='".$modul."', `modul_type`='".$modul_type."', `noi`='".$noi."', `noo`='".$noo."', `nos`='".$nos."', `recog`='".$recog."', `nost`='".$nost."', `recogt`='".$recogt."', `noc`='".$noc."', `tolerance`='".$tolerance."', `nofv`='".$nofv."', `ip`='".$ip."', `port`='".$port."', `date`='".$date."' WHERE `id`='".$ID."'");
		
			if (!$res) die(mysql_error());
			$ERROR = "Module udpated successfully!";
			mysql_query("INSERT INTO `log` (`info`) VALUES ('Module updated by user: ".$theUser['user']."')");

	}else $ERROR="Please fill in all fields!";
	
	if (empty($_GET['ID']))
	{ $ERROR="No module selected!"; }
	else 
	{
		$theId = $_GET['ID'];
		if (is_numeric($theId))
		{
			
		$result = mysql_query("SELECT * FROM `modules` WHERE `id`='".mysql_real_escape_string($theId)."'");
		$row = mysql_fetch_array($result);
			$_POST['company'] = ($row['Company']);	// cistenje na vleznite podatoci
			$_POST['modul'] = ($row['Module']);
			$_POST['modul_type'] = ($row['modul_type']);
			$_POST['noi'] = ($row['noi']);	
			$_POST['noo'] = ($row['noo']);
			$_POST['nos'] = ($row['nos']);	
			$_POST['recog'] = ($row['recog']);
			$_POST['nost'] = ($row['nost']);
			$_POST['recogt'] = ($row['recogt']);	
			$_POST['noc'] = ($row['noc']);
			$_POST['tolerance'] = ($row['tolerance']);	
			$_POST['nofv'] = ($row['nofv']);
			$_POST['ip'] = ($row['ip']);
			$_POST['port'] = ($row['port']);	
			$_POST['date'] = ($row['date']);
		}
	}
	
	
	if ($_GET['action'] == "do")
	if ($ERROR != "") die($ERROR); else die("Unknown error");
?>

<script>
$(function() {
		$( "#date" ).datepicker();
	});
function UpModuleF() 
{
	formdata = $(":input").serialize();
	$.post('upModule.php?action=do&ID=<?php echo $_GET['ID']; ?>', formdata, function(data) {
  	ShowAlert("Info", data);
	if (data == "Module udpated successfully!" ) 
	{
		$("#modul_wrap").load('getModules.php?ID='+Selected);
		$("#dialog").dialog( "close" );
	}
	else if (data =="No module selected!") $("#dialog").dialog( "close" );
});
return;
}
</script>
<div id="mcont" style="margin:0 auto; width:450px;">

<table>
<form id="mform" action="/" method="post">
<tr><td width="150">
<label for="company">Company:</label></td><td width="250"><input type="text" name="company" id="company" size="40" value="<?php echo $_POST['company']; ?>" /></td></tr>
<tr><td>
<label for="modul">Module name:</label></td><td width="250"><input type="text" name="modul" id="modul" size="40" value="<?php echo $_POST['modul']; ?>" /></td></tr>
<tr><td>
<label for="modul_type">Module type:</label></td><td width="250">

<select name="modul_type" id="modul_type">
<option value="pat_rec" <?php if ($_POST['modul_type'] == "pat_rec") echo "selected='selected'"; ?>>pat_rec</option>
<option value="forecas" <?php if ($_POST['modul_type'] == "forecas") echo "selected='selected'"; ?>>forecas</option>
</select>

</td></tr>
<tr><td>
<label for="noi">Number of inputs (pat.):</label></td><td width="250"><input type="text" name="noi" id="noi" size="40" value="<?php echo $_POST['noi']; ?>" /></td></tr>
<tr><td>
<label for="noo">Number of outputs (pat.):</label></td><td width="250"> <input type="text" name="noo" id="noo" size="40" value="<?php echo $_POST['noo']; ?>" /></td></tr>
<tr><td>
<label for="nos">Num. of samples (pat.) - training:</label></td><td width="250"><input type="text" name="nos" id="nos" size="40" value="<?php echo $_POST['nos']; ?>" /></td></tr>
<tr><td>
<label for="recog">Recognition % - training (pat.):</label></td><td width="250"><input type="text" name="recog" id="recog" size="40" value="<?php echo $_POST['recog']; ?>" /></td></tr>
<tr><td>
<label for="nost">Num. of samples - testing (pat.):</label></td><td width="250"><input type="text" name="nost" id="nost" size="40" value="<?php echo $_POST['nost']; ?>" /></td></tr>
<tr><td>
<label for="recogt">Recognition % - testing (pat.):</label></td><td width="250"><input type="text" name="recogt" id="recogt" size="40" value="<?php echo $_POST['recogt']; ?>" /></td></tr>
<tr><td>
<label for="noc">Num. of columns (fore.):</label></td><td width="250"><input type="text" name="noc" id="noc" size="40" value="<?php echo $_POST['noc']; ?>" /></td></tr>
<tr><td>
<label for="tolerance">Tolerance +/- (fore.):</label></td><td width="250"><input type="text" name="tolerance" id="tolerance" size="40" value="<?php echo $_POST['tolerance']; ?>" /></td></tr>
<tr><td>
<label for="nofv">Num. of forecasting values (fore.):</label></td><td width="250"><input type="text" name="nofv" id="nofv" size="40" value="<?php echo $_POST['nofv']; ?>" /></td></tr>
<tr><td>
<label for="ip">IP address:</label></td><td width="250"><input type="text" name="ip" id="ip" size="40" value="<?php echo $_POST['ip']; ?>" /></td></tr>
<tr><td>
<label for="port">Port:</label></td><td width="250"><input type="text" name="port" id="port" size="40" value="<?php echo $_POST['port']; ?>" /></td></tr>
<tr><td>
<label for="date">Date:</label></td><td width="250"><input type="text" name="date" id="date" size="40" value="<?php echo $_POST['date'] ? $_POST['date']: date('m/d/Y'); ?>" /></td></tr>
</form>
<tr><td colspan="2" align="right"><button id="bcreate" onClick="UpModuleF()">Update module</button></td></tr>
</table>

</div>
