<?
$location = "상품관리 > 옵션바구니(SET) 관리";
include "../_header.php";

$preset = Clib_Application::getModelClass('goods_option_preset');

if (Clib_Application::request()->get('sno')) {
	$preset->load(
		Clib_Application::request()->get('sno')
	);
}
?>
<script type="text/javascript" src="../js/adm_form.js"></script>
<script type="text/javascript" src="./js/goods_register.js"></script>
<script type="text/javascript">
function __generate()
{
	if (!document.fm.title.value)
	{
		alert('옵션 세트명을 입력해 주세요.');
		return false;
	}

	if (!nsAdminGoodsForm.option.validateOptionForm())
	{
		return false;
	}

	var optionName = {};
	var optionValue = {};

	$$('input[name="option_name[]"]').each(function(el, idx){
		optionName[idx] = el.value;
		optionValue[idx] = $$('input[name="option_value[]"]')[idx].value
	});

	document.fm.name.value = JSON.stringify(optionName);
	document.fm.value.value = JSON.stringify(optionValue);

	return true;
}
</script>

<h2 class="title">옵션바구니 등록/수정</h2>

<form name="fm" id="fm" method="post" class="admin-form" action="indb_adm_popup_option_preset_form.php" onSubmit="return __generate();">
	<input type="hidden" name="action" value="save">
	<input type="hidden" name="sno" value="<?=$preset->getId()?>">
	<input type="hidden" name="name" value="">
	<input type="hidden" name="value" value="">

	<table class="admin-form-table">
	<tr>
		<th>옵션 세트명</th>
		<td>
			<div class="field-wrapper">
			<input type="text" name="title" value="<?=$preset->getSetName()?>" >
			</div>
		</td>
	</tr>
	</table>

	<?
	// array (option_name => option value)
	$sets = $preset->getSet();

	$optionValues = array_values($sets);
	$optionNames = array_keys($sets);
	?>

	<table class="admin-form-table" id="el-option-table" style="margin-top:10px;">
	<thead>
	<tr>
		<th>옵션명</th>
		<th>옵션값 <span class="help">콤마(,)로 구분 (ex: 빨강, 파랑)</span></th>
	<tr>
	</thead>
	<tbody>
	<tr>
		<th><input type="text" name="option_name[]" value="<?=$optionNames[0]?>"></th>
		<td>
			<!-- css3 -->
			<div style="width:100%;padding-right:100px;box-sizing:border-box;">
				<div class="field-wrapper" style="float:left;">
					<input type="text" name="option_value[]" value="<?=$optionValues[0]?>">
				</div>
				<div style="float:left;margin:2px -100px 0 5px;">
					<a href="javascript:void(0);" onclick="nsAdminGoodsForm.option.add();"><img src="../img/i_add.gif"></a>
				</div>
			</div>
		</td>
	</tr>
	<? for ($i=1,$m=sizeof($optionNames);$i<$m;$i++) { ?>
	<tr>
		<th><input type="text" name="option_name[]" value="<?=$optionNames[$i]?>"></th>
		<td>
			<div style="width:100%;padding-right:100px;box-sizing:border-box;">
				<div class="field-wrapper" style="float:left;">
					<input type="text" name="option_value[]" value="<?=$optionValues[$i]?>">
				</div>
				<div style="float:left;margin:2px -100px 0 5px;">
					<a href="javascript:void(0);" onclick="nsAdminGoodsForm.option.del(event);"><img src="../img/i_del.gif"></a>
				</div>
			</div>
		</td>
	</tr>
	<? } ?>
	</tbody>
	</table>

	<div class="button-container ar">
		<a href="javascript:void(0);" onclick="nsAdminGoodsForm.option.preset.openPresetSample();"><img src="../img/buttons/btn_sample_select.gif"></a>
	</div>

	<div class="button-container">
		<input type="image" src="../img/buttons/btn_popup_register.gif" >
	</div>

</form>

<?php include "../_footer.php"; ?>
