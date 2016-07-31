<?

$location = "기본관리 > WebFTP 이미지관리";
include "../_header.php";
?>

<div class="title title_top">WebFTP 이미지관리 <span>내 쇼핑몰의 모든 이미지를 관리합니다</span>  <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=design&no=5')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<div style="padding-top:10;"></div>

<?
include "../design/webftp/main.php"; // WebFTP 메인
?>

<? include "../_footer.php"; ?>