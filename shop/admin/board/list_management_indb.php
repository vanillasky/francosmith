<?
$_POST['mode'] = ($_GET['mode'] ? $_GET['mode'] : $_POST['mode'] );
$_POST['id'] = ($_GET['id'] ? $_GET['id'] : $_POST['id'] );

if(!preg_match('/^[a-zA-Z0-9_]*$/',$_POST['id'])) exit;
include "../../conf/bd_$_POST[id].php";
include "../../lib/library.php";
require_once("../../lib/upload.lib.php");

$_POST['subject'] = html_entity_decode($_POST['subject']);
$_POST['contents'] = html_entity_decode($_POST['contents']);

// 제목 스타일이 있는경우
if(is_array($_POST['titleStyle'])) {
	if($_POST['titleStyle']['C']) $titleStyle['C'] = "^C:".$_POST['titleStyle']['C']; // 제목 색상
	if($_POST['titleStyle']['S']) $titleStyle['S'] = "^S:".$_POST['titleStyle']['S']; // 제목 크기
	if($_POST['titleStyle']['B']) $titleStyle['B'] = "^B:".$_POST['titleStyle']['B']; // 제목 굵기

	if(is_array($titleStyle)) $titleStyle	= implode("|",$titleStyle);
}


//* bd class *//

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
$bd->mode	= $_POST[mode] == 'register' ? 'write' : $_POST[mode];	// 왜 register = write (치환처리 함)
$bd->sess	= $sess;
$bd->style	= $titleStyle;
$bd->ici_admin	= $ici_admin;

$bd->bdMaxSize	= $bdMaxSize;
$bd->exec_();


switch($_POST['mode']) {

	case "register":
		//무료보안서버
		$write_end_url = $sitelink->link("admin/board/list_management_indb.php?id=".$_POST['id']."&mode=register_end","regular");
		echo "<script>location.href='$write_end_url';</script>";
		exit;
	break;

	case "reply":
		//무료보안서버
		$write_end_url = $sitelink->link("admin/board/list_management_indb.php?id=".$_POST['id']."&mode=reply_end","regular");
		echo "<script>location.href='$write_end_url';</script>";
		exit;
	break;

	case "modify":
		//무료보안서버
		$write_end_url = $sitelink->link("admin/board/list_management_indb.php?id=".$_POST['id']."&mode=modify_end","regular");
		echo "<script>location.href='$write_end_url';</script>";
		exit;
	break;

	case "register_end":
		//무료보안서버 관련 부모창 새로고침을 위해 https 에서 http로 전환
		echo "<script>alert('글을 등록했습니다.');opener.location.reload();window.close()</script>";
		exit;

	case "reply_end":
		//무료보안서버 관련 부모창 새로고침을 위해 https 에서 http로 전환
		echo "<script>alert('답변을 작성했습니다.');opener.location.reload();window.close()</script>";
		exit;
	break;

	case "modify_end":
		//무료보안서버 관련 부모창 새로고침을 위해 https 에서 http로 전환
		echo "<script>alert('글을 수정했습니다.');opener.location.reload();window.close()</script>";
		exit;
	break;
}
?>
