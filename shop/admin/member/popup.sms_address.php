<?

include "../_header.popup.php";

### �׷�� ��������
$query = "SELECT sms_grp FROM ".GD_SMS_ADDRESS." GROUP BY sms_grp ORDER BY sms_grp ASC";
$res = $db->query($query);
while ($data=$db->fetch($res)) $r_grp[] = $data['sms_grp'];

if(!$_GET['mode']){
	$data = $db->fetch("SELECT * FROM ".GD_SMS_ADDRESS." WHERE sno='".$_GET['sno']."'");
	extract($data);
	$sms_mobile	= explode("-",$sms_mobile);
	$selected['grp'][$sms_grp]	= "selected";
	$checked['sex'][$sex]		= "checked";
}
?>

<div class="title title_top">SMS �ּҷ� ����</div>

<form name="frmMember" method="post" enctype="multipart/form-data" action="./indb.php" onsubmit="return chkForm(this);">
<input type="hidden" name="mode" value="sms_address_add<?=$_GET['mode'] =='excel' ? '_by_excel' : ''?>">
<input type="hidden" name="sno" value="<?=$_GET['sno']?>">
<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td>�׷�</td>
	<td>
	<div>
	<span class="noline"><input type="radio" name="grp_chk" value="Def" checked />�����׷�� : </span>
	<select name="sms_grp">
	<option value="">==�׷켱��==</option>
	<? foreach( $r_grp as $v ){ ?>
	<option value="<?=$v?>" <?=$selected['grp'][$v]?>><?=$v?></option>
	<? } ?>
	</select>
	</div>
	<div>
	<span class="noline"><input type="radio" name="grp_chk" value="New" />�űԱ׷�� : </span>
	<input type="text" NAME="sms_grp_new" value="" class="line"/></td>
	</div>
</tr>
<? if ( $_GET['mode'] === 'excel' ) { ?>
<tr>
	<td>���� ����</td>
	<td><input type="file" NAME="xls_file" value="" require  class="line" /></td>
</tr>
</table>
<p>
��ǥ(,)�� ���е� ���� csv ����(.csv) �� ���ε� �����մϴ�. <a href="../data/csv_sms.xls">[���� �ٿ�ε�]</a><br>
�̸�, �ڵ�����ȣ, ������ �ʼ� �����Դϴ�.
</p>
<? } else { ?>
<tr>
	<td>�̸�</td>
	<td><input type="text" NAME="sms_name" value="<?=$sms_name?>" require  class="line" /></td>
</tr>
<tr>
	<td>�ڵ�����ȣ</td>
	<td>
	<input type="text" NAME="sms_mobile[]" size="4" maxlength="3" value="<?=$sms_mobile[0]?>" onkeydown="onlynumber();" require  class="line" /> -
	<input type="text" NAME="sms_mobile[]" size="4" maxlength="4" value="<?=$sms_mobile[1]?>" onkeydown="onlynumber();" require class="line" /> -
	<input type="text" NAME="sms_mobile[]" size="4" maxlength="4" value="<?=$sms_mobile[2]?>" onkeydown="onlynumber();" require class="line" />
	</td>
</tr>
<tr>
	<td>����</td>
	<td class="noline">
	<input type="radio" name="sex" value="M" <?=$checked['sex']['M']?> />����
	<input type="radio" name="sex" value="F" <?=$checked['sex']['F']?> />����
	</td>
</tr>
<tr>
	<td>���</td>
	<td><input type="text" NAME="sms_etc" value="<?=$sms_etc?>" style="width:100%"  class="line" /></td>
</tr>
</table>
<? } ?>
<p>

<div class="button_popup" align="center">
<input type="image" src="../img/btn_confirm_s.gif" />
</div>

</form>

<script>table_design_load();</script>