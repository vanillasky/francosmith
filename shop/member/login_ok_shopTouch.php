<?php
include("../lib/library.php");

if (get_magic_quotes_gpc()) {
	stripslashes_all($_POST);
	stripslashes_all($_GET);
}

if ($_POST['mode']=="guest"){ // 비회원 주문목록보기
	$ordno = (string)$_POST['ordno'];
	$nameOrder = (string)$_POST['nameOrder'];
	
	// 변수 유효성 검증
	$validation_check = array(
		'ordno'=>array('require'=>true,'pattern'=>'/^[0-9]+$/'),
		'nameOrder'=>array('require'=>true),
	);
	$chk_result = array_value_cheking($validation_check,array('ordno'=>$ordno,'nameOrder'=>$nameOrder));

	if(count($chk_result)) {
		msg("주문자명과 주문번호가 일치하는 주문이 존재하지 않습니다",-1);
	}
	
	// 주문번호와 주문자명으로 조회
	$query = $db->_query_print("select ordno from gd_order where ordno=[s] and nameOrder=[s]",$ordno,$nameOrder);
	$result = $db->_select($query);
	if($result[0]['ordno']) {
		setcookie("guest_ordno",$ordno,0,'/');
		setcookie("guest_nameOrder",$nameOrder,0,'/');
		go('/shopTouch/shopTouch_myp/orderlist.php');
	}
	else {
		msg("주문자명과 주문번호가 일치하는 주문이 존재하지 않습니다",-1);
	}
	exit;
}
else if ($_POST['mode']=="adult_guest") {

	include "../conf/fieldset.php";

	if ( $realname[useyn] == 'y' && !empty($realname[id]) ){

		// 인증 처리 및 페이지 이동은 아래 파일에서 처리 함.
		require_once( "./realname/RNCheckRequest.php" );
		exit;
	}
	else {
		msg("성인인증 서비스를 사용하고 있지 않습니다.");
	}
}
else { // 회원 로그인 부분
	$m_id = (string)$_POST['m_id'];
	$password = (string)$_POST['password'];

	$result = $session->login($m_id,$password);

	if($result !== true) {
		if($result==='NOT_FOUND') {
			msg('아이디 또는 비밀번호 오류입니다', -1);
		}
		elseif($result==='NOT_ACCESS') {
			msg('고객님은 본 사이트에서 승인되지 않아 로그인이 제한됩니다.', -1);
		}
		elseif($result==='NOT_VALID') {
			msg('아이디 또는 비밀번호 입력 형식 오류입니다', -1);
		}
		exit;
	}
	
	//출석체크관련 처리
	if(!preg_match('/admin/',$_POST['returnUrl'])) {
		$attd = Core::loader('attendance');
		$result = $attd->login_check($session->m_no);
		if($result) {
			
			msg($attd->get_check_message($result));  
		}
	}

	### ace카운터 처리 부분
	$Acecounter = Core::loader('Acecounter');
	$Acecounter->get_common_script();
	$Acecounter->member_login($session->m_id);
	if($Acecounter->scripts){
		echo $Acecounter->scripts;
	}
	
	## 로그인 내역 기록
	member_log( $session->m_id );

	## 운영 체크
	if ($session->level > 80) { 
		include(SHOPROOT.'/proc/shop_warning_msg.php');
	}



	// 투데이샵 분류 설정
	$todayshop = Core::loader('todayshop');
	if ($todayshop->auth() && $todayshop->cfg['useTodayShop'] == 'y') {
		$ts_interest = unserialize(stripslashes($todayshop->cfg['interest']));
		if ($ts_interest['use'] == 'y') {
			// 관심 분류가 등록되어 있는가
			list($sc) = $db->fetch("SELECT category FROM ".GD_TODAYSHOP_SUBSCRIBE." WHERE m_id = '".$session->m_id."' AND category <> '' ");

			if (!$sc) $ext_param = '&interest=1';
			else	 {
				$ext_param = '&category='.$sc;
				$_POST['returnUrl'] = isset($_POST['returnUrl']) ? str_replace('today_goods.php','today_list.php',$_POST['returnUrl']) : str_replace('today_goods.php','today_list.php',$_SERVER['HTTP_REFERER']);
			}
		}
	}
}


$mem_query = $db->_query_print('SELECT name FROM '.GD_MEMBER.' WHERE m_id=[s]', $session->m_id);
$mem_res = $db->_select($mem_query);
$mem_name = $mem_res[0]['name'];

if($_POST['save_id']=='y') {
    setcookie('save_id',$_POST['m_id'],time()+3600*24*5,'/');
}
else {
	setcookie('save_id','',time(),'/');
}

if($_POST['save_pw']=='y') {
    setcookie('save_pw',base64_encode($_POST['password']),time()+3600*24*5,'/');
}
else {
	setcookie('save_pw','',time(),'/');
}

if($_POST['returnUrl']) {
	msg('로그인 성공', 'vumall://vercoop.com/login_success?close=false&usr_nm='.urlencode(iconv('euc-kr', 'utf-8', $mem_name)).'&redirect='.urlencode('http://'.$_SERVER['HTTP_HOST'].$_POST['returnUrl']));
}
else {
	msg('로그인 성공', 'vumall://vercoop.com/login_success?close=true&usr_nm='.urlencode(iconv('euc-kr', 'utf-8', $mem_name)));
}


?>
