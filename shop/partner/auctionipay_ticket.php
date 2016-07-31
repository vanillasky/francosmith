<?
/**
 * @Path		: /shop/partner/auctionipay_ticket.php
 * @Description	: 옥션 iPay 코드를 받아 현재 띄워있는 옥션 iPay설정 창의 인증키 부분을 채워줌
 * @Author		: 박규진@개발팀
 * @Since		: 2011.04.18 MON
 */

// create variable
$refer = $_SERVER['HTTP_REFERER'];
$ticket = ($_GET['ticket']) ? trim($_GET['ticket']) : "";
$return_url = ($_GET['return_url']) ? trim($_GET['return_url']) : "";


// error
if(!$ticket) exit("옥션 아이페이 인증키 값을 전송받지 못했습니다.");
?>
<script language="JavaScript">
	// ooz
	opener.document.all.ticket.value = "<?php echo str_replace(' ', '+', $ticket); ?>";
	self.close();
</script>