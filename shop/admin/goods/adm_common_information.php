<?php
$location = "�⺻���� > ���ϸ���(������) ����";
include "../_header.php";

$commonInformation = Core::config('goods_common_information');
?>
<script type="text/javascript" src="../../lib/meditor/mini_editor.js"></script>
<h2 class="title">��ǰ ��������/�̿�ȳ�<span>��ǰ����������� �� ���� ��ǰ �ŷ����� ���� ����</span></h2>

<form name="admin-form" method="post" action="indb_adm_common_information.php">
<table class="admin-form-table">
<tr>
	<th>���ް��� ����</th>
	<td>
		<div class="field-wrapper IF_use_separate_supply_info_IS_1">
		<textarea style="width:100%;height:400px" name="separate_supply_info" type="editor"><?=$commonInformation['separate_supply_info']?></textarea>
		</div>
	</td>
</tr>
<tr>
	<th>û��öȸ �� �������</th>
	<td>
		<div class="field-wrapper IF_use_separate_cancel_info_IS_1">
		<textarea style="width:100%;height:400px" name="separate_cancel_info" type="editor"><?=$commonInformation['separate_cancel_info']?></textarea>
		</div>
	</td>
</tr>
<tr>
	<th>��ȯ/��ǰ/���� ���ǰ� ����</th>
	<td>
		<div class="field-wrapper IF_use_separate_claim_info_IS_1">
		<textarea style="width:100%;height:400px" name="separate_claim_info" type="editor"><?=$commonInformation['separate_claim_info']?></textarea>
		</div>
	</td>
</tr>
<tr>
	<th>����ó�� ����</th>
	<td>
		<div class="field-wrapper IF_use_separate_trouble_info_IS_1">
		<textarea style="width:100%;height:400px" name="separate_trouble_info" type="editor"><?=$commonInformation['separate_trouble_info']?></textarea>
		</div>
	</td>
</tr>
<tr>
	<th>�ŷ����</th>
	<td>
		<div class="field-wrapper IF_use_separate_service_info_IS_1">
		<textarea style="width:100%;height:400px" name="separate_service_info" type="editor"><?=$commonInformation['separate_service_info']?></textarea>
		</div>
	</td>
</tr>
</table>

<div class="button">
	<input type="image" src="../img/btn_save.gif">
</div>

</form>

<script type="text/javascript">
mini_editor("../../lib/meditor/");
</script>

<? include "../_footer.php"; ?>
