<?
	include "../lib.php";
	require_once("../../lib/qfile.class.php");

	// Ŭ���� ����
		$qfile = new qfile();

	// ���� ����
		$mode = ( $_POST['mode'] ) ? $_POST['mode'] : $_GET['mode'];
		$type = ( $_POST['type'] ) ? $_POST['type'] : $_GET['type'];

		if(!$_POST['qstr']) {
			$_POST['qstr'] .= "type=".$_GET['type'];
			$_POST['qstr'] .= "&page=".$_GET['page'];
			$_POST['qstr'] .= "&selType=".$_GET['selType'];
		}

		if (!$_POST['returnUrl']) $_POST['returnUrl'] = $_SERVER['HTTP_REFERER'];

	// �Լ�
		// ���� ����
		function mailsend( $sno ) {
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

		// SMS ����
		function smssend( $sno ) {
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

		// ó��
		switch($mode) {
			case "replySet" :

				### ���־��� �亯 ����
				include "../../conf/config.php";
				$cfg = (array)$cfg;
				$cfg = array_map("addslashes",array_map("stripslashes",$cfg));
				$_POST["qnaSpamComment"] = @array_sum($_POST["qnaSpamComment"]) * 1;
				$_POST["qnaSpamBoard"] = @array_sum($_POST["qnaSpamBoard"]) * 1;
				$_POST["reviewSpamComment"] = @array_sum($_POST["reviewSpamComment"]) * 1;
				$_POST["reviewSpamBoard"] = @array_sum($_POST["reviewSpamBoard"]) * 1;

				$cfg = array_merge($cfg,(array)$_POST);
				unset( $cfg['returnUrl'] );
				unset( $cfg['qstr'] );
				unset( $cfg['x'] );
				unset( $cfg['y'] );

				$qfile->open("../../conf/config.php");
				$qfile->write("<? \n");
				$qfile->write("\$cfg = array( \n");
				foreach ($cfg as $k=>$v) $qfile->write("'$k' => '$v', \n");
				$qfile->write(") \n;");
				$qfile->write("?>");
				$qfile->close();

				break;

			case "replyRegist" :
				### ���־��� �亯 ���
				$query = "INSERT INTO ".GD_GOODS_FAVORITE_REPLY." SET
					customerType = '$type',
					subject = '".$_POST['subject']."',
					contents = '".$_POST['contents']."',
					regdt = NOW(),
					ip = '".$_SERVER['REMOTE_ADDR']."'";
				$result = $db->query($query);

				msg("���־��� �亯�� ��ϵǾ����ϴ�.");
				echo "<script>opener.location.reload();</script>";
				go("../board/customer_reply.php?id=".$db->lastID()."&mode=replyModify&".$_POST['qstr']);
				break;

			case "replyModify" :
				### ���־��� �亯 ����
				list($chkCount) = $db->fetch("SELECT COUNT(sno) FROM ".GD_GOODS_FAVORITE_REPLY." WHERE sno = '".$_POST['id']."' AND customerType = '$type'");
				if(!$chkCount) {
					msg("���־��� �亯�� �����Ǿ��ų� �������� �ʽ��ϴ�.");
					echo "<script>opener.location.reload();</script>";
					go("../board/customer_reply.php?id=".$_POST['id']."&mode=replyModify&".$_POST['qstr']);
					break;
				}

				$query = "UPDATE ".GD_GOODS_FAVORITE_REPLY." SET
					subject = '".$_POST['subject']."',
					contents = '".$_POST['contents']."',
					ip = '".$_SERVER['REMOTE_ADDR']."'
				WHERE
				sno = '".$_POST['id']."' AND customerType = '$type'";
				$result = $db->query($query);

				msg("���־��� �亯�� �����Ǿ����ϴ�.");
				echo "<script>opener.location.reload();</script>";
				go("../board/customer_reply.php?id=".$_POST['id']."&mode=replyModify&".$_POST['qstr']);

				break;

			case "replyDelete" :
				### ���־��� �亯 ����
				list($chkCount) = $db->fetch("SELECT COUNT(sno) FROM ".GD_GOODS_FAVORITE_REPLY." WHERE sno = '".$_GET['id']."' AND  customerType = '$type'");
				if(!$chkCount) {
					msg("�������� �ʴ� �亯�Դϴ�.");
					echo "<script>opener.location.reload();</script>";
					go("../board/customer_reply.php?".$_POST['qstr']);
					break;
				}

				$query = "DELETE FROM ".GD_GOODS_FAVORITE_REPLY." WHERE sno = '".$_GET['id']."' AND  customerType = '$type'";
				$result = $db->query($query);

				msg("���־��� �亯�� �����Ǿ����ϴ�.");
				echo "<script>opener.location.reload();</script>";
				go("../board/customer_reply.php?".$_POST['qstr']);

				break;

			case "selectReply" :
				$rno = ($_GET['rno']) ? $_GET['rno'] : "";
				list($subject, $contents) = $db->fetch("SELECT subject, contents FROM ".GD_GOODS_FAVORITE_REPLY." WHERE sno = '$rno' AND  customerType = '$type'");

				$str = "
					<div style='display:none;'>
					<form name='tempForm'>
					<input type='text' name='subject' id='subject' value='$subject'>
					<textarea name='contents' id='contents'>$contents</textarea>
					</form>
					</div>
					<script language='JavaScript'>
					window.onload = function() {
						tmpS = opener.document.getElementById('subject');
						tmpC = opener.document.getElementById('contents');

						tmpS.value = document.getElementById('subject').value;";

				if($_GET['type'] == 'qna') $str .= "
					opener.miniEditorMode(0,'source');";

				$str .= "
					if(tmpC.value) tmpC.value = '".(($_GET['type'] == 'qna') ? "<br /><br /><br />" : "\\n\\n\\n")."' + tmpC.value;
					tmpC.value = document.getElementById('contents').value + tmpC.value;";

				if($_GET['type'] == 'qna') $str .= "
					opener.miniEditorMode(0,'editor');";

				$str .= "
					self.close();
				}
				</script>";
				exit($str);
				break;

			case "qnaReply" :
				$q /*uestion*/ = $db->fetch("SELECT * FROM ".GD_GOODS_QNA." WHERE sno = ".$_POST['sno'], 1);

				// �亯 �Ϸ� sms ����
				if($q['rcv_sms'] == 1 && $_POST['snd_sms'] == '1' && $_POST['sms'] != '') {
					$sms = Core::loader('sms');
					$sms_sendlist = $sms->loadSendlist();
					if($sms->smsPt < 1) {
						msg("SMS ���� ����Ʈ�� �����մϴ�.",-1);
						exit;
					}

					$msg = parseCode($_POST['sms']);

					$sms->log($msg,$q['phone'], 0, 1);
					$sms_sendlist->setSimpleInsert($q['phone'], $sms->smsLogInsertId, '');
					if($sms->send($msg,$q['phone'],$cfg['smsRecall'])) {
						$sms->update();
					}
				}
				// ���Ϻ�����
				if($q['rcv_email'] == '1' && $_POST['snd_email'] == '1') {
					list( $q['m_id'], $q['name'] ) = $db->fetch("select m_id, name from ".GD_MEMBER." where m_no='" . $q['m_no'] . "'");

					$modeMail = 20;
					include "../../lib/automail.class.php";
					$automail = new automail();
					$automail->_set($modeMail, $q['email'], $cfg);
					$automail->_assign($_POST);
					$automail->_assign('name', $q['name']);
					$automail->_assign('questiontitle', $q['subject']);
					$automail->_assign('question', nl2br( $q['contents'] ));
					$automail->_assign('answertitle', $_POST['subject']);
					$automail->_assign('answer', nl2br( $_POST['contents'] ));
					$result = $automail->_send();
				}
				### ����Ÿ ���
				$query = "
				INSERT INTO ".GD_GOODS_QNA." SET
					goodsno		= '".$_POST['goodsno']."',
					subject		= '".$_POST['subject']."',
					contents	= '".$_POST['contents']."',
					parent		= '".$_POST['sno']."',
					m_no		= '".$_POST['m_no']."',
					regdt		= NOW(),
					ip			= '".$_SERVER['REMOTE_ADDR']."'
				";
				$db->query($query);

				//���Ẹ�ȼ���
				$write_end_url = $sitelink->link("admin/board/customer_indb.php?mode=reply_end","regular");
				echo "<script>location.href='$write_end_url';</script>";
				exit;
				break;

			case "reply_end":
				//���Ẹ�ȼ��� ���� �θ�â ���ΰ�ħ�� ���� https ���� http�� ��ȯ
				echo "<script>alert('�亯�� ��ϵǾ����ϴ�.');opener.location.reload();window.close()</script>";
				exit;
				break;

			case "reviewReply" :

				### ����Ÿ ���
				$query = "
				INSERT INTO ".GD_GOODS_REVIEW." SET
					goodsno		= '".$_POST['goodsno']."',
					subject		= '".$_POST['subject']."',
					contents	= '".$_POST['contents']."',
					parent		= '".$_POST['sno']."',
					m_no		= '".$_POST['m_no']."',
					regdt		= NOW(),
					ip			= '".$_SERVER['REMOTE_ADDR']."'
				";
				$db->query($query);

				if($_POST['writer_m_no']){
					$dormantCheckArray = array('m_no' => $_POST['writer_m_no']);
					$dormantMember = false;
					$dormant = Core::loader('dormant');
					$dormantMember = $dormant->checkDormantMember($dormantCheckArray, 'm_no');
				}

				//�޸�ȸ���� �ƴҽ� ����Ʈ ����, SMS �߼�
				if($dormantMember === false){
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
				}

				//���Ẹ�ȼ���
				$write_end_url = $sitelink->link("admin/board/customer_indb.php?mode=reply_end","regular");
				echo "<script>location.href='$write_end_url';</script>";
				exit;
				break;

			case "reply_end":
				//���Ẹ�ȼ��� ���� �θ�â ���ΰ�ħ�� ���� https ���� http�� ��ȯ
				echo "<script>alert('�亯�� ��ϵǾ����ϴ�.');opener.location.reload();window.close()</script>";
				exit;
				break;

			case "memberQnaReply":

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

				//���Ẹ�ȼ���
				$write_end_url = $sitelink->link("admin/board/customer_indb.php?mode=reply_end","regular");
				echo "<script>location.href='$write_end_url';</script>";
				exit;
				break;

			case "reply_end":
				//���Ẹ�ȼ��� ���� �θ�â ���ΰ�ħ�� ���� https ���� http�� ��ȯ
				echo "<script>alert('�亯�� ��ϵǾ����ϴ�.');opener.location.reload();window.close()</script>";
				exit;
				break;
		}

// ó�� �� �׼�
if($mode == "set" || $mode == "replySet") go($_POST['returnUrl']);
?>
