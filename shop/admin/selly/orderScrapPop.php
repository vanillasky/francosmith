<?
/*********************************************************
* ���ϸ�     :  orderScrapPop.php
* ���α׷��� :  �ֹ���������â
* �ۼ���     :  dn
* ������     :  2012.05.12
**********************************************************/
$location = "���� > �ֹ���������";
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
		$('res_'+res_data.scrap_order_status+'_'+res_data.minfo_idx).innerHTML = '���� �� ������ �߻��߽��ϴ�.<br />����� �ٽ� �õ��� �ּ���';
	}
	else if(res_data.code) {
		$('res_'+res_data.scrap_order_status+'_'+res_data.minfo_idx).innerHTML = res_data.msg;
	}
	else {
		$('res_'+res_data.scrap_order_status+'_'+res_data.minfo_idx).innerHTML = '��ü:'+res_data.total_cnt;
		
		if(res_data.total_cnt) {
			$('res_'+res_data.scrap_order_status+'_'+res_data.minfo_idx).innerHTML += '<br />'+'����:'+res_data.new_cnt+'<br />'+'�����:'+res_data.old_cnt;
		}

		if(res_data.err_cnt) {
			$('res_'+res_data.scrap_order_status+'_'+res_data.minfo_idx).innerHTML += '<br />��������:'+res_data.err_cnt;
		}
	}

	if(res_data.scrap_order_status == 'adjust') {
		$('res_all_'+res_data.minfo_idx).innerHTML = '�����Ϸ�';
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
<div class="title title_top">�ֹ����� ����<span>�ֹ����� �������Դϴ�. �Ϸ� ���� â�� �ݰų� esc��ư�� �����ø� ������ �ߴܵ˴ϴ�.</span></div>
<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr><td class=rnd colspan=4></td></tr>
<tr class="rndbg">
	<th width="20%" align="center">����</th>
	<th width="20%" align="center">�α��� ID</th>
	<th width="30%" align="center">�ֹ�����</th>
	<th width="30%" align="center">�������</th>
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
	<td align="center">��ü</td>
	<td align="center"><span id="res_all_<?=$minfo_idx?>"class="extext">���������</span></td>
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
	<a href="javascript:goMarketOrder();"><img id="btn_market_order" src="../img/btn_orderscrappop01.gif" alt="���� �ֹ����� �ٷΰ���" align="absmiddle" /></a>
	<a href="javascript:goAllOrder();"><img id="btn_all_order" src="../img/btn_orderscrappop02.gif" alt="���� �ֹ����� �ٷΰ���" align="absmiddle" /></a>
</div>