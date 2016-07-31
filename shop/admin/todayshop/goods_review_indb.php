<?

include "../lib.php";
require_once("../../lib/qfile.class.php");
$qfile = new qfile();

$mode = ( $_POST['mode'] ) ? $_POST['mode'] : $_GET['mode'];

if (!$_POST['returnUrl']) $_POST['returnUrl'] = $_SERVER['HTTP_REFERER'];

require_once('../../lib/todayshop_cache.class.php');
if ($mode) todayshop_cache::remove('*','goodsreview');    // 리뷰 캐시 삭제

switch ( $mode ){

	case "delete":

		$infostr = split( ";", $_POST['nolist'] );
		for ( $i = 0; $i < count( $infostr ); $i++ ){
			$db->query("delete from ".GD_TODAYSHOP_GOODS_REVIEW." WHERE sno='" . $infostr[$i] . "'");
		}

		break;

	case "modify":

		### 데이타 수정
		$query = "
		update ".GD_TODAYSHOP_GOODS_REVIEW." set
			subject		= '".$_POST['subject']."',
			contents	= '".$_POST['contents']."'
		where
			sno = '".$_POST['sno']."'
		";
		$db->query($query);

		echo "<script>parent.location.reload();</script>";
		exit;
		break;

	case "set":

		### 환경설정
		include "../../conf/config.php";
		$cfg = (array)$cfg;
		$cfg = array_map("addslashes",array_map("stripslashes",$cfg));
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
		insert ".GD_TODAYSHOP_GOODS_REVIEW." set
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
			$query = "update ".GD_TODAYSHOP_GOODS_REVIEW." set emoney = '".$_POST['emoney']."' where sno = '".$_POST['sno']."'";
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

		echo "<script>parent.location.reload();</script>";
		exit;
		break;
}

go($_POST['returnUrl']);

?>