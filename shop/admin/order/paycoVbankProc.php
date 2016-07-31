<?php
/*********************************************************
* ���ϸ�     :  paycoVbankProc.php
* ���α׷��� :  ������ ������� ���� ��� ���� ���θ�ó�� API
**********************************************************/
include "../lib.php";
include "../../conf/config.php";
include "../../lib/paycoApi.class.php";

if(!$paycoCfg && is_file(dirname(__FILE__) . '/../../conf/payco.cfg.php')){
	include dirname(__FILE__) . '/../../conf/payco.cfg.php';
}

$post_mode = $_POST['mode'];//mode
$post_ordno = $_POST['ordno'];//�ֹ���ȣ
$post_sno = $_POST['sno'];//���sno
$post_part = $_POST['part'];//�κ���ҿ��� N = ��ü���, Y = �κ����
$post_repay_fee = $_POST['repayfee'];//ȯ�Ҽ�����
$post_repay_point = $_POST['repay_point'];// ȯ���Ϸ��� ������ ����Ʈ
$post_payco_settle_type = $_POST['payco_settle_type'];//������ �ֹ�����(checkout=������, easypay=�������)
$post_overlap_fee = $_POST['overlap_fee'];// ������ ����Ʈ ���� ȯ�ұݾ�

$orderDeliveryItem = &load_class('orderDeliveryItem','orderDeliveryItem');

if($post_ordno) $orderDeliveryItem->ordno = $post_ordno;
else $orderDeliveryItem->ordno = $post_ordno;
$cancel_delivery = $orderDeliveryItem->cancel_delivery($post_sno);

$payco = &load_class('payco','payco');
if($payco->checkCancelYn($post_sno) === true) {
	$rtn['code'] = '555';
	$rtn['msg'] = '�̹� ������ ������Ұ� �Ϸ�� �ֹ��Դϴ�.';

	$json = &load_class('json','Services_JSON');
	echo $json->encode($rtn);
	exit;
}

if($post_part == 'Y') {//�κ����
	$arr_data['seller_key'] = $paycoCfg['paycoSellerKey'];
	$arr_data['ordno'] = $post_ordno;
	$arr_data['cancelTotalAmt'] = $post_repay_point + abs($post_overlap_fee);// ȯ���Ϸ��� ������ ����Ʈ
	$arr_data['cancelTotalFeeAmt'] = abs($post_overlap_fee);
	$arr_data['payco_settle_type'] = $post_payco_settle_type;

	$goods_cnt = 0;

	foreach($cancel_delivery['item'] as $tmp_item) {
		if(!$tmp_item['delivery']) $goods_cnt++;
	}

	$arr_data['orderProducts'] = $cancel_delivery['item'];
}
else {//��ü���
	// ȯ�Ҽ������ ��ǰ�ݾ��� ���� ���� �� ������ �ݾ��� �����Ѵ�.
	if($post_overlap_fee < 0) {
		//��ǰ�ݾ׺��� ȯ�Ҽ����ᰡ ���� ��� $post_overlap_fee ���� ������ ����Ʈ���� ���� ȯ�� �������̴�.
		$cancelTotalAmt = $post_repay_point;
		$cancelTotalFeeAmt = abs($post_overlap_fee);
	}
	else {
		//��ǰ�ݾ׺��� ȯ�Ҽ����ᰡ ���� ��� ȯ�ҵ� ������ ����Ʈ���� ȯ�Ҽ������ ����.
		$cancelTotalAmt = $post_repay_point;
		$cancelTotalFeeAmt = '0';
	}

	$arr_data['seller_key'] = $paycoCfg['paycoSellerKey'];
	$arr_data['ordno'] = $post_ordno;
	$arr_data['cancelTotalAmt'] = $cancelTotalAmt;// ȯ���Ϸ��� ������ ����Ʈ
	$arr_data['cancelTotalFeeAmt'] = $cancelTotalFeeAmt;
	$arr_data['payco_settle_type'] = $post_payco_settle_type;
}

switch($post_mode) {
	case 'order_cancel' :
		### ������ ����Ʈ ȯ�Ұ��ɱݾ� Ȯ�� START ###
		$paycoApi = &load_class('paycoApi','paycoApi');
		$res = $paycoApi->request('order_cancel_yn', $arr_data);
		$tmp_res = json_decode($res, true);
		### ������ ����Ʈ ȯ�Ұ��ɱݾ� Ȯ�� END ###

		if($tmp_res['code'] !== '000') {
			$rtn['code'] = '999';

			$msg[] = $post_ordno.' ('.date('Y:m:d H:i:s').')';
			$msg[] = '-----------------------------------';
			$msg[] = '������ ����Ʈ ȯ�ҽ���';
			$msg[] = '��ҿ�û�ݾ� : '.number_format($arr_data['cancelTotalAmt']);
			if(isset($tmp_res['cancelImpossibleReason'])) $msg[] = 'Payco ��ҺҰ����� : '.iconv('utf-8', 'euc-kr', $tmp_res['cancelImpossibleReason']);
			if(isset($tmp_res['msg'])) $msg[] = 'Payco ��ҺҰ����� : '.iconv('utf-8', 'euc-kr', $tmp_res['msg']);
			$msg[] = '-----------------------------------';
		}
		else {
			if(($arr_data['cancelTotalAmt'] - $arr_data['cancelTotalFeeAmt']) > $tmp_res['price']) {
				$rtn['code'] = '999';

				$msg[] = $post_ordno.' ('.date('Y:m:d H:i:s').')';
				$msg[] = '-----------------------------------';
				$msg[] = '������ ����Ʈ ȯ�ҽ���';
				$msg[] = '��ҿ�û�ݾ� : '.number_format($arr_data['cancelTotalAmt']);
				$msg[] = '��ҺҰ����� : ��Ұ����� �ݾ��� �ʰ��Ͽ����ϴ�. [��Ұ��ɱݾ� : '.number_format($tmp_res['price']).']';
				$msg[] = '-----------------------------------';
			}
			else {
				/*
				 * ������ ��������� ��� ���հ��� ��� ���� - 2015-05-04
				 * 1. ������� ��ҽ� ��ұݾ��� �������� �ʰ� ���¸� ������.
				 * 2. ������� + ������ ����Ʈ �ֹ��ÿ� ������ ����Ʈ�� ���μ����̱� ������ ���հ��� ����� �ʿ����
				*/

				//������ ����Ʈ ȯ��ó��
				$json_res = $paycoApi->request($post_mode, $arr_data);
				$res = json_decode($json_res, true);

				if($res['code'] == '000') {
					$msg[] = "\n\n".$arr_data['ordno'].' ('.date('Y:m:d H:i:s').')';
					$msg[] = '-----------------------------------';
					$msg[] = '������ ����Ʈ ȯ�Ҽ���';
					$msg[] = '��ҳ�����ȣ : '.$res['cancelTradeSeq'];
					$msg[] = '�� ��ұݾ� : '.$res['totalCancelPaymentAmt'];
					$msg[] = '-----------------------------------';

					### ������ ����Ʈ ȯ�ҿϷ� ó��
					$query = "update ".GD_ORDER." set settlelog=concat(ifnull(settlelog,''),'".implode("\n",$msg)."'), payco_use_point_repay = payco_use_point_repay + ".$res['totalCancelPaymentAmt']." where ordno='".$arr_data['ordno']."'";
					$db -> query($query);

					if($post_overlap_fee > 0) $post_overlap_fee = 0;
					else $post_overlap_fee = abs($post_overlap_fee);

					// ȯ��ó���� ������ ����Ʈ ����
					// ������ ����Ʈ ȯ�� �� pgcancel�� p�� �����Ѵ�.
					$query2 = "update ".GD_ORDER_CANCEL." set rpayco_point = rpayco_point + ".$res['totalCancelPaymentAmt'].", rfee=".$post_repay_fee.", rpayco_point_fee=".$post_overlap_fee.", pgcancel='p' where sno='".$post_sno."'";
					$db -> query($query2);

					### �ֹ���ҿϷ� ��ۺ� ����/���� ����
					$orderDeliveryItem = &load_class('orderDeliveryItem','orderDeliveryItem');
					$orderDeliveryItem->update_delivery_data($cancel_delivery['delivery']);

					### ���ϸ��� ������� �� ��һ��� ���� ����

					// ������ ����Ʈ ������� ������
					$payco_point_data['seller_key'] = $paycoCfg['paycoSellerKey'];
					$payco_point_data['ordno'] = $post_ordno;
					$payco_point_data['cancelTotalAmt'] = ($cancel_delivery['total_cancel_price'] + $cancel_delivery['total_cancel_delivery_price']) - $post_repay_fee;

					$item_data = $cancel_delivery['item'];

					$paycoApi = &load_class('paycoApi','paycoApi');
					$res = $paycoApi->request('cancel_mileage', $payco_point_data);//������ ����Ʈ ������� ������ ����
					$res = json_decode($res, true);

					if($res['code'] == '0' && $res['canceledMileageAcmAmount'] > 0) {
						$msg[] = "\n\n".$arr_data['ordno'].' ('.date('Y:m:d H:i:s').')';
						$msg[] = '-----------------------------------';
						$msg[] = '������ ����Ʈ ������� �Ϸ�';
						$msg[] = '������� ���ϸ��� : '.$res['canceledMileageAcmAmount'];
						$msg[] = '������� ���ϸ��� : '.$res['remainingMileageAcmAmount'];
						$msg[] = '-----------------------------------';
					}
					else {
						$msg[] = $arr_data['ordno'].' ('.date('Y:m:d H:i:s').')';
						$msg[] = '-----------------------------------';
						$msg[] = '������ ����Ʈ ������� ���� ['.iconv('utf-8', 'euc-kr', $res['message']).']';
						$msg[] = '-----------------------------------';
					}

					//�ֹ���� ���� ������ ��ȸ
					$arr_cancel_item_sno = $payco->getCancelItem($post_payco_settle_type, $post_ordno, $post_sno, $item_data);

					if(!empty($arr_cancel_item_sno)) {//������ �ֹ�item ���º���
						### step �ڵ�� �߿����� �ʾ� 1�� ����ó����
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
							$msg[] = '��һ��� ���漺��';
							$msg[] = '-----------------------------------';
						}
						else {
							$rtn['code'] = '999';

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
					}
					else {
						//������ ��һ��� ���� ���� �α� ����
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
					$msg[] = '������ ����Ʈ ȯ�ҽ���';
					$msg[] = '��ҿ�û�ݾ� : '.number_format($arr_data['cancelTotalAmt']);
					$msg[] = 'Payco ��ҺҰ����� : '.iconv('utf-8', 'euc-kr', $res['msg']);
					$msg[] = '-----------------------------------';
				}
			}
		}

		$rtn['msg'] = nl2br(implode("\n", $msg));

		$json = &load_class('json','Services_JSON');
		echo $json->encode($rtn);
		exit;

		break;


	case 'cancel_status' ://��һ��� ����

		// ������ ����Ʈ ������� ������
		$payco_point_data['seller_key'] = $paycoCfg['paycoSellerKey'];
		$payco_point_data['ordno'] = $post_ordno;
		$payco_point_data['cancelTotalAmt'] = ($cancel_delivery['total_cancel_price'] + $cancel_delivery['total_cancel_delivery_price']) - $post_repay_fee;

		$item_data = $cancel_delivery['item'];

		$paycoApi = &load_class('paycoApi','paycoApi');
		$res = $paycoApi->request('cancel_mileage', $payco_point_data);//������ ����Ʈ ������� ������ ����
		$res = json_decode($res, true);

		if($res['code'] == '0' && $res['canceledMileageAcmAmount'] > 0) {
			$msg[] = $arr_data['ordno'].' ('.date('Y:m:d H:i:s').')';
			$msg[] = '-----------------------------------';
			$msg[] = '������ ����Ʈ ������� �Ϸ�';
			$msg[] = '������� ���ϸ��� : '.$res['canceledMileageAcmAmount'];
			$msg[] = '������� ���ϸ��� : '.$res['remainingMileageAcmAmount'];
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

			$paycoApi = &load_class('paycoApi','paycoApi');
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

		$json = &load_class('json','Services_JSON');

		$rtn['msg'] = nl2br(implode("\n", $msg));
		echo $json->encode($rtn);
		exit;

		break;
}
?>