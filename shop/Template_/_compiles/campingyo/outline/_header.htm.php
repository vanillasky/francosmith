<?php /* Template_ 2.2.7 2016/04/11 21:49:03 /www/francotr3287_godo_co_kr/shop/data/skin/campingyo/outline/_header.htm 000005985 */ ?>
<html>
<head>
<?php echo $TPL_VAR["systemHeadTagStart"]?>

	<link rel="shortcut icon" href="/shop/data/images/francosmith.ico">
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<meta name="description" content="<?php echo $GLOBALS["meta_title"]?>">
<meta name="keywords" content="<?php echo $GLOBALS["meta_keywords"]?>">
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
<script src="/shop/data/skin/campingyo/common.js"></script>
<script src="/shop/data/skin/campingyo/cart_tab/godo.cart_tab.js"></script>
<link rel="styleSheet" href="/shop/data/skin/campingyo/cart_tab/style.css">
<link rel="styleSheet" href="/shop/data/skin/campingyo/style.css">

<style type="text/css">
.outline_both {
border-left-style:solid;
border-right-style:solid;
border-left-width:<?php echo $GLOBALS["cfg"]['shopLineWidthL']?>;
border-right-width:<?php echo $GLOBALS["cfg"]['shopLineWidthR']?>;
border-left-color:<?php echo $GLOBALS["cfg"]['shopLineColorL']?>;
border-right-color:<?php echo $GLOBALS["cfg"]['shopLineColorR']?>;
}

<?php if($this->tpl_['side_inc']&&$GLOBALS["cfg"]['outline_sidefloat']=='left'){?>
.outline_side {
border-left-style:solid;
border-left-width:<?php echo $GLOBALS["cfg"]['shopLineWidthC']?>;
border-left-color:<?php echo $GLOBALS["cfg"]['shopLineColorC']?>;
}
<?php }elseif($this->tpl_['side_inc']&&$GLOBALS["cfg"]['outline_sidefloat']=='right'){?>
.outline_side {
border-right-style:solid;
border-right-width:<?php echo $GLOBALS["cfg"]['shopLineWidthC']?>;
border-right-color:<?php echo $GLOBALS["cfg"]['shopLineColorC']?>;
<?php }?>
</style>
<?php if($GLOBALS["overture_cc"]){?><?php $this->print_("overture_cc",$TPL_SCP,1);?><?php }?>
<?php echo $TPL_VAR["customHeader"]?>

<?php echo $TPL_VAR["systemHeadTagEnd"]?>

</head>

<!-- BEGIN LivePerson Monitor. -->
<script type="text/javascript"> window.lpTag=window.lpTag||{};if(typeof window.lpTag._tagCount==='undefined'){window.lpTag={site:'50939346'||'',section:lpTag.section||'',autoStart:lpTag.autoStart===false?false:true,ovr:lpTag.ovr||{},_v:'1.5.1',_tagCount:1,protocol:location.protocol,events:{bind:function(app,ev,fn){lpTag.defer(function(){lpTag.events.bind(app,ev,fn);},0);},trigger:function(app,ev,json){lpTag.defer(function(){lpTag.events.trigger(app,ev,json);},1);}},defer:function(fn,fnType){if(fnType==0){this._defB=this._defB||[];this._defB.push(fn);}else if(fnType==1){this._defT=this._defT||[];this._defT.push(fn);}else{this._defL=this._defL||[];this._defL.push(fn);}},load:function(src,chr,id){var t=this;setTimeout(function(){t._load(src,chr,id);},0);},_load:function(src,chr,id){var url=src;if(!src){url=this.protocol+'//'+((this.ovr&&this.ovr.domain)?this.ovr.domain:'lptag.liveperson.net')+'/tag/tag.js?site='+this.site;}var s=document.createElement('script');s.setAttribute('charset',chr?chr:'UTF-8');if(id){s.setAttribute('id',id);}s.setAttribute('src',url);document.getElementsByTagName('head').item(0).appendChild(s);},init:function(){this._timing=this._timing||{};this._timing.start=(new Date()).getTime();var that=this;if(window.attachEvent){window.attachEvent('onload',function(){that._domReady('domReady');});}else{window.addEventListener('DOMContentLoaded',function(){that._domReady('contReady');},false);window.addEventListener('load',function(){that._domReady('domReady');},false);}if(typeof(window._lptStop)=='undefined'){this.load();}},start:function(){this.autoStart=true;},_domReady:function(n){if(!this.isDom){this.isDom=true;this.events.trigger('LPT','DOM_READY',{t:n});}this._timing[n]=(new Date()).getTime();},vars:lpTag.vars||[],dbs:lpTag.dbs||[],ctn:lpTag.ctn||[],sdes:lpTag.sdes||[],ev:lpTag.ev||[]};lpTag.init();}else{window.lpTag._tagCount+=1;} </script>
<!-- END LivePerson Monitor. -->
	
<?php echo copyProtect()?>

<body bgcolor="<?php echo $GLOBALS["cfg"]['outbg_color']?>" background="<?php echo $GLOBALS["cfg"]['outbg_img']?>" <?php echo copyProtect(true)?>>

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
<td valign=top width=<?php echo $GLOBALS["cfg"]['shopSideSize']?> nowrap style="border:solid 1px #ccc; border-width:0 1px;"><?php $this->print_("side_inc",$TPL_SCP,1);?></td>
<?php }?>
<td valign=top width=100% height=100% bgcolor="<?php echo $GLOBALS["cfg"]['inbg_color']?>" background="<?php echo $GLOBALS["cfg"]['inbg_img']?>" class=outline_side>