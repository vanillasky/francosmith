<?php
 /***************************************************************************************************************
 * 올더게이트로부터 가상계좌 입/출금 데이타를 받아서 상점에서 처리 한 후
 * 올더게이트로 다시 응답값을 리턴하는 페이지입니다.
 * 상점 DB처리 부분을 업체에 맞게 수정하여 작업하시기 바랍니다.
***************************************************************************************************************/
include "../../../lib/library.php";
include "../../../conf/config.php";

/*********************************** 올더게이트로 부터 넘겨 받는 값들 시작 *************************************/
$trcode     = trim( $_POST["trcode"] );					    //거래코드
$service_id = trim( $_POST["service_id"] );					//상점아이디
$orderdt    = trim( $_POST["orderdt"] );				    //승인일자
$virno      = trim( $_POST["virno"] );				        //가상계좌번호
$deal_won   = trim( $_POST["deal_won"] );					//입금액
$ordno		= trim( $_POST["ordno"] );                      //주문번호
$inputnm	= trim( $_POST["inputnm"] );					//입금자명
/*********************************** 올더게이트로 부터 넘겨 받는 값들 끝 *************************************/

/***************************************************************************************************************
 * 상점에서 해당 거래에 대한 처리 db 처리 등....
 *
 * trcode = "1" ☞ 일반가상계좌 입금통보전문
 * trcode = "2" ☞ 일반가상계좌 취소통보전문
 *
***************************************************************************************************************/

if ($trcode == '1') $trname = '일반가상계좌 입금통보전문';
else if ($trcode == '2') $trname = '일반가상계좌 취소통보전문';
$tmp_log = array();
$tmp_log[] = '----------------------------------------';
$tmp_log[] = '입금확인 : PG단자동입금확인('.$trname.')';
$tmp_log[] = '확인시간 : '.date('Y:m:d H:i:s');
$tmp_log[] = '거래코드 : '.$trcode;
$tmp_log[] = '상점아이디 : '.$service_id;
$tmp_log[] = '주문일시 : '.$orderdt;
$tmp_log[] = '가상계좌번호 : '.$virno;
$tmp_log[] = '입금액 : '.$deal_won;
$tmp_log[] = '입금자명 : '.$inputnm;
$tmp_log[] = '----------------------------------------';
$settlelog = implode( "\n", $tmp_log )."\n";

### item check stock
include "../../../lib/cardCancel.class.php";
$cancel = new cardCancel();
if(!$cancel->chk_item_stock($ordno) && $cfg['stepStock'] == '1'){
	$cancel -> cancel_db_proc($ordno,$no_tid);
} else if ($trcode == '1') {
	$query = "
	select * from
		".GD_ORDER." a
		left join ".GD_LIST_BANK." b on a.bankAccount = b.sno
	where
		a.ordno='$ordno'
	";
	$data = $db->fetch($query);

	### 실데이타 저장
	$db->query("
	update ".GD_ORDER." set cyn='y', cdt=now(),
		step		= '1',
		step2		= '',
		settlelog	= concat('".$settlelog."',settlelog)
	where ordno='$ordno'"
	);
	$db->query("update ".GD_ORDER_ITEM." set cyn='y', istep='1' where ordno='$ordno'");

	### 주문로그 저장
	orderLog($ordno,$r_step2[$data['step2']]." > ".$r_step[$step]);

	### 재고 처리
	setStock($ordno);

	### 입금확인메일
	sendMailCase($data['email'],1,$data);

	### 입금확인SMS
	$dataSms = $data;
	sendSmsCase('incash',$data['mobileOrder']);

	### Ncash 거래 확정 API
	include "../../../lib/naverNcash.class.php";
	$naverNcash = new naverNcash();
	$naverNcash->deal_done($ordno);
}


/******************************************처리 결과 리턴******************************************************/
$rResMsg  = "";
$rSuccYn  = "y";// 정상 : y 실패 : n

//정상처리 경우 거래코드|상점아이디|주문일시|가상계좌번호|처리결과|
$rResMsg .= $trcode."|";
$rResMsg .= $service_id."|";
$rResMsg .= $orderdt."|";
$rResMsg .= $virno."|";
$rResMsg .= $rSuccYn."|";

echo $rResMsg;
/******************************************처리 결과 리턴******************************************************/
?>