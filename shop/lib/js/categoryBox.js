/**********************
 * categoryBox
 *
 * @name	category Æû°´Ã¼¸í
 * @idx		category ¹Ú½º °¹¼ö
 * @tid		select °´Ã¼¸¦ »ðÀÔÇÒ °´Ã¼ÀÇ ID
 */

function categoryBox(name,idx,val,type,formnm,tid)
{
	if (!idx || idx==1) idx = 2;
	if (type=="multiple") type = "multiple style='width:160px;height:96'";
	if (tid) {
		var _select = "";
		for (i=0;i<idx;i++) {
			_select += "<select " + type + " idx=" + i + " name='" + name + "' onchange='categoryBox_request(this)' class='select'></select>";
		}
		document.getElementById(tid).innerHTML = _select;

	}
	else {
		for (i=0;i<idx;i++) document.write("<select " + type + " idx=" + i + " name='" + name + "' onchange='categoryBox_request(this)' class='select'></select>");
	}

	oForm = eval("document.forms['" + formnm + "']");

	if ( oForm == null ) this.oCate = eval("document.forms[0]['" + name + "']");
	else{ this.oCate = eval("document." + oForm.name + "['" + name + "']"); }

	if (idx==1) this.oCate = new Array(this.oCate);

	this.categoryBox_init = categoryBox_init;
	this.categoryBox_build = categoryBox_build;
	this.categoryBox_init();

	function categoryBox_init()
	{
		this.categoryBox_build();
		categoryBox_request(this.oCate[0],val);
	}

	function categoryBox_build()
	{
		for (i=0;i<4;i++){
			if (this.oCate[i]){
				this.oCate[i].options[0] = new Option("= "+(i+1)+"Â÷ ºÐ·ù =","");
			}
		}
	}

}

function categoryBox_request(obj,val)
{
	if (!val) val = "";
	var idx = obj.getAttribute('idx');

	if ( document.location.href.indexOf("/admin") == -1 ){
		exec_script("../lib/_categoryBox.script.php?mode=user&idx=" + idx + "&obj=" + obj.name + "&formnm=" + obj.form.name + "&val=" + val + "&category=" + obj.value);
	}
	else {
		exec_script("../../lib/_categoryBox.script.php?mode=admin&idx=" + idx + "&obj=" + obj.name + "&formnm=" + obj.form.name + "&val=" + val + "&category=" + obj.value);
	}
}