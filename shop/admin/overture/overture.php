<?
$location = "오버추어 관리 > 오버추어 신청";
include "../_header.php";

//쇼핑몰 정보!!
$send_cfg = array(
'settlePg'=>$cfg['settlePg'],
'shopName'=>$cfg['shopName'],
'adminEmail'=>$cfg['adminEmail'],
'shopUrl'=>$cfg['shopUrl'],
'compName'=>$cfg['compName'],
'service'=>$cfg['service'],
'item'=>$cfg['item'],
'zipcode'=>$cfg['zipcode'],
'address'=>$cfg['address'],
'compSerial'=>$cfg['compSerial'],
'orderSerial'=>$cfg['orderSerial'],
'ceoName'=>$cfg['ceoName'],
'adminName'=>$cfg['adminName'],
'compPhone'=>$cfg['compPhone'],
'compFax'=>$cfg['compFax']
);
$enamoo_serialize = serialize($send_cfg);
?>

<iframe name='inoverture' src='' frameborder='0' marginwidth='0' marginheight='0' width='100%' height='2100'></iframe>

<form name='overtureFm' method='post' action='http://www.godo.co.kr/service/overture_service_register.php?iframe=yes&shopSno=<?=$godo['sno']?>&shopHost=<?=$_SERVER['HTTP_HOST']?>'>
<input type='hidden' name='send_cfg' value='<?=$enamoo_serialize?>'>
</form>

<script language="javascript">
document.overtureFm.target = "inoverture";
document.overtureFm.submit();
</script>

<? include "../_footer.php"; ?>