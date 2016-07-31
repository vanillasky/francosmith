<?php
include "../_header.popup.php";

$goods = Clib_Application::getModelClass('goods')->load(Clib_Application::request()->get('goodsno'));

$options = $goods->getOptions();
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

		$$('input[name^="sort["]').each(function(el){

			value = el.value.trim();

			if (value == '') {
				el.focus();
				throw 'empty';
			}
			else {

				values.each(function(el){
					if (el == value) {
						throw 'duplicate';
					}
				});

				values.push(value);
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
		}
		return false;
	}

	return true;
}

function __autoindex()
{
	$$('input[name^="sort["]').each(function(el, idx){
		el.value = idx + 1;
	});
}
</script>

<form name="fm" id="fm" method="post" class="admin-form" action="indb_adm_popup_goods_option_sort.php" onSubmit="return __sort();">

	<table class="admin-list-table">
	<thead>
	<tr>
		<th>순서</th>
		<? foreach($optionNames = $goods->getOptionName() as $name) { ?>
		<th><?=$name?></th>
		<? } ?>
	</tr>
	</thead>
	<tbody>
	<?
	$optionSize = sizeof($optionNames);
	foreach($options as $option) {
	?>
	<tr class="ac">
		<td><input type="text" style="width:30px;text-align:center;" name="sort[<?=$option['sno']?>]" value="<?=$option['go_sort']?>"></td>
		<? for ($i=1;$i<=$optionSize;$i++) { ?>
		<td><?=$option->getNthName($i);?></td>
		<? } ?>
	</tr>
	<? } ?>
	</tbody>
	</table>

	<div class="button-container">

		<div class="al">
			<a href="javascript:void(0);" onclick="__autoindex('<?=$nth?>');return false;"><img src="../img/buttons/btn_popup_auto.gif"></a>
		</div>

		<div class="ar">
			<input type="image" src="../img/buttons/btn_popup_register.gif" >
		</div>

		<div class="clear"></div>

	</div>

</form>

<?php include "../_footer.popup.php"; ?>
