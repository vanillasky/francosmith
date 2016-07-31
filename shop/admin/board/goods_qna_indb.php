<?

include "../lib.php";
require_once("../../lib/qfile.class.php");
$qfile = new qfile();

$mode = ( $_POST['mode'] ) ? $_POST['mode'] : $_GET['mode'];

if (!$_POST[returnUrl]) $_POST[returnUrl] = $_SERVER[HTTP_REFERER];


switch ( $mode ){

	case "delete":

		$infostr = split( ";", $_POST['nolist'] );
		for ( $i = 0; $i < count( $infostr ); $i++ ){
			list($parent) = $db->fetch("select parent from ".GD_GOODS_QNA." where sno='" . $infostr[$i] . "'");
			if($parent == $infostr[$i]){
				$query = "delete from ".GD_GOODS_QNA." WHERE parent='" . $infostr[$i] . "'";
			}else{
				$query = "delete from ".GD_GOODS_QNA." WHERE sno='" . $infostr[$i] . "'";
			}
			$db->query($query);
		}

		break;

	case "modify":

		### 데이타 수정
		$query = "
		update ".GD_GOODS_QNA." set
			subject		= '$_POST[subject]',
		";
		if ($cfg['qnaSecret'] == 'secret') {
		$query .= "
			secret        = ".(isset($_POST[secret]) ? "'".$_POST[secret]."'" : "'0'").",
		";
		}
		$query .="
			contents	= '$_POST[contents]'
		where
			sno = '$_POST[sno]'
		";
		$db->query($query);
		break;

	case "reply":

		$q /*uestion*/ = $db->fetch("SELECT * FROM ".GD_GOODS_QNA." WHERE sno = $_POST[sno]",1);

		// 답변 완료 sms 전송
		if ($q['rcv_sms'] == 1 && $_POST['snd_sms'] == '1' && $_POST['sms'] != '') {

			$sms = Core::loader('sms');
			$sms_sendlist = $sms->loadSendlist();
			if ($sms->smsPt < 1) {
				echo "<script>
				alert('SMS 충전 포인트가 부족합니다.');
				</script>";
				exit;
			}

			$msg = parseCode($_POST['sms']);

			$sms->log($msg,$q['phone'],0,1);
			$sms_sendlist->setSimpleInsert($q['phone'], $sms->smsLogInsertId, '');
			if ($sms->send($msg,$q['phone'],$cfg['smsRecall'])) {
				$sms->update();
			}

		}


		### 데이타 답글
		$query = "
		insert ".GD_GOODS_QNA." set
			goodsno		= '$_POST[goodsno]',
			subject		= '$_POST[subject]',
			contents	= '$_POST[contents]',
			parent		= '$_POST[sno]',
			m_no		= '$_POST[m_no]',
			regdt		= now(),
			ip			= '$_SERVER[REMOTE_ADDR]'
		";
		$db->query($query);
		break;

	case "set":

		### 환경설정
		include "../../conf/config.php";
		$cfg = (array)$cfg;
		$cfg = array_map("addslashes",array_map("stripslashes",$cfg));
		$_POST["qnaSpamComment"] = @array_sum($_POST["qnaSpamComment"]);
		$_POST["qnaSpamBoard"] = @array_sum($_POST["qnaSpamBoard"]);
		$cfg = array_merge($cfg,(array)$_POST);
		unset( $cfg[returnUrl] );

		$qfile->open("../../conf/config.php");
		$qfile->write("<? \n");
		$qfile->write("\$cfg = array( \n");
		foreach ($cfg as $k=>$v) $qfile->write("'$k' => '$v', \n");
		$qfile->write(") \n;");
		$qfile->write("?>");
		$qfile->close();
		break;

	case "noticeRegist":
		$query = "
		insert into ".GD_GOODS_QNA." set
			subject		= '$_POST[subject]',
			contents	= '$_POST[contents]',
			m_no		= '$sess[m_no]',
			name		= '$_POST[name]',
			password	= md5('$_POST[password]'),
			regdt		= now(),
			secret		= '0',
			ip			= '$_SERVER[REMOTE_ADDR]',
			notice	= '1'
		";
		$db->query($query);
		$db->query("update ".GD_GOODS_QNA." set parent=sno where sno='" . $db->lastID() . "'");
	break;

	case "noticeModify":
		$query = "update ".GD_GOODS_QNA." set subject='{$_POST['subject']}',contents='{$_POST['contents']}' where sno='{$_POST['sno']}'";
		$db->query($query);
	break;
}

if($mode == "set" || $mode == "delete") go($_POST[returnUrl]);
else {
	$printText = "<script language=\"JavaScript\">";
	switch($_POST['mode']) {
		case "reply" : $printText .= "alert('답변을 작성했습니다.');"; break;
		case "modify" : $printText .= "alert('글을 수정했습니다.');"; break;
		case "noticeRegist" : $printText .= "alert('공지가 등록되었습니다!');"; break;
		case "noticeModify" : $printText .= "alert('공지가 수정되었습니다!');"; break;
	}
	$printText .= "window.opener.location.reload();window.close();</script>";
	exit($printText);
}

?>
