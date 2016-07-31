<?
$location = "�ֹ����� > ȯ����������Ʈ";
include "../_header.php";
include "../../lib/page.class.php";
$r_bank = codeitem("bank");

if (get_magic_quotes_gpc()) {
	stripslashes_all($_POST);
	stripslashes_all($_GET);
}

// $_GET���� �޴� ��� �� ����
$search=array(
	'regdt_start'=>(string)$_GET['regdt'][0], // ó������ ����
	'regdt_end'=>(string)$_GET['regdt'][1], // ó������ ��
	'dtkind'=>(string)($_GET['dtkind'] ? $_GET['dtkind'] : 'orddt'), // ó������ ����
	'sword'=>trim((string)$_GET['sword']), // �˻���
	'skey'=>($_GET['skey'] ? (string)$_GET['skey'] : 'all'), // �˻��ʵ�
	'settlekind'=>(string)$_GET['settlekind'], // �������
	'bankcode'=>(string)$_GET['bankcode'], // ȯ�� ���� ����
);

if(strlen($search['bankcode']) == '1'){
	$search['bankcode'] = "0".$search['bankcode'];
}

// ��������
if(!in_array($search['dtkind'],array('orddt','cdt'))) { exit; }
if(!in_array($search['skey'],array('all','ordno','nameOrder','goodsnm','name'))) { exit; }
if(!in_array($search['settlekind'],array('','a','c','o','v','h','u','y'))) { exit; }
if(!array_key_exists($search['bankcode'],$r_bank) && $search['bankcode'] != 'all' && $search['bankcode']) { exit; }

// �������� ���� �˻����� �����
$where = array();
if($search['regdt_start']) {
	if(!$search['regdt_end']) $search['regdt_end'] = date('Ymd');
	$tmp_start = substr($search['regdt_start'],0,4).'-'.substr($search['regdt_start'],4,2).'-'.substr($search['regdt_start'],6,2).' 00:00:00';
	$tmp_end = substr($search['regdt_end'],0,4).'-'.substr($search['regdt_end'],4,2).'-'.substr($search['regdt_end'],6,2).' 23:59:59';
	switch($search['dtkind']) {
		case 'orddt': $where[] = $db->_query_print('c.orddt between [s] and [s]',$tmp_start,$tmp_end); break;
		case 'cdt': $where[] = $db->_query_print('a.regdt between [s] and [s]',$tmp_start,$tmp_end); break;
	}
}
if($search['settlekind']) {
	$where[] = $db->_query_print('c.settlekind = [s]',$search['settlekind']);
}
if($search['sword'] && $search['skey']) {
	$es_sword = $db->_escape($search['sword']);
	switch($search['skey']) {
		case 'all':
			$where[] = "(
				c.ordno = '{$es_sword}' or
				c.nameOrder like '%{$es_sword}%' or
				b.goodsnm like '%{$es_sword}%' or
				a.name like '%{$es_sword}%'
			)"; break;
		case 'ordno': $where[] = "c.ordno = '{$es_sword}'"; break;
		case 'nameOrder': $where[] = "c.nameOrder like '%{$es_sword}%'"; break;
		case 'goodsnm': $where[] = "b.goodsnm like '%{$es_sword}%'"; break;
		case 'name': $where[] = "a.name like '%{$es_sword}%'"; break;
	}
}
if($search['bankcode']){
	$es_sword = $db->_escape($search['bankcode']);
	if($es_sword != 'all'){
		$where[] = "a.bankcode = '{$es_sword}'";
	}
}

$db_table = "
".GD_ORDER_CANCEL." a
left join ".GD_ORDER_ITEM." b on a.sno=b.cancel and a.ordno = b.ordno
,".GD_ORDER." c
left join ".GD_MEMBER." d on c.m_no=d.m_no
";

$where[] = "b.ordno=c.ordno";
$where[] = "istep > 40";
$where[] = "b.cyn = 'y'";
$where[] = "b.dyn in ('n','r')";

$pg = new Page($_GET[page]);
$pg->field = "
a.sno,a.regdt canceldt,a.name nameCancel,a.bankcode,AES_DECRYPT(UNHEX(a.bankaccount), a.ordno) AS bankaccount,a.bankuser,
sum((b.price-b.memberdc -b.coupon - b.oi_special_discount_amount)*b.ea)  as repay,count(*) cnt,
c.ordno,c.orddt,c.nameOrder,c.settlekind,c.step2,c.settleprice,c.m_no,c.ncash_emoney,c.ncash_cash,a.rncash_emoney,a.rncash_cash,
c.settleprice, c.goodsprice, c.delivery, c.coupon, c.emoney, c.memberdc,c.enuri,c.eggFee,c.escrowyn,c.pgcancel,c.pg,d.m_no,d.m_id,c.settleInflow,c.cdt, d.dormant_regDate
";
$pg->setQuery($db_table,$where,"a.sno desc","group by a.sno");
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
	var repayfee = parseInt(document.getElementsByName('repayfee[]')[idx].value);
	if (repayfee && parseInt(repayfee) > 0) {
		cardPartCancel(idx);
	}
	else if (confirm('ī������� ����Ͻðڽ��ϱ�?')) {
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

//������ �������� �κ�/��ü ���
function paycoCancel(idx,part,vbank) {
	var ordno = document.getElementsByName('ordno[]')[idx].value;
	var sno = document.getElementsByName('sno[]')[idx].value;
	var lastRepay = document.getElementsByName('repay[]')[idx].value;//ȯ�ҿ����ݾ�
	var repayfee = document.getElementsByName('repayfee[]')[idx].value;//ȯ�Ҽ�����
	var repay = parseInt(lastRepay) + parseInt(repayfee);//���� ȯ�ұݾ�
	var remoney = document.getElementsByName('remoney[]')[idx].value;//������ ������

	if(vbank) {
		if(part == "Y") file = 'paycoPartCancelVbank.php';//������� �κ����ó��
		else file = 'paycoCancelVbank.php';//������� ���ó��
	}
	else file = 'paycoCancel.php';//�ſ�ī��,����������Ʈ,�޴�������,������ü ���ó��

	popupLayer("./"+file+"?ordno="+ordno+"&sno="+sno+"&part="+part+"&repay="+repay+"&lastRepay="+lastRepay+"&repayfee="+repayfee+"&remoney="+remoney,600,350);
}
</script>
<div class="title title_top">ȯ����������Ʈ <span>ȯ�������� �ֹ����� ��ȸ�ϰ� ȯ�ҿϷ�ó���մϴ�</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=order&no=5')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<form>
<input type="hidden" name="mode" value="<?=$search['mode']?>"/>

<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td><span class="small1">Ű���� �˻� (����)</span></td>
	<td>
	<select name="skey">
	<option value="all"> = ���հ˻� = </option>
	<option value="ordno" <?=frmSelected($search['skey'],'ordno');?>> �ֹ���ȣ</option>
	<option value="nameOrder" <?=frmSelected($search['skey'],'nameOrder');?>> �ֹ��ڸ�</option>
	<option value="goodsnm" <?=frmSelected($search['skey'],'goodsnm');?>> ��ǰ��</option>
	<option value="name" <?=frmSelected($search['skey'],'name');?>> ó����</option>
	</select>
	<input type="text" name="sword" value="<?=htmlspecialchars($search['sword'])?>" class="line"/>
	</td>
</tr>
<tr>
	<td><span class="small1">�������</span></td>
	<td colspan="3" class="noline"><span class="small1" style="color:#5C5C5C;">
	<input type="radio" name="settlekind" value="" <?=frmChecked('',$search['settlekind'])?>>��ü</input>
	<input type="radio" name="settlekind" value="a" <?=frmChecked('a',$search['settlekind'])?>>������</input>
	<input type="radio" name="settlekind" value="c" <?=frmChecked('c',$search['settlekind'])?>>�ſ�ī��</input>
	<input type="radio" name="settlekind" value="o" <?=frmChecked('o',$search['settlekind'])?>>������ü</input>
	<input type="radio" name="settlekind" value="v" <?=frmChecked('v',$search['settlekind'])?>>�������</input>
	<input type="radio" name="settlekind" value="h" <?=frmChecked('h',$search['settlekind'])?>>�ڵ���</input>
	<input type="radio" name="settlekind" value="u" <?=frmChecked('u',$search['settlekind'])?>>�ſ�ī��(�߱�)</input>
	<? if ($cfg['settlePg'] == "inipay") { ?>
	<input type="radio" name="settlekind" value="y" <?=frmChecked('y',$search['settlekind'])?>>��������</input>
	<? } ?>
	</span>
	</td>
</tr>
<tr>
	<td><span class="small1">ȯ�� ���� ����</span></td>
	<td colspan="3" class="noline">
	<select name="bankcode">
	<option value="all"> == ���� == </option>
	<? foreach ( $r_bank as $k=>$v){ ?>
	<option value="<?=$k?>"<?if(trim($k)==$search[bankcode])echo" selected";?>><?=$v?>
	<? } ?>
	</select>
	</td>
</tr>
<tr>
	<td><span class="small1">�Ⱓ�˻�</span></td>
	<td colspan="3">
	<span class="noline small1" style="color:5C5C5C; margin-right:20px;">
	<input type="radio" name="dtkind" value="orddt" <?=frmChecked($search['dtkind'],'orddt')?>>�ֹ���</input>
	<input type="radio" name="dtkind" value="cdt" <?=frmChecked($search['dtkind'],'cdt')?>>�ֹ������</input>
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
</table>
<div class="button_top">
<input type="image" src="../img/btn_search2.gif"/>
</div>
</form>

<div style="padding-top:15px"></div>

<form method=post action="indb.php">
<input type=hidden name=mode value="repay">

<table width=100% cellpadding=2 cellspacing=0>
<tr><td class=rnd colspan=10></td></tr>
<tr class=rndbg>
	<th><a href="javascript:chkBox(document.getElementsByName('chk[]'),'rev')" class=white>����</a></th>
	<th><font class=small1><b>�ֹ���</th>
	<th><font class=small1><b>�ֹ������</th>
	<th><font class=small1><b>�ֹ���ȣ</th>
	<th><font class=small1><b>�ֹ���</th>
	<th><font class=small1><b>ó����</th>
	<th><font class=small1><b>��Ҽ���/�ֹ�����</th>
	<th><font class=small1><b>��������</th>

</tr>
<col align=center span=10>
<?
$i=0;
while ($data=$db->fetch($res,1)){
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

	// % ����, ��ǰ�� ����
	list($data[percentCoupon], $data[special_discount]) = $db->fetch("select sum(coupon * ea), sum(oi_special_discount_amount * ea) from gd_order_item where ordno = '$data[ordno]'");

	if($data[settleprice] >= $data[repay]){
		if($data['settleInflow'] == 'payco') {
			$orderDeliveryItem = &load_class('orderDeliveryItem','orderDeliveryItem');
			$orderDeliveryItem->ordno = $data['ordno'];//�ݺ������� �ֹ���ȣ�� ����� �� �־� ���� ����
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
			$repay = $data[repay];
			$repaymsg = "��ǰ�����ܰ�";
			if($ccnt == $cnt){
				$repaymsg = "��ǰ�����ܰ� + ��۷� - ������ - ������ - ���� - ��ǰ�� ���� + �������������";
				$repay = $repay + $data[delivery] - $data[enuri] - $data[emoney] - $data['ncash_emoney'] - $data['ncash_cash'] - ($data[coupon] - $data[percentCoupon]) + $data[eggFee];


			}
			if((int)$total_use_naver_mileage) $repaymsg .= " - ���̹����ϸ���";
			if((int)$total_use_naver_cash) $repaymsg .= " - ���̹�ĳ��";
		}
	}else $repay = $data[settleprice];

	if($data[cnt] == $cnt) $repaymsg = "�� �����ݾ�";
	if($repay < 0) $repay = 0;
	$repayfee = getRepayFee($repay);
	if($data[settleprice] < $data[repay]) $remoney = $data[repay] - $repay;



?>

<tr><td colspan=10 class=rndline></td></tr>
<tr>
	<td class=noline><input type=checkbox name=chk[] value="<?=$i?>"></td>
	<td><font class=ver71 color=444444><?=$data[orddt]?></td>
	<td><font class=ver71 color=444444><?=$data[canceldt]?></td>
	<td><a href="javascript:popup('popup.order.php?ordno=<?=$data[ordno]?>',800,600)"><font class=ver71 color=0074BA><b><?=$data[ordno]?></a></td>
	<td>
		<?php if($data[m_id]){ ?>
			<?php if($data['dormant_regDate'] == '0000-00-00 00:00:00'){ ?>
				<span id="navig" name="navig" m_id="<?=$data['m_id']?>" m_no="<?=$data['m_no']?>"><span class="small1" style="color:#0074BA"><?=$data['nameOrder']?></span></span>
			<?php } else { ?>
				<span class="small1" style="color:#0074BA"><?=$data['nameOrder']?>(�޸�ȸ��)</span>
			<?php } ?>
		<?php } else { ?>
			<span class="small1"><?=$data['nameOrder']?></span>
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

					<?if($cancel_delivery['view'][$item['delivery_type']][$item['goodsno']]['cnt'] > 1) {?><td rowspan="<?=$cancel_delivery['view'][$item['delivery_type']][$item['goodsno']]['cnt']?>">
					<?} else {?>
		<td>
					<?}?>

			<font class=ver7 color=0074BA>
				<b><?=number_format($cancel_delivery['view'][$item['delivery_type']][$item['goodsno']]['area_delivery_price'] + $cancel_delivery['view'][$item['delivery_type']][$item['goodsno']]['delivery_price'])?></b>
			</font>
		</td>
				<?unset($cancel_delivery['view'][$item['delivery_type']][$item['goodsno']]);?>
				<?}?>

			<?} else if($item['delivery_type'] == '5') {?>
		<td><font class=ver7 color=0074BA><b><?=number_format($cancel_delivery['view'][$item['delivery_type']][$item['optno']]['area_delivery_price'] + $cancel_delivery['view'][$item['delivery_type']][$item['optno']]['delivery_price'])?></font></b></td>
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
   	<div align=center><b>ȯ�� ���� : </b><select name=bankcode[]><option value="">==����==
		<? foreach ( $r_bank as $k=>$v){ ?>
		<option value="<?=$k?>"<?if(trim($k)==$data[bankcode])echo" selected";?>><?=$v?>
		<? } ?>
		</select>
		<input type=text name='bankaccount[]' value='<?=$data['bankaccount']?>'>
		<font class=ver71 color=444444>������</font> <input type=text name='bankuser[]' value='<?=$data[bankuser]?>'>
	</div>
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
		<td><font class=ver7 color=424242><input type=text name='repayfee[]' class=noline value='<?=$repayfee?>' <?=$readonly['repayfee'][$data['sno']]?> onchange="cal_repay(this.value,<?=$repay?>,<?=$data['settleprice']?>,<?=$before_refund_amount?>,<?=$i?>)" onkeydown="onlynumber()" style='text-align=right;background:#E9FFB3'>��</td>
		<td bgcolor=E9FFB3><input type=hidden name='repay[]' style='background:#DEFD33' value='' style='text-align=right' readonly><div style="font-weight:bold;color:#FD3C00;" id='viewrepay<?=$i?>'></div></td>
		<td bgcolor=E9FFB3>
		<span id="canceltype<?=$i?>">
		<?
			### ���ó�� �κ�
			if($data['settlekind'] == 'c' || $data['settlekind'] == 'u' || ($data['settlekind'] == 'e' && $data['settleInflow'] == 'payco') ){		// ī�����/������ �϶��� ��� �κ� ���
				if( $repay == $data['settleprice'] ){	// ��ü��� ( �����ݾװ� ȯ�ұݾ��� ���� �� )
					if( $data['pgcancel'] == 'y' ){
						echo "<strong>ī��������</strong>�� �ֹ��Դϴ�.<br/>ȯ�ҿϷ�ó�����ֽñ�ٶ��ϴ�.";
					} else if ($data['pgcancel'] == 'r' && (int)$cancel_info['rfee'] > 0) {
						echo "<strong style='color: #ff0000;'>ȯ�Ҽ�����</strong>�� �����ϰ� <strong>ī��������</strong>�� �ֹ��Դϴ�.<br/>ȯ�ҿϷ�ó�����ֽñ�ٶ��ϴ�.";
					}else{
						if($data['settleInflow'] == 'payco') echo "<a href=\"javascript:paycoCancel(".$i.", 'N', 0)\"><img src='../img/payco_cancel_btn.gif' ></a>";//������ ���� ��ü���(ī��/������ü)
						else echo "<a href=\"javascript:cardSettleCancel('".$data[ordno]."','".$data[sno]."',".$i.")\"><img src='../img/cardcancel_btn.gif' ></a>";
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
						if ((int)$cancel_info['rfee'] > 0) {
							echo "<strong style='color: #ff0000;'>ȯ�Ҽ�����</strong>�� �����ϰ� <strong>�κ����</strong>�� �ֹ��Դϴ�.<br/>ȯ�ҿϷ�ó�����ֽñ�ٶ��ϴ�.";
						}
						else {
							echo "<strong>�κ����</strong>�� �ֹ��Դϴ�.<br/>ȯ�ҿϷ�ó�����ֽñ�ٶ��ϴ�.";
						}
					}
				}
			}
			else if ($data['settlekind'] == 'h' && in_array($data['pg'], array('mobilians', 'payco', 'danal'))) {
				if ($repay == $data['settleprice']) {
					if ($data['pgcancel'] == 'y') {
						echo "<strong>�������</strong>�� �ֹ��Դϴ�.<br/>ȯ�ҿϷ�ó�����ֽñ�ٶ��ϴ�.";
					}
					else {
						if($data['settleInflow'] == 'payco') {
							if(substr($data['cdt'], 5, 2) == date('m')) echo "<a href=\"javascript:paycoCancel(".$i.", 'N', 0)\"><img src='../img/payco_cancel_btn.gif'></a>";//������ ���� ���(�޴���)
							else echo '�������� ���� �޴��� �������� ��Ұ� �Ұ��� �մϴ�.';
						}
						else if ($data['pg'] == 'danal') {
							echo '<img src="../img/payment_cancel_btn.jpg" onclick="ifrmHidden.location.href=\''.$cfg['rootDir'].'/order/card/danal/card_cancel.php?ordno='.$data['ordno'].'\';" style="cursor: pointer;"/>';
						}
						else echo '<img src="../img/payment_cancel_btn.jpg" onclick="ifrmHidden.location.href=\''.$cfg['rootDir'].'/order/card/mobilians/card_cancel.php?ordno='.$data['ordno'].'\';" style="cursor: pointer;"/>';
					}
				}
				else {
					if($data['settleInflow'] == 'payco') {
						if($cancel_info['pgcancel'] != 'r') echo "<a href=\"javascript:paycoCancel(".$i.", 'Y', 0)\"><img src='../img/payco_partcancel_btn.gif'></a>";//������ ���� �κ����
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
						if ((int)$cancel_info['rfee'] > 0) {
							echo "<strong style='color: #ff0000;'>ȯ�Ҽ�����</strong>�� �����ϰ� <strong>�κ����</strong>�� �ֹ��Դϴ�.<br/>ȯ�ҿϷ�ó�����ֽñ�ٶ��ϴ�.";
						}
						else {
							echo "<strong>�κ����</strong>�� �ֹ��Դϴ�.<br/>ȯ�ҿϷ�ó�����ֽñ�ٶ��ϴ�.";
						}
					}
				}
			}
			else {
				echo "ī��������� �ƴմϴ�.";
			}
		?>
		</span>
		</td>
	</tr>
	</table>

	<div align=center style="margin:3px;padding:5px;color:red;border:2px dotted red;" id="el-over-refund-message<?=$i?>">
	���߿�!  �� ȯ�ұݾ��� �� �����ݾ��� �ʰ��Ͽ����ϴ�. ȯ���Ͻð��� �ϴ� �ݾ��� �´��� �ٽ� �ѹ� Ȯ���� �ּ���.
	</div>

	<div align=center style="padding-top:5">�� �ֹ������� ����� �������� �� <font color=0074BA><b><?=number_format($data[emoney])?>��</b></font> �Դϴ�.&nbsp;&nbsp;������ ����� ������ �� <input type=text name='remoney[]' style='text-align=right;background:#E9FFB3' onkeydown='onlynumber();' value='0'>���� �ǵ����ݴϴ�.</div>
	<?
	if($agoemoney){
	?>
	<div align=center style="padding-top:5">������� �� ����ֹ����� �ǵ����� �������� �� <font color=0074BA><b><?=number_format($agoemoney)?>��</b></font> �Դϴ�.</div>
	<?}?>
	</td>
</tr>
<tr><td colspan=10 bgcolor='#616161' height=3></td></tr>
<tr><td colspan=10 height=10></td></tr>
<script>cal_repay(<?=$repayfee?>,<?=$repay?>,<?=$data['settleprice']?>,<?=$before_refund_amount?>,<?=$i?>);</script>
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
