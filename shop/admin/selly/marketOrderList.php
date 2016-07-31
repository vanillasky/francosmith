<?
/*********************************************************
* ���ϸ�     :  marketOrderList.php
* ���α׷��� :  �����ֹ�����
* �ۼ���     :  dn
* ������     :  2012.05.21
**********************************************************/
$location = "���� > �����ֹ�����";
include "../_header.php";
include "../../conf/config.pay.php";
include "../../lib/sAPI.class.php";
include "../../lib/page.class.php";

list($cust_seq) = $db->fetch("SELECT value FROM gd_env WHERE category = 'selly' AND name = 'cust_seq'");
list($cust_cd) = $db->fetch("SELECT value FROM gd_env WHERE category = 'selly' AND name = 'cust_cd'");

if(!$cust_seq || !$cust_seq) {
	msg("������ ��û�ϰ� ���� ���� ��� �Ŀ� ��밡���� �����Դϴ�.");
	go("./setting.php");
}

$sAPI = new sAPI();

$search = $_GET;
$page = (int)$_GET['page'] ? (int)$_GET['page'] : 1;
unset($_GET);

$code_arr['grp_cd'] = 'order_status';
$tmp_order_status = $sAPI->getcode($code_arr, 'hash');
unset($code_arr);
if(is_array($tmp_order_status) && !empty($tmp_order_status)) {
	foreach($tmp_order_status as $key_order_status => $val_order_status) {
		$order_status[$key_order_status]['code'] = $key_order_status;
		$order_status[$key_order_status]['code_nm'] = $val_order_status;
		
		if($key_order_status == '0020' || $key_order_status == '0030' ||$key_order_status == '0022' || $key_order_status == '0032' || $key_order_status == '0042' || $key_order_status == '0043' || $key_order_status == '0044') {
			$send_order_status[$key_order_status]['code'] = $key_order_status;
			$send_order_status[$key_order_status]['code_nm'] = $val_order_status;
		}
		
	}
}

if(is_array($order_status) && !empty($order_status)) {
	foreach($order_status as $row_order_status){
		$nowsts[$row_order_status['code']] = ($row_order_status['code'] == $search['status'])? 'on_sts' : 'sts';
	}
}

$code_arr['grp_cd'] = 'mall_cd';
$mall_cd = $sAPI->getcode($code_arr, 'hash');
unset($code_arr);

$code_arr['grp_cd'] = 'MALL_GOODS_URL';
$mall_goods_url = $sAPI->getCode($code_arr, 'hash');
unset($code_arr);

$tmp_mall_set = $sAPI->getSetList();
foreach($tmp_mall_set as $mall_data) {
	if(isset($arr_mall_data[$mall_data['mall_cd']][$mall_data['mall_login_id']])) continue;
	$arr_mall_data[$mall_data['mall_cd']][$mall_data['mall_login_id']] = $mall_data;
}

$search_date_arr = array();
$search_date_arr['om.reg_date'] = '�ֹ�������';
$search_date_arr['om.order_date'] = '�ֹ���';
$search_date_arr['om.check_date'] = '����Ȯ����';
$search_date_arr['om.delivery_date'] = '�����';
$search_date_arr['om.delivery_end_date'] = '��ۿϷ���';
$search_date_arr['om.adjust_date'] = '����Ϸ���';
$search_date_arr['om.cancel_date'] = '��ҿ�û��';
$search_date_arr['om.cancel_confirm_date'] = '��ҿϷ���';
$search_date_arr['om.return_date'] = '��ǰ��û��';
$search_date_arr['om.return_confirm_date'] = '��ǰ�Ϸ���';
$search_date_arr['om.exchange_date'] = '��ȯ��û��';
$search_date_arr['om.exchange_return_date'] = '��ȯ�԰���';
$search_date_arr['om.exchange_delivery_date'] = '��ȯ�����';
$search_date_arr['om.exchange_confirm_date'] = '��ȯ�Ϸ���';

$search_key_arr = array();
$search_key_arr['om.order_no'] = '�ֹ���ȣ';
$search_key_arr['om.mall_order_no'] = '�����ֹ���ȣ';
$search_key_arr['omi.goodsnm'] = '�ֹ���ǰ';
$search_key_arr['om.order_nm'] = '�ֹ���';
$search_key_arr['om.receive_nm'] = '������';

if($search['search_date'][0]) $search['search_date_start'] = $search['search_date'][0];
if($search['search_date'][1]) $search['search_date_end'] = $search['search_date'][1];

$arr_where = array();

if($search['search_date_start']) {
	if(!$search['search_date_end']) $search['search_date_end'] = date('Ymd');

	$tmp_start = date('Y-m-d 00:00:00', mktime(0, 0, 0, substr($search['search_date_start'],4,2), substr($search['search_date_start'],6,2), substr($search['search_date_start'],0,4)));
	$tmp_end = date('Y-m-d 23:59:59', mktime(0, 0, 0, substr($search['search_date_end'],4,2), substr($search['search_date_end'],6,2), substr($search['search_date_end'],0,4)));
	
	$arr_where[] = $db->_query_print($search['search_date_type']. ' >=[s] AND '.$search['search_date_type'].' <=[s]', $tmp_start, $tmp_end);
}

if($search['sword']) {
	$tmp_sword = '%'.$search['sword'].'%';
	$arr_where[] = $db->_query_print($search['search_key_type']. ' LIKE [s] ', $tmp_sword);
}

if(empty($search['mall_cd'])) {
	$search['mall_cd'][0] = 'all';
}

if($search['mall_cd'][0] == 'all') {
	$checked['mall_cd']['all'] = 'checked';
	if(is_array($mall_cd) && !empty($mall_cd)) {
		foreach($mall_cd as $key_mall_cd => $val_mall_cd) {
			$checked['mall_cd'][$key_mall_cd] = 'checked';
		}
	}
}
else {
	$tmp_mall_cd = array();
	foreach($search['mall_cd'] as $val_mall_cd) {
		$checked['mall_cd'][$val_mall_cd] = 'checked';
		$tmp_mall_cd[] = $val_mall_cd;
	}

	$arr_where[] = $db->_query_print('om.mall_cd IN [v]', $tmp_mall_cd);

}

if(empty($search['send_yn'])) {
	$search['send_yn'][0] = 'all';
}

if($search['send_yn'][0] == 'all') {
	$checked['send_yn']['all'] = 'checked';
	$checked['send_yn']['none'] = 'checked';
	$checked['send_yn']['N'] = 'checked';
	$checked['send_yn']['Y'] = 'checked';
}
else {
	$tmp_send_yn = array();
	foreach($search['send_yn'] as $val_send_yn) {
		$checked['send_yn'][$val_send_yn] = 'checked';
		if($val_send_yn == 'none') {
			$tmp_arr_where[] = $db->_query_print('om.send_yn IS NULL');
		}
		else {
			$tmp_send_yn[] = $val_send_yn;
		}		
	}
	
	if(!empty($tmp_send_yn)) $tmp_arr_where[] = $db->_query_print('om.send_yn IN [v]', $tmp_send_yn);
	
	$arr_where[] = '('.implode(' OR ', $tmp_arr_where).')';
}

## �ֹ� ���º� �ֹ� count ##
$cnt_where = implode(' AND ', $arr_where);
if(!$cnt_where) $cnt_where = '1=1';
$cnt_query = $db->_query_print('SELECT COUNT(om.morder_no) as status_cnt, om.status FROM '.GD_MARKET_ORDER.' om WHERE '.$cnt_where.' GROUP BY om.status');
$res_cnt = $db->_select($cnt_query);

$status_cnt = array();
if(!empty($res_cnt) && is_array($res_cnt)) {
	foreach($res_cnt as $row_cnt) {
		$status_cnt[$row_cnt['status']] = $row_cnt['status_cnt'];
	}
}

unset($cnt_where, $cnt_query, $res_cnt);

if($search['status']) $arr_where[] = $db->_query_print('om.status=[s]', $search['status']);

if(empty($arr_where)) $arr_where[] = '1=1';

$db_table = " ".GD_MARKET_ORDER." om";
$order_by = "-om.reg_date";

$pg = new Page($page, $cfg['orderPageNum']);
$pg->field = 'om.order_no, om.reg_date, om.order_idx, om.mall_cd, om.mall_login_id, om.mall_goods_cd, om.mall_order_no, om.mall_goods_nm, om.order_nm, om.receive_nm, om.settle_price, om.status';//�˻��ʵ�
$pg->setQuery($db_table,$arr_where,$order_by);
$pg->exec();
$res = $db->query($pg->query);

$selected['search_date_type'][$search['search_date_type']] = 'selected';
$selected['search_key_type'][$search['search_key_type']] = 'selected';

?>
<style type="text/css">
.orderstatus { }
.orderstatus-wrap {  }
.orderstatus-title { height:auto; background-color:#7808FF; overflow:hidden; background:url(../images/common/bg_widget_right.gif) right top no-repeat; border-bottom:solid 1px #D8D8D8; }
.orderstatus-title-wrap { display:inline-block; width:100%; height:26px; }
.orderstatus-title-more { cursor:pointer; float:right; margin-top:5px; margin-right:10px; }
.orderstatus-contents { border-left:solid 1px #D8D8D8; border-right:solid 1px #D8D8D8; border-bottom:solid 1px #D8D8D8; padding:5px; }

table.order_status { width:100%; height:100%; font-family:verdana,dotum,����,gulim,����; }
table.order_status td { text-align:center; line-height:20px; padding:0px; word-break:keep-all; word-wrap:normal; letter-spacing:-2px; }
table.order_status .sts { border:solid 1px #3F82AD; cursor:pointer; line-height:30px; color:#122438; }
table.order_status .on_sts { border:solid 1px #33383B; cursor:pointer; line-height:30px; color:#FFFFFF; background-color:#4689BE;/*061220*/ }
table.order_status .sts .num { font-size:12px !important; color:#015BE5; letter-spacing:-1px; font-weight:bold; }
table.order_status .on_sts .num { font-size:12px !important; color:#FFF005; letter-spacing:-1px; font-weight:bold; }
table.order_status .arrow { padding:8px; }
</style>
<script type="text/javascript" src="./js/selly.js"></script>
<script type="text/javascript">
/**
* ���λ��� Ȱ��ȭ
*/
function iciSelect(obj) {
	var row = obj.parentNode.parentNode;
	row.style.background = (obj.checked) ? "#F9FFA1" : "";
	if($('tr_'+obj.value)) {
		$('tr_'+obj.value).style.background=row.style.background;
	}
}

/**
* ��ü����
*/
var chkBoxAll_flag=true;
function chkBoxAll() {
	$$(".chk_ordno").each(function(item){
		if(item.disabled==true) return;
		item.checked=chkBoxAll_flag;
		iciSelect(item);
	});
	chkBoxAll_flag=!chkBoxAll_flag;
}

function statusSearch(ord_status) {
	frmSearch.status.value = ord_status;
	frmSearch.submit();
}

function setCmdSelect() {
	var search_status = $('status').value;

	var send_status = $('send_status');
	var send_status_opt = send_status.options;
	
	var opt_no = 1;
	var len_opt = send_status_opt.length;
	for(var i=0; i<len_opt; i++) {
		if(i == 0) continue;
		switch(search_status){
			case '0010' :
				if (send_status_opt[opt_no].value == '0020' || send_status_opt[opt_no].value == '0022') {
					opt_no ++;
					continue;
				}
				break;
			case '0020' :
				if (send_status_opt[opt_no].value == '0030' || send_status_opt[opt_no].value == '0022') {
					opt_no ++;
					continue;
				}
				break;
			case '0021' :
				if (send_status_opt[opt_no].value == '0022') {
					opt_no ++;
					continue;
				}
				break;
			case '0031' :
				if (send_status_opt[opt_no].value == '0032') {
					opt_no ++;
					continue;
				}
				break;
			case '0041' :
				if (send_status_opt[opt_no].value == '0042') {
					opt_no ++;
					continue;
				}
				break;
			case '0042' :
				if (send_status_opt[opt_no].value == '0043') {
					opt_no ++;
					continue;
				}
				break;
		}
		
		send_status_opt.remove(opt_no);

	}

}

var popup_no = 0;
function sendStatus() {
	var chk = document.getElementsByName("chk[]");
	var bool_chk = false;

	for(var i=0; i<chk.length; i++) {
		if(chk[i].checked == true) bool_chk = true;
	}

	if(!document.getElementById('send_status').options[document.getElementById('send_status').selectedIndex].value) {
		alert('ó�����¸� ���� ������ �ּ���');
		return;
	}

	if(bool_chk) {
		var frm = document.frmList;
		popup_return('_blank.php', 'send_pop'+popup_no, 800, 700, '', '', 1);

		frm.mode.value = 'send_status';
		frm.action = 'orderSendPop.php'

		frm.target = 'send_pop'+popup_no;
		frm.submit();

		frm.mode.value = '';
		frm.action = 'indb.php';
		frm.target = '';

		popup_no ++;
	}
	else {
		alert('ó���� �ֹ��� �����ϴ�.');
		return;
	}
}

function downOrderExcel() {
	location.href = "marketOrderExcel.php?<?=$_SERVER['QUERY_STRING']?>";
}

function setDeliveryInfo() {
	var frm = document.frmList;
	frm.action = "indb.php";
	frm.mode.value = "set_delivery_info";
	frm.submit();

	frm.action = '';
	frm.mode.value = '';

}
document.observe('dom:loaded', function() {
	setCmdSelect();
});
</script>

<div class="title title_top" style="position:relative;padding-bottom:15px">���� �ֹ�����<span>���Ͽ� ��ũ�� ��ǰ�� �ֹ��� ���� �Ͻ� �� �ֽ��ϴ�.</span><a href="javascript:manual('<?=$guideUrl?>board/view.php?id=selly&no=13')"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2"></a>
</div>
<form name="frmSearch" action="<?=$_SERVER['PHP_SELF']?>">
<input type="hidden" name="mode" value="<?=$search['mode']?>" />
<input type="hidden" name="status" id="status" value="<?=$search['status']?>" />
<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td>�����ֹ�����<br />
		<a href="javascript:statusSearch('');"><img src="../img/btn_allorder.gif" alt="��ü�ֹ�" align="absmiddle" /></a>
	</td>
	<td>
		<table class="order_status" >
			<colgroup><col width="15%" /><col width="2%" /><col width="15%" /><col width="2%" /><col width="15%" /><col width="2%" /><col width="15%" /><col width="2%" /><col width="15%" /><col width="2%" /><col width="15%" /></colgroup>
			<tbody>
				<tr>
					<td class="<?= $nowsts['0010']?>" id="<?=$order_status['0010']['code']?>" onClick="javascript:statusSearch('<?=$order_status['0010']['code']?>');"><?=$order_status['0010']['code_nm']?> (<? if($status_cnt['0010']){ ?> <?=$status_cnt['0010']?> <? } else { ?> 0 <? } ?>)</td>
					<td class="arrow"><img src="../img/blt_order_arrow_right.png" alt="��" align="absmiddle" /></td>
					<td class="<?= $nowsts['0020']?>" id="<?=$order_status['0020']['code']?>" onClick="javascript:statusSearch('<?=$order_status['0020']['code']?>');"><?=$order_status['0020']['code_nm']?> (<? if($status_cnt['0020']){ ?> <?=$status_cnt['0020']?> <? } else { ?> 0 <? } ?>)</td>
					<td class="arrow"><img src="../img/blt_order_arrow_right.png" alt="��" align="absmiddle" /></td>
					<td class="<?= $nowsts['0030']?>" id="<?=$order_status['0030']['code']?>" onClick="javascript:statusSearch('<?=$order_status['0030']['code']?>');"><?=$order_status['0030']['code_nm']?> (<? if($status_cnt['0030']){ ?> <?=$status_cnt['0030']?> <? } else { ?> 0 <? } ?>)</td>
					<td class="arrow"><img src="../img/blt_order_arrow_right.png" alt="��" align="absmiddle" /></td>
					<td class="<?= $nowsts['0040']?>" id="<?=$order_status['0040']['code']?>" onClick="javascript:statusSearch('<?=$order_status['0040']['code']?>');"><?=$order_status['0040']['code_nm']?> (<? if($status_cnt['0040']){ ?> <?=$status_cnt['0040']?> <? } else { ?> 0 <? } ?>)</td>
					<td class="arrow"><img src="../img/blt_order_arrow_right.png" alt="��" align="absmiddle" /></td>
					<td class="<?= $nowsts['0050']?>" id="<?=$order_status['0050']['code']?>" onClick="javascript:statusSearch('<?=$order_status['0050']['code']?>');"><?=$order_status['0050']['code_nm']?> (<? if($status_cnt['0050']){ ?> <?=$status_cnt['0050']?> <? } else { ?> 0 <? } ?>)</td>
					<td class="arrow"><img src="../img/blt_order_arrow_right.png" alt="��" align="absmiddle" /></td>
					<td class="<?= $nowsts['0060']?>" id="<?=$order_status['0060']['code']?>" onClick="javascript:statusSearch('<?=$order_status['0060']['code']?>');"><?=$order_status['0060']['code_nm']?> (<? if($status_cnt['0060']){ ?> <?=$status_cnt['0060']?> <? } else { ?> 0 <? } ?>)</td>
				</tr>
				<tr>
					<td class="arrow"><img src="../img/blt_order_arrow_down.png" alt="��" align="absmiddle" /></td>
					<td></td>
					<td></td>
					<td></td>
					<td class="arrow"><img src="../img/blt_order_arrow_down.png" alt="��" align="absmiddle" /></td>
					<td></td>
					<td class="arrow"><img src="../img/blt_order_arrow_down.png" alt="��" align="absmiddle" /></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td class="<?= $nowsts['0021']?>" id="<?=$order_status['0021']['code']?>" onClick="javascript:statusSearch('<?=$order_status['0021']['code']?>');"><?=$order_status['0021']['code_nm']?> (<? if($status_cnt['0021']){ ?> <?=$status_cnt['0021']?> <? } else { ?> 0 <? } ?>)</td>
					<td></td>
					<td></td>
					<td></td>
					<td class="<?= $nowsts['0031']?>" id="<?=$order_status['0031']['code']?>" onClick="javascript:statusSearch('<?=$order_status['0031']['code']?>');"><?=$order_status['0031']['code_nm']?> (<? if($status_cnt['0031']){ ?> <?=$status_cnt['0031']?> <? } else { ?> 0 <? } ?>)</td>
					<td></td>
					<td class="<?= $nowsts['0041']?>" id="<?=$order_status['0041']['code']?>" onClick="javascript:statusSearch('<?=$order_status['0041']['code']?>');"><?=$order_status['0041']['code_nm']?> (<? if($status_cnt['0041']){ ?> <?=$status_cnt['0041']?> <? } else { ?> 0 <? } ?>)</td>
					<td></td>
					<td></td>
					<td></td>
					<td class="arrow"><img src="../img/blt_order_arrow_up.png" alt="��" align="absmiddle" /></td>
				</tr>
				<tr>
					<td class="arrow"><img src="../img/blt_order_arrow_down.png" alt="��" align="absmiddle" /></td>
					<td></td>
					<td></td>
					<td></td>
					<td class="arrow"><img src="../img/blt_order_arrow_down.png" alt="��" align="absmiddle" /></td>
					<td></td>
					<td class="arrow"><img src="../img/blt_order_arrow_down.png" alt="��" align="absmiddle" /></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td class="<?= $nowsts['0022']?>" id="<?=$order_status['0022']['code']?>" onClick="javascript:statusSearch('<?=$order_status['0022']['code']?>');"><?=$order_status['0022']['code_nm']?> (<? if($status_cnt['0022']){ ?> <?=$status_cnt['0022']?> <? } else { ?> 0 <? } ?>)</td>
					<td></td>
					<td class="arrow"><img src="../img/blt_order_arrow_left.png" alt="��" align="absmiddle" /></td>
					<td></td>
					<td class="<?= $nowsts['0032']?>" id="<?=$order_status['0032']['code']?>" onClick="javascript:statusSearch('<?=$order_status['0032']['code']?>');"><?=$order_status['0032']['code_nm']?> (<? if($status_cnt['0032']){ ?> <?=$status_cnt['0032']?> <? } else { ?> 0 <? } ?>)</td>
					<td></td>
					<td class="<?= $nowsts['0042']?>" id="<?=$order_status['0042']['code']?>" onClick="javascript:statusSearch('<?=$order_status['0042']['code']?>');"><?=$order_status['0042']['code_nm']?> (<? if($status_cnt['0042']){ ?> <?=$status_cnt['0042']?> <? } else { ?> 0 <? } ?>)</td>
					<td class="arrow"><img src="../img/blt_order_arrow_right.png" alt="��" align="absmiddle" /></td>
					<td class="<?= $nowsts['0043']?>" id="<?=$order_status['0043']['code']?>" onClick="javascript:statusSearch('<?=$order_status['0043']['code']?>');"><?=$order_status['0043']['code_nm']?> (<? if($status_cnt['0043']){ ?> <?=$status_cnt['0043']?> <? } else { ?> 0 <? } ?>)</td>
					<td class="arrow"><img src="../img/blt_order_arrow_right.png" alt="��" align="absmiddle" /></td>
					<td class="<?= $nowsts['0044']?>" id="<?=$order_status['0044']['code']?>" onClick="javascript:statusSearch('<?=$order_status['0044']['code']?>');"><?=$order_status['0044']['code_nm']?> (<? if($status_cnt['0044']){ ?> <?=$status_cnt['0044']?> <? } else { ?> 0 <? } ?>)</td>
				</tr>
			</tbody>
		</table>
	</td>
</tr>
<tr>
	<td>ó����</td>
	<td>
		<select name="search_date_type" >
		<? foreach($search_date_arr as $key => $val) { ?>
			<option value="<?=$key?>" <?=$selected['search_date_type'][$key]?>><?=$val?></option>
		<? } ?>
		</select>
		<input type="text" name="search_date[]" value="<?=$search['search_date_start']?>" onclick="calendar(event)" size="12" class="line"/> - 
		<input type="text" name="search_date[]" value="<?=$search['search_date_end']?>" onclick="calendar(event)" size="12" class="line"/>
		<a href="javascript:setDate('search_date[]',<?=date("Ymd")?>,<?=date("Ymd")?>)"><img src="../img/sicon_today.gif" align="absmiddle"/></a>
		<a href="javascript:setDate('search_date[]',<?=date("Ymd",strtotime("-7 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_week.gif" align="absmiddle"/></a>
		<a href="javascript:setDate('search_date[]',<?=date("Ymd",strtotime("-15 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twoweek.gif" align="absmiddle"/></a>
		<a href="javascript:setDate('search_date[]',<?=date("Ymd",strtotime("-1 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_month.gif" align="absmiddle"/></a>
		<a href="javascript:setDate('search_date[]',<?=date("Ymd",strtotime("-2 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twomonth.gif" align="absmiddle"/></a>
		</td>
	</td>
</tr>
<tr>
	<td>����</td>
	<td class="noline">
		<label><input type="checkbox" name="mall_cd[]" value="all" <?=$checked['mall_cd']['all']?>>��ü</label>
		<? if(is_array($mall_cd) && !empty($mall_cd)) { ?>
		<? foreach($mall_cd as $key => $val) {
			if($key == 'mall0005') continue;
			?>
		<label><input type="checkbox" name="mall_cd[]" value="<?=$key?>" <?=$checked['mall_cd'][$key]?>><?=$val?></label>
		<? } ?>
		<? } ?>
	</td>
</tr>
<tr>
	<td>��������</td>
	<td class="noline">
		<label><input type="checkbox" name="send_yn[]" value="all" <?=$checked['send_yn']['all']?>>��ü</label>
		<label><input type="checkbox" name="send_yn[]" value="none" <?=$checked['send_yn']['none']?>>������Է�</label>
		<label><input type="checkbox" name="send_yn[]" value="N" <?=$checked['send_yn']['N']?>>�����Է�</label>
		<label><input type="checkbox" name="send_yn[]" value="Y" <?=$checked['send_yn']['Y']?>>��������</label>
	</td>
</tr>
<tr>
	<td>�ֹ��˻�</td>
	<td>
		<select name="search_key_type">

		<? foreach($search_key_arr as $key=>$val) { ?>
			<option value="<?=$key?>" <?=$selected['search_key_type'][$key]?>><?=$val?></option>
		<? } ?>
		</select>
		<input type="text" name="sword" value="<?=htmlspecialchars($search['sword'])?>" class="line" size="50" />
	</td>
</tr>
</table>
<div class="button_top">
<input type="image" src="../img/btn_search2.gif"/>
</div>
</form>
<div style="margin-top:15px"></div>
<form name="frmList" method="post" action="indb.php"  id="frmList" enctype="multipart/form-data">
<input type="hidden" name="mode" />
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<col width="25"><col width="30"><col width="100"><col width="160"><col><col width="95"><col width="50"><col width="50"><col>
<tr><td class="rnd" colspan="20"></td></tr>
<tr class="rndbg">
	<th><a href="javascript:void(0)" onClick="chkBoxAll()" class=white>����</a></th>
	<th>��ȣ</th>
	<th>�ֹ�������</th>
	<th>����(ID)</th>
	<th>�ֹ���ȣ (�ֹ���ǰ)</th>
	<th>�ֹ���</th>
	<th>�޴º�</th>
	<th>�ݾ�</th>
	<th>���ϻ���</th>
</tr>
<tr><td class="rnd" colspan="20"></td></tr>
<?
$ord_idx = ($page - 1) * $cfg['orderPageNum'];
while ($data=$db->fetch($res)) {
	$ord_idx ++;
?>

<tr height="25"align="center">
	<td class="noline"><input type="checkbox" name="chk[]" value="<?=$data['order_idx']?>" onclick="iciSelect(this)" <?=$disabled?> class="chk_ordno" /></td>
	<td><?=$ord_idx?></td>
	<td><?=$data['reg_date']?></td>
	<td><?=$mall_cd[$data['mall_cd']]?><br /><?=$data['mall_login_id']?></td>
	<td><a href="javascript:popup('./popup.order.selly.php?ordno=<?=$data['order_no']?>',800,600)"><?=$data['order_no']?>(<?=$data['mall_order_no']?>)</a><br />
	<?
		if($data['mall_cd'] == 'mall0007') {
			if($arr_mall_data[$data['mall_cd']][$data['mall_login_id']]['etc4'] != '') $goods_url = $arr_mall_data[$data['mall_cd']][$data['mall_login_id']]['etc4'].'/products/'.$data['mall_goods_cd'];
			else $goods_url = str_replace('{mall_login_id}', $data['mall_login_id'], str_replace('{mall_goods_cd}', $data['mall_goods_cd'], $mall_goods_url[$data['mall_cd']]));
		}
		else $goods_url = str_replace('{mall_goods_cd}', $data['mall_goods_cd'], $mall_goods_url[$data['mall_cd']]);
	?>
	<a href="<?=$goods_url?>" target="_blank"><?=$data['mall_goods_nm']?></a></td>
	<td><?=$data['order_nm']?></td>
	<td><?=$data['receive_nm']?></td>
	<td><?=number_format($data['settle_price'])?></td>
	<td><?=$order_status[$data['status']]['code_nm']?></td>
</tr>
<tr><td colspan="20" bgcolor="#E4E4E4"></td></tr>
<?}?>

</table>
<div style="margin-top:15px"></div>
<div align=center class=pageNavi><font class=ver8><?=$pg->page[navi]?></font></div>
<div style="margin-top:15px"></div>
<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td>���ϻ��� �ϰ�ó��</td>
	<td>�������ֹ�
		<select name="send_status" id="send_status">
			<option value="">== ó������ ���� ==</option>
			<? foreach($send_order_status as $row_send_order) { ?>
			<option value="<?=$row_send_order['code']?>" ><?=$row_send_order['code_nm']?></option>
			<? }?>
		</select>
		<a href="javascript:sendStatus();"><img src="../img/btn_maretorder.gif" alt="ó��" align="absmiddle" /></a>
	</td>
</tr>
<tr>
	<td>���� CSV ���� �ø���</td>
	<td><input type="file" name="file_excel" size="45" required label="CSV ����"> &nbsp;&nbsp; <a href="javascript:setDeliveryInfo();"><img src="../img/btn_regist_s.gif" align="absmiddle"></a> <a href="javascript:downOrderExcel();"><img src="../img/btn_marketorder_x.gif" alt="�˻��ֹ� ���� �ٿ�ε�" align="absmiddle"></a>
	</td>
</tr>
</table>
</form>

<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td height="5"></td></tr>
<tr><td>
<a href="../selly/orderScrap.php"><font color=white><u>[�ֹ�����]</u></font></a>���� ������ �ֹ��� ���¸� ó���Ͻ� �� �ֽ��ϴ�.<br/><br/><br/>

�����ֹ����¿��� ���º� �ؽ�Ʈ�� Ŭ���Ͻø� �ش� ������ �ֹ��� �˻��Ͻ� �� �ֽ��ϴ�.<br/>
�����ֹ����� �ϴܿ� �ִ� ��ü�ֹ� �������� Ŭ���Ͽ� �������� �ֹ��� �˻��Ͻ� �� �ֽ��ϴ�.<br/>
���ϻ��� �ϰ�ó���� �ֹ����º��� �˻��� �Ͻø� ó���� �� �ִ� �ֹ��� ���¸� �����Ͻ� �� �ֽ��ϴ�.<br/>
����Ʈ �ϴ��� ���� CSV���� �ø��⸦ ���� �����ȣ�� �ϰ��� ����Ͻ� �� �ֽ��ϴ�.<br/>
����Ʈ���� �ֹ���ȣ�� Ŭ���� �˾����� �ֹ��󼼳����� Ȯ���Ͻ� �� �ֽ��ϴ�.<br/><br/><br/>

�ֹ��󼼳��� �˾������� �ֹ�����, ��������, �ֹ������� �� ��ۿ� �ʿ��� ������ Ȯ���Ͻ� �� ������<br/>
�����ȣ/��ȯ�����ȣ�� �Է��Ͻ� �� �ֽ��ϴ�.<br/>
�ֹ��󼼳��� �˾��� �����ֹ�����ó������ ���� ������ �ؽ�Ʈ�ڽ��� Ŭ���Ͻø� �ֹ��� ���¸� ������ ���·� �����Ͻ� �� �ֽ��ϴ�.
</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

<? include "../_footer.php"; ?>


