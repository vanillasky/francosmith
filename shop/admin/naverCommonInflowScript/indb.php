<?php

include dirname(__FILE__).'/../lib.php';
include dirname(__FILE__).'/../../lib/naverCommonInflowScript.class.php';
include dirname(__FILE__).'/../../lib/naverCommonAuthKeyAPI.class.php';
include dirname(__FILE__).'/../../lib/json.class.php';

$naverCommonInflowScript = new NaverCommonInflowScript();
$naverCommonAuthKeyAPI = new NaverCommonAuthKeyAPI();
$json = new Services_JSON();

if(isset($_POST['mode'])===false || isset($_POST['accountId'])===false) exit('{code:"INVALID_REQUEST",message:"�߸��� ��û�Դϴ�."}');

switch($_POST['mode'])
{
        case 'checkDuplicateAccountId':
			if($naverCommonInflowScript->isEnabled===false)
			{
				$responseData = $naverCommonAuthKeyAPI->isExists($godo['sno'], $_POST['accountId']);
				$response = $json->decode($responseData);
				switch($response->response)
				{
					case 'false':
						exit('{code:"IS_NOT_EXISTS",message:"����� �� �ִ� ��������Ű �Դϴ�."}');
					case 'true':
						exit('{code:"IS_EXISTS",message:"�Է��Ͻ� ��������Ű�� �̹� ��ϵǾ� �ֽ��ϴ�."}');
					case 'err_accountId_str':
						exit('{code:"INVALID_ACCOUNT_ID",message:"��������Ű�� �߸��Ǿ����ϴ�."}');
					default:
						exit($naverCommonAuthKeyAPI->classifyCommonError($response->response));
				}
			}
			else
			{
				exit('{code:"INVALID_REQUEST",message:"�߸��� ��û�Դϴ�."}');
			}

        case 'saveConfigure':
			if($naverCommonInflowScript->isEnabled===false)
			{
                $responseData = $naverCommonAuthKeyAPI->doSave($godo['sno'], $_POST['accountId']);
                $response = $json->decode($responseData);
                switch($response->response)
                {
					case 'true':
						if(strlen($_POST['accountId'])<1) exit('{code:"EMPTY_NCAK",message:"[���̹���������Ű]�� �Էµ��� �ʾҽ��ϴ�."}');
						if(preg_match('/[\'"\\\]/', $_POST['accountId'])>0) exit('{code:"INVALID_NCAK",message:"[���̹���������Ű]�� ����� �� ���� ���ڰ� �ԷµǾ����ϴ�."}');
						break;
					case 'false':
						exit('{code:"ACCOUNT_ID_IS_EXISTS",message:"�Է��Ͻ� ��������Ű�� �̹� ��ϵǾ��ֽ��ϴ�.\r\n���Բ� �߱޵� ��������Ű�� �´��� Ȯ���Ͽ��ֽñ� �ٶ��ϴ�."}');
					default:
						exit($naverCommonAuthKeyAPI->classifyCommonError($response->response));
                }
			}

			if($naverCommonInflowScript->doSave($_POST['accountId'], $_POST['whiteList'])) exit('{code:"SUCCESS",message:"���������� ����Ǿ����ϴ�."}');
			else exit('{code:"CONFIG_FILE_WRITE_FAILURE",message:"�������� �ۼ��� �����Ͽ����ϴ�."}');
}

?>