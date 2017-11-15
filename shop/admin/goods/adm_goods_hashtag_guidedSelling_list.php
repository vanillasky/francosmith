<?php
$location = "상품관리 > 해시태그 가이드 셀링";
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
		해시태그 가이드 셀링
		<span>해시태그를 이용한 가이드 셀링 기능을 사용할 수 있습니다.</span>
		<a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=54')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a>
	</div>

	<div class="guidedSellingList-effect">
		<div class="guidedSellingList-effect-imgArea"><img src="../img/btn__icon.gif" border="0" /></div>
		<div class="guidedSellingList-effect-textArea">
			<div class="guidedSellingList-effect-subject">가이드 셀링이란?</div>
			<div class="guidedSellingList-effect-content">
				<div>나는 청바지를 사고 싶어!” 하는 고객은 청바지 카테고리로 이동할 수 있습니다.</div>
				<div>하지만, “나는 <span style="color: blue;">#많이_먹어도_걱정없는</span> 옷을 사고 싶어!” 하는 고객은… !?</div>

				<div style="margin: 10px 0 10px 0;">가이드 셀링을 이용하면 위와 같은 고객들도 구매자로 전환시킬 수 있습니다.</div>

				<div>가이드 셀링은 고객이 원하는 최적의 상품을 선택할 수 있도록 도와주고,</div>
				<div>고객이 쉽게 상품을 구매할 수 있도록 가이드를 해주는 스마트한 판매직원입니다.</div>
			</div>
		</div>
	</div>

	<div class="guidedSellingList-skinPatchInfo">
		※ 2016년 11월 24일 이전 제작 스킨을 사용하시는 경우 반드시 스킨패치를 적용해야 기능 사용이 가능합니다.
		<a href="http://www.godo.co.kr/customer_center/patch.php?sno=2733" class="extext" style="font-weight:bold" target="_blank"> [패치 바로가기]</a>
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
			<th>번호</th>
			<th>가이드 셀링 이름</th>
			<th>미리보기</th>
			<th>페이지 URL</th>
			<th>위젯 소스</th>
			<th>수정</th>
			<th>삭제</th>
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