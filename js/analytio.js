// JavaScript Document
var SessionName = "";
var SelectedFileName = "";
var newSession = true;
var modIndex=0;
var sesIndex=0;
var sessionID;
var moduleID;
var jModules;
var jSession;
var jFiles = [];
var StatusTimeID;
var TotalLines=0;
var Columns=[];
var XHRmAll;
var CONFIRM="";
var LoadedRES=false;
var SESSION_CHANGED=false;
/*	*************************************************************** */

function ResetTable() {
	$("#theTable").empty();
	$("#theTable").html("<table>");
	for (i=0; i<10; i++) {
		 $("#theTable").append("<tr style='width:100%'><td style='width:40px'>&nbsp;</td><td style='width:100%'>&nbsp;</td></tr>");
	}
	$("#theTable").append("</table>");
}
/*	*************************************************************** */

function MinReset()
{
	$("#bRedSelRows").attr("disabled","disabled");
 $("#bRedSelRows1").attr("disabled","disabled");
		  $("#bRedAllRows").attr("disabled","disabled");
		  $("#bRedAllRows1").attr("disabled","disabled");
		  $("#bResetGraph").attr("disabled","disabled");
		  $("#bComAll").attr("disabled","disabled");
		  $("#bComStep").attr("disabled","disabled");
	$("#bExCSV").attr("disabled","disabled");
}

function resetPercents()
{
	$("#pattFound").val("0");
	$("#pattOut").val("0");
	$("#pattPerc").val("0");
	$("#pattFound1").val("0");
	$("#pattOut1").val("0");
	$("#pattPerc1").val("0");
}

function MasterReset()
{
	SessionName = ""; SelectedFileName = ""; modIndex=0;
 sessionID=0; moduleID=0; jSession=0; jFiles = []; StatusTimeID=0; TotalLines=0;
 Columns=[]; XHRmAll=null; LoadedRES=false;
 $("#module_box").removeAttr("disabled");
 $("#bSelectModule").removeAttr("disabled");
 
 $("#bRedSelRows").attr("disabled","disabled");
 $("#bRedSelRows1").attr("disabled","disabled");
		  $("#bRedAllRows").attr("disabled","disabled");
		  $("#bRedAllRows1").attr("disabled","disabled");
		  $("#bResetGraph").attr("disabled","disabled");
		  $("#bComAll").attr("disabled","disabled");
		  $("#bComStep").attr("disabled","disabled");
	$("#bExCSV").attr("disabled","disabled");
	$("#file_list").empty();
	$("#col_list").empty();
	$("#line_color").empty();
	
	resetPercents();
	$("#sessionName").text("default");
	$("#filespan").text("");
	$("#selected_file").text("");

	$("#comments").val("");
	SetChartW();createPieChart();
		  ResetFileButtons();
}
/*	*************************************************************** */

function exitApp(w) 
{
if (SessionName != "" && SESSION_CHANGED) {
		ShowConfirm("Confirm", "Are you sure you want to exit?");
		$("#dialog-confirm").dialog({
   				close: function(event, ui) { if (CONFIRM=="YES") window.location=w; }
		});
	} else window.location=w;
}
/*	*************************************************************** */

function createChart(xml)
{
	if (!xml) xml = "charts/default.xml";
	else xml = xml;
	$.post("clientAjax.php?nocache="+(Math.random()), 
	  {
		  action: "GetFileSize",
		  params: xml
	  },
	  function(data)	{
		  var so = new SWFObject("charts/fc2.swf?dataUrl="+xml+ "?nocache="+(Math.random()),"graph",$("#center_frame").css("width"),"550","8","#FFFFFF");
		  so.addParam("scale", "noscale");
		  so.addParam("salign", "lt");
		  so.addParam("wmode", "opaque");
		  so.addParam("allowScriptAccess", "sameDomain");
		  so.write("grapharea");
		  
		  $('#grapharea').block({ 
			  message: '<h1>Please wait while data is redrawn...</h1>', 
			  css: { border: '3px solid #a00' } 
		  }); 
		  if (parseInt(data, 10) > 0) setTimeout("CloseBlockUI()", Math.floor( parseInt(data, 10) / 50) );
		  else setTimeout("CloseBlockUI()", 2000);	
	  });
	
}
/*	*************************************************************** */

function CloseBlockUI()
{
	$('#grapharea').unblock();
}

function createPieChart(xml)
{
	if (!xml) xml = "charts/pie.xml?nocache="+(Math.random());
	else xml = xml + "?nocache="+(Math.random());
	var so = new SWFObject("charts/fc2.swf?dataUrl="+xml,"graph","200","200","8","#FFFFFF");
	so.addParam("scale", "noscale");
	so.addParam("salign", "lt");
	so.addParam("wmode", "opaque");
	so.addParam("allowScriptAccess", "sameDomain");
	so.write("piechart");
}
/*	*************************************************************** */

function SetChartW(dolength,skip)
{
	var tmpFile;
	if (skip) tmpFile = "";
	else tmpFile = SelectedFileName;
	$.post("clientAjax.php?nocache="+(Math.random()), 
	  {
		  action: "SetChartW",
		  params: tmpFile+"^"+($("#center_frame").width()-80)
	  },
	  function(data)	{
		  if (data != "FAIL")
		  {
			  createChart(data);
		  }
		  else $("#grapharea").html("<h1>Failed to create chart</h1>");
	  });
}
/*	*************************************************************** */

function SetChartLength()
{
	$.post("clientAjax.php?nocache="+(Math.random()), 
	  {
		  action: "SetChartLength",
		  params: SelectedFileName
	  },
	  function(data)	{
		  if (data != "FAIL")
		  {
			  createChart(data);
		  }
		  else $("#grapharea").html("<h1>Failed to create chart</h1>");
	  });
}
/*	*************************************************************** */

function SelectModule(on) {
	//if (SessionName == "") createNewSession();
	if (on)
		modIndex = on.selectedIndex;
	if (jModules.length > 0)
	{
	moduleID = jModules[modIndex].id;
	$("#noi").val(jModules[modIndex].noi);$("#noo").val(jModules[modIndex].noo);
	$("#nos").val(jModules[modIndex].nos);$("#recog").val(jModules[modIndex].recog);
	$("#nost").val(jModules[modIndex].nost);$("#recogt").val(jModules[modIndex].recogt);
	//$("#comments").val(jModules[modIndex].comment);
	}
	SESSION_CHANGED=true;
}
/*	*************************************************************** */

function promtNewSes() {
	ShowAlert("New session", "Name the new session: <input type='text' id='newsesname'/>");
		$("#alertBox").dialog({
   				close: function(event, ui) { 
				var name=$("#newsesname").val();
				if (name!=null && name!="")
				  {
					  $.post("clientAjax.php?nocache="+(Math.random()), 
					  {
						  action: "CheckSessionName",
						  params: name
					  },
					  function(data)	{
						  if (data == "OK")
						  {
							  MasterReset();
							  ResetTable();
							  SessionName=name;
							  $("#sessionName").text(SessionName);
							  newSession=true;
						  }
						  else { ShowAlert("Error", data);
						  	$("#alertBox").dialog({
   								close: function(event, ui) {  promtNewSes() }});
						  }
					  });
				  } 
				}
		});
}
/*	*************************************************************** */

function createNewSession() {
	$("#bSelectModule").removeAttr("disabled");
	$("#module_box").removeAttr("disabled");
	if (SessionName != "" && SESSION_CHANGED) {
		ShowConfirm("Confirm", "Are you sure to loose the current session?");
		$("#dialog-confirm").dialog({
   				close: function(event, ui) { if (CONFIRM=="YES") promtNewSes();	}
		});
	}
	else promtNewSes();
}
/*	*************************************************************** */

function looseCurSes() {
		$("#bSelectModule").removeAttr("disabled");
	$("#module_box").removeAttr("disabled");
	var sesBox = document.getElementById("sessin_box");
	sesIndex = sesBox.selectedIndex;
		MasterReset();
		sessionID = sesBox[sesIndex].value;
		SessionName = sesBox[sesIndex].innerHTML;
		$.post("clientAjax.php?nocache="+(Math.random()), 
		{
			action: "LoadSession",
			params: sessionID
		},
		function(data)	{
			if (data != "Invalid Session!") 
			{
				newSession=false;
				SESSION_CHANGED=false;
				
				ResetTable();
				$("#module_box").empty();
				console.log(data);
				if (data[1])
				{
					//jModules = data[1];
					var tmp=0;
					for (tmp=0; tmp<jModules.length; tmp++) {
						if (jModules[tmp].Module != "")
						 $("#module_box").append("<option value='"+jModules[tmp].id+"'>"+jModules[tmp].Module+"</option>");
					}
				}
				
				if (data[0])
				{
					jSession = data[0];
					if (jSession.comment && jSession.comment != null)
						$("#comments").val(jSession.comment);
					if (jSession.files != "" && jSession.files != null)
					{
					jFiles = jSession.files.split(",");
					$("#file_list").empty();
					for (tmp=0; tmp<jFiles.length; tmp++) {
						if (jFiles[tmp] != "")
						 $("#file_list").append("<option value='"+jFiles[tmp]+"'>"+jFiles[tmp]+"</option>");
					}
					}
				}
				
				if (jSession.modul != "" && jSession.modul != null) {
					$("#bSelectModule").attr("disabled","disabled");
			  		$("#module_box").attr("disabled","disabled");
					modIndex=0;
					SelectModule();
				} 
				if (jSession.lastFile != "" && jSession.lastFile != null)
				{
					SelectedFileName = jSession.lastFile;
					//createChart("users/"+$("#userName").text()+"/"+SelectedFileName+"RESULT.xml");
					
					$("#selected_file").text(SelectedFileName);
					TransferFile(true);
					
				}
				
				$("#sessionName").text(SessionName);
			} else ShowAlert("Error", "Session failed to load!");
		}, 'json');
		
}
/*	*************************************************************** */

function loadSession() {
	var sesBox = document.getElementById("sessin_box");
	sesIndex = sesBox.selectedIndex;
	if (sesIndex == 0) return;

	if (SessionName != "" && SESSION_CHANGED) {
		ShowConfirm("Confirm", "Are you sure you want to loose the current session?");
		$("#dialog-confirm").dialog({
   				close: function(event, ui) { if (CONFIRM=="YES") looseCurSes(); }
		});
	}
	else  looseCurSes();
}
/*	*************************************************************** */

function LoadResults()
{
	$.post("clientAjax.php?nocache="+(Math.random()), 
		  {
			  action: "LoadResults",
			  params: SelectedFileName+"^"+($("#center_frame").width()-80)
		  },
		  function(data)	{
			  if (data == "" || !data || data == undefined) return;
			  var pmall = data.substring(0, 1);
			  data = data.slice(1);
			  if (pmall == "1") {
				  $("#bComAll").attr("disabled","disabled");
		  			$("#bComStep").attr("disabled","disabled");
			  }
			  var which = data.substring(0, 1);
			  if (which == "A") SetChartW(); //createChart("users/"+$("#userName").text()+"/"+SessionName+"/"+SelectedFileName+".xml");
			  else SetChartW(); //createChart("users/"+$("#userName").text()+"/"+SessionName+"/"+SelectedFileName+"SEL.xml");
			  createPieChart("users/"+$("#userName").text()+"/"+SessionName+"/"+SelectedFileName+"PIE.xml");
			  data = data.slice(1);
			  $("#bExCSV").removeAttr("disabled");
			  var colors = data.substring(0, (ColsColors.length*6));
			  data = data.slice((parseInt( ColsColors.length, 10)*6) );
			  
			  var $kids = $("#line_color").children();
			  for (var o=0; o<ColsColors.length; o++)
			  {
				  ColsColors[o] = colors.substring(o*6, (o*6)+6);
				  
				  $kids[o].style.backgroundColor = "#"+ColsColors[o];
			  }
			  
			 // $("#theColorInp").val(ColsColors[SelColorInd]);
				$("#theColorInp").css("background-color", "#"+ColsColors[SelColorInd]);
			  
			  if (!isNaN(data.substring(0, 1)))
			   {
				   LoadedRES=true;
				TotalLines=data.length;
				$("#pattOut").val(TotalLines);
				for (var i=0; i<TotalLines; i++)
				{
					if (data[i] == "1") {
						 $("#pattFound").val(parseInt($("#pattFound").val(), 10)+1)
						 $("#pattFound1").val(parseInt($("#pattFound1").val(), 10)+1)
					}
					$("#pdr"+(i+1)).text(data[i]);
				}
				if (parseInt($("#pattOut").val(), 10) > 0) {
				   var percent = (parseFloat($("#pattFound").val(), 10)*100)/parseFloat($("#pattOut").val());
				  $("#pattPerc").val(percent.toFixed(3));
				  $("#pattPerc1").val(percent.toFixed(3));
				}
			   }
			   $.post("clientAjax.php?nocache="+(Math.random()), 
				  {
					  action: "LoadLastIOS",
					  params: SelectedFileName
				  }, function(data)	{
					  var seltmp = data.split("|");
						for (var stmp in seltmp) {
							if (seltmp[stmp])
							$("#clist"+seltmp[stmp]).attr("selected", "selected");
						}
				  });
		  });
}
/*	*************************************************************** */

var ErrCNT=0;
function activateModule(on) 
{
	if (SessionName == "") { saveSession(true); return; }
	
	if (SessionName == "") ShowAlert("Error","You must first create a session!");
	else 
	{
		if (!moduleID)	moduleID = jModules[modIndex].id;
	  $.post("clientAjax.php?nocache="+(Math.random()), 
	  {
		  action: "SetModule",
		  params: SessionName+"^"+moduleID
	  },
	  function(data)	{
		  if (data == "No session selected!")
		  {
			  if (ErrCNT == 0)	  {
				  saveSession();
				  ErrCNT++;
				  activateModule();
				  return;
			  }
			  
		  }
		  if (data == "Module activated!")
		  {
			  SESSION_CHANGED=true;
//			  jSession.id = 
			  ErrCNT=0;
			  $("#bSelectModule").attr("disabled","disabled");
			  $("#module_box").attr("disabled","disabled");
			  ShowAlert("Info", data);
		  }else
		  ShowAlert("Error", data);
	  });
	}
}
/*	*************************************************************** */

function ShowAlertBox(txt)
{
	$("#alertNfo").clearQueue().html(txt).fadeIn('fast').fadeOut(5000);
}
/*	*************************************************************** */

function UpdateSession()
{
	var tmpSesName = SessionName;
	clearTimeout(AutoSaveTimer);
	if (tmpSesName == "")
	{
		AutoSaveTimer = setTimeout("UpdateSession()", 600000);
		return;
		
		tmpSesName = "temp";
		$.post("clientAjax.php?nocache="+(Math.random()), 
		{
			action: "SaveSession",
			params: tmpSesName
		}, function(){ 
			ShowAlertBox("<h1>Auto save session created!</h1>");
			if (SelectedFileName != "")
				{
					$(".unsafe").removeClass("unsafe");
					if ($("#filespan").text().substr(0,SelectedFileName.length) == SelectedFileName) {
						$("#filespan").text(SelectedFileName);
						$("#filespan").css("color", "black");
					}
				} 
			});
	}
	else {
	$.post("clientAjax.php?nocache="+(Math.random()), 
		{
			action: "UpdateSession",
			params: tmpSesName+"^"+moduleID+"^"+jFiles
		},
		function(data)	{
			if (data == "Session Saved!") {
				newSession=false;
				$(".unsafe").removeClass("unsafe");
				SESSION_CHANGED=false;
				if (SelectedFileName != "")
				{
					if ($("#filespan").text().substr(0,SelectedFileName.length) == SelectedFileName) {
						$("#filespan").text(SelectedFileName);
						$("#filespan").css("color", "black");
					}
				}
			}
			ShowAlertBox("<h1>"+data+"</h1>");
		});
	}
	AutoSaveTimer = setTimeout("UpdateSession()", 600000);
}
/*	*************************************************************** */

function delSession()
{
	var sesBoxNMPAL = document.getElementById("sessin_box");
	sesIndexNMPAL = sesBoxNMPAL.selectedIndex;
		sessionIDNMPAL = sesBoxNMPAL[sesIndexNMPAL].value;
		SessionNameNMPAL = sesBoxNMPAL[sesIndexNMPAL].innerHTML;
		
	if (SessionNameNMPAL == "default") return;	
		
	if (SessionNameNMPAL!=null && SessionNameNMPAL!="")
	{
		ShowConfirm("Delete session?", "Are you sure you want to delete the session? This cannot be undone!");
		$("#dialog-confirm").dialog({
   				close: function(event, ui) { 
				
				if (CONFIRM=="YES") $.post("clientAjax.php?nocache="+(Math.random()), 
		{
			action: "DelSession",
			params: SessionNameNMPAL
		},
		function(data)	{
			var arr = $('#sessin_box option');
			arr.each(function(index, option) {
				if (SessionNameNMPAL == option.text)
				{
					$(option).remove();
					return;
				}
			});
			ShowAlert("Info", data);
			if (SessionNameNMPAL == SessionName)
				{
					MasterReset();  ResetTable();
					newSession=true;
					SESSION_CHANGED=false;
				}
			});
		}
		});
	} else ShowAlert("Error", "No session selected!");
}
/*	*************************************************************** */

function saveSession(activMod)
{
	if (!newSession) { UpdateSession(); return; }
	if (SessionName == "")
	{
		ShowAlert("New session", "Name the new session: <input type='text' id='newsesname'/>");
		$("#alertBox").dialog({
   				close: function(event, ui) { 
					var name=$("#newsesname").val();
					if (name!=null && name!="") {
						SessionName=name;
						saveSession(activMod);
					}
				}
		});
		return;
	}
	if (SessionName!=null && SessionName!="")
	{
		$("#sessionName").text(SessionName);
		$.post("clientAjax.php?nocache="+(Math.random()), 
		{
			action: "SaveSession",
			params: SessionName
		},
		function(data)	{
			if (!isNaN( data )) {
				$("#sessin_box").append("<option value='"+data+"' selected='selected'>"+SessionName+"</option>");
				//$("#sessin_box").text(SessionName);
				if (SelectedFileName != "")
				{
					$(".unsafe").removeClass("unsafe");
					if ($("#filespan").text().substr(0,SelectedFileName.length) == SelectedFileName) {
						$("#filespan").text(SelectedFileName);
						$("#filespan").css("color", "black");
					}
				}
				if (jSession == "" || jSession == 0 || jSession == undefined) jSession = [];
				jSession.id = data;
				newSession=false;
				ShowAlert("Info", "Session Saved!");
				SESSION_CHANGED=false;
				if (SelectedFileName != "") SaveFileDB(true);
				if (activMod) activateModule();
			}
			else
			ShowAlert("Error", data);
		});
	}
}
/*	*************************************************************** */

function SaveFileDB(silent)
{
	var tmpfileNhold = SelFileNamTmp;
	if (silent) {
		SelFileNamTmp = SelectedFileName;
	} else
	if (SelFileNamTmp == "") {
		if (!silent)
		ShowAlert("Not ready", "Select file from the list!");
		return;
	}
	//if (SessionName == "") saveSession();
	
	if (SessionName == "") {
		if (!silent) ShowAlert("Error", "You must first create a session!");
	}
	else 
	{
	  $.post("clientAjax.php?nocache="+(Math.random()), 
	  {
		  action: "SaveFileDB",
		  params: SessionName+"^"+SelFileNamTmp
	  },
	  function(data)	{
		  if (data == "File saved!")
		  	{
				SESSION_CHANGED=true;
				on = document.getElementById("file_list");
				jq = on.options[on.selectedIndex];
				if ($(jq).hasClass("unsafe")) {
					$(jq).removeClass("unsafe");
				}
				if ($("#filespan").text().substr(0,SelFileNamTmp.length) == SelFileNamTmp) {
						$("#filespan").text(SelFileNamTmp);
						$("#filespan").css("color", "black");
					}
				if (!silent)	  ShowAlert("Info", data);
			}
		else if (!silent)	  ShowAlert("Error", data);
	  });
	}
	SelFileNamTmp = tmpfileNhold;
}
/*	*************************************************************** */

function DelFileDB()
{
	if (SelFileNamTmp == "") {
		ShowAlert("Not ready", "Select file from the list!");
		return;
	}
	//if (SessionName == "") saveSession();
	
	if (SessionName == "") ShowAlert("Error", "You must first create a session!");
	else 
	{
		ShowConfirm("Delete file?", "Are you sure you want to delete this file? This is permanent and can't be undone!");
		$("#dialog-confirm").dialog({
   				close: function(event, ui) { if (CONFIRM=="YES") {
		
				ResetTable();
			  $.post("clientAjax.php?nocache="+(Math.random()), 
			  {
				  action: "DelFileDB",
				  params: SessionName+"^"+SelFileNamTmp
			  },
			  function(data)	{
				  if (data == "File removed!")
				  {
					var tmpA = [];
					$("#file_list").empty();
					for (var tmpF in jFiles)
					  {
						  if (jFiles[tmpF] != SelFileNamTmp && jFiles[tmpF] != "" && jFiles[tmpF] != "0" && jFiles[tmpF] != "")
						  {
							  tmpA.push(jFiles[tmpF]);
							  $("#file_list").append("<option value='"+jFiles[tmpF]+"'>"+jFiles[tmpF]+"</option>");
						  }
					  }
					jFiles = tmpA;
					
					
					if (SelFileNamTmp == $("#filespan").text())
					{
						resetPercents();
						SetChartW(0, true);createPieChart();
						$("#col_list").empty();
						$("#line_color").empty();
						ResetFileButtons();
						 $("#filespan").text("");
						 SelectedFileName = "";
					}
					SelFileNamTmp="";
					
				  }
				  
				  ShowAlert("Error", data);
			  });
				}
			}
	  });
	}
}
/*	*************************************************************** */

function TransferFile(results, conf)
{
	SelectedFileName = $("#selected_file").text();
	if ($("#filespan").text().substr(0,SelectedFileName.length) == SelectedFileName) return;
	if (SelectedFileName == "") {
		ShowAlert("Error", "Select file from the list!");
		return;
	}
	if ($("#module_box").attr("disabled") != "disabled"){
		ShowAlert("Error", "Select module first!");
		return;
	}
	if (!conf || conf=="" || conf== undefined)
	{
		if ($("#filespan").text() != SelectedFileName && $("#filespan").text() != "" && !results)
		{
		ShowConfirm("Transfer new file?", "You are about to delete the previous data in the table and reset the graph and the pie chart !");
			$("#dialog-confirm").dialog({
				close: function(event, ui) { if (CONFIRM=="YES") {
					TransferFile(results, true);
					}
				}
			});
			return;
		}
	}
	
	$("#theTable").empty();
	
	
	$("#filespan").text(SelectedFileName);
	if ($(FileSJQ).hasClass("unsafe")) {
		$("#filespan").append(" (not saved)");
		$("#filespan").css("color", "red");
	} else $("#filespan").css("color", "black");
	
	 $.post("clientAjax.php?nocache="+(Math.random()), 
	  {
		  action: "TransferFile",
		  params: SelectedFileName
	  },
	  function(data)	{
		  
		   var pmall = data.substring(0, 1);
			  data = data.slice(1);
		  
		  resetPercents();
		  SetChartW(0, true);createPieChart();
		  SESSION_CHANGED=true;
		  $("#theTable").html(data);
		  $("#theTable tr").bind('mousedown', function(event) {  selectMe(this, event); });
		  
		  var total = $("#theTable tr");
		  $("#pattOut").val((parseInt(total.length,10)-1));
		  $("#pattOut1").val((parseInt(total.length,10)-1));
		  
		  $("#bRedAllRows").removeAttr("disabled");
		  $("#bRedAllRows1").removeAttr("disabled");
		  $("#bResetGraph").removeAttr("disabled");
		  if (pmall=="0")
		  {
		  	$("#bComAll").removeAttr("disabled");
		  	$("#bComStep").removeAttr("disabled");
		  } else {
			  $("#bComAll").attr("disabled","disabled");
		  		$("#bComStep").attr("disabled","disabled");
		  }
		  SelectedLine=1;
		  $("#r"+SelectedLine).addClass("active");
		  ColsColors=[];
		  $("#col_list").empty(); $("#line_color").empty();
			var arr = $('#r0 th');
		  arr.each(function(index, tritem) {
			  var tmpTD = tritem.innerHTML;
			  if (tmpTD != "Row N.")
			  {
			  Columns.push(tmpTD);
			  
			  if (index == arr.length-1) {
				  ColsColors.push("0020C2");
				  rndColor="0020C2";
			  }
			  else {
				  var rndColor = "";
				  for (ik=0; ik<6; ik++) {
						rndColor += Number(Math.floor((Math.random()*15)+1)).toString(16);
					}
					
				  ColsColors.push(rndColor);
			  }
			  
			 	$("#myValueColor").val(rndColor);
				$("#theColorInp").css("background-color", "#"+rndColor);
				SelColorInd=index-1;
				
			  if (tmpTD == "Pattern_recognition_result") {
				   $("#col_list").append("<option selected='selected' id='clist"+tmpTD+"' value='"+tmpTD+"'>"+tmpTD+"</option>");
				   $("#line_color").append("<option selected='selected' style='background-color:#"+rndColor+"' value='"+tmpTD+"'>"+tmpTD+"</option>");
			  }
			  else {
				  $("#col_list").append("<option id='clist"+tmpTD+"' value='"+tmpTD+"'>"+tmpTD+"</option>");
			  		$("#line_color").append("<option style='background-color:#"+rndColor+"' value='"+tmpTD+"'>"+tmpTD+"</option>");
			  }
			  
			  }
		  });
		  if (results)		  LoadResults();
	  });
}
/*	*************************************************************** */

function selectAllOptions(id, me)
{
	var ref = document.getElementById(id);

	if (me.checked)
	{
		for(i=0; i<ref.options.length; i++)
		ref.options[i].selected = true;
	} else 
	{
		for(i=0; i<ref.options.length; i++)
		ref.options[i].selected = false;
	}
}
/*	*************************************************************** */

function PMatchLine(me)
{
	if (SelectedFileName == "") {
		ShowAlert("Error", "Select file from the list!");
		return;
	}
	var tmp = $("#pdr"+SelectedLine).text();
	 $.post("clientAjax.php?nocache="+(Math.random()),
	  {
		  action: "PMatchLine",
		  params: SelectedFileName+"^"+SelectedLine+"^"+moduleID
	  },
	  function(data)	{
		  if (isNaN(data)) {
			  ShowAlert("Error", data);
			  MinReset();
			  createPieChart("charts/pie.xml");
			  return;
		  }
		  else 
		  {
			  SESSION_CHANGED=true;
			  $("#pdr"+SelectedLine).text(data);
			  if (tmp != "1" && data == "1" && !LoadedRES) {
				  $("#pattFound").val(parseInt($("#pattFound").val(), 10)+1);
				  $("#pattFound1").val(parseInt($("#pattFound1").val(), 10)+1);
				  if (parseInt($("#pattOut").val(), 10) > 0) {
					var percent = (parseFloat($("#pattFound").val(), 10)*100)/parseFloat($("#pattOut").val());
					$("#pattPerc").val(percent.toFixed(3));
					$("#pattPerc1").val(percent.toFixed(3));
				  }
			  }
			   $("#r"+SelectedLine).removeClass("active");
			   SelectedLine++;
			   
			   var redLines="";
			   for (var j=1; j<parseInt(SelectedLine, 10); j++){
				   redLines += j+"|";
			   }
			   
			   $("#r"+SelectedLine).addClass("active");
			   createPieChart("users/"+$("#userName").text()+"/"+SessionName+"/"+SelectedFileName+"PIE.xml");
			   $.post("clientAjax.php?nocache="+(Math.random()),
				{
					action: "PRedrawSelected",
					params: SelectedFileName+"^"+GetSelectCol()+"^"+redLines+"^"+($("#center_frame").width()-80)+"^"+ColsColors
				},
				function(data)	{
					if (data.substr(0,1) == "@") createChart(data.slice(1));
				});
		  }
	  });
}
/*	*************************************************************** */

function PMatchAll(me)
{
	if (SelectedFileName == "") {
		ShowAlert("Error", "Select file from the list!");
		return;
	}
	$(me).attr("disabled", "disabled");

	$("#pattFound").val("0");$("#pattPerc").val("0");
	$("#pattFound1").val("0");$("#pattPerc1").val("0");
	XHRmAll = $.post("clientAjax.php?nocache="+(Math.random()), 
	  {
		  action: "PMatchAll",
		  params: SelectedFileName+"^"+moduleID+"^"+($("#center_frame").width()-80)
	  },
	  function(data)	{
			$(me).removeAttr("disabled");
		 if (isNaN(data.substring(0, 1)))
		 {
			 ShowAlert("Error", data);
			 MinReset();
			 return;
		 }
		 SESSION_CHANGED=true;
		  $("#bComStep").attr("disabled", "disabled");
		  $(me).attr("disabled","disabled");
		  TotalLines=data.length;
		  for (var i=0; i<data.length; i++)
		  {
			  if (data[i] == "1") {
				   $("#pattFound").val(parseInt($("#pattFound").val(), 10)+1)
				   $("#pattFound1").val(parseInt($("#pattFound1").val(), 10)+1)
			  }
			  $("#pdr"+(i+1)).text(data[i]);
		  }
		  $("#bExCSV").removeAttr("disabled");
		  if (parseInt($("#pattOut").val(), 10) > 0) {
			   var percent = (parseFloat($("#pattFound").val(), 10)*100)/parseFloat($("#pattOut").val());
			  $("#pattPerc").val(percent.toFixed(3));
			  $("#pattPerc1").val(percent.toFixed(3));
		  }

		  //createChart("users/"+$("#userName").text()+"/"+SessionName+"/"+SelectedFileName+"RESULT.xml");
		  createPieChart("users/"+$("#userName").text()+"/"+SessionName+"/"+SelectedFileName+"PIE.xml");
			   $.post("clientAjax.php?nocache="+(Math.random()),
				{
					action: "PRedrawAll",
					params: SelectedFileName+"^"+GetSelectCol()+"^"+($("#center_frame").width()-80)+"^"+ColsColors
				},
				function(data)	{
					if (data.substr(0,1) == "@") createChart(data.slice(1));
				});
		  createPieChart("users/"+$("#userName").text()+"/"+SessionName+"/"+SelectedFileName+"PIE.xml");
	  });
	  timeD=0;
	  $("#blockBox").html("<h1>Computing all...</h1><img src='images/ajax-progres.gif'/><button onclick='KillMatchAll()'>Cancel Compute All</button><p id='timeD'></p>");
	 StatusTimeID = setTimeout("PMatchStatus()", 1001);
}
/*	*************************************************************** */

function KillMatchAll() {
	if (XHRmAll)	XHRmAll.abort()
	$("#bComAll").removeAttr("disabled");
}
/*	*************************************************************** */
var timeD;
function PMatchStatus()
{
	timeD++;
	clearTimeout(StatusTimeID);
	
	sec = parseInt(timeD%60, 10); if (sec<10) sec="0"+sec;
	minuts = parseInt((timeD/60)%60, 10);if (minuts<10) minuts="0"+minuts;
	hours = parseInt((timeD/3600)%12, 10);if (hours<10) hours="0"+hours;
	$("#timeD").html("<h3>Time elapsed "+hours+":"+minuts+":"+sec+"</h3>");
	
	if (XHRmAll.readyState != 4) StatusTimeID = setTimeout("PMatchStatus()", 1001);
}
/*	*************************************************************** */

function ResetGraph(me)
{

	SetChartW(0, true);
	//$("#pattFound").val("0");
	//$("#pattOut").val("0");
	//$("#pattPerc").val("0");
	//$("#pattFound1").val("0");
	//$("#pattOut1").val("0");
	//$("#pattPerc1").val("0");
}
/*	*************************************************************** */

function RedAllRows(me) 
{
	if (SelectedFileName == "") {
		ShowAlert("Error", "Select file from the list!");
		return;
	}
	 $.post("clientAjax.php?nocache="+(Math.random()),
	  {
		  action: "PRedrawAll",
		  params: SelectedFileName+"^"+GetSelectCol()+"^"+($("#center_frame").width()-80)+"^"+ColsColors
	  },
	  function(data)	{
		 if (data.substr(0,1) != "@")ShowAlert("Chart error", data);
		  else createChart(data.slice(1)); 
	  });
}
/*	*************************************************************** */

function RedSelRows(me)
{
	if (SelectedFileName == "") {
		ShowAlert("Error", "Select file from the list!");
		return;
	}
	var tmps = SelectedLine.split("|");
	for (var i=1; i<tmps.length-1; i++) {
		if (parseInt(tmps[i],10) != parseInt(tmps[i-1], 10)+1) {
			ShowAlert("Error", "Selected rows are not conescutive one after another!");
			return;
		}
	}
	 $.post("clientAjax.php?nocache="+(Math.random()),
	  {
		  action: "PRedrawSelected",
		  params: SelectedFileName+"^"+GetSelectCol()+"^"+SelectedLine+"^"+($("#center_frame").width()-80)+"^"+ColsColors
	  },
	  function(data)	{
		  if (data.substr(0,1) != "@")ShowAlert("Chart error", data);
		  else createChart(data.slice(1));
	  });
}
/*	*************************************************************** */

function ExportCSV()
{
	if (SelectedFileName == "") {
		ShowAlert("Error", "Select file from the list!");
		return;
	}
	$.post("clientAjax.php?nocache="+(Math.random()),
	  {
		  action: "ExportCSV",
		  params: SelectedFileName
	  },
	  function(data)	{
		frs = data.substr(0,1);
		
		  if (frs != "@")ShowAlert("CSV Error", (data));
		  else {
			  data = data.slice(1);
			   $.generateFile({
				filename	: data,
				script		: 'download.php'
				});
			   ShowAlert("Info", "Downloading CSV file. Check your browser downloads.");
		  }
	  });
}
