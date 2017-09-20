<?
//------- �ֹ������� ��½� _form.php �� �״�� ����ϱ� ������ �������� �þ�� �ּҰ� ©���� ������ ����
//------- ��¿����� _reportForm.php ���� ������, �������� �޴��� ���������� _reportForm.php �� �Բ� �������־�� ��.
@include "../../conf/egg.usafe.php";
@include "../../conf/config.pay.php";
@include "../../conf/phone.php";

### �ֹ�����Ʈ ���۷�
$referer = ($_GET[referer]) ? $_GET[referer] : $_SERVER[HTTP_REFERER];

### ���� �迭����
$r_bank = codeitem("bank");

### ��һ��� �迭����
$r_cancel = codeitem("cancel");
$r_cancel[0] = "�ֹ���Һ���";

### ��۾�ü ����
$query = "select * from ".GD_LIST_DELIVERY." where useyn='y' order by deliverycomp";
$res = $db->query($query);
while ($data=$db->fetch($res)){
	$_delivery[] = $data;
	$r_delivery[$data[deliveryno]] = $data[deliverycomp];
}

### �Աݰ��� ����
$query = "select * from ".GD_LIST_BANK." order by useyn asc, sno";
$res = $db->query($query);
while ($data=$db->fetch($res)){
	$data['name'] .= ($data['useyn'] == 'y' ? '' : ' (�����Ѱ���)');
	$_bank[] = $data;
}

// �ֹ�����
$ordno = $_GET[ordno];
$order = new order();
$order->load($ordno);

if(!$order[deliveryno] && $_delivery[0][deliveryno]) $order[deliveryno] = $_delivery[0][deliveryno];
$_selected[deliveryno][$order[deliveryno]] = "selected";
$_selected[bankAccount][$order[bankAccount]] = "selected";

if(!$order[confirm])$order[confirm] = "admin";

### ī������α� �Ľ�
if ($order[settlelog]){
	$div = explode("\n",$order[settlelog]);
	foreach ($div as $v){
		$div2 = explode(":",$v);
		$r_settlelog[trim($div2[0])] = trim($div2[1]);
	}
}

### ���ݰ�꼭
$query = "select regdt, agreedt, printdt, price, step, doc_number from ".GD_TAX." where ordno='$ordno' order by sno desc limit 1";
$taxed = $db->fetch($query);
if ( $taxed['step'] != '' && $taxed['step']==0 )
	$_taxstate = "<FONT COLOR=#007FC8>�����û</font> - ��û�� : {$taxed['regdt']}";
else if ( $taxed['step'] != '' && $taxed['step']==1 )
	$_taxstate = "<FONT COLOR=#587E06>�������</font> <a href=\"javascript:orderPrint('{$ordno}','tax')\">[���ݰ�꼭 �μ�]</a><br>����� : " . number_format($taxed['price']) . ", ������ : {$taxed['agreedt']}";
else if ( $taxed['step'] != '' && $taxed['step']==2 )
	$_taxstate = "<FONT COLOR=#2266BB>����Ϸ�</font> <a href=\"javascript:orderPrint('{$ordno}','tax')\">[���ݰ�꼭 �μ�]</a><br>����� : " . number_format($taxed['price']) . ", �Ϸ��� : {$taxed['printdt']}";
else if ( $taxed['step'] != '' && $taxed['step']==3 )
	$_taxstate = "<div id=\"tax1\"><FONT COLOR=#2266BB>���ڹ���</font></div><div id=\"tax2\">����� : " . number_format($taxed['price']) . ", ��û�� : {$taxed['agreedt']}</div><script>getTaxbill('{$taxed[doc_number]}');</script>";

### ������ũ
$inpk_ordno = $order['inpk_ordno'];
$inpk_regdt = $order['inpk_regdt'];

### �½��÷�
$query = "
SELECT GF.*
FROM ".GD_ORDER." AS O

INNER JOIN ".GD_GOODSFLOW_ORDER_MAP." AS OM
ON O.ordno = OM.ordno

INNER JOIN ".GD_GOODSFLOW." AS GF
ON GF.sno = OM.goodsflow_sno

WHERE
	GF.status > '' AND O.ordno = '$ordno'

LIMIT 1
";

$GF = $db->fetch($query,1);
?>

<style>
.title2 {
	font-weight:bold;
	padding-bottom:5px;
}
</style>
<script>
function cal_repay(repay,price,i){
	var tmp = price - repay;
	document.getElementsByName('repay')[0].value=tmp;
}

function chkCancel(m)
{
	var sno = new Array();
	var el = document.getElementsByName('chk[]');
	for (i=0;i<el.length;i++) if (el[i].checked) sno[sno.length] = el[i].value;
	if (sno.length==0){
		alert("��������� ��ǰ�� �������ּ���");
		return;
	}
	_ID('layer_cancel').style.display = "block";
	ifrmCancel.location.href = "ifrm.cancel.php?m="+m+"&ifrmScroll=1&ordno=<?=$ordno?>&chk=" + sno.join();
}
function paycoCancel(ordno, settle_type){
	if(confirm('������ ������ ����Ͻðڽ��ϱ�?'))	{
		if(settle_type == 'v' || settle_type == 'o') popupLayer('./paycoCancelVbank.php?ordno='+ordno,600,300);
		else popupLayer('./paycoCancel.php?ordno='+ordno,600,300);
	}
}
function cardSettleCancel(ordno){
	var obj = document.ifrmHidden;
	if(confirm('ī������� ����Ͻðڽ��ϱ�?'))	obj.location.href = "cardCancel.php?ordno="+ordno;
}
function cardCancel(pg,ordno){
	var obj = document.ifrmHidden;
	obj.location.href = "../../order/card/<?=$cfg[settlePg]?>/escrow_gate.php?ordno=<?=$ordno?>";
}
function orderPrint(ordno,type)
{
	if (!type){
		alert("�μ��� ���� ������ �����ϼ���");
		return;
	}
	var orderPrint = window.open("_paper.php?ordno=" + ordno + "&type=" + type,"orderPrint","width=750,height=900,scrollbars=1");
	orderPrint.window.print();
}
function escrow_confirm()
{
	var obj = document.ifrmHidden;
	obj.location.href = "../../order/card/<?=$cfg[settlePg]?>/escrow_gate.php?ordno=<?=$ordno?>";
}
function escrow_cancel()
{
	var win = window.open("../../order/card/<?=$cfg[settlePg]?>/escrow_cancel.php?ordno=<?=$ordno?>","","width=520,height=250,scrollbars=0");
}
function chk_step(val){

	var ea =  document.getElementsByName('ea[]');
	var price =  document.getElementsByName('price[]');
	var supply =  document.getElementsByName('supply[]');

	// a (=������ �Ա�) �� �ƴ� ��� ����, �ǸŰ�, ���԰� �ʵ� ��Ȱ��ȭ
	if ('<?=$order['settlekind']?>' != 'a') {
		val = 1;
	}

	for(var i=0;i<ea.length;i++){
		if(val == 0){
			ea[i].style.background = "#ffffff";
			ea[i].readOnly = false;

			price[i].style.background = "#ffffff";
			price[i].readOnly = false;

			supply[i].style.background = "#ffffff";
			supply[i].readOnly = false;
		}else{
			ea[i].style.background = "#e3e3e3";
			ea[i].readOnly = true;

			price[i].style.background = "#e3e3e3";
			price[i].readOnly = true;

			supply[i].style.background = "#e3e3e3";
			supply[i].readOnly = true;

		}
	}
}
function registerDelivery()
{
	var sno = new Array();
	var el = document.getElementsByName('chk[]');
	for (i=0;i<el.length;i++) if (el[i].checked) sno[sno.length] = el[i].value;
	if (sno.length==0){
		alert("��������� �Է��� ��ǰ�� �������ּ���");
		return;
	}
	_ID('layer_cancel').style.display = "block";
	ifrmCancel.location.href = "ifrm.delivery.php?ifrmScroll=1&ordno=<?=$ordno?>&chk=" + sno.join();
}
</script>
<script>
/*** Taxbill ���� ��� ***/
function getTaxbill(doc_number)
{
	var print = function(){
		var req = ajax.transport;
		if (req.status == 200){
			var jsonData = eval( '(' + req.responseText + ')' );
			document.getElementById('tax1').innerHTML += (jsonData.status_txt != null ? ' - ' + jsonData.status_txt : '');
			if (jsonData.tax_path != null) document.getElementById('tax1').innerHTML +=" <a href=\"javascript:popup('" + jsonData.tax_path + "',750,600);\">[���ݰ�꼭 �μ�]</a>";
			document.getElementById('tax2').innerHTML += (jsonData.mtsid != null ? '<br>�ĺ���ȣ : ' + jsonData.mtsid : '');
		}
		else {
			var msg = req.getResponseHeader("Status");
			document.getElementById('tax1').title = msg;
			document.getElementById('tax1').innerHTML += '<font class=small color=444444> - �ε��߿���</font>';
		}
	}
	var ajax = new Ajax.Request("../order/tax_indb.php?mode=getTaxbill&doc_number=" + doc_number + "&dummy=" + new Date().getTime(), { method: "get", onComplete: print });
}

function iciSelect(obj)
{
	var row = obj.parentNode.parentNode;
	row.style.background = (obj.checked) ? "#F9FFF0" : row.getAttribute('bg');
}

function chkBoxAll(El,mode)
{
	if (!El || !El.length) return;
	for (i=0;i<El.length;i++){
		if (El[i].disabled) continue;
		El[i].checked = (mode=='rev') ? !El[i].checked : mode;
		iciSelect(El[i]);
	}
}

document.observe("dom:loaded", function() {
	var selDeliveryNo=document.frmOrder.deliveryno;
	var iptDeliveryCode=document.frmOrder.deliverycode;
	Element.extend(iptDeliveryCode);
	Element.extend(selDeliveryNo);
	if(selDeliveryNo.value=="100") {
		iptDeliveryCode.readOnly=true;
	}
	else {
		iptDeliveryCode.readOnly=false;
	}

	selDeliveryNo.observe("change",function(evt){
		var element = evt.element();
		if(element.value=="100") {
			iptDeliveryCode.readOnly=true;
		}
		else {
			iptDeliveryCode.readOnly=false;
		}
	});


});

function couponDelPop(){
	if(confirm("���� ��볻���� ����ϰ� �̻�� ���·� �����Ͻðڽ��ϱ�?")){
		document.frmOrder.mode.value = "restoreDiscount";
		document.frmOrder.submit();
	}
}

function recovery(sno){
		if (confirm('����ó���Ͻðڽ��ϱ�?'))
		{
			document.getElementById('img_recovery').innerHTML="<img src='../img/ajax-loader.gif' style='border:none'>";
			location.href='indb.php?mode=recovery&sno='+sno;
		}
}

function chk_ea(obj) {
	 if(obj.value == '' || isNaN(obj.value) || parseInt(obj.value) < 1) {
		  obj.value = 1;
		  return false;
	  }
}
</script>
<?getjskPc080();?>

<div class="title title_top">�ֹ��󼼳���<span>�� �ֹ��� ���� ���� ������ ��ȸ�ϰ� �����Ͻ� �� �ֽ��ϴ�</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=order&no=2')"><img src="../img/btn_q.gif" border=0 hspace=2 align=absmiddle></a></div>

<table width=100% cellpadding=0 cellspacing=0>
<tr><td style="padding:5px 10px;background:#f7f7f7;margin:10px 0;border:3px solid #627dce;">
<table width=100%>
<tr>
	<td id="orderInfoBox">
	<font class=def>�ֹ���ȣ:</font> <span style="color:<?=($order['inflow']!="sugi") ? "#4f67af" : "#ED6C0A"?>;font:bold 11px verdana"><?=$ordno.(($order['inflow']=="sugi") ? "(�����ֹ�)" : "")?></span>
	<? if ($order[inflow]!=""&&$order[inflow]!="sugi"){ ?><img src="../img/inflow_<?=$order[inflow]?>.gif" align=absmiddle> <?=$r_inflow[$order[inflow]]?><? } ?>
	<? if ($order[pCheeseOrdNo]!=""){ ?><img src="../img/icon_plus_cheese.gif" align=absmiddle> �÷��� ġ�� �ֹ�<? } ?>
	</td>
	<td align=right <?=$hiddenPrint?>>
	<select name="order_print" class="Select_Type1" style="font:8pt ����">
	<option value=""> - �μ⼱�� - </option>
	<option value="report"> �ֹ�������  </option>
	<option value="report_customer"> �ֹ�������(����)  </option>
	<option value="reception"> ���̿�����  </option>
	<option value="tax"> ���ݰ�꼭  </option>
	<option value="particular"> �ŷ�����  </option>
	</select>
	<a href="javascript:orderPrint(<?=$ordno?>, document.getElementsByName('order_print')[0].value);"><img src="../img/btn_print.gif" border="0" align="absmiddle"></a>
	</td>
</tr>
</table>
</td></tr>
<tr><td height=8></td></tr>
</table>

<form name=frmOrder action="<?=$sitelink->link('admin/order/indb.php','ssl');?>" method=post>
<input type=hidden name=mode value="modOrder">
<input type=hidden name=ordno value="<?=$ordno?>">
<input type=hidden name=referer value="<?=$referer?>">
<input type=hidden name=step2 value="<?=$order[step2]?>">

<table class=tb cellpadding=4 cellspacing=0>
<tr height=25 bgcolor=#2E2B29 class=small4 style="padding-top:8px">
	<th><font color=white><a href="javascript:void(0)" onClick="chkBoxAll(document.getElementsByName('chk[]'),'rev')" class=white>����</a></th>
	<th><font color=white>��ȣ</th>
	<th colspan=2><font color=white>��ǰ��</th>
	<th><font color=white>����</th>
	<th><font color=white>�ǸŰ�</th>
	<th><font color=white>���αݾ�</th>
	<th><font color=white>ȸ������</th>
	<th><font color=white>�����ݾ�</th>
	<th><font color=white>���԰�</th>
	<?if($set[delivery][basis]){?>
	<th nowrap><font color=white >�ù��/�����ȣ</th>
	<?}?>
	<th><font color=white>ó������</th>
</tr>
<col align=center span=3><col>
<col align=center span=10>
<?
$idx = 0;

// �ֹ� ��ǰ
foreach($order->getOrderItems() as $item) {

	unset($selected);
	$selected[dvno][$item[dvno]] = "selected";
	$selected[istep][$item[istep]] = "selected";

	if ($item->hasCanceled()) {
		$disabled[chk] = "disabled";
		$bgcolor = "#F0F4FF";
	}
	else {
		$disabled[chk] = "";
		$bgcolor = "#ffffff";
	}

	if($item[dvcode]) $cntDv++;

	$isNaverMileage = (strlen(trim($order['ncash_tx_id']))>0);
	$naverMileageRecover = ($isNaverMileage===false || ($isNaverMileage && $item['istep']==41));

	$paycoServiceRecover = false;
	if($order['settleInflow'] == 'payco' && $item['cancel'] > 0 && $item['istep'] > 40){
		list($paycoOrderItemPgcancel) = $db->fetch("SELECT pgcancel FROM ".GD_ORDER_CANCEL." WHERE sno='".$item['cancel']."' LIMIT 1");
		if($paycoOrderItemPgcancel == 'r' || $paycoOrderItemPgcancel == 'y') $paycoServiceRecover = true;
	}
?>
<input type=hidden name=sno[] value="<?=$item[sno]?>">
<tr bgcolor="<?=$bgcolor?>" bg="<?=$bgcolor?>">
	<td width=35 nowrap class=noline><input type=checkbox name=chk[] value="<?=$item[sno]?>" onclick="iciSelect(this)" <?=$disabled[chk]?>></td>
	<td width=35 nowrap><font class=ver8 color=444444><?=++$idx?></td>
	<? if ($item['todaygoods'] != 'y') { ?>
	<td width=50 nowrap><a href="../../goods/goods_view.php?goodsno=<?=$item[goodsno]?>" target=_blank><?=goodsimg($item[img_s],30,"style='border:1 solid #cccccc'",1)?></a></td>
	<td width=100%><a href="javascript:popup('../goods/popup.register.php?mode=modify&goodsno=<?=$item[goodsno]?>',825,600)">
	<? } else { ?>
	<td width=50 nowrap><a href="../../todayshop/today_goods.php?tgsno=<?=$item[tgsno]?>" target=_blank><?=goodsimg($item[img_s],30,"style='border:1 solid #cccccc'",1)?></a></td>
	<td width=100%><a href="../todayshop/goods_reg.php?mode=modify&tgsno=<?=$item[tgsno]?>"  target=_blank>
	<? } ?>
	<font class=small color=0074BA><? if ($item['todaygoods']=='y') echo '<�����̼���ǰ>'?><?=$item[goodsnm]?>
	<? if ($item[opt1]){ ?>[<?=$item[opt1]?><? if ($item[opt2]){ ?>/<?=$item[opt2]?><? } ?>]<? } ?>
	<? if ($item[addopt]){ ?><div>[<?=str_replace("^","] [",$item[addopt])?>]</div><? } ?></a>
	<div style="padding-top:3"><font class=small1 color=6d6d6d>������ : <?=$item[maker] ? $item[maker] : '����'?></div>
	<div><font class=small1 color=6d6d6d>�귣�� : <?=$item[brandnm] ? $item[brandnm] : '����'?></div>
	<? if ($item[deli_msg]){ ?><div><font class=small1 color=6d6d6d>(<?=$item[deli_msg]?>)</font></div><? } ?>
	</td>
	<td nowrap><input type=text name=ea[] value="<?=$item[ea]?>" size=3 class=right onkeydown="onlynumber()" onblur="chk_ea(this);"></td>
	<td nowrap><input type=text name=price[] value="<?=$item[price]?>" size=7 class=right></td>
	<td width=55 nowrap><?=number_format($item->getPercentCouponDiscount() + $item->getSpecialDiscount())?></td>
	<td width=55 nowrap><?=number_format($item->getMemberDiscount())?></td>
	<td width=55 nowrap><?=number_format($item->getSettleAmount())?></td>
	<td nowrap><input type=text name=supply[] value="<?=$item[supply]?>" size=7 class=right></td>
	<?if($set[delivery][basis]){?>
	<td><div nowrap><font class=small color=555555><b><?=((!$item[dvcode]||!$item[dvno])&&$order[step])? "-":$r_delivery[$item[dvno]] . "</div><div nowrap>" . $item[dvcode]?></font></div></td>
	<?}?>
	<td width=70 nowrap>
	<font class=small4><?=$r_istep[$item[istep]]?></font>
	<? if (($item[istep]==41 || ($item[istep]==44 && $item[cyn].$item[dyn]=="nn")) && $naverMileageRecover && $paycoServiceRecover === false){ ?><div id="img_recovery"><a href="javascript:recovery(<?=$item[sno]?>)"><img src="../img/btn_return.gif" border=0></a></div><? } ?>
	</td>
</tr>
<?
}
?>
</table>

<table cellpadding=0 cellspacing=0 width=100%>
<tr><td width=60% style="padding:5px 0 0 12px"><a href="javascript:chkCancel(0)"><img src="../img/btn_cancelorder.gif" border=0></a>
<?if($order[step] > 1){?><a href="javascript:chkCancel(1)"><img src="../img/btn_exchangeorder.gif" border=0></a><?}?></td>
<td width=40% align=right style="padding-right:5px"><a href="javascript:manual('<?=$guideUrl?>board/view.php?id=order&no=3')"><img src="../img/btn_cancel_manual.gif" border=0></a>
</td></tr></table>



<!-- ���������Է� ���� -->
<?if($set[delivery][basis]){?>
<div style="padding:5px 0 0 12px">
<?if($order[step]){?><a href="javascript:registerDelivery()"><img src="../img/btn_input_delinumber.gif" border=0></a><?}?>
</div>
<?}?>
<!-- ���������Է� �� -->
<div id=layer_cancel style="display:none;padding-top:10px">
<iframe id=ifrmCancel name=ifrmCancel style="width:100%;height:0;" frameborder=0></iframe>
</div><p>
<?
$selected[step][$order[step]] = "selected";
$selected[step2][$order[step2]] = "selected";
$selected[deliveryno][$order[deliveryno]] = "selected";

?>
<div class=title2>&nbsp;<img src="../img/icon_process.gif" align=absmiddle><font color=508900>���ֹ�����</font></div>

<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>�ֹ�����</td>
	<td style="padding:2px 10px">
	<script>chk_step(1)</script>
	<table width=100%>
	<tr>
		<td><font class=small1>
		<?if($order['settleInflow'] == 'payco') {?>
			<? if($order['step'] > 0 && !$order['step2']){ ?>
			<select name=step onchange="chk_step(this.value)">
			<? foreach ($r_step as $k=>$v){ if ($k>9) break; if($k < 1) continue;?>
			<option value="<?=$k?>" <?=$selected[step][$k]?>><?=$v?>
			<? } ?>
			</select>
			<?
			if($order->getCanceledCount()) printf('�ֹ���� %d ���Դϴ�.', $order->getCanceledCount());
			?>
			<script>chk_step('<?=$order[step]?>')</script>
			<? } else {
				echo $order->getStepMsg();
			}?>
		<?} else {?>
			<? if (!$order[step2]){ ?>
			<select name=step onchange="chk_step(this.value)">
			<? foreach ($r_step as $k=>$v){ if ($k>9) break; ?>
			<option value="<?=$k?>" <?=$selected[step][$k]?>><?=$v?>
			<? } ?>
			</select>
			<?
			if($order->getCanceledCount()) printf('�ֹ���� %d ���Դϴ�.', $order->getCanceledCount());
			?>
			<script>chk_step('<?=$order[step]?>')</script>
			<? } else {
				echo $order->getStepMsg();
			}?>
		<?}?>
		<?
		if ($order->hasExchanged()) {
			$query = "select ordno from ".GD_ORDER." where oldordno='$ordno'";
			$nres = $db->query($query);
			while($ndata = $db->fetch($nres))	$newordno[] = "<a href=\"javascript:popup('popup.order.php?ordno=".$ndata[ordno]."',800,600)\"><font color=0074BA class=ver81><b><u>".$ndata[ordno]."</u></b></font></a>";
		?>
		&nbsp;<img src="../img/arrow_gray.gif" align=absmiddle><font class=small1 color=444444>��ȯ���� ���� �ڵ������� <font color=ED00A2>�±�ȯ�ֹ���</font> (<?=implode(',',$newordno)?>) �� �ֽ��ϴ�.</font>
		<?
		}
		?>
		<?
		if($order[oldordno]){
		?>
		&nbsp;<img src="../img/arrow_gray.gif" align=absmiddle><font class=small1 color=444444>�� �ֹ��� <font color=ED00A2>��ȯ��û��</font> (<a href="javascript:popup('popup.order.php?ordno=<?=$order[oldordno]?>',800,600)"><font color=0074BA class=ver81><b><u><?=$order[oldordno]?></u></b></font></a>) ���� �ڵ������� ���ֹ� �Դϴ�.</font>
		<?if($order->hasPaycoExchange() === true) {?>
			<dd style="padding:0;margin:5px 0 0 0px;" class="extext">
			<img src="../img/arrow_gray.gif" align=absmiddle> �� �ֹ��� ������ ���� �ֹ����� ���ֹ������ν� ��ҽ� ����� ȯ���� ó���ؾ��ϸ�, �����ڷ� ������ �ݾ��� ������ ������ ��Ʈ�� ���Ϳ��� ���� �� �ֽ��ϴ�.
			</dd>
		<?
			}
		}
		?>
		</td>
		<td align="right">
		<?if($order[step2] >= 50){?>
		<?
		if ($order['settleInflow'] != 'payco') {
			$alertMsg = "return confirm('�� ����� �����õ�(����) �ֹ����� �Ա�Ȯ�� ���·� �����ϴ� ����Դϴ�. �ݵ�� �ֹ����� �Աݳ��� �� �������� ���θ� Ȯ���Ͻ� �� ������ �ֽñ� �ٶ��ϴ�.(���󺹱� �Ұ�)\\n\\n�����Ͻ� �ֹ�[".$ordno."]�� ������ �Ա�Ȯ������ �����Ͻðڽ��ϱ�?')";
		} else {
			$alertMsg = "alert('������ �ֹ����� �ֹ����´� �������� �ֹ����¸� �������� ������ �ַ�ǿ��� ���Ƿ� �����Ͻ� �� �����ϴ�.'); return false; void(0);";
		}
		?>
		<span><a href="indb.php?mode=faileRcy&ordno=<?=$ordno?>&returnUrl=<?=urlencode($referer)?>&popup=<?=$popup?>" onclick="<?=$alertMsg?>"><img src="../img/btn_order_try_return.gif"></a></span>
		<?}?>
		<?if($order['step'] == 1 && $order['step2'] == 0 && $order['pgcancel'] != 'r' && $order['settleInflow'] == 'payco') {?>
			<?if($order['settlekind'] == 'h' && $order['cdt'] >= date('Y-m-d h:i:s',strtotime("-2 day"))) {?>
			<a href="javascript:paycoCancel(<?=$ordno?>, '<?=$order['settlekind']?>')"><img src="../img/payco_cancel_btn.gif" /></a>
			<?} else if($order['settlekind'] != 'h') {?>
			<a href="javascript:paycoCancel(<?=$ordno?>, '<?=$order['settlekind']?>')"><img src="../img/payco_cancel_btn.gif" /></a>
			<?}?>
		<?} else if(($order['settlekind'] == 'c' || $order['settlekind'] == 'u') && $order['step'] == 1 && $order['step2'] == 0 && $order['cdt'] >= date('Y-m-d h:i:s',strtotime("-2 day")) && $order['pgcancel'] != 'r'){?>
			<?if($order['cardtno']) {?>
			<a href="javascript:cardSettleCancel(<?=$ordno?>)"><img src="../img/cardcancel_btn.gif" /></a>
			<?}?>
		<?}?>
		<span style='width:80'><a href="indb.php?mode=delOrder&ordno=<?=$ordno?>&returnUrl=<?=urlencode($referer)?>&popup=<?=$popup?>" onclick="return confirm('�ֹ������� �� �ֹ�����Ÿ�� �ܼ��� ������ �ϴ� ����Դϴ�.\n\n���� �ٷ� ������ �ϸ� �� �ֹ��� ���� ���, ������, ������ ȯ���� �ȵ˴ϴ�.\n\n\���, ������, ������ ȯ���Ϸ��� �ݵ�� �ֹ����(������ ��ǰ���)�� ���� ���ּ���.\n\n�ֹ���Ұ� �Ǹ� ������� ���, ������, ������ ȯ���˴ϴ�.\n\n�׸��� �ֹ������� �Ͻñ� �ٶ��ϴ�.\n\n�ѹ� ������ �ֹ��� ������ �Ұ����մϴ�. ������ �����ϼ���.\n\n�����Ͻ� �ֹ�[<?=$ordno?>]�� ������ �����Ͻðڽ��ϱ�?')"><img src="../img/btn_delete_order.gif"></a><span>
		</td>

	</tr>
	</table>

	</td>
</tr>
<?php if ($order['pg'] === 'mobilians' && strlen($order['pgAppNo']) > 0) { ?>
<tr>
	<td>��������</td>
	<td style="padding: 7px;">
		<div style="float: left; padding: 5px;" class="small1"><?php echo ($order['pgcancel'] === 'y') ? '������ҵ�' : '������'; ?></div>
		<div style="float: right;">
			<?php if ($order['step'] > 0 && $order['step2'] == '0' && $order['cardtno'] && $order->getCanceledCount()<1 && $order['pgcancel'] != 'y') { ?>
				<?php if (substr($order['pgAppDt'], 0, 6)==date('Ym')) { ?>
				<img src="../img/payment_cancel_btn.jpg" onclick="if (confirm('�� �ֹ����� ������ ����Ͻðڽ��ϱ�?')) ifrmHidden.location.href='<?php echo $cfg['rootDir']; ?>/order/card/mobilians/card_cancel.php?ordno=<?php echo $ordno; ?>';" style="cursor: pointer;"/>
				<?php } else { ?>
				<span style="float: left; padding: 5px;" class="small1">�̿��� �ֹ������� ��ҺҰ�</span>
				<?php } ?>
			<?php } ?>
		</div>
	</td>
</tr>
<?php } else if ( $order['pg'] === 'danal' && strlen($order['pgAppNo']) > 0) { ?>
<tr>
	<td>��������</td>
	<td style="padding: 7px;">
		<div style="float: left; padding: 5px;" class="small1"><?php echo ($order['pgcancel'] === 'y') ? '������ҵ�' : '������'; ?></div>
		<div style="float: right;">
			<?php if ($order['step'] > 0 && $order['step2'] == '0' && $order['cardtno'] && $order->getCanceledCount()<1 && $order['pgcancel'] != 'y') { ?>
				<?php if (substr($order['pgAppDt'], 0, 6)==date('Ym')) { ?>
				<img src="../img/payment_cancel_btn.jpg" onclick="if (confirm('�� �ֹ����� ������ ����Ͻðڽ��ϱ�?')) ifrmHidden.location.href='<?php echo $cfg['rootDir']; ?>/order/card/danal/card_cancel.php?ordno=<?php echo $ordno; ?>';" style="cursor: pointer;"/>
				<?php } else { ?>
				<span style="float: left; padding: 5px;" class="small1">�̿��� �ֹ������� ��ҺҰ�</span>
				<?php } ?>
			<?php } ?>
		</div>
	</td>
</tr>
<?php } ?>
</table><p>

<div class=title2>
	<span style="padding-right:10px">&nbsp;<img src="../img/icon_process.gif" align=absmiddle><font color=508900>�����ݾ�����</font> <font class=small1 color=6d6d6d>�Ʒ��� �ֹ��� ���(ȯ��)�ݾ� �󼼳����Դϴ�</font></span>
	<a href="javascript:popup('popup.pay_history.php?ordno=<?=$ordno?>',800,600)"><img src="../img/btn_orderhistory.gif" align=absmiddle border=0></a>
</div>
<table border=2 bordercolor=627dce style="border-collapse:collapse" width=100%>
<tr><td style="padding:10px 1px;">

<table width="100%" height="100%" cellpadding=0 cellspacing=0>
<col span=3 valign=top>
<? if ($order->getCanceledCount() > 0){ ?>
<tr>
    <td width="50%" valign="top">
        <? include '_form.enamoo.inc_orderprice.php'; ?>
    </td>
    <td width="10" nowrap="nowrap"></td>
    <td width="50%" valign="top">
        <?
        if ($order->getCancelCompletedCount() > 0){
            include '_form.enamoo.inc_canceledprice.php';
        } else {
            include '_form.enamoo.inc_cancelingprice.php';
        }
        ?>
    </td>
</tr>
<? } else { ?>
<tr>
    <td width="50%" valign="top">
        <? include '_form.enamoo.inc_orderprice.php'; ?>
    </td>
</tr>
<? } ?>
<? if ($order->getCancelCompletedCount() > 0){ ?>
<tr><td height=15></td></tr>
<tr>
    <td width="50%" valign="top">
        <? include '_form.enamoo.inc_settleprice.php'; ?>
    </td>
    <td width="10" nowrap="nowrap"></td>
    <td width="50%" valign="top">
        <? include '_form.enamoo.inc_cancelingprice.php'; ?>
    </td>
</tr>
<? } ?>
</table><p>

<dl style="margin:5px;">
	<dt><font color=627dce>��</font> <font class=extext>�����ֹ��ݾ�</font></dt>
	<dd style="padding:0;margin:-13px 0 0 110px;" class="extext">
	- ��ǰ�� �����̳� �ǸŰ����� ���Ƿ� �������� �� ǥ�õǸ�, �̷� ���� ����Ǵ� �����ֹ��ݾ��� �����ݴϴ�. <br />
	</dd>

	<dt><font color=627dce>��</font> <font class=extext>��ǰ�����ݾ�</font></dt>
	<dd style="padding:0;margin:-13px 0 0 110px;" class="extext">
	- ��ǰ�� �����̳� �ǸŰ����� ���Ƿ� �������� �� ǥ�õǸ�, �����ֹ��ݾ��� ��ŭ ������ �߻��ߴ��� �����ݴϴ�.<br />
	</dd>

	<dt><font color=627dce>��</font> <font class=extext>���(ȯ��)�����ݾ�</font></dt>
	<dd style="padding:0;margin:-13px 0 0 110px;" class="extext">
	- ��ǰ�� ���(ȯ��)������ ���� �� ǥ�õǸ�, ���(ȯ��)������ ��ǰ�� ���(����)����Ʈ�� �������ϴ�.<br />
	- ���(ȯ��)�� �Ϸ�� ���°� �ƴϸ�, ���(����)����Ʈ���� ȯ�ҿϷ� ó���� �ؾ� ���(ȯ��)�� �Ϸ�˴ϴ�.<br />
	- �κ���������� �����ݾ�����, �������� ��������ݾ׿� �ݿ����� �ʽ��ϴ�. �� ��� ȯ�� �� ��������ݾ׿� ���̳ʽ�(-)�� ǥ�õ� �� �ֽ��ϴ�.<br />
	- ��ü��������� �����ݾ�����, ȸ������, �������� �ڵ����� ��������ݾ׿� �ݿ��˴ϴ�.
	</dd>

	<dt><font color=627dce>��</font> <font class=extext>���(ȯ��)�Ϸ�ݾ�</font></dt>
	<dd style="padding:0;margin:-13px 0 0 110px;" class="extext">
	- ȯ�ҿϷ� ó���� ���� ���(ȯ��)�ݾ��Դϴ�.
	</dd>

	<dt><font color=627dce>��</font> <font class=extext>���������ݾ�</font></dt>
	<dd style="padding:0;margin:-13px 0 0 110px;" class="extext">
	- �����ݾ׿��� ���(ȯ��)�Ϸ�ݾ��� ������ ���������ݾ��Դϴ�.
	</dd>
</dl>

</td></tr></table><p>
<?
$res = $db->query("select a.*,c.couponcd,c.goodsnm from ".GD_COUPON_ORDER." a left join ".GD_ORDER_ITEM." b on a.goodsno=b.goodsno and a.ordno=b.ordno left join ".GD_COUPON_APPLY." c on a.applysno=c.sno where a.ordno='$ordno'");
if($db->count_($res)){
?>
<div class=title2>&nbsp;<img src="../img/icon_process.gif" align=absmiddle><font color=508900>�����������</font></div>
<table class=tb cellpadding=4 cellspacing=0>
<tr height=25 bgcolor=#2E2B29>
	<td bgcolor="#F6F6F6" align=center>������ȣ</td>
	<td bgcolor="#F6F6F6" align=center>������</td>
	<td bgcolor="#F6F6F6" align=center>����/����</td>
	<td bgcolor="#F6F6F6" align=center>����Ͻ�</td>
</tr>
<col align=center><col align=center><col align=center><col align=center>
<?
	while($row = $db->fetch($res)){
		if($row[downloadsno]){
			$res_cp = $db->query("select p.number from gd_offline_download d left outer join gd_offline_paper p on d.paper_sno = p.sno where  d.sno = '$row[downloadsno]'");
			$rst_cp = $db->fetch($res_cp);
			$row[couponcd] = $rst_cp[number];
		}
?>
<tr>
	<td nowrap><?=$row[couponcd]?></td>
	<td nowrap><?=$row[coupon]?></td>
	<td nowrap><font class=ver8 color=444444>
		<?
		if($row[dc]){
			if(substr($row[dc],-1,1) == '%')	echo "���� ".$row[dc];
			else echo "���� ".number_format($row[dc])."��";
		}
		if($row[emoney]){
			if(substr($row[emoney],-1,1) == '%')	echo "���� ".$row[emoney];
			else echo "���� ".number_format($row[emoney])."��";
		}
		?>
	</td>
	<td nowrap><?=$row[regdt]?></td>
</tr>
<?}?>
</table><p>
<?}?>

<!-- ������ũ_Ŭ���� -->
<div id="interpark_claim"></div>

<?
## okcashbag ������ ǥ��
if($order[cbyn] == "Y"){
?>
<div class=title2>&nbsp;<img src="../img/icon_process.gif" align=absmiddle><font color=494949>OKCashBag����</font> <font class=small1 color=6d6d6d>�Ʒ��� ĳ���� ���������Դϴ�</font></div>
<table class=tb cellpadding=4 cellspacing=0>
<tr>
	<td width=5% align=center bgcolor=#F6F6F6><font class=small1 color=444444><b>��ȣ</td>
	<td width=20% align=center bgcolor=#F6F6F6><font class=small1 color=444444><b>�ŷ���ȣ</td>
	<td width=20% align=center bgcolor=#F6F6F6><font class=small1 color=444444><b>�����ݾ�</td>
	<td width=20% align=center bgcolor=#F6F6F6><font class=small1 color=444444><b>������</td>
	<td width=20% align=center bgcolor=#F6F6F6><font class=small1 color=444444><b>�������</td>
</tr>
<?
	$query = "select * from gd_order_okcashbag where ordno='$ordno'";
	$cashbag_res = $db ->query($query);
	while($r_cashbag = $db->fetch($cashbag_res)){
		$ci++;
?>
<tr>
	<td  style="padding:2px 10px" rowspan=2 align=center><font class=ver7 color=444444><?=$ci?></td>
	<td align=center><font class=ver8><?=$r_cashbag[tno]?></td>
	<td align=center><font class=ver8><?=number_format($r_cashbag[add_pnt])?>��</td>
	<td align=center><font class=ver8><?=substr($r_cashbag[pnt_app_time],0,4)?>-<?=substr($r_cashbag[pnt_app_time],4,2)?>-<?=substr($r_cashbag[pnt_app_time],6,2)?> <?=substr($r_cashbag[pnt_app_time],8,2)?>:<?=substr($r_cashbag[pnt_app_time],10,2)?></td>
	<td align=center><font class=ver8><a href="javascript:popup('../../order/card/kcp/cancel_okcashbag.php?tno=<?=$r_cashbag[tno]?>',600,300)">[�������]</a></td>
</tr>
<?
	}
}

## ȯ�ҿϷ� ��ǰ
unset($rcancel);
$query = "select distinct a.cancel,b.*,AES_DECRYPT(UNHEX(b.bankaccount), b.ordno) AS bankaccount from ".GD_ORDER_ITEM." a left join ".GD_ORDER_CANCEL." b on a.cancel=b.sno where a.istep = 44 and a.cyn in ('r','y') and a.ordno='$ordno' and (b.rprice OR b.remoney OR b.rfee)";
$rres = $db->query($query);



if($db->count_($rres)){
?>
<div class=title2>&nbsp;<img src="../img/icon_process.gif" align=absmiddle><font color=508900>ȯ�ҳ�������</font> <font class=small1 color=6d6d6d>�Ʒ��� �̹� ȯ�ҿϷ�� �����Դϴ�</font></div>

<table border=2 bordercolor=#F43400 style="border-collapse:collapse" width=100%>
<tr><td>

<table class=tb cellpadding=4 cellspacing=0>
	<tr>
		<td width=5% align=center bgcolor=#F6F6F6><font class=small1 color=444444><b>��ȣ</td>
		<td width=20% align=center bgcolor=#F6F6F6><font class=small1 color=444444><b>ȯ�Ҽ�����</td>
		<td width=20% align=center bgcolor=#F6F6F6><font class=small1 color=444444><b>ȯ�ұݾ�</td>
		<td width=20% align=center bgcolor=#F6F6F6><font class=small1 color=444444><b>������ȯ�ұݾ�</td>
		<td width=20% align=center bgcolor=#F6F6F6><font class=small1 color=444444><b>ȯ�ҿϷ� ó����</td>
		<td width=15% align=center bgcolor=#F6F6F6><font class=small1 color=444444><b>ó������</td>
	</tr>
<?
	$i=0;
	while($row2 = $db->fetch($rres)){

		$i++;
		$row2[bankcode] = sprintf('%02d',$row2[bankcode]);

		$query = "select * from ".GD_ORDER_ITEM." where cancel='$row2[cancel]'";
		$res3 = $db->query($query);
		$body3 = "<table>";
		while($row3 = $db->fetch($res3)){
			$body3 .= "<tr><td width=200><div style='text-overflow:ellipsis;overflow:hidden;width:200px' nowrap><font class=small1 color=444444>".$row3[goodsnm]."</div></td>";
			$body3 .= "<td width=50 style=padding-left:10><font class=small1 color=444444>".$row3[ea]."��</td></tr>";
		}
		$body3 .= "</table>";
?>
	<tr>
		<td  style="padding:2px 10px" rowspan=2 align=center><font class=ver7 color=444444><?=$i?></td>
		<td align=center><font class=ver8><?=number_format($row2[rfee])?>��</td>

		<td align=center><font class=ver8 color=EA0095><b><?=number_format($row2[rprice])?></b>��</td>

		<td align=center><font class=ver8><?=number_format($row2[remoney])?>��</td>
		<td align=center><font class=ver81><?=$row2[ccdt]?>	</td>
		<td align=center><font class=small1 color=0074BA><b>ȯ�ҿϷ�</td>
	</tr>

	<tr>
		<td colspan=3>
			<div style='float:left'><?=$body3?></div>
		</div>
		<td colspan=2 align=center>
			<font class=small1 color=444444><b>ȯ�Ұ���</b>: <?=$r_bank[$row2[bankcode]]?>&nbsp;<?=$row2[bankaccount]?>&nbsp;&nbsp;<b>������</b>: <?=$row2[bankuser]?>

		</td>
	</tr>
<?
	}
?>
	</table>
	</td></tr></table>
	<p>
<?
}
?>




<table width=100% cellpadding=0 cellspacing=0>
<col span=3 valign=top>
<tr>
	<td width=50%>

	<div class=title2>&nbsp;<img src="../img/icon_process.gif" align=absmiddle><font color=508900>�ֹ�������</font></div>
	<table class=tb>
	<col class=cellC><col class=cellL>
	<tr>
		<td>�̸�/ID</td>
		<td><? if ($order[m_id] && $order['dormant_regDate'] == '0000-00-00 00:00:00') { ?><span id="navig" name="navig" m_id="<?=$order[m_id]?>" m_no="<?=$order[m_no]?>" popup="<?=$popup?>"><? } ?><font color=0074BA><b>
		<?=$order[nameOrder]?>
		<? if ($order[m_id]){ ?>/ <?=$order[m_id]?> <?php if($order['dormant_regDate'] != '0000-00-00 00:00:00'){ ?>(�޸�ȸ��)<?php } ?></b></font></span>
		<? } ?>
		</td>
	</tr>
	<tr>
		<td>�̸���</td>
		<td><font class=ver8><?=$order[email]?></font> <a href="javascript:popup('../member/email.php?type=direct&email=<?=$order['email']?>',780,600)"><font color="#FF6C4B"><img src="../img/btn_smsmailsend.gif" align=absmiddle></font></a></td>
	</tr>
	<tr>
		<td>����ó</td>
		<td class=ver8>
		<?=$order[phoneOrder]?><?getlinkPc080($order['phoneOrder'],'phone')?> / <?=$order[mobileOrder]?><?getlinkPc080($order['mobileOrder'],'mobile')?> <a href="javascript:popup('../member/popup.sms.php?mobile=<?=$order['mobileOrder']?>',780,600)"><img src="../img/btn_sms.gif" align=absmiddle></a>
		</td>
	</tr>
	<tr>
		<td>�ֹ���</td>
		<td><font class=ver8><?=$order[orddt]?></td>
	</tr>
	</table>

	</td>
	<td width=10 nowrap></td>
	<td width=50%>

	<div class=title2>&nbsp;<img src="../img/icon_process.gif" align=absmiddle><font color=508900>����������</font></div>
	<table class=tb>
	<col class=cellC><col class=cellL>
	<tr>
		<td>������</td>
		<td>
		<input type=text name=nameReceiver value="<?=$order[nameReceiver]?>" style="width:115px" class=line>
		</td>
	</tr>
	<tr>
		<td>����ó</td>
		<td>
		<input type=text name="phoneReceiver" value="<?=$order[phoneReceiver]?>" style="width:95px" class=line><?getlinkPc080($order['phoneReceiver'],'phone')?><?if($popup != 1){?> /<?}?> <input type=text name="mobileReceiver" value="<?=$order[mobileReceiver]?>" style="width:95px" class=line><?getlinkPc080($order['mobileReceiver'],'mobile')?> <a href="javascript:popup('../member/popup.sms.php?mobile=<?=$order['mobileReceiver']?>',780,600)"><font color="#FF6C4B"><img src="../img/btn_sms.gif" align=absmiddle></font></a>
		</td>
	</tr>
	<tr>
		<td>�ּ�</td>
		<td><font color=444444>
		<input type="text" name="zonecode" id="zonecode" size="4" readonly value="<?php echo $order[zonecode]; ?>" class=line>
		(<input type=text name=zipcode[] id="zipcode0" size=3 readonly value="<?=substr($order[zipcode],0,3)?>" class=line> -
		<input type=text name=zipcode[] id="zipcode1" size=3 readonly value="<?=substr($order[zipcode],4)?>" class=line>)
		<a href="javascript:popup('../../proc/popup_address.php',500,432)"><img src="../img/btn_zipcode.gif" align=absmiddle></a>
		</td>
	</tr>
	<tr>
		<td></td>
		<td colspan=3>
			������: <input type=text name=address id="address" style="width:80%" value="<?=$order[address]?>"  class=line>
			<div>
			���θ�: <input type="text" name="road_address" id="road_address" style="width:80%" value="<?=$order['road_address']?>" class="line">
			</div>
		</td>
	</tr>
	</table>

	</td>
</tr><tr><td height=15></td></tr>
<tr>
	<td>

	<div class=title2>&nbsp;<img src="../img/icon_process.gif" align=absmiddle><font color=508900>��������</div>
	<table class=tb>
	<col class=cellC><col class=cellL>

	<tr>
		<td>��������</td>
		<td>
		<?if($order[pg] == 'payco') {?>
		<?=implode(' ', $order->getPaycoOrderDetailArray())?>
		<?}else{?>
		<?=$r_settlekind[$order[settlekind]]?><!--<?if($order[settlekind]=='c'){?>&nbsp;<a href='javascript:cardCancel();'>[�ſ�ī�����]</a><?}?>-->
		<?}?>
		</td>
	</tr>
	<? if ($order[settlekind]=="a"){ ?>
	<tr>
		<td>�Աݰ���</td>
		<td>
		<select name=bankAccount>
		<? foreach ($_bank as $v){ ?>
		<option value="<?=$v[sno]?>" <?=$_selected[bankAccount][$v[sno]]?>><?=$v[bank]?> <?=$v[account]?> <?=$v[name]?>
		<? } ?>
		</select>
		</td>
	</tr>
	<tr>
		<td>�Ա���</td>
		<td><input type=text name=bankSender value="<?=$order[bankSender]?>"></td>
	</tr>
	<? } else if ($order[settlekind]=="v"){ ?>
	<tr>
		<td>�������</td>
		<td><?=$order[vAccount]?></td>
	</tr>
	<? } ?>
	<tr>
		<td>����Ȯ����</td>
		<td><font class=ver8>
		<? if ($order[settlekind]=="c" && $order[settlelog]){ ?><font class=small1 color=FD4700><b>[<?if($r_settlelog['�������']){echo $r_settlelog['�������'];}else{echo $r_settlelog['����޽���'];}?>]</b></font><? } ?>
		<?=$order[cdt]?>
		</td>
	</tr>
	<tr>
		<td>û���ǻ� ��Ȯ��</td>
		<td><font class=ver8>
		<?
		$dubChk = array('none'=>'<font color="#EA0095">��Ȯ�� ��� ������</font>', 'n'=>'<font color="#EA0095">���Ǿ���</font>', 'y'=>'<font color="#0074BA">������</font>');
		echo $dubChk[$order['doubleCheck']];
		?>
		</td>
	</tr>
	<? if ($order['settlekind'] != 'c' && $order['settleInflow'] != 'payco'){ ?>
	<tr>
		<td>���ݿ�����</td>
		<td>
		<?
		@include dirname(__FILE__).'/../../lib/cashreceipt.class.php';
		if (class_exists('cashreceipt'))
		{
			$cashreceipt = new cashreceipt();
			echo $cashreceipt->prnAdminReceipt($ordno);
		}
		else if ($order['cashreceipt']){
			echo $order['cashreceipt'];
		}
		?>
		<div><input type="checkbox" name="cashreceipt_ectway" value="Y" class="null" <?=($order['cashreceipt_ectway'] == 'Y' ? 'checked' : '');?>> ���ݿ����� �����߱� �� �������� �Ǿ��ٸ� üũ�ϼ���.(�ߺ����� ��������)</div>
		</td>
	</tr>
	<? } ?>
	<? if ( !empty($_taxstate) ){ ?>
	<tr>
		<td>���ݰ�꼭</td>
		<td><?=$_taxstate?></td>
	</tr>
	<? } ?>
	<? if ($order[inflow]!="" && $order[inflow]!="sugi"){ ?>
	<tr>
		<td>����ó�ֹ�</td>
		<td><img src="../img/inflow_<?=$order[inflow]?>.gif" align=absmiddle> <?=$r_inflow[$order[inflow]]?></td>
	</tr>
	<? } ?>
	<? if ($order[eggyn]!="n"){ ?>
	<tr>
		<td>���ں�������</td>
		<td>
		<? if ($order[eggno]!=""){ ?><a href="javascript:popupEgg('<?=$egg['usafeid']?>', '<?=$ordno?>')"><font class=ver71 color=0074BA><b><?=$order[eggno]?> <font class=small1>(������ ����)</b></font></a><? } ?>
		<? if ($order[eggno]=="" && $r_settlelog['����޼���']){ ?><font class=small1 color=FD4700><b>[<?=$r_settlelog['����޼���']?>]</b></font><? } ?>
		</td>
	</tr>
	<? } ?>
	</table>

	</td>
	<td></td>
	<td>

	<div class=title2>&nbsp;<img src="../img/icon_process.gif" align=absmiddle><font color=508900>�������</div>
	<table class=tb>
	<col class=cellC><col class=cellL>
	<?if($order[deli_title] != null){?>
	<tr>
		<td>��۹��</td>
		<td><?if($order['deli_msg'] != "���� ���� ��ۺ�"){?><?=$order['deli_title']?><?}?> <?=( $order['deli_msg'] )?$order['deli_msg']:""?></td>
	</tr>
	<?}?>

	<?
	// �½��÷θ� �̿��� �ù� ����϶� �����ȣ �� ������ �� ���� (����غ��� �̻��� ��쿡��)
	if ($GF['type'] == 'casebyorder' || $GF['type'] == 'package') {
	?>
	<tr>
		<td>�����ȣ</td>
		<td>
			<? if ($GF['status'] == 'print_invoice') { ?>
			�½��÷� �ù� �������񽺸� ���� �߱� ���� �����ȣ�� ���� �����Ͻ� �� �����ϴ�.
			<div style="margin-top:3px;">
			<a href="../order/goodsflow.standby.php"class="extext">[�½��÷� ��ǰ���� ��⸮��Ʈ �ٷΰ���]</a>
			</div>

			<? } else { ?>
			�½��÷� �ù� �������񽺸� ���� �߱� ���� �����ȣ�� �����Ͻ� �� �����ϴ�.
			<? } ?>
		</td>
	</tr>
	<? } else { ?>
	<tr>
		<td>�����ȣ</td>
		<td>
		<? if($order['step'] >= 1 && $order['step'] < 4 && !$set['delivery']['basis']): ?>
			<select name="deliveryno">
			<option value="">==�ù��==</option>
			<? foreach((array)$_delivery as $v): ?>
				<option value="<?=$v['deliveryno']?>" <?=$_selected['deliveryno'][$v['deliveryno']]?>><?=$v['deliverycomp']?>
			<? endforeach; ?>
			</select>
			<input type='text' name='deliverycode' value="<?=$order['deliverycode']?>" class=line>
		<? else: ?>
			<? if($order['deliverycode']) : ?>
				<?=$r_delivery[$order['deliveryno']]?> <?=$order['deliverycode']?>
				<div class=small1 color=444444>�Ʒ� ��ۻ������� ��ư�� ���� Ȯ���ϼ���.</div>
			<? endif; ?>
			<input type='hidden' name='deliveryno' value='<?=$order['deliveryno']?>'>
			<input type='hidden' name='deliverycode' value='<?=$order['deliverycode']?>'>
		<? endif; ?>
		</td>
	</tr>
	<? } ?>

	<? if ($order[deliverycode] || $cntDv ){ ?>
	<tr>
		<td>�������</td>
		<td><a href="javascript:popup('popup.delivery.php?ordno=<?=$ordno?>',800,500)"><img src="../img/btn_delifind.gif" border=0></a></td>
	</tr>
	<? } ?>
	<tr>
		<td>�����(�����)</td>
		<td><font class=ver8><?=$order[ddt]?></td>
	</tr>
	<? if ($order[confirmdt]){ ?>
	<tr>
		<td>��ۿϷ���</td>
		<td><font class=ver8><?=$order[confirmdt]?>(<?=$order[confirm]?>)</td>
	</tr>
	<? } ?>
	<? if ($order[escrowyn]=="y"){ ?><!-- ����ũ�� ��� Ȯ�� -->
	<tr>
		<td>����ũ��</td>
		<td>
		<? if (!$order[escrowconfirm]){ ?><a href="javascript:escrow_confirm()">[���Ȯ�ο�û]</a>
		<? } else if ($order[escrowconfirm]==1){ ?>��ۿ�û��
		<? } else if ($order[escrowconfirm]==2){ ?>��ۿϷ�
			<?if ($cfg[settlePg] == 'inicis' || $cfg[settlePg] == 'inipay'){?>&nbsp;<a href="javascript:escrow_cancel()">[��ǰ���]</a><?}?>
		<?}?>
		</td>
	</tr>
	<? } ?>
	</table>

	</td>
</tr><tr><td height=15></td></tr>
<tr>
	<td>

	<div class=title2>
	<span style="padding-right:10px">&nbsp;<img src="../img/icon_process.gif" align=absmiddle><font color=508900>��û����/���޸�</span>
	<a href="javascript:popupLayer('popup.log.php?ordno=<?=$ordno?>')"><img src="../img/btn_orderlog.gif" align=absmiddle border=0></a>
	</div>
	<table class=tb>
	<col class=cellC><col class=cellL>
	<tr height=25>
		<td>����û����</td>
		<td><textarea name=memo style="width:100%;height:100px"><?=$order[memo]?></textarea></td>
	</tr>
	<tr height=25>
		<td>�����޸�</td>
		<td><textarea name=adminmemo style="width:100%;height:100px"><?=$order[adminmemo]?></textarea></td>
	</tr>
	<tr height=25>
		<td>�����α�</td>
		<td><textarea style="width:100%;height:100px;overflow:visible;font:9pt ����ü;padding:10px 0 0 8px"><?=trim($order[settlelog])?></textarea></td>
	</tr>
	</table>

	</td>
	<td></td>
	<td>

	<div class=title2>&nbsp;<img src="../img/icon_process.gif" align=absmiddle><font color=508900>��ҳ�������Ʈ <font class=small1 color=6d6d6d>(�ֹ���Ҹ� ��û�� ������ �� �� �ֽ��ϴ�)</font></div>
	<table class=tb>
	<col class=cellC><col class=cellL>
	<?
	$query = "select * from ".GD_ORDER_CANCEL." where ordno='$ordno' order by sno desc";
	$res = $db->query($query);
	while ($data=$db->fetch($res)){
	?>
	<tr>
		<td><font class=small><?=$data[name]?></td>
		<td>
		<div style="float:left" class=ver81><font color=555555><?=$data[regdt]?></div>
		<div style="float:right"><font class=small1><?=$r_cancel[$data[code]]?></div>
		</td>
	</tr>

	<tr>
		<td colspan=2 bgcolor=#ffffff align=left style="padding:5px">

		<table width=100%>
		<?
		$query = "select * from ".GD_LOG_CANCEL." where cancel='$data[sno]'";
		$res2 = $db->query($query);
		while ($item=$db->fetch($res2)){
		?>
		<tr >
			<td><font class=small1 color=444444>- <?=$item[goodsnm]?> <?=$item[ea]?>��</td>
			<td align=right><font class=small1 color=EA0095><?=$r_stepi[$item[prev]][$item[next]]?></td>
		</tr>
		<? } ?>
		</table>
		</div>
		</td>
	</tr>
	<tr>
		<td colspan=2 bgcolor=#ffffff align=left style="padding:5px">
		<? if ($data[memo]){ ?>
		<div style="margin:5px" class=small><font color=0074BA>ó���޸�:</font> <font color=555555><?=nl2br($data[memo])?></div>
		<? } ?>
		</td>
	</tr>

	<? } ?>

	</table>

	</td>
</tr>

</table>

<?
$res = $db->query("select * from ".GD_ORDER_ITEM_LOG." where ordno='$ordno'");
if($db->count_($res)){
?>
<p />
<div class=title2>&nbsp;<img src="../img/icon_process.gif" align=absmiddle><font color="#508900">�ֹ���ǰ �������</font></div>
<table class=tb cellpadding=4 cellspacing=0>
<tr height=25 bgcolor=#2E2B29>
	<td bgcolor="#F6F6F6" align=center>����</td>
	<td bgcolor="#F6F6F6" align=center>��ǰ��</td>
	<td bgcolor="#F6F6F6" align=center>�������</td>
	<td bgcolor="#F6F6F6" align=center>����</td>
</tr>
<col align="center" width="50"><col align=center><col align=left><col align=center width="100">
<?
$cnum = 0;
while($clog = $db->fetch($res)){
	$cnum++;
?>
<tr height=60>
	<td><?=$cnum?></td>
	<td><?=$clog['goodsnm']?></td>
	<td><?=nl2br($clog['log'])?></td>
	<td><?=substr($clog['regdt'],0,10)?><br><?=substr($clog['regdt'],10)?></td>
</tr>
<?}?>
</table><p />
<?}?>

<div class=button <?=$hiddenPrint?>>
<input type=image src="../img/btn_modify.gif">
<?
if(!preg_match('/popup.order.php/',$_SERVER[SCRIPT_FILENAME])){
?>
<a href='<?=$referer?>'><img src='../img/btn_list.gif'></a>
<?
}
?>
</div>

</form>

<script>window.onload = function(){ UNM.inner();};</script>
<?
if($order[inflow]=="openstyle"){
	@include dirname(__FILE__) . "/../interpark/_openstyle_order_form.php"; // ������ũ_��Ŭ���
}else{
	@include dirname(__FILE__) . "/../interpark/_order_form.php"; // ������ũ_��Ŭ���
}

?>