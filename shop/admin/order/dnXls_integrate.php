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
	stripslashes_all($_POST); stripslashes_all($_GET); stripslashes_all($_COOKIE);
}

// �˻��������� �޴� ��� �� ����
$search = unserialize($_POST['search']);

// ���� �ֹ� ����
	@include(dirname(__FILE__).'/_cfg.integrate.php');


	$search['sort']			= !empty($search['sort']) ? $search['sort'] : 'o.ord_date desc';		// ����
	$search['mode']			= !empty($search['mode']) ? $search['mode'] : '';					// �� ����
	$search['channel']		= !empty($search['channel']) ? $search['channel'] : array();			// �Ǹ�ä��

	$search['ord_status']		= isset($search['ord_status']) ? $search['ord_status'] : -1;	// ó������
	$search['pay_method']		= !empty($search['pay_method']) ? $search['pay_method'] : '';		// ��������
	$search['ord_type']		= !empty($search['ord_type']) ? $search['ord_type'] : '';		// ��������
	$search['skey']			= !empty($search['skey']) ? $search['skey'] : '';					// �ֹ��˻� ����
	$search['sword']			= !empty($search['sword']) ? trim($search['sword']) : '';					// �ֹ��˻� Ű����
	$search['dtkind']			= !empty($search['dtkind']) ? $search['dtkind'] : 'ord_date';				// ��¥ ����
	$search['regdt']			= !empty($search['regdt']) ? $search['regdt'] : array(date('Ymd',strtotime('-'.(int)$cfg['orderPeriod'].' day',$now)), date('Ymd',$now));					// ��¥
	$search['regdt_range']	= !empty($search['regdt']) ? $search['regdt'] : '';					// ��¥ �Ⱓ ( regdt[0] ���� ��ĥ )
	$search['regdt_time']		= !empty($search['regdt_time']) ? $search['regdt_time'] : array(-1,-1);		// �ð�
	$search['sgkey']			= !empty($search['sgkey']) ? $search['sgkey'] : '';					// ��ǰ�˻� ����
	$search['sgword']			= !empty($search['sgword']) ? trim($search['sgword']) : '';				// ��ǰ�˻� Ű����

	$search['flg_egg']			= !empty($search['flg_egg']) ? $search['flg_egg'] : '';					// �Һ������غ�����
	$search['flg_escrow']		= !empty($search['flg_escrow']) ? $search['flg_escrow'] : '';			// ����ũ��
	$search['flg_cashreceipt']	= !empty($search['flg_cashreceipt']) ? $search['flg_cashreceipt'] : '';		// ���ݿ�����

	$search['flg_coupon']		= !empty($search['flg_coupon']) ? $search['flg_coupon'] : '';			// �������

	$search['flg_aboutcoupon']	= !empty($search['flg_aboutcoupon']) ? $search['flg_aboutcoupon'] : '';		// ��ٿ�����
	$search['flg_cashbag']			= !empty($search['flg_cashbag']) ? $search['flg_cashbag'] : '';					// okĳ�ù� ����
	$search['pay_method_p']	= !empty($search['pay_method_p']) ? $search['pay_method_p'] : '';	// ������(����Ʈ)
	$search['chk_inflow']		= !empty($search['chk_inflow']) ? $search['chk_inflow'] : array();	// ȫ��ä�� (���԰��)

	$search['page']			= !empty($search['page']) ? $search['page'] : 1;						// ������
	$search['page_num']		= !empty($search['page_num']) ? $search['page_num'] : ($cfg['orderPageNum'] ? $cfg['orderPageNum'] : 20);	// �������� ���ڵ��


// �˻��� ����

	#0. �ʱ�ȭ
		$arWhere = array();

	#1. �Ǹ� ä��
		if (sizeof($search['channel']) < 1 || $search['channel']['all']) {
			$search['channel'] = array();
			$search['channel']['all'] = 1;
		}
		elseif (sizeof($search['channel']) === 6) {
			$search['channel'] = array();
			$search['channel']['all'] = 1;
		}
		else {
			$_tmp = array();
			if ($search['channel']['mobile'] || $search['channel']['todayshop']) $search['channel']['enamoo'] = 1;
			foreach($search['channel'] as $k=>$v) {
				if ($k == 'mobile') {
					$arWhere[] = 'o.flg_mobile = \'1\'';
				}
				else if ($k == 'todayshop') {
					$arWhere[] = 'exists(SELECT * FROM '.GD_ORDER_ITEM.' AS oi JOIN '.GD_GOODS.' AS g ON oi.goodsno=g.goodsno WHERE oi.ordno=o.ordno AND g.todaygoods=\'y\')';
				}
				else {
					$_tmp[] = " o.channel = '$k' ";
				}
			}
			if (sizeof($_tmp) > 0) $arWhere[] = '('.implode(' OR ',$_tmp).')';
		}

	#2. �ֹ� ����
		if ($search['ord_status'] == 91) {
			$arWhere[] = "(o.old_ordno > '')";
		}
		else if ($search['ord_status'] > -1) {
			$arWhere[] = $db->_query_print('o.ord_status= [s]',$search['ord_status']);
		}

	#3. ���� ����
		if($search['pay_method']) {
			$arWhere[] = $db->_query_print('o.pay_method= [s]',$search['pay_method']);
		}

	#4. ���� �˻�
		if($search['sword'] && $search['skey']) {
			$es_sword = $db->_escape($search['sword']);
			switch($search['skey']) {
				case 'all':
					$_where = array();

					foreach($integrate_cfg['skey'] as $cond) {
						if (preg_match($cond['pattern'],$es_sword)) {
							$_condition = $cond['field'];

							if ($cond['condition'] == 'like') $_condition .= ' like \'%'.$es_sword.'%\'';
							else if ($cond['condition'] == 'equal') $_condition .= ' = \''.$es_sword.'\'';
							else continue;

							$_where[] = $_condition;
						}
					}

					if (sizeof($_where) > 0) $arWhere[] = "(".implode(' OR ',$_where).")";
					break;
				case 'ordno': $arWhere[] = "o.ordno = '{$es_sword}'"; break;
				case 'ord_name': $arWhere[] = "o.ord_name like '%{$es_sword}%'"; break;
				case 'rcv_name': $arWhere[] = "o.rcv_name like '%{$es_sword}%'"; break;
				case 'pay_bank_name': $arWhere[] = "o.pay_bank_name like '%{$es_sword}%'"; break;
				case 'm_id': $arWhere[] = "m.m_id = '{$es_sword}'"; break;
				case 'ord_phone': $arWhere[] = "o.ord_phone like '%{$es_sword}%'"; break;
				case 'rcv_phone': $arWhere[] = "o.rcv_phone like '%{$es_sword}%'"; break;
				case 'rcv_address': $arWhere[] = "o.rcv_address like '%{$es_sword}%'"; break;
				case 'dlv_no': $arWhere[] = "o.dlv_no like '%{$es_sword}%'"; break;
			}
		}

	#5. ó������
		if($search['regdt'][0]) {
			if(!$search['regdt'][1]) $search['regdt'][1] = date('Ymd',$now);

			$tmp_start = substr($search['regdt'][0],0,4).'-'.substr($search['regdt'][0],4,2).'-'.substr($search['regdt'][0],6,2);
			$tmp_end = substr($search['regdt'][1],0,4).'-'.substr($search['regdt'][1],4,2).'-'.substr($search['regdt'][1],6,2);

			if ((int)$search['regdt_time'][0] !== -1 && (int)$search['regdt_time'][1] !== -1) {
				$tmp_start .= ' '.sprintf('%02d',$search['regdt_time'][0]).':00:00';
				$tmp_end .= ' '.sprintf('%02d',$search['regdt_time'][1]).':59:59';
			}
			else {
				$tmp_start .= ' 00:00:00';
				$tmp_end .= ' 23:59:59';
			}
			switch($search['dtkind']) {
				case 'ord_date': $arWhere[] = $db->_query_print('o.ord_date between [s] and [s]',$tmp_start,$tmp_end); break;
				case 'pay_date': $arWhere[] = $db->_query_print('o.pay_date between [s] and [s]',$tmp_start,$tmp_end); break;
				case 'dlv_date': $arWhere[] = $db->_query_print('o.dlv_date between [s] and [s]',$tmp_start,$tmp_end); break;
				case 'fin_date': $arWhere[] = $db->_query_print('o.fin_date between [s] and [s]',$tmp_start,$tmp_end); break;
			}
		}

	#6. ��ǰ�˻�
		$join_GD_PURCHASE = '';
		if($search['sgword'] && $search['sgkey']) {
			$es_sgword = $db->_escape($search['sgword']);
			switch($search['sgkey']) {
				case 'goodsnm': $arWhere[] = "oi.goodsnm like '%{$es_sgword}%'"; break;
				case 'brandnm': $arWhere[] = "EXISTS (SELECT ordno FROM ".GD_ORDER_ITEM." AS _oi WHERE _oi.brandnm like '%{$es_sgword}%' AND _oi.ordno = o.ordno) "; break;
				case 'maker': $arWhere[] = "EXISTS (SELECT ordno FROM ".GD_ORDER_ITEM." AS _oi WHERE _oi.maker like '%{$es_sgword}%' AND _oi.ordno = o.ordno) "; break;
				case 'goodsno': $arWhere[] = "oi.goodsno like '%{$es_sgword}%'"; break;
				case 'purchase': $arWhere[] = "pch.comnm like '%{$es_sgword}%'"; $join_GD_PURCHASE = 'INNER JOIN '.GD_PURCHASE_GOODS.' AS pchg ON pchg.goodsno = oi.goodsno INNER JOIN '.GD_PURCHASE.' AS pch ON pchg.pchsno = pch.pchsno'; break;
			}
		}

	#7. �Һ������غ�����
		if($search['flg_egg']) {
			$arWhere[] = $db->_query_print('o.flg_egg = [s]',$search['flg_egg']);
		}

	#8. ������ ����
		$tmp_arWhere = array();

		if($search['flg_escrow']) {
			$tmp_arWhere[] = $db->_query_print('o.flg_escrow = [s]',$search['flg_escrow']);
		}

		if($search['flg_cashreceipt']) {
			$tmp_arWhere[] = 'o.flg_cashreceipt != ""';
		}
		if($search['flg_coupon']) {
			$tmp_arWhere[] = 'co.ordno is not null';
			$join_GD_COUPON_ORDER='left join '.GD_COUPON_ORDER.' as co on o.ordno=co.ordno';
		}
		else {
			$join_GD_COUPON_ORDER='';
		}

		if($search['flg_aboutcoupon']=='1') {
			$tmp_arWhere[] = 'o.flg_aboutcoupon = "Y"';
		}

		if($search['pay_method_p']=='1') {
			$tmp_arWhere[] = 'o.pay_method= "p"';
		}

		if($search['flg_cashbag']=='Y') {
			$tmp_arWhere[] = 'o.flg_cashbag = "Y"';
		}

		if (sizeof($tmp_arWhere) > 0) {
			$arWhere[] = '('.implode(' OR ',$tmp_arWhere).')';
			unset($tmp_arWhere);
		}

	#9. ȫ��ä��
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
			$arWhere[] = 'o.flg_inflow in ('.implode(',',$es_inflow).')';
		}

	#10. �������� (���� �ʵ尡 ����, inflow ���� sugi �� ���ڵ�)
		if($search['ord_type'] == 'offline') {
			$arWhere[] = 'o.flg_inflow = \'sugi\'';
		}
		else if ($search['ord_type'] == 'online') {
			$arWhere[] = 'o.flg_inflow <> \'sugi\'';
		}

	#XX. where �� ��ħ
		if(!empty($arWhere)) {
			$strWhere = 'where '.implode(' and ',$arWhere);
		}



// ���� ����
if ($_POST['mode'] == 'goods'){ // ��ǰ�� ��������
	$query = '
		SELECT
			o2.*,
			m.m_id as m_id,
			g.*,
			oi.*,
			o2.dyn,
			o2.memo as order_memo,
			oi.goodsnm
		FROM

			'.GD_INTEGRATE_ORDER.' as o
			INNER JOIN '.GD_ORDER.' as o2
			ON o.ordno = o2.ordno
			LEFT JOIN '.GD_ORDER_ITEM.' as oi
			ON o.ordno = oi.ordno
			left join '.GD_GOODS.' g on oi.goodsno=g.goodsno
			LEFT JOIN '.GD_MEMBER.' as m
			ON o.m_no = m.m_no
			'.$join_GD_COUPON_ORDER.'
			'.$join_GD_PURCHASE.'
			'.$strWhere.'
	';

} else { // �ֹ��� ��������

	$query = '
		SELECT
			o2.*,
			m.m_id,
			m.m_no,
			oi.goodsnm,
			oi.goodsno,
			COUNT(oi.sno) AS count_item

		FROM

			'.GD_INTEGRATE_ORDER.' as o
			INNER JOIN '.GD_ORDER.' as o2
			ON o.ordno = o2.ordno
			LEFT JOIN '.GD_ORDER_ITEM.' as oi
			ON o.ordno = oi.ordno
			LEFT JOIN '.GD_MEMBER.' as m
			ON o.m_no = m.m_no
			'.$join_GD_COUPON_ORDER.'
			'.$join_GD_PURCHASE.'
			'.$strWhere.'

			GROUP BY o.ordno
	';
}

$result = $db->_select($query);

// �����׸�����
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
	$data['deli_type'] = str_replace('�ĺ�','����',$data['deli_type']);
	/* ���θ��ּҰ� ������ ���θ��ּҰ� ��µǰ� ������ �����ּҰ� ��µ� */
	if($data['road_address'] != "") {
		$data['address_'] = $data['road_address'];
	} else {
		$data['address_'] = $data['address'];
	}
	/* �� �����ȣ�� ������ �� �����ȣ�� ��µǰ� ������ (��)�����ȣ�� ��µ� */
	if($data['zonecode'] != '') {
		$data['zipcode_'] = $data['zonecode'];
	} else {
		$data['zipcode_'] = $data['zipcode'];
	}

	foreach($orderGoodsXls as $k => $v)  echo('<td>'.strip_tags($data[$v[1]]).'</td>');
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
		if($data['count_item']>1) $data['goodsnm'] = $data['goodsnm'].' ��'.($data['count_item']-1).'��';
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
	if($data['deli_msg']  == '���� ���� ��ۺ�') $data['deli_type'] = '���� ����';
	if($data['deli_type'] == '����' && $dcnt > 0) $data['deli_type'] .= '(���� ����)';
	$data['deli_type'] = str_replace('�ĺ�','����',$data['deli_type']);
	/* ���θ��ּҰ� ������ ���θ��ּҰ� ��µǰ� ������ �����ּҰ� ��µ� */
	if($data['road_address'] != "") {
		$data['address_'] = $data['road_address'];
	} else {
		$data['address_'] = $data['address'];
	}
	/* �� �����ȣ�� ������ �� �����ȣ�� ��µǰ� ������ (��)�����ȣ�� ��µ� */
	if($data['zonecode'] != '') {
		$data['zipcode_'] = $data['zonecode'];
	} else {
		$data['zipcode_'] = $data['zipcode'];
	}

	foreach($orderXls as $k => $v) echo('<td>'.strip_tags($data[$v[1]]).'</td>');
	?>
</tr>
<? } ?>
</table>
<? } ?>