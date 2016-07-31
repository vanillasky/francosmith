<?php
include "../lib.php";
include "../../conf/config.php";
include "../../lib/godopost.class.php";

if (get_magic_quotes_gpc()) {
	stripslashes_all($_POST);
	stripslashes_all($_GET);
}

$result_count = array( 'total'=>0,'success'=>0, 'fail'=>0 );	// 할당 결과 카운트

$godopost = new godopost();

$sel_ordno = (array)$_POST['sel_ordno'];
$ps_method = $_POST['ps_method'];

$sword = $_POST['sword'];
$skey = ($_POST['skey']?$_POST['skey']:'all');
$dvcodeflag = $_POST['dvcodeflag'];
$reserved = $_POST['reserved'];
$arStep = (array)$_POST['step'];
$settlekind = $_POST['settlekind'];
$regdt_start = (int)$_POST['regdt'][0];
$regdt_end = (int)$_POST['regdt'][1];

$prepay_ordno=array();
$collect_ordno=array();

if($ps_method=='searched') { // 검색된 주문 모두 송장 번호를 할당 받아야 하는 경우
	// 검색 배열 만들기
	$arWhere=array();

	// 키워드 검색
	if($sword) {
		$sword = $db->_escape($sword);
		switch($skey) {
			case 'all':
				$arWhere[] = "(
					ordno = '{$sword}' or
					nameOrder = '{$sword}' or
					bankSender = '{$sword}'
				)";
				break;
			case 'ordno':
				$arWhere[] = "ordno = '{$sword}'";
				break;
			case 'nameOrder':
				$arWhere[] = "nameOrder = '{$sword}'";
				break;
			case 'bankSender':
				$arWhere[] = "bankSender = '{$sword}'";
				break;
		}
	}

	// 송장번호 발급상태
	if($dvcodeflag=='yes') {
		$arWhere[] = 'deliverycode <> ""';
	}
	elseif($dvcodeflag=='no') {
		$arWhere[] = 'deliverycode = ""';
	}
	elseif($dvcodeflag=='error') {
		$arWhere[] = 'TRIM(mobileReceiver) NOT REGEXP \'^([0-9]{3,4})-?([0-9]{3,4})-?([0-9]{4})$\'';
	}

	// 주문상태 검색
	if(count($arStep)) {
		foreach($arStep as $k=>$v) {
			$arStep[$k]=(int)$v;
		}
		$arWhere[] = 'step in ('.implode(',',$arStep).')';
		$arWhere[] = 'step2 = 0';
	}

	// 주문일 검색
	if($regdt_start && $regdt_end) {
		$tmp_start = date("Y-m-d 00:00:00",strtotime($regdt_start));
		$tmp_end = date("Y-m-d 23:59:59",strtotime($regdt_end));
		$arWhere[] = "orddt between '{$tmp_start}' and '{$tmp_end}'";
	}
	elseif($regdt_start) {
		$tmp_start = date("Y-m-d 00:00:00",strtotime($regdt_start));
		$arWhere[] = "orddt >= '{$tmp_start}'";
	}
	elseif($regdt_end) {
		$tmp_end = date("Y-m-d 23:59:59",strtotime($regdt_end));
		$arWhere[] = "orddt <= '{$tmp_end}'";
	}

	// 결제방법 검색
	if($settlekind) {
		$settlekind = $db->_escape($settlekind);
		$arWhere[] = "settlekind = '{$settlekind}'";
	}

	$arWhere[] = 'deliverycode = ""';

	if(count($arWhere)) {
		$strWhere = 'where '.implode(" and ",$arWhere);
	}

	$query = "
		select 
			ordno,deli_type,deli_msg,mobileReceiver
		from
			gd_order
		{$strWhere}
		limit
			1000
	";
	$result = $db->_select($query);

	
	foreach($result as $v) {

		$result_count['total']++;

		if (getPurePhoneNumber($v['mobileReceiver']) == '') {
			$result_count['fail']++;
			continue;
		}

		if($v['deli_type']=='후불' && $v['deli_msg']=='0원') {
			$prepay_ordno[]=$v['ordno'];
		}
		elseif($v['deli_type']=='후불'){
			$collect_ordno[]=$v['ordno'];
		}
		elseif($v['deli_msg']=='개별 착불 배송배') {
			$collect_ordno[]=$v['ordno'];
		}
		else {
			$prepay_ordno[]=$v['ordno'];
		}

		$result_count['success']++;
	}
}
else { // 선택된 주문만 할당 받는 경우
	$query = $db->_query_print("
		select 
			ordno,deli_type,deli_msg,mobileReceiver
		from
			gd_order
		where
			ordno in [v]
	",$sel_ordno);
	$result = $db->_select($query);
	foreach($result as $v) {

		$result_count['total']++;

		if (getPurePhoneNumber($v['mobileReceiver']) == '') {
			$result_count['fail']++;
			continue;
		}
		
		if($v['deli_type']=='후불' && $v['deli_msg']=='0원') {
			$prepay_ordno[]=$v['ordno'];
		}
		elseif($v['deli_type']=='후불'){
			$collect_ordno[]=$v['ordno'];
		}
		elseif($v['deli_msg']=='개별 착불 배송배') {
			$collect_ordno[]=$v['ordno'];
		}
		else {
			$prepay_ordno[]=$v['ordno'];
		}

		$result_count['success']++;
	}
}



// 몇개의 송장번호가 필요한지 정의
$needs_prepay_count = count($prepay_ordno);
$needs_collect_count = count($collect_ordno);
if($needs_count==0 && $collect_ordno==0) {
	exit;
}

$result = $godopost->get_regino($needs_prepay_count,$needs_collect_count); //godopost클래스로부터 송장번호를 할당 받는다

for($i=0;$i<$needs_prepay_count;$i++) {
	$query = $db->_query_print(
		'update gd_order set deliveryno=[i] , deliverycode=[s] where ordno=[s]'
		,100,$result['prepay'][$i],$prepay_ordno[$i]
	);
	$db->query($query);


	$query = $db->_query_print(
		'update gd_order_item set dvno=[i] , dvcode=[s] where ordno=[s]'
		,100,$result['prepay'][$i],$prepay_ordno[$i]
	);
	$db->query($query);
}

for($i=0;$i<$needs_collect_count;$i++) {
	$query = $db->_query_print(
		'update gd_order set deliveryno=[i] , deliverycode=[s] where ordno=[s]'
		,100,$result['collect'][$i],$collect_ordno[$i]
	);
	$db->query($query);


	$query = $db->_query_print(
		'update gd_order_item set dvno=[i] , dvcode=[s] where ordno=[s]'
		,100,$result['collect'][$i],$collect_ordno[$i]
	);
	$db->query($query);
}


// 완료처리필요

?>
<script>
var msg  = '우체국택배 송장번호가 발급되었습니다.';
	<?if ($result_count['fail'] > 0) { ?>
	msg += '\n\n';
	msg += ' 전체 : <?=number_format($result_count['total'])?>건\n';
	msg += ' 발급 : <?=number_format($result_count['success'])?>건\n';
	msg += ' 실패 : <?=number_format($result_count['fail'])?>건 (사유 : 연락처 오류)\n\n';
	msg += '확인을 누르시면 실패목록으로 이동합니다.';
	<? } ?>

<? if ($result_count['fail'] > 0) { ?>
if (confirm(msg)) {
	parent.location.href='./post_list.php?dvcodeflag=error';
}
else {
	parent.location.href=parent.location.href;
}
<? } else { ?>
	alert(msg);
	parent.location.href=parent.location.href;
<? } ?>
</script>
