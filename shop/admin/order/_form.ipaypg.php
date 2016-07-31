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

$ordno = $_GET[ordno];

$query = "select b.m_id,a.* from ".GD_ORDER." a left join ".GD_MEMBER." b on a.m_no=b.m_no where ordno='$ordno'";
$data = $db->fetch($query);

$inflow = $data[inflow];

if(!$data[deliveryno] && $_delivery[0][deliveryno]) $data[deliveryno] = $_delivery[0][deliveryno];
$_selected[deliveryno][$data[deliveryno]] = "selected";
$_selected[bankAccount][$data[bankAccount]] = "selected";

if(!$data[confirm])$data[confirm] = "admin";

### ī������α� �Ľ�
if ($data[settlelog]){
	$div = explode("\n",$data[settlelog]);
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
$inpk_ordno = $data['inpk_ordno'];
$inpk_regdt = $data['inpk_regdt'];

### �½��÷�
if ((int)$data['step'] >= 2) {

	// �½��÷θ� ���� �߱� �޾Ҵ°�.
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

}
else {
	$GF = false;
}
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
	var orderPrint = window.open("_paper.php?ordno=" + ordno + "&type=" + type,"orderPrint","width=750,height=600,scrollbars=1");
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
	if(confirm("�����õ�/���� ������ �������� ������ �̷�� ���� �ʾ��� ��� \n���Ǿ��� ������ �̻�� ���·� �����ϴ� ����Դϴ�. \n�����Ͻðڽ��ϱ�?")){
		document.frmOrder.mode.value = "restoreDiscount";
		document.frmOrder.submit();
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
	<font class=def>�ֹ���ȣ:</font> <span style="color:<?=($data['inflow']!="sugi") ? "#4f67af" : "#ED6C0A"?>;font:bold 11px verdana"><?=$ordno.(($data['inflow']=="sugi") ? "(�����ֹ�)" : "")?></span>
	<? if ($data[inflow]!=""&&$data[inflow]!="sugi"){ ?><img src="../img/inflow_<?=$data[inflow]?>.gif" align=absmiddle> <?=$r_inflow[$data[inflow]]?><? } ?>
	<? if ($data[pCheeseOrdNo]!=""){ ?><img src="../img/icon_plus_cheese.gif" align=absmiddle> �÷��� ġ�� �ֹ�<? } ?>
	<font class=def>iPay īƮ��ȣ:</font> <span style="color:#4f67af;font:11px verdana"><?=$data['ipay_cartno']?></span>
	<font class=def>iPay ������ȣ:</font> <span style="color:#4f67af;font:11px verdana"><?=$data['ipay_payno']?></span>
	</td>
	<td align=right <?=$hiddenPrint?>>
	<select name="order_print" class="Select_Type1" style="font:8pt ����">
	<option value=""> - �μ⼱�� - </option>
	<option value="report"> �ֹ�������  </option>
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

<?
$query = "
select b.*,a.*, tg.tgsno from
	".GD_ORDER_ITEM." a
	left join ".GD_GOODS." b on a.goodsno=b.goodsno
	left join ".GD_TODAYSHOP_GOODS." tg on a.goodsno=tg.goodsno
where
	a.ordno='$ordno'
order by a.sno
";
$sub = $db->query($query);

?>
<form name=frmOrder action="indb.php" method=post>
<input type=hidden name=mode value="modOrder">
<input type=hidden name=ordno value="<?=$ordno?>">
<input type=hidden name=referer value="<?=$referer?>">
<input type=hidden name=step2 value="<?=$data[step2]?>">

<table class=tb cellpadding=4 cellspacing=0>
<tr height=25 bgcolor=#2E2B29 class=small4 style="padding-top:8px">
	<th><font color=white><a href="javascript:void(0)" onClick="chkBoxAll(document.getElementsByName('chk[]'),'rev')" class=white>����</a></th>
	<th><font color=white>��ȣ</th>
	<th colspan=2><font color=white>��ǰ��</th>
	<th><font color=white>����</th>
	<th><font color=white>��ǰ����</th>
	<th><font color=white>�� ����</th>
	<th><font color=white>�Ұ�</th>
	<th><font color=white>���԰�</th>
	<!--<th><font color=white>�ù��/�����ȣ</th>-->
	<th><font color=white>ó������</th>
	<?if($set[delivery][basis]){?>
	<th nowrap><font color=white >�ù��/�����ȣ</th>
	<?}?>
</tr>
<col align=center span=3><col>
<col align=center span=10>
<?
$idx = $goodsprice = 0;
$icancel = 0;

## ���� �ֹ���ǰ ���� ���ϱ�
$query = "select count(*) from ".GD_ORDER_ITEM." where istep < 40 and ordno='$ordno'";
list($icnt) = $db->fetch($query);

while ($item=$db->fetch($sub)){
	unset($selected);
	$supply += $item[supply] * $item[ea];
	$selected[dvno][$item[dvno]] = "selected";
	$selected[istep][$item[istep]] = "selected";
	$disabled[chk] = ($item[istep]>40) ? "disabled" : "";

	if($icnt == 0){ //��� �ֹ���ǰ�� ���,ȯ���� ���
		$goodsprice += $item[price] * $item[ea];
		$memberdc += $item[memberdc] * $item[ea];
		$coupon += $item[coupon] * $item[ea];
	}else if ($item[istep]<40){
		$goodsprice += $item[price] * $item[ea];
		$memberdc += $item[memberdc] * $item[ea];
		$coupon += $item[coupon] * $item[ea];
	}

	## �ֹ���� �� ī��Ʈ
	if($item[istep] > 40) $icancel++;

	$bgcolor = ($item[istep]>40) ? "#F0F4FF" : "#ffffff";

	if($item[dvcode])$cntDv++;
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
	<div style="padding-top:3"><font class=small1 color=6d6d6d>iPay �ֹ���ȣ/��ǰ��ȣ : <span style="font:10px verdana"><?=$item['ipay_ordno']?> / <?=$item['ipay_itemno']?></span></font></div>
	</td>
	<td nowrap><input type=text name=ea[] value="<?=$item[ea]?>" size=3 class=right readonly style="background-color:#e3e3e3;"></td>
	<td nowrap><input type=text name=price[] value="<?=$item[price]?>" size=7 class=right readonly style="background-color:#e3e3e3;"></td>
	<td width=55 nowrap><?php echo number_format($item['ipay_dcprice']); ?></td>
	<td width=55 nowrap><?php echo number_format(($item['price']*$item['ea'])-$item['ipay_dcprice']); ?></td>
	<td nowrap><input type=text name=supply[] value="<?=$item[supply]?>" size=7 class=right></td>
	<td width=70 nowrap>
	<font class=small4><?=$r_istep[$item[istep]]?></font>
	</td>
	<?if($set[delivery][basis]){?>
	<td><div nowrap><font class=small color=555555><b><?=((!$item[dvcode]||!$item[dvno])&&$data[step])? "-":$r_delivery[$item[dvno]] . "</div><div nowrap>" . $item[dvcode]?></font></div></td>
	<?}?>
</tr>
<?
}

### ���ξ� ���
$discount = $memberdc + $data[emoney] + $data[coupon] + $data[enuri] + $data[ncash_emoney] + $data[ncash_cash];

### �ǵ���Ÿ ������� �����ݾ� ����
$settleprice = $goodsprice + $data[delivery] - $discount + $data[eggFee];
?>
</table>

<table cellpadding=0 cellspacing=0 width=100%>
<tr><td width=60% style="padding:5px 0 0 12px"><a href="javascript:chkCancel(0)"><img src="../img/btn_cancelorder.gif" border=0></a></td>
<td width=40% align=right style="padding-right:5px"><a href="javascript:manual('<?=$guideUrl?>board/view.php?id=order&no=3')"><img src="../img/btn_cancel_manual.gif" border=0></a>
</td></tr></table>



<!-- ���������Է� ���� -->
<?if($set[delivery][basis]){?>
<div style="padding:5px 0 0 12px">
<?if($data[step]){?><a href="javascript:registerDelivery()"><img src="../img/btn_input_delinumber.gif" border=0></a><?}?>
</div>
<?}?>
<!-- ���������Է� �� -->
<div id=layer_cancel style="display:none;padding-top:10px">
<iframe id=ifrmCancel name=ifrmCancel style="width:100%;height:0;" frameborder=0></iframe>
</div><p>
<?
$selected[step][$data[step]] = "selected";
$selected[step2][$data[step2]] = "selected";
$selected[deliveryno][$data[deliveryno]] = "selected";

if ($memberdc) $dc[memberdc] = "ȸ������ (<font color=0074BA class=ver81>".number_format($memberdc)."</font>��)";
if ($data[coupon]) $dc[coupon] = "�������� (<font color=0074BA class=ver81>".number_format($data[coupon]-$data[about_dc_sum])."</font>��)";
if ($data[about_coupon_flag]) $dc[aboutcoupon] = "��ٿ����� (<font color=0074BA class=ver81>".number_format($data[about_dc_sum])."</font>��)";
if ($data[emoney]) $dc[emoney] = "�����ݻ�� (<font color=0074BA class=ver81>".number_format($data[emoney])."</font>��)";
if ($data[ncash_emoney]) $dc[ncash_emoney] = "���̹����ϸ������ (<font color=0074BA class=ver81>".number_format($data[ncash_emoney])."</font>��)";
if ($data['ncash_cash']) $dc['ncash_cash'] = "���̹�ĳ����� (<font color=0074BA class=ver81>".number_format($data['ncash_cash'])."</font>��)";
$dc[enuri] = "������ <input type=text name=enuri value='$data[enuri]' size=6 class='ver81 right' style='color:#0074BA'> ��";
?>
<div class=title2>&nbsp;<img src="../img/icon_process.gif" align=absmiddle><font color=508900>���ֹ�����</font></div>

<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>�ֹ�����</td>
	<td style="padding:2px 10px">
	<table width=100%>
	<tr>
		<td><font class="small1">
		<? if (!$data['step2']){ ?>
			<select name="step">
			<? foreach ($r_step as $k=>$v){ if ($k>9) break; ?>
			<option value="<?=$k?>" <?=$selected[step][$k]?>><?=$v?>
			<? } ?>
			</select>
			<span class="extext" style="margin-top: 10px;">iPay PG �ֹ����� ��� ��������� ��ȯ �� �ݵ�� �����ȣ�� �Է��ؾ� �մϴ�.</span>
		<?
			if($icancel) echo "�ֹ���� $icancel ���Դϴ�.";
		} else {
			echo getStepMsg($data[step],$data[step2],$data[ordno]);
		}
		?>
		<?
		list($cnt) = $db->fetch("select count(*) from ".GD_ORDER." where oldordno='$ordno'");
		if($cnt){
			$query = "select ordno from ".GD_ORDER." where oldordno='$ordno'";
			$nres = $db->query($query);
			while($ndata = $db->fetch($nres))	$newordno[] = "<a href=\"javascript:popup('popup.order.php?ordno=".$ndata[ordno]."',800,600)\"><font color=0074BA class=ver81><b><u>".$ndata[ordno]."</u></b></font></a>";
		?>
		&nbsp;<img src="../img/arrow_gray.gif" align=absmiddle><font class=small1 color=444444>��ȯ���� ���� �ڵ������� <font color=ED00A2>�±�ȯ�ֹ���</font> (<?=implode(',',$newordno)?>) �� �ֽ��ϴ�.</font>
		<?
		}
		?>
		<?
		if($data[oldordno]){
		?>
		&nbsp;<img src="../img/arrow_gray.gif" align=absmiddle><font class=small1 color=444444>�� �ֹ��� <font color=ED00A2>��ȯ��û��</font> (<a href="javascript:popup('popup.order.php?ordno=<?=$data[oldordno]?>',800,600)"><font color=0074BA class=ver81><b><u><?=$data[oldordno]?></u></b></font></a>) ���� �ڵ������� ���ֹ� �Դϴ�.</font>
		<?
		}
		?>
		</td>
		<td align="right">
		<?if($data[step2] >= 50){?>
		<span><a href="indb.php?mode=faileRcy&ordno=<?=$ordno?>&returnUrl=<?=urlencode($referer)?>&popup=<?=$popup?>" onclick="return confirm('�ֹ������� �����¿��� �ܼ��� ����Ÿ�� �Ա�Ȯ�� ���·� �����ϴ� ����Դϴ�.\n\n�������ֹ��� �ٽ� �õ� ���з� ������ �Ұ��մϴ�.\n\n�����Ͻ� �ֹ�[<?=$ordno?>]�� ������ �Ա�Ȯ������ �����Ͻðڽ��ϱ�?')"><img src="../img/btn_order_try_return.gif"></a></span>
		<?}?>
		<span style='width:80'><a href="indb.php?mode=delOrder&ordno=<?=$ordno?>&returnUrl=<?=urlencode($referer)?>&popup=<?=$popup?>" onclick="return confirm('�ֹ������� �� �ֹ�����Ÿ�� �ܼ��� ������ �ϴ� ����Դϴ�.\n\n���� �ٷ� ������ �ϸ� �� �ֹ��� ���� ���, ������, ������ ȯ���� �ȵ˴ϴ�.\n\n\���, ������, ������ ȯ���Ϸ��� �ݵ�� �ֹ����(������ ��ǰ���)�� ���� ���ּ���.\n\n�ֹ���Ұ� �Ǹ� ������� ���, ������, ������ ȯ���˴ϴ�.\n\n�׸��� �ֹ������� �Ͻñ� �ٶ��ϴ�.\n\n�ѹ� ������ �ֹ��� ������ �Ұ����մϴ�. ������ �����ϼ���.\n\n�����Ͻ� �ֹ�[<?=$ordno?>]�� ������ �����Ͻðڽ��ϱ�?')"><img src="../img/btn_delete_order.gif"></a><span>
		</td>

	</tr>
	</table>

	</td>
</tr>
</table><p>

<div class=title2>&nbsp;<img src="../img/icon_process.gif" align=absmiddle><font color=508900>�����ݾ�����</font></div>

<table class=tb>
<col class=cellC><col class=cellL>
<!--<col class=cellC><col class=cellL width=120><col class=cellL>-->
<tr>
	<td>�ֹ��ݾ�</td>
	<td width=110 align=right><font class=ver8><?=number_format($goodsprice+$data[delivery]+$data[eggFee])?></font>��</td>
	<td><img src="../img/arrow_gray.gif" align=absmiddle><font class=small color=444444>��ǰ���� (<font color=0074BA class=ver81><?=number_format($goodsprice)?></font>��)
	<? if ($data[delivery]){ ?>
	+ ��ۺ� (<font color=0074BA class=ver81><?=number_format($data[delivery])?></font>��)
	<? } ?>
	<? if ($data[eggFee]){ ?>
	+ ������������� (<font color=0074BA class=ver81><?=number_format($data[eggFee])?></font>��)
	<? } ?>
	</td>
</tr>
<tr>
	<td>���ξ�</td>
	<td align=right><font class=ver8>- <?=number_format($discount)?></font>��</td>
	<td><img src="../img/arrow_gray.gif" align=absmiddle><font class=small color=444444><?=implode(" + ",$dc)?>
	<? if($data[step2] > 40){ ?>&nbsp;<a href="javascript:couponDelPop();"><img src="../img/btn_savedmoney.gif" align=absmiddle></a>
	<? }else{ ?>&nbsp;<img src="../img/btn_savedmoney_off.gif" align=absmiddle></a>
	<? } ?>
	</td>
</tr>
<tr>
	<td>�����ݾ�</td>
	<td align=right><font color=0074BA class=ver8><b><?=number_format($settleprice)?></b></font>��</td>
	<td><font class=small color=444444><? if ($settleprice!=$data[settleprice]){ ?><img src="../img/arrow_gray.gif" align=absmiddle>�����ֹ��ݾ�  (<font color=0074BA class=ver81><?=number_format($data[settleprice])?></font>��)
	 + ��ұݾ� �հ� (<font color=0074BA class=ver81><?=number_format($settleprice-$data[settleprice])?></font>��)
	<? } ?>
	</td>
</tr>
</table><p>
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
	while($coupon = $db->fetch($res)){
		if($coupon[downloadsno]){
			$res_cp = $db->query("select p.number from gd_offline_download d left outer join gd_offline_paper p on d.paper_sno = p.sno where  d.sno = '$coupon[downloadsno]'");
			$rst_cp = $db->fetch($res_cp);
			$coupon[couponcd] = $rst_cp[number];
		}
?>
<tr>
	<td nowrap><?=$coupon[couponcd]?></td>
	<td nowrap><?=$coupon[coupon]?></td>
	<td nowrap><font class=ver8 color=444444>
		<?
		if($coupon[dc]){
			if(substr($coupon[dc],-1,1) == '%')	echo "���� ".$coupon[dc];
			else echo "���� ".number_format($coupon[dc])."��";
		}
		if($coupon[emoney]){
			if(substr($coupon[emoney],-1,1) == '%')	echo "���� ".$coupon[emoney];
			else echo "���� ".number_format($coupon[emoney])."��";
		}
		?>
	</td>
	<td nowrap><?=$coupon[regdt]?></td>
</tr>
<?}?>
</table><p>
<?}?>

<!-- ������ũ_Ŭ���� -->
<div id="interpark_claim"></div>

<?
## okcashbag ������ ǥ��
if($data[cbyn] == "Y"){
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
$query = "select distinct a.cancel,b.* from ".GD_ORDER_ITEM." a left join ".GD_ORDER_CANCEL." b on a.cancel=b.sno where a.istep = 44 and a.cyn in ('r','y') and a.ordno='$ordno' and (b.rprice OR b.remoney OR b.rfee)";
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
		<td><? if ($data[m_id]) { ?><span id="navig" name="navig" m_id="<?=$data[m_id]?>" m_no="<?=$data[m_no]?>" popup="<?=$popup?>"><? } ?><font color=0074BA><b>
		<?=$data[nameOrder]?>
		<? if ($data[m_id]){ ?>/ <?=$data[m_id]?></b></font></span>
		<? } ?>
		</td>
	</tr>
	<tr>
		<td>�̸���</td>
		<td><font class=ver8><?=$data[email]?></font> <a href="javascript:popup('../member/email.php?type=direct&email=<?=$data['email']?>',780,600)"><font color="#FF6C4B"><img src="../img/btn_smsmailsend.gif" align=absmiddle></font></a></td>
	</tr>
	<tr>
		<td>����ó</td>
		<td class=ver8>
		<?=$data[phoneOrder]?><?getlinkPc080($data['phoneOrder'],'phone')?> / <?=$data[mobileOrder]?><?getlinkPc080($data['mobileOrder'],'mobile')?> <a href="javascript:popup('../member/popup.sms.php?mobile=<?=$data['mobileOrder']?>',780,600)"><img src="../img/btn_sms.gif" align=absmiddle></a>
		</td>
	</tr>
	<tr>
		<td>�ֹ���</td>
		<td><font class=ver8><?=$data[orddt]?></td>
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
		<input type=text name=nameReceiver value="<?=$data[nameReceiver]?>" style="width:115px" class=line>
		</td>
	</tr>
	<tr>
		<td>����ó</td>
		<td>
		<input type=text name="phoneReceiver" value="<?=$data[phoneReceiver]?>" style="width:95px" class=line><?getlinkPc080($data['phoneReceiver'],'phone')?><?if($popup != 1){?> /<?}?> <input type=text name="mobileReceiver" value="<?=$data[mobileReceiver]?>" style="width:95px" class=line><?getlinkPc080($data['mobileReceiver'],'mobile')?> <a href="javascript:popup('../member/popup.sms.php?mobile=<?=$data['mobileReceiver']?>',780,600)"><font color="#FF6C4B"><img src="../img/btn_sms.gif" align=absmiddle></font></a>
		</td>
	</tr>
	<tr>
		<td>�ּ�</td>
		<td><font color=444444>
		<input type=text name=zipcode[] size=3 readonly value="<?=substr($data[zipcode],0,3)?>" class=line> - <input type=text name=zipcode[] size=3 readonly value="<?=substr($data[zipcode],4)?>" class=line>
		<a href="javascript:popup('../proc/popup_zipcode.php?form=opener.document.frmOrder',400,500)"><img src="../img/btn_zipcode.gif" align=absmiddle></a>
		</td>
	</tr>
	<tr>
		<td></td>
		<td colspan=3><input type=text name=address style="width:100%" value="<?=$data[address]?>" class=line></td>
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
		<td><?=$r_settlekind[$data[settlekind]]?><!--<?if($data[settlekind]=='c'){?>&nbsp;<a href='javascript:cardCancel();'>[�ſ�ī�����]</a><?}?>--></td>
	</tr>
	<? if ($data[settlekind]=="a"){ ?>
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
		<td><input type=text name=bankSender value="<?=$data[bankSender]?>"></td>
	</tr>
	<? } else if ($data[settlekind]=="v"){ ?>
	<tr>
		<td>�������</td>
		<td><?=$data[vAccount]?></td>
	</tr>
	<? } ?>
	<tr>
		<td>����Ȯ����</td>
		<td><font class=ver8>
		<? if ($data[settlekind]=="c" && $data[settlelog]){ ?><font class=small1 color=FD4700><b>[<?=$r_settlelog['�������']?>]</b></font><? } ?>
		<?=$data[cdt]?>
		</td>
	</tr>
	<? if ($data['settlekind'] != 'c'){ ?>
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
		else if ($data['cashreceipt']){
			echo $data['cashreceipt'];
		}
		?>
		<div><input type="checkbox" name="cashreceipt_ectway" value="Y" class="null" <?=($data['cashreceipt_ectway'] == 'Y' ? 'checked' : '');?>> ���ݿ����� �����߱� �� �������� �Ǿ��ٸ� üũ�ϼ���.(�ߺ����� ��������)</div>
		</td>
	</tr>
	<? } ?>
	<? if ( !empty($_taxstate) ){ ?>
	<tr>
		<td>���ݰ�꼭</td>
		<td><?=$_taxstate?></td>
	</tr>
	<? } ?>
	<? if ($data[inflow]!="" && $data[inflow]!="sugi"){ ?>
	<tr>
		<td>����ó�ֹ�</td>
		<td><img src="../img/inflow_<?=$data[inflow]?>.gif" align=absmiddle> <?=$r_inflow[$data[inflow]]?></td>
	</tr>
	<? } ?>
	<? if ($data[eggyn]!="n"){ ?>
	<tr>
		<td>���ں�������</td>
		<td>
		<? if ($data[eggno]!=""){ ?><a href="javascript:popupEgg('<?=$egg['usafeid']?>', '<?=$ordno?>')"><font class=ver71 color=0074BA><b><?=$data[eggno]?> <font class=small1>(������ ����)</b></font></a><? } ?>
		<? if ($data[eggno]=="" && $r_settlelog['����޼���']){ ?><font class=small1 color=FD4700><b>[<?=$r_settlelog['����޼���']?>]</b></font><? } ?>
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
	<?if($data[deli_title] != null){?>
	<tr>
		<td>��۹��</td>
		<td><?if($data['deli_msg'] != "���� ���� ��ۺ�"){?><?=$data['deli_title']?><?}?> <?=( $data['deli_msg'] )?$data['deli_msg']:""?></td>
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
		<? if($data['step'] >= 1 && $data['step'] < 4 && !$set['delivery']['basis']): ?>
			<select name="deliveryno">
			<option value="">==�ù��==</option>
			<? foreach((array)$_delivery as $v): ?>
				<option value="<?=$v['deliveryno']?>" <?=$_selected['deliveryno'][$v['deliveryno']]?>><?=$v['deliverycomp']?>
			<? endforeach; ?>
			</select>
			<input type='text' name='deliverycode' value="<?=$data['deliverycode']?>" class=line>
		<? else: ?>
			<? if($data['deliverycode']) : ?>
				<?=$r_delivery[$data['deliveryno']]?> <?=$data['deliverycode']?>
				<div class=small1 color=444444>�Ʒ� ��ۻ������� ��ư�� ���� Ȯ���ϼ���.</div>
			<? endif; ?>
			<input type='hidden' name='deliveryno' value='<?=$data['deliveryno']?>'>
			<input type='hidden' name='deliverycode' value='<?=$data['deliverycode']?>'>
		<? endif; ?>
		</td>
	</tr>
	<? } ?>

	<? if ($data[deliverycode] || $cntDv ){ ?>
	<tr>
		<td>�������</td>
		<td><a href="javascript:popup('popup.delivery.php?ordno=<?=$ordno?>',800,500)"><img src="../img/btn_delifind.gif" border=0></a></td>
	</tr>
	<? } ?>
	<tr>
		<td>�����(�����)</td>
		<td><font class=ver8><?=$data[ddt]?></td>
	</tr>
	<? if ($data[confirmdt]){ ?>
	<tr>
		<td>��ۿϷ���</td>
		<td><font class=ver8><?=$data[confirmdt]?>(<?=$data[confirm]?>)</td>
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
		<td><textarea name=memo style="width:100%;height:100px"><?=$data[memo]?></textarea></td>
	</tr>
	<tr height=25>
		<td>�����޸�</td>
		<td><textarea name=adminmemo style="width:100%;height:100px"><?=$data[adminmemo]?></textarea></td>
	</tr>
	<tr height=25>
		<td>�����α�</td>
		<td><textarea style="width:100%;height:100px;overflow:visible;font:9pt ����ü;padding:10px 0 0 8px"><?=trim($data[settlelog])?></textarea></td>
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
if($inflow=="openstyle"){
	@include dirname(__FILE__) . "/../interpark/_openstyle_order_form.php"; // ������ũ_��Ŭ���
}else{
	@include dirname(__FILE__) . "/../interpark/_order_form.php"; // ������ũ_��Ŭ���
}

?>