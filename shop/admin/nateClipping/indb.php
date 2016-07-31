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
		msg("쇼핑몰 한줄 소개는 한글(공백포함)50자 이내로 작성하셔야 합니다.");
		exit;
	}
	if($_POST['proContentsLink']){
		if(!$validator->check_url($_POST['proContentsLink'])){
			msg("url형식이 아닙니다.");
			exit;
		}
	}
}

$nate = new nateClipping();
$nate -> upload_scrapBt($_FILES['scrapbt'],$_FILES['logo'],$_POST['imgWidth'],$_POST['imgHeight'],$_POST['proContents'],$_POST['proContentsLink'],$cfg['tplSkin']);
msg("저장이 완료 되었습니다.");
?>
<script type="text/javascript">parent.location.reload();</script>