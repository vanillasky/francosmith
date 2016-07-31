<?php
@require "../lib.php";


unset($_POST['x'], $_POST['y']);

$todayShop = &load_class('todayshop', 'todayshop');
$tsCfg = $todayShop->cfg;

$metasite = serialize($_POST['metasite']);

$tsCfg['metasite'] = $metasite;

// 로고 이미지 처리
if (isset($_FILES['logo'])) {
	$file = $_FILES['logo'];
	$_ext = array_pop(explode('.',$file['name']));

	if (strtolower($_ext) == 'jpg') {

		if ($file['error'] == 0 && $file['size'] > 0) {

			$todayshop_logo = $_SERVER['DOCUMENT_ROOT'].'/shop/data/todayshop/todayshop_logo.jpg';
			@move_uploaded_file($file['tmp_name'], $todayshop_logo);

		}
	}
}

$todayShop->saveConfig($tsCfg);
msg('설정이 저장되었습니다.');
?>