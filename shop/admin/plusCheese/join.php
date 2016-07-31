<?
$location = "플러스치즈 소셜쇼핑 > 플러스치즈 가입";
include "../_header.php";
?>
<div style="width:100%">
	<div class="title title_top">플러스치즈 소셜쇼핑 설정관리</div>

	<iframe src="http://admin.pluscheese.com/enterprise/godoRegister.do?godoID=<?=$_POST['key']?>" width="100%" height="800" frameborder="0"></iframe>
</div>
<? include "../_footer.php"; ?>