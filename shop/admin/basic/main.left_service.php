<?
	# ���ĵ�����
	if($godo['godoUrl'] == false){
		$service[]	= "<img src=\"../img/icon_txt_ing.gif\" />";
	}else{
		$service[]	= "<a href=\"http://www.godo.co.kr/mygodo/index.html\" target=\"_blank\"><img src=\"../img/icon_apply.gif\" /></a>";
	}

	# SMS ��������
	if(getSmsPoint() > 0){
		$service[]	= "<img src=\"../img/icon_txt_ing.gif\" />";
	}else{
		$service[]	= "<a href=\"../member/sms.pay.php\"><img src=\"../img/icon_apply.gif\" /></a>";
	}

	# �������� ī�����
	if($use_pg == true){
		$service[]	= "<img src=\"../img/icon_txt_ing.gif\" />";
	}else{
		$service[]	= "<a href=\"../basic/pg.intro.php\"><img src=\"../img/icon_apply.gif\" /></a>";
	}

	# ���ž���(����ũ��), ���θ� ��������
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

	# ���ڼ��ݰ�꼭
	$config_pay = $config->load('configpay');
	$config_tax = $config_pay['tax'];
	$config_godotax = $config->load('godotax');
	if($config_tax['useyn'] == 'y' && $config_godotax['site_id'] != ''){
		$service[]	= "<img src=\"../img/icon_txt_ing.gif\" />";
	}else{
		$service[]	= "<a href=\"../order/godotax.intro.php\"><img src=\"../img/icon_apply.gif\" /></a>";
	}

	# �����ڵ��Ա� Ȯ��
	$bankAutoChk[0]	= sprintf("GODO%05d",$godo['sno']);
	$bankAutoChk[1]	= readurl("http://bankmatch.godo.co.kr/sock_ismid.php?MID=".$bankAutoChk[0]."&hashdata=" . md5($bankAutoChk[0]));
	if($bankAutoChk[1] == 'true'){
		$service[]	= "<img src=\"../img/icon_txt_ing.gif\" />";
	}else{
		$service[]	= "<a href=\"../order/bankmatch.intro.php\"><img src=\"../img/icon_apply.gif\" /></a>";
	}

	# ���ȼ���
	if($cfg['ssl']){
		$service[]	= "<img src=\"../img/icon_txt_ing.gif\" />";
	}else{
		$service[]	= "<a href=\"../basic/ssl_guide.php\"><img src=\"../img/icon_apply.gif\" /></a>";
	}

	# �̹��� ȣ����
	$serviceChk = readurl("http://gongji.godo.co.kr/userinterface/season2.service.info.php?mode=imghosting&godosno=".$godo['sno']);
	if ( $serviceChk == 'true' ){
		$service[]	= "<a href=\"javascript:popup('http://image.godo.co.kr/login/imghost_login.php',980,700)\"><img src=\"../img/icon_txt_ing.gif\" /></a>";
	}else{
		$service[]	= "<a href=\"http://hosting.godo.co.kr/imghosting/intro.php\" target=\"_blank\"><img src=\"../img/icon_apply.gif\" /></a>";
	}
	unset($serviceChk);

	/*
	���� ��� ��Ȳ ����

	���ĵ�����
	SMS ��������
	���ڰ���(ī�����)
	���ž���(����ũ��)
	�Ǹ�Ȯ��
	���ڼ��ݰ�꼭
	�����ڵ��Ա�Ȯ��
	���ȼ���
	�̳�����
	�̹���ȣ����
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