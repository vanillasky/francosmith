<?
include "../_header.popup.php";

### ��������
	$idx = ($_GET['idx']) ? $_GET['idx'] : "";
	$mode = (!$idx) ? "faRegist" : "faModify";

### �׷���
	$groupQuery = "SELECT fa_group FROM ".GD_FAVORITE_ADDRESS." GROUP BY fa_group ORDER BY fa_group DESC";
	$groupResult = $db->query($groupQuery);

### idx���� �Ѿ���� ������� & �ּ� �б�
	if($idx) {
		$query = "SELECT * FROM ".GD_FAVORITE_ADDRESS." WHERE fa_no = '$idx'";
		$data = $db->fetch($query);

		$selected["fa_group"][$data['fa_group']] = "selected";
		$data['fa_zipcode'] = explode("-", $data['fa_zipcode']);
		$data['fa_phone'] = explode("-", $data['fa_phone']);
		$data['fa_mobile'] = explode("-", $data['fa_mobile']);
	}
?>

<script language="JavaScript">
	function formChecker(f) {
		// �׷� �Է� �˻�
			grOptSelect = document.getElementById('fa_groupOptionSelect');
			grOptCustom = document.getElementById('fa_groupOptionCustom');

			if(grOptSelect.checked) {
				if(!f.fa_groupSelect.value) {
					alert("���� �׷���� ������ �ּ���.");
					f.fa_groupSelect.focus();
					return false;
				}
			}
			else if(grOptCustom.checked) {
				if(!f.fa_groupCustom.value) {
					alert("�ű� �׷���� �Է��� �ּ���.");
					f.fa_groupCustom.focus();
					return false;
				}
			}
			else {
				alert("�׷� �Է� ����� ������ �ּ���.");
				grOptSelect.focus();
				return false;
			}

		// �̸� �Է� �˻�
			if(!f.fa_name.value) {
				alert("�̸��� �Է��� �ּ���.");
				f.fa_name.focus();
				return false;
			}

		// �ּ� �Է� �˻�
			zipcode = document.getElementsByName('zipcode[]');
			if(!zipcode[0].value || !zipcode[1].value || !f.address.value) {
				alert("�����ȣ�� �˻��� �ּ���.");
				popup('../proc/popup_zipcode.php?form=opener.document.frmFA',400,500);
				return false;
			}

		// ����ó �Է� �˻�
			fa_phone = document.getElementsByName('fa_phone[]');
			for(i = 0; i < fa_phone.length; i++) {
				if(!fa_phone[i].value) {
					alert("����ó�� �Է����ּ���.");
					fa_phone[i].focus();
					return false;
				}
			}

		return true;
	}

	function toggleGroup(groupOptionType) {
		grSelect = document.getElementById('fa_groupSelect');
		grCustom = document.getElementById('fa_groupCustom');

		switch(groupOptionType) {
			case 1 :
				grSelect.disabled = false;
				grCustom.disabled = true;
				break;
			case 2 :
				grSelect.disabled = true;
				grCustom.disabled = false;
				break;
		}
	}
</script>

<div class="title title_top">���� ���� �ּҷ� ���</div>
<div class="extext" style="margin:5px 0px 20px 10px;">���� �ֹ� �� ���� ���� �ּҸ� ����Ͽ� ���ϰ� ����� �� �ֽ��ϴ�.</div>
<div>
<form name="frmFA" method="post" action="indb.favorite_address.php" onsubmit="return formChecker(this)">
<input type="hidden" name="idx" id="idx" value="<?=$idx?>" />
<input type="hidden" name="mode" id="mode" value="<?=$mode?>" />
<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td>�׷�</td>
	<td>
		<input type="radio" name="fa_groupOption" id="fa_groupOptionSelect" value="select" onclick="toggleGroup(1)" style="border:0px;" checked />���� �׷�� :
		<select name="fa_groupSelect" id="fa_groupSelect">
			<option value="">==�׷켱��==</option>
<? while($groupData = $db->fetch($groupResult)) { ?>
			<option value="<?=$groupData['fa_group']?>" <?= $selected["fa_group"][$groupData['fa_group']]?>><?=$groupData['fa_group']?></option>
<? } ?>
		</select><br />
		<input type="radio" name="fa_groupOption" id="fa_groupOptionCustom" value="custom" onclick="toggleGroup(2)" style="border:0px;" />�ű� �׷�� :
		<input type="text" name="fa_groupCustom" id="fa_groupCustom" value="" style="width:130px;" class="line" disabled='yes' />
	</td>
</tr>
<tr>
	<td>�̸�</td>
	<td><input type="text" name="fa_name" value="<?=$data['fa_name']?>" style="width:100px;" class="line" /></td>
</tr>
<tr>
	<td>�ּ�</td>
	<td>
		<input type="text" name="zonecode" id="zonecode" size="5" readonly value="<?=$data['fa_zonecode']?>" class="line" />
		( <input type="text" name="zipcode[]" id="zipcode0" size="3" readonly value="<?=$data['fa_zipcode'][0]?>" class="line" /> -
		<input type="text" name="zipcode[]" id="zipcode1" size="3" readonly value="<?=$data['fa_zipcode'][1]?>" class="line" /> )
		<a href="javascript:popup('../../proc/popup_address.php',500,432)"><img src="../img/btn_zipcode.gif" align="absmiddle" /></a><br />
		<input type="text" name="address" id="address" size="50" readonly value="<?=$data['fa_address']?>" class="line" />
		<input type="text" name="address_sub" id="address_sub" size="30" value="<?=$data['fa_address_sub']?>" onkeyup="SameAddressSub(this)" oninput="SameAddressSub(this)" class="line" />
		<input type="hidden" name="road_address" id="road_address" value="<?=$data['fa_road_address']?>">
		<div style="padding:5px 5px 0 5px;font:12px dotum;color:#999;float:left;" id="div_road_address"><?=$data['fa_road_address']?></div>
		<div style="padding:5px 0 0 1px;font:12px dotum;color:#999;" id="div_road_address_sub"><? if ($data['fa_road_address']) { echo $data['fa_address_sub']; } ?></div>
	</td>
</tr>
<tr>
	<td>�̸���</td>
	<td>
		<input type="text" name="fa_email" id="fa_email" value="<?=$data['fa_email']?>" size="26" class="line" />
	</td>
</tr>
<tr>
	<td>����ó</td>
	<td>
		<input type="text" name="fa_phone[]" id="fa_phone1" value="<?=$data['fa_phone'][0]?>" maxlength="4" size="4" class="line" /> -
		<input type="text" name="fa_phone[]" id="fa_phone2" value="<?=$data['fa_phone'][1]?>" maxlength="4" size="4" class="line" /> -
		<input type="text" name="fa_phone[]" id="fa_phone3" value="<?=$data['fa_phone'][2]?>" maxlength="4" size="4" class="line" />
	</td>
</tr>
<tr>
	<td>�޴���</td>
	<td>
		<input type="text" name="fa_mobile[]" id="fa_mobile1" value="<?=$data['fa_mobile'][0]?>" maxlength="4" size="4" class="line" /> -
		<input type="text" name="fa_mobile[]" id="fa_mobile2" value="<?=$data['fa_mobile'][1]?>" maxlength="4" size="4" class="line" /> -
		<input type="text" name="fa_mobile[]" id="fa_mobile3" value="<?=$data['fa_mobile'][2]?>" maxlength="4" size="4" class="line" />
	</td>
</tr>
<tr>
	<td>�޸�</td>
	<td>
		<textarea name="fa_memo" id="fa_memo" style="width:100%; height:100px;"><?=$data['fa_memo']?></textarea>
	</td>
</tr>
</table>

<div class="button_popup">
<input type=image src="../img/btn_confirm_s.gif">
<a href="javascript:self.close()"><img src="../img/btn_cancel_s.gif"></a>
</div>

</form>
</div>

<script>
table_design_load();
</script>