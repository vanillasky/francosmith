<?
$location = "하이! eBay > 상용어구 관리";
include "../_header.php";
include_once "./checker.php";
?>
<iframe name="hiebayFrame" src="<?=$fsConfig['apiUrl']?>/godo-translate-common-list?token=<?=$fsConfig['token']?>" frameborder="0" marginwidth="0" marginheight="0" width="100%" height="500" scrolling="no"></iframe>
<?include "../_footer.php"; ?>
