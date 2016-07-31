<?

$location = "인터파크 오픈스타일 입점 > 구매확정내역";
include "../_header.php";
include "../../lib/page.class.php";

list ($total) = $db->fetch("select count(*) from ".GD_ORDER_ITEM." where inpk_compdt>0");

### 변수할당
if (!$_GET['page_num']) $_GET['page_num'] = 20; # 페이지 레코드수
$selected['page_num'][$_GET['page_num']] = "selected";

$orderby = ($_GET['sort']) ? $_GET['sort'] : "inpk_compdt desc"; # 정렬 쿼리
$selected['sort'][$orderby] = "selected";

$selected['skey'][$_GET['skey']] = "selected";

### 목록
$db_table = GD_ORDER_ITEM;

$where[] = "inpk_compdt>0";
if ($_GET['sword']){
	$_GET['sword'] = trim($_GET['sword']);
	$t_skey = ($_GET['skey']=="all") ? "concat(goodsnm, brandnm, maker, goodsno)" : $_GET['skey'];
	$where[] = "$t_skey like '%{$_GET['sword']}%'";
}
if ($_GET['regdt'][0]){
	if (!$_GET['regdt'][1]) $_GET['regdt'][1] = date("Ymd");
	$where[] = "inpk_compdt between date_format({$_GET['regdt'][0]},'%Y-%m-%d 00:00:00') and date_format({$_GET['regdt'][1]},'%Y-%m-%d 23:59:59')";
}

$pg = new Page($_GET[page],$_GET[page_num]);
$pg->field = "*";
$pg->setQuery($db_table,$where,$orderby);
$pg->exec();

$res = $db->query($pg->query);

?>

<div class="title title_top">구매확정내역<span>인터파크로부터 구매확정된 주문상품 내역입니다.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=marketing&no=26')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<div style="padding:8px 13px;background:#f7f7f7;border:3px solid #C6C6C6;margin-bottom:18px;" id="goodsInfoBox">
<div><font color="#EA0095"><b>필독!</b></font></div>
<div style="padding-top:2">구매자가 <font color=EA0095>구매확정한 주문만 인터파크 정산에 포함</font>되며, 출고지시 후 14일 이후 자동구매확정됩니다.</div>
<div style="padding-top:2">본 내역은 인터파크와 <font color=0074BA>정산할 때 참고 자료로만 사용</font>하세요.</div>
</div>


<!-- 검색조건 : start -->
<form name=frmList onsubmit="return chkForm(this)">

<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td><font class=small1>검색 (통합)</td>
	<td>
	<select name=skey>
	<option value="all"> = 통합검색 =
	<option value="goodsnm" <?=$selected['skey']['goodsnm']?>> 상품명
	<option value="brandnm" <?=$selected['skey']['brandnm']?>> 브랜드
	<option value="maker" <?=$selected['skey']['maker']?>> 제조사
	<option value="goodsno" <?=$selected['skey']['goodsno']?>>고유번호
	</select>
	<input type=text name=sword value="<?=$_GET['sword']?>">
	</td>
</tr>
<tr>
	<td><font class=small1>구매확정일</td>
	<td colspan=3>
	<input type=text name=regdt[] value="<?=$_GET['regdt'][0]?>" onclick="calendar()" size=12> -
	<input type=text name=regdt[] value="<?=$_GET['regdt'][1]?>" onclick="calendar()" size=12>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd")?>,<?=date("Ymd")?>)"><img src="../img/sicon_today.gif" align=absmiddle></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-7 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_week.gif" align=absmiddle></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-15 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twoweek.gif" align=absmiddle></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-1 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_month.gif" align=absmiddle></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-2 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twomonth.gif" align=absmiddle></a>
	<a href="javascript:setDate('regdt[]')"><img src="../img/sicon_all.gif" align=absmiddle></a>
	</td>
</tr>
</table>
<div class=button_top><input type=image src="../img/btn_search2.gif"></div>

<table width=100%>
<tr>
	<td class=pageInfo><font class=ver8>
	총 <b><?=number_format($total)?></b>개, 검색 <b><?=number_format($pg->recode[total])?></b>개, <b><?=number_format($pg->page[now])?></b> of <?=number_format($pg->page[total])?> Pages
	</td>
	<td align=right>
	<select name="sort" onchange="this.form.submit();">
	<option value="inpk_compdt desc" <?=$selected[sort]['inpk_compdt desc']?>>- 구매확정일 정렬↑</option>
	<option value="inpk_compdt asc" <?=$selected[sort]['inpk_compdt asc']?>>- 구매확정일 정렬↓</option>
    <optgroup label="------------"></optgroup>
	<option value="ordno desc" <?=$selected[sort]['ordno desc']?>>- 주문번호 정렬↑</option>
	<option value="ordno asc" <?=$selected[sort]['ordno asc']?>>- 주문번호 정렬↓</option>
	</select>&nbsp;
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

</form>
<!-- 검색조건 : end -->


<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr><td class=rnd colspan=12></td></tr>
<tr class=rndbg>
	<th><font class=small1><b>번호</th>
	<th><font class=small1><b>주문번호</th>
	<th><font class=small1><b>상품명</th>
	<th><font class=small1><b>수량</th>
	<th><font class=small1><b>상품가격</th>
	<th><font class=small1><b>소계</th>
	<th><font class=small1><b>매입가</th>
	<th><font class=small1><b>구매확정일</th>
</tr>
<tr><td class=rnd colspan=12></td></tr>
<col width=35><col width=120><col><col width=60><col width=60 span=3><col width=80>
<?
while (is_resource($res) && $data=$db->fetch($res))
{
	$goodsnm = $data['goodsnm'];
	if ($data['opt1']) $goodsnm .= "[{$data['opt1']}" . ($data['opt2'] ? "/{$data['opt2']}" : "") . "]";
	if ($data['addopt']) $goodsnm .= "<div>[" . str_replace("^","] [",$data[addopt]) . "]</div>";
?>
<tr><td height=4 colspan=12></td></tr>
<tr height=18>
	<td align=center><font class="ver8" color="#616161"><?=$pg->idx--?></font></td>
	<td>
	<a href="../order/view.php?ordno=<?=$data['ordno']?>"><font class=ver81 color=0074BA><b><?=$data['ordno']?></b></font></a>
	<a href="javascript:popup('../order/popup.order.php?ordno=<?=$data['ordno']?>',800,600)"><img src="../img/btn_newwindow.gif" border=0 align=absmiddle></a>
	</td>
	<td>
	<font class=small><?=$goodsnm?></font>
	<div style="padding-top:3"><font class=small1 color=6d6d6d>제조사 : <?=$data[maker] ? $data[maker] : '없음'?></div>
	<div><font class=small1 color=6d6d6d>브랜드 : <?=$data[brandnm] ? $data[brandnm] : '없음'?></div>
	</td>
	<td align=center><?=number_format($data[ea])?></td>
	<td align=center><?=number_format($data[price])?></td>
	<td align=center><?=number_format($data[price]*$data[ea])?></td>
	<td align=center><?=number_format($data[supply])?></td>
	<td align=center><font class="small" color="#444444"><?=substr($data['inpk_compdt'],2,8)?></font></td>
</tr>
<tr><td height=4></td></tr>
<tr><td colspan=12 class=rndline></td></tr>
<? } ?>
</table>

<div align=center class=pageNavi><font class=ver8><?=$pg->page[navi]?></font></div>


<? include "../_footer.php"; ?>