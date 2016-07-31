<?

include "../_header.php"; chkMember();

if ($socialMemberService->isEnabled() && SocialMemberService::getPersistentData('social_code')) {
	list($password) = $db->fetch('SELECT password FROM '.GD_MEMBER.' WHERE m_no='.$sess['m_no']);
	if (strlen($password) < 1) {
		msg('등록된 비밀번호가 없습니다. 회원정보수정을 통해 비밀번호를 등록할 수 있습니다.', -1);
	}
}

if (class_exists('validation') && method_exists('validation', 'xssCleanArray')) {
	$_POST = validation::xssCleanArray($_POST, array(
	    validation::DEFAULT_KEY => 'text',
	));
}

if( $_POST['act'] == 'Y' && $sess && $_POST[password] ){

	extract($_POST);

	if (!$_SESSION['sess']['confirm_hack']) {
		list( $cnt ) = $db->fetch("select count(password) from ".GD_MEMBER." where m_no='" . $sess['m_no'] . "' and password in (password('".$password."'),old_password('".$password."'),'".md5($password)."')");
		if ($cnt < 1) msg('비밀번호가 일치하지 않습니다',-1);
	}

	### ace카운터
	$Acecounter->member_hack();
	if($Acecounter->scripts){
		echo $Acecounter->scripts;
	}

	### 네이버 체크아웃(회원연동)
	@include "../conf/naverCheckout.cfg.php";
	if($checkoutCfg['useYn']=='y'):
		$res = naverCheckoutHack($sess['m_no']);
		if ($res['result'] === false) {
			msg('네이버체크아웃 회원 철회가 실패되어 탈퇴할 수 없습니다.'.($res['error'] ? '\n('.$res['error'].')' : ''),-1);
		}
	endif;

	if ( is_array( $outComplain ) ){
		foreach ( $outComplain as $k => $v ) $outComplain[$k] = pow( 2, $v );
		$outComplain = @array_sum($outComplain);
	}

	// 탈퇴로그 저장
	list( $dupeinfo ) = $db->fetch("select dupeinfo from ".GD_MEMBER." where m_no='" . $sess['m_no'] . "'");
	$db->query("insert into ".GD_LOG_HACK." ( m_id, name, actor, itemcd, reason, ip, dupeinfo, regdt ) values ( '$sess[m_id]', '$member[name]', '1', '$outComplain', '$outComplain_text', '" . $_SERVER[REMOTE_ADDR] . "', '$dupeinfo', now() )" );


	{ // 데이타 삭제
		$db->query("delete from ".GD_MEMBER." WHERE m_no='" . $sess['m_no'] . "'");
		$db->query("delete from ".GD_LOG_EMONEY." WHERE m_no='" . $sess['m_no'] . "'");
		$db->query('DELETE FROM '.GD_SNS_MEMBER.' WHERE m_no='.$sess['m_no']);
	}


	if ( $member[email] && $cfg[mailyn_12] == 'y'){ ### 회원탈퇴메일
		$modeMail = 12;
		include "../lib/automail.class.php";
		$automail = new automail();
		$automail->_set($modeMail,$member[email],$cfg);
		$automail->_assign('name',$member[name]);
		$automail->_send();
	}


	msg( "정상적으로 회원탈퇴처리가 승인되었습니다. \\n\\n 그동안 이용해 주셔서 진심으로 감사합니다.", "../member/logout.php" );
}

$guideSecede = getTermsGuideContents('guide', 'guideSecede');

$tpl->assign('guideSecede', $guideSecede);
$tpl->print_('tpl');

?>