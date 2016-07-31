<?
/*********************************************************
* ���ϸ�     :  pBoardIndb.php
* ���α׷��� :	pad �Խ��� ó��
* �ۼ���     :  dn
* ������     :  2011.10.22
**********************************************************/
include "../../lib/library.php";
include "../../conf/config.php";
require_once "../../lib/pAPI.class.php";
require_once "../../lib/json.class.php";

$pAPI = new pAPI();
$json = new Services_JSON(16);

### ����Ű Check (�����δ� ���̵�� ��� ��) ���� ###
if(!$_POST['authentic']) {
	$res_data['code'] = '302';
	$res_data['msg'] = '����Ű�� �����ϴ�.';
	echo ($json->encode($res_data));
	exit;
}

if(!($m_no = $pAPI->keyCheck($_POST['authentic'], 'm_no'))) {
	$res_data['code'] = '302';
	$res_data['msg'] = '����Ű�� ���� �ʽ��ϴ�.';
	echo ($json->encode($res_data));
	exit;
}

unset($_POST['authentic']);
### ����Ű Check �� ###

$mode = $_POST['mode'];

function mailsend($sno) {
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

unset($_POST['mode']);
if(!$mode) {
	$res_data['code'] = '301';
	$res_data['msg'] = '�߸��� ���� �Դϴ�.';
	echo ($json->encode($res_data));
	exit;
}

foreach($_POST as $key=>$val) {
	if(strstr($key, 'arr_')) {
		$tmp_arr[str_replace('arr_', '', $key)] = explode('|', $val);
	}
	else  {
		$tmp_arr[$key] = $val;
	}
}
unset($_POST);
$_POST = $tmp_arr;

switch($mode) {

	case 'modify_qna' :	// ��ǰ���� ����

		$arr_upd = array();
		$arr_upd['subject'] = $_POST['subject'];
		$arr_upd['contents'] = str_replace('<P></P>', '<P></P>', '<P>'.str_replace("\r\n", '</P>'."\r\n".'<P>', $_POST['contents']).'</P>');

		$modify_query = $db->_query_print('UPDATE '.GD_GOODS_QNA.' SET [cv] WHERE sno=[i]', $arr_upd, $_POST['sno']);
		$db->query($modify_query);
		unset($arr_upd);

		$res_data['code'] = '000';
		$res_data['msg'] = '����';		
		break;
	
	case 'reply_qna' :	// ���� �亯 ���� / ����
		
		if($_POST['sno']) {
			$arr_upd = array();
			$arr_upd['subject'] = $_POST['subject'];
			$arr_upd['contents'] = str_replace('<P></P>', '<P></P>', '<P>'.str_replace("\r\n", '</P>'."\r\n".'<P>', $_POST['contents']).'</P>');

			$reply_query = $db->_query_print('UPDATE '.GD_GOODS_QNA.' SET [cv] WHERE sno=[i]', $arr_upd, $_POST['sno']);
			unset($arr_upd);
		}
		else {
			$arr_ins = array();
			$arr_ins['goodsno'] = $_POST['goodsno'];
			$arr_ins['subject'] = $_POST['subject'];
			$arr_ins['contents'] = str_replace('<P></P>', '<P></P>', '<P>'.str_replace("\r\n", '</P>'."\r\n".'<P>', $_POST['contents']).'</P>');
			$arr_ins['parent'] = $_POST['parent'];
			$arr_ins['m_no'] = $m_no;
			$arr_ins['ip'] = $_POST['ip'];

			$reply_query = $db->_query_print('INSERT INTO '.GD_GOODS_QNA.' SET [cv], regdt=now()', $arr_ins);
			unset($arr_ins);
		}

		$db->query($reply_query);

		$res_data['code'] = '000';
		$res_data['msg'] = '����';		
		break;
	
	case 'del_qna' : // ���� ����  ���Ǳ��� ��� �亯���� ���� ����
		
		$chk_query = $db->_query_print('SELECT parent FROM '.GD_GOODS_QNA.' WHERE sno=[i]', $_POST['sno']);
		$res_chk = $db->_select($chk_query);
		$parent = $res_chk[0]['parent'];

		if($parent == $_POST['sno']) {
			$del_query = $db->_query_print('DELETE FROM '.GD_GOODS_QNA.' WHERE parent=[i]', $_POST['sno']);
		}
		else {
			$del_query = $db->_query_print('DELETE FROM '.GD_GOODS_QNA.' WHERE sno=[i]', $_POST['sno']);
		}
		$db->query($del_query);

		$res_data['code'] = '000';
		$res_data['msg'] = '����';
		break;

	case 'noti_qna' : // �������� ��� / ����
		
		if($_POST['sno']) {
			$arr_upd = array();
			$arr_upd['subject'] = $_POST['subject'];
			$arr_upd['contents'] = str_replace('<P></P>', '<P></P>', '<P>'.str_replace("\r\n", '</P>'."\r\n".'<P>', $_POST['contents']).'</P>');

			$notice_query = $db->_query_print('UPDATE '.GD_GOODS_QNA.' SET [cv] WHERE sno=[i]', $arr_upd, $_POST['sno']);
			unset($arr_upd);

			$db->query($notice_query);
		}
		else {
			$arr_ins = array();
			$arr_ins['subject'] = $_POST['subject'];
			$arr_ins['contents'] = str_replace('<P></P>', '<P></P>', '<P>'.str_replace("\r\n", '</P>'."\r\n".'<P>', $_POST['contents']).'</P>');
			$arr_ins['m_no'] = $m_no;
			$arr_ins['name'] = '������';
			$arr_ins['ip'] = $_POST['ip'];
			$arr_ins['secret'] = 0;
			$arr_ins['notice']	= 1;

			$notice_query = $db->_query_print('INSERT INTO '.GD_GOODS_QNA.' SET [cv], password=md5([s]), regdt=now()', $arr_ins, '');

			unset($arr_upd);
			
			$db->query($notice_query);

			$last_sno = $db->_last_insert_id();

			$upd_query = $db->_query_print('UPDATE '.GD_GOODS_QNA.' SET parent=sno WHERE sno=[i]', $last_sno);		

			$db->query($upd_query);
	
		}
		
		$res_data['code'] = '000';
		$res_data['msg'] = '����';
		break;

	case 'modify_review' :	// ��ǰ�ı� ����

		$arr_upd = array();
		$arr_upd['subject'] = $_POST['subject'];
		$arr_upd['contents'] = str_replace('<P></P>', '<P></P>', '<P>'.str_replace("\r\n", '</P>'."\r\n".'<P>', $_POST['contents']).'</P>');

		$modify_query = $db->_query_print('UPDATE '.GD_GOODS_REVIEW.' SET [cv] WHERE sno=[i]', $arr_upd, $_POST['sno']);
		$db->query($modify_query);
		unset($arr_upd);

		$res_data['code'] = '000';
		$res_data['msg'] = '����';		
		break;
	
	case 'reply_review' :	// �ı� �亯 ���� / ����
		
		if($_POST['sno']) {
			$arr_upd = array();
			$arr_upd['subject'] = $_POST['subject'];
			$arr_upd['contents'] = str_replace('<P></P>', '<P></P>', '<P>'.str_replace("\r\n", '</P>'."\r\n".'<P>', $_POST['contents']).'</P>');
			
			$reply_query = $db->_query_print('UPDATE '.GD_GOODS_REVIEW.' SET [cv] WHERE sno=[i]', $arr_upd, $_POST['sno']);
			$db->query($reply_query);
			unset($arr_upd);
		}
		else {
			$arr_ins = array();
			$arr_ins['goodsno'] = $_POST['goodsno'];
			$arr_ins['subject'] = $_POST['subject'];
			$arr_ins['contents'] = str_replace('<P></P>', '<P></P>', '<P>'.str_replace("\r\n", '</P>'."\r\n".'<P>', $_POST['contents']).'</P>');
			$arr_ins['parent'] = $_POST['parent'];
			$arr_ins['m_no'] = $m_no;
			$arr_ins['ip'] = $_POST['ip'];

			$reply_query = $db->_query_print('INSERT INTO '.GD_GOODS_REVIEW.' SET [cv], regdt=now()', $arr_ins);

			$db->query($reply_query);
			
			unset($arr_ins);

			### �ۼ��� ���� ����Ʈ ����
			if ( $_POST['memo'] == 'direct' ) $_POST['memo'] = $_POST['direct_memo'];
			if ( $_POST['emoney'] > 0 && $_POST['emoneyPut'] == "Y" && $_POST['writer_m_no'] && $_POST['memo'] ){
				
				# �ۼ��� ���� ���̺� ������ �Է�
				$emoney_query = $db->_query_print('UPDATE '.GD_GOODS_REVIEW.' SET emoney=[i] WHERE sno=[i]', $_POST['emoney'], $_POST['parent']);
				
				$db->query($emoney_query);
				
				# ������ ����
				$arr_log = Array();
				$arr_log['ordno'] = $_POST['parent'];
				$arr_log['m_no'] = $_POST['writer_m_no'];
				$arr_log['emoney'] = $_POST['emoney'];
				$arr_log['memo'] = $_POST['memo'];

				$log_query = $db->_query_print('INSERT INTO '.GD_LOG_EMONEY.' SET [cv], regdt=now()', $arr_log);
				$db->query($log_query);
				
				$member_query = $db->_query_print('UDPATE '.GD_MEMBER.' SET emoney=emoney+[i] WHERE m_no=[i]', $_POST['emoney'], $_POST['writer_m_no']);
				$db->query($member_query);
			}
		}

		$res_data['code'] = '000';
		$res_data['msg'] = '����';		
		break;
	
	case 'del_review' : // �ı� ����  ���Ǳ��� ��� �亯���� ���� ����
		
		$chk_query = $db->_query_print('SELECT parent FROM '.GD_GOODS_REVIEW.' WHERE sno=[i]', $_POST['sno']);
		$res_chk = $db->_select($chk_query);
		$parent = $res_chk[0]['parent'];

		if($parent == $_POST['sno']) {
			//$del_query = $db->_query_print('DELETE FROM '.GD_GOODS_REVIEW.' WHERE parent=[i]', $_POST['sno']);
			$del_query = $db->_query_print('DELETE FROM '.GD_GOODS_REVIEW.' WHERE sno=[i]', $_POST['sno']);
		}
		else {
			$del_query = $db->_query_print('DELETE FROM '.GD_GOODS_REVIEW.' WHERE sno=[i]', $_POST['sno']);
		}
		$db->query($del_query);

		$res_data['code'] = '000';
		$res_data['msg'] = '����';
		break;

	case 'modify_member_qna' : // 1:1���� ����

		$arr_upd = array();
		$arr_upd['subject'] = $_POST['subject'];
		$arr_upd['contents'] = str_replace('<P></P>', '<P></P>', '<P>'.str_replace("\r\n", '</P>'."\r\n".'<P>', $_POST['contents']).'</P>');

		$modify_query = $db->_query_print('UPDATE '.GD_MEMBER_QNA.' SET [cv] WHERE sno=[i]', $arr_upd, $_POST['sno']);
		$db->query($modify_query);
		unset($arr_upd);

		$res_data['code'] = '000';
		$res_data['msg'] = '����';
		break;

	case 'reply_member_qna' :	// 1:1���� �亯 ���� / ����
		
		if($_POST['sno']) {
			$arr_upd = array();
			$arr_upd['subject'] = $_POST['subject'];
			$arr_upd['contents'] = str_replace('<P></P>', '<P></P>', '<P>'.str_replace("\r\n", '</P>'."\r\n".'<P>', $_POST['contents']).'</P>');
			
			$reply_query = $db->_query_print('UPDATE '.GD_MEMBER_QNA.' SET [cv] WHERE sno=[i]', $arr_upd, $_POST['sno']);
			$db->query($reply_query);
			unset($arr_upd);

			$new_sno = $_POST['sno'];
		}
		else {
			$arr_ins = array();
			$arr_ins['ordno'] = $_POST['ordno'];
			$arr_ins['subject'] = $_POST['subject'];
			$arr_ins['contents'] = str_replace('<P></P>', '<P></P>', '<P>'.str_replace("\r\n", '</P>'."\r\n".'<P>', $_POST['contents']).'</P>');
			$arr_ins['parent'] = $_POST['parent'];
			$arr_ins['m_no'] = $m_no;
			$arr_ins['ip'] = $_POST['ip'];

			$reply_query = $db->_query_print('INSERT INTO '.GD_MEMBER_QNA.' SET [cv], regdt=now()', $arr_ins);
			$db->query($reply_query);
			unset($arr_ins);	

			$new_sno = $db->_last_insert_id();
		}
		
		if ( $_POST['mailyn'] == 'Y' && $new_sno ) mailsend( $new_sno ); ### ���Ǵ亯����
		if ( $_POST['smsyn'] == 'Y' && $new_sno ) smssend( $new_sno ); ### ���Ǵ亯SMS

		$res_data['code'] = '000';
		$res_data['msg'] = '����';		
		break;

	case 'del_member_qna' :
		$chk_query = $db->_query_print('SELECT parent FROM '.GD_MEMBER_QNA.' WHERE sno=[i]', $_POST['sno']);
		$res_chk = $db->_select($chk_query);
		$parent = $res_chk[0]['parent'];

		if($parent == $_POST['sno']) {
			//$del_query = $db->_query_print('DELETE FROM '.GD_MEMBER_QNA.' WHERE parent=[i]', $_POST['sno']);
			$del_query = $db->_query_print('DELETE FROM '.GD_MEMBER_QNA.' WHERE sno=[i]', $_POST['sno']);
		}
		else {
			$del_query = $db->_query_print('DELETE FROM '.GD_MEMBER_QNA.' WHERE sno=[i]', $_POST['sno']);
		}
		$db->query($del_query);

		$res_data['code'] = '000';
		$res_data['msg'] = '����';
		break;
}

echo ($json->encode($res_data));