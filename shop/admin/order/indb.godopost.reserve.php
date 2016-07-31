<?php
include "../_header.popup.php";
include "../../lib/godopost.class.php";

if (get_magic_quotes_gpc()) {
	stripslashes_all($_POST);
	stripslashes_all($_GET);
}
function strcut_mb_custom($s, $l) {
if (!$s) return '';
preg_match('/^([\xa1-\xfe]{2}|.){'.$l.'}/s', $s, $m);
return (!$m[0]) ? $s : ($m[0].'...');
}


$godopost = new godopost();

$sel_dvcode = (array)$_POST['sel_dvcode'];
$ps_method = $_POST['ps_method'];

$regdt_start = (int)$_POST['regdt'][0];
$regdt_end = (int)$_POST['regdt'][1];

if($ps_method=='searched') { // �˻��� �ֹ� ��� ���� ��ȣ�� �Ҵ� �޾ƾ� �ϴ� ���
	// �˻� �迭 �����
	$arWhere=array();

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

	$arWhere[] = 'dvno="100"';
	$arWhere[] = 'isnull(r.deliverycode)';

	if(count($arWhere)) {
		$strWhere = 'where '.implode(" and ",$arWhere);
	}


	$query = "
		select
			i.dvcode
		from
			gd_order_item as i
			left join gd_godopost_reserved as r on i.dvcode=r.deliverycode
		{$strWhere}
		group by
			i.dvcode
	";
	$result = $db->_select($query);

	$sel_dvcode=array();
	foreach($result as $v) {
		$sel_dvcode[]=$v['dvcode'];
	}
}

?>

<div class="title title_top">��ü���ù� ���� ó�� ��</div>

<?=count($sel_dvcode)?>���� ���� ó�� ��...<br>

<?

$godopost->reserve_reset();

$orderFieldNm = '';

// ���θ��ּ� �ʵ�
$roadQry = "SHOW COLUMNS FROM gd_order WHERE field='road_address'";
$roadRes = $db->_select($roadQry);
if ($roadRes[0]['Field'] != '') {
	$orderFieldNm = ', road_address';
}

foreach($sel_dvcode as $each_dvcode) {
	$query = $db->_query_print("
		select
			ordno,
			goodsno,
			goodsnm,
			opt1,
			opt2,
			addopt,
			ea
		from
			gd_order_item
		where
			dvno='100' and
			dvcode=[s] and
			cancel='0'
		order by
			sno
	",$each_dvcode);


	$item_result = $db->_select($query);
	$ordno = $item_result[0]['ordno'];

	$query = $db->_query_print("
		select
			nameOrder,
			phoneOrder,
			mobileOrder,
			nameReceiver,
			phoneReceiver,
			mobileReceiver,
			zipcode,
			address,
			memo,
			deli_type,
			deli_msg,
			zonecode
			" . $orderFieldNm . "
		from
			gd_order
		where
			ordno=[s]
	",$ordno);
	$tmp = $db->_select($query);
	$order_result = $tmp[0];

	$goodsnm = array();	// ���� �ʱ�ȭ
	$goodscd = array();	// ���� �ʱ�ȭ
	$option = '';	// �ɼ� ���� �ʱ�ȭ
	$count = '';	// ���� ���� �ʱ�ȭ

	if(count($item_result) > 5) {
		
		// �ɼǸ� ����
		if ($item_result[0]['addopt']) $item_result[0]['addopt'] = str_replace('^','/',$item_result[0]['addopt']);
		if ($item_result[0]['opt1'] && ($item_result[0]['opt2'] || $item_result[0]['addopt'])) $item_result[0]['opt1'] = $item_result[0]['opt1'].'/';
		if ($item_result[0]['opt2'] && $item_result[0]['addopt']) $item_result[0]['opt2'] = $item_result[0]['opt2'].'/';

		$option = $item_result[0]['opt1'].$item_result[0]['opt2'].$item_result[0]['addopt'];
		$count = '(����:'.$item_result[0]['ea'].')'.' �� '.(count($item_result)-1).'��';

		$goodsnm[0] = $godopost->goods_name($item_result[0]['goodsnm'],$option,$count);	// ��ǰ�� �˻�
		$goodscd[0] = $item_result[0]['goodsno'];
	}
	else {
		for($i=0; $i<count($item_result); $i++){

			// �ɼǸ� ����
			if ($item_result[$i]['addopt']) $item_result[$i]['addopt'] = str_replace('^','/',$item_result[$i]['addopt']);
			if ($item_result[$i]['opt1'] && ($item_result[$i]['opt2'] || $item_result[$i]['addopt'])) $item_result[$i]['opt1'] = $item_result[$i]['opt1'].'/';
			if ($item_result[$i]['opt2'] && $item_result[$i]['addopt']) $item_result[$i]['opt2'] = $item_result[$i]['opt2'].'/';

			$option = $item_result[$i]['opt1'].$item_result[$i]['opt2'].$item_result[$i]['addopt'];
			$count = '(����:'.$item_result[$i]['ea'].')';

			$goodsnm[$i] = $godopost->goods_name($item_result[$i]['goodsnm'],$option,$count);	// ��ǰ�� �˻�
			$goodscd[$i] = $item_result[$i]['goodsno'];
		}
	}

	if($order_result['deli_type']=='�ĺ�' && $order_result['deli_msg']=='0��') {
		$dfpayyn='N';
	}
	elseif($order_result['deli_type']=='�ĺ�'){
		$dfpayyn='Y';
	}
	elseif($order_result['deli_msg']=='���� ���� ��۹�') {
		$dfpayyn='Y';
	}
	else {
		$dfpayyn='N';
	}

	/* ���θ��ּҰ� ������ ���θ��ּҰ� ��µǰ� ������ �����ּҰ� ��µ� */
	if($order_result['road_address'] != '') {
		$order_result['address_'] = $order_result['road_address'];
	} else {
		$order_result['address_'] = $order_result['address'];
	}

	/* �ּ� ���� */
	if(strlen($order_result['address_'])>30) {
		$tmp = strrpos(substr($order_result['address_'],0,30)," ");
		if(!$tmp) $tmp=30;
		$recprsnaddr = substr($order_result['address_'],0,$tmp);
		$recprsndtailaddr = substr($order_result['address_'],$tmp);
	}
	else {
		$recprsnaddr=$order_result['address_'];
		$recprsndtailaddr="";
	}

	// ������ȣ�� ������ ������ȣ�� ��µǰ� ������ �����ȣ ��µ�
	$recprsnzipcd = '';
	if ($order_result['zonecode'] != '' && $order_result['zonecode'] != null) {
		$recprsnzipcd = $order_result['zonecode'];
	}
	else {
		$recprsnzipcd = str_replace('-','',$order_result['zipcode']);
	}

	$arRequest=array(
		'sendreqdivcd'=>'01',
		'compdivcd'=>$godopost->config['compdivcd'],
		'orderno'=>$ordno,
		'regino'=>$each_dvcode,
		'recprsnnm'=>$order_result['nameReceiver'],
		'recprsntelno'=>getPurePhoneNumber($order_result['mobileReceiver']),	// ����߱޽�(����ܰ�) �������� ó������ �����Ƿ� �������� ����.
		'recprsnetctelno'=>getPurePhoneNumber($order_result['phoneReceiver']),
		'recprsnzipcd'=>$recprsnzipcd,
		'recprsnaddr'=>$recprsnaddr,
		'recprsndtailaddr'=>$recprsndtailaddr,
		'orderprsnnm'=>$order_result['nameOrder'],
		'orderprsntelfno'=>$order_result['mobileOrder'],
		'orderprsnetctelno'=>'',
		'orderprsnzipcd'=>'',
		'orderprsnaddr'=>'',
		'orderprsndtailaddr'=>'',
		'sendwishymd'=>'',
		'sendmsgcont'=>strcut_mb_custom($order_result['memo'],50),
		'goodscd1'=>$goodscd[0],
		'goodsnm1'=>$goodsnm[0],
		'goodscd2'=>$goodscd[1],
		'goodsnm2'=>$goodsnm[1],
		'goodscd3'=>$goodscd[2],
		'goodsnm3'=>$goodsnm[2],
		'goodscd4'=>$goodscd[3],
		'goodsnm4'=>$goodsnm[3],
		'goodscd5'=>$goodscd[4],
		'goodsnm5'=>$goodsnm[4],
		'mailwght'=>'2000',
		'mailvolm'=>'60',
		'boxcnt'=>'',
		'dfpayyn'=>$dfpayyn,
		'expectrecevprc'=>'',
		'thisdddelivyn'=>'',
		'domexpyn'=>'',
		'microprclyn'=>'',
	);
	$godopost->reserve_add($arRequest);

	ctlStep($ordno,2);

}

$result = $godopost->reserve_send();

$date_reserved = time();
$insertValues=array();
foreach($result as $deliveryCode) {
	$insertValues[]=array($deliveryCode,$date_reserved);
}

$query = $db->_query_print("insert into gd_godopost_reserved (deliverycode,date_reserved) values [vs]",$insertValues);

$db->query($query);
?>
������ �Ϸ�Ǿ����ϴ�

<input type="button" value="�ݱ�" onclick="parent.location.href=parent.location.href;">
