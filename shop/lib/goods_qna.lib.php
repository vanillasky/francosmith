<?php
### qna 권한체크
function goods_qna_chkAuth($data){
	global $cfg,$sess;

	### 공지글일 경우
	if($data['notice'])return array('N','N','Y');

	### 게시물 인증 세션
	$qna_auth = unserialize($_SESSION['qna_auth']);
	if(!$qna_auth) $qna_auth = array();

	### 비회원처리
	if ( empty($data['m_no']) ) $data['m_id'] = $data['name']; // 비회원명

	### 권한설정
	$data['authmodify'] = $data['authdelete'] = $data['authview'] = 'Y'; # 권한초기값
	if(!$cfg['qnaSecret']){
		$data['secret'] = 0;
	}
	if($data['secret'] == '1'){
		$data['authmodify'] = $data['authdelete'] = $data['authview'] = 'N'; # 권한초기값
		$data['authview'] = ( isset($sess) && $sess['m_no'] == $data['m_no'] ? 'Y' : 'N' );
	}

	### 게시물 권한 체크
	if ( empty($cfg['qnaWriteAuth']) || isset($sess) || !empty($data['m_no']) ){ // 회원전용 or 회원 or 작성자==회원
		$data['authmodify'] = ( isset($sess) && $sess['m_no'] == $data['m_no'] ? 'Y' : 'N' );
		$data['authdelete'] = ( isset($sess) && $sess['m_no'] == $data['m_no'] ? 'Y' : 'N' );
	}

	### 원본 게시물 권한 체크
	if ( empty($cfg['qnaWriteAuth']) || isset($sess) || !empty($data['parent_m_no']) ){ // 회원전용 or 회원 or 작성자==회원
		if( isset($sess) && $sess['m_no'] == $data['parent_m_no']) $data['authview'] = 'Y';
	}

	### 원본 게시물 인증 체크
	if(in_array($data['sno'],$qna_auth)) $data['authmodify'] = $data['authdelete'] = $data['authview'] = 'Y';
	if(in_array($data['parent'],$qna_auth)) $data['authview'] = 'Y';

	### 관리자일경우
	if(isset($sess) && $sess['level'] > 79){
		$data['authmodify'] = $data['authdelete'] = $data['authview'] =  'Y';
	}
	return array($data['authmodify'],$data['authdelete'],$data['authview']);
}

### 답변
function goods_qna_answer($sno,$parent,$secret,$notice=''){
	global $db;
	$type = 'Q';
	if($sno != $parent){
		$query = "select m_no,secret from ".GD_GOODS_QNA." where sno='".$parent."' limit 1";
		list($parent_m_no,$parent_secret) = $db->fetch($query);
		if($secret == 0 && $parent_secret == 1) $secret = 1;
		$type = 'A';
	}
	if($notice) $type = 'N';
	return array($parent_m_no,$secret,$type);
}

### 답변 여부
function goods_qna_answer_yn($sno){
	global $db;

	$query = "SELECT COUNT(sno) as cnt_answer FROM ".GD_GOODS_QNA." WHERE parent='".$sno."' AND sno != '".$sno."'";
	list($cnt_answer) = $db->fetch($query);

	return $cnt_answer;
}
?>