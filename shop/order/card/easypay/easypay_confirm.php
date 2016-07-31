<?php
include "../../../lib/library.php";
include "../../../conf/config.php";

/* -------------------------------------------------------------------------- */
/* ::: 노티수신                                                               */
/* -------------------------------------------------------------------------- */
$result_msg = "";

$r_res_cd         = $_POST[ "res_cd"         ];  // 응답코드
$r_res_msg        = $_POST[ "res_msg"        ];  // 응답 메시지
$r_cno            = $_POST[ "cno"            ];  // PG거래번호
$r_memb_id        = $_POST[ "memb_id"        ];  // 가맹점 ID
$r_amount         = $_POST[ "amount"         ];  // 총 결제금액
$r_order_no       = $_POST[ "order_no"       ];  // 주문번호
$r_noti_type      = $_POST[ "noti_type"      ];  // 노티구분 변경(20), 입금(30), 에스크로 변경(40)
$r_auth_no        = $_POST[ "auth_no"        ];  // 승인번호
$r_tran_date      = $_POST[ "tran_date"      ];  // 승인일시
$r_card_no        = $_POST[ "card_no"        ];  // 카드번호
$r_issuer_cd      = $_POST[ "issuer_cd"      ];  // 발급사코드
$r_issuer_nm      = $_POST[ "issuer_nm"      ];  // 발급사명
$r_acquirer_cd    = $_POST[ "acquirer_cd"    ];  // 매입사코드
$r_acquirer_nm    = $_POST[ "acquirer_nm"    ];  // 매입사명
$r_install_period = $_POST[ "install_period" ];  // 할부개월
$r_noint          = $_POST[ "noint"          ];  // 무이자여부
$r_bank_cd        = $_POST[ "bank_cd"        ];  // 은행코드
$r_bank_nm        = $_POST[ "bank_nm"        ];  // 은행명
$r_account_no     = $_POST[ "account_no"     ];  // 계좌번호
$r_deposit_nm     = $_POST[ "deposit_nm"     ];  // 입금자명
$r_expire_date    = $_POST[ "expire_date"    ];  // 계좌사용만료일
$r_cash_res_cd    = $_POST[ "cash_res_cd"    ];  // 현금영수증 결과코드
$r_cash_res_msg   = $_POST[ "cash_res_msg"   ];  // 현금영수증 결과메시지
$r_cash_auth_no   = $_POST[ "cash_auth_no"   ];  // 현금영수증 승인번호
$r_cash_tran_date = $_POST[ "cash_tran_date" ];  // 현금영수증 승인일시
$r_cp_cd          = $_POST[ "cp_cd"          ];  // 포인트사
$r_used_pnt       = $_POST[ "used_pnt"       ];  // 사용포인트
$r_remain_pnt     = $_POST[ "remain_pnt"     ];  // 잔여한도
$r_pay_pnt        = $_POST[ "pay_pnt"        ];  // 할인/발생포인트 
$r_accrue_pnt     = $_POST[ "accrue_pnt"     ];  // 누적포인트
$r_escrow_yn      = $_POST[ "escrow_yn"      ];  // 에스크로 사용유무
$r_canc_date      = $_POST[ "canc_date"      ];  // 취소일시
$r_canc_acq_date  = $_POST[ "canc_acq_date"  ];  // 매입취소일시
$r_refund_date    = $_POST[ "refund_date"    ];  // 환불예정일시
$r_pay_type       = $_POST[ "pay_type"       ];  // 결제수단
$r_auth_cno       = $_POST[ "auth_cno"       ];  // 인증거래번호

/* -------------------------------------------------------------------------- */
/* ::: 노티수신 - 에스크로 상태변경                                           */
/* -------------------------------------------------------------------------- */
$r_escrow_yn      = $_POST[ "escrow_yn "     ];  // 에스크로유무
$r_stat_cd        = $_POST[ "stat_cd "       ];  // 변경에스크로상태코드	
$r_stat_msg       = $_POST[ "stat_msg"       ];  // 변경에스크로상태메세지    
/*ES01	승인	ES02	승인취소	ES03	입금대기
ES04	입금완료	ES05	환불요청	ES06	환불완료
ES07	배송중	ES08	베송중 취소요청	ES09	배송중 취소완료
ES10	배송중 환불요청	ES11	배송중 환불완료	ES12	구매확인
ES13	구매거절				
*/
 
if ( $r_res_cd == "0000" )    
{
	
	if($r_noti_type=='30') {		//입금
			//gd_order update step=1 
	
			//--- 로그 생성
			$settlelog	= '===================================================='.chr(10);
			$settlelog	.= '가상계좌 입금 자동 확인 : 성공 ('.date('Y-m-d H:i:s').')'.chr(10);
			$settlelog	.= '===================================================='.chr(10);
			$settlelog	.= '주문번호 : '.$r_order_no.chr(10);
			$settlelog	.= '거래번호 : '.$r_cno.chr(10); 
			$settlelog	.= '입금금액 : '.number_format($r_amount).chr(10);
			
				// 현금영수증 결과 정보
			if (empty($r_cash_auth_no) === false ) {
				$settlelog	.= '결과내용 : 가상계좌 자동입금 확인에 의한 처리'.chr(10);
				$settlelog	.= '현금영수증 발급번호 : '.$r_cash_auth_no.chr(10);
				$settlelog	.= '현금영수증 승인일시 : '.$cash_tran_date.chr(10);

				$qrc1	= "cashreceipt='".$r_cash_auth_no."',";
			}
			$settlelog	.= '===================================================='.chr(10);

			// 주문 번호
			$ordno	= $r_order_no;

			// 주문 정보
			$query = "
			SELECT * FROM
				".GD_ORDER." a
				LEFT JOIN ".GD_LIST_BANK." b on a.bankAccount = b.sno
			WHERE
				a.ordno='$ordno'
			";
			$data = $db->fetch($query);
			$sql="UPDATE ".GD_ORDER." SET ".$qrc1." cyn='y', cdt=now(),
				step		= '1',
				step2		= '',
				cardtno		= '$r_cno',
				settlelog	= concat(settlelog,'".$settlelog."')
			WHERE ordno='$ordno'";

			### 실데이타 저장
			$db->query("
			UPDATE ".GD_ORDER." SET ".$qrc1." cyn='y', cdt=now(),
				step		= '1',
				step2		= '',
				cardtno		= '$r_cno',
				settlelog	= concat(settlelog,'".$settlelog."')
			WHERE ordno='$ordno'"
			);
			echo $sql;
			exit;
			$db->query("update ".GD_ORDER_ITEM." set cyn='y', istep='1' where ordno='$ordno'");

			### 주문로그 저장
			orderLog($ordno,$r_step2[$data[step2]]." > ".$r_step[$step]);

			### 재고 처리
			setStock($ordno);

			### 입금확인메일
			sendMailCase($data[email],1,$data);

			### 입금확인SMS
			$dataSms = $data;
			sendSmsCase('incash',$data[mobileOrder]);

			### Ncash 거래 확정 API
			include "../../../lib/naverNcash.class.php";
			$naverNcash = new naverNcash();
			$naverNcash->deal_done($ordno);



			
	}




	

/* ---------------------------------------------------------------------- */
/* ::: 가맹점 DB 처리                                                     */
/* ---------------------------------------------------------------------- */
/* DB처리 성공 시 : res_cd=0000, 실패 시 : res_cd=5001                    */
/* ---------------------------------------------------------------------- */
	$result_msg = "res_cd=0000" . chr(31). "res_msg=SUCCESS";
	 	
}	
else
{	
	$result_msg = "res_cd=5001". chr(31) . "res_msg=FAIL";
}

/* -------------------------------------------------------------------------- */
/* ::: 노티 처리결과 처리                                                     */
/* -------------------------------------------------------------------------- */
echo $result_msg;

?>