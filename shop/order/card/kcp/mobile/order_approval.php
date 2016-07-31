<?php
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Cache-Control: no-store");
    header("Pragma: no-cache");

    include dirname(__FILE__)."/../../../../conf/pg.kcp.php";
	include dirname(__FILE__)."/../../../../conf/pg_mobile.kcp.php";
	@include dirname(__FILE__)."/../../../../conf/pg.escrow.php";	
    
	require "./KCPComLibrary.php";              										// library [�����Ұ�]
	
	$g_conf_home_dir  = $_SERVER['DOCUMENT_ROOT'].$cfg[rootDir]."/order/card/kcp/mobile/receipt";     // BIN ������ �Է� (bin������) 
	$g_conf_gw_url    = "paygw.kcp.co.kr";
    $g_conf_site_cd   = $pg[id];
	$g_conf_site_key  = $pg[key];
	$g_conf_site_name = "KCP SHOP";
	$g_conf_gw_port   = "8090";        // ��Ʈ��ȣ(����Ұ�)
	
	/* ============================================================================== */
    /* = ����Ʈ�� SOAP ��� ����                                                     = */
    /* =----------------------------------------------------------------------------= */
    /* = �׽�Ʈ �� : KCPPaymentService.wsdl                                         = */
    /* = �ǰ��� �� : real_KCPPaymentService.wsdl                                    = */
    /* ============================================================================== */
    $g_wsdl           = "real_KCPPaymentService.wsdl";
    /* = -------------------------------------------------------------------------- = */
    /* =   ȯ�� ���� ���� Include END                                               = */
    /* ============================================================================== */
    // ���θ� �������� �´� ���ڼ��� ������ �ּ���.
    
	$charSetType      = "euc-kr";             // UTF-8�� ��� "utf-8"�� ����
    
    $siteCode         = $_GET[ "site_cd"     ];
    $orderID          = $_GET[ "ordr_idxx"   ];
    $paymentMethod    = $_GET[ "pay_method"  ];
    $escrow           = ( $_GET[ "escw_used"   ] == "Y" ) ? true : false;
    $productName      = $_GET[ "good_name"   ];

    // �Ʒ� �ΰ��� POST�� ���� ������� �ʰ� ������ SESSION�� ����� ���� ����Ͽ��� ��.
    $paymentAmount    = $_GET[ "good_mny"    ]; // ���� �ݾ�
    $returnUrl        = $_GET[ "Ret_URL"     ];

    // Access Credential ����
    $accessLicense    = "";
    $signature        = "";
    $timestamp        = "";

    // Base Request Type ����
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
        printf( "%s,%s,%s,%s", "95XX", "", "", iconv("EUC-KR","UTF-8","���� ���� (PHP SOAP ��� ��ġ �ʿ�)" ) );
    }
?>