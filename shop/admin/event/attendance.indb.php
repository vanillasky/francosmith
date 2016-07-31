<?php
include "../lib.php";
if(get_magic_quotes_gpc()) {
	stripslashes_all($_POST);
	stripslashes_all($_GET);
}
$mode = $_REQUEST['mode'];

$attd = Core::loader('attendance');

if($mode=='add') {


	// POST로 넘어온값 정리
	$ar_insert=array(
		'name'=>(string)$_POST['name'],
		'start_date'=>substr($_POST['start_date'],0,4).'-'.substr($_POST['start_date'],4,2).'-'.substr($_POST['start_date'],6,2),
		'end_date'=>substr($_POST['end_date'],0,4).'-'.substr($_POST['end_date'],4,2).'-'.substr($_POST['end_date'],6,2),
		'mobile_useyn'=>(string)$_POST['mobile_useyn'],
		'condition_type'=>(string)$_POST['condition_type'],
		'condition_period'=>(string)$_POST['condition_period'],
		'provide_method'=>(string)$_POST['provide_method'],
		'auto_reserve'=>(int)$_POST['auto_reserve'],
		'check_method'=>(string)$_POST['check_method'],
		'check_message_type'=>(string)$_POST['check_message_type'],
		'check_message_select'=>(int)$_POST['check_message_select'],
		'check_message_custom'=>(string)$_POST['check_message_custom'],
		'design_head_type'=>(string)$_POST['design_head_type'],
		'design_head_image'=>(string)$_POST['design_head_image'],
		'design_head_html'=>(string)$_POST['design_head_html'],
		'design_body'=>(string)$_POST['design_body'],
		'design_stamp'=>(string)$_POST['design_stamp'],
	);

	$result = $attd->add_attendance($ar_insert);

	switch($result) {
		case 'NOT_VALID_DATA':
			msg('입력값을 다시 확인해주세요');
			exit;
			break;
		case 'NOT_VALID_START_END_DATE':
			msg('진행기간이 잘못 입력 되었습니다');
			exit;
			break;
		case 'DATE_OVERLAP':
			msg('다른 출석체크이벤트와 날짜가 겹칩니다');
			exit;
			break;
	}

	if($result) {
		if($_FILES['design_head_file']['error'] == UPLOAD_ERR_OK) {
			move_uploaded_file($_FILES['design_head_file']['tmp_name'],SHOPROOT.'/data/attendance/custom/'.$result.'_head.jpg');
		}
		if($_FILES['design_stamp_upload']['error'] == UPLOAD_ERR_OK) {
			move_uploaded_file($_FILES['design_stamp_upload']['tmp_name'],SHOPROOT.'/data/attendance/custom/'.$result.'_stamp.jpg');
		}
	}

	echo "
	<script>
	alert('등록되었습니다');
	parent.location.href='attendance_list.php';
	</script>
	";

}
elseif($mode=='modify') {
	$attendance_no = (int)$_POST['attendance_no'];

	$ar_update=array(
		'name'=>(string)$_POST['name'],
		'design_head_type'=>(string)$_POST['design_head_type'],
		'design_head_image'=>(string)$_POST['design_head_image'],
		'design_head_html'=>(string)$_POST['design_head_html'],
		'design_body'=>(string)$_POST['design_body'],
		'design_stamp'=>(string)$_POST['design_stamp'],
	);

	$resutl = $attd->modify_attendance($attendance_no,$ar_update);

	switch($result) {
		case 'NOT_VALID_DATA':
			msg('입력값을 다시 확인해주세요');
			exit;
			break;
	}

	if($_FILES['design_head_file']['error'] == UPLOAD_ERR_OK) {
		move_uploaded_file($_FILES['design_head_file']['tmp_name'],SHOPROOT.'/data/attendance/custom/'.$attendance_no.'_head.jpg');
	}
	if($_FILES['design_stamp_upload']['error'] == UPLOAD_ERR_OK) {
		move_uploaded_file($_FILES['design_stamp_upload']['tmp_name'],SHOPROOT.'/data/attendance/custom/'.$attendance_no.'_stamp.jpg');
	}

	echo "
	<script>
	alert('수정되었습니다');
	parent.location.reload();
	</script>
	";

}
elseif($mode=='attd_delete') {

	$attendance_no = (int)$_GET['attendance_no'];
	$attd->delete_attendance($attendance_no);
	echo "
	<script>
	alert('삭제되었습니다');
	parent.location.href=parent.location.href;
	</script>
	";
}
elseif($mode=='attd_stop') {

	$attendance_no = (int)$_GET['attendance_no'];
	$attd->stop_attendance($attendance_no);
	echo "
	<script>
	alert('종료되었습니다');
	parent.location.href=parent.location.href;
	</script>
	";
}
elseif($mode=='reserve') {
	$ar_check_no = explode(',',$_POST['check_no']);
	$reserve = (int)$_POST['reserve']; // 지급될 적립금
	$msg = (string)$_POST['msg']; // SMS 메세지
	$callback = (string)$_POST['callback']; // SMS 보내는이
	$smsyn = $_POST['smsyn']; // SMS 보내기 유무

	$reserve_memo = '출석체크 이벤트 혜택 지급';

	$sms = Core::loader('sms');
	$sms_sendlist = $sms->loadSendlist();

	// check_no 로 회원정보 찾기
	$query = $db->_query_print("
		select
			ac.check_no,
			ac.member_no,
			m.mobile
		from
			gd_attendance_check as ac
			inner join gd_member as m on ac.member_no=m.m_no
		where
			ac.check_no in [v]
	",$ar_check_no);
	$result = $db->_select($query);


	foreach($result as $v) {
		// 적립금 지급
		$query = $db->_query_print('
			insert into gd_log_emoney set
				m_no = [s],
				emoney = [s],
				memo = [s],
				regdt = now()
		',$v['member_no'],$reserve,$reserve_memo);
		$db->query($query);

		$query = $db->_query_print('update gd_member set emoney = emoney + [i] where m_no = [s]',$reserve,$v['member_no']);
		$db->query($query);

		$query = $db->_query_print('update gd_attendance_check set reserve = [s] , provide_method = "manual" where check_no = [s]',$reserve,$v['check_no']);
		$db->query($query);

		// SMS보내기
		if($smsyn=='1') {
			$sms->log($msg,$v['mobile'],0,1);
			$sms_sendlist->setSimpleInsert($v['mobile'], $sms->smsLogInsertId, '');
			$sms->send($msg,$v['mobile'],$callback);
			$sms->update_ok_eNamoo = true;
			$sms->update();
		}
	}
	echo "
	<script>
	alert('처리되었습니다');
	parent.parent.location.href=parent.parent.location.href;
	</script>
	";



}




?>
