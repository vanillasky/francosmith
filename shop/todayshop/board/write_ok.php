<?
if(!preg_match('/^[a-zA-Z0-9_]*$/',$_POST['id'])) exit;
include "../../conf/bd_$_POST[id].php";
include "../../lib/library.php";
require_once("../../lib/upload.lib.php");

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
	$strSQL ="ALTER TABLE `".GD_BD_.$_POST[id]."` ADD titleStyle VARCHAR( 50 ) AFTER homepage;";
	$db->query($strSQL);
}

if ($bdLvlW && $bdLvlW>$sess[level]) msg("글 작성 권한이 없습니다",-1);

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

class miniSave
{
	### MySQL v5.0 이상일때 binary 정렬문제로 인한 패치..
	function binary_patch()
	{
		$this->orderby = "order by idx,main,hex(sub)";
	}

	function chkNotice()
	{
		if ($this->mode=="modify"){
			if (($_POST[notice] && !$this->data[notice]) || (!$_POST[notice] && $this->data[notice])){
				if (!$_POST[notice]) $qr_notice = "and notice!='o'";
				$query	= "select * from `".GD_BD_.$this->id."` where idx like 'a%' $qr_notice $this->orderby limit 1";
				$data = $this->db->fetch($query);
				$this->idx  = ($data[idx]) ? substr($data[idx],1) : 999;
				$this->main = $data['main'] ? $data['main'] - 1 : 5000;
			}
		}
		if ($_POST[notice]){
			if ($this->mode=="reply") msg("공지는 답변글의 형태로 등록이 안됩니다",-1);
			list ($chk) = $this->db->fetch("select count(*) from `".GD_BD_.$this->id."` where notice='o'");
			if (!$chk) $this->main = -10000;
			### 공지글이 첫글일때 inf 테이블 데이타 저장
			list ($cnt) = $this->db->fetch("select count(*) from `".GD_BD_.$this->id."`");
			if ($cnt==1){
				$this->idx--;
				### idx값 검증
				if($this -> idx < 100) msg("올바르지 않은 게시판 그룹번호입니다.",-1);
				list($chk) = $this->db->fetch("select id from ".GD_BOARD_INF." where id='{$this->id}' and idx='a{$this->idx}'");
				if (!$chk) $this->db->query("insert into ".GD_BOARD_INF." set id='{$this->id}', idx='a{$this->idx}'");
			}
		}
	}

	function getIndex()
	{
		if ($this->mode=="write"){
			if (!$_POST[notice]) $qr_notice = "and notice!='o'";
			$query	= "select * from `".GD_BD_.$this->id."` where idx like 'a%' $qr_notice $this->orderby limit 1";
			$gap	= -1;
		} else $query = "select * from `".GD_BD_.$this->id."` where no='".$this->no."'";

		$this->data = $this->db->fetch($query,1);

		$this->idx  = ($this->data[idx]) ? substr($this->data[idx],1) : 1000;
		$this->main = $this->data['main'] + $gap;
		$this->sub	= $this->data[sub];

		$query = "select count(*) from `".GD_BD_.$this->id."` where idx='".$this->data[idx]."' and main='".$this->data[main]."'";
		list($cnt) = $this->db->fetch($query);
		if ($this->mode!="write" && $_POST[notice] && $cnt > 1) msg("답변글이 달린 게시물은 공지형태로 등록이 안됩니다",-1);

		if ($_POST[notice] || $this->data[notice]) $this->chkNotice();
		else if ($this->main<0){
			$this->idx--;
			### idx값 검증
			if($this -> idx < 100) msg("올바르지 않은 게시판 그룹번호입니다.",-1);
			$this->main = 5000;

			list($chk) = $this->db->fetch("select id from ".GD_BOARD_INF." where id='{$this->id}' and idx='a{$this->idx}'");
			if (!$chk) $this->db->query("insert into ".GD_BOARD_INF." set id='{$this->id}', idx='a{$this->idx}'");

			list ($chk) = $this->db->fetch("select count(*) from `".GD_BD_.$this->id."` where notice='o'");
			if ($chk){
				$this->db->query("update `".GD_BD_.$this->id."` set idx='a{$this->idx}' where notice='o'");
				$this->db->query("update ".GD_BOARD_INF." set num=$chk where id='{$this->id}' and idx='a{$this->idx}'");
				$this->db->query("update ".GD_BOARD_INF." set num=num-$chk where id='{$this->id}' and idx='a".($this->idx+1)."'");
			}
		}

		if ($this->mode=="reply"){
			if ($this->data[notice]) msg("공지에는 답변을 달 수가 없습니다",-1);
			$query	= "select right(sub,1) from `".GD_BD_.$this->id."` where idx='{$this->data[idx]}' and main='{$this->data[main]}' and length(sub)=length('{$this->data[sub]}')+1 and left(sub,length('{$this->data[sub]}'))='{$this->data[sub]}' order by sub desc limit 1";
			list ($sub) = $this->db->fetch($query);
			$sub = ord($sub) + 1;
			if ($sub==39 || $sub==92) $sub++;
			else if ($sub==256) $sub = 255;
			$this->sub .= chr($sub);
			$this->_pass   = $this->data[password];
			$this->_member = $this->data[m_no];
		}
	}

	function setFileName()
	{
		$maxStr = @floor((256-count($_FILES[file][tmp_name]))/count($_FILES[file][tmp_name]));
		for ($i=0;$i<count($_FILES[file][tmp_name]);$i++){
			if ($this->old_file[$i]){
				if (strlen($this->old_file[$i])>$maxStr) $div = explode(".",$this->old_file[$i]);
				$tmp_old[] = (strlen($this->old_file[$i])>$maxStr) ? substr($this->old_file[$i],0,$maxStr-6).sprintf("%02d",$i+1).".".substr($div[count($div)-1],0,3) : $this->old_file[$i];
				$tmp_new[] = $this->new_file[$i];
			}
		}
		if ( count($_FILES[file][tmp_name]) ){
			$this->new_file = @implode("|",$tmp_new);
			$this->old_file = @implode("|",$tmp_old);
		}
		else {
			$this->new_file = @implode("|",$this->new_file);
			$this->old_file = @implode("|",$this->old_file);
		}
	}

	function getPreFileArr()
	{
		$this->old_file = explode("|",$this->data[old_file]);
		$this->new_file = explode("|",$this->data[new_file]);
	}

	function multiUpload()
	{
		GLOBAL $bdListImgSizeW,$bdListImgSizeH;

		if ($this->mode=="modify") $this->getPreFileArr();
		$file_array = reverse_file_array($_FILES[file]);
		for ($i=0;$i<count($_FILES[file][tmp_name]);$i++){
			if ($_POST[del_file][$i]=="on"){
				unlink("../../data/board/$this->id/".$this->new_file[$i]);
				@unlink("../../data/board/$this->id/t/".$this->new_file[$i]);
				$this->old_file[$i] = "";
				$isChange = true;
			}
			if (is_uploaded_file($_FILES[file][tmp_name][$i])){
				if ($this->bdMaxSize && $_FILES[file][size][$i] > $this->bdMaxSize) msg("최대 업로드 사이즈는 ".byte2str($this->bdMaxSize)."입니다",-1);
				if ($this->new_file[$i]){
					unlink("../../data/board/$this->id/".$this->new_file[$i]);
					@unlink("../../data/board/$this->id/t/".$this->new_file[$i]);
				}
				$this->old_file[$i]	= $_FILES[file][name][$i];
				$this->new_file[$i]	= substr(md5(microtime()),0,16);
				if (preg_match("/^image/",$_FILES[file][type][$i])) thumbnail($_FILES[file][tmp_name][$i],"../../data/board/$this->id/t/".$this->new_file[$i],$bdListImgSizeW,$bdListImgSizeH,1);
				$upload = new upload_file($file_array[$i],"../../data/board/$this->id/".$this->new_file[$i]);
				if(!$upload -> upload()) msg('파일이 올바르지 않습니다.',-1);
				$isChange = true;
			}
		}

		### 계정용량 계산
		if ($isChange === true) setDu('board');

		$this->setFileName();
	}

	function chkPrivilege()
	{
		switch ($this->mode){
		case "modify":
			if ($this->ici_admin) $priv_modify = 1;
			if ($this->data[m_no]){
				if ($this->sess[m_no]==$this->data[m_no]) $priv_modify = 1;
			} else if ($_POST[password]){
				$query = "select no from `".GD_BD_.$this->id."` where no='".$this->no."' and password='".md5($_POST[password])."'";
				list ($chk) = mysql_fetch_array(mysql_query($query));
				if ($chk) $priv_modify = 1;
			}
			if (!$priv_modify) msg("비밀번호가 일치하지 않습니다",-1);
			break;
		case "reply":
			if ($this->data[notice]) msg("공지사항에는 답변이 불가능합니다",-1);
		}
	}

	function exec_()
	{
		$this->binary_patch();

		$this->getIndex();
		$this->chkPrivilege();
		$this->multiUpload();

		if ($_POST[html])	$html = 1;
		if ($_POST[br])		$html += 2;

		### 웹에디터 사용시 html 기능 on
		$html = 1;

		if ((!eregi("^http://",$_POST[urlLink])) && $_POST[urlLink]) $_POST[urlLink] = "http://".$_POST[urlLink];

		switch ($this->mode)
		{
		case "reply":
			$query	= "
					_pass		= '{$this->_pass}',
					_member		= '{$this->_member}',
					";
		case "write":
			$query	.= "
					password	= '".md5($_POST[password])."',
					m_no		= '{$this->sess[m_no]}',
					ip			= '$_SERVER[REMOTE_ADDR]',
					regdt		= now(),
					";
			$this->db->query("update ".GD_BOARD_INF." set num=num+1 where id='$this->id' and idx='a{$this->idx}'");
		case "modify":
			$query	.= "
					idx			= 'a{$this->idx}',
					main		= $this->main,
					sub			= '$this->sub',
					name		= '$_POST[name]',
					email		= '$_POST[email]',
					homepage	= '$_POST[homepage]',
					titleStyle	= '".$this->style."',
					subject		= '$_POST[subject]',
					contents	= '$_POST[contents]',
					urlLink		= '$_POST[urlLink]',
					old_file	= '" . addslashes($this->old_file) . "',
					new_file	= '" . addslashes($this->new_file) . "',
					notice		= '$_POST[notice]',
					secret		= '$_POST[secret]',
					html		= '$html',
					category	= '$_POST[subSpeech]'
					";
		}

		$query	= ($this->mode=="modify") ?
				"update `".GD_BD_.$this->id."` set $query where no='".$this->no."'" :
				"insert into `".GD_BD_.$this->id."` set $query";
		$this->db->query($query);
	}

}

//* bd class *//

if($_POST['mode']=="reply")
{
	$query = "select no from `".GD_BD_.$_POST[id]."` where no='".$_POST['no']."'";
	list($tmp) = $db->fetch($query);
	if(!$tmp) msg("원글이 삭제되어 답변글을 남길 수 없습니다",-1);
}

$bd = new miniSave;

$bd->db		= &$db;
$bd->id		= $_POST[id];
$bd->no		= $_POST[no];
$bd->mode	= $_POST[mode];
$bd->sess	= $sess;
$bd->style	= $titleStyle;
$bd->ici_admin	= $ici_admin;

$bd->bdMaxSize	= $bdMaxSize;
$bd->exec_();

go("list.php?id=$_POST[id]&".getReUrlQuery('no,id,mode', $_SERVER[HTTP_REFERER]));

//debug($db->log);

?>