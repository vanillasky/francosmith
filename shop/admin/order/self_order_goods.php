<?
	include "../_header.popup.php";
	include "../../conf/config.pay.php";
?>
<style type="text/css">
	.goodsTotalBox { width:100%; padding:10px; text-align:right; background:#E6EEF9; }
	.goodsTotalPrice { font-size:13px; font-weight:bold; }
	.goodsBottomButton { padding:5px 0px; }
</style>

<script language="JavaScript">
	function resizeIframe(iFrameID) {
		if(parent._ID(iFrameID)) {
			parent._ID(iFrameID).style.height = document.body.scrollHeight;
			document.body.style.margin = '0';
			return true;
		}
	}

	function act_delete(){

		if ( PubChkSelect( fmList['selectGood[]'] ) == false ){
			alert( "�����Ͻ� ������ �����Ͽ� �ֽʽÿ�." );
			return;
		}

		if ( confirm( "���û�ǰ�� �����Ͻðڽ��ϱ�?" ) == false ) return;

		var idx = 0;
		var codes = new Array();
		var count = fmList['selectGood[]'].length;

		if ( count == undefined ) codes[ idx++ ] = fmList['selectGood[]'].value;
		else {

			for ( i = 0; i < count ; i++ ){
				if ( fmList['selectGood[]'][i].checked ) codes[ idx++ ] = fmList['selectGood[]'][i].value;
			}
		}

		fmList.delList.value = codes.join( ";" );
		fmList.mode.value = "goodsDelete";
		fmList.submit();
	}

	function modifyEa() {
		fmList.mode.value = "modifyEa";
		fmList.submit();
	}

	function editOption(idx) {
		window.open('../order/self_order_goods_edit.php?idx='+idx, 'POP_selfOptionEditor', "width=350,height=500");
	}

	function increStock(stockIptID) {
		$(stockIptID).value = ($(stockIptID).value) * 1 + 1;
	}

	function decreStock(stockIptID) {
		if(($(stockIptID).value * 1) > 1) $(stockIptID).value = ($(stockIptID).value) * 1 - 1;
	}

	var chkBoxState = false;
	function chkAll(chkBoxName) {
		chkObj = document.getElementsByName(chkBoxName);
		chkBoxState = !chkBoxState;
		for(i = 0; i < chkObj.length; i++) {
			chkObj[i].checked = chkBoxState;
		}
	}

	window.onload = function() {
		resizeIframe('selfOrderGoods');
	}
</script>

<table cellpadding="0" cellspacing="0" border="0" width="100%" style="margin-bottom:7px;">
	<tr valign="bottom">
		<td align="left" style="font-weight:bold; font-size:14px; font-family:dotum;"><img src="../img/titledot.gif" align="absbottom" style="margin-right:5px;" />�ֹ���ǰ</td>
		<td align="right"><a href="javascript:;" onclick="popup2('../order/popup.self_order_goods.php?memID=' + parent.$('m_id').value, 1000, 800, 1)"><img src="../img/su_btn01.gif" align="absmiddle" /></a></td>
	</tr>
</table>

<div>
<form name="fmList" method="post" action="../order/indb.self_order.php">
<input type="hidden" name="selfOrderID" id="selfOrderID" value="<?=$_SESSION['SelfOrder']['id']?>" />
<input type="hidden" name="delList" id="delList" />
<input type="hidden" name="mode" id="mode" />
<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr><td class="rnd" colspan="7"></td></tr>
	<tr class="rndbg">
		<th width="40" onclick="javascript:chkAll('selectGood[]');" style="cursor:pointer;">����</th>
		<th width="40">��ȣ</th>
		<th>��ǰ��</th>
		<th width="110">������</th>
		<th width="150">����</th>
		<th width="110">�ǸŰ�</th>
		<th width="110">�հ�</th>
	</tr>
	<tr><td class="rnd" colspan="7"></td></tr>
<?
	$cart = new Cart();
	$cart->calcu();

	if($imax = sizeof($cart->item)) {

		foreach ($cart->item as $k => $data) {

			if ($data['min_ea'] < $data['sales_unit']) {
				$data['min_ea'] = $data['sales_unit'];
			}
?>
	<tr><td height="4" colspan="7"></td></tr>
	<tr height="25" bgcolor="#FFFFFF" align="center">
<input type="hidden" name="goodsListKey[]" id="goodsListKey_<?=$k?>" value="<?=$key[$k]?>" />
<input type="hidden" name="modGoodsInfo[]" id="modGoodsInfo_<?=$k?>" value="<?=$key[$k]?>" />
		<td><input type="checkbox" name="selectGood[]" value="<?=$k?>" style="border:0px" /></td>
		<td><font class="ver8" color="#616161"><?=$imax - $k?></font></td>
		<td align="left">
			<table cellpadding="3" cellspacing="0" border="0" style="width:100%; margin:5px;">
				<tr>
					<td rowspan="2" align="center" valign="middle" style="width:60px; padding-left:0px; padding-right:5px;"><a href="../../goods/goods_view.php?goodsno=<?=$data['goodsno']?>" target="_blank"><?=goodsimg($data['img'], 50, 'style="border:1px #e9e9e9 solid;"', 1)?></a></td>
					<td style="border-bottom:#BDBDBD 1px dotted;"><?=$data['goodsnm']?></td>
				</tr>
				<tr>
					<td style="padding:5px; font-size:11px; font-family:dotum;">
					<? if ($data['opt']) { ?>
						<span style="color:#595959;">���ÿɼ� : <?=implode('/', $data['opt'])?></span><br />
					<? } ?>

					<? if ($data['select_addopt']) { foreach($data['select_addopt'] as $_opt) {?>
						<span style="color:#A6A6A6;">�߰��ɼ� : <?=$_opt['optnm']?>:<?=$_opt['opt']?></span><br />
					<? }} ?>

					<? if ($data['input_addopt']) { foreach($data['input_addopt'] as $_opt) {?>
						<span style="color:#A6A6A6;">�Է¿ɼ� : <?=$_opt['optnm']?>:<?=$_opt['opt']?></span><br />
					<? }} ?>
					</td>
				</tr>
				<tr>
					<td></td>
					<td><a href="javascript:editOption('<?=$k?>');"><img src="../img/btn_check_modify.gif" /></a></td>
				</tr>
			</table>
		</td>
		<td><?=number_format($data['reserve'])?> ��</td>
		<td>
			<table cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td rowspan="2"><input type="text" name="stock[]" id="stock_<?=$k?>" size="4" value="<?=$data['ea']?>" step="<?=$data['sales_unit'] ? $data['sales_unit'] : '1' ?>" min="<?=$data['min_ea'] ? $data['min_ea'] : '1' ?>" max="<?=$data['max_ea'] ? $data['max_ea'] : '0' ?>" onblur="chg_cart_ea(this, 'set');" onkeydown="onlynumber()"></td>
					<td><a href="javascript:;" onClick="chg_cart_ea(fmList['stock[]'],'up',<?=$k?>)"><img src="../img/btn_plus.gif" /></a></td>
					<td rowspan="2"><a href="javascript:modifyEa()"><img src="../img/sbtn_mod.gif" /></a></td>
				</tr>
				<tr>
					<td><a href="javascript:;" onClick="chg_cart_ea(fmList['stock[]'],'dn',<?=$k?>)"><img src="../img/btn_minus.gif" /></a></td>
				</tr>
			</table>
		</td>
		<td><?=number_format($data['price'] + $data['addprice'])?> ��</td>
		<td><?=number_format(($data['price'] + $data['addprice']) * $data['ea'])?> ��</td>
	</tr>
	<tr><td height="4"></td></tr>
	<tr><td colspan="7" class="rndline"></td></tr>
<?
		}
	}
	else {
?>
	<tr><td colspan="7" align="center" valign="middle" height="100">�ֹ��Ͻ� ��ǰ�� ����/�߰��� �ּ���.</td></tr>
	<tr><td colspan="7" class="rndline"></td></tr>
<?
	}
?>
</table>

<?
	if($i > 0) {
?>
<div class="goodsTotalBox">
	��ǰ �հ� �ݾ� : <span class="goodsTotalPrice"><?=number_format($cart->goodsprice)?> ��</span><br />
	������ ������ : <span class="goodsTotalPrice"><?=number_format($cart->bonus)?> ��</span>
</div>
<?
	}
?>
<div class="goodsBottomButton"><img src="../img/btn_alldelet_s.gif" alt="���û���" align='absmiddle' style="cursor:pointer;" onclick="javascript:<?=($i > 0) ? "act_delete();" : "alert( '����Ÿ�� �������� �ʽ��ϴ�.' );";?>"></div>
<input type="hidden" name="originalPrice" id="originalPrice" value="<?=$cart->goodsprice?>" />
</form>
</div>


<script type="text/javascript">
var uid = '<?=$_SESSION['uid']?>';

Event.observe(document, 'dom:loaded', function(){
	if (uid == '') {
		parent.location.reload();
	}
});
</script>