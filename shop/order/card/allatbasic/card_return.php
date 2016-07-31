<?php
include "../../../lib/library.php";
include "../../../conf/config.php";
include "../../../conf/pg.allatbasic.php";


// 올앳관련 함수 Include
//----------------------
include "./allatutil.php";

// PG결제 위변조 체크 및 유효성 체크
if (forge_order_check($_POST['allat_order_no'],$_POST['allat_amt']) === false) {
	msg('주문 정보와 결제 정보가 맞질 않습니다. 다시 결제 바랍니다.','../../order_fail.php?ordno='.$_POST['allat_order_no'],'parent');
	exit();
}

// Ncash 결제 승인 API
include "../../../lib/naverNcash.class.php";
$naverNcash = new naverNcash();
if($naverNcash->useyn=='Y')
{
	if($_POST['allat_vbank_yn']=="Y") $ncashResult = $naverNcash->payment_approval($_POST['allat_order_no'], false);
	else $ncashResult = $naverNcash->payment_approval($_POST['allat_order_no'], true);
	if($ncashResult===false)
	{
		msg('네이버 마일리지 사용에 실패하였습니다.','../../order_fail.php?ordno='.$_POST['allat_order_no'],'parent');
		exit();
	}
}

function return_allat($str){
	$tmp = explode("\n",trim($str));
	for($i=0;$i<sizeof($tmp);$i++){
		$div = explode("=",trim($tmp[$i]));
		$arr[$div[0]] = $div[1];
	}
	return $arr;
}

function allat_log_write($logMsg)
{
	$logInfo  = 'INFO ['.date('Y-m-d H:i:s').'] START Order log'.chr(10);
	$logInfo .= 'DEBUG ['.date('Y-m-d H:i:s').'] Connect IP : '.$_SERVER['REMOTE_ADDR'].chr(10);
	$logInfo .= 'DEBUG ['.date('Y-m-d H:i:s').'] Request URL : '.$_SERVER['REQUEST_URI'].chr(10);
	$logInfo .= 'DEBUG ['.date('Y-m-d H:i:s').'] User Agent : '.$_SERVER['HTTP_USER_AGENT'].chr(10);
	$logInfo .= $logMsg;
	$logInfo .= 'INFO ['.date('Y-m-d H:i:s').'] END Order log'.chr(10);
	$logInfo .= '------------------------------------------------------------------------------'.chr(10).chr(10);

	error_log($logInfo, 3, './log/allat_log_'.date('Ymd').'.log');
}

$ordno = $_POST['allat_order_no'];

//Request Value Define
//----------------------
/********************* Service Code *********************/
$at_cross_key = $pg['crosskey'];     //설정필요 [사이트 참조 - http://www.allatpay.com/servlet/AllatBiz/support/sp_install_guide_scriptapi.jsp#shop]
$at_shop_id   = urlencode($pg[id]);       //설정필요
$at_amt=$_POST['allat_amt'];         //결제 금액을 다시 계산해서 만들어야 함(해킹방지)
                                         //( session, DB 사용 )
/*********************************************************/

// 요청 데이터 설정
//----------------------

$at_data   = "allat_shop_id=".$at_shop_id.
             "&allat_amt=".$at_amt.
             "&allat_enc_data=".$_POST["allat_enc_data"].
             "&allat_cross_key=".$at_cross_key;


// 올앳 결제 서버와 통신 : ApprovalReq->통신함수, $at_txt->결과값
//----------------------------------------------------------------
$at_txt = ApprovalReq($at_data,$pg[ssl]); // 설정 필요 (SSL:SSL이용시 / NOSSL:SSL미사용시-에러코드 0212일 경우 사용)
// 이 부분에서 로그를 남기는 것이 좋습니다.
// (올앳 결제 서버와 통신 후에 로그를 남기면, 통신에러시 빠른 원인파악이 가능합니다.)
$at_return	= return_allat($at_txt);

// 올앳 로그
$logMsg = chr(9).str_replace(chr(10),chr(10).chr(9), str_replace('=', chr(9).chr(9).'= ', $at_txt)).chr(10);
allat_log_write($logMsg);

// 결제 결과 값 확인
//------------------
$REPLYCD   =getValue("reply_cd",$at_txt);        //결과코드
$REPLYMSG  =getValue("reply_msg",$at_txt);       //결과 메세지


// 결제로그 저장
$at_return = array_map("trim",$at_return);
extract($at_return);

/*******************************************************************************
reply_cd			= 0000				# 결과코드
reply_msg			= 정상				# 결과메세지
order_no			= 1341801465732		# 주문번호
amt					= 1000				# 승인금액
pay_type			= ISP				# 지불수단 (3D, ISP, NOR, ABANK)
approval_ymdhms		= 20120709113811	# 승인일시
seq_no				= 164884116			# 거래일련번호
escrow_yn			=					# 에스크로여부 - Y(에스크로), N(미적용)
******************************신용카드******************************************
approval_no			= 30012692			# 승인번호
card_id				= 00				# 카드ID - 카드종류코드(예:01,02,… … )
card_nm				= 테스트			# 카드명 - 카드종류명(예:삼성, 국민, … … )
sell_mm				= 00				# 할부개월
zerofee_yn			= N					# 무이자(Y),일시불(N)
cert_yn				= N					# 인증여부 - 인증(Y),미인증(N)
contract_yn			= N					# 직가맹여부 - 3자가맹점(Y),대표가맹점(N)
save_amt			=					# 세이브 결제 금액
******************************계좌이체 / 가상계좌*******************************
bank_id				=					# 은행ID
bank_nm				=					# 은행명
cash_bill_no		=					# 현금영수증일련번호 - 현금영수증 등록시
******************************가상계좌******************************************
account_no			=					# 계좌번호
income_acc_nm		=					# 입금계좌명
account_nm			=					# 입금자명
income_limit_ymd	=					# 입금기한일
income_expect_ymd	=					# 입금예정일
cash_yn				=					# 현금영수증신청여부
******************************휴대폰결제****************************************
hp_id				=					# 이동통신사구분
******************************상품권결제****************************************
ticket_id			=					# 상품권 ID
ticket_name			=					# 상품권 이름
ticket_pay_type		=					# 결제구분
********************************************************************************
sfcard_id		= 00					#
sfcard_nm		= 테스트				#
*******************************************************************************/

switch ($pay_type){
	case "3D": case "ISP": case "NOR":
		$settlelogAdd = "
결제카드 : [$card_id] $card_nm
할부개월 : $sell_mm
무이자   : $zerofee_yn
";
		break;
	case "ABANK":
		$settlelogAdd = "
결제은행 : [$bank_id] $bank_nm
현금영수증일련번호 : $cash_bill_no
";
		break;
	case "VBANK":
		$settlelogAdd = "
가상계좌 : $bank_nm $account_no $account_nm
입금계좌명 : $income_account_nm
입금기한일 : $income_limit_ymd
입금예정일 : $income_expect_ymd
현금영수증신청여부 : $cash_yn
현금영수증일련번호 : $cash_bill_no
";
		break;
	case "HP":
		$settlelogAdd = "
이동통신사구분 : $hp_id
";
		break;
}

$settlelog = "$ordno (".date('Y:m:d H:i:s').")
----------------------------------------
결과코드 : $reply_cd
결과내용 : $reply_msg
승인금액 : $amt
지불수단 : $pay_type
승인일시 : $approval_ymdhms
거래번호 : $seq_no
승인번호 : $approval_no
인증여부 : $cert_yn
에스크로 : $escrow_yn
----------------------------------------";

if ($settlelogAdd) $settlelog .= $settlelogAdd."----------------------------------------";

// 전자보증보험 발급
@session_start();
if (session_is_registered('eggData') === true && !strcmp($REPLYCD,"0000")){
	if ($_SESSION[eggData][ordno] == $ordno && $_SESSION[eggData][resno1] != '' && $_SESSION[eggData][resno2] != '' && $_SESSION[eggData][agree] == 'Y'){
		include '../../../lib/egg.class.usafe.php';
		$eggData = $_SESSION[eggData];
		switch ($pay_type){
			case "3D": case "ISP": case "NOR":
				$eggData[payInfo1] = $card_nm; # (*) 결제정보(카드사)
				$eggData[payInfo2] = $approval_no; # (*) 결제정보(승인번호)
				break;
			case "ABANK":
				$eggData[payInfo1] = $bank_nm; # (*) 결제정보(은행명)
				$eggData[payInfo2] = $seq_no; # (*) 결제정보(승인번호 or 거래번호)
				break;
			case "VBANK":
				$eggData[payInfo1] = $bank_nm; # (*) 결제정보(은행명)
				$eggData[payInfo2] = $account_no; # (*) 결제정보(계좌번호)
				break;
		}
		$eggCls = new Egg( 'create', $eggData );
		if ( $eggCls->isErr == true && $pay_type == "VBANK" ){
			$REPLYCD = '';
		}
		else if ( $eggCls->isErr == true && in_array($pay_type, array("3D","ISP","NOR","ABANK")) );
	}
	session_unregister('eggData');
}

// 거래일련번호 저장
$query = "update ".GD_ORDER." set cardtno='".$seq_no."' where ordno='".$ordno."'";
$db -> query($query);

// 가상계좌 결제의 재고 체크 단계 설정
$res_cstock = true;
if($cfg['stepStock'] == '1' && $pay_type=="VBANK") $res_cstock = false;

// item check stock
include "../../../lib/cardCancel.class.php";
$cancel = new cardCancel();
if(!$cancel->chk_item_stock($ordno) && $res_cstock){
	$cancel -> cancel_allat_request($ordno);
	exit;
}

$oData = $db->fetch("select step, vAccount from ".GD_ORDER." where ordno='$ordno'");

  // 결과값 처리
  //--------------------------------------------------------------------------
  // 결과 값이 '0000'이면 정상임. 단, allat_test_yn=Y 일경우 '0001'이 정상임.
  // 실제 결제   : allat_test_yn=N 일 경우 reply_cd=0000 이면 정상
  // 테스트 결제 : allat_test_yn=Y 일 경우 reply_cd=0001 이면 정상
  //--------------------------------------------------------------------------
 if($oData['step'] > 0 || $oData['vAccount'] != ''){		// 중복결제

	$db->query("update ".GD_ORDER." set settlelog=concat(ifnull(settlelog,''),'$settlelog') where ordno='$ordno'");
	go("../../order_end.php?ordno=$ordno&card_nm=$card_nm","parent");

} else  if( !strcmp($REPLYCD,"0000") ){
    // reply_cd "0000" 일때만 성공
    $ORDER_NO         =getValue("order_no",$at_txt);
    $AMT              =getValue("amt",$at_txt);
    $PAY_TYPE         =getValue("pay_type",$at_txt);
    $APPROVAL_YMDHMS  =getValue("approval_ymdhms",$at_txt);
    $SEQ_NO           =getValue("seq_no",$at_txt);
    $APPROVAL_NO      =getValue("approval_no",$at_txt);
    $CARD_ID          =getValue("card_id",$at_txt);
    $CARD_NM          =getValue("card_nm",$at_txt);
    $SELL_MM          =getValue("sell_mm",$at_txt);
    $ZEROFEE_YN       =getValue("zerofee_yn",$at_txt);
    $CERT_YN          =getValue("cert_yn",$at_txt);
    $CONTRACT_YN      =getValue("contract_yn",$at_txt);
    $SAVE_AMT         =getValue("save_amt",$at_txt);
    $BANK_ID          =getValue("bank_id",$at_txt);
    $BANK_NM          =getValue("bank_nm",$at_txt);
    $CASH_BILL_NO     =getValue("cash_bill_no",$at_txt);
    $ESCROW_YN        =getValue("escrow_yn",$at_txt);
    $ACCOUNT_NO       =getValue("account_no",$at_txt);
    $ACCOUNT_NM       =getValue("account_nm",$at_txt);
    $INCOME_ACC_NM    =getValue("income_account_nm",$at_txt);
    $INCOME_LIMIT_YMD =getValue("income_limit_ymd",$at_txt);
    $INCOME_EXPECT_YMD=getValue("income_expect_ymd",$at_txt);
    $CASH_YN          =getValue("cash_yn",$at_txt);
    $HP_ID            =getValue("hp_id",$at_txt);
    $TICKET_ID        =getValue("ticket_id",$at_txt);
    $TICKET_PAY_TYPE  =getValue("ticket_pay_type",$at_txt);
    $TICKET_NAME      =getValue("ticket_nm",$at_txt);

   /* echo "결과코드              : ".$REPLYCD."<br>";
    echo "결과메세지            : ".$REPLYMSG."<br>";
    echo "주문번호              : ".$ORDER_NO."<br>";
    echo "승인금액              : ".$AMT."<br>";
    echo "지불수단              : ".$PAY_TYPE."<br>";
    echo "승인일시              : ".$APPROVAL_YMDHMS."<br>";
    echo "거래일련번호          : ".$SEQ_NO."<br>";
    echo "에스크로 적용 여부    : ".$ESCROW_YN."<br>";
    echo "=============== 신용 카드 ===============================<br>";
    echo "승인번호              : ".$APPROVAL_NO."<br>";
    echo "카드ID                : ".$CARD_ID."<br>";
    echo "카드명                : ".$CARD_NM."<br>";
    echo "할부개월              : ".$SELL_MM."<br>";
    echo "무이자여부            : ".$ZEROFEE_YN."<br>";   //무이자(Y),일시불(N)
    echo "인증여부              : ".$CERT_YN."<br>";      //인증(Y),미인증(N)
    echo "직가맹여부            : ".$CONTRACT_YN."<br>";  //3자가맹점(Y),대표가맹점(N)
    echo "세이브 결제 금액      : ".$SAVE_AMT."<br>";
    echo "=============== 계좌 이체 / 가상계좌 ====================<br>";
    echo "은행ID                : ".$BANK_ID."<br>";
    echo "은행명                : ".$BANK_NM."<br>";
    echo "현금영수증 일련 번호  : ".$CASH_BILL_NO."<br>";
    echo "=============== 가상계좌 ================================<br>";
    echo "계좌번호              : ".$ACCOUNT_NO."<br>";
    echo "입금계좌명            : ".$INCOME_ACC_NM."<br>";
    echo "입금자명              : ".$ACCOUNT_NM."<br>";
    echo "입금기한일            : ".$INCOME_LIMIT_YMD."<br>";
    echo "입금예정일            : ".$INCOME_EXPECT_YMD."<br>";
    echo "현금영수증신청 여부   : ".$CASH_YN."<br>";
    echo "=============== 휴대폰 결제 =============================<br>";
    echo "이동통신사구분        : ".$HP_ID."<br>";
    echo "=============== 상품권 결제 =============================<br>";
    echo "상품권 ID             : ".$TICKET_ID."<br>";
    echo "상품권 이름           : ".$TICKET_NAME."<br>";
    echo "결제구분              : ".$TICKET_PAY_TYPE."<br>"; */

	$query = "
	select * from
		".GD_ORDER." a
		left join ".GD_LIST_BANK." b on a.bankAccount = b.sno
	where
		a.ordno='$ordno'
	";
	$data = $db->fetch($query);	

	// 에스크로 여부 확인
	$escrowyn = ($escrow_yn=="Y") ? "y" : "n";

	// 결제 정보 저장
	$step = 1;
	$qrc1 = "cyn='y', cdt=now(),";
	$qrc2 = "cyn='y',";

	// 가상계좌 결제시 계좌정보 저장
	if ($pay_type=="VBANK"){
		$vAccount = $bank_nm." ".$account_no." ".$account_nm;
		$step = 0; $qrc1 = $qrc2 = "";
	}

	// 현금영수증 저장
	if ($cash_bill_no != ''){
		$qrc1 .= "cashreceipt='{$cash_bill_no}',";
	}

	// 실데이타 저장
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

	// 주문로그 저장
	orderLog($ordno,$r_step2[$data[step2]]." > ".$r_step[$step]);

	// 재고 처리
	setStock($ordno);

	// 상품구입시 적립금 사용 _ 2007-06-04
	if ($data[m_no] && $data[emoney]){
		setEmoney($data[m_no],-$data[emoney],"상품구입시 적립금 결제 사용",$ordno);
	}

	### 주문확인메일
	if(function_exists('getMailOrderData')){
		sendMailCase($data['email'],0,getMailOrderData($ordno));
	}

	// SMS 변수 설정
	$dataSms = $data;

	if ($pay_type!="VBANK"){
		sendMailCase($data[email],1,$data);			// 입금확인메일
		sendSmsCase('incash',$data[mobileOrder]);	// 입금확인SMS
	} else {
		sendSmsCase('order',$data[mobileOrder]);	// 주문확인SMS
	}
	
	

	go("../../order_end.php?ordno=$ordno&card_nm=$card_nm","parent");



  }else{
    // reply_cd 가 "0000" 아닐때는 에러 (자세한 내용은 매뉴얼참조)
    // reply_msg 는 실패에 대한 메세지
    echo "결과코드  : ".$REPLYCD."<br>";
    echo "결과메세지: ".$REPLYMSG."<br>";

	$db->query("update ".GD_ORDER." set step2=54, settlelog=concat(ifnull(settlelog,''),'$settlelog') where ordno='$ordno' and step2=50");
	$db->query("update ".GD_ORDER_ITEM." set istep=54 where ordno='$ordno' and istep=50");

	// Ncash 결제 승인 취소 API 호출
	if($naverNcash->useyn=='Y') $naverNcash->payment_approval_cancel($ordno);

	go("../../order_fail.php?ordno=$ordno","parent");
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