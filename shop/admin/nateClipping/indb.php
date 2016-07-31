<?php
@require "../lib.php";
@require "../../lib/load.class.php";
@require "../../lib/nateClipping.class.php";
@require "../../lib/qfile.class.php";
@require "../../lib/upload.lib.php";
@require "../../lib/validation.class.php";
@require "../../conf/config.php";

$validator = new Validation;
$_POST['proContentsLink'] = str_replace(array('http://'),'',$_POST['proContentsLink']);
if($_POST['proContents']){
	if(!$validator->check_max(50,$_POST['proContents'])){
		msg("���θ� ���� �Ұ��� �ѱ�(��������)50�� �̳��� �ۼ��ϼž� �մϴ�.");
		exit;
	}
	if($_POST['proContentsLink']){
		if(!$validator->check_url($_POST['proContentsLink'])){
			msg("url������ �ƴմϴ�.");
			exit;
		}
	}
}

$nate = new nateClipping();
$nate -> upload_scrapBt($_FILES['scrapbt'],$_FILES['logo'],$_POST['imgWidth'],$_POST['imgHeight'],$_POST['proContents'],$_POST['proContentsLink'],$cfg['tplSkin']);
msg("������ �Ϸ� �Ǿ����ϴ�.");
?>
<script type="text/javascript">parent.location.reload();</script>