<?

$location = '현금영수증 서비스 > 현금영수증 서비스 안내';
include '../_header.php';

?>

<div class="title title_top">현금영수증 서비스 안내 <span>현금영수증 서비스에 대한 안내해 드리는 컨텐츠 페이지입니다</span></div>

<iframe name="innaver" src="http://www.godo.co.kr/service/cashreceipt.php?iframe=yes&ifrParentDomain=<?=$_SERVER['SERVER_NAME']?>&ecCode=<?=$godo['ecCode']?>" frameborder="0" marginwidth="0" marginheight="0" width="100%" height="500"></iframe>

<? include "../_footer.php"; ?>