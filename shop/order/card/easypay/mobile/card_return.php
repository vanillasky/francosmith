<?php
/**
 * 이니시스 PG 모듈 페이지
 * 이니시스 PG 버전 : INIpayMobile Web (V 2.4 - 20110725)
 */

include dirname(__FILE__)."/../../../../lib/library.php";
include dirname(__FILE__)."/../../../../conf/config.mobileShop.php";
 




$shopdir = dirname(__FILE__).'/../../../..';
include($shopdir.'/conf/config.php'); 
include($shopdir.'/conf/pg.easypay.php'); 
require_once($shopdir.'/order/card/easypay/inc/easypay_config.php');
require_once($shopdir.'/order/card/easypay/easypay_client.php');

// PG결제 위변조 체크 및 유효성 체크
if (forge_order_check($_POST["order_no"],$_POST['pay_mny']) === false) {
	msg('주문 정보와 결제 정보가 맞질 않습니다. 다시 결제 바랍니다.',$cfgMobileShop['mobileShopRootDir'].'/ord/order_fail.php?ordno='.$_POST["order_no"],'parent');
	exit();
}

// 네이버 마일리지 결제 승인 API
include dirname(__FILE__)."/../../../../lib/naverNcash.class.php";
$naverNcash = new naverNcash();
if ($naverNcash->useyn == 'Y') {
	if ($_POST['pay_type'] == '22') $ncashResult = $naverNcash->payment_approval($_POST['order_no'], false);
	else $ncashResult = $naverNcash->payment_approval($_POST['order_no'], true);
	if ($ncashResult === false) {
		msg('네이버 마일리지 사용에 실패하였습니다.', $cfgMobileShop['mobileShopRootDir'].'/ord/order_fail.php?ordno='.$_POST["order_no"],'parent');
		exit();
	}
}

/* -------------------------------------------------------------------------- */
/* ::: 처리구분 설정                                                          */
/* -------------------------------------------------------------------------- */
$TRAN_CD_NOR_PAYMENT    = "00101000";   // 승인(일반, 에스크로)
$TRAN_CD_NOR_MGR        = "00201000";   // 변경(일반, 에스크로)

/* -------------------------------------------------------------------------- */
/* ::: 처리 응답정보 설정                                                     */
/* -------------------------------------------------------------------------- */
$res_cd           = $_POST["res_cd"];              // [필수]요청구분
$res_msg          = $_POST["res_msg"];             // [필수]요청구분

$tr_cd            = $_POST["tr_cd"];               // [필수]요청구분
$sessionkey       = $_POST["sessionkey"];          // [필수]암호화키
$encrypt_data     = $_POST["encrypt_data"];        // [필수]암호화 데이타


$mall_id          = $_POST["mall_id"];             // [필수]가맹점 ID
$order_no         = $_POST["order_no"];            // [필수]주문번호

$pay_type         = $_POST["pay_type"];            // [필수]결제수단

$client_ip         = $_POST["client_ip"];            // [필수]고객IP


/* -------------------------------------------------------------------------- */
/* ::: 가맹점 예약필드 응답정보 설정                                          */
/* -------------------------------------------------------------------------- */
$mobilereserved1 = $_POST["mobilereserved1"];      // [선택]예약필드
$mobilereserved2 = $_POST["mobilereserved2"];      // [선택]예약필드
$reserved1       = $_POST["reserved1"];            // [선택]예약필드
$reserved2       = $_POST["reserved2"];            // [선택]예약필드
$reserved3       = $_POST["reserved3"];            // [선택]예약필드
$reserved4       = $_POST["reserved4"];            // [선택]예약필드

/* -------------------------------------------------------------------------- */
/* ::: 변경관리 정보 설정                                                     */
/* -------------------------------------------------------------------------- */
$mgr_txtype       = $_POST["mgr_txtype"];          // [필수]거래구분
$mgr_subtype      = $_POST["mgr_subtype"];         // [선택]변경세부구분
$org_cno          = $_POST["org_cno"];             // [필수]원거래고유번호
$mgr_amt          = $_POST["mgr_amt"];             // [선택]부분취소/환불요청 금액
$mgr_bank_cd      = $_POST["mgr_bank_cd"];         // [선택]환불계좌 은행코드
$mgr_account      = $_POST["mgr_account"];         // [선택]환불계좌 번호
$mgr_depositor    = $_POST["mgr_depositor"];       // [선택]환불계좌 예금주명
$mgr_socno        = $_POST["mgr_socno"];           // [선택]환불계좌 주민번호
$mgr_telno        = $_POST["mgr_telno"];           // [선택]환불고객 연락처
$deli_cd          = $_POST["deli_cd"];             // [선택]배송구분[자가:DE01,택배:DE02]
$deli_corp_cd     = $_POST["deli_corp_cd"];        // [선택]택배사코드
$deli_invoice     = $_POST["deli_invoice"];        // [선택]운송장 번호
$deli_rcv_nm      = $_POST["deli_rcv_nm"];         // [선택]수령인 이름
$deli_rcv_tel     = $_POST["deli_rcv_tel"];        // [선택]수령인 연락처
$req_id           = $_POST["req_id"];              // [선택]가맹점 관리자 로그인 아이디
$mgr_msg          = $_POST["mgr_msg"];             // [선택]변경 사유

/* -------------------------------------------------------------------------- */
/* ::: EasyPayClient 인스턴스 생성 [변경불가 !!].                             */
/* -------------------------------------------------------------------------- */
$easyPay = new EasyPay_Client;         // 전문처리용 Class (library에서 정의됨)
$easyPay->clearup_msg();

$easyPay->set_home_dir($g_home_dir);
$easyPay->set_gw_url($g_gw_url);
$easyPay->set_gw_port($g_gw_port);
$easyPay->set_log_dir($g_log_dir);
$easyPay->set_log_level($g_log_level);
$easyPay->set_cert_file($g_cert_file);

if( $TRAN_CD_NOR_PAYMENT == $tr_cd )
{
	
	/* ---------------------------------------------------------------------- */
    /* ::: 승인요청(암호화 전문 설정)                                         */
    /* ---------------------------------------------------------------------- */
    /* ---------------------------------------------------------------------- */
    /* ::: 승인요청 전 응답코드, 주문번호로, MALL_ID로 거래확인 필수          */
    /* ---------------------------------------------------------------------- */ 
	if( $res_cd == "0000" ) {    
        //$easyPay->set_trace_no($trace_no);
        $easyPay->set_snd_key($sessionkey);
        $easyPay->set_enc_data($encrypt_data);	


    }
	
}
else if( $TRAN_CD_NOR_MGR == $tr_cd )
{
    /* ---------------------------------------------------------------------- */
    /* ::: 변경관리 요청                                                      */
    /* ---------------------------------------------------------------------- */
    $mgr_data = $easyPay->set_easypay_item("mgr_data");    
    $easyPay->set_easypay_deli_us( $mgr_data, "mgr_txtype"      , $mgr_txtype       );
    $easyPay->set_easypay_deli_us( $mgr_data, "mgr_subtype"     , $mgr_subtype      );
    $easyPay->set_easypay_deli_us( $mgr_data, "org_cno"         , $org_cno          );
    $easyPay->set_easypay_deli_us( $mgr_data, "pay_type"        , $pay_type         );
    $easyPay->set_easypay_deli_us( $mgr_data, "mgr_amt"         , $mgr_amt          );
    $easyPay->set_easypay_deli_us( $mgr_data, "mgr_bank_cd"     , $mgr_bank_cd      );
    $easyPay->set_easypay_deli_us( $mgr_data, "mgr_account"     , $mgr_account      );
    $easyPay->set_easypay_deli_us( $mgr_data, "mgr_depositor"   , $mgr_depositor    );
    $easyPay->set_easypay_deli_us( $mgr_data, "mgr_socno"       , $mgr_socno        );
    $easyPay->set_easypay_deli_us( $mgr_data, "mgr_telno"       , $mgr_telno        );
    $easyPay->set_easypay_deli_us( $mgr_data, "deli_corp_cd"    , $deli_corp_cd     );
    $easyPay->set_easypay_deli_us( $mgr_data, "deli_invoice"    , $deli_invoice     );
    $easyPay->set_easypay_deli_us( $mgr_data, "deli_rcv_nm"     , $deli_rcv_nm      );
    $easyPay->set_easypay_deli_us( $mgr_data, "deli_rcv_tel"    , $deli_rcv_tel     );
    $easyPay->set_easypay_deli_us( $mgr_data, "req_ip"          , $client_ip        );
    $easyPay->set_easypay_deli_us( $mgr_data, "req_id"          , $req_id           );
    $easyPay->set_easypay_deli_us( $mgr_data, "mgr_msg"         , $mgr_msg          );
    
    /* PG Client 실행을 위해서 설정 */
    $res_cd = "0000";
}

/* -------------------------------------------------------------------------- */
/* ::: 실행                                                                   */
/* -------------------------------------------------------------------------- */ 
/* 인증응답이 '0000'이 아니면 오류처리 */
if ( $res_cd == "0000" ) {    
	$opt = "option value";
    $easyPay->easypay_exec($g_mall_id, $tr_cd, $order_no, $client_ip, $opt);
    
    $res_cd  = $easyPay->_easypay_resdata["res_cd"];    // 응답코드
    $res_msg = $easyPay->_easypay_resdata["res_msg"];   // 응답메시지
}

/* -------------------------------------------------------------------------- */
/* ::: 결과 처리                                                              */
/* -------------------------------------------------------------------------- */
$r_cno             = $easyPay->_easypay_resdata[ "cno"             ];    // PG거래번호 
$r_amount          = $easyPay->_easypay_resdata[ "amount"          ];    //총 결제금액
$r_auth_no         = $easyPay->_easypay_resdata[ "auth_no"         ];    //승인번호
$r_tran_date       = $easyPay->_easypay_resdata[ "tran_date"       ];    //승인일시
$r_pnt_auth_no     = $easyPay->_easypay_resdata[ "pnt_auth_no"     ];    //포인트승인번호
$r_pnt_tran_date   = $easyPay->_easypay_resdata[ "pnt_tran_date"   ];    //포인트승인일시
$r_cpon_auth_no    = $easyPay->_easypay_resdata[ "cpon_auth_no"    ];    //쿠폰승인번호
$r_cpon_tran_date  = $easyPay->_easypay_resdata[ "cpon_tran_date"  ];    //쿠폰승인일시
$r_card_no         = $easyPay->_easypay_resdata[ "card_no"         ];    //카드번호
$r_issuer_cd       = $easyPay->_easypay_resdata[ "issuer_cd"       ];    //발급사코드
$r_issuer_nm       = $easyPay->_easypay_resdata[ "issuer_nm"       ];    //발급사명
$r_acquirer_cd     = $easyPay->_easypay_resdata[ "acquirer_cd"     ];    //매입사코드
$r_acquirer_nm     = $easyPay->_easypay_resdata[ "acquirer_nm"     ];    //매입사명
$r_install_period  = $easyPay->_easypay_resdata[ "install_period"  ];    //할부개월
$r_noint           = $easyPay->_easypay_resdata[ "noint"           ];    //무이자여부
$r_bank_cd         = $easyPay->_easypay_resdata[ "bank_cd"         ];    //은행코드
$r_bank_nm         = $easyPay->_easypay_resdata[ "bank_nm"         ];    //은행명
$r_account_no      = $easyPay->_easypay_resdata[ "account_no"      ];    //계좌번호
$r_deposit_nm      = $easyPay->_easypay_resdata[ "deposit_nm"      ];    //입금자명
$r_expire_date     = $easyPay->_easypay_resdata[ "expire_date"     ];    //계좌사용만료일
$r_cash_res_cd     = $easyPay->_easypay_resdata[ "cash_res_cd"     ];    //현금영수증 결과코드
$r_cash_res_msg    = $easyPay->_easypay_resdata[ "cash_res_msg"    ];    //현금영수증 결과메세지
$r_cash_auth_no    = $easyPay->_easypay_resdata[ "cash_auth_no"    ];    //현금영수증 승인번호
$r_cash_tran_date  = $easyPay->_easypay_resdata[ "cash_tran_date"  ];    //현금영수증 승인일시
$r_auth_id         = $easyPay->_easypay_resdata[ "auth_id"         ];    //PhoneID
$r_billid          = $easyPay->_easypay_resdata[ "billid"          ];    //인증번호
$r_mobile_no       = $easyPay->_easypay_resdata[ "mobile_no"       ];    //휴대폰번호
$r_ars_no          = $easyPay->_easypay_resdata[ "ars_no"          ];    //전화번호
$r_cp_cd           = $easyPay->_easypay_resdata[ "cp_cd"           ];    //포인트사/쿠폰사
$r_used_pnt        = $easyPay->_easypay_resdata[ "used_pnt"        ];    //사용포인트
$r_remain_pnt      = $easyPay->_easypay_resdata[ "remain_pnt"      ];    //잔여한도
$r_pay_pnt         = $easyPay->_easypay_resdata[ "pay_pnt"         ];    //할인/발생포인트
$r_accrue_pnt      = $easyPay->_easypay_resdata[ "accrue_pnt"      ];    //누적포인트
$r_remain_cpon     = $easyPay->_easypay_resdata[ "remain_cpon"     ];    //쿠폰잔액
$r_used_cpon       = $easyPay->_easypay_resdata[ "used_cpon"       ];    //쿠폰 사용금액
$r_mall_nm         = $easyPay->_easypay_resdata[ "mall_nm"         ];    //제휴사명칭
$r_escrow_yn       = $easyPay->_easypay_resdata[ "escrow_yn"       ];    //에스크로 사용유무
$r_complex_yn      = $easyPay->_easypay_resdata[ "complex_yn"      ];    //복합결제 유무
$r_canc_acq_data   = $easyPay->_easypay_resdata[ "canc_acq_data"   ];    //매입취소일시
$r_canc_date       = $easyPay->_easypay_resdata[ "canc_date"       ];    //취소일시
$r_refund_date     = $easyPay->_easypay_resdata[ "refund_date"     ];    //환불예정일시    

$oData = $db->fetch("select step, vAccount from ".GD_ORDER." where ordno='$order_no'");
if($oData['step'] > 0 || $oData['vAccount'] != '' ){
	$settlelog = PHP_EOL."$order_no (".date('Y:m:d H:i:s').")
	----------------------------------------
	중복승인요청
	----------------------------------------
	응답코드 : $res_cd
	PG거래번호 : $r_cno
	----------------------------------------";
	$db->query("update ".GD_ORDER." set settlelog=concat(ifnull(settlelog,''),'$settlelog') where ordno='$order_no'");
	go($cfgMobileShop['mobileShopRootDir']."/ord/order_end.php?ordno=$order_no&card_nm=".$r_issuer_nm,"parent");
	exit();
}

/* -------------------------------------------------------------------------- */
/* ::: 가맹점 DB 처리                                                         */
/* -------------------------------------------------------------------------- */
/* 응답코드(res_cd)가 "0000" 이면 정상승인 입니다.                            */
/* r_amount가 주문DB의 금액과 다를 시 반드시 취소 요청을 하시기 바랍니다.     */
/* DB 처리 실패 시 취소 처리를 해주시기 바랍니다.                             */
/* -------------------------------------------------------------------------- */
if ( $TRAN_CD_NOR_PAYMENT == $tr_cd && $res_cd == "0000" )
{

		### 로그

		switch($pay_type){
			case "11" : // 신용카드
				$settlelogAdd = "
	카드번호 : $r_card_no
	발 급 사 : [$r_issuer_cd] $r_issuer_nm
	매 입 사 : [$r_acquirer_cd] $r_acquirer_nm
	할부개월 : $r_install_period
	무이자여부 : $r_noint
	";
				break;
			case "21" : // 계좌이체
			$settlelogAdd = "
	은행정보 : [$r_bank_cd] $r_bank_nm
	현금영수증 결과 코드 : $r_cash_res_cd
	현금영수증 결과 메시지 : $r_cash_res_msg
	현금영수증 승인번호 : $r_cash_auth_no
	";
				break;
			case "22" : // 무통장입금(가상계좌)
			$settlelogAdd = "
	은행정보 : [$r_bank_cd] $r_bank_nm
	계좌번호 : $r_account_no
	계좌사용 만료일 : ".date('Y-m-d G:i:s',strtotime($r_expire_date))."
	";
				break;
			case "31" :	// 휴대폰
			$settlelogAdd = "
	휴대폰 인증 ID : $r_auth_id
	휴대폰 인증번호 : $r_billid
	휴대폰 번호 : $r_mobile_no
	";
				break;
		}

	$settlelog = "$order_no (".date('Y:m:d H:i:s').")
	----------------------------------------
	응답코드 : $res_cd
	응답메시지 : $res_msg
	PG거래번호 : $r_cno
	총 결제금액 : $r_amount
	승인번호 : $r_auth_no
	승인일시 : $r_tran_date
	에스크로 사용유무 : $r_escrow_yn
	----------------------------------------";

	$settlelog .= $settlelogAdd."----------------------------------------";



	$card_nm=$r_issuer_nm;
	### 가상계좌 결제의 재고 체크 단계 설정
	$res_cstock = true;
	if($cfg['stepStock'] == '1' && $pay_type=="22") $res_cstock = false;

	### item check stock
	include "../../../../lib/cardCancel.class.php";
	$cancel = new cardCancel();
	if(!$cancel->chk_item_stock($order_no) && $res_cstock){
		$step = 51; $qrc1 = $qrc2 = "";
	}

	$query = "
		select * from
			".GD_ORDER." a
			left join ".GD_LIST_BANK." b on a.bankAccount = b.sno
		where
			a.ordno='$order_no'
		";
		$data = $db->fetch($query);

		### 에스크로 여부 확인
		$escrowyn = ($r_escrow_yn=="Y") ? "y" : "n";
		if($escrowyn == 'y')$escrowno = $r_cno; // <- 확인해봐야함.

		### 결제 정보 저장
		$step = 1;
		$qrc1 = "cyn='y', cdt=now(),";
		$qrc2 = "cyn='y',";

		### 가상계좌 결제시 계좌정보 저장
		if ($pay_type=="22"){
			$vAccount = $r_bank_nm." ".$r_account_no." ".$r_deposit_nm;
			$step = 0; $qrc1 = $qrc2 = "";
		}

		### 현금영수증 저장
		if ($r_cash_res_cd == "0000"){
			$qrc1 .= "cashreceipt='{$r_cash_auth_no}',";
		}

		### 실데이타 저장
		$db->query("
		update ".GD_ORDER." set $qrc1
			step		= '$step',
			step2		= '',
			escrowyn	= '$escrowyn',
			escrowno	= '$escrowno',
			vAccount	= '$vAccount',
			settlelog	= concat(ifnull(settlelog,''),'$settlelog'),
			cardtno		= '".$r_cno."'
		where ordno='$order_no'"
		);
		$res = $db->query("update ".GD_ORDER_ITEM." set $qrc2 istep='$step' where ordno='$order_no'");

		### 주문로그 저장
		orderLog($order_no,$r_step2[$data[step2]]." > ".$r_step[$step]);

		### 재고 처리
		setStock($order_no);

		### 상품구입시 적립금 사용
		if ($data[m_no] && $data[emoney]){
			setEmoney($data[m_no],-$data[emoney],"상품구입시 적립금 결제 사용",$order_no);
		}

		### 주문확인메일
		if(function_exists('getMailOrderData')){
			sendMailCase($data['email'],0,getMailOrderData($order_no));
		}

		### SMS 변수 설정
		$dataSms = $data;

		if ($pay_type!="22"){
			sendMailCase($data[email],1,$data);			### 입금확인메일
			sendSmsCase('incash',$data[mobileOrder]);	### 입금확인SMS
		} else {
			sendSmsCase('order',$data[mobileOrder]);	### 주문확인SMS
		}

	if($res && $step != 51) {
		$bDBProc = "true"; // DB 작업 실패 또는 금액 불일치의 경우 "false" 로 세팅
		$res = true;
	}else{
		$bDBProc = "false";
		$res = false;
	}
    $bDBProc = "true";     // DB처리 성공 시 "true", 실패 시 "false"
    if ( $bDBProc != "true" )
    {
        $easyPay->clearup_msg();
    
        $tr_cd = $TRAN_CD_NOR_MGR; 
        $mgr_data = $easyPay->set_easypay_item("mgr_data");
        if ( $r_escrow_yn != "Y" )    
        {
            $easyPay->set_easypay_deli_us( $mgr_data, "mgr_txtype"      , "40"   );
        }
        else
        {
            $easyPay->set_easypay_deli_us( $mgr_data, "mgr_txtype"      , "61"   );
            $easyPay->set_easypay_deli_us( $mgr_data, "mgr_subtype"     , "ES02" );
        }
        $easyPay->set_easypay_deli_us( $mgr_data, "org_cno"         , $r_cno     );
        $easyPay->set_easypay_deli_us( $mgr_data, "req_ip"          , $client_ip );
        $easyPay->set_easypay_deli_us( $mgr_data, "req_id"          , "MALL_R_TRANS" );
        $easyPay->set_easypay_deli_us( $mgr_data, "mgr_msg"         , "DB 처리 실패로 망취소"  );
    
        $easyPay->easypay_exec($g_mall_id, $tr_cd, $order_no, $client_ip, $opt);
        $res_cd      = $easyPay->_easypay_resdata["res_cd"     ];    // 응답코드
        $res_msg     = $easyPay->_easypay_resdata["res_msg"    ];    // 응답메시지
        $r_cno       = $easyPay->_easypay_resdata["cno"        ];    // PG거래번호 
        $r_canc_date = $easyPay->_easypay_resdata["canc_date"  ];    // 취소일시

				$settlelog = "";

				$settlelog = "$order_no (".date('Y:m:d H:i:s').")
	----------------------------------------
	승인요청실패(DB처리실패)
	----------------------------------------
	응답코드 : $res_cd
	응답메시지 : $res_msg
	PG거래번호 : $r_cno
	취소일시 : $r_canc_date
	----------------------------------------";

				$db->query("update ".GD_ORDER." set step2='54', settlelog=concat(ifnull(settlelog,''),'".$settlelog."') where ordno='".$order_no."'");
				$db->query("update ".GD_ORDER_ITEM." set istep='54' where ordno='".$order_no."'");

				// Ncash 결제 승인 취소 API 호출
				if ($naverNcash->useyn == 'Y') $naverNcash->payment_approval_cancel($order_no);

				go($cfgMobileShop['mobileShopRootDir']."/ord/order_fail.php?ordno=$order_no","parent");
	}else{
		go($cfgMobileShop['mobileShopRootDir']."/ord/order_end.php?ordno=$order_no&card_nm=$card_nm","parent"); 
		} 
    }else{
		if ($step == '51') {
			$cancel->cancel_db_proc($order_no);
		} else {
			$db->query("update ".GD_ORDER." set step2='54', settlelog=concat(ifnull(settlelog,''),'".$settlelog."') where ordno='".$order_no."'");
			$db->query("update ".GD_ORDER_ITEM." set istep='54' where ordno='".$order_no."'");
		}

		// Ncash 결제 승인 취소 API 호출
		if ($naverNcash->useyn == 'Y') $naverNcash->payment_approval_cancel($order_no);

		go($cfgMobileShop['mobileShopRootDir']."/ord/order_fail.php?ordno=$order_no","parent");
	}

 ?>