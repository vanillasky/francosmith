<?

$hiddenLeft = 1;
$location = "�����ΰ��� > �������ڵ� (HTML�۾�)";
$scriptLoad='
	<link rel="stylesheet" type="text/css" href="../DynamicTree.css">
	<script src="../DynamicTree.js"></script>
	<script src="../design/codi/_codi.js"></script>
';

include "../_header.php";
include dirname(__FILE__) . "/codi/code.class.php";


### ���� OPEN ��Ű �߰�����
$codiTree = new codiTree;
$opened = $codiTree->resetCookie($_GET['design_file']);
if ($_GET['design_file'] == '' && strpos($opened, 'ahead/') === false) $opened .= '|ahead/';


### IFRAME �Ѱ����� ����Ÿ
parse_str( $_SERVER['QUERY_STRING'], $query_str );
unset( $query_str[ifrmScroll] );
unset( $query_str[design_file] );
foreach( $query_str as $k => $v ) $query_str[$k] = "$k=$v";
$query_str = implode( "&", $query_str );

?>

<script language="javascript">

/*** ���� OPEN ��Ű �߰����� ***/
var date = new Date(new Date().getTime()+3600*24*30*1000);
document.cookie = ("opened" + "=" + escape("<?=$opened?>")) + ("; expires="+date.toGMTString());

/*** �з�Ʈ�� �Ϻγ�� �ε� ***/
function openTree(obj)
{
	if(obj.getElementsByTagName('input')[0].value.toString().match(/.php/) == null){
		ifrmCodi.location.href = "../design/iframe.codi.php?design_file=" + obj.getElementsByTagName('input')[0].value + "&<?=$query_str?>";
	}
	else {
		ifrmCodi.location.href = obj.getElementsByTagName('input')[0].value;
	}
}

function loadHistory(category)
{
	if (category == 'popup/') category = '../design/iframe.popup_list.php';
	else if (category.match(/^popup\//)){
		category = '../design/iframe.popup_register.php?file=' + category.replace(/^popup\//, '');
	}
	else if (category == 'style.css') category = '../design/iframe.css.php';
	else if (category == 'common.js') category = '../design/iframe.js.php';
	else if (category == 'main/intro.htm') category = '../design/iframe.intro.php';

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

	<!-- ���޴� : Start -->
	<table cellpadding=0 cellspacing=0 border=0 style="margin:11px 0 10px 0">
	<tr>
		<td><a href="../design/iframe.default.php" target="ifrmCodi"><img src="../img/btn_q_dskin.gif"></a></td>
		<td width=4></td>
		<td><a href="javascript:popup('../design/popup.banner.php',980,700)"><img src="../img/btn_q_banner.gif"></a></td>
	</tr>
	<tr><td height=4 colspan=5></td></tr>
	<tr>
		<td><a href="javascript:webftp();"><img src="../img/btn_q_ftp.gif"></a></td>
		<td width=4></td>
		<td><a href="javascript:popup('http://image.godo.co.kr/login/imghost_login.php',980,700)"><img src="../img/btn_q_openftp.gif"></a></td>
	</tr>
	<tr><td height=4 colspan=5></td></tr>
	<tr>
		<td><a href="javascript:popup2('popup.webftp_activex.php',760,610,0);"><img src="../img/btn_a_ftp.gif"/></a></td>
		<td width=4></td>
		<td><a href="javascript:popup2('popup.webftp_activex.php?mode=imagehosting',760,610,0);"><img src="../img/btn_a_openftp.gif"/></a></td>
	</tr>
	</table>

	<table cellpadding=0 cellspacing=0 border=0>
	<tr>
        <td><a href="iframe.codi.php?design_file=default&" target="ifrmCodi"><img src="../img/btn_q_alllayout.gif"></a></td>
		<td width=1></td>
		<td><a href="iframe.codi.php?design_file=main/index.htm&" target="ifrmCodi"><img src="../img/btn_q_mainpage.gif"></a></td>
	</tr>
	<tr><td height=4 colspan=5></td></tr>
	<tr>
		<td><a href="javascript:stylesheet();"><img src="../img/btn_q_ss.gif"></a></td>
		<td width=1></td>
		<td><a href="iframe.intro.php" target="ifrmCodi"><img src="../img/btn_q_intro.gif"></a></td>
	</tr>
	<tr><td height=4 colspan=5></td></tr>
	<tr>
		<td><a href="iframe.popup_list.php" target="ifrmCodi"><img src="../img/btn_q_popup.gif"></a></td>
		<td width=1></td>
		<td><a href="iframe.multi_popup_list.php" target="ifrmCodi"><img src="../img/btn_q_multipopup.gif"></a></td>
	</tr>
	<tr><td height=4 colspan=5></td></tr>
	<tr>
		<td><a href="iframe.rollbanner.php" target="ifrmCodi"><img src="../img/btn_m_popup.gif"></a></td>
		<td width=1></td>
		<td><a href="javascript:popupLayer('./popup.link.php',700,600)"><img src="../img/btn_pageurl_q.gif"></a></td>
	</tr>
	<tr><td height=4 colspan=5></td></tr>
	<tr>
		<td><a href="iframe.cart.tab.php" target="ifrmCodi"><img src="../img/btn_q_cart_tab.gif"></a></td>
		<td width=1></td>
		<td><a href="iframe.bgm.setting.php" target="ifrmCodi"><img src="../img/btn_m_bgm.gif"></a></td>
	</tr>
	<tr><td height=4 colspan=5></td></tr>
	<tr>
		<td><a href="http://blog.daum.net/daummaps/482" onclick="javascript:alert('���� �൵����� API ���񽺰� ����Ǿ����ϴ�. ���������� �൵����� ���񽺸� �̿��Ͽ� �츮ȸ���� �൵�� �־��ּ���');" target="_blank"><img src="../img/btn_map.gif"></a></td>
		<td width=1></td>
		<td><a href="javascript:popup_bannereditor();"><img src="../img/btn_leftmenu_logobannerdit.gif" alt="��ʿ�����"></a></td>
	</tr>
	<tr><td height=4 colspan=5></td></tr>
</table>

<table>
<tr><td>
<div style="background:url('../img/codi/webfont_banner.gif') no-repeat;width:192px;height:100px;margin:10px 0px 0px 0px;padding:30px 5px 5px 5px;overflow:hidden"><iframe src="http://godo.co.kr/userinterface/_font/font_left_recommend.php?iframe=yes&shopHost=<?=$_SERVER['HTTP_HOST']?>&shopSno=<?=$godo['sno']?>&homedir=<?=$cfg['rootDir']?>" frameborder="0" width="180" height="60" scrolling="no"></iframe></div>
</td></tr>
</table>

<table>
	<tr><td colspan=5 height=25 valign=bottom align=center><img src="../img/line_html_codi.gif"></td></tr>
	</table>
	<!-- ���޴� : End -->

	<?
	### ������Ų�� �޴� ���
	if($cfg['tplSkinWork'] == 'easy'){
	?>
	<table width="177" cellpadding="0" cellspacing="0" border="0">
	<?
	$i=1;
	if($menu['title'][$i] && count($menu['subject'][$i])){
	?>
	<!-------------------- ���� ū�޴� ���� ------------------------------->
	<tr>
		<td background="../img/left_navi_bg.gif" height="25" class="lmenu"><?=$menu['title'][$i]?></td>
	</tr>
	<tr>
		<td style="padding:8px 0 16px 8px">

		<table cellpadding=2 cellpadding="0" cellspacing="0" border="0">
		<? for ($j=0;$j<count($menu['subject'][$i]);$j++){
		if($menu['subject'][$i][$j]){
		?>
		<!-------------------- ���� �����޴� ���� ------------------------------->
		<tr>
			<td style="font:8pt dotum;letter-spacing:-1px;line-height:16px;padding-left:6px">
			<?if(trim($menu['value'][$i][$j])){?>
			<a href="<?=$menu['value'][$i][$j]?>" name="navi" <?if(preg_match('/'.str_replace('/','\/',$menu['value'][$i][$j]).'/',$_SERVER['SCRIPT_FILENAME'])){?>style="font:bold"<?}?>>
			<?}?>
			<?=trim($menu['subject'][$i][$j])?>
			<?if(trim($menu['value'][$i][$j])){?></b></a><?}?>
			<?if(preg_match('/realname/',$menu['value'][$i][$j]) && !$use_realname){?>
			<img src="../img/btn_nouse.gif" align="absmiddle" />
			<?}?>
			<?if(preg_match('/pg.php/',$menu['value'][$i][$j]) && !$use_pg){?>
			<img src="../img/btn_nouse.gif" align="absmiddle" />
			<?}?>
			<?if(preg_match('/etax.php/',$menu['value'][$i][$j]) && !$godo[tax]){?>
			<img src="../img/btn_nouse.gif" align="absmiddle" />
			<?}?>
			</td>
		</tr>
		<? }} ?>
		</table>

		</td>
	</tr>
	<?}?>
	</table>
	<?}?>

	<!-- Ʈ�� : Start -->
	<div id="treeCodi" class="scroll" style="overflow-y:hidden;">
	<div style="padding-bottom:1px"><b style="color:0094C3;"><?=$cfg['tplSkinWork']?> (��Ų)</b></div>
	<div class="DynamicTree"><div class="wrap" id="tree">
	</div></div>
	</div>
	<!-- Ʈ�� : End -->

	<!-- ���ο� ������ �߰��ϱ� : Start -->
	<div style="padding-bottom:10px"><a href="javascript:popupLayer('./codi/popup.create.php')"><img src="../img/btn_q_newpage.gif" border=0></a></div>
	<!-- ���ο� ������ �߰��ϱ� : End -->

	</td>
	<td valign=top width=100% style="padding-left:10px">
	<div id="designBanner"><script>panel('designBanner', 'design');</script></div>

	<? if($_GET['ifrmCodiHref']):?>
		<iframe id=ifrmCodi name=ifrmCodi src="<?=$_GET['ifrmCodiHref']?>" style="width:100%;height:500px;" frameborder=0></iframe>
	<? else: ?>
		<iframe id=ifrmCodi name=ifrmCodi src="<?=($_GET['design_file'] ? '../../blank.txt' : '../design/iframe.default.php')?>" style="width:100%;height:500px;" frameborder=0></iframe>
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
