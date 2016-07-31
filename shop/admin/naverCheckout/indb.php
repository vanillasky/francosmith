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
alert("���̹� ��������Ű�� �����ϼž� �����ϽǼ� �ֽ��ϴ�.");
</script>');

$qfile = new qfile;

unset($_POST['x'],$_POST['y'],$_POST['exceptions'],$_POST['search_exceptions'],$_POST['cate']);

// ���̹� üũ�ƿ� ȸ������ ���� �˻�
if($_POST['ncMemberYn'] == 'y') {
	$checkMsg = '';
	if($checked['useField']['resno'] != 'checked' || $checked['reqField']['resno'] != 'checked') {
		$_POST['ncMemberYn'] = 'n';
		$checkMsg .= "\\nȸ������ �� �ֹε�Ϲ�ȣ�� �ʼ� �׸����� üũ�ϼž� �մϴ�.";
	}
	if($joinset['status'] != '1') {
		$_POST['ncMemberYn'] = 'n';
		$checkMsg .= "\\nȸ���������� ������ \'������������\'���� üũ�ϼž� �մϴ�.";
	}
	if(($ipin['useyn'] == 'y' && $ipin['id']) && !($realname['useyn'] == 'y' && $realname['id'])) {
		$_POST['ncMemberYn'] = 'n';
		$checkMsg .= "\\n�Ǹ�Ȯ�� �������� �����ɸ� ����ϸ� �ȵ˴ϴ�.";
	}
	if ($checkMsg != '') {
		$checkMsg = "\\n\\n�� �Ϻμ��� �������\\n\\n���̹� üũ�ƿ� �ΰ����񽺸� ����Ͻ÷���".$checkMsg;
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

// �ֹ�Api����
$configCheckoutAPI = array(
	'integrateOrder'=>(string)$_POST['integrateOrder'],
	'linkStock'=>(string)$_POST['linkStock'],
);
$config->save('checkoutapi',$configCheckoutAPI);

msg('������ ����Ǿ����ϴ�.'.$checkMsg,'partner.php','parent');
?>
