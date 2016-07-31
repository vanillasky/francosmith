<?
    /* ============================================================================== */
    /* =   PAGE : 지불 요청 및 결과 처리 PAGE                                       = */
    /* = -------------------------------------------------------------------------- = */
    /* =   연동시 오류가 발생하는 경우 아래의 주소로 접속하셔서 확인하시기 바랍니다.= */
    /* =   접속 주소 : http://testpay.kcp.co.kr/pgsample/FAQ/search_error.jsp       = */
    /* = -------------------------------------------------------------------------- = */
    /* =   Copyright (c)  2010.05   KCP Inc.   All Rights Reserved.                 = */
    /* ============================================================================== */

	include_once "../../../../lib/library.php";
	include_once "../../../../conf/config.php";
	include_once "../../../../conf/config.mobileShop.php";
	@include_once "../../../../conf/pg.kcp.php";

	$page_type = $_GET['page_type'];

	if($page_type=='mobile') {
		$order_end_page = $cfgMobileShop['mobileShopRootDir'].'/ord/order_end.php';
		$order_fail_page = $cfgMobileShop['mobileShopRootDir'].'/ord/order_fail.php';
	}
	else {
		$order_end_page = $cfg['rootDir'].'/order/order_end.php';
		$order_fail_page = $cfg['rootDir'].'/order/order_fail.php';
	}

	// PG결제 위변조 체크 및 유효성 체크
	if (forge_order_check($_POST['ordr_idxx'],$_POST['good_mny']) === false && $_POST['req_tx'] == 'pay') {
		msg('주문 정보와 결제 정보가 맞질 않습니다. 다시 결제 바랍니다.',$order_fail_page.'?ordno='.$_POST['ordr_idxx'],'parent');
		exit();
	}

	// 네이버 마일리지 결제 승인 API
	include dirname(__FILE__).'/../../../../lib/naverNcash.class.php';
	$naverNcash = new naverNcash(true);
	if ($naverNcash->useyn == 'Y' && $_POST['req_tx'] != 'mod_escrow') {
		if ($_POST['use_pay_method'] == '001000000000') $ncashResult = $naverNcash->payment_approval($_POST['ordr_idxx'], false);
		else $ncashResult = $naverNcash->payment_approval($_POST['ordr_idxx'], true);
		if ($ncashResult === false) {
			msg('네이버 마일리지 사용에 실패하였습니다.', $order_fail_page.'?ordno='.$_POST['ordr_idxx'],'parent');
			exit();
		}
	}

	function settlelog($data){
		$tmp_log = array();

		if($data[req_tx])$tmp_log[] = "요청 구분 : ".$data[req_tx];
		if($data[use_pay_method])$tmp_log[] = "사용한 결제 수단 : ".$data[use_pay_method];

		if($data[res_cd])$tmp_log[] = "결과코드 : ".$data[res_cd];
		if($data[res_msg])$tmp_log[] = "결과내용 : ".$data[res_msg];
		if($data[ordr_idxx])$tmp_log[] = "주문번호 : ".$data[ordr_idxx];
		if($data[tno])$tmp_log[] = "KCP 거래번호 : ".$data[tno];
		if($data[good_mny])$tmp_log[] = "결제금액 : ".$data[good_mny];
		if($data[good_name])$tmp_log[] = "상품명 : ".$data[good_name];
		if($data[buyr_name])$tmp_log[] = "주문자명 : ".$data[buyr_name];
		if($data[buyr_tel1])$tmp_log[] = "주문자 전화번호 : ".$data[buyr_tel1];
		if($data[buyr_tel2])$tmp_log[] = "주문자 휴대폰번호 : ".$data[buyr_tel2];
		if($data[buyr_mail])$tmp_log[] = "주문자 E-mail : ".$data[buyr_mail];

		if($data[card_cd])$tmp_log[] = "카드코드 : ".$data[card_cd];
		if($data[card_name])$tmp_log[] = "카드명 : ".$data[card_name];
		if($data[app_time])$tmp_log[] = "승인시간 : ".$data[app_time];
		if($data[app_no])$tmp_log[] = "승인번호 : ".$data[app_no];
		if($data[quota])$tmp_log[] = "할부개월 : ".$data[quota];

		if($data[epnt_issu])$tmp_log[] = "포인트 서비스사 : ".$data[epnt_issu];
		if($data[pnt_amount])$tmp_log[] = "포인트 적립금액(사용금액) : ".$data[pnt_amount];
		if($data[pnt_app_time])$tmp_log[] = "포인트승인시간 : ".$data[pnt_app_time];
		if($data[pnt_app_no])$tmp_log[] = "포인트승인번호 : ".$data[pnt_app_no];
		if($data[add_pnt])$tmp_log[] = "발생 포인트 : ".$data[add_pnt];
		if($data[use_pnt])$tmp_log[] = "사용가능 포인트 : ".$data[use_pnt];
		if($data[rsv_pnt])$tmp_log[] = "적립 포인트 : ".$data[rsv_pnt];

		if($data[bank_name])$tmp_log[] = "은행명 : ".$data[bank_name];
		if($data[bank_code])$tmp_log[] = "은행코드 : ".$data[bank_code];

		if($data[bankname])$tmp_log[] = "입금 은행 : ".$data[bankname];
		if($data[depositor])$tmp_log[] = "입금계좌 예금주 : ".$data[depositor];
		if($data[account])$tmp_log[] = "입금계좌 번호 : ".$data[account];

		if($data[cash_yn])$tmp_log[] = "현금영수증 등록 여부 : ".$data[cash_yn];
		if($data[cash_authno])$tmp_log[] = "현금 영수증 승인 번호 : ".$data[cash_authno];
		if($data[cash_tr_code])$tmp_log[] = "현금 영수증 발행 구분 : ".$data[cash_tr_code];
		if($data[cash_id_info])$tmp_log[] = "현금 영수증 등록 번호 : ".$data[cash_id_info];

		$settlelog = "{$data[ordr_idxx]} (" . date('Y:m:d H:i:s') . ")\n-----------------------------------\n" . implode( "\n", $tmp_log ) . "\n-----------------------------------\n";
		return $settlelog;
	}

    /* ============================================================================== */
    /* =   환경 설정 파일 Include                                                   = */
    /* = -------------------------------------------------------------------------- = */
    /* =   ※ 필수                                                                  = */
    /* =   테스트 및 실결제 연동시 site_conf_inc.php파일을 수정하시기 바랍니다.     = */
    /* = -------------------------------------------------------------------------- = */

	$g_conf_home_dir  = $_SERVER['DOCUMENT_ROOT'].$cfg[rootDir]."/order/card/kcp/mobile/receipt";     // BIN 절대경로 입력 (bin전까지)
	//$g_conf_gw_url    = "testpaygw.kcp.co.kr";
    //$g_conf_site_cd   = "T0000";
	//$g_conf_site_key  = "3grptw1.zW0GSo4PQdaGvsF__";
	$g_conf_site_name = "KCP SHOP";
	$g_conf_gw_url    = "paygw.kcp.co.kr";	// 실결제시 paygw.kcp.co.kr
	$g_conf_site_cd   = $pg[id];				// 실결제시 실제 ID
    $g_conf_site_key  = $pg[key];				// 실결제시 실제 key
	$g_conf_gw_port   = "8090";        // 포트번호(변경불가)
	$g_conf_log_level = "3";			// 결제 로그 레벨 (변경불가)
	$g_conf_module_type = "01";			// 결제 모듈 타입 설정 (변경불가)

    require "pp_ax_hub_lib.php";              // library [수정불가]

	$module_type      = "01";          // 변경불가
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
?>

<?
    /* ============================================================================== */
    /* =   01. 지불 요청 정보 설정                                                  = */
    /* = -------------------------------------------------------------------------- = */
	$req_tx         = $_POST[ "req_tx"         ]; // 요청 종류
	$tran_cd        = $_POST[ "tran_cd"        ]; // 처리 종류
	/* = -------------------------------------------------------------------------- = */
	$cust_ip        = getenv( "REMOTE_ADDR"    ); // 요청 IP
	$ordr_idxx      = $_POST[ "ordr_idxx"      ]; // 쇼핑몰 주문번호
	$good_name      = $_POST[ "good_name"      ]; // 상품명
	$good_mny       = $_POST[ "good_mny"       ]; // 결제 총금액
	/* = -------------------------------------------------------------------------- = */
    $res_cd         = "";                         // 응답코드
    $res_msg        = "";                         // 응답메시지
    $tno            = $_POST[ "tno"            ]; // KCP 거래 고유 번호
    /* = -------------------------------------------------------------------------- = */
    $buyr_name      = $_POST[ "buyr_name"      ]; // 주문자명
    $buyr_tel1      = $_POST[ "buyr_tel1"      ]; // 주문자 전화번호
    $buyr_tel2      = $_POST[ "buyr_tel2"      ]; // 주문자 핸드폰 번호
    $buyr_mail      = $_POST[ "buyr_mail"      ]; // 주문자 E-mail 주소
    /* = -------------------------------------------------------------------------- = */
    $mod_type       = $_POST[ "mod_type"       ]; // 변경TYPE VALUE 승인취소시 필요
    $mod_desc       = $_POST[ "mod_desc"       ]; // 변경사유
    /* = -------------------------------------------------------------------------- = */
    $use_pay_method = $_POST[ "use_pay_method" ]; // 결제 방법
    $bSucc          = "";                         // 업체 DB 처리 성공 여부
    /* = -------------------------------------------------------------------------- = */
	$app_time       = "";                         // 승인시간 (모든 결제 수단 공통)
	$amount         = "";                         // KCP 실제 거래 금액
	$total_amount   = 0;                          // 복합결제시 총 거래금액
    /* = -------------------------------------------------------------------------- = */
    $card_cd        = "";                         // 신용카드 코드
    $card_name      = "";                         // 신용카드 명
    $app_no         = "";                         // 신용카드 승인번호
    $noinf          = "";                         // 신용카드 무이자 여부
    $quota          = "";                         // 신용카드 할부개월
	/* = -------------------------------------------------------------------------- = */
    $bankname       = "";                         // 입금할 은행명
    $depositor      = "";                         // 입금할 계좌 예금주 성명
    $account        = "";                         // 입금할 계좌 번호
    /* = -------------------------------------------------------------------------- = */
	$pnt_issue      = "";                         // 결제 포인트사 코드
	$pt_idno        = "";                         // 결제 및 인증 아이디
	$pnt_amount     = "";                         // 적립금액 or 사용금액
	$pnt_app_time   = "";                         // 승인시간
	$pnt_app_no     = "";                         // 승인번호
    $add_pnt        = "";                         // 발생 포인트
	$use_pnt        = "";                         // 사용가능 포인트
	$rsv_pnt        = "";                         // 적립 포인트
    /* = -------------------------------------------------------------------------- = */
	$commid         = "";                         // 통신사 코드
	$mobile_no      = "";                         // 휴대폰 번호
	/* = -------------------------------------------------------------------------- = */
	$tk_van_code    = "";                         // 발급사 코드
	$tk_app_no      = "";                         // 상품권 승인 번호
	/* = -------------------------------------------------------------------------- = */
    $cash_yn        = $_POST[ "cash_yn"        ]; // 현금영수증 등록 여부
    $cash_authno    = "";                         // 현금 영수증 승인 번호
    $cash_tr_code   = $_POST[ "cash_tr_code"   ]; // 현금 영수증 발행 구분
    $cash_id_info   = $_POST[ "cash_id_info"   ]; // 현금 영수증 등록 번호
    /* ============================================================================== */

    /* ============================================================================== */
    /* =   02. 인스턴스 생성 및 초기화                                              = */
    /* = -------------------------------------------------------------------------- = */
    /* =       결제에 필요한 인스턴스를 생성하고 초기화 합니다.                     = */
    /* = -------------------------------------------------------------------------- = */
    $c_PayPlus = new C_PP_CLI;

    $c_PayPlus->mf_clear();
    /* ------------------------------------------------------------------------------ */
	/* =   02. 인스턴스 생성 및 초기화 END											= */
	/* ============================================================================== */


    /* ============================================================================== */
    /* =   03. 처리 요청 정보 설정                                                  = */
    /* = -------------------------------------------------------------------------- = */

    /* = -------------------------------------------------------------------------- = */
    /* =   03-1. 승인 요청                                                          = */
    /* = -------------------------------------------------------------------------- = */
    if ( $req_tx == "pay" )
    {
            $c_PayPlus->mf_set_encx_data( $_POST[ "enc_data" ], $_POST[ "enc_info" ] );
    }

    /* = -------------------------------------------------------------------------- = */
    /* =   03-2. 취소/매입 요청                                                     = */
    /* = -------------------------------------------------------------------------- = */
    else if ( $req_tx == "mod" )
    {
        $tran_cd = "00200000";

        $c_PayPlus->mf_set_modx_data( "tno",      $tno      ); // KCP 원거래 거래번호
        $c_PayPlus->mf_set_modx_data( "mod_type", $mod_type ); // 원거래 변경 요청 종류
        $c_PayPlus->mf_set_modx_data( "mod_ip",   $cust_ip  ); // 변경 요청자 IP
        $c_PayPlus->mf_set_modx_data( "mod_desc", $mod_desc ); // 변경 사유
    }
	/* ------------------------------------------------------------------------------ */
	/* =   03.  처리 요청 정보 설정 END  											= */
	/* ============================================================================== */



    /* ============================================================================== */
    /* =   04. 실행                                                                 = */
    /* = -------------------------------------------------------------------------- = */
    if ( $tran_cd != "" )
    {
        $c_PayPlus->mf_do_tx( $trace_no, $g_conf_home_dir, $g_conf_site_cd, $g_conf_site_key, $tran_cd, "",
                              $g_conf_gw_url, $g_conf_gw_port, "payplus_cli_slib", $ordr_idxx,
                              $cust_ip, $g_conf_log_level, 0, 0 ); // 응답 전문 처리

		$res_cd  = $c_PayPlus->m_res_cd;  // 결과 코드
		$res_msg = $c_PayPlus->m_res_msg; // 결과 메시지
    }
    else
    {
        $c_PayPlus->m_res_cd  = "9562";
        $c_PayPlus->m_res_msg = "연동 오류|tran_cd값이 설정되지 않았습니다.";
    }


    /* = -------------------------------------------------------------------------- = */
    /* =   04. 실행 END                                                             = */
    /* ============================================================================== */


    /* ============================================================================== */
    /* =   05. 승인 결과 값 추출                                                    = */
    /* = -------------------------------------------------------------------------- = */
    if ( $req_tx == "pay" )
    {
        if( $res_cd == "0000" )
        {
            $tno       = $c_PayPlus->mf_get_res_data( "tno"       ); // KCP 거래 고유 번호
            $amount    = $c_PayPlus->mf_get_res_data( "amount"    ); // KCP 실제 거래 금액
			$pnt_issue = $c_PayPlus->mf_get_res_data( "pnt_issue" ); // 결제 포인트사 코드

    /* = -------------------------------------------------------------------------- = */
    /* =   05-1. 신용카드 승인 결과 처리                                            = */
    /* = -------------------------------------------------------------------------- = */
            if ( $use_pay_method == "100000000000" )
            {
                $card_cd   = $c_PayPlus->mf_get_res_data( "card_cd"   ); // 카드사 코드
                $card_name = $c_PayPlus->mf_get_res_data( "card_name" ); // 카드 종류
                $app_time  = $c_PayPlus->mf_get_res_data( "app_time"  ); // 승인 시간
                $app_no    = $c_PayPlus->mf_get_res_data( "app_no"    ); // 승인 번호
                $noinf     = $c_PayPlus->mf_get_res_data( "noinf"     ); // 무이자 여부 ( 'Y' : 무이자 )
                $quota     = $c_PayPlus->mf_get_res_data( "quota"     ); // 할부 개월 수
            }

    /* = -------------------------------------------------------------------------- = */
    /* =   05-2. 가상계좌 승인 결과 처리                                            = */
    /* = -------------------------------------------------------------------------- = */
            if ( $use_pay_method == "001000000000" )
            {
                $bankname  = $c_PayPlus->mf_get_res_data( "bankname"  ); // 입금할 은행 이름
                $depositor = $c_PayPlus->mf_get_res_data( "depositor" ); // 입금할 계좌 예금주
                $account   = $c_PayPlus->mf_get_res_data( "account"   ); // 입금할 계좌 번호
				$app_time  = $c_PayPlus->mf_get_res_data( "app_time"  ); // 승인 시간
            }

    /* = -------------------------------------------------------------------------- = */
    /* =   05-3. 포인트 승인 결과 처리                                               = */
    /* = -------------------------------------------------------------------------- = */
            if ( $use_pay_method == "000100000000" )
            {
				$pt_idno      = $c_PayPlus->mf_get_res_data( "pt_idno"      ); // 결제 및 인증 아이디
                $pnt_amount   = $c_PayPlus->mf_get_res_data( "pnt_amount"   ); // 적립금액 or 사용금액
	            $pnt_app_time = $c_PayPlus->mf_get_res_data( "pnt_app_time" ); // 승인시간
	            $pnt_app_no   = $c_PayPlus->mf_get_res_data( "pnt_app_no"   ); // 승인번호
	            $add_pnt      = $c_PayPlus->mf_get_res_data( "add_pnt"      ); // 발생 포인트
                $use_pnt      = $c_PayPlus->mf_get_res_data( "use_pnt"      ); // 사용가능 포인트
                $rsv_pnt      = $c_PayPlus->mf_get_res_data( "rsv_pnt"      ); // 적립 포인트
            }

    /* = -------------------------------------------------------------------------- = */
    /* =   05-4. 휴대폰 승인 결과 처리                                              = */
    /* = -------------------------------------------------------------------------- = */
            if ( $use_pay_method == "000010000000" )
            {
				$app_time  = $c_PayPlus->mf_get_res_data( "hp_app_time"  ); // 승인 시간
				$commid    = $c_PayPlus->mf_get_res_data( "commid"	     ); // 통신사 코드
				$mobile_no = $c_PayPlus->mf_get_res_data( "mobile_no"	 ); // 휴대폰 번호
            }

    /* = -------------------------------------------------------------------------- = */
    /* =   05-5. 상품권 승인 결과 처리                                              = */
    /* = -------------------------------------------------------------------------- = */
            if ( $use_pay_method == "000000001000" )
            {
				$app_time    = $c_PayPlus->mf_get_res_data( "tk_app_time"  ); // 승인 시간
				$tk_van_code = $c_PayPlus->mf_get_res_data( "tk_van_code"  ); // 발급사 코드
				$tk_app_no   = $c_PayPlus->mf_get_res_data( "tk_app_no"    ); // 승인 번호
            }

    /* = -------------------------------------------------------------------------- = */
    /* =   05-6. 현금영수증 결과 처리                                               = */
    /* = -------------------------------------------------------------------------- = */
			$cash_yn = $c_PayPlus->mf_get_res_data("cash_yn");	// 현금영수증 등록여부
            $cash_authno  = $c_PayPlus->mf_get_res_data( "cash_authno"  ); // 현금 영수증 승인 번호
			$cash_id_info = $c_PayPlus->mf_get_res_data("cash_id_info");	// 현금영수증 등록번호

		}
	}
	/* = -------------------------------------------------------------------------- = */
    /* =   05. 승인 결과 처리 END                                                   = */
    /* ============================================================================== */

	/* ============================================================================== */
    /* =   06. 승인 및 실패 결과 DB처리                                             = */
    /* = -------------------------------------------------------------------------- = */
	/* =       결과를 업체 자체적으로 DB처리 작업하시는 부분입니다.                 = */
    /* = -------------------------------------------------------------------------- = */

  if( $res_cd == "" ) //결제취소시 (KCP결제진행중 결제창을 종료시)
  {
	  msg('결제를 취소하였습니다');
	  go($cfg['rootDir'].'/order/order.php');
  }
  
	if ( $req_tx == "pay" )
    {
		$oData = $db->fetch("select step, vAccount from ".GD_ORDER." where ordno='$ordr_idxx'");
		if( ($oData['step'] > 0 || $oData['vAccount'] != '' || $res_cd=='8128') && $_POST[pay_method] != "SAVE") // 중복결제
		{
			### 로그 저장
			$db->query("update ".GD_ORDER." set settlelog=concat(ifnull(settlelog,''),'$settlelog') where ordno='$ordr_idxx'");
			$res = true;
		}
		else if( $res_cd == "0000" )
        {
			$tno	= $arr[tno] = $c_PayPlus->mf_get_res_data( "tno"	); // KCP 거래 고유 번호

			$amount = $arr[amount] =  $c_PayPlus->mf_get_res_data( "amount" ); // KCP 실제 거래 금액

			// 06-1-1. 신용카드
			if ( $use_pay_method == "100000000000" )
            {
				$card_cd = $arr[card_cd]  = $c_PayPlus->mf_get_res_data( "card_cd"   ); // 카드 코드
				$card_name = $arr[card_name] = $c_PayPlus->mf_get_res_data( "card_name" ); // 카드 종류
				$app_time = $arr[app_time]  = $c_PayPlus->mf_get_res_data( "app_time"  ); // 승인 시간
				$app_no = $arr[app_no]   = $c_PayPlus->mf_get_res_data( "app_no"	); // 승인 번호
				$noinf = $arr[noinf]	= $c_PayPlus->mf_get_res_data( "noinf"	 ); // 무이자 여부 ( 'Y' : 무이자 )
				$quota = $arr[quota]	= $c_PayPlus->mf_get_res_data( "quota"	 ); // 할부 개월
			}
			// 06-1-2. 가상계좌
			if ( $use_pay_method == "001000000000" )
            {
				$bankname = $arr[bankname]  = $c_PayPlus->mf_get_res_data( "bankname"  ); // 입금할 은행 이름
				$depositor = $arr[depositor] = $c_PayPlus->mf_get_res_data( "depositor" ); // 입금할 계좌 예금주
				$account = $arr[account]  = $c_PayPlus->mf_get_res_data( "account"   ); // 입금할 계좌 번호
				$app_time = $arr[app_time]  = $c_PayPlus->mf_get_res_data( "app_time"  ); // 승인 시간
			}
			// 06-1-3. 포인트
			if ( $use_pay_method == "000100000000" )
            {
				$pnt_amount = $arr[pnt_amount]  = $c_PayPlus->mf_get_res_data( "pnt_amount"   );
				$pnt_app_time = $arr[pnt_app_time] = $c_PayPlus->mf_get_res_data( "pnt_app_time" );
				$pnt_app_no = $arr[pnt_app_no]   = $c_PayPlus->mf_get_res_data( "pnt_app_no"   );
				$add_pnt  = $arr[add_pnt]	= $c_PayPlus->mf_get_res_data( "add_pnt"	  );
				$use_pnt  = $arr[use_pnt]	= $c_PayPlus->mf_get_res_data( "use_pnt"	  );
				$rsv_pnt   = $arr[rsv_pnt]   = $c_PayPlus->mf_get_res_data( "rsv_pnt"	  );
			}
			// 06-1-4. 휴대폰
			if ( $use_pay_method == "000010000000" )
            {
				$app_time = $arr[app_time] = $c_PayPlus->mf_get_res_data( "hp_app_time"  ); // 승인 시간
			}
			// 06-1-5. 상품권
			 if ( $use_pay_method == "000000001000" )
            {
				 $app_time = $arr[app_time] = $c_PayPlus->mf_get_res_data( "tk_app_time"  ); // 승인 시간
			}

			// 현금영수증
			//$cash_yn = $arr[cash_yn] = $c_PayPlus->mf_get_res_data("cash_yn");	// 현금영수증 등록여부
			//$cash_authno = $arr[cash_authno] = $c_PayPlus->mf_get_res_data("cash_authno");	// 현금영수증 승인번호
			//$cash_id_info = $arr[cash_id_info] = $c_PayPlus->mf_get_res_data("cash_id_info");	// 현금영수증 등록번호

			$query = "
				select * from
					".GD_ORDER." a
					left join ".GD_LIST_BANK." b on a.bankAccount = b.sno
				where
					a.ordno='$ordr_idxx'
				";
				$data = $db->fetch($query);


				### 에스크로 여부 확인
				$escrowyn = ($_POST['escw_used']=="Y") ? "y" : "n";
				if($escrowyn == 'y')$escrowno = $tno;

				$arr = array_merge($_POST,$arr);
				$settlelog = settlelog($arr);

				### 결제 정보 저장
				$step = 1;
				$qrc1 = "cyn='y', cdt=now(),";
				$qrc2 = "cyn='y',";

				switch ($use_pay_method) {
					case "010000000000" : //계좌이체
						 // 은행명 $bank_name 은행코드 $bank_code

					break;
					case "001000000000" : //가상계좌
						// 입금할 은행 이름 $bankname 입금할 계좌 예금주 $depositor 입금할 계좌 번호 $account
						$vAccount = $bankname." ".$account." ".$depositor;
						$step = 0; $qrc1 = $qrc2 = "";
					break;
				}

				### 전자보증보험 발급
				@session_start();
				if (session_is_registered('eggData') === true && $res_cd == "0000"){
					if ($_SESSION[eggData][ordno] == $ordr_idxx && $_SESSION[eggData][resno1] != '' && $_SESSION[eggData][resno2] != '' && $_SESSION[eggData][agree] == 'Y'){
						include '../../../../lib/egg.class.usafe.php';
						$eggData = $_SESSION[eggData];
						switch ($use_pay_method){
							case "100000000000":
								$eggData[payInfo1] = $card_name; # (*) 결제정보(카드사)
								$eggData[payInfo2] = $app_no; # (*) 결제정보(승인번호)
								break;
							case "010000000000":
								$eggData[payInfo1] = $bank_name; # (*) 결제정보(은행명)
								$eggData[payInfo2] = $tno; # (*) 결제정보(승인번호 or 거래번호)
								break;
							case "001000000000":
								$eggData[payInfo1] = $bank_name; # (*) 결제정보(은행명)
								$eggData[payInfo2] = $account; # (*) 결제정보(계좌번호)
								break;
						}
						$eggCls = new Egg( 'create', $eggData );
						if ( $eggCls->isErr == true && $use_pay_method == "001000000000" ){
							//$inipay->m_resultCode = '';
						}
						else if ( $eggCls->isErr == true && in_array($use_pay_method, array("100000000000","010000000000")) );
					}
					session_unregister('eggData');
				}

				### 가상계좌 결제의 재고 체크 단계 설정
				$res_cstock = true;
				if($cfg['stepStock'] == '1' && $use_pay_method=="001000000000") $res_cstock = false;

				### item check stock
				include "../../../../lib/cardCancel.class.php";
				$cancel = new cardCancel();
				if(!$cancel->chk_item_stock($ordr_idxx) && $res_cstock){
					$step = 51; $qrc1 = $qrc2 = "";
				}

				if($step == 51) $cancel->cancel_db_proc($ordr_idxx,$tno);
				else {
					### 실데이타 저장
					$db->query("
					update ".GD_ORDER." set $qrc1
						step		= '$step',
						step2		= '',
						escrowyn	= '$escrowyn',
						escrowno	= '$escrowno',
						vAccount	= '$vAccount',
						cardtno		= '".$tno."',
						settlelog	= concat(ifnull(settlelog,''),'$settlelog')
					where ordno='$ordr_idxx'"
					);
					$res = $db->query("update ".GD_ORDER_ITEM." set $qrc2 istep='$step' where ordno='$ordr_idxx'");

					### 주문로그 저장
					orderLog($ordr_idxx,$r_step2[$data[step2]]." > ".$r_step[$step]);

					### 재고 처리
					setStock($ordr_idxx);

					### 상품구입시 적립금 사용
					if ($sess[m_no] && $data[emoney]){
						setEmoney($sess[m_no],-$data[emoney],"상품구입시 적립금 결제 사용",$ordr_idxx);
					}

					### 주문확인메일
					if(function_exists('getMailOrderData')){
						sendMailCase($data['email'],0,getMailOrderData($ordr_idxx));
					}

					### SMS 변수 설정
					$dataSms = $data;

					if ($use_pay_method != "001000000000"){ //가상계좌가 아닐 경우
						sendMailCase($data[email],1,$data);			### 입금확인메일
						sendSmsCase('incash',$data[mobileOrder]);	### 입금확인SMS
					} else {
						sendSmsCase('order',$data[mobileOrder]);	### 주문확인SMS
					}
				}

				if($res && $step != 51) {
					$bSucc = "true"; // DB 작업 실패 또는 금액 불일치의 경우 "false" 로 세팅
					$res = true;
				}else{
					$bSucc = "false";
					$res = false;
				}

				/* = -------------------------------------------------------------------------- = */
				/* =   05-11. DB 작업 실패일 경우 자동 승인 취소								 = */
				/* = -------------------------------------------------------------------------- = */
				if ( $bSucc == "false" )
				{
					$c_PayPlus->mf_clear();

					$tran_cd = "00200000";

					$c_PayPlus->mf_set_modx_data( "tno",	  $tno						 );  // KCP 원거래 거래번호
					$c_PayPlus->mf_set_modx_data( "mod_type", "STSC"					   );  // 원거래 변경 요청 종류
					$c_PayPlus->mf_set_modx_data( "mod_ip",   $cust_ip					 );  // 변경 요청자 IP
					$c_PayPlus->mf_set_modx_data( "mod_desc", "결과 처리 오류 - 자동 취소" );  // 변경 사유

					$c_PayPlus->mf_do_tx( $tno,  $g_conf_home_dir, $site_cd,
										  $site_key,  $tran_cd,	"",
										  $g_conf_pa_url,  $g_conf_pa_port,  "payplus_cli_slib",
										  $ordr_idxx, $cust_ip,	$g_conf_log_level,
										  0,	$g_conf_mode );

					$res_cd = $arr[res_cd]  = $c_PayPlus->m_res_cd;
					$res_msg = $arr[res_msg] = $c_PayPlus->m_res_msg;
				}

				if($cash_authno) $db-> query("update ".GD_ORDER." set cashreceipt='$cash_authno' where ordno='$ordr_idxx'"); //현금영수증이 발급되었을 경우 현금영수증 처리
				
				go($order_end_page."?ordno=$ordr_idxx&card_nm=$card_name","parent");
		}

	/* = -------------------------------------------------------------------------- = */
    /* =   06. 승인 및 실패 결과 DB처리                                             = */
    /* ============================================================================== */
		else if ( $req_cd != "0000" )
		{
			$res_cd = $arr[res_cd]  = $c_PayPlus->mf_get_res_data( "res_cd"   ); // 결과 코드
			$card_cd = $arr[card_cd]  = $c_PayPlus->mf_get_res_data( "card_cd"   ); // 카드 코드
			$res_msg = $arr[res_msg] = $c_PayPlus->m_res_msg;

			$arr = array_merge($_POST,$arr);
			$settlelog = settlelog($arr);

			if($_POST[pay_method] == "SAVE" ){
				msg('OK캐쉬백 적립실패되었습니다.',0);
			}else{
				$db->query("update ".GD_ORDER." set step2=54, settlelog=concat(ifnull(settlelog,''),'$settlelog'),cardtno='".$tno."' where ordno='$ordr_idxx'");
				$db->query("update ".GD_ORDER_ITEM." set istep=54 where ordno='$ordr_idxx'");
			}

			// Ncash 결제 승인 취소 API 호출
			if ($naverNcash->useyn == 'Y') $naverNcash->payment_approval_cancel($ordr_idxx);

			$res = false;

			go($order_fail_page."?ordno=$ordr_idxx&card_nm=$card_name","parent");
		}
	}

	/* ============================================================================== */
    /* =   07. 승인 결과 DB처리 실패시 : 자동취소                                   = */
    /* = -------------------------------------------------------------------------- = */
    /* =         승인 결과를 DB 작업 하는 과정에서 정상적으로 승인된 건에 대해      = */
    /* =         DB 작업을 실패하여 DB update 가 완료되지 않은 경우, 자동으로       = */
    /* =         승인 취소 요청을 하는 프로세스가 구성되어 있습니다.                = */
	/* =                                                                            = */
    /* =         DB 작업이 실패 한 경우, bSucc 라는 변수(String)의 값을 "false"     = */
    /* =         로 설정해 주시기 바랍니다. (DB 작업 성공의 경우에는 "false" 이외의 = */
    /* =         값을 설정하시면 됩니다.)                                           = */
    /* = -------------------------------------------------------------------------- = */

	$bSucc = ""; // DB 작업 실패 또는 금액 불일치의 경우 "false" 로 세팅

    /* = -------------------------------------------------------------------------- = */
    /* =   07-1. DB 작업 실패일 경우 자동 승인 취소                                 = */
    /* = -------------------------------------------------------------------------- = */
    if ( $req_tx == "pay" )
    {
		if( $res_cd == "0000" )
        {
			if ( $bSucc == "false" )
            {
                $c_PayPlus->mf_clear();

                $tran_cd = "00200000";

                $c_PayPlus->mf_set_modx_data( "tno",      $tno                         );  // KCP 원거래 거래번호
                $c_PayPlus->mf_set_modx_data( "mod_type", "STSC"                       );  // 원거래 변경 요청 종류
                $c_PayPlus->mf_set_modx_data( "mod_ip",   $cust_ip                     );  // 변경 요청자 IP
                $c_PayPlus->mf_set_modx_data( "mod_desc", "결과 처리 오류 - 자동 취소" );  // 변경 사유

                $c_PayPlus->mf_do_tx( "",  $g_conf_home_dir, $g_conf_site_cd,
                                      $g_conf_site_key,  $tran_cd,    "",
                                      $g_conf_gw_url,  $g_conf_gw_port,  "payplus_cli_slib",
                                      $ordr_idxx, $cust_ip,    $g_conf_log_level,
                                      0, 0 );

                $res_cd  = $c_PayPlus->m_res_cd;
                $res_msg = $c_PayPlus->m_res_msg;
            }
        }
	} // End of [res_cd = "0000"]

?>