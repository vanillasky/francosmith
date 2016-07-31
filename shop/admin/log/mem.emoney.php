<?
/*
주문테이블 coupon_emoney
상품 reserve
*/
$location = "통계관리 > 회원분석 > 회원 적립금 분석";
include "../_header.php";
include "../../lib/page.class.php";
@include "../../conf/config.pay.php";
@include "../../conf/coupon.php";

$where = array();


// 검색 조건

	$_GET['stype'] = isset($_GET['stype']) ? $_GET['stype'] : 'by_m_no';

	$sdate_s = ($_GET['regdt'][0]) ? date('Y-m-d',strtotime($_GET['regdt'][0])) : date('Y-m',G_CONST_NOW).'-01';
	$sdate_e = ($_GET['regdt'][1]) ? date('Y-m-d', strtotime('+1 day', strtotime($_GET['regdt'][1]))) : date('Y-m',strtotime('+1 month', strtotime($sdate_s))).'-01';

	$_GET['regdt'][0] = $_GET['regdt'][0] ? $_GET['regdt'][0] : date('Ym', G_CONST_NOW).'01';
	$_GET['regdt'][1] = $_GET['regdt'][1] ? $_GET['regdt'][1] : date('Ymt',G_CONST_NOW);

	if (!isset($_GET['syear']) && !isset($_GET['smonth'])) {

		$_GET['syear'][0] = date('Y',G_CONST_NOW);
		$_GET['syear'][1] = date('Y',G_CONST_NOW);

		$_GET['smonth'][0] = date('n',G_CONST_NOW);
		$_GET['smonth'][1] = date('n',G_CONST_NOW);
	}

	$_paging = false;

	switch($_GET['stype']) {
		case 'by_m_no' :
			/* 회원별 -------- */
			$_paging = true;

			$sword = isset($_GET['sword']) ? $_GET['sword'] : '';
			if ($sword) {
				if ($_GET['skey'] == 'all') {
					$where[] = "( CONCAT( MB.m_id, MB.name, MB.nickname, MB.email, MB.phone, MB.mobile, MB.recommid, MB.company ) like '%".$_GET['sword']."%' or MB.nickname like '%".$_GET['sword']."%' )";
				}
				else {
					$where[] = 'MB.'.$_GET['skey']." like '%$sword%'";
				}
			}

			$slevel = isset($_GET['slevel']) ? $_GET['slevel'] : '';
			if ($slevel) {
				$where[] = "MB.level='".$slevel."'";
			}

			$group_field = 'M.m_no';
			$group_order_field = 'O.m_no';
			$join_field = 'XO.m_no';
			$extra_fields = 'MB.m_id, MB.name,';
			break;


		case 'by_month' :
			/* 월별 ---------- */
			$_paging = false;

			$sdate_s = $_GET['syear'][0].'-'.str_pad($_GET['smonth'][0], 2, 0, STR_PAD_LEFT).'-'.'01';
			$sdate_e = $_GET['syear'][1].'-'.str_pad($_GET['smonth'][1] + 1, 2, 0, STR_PAD_LEFT).'-'.'01';

			$group_field = "DATE_FORMAT(M.regdt, '%Y-%m')";
			$group_order_field = "DATE_FORMAT(O.ddt, '%Y-%m')";
			$group_order_allias = " as ddt";

			$join_field = "XO.ddt";
			$extra_fields = '';
			break;


		case 'by_day' :
			/* 일별 ---------- */
			$_paging = false;

			$group_field = "DATE_FORMAT(M.regdt, '%Y-%m-%d')";
			$group_order_field = "DATE_FORMAT(O.ddt, '%Y-%m-%d')";
			$group_order_allias = " as ddt";
			$join_field = "XO.ddt";
			$extra_fields = '';
			break;
	}

	if (checkStatisticsDateRange($sdate_s, $sdate_e) > 365) {
		msg('조회기간 설정은 최대 1년을 넘지 못합니다. 기간 확인후 재설정 해주세요.',$_SERVER['PHP_SELF']);exit;
	}


	$where[] = "M.regdt >= '$sdate_s'";
	$where[] = "M.regdt < '$sdate_e'";


// sql

	// 현재까지 누적 통계
	$query = "
	SELECT
		SUM( IF(M.emoney > 0, M.emoney, 0) ) AS plus, COUNT( IF(M.emoney > 0, 1, null) ) AS plus_cnt,
		SUM( IF(M.emoney < 0, M.emoney, 0) ) AS minus, COUNT( IF(M.emoney < 0, 1, null) ) AS minus_cnt
	FROM ".GD_LOG_EMONEY." AS M
	";
	$overview = $db->fetch($query,1);

	// 통계 리스트
	if (!$_GET[page_num]) $_GET[page_num] = 10;
	if (!$_GET[page]) $_GET[page] = 1;

	$pg = new Page($_GET[page],$_GET[page_num]);

	$pg->field = "
		$group_field AS `group_field`,

		$extra_fields
				
		SUM( IF(M.emoney > 0, M.emoney, 0) ) AS plus,
		COUNT( IF(M.emoney > 0, 1, null) ) AS plus_cnt,
		SUM( IF(M.emoney < 0, M.emoney, 0) ) AS minus,
		COUNT( IF(M.emoney < 0, 1, null) ) AS minus_cnt,
		XO.reserve, XO.reserve_cnt
	";

	$db_table = "
	".GD_LOG_EMONEY." AS M
	INNER JOIN ".GD_MEMBER." AS MB
	ON M.m_no = MB.m_no
	LEFT JOIN 
	(
	SELECT ".$group_order_field.$group_order_allias.",
		SUM( 
			IF ('".$set['emoney']['limit']."' = '1' && O.emoney > 0 , null, O.reserve)
		) AS reserve,
		COUNT( 
			IF ('".$set['emoney']['limit']."' = '1' && O.emoney > 0 , null, O.reserve)
		) AS reserve_cnt	
	
	FROM
	".GD_ORDER." AS O
	WHERE 
	O.m_no AND O.step = 3 AND O.step2 < 40
	AND (O.ncash_save_yn is null OR ncash_save_yn='n' OR ncash_save_yn='b')
	GROUP BY $group_order_field
	) XO
	ON ".$group_field." = ".$join_field."
	";

	if ($_paging === true) {
		$pg->cntQuery = 'SELECT COUNT( DISTINCT MB.m_no) FROM '.$db_table.' WHERE '.implode(' AND ', $where);
		$pg->setQuery($db_table,$where,'','GROUP BY `group_field`');
		$pg->exec();

		$query = $pg->query;
	}
	else {
		$query = 'SELECT '.$pg->field.' FROM '.$db_table;
		$query .= ' WHERE '.implode(' AND ', $where);
		$query .= ' GROUP BY `group_field` ';
		//$query .= ' ORDER BY `group_field` ';
	}

// 쿼리
$rs = $db->query($query);
$rs_max = $db->count_($rs);
$total = $arRow = array();

while ($row = $db->fetch($rs,1)) {
	$arRow[] = $row;
}
$db->free($rs);
?>

<script type="text/javascript">
function fnSetSearchForm(fld) {
	switch(fld) {
		case 'by_m_no':
			$('extra_field_not_by_month').setStyle({'display':'block'});
			$('extra_field_by_month').setStyle({'display':'none'});
			$('extra_field_by_m_no').setStyle({'display':'block'});
			break;

		case 'by_month':
			$('extra_field_not_by_month').setStyle({'display':'none'});
			$('extra_field_by_month').setStyle({'display':'block'});
			$('extra_field_by_m_no').setStyle({'display':'none'});
			break;

		case 'by_day':
			$('extra_field_not_by_month').setStyle({'display':'block'});
			$('extra_field_by_month').setStyle({'display':'none'});
			$('extra_field_by_m_no').setStyle({'display':'none'});
			break;
	}
}


function fnDownloadStatistics() {
	if (confirm('검색된 통계 내역을 다운로드 하시겠습니까?')) {
		var f = document.frmStatistics;
		f.method = 'post'; f.action = './indb.excel.mem.emoney.php'; 
		f.submit();
		f.action = ''; f.target = ''; f.method = '';
	}
}
</script>

<div class="title title_top">회원 적립금 분석 <span>회원 적립금 현황을 조회/분석 할 수 있습니다.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=data&no=34')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<form name="frmStatistics" id="frmStatistics" method=get>
<input type="hidden" name="page_num" value="">
	<table class=tb>
	<col class=cellC><col class=cellL><col class=cellC><col class=cellL>
	<tr>
		<td>검색기준</td>
		<td colspan="3" class="noline">
			<label><input type="radio" name="stype" value="by_m_no" onClick="fnSetSearchForm('by_m_no');" <?=$_GET['stype'] == 'by_m_no' ? 'checked' : ''?>>회원별</label>
			<label><input type="radio" name="stype" value="by_month" onClick="fnSetSearchForm('by_month');" <?=$_GET['stype'] == 'by_month' ? 'checked' : ''?>>월별</label>
			<label><input type="radio" name="stype" value="by_day" onClick="fnSetSearchForm('by_day');" <?=$_GET['stype'] == 'by_day' ? 'checked' : ''?>>일별</label>
		</td>
	</tr>

	<tr id="extra_field_by_m_no" style="display:<?=$_GET['stype'] == 'by_m_no' ? 'block' : 'none'?>">
		<td>키워드검색</td>
		<td>
			<select name="skey">
			<option value="all" <?=$_GET['skey'] == 'all' ? 'selected' : '' ?>> 통합검색 </option>
			<option value="name" <?=$_GET['skey'] == 'name' ? 'selected' : '' ?>> 회원명 </option>
			<option value="nickname" <?=$_GET['skey'] == 'nickname' ? 'selected' : '' ?>> 닉네임 </option>
			<option value="m_id" <?=$_GET['skey'] == 'm_id' ? 'selected' : '' ?>> 아이디 </option>
			<option value="email" <?=$_GET['skey'] == 'email' ? 'selected' : '' ?>> 이메일 </option>
			<option value="phone" <?=$_GET['skey'] == 'phone' ? 'selected' : '' ?>> 전화번호 </option>
			<option value="mobile" <?=$_GET['skey'] == 'mobile' ? 'selected' : '' ?>> 핸폰번호 </option>
			<option value="recommid" <?=$_GET['skey'] == 'recommid' ? 'selected' : '' ?>> 추천인 </option>
			<option value="company" <?=$_GET['skey'] == 'company' ? 'selected' : '' ?>> 회사명 </option>
			</select>

			<input type="text" name="sword" value="<?=$_GET['sword']?>" class="line" />
		</td>
		<td>그룹선택</td>
		<td>
			<select name="slevel">
			<option value="">==그룹선택==</option>
			<? foreach( member_grp() as $v ){ ?>
			<option value="<?=$v[level]?>" <?=$_GET['slevel'] == $v['level'] ? 'selected' : ''?> ><?=$v['grpnm']?> - lv[<?=$v['level']?>]</option>
			<? } ?>
			</select>
		</td>
	</tr>

	<tr>
		<td>기간설정</td>
		<td colspan="3">

			<div id="extra_field_not_by_month" style="display:<?=$_GET['stype'] == 'by_month' ? 'none' : 'block'?>">
				<input type="text" name="regdt[]" onclick="calendar(event)" size="12" class="line" value="<?=$_GET['regdt'][0]?>" /> -
				<input type="text" name="regdt[]" onclick="calendar(event)" size="12" class="line" value="<?=$_GET['regdt'][1]?>"/>

				<a href="javascript:setDate('regdt[]',<?=date("Ymd",G_CONST_NOW)?>,<?=date("Ymd",G_CONST_NOW)?>)"><img src="../img/sicon_today.gif" align="absmiddle"/></a>
				<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-7 day",G_CONST_NOW))?>,<?=date("Ymd",G_CONST_NOW)?>)"><img src="../img/sicon_week.gif" align="absmiddle"/></a>
				<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-15 day",G_CONST_NOW))?>,<?=date("Ymd",G_CONST_NOW)?>)"><img src="../img/sicon_twoweek.gif" align="absmiddle"/></a>
				<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-1 month",G_CONST_NOW))?>,<?=date("Ymd",G_CONST_NOW)?>)"><img src="../img/sicon_month.gif" align="absmiddle"/></a>
				<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-2 month",G_CONST_NOW))?>,<?=date("Ymd",G_CONST_NOW)?>)"><img src="../img/sicon_twomonth.gif" align="absmiddle"/></a>
			</div>

			<div id="extra_field_by_month" style="display:<?=$_GET['stype'] == 'by_month' ? 'block' : 'none'?>">
				<?
				// 적립금 로그 최소년도
				$_e = time();
				$_s = $db->fetch("SELECT MIN(regdt) as regdt FROM ".GD_LOG_EMONEY."",1);
				$_s = $_s['regdt'] ? strtotime($_s['regdt']) : $_e;
				?>
				<select name="syear[]">
					<? for ($i=date('Y',$_s),$m=date('Y',$_e);$i<=$m;$i++) { ?>
					<option value="<?=$i?>" <?=$_GET['syear'][0] == $i ? 'selected' : ''?>><?=$i?></option>
					<? } ?>
				</select>년

				<select name="smonth[]">
					<? for ($i=1;$i<=12;$i++) { ?>
					<option value="<?=$i?>" <?=$_GET['smonth'][0] == $i ? 'selected' : ''?>><?=$i?></option>
					<? } ?>
				</select>월

				~

				<select name="syear[]">
					<? for ($i=date('Y',$_s),$m=date('Y',$_e);$i<=$m;$i++) { ?>
					<option value="<?=$i?>" <?=$_GET['syear'][1] == $i ? 'selected' : ''?>><?=$i?></option>
					<? } ?>
				</select>년

				<select name="smonth[]">
					<? for ($i=1;$i<=12;$i++) { ?>
					<option value="<?=$i?>" <?=$_GET['smonth'][1] == $i ? 'selected' : ''?>><?=$i?></option>
					<? } ?>
				</select>월
			</div>
		</td>
	</tr>

	</table>

	<div class=button_top><input type=image src="../img/btn_search_s.gif"></div>

</form>

<div style="padding-top:15px"></div>

<div class="title title_top">회원 적립금 누적 / 현재기준</div>
<table width=500 cellpadding=0 cellspacing=0 class="statistics-list">
<tr><td class=rnd colspan=15></td></tr>
<tr class=rndbg>
	<th colspan="2">지급적립금</th>
	<th colspan="2">사용적립금</th>
	<th>잔여적립금</th>
</tr>
<tr><td class=rnd colspan=15></td></tr>
<tr height=25 align="center">
	<th class="cell1">건수</th>
	<th class="cell1">금액</th>
	<th>건수</th>
	<th>금액</th>
	<th class="cell1 highlight">금액</th>
</tr>
<tr><td class=rnd colspan=15></td></tr>
<tr height=25>
	<td class="cell1"><?=number_format($overview['plus_cnt'])?></td>
	<td class="cell1"><?=number_format($overview['plus'])?></td>
	<td><?=number_format($overview['minus_cnt'])?></td>
	<td><?=number_format($overview['minus'])?></td>
	<td class="cell1 highlight"><?=number_format($overview['plus'] + $overview['minus'] ) // minus 는 음수이므로 뺄셈 하지 않는다. ?></td>
</tr>
<tr><td colspan=15 bgcolor=A3A3A3></td></tr>

</table>

<div style="padding-top:15px"></div>


<? if ($_paging) { ?>
<table width="100%" cellpadding=0 cellspacing=0 border=0>
<tr>
	<td align="right">
	<img src="../img/sname_output.gif" align=absmiddle>
	<select name=page_num onchange="document.frmStatistics.page_num.value=this.value;document.frmStatistics.submit()">
	<?
	$r_pagenum = array(10,20,40,60,100);
	foreach ($r_pagenum as $v){
	?>
	<option value="<?=$v?>" <?=$_GET[page_num] == $v ? 'selected' : ''?>><?=$v?>개 출력
	<? } ?>
	</select>
	</td>
</tr>
</table>
<? } ?>

<table width=100% cellpadding=0 cellspacing=0 class="statistics-list">
<tr><td class=rnd colspan=15></td></tr>
<tr class=rndbg>
	<th>
	<? if ($_GET['stype'] == 'by_m_no') { ?>
	회원
	<? } else { ?>
	날짜
	<? } ?>
	</th>
	<th colspan="2">지급적립금</th>
	<th colspan="2">사용적립금</th>
	<th>잔여적립금</th>
	<th colspan="2">잠정적립금(배송완료 전)</th>
</tr>
<tr><td class=rnd colspan=15></td></tr>
<tr height=25 align="center">
	<th class="cell1">
	<? if ($_GET['stype'] == 'by_m_no') { ?>
	이름/아이디
	<? } else if ($_GET['stype'] == 'by_month') { ?>
	년/월
	<? } else { ?>
	년/월/일
	<? } ?>
	</th>
	<th>건수</th>
	<th class="">금액</th>
	<th>건수</th>
	<th class="">금액</th>
	<th class="cell1">금액</th>
	<th>건수</th>
	<th>금액</th>
</tr>
<tr><td class=rnd colspan=15></td></tr>

<?
for ($i=0,$m=sizeof($arRow);$i<$m;$i++) {

	$row = $arRow[$i];

	$list_overview['plus_cnt'] += $row['plus_cnt'];
	$list_overview['plus'] += $row['plus'];
	$list_overview['minus_cnt'] += $row['minus_cnt'];
	$list_overview['minus'] += $row['minus'];
	$list_overview['reserve_cnt'] += $row['reserve_cnt'];
	$list_overview['reserve'] += $row['reserve'];
?>
<tr height=25>
	<td class="cell1">
	<? if ($_GET['stype'] == 'by_m_no') { ?>
	<span id="navig" name="navig" m_id="<?=$row['m_id']?>" m_no="<?=$row['group_field']?>"><font class="small1" color="#0074ba"><b><?=$row['name']?></b></font> / <?=$row['m_id']?></span>
	<? } else { ?>
	<?=$row['group_field']?>
	<? } ?>
	</td>

	<td class="numeric ar"><?=number_format($row['plus_cnt'])?></td>
	<td class="currency ar"><?=number_format($row['plus'])?></td>

	<td class="numeric ar"><?=number_format($row['minus_cnt'])?></td>
	<td class="currency ar"><?=number_format($row['minus'])?></td>

	<td class="cell1 numeric highlight ar"><?=number_format($row['plus'] + $row['minus'])?></td>

	<td class="numeric ar"><?=number_format($row['reserve_cnt'])?></td>
	<td class="numeric ar"><?=number_format($row['reserve'])?></td>

</tr>
<tr><td colspan=15 class=rndline></td></tr>
<? } ?>
<tr><td colspan=15 bgcolor=A3A3A3></td></tr>
<tfoot>
<tr>
	<td>합계</td>

	<td class="numeric ar"><?=number_format($list_overview['plus_cnt'])?></td>
	<td class="currency ar"><?=number_format($list_overview['plus'])?></td>

	<td class="numeric ar"><?=number_format($list_overview['minus_cnt'])?></td>
	<td class="numeric ar"><?=number_format($list_overview['minus'])?></td>

	<td class="numeric highlight ar"><?=number_format($list_overview['plus'] + $list_overview['minus'])?></td>

	<td class="numeric ar"><?=number_format($list_overview['reserve_cnt'])?></td>
	<td class="numeric ar"><?=number_format($list_overview['reserve'])?></td>
</tr>
</tfoot>
<tr><td colspan=10 class=rndline></td></tr>
</table>

<table width="100%" style="margin-top:10px;">
<tr>
	<td width="" align="right"><a href="javascript:void(0);" onClick="fnDownloadStatistics()"><img src="../img/btn_download_s.gif"></a></td>
</tr>
</table>


<? if ($_paging === true) { ?>
<div align=center class=pageNavi><font class=ver8><?=$pg->page[navi]?></font></div>
<? } ?>
<div style="padding-top:15px"></div>

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">회원별, 월별, 일별 회원적립금 내역을 확인할 수 있습니다.</td></tr>
<tr><td height="8"></td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">회원적립금 누적 : 현재까지의 회원적립금 지급 및 사용의 누적 내역을 보여줍니다.</td></tr>
<tr><td height="8"></td></tr>
<tr><td>&nbsp;&nbsp;적립금내역 리스트</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">회원별 적립금 내역 : 회원명을 클릭하시면 해당회원의 상세 적립금내역 및 주문 내역을 확인 하실 수 있습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">지급적립금 : 현재까지 지급된 적립금 내역</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">사용적립금 : 현재까지 사용된 적립금 내역</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">잔여적립금 : 현재까지 지급된 적립금 중 사용된 적립금을 제외한 앞으로 사용가능한 적립금 내역</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">잠정적립금 : 지급예정(배송완료수 지급)인 적립금 내역</td></tr>
<tr><td height="8"></td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">시스템 과부화를 고려하여 상품이 많은 경우 검색기간은 최대 1년 단위로 나누어 검색하시고, 엑셀로 파일로 다운로드 하여 활용하시기를 권장 드립니다.</td></tr>
<tr><td height="8"></td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">위 통계 데이터는 주문취소 금액과 메인쇼핑몰(e나무)외 다른 판매채널의 주문관련 금액이 제외된 통계자료 입니다.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>


<script>window.onload = function(){ UNM.inner();};</script>

<? include "../_footer.php"; ?>
