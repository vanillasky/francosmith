<?php
/*********************************************************
* 파일명     :  paycoCancel.php
* 프로그램명 :  페이코 결제취소(신용카드,휴대폰,페이코포인트)
**********************************************************/
include "../lib.php";
include "../../conf/config.php";
include "../../lib/paycoApi.class.php";
if(!$paycoCfg && is_file(dirname(__FILE__) . '/../../conf/payco.cfg.php')){
	include dirname(__FILE__) . '/../../conf/payco.cfg.php';
}

$get_ordno = $_GET['ordno'];
$get_sno = $_GET['sno'];
$get_repayfee = $_GET['repayfee'];
$get_repay = $_GET['repay'];
$get_part = $_GET['part'];
$get_remoney = $_GET['remoney'];
if($get_remoney == '') $get_remoney = 0;

if($get_ordno) {
	if($get_sno) {
		//환불완료 후 추가확인 메세지
		$repay_add_msg = '';
		if($get_remoney > 0) $arr_repay_add_msg[] = '\n\n※환불완료 처리시 결제시 사용한 적립금 중 '.number_format($get_remoney).'원을 되돌려줍니다.';
		if(isset($arr_repay_add_msg)) $repay_add_msg = implode('\n\n',$arr_repay_add_msg);

		//환불진행 페이지 데이터 생성
		if(!$get_repayfee) $repayfee = 0;
		else $repayfee = $get_repayfee;

		$orderDeliveryItem = &load_class('orderDeliveryItem','orderDeliveryItem', $get_ordno);
		$payco = &load_class('payco','payco');
		$order = &load_class('order','order');
		$order->load($get_ordno);

		if($payco->checkCancelYn($get_sno) === true) {
			msg('이미 페이코 결제취소가 완료된 주문입니다.', -1);
			echo('<script>location.reload();</script>');
			exit;
		}

		$cancel_delivery = $orderDeliveryItem->cancel_delivery($get_sno);

		$total_cancel_price = $cancel_delivery['total_cancel_price'];
		$total_cancel_delivery_price = $cancel_delivery['total_cancel_delivery_price'];
		$cancel_price = ($total_cancel_price + $total_cancel_delivery_price) - $repayfee;

		if($total_cancel_delivery_price < 1) $display['delivery'] = 'display:none;';

		if($orderDeliveryItem->checkLastCancel($get_sno) === true) {
			$total_cancel_price -= ($cancel_delivery['coupon']['m'] - $cancel_delivery['coupon']['f']) + $cancel_delivery['emoney'];
			$cancel_price -= ($cancel_delivery['coupon']['m'] - $cancel_delivery['coupon']['f']) + $cancel_delivery['emoney'];
		}

		$firsthand_refund = 'N';
		if($order->offsetGet('payco_coupon_use_yn') === 'Y') {
			$coupon_text = '※ 페이코 쿠폰('.number_format($order->offsetGet('payco_coupon_price')).'원)이 사용된 주문입니다.';

			// $firsthand_refund : 수기환불여부 Y = 수기환불, N = 페이코취소
			if($order->offsetGet('payco_firsthand_refund') == 'Y') {
				$firsthand_refund = 'Y';//수기환불을 진행한 이력이 있는 경우 수기환불만 진행
				$cancel_text = '※ 이전 취소에서 수기환불된 주문입니다.';
			}
			else if($order->offsetGet('payco_coupon_repay') == 'Y') {//수기환불을 안한 상태에서 쿠폰이 취소되어 있는 경우 페이코 취소진행
				$firsthand_refund = 'N';
				unset($coupon_text);
			}
			else if($order->offsetGet('payco_coupon_price') >= $cancel_price) $firsthand_refund = 'Y';//쿠폰취소가 안된 상태에서 쿠폰 사용금액보다 취소하려는 금액이 같거나 작으면 수기환불
			else $firsthand_refund = 'N';//그외 페이코 취소진행
		}

		if($firsthand_refund === 'Y') {
			$display['cd_refund'] = 'display:none;';
			$display['fh_refund'] = '';
		}
		else {
			$display['cd_refund'] = '';
			$display['fh_refund'] = 'display:none;';
		}
	}
	else {
		//주문상세 페이지에서 주문전체 취소 처리시 취소접수 처리
		$payco = &load_class('payco','payco');
		$res = $payco->getOrderData($get_ordno, 'gd_order_item', 'query');

		while($item = $db->fetch($res)) {

			if($item['cancel']) {
				$cancel_no = $item['cancel'];
				$cancel_no_yn = true;
				break;
			}
			$data['sno'][] = $item['sno'];
			$data['ea'][] = $item['ea'];
		}

		if($cancel_no_yn !== true) {
			$data['memo'] = '';
			$data['name'] = $_COOKIE['member']['name'];
			$data['code'] = 9; // 주문 취소시 취소 사유 코드 (기본값 9)

			### 주문 취소 접수
			chkCancel($get_ordno,$data);

			$arr_coulms[] = 'sno';
			$cancel_no = $payco->getOrderData($get_ordno, 'gd_order_cancel', 'fetch_true', $arr_coulms, 'order by sno desc limit 1');

			echo('<script>location.href = "./paycoCancel.php?ordno='.$get_ordno.'&sno='.$cancel_no['sno'].'";</script>');
			exit;
		}
		else {
			echo('<script>alert("이미 취소접수/완료된 주문상품이 있습니다.");parent.location.reload();</script>');
			exit;
		}
	}
?>
<!--환불완료 전용폼-->
<form id="repay" method="post" action="./indb.php">
	<input type="hidden" name="mode" value="repay">
	<input type="hidden" name="bankcode[]" value="">
	<input type="hidden" name="bankaccount[]" value="">
	<input type="hidden" name="bankuser[]" value="">
	<input type="hidden" name="m_no[]" value="<?=$order->offsetGet('m_no')?>">
	<input type="hidden" name="sno[]" value="<?=$get_sno?>">
	<input type="hidden" name="ordno[]" value="<?=$get_ordno?>">
	<input type="hidden" name="repayfee[]" value=""><!--수수료-->
	<input type="hidden" name="repay[]" value=""><!--환불금액-->
	<input type="hidden" name="chk[]" value="0"><!--순번-->
	<input type="hidden" name="remoney[]" value="<?=$get_remoney?>"><!--환불적립금-->
</form>

<link rel="styleSheet" href="../style.css">
<script type="text/javascript" src="../common.js"></script>
<script type="text/javascript" src="../prototype.js"></script>
<form id="frmIni" name="frmIni" onsubmit="return false;">
<input type="hidden" name="payco_settle_type" value="<?=$order->offsetGet('payco_settle_type')?>">
<input type="hidden" name="firsthand_refund" value="<?=$firsthand_refund?>">
<input type="hidden" name="payco_coupon_price" value="<?=$order->offsetGet('payco_coupon_price')?>">
<input type="hidden" name="mode" value="order_cancel" />
<input type="hidden" name="ordno" value="<?=$get_ordno?>" /> <!-- 주문번호 -->
<input type="hidden" name="sno" value="<?=$get_sno?>" /> <!-- 환불접수번호 -->
<input type="hidden" name="repay" value="<?=$get_repay?>" /> <!-- 환불접수된 금액 -->
<input type="hidden" name="part" value="<?=$get_part?>" /> <!-- 환불접수된 금액 -->
	<div class="subtitle">
		<div class="title title_top">페이코 <?=$r_settlekind[$order->offsetGet('settlekind')]?> 취소/환불<span></span></div>
	</div>
	<div id="cancelFail" class="input_wrap"></div>

	<div id="cancelFrom" class="input_wrap">
		<div class="extext"><?=$coupon_text?></div>
		<div class="extext"><?=$cancel_text?></div>
		<div id="paycoProcess"></div>

		<table class=tb>
			<col width="19%" class="cellC">
			<col width="21%" class="cellL">
			<col width="60%" class="cellL">
			<tr>
				<th class="input_title r_space" align="left">상품금액</th>
				<td class="input_area" align="right">
					<input type="hidden" name="total_cancel_price" value="<?=$total_cancel_price?>" class="input_text width_small" readonly /><?=$total_cancel_price?> 원
				</td>
				<td></td>
			</tr>

			<tr style="<?=$display['delivery']?>">
				<th class="input_title r_space" align="left">배송비</th>
				<td class="input_area" align="right">
					<input type="hidden" name="total_cancel_delivery_price" value="<?=$total_cancel_delivery_price?>" class="input_text width_small" readonly /><?=$total_cancel_delivery_price?> 원
				</td>
				<td>
				</td>
			</tr>

			<tr>
				<th class="input_title r_space" align="left">환불 수수료</th>
				<td class="input_area" align="right">
					<input type="text" name="repayfee" value="<?=$repayfee?>" onblur="price_calculate();btnType();" class="input_text width_small" style="width:60px;text-align:right;" /> 원
				</td>
				<td>
				<ul style="margin-left:-25px;">
					<li>환불시 발생되는 반송비용 및 기타 수수료 등을 정하실 수 있습니다.</li>
				</ul>
				</td>
			</tr>

			<tr>
				<th class="input_title r_space" align="left">총 취소예정금액</th>
				<td class="input_area" align="right">
					<span id="text_cancel_price"><?=$cancel_price?></span> 원
					<input type="hidden" name="cancel_price" value="<?=$cancel_price?>" class="input_text width_small" />
				</td>
				<td>
				<ul style="margin-left:-25px;">
					<li>총 취소예정금액 = (상품금액 + 배송비) - 환불수수료</li>
				</ul>
				</td>
			</tr>
		</table>

		<!--수기환불-->
		<div id="fh_refund" style="<?=$display['fh_refund']?>">
			<div style="margin:3px;padding:5px;">
				<div style="color:red;">※ 총 취소예정금액이 사용된 페이코 쿠폰금액보다 작아 수기환불로 처리해야합니다.</div>
				<div style="padding-left:13px;color:#B70000;">- 사용된 쿠폰의 금액보다 취소할 결제금액이 작으면 페이코 카드취소가 불가능하며 수기환불로 처리해야합니다.</div>
				<div style="padding-left:13px;color:#B70000;">- 페이코 포인트가 사용된 주문건의 수기환불시 페이코 포인트도 함께 수기환불로 처리해야합니다.</div>
				<div style="padding-left:13px;color:#B70000;">- 수기환불로 처리된 페이코 포인트는 페이코 파트너센터에서 정산받을 수 있습니다.</div>
				<div style="color:red;">※ 한 번 수기환불로 처리한 주문의 추가 취소는 모두 수기환불로 처리해야합니다.</div>
			</div>

			<div style="margin-top:10px;margin-bottom:10px;text-align:center;">
				<input type="button" onClick="javascript:formChk(this);" style="width:190px;height:27px;"value="수기환불완료" />
			</div>
		</div>

		<!--취소API-->
		<div id="cd_refund" style="<?=$display['cd_refund']?>">
			<div style="margin-top:10px;margin-bottom:10px;text-align:center;">
				<input type="button" onClick="javascript:formChk(this);" style="width:190px;height:27px;"value="결제취소/환불" />
			</div>
		</div>
		<div id="loading" style="margin-top:10px;margin-bottom:10px;text-align:center;display:none;"><img src="../img/loading40.gif" /></div>
	</div>
</form>


<script>
	function formChk(btn) {

		if(price_calculate()) {
			btn.style.display = "none";
			document.getElementById("loading").style.display = "block";
			var form = document.getElementsByName("frmIni")[0];

			var repayfee = 0;
			var cancel_price = 0;
			var param_data = "?";
			for(var i = 0; i < form.elements.length; i++) {
				if (form.elements[i].name === "") {
					continue;
				}

				if(form.elements[i].nodeName == "INPUT") {
					switch (form.elements[i].type) {
						case "text" :
						case "hidden" :
							if(form.elements[i].name == "repayfee") {
								repayfee = encodeURIComponent(form.elements[i].value);
							}
							else if(form.elements[i].name == "cancel_price") {
								cancel_price = encodeURIComponent(form.elements[i].value);
							}
							param_data += "&" + form.elements[i].name + "=" + encodeURIComponent(form.elements[i].value);
							break;
					}
				}
			}

			var api_url = "./paycoCancelProc.php";

			var api_ajax = new Ajax.Request(api_url, {
				method: "post",
				parameters: param_data,
				onSuccess: function(req) {
					var response = eval( "(" + req.responseText + ")" );

					if(response["code"] == "000") {
						if(confirm("페이코 결제취소가 완료되었습니다.\n환불완료처리를 하시겠습니까?<?=$repay_add_msg?>")) {
							document.getElementsByName("repayfee[]")[0].value = repayfee;//수수료
							document.getElementsByName("repay[]")[0].value = cancel_price;//환불금액

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
	}

	function price_calculate() {
		var total_cancel_price = document.getElementsByName("total_cancel_price")[0].value;
		var total_cancel_delivery_price = document.getElementsByName("total_cancel_delivery_price")[0].value;
		var repayfee = document.getElementsByName("repayfee")[0].value;
		if(repayfee == "") repayfee = 0;
		var cancel_price = document.getElementsByName("cancel_price")[0].value;
		var settle_price = parseInt(total_cancel_price) + parseInt(total_cancel_delivery_price);

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

		if(parseInt(settle_price) < parseInt(repayfee)) {
			alert("환불 수수료는 결제금액을 초과할 수 없습니다.(" + settle_price + " 원)");
			document.getElementsByName("repayfee")[0].value = 0;
			return false;
		}

		var cancel_price = (parseInt(total_cancel_price) + parseInt(total_cancel_delivery_price)) - parseInt(repayfee);
		document.getElementById("text_cancel_price").innerText = cancel_price;
		document.getElementsByName("cancel_price")[0].value = cancel_price;
		return true;
	}

	function btnType() {
		var coupon_price = document.getElementsByName("payco_coupon_price")[0].value;
		var cancel_price = document.getElementsByName("cancel_price")[0].value;

		if("<?=$order->offsetGet('payco_coupon_repay')?>" == "N" && ("<?=$order->offsetGet('payco_firsthand_refund')?>" == "Y" || parseInt(coupon_price) >= parseInt(cancel_price))) {
			document.getElementById("fh_refund").style.display = "block";
			document.getElementById("cd_refund").style.display = "none";
			document.getElementsByName("firsthand_refund")[0].value = "Y";
		}
		else {
			document.getElementById("fh_refund").style.display = "none";
			document.getElementById("cd_refund").style.display = "block";
			document.getElementsByName("firsthand_refund")[0].value = "N";
		}
	}

	table_design_load();
</script>

<?
}
?>