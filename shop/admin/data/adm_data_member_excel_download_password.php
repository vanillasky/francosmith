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
			alert("[비밀번호]와 [비밀번호 확인]이 같지 않습니다.");
			return false;
		}
		else {
			var passwordCount = 0;
			if (/[a-z]/.test(form.password.value)) passwordCount++;
			if (/[A-Z]/.test(form.password.value)) passwordCount++;
			if (/[0-9]/.test(form.password.value)) passwordCount++;
			if (/[~`!>@?\/<#\"\'$;:\]%.^,&[*()_+\-=|\\\{}]/.test(form.password.value)) passwordCount++;
			if (!/^[\x21-\x7E]{10,16}$/.test(form.password.value) || passwordCount < 2) {
				alert('[비밀번호]의 입력형식이 잘못되었습니다.');
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
<div class="title title_top">다운로드 파일 비밀번호<span>다운로드할 회원DB파일의 비밀번호를 설정하세요</span></div>
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
			<td>비밀번호 <img src="../img/icons/bullet_compulsory.gif" style="vertical-align: middle;"/></td>
			<td>
				<input type="password" name="password" class="line" fld_esssential="fld_esssential" label="비밀번호"/>
				<div style="color: #627dce; font-size: 11px; margin-top: 5px;">
					영문대/소문자, 숫자, 특수문자 중 2종류 이상의<br/>
					조합으로 10~16자로 적용
				</div>
			</td>
		</tr>
		<tr>
			<td>비밀번호 확인 <img src="../img/icons/bullet_compulsory.gif" style="vertical-align: middle;"/></td>
			<td>
				<input type="password" name="passwordConfirm" class="line" fld_esssential="fld_esssential" label="비밀번호 확인"/>
			</td>
		</tr>
	</table>
	<div style="text-align: center; margin-top: 10px;">
		<button id="excel-download-button" type="button">회원DB다운로드</button>
	</div>
</form>
<?php include dirname(__FILE__).'/../_footer.popup.php'; ?>