/*
filedrag.js - HTML5 File Drag & Drop demonstration
Featured on SitePoint.com
Developed by Craig Buckler (@craigbuckler) of OptimalWorks.net
*/
var FileForUpload={};
var FileInputs;
var FileOutputs;
	// getElementById
	function $id(id) {
		return document.getElementById(id);
	}


	// output information
	function Output(msg) {
		var m = $id("messages");
		m.innerHTML = msg + m.innerHTML;
	}


	// file drag hover
	function FileDragHover(e) {
		e.stopPropagation();
		e.preventDefault();
		e.target.className = (e.type == "dragover" ? "hover" : "");
	}


	// file selection
	function FileSelectHandler(e) {

		// cancel event and hover styling
		FileDragHover(e);

		// fetch FileList object
		var files = e.target.files || e.dataTransfer.files;

		// process all File objects
		for (var i = 0, f; f = files[i]; i++) {
			ParseFile(f);
			//UploadFile(f);
		}

	}


	// output file information
	function ParseFile(file) {

		/*Output(
			"<p>File information: <strong>" + file.name +
			"</strong> type: <strong>" + file.type +
			"</strong> size: <strong>" + file.size +
			"</strong> bytes</p>"
		);*/
		
		if (file.name.substr(file.name.length-4, 4)== ".inp")
		{
		// display text
			var reader = new FileReader();
			reader.onload = function(e) {
				
				var contents = e.target.result;
				tmpI = contents.indexOf("\n");
				FileInputs =  $.trim(contents.substr(0, tmpI))
				$("#LFnoi").val(FileInputs);
				tmpI1 = contents.indexOf("\n", tmpI+1);
				FileOutputs =  $.trim(contents.substr(tmpI+1, tmpI1-tmpI));
				$("#LFnoo").val(FileOutputs);

				if ( FileOutputs == $("#noo").val() &&  FileInputs == $("#noi").val())
				{
					FileForUpload = file;
					console.log(FileForUpload);
					$("#messages").text("File for upload: "+file.name);
					$("#SFok").removeAttr("disabled");
				}
				else 
				{
					ShowAlert("Error", "Selected file has different number of inputs and outputs in comparison to the selected module!");
					$("#SFok").attr("disabled","disabled");
				}
			}
			if (file.webkitSlice) {
			  var blob = file.webkitSlice(0, 10);
			} else if (file.mozSlice) {
			  var blob = file.mozSlice(0, 10);
			}
			reader.readAsBinaryString(blob);
		}

	}

	// initialize
	function Init() {

		var fileselect = $id("fileselect"),
			filedrag = $id("filedrag"),
			submitbutton = $id("submitbutton");

		// file select
		fileselect.addEventListener("change", FileSelectHandler, false);

		// is XHR2 available?
		var xhr = new XMLHttpRequest();
		if (xhr.upload) {

			// file drop
			filedrag.addEventListener("dragover", FileDragHover, false);
			filedrag.addEventListener("dragleave", FileDragHover, false);
			filedrag.addEventListener("drop", FileSelectHandler, false);
			filedrag.style.display = "block";

			// remove submit button
			submitbutton.style.display = "none";
		}
		$("#CMnoo").val($("#noo").val());
		$("#CMnoi").val($("#noi").val());
	}

	// call initialization file
	if (window.File && window.FileList && window.FileReader) {
		Init();
	}

// upload JPEG files
	function SFUploadFile(me) {
		
		$(me).attr("disabled", "disabled");
		var xhr = new XMLHttpRequest();
		if (xhr.upload ){ //&& file.type == "image/jpeg" && file.size <= $id("MAX_FILE_SIZE").value) {

			// create progress bar
			var o = $id("progress");
			var progress = o.appendChild(document.createElement("p"));
			progress.appendChild(document.createTextNode(FileForUpload.name));

			// progress bar
			xhr.upload.addEventListener("progress", function(e) {
				var pc = parseInt((e.loaded / e.total) * 100);
				progress.style.backgroundPosition = pc + "% 0";
				$("#procent").text("File uploaded: "+pc+"%");
			}, false);

			// file received/failed
			xhr.onreadystatechange = function(e) {
				if (xhr.readyState == 4) {
					if (xhr.status == 200) {
						if (xhr.responseText == "File uploaded!")
						{
						  UploadedFile = FileForUpload;
						  ShowAlert("Info", "File uploaded successfully!");
						  UpdateFileList();
						} else ShowAlert("Error", "Unsupported file size! File to big!");
						$('#dialog').dialog( 'close' );
					}
					else {
						ShowAlert("Error", "Error while uploading file!");
						$(me).removeAttr("disabled");
					}
					progress.className = (xhr.status == 200 ? "success" : "failure");
				}
			};
			xhr.upload.addEventListener("error", function (ev) {alert("Error!");$("#fproc").text(ev)}, false);
			// start upload
			xhr.open("POST", $id("upload").action, true);
			xhr.setRequestHeader("X_FILENAME", FileForUpload.name);
			xhr.send(FileForUpload);

		}

	}