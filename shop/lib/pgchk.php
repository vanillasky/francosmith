<?
function pgChk()
{
	global $godo;

	require_once(dirname(__FILE__)."/../lib/qfile.class.php");
	$qfile = new qfile();

	if(!$godo){
		$file = dirname(__FILE__)."/../conf/godomall.cfg.php";
		if (!is_file($file)) return false;
		$file = file($file);
		$godo = decode($file[1],1);
	}

	@include dirname(__FILE__)."/../conf/config.php";
	if($cfg[settlePg]) @include dirname(__FILE__)."/../conf/pg.".$cfg[settlePg].".php";
	if($pg[id] && $pg[id] != $cfg[gpg_id])$arr = array('gpg_id' => $pg[id]);

	if($arr){
		## 환경설정 파일에 pg_id 정보 저장
		$cfg = array_map("stripslashes",$cfg);
		$cfg = array_map("addslashes",$cfg);
		$cfg = array_merge($cfg,$arr);
		$qfile->open( dirname(__FILE__)."/../conf/config.php");
		$qfile->write("<? \n");
		$qfile->write("\$cfg = array( \n");
		foreach ($cfg as $k=>$v) $qfile->write("'$k' => '$v', \n");
		$qfile->write(") \n;");
		$qfile->write("?>");
		$qfile->close();

		## 고도몰에 pg_id와 서비스 타입을 전송한다
		$url = 'http://godo.co.kr/userinterface/pgUpdate.php?id='.$pg[id].'&sno='.$godo[sno].'&pg='.$cfg[settlePg];
		@file($url);
	}
}
?>