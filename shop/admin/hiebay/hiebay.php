<?
$location = "����! eBay > ����! eBay �����ϱ�";
include "../_header.php";
include_once "./checker.php";
if($_GET['p']) $p = "&p=".$_GET['p'];
?>
<iframe name="hiebayFrame" src="<?=$fsConfig['apiUrl']?>/r?token=<?=$fsConfig['token'].$p?>" frameborder="0" marginwidth="0" marginheight="0" width="100%" height="500" scrolling="no"></iframe>
<?include "../_footer.php"; ?>
