var sub = new Array();

<?
$i = 0;
$od = opendir("../../data/skin/$_GET[tplSkin]/board/$_GET[skin]");
while ($rd=readdir($od)){
	if (!ereg("\.$",$rd) && is_dir("../../data/skin/$_GET[tplSkin]/board/$_GET[skin]/$rd")){
		echo "sub[$i] = \"$rd\"; \n";
		$i++;
	}
}
?>

function createSub()
{
	var idx	= 0;
	var tmp	= new Array();
	for (i=0;i<sub.length;i++){
		tmp[i] = "<option value='" + sub[i] + "'>" + sub[i] + "</option>";
		if (sub[i]=="<?=$bdImg?>") var idx = i;
	}
	parent.IMG.innerHTML = "<select name=bdImg>" + tmp.join() + "</select>";
	if (sub.length)	parent.document.forms[0].bdImg.options[idx].selected = 1;
}

if (sub[0]) createSub();