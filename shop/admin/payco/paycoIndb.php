<?php
include '../lib.php';
@include '../../conf/payco.cfg.php';
@include '../../lib/paycoApi.class.php';
@include '../../lib/payco.class.php';
@include '../../lib/paycoConfig.class.php';

if($sess['level'] < 100){
	msg("������ ���� ������ �ְ�������� �������θ� ���������մϴ�. ",-1);
}

if(!$payco) $payco = Core::loader('payco');
if(!$paycoConfig) $paycoConfig = Core::loader('paycoConfig');
if(!$payco) {
	echo 'fail|�ʼ� ������ �������� �ʽ��ϴ�. �����Ϳ� �����Ͻñ� �ٶ��ϴ�.[payco.class.php]';
	exit;
}
if(!$paycoConfig) $payco->returnAdminMsg('fail', '�ʼ� ������ �������� �ʽ��ϴ�. �����Ϳ� �����Ͻñ� �ٶ��ϴ�.[paycoConfig.class.php]');

$mode = $_POST['mode'];
unset($_POST['mode'], $_POST['x'], $_POST['y']);

switch($mode){
	case 'save':
		if(!is_array($_POST['e_category']) || !$_POST['e_category']) $_POST['e_category'] = array();
		if(!is_array($_POST['e_exceptions']) || !$_POST['e_exceptions']) $_POST['e_exceptions'] = array();

		$_POST = array_merge((array)$paycoCfg, (array)$_POST);

		//������ �������� ����
		$paycoConfig->savePaycoConfigFile($_POST);

		msg('������ ����Ǿ����ϴ�.', 'paycoPartner.php', 'parent');
	break;

	case 'saveID':
		if(!$paycoApi) $paycoApi = Core::loader('paycoApi');
		if(!$config) $config = Core::loader('config');	
		if(!$paycoApi) $payco->returnAdminMsg('fail', '�ʼ� ������ �������� �ʽ��ϴ�. �����Ϳ� �����Ͻñ� �ٶ��ϴ�.[paycoApi.class.php]');
		if(!$config) $payco->returnAdminMsg('fail', '�ʼ� ������ �������� �ʽ��ϴ�. �����Ϳ� �����Ͻñ� �ٶ��ϴ�.[config.class.php]');

		$apiStartType = false;

		//validation check
		if($_POST['useType'] != 'N'){
			$errorMsg = $payco->check_paycoPostData($apiStartType);
			if($errorMsg) $payco->returnAdminMsg('fail', $errorMsg);

			//API ���࿩�� �� crypt_key ���
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
				//crypt_key, �������ڵ�, ����ID DB���� (������� - �ǻ��X)
				$paycoConfig->saveEnvData($_POST);
			}

			//������ �������� ����
			$paycoConfig->savePaycoConfigFile($_POST);

			$payco->returnAdminMsg('success', '������ ����Ǿ����ϴ�.');
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
				$errorMsg = "����� ���������� �ʽ��ϴ�.\n����� �ٽ� �õ��Ͽ� �ּ���.";
			}
			$payco->returnAdminMsg('fail', $errorMsg);
		}
	break;
}
exit;
?>