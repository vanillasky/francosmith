<?php
include "../_header.popup.php";

$categories = Clib_Application::getCollectionClass('category');
$categories->setOrder('category','asc');
//$categories->setCategoryFilter($categoryId);
$categories->load();
?>
<link rel="stylesheet" type="text/css" href="./css/css.css">
<script type="text/javascript">
function __add() {
	parent.nsAdminGoodsForm.category.add($$('#el-category-multi-selector input:checked'));
}
function __uncheck() {

	$$('#el-category-multi-selector input:checked').each(function(el){
		if (!el.disabled) {
			el.writeAttribute('checked', false);
		}
	});

}
</script>

<form class="admin-form">
<table class="nude" style="width:100%;">
<colgroup>
	<col width="*">
	<col width="125">
</colgroup>
<tr>
	<td>
		<? if (_CATEGORY_NEW_METHOD_ === true) {?>
		<p class="help">
			<span class="specialchar">※</span> 상품분류를 일괄 선택하여 등록할 수 있습니다. 상품을 노출하고자 하는 분류를 모두 선택 후 등록해주세요
		</p>
		<?}?>
		<div id="el-category-multi-selector">
		<ul>
			<?
			$preCheckedCategories = explode(',', Clib_Application::request()->get('cate'));
			foreach ($categories as $category) {
			?>
			<li><label><input type="checkbox" name="checkable_cate[]" value="<?=$category->getId()?>" <?=(in_array($category->getId(), $preCheckedCategories)) ? 'checked disabled' : ''?>> <?=$categories->getCategoryRoute($category)?></label></li>
			<? } ?>
		</ul>
		</div>
	</td>
	<td>
		<a href="javascript:void(0);" onclick="__add();return false;"><img src="../img/i_regist_l.gif" vspace="4"></a>
	</td>
</tr>
</table>
</form>
<div class="button-container al">
	<a href="javascript:void(0);" onclick="__uncheck();return false;"><img src="../img/buttons/btn_popup_select_clear.gif"></a>
</div>

<?
include "../_footer.popup.php";
?>
