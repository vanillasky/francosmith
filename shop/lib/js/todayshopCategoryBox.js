/**********************
 * categoryBox
 *
 * @name	category Æû°´Ã¼¸í
 * @idx		category ¹Ú½º °¹¼ö
 */

function todayshopCategoryBox(name,idx,val,type,formnm)
{
	if (!idx || idx==1) idx = 2;
	if (type=="multiple") type = "multiple style='width:160px;height:96'";
	for (i=0;i<idx;i++) document.write("<select " + type + " idx=" + i + " name='" + name + "' onchange='todayshopCategoryBox_request(this)' class='select'></select>");

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
		todayshopCategoryBox_request(this.oCate[0],val);
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

function todayshopCategoryBox_request(obj,val)
{
	if (!val) val = "";
	var idx = obj.getAttribute('idx');

	if ( document.location.href.indexOf("/admin") == -1 ){
		exec_script("../lib/_todayshopCategoryBox.script.php?mode=user&idx=" + idx + "&obj=" + obj.name + "&formnm=" + obj.form.name + "&val=" + val + "&category=" + obj.value);
	}
	else {
		exec_script("../../lib/_todayshopCategoryBox.script.php?mode=admin&idx=" + idx + "&obj=" + obj.name + "&formnm=" + obj.form.name + "&val=" + val + "&category=" + obj.value);
	}
}