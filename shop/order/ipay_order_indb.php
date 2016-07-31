<?php
/**
 * @Path		: /shop/order/ipay_order_indb.php
 * @Description	: 옥션 아이페이 전용결제 이용 주문 DB 처리 페이지
 * @Author		: 박형준@개발팀
 * @Since		: 2012.05.19
 */

include "../lib/library.php";
include "../conf/config.php";
require_once dirname(__FILE__)."/../lib/auctionIpay.service.class.php";
require_once dirname(__FILE__)."/../lib/integrate_order_processor.class.php";
require_once dirname(__FILE__)."/../lib/integrate_order_processor.model.ipay.class.php";

if (get_magic_quotes_gpc()) {
	stripslashes_all($_POST);
	stripslashes_all($_GET);
}

// Ncash 거래 확정 API
include "../lib/naverNcash.class.php";
$naverNcash = new naverNcash();
if($naverNcash->useyn=='Y')
{
	if($result['PaymentType']=='A') $ncashResult = $naverNcash->payment_approval($_GET['ordno'], false);
	else $ncashResult = $naverNcash->payment_approval($_GET['ordno'], true);
	if($ncashResult===false)
	{
		msg('네이버 마일리지 사용에 실패하였습니다.\r\n옥션 아이페이 결제는 자동취소되지 않으므로 입금이 완료된경우\r\n쇼핑몰 고객센터로 환불요청 주시기 바랍니다.', 'order_fail.php?ordno='.$_GET['ordno'],'parent');
		exit();
	}
}

$auctionIpay = new integrate_order_processor_ipay();

// 결제완료 정보 수신
$result = $auctionIpay->GetIpayAccountNumb($_GET['ipayno']);

$ordno = $_GET['ordno'];

$settlelogAdd = "";
$TypeName = "";

$PayPrice = (int)$result['PayPrice'];

switch ($result['PaymentType']){
	case "A" :	// 무통장입금(가상계좌)
		if($PayPrice==0)
		{
			$settlelogAdd = PHP_EOL."옥션 적립금으로 결제".PHP_EOL;
		}
		else
		{
			$settlelogAdd = "
은 행 명 : ".$result['BankName']."
가상계좌 : ".$result['AcctNumb']."
결제마감일 : ".$result['ExpireDate']."
";
			$TypeName = "무통장입금(가상계좌)";
		}
		break;
	case "C" :	// 카드결제
		$settlelogAdd = "
승인일시 : ".$result['PayDate']."
할부기간 : ".$result['CardMonth']."
무이자할부 : ".$result['NoInterestYN']."
신용카드사 : ".$result['CardName']."
카드번호 : ".$result['CardNumb']."-****-****
";
		$TypeName = "신용카드결제";
		break;
	case "D" :	// 실시간계좌이체
		$settlelogAdd = "
승인일시 : ".$result['PayDate']."
";
		$TypeName = "실시간계좌이체";
		break;
	case "M" :	// 휴대폰결제
		$TypeName = "휴대폰결제";
		break;
}


$settlelog = $ordno." (".date('Y:m:d H:i:s').")
----------------------------------------
거래번호 : ".$result['OrderNo']."
결과내용 : 정상처리
지불방법 : ".$TypeName."
승인금액 : ".$result['PayPrice']."
----------------------------------------";

$settlelog .= $settlelogAdd."----------------------------------------";


### 가상계좌 결제의 재고 체크 단계 설정
$res_cstock = true;
if($cfg['stepStock'] == '1' && $result['PaymentType']=="A") $res_cstock = false;

### item check stock
include "../lib/cardCancel.class.php";
$pre_pg = $pg;	// pg 임시 저장
$pg = "ipay";
$pg['id'] = "ipay";
$cancel = new cardCancel();
if(!$cancel->chk_item_stock($ordno) && $res_cstock){
	$step = "51";
}
$pg = $pre_pg;	 // pg 복구

$oData = $db->fetch("select step, vAccount from ".GD_ORDER." where ordno='$ordno'");
if($oData['step'] > 0 || $oData['vAccount'] != ''){		// 중복결제
	### 로그 저장
	$db->query("update ".GD_ORDER." set settlelog=concat(ifnull(settlelog,''),'$settlelog') where ordno='$ordno'");
	go("../../order_end.php?ordno=$ordno&card_nm=$result[CardName]");

}elseif($step != 51){	// 결제성공
	$query = "
	select * from
		".GD_ORDER." a
		left join ".GD_LIST_BANK." b on a.bankAccount = b.sno
	where
		a.ordno='$ordno'
	";
	$data = $db->fetch($query);

	include "../lib/cart.class.php";

	$cart = new Cart($_COOKIE[gd_isDirect]);
	$cart->chkCoupon();
	$cart->delivery = $data[delivery];
	$cart->dc = $member[dc]."%";
	$cart->calcu();
	$cart -> totalprice += $data[price];

	### 주문확인메일
	$data[cart] = $cart;
	$data[str_settlekind] = $r_settlekind[$data[settlekind]];
	sendMailCase($data[email],0,$data);

	### 결제 정보 저장
	$step = 1;
	$qrc1 = "cyn='y', cdt=now(),";
	$qrc2 = "cyn='y',";

	### 가상계좌 결제시 계좌정보 저장
	if ($result['PaymentType']=="A"){
		$vAccount = $result['BankName']." ".$result['AcctNumb'];
		$step = 0; $qrc1 = $qrc2 = "";
	}

	### 현금영수증 저장

	### gd_order_item 에 아이페이주문번호(AuctionOrderNos) 값 처리
	$ItemNos = array();
	$AuctionOrderNos = array();

	$ItemNos = explode("@",$result['ItemNos']);
	$AuctionOrderNos = explode("@",$result['AuctionOrderNos']);

	for($i=0;$i<count($ItemNos)-1;$i++){
		$order_ItemSno = "";
		$order_ItemSno = split_betweenStr($ItemNos[$i],'_','=');
		$db->query("update ".GD_ORDER_ITEM." set ipay_ordno='$AuctionOrderNos[$i]' where sno='$order_ItemSno[0]'");
	}

	// 결제방법
	switch($result['PaymentType'])
	{
		// 가상계좌
		case 'A':
			$ipay_settlekind = 'v';
			break;
		// 신용카드
		case 'C':
			$ipay_settlekind = 'c';
			break;
		// 모바일
		case 'M':
			$ipay_settlekind = 'h';
			break;
		// 실시간 계좌이체
		case 'D':
			$ipay_settlekind = 'o';
			break;
	}

	// gd_order 테이블에 옥션 이머니, 포인트 차감되고난 나머지금액 업데이트
	$qrc1 .= "`settleprice`=".$result['PayPrice'].", `prn_settleprice`=".$result['PayPrice'].",";
	
	// 최종결제금액이 0원이면 전액할인 및 입금확인 처리
	if($PayPrice==0)
	{
		$step = 1;
		$ipay_settlekind = 'd';
		$qrc1 .= "`cyn`='y', `cdt`=NOW(),";
		$qrc2 .= "`cyn`='y',";
	}

	### 실데이타 저장
	$db->query("
	update ".GD_ORDER." set $qrc1
		step			= '$step',
		step2			= '',
		vAccount		= '$vAccount',
		`settlekind`	= '".$ipay_settlekind."',
		settlelog		= concat(ifnull(settlelog,''),'$settlelog'),
		cardtno			= '$result[OrderNo]',
		ipay_payno		= '$result[PayNo]',
		ipay_cartno		= '$result[IpayCartNo]'
	where ordno='$ordno'"
	);

	$ItemNos = explode('@', $result['ItemNos']);
	array_pop($ItemNos);
	$AuctionOrderNos = explode('@', $result['AuctionOrderNos']);
	array_pop($AuctionOrderNos);
	foreach($AuctionOrderNos as $key => $AuctionOrderNo)
	{
		$ItemNo = explode('=', $ItemNos[$key]);
		$option = explode('_', $ItemNo[0]);
		$db->query("
		UPDATE `".GD_ORDER_ITEM."` SET ".$qrc2."
			`istep`='".$step."',
			`ipay_ordno`='".$AuctionOrderNo."',
			`ipay_itemno`='".$ItemNo[1]."'
		WHERE `ordno`='".$ordno."' AND `sno`=".$option[1]
		);
	}

	### 주문로그 저장
	orderLog($ordno,$r_step2[$data[step2]]." > ".$r_step[$step]);

	### 재고 처리
	setStock($ordno);

	### 상품구입시 적립금 사용
	if ($data[m_no] && $data[emoney]){
		setEmoney($data[m_no],-$data[emoney],"상품구입시 적립금 결제 사용",$ordno);
	}

	### SMS 변수 설정
	$dataSms = $data;

	if ($result['PaymentType']!="A"){
		sendMailCase($data[email],1,$data);			### 입금확인메일
		sendSmsCase('incash',$data[mobileOrder]);	### 입금확인SMS
	} else {
		sendSmsCase('order',$data[mobileOrder]);	### 주문확인SMS
	}

	go("./order_end.php?ordno=$ordno&card_nm=$result[CardName]");

}else{		// 카드결제 실패
	if($step == '51'){
		$cancel -> cancel_db_proc($ordno);
	}else{
		$db->query("update ".GD_ORDER." set step2=54, settlelog=concat(ifnull(settlelog,''),'$settlelog') where ordno='$ordno'");
		$db->query("update ".GD_ORDER_ITEM." set istep=54 where ordno='$ordno'");
	}

	// Ncash 결제 승인 취소 API 호출
	if($naverNcash->useyn == 'Y') $naverNcash->payment_approval_cancel($ordno);

	go("./order_fail.php?ordno=$ordno");

}

?>