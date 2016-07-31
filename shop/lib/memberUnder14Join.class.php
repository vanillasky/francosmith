<?
/**
 * 만14세 미만 회원가입 라이브러리
 * @author pr
 */
class memberUnder14Join
{
	var $useConstraint = false; // (bool) 회원가입통제여부
	var $under14Status; // 만14세 미만 가입 설정
	var $ipinYn = 'n'; // 아이핀(구 한국신용정보) 사용여부
	var $niceIpinYn = 'n'; // 아이핀(신) 사용여부
	var $hpAuthYn = 'n'; // 휴대폰본인확인 (Mcerti / 드림시큐리티) 사용여부
	var $selfCert = false; // (bool) 본인인증 사용여부

	function memberUnder14Join()
	{
		// 회원가입정책
		$cfgfile = dirname(__FILE__).'/../conf/fieldset.php';
		if(file_exists($cfgfile)) @include $cfgfile;

		// 회원가입통제여부
		if ($joinset['under14status'] == 1 || $joinset['under14status'] == 2) {
			$this->useConstraint = true;
		}
		else {
			$this->useConstraint = false;
		}

		// 만14세 미만 가입 설정
		$this->under14Status = $joinset['under14status']; // 1 : 관리자 승인 후 가입, 0 : 승인없이 가입, 2 : 가입불가

		// 본인인증 > 아이핀
		$this->ipinYn = (empty($ipin['id']) ? 'n' : empty($ipin['useyn']) ? 'n': $ipin['useyn']); // 아이핀(구 한국신용정보) 사용여부
		$this->niceIpinYn = (empty($ipin['code']) ? 'n' : empty($ipin['nice_useyn'])? 'n': $ipin['nice_useyn']); // 아이핀(신) 사용여부

		// 본인인증 > 휴대폰본인확인 (Mcerti / 드림시큐리티) 사용여부
		if (file_exists(dirname(__FILE__).'/../lib/Hpauth.class.php') === true) {
			$hpauth = Core::loader('Hpauth');
			$hpauthRequestData = $hpauth->getAuthRequestData();
			$this->hpAuthYn = (empty($hpauthRequestData['cpid']) ? 'n' : empty($hpauthRequestData['useyn'])? 'n': $hpauthRequestData['useyn']);
		}

		// 본인인증 사용여부
		if ($this->ipinYn == 'y' || $this->niceIpinYn == 'y' || $this->hpAuthYn == 'y') {
			$this->selfCert = true;
		}
		else {
			$this->selfCert = false;
		}
	}

	/**
	 * 본인인증에서 만14세 미만 회원가입 허용상태
	 *
	 * @param string $birthday 생년월일
	 * @return string (허용상태코드 - rejectJoin : 가입거부, adminStatus : 관리자 승인 후 가입, pass : 계속 진행)
	 *
	 * Ex) 만14세 미만 생년월일 : 오늘날짜(YYYYMMDD) - 14년 + 1일; (오늘날짜(20140625) => '20000626')
	 * Ex) 만14세 이상 생년월일 : 오늘날짜(YYYYMMDD) - 14년; (오늘날짜(20140625) => '20000625')
	 *
	 */
	function joinSelfCert($birthday)
	{
		// 세션 초기화
		unset($_SESSION['under14']);

		// 1. 회원가입통제여부
		if ($this->useConstraint !== true) return 'pass';

		// 2. 만14세 미만 비교
		if ($this->calMinus1Age($birthday) < 14) { // 미만
			if ($this->under14Status == '1') { // 관리자 승인 후 가입
				return 'adminStatus';
			}
			else if ($this->under14Status == '2') { // 가입불가
				return 'rejectJoin';
			}
		}
		else { // 이상
			$_SESSION['under14'] = 1;
			return 'pass';
		}
	}

	/**
	 * 가입폼작성에서 만14세 미만 회원가입 허용상태
	 *
	 * @return string (허용상태코드 - rejectJoin : 가입거부, adminStatus : 관리자 승인 후 가입, pass : 계속 진행, needBirthCheck : 생년월일 체크 필요)
	 *
	 */
	function joinWrite()
	{
		// 1. 회원가입통제여부
		if ($this->useConstraint !== true) return 'pass';

		// 2. 본인인증 체크
		if ($this->selfCert === true) { // 사용
			// 2-1. 만14세 미만 비교
			if ($_SESSION['under14'] != 1) { // 미만
				if ($this->under14Status == '1') { // 관리자 승인 후 가입
					return 'adminStatus';
				}
				else if ($this->under14Status == '2') { // 가입불가
					return 'rejectJoin';
				}
			}
			else { // 이상
				return 'pass';
			}
		}
		else { // 미사용
			return 'needBirthCheck'; // 생년월일 체크 필요
		}
	}

	/**
	 * 회원저장에서 만14세 미만 회원가입 허용상태
	 *
	 * @param string $birthday 생년월일
	 * @return string (허용상태코드 - rejectJoin : 가입거부, adminStatus : 관리자 승인 후 가입, pass : 계속 진행, over14 : 만14세 이상)
	 *
	 * Ex) 만14세 미만 생년월일 : 오늘날짜(YYYYMMDD) - 14년 + 1일; (오늘날짜(20140625) => '20000626')
	 * Ex) 만14세 이상 생년월일 : 오늘날짜(YYYYMMDD) - 14년; (오늘날짜(20140625) => '20000625')
	 *
	 */
	function joinIndb($birthday='')
	{
		// 1. 회원가입통제여부
		if ($this->useConstraint !== true) return 'pass';

		// 2. 본인인증 체크
		if ($this->selfCert === true) { // 사용
			// 2-1. 만14세 미만 비교
			if ($_SESSION['under14'] != 1) { // 미만
				if ($this->under14Status == '1') { // 관리자 승인 후 가입
					return 'adminStatus';
				}
				else if ($this->under14Status == '2') { // 가입불가
					return 'rejectJoin';
				}
			}
			else { // 이상
				return 'over14';
			}
		}
		else { // 미사용
			if ($birthday == '' || $birthday == '00000000') { // 생년월일 값 없음
				if ($this->under14Status == '1') { // 관리자 승인 후 가입
					return 'undecidableAdminStatus';
				}
				else if ($this->under14Status == '2') { // 가입불가
					return 'undecidableRejectJoin';
				}
			}
			else { // 생년월일 값 있음
				// 만14세 미만 비교
				if ($this->calMinus1Age($birthday) < 14) { // 미만
					if ($this->under14Status == '1') { // 관리자 승인 후 가입
						return 'adminStatus';
					}
					else if ($this->under14Status == '2') { // 가입불가
						return 'rejectJoin';
					}
				}
				else { // 이상
					return 'over14';
				}
			}
		}
	}

	/**
	 * 만 나이 계산
	 *
	 * @param string $birthday 생년월일
	 * @return int 만 나이
	 *
	 */

	function calMinus1Age($birthday)
	{
		$birthday = str_replace('-', '', trim($birthday));

		$now_year = date('Y', time());
		$now_month = date('m', time());
		$now_day = date('d', time());

		$birth_year = substr($birthday, 0, 4);
		$birth_month = substr($birthday, 4, 2);
		$birth_day = substr($birthday, 6, 2);

		if ($birth_month < $now_month) {
			$age = $now_year - $birth_year;
		}
		else if ($birth_month == $now_month) {
			if ($birth_day <= $now_day)
				$age= $now_year - $birth_year;
			else
				$age= $now_year - $birth_year - 1;
		}
		else {
			$age= $now_year - $birth_year - 1;
		}

		return $age;
	}
}
?>