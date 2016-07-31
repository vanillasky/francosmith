	<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<col width="35"><col width="40"><col width="100"><col width="70"><col width="100"><col width="140"><col><col width="70"><col width="70"><col width="50"><col width="55">
	<col width="70"><col width="70"><col width="100"><col width="100">
	<tr><td class="rnd" colspan="20"></td></tr>
	<tr class="rndbg">
		<th><a href="javascript:void(0)" onClick="chkBoxAll()" class=white>선택</a></th>
		<th>번호</th>
		<th>주문일시</th>
		<th>발송기한</th>
		<th>발송지연사유</th>
		<th colspan="2">주문번호 (주문상품)</th>
		<th>주문자</th>
		<th>받는분</th>
		<th>결제</th>
		<th>금액</th>
		<th>배송일</th>
		<th>배송방법</th>
		<th>택배사</th>
		<th>송장번호</th>
	</tr>
	<tr><td class="rnd" colspan="20"></td></tr>
	<?
	$idx = 0;
	while ($row = $db->fetch($rs,1)) {
		$idx++;
		$view_url = 'checkout.view.php?OrderID='.$row['OrderID'].'&ProductOrderIDList='.$row['ProductOrderIDList'];
	?>
	<tr height="25" bgcolor="#ffffff" bg="#ffffff" align="center">
		<td class="noline">
			<input type="checkbox" name="OrderID[<?=$idx?>]" value="<?=$row['OrderID']?>" class="el-OrderID {ProductOrderStatus:'<?=$row['ProductOrderStatus']?>',ClaimType:'<?=$row['ClaimType']?>',ClaimStatus:'<?=$row['ClaimStatus']?>',PlaceOrderStatus:'<?=$row['PlaceOrderStatus']?>'}" onclick="iciSelect(this)"/>
			<input type="hidden" name="ProductOrderIDList[<?=$idx?>]" value="<?=$row['ProductOrderIDList']?>" />
		</td>
		<td><span class="ver8" style="color:#616161"><?=$pg->idx--?></span></td>
		<td><span class="ver81" style="color:#616161"><?=substr($row['OrderDate'],0,-3)?></span></td>
		<td><span class="ver81" style="color:#616161"><?=substr($row['ShippingDueDate'],0,-9)?></span></td>
		<td><span class="small1" style="color:#616161"><?=$checkout_message_schema['delayedDispatchReasonType'][$row['DelayedDispatchReason']]?></span></td>
		<td align="left">
			<a href="<?=$view_url?>"><span class="ver81" style="color:#0074BA"><b><?=$row['OrderID']?></b></span></a>
			<a href="javascript:popup('<?=$view_url?>&win=1',800,600)"><img src="../img/btn_newwindow.gif" border=0 align="absmiddle"/></a>
		</td>
		<td align="left">
			<div style="height:13px; overflow-y:hidden;">
			<span class="small1" style="color:#444444"><?=$row['OrderCount'] > 1 ? $row['ProductName'].' 외 '.($row['OrderCount'] - 1).'건' : $row['ProductName'] ?></span>
			</div>
		</td>
		<td>
			<? if ($row['m_id']) { ?><span id="navig" name="navig" m_id="<?=$row['m_id']?>" m_no="<?=$row['m_no']?>"><? } ?>
			<span class="small1" style="color:#0074BA">
			<b><?=$row['OrdererName']?></b>
			</span>
			<? if ($row['m_id']) { ?> (<?=$row['m_id']?>)</span><? } ?>
		</td>
		<td><span class="small1" style="color:#444444;"><?=$row['ShippingAddressName']?></span></td>
		<td class="small4"><?=$checkout_message_schema['payMeansClassType'][$row['PaymentMeans']]?></td>
		<td class="ver81"><b><?=number_format($row['calculated_payAmount'])?></b></td>
		<td><input type="text" name="DispatchDate[<?=$idx?>]" value="" onclick="calendar(event)" readonly style="width:95%"></td>
		<td>
			<select name="DeliveryMethodCode[<?=$idx?>]" style="width:95%;" class="small-selectbox">
			<option value="">(선택)</option>
			<? foreach ($checkout_message_schema['deliveryMethodType'] as $code => $name) { ?>
			<? if (strpos($code,'RETURN_') === 0 || $code == 'NOTHING') continue;?>
			<option value="<?=$code?>"><?=$name?></option>
			<? } ?>
			</select>
		</td>
		<td>
			<select name="DeliveryCompanyCode[<?=$idx?>]" style="width:95%;" class="small-selectbox">
			<option value="">(선택)</option>
			<? foreach ($checkout_message_schema['selectDeliveryCompanyType'] as $code => $name) { ?>
			<option value="<?=$code?>"><?=$name?></option>
			<? } ?>
			</select>
		</td>
		<td><input type="text" name="TrackingNumber[<?=$idx?>]" value="" style="width:95%"></td>
	</tr>
	<tr><td colspan="20" bgcolor="#E4E4E4"></td></tr>
	<? } ?>
	</table>