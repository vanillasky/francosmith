<?php /* Template_ 2.2.7 2015/08/04 10:18:13 /www/francotr3287_godo_co_kr/shop/data/skin/standard/outline/_header.htm 000004053 */ ?>
<html>
<head>
<?php echo $TPL_VAR["systemHeadTagStart"]?>

<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<meta name="description" content="<?php echo $GLOBALS["meta_title"]?>">
<meta name="keywords" content="<?php echo $GLOBALS["meta_keywords"]?>">
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
<script src="/shop/data/skin/standard/common.js"></script>
<script src="/shop/data/skin/standard/cart_tab/godo.cart_tab.js"></script>
<link rel="styleSheet" href="/shop/data/skin/standard/cart_tab/style.css">
<link rel="styleSheet" href="/shop/data/skin/standard/style.css">
<style type="text/css">
.outline_both {
border-left-style:solid;
border-right-style:solid;
border-left-width:<?php echo $GLOBALS["cfg"]['shopLineWidthL']?>px;
border-right-width:<?php echo $GLOBALS["cfg"]['shopLineWidthR']?>px;
border-left-color:#<?php echo $GLOBALS["cfg"]['shopLineColorL']?>;
border-right-color:#<?php echo $GLOBALS["cfg"]['shopLineColorR']?>;
}

<?php if($this->tpl_['side_inc']&&$GLOBALS["cfg"]['outline_sidefloat']=='left'){?>
.outline_side {
border-left-style:solid;
border-left-width:<?php echo $GLOBALS["cfg"]['shopLineWidthC']?>px;
border-left-color:#<?php echo $GLOBALS["cfg"]['shopLineColorC']?>;
}
<?php }elseif($this->tpl_['side_inc']&&$GLOBALS["cfg"]['outline_sidefloat']=='right'){?>
.outline_side {
border-right-style:solid;
border-right-width:<?php echo $GLOBALS["cfg"]['shopLineWidthC']?>px;
border-right-color:#<?php echo $GLOBALS["cfg"]['shopLineColorC']?>;
<?php }?>
</style>
<?php if($GLOBALS["overture_cc"]){?><?php $this->print_("overture_cc",$TPL_SCP,1);?><?php }?>
<?php if($GLOBALS["mainpage"]=='1'){?>
<script src="/shop/lib/js/jquery-1.10.2.min.js"></script>
<script src="/shop/lib/js/jquery.banner.js"></script>
<?php }?>
<?php echo $TPL_VAR["customHeader"]?>

<?php echo $TPL_VAR["systemHeadTagEnd"]?>

</head>

<?php echo copyProtect()?>

<body bgcolor="<?php echo $GLOBALS["cfg"]['outbg_color']?>" background="<?php echo $GLOBALS["cfg"]['outbg_img']?>" <?php echo copyProtect(true)?> style="background:url(/shop/data/skin/standard/img/main/bg.gif) repeat-y center;">

<?php $this->print_("myBoxLayer",$TPL_SCP,1);?>

<?php if($TPL_VAR["useMyLevelLayerBox"]=='y'){?>
<?php $this->print_("myLevelLayer",$TPL_SCP,1);?>

<?php }?>
<?php if($TPL_VAR["alertCoupon"]==true){?>
<?php $this->print_("myCouponLayer",$TPL_SCP,1);?>

<?php }?>
<table width=100% height=100% cellpadding=0 cellspacing=0 border=0>
<?php if($this->tpl_['header_inc']){?>
<tr>
<td><?php $this->print_("header_inc",$TPL_SCP,1);?></td>
</tr>
<?php }?>
<tr>
<td height=100% align=<?php echo $GLOBALS["cfg"]['shopAlign']?>>

<table width=<?php echo $GLOBALS["cfg"]['shopSize']?> height=100% cellpadding=0 cellspacing=0 border=0 class="outline_both">
<tr>
<?php if($this->tpl_['side_inc']&&$GLOBALS["cfg"]['outline_sidefloat']=='left'){?>
<td valign=top width=<?php echo $GLOBALS["cfg"]['shopSideSize']?> nowrap><?php $this->print_("side_inc",$TPL_SCP,1);?></td>
<?php }?>
<td valign=top width=100% height=100% bgcolor="<?php echo $GLOBALS["cfg"]['inbg_color']?>" background="<?php echo $GLOBALS["cfg"]['inbg_img']?>" class=outline_side>