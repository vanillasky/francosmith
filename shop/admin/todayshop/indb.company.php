<?
include "../lib.php";

$formatter = &load_class('stringFormatter', 'stringFormatter');




// ���� �ޱ�
	$mode = isset($_POST['mode']) ? $_POST['mode'] : '';
	$sno = isset($_POST['sno']) ? $_POST['sno'] : '';
	$cp_name = isset($_POST['cp_name']) ? $_POST['cp_name'] : '';
	$cp_ceo = isset($_POST['cp_ceo']) ? $_POST['cp_ceo'] : '';
	$cp_type = isset($_POST['cp_type']) ? $_POST['cp_type'] : '';
	$cp_bizno = isset($_POST['cp_bizno']) ? $_POST['cp_bizno'] : '';
	$cp_phone = isset($_POST['cp_phone']) ? $_POST['cp_phone'] : '';
	$cp_fax = isset($_POST['cp_fax']) ? $_POST['cp_fax'] : '';
	$cp_address = isset($_POST['address']) ? $_POST['address'] : '';						// �����ȣ �Է±⿡�� address �ʵ�θ� �ѱ�
	$cp_address_post = isset($_POST['zipcode']) ? implode("-",$_POST['zipcode']) : '';		// �����ȣ �Է±⿡�� zipcode �ʵ�θ� �ѱ�
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

// ������ �� �ʼ��� ó��
	if ($cp_phone = $formatter->get($cp_phone,'dial',"-") === false) {
		$cp_phone = $_POST['cp_phone'];
	}

	if ($cp_fax = $formatter->get($cp_fax,'dial',"-") === false) {
		$cp_fax = $_POST['cp_fax'];
	}



// db ó��.
switch($mode) {

	case 'register' :
	case 'modify' :



		// �ʼ��� üũ �� ���� ����
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
				$msg = '���޾�ü ������ �����߽��ϴ�.';
			else
				$msg = '���޾�ü ���� ������ �����߽��ϴ�.';

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
				$msg = '���޾�ü�� ����߽��ϴ�.';
			else
				$msg = '���޾�ü ��Ͽ� �����߽��ϴ�.';

		}

		break;





	case 'delete':
		$query = "DELETE FROM ".GD_TODAYSHOP_COMPANY." WHERE cp_sno = $sno";
		if ($db->query($query))
			$msg = '���޾�ü�� �����߽��ϴ�.';
		else
			$msg = '���޾�ü�� �����߽��ϴ�.';

		$returnUrl = './company_list.php';

		break;
}	// eof switch;


if ($msg != '') echo '<script>alert("'.$msg.'");</script>';


go($returnUrl, "parent");
?>