<?php /* Template_ 2.2.7 2016/05/07 11:51:16 /www/francotr3287_godo_co_kr/shop/data/skin/freemart/goods/goods_grp_01.htm 000006643 */ 
if (is_array($GLOBALS["r_page_num"])) $TPL__r_page_num_1=count($GLOBALS["r_page_num"]); else if (is_object($GLOBALS["r_page_num"]) && in_array("Countable", class_implements($GLOBALS["r_page_num"]))) $TPL__r_page_num_1=$GLOBALS["r_page_num"]->count();else $TPL__r_page_num_1=0;?>
<?php $this->print_("header",$TPL_SCP,1);?>

<!-- 상단이미지 || 현재위치 -->
<div style="padding:5px 0 10px 0"><?php echo stripslashes($GLOBALS["lstcfg"]["body"])?></div>

<div class="page-wrapper">

	<div class="indiv"><!-- Start indiv -->
		<table width=100% border=0 cellpadding=0 cellspacing=0>
		<tr>
			<td style="padding:5px 0 0 0; text-align:left;">
				<img src="/shop/data/images/web/icon/arrow-r.png" align="absmiddle">&nbsp;&nbsp;<b>BEST SELLERS</b><span id="prod_cnt">[Total:<?php echo $TPL_VAR["pg"]->recode['total']?>]</span>
			</td>
		</tr>
		<tr>
		</table>
	
		<div id="cate_prod" ></div>
	
		<!-- 타이틀이미지 네임 :::
			할인상품 <img src="/shop/data/skin/freemart/img/common/title_discount.gif" border=0>
			베스트상품 <img src="/shop/data/skin/freemart/img/common/title_best.gif" border=0>
			추천상품 <img src="/shop/data/skin/freemart/img/common/title_recomgoods.gif" border=0>
			-->

		<form name=frmList>
			<input type=hidden name=sort value="<?php echo $_GET['sort']?>">
			<input type=hidden name=page_num value="<?php echo $_GET['page_num']?>">
		
		
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
					
			<select onchange="this.form.page_num.value=this.value;this.form.submit()" style="font:8pt 돋움"><?php if($TPL__r_page_num_1){foreach($GLOBALS["r_page_num"] as $TPL_V1){?><option value="<?php echo $TPL_V1?>" <?php echo $GLOBALS["selected"]["page_num"][$TPL_V1]?>><?php echo $TPL_V1?><?php }}?></select>
		</div>
		<!-- capture_end ("list_top") -->
		
		
		<!-- 테이블 -->
		<table width=100% border=0 cellpadding=0 cellspacing=0>
		<tr><td height=1 bgcolor=#DDDDDD></td></tr>
		<tr>
			<td style="padding:15 0">
			<?php echo $this->assign('loop',$TPL_VAR["loop"])?>

			<?php echo $this->assign('cols', 4)?>

			<?php echo $this->assign('size', 130)?>

			<?php echo $this->define('tpl_include_file_1',"goods/list/tpl_01.htm")?> <?php $this->print_("tpl_include_file_1",$TPL_SCP,1);?>

		
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
					
			<select onchange="this.form.page_num.value=this.value;this.form.submit()" style="font:8pt 돋움"><?php if($TPL__r_page_num_1){foreach($GLOBALS["r_page_num"] as $TPL_V1){?><option value="<?php echo $TPL_V1?>" <?php echo $GLOBALS["selected"]["page_num"][$TPL_V1]?>><?php echo $TPL_V1?><?php }}?></select>
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

	</div><!-- End indiv -->
</div>

<?php $this->print_("footer",$TPL_SCP,1);?>