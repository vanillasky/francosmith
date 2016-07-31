<?php
/********************************************************************************
*
* ���ϸ� : AGS_pay_ing.php
* ������������ : 2009/04/10
*
* �ô�����Ʈ �÷����ο��� ���ϵ� ����Ÿ�� �޾Ƽ� ���ϰ�����û�� �մϴ�.
*
* Copyright AEGIS ENTERPRISE.Co.,Ltd. All rights reserved.
*
*
*  �� ���ǻ��� ��
*  1.  "|"(������) ���� ����ó�� �� �����ڷ� ����ϴ� �����̹Ƿ� ���� �����Ϳ� "|"�� �������
*   ������ ���������� ó������ �ʽ��ϴ�.(���� ������ ���� ���� ���� ����)
********************************************************************************/

	include "../../../lib/library.php";
	include "../../../conf/config.php";
	include "../../../conf/pg.agspay.php";

	// PG���� ������ üũ �� ��ȿ�� üũ
	if (forge_order_check($_POST['OrdNo'],$_POST['Amt']) === false) {
		msg('�ֹ� ������ ���� ������ ���� �ʽ��ϴ�. �ٽ� ���� �ٶ��ϴ�.','../../order_fail.php?ordno='.$_POST['OrdNo'],'parent');
		exit();
	}

	// Ncash ���� ���� API
	include "../../../lib/naverNcash.class.php";
	$naverNcash = new naverNcash();
	if($naverNcash->useyn=='Y')
	{
		if(trim($_POST["AuthTy"])=='virtual') $ncashResult = $naverNcash->payment_approval($_POST['OrdNo'], false);
		else $ncashResult = $naverNcash->payment_approval($_POST['OrdNo'], true);
		if($ncashResult===false)
		{
			msg('���̹� ���ϸ��� ��뿡 �����Ͽ����ϴ�.', '../../order_fail.php?ordno='.$_POST['OrdNo'],'parent');
			exit();
		}
	}

	/****************************************************************************
	*
	* [1] ���̺귯��(AGSLib.php)�� ��Ŭ��� �մϴ�.
	*
	****************************************************************************/
	require ("./lib/AGSLib.php");


	/****************************************************************************
	*
	* [2]. agspay4.0 Ŭ������ �ν��Ͻ��� �����մϴ�.
	*
	****************************************************************************/
	$agspay = new agspay40;



	/****************************************************************************
	*
	* [3] AGS_pay.html �� ���� �Ѱܹ��� ����Ÿ
	*
	****************************************************************************/

	/*������*/
	//$agspay->SetValue("AgsPayHome","C:/htdocs/agspay");			//�ô�����Ʈ ������ġ ���丮 (������ �°� ����)
	$agspay->SetValue("AgsPayHome",$_SERVER['DOCUMENT_ROOT'].$cfg['rootDir'].'/log/agspay');			//�ô�����Ʈ ������ġ ���丮 (������ �°� ����)
	$agspay->SetValue("StoreId",trim($_POST["StoreId"]));		//�������̵�
	$agspay->SetValue("log","true");							//true : �αױ��, false : �αױ�Ͼ���.
	$agspay->SetValue("logLevel","INFO");						//�α׷��� : DEBUG, INFO, WARN, ERROR, FATAL (�ش� �����̻��� �α׸� ��ϵ�)
	$agspay->SetValue("UseNetCancel","true");					//true : ����� ���. false: ����� �̻��
	$agspay->SetValue("Type", "Pay");							//������(�����Ұ�)
	$agspay->SetValue("RecvLen", 7);							//���� ������(����) üũ ������ 6 �Ǵ� 7 ����.

	$agspay->SetValue("AuthTy",trim($_POST["AuthTy"]));			//��������
	$agspay->SetValue("SubTy",trim($_POST["SubTy"]));			//�����������
	$agspay->SetValue("OrdNo",trim($_POST["OrdNo"]));			//�ֹ���ȣ
	$agspay->SetValue("Amt",trim($_POST["Amt"]));				//�ݾ�
	$agspay->SetValue("UserEmail",trim($_POST["UserEmail"]));	//�ֹ����̸���
	$agspay->SetValue("ProdNm",trim($_POST["ProdNm"]));			//��ǰ��

	/*�ſ�ī��&������»��*/
	$agspay->SetValue("MallUrl",trim($_POST["MallUrl"]));		//MallUrl(�������Ա�) - ���� ������ ��������߰�
	$agspay->SetValue("UserId",trim($_POST["UserId"]));			//ȸ�����̵�


	/*�ſ�ī����*/
	$agspay->SetValue("OrdNm",trim($_POST["OrdNm"]));			//�ֹ��ڸ�
	$agspay->SetValue("OrdPhone",trim($_POST["OrdPhone"]));		//�ֹ��ڿ���ó
	$agspay->SetValue("OrdAddr",trim($_POST["OrdAddr"]));		//�ֹ����ּ� ��������߰�
	$agspay->SetValue("RcpNm",trim($_POST["RcpNm"]));			//�����ڸ�
	$agspay->SetValue("RcpPhone",trim($_POST["RcpPhone"]));		//�����ڿ���ó
	$agspay->SetValue("DlvAddr",trim($_POST["DlvAddr"]));		//������ּ�
	$agspay->SetValue("Remark",trim($_POST["Remark"]));			//���
	$agspay->SetValue("DeviId",trim($_POST["DeviId"]));			//�ܸ�����̵�
	$agspay->SetValue("AuthYn",trim($_POST["AuthYn"]));			//��������
	$agspay->SetValue("Instmt",trim($_POST["Instmt"]));			//�Һΰ�����
	$agspay->SetValue("UserIp",$_SERVER["REMOTE_ADDR"]);		//ȸ�� IP

	/*�ſ�ī��(ISP)*/
	$agspay->SetValue("partial_mm",trim($_POST["partial_mm"]));		//�Ϲ��ҺαⰣ
	$agspay->SetValue("noIntMonth",trim($_POST["noIntMonth"]));		//�������ҺαⰣ
	$agspay->SetValue("KVP_CURRENCY",trim($_POST["KVP_CURRENCY"]));	//KVP_��ȭ�ڵ�
	$agspay->SetValue("KVP_CARDCODE",trim($_POST["KVP_CARDCODE"]));	//KVP_ī����ڵ�
	$agspay->SetValue("KVP_SESSIONKEY",$_POST["KVP_SESSIONKEY"]);	//KVP_SESSIONKEY
	$agspay->SetValue("KVP_ENCDATA",$_POST["KVP_ENCDATA"]);			//KVP_ENCDATA
	$agspay->SetValue("KVP_CONAME",trim($_POST["KVP_CONAME"]));		//KVP_ī���
	$agspay->SetValue("KVP_NOINT",trim($_POST["KVP_NOINT"]));		//KVP_������=1 �Ϲ�=0
	$agspay->SetValue("KVP_QUOTA",trim($_POST["KVP_QUOTA"]));		//KVP_�Һΰ���

	/*�ſ�ī��(�Ƚ�)*/
	$agspay->SetValue("CardNo",trim($_POST["CardNo"]));			//ī���ȣ
	$agspay->SetValue("MPI_CAVV",$_POST["MPI_CAVV"]);			//MPI_CAVV
	$agspay->SetValue("MPI_ECI",$_POST["MPI_ECI"]);				//MPI_ECI
	$agspay->SetValue("MPI_MD64",$_POST["MPI_MD64"]);			//MPI_MD64

	/*�ſ�ī��(�Ϲ�)*/
	$agspay->SetValue("ExpMon",trim($_POST["ExpMon"]));				//��ȿ�Ⱓ(��)
	$agspay->SetValue("ExpYear",trim($_POST["ExpYear"]));			//��ȿ�Ⱓ(��)
	$agspay->SetValue("Passwd",trim($_POST["Passwd"]));				//��й�ȣ
	$agspay->SetValue("SocId",trim($_POST["SocId"]));				//�ֹε�Ϲ�ȣ/����ڵ�Ϲ�ȣ

	/*������ü���*/
	$agspay->SetValue("ICHE_OUTBANKNAME",trim($_POST["ICHE_OUTBANKNAME"]));		//��ü�����
	$agspay->SetValue("ICHE_OUTACCTNO",trim($_POST["ICHE_OUTACCTNO"]));			//��ü���¹�ȣ
	$agspay->SetValue("ICHE_OUTBANKMASTER",trim($_POST["ICHE_OUTBANKMASTER"]));	//��ü���¼�����
	$agspay->SetValue("ICHE_AMOUNT",trim($_POST["ICHE_AMOUNT"]));				//��ü�ݾ�

	/*�ڵ������*/
	$agspay->SetValue("HP_SERVERINFO",trim($_POST["HP_SERVERINFO"]));	//SERVER_INFO(�ڵ�������)
	$agspay->SetValue("HP_HANDPHONE",trim($_POST["HP_HANDPHONE"]));		//HANDPHONE(�ڵ�������)
	$agspay->SetValue("HP_COMPANY",trim($_POST["HP_COMPANY"]));			//COMPANY(�ڵ�������)
	$agspay->SetValue("HP_ID",trim($_POST["HP_ID"]));					//HP_ID(�ڵ�������)
	$agspay->SetValue("HP_SUBID",trim($_POST["HP_SUBID"]));				//HP_SUBID(�ڵ�������)
	$agspay->SetValue("HP_UNITType",trim($_POST["HP_UNITType"]));		//HP_UNITType(�ڵ�������)
	$agspay->SetValue("HP_IDEN",trim($_POST["HP_IDEN"]));				//HP_IDEN(�ڵ�������)
	$agspay->SetValue("HP_IPADDR",trim($_POST["HP_IPADDR"]));			//HP_IPADDR(�ڵ�������)

	/*ARS���*/
	$agspay->SetValue("ARS_NAME",trim($_POST["ARS_NAME"]));				//ARS_NAME(ARS����)
	$agspay->SetValue("ARS_PHONE",trim($_POST["ARS_PHONE"]));			//ARS_PHONE(ARS����)

	/*������»��*/
	$agspay->SetValue("VIRTUAL_CENTERCD",trim($_POST["VIRTUAL_CENTERCD"]));	//�����ڵ�(�������)
	$agspay->SetValue("VIRTUAL_DEPODT",trim($_POST["VIRTUAL_DEPODT"]));		//�Աݿ�����(�������)
	$agspay->SetValue("ZuminCode",trim($_POST["ZuminCode"]));				//�ֹι�ȣ(�������)
	$agspay->SetValue("MallPage",trim($_POST["MallPage"]));					//���� ��/��� �뺸 ������(�������)
	$agspay->SetValue("VIRTUAL_NO",trim($_POST["VIRTUAL_NO"]));				//������¹�ȣ(�������)

	/*����ũ�λ��*/
	$agspay->SetValue("ES_SENDNO",trim($_POST["ES_SENDNO"]));				//����ũ��������ȣ

	/*������ü(����) ���� ��� ����*/
	$agspay->SetValue("ICHE_SOCKETYN",trim($_POST["ICHE_SOCKETYN"]));			//������ü(����) ��� ����
	$agspay->SetValue("ICHE_POSMTID",trim($_POST["ICHE_POSMTID"]));				//������ü(����) �̿����ֹ���ȣ
	$agspay->SetValue("ICHE_FNBCMTID",trim($_POST["ICHE_FNBCMTID"]));			//������ü(����) FNBC�ŷ���ȣ
	$agspay->SetValue("ICHE_APTRTS",trim($_POST["ICHE_APTRTS"]));				//������ü(����) ��ü �ð�
	$agspay->SetValue("ICHE_REMARK1",trim($_POST["ICHE_REMARK1"]));				//������ü(����) ��Ÿ����1
	$agspay->SetValue("ICHE_REMARK2",trim($_POST["ICHE_REMARK2"]));				//������ü(����) ��Ÿ����2
	$agspay->SetValue("ICHE_ECWYN",trim($_POST["ICHE_ECWYN"]));					//������ü(����) ����ũ�ο���
	$agspay->SetValue("ICHE_ECWID",trim($_POST["ICHE_ECWID"]));					//������ü(����) ����ũ��ID
	$agspay->SetValue("ICHE_ECWAMT1",trim($_POST["ICHE_ECWAMT1"]));				//������ü(����) ����ũ�ΰ����ݾ�1
	$agspay->SetValue("ICHE_ECWAMT2",trim($_POST["ICHE_ECWAMT2"]));				//������ü(����) ����ũ�ΰ����ݾ�2
	$agspay->SetValue("ICHE_CASHYN",trim($_POST["ICHE_CASHYN"]));				//������ü(����) ���ݿ��������࿩��
	$agspay->SetValue("ICHE_CASHGUBUN_CD",trim($_POST["ICHE_CASHGUBUN_CD"]));	//������ü(����) ���ݿ���������
	$agspay->SetValue("ICHE_CASHID_NO",trim($_POST["ICHE_CASHID_NO"]));			//������ü(����) ���ݿ������ź�Ȯ�ι�ȣ

	/*������ü-�ڷ���ŷ(����) ���� ��� ����*/
	$agspay->SetValue("ICHEARS_SOCKETYN", trim($_POST["ICHEARS_SOCKETYN"]));	//�ڷ���ŷ������ü(����) ��� ����
	$agspay->SetValue("ICHEARS_ADMNO", trim($_POST["ICHEARS_ADMNO"]));			//�ڷ���ŷ������ü ���ι�ȣ
	$agspay->SetValue("ICHEARS_POSMTID", trim($_POST["ICHEARS_POSMTID"]));		//�ڷ���ŷ������ü �̿����ֹ���ȣ
	$agspay->SetValue("ICHEARS_CENTERCD", trim($_POST["ICHEARS_CENTERCD"]));	//�ڷ���ŷ������ü �����ڵ�
	$agspay->SetValue("ICHEARS_HPNO", trim($_POST["ICHEARS_HPNO"]));			//�ڷ���ŷ������ü �޴�����ȣ

	/****************************************************************************
	*
	* [4] �ô�����Ʈ ���������� ������ ��û�մϴ�.
	*
	****************************************************************************/
	$agspay->startPay();


	/****************************************************************************
	*
	* [5] ��������� ���� ����DB ���� �� ��Ÿ �ʿ��� ó���۾��� �����ϴ� �κ��Դϴ�.
	*
	*	�Ʒ��� ��������� ���Ͽ� �� �������ܺ� ����������� ����Ͻ� �� �ֽ��ϴ�.
	*
	*	-- ������ --
	*	��üID : $agspay->GetResult("rStoreId")
	*	�ֹ���ȣ : $agspay->GetResult("rOrdNo")
	*	��ǰ�� : $agspay->GetResult("rProdNm")
	*	�ŷ��ݾ� : $agspay->GetResult("rAmt")
	*	�������� : $agspay->GetResult("rSuccYn") (����:y ����:n)
	*	����޽��� : $agspay->GetResult("rResMsg")
	*
	*	1. �ſ�ī��
	*
	*	�����ڵ� : $agspay->GetResult("rBusiCd")
	*	�ŷ���ȣ : $agspay->GetResult("rDealNo")
	*	���ι�ȣ : $agspay->GetResult("rApprNo")
	*	�Һΰ��� : $agspay->GetResult("rInstmt")
	*	���νð� : $agspay->GetResult("rApprTm")
	*	ī����ڵ� : $agspay->GetResult("rCardCd")
	*
	*	2.������ü(���ͳݹ�ŷ/�ڷ���ŷ)
	*	����ũ���ֹ���ȣ : $agspay->GetResult("ES_SENDNO") (����ũ�� ������)
	*
	*	3.�������
	*	��������� ���������� ������¹߱��� �������� �ǹ��ϸ� �Աݴ����·� ���� ���� �Ա��� �Ϸ��� ���� �ƴմϴ�.
	*	���� ������� �����Ϸ�� �����Ϸ�� ó���Ͽ� ��ǰ�� ����Ͻø� �ȵ˴ϴ�.
	*	������ ���� �߱޹��� ���·� �Ա��� �Ϸ�Ǹ� MallPage(���� �Ա��뺸 ������(�������))�� �Աݰ���� ���۵Ǹ�
	*	�̶� ��μ� ������ �Ϸ�ǰ� �ǹǷ� �����Ϸῡ ���� ó��(��ۿ�û ��)��  MallPage�� �۾����ּž� �մϴ�.
	*	�������� : $agspay->GetResult("rAuthTy") (������� �Ϲ� : vir_n ��Ŭ�� : vir_u ����ũ�� : vir_s)
	*	�������� : $agspay->GetResult("rApprTm")
	*	������¹�ȣ : $agspay->GetResult("rVirNo")
	*
	*	4.�ڵ�������
	*	�ڵ��������� : $agspay->GetResult("rHP_DATE")
	*	�ڵ������� TID : $agspay->GetResult("rHP_TID")
	*
	*	5.ARS����
	*	ARS������ : $agspay->GetResult("rHP_DATE")
	*	ARS���� TID : $agspay->GetResult("rHP_TID")
	*
	****************************************************************************/


	$banks = array(
		'39' => '�泲����',
		'34' => '��������',
		'04' => '��������',
		'11' => '�����߾�ȸ',
		'31' => '�뱸����',
		'32' => '�λ�����',
		'02' => '�������',
		'45' => '�������ݰ�',
		'07' => '�����߾�ȸ',
		'48' => '�ſ���������',
		'26' => '(��)��������',
		'05' => '��ȯ����',
		'20' => '�츮����',
		'71' => '��ü��',
		'37' => '��������',
		'23' => '��������',
		'35' => '��������',
		'21' => '(��)��������',
		'03' => '�߼ұ������',
		'81' => '�ϳ�����',
		'88' => '��������',
		'27' => '�ѹ�����',
	);

	$cards = array(
		'0100' => '��',
		'0310' => '�ϳ�����',
		'0200' => 'KB',
		'0201' => '����visa',
		'0206' => '��Ƽvisa',
		'0205' => '�츮visa',
		'0304' => '����visa',
		'0300' => '��ȯ',
		'0309' => '���ú���',
		'1000' => '�ؿ�visa',
		'0500' => '����',
		'1100' => '�ؿ�master',
		'0700' => '�ؿ�JCB',
		'0303' => '����visa',
		'0302' => '����visa',
		'0301' => '����visa',
		'0207' => '�ż����ѹ�',
		'0203' => '�ѹ�visa',
		'0202' => '����visa',
		'0400' => '�Ｚ',
		'0800' => '����',
		'0801' => '�ؿ�Diners',
		'0900' => '�Ե�',
		'0901' => '�ؿ�AMEX',
	);


	### �ֹ���ȣ
	$ordno = $agspay->GetResult('OrdNo');

	### �α�
	$tmp_log = array();
	if ($agspay->GetResult('AuthTy') == 'card') {
		if ($agspay->GetResult('SubTy') == 'isp') {
			$tmp = '�ſ�ī�����-��������(ISP)';
		} else if($agspay->GetResult('SubTy') == 'visa3d') {
			$tmp = '�ſ�ī�����-�Ƚ�Ŭ��';
		} else if($agspay->GetResult('SubTy') == 'normal') {
			$tmp = '�ſ�ī�����-�Ϲݰ���';
		}
	} else if($agspay->GetResult('AuthTy') == 'iche') {
		$tmp = '������ü';
	} else if($agspay->GetResult('AuthTy') == 'hp') {
		$tmp = '�ڵ�������';
	} else if($agspay->GetResult('AuthTy') == 'ars') {
		$tmp = 'ARS����';
	} else if($agspay->GetResult('AuthTy') == 'virtual') {
		$tmp = '������°���';
	}

	$tmp_log[] = '�������� : '.$tmp;
	$tmp_log[] = '�������̵� : '.$agspay->GetResult('StoreId');
	$tmp_log[] = '�ֹ���ȣ : '.$agspay->GetResult('OrdNo');
	$tmp_log[] = '�ֹ��ڸ� : '.$agspay->GetResult('OrdNm');
	$tmp_log[] = '��ǰ�� : '.$agspay->GetResult('ProdNm');
	$tmp_log[] = '�����ݾ� : '.$agspay->GetResult('rAmt');
	$tmp_log[] = '�������� : '.$agspay->GetResult('rSuccYn');
	$tmp_log[] = '����޽��� : '.$agspay->GetResult('rResMsg');

	if($agspay->GetResult('AuthTy') == 'card' || $agspay->GetResult('AuthTy') == 'virtual') {
		$tmp_log[] = '���νð� : '.$agspay->GetResult('rApprTm');
	}

	if($agspay->GetResult('AuthTy') == 'card' ) {
		$card_nm = $cards[$agspay->GetResult('rCardCd')];
		$tmp_log[] = '�����ڵ� : '.$agspay->GetResult('rBusiCd');
		$tmp_log[] = '���ι�ȣ : '.$agspay->GetResult('rApprNo');
		$tmp_log[] = 'ī����ڵ� : '.$agspay->GetResult('rCardCd').'('.$card_nm.')';
		$tmp_log[] = '�ŷ���ȣ : '.$agspay->GetResult('rDealNo');
	}

	if($agspay->GetResult('AuthTy') == 'card' && ($SubTy == 'visa3d' || $SubTy == 'normal') ) {
		$tmp_log[] = 'ī���� : '.$agspay->GetResult('rCardNm');
		$tmp_log[] = '���Ի��ڵ� : '.$agspay->GetResult('rAquiCd');
		$tmp_log[] = '���Ի�� : '.$agspay->GetResult('rAquiNm');
		$tmp_log[] = '��������ȣ : '.$agspay->GetResult('rMembNo');
	}

	if($agspay->GetResult('AuthTy') == 'iche') {
		$tmp_log[] = '��ü����� : '.$agspay->GetResult('ICHE_OUTBANKNAME');
		$tmp_log[] = '��ü���¿����� : '.$agspay->GetResult('ICHE_OUTBANKMASTER');
		$tmp_log[] = '��ü�ݾ� : '.$agspay->GetResult('ICHE_AMOUNT');
	}

	if($agspay->GetResult('AuthTy') == 'hp' ) {
		$tmp_log[] = '�ڵ�������TID : '.$agspay->GetResult('rHP_TID');
		$tmp_log[] = '�ڵ���������¥ : '.$agspay->GetResult('rHP_DATE');
		$tmp_log[] = '�ڵ��������ڵ�����ȣ : '.$agspay->GetResult('HP_HANDPHONE');
		$tmp_log[] = '�ڵ���������Ż�� : '.$agspay->GetResult('HP_COMPANY');
	}

	if($agspay->GetResult('AuthTy') == 'virtual' || $agspay->GetResult('AuthTy') == 'evirtual' ) {
		$bank_nm = $banks[$agspay->GetResult('VIRTUAL_CENTERCD')];
		$tmp_log[] = '������¹�ȣ : '.$agspay->GetResult('rVirNo');
		$tmp_log[] = '������������ڵ� : ' . $bank_nm;
	}

	if (preg_match('/ES_SENDNO:([0-9]{6})/', $agspay->GetResult('rResMsg'), $matches) == 1) {
		$es_sendno = $matches['1'];
		$tmp_log[] = '����������ũ��(������ȣ,ī��) : '.$es_sendno;
	} else if ($agspay->GetResult('ES_SENDNO') != '') {
		$es_sendno = $agspay->GetResult('ES_SENDNO');
		$tmp_log[] = '����������ũ��(������ȣ,����/����) : '.$es_sendno;
	}

	$settlelog = $ordno." (" . date('Y:m:d H:i:s') . ")\n-----------------------------------\n" . implode( "\n", $tmp_log ) . "\n-----------------------------------\n";

	### ���ں������� �߱�
	@session_start();
	if (session_is_registered('eggData') === true && !strcmp($agspay->GetResult('rSuccYn'),"y")){
		if ($_SESSION[eggData][ordno] == $ordno && $_SESSION[eggData][resno1] != '' && $_SESSION[eggData][resno2] != '' && $_SESSION[eggData][agree] == 'Y'){
			include '../../../lib/egg.class.usafe.php';
			$eggData = $_SESSION[eggData];
			switch ($agspay->GetResult('AuthTy')){
				case "card":
					$eggData[payInfo1] = $card_nm; # (*) ��������(ī���)
					$eggData[payInfo2] = $agspay->GetResult('rApprNo'); # (*) ��������(���ι�ȣ)
					break;
				case "iche":
					$eggData[payInfo1] = $agspay->GetResult('ICHE_OUTBANKNAME'); # (*) ��������(�����)
					$eggData[payInfo2] = $agspay->GetResult("rOrdNo"); # (*) ��������(���ι�ȣ or �ŷ���ȣ)
					break;
				case "virtual":
				case "evirtual":
					$eggData[payInfo1] = $bank_nm; # (*) ��������(�����)
					$eggData[payInfo2] = $agspay->GetResult('rVirNo'); # (*) ��������(���¹�ȣ)
					break;
			}
			$eggCls = new Egg( 'create', $eggData );
			if ( $eggCls->isErr == true && ($agspay->GetResult('AuthTy')=="virtual" || $agspay->GetResult('AuthTy')=="evirtual") ){
				$agspay->SetValue('rSuccYn', '');
			}
			else if ( $eggCls->isErr == true && in_array($agspay->GetResult('AuthTy'), array("card","iche")) );
		}
		session_unregister('eggData');
	}

	### ������� ������ ��� üũ �ܰ� ����
	$res_cstock = true;
	if($cfg['stepStock'] == '1' && ($agspay->GetResult('AuthTy')=="virtual" || $agspay->GetResult('AuthTy')=="evirtual")) $res_cstock = false;

	### item check stock
	include "../../../lib/cardCancel.class.php";
	$cancel = new cardCancel();
	if(!$cancel->chk_item_stock($ordno) && $res_cstock){
		if( !strcmp($agspay->GetResult('rSuccYn'),"y") ) msg('�����ڿ��� �����Ͽ� ī�������� ��û�Ͻʽÿ�!');
		$step = 51;
	}

	### DB(����&����) ó��
	$oData = $db->fetch("select step, vAccount from ".GD_ORDER." where ordno='".$ordno."'");
	if ($oData['step'] > 0 || $oData['vAccount'] != '') { // �ߺ�����

		### �α� ����
		$db->query("update ".GD_ORDER." set settlelog=concat(ifnull(settlelog,''),'".$settlelog."') where ordno='".$ordno."'");
		go('../../order_end.php?ordno='.$ordno.'&card_nm='.$card_nm,'parent');

	} else if ($agspay->GetResult('rSuccYn') == 'y' && $step != 51) { // ��������

		$query = "
		select * from
			".GD_ORDER." a
			left join ".GD_LIST_BANK." b on a.bankAccount = b.sno
		where
			a.ordno='".$ordno."'
		";
		$data = $db->fetch($query);

		### ����ũ�� ���� Ȯ��
		if ($es_sendno != '') {
			$escrowyn = 'y';
			$escrowno = $es_sendno;
		} else {
			$escrowyn = 'n';
			$escrowno = '';
		}

		### ���� ���� ����
		$step = 1;
		$qrc1 = "cyn='y', cdt=now(),";
		$qrc2 = "cyn='y',";

		### ������� ������ �������� ����
		if ($agspay->GetResult('AuthTy') == 'virtual') {
			//������°����� ��� �Ա��� �Ϸ���� ���� �Աݴ�����(������� �߱޼���)�̹Ƿ� ��ǰ�� ����Ͻø� �ȵ˴ϴ�.
			$vAccount = $bank_nm.' '.$agspay->GetResult('rVirNo').' '.$_POST['StoreNm'];
			$step = 0; $qrc1 = $qrc2 = '';
		}

		### ���ݿ����� ����
		if (strpos($agspay->GetResult('rResMsg'),'���ݿ��������༺��') !== false) {
			$qrc1 .= "cashreceipt='pg-agspay',";
		}

		### PG���� ����
		if($agspay->GetResult('AuthTy') == 'card' ) {
			$qrc1 .= "
			cardtno		= '".$agspay->GetResult('rDealNo')."',
			pgAppNo		= '".$agspay->GetResult('rApprNo')."',
			pgCardCd		= '".$agspay->GetResult('rCardCd')."',
			pgAppDt		= '".$agspay->GetResult('rApprTm')."',
			";
		} else if($agspay->GetResult('AuthTy') == 'virtual') {
			$qrc1 .= "
			pgAppDt		= '".$agspay->GetResult('rApprTm')."',
			";
		} else if($agspay->GetResult('AuthTy') == 'iche') {
		} else if($agspay->GetResult('AuthTy') == 'hp' ) {
			$qrc1 .= "
			cardtno		= '".$agspay->GetResult('rHP_TID')."',
			pgAppDt		= '".$agspay->GetResult('rHP_DATE')."',
			";
		}

		### �ǵ���Ÿ ����
		$db->query("
		update ".GD_ORDER." set ".$qrc1."
			step		= '".$step."',
			step2		= '',
			escrowyn	= '".$escrowyn."',
			escrowno	= '".$escrowno."',
			vAccount	= '".$vAccount."',
			settlelog	= concat(ifnull(settlelog,''),'".$settlelog."')
		where ordno='".$ordno."'"
		);
		$db->query("update ".GD_ORDER_ITEM." set ".$qrc2." istep='".$step."' where ordno='".$ordno."'");

		### �ֹ��α� ����
		orderLog($ordno,$r_step2[$data['step2']]." > ".$r_step[$step]);

		### ��� ó��
		setStock($ordno);

		### ��ǰ���Խ� ������ ���
		if ($data['m_no'] && $data['emoney']) {
			setEmoney($data['m_no'],-$data['emoney'],'��ǰ���Խ� ������ ���� ���',$ordno);
		}

		### �ֹ�Ȯ�θ���
		if(function_exists('getMailOrderData')){
			sendMailCase($data['email'],0,getMailOrderData($ordno));
		}

		### SMS ���� ����
		$dataSms = $data;

		if ($agspay->GetResult('AuthTy') != 'virtual') {
			sendMailCase($data['email'],1,$data);			### �Ա�Ȯ�θ���
			sendSmsCase('incash',$data['mobileOrder']);	### �Ա�Ȯ��SMS
		} else {
			sendSmsCase('order',$data['mobileOrder']);	### �ֹ�Ȯ��SMS
		}

		go('../../order_end.php?ordno='.$ordno.'&card_nm='.$card_nm,'parent');
	} else { // ��������
		if ($step == '51') {
			$cancel->cancel_db_proc($ordno);
		} else {
			$db->query("update ".GD_ORDER." set step2='54', settlelog=concat(ifnull(settlelog,''),'".$settlelog."') where ordno='".$ordno."'");
			$db->query("update ".GD_ORDER_ITEM." set istep='54' where ordno='".$ordno."'");
		}

		// Ncash ���� ���� ��� API ȣ��
		if($naverNcash->useyn=='Y') $naverNcash->payment_approval_cancel($ordno);

		go('../../order_fail.php?ordno='.$ordno,'parent');
	}


	/*******************************************************************
	* [6] ������ ����ó������ ������ ��� $agspay->GetResult("NetCancID") ���� �̿��Ͽ�
	* ��������� ���� ��Ȯ�ο�û�� �� �� �ֽ��ϴ�.
	*
	* �߰� �����ͼۼ����� �߻��ϹǷ� ������ ����ó������ �ʾ��� ��쿡�� ����Ͻñ� �ٶ��ϴ�.
	*
	* ����� :
	* $agspay->checkPayResult($agspay->GetResult("NetCancID"));
	*
	*******************************************************************/

	/*
	$agspay->SetValue("Type", "Pay"); // ����
	$agspay->checkPayResult($agspay->GetResult("NetCancID"));
	*/

	/*******************************************************************
	* [7] ����DB ���� �� ��Ÿ ó���۾� ������н� �������
	*
	* $cancelReq : "true" ������ҽ���, "false" ������ҽ������.
	*
	* ��������� ���� ����ó���κ� ���� �� �����ϴ� ���
	* �Ʒ��� �ڵ带 �����Ͽ� �ŷ��� ����� �� �ֽ��ϴ�.
	*	��Ҽ������� : $agspay->GetResult("rCancelSuccYn") (����:y ����:n)
	*	��Ұ���޽��� : $agspay->GetResult("rCancelResMsg")
	*
	* ���ǻ��� :
	* �������(virtual)�� ������� ����� �������� �ʽ��ϴ�.
	*******************************************************************/

	// ����ó���κ� ������н� $cancelReq�� "true"�� �����Ͽ�
	// ������Ҹ� ����ǵ��� �� �� �ֽ��ϴ�.
	// $cancelReq�� "true"������ ���������� �������� �Ǵ��ϼž� �մϴ�.

	/*
	$cancelReq = "false";

	if($cancelReq == "true")
	{
		$agspay->SetValue("Type", "Cancel"); // ����
		$agspay->SetValue("CancelMsg", "DB FAIL"); // ��һ���
		$agspay->startPay();
	}
	*/


?>