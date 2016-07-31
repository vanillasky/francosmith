<?

include "../_header.php";

### 변수할당
$mode		= $_GET[mode];
$sno		= $_GET[sno];
// 2013-01-16 dn 상품 QA 게시판 비회원 글 비밀번호 입력후 수정 폼 보여지게 수정 관련 $mode변수에 default 값 입력
if(!$mode) $mode = 'auth_qna';

### 상품 질문과답변
$query = "select m_no, subject,secret from ".GD_GOODS_QNA." where sno='$sno'";
$data = $db->fetch($query,1);

### 비밀글 인증세션 체크
$qna_auth = unserialize($_SESSION['qna_auth']);
if(!$qna_auth) $qna_auth = array();

### 로그인체크
if(!isset($sess) && $data['m_no']){
	msg('로그인해주세요!');
	echo("<script>opener.parent.location.href='../member/login.php';self.close();</script>");
}

### 템플릿 출력
$tpl->print_('tpl');

?>