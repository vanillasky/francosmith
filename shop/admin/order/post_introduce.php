<?php

$location = "택배연동 서비스 > 우체국택배 연동 안내";

include "../_header.php";
include "../../lib/page.class.php";
include "../../conf/config.pay.php";
include "../../lib/godopost.class.php";


?>

<div class="title title_top">우체국택배 연동 안내</div>


<iframe name='introduce' src='http://www.godo.co.kr/service/godopost/step1.php?iframe=yes&shopSno=<?=$godo['sno']?>&shopHost=<?=$_SERVER['HTTP_HOST']?>' frameborder='0' marginwidth='0' marginheight='0' width='100%' height='2100'></iframe>
<?include "../_footer.php";?>