<?

$location = "인터파크 오픈스타일 입점 > 상품일괄전송";
$scriptLoad = '<script src="../interpark/js/common.js"></script>';
include "../_header.php";
include "../../lib/page.class.php";

list ($total) = $db->fetch("select count(*) from ".GD_GOODS." where inpk_dispno!=''");

$selected[skey][$_GET[skey]] = "selected";
$checked[open][$_GET[open]] = "checked";
$checked[register][$_GET[register]] = "checked";
$checked[isall][$_GET[isall]] = "checked";

if ($_GET[indicate] == 'search'){
	$orderby = "a.goodsno desc";
	$where[] = "inpk_dispno!=''";
	$where[] = "a.todaygoods = 'n'";	// 투데이샵 상품 제외

	if ($_GET[cate]){
		$category = array_notnull($_GET[cate]);
		$category = $category[count($category)-1];
	}

	$db_table = "
	".GD_GOODS." a
	left join ".GD_GOODS_OPTION." b on a.goodsno=b.goodsno and link and go_is_deleted <> '1'
	";

	// 상품분류 연결방식 전환 여부에 따른 처리
	$whereArr	= getCategoryLinkQuery('c.category', $category);

	if ($category){
		$db_table .= "left join ".GD_GOODS_LINK." c on a.goodsno=c.goodsno";
		$where[]	= $whereArr['where'];
	}
	if ($_GET[register] =='Y') $where[] = "inpk_prdno!=''";
	else if ($_GET[register] =='N') $where[] = "inpk_prdno=''";
	if ($_GET[inpk_dispno]) $where[] = "inpk_dispno = '$_GET[inpk_dispno]'";
	if ($_GET[sword]) $where[] = "$_GET[skey] like '%$_GET[sword]%'";
	if ($_GET[open]) $where[] = "open=".substr($_GET[open],-1);

	$pg = new Page($_GET[page]);
	$pg->field = $whereArr['distinct']." a.goodsno,a.goodsnm,a.open,a.regdt,a.maker,a.inpk_dispno,a.inpk_prdno,a.totstock,b.*";
	$pg->setQuery($db_table,$where,$orderby);
	$pg->exec();

	$res = $db->query($pg->query);
}

?>

<script><!--
function iciSelect(obj)
{
	var row = obj.parentNode.parentNode;
	row.style.background = (obj.checked) ? "#F0F4FF" :"#FFFFFF";
}

function chkFormList(fObj){
	if (chkForm(fObj) === false) return false;
	if (fObj['isall'][0].checked === false && isChked(document.getElementsByName('chk[]')) === false){
		if (document.getElementsByName('chk[]').length) document.getElementsByName('chk[]')[0].focus();
		return false;
	}

	var msg = '';
	msg += '일괄적으로 전송하시겠습니까?';
	if (!confirm(msg)) return false;

	fObj.target = "_self";
	fObj.action = "transmit_action.php";
	return true;
}
--></script>

<div class="title title_top">상품일괄전송<span>상점 상품을 편리하게 인터파크에 전송 할 수 있습니다.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=marketing&no=23')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<div style="padding:8px 13px;background:#f7f7f7;border:3px solid #C6C6C6;margin-bottom:18px;" id="goodsInfoBox">
<div><font color="#EA0095"><b>필독! 상품일괄전송이란?</b></font></div>
<div style="padding-top:2"><font  color=777777>인터파크와 입점계약을 맺으면 상점은 모든 상품들을 인터파크로 전송해야 합니다.</div>
<div style="padding-top:2">입점전에 내 상점에 등록한 상품수가  많다면 하나하나 인터파크로 전송하는데 시간이 많이 걸리게 됩니다.</div>
<div style="padding-top:2"><font color=0074BA>아래 기능은 인터파크 오픈스타일에 입점전에 등록된 상품을 인터파크로 한꺼번에 일괄 전송하는 기능입니다.</font></div>
<div style="padding-top:2">물론, 상품리스트에서 한 상품씩 따로따로 전송해도 상관없습니다. 빠르게 일괄전송을 하려면 아래 기능을 사용하세요.</div>


<div style="padding-top:5"><font color="#EA0095"><b>상품일괄전송 순서</b></font></div>
<div style="padding-top:2">① 상품설명 이미지가 이미지호스팅을 이용하여 연결되어 있는지 체크하세요.</div>
<div style="padding-top:2">② 인터파크 카테고리를 좌측 <a href="./link.php">[분류일괄매칭]</a> 메뉴에서 일괄 매칭하세요</div>
<div style="padding-top:2">③ 매칭된 카테고리의 상품을 일괄 전송하시면 인터파크로 상품이 등록됩니다.</div>
</div>


<!-- 상품출력조건 : start -->
<form name=frmList onsubmit="return chkForm(this)">
<input type="hidden" name="indicate" value="search">

<div style="padding:10 0 5 5"><font class="def1" color="#EA0095"><b><font size="3">①</font> 먼저 아래에서 인터파크로 전송할 상품을 검색합니다.</b></font></div>
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>상점분류선택</td>
	<td>
	<script>new categoryBox('cate[]',4,'<?=$category?>');</script>
	</td>
</tr>
<tr>
	<td>인터파크분류선택</td>
	<td>
	<input class="lline" style="letter-spacing:-1px; width:450px;" readonly id="inpk_dispnm">
	<input type=hidden name=inpk_dispno value="<?=$_GET[inpk_dispno]?>">
	<a href="javascript:;" onclick="popupLayer('../interpark/popup.category.php?spot=inpk_dispno',650,500);"><img src= "../img/btn_interpark_catesearch.gif" align=absmiddle></a>
<? if ($_GET[inpk_dispno]){ ?>
	<script>getDispNm('<?=$_GET[inpk_dispno]?>','inpk_dispnm');</script>
<? } ?>
	</td>
</tr>
<tr>
	<td>검색어</td>
	<td>
	<select name=skey>
	<option value="goodsnm" <?=$selected[skey][goodsnm]?>>상품명
	<option value="a.goodsno" <?=$selected[skey]['a.goodsno']?>>고유번호
	<option value="goodscd" <?=$selected[skey][goodscd]?>>상품코드
	<option value="keyword" <?=$selected[skey][keyword]?>>유사검색어
	</select>
	<input type=text name=sword class=lline value="<?=$_GET[sword]?>">
	</td>
</tr>
<tr>
	<td>상품출력여부</td>
	<td class=noline>
	<input type=radio name=open value="" <?=$checked[open]['']?>>전체
	<input type=radio name=open value="11" <?=$checked[open][11]?>>출력상품
	<input type=radio name=open value="10" <?=$checked[open][10]?>>미출력상품
	</td>
</tr>
<tr>
	<td>인터파크등록여부</td>
	<td class=noline>
	<input type=radio name=register value="" <?=$checked[register]['']?>>전체
	<input type=radio name=register value="Y" <?=$checked[register][Y]?>>등록상품
	<input type=radio name=register value="N" <?=$checked[register][N]?>>미등록상품
	</td>
</tr>
</table>
<div class=button_top><input type=image src="../img/btn_search2.gif"></div>

</form>
<!-- 상품출력조건 : end -->

<form name="fmList" method="post" onsubmit="return chkFormList(this)">
<input type=hidden name=query value="<?=substr($pg->query,0,strpos($pg->query,"limit"))?>" required msgR="일괄전송 할 상품을 먼저 검색하세요.">

<div class="pageInfo ver8">총 <b><?=$total?></b>개, 검색 <b><?=$pg->recode[total]?></b>개, <b><?=$pg->page[now]?></b> of <?=$pg->page[total]?> Pages</div>

<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr><td class=rnd colspan=12></td></tr>
<tr class=rndbg>
	<th><a href="javascript:chkBox(document.getElementsByName('chk[]'),'rev')" class=white><font class=small1><b>선택</a></th>
	<th><font class=small1><b>번호</th>
	<th><font class=small1><b>상품명</th>
	<th><font class=small1><b>제조사</th>
	<th><font class=small1><b>등록일</th>
	<th><font class=small1><b>판매가</th>
	<th><font class=small1><b>적립금</th>
	<th><font class=small1><b>재고</th>
	<th><font class=small1><b>진열</th>
	<th><font class=small1><b>등록</th>
</tr>
<tr><td class=rnd colspan=12></td></tr>
<col width=35><col width=50><col><col width=150><col width=60><col width=80 span=2><col width=55><col width=40 span=2>
<?
while (is_resource($res) && $data=$db->fetch($res)){
	$stock = $data['totstock'];
?>
<tr><td height=4 colspan=12></td></tr>
<tr>
	<td align=center class="noline"><input type=checkbox name=chk[] value="<?=$data[goodsno]?>" onclick="iciSelect(this)"></td>
	<? if ($data[link]){ ?>
	<td align=center><font class="ver8" color="#616161"><?=$pg->idx--?></td>
	<td><a href="javascript:popup('../goods/popup.register.php?mode=modify&goodsno=<?=$data[goodsno]?>',825,600)"><font class=small1 color=0074BA><?=$data[goodsnm]?></a></td>
	<? } else { ?><td><!--<?=$pg->idx--?>--></td><td></td>
	<? } ?>
	<td align=center><?=$data[maker]?></td>
	<td align=center><font class="ver81" color="#444444"><?=substr($data[regdt],0,10)?></td>
	<td align=right style="padding-right:10px" nowrap><font class="ver8" color="#444444"><b><?=number_format($data[price])?></b></font></td>
	<td align=right style="padding-right:10px" nowrap><font class="ver8" color="#444444"><?=number_format($data[reserve])?></font></td>
	<td align=center><font class="ver81" color="#444444"><?=number_format($stock)?></td>
	<td align=center><img src="../img/icn_<?=$data[open]?>.gif"></td>
	<td align=center><img src="../img/icn_<?=($data[inpk_prdno] ? '1' : '0')?>.gif"></td>
</tr>
<tr>
	<td colspan=2></td>
	<td colspan=10>
	<? if ($data[inpk_dispno]){ ?>
	인터파크 분류 : <span id="dispnm<?=$pg->idx?>" style="letter-spacing:-1px;"></span>
	<script>getDispNm('<?=$data[inpk_dispno]?>','dispnm<?=$pg->idx?>');</script>
	<? } ?>
	</td>
</tr>
<tr><td height=4></td></tr>
<tr><td colspan=12 class=rndline></td></tr>
<? } ?>
</table>

<div align=center class=pageNavi><font class=ver8><?=$pg->page[navi]?></font></div>

<!-- 실행 : start -->
<div style="padding:20 0 5 5"><font class="def1" color="#EA0095"><b><font size="3">②</font> 위 상품리스트에 있는 상품을 인터파크로 전송합니다.</b></font></div>
<div class="noline" style="padding:0 0 5 5">
	<div style="float:left;">
	<input type="radio" name="isall" value="Y" <?=$checked[isall]['Y']?>>검색된 상품 전체<?=($pg->recode[total]?"({$pg->recode[total]}개)":"")?>를 전송합니다.<br>
	<input type="radio" name="isall" value="" <?=$checked[isall]['']?>>선택한 상품을 전송합니다.
	</div>
	<div style="padding-left:210px;"><input type=image src="../img/btn_interpark_transmit.gif" align=top></div>
</div>
<!-- 실행 : end -->

</form>

<div style="padding-top:30"></div>

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">인터파크 오픈스타일로 입점된 상점은 모든 상품을 인터파크로 전송해야 합니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">입점전에 등록한 상품이 많을 경우 하나씩 전송하려면 시간이 많이 소요됩니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">하나씩 등록하는 번거로움없이 일괄전송할 수 있도록 본 기능을 제공합니다.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>


<? include "../_footer.php"; ?>
