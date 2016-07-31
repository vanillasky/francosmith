<?php
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Cache-Control: no-store");
    header("Pragma: no-cache");

    include dirname(__FILE__)."/../../../../conf/pg.kcp.php";
	include dirname(__FILE__)."/../../../../conf/pg_mobile.kcp.php";
	@include dirname(__FILE__)."/../../../../conf/pg.escrow.php";	
    
	require "./KCPComLibrary.php";              										// library [수정불가]
	
	$g_conf_home_dir  = $_SERVER['DOCUMENT_ROOT'].$cfg[rootDir]."/order/card/kcp/mobile/receipt";     // BIN 절대경로 입력 (bin전까지) 
	$g_conf_gw_url    = "paygw.kcp.co.kr";
    $g_conf_site_cd   = $pg[id];
	$g_conf_site_key  = $pg[key];
	$g_conf_site_name = "KCP SHOP";
	$g_conf_gw_port   = "8090";        // 포트번호(변경불가)
	
	/* ============================================================================== */
    /* = 스마트폰 SOAP 통신 설정                                                     = */
    /* =----------------------------------------------------------------------------= */
    /* = 테스트 시 : KCPPaymentService.wsdl                                         = */
    /* = 실결제 시 : real_KCPPaymentService.wsdl                                    = */
    /* ============================================================================== */
    $g_wsdl           = "real_KCPPaymentService.wsdl";
    /* = -------------------------------------------------------------------------- = */
    /* =   환경 설정 파일 Include END                                               = */
    /* ============================================================================== */
    // 쇼핑몰 페이지에 맞는 문자셋을 지정해 주세요.
    
	$charSetType      = "euc-kr";             // UTF-8인 경우 "utf-8"로 설정
    
    $siteCode         = $_GET[ "site_cd"     ];
    $orderID          = $_GET[ "ordr_idxx"   ];
    $paymentMethod    = $_GET[ "pay_method"  ];
    $escrow           = ( $_GET[ "escw_used"   ] == "Y" ) ? true : false;
    $productName      = $_GET[ "good_name"   ];

    // 아래 두값은 POST된 값을 사용하지 않고 서버에 SESSION에 저장된 값을 사용하여야 함.
    $paymentAmount    = $_GET[ "good_mny"    ]; // 결제 금액
    $returnUrl        = $_GET[ "Ret_URL"     ];

    // Access Credential 설정
    $accessLicense    = "";
    $signature        = "";
    $timestamp        = "";

    // Base Request Type 설정
    $detailLevel      = "0";
    $requestApp       = "WEB";
    $requestID        = $orderID;
    $userAgent        = $_SERVER['HTTP_USER_AGENT'];
    $version          = "0.1";

    try
    {
        $payService = new PayService( $g_wsdl );

        $payService->setCharSet( $charSetType );
        
        $payService->setAccessCredentialType( $accessLicense, $signature, $timestamp );
        $payService->setBaseRequestType( $detailLevel, $requestApp, $requestID, $userAgent, $version );
        $payService->setApproveReq( $escrow, $orderID, $paymentAmount, $paymentMethod, $productName, $returnUrl, $siteCode );

        $approveRes = $payService->approve();
                
        printf( "%s,%s,%s,%s", $payService->resCD,  $approveRes->approvalKey,
                               $approveRes->payUrl, $payService->resMsg );

    }
    catch (SoapFault $ex )
    {
        printf( "%s,%s,%s,%s", "95XX", "", "", iconv("EUC-KR","UTF-8","연동 오류 (PHP SOAP 모듈 설치 필요)" ) );
    }
?>