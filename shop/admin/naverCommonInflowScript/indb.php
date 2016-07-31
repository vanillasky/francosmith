<?php

include dirname(__FILE__).'/../lib.php';
include dirname(__FILE__).'/../../lib/naverCommonInflowScript.class.php';
include dirname(__FILE__).'/../../lib/naverCommonAuthKeyAPI.class.php';
include dirname(__FILE__).'/../../lib/json.class.php';

$naverCommonInflowScript = new NaverCommonInflowScript();
$naverCommonAuthKeyAPI = new NaverCommonAuthKeyAPI();
$json = new Services_JSON();

if(isset($_POST['mode'])===false || isset($_POST['accountId'])===false) exit('{code:"INVALID_REQUEST",message:"잘못된 요청입니다."}');

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
						exit('{code:"IS_NOT_EXISTS",message:"등록할 수 있는 공통인증키 입니다."}');
					case 'true':
						exit('{code:"IS_EXISTS",message:"입력하신 공통인증키로 이미 등록되어 있습니다."}');
					case 'err_accountId_str':
						exit('{code:"INVALID_ACCOUNT_ID",message:"공통인증키가 잘못되었습니다."}');
					default:
						exit($naverCommonAuthKeyAPI->classifyCommonError($response->response));
				}
			}
			else
			{
				exit('{code:"INVALID_REQUEST",message:"잘못된 요청입니다."}');
			}

        case 'saveConfigure':
			if($naverCommonInflowScript->isEnabled===false)
			{
                $responseData = $naverCommonAuthKeyAPI->doSave($godo['sno'], $_POST['accountId']);
                $response = $json->decode($responseData);
                switch($response->response)
                {
					case 'true':
						if(strlen($_POST['accountId'])<1) exit('{code:"EMPTY_NCAK",message:"[네이버공통인증키]가 입력되지 않았습니다."}');
						if(preg_match('/[\'"\\\]/', $_POST['accountId'])>0) exit('{code:"INVALID_NCAK",message:"[네이버공통인증키]에 사용할 수 없는 문자가 입력되었습니다."}');
						break;
					case 'false':
						exit('{code:"ACCOUNT_ID_IS_EXISTS",message:"입력하신 공통인증키가 이미 등록되어있습니다.\r\n고객님께 발급된 공통인증키가 맞는지 확인하여주시기 바랍니다."}');
					default:
						exit($naverCommonAuthKeyAPI->classifyCommonError($response->response));
                }
			}

			if($naverCommonInflowScript->doSave($_POST['accountId'], $_POST['whiteList'])) exit('{code:"SUCCESS",message:"정상적으로 저장되었습니다."}');
			else exit('{code:"CONFIG_FILE_WRITE_FAILURE",message:"설정파일 작성에 실패하였습니다."}');
}

?>