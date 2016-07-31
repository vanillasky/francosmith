<?
	// from card_gate.php
	header( "Pragma: No-Cache" );
	include( "./inc/function.php" );
	include dirname(__FILE__).'/../../../conf/config.mobileShop.php';

	/******************************************************************************** 
	 *
	 * 다날 휴대폰 결제
	 *
	 * - 결제 요청 페이지 
	 *금액 확인 및 결제 요청
	 *
	 * 결제 시스템 연동에 대한 문의사항이 있으시면 서비스개발팀으로 연락 주십시오.
	 * DANAL Commerce Division Technique supporting Team 
	 * EMail : tech@danal.co.kr 
	 *
	 ********************************************************************************/

	$ordno = $_POST['ordno'];			// 주문번호
	$price = $danal->getPrice($ordno);			// 결제 금액을 검증하기 위해 DB에서 꺼내옴
	$isMobile = $_POST['isMobile'];	// 모바일 사용 여부 확인
	$isPc = $_POST['isPc'];

	$BillErr = false;
	$TransR = array();

	/*
	 * Get ServerInfo
	 */
	$ServerInfo = $_POST["ServerInfo"];   

	/*
	 * NCONFIRM
	 */
	$nConfirmOption = 1; 
	$TransR["Command"] = "NCONFIRM";
	$TransR["OUTPUTOPTION"] = "DEFAULT";
	$TransR["ServerInfo"] = $ServerInfo;
	$TransR["IFVERSION"] = "V1.1.2";
	$TransR["ConfirmOption"] = $nConfirmOption;

	/*
	 * ConfirmOption이 1이면 CPID, AMOUNT 필수 전달
	 */
	if( $nConfirmOption == 1 )
	{
		$TransR["CPID"] = $ID;
		$TransR["AMOUNT"] = $price;
	}

	$Res = CallTeledit( $TransR,false );

	if( $Res["Result"] == "0" )
	{
		/*
		 * NBILL
		 */
		$TransR = array();

		$nBillOption = 1;
		$TransR["Command"] = "NBILL";
		$TransR["OUTPUTOPTION"] = "DEFAULT";
		$TransR["ServerInfo"] = $ServerInfo;
		$TransR["IFVERSION"] = "V1.1.2";
		$TransR["BillOption"] = $nBillOption;

		$Res2 = CallTeledit( $TransR,false );

		if( $Res2["Result"] != "0" )
		{
			$BillErr = true;
		}
	}

	if( $Res["Result"] == "0" && $Res2["Result"] == "0" )
	{
		/**************************************************************************
		 *
		 * 결제 완료에 대한 작업 
		 * - AMOUNT, ORDERID 등 결제 거래내용에 대한 검증을 반드시 하시기 바랍니다.
		 *
		 **************************************************************************/

		// 데이터베이스 갱신
		$result = array_merge($_POST, $Res, $Res2 );
		$danal->setDB($result);

		if ($isMobile && $isPc) {
			go($shopConfig['rootDir'].'/order/order_end.php?ordno='.$ordno);
		}
		else if ($isMobile && !$isPc) {
			go($cfgMobileShop['mobileShopRootDir'].'/ord/order_end.php?ordno='.$ordno);
		}
		else {
			echo "<script>opener.parent.location.replace('".$shopConfig['rootDir']."/order/order_end.php?ordno=".$ordno."')</script>";
			echo '<script>window.close();</script>';
		}
	}
	else {
		/**************************************************************************
		 *
		 * 결제 실패에 대한 작업 
		 *
		 **************************************************************************/

		if( $BillErr ) $Res = $Res2;

		$Result		= $Res["Result"];
		$ErrMsg		= $Res["ErrMsg"];
		$AbleBack	= false;
		$BackURL	= $_POST["BackURL"];
		$IsUseCI	= $_POST["IsUseCI"];
		$CIURL		= $_POST["CIURL"];
		$BgColor	= $_POST["BgColor"];
		
		// 결제 실패 로그 작성
		$danal->failLog($ordno, $Result, $ErrMsg);

		if (isset($isMobile)) {
			include( "./card_mobile_error.php" );
		}
		else {
			include( "./card_error.php" );
		}
	}
?>
