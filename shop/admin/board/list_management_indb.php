<?
$_POST['mode'] = ($_GET['mode'] ? $_GET['mode'] : $_POST['mode'] );
$_POST['id'] = ($_GET['id'] ? $_GET['id'] : $_POST['id'] );

if(!preg_match('/^[a-zA-Z0-9_]*$/',$_POST['id'])) exit;
include "../../conf/bd_$_POST[id].php";
include "../../lib/library.php";
require_once("../../lib/upload.lib.php");

$_POST['subject'] = html_entity_decode($_POST['subject']);
$_POST['contents'] = html_entity_decode($_POST['contents']);

// ���� ��Ÿ���� �ִ°��
if(is_array($_POST['titleStyle'])) {
	if($_POST['titleStyle']['C']) $titleStyle['C'] = "^C:".$_POST['titleStyle']['C']; // ���� ����
	if($_POST['titleStyle']['S']) $titleStyle['S'] = "^S:".$_POST['titleStyle']['S']; // ���� ũ��
	if($_POST['titleStyle']['B']) $titleStyle['B'] = "^B:".$_POST['titleStyle']['B']; // ���� ����

	if(is_array($titleStyle)) $titleStyle	= implode("|",$titleStyle);
}


//* bd class *//

if($_POST['mode']=="reply")
{
	$query = "select no from `".GD_BD_.$_POST[id]."` where no='".$_POST['no']."'";
	list($tmp) = $db->fetch($query);
	if(!$tmp) msg("������ �����Ǿ� �亯���� ���� �� �����ϴ�",-1);
}

$bd = Core::loader('miniSave');

$bd->db		= &$db;
$bd->id		= $_POST[id];
$bd->no		= $_POST[no];
$bd->mode	= $_POST[mode] == 'register' ? 'write' : $_POST[mode];	// �� register = write (ġȯó�� ��)
$bd->sess	= $sess;
$bd->style	= $titleStyle;
$bd->ici_admin	= $ici_admin;

$bd->bdMaxSize	= $bdMaxSize;
$bd->exec_();


switch($_POST['mode']) {

	case "register":
		//���Ẹ�ȼ���
		$write_end_url = $sitelink->link("admin/board/list_management_indb.php?id=".$_POST['id']."&mode=register_end","regular");
		echo "<script>location.href='$write_end_url';</script>";
		exit;
	break;

	case "reply":
		//���Ẹ�ȼ���
		$write_end_url = $sitelink->link("admin/board/list_management_indb.php?id=".$_POST['id']."&mode=reply_end","regular");
		echo "<script>location.href='$write_end_url';</script>";
		exit;
	break;

	case "modify":
		//���Ẹ�ȼ���
		$write_end_url = $sitelink->link("admin/board/list_management_indb.php?id=".$_POST['id']."&mode=modify_end","regular");
		echo "<script>location.href='$write_end_url';</script>";
		exit;
	break;

	case "register_end":
		//���Ẹ�ȼ��� ���� �θ�â ���ΰ�ħ�� ���� https ���� http�� ��ȯ
		echo "<script>alert('���� ����߽��ϴ�.');opener.location.reload();window.close()</script>";
		exit;

	case "reply_end":
		//���Ẹ�ȼ��� ���� �θ�â ���ΰ�ħ�� ���� https ���� http�� ��ȯ
		echo "<script>alert('�亯�� �ۼ��߽��ϴ�.');opener.location.reload();window.close()</script>";
		exit;
	break;

	case "modify_end":
		//���Ẹ�ȼ��� ���� �θ�â ���ΰ�ħ�� ���� https ���� http�� ��ȯ
		echo "<script>alert('���� �����߽��ϴ�.');opener.location.reload();window.close()</script>";
		exit;
	break;
}
?>
