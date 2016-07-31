<?
	# 정식도메인
	if($godo['godoUrl'] == false){
		$service[]	= "<img src=\"../img/icon_txt_ing.gif\" />";
	}else{
		$service[]	= "<a href=\"http://www.godo.co.kr/mygodo/index.html\" target=\"_blank\"><img src=\"../img/icon_apply.gif\" /></a>";
	}

	# SMS 문자전송
	if(getSmsPoint() > 0){
		$service[]	= "<img src=\"../img/icon_txt_ing.gif\" />";
	}else{
		$service[]	= "<a href=\"../member/sms.pay.php\"><img src=\"../img/icon_apply.gif\" /></a>";
	}

	# 전자지불 카드결제
	if($use_pg == true){
		$service[]	= "<img src=\"../img/icon_txt_ing.gif\" />";
	}else{
		$service[]	= "<a href=\"../basic/pg.intro.php\"><img src=\"../img/icon_apply.gif\" /></a>";
	}

	# 구매안전(에스크로), 쇼핑몰 보증보험
	include_once '../../lib/lib.func.egg.php';
	$egg = getEggConf();
	@include "../../conf/pg.escrow.php";
	if($egg['use'] == "Y" || $escrow['use'] == "Y"){
		$service[]	= "<img src=\"../img/icon_txt_ing.gif\" />";
	}else{
		$service[]	= "<a href=\"../basic/egg.intro.php\"><img src=\"../img/icon_apply.gif\" /></a>";
	}

	if($egg['use'] == "Y"){
		$service[]	= "<img src=\"../img/icon_txt_ing.gif\" />";
	}else{
		$service[]	= "<a href=\"../basic/egg.uclick.php\"><img src=\"../img/icon_apply.gif\" /></a>";
	}

	# 전자세금계산서
	$config_pay = $config->load('configpay');
	$config_tax = $config_pay['tax'];
	$config_godotax = $config->load('godotax');
	if($config_tax['useyn'] == 'y' && $config_godotax['site_id'] != ''){
		$service[]	= "<img src=\"../img/icon_txt_ing.gif\" />";
	}else{
		$service[]	= "<a href=\"../order/godotax.intro.php\"><img src=\"../img/icon_apply.gif\" /></a>";
	}

	# 통장자동입금 확인
	$bankAutoChk[0]	= sprintf("GODO%05d",$godo['sno']);
	$bankAutoChk[1]	= readurl("http://bankmatch.godo.co.kr/sock_ismid.php?MID=".$bankAutoChk[0]."&hashdata=" . md5($bankAutoChk[0]));
	if($bankAutoChk[1] == 'true'){
		$service[]	= "<img src=\"../img/icon_txt_ing.gif\" />";
	}else{
		$service[]	= "<a href=\"../order/bankmatch.intro.php\"><img src=\"../img/icon_apply.gif\" /></a>";
	}

	# 보안서버
	if($cfg['ssl']){
		$service[]	= "<img src=\"../img/icon_txt_ing.gif\" />";
	}else{
		$service[]	= "<a href=\"../basic/ssl_guide.php\"><img src=\"../img/icon_apply.gif\" /></a>";
	}

	# 이미지 호스팅
	$serviceChk = readurl("http://gongji.godo.co.kr/userinterface/season2.service.info.php?mode=imghosting&godosno=".$godo['sno']);
	if ( $serviceChk == 'true' ){
		$service[]	= "<a href=\"javascript:popup('http://image.godo.co.kr/login/imghost_login.php',980,700)\"><img src=\"../img/icon_txt_ing.gif\" /></a>";
	}else{
		$service[]	= "<a href=\"http://hosting.godo.co.kr/imghosting/intro.php\" target=\"_blank\"><img src=\"../img/icon_apply.gif\" /></a>";
	}
	unset($serviceChk);

	/*
	서비스 사용 현황 순서

	정식도메인
	SMS 문자전송
	전자결제(카드결제)
	구매안전(에스크로)
	실명확인
	전자세금계산서
	통장자동입금확인
	보안서버
	이나무폰
	이미지호스팅
	*/

?>
	<div class="main-basic-left-service">
	<ul>
	<?
	foreach($service as $sVal){
		echo "<li>".$sVal."</li>";
	}
	?>
	</ul>
	</div>
<? unset($service); ?>