<?php
include "../_header.popup.php";

$goods = Clib_Application::getModelClass('goods')->load(Clib_Application::request()->get('goodsno'));
$form = Clib_Application::form('admin_goods_register');
?>
<link rel="stylesheet" type="text/css" href="./css/css.css">
<script type="text/javascript" src="../js/adm_form.js"></script>
<script type="text/javascript" src="./js/goods_register.js"></script>
<script type="text/javascript">
function __submit(f)
{
	if (confirm('등록/수정 하시겠습니까?\n기존의 옵션정보는 복구되지 않습니다.'))
	{
		return chkForm(f);
	}
	return false;
}
</script>

<h2 class="title">옵션 새로 등록하기</h2>

<form name="fm" id="goods-form" class="admin-form" method="post" action="indb_adm_popup_goods_option_reset.php" onsubmit="return __submit(this);">
<input type="hidden" name="goodsno" value="<?=$goods->getId()?>">
<input type="hidden" name="price" value="<?=$goods->getPrice()?>">
<input type="hidden" name="use_option" value="1">

	<table class="admin-form-table">
	<!-- if 다수 옵션 -->
	<tr>
		<th>옵션 출력방식</th>
		<td colspan="3">
			<?php
			$tags = $form->getTag('opttype');
			?>
			<label><?=$tags['일체형'];?>일체형</label>
			<label><?=$tags['분리형'];?>분리형</label>
		</td>
	</tr>
	<tr>
		<th class="require">옵션 등록하기</th>
		<td colspan="3">
		<table class="admin-form-table" id="el-option-table">
		<thead>
		<tr>
			<th>옵션명</th>
			<th>옵션값 <span class="help">콤마(,)로 구분 (ex: 빨강, 파랑)</span></th>
		<tr>
		</thead>
		<tbody>
		<tr>
			<th><input type="text" name="option_name[]" value=""></th>
			<td>
				<!-- css3 -->
				<div style="width:100%;padding-right:100px;box-sizing:border-box;">
					<div class="field-wrapper" style="float:left;">
						<input type="text" name="option_value[]" value="">
					</div>
					<div style="float:left;margin:2px -100px 0 5px;">
						<a href="javascript:void(0);" onclick="nsAdminGoodsForm.option.add();"><img src="../img/i_add.gif"></a>
					</div>
				</div>
			</td>
		</tr>
		</tbody>
		</table>

		<div class="button-container">
			<div class="al">
				<a href="javascript:void(0);" onclick="nsAdminGoodsForm.option.generate();return false;"><img src="../img/buttons/btn_form_confirm.gif"></a>
			</div>

			<div class="ar">
				<a href="javascript:void(0);" onclick="nsAdminGoodsForm.option.preset.save();return false;"><img src="../img/buttons/btn_option_save.gif"></a>
				<a href="javascript:void(0);" onclick="nsAdminGoodsForm.option.preset.load();return false;"><img src="../img/buttons/btn_option_select.gif"></a>
			</div>

			<div class="clear"></div>
		</div>
		</td>
	</tr>
	<tr>
		<th class="require">옵션 재고설정</th>
		<td colspan="3">
		<!-- // option form -->
		<table class="admin-form-table" id="el-option-list">
		<thead>

		<tr>
			<th style=""></th>
			<th style="width:80px;">재고</th>
			<th style="width:80px;">옵션판매금액</th>
			<th style="width:80px;">정가</th>
			<th style="width:80px;">매입가</th>
			<th style="width:80px;">적립금</th>
			<th style="width:30px;">출력</th>
			<th style="width:30px;">삭제</th>
		</tr>
		</thead>
		<tbody>
		<tr class="ac" style="display:none;">

			<input type="hidden" name="option_sno[]" value="">

			<td></td>

			<td><input type="text" name="option_stock[]" value="" style="width:60px;" ></td>
			<td><input type="text" name="option_price[]" value="" style="width:60px;" ></td>
			<td><input type="text" name="option_consumer[]" value="" style="width:60px;" ></td>
			<td><input type="text" name="option_supply[]" value="" style="width:60px;" ></td>
			<td><input type="text" name="option_reserve[]" value="" style="width:60px;" ></td>
			<td><input type="checkbox" name="option_is_display[]" value="1" checked></td>
			<td><input type="checkbox" name="option_is_deleted[]" value="1"></td>
		</tr>
		</tbody>
		<tfoot>
		<tr class="ac">
			<td>일괄적용</td>
			<td>
				<input type="text" name="all_option_stock" style="width:60px;" >
				<a href="javascript:void(0);" onclick="nsAdminGoodsForm.option.allChange('option_stock');"><img src="../img/buttons/btn_seting.gif"></a>
			</td>
			<td>
				<input type="text" name="all_option_price" style="width:60px;" >
				<a href="javascript:void(0);" onclick="nsAdminGoodsForm.option.allChange('option_price');"><img src="../img/buttons/btn_seting.gif"></a>
			</td>
			<td>
				<input type="text" name="all_option_consumer" style="width:60px;" >
				<a href="javascript:void(0);" onclick="nsAdminGoodsForm.option.allChange('option_consumer');"><img src="../img/buttons/btn_seting.gif"></a>
			</td>
			<td>
				<input type="text" name="all_option_supply" style="width:60px;" >
				<a href="javascript:void(0);" onclick="nsAdminGoodsForm.option.allChange('option_supply');"><img src="../img/buttons/btn_seting.gif"></a>
			</td>
			<td>
				<input type="text" name="all_option_reserve" style="width:60px;" >
				<a href="javascript:void(0);" onclick="nsAdminGoodsForm.option.allChange('option_reserve');"><img src="../img/buttons/btn_seting.gif"></a>
			</td>
			<td>
				<input type="checkbox" value="1" checked name="all_option_is_display" onclick="nsAdminGoodsForm.option.allChange('option_is_display');">
			</td>
			<td>
				<input type="checkbox" value="1" checked name="all_option_is_deleted" onclick="nsAdminGoodsForm.option.allChange('option_is_deleted');">
			</td>
		</tr>
		</tfoot>
		</table>

		<div id="el-option-list-paging">
		</div>

		<p class="help IF_mode_IS_register">
			<span class="specialchar">※</span> 등록된 옵션정보는 전체 삭제가 불가능 합니다. (옵션 1개이상 출력 필수)<br />
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;수정모드에서 [옵션새로등록]을 통해 새로 등록해 주세요.<br />
			<span class="specialchar">※</span> 옵션 출력순서는 수정모드에서 설정/관리 가능합니다.
		</p>

		<!-- // option form -->
		</td>
	</tr>
	<!-- if 다수 옵션 -->
	</table>

	<div class=button>
		<input type="image" src="../img/btn_modify.gif" >
		<a href="javascript:void(0);" onclick="parent.nsAdminForm.dialog.close();"><img src="../img/btn_cancel.gif"></a>
	</div>
</form>

<script type="text/javascript">
// onload events
Event.observe(document, 'dom:loaded', function(){

	nsAdminForm.init($('goods-form'));

	nsAdminGoodsForm.option.init({
		pageSize : 10
	});

});

</script>
<?php include "../_footer.popup.php"; ?>
