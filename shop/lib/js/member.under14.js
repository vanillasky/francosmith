/**
 * 만14세 미만 회원가입 가능여부 생년월일로 체크
 *
 * @param string birthDay 생년월일
 * @param string under14Status (1 : 관리자 승인 후 가입, 2 : 가입불가)
 * @param string under14Code 회원가입 허용상태코드
 * @return bool
 *
 */
function chkUnder14(birthDay, under14Status, under14Code)
{
	birthDay = birthDay.replace('-', '');
	var digit = /^[0-9]+$/;
	if (birthDay == '' || birthDay == '00000000' || digit.test(birthDay) === false || birthDay.length != 8) { // 생년월일 값 없음
		if (under14Status == '1') { // 관리자 승인 후 가입
			if (confirm('만14세 미만을 확인할 수 없으므로 관리자 승인 후 가입할 수 있습니다.\n계속 진행하시겠습니까?') === false) {
				return false;
			}
		}
		else if (under14Status == '2') { // 가입불가
			alert('만14세 미만을 확인할 수 없으므로 회원가입을 허용하지 않습니다. 관리자에게 문의해 주세요.');
			return false;
		}
	}
	else { // 생년월일 값 있음
		// 만14세 미만 비교
		if (calMinus1Age(birthDay) < 14) { // 미만
			if (under14Status == '1') { // 관리자 승인 후 가입
				if (confirm('만14세 미만 회원의 경우 관리자 승인 후 가입이 완료됩니다.\n계속 진행하시겠습니까?') === false) {
					return false;
				}
				else {
					return true;
				}
			}
			else if (under14Status == '2') { // 가입불가
				alert('만 14세 미만의 경우 회원가입을 허용하지 않습니다.');
				return false;
			}
		}
		else { // 이상
			return true;
		}
	}
}

/**
 * 만 나이 계산
 *
 * @param string birthDay 생년월일
 * @return int 만 나이
 *
 */
function calMinus1Age(birthDay)
{
	birthDay = birthDay.replace('-', '');

	var now_date = new Date();
	now_year = parseInt(now_date.getFullYear());
	now_month = parseInt(now_date.getMonth() + 1);
	now_day = parseInt(now_date.getDate());

	birth_year = parseInt(birthDay.substring(0, 4));
	birth_month = parseInt(birthDay.substring(4, 6));
	birth_day = parseInt(birthDay.substring(6, 8));

	if (birth_month < now_month) {
		age = now_year - birth_year;
	}
	else if (birth_month == now_month) {
		if (birth_day <= now_day)
			age= now_year - birth_year;
		else
			age= now_year - birth_year - 1;
	}
	else {
		age= now_year - birth_year - 1;
	}

	return age;
}