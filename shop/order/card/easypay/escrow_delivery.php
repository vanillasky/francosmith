<?php

	include "../../../lib/library.php";
	include "../../../conf/config.php";
	include "../../../conf/pg.easypay.php";
    include "./inc/easypay_config.php";
    include "./easypay_client.php";


/* -------------------------------------------------------------------------- */
/* ::: 처리구분 설정                                                          */
/* -------------------------------------------------------------------------- */
$TRAN_CD_NOR_PAYMENT    = "00101000";   // 승인(일반, 에스크로)
$TRAN_CD_NOR_MGR        = "00201000";   // 변경(일반, 에스크로)

/* -------------------------------------------------------------------------- */
/* ::: 플러그인 응답정보 설정                                                 */
/* -------------------------------------------------------------------------- */
$tr_cd            = $_POST["EP_tr_cd"];            // [필수]요청구분
$trace_no         = $_POST["EP_trace_no"];         // [필수]추적고유번호
$sessionkey       = $_POST["EP_sessionkey"];       // [필수]암호화키
$encrypt_data     = $_POST["EP_encrypt_data"];     // [필수]암호화 데이타

$pay_type         = $_POST["EP_ret_pay_type"];     // 결제수단
$complex_yn       = $_POST["EP_ret_complex_yn"];   // 복합결제유무
$card_code        = $_POST["EP_card_code"];        // 카드코드

/* -------------------------------------------------------------------------- */
/* ::: 결제 주문 정보 설정                                                    */
/* -------------------------------------------------------------------------- */
$order_no         = $_POST["ordno"];				// [필수]주문번호
$user_type        = $_POST["EP_user_type"];        // [선택]사용자구분구분[1:일반,2:회원]
$memb_user_no     = $_POST["EP_memb_user_no"];     // [선택]가맹점 고객일련번호
$user_id          = $_POST["EP_user_id"];          // [선택]고객 ID
$user_nm          = $_POST["EP_user_nm"];          // [필수]고객명
$user_mail        = $_POST["EP_user_mail"];        // [필수]고객 E-mail
$user_phone1      = $_POST["EP_user_phone1"];      // [선택]가맹점 고객 전화번호
$user_phone2      = $_POST["EP_user_phone2"];      // [선택]가맹점 고객 휴대폰
$user_addr        = $_POST["EP_user_addr"];        // [선택]가맹점 고객 주소
$product_type     = $_POST["EP_product_type"];     // [선택]상품정보구분[0:실물,1:컨텐츠]
$product_nm       = $_POST["EP_product_nm"];       // [필수]상품명
$product_amt      = $_POST["EP_product_amt"];      // [필수]상품금액

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
$req_ip           = $_POST["req_ip"];              // [필수]요청자 IP
$req_id           = $_POST["req_id"];              // [선택]가맹점 관리자 로그인 아이디
$mgr_msg          = $_POST["mgr_msg"];             // [선택]변경 사유
/* -------------------------------------------------------------------------- */
/* ::: 결제 결과                                                              */
/* -------------------------------------------------------------------------- */
$res_cd     = "";
$res_msg    = "";

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

/* -------------------------------------------------------------------------- */
/* ::: IP 정보 설정                                                           */
/* -------------------------------------------------------------------------- */
$client_ip = $easyPay->get_remote_addr();    // [필수]결제고객 IP


if( $TRAN_CD_NOR_PAYMENT == $tr_cd )
{

	/* ---------------------------------------------------------------------- */
    /* ::: 승인요청(플러그인 암호화 전문 설정)                                */
    /* ---------------------------------------------------------------------- */
    $easyPay->set_trace_no($trace_no);
    $easyPay->set_snd_key($sessionkey);
    $easyPay->set_enc_data($encrypt_data);

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
    $easyPay->set_easypay_deli_us( $mgr_data, "deli_cd"    , $deli_cd     );		
    $easyPay->set_easypay_deli_us( $mgr_data, "req_ip"          , $client_ip        );
    $easyPay->set_easypay_deli_us( $mgr_data, "req_id"          , $req_id           );
    $easyPay->set_easypay_deli_us( $mgr_data, "mgr_msg"         , $mgr_msg          );
}

/* -------------------------------------------------------------------------- */
/* ::: 실행                                                                   */
/* -------------------------------------------------------------------------- */
$opt = "option value";
$easyPay->easypay_exec($g_mall_id, $tr_cd, $order_no, $client_ip, $opt);
$res_cd  = $easyPay->_easypay_resdata["res_cd"];    // 응답코드
$res_msg = $easyPay->_easypay_resdata["res_msg"];   // 응답메시지

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
$r_canc_acq_date   = $easyPay->_easypay_resdata[ "canc_acq_date"   ];    //매입취소일시
$r_canc_date       = $easyPay->_easypay_resdata[ "canc_date"       ];    //취소일시
$r_refund_date     = $easyPay->_easypay_resdata[ "refund_date"     ];    //환불예정일시

/* -------------------------------------------------------------------------- */
/* ::: 가맹점 DB 처리                                                         */
/* -------------------------------------------------------------------------- */
/* 응답코드(res_cd)가 "0000" 이면 정상승인 입니다.                            */
/* r_amount가 주문DB의 금액과 다를 시 반드시 취소 요청을 하시기 바랍니다.     */
/* DB 처리 실패 시 취소 처리를 해주시기 바랍니다.                             */
/* -------------------------------------------------------------------------- */


//////////////////////////// 승인 Start ////////////////////////////
//--- 로그 생성
$settlelog	= '';
$settlelog	.= '===================================================='.chr(10);
$settlelog	.= '주문번호 : '.$order_no.chr(10);
$settlelog	.= '거래번호 : '.$r_cno.chr(10)  ;
$settlelog	.= '결과코드 : '.$res_cd.chr(10);
$settlelog	.= '결과내용 : '.$res_msg.chr(10);
$settlelog	.= '처리날짜 : '.$r_tran_date.chr(10);
$settlelog	.= '처리자IP : '.$_SERVER['REMOTE_ADDR'].chr(10);
 
	 
if ( $res_cd == "0000" )	// 결제 성공
{
	$getPgResult		= true;
	$settlelog	= '===================================================='.chr(10).'에스크로 배송등록 : 처리완료시간('.date('Y-m-d H:i:s').')'.chr(10).$settlelog.'===================================================='.chr(10);
}else{	// 결제 실패

	$settlelog	= '===================================================='.chr(10).'에스크로 배송등록 : 실패확인시간('.date('Y-m-d H:i:s').')'.chr(10).$settlelog.'===================================================='.chr(10);

	// PG 결과
	$getPgResult		= false;
}

 
//////////////////////////// 승인 End ////////////////////////////


//--- 성공시 디비 처리
if( $getPgResult === true ){
	// 실데이타 저장
	$db->query("
	UPDATE ".GD_ORDER." SET
		escrowconfirm	= 1,
		settlelog		= concat(ifnull(settlelog,''),'$settlelog')
	WHERE ordno='$order_no'"
	);
} else {
	// 실데이타 저장
	$db->query("
	UPDATE ".GD_ORDER." SET
		settlelog		= concat(ifnull(settlelog,''),'$settlelog')
	WHERE ordno='$order_no'"
	);
}
msg($res_msg);
exit;
?>