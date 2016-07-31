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
	<td class="goodsChoice_outlineTdCenter"><div class="goodsChoice_title"> <span class="goodsChoice_titleArrow">��</span>��ǰ���� <span class="extext">�ִ� ��� ������ ��ǰ���� <?php echo number_format($maxLimit); ?>�� �Դϴ�. ��� ��ǰ <?php echo number_format($maxLimit); ?>�� �ʰ��� ���� ��ϵ� ��ǰ�� �ڵ� ���� �˴ϴ�</span></div></td>
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
	<td class="goodsChoice_outlineTdCenter"><div class="goodsChoice_title"> <span class="goodsChoice_titleArrow">��</span>��� ��ǰ ����Ʈ</div></td>
</tr>
<tr>
	<!-- ��ǰ���� ����Ʈ-->
	<td valign="top" class="goodsChoice_outlineTd">
		<iframe id="iframe_goodsChoiceList" src="../proc/_goodsChoiceList.php" width="100%" frameborder="0"></iframe>
	</td>
	<!-- ��ǰ���� ����Ʈ-->

	<!-- ��ϻ�ǰ ����Ʈ-->
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
	<!-- ��ϻ�ǰ ����Ʈ-->
</tr>
<tr class="goodChoice_buttonArea">
	<td colspan="3" >
		<div class="registeredGoodsCountMsgArea">���û�ǰ ���� : <span id="registeredCheckedGoodsCountMsg" class="registeredGoodsCountInfo">0</span>�� / ��ϻ�ǰ ���� : <span id="registeredGoodsCountMsg"  class="registeredGoodsCountInfo">0</span>��</div>
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