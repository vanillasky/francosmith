<?

include "../../../lib/library.php";
include "../../../conf/config.php";
include "../../../conf/pg.allat.php";
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
	if($_POST['allat_pay_type']=="VBANK") $ncashResult = $naverNcash->payment_approval($_POST['allat_order_no'], false);
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

### 결제인터페이스의 결과값 Get : 이전 주문결제페이지에서 Request Get
$at_data	= "allat_shop_id=".urlencode($pg[id])."&allat_amt=$_POST[allat_amt]&allat_enc_data=$_POST[allat_enc_data]&allat_cross_key=$pg[crosskey]";
$at_txt		= ApprovalReq($at_data,$pg[ssl]);	// 설정 필요 (SSL:SSL이용시 / NOSSL:SSL미사용시-에러코드 0212일 경우 사용)
$at_return	= return_allat($at_txt);

//--- 올엣 로그
$logMsg = chr(9).str_replace(chr(10),chr(10).chr(9), str_replace('=', chr(9).chr(9).'= ', iconv('EUC-KR','UTF-8',($at_txt)))).chr(10);
allat_log_write($logMsg);

$REPLYCD	= $at_return['reply_cd'];		//결과코드
$REPLYMSG	= $at_return['reply_msg'];		//결과메세지

### 결제로그 저장
$at_return = array_map("trim",$at_return);
extract($at_return);
/******************************************************************************
reply_cd		= 0000				# 결과코드
reply_msg		= 테스트성공		# 결과메세지
order_no		= 1149831153181		# 주문번호
amt				= 14600				# 승인금액
pay_type		= ISP				# 지불수단 (3D, ISP, NOR, ABANK)
approval_ymdhms	= 20060609150711	# 승인일시
seq_no			= 0000000000		# 거래일련번호
approval_no		= 12345678			# 승인번호
card_id			= 00				# 카드ID - 카드종류코드(예:01,02,… … )
card_nm			= 테스트			# 카드명 - 카드종류명(예:삼성, 국민, … … )
sell_mm			= 00				# 할부개월
zerofee_yn		= N					# 무이자(Y),일시불(N)
cert_yn			= N					# 인증여부 - 인증(Y),미인증(N)
contract_yn		= N					# 직가맹여부 - 3자가맹점(Y),대표가맹점(N)
*******************************************************************************
sfcard_id		= 00				#
sfcard_nm		= 테스트			#
bank_id			=					# 은행ID
bank_nm			=					# 은행명
cash_bill_no	=					# 현금영수증일련번호 - 현금영수증 등록시
escrow_yn		=					# 에스크로여부 - Y(에스크로), N(미적용)
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

### 전자보증보험 발급
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

### 거래일련번호 저장
$query = "update ".GD_ORDER." set cardtno='".$seq_no."' where ordno='".$ordno."'";
$db -> query($query);

### 가상계좌 결제의 재고 체크 단계 설정
$res_cstock = true;
if($cfg['stepStock'] == '1' && $pay_type=="VBANK") $res_cstock = false;

### item check stock
include "../../../lib/cardCancel.class.php";
$cancel = new cardCancel();
if(!$cancel->chk_item_stock($ordno) && $res_cstock){
	$cancel -> cancel_allat_request($ordno);
	exit;
}

$oData = $db->fetch("select step, vAccount from ".GD_ORDER." where ordno='$ordno'");
if($oData['step'] > 0 || $oData['vAccount'] != ''){		// 중복결제

	$db->query("update ".GD_ORDER." set settlelog=concat(ifnull(settlelog,''),'$settlelog') where ordno='$ordno'");
	go("../../order_end.php?ordno=$ordno&card_nm=$card_nm","parent");

} else if( !strcmp($REPLYCD,"0000") ){		// 결제 성공

	$query = "
	select * from
		".GD_ORDER." a
		left join ".GD_LIST_BANK." b on a.bankAccount = b.sno
	where
		a.ordno='$ordno'
	";
	$data = $db->fetch($query);

	### 에스크로 여부 확인
	$escrowyn = ($escrow_yn=="Y") ? "y" : "n";

	### 결제 정보 저장
	$step = 1;
	$qrc1 = "cyn='y', cdt=now(),";
	$qrc2 = "cyn='y',";

	### 가상계좌 결제시 계좌정보 저장
	if ($pay_type=="VBANK"){
		$vAccount = $bank_nm." ".$account_no." ".$account_nm;
		$step = 0; $qrc1 = $qrc2 = "";
	}

	### 현금영수증 저장
	if ($cash_bill_no != ''){
		$qrc1 .= "cashreceipt='{$cash_bill_no}',";
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

	### 주문확인메일
	if(function_exists('getMailOrderData')){
		sendMailCase($data['email'],0,getMailOrderData($ordno));
	}

	### SMS 변수 설정
	$dataSms = $data;

	if ($pay_type!="VBANK"){
		sendMailCase($data[email],1,$data);			### 입금확인메일
		sendSmsCase('incash',$data[mobileOrder]);	### 입금확인SMS
	} else {
		sendSmsCase('order',$data[mobileOrder]);	### 주문확인SMS
	}

	go("../../order_end.php?ordno=$ordno&card_nm=$card_nm","parent");

} else {	// 결제 실패

	$db->query("update ".GD_ORDER." set step2=54, settlelog=concat(ifnull(settlelog,''),'$settlelog') where ordno='$ordno' and step2=50");
	$db->query("update ".GD_ORDER_ITEM." set istep=54 where ordno='$ordno' and istep=50");

	// Ncash 결제 승인 취소 API 호출
	if($naverNcash->useyn=='Y') $naverNcash->payment_approval_cancel($ordno);

	go("../../order_fail.php?ordno=$ordno","parent");

}

?>