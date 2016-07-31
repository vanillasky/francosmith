<?
	/* ============================================================================== */
	/* =   PAGE : 지불 요청 및 결과 처리 PAGE									   = */
	/* = -------------------------------------------------------------------------- = */
	/* =   Copyright (c)  2006   KCP Inc.   All Rights Reserverd.				   = */
	/* ============================================================================== */

	include "../../../lib/library.php";
	include "../../../conf/config.php";
	@include "../../../conf/pg.kcp.php";

	// PG결제 위변조 체크 및 유효성 체크
	if (forge_order_check($_POST['ordr_idxx'],$_POST['good_mny']) === false && $_POST['req_tx'] == 'pay') {
		msg('주문 정보와 결제 정보가 맞질 않습니다. 다시 결제 바랍니다.','../../order_fail.php?ordno='.$_POST['ordr_idxx'],'parent');
		exit();
	}

	// Ncash 결제 승인 API
	include "../../../lib/naverNcash.class.php";
	$naverNcash = new naverNcash();
	if($naverNcash->useyn=='Y' && $_POST['req_tx']!='mod_escrow')
	{
		if($_POST['use_pay_method']=='001000000000') $ncashResult = $naverNcash->payment_approval($_POST['ordr_idxx'], false);
		else $ncashResult = $naverNcash->payment_approval($_POST['ordr_idxx'], true);
		if($ncashResult===false)
		{
			msg('네이버 마일리지 사용에 실패하였습니다.', '../../order_fail.php?ordno='.$_POST['ordr_idxx'],'parent');
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

		$settlelog = "{$ordno} (" . date('Y:m:d H:i:s') . ")\n-----------------------------------\n" . implode( "\n", $tmp_log ) . "\n-----------------------------------\n";
		return $settlelog;
	}

	/* ============================================================================== */
	/* =   01. 지불 데이터 셋업 (업체에 맞게 수정)								  = */
	/* = -------------------------------------------------------------------------- = */
	function get_base_dir( $fname ) {
		$tmp = explode( "/", realpath( $fname ) );
		array_pop( $tmp );
		return implode( "/", $tmp );
	}
	$SERVER_DIR = get_base_dir(__FILE__);

	$g_conf_home_dir  = $SERVER_DIR."/payplus"; // BIN 절대경로 입력
	$g_conf_log_level = "3";					  // 변경불가
	$g_conf_pa_url	= "paygw.kcp.co.kr";	// real url : paygw.kcp.co.kr , test url : testpaygw.kcp.co.kr
	$g_conf_pa_port   = "8090";				   // 포트번호 , 변경불가
	$g_conf_mode	  = 0;						// 변경불가

	require "pp_ax_hub_lib.php";				  // library [수정불가]
	/* ============================================================================== */


	/* ============================================================================== */
	/* =   02. 지불 요청 정보 설정												  = */
	/* = -------------------------------------------------------------------------- = */
	$site_cd		= $_POST[ "site_cd"		]; // 사이트 코드
	$site_key	   = $_POST[ "site_key"	   ]; // 사이트 키
	$req_tx		 = $_POST[ "req_tx"		 ]; // 요청 종류
	$cust_ip		= getenv( "REMOTE_ADDR"	); // 요청 IP
	$ordr_idxx	  = $_POST[ "ordr_idxx"	  ]; // 쇼핑몰 주문번호
	$good_name	  = $_POST[ "good_name"	  ]; // 상품명
	/* = -------------------------------------------------------------------------- = */
	$good_mny	   = $_POST[ "good_mny"	   ]; // 결제 총금액
	$tran_cd		= $_POST[ "tran_cd"		]; // 처리 종류
	/* = -------------------------------------------------------------------------- = */
	$res_cd		 = "";						 // 응답코드
	$res_msg		= "";						 // 응답메시지
	$tno			= $_POST[ "tno"			]; // KCP 거래 고유 번호
	/* = -------------------------------------------------------------------------- = */
	$buyr_name	  = $_POST[ "buyr_name"	  ]; // 주문자명
	$buyr_tel1	  = $_POST[ "buyr_tel1"	  ]; // 주문자 전화번호
	$buyr_tel2	  = $_POST[ "buyr_tel2"	  ]; // 주문자 핸드폰 번호
	$buyr_mail	  = $_POST[ "buyr_mail"	  ]; // 주문자 E-mail 주소
	/* = -------------------------------------------------------------------------- = */
	$bank_name	  = "";						 // 은행명
	$bank_code	  = "";						 // 은행코드
	$bank_issu	  = $_POST[ "bank_issu"	  ]; // 계좌이체 서비스사
	/* = -------------------------------------------------------------------------- = */
	$mod_type	   = $_POST[ "mod_type"	   ]; // 변경TYPE VALUE 승인취소시 필요
	$mod_desc	   = $_POST[ "mod_desc"	   ]; // 변경사유
	/* = -------------------------------------------------------------------------- = */
	$use_pay_method = $_POST[ "use_pay_method" ]; // 결제 방법
	$epnt_issu	  = $_POST[ "epnt_issu"	  ]; //포인트(OK캐쉬백,복지포인트)
	$bSucc		  = "";						 // 업체 DB 처리 성공 여부
	$acnt_yn		= $_POST[  "acnt_yn"	   ]; // 상태변경시 계좌이체, 가상계좌 여부
	$escw_used	  = $_POST[  "escw_used"	 ]; // 에스크로 사용 여부
	$pay_mod		= $_POST[  "pay_mod"	   ]; // 에스크로 결제처리 모드
	$deli_term	  = $_POST[  "deli_term"	 ]; // 배송 소요일
	$bask_cntx	  = $_POST[  "bask_cntx"	 ]; // 장바구니 상품 개수
	$good_info	  = $_POST[  "good_info"	 ]; // 장바구니 상품 상세 정보
	$rcvr_name	  = $_POST[  "rcvr_name"	 ]; // 수취인 이름
	$rcvr_tel1	  = $_POST[  "rcvr_tel1"	 ]; // 수취인 전화번호
	$rcvr_tel2	  = $_POST[  "rcvr_tel2"	 ]; // 수취인 휴대폰번호
	$rcvr_mail	  = $_POST[  "rcvr_mail"	 ]; // 수취인 E-Mail
	$rcvr_zipx	  = $_POST[  "rcvr_zipx"	 ]; // 수취인 우편번호
	$rcvr_add1	  = $_POST[  "rcvr_add1"	 ]; // 수취인 주소
	$rcvr_add2	  = $_POST[  "rcvr_add2"	 ]; // 수취인 상세주소

	/* = -------------------------------------------------------------------------- = */
	$card_cd		= "";						 // 신용카드 코드
	$card_name	  = "";						 // 신용카드 명
	$app_time	   = "";						 // 승인시간 (모든 결제 수단 공통)
	$app_no		 = "";						 // 신용카드 승인번호
	$noinf		  = "";						 // 신용카드 무이자 여부
	$quota		  = "";						 // 신용카드 할부개월
	$bankname	   = "";						 // 은행명
	$depositor	  = "";						 // 입금 계좌 예금주 성명
	$account		= "";						 // 입금 계좌 번호
	/* = -------------------------------------------------------------------------- = */
	$amount		 = "";						 // KCP 실제 거래 금액
	/* = -------------------------------------------------------------------------- = */
	$add_pnt		= "";						 // 발생 포인트
	$use_pnt		= "";						 // 사용가능 포인트
	$rsv_pnt		= "";						 // 적립 포인트
	$pnt_app_time   = "";						 // 승인시간
	$pnt_app_no	 = "";						 // 승인번호
	$pnt_amount	 = "";						 // 적립금액 or 사용금액
	/* ============================================================================== */
	$cash_yn		= $_POST[ "cash_yn"		]; // 현금영수증 등록 여부
	$cash_authno	= "";						 // 현금 영수증 승인 번호
	$cash_tr_code   = $_POST[ "cash_tr_code"   ]; // 현금 영수증 발행 구분
	$cash_id_info   = $_POST[ "cash_id_info"   ]; // 현금 영수증 등록 번호

	$ordno = $ordr_idxx;


	/* ============================================================================== */
	/* =   03. 인스턴스 생성 및 초기화											  = */
	/* = -------------------------------------------------------------------------- = */
	/* =	   결제에 필요한 인스턴스를 생성하고 초기화 합니다.					 = */
	/* = -------------------------------------------------------------------------- = */
	$c_PayPlus = new C_PP_CLI;

	$c_PayPlus->mf_clear();
	/* ============================================================================== */


	/* ============================================================================== */
	/* =   04. 처리 요청 정보 설정, 실행											= */
	/* = -------------------------------------------------------------------------- = */

	/* = -------------------------------------------------------------------------- = */
	/* =   04-1. 승인 요청														  = */
	/* = -------------------------------------------------------------------------- = */
	if ( $req_tx == "pay" )
	{
		$c_PayPlus->mf_set_ordr_data( 'ordr_mony',  $_POST['good_mny'] );
		$c_PayPlus->mf_set_encx_data( $_POST[ "enc_data" ], $_POST[ "enc_info" ] );
	}

	/* = -------------------------------------------------------------------------- = */
	/* =   04-2. 취소/매입 요청													 = */
	/* = -------------------------------------------------------------------------- = */
	else if ( $req_tx == "mod" )
	{
		$tran_cd = "00200000";

		$c_PayPlus->mf_set_modx_data( "tno",	  $tno	  ); // KCP 원거래 거래번호
		$c_PayPlus->mf_set_modx_data( "mod_type", $mod_type ); // 원거래 변경 요청 종류
		$c_PayPlus->mf_set_modx_data( "mod_ip",   $cust_ip  ); // 변경 요청자 IP
		$c_PayPlus->mf_set_modx_data( "mod_desc", $mod_desc ); // 변경 사유
	}

	/* = -------------------------------------------------------------------------- = */
	/* =   04-3. 에스크로 상태변경 요청											  = */
	/* = -------------------------------------------------------------------------- = */
	else if ( $req_tx == "mod_escrow" )
	{
		$tran_cd = "00200000";

		$c_PayPlus->mf_set_modx_data( "tno",		$tno			);		  // KCP 원거래 거래번호
		$c_PayPlus->mf_set_modx_data( "mod_type",   $mod_type	   );		  // 원거래 변경 요청 종류
		$c_PayPlus->mf_set_modx_data( "mod_ip",	 $cust_ip		);		  // 변경 요청자 IP
		$c_PayPlus->mf_set_modx_data( "mod_desc",   $mod_desc	   );		  // 변경 사유
		if ($mod_type == "STE1")												// 상태변경 타입이 [배송요청]인 경우
		{
			$c_PayPlus->mf_set_modx_data( "deli_numb",   $_POST[ "deli_numb" ] );		  // 운송장 번호
			$c_PayPlus->mf_set_modx_data( "deli_corp",   $_POST[ "deli_corp" ] );		  // 택배 업체명
		}
		else if ($mod_type == "STE2" || $mod_type == "STE4")					// 상태변경 타입이 [즉시취소] 또는 [취소]인 계좌이체, 가상계좌의 경우
		{
			if ($acnt_yn == "Y")
			{
				$c_PayPlus->mf_set_modx_data( "refund_account",   $_POST[ "refund_account" ] );	  // 환불수취계좌번호
				$c_PayPlus->mf_set_modx_data( "refund_nm",		$_POST[ "refund_nm"	  ] );	  // 환불수취계좌주명
				$c_PayPlus->mf_set_modx_data( "bank_code",		$_POST[ "bank_code"	  ] );	  // 환불수취은행코드
			}
		}
	}

	/* = -------------------------------------------------------------------------- = */
	/* =   04-3. 실행															   = */
	/* = -------------------------------------------------------------------------- = */
	if ( $tran_cd != "" )
	{
		$c_PayPlus->mf_do_tx( $trace_no, $g_conf_home_dir, $site_cd, $site_key, $tran_cd, "",
							  $g_conf_pa_url, $g_conf_pa_port, "payplus_cli_slib", $ordr_idxx,
							  $cust_ip, $g_conf_log_level, 0, $g_conf_mode );
	}
	else
	{
		$c_PayPlus->m_res_cd  = "9562";
		$c_PayPlus->m_res_msg = "연동 오류 TRAN_CD[" . $tran_cd . "]";
	}

	$res_cd = $arr[res_cd] = $c_PayPlus->m_res_cd;  // 결과 코드
	$res_msg = $arr[res_msg] = $c_PayPlus->m_res_msg; // 결과 메시지
	/* ============================================================================== */
	// 에스크로 배송등록 인경우
	if ( $req_tx == "mod_escrow" ){
		$escrowLog = '';
		$escrowLog .= '=========================================='.chr(10);
		$escrowLog .= '주문번호 : '.$_POST['ordno'].chr(10);
		$escrowLog .= '거래번호 : '.$tno.chr(10);
		$escrowLog .= '결과코드 : '.$res_cd.chr(10);
		$escrowLog .= '결과내용 : '.$res_msg.chr(10);
		$escrowLog = '=========================================='.chr(10).'에스크로 배송등록 : ('.date('Y-m-d H:i:s').')'.chr(10).$escrowLog.'=========================================='.chr(10);

		if( $res_cd == '0000' ){
			$db->query("update ".GD_ORDER." set escrowconfirm=1, settlelog=concat(ifnull(settlelog,''),'$escrowLog') where ordno='$_POST[ordno]'");
		} else {
			$db->query("update ".GD_ORDER." set escrowconfirm=0, settlelog=concat(ifnull(settlelog,''),'$escrowLog') where ordno='$_POST[ordno]'");
		}
	}


	/* ============================================================================== */
	/* =   05. 승인 결과 처리													   = */
	/* = -------------------------------------------------------------------------- = */
	if ( $req_tx == "pay" )
	{
		$oData = $db->fetch("select step, vAccount from ".GD_ORDER." where ordno='$ordno'");
		if( ($oData['step'] > 0 || $oData['vAccount'] != '' || $res_cd=='8128') && $_POST[pay_method] != "SAVE") // 중복결제
		{
			### 로그 저장
			$db->query("update ".GD_ORDER." set settlelog=concat(ifnull(settlelog,''),'$settlelog') where ordno='$ordno'");
			$res = true;
		}
		else if( $res_cd == "0000" )
		{
			$tno	= $arr[tno] = $c_PayPlus->mf_get_res_data( "tno"	); // KCP 거래 고유 번호

			$amount = $arr[amount] =  $c_PayPlus->mf_get_res_data( "amount" ); // KCP 실제 거래 금액

	/* = -------------------------------------------------------------------------- = */
	/* =   05-1. 신용카드 승인 결과 처리											= */
	/* = -------------------------------------------------------------------------- = */
			if ( $use_pay_method == "100000000000" )
			{
				$card_cd = $arr[card_cd]  = $c_PayPlus->mf_get_res_data( "card_cd"   ); // 카드 코드
				$card_name = $arr[card_name] = $c_PayPlus->mf_get_res_data( "card_name" ); // 카드 종류
				$app_time = $arr[app_time]  = $c_PayPlus->mf_get_res_data( "app_time"  ); // 승인 시간
				$app_no = $arr[app_no]   = $c_PayPlus->mf_get_res_data( "app_no"	); // 승인 번호
				$noinf = $arr[noinf]	= $c_PayPlus->mf_get_res_data( "noinf"	 ); // 무이자 여부 ( 'Y' : 무이자 )
				$quota = $arr[quota]	= $c_PayPlus->mf_get_res_data( "quota"	 ); // 할부 개월

				/* = -------------------------------------------------------------- = */
				/* =   05-1.1. 복합결제(포인트+신용카드) 승인 결과 처리			   = */
				/* = -------------------------------------------------------------- = */
				if ( $epnt_issu == "SCSK" || $epnt_issu == "SCWB" )
				{
					$pnt_amount  = $arr[pnt_amount]   = $c_PayPlus->mf_get_res_data ( "pnt_amount"   );
					$pnt_app_time  = $arr[pnt_app_time] = $c_PayPlus->mf_get_res_data ( "pnt_app_time" );
					$pnt_app_no  = $arr[pnt_app_no]   = $c_PayPlus->mf_get_res_data ( "pnt_app_no"   );
					$add_pnt  = $arr[add_pnt]	 = $c_PayPlus->mf_get_res_data ( "add_pnt"	  );
					$use_pnt	= $arr[use_pnt]   = $c_PayPlus->mf_get_res_data ( "use_pnt"	  );
					$rsv_pnt	 = $arr[rsv_pnt]  = $c_PayPlus->mf_get_res_data ( "rsv_pnt"	  );
				}
			}

	/* = -------------------------------------------------------------------------- = */
	/* =   05-2. 계좌이체 승인 결과 처리											= */
	/* = -------------------------------------------------------------------------- = */
			if ( $use_pay_method == "010000000000" )
			{
				$bank_name = $arr[bank_name] = $c_PayPlus->mf_get_res_data( "bank_name"  );  // 은행명
				$bank_code = $arr[bank_code] = $c_PayPlus->mf_get_res_data( "bank_code"  );  // 은행코드
			}

	/* = -------------------------------------------------------------------------- = */
	/* =   05-3. 가상계좌 승인 결과 처리											= */
	/* = -------------------------------------------------------------------------- = */
			if ( $use_pay_method == "001000000000" )
			{
				$bankname = $arr[bankname]  = $c_PayPlus->mf_get_res_data( "bankname"  ); // 입금할 은행 이름
				$depositor = $arr[depositor] = $c_PayPlus->mf_get_res_data( "depositor" ); // 입금할 계좌 예금주
				$account = $arr[account]  = $c_PayPlus->mf_get_res_data( "account"   ); // 입금할 계좌 번호
			}

	/* = -------------------------------------------------------------------------- = */
	/* =   05-4. 포인트 승인 결과 처리											   = */
	/* = -------------------------------------------------------------------------- = */
			if ( $use_pay_method == "000100000000" )
			{
				$pnt_amount = $arr[pnt_amount]  = $c_PayPlus->mf_get_res_data( "pnt_amount"   );
				$pnt_app_time = $arr[pnt_app_time] = $c_PayPlus->mf_get_res_data( "pnt_app_time" );
				$pnt_app_no = $arr[pnt_app_no]   = $c_PayPlus->mf_get_res_data( "pnt_app_no"   );
				$add_pnt  = $arr[add_pnt]	= $c_PayPlus->mf_get_res_data( "add_pnt"	  );
				$use_pnt  = $arr[use_pnt]	= $c_PayPlus->mf_get_res_data( "use_pnt"	  );
				$rsv_pnt   = $arr[rsv_pnt]   = $c_PayPlus->mf_get_res_data( "rsv_pnt"	  );
			}

	/* = -------------------------------------------------------------------------- = */
	/* =   05-5. 휴대폰 승인 결과 처리											  = */
	/* = -------------------------------------------------------------------------- = */
			if ( $use_pay_method == "000010000000" )
			{
				$app_time = $arr[app_time] = $c_PayPlus->mf_get_res_data( "hp_app_time"  ); // 승인 시간
			}

	/* = -------------------------------------------------------------------------- = */
	/* =   05-6. 상품권 승인 결과 처리											  = */
	/* = -------------------------------------------------------------------------- = */
			if ( $use_pay_method == "000000001000" )
			{
				$app_time = $arr[app_time] = $c_PayPlus->mf_get_res_data( "tk_app_time"  ); // 승인 시간
			}

	/* = -------------------------------------------------------------------------- = */
	/* =   05-7. 티머니 승인 결과 처리											  = */
	/* = -------------------------------------------------------------------------- = */
			if ( $use_pay_method == "000000000100" )
			{
				$app_time = $arr[app_time] = $c_PayPlus->mf_get_res_data("app_time"	  ); // 승인시간
			}

	/* = -------------------------------------------------------------------------- = */
	/* =   05-8. ARS 승인 결과 처리												 = */
	/* = -------------------------------------------------------------------------- = */
			if ( $use_pay_method == "000000000010" )
			{
				$app_time = $arr[app_time] = $c_PayPlus->mf_get_res_data( "ars_app_time" ); // 승인 시간
			}

	/* = -------------------------------------------------------------------------- = */
	/* =   05-9. 현금영수증 결과 처리											   = */
	/* = -------------------------------------------------------------------------- = */
			if ( $cash_yn == "Y" )
			{
				$cash_authno = $arr[cash_authno]  = $c_PayPlus->mf_get_res_data( "cash_authno"  ); // 현금 영수증 승인 번호
			}
	/* = -------------------------------------------------------------------------- = */
	/* =   05-10. 승인 결과를 업체 자체적으로 DB 처리 작업하시는 부분입니다.		 = */
	/* = -------------------------------------------------------------------------- = */
	/* =		 승인 결과를 DB 작업 하는 과정에서 정상적으로 승인된 건에 대해	  = */
	/* =		 DB 작업을 실패하여 DB update 가 완료되지 않은 경우, 자동으로	   = */
	/* =		 승인 취소 요청을 하는 프로세스가 구성되어 있습니다.				= */
	/* =		 DB 작업이 실패 한 경우, bSucc 라는 변수(String)의 값을 "false"	 = */
	/* =		 로 세팅해 주시기 바랍니다. (DB 작업 성공의 경우에는 "false" 이외의 = */
	/* =		 값을 세팅하시면 됩니다.)										   = */
	/* =		 amount(KCP실제 거래금액)과 업체가 DB 처리하실 금액이 다를 경우의   = */
	/* =		 비교 루틴을 추가 하셔서 다를 경우 마찬가지로 "false"로 셋팅하여	= */
	/* =		 주시길 바랍니다.												   = */
	/* = -------------------------------------------------------------------------- = */
			if( $_POST[pay_method] == "SAVE" && $res_cd == "0000" ){

				$add_pnt = $r_cashbag['add_pnt'] = $c_PayPlus->mf_get_res_data("add_pnt");
				$use_pnt = $r_cashbag['use_pnt'] =  $c_PayPlus->mf_get_res_data("use_pnt");
				$rsv_pnt = $r_cashbag['rsv_pnt'] = $c_PayPlus->mf_get_res_data("rsv_pnt");
				$pnt_app_time = $r_cashbag['pnt_app_time'] = $c_PayPlus->mf_get_res_data("pnt_app_time");
				$pnt_amount = $r_cashbag['pnt_amount'] = $c_PayPlus->mf_get_res_data("pnt_amount");

				### 주문서의 캐쉬백 적립 여부 업데이트
				$query = "update ".GD_ORDER." set cbyn='Y' where ordno = '$ordno' and cbyn='N' and step='4' and step2 = '0'";
				$db -> query($query);

				### ok캐쉬백 적립로그
				$query = "insert into ".GD_ORDER_OKCASHBAG." set ordno='$ordno', tno = '$tno', add_pnt='$add_pnt', use_pnt='$use_pnt', rsv_pnt='$rsv_pnt', pnt_app_time='$pnt_app_time', pnt_amount='$pnt_amount'";
				$db -> query($query);

				msg('OK캐쉬백 적립금이 적립되었습니다.',0);
				echo("<script>parent.location.reload();</script>");
				exit;
			}



			$query = "
			select * from
				".GD_ORDER." a
				left join ".GD_LIST_BANK." b on a.bankAccount = b.sno
			where
				a.ordno='$ordno'
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
				if ($_SESSION[eggData][ordno] == $ordno && $_SESSION[eggData][resno1] != '' && $_SESSION[eggData][resno2] != '' && $_SESSION[eggData][agree] == 'Y'){
					include '../../../lib/egg.class.usafe.php';
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
						$res_cd = ''; $step = 51;
					}
					else if ( $eggCls->isErr == true && in_array($use_pay_method, array("100000000000","010000000000")) );
				}
				session_unregister('eggData');
			}

			### 가상계좌 결제의 재고 체크 단계 설정
			$res_cstock = true;
			if($cfg['stepStock'] == '1' && $use_pay_method=="001000000000") $res_cstock = false;

			### item check stock
			include "../../../lib/cardCancel.class.php";
			$cancel = new cardCancel();
			if(!$cancel->chk_item_stock($ordno) && $res_cstock){
				$step = 51; $qrc1 = $qrc2 = "";
			}

			if($step == 51) $cancel->cancel_db_proc($ordno,$tno);
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
				where ordno='$ordno'"
				);
				$res = $db->query("update ".GD_ORDER_ITEM." set $qrc2 istep='$step' where ordno='$ordno'");

				### 주문로그 저장
				orderLog($ordno,$r_step2[$data[step2]]." > ".$r_step[$step]);

				### 재고 처리
				setStock($ordno);

				### 상품구입시 적립금 사용
				if ($data[m_no] && $data[emoney]){
					setEmoney($data[m_no],-$data[emoney],"상품구입시 적립금 결제 사용",$ordno);
				}

				### 주문확인메일
				if(function_exists('getMailOrderData')){
					sendMailCase($data['email'],0,getMailOrderData($ordno));
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

		} // End of [res_cd = "0000"]

	/* = -------------------------------------------------------------------------- = */
	/* =   05-12. 승인 실패를 업체 자체적으로 DB 처리 작업하시는 부분입니다.		 = */
	/* = -------------------------------------------------------------------------- = */
		else
		{

			$arr = array_merge($_POST,$arr);
			$settlelog = settlelog($arr);

			if($_POST[pay_method] == "SAVE" ){
				msg('OK캐쉬백 적립실패되었습니다.',0);
			}else{
				$db->query("update ".GD_ORDER." set step2=54, settlelog=concat(ifnull(settlelog,''),'$settlelog'),cardtno='".$tno."' where ordno='$ordno'");
				$db->query("update ".GD_ORDER_ITEM." set istep=54 where ordno='$ordno'");
			}

			$res = false;

		}
	}
	/* ============================================================================== */


	/* ============================================================================== */
	/* =   06. 취소/매입 결과 처리												  = */
	/* = -------------------------------------------------------------------------- = */
	else if ( $req_tx == "mod" )
	{
	}
	if($cash_authno) $db-> query("update ".GD_ORDER." set cashreceipt='$cash_authno' where ordno='$ordno'"); //현금영수증이 발급되었을 경우 현금영수증 처리
	?>
	<script>
	var openwin = window.open( 'proc_win.html', 'proc_win', '' );
	openwin.close();
	</script>
	<?

	if($res && $req_tx == "pay")go("../../order_end.php?ordno=$ordno&card_nm=$card_name","parent");
	else if($req_tx == "pay" && !$res) {
		// Ncash 결제 승인 취소 API 호출
		if($naverNcash->useyn=='Y') $naverNcash->payment_approval_cancel($ordno);

		go("../../order_fail.php?ordno=$ordno","parent");
	}
	else if ( $req_tx == "mod" ){
		### 캐쉬백 적립취소
		$settlelog = chr(10). $tno. " 적립 취소 ".date('Y-m-d h:i:s',time());
		$query = "select ordno from  ".GD_ORDER_OKCASHBAG." where tno='$tno' limit 1";
		list($ordno) = $db -> fetch($query);
		$db->query("update ".GD_ORDER." set cbyn='N',settlelog=concat(ifnull(settlelog,''),'$settlelog') where ordno='$ordno'");
		$query = "delete from  ".GD_ORDER_OKCASHBAG." where tno='$tno'";
		$db -> query($query);

		echo("<script>alert('캐쉬백 적립이 취소 되었습니다.');opener.location.reload();self.close();</script>");
		exit;
	}
	?>
