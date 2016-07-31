<?php
/*********************************************************
* ���ϸ�     :  paycoCancel.php
* ���α׷��� :  ������ �������(�ſ�ī��,�޴���,����������Ʈ)
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
		//ȯ�ҿϷ� �� �߰�Ȯ�� �޼���
		$repay_add_msg = '';
		if($get_remoney > 0) $arr_repay_add_msg[] = '\n\n��ȯ�ҿϷ� ó���� ������ ����� ������ �� '.number_format($get_remoney).'���� �ǵ����ݴϴ�.';
		if(isset($arr_repay_add_msg)) $repay_add_msg = implode('\n\n',$arr_repay_add_msg);

		//ȯ������ ������ ������ ����
		if(!$get_repayfee) $repayfee = 0;
		else $repayfee = $get_repayfee;

		$orderDeliveryItem = &load_class('orderDeliveryItem','orderDeliveryItem', $get_ordno);
		$payco = &load_class('payco','payco');
		$order = &load_class('order','order');
		$order->load($get_ordno);

		if($payco->checkCancelYn($get_sno) === true) {
			msg('�̹� ������ ������Ұ� �Ϸ�� �ֹ��Դϴ�.', -1);
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
			$coupon_text = '�� ������ ����('.number_format($order->offsetGet('payco_coupon_price')).'��)�� ���� �ֹ��Դϴ�.';

			// $firsthand_refund : ����ȯ�ҿ��� Y = ����ȯ��, N = ���������
			if($order->offsetGet('payco_firsthand_refund') == 'Y') {
				$firsthand_refund = 'Y';//����ȯ���� ������ �̷��� �ִ� ��� ����ȯ�Ҹ� ����
				$cancel_text = '�� ���� ��ҿ��� ����ȯ�ҵ� �ֹ��Դϴ�.';
			}
			else if($order->offsetGet('payco_coupon_repay') == 'Y') {//����ȯ���� ���� ���¿��� ������ ��ҵǾ� �ִ� ��� ������ �������
				$firsthand_refund = 'N';
				unset($coupon_text);
			}
			else if($order->offsetGet('payco_coupon_price') >= $cancel_price) $firsthand_refund = 'Y';//������Ұ� �ȵ� ���¿��� ���� ���ݾ׺��� ����Ϸ��� �ݾ��� ���ų� ������ ����ȯ��
			else $firsthand_refund = 'N';//�׿� ������ �������
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
		//�ֹ��� ���������� �ֹ���ü ��� ó���� ������� ó��
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
			$data['code'] = 9; // �ֹ� ��ҽ� ��� ���� �ڵ� (�⺻�� 9)

			### �ֹ� ��� ����
			chkCancel($get_ordno,$data);

			$arr_coulms[] = 'sno';
			$cancel_no = $payco->getOrderData($get_ordno, 'gd_order_cancel', 'fetch_true', $arr_coulms, 'order by sno desc limit 1');

			echo('<script>location.href = "./paycoCancel.php?ordno='.$get_ordno.'&sno='.$cancel_no['sno'].'";</script>');
			exit;
		}
		else {
			echo('<script>alert("�̹� �������/�Ϸ�� �ֹ���ǰ�� �ֽ��ϴ�.");parent.location.reload();</script>');
			exit;
		}
	}
?>
<!--ȯ�ҿϷ� ������-->
<form id="repay" method="post" action="./indb.php">
	<input type="hidden" name="mode" value="repay">
	<input type="hidden" name="bankcode[]" value="">
	<input type="hidden" name="bankaccount[]" value="">
	<input type="hidden" name="bankuser[]" value="">
	<input type="hidden" name="m_no[]" value="<?=$order->offsetGet('m_no')?>">
	<input type="hidden" name="sno[]" value="<?=$get_sno?>">
	<input type="hidden" name="ordno[]" value="<?=$get_ordno?>">
	<input type="hidden" name="repayfee[]" value=""><!--������-->
	<input type="hidden" name="repay[]" value=""><!--ȯ�ұݾ�-->
	<input type="hidden" name="chk[]" value="0"><!--����-->
	<input type="hidden" name="remoney[]" value="<?=$get_remoney?>"><!--ȯ��������-->
</form>

<link rel="styleSheet" href="../style.css">
<script type="text/javascript" src="../common.js"></script>
<script type="text/javascript" src="../prototype.js"></script>
<form id="frmIni" name="frmIni" onsubmit="return false;">
<input type="hidden" name="payco_settle_type" value="<?=$order->offsetGet('payco_settle_type')?>">
<input type="hidden" name="firsthand_refund" value="<?=$firsthand_refund?>">
<input type="hidden" name="payco_coupon_price" value="<?=$order->offsetGet('payco_coupon_price')?>">
<input type="hidden" name="mode" value="order_cancel" />
<input type="hidden" name="ordno" value="<?=$get_ordno?>" /> <!-- �ֹ���ȣ -->
<input type="hidden" name="sno" value="<?=$get_sno?>" /> <!-- ȯ��������ȣ -->
<input type="hidden" name="repay" value="<?=$get_repay?>" /> <!-- ȯ�������� �ݾ� -->
<input type="hidden" name="part" value="<?=$get_part?>" /> <!-- ȯ�������� �ݾ� -->
	<div class="subtitle">
		<div class="title title_top">������ <?=$r_settlekind[$order->offsetGet('settlekind')]?> ���/ȯ��<span></span></div>
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
				<th class="input_title r_space" align="left">��ǰ�ݾ�</th>
				<td class="input_area" align="right">
					<input type="hidden" name="total_cancel_price" value="<?=$total_cancel_price?>" class="input_text width_small" readonly /><?=$total_cancel_price?> ��
				</td>
				<td></td>
			</tr>

			<tr style="<?=$display['delivery']?>">
				<th class="input_title r_space" align="left">��ۺ�</th>
				<td class="input_area" align="right">
					<input type="hidden" name="total_cancel_delivery_price" value="<?=$total_cancel_delivery_price?>" class="input_text width_small" readonly /><?=$total_cancel_delivery_price?> ��
				</td>
				<td>
				</td>
			</tr>

			<tr>
				<th class="input_title r_space" align="left">ȯ�� ������</th>
				<td class="input_area" align="right">
					<input type="text" name="repayfee" value="<?=$repayfee?>" onblur="price_calculate();btnType();" class="input_text width_small" style="width:60px;text-align:right;" /> ��
				</td>
				<td>
				<ul style="margin-left:-25px;">
					<li>ȯ�ҽ� �߻��Ǵ� �ݼۺ�� �� ��Ÿ ������ ���� ���Ͻ� �� �ֽ��ϴ�.</li>
				</ul>
				</td>
			</tr>

			<tr>
				<th class="input_title r_space" align="left">�� ��ҿ����ݾ�</th>
				<td class="input_area" align="right">
					<span id="text_cancel_price"><?=$cancel_price?></span> ��
					<input type="hidden" name="cancel_price" value="<?=$cancel_price?>" class="input_text width_small" />
				</td>
				<td>
				<ul style="margin-left:-25px;">
					<li>�� ��ҿ����ݾ� = (��ǰ�ݾ� + ��ۺ�) - ȯ�Ҽ�����</li>
				</ul>
				</td>
			</tr>
		</table>

		<!--����ȯ��-->
		<div id="fh_refund" style="<?=$display['fh_refund']?>">
			<div style="margin:3px;padding:5px;">
				<div style="color:red;">�� �� ��ҿ����ݾ��� ���� ������ �����ݾ׺��� �۾� ����ȯ�ҷ� ó���ؾ��մϴ�.</div>
				<div style="padding-left:13px;color:#B70000;">- ���� ������ �ݾ׺��� ����� �����ݾ��� ������ ������ ī����Ұ� �Ұ����ϸ� ����ȯ�ҷ� ó���ؾ��մϴ�.</div>
				<div style="padding-left:13px;color:#B70000;">- ������ ����Ʈ�� ���� �ֹ����� ����ȯ�ҽ� ������ ����Ʈ�� �Բ� ����ȯ�ҷ� ó���ؾ��մϴ�.</div>
				<div style="padding-left:13px;color:#B70000;">- ����ȯ�ҷ� ó���� ������ ����Ʈ�� ������ ��Ʈ�ʼ��Ϳ��� ������� �� �ֽ��ϴ�.</div>
				<div style="color:red;">�� �� �� ����ȯ�ҷ� ó���� �ֹ��� �߰� ��Ҵ� ��� ����ȯ�ҷ� ó���ؾ��մϴ�.</div>
			</div>

			<div style="margin-top:10px;margin-bottom:10px;text-align:center;">
				<input type="button" onClick="javascript:formChk(this);" style="width:190px;height:27px;"value="����ȯ�ҿϷ�" />
			</div>
		</div>

		<!--���API-->
		<div id="cd_refund" style="<?=$display['cd_refund']?>">
			<div style="margin-top:10px;margin-bottom:10px;text-align:center;">
				<input type="button" onClick="javascript:formChk(this);" style="width:190px;height:27px;"value="�������/ȯ��" />
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
						if(confirm("������ ������Ұ� �Ϸ�Ǿ����ϴ�.\nȯ�ҿϷ�ó���� �Ͻðڽ��ϱ�?<?=$repay_add_msg?>")) {
							document.getElementsByName("repayfee[]")[0].value = repayfee;//������
							document.getElementsByName("repay[]")[0].value = cancel_price;//ȯ�ұݾ�

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
			alert("ȯ�� ������� ���ڸ� �Է��� �ֽñ� �ٶ��ϴ�.");
			document.getElementsByName("repayfee")[0].value = 0;
			return false;
		}
		else if(parseInt(repayfee) < 0) {
			alert("ȯ�� ������� 0�� ���Ϸ� �Է��� �� �����ϴ�.");
			document.getElementsByName("repayfee")[0].value = 0;
			return false;
		}

		if(parseInt(settle_price) < parseInt(repayfee)) {
			alert("ȯ�� ������� �����ݾ��� �ʰ��� �� �����ϴ�.(" + settle_price + " ��)");
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