<button onclick="createNewSession()" style="width:140px">New Session</button><br />

<select id="sessin_box">
<option value='0'>default</option>
<?php
//$Sesija=array();
$JSes=array();
$resSession = mysql_query("SELECT * FROM `session` WHERE `link`='".$theUser['id']."'"); 

while ($rowSession = mysql_fetch_assoc($resSession)) {
		echo "<option value='".$rowSession['id']."'>".$rowSession['name']."</option>";
		//if (empty($Sesija)) $Sesija = $rowSession;
		$JSes[] = $rowSession;
}
?>
</select>
<br />
<button onclick="loadSession()" style="width:200px">Load Session</button><br />
<button id="saveSessionB" onclick="saveSession()" style="width:200px">Save Session</button><br />
<button id="delSessionB" onclick="delSession()" style="width:140px">Delete Session</button>
<hr/>
<p>Select a module:</p>
<?php
$Modul=array();
$JMod=array();
$resModule = mysql_query("SELECT * FROM `modules` WHERE `link`='".$theUser['id']."'");
?>
<select id="module_box" onChange="SelectModule(this)">
<?php
if (mysql_num_rows($resModule) < 1) echo "<option value='0'>No modules found</option>";
else {
	
	while ($rowModule = mysql_fetch_assoc($resModule)) {
		echo "<option value='".$rowModule['id']."'>".$rowModule['Module']."</option>";
		if (empty($Modul)) $Modul = $rowModule;
		$JMod[] = $rowModule;
	}
}
?>
</select>
<script>
jModules = <?php echo json_encode($JMod); ?>;
jSession = <?php echo json_encode($JSes); ?>;
console.log(jModules);
console.log(jSession);
</script>
<button id="bSelectModule" onclick="activateModule(this)">Select Module</button>
<table width="100%" border="0">
  <tr>
    <td width="150">Number of Inputs/ Outputs:</td>
    <td align="left"><input type="text" id="noi"  size="3" value="<?php echo $Modul['noi']; ?>" readonly/>/
<input type="text" id="noo" size="3" value="<?php echo $Modul['noo']; ?>" readonly/></td>
  </tr>
</table>

<hr/>
<table width="100%" border="0">
  	<tr><td width="150">Number of samples by training:</td>
    <td align="left"><input type="text" id="recog" size="3" value="<?php echo $Modul['recog']; ?>" readonly/>%</td></tr>
    <tr><td width="150">Pattern recognition ratio by training:</td>
    <td align="left"><input type="text" id="nos" size="3" value="<?php echo $Modul['nos']; ?>" readonly/>%</td></tr>
</table>

<hr/>
<table width="100%" border="0">
  <tr>
    <td width="150">Number of samples by testing:</td>
    <td align="left"><input type="text" id="nost" size="3" value="<?php echo $Modul['nost']; ?>" readonly/>%</td></tr>
    <tr><td width="150">Pattern recognition ratio by testing:</td>
    <td align="left"><input type="text" id="recogt" size="3" value="<?php echo $Modul['recogt']; ?>" readonly/>%</td>
  </tr>
</table>
<hr/>
<p>Files in session database:</p>
<select name="file_list" size="5" id="file_list" onchange="selectFile(this)" style="width:200px"></select>
<p>Current file: <span id="selected_file">None</span></p>
<div style="border:thin #003 solid; border-radius:10px; padding:3px; width:150px; margin:0 auto;">
<button id="BloadFile" class="icon loadFile" title="Load file" onclick="ShowDialog('Load File', 'selectFile.php')"></button>
<button id="BsaveFile" class="icon saveFile inactive" title="Save file in session database" onclick="SaveFileDB()" ></button>
<button id="BdelFile" class="icon delFile inactive" title="Delete file from database" onclick="DelFileDB()"></button>
<button id="BtransFile" disabled="disabled" class="trasnfer tinac" title="Transfer data from the selected input file into the table" onclick="TransferFile()"></button>
</div>
<hr/>
<p>Session description & comments:</p>
<textarea style="resize: none;" id="comments" cols="25" rows="5"></textarea><br />
<button onclick="UpdateModuleComment()">Save session description & comments</button>
