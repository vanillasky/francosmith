<?
if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['id'])) exit;

## 변수할당
$id		= $_GET['id'];
$no		= $_GET['no'];
$mode	= $_GET['mode'];

@include_once "../conf/bd_".$_GET['id'].".php";
include "../_header.php";

$b_referer = (!$sess) ? '../member/login.php?returnUrl=../board/list.php?'.$_SERVER['QUERY_STRING'] : '../board/list.php?'.$_SERVER['QUERY_STRING'];
if($bdLvlW && !$sess && ($mode == "write" || !$mode || $mode == "reply")){
	msg("권한이 없습니다.",$b_referer);
}
if ($bdLvlW && $bdLvlW > $sess['level'] && ($mode == "write" || !$mode)){
	$level_name = $db->fetch("select grpnm from gd_member_grp where level='".$bdLvlW."'");
	msg($level_name['grpnm']." 등급이상만 작성이 가능합니다.",$b_referer);
}
if ($bdLvlP && $bdLvlP > $sess['level'] && $mode == "reply"){
	$level_name = $db->fetch("select grpnm from gd_member_grp where level='".$bdLvlW."'");
	msg($level_name['grpnm']." 등급이상만 답글 작성이 가능합니다.",$b_referer);
}

# Anti-Spam 검증
$rst = antiSpam(($bdSpamBoard&1 ? '1' : '0'));
if ($rst['code'] <> '0000') msg("무단 링크를 금지합니다.",-1);

# 계정용량체크
list( $disk_errno, $disk_msg ) = disk();
if ( !empty( $disk_errno ) ) $bdUseFile="";

if (!$mode) $mode = "write";
$checked['br'] = "checked";

if ($sess) $tpl->assign(readonly,array(name => "readonly style='border:0;font-weight:bold'"));

switch ($mode){

	case "modify":

		$data = $db->fetch("select * from ".GD_BD_.$id." where no=".$no);

		if(class_exists('validation') && method_exists('validation','xssClean')){
			$data = validation::xssCleanArray($data , 
				array(
					validation::DEFAULT_KEY=>'text',
					'contents'=>array($bdUseXss, null, $bdAllowPluginTag , $bdAllowPluginDomain),
					'subject'=>array($bdUseXss, null, $bdAllowPluginTag , $bdAllowPluginDomain),
					'category'=>array($bdUseXss,null, $bdAllowPluginTag , $bdAllowPluginDomain),
					'link'=>'disable',
					'password'=>'disable',
					'old_file'=>'disable',
					'new_file'=>'disable',
					'urlLink'=>'disable',
					'titleStyle'=>'disable',
				));
		}

		$_POST['titleStyle'] = validation::xssCleanArray($_POST['titleStyle'] , array(
			validation::DEFAULT_KEY=>'html',
		));

		# 제목 스타일
		if( $bdTitleCChk || $bdTitleSChk || $bdTitleBChk ){
			$tmp_titleStyle	= explode("|",$data['titleStyle']);
			unset($data['titleStyle']);
			foreach($tmp_titleStyle AS $sKey => $sVal){
				$tmp_title	= explode(":",$sVal);
				if( $bdTitleCChk && $tmp_title[0] == "^C"){
					$data['titleStyle']['C'][$tmp_title[1]]	= "selected";
				}
				if( $bdTitleSChk && $tmp_title[0] == "^S"){
					$data['titleStyle']['S'][$tmp_title[1]]	= "selected";
				}
				if( $bdTitleBChk && $tmp_title[0] == "^B"){
					$data['titleStyle']['B'][$tmp_title[1]]	= "selected";
				}
			}
		}

		if ($data['notice']) $checked['notice']	= "checked";
		if ($data['secret']) $checked['secret']	= "checked";
		if ($data['html']%2) $checked['html']	= "checked";
		$checked['br'] = ($data['html'] > 1) ? "checked" : "";

		if ($data['old_file']){
			$div = explode("|",$data['old_file']);
			for ($tmp='',$i=0; $i < count($div); $i++){
				$tmp .= "
				<tr id=".($i+1).">
					<td valign=\"top\" style=\"padding-top:3\">".($i+1)."</td>
					<td class=\"eng\">
					<input type=\"file\" name=\"file[]\" style=\"width:90%\" class=\"line\" onChange=\"preview(this.value,".($i+1).");\" /><br>
					<input type=\"checkbox\" name=\"del_file[$i]\" /> Delete Uploaded File .. {$div[$i]}
					</td>
					<td id=\"prvImg".($i+1)."\"><a href=\"javascript:input(".($i+1).")\"><img src=\"download.php?id=".$id."&no=".$no."&mode=1&div=".$i."&thumbnail=1\" width=\"50\" onload=\"if(this.height>this.width){this.height=50}\" onError=\"this.style.display='none'\" /></a></td>
				</tr>
				";
			}
			$data['prvFile'] = $tmp;
		}

		break;

	case "reply":

		list ($data['no'],$data['subject']) = $db->fetch("select no,subject from `".GD_BD_.$id."` where no='".$no."'");
		//if (!ereg("^Re:",$data['subject'])) $data['subject'] = "Re: ".$data['subject'];

	case "write":

		if ($sess){
			$tpl->assign(array(
				name	=> $member['name'],
				email	=> $member['email'],
				//homepage=> $member[homepage],
				));
		}else{
			if (!$bdEmailNo || $bdEmailNo == "") {
				$bdPrivateYN	= "Y";
			}
		}
		$data['category'] = $_GET['subSpeech'];
}

# 비밀글 설정 - 작성시 기본글
$inputSecretStr	= "<input type=\"checkbox\" name=\"secret\" ".$checked['secret']." />";

# 비밀글 설정 - 작성시 기본 비밀글
if ($bdSecretChk == 1){
	if($mode != "modify"){
		$inputSecretStr	= "<input type=\"checkbox\" name=\"secret\" checked />";
	}

# 비밀글 설정 - 작성시 무조건 일반글
}else if ($bdSecretChk == 2){
	$inputSecretStr	= "<input type=\"hidden\" name=\"secret\" value=\"\" />";

# 비밀글 설정 - 작성시 무조건 비밀글
}else if ($bdSecretChk == 3){
	$inputSecretStr	= "<input type=\"hidden\" name=\"secret\" value=\"o\" />";
}

$chk	= array(
		secret	=> $inputSecretStr,
		html	=> "<input type=\"checkbox\" name=\"html\" ".$checked['html']." />",
		br		=> "<input type=\"checkbox\" name=\"br\" ".$checked['br']." />",
		);
if ($ici_admin && $mode!="reply" && !$data['sub']) $chk['notice'] = "<input type=\"checkbox\" name=\"notice\" ".$checked['notice'].">";

# 말머리
if ($bdUseSubSpeech){
	$subSpeech	= explode("|",$bdSubSpeech);
	foreach ($subSpeech AS $sKey => $sVal){
		$chk['subSpeech'] = ($data['category']==$sVal) ? "selected" : "";
		$speechBox .= "<option value=\"".$sVal."\" ".$chk['subSpeech'].">".$sVal."</option>";
	}
	$data['subSpeech'] = "<select name=\"subSpeech\" class=\"speechBox\">".$speechBox."</select>";
}

/*
	2011-01-13 by x-ta-c
	bdEditorChk 값이 없을시 게시판 설정에서 기본값으로 설정되는 사용함(=1)로 설정.
*/
if (!isset($bdEditorChk)) {
	$bdEditorChk = 1;
}
// 2011-01-13

$bdHeader = stripslashes($bdHeader);
$bdFooter = stripslashes($bdFooter);
if ($bdMaxSize == ''){
	$bdMaxSize = str_replace('M', '', ini_get('upload_max_filesize')) * 1024 * 1024;
}

if ($data) $tpl->assign($data);
if ($div) $tpl->assign(array(file => $div));
$tpl->assign(array(
			id		=> $id,
			mode	=> $mode,
			page	=> $page
			));
$tpl->define('tpl',"board/".$bdSkin."/write.htm");

$termsPolicyCollection3 = getTermsGuideContents('terms', 'termsPolicyCollection3');
$tpl->assign('termsPolicyCollection3', $termsPolicyCollection3);
### 무료보안서버 회원처리url
$tpl->assign('boardwriteActionUrl',$sitelink->link('board/write_ok.php','ssl'));

$tpl->print_('tpl');
?>

<script src="../lib/js/board.js"></script>