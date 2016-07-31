<?

include "../lib.php";
include "../../conf/config.php";


function mailsend( $sno )
{
	global $db, $cfg;

	$data_r = $db->fetch("select parent, subject, contents from ".GD_MEMBER_QNA." where sno='" . $sno . "'",1);

	if ( $data_r[parent] == $sno ) return false;

	$data_p = $db->fetch("select subject, contents, m_no, email, mobile from ".GD_MEMBER_QNA." where sno='" . $data_r['parent'] . "'",1);
	list( $data_p['m_id'], $data_p['name'] ) = $db->fetch("select m_id, name from ".GD_MEMBER." where m_no='" . $data_p['m_no'] . "'");

	if ( $data_p[email] == '' ) return false;

	$modeMail = 20;
	include "../../lib/automail.class.php";
	$automail = new automail();
	$automail->_set($modeMail,$data_p[email],$cfg);
	$automail->_assign($_POST);
	$automail->_assign('name',$data_p['name']);
	$automail->_assign('questiontitle',$data_p['subject']);
	$automail->_assign('question',nl2br( $data_p['contents'] ));
	$automail->_assign('answertitle',$data_r['subject']);
	$automail->_assign('answer',nl2br( $data_r['contents'] ));
	$result = $automail->_send();

	if ( $result ) $db->query("update ".GD_MEMBER_QNA." set maildt=now() where sno = '$sno'");

	return $result;
}


function smssend( $sno )
{
	global $db;

	$data_r = $db->fetch("select parent from ".GD_MEMBER_QNA." where sno='" . $sno . "'",1);

	if ( $data_r[parent] == $sno ) return false;

	$data_p = $db->fetch("select m_no, mobile from ".GD_MEMBER_QNA." where sno='" . $data_r['parent'] . "'",1);
	list( $data_p['m_id'], $data_p['name'] ) = $db->fetch("select m_id, name from ".GD_MEMBER." where m_no='" . $data_p['m_no'] . "'");

	if ( $data_p[mobile] == '' ) return false;

	list( $now ) = $db->fetch("select now()");
	$GLOBALS[dataSms] = $data_p;
	sendSmsCase('qna',$data_p[mobile]);
	list( $result ) = $db->fetch("select count(*) as cnt from ".GD_SMS_LOG." where type='qna' and to_tran='" . str_replace("-","",$data_p[mobile]) . "' and cnt='1' and regdt>='$now'" );

	if ( $result ) @$db->query("update ".GD_MEMBER_QNA." set smsdt=now() where sno = '$sno'");

	return $result;
}


$mode = ( $_POST['mode'] ) ? $_POST['mode'] : $_GET['mode'];

if (!$_POST[returnUrl]) $_POST[returnUrl] = $_SERVER[HTTP_REFERER];

$mobile		= @implode("-",$_POST[mobile]);
$mailling	= ($_POST[mailling]) ? "y" : "n";
$sms		= ($_POST[sms]) ? "y" : "n";

switch ( $mode ){

	case "delete":

		$infostr = split( ";", $_POST['nolist'] );
		for ( $i = 0; $i < count( $infostr ); $i++ ){
			$db->query("delete from ".GD_MEMBER_QNA." WHERE sno='" . $infostr[$i] . "'");
		}

		break;

	case "modify":

		### ����Ÿ ����
		$query = "
		update ".GD_MEMBER_QNA." set
			itemcd		= '$_POST[itemcd]',
			subject		= '$_POST[subject]',
			contents	= '$_POST[contents]',
			email		= '$_POST[email]',
			mobile		= '$mobile',
			mailling	= '$mailling',
			sms			= '$sms',
			ordno		= '$_POST[ordno]'
		where
			sno = '$_POST[sno]'
		";
		$db->query($query);

		if ( $_POST[mailyn] == 'Y' ) mailsend( $_POST[sno] ); ### ���Ǵ亯����
		if ( $_POST[smsyn] == 'Y' ) smssend( $_POST[sno] ); ### ���Ǵ亯SMS

		if ($cfg['ssl'] == "1") {
			//���Ẹ�ȼ���
			$write_end_url = $sitelink->link("admin/board/".basename($_SERVER["PHP_SELF"])."?mode=modify_end","regular");
			echo "<script>location.href='$write_end_url';</script>";
		} else {
			echo "<script>alert('���� �����߽��ϴ�.');opener.location.reload();window.close()</script>";
		}
		exit;


		break;

	case "modify_end":
		//���Ẹ�ȼ��� ���� �θ�â ���ΰ�ħ�� ���� https ���� http�� ��ȯ
		echo "<script>alert('���� �����߽��ϴ�.');opener.location.reload();window.close()</script>";
		exit;
	break;

	case "reply":

		### ����Ÿ ���
		$query = "
		insert ".GD_MEMBER_QNA." set
			subject		= '$_POST[subject]',
			contents	= '$_POST[contents]',
			parent		= '$_POST[sno]',
			m_no		= '$_POST[m_no]',
			regdt		= now(),
			ip			= '$_SERVER[REMOTE_ADDR]'
		";
		$db->query($query);
		$new_sno = $db->lastID();

		if ( $_POST[mailyn] == 'Y' ) mailsend( $new_sno ); ### ���Ǵ亯����
		if ( $_POST[smsyn] == 'Y' ) smssend( $new_sno ); ### ���Ǵ亯SMS

		echo "<script>parent.location.reload();</script>";
		exit;
		break;

	case "mailsend":

		if ( mailsend( $_GET['sno'] ) ) echo "<script>alert('������ ���۵Ǿ����ϴ�.'); parent.document.location.reload();</script>";
		else msg($msg='���� ������ ���еǾ����ϴ�.', $code='null');
		break;

	case "smssend":

		if ( smssend( $_GET['sno'] ) ) echo "<script>alert('SMS�� ���۵Ǿ����ϴ�.'); parent.document.location.reload();</script>";
		else msg($msg='SMS ������ ���еǾ����ϴ�.', $code='null');
		break;

	case "noticeRegist":
		$query = "
		insert into ".GD_MEMBER_QNA." set
			subject		= '$_POST[subject]',
			contents	= '$_POST[contents]',
			m_no		= '$sess[m_no]',
			name		= '$_POST[name]',
			password	= md5('$_POST[password]'),
			regdt		= now(),
			ip			= '$_SERVER[REMOTE_ADDR]',
			notice	= '1'
		";
		$db->query($query);
		$temp_parent = $db->lastID();
		$db->query("UPDATE ".GD_MEMBER_QNA." SET parent = '$temp_parent' WHERE sno = '$temp_parent'");
		echo "<script>
		alert('������ ��ϵǾ����ϴ�!');
		opener.location.reload();
		self.close();
		</script>";
		break;

	case "noticeModify":
		$query = "update ".GD_MEMBER_QNA." set itemcd='{$_POST['itemcd']}', subject='{$_POST['subject']}',contents='{$_POST['contents']}' where sno='{$_POST['sno']}'";
		$db->query($query);
		echo "<script>
		alert('������ �����Ǿ����ϴ�!');
		parent.location.reload();
		</script>";
		break;
}

go($_POST[returnUrl]);

?>
