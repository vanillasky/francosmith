<?

include "../lib.php";
require_once("../../lib/qfile.class.php");
$qfile = new qfile();

$mode = ( $_POST['mode'] ) ? $_POST['mode'] : $_GET['mode'];

if (!$_POST['returnUrl']) $_POST['returnUrl'] = $_SERVER['HTTP_REFERER'];

require_once('../../lib/todayshop_cache.class.php');
if ($mode) todayshop_cache::remove('*','goodsreview');    // ���� ĳ�� ����

switch ( $mode ){

	case "delete":

		$infostr = split( ";", $_POST['nolist'] );
		for ( $i = 0; $i < count( $infostr ); $i++ ){
			$db->query("delete from ".GD_TODAYSHOP_GOODS_REVIEW." WHERE sno='" . $infostr[$i] . "'");
		}

		break;

	case "modify":

		### ����Ÿ ����
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

		### ȯ�漳��
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

		### ����Ÿ ���
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

		### �ۼ��� ���� ����Ʈ ����
		if ( $_POST['memo'] == 'direct' ) $_POST['memo'] = $_POST['direct_memo'];
		if ( $_POST['emoney'] > 0 && $_POST['emoneyPut'] == "Y" && $_POST['writer_m_no'] && $_POST['memo'] ){

			# �ۼ��� ���� ���̺� ������ �Է�
			$query = "update ".GD_TODAYSHOP_GOODS_REVIEW." set emoney = '".$_POST['emoney']."' where sno = '".$_POST['sno']."'";
			$db->query($query);

			# ������ ����
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

		### �ۼ��ڿ��� SMS ����
		if ( $_POST['smsSendYN'] == "Y" && $_POST['type'] == "1" && $_POST['phone'] && $_POST['callback'] && $_POST['msg']  ){

			$div[0]		= $_POST['phone'];
			$to_tran	= $_POST['phone']."[".$_POST['name']."]";
			$total		= count($div);

			if ($total>getSmsPoint()){
				msg("SMS �߼ۿ����� ".number_format($total)."���� �ܿ��ݼ��� ".number_format(getSmsPoint())."�Ǻ��� �����ϴ�");
				exit;
			}

			### SMS �߼�
			include "../member/znd_sms.php";

			$msg = "SMS �߼ۿ�û �Ǽ� : ".number_format(array_sum($num))."�� \\n ------------------- \\n �߼ۿ�û : ".number_format($num['success'])." / �߼ۿ�û���� : ".number_format($num['fail']);
			msg($msg);

		}

		echo "<script>parent.location.reload();</script>";
		exit;
		break;
}

go($_POST['returnUrl']);

?>