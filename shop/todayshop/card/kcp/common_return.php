<?
    /* ============================================================================== */
    /* =   01. 공통 통보 페이지 설명(필독!!)                                        = */
    /* = -------------------------------------------------------------------------- = */
    /* =   공통 통보 페이지에서는, 가상계좌 입금 통보 데이터와 모바일안심결제       = */
    /* =   통보 데이터 등을 KCP 를 통해 별도로 통보 받을 수 있습니다. 이러한 통보   = */
    /* =   데이터를 받기 위해 가맹점측은 결과를 전송받는 페이지를 마련해 놓아야     = */
    /* =   합니다. 현재의 페이지를 업체에 맞게 수정하신 후, KCP 관리자 페이지에     = */
    /* =   등록해 주시기 바랍니다. 등록 방법은 연동 매뉴얼을 참고하시기 바랍니다.   = */
    /* ============================================================================== */
	$ip_arr = array('203.238.36.58','203.238.36.160','203.238.36.161','203.238.36.173','203.238.36.178');
	if(!in_array($_SERVER[REMOTE_ADDR],$ip_arr))exit; //아이피 인증

    /* ============================================================================== */
    /* =   02. 공통 통보 데이터 받기                                                = */
    /* = -------------------------------------------------------------------------- = */
    $site_cd      = $_POST [ "site_cd"  ];                 // 사이트 코드
    $tno          = $_POST [ "tno"      ];                 // KCP 거래번호
    $order_no     = $_POST [ "order_no" ];                 // 주문번호
    $tx_cd        = $_POST [ "tx_cd"    ];                 // 업무처리 구분 코드
    $tx_tm        = $_POST [ "tx_tm"    ];                 // 업무처리 완료 시간
    /* = -------------------------------------------------------------------------- = */
    $ipgm_name    = "";                                    // 주문자명
    $remitter     = "";                                    // 입금자명
    $ipgm_mnyx    = "";                                    // 입금 금액
    $bank_code    = "";                                    // 은행코드
    $account      = "";                                    // 가상계좌 입금계좌번호
    $op_cd        = "";                                    // 처리구분 코드
    $noti_id      = "";                                    // 통보 아이디
    /* = -------------------------------------------------------------------------- = */

    /* = -------------------------------------------------------------------------- = */
    /* =   02-1. 가상계좌 입금 통보 데이터 받기                                     = */
    /* = -------------------------------------------------------------------------- = */
    if ( $tx_cd == "TX00" )
    {
        $ipgm_name = $_POST[ "ipgm_name" ];                // 주문자명
        $remitter  = $_POST[ "remitter"  ];                // 입금자명
        $ipgm_mnyx = $_POST[ "ipgm_mnyx" ];                // 입금 금액
        $bank_code = $_POST[ "bank_code" ];                // 은행코드
        $account   = $_POST[ "account"   ];                // 가상계좌 입금계좌번호
        $op_cd     = $_POST[ "op_cd"     ];                // 처리구분 코드
        $noti_id   = $_POST[ "noti_id"   ];                // 통보 아이디
    }

    /* = -------------------------------------------------------------------------- = */
    /* =   02-2. 모바일안심결제 통보 데이터 받기                                    = */
    /* = -------------------------------------------------------------------------- = */
    else if ( $tx_cd == "TX08" )
    {
        $ipgm_mnyx = $_POST[ "ipgm_mnyx" ];                // 입금 금액
        $bank_code = $_POST[ "bank_code" ];                // 은행코드
    }
    /* ============================================================================== */


    /* ============================================================================== */
    /* =   03. 공통 통보 결과를 업체 자체적으로 DB 처리 작업하시는 부분입니다.      = */
    /* = -------------------------------------------------------------------------- = */
    /* =   통보 결과를 DB 작업 하는 과정에서 정상적으로 통보된 건에 대해 DB 작업을  = */
    /* =   실패하여 DB update 가 완료되지 않은 경우, 결과를 재통보 받을 수 있는     = */
    /* =   프로세스가 구성되어 있습니다. 소스에서 result 라는 Form 값을 생성 하신   = */
    /* =   후, DB 작업이 성공 한 경우, result 의 값을 "0000" 로 세팅해 주시고,      = */
    /* =   DB 작업이 실패 한 경우, result 의 값을 "0000" 이외의 값으로 세팅해 주시  = */
    /* =   기 바랍니다. result 값이 "0000" 이 아닌 경우에는 재통보를 받게 됩니다.   = */
    /* = -------------------------------------------------------------------------- = */

    /* = -------------------------------------------------------------------------- = */
    /* =   03-1. 가상계좌 입금 통보 데이터 DB 처리 작업 부분                        = */
    /* = -------------------------------------------------------------------------- = */
    if ( $tx_cd == "TX00" )
    {
    }

    /* = -------------------------------------------------------------------------- = */
    /* =   03-2. 모바일안심결제 통보 데이터 DB 처리 작업 부분                       = */
    /* = -------------------------------------------------------------------------- = */
    else if ( $tx_cd == "TX08" )
    {
    }
    /* ============================================================================== */


    /* ============================================================================== */
    /* =   04. result 값 세팅 하기                                                  = */
    /* ============================================================================== */
	include "../../../lib/library.php";
	include "../../../conf/config.php";

	$ordno = $order_no;
	if (!$ordno) exit;

	$settlelog = "
	----------------------------------------";
	if($site_cd)$settlelog		.= " 	사이트 코드 : ".$site_cd;
	if($tno)$settlelog			.= " 	KCP 거래번호 : ".$tno;
	if($tx_cd)$settlelog		.= " 	업무처리 구분 코드 : ".$tx_cd;
	if($tx_tm)$settlelog		.= " 	업무처리 완료 시간 : ".$tx_tm;
	if($ipgm_name)$settlelog	.= " 	주문자명 : ".$ipgm_name;
	if($ipgm_mnyx)$settlelog	.= " 	입금 금액 : ".$ipgm_mnyx;
	if($bank_code)$settlelog	.= " 	은행코드 : ".$bank_code;
	if($account)$settlelog		.= " 	가상계좌 입금계좌번호 : ".$account;
	if($op_cd)$settlelog		.= " 	처리구분 코드 : ".$op_cd;
	if($noti_id)$settlelog		.= " 	통보 아이디 : ".$noti_id;
	if($bank_code)$settlelog	.= " 	은행코드 : ".$bank_code;
	$settlelog	.= "
	----------------------------------------
	";

	### item check stock
	include "../../../lib/cardCancel.class.php";
	include "../../../lib/cardCancel_social.class.php";
	$cancel = new cardCancel_social();
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
			cardtno		= '$tno'
		where ordno='$ordno'"
		);
		$db->query("update ".GD_ORDER_ITEM." set cyn='y', istep='$step' where ordno='$ordno'");

		### 주문로그 저장
		orderLog($ordno,$r_step2[$data[step2]]." > ".$r_step[$step]);

		### 재고 처리
		setStock($ordno);
/*
		### 입금확인메일
		sendMailCase($data[email],1,$data);

		### 입금확인SMS
		$dataSms = $data;
		sendSmsCase('incash',$data[mobileOrder]);
*/
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
		else {	
			// 쿠폰이 아닌 실물상품인 경우, 판매량 증가
			$query = "
				select
				TG.tgsno from ".GD_ORDER_ITEM." AS O
				INNER JOIN ".GD_TODAYSHOP_GOODS." AS TG
				ON O.goodsno = TG.goodsno
				where O.ordno='$ordno'
			";
			$res = $db->query($query);
			while($tmp = $db->fetch($res)) {
	
				$query = "
					SELECT
	
						IFNULL(SUM(OI.ea), 0) AS cnt
	
					FROM ".GD_ORDER." AS O
					INNER JOIN ".GD_ORDER_ITEM." AS OI
						ON O.ordno=OI.ordno
					INNER JOIN ".GD_TODAYSHOP_GOODS_MERGED." AS TG
						ON OI.goodsno = TG.goodsno
	
					WHERE
							O.step > 0
						AND O.step2 < 40
						AND TG.tgsno='".$tmp['tgsno']."'
	
				";
	
				$_res = $db->query($query);
	
				while ($_tmp = $db->fetch($_res)) {
	
					$query = "
					UPDATE
						".GD_TODAYSHOP_GOODS_MERGED."		AS TGM
						INNER JOIN ".GD_TODAYSHOP_GOODS."	AS TG	ON TGM.goodsno = TG.goodsno
					SET
						TGM.buyercnt = ".$_tmp['cnt'].",
						TG.buyercnt = ".$_tmp['cnt']."
					WHERE
						TG.tgsno = ".$tmp['tgsno']."
					";
					$db->query($query);
	
				}
	
			}
		}			
	}

?>
<html><body><form><input type="hidden" name="result" value="0000"></form></body></html>