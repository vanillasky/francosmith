<?
// deprecated. redirect to new page;
header('location: ./adm_goods_list.php?'.$_SERVER['QUERY_STRING']);
exit;
//$hiddenLeft = 1;
$location = "상품관리 > 상품리스트";
include "../_header.php";
include "../../lib/page.class.php";
include "../../conf/config.pay.php";

### 공백 제거
$_GET[sword] = trim($_GET[sword]);

list ($total) = $db->fetch("select count(*) from ".GD_GOODS." WHERE todaygoods='n'");

if (!$_GET[page_num]) $_GET[page_num] = 10;
$selected[page_num][$_GET[page_num]] = "selected";
$selected[skey][$_GET[skey]] = "selected";
$selected[brandno][$_GET[brandno]] = "selected";
$checked[open][$_GET[open]] = "checked";
$checked[blog][$_GET[blog]] = "checked";
if (!$_GET[stock_type]) $_GET[stock_type] = "product";
$checked[stock_type][$_GET[stock_type]] = "checked";

$orderby = ($_GET[sort]) ? $_GET[sort] : "-a.goodsno";
$div = explode(" ",$orderby);
$flag['sort'][$div[0]] = (!preg_match("/desc$/i",$orderby)) ? "▲" : "▼";

if ($_GET[cate]){
	$category = array_notnull($_GET[cate]);
	$category = $category[count($category)-1];
}

$db_table = "
".GD_GOODS." a
left join ".GD_GOODS_OPTION." b on a.goodsno=b.goodsno and link
";
$where[] = "a.todaygoods='n'";
if ($category){
	$db_table .= "left join ".GD_GOODS_LINK." c on a.goodsno=c.goodsno";
	$where[] = "category like '$category%'";
}
if ($_GET[sword]) $where[] = "$_GET[skey] like '%$_GET[sword]%'";
if ($_GET[price][0] && $_GET[price][1]) $where[] = "price between {$_GET[price][0]} and {$_GET[price][1]}";
if ($_GET[brandno]) $where[] = "brandno='$_GET[brandno]'";
if ($_GET[regdt][0] && $_GET[regdt][1]) $where[] = "regdt between date_format({$_GET[regdt][0]},'%Y-%m-%d 00:00:00') and date_format({$_GET[regdt][1]},'%Y-%m-%d 23:59:59')";
if ($_GET[open]) $where[] = "open=".substr($_GET[open],-1);
if ($_GET['blog']) $where[] = "useblog='y'";
if ($_GET[sOrigin]) $where[] = "origin like '%$_GET[sOrigin]%'";

// 상품재고수량 검색조건
$add_stock_sub_table = false;
if ($_GET['tot_stock'][0] != '') {
	$tot_stock_start = intval($_GET['tot_stock'][0]);
	$add_stock_sub_table = true;
}
if ($_GET['tot_stock'][1] != '') {
	$tot_stock_end = intval($_GET['tot_stock'][1]);
	$add_stock_sub_table = true;
}
if ($add_stock_sub_table) {
	$sub_table = GD_GOODS." a left join ".GD_GOODS_OPTION." b on a.goodsno=b.goodsno";

	$where[] = "a.usestock = 'o'";
	$where_sub[] = "a.usestock = 'o'";

	if ($category){
		$sub_table.= " JOIN (select distinct goodsno from gd_goods_link where category LIKE '$category%') Y on Y.goodsno = a.goodsno";
	}

	if ($_GET['stock_type'] == 'product') {
		$sub_table_query = " SELECT a.goodsno, sum(b.stock) as tstock ";
		$sub_table_query.= " FROM ".$sub_table." ";
		$sub_table_query.= " WHERE ".implode(" AND ", $where_sub);
		$sub_table_query.= " group by a.goodsno ";

		$db_table.= " JOIN (".$sub_table_query.") X on X.goodsno = a.goodsno";
		if ($_GET['tot_stock'][0] != '') {
			$where[] = "X.tstock >= ".$tot_stock_start." ";
		}
		if ($_GET['tot_stock'][1] != '') {
			$where[] = "X.tstock <= ".$tot_stock_end." ";
		}
	}
	else if ($_GET['stock_type'] == 'item') {
		if ($_GET['tot_stock'][0] != '') {
			$where_sub[] = " b.stock >= ".$tot_stock_start." ";
		}
		if ($_GET['tot_stock'][1] != '') {
			$where_sub[] = " b.stock <= ".$tot_stock_end." ";
		}

		$sub_item_table_query = " SELECT a.goodsno ";
		$sub_item_table_query.= " FROM ".$sub_table." ";
		$sub_item_table_query.= " WHERE ".implode(" AND ", $where_sub);
		$sub_item_table_query.= " group by a.goodsno ";
		$db_table.= " JOIN (".$sub_item_table_query.") Z on Z.goodsno = a.goodsno ";
	}
}

$pg = new Page($_GET[page],$_GET[page_num]);
$pg->field = "
distinct a.goodsno,a.goodsnm,a.img_s,a.icon,a.open,a.regdt,a.runout,a.usestock,a.inpk_prdno,a.totstock,
b.price,b.reserve,a.use_emoney
";
$pg->setQuery($db_table,$where,$orderby);

$pg->exec();
$res = $db->query($pg->query);

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

window.onload = function(){ sort_chk('<?=$_GET[sort]?>'); }

</script>

<form name=frmList>
<input type=hidden name=sort value="<?=$_GET['sort']?>">

<div class="title title_top">전체상품리스트<span>등록하신 상품을 한눈에 살펴보시고 편리하게 수정하실 수 있습니다</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=2')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<table class=tb>
<col class=cellC><col class=cellL style="width:250px">
<col class=cellC><col class=cellL>
<tr>
	<td>분류선택</td>
	<td colspan=3><script>new categoryBox('cate[]',4,'<?=$category?>');</script></td>
</tr>
<tr>
	<td>검색어</td>
	<td>
	<select name=skey>
	<option value="goodsnm" <?=$selected[skey][goodsnm]?>>상품명
	<option value="a.goodsno" <?=$selected[skey][a.goodsno]?>>고유번호
	<option value="goodscd" <?=$selected[skey][goodscd]?>>상품코드
	<option value="keyword" <?=$selected[skey][keyword]?>>유사검색어
	</select>
	<input type=text name="sword" value="<?=$_GET[sword]?>" class="line" style="height:22px">
	</td>
	<td>원산지</td>
	<td><input type=text name="sOrigin" value="<?=$_GET[sOrigin]?>" class="line" style="height:22px"></td>
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
	<td>상품재고수량</td>
	<td colspan=3>
	<div>
		<input type=radio name='stock_type' value='product' <?=$checked[stock_type]['product']?> style='border:0;'/>상품재고(품목재고 합)
		<input type=radio name='stock_type' value='item' <?=$checked[stock_type]['item']?> style='border:0;'/>품목재고
	</div>
	<div class="small" style="color:#444444">
		<input type=text name=tot_stock[] value="<?=$_GET[tot_stock][0]?>" onkeydown="onlynumber()" size="15" class="rline"> 개 -
		<input type=text name=tot_stock[] value="<?=$_GET[tot_stock][1]?>" onkeydown="onlynumber()" size="15" class="rline"> 개
	</div>
	<div class="small">
		<font color="blue">상품재고:</font> 상품내 품목(가격옵션)별 재고 총합의 조건을 말합니다. 주문시 재고차감(재고량연동)인 상품만 조회대상이 됩니다.
	</div>
	<div class="small">
		<font color="blue">품목재고:</font> 품목(가격옵션) 개별 재고 조건을 말합니다. 주문시 재고차감(재고량연동)인 상품만 조회대상이 됩니다.
	</div>
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
	<td>상품출력여부</td>
	<td class=noline>
	<input type=radio name=open value="" <?=$checked[open]['']?>>전체
	<input type=radio name=open value="11" <?=$checked[open][11]?>>출력상품
	<input type=radio name=open value="10" <?=$checked[open][10]?>>미출력상품
	</td>
	<td>블로그 연동</td>
	<td class=noline>
	<input type=checkbox name=blog value="y" <?=$checked['blog']['y']?>>연동된 상품
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
</form>

<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr><td class=rnd colspan=12></td></tr>
<tr class=rndbg>
	<th width=60>번호</th>
	<th></th>
	<th width=10></th>
	<th>상품명</th>
	<th>등록일</th>
	<th>가격</th>
	<th>재고</th>
	<th>진열</th>
	<th>복사</th>
	<th>수정</th>
	<th>삭제</th>
</tr>
<tr><td class=rnd colspan=12></td></tr>
<col width=40 span=2 align=center>
<?
while ($data=$db->fetch($res)){
	$stock = $data['totstock'];

	### 적립금
	if(!$data['use_emoney']){
		if( !$set['emoney']['chk_goods_emoney'] ){
			if( $set['emoney']['goods_emoney'] ) $data['reserve'] = getDcprice($data['price'],$set['emoney']['goods_emoney'].'%');
		}else{
			$data['reserve']	= $set['emoney']['goods_emoney'];
		}
	}
	$icon = setIcon($data[icon],$data[regdt],"../");

	### 실재고에 따른 자동 품절 처리
	if ($data[usestock] && $stock==0) $data[runout] = 1;
?>
<tr><td height=4 colspan=12></td></tr>
<tr height=25>
	<td><font class=ver8 color=616161><?=$pg->idx--?></td>
	<td style="width:40px; border:1px #e9e9e9 solid;"><a href="../../goods/goods_view.php?goodsno=<?=$data[goodsno]?>" target=_blank><?=goodsimg($data[img_s],40,'',1)?></a></td>
	<td></td>
	<td>
	<!--<a href="javascript:popup('popup.register.php?mode=modify&goodsno=<?=$data[goodsno]?>',800,600)"><img src="../img/icon_popup.gif" hspace=2 align=absmiddle></a>-->
	<a href="javascript:popup('popup.register.php?mode=modify&goodsno=<?=$data[goodsno]?>',850,600)"><font color=303030><!--<a href="register.php?mode=modify&goodsno=<?=$data[goodsno]?>">--><?=$data[goodsnm]?></font></a>
	<? if ($icon){ ?><div style="padding-top:3px"><?=$icon?></div><? } ?>
	<? if ($data[runout]){ ?><div style="padding-top:3px"><img src="../../data/skin/<?=$cfg[tplSkin]?>/img/icon/good_icon_soldout.gif"></div><? } ?>
	</td>
	<td align=center><font class=ver81 color=444444><?=substr($data[regdt],0,10)?></td>
	<td align=center>
	<font color=4B4B4B><font class=ver81 color=444444><b><?=number_format($data[price])?></b></font>
	<div style="padding-top:2px"></div>
	<img src="../img/good_icon_point.gif" align=absmiddle><font class=ver8><?=number_format($data[reserve])?></font>
	</td>
	<td align=center><font class=ver81 color=444444><?=number_format($stock)?></td>
	<td align=center><img src="../img/icn_<?=$data[open]?>.gif"></td>
	<td align=center><a href="indb.php?mode=copyGoods&goodsno=<?=$data[goodsno]?>" onclick="return confirm('동일한 상품을 하나 더 자동등록합니다')"><img src="../img/i_copy.gif"></a></td>
	<td align=center><!--<a href="javascript:popup('popup.register.php?mode=modify&goodsno=<?=$data[goodsno]?>',825,600)">-->
	<a href="register.php?mode=modify&goodsno=<?=$data[goodsno]?>"><img src="../img/i_edit.gif"></a></td>
	<? if ($data[inpk_prdno] != '' && ($inpkCfg['use'] == 'Y'||$inpkOSCfg['use'] == 'Y')){ ?>
	<td align=center><span title="인터파크에 등록된 상품은 삭제할 수 없습니다.">×</span></td>
	<? } else { ?>
	<td align=center><a href="indb.php?mode=delGoods&goodsno=<?=$data[goodsno]?>" onclick="return confirm('정말로 삭제하시겠습니까?\n\n업로드된 상품이미지는 자동삭제됩니다.\n단, 상세정보에 쓰인 이미지는 다른 곳에서도 사용하고 있을 수 있으므로 자동 삭제되지 않습니다. \n\'디자인관리 > webFTP이미지관리 > data > editor\'에서 이미지체크 후 삭제관리하세요.')"><img src="../img/i_del.gif"></a></td>
	<? } ?>
</tr>
<tr><td height=4></td></tr>
<tr><td colspan=12 class=rndline></td></tr>
<? } ?>
</table>
<div align=center class=pageNavi><font class=ver8><?=$pg->page[navi]?></font></div>

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">현재까지 등록한 상품의 전체상품리스트입니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">복사버튼을 누르면 자동으로 똑같은 상품이 생성됩니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">상품정보를 수정하려면 수정버튼을 누르세요.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">상품이미지를 클릭하시면 해당 상품의 상세페이지</a>를 새창으로 보실 수 있습니다.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>


<? include "../_footer.php"; ?>
