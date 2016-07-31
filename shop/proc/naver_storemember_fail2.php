<?
	include_once "../lib/library.php";
	include "../conf/naverCheckout.cfg.php";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<title>쇼핑몰 회원 확인 :: 실패</title>
<link type="text/css" rel="stylesheet" href="./naver_material/css/checkout_store.css">
</head>

<body>
<div id="pop_wrap" class="w410">
	<div id="pop_header">
		<h1><img src="./naver_material/img/store/h1_shopfail.gif" width="133" height="16" alt="쇼핑몰 회원 확인 실패"></h1>
		<dl class="store_logo">
		<dt><img src="./naver_material/img/store/text.gif" width="40" height="13" alt="쇼핑몰 :"></dt>
		<dd><a href="http://<?=$_SERVER['HTTP_HOST']?>" target="_blank"><img src="<?=$_SESSION['NCINFO']['NCMallLogo']?>" width="93" height="25" alt="<?=$_SESSION['NCINFO']['NCMallName']?>" /></a></dd>
		</dl>
	</div>
	<div id="pop_content">
		<dl class="confirm_text">
		<dt><img src="./naver_material/img/store/text_cont2.gif" width="216" height="16" alt="쇼핑몰 회원 확인이 실패 하였습니다!"></dt>
		<dd><img src="./naver_material/img/store/text_info3.gif" width="228" height="25" alt="체크아웃 회원과 쇼핑몰 회원이 일치 하지 않습니다.실제 동일한 회원인 경우에만 서비스 이용이 가능 합니다."></dd>
		</dl>
		<div class="section">
			<table cellspacing="0" border="1" class="shop_list">
			<col width="101"><col>
			<tbody>
			<tr>
			<th scope="row"><img src="./naver_material/img/store/text_th.gif" width="44" height="12" alt="쇼핑몰명"></th>
			<td><a href="/" target="_blank" style="color:#686868;"><strong><?=$_SESSION['NCINFO']['NCMallName']?></strong></a></td>
			</tr>
			<tr>
			<th scope="row"><img src="./naver_material/img/store/text_th2.gif" width="75" height="12" alt="쇼핑몰 고객센터"></th>
			<td><?=$_SESSION['NCINFO']['NCMallPhoneNo']?></td>
			</tr>
			</tbody>
			</table>
		</div>
	</div>
	<div id="pop_footer">
		<div class="btn_section">
			<a href="naver_storemember.php"><img src="./naver_material/img/store/btn_reply.gif" width="63" height="23" alt="다시시도"></a>
			<a href="javascript:top.close();"><img src="./naver_material/img/store/btn_close.gif" width="40" height="23" alt="닫기"></a>
		</div>
		<div class="logo_section">
			<img src="./naver_material/img/store/l_naver.gif" width="39" height="13" alt="NAVER" />
			<img src="./naver_material/img/store/l_checkout.gif" width="58" height="13" alt="Checkout" />
		</div>
	</div>
</div>
</body>
</html>
