<?php
require("../session_check.php");

if ($theUser['access'] < 10) header("Location: ../login.php");

//print_r($theUser);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Users Admin Panel</title>
<link rel="shortcut icon" type="image/x-icon" href="../favicon.ico" />
<link rel="icon" type="image/x-icon" href="../favicon.ico" />
<link rel="stylesheet" href="../css/smoothness/jquery-ui-1.8.18.custom.css" type="text/css" media="all" />
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script src="../js/jquery-ui-1.8.18.custom.min.js" type="text/javascript"></script>
<script src="../js/jquery.blockUI.js" type="text/javascript"></script>
<style>
/*demo page css*/

			body{ font: 12px "Trebuchet MS", sans-serif; margin: 50px;}

			.demoHeaders { margin-top: 2em; }

			#dialog_link {padding: .4em 1em .4em 20px;text-decoration: none;position: relative;}

			#dialog_link span.ui-icon {margin: 0 5px 0 0;position: absolute;left: .2em;top: 50%;margin-top: -8px;}

			ul#icons {margin: 0; padding: 0;}

			ul#icons li {margin: 2px; position: relative; padding: 4px 0; cursor: pointer; float: left;  list-style: none;}

			ul#icons span.ui-icon {float: left; margin: 0 4px;}

#users_nav {
	height: 40px;
}
#users_nav button {
	width:115px;
	padding:5px;
}
#users_pages {
	width:138px; 
	margin:0 auto;
	border:#FFC medium groove;
	border-radius:3px;
	text-align:center;
	padding:5px;
	display:inline-block;
}

#black table {
	width:100%;
	text-align:center;
}
tr:hover {
	background-color:#FF9;
}
.active {
	background-color:#FC3;
}
#users {
	margin:40px 0;
}
#content {
	width: 960px;
	margin: 0 auto;
	text-align:center;
}
.bigButton {
	padding:10px;
	font-size:16px;
	width: 145px;
}
.moved { float:right; display:none; }
.green {background-color:#0C0;}
.red { background-color:#F00; }
</style>
<script>
var Selected=-1;
var SMod=-1;
	   
function ShowAlert(title, txt)
{
	if (!title) title = "Alert";
	$("#alertBox").dialog( "option", "title", title);
	$("#alert_cont").html(txt);
	$("#alertBox").dialog( "open" );
	return false;
}
function selectMe(me) {
	$("#modul_wrap").html("<h1>Loading modules...</h1><img src='../images/ajax-loader.gif' />");
	 Selected = $(me).attr("id");
	 $("#delete").removeAttr("disabled");
	 $("#manage").removeAttr("disabled");
	 $("#amod").removeAttr("disabled");
	 $("tr").removeClass("active");
	 $(me).addClass("active");
	 $("#modul_wrap").load('getModules.php?ID='+Selected);
	 }
<?php if (function_exists("pcntl_exec")) { ?> 
function CheckModBUttons()
{
$("#startmod").fadeOut();
	   $("#stopmod").fadeOut();
	 $.post("moduleEngine.php", { 'action': 'Check', 'module' : SMod }, function(data)
	{
		switch (data) {
			case "1": $("#startmod").removeAttr("disabled"); $("#startmod").fadeIn(); break;
			case "2": $("#stopmod").removeAttr("disabled"); $("#stopmod").fadeIn(); break;
			case "3": ShowAlert("Error", "Module not found!"); break;
			//default: ShowAlert("Error", data); 
		}
	});
}
<?php } ?>

function selectMod(me) {
	 SMod = $(me).attr("id");
	 <?php if (function_exists("pcntl_exec")) { ?> 
	  CheckModBUttons();
	<?php } ?>
	 $("#emod").removeAttr("disabled");
	 $("#dmod").removeAttr("disabled");
	 $("#modul_wrap tr").removeClass("active");
	 $(me).addClass("active");
	 }
function DeleteMe() {
	ShowConfirm("Confirm", "The selected user will be deleted!");
		$("#dialog-confirm").dialog({
   				close: function(event, ui) { if (CONFIRM=="YES") window.location = "?delete="+Selected;	}
		});
}
function ShowDialog(title, src)
{
	$("#d_cont").html("<h1>Loading modules...</h1><img src='../images/ajax-loader.gif' />");
	$("#dialog").dialog( "option", "title", title);
	$("#d_cont").load(src);
	$("#dialog").dialog( "open" );
	return false;
}
function addUser() {
	ShowDialog('Add User', 'register.php');
}
function ManageMe() {
	ShowDialog('Manage User', 'update.php?ID='+Selected);
}
function AddModule()
{
	ShowDialog('Add Module', 'addModule.php?ID='+Selected);
}
function EditModule()
{
	ShowDialog('Edit Module', 'upModule.php?ID='+SMod);
}
function DeleteModule()
{
	ShowConfirm("Confirm", "Are you sure you want to  delete the selected module? This cannot be undone!");
	$("#dialog-confirm").dialog({
			close: function(event, ui) { if (CONFIRM=="YES") window.location = "?remove="+SMod;}
	});
}
<?php if (function_exists("pcntl_exec")) { ?> 
function StartModule()
{
	$.post("../out/moduleEngine.php", { 'action': 'Start', 'module' : SMod }, function(data)
	{
		ShowAlert("Module info", data);
		CheckModBUttons();
	}).error(function() { ShowAlert("Module info", "Module started!"); });
}
function StopModule()
{
	$.post("moduleEngine.php", { 'action': 'Stop', 'module' : SMod }, function(data)
	{
		ShowAlert("Module info", data);
CheckModBUttons();
	});
}
<?php } ?>


$(function() {
		$( "#dialog" ).dialog({
			autoOpen: false,
			resizable: false,
			show: "fadeIn",
			hide: "fadeOut",
			width: 500,
			height: 600
		});
		$( "#dialog-confirm" ).dialog({
			autoOpen: false,
			resizable: false,
			height:175,
			modal: true,
			buttons: {
				"No": function() {
					CONFIRM="NO";
					$( this ).dialog( "close" );
				},
				"Yes": function() {
					CONFIRM="YES";
					$( this ).dialog( "close" );
				}
			}
		});

		$( "#alertBox" ).dialog({
			buttons: {
				Ok: function() {
					$( this ).dialog( "close" );
				}
			},
			resizable: false,
			autoOpen: false,
			show: "fadeIn",
			hide: "fadeOut",
			modal: true
		});
	});
function ShowConfirm(title, txt)
{
	CONFIRM="";
	if (!title) title = "Alert";
	$("#dialog-confirm").dialog( "option", "title", title);
	$("#confirm_cont").html(txt);
	$("#dialog-confirm").dialog( "open" );
	return false;
}
$(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);
</script>
</head>
<body>
<div id="alertBox" title="Basic dialog" style="display:none"><p id="alert_cont"> </p> </div>
<div id="dialog-confirm" title="Basic dialog" style="display:none"><p id="confirm_cont"> </p> </div>
<button style="float:right; height:50px" id="exit" onclick="window.location='./'">Exit to Admin panel</button>
<div id="dialog" title="Basic dialog" style="display:none">

	<div id="d_cont">
    <p>Loading...</p>
    </div>
	
</div>

<div id="content">
<h1>Users management panel</h1>
<br />

<button id="manage"  class="bigButton" disabled="disabled" onclick="ManageMe()">Manage User</button>
<button id="addUser" class="bigButton" onclick="addUser()">Add User</button>
<button id="delete" class="bigButton" disabled="disabled" onclick="DeleteMe()">Delete User</button>

<div id="users">
    <div id="users_nav">
        <div style="float:left">
            <button id="users_first">First Page</button>
            <button id="users_prev">Previous Page</button>
        </div>
        <div id="users_pages">Pages</div>
        <div style="float:right">
            <button id="users_next" disabled="disabled">Next Page</button>
            <button id="users_last" disabled="disabled">Last Page</button>
        </div>
    </div>
       
        
<?php

require( "../config.php" );	//	vklucuvanje na bazata
DB_CONN();
if (isset($_GET["delete"])) {
	mysql_query("DELETE FROM login WHERE `id`= '".$_GET["delete"]."'");
	?>
    <script type="text/javascript">
			window.location = "?";
	</script>
    <?php
}
if (isset($_GET["remove"])) {
	$rezTmp = mysql_query("SELECT `link` FROM `modules` WHERE `id`= '".$_GET["remove"]."'");
	mysql_query("DELETE FROM modules WHERE `id`= '".$_GET["remove"]."'");
	$tmpU = mysql_fetch_array($rezTmp);
	mysql_query("UPDATE `login` SET `Modules` = `Modules`-1 WHERE `id`='".$tmpU['link']."'");
	?>
    <script type="text/javascript">
			window.location = "?";
	</script>
    <?php
}
$ofs = "0";
if (isset($_GET["ofs"])) $ofs = $_GET["ofs"];

echo "<div id='black'>";

$m_res = mysql_query("SELECT * FROM `login` WHERE `access`='0'");
$rowMax = mysql_num_rows($m_res);


$result = mysql_query("SELECT * FROM `login` WHERE `access`='0' ORDER BY `Date` DESC limit $ofs, 30");
$number_of_rows = mysql_num_rows($result);


echo "<table border='1' bordercolor='#CCCCCC'><tr style='background-color: #CCC'><th>Company</th><th>Language</th><th>Num. of Modules</th><th>Username</th><th>Name & Surname</th><th>Telephone</th>
<th>Address</th><th>Post Code</th><th>Town</th><th>Country</th><th>Date</th></tr>";
             while ($row = mysql_fetch_array($result)) {

                  $DBuser = ($row['user']);	// cistenje na vleznite podatoci
				//  $pass = mysql_real_escape_string($row['pass']);
				  $modules = $row['Modules'];
				  $language = ($row['Language']);	
				  $company = ($row['Company']);
				  $name = ($row['Name']);	
				  $telephone = ($row['Telephone']);
				  $address = ($row['Address']);
				  $PostCode = ($row['PostCode']);	
				  $town = ($row['Town']);
				  $country = ($row['Country']);	
				  $date = ($row['Date']); 
		
                  ?>
                  
                  <tr onclick="selectMe(this)" id="<?php echo $row['id']; ?>">
                  <td><?php echo $company; ?></td>
                  <td><?php echo $language; ?></td>
                  <td id="mid<?php echo $row['id']; ?>"><?php echo $modules; ?></td>
                  <td><?php echo $DBuser; ?></td>
                  <td><?php echo $name; ?></td>
                  <td><?php echo $telephone; ?></td>
                  <td><?php echo $address; ?></td>
                  <td><?php echo $PostCode; ?></td>
                  <td><?php echo $town; ?></td>
                  <td><?php echo $country; ?></td>
                  <td><?php echo $date; ?></td>
                  </tr>
                  
                  <?php  }   ?>
       </table>
</div>
<script type="text/javascript">
	$('#users_pages').text('Page <?php echo (ceil($ofs/30)+1) . "/" . ceil($rowMax/30); ?>');
	
	$('#users_first').click( function () {
		window.location = "?<?php echo rand() . '=' . rand(); ?>";
	});
	
	$('#users_prev').click( function () {
		window.location =  "?ofs=<?php if ( $ofs-30 > 0) echo ceil($ofs-30); else echo "0"; ?>";
	});
	
	<?php if (ceil($rowMax/30) > 1) { ?>
		$('#users_next').removeAttr("disabled");
		$('#users_last').removeAttr("disabled");
	<?php } ?>
	$('#users_next').click( function () {
		window.location = "?ofs=<?php if ( $ofs+30 < $rowMax) echo ceil($ofs+30); else echo $ofs; ?>";
	});
	
	$('#users_last').click( function () {
		window.location = "?ofs=<?php echo ((ceil($rowMax/30)*30)-30) > 0 ? (ceil($rowMax/30)*30)-30 : 0; ?>";
	});
	
</script>
<br />
<button id="amod"  class="bigButton" disabled="disabled" onclick="AddModule()">Add Module</button>
<button id="emod" class="bigButton" disabled="disabled" onclick="EditModule()">Edit Module</button>
<button id="dmod" class="bigButton" disabled="disabled" onclick="DeleteModule()">Delete Module</button><br />
<br />
<?php if (function_exists("pcntl_exec")) { ?> 
<button id="startmod"  class="bigButton moved green" disabled="disabled" onclick="StartModule()">Start Module</button>
<button id="stopmod" class="bigButton moved red" disabled="disabled" onclick="StopModule()">Stop Module</button><br />
<?php } else echo "Server doesn't support external program execution!" ?>
<br /><br />


  <div id="modul_wrap">
  	<h1>No user selected</h1>
  </div>
</div>
</div>
</body>
</html>
