<?php
include "../_header.popup.php";
?>
<script type="text/javascript">
function __generate()
{
	if (!document.fm.title.value)
	{
		alert('�ɼ� ��Ʈ���� �Է��� �ּ���.');
		return false;
	}

	var optionName = {};
	var optionValue = {};

	parent.$$('input[name="option_name[]"]').each(function(el, idx){
		optionName[idx] = el.value;
		optionValue[idx] = parent.$$('input[name="option_value[]"]')[idx].value
	});

	document.fm.name.value = JSON.stringify(optionName);
	document.fm.value.value = JSON.stringify(optionValue);

	return true;
}
</script>
<style>
body {margin:0;overflow:hidden;}
.container {background:#f6f6f6;line-height:160%;padding:10px;border-bottom:1px solid #e6e6e6;}
.container input {width:105px;color:#e73366;font-weight:bold;text-align:center;}
</style>
<form name="fm" id="fm" method="post" class="admin-form" action="indb_adm_popup_option_preset_save.php" onSubmit="return __generate();">
<input type="hidden" name="name" value="" >
<input type="hidden" name="value" value="" >
<div class="container">
	�ش� �ɼ� ������ <br />
	�ɼ� ��Ʈ�� <input type="text" name="title" value="" style=""> ���� <br />
	�ɼǹٱ��Ͽ� �����մϴ�.
</div>

<div class="button-container">
	<input type="image" src="../img/buttons/btn_popup_confirm.gif">
</div>
</form>

<?php include "../_footer.popup.php"; ?>
