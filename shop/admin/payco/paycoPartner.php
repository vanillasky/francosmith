<?php
$location = '페이코 > 페이코 서비스 설정';
include '../_header.php';
@include '../../conf/payco.cfg.php';
@include '../../conf/config.mobileShop.php';
$payco = Core::loader('payco');

if($paycoCfg['e_exceptions']){
	$res = $db->query("
	SELECT
		a.goodsno, a.goodsnm, a.img_s, b.price
	FROM
		" . GD_GOODS . " a,
		" . GD_GOODS_OPTION . " b
	WHERE
		a.goodsno=b.goodsno and link and go_is_deleted <> '1'
		and a.goodsno IN (" . implode(',', $paycoCfg['e_exceptions']) . ")");
	while($tmp = $db->fetch($res)) $e_exceptions[] = $tmp;
}

if(!in_array($paycoCfg['button_checkout'], array('A','B','C'))) $paycoCfg['button_checkout'] = 'C';
if(!$paycoCfg['button_checkoutDetail_A']) $paycoCfg['button_checkoutDetail_A'] = 'A1';
if(!$paycoCfg['button_checkoutDetail_B']) $paycoCfg['button_checkoutDetail_B'] = 'B1';
if(!$paycoCfg['button_checkoutDetail_C']) $paycoCfg['button_checkoutDetail_C'] = 'C1';
if(!$paycoCfg['button_easypay']) $paycoCfg['button_easypay'] = 'A1';
if(!$paycoCfg['useYn']) $paycoCfg['useYn'] = 'all';
if(!$paycoCfg['testYn']) $paycoCfg['testYn'] = 'Y';
if(!$paycoCfg['useType']) $paycoCfg['useType'] = 'CE';

$radioArr = array('useYn', 'useType', 'button_checkout', 'button_checkoutDetail_A', 'button_checkoutDetail_B', 'button_checkoutDetail_C', 'button_easypay', 'testYn');
foreach($radioArr as $name){
	$radio[$name][$paycoCfg[$name]] = "checked='checked'";
}
if($cfgMobileShop['vtype_goods_view_skin'] == '1'){
	$viewPage = 'view2';
	$viewDetailPage = 'view_detail2';
}
else {
	$viewPage = 'view';
	$viewDetailPage = 'view_detail';
}
$mobileShopDesignUrl_goods = '../' . $mobileShop . '/codi.php?design_file=goods/'.$viewPage.'.htm';
$mobileShopDesignUrl_cart = '../' . $mobileShop . '/codi.php?design_file=goods/cart.htm';
$mobileShopDesignUrl_order = '../' . $mobileShop . '/codi.php?design_file=ord/order.htm';
if($mobileShop == 'mobileShop2'){
	$mobileShopDesignUrl_goodsDetails = '../mobileShop2/codi.php?design_file=goods/'.$viewDetailPage.'.htm';
	$mobileShopDesignUrl_viewGoods = '../mobileShop2/codi.php?design_file=myp/viewgoods.htm';
}
?>
<script type="text/javascript" src="./paycoAdminControl.js"></script>
<script type="text/javascript" src="../godo.loading.indicator.js"></script>
<script type="text/javascript">
window.onload = function(){
	//간편구매(체크아웃)버튼 display
	changeButtonType('<?php echo $paycoCfg[button_checkout]; ?>');
	defaultTextValSetting('set', '');
	cssRound('MSG01');
};
</script>
<style type="text/css">
img										{ border: 0px; }
.paycoLayout							{ width: 1000px; }
.payco_formTable						{ background-color:#A6A6A6; }
.payco_formTable td						{ padding-left: 5px; height: 30px; }
.payco_BgColorGray2						{ background-color:#EAEAEA; }
.payco_BgColorWhite						{ background-color:#FFFFFF; }
.payco_marginTop						{ margin-top: 50px; }
.payco_guide							{ font-family: Dotum; color:#627dce; font-size: 11px; margin-top: 5px; }
.payco_borderZ							{ border: 0px;}
.payco_ButtonTypeMargin					{ margin-top: 10px; }
.payco_checkoutButton					{ margin: 25px 0px 0px 20px; }
.payco_ValignBttom						{ vertical-align: bottom; }
.paycoInfo								{ width: 1000px; margin: 0px 0px 30px 0px; border: 3px #dce1e1 solid; padding: 5px; }
.paycoInfo .sub_paycoInfo				{ background-color: #989898; }
.paycoInfo .sub_paycoInfo tr 			{ background-color: #FFFFFF; height: 50px; }
.paycoInfo .sub_paycoInfo tr .firstTd	{ background-color: #989898; text-align: center; color: #FFFFFF;}
.paycoInfo .sub_paycoInfo tr .lastTd	{ padding-left: 5px; }
.payco_IDTextBox						{ margin: 3px 0px 3px 0px; width: 300px; height: 28px; }
.payco_textInputLayout					{ background-color: #FFFFFF; }
.payco_textInputLayout tr				{ background-color: #FFFFFF; height: 40px; }
.payco_textInputLayout tr .firstTd		{ background-color: #EAEAEA; width: 120px; }
.paycoTab								{ background-color: #FFFFFF; width: 100%; border: 1px #627DCE solid; border-width: 0px 0px 1px 1px; height: 46px; color: #627DCE; font-size:14px; margin-top: 50px; }
.paycoTab div							{ float: left; width: 210px; height: 46px; margin: 0 auto; line-height: 46px; border: 1px #627DCE solid; border-width: 1px 1px 0px 0px; text-align: center; cursor: pointer; font-weight: bold; }
.paycoInputRadio1						{ border: 0px; vertical-align: top; margin-top: 35px; }
.paycoInputRadio2						{ border: 0px; vertical-align: top; margin-top: 115px; }
.payco_checkoutDetailSpace				{ margin-right: 20px; }
.payco_deisignLink						{ text-decoration: underline; color: #627dce; }
.payco_validationMsg					{ margin-left: 5px; font-family: Dotum; color:#627dce; font-size: 11px; vertical-align: middle;}
</style>

<div class="paycoLayout">

<div class="title title_top">페이코 서비스 설정</div>

<table cellpadding="0" cellspacing="2" width="100%" border="0" class="paycoInfo">
<tr>
	<td class="payco_BgColorWhite">
		<strong>페이코 서비스란?</strong><br />
		NHN 엔터테인먼트에서 제공하는 결제대행 서비스입니다.  간편구매 서비스와 간편결제 서비스를 제공합니다.<br />
		<table cellpadding="5" cellspacing="0" border="0" width="100%">
		<tr>
			<td>
				<table cellpadding="0" cellspacing="1" border="0" width="450" class="sub_paycoInfo">
				<tr>
					<td width="70" class="firstTd">간편구매</td>
					<td class="lastTd">
						<div>- 페이코 ID로 상품주문(쇼핑몰 비회원 구매)</div>
						<div>- 페이코의 결제수단으로 주문</div>
						<div>- 신용카드, 계좌이체, 가상계좌, 휴대폰결제 지원</div>
					</td>
				</tr>
				</table>
			</td>
			<td>
				<table cellpadding="0" cellspacing="1" border="0" width="450" class="sub_paycoInfo">
				<tr>
					<td width="70" class="firstTd">간편결제</td>
					<td class="lastTd">
						<div>- 쇼핑몰 ID나 비회원으로 구매</div>
						<div>- 기존의 결제수단과 함께 사용 가능</div>
						<div>- 신용카드, 휴대폰결제 지원</div>
					</td>
				</tr>
				</table>
			</td>
		</tr>
		</table>
		자세한 내용은 서비스 안내를 참고하여 주세요.&nbsp;<a href="./paycoInfo.php" target="_blank"><img src="../img/btn_go.gif" border="0" class="payco_borderZ payco_ValignBttom" /></a>
	</td>
</tr>
</table>

<div class="paycoTab">
	<div onclick="javascript:location.href='#part1';" style="cursor: pointer;">페이코 서비스 연동 설정</div>
	<div onclick="javascript:location.href='#part2';" style="cursor: pointer;">페이코 서비스 이용 설정</div>
	<div onclick="javascript:location.href='#part3';" style="cursor: pointer;">페이코 서비스 상품 설정</div>
</div>

<form name="paycoServiceForm" id="paycoServiceForm" method="post">

<div class="title payco_ButtonTypeMargin" id="part1">페이코 서비스 연동 설정 &nbsp;<a href="http://partner.payco.com" target="_blank"><img src="../img/btn_payco_partner.gif" class="payco_borderZ" border="0" align="absmiddle" hspace="2" /></a>&nbsp;<a href="javascript:manual('<?=$guideUrl?>board/view.php?id=marketing&no=33')"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2" /></a></div>
<table cellpadding="0" cellspacing="1" width="100%" border="0" class="payco_formTable">
<colgroup>
	<col width="150px" />
	<col width="*" />
</colgroup>
<tr>
	<td class="payco_BgColorGray2">페이코 서비스 선택</td>
	<td class="payco_BgColorWhite">
		<div class="payco_guide">이용할 페이코 서비스 종류를 선택해주세요.</div>
		<div>
			<input type="radio" name="useType" value="CE" class="payco_borderZ" required fld_esssential msgR="서비스 종류를 선택해 주세요." <?php echo $radio['useType']['CE']; ?>/> 페이코 간편구매 + 페이코 간편결제
			&nbsp;
			<input type="radio" name="useType" value="E" class="payco_borderZ" required fld_esssential msgR="서비스 종류를 선택해 주세요." <?php echo $radio['useType']['E']; ?>/> 페이코 간편결제
			&nbsp;
			<input type="radio" name="useType" value="N" class="payco_borderZ" required fld_esssential msgR="서비스 종류를 선택해 주세요." <?php echo $radio['useType']['N']; ?>/> 사용안함
		</div>
	</td>
</tr>
<tr>
	<td class="payco_BgColorGray2">페이코 사용 설정</td>
	<td class="payco_BgColorWhite">
		<table width="100%" cellpadding="3" cellspacing="0" border="0">
		<tr>
			<td>
				<div class="payco_guide">'테스트하기'를 선택하면 결제버튼이 관리자 로그인 시에만 보여지며, 쇼핑몰에서 결제 시 구매 과정 및 실제 결제는 동일하게 처리됩니다.</div>
				<div>
					<input type="radio" name="testYn" value="Y" class="payco_borderZ" required fld_esssential msgR="테스트 설정을 선택해 주세요." <?php echo $radio['testYn']['Y']; ?> /> 테스트하기
					&nbsp;
					<input type="radio" name="testYn" value="N" class="payco_borderZ" required fld_esssential msgR="테스트 설정을 선택해 주세요." <?php echo $radio['testYn']['N']; ?> /> 실제 사용하기
				</div>
			</td>
		</tr>
		</table>
	</td>
</tr>
<tr>
	<td class="payco_BgColorGray2">페이코 서비스 설정</td>
	<td class="payco_BgColorWhite">
			<div class="payco_guide">페이코에서 받은 가맹점코드와 상점ID를 입력해주세요</div>
			<div>
				<table cellpadding="0" cellspacing="1" border="0" width="100%" class="payco_textInputLayout">
				<tr>
					<td class="firstTd">가맹점코드</td>
					<td>
						<?if($paycoCfg['auto']) {?>
							<b><?=$paycoCfg['paycoSellerKey'];?></b>
						<span class="payco_guide"><b>자동설정 완료</b></span>
						<span id="validateCheckMsg_paycoSellerKey" class="payco_validationMsg"></span>
						<input type="hidden" name="paycoSellerKey" id="paycoSellerKey" value="<?php echo $paycoCfg['paycoSellerKey']; ?>"/>
						<?} else {?>
						<input type="text" name="paycoSellerKey" id="paycoSellerKey" value="<?php echo $paycoCfg['paycoSellerKey']; ?>" required fld_esssential msgR="가맹점코드를 입력해 주세요." class="payco_IDTextBox" onfocus="javascript:defaultTextValSetting('focus', this);" onblur="javascript:defaultTextValSetting('blur', this);" /><span id="validateCheckMsg_paycoSellerKey" class="payco_validationMsg"></span>
						<?}?>
					</td>
				</tr>
				<tr>
					<td class="firstTd">상점ID</td>
					<td>
						<?if($paycoCfg['auto']) {?>
							<b><?=$paycoCfg['paycoCpId'];?></b>
						<span class="payco_guide"><b>자동설정 완료</b></span>
						<span id="validateCheckMsg_paycoCpId" class="payco_validationMsg"></span>
						<input type="hidden" name="paycoCpId" id="paycoCpId" value="<?php echo $paycoCfg['paycoCpId']; ?>"  />
						<?} else {?>
						<input type="text" name="paycoCpId" id="paycoCpId" value="<?php echo $paycoCfg['paycoCpId']; ?>" required fld_esssential msgR="상점ID를 입력해 주세요." onfocus="javascript:defaultTextValSetting('focus', this);" onblur="javascript:defaultTextValSetting('blur', this);" class="payco_IDTextBox" /><span id="validateCheckMsg_paycoCpId" class="payco_validationMsg"></span>
						<?}?>
					</td>
				</tr>
				</table>
			</div>
		</div>
	</td>
</tr>
</table>

<div id="saveId" class="button" style="margin-top:13px;">
	<img src="../img/btn_naver_install.gif" class="payco_borderZ" onclick="javascript:submitSaveID();" style="cursor: pointer;" />
</div>
</form>



<form name="paycoForm" id="paycoForm" method="post" action="paycoIndb.php" onsubmit="return submitSaveService();" target="ifrmHidden">
<input type="hidden" name="mode" value="save" />

<!-- part 2 -->
<div class="title payco_marginTop" id="part2">페이코 서비스 이용 설정</div>
<table cellpadding="0" cellspacing="1" width="100%" border="0" class="payco_formTable">
<colgroup>
	<col width="150px" />
	<col width="*" />
</colgroup>
<tr>
	<td class="payco_BgColorGray2">페이코 이용 영역 선택</td>
	<td class="payco_BgColorWhite">
		<div class="payco_guide">쇼핑몰에서 페이코 이용 영역을 선택하세요.</div>
		<div>
			<input type="radio" name="useYn" value="all" class="payco_borderZ" <?php echo $radio['useYn']['all']; ?>/> PC버전+모바일샵
			&nbsp;
			<input type="radio" name="useYn" value="pc" class="payco_borderZ" <?php echo $radio['useYn']['pc']; ?>/> PC버전
			&nbsp;
			<input type="radio" name="useYn" value="mobile" class="payco_borderZ" <?php echo $radio['useYn']['mobile']; ?>/> 모바일샵
		</div>
	</td>
</tr>
<tr>
	<td class="payco_BgColorGray2">페이코 간편구매<br />버튼 선택</td>
	<td class="payco_BgColorWhite">
		<table width="100%" cellpadding="3" cellspacing="0" border="0">
		<tr>
			<td>
				<div class="payco_guide">‘구매하기’ 버튼은 상품상세 페이지와 장바구니에 노출됩니다. 타입별로 크기와 디자인을 선택할 수 있습니다.<br />쇼핑몰에 노출할 페이코 간편구매 버튼을 선택하고 반드시 스킨에 치환코드를 넣어야 합니다.<br />[PC용] <a href="../design/codi.php?design_file=goods/goods_view.htm" target="_blank"><span class="payco_deisignLink">상품상세</span></a>, <a href="../design/codi.php?design_file=goods/goods_cart.htm" target="_blank"><span class="payco_deisignLink">장바구니</span></a> 메뉴를 클릭, [모바일용] 모바일샵 <a href="<?php echo $mobileShopDesignUrl_goods; ?>" target="_blank"><span class="payco_deisignLink">상품상세</span></a>, <a href="<?php echo $mobileShopDesignUrl_cart; ?>" target="_blank"><span class="payco_deisignLink">장바구니</span></a><?php if($mobileShop == 'mobileShop2'){ ?>, <a href="<?php echo $mobileShopDesignUrl_goodsDetails; ?>" target="_blank"><span class="payco_deisignLink">상품상세보기</span></a>, <a href="<?php echo $mobileShopDesignUrl_viewGoods; ?>" target="_blank"><span class="payco_deisignLink">최근본상품</span></a><?php } ?> 메뉴를 클릭하여, [바로구매] 또는 [주문하기] 버튼 아래에 반드시 삽입/확인하세요.</div>
				<div style="margin-bottom: 5px;">
					{Payco}&nbsp;&nbsp;<img class="hand" src="../img/i_copy.gif" onclick="javascript:copy_txt('{Payco}');" alt="복사하기" align="absmiddle" border="0" />
				</div>
				<div>
					<input type="radio" name="button_checkout" value="A" class="payco_borderZ" onclick="javascript:changeButtonType('A');" <?php echo $radio['button_checkout']['A']; ?>/> A타입 (277px*70px)
					&nbsp;
					<input type="radio" name="button_checkout" value="B" class="payco_borderZ" onclick="javascript:changeButtonType('B');" <?php echo $radio['button_checkout']['B']; ?>/> B타입 (388px*84px)
					&nbsp;
					<input type="radio" name="button_checkout" value="C" class="payco_borderZ" onclick="javascript:changeButtonType('C');" <?php echo $radio['button_checkout']['C']; ?>/> C타입 (296px*84px)
				</div>

				<div id="buttonTypeA" class="payco_checkoutButton" style="display: none;">
					<div>
						<input type="radio" name="button_checkoutDetail_A" value="A1" class="paycoInputRadio1" <?php echo $radio['button_checkoutDetail_A']['A1']; ?>/><img src="<?php echo $payco->getAdminBtnImageUrl('A1', 'png'); ?>" border="0" class="payco_borderZ payco_checkoutDetailSpace" />
						<input type="radio" name="button_checkoutDetail_A" value="A4" class="paycoInputRadio1" <?php echo $radio['button_checkoutDetail_A']['A4']; ?>/><img src="<?php echo $payco->getAdminBtnImageUrl('A4', 'png'); ?>" border="0" class="payco_borderZ" />
					</div>
					<div class="payco_ButtonTypeMargin">
						<input type="radio" name="button_checkoutDetail_A" value="A2" class="paycoInputRadio1" <?php echo $radio['button_checkoutDetail_A']['A2']; ?>/><img src="<?php echo $payco->getAdminBtnImageUrl('A2', 'png'); ?>" border="0" class="payco_borderZ payco_checkoutDetailSpace" />
						<input type="radio" name="button_checkoutDetail_A" value="A5" class="paycoInputRadio1" <?php echo $radio['button_checkoutDetail_A']['A5']; ?>/><img src="<?php echo $payco->getAdminBtnImageUrl('A5', 'png'); ?>" border="0" class="payco_borderZ" />
					</div>
					<div class="payco_ButtonTypeMargin">
						<input type="radio" name="button_checkoutDetail_A" value="A3" class="paycoInputRadio1" <?php echo $radio['button_checkoutDetail_A']['A3']; ?>/><img src="<?php echo $payco->getAdminBtnImageUrl('A3', 'png'); ?>" border="0" class="payco_borderZ payco_checkoutDetailSpace" />
						<input type="radio" name="button_checkoutDetail_A" value="A6" class="paycoInputRadio1" <?php echo $radio['button_checkoutDetail_A']['A6']; ?>/><img src="<?php echo $payco->getAdminBtnImageUrl('A6', 'png'); ?>" border="0" class="payco_borderZ" />
					</div>
				</div>

				<div id="buttonTypeB" class="payco_checkoutButton" style="display: none;">
					<div>
						<input type="radio" name="button_checkoutDetail_B" value="B1" class="paycoInputRadio1" <?php echo $radio['button_checkoutDetail_B']['B1']; ?>/><img src="<?php echo $payco->getAdminBtnImageUrl('B1', 'png'); ?>" border="0" class="payco_borderZ payco_checkoutDetailSpace" />
						<input type="radio" name="button_checkoutDetail_B" value="B4" class="paycoInputRadio1" <?php echo $radio['button_checkoutDetail_B']['B4']; ?>/><img src="<?php echo $payco->getAdminBtnImageUrl('B4', 'png'); ?>" border="0" class="payco_borderZ" />
					</div>
					<div class="payco_ButtonTypeMargin">
						<input type="radio" name="button_checkoutDetail_B" value="B2" class="paycoInputRadio1" <?php echo $radio['button_checkoutDetail_B']['B2']; ?>/><img src="<?php echo $payco->getAdminBtnImageUrl('B2', 'png'); ?>" border="0" class="payco_borderZ payco_checkoutDetailSpace" />
						<input type="radio" name="button_checkoutDetail_B" value="B5" class="paycoInputRadio1" <?php echo $radio['button_checkoutDetail_B']['B5']; ?>/><img src="<?php echo $payco->getAdminBtnImageUrl('B5', 'png'); ?>" border="0" class="payco_borderZ" />
					</div>
					<div class="payco_ButtonTypeMargin">
						<input type="radio" name="button_checkoutDetail_B" value="B3" class="paycoInputRadio1" <?php echo $radio['button_checkoutDetail_B']['B3']; ?>/><img src="<?php echo $payco->getAdminBtnImageUrl('B3', 'png'); ?>" border="0" class="payco_borderZ payco_checkoutDetailSpace" />
						<input type="radio" name="button_checkoutDetail_B" value="B6" class="paycoInputRadio1" <?php echo $radio['button_checkoutDetail_B']['B6']; ?>/><img src="<?php echo $payco->getAdminBtnImageUrl('B6', 'png'); ?>" border="0" class="payco_borderZ" />
					</div>
				</div>

				<div id="buttonTypeC" class="payco_checkoutButton" style="display: none;">
					<div>
						<input type="radio" name="button_checkoutDetail_C" value="C1" class="paycoInputRadio1" <?php echo $radio['button_checkoutDetail_C']['C1']; ?>/><img src="<?php echo $payco->getAdminBtnImageUrl('C1', 'png'); ?>" border="0" class="payco_borderZ payco_checkoutDetailSpace" />
						<input type="radio" name="button_checkoutDetail_C" value="C4" class="paycoInputRadio1" <?php echo $radio['button_checkoutDetail_C']['C4']; ?>/><img src="<?php echo $payco->getAdminBtnImageUrl('C4', 'png'); ?>" border="0" class="payco_borderZ" />
					</div>
					<div class="payco_ButtonTypeMargin">
						<input type="radio" name="button_checkoutDetail_C" value="C2" class="paycoInputRadio1" <?php echo $radio['button_checkoutDetail_C']['C2']; ?>/><img src="<?php echo $payco->getAdminBtnImageUrl('C2', 'png'); ?>" border="0" class="payco_borderZ payco_checkoutDetailSpace" />
						<input type="radio" name="button_checkoutDetail_C" value="C5" class="paycoInputRadio1" <?php echo $radio['button_checkoutDetail_C']['C5']; ?>/><img src="<?php echo $payco->getAdminBtnImageUrl('C5', 'png'); ?>" border="0" class="payco_borderZ" />
					</div>
					<div class="payco_ButtonTypeMargin">
						<input type="radio" name="button_checkoutDetail_C" value="C3" class="paycoInputRadio1" <?php echo $radio['button_checkoutDetail_C']['C3']; ?>/><img src="<?php echo $payco->getAdminBtnImageUrl('C3', 'png'); ?>" border="0" class="payco_borderZ payco_checkoutDetailSpace" />
						<input type="radio" name="button_checkoutDetail_C" value="C6" class="paycoInputRadio1" <?php echo $radio['button_checkoutDetail_C']['C6']; ?>/><img src="<?php echo $payco->getAdminBtnImageUrl('C6', 'png'); ?>" border="0" class="payco_borderZ" />
					</div>
				</div>
			</td>
		</tr>
		</table>
	</td>
</tr>
<tr>
	<td class="payco_BgColorGray2">페이코 간편결제<br />버튼 선택</td>
	<td class="payco_BgColorWhite">
		<table width="100%" cellpadding="3" cellspacing="0" border="0">
		<tr>
			<td>
				<div class="payco_guide">간편결제 버튼은 상품구매 페이지에서 결제수단을 선택할 때 보여집니다.<br />쇼핑몰에 노출할 페이코 간편결제 버튼을 선택하고 반드시 스킨에 치환코드를 넣어야 합니다.<br />[PC용] <a href="../design/codi.php?design_file=order/order.htm" target="_blank"><span class="payco_deisignLink">주문하기</span></a> 메뉴를 클릭 ‘일반결제’ 항목 위쪽에 삽입, [모바일용] 모바일샵 <a href="<?php echo $mobileShopDesignUrl_order; ?>" target="_blank"><span class="payco_deisignLink">주문하기</span></a> 메뉴를 클릭하여 ‘결제수단’ 안쪽에 반드시 삽입/확인하세요.</div>
				<div style="margin-bottom: 5px;">
					{Payco}&nbsp;&nbsp;<img class="hand" src="../img/i_copy.gif" onclick="javascript:copy_txt('{Payco}');" alt="복사하기" align="absmiddle" border="0" />
				</div>
				<div class="payco_ButtonTypeMargin">
					<input type="radio" name="button_easypay" value="A1" class="payco_borderZ" <?php echo $radio['button_easypay']['A1']; ?> />&nbsp;
					<img src="<?php echo $payco->getAdminBtnImageUrl('easypay_A1', 'png'); ?>" border="0" class="payco_borderZ" />
					&nbsp;&nbsp;&nbsp;
					<input type="radio" name="button_easypay" value="A2" class="payco_borderZ" <?php echo $radio['button_easypay']['A2']; ?> />&nbsp;
					<img src="<?php echo $payco->getAdminBtnImageUrl('easypay_A2', 'png'); ?>" border="0" class="payco_borderZ" />
				</div>
			</td>
		</tr>
		</table>
	</td>
</tr>
</table>

<div class="title payco_marginTop" id="part3">페이코 서비스 예외상품 설정</div>
<table cellpadding="0" cellspacing="1" width="100%" border="0" class="payco_formTable">
<colgroup>
	<col width="150px" />
	<col width="*" />
</colgroup>
<tr>
	<td class="payco_BgColorGray2">예외 카테고리 설정</td>
	<td class="payco_BgColorWhite">
		<div class="payco_guide">페이코로 구매할 수 없는 상품 카테고리를 선택하여 주세요. 선택한 카테고리의 상품들은 페이코로 구매할 수 없습니다.</div>
		<div>
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td align="left" style="padding: 0px;">
					<script>new categoryBox('cate[]', 4, '', '', 'paycoForm');</script>
					<a href="javascript:exec_add();"><img src="../img/btn_coupon_cate.gif" /></a>
				</td>
			</tr>
			</table>
		</div>
		<div class="box">
			<table cellpadding="8" cellspacing="0" id="objCategory" bgcolor="f3f3f3" border="0" bordercolor="#cccccc" style="border-collapse:collapse">
			<?php
			if ($paycoCfg['e_category']){
				foreach ($paycoCfg['e_category'] as $k){
			?>
			<tr>
				<td id="currPosition"><?php echo strip_tags(currPosition($k)); ?></td>
				<td><input type="text" name="e_category[]" value="<?php echo $k; ?>" style="display: none;">
				<td><a href="javascript:void(0);" onClick="javascript:cate_del(this.parentNode.parentNode);"><img src="../img/i_del.gif" border="0" align="absmiddle" /></a>
				</td>
			</tr>
			<?php
				}
			}
			?>
			</table>
		</div>
	</td>
</tr>
<tr>
	<td class="payco_BgColorGray2">예외 상품 설정</td>
	<td class="payco_BgColorWhite">
		<div class="payco_guide">페이코로 구매할 수 없는 상품을 선택하여 주세요. 선택한 상품은 페이코로 구매할 수 없습니다.</div>
		<div style="position: relative;">
			<div style="padding:5px 0px 0px 0px;"><img src="../img/btn_goodsChoice.gif" class="hand" onclick="javascript:popupGoodschoice('e_exceptions[]', 'exceptionsX');" align="absmiddle" /> <font class="extext">※주의: 상품선택 후 반드시 하단 등록(수정)버튼을 누르셔야 최종 저장이 됩니다.</font></div>
			<div id="exceptionsX" style="padding-top:3px;">
				<?php
					if ($e_exceptions){
						foreach ($e_exceptions as $v){
				?>
					<a href="../../goods/goods_view.php?goodsno=<?php echo $v['goodsno']; ?>" target="_blank"><?php echo goodsimg($v['img_s'], '40,40', '', 1); ?></a>
					<input type=hidden name="e_exceptions[]" value="<?php echo $v['goodsno']; ?>" />
				<?php
						}
					}
				?>
			</div>
		</div>
	</td>
</tr>
</table>

<div class="button">
	<input type="image" src="../img/btn_save.gif" />
</div>
</form>

<!-- 궁금증해결 -->
<div style="clear:both;" id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class="small_ex">
<tr>
	<td>
		<div>ㆍ페이코 결제 테스트하기</div>
		<div style="margin-left: 6px;">‘테스트하기’를 선택하고 테스트용 페이코 설정값을 넣고 저장합니다.</div>
		<div style="margin-left: 10px;">- 이때 관리자로 로그인 된 상태이면 쇼핑몰 > 상품상세 및 장바구니 > 구매하기 버튼이 보이는지 확인하세요.</div>
		<div style="margin-left: 10px;">- [구매하기] 버튼을 눌러 결제를 진행해보세요. 실제 결제와 동일하게 테스트해볼 수 있습니다.(실제로 결제가 되지는 않음). 테스트에 도움이 필요하시면 페이코 고객센터로 문의하여 주세요.</div>

		<div style="margin-top: 10px;">ㆍ페이코 결제하기</div>
		<div style="margin-left: 10px;">- 테스트 결제를 성공하면 ‘○실제 사용하기’를 선택하고 실제 페이코 설정값을 저장합니다.</div>
		<div style="margin-left: 10px;">- 이때 부터 실제로 페이코 결제가 이루어집니다.</div>

		<div style="margin-top: 10px;">ㆍ페이코 주문건의 현금영수증 발급 안내</div>
		<div style="margin-left: 10px;">- 페이코 주문은 페이코 결제팝업에서 무통장입금(가상계좌) 선택시 구매자가 현금영수증을 발행할 수 있습니다.</div>
	</td>
</tr>
</table>
</div>
<!-- 궁금증해결 -->

</div>
<?php include '../_footer.php'; ?>