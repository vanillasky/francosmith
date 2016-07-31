<?php /* Template_ 2.2.7 2014/08/01 15:04:33 /www/francotr3287_godo_co_kr/shop/data/skin/campingyo/main/index.htm 000010905 */  $this->include_("dataPopup","dataBoardArticle","dataDisplayGoods");?>
<?php $this->print_("header",$TPL_SCP,1);?>


<!-- 메인팝업창 -->
<?php if((is_array($TPL_R1=dataPopup())&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?>

<?php if($TPL_V1["type"]=='layer'&&isset($_COOKIE['blnCookie_'.$TPL_V1["code"]])===false){?>
<div id="<?php echo 'blnCookie_'.$TPL_V1["code"]?>" STYLE="position:absolute; width:<?php echo $TPL_V1["width"]?>px; height:<?php echo $TPL_V1["height"]?>px; left:<?php echo $TPL_V1["left"]?>px; top:<?php echo $TPL_V1["top"]?>px; z-index:200;">
<?php echo eval("\$_GET[code]='blnCookie_".$TPL_V1["code"]."';")?>

<?php echo $this->define('tpl_include_file_1',$TPL_V1["file"])?> <?php $this->print_("tpl_include_file_1",$TPL_SCP,1);?>

</div>
<?php }?>

<?php if($TPL_V1["type"]=='layerMove'&&isset($_COOKIE['blnCookie_'.$TPL_V1["code"]])===false){?>
<!-- 이동레이어 팝업창 시작 -->
<div id="<?php echo 'blnCookie_'.$TPL_V1["code"]?>" STYLE="position:absolute; width:<?php echo $TPL_V1["width"]?>px; height:<?php echo $TPL_V1["height"]?>px; left:<?php echo $TPL_V1["left"]?>px; top:<?php echo $TPL_V1["top"]?>px; z-index:200;">
<div onmousedown="Start_move(event,'<?php echo 'blnCookie_'.$TPL_V1["code"]?>');" onmouseup="Moveing_stop();" style='cursor:move;'>
<table border="0" cellspacing="0" cellpadding="0">
<tr>
<td>
<?php echo eval("\$_GET[code]='blnCookie_".$TPL_V1["code"]."';")?>

<?php echo $this->define('tpl_include_file_1',$TPL_V1["file"])?> <?php $this->print_("tpl_include_file_1",$TPL_SCP,1);?>

</td>
</tr>
</table>
</div>
</div>
<!-- 이동레이어 팝업창 끝 -->
<?php }?>

<?php }}?>

<script language="JavaScript"><!--
<?php if((is_array($TPL_R1=dataPopup())&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?>
<?php if($TPL_V1["type"]==''){?>
if ( !getCookie( "blnCookie_<?php echo $TPL_V1["code"]?>" ) ) { // <?php echo $TPL_V1["name"]?> 팝업호출
var property = 'width=<?php echo $TPL_V1["width"]?>, height=<?php echo $TPL_V1["height"]?>, top=<?php echo $TPL_V1["top"]?>, left=<?php echo $TPL_V1["left"]?>, scrollbars=no, toolbar=no';
var win=window.open( './html.php?htmid=<?php echo $TPL_V1["file"]?>&code=blnCookie_<?php echo $TPL_V1["code"]?>', '<?php echo $TPL_V1["code"]?>', property );
if(win) win.focus();
}
<?php }?>
<?php }}?>
//--></script>

<table border="0" cellpadding="0" cellspacing="0">
<tr>
	<td valign="top">
		<table width="340" border="0" cellpadding="0" cellspacing="0" style="font-size:11px; color:#666;">
			<tr>
				<td style="padding-left:20px;" height="43"><img src="/shop/data/skin/campingyo/img/main/txt_notice.gif"></td>
<?php if((is_array($TPL_R1=dataBoardArticle('notice', 1))&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?>
				<td style="padding-top:0px;"><a href="<?php echo url("board/view.php?")?>&id=<?php echo $TPL_V1["id"]?>&no=<?php echo $TPL_V1["no"]?>" style="color:#666"><b><?php echo strcut($TPL_V1["subject"], 40)?></b></a></td>
				<td width="39" align="right" style="padding-top:2px;"><a href="<?php echo url("board/list.php?")?>&id=<?php echo $TPL_V1["id"]?>"><img src="/shop/data/skin/campingyo/img/main/btn_more.gif"></a></td>
<?php }}?>
			</tr>
		</table>
	</td>
	<td width="20" align="center"><img src="/shop/data/skin/campingyo/img/main/line_dot.gif"></td>
	<td valign="top" style="border-right:solid 1px #ccc;">
		<table width="340" border="0" cellpadding="0" cellspacing="0" style="font-size:11px; color:#666;">
			<tr>
				<td align="left" height="43"><img src="/shop/data/skin/campingyo/img/main/txt_event.gif"></td>
				<!-- 이벤트 게시판 생성 후 notice 를 생성 게시판 아이디를 입력하세요 -->
<?php if((is_array($TPL_R1=dataBoardArticle('event', 1))&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?>
				<td style="padding-top:0px;"><a href="<?php echo url("board/view.php?")?>&id=<?php echo $TPL_V1["id"]?>&no=<?php echo $TPL_V1["no"]?>" style="color:#666"><b><?php echo strcut($TPL_V1["subject"], 40)?></b></a></td>
				<td width="39" align="right" style="padding-top:2px;padding-right:10px;"><a href="<?php echo url("board/list.php?")?>&id=<?php echo $TPL_V1["id"]?>"><img src="/shop/data/skin/campingyo/img/main/btn_more.gif"></a></td>
<?php }}?>
			</tr>
		</table>
	</td>
</tr>
<tr>
	<td colspan="3">
	<!-- 메인 비쥬얼 썸네일 시작 -->
	<div id="main_banner" style="position:relative;display:none;background:url(/shop/data/skin/campingyo/img/main/main_green_space.png)">
		<!-- <div><a href="/shop/member/join.php?&"><img src="/shop/data/images/rolling_baner_cupon.jpg"></a></div> -->
		<div><a href="/shop/goods/goods_brand.php?brand=18"><img src="/shop/data/images/lie-nielsen/rolling_baner_lienielsen.jpg"></a></div>
		<div><a href="/shop/goods/goods_brand.php?brand=17"><img src="/shop/data/images/rolling_baner_chattapencil.jpg"></a></div>
		<div><a href="/shop/goods/goods_list.php?&category=001"><img src="/shop/data/images/rolling_baner_gorilla.jpg"></a></div>
		<div><a href="/shop/goods/goods_brand.php?brand=2"><img src="/shop/data/images/rolling_baner_microjig.jpg"></a></div>
		<div><a href="/shop/goods/goods_brand.php?brand=3"><img src="/shop/data/images/rolling_baner_dmt.jpg"></a></div>
	</div>
	
	<script src="/shop/lib/js/ierotator.js" type="text/javascript"></script>
	<style>
		#main_banner ul {top:200px !important; left:-10px !important;}
		#main_banner ul li {padding-left:6px;}
	</style>
	<script>
	var config = {
		'id':'main_banner',
		'effect':'FILTER: progid:DXImageTransform.Microsoft.Fade(Overlap=0.25,Duration=0.7)',
		'width':700,
		'height':200,
		'wait':3000,
		'numDisplay':'block',
		'numimg':[
			
			['/shop/data/skin/campingyo/img/main/main_rolling_on.png','/shop/data/skin/campingyo/img/main/main_rolling_off.png'],
			['/shop/data/skin/campingyo/img/main/main_rolling_on.png','/shop/data/skin/campingyo/img/main/main_rolling_off.png'],
			['/shop/data/skin/campingyo/img/main/main_rolling_on.png','/shop/data/skin/campingyo/img/main/main_rolling_off.png'],
			['/shop/data/skin/campingyo/img/main/main_rolling_on.png','/shop/data/skin/campingyo/img/main/main_rolling_off.png'],
			['/shop/data/skin/campingyo/img/main/main_rolling_on.png','/shop/data/skin/campingyo/img/main/main_rolling_off.png'],
			['/shop/data/skin/campingyo/img/main/main_rolling_on.png','/shop/data/skin/campingyo/img/main/main_rolling_off.png'],
			['/shop/data/skin/campingyo/img/main/main_rolling_on.png','/shop/data/skin/campingyo/img/main/main_rolling_off.png'],
			['/shop/data/skin/campingyo/img/main/main_rolling_on.png','/shop/data/skin/campingyo/img/main/main_rolling_off.png'],
			['/shop/data/skin/campingyo/img/main/main_rolling_on.png','/shop/data/skin/campingyo/img/main/main_rolling_off.png'] 
		]
	}
	ier = new ierotator(config);
	</script>
	<!-- 메인 비쥬얼 썸네일 끝 -->
	</td>
</tr>

</table>
<!-- 아래 상품리스트에 쓰이는 세부소스는 '디자인관리 > 상품(goods) > 상품목록 > 갤러리형,리스트형,리스트그룹형,상품이동형' 에 있습니다  -->

<div style="width:678px;padding-left:20px;">
<div style="width:678px;">
<!-- 상품 리스트 #1 -->
<?php if($GLOBALS["cfg_step"][ 0]["chk"]){?>
<div style="float:left;"><a href="<?php echo url("goods/goods_grp_01.php")?>&"><img src="/shop/data/images/new_arrivals.jpg"></a></div>
<div style="float:left;padding-top:31px;"><hr style="width:400px"></div>
<div align="right" style="padding-top:30px; padding-right:20px;"><a href="<?php echo url("goods/goods_grp_01.php")?>&"><img src="/shop/data/skin/campingyo/img/main/btn_more.gif"></a></div>
<div style="clear: both;"></div>
<?php echo $this->assign('loop',dataDisplayGoods( 0,$GLOBALS["cfg_step"][ 0]["img"],$GLOBALS["cfg_step"][ 0]["page_num"]))?>

<?php echo $this->assign('cols',$GLOBALS["cfg_step"][ 0]["cols"])?>

<?php echo $this->assign('size',$GLOBALS["cfg"][$GLOBALS["cfg_step"][ 0]["img"]])?>

<?php echo $this->assign('id',"main_list_01")?>

<?php echo $this->assign('dpCfg',$GLOBALS["cfg_step"][ 0])?>

<?php echo $this->define('tpl_include_file_3',"goods/list/".$GLOBALS["cfg_step"][ 0]["tpl"].".htm")?> <?php $this->print_("tpl_include_file_3",$TPL_SCP,1);?>

<?php }?>

<!-- 상품 리스트 #2 -->
<?php if($GLOBALS["cfg_step"][ 1]["chk"]){?>
<div style="float:left;"><a href="<?php echo url("goods/goods_grp_02.php")?>&"><img src="/shop/data/images/best_products.jpg"></a></div>
<div style="float:left;padding-top:31px;"><hr style="width:400px"></div>
<div align="right" style="padding-top:30px; padding-right:20px;"><a href="<?php echo url("goods/goods_grp_02.php")?>&"><img src="/shop/data/skin/campingyo/img/main/btn_more.gif"></a></div>
<div style="clear: both;"></div>
<?php echo $this->assign('loop',dataDisplayGoods( 1,$GLOBALS["cfg_step"][ 1]["img"],$GLOBALS["cfg_step"][ 1]["page_num"]))?>

<?php echo $this->assign('cols',$GLOBALS["cfg_step"][ 1]["cols"])?>

<?php echo $this->assign('size',$GLOBALS["cfg"][$GLOBALS["cfg_step"][ 1]["img"]])?>

<?php echo $this->assign('id',"main_list_02")?>

<?php echo $this->assign('dpCfg',$GLOBALS["cfg_step"][ 1])?>

<?php echo $this->define('tpl_include_file_4',"goods/list/".$GLOBALS["cfg_step"][ 1]["tpl"].".htm")?> <?php $this->print_("tpl_include_file_4",$TPL_SCP,1);?>

<?php }?>

<!-- 상품 리스트 #3 -->
<?php if($GLOBALS["cfg_step"][ 2]["chk"]){?>
<div style="float:left;"><a href="<?php echo url("goods/goods_grp_03.php")?>&"><img src="/shop/data/images/special_prices.jpg"></a></div>
<div style="float:left;padding-top:31px;"><hr style="width:400px"></div>
<div align="right" style="padding-top:30px; padding-right:20px;"><a href="<?php echo url("goods/goods_grp_03.php")?>&"><img src="/shop/data/skin/campingyo/img/main/btn_more.gif"></a></div>
<div style="clear: both;"></div>
<?php echo $this->assign('loop',dataDisplayGoods( 2,$GLOBALS["cfg_step"][ 2]["img"],$GLOBALS["cfg_step"][ 2]["page_num"]))?>

<?php echo $this->assign('cols',$GLOBALS["cfg_step"][ 2]["cols"])?>

<?php echo $this->assign('size',$GLOBALS["cfg"][$GLOBALS["cfg_step"][ 2]["img"]])?>

<?php echo $this->assign('id',"main_list_03")?>

<?php echo $this->assign('dpCfg',$GLOBALS["cfg_step"][ 2])?>

<?php echo $this->define('tpl_include_file_5',"goods/list/".$GLOBALS["cfg_step"][ 2]["tpl"].".htm")?> <?php $this->print_("tpl_include_file_5",$TPL_SCP,1);?>

<?php }?>	

</div>
</div>
<?php $this->print_("footer",$TPL_SCP,1);?>