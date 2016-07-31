<?php
/* -------------------------------------------------------------------------- */
/* ::: 이지페이 노티수신                                                               */
/* -------------------------------------------------------------------------- */
$TEMP_IP	= getenv('REMOTE_ADDR');
$PG_IP		= substr($TEMP_IP, 0, 10);
//--- PG에서 보냈는지 IP로 체크
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
	/* r_stat_cd 상태 코드표
	ES01	승인	ES02	승인취소	ES03	입금대기
	ES04	입금완료	ES05	환불요청	ES06	환불완료
	ES07	배송중	ES08	베송중 취소요청	ES09	배송중 취소완료
	ES10	배송중 환불요청	ES11	배송중 환불완료	ES12	구매확인
	ES13	구매거절				
	*/

	//--- 이지페이 경로
$easypayHome	= dirname($_SERVER['SCRIPT_FILENAME']);      // 이니페이 홈디렉터리
$logfile		= fopen( $easypayHome . '/log/easypay_noti_log_'.date('Ymd').'.log', 'a+' );

// 로그 저장 
$logInfo	 = '------------------------------------------------------------------------------'.chr(10);
$logInfo	.= 'INFO	['.date('Y-m-d H:i:s').']	START Order log'.chr(10);
foreach ($_POST as $key => $val) {
	$logInfo	.= 'DEBUG	['.date('Y-m-d H:i:s').']	'.$key.'	: '.$val.chr(10);
}
$logInfo	.= 'DEBUG	['.date('Y-m-d H:i:s').']	IP	: '.$_SERVER['REMOTE_ADDR'].chr(10);
$logInfo	.= 'INFO	['.date('Y-m-d H:i:s').']	END Order log'.chr(10);
$logInfo	.= '------------------------------------------------------------------------------'.chr(10).chr(10);
fwrite( $logfile, $logInfo);
fclose( $logfile );

$ip_arr = array('203.233.72.14','203.233.72.150','203.233.72.151','203.233.72.158');
if(!in_array($_SERVER[REMOTE_ADDR],$ip_arr))exit; //아이피 인증

		if ( $r_res_cd == "0000" )
		{
		/* ---------------------------------------------------------------------- */
		/* ::: 가맹점 DB 처리                                                     */
		/* ---------------------------------------------------------------------- */
		/* DB처리 성공 시 : res_cd=0000, 실패 시 : res_cd=5001                    */
		/* ---------------------------------------------------------------------- */

			include "../../../lib/library.php";
			include "../../../conf/config.php";
			$noti_name = "";
			$ordno = $r_order_no;
			switch($r_noti_type){
				case "20" :
					$noti_name = "승인 변경";
					break;
				case "30" :
					$noti_name = "입금";
					break;
				case "40" :
					$noti_name = "에스크로 변경";
					break;
			}

			$settlelog = "\n----------------------------------------\n";
			$settlelog .= "처리 시간 : ".date('Y:m:d H:i:s')."\n";
			if($r_noti_type == '30')$settlelog .="입금확인 : PG단자동입금확인\n";
			$settlelog		.= "에스크로 : ".$r_escrow_yn."\n";
			$settlelog		.= "노티구분 : ".$r_noti_type."(".$noti_name.")\n";
			$settlelog		.= "상태코드 : ".$r_stat_cd."\n";
			$settlelog		.= "상태메시지 : ".$r_stat_msg."\n";
			if($r_res_cd)$settlelog		.= "응답 코드 : ".$r_res_cd."\n";
			if($r_res_msg)$settlelog			.= "응답 메세지 : ".$r_res_msg."\n";
			if($r_noti_type)$settlelog		.= "응답 구분 : ".$r_noti_type."\n";
			if($r_auth_no)$settlelog		.= "승인번호 : ".$r_auth_no."\n";
			if($r_tran_date)$settlelog	.= "승인일시 : ".$r_tran_date."\n";
			if($r_bank_nm || $r_bank_cd)$settlelog	.= "은행정보 : [".$r_bank_cd."] ".$r_bank_nm."\n";
			if($r_amount)$settlelog	.= "입금금액 : ".$r_amount."\n";
			if($r_deposit_nm)$settlelog	.= "입금자명 : ".$r_deposit_nm."\n";
			if($r_cash_res_cd)$settlelog		.= "현금영수증 결과코드 : ".$r_cash_res_cd."\n";
			if($r_cash_res_msg)$settlelog		.= "현금영수증 결과메시지 : ".$r_cash_res_msg."\n";
			if($r_cash_auth_no)$settlelog		.= "현금영수증 승인번호 : ".$r_cash_auth_no."\n";
			if($r_cash_tran_date)$settlelog		.= "현금영수증 승인일시 : ".$r_cash_tran_date."\n";
			if($r_auth_cno)$settlelog		.= "인증거래번호 : ".$r_auth_cno."\n";
			$settlelog	.= "----------------------------------------";

			### item check stock
			include "../../../lib/cardCancel.class.php";
			if($r_noti_type=="40" || $r_noti_type=="30") {		//에스크로 변경 or 입금
					$cancel = new cardCancel();
					$step = 1;
					if(!$cancel->chk_item_stock($ordno) && $cfg['stepStock'] == '1')$step = 51;
					$query = "
					select * from
						".GD_ORDER." a
						left join ".GD_LIST_BANK." b on a.bankAccount = b.sno
					where
						a.ordno='$ordno'
					";
					$data = $db->fetch($query);
					if($step==51)$cancel->cancel_db_proc($ordno);
					else{
						### 실데이타 저장
						$db->query("
						update ".GD_ORDER." set cyn='y', cdt=now(),
							step		= '$step',
							step2		= '',
							settlelog	= concat(settlelog,'$settlelog'),
							cardtno		= '$r_auth_no'
						where ordno='$ordno'"
						);
						$db->query("update ".GD_ORDER_ITEM." set cyn='y', istep='$step' where ordno='$ordno'");

						if($r_cash_auth_no) $db-> query("update ".GD_ORDER." set cashreceipt='$r_cash_auth_no' where ordno='$ordno'"); //현금영수증이 발급되었을 경우 현금영수증 처리

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
			}
			else{
				$db->query("
				UPDATE ".GD_ORDER." SET
					settlelog		= concat(ifnull(settlelog,''),'$settlelog')
				WHERE ordno='$ordno'");
			}
					

			$result_msg = "res_cd=0000" . chr(31) . "res_msg=SUCCESS";
		}
		else
		{
			$result_msg = "res_cd=5001" . chr(31) . "res_msg=FAIL";
		}
	/* -------------------------------------------------------------------------- */
	/* ::: 노티 처리결과 처리                                                     */
	/* -------------------------------------------------------------------------- */
	echo $result_msg;

?>