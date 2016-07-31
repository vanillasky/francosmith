<?php
include "../_header.popup.php";

$goods = Clib_Application::getModelClass('goods')->load(Clib_Application::request()->get('goodsno'));
$options = $goods->getOptions()->sort();

$optionBlock = array();

foreach($goods->getOptionName() as $nth => $name) {

	foreach($options as $option) {
		$optionBlock[$nth][] = $option->getNthName($nth+1);
	}

	$optionBlock[$nth] = array_unique($optionBlock[$nth]);
}
?>
<script type="text/javascript" src="./js/goods_register.js"></script>
<script type="text/javascript">
function __sort()
{
	try
	{
		// 빈값, 중복값 체크
		var values = [];
		var value = '';
		var nth = '';

		$$('input[name^="_sort["]').each(function(el){

			value = el.value.trim();
			nth = el.readAttribute('data-nth');

			if (value == '') {
				el.focus();
				throw 'empty';
			}
			else {

				value = value - 1;

				if (values[nth]) {
					if (values[nth][value])
					{
						throw 'duplicate';
					}
				}
				else {
					values[nth] = [];
				}

				values[nth][value] = el.readAttribute('data-name');
			}
		});

	}
	catch (e) {
		switch (e)
		{
			case 'empty':
				alert('숫자를 다 채워주세요.');
				break;
			case 'duplicate':
				alert('중복된 숫자가 있습니다.');
				break;
			default :
				console.log(e);
				break;
		}
		return false;
	}

	nsAdminGoodsForm.option.setOptionListFromArray(values);

	var sort = 0;
	var _opt = '';
	nsAdminGoodsForm.option.optionList.each(function(opt){

		_opt = opt.join(',');

		$$('input[name^="sort["]').each(function(el) {
			if (el.readAttribute('data-name') == _opt) {
				el.value = ++sort;
				throw $break;
			}
		});

	});

	return true;
}

function __autoindex(nth)
{
	$$('input[name^="_sort['+nth+']"]').each(function(el, idx){
		el.value = idx + 1;
	});
}
</script>

<form name="fm" id="fm" method="post" class="admin-form" action="indb_adm_popup_goods_option_sort.php" onSubmit="return __sort();">
<? foreach($options as $option) { ?>
<input type="hidden" name="sort[<?=$option['sno']?>]" data-name="<?=$option->getName(',')?>" value="<?=$option['go_sort']?>">
<? } ?>

	<? foreach ($optionBlock as $nth => $values) { ?>
	<div style="margin-right:10px;width:150px;float:left;">
		<table class="admin-list-table">
		<thead>
		<tr>
			<th>순서</th>
			<th>옵션값</th>
		</tr>
		</thead>
		<tbody>
		<?
		$idx = 1;
		foreach($values as $value) {
		?>
		<tr class="ac">
			<td><input type="text" style="width:30px;text-align:center;" name="_sort[<?=$nth?>][]" data-nth="<?=$nth?>" data-name="<?=$value?>" value="<?=$idx++?>"></td>
			<td><?=$value?></td>
		</tr>
		<? } ?>
		</tbody>
		</table>

		<div class="button-container">
			<a href="javascript:void(0);" onclick="__autoindex('<?=$nth?>');return false;"><img src="../img/buttons/btn_popup_auto.gif"></a>
		</div>

	</div>
	<? } ?>

	<div class="clear"></div>

	<hr>

	<div class="button-container">
		<input type="image" src="../img/buttons/btn_popup_register.gif" >
	</div>

</form>

<?php include "../_footer.popup.php"; ?>
