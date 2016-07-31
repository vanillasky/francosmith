<?

### 데이콤 (Noteurl_Link_PHP)
### 소켓결제결과를 처리합니다.

include "../../../lib/library.php";
include "../../../conf/config.php";
include "../../../conf/pg.dacom.php";

// 투데이샵 사용중인 경우 PG 설정 교체
resetPaymentGateway();

foreach ( $_POST as $k => $v ) $_POST[$k] = trim( $v );
extract($_POST);


$ordno = $oid;


### 결제로그 저장
/******************************************************************************
//공통사용
transaction					# 거래번호
mid							# 상점아이디
oid							# 주문번호
amount						# 금액
respcode					# 응답코드 "0000" 또는 "C000" 이면 성공 이외는 실패
respmsg						# 응답메세지

//신용카드
authdate					# 승인일자 (yyyyMMDDHHMMSS)
authnumber					# 승인번호
cardtype					# 카드 타입
cardname					# 카드 종류

//계좌이체
accountNum					# 계좌번호
userName					# 계좌소유주 이름
bankcode					# 은행코드
pid							# 계좌소유주 주민등록번호
respDate					# 결제일자 (yyyyMMDDHHMMSS


//핸드폰
respDate					# 결제일자 (yyyyMMDDHHMMSS)
email						# 휴대폰결제시 입력한 메일주소(결제결과통보)
telCo						# 이동통신사 (1:SKT, 2:KTF, 3:LGT)
telNo1						# 휴대폰번호1
telNo2						# 휴대폰번호2
telNo3						# 휴대폰번호3
*******************************************************************************/

### item check stock
include "../../../lib/cardCancel.class.php";
$cancel = new cardCancel();
if(!$cancel->chk_item_stock($ordno))$respcode="OUTOFSTOCK";

if( !strcmp($respcode,"0000") || !strcmp($respcode,"C000") || !strcmp($respcode,"S007") ){		// 결제 성공

	### 메일전송 (주문확인메일/입금확인메일)
	$pre = $db->fetch("select * from ".GD_ORDER." where ordno='$ordno'");
	$step = $pre['step'];

	if ($step == 0 && $cfg["mailyn_0"] == "y"){
		$pre['str_settlekind'] = $r_settlekind[ $pre['settlekind'] ];
		if ($pre['settlekind'] == 'v') $pre['str_settlekind'] .= ' ('. $pre['vAccount'] .')';

		$tCart = (object) $tCart;
		$query = "
		select a.*, b.img_s img from
			".GD_ORDER_ITEM." a
			left join ".GD_GOODS." b on a.goodsno=b.goodsno
		where
			a.ordno = '$ordno'
		";
		$res = $db->query($query);
		while ($item=$db->fetch($res)){
			if ($item['opt1']) $item['opt'] = array($item['opt1'], $item['opt2']);
			if ($item['addopt']){
				$tmp1 = explode("^", $item['addopt']);
				$item['addopt'] = array();
				foreach ($tmp1 as $v){
					$tmp2 = explode(":", $v);
					$item['addopt'][] = array('optnm' => $tmp2[0], 'opt' => $tmp2[1]);
				}
			}
			$tCart->item[] = $item;
			$goodsprice += $item['price'] * $item['ea'];
		}
		$tCart->goodsprice = $goodsprice;
		$tCart->delivery = $pre['delivery'];
		$tCart->totalprice = $goodsprice + $pre['delivery'];

		include_once "../../../lib/automail.class.php";
		$automail = new automail();
		$automail->_set(0,$pre['email'],$cfg);
		$automail->_assign($pre);
		$automail->_assign('cart',$tCart);
		$automail->_send();
	}
	else if ($step == 1 && $cfg["mailyn_1"] == "y"){
		//sendMailCase($pre['email'],1,$pre);
	}

	### SMS전송 (주문확인SMS/입금확인SMS)
	if ($step == 1 || $step == 0){
		$GLOBALS['dataSms'] = $pre;
		//sendSmsCase(($step == 0 ? 'order' : 'incash'), $pre['mobileOrder']);
	}

	if ($step == 1) {

		// 투데이샵 입금 확인 sms & 메일 그리고 쿠폰 발급
		$todayshop_noti = &load_class('todayshop_noti', 'todayshop_noti');
		$formatter = &load_class('stringFormatter', 'stringFormatter');

		$todayshop_noti->set($ordno,'order');
		$todayshop_noti->send();

		// 즉시 발급 쿠폰 생성 및 문자 전송 (todayshop_noti 클래스는 todayshop 을 상속받았기 때문에 멤버를 사용해도 됨)
		if (($cp_sno = $todayshop_noti->publishCoupon($ordno)) !== false) {

			if ($phone = $formatter->get($pre['mobileReceiver'],'dial','-')) {
				$db->query("UPDATE ".GD_TODAYSHOP_ORDER_COUPON." SET cp_publish = 1 WHERE cp_sno = '$cp_sno'");	// 발급 처리
				ctlStep($ordno,4,1);
			}

		}
		// eof 투데이샵 쿠폰 발급
	}



	go("../../order_end.php?ordno=$ordno&card_nm=$cardname","parent");

} else {							// 결제 실패

	if ($respcode == "OUTOFSTOCK") {
		$cancel->cancel_db_proc($ordno);
	}
	else {
		$db->query("update ".GD_ORDER." set step2='54' where ordno='".$ordno."'");
		$db->query("update ".GD_ORDER_ITEM." set istep='54' where ordno='".$ordno."'");
	}

	go("../../order_fail.php?ordno=$ordno","parent");

}

?>