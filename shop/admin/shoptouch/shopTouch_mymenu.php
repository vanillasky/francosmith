<?php

$location = "���θ� App���� > ���̼� ����";
include "../_header.php";
@include "../../lib/pAPI.class.php";
@include_once "../../lib/json.class.php";
$pAPI = new pAPI();
$json = new Services_JSON(16);

$expire_dt = $pAPI->getExpireDate();
if(!$expire_dt) {
	msg('���� ��û�Ŀ� ��밡���� �޴��Դϴ�.', -1);
}

$now_date = date('Y-m-d 23:59:59');
$tmp_now_date = date('Y-m-d 23:59:59', mktime(0,0,0, substr($now_date, 5, 2), substr($now_date, 8, 2) - 30, substr($now_date, 0, 4)));
if($expire_dt < $tmp_now_date) {
	msg('���� ���Ⱓ ������ 30���� ���� ���񽺰� ���� �Ǿ����ϴ�.\n���񽺸� �ٽ� ��û�� �ֽñ� �ٶ��ϴ�.', -1);
}

$tmp_mymenu = $pAPI->getMyMenu($godo['sno']);
$arr_mymenu = $json->decode($tmp_mymenu);

$basic_mymenu = Array();

$basic_mymenu = Array(
	'�α���' => array('menu_name' => '�α���','menu_web_url' => 'http://'.$_SERVER['HTTP_HOST'].'/shopTouch/shopTouch_mem/login.php','visibility' => 'true'),
	'��ٱ���' => array('menu_name' => '��ٱ���','menu_web_url' => 'http://'.$_SERVER['HTTP_HOST'].'/shopTouch/shopTouch_goods/cart.php','visibility' => 'true'),
	'�ֹ�/���' => array('menu_name' => '�ֹ�/���','menu_web_url' => 'http://'.$_SERVER['HTTP_HOST'].'/shopTouch/shopTouch_myp/orderlist.php','visibility' => 'true'),
	'1:1����' => array('menu_name' => '1:1����','menu_web_url' => 'http://'.$_SERVER['HTTP_HOST'].'/shopTouch/shopTouch_myp/qna.php','visibility' => 'true'),
	'��������' => array('menu_name' => '��������','menu_web_url' => 'http://'.$_SERVER['HTTP_HOST'].'/shopTouch/shopTouch_myp/couponlist.php','visibility' => 'true'),
	'�����ݳ���' => array('menu_name' => '�����ݳ���','menu_web_url' => 'http://'.$_SERVER['HTTP_HOST'].'/shopTouch/shopTouch_myp/emoneylist.php','visibility' => 'true'),
	'���� ��ǰ�ı�' => array('menu_name' => '���� ��ǰ�ı�','menu_web_url' => 'http://'.$_SERVER['HTTP_HOST'].'/shopTouch/shopTouch_myp/review.php','visibility' => 'true'),
	'���� ��ǰ����' => array('menu_name' => '���� ��ǰ����','menu_web_url' => 'http://'.$_SERVER['HTTP_HOST'].'/shopTouch/shopTouch_myp/qna_goods.php','visibility' => 'true'),
	'FAQ' => array('menu_name' => 'FAQ','menu_web_url' => 'http://'.$_SERVER['HTTP_HOST'].'/shopTouch/shopTouch_myp/faq.php','visibility' => 'true')
);

if(!$arr_mymenu['code'] && !empty($arr_mymenu) && is_array($arr_mymenu)) {
	foreach($arr_mymenu as $row_mymenu) {
		if(!empty($basic_mymenu[$row_mymenu['menu_name']])) {
			$basic_mymenu[$row_mymenu['menu_name']]['menu_idx'] = $row_mymenu['menu_idx'];
			$basic_mymenu[$row_mymenu['menu_name']]['visibility'] = $row_mymenu['visibility'];
		}
		else {
			$del_mymenu[$row_mymenu['menu_name']] = $row_mymenu['menu_idx'];
		}
	}

}

foreach($basic_mymenu as $row_basic) {
	if(!$row_basic['menu_idx']) {
		$menu_idx = 0;

		$menu_idx = $json->decode($pAPI->myMenuAdd($godo['sno'], $row_basic));

		if($menu_idx) $basic_mymenu[$row_basic['menu_name']]['menu_idx'] = $menu_idx['menu_idx'];
	}

}

if(!empty($del_mymenu) && is_array($del_mymenu)) {
	foreach($del_mymenu as $row_del) {
		$tmp_del['menu_idx'] = $row_del;
		$ret = $pAPI->myMenuDelete($godo['sno'], $tmp_del);
	}
}

$arr_mymenu = $basic_mymenu;

?>
<script type="text/javascript">
function chkBoxAll(El,mode)
{
	if (!El || !El.length) return;

	for (i=0;i<El.length;i++){
		El[i].checked = (mode=='rev') ? !El[i].checked : mode;
	}
}
</script>
<?
if($expire_dt < $now_date) {
	@include('shopTouch_expire_msg.php');
}
?>
<form name=form method=post action="indb.php" enctype="multipart/form-data">
<input type=hidden name=mode value="mymenu">

<div class="title title_top">���Ǹ޴� ���� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=shoppingapp&no=10')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr><td class=rnd colspan=3></td></tr>
<tr class="rndbg">
	<th width="50" align="center">���</th>
	<th width="200" align="center">��ɸ�</th>
	<th width="200" align="center">URL</th>
</tr>
<tr><td class="rnd" colspan="3"></td></tr>
<?
if(!empty($basic_mymenu)) {
	$i = 0;
	foreach($basic_mymenu as $row_mymenu) {
		$checked['visibility'] = Array();
		$checked['visibility'][$row_mymenu['visibility']] = 'checked';
?>
<tr><td height=4 colspan=3></td></tr>
<tr height=25>
	<td width="50" class="noline" align="center"><input type=checkbox name="visibility[<?=$i?>]" value="true" <?=$checked['visibility']['true']?> /></td>
	<td width="200" align="center"><input type="hidden" name="menu_idx[<?=$i?>]" value="<?=$row_mymenu['menu_idx']?>" /><input type="hidden" name="menu_name[<?=$i?>]" value="<?=$row_mymenu['menu_name']?>" /><?=$row_mymenu['menu_name']?></td>
	<td width="200" align="center"><input type="hidden" name="menu_web_url[<?=$i?>]" value="<?=$row_mymenu['menu_web_url']?>" /><?=$row_mymenu['menu_web_url']?></td>
</tr>
<tr><td height=4></td></tr>
<tr><td colspan=3 class=rndline></td></tr>
<?
	$i++;
	}
}
?>
</table>

<div class="button">
<input type=image src="../img/btn_modify.gif">
</div>

</form>

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">���θ� App ���� ���Ű��Ե鲲 ������ ���̼��� �޴��� �����ϴ� ����Դϴ�. </td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

<? include "../_footer.php"; ?>