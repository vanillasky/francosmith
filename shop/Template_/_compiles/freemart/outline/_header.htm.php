<?php /* Template_ 2.2.7 2016/07/25 15:58:26 /www/francotr3287_godo_co_kr/shop/data/skin/freemart/outline/_header.htm 000004806 */ ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">


<html>
<head>
<?php echo $TPL_VAR["systemHeadTagStart"]?>




<!--[if lt IE 9]>
	<script src="/shop/data/js/jquery-1.11.3.min.js"></script> 
<![endif]-->

<!--[if gte IE 9 ]><!-->
   <script src="/shop/data/js/jquery-2.2.3.min.js" ></script> 
<!-- [endif]-->
	
<!-- [if !IE]> <!-->
	<script src="/shop/data/js/jquery-1.11.3.min.js"></script> 
<!--  [endif]-->

<script src="/shop/data/js/jquery-ui.js"></script>   
	
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<meta name="description" content="<?php echo $GLOBALS["meta_title"]?>">
<meta name="keywords" content="<?php echo $GLOBALS["meta_keywords"]?>">
<meta property="og:type"          content="website" />
<meta property="og:title"         content="Francosmith Tools & Woodworking" />	
<meta name="google-site-verification" content="FZqtnU-EnzciANnzLJfBIztQ-yjjuntrMSXo_1ijE-E" />
<title><?php echo $GLOBALS["meta_title"]?></title>

<?php if($_POST){?>

<script>
	
history.back = function() {

    var step = (document.location.protocol == 'https:' ? 2 : 1) * -1;

    history.go( step );

}


</script>

<?php }?>
<?php if($TPL_VAR["connInterpark"]=='ok'){?>


<script type="text/javascript">var entr_nm = "<a href='/'><?php echo $GLOBALS["cfg"]["shopName"]?></a>"; // 상점명</script>
<script type="text/javascript" src="http://www.interpark.com/gate/minm/topnav_shopplus_soho.js"></script>

<?php }?>
<?php if($_COOKIE['cc_inflow']=='yahoo_fss'||$_GET['ref']=='yahoo_fss'){?>
<script language="javascript" src="http://kr.ysp.shopping.yahoo.com/ysp/ysp_fss.js"></script>
<script> ykfss_bar();</script>
<?php }?>
<script src="/shop/data/skin/freemart/common.js"></script>
<script src="/shop/data/skin/freemart/cart_tab/godo.cart_tab.js"></script>
<script src="/js/mall.js"></script>	


<link rel="styleSheet" href="/shop/data/skin/freemart/cart_tab/style.css">
<link rel="styleSheet" href="/shop/data/skin/freemart/style.css">

<link rel="styleSheet" href="/shop/data/skin/freemart/shop_layout.css">
<link rel="styleSheet" href="/shop/data/skin/freemart/prod.css">
<link rel="styleSheet" href="/shop/data/skin/freemart/table.css">	
<link rel="styleSheet" href="/shop/data/skin/freemart/main-navi.css">
<link rel="styleSheet" href="/shop/data/skin/freemart/mypage.css" />	
<link rel="styleSheet" href="/shop/data/skin/freemart/font.css" />	
<link rel="styleSheet" href="/shop/data/skin/freemart/button.css" />	
<link rel="styleSheet" href="/shop/data/skin/freemart/tab.css" />	
<link rel="styleSheet" href="/shop/data/skin/freemart/smart_search.css" />
	
<style type="text/css">
.outline_both {
border-left-style:solid;
border-right-style:solid;
border-left-width:<?php echo $GLOBALS["cfg"]['shopLineWidthL']?>px;
border-right-width:<?php echo $GLOBALS["cfg"]['shopLineWidthR']?>px;
border-left-color:#<?php echo $GLOBALS["cfg"]['shopLineColorL']?>;
border-right-color:#<?php echo $GLOBALS["cfg"]['shopLineColorR']?>;
}

</style>
<?php if($GLOBALS["overture_cc"]){?><?php $this->print_("overture_cc",$TPL_SCP,1);?><?php }?>
<?php echo $TPL_VAR["customHeader"]?>

<?php echo $TPL_VAR["systemHeadTagEnd"]?>

</head>

<?php echo copyProtect()?>

<body bgcolor="<?php echo $GLOBALS["cfg"]['outbg_color']?>" background="<?php echo $GLOBALS["cfg"]['outbg_img']?>" <?php echo copyProtect(true)?>>
	
<?php $this->print_("myBoxLayer",$TPL_SCP,1);?>

<?php if($TPL_VAR["useMyLevelLayerBox"]=='y'){?>
<?php $this->print_("myLevelLayer",$TPL_SCP,1);?>

<?php }?>
<?php if($TPL_VAR["alertCoupon"]==true){?>
<?php $this->print_("myCouponLayer",$TPL_SCP,1);?>

<?php }?>


	
<table width=100% height=100% cellpadding=0 cellspacing=0 border="0">
<?php if($this->tpl_['header_inc']){?>
<tr>
	<td><?php $this->print_("header_inc",$TPL_SCP,1);?></td>
</tr>
<?php }?>
<tr>
	<td height=100% align=<?php echo $GLOBALS["cfg"]['shopAlign']?>>
<?php if($this->tpl_['side_inc']&&$GLOBALS["cfg"]['outline_sidefloat']=='left'){?>
		<table width="<?php echo $GLOBALS["cfg"]['shopSize']?>" height="100%" class="outline_both">
<?php }else{?>
		<table width="99%" height=100% cellpadding=0 cellspacing=0 border=0>
<?php }?>
		<tr>
<?php if($this->tpl_['side_inc']&&$GLOBALS["cfg"]['outline_sidefloat']=='left'){?>
			<td valign=top width="<?php echo $GLOBALS["cfg"]['shopSideSize']?>" nowrap><?php $this->print_("side_inc",$TPL_SCP,1);?></td>
<?php }?>
			<td valign=top width="100%" height="100%" bgcolor="<?php echo $GLOBALS["cfg"]['inbg_color']?>" background="<?php echo $GLOBALS["cfg"]['inbg_img']?>" >