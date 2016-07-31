<?php
/*********************************************************
* 파일명     :  paycoVbankProc.php
* 프로그램명 :  페이코 가상계좌 결제 취소 관련 쇼핑몰처리 API
**********************************************************/
include "../lib.php";
include "../../conf/config.php";
include "../../lib/paycoApi.class.php";

if(!$paycoCfg && is_file(dirname(__FILE__) . '/../../conf/payco.cfg.php')){
	include dirname(__FILE__) . '/../../conf/payco.cfg.php';
}

$post_mode = $_POST['mode'];//mode
$post_ordno = $_POST['ordno'];//주문번호
$post_sno = $_POST['sno'];//취소sno
$post_part = $_POST['part'];//부분취소여부 N = 전체취소, Y = 부분취소
$post_repay_fee = $_POST['repayfee'];//환불수수료
$post_repay_point = $_POST['repay_point'];// 환불하려는 페이코 포인트
$post_payco_settle_type = $_POST['payco_settle_type'];//페이코 주문수단(checkout=간편구매, easypay=간편결제)
$post_overlap_fee = $_POST['overlap_fee'];// 페이코 포인트 제외 환불금액

$orderDeliveryItem = &load_class('orderDeliveryItem','orderDeliveryItem');

if($post_ordno) $orderDeliveryItem->ordno = $post_ordno;
else $orderDeliveryItem->ordno = $post_ordno;
$cancel_delivery = $orderDeliveryItem->cancel_delivery($post_sno);

$payco = &load_class('payco','payco');
if($payco->checkCancelYn($post_sno) === true) {
	$rtn['code'] = '555';
	$rtn['msg'] = '이미 페이코 결제취소가 완료된 주문입니다.';

	$json = &load_class('json','Services_JSON');
	echo $json->encode($rtn);
	exit;
}

if($post_part == 'Y') {//부분취소
	$arr_data['seller_key'] = $paycoCfg['paycoSellerKey'];
	$arr_data['ordno'] = $post_ordno;
	$arr_data['cancelTotalAmt'] = $post_repay_point + abs($post_overlap_fee);// 환불하려는 페이코 포인트
	$arr_data['cancelTotalFeeAmt'] = abs($post_overlap_fee);
	$arr_data['payco_settle_type'] = $post_payco_settle_type;

	$goods_cnt = 0;

	foreach($cancel_delivery['item'] as $tmp_item) {
		if(!$tmp_item['delivery']) $goods_cnt++;
	}

	$arr_data['orderProducts'] = $cancel_delivery['item'];
}
else {//전체취소
	// 환불수수료는 상품금액을 먼저 차감 후 나머지 금액을 차감한다.
	if($post_overlap_fee < 0) {
		//상품금액보다 환불수수료가 높은 경우 $post_overlap_fee 값이 페이코 포인트에서 사용될 환불 수수료이다.
		$cancelTotalAmt = $post_repay_point;
		$cancelTotalFeeAmt = abs($post_overlap_fee);
	}
	else {
		//상품금액보다 환불수수료가 작은 경우 환불될 페이코 포인트에서 환불수수료는 없다.
		$cancelTotalAmt = $post_repay_point;
		$cancelTotalFeeAmt = '0';
	}

	$arr_data['seller_key'] = $paycoCfg['paycoSellerKey'];
	$arr_data['ordno'] = $post_ordno;
	$arr_data['cancelTotalAmt'] = $cancelTotalAmt;// 환불하려는 페이코 포인트
	$arr_data['cancelTotalFeeAmt'] = $cancelTotalFeeAmt;
	$arr_data['payco_settle_type'] = $post_payco_settle_type;
}

switch($post_mode) {
	case 'order_cancel' :
		### 페이코 포인트 환불가능금액 확인 START ###
		$paycoApi = &load_class('paycoApi','paycoApi');
		$res = $paycoApi->request('order_cancel_yn', $arr_data);
		$tmp_res = json_decode($res, true);
		### 페이코 포인트 환불가능금액 확인 END ###

		if($tmp_res['code'] !== '000') {
			$rtn['code'] = '999';

			$msg[] = $post_ordno.' ('.date('Y:m:d H:i:s').')';
			$msg[] = '-----------------------------------';
			$msg[] = '페이코 포인트 환불실패';
			$msg[] = '취소요청금액 : '.number_format($arr_data['cancelTotalAmt']);
			if(isset($tmp_res['cancelImpossibleReason'])) $msg[] = 'Payco 취소불가이유 : '.iconv('utf-8', 'euc-kr', $tmp_res['cancelImpossibleReason']);
			if(isset($tmp_res['msg'])) $msg[] = 'Payco 취소불가이유 : '.iconv('utf-8', 'euc-kr', $tmp_res['msg']);
			$msg[] = '-----------------------------------';
		}
		else {
			if(($arr_data['cancelTotalAmt'] - $arr_data['cancelTotalFeeAmt']) > $tmp_res['price']) {
				$rtn['code'] = '999';

				$msg[] = $post_ordno.' ('.date('Y:m:d H:i:s').')';
				$msg[] = '-----------------------------------';
				$msg[] = '페이코 포인트 환불실패';
				$msg[] = '취소요청금액 : '.number_format($arr_data['cancelTotalAmt']);
				$msg[] = '취소불가이유 : 취소가능한 금액을 초과하였습니다. [취소가능금액 : '.number_format($tmp_res['price']).']';
				$msg[] = '-----------------------------------';
			}
			else {
				/*
				 * 페이코 가상계좌인 경우 복합과세 계산 제외 - 2015-05-04
				 * 1. 가상계좌 취소시 취소금액을 전송하지 않고 상태만 전송함.
				 * 2. 가상계좌 + 페이코 포인트 주문시엔 페이코 포인트는 할인수단이기 때문에 복합과세 계산이 필요없음
				*/

				//페이코 포인트 환불처리
				$json_res = $paycoApi->request($post_mode, $arr_data);
				$res = json_decode($json_res, true);

				if($res['code'] == '000') {
					$msg[] = "\n\n".$arr_data['ordno'].' ('.date('Y:m:d H:i:s').')';
					$msg[] = '-----------------------------------';
					$msg[] = '페이코 포인트 환불성공';
					$msg[] = '취소내역번호 : '.$res['cancelTradeSeq'];
					$msg[] = '총 취소금액 : '.$res['totalCancelPaymentAmt'];
					$msg[] = '-----------------------------------';

					### 페이코 포인트 환불완료 처리
					$query = "update ".GD_ORDER." set settlelog=concat(ifnull(settlelog,''),'".implode("\n",$msg)."'), payco_use_point_repay = payco_use_point_repay + ".$res['totalCancelPaymentAmt']." where ordno='".$arr_data['ordno']."'";
					$db -> query($query);

					if($post_overlap_fee > 0) $post_overlap_fee = 0;
					else $post_overlap_fee = abs($post_overlap_fee);

					// 환불처리된 페이코 포인트 저장
					// 페이코 포인트 환불 후 pgcancel을 p로 저장한다.
					$query2 = "update ".GD_ORDER_CANCEL." set rpayco_point = rpayco_point + ".$res['totalCancelPaymentAmt'].", rfee=".$post_repay_fee.", rpayco_point_fee=".$post_overlap_fee.", pgcancel='p' where sno='".$post_sno."'";
					$db -> query($query2);

					### 주문취소완료 배송비 차감/내역 저장
					$orderDeliveryItem = &load_class('orderDeliveryItem','orderDeliveryItem');
					$orderDeliveryItem->update_delivery_data($cancel_delivery['delivery']);

					### 마일리지 적립취소 및 취소상태 변경 진행

					// 페이코 포인트 적립취소 데이터
					$payco_point_data['seller_key'] = $paycoCfg['paycoSellerKey'];
					$payco_point_data['ordno'] = $post_ordno;
					$payco_point_data['cancelTotalAmt'] = ($cancel_delivery['total_cancel_price'] + $cancel_delivery['total_cancel_delivery_price']) - $post_repay_fee;

					$item_data = $cancel_delivery['item'];

					$paycoApi = &load_class('paycoApi','paycoApi');
					$res = $paycoApi->request('cancel_mileage', $payco_point_data);//페이코 포인트 적립취소 데이터 전송
					$res = json_decode($res, true);

					if($res['code'] == '0' && $res['canceledMileageAcmAmount'] > 0) {
						$msg[] = "\n\n".$arr_data['ordno'].' ('.date('Y:m:d H:i:s').')';
						$msg[] = '-----------------------------------';
						$msg[] = '페이코 포인트 적립취소 완료';
						$msg[] = '적립취소 마일리지 : '.$res['canceledMileageAcmAmount'];
						$msg[] = '적립대상 마일리지 : '.$res['remainingMileageAcmAmount'];
						$msg[] = '-----------------------------------';
					}
					else {
						$msg[] = $arr_data['ordno'].' ('.date('Y:m:d H:i:s').')';
						$msg[] = '-----------------------------------';
						$msg[] = '페이코 포인트 적립취소 실패 ['.iconv('utf-8', 'euc-kr', $res['message']).']';
						$msg[] = '-----------------------------------';
					}

					//주문취소 상태 변경대상 조회
					$arr_cancel_item_sno = $payco->getCancelItem($post_payco_settle_type, $post_ordno, $post_sno, $item_data);

					if(!empty($arr_cancel_item_sno)) {//페이코 주문item 상태변경
						### step 코드는 중요하지 않아 1로 고정처리함
						$status_data['seller_key'] = $paycoCfg['paycoSellerKey'];
						$status_data['payco_settle_type'] = $post_payco_settle_type;
						$status_data['ordno'] = $post_ordno;
						$status_data['step'] = '1';
						$status_data['step2'] = '44';
						$status_data['sno'] = implode('|', $arr_cancel_item_sno);

						$paycoApi = &load_class('paycoApi','paycoApi');
						$res = $paycoApi->request('order_status', $status_data);

						$res = json_decode($res, true);

						if($res['code'] == '0') {
							$msg[] = "\n\n".$arr_data['ordno'].' ('.date('Y:m:d H:i:s').')';
							$msg[] = '-----------------------------------';
							$msg[] = '취소상태 변경성공';
							$msg[] = '-----------------------------------';
						}
						else {
							$rtn['code'] = '999';

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
					}
					else {
						//페이코 취소상태 변경 실패 로그 저장
						$payco->paycoCancelFailLog($post_ordno, $msg);
					}

					$json = &load_class('json','Services_JSON');
					echo $json->encode($res);
					exit;

				}
				else {
					$rtn['code'] = '999';

					$msg[] = $post_ordno.' ('.date('Y:m:d H:i:s').')';
					$msg[] = '-----------------------------------';
					$msg[] = '페이코 포인트 환불실패';
					$msg[] = '취소요청금액 : '.number_format($arr_data['cancelTotalAmt']);
					$msg[] = 'Payco 취소불가이유 : '.iconv('utf-8', 'euc-kr', $res['msg']);
					$msg[] = '-----------------------------------';
				}
			}
		}

		$rtn['msg'] = nl2br(implode("\n", $msg));

		$json = &load_class('json','Services_JSON');
		echo $json->encode($rtn);
		exit;

		break;


	case 'cancel_status' ://취소상태 변경

		// 페이코 포인트 적립취소 데이터
		$payco_point_data['seller_key'] = $paycoCfg['paycoSellerKey'];
		$payco_point_data['ordno'] = $post_ordno;
		$payco_point_data['cancelTotalAmt'] = ($cancel_delivery['total_cancel_price'] + $cancel_delivery['total_cancel_delivery_price']) - $post_repay_fee;

		$item_data = $cancel_delivery['item'];

		$paycoApi = &load_class('paycoApi','paycoApi');
		$res = $paycoApi->request('cancel_mileage', $payco_point_data);//페이코 포인트 적립취소 데이터 전송
		$res = json_decode($res, true);

		if($res['code'] == '0' && $res['canceledMileageAcmAmount'] > 0) {
			$msg[] = $arr_data['ordno'].' ('.date('Y:m:d H:i:s').')';
			$msg[] = '-----------------------------------';
			$msg[] = '페이코 포인트 적립취소 완료';
			$msg[] = '적립취소 마일리지 : '.$res['canceledMileageAcmAmount'];
			$msg[] = '적립대상 마일리지 : '.$res['remainingMileageAcmAmount'];
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

			$paycoApi = &load_class('paycoApi','paycoApi');
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

		$json = &load_class('json','Services_JSON');

		$rtn['msg'] = nl2br(implode("\n", $msg));
		echo $json->encode($rtn);
		exit;

		break;
}
?>