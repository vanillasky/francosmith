<?
/*********************************************************
* 파일명     :  marketOrderList.php
* 프로그램명 :  마켓주문관리
* 작성자     :  dn
* 생성일     :  2012.05.21
**********************************************************/
$location = "셀리 > 마켓주문관리";
include "../_header.php";
include "../../conf/config.pay.php";
include "../../lib/sAPI.class.php";
include "../../lib/page.class.php";

list($cust_seq) = $db->fetch("SELECT value FROM gd_env WHERE category = 'selly' AND name = 'cust_seq'");
list($cust_cd) = $db->fetch("SELECT value FROM gd_env WHERE category = 'selly' AND name = 'cust_cd'");

if(!$cust_seq || !$cust_seq) {
	msg("셀리를 신청하고 상점 인증 등록 후에 사용가능한 서비스입니다.");
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
$search_date_arr['om.reg_date'] = '주문수집일';
$search_date_arr['om.order_date'] = '주문일';
$search_date_arr['om.check_date'] = '발주확인일';
$search_date_arr['om.delivery_date'] = '배송일';
$search_date_arr['om.delivery_end_date'] = '배송완료일';
$search_date_arr['om.adjust_date'] = '정산완료일';
$search_date_arr['om.cancel_date'] = '취소요청일';
$search_date_arr['om.cancel_confirm_date'] = '취소완료일';
$search_date_arr['om.return_date'] = '반품요청일';
$search_date_arr['om.return_confirm_date'] = '반품완료일';
$search_date_arr['om.exchange_date'] = '교환요청일';
$search_date_arr['om.exchange_return_date'] = '교환입고일';
$search_date_arr['om.exchange_delivery_date'] = '교환배송일';
$search_date_arr['om.exchange_confirm_date'] = '교환완료일';

$search_key_arr = array();
$search_key_arr['om.order_no'] = '주문번호';
$search_key_arr['om.mall_order_no'] = '마켓주문번호';
$search_key_arr['omi.goodsnm'] = '주문상품';
$search_key_arr['om.order_nm'] = '주문자';
$search_key_arr['om.receive_nm'] = '수취인';

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

## 주문 상태별 주문 count ##
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
$pg->field = 'om.order_no, om.reg_date, om.order_idx, om.mall_cd, om.mall_login_id, om.mall_goods_cd, om.mall_order_no, om.mall_goods_nm, om.order_nm, om.receive_nm, om.settle_price, om.status';//검색필드
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

table.order_status { width:100%; height:100%; font-family:verdana,dotum,돋움,gulim,굴림; }
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
* 라인색상 활성화
*/
function iciSelect(obj) {
	var row = obj.parentNode.parentNode;
	row.style.background = (obj.checked) ? "#F9FFA1" : "";
	if($('tr_'+obj.value)) {
		$('tr_'+obj.value).style.background=row.style.background;
	}
}

/**
* 전체선택
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
		alert('처리상태를 먼저 선택해 주세요');
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
		alert('처리할 주문이 없습니다.');
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

<div class="title title_top" style="position:relative;padding-bottom:15px">마켓 주문관리<span>마켓에 링크된 상품의 주문을 관리 하실 수 있습니다.</span><a href="javascript:manual('<?=$guideUrl?>board/view.php?id=selly&no=13')"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2"></a>
</div>
<form name="frmSearch" action="<?=$_SERVER['PHP_SELF']?>">
<input type="hidden" name="mode" value="<?=$search['mode']?>" />
<input type="hidden" name="status" id="status" value="<?=$search['status']?>" />
<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td>마켓주문상태<br />
		<a href="javascript:statusSearch('');"><img src="../img/btn_allorder.gif" alt="전체주문" align="absmiddle" /></a>
	</td>
	<td>
		<table class="order_status" >
			<colgroup><col width="15%" /><col width="2%" /><col width="15%" /><col width="2%" /><col width="15%" /><col width="2%" /><col width="15%" /><col width="2%" /><col width="15%" /><col width="2%" /><col width="15%" /></colgroup>
			<tbody>
				<tr>
					<td class="<?= $nowsts['0010']?>" id="<?=$order_status['0010']['code']?>" onClick="javascript:statusSearch('<?=$order_status['0010']['code']?>');"><?=$order_status['0010']['code_nm']?> (<? if($status_cnt['0010']){ ?> <?=$status_cnt['0010']?> <? } else { ?> 0 <? } ?>)</td>
					<td class="arrow"><img src="../img/blt_order_arrow_right.png" alt="▶" align="absmiddle" /></td>
					<td class="<?= $nowsts['0020']?>" id="<?=$order_status['0020']['code']?>" onClick="javascript:statusSearch('<?=$order_status['0020']['code']?>');"><?=$order_status['0020']['code_nm']?> (<? if($status_cnt['0020']){ ?> <?=$status_cnt['0020']?> <? } else { ?> 0 <? } ?>)</td>
					<td class="arrow"><img src="../img/blt_order_arrow_right.png" alt="▶" align="absmiddle" /></td>
					<td class="<?= $nowsts['0030']?>" id="<?=$order_status['0030']['code']?>" onClick="javascript:statusSearch('<?=$order_status['0030']['code']?>');"><?=$order_status['0030']['code_nm']?> (<? if($status_cnt['0030']){ ?> <?=$status_cnt['0030']?> <? } else { ?> 0 <? } ?>)</td>
					<td class="arrow"><img src="../img/blt_order_arrow_right.png" alt="▶" align="absmiddle" /></td>
					<td class="<?= $nowsts['0040']?>" id="<?=$order_status['0040']['code']?>" onClick="javascript:statusSearch('<?=$order_status['0040']['code']?>');"><?=$order_status['0040']['code_nm']?> (<? if($status_cnt['0040']){ ?> <?=$status_cnt['0040']?> <? } else { ?> 0 <? } ?>)</td>
					<td class="arrow"><img src="../img/blt_order_arrow_right.png" alt="▶" align="absmiddle" /></td>
					<td class="<?= $nowsts['0050']?>" id="<?=$order_status['0050']['code']?>" onClick="javascript:statusSearch('<?=$order_status['0050']['code']?>');"><?=$order_status['0050']['code_nm']?> (<? if($status_cnt['0050']){ ?> <?=$status_cnt['0050']?> <? } else { ?> 0 <? } ?>)</td>
					<td class="arrow"><img src="../img/blt_order_arrow_right.png" alt="▶" align="absmiddle" /></td>
					<td class="<?= $nowsts['0060']?>" id="<?=$order_status['0060']['code']?>" onClick="javascript:statusSearch('<?=$order_status['0060']['code']?>');"><?=$order_status['0060']['code_nm']?> (<? if($status_cnt['0060']){ ?> <?=$status_cnt['0060']?> <? } else { ?> 0 <? } ?>)</td>
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
					<td class="<?= $nowsts['0022']?>" id="<?=$order_status['0022']['code']?>" onClick="javascript:statusSearch('<?=$order_status['0022']['code']?>');"><?=$order_status['0022']['code_nm']?> (<? if($status_cnt['0022']){ ?> <?=$status_cnt['0022']?> <? } else { ?> 0 <? } ?>)</td>
					<td></td>
					<td class="arrow"><img src="../img/blt_order_arrow_left.png" alt="◀" align="absmiddle" /></td>
					<td></td>
					<td class="<?= $nowsts['0032']?>" id="<?=$order_status['0032']['code']?>" onClick="javascript:statusSearch('<?=$order_status['0032']['code']?>');"><?=$order_status['0032']['code_nm']?> (<? if($status_cnt['0032']){ ?> <?=$status_cnt['0032']?> <? } else { ?> 0 <? } ?>)</td>
					<td></td>
					<td class="<?= $nowsts['0042']?>" id="<?=$order_status['0042']['code']?>" onClick="javascript:statusSearch('<?=$order_status['0042']['code']?>');"><?=$order_status['0042']['code_nm']?> (<? if($status_cnt['0042']){ ?> <?=$status_cnt['0042']?> <? } else { ?> 0 <? } ?>)</td>
					<td class="arrow"><img src="../img/blt_order_arrow_right.png" alt="▶" align="absmiddle" /></td>
					<td class="<?= $nowsts['0043']?>" id="<?=$order_status['0043']['code']?>" onClick="javascript:statusSearch('<?=$order_status['0043']['code']?>');"><?=$order_status['0043']['code_nm']?> (<? if($status_cnt['0043']){ ?> <?=$status_cnt['0043']?> <? } else { ?> 0 <? } ?>)</td>
					<td class="arrow"><img src="../img/blt_order_arrow_right.png" alt="▶" align="absmiddle" /></td>
					<td class="<?= $nowsts['0044']?>" id="<?=$order_status['0044']['code']?>" onClick="javascript:statusSearch('<?=$order_status['0044']['code']?>');"><?=$order_status['0044']['code_nm']?> (<? if($status_cnt['0044']){ ?> <?=$status_cnt['0044']?> <? } else { ?> 0 <? } ?>)</td>
				</tr>
			</tbody>
		</table>
	</td>
</tr>
<tr>
	<td>처리일</td>
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
	<td>마켓</td>
	<td class="noline">
		<label><input type="checkbox" name="mall_cd[]" value="all" <?=$checked['mall_cd']['all']?>>전체</label>
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
	<td>송장정보</td>
	<td class="noline">
		<label><input type="checkbox" name="send_yn[]" value="all" <?=$checked['send_yn']['all']?>>전체</label>
		<label><input type="checkbox" name="send_yn[]" value="none" <?=$checked['send_yn']['none']?>>송장미입력</label>
		<label><input type="checkbox" name="send_yn[]" value="N" <?=$checked['send_yn']['N']?>>송장입력</label>
		<label><input type="checkbox" name="send_yn[]" value="Y" <?=$checked['send_yn']['Y']?>>송장전송</label>
	</td>
</tr>
<tr>
	<td>주문검색</td>
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
	<th><a href="javascript:void(0)" onClick="chkBoxAll()" class=white>선택</a></th>
	<th>번호</th>
	<th>주문수집일</th>
	<th>마켓(ID)</th>
	<th>주문번호 (주문상품)</th>
	<th>주문자</th>
	<th>받는분</th>
	<th>금액</th>
	<th>마켓상태</th>
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
	<td>마켓상태 일괄처리</td>
	<td>선택한주문
		<select name="send_status" id="send_status">
			<option value="">== 처리상태 선택 ==</option>
			<? foreach($send_order_status as $row_send_order) { ?>
			<option value="<?=$row_send_order['code']?>" ><?=$row_send_order['code_nm']?></option>
			<? }?>
		</select>
		<a href="javascript:sendStatus();"><img src="../img/btn_maretorder.gif" alt="처리" align="absmiddle" /></a>
	</td>
</tr>
<tr>
	<td>송장 CSV 파일 올리기</td>
	<td><input type="file" name="file_excel" size="45" required label="CSV 파일"> &nbsp;&nbsp; <a href="javascript:setDeliveryInfo();"><img src="../img/btn_regist_s.gif" align="absmiddle"></a> <a href="javascript:downOrderExcel();"><img src="../img/btn_marketorder_x.gif" alt="검색주문 엑셀 다운로드" align="absmiddle"></a>
	</td>
</tr>
</table>
</form>

<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td height="5"></td></tr>
<tr><td>
<a href="../selly/orderScrap.php"><font color=white><u>[주문수집]</u></font></a>으로 수집된 주문의 상태를 처리하실 수 있습니다.<br/><br/><br/>

마켓주문상태에서 상태별 텍스트를 클릭하시면 해당 상태의 주문만 검색하실 수 있습니다.<br/>
마켓주문상태 하단에 있는 전체주문 아이콘을 클릭하여 모든상태의 주문을 검색하실 수 있습니다.<br/>
마켓상태 일괄처리는 주문상태별로 검색을 하시면 처리할 수 있는 주문을 상태를 선택하실 수 있습니다.<br/>
리스트 하단의 송장 CSV파일 올리기를 통해 송장번호를 일괄로 등록하실 수 있습니다.<br/>
리스트에서 주문번호를 클릭시 팝업으로 주문상세내역을 확인하실 수 있습니다.<br/><br/><br/>

주문상세내역 팝업에서는 주문정보, 결제정보, 주문자정보 등 배송에 필요한 정보를 확인하실 수 있으며<br/>
송장번호/교환송장번호를 입력하실 수 있습니다.<br/>
주문상세내역 팝업의 마켓주문상태처리에서 다음 상태의 텍스트박스를 클릭하시면 주문의 상태를 선택한 상태로 변경하실 수 있습니다.
</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

<? include "../_footer.php"; ?>


