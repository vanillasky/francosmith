<?php
/*
	�߱� ����ī�� ���� ó�� ���(������ �����Ǿ����ϴ� ���� �ȳ�) ������

	noteurl ���� ��������,
	noteurl�� LG u+�� ���� ����� �����Ͽ� DB ó���ϴ� �������̸�, OK �� ��½� 2�ð� ���� 3�� �ֱ�� ��� ȣ���.
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


// �ؽ� ������ ����
$LGD_MERTKEY = $pg['cup_mertkey'];  //�����޿��� �߱��� ����Ű�� ������ �ֽñ� �ٶ��ϴ�.
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