<?

include "../lib.php";
require_once("../../lib/qfile.class.php");
$qfile = new qfile();

$mode = $_POST[mode];
$skin_type= $_POST[skin_type];

include "../../conf/config.php";
if(!$cfg[adminEmail]) {
	msg('발송 메일주소 누락!\n[기본정보 설정] 에서 관리자 Email 정보를 등록해 주세요.',-1,$target='');
}
$cfg = (array)$cfg;
$cfg = array_map("stripslashes",$cfg);
$cfg = array_map("addslashes",$cfg);
$cfg = array_merge($cfg,(array)$_POST[cfg]);

$qfile->open("../../conf/config.php");
$qfile->write("<? \n");
$qfile->write("\$cfg = array( \n");
foreach ($cfg as $k=>$v) $qfile->write("'$k' => '$v', \n");
$qfile->write(") \n;");
$qfile->write("?>");
$qfile->close();

$subject_path="../../conf/email/subject_{$mode}.php";
$qfile->open($subject_path);
$qfile->write("<?");
$qfile->write("\$headers[Subject] = \"{$_POST[subject]}\";");
$qfile->write("?>");
$qfile->close();
@chmod($subject_path, 0707);

$body_path="../../conf/email/tpl_{$mode}.php";
$body = str_replace("cart-&gt;","cart->",$_POST[body]);
$qfile->open($body_path);
$qfile->write(stripslashes($body));
$qfile->close();
@chmod($body_path, 0707);


//로그파일 생성
if($mode==3){
	$dir = "../../log/email/";
	if (!is_dir($dir)) {
		@mkdir($dir, 0707);
		@chmod($dir, 0707);
	}
	$filename="email_".$mode."_".date("Y-m-d_H:i:s").".htm";
	$qfile->open($dir.$filename);
	$qfile->write(stripslashes($body));
	$qfile->close();


	$files=array();
	if (is_dir($dir)) {
		if ($dh = opendir($dir)) {
			$cnt=0;
			while (($file = readdir($dh)) !== false) {
				if ( ($file != "." && $file != "..") || filetype($dir . $file)=='file') { 
					$files[filemtime($dir.$file)]=$file;
					$cnt++;
				}
			}
			closedir($dh);
		}
	}
	if($cnt>3) {	//로그가 4개 이상이면 오래전 파일 지움
		$min_key= array_keys($files,min($files));
		$min_filename=$files[$min_key[0]]; 
		if(is_file($dir.$min_filename)){
			unlink($dir.$min_filename);
		}
	}
	unset($files);
}

go($_SERVER[HTTP_REFERER]);
?>