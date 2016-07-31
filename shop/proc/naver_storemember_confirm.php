<?
	include_once "../lib/library.php";
	include "../conf/naverCheckout.cfg.php";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<title>쇼핑몰 회원 확인 :: 개인정보 제공동의</title>
<link type="text/css" rel="stylesheet" href="./naver_material/css/checkout_store.css">
<script type="text/javascript" src="./naver_material/js/checkout_store.js"></script>
</head>

<body>
<div id="pop_wrap" class="w410">
<form name="agrForm" method="post" action="indb.naver.php" onsubmit="return checkAgreeForm(this)">
	<input type="hidden" name="mode" id="mode" value="infoSupplyAgreement" />
	<div id="pop_header">
		<h1><img src="./naver_material/img/store/h1_shopconfirm.gif" width="99" height="16" alt="쇼핑몰 회원확인"></h1>
		<dl class="store_logo">
		<dt><img src="./naver_material/img/store/text.gif" width="40" height="13" alt="쇼핑몰 :"></dt>
		<dd><a href="http://<?=$_SERVER['HTTP_HOST']?>" target="_blank"><img src="<?=$_SESSION['NCINFO']['NCMallLogo']?>" width="93" height="25" alt="<?=$_SESSION['NCINFO']['NCMallName']?>" /></a></dd>
		</dl>
	</div>
	<div id="pop_content">
		<dl class="confirm_text2">
		<dt class="blind">개인정보 제공에 동의해 주세요!</dt>
		<dd class="blind">체크아웃 쇼핑몰 회원 구매이용을 위해 개인정보 제공에 동의해주세요.</dd>
		</dl>
		<div class="section3">
			<dl>
			<dt><img src="./naver_material/img/store/text_agree.gif" width="108" height="11" alt="고유식별 정보 제공 안내"></dt>
			<dd>
				<p class="lh16"><?=$cfg['compName']?>에서는 회원구매 혜택 제공을 위한, 회원 구매 이용내역 확인 목적으로 고유식<br>
				별정보(주민등록번호)를 엔에이치엔비즈니스플랫폼(주에서 제공하는 네이버 체크<br>
				아웃에 제공합니다.<br>
				제공하신 주민등록번호는 아이핀 연계정보(CI) 변환을 통해 회원확인이 이루어지며,<br>
				네이버 체크아웃에서는 주민등록번호 자체를 저장하지 않습니다.</p>
			</dd>
			</dl>
		</div>
		<div class="agree_area">
			<input type="checkbox" id="agree" name="agree" value="y" /><label for="agree">고유식별정보 제공에 동의합니다. </label>
		</div>
		<div class="section3">
			<dl>
			<dt><img src="./naver_material/img/store/text_agree2.gif" width="85" height="11" alt="개인정보 제공 안내"></dt>
			<dd>
				<table cellspacing="0" border="1" class="shop_list">
					<col width="150"><col>
					<tbody>
					<tr>
					<th scope="row"><img src="./naver_material/img/store/text_th3.gif" width="76" height="12" alt="개인정보 제공자"></th>
					<td><strong><?=$cfg['compName']?></strong></td>
					</tr>
					<tr>
					<th scope="row"><img src="./naver_material/img/store/text_th4.gif" width="96" height="12" alt="개인정보 제공받는자"></th>
					<td>엔에이치엔비즈니스플랫폼㈜</td>
					</tr>
					</tbody>
					</table>
					<table cellspacing="0" border="1" class="shop_list2">
					<col width="139"><col>
					<tbody>
					<tr class="none">
					<th scope="row">제공하는 개인정보 항목</th>
					<td>회원아이디, 회원명</td>
					</tr>
					<tr>
					<th scope="row">개인정보의 이용목적</th>
					<td>쇼핑몰 - 체크아웃 간 동일 회원 식별,<br>쇼핑몰 회원 구매 이용 내역 식별.</td>
					</tr>
					<tr>
					<th scope="row">개인정보의 보유기간 및<br>이용기간</th>
					<td>네이버 체크아웃 회원 탈퇴시 즉시 삭제, <br>
					단, 관계법령에 의해 보존이 필요한 경우 <br>
					법령에 따라 보관.</td>
					</tr>
					</tbody>
				</table>
			</dd>
			</dl>
		</div>
		<div class="agree_area">
			<input type="checkbox" id="agree2" name="agree2" value="y" /><label for="agree2">개인정보 제공에 동의합니다.</label>
		</div>
	</div>
	<div id="pop_footer">
		<div class="btn_section">
			<input type="image" src="./naver_material/img/store/btn_confirm2.gif" width="60" height="30" alt="확인" />
			<a href="javascript:top.close();"><img src="./naver_material/img/store/btn_close2.gif" width="60" height="30" alt="닫기"></a>
		</div>
		<div class="logo_section">
			<img src="./naver_material/img/store/l_naver.gif" width="39" height="13" alt="NAVER" />
			<img src="./naver_material/img/store/l_checkout.gif" width="58" height="13" alt="Checkout" />
		</div>
	</div>
</form>
</div>
</body>
</html>
