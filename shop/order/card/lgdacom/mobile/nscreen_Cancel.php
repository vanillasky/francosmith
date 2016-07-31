<?php
	include dirname(__FILE__)."/../../../../conf/config.php";
	include dirname(__FILE__)."/../../../../conf/config.mobileShop.php";

	$page_type = $_GET['page_type'];

	if($page_type=='mobile') {
		$order_page = $cfgMobileShop['mobileShopRootDir'].'/ord/order.php';
	}
	else {
		$order_page = $cfg['rootDir'].'/order/order.php';
	}

	if($_GET['isAsync']=='Y'){
		$orderUrl=$order_page;
		echo "<script>alert('사용자가 ISP(국민/BC) 카드결제을 중단하였습니다.');location.href='$orderUrl'</script>";
		exit;
	}
	/*
	 * [결제취소 요청 페이지]
	 *
	 * LG데이콤으로 부터 내려받은 거래번호(LGD_TID)를 가지고 취소 요청을 합니다.(파라미터 전달시 POST를 사용하세요)
	 * (승인시 LG데이콤으로 부터 내려받은 PAYKEY와 혼동하지 마세요.)
	 */
	$CST_PLATFORM				= $data['service'];								//LG데이콤 결제 서비스 선택(test:테스트, service:서비스)
	$CST_MID					= $data['mid'];									//상점아이디(LG데이콤으로 부터 발급받으신 상점아이디를 입력하세요)
	$LGD_MID					= (("test" == $CST_PLATFORM)?"t":"").$CST_MID;	//상점아이디(자동생성)
	$LGD_TID					= $data['tid'];									//LG데이콤으로 부터 내려받은 거래번호(LGD_TID)

 	$configPath					=$_SERVER['DOCUMENT_ROOT'].$cfg['rootDir']."/conf/lgdacom";				//LG데이콤에서 제공한 환경파일("/conf/lgdacom.conf") 위치 지정.

	require_once(dirname(__FILE__)."/nscreen_XPayClient.php");
	$xpay = &new XPayClient($configPath, $CST_PLATFORM);
	$xpay->Init_TX($LGD_MID);

	$xpay->Set("LGD_TXNAME", "Cancel");
	$xpay->Set("LGD_TID", $LGD_TID);

	/*
	 * 1. 결제취소 요청 결과처리
	 *
	 * 취소결과 리턴 파라미터는 연동메뉴얼을 참고하시기 바랍니다.
	 */
	$xpay->TX();

	if( "0000" == $xpay->Response_Code() ){
		//1)결제취소결과 화면처리(성공,실패 결과 처리를 하시기 바랍니다.)
		$settlelog = 'LGU+ SmartXPay 카드 취소 결과'."\n";
		$settlelog .= '결과코드 : '.$xpay->Response_Code()."\n";
		$settlelog .= '결과내용 : '.$xpay->Response_Msg()."\n";
		$cardCancelResult	= true;
	}else {
		//2)API 요청 실패 화면처리
		$settlelog = $data['oid'].' ('.date('Y:m:d H:i:s').')'."\n";
		$settlelog .= '-----------------------------------'."\n";
		$settlelog .= 'LGU+ SmartXPay 카드 취소 실패'."\n";
		$settlelog .= '결과코드 : '.$xpay->Response_Code()."\n";
		$settlelog .= '결과내용 : '.$xpay->Response_Msg()."\n";
		$settlelog .= '-----------------------------------'."\n";
		$cardCancelResult	= false;
	}
?>
