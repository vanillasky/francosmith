<?

$location = "��ǰ���� > �귣�����";
include "../_header.php";

$query = "select * from ".GD_GOODS_BRAND."";
$res = $db->query($query);
while ($data=$db->fetch($res, MYSQL_ASSOC)){
	$data[brandnm] = strip_tags( $data[brandnm] );
	if (!$data[brandnm]) $data[brandnm] = "_deleted_";
	$brands[$data[sort]][] = $data;
}

### �迭 ���� ������
$brands = resort($brands);

### IFRAME �Ѱ����� ����Ÿ
parse_str( $_SERVER['QUERY_STRING'], $query_str );
unset( $query_str[ifrmScroll] );
unset( $query_str[brand] );
foreach( $query_str as $k => $v ) $query_str[$k] = "$k=$v";
$query_str = implode( "&", $query_str );
?>

<script>

/*** �귣��Ʈ�� �Ϻγ�� �ε� ***/
function openTree(obj)
{
	tree.sorting.ready(obj);
	ifrmbrand.location.href = "iframe.brand.php?ifrmScroll=1&brand=" + obj.parentNode.getElementsByTagName('input')[0].value + "&<?=$query_str?>";
}

function loadHistory(brand)
{
	var el = "brand[]";
	var obj = document.getElementsByName(el);
	for (i=0;i<obj.length;i++){
		if (obj[i].value==brand){
			openTree(obj[i].parentNode);
			break;
		}
	}
}

</script>

<link rel="stylesheet" type="text/css" href="http://tagin.net/js/ex/js7-61/DynamicTree.css" />
<script src="../DynamicTree.js"></script>
<script src="../DynamicTreeSorting.js"></script>

<div class="title title_top">�귣�� ����<span>���ο� �귣����� ����ϰ� ��ǰ��Ͻ� �귣���Է�â���� ��ϵ� �귣�带 �����ϰ� �մϴ�</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=5')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a></div>


<table width=100%>
<tr>
	<td valign=top>
	<form method=post action="indb.php">
	<input type=hidden name=mode value="chgBrandSort">
	<input type=hidden name=rtn_query>

	<div id=treeCategory class=scroll onmousewheel="return iciScroll(this)">

	<div style="padding-bottom:1px"><span><a id=node_top href="javascript:void(0)" onclick="openTree(this)" onfocus=blur()>TOP<input type=hidden name=brand[] value=""></a></span> (�ֻ���)</div>
	<div class="DynamicTree"><div class="wrap" id="tree">
	<? foreach ($brands as $data){ ?>
	<div class="doc"><span><a href="javascript:void(0)" onclick="openTree(this)" onfocus=blur()><?=$data[brandnm]?><input type=hidden name=brand[] value="<?=$data[sno]?>"></a></span></div>
	<? } ?>
	</div></div>

	</div>

	</form>

	<div id=MSG01>
	<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
	<tr><td><img src="../img/icon_list.gif" align="absmiddle">TOP</font> ������ �귣�带 �����ϼ���.<br>
	<img src="../img/icon_list.gif" align="absmiddle">�귣��������� = Ű���� �����̵�Ű���
	</td></tr>
	</table>
	</div>
	<script>cssRound('MSG01')</script>

	</td>
	<td valign=top width=100% style="padding-left:10px">

	<iframe id=ifrmbrand name=ifrmbrand src="iframe.brand.php?ifrmScroll=1" style="width:100%;height:500px" frameborder=0></iframe>

	</td>
</tr>
</table>

<script type="text/javascript">
var tree = new DynamicTree("tree");
tree.init();
tree.Sorting();
<? if ($_GET[brand]){ ?>loadHistory('<?=$_GET[brand]?>');
<? } else { ?>openTree(_ID('node_top'));
<? } ?>
</script>

<? include "../_footer.php"; ?>