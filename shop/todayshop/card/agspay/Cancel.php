<?php
/**********************************************************************************************
*
* ���ϸ� : AGS_cancel_ing.php
* �ۼ����� : 2009/04/01
*
* �ô�����Ʈ �÷����ο��� ���ϵ� ����Ÿ�� �޾Ƽ� ������ҿ�û�� �մϴ�.
*
* Copyright AEGIS ENTERPRISE.Co.,Ltd. All rights reserved.
*
**********************************************************************************************/

	/****************************************************************************
	*
	* [1] ���̺귯��(AGSLib.php)�� ��Ŭ��� �մϴ�.
	*
	****************************************************************************/
	require (dirname(__FILE__)."/lib/AGSLib.php");

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
	//$agspay->SetValue("AgsPayHome","C:/htdocs/agspay");				//�ô�����Ʈ ������ġ ���丮 (������ �°� ����)
	$agspay->SetValue("AgsPayHome",$_SERVER['DOCUMENT_ROOT'].$data['rootDir'].'/log/agspay');			//�ô�����Ʈ ������ġ ���丮 (������ �°� ����)
	$agspay->SetValue("log","true");									//true : �αױ��, false : �αױ�Ͼ���.
	$agspay->SetValue("logLevel","INFO");								//�α׷��� : DEBUG, INFO, WARN, ERROR, FATAL (�ش� �����̻��� �α׸� ��ϵ�)
	$agspay->SetValue("Type", "Cancel");								//������(�����Ұ�)
	$agspay->SetValue("RecvLen", 7);									//���� ������(����) üũ ������ 6 �Ǵ� 7 ����.

	$agspay->SetValue("StoreId",trim($data["StoreId"]));				//�������̵�
	$agspay->SetValue("AuthTy",'card');									//��������
	$agspay->SetValue("SubTy",trim($data["SubTy"]));					//�����������
	$agspay->SetValue("rApprNo",trim($data["rApprNo"]));				//���ι�ȣ
	$agspay->SetValue("rApprTm",trim($data["rApprTm"]));				//��������
	$agspay->SetValue("rDealNo",trim($data["rDealNo"]));				//�ŷ���ȣ
	if (empty($data["cancelPrice"]) === false) {
		$agspay->SetValue("cancelPrice",trim($data["cancelPrice"]));	//��ұݾ�
	}

	/****************************************************************************
	*
	* [4] �ô�����Ʈ ���������� ������ ��û�մϴ�.
	*
	****************************************************************************/
	echo ($agspay->startPay());

	/****************************************************************************
	*
	* [5] ��ҿ�û����� ���� ����DB ���� �� ��Ÿ �ʿ��� ó���۾��� �����ϴ� �κ��Դϴ�.
	*
	* �ſ�ī����� ��Ұ���� ���������� ���ŵǾ����Ƿ� DB �۾��� �� ���
	* ����������� �����͸� �����ϱ� �� �̺κп��� �ϸ�ȴ�.
	*
	* ���⼭ DB �۾��� �� �ּ���.
	* ��Ҽ������� : $agspay->GetResult("rCancelSuccYn") (����:y ����:n)
	* ��Ұ���޽��� : $agspay->GetResult("rCancelResMsg")
	*
	****************************************************************************/

	if($agspay->GetResult("rCancelSuccYn") == "y")
	{
		$settlelog .= '�ô�����Ʈ ī�� ��� ���'."\n";
		$settlelog .= "����ڵ�: ".$agspay->GetResult("rCancelSuccYn")."\n";
		$settlelog .= "����޼���: ".$agspay->GetResult("rCancelResMsg");
		$cardCancelResult = true;
	}
	else
	{
		// �������п� ���� ����ó���κ�
		$settlelog = "\n----------------------------------------\n";
		$settlelog .= "�ô�����Ʈ ī�� ��� ���\n";
		$settlelog .= "$ordno (".date('Y:m:d H:i:s').")\n";
		$settlelog .= "����ڵ�: ".$agspay->GetResult("rCancelSuccYn")."\n";
		$settlelog .= "����޼���: ".$agspay->GetResult("rCancelResMsg")."\n";
		$settlelog .= "----------------------------------------\n";
		$cardCancelResult = false;
	}
?>