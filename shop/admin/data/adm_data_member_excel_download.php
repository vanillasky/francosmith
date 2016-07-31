<?php
if ($_POST['lockPassword'] === 'y' && $_POST['mode'] !== 'downloadPasswordExcel') {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko" lang="ko">
	<head>
		<script type="text/javascript">
			var originAction = parent.document.fm.action;
			var originTarget = parent.document.fm.target;
			parent.popupLayer("about:blank", 450, 240);
			parent.document.getElementById("objPopupLayer").getElementsByTagName("iframe")[0].contentWindow.name = "objPopupIframe";
			parent.document.fm.action = "./adm_data_member_excel_download_password.php";
			parent.document.fm.target = "objPopupIframe";
			parent.document.fm.submit();
			parent.document.fm.action = originAction;
			parent.document.fm.target = originTarget;
		</script>
	</head>
	<body></body>
</html>
<?php
}
else {
	include dirname(__FILE__).'/../../conf/config.php';

	require_once dirname(__FILE__).'/../lib.php';
	require_once dirname(__FILE__).'/../../lib/PHPExcel/PHPExcel.php';
	require_once dirname(__FILE__).'/../../lib/qfile.class.php';

	if ($_POST['limitmethod'] === 'part') {
		if ($_POST['limit'][0] == '' || $_POST['limit'][1] == '') {
			msg('부분다운 하실 경우에는 줄수를 꼭! 입력합니다.', $_SERVER['HTTP_REFERER'] );
		}
	}

	if (strlen(trim($_POST['filename'])) > 0) {
		$fileName = basename($_POST['filename']);
	}
	else {
		$fileName = '[' . strftime( '%y년%m월%d일' ) . '] 회원';
	}

	// 필드 속성 저장(다운필드)
	$ddlPath = dirname(__FILE__).'/../../conf/data_memberddl.ini';
	if (count($_POST['field']) > 0) {
		$qfile = new qfile();

		$addFields = array(
			'zonecode' => array(
				'text' => '새 우편번호',
				'down' => 'Y',
				'desc' => '5자리의 새 우편번호(국가기초구역번호) 입력. 형식) XXXXX'
			),
		);

		$fields = parse_ini_file($ddlPath, true);

		foreach($addFields as $k => $v) {
			if(!$fields[$k]) $fields[$k] = $v;
		}

		$qfile->open($ddlPath);

		foreach ($fields as $key => $field) {
			if (array_search($key, $_POST['field']) !== false) {
				$field['down'] = 'Y';
			}
			else {
				$field['down'] = 'N';
			}
			$qfile->write('['.$key.']'.PHP_EOL);
			$qfile->write('text = "'.$field['text'].'"'.PHP_EOL);
			$qfile->write('down = "'.$field['down'].'"'.PHP_EOL);
			$qfile->write('desc = "'.$field['desc'].'"'.PHP_EOL.PHP_EOL);
		}

		$qfile->close();
		@chmod($ddlPath, 0707);
	}

	$config->save('memberExcelDownload', array(
	    'lockPassword' => $_POST['lockPassword'],
	));

	// 쿼리 생성
	if ($_GET['sample'] !== 'Y') {
		$db_table = GD_MEMBER;
		if ($_POST['skey'] && $_POST['sword']) {
			if ($_POST['skey'] === 'resno') {
				$tmp = str_replace('-', '', $_POST['sword']);
				$where[] = '(resno1=MD5("'.substr($tmp, 0, 6 ).'") AND resno2=MD5("'.substr($tmp, 6, 7).'"))';
			}
			else if ($_POST['skey'] === 'all') {
				$where[] = '(CONCAT(m_id, name) LIKE "%'.$_POST['sword'].'%" OR nickname LIKE "%'.$_POST['sword'].'%")';
			}
			else {
				$where[] = $_POST['skey'].' LIKE "%'.$_POST['sword'].'%"';
			}
		}

		if ($_POST['sstatus'] != '') {
			$where[] = 'status="'.$_POST['sstatus'].'"';
		}

		if ($_POST['slevel'] != '') {
			$where[] = 'level="'.$_POST['slevel'].'"';
		}

		if ($_POST['ssum_sale'][0] != '' && $_POST['ssum_sale'][1] != '') {
			$where[] = 'sum_sale BETWEEN '.$_POST['ssum_sale'][0].' AND '.$_POST['ssum_sale'][1];
		}
		else if ($_POST['ssum_sale'][0] != '' && $_POST['ssum_sale'][1] == '') {
			$where[] = 'sum_sale >= '.$_POST['ssum_sale'][0];
		}
		else if ($_POST['ssum_sale'][0] == '' && $_POST['ssum_sale'][1] != '') {
			$where[] = 'sum_sale <= '.$_POST['ssum_sale'][1];
		}

		if ($_POST['semoney'][0] != '' && $_POST['semoney'][1] != '') {
			$where[] = 'emoney BETWEEN '.$_POST['semoney'][0].' AND '.$_POST['semoney'][1];
		}
		else if ($_POST['semoney'][0] != '' && $_POST['semoney'][1] == '') {
			$where[] = 'emoney >= '.$_POST['semoney'][0];
		}
		else if ($_POST['semoney'][0] == '' && $_POST['semoney'][1] != '') {
			$where[] = 'emoney <= '.$_POST['semoney'][1];
		}

		if ($_POST['sregdt'][0] && $_POST['sregdt'][1]) {
			$where[] = 'regdt BETWEEN DATE_FORMAT('.$_POST['sregdt'][0].',"%Y-%m-%d 00:00:00") AND DATE_FORMAT('.$_POST['sregdt'][1].',"%Y-%m-%d 23:59:59")';
		}

		if ($_POST['slastdt'][0] && $_POST['slastdt'][1]) {
			$where[] = 'last_login BETWEEN DATE_FORMAT('.$_POST['slastdt'][0].',"%Y-%m-%d 00:00:00") AND DATE_FORMAT('.$_POST['slastdt'][1].',"%Y-%m-%d 23:59:59")';
		}

		if ($_POST['sex']) {
			$where[] = 'sex = "'.$_POST['sex'].'"';
		}

		if ($_POST['sage'] != '') {
			$age[] = date('Y') + 1 - $_POST['sage'];
			$age[] = $age[0] - 9;
			foreach ($age as $k => $v) {
				$age[$k] = substr($v, 2, 2);
			}
			if ($_POST['sage'] == '60') {
				$where[] = 'RIGHT(birth_year, 2) <= '.$age[1];
			}
			else {
				$where[] = 'RIGHT(birth_year, 2) BETWEEN '.$age[1].' AND '.$age[0];
			}
		}

		if ($_POST['scnt_login'][0] != '' && $_POST['scnt_login'][1] != '') {
			$where[] = 'cnt_login BETWEEN '.$_POST['scnt_login'][0].' AND '.$_POST['scnt_login'][1];
		}
		else if ($_POST['scnt_login'][0] != '' && $_POST['scnt_login'][1] == '') {
			$where[] = 'cnt_login >= '.$_POST['scnt_login'][0];
		}
		else if ($_POST['scnt_login'][0] == '' && $_POST['scnt_login'][1] != '') {
			$where[] = 'cnt_login <= '.$_POST['scnt_login'][1];
		}

		if ($_POST['dormancy']) {
			$dormancyDate = date('Ymd', strtotime('-'.$_POST['dormancy'].' day'));
			$where[] = ' DATE_FORMAT(last_login, "%Y%m%d") <= "'.$dormancyDate.'"';
		}

		if ($_POST['mailing']) {
			$where[] = 'mailling = "'.$_POST['mailing'].'"';
		}
		if ($_POST['smsyn']) {
			$where[] = 'sms = "'.$_POST['smsyn'].'"';
		}

		if ($_POST['birthtype']) {
			$where[] = 'calendar = "'.$_POST['birthtype'].'"';
		}

		if ($_POST['birthdate'][0]) {
			if ($_POST['birthdate'][1]) {
				if(strlen($_POST['birthdate'][0]) > 4 && strlen($_POST['birthdate'][1]) > 4) {
					$where[] = 'CONCAT(birth_year, birth) BETWEEN "'.$_POST['birthdate'][0].' AND '.$_POST['birthdate'][1].'"';
				}
				else {
					$where[] = 'birth BETWEEN "'.$_POST['birthdate'][0].'" AND "'.$_POST['birthdate'][1].'"';
				}
			}
			else {
				$where[] = 'birth = "'.$_POST['birthdate'][0].'"';
			}
		}

		if ($_POST['marriyn']) {
			$where[] = 'marriyn = "'.$_POST['marriyn'].'"';
		}

		if ($_POST['marridate'][0]) {
			if ($_POST['marridate'][1]) {
				if (strlen($_POST['marridate'][0]) > 4 && strlen($_POST['marridate'][1]) > 4) {
					$where[] = 'marridate BETWEEN "'.$_POST['marridate'][0].'" AND "'.$_POST['marridate'][1].'"';
				}
				else {
					$where[] = 'SUBSTRING(marridate, 5, 4) BETWEEN "'.$_POST['marridate'][0].'" AND "'.$_POST['marridate'][1].'"';
				}
			}
			else {
				$where[] = 'SUBSTRING(marridate, 5, 4) = "'.$_POST['marridate'][0].'"';
			}
		}

		// 메인에서 생일자 SMS 확인용
		if ($_POST['mobileYN'] == "y") {
			$where[] = 'mobile != ""';
		}

		$where[] = 'm_id != "godomall"';
		$where[] = MEMBER_DEFAULT_WHERE;

		if ($_POST['limitmethod'] == 'part') {
			$limit = ' LIMIT '.($_POST['limit'][0] - 1).', '.($_POST['limit'][1] - $_POST['limit'][0] + 1);
		}
		else {
			$limit = '';
		}

		$query = 'SELECT * FROM '.$db_table.(count($where) ? ' WHERE '.implode(' AND ', $where) : '').' ORDER BY '.$_POST['sort'].$limit;

		$res = $db->query($query);
	}

	// 엑셀 생성
	$dataDir = realpath(dirname(__FILE__).'/../../data');
	define('PCLZIP_TEMPORARY_DIR', $dataDir);
	PHPExcel_Settings::setZipClass(PHPExcel_Settings::PCLZIP);

	$excel = new PHPExcel();
	$excel->getProperties()->setTitle($fileName)->setSubject($fileName);

	$excel->setActiveSheetIndex(0);
	$columnNumber = 0;
	$fields = parse_ini_file($ddlPath, true);
	foreach ($fields as $key => $arr) {
		if ($arr['down'] === 'Y' || $_GET['sample'] === 'Y') {
			$excel->getActiveSheet()->getCellByColumnAndRow($columnNumber, 1)->setValue(iconv('EUC-KR', 'UTF-8', $arr['text']));
			$excel->getActiveSheet()->getStyleByColumnAndRow($columnNumber, 1)->getFont()->setBold(true);
			$excel->getActiveSheet()->getCellByColumnAndRow($columnNumber, 2)->setValue(iconv('EUC-KR', 'UTF-8', $key));
			$excel->getActiveSheet()->getStyleByColumnAndRow($columnNumber, 2)->getFont()->setBold(true);
			$columnNumber++;
		}
	}

	if ($_GET['sample'] != 'Y') {
		$rowNumber = 3;
		while ($data = $db->fetch($res)) {
			$columnNumber = 0;
			foreach ($fields as $key => $arr) {
				if ($arr['down'] != 'Y') continue;

				if ($key == 'interest') {
					$tmp = array();
					foreach (codeitem('like') as $k => $v) {
						if ($data['interest'] & pow(2, $k)) $tmp[] = $k;
					}
					$data[$key] = implode('|', $tmp);
				}

				if (in_array($key, array('emoney', 'cnt_login', 'cnt_sale', 'sum_sale'))) {
					$excel->getActiveSheet()->setCellValueByColumnAndRow($columnNumber, $rowNumber, $data[$key]);
				}
				else if (in_array($key, array('zonecode'))) {
					$excel->getActiveSheet()->setCellValueExplicitByColumnAndRow($columnNumber, $rowNumber, $data[$key], PHPExcel_Cell_DataType::TYPE_STRING);
				}
				else {
					$excel->getActiveSheet()->setCellValueByColumnAndRow($columnNumber, $rowNumber, iconv('EUC-KR', 'UTF-8', $data[$key]));
				}
				$columnNumber++;
			}
			$rowNumber++;
		}
	}

	// 비밀번호 설정안함
	if ($_POST['lockPassword'] === 'n') {
		// Redirect output to a client’s web browser (Excel2007)
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$fileName.'.xls"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');

		// If you're serving to IE over SSL, then the following may be needed
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
		header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header ('Pragma: public'); // HTTP/1.0

		$excelWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
		$excelWriter->save('php://output');

		exit;
	}

	// 비밀번호 설정 다운로드
	if ($_POST['mode'] === 'downloadPasswordExcel') {
		if (!$_POST['password']) {
			msg('[비밀번호] 필수입력사항', -1);
		}
		if (!$_POST['passwordConfirm']) {
			msg('[비밀번호 확인] 필수입력사항', -1);
		}
		if ($_POST['password'] !== $_POST['passwordConfirm']) {
			msg('[비밀번호]와 [비밀번호 확인]이 같지 않습니다.', -1);
		}
		if (passwordPatternCheck($_POST['password']) === false) {
			msg('[비밀번호]의 입력형식이 잘못되었습니다.', -1);
		}

		$temporaryDirPath = $dataDir.'/'.session_id().'_member_excel';
		$excelWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');

		mkdir($temporaryDirPath);
		chmod($temporaryDirPath, 0777);
		$excelWriter->save($temporaryDirPath.'/'.$fileName.'.xls');
		exec('cd '.$temporaryDirPath.' && zip -P '.$_POST['password'].' -r "'.$fileName.'.zip" "'.$fileName.'.xls"');

		if (file_exists($temporaryDirPath.'/'.$fileName.'.zip') === false) {
			msg('다운로드 파일에 비밀번호를 설정할 수 없습니다.\r\n운영중인 서버에서 zip명령어 실행이 가능한지 확인바랍니다.');
		}

		header('Content-Disposition: attachment; filename="'.$fileName.'.zip"');
		header('Content-Transfer-Encoding: binary');
		header('Content-Length: '.filesize($temporaryDirPath.'/'.$fileName.'.zip'));
		header('Content-Type: application/zip');

		$fileHandler = fopen($temporaryDirPath.'/'.$fileName.'.zip', 'rb');
		while ($content = fread($fileHandler, 1024)) {
			echo $content;
		}
		fclose($fileHandler);

		unlink($temporaryDirPath.'/'.$fileName.'.xls');
		unlink($temporaryDirPath.'/'.$fileName.'.zip');
		rmdir($temporaryDirPath);

		exit;
	}
}