<?
include "../lib.php";

$formatter = &load_class('stringFormatter', 'stringFormatter');




// 변수 받기
	$mode = isset($_POST['mode']) ? $_POST['mode'] : '';
	$sno = isset($_POST['sno']) ? $_POST['sno'] : '';
	$cp_name = isset($_POST['cp_name']) ? $_POST['cp_name'] : '';
	$cp_ceo = isset($_POST['cp_ceo']) ? $_POST['cp_ceo'] : '';
	$cp_type = isset($_POST['cp_type']) ? $_POST['cp_type'] : '';
	$cp_bizno = isset($_POST['cp_bizno']) ? $_POST['cp_bizno'] : '';
	$cp_phone = isset($_POST['cp_phone']) ? $_POST['cp_phone'] : '';
	$cp_fax = isset($_POST['cp_fax']) ? $_POST['cp_fax'] : '';
	$cp_address = isset($_POST['address']) ? $_POST['address'] : '';						// 우편번호 입력기에서 address 필드로만 넘김
	$cp_address_post = isset($_POST['zipcode']) ? implode("-",$_POST['zipcode']) : '';		// 우편번호 입력기에서 zipcode 필드로만 넘김
	$cp_www = isset($_POST['cp_www']) ? $_POST['cp_www'] : '';
	$cp_man = isset($_POST['cp_man']) ? $_POST['cp_man'] : '';
	$cp_man_phone = isset($_POST['cp_man_phone']) ? $_POST['cp_man_phone'] : '';
	$cp_man_mobile = isset($_POST['cp_man_mobile']) ? $_POST['cp_man_mobile'] : '';
	$cp_man_email = isset($_POST['cp_man_email']) ? $_POST['cp_man_email'] : '';
	$cp_calc_rate = isset($_POST['cp_calc_rate']) ? $_POST['cp_calc_rate'] : '';
	$cp_calc_day = isset($_POST['cp_calc_day']) ? $_POST['cp_calc_day'] : '';
	$cp_calc_account_bank = isset($_POST['cp_calc_account_bank']) ? $_POST['cp_calc_account_bank'] : '';
	$cp_calc_account_no = isset($_POST['cp_calc_account_no']) ? $_POST['cp_calc_account_no'] : '';
	$cp_calc_account_owner = isset($_POST['cp_calc_account_owner']) ? $_POST['cp_calc_account_owner'] : '';

	$returnUrl = isset($_POST['returnUrl']) ? $_POST['returnUrl'] : $_SERVER['HTTP_REFERER'];

// 포맷팅 및 필수값 처리
	if ($cp_phone = $formatter->get($cp_phone,'dial',"-") === false) {
		$cp_phone = $_POST['cp_phone'];
	}

	if ($cp_fax = $formatter->get($cp_fax,'dial',"-") === false) {
		$cp_fax = $_POST['cp_fax'];
	}



// db 처리.
switch($mode) {

	case 'register' :
	case 'modify' :



		// 필수값 체크 및 쿼리 생성
		if ($sno != '' && $mode == 'modify') {

			$query = "
			UPDATE ".GD_TODAYSHOP_COMPANY." SET
				cp_name = '$cp_name',
				cp_ceo = '$cp_ceo',
				cp_type = '$cp_type',
				cp_bizno = '$cp_bizno',
				cp_phone = '$cp_phone',
				cp_fax = '$cp_fax',
				cp_address = '$cp_address',
				cp_address_post = '$cp_address_post',
				cp_www = '$cp_www',
				cp_man = '$cp_man',
				cp_man_phone = '$cp_man_phone',
				cp_man_mobile = '$cp_man_mobile',
				cp_man_email = '$cp_man_email',
				cp_calc_rate = '$cp_calc_rate',
				cp_calc_day = '$cp_calc_day',
				cp_calc_account_bank = '$cp_calc_account_bank',
				cp_calc_account_no = '$cp_calc_account_no',
				cp_calc_account_owner = '$cp_calc_account_owner'
			WHERE cp_sno = '$sno'
			";

			if ($db->query($query))
				$msg = '공급업체 정보를 수정했습니다.';
			else
				$msg = '공급업체 정보 수정에 실패했습니다.';

		}
		else {

			$query = "
			INSERT INTO ".GD_TODAYSHOP_COMPANY." SET
				cp_name = '$cp_name',
				cp_ceo = '$cp_ceo',
				cp_type = '$cp_type',
				cp_bizno = '$cp_bizno',
				cp_phone = '$cp_phone',
				cp_fax = '$cp_fax',
				cp_address = '$cp_address',
				cp_address_post = '$cp_address_post',
				cp_www = '$cp_www',
				cp_man = '$cp_man',
				cp_man_phone = '$cp_man_phone',
				cp_man_mobile = '$cp_man_mobile',
				cp_man_email = '$cp_man_email',
				cp_calc_rate = '$cp_calc_rate',
				cp_calc_day = '$cp_calc_day',
				cp_calc_account_bank = '$cp_calc_account_bank',
				cp_calc_account_no = '$cp_calc_account_no',
				cp_calc_account_owner = '$cp_calc_account_owner',
				regdt = NOW()
			";


			if ($db->query($query))
				$msg = '공급업체를 등록했습니다.';
			else
				$msg = '공급업체 등록에 실패했습니다.';

		}

		break;





	case 'delete':
		$query = "DELETE FROM ".GD_TODAYSHOP_COMPANY." WHERE cp_sno = $sno";
		if ($db->query($query))
			$msg = '공급업체를 삭제했습니다.';
		else
			$msg = '공급업체를 삭제했습니다.';

		$returnUrl = './company_list.php';

		break;
}	// eof switch;


if ($msg != '') echo '<script>alert("'.$msg.'");</script>';


go($returnUrl, "parent");
?>