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

// 검색조건으로 받는 모든 값 정의
$search = unserialize($_POST['search']);

// 변수검증
if(!in_array($search['dtkind'],array('orddt','cdt','ddt','confirmdt'))) { exit; }
if(!in_array($search['skey'],array('all','ordno','nameOrder','nameReceiver','bankSender','m_id','mobileOrder'))) { exit; }
if(!in_array($search['sgkey'],array('','goodsnm','brandnm','maker'))) { exit; }
foreach($search['step'] as $k=>$v) { $search['step'][$k]=(int)$v; }
foreach($search['step2'] as $k=>$v) { $search['step2'][$k]=(int)$v; }

// 쿼리문을 위한 검색조건 만들기
$isOrderItemSearch=false;
$arWhere = array();
// 접수유형
if($search['sugi']) {
	if($search['sugi'] == "Y") $arWhere[] = "o.inflow = 'sugi'";
	elseif($search['sugi'] == "N") $arWhere[] = "o.inflow != 'sugi'";
}
if($search['regdt_start']) {
	if(!$search['regdt_end']) $search['regdt_end'] = date('Ymd');
	$tmp_start = substr($search['regdt_start'],0,4).'-'.substr($search['regdt_start'],4,2).'-'.substr($search['regdt_start'],6,2);
	$tmp_end = substr($search['regdt_end'],0,4).'-'.substr($search['regdt_end'],4,2).'-'.substr($search['regdt_end'],6,2);

	if ($search['regdt_time_start'] !== -1 && $search['regdt_time_end'] !== -1) {
		$tmp_start .= ' '.sprintf('%02d',$search['regdt_time_start']).':00:00';
		$tmp_end .= ' '.sprintf('%02d',$search['regdt_time_end']).':59:59';
	}
	else {
		$tmp_start .= ' 00:00:00';
		$tmp_end .= ' 23:59:59';
	}

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
				case 60 : $subWhere[] = "(oi.dyn='e' and oi.cyn='e')"; $isOrderItemSearch=true; break; //교환완료
				case 61 : $subWhere[] = "o.oldordno != ''";break; //재주문
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
				m.m_id = '{$es_sword}' or 
				o.mobileOrder like '%{$es_sword}%'
			)"; break;
		case 'ordno': $arWhere[] = "o.ordno = '{$es_sword}'"; break;
		case 'nameOrder': $arWhere[] = "o.nameOrder like '%{$es_sword}%'"; break;
		case 'nameReceiver': $arWhere[] = "o.nameReceiver like '%{$es_sword}%'"; break;
		case 'bankSender': $arWhere[] = "o.bankSender like '%{$es_sword}%'"; break;
		case 'm_id': $arWhere[] = "m.m_id = '{$es_sword}'"; break;
		case 'mobileOrder': $arWhere[] = "o.mobileOrder like '%{$es_sword}%'"; break;
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

//주문금액 검색
if ($search['s_prn_settleprice'] != '' && $search['e_prn_settleprice'] != '')			$arWhere[] = "o.prn_settleprice between ".$search['s_prn_settleprice']." and ".$search['e_prn_settleprice'];
else if ($search['s_prn_settleprice'] != '' &&  $search['e_prn_settleprice'] == '')		$arWhere[] = "o.prn_settleprice >= ".$search['s_prn_settleprice'];
else if ($search['s_prn_settleprice'] == '' && $search['e_prn_settleprice'] != '')	

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
	$arWhere[] = $db->_query_print('exists(SELECT * FROM '.GD_ORDER_ITEM.' AS oi JOIN '.GD_GOODS.' AS g ON oi.goodsno=g.goodsno WHERE oi.ordno=o.ordno AND g.todaygoods=[s])',$search['todaygoods']);
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

// 엑셀다운로드 조건 처리
if($_POST['xls_itemcondition']) {
	if ($_POST['xls_itemcondition'] == 'N') {
		$arWhere[] = 'oi.istep < 40';
	}
	else if ($_POST['xls_itemcondition'] == 'A') {
		$arWhere[] = 'oi.istep > 40';
	}
}

// gd_order_item 에서 검색조건이 발생하는 경우 상품갯수와 상품송장체크는 별도로 처리
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

$orderby = 'order by o.ordno desc';
if ($search['mode'] == 'group') {
	$orderby = 'order by step2*10+step,o.dyn,o.ordno desc';
}

// 쿼리 실행
if ($_POST['mode'] == 'goods'){ // 상품별 엑셀파일
	$query = '
		select
			o.*,
			m.m_id as m_id,
			g.*,
			oi.*,
			o.dyn,
			o.memo as order_memo,
			oi.goodsnm
		from
			'.GD_ORDER.' as o
			left join '.GD_ORDER_ITEM.' as oi on o.ordno=oi.ordno
			left join '.GD_GOODS.' g on oi.goodsno=g.goodsno
			left join '.GD_MEMBER.' as m on o.m_no = m.m_no
			'.$join_GD_COUPON_ORDER.'
		'.$strWhere.'
		'.$orderby.'
	';
} else { // 주문별 엑셀파일
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
			left join '.GD_MEMBER.' as m on o.m_no = m.m_no
			'.$join_GD_COUPON_ORDER.'
		'.$strWhere.'
		group by o.ordno
		'.$orderby.'
	';
}
$result = $db->_select($query);

// 엑셀항목정의
if(!$orderXls)$orderXls = $default['orderXls'];
else $orderXls = getdefault('orderXls');
foreach($orderXls as $tmp) if($tmp[1]=='goodsnm' && $tmp[3]=='checked')$addfield['goodsnm']=1;

if(!$orderGoodsXls)$orderGoodsXls = $default['orderGoodsXls'];
else $orderGoodsXls = getdefault('orderGoodsXls');

foreach($orderXls as $key=>$value)if($value[3]=='')unset($orderXls[$key]);
foreach($orderGoodsXls as $key=>$value)if($value[3]=='')unset($orderGoodsXls[$key]);

?>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<style>td {mso-number-format:"@"}</style>
<? if ($_POST[mode]=='goods'){ ?>

<table border="1">
<tr bgcolor="#f7f7f7">
<?
	foreach($orderGoodsXls as $k => $v)	echo('<th>'.$v[0].'</th>');
?>
</tr>
<?
	foreach($result as $data) {
?>
<tr>
	<?
	$data['no'] = $data['opt'] = $data['sprice'] = $data['deliveryno'] = $data['deliverycode'] = '';
	if(!$data['dvno']) $data['dvno'] = '';
	$data['no'] = ++$idx;
	if($data['opt1'])$data['opt'] .= '['.$data['opt1'];
	if($data['opt2'])$data['opt'] .= '/'.$data['opt2'];
	if($data['opt'])$data['opt'] .= ']';
	if($data['addopt']) $data['opt'] .= '<div>['.str_replace("^",'] [',$data['addopt']).']</div>';
	$data['settlekind'] = $r_settlekind[$data['settlekind']];
	$data['step'] = $r_istep[$data['istep']];
	$data['deliveryno'] = $data['dvno'];
	$data['deliverycode'] = $data['dvcode'];
	$data['sprice']=$data['prn_settleprice'];
	if($data['deli_msg'])$data['deli_type'] = $data['deli_msg'];
	$data['deli_type'] = str_replace('후불','착불',$data['deli_type']);
	/* 도로명주소가 있으면 도로명주소가 출력되고 없으면 지번주소가 출력됨 */
	if($data['road_address'] != "") {
		$data['address_'] = $data['road_address'];
	} else {
		$data['address_'] = $data['address'];
	}
	/* 새 우편번호가 있으면 새 우편번호가 출력되고 없으면 (구)우편번호가 출력됨 */
	if($data['zonecode'] != '') {
		$data['zipcode_'] = $data['zonecode'];
	} else {
		$data['zipcode_'] = $data['zipcode'];
	}

	foreach($orderGoodsXls as $k => $v)  echo('<td>'.nl2br(strip_tags($data[$v[1]])).'</td>');
	?>
</tr>
<? } ?>
</table>

<? } else {?>

<table border="1">
<tr bgcolor="#f7f7f7">
<?
	foreach($orderXls as $k => $v)	echo('<th>'.$v[0].'</th>');
?>
</tr>
<?
	foreach($result as $data) {
?>
<tr>
	<?
	if($addfield['goodsnm']){
		if($data['count_item']>1) $data['goodsnm'] = $data['goodsnm'].' 외'.($data['count_item']-1).'건';
		else $data['goodsnm'] = $data['goodsnm'];
	}
	if(!$data['deliveryno']) $data['deliveryno'] = '';
	$data['no'] = $data['opt'] = $data['sprice'] = '';
	$data['no'] = ++$idx;
	$data['settlekind'] = $r_settlekind[$data['settlekind']];
	$step = getStepMsg($data['step'],$data['step2'],$data['ordno']);
	if(strlen($step) > 10) $step = substr($step,10);
	$data['step'] = $step;
	$data['order_memo'] = $data['memo'];
	$data['settleprice'] = $data['prn_settleprice'];
	list($dcnt) = $db->fetch("select count(*) from gd_order_item where ordno='$data[ordno]' and deli_msg != ''");
	if($data['deli_msg']  == '개별 착불 배송비') $data['deli_type'] = '개별 착불';
	if($data['deli_type'] == '선불' && $dcnt > 0) $data['deli_type'] .= '(개별 착불)';
	$data['deli_type'] = str_replace('후불','착불',$data['deli_type']);
	/* 도로명주소가 있으면 도로명주소가 출력되고 없으면 지번주소가 출력됨 */
	if($data['road_address'] != "") {
		$data['address_'] = $data['road_address'];
	} else {
		$data['address_'] = $data['address'];
	}
	/* 새 우편번호가 있으면 새 우편번호가 출력되고 없으면 (구)우편번호가 출력됨 */
	if($data['zonecode'] != '') {
		$data['zipcode_'] = $data['zonecode'];
	} else {
		$data['zipcode_'] = $data['zipcode'];
	}

	foreach($orderXls as $k => $v) echo('<td>'.nl2br(strip_tags($data[$v[1]])).'</td>');
	?>
</tr>
<? } ?>
</table>
<? } ?>