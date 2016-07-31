<?php
/*********************************************************
* 파일명     :  paycoCancelProc.php
* 프로그램명 :  페이코 신용카드/휴대폰 결제 취소 관련 쇼핑몰처리 API
**********************************************************/
include "../lib.php";
include "../../conf/config.php";
include "../../lib/paycoApi.class.php";
include "../../lib/order.class.php";

if(!$paycoCfg && is_file(dirname(__FILE__) . '/../../conf/payco.cfg.php')){
	include dirname(__FILE__) . '/../../conf/payco.cfg.php';
}

$post_mode = $_POST['mode'];
$post_ordno = $_POST['ordno'];
$post_sno = $_POST['sno'];
$post_part = $_POST['part'];
$post_repayfee = $_POST['repayfee'];
$post_payco_settle_type = $_POST['payco_settle_type'];
$post_firsthand_refund = $_POST['firsthand_refund'];

if(isset($post_mode)) {//취소 데이터 생성
	$orderDeliveryItem = &load_class('orderDeliveryItem','orderDeliveryItem');
	$payco = &load_class('payco','payco');
	$paycoApi = &load_class('paycoApi','paycoApi');
	$json = &load_class('json','Services_JSON');
	$order = &load_class('order','order');
	$order->load($post_ordno);

	$msg = Array();

	/* 공통사항 - 페이코 취소완료 상태 확인 */
	if($payco->checkCancelYn($post_sno) === true) {
		$rtn['code'] = '555';
		$rtn['msg'] = '이미 페이코 결제취소가 완료된 주문입니다.';

		echo $json->encode($rtn);
		exit;
	}

	if($ordno) $orderDeliveryItem->ordno = $ordno;
	else $orderDeliveryItem->ordno = $post_ordno;
	$cancel_delivery = $orderDeliveryItem->cancel_delivery($post_sno);

	$tmp_item = $cancel_delivery['item'];

	for($i = 0; $i < count($cancel_delivery['item']); $i++) {
		if($cancel_delivery['item'][$i]['delivery'] == '1') {
			$cancel_delivery['item'][$i]['sno'] = $cancel_delivery['item'][$i]['oi_delivery_idx'];
			unset($cancel_delivery['item'][$i]['oi_delivery_idx']);
		}
	}

	if($post_part != 'Y') {//전체취소
		$arr_data['seller_key'] = $paycoCfg['paycoSellerKey'];
		$arr_data['ordno'] = $post_ordno;
		$arr_data['cancelTotalAmt'] = $cancel_delivery['total_cancel_price'] + $cancel_delivery['total_cancel_delivery_price'];
		$arr_data['cancelTotalFeeAmt'] = $post_repayfee;
		if($arr_data['cancelTotalFeeAmt'] == '') $arr_data['cancelTotalFeeAmt'] = '0';
	}
	else {//부분취소
		$arr_data['seller_key'] = $paycoCfg['paycoSellerKey'];
		$arr_data['ordno'] = $post_ordno;
		$arr_data['cancelTotalAmt'] = $cancel_delivery['total_cancel_price'] + $cancel_delivery['total_cancel_delivery_price'];
		$arr_data['cancelTotalFeeAmt'] = $post_repayfee;
		if($arr_data['cancelTotalFeeAmt'] == '') $arr_data['cancelTotalFeeAmt'] = '0';

		$arr_data['orderProducts'] = $cancel_delivery['item'];
	}
	$arr_data['payco_settle_type'] = $post_payco_settle_type;

	if($orderDeliveryItem->checkLastCancel($post_sno) === true) {
		//마지막 취소건인 경우 할인금액 제외
		$arr_data['cancelTotalAmt'] -= ($cancel_delivery['coupon']['m'] - $cancel_delivery['coupon']['f']) + $cancel_delivery['emoney'];
	}

	### 수기환불 진행 ###
	if($post_firsthand_refund === 'Y') {

		// 페이코 포인트 적립취소 데이터
		$payco_point_data['seller_key'] = $paycoCfg['paycoSellerKey'];
		$payco_point_data['ordno'] = $post_ordno;
		$payco_point_data['cancelTotalAmt'] = ($cancel_delivery['total_cancel_price'] + $cancel_delivery['total_cancel_delivery_price']) - $post_repay_fee;

		$item_data = $cancel_delivery['item'];

		$res = $paycoApi->request('cancel_mileage', $payco_point_data);//페이코 포인트 적립취소 데이터 전송
		$res = json_decode($res, true);

		if($res['code'] == '0' && $res['result']['canceledMileageAcmAmount'] > 0) {
			$msg[] = $arr_data['ordno'].' ('.date('Y:m:d H:i:s').')';
			$msg[] = '-----------------------------------';
			$msg[] = '페이코 포인트 적립취소 완료';
			$msg[] = '적립취소 마일리지 : '.$res['result']['canceledMileageAcmAmount'];
			$msg[] = '적립대상 마일리지 : '.$res['result']['remainingMileageAcmAmount'];
			$msg[] = '-----------------------------------';
		}
		else {
			$msg[] = $arr_data['ordno'].' ('.date('Y:m:d H:i:s').')';
			$msg[] = '-----------------------------------';
			$msg[] = '페이코 포인트 적립취소 실패 ['.iconv('utf-8', 'euc-kr', $res['message']).']';
			$msg[] = '-----------------------------------';
		}

		$arr_cancel_item_sno = $payco->getCancelItem($post_payco_settle_type, $post_ordno, $post_sno, $item_data);

		if(!empty($arr_cancel_item_sno)) {//페이코 주문item 상태변경
			### step 코드는 중요하지 않아 1로 고정처리함
			$status_data['seller_key'] = $paycoCfg['paycoSellerKey'];
			$status_data['payco_settle_type'] = $post_payco_settle_type;
			$status_data['ordno'] = $post_ordno;
			$status_data['step'] = '1';
			$status_data['step2'] = '44';
			$status_data['sno'] = implode('|', $arr_cancel_item_sno);

			$res = $paycoApi->request('order_status', $status_data);
			$res = json_decode($res, true);

			if($res['code'] == '0') {
				$msg[] = $arr_data['ordno'].' ('.date('Y:m:d H:i:s').')';
				$msg[] = '-----------------------------------';
				$msg[] = '취소상태 변경성공';
				$msg[] = '-----------------------------------';
			}
			else {
				$msg[] = $arr_data['ordno'].' ('.date('Y:m:d H:i:s').')';
				$msg[] = '-----------------------------------';
				$msg[] = '취소상태 변경실패 ['.iconv('utf-8', 'euc-kr', $res['msg']).']';
				$msg[] = '-----------------------------------';
			}
		}

		if($res['code'] == '0' || $res['code'] == '000') {
			$cancel_data['ordno'] = $post_ordno;//주문번호
			$cancel_data['cancelTotalAmt'] = $cancel_delivery['total_cancel_price'] + $cancel_delivery['total_cancel_delivery_price'];//(취소상품금액 + 취소상품배송비)
			$cancel_data['cancelTotalFeeAmt'] = $post_repay_fee;//환불수수료

			$payco_coupon_data['payco_firsthand_refund'] = 'Y';//수기환불 여부

			$payco = &load_class('payco','payco');
			$payco->paycoCancel($cancel_data, $post_sno, $post_part, $msg, $payco_coupon_data);

			### 주문취소완료 배송비 차감/내역 저장
			$orderDeliveryItem = &load_class('orderDeliveryItem','orderDeliveryItem');
			$orderDeliveryItem->update_delivery_data($cancel_delivery['delivery']);

			$rtn['code'] = '000';
		}
		else {
			$rtn['code'] = '999';
			//페이코 취소상태 변경 실패 로그 저장
			$payco->paycoCancelFailLog($post_ordno, $msg);
		}

		$rtn['msg'] = nl2br(implode("\n", $msg));
		echo $json->encode($rtn);
		exit;
	}
	else {
		### 페이코 취소진행 ###

		### 취소가능여부 조회 START ###
		$res = $paycoApi->request('order_cancel_yn', $arr_data);
		$arr_cancel_check = json_decode($res,true);
		### 취소가능여부 조회 END ###

		if($arr_cancel_check['cancel_yn'] === 'Y') {

			### 복합과세 데이터 설정 START ###
			/*
			 * $tax[taxall] => 3000		//과세상품금액
			 * $tax[taxfree] => 3000	//면세상품금액
			 * $tax[tax] => 2728		//과세상품금액(부가세제외)
			 * $tax[vat] => 272			//부가세
			*/
			$tax = $order->getCancelItemTaxWithSno($post_sno);

			if(isset($tax['code']) && isset($tax['msg'])) {
				$msg[] = $post_ordno.' ('.date('Y:m:d H:i:s').')';
				$msg[] = '-----------------------------------';
				$msg[] = '페이코 환불 실패';
				$msg[] = '취소요청금액 : '.number_format($arr_data['cancelTotalAmt']);
				$msg[] = '취소불가이유 : '.$tax['msg'];
				$msg[] = '-----------------------------------';

				$rtn['msg'] = nl2br(implode("\n", $msg));
				echo $json->encode($rtn);
				exit;
			}

			$minus_delivery_tax = 0;
			$munus_delivery_vat = 0;
			$delivery_tax = 0;
			$delivery_vat = 0;

			// 마지막 취소건인 경우 복합과세에 포함되어 있는 배송비 제외
			if($orderDeliveryItem->checkLastCancel($post_sno) === true) {
				$minus_delivery = $order->getDeliveryFee();
				$minus_delivery_tax = floor($minus_delivery / 1.1);
				$munus_delivery_vat = $minus_delivery - $minus_delivery_tax;
			}

			// 배송비 복합과세 별도 계산
			if($cancel_delivery['total_cancel_delivery_price'] > 0) {
				$delivery_tax = floor($cancel_delivery['total_cancel_delivery_price'] / 1.1);
				$delivery_vat = $cancel_delivery['total_cancel_delivery_price'] - $delivery_tax;
			}

			$arr_data['totalCancelTaxfreeAmt'] = $tax['taxfree'];//면세금액
			$arr_data['totalCancelTaxableAmt'] = $tax['tax'] + $delivery_tax - $minus_delivery_tax;//과세금액
			$arr_data['totalCancelVatAmt'] = $tax['vat'] + $delivery_vat - $munus_delivery_vat;//부가세

			$tax_total = $arr_data['totalCancelTaxfreeAmt'] + $arr_data['totalCancelTaxableAmt'] + $arr_data['totalCancelVatAmt'];

			if($tax_total != $arr_data['cancelTotalAmt']) {
				$msg[] = $post_ordno.' ('.date('Y:m:d H:i:s').')';
				$msg[] = '-----------------------------------';
				$msg[] = '계산된 복합과세 금액이 다릅니다.';
				$msg[] = '취소요청금액 : '.number_format($arr_data['cancelTotalAmt']);
				$msg[] = '복합과세금액 : '.number_format($tax_total);
				$msg[] = '면세금액 : '.number_format($arr_data['totalCancelTaxfreeAmt']);
				$msg[] = '과세금액 : '.number_format($arr_data['totalCancelTaxableAmt']);
				$msg[] = '부가세 : '.number_format($arr_data['totalCancelVatAmt']);
				$msg[] = '-----------------------------------';

				$rtn['msg'] = nl2br(implode("\n", $msg));
				echo $json->encode($rtn);
				exit;
			}
			### 복합과세 데이터 설정 END ###

			if($arr_cancel_check['price'] == ($arr_data['cancelTotalAmt'] - $arr_data['cancelTotalFeeAmt'])) {

				### 페이코 쿠폰 관련 유효성 체크 START ###
				if($order->offsetGet('settlekind') == 'c' && $order->offsetGet('payco_coupon_use_yn') == 'Y' && $order->offsetGet('payco_coupon_repay') == 'N') {//쿠폰사용 후 취소되지 않은 경우
					if($order->offsetGet('payco_firsthand_refund') == 'Y') {
						//수기환불된 이력이 있는 경우 취소처리 실패
						$msg[] = $post_ordno.' ('.date('Y:m:d H:i:s').')';
						$msg[] = '-----------------------------------';
						$msg[] = '수기환불된 이력이 있어 카드취소가 불가합니다.';
						$msg[] = '-----------------------------------';

						$rtn['msg'] = nl2br(implode("\n", $msg));
						echo $json->encode($rtn);
						exit;
					}

					if($order->offsetGet('payco_coupon_price') > ($arr_data['cancelTotalAmt'] - $arr_data['cancelTotalFeeAmt'])) {
						//쿠폰금액보다 취소하려는 금액이 작은 경우 수기환불해야 함
						$msg[] = $post_ordno.' ('.date('Y:m:d H:i:s').')';
						$msg[] = '-----------------------------------';
						$msg[] = '쿠폰사용 금액보다 취소하려는 금액이 작습니다.';
						$msg[] = '[수기환불 요망]';
						$msg[] = '-----------------------------------';

						$rtn['msg'] = nl2br(implode("\n", $msg));
						echo $json->encode($rtn);
						exit;
					}
				}
				### 페이코 쿠폰 관련 유효성 체크 END ###

				### 페이코 취소처리 START ###
				$json_res = $paycoApi->request($post_mode, $arr_data);
				$res = json_decode($json_res, true);
				### 페이코 취소처리 END ###

				### 페이코 취소결과처리 START ###
				if($res['code'] == '000') {
					$msg[] = $arr_data['ordno'].' ('.date('Y:m:d H:i:s').')';
					$msg[] = '-----------------------------------';
					$msg[] = '페이코 환불 성공';
					$msg[] = '취소내역번호 : '.$res['cancelTradeSeq'];
					$msg[] = '취소요청금액 : '.number_format($res['totalCancelPaymentAmt']);
					$msg[] = '취소면세금액 : '.$arr_data['totalCancelTaxfreeAmt'];
					$msg[] = '취소과세금액 : '.$arr_data['totalCancelTaxableAmt'];
					$msg[] = '취소부가세 : '.$arr_data['totalCancelVatAmt'];
					$msg[] = '-----------------------------------';

					if($order->offsetGet('settlekind') == 'c' && $order->offsetGet('payco_coupon_use_yn') == 'Y' && $order->offsetGet('payco_coupon_repay') == 'N') {
						if(isset($res['cancelPaymentDetails'])) {
							foreach($res['cancelPaymentDetails'] as $cancel_payment_detail) {
								switch($cancel_payment_detail['paymentMethodCode']) {
									case '75' ://페이코 쿠폰(자유이용쿠폰)
									case '76' ://카드 쿠폰
									case '77' ://가맹점 쿠폰
										//취소하려는 금액이 페이코 쿠폰 사용금액보다 큰 경우에만 취소처리
										$payco_coupon_data['payco_coupon_repay'] = 'Y';//쿠폰환불여부
										$msg[] = $post_ordno.' ('.date('Y:m:d H:i:s').')';
										$msg[] = '-----------------------------------';
										$msg[] = '페이코 쿠폰취소 완료';
										$msg[] = '페이코 쿠폰금액 : '.number_format($cancel_payment_detail['cancelPaymentAmt']).'원';
										$msg[] = '-----------------------------------';
										break;
								}
							}
						}
					}

					### 주문취소완료 상태처리
					$payco->paycoCancel($arr_data, $post_sno, $post_part, $msg, $payco_coupon_data);

					### 주문취소완료 배송비 차감/내역 저장
					$orderDeliveryItem = &load_class('orderDeliveryItem','orderDeliveryItem');
					$orderDeliveryItem->update_delivery_data($cancel_delivery['delivery']);

					$rtn['code'] = '000';
				}
				else {
					$rtn['code'] = '999';

					$msg[] = $post_ordno.' ('.date('Y:m:d H:i:s').')';
					$msg[] = '-----------------------------------';
					$msg[] = '페이코 환불 실패';
					$msg[] = '취소요청금액 : '.number_format($arr_data['cancelTotalAmt']);
					if(isset($res['msg'])) $msg[] = 'Payco 취소불가이유 : '.iconv('utf-8', 'euc-kr', $res['msg']);
					$msg[] = '-----------------------------------';
				}
				### 페이코 취소결과처리 END ###
			}
			else {
				### 취소예정금액과 취소금액이 다른 경우
				$rtn['code'] = '999';

				$text_msg = 'Payco 취소불가이유 : '.iconv('utf-8', 'euc-kr', $arr_cancel_check['cancelImpossibleReason']);
				if($arr_cancel_check['pgCancelPossibleAmt'] > 0) $text_msg .= ' 취소가능금액 : '.$arr_cancel_check['pgCancelPossibleAmt'];

				$msg[] = $post_ordno.' ('.date('Y:m:d H:i:s').')';
				$msg[] = '-----------------------------------';
				$msg[] = '페이코 환불 실패';
				$msg[] = '취소요청금액 : '.number_format($arr_data['cancelTotalAmt']);
				$msg[] = $text_msg;
				$msg[] = '-----------------------------------';
			}
		}
		else {
			### 취소불가 상태 처리 ###
			$rtn['code'] = '999';

			$msg[] = $post_ordno.' ('.date('Y:m:d H:i:s').')';
			$msg[] = '-----------------------------------';
			$msg[] = '페이코 환불체크 실패';
			$msg[] = '취소요청금액 : '.number_format($arr_data['cancelTotalAmt']);
			if(isset($arr_cancel_check['cancelImpossibleReason'])) $msg[] = 'Payco 취소불가이유 : '.iconv('utf-8', 'euc-kr', $arr_cancel_check['cancelImpossibleReason']);
			if(isset($arr_cancel_check['msg'])) $msg[] = 'Payco 취소불가이유 : '.iconv('utf-8', 'euc-kr', $arr_cancel_check['msg']);
			$msg[] = '-----------------------------------';
		}
	}

	//환불실패 로그 저장
	if($rtn['code'] == '999') $payco->paycoCancelFailLog($post_ordno, $msg);

	$rtn['msg'] = nl2br(implode("\n", $msg));
	echo $json->encode($rtn);
	exit;
}
?>