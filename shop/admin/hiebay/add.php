<?
$location = "하이! eBay > eBay 신규상품 만들기";
include "../_header.php";
include_once "./checker.php";
?>
<iframe name="hiebayFrame" src="<?=$fsConfig['apiUrl']?>/godo-inventory-new?token=<?=$fsConfig['token']?>" frameborder="0" marginwidth="0" marginheight="0" width="100%" height="500" scrolling="no"></iframe>
<?include "../_footer.php"; ?>
