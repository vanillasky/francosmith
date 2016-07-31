<?php
	include( "./inc/function.php" );

	/********************************************************************************
	 *
	 * 다날 휴대폰 결제 취소
	 *
	 * - 결제 취소 요청 페이지
	 *      CP인증 및 결제 취소 정보 전달
	 *
	 * 결제 시스템 연동에 대한 문의사항이 있으시면 서비스개발팀으로 연락 주십시오.
	 * DANAL Commerce Division Technique supporting Team
	 * EMail : tech@danal.co.kr
	 *
	 ********************************************************************************/
	$cardCancel = Core::loader('cardCancel');
	$ordno = $_GET['ordno'];
	$orderData = $db->fetch('SELECT cardtno,pgcancel FROM '.GD_ORDER.' WHERE ordno='.$ordno.' LIMIT 1', true);

	if (!$ordno) {
		msg('주문번호가 존재하지 않는 주문입니다',-1);
		exit;
	}
	else if (!$orderData['cardtno']) {
		msg('거래번호가 존재하지 않습니다.',-1);
		exit;
	}
	else if ($orderData['pgcancel'] === 'y') {
		msg('이미 취소된 주문입니다.',-1);
		exit;
	}

	/***[ 필수 데이터 ]************************************/
	$TransR = array();

	/******************************************************
	 * ID		: 다날에서 제공해 드린 ID( function 파일 참조 )
	 * PWD		: 다날에서 제공해 드린 PWD( function 파일 참조 )
	 * TID		: 결제 후 받은 거래번호( TID or DNTID )
	 ******************************************************/
	$TransR["ID"] = $ID;
	$TransR["PWD"] = $PWD;
	$TransR["TID"] = $orderData['cardtno'];

	$Res = CallTeleditCancel( $TransR,false );

	if ( $Res['Result'] === '0' )
	{
		/**************************************************************************
		 *
		 * 취소 성공에 대한 작업
		 *
		 **************************************************************************/

		 $cardCancel->cancel_proc($ordno, '['.date('Y-m-d H:i:s').'] 결제취소 : 성공');
		 msg('정상적으로 취소되었습니다.');
		 echo "<script> parent.location.reload(); </script>";
	}
	else if ( $Res["Result"] === '507' )
	{
		/**************************************************************************
		 *
		 * 취소 실패에 대한 작업
		 *
		 **************************************************************************/

		 $cardCancel->cancel_proc($ordno, '['.date('Y-m-d H:i:s').'] 결제취소 : 이미 취소처리 되어 주문 업데이트');
		 msg($Res['ErrMsg']);
		 echo "<script> parent.location.reload(); </script>";
	}
	else
	{
		msg($Res['ErrMsg']);
	}
?>