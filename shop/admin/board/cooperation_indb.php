<?

include "../lib.php";


function mailsend( $sno )
{
	global $db;

	$data = $db->fetch("select name, email, title, content, reply from ".GD_COOPERATION." where sno='" . $sno . "'",1);

	if ( $data[email] == '' ) return false;

	$modeMail = 20;
	include "../../conf/config.php";
	include_once "../../Template_/Template_.class.php";
	include "../../lib/mail.class.php";
	$mail = new Mail($params);
	$headers['Name']    = $cfg[shopName];
	$headers['From']    = $cfg[adminEmail];
	$headers['To']		= $data[email];
	$tpl = new Template_;
	$tpl->template_dir	= "../../conf/email";
	$tpl->compile_dir	= "../../Template_/_compiles/$cfg[tplSkin]/conf/email";
	$tpl->assign('name',$data['name']);
	$tpl->assign('questiontitle',$data['title']);
	$tpl->assign('question',nl2br( $data['content'] ));
	$tpl->assign('answer',nl2br( $data['reply'] ));
	$tpl->assign('cfg',$cfg);
	include "../../conf/email/subject_$modeMail.php";
	$tpl->define('tpl',"tpl_$modeMail.php");
	$result = $mail->send($headers, $tpl->fetch('tpl'));

	if ( $result ) $db->query("update ".GD_COOPERATION." set maildt=now() where sno = '$sno'");

	return $result;
}


$mode = ( $_POST['mode'] ) ? $_POST['mode'] : $_GET['mode'];

if (!$_POST[returnUrl]) $_POST[returnUrl] = $_SERVER[HTTP_REFERER];


switch ( $mode ){

	case "delete":

		$infostr = split( ";", $_POST['nolist'] );
		for ( $i = 0; $i < count( $infostr ); $i++ ){
			$db->query("delete from ".GD_COOPERATION." WHERE sno='" . $infostr[$i] . "'");
		}

		break;

	case "modify":

		### 데이타 수정
		$query = "
		update ".GD_COOPERATION." set
			itemcd		= '$_POST[itemcd]',
			name		= '$_POST[name]',
			email 		= '$_POST[email]',
			reply		= '$_POST[reply]'
		where
			sno = '$_POST[sno]'
		";
		$db->query($query);

		if ( $_POST[reply] <> '' ){ // 답변이 있는 경우

			if ( $_POST[replydt] <> '' && $_POST[replydt] <> '0000-00-00 00:00:00' ) $_POST[replydt] = "'$_POST[replydt]'";
			else $_POST[replydt] = "now()";

			$db->query("update ".GD_COOPERATION." set replydt=$_POST[replydt] where sno = '$_POST[sno]'");

			if ( $_POST[mailyn] == 'Y' ) mailsend( $_POST[sno] ); ### 문의답변메일
		}
		else { // 답변이 없는 경우
			$db->query("update ".GD_COOPERATION." set replydt='' where sno = '$_POST[sno]'");
		}

		$_POST[returnUrl] = './cooperation_register.php?mode=modify&sno=' . $_POST['sno'] . '&returnUrl=' . urlencode( $_POST[returnUrl] );

		break;

	case "mailsend":

		if ( mailsend( $_GET['sno'] ) ) echo "<script>alert('메일이 전송되었습니다.'); parent.document.location.reload();</script>";
		else msg($msg='메일 전송이 실패되었습니다.', $code='null');
		break;

	case "allmodify":

		$fieldChk = array( '' ); // 체크박스 필드명

		$exp = explode( "||", preg_replace( "/\|\|$/", "", $_POST['allmodify'] ) );

		foreach( $exp as $k => $value ){

			if ( $value == '' ){ unset( $exp[ $k ] ); continue; }

			$tmp = explode( "==", $value );
			$tmp[1] = preg_replace( "/;$/", "", $tmp[1] );

			if( !in_array( $key, $fieldChk ) ) $exp[ $tmp[0] ] = explode( ";", $tmp[1] );
			else $exp[ $tmp[0] ] = explode( ";", str_replace( "true", "Y", str_replace( "false", "N", $tmp[1] ) ) ); // 체크박스 필드경우

			unset( $exp[ $k ] );
		}

		foreach( $exp['code'] as $idx => $code ){
			$db->query("UPDATE ".GD_COOPERATION." SET itemcd='" . $exp['itemcd'][$idx] . "' WHERE sno='" . $code . "'");
		}

		break;
}

go($_POST[returnUrl]);

?>
