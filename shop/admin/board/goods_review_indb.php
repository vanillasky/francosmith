<?

include "../lib.php";
require_once("../../lib/qfile.class.php");
$qfile = new qfile();

$mode = ( $_POST['mode'] ) ? $_POST['mode'] : $_GET['mode'];

if (!$_POST['returnUrl']) $_POST['returnUrl'] = $_SERVER['HTTP_REFERER'];


switch ( $mode ){

	case "delete":

		$infostr = split( ";", $_POST['nolist'] );
		for ( $i = 0; $i < count( $infostr ); $i++ ){
			$res=$db->query("select parent from ".GD_GOODS_REVIEW." where sno='" . $infostr[$i] . "'");
			$row = $db->fetch($res);
			$parent=$row[0];
			daum_goods_review($infostr[$i]);	// 다음 상품평 DB 저장
			if($parent==$infostr[$i]) {	//부모글이면
				$db->query("delete from ".GD_GOODS_REVIEW." WHERE parent='" . $parent . "'");	
			}
			else{
				$db->query("delete from ".GD_GOODS_REVIEW." WHERE sno='" . $infostr[$i] . "'");
			}
		}

		break;

	case "modify":
		
		### 데이타 수정
		$query = "
		update ".GD_GOODS_REVIEW." set
			subject		= '".$_POST['subject']."',
			contents	= '".$_POST['contents']."'
		where
			sno = '".$_POST['sno']."'
		";
		$db->query($query);
		break;

	case "set":

		### 환경설정
		include "../../conf/config.php";
		$cfg = (array)$cfg;
		$cfg = array_map("addslashes",array_map("stripslashes",$cfg));
		$_POST["reviewSpamComment"] = @array_sum($_POST["reviewSpamComment"]);
		$_POST["reviewSpamBoard"] = @array_sum($_POST["reviewSpamBoard"]);
		$cfg = array_merge($cfg,(array)$_POST);
		unset( $cfg['returnUrl'] );

		$qfile->open("../../conf/config.php");
		$qfile->write("<? \n");
		$qfile->write("\$cfg = array( \n");
		foreach ($cfg as $k=>$v) $qfile->write("'$k' => '$v', \n");
		$qfile->write(") \n;");
		$qfile->write("?>");
		$qfile->close();
		break;

	case "reply":

		### 데이타 답글
		$query = "
		insert ".GD_GOODS_REVIEW." set
			goodsno		= '".$_POST['goodsno']."',
			subject		= '".$_POST['subject']."',
			contents	= '".$_POST['contents']."',
			parent		= '".$_POST['sno']."',
			m_no		= '".$_POST['m_no']."',
			regdt		= now(),
			ip			= '".$_SERVER['REMOTE_ADDR']."'
		";
		$db->query($query);
		
		### 작성자 에게 포인트 지급
		if ( $_POST['memo'] == 'direct' ) $_POST['memo'] = $_POST['direct_memo'];
		if ( $_POST['emoney'] > 0 && $_POST['emoneyPut'] == "Y" && $_POST['writer_m_no'] && $_POST['memo'] ){
			
			# 작성자 리뷰 테이블에 적립금 입력
			$query = "update ".GD_GOODS_REVIEW." set emoney = '".$_POST['emoney']."' where sno = '".$_POST['sno']."'";
			$db->query($query);
			
			# 적립금 지급
			$query = "
			insert into ".GD_LOG_EMONEY." set
				ordno	= '".$_POST['sno']."',
				m_no	= '".$_POST['writer_m_no']."',
				emoney	= '".$_POST['emoney']."',
				memo	= '".$_POST['memo']."',
				regdt	= now()
			";
			$db->query($query);
	
			$query = "update ".GD_MEMBER." set emoney = emoney + ".$_POST['emoney']." where m_no='" . $_POST['writer_m_no'] . "'";
			$db->query($query);
		}
		
		### 작성자에게 SMS 전송
		if ( $_POST['smsSendYN'] == "Y" && $_POST['type'] == "1" && $_POST['phone'] && $_POST['callback'] && $_POST['msg']  ){
			
			$div[0]		= $_POST['phone'];
			$to_tran	= $_POST['phone']."[".$_POST['name']."]";
			$total		= count($div);
			
			if ($total>getSmsPoint()){
				msg("SMS 발송예정인 ".number_format($total)."건이 잔여콜수인 ".number_format(getSmsPoint())."건보다 많습니다");
				exit;
			}
			
			### SMS 발송
			include "../member/znd_sms.php";
			
			$msg = "SMS 발송요청 건수 : ".number_format(array_sum($num))."건 \\n ------------------- \\n 발송요청 : ".number_format($num['success'])." / 발송요청실패 : ".number_format($num['fail']);
			msg($msg);
			
		}
		break;
	
	case "noticeRegist":
		$query = "
		insert into ".GD_GOODS_REVIEW." set
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
		break;

	case "noticeModify":
		$query = "update ".GD_GOODS_REVIEW." set subject='{$_POST['subject']}',contents='{$_POST['contents']}' where sno='{$_POST['sno']}'";
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