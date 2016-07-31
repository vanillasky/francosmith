<?php
include "../lib/library.php";

if(!$session->m_no) {
	msg("로그인하셔야 본 서비스를 이용하실 수 있습니다",$sitelink->link("member/login.php"));
	exit;
}

if (get_magic_quotes_gpc()) {
	stripslashes_all($_POST);
	stripslashes_all($_GET);
}

$mode = $_REQUEST['mode'];

$attd = Core::loader('attendance');

if (class_exists('validation') && method_exists('validation', 'xssCleanArray')) {
	$_POST = validation::xssCleanArray($_POST, array(
	    validation::DEFAULT_KEY => 'text',
	    'comment' => array('html', 'ent_noquotes'),
	));
}

if (class_exists('validation') && method_exists('validation', 'xssCleanArray')) {
	$_GET = validation::xssCleanArray($_GET, array(
	    validation::DEFAULT_KEY => 'text',
	    'comment' => array('html', 'ent_noquotes'),
	));
}

switch($mode) {
    case 'check':
        $attendance_no = (int)$_GET['attendance_no'];
       	
        if($result = $attd->check($attendance_no,$session->m_no)) {
            switch($result) {
                case 'NOT_VALID_ATTENDANCE':
                    msg('유효하지 않은 출석체크입니다');
                break;
				case 'NOT_VALID_DATE':
					msg('출석체크 기간이 아닙니다');
				break;
                case 'ALREADY_CHECKED':
                    msg('이미 출석체크 되었습니다');
                break;
            }
            exit;
        }
        
		$check_message = $attd->get_check_message($attendance_no);
        echo "
        <script>
        alert('{$check_message}');
        parent.location.href=parent.location.href;
        </script>
        ";
		exit;
    break;
    case 'comment_add':
    	$attendance_no=(int)$_POST['attendance_no'];
    	$comment = $_POST['comment'];
    
    	if($result = $attd->check($attendance_no,$session->m_no)) {
		switch($result) {
			case 'NOT_VALID_ATTENDANCE':
				msg('유효하지 않은 출석체크입니다');
			break;
			case 'NOT_VALID_DATE':
				msg('출석체크 이벤트 기간이 아닙니다');
			break;
			case 'ALREADY_CHECKED':
				msg('이미 출석체크 되었습니다');
			break;
			}
			exit;
		}

		$attd->add_comment($attendance_no,$session->m_no,$comment);
    	
    	$check_message = $attd->get_check_message($attendance_no);
    	
    	if($result=='') {
    		msg($check_message);
    	}
    	else {
    		msg('댓글이 등록되었습니다');
    	}
    	
    	echo "
    	<script>
    	parent.location.href=parent.location.href;
    	</script>
    	";
    	
    	
    	
    	exit;
    break;
    
}



?>
