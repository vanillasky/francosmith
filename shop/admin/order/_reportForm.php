<?
@include "../../conf/egg.usafe.php";

### �ֹ�����Ʈ ���۷�
$referer = ($_GET[referer]) ? $_GET[referer] : $_SERVER[HTTP_REFERER];

### ��һ��� �迭����
$r_cancel = codeitem("cancel");
$r_cancel[0] = "�ֹ���Һ���";

### ��۾�ü ����
$query = "select * from ".GD_LIST_DELIVERY." where useyn='y' order by deliverycomp";
$res = $db->query($query);
while ($data=$db->fetch($res)){
	$_delivery[] = $data;
}

### �Աݰ��� ����
$query = "select * from ".GD_LIST_BANK." order by sno";
$res = $db->query($query);
while ($data=$db->fetch($res)){
	$data['name'] .= ($data['useyn'] == 'y' ? '' : ' (�����Ѱ���)');
	$_bank[] = $data;
}

$ordno = $_GET[ordno];
$order = Core::loader('order');
$order->load($ordno);


$_selected[deliveryno][$order[deliveryno]] = "selected";
$_selected[bankAccount][$order[bankAccount]] = "selected";

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

?>

<style>
.title2 {
	font-weight:bold;
	padding-bottom:5px;
}
</style>
<script>
function chkCancel()
{
	var sno = new Array();
	var el = document.getElementsByName('chk[]');
	for (i=0;i<el.length;i++) if (el[i].checked) sno[sno.length] = el[i].value;
	if (sno.length==0){
		alert("��������� ��ǰ�� �������ּ���");
		return;
	}
	_ID('layer_cancel').style.display = "block";
	ifrmCancel.location.href = "ifrm.cancel.php?ifrmScroll=1&ordno=<?=$ordno?>&chk=" + sno.join();
}
function orderPrint(ordno,type)
{
	if (!type){
		alert("�μ��� ���� ������ �����ϼ���");
		return;
	}
	var orderPrint = window.open("_paper.php?ordno=" + ordno + "&type=" + type,"orderPrint","width=750,height=600");
	orderPrint.window.print();
}
function escrow_confirm()
{
	var obj = document.ifrmHidden;
	obj.location.href = "../../order/card/<?=$cfg[settlePg]?>/escrow_gate.php?ordno=<?=$ordno?>";
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
</script>

<table width=100% cellpadding=0 cellspacing=0><tr><td style="padding:5px 10px;background:#f7f7f7;margin:10px 0;border:3px solid #C6C6C6;">
<table width=100%>
<tr>
	<td id="orderInfoBox">
	<font class=def>�ֹ���ȣ:</font> <span style="color:#000000;font:bold 14px verdana"><?=$ordno?></span>
	<? if ($order[inflow]!=""&&$order[inflow]!="sugi"){ ?><img src="../img/inflow_<?=$order[inflow]?>.gif" align=absmiddle> <?=$r_inflow[$order[inflow]]?><? } ?>
	</td>
	<td align=right <?=$hiddenPrint?>>
	<select name="order_print" class="Select_Type1" style="font:8pt ����">
	<option value=""> - �μ⼱�� - </option>
	<option value="report"> �ֹ�������  </option>
	<option value="reception"> ���̿�����  </option>
	<option value="tax"> ���ݰ�꼭  </option>
	<!--<option value="particular"> �ŷ�����  </option>    -->
	</select>
	<a href="javascript:orderPrint(<?=$ordno?>, document.getElementsByName('order_print')[0].value);"><img src="../img/btn_print.gif" border="0" align="absmiddle"></a>
	</td>
</tr>
</table>
</td></tr>
<tr><td height=8></td></tr>
</table>

<form name=frmOrder action="indb.php" method=post>
<input type=hidden name=mode value="modOrder">
<input type=hidden name=ordno value="<?=$ordno?>">
<input type=hidden name=referer value="<?=$referer?>">

<table class=tb cellpadding=4 cellspacing=0>
<tr height=25 bgcolor=#2E2B29 class=small4 style="padding-top:8px">
	<th><font color=white>��ȣ</th>
	<th colspan=2><font color=white>��ǰ�� / ��ǰ�ڵ�</th>
	<th><font color=white>����</th>
	<th><font color=white>�ǸŰ�</th>
	<th><font color=white>���αݾ�</th>
	<th><font color=white>ȸ������</th>
	<th><font color=white>�����ݾ�</th>
	<!--<th><font color=white>�ù��/�����ȣ</th>-->
	<th><font color=white>ó������</th>
</tr>
<col align=center span=2><col>
<col align=center span=9>
<?
$idx = $goodsprice = 0;

## ���� �ֹ���ǰ ���� ���ϱ�
$query = "select count(*) from ".GD_ORDER_ITEM." where istep < 40 and ordno='$ordno'";
list($icnt) = $db->fetch($query);

unset($goodsprice,$memberdc,$coupon,$dc);

foreach($order->getOrderItems() as $item) {

	if ($item->hasCanceled()) {
		$disabled[chk] = $item->hasCanceled() ? "disabled" : "";
		$bgcolor = "#F0F4FF";
	}
	else {
		$disabled[chk] = "";
		$bgcolor = "#ffffff";
	}

?>
<input type=hidden name=sno[] value="<?=$item[sno]?>">
<tr bgcolor="<?=$bgcolor?>">
	<td width=35 nowrap><font class=ver8 color=444444><?=++$idx?></td>
	<td width=50 nowrap><?=goodsimg($item[img_s],40,"style='border:1 solid #cccccc'",1)?></td>
	<td width=100%><a href="javascript:popup('../goods/popup.register.php?mode=modify&goodsno=<?=$item[goodsno]?>',825,600)">
	<font class=def><?=$item[goodsnm]?>
	<? if ($item[opt1]){ ?>[<?=$item[opt1]?><? if ($item[opt2]){ ?>/<?=$item[opt2]?><? } ?>]<? } ?>
	<? if ($item[addopt]){ ?><div>[<?=str_replace("^","] [",$item[addopt])?>]</div><? } ?></a>
	<br><?=$item[goodscd]?><!-- ����û���� �ڵ��߰� - mickey 2007-01-03  -->
	<div class=small4>������ : <?=$item[maker] ? $item[maker] : '��'?></div>
	<div class=small4>�귣�� : <?=$item[brandnm] ? $item[brandnm] : '��'?></div>
	<? if ($item[deli_msg]){ ?><div><font class=small1 color=6d6d6d>(<?=$item[deli_msg]?>)</font></div><? } ?>
	</td>
	<td nowrap><input type=text name=ea[] value="<?=$item[ea]?>" size=3 class=right></td>
	<td nowrap><input type=text name=price[] value="<?=$item[price]?>" size=7 class=right></td>
	<td width=65 nowrap><?=number_format($item->getPercentCouponDiscount() + $item->getSpecialDiscount())?></td>
	<td width=65 nowrap><?=number_format($item->getMemberDiscount())?></td>
	<td width=65 nowrap><?=number_format($item->getSettleAmount())?></td>
	<!--<td nowrap>
	<select name=dvno[]>
	<option value="">==�ù��==
	<? foreach ($_delivery as $v){ ?>
	<option value="<?=$v[deliveryno]?>" <?=$selected[dvno][$v[deliveryno]]?>><?=$v[deliverycomp]?>
	<? } ?>
	</select>
	<input type=text name=dvcode[] size=15 value="<?=$item[dvcode]?>">
	</td>-->
	<td width=70 nowrap>
	<font class=small4><?=$r_istep[$item[istep]]?></font>
	<? if ($item[istep]==41 || ($item[istep]==44 && $item[cyn].$item[dyn]=="nn")){ ?><div><a href="indb.php?mode=recovery&sno=<?=$item[sno]?>" onclick="return confirm(' ����ó���Ͻðڽ��ϱ�?')"><img src="../img/btn_return.gif" border=0></a></div><? } ?>
	<!--<div><?=$item[cyn]?>|<?=$item[dyn]?></div>-->
	</td>
</tr>
<?
}
?>
</table>

<div id=layer_cancel style="display:none;padding-top:10px">
<iframe id=ifrmCancel name=ifrmCancel style="width:100%;height:0;" frameborder=0></iframe>
</div><p>

<?
$selected[step][$order[step]] = "selected";
$selected[step2][$order[step2]] = "selected";
$selected[deliveryno][$order[deliveryno]] = "selected";
?>

	</td>
</tr>
</table><p>

<div class=title2>&nbsp;<img src="../img/icon_process.gif" align=absmiddle><font color=508900>�����ݾ�����</font></div>
<table border=2 bordercolor=627dce style="border-collapse:collapse" width=100%>
<tr><td style="padding:10px 0;">

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
</td></tr></table><p>
<?
if($r_stepi[$order[step]][$order[step2]] == "ȯ�ҿϷ�"){
?>
<div class=title2>&nbsp;<img src="../img/icon_process.gif" align=absmiddle><font color=494949>ȯ�ұݾ�����</font></div>
<input type=hidden name='cancelsno' value='<?=$row2[sno]?>'>
<table class=tb cellpadding=4 cellspacing=0>
<tr>
	<td width=110 align=center bgcolor=#F6F6F6>ȯ�Ҽ�����</td>
	<td style="padding:2px 10px">
		<?=number_format($order->getRefundedFeeAmount())?>��
	</td>
</tr>
<tr>
	<td width=110 align=center bgcolor=#F6F6F6>ȯ�ұݾ�</td>
	<td style="padding:2px 10px">
		<?=number_format($order->getRefundedAmount())?>��
	</td>
</tr>
</table><p>
<?
}
?>
<table width=100% cellpadding=0 cellspacing=0>
<col span=3 valign=top>
<tr>
	<td width=50%>

	<div class=title2>&nbsp;<img src="../img/icon_process.gif" align=absmiddle><font color=494949>�ֹ�������</font></div>
	<table class=tb>
	<col class=cellC><col class=cellL>
	<tr>
		<td>����/�ֹ���(ID)</td>
		<td>
		<?=$order[nameOrder]?>
		<? if ($order[m_id]){ ?>/ <font color=0074BA><b><?=$order[m_id]?></b></font>
		<? } ?>
		</td>
	</tr>
	<tr>
		<td>�̸���</td>
		<td><font class=ver8><?=$order[email]?></font></td>
	</tr>
	<tr>
		<td>����ó</td>
		<td><font class=ver8>
		<?=$order[phoneOrder]?> / <?=$order[mobileOrder]?>
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

	<div class=title2>&nbsp;<img src="../img/icon_process.gif" align=absmiddle><font color=494949>����������</font></div>
	<table class=tb>
	<col class=cellC><col class=cellL>
	<tr>
		<td>������</td>
		<td>
		<?=$order[nameReceiver]?>
		</td>
	</tr>
	<tr>
		<td>����ó</td>
		<td>
		<?=$order[phoneReceiver]?> &nbsp;/&nbsp;
		<?=$order[mobileReceiver]?>
		</td>
	</tr>
	<tr>
		<td>�ּ�</td>
		<td><font color=444444>
		<?php echo $order[zonecode]; ?>
		<?php if(str_replace("-", "", $order[zipcode])) echo '('.substr($order[zipcode],0,3).' - '.substr($order[zipcode],4).')'; ?>
		</td>
	</tr>
	<tr>
		<td></td>
		<td colspan=3><?if($order['road_address']) { ?>���� : <? } ?><?=$order[address]?><div style="padding-top:5px;" id="div_road_address"><?if($order['road_address']) { ?>���θ� : <?=$order['road_address']?><? } ?></div></td>
	</tr>
	</table>

	</td>
</tr><tr><td height=15></td></tr>
<tr>
	<td>

	<div class=title2>&nbsp;<img src="../img/icon_process.gif" align=absmiddle><font color=494949>��������</div>
	<table class=tb>
	<col class=cellC><col class=cellL>
	<tr>
		<td>��������</td>
		<td><?=$r_settlekind[$order[settlekind]]?></td>
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
		<? if ($order[settlekind]=="c" && $order[settlelog]){ ?><font class=small1 color=FD4700><b>[<?=$r_settlelog['�������']?>]</b></font><? } ?>
		<?=$order[cdt]?>
		</td>
	</tr>
	<? if ($order[cashreceipt]){ ?>
	<tr>
		<td>���ݿ�������ȣ</td>
		<td><?=$order[cashreceipt]?></td>
	</tr>
	<? } ?>
	<? if ( !empty($_taxstate) ){ ?>
	<tr>
		<td>���ݰ�꼭</td>
		<td><?=$_taxstate?></td>
	</tr>
	<? } ?>
	<? if ($order[inflow]!=""&&$order[inflow]!="sugi"){ ?>
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

	<div class=title2>&nbsp;<img src="../img/icon_process.gif" align=absmiddle><font color=494949>�������</div>
	<table class=tb>
	<col class=cellC><col class=cellL>
	<?if($order[deli_title] != null){?>
	<tr>
		<td>��۹��</td>
		<td><?if($order['deli_msg'] != "���� ���� ��ۺ�"){?><?=$order['deli_title']?><?}?> <?=( $order['deli_msg'] )?$order['deli_msg']:""?></td>
	</tr>
	<?}?>
	<tr>
		<td>�����ȣ</td>
		<td>
		<?
		if($order[step] >= 1 && $order[step] < 4){?>
		<select name=deliveryno>
		<option value="">==�ù��==
		<?
		if ($_delivery){ foreach ($_delivery as $v){ ?>
		<option value="<?=$v[deliveryno]?>" <?=$_selected[deliveryno][$v[deliveryno]]?>><?=$v[deliverycomp]?>
		<? }} ?>
		</select>
		<input type=text name=deliverycode value="<?=$order[deliverycode]?>" class=line>
		<?}else{?>
		<font class=small1 color=444444>�Ʒ� ��ۻ������� ��ư�� ���� Ȯ���ϼ���.</font>
		<input type=hidden name='deliveryno' value='<?=$order[deliveryno]?>'>
		<input type=hidden name='deliverycode' value='<?=$order[deliverycode]?>'>
		<?}?>
		</td>
	</tr>
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
		<? } else if ($order[escrowconfirm]==2){ ?>��ۿϷ�<? } ?>
		</td>
	</tr>
	<? } ?>
	</table>

	</td>
</tr><tr><td height=15></td></tr>
<tr>
	<td>

	<div class=title2>
	<span style="padding-right:10px">&nbsp;<img src="../img/icon_process.gif" align=absmiddle><font color=494949>��û����/���޸�</span>
	</div>
	<table class=tb>
	<col class=cellC><col class=cellL>
	<tr height=25>
		<td>����û����</td>
		<td><?=nl2br($order[memo])?></td>
	</tr>
	<tr height=25>
		<td>�����޸�</td>
		<td><?=nl2br($order[adminmemo])?></td>
	</tr>
	<tr height=25>
		<td>�����α�</td>
		<td><textarea style="width:100%;height:100px;overflow:visible;font:9pt ����ü;padding:10px 0 0 8px"><?=trim(strcut($order[settlelog],134))?></textarea></td>
	</tr>
	</table>

	</td>
	<td></td>
	<td>

	<div class=title2>&nbsp;<img src="../img/icon_process.gif" align=absmiddle><font color=494949>��ҿ�û ����Ʈ</div>
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
		<div style="float:left" class=ver8 color=444444><?=$data[regdt]?></div>
		<div style="float:right">[<?=$r_cancel[$data[code]]?>]</div>
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
		<tr bgcolor=#f7f7f7>
			<td>- <?=$item[goodsnm]?> <?=$item[ea]?>��</td>
			<td align=right><?=$r_istep[$item[prev]]?> �� <?=$r_istep[$item[next]]?></td>
		</tr>
		<? } ?>
		</table>
		</div>

		<? if ($data[memo]){ ?>
		<div style="margin:5px" class=small><?=nl2br($data[memo])?></div>
		<? } ?>
		</td>
	</tr>
	<? } ?>
	</table>

	</td>
</tr>
</table>

<div class=button <?=$hiddenPrint?>>
<input type=image src="../img/btn_modify.gif">
<a href='<?=$referer?>'><img src='../img/btn_list.gif'></a>
</div>

</form>

<?
if($order['inflow']=="openstyle"){
	@include dirname(__FILE__) . "/../interpark/_openstyle_order_form.php"; // ������ũ_��Ŭ���
}else{
	@include dirname(__FILE__) . "/../interpark/_order_form.php"; // ������ũ_��Ŭ���
}
?>