<?
/**
 * @Path		: /shop/partner/auctionipay_ticket.php
 * @Description	: ���� iPay �ڵ带 �޾� ���� ����ִ� ���� iPay���� â�� ����Ű �κ��� ä����
 * @Author		: �ڱ���@������
 * @Since		: 2011.04.18 MON
 */

// create variable
$refer = $_SERVER['HTTP_REFERER'];
$ticket = ($_GET['ticket']) ? trim($_GET['ticket']) : "";
$return_url = ($_GET['return_url']) ? trim($_GET['return_url']) : "";


// error
if(!$ticket) exit("���� �������� ����Ű ���� ���۹��� ���߽��ϴ�.");
?>
<script language="JavaScript">
	// ooz
	opener.document.all.ticket.value = "<?php echo str_replace(' ', '+', $ticket); ?>";
	self.close();
</script>