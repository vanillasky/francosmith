<?php

$location = "�ù迬�� ���� > ��ü���ù�����";

include "../_header.php";
include "../../lib/page.class.php";
include "../../conf/config.pay.php";
include "../../lib/godopost.class.php";

$godopost = new godopost();

if (get_magic_quotes_gpc()) {
	stripslashes_all($_POST);
	stripslashes_all($_GET);
}
$godopost->linked=1;
?>
<script type="text/javascript">
function popupGodoPostManualConfirm(ordno) {
	popupLayer('popup.godopost.manualconfirm.php');
}
</script>
<div class="title title_top">��ü���ù� ��û/����<span>��ü���ù�  �ڵ� �������񽺸� ��û/ �����ϴ� ������ �Դϴ�.</span></div>

<br><br>

<iframe name="requestPostIfrm" src="http://www.godo.co.kr/service/godopost/regist.php?shopSno=<?=$godo['sno']?>&shopHost=<?=$_SERVER['HTTP_HOST']?>" frameborder="0" style="width:100%;height;500px" width="100%" height="500"></iframe>

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">��ü�� �ù迬�� ���񽺸� ��û�Ͻø�, ����ڰ� Ȯ�� �� ����ó���� �ص帳�ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">��û ��, ���οϷ���� �� 2~3�� ���� �ҿ�˴ϴ�. </td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">��ü�� �ù迬�� ���� ��û ���� ��ü�� ����ù� ���� �� ��ü�� �����ȸ�������� �Ϸ�Ǽž� �մϴ�.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>
<br><br>
<? if($godopost->linked): ?>
<div class="title">��ü���ù� ��ۻ��� �ڵ� ������Ʈ<span></span> </div>
- ��ü���ù� ��ۻ��¸� ���θ��� 2�ð����� �ڵ����� ������Ʈ �մϴ�.<br>
- 2�ð����� �ڵ����� ��ۻ��¸� Ȯ���Ͽ� ����� �Ϸ�� �ֹ��� ����ۿϷᡯ�� ������Ʈ �˴ϴ�.<br>
<br>
<br>
<br>
<div class="title">��ü���ù� ��ۻ��� ���� ������Ʈ<span></span> </div>

<div style="margin:5px;text-indent:5px">
- ��ü���ù� ��ۻ��¸� ���θ��� ���� ������Ʈ�Ϸ���, �Ʒ� ��ư�� Ŭ���� �ּ���.<br>
- �������� ��ۻ��¸� Ȯ���Ͽ� ����� �Ϸ�� �ֹ��� ����ۿϷᡯ�� ������Ʈ �˴ϴ�.<br> 
</div>

<input type="button" value=" ��ۻ��� ���� ������Ʈ " onclick="popupGodoPostManualConfirm()">
<? endif; ?>


<? include "../_footer.php"; ?>