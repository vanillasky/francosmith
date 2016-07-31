<?php
/*********************************************************
* 파일명     :  paycoPartCancelVbank.php
* 프로그램명 :  페이코 결제 부분취소(가상계좌/계좌이체)
**********************************************************/
include "../lib.php";
include "../../conf/config.php";

$charge = true;// true 포인트 후차감 / false 포인트 선차감
$get_ordno = $_GET['ordno'];
$get_sno = $_GET['sno'];
$get_repayfee = $_GET['repayfee'];
$get_repay = $_GET['repay'];
$get_part = $_GET['part'];
$get_remoney = $_GET['remoney'];
if($get_remoney == '') $get_remoney = 0;
$set_data['settlekind']['v'] = '가상계좌';
$set_data['settlekind']['o'] = '계좌이체';

if($get_ordno) {
	if($get_sno) {
		//환불완료 후 추가확인 메세지
		$repay_add_msg = '';
		if($get_remoney > 0) $arr_repay_add_msg[] = '\n\n※환불완료 처리시 결제시 사용한 적립금 중 '.number_format($get_remoney).'원을 되돌려줍니다.';
		if(isset($arr_repay_add_msg)) $repay_add_msg = implode('\n\n',$arr_repay_add_msg);
		
		$orderDeliveryItem = &load_class('orderDeliveryItem','orderDeliveryItem', $get_ordno);
		$payco = &load_class('payco','payco');

		if($payco->checkCancelYn($get_sno) === true) {
			msg('이미 페이코 결제취소가 완료된 주문입니다.', -1);
			echo('<script>location.reload();</script>');
			exit;
		}

		$cancel_delivery = $orderDeliveryItem->cancel_delivery($get_sno);

		$now_cancel_price = $cancel_delivery['total_cancel_price'] + $cancel_delivery['total_cancel_delivery_price'];//현재취소건 취소금액(환불수수료 제외)

		if($orderDeliveryItem->checkLastCancel($get_sno) === true) {
			//마지막 취소건인 경우 정액쿠폰, 적립금 사용액 차감
			$now_cancel_price -= ($cancel_delivery['coupon']['m'] - $cancel_delivery['coupon']['f']) + $cancel_delivery['emoney'];
		}

		//주문금액 정보조회
		$arr_coulms[] = 'payco_use_point';
		$arr_coulms[] = '(payco_use_point - payco_use_point_repay) as prn_payco_use_point';
		$arr_coulms[] = 'payco_settle_type';
		$arr_coulms[] = 'm_no';
		$arr_coulms[] = 'settlekind';
		$arr_order_data = $payco->getOrderData($get_ordno, 'gd_order', 'fetch_true', $arr_coulms);
		unset($arr_coulms);

		//주문에서 취소된 배송비를 조회한다
		$cancel_data = $orderDeliveryItem->get_cancel_delivery($get_ordno);

		/*
		 * $order->getRealPrnSettleAmount()에는 차감된 배송비가 포함되어 있지 않아 취소된 배송비 확인 후 실결제금액 계산(환불수수료는 차감되어 있다)
		*/
		$order = new order();
		$order->load($get_ordno);
		$real_settle_price = $order->getCancelCompletedRealSettleAmount();//이나무 실제남은 금액

		$cash_bool = false;
		$point_bool = false;

		if($arr_order_data['payco_use_point'] > 0) {
			//주문시 페이코 포인트를 사용한 경우
			if($arr_order_data['payco_use_point'] > ($real_settle_price - $now_cancel_price)) {
				//실 결제금액이 현재 취소건을 취소했을때 주문시 사용한 페이코 포인트보다 작은 금액인 경우 페이코 포인트 환불

				if($real_settle_price > $arr_order_data['payco_use_point']) {
					//현금결제금액과 페이코 포인트를 같이 환불하는 경우
					$cash_bool = true;
					$point_bool = true;
				}
				else {
					//페이코 포인트만 환불하는 경우
					$point_bool = true;
				}
			}
			else {
				//현금결제금액 환불(포인트 사용한 주문)
				$cash_bool = true;
			}
		}
		else {
			//현금결제금액 환불(포인트 미사용 주문)
			$cash_bool = true;
		}

		$cancel_cash_price = 0;
		$payco_point = 0;
		$cancel_price = 0;

		$cash_delivery = 0;
		$point_delivery = 0;

		if($cash_bool === true && $point_bool === false) {

			//현금결제금액 환불
			$cancel_cash_price = $now_cancel_price;//취소예정금액[현금]
			$cash_delivery = $cancel_delivery['total_cancel_delivery_price'];//배송비
			$cancel_price = $cancel_cash_price - $get_repayfee;//총취소예정금액

			$msg['alert'] = '취소정보가 페이코에 전송되었습니다.\n쇼핑몰 환불완료처리를 하시겠습니까?';

			$display['point'] = 'display:none;';
			$display['etc2'] = 'display:none;';
			$display['etc3'] = 'display:none;';

		}
		else if($cash_bool === true && $point_bool === true) {

			//현금결제금액과 페이코 포인트를 같이 환불
			$cancel_cash_price = $real_settle_price - $arr_order_data['payco_use_point'];//취소예정금액[현금]
			$payco_point = $now_cancel_price - $cancel_cash_price;//취소예정금액[페이코 포인트]
			$cancel_price = ($cancel_cash_price + $payco_point) - $get_repayfee;//총 취소예정금액

			// 배송비 계산
			if($cancel_cash_price > $cancel_delivery['total_cancel_price']) {//현금결제금액 > 취소상품금액
				$cash_delivery = $cancel_cash_price - $cancel_delivery['total_cancel_price'];
				if($cash_delivery != $cancel_delivery['total_cancel_price']) $point_delivery = $cancel_delivery['total_cancel_delivery_price'] - $cash_delivery;;
			}
			else {//현금결제금액 < 취소상품금액
				$point_delivery = $payco_point - ($cancel_delivery['total_cancel_price'] - $cancel_cash_price);
			}

			$msg['alert'] = '페이코 포인트 환불처리가 완료되었습니다.\n쇼핑몰 환불완료처리를 하시겠습니까?';

			$display['etc1'] = 'display:none;';
			$display['etc2'] = 'display:none;';
			$display['btn'] = 'display:none;';

		}
		else if($cash_bool === false && $point_bool === true) {

			//페이코 포인트만 환불
			$payco_point = $now_cancel_price;//취소예정금액[페이코 포인트]
			$cancel_price = ($cancel_cash_price + $payco_point) - $get_repayfee;//총 취소예정금액

			$point_delivery = $cancel_delivery['total_cancel_delivery_price'];

			$msg['alert'] = '페이코 포인트 환불처리가 완료되었습니다.\n쇼핑몰 환불완료처리를 하시겠습니까?';

			$display['cash'] = 'display:none;';
			$display['etc1'] = 'display:none;';
			$display['etc2'] = 'display:none;';
			$display['btn'] = 'display:none;';

		}

	}
	else {
		echo('<script>alert("잘못된 접근입니다.");parent.location.reload();</script>');
		exit;
	}

?>
<form id="repay" method="post" action="./indb.php">
	<input type="hidden" name="mode" value="repay">
	<input type="hidden" name="bankcode[]" value="">
	<input type="hidden" name="bankaccount[]" value="">
	<input type="hidden" name="bankuser[]" value="">
	<input type="hidden" name="m_no[]" value="<?=$arr_order_data['m_no']?>">
	<input type="hidden" name="sno[]" value="<?=$get_sno?>">
	<input type="hidden" name="ordno[]" value="<?=$get_ordno?>">
	<input type="hidden" name="repayfee[]" value=""><!--수수료-->
	<input type="hidden" name="repay[]" value=""><!--환불금액-->
	<input type="hidden" name="chk[]" value="0"><!--환불적립금-->
	<input type="hidden" name="remoney[]" value="<?=$get_remoney?>"><!--환불적립금-->
</form>

<input type="hidden" name="payco_settle_type" value="<?=$arr_order_data['payco_settle_type']?>">
<link rel="styleSheet" href="../style.css">
<script type="text/javascript" src="../common.js"></script>
<script type="text/javascript" src="../prototype.js"></script>
<form name="frmIni" onsubmit="return false;">
<input type="hidden" name="mode" value="" />
<input type="hidden" name="ordno" value="<?=$get_ordno?>" /> <!-- 주문번호 -->
<input type="hidden" name="sno" value="<?=$get_sno?>" /> <!-- 환불접수번호 -->
<input type="hidden" name="repay" value="<?=$get_repay?>" /> <!-- 환불접수된 금액 -->
<input type="hidden" name="part" value="<?=$get_part?>" /> <!-- 환불접수된 금액 -->
<input type="hidden" name="total_cancel_delivery_price" value="<?=$cancel_delivery['total_cancel_delivery_price']?>" /> <!-- 환불접수된 금액 -->
	<div class="subtitle">
		<div class="title title_top">페이코 <?=$set_data['settlekind'][$arr_order_data['settlekind']]?> 취소/환불<span></span></div>
		<?if(empty($display['cash']) && empty($display['point'])) {?><!--현금+포인트 환불-->
		<div style="margin-top:-10px;padding-bottom:8px;padding-left:20px;"><font color="#003399">※ 페이코 <?=$set_data['settlekind'][$arr_order_data['settlekind']]?> 취소/환불시 페이코 포인트를 제외한 실입금액은 수기환불을 해야 합니다.</font></div>
		<?}?>
		<?if(empty($display['cash']) && empty($display['point']) === false) {?><!--현금환불-->
		<div style="margin-top:-10px;padding-bottom:8px;padding-left:20px;font-color:#003399;"><font color="#003399">※ 페이코 <?=$set_data['settlekind'][$arr_order_data['settlekind']]?> 취소/환불시 실입금액을 수기환불을 해야 합니다.</font></div>
		<?}?>
		<?if(empty($display['cash']) === false && empty($display['point'])) {?><!--포인트환불-->
		<div style="margin-top:-10px;padding-bottom:8px;padding-left:20px;font-color:#003399;"><font color="#003399">※ 페이코 <?=$set_data['settlekind'][$arr_order_data['settlekind']]?> 취소/환불시 페이코 포인트는 환불처리를 해야 합니다.</font></div>
		<?}?>
	</div>

	<div id="cancelFail" class="input_wrap"></div>

	<div id="cancelFrom" class="input_wrap">
		<?if($cancel_price > $real_settle_price) {?>
		<div align=center style="margin:3px;padding:5px;color:red;border:2px dotted red;">
			※중요! 총 환불금액이 총 결제금액을 초과하였습니다. 다시 한번 확인해 주세요. 
		</div>
		<?}?>

		<div id="paycoProcess"></div>

		<table class=tb>
			<col width="20%" class="cellC">
			<col width="15%" class="cellL">
			<col width="65%" class="cellL">
			<tr style="<?=$display['cash']?>">
				<th class="input_title r_space" align="left">취소예정금액<br>[실입금액]</th>
				<td class="input_area" align="right">
					<input type="hidden" name="total_cancel_price" value="<?=$cancel_cash_price?>" style="width:60px;" readonly /><?=$cancel_cash_price?> 원
				</td>
				<td class="input_area">
					<ul style="margin-left:-25px;">
						<?if($cash_delivery > 0) {?> <li>배송비(<?=$cash_delivery?>원)가 포함되어 있습니다.</li> <?}?>
						<li><font color="#980000">취소예정금액[실입금액]은 수기환불을 해야 합니다.</font></li>
					</ul>
				</td>
			</tr>

			<tr style="<?=$display['point']?>">
				<th class="input_title r_space" align="left">취소예정금액<br>[페이코 포인트]</th>
				<td class="input_area" align="right">
					<input type="hidden" name="repay_point" value="<?=$payco_point?>" style="width:60px;" readonly /><?=$payco_point?> 원
				</td>
				<td class="input_area">
					<ul style="margin-left:-25px;">
						<?if($point_delivery > 0) {?><li>배송비(<?=$point_delivery?>원)가 포함되어 있습니다.</li><?}?>
						<li><font color="#980000">페이코 포인트 환불처리가 필요 합니다.</font></li>
					</ul>
				</td>
			</tr>

			<tr>
				<th class="input_title r_space" align="left">환불 수수료</th>
				<td class="input_area" align="right">
					<input type="text" name="repayfee" value="<?=$get_repayfee?>" onblur="price_calculate();" style="width:60px;text-align:right;" class="input_text width_small" /> 원
				</td>
				<td class="input_area">
					<ul style="margin-left:-25px;">
					<li>환불시 발생되는 발송비용 및 기타 수수료 등을 정하실 수 있습니다.</li>
					<ul>
				</td>
			</tr>

			<tr>
				<th class="input_title r_space" align="left">총 취소예정금액</th>
				<td class="input_area" align="right">
					<span id="text_cancel_price"><?=$cancel_price?></span> 원
					<input type="hidden" name="cancel_price" style="width:60px;" value="<?=$cancel_price?>" />
				</td>
				<td class="input_area">
					<ul style="margin-left:-25px;">
						<li style="<?=$display['etc1']?>">총 취소예정금액 = 취소예정금액[실입금액] - 환불수수료</li>
						<li style="<?=$display['etc2']?>">총 취소예정금액 = 취소예정금액[페이코 포인트] - 환불수수료</li>
						<li style="<?=$display['etc3']?>">총 취소예정금액 = (취소예정금액[실입금액] + 취소예정금액[페이코 포인트]) - 환불수수료</li>
						<li>총 취소예정금액 만큼 페이코 포인트 적립이 제외됩니다.</li>
					<ul>
				</td>
			</tr>
		</table>

		<div style="margin-top:10px;margin-bottom:10px;text-align:center;<?=$display['point']?>">
			<input type="button" onClick="javascript:cancelYn(this);" style="width:190px;height:27px;" value="페이코 포인트 환불처리" />
		</div>

		<?if($cash_bool === true && $point_bool !== true) {?>
		<div style="margin-top:10px;margin-bottom:10px;text-align:center;">
			<input type="button" onClick="javascript:cancelStatus(this);" style="width:190px;height:27px;" value="수기환불완료" />
		</div>
		<?}?>

		<div id="loading" style="margin-top:10px;margin-bottom:10px;text-align:center;display:none;"><img src="../img/loading40.gif" /></div>
	</div>
</form>

<script>
	var point = 0;

	//환불완료 처리
	function cancelStatus(btn) {
		btn.style.display = "none";
		document.getElementById("loading").style.display = "block";

		var api_url = "./paycoVbankProc.php";
		var param_data = "?";
			param_data += "&mode=cancel_status";
			param_data += "&payco_settle_type=" + document.getElementsByName("payco_settle_type")[0].value;
			param_data += "&ordno=<?=$get_ordno?>";
			param_data += "&sno=<?=$get_sno?>";
			param_data += "&part=<?=$get_part?>";
			param_data += "&repayfee=" + document.getElementsByName("repayfee")[0].value;
			param_data += "&repay_point=" + document.getElementsByName("repay_point")[0].value;
			param_data += "&cancel_price=" + document.getElementsByName("cancel_price")[0].value;

		var api_ajax = new Ajax.Request(api_url, {
			method: 'post',
			parameters: param_data,
			onSuccess: function(req) {
				var response = eval( "(" + req.responseText + ")" );
					if(response["code"] == "000" || response["code"] == "0") {
						// 수기환불완료시...에는?
						if(confirm("<?=$msg['alert']?><?=$repay_add_msg?>")) {
							document.getElementsByName('repayfee[]')[0].value = document.getElementsByName("repayfee")[0].value;
							document.getElementsByName('repay[]')[0].value = document.getElementsByName("cancel_price")[0].value;

							repay.submit();

							parent.location.reload();
						}
						else {
							parent.location.reload();
						}
					}
					else if(response["code"] == "999") {
						document.getElementById("cancelFrom").style.display = "none";
						document.getElementById("cancelFail").innerHTML = response["msg"];
						return false;
					}
					else {
						alert(response["message"]);
						return;
					}
			},
			OnError: function() {
				return false;
			}
		});
	}

	function price_calculate() {
		var total_cancel_price = document.getElementsByName("total_cancel_price")[0].value;//취소예정금액[현금]
		var repay_point = document.getElementsByName("repay_point")[0].value;//취소예정금액[페이코 포인트]
		var repayfee = document.getElementsByName("repayfee")[0].value;//환불수수료
		if(point < 1) point = repay_point;
		else repay_point = point;

		if(total_cancel_price == "") total_cancel_price = 0;
		if(repay_point == "") repay_point = 0;
		if(repayfee == "") repayfee = 0;

		if(isNaN(repayfee)) {
			alert("환불 수수료는 숫자만 입력해 주시기 바랍니다.");
			document.getElementsByName("repayfee")[0].value = 0;
			return false;
		}
		else if(parseInt(repayfee) < 0) {
			alert("환불 수수료는 0원 이하로 입력할 수 없습니다.");
			document.getElementsByName("repayfee")[0].value = 0;
			return false;
		}

		if((parseInt(total_cancel_price) + parseInt(repay_point)) < parseInt(repayfee)) {
			alert("환불 수수료는 결제금액을 초과할 수 없습니다.(" + (parseInt(total_cancel_price) + parseInt(repay_point)) + " 원)");
			document.getElementsByName("repayfee")[0].value = 0;
			return false;
		}

		var cancel_price = (parseInt(total_cancel_price) + parseInt(repay_point)) - parseInt(repayfee);

		if(parseInt(repay_point) > 0) {
			if(parseInt(repayfee) > parseInt(total_cancel_price)) repay_point = repay_point - (parseInt(repayfee) - parseInt(total_cancel_price));
		}

		document.getElementById("text_cancel_price").innerText = cancel_price;
		document.getElementsByName("cancel_price")[0].value = cancel_price;
		document.getElementsByName("repay_point")[0].value = repay_point;
		return true;
	}

	// 페이코 포인트 환불처리
	function cancelYn(btn) {
		btn.style.display = "none";
		document.getElementById("loading").style.display = "block";

		var overlap_fee = document.getElementsByName("total_cancel_price")[0].value - document.getElementsByName("repayfee")[0].value;

		var api_url = "./paycoVbankProc.php";
		var param_data = "?";
			param_data += "&mode=order_cancel";
			param_data += "&ordno=<?=$get_ordno?>";
			param_data += "&sno=<?=$get_sno?>";
			param_data += "&part=<?=$get_part?>";
			param_data += "&repayfee=" + document.getElementsByName("repayfee")[0].value;
			param_data += "&repay_point=" + document.getElementsByName("repay_point")[0].value;
			param_data += "&payco_settle_type=" + document.getElementsByName("payco_settle_type")[0].value;
			param_data += "&overlap_fee=" + overlap_fee;

		if(price_calculate() === false) return false;

		var api_ajax = new Ajax.Request(api_url, {
			method: 'post',
			parameters: param_data,
			onSuccess: function(req) {
				var response = eval( "(" + req.responseText + ")" );
				if(response["code"] == "000") {
					if(confirm("<?=$msg['alert']?><?=$repay_add_msg?>")) {
						document.getElementsByName('repayfee[]')[0].value = document.getElementsByName("repayfee")[0].value;
						document.getElementsByName('repay[]')[0].value = document.getElementsByName("cancel_price")[0].value;

						repay.submit();

						parent.location.reload();
					}
					else {
						parent.location.reload();
					}
				}
				else {
					document.getElementById("cancelFrom").style.display = "none";
					document.getElementById("cancelFail").innerHTML = response["msg"];
					return false;
				}
			},
			OnError: function() {
				return false;
			}
		});
	}

	table_design_load();
</script>

<?
}
?>