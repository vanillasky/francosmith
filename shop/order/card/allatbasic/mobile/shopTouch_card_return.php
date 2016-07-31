<?php

include "../../../../lib/library.php";
include "../../../../conf/config.mobileShop.php";
include "../../../../conf/config.php";
include "../../../../conf/pg_mobile.allatbasic.php";
include "./allatutil.php";

$ordno = $_POST['allat_order_no'];

$query = "select settleprice from gd_order where ordno='".$ordno."';";
list($settleprice) = $db->fetch($query);

//Request Value Define
//----------------------
/********************* Service Code *********************/
$at_cross_key =  $pg_mobile['crosskey']; //설정필요
$at_shop_id = $pg_mobile['id']; //설정필요
$at_amt=$settleprice; //결제 금액을 다시 계산해서 만들어야 함(해킹방지)
/*********************************************************/

// 요청 데이터 설정
//----------------------

$at_data   = "allat_shop_id=".$at_shop_id.
		   "&allat_amt=".$at_amt.
		   "&allat_enc_data=".$_POST["allat_enc_data"].
		   "&allat_cross_key=".$at_cross_key;


// 올앳 결제 서버와 통신 : ApprovalReq->통신함수, $at_txt->결과값
//----------------------------------------------------------------
$at_txt = ApprovalReq($at_data,"SSL");
// 이 부분에서 로그를 남기는 것이 좋습니다.
// (올앳 결제 서버와 통신 후에 로그를 남기면, 통신에러시 빠른 원인파악이 가능합니다.)

// 결제 결과 값 확인
//------------------
$REPLYCD			= getValue("reply_cd",$at_txt);        //결과코드
$REPLYMSG			= getValue("reply_msg",$at_txt);       //결과 메세지

$ORDER_NO			=getValue("order_no",$at_txt);
$AMT				=getValue("amt",$at_txt);
$PAY_TYPE			=getValue("pay_type",$at_txt);
$APPROVAL_YMDHMS	=getValue("approval_ymdhms",$at_txt);
$SEQ_NO				=getValue("seq_no",$at_txt);
$APPROVAL_NO		=getValue("approval_no",$at_txt);
$CARD_ID			=getValue("card_id",$at_txt);
$CARD_NM			=getValue("card_nm",$at_txt);
$SELL_MM			=getValue("sell_mm",$at_txt);
$ZEROFEE_YN			=getValue("zerofee_yn",$at_txt);
$CERT_YN			=getValue("cert_yn",$at_txt);
$CONTRACT_YN		=getValue("contract_yn",$at_txt);
$SAVE_AMT			=getValue("save_amt",$at_txt);
$BANK_ID			=getValue("bank_id",$at_txt);
$BANK_NM			=getValue("bank_nm",$at_txt);
$CASH_BILL_NO		=getValue("cash_bill_no",$at_txt);
$ESCROW_YN			=getValue("escrow_yn",$at_txt);
$ACCOUNT_NO			=getValue("account_no",$at_txt);
$ACCOUNT_NM			=getValue("account_nm",$at_txt);
$INCOME_ACC_NM		=getValue("income_account_nm",$at_txt);
$INCOME_LIMIT_YMD	=getValue("income_limit_ymd",$at_txt);
$INCOME_EXPECT_YMD	=getValue("income_expect_ymd",$at_txt);
$CASH_YN			=getValue("cash_yn",$at_txt);
$HP_ID				=getValue("hp_id",$at_txt);
$TICKET_ID			=getValue("ticket_id",$at_txt);
$TICKET_PAY_TYPE	=getValue("ticket_pay_type",$at_txt);
$TICKET_NAME		=getValue("ticket_nm",$at_txt);

switch ($PAY_TYPE){
	case "3D": case "ISP": case "NOR":
		$settlelogAdd = "결제카드 : [$CARD_ID] $CARD_NM
할부개월 : $SELL_MM
무이자   : $ZEROFEE_YN
";
		break;
	case "ABANK":
		$settlelogAdd = "결제은행 : [$BANK_ID] $BANK_NM
현금영수증일련번호 : $CASH_BILL_NO
";
		break;
	case "VBANK":
		$settlelogAdd = "가상계좌 : $BANK_NM $ACCOUNT_NO $ACCOUNT_NM
입금계좌명 : $INCOME_ACC_NM
입금기한일 : $INCOME_LIMIT_YMD
입금예정일 : $INCOME_EXPECT_YMD
현금영수증신청여부 : $CASH_YN
현금영수증일련번호 : $CASH_BILL_NO
";
		break;
	case "HP":
		$settlelogAdd = "이동통신사구분 : $HP_ID
";
		break;
}

$settlelog = "All@Pay Mobile 결제요청에 대한 결과
$ordno (".date('Y:m:d H:i:s').")
----------------------------------------
결과코드 : $REPLYCD
결과내용 : $REPLYMSG
승인금액 : $AMT
지불수단 : $PAY_TYPE
승인일시 : $APPROVAL_YMDHMS
거래번호 : $SEQ_NO
승인번호 : $APPROVAL_NO
인증여부 : $CERT_YN
----------------------------------------
";

if ($settlelogAdd) $settlelog .= $settlelogAdd."----------------------------------------\n";

### 거래일련번호 저장
$query = "update ".GD_ORDER." set cardtno='".$SEQ_NO."' where ordno='".$ordno."'";
$db -> query($query);

### 가상계좌 결제의 재고 체크 단계 설정
$res_cstock = true;
if($cfg['stepStock'] == '1' && $PAY_TYPE=="VBANK") $res_cstock = false;

### item check stock
include "../../../../lib/cardCancel.class.php";
$cancel = new cardCancel();
if(!$cancel->chk_item_stock($ordno) && $res_cstock && (!strcmp($REPLYCD,"0000") || !strcmp($REPLYCD,"0001"))){
	if($cancel->cancel_allat_mobile_request($ordno))
	{
		$REPLYCD = "OUT OF STOCK";
	}
}

$oData = $db->fetch("select step, vAccount from ".GD_ORDER." where ordno='$ordno'");
if($oData['step'] > 0 || $oData['vAccount'] != ''){		// 중복결제

	$db->query("update ".GD_ORDER." set settlelog=concat(ifnull(settlelog,''),'$settlelog') where ordno='$ordno'");
	go("../../order_end.php?ordno=$ordno&card_nm=$CARD_NM","parent");

} else if( !strcmp($REPLYCD,"0000") || !strcmp($REPLYCD,"0001") ){		// 결제 성공
	// 결과값 처리
	//--------------------------------------------------------------------------
	// 결과 값이 '0000'이면 정상임. 단, allat_test_yn=Y 일경우 '0001'이 정상임.
	// 실제 결제   : allat_test_yn=N 일 경우 reply_cd=0000 이면 정상
	// 테스트 결제 : allat_test_yn=Y 일 경우 reply_cd=0001 이면 정상
	//--------------------------------------------------------------------------
	$query = "
	select * from
		".GD_ORDER." a
		left join ".GD_LIST_BANK." b on a.bankAccount = b.sno
	where
		a.ordno='$ordno'
	";
	$data = $db->fetch($query);

	include "../../../../lib/cart.class.php";

	$cart = new Cart($_COOKIE[gd_isDirect]);
	$cart->chkCoupon();

	$cart->delivery = $data[delivery];
	$cart->dc = $member[dc]."%";
	$cart->calcu();
	$cart -> totalprice += $delivery[price];

	### 주문확인메일
	$data[cart] = $cart;
	$data[str_settlekind] = $r_settlekind[$data[settlekind]];
	sendMailCase($data[email],0,$data);

	### 에스크로 여부 확인
	$escrowyn = ($ESCROW_YN=="Y") ? "y" : "n";

	### 결제 정보 저장
	$step = 1;
	$qrc1 = "cyn='y', cdt=now(),";
	$qrc2 = "cyn='y',";

	### 가상계좌 결제시 계좌정보 저장
	if ($PAY_TYPE=="VBANK"){
		$vAccount = $BANK_NM." ".$ACCOUNT_NO." ".$ACCOUNT_NM;
		$step = 0; $qrc1 = $qrc2 = "";
	}

	### 현금영수증 저장
	if ($CASH_BILL_NO != ''){
		$qrc1 .= "cashreceipt='{$CASH_BILL_NO}',";
	}

	### 실데이타 저장
	$db->query("
	update ".GD_ORDER." set $qrc1
		step		= '$step',
		step2		= '',
		escrowyn	= '$escrowyn',
		escrowno	= '$escrowno',
		vAccount	= '$vAccount',
		settlelog	= concat(ifnull(settlelog,''),'$settlelog')
	where ordno='$ordno'"
	);
	$db->query("update ".GD_ORDER_ITEM." set $qrc2 istep='$step' where ordno='$ordno'");

	### 주문로그 저장
	orderLog($ordno,$r_step2[$data[step2]]." > ".$r_step[$step]);

	### 재고 처리
	setStock($ordno);

	### 상품구입시 적립금 사용 _ 2007-06-04
	if ($data[m_no] && $data[emoney]){
		setEmoney($data[m_no],-$data[emoney],"상품구입시 적립금 결제 사용",$ordno);
	}

	### SMS 변수 설정
	$dataSms = $data;

	if ($PAY_TYPE!="VBANK"){
		sendMailCase($data[email],1,$data);			### 입금확인메일
		sendSmsCase('incash',$data[mobileOrder]);	### 입금확인SMS
	} else {
		sendSmsCase('order',$data[mobileOrder]);	### 주문확인SMS
	}

	go("/shopTouch/shopTouch_ord/order_end.php?ordno=$ordno&card_nm=$CARD_NM","parent");

} else {	// 결제 실패

	$db->query("update ".GD_ORDER." set step2=54, settlelog=concat(ifnull(settlelog,''),'$settlelog') where ordno='$ordno' and step2=50");
	$db->query("update ".GD_ORDER_ITEM." set istep=54 where ordno='$ordno' and istep=50");
	go("/shopTouch/shopTouch_ord/order_fail.php?ordno=$ordno","parent");

}

/*
    [신용카드 전표출력 예제]

    결제가 정상적으로 완료되면 아래의 소스를 이용하여, 고객에게 신용카드 전표를 보여줄 수 있습니다.
    전표 출력시 상점아이디와 주문번호를 설정하시기 바랍니다.

    var urls ="http://www.allatpay.com/servlet/AllatBizPop/member/pop_card_receipt.jsp?shop_id=상점아이디&order_no=주문번호";
    window.open(urls,"app","width=410,height=650,scrollbars=0");

    현금영수증 전표 또는 거래확인서 출력에 대한 문의는 올앳페이 사이트의 1:1상담을 이용하시거나
    02) 3788-9990 으로 전화 주시기 바랍니다.

    전표출력 페이지는 저희 올앳 홈페이지의 일부로써, 홈페이지 개편 등의 이유로 인하여 페이지 변경 또는 URL 변경이 있을 수
    있습니다. 홈페이지 개편에 관한 공지가 있을 경우, 전표출력 URL을 확인하시기 바랍니다.
*/
?>
