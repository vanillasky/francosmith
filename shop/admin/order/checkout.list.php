<?php
/**
 * ���̹�üũ�ƿ� �ֹ� > ��ü�ֹ���ȸ
 * @author sunny, oneorzero
 */
$location = '���̹�üũ�ƿ� �ֹ� > ��ü�ֹ���ȸ';
include '../_header.php';

$page = $_GET['page'] ? $_GET['page'] : 1;
$sk = $_GET['sk'];
$sv = $_GET['sv'];
$code = (array)$_GET['code'];
$orddt = $_GET['orddt'];

$ORDER_OrderDateTimeStart = preg_replace('/^(\d{4})(\d{2})(\d{2})$/','$1-$2-$3',$orddt[0]);
$ORDER_OrderDateTimeEnd = preg_replace('/^(\d{4})(\d{2})(\d{2})$/','$1-$2-$3',$orddt[1]);

$arWhere=array();
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
		case 'ORDER_MallMemberID':
			$arWhere[] = "ORDER_MallMemberID = '{$sv}'";
		break;
		case 'SHIPPING_Recipient':
			$arWhere[] = "SHIPPING_Recipient = '{$sv}'";
		break;
	}
}
if(count($code)) {
	$tmp=array();
	foreach($code as $eachCode) {
		$tmp[] = '"'.$eachCode.'"';
	}
	$tmp = implode(',',$tmp);
	$arWhere[] = "ORDER_OrderStatusCode in ({$tmp})";
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

if(count($arWhere)) {
	$strWhere = 'where '.implode(' and ',$arWhere);
}

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

<script type="text/javascript">
document.observe("dom:loaded", function() {
	var flagCancel=true;
	var flagReturn=true;
	var flagExchange=true;
	Event.observe($("btnAllCancel"), 'click', function(event) {
		var element = Event.element(event);
		var checkValue = ['OD0003','OD0004','OD0005','OD0006'];
		var tmp;
		$$("input[name='code[]']").each(function(item){
			tmp=false;
			checkValue.each(function(v){
				if(item.value==v) {
					tmp=true;
				}
			});
			if(tmp) item.checked=flagCancel;
		});
		flagCancel = !flagCancel;
	});
	Event.observe($("btnAllExchange"), 'click', function(event) {
		var element = Event.element(event);
		var checkValue = ['OD0014','OD0015','OD0016','OD0017','OD0018','OD0019','OD0020','OD0021','OD0022','OD0023','OD0024','OD0025'];
		var tmp;
		$$("input[name='code[]']").each(function(item){
			tmp=false;
			checkValue.each(function(v){
				if(item.value==v) {
					tmp=true;
				}
			});
			if(tmp) item.checked=flagExchange;
		});
		flagExchange = !flagExchange;

	});
	Event.observe($("btnAllReturn"), 'click', function(event) {
		var element = Event.element(event);
		var checkValue = ['OD0026','OD0027','OD0028','OD0029','OD0030','OD0031','OD0032','OD0033','OD0034','OD0035'];
		var tmp;
		$$("input[name='code[]']").each(function(item){
			tmp=false;
			checkValue.each(function(v){
				if(item.value==v) {
					tmp=true;
				}
			});
			if(tmp) item.checked=flagReturn;
		});
		flagReturn = !flagReturn;

	});
});

</script>
<div class="title title_top">��ü�ֹ���ȸ <span>���̹�üũ�ƿ��� �ֹ�/��� �����Դϴ�. �ֹ���ȣ�� Ŭ���Ͻø� �������� Ȯ���ϽǼ� �ֽ��ϴ�.</span></div>
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
			<option value="ORDER_MallMemberID" <?=frmSelected($_GET['sk'],'ORDER_MallMemberID')?>>�ֹ��ھ��̵�(���θ�)</option>
			<option value="SHIPPING_Recipient" <?=frmSelected($_GET['sk'],'SHIPPING_Recipient')?>>������ �̸�</option>
		</select>
		<input type="text" name="sv" value="<?=htmlspecialchars($_GET['sv'])?>" class="line">
	</td>
</tr>
<tr>
	<td valign="top"><span class="small1">�ֹ�����</span></td>
	<td class="noline">
	<table >
	<tr>
		<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0001" <?=(in_array('OD0001',$code)?'checked':'')?>>�Ա� ���</td>
		<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0002" <?=(in_array('OD0002',$code)?'checked':'')?>>���� �Ϸ�</td>
		<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0003" <?=(in_array('OD0003',$code)?'checked':'')?>>�ֹ� ���(�Ա� ���� ����)</td>
		<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0004" <?=(in_array('OD0004',$code)?'checked':'')?>>�ֹ� ���(�̿��� ���)</td>
		<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0005" <?=(in_array('OD0005',$code)?'checked':'')?>>�ֹ� ���(���� ���)</td>
		<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0006" <?=(in_array('OD0006',$code)?'checked':'')?>>�Ǹ� ���</td>
	</tr>
	<tr>
		<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0007" <?=(in_array('OD0007',$code)?'checked':'')?>>�߼� ó�� ��û</td>
		<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0008" <?=(in_array('OD0008',$code)?'checked':'')?>>�̹߼�</td>
		<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0009" <?=(in_array('OD0009',$code)?'checked':'')?>>��ȭ ����</td>
		<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0010" <?=(in_array('OD0010',$code)?'checked':'')?>>��� ���� ���� ���</td>
		<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0011" <?=(in_array('OD0011',$code)?'checked':'')?>>��� ���� ����</td>
		<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0012" <?=(in_array('OD0012',$code)?'checked':'')?>>��� ��</td>
	</tr>
	<tr>
		<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0013" <?=(in_array('OD0013',$code)?'checked':'')?>>��� �Ϸ�</td>
		<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0014" <?=(in_array('OD0014',$code)?'checked':'')?>>��ȯ ��û ��</td>
		<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0015" <?=(in_array('OD0015',$code)?'checked':'')?>>��ȯ ��û ����</td>
		<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0016" <?=(in_array('OD0016',$code)?'checked':'')?>>��ȯ ��û ����</td>
		<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0017" <?=(in_array('OD0017',$code)?'checked':'')?>>���� ����(��ȯ ��û ����)</td>
		<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0018" <?=(in_array('OD0018',$code)?'checked':'')?>>�ݼ� ��(��ȯ)</td>
	</tr>
	<tr>
		<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0019" <?=(in_array('OD0019',$code)?'checked':'')?>>��ȯ �ɻ� ��</td>
		<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0020" <?=(in_array('OD0020',$code)?'checked':'')?>>��ȯ ����</td>
		<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0021" <?=(in_array('OD0021',$code)?'checked':'')?>>��ȯ ����</td>
		<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0022" <?=(in_array('OD0022',$code)?'checked':'')?>>���� ����(��ȯ ����)</td>
		<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0023" <?=(in_array('OD0023',$code)?'checked':'')?>>���� ��</td>
	</tr>
	<tr>
		<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0024" <?=(in_array('OD0024',$code)?'checked':'')?>>��ȯ �Ϸ�</td>
		<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0025" <?=(in_array('OD0025',$code)?'checked':'')?>>��ȯ �Ϸ�(������ ���)</td>
		<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0026" <?=(in_array('OD0026',$code)?'checked':'')?>>��ǰ ��û ��</td>
		<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0027" <?=(in_array('OD0027',$code)?'checked':'')?>>��ǰ ��û ����</td>
		<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0028" <?=(in_array('OD0028',$code)?'checked':'')?>>��ǰ ��û ����</td>
		<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0029" <?=(in_array('OD0029',$code)?'checked':'')?>>���� ����(��ǰ ��û ����)</td>
	</tr>
	<tr>
		<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0030" <?=(in_array('OD0030',$code)?'checked':'')?>>�ݼ� ��(��ǰ)</td>
		<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0031" <?=(in_array('OD0031',$code)?'checked':'')?>>��ǰ �ɻ� ��</td>
		<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0032" <?=(in_array('OD0032',$code)?'checked':'')?>>��ǰ �Ϸ�</td>
		<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0033" <?=(in_array('OD0033',$code)?'checked':'')?>>��ǰ �Ϸ�(������ ���)</td>
		<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0034" <?=(in_array('OD0034',$code)?'checked':'')?>>��ǰ ����</td>
		<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0035" <?=(in_array('OD0035',$code)?'checked':'')?>>���� ����(��ǰ ����)</td>
	</tr>
	<tr>
		<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0036" <?=(in_array('OD0036',$code)?'checked':'')?>>�Ǹ� �Ϸ�</td>
		<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0037" <?=(in_array('OD0037',$code)?'checked':'')?>>��� ó�� �Ϸ�</td>
		<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0038" <?=(in_array('OD0038',$code)?'checked':'')?>>ȯ�� ���(�Ǹ� ���)</td>
		<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0039" <?=(in_array('OD0039',$code)?'checked':'')?>>ȯ�� ���(�ֹ� ���� ���)</td>
		<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0040" <?=(in_array('OD0040',$code)?'checked':'')?>>ȯ�� ���(��ȯ ������ ���)</td>
		<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0041" <?=(in_array('OD0041',$code)?'checked':'')?>>ȯ�� ���(��ǰ ���� ���)</td>
	</tr>
	<tr>
		<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0042" <?=(in_array('OD0042',$code)?'checked':'')?>>ȯ�� ���(�ֹ� ���)
		<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0043" <?=(in_array('OD0043',$code)?'checked':'')?>>����� ��
		<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0044" <?=(in_array('OD0044',$code)?'checked':'')?>>���� ����(�����)
		<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0045" <?=(in_array('OD0045',$code)?'checked':'')?>>�Ա� ���(�����)
	</tr>
	</table>
	<br>
	<input type="button" value="��Ұ��� ���üũ" style="border:1px solid #cccccc;font-size:8pt" id="btnAllCancel">
	<input type="button" value="��ȯ���� ���üũ" style="border:1px solid #cccccc;font-size:8pt" id="btnAllExchange">
	<input type="button" value="��ǰ���� ���üũ" style="border:1px solid #cccccc;font-size:8pt" id="btnAllReturn">
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


<br><br>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr><td class="rnd" colspan="10"></td></tr>
<tr class="rndbg">
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
<tr><td class="rnd" colspan="10"></td></tr>
<col align="center" width="90"/>
<col align="center" width="120"/>
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

<? include '../_footer.php'; ?>