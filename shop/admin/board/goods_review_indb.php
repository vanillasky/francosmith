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
			daum_goods_review($infostr[$i]);	// ���� ��ǰ�� DB ����
			if($parent==$infostr[$i]) {	//�θ���̸�
				$db->query("delete from ".GD_GOODS_REVIEW." WHERE parent='" . $parent . "'");	
			}
			else{
				$db->query("delete from ".GD_GOODS_REVIEW." WHERE sno='" . $infostr[$i] . "'");
			}
		}

		break;

	case "modify":
		
		### ����Ÿ ����
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

		### ȯ�漳��
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

		### ����Ÿ ���
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
		
		### �ۼ��� ���� ����Ʈ ����
		if ( $_POST['memo'] == 'direct' ) $_POST['memo'] = $_POST['direct_memo'];
		if ( $_POST['emoney'] > 0 && $_POST['emoneyPut'] == "Y" && $_POST['writer_m_no'] && $_POST['memo'] ){
			
			# �ۼ��� ���� ���̺� ������ �Է�
			$query = "update ".GD_GOODS_REVIEW." set emoney = '".$_POST['emoney']."' where sno = '".$_POST['sno']."'";
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
		case "reply" : $printText .= "alert('�亯�� �ۼ��߽��ϴ�.');"; break;
		case "modify" : $printText .= "alert('���� �����߽��ϴ�.');"; break;
		case "noticeRegist" : $printText .= "alert('������ ��ϵǾ����ϴ�!');"; break;
		case "noticeModify" : $printText .= "alert('������ �����Ǿ����ϴ�!');"; break;
	}
	$printText .= "window.opener.location.reload();window.close();</script>";
	exit($printText);
}

?>