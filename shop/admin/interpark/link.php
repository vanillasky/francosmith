<?

$location = "인터파크 오픈스타일 입점 > 분류일괄매칭";
$scriptLoad = '<script src="../interpark/js/common.js"></script>';
include "../_header.php";
include "../../lib/page.class.php";

list ($total) = $db->fetch("select count(*) from ".GD_GOODS);

if ($_GET[isall] == '') $_GET[isall]='N';
$selected[skey][$_GET[skey]] = "selected";
$checked[open][$_GET[open]] = "checked";
$checked[isall][$_GET[isall]] = "checked";

if ($_GET[indicate] == 'search'){
	$orderby = "a.goodsno desc";

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
	if ($_GET[unlink] == 'Y') $where[] = "inpk_dispno=''";
	if ($_GET[inpk_dispno]) $where[] = "inpk_dispno = '$_GET[inpk_dispno]'";
	if ($_GET[sword]) $where[] = "$_GET[skey] like '%$_GET[sword]%'";
	if ($_GET[open]) $where[] = "open=".substr($_GET[open],-1);

	$pg = new Page($_GET[page]);
	$pg->field = $whereArr['distinct']." a.goodsno,a.goodsnm,a.open,a.regdt,a.maker,a.inpk_prdno,a.inpk_dispno,a.totstock,b.*";
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

function chkFormList(mode){
	var fObj = document.forms['fmList'];
	if (chkForm(fObj) === false) return;
	if (fObj['isall'][0].checked === false && isChked(document.getElementsByName('chk[]')) === false){
		if (document.getElementsByName('chk[]').length) document.getElementsByName('chk[]')[0].focus();
		return;
	}
	if (mode == 'link' && document.getElementsByName("sinpk_dispno")[0].value == ''){
		alert("선택한 상품에 연결 할 분류를 선택해주세요.");
		_ID("sinpk_dispnm").focus();
		return;
	}

	var msg = '';
	if (mode == 'link') msg += '선택한 상품에 해당 분류를 연결하시겠습니까?';
	else if (mode == 'unlink') msg += '선택한 상품의 분류를 해제하시겠습니까?';
	if (!confirm(msg)) return;

	fObj.target = "_self";
	fObj.mode.value = mode;
	fObj.action = "indb.php";
	fObj.submit();
}
--></script>

<div class="title title_top">분류일괄매칭<span>등록하신 상품을 편리하게 인터파크 분류(카테고리)와 연결 할 수 있습니다.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=marketing&no=22')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<div style="padding:8px 13px;background:#f7f7f7;border:3px solid #C6C6C6;margin-bottom:18px;" id="goodsInfoBox">
<div><font color="#EA0095"><b>필독! 분류일괄매칭이란?</b></font></div>
<div style="padding-top:2"><font  color=777777>인터파크와 입점계약을 맺은 이후 상점은 모든 상품들의 분류를 인터파크분류와 매칭시켜야만 합니다.</div>
<div style="padding-top:2">입점전에 내 상점에 등록한 상품수가  많다면 하나하나 인터파크로 분류연결하는데 시간이 많이 걸리게 됩니다.</div>
<div style="padding-top:2"><font color=0074BA>아래 기능은 인터파크 오픈스타일에 입점하기 전에 등록된 상품을 인터파크분류로 한꺼번에 일괄 연결하는 기능입니다.</font></div>
<div style="padding-top:2">물론, 상품리스트에서 한 상품씩 따로따로 분류연결을 해도 상관없습니다. 빠르게 분류연결을 하려면 아래 기능을 사용하세요.</div>
</div>



<!-- 상품출력조건 : start -->
<form name=frmList onsubmit="return chkForm(this)">
<input type="hidden" name="indicate" value="search">

<div style="padding:10 0 5 5"><font class="def1" color="#EA0095"><b><font size="3">①</font> 먼저 아래에서 인터파크 분류를 연결할 상품을 검색합니다.</b></font></div>
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>상점분류선택</td>
	<td>
	<script>new categoryBox('cate[]',4,'<?=$category?>');</script>
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
</table>
<div class=button_top><input type=image src="../img/btn_search2.gif"></div>

</form>
<!-- 상품출력조건 : end -->


<div style="padding: 3 0 10 12"><font color=EA0095><b>※</b></font> <font class=small1 color=EA0095>수정불가표시가 있는 상품은 이미 전송한 상품이므로 인터파크 분류(카테고리) 수정이 불가능합니다.</font></div>


<form name="fmList" method="post" onsubmit="return false">
<input type=hidden name=mode>
<input type=hidden name=query value="<?=substr($pg->query,0,strpos($pg->query,"limit"))?>" required msgR="일괄연결 할 상품을 먼저 검색하세요.">

<div class="pageInfo ver8">총 <b><?=$total?></b>개, 검색 <b><?=$pg->recode[total]?></b>개, <b><?=$pg->page[now]?></b> of <?=$pg->page[total]?> Pages</div>

<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr><td class=rnd colspan=12></td></tr>
<tr class=rndbg>
	<th><a href="javascript:chkBox(document.getElementsByName('chk[]'),'rev')" class=white><font class=small1><b>선택</a></th>
	<th><font class=small1><b>번호</th>
	<th><font class=small1><b>상품명</th>
	<!--<th><font class=small1><b>제조사</th>-->
	<th><font class=small1><b>등록일</th>
	<th><font class=small1><b>판매가</th>
	<th><font class=small1><b>적립금</th>
	<th><font class=small1><b>재고</th>
	<th><font class=small1><b>진열</th>
</tr>
<tr><td class=rnd colspan=12></td></tr>
<col width=35><col width=50><col><col width=150><col width=60><col width=80 span=2><col width=55 span=2>
<?
while (is_resource($res) && $data=$db->fetch($res)){
	$stock = $data[totstock];
?>
<tr><td height=4 colspan=12></td></tr>
<tr>
	<? if ($data[inpk_prdno]){ ?>
	<td align=center class="noline" valign=middle><font class=small1 color=red>수정<div>불가</div></font></td>
	<? } else { ?>
	<td align=center class="noline"><input type=checkbox name=chk[] value="<?=$data[goodsno]?>" onclick="iciSelect(this)"></td>
	<? } ?>
	<? if ($data[link]){ ?>
	<td align=center><font class="ver8" color="#616161"><?=$pg->idx--?></td>
	<td><a href="javascript:popup('../goods/popup.register.php?mode=modify&goodsno=<?=$data[goodsno]?>',825,600)"><font class=small1 color=0074BA><?=$data[goodsnm]?></a></td>
	<? } else { ?><td><!--<?=$pg->idx--?>--></td><td></td>
	<? } ?>
	<!--<td align=center><font class=small1 color=666666><?=$data[maker]?></font></td>-->
	<td align=center><font class="ver81" color="#444444"><?=substr($data[regdt],0,10)?></td>
	<td align=center style="padding-right:10px" nowrap><font class="ver81" color="#444444"><b><?=number_format($data[price])?></b></font></td>
	<td align=right style="padding-right:10px" nowrap><font class="ver81" color="#444444"><?=number_format($data[reserve])?></font></td>
	<td align=center><font class="ver81" color="#444444"><?=number_format($stock)?></td>
	<td align=center><img src="../img/icn_<?=$data[open]?>.gif"></td>
</tr>
<tr>
	<td colspan=2></td>
	<td colspan=10>
	<? if ($data[inpk_dispno]){ ?>
	<font class=small1 color="#EA0095">인터파크 분류 : <span id="dispnm<?=$pg->idx?>" style="letter-spacing:-1px;"></span></font>
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
<div style="padding:20 0 5 5"><font class="def1" color="#EA0095"><b><font size="3">②</font> 위 상품리스트에 있는 상품을, 아래 인터파크 분류와 연결합니다.</b></font></div>
<div class="noline" style="padding:0 0 5 5">
	<input type="radio" name="isall" value="Y" <?=$checked[isall]['Y']?>>검색된 상품 전체<?=($pg->recode[total]?"({$pg->recode[total]}개)":"")?>를 연결(또는 해제)합니다. <span class=small1>(단, 인터파크에 전송된 상품은 수정이 불가능합니다.)</span><br>
	<input type="radio" name="isall" value="N" <?=$checked[isall]['N']?>>선택한 상품을 연결(또는 해제)합니다.
</div>
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>인터파크 분류연결</td>
	<td>
	<div style="margin:5px 0">
	선택한 상품을
	<input class="lline" style="letter-spacing:-1px; width:450px; text-align:center" readonly id="sinpk_dispnm">
	<input type=hidden name=sinpk_dispno value="<?=$_GET[sinpk_dispno]?>">
	<a href="javascript:popupLayer('../interpark/popup.category.php?spot=sinpk_dispno',650,500);"><img src= "../img/btn_interpark_catesearch.gif" align=absmiddle></a>
	으로
	<a href="javascript:chkFormList('link')"><img src="../img/btn_cate_connect.gif" align="absmiddle" alt="연결"></a>
<? if ($_GET[sinpk_dispno]){ ?>
	<script>getDispNm('<?=$_GET[sinpk_dispno]?>','sinpk_dispnm');</script>
<? } ?>
	</div>
	</td>
</tr>
<tr height=35>
	<td>인터파크 분류해제</td>
	<td>선택한 상품의 인터파크 분류(카테고리)를 <a href="javascript:chkFormList('unlink')"><img src="../img/btn_cate_unconnect.gif" align="absmiddle" alt="해제"></a> <font class=small1 color=555555>(신중하게 진행하세요. 버튼클릭시 상품에 연결된 인터파크 분류가 해제됩니다)</td>
</tr>
</table>
<!-- 실행 : end -->

</form>

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">인터파크 분류연결 : 상품에 인터파크 분류(카테고리)를 연결하는 기능입니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">인터파크 분류해제 : 현재 연결된 인터파크 분류를 해제하는 기능입니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"><b>수정불가표시가 있는 상품은 이미 전송한 상품이므로 인터파크 분류(카테고리) 수정이 불가능합니다.</b></td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>


<? include "../_footer.php"; ?>
