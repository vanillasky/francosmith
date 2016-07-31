<?php
/*********************************************************
* ���ϸ�     :  paycoPartCancelVbank.php
* ���α׷��� :  ������ ���� �κ����(�������/������ü)
**********************************************************/
include "../lib.php";
include "../../conf/config.php";

$charge = true;// true ����Ʈ ������ / false ����Ʈ ������
$get_ordno = $_GET['ordno'];
$get_sno = $_GET['sno'];
$get_repayfee = $_GET['repayfee'];
$get_repay = $_GET['repay'];
$get_part = $_GET['part'];
$get_remoney = $_GET['remoney'];
if($get_remoney == '') $get_remoney = 0;
$set_data['settlekind']['v'] = '�������';
$set_data['settlekind']['o'] = '������ü';

if($get_ordno) {
	if($get_sno) {
		//ȯ�ҿϷ� �� �߰�Ȯ�� �޼���
		$repay_add_msg = '';
		if($get_remoney > 0) $arr_repay_add_msg[] = '\n\n��ȯ�ҿϷ� ó���� ������ ����� ������ �� '.number_format($get_remoney).'���� �ǵ����ݴϴ�.';
		if(isset($arr_repay_add_msg)) $repay_add_msg = implode('\n\n',$arr_repay_add_msg);
		
		$orderDeliveryItem = &load_class('orderDeliveryItem','orderDeliveryItem', $get_ordno);
		$payco = &load_class('payco','payco');

		if($payco->checkCancelYn($get_sno) === true) {
			msg('�̹� ������ ������Ұ� �Ϸ�� �ֹ��Դϴ�.', -1);
			echo('<script>location.reload();</script>');
			exit;
		}

		$cancel_delivery = $orderDeliveryItem->cancel_delivery($get_sno);

		$now_cancel_price = $cancel_delivery['total_cancel_price'] + $cancel_delivery['total_cancel_delivery_price'];//������Ұ� ��ұݾ�(ȯ�Ҽ����� ����)

		if($orderDeliveryItem->checkLastCancel($get_sno) === true) {
			//������ ��Ұ��� ��� ��������, ������ ���� ����
			$now_cancel_price -= ($cancel_delivery['coupon']['m'] - $cancel_delivery['coupon']['f']) + $cancel_delivery['emoney'];
		}

		//�ֹ��ݾ� ������ȸ
		$arr_coulms[] = 'payco_use_point';
		$arr_coulms[] = '(payco_use_point - payco_use_point_repay) as prn_payco_use_point';
		$arr_coulms[] = 'payco_settle_type';
		$arr_coulms[] = 'm_no';
		$arr_coulms[] = 'settlekind';
		$arr_order_data = $payco->getOrderData($get_ordno, 'gd_order', 'fetch_true', $arr_coulms);
		unset($arr_coulms);

		//�ֹ����� ��ҵ� ��ۺ� ��ȸ�Ѵ�
		$cancel_data = $orderDeliveryItem->get_cancel_delivery($get_ordno);

		/*
		 * $order->getRealPrnSettleAmount()���� ������ ��ۺ� ���ԵǾ� ���� �ʾ� ��ҵ� ��ۺ� Ȯ�� �� �ǰ����ݾ� ���(ȯ�Ҽ������ �����Ǿ� �ִ�)
		*/
		$order = new order();
		$order->load($get_ordno);
		$real_settle_price = $order->getCancelCompletedRealSettleAmount();//�̳��� �������� �ݾ�

		$cash_bool = false;
		$point_bool = false;

		if($arr_order_data['payco_use_point'] > 0) {
			//�ֹ��� ������ ����Ʈ�� ����� ���
			if($arr_order_data['payco_use_point'] > ($real_settle_price - $now_cancel_price)) {
				//�� �����ݾ��� ���� ��Ұ��� ��������� �ֹ��� ����� ������ ����Ʈ���� ���� �ݾ��� ��� ������ ����Ʈ ȯ��

				if($real_settle_price > $arr_order_data['payco_use_point']) {
					//���ݰ����ݾװ� ������ ����Ʈ�� ���� ȯ���ϴ� ���
					$cash_bool = true;
					$point_bool = true;
				}
				else {
					//������ ����Ʈ�� ȯ���ϴ� ���
					$point_bool = true;
				}
			}
			else {
				//���ݰ����ݾ� ȯ��(����Ʈ ����� �ֹ�)
				$cash_bool = true;
			}
		}
		else {
			//���ݰ����ݾ� ȯ��(����Ʈ �̻�� �ֹ�)
			$cash_bool = true;
		}

		$cancel_cash_price = 0;
		$payco_point = 0;
		$cancel_price = 0;

		$cash_delivery = 0;
		$point_delivery = 0;

		if($cash_bool === true && $point_bool === false) {

			//���ݰ����ݾ� ȯ��
			$cancel_cash_price = $now_cancel_price;//��ҿ����ݾ�[����]
			$cash_delivery = $cancel_delivery['total_cancel_delivery_price'];//��ۺ�
			$cancel_price = $cancel_cash_price - $get_repayfee;//����ҿ����ݾ�

			$msg['alert'] = '��������� �����ڿ� ���۵Ǿ����ϴ�.\n���θ� ȯ�ҿϷ�ó���� �Ͻðڽ��ϱ�?';

			$display['point'] = 'display:none;';
			$display['etc2'] = 'display:none;';
			$display['etc3'] = 'display:none;';

		}
		else if($cash_bool === true && $point_bool === true) {

			//���ݰ����ݾװ� ������ ����Ʈ�� ���� ȯ��
			$cancel_cash_price = $real_settle_price - $arr_order_data['payco_use_point'];//��ҿ����ݾ�[����]
			$payco_point = $now_cancel_price - $cancel_cash_price;//��ҿ����ݾ�[������ ����Ʈ]
			$cancel_price = ($cancel_cash_price + $payco_point) - $get_repayfee;//�� ��ҿ����ݾ�

			// ��ۺ� ���
			if($cancel_cash_price > $cancel_delivery['total_cancel_price']) {//���ݰ����ݾ� > ��һ�ǰ�ݾ�
				$cash_delivery = $cancel_cash_price - $cancel_delivery['total_cancel_price'];
				if($cash_delivery != $cancel_delivery['total_cancel_price']) $point_delivery = $cancel_delivery['total_cancel_delivery_price'] - $cash_delivery;;
			}
			else {//���ݰ����ݾ� < ��һ�ǰ�ݾ�
				$point_delivery = $payco_point - ($cancel_delivery['total_cancel_price'] - $cancel_cash_price);
			}

			$msg['alert'] = '������ ����Ʈ ȯ��ó���� �Ϸ�Ǿ����ϴ�.\n���θ� ȯ�ҿϷ�ó���� �Ͻðڽ��ϱ�?';

			$display['etc1'] = 'display:none;';
			$display['etc2'] = 'display:none;';
			$display['btn'] = 'display:none;';

		}
		else if($cash_bool === false && $point_bool === true) {

			//������ ����Ʈ�� ȯ��
			$payco_point = $now_cancel_price;//��ҿ����ݾ�[������ ����Ʈ]
			$cancel_price = ($cancel_cash_price + $payco_point) - $get_repayfee;//�� ��ҿ����ݾ�

			$point_delivery = $cancel_delivery['total_cancel_delivery_price'];

			$msg['alert'] = '������ ����Ʈ ȯ��ó���� �Ϸ�Ǿ����ϴ�.\n���θ� ȯ�ҿϷ�ó���� �Ͻðڽ��ϱ�?';

			$display['cash'] = 'display:none;';
			$display['etc1'] = 'display:none;';
			$display['etc2'] = 'display:none;';
			$display['btn'] = 'display:none;';

		}

	}
	else {
		echo('<script>alert("�߸��� �����Դϴ�.");parent.location.reload();</script>');
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
	<input type="hidden" name="repayfee[]" value=""><!--������-->
	<input type="hidden" name="repay[]" value=""><!--ȯ�ұݾ�-->
	<input type="hidden" name="chk[]" value="0"><!--ȯ��������-->
	<input type="hidden" name="remoney[]" value="<?=$get_remoney?>"><!--ȯ��������-->
</form>

<input type="hidden" name="payco_settle_type" value="<?=$arr_order_data['payco_settle_type']?>">
<link rel="styleSheet" href="../style.css">
<script type="text/javascript" src="../common.js"></script>
<script type="text/javascript" src="../prototype.js"></script>
<form name="frmIni" onsubmit="return false;">
<input type="hidden" name="mode" value="" />
<input type="hidden" name="ordno" value="<?=$get_ordno?>" /> <!-- �ֹ���ȣ -->
<input type="hidden" name="sno" value="<?=$get_sno?>" /> <!-- ȯ��������ȣ -->
<input type="hidden" name="repay" value="<?=$get_repay?>" /> <!-- ȯ�������� �ݾ� -->
<input type="hidden" name="part" value="<?=$get_part?>" /> <!-- ȯ�������� �ݾ� -->
<input type="hidden" name="total_cancel_delivery_price" value="<?=$cancel_delivery['total_cancel_delivery_price']?>" /> <!-- ȯ�������� �ݾ� -->
	<div class="subtitle">
		<div class="title title_top">������ <?=$set_data['settlekind'][$arr_order_data['settlekind']]?> ���/ȯ��<span></span></div>
		<?if(empty($display['cash']) && empty($display['point'])) {?><!--����+����Ʈ ȯ��-->
		<div style="margin-top:-10px;padding-bottom:8px;padding-left:20px;"><font color="#003399">�� ������ <?=$set_data['settlekind'][$arr_order_data['settlekind']]?> ���/ȯ�ҽ� ������ ����Ʈ�� ������ ���Աݾ��� ����ȯ���� �ؾ� �մϴ�.</font></div>
		<?}?>
		<?if(empty($display['cash']) && empty($display['point']) === false) {?><!--����ȯ��-->
		<div style="margin-top:-10px;padding-bottom:8px;padding-left:20px;font-color:#003399;"><font color="#003399">�� ������ <?=$set_data['settlekind'][$arr_order_data['settlekind']]?> ���/ȯ�ҽ� ���Աݾ��� ����ȯ���� �ؾ� �մϴ�.</font></div>
		<?}?>
		<?if(empty($display['cash']) === false && empty($display['point'])) {?><!--����Ʈȯ��-->
		<div style="margin-top:-10px;padding-bottom:8px;padding-left:20px;font-color:#003399;"><font color="#003399">�� ������ <?=$set_data['settlekind'][$arr_order_data['settlekind']]?> ���/ȯ�ҽ� ������ ����Ʈ�� ȯ��ó���� �ؾ� �մϴ�.</font></div>
		<?}?>
	</div>

	<div id="cancelFail" class="input_wrap"></div>

	<div id="cancelFrom" class="input_wrap">
		<?if($cancel_price > $real_settle_price) {?>
		<div align=center style="margin:3px;padding:5px;color:red;border:2px dotted red;">
			���߿�! �� ȯ�ұݾ��� �� �����ݾ��� �ʰ��Ͽ����ϴ�. �ٽ� �ѹ� Ȯ���� �ּ���. 
		</div>
		<?}?>

		<div id="paycoProcess"></div>

		<table class=tb>
			<col width="20%" class="cellC">
			<col width="15%" class="cellL">
			<col width="65%" class="cellL">
			<tr style="<?=$display['cash']?>">
				<th class="input_title r_space" align="left">��ҿ����ݾ�<br>[���Աݾ�]</th>
				<td class="input_area" align="right">
					<input type="hidden" name="total_cancel_price" value="<?=$cancel_cash_price?>" style="width:60px;" readonly /><?=$cancel_cash_price?> ��
				</td>
				<td class="input_area">
					<ul style="margin-left:-25px;">
						<?if($cash_delivery > 0) {?> <li>��ۺ�(<?=$cash_delivery?>��)�� ���ԵǾ� �ֽ��ϴ�.</li> <?}?>
						<li><font color="#980000">��ҿ����ݾ�[���Աݾ�]�� ����ȯ���� �ؾ� �մϴ�.</font></li>
					</ul>
				</td>
			</tr>

			<tr style="<?=$display['point']?>">
				<th class="input_title r_space" align="left">��ҿ����ݾ�<br>[������ ����Ʈ]</th>
				<td class="input_area" align="right">
					<input type="hidden" name="repay_point" value="<?=$payco_point?>" style="width:60px;" readonly /><?=$payco_point?> ��
				</td>
				<td class="input_area">
					<ul style="margin-left:-25px;">
						<?if($point_delivery > 0) {?><li>��ۺ�(<?=$point_delivery?>��)�� ���ԵǾ� �ֽ��ϴ�.</li><?}?>
						<li><font color="#980000">������ ����Ʈ ȯ��ó���� �ʿ� �մϴ�.</font></li>
					</ul>
				</td>
			</tr>

			<tr>
				<th class="input_title r_space" align="left">ȯ�� ������</th>
				<td class="input_area" align="right">
					<input type="text" name="repayfee" value="<?=$get_repayfee?>" onblur="price_calculate();" style="width:60px;text-align:right;" class="input_text width_small" /> ��
				</td>
				<td class="input_area">
					<ul style="margin-left:-25px;">
					<li>ȯ�ҽ� �߻��Ǵ� �߼ۺ�� �� ��Ÿ ������ ���� ���Ͻ� �� �ֽ��ϴ�.</li>
					<ul>
				</td>
			</tr>

			<tr>
				<th class="input_title r_space" align="left">�� ��ҿ����ݾ�</th>
				<td class="input_area" align="right">
					<span id="text_cancel_price"><?=$cancel_price?></span> ��
					<input type="hidden" name="cancel_price" style="width:60px;" value="<?=$cancel_price?>" />
				</td>
				<td class="input_area">
					<ul style="margin-left:-25px;">
						<li style="<?=$display['etc1']?>">�� ��ҿ����ݾ� = ��ҿ����ݾ�[���Աݾ�] - ȯ�Ҽ�����</li>
						<li style="<?=$display['etc2']?>">�� ��ҿ����ݾ� = ��ҿ����ݾ�[������ ����Ʈ] - ȯ�Ҽ�����</li>
						<li style="<?=$display['etc3']?>">�� ��ҿ����ݾ� = (��ҿ����ݾ�[���Աݾ�] + ��ҿ����ݾ�[������ ����Ʈ]) - ȯ�Ҽ�����</li>
						<li>�� ��ҿ����ݾ� ��ŭ ������ ����Ʈ ������ ���ܵ˴ϴ�.</li>
					<ul>
				</td>
			</tr>
		</table>

		<div style="margin-top:10px;margin-bottom:10px;text-align:center;<?=$display['point']?>">
			<input type="button" onClick="javascript:cancelYn(this);" style="width:190px;height:27px;" value="������ ����Ʈ ȯ��ó��" />
		</div>

		<?if($cash_bool === true && $point_bool !== true) {?>
		<div style="margin-top:10px;margin-bottom:10px;text-align:center;">
			<input type="button" onClick="javascript:cancelStatus(this);" style="width:190px;height:27px;" value="����ȯ�ҿϷ�" />
		</div>
		<?}?>

		<div id="loading" style="margin-top:10px;margin-bottom:10px;text-align:center;display:none;"><img src="../img/loading40.gif" /></div>
	</div>
</form>

<script>
	var point = 0;

	//ȯ�ҿϷ� ó��
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
						// ����ȯ�ҿϷ��...����?
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
		var total_cancel_price = document.getElementsByName("total_cancel_price")[0].value;//��ҿ����ݾ�[����]
		var repay_point = document.getElementsByName("repay_point")[0].value;//��ҿ����ݾ�[������ ����Ʈ]
		var repayfee = document.getElementsByName("repayfee")[0].value;//ȯ�Ҽ�����
		if(point < 1) point = repay_point;
		else repay_point = point;

		if(total_cancel_price == "") total_cancel_price = 0;
		if(repay_point == "") repay_point = 0;
		if(repayfee == "") repayfee = 0;

		if(isNaN(repayfee)) {
			alert("ȯ�� ������� ���ڸ� �Է��� �ֽñ� �ٶ��ϴ�.");
			document.getElementsByName("repayfee")[0].value = 0;
			return false;
		}
		else if(parseInt(repayfee) < 0) {
			alert("ȯ�� ������� 0�� ���Ϸ� �Է��� �� �����ϴ�.");
			document.getElementsByName("repayfee")[0].value = 0;
			return false;
		}

		if((parseInt(total_cancel_price) + parseInt(repay_point)) < parseInt(repayfee)) {
			alert("ȯ�� ������� �����ݾ��� �ʰ��� �� �����ϴ�.(" + (parseInt(total_cancel_price) + parseInt(repay_point)) + " ��)");
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

	// ������ ����Ʈ ȯ��ó��
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