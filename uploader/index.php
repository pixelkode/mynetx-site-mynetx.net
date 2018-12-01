<?php

if(isset($_FILES['Filedata'])) {
	move_uploaded_file($_FILES['Filedata']['tmp_name'], 'files/'.$_FILES['Filedata']['name']) or die('Error');
	$strFile = 'http://mynetx.net/uploader/files/'.rawurlencode($_FILES['Filedata']['name']);
	$strFileTrim = $strFile;
	if(!isset($_POST['noscript']))
		echo $strFile."\r\n".$strFileTrim;
	else
		echo 'File uploaded! <a href="./">Upload more</a><br /><input type="text" style="width: 400px" value="'.$strFile.'" />';

	if(!isset($_GET['admin'])) {
		mail('mynetx1@gmail.com', 'File uploaded', "A file was uploaded by ".gethostbyaddr($_SERVER['REMOTE_ADDR']).
				".\r\n".$strFile, "From: mynetx <mynetx@gmx.de>\r\n");
	}
	die();
}

function convertBytes( $value ) {
	if ( is_numeric( $value ) ) {
		return $value;
	} else {
		$value_length = strlen( $value );
		$qty = substr( $value, 0, $value_length - 1 );
		$unit = strtolower( substr( $value, $value_length - 1 ) );
		switch ( $unit ) {
			case 'k':
				$qty *= 1024;
				break;
			case 'm':
				$qty *= 1048576;
				break;
			case 'g':
				$qty *= 1073741824;
				break;
		}
		return $qty;
	}
}

?>
<title>Upload files</title>
	Click Browse, and choose a file to upload (max. <?php
				echo $intMax = intval(min(
					convertBytes(ini_get('upload_max_filesize')),
					convertBytes(ini_get('post_max_size'))) / 1048576);
				?> MB).<br />
	Klicke auf Browse, und w&auml;hle eine Datei zum Hochladen (max. <?php
				echo $intMax;
				?> MB).<br />
<form action="./<?php if(isset($_COOKIE['admin']) || isset($_GET['admin'])) echo '?admin'; ?>" method="post" enctype="multipart/form-data">
<noscript>
<input type="file" name="Filedata" />
<input type="hidden" name="noscript" value="1" />
<br />
<input type="submit" value="Upload" />
</noscript>

<div id="flash" style="position: relative; top: -9999px; left: 0">
	<span style="position: relative">
		<span style="position: absolute; z-index: 1">
			<span id="flashbutton"></span>
		</span>
		<button style="width: 60px" style="position: absolute">Browse...</button>
	</span>
</div>
<div id="name" style="margin-top: 20px"></div>
<span id="linkarea" style="display: none">
<input type="text" id="link" style="width: 400px; margin-top: 45px" onfocus="this.select()" onclick="this.select()" readonly="readonly" />
<button onclick="window.open(document.getElementById('link').value);return false">Open</button><br />
<input type="text" id="link2" style="width: 400px;" onfocus="this.select()" onclick="this.select()" readonly="readonly" />
</span>
<br />
<div id="bar" style="display: none; position: relative; width: 200px; height: 12px; border: 1px solid #aaa; background: #fff">
	<div id="bartrack" style="position: absolute; top: 0; left: 0; height: 12px; width: 0%; background: #aaa">
</div>
</form>

<script type="text/javascript" src="swfupload/swfupload.js"></script>
<script type="text/javascript" src="swfupload/swfupload.swfobject.js"></script>
<script type="text/javascript" src="swfupload/swfupload.speed.js"></script>
<script type="text/javascript">
	/*<![CDATA[*/
	var swfu;
	var objUpload = null;
	document.getElementById("flash").style.top = "0";
	<?php if(isset($_SERVER['HTTP_USER_AGENT']) && stristr($_SERVER['HTTP_USER_AGENT'], 'MSIE')) { ?>
	document.getElementById("flash").style.lineHeight = "1.2em";
	if(!document.documentMode || document.documentMode == 7) {
		document.getElementById("flash").style.paddingBottom = "29px";
	}
	<?php } ?>
	SWFUpload.onload = function () {
		objUpload = new SWFUpload({
			flash_url : "swfupload/swfupload.swf",
			upload_url: "./<?php if(isset($_COOKIE['admin']) || isset($_GET['admin'])) echo '?admin'; ?>",
			file_size_limit : "<?php
				echo min(
					convertBytes(ini_get('upload_max_filesize')),
					convertBytes(ini_get('post_max_size')));
				?>b",
			file_types : "*.*",
			file_types_description : "All files",
			debug: false,
			button_placeholder_id : "flashbutton",
			button_image_url : "swfupload/button.gif",
			button_width : "60",
			button_height : "29",
			button_action : SWFUpload.BUTTON_ACTION.SELECT_FILE,
			button_window_mode : SWFUpload.WINDOW_MODE.TRANSPARENT,
			file_queued_handler : clsUpload.FileQueued,
			upload_start_handler : clsUpload.UploadStart,
			upload_progress_handler : clsUpload.UploadProgress,
			upload_error_handler : clsUpload.UploadError,
			upload_success_handler : clsUpload.UploadSuccess,
			swfupload_loaded_handler: clsUpload.Loaded,
			minimum_flash_version : "9.0.28",
			swfupload_load_failed_handler: clsUpload.LoadFailed
		});
	};

	var clsUpload = {
		intUploading : 0,
		FileQueued : function(objFile) {
			document.getElementById("flash").style.top = "-9999px";
			document.getElementById("flash").style.marginBottom = "-25px";
			document.getElementById("linkarea").style.display = "none";
			document.getElementById("name").style.display = "block";
			document.getElementById("name").innerHTML =
				objFile.name
				+ ' <span id="progress" '
				+ 'style="padding-right: 20px; background: url(swfupload/loading.gif)'
				+ ' no-repeat right center">(0&nbsp;%)</span>';
			document.getElementById("bar").style.display = "block";
			<?php if(isset($_SERVER['HTTP_USER_AGENT']) && stristr($_SERVER['HTTP_USER_AGENT'], 'MSIE')) { ?>
			if(!document.documentMode || document.documentMode == 7) {
				try {
					document.getElementById("flash").style.left = "0";
				}
				catch(e) { }
			}
			<?php } ?>
			this.startUpload();
			clsUpload.intUploading++;
		},
		UploadStart : function(objFile) {
		},
		UploadProgress : function(objFile, intBytesSent, intBytesTotal) {
			document.getElementById("progress").innerHTML =
				"(" + String(Math.ceil(intBytesSent / intBytesTotal * 100)) + "&nbsp;%; "
				+ "%s&nbsp;kB left".replace("%s",
				Math.round((objFile.size - objFile.sizeUploaded) / 1024)) + ")";
			document.getElementById("bartrack").style.width =
				Math.ceil(intBytesSent / intBytesTotal * 100) + "%";
			document.title = String(Math.ceil(intBytesSent / intBytesTotal * 100)) + " % - Upload files";
		},
		UploadError : function(objFile, intError, strError) {
			document.getElementById("progress").style.visibility = "hidden";
			document.getElementById("bar").style.display = "none";
			document.getElementById("name").style.color = "#f00";
			document.getElementById("name").innerHTML = "Upload Error";
			clsUpload.intUploading--;
		},
		UploadSuccess : function(objFile, strReply, boolReceivedResponse) {
			document.getElementById("linkarea").style.display = "inline";
			document.getElementById("link").value = strReply.split("\r\n")[0];
			document.getElementById("link").select();
			document.getElementById("link2").value = strReply.split("\r\n")[1];
			document.getElementById("link2").select();
			document.getElementById("link2").focus();
			document.getElementById("name").style.display = "none";
			document.getElementById("bar").style.display = "none";
			document.getElementById("flash").style.top = "0";
			document.title = "Upload files";
			clsUpload.intUploading--;
		},
		Loaded : function() {
			clsUpload.IsFlash = true;
		},
		LoadFailed : function() {
			alert("Something might be wrong with your Flash Player.");
			document.getElementById("flash").style.display = "none";
		},
		IsFlash : false
	};
	/*]]>*/
</script>

