<?

// �þܰ��� �Լ� Include
include "../../../lib/library.php";
include "../../../conf/pg.allat.php";
include "./allatutil.php";

// �����������̽��� ����� Get : ���� �ֹ��������������� Request Get
$at_shop_id   = $pg[id];
$at_cross_key = $pg[crosskey];

$at_data   = "allat_shop_id=".urlencode($at_shop_id).
			 "&allat_enc_data=".$_POST["allat_enc_data"].
			 "&allat_cross_key=".$at_cross_key ;
$at_txt = EscrowChkReq($at_data,$pg[ssl]); //���� �ʿ� https(SSL),http(NOSSL)

$REPLYCD   =getValue("reply_cd",$at_txt);
$REPLYMSG  =getValue("reply_msg",$at_txt);

if( !strcmp($REPLYCD,"0000") ){	// reply_cd "0000" �϶��� ����
	$ESCROWCHECK_YMDSHMS=getValue("escrow_check_ymdhms",$at_txt);
	echo "����ڵ�  : ".$REPLYCD."<br>";
	echo "����޼���: ".$REPLYMSG."<br>";
	echo "����ũ�� ��� ������ : ".$ESCROWCHECK_YMDSHMS."<br>";

	$db->query("update ".GD_ORDER." set escrowconfirm=1 where ordno='$_POST[allat_order_no]'");

} else {	// reply_cd �� "0000" �ƴҶ��� ���� (�ڼ��� ������ �Ŵ�������) // reply_msg �� ���п� ���� �޼���
	echo "����ڵ�  : ".$REPLYCD."<br>";
	echo "����޼���: ".$REPLYMSG."<br>";
}

?>

<script>alert("<?=$REPLYMSG?>");</script>