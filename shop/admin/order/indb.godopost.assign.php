<?php
include "../lib.php";
include "../../conf/config.php";
include "../../lib/godopost.class.php";

if (get_magic_quotes_gpc()) {
	stripslashes_all($_POST);
	stripslashes_all($_GET);
}

$result_count = array( 'total'=>0,'success'=>0, 'fail'=>0 );	// �Ҵ� ��� ī��Ʈ

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

if($ps_method=='searched') { // �˻��� �ֹ� ��� ���� ��ȣ�� �Ҵ� �޾ƾ� �ϴ� ���
	// �˻� �迭 �����
	$arWhere=array();

	// Ű���� �˻�
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

	// �����ȣ �߱޻���
	if($dvcodeflag=='yes') {
		$arWhere[] = 'deliverycode <> ""';
	}
	elseif($dvcodeflag=='no') {
		$arWhere[] = 'deliverycode = ""';
	}
	elseif($dvcodeflag=='error') {
		$arWhere[] = 'TRIM(mobileReceiver) NOT REGEXP \'^([0-9]{3,4})-?([0-9]{3,4})-?([0-9]{4})$\'';
	}

	// �ֹ����� �˻�
	if(count($arStep)) {
		foreach($arStep as $k=>$v) {
			$arStep[$k]=(int)$v;
		}
		$arWhere[] = 'step in ('.implode(',',$arStep).')';
		$arWhere[] = 'step2 = 0';
	}

	// �ֹ��� �˻�
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

	// ������� �˻�
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

		if($v['deli_type']=='�ĺ�' && $v['deli_msg']=='0��') {
			$prepay_ordno[]=$v['ordno'];
		}
		elseif($v['deli_type']=='�ĺ�'){
			$collect_ordno[]=$v['ordno'];
		}
		elseif($v['deli_msg']=='���� ���� ��۹�') {
			$collect_ordno[]=$v['ordno'];
		}
		else {
			$prepay_ordno[]=$v['ordno'];
		}

		$result_count['success']++;
	}
}
else { // ���õ� �ֹ��� �Ҵ� �޴� ���
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
		
		if($v['deli_type']=='�ĺ�' && $v['deli_msg']=='0��') {
			$prepay_ordno[]=$v['ordno'];
		}
		elseif($v['deli_type']=='�ĺ�'){
			$collect_ordno[]=$v['ordno'];
		}
		elseif($v['deli_msg']=='���� ���� ��۹�') {
			$collect_ordno[]=$v['ordno'];
		}
		else {
			$prepay_ordno[]=$v['ordno'];
		}

		$result_count['success']++;
	}
}



// ��� �����ȣ�� �ʿ����� ����
$needs_prepay_count = count($prepay_ordno);
$needs_collect_count = count($collect_ordno);
if($needs_count==0 && $collect_ordno==0) {
	exit;
}

$result = $godopost->get_regino($needs_prepay_count,$needs_collect_count); //godopostŬ�����κ��� �����ȣ�� �Ҵ� �޴´�

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


// �Ϸ�ó���ʿ�

?>
<script>
var msg  = '��ü���ù� �����ȣ�� �߱޵Ǿ����ϴ�.';
	<?if ($result_count['fail'] > 0) { ?>
	msg += '\n\n';
	msg += ' ��ü : <?=number_format($result_count['total'])?>��\n';
	msg += ' �߱� : <?=number_format($result_count['success'])?>��\n';
	msg += ' ���� : <?=number_format($result_count['fail'])?>�� (���� : ����ó ����)\n\n';
	msg += 'Ȯ���� �����ø� ���и������ �̵��մϴ�.';
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
