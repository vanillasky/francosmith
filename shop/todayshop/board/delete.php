<?
if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['id'])) exit;

@include_once "../../conf/bd_".$_GET['id'].".php";
include "../../_header.php";

$mode = $_GET[mode];

$no = ($mode=="comment") ? $_GET[sno] : $_GET[no];

if (!$mode) $mode = "delete";
$query = ($mode=="comment") ? "select m_no from ".GD_BOARD_MEMO." where sno='".$_GET[sno]."'" : "select m_no from `".GD_BD_.$_GET['id']."` where no='".$_GET[no]."'";
list ($m_no) = $db->fetch($query);

$returnUrl = ($mode=="delete") ? "list.php?".getVars('no') : $_SERVER[HTTP_REFERER];

$bdHeader = stripslashes($bdHeader);
$bdFooter = stripslashes($bdFooter);

$tpl->define('tpl',$tpl->define('list','../../skin_today/'.$cfg['tplSkinToday'].'/board/'.$bdSkin.'/delete.htm'));
$tpl->print_('tpl');

?>
