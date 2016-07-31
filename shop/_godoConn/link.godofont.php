<?php
include("../lib/library.php");
$godo = $config->load('godo');

$mode = $_POST['mode'];

// 체크용 통신
if($mode=='check') {
	echo $godo['sno'];
	exit;
}

// 고도 폰트 서버에서만 접속 가능 ( 보안 적용 )
if($_SERVER['REMOTE_ADDR'] != '211.233.50.19'){
	exit('Not Certified');
}

// 폰트 받기
if($mode=='upload' && count($_FILES['font']['error'])) {

	$font_code = $_POST['fontcode'];
	$font_name = iconv('utf-8','euc-kr',$_POST['fontname']);
	$expire_start = $_POST['expire_start'];
	$expire_end = $_POST['expire_end'];


	//validation하기
	//
	//


	$ar_size = array();
	foreach($_FILES['font']['error'] as $k=>$v) {
		if($v !== UPLOAD_ERR_OK) {
			exit('UPLOAD_FAILED');
		}
		$ar_size[]=$k;
	}

	$ar_fontdata = array(
		'font_code'=>$font_code,
		'font_name'=>$font_name,
		'enable_size'=>implode(',',$ar_size),
		'expire_start'=>$expire_start,
		'expire_end'=>$expire_end,
	);

	$query = $db->_query_print('select font_no from gd_webfont where font_code=[s]',$font_code);
	$result = $db->_select($query);
	if($result[0]['font_no']) {
		$query = $db->_query_print('update gd_webfont set [cv] where font_no=[s]',$ar_fontdata,$result[0]['font_no']);
	}
	else {
		$query = $db->_query_print('insert into gd_webfont set [cv]',$ar_fontdata);
	}
	$db->query($query);

	foreach($ar_size as $v) {
		if(!is_dir(SHOPROOT.'/data/font')) {
			mkdir(SHOPROOT.'/data/font');
			@chmod(SHOPROOT.'/data/font',0707);
		}
		move_uploaded_file($_FILES['font']['tmp_name'][$v],SHOPROOT.'/data/font/'.$font_code.'_'.sprintf('%02d',$v));
	}
	echo 'DONE';

}


?>