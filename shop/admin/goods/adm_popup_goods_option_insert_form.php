<?php
include "../_header.popup.php";

$goods = Clib_Application::getModelClass('goods')->load(Clib_Application::request()->get('goodsno'));
$optionName = $goods->getOptionName();
?>
<script type="text/javascript">
function __generate(f)
{
	// 필수 값 체크
	try
	{
		$$('input[name="opt"]').each(function(el){
			el.value = el.value.trim();
			if (el.value == '') {
				throw {};
			}
		});
	}
	catch (e) {
		alert('옵션명을 입력해 주세요.');
		return false;
	}

	if (!chkForm(f))
	{
		return false;
	}

	// 옵션값 중복 체크

	// 삽입
	var data = Form.serializeElements( $('goods-form').getElements(), true );

	parent.nsAdminGoodsForm.option.insertRow(JSON.stringify(data));
	parent.nsAdminForm.dialog.close();

	return false;

}

Event.observe(document, 'dom:loaded', function(){
	var tmp;
	$$('input[type="text"][name^="option_"]').each(function(el){
		tmp = el.name.replace('option_', 'goods_');
		if (tmp != 'goods_stock')
		{
			el.value = parent.$$('input[name="'+tmp+'"]').first().value;
		}
	});
});
</script>

<link rel="stylesheet" type="text/css" href="./css/css.css">
<script type="text/javascript" src="../js/adm_form.js"></script>
<script type="text/javascript" src="./js/goods_register.js"></script>
<h2 class="title">옵션(품목) 추가하기</h2>

<form name="fm" id="goods-form" class="admin-form" method="post" onSubmit="return __generate(this);">

	<table class="admin-form-table">
	<tr>
		<th>옵션값 추가</th>
		<td>
			<table class="admin-form-table">
			<tr class="ac">
				<? foreach($goods->getOptionName() as $name) { ?>
				<th><?=$name?></th>
				<? } ?>
			</tr>
			<tr>
				<? foreach($goods->getOptionName() as $idx => $name) { ?>
				<td><div class="field-wrapper"><input type="text" name="opt" value=""></div></td>
				<? } ?>
			</tr>
			</table>
		</td>
	</tr>
	<tr>
		<th>옵션 재고 설정</th>
		<td>
			<table class="admin-form-table">
			<tr class="ac">
				<th style="">재고</th>
				<th style="">옵션판매금액</th>
				<th style="">정가</th>
				<th style="">매입가</th>
				<th style="">적립금</th>
				<th style="width:30px;">출력</th>
			</tr>
			<tr class="ac">
				<td><div class="field-wrapper"><input type="text" name="option_stock" value="" option="regNum" label="재고"></div></td>
				<td><div class="field-wrapper"><input type="text" name="option_price" value="" option="regNum" label="옵션판매금액" required></div></td>
				<td><div class="field-wrapper"><input type="text" name="option_consumer" value="" option="regNum" label="정가"></div></td>
				<td><div class="field-wrapper"><input type="text" name="option_supply" value="" option="regNum" label="매입가"></div></td>
				<td><div class="field-wrapper"><input type="text" name="option_reserve" value="" option="regNum" label="적립금"></div></td>
				<td><input type="checkbox" name="option_is_display" value="1" checked></td>
			</tr>
			</table>
		</td>
	</tr>
	</table>

	<div class=button>
		<input type="image" src="../img/btn_modify.gif" >
		<a href="javascript:void(0);" onclick="parent.nsAdminForm.dialog.close();"><img src="../img/btn_cancel.gif"></a>
	</div>
</form>

<?php include "../_footer.popup.php"; ?>
