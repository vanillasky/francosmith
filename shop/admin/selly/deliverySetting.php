<?
/*********************************************************
* ���ϸ�     :  deliverySetting.php
* ���α׷��� :  ������ǰ ��ۺ� ����
* �ۼ���     :  ����
* ������     :  2012.05.31
**********************************************************/
/*********************************************************
* ������     :  
* ��������   :  
**********************************************************/
$location = "���� > ������ǰ ��ۺ� ����";
include "../_header.php";

$deilvery_type = Array(
	'1' => '����',
	'2' => '��������������',
	'3' => '���Ҹ�����',
	'4' => '������������'
);

$query = $db->_query_print('SELECT * FROM gd_env WHERE category=[s]', 'selly');
$delivery_data = $db->_select($query);

if($delivery_data) {
	foreach($delivery_data as $data) {
		if($data['name'] == 'basic_payment_delivery_price') {
			$arr_data[$data['name']] = $data['value'];
		}
		else {
			$selected[$data['name']][$data['value']] = 'selected';
		}
	}
}
?>

<script>

function submitForm() {
	var check = formCheck();
	if(check) {
		alert(check);
		return;
	}

	var fm = document.frmList;
	fm.method = "POST";
	fm.action = "../selly/indb.php";
	fm.submit();
}

function formCheck() {
	if(!document.getElementsByName('fixe_delivery')[0].value) return '������ۺ��� �����ϴ�.';//������ۺ�
	if(!document.getElementsByName('cnt_delivery')[0].value) return '��������ۺ��� �����ϴ�.';//��������ۺ�
	if(!document.getElementsByName('payment_delivery')[0].value) return '���ҹ�ۺ��� �����ϴ�.';//���ҹ�ۺ�
	if(!document.getElementsByName('basic_advence_delivery')[0].value) return '�⺻�����å(����) ���� �����ϴ�.';//�⺻�����å_����
	if(!document.getElementsByName('basic_payment_delivery')[0].value) return '�⺻�����å(����) ���� �����ϴ�.';//�⺻�����å_���� Ÿ��
	if(!document.getElementsByName('basic_payment_delivery_price')[0].value) return '�⺻�����å(���� ��ۺ�) ���� �����ϴ�.';//�⺻�����å_���� ��ۺ�
}

</script>

<div class="title title_top">������ǰ ��ۺ� ����<span>SELLY�� �����ϱ� ���� ��ۺ� �����Ͻ� �� �ֽ��ϴ�.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=selly&no=5')"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2"></a></div>

<div style="padding-top:15px"></div>

<form name="frmList" action="../selly/indb.php">
	<input type="hidden" name="mode" value="basic_delivery">
	<table class="tb">
		<col class="cellC" style="width:130px;"><col class="cellC" style="width:120px;"><col class="cellL">
		<tr>
			<td rowspan="4">��ǰ�� ��ۺ�</td>
			<td style="height=40px;">������</td>
			<td>SELLY�� ��ǰ��Ͻ� ��ۺ� ����� �����˴ϴ�.</td>
		</tr>
		<tr>
			<td style="height=40px;">������ۺ�</td>
			<td>
				<span>
					���Ÿ�� : 
					<select name="fixe_delivery">
						<option value="">�����ϼ���</option>
					<? foreach($deilvery_type as $key => $val) { ?>
						<? if($key == '1') continue; ?>
						<option value="<?=$key?>" <?=$selected['fixe_delivery'][$key]?>><?=$val?></option>
					<? } ?>
					</select>
				</span>
				<span style="margin-left:10px;">
					��ۺ� : ��ǰ�� �Էµ� ��ۺ�� ��ϵ˴ϴ�.
				 </span>
			</td>
		</tr>
		<tr>
			<td style="height=40px;">��������ۺ�</td>
			<td>
				<span>
					���Ÿ�� : 
					<select name="cnt_delivery">
						<option value="">�����ϼ���</option>
					<? foreach($deilvery_type as $key => $val) { ?>
						<? if($key == '1') continue; ?>
						<option value="<?=$key?>" <?=$selected['cnt_delivery'][$key]?>><?=$val?></option>
					<? } ?>
					</select>
				</span>
				<span style="margin-left:10px;">
					��ۺ� : ��ǰ�� �Էµ� ��ۺ�� ��ϵ˴ϴ�.
				 </span>
			</td>
		</tr>
		<tr>
			<td style="height=40px;">���ҹ�ۺ�</td>
			<td>
				<span>
					���Ÿ�� : 
					<select name="payment_delivery">
						<option value="">�����ϼ���</option>
					<? foreach($deilvery_type as $key => $val) { ?>
						<? if($key == '4' || $key == '1') continue; ?>
						<option value="<?=$key?>" <?=$selected['payment_delivery'][$key]?>><?=$val?></option>
					<? } ?>
					</select>
				</span>
				<span style="margin-left:10px;">
					��ۺ� : ��ǰ�� �Էµ� ��ۺ�� ��ϵ˴ϴ�.
				 </span>
			</td>
		</tr>
		<tr>
			<td rowspan="3">�⺻��ۺ���å</td>
		</tr>
		<tr>
			<td style="height=65px;">����</td>
			<td>
				<span>
					���Ÿ�� : 
					<select name="basic_advence_delivery">
						<option value="">�����ϼ���</option>
					<? foreach($deilvery_type as $key => $val) { ?>
						<? if($key == '1' || $key == '3') continue;?>
						<option value="<?=$key?>" <?=$selected['basic_advence_delivery'][$key]?>><?=$val?></option>
					<? } ?>
					</select>
				</span>
				<span style="margin-left:10px;">
					��ۺ� : �⺻�����å�� ������ ��ۺ�� ��ϵ˴ϴ�.
					<div class="extext" style="margin-top:8px;">* �������� �̻��� �ǸŰ��� ���� ��ǰ�� ����� ��ϵ˴ϴ�.</div>
				 </span>
			</td>
		</tr>
		<tr>
			<td style="height=65px;">����</td>
			<td>
				<span>
					���Ÿ�� : 
					<select name="basic_payment_delivery">
						<option value="">�����ϼ���</option>
					<? foreach($deilvery_type as $key => $val) { ?>
						<? if($key == '1' || $key == '4') continue;?>
						<option value="<?=$key?>" <?=$selected['basic_payment_delivery'][$key]?>><?=$val?></option>
					<? } ?>
					</select>
				</span>
				<span style="margin-left:10px;">
					��ۺ� : <input type="text" name="basic_payment_delivery_price" value="<?=$arr_data['basic_payment_delivery_price']?>" class="line" style="height:22px" onkeydown="onlynumber();" />
					<div class="extext" style="margin-top:8px;">* �������� �̻��� �ǸŰ��� ���� ��ǰ�� ����� ��ϵ˴ϴ�.</div>
				</span>
			</td>
		</tr>
	</table>
	<div class="button_top">
		<input type="image" src="../img/btn_register.gif" alt="���" onclick="submitForm();return false;" />
	</div>
</form>

<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td height="5"></td></tr>
<tr><td>
SELLY�� �����Ǵ� ��ǰ�� ��ۺ� �����Ͻ� �� �ֽ��ϴ�.<br/>
SELLY���� ���Ǵ� ��ۺ�� ����, ��������������, ���Ҹ�����, �������������� ������<br/>
e���� ��ۺ� ������ ���� ������ �� �ִ� ���� �޶����� �˴ϴ�.<br/><br/><br/>

������ǰ ��ۺ� ������ ���Ͻ� ��� e���� ��ۺ� ���ᰡ �ƴ� ��ǰ�� �������� ��ũ�Ͻ� �� �����ϴ�.
</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

<? include "../_footer.php"; ?>