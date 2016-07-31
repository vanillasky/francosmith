<?
/*********************************************************
* 파일명     :  _market_order_form.php
* 프로그램명 :  마켓주문 전용 페이지
* 작성자     :  dn
* 생성일     :  2012.05.24
**********************************************************/
include "../../lib/sAPI.class.php";

### 마켓 상품 여부 확인
$market_order_query = $db->_query_print('SELECT * FROM '.GD_MARKET_ORDER.' WHERE order_no=[s]', $_GET['ordno']);
$res_market_order = $db->_select($market_order_query);
$row_market_order = $res_market_order[0];

$sAPI = new sAPI();
$code_arr['grp_cd'] = 'order_status';
$tmp_order_status = $sAPI->getcode($code_arr, 'hash');
unset($code_arr);

$domain_query = $db->_query_print('SELECT value FROM gd_env WHERE category=[s] AND name=[s]', 'selly', 'domain');
$res_domain = $db->_select($domain_query);
$domain = $res_domain[0]['value'];

$mall_list_data['perpage'] = '10';
$mall_list_data['pagenum'] = '1';
$arr_mall_list = $sAPI->getMallList($mall_list_data, 'hash');

if(is_array($arr_mall_list) && !empty($arr_mall_list)) {
	$total_count = $arr_mall_list[0]['totalcount'];
	if($mall_list_data['perpage'] < $total_count) {
		$mall_list_data['perpage'] = $total_count;
		$arr_mall_list = $sAPI->getMallList($mall_list_data, 'hash');
	}

	foreach($arr_mall_list as $mall_data) {
		if(($mall_data['mall_cd'] == $row_market_order['mall_cd']) && ($mall_data['mall_login_id'] == $row_market_order['mall_login_id'])) {
			$mall_info_idx = $mall_data['minfo_idx'];
			break;
		}
	}

	$cust_seq_query = $db->_query_print('SELECT * FROM gd_env WHERE category=[s] AND name=[s]', 'selly', 'cust_seq');
	$cust_seq_res = $db->_select($cust_seq_query);
	$cust_seq = $cust_seq_res[0]['value'];

	$cust_cd_query = $db->_query_print('SELECT * FROM gd_env WHERE category=[s] AND name=[s]', 'selly', 'cust_cd');
	$cust_cd_seq = $db->_select($cust_cd_query);
	$cust_cd = $cust_cd_seq[0]['value'];

	$seq = base64_encode($sAPI->xcryptare($cust_seq, $cust_cd, true));
}

if(is_array($tmp_order_status) && !empty($tmp_order_status)) {
	foreach($tmp_order_status as $key_order_status => $val_order_status) {
		$order_status[$key_order_status]['code'] = $key_order_status;
		$order_status[$key_order_status]['code_nm'] = $val_order_status;
		
		switch($key_order_status) {
			case '0010' :
				$order_status[$key_order_status]['c_date'] = 'order_date';
				break;
			case '0020' :
				$order_status[$key_order_status]['c_date'] = 'check_date';
				break;
			case '0030' :
				$order_status[$key_order_status]['c_date'] = 'delivery_date';
				break;
			case '0040' :
				$order_status[$key_order_status]['c_date'] = 'delivery_end_date';
				break;
			case '0050' :
				$order_status[$key_order_status]['c_date'] = 'buy_confirm_date';
				break;
			case '0060' :
				$order_status[$key_order_status]['c_date'] = 'adjust_date';
				break;
			case '0021' :
				$order_status[$key_order_status]['c_date'] = 'cancel_date';
				break;
			case '0022' :
				$order_status[$key_order_status]['c_date'] = 'cancel_confirm_date';
				break;
			case '0031' :
				$order_status[$key_order_status]['c_date'] = 'return_date';
				break;
			case '0032' :
				$order_status[$key_order_status]['c_date'] = 'return_confirm_date';
				break;
			case '0041' :
				$order_status[$key_order_status]['c_date'] = 'exchange_date';
				break;
			case '0042' :
				$order_status[$key_order_status]['c_date'] = 'exchange_return_date';
				break;
			case '0043' :
				$order_status[$key_order_status]['c_date'] = 'exchange_delivery_date';
				break;
			case '0044' :
				$order_status[$key_order_status]['c_date'] = 'exchange_confirm_date';
				break;

		}
	}
}

$code_arr['grp_cd'] = 'mall_cd';
$mall_cd = $sAPI->getCode($code_arr, 'hash');
unset($code_arr);

$code_arr['grp_cd'] = 'delivery_type_order';
$delivery_type_order = $sAPI->getCode($code_arr, 'hash');
unset($code_arr);

$code_arr['grp_cd'] = 'delivery_st';
$delivery_st = $sAPI->getCode($code_arr, 'hash');
unset($code_arr);

$code_arr['grp_cd'] = 'MALL_GOODS_URL';
$mall_goods_url = $sAPI->getCode($code_arr, 'hash');
unset($code_arr);

### 배송업체 정보
$delivery_query = $db->_query_print('SELECT * FROM '.GD_LIST_DELIVERY.' WHERE useyn=[s] ORDER BY deliverycomp', 'y');
$res_delivery = $db->_select($delivery_query);

foreach($order_status as $row_order_status){
	$nowsts[$row_order_status['code']] = ($row_order_status['code'] == $row_market_order['status'])? 'on_sts' : 'sts';
}

$selected['delivery_cd'][$row_market_order['delivery_cd']] = 'selected';
$selected['exchange_delivery_cd'][$row_market_order['exchange_delivery_cd']] = 'selected';

// 주문상품정보
$market_order_item_query = $db->_query_print('SELECT * FROM '.GD_MARKET_ORDER_ITEM.' WHERE order_no=[s]', $_GET['ordno']);
$res_market_order_item = $db->_select($market_order_item_query);

$tmp_mall_set = $sAPI->getSetList();
foreach($tmp_mall_set as $mall_data) {
	if($mall_data['mall_cd'] == $row_market_order['mall_cd'] && $mall_data['mall_login_id'] == $row_market_order['mall_login_id']) {
		$arr_mall_data = $mall_data;
	}
}

?>

<style type="text/css">
table.order_status { width:100%; height:100%; font-family:verdana,dotum,돋움,gulim,굴림; }
table.order_status td { text-align:center;   padding:0px; word-break:keep-all; word-wrap:normal; letter-spacing:-2px; }
table.order_status .sts { border:solid 1px #3F82AD; cursor:pointer;  color:#122438; height:40px;}
table.order_status .on_sts { border:solid 1px #33383B; cursor:pointer;  color:#FFFFFF; background-color:#4689BE;/*061220*/ height:40px; }
table.order_status .sts .num { font-size:12px !important; color:#015BE5; letter-spacing:-1px; font-weight:bold; }
table.order_status .on_sts .num { font-size:12px !important; color:#FFF005; letter-spacing:-1px; font-weight:bold; }
table.order_status .arrow { padding:8px; }
</style>
<script type="text/javascript">
function statusChange(send_status) {
	
	var pre_status = $('pre_status').value;
	var bool_chk = false;

	switch (send_status) {
		case '0020' :
			if(pre_status == '0010') bool_chk = true;
			break;
		case '0030' :
			if(pre_status == '0020') bool_chk = true;
			break;
		case '0022' :
			if(pre_status == '0021') bool_chk = true;
			break;
		case '0032' :
			if(pre_status == '0031') bool_chk = true;
			break;
		case '0042' :
			if(pre_status == '0041') bool_chk = true;
			break;
		case '0043' :
			if(pre_status == '0042') bool_chk = true;
			break;	
	}

	if(bool_chk) {
		frm = document.frmMorder;
		$('send_status').value = send_status;

		frm.submit();

	}
	else {
		alert('선택하신 주문상태로 처리 할 수 없습니다');
	}
}

function setDeliveryInfo() {
	
	var delivery_cd = $('delivery_cd').value;
	var delivery_no = $('delivery_no').value;

	if(!delivery_cd || !delivery_no) {
		alert('배송정보를 입력해주세요');
		return;
	}
	
	frm = document.frmMorder;
	$('mode').value = 'setdeliveryinfo';
	frm.submit();

}

function setExchangeDeliveryInfo() {
	
	var exchange_delivery_cd = $('exchange_delivery_cd').value;
	var exchange_delivery_no = $('exchange_delivery_no').value;

	if(!exchange_delivery_cd || !exchange_delivery_no) {
		alert('교환배송정보를 입력해주세요');
		return;
	}
	
	frm = document.frmMorder;
	$('mode').value = 'setexchangedeliveryinfo';
	frm.submit();

}

function scm_login(minfo_idx) {
	if(minfo_idx == null || minfo_idx == '') {
		alert('SCM로그인 기능이 지원되지 않는 마켓 입니다.');
		return;
	}

	document.getElementsByName('minfo_idx')[0].value = minfo_idx;

	var fm = document.mallInfo;
	fm.target = "_blank";
	fm.method = "POST";
	fm.action = "http://<?=$domain?>/basic/STMallLoginTestShop.gm";
	fm.submit();
}
</script>

<form name="mallInfo" method="POST">
	<input type="hidden" name="seq" value="<?=$seq?>">
	<input type="hidden" name="minfo_idx" />
</form>

<form name="frmMorder" action="../selly/indb.php" method="post" >
<input type="hidden" id="mode" name="mode" value="sendorder" />
<input type="hidden" name="order_idx" value="<?=$row_market_order['order_idx']?>" />
<input type="hidden" id="pre_status" name="pre_status" value="<?=$row_market_order['status']?>"/>
<input type="hidden" id="send_status" name="send_status" />
<input type="hidden" id="ret_url" name="ret_url" value="<?=$_SERVER['PHP_SELF']?>?ordno=<?=$_GET['ordno']?>" />
<table class="tb" cellpadding="4" cellspacing="0">
<tr height="25" bgcolor="#2E2B29" class="small4" style="padding-top:8px">
	<th><font color="white">번호</font></th>
	<th><font color="white">상품명</font></th>
	<th><font color="white">옵션</font></th>
	<th><font color="white">수량</font></th>
	<th><font color="white">상품가격</font></th>
</tr>
<col align=center>
<col>
<col align=center span=4>
<? if(is_array($res_market_order_item) && !empty($res_market_order_item)) { ?>
<?
$item_no = 0;
foreach($res_market_order_item as $row_order_item) {
	$item_no++;

	if($row_market_order['mall_cd'] == 'mall0007') {
		if($arr_mall_data['etc4'] != '') $goods_url = $arr_mall_data['etc4'].'/products/'.$row_market_order['mall_goods_cd'];
		else $goods_url = str_replace('{mall_login_id}', $arr_mall_data['mall_login_id'], str_replace('{mall_goods_cd}', $row_market_order['mall_goods_cd'], $mall_goods_url[$row_market_order['mall_cd']]));
	}
	else $goods_url = str_replace('{mall_goods_cd}', $row_market_order['mall_goods_cd'], $mall_goods_url[$row_market_order['mall_cd']]);
	?>
<tr>
	<td width=70 nowrap><?=$item_no?></td>
	<td width=100%><a href="<?=$goods_url?>" target="_blank"><?=htmlspecialchars($row_order_item['goodsnm'])?></a></td>
	<td width=200 nowrap><?=htmlspecialchars($row_order_item['goodsopt'])?></td>
	<td width=80 nowrap><?=number_format($row_order_item['ea'])?></td>
	<td width=150 nowrap><?=number_format($row_order_item['price'])?>원</td>
</tr>
<? }

}?>
</table>
<div style="height:20px;"></div>
<div class="title2" style="margin:0px 0px 5px 0px">&nbsp;<img src="../img/icon_process.gif" align="absmiddle"><font color="508900"><b>주문 정보</b></font></div>
<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td>마켓</td>
	<td><?=$mall_cd[$row_market_order['mall_cd']]?></td>
</tr>
<tr>
	<td>마켓주문번호</td>
	<td><?=$row_market_order['mall_order_no']?></td>
</tr>
<tr>
	<td>마켓상품코드</td>
	<td><?=$row_market_order['mall_goods_cd']?></td>
</tr>
<tr>
	<td>마켓주문상태처리</td>
	<td >
		<table class="order_status" >
			<colgroup><col width="15%" /><col width="2%" /><col width="15%" /><col width="2%" /><col width="15%" /><col width="2%" /><col width="15%" /><col width="2%" /><col width="15%" /><col width="2%" /><col width="15%" /></colgroup>
			<tbody>
				<tr>
					<td class="<?= $nowsts['0010']?>" id="<?=$order_status['0010']['code']?>" onClick="javascript:statusChange('<?=$order_status['0010']['code']?>');"><?=$order_status['0010']['code_nm']?><? if($row_market_order[$order_status['0010']['c_date']]){?><br /><?=(substr($row_market_order[$order_status['0010']['c_date']], 0, 10) == '0000-00-00' ? '' : substr($row_market_order[$order_status['0010']['c_date']], 0, 10)) ?><?}?></td>
					<td class="arrow"><img src="../img/blt_order_arrow_right.png" alt="▶" align="absmiddle" /></td>
					<td class="<?= $nowsts['0020']?>" id="<?=$order_status['0020']['code']?>" onClick="javascript:statusChange('<?=$order_status['0020']['code']?>');"><?=$order_status['0020']['code_nm']?><? if($row_market_order[$order_status['0020']['c_date']]){?><br /><?=(substr($row_market_order[$order_status['0020']['c_date']], 0, 10) == '0000-00-00' ? '' : substr($row_market_order[$order_status['0020']['c_date']], 0, 10)) ?><?}?></td>
					<td class="arrow"><img src="../img/blt_order_arrow_right.png" alt="▶" align="absmiddle" /></td>
					<td class="<?= $nowsts['0030']?>" id="<?=$order_status['0030']['code']?>" onClick="javascript:statusChange('<?=$order_status['0030']['code']?>');"><?=$order_status['0030']['code_nm']?><? if($row_market_order[$order_status['0030']['c_date']]){?><br /><?=(substr($row_market_order[$order_status['0030']['c_date']], 0, 10) == '0000-00-00' ? '' : substr($row_market_order[$order_status['0030']['c_date']], 0, 10)) ?><?}?></td>
					<td class="arrow"><img src="../img/blt_order_arrow_right.png" alt="▶" align="absmiddle" /></td>
					<td class="<?= $nowsts['0040']?>" id="<?=$order_status['0040']['code']?>" onClick="javascript:statusChange('<?=$order_status['0040']['code']?>');"><?=$order_status['0040']['code_nm']?><? if($row_market_order[$order_status['0040']['c_date']]){?><br /><?=(substr($row_market_order[$order_status['0040']['c_date']], 0, 10) == '0000-00-00' ? '' : substr($row_market_order[$order_status['0040']['c_date']], 0, 10)) ?><?}?></td>
					<td class="arrow"><img src="../img/blt_order_arrow_right.png" alt="▶" align="absmiddle" /></td>
					<td class="<?= $nowsts['0050']?>" id="<?=$order_status['0050']['code']?>" onClick="javascript:statusChange('<?=$order_status['0050']['code']?>');"><?=$order_status['0050']['code_nm']?><? if($row_market_order[$order_status['0050']['c_date']]){?><br /><?=(substr($row_market_order[$order_status['0050']['c_date']], 0, 10) == '0000-00-00' ? '' : substr($row_market_order[$order_status['0050']['c_date']], 0, 10)) ?><?}?></td>
					<td class="arrow"><img src="../img/blt_order_arrow_right.png" alt="▶" align="absmiddle" /></td>
					<td class="<?= $nowsts['0060']?>" id="<?=$order_status['0060']['code']?>" onClick="javascript:statusChange('<?=$order_status['0060']['code']?>');"><?=$order_status['0060']['code_nm']?><? if($row_market_order[$order_status['0060']['c_date']]){?><br /><?=(substr($row_market_order[$order_status['0060']['c_date']], 0, 10) == '0000-00-00' ? '' : substr($row_market_order[$order_status['0060']['c_date']], 0, 10)) ?><?}?></td>
				</tr>
				<tr>
					<td class="arrow"><img src="../img/blt_order_arrow_down.png" alt="▼" align="absmiddle" /></td>
					<td></td>
					<td></td>
					<td></td>
					<td class="arrow"><img src="../img/blt_order_arrow_down.png" alt="▼" align="absmiddle" /></td>
					<td></td>
					<td class="arrow"><img src="../img/blt_order_arrow_down.png" alt="▼" align="absmiddle" /></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td class="<?= $nowsts['0021']?>" id="<?=$order_status['0021']['code']?>" onClick="javascript:statusChange('<?=$order_status['0021']['code']?>');"><?=$order_status['0021']['code_nm']?><? if($row_market_order[$order_status['0021']['c_date']]){?><br /><?=(substr($row_market_order[$order_status['0021']['c_date']], 0, 10) == '0000-00-00' ? '' : substr($row_market_order[$order_status['0021']['c_date']], 0, 10)) ?><?}?></td>
					<td></td>
					<td></td>
					<td></td>
					<td class="<?= $nowsts['0031']?>" id="<?=$order_status['0031']['code']?>" onClick="javascript:statusChange('<?=$order_status['0031']['code']?>');"><?=$order_status['0031']['code_nm']?><? if($row_market_order[$order_status['0031']['c_date']]){?><br /><?=(substr($row_market_order[$order_status['0031']['c_date']], 0, 10) == '0000-00-00' ? '' : substr($row_market_order[$order_status['0031']['c_date']], 0, 10)) ?><?}?></td>
					<td></td>
					<td class="<?= $nowsts['0041']?>" id="<?=$order_status['0041']['code']?>" onClick="javascript:statusChange('<?=$order_status['0041']['code']?>');"><?=$order_status['0041']['code_nm']?><? if($row_market_order[$order_status['0041']['c_date']]){?><br /><?=(substr($row_market_order[$order_status['0041']['c_date']], 0, 10) == '0000-00-00' ? '' : substr($row_market_order[$order_status['0041']['c_date']], 0, 10)) ?><?}?></td>
					<td></td>
					<td></td>
					<td></td>
					<td class="arrow"><img src="../img/blt_order_arrow_up.png" alt="▲" align="absmiddle" /></td>
				</tr>
				<tr>
					<td class="arrow"><img src="../img/blt_order_arrow_down.png" alt="▼" align="absmiddle" /></td>
					<td></td>
					<td></td>
					<td></td>
					<td class="arrow"><img src="../img/blt_order_arrow_down.png" alt="▼" align="absmiddle" /></td>
					<td></td>
					<td class="arrow"><img src="../img/blt_order_arrow_down.png" alt="▼" align="absmiddle" /></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td class="<?= $nowsts['0022']?>" id="<?=$order_status['0022']['code']?>" onClick="javascript:statusChange('<?=$order_status['0022']['code']?>');"><?=$order_status['0022']['code_nm']?><? if($row_market_order[$order_status['0022']['c_date']]){?><br /><?=(substr($row_market_order[$order_status['0022']['c_date']], 0, 10) == '0000-00-00' ? '' : substr($row_market_order[$order_status['0022']['c_date']], 0, 10)) ?><?}?></td>
					<td></td>
					<td class="arrow"><img src="../img/blt_order_arrow_left.png" alt="◀" align="absmiddle" /></td>
					<td></td>
					<td class="<?= $nowsts['0032']?>" id="<?=$order_status['0032']['code']?>" onClick="javascript:statusChange('<?=$order_status['0032']['code']?>');"><?=$order_status['0032']['code_nm']?><? if($row_market_order[$order_status['0032']['c_date']]){?><br /><?=(substr($row_market_order[$order_status['0032']['c_date']], 0, 10) == '0000-00-00' ? '' : substr($row_market_order[$order_status['0032']['c_date']], 0, 10)) ?><?}?></td>
					<td></td>
					<td class="<?= $nowsts['0042']?>" id="<?=$order_status['0042']['code']?>" onClick="javascript:statusChange('<?=$order_status['0042']['code']?>');"><?=$order_status['0042']['code_nm']?><? if($row_market_order[$order_status['0042']['c_date']]){?><br /><?=(substr($row_market_order[$order_status['0042']['c_date']], 0, 10) == '0000-00-00' ? '' : substr($row_market_order[$order_status['0042']['c_date']], 0, 10)) ?><?}?></td>
					<td class="arrow"><img src="../img/blt_order_arrow_right.png" alt="▶" align="absmiddle" /></td>
					<td class="<?= $nowsts['0043']?>" id="<?=$order_status['0043']['code']?>" onClick="javascript:statusChange('<?=$order_status['0043']['code']?>');"><?=$order_status['0043']['code_nm']?><? if($row_market_order[$order_status['0043']['c_date']]){?><br /><?=(substr($row_market_order[$order_status['0043']['c_date']], 0, 10) == '0000-00-00' ? '' : substr($row_market_order[$order_status['0043']['c_date']], 0, 10)) ?><?}?></td>
					<td class="arrow"><img src="../img/blt_order_arrow_right.png" alt="▶" align="absmiddle" /></td>
					<td class="<?= $nowsts['0044']?>" id="<?=$order_status['0044']['code']?>" onClick="javascript:statusChange('<?=$order_status['0044']['code']?>');"><?=$order_status['0044']['code_nm']?><? if($row_market_order[$order_status['0044']['c_date']]){?><br /><?=(substr($row_market_order[$order_status['0044']['c_date']], 0, 10) == '0000-00-00' ? '' : substr($row_market_order[$order_status['0044']['c_date']], 0, 10)) ?><?}?></td>
				</tr>
			</tbody>
		</table>
	</td>
</tr>
<? //if($row_market_order['status'] == '0020') { ?>
<tr>
	<td>송장입력</td>
	<td>
		<div id="exchange_delivery_info">
			송장입력
			<select id="delivery_cd" name="delivery_cd">
				<option value="">==택배사 선택==</option>
				<? if(is_array($res_delivery) && !empty($res_delivery)) { 
					foreach($res_delivery as $row_delivery) { ?>
				<option value="<?=$row_delivery['deliveryno']?>" <?=$selected['delivery_cd'][$row_delivery['deliveryno']]?>><?=$row_delivery['deliverycomp']?></option>
				<?	}
				}?>
			</select>
			<input type="text" id="delivery_no" name="delivery_no" value="<?=$row_market_order['delivery_no']?>"/>
			<a href="javascript:setDeliveryInfo();"><img src="../img/btn_popuporder_print.gif" alt="송장입력" align="absmiddle" /></a>
		</div>
	</td>
</tr>
<? //} ?>
<? //if($row_market_order['status'] == '0042') { ?>
<tr>
	<td>교환송장입력</td>
	<td>
		<div id="exchange_delivery_info">
			교환송장입력
			<select id="exchange_delivery_cd" name="exchange_delivery_cd">
				<option value="">==택배사 선택==</option>
				<? if(is_array($res_delivery) && !empty($res_delivery)) { 
					foreach($res_delivery as $row_delivery) { ?>
				<option value="<?=$row_delivery['deliveryno']?>" <?=$selected['exchange_delivery_cd'][$row_delivery['deliveryno']]?>><?=$row_delivery['deliverycomp']?></option>
				<?	}
				}?>
			</select>
			<input type="text" id="exchange_delivery_no" name="exchange_delivery_no" value="<?=$row_market_order['exchange_delivery_no']?>"/>
			<a href="javascript:setExchangeDeliveryInfo();"><img src="../img/btn_popuporder_printinput.gif" alt="교환송장입력" align="absmiddle" /></a>
		</div>
	</td>
</tr>
<? //} ?>
</table>
<div style="height:20px;"></div>
<div class="title2" style="margin:0px 0px 5px 0px">&nbsp;<img src="../img/icon_process.gif" align="absmiddle"><font color="508900"><b>결제정보</b></font></div>
<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td>주문 금액</td>
	<td><?=number_format($row_market_order['order_price'])?>원</td>
</tr>
<tr>
	<td>배송비</td>
	<td><?=number_format($row_market_order['settle_delivery_price'])?>원</td>
</tr>
<tr>
	<td>결제 금액</td>
	<td><?=number_format($row_market_order['settle_price'])?>원</td>
</tr>
<tr>
	<td>결제 방법</td>
	<td>-</td>
</tr>
<tr>
	<td>결제 일시</td>
	<td><?=$row_market_order['reg_date']?></td>
</tr>

<tr>
	<td>배송비 종류</td>
	<td><?=$delivery_type_order[$row_market_order['delivery_type_order']]?></td>
</tr>
</table>
<div style="height:20px;"></div>
<table border="0" width="100%">
<tr>
<td width="50%"  valign="top">
	<div class="title2" style="margin:0px 0px 5px 0px">&nbsp;<img src="../img/icon_process.gif" align="absmiddle"><font color="508900"><b>주문자정보</b></font>&nbsp;&nbsp;&nbsp;<a href="javascript:scm_login('<?=$mall_info_idx?>');"><img src="../img/btn_scmlogin.gif" alt="SCM로그인" align="absmiddle" /></a></div>
	<table class="tb">
	<col class="cellC"><col class="cellL">
	<tr>
		<td>이름</td>
		<td><?=htmlspecialchars($row_market_order['order_nm'])?></td>
	</tr>
	<tr>
		<td>아이디(마켓)</td>
		<td><?=htmlspecialchars($row_market_order['order_id'])?></td>
	</tr>
	<tr>
		<td>연락처1</td>
		<td><?=htmlspecialchars($row_market_order['order_tel'])?></td>
	</tr>
	<tr>
		<td>연락처2</td>
		<td><?=htmlspecialchars($row_market_order['order_cel'])?></td>
	</tr>
	</table>
</td>
<td width="50%" valign="top">
	<div class="title2" style="margin:0px 0px 5px 0px">&nbsp;<img src="../img/icon_process.gif" align="absmiddle"><font color="508900"><b>수령자 정보</b></font></div>
	<table class="tb">
	<col class="cellC"><col class="cellL">
	<tr>
		<td>이름</td>
		<td><?=htmlspecialchars($row_market_order['receive_nm'])?></td>
	</tr>
	<tr>
		<td>배송지 주소</td>
		<td>(<?=$row_market_order['receive_zip']?>) <br>
		<?=$row_market_order['receive_addr']?> </td>
	</tr>
	<tr>
		<td>연락처1</td>
		<td><?=htmlspecialchars($row_market_order['receive_tel'])?></td>
	</tr>
	<tr>
		<td>연락처2</td>
		<td><?=htmlspecialchars($row_market_order['receive_cel'])?></td>
	</tr>
	<tr>
		<td>배송 메세지</td>
		<td><?=htmlspecialchars($row_market_order['delivery_msg'])?></td>
	</tr>
	</table>
</td>
</table>
<div style="height:20px;"></div>
<table border="0" width="100%">
<tr>
<td width="50%"  valign="top">
	<div class="title2" style="margin:0px 0px 5px 0px">&nbsp;<img src="../img/icon_process.gif" align="absmiddle"><font color="508900"><b>배송정보</b></font></div>
	<table class="tb">
	<col class="cellC"><col class="cellL">
	<tr>
		<td>배송 일시</td>
		<td>
			<? if($row_market_order['delivery_date'] != '0000-00-00 00:00:00') { ?>
				<?=$row_market_order['delivery_date']?>
			<? } ?>
		</td>
	</tr>
	<tr>
		<td>배송 방법</td>
		<td><?=$delivery_st[$row_market_order['delivery_st']]?></td>
	</tr>
	<tr>
		<td>배송사</td>
		<td>
		<? foreach($res_delivery as $row_delivery) { ?>
			<? if($row_delivery['deliveryno'] == $row_market_order['delivery_cd']) { ?>
				<?=$row_delivery['deliverycomp']?>
			<? } ?>
		<? } ?>
		<?=$integrate_cfg['dlv_company']['shople'][$row_market_order['delivery_cd']]?></td>
	</tr>
	<tr>
		<td>송장 번호</td>
		<td><?=htmlspecialchars($row_market_order['delivery_no'])?></td>
	</tr>
	<tr>
		<td>배송 완료 일시</td>
		<td>
			<? if($row_market_order['delivery_end_date'] != '0000-00-00 00:00:00') { ?>
				<?=$row_market_order['delivery_end_date']?>
			<? } ?>
		</td>
	</tr>
	</table>
</td>
<td width="50%" valign="top">
	<div class="title2" style="margin:0px 0px 5px 0px">&nbsp;<img src="../img/icon_process.gif" align="absmiddle"><font color="508900"><b>취소/반품/교환 정보</b></font></div>
	<?
	$cs_type = null;
	switch ($row_market_order['status']) {	// x 번대
		case '0021' :
		case '0022' :
			$cs_type = '취소';
			$req_date = 'cancel_date';
			$confirm_date = 'cancel_confirm_date';
			break;
		case '0031' :
		case '0032' :
			$cs_type = '반품';
			$req_date = 'return_date';
			$confirm_date = 'return_confirm_date';
			break;
		case '0041' :
		case '0042' :
		case '0043' :
		case '0044' :
			$req_date = 'exchange_date';
			$confirm_date = 'exchange_confirm_date';
			$cs_type = '교환';
			break;
	}
	?>
	<? if ($cs_type !== null) { ?>
	<table class="tb">
	<col class="cellC"><col class="cellL">
	<tr>
		<td><?=$cs_type?> 신청 일시</td>
		<td><?=($row_market_order[$req_date])?></td>
	</tr>
	<tr>
		<td><?=$cs_type?> 처리 일시</td>
		<td><?=($row_market_order[$confirm_date])?></td>
	</tr>
	<tr>
		<td><?=$cs_type?> 사유</td>
		<td><?=($row_market_order['return_msg'])?></td>
	</tr>
	</table>
	<? } ?>
</td>
</table>


</form>