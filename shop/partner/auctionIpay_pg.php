<?php
/**
 * @Path		: /shop/partner/auctionIpay_pg.php
 * @Description	: 옥션 iPay 결제대행 서비스 주문처리 페이지
 * @Author		: 서혜진@개발팀
 * @Since		: 2012.01.19
 */

include "../lib/library.php";

if (get_magic_quotes_gpc()) {
	stripslashes_all($_POST);
	stripslashes_all($_GET);
}

if(is_numeric($_GET['ipayno']) && is_numeric($_GET['ordno'])){	// ipay 주문번호와 , godo 주문번호가 존재할 때.
	echo '
	<script type="text/javascript">
	opener.parent.location.href = "../order/ipay_order_indb.php?ordno='.$_GET['ordno'].'&ipayno='.$_GET['ipayno'].'";
	self.close();
	</script>';
}else{	// ipay 주문번호가 존재하지 않을 때.

	$settlelog = "
-----------------------------
 옥션iPay 주문번호 생성 실패
-----------------------------
";

	$db->query("update ".GD_ORDER." set step2=54, settlelog=concat(ifnull(settlelog,''),'$settlelog') where ordno='$_GET[ordno]'");
	$db->query("update ".GD_ORDER_ITEM." set istep=54 where ordno='$_GET[ordno]'");

	echo '
	<script type="text/javascript">
	opener.parent.location.href = "../order/order_fail.php?ordno='.$_GET['ordno'].'&ipayno='.$_GET['ipayno'].'";
	self.close();
	</script>';

}
?>