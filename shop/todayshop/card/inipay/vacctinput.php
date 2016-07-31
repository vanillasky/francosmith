<?php
/**
 * 이니시스 PG 가상계좌 입금 처리 페이지
 * 원본 파일명 vacctinput.php
 * 이니시스 PG 버전 : INIpay V5.0 (V 0.1.1 - 20120302)
 */

//--- 기본 정보
include "../../../lib/library.php";
include "../../../conf/config.php";

//--- 결과값을 extract 함
@extract($_GET);
@extract($_POST);
@extract($_SERVER);

//--- INIpay 경로
$INIpayHome	= dirname($_SERVER['SCRIPT_FILENAME']);      // 이니페이 홈디렉터리

//--- 기본 설정
$TEMP_IP	= getenv('REMOTE_ADDR');
$PG_IP		= substr($TEMP_IP, 0, 10);

//--- PG에서 보냈는지 IP로 체크
if( $PG_IP == '203.238.37' || $PG_IP == '210.98.138' )
{
	$msg_id			= $msg_id;				//메세지 타입
	$no_tid			= $no_tid;				//거래번호
	$no_oid			= $no_oid;				//상점 주문번호
	$id_merchant	= $id_merchant;			//상점 아이디
	$cd_bank		= $cd_bank;				//거래 발생 기관 코드
	$cd_deal		= $cd_deal;				//취급 기관 코드
	$dt_trans		= $dt_trans;			//거래 일자
	$tm_trans		= $tm_trans;			//거래 시간
	$no_msgseq		= $no_msgseq;			//전문 일련 번호
	$cd_joinorg		= $cd_joinorg;			//제휴 기관 코드

	$dt_transbase	= $dt_transbase;		//거래 기준 일자
	$no_transeq		= $no_transeq;			//거래 일련 번호
	$type_msg		= $type_msg;			//거래 구분 코드
	$cl_close		= $cl_close;			//마감 구분코드
	$cl_kor			= $cl_kor;				//한글 구분 코드
	$no_msgmanage	= $no_msgmanage;		//전문 관리 번호
	$no_vacct		= $no_vacct;			//가상계좌번호
	$amt_input		= $amt_input;			//입금금액
	$amt_check		= $amt_check;			//미결제 타점권 금액
	$nm_inputbank	= $nm_inputbank;		//입금 금융기관명
	$nm_input		= $nm_input;			//입금 의뢰인
	$dt_inputstd	= $dt_inputstd;			//입금 기준 일자
	$dt_calculstd	= $dt_calculstd;		//정산 기준 일자
	$flg_close		= $flg_close;			//마감 전화

	// 가상계좌채번시 현금영수증 자동발급신청시에만 전달
	$dt_cshr     	= $dt_cshr;				//현금영수증 발급일자
	$tm_cshr     	= $tm_cshr;				//현금영수증 발급시간
	$no_cshr_appl	= $no_cshr_appl;		//현금영수증 발급번호
	$no_cshr_tid 	= $no_cshr_tid;			//현금영수증 발급TID

	$logfile		= fopen( $INIpayHome . '/log/INI_vbank_result_'.date('Ymd').'.log', 'a+' );

	// 로그 저장 (이니시스 로그로 파일로 저장 이니시스의 모든 값을 저장)
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

	//--- 입금 확인 처리
	if (empty($no_oid) === false) {

		//--- 로그 생성
		$settlelog	= '===================================================='.chr(10);
		$settlelog	.= '가상계좌 입금 자동 확인 : 성공 ('.date('Y-m-d H:i:s').')'.chr(10);
		$settlelog	.= '===================================================='.chr(10);
		$settlelog	.= '주문번호 : '.$no_oid.chr(10);
		$settlelog	.= '거래번호 : '.$no_tid.chr(10);
		$settlelog	.= '전문 일련 번호 : '.$no_msgseq.chr(10);
		$settlelog	.= '입금금액 : '.number_format($amt_input).chr(10);

		// 현금영수증 결과 정보
		if (empty($no_cshr_appl) === false && empty($no_cshr_tid) === false) {
			$settlelog	.= '결과내용 : 가상계좌 자동입금 확인에 의한 처리'.chr(10);
			$settlelog	.= '현금영수증 발급번호 : '.$no_cshr_appl.chr(10);
			$settlelog	.= '현금영수증 발급TID : '.$no_cshr_tid.chr(10);

			$qrc1	= "cashreceipt='".$no_cshr_tid."',";
		}
		$settlelog	.= '===================================================='.chr(10);

		// 주문 번호
		$ordno	= $no_oid;

		// 주문 정보
		$query = "
		SELECT * FROM
			".GD_ORDER." a
			LEFT JOIN ".GD_LIST_BANK." b on a.bankAccount = b.sno
		WHERE
			a.ordno='$ordno'
		";
		$data = $db->fetch($query);

		### 실데이타 저장
		$db->query("
		UPDATE ".GD_ORDER." SET ".$qrc1." cyn='y', cdt=now(),
			step		= '1',
			step2		= '',
			cardtno		= '$no_tid',
			settlelog	= concat(settlelog,'$settlelog')
		WHERE ordno='$ordno'"
		);
		$db->query("update ".GD_ORDER_ITEM." set cyn='y', istep='1' where ordno='$ordno'");

		### 주문로그 저장
		orderLog($ordno,$r_step2[$data[step2]]." > ".$r_step[$step]);

		### 재고 처리
		setStock($ordno);

		### 입금확인메일
		//sendMailCase($data[email],1,$data);

		### 입금확인SMS
		//$dataSms = $data;
		//sendSmsCase('incash',$data[mobileOrder]);

		// 즉시 발급 쿠폰 생성 및 문자 전송 (todayshop_noti 클래스는 todayshop 을 상속받았기 때문에 멤버를 사용해도 됨)
		$todayshop_noti = &load_class('todayshop_noti', 'todayshop_noti');
		$orderinfo = $todayshop_noti->getorderinfo($ordno);
		if ($orderinfo['goodstype'] == 'coupon') { // 쿠폰인 경우
			if ($orderinfo['processtype'] == 'i') { // 즉시 발급 쿠폰만 발급하고 SMS/MAIL
				if (($cp_sno = $todayshop_noti->publishCoupon($ordno)) !== false) {
					$formatter = &load_class('stringFormatter', 'stringFormatter');
					if ($phone = $formatter->get($data['mobileReceiver'],'dial','-')) {
						$db->query("UPDATE ".GD_TODAYSHOP_ORDER_COUPON." SET cp_publish = 1 WHERE cp_sno = '$cp_sno'");	// 발급 처리
						ctlStep($ordno,4,1);
					}
				}
			}
		}

	    //위에서 상점 데이터베이스에 등록 성공유무에 따라서 성공시에는 "OK"를 이니시스로
	    //리턴하셔야합니다. 아래 조건에 데이터베이스 성공시 받는 FLAG 변수를 넣으세요
	    //(주의) OK를 리턴하지 않으시면 이니시스 지불 서버는 "OK"를 수신할때까지 계속 재전송을 시도합니다
	    //기타 다른 형태의 PRINT( echo )는 하지 않으시기 바랍니다
		echo "OK";			// 절대로 지우지마세요
	}
}
?>