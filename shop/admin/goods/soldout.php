<?
$location = "��ǰ���� > ǰ����ǰ ���� ����";
include "../_header.php";

if (is_file("../../conf/config.soldout.php")) include "../../conf/config.soldout.php";
else {
	// �⺻ ���� ��
	$cfg_soldout['exclude_main'] =  0;
	$cfg_soldout['back_main'] = 0;
	$cfg_soldout['exclude_category'] =  0;
	$cfg_soldout['back_category'] = 0;
	$cfg_soldout['exclude_search'] =  0;
	$cfg_soldout['back_search'] = 0;

	$cfg_soldout['display'] = 'icon';
	$cfg_soldout['mobile_display'] = 'none';
	$cfg_soldout['price'] = 'price';
	$cfg_soldout['display_overlay'] = 1;
	$cfg_soldout['mobile_display_overlay'] = 1;
	$cfg_soldout['display_icon'] = 1;
}
?>
<style>
p.soldout {margin:3px 0 0 0;}
</style>

<div class="title title_top">ǰ����ǰ ��������<span> ���� ���������� ǰ����ǰ ���� �� ������ �����Ͻ� �� �ֽ��ϴ�.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=37')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a></div>

<form name="frmRelatedGoods" method="post" action="./indb.soldout.php" target="ifrmHidden" enctype="multipart/form-data">
<input type="hidden" name="mode" value="config">

	<div style="padding:10px 0px 5px 0px;font-weight:bold;">���������� ǰ����ǰ ��������</div>
	<table class=tb style="margin-bottom:20px;">
	<col class=cellC width="120"><col class=cellL>
	<tr>
		<td>ǰ�� ��ǰ ����</td>
		<td>
			<label class="noline"><input type="radio" name="cfg_soldout[exclude_main]" value="0" <?=$cfg_soldout['exclude_main'] != '1' ? 'checked' : ''?>>ǰ����ǰ �����ֱ�</label>
			<label class="noline"><input type="radio" name="cfg_soldout[exclude_main]" value="1" <?=$cfg_soldout['exclude_main'] == '1' ? 'checked' : ''?>>ǰ����ǰ �������� �ʱ�</label>
		</td>
	</tr>
	<tr>
		<td>ǰ����ǰ ��������</td>
		<td>
			<label class="noline"><input type="radio" name="cfg_soldout[back_main]" value="0" <?=$cfg_soldout['back_main'] != '1' ? 'checked' : ''?>>���� ������� �����ֱ�</label>
			<label class="noline"><input type="radio" name="cfg_soldout[back_main]" value="1" <?=$cfg_soldout['back_main'] == '1' ? 'checked' : ''?>>����Ʈ ������ ������</label>
		</td>
	</tr>
	</table>


	<div style="padding:10px 0px 5px 0px;font-weight:bold;">�з������� ǰ����ǰ ���� ����</div>
	<table class=tb style="margin-bottom:20px;">
	<col class=cellC width="120"><col class=cellL>
	<tr>
		<td>ǰ�� ��ǰ ����</td>
		<td>
			<label class="noline"><input type="radio" name="cfg_soldout[exclude_category]" value="0" <?=$cfg_soldout['exclude_category'] != '1' ? 'checked' : ''?>>ǰ����ǰ �����ֱ�</label>
			<label class="noline"><input type="radio" name="cfg_soldout[exclude_category]" value="1" <?=$cfg_soldout['exclude_category'] == '1' ? 'checked' : ''?>>ǰ����ǰ �������� �ʱ�</label>
		</td>
	</tr>
	<tr>
		<td>ǰ����ǰ ��������</td>
		<td>
			<label class="noline"><input type="radio" name="cfg_soldout[back_category]" value="0" <?=$cfg_soldout['back_category'] != '1' ? 'checked' : ''?>>���� ������� �����ֱ�</label>
			<label class="noline"><input type="radio" name="cfg_soldout[back_category]" value="1" <?=$cfg_soldout['back_category'] == '1' ? 'checked' : ''?>>����Ʈ ������ ������</label>
		</td>
	</tr>
	</table>



	<div style="padding:10px 0px 5px 0px;font-weight:bold;">�˻������� ǰ����ǰ ���� ����</div>
	<table class=tb style="margin-bottom:20px;">
	<col class=cellC width="120"><col class=cellL>
	<tr>
		<td>ǰ�� ��ǰ ����</td>
		<td>
			<label class="noline"><input type="radio" name="cfg_soldout[exclude_search]" value="0" <?=$cfg_soldout['exclude_search'] != '1' ? 'checked' : ''?>>ǰ����ǰ �����ֱ�</label>
			<label class="noline"><input type="radio" name="cfg_soldout[exclude_search]" value="1" <?=$cfg_soldout['exclude_search'] == '1' ? 'checked' : ''?>>ǰ����ǰ �������� �ʱ�</label>
		</td>
	</tr>
	<tr>
		<td>ǰ����ǰ ��������</td>
		<td>
			<label class="noline"><input type="radio" name="cfg_soldout[back_search]" value="0" <?=$cfg_soldout['back_search'] != '1' ? 'checked' : ''?>>���� ������� �����ֱ�</label>
			<label class="noline"><input type="radio" name="cfg_soldout[back_search]" value="1" <?=$cfg_soldout['back_search'] == '1' ? 'checked' : ''?>>����Ʈ ������ ������</label>
		</td>
	</tr>
	</table>


	<div style="padding:10px 0px 5px 0px;font-weight:bold;">�귣�������� ǰ����ǰ ���� ����</div>
	<table class=tb style="margin-bottom:20px;">
	<col class=cellC width="120"><col class=cellL>
	<tr>
		<td>ǰ�� ��ǰ ����</td>
		<td>
			<label class="noline"><input type="radio" name="cfg_soldout[exclude_brand]" value="0" <?=$cfg_soldout['exclude_brand'] != '1' ? 'checked' : ''?>>ǰ����ǰ �����ֱ�</label>
			<label class="noline"><input type="radio" name="cfg_soldout[exclude_brand]" value="1" <?=$cfg_soldout['exclude_brand'] == '1' ? 'checked' : ''?>>ǰ����ǰ �������� �ʱ�</label>
		</td>
	</tr>
	<tr>
		<td>ǰ����ǰ ��������</td>
		<td>
			<label class="noline"><input type="radio" name="cfg_soldout[back_brand]" value="0" <?=$cfg_soldout['back_brand'] != '1' ? 'checked' : ''?>>���� ������� �����ֱ�</label>
			<label class="noline"><input type="radio" name="cfg_soldout[back_brand]" value="1" <?=$cfg_soldout['back_brand'] == '1' ? 'checked' : ''?>>����Ʈ ������ ������</label>
		</td>
	</tr>
	</table>


	<div style="padding:10px 0px 5px 0px;font-weight:bold;">�̺�Ʈ������ ǰ����ǰ ���� ����</div>
	<table class=tb style="margin-bottom:20px;">
	<col class=cellC width="120"><col class=cellL>
	<tr>
		<td>ǰ�� ��ǰ ����</td>
		<td>
			<label class="noline"><input type="radio" name="cfg_soldout[exclude_event]" value="0" <?=$cfg_soldout['exclude_event'] != '1' ? 'checked' : ''?>>ǰ����ǰ �����ֱ�</label>
			<label class="noline"><input type="radio" name="cfg_soldout[exclude_event]" value="1" <?=$cfg_soldout['exclude_event'] == '1' ? 'checked' : ''?>>ǰ����ǰ �������� �ʱ�</label>
		</td>
	</tr>
	<tr>
		<td>ǰ����ǰ ��������</td>
		<td>
			<label class="noline"><input type="radio" name="cfg_soldout[back_event]" value="0" <?=$cfg_soldout['back_event'] != '1' ? 'checked' : ''?>>���� ������� �����ֱ�</label>
			<label class="noline"><input type="radio" name="cfg_soldout[back_event]" value="1" <?=$cfg_soldout['back_event'] == '1' ? 'checked' : ''?>>����Ʈ ������ ������</label>
		</td>
	</tr>
	</table>

	<div class="title title_top">PC ǰ����ǰ ǥ�� ����<span>PC���� ���θ� ������������ ǰ��ǥ�ø� �����մϴ�.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=37')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a></div>
	<table class=tb style="margin-bottom:20px;">
	<col class=cellC width="120"><col class=cellL>
	<tr>
		<td>ǰ�� ǥ�� ����</td>
		<td>

			<fieldset class="soldout"><legend><label class="noline"><input type="radio" name="cfg_soldout[display]" value="overlay" <?=$cfg_soldout['display'] == 'overlay' ? 'checked' : ''?>>��ǰ �̹��� ��������</label></legend>
				<table border="0" style="" cellpadding="5">
				<tr>
				<?
				// �⺻ ���� ������
				for ($i=1;$i<=5;$i++) {
				?>
				<td align="center" class="noline">
					<div style="width:130px;height:130px;background:url(../../data/goods/icon/icon_soldout<?=$i?>) no-repeat center center;border:1px solid #CCCCCC;"></div>
					<input type="radio" name="cfg_soldout[display_overlay]" value="<?=$i?>" <?=$cfg_soldout['display_overlay'] == $i ? 'checked' : ''?>>
				</td>
				<? } ?>
				<td align="center" class="noline">
					<div style="width:130px;height:130px;background:url(../../data/goods/icon/custom/soldout_overlay) no-repeat center center;border:1px solid #CCCCCC;" id="el-user-soldout-overlay"></div>
					<input type="radio" name="cfg_soldout[display_overlay]" value="custom" <?=$cfg_soldout['display_overlay'] == 'custom' ? 'checked' : ''?>>
				</td>
				</table>

				<div style="padding-top:3px">����� �̹��� : <input type="file" name="soldout_overlay" value=""> <span class="extext">(���� ������ : <?=$cfg[img_m]?>px)</span></div>

				<p class="soldout extext">
				����� �̹��� ���ε�� �ݵ�� ����� ����ó���� png, gif ���Ϸ� ���ε� �ϼž� �մϴ�.
				</p>
			</fieldset>

			<fieldset class="soldout"><legend><label class="noline"><input type="radio" name="cfg_soldout[display]" value="icon" <?=$cfg_soldout['display'] == 'icon' ? 'checked' : ''?>>ǰ�� ������ ǥ��</label></legend>
				<div style="padding-top:3px"><label class="noline"><input type="radio" name="cfg_soldout[display_icon]" value="1" <?=$cfg_soldout['display_icon'] != 'custom' ? 'checked' : ''?>>�⺻ ������</label> : <img src="../../data/skin/<?=$cfg[tplSkin]?>/img/icon/good_icon_soldout.gif" align="absmiddle"></div>
				<div style="padding-top:3px"><label class="noline"><input type="radio" name="cfg_soldout[display_icon]" value="custom" <?=$cfg_soldout['display_icon'] == 'custom' ? 'checked' : ''?>>��ü ������</label> : <img src="../../data/goods/icon/custom/soldout_icon" onerror="this.src='../img/img_basket.gif';" id="el-user-soldout-icon"> <input type="file" name="soldout_icon" value=""></div>
			</fieldset>

			<fieldset class="soldout"><legend><label class="noline"><input type="radio" name="cfg_soldout[display]" value="none" <?=$cfg_soldout['display'] == 'none' ? 'checked' : ''?>>ǥ�� ����</label></legend>
				<p class="soldout extext">
				������, �������� �̹��� ��� ǥ������ �ʽ��ϴ�.
				</p>
			</fieldset>

		</td>
	</tr>
	</table>

	<div class="title title_top">����ϼ� ǰ����ǰ ǥ�� ���� <span>����ϼ� ������������ ǰ��ǥ�ø� �����մϴ�.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=37')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a></div>
	<table class=tb style="margin-bottom:20px;">
	<col class=cellC width="120"><col class=cellL>
	<tr>
		<td>ǰ�� ǥ�� ����</td>
		<td>

			<fieldset class="soldout"><legend><label class="noline"><input type="radio" name="cfg_soldout[mobile_display]" value="overlay" <?=$cfg_soldout['mobile_display'] === 'overlay' ? 'checked' : ''?>>��ǰ �̹��� ��������</label></legend>
				<table border="0" style="" cellpadding="5">
				<tr>
				<?
				// �⺻ ���� ������
				for ($i=1;$i<=1;$i++) {
				?>
				<td align="center" class="noline">
					<div style="width:130px;height:130px;background:url(../../data/goods/icon/mobile_icon_soldout<?=$i?>) no-repeat center center; background-size: cover; border:1px solid #CCCCCC;"></div>
					<input type="radio" name="cfg_soldout[mobile_display_overlay]" value="<?=$i?>" <?=$cfg_soldout['mobile_display_overlay'] == $i ? 'checked' : ''?>>
				</td>
				<? } ?>
				
				<td align="center" class="noline">
					<div style="width:130px;height:130px;background:url(../../data/goods/icon/mobile_custom_soldout) no-repeat center center;border:1px solid #CCCCCC;" id="mobile-el-user-soldout-overlay"></div>
					<input type="radio" name="cfg_soldout[mobile_display_overlay]" value="custom" <?=$cfg_soldout['mobile_display_overlay'] === 'custom' ? 'checked' : ''?>>
				</td>
				</table>

				<div style="padding-top:3px">����� �̹��� : <input type="file" name="mobile_custom_soldout" value=""> <span class="extext">(���� ������ : <?=$cfg[img_m]?>px)</span></div>

				<p class="soldout extext">
				����� �̹��� ���ε�� �ݵ�� ����� ����ó���� png, gif ���Ϸ� ���ε� �ϼž� �մϴ�.
				</p>
			</fieldset>

			<input type="radio" name="cfg_soldout[mobile_display]" value="none" <?=$cfg_soldout['mobile_display'] === 'none' ? 'checked' : ''?>>ǥ�� ����

		</td>
	</tr>
	</table>

	<div class="title title_top">ǰ����ǰ ���Ⱚ ����<span>��� ������������ ǰ����ǰ ���Ⱚ�� �����մϴ�.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=37')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a></div>
	<table class=tb style="margin-bottom:20px;">
	<col class=cellC width="120"><col class=cellL>
	<tr>
		<td>��ǰ��</td>
		<td class="noline">
			<label><input type="radio" name="cfg_soldout[goodsnm]" value="1" <?=$cfg_soldout[goodsnm] != '0' ? 'checked' : '' ?>>����</label>
			<label><input type="radio" name="cfg_soldout[goodsnm]" value="0" <?=$cfg_soldout[goodsnm] == '0' ? 'checked' : '' ?>>�������</label>
		</td>
	</tr>
	<tr>
		<td>��ǰ����
		<p style="margin:5px 0 0 0;" class="extext">
		��ǰ �� ���������� �Բ� ����˴ϴ�.
		</p>
		</td>
		<td>
			<fieldset class="soldout"><legend><label class="noline"><input type="radio" name="cfg_soldout[price]" value="price" <?=$cfg_soldout['price'] == 'price' ? 'checked' : ''?>>���� ǥ��</label></legend>
				<span class="extext">��ǰ�� ������ ǥ����.</span>
			</fieldset>

			<fieldset class="soldout"><legend><label class="noline"><input type="radio" name="cfg_soldout[price]" value="string" <?=$cfg_soldout['price'] == 'string' ? 'checked' : ''?>>���� ��ü ����</label></legend>
				<input type="text" name="cfg_soldout[price_string]" value="<?=$cfg_soldout[price_string]?>" class="line"> <span class="extext">ǰ���� ��ǰ ������ ��ü�� �ؽ�Ʈ �Է�</span>
			</fieldset>

			<fieldset class="soldout"><legend><label class="noline"><input type="radio" name="cfg_soldout[price]" value="image" <?=$cfg_soldout['price'] == 'image' ? 'checked' : ''?>>�̹��� ���</label></legend>
				<img src="../../data/goods/icon/custom/soldout_price" onerror="this.src='../img/img_basket.gif';" id="el-user-soldout-price"><br>
				<input type="file" name="soldout_price" value=""> <span class="extext">ǰ���� ��ǰ ������ ��ü�� �̹��� ���ε�</span>
			</fieldset>
		</td>
	</tr>
	<tr>
		<td>ª������</td>
		<td class="noline">
			<label><input type="radio" name="cfg_soldout[shortdesc]" value="1" <?=$cfg_soldout[shortdesc] != '0' ? 'checked' : '' ?>>����</label>
			<label><input type="radio" name="cfg_soldout[shortdesc]" value="0" <?=$cfg_soldout[shortdesc] == '0' ? 'checked' : '' ?>>�������</label>
		</td>
	</tr>
	<tr>
		<td>��������</td>
		<td class="noline">
			<label><input type="radio" name="cfg_soldout[coupon]" value="1" <?=$cfg_soldout[coupon] != '0' ? 'checked' : '' ?>>����</label>
			<label><input type="radio" name="cfg_soldout[coupon]" value="0" <?=$cfg_soldout[coupon] == '0' ? 'checked' : '' ?>>�������</label>
		</td>
	</tr>
	<tr>
		<td>��ǰ������</td>
		<td class="noline">
			<label><input type="radio" name="cfg_soldout[icon]" value="1" <?=$cfg_soldout[icon] != '0' ? 'checked' : '' ?>>����</label>
			<label><input type="radio" name="cfg_soldout[icon]" value="0" <?=$cfg_soldout[icon] == '0' ? 'checked' : '' ?>>�������</label>
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
<tr><td>- ǰ��ǥ�� ����</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">��ǰ �̹��� �������� : ��ǰ�̹���(�����) ����� �������� �̹����� ���� ǥ�� �˴ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">(�� ����� �̹��� ���ε�� �ݵ�� ����� ����ó���� png,gif ���Ϸ� ���ε� �ϼž� �մϴ�.)</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">ǰ�� ������ ǥ��: [ã�ƺ���]�� ǰ�� ������ ��� �ٸ� �̹����� ������ ��ü�� �����մϴ�.</td></tr>
<tr><td>&nbsp;&nbsp;&nbsp;��ü �������� ���� ��� "ǰ��" ���������� ǥ�õ˴ϴ�.</td></tr>
<tr><td>&nbsp;</td></tr>
<tr><td>- ��ǰ���� ���⼳��</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">���� ��ü������ ����Ͽ� ���� ǥ�ø� �����Ͻ� �� �ֽ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�̹��� ����� �����Ͽ� �̹����� ǰ����ǰ ������ ��� �Ͻ� �� �ֽ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">(�ػ�ǰ ������������ �Բ� ����˴ϴ�.)</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

<? include "../_footer.php"; ?>