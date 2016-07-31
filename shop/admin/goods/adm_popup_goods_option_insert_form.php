<?php
include "../_header.popup.php";

$goods = Clib_Application::getModelClass('goods')->load(Clib_Application::request()->get('goodsno'));
$optionName = $goods->getOptionName();
?>
<script type="text/javascript">
function __generate(f)
{
	// �ʼ� �� üũ
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
		alert('�ɼǸ��� �Է��� �ּ���.');
		return false;
	}

	if (!chkForm(f))
	{
		return false;
	}

	// �ɼǰ� �ߺ� üũ

	// ����
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
<h2 class="title">�ɼ�(ǰ��) �߰��ϱ�</h2>

<form name="fm" id="goods-form" class="admin-form" method="post" onSubmit="return __generate(this);">

	<table class="admin-form-table">
	<tr>
		<th>�ɼǰ� �߰�</th>
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
		<th>�ɼ� ��� ����</th>
		<td>
			<table class="admin-form-table">
			<tr class="ac">
				<th style="">���</th>
				<th style="">�ɼ��Ǹűݾ�</th>
				<th style="">����</th>
				<th style="">���԰�</th>
				<th style="">������</th>
				<th style="width:30px;">���</th>
			</tr>
			<tr class="ac">
				<td><div class="field-wrapper"><input type="text" name="option_stock" value="" option="regNum" label="���"></div></td>
				<td><div class="field-wrapper"><input type="text" name="option_price" value="" option="regNum" label="�ɼ��Ǹűݾ�" required></div></td>
				<td><div class="field-wrapper"><input type="text" name="option_consumer" value="" option="regNum" label="����"></div></td>
				<td><div class="field-wrapper"><input type="text" name="option_supply" value="" option="regNum" label="���԰�"></div></td>
				<td><div class="field-wrapper"><input type="text" name="option_reserve" value="" option="regNum" label="������"></div></td>
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
