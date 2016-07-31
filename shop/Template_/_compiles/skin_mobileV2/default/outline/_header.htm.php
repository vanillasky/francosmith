<?php /* Template_ 2.2.7 2014/05/05 02:24:14 /www/francotr3287_godo_co_kr/shop/data/skin_mobileV2/default/outline/_header.htm 000009083 */ 
if (is_array($TPL_VAR["mobile_script"])) $TPL_mobile_script_1=count($TPL_VAR["mobile_script"]); else if (is_object($TPL_VAR["mobile_script"]) && in_array("Countable", class_implements($TPL_VAR["mobile_script"]))) $TPL_mobile_script_1=$TPL_VAR["mobile_script"]->count();else $TPL_mobile_script_1=0;?>
<!DOCTYPE html>
<head>
<meta name="description" content="<?php echo $GLOBALS["meta_title"]?>" />
<meta name="keywords" content="<?php echo $GLOBALS["meta_keywords"]?>" />
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
<meta name="viewport" content="user-scalable=yes, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, width=device-width, height=device-height" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />

<title><?php echo $GLOBALS["meta_title"]?></title>

<script src="/shop/data/skin_mobileV2/default/common/js/common.js"></script>
<script src="/shop/data/skin_mobileV2/default/common/js/goods_list_action.js"></script>
<script src="/shop/data/skin_mobileV2/default/common/js/jquery-1.4.2.min.js"></script>
<script src="/shop/data/skin_mobileV2/default/common/js/jquery.mobile-1.1.1.js"></script>
<script src="/shop/data/skin_mobileV2/default/common/js/jquery.cookie.js"></script>

<?php if($TPL_mobile_script_1){foreach($TPL_VAR["mobile_script"] as $TPL_V1){?>
<script src="<?php echo $TPL_V1["script_file"]?>"></script>
<?php }}?>

<script type="text/javascript">
function showSearchArea() {
	if($(".search-area").is(':hidden') == true) {

		$(".search-area").slideDown(30);
	}
	else {

		$(".search-area").slideUp(0);
	}
}

function showCateArea() {

	var now_cate = $("[name=now_cate]").val();

	if($("#cate-area").is(':hidden') == true) {
		$("#page_title").hide();
		$(".content").hide();
		$(".content_goods").hide();
		$("body").addClass('back_bg');
		$("#cate-area").slideDown(30);
		getCategoryData(now_cate);
	}
	else {
		$("#cate-area").hide();
		$("body").removeClass('back_bg');
		$("#page_title").show();
		$(".content").show();

	}
}

function goHome() {
	document.location.href="/" + getMobileHomepath() + "/index.php";
}

function goMenu(page_nm) {
	switch(page_nm) {
		case "my" :
			document.location.href="/" + getMobileHomepath() + "/myp/menu_list.php";
			break;
		case "cart" :
			document.location.href="/" + getMobileHomepath() + "/goods/cart.php";
			break;
		case "wish" :
			document.location.href="/" + getMobileHomepath() + "/myp/wishlist.php";
			break;
		case "login" :
			document.location.href="/" + getMobileHomepath() + "/mem/login.php";
			break;
		case "logout" :
				document.location.href="/" + getMobileHomepath() + "/mem/logout.php";
			break;
		case "viewgoods" :
				document.location.href="/" + getMobileHomepath() + "/myp/viewgoods.php";
			break;
	}
}

function searchKw() {
	if($("[name=search_key]").val()) {
		document.location.href="/" + getMobileHomepath() + "/goods/list.php?kw=" + $("[name=search_key]").val();
	}
	else {
		alert("검색 키워드를 입력해 주시기 바랍니다");
		return;
	}
}

function getMobileHomepath() {
	//각 URL 최상위 홈PATH를 구한다. 모바일의 홈이 여러 종류일수 있으므로  2012-09-20 khs
	var path1 = document.location.pathname;

	if (path1.charAt(0) == '/')	{
		path1 = path1.substring(1);
	}
	var x = path1.split("/");

	return x[0];
}

function showCategoryMsg(message) {

	var sec = 1000;

	$("[id=goodsres-hide] .text_msg").text(message);
	$("[id=goodsres-hide]").fadeIn(300);

	setTimeout( function() {
		$("[id=goodsres-hide]").fadeOut(300);
	}, sec);
}

$(document).ready(function(){
	$.ajax({
		"url" : "<?php echo $GLOBALS["mobileRootDir"]?>/proc/mAjaxAction.php",
		"type" : "post",
		"data" : {
			"mode" : "get_cart_item"
		},
		"cash" : false,
		"dataType" : "json",
		"success" : function(cartItem)
		{
			if (cartItem.quantity) {
				$("#cart-btn .cart-item-quantity").text(" ("+cartItem.quantity.toString()+")");
			}
		}
	});

	try {
		var todayGoodsMobileIdx = $.cookie('todayGoodsMobileIdx');
		
		if(todayGoodsMobileIdx != "undefined" && todayGoodsMobileIdx != "") {
			
			var goods_idx = todayGoodsMobileIdx.split(',');
			var view_cnt = goods_idx.length - 1;
			$("#viewgoods-btn .viewgoods-quantity").text(" ("+view_cnt.toString()+")");
		}
	}
	catch(e) {}

	

});

</script>
<link rel="stylesheet" type="text/css" href="/shop/data/skin_mobileV2/default/common/css/reset.css" />
<link rel="stylesheet" type="text/css" href="/shop/data/skin_mobileV2/default/common/css/style.css" />
<style type="text/css">
.cart-item-quantity{font-weight:normal;}

#goodsres-hide2 {display:none;} 
.goodsres_wrap {position : fixed;left : 10%;width : 80%;background : #ffffff;display : block;border-radius:1em;box-shadow:2px 2px 4px #7f7f7f;z-index: 1000; bottom:20%;}
.goodsres_wrap .goodsres_title {background:#313030;width:100%;border-top-left-radius:1em;border-top-right-radius:1em;height:45px;border-bottom:solid 1px #b2b2b2;margin-bottom:6px;}
.goodsres_wrap .goodsres_title .title{padding-left:14px;line-height:45px;font-size:16px;font-weight:bold;color:#FFFFFF;font-family:dotum;}
.goodsres_wrap .goodsres_msg { padding:15px; }
.goodsres_wrap .goodsres_msg .text_msg{ font-size:12px; color:#353535;}

</style>
<!--<link rel="stylesheet" href="/shop/data/skin_mobileV2/default/style_screen.css" type="text/css" media="screen and (min-width: 321px)"  />-->

<?php if($GLOBALS["cfgMobileShop"]["mobileShopIcon"]){?><link rel="apple-touch-icon-precomposed" href="<?php echo $GLOBALS["cfg"]["rootDir"]?>/data/skin_mobileV2/<?php echo $GLOBALS["cfgMobileShop"]["tplSkinMobile"]?>/<?php echo $GLOBALS["cfgMobileShop"]["mobileShopIcon"]?>" /><?php }?>

<?php echo $TPL_VAR["customHeader"]?>

</head>

<body>

<div id="dynamic"></div>

<div id="wrap">

<header>
<div class="gnb">
	<div id="home-btn" onClick="javascript:goHome();"><img src="/shop/data/skin_mobileV2/default/common/img/gnb/btn_home_object.png" /></div>
	<div id="logo">
<?php if($GLOBALS["cfgMobileShop"]["mobileShopLogo"]){?>
		<a href="<?php echo $GLOBALS["mobileRootDir"]?>"><img src="/shop/data/images/top_logo-mobile.jpg" alt="<?php echo $GLOBALS["cfg"]["shopName"]?>" title="<?php echo $GLOBALS["cfg"]["shopName"]?>" width="110px" height="35px"/></a>
<?php }else{?>
		<div class="top_title"><a href="<?php echo $GLOBALS["mobileRootDir"]?>"><?php echo $GLOBALS["shop_name"]?></a></div>
<?php }?>
		<div class="top_global" ><?php if($GLOBALS["sess"]){?><span onClick="javascript:goMenu('logout');" >로그아웃</span><?php }else{?><span onClick="javascript:goMenu('login');" >로그인</span><?php }?> | <span onClick="javascript:goMenu('my');" >마이페이지</span></div>
	</div>

	<div id="search-btn" onClick="javascript:showSearchArea();"><img src="/shop/data/skin_mobileV2/default/common/img/gnb/btn_search_object.png" /></div>
</div>
<div class="search-area">
	<div id="search-box">
		<input type="text" name="search_key" placeholder="검색어를 입력해 주세요"/>
	</div>
	<div id="search-box-btn"><img src="/shop/data/skin_mobileV2/default/common/img/gnb/search_btn_object.png" onclick="javascript:searchKw();" /></div>
</div>
<div class="new-menu-area">
	<div id="category-btn" onClick="javascript:showCateArea();">카테고리<div class="bar_area"><img src="/shop/data/skin_mobileV2/default/common/img/menu/new_menubar.png" /></div></div>
	<div id="cart-btn" onClick="javascript:goMenu('cart');">장바구니<span class="cart-item-quantity"></span><div class="bar_area"><img src="/shop/data/skin_mobileV2/default/common/img/menu/new_menubar.png" /></div></div>
	<div id="viewgoods-btn" onClick="javascript:goMenu('viewgoods');">최근본상품<span class="viewgoods-quantity"></span><div class="bar_area"><img src="/shop/data/skin_mobileV2/default/common/img/menu/new_menubar.png" /></div></div>
	<div id="wish-btn" onClick="javascript:goMenu('wish');">Wish List</div>
</div>

</header>
<section id="cate-area">
	<div class="top_title"><div class="title">카테고리</div></div>
	<div class="top_path"><div class='now_path'><div class='pathitem activeitem allpath' onClick='javascript:cateSelect("");'>전체카테고리</div></div></div>
	<div class="now_cate">

		<input type="hidden" name="now_cate" value="<?php echo $TPL_VAR["now_cate"]?>" />
	</div>
	<div class="cate_path">
	</div>
	<div class="cate_list">
	</div>

</section>
<div class="clearb"></div>

<section id="goodsres-hide" class="content_goods">
	<div class="pop_back">
		<div class="pop_effect">
			<div class="text_msg"></div>
		</div>
	</div>
</section>

<section id="goodsres-hide2" class="content_goods">
	<div class="goodsres_wrap">
		<div class="goodsres_title"><div class="title">알림</div></div>
		<div class="goodsres_msg"><div class="text_msg"></div></div>
	</div>
</section>