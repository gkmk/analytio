<?php
require("session_check.php");

if ($theUser['access'] == 10) header("Location: admin/");

require( "config.php" );	//	vklucuvanje na bazata
DB_CONN();
$_SESSION['ASN']="";$_SESSION['ASID']="";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link rel="shortcut icon" type="image/x-icon" href="favicon.ico" />
<link rel="icon" type="image/x-icon" href="favicon.ico" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Pattern Recognition</title>
<link href="css/analytio.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="css/smoothness/jquery-ui-1.8.18.custom.css" type="text/css" media="all" />

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script src="js/jquery-ui-1.8.18.custom.min.js" type="text/javascript"></script>
<script src="js/jquery.blockUI.js" type="text/javascript"></script>
<script src="js/analytio.js"></script>
<script src="js/jquery.generateFile.js"></script>
<script type="text/javascript" src="charts/swfobject.js"></script>
<script type="text/javascript" src="js/jscolor.js"></script>
<style>
/*demo page css*/

			body{ font: 12px "Trebuchet MS", sans-serif; }

			.demoHeaders { margin-top: 2em; }

			#dialog_link {padding: .4em 1em .4em 20px;text-decoration: none;position: relative;}

			#dialog_link span.ui-icon {margin: 0 5px 0 0;position: absolute;left: .2em;top: 50%;margin-top: -8px;}

			ul#icons {margin: 0; padding: 0;}

			ul#icons li {margin: 2px; position: relative; padding: 4px 0; cursor: pointer; float: left;  list-style: none;}

			ul#icons span.ui-icon {float: left; margin: 0 4px;}
</style>
<script>
$(function() {
		$( "#dialog" ).dialog({
			autoOpen: false,
			show: "fadeIn",
			hide: "fadeOut",
			width: 500,
			height: 450,
			modal: true
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
		$( "#dialog-confirm" ).dialog({
			autoOpen: false,
			resizable: false,
			height:190,
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
	});
function ShowDialog(title, src)
{
	$("#d_cont").html("<h1>Loading...</h1><img src='images/ajax-loader.gif' />");
	$("#dialog").dialog( "option", "title", title);
	$("#d_cont").load(src);
	$("#dialog").dialog( "open" );
	return false;
}
function ShowAlert(title, txt)
{
	if (!title) title = "Alert";
	$("#alertBox").dialog( "option", "title", title);
	$("#alert_cont").html(txt);
	$("#alertBox").dialog( "open" );
	return false;
}
function ShowConfirm(title, txt)
{
	CONFIRM="";
	if (!title) title = "Alert";
	$("#dialog-confirm").dialog( "option", "title", title);
	$("#confirm_cont").html(txt);
	$("#dialog-confirm").dialog( "open" );
	return false;
}
function UpdateFileList()
{
	var AddIt=true;
	var arr = $('#file_list option');
	arr.each(function(index, option) {
		if (UploadedFile.name == option.text)
		{
			AddIt=false;
			return;
		}
	});
	if (AddIt) {
		$("#file_list").append("<option class='unsafe' value='"+UploadedFile.name+"'>"+UploadedFile.name+"</option>");	
		jFiles.push(UploadedFile.name);
	}
}
function UpdateModuleComment()
{
	if ($("#comments").val() != "")
	{
	jSession.comment = $("#comments").val();
	$.post("addModuleComment.php?ID="+jSession.id, { comment: $("#comments").val() }, function (data){ ShowAlert('Session description',data); } );
	}
}
function ResetFileButtons(on)
{
	if (on){
		$("#BtransFile").removeClass("tinac");
		$("#BtransFile").removeAttr("disabled");
		$("#BdelFile").removeClass("inactive");
		$("#BsaveFile").removeClass("inactive");
		if (!newSession)	$("#bExCSV").removeAttr("disabled");
	} else {
		$("#BtransFile").addClass("tinac");
		$("#BtransFile").attr("disabled","disabled");
		$("#BdelFile").addClass("inactive");
		$("#bExCSV").attr("disabled","disabled");
		$("#BsaveFile").addClass("inactive");
	}
}
function GetSelectCol()
{
	var tmpCols="";
	var arr = $('#col_list option');
	arr.each(function(index, option) {
		if (option.selected)
		{
			tmpCols += option.text+"|";
		}
	});
	return tmpCols;
}
var FileSJQ;
var SelFileNamTmp="";
function selectFile(on) 
{
	SelFileNamTmp = on.options[on.selectedIndex].text;
	FileSJQ = on.options[on.selectedIndex];
	$("#selected_file").text(SelFileNamTmp);
	
	if (SelFileNamTmp != "")
		ResetFileButtons(true);
}
var SelectedLine=1;

function selectMe(me, event)
{
if (event.ctrlKey==1)
  {
	  if ($(me).attr("id") != "r0")
		$(me).addClass("active");
	SelectedLine = "";
	if (event.altKey==1)
	{
		var prv = $('.active th').first().text();
		var last = $('.active th').last().text();
		console.log("P"+prv+" L"+last);
		for (var i=prv; i<=last; i++) {	
			if (! $("#r"+i).hasClass("active"))
				$("#r"+i).addClass("active");
		}
		console.log(SelectedLine);
	}
	var arr = $('.active th');
	arr.each(function(index, tritem) {
		SelectedLine += tritem.innerHTML+"|";
	});
	if (arr.length > 1)	{
		$("#bRedSelRows").removeAttr("disabled");
		$("#bRedSelRows1").removeAttr("disabled");
	}
  }
else
  {
  $("#theTable tr").removeClass("active");
	$(me).addClass("active");
	SelectedLine = $(".active th").text();
	$("#bRedSelRows").attr("disabled","disabled");
	$("#bRedSelRows1").attr("disabled","disabled");
  }
}

var ColsColors=[]
var SelColorInd=0;
function SelColor(me)
{
	SelColorInd = me.selectedIndex;
	if (ColsColors[SelColorInd] != "") {
		//$("#theColorInp").val(ColsColors[SelColorInd]);
		$("#theColorInp").css("background-color", "#"+ColsColors[SelColorInd]);
	}
}
function SetColor(me)
{
	on = document.getElementById("line_color");
	jq = on.options[SelColorInd];
	$(jq).css("background-color", "#"+$("#myValueColor").val());
	ColsColors[SelColorInd]=$("#myValueColor").val();
}

var UploadedFile;
$(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);

var AutoSaveTimer = setTimeout("UpdateSession()", 600000);

</script>
</head>

<body>

<div id="dialog-confirm" title="Basic dialog" style="display:none"><p id="confirm_cont"> </p> </div>
<div id="alertBox" title="Basic dialog" style="display:none"><p id="alert_cont"> </p> </div>
<div id="dialog" title="Basic dialog" style="display:none">	<div id="d_cont">    <p>Loading...</p>    </div></div>

<div id="main_cont">

<div id="left_frame">
	<?php include("left_frame.php"); ?>
</div> <!-- END OF LEFT FRAME -->

<div id="center_frame">
<div id="alertNfo"></div>
<h2><img src="images/Analytio_Logo.jpg" alt="logo" style="position:relative; top:12px; left:-10px"/> Pattern Recognition</h2>
<h2>Session name: <span id="sessionName">Session01</span></h2>
<div id="grapharea">
<h1>Loading chart...</h1>
</div>

<p>Patterns found in <input id="pattFound" readonly="readonly" type="text" size="10" value="0"/>
out of <input id="pattOut" readonly="readonly" type="text" size="10" value="0"/> samples, or: 
<input id="pattPerc" readonly="readonly" type="text" size="4" value="0"/>%</p>
<p>File name: <span id="filespan"></span></p>
<div id="tableControl">
<button id="bComAll" disabled="disabled" onclick="PMatchAll(this)">Compute All</button>
<button id="bComStep" disabled="disabled" onclick="PMatchLine(this)">Compute Step-by-Step</button>
<button id="bRedSelRows" disabled="disabled" onclick="RedSelRows(this)">Redraw selected rows</button>
<button id="bRedAllRows" disabled="disabled" onclick="RedAllRows(this)">Redraw all rows</button>
<button id="bResetGraph" disabled="disabled" onclick="ResetGraph(this)">Reset the graphs and pie chart</button></div><br />
<p>* Hold Ctrl+Alt to select multiple rows in the table</p>
<div id="theTable"></div>
</div> <!-- END OF CENTER FRAME -->

<div id="right_frame">
<h2>Welcome <span id="userName"><?php echo $theUser['user']; ?></span></h2>
<button title="Back to previous screen" onclick="exitApp('./')">Back to previous screen</button>
<button title="Log out" onclick="exitApp('logout.php')">Log out</button><hr />
<p>Found patterns:</p>
<div id="piechart"></div>
<p align="left">Patterns found in <input id="pattFound1" readonly="readonly" type="text" size="10" value="0"/>
out of <input id="pattOut1" readonly="readonly" type="text" size="10" value="0"/> samples, or: 
<input id="pattPerc1" readonly="readonly" type="text" size="4" value="0"/>%</p>
<hr/>
<p>Select which Inputs, Outputs or a Pattern_recognition_result line to display</p>
<select name="col_list" size="7" id="col_list" multiple="multiple"></select>
<input type="checkbox" id="checkAllCols" onchange="selectAllOptions('col_list', this)"/><label for="checkAllCols">Select All</label>
<p style="text-align:left">* Hold "Shift" to select multiple<br />
** Pattern_recognition_result is by default selected<br />
***Press a button to redraw the selected / all rows!</p>

<button id="bRedSelRows1" disabled="disabled" onclick="RedSelRows(this)">Redraw selected rows</button>
<button id="bRedAllRows1" disabled="disabled" onclick="RedAllRows(this)">Redraw all rows</button>
<hr/>
<p>Select line color: <select name="line_color" id="line_color" onchange="SelColor(this)"></select><br />
<input id="theColorInp" size="4" style="height:20px; margin-top:10px" class="color {valueElement:'myValueColor'}"><input type="hidden" onchange="SetColor(this)" id="myValueColor"></p>
<hr/>
<button disabled="disabled" id="bExCSV" title="Export the table as .csv File" onclick="ExportCSV()">Export the table as .csv File</button>
</div> <!-- END OF RIGHT FRAME -->

<div class="clear"></div>
</div> <!-- END OF MAIN CONT -->
<script>
$("#main_cont").css("width", parseInt($("#main_cont").css("width"),10) );
$("#center_frame").css("width", parseInt($("#main_cont").css("width"),10)-460 );
SetChartW();createPieChart();
ResetTable();
</script>
</body>
</html>
