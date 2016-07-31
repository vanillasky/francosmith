<?php
/*
	Session 클래스
*/
class session {

	var $m_no;
	var $m_id;
	var $level;
	var $groupsno;
	var $dc;

	function session() {
		if($_SESSION['sess']['m_no']) {
			$this->m_no=$_SESSION['sess']['m_no'];
			$this->m_id=$_SESSION['sess']['m_id'];
			$this->level=$_SESSION['sess']['level'];
			$this->groupsno=$_SESSION['sess']['groupsno'];
			$this->dc=$_SESSION['sess']['dc'];
		}
		else {
			$this->m_no=false;
			$this->m_id='';
			$this->level='';
			$this->groupsno='';
			$this->dc='';
		}
	}

	/*
		로그인

		return 값
		정상적으로 로그인 된 경우 = true
		아이디나 비밀번호가 입력형식에 어긋난 경우 = NOT_VALID
		아이디나 비밀번호 맞지 않는 경우 = NOT_FOUND
		접속이 승인되지 않는 경우 = NOT_ACCESS
	*/
	function login($id,$password) {
		// 입력 형식 체크
		$validation_check = array(
			'id'=>array('require'=>true,'pattern'=>'/^[\xa1-\xfea-zA-Z0-9_-]{4,20}$/'),
			'password'=>array('require'=>true,'pattern'=>'/^[\x21-\x7E]{4,}$/'),
		);
		$chk_result = array_value_cheking($validation_check,array('id'=>$id,'password'=>$password));
		if(count($chk_result)) {
			return 'NOT_VALID';
		}

		// 아이디,비밀번호 조회
		$db = Core::loader('db');

		//휴면회원 체크 - 비밀번호 확인 방식 변경시 같이 변경 요망
		$dormantRestore = false;
		$dormant = Core::loader('dormant');
		$dormantRestore = $dormant->checkDormantLogin($id, $password);

		$query = $db->_query_print('
			select
				m.m_no,
				m.m_id,
				m.name,
				m.nickname,
				m.email,
				m.status,
				m.level,
				m.password_moddt,
				g.dc,
				g.sno gsno
			from
				gd_member as m
				left join gd_member_grp as g on m.level=g.level
			where
				m.m_id = [s] and
				m.password in (password([s]),old_password([s]),[s])
		',$id,$password,$password,md5($password));

		$result = $db->_select($query);
		$result = $result[0];

		if(!$result['m_no']) { // 일치하는 결과 값이 없는 경우
			return 'NOT_FOUND';
		}

		if($result['status']==1)  { // 로그인이 성공한 경우
			// 모바일의 자동로그인 쿠키가 있는 경우 예외처리
			if ($result['level'] >= 80 && function_exists('accessCookieMemberInfo')) {
				// 자동로그인 쿠키가 있는 경우
				if (accessCookieMemberInfo()) {
					// 자동로그인 쿠키를 파괴한후 로그인 실패 처리
					expireCookieMemberInfo();
					return false;
				}
			}

			// 로그인 성공에 따른 최근 로그인날짜&휴면회원안내정보 갱신
			$query = $db->_query_print('
				update gd_member set
					last_login = now(),
					cnt_login = (cnt_login+1),
					last_login_ip = [s],
					dormant_mailSendDate = "",
					dormant_smsSendCheck = ""
				where
					m_no = [s]
			',$_SERVER['REMOTE_ADDR'],$result['m_no']);
			$db->query($query);

			// 세션정보 저장
			$_SESSION['sess']=array(
				'm_no'=>$result['m_no'],
				'm_id'=>$result['m_id'],
				'level'=>$result['level'],
				'groupsno'=>$result['gsno'],
				'dc'=>($result['dc'] ? $result['dc'].'%' : ''),
			);
			$_SESSION['member']=array(
				'name'=>$result['name'],
				'email'=>$result['email'],
				'nickname'=>$result['nickname'],
				'password_moddt'=>$result['password_moddt'],
			);
			$this->session();

			//휴면회원 로그인 성공시
			if($dormantRestore === true){
				echo '<script>alert("회원님의 계정이 휴면회원에서 일반 회원으로 변경 되었습니다.\n정상적으로 서비스를 이용할 수 있습니다.");</script>';
			}

			return true;
		}
		else {
			return 'NOT_ACCESS';
		}

	}

	/*
		세션 갱신

	 *	회원이 로그인 한 상태에서
	 *	관리자가 로그인 한 회원 등급 변경 또는 회원 정보 변경을
	 *	했을 경우
	 *	회원이 페이지 이동 또는 새로고침 할 시 변경된 상태 적용
	*/
	function sessionUpdate(){
		if($_SESSION['sess']['m_no']){ // 회원 로그인 되어 있는 상태
			$db = Core::loader('db');

			$query = $db->_query_print('
				select
					m.m_no,
					m.m_id,
					m.name,
					m.nickname,
					m.email,
					m.status,
					m.level,
					m.password_moddt,
					g.dc,
					g.sno gsno
				from
					gd_member as m
					left join gd_member_grp as g on m.level=g.level
				where
					m.m_no = [s]
			',$_SESSION['sess']['m_no']);

			$result = $db->_select($query);
			$result = $result[0];

			if(!$result['m_no']) { // 회원이 로그인 되어 있는 상태에서 관리자가 삭제 했을 경우
				$this->logout();
				$this->session();
			}

			if($result['status']==1)  { // 회원이 존재하는 성공한 경우
				// 세션정보 저장
				$_SESSION['sess']['m_no'] = $result['m_no'];
				$_SESSION['sess']['m_id'] = $result['m_id'];
				$_SESSION['sess']['level'] = $result['level'];
				$_SESSION['sess']['groupsno'] = $result['gsno'];
				$_SESSION['sess']['dc'] = ($result['dc'] ? $result['dc'].'%' : '');

				$_SESSION['member']['name'] = $result['name'];
				$_SESSION['member']['email'] = $result['email'];
				$_SESSION['member']['nickname'] = $result['nickname'];
				$_SESSION['member']['password_moddt'] = $result['password_moddt'];
				$this->session();
			}
			else { // 회원이 로그인 되어 있는 상태에서 관리자가 미승인 변경 했을 경우
				$this->logout();
				$this->session();
			}
		} else { // 로그인 안되어 있는 상태
		}
	}

	function socialLogin(SocialMember $socialMember)
	{
		$db = Core::loader('db');
		$m_no = $socialMember->getMemberNo();

		//휴면회원 체크 - 비밀번호 확인 방식 변경시 같이 변경 요망
		$dormantRestore = false;
		$dormant = Core::loader('dormant');
		$dormantRestore = $dormant->checkDormantSocialLogin($m_no);

		// 회원정보 조회
		$query = $db->_query_print('
			select
				m.m_no,
				m.m_id,
				m.name,
				m.nickname,
				m.email,
				m.status,
				m.level,
				m.password_moddt,
				g.dc,
				g.sno gsno
			from
				gd_member as m
				left join gd_member_grp as g on m.level=g.level
			where
				m.m_no = [i]
		',$m_no);

		$result = $db->_select($query);
		$result = $result[0];

		if(!$result['m_no']) { // 일치하는 결과 값이 없는 경우
			return 'NOT_FOUND';
		}

		if($result['status']==1)  { // 로그인이 성공한 경우
			// 로그인 성공에 따른 최근 로그인날짜 갱신
			$query = $db->_query_print('
				update gd_member set
					last_login = now(),
					cnt_login = (cnt_login+1),
					last_login_ip = [s],
					dormant_mailSendDate = "",
					dormant_smsSendCheck = ""
				where
					m_no = [s]
			',$_SERVER['REMOTE_ADDR'],$result['m_no']);
			$db->query($query);

			// 세션정보 저장
			$_SESSION['sess']=array(
				'm_no'=>$result['m_no'],
				'm_id'=>$result['m_id'],
				'level'=>$result['level'],
				'groupsno'=>$result['gsno'],
				'dc'=>($result['dc'] ? $result['dc'].'%' : ''),
			);
			$_SESSION['member']=array(
				'name'=>$result['name'],
				'email'=>$result['email'],
				'nickname'=>$result['nickname'],
				'password_moddt'=>$result['password_moddt'],
			);
			$this->session();

			//휴면회원 로그인 성공시
			if($dormantRestore === true){
				echo '<script>alert("회원님의 계정이 휴면회원에서 일반 회원으로 변경 되었습니다.\n정상적으로 서비스를 이용할 수 있습니다.");</script>';
			}

			return true;
		}
		else {
			return 'NOT_ACCESS';
		}
	}

	/*
		로그아웃
	*/
	function logout() {
		session_unset();
		session_destroy();
		setCookie('Xtime','',0,'/');
		setcookie('gd_cart','',time() - 3600,'/');
		setcookie('gd_cart_direct','',time() - 3600,'/');
		if (SocialMemberService::getPersistentData('social_code')) {
			$this->socialLogout(SocialMemberService::getPersistentData('social_code'));
		}
	}

	function socialLogout($socialCode)
	{
		include dirname(__FILE__).'/SocialMember/SocialMemberServiceLoader.php';
		$socialMember = SocialMemberService::getMember($socialCode);
		$socialMember->logout();
	}

}


?>
