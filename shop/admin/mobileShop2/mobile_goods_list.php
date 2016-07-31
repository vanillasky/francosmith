<?php

$location = "모바일샵관리 > 모바일샵 상품관리";
include "../_header.php";
include "../../lib/page.class.php";
include "../../conf/config.pay.php";

if(!$_GET[set_open_mobile]) $_GET[set_open_mobile] = 'true';

$selected[page_num][$_GET[page_num]] = "selected";
$selected[skey][$_GET[skey]] = "selected";
$selected[brandno][$_GET[brandno]] = "selected";

$checked[open][$_GET[open]] = "checked";
$checked[open_mobile][$_GET[open_mobile]] = "checked";
$checked[mlongdesc][$_GET[mlongdesc]] = "checked";
$checked[set_open_mobile][$_GET[set_open_mobile]] = "checked";

if($_GET['searchYn']=='y'){

### 공백 제거
$_GET[sword] = trim($_GET[sword]);

list ($total) = $db->fetch("select count(*) from ".GD_GOODS."");

if (!$_GET[page_num]) $_GET[page_num] = 10;

$orderby = ($_GET[sort]) ? $_GET[sort] : "-a.goodsno";
$div = explode(" ",$orderby);
$flag['sort'][$div[0]] = (!preg_match("/desc$/i",$orderby)) ? "▲" : "▼";

if ($_GET[cate]){
	$category = array_notnull($_GET[cate]);
	$category = $category[count($category)-1];
}

$db_table = "
".GD_GOODS." a
left join ".GD_GOODS_OPTION." b on a.goodsno=b.goodsno and link and go_is_deleted <> '1'
";

if ($category){
	$db_table .= "left join ".GD_GOODS_LINK." c on a.goodsno=c.goodsno";
	$where[] = "category like '$category%'";
}
if ($_GET[sword]) $where[] = "$_GET[skey] like '%$_GET[sword]%'";
if ($_GET[price][0] && $_GET[price][1]) $where[] = "price between {$_GET[price][0]} and {$_GET[price][1]}";
if ($_GET[brandno]) $where[] = "brandno='$_GET[brandno]'";
if ($_GET[regdt][0] && $_GET[regdt][1]) $where[] = "regdt between date_format({$_GET[regdt][0]},'%Y-%m-%d 00:00:00') and date_format({$_GET[regdt][1]},'%Y-%m-%d 23:59:59')";
if ($_GET[open]) $where[] = "open=".substr($_GET[open],-1);
if (strlen($_GET[open_mobile])>0) $where[] = "open_mobile=".$_GET[open_mobile];
if ($_GET[mlongdesc]) {
	if ($_GET[mlongdesc] == 'Y') $where[] = "length(mlongdesc)>0";
	else if ($_GET[mlongdesc] == 'N') $where[] = "( mlongdesc is null or length(mlongdesc)=0 )";
}

$pg = new Page($_GET[page],$_GET[page_num]);
$pg->field = "
distinct a.goodsno,a.goodsnm,a.img_s,a.icon,a.open,a.regdt,a.runout,a.usestock,a.inpk_prdno,a.totstock,a.open_mobile,a.mlongdesc,
b.price,b.reserve,a.use_emoney
";
$pg->setQuery($db_table,$where,$orderby);

$pg->exec();
$res = $db->query($pg->query);

}
?>
<script>

function eSort(obj,fld)
{
	var form = document.frmList;
	if (obj.innerText.charAt(1)=="▲") fld += " desc";
	form.sort.value = fld;
	form.submit();
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

function iciSelect(obj)
{
	//var row = obj.parentNode.parentNode;
	//row.style.background = (obj.checked) ? "#F9FFA1" : row.getAttribute('bg');
}

function chkBoxAll(El,mode)
{
	if (!El || !El.length) return;

	for (i=0;i<El.length;i++){
		El[i].checked = (mode=='rev') ? !El[i].checked : mode;
		iciSelect(El[i]);
	}
}

window.onload = function(){ sort_chk('<?=$_GET[sort]?>'); }

// 지정된 상품의 모바일 노출/미노출을 지정한다.
function onclickMobileView()
{
	var frm = document.frmList;
	var org_action;
	var org_method;

	var tag_chk, tcount ;
	tag_chk = document.getElementsByName("chk[]");

	for (i=0; i<tag_chk.length; i++) {
		if (tag_chk[i].checked)
			tcount ++;
	}

	org_action = frm.action ;
	org_method = frm.action ;

	frm.action = "mobile_goods_indb.php";
	frm.method = "post";

	frm.submit();

	frm.action = org_action;
	frm.method = org_method;
}

</script>

<form name=frmList>
<input type='hidden' name='searchYn' value='y' />
<input type='hidden' name='sort' value="<?=$_GET['sort']?>">
<input type='hidden' name='m_mode' value='' />
<input type='hidden' name='m_cate' value='<?=$category?>' />
<input type='hidden' name='m_skey' value='<?=$_GET[skey]?>' />
<input type='hidden' name='m_sword' value='<?=$_GET[sword]?>' />
<input type='hidden' name='m_open' value='<?=substr($_GET[open],-1)?>' />
<input type='hidden' name='m_price_0' value='<?=$_GET[price][0]?>' />
<input type='hidden' name='m_price_1' value='<?=$_GET[price][1]?>' />
<input type='hidden' name='m_brandno' value='<?=$_GET[brandno]?>' />
<input type='hidden' name='m_regdt_0' value='<?=$_GET[regdt][0]?>' />
<input type='hidden' name='m_regdt_1' value='<?=$_GET[regdt][1]?>' />
<input type='hidden' name='m_open' value='<?=$_GET[open]?>' />
<input type='hidden' name='m_open_mobile' value='<?=$_GET[open_mobile]?>' />

<div class="title title_top">모바일샵 상품관리<span>온라인쇼핑몰 상품을 검색하여, 모바일샵에 노출여부를 관리할 수 있습니다.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=mobileshopV2&no=5')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<table class=tb>
<col class=cellC><col class=cellL style="width:250px">
<col class=cellC><col class=cellL>
<tr>
	<td>분류선택</td>
	<td colspan=3><script>new categoryBox('cate[]',4,'<?=$category?>');</script></td>
</tr>
<tr>
	<td>검색어</td>
	<td colspan=3>
	<select name=skey>
	<option value="goodsnm" <?=$selected[skey][goodsnm]?>>상품명
	<option value="a.goodsno" <?=$selected[skey][a.goodsno]?>>고유번호
	<option value="goodscd" <?=$selected[skey][goodscd]?>>상품코드
	<option value="keyword" <?=$selected[skey][keyword]?>>유사검색어
	</select>
	<input type=text name="sword" value="<?=$_GET[sword]?>" class="line" style="height:22px">
	</td>
</tr>
<tr>
	<td>상품가격</td>
	<td><font class=small color=444444>
	<input type=text name=price[] value="<?=$_GET[price][0]?>" onkeydown="onlynumber()" size="15" class="rline"> 원 -
	<input type=text name=price[] value="<?=$_GET[price][1]?>" onkeydown="onlynumber()" size="15" class="rline"> 원
	</td>
	<td>브랜드</td>
	<td>
	<select name=brandno>
	<option value="">-- 브랜드 선택 --
	<?
	$bRes = $db->query("select * from gd_goods_brand order by sort");
	while ($tmp=$db->fetch($bRes)){
	?>
	<option value="<?=$tmp[sno]?>" <?=$selected[brandno][$tmp[sno]]?>><?=$tmp[brandnm]?>
	<? } ?>
	</select>
	</td>
</tr>
<tr>
	<td>상품등록일</td>
	<td colspan=3>
	<input type=text name=regdt[] value="<?=$_GET[regdt][0]?>" onclick="calendar(event)" onkeydown="onlynumber()" class="cline"> -
	<input type=text name=regdt[] value="<?=$_GET[regdt][1]?>" onclick="calendar(event)" onkeydown="onlynumber()" class="cline">
	<a href="javascript:setDate('regdt[]',<?=date("Ymd")?>,<?=date("Ymd")?>)"><img src="../img/sicon_today.gif" align=absmiddle></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-7 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_week.gif" align=absmiddle></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-15 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twoweek.gif" align=absmiddle></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-1 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_month.gif" align=absmiddle></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-2 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twomonth.gif" align=absmiddle></a>
	<a href="javascript:setDate('regdt[]')"><img src="../img/sicon_all.gif" align=absmiddle></a>
	</td>
</tr>
<tr>
	<td>모바일상세설명</td>
	<td class=noline colspan=3>
	<input type=radio name=mlongdesc value="" <?=$checked[mlongdesc]['']?>>전체
	<input type=radio name=mlongdesc value="Y" <?=$checked[mlongdesc][11]?>>존재
	<input type=radio name=mlongdesc value="N" <?=$checked[mlongdesc][10]?>>미존재
	</td>
</tr>
<tr>
	<td>상품출력여부</td>
	<td class=noline>
	<input type=radio name=open value="" <?=$checked[open]['']?>>전체
	<input type=radio name=open value="11" <?=$checked[open][11]?>>출력상품
	<input type=radio name=open value="10" <?=$checked[open][10]?>>미출력상품
	</td>
	<td>모바일출력여부</td>
	<td class=noline>
	<input type=radio name=open_mobile value="" <?=$checked[open_mobile]['']?>>전체
	<input type=radio name=open_mobile value="1" <?=$checked[open_mobile][1]?>>출력상품
	<input type=radio name=open_mobile value="0" <?=$checked[open_mobile][0]?>>미출력상품
	</td>
</tr>
</table>
<div class=button_top><input type=image src="../img/btn_search2.gif"></div>

<div style="padding-top:15px"></div>
<table width=100% cellpadding=0 cellspacing=0>
<tr>
	<td class=pageInfo><font class=ver8>
	총 <b><?=$total?></b>개, 검색 <b><?=$pg->recode[total]?></b>개, <b><?=$pg->page[now]?></b> of <?=$pg->page[total]?> Pages
	</td>
	<td align=right>

	<table cellpadding=0 cellspacing=0 border=0>
	<tr>
		<td valign=bottom>
		<img src="../img/sname_date.gif"><a href="javascript:sort('regdt desc')"><img name=sort_regdt_desc src="../img/list_up_off.gif"></a><a href="javascript:sort('regdt')"><img name=sort_regdt src="../img/list_down_off.gif"></a><img src="../img/sname_dot.gif"><img src="../img/sname_product.gif"><a href="javascript:sort('goodsnm desc')"><img name=sort_goodsnm_desc src="../img/list_up_off.gif"></a><a href="javascript:sort('goodsnm')"><img name=sort_goodsnm src="../img/list_down_off.gif"></a><img src="../img/sname_dot.gif"><img src="../img/sname_price.gif"><a href="javascript:sort('price desc')"><img name=sort_price_desc src="../img/list_up_off.gif"></a><a href="javascript:sort('price')"><img name=sort_price src="../img/list_down_off.gif"></a><img src="../img/sname_dot.gif"><img src="../img/sname_brand.gif"><a href="javascript:sort('brandno desc')"><img name=sort_brandno_desc src="../img/list_up_off.gif"></a><a href="javascript:sort('brandno')"><img name=sort_brandno src="../img/list_down_off.gif"></a><img src="../img/sname_dot.gif"><img src="../img/sname_company.gif"><a href="javascript:sort('maker desc')"><img name=sort_maker_desc src="../img/list_up_off.gif"></a><a href="javascript:sort('maker')"><img name=sort_maker src="../img/list_down_off.gif"></a></td>
		<td style="padding-left:20px">
		<img src="../img/sname_output.gif" align=absmiddle>
		<select name=page_num onchange="this.form.submit()">
		<?
		$r_pagenum = array(10,20,40,60,100);
		foreach ($r_pagenum as $v){
		?>
		<option value="<?=$v?>" <?=$selected[page_num][$v]?>><?=$v?>개 출력
		<? } ?>
		</select>
		</td>
	</tr>
	</table>

	</td>
</tr>
</table>

<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr><td class=rnd colspan=10></td></tr>
<tr class="rndbg">
	<th width="30"><a href="javascript:void(0)" onClick="chkBoxAll(document.getElementsByName('chk[]'),'rev')" class=white>선택</a></th>
	<th></th>
	<th width=10></th>
	<th>상품명</th>
	<th width="60">등록일</th>
	<th width="100">모바일웹 상세설명</th>
	<th width="90">가격</th>
	<th width="60">재고</th>
	<th width="60">PC진열</th>
	<th width="60">모바일진열</th>
</tr>
<tr><td class="rnd" colspan="14"></td></tr>
<?
while ($data=$db->fetch($res)){
	$stock = $data['totstock'];

?>
<tr><td height=4 colspan=10></td></tr>
<tr height=25>
	<td class="noline"><input type=checkbox name=chk[] value="<?=$data[goodsno]?>" onclick="iciSelect(this)" required label=">선택사항이 없습니다" <?=$disabled?>></td>
	<td style="border:1px #e9e9e9 solid;"><a href="../../goods/goods_view.php?goodsno=<?=$data[goodsno]?>" target=_blank><?=goodsimg($data[img_s],40,'',1)?></a></td>
	<td></td>
	<td>
	<a href="javascript:popup('../goods/popup.register.php?mode=modify&goodsno=<?=$data[goodsno]?>',850,600)"><font color=303030><!--<a href="register.php?mode=modify&goodsno=<?=$data[goodsno]?>">--><?=$data[goodsnm]?></a>
	<? if ($icon){ ?><div style="padding-top:3px"><?=$icon?></div><? } ?>
	<? if ($data[runout]){ ?><div style="padding-top:3px"><img src="../../data/skin/<?=$cfg[tplSkin]?>/img/icon/good_icon_soldout.gif"></div><? } ?>
	</td>
	<td align=center><font class=ver81 color=444444><?=substr($data[regdt],0,10)?></td>
	<td align=center><? if (strlen($data['mlongdesc'])>0) { echo "<a href=\"javascript:popup('../goods/popup.register.php?mode=modify&goodsno={$data[goodsno]}&call=tabLongdescShow#tabLongdesc',825,600)\"><img src='../img/btn_viewbbs.gif' border=0></a>"; } else { echo "-"; } ?></td>
	<td align=center>
	<font color=4B4B4B><font class=ver81 color=444444><b><?=number_format($data[price])?></b></font>
	<div style="padding-top:2px"></div>
	<img src="../img/good_icon_point.gif" align=absmiddle><font class=ver8><?=number_format($data[reserve])?></font>
	</td>
	<td align=center><font class=ver81 color=444444><?=number_format($stock)?></td>
	<td align=center><img src="../img/icn_<?=$data[open]?>.gif"></td>
	<td align=center><img src="../img/icn_<?=$data[open_mobile]?>.gif"></td>
</tr>
<tr><td height=4></td></tr>
<tr><td colspan=12 class=rndline></td></tr>
<?
}
?>
</table>

<div align=center class=pageNavi><font class=ver8><?=$pg->page[navi]?></font></div>

<div style="padding-top:15px"></div>

<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td style="text-align:center;">
		모바일샵 진열<br />
		<select name='range_type1' style='margin-top:5px;width:150px'>
			<option value='query_select'>선택된 상품을 </option>
			<option value='query_all'>검색된 모든 상품을</option>
		</select>
	</td>
	<td class="noline">
		<div><label><input type='radio' name='set_open_mobile' value='true' <?=$checked['set_open_mobile']['true']?> />선택/검색한 상품을 모바일샵에 진열합니다.</label></div>
		<div><label><input type='radio' name='set_open_mobile' value='false' <?=$checked['set_open_mobile']['false']?> />선택/검색한 상품을 모바일샵에 진열하지 않습니다.</label></div>
		<div><label><input type='radio' name='set_open_mobile' value='same' <?=$checked['set_open_mobile']['same']?> />선택/검색한 상품을 온라인샵 진열과 동일하게 설정합니다.</label></div>
		<div class="small" style="padding:10px 0 0 5px;"><font class="extext">( 별도의 설정을 하지 않으면, 온라인쇼핑몰의 상품이 모두 모바일샵에 진열 됩니다 )</font></div>
	</td>
</tr>
</table>


<p style="text-align:center;"><a href="javascript:onclickMobileView();"><img src="../img/btn_modify.gif" border="0" /></a></p>

</form>

<div style="padding-top:15px"></div>

<? include "../_footer.php"; ?>