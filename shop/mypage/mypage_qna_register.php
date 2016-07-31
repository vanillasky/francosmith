<?

include "../_header.php"; chkMember();

### 변수할당
$mode		= $_GET[mode];
$sno		= $_GET[sno];

### 1:1 문의
if ( $mode == 'mod_qna' ){
	$query = "select b.m_id, a.m_no, a.itemcd, a.subject, a.contents, a.email, a.mobile, a.mailling, a.sms, a.ordno, a.parent from ".GD_MEMBER_QNA." a left join ".GD_MEMBER." b on a.m_no=b.m_no where a.sno='$sno'";
	$data = $db->fetch($query,1);
	// @qnibus 2015-06 회원아이디와 게시글 작성자 일치여부 확인 (로그인한 회원만 사용가능)
	if($sess['level'] < 80 && $sess['m_no'] != $data['m_no']) {
		msg('본인이 작성한 1:1문의만 수정하실 수 있습니다.', 'close');
	}
	$data[mobile]	= explode("-",$data[mobile]);
}
else {
	$data['m_id'] = $sess['m_id'];
}

if( $mode == 'reply_qna' || ( $sno != '' && $data['parent'] != '' && $sno != $data['parent'] ) ) $formtype = 'reply'; // 입력항목 제어


### 무료보안서버 회원처리url
$tpl->assign('myqnaActionUrl',$sitelink->link('mypage/indb.php','ssl'));

// 개인정보수집 및 이용에 대한 안내
$termsPolicyCollection4 = getTermsGuideContents('terms', 'termsPolicyCollection4');
$tpl->assign('termsPolicyCollection4', $termsPolicyCollection4);

### 템플릿 출력
$tpl->print_('tpl');

?>