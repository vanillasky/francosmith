<?
include "../lib.php";
$qfile = Core::loader('qfile');


$mode = isset($_POST['mode']) ? $_POST['mode'] : '';


function gd_mkdir($path) {	// recursive �� ���丮 �����..

	$dirs = explode('/',$path);

	$_path = '';
	$_perm = '';

	foreach($dirs as $dir) {
		$_path .= $dir.'/';

		if (is_dir($_path)) {
			$_perm = substr(sprintf('%o', fileperms($_path)), -4);
		}
		else {
			mkdir($_path);
			if ($_perms != '') chmod($_path,$_perm);	// �θ� ���丮�� �۹̼� ������ ���󰣴�
		}
	}
}

$rs = array(
'result'=>false,
'body'=>''
);

if ($mode == 'save') {

	$date = isset($_POST['date']) ? $_POST['date'] : '';
	$name = isset($_POST['name']) ? iconv('UTF-8','EUC-KR',$_POST['name']) : '';
	$bank = isset($_POST['bank']) ? iconv('UTF-8','EUC-KR',$_POST['bank']) : '';
	$money = isset($_POST['money']) ? $_POST['money'] : '';



	$query = "
	INSERT INTO gd_ghostbanker SET
		`sno` = '',
		`date` = '$date',
		`bank` = '$bank',
		`name` = '$name',
		`money` = '$money',
		`regdt` = NOW()
	";
	if ($db->query($query)) {
		$rs['result'] = true;

	}
	else {
		$rs['result'] = false;
		$rs['body'] = '���� ����';
	}

}
elseif ($mode == 'delete') {

	$chk = isset($_POST['chk']) ? $_POST['chk'] : array();

	$instr = preg_replace('/,$/','',implode(',',$chk));

	$query = "
	DELETE FROM gd_ghostbanker
	WHERE `sno` IN ($instr)
	";

	if ($db->query($query)) {
		$rs['result'] = true;

	}
	else {
		$rs['result'] = false;
		$rs['body'] = '���� ����';
	}

}
elseif ($mode == 'load' || $mode == 'download') {

	$_skey = isset($_POST['skey']) ? $_POST['skey'] : '';
	$_sword = isset($_POST['sword']) ? iconv('UTF-8','EUC-KR',$_POST['sword']) : '';
	$_regdt = isset($_POST['regdt']) ? $_POST['regdt'] : '';



	$WHERE_STR = '';

	if ($_sword != '') {
		switch ($_skey) {
			case 'bank' :
				$WHERE_STR = " WHERE `bank` like '%$_sword%'";
				break;
			case 'name' :
				$WHERE_STR = " WHERE `name` like '%$_sword%'";
				break;
			case 'money' :
				$WHERE_STR = " WHERE `money` like '%$_sword%'";
				break;
			default :
				$WHERE_STR = " WHERE
									`bank` like '%$_sword%' OR
									`name` like '%$_sword%' OR
									`money` like '%$_sword%'
							 ";
				break;
		}
	}


	if ($_regdt[0] != '' && $_regdt[1] != '') {

		$WHERE_STR .= ($WHERE_STR == '') ? ' WHERE ': ' AND ';

		$WHERE_STR .= "
			`date` >= '$_regdt[0]' AND `date` <= '$_regdt[1]'
		";
	}


	// ����Ʈ
		$query = " SELECT * FROM gd_ghostbanker $WHERE_STR ";
		$res = $db->query($query);

		$arRow = array();
		while ($row = $db->fetch($res,1)) {
			$arRow[] = $row;
		}

	// ����¡
		list($total) = $db->fetch("SELECT count(sno) AS cnt FROM gd_ghostbanker $WHERE_STR ");

	// ���
		$rs['result'] = true;
		$rs['body'] = $arRow;
		$rs['page'] = array(
							'total'=>$total
							);

	// ���� �ٿ�ε�
		if ($mode == 'download') {

			header("Content-Type: application/vnd.ms-excel; charset=euc-kr");
			header("Content-Disposition: attachment; filename=��Ȯ���Ա��ڸ���Ʈ_".date("YmdHi").".xls");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
			header("Pragma: public");

			echo '<table border=1>';
			echo '
			<tr>
				<td>��ȣ</td>
				<td>�Ա�����</td>
				<td>����</td>
				<td>����</td>
				<td>�Աݾ�</td>
			</tr>
			';

			$i=0;
			foreach($arRow as $k => $row) {
				echo '
				<tr>
					<td>'.++$i.'</td>
					<td>'.$row['date'].'</td>
					<td>'.$row['name'].'</td>
					<td>'.$row['bank'].'</td>
					<td>'.number_format($row['money']).'</td>
				</tr>
				';
			}
			echo '</table>';
			exit;
		}

}
elseif ($mode == 'config') {

	// ȯ�� ���� ���� ��ƾ�� ajax�� ȣ������ �ʽ��ϴ�. (���� ���ε� ����)

	$_config = array();
	$_config['use'] = isset($_POST['use']) ? $_POST['use'] : '';
	$_config['expire'] = isset($_POST['expire']) ? $_POST['expire'] : '';
	$_config['hide_bank'] = isset($_POST['hide_bank']) ? $_POST['hide_bank'] : '';
	$_config['hide_money'] = isset($_POST['hide_money']) ? $_POST['hide_money'] : '';
	$_config['bankda_use'] = isset($_POST['bankda_use']) ? $_POST['bankda_use'] : '';
	$_config['bankda_limit'] = isset($_POST['bankda_limit']) ? $_POST['bankda_limit'] : '';
	$_config['banner_skin'] = isset($_POST['banner_skin']) ? $_POST['banner_skin'] : '';
	$_config['design_skin'] = isset($_POST['design_skin']) ? $_POST['design_skin'] : '';
	$_config['design_html'] = isset($_POST['design_html']) ? $_POST['design_html'] : '';
	$_config['banner_skin_type'] = isset($_POST['banner_skin_type']) ? $_POST['banner_skin_type'] : '';	// FILE
	$_config['design_skin_type'] = isset($_POST['design_skin_type']) ? $_POST['design_skin_type'] : '';	// FILE

	$file = isset($_FILES['banner_file']) ? $_FILES['banner_file'] : false;
	$delete = isset($_POST['banner_file_delete']) ? $_POST['banner_file_delete'] : false;

	// ��� �̹���
	$target_dir = SHOPROOT.'/data/ghostbanker';

		// ����
		if ($delete) {
			if (is_file($target_dir.'/'.$delete)) unlink($target_dir.'/'.$delete);
		}

		// ���ε�
		if ($file && $file['error'] == 0 && $file['size'] > 0) {

			$_ext = strtolower(array_pop(explode('.',$file['name'])));

			if (!in_array($_ext,array('jpg','jpeg','gif','png'))) {
				msg('�̹��� ����(jpg, gif, png)�� ���ϸ� ���ε� �Ͻ� �� �ֽ��ϴ�.');
				exit;
			}

			if (!is_dir($target_dir)) {
				gd_mkdir($target_dir);
			}

			// ���ε�..
			if (move_uploaded_file($file['tmp_name'], $target_dir.'/banner.'.$_ext)) {
				$_config['banner_file'] = 'banner.'.$_ext;
			}
			else {
				$_config['banner_file'] = '';
			}
		}



	// ���� ����
		$qfile->open($target_dir.'/tpl/src/custom.htm');
		$_config['design_html'] = get_magic_quotes_gpc() ? stripslashes($_config['design_html']) : $_config['design_html'];
		$qfile->write($_config['design_html']);
		$qfile->close();


	// ȯ�� ���� ����
	unset($_config['design_html']);
	$config->save('ghostbanker',$_config);

	msg('����Ǿ����ϴ�.');
	exit;

}

echo gd_json_encode($rs);
?>
