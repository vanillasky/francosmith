<?

$location = "상품관리 > 브랜드관리";
include "../_header.php";

$query = "select * from ".GD_GOODS_BRAND."";
$res = $db->query($query);
while ($data=$db->fetch($res, MYSQL_ASSOC)){
	$data[brandnm] = strip_tags( $data[brandnm] );
	if (!$data[brandnm]) $data[brandnm] = "_deleted_";
	$brands[$data[sort]][] = $data;
}

### 배열 순서 재정의
$brands = resort($brands);

### IFRAME 넘겨지는 데이타
parse_str( $_SERVER['QUERY_STRING'], $query_str );
unset( $query_str[ifrmScroll] );
unset( $query_str[brand] );
foreach( $query_str as $k => $v ) $query_str[$k] = "$k=$v";
$query_str = implode( "&", $query_str );
?>

<script>

/*** 브랜드트리 하부노드 로딩 ***/
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

<div class="title title_top">브랜드 관리<span>새로운 브랜드들을 등록하고 상품등록시 브랜드입력창에서 등록된 브랜드를 선택하게 합니다</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=5')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a></div>


<table width=100%>
<tr>
	<td valign=top>
	<form method=post action="indb.php">
	<input type=hidden name=mode value="chgBrandSort">
	<input type=hidden name=rtn_query>

	<div id=treeCategory class=scroll onmousewheel="return iciScroll(this)">

	<div style="padding-bottom:1px"><span><a id=node_top href="javascript:void(0)" onclick="openTree(this)" onfocus=blur()>TOP<input type=hidden name=brand[] value=""></a></span> (최상위)</div>
	<div class="DynamicTree"><div class="wrap" id="tree">
	<? foreach ($brands as $data){ ?>
	<div class="doc"><span><a href="javascript:void(0)" onclick="openTree(this)" onfocus=blur()><?=$data[brandnm]?><input type=hidden name=brand[] value="<?=$data[sno]?>"></a></span></div>
	<? } ?>
	</div></div>

	</div>

	</form>

	<div id=MSG01>
	<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
	<tr><td><img src="../img/icon_list.gif" align="absmiddle">TOP</font> 누르고 브랜드를 생성하세요.<br>
	<img src="../img/icon_list.gif" align="absmiddle">브랜드순서변경 = 키보드 상하이동키↓↑
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