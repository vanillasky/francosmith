<?php
include "../_header.popup.php";


?>

<div class="title title_top">��Ʈ ���� ��û<span></span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=design&no=15')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a></div>

<? if($_GET['code']): ?>
	<iframe name='introduce' src='http://www.godo.co.kr/userinterface/_font/
fontGoods.php?pageKey=<?=base64_encode($godo['sno'])?>&recom=yes&code=<?=$_GET['code']?>' frameborder='0' marginwidth='0' marginheight='0' width='100%' height='1100'></iframe>
<? else: ?>
	<iframe name='introduce' src='http://www.godo.co.kr/userinterface/_font/fontService.php?pageKey=<?=base64_encode($godo['sno'])?>' frameborder='0' marginwidth='0' marginheight='0' width='100%' height='1100'></iframe>
<? endif; ?>







<script>
table_design_load();
setHeight_ifrmCodi();
document.observe("dom:loaded", function() {
	parent.document.getElementById('leftfooter').src = "../img/footer_left.gif";
	parent._ID('sub_left_menu').style.display = "none";
	parent._ID('btn_menu').style.display = "none";
	parent._ID('leftMenu').style.display = "block";
	
	var menus = parent.document.getElementsByName("navi");
	for(i=0;i<menus.length;i++) {
		if(menus[i].href && /iframe\.webfont\.buy\.php/.test(menus[i].href)) {
			menus[i].style.fontWeight='bold';
		}
		else {
			menus[i].style.fontWeight='';
		}
	}
});
</script>