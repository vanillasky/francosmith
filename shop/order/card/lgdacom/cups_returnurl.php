<?php
/*
	중국 은련카드 결제 처리 결과(결제가 성공되었습니다 등의 안내) 페이지

	noteurl 과의 차이점은,
	noteurl는 LG u+의 결제 결과를 수신하여 DB 처리하는 페이지이며, OK 미 출력시 2시간 동안 3분 주기로 계속 호출됨.
*/
include "../../../lib/library.php";
include "../../../conf/config.php";
include "../../../conf/pg.lgdacom.php";


$LGD_RESPCODE            = $HTTP_POST_VARS["LGD_RESPCODE"];
$LGD_RESPMSG             = $HTTP_POST_VARS["LGD_RESPMSG"];
$LGD_MID                 = $HTTP_POST_VARS["LGD_MID"];
$LGD_OID                 = $HTTP_POST_VARS["LGD_OID"];
$LGD_AMOUNT              = $HTTP_POST_VARS["LGD_AMOUNT"];
$LGD_TID                 = $HTTP_POST_VARS["LGD_TID"];
$LGD_PAYTYPE             = $HTTP_POST_VARS["LGD_PAYTYPE"];
$LGD_PAYDATE             = $HTTP_POST_VARS["LGD_PAYDATE"];
$LGD_HASHDATA            = $HTTP_POST_VARS["LGD_HASHDATA"];
$LGD_FINANCECODE         = $HTTP_POST_VARS["LGD_FINANCECODE"];
$LGD_FINANCENAME         = $HTTP_POST_VARS["LGD_FINANCENAME"];
$LGD_TIMESTAMP           = $HTTP_POST_VARS["LGD_TIMESTAMP"];

$LGD_BUYER               = $HTTP_POST_VARS["LGD_BUYER"];
$LGD_PRODUCTINFO         = $HTTP_POST_VARS["LGD_PRODUCTINFO"];
$LGD_BUYERID             = $HTTP_POST_VARS["LGD_BUYERID"];
$LGD_BUYERADDRESS        = $HTTP_POST_VARS["LGD_BUYERADDRESS"];
$LGD_BUYERPHONE          = $HTTP_POST_VARS["LGD_BUYERPHONE"];
$LGD_BUYEREMAIL          = $HTTP_POST_VARS["LGD_BUYEREMAIL"];
$LGD_BUYERSSN            = $HTTP_POST_VARS["LGD_BUYERSSN"];
$LGD_PRODUCTCODE         = $HTTP_POST_VARS["LGD_PRODUCTCODE"];
$LGD_RECEIVER            = $HTTP_POST_VARS["LGD_RECEIVER"];
$LGD_RECEIVERPHONE       = $HTTP_POST_VARS["LGD_RECEIVERPHONE"];
$LGD_DELIVERYINFO        = $HTTP_POST_VARS["LGD_DELIVERYINFO"];


// 해시 데이터 검증
$LGD_MERTKEY = $pg['cup_mertkey'];  //데이콤에서 발급한 상점키로 변경해 주시기 바랍니다.
$LGD_HASHDATA2 = md5($LGD_MID.$LGD_OID.$LGD_AMOUNT.$LGD_RESPCODE.$LGD_TIMESTAMP.$LGD_MERTKEY);

if ( $LGD_HASHDATA2 == $LGD_HASHDATA && $LGD_RESPCODE == '0000') {
	$location = "../../order_end.php?ordno=$LGD_OID&card_nm=$LGD_FINANCENAME";
} else {
	$location = "../../order_fail.php?ordno=$LGD_OID";
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
 <head>
  <title> return </title>
 </head>
 <body>
<script type="text/javascript">
opener.location.href = '<?=$location?>';
self.close();
</script>
 </body>
</html>