<?php
@require "../lib.php";
@require "../../lib/lib.enc.php";
@require "../../lib/load.class.php";
@require "../../lib/qfile.class.php";
@require "../../lib/upload.lib.php";
@require "../../conf/config.php";
@require "../../conf/fieldset.php";

if(class_exists('NaverCommonInflowScript', false)===false) include dirname(__FILE__).'/../../lib/naverCommonInflowScript.class.php';
$naverCommonInflowScript = new NaverCommonInflowScript();
if($naverCommonInflowScript->isEnabled===false) exit('
<script type="text/javascript">
alert("네이버 공통인증키를 저장하셔야 설정하실수 있습니다.");
</script>');

$qfile = new qfile;

unset($_POST['x'],$_POST['y'],$_POST['exceptions'],$_POST['search_exceptions'],$_POST['cate']);

// 네이버 체크아웃 회원연동 설정 검사
if($_POST['ncMemberYn'] == 'y') {
	$checkMsg = '';
	if($checked['useField']['resno'] != 'checked' || $checked['reqField']['resno'] != 'checked') {
		$_POST['ncMemberYn'] = 'n';
		$checkMsg .= "\\n회원가입 시 주민등록번호를 필수 항목으로 체크하셔야 합니다.";
	}
	if($joinset['status'] != '1') {
		$_POST['ncMemberYn'] = 'n';
		$checkMsg .= "\\n회원인증절차 설정을 \'인증절차없음\'으로 체크하셔야 합니다.";
	}
	if(($ipin['useyn'] == 'y' && $ipin['id']) && !($realname['useyn'] == 'y' && $realname['id'])) {
		$_POST['ncMemberYn'] = 'n';
		$checkMsg .= "\\n실명확인 수단으로 아이핀만 사용하면 안됩니다.";
	}
	if ($checkMsg != '') {
		$checkMsg = "\\n\\n※ 일부설정 저장실패\\n\\n네이버 체크아웃 부가서비스를 사용하시려면".$checkMsg;
	}
}

foreach($_POST as $k=>$v)
{
	if(is_array($v)):
		foreach ($v as $k1=>$v1)$checkoutCfg[$k][] = addslashes($v1);
	else:
		$checkoutCfg[$k] = addslashes($v);
	endif;
}

$qfile->open("../../conf/naverCheckout.cfg.php");
$qfile->write("<? \n");
$qfile->write("\$checkoutCfg = array( \n");
foreach ($checkoutCfg as $k=>$v)
{
	if(is_array($v)):
		$qfile->write("'$k' => array(");
		foreach ($v as $k1=>$v1) $qfile->write("'$v1',");
		$qfile->write("), \n");
	else:
		$qfile->write("'$k' => '$v', \n");
	endif;
}
$qfile->write(") \n;");
$qfile->write("?>");
$qfile->close();
@chmod("../../conf/naverCheckout.cfg.php",0707);

$tmp = readurl('http://gongji.godo.co.kr/userinterface/naverCheckout/banWords.php');
$out = godoConnDecode($tmp);
$tmp = explode(',',$out);

$qfile->open("../../conf/naverCheckout.banWords.php");
$qfile->write("<?\n");
$qfile->write("\$checkoutBan = array(\n");
foreach($tmp as $v)
{
	$qfile->write("'".$v."',");
}
$qfile->write(");\n");
$qfile->write("?>");
$qfile->close();
@chmod("../../conf/naverCheckout.banWords.php",0707);

// 주문Api연동
$configCheckoutAPI = array(
	'integrateOrder'=>(string)$_POST['integrateOrder'],
	'linkStock'=>(string)$_POST['linkStock'],
);
$config->save('checkoutapi',$configCheckoutAPI);

msg('설정이 저장되었습니다.'.$checkMsg,'partner.php','parent');
?>
