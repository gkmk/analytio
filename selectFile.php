<?php
require("session_check.php");
?>
<link href="css/styles.css" rel="stylesheet" type="text/css">

<form id="upload" action="upload.php" method="POST" enctype="multipart/form-data">

<fieldset>
<legend>Load input file (over secure connection)</legend>

<input type="hidden" id="MAX_FILE_SIZE" name="MAX_FILE_SIZE" value="300000" />

<div>
	<input type="file" id="fileselect" name="fileselect[]" />
	<div id="filedrag">or drop file here</div>
</div>

<div id="submitbutton">
	<button type="submit">Upload Files</button>
</div>

</fieldset>

</form>

<div id="messages"></div>

<script src="js/filedrag.js"></script>
<hr/>
<table width="100%" border="0">
  <tr>
    <td width="300">Number of Inputs/Outputs by the selected file:</td>
    <td><input type="text" id="LFnoi"  size="3" value="" readonly/>/
<input type="text" id="LFnoo" size="3" value="" readonly/></td>
  </tr>
</table>
<hr/>
<table width="100%" border="0">
  <tr>
    <td width="300">Number of Inputs/Outputs by currently selected module:</td>
    <td><input type="text" id="CMnoi"  size="3" value="" readonly/>/
<input type="text" id="CMnoo" size="3" value="" readonly/></td>
  </tr>
</table>
<hr/>
<div style="float:right">
<button onClick="SFUploadFile(this)" id="SFok" disabled style="width:80px; padding:5px">Ok</button>
<button onClick="$('#dialog').dialog( 'close' );" style="width:80px; padding:5px">Cancel</button>
</div>
<div style="clear:both"></div>
<hr/>
<div id="procent">Progress: 0%</div>
<div id="progress"></div>
