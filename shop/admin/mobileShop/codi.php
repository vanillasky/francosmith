<?

$hiddenLeft = 1;
$location = "디자인관리 > 디자인코디 (HTML작업)";
$scriptLoad='
	<link rel="stylesheet" type="text/css" href="../DynamicTree.css">
	<script src="../DynamicTree.js"></script>
	<script src="../mobileShop/codi/_codi.js"></script>
';

include "../_header.php";
include dirname(__FILE__) . "/codi/code.class.php";
require_once("../../lib/qfile.class.php");
$qfile = new qfile();

// 현재 사용 중인 모바일 스킨이 없을 때 기본 셋팅 스킨명 저장.
if(empty($cfg['tplSkinMobile']) === true){

	$cfg['tplSkinMobile'] = $cfg['tplSkinMobileWork'] = "default";

	$cfg = array_map("stripslashes",$cfg);
	$cfg = array_map("addslashes",$cfg);

	$qfile->open( $path = dirname(__FILE__) . "/../../conf/config.php");
	$qfile->write("<?\n\n" );
	$qfile->write("\$cfg = array(\n" );

	foreach ( $cfg as $k => $v ){

		if ( $v === true ) $qfile->write("'$k'\t\t\t=> true,\n" );
		else if ( $v === false ) $qfile->write("'$k'\t\t\t=> false,\n" );
		else $qfile->write("'$k'\t\t\t=> '$v',\n" );
	}

	$qfile->write(");\n\n" );
	$qfile->write("?>" );
	$qfile->close();
	@chMod( $path, 0757 );

	@include dirname(__FILE__) . "/../../conf/config.mobileShop.php";
	$cfgMobileShop = (array)$cfgMobileShop;
	$cfgMobileShop = array_map("stripslashes",$cfgMobileShop);
	$cfgMobileShop = array_map("addslashes",$cfgMobileShop);

	$cfgMobileShop['tplSkinMobile'] = "default";

	$qfile->open($path = dirname(__FILE__) . "/../../conf/config.mobileShop.php");
	$qfile->write("<? \n");
	$qfile->write("\$cfgMobileShop = array( \n");
	foreach ($cfgMobileShop as $k=>$v) $qfile->write("'$k' => '$v', \n");
	$qfile->write(") \n;");
	$qfile->write("?>");
	$qfile->close();
	@chMod( $path, 0757 );
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
		ifrmCodi.location.href = "../mobileShop/iframe.codi.php?design_file=" + obj.getElementsByTagName('input')[0].value + "&<?=$query_str?>";
	}
	else {
		ifrmCodi.location.href = obj.getElementsByTagName('input')[0].value;
	}
}

function loadHistory(category)
{
	if (category == 'style.css') category = '../mobileShop/iframe.css.php';
	else if (category == 'common.js') category = '../mobileShop/iframe.js.php';

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
<table>
	<tr><td colspan=5 height=25 valign=bottom align=center><img src="../img/line_html_codi.gif"></td></tr>
	</table>
	<!-- 퀵메뉴 : End -->

	<!-- 트리 : Start -->
	<div id="treeCodiMobile" class="scroll" style="overflow-y:hidden;">
	<div style="padding-bottom:1px"><b style="color:0094C3;"><?=$cfg['tplSkinMobileWork']?> (스킨)</b></div>
	<div class="DynamicTree"><div class="wrap" id="tree">
	</div></div>
	</div>
	<!-- 트리 : End -->

	<!-- 새로운 페이지 추가하기 : Start -->
	<div style="padding-bottom:10px"><a href="javascript:popupLayer('./codi/popup.create.php')"><img src="../img/btn_q_newpage.gif" border=0></a></div>
	<!-- 새로운 페이지 추가하기 : End -->

	</td>
	<td valign=top width=100% style="padding-left:10px">
	<div id="s2designBanner"><script>panel('s2designBanner', 'design');</script></div>

	<? if($_GET['ifrmCodiHref']):?>
		<iframe id=ifrmCodi name=ifrmCodi src="<?=$_GET['ifrmCodiHref']?>" style="width:100%;height:500px;" frameborder=0></iframe>
	<? else: ?>
		<iframe id=ifrmCodi name=ifrmCodi src="<?=($_GET['design_file'] ? '../../blank.txt' : '../mobileShop/iframe.default.php')?>" style="width:100%;height:500px;" frameborder=0></iframe>
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
