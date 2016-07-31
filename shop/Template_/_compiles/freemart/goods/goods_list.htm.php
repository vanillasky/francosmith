<?php /* Template_ 2.2.7 2016/07/26 09:04:31 /www/francotr3287_godo_co_kr/shop/data/skin/freemart/goods/goods_list.htm 000008092 */  $this->include_("dataDisplayGoods");?>
<?php $this->print_("header",$TPL_SCP,1);?>


<!-- 상단 HTML -->
<div style="padding:5px 0 10px 0"><?php echo stripslashes($GLOBALS["lstcfg"]["body"])?></div>

<div class="page-wrapper">
<div><!-- Start indiv -->

<form name=frmList>
<input type=hidden name=category value="<?php echo $GLOBALS["category"]?>">
<input type=hidden name=sort value="<?php echo $_GET['sort']?>">
<input type=hidden name=page_num value="<?php echo $_GET['page_num']?>">

	<!-- 상단 카테고리 메뉴 -->
	<table width=100% border=0 cellpadding=0 cellspacing=0>
	<tr>
	<td style="padding:5px 0 0 0; text-align:left;">
		<img src="/shop/data/images/web/icon/arrow-r.png" align="absmiddle">&nbsp;&nbsp;<b><?php echo currPosition($GLOBALS["category"])?></b><span id="prod_cnt">[Total:<?php echo $TPL_VAR["pg"]->recode['total']?>]</span></td>
	</tr>
	<tr>
	</table>
		
	<div style="padding-top:15px"></div>	
	

<!-- 추천상품 -->
<?php echo $this->assign('loop',dataDisplayGoods($GLOBALS["category"],$TPL_VAR["lstcfg"]["rimg"],$TPL_VAR["lstcfg"]["rpage_num"]))?>

<?php echo $this->assign('dpCfg',$GLOBALS["dpCfg"]["rtpl"])?>

<?php echo $this->assign('cols',$TPL_VAR["lstcfg"]["rcols"])?>

<?php echo $this->assign('size',$TPL_VAR["lstcfg"]["rsize"])?>

<?php if($TPL_VAR["loop"]){?>
<div style="font:0;padding-top:10"></div>
<table border=0 cellpadding=0 cellspacing=0>
	<tr>
		<TD style="background: URL(/shop/data/skin/freemart/img/common/title_recom.gif) no-repeat;" nowrap width="236" height="30"></TD>
		<TD style="background: URL(/shop/data/skin/freemart/img/common/title_recom_bg.gif) repeat-x;" width='90%' height="30"></TD>
	</tr>
</table>
<div style="font:0;padding-top:10"></div>
<?php }?>
<?php echo $this->define('tpl_include_file_1',"goods/list/".$TPL_VAR["lstcfg"]["rtpl"].".htm")?> <?php $this->print_("tpl_include_file_1",$TPL_SCP,1);?>



	
<!-- capture_start("list_top") -->
<div class="sort_area_top_line"></div>
<div class="sort_area">
	<ul id="sort_ul" class="sort_list">
		<li class="" id="sort_goods_price"><a href="javascript:sort('price')"><span></span>낮은 가격순</a></li>
		<li class="" id="sort_goods_price_desc"><a href="javascript:sort('price desc')"><span></span>높은 가격순</a></li>
		<li class="" id="sort_goodsnm_desc"><a href="javascript:sort('goodsnm desc')"><span></span>상품명↑</a></li>
		<li class="" id="sort_goodsnm"><a href="javascript:sort('goodsnm')"><span></span>상품명↓</a></li>
		<li class="" id="sort_goods_reserve_desc"><a href="javascript:sort('reserve desc')"><span></span>적립금↑</a></li>
		<li class="" id="sort_goods_reserve"><a href="javascript:sort('reserve')"><span></span>적립금↓</a></li>
		<li class="" id="sort_maker_desc"><a href="javascript:sort('maker desc')"><span></span>제조사↑</a></li>
		<li class="" id="sort_maker"><a href="javascript:sort('maker')"><span></span>제조사↓</a></li>
	</ul>
</div>
<div class="sort_area_items_per_page">
	<span>Items per page:</span>
	<select onchange="if(typeof(document.sSearch) != 'undefined') { _ID('page_num').value=this.value; document.sSearch.submit() } else { this.form.page_num.value=this.value;this.form.submit() }" style="font:8pt 돋움"><?php if((is_array($TPL_R1=$TPL_VAR["lstcfg"]["page_num"])&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?><option value="<?php echo $TPL_V1?>" <?php echo $GLOBALS["selected"]["page_num"][$TPL_V1]?>><?php echo $TPL_V1?><?php }}?></select>		
</div>
<!-- capture_end ("list_top") -->

<table width=100% border=0 cellpadding=0 cellspacing=0>
<tr><td height=1 bgcolor=#DDDDDD></td></tr>
<tr>
<td style="padding:15 0">
<?php echo $this->assign('loop',$TPL_VAR["loopM"])?>

<?php echo $this->assign('slevel',$TPL_VAR["slevel"])?>

<?php echo $this->assign('clevel',$TPL_VAR["clevel"])?>

<?php echo $this->assign('clevel_auth',$TPL_VAR["clevel_auth"])?>

<?php echo $this->assign('auth_step',$TPL_VAR["auth_step"])?>

<?php echo $this->assign('dpCfg',$GLOBALS["dpCfg"]["tpl"])?>

<?php echo $this->assign('cols',$TPL_VAR["lstcfg"]["cols"])?>

<?php echo $this->assign('size',$TPL_VAR["lstcfg"]["size"])?>

<?php echo $this->define('tpl_include_file_2',"goods/list/".$TPL_VAR["lstcfg"]["tpl"].".htm")?> <?php $this->print_("tpl_include_file_2",$TPL_SCP,1);?>

</td>
</tr>
<tr><td height=1 bgcolor=#DDDDDD></td></tr>
<tr>
<td>
<!-- capture_start("list_top") -->
<div class="sort_area_top_line"></div>
<div class="sort_area">
	<ul id="sort_ul" class="sort_list">
		<li class="" id="sort_goods_price"><a href="javascript:sort('price')"><span></span>낮은 가격순</a></li>
		<li class="" id="sort_goods_price_desc"><a href="javascript:sort('price desc')"><span></span>높은 가격순</a></li>
		<li class="" id="sort_goodsnm_desc"><a href="javascript:sort('goodsnm desc')"><span></span>상품명↑</a></li>
		<li class="" id="sort_goodsnm"><a href="javascript:sort('goodsnm')"><span></span>상품명↓</a></li>
		<li class="" id="sort_goods_reserve_desc"><a href="javascript:sort('reserve desc')"><span></span>적립금↑</a></li>
		<li class="" id="sort_goods_reserve"><a href="javascript:sort('reserve')"><span></span>적립금↓</a></li>
		<li class="" id="sort_maker_desc"><a href="javascript:sort('maker desc')"><span></span>제조사↑</a></li>
		<li class="" id="sort_maker"><a href="javascript:sort('maker')"><span></span>제조사↓</a></li>
	</ul>
</div>
<div class="sort_area_items_per_page">
	<span>Items per page:</span>
	<select onchange="if(typeof(document.sSearch) != 'undefined') { _ID('page_num').value=this.value; document.sSearch.submit() } else { this.form.page_num.value=this.value;this.form.submit() }" style="font:8pt 돋움"><?php if((is_array($TPL_R1=$TPL_VAR["lstcfg"]["page_num"])&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?><option value="<?php echo $TPL_V1?>" <?php echo $GLOBALS["selected"]["page_num"][$TPL_V1]?>><?php echo $TPL_V1?><?php }}?></select>		
</div>
<!-- capture_end ("list_top") -->
</td>
</tr>
<tr><td height=2 bgcolor=#DDDDDD></td></tr>
<tr><td align=center height=50><?php echo $TPL_VAR["pg"]->page['navi']?></td></tr>
</table>
	
</form>
<form name=frmCharge method=post>
<input type=hidden name=mode value="">
<input type=hidden name=goodsno value="">
<input type=hidden name=ea value="1">
<input type=hidden name=opt[] id=opt value="">
</form>
</div><!-- End indiv -->

</div>
<script>
function act(target,goodsno,opt1,opt2)
{
	var form = document.frmCharge;

	form.mode.value = "addItem";
	form.goodsno.value = goodsno;

	if(opt2) opt1 += opt2;
	document.getElementById("opt").value=opt1;

	form.action = target + ".php";
	form.submit();
}

function sort(sort)
{
	var fm = document.frmList;
	if(typeof(document.sSearch) != "undefined") fm = document.sSearch;
	fm.sort.value = sort;
	
	
	if (fm.rMinPrice) {
		fm.ssMinPrice.value = fm.rMinPrice.value.replace(",", "");
	}
	
	if (fm.ssMaxPrice) {
		fm.ssMaxPrice.value = fm.rMaxPrice.value.replace(",", "");;
	}
	
	fm.submit();
}
function sort_chk(sort)
{
	if (!sort) return;
	sort = sort.replace(" ","_");
	
	var obj = document.getElementsByName('sort_'+sort);
	
	if (obj.length && obj[0].src){
		div = obj[0].src.split('list_');
		for (i=0;i<obj.length;i++) {
			chg = (div[1]=="up_off.gif") ? "up_on.gif" : "down_on.gif";
			obj[i].src = div[0] + "list_" + chg;
		}
	}
	
	
	var jq = jQuery.noConflict();
	var sort_by = jq("#sort_" + sort);
	var sort_ul = jq("#sort_list");
	if (sort_by.length) {
		//sort_ul.children("li" a span").removeClass("on");
		sort_by.find("a span").addClass("on");
	} 

}
<?php if($_GET['sort']){?>
sort_chk('<?php echo $_GET['sort']?>');
<?php }?>
</script>

<?php $this->print_("footer",$TPL_SCP,1);?>