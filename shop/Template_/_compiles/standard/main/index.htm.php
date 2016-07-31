<?php /* Template_ 2.2.7 2015/08/04 10:18:13 /www/francotr3287_godo_co_kr/shop/data/skin/standard/main/index.htm 000007830 */  $this->include_("dataPopup","dataBanner","dataDisplayGoods");?>
<?php $this->print_("header",$TPL_SCP,1);?>


<!-- 메인팝업창 -->
<?php if($TPL_VAR["page_cache_enabled"]){?>
<div id="template-popup-layer" style="display: none; position: absolute; z-index: 200;"></div>
<div id="template-popup-move-layer" style="display: none; position: absolute; z-index: 200; cursor: move;"></div>
<?php }else{?>
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
<?php }?>
<div><!-- 메인 이미지 배너 (배너관리에서 수정가능) --><?php if((is_array($TPL_R1=dataBanner( 1))&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?><?php echo $TPL_V1["tag"]?><?php }}?></div>
<div class="div_line" style="margin-bottom:30px;"><!-- --></div>


<!-- 아래 상품리스트에 쓰이는 세부소스는 '디자인관리 > 상품(goods) > 상품목록 > 갤러리형,리스트형,리스트그룹형,상품이동형' 에 있습니다  -->

<!-- 상품 리스트 #1 -->
<?php if($GLOBALS["cfg_step"][ 0]["chk"]){?>
<div class="yuls_main_tit"><a href="<?php echo url("goods/goods_grp_01.php")?>&"><?php echo $GLOBALS["cfg_step"][ 0]["title"]?></a></div>
<?php echo $this->assign('loop',dataDisplayGoods( 0,$GLOBALS["cfg_step"][ 0]["img"],$GLOBALS["cfg_step"][ 0]["page_num"]))?>

<?php echo $this->assign('cols',$GLOBALS["cfg_step"][ 0]["cols"])?>

<?php echo $this->assign('size',$GLOBALS["cfg"][$GLOBALS["cfg_step"][ 0]["img"]])?>

<?php echo $this->assign('id',"main_list_01")?>

<?php echo $this->assign('dpCfg',$GLOBALS["cfg_step"][ 0])?>

<?php echo $this->define('tpl_include_file_3',"goods/list/".$GLOBALS["cfg_step"][ 0]["tpl"].".htm")?> <?php $this->print_("tpl_include_file_3",$TPL_SCP,1);?>

<?php }?>

<div class="div_main_line"><!-- --></div>

<!-- 상품 리스트 #2 -->
<?php if($GLOBALS["cfg_step"][ 1]["chk"]){?>
<div class="yuls_main_tit"><a href="<?php echo url("goods/goods_grp_02.php")?>&"><?php echo $GLOBALS["cfg_step"][ 1]["title"]?></a></div>
<?php echo $this->assign('loop',dataDisplayGoods( 1,$GLOBALS["cfg_step"][ 1]["img"],$GLOBALS["cfg_step"][ 1]["page_num"]))?>

<?php echo $this->assign('cols',$GLOBALS["cfg_step"][ 1]["cols"])?>

<?php echo $this->assign('size',$GLOBALS["cfg"][$GLOBALS["cfg_step"][ 1]["img"]])?>

<?php echo $this->assign('id',"main_list_02")?>

<?php echo $this->assign('dpCfg',$GLOBALS["cfg_step"][ 1])?>

<?php echo $this->define('tpl_include_file_4',"goods/list/".$GLOBALS["cfg_step"][ 1]["tpl"].".htm")?> <?php $this->print_("tpl_include_file_4",$TPL_SCP,1);?>

<?php }?>

<div class="div_main_line"><!-- --></div>

<!-- 상품 리스트 #3 -->
<?php if($GLOBALS["cfg_step"][ 2]["chk"]){?>
<div class="yuls_main_tit"><a href="<?php echo url("goods/goods_grp_03.php")?>&"><?php echo $GLOBALS["cfg_step"][ 2]["title"]?></a></div>
<?php echo $this->assign('loop',dataDisplayGoods( 2,$GLOBALS["cfg_step"][ 2]["img"],$GLOBALS["cfg_step"][ 2]["page_num"]))?>

<?php echo $this->assign('cols',$GLOBALS["cfg_step"][ 2]["cols"])?>

<?php echo $this->assign('size',$GLOBALS["cfg"][$GLOBALS["cfg_step"][ 2]["img"]])?>

<?php echo $this->assign('id',"main_list_03")?>

<?php echo $this->assign('dpCfg',$GLOBALS["cfg_step"][ 2])?>

<?php echo $this->define('tpl_include_file_5',"goods/list/".$GLOBALS["cfg_step"][ 2]["tpl"].".htm")?> <?php $this->print_("tpl_include_file_5",$TPL_SCP,1);?>

<?php }?>

<div class="div_main_line"><!-- --></div>

<!-- 상품 리스트 #4 -->
<?php if($GLOBALS["cfg_step"][ 3]["chk"]){?>
<div class="yuls_main_tit"><a href="<?php echo url("goods/goods_grp_04.php")?>&"><?php echo $GLOBALS["cfg_step"][ 3]["title"]?></a></div>
<?php echo $this->assign('loop',dataDisplayGoods( 3,$GLOBALS["cfg_step"][ 3]["img"],$GLOBALS["cfg_step"][ 3]["page_num"]))?>

<?php echo $this->assign('cols',$GLOBALS["cfg_step"][ 3]["cols"])?>

<?php echo $this->assign('size',$GLOBALS["cfg"][$GLOBALS["cfg_step"][ 3]["img"]])?>

<?php echo $this->assign('id',"main_list_04")?>

<?php echo $this->assign('dpCfg',$GLOBALS["cfg_step"][ 3])?>

<?php echo $this->define('tpl_include_file_6',"goods/list/".$GLOBALS["cfg_step"][ 3]["tpl"].".htm")?> <?php $this->print_("tpl_include_file_6",$TPL_SCP,1);?>

<?php }?>

<div class="div_main_line"><!-- --></div>

<!-- 상품 리스트 #5 -->
<?php if($GLOBALS["cfg_step"][ 4]["chk"]){?>
<div class="yuls_main_tit"><a href="<?php echo url("goods/goods_grp_05.php")?>&"><?php echo $GLOBALS["cfg_step"][ 4]["title"]?></a></div>
<?php echo $this->assign('loop',dataDisplayGoods( 4,$GLOBALS["cfg_step"][ 4]["img"],$GLOBALS["cfg_step"][ 4]["page_num"]))?>

<?php echo $this->assign('cols',$GLOBALS["cfg_step"][ 4]["cols"])?>

<?php echo $this->assign('size',$GLOBALS["cfg"][$GLOBALS["cfg_step"][ 4]["img"]])?>

<?php echo $this->assign('id',"main_list_05")?>

<?php echo $this->assign('dpCfg',$GLOBALS["cfg_step"][ 4])?>

<?php echo $this->define('tpl_include_file_7',"goods/list/".$GLOBALS["cfg_step"][ 4]["tpl"].".htm")?> <?php $this->print_("tpl_include_file_7",$TPL_SCP,1);?>

<?php }?>

<div style="padding-top:15px"></div>

<!-- 멀티 팝업 -->
<script src="/shop/data/skin/standard/proc/multi_popup.js"></script>
<script>multiPopup('<?php echo $GLOBALS["cfg"]["tplSkin"]?>');</script>

<?php $this->print_("footer",$TPL_SCP,1);?>