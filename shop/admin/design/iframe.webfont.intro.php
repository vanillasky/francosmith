<?php
include "../_header.popup.php";

?>



<div class="title title_top">폰트 이용 안내<span></span></div>


<iframe name='introduce' src='http://www.godo.co.kr/service/font_info.php?iframe=yes&shopHost=<?=$_SERVER['HTTP_HOST']?>&shopSno=<?=$godo['sno']?>' frameborder='0' marginwidth='0' marginheight='0' width='100%' height='1200'></iframe>


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
		if(menus[i].href && /iframe\.webfont\.intro\.php/.test(menus[i].href)) {
			menus[i].style.fontWeight='bold';
		}
		else {
			menus[i].style.fontWeight='';
		}
	}
});
</script>