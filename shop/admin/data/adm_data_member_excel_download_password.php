<?php require_once dirname(__FILE__).'/../_header.popup.php'; ?>
<script type="text/javascript">
	var submit = function()
	{
		var form = $("excel-download-form");
		form.target = "ifrmHidden";
		if (!chkForm(form)) {
			return false;
		}
		else if (form.password.value !== form.passwordConfirm.value) {
			alert("[��й�ȣ]�� [��й�ȣ Ȯ��]�� ���� �ʽ��ϴ�.");
			return false;
		}
		else {
			var passwordCount = 0;
			if (/[a-z]/.test(form.password.value)) passwordCount++;
			if (/[A-Z]/.test(form.password.value)) passwordCount++;
			if (/[0-9]/.test(form.password.value)) passwordCount++;
			if (/[~`!>@?\/<#\"\'$;:\]%.^,&[*()_+\-=|\\\{}]/.test(form.password.value)) passwordCount++;
			if (!/^[\x21-\x7E]{10,16}$/.test(form.password.value) || passwordCount < 2) {
				alert('[��й�ȣ]�� �Է������� �߸��Ǿ����ϴ�.');
				return false;
			}
			else {
				form.submit();
				parent.closeLayer();
			}
		}
	};
	var enterSubmit = function(event)
	{
		event = event || window.event;
		if (event.keyCode === 13) {
			submit();
		}
	};
	document.observe("dom:loaded", function(){
		$("excel-download-button").observe("click", submit);
		$("excel-download-form").password.onkeydown = enterSubmit;
		$("excel-download-form").passwordConfirm.onkeydown = enterSubmit;
	});
</script>
<link rel="stylesheet" type="text/css" href="../style.css"/>
<style type="text/css">
	table.tb, table.tb tr, table.tb td {
		border: solid #e6e6e6 1px;
	}
	#excel-download-button {
		cursor: pointer;
		vertical-align: middle;
		border: none; background: url('../img/btn_gooddown.gif') 100% 100%;
		width: 146px;
		height: 39px;
		display: block;
		text-indent: -1000px;
		font-size: 0;
		margin: 0 auto;
	}
</style>
<div class="title title_top">�ٿ�ε� ���� ��й�ȣ<span>�ٿ�ε��� ȸ��DB������ ��й�ȣ�� �����ϼ���</span></div>
<form id="excel-download-form" method="post" action="./adm_data_member_excel_download.php">
	<input type="hidden" name="mode" value="downloadPasswordExcel"/>
<?php foreach ($_POST as $name => $value) { ?>
	<?php if (is_array($value)) { ?>
		<?php foreach ($value as $_key => $_value) { ?>
		<input type="hidden" name="<?php echo $name; ?>[<?php echo $_key; ?>]" value="<?php echo $_value; ?>"/>
		<?php } ?>
	<?php } else { ?>
	<input type="hidden" name="<?php echo $name; ?>" value="<?php echo $value; ?>"/>
	<?php } ?>
<?php } ?>
	<table class="tb" style="width: 100%; border-collapse: collapse;" border="1" cellpadding="5">
		<col class="cellC"/><col class="cellL"/>
		<tr>
			<td>��й�ȣ <img src="../img/icons/bullet_compulsory.gif" style="vertical-align: middle;"/></td>
			<td>
				<input type="password" name="password" class="line" fld_esssential="fld_esssential" label="��й�ȣ"/>
				<div style="color: #627dce; font-size: 11px; margin-top: 5px;">
					������/�ҹ���, ����, Ư������ �� 2���� �̻���<br/>
					�������� 10~16�ڷ� ����
				</div>
			</td>
		</tr>
		<tr>
			<td>��й�ȣ Ȯ�� <img src="../img/icons/bullet_compulsory.gif" style="vertical-align: middle;"/></td>
			<td>
				<input type="password" name="passwordConfirm" class="line" fld_esssential="fld_esssential" label="��й�ȣ Ȯ��"/>
			</td>
		</tr>
	</table>
	<div style="text-align: center; margin-top: 10px;">
		<button id="excel-download-button" type="button">ȸ��DB�ٿ�ε�</button>
	</div>
</form>
<?php include dirname(__FILE__).'/../_footer.popup.php'; ?>