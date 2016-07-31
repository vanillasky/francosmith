<?
/*********************************************************
* 파일명     :  orderSendPop.php
* 프로그램명 :  주문전송진행창
* 작성자     :  dn
* 생성일     :  2012.05.22
**********************************************************/
$location = "셀리 > 주문전송진행";
include "../_header.popup.php";
include "../../conf/config.pay.php";
include "../../lib/sAPI.class.php";

$sAPI = new sAPI();

$order_idx_arr = $_POST['chk'];
$send_status = $_POST['send_status'];
unset($_POST);

$code_arr = array();
$code_arr['grp_cd'] = 'order_status';
$tmp_order_status = $sAPI->getcode($code_arr, 'hash');
unset($conde_arr);

$code_arr = array();
$code_arr['grp_cd'] = 'MALL_CD';
$mall_cd = $sAPI->getcode($code_arr, 'hash');
unset($code_arr);


?>
<script type="text/javascript" src="./js/selly.js"></script>
<script type="text/javascript">
var send_idx = 0;

function successAjax(tmp_data) {

	res_data = eval('(' + tmp_data + ')');
	if(!res_data) {
		$('res_'+res_data.order_idx).innerHTML = '처리 중 오류가 발생했습니다.<br />잠시후 다시 시도해 주세요';
	}
	else {
		if(res_data.code == '000') {
			$('res_'+res_data.order_idx).innerHTML += ' → ' + res_data.msg + ' : ' + '<?=$tmp_order_status[$send_status]?>';
		}
		else {
			$('res_'+res_data.order_idx).innerHTML = res_data.msg;
		}
	}

	send_idx++;
	sendOrder(send_idx);
}

function sendOrder(idx) {

	$('btn_close').disabled = true;
	$('btn_close').src = '../img/btn_close2_out.gif';

	if($('order_idx_'+idx)) {
		var order_idx = $('order_idx_'+idx).value;
		var send_status = $('send_status').value;

		sellyLink.sendOrder(order_idx, send_status);
	}
	else {
		btnActivity();
		return false;
	}

}

function btnActivity() {
	$('btn_close').disabled = false;
	$('btn_close').src = '../img/btn_close2.gif';
}

function winClose() {
	if($('btn_close').disabled != true) {
		if(opener) {
			opener.location.reload();
		}
		window.close();
	}
}

document.observe('dom:loaded', function() {
	sendOrder(send_idx);
});
</script>
<div class="title title_top"><?=$tmp_order_status[$send_status]?>처리 진행<span><?=$tmp_order_status[$send_status]?>처리 진행중입니다. 완료 전에 창을 닫거나 esc버튼을 누르시면 수집이 중단됩니다.</span></div>
<input type="hidden" id="send_status" name="send_status" value="<?=$send_status?>" />
<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr><td class=rnd colspan=4></td></tr>
<tr class="rndbg">
	<th width="20%" align="center">마켓</th>
	<th width="20%" align="center">로그인 ID</th>
	<th width="30%" align="center">마켓주문번호</th>
	<th width="30%" align="center">처리결과</th>
</tr>
<tr><td class="rnd" colspan="4"></td></tr>
<tr><td height=4 colspan=4></td></tr>
<? 
$i = 0;
foreach($order_idx_arr as $order_idx) { 

	$ord_query = $db->_query_print('SELECT order_idx, mall_cd, mall_login_id, mall_order_no, status FROM '.GD_MARKET_ORDER.' WHERE order_idx=[i]', $order_idx);
	$res_ord = $db->_select($ord_query);
	$row_ord = $res_ord[0];

	?>
<tr><td height=4 colspan=4></td></tr>
<tr height=25>
	<td align="center">
		<?=$mall_cd[$row_ord['mall_cd']]?>
		<input type="hidden" id="order_idx_<?=$i?>" value="<?=$order_idx?>" />
	
	</td>
	<td align="center"><?=$row_ord['mall_login_id']?></td>
	<td align="center"><?=$row_ord['mall_order_no']?></td>
	<td align="center"><span id="res_<?=$order_idx?>"class="extext"><?=$tmp_order_status[$row_ord['status']]?></span></td>
</tr>
<tr><td height=4></td></tr>
<tr><td colspan=4 class=rndline></td></tr>
<? 
	unset($order_idx, $ord_query, $res_ord, $row_ord);
	$i++;
} 
?>
</table>
<div style="margin-top:20px;text-align:right;">
	<a href="javascript:winClose();"><img id="btn_close" src="../img/btn_close2.gif" alt="닫기" align="absmiddle" /></a>
</div>