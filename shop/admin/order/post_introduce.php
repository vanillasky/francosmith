<?php

$location = "�ù迬�� ���� > ��ü���ù� ���� �ȳ�";

include "../_header.php";
include "../../lib/page.class.php";
include "../../conf/config.pay.php";
include "../../lib/godopost.class.php";


?>

<div class="title title_top">��ü���ù� ���� �ȳ�</div>


<iframe name='introduce' src='http://www.godo.co.kr/service/godopost/step1.php?iframe=yes&shopSno=<?=$godo['sno']?>&shopHost=<?=$_SERVER['HTTP_HOST']?>' frameborder='0' marginwidth='0' marginheight='0' width='100%' height='2100'></iframe>
<?include "../_footer.php";?>