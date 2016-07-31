<?php /* Template_ 2.2.7 2014/03/05 23:19:27 /www/francotr3287_godo_co_kr/shop/data/skin/campingyo/goods/goods_search.htm 000013996 */ ?>
<?php $this->print_("header",$TPL_SCP,1);?>

<!-- 상단이미지 || 현재위치 -->

<?php if($_GET['disp_type']=='gallery'){?>
<?  $TPL_VAR["lstcfg"]["tpl"] = "tpl_01"; ?>
<?php }elseif($_GET['disp_type']=='list'){?>
<?  $TPL_VAR["lstcfg"]["tpl"] = "tpl_02"; ?>
<?php }?>

<style>
.paletteColor { width:16px; height:16px; cursor:pointer; float:left; margin:2px; border:1px solid #dfdfdf;}
.paletteColor_selected { width:16px; height:16px; cursor:pointer; float:left; margin:2px; border:1px solid #dfdfdf; }
</style>
<TABLE width=100% cellpadding=0 cellspacing=0 border=0>
<tr>
<td><img src="/shop/data/skin/campingyo/img/common/title_search.gif" border=0></td>
</tr>
<TR>
<td class="path">HOME > <B>상품검색</B></td>
</TR>
</TABLE>


<div class="indiv"><!-- Start indiv -->

<form name=frmList id=form>
<input type="hidden" name="disp_type" value="<?php echo $_GET['disp_type']?>" />
<input type="hidden" name="hid_sword" value="<?php echo $_GET['hid_sword']?>" />
<input type=hidden name=searched value="Y">
<input type=hidden name=log value="1">
<input type=hidden name=sort value="<?php echo $_GET['sort']?>">
<input type=hidden name=page_num value="<?php echo $_GET['page_num']?>">

<div style="border:1px solid #DEDEDE; background-color:#F5F5F5;">
<table width=100% cellspacing=0 border=0>
<tr>
<td style="text-align:center;padding-top:20px;">
	<select name="skey">
	<option value="all" <?php echo $GLOBALS["selected"]["skey"]['all']?>> 통합검색 </option>
	<option value="goodsnm" <?php echo $GLOBALS["selected"]["skey"]['goodsnm']?>> 상품명 </option>
	<option value="goodscd" <?php echo $GLOBALS["selected"]["skey"]['goodscd']?>> 상품코드 </option>
	<option value="maker" <?php echo $GLOBALS["selected"]["skey"]['maker']?>> 제조사 </option>
	<option value="brand" <?php echo $GLOBALS["selected"]["skey"]['brand']?>> 브랜드 </option>
	</select>
	<input type="text" NAME="sword" value="<?php echo $_GET['sword']?>" size="32" style="background-color:#FFFFFF; font-color:#555555;font-face:dotum">
	<span class=noline><input type="image" src="/shop/data/skin/campingyo/img/common/btn_search.gif" align=absmiddle></span>
	<label><input type="checkbox" name="refind" style="border:0" onclick="search_refind(this)" <?php echo $GLOBALS["hid_checked"]?>/>결과 내 재검색</label>
</td>
</tr>
<tr>

	<td style="text-align:left;padding:5px, 200px, 20x, 0;"><?php if($GLOBALS["s_type"]['keyword']){?>인기검색어 : <?php echo $GLOBALS["s_type"]['keyword']?><?php }?></td>
</tr>
</table>
</div>
<div style="padding:2px"></div>
<?php if($GLOBALS["s_type"]['detail_type']){?>
<div style="border:1px solid #DEDEDE;">
<table width=100% cellpadding=10 cellspacing=0 border=0>
<tr>
<td style="text-align:center;">
	<table cellpadding="0" cellspacing="0" border="0" >
	<col /><col width="100px">
	<tr>
	<td style="text-align:center;">
	<!-- 검색 : Start -->
		<table cellpadding="2" cellspacing="0" border="0" style="text-align:center">
<?php if((is_array($TPL_R1=$GLOBALS["s_type"]['detail_type'])&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?>
		<tr>
<?php switch($TPL_V1){case 'category':?>
				<td style="text-align:right;">상품분류 : </td>
				<td style="text-align:left;">
				<div id=dynamic></div>
				<script src="/shop/lib/js/categoryBox.js"></script>
				<script>new categoryBox('cate[]',4,'<?php echo $GLOBALS["category"]?>','','frmList');</script>
				</td>
<?php break;case 'price':?>
				<td style="text-align:right;">가격 : </td>
				<td style="text-align:left;">
				<input type=text name=price[] style="width:100px;background-color:#FFFFFF;" value="<?php echo $_GET['price'][ 0]?>">원 ~
				<input type=text name=price[] style="width:100px;background-color:#FFFFFF;" value="<?php echo $_GET['price'][ 1]?>">원
				</td>
<?php break;case 'add':?>
				<td style="text-align:right;">조건선택 : </td>
				<td style="text-align:left;">
<?php if((is_array($TPL_R2=$GLOBALS["s_type"]['detail_add_type'])&&!empty($TPL_R2)) || (is_object($TPL_R2) && in_array("Countable", class_implements($TPL_R2)) && $TPL_R2->count() > 0)) {foreach($TPL_R2 as $TPL_V2){?>
				<label><input type="checkbox" name="detail_add_type[]" value="<?php echo $TPL_V2?>" style="border:0" <?php echo $GLOBALS["add_checked"][$TPL_V2]?>/>
<?php switch($TPL_V2){case 'free_deliveryfee':?>무료배송<?php break;case 'dc':?>할인쿠폰<?php break;case 'save':?>적립쿠폰<?php break;case 'new':?>신상품<?php break;case 'event':?>이벤트상품<?php }?>
				</label>
<?php }}?>
				</td>
<?php break;case 'color':?>
				<td style="text-align:right;">색상 : </td>
				<td style="text-align:left;">
				<input type="hidden" name="ssColor" id="ssColor" value="<?php echo $GLOBALS["_GET"]["ssColor"]?>" />
				<table border="0" cellpadding="0" cellspacing="2">
				<tr>
					<td>
<?php if((is_array($TPL_R2=($GLOBALS["colorList"]))&&!empty($TPL_R2)) || (is_object($TPL_R2) && in_array("Countable", class_implements($TPL_R2)) && $TPL_R2->count() > 0)) {foreach($TPL_R2 as $TPL_V2){?>
					<div class="paletteColor" style="background-color:#<?php echo $TPL_V2?>;" onclick="ssSelectColor(this.style.backgroundColor)"></div>
<?php }}?>
					</td>
				</tr>
				<tr>
					<td id="selectedColor" title="선택된 색은 더블클릭으로 삭제하실 수 있습니다."></td>
				</tr>
				</table>
				</td>
<?php break;default:?>
<?php }?>
		</tr>
<?php }}?>
		</table>
	<!-- 검색 : End -->
	</td>
	<td style="text-align:right">
		<span class=noline><input type="image" src="/shop/data/skin/campingyo/img/common/btn_search_b.gif" align=absmiddle></span>
	</td>
	</tr>
	</table>
</td>
</tr>
</table>
</div>
<?php }?>

<table width=100% border=0 cellpadding=0 cellspacing=0>
<tr><td height=18></td></tr>
<tr>
<td class=small height=27 style="padding:0 0 0 5">
<table width=100% border=0 cellpadding=0 cellspacing=0>
<tr>
<td style="text-align:left;">
<FONT COLOR="#555555">
<?php if($_GET['hid_sword']&&$_GET['sword']){?>
<font style="font-weight:bold; font-size:11px;"><?php echo $_GET['hid_sword']?></font> 검색 결과 내,<font style="font-weight:bold; font-size:11px"><?php echo $_GET['sword']?></font>를 포함한 검색결과
<?php }elseif(!$_GET['hid_sword']&&$_GET['sword']){?>
<font style="font-weight:bold; font-size:11px;"><?php echo $_GET['sword']?></font> 로 검색한 결과
<?php }?>
총 <font style="font-weight:bold; font-size:11px"><?php echo $TPL_VAR["pg"]->recode['total']?></font>개의 상품이 있습니다.</FONT>
</td>
<td style="text-align:right;">
<?php if($GLOBALS["s_type"]['disp_type']=='Y'){?>
<a href="javascript: add_param_submit('disp_type','list')">
<?php if($_GET['disp_type']=='list'){?>
	<img name="disp_list" src="/shop/data/skin/campingyo/img/common/btn_list_on.gif">
<?php }else{?>
	<img name="disp_list" src="/shop/data/skin/campingyo/img/common/btn_list_off.gif">
<?php }?>
</a>
<a href="javascript: add_param_submit('disp_type', 'gallery')">
<?php if($_GET['disp_type']=='gallery'){?>
	<img name="disp_gallery" src="/shop/data/skin/campingyo/img/common/btn_gallery_on.gif">
<?php }else{?>
	<img name="disp_gallery" src="/shop/data/skin/campingyo/img/common/btn_gallery_off.gif">
<?php }?>
</a>
<?php }?>
</td>
</tr>
</table>
</td>
</tr>
<tr>
<td>
<!-- capture_start("list_top") -->
<table width=100% cellpadding=0 cellspacing=0>
<tr>
<td><img src="/shop/data/skin/campingyo/img/common/goodlist_txt_01.gif"><a href="javascript:sort('maker desc')"><img name=sort_maker_desc src="/shop/data/skin/campingyo/img/common/list_up_off.gif"></a><a href="javascript:sort('maker')"><img name=sort_maker src="/shop/data/skin/campingyo/img/common/list_down_off.gif"></a><img src="/shop/data/skin/campingyo/img/common/goodlist_txt_03.gif"><a href="javascript:sort('goodsnm desc')"><img name=sort_goodsnm_desc src="/shop/data/skin/campingyo/img/common/list_up_off.gif"></a><a href="javascript:sort('goodsnm')"><img name=sort_goodsnm src="/shop/data/skin/campingyo/img/common/list_down_off.gif"></a><img src="/shop/data/skin/campingyo/img/common/goodlist_txt_04.gif"><a href="javascript:sort('price desc')"><img name=sort_price_desc src="/shop/data/skin/campingyo/img/common/list_up_off.gif"></a><a href="javascript:sort('price')"><img name=sort_price src="/shop/data/skin/campingyo/img/common/list_down_off.gif"></a><img src="/shop/data/skin/campingyo/img/common/goodlist_txt_05.gif"><a href="javascript:sort('reserve desc')"><img name=sort_reserve_desc src="/shop/data/skin/campingyo/img/common/list_up_off.gif"></a><a href="javascript:sort('reserve')"><img name=sort_reserve src="/shop/data/skin/campingyo/img/common/list_down_off.gif"></a></td>
<td align=right><img src="/shop/data/skin/campingyo/img/common/goodlist_txt_06.gif" align=absmiddle><select onchange="this.form.page_num.value=this.value;this.form.submit()" style="font:8pt 돋움"><?php if((is_array($TPL_R1=$TPL_VAR["lstcfg"]["page_num"])&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?><option value="<?php echo $TPL_V1?>" <?php echo $GLOBALS["selected"]["page_num"][$TPL_V1]?>><?php echo $TPL_V1?>개씩 정렬<?php }}?></select></td>
</tr>
</table>

<script>
color2Tag('selectedColor');

function remove_txt(obj){
	obj.style.cssText = "text-align:left;";
	obj.value="";
}
function search_refind(obj){
	var frm = document.frmList;
	if(obj.checked) tmp = frm.sword.value;
	else tmp = '';
	frm.sword.value = '';
	frm.hid_sword.value = tmp;
}
function add_param_submit (param_nm, param_val){
	var frm = document.frmList;
	if( param_nm == 'sword') frm.sword.value = param_val;
	else frm.disp_type.value = param_val;
	frm.submit();
}
function sort(sort)
{
var fm = document.frmList;
fm.sort.value = sort;
fm.submit();
}
function sort_chk(sort)
{
if (!sort) return;
sort = sort.replace(" ","_");
var obj = document.getElementsByName('sort_'+sort);
if (obj.length){
div = obj[0].src.split('list_');
for (i=0;i<obj.length;i++){
chg = (div[1]=="up_off.gif") ? "up_on.gif" : "down_on.gif";
obj[i].src = div[0] + "list_" + chg;
}
}
}
<?php if($_GET['sort']){?>
if (is_sort) sort_chk('<?php echo $_GET['sort']?>');
<?php }?>
var is_sort = 1;
</script>
<!-- capture_end ("list_top") -->
</td>
</tr>
<tr><td height=1 bgcolor=#DDDDDD style="padding:0px"></td></tr>
<tr>
<td style="padding:15 0">
<?php echo $this->assign('cols',$TPL_VAR["lstcfg"]["cols"])?>

<?php echo $this->assign('size',$TPL_VAR["lstcfg"]["size"])?>

<?php echo $this->define('tpl_include_file_1',"goods/list/".$TPL_VAR["lstcfg"]["tpl"].".htm")?> <?php $this->print_("tpl_include_file_1",$TPL_SCP,1);?>

</td>
</tr>
<tr><td height=1 bgcolor=#DDDDDD style="padding:0px"></td></tr>
<tr>
<td>
<!-- capture_start("list_top") -->
<table width=100% cellpadding=0 cellspacing=0>
<tr>
<td><img src="/shop/data/skin/campingyo/img/common/goodlist_txt_01.gif"><a href="javascript:sort('maker desc')"><img name=sort_maker_desc src="/shop/data/skin/campingyo/img/common/list_up_off.gif"></a><a href="javascript:sort('maker')"><img name=sort_maker src="/shop/data/skin/campingyo/img/common/list_down_off.gif"></a><img src="/shop/data/skin/campingyo/img/common/goodlist_txt_03.gif"><a href="javascript:sort('goodsnm desc')"><img name=sort_goodsnm_desc src="/shop/data/skin/campingyo/img/common/list_up_off.gif"></a><a href="javascript:sort('goodsnm')"><img name=sort_goodsnm src="/shop/data/skin/campingyo/img/common/list_down_off.gif"></a><img src="/shop/data/skin/campingyo/img/common/goodlist_txt_04.gif"><a href="javascript:sort('price desc')"><img name=sort_price_desc src="/shop/data/skin/campingyo/img/common/list_up_off.gif"></a><a href="javascript:sort('price')"><img name=sort_price src="/shop/data/skin/campingyo/img/common/list_down_off.gif"></a><img src="/shop/data/skin/campingyo/img/common/goodlist_txt_05.gif"><a href="javascript:sort('reserve desc')"><img name=sort_reserve_desc src="/shop/data/skin/campingyo/img/common/list_up_off.gif"></a><a href="javascript:sort('reserve')"><img name=sort_reserve src="/shop/data/skin/campingyo/img/common/list_down_off.gif"></a></td>
<td align=right><img src="/shop/data/skin/campingyo/img/common/goodlist_txt_06.gif" align=absmiddle><select onchange="this.form.page_num.value=this.value;this.form.submit()" style="font:8pt 돋움"><?php if((is_array($TPL_R1=$TPL_VAR["lstcfg"]["page_num"])&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?><option value="<?php echo $TPL_V1?>" <?php echo $GLOBALS["selected"]["page_num"][$TPL_V1]?>><?php echo $TPL_V1?>개씩 정렬<?php }}?></select></td>
</tr>
</table>

<script>
color2Tag('selectedColor');

function remove_txt(obj){
	obj.style.cssText = "text-align:left;";
	obj.value="";
}
function search_refind(obj){
	var frm = document.frmList;
	if(obj.checked) tmp = frm.sword.value;
	else tmp = '';
	frm.sword.value = '';
	frm.hid_sword.value = tmp;
}
function add_param_submit (param_nm, param_val){
	var frm = document.frmList;
	if( param_nm == 'sword') frm.sword.value = param_val;
	else frm.disp_type.value = param_val;
	frm.submit();
}
function sort(sort)
{
var fm = document.frmList;
fm.sort.value = sort;
fm.submit();
}
function sort_chk(sort)
{
if (!sort) return;
sort = sort.replace(" ","_");
var obj = document.getElementsByName('sort_'+sort);
if (obj.length){
div = obj[0].src.split('list_');
for (i=0;i<obj.length;i++){
chg = (div[1]=="up_off.gif") ? "up_on.gif" : "down_on.gif";
obj[i].src = div[0] + "list_" + chg;
}
}
}
<?php if($_GET['sort']){?>
if (is_sort) sort_chk('<?php echo $_GET['sort']?>');
<?php }?>
var is_sort = 1;
</script>
<!-- capture_end ("list_top") -->
</td>
</tr>
<tr><td height=2 bgcolor=#DDDDDD></td></tr>
<tr><td align=center height=30 bgcolor="#F3F3F3"><?php echo $TPL_VAR["pg"]->page['navi']?></td></tr>
</table>

</form>
<br><br>

</div><!-- End indiv -->

<?php $this->print_("footer",$TPL_SCP,1);?>