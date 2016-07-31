<?
	$location = "�ֹ����� > �������� ���";
	include "../_header.php";
	include "../../conf/config.pay.php";

	// ���� ������ ����

	function setSOUID($uid='') {
		global $_SESSION, $sess, $db;
		if(!$uid) {
			$_SESSION['uid'] = "sugi__".$sess['m_id']."__".time();
			$db->query("DELETE FROM ".GD_CART." WHERE uid LIKE 'sugi__".$sess['m_id']."__%' AND uid != '".$_SESSION['uid']."'");
		}

		return $_SESSION['uid'];
	}

	// getordno �Լ� �̵� (shop/lib/lib.func.php)
	$ordno = getordno();
	setSOUID();	// uid �� �����Ѵ�.

	/* ������ ���� */
		/* ��۹�� */
		$deli_type_key = explode("|", $set['r_delivery']['title']);
?>

<style type="text/css">
	/* self order step manual */
	.sosmArea { width:10px; padding:10px; background:#F6F6F6; }
	.sosmArea .sosmTitle { margin:0px 0px 5px 18px; color:#403F3F; font-weight:bold; font-size:12px; font-family:Dotum; }
	.sosmArea .sosmTitle span { color:#0084E1; }
	.sosmArea .sosmStepMenu { padding:10px; background:#F6F6F6; font-weight:bold; }
	.sosmArea .sosmStepArrow { padding:5px; font-weight:bold; color:#E46C0A; }
	.sosmAlert { padding-top:10px; font-size:11px; font-family:dotum; color:#E00021; }

	.stdTbl1 { width:500px; background:#E6E6E6;}
	.stdTbl2 { width:100%; background:#E6E6E6;}
	.stdTd1 { width:120px; color:#333333; background:#F6F6F6; font-weight:bold; }
	.stdTd2 { color:#000000; background:#FFFFFF; }
</style>

<script language="JavaScript">
	// �ֹ��� ���� �� �˻�
	function checkOrder() {
		// ���� ����
			document.getElementById('settleprice').value = uncomma(document.getElementById('orderTotalPrice').innerHTML.replace(" ��", ""));
		// �� �˻�
			if($('memType1').checked && !$('m_id').value) {
				alert("�ֹ��ڰ� ȸ���� ��� ȸ���� �˻��� �ּ���.");
				findMember(); return false;
			}

			var checkList = "email,phoneOrder0,phoneOrder1,phoneOrder2,mobileOrder0,mobileOrder1,mobileOrder2,m_zipcode0,m_zipcode1,m_address,m_address_sub,nameReceiver,phoneReceiver0,phoneReceiver1,phoneReceiver2,mobileReceiver0,mobileReceiver1,mobileReceiver2,zipcode0,zipcode1,address,bankAccount,bankSender"; // �ʼ��Է� ���� ��� [�޸�(,)�� �����Ͽ� �Է�]
			var checkListScript = ""; //
			checkList = checkList.split(",");

			for(i = 0; i < checkList.length; i++) {
				if(checkList[i] && !$(checkList[i]).value) {
					alert("'" + $(checkList[i]).title + "' ��(��) �Է��� �ֽñ� �ٶ��ϴ�.");
					$(checkList[i]).focus();
					return false;
				}
				if ($(checkList[i]).getAttribute("option")!=null) {
					if (!chkPatten($(checkList[i]), $(checkList[i]).getAttribute("option"))) return false;
				}
			}
	}

	// convert to Int : ���� �κ��� ��Ʈ������ ��ȯ
	function cvint(str) { return parseInt(uncomma(str.replace(" ��", ""))); }

	// ȸ�� ��ȸ�� ����
	function memberOptionToggle(st) {
		switch(st) {
			case "1" :
				$('m_id').disabled = false;
				$('m_id').style.backgroundColor = '#FFFFFF';
				$('memFindBtn').style.display = '';
				$('addrFindBtn').style.display = 'none';
				$('emoney').disabled = false;
				$('emoney').style.backgroundColor = '#FFFFFF';
				$('reserveInfo').style.display = '';
				$('cpnFindBtn').style.display = '';
				break;

			case "2" :
				$('m_id').disabled = true;
				$('m_id').style.backgroundColor = '#EEEEEE';
				$('memFindBtn').style.display = 'none';
				$('addrFindBtn').style.display = '';
				$('emoney').value = '0';
				$('emoney').disabled = true;
				$('emoney').style.backgroundColor = '#EEEEEE';
				$('reserveInfo').style.display = 'none';
				$('cpnFindBtn').style.display = 'none';

				$('m_id').value = $('nameOrder').value = $('email').value = $('phoneOrder0').value = $('phoneOrder1').value = $('phoneOrder2').value = $('mobileOrder0').value = $('mobileOrder1').value = $('mobileOrder2').value = $('m_zipcode0').value = $('m_zipcode1').value = $('m_address').value = $('m_road_address').value = $('m_div_road_address').innerHTML = $('div_road_address_sub').innerHTML = $('m_address_sub').value = '';
				break;
		}

		setPayInfo();
	}

	// ȸ�� �˻�
	function findMember() {
		var url = "../order/member_select.php?mode=selectMember&m_id=&skey=all&sval=";
		popup_return(url, "memberSelect", 350, 500, '', '', 1);
	}

	// ���־��� �ּ� �˻�(�̸�,�����ȣ1,�����ȣ2,�ּ�,���ּ�,���θ��ּ�,����ó1,����ó2,����ó3)
	function findFA(nmr, eml, zcd1, zcd2, zonecode, ad1, ad2, road_address, div_road_address, div_road_address_sub, phn1, phn2, phn3, mb1, mb2, mb3) {
		var url = "../order/favoriteAddress_select.php?";
		if(nmr) url += "&nmr=" + nmr;
		if(eml) url += "&eml=" + eml;
		if(zcd1) url += "&zcd1=" + zcd1;
		if(zcd2) url += "&zcd2=" + zcd2;
		if(zonecode) url += "&zonecode=" + zonecode;
		if(ad1) url += "&ad1=" + ad1;
		if(ad2) url += "&ad2=" + ad2;
		if(road_address) url += "&road_address=" + road_address;
		if(div_road_address) url += "&div_road_address=" + div_road_address;
		if(div_road_address_sub) url += "&div_road_address_sub=" + div_road_address_sub;
		if(phn1) url += "&phn1=" + phn1;
		if(phn2) url += "&phn2=" + phn2;
		if(phn3) url += "&phn3=" + phn3;
		if(mb1) url += "&mb1=" + mb1;
		if(mb2) url += "&mb2=" + mb2;
		if(mb3) url += "&mb3=" + mb3;
		popup2(url, 1000, 500, 1);
	}

	// �������� ����
	function setPayInfo() {
		// ��ǰ �ݾ� �հ�
			$("orderOriginalPrice").innerHTML = comma(document.getElementById("selfOrderGoods").contentWindow.document.fmList.originalPrice.value) + " ��";

		// ��ۺ�
			var deliveryParam = "";
			deliveryParam += "&zipcode=" + (($('zipcode0').value && $('zipcode1').value) ? $('zipcode0').value + "-" + $('zipcode1').value : "");
			deliveryParam += "&deliPoli=" + $('deliPoli').value;
			deliveryParam += "&coupon=" + $('coupon').value;
			deliveryParam += "&coupon_emoney=" + $('coupon_emoney').value;
			deliveryParam += "&emoney=" + $('emoney').value;
			deliveryParam += "&memID=" + $('m_id').value;
			deliveryParam += "&road_address=" + $('road_address').value;
			deliveryParam += "&address=" + $('address').value;
			exAjax("orderDeliveryPay", "orderDeliveryPay", 0, deliveryParam);

		// ��ǰ����
			exAjax("specialDC", "specialDC", 0, "&memID=" + $('m_id').value);

		// ȸ������
			if($('m_id').value) exAjax("memberDC", "memberDC", 0, "&memID=" + $('m_id').value);

		// ������ ����
			if($('memType1').checked) exAjax("reserveInfo", "reserveInfo", 0, deliveryParam);

		// �� �����ݾ�
			totalPriceCal();
	}

	// ���� ������ ���
	function totalPriceCal() {
		op = cvint(document.getElementById("selfOrderGoods").contentWindow.document.fmList.originalPrice.value);
		odp = cvint($("orderDeliveryPay").innerHTML);
		mdc = cvint($("memberDC").innerHTML);
		sdc = cvint($("specialDC").innerHTML);
		emn = cvint($("emoney").value);
		cp = cvint($("coupon").value);
		if (cp >= (op + odp - mdc - emn - sdc)) cp = op + odp - mdc - emn - sdc;
		$("orderTotalPrice").innerHTML = comma(op + odp - mdc - emn - cp - sdc) + " ��";
	}

	// ajax ȣ��
	function exAjax(mode, objID, objType, addQueryString) {
		if(!objType) objType = 0;
		var url = "./indb.self_order.php?uid=<?=$_SESSION['uid']?>&mode=" + mode + addQueryString;

		new Ajax.Request(url, {
			method: "get",
			onSuccess: function(transport) {
				var rtnFullStr = transport.responseText;
				if(!rtnFullStr) rtnFullStr = "0";

				switch(mode) {
					case "orderDeliveryPay" : rtnFullStr += " ��"; break;
				}

				if(objType == 0) $(objID).innerHTML = rtnFullStr;
				else $(objID).value = rtnFullStr;

				totalPriceCal();

				return true;
			},
			OnError: function() { return false; }
		});
	}

	// ������ ������ �ֹ��� ���� �ֱ�
	function copyInfo() {
		if($('copyInfoCheck').checked) {
			$('nameReceiver').value = $('nameOrder').value;

			$('phoneReceiver0').value = $('phoneOrder0').value;
			$('phoneReceiver1').value = $('phoneOrder1').value;
			$('phoneReceiver2').value = $('phoneOrder2').value;

			$('mobileReceiver0').value = $('mobileOrder0').value;
			$('mobileReceiver1').value = $('mobileOrder1').value;
			$('mobileReceiver2').value = $('mobileOrder2').value;

			$('zipcode0').value = $('m_zipcode0').value;
			$('zipcode1').value = $('m_zipcode1').value;
			$('zonecode').value = $('m_zonecode').value;
			$('address').value = $('m_address').value + " " + $('m_address_sub').value;
			if($('m_road_address').value != "") {
				$('road_address').value = $('m_road_address').value + " " + $('m_address_sub').value;
			}
		}
		else {
			$('nameReceiver').value = "";

			$('phoneReceiver0').value = "";
			$('phoneReceiver1').value = "";
			$('phoneReceiver2').value = "";

			$('mobileReceiver0').value = "";
			$('mobileReceiver1').value = "";
			$('mobileReceiver2').value = "";

			$('zipcode0').value = "";
			$('zipcode1').value = "";
			$('zonecode').value = "";
			$('address').value = "";
			$('road_address').value = "";
		}
	}

	// �ֹ��� ������ ���� üũ Ǯ��
	function unchkCopy() { $('copyInfoCheck').checked = false; }

	var my_emoney = 0;
	// ������ üũ
	function chk_emoney(obj) {
		if(!$('m_id').value && obj.value && parseInt(obj.value) > 0) { alert("�������� ����Ͻ÷��� ���� ȸ���� ������ �ּ���."); obj.value = '0'; return; }
		var form = document.orderForm;
		var max = '<?=$set['emoney']['max']?>';
		var min = '<?=$set['emoney']['min']?>';
		var hold = '<?=$set['emoney']['hold']?>';
		var limit = '<?=$set['emoney']['totallimit']?>';

		var delivery = cvint($('orderDeliveryPay').innerHTML);
		var goodsprice = cvint($('orderOriginalPrice').innerText);
		<? if($set['emoney']['emoney_use_range']) { ?>
		var erangeprice = goodsprice + delivery;
		<? } else { ?>
		var erangeprice = goodsprice;
		<? } ?>
		var max_base = erangeprice - cvint($('memberdc').innerHTML) - cvint($('coupon').value);
		var coupon = coupon_emoney = 0;
		if(form.coupon) coupon = cvint(form.coupon.value);
		if(form.coupon_emoney) coupon_emoney = cvint(form.coupon_emoney.value);
		max = getDcprice(max_base, max, <?=pow(10, $set['emoney']['cut'])?>);
		min = parseInt(min);

		if(max > max_base) max = max_base;
		if($('print_emoney_max') && $('print_emoney_max').innerHTML != comma(max)) $('print_emoney_max').innerHTML = comma(max);

		var emoney = uncomma(obj.value);
		if (emoney > my_emoney) { alert("���� ���� �������� " + my_emoney + "�� �Դϴ�."); emoney = my_emoney; }

		$('reserveInfo').innerHTML = " (���������� : " + comma(my_emoney) + " ��) �������� " + comma(min) + "������ " + comma(max) + "���� ����� �����մϴ�.";

		// �ߺ� ��� üũ
		var dup = <?=($set['emoney']['useduplicate'] == '1') ? "true" : "false"?>;
		if(my_emoney > 0 && emoney > 0 && (parseInt(coupon) > 0 || parseInt(coupon_emoney)) > 0 && !dup) {
			alert('�����ݰ� ���� ����� �ߺ�������� �ʽ��ϴ�.');
			emoney = 0;
		}
		if(my_emoney > 0 && emoney > 0 && limit > goodsprice) {
			alert("��ǰ �ֹ� �հ���� "+ comma(limit) + "�� �̻� �� ��� ����Ͻ� �� �ֽ��ϴ�.");
			emoney = 0;
		}
		if(my_emoney > 0 && emoney > 0 && my_emoney < hold) {
			alert("������������ "+ comma(hold) + "�� �̻� �� ��쿡�� ����Ͻ� �� �ֽ��ϴ�.");
			emoney = 0;
		}
		if (min && emoney > 0 && emoney < min) {
			alert("�������� " + comma(min) + "�� ���� " + comma(max) + "�� ������ ����� �����մϴ�");
			emoney = 0;
		} else if(max && emoney > max && emoney > 0) {
			if(emoney_max < min) {
				alert("�ֹ� ��ǰ �ݾ��� �ּ� ��� ������ " + comma(min) + "�� ����  �۽��ϴ�.");
				emoney = 0;
			} else {
				alert("�������� " + comma(min) + "�� ���� " + comma(max) + "�� ������ ����� �����մϴ�");
				emoney = max;
			}
		}

		obj.value = emoney;
		setPayInfo();
	}

	// ���� ���� �˾� ����
	function checkCouponPopup() {
		if($('m_id').value) popup2('../order/popup.self_order_coupon.php?tmpMemID=' + $('m_id').value, 600, 700, 1);
		else alert("ȸ���� �������ּ���.");
	}

	// ���� ���� Ǯ��
	function del_coupon() {
		document.orderForm.coupon.value = '0';
		document.orderForm.coupon_emoney.value = '0';

		$('del_coupon').style.visibility = "hidden";

		if(typeof document.getElementsByName('apply_coupon[]')[0] == 'object'){
			for(i=0;i<document.getElementsByName('apply_coupon[]').length;i++){
				document.getElementsByName('apply_coupon[]')[i].value = '';
			}
		}

		setPayInfo();
	}
</script>

<div class="title title_top">�������� ��� <span>���� ��ǰ �ֹ������� ��ڰ� ���� �Է��Ͽ� �ֹ� �ϴ� ����Դϴ�.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=order&no=34', 870, 800)"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2" /></a></div>

<div class="sosmArea">
<table cellpadding="0" cellspacing="0" border="0">
<tr>
	<td colspan="13" class="sosmTitle"><img src="../img/su_blt01.gif" align="absmiddle" />�������� <span>��� ����</span></td>
</tr>
<tr align="center">
	<td class="sosmStepMenu"><img src="../img/su_img01.gif" /></td>
</tr>
</table>
</div>
<table cellpadding="0" cellspacing="0" border="0">
<tr>
	<td colspan="13" class="sosmAlert">* �ۼ��� ���������� <b>�ֹ�����Ʈ</b>�� ���� <b>����������</b>�� <b>�ڵ����� ���</b>�˴ϴ�.</td>
</tr>
</table>

<div style="height:20px;"></div>

<!-- ��ǰ ���� ��� -->
<div><iframe src="about:blank" name="selfOrderGoods" id="selfOrderGoods" frameborder="0" width="100%" onload="setPayInfo()"></iframe></div>

<!-- �ֹ��� / ������ ���� -->
<div><form name="orderForm" method="post" action="../order/indb.self_order.php" onsubmit="return checkOrder();" target="ifrmHidden">
<input type="hidden" name="ordno" name="ordno" value="<?=$ordno?>" />
<input type="hidden" name="settleprice" id="settleprice" />
<input type="hidden" name="mode" id="mode" value="writeOrder" />
<input type="hidden" name="uid" id="uid" value="<?=$_SESSION['uid']?>" />
<table cellpadding="0" cellspacing="0" border="0">
<tr>
	<td width="50%" valign="top" style="padding-right:5px;">
		<table cellpadding="0" cellspacing="0" border="0" width="100%" style="margin-bottom:7px;">
		<tr valign="bottom">
			<td style="font-weight:bold; font-size:14px; font-family:dotum;"><img src="../img/titledot.gif" align="absbottom" style="margin-right:5px;" />�ֹ��� ����</td>
		</tr>
		</table>

		<table cellpadding="6" cellspacing="1" class="stdTbl1">
		<colgroup>
			<col class="stdTd1"><col class="stdTd2">
		</colgroup>
		<tr>
			<td>ȸ������</td>
			<td class="noline">
				<input type="radio" name="memType" id="memType1" value="1" onclick="memberOptionToggle('1')" checked /><label for="memType1">ȸ��</label>
				<input type="radio" name="memType" id="memType2" value="2" onclick="memberOptionToggle('2')" /><label for="memType2">��ȸ��</label>
			</td>
		</tr>
		<tr>
			<td>ȸ�� ID</td>
			<td><input type="type" name="m_id" id="m_id" value="" class="line" readonly /> <a href="javascript:;" onclick="findMember();" id="memFindBtn"><img src="../img/su_btn02.gif" align="absmiddle" /></a></td>
		</tr>
		<tr>
			<td>�ֹ��� �̸�</td>
			<td><input type="type" name="nameOrder" id="nameOrder" value="" class="line" /></td>
		</tr>
		<tr>
			<td>�̸���</td>
			<td><input type="type" name="email" id="email" class="line" style="width:250px" title="�̸���" /></td>
		</tr>
		<tr>
			<td>��ȭ��ȣ</td>
			<td>
				<input type="type" name="phoneOrder[]" id="phoneOrder0" size="4" class="line" title="�ֹ��� ��ȭ��ȣ" option=regNum label="�ֹ��� ��ȭ��ȣ" /> -
				<input type="type" name="phoneOrder[]" id="phoneOrder1" size="4" class="line" title="�ֹ��� ��ȭ��ȣ" option=regNum label="�ֹ��� ��ȭ��ȣ" /> -
				<input type="type" name="phoneOrder[]" id="phoneOrder2" size="4" class="line" title="�ֹ��� ��ȭ��ȣ" option=regNum label="�ֹ��� ��ȭ��ȣ" />
			</td>
		</tr>
		<tr>
			<td>�ڵ��� ��ȣ</td>
			<td>
				<input type="type" name="mobileOrder[]" id="mobileOrder0" value="<?=$data['mobileOrder'][0]?>" size="4" class="line" title="�ֹ��� �ڵ��� ��ȣ" option=regNum label="�ֹ��� �ڵ��� ��ȣ" /> -
				<input type="type" name="mobileOrder[]" id="mobileOrder1" value="<?=$data['mobileOrder'][1]?>" size="4" class="line" title="�ֹ��� �ڵ��� ��ȣ" option=regNum label="�ֹ��� �ڵ��� ��ȣ" /> -
				<input type="type" name="mobileOrder[]" id="mobileOrder2" value="<?=$data['mobileOrder'][2]?>" size="4" class="line" title="�ֹ��� �ڵ��� ��ȣ" option=regNum label="�ֹ��� �ڵ��� ��ȣ" />
			</td>
		</tr>
		<tr>
			<td>�ּ�</td>
			<td>
				<input type="type" name="m_zonecode" id="m_zonecode" value="<?=$data['m_zonecode']?>" size="5" class="line" readonly title="�ֹ��� �����ȣ" />
				( <input type="type" name="m_zipcode[]" id="m_zipcode0" value="<?=$data['m_zipcode'][0]?>" size="3" class="line" readonly title="�ֹ��� �����ȣ" /> -
				<input type="type" name="m_zipcode[]" id="m_zipcode1" value="<?=$data['m_zipcode'][1]?>" size="3" class="line" readonly title="�ֹ��� �����ȣ" /> )
				<a href="javascript:popup('../../proc/popup_address.php?gubun=m',500,432)"><img src="../img/btn_zipcode.gif" align="absmiddle" /></a>
				<a href="javascript:;" onclick="findFA('nameOrder', 'email', 'm_zipcode0', 'm_zipcode1', 'm_zonecode', 'm_address', 'm_address_sub', 'm_road_address', 'm_div_road_address', 'div_road_address_sub', 'phoneOrder0', 'phoneOrder1', 'phoneOrder2', 'mobileOrder0', 'mobileOrder1', 'mobileOrder2')" id="addrFindBtn" style="display:none;"><img src="../img/su_btn03.gif" align="absmiddle"></a><br />
				<input type="type" name="m_address" id="m_address" value="<?=$data['m_address'][0]?>" style="width:100%" class="line" readonly title="�ֹ��� �ּ�" /><br />
				<input type="type" name="m_address_sub" id="m_address_sub" value="<?=$data['m_address_sub'][1]?>" style="width:100%" onkeyup="SameAddressSub(this)" oninput="SameAddressSub(this)" class="line" title="�ֹ��� ���ּ�" /><br />
				<input type="hidden" name="m_road_address" id="m_road_address" value="<?=$data['m_road_address']?>" title="���θ��ּ�">
				<div style="padding:5px 5px 0 5px;font:12px dotum;color:#999;float:left;" id="m_div_road_address"><?=$data['m_road_address']?></div>
				<div style="padding:5px 0 0 1px;font:12px dotum;color:#999;" id="div_road_address_sub"><? if ($data['m_road_address']) { echo $data['m_address_sub']; } ?></div>
			</td>
		</tr>
		</table>
	</td>
	<td width="50%" valign="top" style="padding-left:5px;">
		<table cellpadding="0" cellspacing="0" border="0" width="100%" style="margin-bottom:7px;">
		<tr valign="bottom">
			<td style="font-weight:bold; font-size:14px; font-family:dotum;"><img src="../img/titledot.gif" align="absbottom" style="margin-right:5px;" />������ ����</td>
		</tr>
		</table>

		<table cellpadding="6" cellspacing="1" class="stdTbl1">
		<colgroup>
			<col class="stdTd1"><col class="stdTd2">
		</colgroup>
		<tr>
			<td>����� Ȯ��</td>
			<td class="noline">
				<input type="checkbox" name="copyInfoCheck" id="copyInfoCheck" value="1" onclick="copyInfo();setPayInfo();" /><label for="copyInfoCheck">�ֹ��� ������ �����մϴ�</label>
			</td>
		</tr>
		<tr>
			<td>��۹��</td>
			<td>
				<select name="deliPoli" id="deliPoli" onchange="setPayInfo()">
					<option value="0"><?=$set['delivery']['deliverynm']." [".$set['delivery']['deliveryType']." : ".(($set['delivery']['deliveryType'] == "����") ? number_format($set['delivery']['default'])."��" : $set['delivery']['default_msg'])."]"?></option>
<?
	for($i = 0, $imax = count($deli_type_key); $i < $imax; $i++) {
		if(!$set[$deli_type_key[$i]]['r_deliType']) continue;
?>
					<option value="<?=$i + 1?>"><?=$deli_type_key[$i]." [".$set[$deli_type_key[$i]]['r_deliType']." : ".(($set[$deli_type_key[$i]]['r_deliType'] == "����") ? number_format($set[$deli_type_key[$i]]['r_default'])."��" : $set[$deli_type_key[$i]]['r_default_msg'])."]"?></option>
<? } ?>
				</select>
			</td>
		</tr>
		<tr>
			<td>�޴� ���</td>
			<td><input type="type" name="nameReceiver" id="nameReceiver" value="<?=$data['nameReceiver']?>" class="line" title="�޴� ���" onkeydown="unchkCopy()" onfocus="setPayInfo()" onblur="setPayInfo()" /></td>
		</tr>
		<tr>
			<td>��ȭ��ȣ</td>
			<td>
				<input type="type" name="phoneReceiver[]" id="phoneReceiver0" value="<?=$data['phoneReceiver'][0]?>" size="4" class="line" title="������ ��ȭ��ȣ" onkeydown="unchkCopy()" option=regNum label="������ ��ȭ��ȣ" /> -
				<input type="type" name="phoneReceiver[]" id="phoneReceiver1" value="<?=$data['phoneReceiver'][1]?>" size="4" class="line" title="������ ��ȭ��ȣ" onkeydown="unchkCopy()" option=regNum label="������ ��ȭ��ȣ" /> -
				<input type="type" name="phoneReceiver[]" id="phoneReceiver2" value="<?=$data['phoneReceiver'][2]?>" size="4" class="line" title="������ ��ȭ��ȣ" onkeydown="unchkCopy()" option=regNum label="������ ��ȭ��ȣ" />
			</td>
		</tr>
		<tr>
			<td>�ڵ��� ��ȣ</td>
			<td>
				<input type="type" name="mobileReceiver[]" id="mobileReceiver0" value="<?=$data['mobileReceiver'][0]?>" size="4" class="line" title="������ �ڵ��� ��ȣ" onkeydown="unchkCopy()" option=regNum label="������ �ڵ��� ��ȣ" /> -
				<input type="type" name="mobileReceiver[]" id="mobileReceiver1" value="<?=$data['mobileReceiver'][1]?>" size="4" class="line" title="������ �ڵ��� ��ȣ" onkeydown="unchkCopy()" option=regNum label="������ �ڵ��� ��ȣ" /> -
				<input type="type" name="mobileReceiver[]" id="mobileReceiver2" value="<?=$data['mobileReceiver'][2]?>" size="4" class="line" title="������ �ڵ��� ��ȣ" onkeydown="unchkCopy()" option=regNum label="������ �ڵ��� ��ȣ" />
			</td>
		</tr>
		<tr>
			<td>�ּ�</td>
			<td>
				<input type="type" name="zonecode" id="zonecode" value="<?=$data['zonecode']?>" size="5" class="line" title="������ �����ȣ" readonly />
				( <input type="type" name="zipcode[]" id="zipcode0" value="<?=$data['zipcode'][0]?>" size="3" class="line" title="������ �����ȣ" readonly /> -
				<input type="type" name="zipcode[]" id="zipcode1" value="<?=$data['zipcode'][1]?>" size="3" class="line" title="������ �����ȣ" readonly /> )
				<a href="javascript:popup('../../proc/popup_address.php',500,432)"><img src="../img/btn_zipcode.gif" align="absmiddle" /></a>
				<a href="javascript:;" onclick="findFA('nameReceiver', '', 'zipcode0', 'zipcode1', 'zonecode', 'address', 'address', 'road_address', 'road_address', 'road_address', 'phoneReceiver0', 'phoneReceiver1', 'phoneReceiver2', 'mobileReceiver0', 'mobileReceiver1', 'mobileReceiver2')"><img src="../img/su_btn03.gif" align="absmiddle"></a><br />
				����	��: <input type="type" name="address" id="address" value="<?=$data['address']?>" style="width:85%" class="line" title="������ �ּ�" onkeydown="unchkCopy()" onfocus="setPayInfo()" onblur="setPayInfo()" /><br />
				���θ�	 : <input type="text" name="road_address" id="road_address" style="width:85%" value="<?=$data['road_address']?>" class="line">
			</td>
		</tr>
		</table>
	</td>
</tr>
</table>
<!-- �������� -->
<table cellpadding="0" cellspacing="0" border="0" width="100%" style="margin:10px 0px 7px 0px;">
<tr valign="bottom">
	<td style="font-weight:bold; font-size:14px; font-family:dotum;"><img src="../img/titledot.gif" align="absbottom" style="margin-right:5px;" />�������� <a href="javascript:;" onclick="setPayInfo()"><img src="../img/btn_refresh.gif" align="absbottom" /></a></td>
</tr>
</table>

<table cellpadding="6" cellspacing="1" class="stdTbl2">
<colgroup>
	<col class="stdTd1"><col class="stdTd2">
</colgroup>
<tr>
	<td>��ǰ�հ� �ݾ�</td>
	<td id="orderOriginalPrice">0 ��</td>
</tr>
<tr>
	<td>��ۺ�</td>
	<td id="orderDeliveryPay"><?=number_format($set['delivery']['default'])?> ��</td>
</tr>
<tr>
	<td>��ǰ����</td>
	<td id='specialDC'>0 ��</td>
</tr>
<tr>
	<td>ȸ������</td>
	<td id='memberDC'>0 ��</td>
</tr>
<tr>
	<td>������</td>
	<td>
		<input type="type" name="emoney" id="emoney" class="line" style="width:100px;text-align:right;" value="0" onblur="chk_emoney(this)" onkeyup="setPayInfo()" onkeydown="if(event.keyCode==13){return false;}" /> �� <span id="reserveInfo"></span>
	</td>
</tr>
<tr>
	<td>��������</td>
	<td>
		<table cellpadding="0" cellspacing="0">
		<tr>
			<td>
				���� : <input type="type" name="coupon" id="coupon" class="line" style="width:100px;text-align:right;" value="0" readonly /> ��
				<img src="../img/btn_coupon.gif" id="cpnFindBtn" onclick="checkCouponPopup()" align="absmiddle" style="cursor:pointer;" />
				<span id="del_coupon" style="visibility:hidden"><a href='javascript:del_coupon();'><img src="../img/btn_coupon_del.gif" align="absmiddle" hspace="2"></a></span>
			</td>
			<td rowspan="2"><div id="apply_coupon"></div></td>
		</tr>
		<tr>
			<td>���� : <input type="type" name="coupon_emoney" id="coupon_emoney" class="line" style="width:100px;text-align:right;" value="0" readonly /> ��</td>
		</tr>
		</table>
	</td>
</tr>
<tr>
	<td>�� �����ݾ�</td>
	<td id="orderTotalPrice" style="font-weight:bold; font-size:14px; color:#E46C0A;">0 ��</td>
</tr>
</table>

<!-- �������� -->
<table cellpadding="0" cellspacing="0" border="0" width="100%" style="margin:10px 0px 7px 0px;">
<tr valign="bottom">
	<td style="font-weight:bold; font-size:14px; font-family:dotum;"><img src="../img/titledot.gif" align="absbottom" style="margin-right:5px;" />��������</td>
</tr>
</table>

<table cellpadding="6" cellspacing="1" class="stdTbl2">
<colgroup>
	<col class="stdTd1"><col class="stdTd2">
</colgroup>
<tr>
	<td>������</td>
	<td style="font-weight:bold;">�ֹ� ���������� ������ ������ �����մϴ�.</td>
</tr>
<tr>
	<td>�Աݰ��� ����</td>
	<td>
		<select name="bankAccount" id="bankAccount" title="�Աݰ���">
			<option value="">- �Աݰ��¸� ������ �ּ��� -</option>
<?
	$bankResult = $db->query("SELECT * FROM ".GD_LIST_BANK." WHERE useyn = 'y'");
	while($bData = $db->fetch($bankResult)) {
?>
			<option value="<?=$bData['sno']?>"><?=$bData['bank']." ".$bData['account']." ".$bData['name']?></option>
<?
	}
?>
		</select>
	</td>
</tr>
<tr>
	<td>�Ա��ڸ�</td>
	<td><input type="type" name="bankSender" id="bankSender" class="line" title="�Ա��ڸ�" /></td>
</tr>
</table>

<div class="button">
	<input type="image" src="../img/btn_register.gif" />
	<a href="javascript:history.back();"><img src="../img/btn_cancel.gif" /></a>
</div>

</form></div>

<div id="MSG02">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle" />����������?</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle" />���� �¶����� ���� �ֹ� ���� �ʰ� ���θ� ��ڰ� ����Ͽ� �ֹ��ϴ� ����Դϴ�.��ȭ�ֹ��̳� ���Ϸ� ���� �ֹ� ������ �Է��Ͽ� ����� �� �ֽ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle" />ȸ��/��ȸ�� �ֹ��� �����ϸ�, ����� �ֹ� ������ �ֹ�����Ʈ�� ���� ������������ �ڵ�����  ��ϵ˴ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle" />���� �ƴ� ��ڰ� �Է��ϴ� ����̱� ������ �ֹ������� ��Ȯ�ϰ� Ȯ���� �� �Է��ؾ� �մϴ�.</td></tr>
</table>
</div>
<script type="text/javascript">
// onload events
Event.observe(document, 'dom:loaded', function(){
	cssRound('MSG02');
	$('selfOrderGoods').src = "../order/self_order_goods.php";
});

Event.observe(window, 'beforeunload', function() {
	new Ajax.Request( "./indb.self_order.php", {
		method: "post",
		asynchronous : false,
		parameters: "mode=destroyUniqueId",
		onComplete: function () { }
	});
});
</script>
<?
	include "../_footer.php"
?>