<?php
include '../lib.php';
@include '../../conf/payco.cfg.php';
@include '../../lib/paycoApi.class.php';
@include '../../lib/payco.class.php';
@include '../../lib/paycoConfig.class.php';

if($sess['level'] < 100){
	msg("페이코 서비스 설정은 최고관리자의 권한으로만 수정가능합니다. ",-1);
}

if(!$payco) $payco = Core::loader('payco');
if(!$paycoConfig) $paycoConfig = Core::loader('paycoConfig');
if(!$payco) {
	echo 'fail|필수 파일이 존재하지 않습니다. 고객센터에 문의하시기 바랍니다.[payco.class.php]';
	exit;
}
if(!$paycoConfig) $payco->returnAdminMsg('fail', '필수 파일이 존재하지 않습니다. 고객센터에 문의하시기 바랍니다.[paycoConfig.class.php]');

$mode = $_POST['mode'];
unset($_POST['mode'], $_POST['x'], $_POST['y']);

switch($mode){
	case 'save':
		if(!is_array($_POST['e_category']) || !$_POST['e_category']) $_POST['e_category'] = array();
		if(!is_array($_POST['e_exceptions']) || !$_POST['e_exceptions']) $_POST['e_exceptions'] = array();

		$_POST = array_merge((array)$paycoCfg, (array)$_POST);

		//페이코 설정파일 저장
		$paycoConfig->savePaycoConfigFile($_POST);

		msg('설정이 저장되었습니다.', 'paycoPartner.php', 'parent');
	break;

	case 'saveID':
		if(!$paycoApi) $paycoApi = Core::loader('paycoApi');
		if(!$config) $config = Core::loader('config');	
		if(!$paycoApi) $payco->returnAdminMsg('fail', '필수 파일이 존재하지 않습니다. 고객센터에 문의하시기 바랍니다.[paycoApi.class.php]');
		if(!$config) $payco->returnAdminMsg('fail', '필수 파일이 존재하지 않습니다. 고객센터에 문의하시기 바랍니다.[config.class.php]');

		$apiStartType = false;

		//validation check
		if($_POST['useType'] != 'N'){
			$errorMsg = $payco->check_paycoPostData($apiStartType);
			if($errorMsg) $payco->returnAdminMsg('fail', $errorMsg);

			//API 실행여부 및 crypt_key 등록
			if($_POST['useType'] == 'CE' || $_POST['useType'] == 'E'){
				if(!$paycoCfg['crypt_key']){
					$apiStartType = true;

					//get api key
					$_POST['crypt_key'] = $payco->setAuth_secretKeyData();
				}
				else {
					$arrayCodeName = array('paycoSellerKey', 'paycoCpId', 'testYn');
					foreach($arrayCodeName as $codeName){
						if($_POST[$codeName] && $paycoCfg[$codeName] != $_POST[$codeName]){
							$apiStartType = true;
							break;
						}
					}
				}
			}
		}

		//API start
		if($apiStartType === true) {
			$responseData = $payco->apiExecute('auth');
		}

		$_POST = array_merge((array)$paycoCfg, (array)$_POST);

		//payco config save
		if($responseData['code'] == '000' || $apiStartType == false){
			if($responseData['code'] == '000'){
				//crypt_key, 가맹점코드, 상점ID DB저장 (백업개념 - 실사용X)
				$paycoConfig->saveEnvData($_POST);
			}

			//페이코 설정파일 저장
			$paycoConfig->savePaycoConfigFile($_POST);

			$payco->returnAdminMsg('success', '설정이 저장되었습니다.');
		}
		else {
			if($responseData['code'] == '112'){
				$responseData['data'] = $payco->setAdminValidateCheck($responseData['data']);
				$payco->returnAdminMsg('validateFail', $responseData['data']);
			}

			if($responseData['msg']) {
				$errorMsg = iconv("utf-8", "euc-kr", $responseData['msg']);
			}
			else {
				$errorMsg = "통신이 정상적이지 않습니다.\n잠시후 다시 시도하여 주세요.";
			}
			$payco->returnAdminMsg('fail', $errorMsg);
		}
	break;
}
exit;
?>