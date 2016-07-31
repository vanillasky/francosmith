<?
// deprecated. redirect to new page;
header('location: ./adm_goods_manage_link.php?'.$_SERVER['QUERY_STRING']);
exit;
$location = "상품관리 > 빠른 이동/복사/삭제";
include "../_header.php";
include "../../lib/page.class.php";

list ($total) = $db->fetch("select count(*) from ".GD_GOODS."");

$selected[skey][$_GET[skey]] = "selected";
$selected[brandno][$_GET[brandno]] = "selected";
$selected[sbrandno][$_GET[sbrandno]] = "selected";
$checked[open][$_GET[open]] = "checked";
$checked[isToday][$_GET[isToday]] = "checked";

if ($_GET[sCate]){
	$sCategory = array_notnull($_GET[sCate]);
	$sCategory = $sCategory[count($sCategory)-1];
}

if ($_GET[indicate] == 'search'){
	$orderby = "a.goodsno desc";

	if ($_GET[cate]){
		$category = array_notnull($_GET[cate]);
		$category = $category[count($category)-1];
	}

	$db_table = "
	".GD_GOODS." a
	left join ".GD_GOODS_OPTION." b on a.goodsno=b.goodsno and link
	";

	if ($category || $_GET[unlink] == 'Y'){
		$db_table .= "left join ".GD_GOODS_LINK." c on a.goodsno=c.goodsno";
		$where[] = ($_GET[unlink] == 'Y') ? "ISNULL(c.goodsno)" : "category like '$category'";
	}
	if ($_GET[sword]) $where[] = "$_GET[skey] like '%$_GET[sword]%'";
	if ($_GET['brandno']) $where[] = "brandno='{$_GET['brandno']}'";
	if ($_GET['unbrand'] == 'Y') $where[] = "brandno='0'";
	if ($_GET[open]) $where[] = "open=".substr($_GET[open],-1);

	$pg = new Page($_GET[page]);
	$pg->field = "
		distinct a.goodsno,a.goodsnm,a.open,a.regdt,a.brandno,a.inpk_prdno,a.totstock,a.img_s,
		b.link, b.reserve, b.price
	";
	$pg->setQuery($db_table,$where,$orderby);
	$pg->exec();

	$res = $db->query($pg->query);
}

// 브랜드
$brands = array();
$bRes = $db->query("select * from gd_goods_brand order by sort");
while ($tmp=$db->fetch($bRes)) $brands[$tmp['sno']] = $tmp['brandnm'];

?>

<script><!--
function iciSelect(obj)
{
	var row = obj.parentNode.parentNode;
	row.style.background = (obj.checked) ? "#F0F4FF" :"#FFFFFF";
}

function chkFormList(mode){
	var fObj = document.forms['fmList'];
	if (inArray(mode, new Array('move','copyGoodses','unlink')) && fObj.category.value == ''){
		if (mode == 'move') alert("분류이동는 분류로 검색했을 경우만 가능합니다.");
		else if (mode == 'copyGoodses') alert("상품복사는 분류로 검색했을 경우만 가능합니다.");
		else if (mode == 'unlink') alert("연결해제는 분류로 검색했을 경우만 가능합니다.");
		document.getElementsByName("cate[]")[0].focus();
		return;
	}
	if (isChked(document.getElementsByName('chk[]')) === false){
		if (document.getElementsByName('chk[]').length) document.getElementsByName('chk[]')[0].focus();
		return;
	}
	if (mode == 'delGoodses'){
		tobj = document.getElementsByName('chk[]');
		for(i=0; i< tobj.length; i++){
			if (tobj[i].checked === true && tobj[i].getAttribute('notDel') == 'notInpk'){
				alert("인터파크에 등록된 상품은 삭제할 수 없습니다.");
				tobj[i].focus();
				return;
			}
		}
	}
	if (inArray(mode, new Array('link','move','copyGoodses')) && document.getElementsByName("sCate[]")[0].value == ''){
		if (mode == 'link') alert("선택한 상품에 연결 할 분류를 선택해주세요.");
		else if (mode == 'move') alert("선택한 상품을 이동 할 분류를 선택해주세요.");
		else if (mode == 'copyGoodses') alert("선택한 상품을 복사 할 분류를 선택해주세요.");
		document.getElementsByName("sCate[]")[0].focus();
		return;
	}
	else if (mode == 'linkBrand' && document.getElementsByName("sbrandno")[0].value == ''){
		alert("선택한 상품에 연결 할 브랜드를 선택해주세요.");
		document.getElementsByName("sbrandno")[0].focus();
		return;
	}

	var msg = '';
	if (mode == 'link') msg += '선택한 상품에 해당 분류를 연결하시겠습니까?';
	else if (mode == 'move') msg += '선택한 상품을 해당 분류로 이동하시겠습니까?';
	else if (mode == 'copyGoodses') msg += '선택한 상품을 해당 분류로 복사하시겠습니까?';
	else if (mode == 'unlink') msg += '선택한 상품의 분류를 해제하시겠습니까?';
	else if (mode == 'delGoodses') msg += '선택한 상품을 정말 삭제하시겠습니까?' + "\n\n" + '[주의] 삭제 후에는 복원이 안되므로 신중하게 삭제하시기 바랍니다.';
	else if (mode == 'linkBrand') msg += '선택한 상품에 해당 브랜드를 연결하시겠습니까?';
	else if (mode == 'unlinkBrand') msg += '선택한 상품의 브랜드를 해제하시겠습니까?';
	if (!confirm(msg)) return;

	fObj.target = "_self";
	fObj.mode.value = mode;
	fObj.action = "indb.php";
	fObj.submit();
}
--></script>

<div class="title title_top">빠른 이동/복사/삭제<span>등록하신 상품을 편리하게 이동/복사/삭제 할 수 있습니다.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=15')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<!-- 상품출력조건 : start -->
<form name=frmList onsubmit="return chkForm(this)">
<input type="hidden" name="indicate" value="search">

<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>분류선택</td>
	<td>
	<script>new categoryBox('cate[]',4,'<?=$category?>');</script>
	&nbsp;&nbsp;&nbsp;<a href="?indicate=search&unlink=Y"><img src="../img/btn_without_cate.gif" alt="미연결상품보기" align=absmiddle></a>
	</td>
</tr>
<tr>
	<td>브랜드</td>
	<td>
	<select name="brandno">
	<option value="">-- 브랜드 선택 --
	<? foreach($brands as $sno => $brandnm){ ?>
	<option value="<?=$sno?>" <?=$selected['brandno'][$sno]?>><?=$brandnm?></option>
	<? } ?>
	</select>
	&nbsp;&nbsp;&nbsp;<a href="?indicate=search&unbrand=Y"><img src="../img/btn_without_brand.gif" alt="미연결상품보기" align=absmiddle></a>
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
	<input type=text name=sword class=lline value="<?=$_GET[sword]?>" class="line">
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

<form name="fmList" method="post" onsubmit="return false">
<input type=hidden name=mode>
<input type=hidden name=category value="<?=$category?>">

<div class="pageInfo ver8" style="margin-top:20px;">총 <b><?=$total?></b>개, 검색 <b><?=$pg->recode[total]?></b>개, <b><?=$pg->page[now]?></b> of <?=$pg->page[total]?> Pages</div>

<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr><td class=rnd colspan=12></td></tr>
<tr class=rndbg>
	<th><a href="javascript:chkBox(document.getElementsByName('chk[]'),'rev')" class=white><font class=small1><b>선택</a></th>
	<th><font class=small1><b>번호</th>
	<th colspan="2"><font class=small1><b>상품명</th>
	<th><font class=small1><b>브랜드</th>
	<th><font class=small1><b>등록일</th>
	<th><font class=small1><b>판매가</th>
	<th><font class=small1><b>적립금</th>
	<th><font class=small1><b>재고</th>
	<th><font class=small1><b>진열</th>
</tr>
<tr><td class=rnd colspan=12></td></tr>
<col width=35><col width=50><col span=2><col width=150><col width=60><col width=80 span=2><col width=55 span=2>
<?
while (is_resource($res) && $data=$db->fetch($res)){
	$stock = $data['totstock'];
	$notDel = ($data['inpk_prdno'] && $inpkOSCfg['use'] == 'Y' ? 'notInpk' : '');
?>
<tr><td height=4 colspan=12></td></tr>
<tr>
	<td align=center class="noline"><input type=checkbox name=chk[] value="<?=$data[goodsno]?>" onclick="iciSelect(this)" notDel="<?=$notDel?>"></td>
	<td align=center><font class="ver8" color="#616161"><?=$pg->idx--?></td>
	<td>
		<a href="../../goods/goods_view.php?goodsno=<?=$data[goodsno]?>" target=_blank><?=goodsimg($data[img_s],40,'',1)?></a>
	</td>
	<td><a href="javascript:popup('popup.register.php?mode=modify&goodsno=<?=$data[goodsno]?>',825,600)"><font class=small1 color=0074BA><?=$data[goodsnm]?></a></td>
	<td align=center><?=$brands[$data['brandno']]?></td>
	<td align=center><font class="ver81" color="#444444"><?=substr($data[regdt],0,10)?></td>
	<td align=right style="padding-right:10px" nowrap><font class="ver8" color="#444444"><b><?=number_format($data[price])?></b></font></td>
	<td align=right style="padding-right:10px" nowrap><font class="ver8" color="#444444"><?=number_format($data[reserve])?></font></td>
	<td align=center><font class="ver81" color="#444444"><?=number_format($stock)?></td>
	<td align=center><img src="../img/icn_<?=$data[open]?>.gif"></td>
</tr>
<tr><td height=4></td></tr>
<tr><td colspan=12 class=rndline></td></tr>
<? } ?>
</table>

<div align=center class=pageNavi><font class=ver8><?=$pg->page[navi]?></font></div>

<table class=tb style="margin:30px 0;">
<col class=cellC><col class=cellL>
<tr>
	<td>연결/이동/복사</td>
	<td>
	<div style="margin:5px 0">
	선택한 상품을 <script>new categoryBox('sCate[]',4,'<?=$sCategory?>','','fmList');</script> 으로
	<a href="javascript:chkFormList('link')"><img src="../img/btn_cate_connect.gif" align="absmiddle" alt="연결"></a>
	<a href="javascript:chkFormList('move')"><img src="../img/btn_cate_move.gif" align="absmiddle" alt="이동"></a>
	<a href="javascript:chkFormList('copyGoodses')"><img src="../img/btn_cate_copy.gif" align="absmiddle" alt="복사"></a>
	</div>
	<div style="margin:5px 0" class="noline">
	<input type="checkbox" name="isToday" value="Y" <?=$checked[isToday]['Y']?>>해당 상품의 등록일을 현재 등록시간으로 변경합니다. <font class=extext>(복사의 경우에는 무조건 현재시간으로 변경됩니다)
	</div>
	</td>
</tr>
<tr height=35>
	<td>분류해제</td>
	<td>선택한 상품의 분류(카테고리)를 <a href="javascript:chkFormList('unlink')"><img src="../img/btn_cate_unconnect.gif" align="absmiddle" alt="해제"></a> <font class=extext>(신중하게 진행하세요. 버튼클릭시 상품에 연결된 분류(카테고리)가 해제됩니다)</td>
</tr>
<tr>
	<td>브랜드연결</td>
	<td>
	<div style="margin:5px 0">
	선택한 상품을
	<select name="sbrandno">
	<option value="">-- 브랜드 선택 --
	<? foreach($brands as $sno => $brandnm){ ?>
	<option value="<?=$sno?>" <?=$selected['sbrandno'][$sno]?>><?=$brandnm?></option>
	<? } ?>
	</select> 으로
	<a href="javascript:chkFormList('linkBrand')"><img src="../img/btn_cate_connect.gif" align="absmiddle" alt="연결"></a>
	<a href="javascript:chkFormList('unlinkBrand')"><img src="../img/btn_cate_unconnect.gif" align="absmiddle" alt="해제"></a>
	</div>
	</td>
</tr>
<tr height=35>
	<td>상품삭제</td>
	<td>선택한 상품을 <a href="javascript:chkFormList('delGoodses')"><img src="../img/btn_cate_del.gif" align="absmiddle" alt="삭제"></a> <font class=extext>(신중하게 진행하세요. 버튼클릭시 선택한 상품들이 삭제됩니다. 삭제되면 복구되지 않습니다)</td>
</tr>
</table>

</form>

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">분류연결 : 상품에 분류(카테고리)를 연결하는 기능입니다.(다중분류기능지원)</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">분류이동 : 현재 연결된 분류에서 다른 분류로 이동하는 기능입니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">분류해제 : 현재 연결된 분류를 해제하는 기능입니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">상품복사 : 다른 분류로 똑같은 상품을 하나 더 복사(생성)하는 기능입니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">상품삭제 : 상품을 삭제하는 기능으로 삭제 후에는 복원이 안되므로 신중하게 삭제하시기 바랍니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">[주의] 위 상품검색시 상품에 연결된 하위분류까지 정확하게 선택한 후 검색하세요.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">[주의] 상품복사 경우 상품문의/상품후기는 복사되지 않습니다.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>


<? include "../_footer.php"; ?>
