<?php
$location = "�ֹ����� > ��ǰ/��ȯ��������Ʈ";
include "../_header.php";
include "../../lib/page.class.php";

### ��һ��� �迭����
$r_cancel = codeitem("cancel");

if (get_magic_quotes_gpc()) {
	stripslashes_all($_POST);
	stripslashes_all($_GET);
}

// $_GET���� �޴� ��� �� ����
$search=array(
	'regdt_start'=>(string)$_GET['regdt'][0], // ó������ ����
	'regdt_end'=>(string)$_GET['regdt'][1], // ó������ ��
	'sword'=>trim((string)$_GET['sword']), // �˻���
	'skey'=>($_GET['skey'] ? (string)$_GET['skey'] : 'all'), // �˻��ʵ�
	'cancelkey'=>($_GET['cancelkey'] ? (string)$_GET['cancelkey'] : 'all'), // ��һ���
);
$page = (int)$_GET['page'] ? (int)$_GET['page'] : 1;

// ��������
if(!in_array($search['skey'],array('all','ordno','nameOrder','goodsnm','name'))) { exit; }
if(!in_array($search['cancelkey'],array('all','0','1','2','3','4','5','6','7','8','9','10'))) { exit; }

// �������� ���� �˻����� �����
$arWhere = array();
$strWhere = "";
if($search['regdt_start']) {
	if(!$search['regdt_end']) $search['regdt_end'] = date('Ymd');
	$tmp_start = substr($search['regdt_start'],0,4).'-'.substr($search['regdt_start'],4,2).'-'.substr($search['regdt_start'],6,2).' 00:00:00';
	$tmp_end = substr($search['regdt_end'],0,4).'-'.substr($search['regdt_end'],4,2).'-'.substr($search['regdt_end'],6,2).' 23:59:59';

	$arWhere[] = $db->_query_print('oc.regdt between [s] and [s]',$tmp_start,$tmp_end);
}
if($search['sword'] && $search['skey']) {
	$es_sword = $db->_escape($search['sword']);
	switch($search['skey']) {
		case 'all':
			$arWhere[] = "(
				o.ordno = '{$es_sword}' or
				o.nameOrder like '%{$es_sword}%' or
				oi.goodsnm like '%{$es_sword}%' or
				oc.name like '%{$es_sword}%'
			)"; break;
		case 'ordno': $arWhere[] = "o.ordno = '{$es_sword}'"; break;
		case 'nameOrder': $arWhere[] = "o.nameOrder like '%{$es_sword}%'"; break;
		case 'goodsnm': $arWhere[] = "oi.goodsnm like '%{$es_sword}%'"; break;
		case 'name': $arWhere[] = "oc.name like '%{$es_sword}%'"; break;
	}
}
if($search['cancelkey']) {
	$es_cancelkey = $db->_escape($search['cancelkey']);
	if($es_cancelkey != 'all'){
		$arWhere[] = "oc.code = '{$es_cancelkey}'";
	}
}

if(count($arWhere)) {
	$strWhere = 'and '.implode(' and ',$arWhere);
}

// ���� ����
@include './checkout._order_regoods.php'; // Checkout include
if($isEnableAdminCheckoutOrder !== true) {
	$query = '
		select SQL_CALC_FOUND_ROWS
			oc.sno as sno,
			oc.regdt as canceldt,
			oc.name as nameCancel,
			oc.code as code,
			o.ordno as ordno,
			o.orddt as orddt,
			o.nameOrder as nameOrder,
			o.settlekind as settlekind,o.pg,
			`o`.`ipay_payno`,
			`o`.`ipay_cartno`,
			o.ncash_tx_id,
			m.m_no as m_no,
			m.m_id as m_id,
			m.dormant_regDate as dormant_regDate,
			o.settleInflow as settleInflow
		from
			gd_order_cancel as oc
			inner join gd_order_item as oi on oc.sno=oi.cancel and oc.ordno = oi.ordno
			inner join gd_order as o on oi.ordno=o.ordno
			left join gd_member as m on o.m_no=m.m_no
		where
			oi.istep> 40 and oi.cyn = "y" and oi.dyn = "y" '.$strWhere.'
		group by
			oc.sno
		order by
			oc.sno desc
	';
	$regoodsResult = $db->_select_page(10,$page,$query);
}
?>

<script type="text/javascript">
/**
* ��ü����
*/
var flagChkAll=true;
function chkAll() {
	$$(".chkSno").each(function(item){
		item.checked=flagChkAll;
	});
	flagChkAll=!flagChkAll;
}

/**
* ��ǰ�Ϸ�ó��
*/
function indbReturn() {
	var checked=false;

	var arChk=document.getElementsByName('chk[]');
	var length=arChk.length;
	for(i=0;i<length;i++) {
		if(arChk[i].checked) {checked=true;break;}
	}

	// ���̹�üũ�ƿ�
	if (typeof(indbCheckoutReturn) == 'function') {
		if (indbCheckoutReturn() === true) {
			checked = true;
		}
	}

	if(checked==false) {
		alert("��ǰó���Ͻ� �ֹ��� �������ּ���");
		return;
	}

	if($$('input[name^="chk["][data-ipay-pg="true"]:checked').size()>0)
	{
		if(confirm("�����Ͻ� �ֹ����߿� iPay PG���� �ֹ����� ���ԵǾ��ֽ��ϴ�.\r\niPay PG���� �ֹ����� ��ǰ�Ϸ� ó���� �ٷ� ȯ��ó�� �˴ϴ�.\r\n��� �����Ͻðڽ��ϱ�?")) {
			document.frmOrder.mode.value="regoods";
			document.frmOrder.submit();
		}
	}
	else
	{
		if(window.confirm("������ ��ǰó���� �Ͻðڽ��ϱ�?")) {
			document.frmOrder.mode.value="regoods";
			document.frmOrder.submit();
		}
	}
}

/**
* ��ȯ�Ϸ� �� ���ֹ� �ֱ�
*/
function indbExchange() {
	// ���̹�üũ�ƿ�
	if (typeof(indbCheckoutExchange) == 'function') {
		if (indbCheckoutExchange() === false) {
			return;
		}
	}

	var arChk=document.getElementsByName('chk[]');
	var length=arChk.length;
	var checked=false;
	for(i=0;i<length;i++) {
		if(arChk[i].checked) {checked=true;break;}
	}
	if(checked==false) {
		alert("��ȯó���Ͻ� �ֹ��� �������ּ���");
		return;
	}

	if($$('input[name^="chk["][data-ipay-pg="true"]:checked').size()>0)
	{
		alert("�����Ͻ� �ֹ��ǵ��� iPay PG���� �ֹ����� �ֽ��ϴ�.\r\niPay PG������ ������ �ֹ����� ��ȯó���� �Ұ��� �Ͽ���\r\n�ش�ǵ��� üũ�� �����Ͽ��ֽñ� �ٶ��ϴ�.\r\n(iPay PG���� �ֹ����� �ֹ���ȣ�տ� iPay�������� �ֽ��ϴ�.)");
		return;
	}
	else if($$('input[name^="chk["][data-naver-mileage="true"]:checked').size()>0)
	{
		alert("�����Ͻ� �ֹ����߿� ���̹� ���ϸ����� ���� �ֹ����� �ֽ��ϴ�.\r\n���̹� ���ϸ����� ���� �ֹ����� ��ȯó���� �Ұ��� �Ͽ���\r\n�ش��ֹ����� üũ�� �����Ͽ��ֽñ� �ٶ��ϴ�.");
		return;
	}
	else
	{
		if(window.confirm("������ ��ȯó���� �Ͻðڽ��ϱ�?")) {
			document.frmOrder.mode.value="exc_ok";
			document.frmOrder.submit();
		}
	}
}
</script>
<div class="title title_top">��ǰ/��ȯ��������Ʈ<span>��ǰ/��ȯ ������ �ֹ����� ��ȸ�ϰ� ��ǰ/��ȯ�Ϸ�ó���� �����մϴ�.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=order&no=4')"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2"/></a></div>

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
	<option value="name" <?=frmSelected($search['skey'],'name');?>> �����</option>
	</select>
	<input type="text" name="sword" value="<?=htmlspecialchars($search['sword'])?>" class="line"/>
	</td>
</tr>
<tr>
	<td><span class="small1">����</span></td>
	<td>
	<select name="cancelkey">
	<option value="all"> = �����ϼ��� = </option>
	<option value="1" <?=frmSelected($search['cancelkey'],'1');?>> ���������</option>
	<option value="2" <?=frmSelected($search['cancelkey'],'2');?>> ǰ�����</option>
	<option value="3" <?=frmSelected($search['cancelkey'],'3');?>> ����������</option>
	<option value="4" <?=frmSelected($search['cancelkey'],'4');?>> �����ֹ����</option>
	<option value="5" <?=frmSelected($search['cancelkey'],'5');?>> �ý��ۿ������</option>
	<option value="6" <?=frmSelected($search['cancelkey'],'6');?>> �������</option>
	<option value="7" <?=frmSelected($search['cancelkey'],'7');?>> �ù�н�</option>
	<option value="8" <?=frmSelected($search['cancelkey'],'8');?>> ��ǰ�ҷ�</option>
	<option value="9" <?=frmSelected($search['cancelkey'],'9');?>> ��Ÿ</option>
	<option value="10" <?=frmSelected($search['cancelkey'],'10');?>> �ŷ��̼���</option>
	</select>
	</td>
</tr>
<tr>
	<td><span class="small1">��ǰ��û��</span></td>
	<td colspan="3">
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

<form method="post" action="indb.php" name="frmOrder">
<input type="hidden" name="mode" value="regoods"/>

<table width="100%" cellpadding="2" cellspacing="0">
<tr><td class="rnd" colspan="10"></td></tr>
<tr class="rndbg">
	<th><a href="javascript:chkAll()" class="white">����</a></th>
	<th><span class="small1"><b>�ֹ���</b></span></th>
	<th><span class="small1"><b>��ǰ��û��</b></span></th>
	<th><span class="small1"><b>�ֹ���ȣ</b></span></th>
	<th><span class="small1"><b>��ǰ����</b></span></th>
	<th><span class="small1"><b>�ֹ���</b></span></th>
	<th><span class="small1"><b>�����</b></span></th>
</tr>
<col align="center" span="10">
<?php
foreach($regoodsResult['record'] as $data):
	if($data['_order_type']=='checkout'):
?>

<tr style="background-color:#ebfce4">
	<td class="noline" style="height:30px"><input type="checkbox" name="checkoutNo[]" value="<?=$data[sno]?>" class="chkSno"/></td>
	<td><span class="ver7" style="color:#444444"><?=substr($data[orddt],0,10)?></span></td>
	<td><span class="ver7" style="color:#444444"><?=substr($data[canceldt],0,10)?></span></td>
	<td><a href="javascript:popup('popup.order.php?ordno=<?=$data[ordno]?>',800,600)"><span class="ver71" style="color:#0074BA"><b><?=$data[ordno]?></b></span></a></td>
	<td><span class="small1" style="color:#444444"><?=$data[code]?></span></td>
	<td><span class="small1"><?=$data[nameOrder]?></span></td>
	<td><span class="small1" style="color:#444444"><?=$data[nameCancel]?></span></td>
</tr>
<tr style="background-color:#ebfce4">
	<td colspan="20" style="font-size:7pt;color:#666666">
		���̹�üũ�ƿ��ֹ��Դϴ�. ���̹�üũ�ƿ����Ϳ��� ��ǰ/��ȯó�� �� "��ǰ�Ϸ�" ������ ���ּ���
	</td>
</tr>
<tr><td colspan="10" class="rndline"></td></tr>

<?php
	else:
?>

<tr>
	<td class="noline"><input type="checkbox" name="chk[]" value="<?=$data[sno]?>" class="chkSno"<?php if($data['pg']==='ipay') echo ' data-ipay-pg="true"'; if(strlen(trim($data['ncash_tx_id']))) echo ' data-naver-mileage="true"'; ?>/></td>
	<td><span class="ver7" style="color:#444444"><?=substr($data[orddt],0,10)?></span></td>
	<td><span class="ver7" style="color:#444444"><?=substr($data[canceldt],0,10)?></span></td>
	<td>
		<?php if(strlen(trim($data['ncash_tx_id']))) echo '<img style="vertical-align: middle;" src="/shop/admin/img/naver_mileage.jpg"/>'; ?>
		<?php if($data['pg']==='ipay'){ ?><img src="<?php echo $cfg['rootDir']; ?>/admin/img/icon_int_order_ipay.gif" style="vertical-align: middle;"/><?php } ?>
		<?=settleIcon($data['settleInflow']);?> <a href="javascript:popup('popup.order.php?ordno=<?=$data[ordno]?>',800,600)"><span class="ver71" style="color:#0074BA"><b><?=$data[ordno]?></b></span></a>
	</td>
	<td><span class="small1" style="color:#444444"><?=$r_cancel[$data[code]]?></span></td>
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
	<td><span class="small1" style="color:#444444"><?=$data[nameCancel]?></span></td>
</tr>
<tr><td colspan="10" class="rndline"></td></tr>
<tr>
	<td colspan="10" style="padding:5px 10px" align="left">
	<table width="100%" border="1" bordercolor="#dedede" style="border-collapse:collapse">
	<tr bgcolor="#f7f7f7" height="22">
		<th><span class="small1" style="color:#444444"><b>��ǰ��</b></span></th>
		<th width="80" nowrap><span class="small1" style="color:#444444"><b>��ǰ����</b></span></th>
		<th width="80" nowrap><span class="small1" style="color:#444444"><b>��ǰ����</b></span></th>
		<?php if($data['pg']==='ipay'){ ?>
		<th width="80" nowrap><span class="small1" style="color:#444444"><b>�� ����</b></span></th>
		<th width="80" nowrap><span class="small1" style="color:#444444"><b>�Ұ�</b></span></th>
		<?php }else{ ?>
		<th width="80" nowrap><span class="small1" style="color:#444444"><b>ȸ������</b></span></th>
		<th width="80" nowrap><span class="small1" style="color:#444444"><b>��������</b></span></th>
		<th width="80" nowrap><span class="small1" style="color:#444444"><b>��ǰ�����ܰ�</b></span></th>
		<?php } ?>
		<th width="50" nowrap><span class="small1" style="color:#444444"><b>����</b></span></th>
	</tr>
	<col><col align="center" span="10">
	<?
	$query = "
	select b.*,a.* from
		".GD_ORDER_ITEM." a
		left join ".GD_GOODS." b on a.goodsno=b.goodsno
	where
		a.cancel='$data[sno]'
		and a.ordno='$data[ordno]'
	";
	$res2 = $db->query($query);
	while ($item=$db->fetch($res2)){
	?>
	<tr>
		<td>

		<table>
		<tr>
			<td><a href="../../goods/goods_view.php?goodsno=<?=$item[goodsno]?>" target="_blank"><?=goodsimg($item[img_s],20,"style='border:1 solid #cccccc'",1)?></a></td>
			<td style="padding-left:3px"><span class="small" style="color:#444444">
			<a href="../../goods/goods_view.php?goodsno=<?=$item[goodsno]?>" target="_blank"><span style="color:#0074BA"><b><?=$item[goodsnm]?></b></span></a>
			<? if ($item[opt1]){ ?>[<?=$item[opt1]?><? if ($item[opt2]){ ?>/<?=$item[opt2]?><? } ?>]<? } ?>
			<? if ($item[addopt]){ ?><div>[<?=str_replace("^","] [",$item[addopt])?>]</div><? } ?>
			</span></td>
		</tr>
		</table>

		</td>
		<td><span class="ver7" style="color:#444444"><?=number_format($item[price])?></span></td>
		<td><span class="ver7" style="color:#444444"><?=number_format($item[oi_special_discount_amount])?></span></td>
		<?php if($data['pg']==='ipay'){ ?>
		<td><span class="ver7" style="color:#444444"><?php echo number_format($item['ipay_dcprice']); ?></span></td>
		<td><span class="ver7" style="color:#444444"><?php echo number_format(($item['price']*$item['ea'])-$item['ipay_dcprice']); ?></span></td>
		<?php }else{ ?>
		<td><span class="ver7" style="color:#444444"><?=number_format($item[memberdc])?></span></td>
		<td><span class="ver7" style="color:#444444"><?=number_format($item[coupon])?></span></td>
		<td><span class="ver7" style="color:#444444"><?=number_format($item[price]-$item[memberdc]-$item[coupon]-$item[oi_special_discount_amount])?></span></td>
		<?php } ?>
		<td><span class="ver7" style="color:#444444"><?=number_format($item[ea])?></span></td>
	</tr>
	<? } ?>
	</table>
	</td>
</tr>
<tr><td colspan="10" class="rndline"></td></tr>

<?php
	endif;
endforeach;
?>
</table>

<?php $pageNavi = &$regoodsResult['page']; ?>
<div class=pageNavi align=center>
	<? if($pageNavi['first']): ?>
		<a href="?<?=getvalue_chg('page',$pageNavi['first'])?>">[1]</a>
	<? endif; ?>
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
	<? if($pageNavi['last']): ?>
		<a href="?<?=getvalue_chg('page',$pageNavi['last'])?>">[<?=$pageNavi['last']?>]</a>
	<? endif; ?>
</div>


<div class="button">
<a href="javascript:indbReturn()"><img src="../img/btn_returngood.gif"/></a>
<a href="javascript:indbExchange()"><img src="../img/btn_exchangegood.gif"/></a>
<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"/></a>
</div>

</form>


<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><b>:: ��ǰ�Ϸ� ó���ϱ� ::</b></td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"/>��ǰ�ֹ��̶� '�����'�̰ų� '��ۿϷ�'�� ���¿��� ���� ��û���� �ֹ���Ұ� �� �ֹ�</font>�� ���մϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"/>�ֹ���Ұ� �Ǿ����Ƿ� ��۵� ��ǰ�� �ٽ� �ݼ۹��� �� ��ǰ�� Ȯ���ϰ� ��ǰ�Ϸ�ó���� �����մϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"/>�̰����� ��ǰ�Ϸ�ó���� �ֹ��� ȯ����������Ʈ'�� �̵��ϰ� �ǰ� ���������� ȯ���� �Ϸ��ؾ߸� ��ǰ�ǿ� ���� �������� �̷�����ϴ�. </td></tr>

<tr><td height="10"></td></tr>

<tr><td><b>:: ��ȯ�Ϸ� �� ���ֹ��ֱ� ::</b></td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"/>��ȯ�� �±�ȯ�� �����մϴ�. �±�ȯ�̶� �ļյ� �ҷ���ǰ�̰ų� ���ڰ� �ִ� ��ǰ�� ��� ���� ��ǰ������ ��ȯó��</font>�� ���մϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"/>��ȯ�� ��û�� �����κ��� ��۵� ��ǰ�� �ݼ۹޾� �̰����� '��ȯ�Ϸ� �� ���ֹ��ֱ�' ��ư�� ���� ó���մϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"/>'��ȯ�Ϸ� �� ���ֹ��ֱ�' ��ư�� �����ԵǸ� �ڵ����� ���� ��ǰ, ���� �������� ���ֹ��� �����˴ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"/>�̷��� ������ ���ֹ��� �ֹ�����Ʈ���� <img src="../img/icon_twice_order.gif"/> (���ֹ�������)���� ǥ�õǾ� �ֹ������Ǹ�, �ٷ� �Ա�Ȯ�� ó���� ������ �����ϸ� �˴ϴ�.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>


<script>window.onload = function(){ UNM.inner();};</script>

<? include "../_footer.php"; ?>