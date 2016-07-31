<?
include "../lib/library.php";
require_once("../lib/upload.lib.php");

if (!isset($_POST['id'])) {
	msg("서버전송 용량이 설정된 값 (".ini_get('post_max_size').")을 초과하였습니다",-1);
}

if(!preg_match('/^[a-zA-Z0-9_]*$/',$_POST['id'])) exit;
include "../conf/bd_".$_POST['id'].".php";


foreach ($_FILES["file"]["error"] as $key => $error)
{
	if ($error === UPLOAD_ERR_INI_SIZE){
		$fileMaxSize =  (isset($bdMaxSize) && $bdMaxSize != null) ? byte2str($bdMaxSize) : ini_get("upload_max_filesize");
		msg("파일 업로드 최대 사이즈는 ".$fileMaxSize."입니다",-1);
	}
}

if(class_exists('validation') && method_exists('validation', 'xssCleanArray')){
	$_POST = validation::xssCleanArray($_POST , array(
		validation::DEFAULT_KEY=>'text',
		'contents'=>'disable',
		'subject'=>'disable',
		'titleStyle'=>'disable',
		'captcha_key'=>'disable',
		'mode'=>'disable',
		'page'=>'disable',
		'chkSpamKey'=>'disable',
		'subSpeech'=>'disable',
		));

	$_POST['titleStyle'] = validation::xssCleanArray($_POST['titleStyle'] , array(
		validation::DEFAULT_KEY=>'html',
		));
}

if($_POST['encode'] == 'y') {
	$_POST['subject'] = iconv('utf8','euckr',urldecode($_POST['subject']));
	$_POST['contents'] = iconv('utf8','euckr',urldecode($_POST['contents']));
	if(class_exists('validation') && method_exists('validation', 'xssCleanArray')){
		$_POST['contents'] = validation::xssClean($_POST['contents'],$bdUseXss,'ent_quotes' , $bdAllowPluginTag, $bdAllowPluginDomain);
		$_POST['subject'] = validation::xssClean($_POST['subject'],$bdUseXss , 'ent_quotes' , $bdAllowPluginTag , $bdAllowPluginDomain);
		$_POST['subSpeech'] = validation::xssClean($_POST['subSpeech'],$bdUseXss , 'ent_quotes' , $bdAllowPluginTag , $bdAllowPluginDomain);
	}
} else if($_POST['encode'] == 'htmlencode') { //특정 한글 호환 2013-05-08
	$_POST['subject'] = html_entity_decode($_POST['subject']);
	$_POST['contents'] = html_entity_decode($_POST['contents']);
	if(class_exists('validation') && method_exists('validation', 'xssCleanArray')){
		$_POST['contents'] = validation::xssClean($_POST['contents'], $bdUseXss, 'ent_compat', $bdAllowPluginTag, $bdAllowPluginDomain);
		$_POST['subject'] = validation::xssClean($_POST['subject'],$bdUseXss , 'ent_compat' , $bdAllowPluginTag , $bdAllowPluginDomain);
		$_POST['subSpeech'] = validation::xssClean($_POST['subSpeech'],$bdUseXss , 'ent_compat' , $bdAllowPluginTag , $bdAllowPluginDomain);
	}
}

### 추가된 필드가 있는지를 체크를 해서 없으면 추가 -- 나중에 이곳은 삭제처리가 되어야 함
$strSQL = "DESC `".GD_BD_.$_POST[id]."`";
$res = $db->query($strSQL);
$fieldChk	= false;
while ($tmp_chk=$db->fetch($res)){
	if($tmp_chk['Field'] == "titleStyle"){
		$fieldChk	= true;
	}
}
if($fieldChk === false){
	$strSQL ="ALTER TABLE `".GD_BD_.$_POST['id']."` ADD titleStyle VARCHAR( 50 ) AFTER homepage;";
	$db->query($strSQL);
}

if ($bdLvlW && $bdLvlW>$sess[level] && $_POST['mode']=="write") msg("글 작성 권한이 없습니다",-1);
if ($bdLvlP && $bdLvlP>$sess[level] && $_POST['mode']=="reply") msg("답글 작성 권한이 없습니다",-1);

# Anti-Spam 검증
$switch = ($bdSpamBoard&1 ? '123' : '000') . ($bdSpamBoard&2 ? '4' : '0');
$rst = antiSpam($switch, "board/write.php", "post");
if (substr($rst[code],0,1) == '4') msg("자동등록방지문자가 일치하지 않습니다. 다시 입력하여 주십시요.",-1);
if ($rst[code] <> '0000') msg("무단 링크를 금지합니다.",-1);

# 제목 스타일이 있는경우
if (is_array($_POST['titleStyle'])){

	# 제목 색상
	if($_POST['titleStyle']['C']){
		$titleStyle['C']	= "^C:".$_POST['titleStyle']['C'];
	}

	# 제목 크기
	if($_POST['titleStyle']['S']){
		$titleStyle['S']	= "^S:".$_POST['titleStyle']['S'];
	}

	# 제목 굵기
	if($_POST['titleStyle']['B']){
		$titleStyle['B']	= "^B:".$_POST['titleStyle']['B'];
	}

	if(is_array($titleStyle)){
		$titleStyle	= implode("|",$titleStyle);
	}
}

//* bd class

if($_POST['mode']=="reply")
{
	$query = "select no from `".GD_BD_.$_POST[id]."` where no='".$_POST['no']."'";
	list($tmp) = $db->fetch($query);
	if(!$tmp) msg("원글이 삭제되어 답변글을 남길 수 없습니다",-1);
}

$bd = Core::loader('miniSave');

$bd->db		= &$db;
$bd->id		= $_POST[id];
$bd->no		= $_POST[no];
$bd->mode	= $_POST[mode];
$bd->sess	= $sess;
$bd->style	= $titleStyle;
$bd->ici_admin	= $ici_admin;

$bd->bdMaxSize	= isset($bdMaxSize) ? $bdMaxSize : ini_get("upload_max_filesize");
$bd->exec_();

// 페이지캐시 초기화
$templateCache = Core::loader('TemplateCache');
$templateCache->clearCacheByClass('board');

$loc_url = $sitelink->link("board/list.php?id=$_POST[id]&".getReUrlQuery('no,id,mode', $_SERVER[HTTP_REFERER]),"regular");
go($loc_url);

//debug($db->log);

?>
