<?

$hiddenLeft = 1;
$location = "투데이샵 > 상품분류(지역)관리";
$scriptLoad='
	<link rel="stylesheet" type="text/css" href="../DynamicTree.css">
	<script src="../DynamicTree.js"></script>
	<script src="../DynamicTreeSorting.js"></script>
	<script src="../DynamicTreeShifting.js"></script>
	<script src="../DynamicTreeHidding.js"></script>
';
include "../_header.php";

$todayShop = &load_class('todayshop', 'todayshop');
if (!$todayShop->auth()) {
	msg(' 서비스 신청안내는 고도몰 고객센터로 문의해주시기 바랍니다.', -1);
}

$tsCfg = $todayShop->cfg;

### IFRAME 넘겨지는 데이타
parse_str( $_SERVER['QUERY_STRING'], $query_str );
unset( $query_str[ifrmScroll] );
unset( $query_str[category] );
foreach( $query_str as $k => $v ) $query_str[$k] = "$k=$v";
$query_str = implode( "&", $query_str );
?>

<script>

/*** 분류트리 하부노드 로딩 ***/
function openTree(obj, chkable)
{

	tree.sorting.ready(obj);
	if (chkable && ifrmCategory.document.getElementsByName('category')[0] != undefined){
		if (ifrmCategory.document.getElementsByName('category')[0].value == obj.getElementsByTagName('input')[0].value) return;
	}
	ifrmCategory.location.href = "iframe.category.php?ifrmScroll=1&category=" + obj.getElementsByTagName('input')[0].value + "&<?=$query_str?>";

}

function loadHistory(category, chkable)
{
	var len = category.length / 3;
	var el = "cate" + len + "[]";
	var obj = document.getElementsByName(el);
	for (i=0;i<obj.length;i++){
		if (obj[i].value==category){
			openTree(obj[i].parentNode, chkable);
			break;
		}
	}
}

</script>

<div class="title title_top">상품분류(지역) 관리<span>편리하게 상품분류를 관리하실 수 있습니다</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=todayshop&no=10')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<table width=100%>
<tr>
	<td valign=top>
	<form method=post action="indb.category.php">
	<input type=hidden name=mode value="chgCategorySort">
	<input type=hidden name=category>

	<div style="padding: 0 0 3 5">
	<? if ( !preg_match( "/^rental_mxfree/i", $godo[ecCode] ) ){ ?>
	<img src="../img/i_cate_eye_on.gif" align=absmiddle><font class=small1 color=444444>분류보임</font>
	<img src="../img/i_cate_eye_off.gif" align=absmiddle><font class=small1 color=444444>분류감춤</font>
	<? } ?>
	<a href="javascript:popup('http://guide.godo.co.kr/guide/php/ex_cate_use.html',630,523)"><img src="../img/icon_use_guide.gif" border="0" align=absmiddle hspace=2></a>
	</div>

	<div id=treeCategory class=scroll onmousewheel="return iciScroll(this)">

	<div style="padding-bottom:1px"><a id=node_top href="javascript:void(0)" onclick="openTree(this)" onfocus=blur()>1차분류만들기<input type=hidden name=cate[] value=""></a> (최상위분류)</div>
	<div class="DynamicTree"><div class="wrap" id="tree">
	</div></div>

	</div>

	</form>

	<div style="clear:both;"></div>

	<div id=MSG01>
	<table cellpadding=1 cellspacing=0 border=0 class=small_ex style="padding-top:0px;">
	<tr><td>
	<div><img src="../img/icon_list.gif" align="absmiddle">1차분류만들기을 누르고 1차분류를 생성하세요.</div>
	<div style="padding-top:2"><img src="../img/icon_list.gif" align="absmiddle">1차분류를 누르고 2차분류를 생성하세요.</div>
	<div style="padding-top:2"><img src="../img/icon_list.gif" align="absmiddle"><img src="../img/icon_plus.gif" align=absmiddle> 를 누르면 하위분류가 보입니다.</div>
	<div style="padding-top:2"><img src="../img/icon_list.gif" align="absmiddle">분류순서변경 = 키보드 상하이동키 이용</div>
	<? if ( !preg_match( "/^rental_mxfree/i", $godo[ecCode] ) ){ ?>
	<div style="padding-top:2"><img src="../img/icon_list.gif" align="absmiddle">분류감춤기능 = 아래처럼 손쉽게 설정</div>
	<div style="padding:2 0 0 10"><img src="../img/i_cate_eye_on.gif" align=absmiddle> : 클릭하면 감춤모드로 설정</div>
	<div style="padding:2 0 0 10"><img src="../img/i_cate_eye_off.gif" align=absmiddle> : 클릭하면 보임모드로 설정</div>
	<div style="padding-top:2"><img src="../img/icon_list.gif" align="absmiddle">분류이동기능 (아래 사용방법 참조)</div>
	<div style="padding:2 0 0 45"><a href="javascript:popup('http://guide.godo.co.kr/guide/php/ex_cate_use.html',630,523)"><img src="../img/icon_use_guide.gif" border="0" align=absmiddle hspace=2></a></div>
	<? } ?>
	</td></tr>
	</table>
	</div>
	<script>cssRound('MSG01')</script>

	</td>
	<td valign=top width=100% style="padding-left:10px">

	<iframe id=ifrmCategory name=ifrmCategory src="iframe.category.php?ifrmScroll=1" style="width:100%;height:500px" frameborder=0></iframe>

	</td>
</tr>
</table>

<script type="text/javascript">
var tree = new DynamicTree("tree");
tree.category = '<?=$_GET[category]?>';
<? if ( preg_match( "/^rental_mxfree/i", $godo[ecCode] ) ){ ?>
tree.useHidding = false;
tree.useShifting = false;
<? } ?>
tree.init('local');
tree.Sorting();
</script>

<? include "../_footer.php"; ?>