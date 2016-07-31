<?
$hiddenLeft = 1;
$location = "투데이샵 > 디자인관리 (HTML작업)";
$scriptLoad='
	<link rel="stylesheet" type="text/css" href="../DynamicTree.css">
	<script src="../DynamicTree.js"></script>
	<script src="../todayshop/codi/_codi.js"></script>
';

include "../_header.php";
include dirname(__FILE__) . "/codi/code.class.php";

$todayShop = &load_class('todayshop', 'todayshop');
if (!$todayShop->auth()) {
	msg(' 서비스 신청안내는 고도몰 고객센터로 문의해주시기 바랍니다.', -1);
}

### 폴더 OPEN 쿠키 추가정의
$codiTree = new codiTree;
$opened = $codiTree->resetCookie($_GET['design_file']);
if ($_GET['design_file'] == '' && strpos($opened, 'ahead/') === false) $opened .= '|ahead/';


### IFRAME 넘겨지는 데이타
parse_str( $_SERVER['QUERY_STRING'], $query_str );
unset( $query_str[ifrmScroll] );
unset( $query_str[design_file] );
foreach( $query_str as $k => $v ) $query_str[$k] = "$k=$v";
$query_str = implode( "&", $query_str );
?>

<script language="javascript">

/*** 폴더 OPEN 쿠키 추가정의 ***/
var date = new Date(new Date().getTime()+3600*24*30*1000);
document.cookie = ("opened" + "=" + escape("<?=$opened?>")) + ("; expires="+date.toGMTString());

/*** 분류트리 하부노드 로딩 ***/
function openTree(obj)
{
	if(obj.getElementsByTagName('input')[0].value.toString().match(/.php/) == null){
		if (obj.getElementsByTagName('input')[0].value.toString().match(/\[[^]]*\] /g)) {
			window.open("../todayshop/codi.php?design_file=" + obj.getElementsByTagName('input')[0].value.toString().replace(/\[[^]]*\] /g, "") + "&<?=$query_str?>");
		}
		else {
			ifrmCodi.location.href = "../todayshop/iframe.codi.php?design_file=" + obj.getElementsByTagName('input')[0].value + "&<?=$query_str?>";
		}
	}
	else {
		ifrmCodi.location.href = obj.getElementsByTagName('input')[0].value;
	}
}

function loadHistory(category)
{
	if (category == 'popup/') category = '../todayshop/iframe.popup_list.php';
	else if (category.match(/^popup\//)){
		category = '../todayshop/iframe.popup_register.php?file=' + category.replace(/^popup\//, '');
	}
	else if (category == 'style.css') category = '../todayshop/iframe.css.php';
	else if (category == 'common.js') category = '../todayshop/iframe.js.php';
	else if (category == 'main/intro.htm') category = '../todayshop/iframe.intro.php';

	var obj = _ID('tree').getElementsByTagName('input');
	for (i=0;i<obj.length;i++){
		if (obj[i].value==category){
			openTree(obj[i].parentNode);
			break;
		}
	}
}

</script>

<table width="100%">
<tr>
	<td valign="top">
		<!-- 퀵메뉴 : Start -->
		<table cellpadding=0 cellspacing=0 border=0 style="margin:11px 0 10px 0">
		<tr>
			<td><a href="../todayshop/iframe.codi.default.php" target="ifrmCodi"><img src="../img/btn_q_dskin.gif"></a></td>
			<td width=4></td>
			<td><a href="javascript:popup('../todayshop/codi.banner.php',980,700)"><img src="../img/btn_q_banner.gif"></a></td>
		</tr>
		<tr><td height=4 colspan=5></td></tr>
		<tr>
			<td><a href="javascript:webftp();"><img src="../img/btn_q_ftp.gif"></a></td>
			<td width=4></td>
			<td><a href="javascript:popup2('../design/popup.webftp_activex.php',760,610,0);"><img src="../img/btn_a_ftp.gif"/></a></td>
		</tr>
		</table>

		<table cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td><a href="iframe.popup_list.php" target="ifrmCodi"><img src="../img/btn_q_popup.gif"></a></td>
		</tr>
		<tr><td height="4"></td></tr>
		</table>
		<!-- 퀵메뉴 : End -->

		<table>
			<tr><td colspan=5 height=25 valign=bottom align=center><img src="../img/line_html_codi.gif"></td></tr>
		</table>
		<!-- 트리 : Start -->
		<div id="treeCodiToday" class="scroll">
			<div style="padding-bottom:1px"><b style="color:0094C3;"><?=$cfg['tplSkinTodayWork']?> (스킨)</b></div>
			<div class="DynamicTree"><div class="wrap" id="tree">
			</div></div>
		</div>
		<!-- 트리 : End -->
	</td>
	<td valign=top width=100% style="padding-left:10px">
		<div id="s2designBanner"><script></script></div>
		<? if($_GET['ifrmCodiHref']):?>
			<iframe id=ifrmCodi name=ifrmCodi src="<?=$_GET['ifrmCodiHref']?>" style="width:100%;height:500px;" frameborder=0></iframe>
		<? else: ?>
			<iframe id=ifrmCodi name=ifrmCodi src="<?=($_GET['design_file'] ? '../../blank.txt' : '../todayshop/iframe.codi.default.php')?>" style="width:100%;height:500px;" frameborder=0></iframe>
		<? endif; ?>
	</td>
</tr>
</table>

<script type="text/javascript">
var tree = new DynamicTree("tree");
tree.category = '<?=$_GET['design_file']?>';
tree.init();
</script>

<? include "../_footer.php"; ?>