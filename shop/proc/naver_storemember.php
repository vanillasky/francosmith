<?
	include_once "../lib/library.php";
	include "../conf/naverCheckout.cfg.php";

	if(!isset($_SESSION['NCINFO'])) session_register("NCINFO");
	// POST로 받아온 값을 session에 저장
	$_SESSION['NCINFO']['NCUserNo']			= (!$_SESSION['NCINFO']['NCUserNo']) ? iconv("UTF-8", "EUC-KR", $_POST['NCUserNo']) : $_SESSION['NCINFO']['NCUserNo'];
	$_SESSION['NCINFO']['NCMallID']			= (!$_SESSION['NCINFO']['NCMallID']) ? iconv("UTF-8", "EUC-KR", $_POST['NCMallID']) : $_SESSION['NCINFO']['NCMallID'];
	$_SESSION['NCINFO']['NCMallName']		= (!$_SESSION['NCINFO']['NCMallName']) ? iconv("UTF-8", "EUC-KR", $_POST['NCMallName']) : $_SESSION['NCINFO']['NCMallName'];
	$_SESSION['NCINFO']['NCMallPhoneNo']	= (!$_SESSION['NCINFO']['NCMallPhoneNo']) ? iconv("UTF-8", "EUC-KR", $_POST['NCMallPhoneNo']) : $_SESSION['NCINFO']['NCMallPhoneNo'];
	$_SESSION['NCINFO']['NCMallLogo']		= (!$_SESSION['NCINFO']['NCMallLogo']) ? urldecode(iconv("UTF-8", "EUC-KR", $_POST['NCMallLogo'])) : $_SESSION['NCINFO']['NCMallLogo'];
	$_SESSION['NCINFO']['Timestamp']		= (!$_SESSION['NCINFO']['Timestamp']) ? iconv("UTF-8", "EUC-KR", $_POST['Timestamp']) : $_SESSION['NCINFO']['Timestamp'];
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<title>쇼핑몰 회원 확인 :: 회원 확인</title>
<link type="text/css" rel="stylesheet" href="./naver_material/css/checkout_store.css">
<script type="text/javascript" src="./naver_material/js/checkout_store.js"></script>
</head>
<body>
<div id="pop_wrap" class="w410">
<form name="shopForm" method="post" action="indb.naver.php" onsubmit="return checkForm(this)">
	<input type="hidden" name="mode" id="mode" value="checkAccount" />
<?
	foreach($_SESSION['NCINFO'] as $k => $v) {
?>
	<input type="hidden" name="<?=$k?>" id="<?=$k?>" value="<?=iconv("utf-8", "euc-kr", urldecode($v))?>" />
<?
	}
?>
	<div id="pop_header">
		<h1><img src="./naver_material/img/store/h1_shopmember.gif" width="99" height="16" alt="쇼핑몰 회원확인" /></h1>
		<dl class="store_logo">
		<dt><img src="./naver_material/img/store/text.gif" width="40" height="13" alt="쇼핑몰 :" /></dt>
		<dd><a href="http://<?=$_SERVER['HTTP_HOST']?>" target="_blank"><img src="<?=$_SESSION['NCINFO']['NCMallLogo']?>" width="93" height="25" alt="<?=$_SESSION['NCINFO']['NCMallName']?>" /></a></dd>
		</dl>
	</div>
	<div id="pop_content">
		<dl class="confirm_text">
		<dt><img src="./naver_material/img/store/text_cont.gif" width="174" height="16" alt="쇼핑몰 회원 확인을 해주세요!" /></dt>
		<dd><img src="./naver_material/img/store/text_info.gif" width="278" height="11" alt="체크아웃 쇼핑몰 회원 구매 이용을 위해 쇼핑몰 회원 확인을 해주세요." /></dd>
		</dl>
		<div class="section">
			<fieldset>
			<legend>쇼핑몰 회원 로그인</legend>
			<div class="mallTitle"></div>
			<dl class="login">
			<dt><strong><?=$_SESSION['NCINFO']['NCMallName']?></strong></dt>
			<dd><input type="text" name="shopId" id="shopId" title="아이디입력" value="" onfocus="iptNullCheck(this, '');setBorder(this, '#5AA409');" onblur="iptNullCheck(this, 'iptID');setBorder(this, '#BEBEBE');" class="iptID"></dd>
			<dd><input type="password" name="shopPassword" id="shopPassword" title="비밀번호입력" value="" onfocus="iptNullCheck(this, '');setBorder(this, '#5AA409');" onblur="iptNullCheck(this, 'iptPW');setBorder(this, '#BEBEBE');" class="iptPW"></dd>
			<dd><input type="password" name="MallUserSSN1" id="MallUserSSN1" title="주민번호입력" maxlength="6" style="width:60px;margin-right:8px;" onfocus="iptNullCheck(this, '');setBorder(this, '#5AA409');" onblur="iptNullCheck(this, 'iptSSN');setBorder(this, '#BEBEBE');" class="iptSSN" />-<input type="password" name="MallUserSSN2" id="MallUserSSN2" title="주민번호입력" maxlength="7" style="width:70px;margin-left:9px;" onfocus="iptNullCheck(this, '');setBorder(this, '#5AA409');" onblur="iptNullCheck(this, 'iptSSN');setBorder(this, '#BEBEBE');" class="iptSSN" /></dd>
			</dl>
			</fieldset>
		</div>
	</div>
	<div id="pop_footer">
		<div class="btn_section">
			<input type="image" src="./naver_material/img/store/btn_member.gif" width="63" height="23" alt="회원확인" />
			<a href="javascript:top.close();"><img src="./naver_material/img/store/btn_close.gif" width="40" height="23" alt="닫기" /></a>
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
