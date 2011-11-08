<?php
//Get the options
$caption=get_option('peb_caption');
$before=get_option('peb_before');
$after=get_option('peb_after');

?>
var edButtonsPeb = [];
function edButtonPeb(id, display, tagStart, tagEnd, access, open) {
	this.id = id;				// used to name the toolbar button
	this.display = display;		// label on button
	this.tagStart = tagStart; 	// open tag
	this.tagEnd = tagEnd;		// close tag
	this.access = access;		// access key
	this.open = open;			// set to -1 if tag does not need to be closed
}
<?php

for ($i=0;$i<count($caption);$i++) {
?>

edButtonsPeb[edButtonsPeb.length]=new edButtonPeb("peb_<?php echo $i; ?>","<?php echo str_replace('"','&quot;',$caption[$i]); ?>","<?php echo str_replace('"','\"',$before[$i]); ?>","<?php echo str_replace('"','\"',$after[$i]); ?>","<?php echo str_replace('"','&quot;',$caption[$i]); ?>");
<?php } ?>

var peb_initialize = function() {
    if (typeof edButtons === "undefined") {
        setTimeout(peb_initialize, 300);
        return;
    }
	for (var i = 0; i < edButtonsPeb.length; i++) {
	    edButtons[edButtons.length] = edButtonsPeb[i];
        jQuery('<input type="button" id="' + edButtonsPeb[i].id + '" accesskey="' + edButtonsPeb[i].access + '" class="ed_button" onclick="edInsertTag(edCanvas, ' + (edButtons.length - 1) + ');" value="' + edButtonsPeb[i].display + '"  />').insertBefore("#ed_spell");
	}
};
peb_initialize();

function peb_deleteRow(id) {
	document.getElementById('row'+id).innerHTML='';

	return false;
}
function peb_addMore(){
	var tbody = document.getElementById('op_table').getElementsByTagName("TBODY")[0];

	var row = document.createElement("TR");

	var td1 = document.createElement("TD");
	td1.innerHTML='<input type="text" name="peb_caption[]" />';

	var td2 = document.createElement("TD");
	td2.innerHTML='<input type="text" name="peb_before[]" />';

	var td3 = document.createElement("TD");
	td3.innerHTML='<input type="text" name="peb_after[]" />';

	row.appendChild(td1);
	row.appendChild(td2);
	row.appendChild(td3);

	tbody.appendChild(row);

	return false;
  }