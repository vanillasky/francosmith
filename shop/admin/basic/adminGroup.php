<?
$location = "�⺻���� > �����ڱ׷���Ѽ���";
include "../_header.php";
include "../../lib/page.class.php";
$adminAuth = 1;
?>
<div class="title title_top">�����ڱ׷���Ѽ���<span>�������� ������ ������ �� �ֽ��ϴ�. <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=19')"><img src="../img/btn_q.gif" border=0 hspace=2 align=absmiddle></a></div>
<?
include './adminAccountSecureGuide.php';
include "../member/_groupForm.php";
?>
<div style='padding-top:25'></div>
<?
include "../basic/_adminList.php";
?>
<?include_once "../_footer.php"; ?>