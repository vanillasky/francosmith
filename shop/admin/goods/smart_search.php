<?
$location = "상품관리 > SMART 검색 테마관리";
include "../_header.php";
include "../../lib/page.class.php";
include "../../conf/config.php";

if(get_magic_quotes_gpc()) {
	stripslashes_all($_POST);
	stripslashes_all($_GET);
}

$sort = ($_GET['sort']) ? $_GET['sort'] : "themenm ASC";
$qstr /* querystring*/ = "skey=".$_GET['skey']."&sword=".$_GET['sword']."&cate[]=".$_GET['cate'][0]."&cate[]=".$_GET['cate'][1]."&cate[]=".$_GET['cate'][2]."&cate[]=".$_GET['cate'][3]."&regdt[]=".$_GET['regdt'][0]."&regdt[]=".$_GET['regdt'][1]."&page=".$_GET['page']."&sort=".$_GET['sort'];

### 공백 제거
$_GET['sword'] = trim($_GET['sword']);
if(!$cfg['smartSearch_useyn']) $cfg['smartSearch_useyn'] = "n";
$checked['smartSearch_useyn'][$cfg['smartSearch_useyn']] = " checked";

list($total) = $db->fetch("SELECT COUNT(*) FROM ".GD_GOODS_SMART_SEARCH);

if(!$_GET['page_num']) $_GET['page_num'] = 20;
$selected['skey'][$_GET['skey']] = "selected";

if ($_GET['cate']){
	$category = array_notnull($_GET['cate']);
	$category = $category[count($category)-1];
}

$db_table = GD_GOODS_SMART_SEARCH." AS a LEFT JOIN ".GD_CATEGORY." AS b ON b.themeno = a.sno";

if ($category){
	$where[] = sprintf(" b.category like '%s%%'", $category);
}
if ($_GET['sword']) {
	if($_GET['skey'] == 'all') {
		$where[] = " (themenm LIKE '%".$_GET['sword']."%' OR catnm LIKE '%".$_GET['sword']."%') ";
	}
	else {
		$where[] = $_GET['skey']." LIKE '%".$_GET['sword']."%'";
	}
}
if ($_GET['regdt'][0] && $_GET['regdt'][1]) $where[] = "regdt BETWEEN DATE_FORMAT(".$_GET['regdt'][0].", '%Y-%m-%d 00:00:00') AND DATE_FORMAT(".$_GET['regdt'][1].", '%Y-%m-%d 23:59:59')";

$pg = new Page($_GET['page'],$_GET['page_num']);
$pg->field = "DISTINCT a.sno, a.themenm, a.basic, a.updatedt, a.regdt";
$pg->setQuery($db_table, $where, "basic ASC, $sort");
$pg->exec();

$res = $db->query($pg->query);

//적용 카테고리 갯수
$theme = array();
$theme_query = $db->query("SELECT themeno, COUNT(themeno) cnt FROM ".GD_CATEGORY." GROUP BY themeno;");
while($theme_cnt = $db->fetch($theme_query)) {
	if($theme_cnt['themeno'] != '') $theme[$theme_cnt['themeno']] = $theme_cnt['cnt'];
}

// 기본 테마 설정 리스트
$sql = "SELECT sno, themenm, basic FROM ".GD_GOODS_SMART_SEARCH." ORDER BY basic ASC, themenm ASC";
$rs = $db->query($sql);

$queryString = "sort=".$_GET['sort']."&skey=".$_GET['skey']."&sword=".$_GET['sword']."&cate[]=".$_GET['cate'][0]."&cate[]=".$_GET['cate'][1]."&cate[]=".$_GET['cate'][2]."&cate[]=".$_GET['cate'][3]."&regdt[]=".$_GET['regdt'][0]."&regdt[]=".$_GET['regdt'][1];
?>

<script language="JavaScript">
	function sort(val) {
		$('sort').value = val;
		frmList.submit();
	}

	function setBasic(basicNo) {
		if(confirm("선택한 테마를 기본테마로 설정 하시겠습니까?")) location.href="../goods/indb.smart_search.php?mode=changeBasic&basic=" + basicNo + "&<?=$qstr?>";
	}

	window.onload = function() {
		cssRound('MSG01');
	}
</script>

<div class="title title_top">SMART 검색 사용설정<span>SMART검색 사용여부를 설정합니다.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=38')"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2"></a></div>

<form name="setForm" method="post" action="indb.smart_search.php">
<input type="hidden" name="mode" id="mode" value="setOption" />
<input type="hidden" name="qstr" id="qstr" value="<?=$qstr?>" />
<table class="tb">
<col class="cellC" style="width:100px"><col class="cellL">
<col class="cellC"><col class="cellL">
<tr>
	<td>사용 설정</td>
	<td class="noline">
		<table cellspacing="0" cellpadding="0" border="0">
		<tr valign="center">
			<td width="145">
				<input type="radio" name="smartSearch_useyn" id="uy" value="y" <?=$checked['smartSearch_useyn']['y']?> /><label for="uy">사용</label>
				<input type="radio" name="smartSearch_useyn" id="un" value="n" <?=$checked['smartSearch_useyn']['n']?> /><label for="un">사용안함</label>
			</td>
			<td>
				<span class="extext">사용여부를 설정합니다.<br />'사용안함'으로 설정시 '상품분류[카테고리]관리> 모든 카테고리의 SMART검색 설정'이 비활성화 됩니다.</span>
			</td>
		</tr>
		</table>
	</td>
</tr>
<tr>
	<td>기본테마 설정</td>
	<td>
		<select name="basic" id="basic">
		<? while($data = $db->fetch($rs)) { ?>
			<option value="<?=$data['sno']?>"<?=$data['basic'] == 'y' ? ' selected' : ''?>><?=htmlspecialchars($data['themenm']).($data['basic'] == 'y' ? '(기본테마)' : '')?></option>
		<? } ?>
		</select>
		<span class="extext" style="margin-left:30px;">'상품분류[카테고리]관리 > SMART검색설정' 에서 별도의 테마선택이 없을시 설정된 기본테마가 적용됩니다.</span>
	</td>
</tr>
</table>
<div class="button_top" style="padding-bottom:15px"><input type="image" src="../img/btn_save3.gif" /></a></div>
</form>

<div class="title title_top">SMART 검색 테마관리<span>SMART검색 테마 등록 및 수정 등을 관리할 수 있습니다.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=38')"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2"></a></div>

<form name="frmList">
<input type="hidden" name="sort" id="sort" value="<?=$_GET['sort']?>" />

<table class="tb">
<col class="cellC" style="width:100px"><col class="cellL">
<col class="cellC"><col class="cellL">
<tr>
	<td>키워드검색</td>
	<td colspan="3"><select name="skey">
	<option value="all"> = 통합검색 =
	<option value="themenm" <?=$selected['skey']['themenm']?>>테마명
	<option value="catnm" <?=$selected['skey']['catnm']?>>카테고리명
	</select>
	<input type="text" name="sword" value="<?=$_GET['sword']?>" class="line" style="height:22px">
	</td>
</tr>
<tr>
	<td>분류선택</td>
	<td colspan=3><script>new categoryBox('cate[]', 4, '<?=$category?>', '', 'frmList');</script></td>
</tr>
<tr>
	<td>등록일</td>
	<td colspan="3">
	<input type=text name="regdt[]" value="<?=$_GET['regdt'][0]?>" onclick="calendar(event)" onkeydown="onlynumber()" class="cline"> -
	<input type=text name="regdt[]" value="<?=$_GET['regdt'][1]?>" onclick="calendar(event)" onkeydown="onlynumber()" class="cline">
	<a href="javascript:setDate('regdt[]',<?=date("Ymd")?>,<?=date("Ymd")?>)"><img src="../img/sicon_today.gif" align="absmiddle"></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-7 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_week.gif" align="absmiddle"></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-15 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twoweek.gif" align="absmiddle"></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-1 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_month.gif" align="absmiddle"></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-2 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twomonth.gif" align="absmiddle"></a>
	<a href="javascript:setDate('regdt[]')"><img src="../img/sicon_all.gif" align="absmiddle"></a>
	</td>
</tr>
</table>
<div class="button_top"><input type="image" src="../img/btn_search2.gif"></div>

<div style="padding-top:15px"></div>

<table width="100%" cellpadding="0" cellspacing="0">
<tr>
	<td class="pageInfo"><font class="ver8">
	총 <b><?=$total?></b>개, 검색 <b><?=$pg->recode['total']?></b>개, <b><?=$pg->page['now']?></b> of <?=$pg->page['total']?> Pages
	</td>
	<td align="right">

	<table cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td valign="bottom"><img src="../img/sname_date.gif" /><a href="javascript:sort('regdt desc')"><img name="sort_regdt_desc" src="../img/list_up_off.gif" /></a><a href="javascript:sort('regdt')"><img name="sort_regdt" src="../img/list_down_off.gif" /></a><img src="../img/sname_dot.gif" /><img src="../img/sname_theme.gif" /><a href="javascript:sort('themenm desc')"><img name="sort_themenm_desc" src="../img/list_up_off.gif" /></a><a href="javascript:sort('themenm')"><img name="sort_themenm" src="../img/list_down_off.gif" /></a>&nbsp;</td>
	</tr>
	</table>

	</td>
</tr>
</table>
</form>

<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr><td class="rnd" colspan="7"></td></tr>
<tr class="rndbg">
	<th width="60">번호</th>
	<th width="">테마명</th>
	<th width="">적용 카테고리</th>
	<th width="">등록일</th>
	<th width="60">복사</th>
	<th width="60">수정</th>
	<th width="60">삭제</th>
</tr>
<tr><td class="rnd" colspan="7"></td></tr>
<?
	while($data = $db->fetch($res)) {
		$count_category = ($theme[$data['sno']] == 0) ? '0' : $theme[$data['sno']];
		if($count_category) $count_category = "<a href=\"javascript:popupLayer('../goods/smart_search_category.php?themeno=".$data['sno']."',600,500);\">".$count_category."개</a>";
		else $count_category .= "개";
?>
<tr><td height="4" colspan="7"></td></tr>
<tr height="25">
	<td align="center"><font class="ver8" color="616161"><?=$pg->idx--?></td>
	<td align="center"><a href="../goods/smart_search_register.php?mode=modTheme&no=<?=$data['sno']?>&<?=$queryString?>&page=<?=$_GET['page']?>"><?=htmlspecialchars($data['themenm']).($data['basic'] == 'y' ? ' (기본테마)' : '')?></a><a href="javascript:setBasic(<?=$data['sno']?>);"></a></td>
	<td align="center"><?=$count_category?></td>
	<td align="center"><font class="ver81" color="444444"><?=substr($data['regdt'], 0, 10)?></td>
	<td align="center"><a href="../goods/indb.smart_search.php?mode=copTheme&no=<?=$data['sno']?>&<?=$queryString?>" onclick="return confirm('동일한 테마를 하나 더 자동 등록 합니다.')"><img src="../img/i_copy.gif"></a></td>
	<td align="center"><a href="../goods/smart_search_register.php?mode=modTheme&no=<?=$data['sno']?>&<?=$queryString?>&page=<?=$_GET['page']?>"><img src="../img/i_edit.gif"></a></td>
	<td align="center">
		<? if($theme[$data['sno']]) { ?>
		<a href="javascript:void(0);" onclick="alert('적용중인 카테고리가 있습니다.\n\n해당 카테고리의 Smart검색 테마설정 변경 후, 삭제해 주세요.[<?=$theme[$data['sno']]?>]');"><img src="../img/i_del.gif"></a>
		<? } else { ?>
		<a href="../goods/indb.smart_search.php?mode=delTheme&no=<?=$data['sno']?>&<?=$queryString?>&page=<?=$_GET['page']?>" onclick="return confirm('[<?=addslashes($data['themenm'])?>] 테마를 삭제하시겠습니까?\n\n삭제시 정보는 복구되지 않습니다.')"><img src="../img/i_del.gif"></a>
		<? } ?>
	</td>
</tr>
<tr><td height="4"></td></tr>
<tr><td class="rndline" colspan="7"></td></tr>
<? } ?>
</table>

<div style="padding-top:15px"></div>
<div align="center" class="pageNavi"><font class="ver8"><?=$pg->page['navi']?></font></div>
<div style="padding:5px 0px 10px 0px;" align="right"><a href="../goods/smart_search_register.php?<?=$queryString?>&page=<?=$_GET['page']?>"><img src="../img/btn_themeapply_s.gif" /></a></div>

<div style="padding-top:30px"></div>

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">[테마등록] 버튼을 클릭하여 SMART검색 사용시 카테고리별로 적용할 테마를 설정, 등록 합니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">검색 메뉴통하여 등록된 테마목록을 조회하고, 적용된 카테고리를 확인 할 수 있습니다.</td></tr>
<tr><td>&nbsp;</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">템플릿 디자인은 <a href="../design/codi.php?ifrmCodiHref=<?=urlencode("iframe.codi.php?design_file=proc/smartSearch.htm")?>" target="_blank" style="color:#FFFFFF; font-weight:bold;">[ 디자인페이지 > 기타페이지 > smartSearch.htm ]</a> 페이지에서 수정 및 편집이 가능 합니다.</td></tr>
</table>
</div>
<? include "../_footer.php"; ?>
