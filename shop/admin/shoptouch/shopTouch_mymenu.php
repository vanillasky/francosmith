<?php

$location = "쇼핑몰 App관리 > 마이샵 설정";
include "../_header.php";
@include "../../lib/pAPI.class.php";
@include_once "../../lib/json.class.php";
$pAPI = new pAPI();
$json = new Services_JSON(16);

$expire_dt = $pAPI->getExpireDate();
if(!$expire_dt) {
	msg('서비스 신청후에 사용가능한 메뉴입니다.', -1);
}

$now_date = date('Y-m-d 23:59:59');
$tmp_now_date = date('Y-m-d 23:59:59', mktime(0,0,0, substr($now_date, 5, 2), substr($now_date, 8, 2) - 30, substr($now_date, 0, 4)));
if($expire_dt < $tmp_now_date) {
	msg('서비스 사용기간 만료후 30일이 지나 서비스가 삭제 되었습니다.\n서비스를 다시 신청해 주시기 바랍니다.', -1);
}

$tmp_mymenu = $pAPI->getMyMenu($godo['sno']);
$arr_mymenu = $json->decode($tmp_mymenu);

$basic_mymenu = Array();

$basic_mymenu = Array(
	'로그인' => array('menu_name' => '로그인','menu_web_url' => 'http://'.$_SERVER['HTTP_HOST'].'/shopTouch/shopTouch_mem/login.php','visibility' => 'true'),
	'장바구니' => array('menu_name' => '장바구니','menu_web_url' => 'http://'.$_SERVER['HTTP_HOST'].'/shopTouch/shopTouch_goods/cart.php','visibility' => 'true'),
	'주문/배송' => array('menu_name' => '주문/배송','menu_web_url' => 'http://'.$_SERVER['HTTP_HOST'].'/shopTouch/shopTouch_myp/orderlist.php','visibility' => 'true'),
	'1:1문의' => array('menu_name' => '1:1문의','menu_web_url' => 'http://'.$_SERVER['HTTP_HOST'].'/shopTouch/shopTouch_myp/qna.php','visibility' => 'true'),
	'할인쿠폰' => array('menu_name' => '할인쿠폰','menu_web_url' => 'http://'.$_SERVER['HTTP_HOST'].'/shopTouch/shopTouch_myp/couponlist.php','visibility' => 'true'),
	'적립금내역' => array('menu_name' => '적립금내역','menu_web_url' => 'http://'.$_SERVER['HTTP_HOST'].'/shopTouch/shopTouch_myp/emoneylist.php','visibility' => 'true'),
	'나의 상품후기' => array('menu_name' => '나의 상품후기','menu_web_url' => 'http://'.$_SERVER['HTTP_HOST'].'/shopTouch/shopTouch_myp/review.php','visibility' => 'true'),
	'나의 상품문의' => array('menu_name' => '나의 상품문의','menu_web_url' => 'http://'.$_SERVER['HTTP_HOST'].'/shopTouch/shopTouch_myp/qna_goods.php','visibility' => 'true'),
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

<div class="title title_top">나의메뉴 설정 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=shoppingapp&no=10')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr><td class=rnd colspan=3></td></tr>
<tr class="rndbg">
	<th width="50" align="center">사용</th>
	<th width="200" align="center">기능명</th>
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
<tr><td><img src="../img/icon_list.gif" align="absmiddle">쇼핑몰 App 에서 구매고객님들께 보여질 마이샵의 메뉴를 설정하는 기능입니다. </td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

<? include "../_footer.php"; ?>