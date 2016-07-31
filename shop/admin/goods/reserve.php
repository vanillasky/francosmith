<?
// deprecated. redirect to new page;
header('location: ./adm_goods_manage_mileage.php?'.$_SERVER['QUERY_STRING']);
exit;
$location = "상품관리 > 빠른 적립금수정";
include "../_header.php";
include "../../lib/page.class.php";
@include "../../conf/design_main.$cfg[tplSkin].php";

### 공백 제거
$_GET[sword] = trim($_GET[sword]);

list ($total) = $db->fetch("select count(*) from ".GD_GOODS." a	left join ".GD_GOODS_OPTION." b on a.goodsno=b.goodsno");

$selected[skey][$_GET[skey]] = "selected";
$selected[smain][$_GET[smain]] = "selected";
$selected[sevent][$_GET[sevent]] = "selected";
$selected[percent][$_GET[percent]] = "selected";
$selected[roundunit][$_GET[roundunit]] = "selected";
$selected[roundtype][$_GET[roundtype]] = "selected";
$checked[open][$_GET[open]] = "checked";
$checked[indicate][$_GET[indicate]] = "checked";
$checked[method][$_GET[method]] = "checked";
$checked[isall][$_GET[isall]] = "checked";

if ($_GET[indicate] == 'search'){
	$orderby = "a.goodsno desc";

	if ($_GET[cate]){
		$category = array_notnull($_GET[cate]);
		$category = $category[count($category)-1];
	}

	$db_table = "
	".GD_GOODS." a
	left join ".GD_GOODS_OPTION." b on a.goodsno=b.goodsno
	";

	if ($category){
		$db_table .= "left join ".GD_GOODS_LINK." c on a.goodsno=c.goodsno";
		$where[] = "category like '$category%'";
	}
	if ($_GET[sword]) $where[] = "$_GET[skey] like '%$_GET[sword]%'";
	if ($_GET[open]) $where[] = "open=".substr($_GET[open],-1);
}
else if ($_GET[indicate] == 'main'){
	$orderby = "c.sort, b.sno";

	$db_table = "
	".GD_GOODS." a
	left join ".GD_GOODS_OPTION." b on a.goodsno=b.goodsno
	left join ".GD_GOODS_DISPLAY." c on b.goodsno=c.goodsno
	";

	if ($_GET[smain] != '') $where[] = "c.mode = '{$_GET[smain]}'";
}
else if ($_GET[indicate] == 'event'){
	$orderby = "c.sort, b.sno";

	$db_table = "
	".GD_GOODS." a
	left join ".GD_GOODS_OPTION." b on a.goodsno=b.goodsno
	left join ".GD_GOODS_DISPLAY." c on b.goodsno=c.goodsno
	";

	if ($_GET[sevent] != '') $where[] = "c.mode = 'e{$_GET[sevent]}'";
}

if (in_array($_GET[indicate], array('search', 'main', 'event')) === true){
	$pg = new Page($_GET[page]);
	$pg->field = "a.goodsno,a.goodsnm,a.open,a.img_s,a.use_emoney,b.*";
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
	if (fObj['method'][0].checked === true){
		if (chkText(fObj['reserve'],fObj['reserve'].value,'') === false) return false;
		if (chkPatten(fObj['reserve'],'regNum') === false) return false;
	}
	if (fObj['isall'].checked === false && isChked(document.getElementsByName('chk[]')) === false){
		if (document.getElementsByName('chk[]').length) document.getElementsByName('chk[]')[0].focus();
		return false;
	}

	var msg = '';
	if (fObj['method'][0].checked === true){
		msg += '적립금을 일괄 ' + fObj['reserve'].value + '원으로 수정하시겠습니까?';
	}
	else {
		msg += '적립금을 판매가의 ' + fObj['percent'].value;
		msg += '%를 ' + fObj['roundunit'].value;
		msg += '원 단위로 ' + fObj['roundtype'].options[fObj['roundtype'].selectedIndex].text;
		msg += '하여 일괄적으로 수정하시겠습니까?';
	}
	msg += "\n\n" + '[주의] 일괄적용 후에는 이전상태로 복원이 안되므로 신중하게 변경하시기 바랍니다.';
	if (!confirm(msg)) return false;

	fObj.target = "_self";
	fObj.mode.value = "reserve";
	fObj.action = "indb.php";
	return true;
}

/*** 이벤트목록 요청 ***/
function getEventList(sobj, selValue)
{
	if (sobj.options[sobj.selectedIndex].getAttribute("call") == null) return;
	function setcallopt(idx, text, value, defaultSelected, selected, call){
		if (idx == 0) for (i = sobj.options.length; i > 0; i--) sobj.remove(i);
		sobj.options[idx] = new Option(text, value, defaultSelected, selected);
		if (call != null) sobj.options[idx].setAttribute('call', call);
	}
	var ajax = new Ajax.Request( "../goods/indb.php",
	{
		method: "post",
		parameters: "mode=getEvent&page=" + sobj.options[sobj.selectedIndex].getAttribute("call") + "&selValue=" + (selValue != null ? selValue : ''),
		onLoading: function (){ setcallopt(0, '== 로 딩 중 ... ==', ''); },
		onComplete: function ()
		{
			var req = ajax.transport;
			if ( req.status == 200 )
			{
				var jsonData = eval( '(' + req.responseText + ')' );
				var lists = jsonData.lists;
				var page = jsonData.page;
				var idx = 0;
				if (page.prev != null) setcallopt(idx++, '☞ 처음목록보기', '', false, false, '1');
				if (page.prev != null) setcallopt(idx++, '☞ 이전목록보기', '', false, false, page.prev);
				if (lists.length == 0) setcallopt(idx++, '== 이벤트가 없습니다 ==', '', false, false);
				for (i = 0; i < lists.length; i++){
					if (i == 0 || (selValue != null && selValue == lists[i].sno)) selected = true; else selected = false;
					setcallopt(idx++, '[' + lists[i].sdate + ' ~ ' + lists[i].edate + '] ' + lists[i].subject, lists[i].sno, false, selected);
				}
				if (page.next != null) setcallopt(idx++, '☞ 다음목록보기', '', false, false, page.next);
				sobj.form['seventpage'].value = page.now;
			}
			else {
				setcallopt(0, '☞ 로딩 시작하기', '', false, false, '1');
				setcallopt(1, '[로딩실패] 재로딩하세요.', '', true, true);
			}
		}
	} );
}
--></script>

<div class="title title_top">빠른 적립금수정<span>상품적립금을 빠르게 일괄수정할 수 있습니다.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=14')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<!-- 상품출력조건 : start -->
<form name=frmList onsubmit="return chkForm(this)">

<div style="padding:10 0 5 5"><font class="def1" color="#000000"><b><font size="3">①</font> 먼저 아래에서 적립금수정할 상품을 검색합니다. <font class=extext>(아래 3가지 방식중 한가지를 선택하여 검색)</font></b></font></div>
<div class="noline" style="padding:0 0 5 20"><input type="radio" name="indicate" value="search" <?=$checked[indicate]['search']?> required label="상품검색조건"><b>전체상품 검색</b> <font class="extext">(전체상품을 대상으로 검색합니다. 특정 분류의 상품을 모두 보려면 분류 선택후 상품명을 공란으로 두고 검색)</font></div>
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td><font class=small1>분류선택</td>
	<td><script>new categoryBox('cate[]',4,'<?=$category?>');</script></td>
</tr>
<tr>
	<td><font class=small1>검색어</td>
	<td>
	<select name=skey>
	<option value="goodsnm" <?=$selected[skey][goodsnm]?>>상품명
	<option value="a.goodsno" <?=$selected[skey][a.goodsno]?>>고유번호
	<option value="goodscd" <?=$selected[skey][goodscd]?>>상품코드
	<option value="keyword" <?=$selected[skey][keyword]?>>유사검색어
	</select>
	<input type=text name=sword class=lline value="<?=$_GET[sword]?>" class="line">
	</td>
</tr>
<tr>
	<td><font class=small1>상품출력여부</td>
	<td class="noline"><font class=small1 color=555555>
	<input type=radio name=open value="" <?=$checked[open]['']?>>전체
	<input type=radio name=open value="11" <?=$checked[open][11]?>>출력상품
	<input type=radio name=open value="10" <?=$checked[open][10]?>>미출력상품
	</td>
</tr>
</table>

<div class="noline" style="padding:15 0 5 20"><input type="radio" name="indicate" value="main" <?=$checked[indicate]['main']?> required label="상품검색조건"><b>메인상품</b> <font class="extext">(메인페이지에 진열된 모든 상품을 출력합니다)</font></div>
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td><font class=small1>상품진열영역</td>
	<td>
	<select name=smain>
<? for ($i=0;$i<5;$i++){ ?>
	<option value="<?=$i?>" <?=$selected[smain][$i]?>><?=$cfg_step[$i][title]?>
<? } ?>
	</select>
	</td>
</tr>
</table>

<div class="noline" style="padding:15 0 5 20"><input type="radio" name="indicate" value="event" <?=$checked[indicate]['event']?> required label="상품검색조건"><b>이벤트상품</b> <font class="extext">(이벤트상품으로 선정해놓은 상품들을 출력합니다)</font></div>
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td><font class=small1>이벤트</td>
	<td>
	<select name=sevent onchange="getEventList(this)">
	<option value="" call="<?=$_GET[seventpage]?>">☞ 로딩 시작하기</option>
	</select>
	<input type="hidden" name="seventpage">
	<script>window.onload = function (){ getEventList(frmList.sevent, '<?=$_GET[sevent]?>'); }</script>
	</td>
</tr>
</table>

<div class=button_top><input type=image src="../img/btn_search2.gif"></div>

</form>
<!-- 상품출력조건 : end -->

<form name="fmList" method="post" onsubmit="return chkFormList(this)">
<input type=hidden name=mode>
<input type=hidden name=query value="<?=substr($pg->query,0,strpos($pg->query,"limit"))?>" required msgR="일괄관리 할 상품을 먼저 검색하세요.">

<div class="pageInfo ver8">총 <b><?=$total?></b>개, 검색 <b><?=$pg->recode[total]?></b>개, <b><?=$pg->page[now]?></b> of <?=$pg->page[total]?> Pages</div>

<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr><td class=rnd colspan=12></td></tr>
<tr class=rndbg>
	<th width=60><a href="javascript:chkBox(document.getElementsByName('chk[]'),'rev')" class=white><font class=small1><b>전체선택</a></th>
	<th><font class=small1><b>번호</th>
	<th colspan="2"><font class=small1><b>상품명</th>
	<th><font class=small1><b>옵션1</th>
	<th><font class=small1><b>옵션2</th>
	<th><font class=small1><b>정가</th>
	<th><font class=small1><b>판매가</th>
	<th><font class=small1><b>매입가</th>
	<th><font class=small1><b>적립금</th>
	<th><font class=small1><b>재고</th>
	<th><font class=small1><b>진열</th>
</tr>
<tr><td class=rnd colspan=12></td></tr>
<col width=35><col width=50><col span="2"><col width=70 span=5><col width=60><col width=35 span=2>
<?
while (is_resource($res) && $data=$db->fetch($res)){

	if ($data['use_emoney'] == 0) {
		$disabled = 'disabled';
		$reserve = '기본정책';
	}
	else {
		$disabled = '';
		$reserve = number_format($data[reserve]);
	}
?>
<tr><td height=4 colspan=12></td></tr>
<tr>
	<td align=center class="noline"><input type=checkbox name=chk[] value="<?=$data[sno]?>" onclick="iciSelect(this)" <?=$disabled?>></td>
	<? if ($data[link]){ ?>
	<td align=center><font class="ver8" color="#616161"><?=$pg->idx--?></td>
	<td>
		<a href="../../goods/goods_view.php?goodsno=<?=$data[goodsno]?>" target=_blank><?=goodsimg($data[img_s],40,'',1)?></a>
	</td>
	<td><a href="javascript:popup('popup.register.php?mode=modify&goodsno=<?=$data[goodsno]?>',825,600)"><font class=small1 color=0074BA><?=$data[goodsnm]?></a></td>
	<? } else { ?><td><!--<?=$pg->idx--?>--></td><td></td><td></td>
	<? } ?>
	<td align=center><font class=small color=555555><?=$data[opt1]?></td>
	<td align=center><font class=small color=555555><?=$data[opt2]?></td>
	<td align=right style="padding-right:10px" nowrap><font class="ver8" color="#444444"><?=number_format($data[consumer])?></font></td>
	<td align=right style="padding-right:10px" nowrap><font class="ver8" color="#444444"><?=number_format($data[price])?></font></td>
	<td align=right style="padding-right:10px" nowrap><font class="ver8" color="#444444"><?=number_format($data[supply])?></font></td>
	<td align=right style="padding-right:10px" nowrap><font class="ver8" color="#0074BA"><b><?=$reserve?></b></font></td>
	<td align=center><font class="ver81" color="#444444"><?=number_format($data[stock])?></td>
	<td align=center><img src="../img/icn_<?=$data[open]?>.gif"></td>
</tr>
<tr><td height=4></td></tr>
<tr><td colspan=12 class=rndline></td></tr>
<? } ?>
</table>

<div align=center class=pageNavi><font class=ver8><?=$pg->page[navi]?></font></div>

<div style="padding:20 0 5 5"><font class="def1" color="#000000"><b><font size="3">②</font> 위 상품리스트에 있는 상품의 적립금을, 아래 조건을 적용하여 일괄수정합니다. <font class=extext>(신중하게 설정하고 수정하세요)</b></font></div>
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>적립금조건설정</td>
	<td>
	<div style="margin:5px 0">
	<span class="noline"><input type="radio" name="method" value="direct" <?=$checked[method]['direct']?> required label="일괄적용방법"></span>적립금을 일괄
	<input type="text" name="reserve" value="<?=$_GET[reserve]?>" size="6" class="rline" label="적립금"> 원으로 수정합니다.
	</div>
	<div style="margin:5px 0">
	<span class="noline"><input type="radio" name="method" value="price" <?=$checked[method]['price']?> required label="일괄적용방법"></span>적립금을 판매가의
	<select name="percent">
	<?
	$idx = 0;
	while (($idx += ($idx <= 0.9 ? 0.1 : 1)) <= 100) echo "<option value=\"{$idx}\" " . $selected[percent]["{$idx}"] . ">{$idx}</option>";
	?>
	</select>%를
	<select name="roundunit">
	<option value="1" <?=$selected[roundunit][1]?>>1</option>
	<option value="10" <?=$selected[roundunit][10]?>>10</option>
	<option value="100" <?=$selected[roundunit][100]?>>100</option>
	<option value="1000" <?=$selected[roundunit][1000]?>>1000</option>
	</select>
	원 단위로
	<select name="roundtype">
	<option value="down" <?=$selected[roundtype][down]?>>내림</option>
	<option value="halfup" <?=$selected[roundtype][halfup]?>>반올림</option>
	<option value="up" <?=$selected[roundtype][up]?>>올림</option>
	</select>
	하여 수정합니다.
	</div>
	<div style="margin:5px 0" class="noline">
	<input type="checkbox" name="isall" value="Y" <?=$checked[isall]['Y']?>>검색된 상품 전체<?=($pg->recode[total]?"({$pg->recode[total]}개)":"")?>를 수정합니다. <font class=extext>(상품수가 많은 경우 비권장합니다. 가능하면 한 페이지씩 선택하여 수정하세요)</div></td>
</tr>
</table>

<div class=button_top><input type=image src="../img/btn_save.gif"></div>

</form>

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">일괄관리 할 상품을 검색 후 상품적립금을 일괄처리 조건에 맞춰 적용합니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">[주의1] 일괄적용 후에는 <b>이전상태로 복원이 안되므로 신중하게 변경하시기 바랍니다.</b></td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">[주의2] 서버 부하등 안정적인 서비스를 위해서 검색결과가 많은 경우에는 검색결과 전체수정은 피하시기 바랍니다.</td></tr>
<tr><td style="padding-top:4"><img src="../img/icon_list.gif" align="absmiddle"><b>[적립금수정 예제]</b></td></tr>
<tr><td style="padding-left:10">판매가의 5.5% 할인된 가격으로 적립금을 일괄적으로 수정하고, 가격 단위는 100원 단위로 내림하여 수정한다면,</td></tr>
<tr><td style="padding-left:10">판매가 10,000원인 상품의 계산식은 다음과 같습니다.</td></tr>
<tr><td style="padding-left:10">⇒ 10,000 × (5.5 / 100) = 550원이며,</td></tr>
<tr><td style="padding-left:10">⇒ 100원 단위 내림하면 500원 으로 최종 적립금수정이 됩니다.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>


<? include "../_footer.php"; ?>
