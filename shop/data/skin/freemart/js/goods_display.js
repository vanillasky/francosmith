/**
 * 
 */

function act(target,goodsno,opt1,opt2)
{
	var form = document.frmCharge;

	form.mode.value = "addItem";
	form.goodsno.value = goodsno;

	if(opt2) opt1 += opt2;
	document.getElementById("opt").value=opt1;

	form.action = target + ".php";
	form.submit();
}

function sort(sort)
{
	var fm = document.frmList;
	if(typeof(document.sSearch) != "undefined") fm = document.sSearch;
	fm.sort.value = sort;
	fm.submit();
}
function sort_chk(sort)
{
	if (!sort) return;
	sort = sort.replace(" ","_");
	
	var obj = document.getElementsByName('sort_'+sort);
	
	if (obj.length && obj[0].src){
		div = obj[0].src.split('list_');
		for (i=0;i<obj.length;i++) {
			chg = (div[1]=="/up_off.gif") ? "/up_on.gif" : "/down_on.gif";
			obj[i].src = div[0] + "list_" + chg;
		}
	}
	
	
	var jq = jQuery.noConflict();
	var sort_by = jq("#sort_" + sort);
	var sort_ul = jq("#sort_list");
	if (sort_by.length) {
		//sort_ul.children("li" a span").removeClass("on");
		sort_by.find("a span").addClass("on");
	} 

}