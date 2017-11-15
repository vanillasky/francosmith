<?php
$location = "��ǰ���� > �ؽ��±� ���� ����";
include '../_header.php';

$hashtag = Core::loader('hashtag');
$hashtagConfig = $hashtag->getConfig();

if(!$hashtagConfig['hashtag_main_use']) $hashtagConfig['hashtag_main_use'] = 'y';
if(!$hashtagConfig['hashtag_main_display_count']) $hashtagConfig['hashtag_main_display_count'] = '10';
if(!$hashtagConfig['hashtag_main_order_by']) $hashtagConfig['hashtag_main_order_by'] = 'goodsCount';
if(!$hashtagConfig['hashtag_goodsView_use']) $hashtagConfig['hashtag_goodsView_use'] = 'y';
if(!$hashtagConfig['hashtag_goodsView_order_by']) $hashtagConfig['hashtag_goodsView_order_by'] = 'goodsCount';
if(!$hashtagConfig['hashtag_goodsView_user_write']) $hashtagConfig['hashtag_goodsView_user_write'] = 'y';
if(!$hashtagConfig['hashtag_goodsList_use']) $hashtagConfig['hashtag_goodsList_use'] = 'y';
if(!$hashtagConfig['hashtag_goodsList_display_count']) $hashtagConfig['hashtag_goodsList_display_count'] = '2';
if(!$hashtagConfig['hashtag_goodsList_order_by']) $hashtagConfig['hashtag_goodsList_order_by'] = 'goodsCount';

$checked['hashtag_main_use'][$hashtagConfig['hashtag_main_use']] = "checked='checked'";
$checked['hashtag_main_order_by'][$hashtagConfig['hashtag_main_order_by']] = "checked='checked'";
$checked['hashtag_goodsView_use'][$hashtagConfig['hashtag_goodsView_use']] = "checked='checked'";
$checked['hashtag_goodsView_order_by'][$hashtagConfig['hashtag_goodsView_order_by']] = "checked='checked'";
$checked['hashtag_goodsView_user_write'][$hashtagConfig['hashtag_goodsView_user_write']] = "checked='checked'";
$checked['hashtag_goodsList_use'][$hashtagConfig['hashtag_goodsList_use']] = "checked='checked'";
$checked['hashtag_goodsList_order_by'][$hashtagConfig['hashtag_goodsList_order_by']] = "checked='checked'";
$selected['hashtag_main_display_count'][$hashtagConfig['hashtag_main_display_count']] = "selected='selected'";
$selected['hashtag_goodsList_display_count'][$hashtagConfig['hashtag_goodsList_display_count']] = "selected='selected'";
?>
<script type="text/javascript" src="<?php echo $cfg['rootDir']; ?>/proc/hashtag/hashtagControl.js?actTime=<?php echo time(); ?>"></script>
<script type="text/javascript" src="../godo_ui.js?ts=<?php echo date('Ym'); ?>"></script>
<style>
div.tooltip {width:720px;padding:0;margin:0;}
.hashtagConfig-layout { width: 1000px; }
.hashtagConfig-layout .hashtagConfig-marginTop5 { margin-top: 5px; }
.hashtagConfig-layout .hashtag-skinPatchInfo { border: 1px solid #cccccc; height: 30px; line-height:30px; margin-bottom: 10px; width: 99%; padding: 3px; color: red; font-weight: bold;}
.hashtagConfig-layout .hashtagConfig-save-button { width: 100%; margin-top: 10px; text-align: center; }
.hashtagConfig-layout .hashtagConfig-buttonLayout { margin-top: 30px; text-align: center; }

.hashtagConfig-layout .hashtagConfig-default-layout {}
.hashtagConfig-layout .hashtagConfig-main-layout,
.hashtagConfig-layout .hashtagConfig-goodsView-layout,
.hashtagConfig-layout .hashtagConfig-goodsList-layout,
.hashtagConfig-layout .hashtagConfig-replaceCode-layout { margin-top: 50px; }
.hashtagConfig-layout .hashtagConfig-fontLink { font-weight: bold; color: #627dce; cursor: pointer; }
</style>

<div class="hashtagConfig-layout">
	<!-- �⺻ �ؽ��±� ���� -->
	<div class="hashtagConfig-default-layout">
		<div class="title title_top">
			�⺻ �ؽ��±� ����
			<span>��ǰ�� ��ϵ� Ư�� �׸��� �ϰ������� �ؽ��±׷� ����� �� �ֽ��ϴ�.</span>
			<a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=52')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a>
		</div>
		
		<div class="hashtag-skinPatchInfo">
			��2016�� 10�� 06�� ���� ���� ��Ų�� ����Ͻô� ��� �ݵ�� ��Ų��ġ�� �����ؾ� ��� ����� �����մϴ�.
			<a href="http://www.godo.co.kr/customer_center/patch.php?sno=2634" class="extext" style="font-weight:bold" target="_blank"> [��ġ �ٷΰ���]</a>
		</div>

		<table class="tb">
			<colgroup>
				<col class="cellC" />
				<col class="cellL" />
			</colgroup>
			<tbody>
			<tr>
				<td>�⺻ �ؽ��±� ����</td>
				<td>
					<div>
						<input type="checkbox" name="brand" value="y" /> �귣���
						&nbsp;
						<input type="checkbox" name="keyword" value="y" /> ����˻���
						&nbsp;
						<input type="checkbox" name="category" value="y" /> ��ǰī�װ� (��ϵ� ������ ī�װ��� �߰���)
					</div>
					<div class="extext hashtagConfig-marginTop5">üũ �� [üũ�� �׸��� �ؽ��±׷� �߰��ϱ�]��ư Ŭ�� �� ���� �׸����� ���� ��ϵ� ��ǰ�� �ؽ��±װ� �߰��˴ϴ�.</div>
				</td>
			</tr>
			</tbody>
		</table>

		<div class="hashtagConfig-save-button"><img src="../img/btn_hashtag_migration.gif" id="hashtagMigragionBtn" class="hand" /></div>
	</div>
	<!-- �⺻ �ؽ��±� ���� -->

	<form name="hashtagConfigForm" id="hashtagConfigForm" action="./adm_goods_hashtag_indb.php" method="post" target="ifrmHidden">
	<input type="hidden" name="mode" id="mode" value="" />

	<!-- ���� ������ �ؽ��±� ���� ���� -->
	<div class="hashtagConfig-main-layout">
		<div class="title title_top">
			���� ������ �ؽ��±� ���� ����
			<span>���� �������� ���θ��� ��ϵ� �ؽ��±� ���� ������ �� �� �ֽ��ϴ�.</span>
			<a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=52')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a>
		</div>

		<table class="tb">
			<colgroup>
				<col class="cellC" />
				<col class="cellL" />
			</colgroup>
			<tbody>
			<tr>
				<td>���� ����</td>
				<td>
					<input type="radio" name="hashtag_main_use" value="y" <?php echo $checked['hashtag_main_use']['y']; ?> /> ������
					&nbsp;
					<input type="radio" name="hashtag_main_use" value="n" <?php echo $checked['hashtag_main_use']['n']; ?> /> �������
					&nbsp;
					<span class="extext">���� �������� ���θ��� ��ϵ� �ؽ��±� ����Ʈ�� �����մϴ�.</span>
					&nbsp;<img src="../img/icons/icon_qmark.gif" style="vertical-align:middle;cursor:pointer;" class="godo-tooltip" tooltip="<img src=&quot;../img/hashtag_info_image1.png&quot; border=0 />">
				</td>
			</tr>
			<tr>
				<td>���� ���� ����</td>
				<td>
					<select name="hashtag_main_display_count">
						<?php for($i=3; $i<=30; $i++){ ?>
						<option value="<?php echo $i; ?>" <?php echo $selected['hashtag_main_display_count'][$i]; ?>><?php echo $i; ?></option>
						<?php } ?>
					</select>
					<span class="extext">����� �ؽ��±��� ������ �����մϴ�.</span>
				</td>
			</tr>
			<tr>
				<td>���� ���ؼ���</td>
				<td>
					<div>
						<input type="radio" name="hashtag_main_order_by" value="goodsCount" <?php echo $checked['hashtag_main_order_by']['goodsCount']; ?> /> ��ǰ��ϼ���
						&nbsp;
						<input type="radio" name="hashtag_main_order_by" value="newRegister" <?php echo $checked['hashtag_main_order_by']['newRegister']; ?> /> �ֱٵ�ϼ�
						&nbsp;
						<input type="radio" name="hashtag_main_order_by" value="name" <?php echo $checked['hashtag_main_order_by']['name']; ?> /> ��������
						&nbsp;
						<input type="radio" name="hashtag_main_order_by" value="user" <?php echo $checked['hashtag_main_order_by']['user']; ?> /> ����ڼ���
						&nbsp;&nbsp;
						<span class="hashtagConfig-fontLink hashtagDisplayPopupBtn">[�����ϱ⢺]</span>
					</div>
					<div class="extext hashtagConfig-marginTop5">���� �������� �ؽ��±� ���� �� ���� ������ �����մϴ�. ����ڼ����� [�����ϱ⢺] ���� ������ ������ �����ϴ�.</div>
				</td>
			</tr>
			</tbody>
		</table>
	</div>
	<!-- ���� ������ �ؽ��±� ���� ���� -->

	<!-- ��ǰ �� ������ �ؽ��±� ���� ���� -->
	<div class="hashtagConfig-goodsView-layout">
		<div class="title title_top">
			��ǰ �� ������ �ؽ��±� ���� ����
			<span>��ǰ �� ���������� ��ǰ�� ��ϵ� �ؽ��±� ���� ������ �� �� �ֽ��ϴ�.</span>
			<a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=52')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a>
		</div>

		<table class="tb">
			<colgroup>
				<col class="cellC" />
				<col class="cellL" />
			</colgroup>
			<tbody>
			<tr>
				<td>���� ����</td>
				<td>
					<input type="radio" name="hashtag_goodsView_use" value="y" <?php echo $checked['hashtag_goodsView_use']['y']; ?> /> ������
					&nbsp;
					<input type="radio" name="hashtag_goodsView_use" value="n" <?php echo $checked['hashtag_goodsView_use']['n']; ?> /> �������
					&nbsp;
					<span class="extext">��ǰ �� �������� ��ǰ�� ��ϵ� �ؽ��±� ����Ʈ�� �����մϴ�.(�ִ� 10��)</span>
					&nbsp;<img src="../img/icons/icon_qmark.gif" style="vertical-align:middle;cursor:pointer;" class="godo-tooltip" tooltip="<img src=&quot;../img/hashtag_info_image2.png&quot; border=0 />">
				</td>
			</tr>
			<tr>
				<td>���� ���ؼ���</td>
				<td>
					<div>
						<input type="radio" name="hashtag_goodsView_order_by" value="goodsCount" <?php echo $checked['hashtag_goodsView_order_by']['goodsCount']; ?> /> ��ǰ��ϼ���
						&nbsp;
						<input type="radio" name="hashtag_goodsView_order_by" value="newRegister" <?php echo $checked['hashtag_goodsView_order_by']['newRegister']; ?> /> �ֱٵ�ϼ�
						&nbsp;
						<input type="radio" name="hashtag_goodsView_order_by" value="name" <?php echo $checked['hashtag_goodsView_order_by']['name']; ?> /> ��������
						&nbsp;
						<input type="radio" name="hashtag_goodsView_order_by" value="user" <?php echo $checked['hashtag_goodsView_order_by']['user']; ?> /> ����ڼ���
					</div>
					<div class="extext hashtagConfig-marginTop5">��ǰ �� �������� �ؽ��±� ���� �� ���� ������ �����մϴ�. ����ڼ����� <a href="./adm_goods_form.php" target="_blank" class="hashtagConfig-fontLink">[��ǰ > ��ǰ���]</a> ���������� ��ǰ���� ������ ������ �����ϴ�.</div>
				</td>
			</tr>
			<tr>
				<td>�� �ؽ��±�<br />�Է� ����</td>
				<td>
					<div>
						<input type="radio" name="hashtag_goodsView_user_write" value="y" <?php echo $checked['hashtag_goodsView_user_write']['y']; ?> /> ���
						&nbsp;
						<input type="radio" name="hashtag_goodsView_user_write" value="n" <?php echo $checked['hashtag_goodsView_user_write']['n']; ?> /> ������
					</div>
					<div class="extext hashtagConfig-marginTop5">
						��ǰ �� ���������� ���� �ش� ��ǰ�� ��︮�� �ؽ��±׸� ���� �߰��� �� �ִ� ����� ��뿩�θ� �����մϴ�.
						�����κ��� �ؽ��±׸� �����ϸ�, ��ǰ�� ��ϵ� �ؽ��±� �����͸� �ø� �� �����Ƿ� ���� �����ϰ� �ؽ��±� ����� ����Ͻ� �� �ֽ��ϴ�.
					</div>
				</td>
			</tr>
			</tbody>
		</table>
	</div>
	<!-- ��ǰ �� ������ �ؽ��±� ���� ���� -->

	<!-- ��ǰ ����Ʈ �ؽ��±� ���� ���� -->
	<div class="hashtagConfig-goodsList-layout">
		<div class="title title_top">
			��ǰ ����Ʈ �ؽ��±� ���� ����
			<span>��ǰ ����Ʈ�� ��ǰ�� ��ϵ� �ؽ��±� ���� ������ �� �� �ֽ��ϴ�.</span>
			<a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=52')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a>
		</div>

		<table class="tb">
			<colgroup>
				<col class="cellC" />
				<col class="cellL" />
			</colgroup>
			<tbody>
			<tr>
				<td>���� ����</td>
				<td>
					<input type="radio" name="hashtag_goodsList_use" value="y" <?php echo $checked['hashtag_goodsList_use']['y']; ?> /> ������
					&nbsp;
					<input type="radio" name="hashtag_goodsList_use" value="n" <?php echo $checked['hashtag_goodsList_use']['n']; ?> /> �������
					&nbsp;
					<span class="extext">����, �з�, �˻�, �̺�Ʈ ������ ��ǰ����Ʈ�� �ؽ��±� ���⿩�θ� �����մϴ�.</span>
					&nbsp;<img src="../img/icons/icon_qmark.gif" style="vertical-align:middle;cursor:pointer;" class="godo-tooltip" tooltip="<img src=&quot;../img/hashtag_info_image3.png&quot; border=0 />">
				</td>
			</tr>
			<tr>
				<td>���� ���� ����</td>
				<td>
					<select name="hashtag_goodsList_display_count">
						<?php for($i=1; $i<=10; $i++){ ?>
						<option value="<?php echo $i; ?>" <?php echo $selected['hashtag_goodsList_display_count'][$i]; ?>><?php echo $i; ?></option>
						<?php } ?>
					</select>
					<span class="extext">��ǰ����Ʈ�� ����� �ؽ��±��� ������ �����մϴ�.</span>
				</td>
			</tr>
			<tr>
				<td>���� ���ؼ���</td>
				<td>
					<div>
						<input type="radio" name="hashtag_goodsList_order_by" value="goodsCount" <?php echo $checked['hashtag_goodsList_order_by']['goodsCount']; ?> /> ��ǰ��ϼ���
						&nbsp;
						<input type="radio" name="hashtag_goodsList_order_by" value="newRegister" <?php echo $checked['hashtag_goodsList_order_by']['newRegister']; ?> /> �ֱٵ�ϼ�
						&nbsp;
						<input type="radio" name="hashtag_goodsList_order_by" value="name" <?php echo $checked['hashtag_goodsList_order_by']['name']; ?> /> ��������
						&nbsp;
						<input type="radio" name="hashtag_goodsList_order_by" value="user" <?php echo $checked['hashtag_goodsList_order_by']['user']; ?> /> ����ڼ���
					</div>
					<div class="extext hashtagConfig-marginTop5">��ǰ ����Ʈ�� �ؽ��±� ���� �� ���� ������ �����մϴ�. ����ڼ����� <a href="./adm_goods_form.php" target="_blank" class="hashtagConfig-fontLink">[��ǰ > ��ǰ���]</a> ���������� ��ǰ���� ������ ������ �����ϴ�</div>
				</td>
			</tr>
			</tbody>
		</table>
	</div>
	<!-- ��ǰ ����Ʈ �ؽ��±� ���� ���� -->

	<!-- �ؽ��±� ġȯ�ڵ� ���� -->
	<div class="hashtagConfig-replaceCode-layout">
		<div class="title title_top">
			�ؽ��±� ġȯ�ڵ� ����
			<span>���θ� �ؽ��±׸� ġȯ�ڵ� ���·� �����մϴ�.</span>
			<a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=52')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a>
		</div>

		<table class="tb">
			<colgroup>
				<col class="cellC" />
				<col class="cellL" />
			</colgroup>
			<tbody>
			<tr>
				<td>�ؽ��±� ����Ʈ<br />ġȯ�ڵ�</td>
				<td>
					<div>{p.hashtagCode->displayHashtag(�ɼ�,���ⰳ��)}</div>
					<div class="extext hashtagConfig-marginTop5">���ϴ� �������� �ؽ��±� ����Ʈ�� ����ǵ��� ġȯ�ڵ带 �����Ͻ� �� �ֽ��ϴ�.</div>
					<div class="extext">�ɼ�: 1 ��ǰ��ϼ���, 2 �ֱٵ�ϼ�, 3 ��������, 4. ����ڼ���&nbsp;&nbsp;<span class="hashtagConfig-fontLink hashtagDisplayPopupBtn">[�����ϱ⢺]</span></div>
					<div class="extext">���ⰳ��: ������ ���ϴ� ġȯ�ڵ��� ������ �־��ּ���. (�ִ�: 50��)</div>
					<div class="extext">��) ��ǰ��ϼ������� 10���� �ؽ��±׸� �����ϰ��� �ϴ� ��� : {ġȯ�ڵ�(1,10)}</div>
				</td>
			</tr>
			</tbody>
		</table>
	</div>
	<!-- �ؽ��±� ġȯ�ڵ� ���� -->

	<div class="button hashtagConfig-buttonLayout">
		<img src="../img/btn_save.gif" border="0" style="cursor: pointer;" id="hashtagConfig_submitImg" />
		<a href="javascript:history.back(-1);"><img src="../img/btn_cancel.gif" border="0" /></a>
	</div>

	</form>
</div>

<script type="text/javascript">
jQuery(document).ready(HashtagConfigController);
</script>
<?php include '../_footer.php'; ?>