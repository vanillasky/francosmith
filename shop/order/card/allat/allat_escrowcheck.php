<?

// 올앳관련 함수 Include
include "../../../lib/library.php";
include "../../../conf/pg.allat.php";
include "./allatutil.php";

// 결제인터페이스의 결과값 Get : 이전 주문결제페이지에서 Request Get
$at_shop_id   = $pg[id];
$at_cross_key = $pg[crosskey];

$at_data   = "allat_shop_id=".urlencode($at_shop_id).
			 "&allat_enc_data=".$_POST["allat_enc_data"].
			 "&allat_cross_key=".$at_cross_key ;
$at_txt = EscrowChkReq($at_data,$pg[ssl]); //설정 필요 https(SSL),http(NOSSL)

$REPLYCD   =getValue("reply_cd",$at_txt);
$REPLYMSG  =getValue("reply_msg",$at_txt);

if( !strcmp($REPLYCD,"0000") ){	// reply_cd "0000" 일때만 성공
	$ESCROWCHECK_YMDSHMS=getValue("escrow_check_ymdhms",$at_txt);
	echo "결과코드  : ".$REPLYCD."<br>";
	echo "결과메세지: ".$REPLYMSG."<br>";
	echo "에스크로 배송 개시일 : ".$ESCROWCHECK_YMDSHMS."<br>";

	$db->query("update ".GD_ORDER." set escrowconfirm=1 where ordno='$_POST[allat_order_no]'");

} else {	// reply_cd 가 "0000" 아닐때는 에러 (자세한 내용은 매뉴얼참조) // reply_msg 가 실패에 대한 메세지
	echo "결과코드  : ".$REPLYCD."<br>";
	echo "결과메세지: ".$REPLYMSG."<br>";
}

?>

<script>alert("<?=$REPLYMSG?>");</script>