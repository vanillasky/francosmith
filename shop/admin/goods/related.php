<?
$location = "��ǰ���� > ���� ��ǰ ���� ����";
include "../_header.php";

if (is_file("../../conf/config.related.goods.php")) include "../../conf/config.related.goods.php";
else {
	// �⺻ ���� ��
	$cfg_related['horizontal'] =  5;
	$cfg_related['vertical'] =  1;
	$cfg_related['size'] =  $cfg[img_s];

	$cfg_related['dp_image'] = 1;	// ����
	$cfg_related['dp_goodsnm'] =  1;
	$cfg_related['dp_price'] = 1;
	$cfg_related['dp_shortdesc'] = $cfg[img_s];

	$cfg_related['use_cart'] = 0;
	$cfg_related['cart_icon'] = 1;

	$cfg_related['exclude_soldout'] =  0;
	$cfg_related['link_type'] = 'self';
}
?>
<script>
function fnSuggestImageSize(col) {
	var _width = Math.floor(640 / col) - 10;
	$('el-suggest-image-size').update(_width);

}
</script>
<div class="title title_top">���� ��ǰ ���� ����<span>��ǰ ��ȭ�鿡�� �������� ���û�ǰ�� �������¸� ���� �� �����Ͻ� �� �ֽ��ϴ�.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=33')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<form name="frmRelatedGoods" method="post" action="./indb.related.php" target="ifrmHidden" enctype="multipart/form-data">
<input type="hidden" name="mode" value="config">

	<table class=tb>
	<col class=cellC width="120"><col class=cellL>

	<tr>
		<td>���û�ǰ ��������</td>
		<td>
			<p class="extext">
			���� <select name="cfg_related[horizontal]" onChange="fnSuggestImageSize(this.value)"><? for ($i=1;$i<=10;$i++) {?><option value="<?=$i?>" <?=($i==$cfg_related['horizontal']) ? 'selected' : ''?>><?=$i?></option><?}?></select>�� X ���� <select name="cfg_related[vertical]"><? for ($i=1;$i<=5;$i++) {?><option value="<?=$i?>" <?=($i==$cfg_related['vertical']) ? 'selected' : ''?>><?=$i?></option><? } ?></select>�� <br>
			��ǰ �̹��� ������ <input type="text" name="cfg_related[size]" value="<?=$cfg_related['size']?>" class="line" style="width:35px;" onKeydown="onlynumber();">�ȼ� (��������� : <span style="letter-spacing:0px;" id="el-suggest-image-size"></span> �ȼ�) <br>
			���� ������ ���� ū ������ �����ÿ� ��ǰ�̹����� ���� ���� �� �ֽ��ϴ�. <br>
			</p>
		</td>
	</tr>
	<tr>
		<td>���û�ǰ ���Ⱚ</td>
		<td class="noline">
			<label><input type="checkbox" name="cfg_related[dp_image]" value="1" checked disabled>�̹���</label>
			<label><input type="checkbox" name="cfg_related[dp_goodsnm]" value="1" <?=$cfg_related['dp_goodsnm'] ? 'checked' : ''?>>��ǰ��</label>
			<label><input type="checkbox" name="cfg_related[dp_price]" value="1" <?=$cfg_related['dp_price'] ? 'checked' : ''?>>����</label>
			<label><input type="checkbox" name="cfg_related[dp_shortdesc]" value="1" <?=$cfg_related['dp_shortdesc'] ? 'checked' : ''?>>ª������</label>
		</td>
	</tr>
	<tr>
		<td>��ٱ��� ���</td>
		<td class="noline">
			<label><input type="radio" name="cfg_related[use_cart]" value="1" <?=$cfg_related['use_cart'] == 1 ? 'checked' : ''?>>�����</label>
			<label><input type="radio" name="cfg_related[use_cart]" value="0" <?=$cfg_related['use_cart'] != 1 ? 'checked' : ''?>>������</label>

			<fieldset id="related-goods-cart-icon" style="margin-top:10px;"><legend>������</legend>

				<table border="0" style="" cellpadding="5">
				<tr>
				<?
				// �⺻ ���� ������
				for ($i=1;$i<=5;$i++) {
				?>
				<td align="center">
					<img src="../../data/goods/icon/icon_basket<?=$i?>.gif"><br>
					<input type="radio" name="cfg_related[cart_icon]" value="<?=$i?>" <?=$cfg_related['cart_icon'] == $i ? 'checked' : ''?>>
				</td>
				<? } ?>

				<td align="center">
					<img src="../../data/goods/icon/custom/basket" onerror="this.src='../img/img_basket.gif';" id="el-user-cart-icon"><br>
					<input type="radio" name="cfg_related[cart_icon]" value="custom" <?=$cfg_related['cart_icon'] == 'custom' ? 'checked' : ''?>>
				</td>
				</table>
				����� ������ : <input type="file" name="cart_image" value="">
			</fieldset>


		</td>
	</tr>
	<tr>
		<td>ǰ����ǰ ���� ����</td>
		<td class="noline">
			<label><input type="radio" name="cfg_related[exclude_soldout]" value="0" <?=$cfg_related['exclude_soldout'] != 1 ? 'checked' : ''?>>ǰ���� ��ǰ ����</label>
			<label><input type="radio" name="cfg_related[exclude_soldout]" value="1" <?=$cfg_related['exclude_soldout'] == 1 ? 'checked' : ''?>>��ǰ ǰ���� �ڵ����� ���û�ǰ���� ����</label>
		</td>
	</tr>
	<tr>
		<td>���û�ǰ ����</td>
		<td class="noline">
			<label><input type="radio" name="cfg_related[link_type]" value="self"  <?=$cfg_related['link_type'] != 'blank' ? 'checked' : ''?>>����â���� ���û�ǰ �� ������ ����</label>
			<label><input type="radio" name="cfg_related[link_type]" value="blank" <?=$cfg_related['link_type'] == 'blank' ? 'checked' : ''?>>��â���� ���û�ǰ �� ������ ����</label>
		</td>
	</tr>
	</table>




	<div class="button">
		<input type=image src="../img/btn_register.gif">
		<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a>
	</div>

</form>



<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">���û�ǰ ����� ��ǰ��� �� ��ǰ����Ʈ�� ���� ���������� �Ͻ� �� �ֽ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">���û�ǰ ��Ͻ� �������� ���ڵ������� ���� �ϼ��� ��쿡�� 5���� ��ǰ�� �������� ����Ǹ�, �������¿� ������ ��ǰ ������ ������� �ʽ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">���û�ǰ ���⳻���� ������ �� �ֽ��ϴ�. ª�������� ��� ��ǰ�̹����� ���콺 ������ ��ǳ������ �������ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">��ٱ��� ��� ����� ������ �� �ֽ��ϴ�. (����� �������� ����Ͽ� �⺻�������� ��ü�� �� �ֽ��ϴ�.) </td></tr>

<tr><td><img src="../img/icon_list.gif" align="absmiddle">ǰ����ǰ ���� ������ �����մϴ�. ��ǰ ǰ���� �ڵ����� ���û�ǰ���� ���� �˴ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">���û�ǰ �̹��� Ŭ���� �������������� ���� �Ǵ� ��â���� ������ ���� ������ �����մϴ�.</td></tr>
</table>
</div>
<script>
cssRound('MSG01');
fnSuggestImageSize(<?=$cfg_related['horizontal']?>);
</script>

<? include "../_footer.php"; ?>