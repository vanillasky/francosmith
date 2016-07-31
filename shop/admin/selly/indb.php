<?
@include "../lib.php";
@include "../../lib/qfile.class.php";
@include "../../conf/config.selly.php";
@include "../../lib/selly.class.php";
@include "../../lib/parsexml.class.php";

$mode				= ($_REQUEST['mode'])			? trim($_REQUEST['mode'])			: "";
$delivery_type		= ($_POST['delivery_type'])		? trim($_POST['delivery_type'])		: "";
$delivery_price		= ($_POST['delivery_price'])	? trim($_POST['delivery_price'])	: 0;
$origin				= ($_POST['origin'])			? trim($_POST['origin'])			: "";

switch($mode) {
	// ���� ���� ���� �� ����
	case "set" :
		// ���� ���� ��� �˻�
		list($cust_seq) = $db->fetch("SELECT value FROM gd_env WHERE category = 'selly' AND name = 'cust_seq'");
		list($cust_cd) = $db->fetch("SELECT value FROM gd_env WHERE category = 'selly' AND name = 'cust_cd'");

		if(!$cust_seq || !$cust_cd) {
			$msg = "���� ���� ��� �Ŀ� ȯ�漳���� ������ֽñ� �ٶ��ϴ�.";
			$url = "../selly/setting.php";
			break;
		}

		// ��������
		$tmpCfgFilePath = "../../conf/config.selly.php";

		// ���� ����
		$qfile = new qfile;
		$qfile->open($tmpCfgFilePath);
		$qfile->write("<?\n");
		$qfile->write('$selly = array('."\n");
		$qfile->write("\t\"set\" => array(\n");
		$qfile->write("\t\t\"delivery_type\" => \"".$delivery_type."\",\n");
		$qfile->write("\t\t\"delivery_price\" => \"".$delivery_price."\",\n");
		$qfile->write("\t\t\"origin\" => \"".$origin."\"\n");
		$qfile->write("\t)\n");
		$qfile->write(");\n");
		$qfile->write("?>");
		$qfile->close();

		// ���� ����
		@chmod($tmpCfgFilePath, 0707);

		// msg & url
		$msg = "������ �Ϸ�Ǿ����ϴ�.";
		$url = "../selly/setting.php";
		break;

	case "idshop" :
		if(!$godo['sno']) {
			$msg = "�����ڵ尡 �������� �ʽ��ϴ�.\\n������Ʈ�� ���� ��Ź�帳�ϴ�.";
		}
		else {
			$st = new selly();		// ���� Ŭ����
			$xmlParser = new XMLParser();	// XML�ļ� Ŭ����
			if($st->idShop($godo['sno'])) {
				$msg = "���������� ������ ���ƽ��ϴ�.";
			}
			else {
				$msg = "������ �Ϸ����� ���߽��ϴ�.\\n����Ȯ�� �� �ٽ� �õ����ֽñ� �ٶ��ϴ�.";
				if($st->resCode != "000") {
					if($st->resCode == "998") {
						$msg = "(".$st->resCode.") ".$st->resMsg;
						$msg .= "\\n\\n���� ���� ���� �������� �̵��Ͻðڽ��ϱ�?";
						$confirmYn = true;
					}
					else {
						$msg .= "\\n\\n(".$st->resCode.") ".$st->resMsg;
					}
				}
			}
		}

		$url = "../selly/setting.php";
		break;

	case "category" :
		$st = new selly();				// ���� Ŭ����
		$xmlParser = new XMLParser();	// XML�ļ� Ŭ����
		$st->shop_cd = $godo['sno'];	// ���� ������
		$msg = $st->sendCategory();

		if(preg_match("/regist/", $_SERVER['HTTP_REFERER'])) { $url = "../selly/regist.php"; }
		else $url = "../selly/setting.php";
		break;

	case "sendorder" :
		$arr = array();

		$arr['order_idx'] = $_POST['order_idx'];
		$arr['send_status'] = $_POST['send_status'];

		if(is_array($_POST) && !empty($_POST)) {
			foreach($_POST as $key=>$val) {
				$arr[$key] = $val;
			}
		}
		include '../../lib/sAPI.class.php';
		$sAPI = new sAPI();

		$res = $sAPI->sendOrder($arr);
		$res_arr = array();

		if($res['code']) {
			if($res['status']) {
				$upd_arr['status'] = $res['status'];
				switch($res['status']) {
					case '0021' :
						$upd_arr['cancel_date'] = date('Y-m-d');
						break;
					case '0022' :
						$upd_arr['cancel_date'] = date('Y-m-d');
						$upd_arr['cancel_confirm_date'] = date('Y-m-d');
						break;
				}
				$db->query($db->_query_print('UPDATE '.GD_MARKET_ORDER.' SET [cv] WHERE order_idx=[i]', $upd_arr, $arr['order_idx']));
				unset($res['status']);
			}
			msg($res['msg'], $_POST['ret_url']);
		}

		$upd_query = array();
		if(is_array($res) && !empty($res)) {
			foreach($res as $row) {
				$req_date_type = array();
				switch($arr['send_status']) {
					case '0020':
						$req_date_type[] = 'check_date';
						break;
					case '0030':
						$req_date_type[] = 'delivery_date';
						$req_date_type[] = 'delivery_end_date';
						break;
					case '0022':
						$req_date_type[] = 'cancel_confirm_date';
						break;
					case '0032':
						$req_date_type[] = 'return_confirm_date';
						break;
					case '0042':
						$req_date_type[] = 'exchange_return_date';
						break;
					case '0043':
						$req_date_type[] = 'exchange_delivery_date';
						$req_date_type[] = 'exchange_confirm_date';
						break;
				}

				$upd_arr = array();
				if($row['status']) $upd_arr['status'] = $row['status'];
				$upd_arr['sync_'] = 0;

				if(is_array($req_date_type) && !empty($req_date_type)) {
					foreach($req_date_type as $val_date_type) {
						if($row[$val_date_type]) $upd_arr[$val_date_type] = $row[$val_date_type];
					}
				}

				if(!empty($upd_arr)) {
					$upd_query[] = $db->_query_print('UPDATE '.GD_MARKET_ORDER.' SET [cv] WHERE order_idx=[i]', $upd_arr, $arr['order_idx']);
				}
			}
		}

		if(!empty($upd_query) && is_array($upd_query)) {
			foreach($upd_query as $row_query) {
				$res_upd = $db->query($row_query);
			}
		}

		go($_POST['ret_url']);

		exit;
		break;

	case 'set_delivery_info' :

		$cfgByte	= trim( preg_replace( "'m'si", "", get_cfg_var( 'upload_max_filesize' ) ) ) * ( 1024 * 1024 ); # ���ε��ִ�뷮 : mb * ( kb * b )
		$fileByte	= filesize( $_FILES['file_excel'][tmp_name] ); # ���Ͽ뷮

		if ( empty( $_FILES['file_excel'][name] ) ) $altMsg = 'CSV������ �������� �����̽��ϴ�.'; // ȭ���� ������
		else if ( !preg_match("/.csv$/i", $_FILES['file_excel'][name] ) ) $altMsg = 'CSV ���ϸ� ���ε� �Ͻ� �� �ֽ��ϴ�.'; // Ȯ���� üũ
		else if ( $fileByte > $cfgByte ) $altMsg = get_cfg_var( 'upload_max_filesize' ) . '������ ���ϸ� ���ε� �Ͻ� �� �ֽ��ϴ�.'; // ���ε��ִ�뷮 �ʰ�
		else { // ȭ���� ������
			$row = 0;
			$fp = fopen( $_FILES['file_excel'][tmp_name], 'r' );

			while ( $data = fgetcsv( $fp, 135000, ',' ) ){
				$order_no = trim($data[0]);
				$upd_arr = array();
				if($order_no) {

					$send_chk_query = $db->_query_print('SELECT order_idx, morder_no, send_yn, exchange_send_yn FROM '.GD_MARKET_ORDER.' WHERE order_no=[s]', $order_no);
					$res_send_chk = $db->_select($send_chk_query);
					$send_chk = $res_send_chk[0];

					if($send_chk['morder_no']) {
						if(trim($data[6])) $upd_arr['delivery_cd'] = trim($data[6]);
						if(trim($data[7])) $upd_arr['delivery_no'] = str_replace('-','',trim($data[7]));
						if(trim($data[8])) $upd_arr['exchange_delivery_cd'] = trim($data[8]);
						if(trim($data[9])) $upd_arr['exchange_delivery_no'] = str_replace('-','',trim($data[9]));

						if(($upd_arr['delivery_cd'] && $upd_arr['delivery_no']) && !$send_chk['send_yn']) $upd_arr['send_yn'] = 'N';
						if(($upd_arr['exchange_delivery_cd'] && $upd_arr['exchange_delivery_no']) && !$send_chk['exchange_send_yn']) $upd_arr['send_yn'] = 'N';

						$upd_query = $db->_query_print('UPDATE '.GD_MARKET_ORDER.' SET [cv] WHERE order_no=[s]', $upd_arr, $order_no);

						$upd_res = $db->query($upd_query);

						if($upd_res) {
							## �����ȣ SELLY �� ����
							@include '../../lib/sAPI.class.php';
							$sAPI = new sAPI();

							$arr_delivery = array();
							$arr_delivery['order_idx'] = $send_chk['order_idx'];
							$arr_delivery['delivery_cd'] = $upd_arr['delivery_cd'];
							$arr_delivery['delivery_no'] = $upd_arr['delivery_no'];

							$ret_delivery = $sAPI->setDeliveryInfo($arr_delivery);
							unset($sAPI, $arr_delivery);

						}
					}
				}

			}

			go($_SERVER['HTTP_REFERER']);
			exit;
		}

		if($altMsg) msg($altMsg, $_SERVER['HTTP_REFERER']);
		exit;
		break;

	case 'setdeliveryinfo' :

		if($_POST['delivery_cd'] && $_POST['delivery_no']) {	//���� ������ ���� ���
			### SELLY ���� �ֹ� üũ START
			$mord_chk_query = $db->_query_print('SELECT morder_no, order_idx, send_yn FROM '.GD_MARKET_ORDER.' WHERE order_idx=[i]', $_POST['order_idx']);
			$res_mord_chk = $db->_select($mord_chk_query);
			$res_mord_chk = $res_mord_chk[0];
			if($res_mord_chk['morder_no']) {
				## �����Է� ���·� ����
				if(!$res_mord_chk['send_yn'] || $res_mord_chk['send_yn'] == 'N') {
					$upd_mord_arr = array();
					$upd_mord_arr['delivery_cd'] = $_POST['delivery_cd'];
					$upd_mord_arr['delivery_no'] = $_POST['delivery_no'];
					$upd_mord_query = $db->_query_print('UPDATE '.GD_MARKET_ORDER.' SET [cv], send_yn=[s] WHERE order_idx=[i]', $upd_mord_arr, 'N', $_POST['order_idx']);

					$db->query($upd_mord_query);

					unset($upd_mord_arr, $upd_mord_query);
				}

				## �����ȣ SELLY �� ����
				@include '../../lib/sAPI.class.php';
				$sAPI = new sAPI();

				$arr_delivery = array();
				$arr_delivery['order_idx'] = $res_mord_chk['order_idx'];
				$arr_delivery['delivery_no'] = $_POST['delivery_no'];
				$arr_delivery['delivery_cd'] = $_POST['delivery_cd'];

				$ret_delivery = $sAPI->setDeliveryInfo($arr_delivery);


				unset($sAPI, $arr_delivery);


			}
			unset($mord_chk_query, $res_mord_chk);
			### SELLY ���� �ֹ� üũ END


			if($ret_delivery['code'] == '000') {
				msg('���������� �Է� �Ͽ����ϴ�.\n���� �ֹ����� ó������ ����� ó���� �Ͻø� ���� ������ ���Ͽ� ���� �˴ϴ�.', $_SERVER['HTTP_REFERER']);
			}
			else {
				msg($ret_delivery['msg'], $_SERVER['HTTP_REFERER']);
			}

		}
		else {
			msg('���������� ��Ȯ�� �Է��� �ֽñ� �ٶ��ϴ�.', $_SERVER['HTTP_REFERER']);
		}

		break;
	case 'setexchangedeliveryinfo':

		if($_POST['exchange_delivery_no'] && $_POST['exchange_delivery_cd']) {	//���� ������ ���� ���
			### SELLY ���� �ֹ� üũ START
			$mord_chk_query = $db->_query_print('SELECT morder_no, order_idx, exchange_send_yn FROM '.GD_MARKET_ORDER.' WHERE order_idx=[i]', $_POST['order_idx']);
			$res_mord_chk = $db->_select($mord_chk_query);
			$res_mord_chk = $res_mord_chk[0];
			if($res_mord_chk['morder_no']) {
				## �����Է� ���·� ����
				if(!$res_mord_chk['exchange_send_yn'] || $res_mord_chk['exchange_send_yn'] == 'N') {
					$upd_mord_arr = array();
					$upd_mord_arr['exchange_delivery_no'] = $_POST['exchange_delivery_no'];
					$upd_mord_arr['exchange_delivery_cd'] = $_POST['exchange_delivery_cd'];

					$upd_mord_query = $db->_query_print('UPDATE '.GD_MARKET_ORDER.' SET [cv], exchange_send_yn=[s] WHERE order_idx=[i]', $upd_mord_arr, 'N', $_POST['order_idx']);

					$db->query($upd_mord_query);

					unset($upd_mord_arr, $upd_mord_query);
				}

				## �����ȣ SELLY �� ����
				@include '../../lib/sAPI.class.php';
				$sAPI = new sAPI();

				$arr_delivery = array();
				$arr_delivery['order_idx'] = $res_mord_chk['order_idx'];
				$arr_delivery['exchange_delivery_no'] = $_POST['exchange_delivery_no'];
				$arr_delivery['exchange_delivery_cd'] = $_POST['exchange_delivery_cd'];

				$ret_delivery = $sAPI->setExchangeDeliveryInfo($arr_delivery);

				unset($sAPI, $arr_delivery);
			}
			unset($mord_chk_query, $res_mord_chk);
			### SELLY ���� �ֹ� üũ END

			if($ret_delivery['code'] == '000') {
				msg('��ȯ���������� �Է� �Ͽ����ϴ�\n���� �ֹ����� ó������ ��ȯ ����� ó���� �Ͻø� ��ȯ ���� ������ ���Ͽ� ���� �˴ϴ�.', $_SERVER['HTTP_REFERER']);
			}
			else {
				msg($ret_delivery['msg'], $_SERVER['HTTP_REFERER']);
			}
			break;
		}
		else {
			msg('��ȯ ���������� ��Ȯ�� �Է��� �ֽñ� �ٶ��ϴ�.', $_SERVER['HTTP_REFERER']);
		}
		break;
	case 'basic_delivery' :
		$delivery_data = $_POST;
		unset($delivery_data['mode']);
		if($delivery_data) {

			$check_msg = Array(
				'fixe_delivery' => '������ۺ�',
				'cnt_delivery' => '��������ۺ�',
				'payment_delivery' => '���ҹ�ۺ�',
				'basic_advence_delivery' => '�⺻�����å(����)',
				'basic_payment_delivery' => '�⺻�����å(����)',
				'basic_payment_delivery_price' => '�⺻�����å(���� ��ۺ�)',
			);

			foreach($delivery_data as $key => $val) {
				$selected[$key][$val] = 'selected';

				$query = $db->_query_print('SELECT * FROM gd_env WHERE category=[s] AND name=[s]', 'selly', $key);
				$res = $db->_select($query);

				if($res) {//update
					$up_data = Array('value' => $val);
					$up_query = $db->_query_print('UPDATE gd_env SET [cv] WHERE category=[s] AND name=[s]', $up_data, 'selly', $key);
					$res = $db->query($up_query);
				}
				else {//insert
					$ins_data = Array(
						'category' => 'selly',
						'name' => $key,
						'value' => $val
					);
					$ins_query = $db->_query_print('INSERT gd_env SET [cv]', $ins_data);
					$res = $db->query($ins_query);
				}
				if(!$res) msg($check_msg[$key].' ��Ͽ� �����߽��ϴ�.', $_SERVER['HTTP_REFERER']);
			}

			msg('����Ǿ����ϴ�.', $_SERVER['HTTP_REFERER']);
		}
		else {
			msg('������ �����Ͱ� �����ϴ�.', $_SERVER['HTTP_REFERER']);
		}
		break;
	case 'selly_domain' :
		$domain_query = $db->_query_print('SELECT value FROM gd_env WHERE category=[s] AND name=[s]', 'selly', 'domain');
		$res_domain = $db->_select($domain_query);
		$domain = $res_domain[0]['value'];

		echo $domain;
		exit;
		break;
}

if($confirmYn) {
?>
<script language="JavaScript">
if(confirm("<?=$msg?>")) {
	window.open("http://selly.godo.co.kr/");
}

location.href = "<?=$url?>";
</script>
<?
	exit();
}
else {
	if($msg) msg($msg);
	if($url) go($url);
}
?>