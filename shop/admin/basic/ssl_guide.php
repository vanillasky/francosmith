<?
$location = "�⺻���� > ���ȼ��� �������� �ȳ�";
include "../_header.php";
//http://www.godo.co.kr/service/sub_06_secure_server.php
if($_SERVER[HTTPS] == 'on')$ptc = "https://";
else $ptc = "http://";
?>
<div class="title title_top">���ȼ��� �������� �ȳ�<span> </div>
<table width=100% cellpadding=0 cellspacing=0>
	<iframe name='chatting' src="<?=$ptc?>www.godo.co.kr/service/sub_06_secure_server.php?iframe=yes" frameborder="0" marginwidth="0" marginheight="0" width="100%" height="1500"></iframe>
</table>
<div style="padding-top:15px"></div>

<? include "../_footer.php"; ?>