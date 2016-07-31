<?
if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['id'])) exit;

@include_once "../../conf/bd_".$_GET['id'].".php";
include "../../_header.php";
include "../../lib/page.class.php";
include "../../lib/board.class.php";

if (!is_file("../../conf/bd_".$_GET[id].".php")) msg("게시판이 존재하지 않습니다",-1);
if ($bdLvlL && $bdLvlL>$sess['level']) msg("글 목록 권한이 없습니다",-1);

### 값이 없는 경우 미리 체크
if(!$bdListImgCntW) $bdListImgCntW = 5;
if(!$bdListImgCntH) $bdListImgCntH = 4;
if($bdSkin == "gallery"){
	if(!$bdListImgSizeW) $bdListImgSizeW = 100;
	if(!$bdListImgSizeH) $bdListImgSizeH = "";
}else{
	if(!$bdListImgSizeW) $bdListImgSizeW = 45;
	if(!$bdListImgSizeH) $bdListImgSizeH = 45;
}

### bd class
$bd = new Board($_GET['page'],$bdPageNum);

$bd->db		= &$db;
$bd->tpl	= &$tpl;
$bd->cfg	= &$cfg;
if ( file_exists( dirname(__FILE__) . '/../../data/skin_today/' . $cfg['tplSkinToday'] . '/admin.gif' ) ) $bd->adminicon = '../../../../data/skin_today/'.$cfg['tplSkinToday'].'/admin.gif';

$bd->id			= $_GET['id'];
$bd->subSpeech	= $_GET['subSpeech'];
$bd->search		= $_GET['search'];
$bd->sess		= $sess;
$bd->ici_admin	= $ici_admin;
$bd->date		= $_GET['date'];

$bd->assign(array(
			bdSearchMode		=> $bdSearchMode,
			bdUseSubSpeech		=> $bdUseSubSpeech,
			bdSubSpeech			=> $bdSubSpeech,
			bdSubSpeechTitle	=> $bdSubSpeechTitle,
			bdLvlR				=> $bdLvlR,
			bdLvlW				=> $bdLvlW,
			bdStrlen			=> $bdStrlen,
			bdNew				=> $bdNew,
			bdHot				=> $bdHot,
			));

$bd->_list();

if ($sess){
	$tpl->assign(readonly,array(name => "readonly style='border:0;font-weight:bold'"));
}

$bdHeader = stripslashes($bdHeader);
$bdFooter = stripslashes($bdFooter);

### tpl class
$tpl->define('list','../../skin_today/'.$cfg['tplSkinToday'].'/board/'.$bdSkin.'/list.htm');
if (!$pageView){
	$tpl->print_('list');
	echo "<script src='../../lib/js/board.js'></script>";
}

//$db->viewLog();

?>