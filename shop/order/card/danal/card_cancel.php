<?php
	include( "./inc/function.php" );

	/********************************************************************************
	 *
	 * �ٳ� �޴��� ���� ���
	 *
	 * - ���� ��� ��û ������
	 *      CP���� �� ���� ��� ���� ����
	 *
	 * ���� �ý��� ������ ���� ���ǻ����� �����ø� ���񽺰��������� ���� �ֽʽÿ�.
	 * DANAL Commerce Division Technique supporting Team
	 * EMail : tech@danal.co.kr
	 *
	 ********************************************************************************/
	$cardCancel = Core::loader('cardCancel');
	$ordno = $_GET['ordno'];
	$orderData = $db->fetch('SELECT cardtno,pgcancel FROM '.GD_ORDER.' WHERE ordno='.$ordno.' LIMIT 1', true);

	if (!$ordno) {
		msg('�ֹ���ȣ�� �������� �ʴ� �ֹ��Դϴ�',-1);
		exit;
	}
	else if (!$orderData['cardtno']) {
		msg('�ŷ���ȣ�� �������� �ʽ��ϴ�.',-1);
		exit;
	}
	else if ($orderData['pgcancel'] === 'y') {
		msg('�̹� ��ҵ� �ֹ��Դϴ�.',-1);
		exit;
	}

	/***[ �ʼ� ������ ]************************************/
	$TransR = array();

	/******************************************************
	 * ID		: �ٳ����� ������ �帰 ID( function ���� ���� )
	 * PWD		: �ٳ����� ������ �帰 PWD( function ���� ���� )
	 * TID		: ���� �� ���� �ŷ���ȣ( TID or DNTID )
	 ******************************************************/
	$TransR["ID"] = $ID;
	$TransR["PWD"] = $PWD;
	$TransR["TID"] = $orderData['cardtno'];

	$Res = CallTeleditCancel( $TransR,false );

	if ( $Res['Result'] === '0' )
	{
		/**************************************************************************
		 *
		 * ��� ������ ���� �۾�
		 *
		 **************************************************************************/

		 $cardCancel->cancel_proc($ordno, '['.date('Y-m-d H:i:s').'] ������� : ����');
		 msg('���������� ��ҵǾ����ϴ�.');
		 echo "<script> parent.location.reload(); </script>";
	}
	else if ( $Res["Result"] === '507' )
	{
		/**************************************************************************
		 *
		 * ��� ���п� ���� �۾�
		 *
		 **************************************************************************/

		 $cardCancel->cancel_proc($ordno, '['.date('Y-m-d H:i:s').'] ������� : �̹� ���ó�� �Ǿ� �ֹ� ������Ʈ');
		 msg($Res['ErrMsg']);
		 echo "<script> parent.location.reload(); </script>";
	}
	else
	{
		msg($Res['ErrMsg']);
	}
?>