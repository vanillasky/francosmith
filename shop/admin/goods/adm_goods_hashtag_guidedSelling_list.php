<?php
$location = "��ǰ���� > �ؽ��±� ���̵� ����";
$colspan='7';

include '../_header.php';
include '../../lib/page.class.php';

$mobileShoConfigpPage = '../../conf/config.mobileShop.php';
if(is_file($mobileShoConfigpPage)){
	include $mobileShoConfigpPage;
}

$guidedSelling = Core::loader('guidedSelling');

list($total) = $db->fetch("SELECT COUNT(*) FROM ".GD_GUIDEDSELLING);

if(!$_GET['page_num']) $_GET['page_num'] = 10;

$pg = new Page($_GET['page'], $_GET['page_num']);
$pg->field = "*";
$pg->setQuery(GD_GUIDEDSELLING, '', 'guided_no DESC');
$pg->exec();
$res = $db->query($pg->query);
?>
<link href="./css/adm_goods_guidedSelling.css?v=20161124" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?php echo $cfg['rootDir']; ?>/proc/guidedSelling/guidedSellingControl.js?actTime=<?php echo time(); ?>"></script>

<input type="hidden" name="shopRootDir" id="shopRootDir" value="<?php echo $cfg['rootDir']; ?>" />
<input type="hidden" name="mobileShopRootDir" id="mobileShopRootDir" value="<?php echo $cfgMobileShop['mobileShopRootDir']; ?>" />
<div class="guidedSellingList-layout">
	<div class="title title_top">
		�ؽ��±� ���̵� ����
		<span>�ؽ��±׸� �̿��� ���̵� ���� ����� ����� �� �ֽ��ϴ�.</span>
		<a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=54')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a>
	</div>

	<div class="guidedSellingList-effect">
		<div class="guidedSellingList-effect-imgArea"><img src="../img/btn__icon.gif" border="0" /></div>
		<div class="guidedSellingList-effect-textArea">
			<div class="guidedSellingList-effect-subject">���̵� �����̶�?</div>
			<div class="guidedSellingList-effect-content">
				<div>���� û������ ��� �;�!�� �ϴ� ���� û���� ī�װ��� �̵��� �� �ֽ��ϴ�.</div>
				<div>������, ������ <span style="color: blue;">#����_�Ծ_��������</span> ���� ��� �;�!�� �ϴ� ������ !?</div>

				<div style="margin: 10px 0 10px 0;">���̵� ������ �̿��ϸ� ���� ���� ���鵵 �����ڷ� ��ȯ��ų �� �ֽ��ϴ�.</div>

				<div>���̵� ������ ���� ���ϴ� ������ ��ǰ�� ������ �� �ֵ��� �����ְ�,</div>
				<div>���� ���� ��ǰ�� ������ �� �ֵ��� ���̵带 ���ִ� ����Ʈ�� �Ǹ������Դϴ�.</div>
			</div>
		</div>
	</div>

	<div class="guidedSellingList-skinPatchInfo">
		�� 2016�� 11�� 24�� ���� ���� ��Ų�� ����Ͻô� ��� �ݵ�� ��Ų��ġ�� �����ؾ� ��� ����� �����մϴ�.
		<a href="http://www.godo.co.kr/customer_center/patch.php?sno=2733" class="extext" style="font-weight:bold" target="_blank"> [��ġ �ٷΰ���]</a>
	</div>

	<div class="guidedSellingList-write">
		<a href="./adm_goods_hashtag_guidedSelling_write.php"><img src="../img/btn_guidedSelling_create.png" border="0" class="hand" id="guidedSellingWriteBtn" /></a>
	</div>

	<div class="guidedSellingList-listArea">
		<table width="100%" cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td class="rnd" colspan="<?php echo $colspan; ?>"></td>
		</tr>
		<tr class="rndbg">
			<th>��ȣ</th>
			<th>���̵� ���� �̸�</th>
			<th>�̸�����</th>
			<th>������ URL</th>
			<th>���� �ҽ�</th>
			<th>����</th>
			<th>����</th>
		</tr>
		<tr>
			<td class="rnd" colspan="<?php echo $colspan; ?>"></td>
		</tr>
		<?php
			while($data = $db->fetch($res, 1)){
		?>
		<tr><td height="4" colspan="<?php echo $colspan; ?>"></td></tr>
		<tr height="25">
			<td><?php echo $pg->idx--; ?></td>
			<td><?php echo $data['guided_subject']; ?></td>
			<td>
				<a href="../../goods/goods_guidedSelling_list.php?guided_no=<?php echo $data['guided_no']; ?>&step=1" target="_blank"><img src="../img/btn_pc.png" border="0" class="hand" /></a>
				&nbsp;
				<a href="<?php echo $cfgMobileShop['mobileShopRootDir']; ?>/goods/goods_guidedSelling_list.php?guided_no=<?php echo $data['guided_no']; ?>&step=1" target="_blank"><img src="../img/btn_mobile.png" border="0" class="hand" /></a>

			</td>
			<td>
				<img src="../img/btn_pc.png" border="0" class="hand guidedSellingCopyUrl" data-no="<?php echo $data['guided_no']; ?>" />
				&nbsp;
				<img src="../img/btn_mobile.png" border="0" class="hand guidedSellingCopyMobileUrl" data-no="<?php echo $data['guided_no']; ?>" />
			</td>
			<td><img src="../img/btn_create_source.png" border="0" class="hand guidedSellingCreateWidget" data-no="<?php echo $data['guided_no']; ?>" /></td>
			<td><img src="../img/buttons/btn_modify_small.gif" border="0" class="hand guidedSellingModify" data-no="<?php echo $data['guided_no']; ?>" /></td>
			<td><img src="../img/i_del.gif" border="0" class="hand guidedSellingDelete" data-no="<?php echo $data['guided_no']; ?>" /></td>
		</tr>
		<tr><td height="4" colspan="<?php echo $colspan; ?>"></td></tr>
		<tr><td colspan="<?php echo $colspan; ?>" class="rndline"></td></tr>
		<?php
			}
		?>
		</table>

		<div align="center" class="pageNavi"><font class="ver8"><?php echo $pg->page['navi']; ?></font></div>
	</div>

</div>

<script type="text/javascript">
jQuery(document).ready(GuidedSellingListController);
</script>
<?php include '../_footer.php'; ?>