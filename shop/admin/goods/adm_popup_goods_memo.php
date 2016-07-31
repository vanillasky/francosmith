<?php
include "../_header.popup.php";

$goods = Clib_Application::getModelClass('goods')->load(
	Clib_Application::request()->get('goodsno')
);

?>
<script type="text/javascript" src="./js/goods_register.js"></script>
<style>
table.admin-form-table tbody th {width:83px;}
</style>
<form class="admin-form" method="post" action="indb_adm_popup_goods_memo.php">
<input type="hidden" name="action" value="save" >
<input type="hidden" name="goodsno" value="<?=$goods->getId()?>" >

<table class="admin-form-table">
<tr>
	<th style="width:83px;">상품명</th>
	<td><?=$goods['goodsnm']?></td>
</tr>
<tr>
	<th>상품번호</th>
	<td><?=$goods['goodscd']?></td>
</tr>
<tr>
	<th>내용</th>
	<td>
	<div class="field-wrapper">
		<textarea id="memo" name="memo" style="width:100%;height:60px" class="tline"><?=$goods['memo']?></textarea>
	</div>
	</td>
</tr>
</table>

<div class="button-container">
	<input type="image" src="../img/buttons/btn_popup_save.gif" >
</div>

</form>

<?php include "../_footer.popup.php"; ?>
