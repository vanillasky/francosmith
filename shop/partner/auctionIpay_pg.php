<?php
/**
 * @Path		: /shop/partner/auctionIpay_pg.php
 * @Description	: ���� iPay �������� ���� �ֹ�ó�� ������
 * @Author		: ������@������
 * @Since		: 2012.01.19
 */

include "../lib/library.php";

if (get_magic_quotes_gpc()) {
	stripslashes_all($_POST);
	stripslashes_all($_GET);
}

if(is_numeric($_GET['ipayno']) && is_numeric($_GET['ordno'])){	// ipay �ֹ���ȣ�� , godo �ֹ���ȣ�� ������ ��.
	echo '
	<script type="text/javascript">
	opener.parent.location.href = "../order/ipay_order_indb.php?ordno='.$_GET['ordno'].'&ipayno='.$_GET['ipayno'].'";
	self.close();
	</script>';
}else{	// ipay �ֹ���ȣ�� �������� ���� ��.

	$settlelog = "
-----------------------------
 ����iPay �ֹ���ȣ ���� ����
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