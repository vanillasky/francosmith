<?php
//           ��   ����  ����  ��  ����  ��.   
//           ��   ����        ��

define('MCASH_CANCEL_DEFINE', true);

// �޴��� ���� ��� ����ȯ�� ����
// ����Ʈ�� �׽�Ʈ ����ȯ������ �����Ǿ� ������
// �ǿ ȯ������ ��ȯ�Ҷ��� �Ǽ���ȯ������ �����ϼž� �մϴ�.

//���� ��� ����
define('RTN_ERR', -1);
define('RTN_OK', 0);

//�޴��� ��� ������� ����ȯ�� ���� 
// TEST IP  : 121.254.135.131
// REAL IP : 121.254.135.130
if (MOBILIANS_SERVICE_TYPE == '00') {
	define('SERVER_NAME', '121.254.135.131');
}
else {
	define('SERVER_NAME', '121.254.135.130');
}
define('SERVER_PORT', 7500);

//�α� ȯ�� ���� (���ð���̹Ƿ� ������ ���� ��η� �����ֽð� other  �׷� ������� �ʼ�)
define('LOG_DIR',    dirname(__FILE__).'/../../../../log/mobilians/mcash_'.date('Ymd'));       //���� �α׸� ���� ���丮 ������
define('LOG_RUN',    'YES');                                                 // �αױ�� ����� YES , �̱���� NO 

//��������
$gMrchid = '';
$gSvcid = '';
$gTradeid = '';
$gPrdtprice = '';
$gMobilid = '';

$gResultcd = '';
$gResultmsg = '';
$gszErrMsg = '';

?>
