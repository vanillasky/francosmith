<?

header ("Cache-Control: no-cache, must-revalidate");
header ("Pragma: no-cache");

include "../lib/library.php";

$category = array();

$val = $_GET[val];
if ($val){
	for ($i=0;$i<=strlen($val)/3;$i++) $category[] = substr($val,0,$i*3);
} else $category[] = $_GET[category];
if ($_GET[idx]) $category = array_notnull($category);
?>
<?/* 칼라 코딩 때문에; ?><script><?*/?>

var oForm = eval("document.forms['<?=$_GET['formnm']?>']");
if ( oForm == null ) oForm = eval("document.forms[0]");

var obj = oForm['<?=$_GET[obj]?>'];
var st = <?=$_GET[idx]+0?> + 1;

for (i=st;i<obj.length;i++){
	for (j=obj[i].options.length;j>0;j--) obj[i].remove(j);
	obj[i].options.selectedIndex = 0;
}

function category_update(ob,ret,category,val)
{
	var idx = category.length / 3;
	var obj = oForm[ob][idx];
	if (typeof(obj)=="object" && ret){
		div = ret.split("||");
		if( obj.options.length != "undefined" ){
			for (i=obj.options.length;i>0;i--) obj.remove(1);
			for (i=0;i<div.length;i++){
				div2 = div[i].split("|");
				obj.options[i+1] = new Option(div2[0],div2[1]);
				if (div2[1]==val.substring(0,div2[1].length)) obj.selectedIndex = i+1;
			}
		}
	}
}

<?
for ($i=0;$i<count($category);$i++){
	$where = $ret = "";
	if ($ici_admin === false && $_GET[mode] == 'user') $where[] = "hidden=0";
	if ($category[$i]) $where[] = "category like '$category[$i]%'";
	$where[] = "length(category)=".(strlen($category[$i])+3);
	if ($where) $where = "where ".implode(" and ",$where);
	$query = "select * from ".GD_TODAYSHOP_CATEGORY." $where order by sort";
	$res = $db->query($query);
	while ($data=$db->fetch($res))
	{
		$data[catnm] = addslashes($data[catnm]);
		$ret[] = $data[catnm]."|".$data[category];
	}
	if ($ret) $ret = implode("||",$ret);
?>
category_update("<?=$_GET[obj]?>","<?=$ret?>","<?=$category[$i]?>","<?=$_GET[val]?>");
<? } ?>
