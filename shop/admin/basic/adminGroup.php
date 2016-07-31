<?
$location = "기본관리 > 관리자그룹권한설정";
include "../_header.php";
include "../../lib/page.class.php";
$adminAuth = 1;
?>
<div class="title title_top">관리자그룹권한설정<span>관리자의 권한을 설정할 수 있습니다. <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=19')"><img src="../img/btn_q.gif" border=0 hspace=2 align=absmiddle></a></div>
<?
include './adminAccountSecureGuide.php';
include "../member/_groupForm.php";
?>
<div style='padding-top:25'></div>
<?
include "../basic/_adminList.php";
?>
<?include_once "../_footer.php"; ?>