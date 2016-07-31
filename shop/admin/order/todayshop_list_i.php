<?php
if ($_GET['goodstype']=='coupon') $loc_detail = '����(��ù߱�)';
else $loc_detail = '�ǹ�(��ù߼�)';

$location = "�ֹ����� > ".$loc_detail." �ֹ�����Ʈ";
include "../_header.php";
@include "../../conf/config.pay.php";
include "../../lib/page.class.php";
@include "../../conf/phone.php";

$todayShop = &load_class('todayshop', 'todayshop');
if (!$todayShop->auth()) {
	msg('��û�Ŀ� ��밡���� �����Դϴ�.', -1);
}

if (get_magic_quotes_gpc()) {
	stripslashes_all($_POST);
	stripslashes_all($_GET);
}

$_GET['todaygoods'] = 'y';
$_GET['processtype'] = 'i';

// $_GET���� �޴� ��� �� ����
$search=array(
	'first'=>(string)$_GET['first'], // ó�� ���ȭ���� ���������� ���� ��
	'regdt_start'=>(string)$_GET['regdt'][0], // ó������ ����
	'regdt_end'=>(string)$_GET['regdt'][1], // ó������ ��
	'dtkind'=>(string)($_GET['dtkind'] ? $_GET['dtkind'] : 'orddt'), // ó������ ����
	'mode'=>(string)$_GET['mode'], // ��� ���� ( �ֹ��Ϸ� ���� , �ֹ�ó���帧���� ���� )
	'sword'=>trim((string)$_GET['sword']), // �˻���
	'skey'=>($_GET['skey'] ? (string)$_GET['skey'] : 'all'), // �˻��ʵ�
	'sgword'=>trim((string)$_GET['sgword']), // ��ǰ �˻���
	'sgkey'=>(string)$_GET['sgkey'], // ��ǰ �˻��ʵ�
	'company'=>(string)$_GET['company'], // ���޾�ü
	'step'=>(array)$_GET['step'], // �ֹ�����
	'step2'=>(array)$_GET['step2'], // �ֹ�����
	'settlekind'=>(string)$_GET['settlekind'], // �������
	'escrowyn'=>(string)$_GET['escrowyn'], // ����ũ��
	'eggyn'=>(string)$_GET['eggyn'], // �Һ������غ�����
	'chk_inflow'=>(array)$_GET['chk_inflow'], // ����ó�ֹ�
	'couponyn'=>(string)$_GET['couponyn'], // �������
	'cashreceipt'=>(string)$_GET['cashreceipt'], // ���ݿ�����
	'cbyn'=>(string)$_GET['cbyn'], // OKCashbag����
	'aboutcoupon'=>(string)$_GET['aboutcoupon'], // ��ٿ�����
	'mobilepay'=>(string)$_GET['mobilepay'], // ����ϼ�
	'todaygoods'=>(string)$_GET['todaygoods'], // �����̼�
	'processtype'=>(string)$_GET['processtype'], // ó������
	'goodstype'=>(string)$_GET['goodstype'], // �����̼� ��ǰŸ��
);
$page = (int)$_GET['page'] ? (int)$_GET['page'] : 1;

// first ���ڰ��� ���� ó������ �⺻�� ����
if($search['first']) {
	if(!$cfg['orderPeriod']) $cfg['orderPeriod']=0;
	$search['regdt_start'] = date('Ymd',strtotime('-'.$cfg['orderPeriod'].' day'));
	$search['regdt_end'] = date('Ymd');
}
$search['first']=0;

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
	if($search['company']) {
		$ts_subqry .= ' AND ts.company=\''.$search['company'].'\'';
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
$orderList=array();
$orderGroupNameMap=array();
@include './checkout._order_list.php'; // Checkout include
if($isEnableAdminCheckoutOrder !== true) {
	$query = '
		select
			o.ordno as ordno,
			o.nameOrder as nameOrder,
			o.nameReceiver as nameReceiver,
			o.settlekind as settlekind,
			o.step as step,
			o.step2 as step2,
			o.orddt as orddt,
			o.dyn as dyn,
			o.escrowyn as escrowyn,
			o.eggyn as eggyn,
			o.inflow as inflow,
			o.deliverycode as deliverycode,
			o.cashreceipt as cashreceipt,
			o.cbyn as cbyn,
			o.oldordno as oldordno,
			o.prn_settleprice as prn_settleprice,
			m.m_id as m_id,
			m.m_no as m_no,
			'.$select_count_item.',
			'.$select_count_dv_item.',
			oi.goodsnm as goodsnm
		';
	if ($_GET['goodstype'] == 'coupon') {
		$query .= ', toc.cp_num, toc.cp_ea ';
	}
	$query .= '
		from
			'.GD_ORDER.' as o
			left join '.GD_ORDER_ITEM.' as oi on o.ordno=oi.ordno
			left join '.GD_MEMBER.' as m on o.m_no = m.m_no
		';
	if ($_GET['goodstype'] == 'coupon') {
		$query .= ' left join '.GD_TODAYSHOP_ORDER_COUPON.' as toc on o.ordno=toc.ordno ';
	}
	$query .= ' '.$join_GD_COUPON_ORDER.' '.$strWhere.'	group by o.ordno ';

	if($search['mode']=='group') {
		$result = $db->_select($query);

		// �׷캰�� �ֹ��� �Ҵ�
		foreach($result as $v) {
			$orderGroupKey = $v['step2']*10+$v['step'];
			$orderGroupNameMap[$orderGroupKey] = getStepMsg($v['step'],$v['step2']);
			$orderList[$orderGroupKey][] = $v;
		}
		ksort($orderList);

		// ����
		foreach($orderList as $orderGroupKey=>$eachOrderGroup) {
			$sortAssistDyn=$sortAssistOrdno=array();
			foreach ($eachOrderGroup as $k => $v) {
				$sortAssistDyn[$k]  = $v['dyn'];
				$sortAssistOrdno[$k] = $v['ordno'];
				$orderList[$orderGroupKey][$k]['stepMsg'] = getStepMsg($v['step'],$v['step2'],$v['ordno']);
			}
			array_multisort($sortAssistDyn,SORT_ASC,$sortAssistOrdno,SORT_DESC,$orderList[$orderGroupKey]);

			$i=0;
			foreach ($eachOrderGroup as $k => $v) {
				$orderList[$orderGroupKey][$k]['_rno'] = count($eachOrderGroup)-($i++);
			}
		}
	}
	else {
		if(!$cfg['orderPageNum']) $cfg['orderPageNum'] = 15;

		$query = $query.' order by o.ordno desc';
		$result = $db->_select_page($cfg['orderPageNum'],$page,$query);

		$orderList[9999]=array();
		foreach($result['record'] as $v) {
			$orderList[9999][] = $v;
		}
		$pageNavi = $result['page'];
	}
}

// ���޾�ü ��������
$res = $db->query("SELECT cp_sno, cp_name FROM ".GD_TODAYSHOP_COMPANY);
while($tmpData = $db->fetch($res, 1)) $cpData[] = array('cp_sno'=>$tmpData['cp_sno'], 'cp_name'=>$tmpData['cp_name']);
unset($res);
?>

<script type="text/javascript">
/**
* ���λ��� Ȱ��ȭ
*/
function iciSelect(obj) {
	var row = obj.parentNode.parentNode;
	row.style.background = (obj.checked) ? "#F9FFA1" : row.getAttribute('bg');
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
/**
* �׷켱��
*/
var chkBoxGroup_flag=true;
function chkBoxGroup(k) {
	$$(".chk_ordno_"+k).each(function(item){
		if(item.disabled==true) return;
		item.checked=chkBoxGroup_flag;
		iciSelect(item);
	});
	chkBoxGroup_flag=!chkBoxGroup_flag;
}
/**
* �ֹ����º������
*/
function processOrder() {
	f = $('frmList');
	var selCase = f.select('select[name=case]')[0];
	var isGodoChk=false;

	if(!selCase.value) {
		alert('�ֹ����°��� �������ּ���');
		return;
	}

	f.select("input[type=checkbox]").each(function(item){
		var re = new RegExp('^chk');
		if(re.test(item.name) && item.checked) {
			isGodoChk=true;
		}
	});

	// ���̹�üũ�ƿ�
	if (typeof(processCheckoutOrder) == 'function') {
		if (processCheckoutOrder(f, selCase, isGodoChk) === false) {
			return;
		}
	}

	if(isGodoChk) {
		f.submit();
	}
	else {
		alert('�ֹ����� �������ּ���');
	}
}

/**
* �������� �ٿ�ε�
*/
function dnXls(mode)
{
	var fm = document.frmDnXls;
	fm.mode.value = mode;
	fm.target = "ifrmHidden";
	fm.action = "dnXls_todayshop_i.php";
	fm.submit();
}
</script>

<div class="title title_top" style="position:relative;padding-bottom:15px">�����̼� <?=$loc_detail?> �ֹ�����Ʈ<span>�ֹ��� Ȯ���ϰ� �ֹ����¸� �����մϴ�</span>
<a href="javascript:manual('<?=$guideUrl?>board/view.php?id=todayshop&no=5')"><img src="../img/btn_q.gif" border="0" hspace="2" align="absmiddle"/></a>
<div style="position:absolute;left:100%;width:231px;height:44px;margin-left:-240px;margin-top:-15px"><a href="../order/post_introduce.php"><img src="../img/btn_postoffic_reserve_go.gif"/></a></div>
</div>

<form>
<input type="hidden" name="mode" value="<?=$search['mode']?>"/>
<input type="hidden" name="goodstype" value="<?=$search['goodstype']?>"/>

<table class="tb">
<col class="cellC"><col class="cellL" style="width:250px">
<col class="cellC"><col class="cellL">
<tr>
	<td><font class=small1>���޾�ü</font></td>
	<td>
		<select name="company">
			<option value="">= ���޾�ü ���� =</option>
			<? for ($i = 0; $i < count($cpData); $i++){ ?>
			<option value="<?=$cpData[$i]['cp_sno']?>" <?=frmSelected($search['company'],$cpData[$i]['cp_sno']);?> ><?=$cpData[$i]['cp_name']?></option>
			<? } ?>
		</select>
	</td>
</tr>
<tr>
	<td><span class="small1">�ֹ��˻� (����)</span></td>
	<td>
	<select name="skey">
	<option value="all"> = ���հ˻� = </option>
	<option value="ordno" <?=frmSelected($search['skey'],'ordno');?>> �ֹ���ȣ</option>
	<option value="nameOrder" <?=frmSelected($search['skey'],'nameOrder');?>> �ֹ��ڸ�</option>
	<option value="nameReceiver" <?=frmSelected($search['skey'],'nameReceiver');?>> �����ڸ�</option>
	<option value="bankSender" <?=frmSelected($search['skey'],'bankSender');?>> �Ա��ڸ�</option>
	<option value="m_id" <?=frmSelected($search['skey'],'m_id');?>> ���̵�</option>
	</select>
	<input type="text" name="sword" value="<?=htmlspecialchars($search['sword'])?>" class="line"/>
	</td>
	<td><span class="small1">��ǰ�˻� (����)</span></td>
	<td>
	<select name="sgkey">
	<option value="goodsnm" <?=frmSelected($search['sgkey'],'goodsnm');?>> ��ǰ��</option>
	<option value="brandnm" <?=frmSelected($search['sgkey'],'brandnm');?>> �귣��</option>
	<option value="maker" <?=frmSelected($search['sgkey'],'maker');?>> ������</option>
	</select>
	<input type=text name="sgword" value="<?=htmlspecialchars($search['sgword'])?>" class="line"/>
	</td>
</tr>
<tr>
	<td><span class="small1">�ֹ�����</span></td>
	<td colspan="3" class="noline">
	<? 
		foreach ($r_step as $k=>$v){
			if ($_GET['goodstype'] == 'coupon' && in_array($k, array('0','2','3'))) continue;
	?>
	<div style="float:left; padding-right:10px; color:#5C5C5C;"><input type="checkbox" name="step[]" value="<?=$k?>" <?=(in_array($k,$search['step'])?'checked':'')?>><span class="small1"><?=$v?></span></input></div>
	<? } ?>
	<div style="float:left; padding-right:10px; color:#5C5C5C;"><input type="checkbox" name="step2[]" value="1" <?=(in_array(1,$search['step2'])?'checked':'')?>><span class="small1">�ֹ����</span></input></div>
	<div style="clear:both;"></div>
	<div style="float:left; padding-right:10px; color:#5C5C5C;"><input type="checkbox" name="step2[]" value="2" <?=(in_array(2,$search['step2'])?'checked':'')?>><span class="small1">ȯ�Ұ���</span></input></div>
	<div style="float:left; padding-right:10px; color:#5C5C5C;"><input type="checkbox" name="step2[]" value="3" <?=(in_array(3,$search['step2'])?'checked':'')?>><span class="small1">��ǰ����</span></input></div>
	<div style="float:left; padding-right:10px; color:#5C5C5C;"><input type="checkbox" name="step2[]" value="60" <?=(in_array(60,$search['step2'])?'checked':'')?>><span class="small1">��ȯ�Ϸ�</span></input></div>
	<div style="float:left; padding-right:10px; color:#5C5C5C;"><input type="checkbox" name="step2[]" value="61" <?=(in_array(61,$search['step2'])?'checked':'')?>><span class="small1">���ֹ�</span></input></div>
	<div style="float:left; padding-right:10px; color:#5C5C5C;"><input type="checkbox" name="step2[]" value="50" <?=(in_array(50,$search['step2'])?'checked':'')?>><span class="small1">�����õ�</span></input></div>
	<div style="float:left; padding-right:10px; color:#5C5C5C;"><input type="checkbox" name="step2[]" value="54" <?=(in_array(54,$search['step2'])?'checked':'')?>><span class="small1">��������</span></input></div>
	<div style="float:left; padding-right:10px; color:#5C5C5C;"><input type="checkbox" name="step2[]" value="51" <?=(in_array(51,$search['step2'])?'checked':'')?>><span class="small1">PGȮ�ο��</span></input></div>
	</td>
</tr>
<tr>
	<td><span class="small1">ó������</span></td>
	<td colspan="3">
	<span class="noline small1" style="color:5C5C5C; margin-right:20px;">
	<input type="radio" name="dtkind" value="orddt" <?=frmChecked($search['dtkind'],'orddt')?>>�ֹ���</input>
	<input type="radio" name="dtkind" value="cdt" <?=frmChecked($search['dtkind'],'cdt')?>>����Ȯ����</input>
	<input type="radio" name="dtkind" value="ddt" <?=frmChecked($search['dtkind'],'ddt')?>>�����</input>
	<input type="radio" name="dtkind" value="confirmdt" <?=frmChecked($search['dtkind'],'confirmdt')?>>��ۿϷ���</input>
	</span>
	<input type="text" name="regdt[]" value="<?=$search['regdt_start']?>" onclick="calendar(event)" size="12" class="line"/> -
	<input type="text" name="regdt[]" value="<?=$search['regdt_end']?>" onclick="calendar(event)" size="12" class="line"/>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd")?>,<?=date("Ymd")?>)"><img src="../img/sicon_today.gif" align="absmiddle"/></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-7 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_week.gif" align="absmiddle"/></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-15 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twoweek.gif" align="absmiddle"/></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-1 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_month.gif" align="absmiddle"/></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-2 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twomonth.gif" align="absmiddle"/></a>
	<a href="javascript:setDate('regdt[]')"><img src="../img/sicon_all.gif" align="absmiddle"/></a>
	</td>
</tr>
<tr>
	<td><span class="small1">�������</span></td>
	<td colspan="3" class="noline"><span class="small1" style="color:#5C5C5C;">
	<input type="radio" name="settlekind" value="" <?=frmChecked('',$search['settlekind'])?>>��ü</input>
	<input type="radio" name="settlekind" value="c" <?=frmChecked('c',$search['settlekind'])?>>�ſ�ī��</input>
	<input type="radio" name="settlekind" value="o" <?=frmChecked('o',$search['settlekind'])?>>������ü</input>
	<input type="radio" name="settlekind" value="h" <?=frmChecked('h',$search['settlekind'])?>>�ڵ���</input>
	<? if ($cfg['settlePg'] == "inipay") { ?>
	<input type="radio" name="settlekind" value="y" <?=frmChecked('y',$search['settlekind'])?>>��������</input>
	<? } ?>
	<input type="checkbox" name="cashreceipt" value="1" <?=frmChecked('1',$search['cashreceipt'])?>>���ݿ����� <img src="../img/icon_cash_receipt.gif"/></input>
	<input type="radio" name="settlekind" value="p" <?=frmChecked('p',$search['settlekind'])?>>����Ʈ</input>
	<input type="checkbox" name="cbyn" value="Y" <?=frmChecked('p',$search['cbyn'])?>><img src="../img/icon_okcashbag.gif" align="absmiddle"/>OKCashBag����</input>
	</span>
	</td>
</tr>
</table>
<div class="button_top">
<input type="image" src="../img/btn_search2.gif"/>
</div>
</form>

<div style="padding-top:15px"></div>


<form name="frmList" method="post" action="indb.php"  id="frmList">
<input type="hidden" name="mode" value="chgAll"/>

<table width="100%" cellpadding="0" cellspacing="0">
<tr>
	<td>�Ʒ����� ������ �ֹ�����
	<select name="case" required label="��������">
	<option value="">- �ֹ����� ����ó�� -</option>
	<option value="1">�Ա�Ȯ�� ó��</option>
	<? if ($_GET['goodstype'] == 'coupon') { ?>
	<option value="4">�߼ۿϷ� ó��</option>
	<? } else { ?>
	<option value="2">����غ��� ó��</option>
	<option value="3">����� ó��</option>
	<option value="4">��ۿϷ� ó��</option>
	<? } ?>
	</select> �մϴ�. <span class="extext">(������ �ϴ� ������ư �� Ŭ��)</span>
	</td>
	<td align="right">
	<?php if ($search['mode']=="group"):?>
		<a href="?<?=getVars('page,mode')?>"><img src="../img/btn_orderdate_off.gif" align="absmiddle"/></a>
		<img src="../img/btn_orderprocess_on.gif" align="absmiddle"/>
	<?php else: ?>
		<img src="../img/btn_orderdate_on.gif" align="absmiddle"/>
		<a href="?mode=group&<?=getVars('page,mode')?>"><img src="../img/btn_orderprocess_off.gif" align="absmiddle"/></a>
	<?php endif; ?>
	</td>
</tr>
<tr><td height="3"></td></tr>
</table>


<table width="100%" cellpadding="0" cellspacing="0" border="0">
<col width="25"><col width="30"><col width="100"><col width="120"><? if ($_GET['goodstype'] == 'coupon') { ?><col width="120"><? } ?><col><col width="120"><col width="95"><col width="50"><col width="50"><col><col width="55">
<tr><td class="rnd" colspan="20"></td></tr>
<tr class="rndbg">
	<th><a href="javascript:void(0)" onClick="chkBoxAll()" class=white>����</a></th>
	<th>��ȣ</th>
	<th>�ֹ��Ͻ�</th>
	<th>�ֹ���ȣ</th>
	<? if ($_GET['goodstype'] == 'coupon') { ?>
	<th>������ȣ/����</th>
	<? } ?>
	<th>�ֹ���ǰ</th>
	<th>�ֹ���</th>
	<th>�޴º�</th>
	<th>����</th>
	<th>�ݾ�</th>
	<th colspan="6">ó������</th>
</tr>
<tr><td class="rnd" colspan="20"></td></tr>
<?php
$totalPrnSettlePrice=0;
foreach($orderList as $orderGroupKey => $eachOrderGroup):
	$groupPrnSettlePrice=0;

	if($orderGroupKey!=9999):
?>
<tr><td colspan="13" bgcolor="#E8E7E7" height="1"></td></tr>
<tr align="center">
	<td colspan="13" bgcolor="#f7f7f7" height="30" style="padding-left:15px">
	<b><img src="../img/icon_process.gif" align="absmiddle"/>
		<?=$orderGroupNameMap[$orderGroupKey]?>
	</b>
	</td>
</tr>
<?php
	endif;

	foreach($eachOrderGroup as $eachOrder):
		if($eachOrder['count_item']>1) $goodsnm = $eachOrder['goodsnm'].' ��'.($eachOrder['count_item']-1).'��';
		else $goodsnm = $eachOrder['goodsnm'];

		$groupPrnSettlePrice+=$eachOrder['prn_settleprice'];
		$disabled = ($eachOrder['step2']) ? 'disabled' : '';
		$bgcolor = ($eachOrder['step2']) ? "#F0F4FF" : "#ffffff";

		if($eachOrder['_order_type']=='checkout'):
?>

<tr height="25" bgcolor="<?=$bgcolor?>" bg="<?=$bgcolor?>" align="center">
	<?php if($eachOrder['step']=='OD0002'): ?>
	<td class="noline"><input type="checkbox" name="checkoutPlaceOrder[]" value="<?=$eachOrder['ordno']?>" class="chk_ordno_<?=$orderGroupKey?> chk_ordno" onclick="iciSelect(this)" <?=$disabled?>/></td>
	<?php elseif($eachOrder['step']=='OD0007' || $eachOrder['step']=='OD0008'): ?>
	<td class="noline"><input type="checkbox" name="checkoutShipOrder[]" value="<?=$eachOrder['ordno']?>" class="chk_ordno_<?=$orderGroupKey?> chk_ordno" onclick="iciSelect(this)" <?=$disabled?>/></td>
	<?php else: ?>
	<td class="noline"><input type="checkbox" disabled/></td>
	<?php endif; ?>

	<td><span class="ver8" style="color:#005B00"><?=$eachOrder['_rno']?></span></td>
	<td><span class="ver81" style="color:#005B00"><?=substr($eachOrder['orddt'],0,-3)?></span></td>
	<? if ($_GET['goodstype'] == 'coupon') { ?>
	<td></td>
	<? } ?>
	<td align="center"><a href="checkout.orderdetail.php?OrderID=<?=$eachOrder['ordno']?>"><span class="ver81" style="color:#005B00"><b><?=$eachOrder['ordno']?></b></span></a></td>
	<td align="left">
		<div style="height:13px; overflow-y:hidden;">
		<? if ($eachOrder['escrowyn']=="y"){ ?><img src="../img/btn_escrow.gif"/><? } ?>
		<? if ($eachOrder['cashreceipt']!=""){ ?><img src="../img/icon_cash_receipt.gif"/><? } ?>
		<span class="small1" style="color:#005700"><?=$goodsnm?></span>
		</div>
	</td>
	<td><span><span class="small1" style="color:#444444"><b><?=$eachOrder['nameOrder']?></b>(<?=$eachOrder['m_id']?>)</span></span></td>
	<td><span class="small1" style="color:#005700"><?=$eachOrder['nameReceiver']?></span></td>
	<td class="small4" style="color:#005700"><?=$eachOrder['settlekind']?></td>
	<td class="ver81" style="color:#005700"><b><?=number_format($eachOrder['prn_settleprice'])?></b></td>
	<td class="small4" width="70" style="color:#005700"><?=$eachOrder['stepMsg']?></td>
</tr>
<?php if($eachOrder['step']=='OD0007' || $eachOrder['step']=='OD0008'): ?>
<tr id="tr_<?=$eachOrder['ordno']?>">
	<td colspan="2"></td>
	<td colspan="10">
		����� : <input type="text" name="ShippingCompleteDate[<?=$eachOrder['ordno']?>]" value="" onclick="calendar(event)" readonly style="width:80px">
		��۹�� :

		<select name="ShippingCompany[<?=$eachOrder['ordno']?>]" style="width:110px;font-size:7pt;" class="selShippingCompany">
		<option value="">(����)</option>
		<option value="korex">�������</option>
		<option value="cjgls">CJGLS</option>
		<option value="sagawa">SC ������</option>
		<option value="yellow">���ο�ĸ</option>
		<option value="kgb">�����ù�</option>
		<option value="dongbu">�����ͽ��������ù�</option>
		<option value="EPOST">��ü���ù�</option>
		<option value="hanjin">�����ù�</option>
		<option value="hyundai">�����ù�</option>
		<option value="kgbls">KGB �ù�</option>
		<option value="z_etc">��Ÿ �ù�</option>
		<option value="z_quick">������</option>
		<option value="z_direct">�����</option>
		<option value="z_visit">�湮 ����</option>
		<option value="z_post">���� ���</option>
		<option value="z_delegation">��ü�� ���</option>
		<option value="kdexp">�浿�ù�</option>
		</select>
		�����ȣ : <input type="text" name="TrackingNumber[<?=$eachOrder['ordno']?>]" value=""   style="width:150px" class="iptTrackingNumber">
	</td>
</tr>
<?php endif; ?>
<tr><td colspan="20" bgcolor="#E4E4E4"></td></tr>

<?php
		else:
?>

<tr height="25" bgcolor="<?=$bgcolor?>" bg="<?=$bgcolor?>" align="center">
	<td class="noline"><input type="checkbox" name="chk[]" value="<?=$eachOrder['ordno']?>" class="chk_ordno_<?=$orderGroupKey?> chk_ordno" onclick="iciSelect(this)" <?=$disabled?>/></td>
	<td><span class="ver8" style="color:#616161"><?=$eachOrder['_rno']?></span></td>
	<td><span class="ver81" style="color:#616161"><?=substr($eachOrder['orddt'],0,-3)?></span></td>
	<td align="left">
	<a href="view.php?ordno=<?=$eachOrder['ordno']?>"><span class="ver81" style="color:#0074BA"><b><?=$eachOrder['ordno']?></b></span></a>
	<a href="javascript:popup('popup.order.php?ordno=<?=$eachOrder['ordno']?>',800,600)"><img src="../img/btn_newwindow.gif" border=0 align="absmiddle"/></a>
	</td>
	<? if ($_GET['goodstype'] == 'coupon') { ?>
	<td>
	<span class="small1" style="color:#444444"><?=$eachOrder['cp_num']?> / <?=$eachOrder['cp_ea']?></span>
	</td>
	<? } ?>
	<td align="left">
	<div style="height:13px; overflow-y:hidden;">
	<? if ($eachOrder['oldordno']!=""){ ?><a href="javascript:popup('popup.order.php?ordno=<?=$eachOrder['ordno']?>',800,600)"><img src="../img/icon_twice_order.gif"/></a><? } ?>
	<? if ($eachOrder['escrowyn']=="y"){ ?><a href="javascript:popup('popup.order.php?ordno=<?=$eachOrder['ordno']?>',800,600)"><img src="../img/btn_escrow.gif"/></a><? } ?>
	<? if ($eachOrder['eggyn']=="y"){ ?><a href="javascript:popup('popup.order.php?ordno=<?=$eachOrder['ordno']?>',800,600)"><img src="../img/icon_guar_order.gif"/></a><? } ?>
	<? if ($eachOrder['inflow']!=""){ ?><a href="javascript:popup('popup.order.php?ordno=<?=$eachOrder['ordno']?>',800,600)"><img src="../img/inflow_<?=$eachOrder['inflow']?>.gif" align="absmiddle"/></a><? } ?>
	<? if ($eachOrder['cashreceipt']!=""){ ?><img src="../img/icon_cash_receipt.gif"/><? } ?>
	<? if ($eachOrder['cbyn']=="Y"){ ?><a href="javascript:popup('popup.order.php?ordno=<?=$eachOrder['ordno']?>',800,600)"><img src="../img/icon_okcashbag.gif" align="absmiddle"/></a><? } ?>
	<span class="small1" style="color:#444444"><?=$goodsnm?></span>
	</div>

	</td>
	<td><? if ($eachOrder['m_id']) { ?><span id="navig" name="navig" m_id="<?=$eachOrder['m_id']?>" m_no="<?=$eachOrder['m_no']?>"><? } ?><span class="small1" style="color:#0074BA">
	<b><?=$eachOrder['nameOrder']?></b><? if ($eachOrder['m_id']){ ?> (<?=$eachOrder['m_id']?>)</span><? if ($eachOrder['m_id']) { ?></span><? } ?>
	<? } ?>
	</td>
	<td><span class="small1" style="color:#444444;"><?=$eachOrder['nameReceiver']?></span></td>
	<td class="small4"><?=$r_settlekind[$eachOrder['settlekind']]?></td>
	<td class="ver81"><b><?=number_format($eachOrder['prn_settleprice'])?></b></td>
	<td class="small4" width="60">
		<? if($eachOrder['deliverycode'] || $eachOrder['count_dv_item']): ?>
			<a href="javascript:popup('popup.delivery.php?ordno=<?=$eachOrder['ordno']?>',800,500)" style="color:#0074BA"><?=$eachOrder['stepMsg']?></a>
		<? else: ?>
			<?=$eachOrder['stepMsg']?>
		<? endif; ?>
	</td>
</tr>
<tr><td colspan="20" bgcolor="#E4E4E4"></td></tr>

<?php
		endif;
	endforeach;
	$totalPrnSettlePrice+=$groupPrnSettlePrice;
?>
<tr>
	<td><a href="javascript:chkBoxGroup('<?=$orderGroupKey?>')"><img src="../img/btn_allchoice.gif" border="0"/></a></td>
	<td height="30" colspan="15" align="right" style="padding-right:8px">�հ�: <span class="ver9"><b><?=number_format($groupPrnSettlePrice)?></span>��</b></td>
	<td colspan="3"></td>
</tr>
<tr><td colspan="13" height="15"></td></tr>
<?php
endforeach;
?>
<tr bgcolor="#f7f7f7" height="30">
	<td colspan="15" align="right" style="padding-right:8px">��ü�հ� : <span class="ver9"><b><?=number_format($totalPrnSettlePrice)?>��</b></span></td>
	<td colspan="3"></td>
</tr>
<tr><td height="4"></td></tr>
<tr><td colspan="15" class="rndline"></td></tr>
</table>

<?php if($search['mode']!='group'): ?>
	<div align="center" class="pageNavi ver8" style="font-weight:bold">
		<? if($pageNavi['prev']): ?>
			<a href="?<?=getvalue_chg('page',$pageNavi['prev'])?>">�� </a>
		<? endif; ?>
		<? foreach($pageNavi['page'] as $v): ?>
			<? if($v==$pageNavi['nowpage']): ?>
				<a href="?<?=getvalue_chg('page',$v)?>"><?=$v?></a>
			<? else: ?>
				<a href="?<?=getvalue_chg('page',$v)?>">[<?=$v?>]</a>
			<? endif; ?>
		<? endforeach; ?>
		<? if($pageNavi['next']): ?>
			<a href="?<?=getvalue_chg('page',$pageNavi['next'])?>">��</a>
		<? endif; ?>
	</div>
<?php endif; ?>

<div class="button">
<a href="javascript:processOrder()"><img src="../img/btn_modify.gif"/></a>
<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"/></a>
</div>

</form>

<form name="frmDnXls" method="post">
<input type="hidden" name="mode"/>
<input type="hidden" name="search" value="<?php echo htmlspecialchars(serialize($search));?>"/>
</form>

<!-- �ֹ����� ����Ʈ&�ٿ�ε� : Start -->
<table width="100%" border="0" cellpadding="10" cellspacing="0" style="border:1px #dddddd solid;">
<tr>
	<td width="50%" align="center" bgcolor="#f6f6f6" style="font:16pt tahoma;"><img src="../img/icon_down.gif" border="0" align="absmiddle"/><b>download</b></td>
	<td width="50%" align="center" bgcolor="#f6f6f6" style="font:16pt tahoma;border-left:1px #dddddd solid;"><img src="../img/icon_down.gif" border="0" align="absmiddle"/><b>print</b></td>
</tr>
<tr>
	<td align="center">
	<table border="0" cellpadding="4" cellpadding="0" border="0">
	<tr align="center">
	<?if ($_GET['goodstype']=='goods') {?>
	<td><a href="javascript:dnXls('todaygoods');"><img src="../img/btn_order_data_order.gif" border="0"/></a></td>
	<?}else{?>
	<td><a href="javascript:dnXls('todaycoupon');"><img src="../img/btn_order_data_order.gif" border="0"/></a></td>
	<?}?>
	</tr>
	<tr align="center">
	<?if ($_GET['goodstype']=='goods') {?>
	<td><a href="javascript:popupLayer('../data/popup.orderxls.php?mode=orderTodayGoodsXls',550,700)"><img src="../img/btn_order_data_order_ot.gif" border="0"/></a></td>
	<?}else{?>
	<td><a href="javascript:popupLayer('../data/popup.orderxls.php?mode=orderTodayCouponXls',550,700)"><img src="../img/btn_order_data_order_ot.gif" border="0"/></a></td>
	<?}?>
	</tr>
	</table>
	</td>
	<td align="center" style="border-left:1px #dddddd solid;">
	<form method="get" name="frmPrint">
	<input type="hidden" name="ordnos"/>

	<table border="0" cellpadding="4" cellpadding="0" border="0">
	<tr align="center">
	<td><select NAME="type">
	<option value="report">�ֹ�������</option>
	<option value="reception">���̿�����</option>
	<option value="tax">���ݰ�꼭</option>
	<option value="particular">�ŷ�����</option>
	</select></td>
	</tr>
	<tr>
	<td align="center"><strong class=noline><label for="r1"><input class="no_line" type="radio" name="list_type" value="list" id="r1" onclick="openLayer('psrch','none')" checked>��ϼ���</input></label>&nbsp;&nbsp;&nbsp;<label for="r2"><input class="no_line" type="radio" name="list_type" value="term" id="r2" onclick="openLayer('psrch','block')">�Ⱓ����</input></label></strong></td>
	</tr>
	<tr>
	<td align="cemter"><div style="float:left; display:none;" id="psrch">
	<input type="text" name="regdt[]" onclick="calendar(event)" size="12" class="line"/> -
	<input type="text" name="regdt[]" onclick="calendar(event)" size="12" class="line"/>
	<select name="settlekind">
	<option value=""> - ������� - </option>
	<? foreach ( $r_settlekind as $k => $v ) echo "<option value=\"{$k}\">{$v}</option>"; ?>
	</select>
	<select name="step">
	<option value=""> - �ܰ輱�� - </option>
	<? foreach ( $r_step as $k => $v ) echo "<option value=\"step_{$k}\">{$v}</option>"; ?>
	<option value="step2_1">�ֹ����</option>
	<option value="step2_2">ȯ�Ұ���</option>
	<option value="step2_3">��ǰ����</option>
	<option value="step2_50">�����õ�</option>
	<option value="step2_54">��������</option>
	</select>
	</div></td>
	</tr>
	<tr>
	<td align="center"><a href="javascript:order_print('frmPrint', 'frmList');" style="padding-top:20px"><img src="../img/btn_print.gif" border="0" align="absmiddle"/></a></td>
	</tr>
	</table>
	</form>
	</td>
</tr>
</table>
<!-- �ֹ����� ����Ʈ : End -->

<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle"/>�ֹ��� �Ǵ� �ֹ�ó���帧 ������� �ֹ������� �����Ͻ� �� �ֽ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"/>�ֹ����¸� �����Ͻ÷��� �ֹ��� ���� - ó���ܰ輱�� �� ������ư�� ��������.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"/>�ֹ����º����� ���� �� �ֹ�ó���ܰ� (�ֹ�����, �Ա�Ȯ��, ����غ�, �����, ��ۿϷ�) �� ������  ó���Ͻ� �� �ֽ��ϴ�.</td></tr>

<tr><td height="8"></td></tr>
<tr><td><span class="def1"><b>- ī������ֹ��� �Ʒ��� ���� ��찡 �߻��� �� �ֽ��ϴ�. (�ʵ��ϼ���!) -</span></td></tr>

<tr><td><img src="../img/icon_list.gif" align="absmiddle"/>�ش� PG�� �����ڸ�忡�� ������ �Ǿ�����, �ֹ�����Ʈ���� �ֹ����°� '�Ա�Ȯ��'�� �ƴ� '�����õ�'�� �Ǿ� �ִ� ��찡 �߻��� �� �ֽ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"/>�̴� �߰��� ��Ż��� ������ ���ϰ��� ����� ���� ���� �ֹ����°� ������ ���� ���� ���Դϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"/>��, �̿Ͱ��� ������ �Ǿ����� �ֹ����°� '�����õ�'�� ��� �ش��ֹ����� �ֹ��󼼳��� ���������� "�����õ�, ���� ����" ó���� �Ͻø� �ֹ�ó�����°� "�Ա�Ȯ��"���� �����˴ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"/>�׷��� �������� ���ϰ��� �޾� �ֹ�ó�����°� ����� ���̱⿡ �̿� ���ؼ��� ��Ȯ�� �����α׸� �ֹ��󼼳������������� Ȯ���� �� �� �����ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"/>���� ���� ī������� �ֹ��� 1�� �����ߴµ� ��Ȥ PG�� �ʿ����� 2���� ����(�ߺ�����)�Ǵ� ��찡 �ֽ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"/>�� ���� �ش� PG���� �����ڸ��� ���� �ߺ����ε� 2���߿� 1���� ������� ���ֽø� �˴ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"/>�ߺ����ΰ��� üũ�ؼ� �ٷ� �������ó������ ������ �̼����� �߻��Ǿ� ���̰� �ǰ�, �ش� PG��κ��� �ŷ�������û ���� �������� ���� �� �ֽ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"/>�������ΰ��� �ֹ����¿� �ߺ����ΰ� ó���� �����ϰ� üũ�ؾ� �ϸ� �̿� ���� å���� ���θ� ��ڿ��� �ֽ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"/>�׻� ī��������� �̰� �ֹ�����Ʈ�� PG�翡�� �����ϴ� ������������ �������ΰǰ� ���ϸ鼭 ���Ǳ�� üũ�Ͽ� ó���Ͻñ� �ٶ��ϴ�.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

<?
include "_deliveryForm.php"; //�����ϰ��Է���
?>

<script>window.onload = function(){ UNM.inner();};</script>
<? @include dirname(__FILE__) . "/../interpark/_order_list.php"; // ������ũ_��Ŭ��� ?>

<? include "../_footer.php"; ?>
