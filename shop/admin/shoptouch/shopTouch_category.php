<?
$hiddenLeft = 1;
$location = "쇼핑몰 App관리 > 쇼핑몰 App 분류【카테고리】관리";
$scriptLoad='
	<link rel="stylesheet" type="text/css" href="../DynamicTree.css">
	<script src="../DynamicTree.js"></script>
	<script src="../DynamicTreeSorting.js"></script>
	<script src="../DynamicTreeShifting.js"></script>
	<script src="../DynamicTreeHidding.js"></script>
';
include "../_header.php";

@include_once "../../lib/pAPI.class.php";
$pAPI = new pAPI();

$expire_dt = $pAPI->getExpireDate();
if(!$expire_dt) {
	msg('서비스 신청후에 사용가능한 메뉴입니다.', -1);
}

$now_date = date('Y-m-d 23:59:59');
$tmp_now_date = date('Y-m-d 23:59:59', mktime(0,0,0, substr($now_date, 5, 2), substr($now_date, 8, 2) - 30, substr($now_date, 0, 4)));
if($expire_dt < $tmp_now_date) {
	msg('서비스 사용기간 만료후 30일이 지나 서비스가 삭제 되었습니다.\n서비스를 다시 신청해 주시기 바랍니다.', -1);
}

### IFRAME 넘겨지는 데이타
parse_str( $_SERVER['QUERY_STRING'], $query_str );
unset( $query_str[ifrmScroll] );
unset( $query_str[category] );
foreach( $query_str as $k => $v ) $query_str[$k] = "$k=$v";
$query_str = implode( "&", $query_str );
?>

<script>

/*** 분류트리 하부노드 로딩 ***/
function openTree(obj, chkable) {

	tree.sorting.ready(obj);
	if (chkable && ifrmCategory.document.getElementsByName('category')[0] != undefined){
		if (ifrmCategory.document.getElementsByName('category')[0].value == obj.getElementsByTagName('input')[0].value) return;
	}
	ifrmCategory.location.href = "iframe.shopTouch_category.php?ifrmScroll=1&category=" + obj.getElementsByTagName('input')[0].value + "&<?=$query_str?>";

}

function loadHistory(category, chkable) {
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
<? 
if($expire_dt < $now_date) {
	@include('shopTouch_expire_msg.php');
}
?>
<div class="title title_top">쇼핑몰 App 분류[카테고리]관리 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=shoppingapp&no=8')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<table width=100%>
<tr>
	<td valign=top>
	<form method=post action="indb.php">
	<input type=hidden name=mode value="chgCategorySort">
	<input type=hidden name=category>

	<div style="padding: 0 0 3 5">
	<? if ( !preg_match( "/^rental_mxfree/i", $godo[ecCode] ) ){ ?>
	<img src="../img/i_cate_eye_on.gif" align=absmiddle><font class=small1 color=444444>분류보임</font>
	<img src="../img/i_cate_eye_off.gif" align=absmiddle><font class=small1 color=444444>분류감춤</font>
	<? } ?>
	</div>

	<div id=treeCategory class=scroll onmousewheel="return iciScroll(this)">

	<div style="padding-bottom:1px"><a id=node_top href="javascript:void(0)" onclick="openTree(this)" onfocus=blur()>1차분류만들기<input type=hidden name=cate_shoptouch[] value=""></a> (최상위분류)</div>
	<div class="DynamicTree"><div class="wrap" id="tree">
		<span><font class=extext>카테고리를 불러오는 중입니다.</font></span>
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
	<? } ?>
	</td></tr>
	</table>
	</div>
	<script>cssRound('MSG01')</script>

	</td>
	<td valign=top width=100% style="padding-left:10px">

	<iframe id=ifrmCategory name=ifrmCategory src="iframe.shopTouch_category.php?ifrmScroll=1" style="width:100%;height:500px" frameborder=0></iframe>

	</td>
</tr>
</table>

<script type="text/javascript">
var tree = new DynamicTree("tree");
tree.category = '<?=$_GET[shopTouch_category]?>';
<? if ( preg_match( "/^rental_mxfree/i", $godo[ecCode] ) ){ ?>
tree.useHidding = false;
tree.useShifting = false;
<? } ?>
tree.init('shoptouch');
tree.Sorting();
</script>

<? include "../_footer.php"; ?>