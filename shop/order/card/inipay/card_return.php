<?php
/**
 * �̴Ͻý� PG ��� ó�� ������
 * ���� ���ϸ� INIsecurepay.php
 * �̴Ͻý� PG ���� : INIpay V5.0 - ������ (V 0.1.1 - 20120302)
 */

include "../../../lib/library.php";
include "../../../conf/config.php";
include "../../../conf/pg.inicis.php";

//--- ���� ����Ʈ ����
error_reporting(E_ALL ^ E_NOTICE);

//--- PG���� ������ üũ �� ��ȿ�� üũ
if (forge_order_check($_POST['ordno'],$_SESSION['INI_PRICE']) === false) {
	msg('�ֹ� ������ ���� ������ ���� �ʽ��ϴ�. �ٽ� ���� �ٶ��ϴ�.','../../order_fail.php?ordno='.$_POST['ordno'],'parent');
	exit();
}

// Ncash ���� ���� API
include "../../../lib/naverNcash.class.php";
$naverNcash = new naverNcash();
if($naverNcash->useyn=='Y')
{
	if($_POST['paymethod']=="VBank") $ncashResult = $naverNcash->payment_approval($_POST['ordno'], false);
	else $ncashResult = $naverNcash->payment_approval($_POST['ordno'], true);
	if($ncashResult===false)
	{
		msg('���̹� ���ϸ��� ��뿡 �����Ͽ����ϴ�.', '../../order_fail.php?ordno='.$_POST['ordno'],'parent');
		exit();
	}
}

//--- ���̺귯�� ��Ŭ���
require_once dirname(__FILE__).'/libs/INILib.php';

//--- INIpay50 Ŭ������ �ν��Ͻ� ����
$inipay	= new INIpay50;

//--- ���� ���� ����
$inipay->SetField('inipayhome', dirname(__FILE__));					// �̴����� Ȩ���͸�
$inipay->SetField('type', 'securepay');								// ���� (���� ���� �Ұ�)
$inipay->SetField('pgid', 'INIphp'.$pgid);							// ���� (���� ���� �Ұ�)
$inipay->SetField('subpgip', '203.238.3.10');						// ���� (���� ���� �Ұ�)
$inipay->SetField('admin', $_SESSION['INI_ADMIN']);					// Ű�н�����(�������̵� ���� ����)
$inipay->SetField('debug', 'true');									// �α׸��('true'�� �����ϸ� �󼼷αװ� ������.)
$inipay->SetField('uid', $uid);										// INIpay User ID (���� ���� �Ұ�)
$inipay->SetField('goodname', $goodname);							// ��ǰ��
$inipay->SetField('currency', $currency);							// ȭ�����

$inipay->SetField('mid', $_SESSION['INI_MID']);						// �������̵�
$inipay->SetField('rn', $_SESSION['INI_RN']);						// �������� �������� RN��
$inipay->SetField('price', $_SESSION['INI_PRICE']);					// ����
$inipay->SetField('tax', $_SESSION['INI_TAX']);						// �ΰ���
$inipay->SetField('taxfree', $_SESSION['INI_TAXFREE']);				// �鼼
$inipay->SetField('enctype', $_SESSION['INI_ENCTYPE']);				// ���� (���� ���� �Ұ�)

$inipay->SetField('buyername', $buyername);							// ������ ��
$inipay->SetField('buyertel', $buyertel);							// ������ ����ó(�޴��� ��ȣ �Ǵ� ������ȭ��ȣ)
$inipay->SetField('buyeremail', $buyeremail);						// ������ �̸��� �ּ�
$inipay->SetField('paymethod', $paymethod);							// ���ҹ�� (���� ���� �Ұ�)
$inipay->SetField('encrypted', $encrypted);							// ��ȣ��
$inipay->SetField('sessionkey', $sessionkey);						// ��ȣ��
$inipay->SetField('url', "http://".$_SERVER[SERVER_NAME]);			// ���� ���񽺵Ǵ� ���� SITE URL�� �����Ұ�
$inipay->SetField('cardcode', $cardcode);							// ī���ڵ� ����
$inipay->SetField('parentemail', $parentemail);						// ��ȣ�� �̸��� �ּ�(�ڵ��� , ��ȭ�����ÿ� 14�� �̸��� ���� �����ϸ�  �θ� �̸��Ϸ� ���� �����뺸 �ǹ�, �ٸ����� ���� ���ÿ� ���� ����)

$inipay->SetField('recvname', $recvname);							// ������ ��
$inipay->SetField('recvtel', $recvtel);								// ������ ����ó
$inipay->SetField('recvaddr', $recvaddr);							// ������ �ּ�
$inipay->SetField('recvpostnum', $recvpostnum);						// ������ �����ȣ
$inipay->SetField('recvmsg', $recvmsg);								// ���� �޼���

$inipay->SetField('joincard', $joincard);							// ����ī���ڵ�
$inipay->SetField('joinexpire', $joinexpire);						// ����ī����ȿ�Ⱓ
$inipay->SetField('id_customer', $id_customer);						// user_id

//--- ���� ��û
$inipay->startAction();
/****************************************************************************************************************
* ����  ���
*
*  1 ��� ���� ���ܿ� ����Ǵ� ���� ��� ������
* 	�ŷ���ȣ : $inipay->GetResult('TID')
* 	����ڵ� : $inipay->GetResult('ResultCode') ("00"�̸� ���� ����)
* 	������� : $inipay->GetResult('ResultMsg') (���Ұ���� ���� ����)
* 	���ҹ�� : $inipay->GetResult('PayMethod') (�Ŵ��� ����)
* 	�����ֹ���ȣ : $inipay->GetResult('MOID')
*	�����Ϸ�ݾ� : $inipay->GetResult('TotPrice')
*
*  2. �ſ�ī��,ISP,�ڵ���, ��ȭ ����, ���������ü, OK CASH BAG Point ���� ��� ������ (�������Ա� , ��ȭ ��ǰ�� ����)
*
* 	�̴Ͻý� ���γ�¥ : $inipay->GetResult('ApplDate') (YYYYMMDD)
* 	�̴Ͻý� ���νð� : $inipay->GetResult('ApplTime') (HHMMSS)
*
*  3. �ſ�ī�� ���� ��� ������
*
* 	�ſ�ī�� ���ι�ȣ : $inipay->GetResult('ApplNum')
* 	�ҺαⰣ : $inipay->GetResult('CARD_Quota')
* 	�������Һ� ���� : $inipay->GetResult('CARD_Interest') ("1"�̸� �������Һ�)
* 	�ſ�ī��� �ڵ� : $inipay->GetResult('CARD_Code') (�Ŵ��� ����)
* 	ī��߱޻� �ڵ� : $inipay->GetResult('CARD_BankCode') (�Ŵ��� ����)
* 	�������� ���࿩�� : $inipay->GetResult('CARD_AuthType') ("00"�̸� ����)
*   ���� �̺�Ʈ ���� ���� : $inipay->GetResult('EventCode')
*
*    ** �޷����� �� ��ȭ�ڵ��  ȯ�� ���� **
*	�ش� ��ȭ�ڵ� : $inipay->GetResult('OrgCurrency')
*	ȯ�� : $inipay->GetResult('ExchangeRate')
*
*   �Ʒ��� "�ſ�ī�� �� OK CASH BAG ���հ���" �Ǵ�"�ſ�ī�� ���ҽÿ� OK CASH BAG����"�ÿ� �߰��Ǵ� ������
* 	OK Cashbag ���� ���ι�ȣ : $inipay->GetResult('OCB_SaveApplNum')
* 	OK Cashbag ��� ���ι�ȣ : $inipay->GetResult('OCB_PayApplNum')
* 	OK Cashbag �����Ͻ� : $inipay->GetResult('OCB_ApplDate') (YYYYMMDDHHMMSS)
* 	OCB ī���ȣ : $inipay->GetResult('OCB_Num')
* 	OK Cashbag ���հ���� �ſ�ī�� ���ұݾ� : $inipay->GetResult('CARD_ApplPrice')
* 	OK Cashbag ���հ���� ����Ʈ ���ұݾ� : $inipay->GetResult('OCB_PayPrice')
*
* 4. �ǽð� ������ü ���� ��� ������
*
* 	�����ڵ� : $inipay->GetResult('ACCT_BankCode')
*	���ݿ����� �������ڵ� : $inipay->GetResult('CSHR_ResultCode')
*	���ݿ����� ���౸���ڵ� : $inipay->GetResult('CSHR_Type')
*
* 5. ������ �Ա� ���� ��� ������
* 	������� ä���� ���� �ֹι�ȣ : $inipay->GetResult('VACT_RegNum')
* 	������� ��ȣ : $inipay->GetResult('VACT_Num')
* 	�Ա��� ���� �ڵ� : $inipay->GetResult('VACT_BankCode')
* 	�Աݿ����� : $inipay->GetResult('VACT_Date') (YYYYMMDD)
* 	�۱��� �� : $inipay->GetResult('VACT_InputName')
* 	������ �� : $inipay->GetResult('VACT_Name')
*
* 6. �ڵ��� ���� ��� ������
* 	��ȭ���� ����� �ڵ� : $inipay->GetResult('HPP_GWCode') ( "���� ���� �ڼ��� ����"���� �ʿ� , ���������� �ʿ���� ������)
* 	�޴��� ��ȣ : $inipay->GetResult('HPP_Num') (�ڵ��� ������ ���� �޴�����ȣ)
*
* 7. ��� ���� ���ܿ� ���� ���� ���нÿ��� ���� ��� ������
* 	�����ڵ� : $inipay->GetResult('ResultErrorCode')
*
****************************************************************************************************************/

/*******************************************************************
* DB���� ���� �� �������                                      *
*                                                                 *
* ���� ����� DB � �����ϰų� ��Ÿ �۾��� �����ϴٰ� �����ϴ�  *
* ���, �Ʒ��� �ڵ带 �����Ͽ� �̹� ���ҵ� �ŷ��� ����ϴ� �ڵ带 *
* �ۼ��մϴ�.                                                     *
*******************************************************************/
/*
$cancelFlag	= "false";
if($cancelFlag == "true")
{
	$TID = $inipay->GetResult("TID");
	$inipay->SetField("type", "cancel"); // ����
	$inipay->SetField("tid", $TID); // ����
	$inipay->SetField("cancelmsg", "DB FAIL"); // ��һ���
	$inipay->startAction();
	if($inipay->GetResult('ResultCode') == "00")
	{
		$inipay->MakeTXErrMsg(MERCHANT_DB_ERR,"Merchant DB FAIL");
	}
}
*/

//--- ���� ���
$pgPayMethod	= array(
		'VCard'			=> '�ſ�ī��(ISP)',
		'Card'			=> '�ſ�ī��(�Ƚ�Ŭ��)',
		'DirectBank'	=> '�ǽð�������ü',
		'HPP'			=> '�ڵ���',
		'VBank'			=> '�������Ա�(�������)',
		'YPAY'			=> '��������',
);

//--- ī��� �ڵ�
$pgCards	= array(
		'01'	=> '��ȯī��',
		'03'	=> '�Ե�ī��',
		'04'	=> '����ī��',
		'06'	=> '����ī��',
		'11'	=> 'BCī��',
		'12'	=> '�Ｚī��',
		'13'	=> '(��)LGī��',
		'14'	=> '����ī��',
		'15'	=> '�ѹ�ī��',
		'16'	=> 'NHī��',
		'17'	=> '�ϳ�SKī��',
		'21'	=> '�ؿܺ���ī��',
		'22'	=> '�ؿܸ�����ī��',
		'23'	=> '�ؿ�JCBī��',
		'24'	=> '�ؿܾƸ߽�ī��',
		'25'	=> '�ؿܴ��̳ʽ�ī��',
);

//--- ���� �ڵ�
$pgBanks	= array(
		'02'	=> '�ѱ��������',
		'03'	=> '�������',
		'04'	=> '��������',
		'05'	=> '��ȯ����',
		'07'	=> '�����߾�ȸ',
		'11'	=> '�����߾�ȸ',
		'12'	=> '��������',
		'16'	=> '�����߾�ȸ',
		'20'	=> '�츮����',
		'21'	=> '��������',
		'23'	=> '��������',
		'25'	=> '�ϳ�����',
		'26'	=> '��������',
		'27'	=> '�ѱ���Ƽ����',
		'31'	=> '�뱸����',
		'32'	=> '�λ�����',
		'34'	=> '��������',
		'35'	=> '��������',
		'37'	=> '��������',
		'38'	=> '��������',
		'39'	=> '�泲����',
		'41'	=> '��ī��',
		'53'	=> '��Ƽ����',
		'54'	=> 'ȫ�����������',
		'71'	=> '��ü��',
		'81'	=> '�ϳ�����',
		'83'	=> '��ȭ����',
		'87'	=> '�ż���',
		'88'	=> '��������',
);

//--- �ֹ���ȣ
$ordno		= $_POST['ordno'];

//--- �α� ����
$settlelog	= '';
$settlelog	.= '===================================================='.chr(10);
$settlelog	.= 'PG�� : �̴Ͻý� - INIpay V5.0'.chr(10);
$settlelog	.= '�ֹ���ȣ : '.$ordno.chr(10);
$settlelog	.= '�ŷ���ȣ : '.$inipay->GetResult('TID').chr(10);
$settlelog	.= '����ڵ� : '.$inipay->GetResult('ResultCode').chr(10);
$settlelog	.= '������� : '.strip_tags($inipay->GetResult('ResultMsg')).chr(10);
$settlelog	.= '���ҹ�� : '.$inipay->GetResult('PayMethod').' - '.$pgPayMethod[$inipay->GetResult('PayMethod')].chr(10);
if ($_POST['escrow'] == "Y") {
	$settlelog	.= '����ũ�� : �ش� ������ ����ũ�� ������'.chr(10);
}
$settlelog	.= '���αݾ� : '.$inipay->GetResult('TotPrice').chr(10);
if ($inipay->GetResult('PayMethod') == "YPAY") {
	$settlelog	.= '���γ�¥ : '.$inipay->GetResult('YPAY_ApplDate').chr(10);
	$settlelog	.= '���ι�ȣ : '.$inipay->GetResult('YPAY_ApplNum').chr(10);

} else {
	$settlelog	.= '���γ�¥ : '.$inipay->GetResult('ApplDate').chr(10);
	$settlelog	.= '���νð� : '.$inipay->GetResult('ApplTime').chr(10);
	$settlelog	.= '���ι�ȣ : '.$inipay->GetResult('ApplNum').chr(10);
}
$settlelog	.= ' --------------------------------------------------'.chr(10);

//--- ���ο��� / ���� ����� ���� ó�� ����
if($inipay->GetResult('ResultCode') === "00"){

	// PG ���
	$getPgResult	= true;
	$pgResultMsg	= '�����ڵ�Ȯ�� : ����Ȯ�νð�';

	switch($inipay->GetResult('PayMethod')){

		// �ſ�ī��
		case 'Card': case 'VCard':
			$card_nm	= $pgCards[$inipay->GetResult('CARD_Code')];
			//$settlelog	.= '�ſ�ī���ȣ : '.$inipay->GetResult('CARD_Num').chr(10);
			$settlelog	.= 'ī���Һο��� : '.$inipay->GetResult('CARD_Interest').' (1�̸� �������Һ�)'.chr(10);
			$settlelog	.= 'ī���ҺαⰣ : '.$inipay->GetResult('CARD_Quota').chr(10);
			$settlelog	.= 'ī��� �ڵ� : '.$inipay->GetResult('CARD_Code').' - '.$pgCards[$inipay->GetResult('CARD_Code')].chr(10);
			$settlelog	.= 'ī�� �߱޻� : '.$inipay->GetResult('CARD_BankCode').' - '.$pgBanks[$inipay->GetResult('CARD_BankCode')].chr(10);
			$settlelog	.= 'ī�� �̺�Ʈ : '.$inipay->GetResult('EventCode').chr(10);
			if ($inipay->GetResult('OCB_Num')) {
				$settlelog	.= ' -------------- OK Cashbag ���� ���� --------------'.chr(10);
				$settlelog	.= 'OK Cashbag ���� ���ι�ȣ : '.$inipay->GetResult('OCB_SaveApplNum').chr(10);
				$settlelog	.= 'OK Cashbag ��� ���ι�ȣ : '.$inipay->GetResult('OCB_PayApplNum').chr(10);
				$settlelog	.= 'OK Cashbag �����Ͻ� : '.$inipay->GetResult('OCB_ApplDate').chr(10);
				$settlelog	.= 'OK Cashbag ī���ȣ : '.$inipay->GetResult('OCB_Num').chr(10);
				$settlelog	.= '���հ���� �ſ�ī�� ���ұݾ� : '.$inipay->GetResult('CARD_ApplPrice').chr(10);
				$settlelog	.= '���հ���� ����Ʈ ���ұݾ� : '.$inipay->GetResult('OCB_PayPrice').chr(10);
				$settlelog	.= ' --------------------------------------------------'.chr(10);
			}
		break;

		// ������ü
		case 'DirectBank':
			$CSHR_ResultCode	= $inipay->GetResult('CSHR_ResultCode');
			$settlelog	.= '�ǽð�������ü ���� �ڵ� : '.$inipay->GetResult('ACCT_BankCode').' - '.$pgBanks[$inipay->GetResult('ACCT_BankCode')].chr(10);
			$settlelog	.= '���ݿ����� �߱ް�� �ڵ� : '.$inipay->GetResult('CSHR_ResultCode').chr(10);
			$settlelog	.= '���ݿ����� �߱ޱ��� �ڵ� : '.$inipay->GetResult('CSHR_Type').chr(10);

			// ���ݿ����� ��� ����
			if (empty($CSHR_ResultCode) === false) {
				$settlelog	.= ' -------------- ���ݿ����� ���� ���� --------------'.chr(10);
				$settlelog	.= '���ݿ����� �߱޿Ϸ� : ����Ȯ�νð�('.date('Y-m-d H:i:s').')'.chr(10);
				$settlelog	.= '�ֹ���ȣ : '.$ordno.chr(10);
				$settlelog	.= '������� : ������ü ���ݿ����� �߱� �Ϸ�'.chr(10);
				$settlelog	.= '���ݿ����� �߱ް�� �ڵ� : '.$inipay->GetResult('CSHR_ResultCode').chr(10);
				$settlelog	.= '���ݿ����� �߱ޱ��� �ڵ� : '.$inipay->GetResult('CSHR_Type').chr(10);
				$settlelog	.= ' --------------------------------------------------'.chr(10);
			}
		break;

		// �������
		case 'VBank':
			$bank_nm	= $pgBanks[$inipay->GetResult('VACT_BankCode')];
			$settlelog	.= ' *** ���� ������ �Ϸ� �Ȱ��� �ƴ� ��û �Ϸ��� ***'.chr(10);
			$settlelog	.= '�Աݰ��¹�ȣ : '.$inipay->GetResult('VACT_Num').chr(10);
			$settlelog	.= '�Ա������ڵ� : '.$inipay->GetResult('VACT_BankCode').' - '.$pgBanks[$inipay->GetResult('VACT_BankCode')].chr(10);
			$settlelog	.= '�����ָ� : '.$inipay->GetResult('VACT_Name').chr(10);
			$settlelog	.= '�۱��ڸ� : '.$inipay->GetResult('VACT_InputName').chr(10);
			$settlelog	.= '�۱����� : '.$inipay->GetResult('VACT_Date').chr(10);
			$settlelog	.= '�۱ݽð� : '.$inipay->GetResult('VACT_Time').chr(10);

			$pgResultMsg	= '�����Ҵ�Ϸ� : ��ûȮ�νð�';
		break;

		// �ڵ���
		case 'HPP':
			$settlelog	.= '�޴��� ��ȣ : '.$inipay->GetResult('HPP_Num').chr(10);
		break;

		// ��������
		case 'YPAY':
			$settlelog	.= '�޴��� ��ȣ : '.$inipay->GetResult('YPAY_PhoneNum').chr(10);
			$settlelog	.= '�������� : '.$inipay->GetResult('YPAY_ApplDate').chr(10);
			$settlelog	.= '���ι�ȣ : '.$inipay->GetResult('YPAY_ApplNum').chr(10);
		break;
	}

	$settlelog	= '===================================================='.chr(10).$pgResultMsg.'('.date('Y-m-d H:i:s').')'.chr(10).$settlelog.'===================================================='.chr(10);
} else {
	// PG ���
	$getPgResult	= false;

	$settlelog	= '===================================================='.chr(10).'��������Ȯ�� : ����Ȯ�νð�('.date('Y-m-d H:i:s').')'.chr(10).$settlelog.'===================================================='.chr(10);
}

//--- ������� ������ ��� üũ �ܰ� ����
$res_cstock = true;
if($cfg['stepStock'] == '1' && $inipay->GetResult('PayMethod') == "VBank") $res_cstock = false;

//--- ��� üũ �� ��� ���� ��� ���� ���
include "../../../lib/cardCancel.class.php";
$cancel = new cardCancel();
if(!$cancel->chk_item_stock($ordno) && $res_cstock === true){
	$TID	= $inipay->GetResult("TID");
	$inipay->SetField("type", "cancel");			// ����
	$inipay->SetField("tid", $TID);					// ����
	$inipay->SetField("cancelmsg", "OUT OF STOCK");	// ��һ���
	$inipay->startAction();
	if($inipay->GetResult('ResultCode') === "00")
	{
		$inipay->MakeTXErrMsg(MERCHANT_DB_ERR,"OUT OF STOCK");

		// PG ���
		$getPgResult	= false;
		$getPgResultCd	= "cancel";

		// �α� �缳��
		$settlelog	= '****************************************************'.chr(10).'�������Ȯ�� : ������ҽð�('.date('Y-m-d H:i:s').')'.chr(10).'��һ��� : ��� �������� ���� ���'.chr(10).$settlelog.'****************************************************'.chr(10);
	}
}

//--- ���ں������� �߱�
@session_start();
if (session_is_registered('eggData') === true && $getPgResult === true){
	if ($_SESSION['eggData']['ordno'] == $ordno && $_SESSION['eggData']['resno1'] != '' && $_SESSION['eggData']['resno2'] != '' && $_SESSION['eggData']['agree'] == 'Y'){
		include '../../../lib/egg.class.usafe.php';
		$eggData = $_SESSION['eggData'];
		switch($inipay->GetResult('PayMethod')){
			case 'Card': case 'VCard':
				$eggData['payInfo1'] = $pgCards[$inipay->GetResult('CARD_Code')];		// (*) ��������(ī���)
				$eggData['payInfo2'] = $inipay->GetResult('ApplNum');					// (*) ��������(���ι�ȣ)
				break;
			case "DirectBank":
				$eggData['payInfo1'] = $pgBanks[$inipay->GetResult('ACCT_BankCode')];	// (*) ��������(�����)
				$eggData['payInfo2'] = $inipay->GetResult('TID');						// (*) ��������(���ι�ȣ or �ŷ���ȣ)
				break;
			case "VBank":
				$eggData['payInfo1'] = $pgBanks[$inipay->GetResult('VACT_BankCode')];	// (*) ��������(�����)
				$eggData['payInfo2'] = $inipay->GetResult('VACT_Num');				// (*) ��������(���¹�ȣ)
				break;
			case "YPAY":
				$eggData['payInfo1'] = $inipay->GetResult('YPAY_ApplNum');			// (*) ��������(���ι�ȣ)
				break;
		}
		$eggCls = new Egg( 'create', $eggData );
		if ( $eggCls->isErr == true && $inipay->GetResult('PayMethod') == "VBank" ){
			//$inipay->GetResult('ResultCode') = '';
		}
		else if ( $eggCls->isErr == true && in_array($inipay->GetResult('PayMethod'), array("Card","VCard","DirectBank")) );
	}
	session_unregister('eggData');
}

//--- �ߺ� ���� üũ
$oData = $db->fetch("SELECT step, vAccount FROM ".GD_ORDER." WHERE ordno='$ordno'");
if($oData['step'] > 0 || $oData['vAccount'] != '' || !strcmp($inipay->GetResult('ResultCode'),"1179")){		// �ߺ�����

	// �α� ����
	$db->query("UPDATE ".GD_ORDER." SET settlelog=concat(ifnull(settlelog,''),'$settlelog') WHERE ordno='$ordno'");
	go("../../order_end.php?ordno=$ordno&card_nm=$card_nm","parent");
	exit();

}

//--- ���� ������ ��� ó��
if( $getPgResult === true ){

	$query = "
	SELECT * from
		".GD_ORDER." a
		LEFT JOIN ".GD_LIST_BANK." b on a.bankAccount = b.sno
	WHERE
		a.ordno='$ordno'
	";
	$data = $db->fetch($query);

	// ����ũ�� ���� Ȯ��
	$escrowyn = ($_POST['escrow']=="Y") ? "y" : "n";
	$escrowno = $inipay->GetResult('TID');

	// ���� ���� ����
	$step = 1;
	$qrc1 = "cyn='y', cdt=now(),";
	$qrc2 = "cyn='y',";

	// ������� ������ �������� ����
	if ($inipay->GetResult('PayMethod')=="VBank"){
		$vAccount = $bank_nm." ".$inipay->GetResult('VACT_Num')." ".$inipay->GetResult('VACT_Name');
		$step = 0; $qrc1 = $qrc2 = "";
	}

	// ���ݿ����� ����
	if (empty($CSHR_ResultCode) === false) {
		$qrc1 .= "cashreceipt='{$inipay->GetResult('TID')}',";
	}

	// �ǵ���Ÿ ����
	$db->query("
	UPDATE ".GD_ORDER." set $qrc1
		step		= '$step',
		step2		= '',
		escrowyn	= '$escrowyn',
		escrowno	= '$escrowno',
		vAccount	= '$vAccount',
		settlelog	= concat(ifnull(settlelog,''),'$settlelog'),
		cardtno		= '".$inipay->GetResult('TID')."'
	WHERE ordno='$ordno'"
	);
	$db->query("update ".GD_ORDER_ITEM." set $qrc2 istep='$step' where ordno='$ordno'");

	// �ֹ��α� ����
	orderLog($ordno,$r_step2[$data[step2]]." > ".$r_step[$step]);

	// ��� ó��
	setStock($ordno);

	// ��ǰ���Խ� ������ ���
	if ($data[m_no] && $data[emoney]){
		setEmoney($data[m_no],-$data[emoney],"��ǰ���Խ� ������ ���� ���",$ordno);
	}

	### �ֹ�Ȯ�θ���
	if(function_exists('getMailOrderData')){
		sendMailCase($data['email'],0,getMailOrderData($ordno));
	}

	// SMS ���� ����
	$dataSms = $data;

	if ($inipay->GetResult('PayMethod')!="VBank"){
		sendMailCase($data[email],1,$data);			### �Ա�Ȯ�θ���
		sendSmsCase('incash',$data[mobileOrder]);	### �Ա�Ȯ��SMS
	} else {
		sendSmsCase('order',$data[mobileOrder]);	### �ֹ�Ȯ��SMS
	}

	go("../../order_end.php?ordno=$ordno&card_nm=$card_nm","parent");

} else {		// ī����� ����
	$db->query("update ".GD_ORDER." set step2=54, settlelog=concat(ifnull(settlelog,''),'$settlelog'),cardtno='".$inipay->GetResult('TID')."' where ordno='$ordno'");
	$db->query("update ".GD_ORDER_ITEM." set istep=54 where ordno='$ordno'");

	if($getPgResultCd == "cancel"){
		$cancel -> cancel_db_proc($ordno,$inipay->GetResult('TID'));
	}

	// Ncash ���� ���� ��� API ȣ��
	if($naverNcash->useyn=='Y') $naverNcash->payment_approval_cancel($ordno);

	go("../../order_fail.php?ordno=$ordno","parent");

}

?>