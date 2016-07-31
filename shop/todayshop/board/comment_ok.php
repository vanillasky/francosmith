<?
if(!preg_match('/^[a-zA-Z0-9_]*$/',$_POST['id'])) exit;
include "../../conf/bd_$_POST[id].php";
include "../../lib/library.php";

if ($bdLvlC && $bdLvlC>$sess[level]) msg("로그인하셔야 본 서비스를 이용하실 수 있습니다","../../member/login.php");

# Anti-Spam 검증
$switch = ($bdSpamComment&1 ? '123' : '000') . ($bdSpamComment&2 ? '4' : '0');
$rst = antiSpam($switch, "board/(view|list).php", "post");
if (substr($rst[code],0,1) == '4') msg("자동등록방지문자가 일치하지 않습니다. 다시 입력하여 주십시요.",-1);
if ($rst[code] <> '0000') msg("무단 링크를 금지합니다.",-1);
if(strlen($_POST[memo]) > 500) msg('내용이 너무 깁니다.',-1);

switch ($_POST[mode]){

	case "write":

		$query = "insert into ".GD_BOARD_MEMO." set
				id			= '$_POST[id]',
				no			= '$_POST[no]',
				name		= '$_POST[name]',
				memo		= '$_POST[memo]',
				password	= '".md5($_POST[password])."',
				m_no		= '$sess[m_no]',
				regdt		= now()
				";

		$db->query($query);
		$db->query("update `".GD_BD_.$_POST['id']."` set comment=comment+1 where no='".$_POST[no]."'");
		break;

	case "modify":

		$data	= $db->fetch("select * from ".GD_BOARD_MEMO." where id='$_POST[id]' and no='".$_POST[no]."'");
		list ($chk)	= $db->fetch("select no from ".GD_BOARD_MEMO." where id='$_POST[id]' and sno='".$_POST[no]."' and password=password('$_POST[password]')");

		if (!(($chk && $_POST[password]) || $ici_admin || $sess[m_no]==$data[m_no])) msg("비밀번호가 일치하지 않습니다",-1);

		$query = "update ".GD_BOARD_MEMO." set
				name		= '$_POST[name]',
				memo		= '$_POST[memo]',
				etc			= '$_POST[etc]'
				where id='$_POST[id]' and sno='".$_POST[no]."'
				";

		$db->query($query);
		break;

}

$query = "select * from ".GD_BD_.$_POST['id']." where no='$_POST[no]'";
$data = $db->fetch($query);
if($data[secret] != 'o'){
	go($_POST[returnUrl]);
}else{
	go("list.php?id=$_POST[id]&".getReUrlQuery('no,id,mode', $_POST[returnUrl]));
}

?>