<?php
$location = '���̹�üũ�ƿ� �ֹ� > 4.0 ���̱׷��̼�';
include '../_header.php';

$page = $_GET['page'] ? $_GET['page'] : 1;
$sk = $_GET['sk'];
$sv = $_GET['sv'];
$code = (array)$_GET['code'];
$orddt = $_GET['orddt'];

$ORDER_OrderDateTimeStart = preg_replace('/^(\d{4})(\d{2})(\d{2})$/','$1-$2-$3',$orddt[0]);
$ORDER_OrderDateTimeEnd = preg_replace('/^(\d{4})(\d{2})(\d{2})$/','$1-$2-$3',$orddt[1]);

$arWhere=array();

$arWhere[] = "O.migrated = '0'";

if($sv) {
	$sv=$db->_escape($sv);
	switch($sk) {
		case 'ORDER_OrderID':
			$arWhere[] = "O.ORDER_OrderID = '{$sv}'";
		break;
		case 'ORDER_OrdererName':
			$arWhere[] = "O.ORDER_OrdererName = '{$sv}'";
		break;
		case 'ORDER_OrdererID':
			$arWhere[] = "O.ORDER_OrdererID = '{$sv}'";
		break;
		case 'ORDER_MallMemberID':
			$arWhere[] = "O.ORDER_MallMemberID = '{$sv}'";
		break;
		case 'SHIPPING_Recipient':
			$arWhere[] = "O.SHIPPING_Recipient = '{$sv}'";
		break;
	}
}
if(count($code)) {
	$tmp=array();
	foreach($code as $eachCode) {
		$tmp[] = '"'.$eachCode.'"';
	}
	$tmp = implode(',',$tmp);
	$arWhere[] = "O.ORDER_OrderStatusCode in ({$tmp})";
}

if($ORDER_OrderDateTimeStart && $ORDER_OrderDateTimeEnd) {
	$arWhere[] = $db->_query_print('O.ORDER_OrderDateTime between [s] and [s]',$ORDER_OrderDateTimeStart,$ORDER_OrderDateTimeEnd);
}
elseif($ORDER_OrderDateTimeStart) {
	$arWhere[] = $db->_query_print('O.ORDER_OrderDateTime >= [s]',$ORDER_OrderDateTimeStart);
}
elseif($ORDER_OrderDateTimeEnd) {
	$arWhere[] = $db->_query_print('O.ORDER_OrderDateTime <= [s]',$ORDER_OrderDateTimeStart,$ORDER_OrderDateTimeEnd);
}

if(count($arWhere)) {
	$strWhere = 'where '.implode(' and ',$arWhere);
}

$query = "
	select
		O.orderNo,
		O.ORDER_OrderID,
		O.ORDER_OrderDateTime,
		O.ORDER_OrderStatusCode,
		O.ORDER_OrderStatus,
		O.ORDER_OrdererName,
		O.ORDER_OrdererID,
		O.ORDER_MallOrderAmount,
		O.SHIPPING_Recipient,

		COUNT(OP.seq) AS PO_cnt,
		OP.ProductName,
		OP.ProductOption

	FROM
		gd_navercheckout_order AS O

	INNER JOIN gd_navercheckout_order_product AS OP
	ON O.orderNo = OP.orderNo

	{$strWhere}

	GROUP BY O.orderNo

	order by
		O.orderNo desc
";

$orderList = $db->_select_page(20,$page,$query);
?>
<script type="text/javascript" src="./checkout.js"></script>
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

function fnGetMigratedProductOrderList() {

	if ($$('input[name="OldOrderID[]"]:checked').size() < 1) {
		alert("������ ��ȯ�� �ֹ����� ������ �ּ���.");
		return false;
	}

	customPopupLayer('about:blank',500,400);

	var f = document.frmNaverCheckout;
	f.action = './checkout.api.migration.process.php';
	f.submit();

}

</script>
<style>
	.el-navercheckout-gogosing {border:4px solid #dce1e1;padding:10px;margin:0 0 10px 0;}
	.el-navercheckout-gogosing h3 {font-size:12px;font-weight:bold;color:#bf0000;margin:0 0 10px 0;}
	.el-navercheckout-gogosing p {margin:0;color:#666666;}
	.el-navercheckout-gogosing dl {}
	.el-navercheckout-gogosing dl dt,
	.el-navercheckout-gogosing dl dd {font-size:12px;margin:0;color:#666666;}
	.el-navercheckout-gogosing dl dt {font-weight:bold;}
	.el-navercheckout-gogosing dl dd {margin:0 0 0 3px;}


</style>
<div class="title title_top">4.0 ���̱׷��̼�</div>

<div class="el-navercheckout-gogosing">
	<h3>���̹�üũ�ƿ� 4.0 ���� ���׷��̵� �ȳ�</h3>
	<p>
		���̹�üũ�ƿ� �ֹ� ���񽺰� 4.0���� ������ ���׷��̵� �Ǿ����ϴ�. <br/>
		���� ���ǻ����� �ݵ�� Ȯ���Ͽ� �ּ���.
	</p>
	<dl>
		<dt>[ 4.0 �ֹ� ������ ���� ]</dt>
		<dd>���̹�üũ�ƿ� 4.0 ����(3�� 15��)�� ���ÿ� ������ 3.0���� ���񽺰� �� �̻� �������� �ʽ��ϴ�.</dd>
		<dd>4.0 ����(3�� 15��) ���� �߻��� �ֹ����� "���̹�üũ�ƿ� 4.0" �ֹ��������� Ȯ�� �� ������ �����Ͻʴϴ�.</dd>
	</dl>
	<dl>
		<dt>[ (��)3.0 ���� �ֹ� ������ ���� ]</dt>
		<dd>4.0 ����(3�� 15��) ������ 3.0 �ֹ����� �ڵ����� �������� �ʽ��ϴ�.</dd>
		<dd>���� �ֹ������͸� 4.0 �������������� ��ȸ �� ���� �Ͻñ� ���ؼ��� ������ ��ȯ �۾��� �ʿ��մϴ�.</dd>
	</dl>
	<dl>
		<dt>[ ������ ��ȯ �۾� ]</dt>
		<dd>�Ʒ��� 3.0 �ֹ� ����Ʈ���� 4.0���� �ֹ������� ��ȯ�ϰ��� �Ͻô� �ֹ����� �����Ͻ���, [�ֹ������ͺ�ȯ] ��ư�� Ŭ���Ͽ� �����͸� ��ȯ�Ͽ� �ּ���.</dd>
		<dd>���� ��ȯ�� �ֹ����� "���̹�üũ�ƿ� 4.0" �ֹ��������� Ȯ�� �� ������ �����Ͻʴϴ�.</dd>
	</dl>
</div>



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

<form name="frmNaverCheckout" method="post" target="processLayerForm">

<table width="100%" cellpadding="0" cellspacing="0" border="0">
<col align="center" width="30"/>
<col align="center" width="35"/>
<col align="center" width="90"/>
<col align="center" width="100"/>
<col align="center" />
<col align="center" width="100" />
<col align="center" width="100" />
<col align="center" width="75" />
<col align="center" width="75" />
<tr><td class="rnd" colspan="9"></td></tr>
<tr class="rndbg">
	<th><a href="javascript:void(0)" onClick="chkBoxAll()" class=white>����</a></th>
	<th>��ȣ</th>
	<th>�ֹ��Ͻ�</th>
	<th>�ֹ���ȣ</th>
	<th>�ֹ���ǰ</th>
	<th>�ֹ���</th>
	<th>�޴º�</th>
	<th>�ݾ�</th>
	<th>ó������</th>
</tr>
<tr><td class="rnd" colspan="9"></td></tr>
<?
foreach($orderList['record'] as $eachOrder) {

	$eachOrder['ORDER_OrderDateTime'] = preg_replace('/^\d{2}(\d{2})-(\d+)-(\d+) (\d+):(\d+):(\d+)$/','$1.$2.$3 $4:$5',$eachOrder['ORDER_OrderDateTime']);
	$eachOrder['ProductName'] = $eachOrder['PO_cnt'] > 1 ? $eachOrder['ProductName'].' �� '.($eachOrder['PO_cnt']-1).'��' : $eachOrder['ProductName'];
?>
<tr>
	<td class="noline"><input type="checkbox" name="OldOrderID[]" value="<?=$eachOrder['ORDER_OrderID']?>" class="el-OrderID"/></td>
	<td><font class=ver8 color=616161><?=$eachOrder['_rno']?></font></td>
	<td><font class=ver81 color=616161><?=$eachOrder['ORDER_OrderDateTime']?></font></td>
	<td height="23"><a href="checkout.orderdetail.php?orderNo=<?=$eachOrder['orderNo']?>"><font class=ver81 color=0074BA><b><?=$eachOrder['ORDER_OrderID']?></b></font></a></td>
	<td align="left"><font class=small1 color=444444><?=$eachOrder['ProductName']?></font></td>
	<td><font class=small1 color=444444><?=$eachOrder['ORDER_OrdererName']?><?=$eachOrder['ORDER_OrdererID'] ? '<br>('.$eachOrder['ORDER_OrdererID'].')' : ''?></font></td>
	<td><font class=small1 color=444444><?=$eachOrder['SHIPPING_Recipient']?></font></td>
	<td class=ver81><?=number_format($eachOrder['ORDER_MallOrderAmount'])?></td>
	<td class="small4"><?=$eachOrder['ORDER_OrderStatus']?></td>
</tr>
<tr><td colspan="9" bgcolor="#E4E4E4"></td></tr>
<? } ?>
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

<button class="default-btn" onClick="fnGetMigratedProductOrderList();return false;">�ֹ������ͺ�ȯ</button>

</form>

<? include '../_footer.php'; ?>