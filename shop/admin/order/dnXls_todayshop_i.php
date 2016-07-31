<?
set_time_limit(0);
include '../lib.php';
@include '../../conf/config.pay.php';
@include '../../conf/orderXls.php';

header('Content-Type: application/vnd.ms-excel; charset=euc-kr');
header('Content-Disposition: attachment; filename=GDorder_'.$_POST['mode'].'_'.date('YmdHi').'.xls');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0,pre-check=0');
header('Pragma: public');

if (get_magic_quotes_gpc()) {
	stripslashes_all($_POST);
}

// �˻��������� �޴� ��� �� ����
$search = unserialize($_POST['search']);

// ��������
if(!in_array($search['dtkind'],array('orddt','cdt','ddt','confirmdt'))) { exit; }
if(!in_array($search['skey'],array('all','ordno','nameOrder','nameReceiver','bankSender','m_id'))) { exit; }
if(!in_array($search['sgkey'],array('','goodsnm','brandnm','maker'))) { exit; }
foreach($search['step'] as $k=>$v) { $search['step'][$k]=(int)$v; }
foreach($search['step2'] as $k=>$v) { $search['step2'][$k]=(int)$v; }

// �������� ���� �˻����� �����
$isOrderItemSearch=false;
$arWhere = array();
if($search['regdt_start']) {
	if(!$search['regdt_end']) $search['regdt_end'] = date('Ymd');
	$tmp_start = substr($search['regdt_start'],0,4).'-'.substr($search['regdt_start'],4,2).'-'.substr($search['regdt_start'],6,2).' 00:00:00';
	$tmp_end = substr($search['regdt_end'],0,4).'-'.substr($search['regdt_end'],4,2).'-'.substr($search['regdt_end'],6,2).' 23:59:59';
	switch($search['dtkind']) {
		case 'orddt': $arWhere[] = $db->_query_print('o.orddt between [s] and [s]',$tmp_start,$tmp_end); break;
		case 'cdt': $arWhere[] = $db->_query_print('o.cdt between [s] and [s]',$tmp_start,$tmp_end); break;
		case 'ddt': $arWhere[] = $db->_query_print('o.ddt between [s] and [s]',$tmp_start,$tmp_end); break;
		case 'confirmdt': $arWhere[] = $db->_query_print('o.confirmdt between [s] and [s]',$tmp_start,$tmp_end); break;
	}
}
if($search['settlekind']) {
	$arWhere[] = $db->_query_print('o.settlekind = [s]',$search['settlekind']);
}
if(count($search['step']) || count($search['step2'])) {
	$subWhere = array();
	if(count($search['step'])) {
		$subWhere[] = '(o.step in ("'.implode('","', $search['step']).'") and o.step2="0")';
	}
	if(count($search['step2'])) {
		foreach($search['step2'] as $k=>$v) {
			switch($v) {
				case 1: $subWhere[] = '(o.step=0 and o.step2 between 1 and 49)'; break;
				case 2: $subWhere[] = '(o.step in (1,2) and o.step2!=0) OR (o.cyn="r" and o.step2="44" and o.dyn!="e")'; break;
				case 3: $subWhere[] = '(o.step in (3,4) and o.step2!=0)'; break;
				case 60 : $subWhere[] = "(oi.dyn='e' and oi.cyn='e')"; $isOrderItemSearch=true; break; //��ȯ�Ϸ�
				case 61 : $subWhere[] = "o.oldordno != ''";break; //���ֹ�
				default : $subWhere[] = "o.step2 = '$v'";
			}
		}
	}
	if(count($subWhere)) {
		$arWhere[] = '('.implode(' or ',$subWhere).')';
	}
}
if($search['sword'] && $search['skey']) {
	$es_sword = $db->_escape($search['sword']);
	switch($search['skey']) {
		case 'all':
			$arWhere[] = "(
				o.ordno = '{$es_sword}' or
				o.nameOrder like '%{$es_sword}%' or
				o.nameReceiver like '%{$es_sword}%' or
				o.bankSender like '%{$es_sword}%' or
				m.m_id = '{$es_sword}'
			)"; break;
		case 'ordno': $arWhere[] = "o.ordno = '{$es_sword}'"; break;
		case 'nameOrder': $arWhere[] = "o.nameOrder like '%{$es_sword}%'"; break;
		case 'nameReceiver': $arWhere[] = "o.nameReceiver like '%{$es_sword}%'"; break;
		case 'bankSender': $arWhere[] = "o.bankSender like '%{$es_sword}%'"; break;
		case 'm_id': $arWhere[] = "m.m_id = '{$es_sword}'"; break;
	}
}
if($search['sgword'] && $search['sgkey']) {
	$es_sgword = $db->_escape($search['sgword']);
	switch($search['sgkey']) {
		case 'goodsnm': $arWhere[] = "oi.goodsnm like '%{$es_sgword}%'"; break;
		case 'brandnm': $arWhere[] = "oi.brandnm like '%{$es_sgword}%'"; break;
		case 'maker': $arWhere[] = "oi.maker like '%{$es_sgword}%'"; break;
	}
	$isOrderItemSearch=true;
}
if(count($search['chk_inflow'])) {
	$es_inflow = array();
	foreach($search['chk_inflow'] as $v) {
		if($v == 'naver_price') {
			$es_inflow[] = '"naver_elec"';
			$es_inflow[] = '"naver_bea"';
			$es_inflow[] = '"naver_milk"';
		}
		else {
			$es_inflow[] = '"'.$db->_escape($v).'"';
		}
	}
	$arWhere[] = 'o.inflow in ('.implode(',',$es_inflow).')';
}
if($search['cbyn']=='Y') {
	$arWhere[] = 'o.cbyn = "Y"';
}
if($search['aboutcoupon']=='1') {
	$arWhere[] = 'o.about_coupon_flag = "Y"';
}
if($search['escrowyn']) {
	$arWhere[] = $db->_query_print('o.escrowyn = [s]',$search['escrowyn']);
}
if($search['eggyn']) {
	$arWhere[] = $db->_query_print('o.eggyn = [s]',$search['eggyn']);
}
if($search['mobilepay']) {
	$arWhere[] = $db->_query_print('o.mobilepay = [s]',$search['mobilepay']);
}
if ($search['todaygoods']) {
	if ($search['goodstype']) {
		$ts_subqry = ' AND ts.goodstype=\''.$search['goodstype'].'\'';
	}
	if ($search['processtype']) {
		$ts_subqry .= ' AND ts.processtype=\''.$search['processtype'].'\'';
	}

	$ts_qry = 'exists(SELECT * FROM '.GD_ORDER_ITEM.' AS oi JOIN '.GD_GOODS.' AS g ON oi.goodsno=g.goodsno JOIN '.GD_TODAYSHOP_GOODS.' AS ts ON g.goodsno=ts.goodsno WHERE oi.ordno=o.ordno AND g.todaygoods=[s] '.$ts_subqry.')';
	$arWhere[] = $db->_query_print($ts_qry,$search['todaygoods']);
}
if($search['cashreceipt']) {
	$arWhere[] = 'o.cashreceipt != ""';
}
if($search['couponyn']) {
	$arWhere[] = 'co.ordno is not null';
	$join_GD_COUPON_ORDER='left join '.GD_COUPON_ORDER.' as co on o.ordno=co.ordno';
}
else {
	$join_GD_COUPON_ORDER='';
}

// gd_order_item ���� �˻������� �߻��ϴ� ��� ��ǰ������ ��ǰ����üũ�� ������ ó��
if($isOrderItemSearch) {
	$select_count_item = '(select count(*) from '.GD_ORDER_ITEM.' as s_oi where s_oi.ordno=o.ordno) as count_item';
	$select_count_dv_item = '(select count(*) from '.GD_ORDER_ITEM.' as s_oi where s_oi.ordno=o.ordno and s_oi.dvcode!="" and s_oi.dvno!="") as count_dv_item';
}
else {
	$select_count_item = 'count(oi.ordno) as count_item';
	$select_count_dv_item = 'sum(oi.dvcode != "" and oi.dvno != "") as count_dv_item';
}

if(count($arWhere)) {
	$strWhere = 'where '.implode(' and ',$arWhere);
}

// ���� ����
switch($_POST['mode']) {
	case 'todaygoods': {
		$query = '
			select
				o.*,
				m.m_id as m_id,
				'.$select_count_item.',
				'.$select_count_dv_item.',
				oi.goodsnm as goodsnm
			from
				'.GD_ORDER.' as o
				left join '.GD_ORDER_ITEM.' as oi on o.ordno=oi.ordno
				left join '.GD_GOODS.' as g on oi.goodsno=g.goodsno
				left join '.GD_TODAYSHOP_GOODS.' as tg on tg.goodsno=g.goodsno
				left join '.GD_MEMBER.' as m on o.m_no = m.m_no
				'.$join_GD_COUPON_ORDER.'
			'.$strWhere.'
			group by o.ordno
			order by o.ordno desc
		';
		break;
	}
	case 'todaycoupon': {
		$query = '
			select
				o.*,
				m.m_id as m_id,
				cp.cp_num, cp.cp_ea AS ea, tg.usestartdt, tg.useenddt,
				'.$select_count_item.',
				'.$select_count_dv_item.',
				oi.goodsnm as goodsnm
			from
				'.GD_ORDER.' as o
				left join '.GD_ORDER_ITEM.' as oi on o.ordno=oi.ordno
				left join '.GD_GOODS.' as g on oi.goodsno=g.goodsno
				left join '.GD_TODAYSHOP_GOODS.' as tg on tg.goodsno=g.goodsno
				left join '.GD_TODAYSHOP_ORDER_COUPON.' as cp on o.ordno = cp.ordno
				left join '.GD_MEMBER.' as m on o.m_no = m.m_no
				'.$join_GD_COUPON_ORDER.'
			'.$strWhere.'
			group by o.ordno
			order by o.ordno desc
		';
		break;
	}
}
$result = $db->_select($query);

?>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<style>td {mso-number-format:"@"}</style>
<?
// �����׸�����
switch($_POST['mode']) {
	case 'todaygoods' : {
		if(!$orderTodayGoodsXls)$orderXls = $default['orderTodayGoodsXls'];
		else $orderXls = getdefault('orderTodayGoodsXls');
		if (is_array($orderXls) && empty($orderXls) === false) {
			foreach($orderXls as $key=>$value) if($value[3]=='') unset($orderXls[$key]);
		}

		break;
	}
	case 'todaycoupon' : {
		if(!$orderTodayCouponXls)$orderXls = $default['orderTodayCouponXls'];
		else $orderXls = getdefault('orderTodayCouponXls');
		if (is_array($orderXls) && empty($orderXls) === false) {
			foreach($orderXls as $key=>$value) if($value[3]=='') unset($orderXls[$key]);
		}

		break;
	}
}
?>
<table border="1">
<tr bgcolor="#f7f7f7">
<?
if (is_array($orderXls) && empty($orderXls) === false) {
	foreach($orderXls as $k => $v)	echo('<th>'.$v[0].'</th>');
}
?>
</tr>
<?
if (is_array($result) && empty($result) === false) {
	foreach($result as $data) {
?>
<tr>
<?
	if(!$data['dvno']) $data['dvno'] = "";
	$data['no'] = ++$idx;
	if ($data['opt1']) $data['opt'] .= "[".$data[opt1];
	if ($data['opt2']) $data['opt'] .= "/".$data[opt2];
	if ($data['opt']) $data['opt'] .= "]";
	if ($data['addopt']) $data['opt'] .= "<div>[".str_replace("^","] [",$data['addopt'])."]</div>";
	$data['settlekind'] = $r_settlekind[$data['settlekind']];
	$data['step'] = getStepMsg($data['step'],$data['step2'],$data['ordno']);
	if(strlen($data['step']) > 10) $data['step'] = substr($data['step'],10);
	$data['deliveryno'] = $data['dvno'];
	$data['deliverycode'] = $data['dvcode'];
	$data['sprice'] = $data['prn_settleprice'];
	if($data['deli_msg']) $data['deli_type'] = $data['deli_msg'];
	$data['deli_type'] = str_replace('�ĺ�','����',$data['deli_type']);
	$data['usedt'] = $data['usestartdt'].'~'.$data['useenddt'];
	$data['order_memo'] = $data['memo'];

	if (is_array($orderXls) && empty($orderXls) === false) {
		foreach($orderXls as $k => $v)  echo('<td>'.strip_tags($data[$v[1]]).'</td>');
	}
?>
</tr>
<?
	}
}
?>
</table>