<?
/*********************************************************
* 파일명     :  orderScrapPop.php
* 프로그램명 :  주문수집진행창
* 작성자     :  dn
* 생성일     :  2012.05.12
**********************************************************/
$location = "셀리 > 주문수집진행";
include "../_header.popup.php";
include "../../lib/sAPI.class.php";

$sAPI = new sAPI();

$minfo_idx_arr = $_POST['chk'];

$code_arr = array();
$code_arr['grp_cd'] = 'MALL_CD';

$mall_cd = $sAPI->getcode($code_arr, 'hash');
unset($code_arr);

$code_arr = array();
$code_arr['grp_cd'] = 'SCRAP_ORDER_STATUS';

$scrap_order_status = $sAPI->getcode($code_arr, 'hash');
unset($code_arr);

$arr = array();
$scrap_data = $sAPI->getMallLoginId($arr, 'hash');
unset($arr);
?>
<script type="text/javascript" src="./js/selly.js"></script>
<script type="text/javascript">
var scrap_idx = 0;

function successAjax(tmp_data) {
	
	res_data = eval('(' + tmp_data + ')');

	if(!res_data) {
		$('res_'+res_data.scrap_order_status+'_'+res_data.minfo_idx).innerHTML = '수집 중 오류가 발생했습니다.<br />잠시후 다시 시도해 주세요';
	}
	else if(res_data.code) {
		$('res_'+res_data.scrap_order_status+'_'+res_data.minfo_idx).innerHTML = res_data.msg;
	}
	else {
		$('res_'+res_data.scrap_order_status+'_'+res_data.minfo_idx).innerHTML = '전체:'+res_data.total_cnt;
		
		if(res_data.total_cnt) {
			$('res_'+res_data.scrap_order_status+'_'+res_data.minfo_idx).innerHTML += '<br />'+'수집:'+res_data.new_cnt+'<br />'+'기수집:'+res_data.old_cnt;
		}

		if(res_data.err_cnt) {
			$('res_'+res_data.scrap_order_status+'_'+res_data.minfo_idx).innerHTML += '<br />수집오류:'+res_data.err_cnt;
		}
	}

	if(res_data.scrap_order_status == 'adjust') {
		$('res_all_'+res_data.minfo_idx).innerHTML = '수집완료';
	}

	scrap_idx++;
	scrapOrder(scrap_idx);
}

function scrapOrder(idx) {

	$('btn_market_order').disabled = true;
	$('btn_market_order').src = '../img/btn_orderscrappop01_out.gif';
	$('btn_all_order').disabled = true;
	$('btn_all_order').src = '../img/btn_orderscrappop02_out.gif';
	
	if($('minfo_idx_'+idx)) {

		var minfo_idx = $('minfo_idx_'+idx).value;
		var status_key = $('status_key_'+idx).value;
		sellyLink.scrapOrder(minfo_idx, status_key);
	}
	else {
		btnActivity();
		return false;
	}	
}

function btnActivity() {
	$('btn_market_order').disabled = false;
	$('btn_market_order').src = '../img/btn_orderscrappop01.gif';
	$('btn_all_order').disabled = false;
	$('btn_all_order').src = '../img/btn_orderscrappop02.gif';
}

function goMarketOrder() {
	if($('btn_market_order').disabled != true) {

		if(opener) {
			opener.location.href="./marketOrderList.php";
		}
		window.close();
	}
}

function goAllOrder() {
	if($('btn_all_order').disabled != true) {
	
		if(opener) {
			opener.location.href="../order/list.integrate.php";
		}
		window.close();
	}
}

document.observe('dom:loaded', function() {
	scrapOrder(scrap_idx);
});
</script>
<div class="title title_top">주문수집 진행<span>주문수집 진행중입니다. 완료 전에 창을 닫거나 esc버튼을 누르시면 수집이 중단됩니다.</span></div>
<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr><td class=rnd colspan=4></td></tr>
<tr class="rndbg">
	<th width="20%" align="center">마켓</th>
	<th width="20%" align="center">로그인 ID</th>
	<th width="30%" align="center">주문상태</th>
	<th width="30%" align="center">수집결과</th>
</tr>
<tr><td class="rnd" colspan="4"></td></tr>
<tr><td height=4 colspan=4></td></tr>
<? 
$i = 0;
foreach($minfo_idx_arr as $minfo_idx) { ?>
<tr><td height=4 colspan=4></td></tr>
<tr height=25>
	<td align="center"><?=$mall_cd[$scrap_data[$minfo_idx]['mall_cd']]?></td>
	<td align="center"><?=$scrap_data[$minfo_idx]['mall_login_id']?></td>
	<td align="center">전체</td>
	<td align="center"><span id="res_all_<?=$minfo_idx?>"class="extext">수집대기중</span></td>
</tr>
<tr><td height=4></td></tr>
<tr><td colspan=4 class=rndline></td></tr>
	<? foreach($scrap_order_status as $status_key => $status_val) { ?>
<tr><td height=4 colspan=4></td></tr>
<tr id="tr_<?=$status_val?>_<?=$minfo_idx?>" height=15>
	<td align="center">
		<input type="hidden" id="minfo_idx_<?=$i?>" value="<?=$minfo_idx?>" />
		<input type="hidden" id="status_key_<?=$i?>" value="<?=$status_key?>" />
	</td>
	<td align="center"></td>
	<td align="center"><?=$status_val?></td>
	<td align="center"><span id="res_<?=$status_key?>_<?=$minfo_idx?>"class="extext">-</span></td>
</tr>
<tr><td height=4></td></tr>
<tr><td colspan=4 class=rndline></td></tr>
	<? $i++; } ?>
<? } ?>
</table>
<div style="margin-top:20px;text-align:right;">
	<a href="javascript:goMarketOrder();"><img id="btn_market_order" src="../img/btn_orderscrappop01.gif" alt="마켓 주문관리 바로가기" align="absmiddle" /></a>
	<a href="javascript:goAllOrder();"><img id="btn_all_order" src="../img/btn_orderscrappop02.gif" alt="통합 주문관리 바로가기" align="absmiddle" /></a>
</div>