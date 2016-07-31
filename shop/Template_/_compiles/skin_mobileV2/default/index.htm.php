<?php /* Template_ 2.2.7 2014/06/27 01:12:24 /www/francotr3287_godo_co_kr/shop/data/skin_mobileV2/default/index.htm 000003188 */ ?>
<?php $this->print_("header",$TPL_SCP,1);?>

<style type="text/css">
#background {
	position : fixed;
	left : 0;
	top : 0;
	bottom:0;
	width : 100%;
	height : 100%;
	background : rgba(0, 0, 0, 0);
	display:block;
	z-index:98;
}

#popup {position:fixed; bottom:0px; width:100%;z-index:99;}
#popup .popup_wrap{width:308px; margin:6px auto;}
#popup .popup_wrap .popup_content{width:306px; text-align:center; border:solid 1px #dadada; background:#FFFFFF; min-height:150px;}
#popup .popup_wrap .popup_content img{max-width:100%; }
#popup .popup_wrap .popup_btn {height:26px;}
#popup .popup_wrap .popup_btn .btn-today-close{height:26px; width:176px; background:url('/shop/data/skin_mobileV2/default/common/img/main/btn_p_today.png') no-repeat; float:left; line-height:26px; color:#FFFFFF; text-align:center;}
#popup .popup_wrap .popup_btn .btn-close{height:26px; width:132px; background:url('/shop/data/skin_mobileV2/default/common/img/main/btn_p_close.png') no-repeat;float:left; line-height:26px; color:#FFFFFF; text-align:center;}


</style>
<script type="text/javascript">
function closePop() {
	$("#popup").hide();
	$("#background").hide();
}

function closeTodayPop(popupNo) {
	setCookieMobile('popup_'+popupNo, 1, 1, '/');
	$("#popup").hide();
	$("#background").hide();

}
</script>
<?php if($TPL_VAR["popup_data"]){?>
<?php if(isset($_COOKIE['popup_'.$TPL_VAR["popup_data"]["mpopup_no"]])===false){?>
<div id="popup" >

<div class="popup_wrap">
<?php if($TPL_VAR["popup_data"]["link_url"]){?>
<a href="http://<?php echo $TPL_VAR["popup_data"]["link_url"]?>">
<?php }?>
<div class="popup_content">
<?php if($TPL_VAR["popup_data"]["popup_type"]=='0'){?>
	<?php echo $TPL_VAR["popup_data"]["popup_img"]?>

<?php }else{?>
	<?php echo $TPL_VAR["popup_data"]["popup_body"]?>

<?php }?>
</div>
<?php if($TPL_VAR["popup_data"]["link_url"]){?>
</a>
<?php }?>
<div class="popup_btn">
	<div class="btn-today-close" onClick="javascript:closeTodayPop('<?php echo $TPL_VAR["popup_data"]["mpopup_no"]?>');">오늘하루 닫기</div>
	<div class="btn-close" onClick="javascript:closePop();">닫기</div>
</div>
</div>

</div>
<!--
오늘 하루 보이지 않음 <input type="checkbox" style="cursor:pointer; background-color:#000000;" onClick="setCookieMobile('popup', 1, 1, '/'); $('div#popup').hide();">
-->
<div id="background"></div>
<?php }?>
<?php }?>

<?php if($GLOBALS["cfgMobileShop"]["mobileShopMainBanner"]){?>
<div class="main_banner content" ><img src="<?php echo $GLOBALS["cfg"]["rootDir"]?>/data/skin_mobileV2/<?php echo $GLOBALS["cfgMobileShop"]["tplSkinMobile"]?>/<?php echo $GLOBALS["cfgMobileShop"]["mobileShopMainBanner"]?>" alt="메인배너이미지" /></div>
<hr class="hidden" />
<?php }?>

<section id="main" class="content" >
	<script type="text/javascript">displayGoods('1', 'main');</script>
	<script type="text/javascript">displayGoods('2', 'main');</script>	
	<script type="text/javascript">displayGoods('3', 'main');</script>
</section>
<?php $this->print_("footer",$TPL_SCP,1);?>