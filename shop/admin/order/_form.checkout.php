<?php
// 체크아웃 4.0 설정
	$checkout_message_schema = include "./_cfg.checkout.php";

// 주문번호, 상품주문번호
	$OrderID			= $_GET['OrderID'];

// 3.0 주문인지 체크
	list($_3_cnt) = $db->fetch("SELECT count(orderNo) FROM gd_navercheckout_order WHERE ORDER_OrderID = '$OrderID'");
	if ($_3_cnt > 0) {
		msg('네이버 체크아웃 3.0 주문건은 마이그레이이션 후 가능합니다.',-1);
		exit;
	}

// 주문정보
	$query = "
		SELECT

			O.*, PO.*,

			G.img_s,

			MB.m_id,MB.m_no,

			D.DeliveryStatus,
			D.DeliveryMethod,
			D.DeliveryCompany,
			D.TrackingNumber,
			D.SendDate,
			D.PickupDate,
			D.DeliveredDate,
			D.IsWrongTrackingNumber,
			D.WrongTrackingNumberRegisteredDate,
			D.WrongTrackingNumberType,

			false AS claimInfo

		FROM ".GD_NAVERCHECKOUT_ORDERINFO."						AS O
		INNER JOIN ".GD_NAVERCHECKOUT_PRODUCTORDERINFO."		AS PO	ON PO.OrderID = O.OrderID
		LEFT JOIN ".GD_GOODS."								AS G ON PO.ProductID = G.goodsno
		LEFT JOIN ".GD_MEMBER."								AS MB	ON PO.MallMemberID=MB.m_id
		LEFT JOIN ".GD_NAVERCHECKOUT_DELIVERYINFO."				AS D	ON PO.ProductOrderID = D.ProductOrderID

		WHERE
			O.OrderID = '$OrderID'
		ORDER BY PO.PackageNumber
	";
	$rs = $db->query($query);

	// 총 결제 금액, 상품별 결제금액, 할인액 계산, 주문정보, 부분배송 등등 체크 및 배열화
	// 클레임 정보가 있다면 가져옴 (매번 join 하지 않음)
		$orderInfo = array();
		$partial = array();
		$has = array();
		$rowspan = array();
		$_rowspan = array();
		$orderStatus = array();
		$refundAmount = array();
		$deliveryInfo = array();

		while ($row = $db->fetch($rs,1)) {


			$row['calculated_payAmount'] = $row['TotalPaymentAmount'];
			$row['calculated_ordAmount'] = $row['TotalProductAmount'];

			// 부분발송 여부 체크
			$_tmp = array($row['DeliveryMethod'],$row['DeliveryCompany'],$row['TrackingNumber']);
			if (!in_array($_tmp,(array)$partial['delivery'])) $partial['delivery'][] = $_tmp;

			// 클레임 정보
			switch($row['ClaimType']) {
				case 'RETURN':
					$claim_table = GD_NAVERCHECKOUT_RETURNINFO;
					break;
				case 'CANCEL':
				case 'ADMIN_CANCEL':
					$claim_table = GD_NAVERCHECKOUT_CANCELINFO;
					break;
				case 'EXCHANGE':
					$claim_table = GD_NAVERCHECKOUT_EXCHANGEINFO;
					break;
				case 'PURCHASE_DECISION_HOLDBACK':
					$claim_table = GD_NAVERCHECKOUT_DECISIONHOLDBACKINFO;
					break;
				default :
					$claim_table = "";
					break;
			}

			if ($claim_table) {
				// 부분클레임(취소/반품/교환) 여부 체크
				$_tmp = array($row['ClaimType'],$row['ClaimStatus']);
				if (!in_array($_tmp,(array)$partial['claim'])) $partial['claim'][] = $_tmp;

				$row['claimInfo'] = $db->fetch("SELECT * FROM ".$claim_table." WHERE ProductOrderID = '".$row['ProductOrderID']."'",1);
				$has['claim'] = true;

				if ($row['claimInfo']['RefundStandbyStatus'] == '환불처리완료') $refundAmount[$row['ProductOrderID']] = $row['calculated_payAmount'];

			}

			$_delivery = array(
				'DeliveryMethod' => $row['DeliveryMethod'],
				'DeliveryStatus' => $row['DeliveryStatus'],
				'DeliveryCompany' => $row['DeliveryCompany'],
				'TrackingNumber' => $row['TrackingNumber'],
				'SendDate' => $row['SendDate'],
				'PickupDate' => $row['PickupDate'],
				'DeliveredDate' => $row['DeliveredDate']
			);

			if (!empty($_delivery['DeliveryMethod']) && !in_array($_delivery,$deliveryInfo)) {
				$deliveryInfo[] = $_delivery;
			}

			// 처리상태
			$orderStatus[] = getCheckoutOrderStatus($row,true);

			$orderInfo[] = $row;

			$_rowspan[$row['PackageNumber']]++;

		}

		// 묶음 배송 코드별 합침
		foreach($_rowspan as $k => $v) {
			$rowspan[] = $v;
			if ($v-1 > 0) $rowspan = array_pad ( $rowspan, sizeof($rowspan) + $v - 1, 0);
		}

		foreach($partial as $k => $v) $partial[$k] = (sizeof($v) > 1) ? true : false;
		unset($_tmp);

		// 지급 적립금(or 지금될 적립금)
		$query = "
			SELECT
				SUM(eNamooEmoney)
			FROM ".GD_NAVERCHECKOUT_PRODUCTORDERINFO."
			WHERE OrderID = '$OrderID'
			";
		list($totalEmoney) = $db->fetch($query);

// 주문 정보
$Order = $orderInfo[0];
$orderStatus = array_unique($orderStatus);
?>
<style>
.title2 {
	font-weight:bold;
	padding-bottom:5px;
}
</style>

<script type="text/javascript" src="./checkout.js"></script>
<script type="text/javascript">
function fnDeliveryTrace(channel, code, dlvno) {
	var url = './popup.delivery.php?channel='+channel+'&code='+code+'&dlvno='+dlvno;
	popup(url,800,500);
}
</script>

<div class="title title_top">주문상세</div>

<!-- 주문 상품 -->
<form name="frmNaverCheckout" method="post" target="processLayerForm">
	<input type="hidden" name="mode" value="">

	<table class=tb cellpadding=4 cellspacing=0>
	<col width="35">
	<col width="120">
	<col width="40">
	<col>
	<col width="50">
	<col width="50">
	<col width="50">
	<col width="50">
	<col width="50">
	<col width="60">
	<? if ($partial['delivery']) { ?>
	<col width="100">
	<? } ?>

	<tr height=25 bgcolor=#2E2B29 class="small"4 style="padding-top:8px">
		<th><font color="white"><a href="javascript:void(0)" onClick="chkBoxAll();fnSetAvailableOperationButton();" class=white>선택</a></font></th>
		<th><font color="white">상품주문번호</font></th>
		<th colspan=2><font color="white">상품명</th>
		<th><font color="white">수량</font></th>
		<th><font color="white">상품가격</font></th>
		<th><font color="white">할인</font></th>
		<th><font color="white">결제</font></th>
		<th><font color="white">배송비</font></th>
		<th><font color="white">처리상태</font></th>
		<? if ($partial['delivery']) { ?>
		<th><font color="white">택배사/송장번호</font></th>
		<? } ?>
	</tr>
	<col align=center span=3><col>
	<col align=center span=10>
	<?
	$idx = 0;

	foreach($orderInfo as $p_order) {
		$idx++;
		$disabled = in_array($p_order['ProductOrderStatus'],array('CANCELED','RETURNED','CANCELED_BY_NOPAYMENT','EXCHANGED','PURCHASE_DECIDED')) ? 'disabled' : '';
	?>
	<tr bgcolor="<?=$bgcolor?>" bg="<?=$bgcolor?>" align="center">
		<td class="noline">
		<input type="checkbox" name="OrderID[<?=$idx?>]" value="<?=$p_order['OrderID']?>" class="el-OrderID {ProductOrderStatus:'<?=$p_order['ProductOrderStatus']?>',ClaimType:'<?=$p_order['ClaimType']?>',ClaimStatus:'<?=$p_order['ClaimStatus']?>',PlaceOrderStatus:'<?=$p_order['PlaceOrderStatus']?>',HoldbackStatus:'<?=$p_order['claimInfo']['HoldbackStatus']?>',HoldbackReason:'<?=$p_order['claimInfo']['HoldbackReason']?>'}" onclick="iciSelect(this);fnSetAvailableOperationButton();" <?=$disabled?> />
		<input type="hidden" name="ProductOrderIDList[<?=$idx?>]" value="<?=$p_order['ProductOrderID']?>" />
		</td>
		<td><font class=ver8 color="#444444"><?=$p_order['ProductOrderID']?></td>
		<td><a href="../../goods/goods_view.php?goodsno=<?=$p_order['ProductID']?>" target=_blank><?=goodsimg($p_order['img_s'],30,"style='border:1 solid #cccccc'",1)?></a></td>
		<td align="left">
			<a href="javascript:popup('../goods/popup.register.php?mode=modify&goodsno=<?=$p_order['ProductID']?>',825,600)">
			<?=$p_order['ProductName']?>
			<?=($p_order['ProductOption']) ? '['.$p_order['ProductOption'].']' : '' ?>
			</a>

			<div style="margin-top:3px;">
				<table class="tb">
				<col class="cellC"><col class="cellL">
				<? if ($p_order['ShippingMemo']) { ?>
				<tr>
					<td class="small">배송 메모</td>
					<td style="padding:3px 10px" class="small"><?=nl2br($p_order['ShippingMemo'])?></td>
				</tr>
				<? }
				if ($p_order['SellingCode']) { ?>
				<tr>
					<td class="small">매출 코드</td>
					<td style="padding:3px 10px"><?=$p_order['SellingCode']?></td>
				</tr>
				<? }
				if ($p_order['MallManageCode']) { ?>
				<tr>
					<td class="small">몰 관리 코드</td>
					<td style="padding:3px 10px"><?=$p_order['MallManageCode']?></td>
				</tr>
				<? }
				if ($p_order['OrderExtraData']) {
					$_OrderExtraDatas = explode('///',$p_order['OrderExtraData']);
					foreach($_OrderExtraDatas as $_OrderExtraData) {
					if (!$_OrderExtraData) continue;
					$_OrderExtraData = explode(':::',$_OrderExtraData);
				?>
				<tr>
					<td class="small"><?=$_OrderExtraData[0]?></td>
					<td style="padding:3px 10px"><?=$_OrderExtraData[1]?></td>
				</tr>
				<? } } ?>
				</table>
			</div>
		</td>
		<td><?=number_format($p_order['Quantity'])?></td>
		<td><?=number_format($p_order['UnitPrice'])?></td>
		<td><?=number_format($p_order['ProductDiscountAmount'])?></td>
		<td><?=number_format($p_order['calculated_payAmount'])?></td>
		<? if ($rowspan[$idx - 1] > 0) { ?>
		<td class="small" rowspan="<?=$rowspan[$idx - 1]?>"><?=$p_order['ShippingFeeType']?><?=$p_order['ShippingFeeType'] != '무료' ? '<br>('.number_format($p_order['DeliveryFeeAmount']).')' : '' ?></td>
		<? } ?>
		<td class="small4"><?=getCheckoutOrderStatus($p_order)?></td>
		<? if ($partial['delivery']) { ?>
		<td>
			<? if ($p_order['DeliveryMethod'] == 'DELIVERY') { ?>
			<div nowrap class="small" color="#555555">
			<?=$checkout_message_schema['deliveryCompanyType'][$p_order['DeliveryCompany']]?>
			</div>
			<div nowrap class="small" color="#555555">
			<?=$p_order['TrackingNumber']?>
			</div>
			<? } else { ?>
			<?=$checkout_message_schema['deliveryMethodType'][$p_order['DeliveryMethod']]?>
			<? } ?>
		</td>
		<? } ?>
	</tr>
	<? } ?>
	</table>

	<div style="margin:5px 0 15px 0;_border:1px solid #fff">

		<div class="fl">

			<button id="el-fnPlaceProductOrder" disabled class="default-btn-off el-product-order" onClick="fnPlaceProductOrder();return false;">발주확인</button>
			<button id="el-fnDelayProductOrder" disabled class="default-btn-off el-product-order" onClick="fnDelayProductOrder();return false;">발송지연</button>
			<button id="el-fnShipProductOrder" disabled class="default-btn-off el-product-order" onClick="fnShipProductOrder();return false;">발송처리</button>

			<button id="el-fnCancelSale" disabled class="default-btn-off el-product-order" onClick="fnCancelSale();return false;">판매취소</button>

			<button id="el-fnApproveCancelApplication" disabled class="default-btn-off el-product-order" onClick="fnApproveCancelApplication();return false;">취소요청승인</button>

			<button id="el-fnRequestReturn" disabled class="default-btn-off el-product-order" onClick="fnRequestReturn();return false;">반품신청</button>
			<button id="el-fnApproveReturnApplication" disabled class="default-btn-off el-product-order" onClick="fnApproveReturnApplication();return false;">반품요청승인</button>

			<button id="el-fnApproveExchangeApplication" disabled class="default-btn-off el-product-order" onClick="fnApproveExchangeApplication();return false;">교환요청승인</button>
			<button id="el-fnApproveCollectedExchange" disabled class="default-btn-off el-product-order" onClick="fnApproveCollectedExchange();return false;">교환수거완료</button>
			<button id="el-fnReDeliveryExchange" disabled class="default-btn-off el-product-order" onClick="fnReDeliveryExchange();return false;">교환재배송</button>

		</div>

		<div class="cb"></div>

	</div>

</form>

<!-- 주문정보 -->

	<div class=title2>&nbsp;<img src="../img/icon_process.gif" align="absmiddle"><font color="#508900">현주문상태</font></div>

	<table class="tb">
	<col class="cellC"><col class="cellL">
	<tr>
		<td>주문상태</td>
		<td style="padding:2px 10px">
		<?=(sizeof($orderStatus) === 1) ? array_shift($orderStatus) : '부분처리 된 주문건입니다. 상단 리스트의 처리상태를 확인하세요.' ?>
	</tr>
	</table><p>

<!-- 결제정보 -->
	<div class=title2>&nbsp;<img src="../img/icon_process.gif" align="absmiddle"><font color="#508900">결제금액정보</font></div>
	<table class="tb">
	<col class="cellC"><col class="cellL">
	<tr>
		<td>주문금액</td>
		<td width=110 align=right><font class=ver8><?=number_format(gd_array_sum($orderInfo, 'calculated_ordAmount'))?></font>원</td>
		<td></td>
	</tr>
	<tr>
		<td>배송비</td>
		<td width=110 align=right><font class=ver8><?=number_format($Order['DeliveryFeeAmount'])?></font>원</td>
		<td>
			<img src="../img/arrow_gray.gif" align="absmiddle">
			<font class="small" color="#444444"><?=$Order['ShippingFeeType']?></font>
		</td>
	</tr>
	<tr>
		<td>할인액</td>
		<?
		$_dc_per_product = gd_array_sum($orderInfo, 'ProductDiscountAmount');
		$_dc_per_order = $Order['OrderDiscountAmount'];
		?>
		<td align=right><font class=ver8>- <?=number_format($_dc_per_product + $_dc_per_order)?></font>원</td>
		<td>
			<img src="../img/arrow_gray.gif" align="absmiddle">
			<font class="small" color="#444444">
			상품별 할인 (<font color=0074BA class=ver81><?=number_format($_dc_per_product)?></font>원)
			+ 주문 할인 (<font color=0074BA class=ver81><?=number_format($_dc_per_order)?></font>원)
			</font>

		</td>
	</tr>
	<? if (sizeof($refundAmount) > 0) { ?>
	<tr>
		<td>환불금액</td>
		<td align=right><font class=ver8>- <?=number_format( array_sum($refundAmount) )?></font>원</td>
		<td>
			<img src="../img/arrow_gray.gif" align="absmiddle">
			<font class="small" color="#444444">
			상품주문번호 (<?=implode(', ',array_keys($refundAmount))?>)
			</font>

		</td>
	</tr>
	<? } ?>
	<tr>
		<td>결제금액</td>
		<td align=right><font color=0074BA class=ver8><b><?=number_format( $Order['GeneralPaymentAmount'] + $Order['NaverMileagePaymentAmount'] + $Order['ChargeAmountPaymentAmount'] + $Order['CheckoutAccumulationPaymentAmount'] );?></b></font>원</td>
		<td>
			<img src="../img/arrow_gray.gif" align="absmiddle">
			<font class="small" color="#444444">
			일반결제 (<font color=0074BA class=ver81><?=number_format($Order['GeneralPaymentAmount'])?></font>원)
			<? if ($Order['NaverMileagePaymentAmount'] > 0) { ?>+ 네이버 마일리지 (<font color=0074BA class=ver81><?=number_format($Order['NaverMileagePaymentAmount'])?></font>원)<? } ?>
			<? if ($Order['ChargeAmountPaymentAmount'] > 0) { ?>+ 네이버 충전금 (<font color=0074BA class=ver81><?=number_format($Order['ChargeAmountPaymentAmount'])?></font>원)<? } ?>
			<? if ($Order['CheckoutAccumulationPaymentAmount'] > 0) { ?>+ 체크아웃 적립금 (<font color=0074BA class=ver81><?=number_format($Order['CheckoutAccumulationPaymentAmount'])?></font>원)<? } ?>
			</font>
		</td>
	</tr>
	<tr>
		<td>적립될 금액</td>
		<td align=right><font class=ver8><?=number_format($totalEmoney);?></font>원</td>
		<td></td>
	</tr>
	</table><p>

<table width=100% cellpadding=0 cellspacing=0>
	<col span=3 valign=top>
	<tr>
		<td width=50%>

		<!-- 주문자정보 -->
		<div class=title2>&nbsp;<img src="../img/icon_process.gif" align="absmiddle"><font color="#508900">주문자정보</font></div>
		<table class="tb">
		<col class="cellC"><col class="cellL">
		<tr>
			<td>이름/ID</td>
			<td><? if ($Order[m_id]) { ?><span id="navig" name="navig" m_id="<?=$Order[m_id]?>" m_no="<?=$Order[m_no]?>" popup="<?=$popup?>"><? } ?><font color=0074BA><b>
			<?=$Order[OrdererName]?>
			<? if ($Order[m_id]){ ?>/ <?=$Order[m_id]?></b></font></span>
			<? } ?>
			</td>
		</tr>
		<tr>
			<td>연락처</td>
			<td class=ver8><?=$Order[OrdererTel1]?> / <?=$Order[OrdererTel2]?></td>
		</tr>
		<tr>
			<td>주문일</td>
			<td><font class=ver8><?=$Order[OrderDate]?></td>
		</tr>
		</table>

		</td>
		<td width=10 nowrap></td>
		<td width=50%>

		<!-- 수령자정보 -->
		<div class=title2>&nbsp;<img src="../img/icon_process.gif" align="absmiddle"><font color="#508900">수령자정보</font></div>
		<table class="tb">
		<col class="cellC"><col class="cellL">
		<tr>
			<td>수령자</td>
			<td><?=$Order[ShippingAddressName]?></td>
		</tr>
		<tr>
			<td>연락처</td>
			<td><?=$Order[ShippingAddressTel1]?> / <?=$Order[ShippingAddressTel2]?></td>
		</tr>
		<tr>
			<td>주소</td>
			<td>
				<font color="#444444"><?=$Order[ShippingAddressZipCode]?></font><br>
				<?=$Order['ShippingAddressBaseAddress']?> <?=$Order['ShippingAddressDetailedAddress']?>
			</td>
		</tr>
		</table>

		</td>
	</tr>
	</tr><tr><td height=15></td></tr>
	<tr>
		<td width=50%>

		<!-- 결제정보 -->
		<div class=title2>&nbsp;<img src="../img/icon_process.gif" align=absmiddle><font color=508900>결제정보</div>
		<table class=tb>
		<col class=cellC><col class=cellL>
		<tr>
			<td>결제 일시(최종 결제)</td>
			<td><?=$Order['PaymentDate']?></td>
		</tr>
		<tr>
			<td>결제 수단</td>
			<td><?=$checkout_message_schema['payMeansClassType'][$Order['PaymentMeans']]?></td>
		</tr>
		<tr>
			<td>입금 기한</td>
			<td><?=$Order['PaymentDueDate']?></td>
		</tr>
		<tr>
			<td>결제 번호</td>
			<td><?=$Order['PaymentNumber']?></td>
		</tr>
		</table>

		</td>
		<td width=10 nowrap></td>
		<td width=50%>

		<!-- 배송정보 -->
		<div class=title2>&nbsp;<img src="../img/icon_process.gif" align="absmiddle"><font color="#508900">배송정보</font></div>

		<? foreach($deliveryInfo as $_delivery) { ?>

		<table class="tb" style="margin-bottom:15px;">
		<col class="cellC"><col class="cellL">
		<tr>
			<td>배송방법</td>
			<td><?=$checkout_message_schema['deliveryMethodType'][$_delivery['DeliveryMethod']]?></td>
		</tr>
		<tr>
			<td>배송상태</td>
			<td><?=$_delivery['DeliveryStatus']?></td>
		</tr>

		<tr>
			<td>송장번호</td>
			<td>
				<?=$checkout_message_schema['deliveryCompanyType'][$_delivery['DeliveryCompany']]?> / <?=$_delivery['TrackingNumber']?>
				<a href="javascript:void(0);" onClick="fnDeliveryTrace('checkout','<?=$_delivery['DeliveryCompany']?>','<?=$_delivery['TrackingNumber']?>');"><img src="../img/btn_delifind.gif" border=0></a>
			</td>
		</tr>
		<tr>
			<td>발송 일시</td>
			<td><font class=ver8><?=$_delivery[SendDate]?></td>
		</tr>
		<tr>
			<td>집화 일시</td>
			<td><font class=ver8><?=$_delivery[PickupDate]?></td>
		</tr>
		<tr>
			<td>배송 완료 일시</td>
			<td><font class=ver8><?=$_delivery[DeliveredDate]?></td>
		</tr>
		</table>

		<? } ?>

		</td>
	</tr>
	</tr><tr><td height=15></td></tr>
	<tr>
		<td colspan="3">
		<?
		if ($has['claim']) {
			$claimInfo = array();

			foreach($orderInfo as $_Order) {

				if (empty($_Order['claimInfo'])) continue;

				// 추가 데이터 설정
				$_Order['claimInfo']['ClaimType'] = $_Order['ClaimType'];

				$claimInfo[] = $_Order['claimInfo'];
				if ($partial['claim'] == false) break;
			}
		?>
		<!-- 클레임(취소/반품/교환) -->
		<div class=title2>&nbsp;<img src="../img/icon_process.gif" align=absmiddle><font color=508900>클레임(취소/반품/교환) 정보</div>

		<?
			foreach ($claimInfo as $claim) {

				$schema = $checkout_message_schema['claim'.$claim['ClaimType']];

		?>
				<table class=tb style="margin-bottom:15px;">
				<col class=cellC><col class=cellL>
				<tr>
					<td>상품주문번호</td>
					<td><?=$claim['ProductOrderID']?></td>
				</tr>
				<? foreach ($schema as $field => $name) { ?>
				<?
					$_field = is_array($name) ? $name['name'] : $name;
					$_value = is_array($name) ? $checkout_message_schema[$name['schema']][$claim[$field]] : $claim[$field];

					if (!$_value) continue;
				?>
				<tr>
					<td><?=$_field?></td>
					<td><?=nl2br($_value)?></td>
				</tr>
				<? } ?>
				</table>
		<?
			}

		}
		?>
		</td>

	</tr>
</table>