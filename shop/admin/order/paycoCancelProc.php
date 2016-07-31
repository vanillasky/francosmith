<?php
/*********************************************************
* ���ϸ�     :  paycoCancelProc.php
* ���α׷��� :  ������ �ſ�ī��/�޴��� ���� ��� ���� ���θ�ó�� API
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

if(isset($post_mode)) {//��� ������ ����
	$orderDeliveryItem = &load_class('orderDeliveryItem','orderDeliveryItem');
	$payco = &load_class('payco','payco');
	$paycoApi = &load_class('paycoApi','paycoApi');
	$json = &load_class('json','Services_JSON');
	$order = &load_class('order','order');
	$order->load($post_ordno);

	$msg = Array();

	/* ������� - ������ ��ҿϷ� ���� Ȯ�� */
	if($payco->checkCancelYn($post_sno) === true) {
		$rtn['code'] = '555';
		$rtn['msg'] = '�̹� ������ ������Ұ� �Ϸ�� �ֹ��Դϴ�.';

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

	if($post_part != 'Y') {//��ü���
		$arr_data['seller_key'] = $paycoCfg['paycoSellerKey'];
		$arr_data['ordno'] = $post_ordno;
		$arr_data['cancelTotalAmt'] = $cancel_delivery['total_cancel_price'] + $cancel_delivery['total_cancel_delivery_price'];
		$arr_data['cancelTotalFeeAmt'] = $post_repayfee;
		if($arr_data['cancelTotalFeeAmt'] == '') $arr_data['cancelTotalFeeAmt'] = '0';
	}
	else {//�κ����
		$arr_data['seller_key'] = $paycoCfg['paycoSellerKey'];
		$arr_data['ordno'] = $post_ordno;
		$arr_data['cancelTotalAmt'] = $cancel_delivery['total_cancel_price'] + $cancel_delivery['total_cancel_delivery_price'];
		$arr_data['cancelTotalFeeAmt'] = $post_repayfee;
		if($arr_data['cancelTotalFeeAmt'] == '') $arr_data['cancelTotalFeeAmt'] = '0';

		$arr_data['orderProducts'] = $cancel_delivery['item'];
	}
	$arr_data['payco_settle_type'] = $post_payco_settle_type;

	if($orderDeliveryItem->checkLastCancel($post_sno) === true) {
		//������ ��Ұ��� ��� ���αݾ� ����
		$arr_data['cancelTotalAmt'] -= ($cancel_delivery['coupon']['m'] - $cancel_delivery['coupon']['f']) + $cancel_delivery['emoney'];
	}

	### ����ȯ�� ���� ###
	if($post_firsthand_refund === 'Y') {

		// ������ ����Ʈ ������� ������
		$payco_point_data['seller_key'] = $paycoCfg['paycoSellerKey'];
		$payco_point_data['ordno'] = $post_ordno;
		$payco_point_data['cancelTotalAmt'] = ($cancel_delivery['total_cancel_price'] + $cancel_delivery['total_cancel_delivery_price']) - $post_repay_fee;

		$item_data = $cancel_delivery['item'];

		$res = $paycoApi->request('cancel_mileage', $payco_point_data);//������ ����Ʈ ������� ������ ����
		$res = json_decode($res, true);

		if($res['code'] == '0' && $res['result']['canceledMileageAcmAmount'] > 0) {
			$msg[] = $arr_data['ordno'].' ('.date('Y:m:d H:i:s').')';
			$msg[] = '-----------------------------------';
			$msg[] = '������ ����Ʈ ������� �Ϸ�';
			$msg[] = '������� ���ϸ��� : '.$res['result']['canceledMileageAcmAmount'];
			$msg[] = '������� ���ϸ��� : '.$res['result']['remainingMileageAcmAmount'];
			$msg[] = '-----------------------------------';
		}
		else {
			$msg[] = $arr_data['ordno'].' ('.date('Y:m:d H:i:s').')';
			$msg[] = '-----------------------------------';
			$msg[] = '������ ����Ʈ ������� ���� ['.iconv('utf-8', 'euc-kr', $res['message']).']';
			$msg[] = '-----------------------------------';
		}

		$arr_cancel_item_sno = $payco->getCancelItem($post_payco_settle_type, $post_ordno, $post_sno, $item_data);

		if(!empty($arr_cancel_item_sno)) {//������ �ֹ�item ���º���
			### step �ڵ�� �߿����� �ʾ� 1�� ����ó����
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
				$msg[] = '��һ��� ���漺��';
				$msg[] = '-----------------------------------';
			}
			else {
				$msg[] = $arr_data['ordno'].' ('.date('Y:m:d H:i:s').')';
				$msg[] = '-----------------------------------';
				$msg[] = '��һ��� ������� ['.iconv('utf-8', 'euc-kr', $res['msg']).']';
				$msg[] = '-----------------------------------';
			}
		}

		if($res['code'] == '0' || $res['code'] == '000') {
			$cancel_data['ordno'] = $post_ordno;//�ֹ���ȣ
			$cancel_data['cancelTotalAmt'] = $cancel_delivery['total_cancel_price'] + $cancel_delivery['total_cancel_delivery_price'];//(��һ�ǰ�ݾ� + ��һ�ǰ��ۺ�)
			$cancel_data['cancelTotalFeeAmt'] = $post_repay_fee;//ȯ�Ҽ�����

			$payco_coupon_data['payco_firsthand_refund'] = 'Y';//����ȯ�� ����

			$payco = &load_class('payco','payco');
			$payco->paycoCancel($cancel_data, $post_sno, $post_part, $msg, $payco_coupon_data);

			### �ֹ���ҿϷ� ��ۺ� ����/���� ����
			$orderDeliveryItem = &load_class('orderDeliveryItem','orderDeliveryItem');
			$orderDeliveryItem->update_delivery_data($cancel_delivery['delivery']);

			$rtn['code'] = '000';
		}
		else {
			$rtn['code'] = '999';
			//������ ��һ��� ���� ���� �α� ����
			$payco->paycoCancelFailLog($post_ordno, $msg);
		}

		$rtn['msg'] = nl2br(implode("\n", $msg));
		echo $json->encode($rtn);
		exit;
	}
	else {
		### ������ ������� ###

		### ��Ұ��ɿ��� ��ȸ START ###
		$res = $paycoApi->request('order_cancel_yn', $arr_data);
		$arr_cancel_check = json_decode($res,true);
		### ��Ұ��ɿ��� ��ȸ END ###

		if($arr_cancel_check['cancel_yn'] === 'Y') {

			### ���հ��� ������ ���� START ###
			/*
			 * $tax[taxall] => 3000		//������ǰ�ݾ�
			 * $tax[taxfree] => 3000	//�鼼��ǰ�ݾ�
			 * $tax[tax] => 2728		//������ǰ�ݾ�(�ΰ�������)
			 * $tax[vat] => 272			//�ΰ���
			*/
			$tax = $order->getCancelItemTaxWithSno($post_sno);

			if(isset($tax['code']) && isset($tax['msg'])) {
				$msg[] = $post_ordno.' ('.date('Y:m:d H:i:s').')';
				$msg[] = '-----------------------------------';
				$msg[] = '������ ȯ�� ����';
				$msg[] = '��ҿ�û�ݾ� : '.number_format($arr_data['cancelTotalAmt']);
				$msg[] = '��ҺҰ����� : '.$tax['msg'];
				$msg[] = '-----------------------------------';

				$rtn['msg'] = nl2br(implode("\n", $msg));
				echo $json->encode($rtn);
				exit;
			}

			$minus_delivery_tax = 0;
			$munus_delivery_vat = 0;
			$delivery_tax = 0;
			$delivery_vat = 0;

			// ������ ��Ұ��� ��� ���հ����� ���ԵǾ� �ִ� ��ۺ� ����
			if($orderDeliveryItem->checkLastCancel($post_sno) === true) {
				$minus_delivery = $order->getDeliveryFee();
				$minus_delivery_tax = floor($minus_delivery / 1.1);
				$munus_delivery_vat = $minus_delivery - $minus_delivery_tax;
			}

			// ��ۺ� ���հ��� ���� ���
			if($cancel_delivery['total_cancel_delivery_price'] > 0) {
				$delivery_tax = floor($cancel_delivery['total_cancel_delivery_price'] / 1.1);
				$delivery_vat = $cancel_delivery['total_cancel_delivery_price'] - $delivery_tax;
			}

			$arr_data['totalCancelTaxfreeAmt'] = $tax['taxfree'];//�鼼�ݾ�
			$arr_data['totalCancelTaxableAmt'] = $tax['tax'] + $delivery_tax - $minus_delivery_tax;//�����ݾ�
			$arr_data['totalCancelVatAmt'] = $tax['vat'] + $delivery_vat - $munus_delivery_vat;//�ΰ���

			$tax_total = $arr_data['totalCancelTaxfreeAmt'] + $arr_data['totalCancelTaxableAmt'] + $arr_data['totalCancelVatAmt'];

			if($tax_total != $arr_data['cancelTotalAmt']) {
				$msg[] = $post_ordno.' ('.date('Y:m:d H:i:s').')';
				$msg[] = '-----------------------------------';
				$msg[] = '���� ���հ��� �ݾ��� �ٸ��ϴ�.';
				$msg[] = '��ҿ�û�ݾ� : '.number_format($arr_data['cancelTotalAmt']);
				$msg[] = '���հ����ݾ� : '.number_format($tax_total);
				$msg[] = '�鼼�ݾ� : '.number_format($arr_data['totalCancelTaxfreeAmt']);
				$msg[] = '�����ݾ� : '.number_format($arr_data['totalCancelTaxableAmt']);
				$msg[] = '�ΰ��� : '.number_format($arr_data['totalCancelVatAmt']);
				$msg[] = '-----------------------------------';

				$rtn['msg'] = nl2br(implode("\n", $msg));
				echo $json->encode($rtn);
				exit;
			}
			### ���հ��� ������ ���� END ###

			if($arr_cancel_check['price'] == ($arr_data['cancelTotalAmt'] - $arr_data['cancelTotalFeeAmt'])) {

				### ������ ���� ���� ��ȿ�� üũ START ###
				if($order->offsetGet('settlekind') == 'c' && $order->offsetGet('payco_coupon_use_yn') == 'Y' && $order->offsetGet('payco_coupon_repay') == 'N') {//������� �� ��ҵ��� ���� ���
					if($order->offsetGet('payco_firsthand_refund') == 'Y') {
						//����ȯ�ҵ� �̷��� �ִ� ��� ���ó�� ����
						$msg[] = $post_ordno.' ('.date('Y:m:d H:i:s').')';
						$msg[] = '-----------------------------------';
						$msg[] = '����ȯ�ҵ� �̷��� �־� ī����Ұ� �Ұ��մϴ�.';
						$msg[] = '-----------------------------------';

						$rtn['msg'] = nl2br(implode("\n", $msg));
						echo $json->encode($rtn);
						exit;
					}

					if($order->offsetGet('payco_coupon_price') > ($arr_data['cancelTotalAmt'] - $arr_data['cancelTotalFeeAmt'])) {
						//�����ݾ׺��� ����Ϸ��� �ݾ��� ���� ��� ����ȯ���ؾ� ��
						$msg[] = $post_ordno.' ('.date('Y:m:d H:i:s').')';
						$msg[] = '-----------------------------------';
						$msg[] = '������� �ݾ׺��� ����Ϸ��� �ݾ��� �۽��ϴ�.';
						$msg[] = '[����ȯ�� ���]';
						$msg[] = '-----------------------------------';

						$rtn['msg'] = nl2br(implode("\n", $msg));
						echo $json->encode($rtn);
						exit;
					}
				}
				### ������ ���� ���� ��ȿ�� üũ END ###

				### ������ ���ó�� START ###
				$json_res = $paycoApi->request($post_mode, $arr_data);
				$res = json_decode($json_res, true);
				### ������ ���ó�� END ###

				### ������ ��Ұ��ó�� START ###
				if($res['code'] == '000') {
					$msg[] = $arr_data['ordno'].' ('.date('Y:m:d H:i:s').')';
					$msg[] = '-----------------------------------';
					$msg[] = '������ ȯ�� ����';
					$msg[] = '��ҳ�����ȣ : '.$res['cancelTradeSeq'];
					$msg[] = '��ҿ�û�ݾ� : '.number_format($res['totalCancelPaymentAmt']);
					$msg[] = '��Ҹ鼼�ݾ� : '.$arr_data['totalCancelTaxfreeAmt'];
					$msg[] = '��Ұ����ݾ� : '.$arr_data['totalCancelTaxableAmt'];
					$msg[] = '��Һΰ��� : '.$arr_data['totalCancelVatAmt'];
					$msg[] = '-----------------------------------';

					if($order->offsetGet('settlekind') == 'c' && $order->offsetGet('payco_coupon_use_yn') == 'Y' && $order->offsetGet('payco_coupon_repay') == 'N') {
						if(isset($res['cancelPaymentDetails'])) {
							foreach($res['cancelPaymentDetails'] as $cancel_payment_detail) {
								switch($cancel_payment_detail['paymentMethodCode']) {
									case '75' ://������ ����(�����̿�����)
									case '76' ://ī�� ����
									case '77' ://������ ����
										//����Ϸ��� �ݾ��� ������ ���� ���ݾ׺��� ū ��쿡�� ���ó��
										$payco_coupon_data['payco_coupon_repay'] = 'Y';//����ȯ�ҿ���
										$msg[] = $post_ordno.' ('.date('Y:m:d H:i:s').')';
										$msg[] = '-----------------------------------';
										$msg[] = '������ ������� �Ϸ�';
										$msg[] = '������ �����ݾ� : '.number_format($cancel_payment_detail['cancelPaymentAmt']).'��';
										$msg[] = '-----------------------------------';
										break;
								}
							}
						}
					}

					### �ֹ���ҿϷ� ����ó��
					$payco->paycoCancel($arr_data, $post_sno, $post_part, $msg, $payco_coupon_data);

					### �ֹ���ҿϷ� ��ۺ� ����/���� ����
					$orderDeliveryItem = &load_class('orderDeliveryItem','orderDeliveryItem');
					$orderDeliveryItem->update_delivery_data($cancel_delivery['delivery']);

					$rtn['code'] = '000';
				}
				else {
					$rtn['code'] = '999';

					$msg[] = $post_ordno.' ('.date('Y:m:d H:i:s').')';
					$msg[] = '-----------------------------------';
					$msg[] = '������ ȯ�� ����';
					$msg[] = '��ҿ�û�ݾ� : '.number_format($arr_data['cancelTotalAmt']);
					if(isset($res['msg'])) $msg[] = 'Payco ��ҺҰ����� : '.iconv('utf-8', 'euc-kr', $res['msg']);
					$msg[] = '-----------------------------------';
				}
				### ������ ��Ұ��ó�� END ###
			}
			else {
				### ��ҿ����ݾװ� ��ұݾ��� �ٸ� ���
				$rtn['code'] = '999';

				$text_msg = 'Payco ��ҺҰ����� : '.iconv('utf-8', 'euc-kr', $arr_cancel_check['cancelImpossibleReason']);
				if($arr_cancel_check['pgCancelPossibleAmt'] > 0) $text_msg .= ' ��Ұ��ɱݾ� : '.$arr_cancel_check['pgCancelPossibleAmt'];

				$msg[] = $post_ordno.' ('.date('Y:m:d H:i:s').')';
				$msg[] = '-----------------------------------';
				$msg[] = '������ ȯ�� ����';
				$msg[] = '��ҿ�û�ݾ� : '.number_format($arr_data['cancelTotalAmt']);
				$msg[] = $text_msg;
				$msg[] = '-----------------------------------';
			}
		}
		else {
			### ��ҺҰ� ���� ó�� ###
			$rtn['code'] = '999';

			$msg[] = $post_ordno.' ('.date('Y:m:d H:i:s').')';
			$msg[] = '-----------------------------------';
			$msg[] = '������ ȯ��üũ ����';
			$msg[] = '��ҿ�û�ݾ� : '.number_format($arr_data['cancelTotalAmt']);
			if(isset($arr_cancel_check['cancelImpossibleReason'])) $msg[] = 'Payco ��ҺҰ����� : '.iconv('utf-8', 'euc-kr', $arr_cancel_check['cancelImpossibleReason']);
			if(isset($arr_cancel_check['msg'])) $msg[] = 'Payco ��ҺҰ����� : '.iconv('utf-8', 'euc-kr', $arr_cancel_check['msg']);
			$msg[] = '-----------------------------------';
		}
	}

	//ȯ�ҽ��� �α� ����
	if($rtn['code'] == '999') $payco->paycoCancelFailLog($post_ordno, $msg);

	$rtn['msg'] = nl2br(implode("\n", $msg));
	echo $json->encode($rtn);
	exit;
}
?>