<?
$location = "�ֹ����� > �ֹ���� ���� > ȯ�� ����Ʈ";
include "../_header.php";
include "../../lib/page.class.php";
$r_bank = codeitem("bank");

if (get_magic_quotes_gpc()) {
	stripslashes_all($_POST); stripslashes_all($_GET); stripslashes_all($_COOKIE);
}

// ���� �ֹ� ����
	@include(dirname(__FILE__).'/_cfg.integrate.php');

// �⺻ ��
	$now = time();
	$today = mktime(0,0,0,date('m',$now), date('d',$now), date('Y',$now));

	if (empty($_GET)) {
		$loc = $sess['m_id'].preg_replace('/[^a-zA-Z0-9]/','_',($_SERVER['PHP_SELF']));
		list($_data) = $db->fetch("SELECT `value` FROM gd_env WHERE `category` = 'form_helper' AND `name` = 'searchCondition'");
		$data = $_data ? unserialize($_data) : array();

		if (!empty($data[$loc])) {
			parse_str( iconv('utf-8','euc-kr',urldecode($data[$loc])),$_GET );
		}
		// �Ⱓ ����
		if (!empty($_GET['regdt'][0]) && !empty($_GET['regdt'][1])) {
			$gap = abs( strtotime($_GET['regdt'][1]) - strtotime($_GET['regdt'][0]) );
			$_GET['regdt'][0] = date('Ymd',$now - $gap);
			$_GET['regdt'][1] = date('Ymd',$now);
		}
		unset($loc,$data,$_data);
	}

// ���� ������ ����
	$integrate_order = Core::loader('integrate_order');
	$integrate_order -> doSync();

// get �Ķ���� ó�� �� �⺻�� ����
	unset($_GET['x'],$_GET['y']);


	$_GET['sort']			= !empty($_GET['sort']) ? $_GET['sort'] : 'a.regdt desc';		// ����
	$_GET['mode']			= !empty($_GET['mode']) ? $_GET['mode'] : '';					// �� ����
	$_GET['ord_status']		= !empty($_GET['ord_status']) ? $_GET['ord_status'] : array();	// ó������
	$_GET['settlekind']		= !empty($_GET['settlekind']) ? $_GET['settlekind'] : '';		// ��������
	$_GET['ord_type']		= !empty($_GET['ord_type']) ? $_GET['ord_type'] : '';		// ��������
	$_GET['skey']			= !empty($_GET['skey']) ? $_GET['skey'] : '';					// �ֹ��˻� ����
	$_GET['sword']			= !empty($_GET['sword']) ? trim($_GET['sword']) : '';					// �ֹ��˻� Ű����
	$_GET['dtkind']			= !empty($_GET['dtkind']) ? $_GET['dtkind'] : 'orddt';				// ��¥ ����
	$_GET['regdt']			= !empty($_GET['regdt']) ? $_GET['regdt'] : array(date('Ymd',strtotime('-'.(int)$cfg['orderPeriod'].' day',$now)), date('Ymd',$now));					// ��¥
	$_GET['regdt_range']	= !empty($_GET['regdt']) ? $_GET['regdt'] : '';					// ��¥ �Ⱓ ( regdt[0] ���� ��ĥ )
	$_GET['regdt_time']		= !empty($_GET['regdt_time']) ? $_GET['regdt_time'] : array(-1,-1);		// �ð�
	$_GET['sgkey']			= !empty($_GET['sgkey']) ? $_GET['sgkey'] : '';					// ��ǰ�˻� ����
	$_GET['sgword']			= !empty($_GET['sgword']) ? trim($_GET['sgword']) : '';				// ��ǰ�˻� Ű����

	$_GET['eggyn']			= !empty($_GET['eggyn']) ? $_GET['eggyn'] : '';					// ���ں�������

	$_GET['escrowyn']		= !empty($_GET['escrowyn']) ? $_GET['escrowyn'] : '';			// ����ũ��
	$_GET['cashreceipt']	= !empty($_GET['cashreceipt']) ? $_GET['cashreceipt'] : '';		// ���ݿ�����
	$_GET['flg_coupon']		= !empty($_GET['flg_coupon']) ? $_GET['flg_coupon'] : '';			// �������
	$_GET['about_coupon_flag']	= !empty($_GET['about_coupon_flag']) ? $_GET['about_coupon_flag'] : '';		// ��ٿ�����
	$_GET['pay_method_p']	= !empty($_GET['pay_method_p']) ? $_GET['pay_method_p'] : '';	// ������(����Ʈ)
	$_GET['cbyn']			= !empty($_GET['cbyn']) ? $_GET['cbyn'] : '';					// okĳ�ù� ����

	$_GET['chk_inflow']		= !empty($_GET['chk_inflow']) ? $_GET['chk_inflow'] : array();	// ȫ��ä�� (���԰��)
	$_GET['page']			= !empty($_GET['page']) ? $_GET['page'] : 1;						// ������
	$_GET['page_num']		= !empty($_GET['page_num']) ? $_GET['page_num'] : ($cfg['orderPageNum'] ? $cfg['orderPageNum'] : 20);	// �������� ���ڵ��

	$_GET['bankcode']		= !empty($_GET['bankcode']) ? $_GET['bankcode'] : '';



// �˻��� ����

	#0. �ʱ�ȭ
		$arWhere = array();

	#1. �Ǹ� ä��


	#2. �ֹ� ����
		// ȯ�� ���� ����
		$arWhere[] = "oi.istep between 40 and 49";

		if ($_GET['ord_status'] == 20) {
			$arWhere[] = "oi.cyn = 'y'";
			$arWhere[] = "oi.dyn in ('n','r')";
		}
		elseif ($_GET['ord_status'] == 21) {
			$arWhere[] = "oi.cyn = 'r'";
			$arWhere[] = "oi.dyn in ('n','r')";
		}
		else {
			$arWhere[] = "(oi.cyn = 'y' or oi.cyn = 'r')";
			$arWhere[] = "oi.dyn in ('n','r')";

		}



	#3. ���� ����
		if($_GET['settlekind']) {
			if($_GET['settlekind'] == 'payco') $arWhere[] = $db->_query_print('o.settleInflow= [s]',$_GET['settlekind']);
			else $arWhere[] = $db->_query_print('o.settlekind= [s]',$_GET['settlekind']);
		}

	#4. ���� �˻�
		if($_GET['sword'] && $_GET['skey']) {
			$es_sword = $db->_escape($_GET['sword']);
			switch($_GET['skey']) {
				case 'all':
					$_where = array();

					// ó������� �߰�
					$integrate_cfg['skey'][] = array(
						'field'=>'a.name',
						'condition'=>'like',
						'pattern'=>'/.{4,}/',
					);

					// �̳����� �����͸� �������Ƿ�, �ʵ带 ��������.
					$_skey_map = array(
						'o.ord_name' => 'o.nameOrder',
						'o.rcv_name' => 'o.nameReceiver',
						'o.pay_bank_name' => 'o.bankSender',
						'm.m_id' => 'd.m_id',
						'o.ord_phone' => 'o.phoneOrder',
						'o.rcv_phone' => 'o.phoneReceiver',
						'o.rcv_address' => 'o.address',
						'o.dlv_no' => 'o.deliverycode',
					);


					foreach($integrate_cfg['skey'] as $cond) {
						if (preg_match($cond['pattern'],$es_sword)) {

							$_condition = isset($_skey_map[$cond['field']]) ? $_skey_map[$cond['field']] : $cond['field'];

							if ($cond['condition'] == 'like') $_condition .= ' like \'%'.$es_sword.'%\'';
							else if ($cond['condition'] == 'equal') $_condition .= ' = \''.$es_sword.'\'';
							else continue;

							$_where[] = $_condition;
						}
					}


					if (sizeof($_where) > 0) $arWhere[] = "(".implode(' OR ',$_where).")";
					break;
				case 'ordno': $arWhere[] = "o.ordno = '{$es_sword}'"; break;
				case 'nameOrder': $arWhere[] = "o.nameOrder like '%{$es_sword}%'"; break;
				case 'nameReceiver': $arWhere[] = "o.nameReceiver like '%{$es_sword}%'"; break;
				case 'bankSender': $arWhere[] = "o.bankSender like '%{$es_sword}%'"; break;
				case 'm_id': $arWhere[] = "d.m_id = '{$es_sword}'"; break;
				case 'phoneOrder': $arWhere[] = "o.phoneOrder like '%{$es_sword}%'"; break;
				case 'phoneReceiver': $arWhere[] = "o.phoneReceiver like '%{$es_sword}%'"; break;
				case 'address': $arWhere[] = "o.address like '%{$es_sword}%'"; break;
				case 'deliverycode': $arWhere[] = "o.deliverycode like '%{$es_sword}%'"; break;
				case 'name': $arWhere[] = "a.name like '%{$es_sword}%'"; break;
			}
		}
	#5. ó������
		if($_GET['regdt'][0]) {
			if(!$_GET['regdt'][1]) $_GET['regdt'][1] = date('Ymd',$now);

			$tmp_start = substr($_GET['regdt'][0],0,4).'-'.substr($_GET['regdt'][0],4,2).'-'.substr($_GET['regdt'][0],6,2);
			$tmp_end = substr($_GET['regdt'][1],0,4).'-'.substr($_GET['regdt'][1],4,2).'-'.substr($_GET['regdt'][1],6,2);

			if ((int)$_GET['regdt_time'][0] !== -1 && (int)$_GET['regdt_time'][1] !== -1) {

				$tmp_start .= ' '.sprintf('%02d',$_GET['regdt_time'][0]).':00:00';
				$tmp_end .= ' '.sprintf('%02d',$_GET['regdt_time'][1]).':59:59';
			}
			else {
				$tmp_start .= ' 00:00:00';
				$tmp_end .= ' 23:59:59';
			}

			//$arWhere[] = $db->_query_print('o.orddt between [s] and [s]',$tmp_start,$tmp_end);

			switch($_GET['dtkind']) {
				case 'orddt': $arWhere[] = $db->_query_print('o.orddt between [s] and [s]',$tmp_start,$tmp_end); break;
				case 'cs_regdt': $arWhere[] = $db->_query_print('a.regdt between [s] and [s]',$tmp_start,$tmp_end); break;
				//case 'ddt': $arWhere[] = $db->_query_print('o.ddt between [s] and [s]',$tmp_start,$tmp_end); break;
				//case 'confirmdt': $arWhere[] = $db->_query_print('o.confirmdt between [s] and [s]',$tmp_start,$tmp_end); break;
			}
		}

	#6. ��ǰ�˻�
		$join_GD_PURCHASE = '';
		if($_GET['sgword'] && $_GET['sgkey']) {
			$es_sgword = $db->_escape($_GET['sgword']);
			switch($_GET['sgkey']) {
				case 'goodsnm': $arWhere[] = "oi.goodsnm like '%{$es_sgword}%'"; break;
				case 'brandnm': $arWhere[] = "EXISTS (SELECT ordno FROM ".GD_ORDER_ITEM." AS _oi WHERE _oi.brandnm like '%{$es_sgword}%' AND _oi.ordno = o.ordno) "; break;
				case 'maker': $arWhere[] = "EXISTS (SELECT ordno FROM ".GD_ORDER_ITEM." AS _oi WHERE _oi.maker like '%{$es_sgword}%' AND _oi.ordno = o.ordno) "; break;
				case 'goodsno': $arWhere[] = "oi.goodsno like '%{$es_sgword}%'"; break;
				case 'purchase': $arWhere[] = "pch.comnm like '%{$es_sgword}%'"; $join_GD_PURCHASE = 'INNER JOIN '.GD_PURCHASE_GOODS.' AS pchg ON pchg.goodsno = oi.goodsno INNER JOIN '.GD_PURCHASE.' AS pch ON pchg.pchsno = pch.pchsno'; break;
			}
		}

	#7. ���ں�������
		if($_GET['eggyn']) {
			$arWhere[] = $db->_query_print('o.eggyn = [s]',$_GET['eggyn']);
		}

	#8. ������ ����
		$tmp_arWhere = array();

		if($_GET['escrowyn']) {
			$tmp_arWhere[] = $db->_query_print('o.escrowyn = [s]',$_GET['escrowyn']);
		}

		if($_GET['cashreceipt']) {
			$tmp_arWhere[] = 'o.cashreceipt != ""';
		}
		if($_GET['flg_coupon']) {
			$tmp_arWhere[] = 'co.ordno is not null';
			$join_GD_COUPON_ORDER='left join '.GD_COUPON_ORDER.' as co on o.ordno=co.ordno';
		}
		else {
			$join_GD_COUPON_ORDER='';
		}

		if($_GET['about_coupon_flag']=='1') {
			$tmp_arWhere[] = 'o.about_coupon_flag = "Y"';
		}

		if($_GET['pay_method_p']=='1') {
			$tmp_arWhere[] = 'o.settlekind= "p"';
		}

		if($_GET['cbyn']=='Y') {
			$tmp_arWhere[] = 'o.cbyn = "Y"';
		}

		if (sizeof($tmp_arWhere) > 0) {
			$arWhere[] = '('.implode(' OR ',$tmp_arWhere).')';
			unset($tmp_arWhere);
		}


	#9. ȫ��ä��
		if(count($_GET['chk_inflow'])) {
			$es_inflow = array();
			foreach($_GET['chk_inflow'] as $v) {
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

	#10. �������� (���� �ʵ尡 ����, inflow ���� sugi �� ���ڵ�)
		if($_GET['ord_type'] == 'offline') {
			$arWhere[] = 'o.inflow = \'sugi\'';
		}
		else if ($_GET['ord_type'] == 'online') {
			$arWhere[] = 'o.inflow <> \'sugi\'';
		}

	#xx. ����¡ query ����
		$_paging_query = http_build_query($_GET);	// php5 �����Լ�. but! lib.func.php �ȿ� php4�� ����.


	# ȯ�Ұ���
	if(strlen($_GET['bankcode']) == '1'){
		$_GET['bankcode'] = "0".$_GET['bankcode'];
	}


	if($_GET['bankcode']){
		$es_sword = $db->_escape($_GET['bankcode']);
		if($es_sword != 'all'){
			$arWhere[] = "a.bankcode = '{$es_sword}'";
		}
	}

	$arWhere[] = "oi.ordno=o.ordno";


$db_table = "
".GD_ORDER_CANCEL." a
left join ".GD_ORDER_ITEM." oi on a.sno=oi.cancel and a.ordno = oi.ordno
,".GD_ORDER." o
left join ".GD_MEMBER." d on o.m_no=d.m_no
";

$orderby = $_GET['sort'];

$pg = new Page($_GET[page],$_GET[page_num]);
$pg->field = "
a.sno,a.regdt canceldt,a.name nameCancel,a.bankcode,AES_DECRYPT(UNHEX(a.bankaccount), a.ordno) AS bankaccount,a.bankuser,
sum((oi.price-oi.memberdc-oi.coupon-oi.oi_special_discount_amount)*oi.ea)  as repay,count(*) cnt,
o.ordno,o.orddt,o.nameOrder,o.settlekind,o.pg,o.step2,o.settleprice,o.m_no,o.ncash_emoney,o.ncash_cash,a.rncash_emoney,a.rncash_cash,
o.settleprice, o.goodsprice, o.delivery, o.coupon, o.emoney, o.memberdc,o.enuri,o.eggFee,o.escrowyn,o.pgcancel,o.inflow,d.m_no,d.m_id, d.dormant_regDate,
oi.istep, oi.cyn, o.settleInflow
";
$pg->setQuery($db_table,$arWhere,$orderby,"group by a.sno");
$pg->exec();

$res = $db->query($pg->query);

// �κ������������üũ
$cardPartCancelable = false;
if (empty($cfg['settlePg']) === false) {
	include "../../lib/cardCancel.class.php";
	$cardPartCancelable = in_array('partcancel_'.$cfg['settlePg'], get_class_methods('cardCancel'));
}
?>
<script>
function cal_repay(repayfee,repay,settleprice,before_refund_amount,i){
	if(!repay) var tmp = 0;
	else var tmp = repay - repayfee;

	if(tmp < 0){
		alert('�ǰ���ݾ׺��� ȯ�Ҽ����ᰡ ū ȯ�Ұ��� �ֽ��ϴ�.');
		document.getElementsByName('repayfee[]')[i].value='<?=$cfg[minrepayfee]?>';
		return;
	}

	document.getElementsByName('repay[]')[i].value=tmp;
	document.getElementById('viewrepay'+i).innerHTML=comma(tmp)+'��';

	// ���� �Էµ� ȯ�� �ݾװ�, ȯ�� �Ϸ� �ݾ��� ���� ���� �����ݾ��� �ʰ��ϴ� ��� �ȳ� �޽��� ���
	document.getElementById('el-over-refund-message' + i).style.display = (tmp + before_refund_amount > settleprice) ? 'block' : 'none';
}
// ī����ü���
function cardSettleCancel(ordno,sno,idx){
	var obj = document.ifrmHidden;
	if(confirm('ī������� ����Ͻðڽ��ϱ�?')){
		obj.location.href = "cardCancel.php?ordno="+ordno+"&sno="+sno+"&idx="+idx;
		document.getElementById("canceltype"+idx).innerHTML="<img src='../img/ajax-loader.gif' />";
	}
}
// ī��κ����
function cardPartCancel(idx) {
	var ordno = document.getElementsByName('ordno[]')[idx].value;
	var sno = document.getElementsByName('sno[]')[idx].value;
	var lastRepay = document.getElementsByName('repay[]')[idx].value;
	var repayfee = document.getElementsByName('repayfee[]')[idx].value;
	var repay = parseInt(lastRepay) + parseInt(repayfee);
	popupLayer('./cardPartCancel.php?ordno='+ordno+'&sno='+sno+'&repay='+repay+'&lastRepay='+lastRepay,600,300);
}

//������ ī����ü/�κ�, �ڵ��� �������
function paycoCancel(idx,part,vbank) {
	var ordno = document.getElementsByName('ordno[]')[idx].value;
	var sno = document.getElementsByName('sno[]')[idx].value;
	var lastRepay = document.getElementsByName('repay[]')[idx].value;//ȯ�ҿ����ݾ�

	if(document.getElementsByName('repayfee[]')[idx]) var repayfee = document.getElementsByName('repayfee[]')[idx].value;//ȯ�Ҽ�����
	else var repayfee = 0;
	var repay = parseInt(lastRepay) + parseInt(repayfee);//���� ȯ�ұݾ�
	var remoney = document.getElementsByName('remoney[]')[idx].value;//������ ������

	if(vbank) {
		if(part == 'Y') file = 'paycoPartCancelVbank.php';//������� ���ó��
		else file = 'paycoCancelVbank.php';//������� ���ó��
	}
	else file = 'paycoCancel.php';//�ſ�ī��,����������Ʈ,�޴�������,������ü ���ó��

	popupLayer("./"+file+"?ordno="+ordno+"&sno="+sno+"&part="+part+"&repay="+repay+"&lastRepay="+lastRepay+"&repayfee="+repayfee+"&remoney="+remoney,600,400);
}
</script>
<div class="title title_top">ȯ�� ����Ʈ <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=order&no=33')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<form name="frmSearch" id="frmSearch" method="get" action="">
	<input type="hidden" name="mode" value="<?=$_GET['mode']?>"/>	<!-- �ֹ��� or �ֹ�ó���帧 -->

	<table class="tb">
	<col class="cellC"><col class="cellL" style="width:250px">
	<col class="cellC"><col class="cellL">
	<tr>
		<td><span class="small1">�ֹ��˻�</span></td>
		<td colspan="3">

			<select>
				<option>ȯ�Ұ���</option>
			</select>

			<select name="settlekind">
				<option value=""> = �������� = </option>
				<? foreach ($integrate_cfg['pay_method'] as $k=>$v) { ?>
				<? if ($k == 'p') continue; ?>
				<option value="<?=$k?>" <?=$_GET['settlekind'] == $k ? 'selected' : ''?>><?=$v?></option>
				<? } ?>
				<option value="payco" <?=$_GET['settlekind'] == 'payco' ? 'selected' : ''?>>������</option>
			</select>

			<select name="ord_type">
				<option value=""> = �������� = </option>
				<option value="online" <?=$_GET['ord_type'] == 'online' ? 'selected' : ''?>>�¶�������</option>
				<option value="offline" <?=$_GET['ord_type'] == 'offline' ? 'selected' : ''?>>��������</option>
			</select>

			<select name="bankcode">
			<option value="all"> = ȯ�Ұ��� ���� = </option>
			<? foreach ( $r_bank as $k=>$v){ ?>
			<option value="<?=$k?>"<?if(trim($k)==$_GET[bankcode])echo" selected";?>><?=$v?>
			<? } ?>
			</select>

			<select name="skey">
				<option value="all"> = ���հ˻� = </option>
				<option value="ordno"			<?=($_GET['skey'] == 'ordno') ? 'selected' : ''?>			>�ֹ���ȣ</option>
				<option value="nameOrder"		<?=($_GET['skey'] == 'nameOrder') ? 'selected' : ''?>		>�ֹ��ڸ�</option>
				<option value="m_id"			<?=($_GET['skey'] == 'm_id') ? 'selected' : ''?>			>�ֹ���ID</option>
				<option value="phoneOrder"			<?=($_GET['skey'] == 'phoneOrder') ? 'selected' : ''?>			>�ֹ��ڿ���ó</option>
				<option value="bankSender"		<?=($_GET['skey'] == 'bankSender') ? 'selected' : ''?>	>�Ա��ڸ�</option>
				<option value="nameReceiver"	<?=($_GET['skey'] == 'nameReceiver') ? 'selected' : ''?>	>�����ڸ�</option>
				<option value="phoneReceiver"	<?=($_GET['skey'] == 'phoneReceiver') ? 'selected' : ''?>	>�����ڿ���ó</option>
				<option value="address"	<?=($_GET['skey'] == 'address') ? 'selected' : ''?>	>������ּ�</option>
				<option value="deliverycode"	<?=($_GET['skey'] == 'deliverycode') ? 'selected' : ''?>	>�����ȣ</option>
				<option value="name"	<?=($_GET['skey'] == 'name') ? 'selected' : ''?>	>ó�������</option>

			</select>

			<input type="text" name="sword" value="<?=htmlspecialchars($_GET['sword'])?>" class="line" />

		</td>
	</tr>
	<tr>
		<td><span class="small1">ó������</span></td>
		<td colspan="3">

			<select name="dtkind">
				<option value="orddt"		<?=($_GET['dtkind'] == 'orddt' ? 'selected' : '')?>		>�ֹ���</option>
				<option value="cs_regdt"			<?=($_GET['dtkind'] == 'cs_regdt' ? 'selected' : '')?>			>ȯ�ҿ�û��</option>
				<!--option value="ddt"			<?=($_GET['dtkind'] == 'ddt' ? 'selected' : '')?>			>�����</option>
				<option value="confirmdt"	<?=($_GET['dtkind'] == 'confirmdt' ? 'selected' : '')?>	>��ۿϷ���</option-->
			</select>

			<input type="text" name="regdt[]" value="<?=$_GET['regdt'][0]?>" onclick="calendar(event)" size="12" class="line"/>

			<select name="regdt_time[]">
			<option value="-1">---</option>
			<? for ($i=0;$i<24;$i++) {?>
			<option value="<?=$i?>" <?=($_GET['regdt_time'][0] === $i ? 'selected' : '')?>><?=sprintf('%02d',$i)?>��</option>
			<? } ?>
			</select>
			-
			<input type="text" name="regdt[]" value="<?=$_GET['regdt'][1]?>" onclick="calendar(event)" size="12" class="line"/>
			<select name="regdt_time[]">
			<option value="-1">---</option>
			<? for ($i=0;$i<24;$i++) {?>
			<option value="<?=$i?>" <?=($_GET['regdt_time'][1] === $i ? 'selected' : '')?>><?=sprintf('%02d',$i)?>��</option>
			<? } ?>
			</select>

			<a href="javascript:setDate('regdt[]',<?=date("Ymd")?>,<?=date("Ymd")?>)"><img src="../img/sicon_today.gif" align="absmiddle"/></a>
			<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-7 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_week.gif" align="absmiddle"/></a>
			<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-15 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twoweek.gif" align="absmiddle"/></a>
			<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-1 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_month.gif" align="absmiddle"/></a>
			<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-2 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twomonth.gif" align="absmiddle"/></a>
			<a href="javascript:setDate('regdt[]')"><img src="../img/sicon_all.gif" align="absmiddle"/></a>

		</td>
	</tr>
	<tr class="blindable">
		<td><span class="small1">��ǰ�˻�</span></td>
		<td>
			<select name="sgkey">
				<option value="goodsnm"	<?=($_GET['sgkey'] == 'goodsnm') ? 'selected' : ''?>	>��ǰ��</option>
				<option value="goodsno"	<?=($_GET['sgkey'] == 'goodsno') ? 'selected' : ''?>	>��ǰ��ȣ</option>
				<option value="brandnm"	<?=($_GET['sgkey'] == 'brandnm') ? 'selected' : ''?>	>�귣��</option>
				<option value="maker"	<?=($_GET['sgkey'] == 'maker') ? 'selected' : ''?>		>������</option>
				<option value="purchase"	<?=($_GET['sgkey'] == 'purchase') ? 'selected' : ''?>	>����ó(����ó)</option>
			</select>
			<input type=text name="sgword" value="<?=htmlspecialchars($_GET['sgword'])?>" class="line"/>
		</td>
		<td><span class="small1">���ں�������</span> <a href="../basic/egg.intro.php"><img src="../img/btn_question.gif"/></a></td>
		<td class="noline">
			<select name="eggyn">
				<option value=""	<?=($_GET['eggyn'] == '') ? 'selected' : ''?>	>��ü</option>
				<option value="n"	<?=($_GET['eggyn'] == 'n') ? 'selected' : ''?>>�̹߱�</option>
				<option value="f"	<?=($_GET['eggyn'] == 'f') ? 'selected' : ''?>>�߱޽���</option>
				<option value="y"	<?=($_GET['eggyn'] == 'y') ? 'selected' : ''?>>�߱޿Ϸ�</option>
			</select>
		</td>
	</tr>
	<tr class="blindable">
		<td><span class="small1">����������</span></td>
		<td colspan="3" class="noline">
			<input type="checkbox" name="escrowyn" value="y" <?=frmChecked('y',$_GET['escrowyn'])?>>����ũ�� <img src="../img/btn_escrow.gif" align="absmiddle"/></input>
			<input type="checkbox" name="cashreceipt" value="1" <?=frmChecked('1',$_GET['cashreceipt'])?>>���ݿ����� <img src="../img/icon_cash_receipt.gif"/></input>
			<input type="checkbox" name="flg_coupon" value="1" <?=frmChecked('1',$_GET['flg_coupon'])?>>�������</input>
			<input type="checkbox" name="about_coupon_flag" value="1" <?=frmChecked('1',$_GET['about_coupon_flag'])?>>��ٿ�����</input>
			<input type="checkbox" name="pay_method_p" value="1" <?=frmChecked('1',$_GET['pay_method_p'])?>>������(����Ʈ)</input>
			<input type="checkbox" name="cbyn" value="Y" <?=frmChecked('Y',$_GET['cbyn'])?>><img src="../img/icon_okcashbag.gif" align="absmiddle"/>OKĳ�ù�����</input>
		</td>
	</tr>
	<tr class="blindable">
		<td><span class="small1">ȫ��ä��<br>(���԰��)</span></td>
		<td colspan="3" class="noline">
			<? foreach ($integrate_cfg['inflows'] as $k=>$v) { ?>
			<label class="small1"><input type="checkbox" name="chk_inflow[]" value="<?=$k?>" <?=(in_array($k,$_GET['chk_inflow']) ? 'checked' : '')?> /><img src="../img/inflow_<?=$k?>.gif" align="absmiddle"/> <?=$v?></label>
			<? } ?>
		</td>
	</tr>
	</table>

	<div class="button_top">
	<table width="100%">
	<tr>
		<td width="35%" align="left">&nbsp;</td>
		<td width="30%" align="center"><input type="image" src="../img/btn_search2.gif"/></td>
		<td width="35%" align="right">
		<a href="javascript:void(0);" onClick="nsGodoFormHelper.save();"><img src="../img/btn_search_form_save.gif"></a>
		<a href="javascript:void(0);" onClick="nsGodoFormHelper.reset();"><img src="../img/btn_search_form_reset.gif"></a>
		<a href="javascript:void(0);" onClick="nsGodoFormHelper.toggle();"><img src="../img/btn_search_form_toggle_open.gif" id="el-godo-form-helper-toggle-btn"></a>
		</td>
	</tr>
	</table>
	</div>

	<div style="padding-top:15px"></div>

	<table width="100%" cellpadding="0" cellspacing="0">
	<tr>
		<td align="left">
		<a href="?<?=$_paging_query?>&ord_status="><img src="../img/btn_list_order_list<?=empty($_GET['ord_status']) ? '_on' : '' ?>.gif"></a>
		<a href="?<?=$_paging_query?>&ord_status=20"><img src="../img/btn_int_order_list_refund_req<?=($_GET['ord_status'] == 20) ? '_on' : ''?>.gif"></a>
		<a href="?<?=$_paging_query?>&ord_status=21"><img src="../img/btn_int_order_list_refund_fin<?=($_GET['ord_status'] == 21) ? '_on' : ''?>.gif"></a>
		</td>

		<td align="right">
		<select name="sort" onchange="this.form.submit();">
			<option value="a.regdt desc" <?=$_GET['sort'] == 'a.regdt desc' ? 'selected' : '' ?>> ����ϼ���</option>
			<option value="a.regdt asc" <?=$_GET['sort'] == 'a.regdt asc' ? 'selected' : '' ?>>����ϼ���</option>

			<option value="o.orddt desc" <?=$_GET['sort'] == 'o.orddt desc' ? 'selected' : '' ?>>�ֹ��ϼ���</option>
			<option value="o.orddt asc" <?=$_GET['sort'] == 'o.orddt asc' ? 'selected' : '' ?>>�ֹ��ϼ���</option>

			<option value="o.cdt desc" <?=$_GET['sort'] == 'o.cdt desc' ? 'selected' : '' ?>>�Ա��ϼ���</option>
			<option value="o.cdt asc" <?=$_GET['sort'] == 'o.cdt asc' ? 'selected' : '' ?>>�Ա��ϼ���</option>

			<option value="o.settleprice desc" <?=$_GET['sort'] == 'o.settleprice desc' ? 'selected' : '' ?>>�����׼���</option>
			<option value="o.settleprice asc" <?=$_GET['sort'] == 'o.settleprice asc' ? 'selected' : '' ?>>�����׼���</option>
		</select>&nbsp;

		<select name="page_num" onchange="this.form.submit();">
			<?
			$r_pagenum = array(10,20,40,60,100);
			if ((int)$cfg['orderPageNum'] > 0 && !in_array((int)$cfg['orderPageNum'] ,$r_pagenum)) {
				$r_pagenum[] = (int)$cfg['orderPageNum'];
				sort($r_pagenum);
			}
			foreach ($r_pagenum as $v){
			?>
			<option value="<?=$v?>" <?=$_GET['page_num'] == $v ? 'selected' : ''?>><?=$v?>�� ���</option>
			<? } ?>
		</select>
		</td>
	</tr>
	</table>
</form>

<form method=post action="indb.php">
<input type=hidden name=mode value="repay">

<table width=100% cellpadding=2 cellspacing=0>
<tr><td class=rnd colspan=10></td></tr>
<tr class=rndbg>
	<th><a href="javascript:chkBox(document.getElementsByName('chk[]'),'rev')" class=white>����</a></th>
	<th><font class=small1><b>�ֹ���</th>
	<th><font class=small1><b>�ֹ������</th>
	<th><font class=small1><b>�ֹ���ȣ</th>
	<th><font class=small1><b>ȫ��ä��</th>
	<th><font class=small1><b>�ֹ���</th>
	<th><font class=small1><b>ó����</th>
	<th><font class=small1><b>��Ҽ���/�ֹ�����</th>
	<th><font class=small1><b>��������</th>

</tr>
<col align=center span=10>
<?
$i=0;
while ($data=$db->fetch($res)){
	//'repayfee' => '10', 'minrepayfee' => '5000', 'minpos' => '4',
	$repay=0; $pemoney=0;$tot = 0;

	list($cnt,$ccnt,$pcnt) = $db->fetch("
	select count(*)
		,ifnull(sum(case when cancel != '' && cancel <= '$data[sno]' then 1 end),'0') as ccnt
	FROM ".GD_ORDER_ITEM." WHERE ordno=$data[ordno]");

	// ��ҵ� ���̹� ���ϸ����� ĳ���� �ִ°�� ȯ�ұݿ��� ����
	if((int)$data['rncash_emoney'] || (int)$data['rncash_cash'])
	{
		$data['repay'] -= $data['rncash_emoney'] + $data['rncash_cash'];
	}

	$total_use_naver_mileage = $data['rncash_emoney']+$data['ncash_emoney'];
	$total_use_naver_cash = $data['rncash_cash']+$data['ncash_cash'];

	// % ����
	list($data[percentCoupon], $data[special_discount]) = $db->fetch("select sum(coupon * ea), sum(oi_special_discount_amount * ea) from gd_order_item where ordno = '$data[ordno]'");

	if($data[settleprice] >= $data[repay]){
		$repay = $data[repay];
		$repaymsg = "��ǰ�����ܰ�";
		if($ccnt == $cnt){
			$repaymsg = "��ǰ�����ܰ� + ��۷� - ������ - ������ - ���� - ��ǰ�� ���� + �������������";
			$repay = $repay + $data[delivery] - $data[enuri] - $data[emoney] - $data['ncash_emoney'] - $data['ncash_cash'] - ($data[coupon] - $data[percentCoupon]) + $data[eggFee];


		}
		if((int)$total_use_naver_mileage) $repaymsg .= " - ���̹����ϸ���";
		if((int)$total_use_naver_cash) $repaymsg .= " - ���̹�ĳ��";
	}else $repay = $data[settleprice];

	if($data['settleInflow'] == 'payco') {
		$orderDeliveryItem = &load_class('orderDeliveryItem','orderDeliveryItem');
		$orderDeliveryItem->ordno = $data['ordno'];//�ݺ������� �ֹ���ȣ�� ����� �� �־� ���� ����

		if($data['istep'] !== "44") {
			$cancel_delivery = $orderDeliveryItem->cancel_delivery($data['sno']);

			$repay = $cancel_delivery['total_cancel_price'] + $cancel_delivery['total_cancel_delivery_price'];
			$repaymsg = '��ǰ�����ܰ� + ��۷� - ��ǰ�� ����';

			if($orderDeliveryItem->checkLastCancel($data['sno']) === true) {
				//������ ��Ұ��� ��� ��������, ������ ���� ����
				$repay -= ($cancel_delivery['coupon']['m'] - $cancel_delivery['coupon']['f']) + $cancel_delivery['emoney'];
				$repaymsg = '��ǰ�����ܰ� + ��۷� - ������ - ������ - ���� - ��ǰ�� ����';
			}
		}
		else {
			$cancel_delivery = $orderDeliveryItem->getCancelCompletedDeliverFeeWithSno($data['sno'], true);
		}
	}

	if($data[cnt] == $cnt) $repaymsg = "�� �����ݾ�";
	if($repay < 0) $repay = 0;
	$repayfee = getRepayFee($repay);
	if($data[settleprice] < $data[repay]) $remoney = $data[repay] - $repay;
?>
<tr><td colspan=10 class=rndline></td></tr>
<tr>
	<td class=noline><input type=checkbox name=chk[] value="<?=$i?>" <?=$data[istep] == 44 ? 'disabled' : ''?>></td>
	<td><font class=ver71 color=444444><?=$data[orddt]?></td>
	<td><font class=ver71 color=444444><?=$data[canceldt]?></td>
	<td><a href="javascript:popup('popup.order.php?ordno=<?=$data[ordno]?>',800,600)"><font class=ver71 color=<?=$data['inflow'] == 'sugi' ? 'ED6C0A' : '0074BA'?>><b><?=$data[ordno]?><?=$data['inflow'] == 'sugi' ? '<span class="small1">(����)</span>' : ''?></a></td>
	<td><? if ($data['inflow']!="" && $data['inflow']!="sugi"){ ?><img src="../img/inflow_<?=$data['inflow']?>.gif" align="absmiddle" alt="<?=$integrate_cfg['inflows'][$data['inflow']]?>" /><? } ?></td>
	<td>
		<?php if($data[m_id]){ ?>
			<?php if($data['dormant_regDate'] == '0000-00-00 00:00:00'){ ?>
				<span id="navig" name="navig" m_id="<?=$data['m_id']?>" m_no="<?=$data['m_no']?>"><span class="small1" style="color:#0074BA;"><?php echo $data['nameOrder']; ?></span></span>
			<?php } else { ?>
				<span class="small1" style="color:#0074BA;"><?php echo $data['nameOrder']; ?> (�޸�ȸ��)</span>
			<?php } ?>
		<?php } else { ?>
			<span class="small1"><?=$data[nameOrder]?></span>
		<?php } ?>
	</td>
	<td><font class=small1 color=444444><?=$data[nameCancel]?></td>
	<td><font class=ver7 color=444444><?=$data[cnt]?>/<?=$cnt?></td>
	<td><font class=small1 color=444444><?=settleIcon($data['settleInflow']);?><?=$r_settlekind[$data[settlekind]]?></td>
</tr>
<tr>
	<td colspan=10 style="padding:5px 10px" align=left>
	<table width=100% border=1 bordercolor=#dedede style="border-collapse:collapse">
		<tr bgcolor=#f7f7f7 height=20>
			<th width=14%><font class=small1 color=444444><b>�ֹ��ݾ�</th>
			<th width=14% nowrap><font class=small1 color=444444><b>��۷�</th>
			<th width=14% nowrap><font class=small1 color=444444><b>��ǰ����</th>
			<th width=14% nowrap><font class=small1 color=444444><b>ȸ������</th>
			<? if ($total_use_naver_mileage > 0){ ?><th width=14% nowrap><font class=small1 color=444444><b>���̹����ϸ������</th><?}?>
			<? if ($total_use_naver_cash > 0){ ?><th width=14% nowrap><font class=small1 color=444444><b>���̹�ĳ�����</th><?}?>
			<th width=14% nowrap><font class=small1 color=444444><b>������</th>
			<th width=14% nowrap><font class=small1 color=444444><b>����</th>
			<th width=14% nowrap><font class=small1 color=444444><b>������ ����� ������</th>
			<th width=14% nowrap><font class=small1 color=444444><b>�������������</th>
			<th width=16% nowrap><font class=small1 color=444444><b>�� �����ݾ�</th>
		</tr>
		<col align=center span=10>
		<tr>
			<td><font class=ver7 color=444444><?=number_format($data[goodsprice])?>��</td>
			<td><font class=ver7 color=444444><?=number_format($data[delivery])?>��</td>
			<td><font class=ver7 color=444444><?=number_format($data[special_discount])?>��</td>
			<td><font class=ver7 color=444444><?=number_format($data[memberdc])?>��</td>
			<? if ($total_use_naver_mileage > 0){ ?><td><font class=ver7 color=444444><?=number_format($total_use_naver_mileage)?>��</td><?}?>
			<? if ($total_use_naver_cash > 0){ ?><td><font class=ver7 color=444444><?=number_format($total_use_naver_cash)?>��</td><?}?>
			<td><font class=ver7 color=444444><?=number_format($data[enuri])?>��</td>
			<td><font class=ver7 color=444444><?=number_format($data[coupon])?>�� (%���� <?=number_format($data[percentCoupon])?>�� + �ݾ����� <?=number_format($data[coupon] - $data[percentCoupon])?>��)</td>
			<td><font class=ver7 color=444444><?=number_format($data[emoney])?>��</td>
			<td><font class=ver7 color=444444><?=number_format($data[eggFee])?>��</td>
			<td><font class=ver7 color=444444><?=number_format($data[settleprice])?>��</td>
		</tr>
	</table>
	</td>
</tr>
<tr><td colspan=10 style="height:20px;">
<div style="height:5px;text-align:center;border-bottom:1px dotted #4A7EBB;margin-bottom:5px;">
	<span style="display:inline-block;position:relative;top:8px;background:#fff;padding:0 5px;color:#627dce;">ȯ �� �� ��</span>
</div></td></tr>
<tr>
	<td colspan=10 style="padding:5px 10px" align=left>
	<table width=100% border=1 bordercolor=#dedede style="border-collapse:collapse">
	<tr bgcolor=#f7f7f7 height=20>
		<th><font class=small1 color=444444><b>��ǰ��</th>
		<th width=80 nowrap><font class=small1 color=444444><b>�ǸŰ���</th>
		<th width=80 nowrap><font class=small1 color=444444><b>��ǰ����</th>
		<th width=80 nowrap><font class=small1 color=444444><b>ȸ������</th>
		<th width=80 nowrap><font class=small1 color=444444><b>��������</th>
		<th width=80 nowrap><font class=small1 color=444444><b>��ǰ�����ܰ�</th>
		<th width=50 nowrap><font class=small1 color=444444><b>����</th>
		<?if($data['settleInflow'] == 'payco') {?><th width=80 nowrap><font class=small1 color=444444><b>��ۺ�</th><?}?>
	</tr>
	<col><col align=center span=10>
	<?
	if($data['settleInflow'] == 'payco') {
		$query = "
		select b.*,a.*,tg.tgsno from
			".GD_ORDER_ITEM." a
			left join ".GD_GOODS." b on a.goodsno=b.goodsno
			left join ".GD_TODAYSHOP_GOODS." tg on a.goodsno=tg.goodsno
			left join ".GD_ORDER_ITEM_DELIVERY." oid on a.oi_delivery_idx=oid.oi_delivery_idx
		where
			a.cancel='$data[sno]'
			and a.ordno='$data[ordno]'
		order by oid.delivery_type, a.goodsno
		";
	}
	else {
		$query = "
		select b.*,a.*,tg.tgsno from
			".GD_ORDER_ITEM." a
			left join ".GD_GOODS." b on a.goodsno=b.goodsno
			left join ".GD_TODAYSHOP_GOODS." tg on a.goodsno=tg.goodsno
		where
			a.cancel='$data[sno]'
			and a.ordno='$data[ordno]'
		";
	}
	$res2 = $db->query($query);
	while ($item=$db->fetch($res2)){
	?>
	<tr>
		<td>
		<table>
		<tr>
			<td style="padding-left:3px"><a href="<?=($item['tgsno'])? '../../todayshop/today_goods.php?tgsno='.$item['tgsno'] : '../../goods/goods_view.php?goodsno='.$item[goodsno]?>" target=_blank><font class=small color=0074BA>
			<?=$item[goodsnm]?>
			<? if ($item[opt1]){ ?>[<?=$item[opt1]?><? if ($item[opt2]){ ?>/<?=$item[opt2]?><? } ?>]<? } ?>
			<? if ($item[addopt]){ ?><div>[<?=str_replace("^","] [",$item[addopt])?>]</div><? } ?><font class=small1 color=0074BA><b>[����]</b></font></a>
			</td>
		</tr>
		</table>
		</td>
		<td><font class=ver7 color=444444><?=number_format($item[price])?></td>
		<td><font class=ver7 color=444444><?=number_format($item[oi_special_discount_amount])?></td>
		<td><font class=ver7 color=444444><?=number_format($item[memberdc])?></td>
		<td><font class=ver7 color=444444><?=number_format($item[coupon])?></td>
		<td><font class=ver7 color=0074BA><b><?=number_format($item[price]-$item[memberdc]-$item[coupon]-$item[oi_special_discount_amount])?></td>
		<td><font class=ver7 color=444444><?=number_format($item[ea])?></td>
		<?if($data['settleInflow'] == 'payco') {?>
			<?if($item['delivery_type'] == '0' || $item['delivery_type'] == '1') {?>
				<?if(isset($cancel_delivery['view'][$item['delivery_type']])) {?>
			<td rowspan="<?=$cancel_delivery['view'][$item['delivery_type']]['cnt']?>">
				<font class=ver7 color=0074BA>
					<b><?=number_format($cancel_delivery['view'][$item['delivery_type']]['area_delivery_price'] + $cancel_delivery['view'][$item['delivery_type']]['delivery_price'])?></b>
				</font>
			</td>
				<?unset($cancel_delivery['view'][$item['delivery_type']]);?>
				<?}?>
			<?} else if($item['delivery_type'] == '4') {?>
				<?if(isset($cancel_delivery['view'][$item['delivery_type']][$item['goodsno']])) {?>
			<td rowspan="<?=$cancel_delivery['view'][$item['delivery_type']][$item['goodsno']]['cnt']?>">
				<font class=ver7 color=0074BA>
					<b><?=number_format($cancel_delivery['view'][$item['delivery_type']][$item['goodsno']]['area_delivery_price'] + $cancel_delivery['view'][$item['delivery_type']][$item['goodsno']]['delivery_price'])?></b>
				</font>
			</td>
				<?unset($cancel_delivery['view'][$item['delivery_type']][$item['goodsno']]);?>
				<?}?>
			<?} else if($item['delivery_type'] == '5') {?>
			<td><font class=ver7 color=0074BA><b><?=number_format($cancel_delivery['view'][$item['delivery_type']][$item['optno']]['area_delivery_price'] + $cancel_delivery['view'][$item['delivery_type']][$item['optno']]['delivery_price'])?></b></font></td>
			<?} else {?>
			<td><font class=ver7 color=0074BA><b><?=number_format($cancel_delivery['view'][$item['delivery_type']][$item['goodsno']]['delivery_price'])?></font></b></td>
			<?}?>
		<?}?>
	</tr>
	<? } ?>
	</table>
	<?
	$data[bankcode] = sprintf("%02d",$data[bankcode]);
	?>
    <div style="padding-top:3px;"></div>
	<? if ($data['istep'] == 44 && $data['cyn'] == 'r') { ?>
		<div align=center>
			<b>ȯ�� ���� :</b>
			<? foreach ( $r_bank as $k=>$v){  if(trim($k)==$data[bankcode]) echo $v; } ?>
			<?=$data[bankaccount]?>
			&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; ������ :  <?=$data[bankuser]?>
		</div>
	<? } else { ?>
		<div align=center><b>ȯ�� ���� : </b><select name=bankcode[] required><option value="">==����==
			<? foreach ( $r_bank as $k=>$v){ ?>
			<option value="<?=$k?>"<?if(trim($k)==$data[bankcode])echo" selected";?>><?=$v?>
			<? } ?>
			</select>
			<input type=text name='bankaccount[]' value='<?=$data[bankaccount]?>'>
			<font class=ver71 color=444444>������</font> <input type=text name='bankuser[]' value='<?=$data[bankuser]?>'>
		</div>
	<? } ?>
    <div style="padding-top:3px"></div>
	<table width=100% border=1 bordercolor=#dedede style="border-collapse:collapse">
	<tr bgcolor=#f7f7f7 height=20>
		<th width=25% nowrap><font class=small1 color=444444><b>ȯ�ҿ����ݾ�(<?=$repaymsg?>)</th>
		<th width=25% nowrap><font class=small1 color=444444><b>ȯ�Ҽ�����</b> <a href="javascript:popupLayer('../basic/popup.emoney.php',600,300)"><img src="../img/btn_repay_price.gif" border=0></a></th>
		<th width=25% nowrap><font class=small1 color=444444><b>���� ȯ�ұݾ�</b> ( = �ǰ����ݾ� - ȯ�Ҽ�����)</th>
		<th width=25% nowrap>���ó��</th>
	</tr>
	<col><col align=center span=10>
	<?
	if($repay-$repayfee < 0) $pre = 0;
	else $pre = $repay-$repayfee;

	## �κ���� ����
	$query = "select rprice, rfee, pgcancel from ".GD_ORDER_CANCEL." where sno = '".$data['sno']."'";
	$cancel_info = $db->fetch($query);

	if($cancel_info['pgcancel'] == 'r'){
		$repayfee = $cancel_info['rfee'];
	} else if($cancel_info['pgcancel'] == 'y'){
		$repayfee = 0;
	}

	if($cancel_info['pgcancel'] != 'n'){
		$readonly['repayfee'][$data['sno']] = "readonly";
	}

	$query = "select sum(remoney), sum(rprice) from ".GD_ORDER_CANCEL."  where ordno='$data[ordno]' and sno != '".$data['sno']."'";
	list($agoemoney, $before_refund_amount) = $db->fetch($query);
	$before_refund_amount = intval($before_refund_amount);
	?>
	<input type=hidden name='m_no[]' style='background:#e3e3e3' value='<?=$data[m_no]?>' readonly>
	<input type=hidden name='sno[]' style='background:#e3e3e3' value='<?=$data[sno]?>' readonly>
	<input type=hidden name='ordno[]' style='background:#e3e3e3' value='<?=$data[ordno]?>' readonly>
	<tr>
		<td align=center><font class=ver7 color=0074BA><b><?=number_format($repay)?>��</b></td>
		<? if ($data['istep'] == 44 && $data['cyn'] == 'r') { ?>
		<td><font class=ver7 color=424242><?=number_format($cancel_info['rfee'])?>��</td>
		<td bgcolor=E9FFB3><input type=hidden name='repay[]' style='background:#DEFD33' value='<?=$cancel_info['rprice']?>' style='text-align=right' readonly><div style="font-weight:bold;color:#FD3C00;" id='viewrepay<?=$i?>'><?=number_format($cancel_info['rprice'])?>��</div></td>
		<? } else { ?>
		<td><font class=ver7 color=424242><input type=text name='repayfee[]' class=noline value='<?=$repayfee?>' <?=$readonly['repayfee'][$data['sno']]?> onchange="cal_repay(this.value,<?=$repay?>,<?=$data['settleprice']?>,<?=$before_refund_amount?>,<?=$i?>)" onkeydown="onlynumber()" style='text-align=right;background:#E9FFB3'>��</td>
		<td bgcolor=E9FFB3><input type=hidden name='repay[]' style='background:#DEFD33' value='' style='text-align=right' readonly><div style="font-weight:bold;color:#FD3C00;" id='viewrepay<?=$i?>'></div></td>
		<? } ?>
		<td bgcolor=E9FFB3>
		<span id="canceltype<?=$i?>">
		<?
			### ���ó�� �κ�
			if($data['settlekind'] == 'c' || $data['settlekind'] == 'u' || ($data['settlekind'] == 'e' && $data['settleInflow'] == 'payco') ){		// ī�����/������ �϶��� ��� �κ� ���
				if( $repay == $data['settleprice'] ){	// ��ü��� ( �����ݾװ� ȯ�ұݾ��� ���� �� )
					if( $data['pgcancel'] == 'y' ){
						echo "<strong>ī��������</strong>�� �ֹ��Դϴ�";
						if ($data['istep'] != 44 && $data['cyn'] != 'r') {
							echo "<br/>ȯ�ҿϷ�ó�����ֽñ�ٶ��ϴ�.";
						}
					} else if ($data['pgcancel'] == 'r' && (int)$cancel_info['rfee'] > 0) {
						echo "<strong style='color: #ff0000;'>ȯ�Ҽ�����</strong>�� �����ϰ� <strong>ī��������</strong>�� �ֹ��Դϴ�.<br/>ȯ�ҿϷ�ó�����ֽñ�ٶ��ϴ�.";
					}else{
						if($data['pg'] == 'payco') echo "<a href=\"javascript:paycoCancel(".$i.", 'N', 0)\"><img src='../img/payco_cancel_btn.gif' ></a>";//������ ���� ��ü���(ī��/������ü)
						else echo "<a href=\"javascript:cardSettleCancel('".$data[ordno]."','".$data[sno]."',".$i.")\"><img src='../img/cardcancel_btn.gif'></a>";
					}
				}else{									// �κ����
					if($cancel_info['pgcancel'] != 'r' && $cardPartCancelable === false && $data['settleInflow'] != 'payco'){
						echo 'ī��κ���� �����ȵ�';
					}else if($cancel_info['pgcancel'] != 'r'){	// �̹� �κ���ҵ� ���� �ƴ� ��
						if($data['settleInflow'] == 'payco') {
							echo "<a href=\"javascript:paycoCancel(".$i.", 'Y', 0)\"><img src='../img/payco_partcancel_btn.gif'></a>";//������ ���� �κ����(ī��/������ü)
						}
						else if( $cfg['settlePg'] == 'inicis' && $data['escrowyn'] != 'n' ){	// �̴Ͻý��� �� ����ũ�� �������� ����
							echo "�̴Ͻý� ����ũ�� �κ���ҺҰ�";
						}
						else if ($cfg['settlePg'] == 'lgdacom' && $data['settlekind'] == 'u') {// ���÷��� �߱�ī�� ���� ����
							echo "LG U+ CUP ���� �κ���ҺҰ�";
						}
						else{
							echo "<a href=\"javascript:cardPartCancel(".$i.")\"><img src='../img/cardpartcancel_btn.gif'></a>";
						}
					}else{
						if($data['istep'] !== "44") {
							if ((int)$cancel_info['rfee'] > 0) {
								echo "<strong style='color: #ff0000;'>ȯ�Ҽ�����</strong>�� �����ϰ� <strong>�κ����</strong>�� �ֹ��Դϴ�.<br/>ȯ�ҿϷ�ó�����ֽñ�ٶ��ϴ�.";
							}
							else {
								echo "<strong>�κ����</strong>�� �ֹ��Դϴ�.<br/>ȯ�ҿϷ�ó�����ֽñ�ٶ��ϴ�.";
							}
						}
					}
				}
			}
			else if ($data['settlekind'] == 'h' && in_array($data['pg'], array('mobilians', 'payco', 'danal'))) {
				if ($repay == $data['settleprice']) {
					if ($data['pgcancel'] == 'y') {
						echo "<strong>�������</strong>�� �ֹ��Դϴ�.";
						if ($data['istep'] != 44 && $data['cyn'] != 'r') {
							echo "<br/>ȯ�ҿϷ�ó�����ֽñ�ٶ��ϴ�.";
						}
					}
					else {
						if($data['settleInflow'] == 'payco') {//������ �ڵ��� ���� ���
							if(substr($data['cdt'], 5, 2) == date('m')) echo "<a href=\"javascript:paycoCancel(".$i.", 'N', 0)\"><img src='../img/payco_cancel_btn.gif'></a>";//������ ���� ���(�޴���)
							else echo '�������� ���� �޴��� �������� ��Ұ� �Ұ��� �մϴ�.';
						}
						else if ($data['pg'] === 'danal') {
							echo '<img src="../img/payment_cancel_btn.jpg" onclick="ifrmHidden.location.href=\''.$cfg['rootDir'].'/order/card/danal/card_cancel.php?ordno='.$data['ordno'].'\';" style="cursor: pointer;"/>';
						}
						else {
							echo '<img src="../img/payment_cancel_btn.jpg" onclick="ifrmHidden.location.href=\''.$cfg['rootDir'].'/order/card/mobilians/card_cancel.php?ordno='.$data['ordno'].'\';" style="cursor: pointer;"/>';
						}
					}
				}
				else {
					if($data['settleInflow'] == 'payco') {
						if($cancel_info['pgcancel'] != 'r') echo "<a href=\"javascript:paycoCancel(".$i.", 'Y', 0)\"><img src='../img/payco_partcancel_btn.gif'></a>";//������ ���� �κ����(�޴���)
						else echo "<strong>�κ����</strong>�� �ֹ��Դϴ�.<br/>ȯ�ҿϷ�ó�����ֽñ�ٶ��ϴ�.";
					}
					else echo '�޴��� ����������<br/>�κ���Ұ� �Ұ����մϴ�.';
				}
			}
			else if($data['pg'] == 'payco' && ($data['settlekind'] == 'v' || $data['settlekind'] == 'o')) {				//������ ������� ���
				if ($data['pgcancel'] == 'y') {
					echo "<strong>�������</strong>�� �ֹ��Դϴ�.<br/>ȯ�ҿϷ�ó�����ֽñ�ٶ��ϴ�.";
				}
				else {
					if($repay == $data['settleprice']) echo "<a href=\"javascript:paycoCancel(".$i.", 'N', 1)\"><img src='../img/payco_cancel_btn.gif' ></a>";//������ ���� ��ü���
					else if($cancel_info['pgcancel'] != 'r') echo "<a href=\"javascript:paycoCancel(".$i.", 'Y', 1)\"><img src='../img/payco_partcancel_btn.gif'></a>";//������ ���� �κ����
					else {
						if($data['istep'] !== "44") {
							if ((int)$cancel_info['rfee'] > 0) {
								echo "<strong style='color: #ff0000;'>ȯ�Ҽ�����</strong>�� �����ϰ� <strong>�κ����</strong>�� �ֹ��Դϴ�.<br/>ȯ�ҿϷ�ó�����ֽñ�ٶ��ϴ�.";
							}
							else {
								echo "<strong>�κ����</strong>�� �ֹ��Դϴ�.<br/>ȯ�ҿϷ�ó�����ֽñ�ٶ��ϴ�.";
							}
						}
					}
				}
			}
			else{
				echo "ī��������� �ƴմϴ�.";
			}
		?>
		</span>
		</td>
	</tr>
	</table>

	<? if ($data['istep'] != 44 && $data['cyn'] != 'r') { ?>
	<div align=center style="margin:3px;padding:5px;color:red;border:2px dotted red;" id="el-over-refund-message<?=$i?>">
	���߿�!  �� ȯ�ұݾ��� �� �����ݾ��� �ʰ��Ͽ����ϴ�. ȯ���Ͻð��� �ϴ� �ݾ��� �´��� �ٽ� �ѹ� Ȯ���� �ּ���.
	</div>

	<script>cal_repay(<?=$repayfee?>,<?=$repay?>,<?=$data['settleprice']?>,<?=$before_refund_amount?>,<?=$i?>);</script>
	<? } ?>

	<? if ($data['istep'] != 44 && $data['cyn'] != 'r') { ?>
		<div align=center style="padding-top:5">�� �ֹ������� ����� �������� �� <font color=0074BA><b><?=number_format($data[emoney])?>��</b></font> �Դϴ�.&nbsp;&nbsp;������ ����� ������ �� <input type=text name='remoney[]' style='text-align=right;background:#E9FFB3' onkeydown='onlynumber();' value='0'>���� �ǵ����ݴϴ�.</div>
	<? } ?>
	<?
	if($agoemoney){
	?>
	<div align=center style="padding-top:5">�� ����ֹ����� �ǵ����� �������� <font color=0074BA><b><?=number_format($agoemoney)?>��</b></font> �Դϴ�.</div>
	<?}?>
	</td>
</tr>
<tr><td colspan=10 bgcolor='#616161' height=3></td></tr>
<tr><td colspan=10 height=10></td></tr>
<?
	$i++;
}
?>
</table>

<div class=pageNavi align=center><?=$pg->page[navi]?></div>

<div class=button>
<input type=image src="../img/btn_refund.gif" onclick="return isChked(document.getElementsByName('chk[]'),'������ ȯ��ó���� �Ͻðڽ��ϱ�?')">
<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a>
</div>

</form>

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"><span class="color_ffe" style="font-weight:bold;">�Ա��� ���¿��� �ֹ��� ���</span>�ϰų� �̹� ��۵Ǿ� <span class="color_ffe" style="font-weight:bold;">��ǰ��</span> �߻��ϴ� <span class="color_ffe" style="font-weight:bold;">ȯ�Ұǿ� ���� �Ϸ�ó��</span>�ϴ� �����Դϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�ֹ���ҿ� ��ǰ�Ϸ�ó���� ���� <span class="color_ffe" style="font-weight:bold;">ȯ�������� �ֹ���</span>�� ȯ����������Ʈ�� ���Դϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">ȯ���������� Ȯ���ϰ� <span class="color_ffe" style="font-weight:bold;">���� �������� ȯ�ұݾ��� �Ա�</span>�մϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">ȯ���Ա��� �Ϸ�� �ش� �ֹ����� ������ �� <span class="color_ffe" style="font-weight:bold;">ȯ�ҿϷ�ó��</span>�մϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"><span class="color_ffe" style="font-weight:bold;">���� ȯ�ұݾ�</span>�̶� <span class="color_ffe" style="font-weight:bold;">�ǰ����ݾ�</span>���� <span class="color_ffe" style="font-weight:bold;">ȯ�Ҽ�����</span>�� ���� �ݾ��� ���մϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"><span class="color_ffe" style="font-weight:bold;">ȯ�Ҽ�����</span>�� ��ǰ���� ���� �߻��� <span class="color_ffe" style="font-weight:bold;">�ݼۺ�� �� ��Ÿ ������</span>�� �ǹ��ϸ�, <span class="color_ffe" style="font-weight:bold;">�⺻���� ����</span>�� �� �ֽ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"><span class="color_ffe" style="font-weight:bold;">���������� ����</span>�� ��� <span class="color_ffe" style="font-weight:bold;">ȯ��������</span>�� �����Ͽ� <span class="color_ffe" style="font-weight:bold;">������</span>���־�� �մϴ�.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>


<script>window.onload = function(){ UNM.inner();};</script>

<? include "../_footer.php"; ?>
