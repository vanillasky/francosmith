<?php
/**
 * ���̹�üũ�ƿ� �ֹ� > �ֹ����ó��
 * @author sunny, oneorzero
 */
$location = '���̹�üũ�ƿ� �ֹ� > �ֹ����ó��';
include '../_header.php';

$page = $_GET['page'] ? $_GET['page'] : 1;
$sk = $_GET['sk'];
$sv = $_GET['sv'];
$code = (array)$_GET['code'];
$orddt = $_GET['orddt'];

$ORDER_OrderDateTimeStart = preg_replace('/^(\d{4})(\d{2})(\d{2})$/','$1-$2-$3',$orddt[0]);
$ORDER_OrderDateTimeEnd = preg_replace('/^(\d{4})(\d{2})(\d{2})$/','$1-$2-$3',$orddt[1]);

$arWhere=array();
$arWhere[] = "ORDER_OrderStatusCode in ('OD0007','OD0008','OD0009','OD0010','OD0011')";

if($sv) {
	$sv=$db->_escape($sv);
	switch($sk) {
		case 'ORDER_OrderID':
			$arWhere[] = "ORDER_OrderID = '{$sv}'";
		break;
		case 'ORDER_OrdererName':
			$arWhere[] = "ORDER_OrdererName = '{$sv}'";
		break;
		case 'ORDER_OrdererID':
			$arWhere[] = "ORDER_OrdererID = '{$sv}'";
		break;
		case 'SHIPPING_Recipient':
			$arWhere[] = "SHIPPING_Recipient = '{$sv}'";
		break;
	}
}

if($ORDER_OrderDateTimeStart && $ORDER_OrderDateTimeEnd) {
	$arWhere[] = $db->_query_print('ORDER_OrderDateTime between [s] and [s]',$ORDER_OrderDateTimeStart,$ORDER_OrderDateTimeEnd);
}
elseif($ORDER_OrderDateTimeStart) {
	$arWhere[] = $db->_query_print('ORDER_OrderDateTime >= [s]',$ORDER_OrderDateTimeStart);
}
elseif($ORDER_OrderDateTimeEnd) {
	$arWhere[] = $db->_query_print('ORDER_OrderDateTime <= [s]',$ORDER_OrderDateTimeStart,$ORDER_OrderDateTimeEnd);
}

$strWhere = 'where '.implode(' and ',$arWhere);

$query = "
	select
		orderNo,
		ORDER_OrderID,
		ORDER_OrderDateTime,
		ORDER_OrderStatusCode,
		ORDER_OrderStatus,
		ORDER_OrdererName,
		ORDER_OrdererID,
		ORDER_MallOrderAmount,
		SHIPPING_Recipient
	from
		gd_navercheckout_order
	{$strWhere}
	order by
		ORDER_OrderDateTime desc
";
$orderList = $db->_select_page(10,$page,$query);

$orderNoList =array();
foreach($orderList['record'] as $eachOrder) {
	$orderNoList[]='"'.$eachOrder['orderNo'].'"';
}
if(count($orderNoList)) {
	$strWhere = implode(',',$orderNoList);
	$query = "select * from gd_navercheckout_order_product where orderNo in ($strWhere) order by orderNo asc,seq asc";
	$result = $db->_select($query);
	$orderProductList=array();
	foreach($result as $eachResult) {
		$orderProductList[$eachResult['orderNo']][]=$eachResult;
	}
}
?>
<? include('checkout.common.php'); ?>
<script type="text/javascript">
function apiCall() {
	var test=false;
	$$('input[name="orderNo[]"]').each(function(item){
		if(item.checked) {test=true; return;}
	});

	if(test==false) {
		alert("�ֹ��� �������ּ���");
		return;
	}

	if($('chkFrm').CancelReasonDetail.value.length==0) {
		alert('�Ǹ� ��� �޼����� �����ּ���');
		return;
	}

	customPopupLayer('about:blank',780,500);
	$('chkFrm').submit();
}
</script>
<div class="title title_top">�ֹ����ó�� <span>�ֹ��� ���� Ȯ���� �Ϸ�� �ֹ����� ����ϱ� ���� �ܰ��Դϴ�</span></div>
<form name="frmSearch" method="get">
<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td><span class="small1">Ű����˻�</span></td>
	<td>
		<select name="sk">
			<option value="ORDER_OrderID" <?=frmSelected($_GET['sk'],'ORDER_OrderID')?>>�ֹ���ȣ</option>
			<option value="ORDER_OrdererName" <?=frmSelected($_GET['sk'],'ORDER_OrdererName')?>>�ֹ����̸�</option>
			<option value="ORDER_OrdererID" <?=frmSelected($_GET['sk'],'ORDER_OrdererID')?>>�ֹ��ھ��̵�(���̹�)</option>
			<option value="SHIPPING_Recipient" <?=frmSelected($_GET['sk'],'SHIPPING_Recipient')?>>������ �̸�</option>
		</select>
		<input type="text" name="sv" value="<?=htmlspecialchars($_GET['sv'])?>" class="line">
	</td>
</tr>
<tr>
	<td><span class="small1">�ֹ��Ͻ�</span></td>
	<td>
		<input type=text name=orddt[] value="<?=$_GET[orddt][0]?>" onclick="calendar(event)" size=12 class=line> -
		<input type=text name=orddt[] value="<?=$_GET[orddt][1]?>" onclick="calendar(event)" size=12 class=line>
	</td>
</tr>
</table>

<table width="100%">
<tr>
	<td align="center">
	<input type="image" src="../img/btn_search2.gif" border="0" style="border:0px">
	</td>
</tr>
</table>
</form>


<form id="chkFrm" action="checkout.api.CancelOrder.php" target="processLayerForm" method="post">
<br><br>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr><td class="rnd" colspan="11"></td></tr>
<tr class="rndbg">
	<th><span onclick="chkAllOrderNO()" style="cursor:pointer">����</a></th>
	<th>�ֹ���ȣ</th>
	<th>����</th>
	<th>�ֹ���</th>
	<th>�ֹ���</th>
	<th>��ǰ��</th>
	<th>�ɼ�</th>
	<th>����</th>
	<th>�����ݾ�</th>
	<th>������</th>
</tr>
<tr><td class="rnd" colspan="11"></td></tr>
<col align="center" width="40"/>
<col align="center" width="120"/>
<col align="center" width="90"/>
<col align="center" width="110" />
<col align="center" width="100" />
<col align="center" />
<col align="center" width="90" />
<col align="center" width="50" />
<col align="center" width="100" />
<col align="center" width="70" />

<? foreach($orderList['record'] as $eachOrder): ?>
<?
	$eachOrder['ORDER_OrderDateTime'] = preg_replace('/^\d{2}(\d{2})-(\d+)-(\d+) (\d+):(\d+):(\d+)$/','$1.$2.$3 $4:$5',$eachOrder['ORDER_OrderDateTime']);
	$orderProduct = $orderProductList[$eachOrder['orderNo']];
	$firstProduct = array_shift($orderProduct);
?>
<tr>
	<td class="noline"><input type="checkbox" name="orderNo[]" value="<?=$eachOrder['orderNo']?>"></td>
	<td height="23"><a href="checkout.orderdetail.php?orderNo=<?=$eachOrder['orderNo']?>"><?=$eachOrder['ORDER_OrderID']?></a></td>
	<td><?=$eachOrder['ORDER_OrderStatus']?></td>
	<td><?=$eachOrder['ORDER_OrderDateTime']?></td>
	<td><?=$eachOrder['ORDER_OrdererName']?>(<?=$eachOrder['ORDER_OrdererID']?>)</td>
	<td><?=$firstProduct['ProductName']?></td>
	<td><?=$firstProduct['ProductOption']?></td>
	<td><?=$firstProduct['Quantity']?></td>
	<td><?=number_format($eachOrder['ORDER_MallOrderAmount'])?></td>
	<td><?=$eachOrder['SHIPPING_Recipient']?></td>
</tr>
<? foreach($orderProduct as $eachProduct): ?>
<tr>
	<td height="23"></td>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
	<td><?=$eachProduct['ProductName']?></td>
	<td><?=$eachProduct['ProductOption']?></td>
	<td><?=$eachProduct['Quantity']?></td>
	<td></td>
	<td></td>
</tr>
<? endforeach; ?>
<tr><td height="4"></td></tr>
<tr><td colspan="16" class="rndline"></td></tr>
</tr>
<? endforeach; ?>

</table>


<? $pageNavi = &$orderList['page']; ?>
<div align="center" class="pageNavi ver8">
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

<br>
<div style="border:1px solid #cccccc;padding:10px;">
<table cellpadding="0">
<tr>
	<td>�ֹ���� ���� :</td>
	<td>
	<select name="CancelReason" style="width:100px">
	<option value="31"> �ʿ伺 ���</option>
	<option value="32"> �ܼ� ����</option>
	<option value="33"> ���� ���� ����</option>
	<option value="34"> ���� �Ҹ�</option>
	<option value="35"> �̹� ��������(��������)</option>
	<option value="36"> ���� �ֹ�</option>
	<option value="37"> ��Ÿ</option>
	</select>
	</td>
<tr>
	<td>�ֹ���� �޼��� :</td>
	<td><input type="text" name="CancelReasonDetail" style="width:300px"></td>
</tr>
<tr>
	<td></td>
	<td> <input type="button" value=" ������ �ֹ� ����ϱ� " onclick="apiCall()"></td>
</tr>
</table>
</div>
</form>


<div style="margin-top:30px;"></div>
<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle"/>�ֹ��� ���� Ȯ��(����)�� �Ϸ�� �ֹ��Դϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"/>�ʿ伺 ��� ������ ���� ����� ��쿡�� ���ֹ���ҡ��� ���� �ֹ��� ����� �ּ���.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

<? include '../_footer.php'; ?>