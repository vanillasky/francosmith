<?
/*********************************************************
* ���ϸ�     :  orderSendPop.php
* ���α׷��� :  �ֹ���������â
* �ۼ���     :  dn
* ������     :  2012.05.22
**********************************************************/
$location = "���� > �ֹ���������";
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
		$('res_'+res_data.order_idx).innerHTML = 'ó�� �� ������ �߻��߽��ϴ�.<br />����� �ٽ� �õ��� �ּ���';
	}
	else {
		if(res_data.code == '000') {
			$('res_'+res_data.order_idx).innerHTML += ' �� ' + res_data.msg + ' : ' + '<?=$tmp_order_status[$send_status]?>';
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
<div class="title title_top"><?=$tmp_order_status[$send_status]?>ó�� ����<span><?=$tmp_order_status[$send_status]?>ó�� �������Դϴ�. �Ϸ� ���� â�� �ݰų� esc��ư�� �����ø� ������ �ߴܵ˴ϴ�.</span></div>
<input type="hidden" id="send_status" name="send_status" value="<?=$send_status?>" />
<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr><td class=rnd colspan=4></td></tr>
<tr class="rndbg">
	<th width="20%" align="center">����</th>
	<th width="20%" align="center">�α��� ID</th>
	<th width="30%" align="center">�����ֹ���ȣ</th>
	<th width="30%" align="center">ó�����</th>
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
	<a href="javascript:winClose();"><img id="btn_close" src="../img/btn_close2.gif" alt="�ݱ�" align="absmiddle" /></a>
</div>