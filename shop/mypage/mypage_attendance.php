<?php
include "../_header.php";
if (get_magic_quotes_gpc()) {
	stripslashes_all($_POST);
	stripslashes_all($_GET);
}


if(!$session->m_no) {
	msg("로그인하셔야 본 서비스를 이용하실 수 있습니다",$sitelink->link("member/login.php?returnUrl=".$_SERVER[PHP_SELF]));
	exit;
}

$attd = Core::loader('attendance');

$page = (int)$_GET['page'] ? (int)$_GET['page'] : 1;

$query = $db->_query_print("
	select
		a.attendance_no,
		a.name,
		a.start_date,
		a.end_date,
		a.condition_type,
		a.condition_period,
		a.manual_stop,
		ac.check_period	
	from
		gd_attendance as a
		left join gd_attendance_check  as ac on a.attendance_no=ac.attendance_no and ac.member_no=[s]
	order by
		a.attendance_no desc
",$session->m_no);
$result = $db->_select_page(10,$page,$query);

$int_curdate = (int)date('Ymd');
foreach($result['record'] as $k=>$v) {
	if($v['condition_period'] <= $v['check_period']) {
		$result['record'][$k]['case'] = true;
	}
	else {
		$result['record'][$k]['case'] = false;
	}
	$int_startdate = (int)str_replace('-','',$v['start_date']);
	$int_enddate = (int)str_replace('-','',$v['end_date']);
	
	if($v['manual_stop']=='y') {
		$result['record'][$k]['status'] = 'stop';
	}
	else {
		if($int_curdate >= $int_startdate && $int_curdate <= $int_enddate) {
			$result['record'][$k]['status'] = 'progress';
		}
		elseif($int_curdate >= $int_startdate) {
			$result['record'][$k]['status'] = 'ready';
		}
		else {
			$result['record'][$k]['status'] = 'done';
		}
	}
	
	
	$result['record'][$k]['check_period']=(int)$result['record'][$k]['check_period'];

	
}

$tpl->assign('loop',$result['record']);
$tpl->assign('pg',$result['page']);


$tpl->print_('tpl');
?>
