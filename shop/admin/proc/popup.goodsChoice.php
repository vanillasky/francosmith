<?php
include '../_header.popup.php';
@include_once(dirname(__FILE__) . '/../../conf/config.goodsChoice.php');
$maxLimit = 0;

$GoodsChoiceList = Core::loader('GoodsChoiceList');
$maxLimit = $GoodsChoiceList->getLimit($_GET['fileName']);
$goodsChoice_outlineSortHeader = $goodsChoice_outlineSortFooter = '';
$goodsChoice_outlineSortHeader = $GoodsChoiceList->getOutlineSortHtml('head');
$goodsChoice_outlineSortFooter = $GoodsChoiceList->getOutlineSortHtml('foot');
?>
<input type="hidden" id="eHiddenName" value="<?php echo $_GET[eHiddenName]; ?>" />
<input type="hidden" id="displayName" value="<?php echo $_GET[displayName]; ?>" />
<input type="hidden" id="maxLimit" value="<?php echo $maxLimit; ?>" />

<table cellpadding="0" cellspacing="0" width="100%" border="0" class="goodsChoice_outlineTable">
<colgroup>
	<col style="width:560px;" />
	<col />
	<col style="width:560px;" />
</colgroup>
<tr>
	<td class="goodsChoice_outlineTdCenter"><div class="goodsChoice_title"> <span class="goodsChoice_titleArrow">▼</span>상품선택 <span class="extext">최대 등록 가능한 상품수는 <?php echo number_format($maxLimit); ?>개 입니다. 등록 상품 <?php echo number_format($maxLimit); ?>개 초과시 기존 등록된 상품은 자동 삭제 됩니다</span></div></td>
	<td rowspan="3" valign="middle" class="goodsChoice_outlineTdCenter">
		<table cellpadding="0" cellspacing="0" class="goodsChoice_addDelButtonArea">
		<tr>
			<td><img src="../img/btn_add.gif" id="addGoods" /></td>
		</tr>
		<tr>
			<td><img src="../img/btn_goodsConfirm_small.gif" id="goodsChoiceConfirmSmall" /></td>
		</tr>
		<tr>
			<td><img src="../img/btn_remove.gif" id="delGoods" /></td>
		</tr>
		</table>
	</td>
	<td class="goodsChoice_outlineTdCenter"><div class="goodsChoice_title"> <span class="goodsChoice_titleArrow">▼</span>등록 상품 리스트</div></td>
</tr>
<tr>
	<!-- 상품선택 리스트-->
	<td valign="top" class="goodsChoice_outlineTd">
		<iframe id="iframe_goodsChoiceList" src="../proc/_goodsChoiceList.php" width="100%" frameborder="0"></iframe>
	</td>
	<!-- 상품선택 리스트-->

	<!-- 등록상품 리스트-->
	<td valign="top" class="goodsChoice_outlineTd">
		<table cellpadding="0" cellpadding="0" width="100%">
		<tr>
			<td class="goodsChoice_outlineSort"><?php echo $goodsChoice_outlineSortHeader; ?></td>
		</tr>
		<tr>
			<td valign="top" class="goodsChoice_registeredTdArea"><div id="goodsChoice_registerdOutlineDiv"></div></td>
		</tr>
		<tr>
			<td class="goodsChoice_outlineSort"><?php echo $goodsChoice_outlineSortFooter; ?></td>
		</tr>
		</table>
	</td>
	<!-- 등록상품 리스트-->
</tr>
<tr class="goodChoice_buttonArea">
	<td colspan="3" >
		<div class="registeredGoodsCountMsgArea">선택상품 개수 : <span id="registeredCheckedGoodsCountMsg" class="registeredGoodsCountInfo">0</span>개 / 등록상품 개수 : <span id="registeredGoodsCountMsg"  class="registeredGoodsCountInfo">0</span>개</div>
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td>
				<img src="../img/btn_goodsConfirm.jpg" id="goodsChoiceConfirm" class="hand" />
				&nbsp;
				<img src="../img/btn_cancel.gif" id="goodsChoiceCancel" class="hand" />
			</td>
		</tr>
		</table>
	</td>
</tr>
</table>

<?php include '../footer.popup.php'; ?>