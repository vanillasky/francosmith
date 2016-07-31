<?
define('ADMINLOGSTATE', 'NO');
include dirname(__FILE__)."/../conf/config.php";
include dirname(__FILE__)."/lib.php";
include dirname(__FILE__)."/lib.skin.php";
include dirname(__FILE__)."/../lib/menu.class.php";
include dirname(__FILE__)."/../lib/blogshop.class.php";

$blogshop = new blogshop();
$mn = new Menu();
$section = $mn->cmKey;
if($mn->section!='etc'){
	$menu = $mn->getMenu();
}else{
	$section = $mn->section;
}

if (!$mainpage)
{
	$over[$section] = "_on";
	if($section=='mobileShop2') $over['mobileShop'] = '_on';
}

### �α��� ���� �ð�
setCookie('Xtime',time(),0,'/');

### PG ��� ��Ȳ
if($cfg['settlePg']){
	@include_once dirname(__FILE__)."/../conf/pg.".$cfg['settlePg'].".php";
	if($pg['id'] != '') $use_pg = true;
	else $use_pg = false;
}else $use_pg = false;

### �����̼� PG �����Ȳ
$todayShop = Core::loader('todayshop');
$tsCfg = $todayShop->cfg;
$tsPG = ($tsCfg['pg'] != '') ? unserialize($tsCfg['pg']) : array();
if ($tsPG['cfg'][settlePg] && !empty($tsPG['set'])) {
	$use_todayshop_pg = true;
} else $use_todayshop_pg = false;

### ��α׼� url
if($blogshop->linked && $blogshop->config['iframe_url']){
	$blogshop_url = str_replace('/admin_iframe','',$blogshop->config['iframe_url']);
}

### ����� ����Ϲ��� (1.0, 2.0) �� Ȯ���ϰ�, ���̰��̼� �޴��� URL ��θ� �������ش�.
$version2_apply_file_name = ".htaccess";
 ## ���� ��������� Ȯ���ϴ�
if ( file_exists(dirname(__FILE__)."/../../m/".$version2_apply_file_name) ) {

	$aFileContent = file(dirname(__FILE__)."/../../m/".$version2_apply_file_name);

	for ($i=0; $i<count($aFileContent); $i++) {
		if (preg_match("/RewriteRule/i", $aFileContent[$i])) {
			break;
		}
	}
	if ($i < count($aFileContent)) {
		$mobileShop = "mobileShop2";
	} else {
		$mobileShop = "mobileShop";
	}
} else {
	$mobileShop = "mobileShop";
}

// �����ΰ����϶� SET_HTML_DEFINE ����, SET_HTML5 ����
if ((preg_match('/\/admin\/design$/', dirname($_SERVER['PHP_SELF'])) || preg_match('/\/admin\/mobileShop(2|)$/', dirname($_SERVER['PHP_SELF']))) && (strpos(basename($_SERVER['PHP_SELF']), 'iframe.') === 0) || basename($_SERVER['PHP_SELF']) == 'codi.php') {
	$SET_HTML_DEFINE = true;
	$SET_HTML5 = true;
}

// SET_HTML_DEFINE�� ����� �������� �ű� ������ ������(adm_*.php) ������ DTD(xhtml)�� ����
if ($SET_HTML_DEFINE || strpos(basename($_SERVER['PHP_SELF']), 'adm_') === 0) {
	if ($SET_HTML5) {
		$DEFINE_DOCTYPE = '<!DOCTYPE html>';
		$scriptLoad .= '<style>img { vertical-align:top; }</style>';
	}
	else $DEFINE_DOCTYPE = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
	$DEFINE_HTML = $DEFINE_DOCTYPE.'<html xmlns="http://www.w3.org/1999/xhtml" lang="ko" xml:lang="ko">';
	$DEFINE_EXTRA_TAGS = '<meta http-equiv="X-UA-Compatible" content="IE=edge" />';
	$DEFINE_EXTRA_TAGS.= '<script type="text/javascript" src="'.$cfg['rootDir'].'/lib/js/jquery-1.10.2.min.js"></script>';
	$DEFINE_EXTRA_TAGS.= '<script type="text/javascript" src="'.$cfg['rootDir'].'/lib/js/jquery-ui.js"></script>';
	$DEFINE_EXTRA_TAGS.= '<script type="text/javascript">jQuery.noConflict();</script>';
}
else {
	$DEFINE_HTML = '<html>';
	$DEFINE_EXTRA_TAGS = '';
}
?>
<?php echo $DEFINE_HTML; ?>
<head>
<title>'Godo Shoppingmall e���� Season4 �����ڸ��'</title>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$_CFG['global']['charset']?>">
<?php echo $DEFINE_EXTRA_TAGS; ?>
<? if($mainpage) {?>
<link href="../basic/css/main.css" rel="stylesheet" type="text/css"/>
<link href="http://adminwidget.godo.co.kr/static/css/admin-widget.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="http://adminwidget.godo.co.kr/static/js/jquery-godo-widget.js"></script>
<script type="text/javascript" src="http://adminwidget.godo.co.kr/static/js/adm_widget.js"></script>
<script type="text/javascript" src="http://adminwidget.godo.co.kr/static/js/adm_widget_loader.js"></script>
<script type="text/javascript" src="<?php echo $cfg['rootDir']; ?>/admin/basic/js/adm_panelAPI.js"></script>
<script type="text/javascript" src="<?php echo $cfg['rootDir']; ?>/admin/basic/js/adm_memo.js"></script>
<? } else { ?>
<link rel="styleSheet" href="../style.css">
<? } ?>
<link rel="styleSheet" href="../_contextmenu/contextmenu.css?<?=time()?>">
<style>
/*** ���� ���̾ƿ� ���� ***/
body {margin:0 0 0 0px}
</style>
<script type="text/javascript" src="../common.js?actTime=<?php echo time(); ?>"></script>
<script type="text/javascript" src="../prototype.js"></script>
<script type="text/javascript" src="../prototype_ext.js"></script>
<script type="text/javascript" src="../_contextmenu/contextmenu.js"></script>
<script type="text/javascript" src="../godo.form_helper.js"></script>
<script type="text/javascript" src="../../lib/js/json/json2.min.js"></script>
<?=$scriptLoad?>
<div id="dynamic"></div>
<iframe name="ifrmHidden" src="../../blank.txt" style="display:none;width:100%;height:500px;"></iframe>
<div id="jsmotion"></div>
<?
$query = "SELECT name, url, target FROM ".GD_CONTEXTMENU." WHERE m_no = '".$sess['m_no']."'";
$rs = $db->query($query);
$context_menu = array();
while ($row = $db->fetch($rs,1)) {
	$context_menu[] = $row;
}

?>
<script type="text/javascript">
function godo_context_menu() {
	if (getCookie('_TOGGLE_CONTEXT_MENU_') == 1) $('el-use-context-menu').checked = true;

	nsGodoContextMenu.init({
		option  : {
					contextWidth : 180,
					zIndex		 : 999999
		}
		<? if (sizeof($context_menu) > 0) echo ',menu : '.gd_json_encode($context_menu); ?>
	});

}

function godo_folding_menu() {
	var h4s =  $H($$('.admin-left-menu > h4'));
	var uls =  $H($$('.admin-left-menu > ul'));

	var el;
	var today = new Date();
	var expire_date = new Date(today.getTime() + 31536000);
	var loc = window.location.pathname.split('/');
	loc.pop();
	loc = loc.join('_');

	h4s.each(function(pair) {

		if (typeof pair.value != 'object') return;

		var el = uls.get(pair.key);

		if (getCookie(loc + '_' + pair.key) == 'none') {
			pair.value.addClassName('fold');
			el.setStyle({display:'none'});
		}

		try
		{
			Event.observe(pair.value, 'click', function(event) {

				if (el.getStyle('display') != 'none') {
					if (!pair.value.hasClassName('fold')) pair.value.addClassName('fold');
					el.setStyle({display:'none'});
					setCookie( loc + '_' + pair.key, 'none', expire_date, '/');
				}
				else {
					if (pair.value.hasClassName('fold')) pair.value.removeClassName('fold');
					el.setStyle({display:'block'});
					setCookie( loc + '_' + pair.key, 'block', expire_date, '/');
				}
			});
		}
		catch (e) { }
	});
}

Event.observe(document, 'dom:loaded', godo_context_menu, false);
Event.observe(document, 'dom:loaded', godo_folding_menu);

</script>

<body class="scroll">
<table width="100%" height="89" cellpadding="0" cellspacing="0" border="0">
<tr>
	<td valign="top">
		<!-------------------- �ΰ�, ���� �޴� ���� --------------------->
		<? include '../_navigator.inc.php';  ?>
		<!-------------------- �ΰ�, ���� �޴� �� --------------------->

		<!--------------- ���г� ���� --------------------->
			<?
			// �Ǹ޴� ��������
			$tabMenu = array(
			'basic/default.php'
			, 'design/codi.php'
			, 'goods/list.php'
			, 'order/list.php'
			, 'member/list.php'
			, 'board/list.php'
			, 'event/list.php'
			, 'marketing/main.php'
			, 'log/index.php'
			, 'mobileShop/index.php'
			, 'mobileShop2/index.php'
			, 'todayshop/index.php'
			, 'etc/index.php'
			, 'blog/index.php'
			, 'shople/index.php'
			, 'selly/index.php'
			);
			preg_match('/[a-z]*\/[a-z]*\.[a-z]*$/i', $_SERVER['SCRIPT_FILENAME'], $tmp);
			if (in_array($tmp[0],$tabMenu) === true) {
				$cookieTab = 'maxtab_'.$section;
				if (isset($_COOKIE[$cookieTab]) === false) {
					echo '<span id="maxtab"><script>panel("maxtab", "'.$section.'");</script></span>';
				}
			}
			?>
		<!--------------- ���г� �� --------------------->
	</td>
</tr>

<!-------------------- ����, ����Ÿ��Ʋ�̹���, �޴����� ���� ------------------------------->
<? if (!$mainpage && !$noleft){ 	// �����̿��� ��� ?>
</table>
<table cellpadding="0" cellspacing="0" border="0">
<tr>
	<td valign="top" id="leftMenu" width="200" style="background:url('../img/sub_leftmenu_back.gif') repeat-y;">

	<table height="100%" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td><img src="../img/left_<?=$section?>_title.gif" class="hand" onClick="hiddenLeft();" /></td>
	</tr>

	<!-------------------- ���� ����Ÿ��Ʋ�̹��� ���� ------------------------------->
	<tr>
		<td height="100%" valign="top">

		<div class="admin-left-menu">
		<?
		$__loc = str_replace('/','_',dirname($_SERVER['PHP_SELF']));

		for ($i=0,$m=sizeof($menu['title']);$i<$m;$i++) {
			$cntSubject = count($menu['subject'][$i]);
			if($menu['title'][$i] && $cntSubject){
		?>
		<h4><?=$menu['title'][$i]?></h4>
		<ul style="display:<?=$_COOKIE[$__loc.'_'.$i] == 'none' ? 'none' : 'block'?>">
			<? for ($j=0;$j<$cntSubject;$j++){
			if($menu['subject'][$i][$j]){
			?>
			<!-------------------- ���� �����޴� ���� ------------------------------->
			<li <?=($j+1==$cntSubject ? 'class="noborder"' : '')?>>
				<?if(trim($menu['value'][$i][$j])){?>
				<!--�޴� Ÿ�� �߰� 2010.12.29 by slowj-->
				<a href="<?=$menu['value'][$i][$j]?>" name="navi" <?if(isset($menu['target'][$i][$j])) {?>target="<?=$menu['target'][$i][$j]?>"<?}?>
				<?
				list($script_filename,$query_string)=explode('?',$menu['value'][$i][$j]);
				if (str_replace(DIRECTORY_SEPARATOR,'/',realpath($script_filename)) == $_SERVER['SCRIPT_FILENAME']) {
					$c = true;

					parse_str($query_string, $tmp1);
					parse_str($_SERVER['QUERY_STRING'], $tmp2);

					foreach ($tmp1 as $k => $v) {
						if (!array_key_exists($k, $tmp2)) {
							$c = false;
							break(1);
						}
					}

					if ($c) {
						echo ' style="font-weight:bold;"';
					}

				}
				?>>
				<?}?>
				<?=trim($menu['subject'][$i][$j])?>
				<?if(trim($menu['value'][$i][$j])){?></a><?}?>
				<?	/**
						2011-02-01 by x-ta-c
						pg ��� ���� üũ �Ͽ� �̻���� ������ ��� �� ����
						�����Խ��� �̿��� ���� �׽�Ʈ�� �ƴ� ������ preg_match �� ���� ����.
					 */
					if ( (strpos($menu['value'][$i][$j],'todayshop/config.pg.php') !== false) || (strpos($menu['value'][$i][$j],'todayshop/config.pg.free.php') !== false) ) {
						if (!$use_todayshop_pg) echo '<img src="../img/btn_nouse.gif" align="absmiddle" />';
					}
					else if (strpos($menu['value'][$i][$j],'pg.php') !== false) {
						if (!$use_pg) echo '<img src="../img/btn_nouse.gif" align="absmiddle" />';
					}
				?>
				<?if(preg_match('/etax.php/',$menu['value'][$i][$j]) && !$godo[tax]){?>
				<img src="../img/btn_nouse.gif" align="absmiddle" />
				<?}?>
				</li>
			<? }} ?>
		</ul>
		<?}}?>
		</div>

		<?if($_SERVER['HTTPS'] != 'on'){?>
		<div id="panelside" style="padding-left:7px"><script>panel('panelside', '<?=$section?>');</script></div>
		<?}?>

		</td>
	</tr>
	</table>

	</td>

	<!-------------------- ��ü ���� �� ------------------------------->
	<td width="19" style="background:url('../img/icon_menuon_bg.gif') repeat-y;" valign="top" id="sub_left_menu" style="display:none">
		<img id="btn_menu" src="../img/icon_menuon.gif" class="hand" onClick="hiddenLeft();" style="display:none;" />
	</td>
	<!------------------------------------------------------------------>
	<!-------------------- ���� ���� �ǻ�ܽ���, �׺�, ��� ------------------------------->
	<td valign="top" height="100%" width="100%" style="background:url('../img/sub_topback.gif') repeat-x;">

	<!--------------  Location ���� ------------------->
	<div style="padding:16px 0 3px 0; margin:0 0 6px 6px; border-bottom:solid 1px #e6e6e6;">
		<img src="../img/b_home.gif"/><span id="location" style="font-family:Dotum; font-size:11px; color:#444444;"><span style="color:#888888">HOME</span> > <?=$location?></span>
	</div>
	<!--------------  Location �� ------------------->

	<!-------------------- ���� ���� ���������� ����  ------------------------------->
	<table width="100%" cellpadding="0" cellspacing="0" bgcolor="white" border="0">
	<tr>
		<td valign="top" style="padding-left:6px; padding-right:6px;">

<? } else { # �����ΰ�� ?>
<tr>
	<td height="100%" valign="top">

	<table width="100%" height="100%" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td valign="top">
<? } ?>

<?
### ���������� ����
if ($mn->isAccess() === false){
?>
<table cellpadding="0" cellspacing="0" border="0" id="errBox" background="http://www.godo.co.kr/userinterface/img/trans_guide_back.gif" width="394" height="201">
<tr>
	<td valign="middle" align="center">
	<div style="padding-top:3px;"><font color="#444444">�� ����� ���� ����ϰ� ���<br /> [<font color="green"><b><?=$godo['ecName']?></b></font>] ������ �������� �ʽ��ϴ�.</div>
	<div style="padding-top:7px;"><a href="http://www.godo.co.kr/service/sub_06_marketing.php" target="_new"><font color="#0074ba"><b>[���� �ΰ����� ���캸��]</b></font></a></div>
	</td>
</tr>
</table>
<?
	include "../_footer.php";
	exit;
}

?>
