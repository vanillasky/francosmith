<?php
include("../lib/library.php");

if (get_magic_quotes_gpc()) {
	stripslashes_all($_POST);
	stripslashes_all($_GET);
}

// 관리자 로그인에서 관리자보안 인증상태 체크
$_ref = parse_url($_SERVER['HTTP_REFERER']);
if (preg_match('/admin\/login\/login\.php$/',$_ref['path']) || $_POST['adm_login']) {
	$alCert = Core::loader('adminLoginCert');
	$alStat = $alCert->loginStatus();
	if ($alStat == 'failure') {
		msg('관리자 휴대폰 보안 인증을 먼저 하세요.', '../admin/login/adm_login_cert.php');
	}
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
		go('../mypage/mypage_orderlist.php');
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
		msg("성인인증 서비스를 사용하고 있지 않습니다.",-1);
	}
}
else { // 회원 로그인 부분
	if ($_GET['SOCIAL_CODE']) {
		include dirname(__FILE__).'/../lib/SocialMember/SocialMemberServiceLoader.php';
		$socialMemberService = SocialMemberService::getMember($_GET['SOCIAL_CODE']);
		$result = $socialMemberService->login();
		$_POST['returnUrl'] = $_GET['return_url'];
	}
	else {
		$m_id = (string)$_POST['m_id'];
		$password = (string)$_POST['password'];
		$result = $session->login($m_id, $password);
	}

	if($result!==true) {
		if($result==='NOT_FOUND') {
			msg('아이디 또는 비밀번호 오류입니다',-1);
		}
		elseif($result==='NOT_ACCESS') {
			msg('고객님은 본 사이트에서 승인되지 않아 로그인이 제한됩니다.',-1);
		}
		if($result==='NOT_VALID') {
			msg('아이디 또는 비밀번호 입력 형식 오류입니다',-1);
		}
		exit;
	}

	// 관리자보안 인증 로그인 처리
	if ($alStat == 'success') {
		$_SESSION['alcAccess'] = md5(crypt(''));
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

	// 패스워드 변경 캠페인
	$info_cfg = $config->load('member_info');
	$_ref = parse_url($_SERVER['HTTP_REFERER']);
	if (( preg_match('/admin\/login\/login\.php$/',$_ref['path']) || preg_match('/member\/login\.php$/',$_ref['path']) || preg_match('/main\/intro_adult\.php$/',$_ref['path']) || preg_match('/main\/intro_member\.php$/',$_ref['path']) )
		&&
		( $info_cfg['campaign_use'] && is_file('../data/skin/'.$cfg['tplSkin'].'/member/password_campaign.htm') )) {

		$show = true;

		// 다음에 변경하기
		if ($show && $_COOKIE['campaign_disregarded_date']) {

			$operate = sprintf('%+d day', $info_cfg['campaign_next_term']);
			$tmp = strtotime($_COOKIE['campaign_disregarded_date']);
			$tmp = strtotime($operate ,$tmp);

			if ($tmp > time()) {
				$show = false;
			}

		}

		if ($show && ($tmp = (int)strtotime($_SESSION['member']['password_moddt'])) > 0) {

			$operate = sprintf('%+d %s', $info_cfg['campaign_period_value'], ($info_cfg['campaign_period_type'] == 'd' ? 'day' : 'month'));
			$tmp = strtotime($operate ,$tmp);

			if ($tmp > time()) {
				$show = false;
			}

		}

		if ($show) {
			$ext_param .= '&ori_returnUrl='.($_POST['returnUrl'] ? urlencode($_POST['returnUrl']) : urlencode($_SERVER['HTTP_REFERER']));
			$_POST['returnUrl'] = str_replace('member/login_ok.php','member/password_campaign.php', $_SERVER['REQUEST_URI']);
		}

	}
}

$auth_date = getAdultAuthDate($session->m_id);
$auth_date = $auth_date['auth_date'];
$current_date = date("Y-m-d");
$auth_period = strtotime("+1 years", strtotime($auth_date)); 
$auth_period = date("Y-m-d", $auth_period);

if(!$_POST['returnUrl']) { $_POST['returnUrl'] = $_SERVER['HTTP_REFERER']; }

if(strpos($_SERVER['HTTP_REFERER'], 'main/intro_adult.php') && ($auth_date == '0000-00-00' || $current_date > $auth_period) && ((int)($session->level) < 80) ){
	go($cfg[rootDir].'/main/intro_adult_login.php?returnUrl=' . urlencode($_POST['returnUrl']) . ($_SERVER['QUERY_STRING'] ? '&'.$_SERVER['QUERY_STRING'] : ''));
}
else{
	if(($auth_date != '0000-00-00' && $current_date < $auth_period) || ((int)($session->level) > 80)){
		$_SESSION['adult'] = 1;
	}
	$div = explode("/",$_POST['returnUrl']);
	if (preg_match("/http/",$div[0]) && in_array($div[count($div)-2],array("member","mypage"))) $_POST['returnUrl'] = "../main/index.php";
	$_POST['returnUrl'] = preg_match('/\?/',$_POST['returnUrl']) ? $_POST['returnUrl'].$ext_param : $_POST['returnUrl'].'?'.$ext_param;

	if (preg_match('/(^\.\.\/)|(^\/shop\/)/',$_POST['returnUrl'])) {
		$_POST['returnUrl'] = preg_replace('/(^\.\.\/)|(^\/shop\/)/', '', $_POST['returnUrl']);
		$_POST['returnUrl'] = $sitelink->link($_POST['returnUrl'],'regular');
	}
	go($_POST['returnUrl']);
}

?>