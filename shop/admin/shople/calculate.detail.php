<?php
$scriptLoad='<link rel="styleSheet" href="./_inc/style.css">';
$location = "쇼플 > 정산리스트";

include "../_header.php";
require_once ('./_inc/config.inc.php');


// 파라미터
$year = isset($_GET['year']) ? $_GET['year'] : date('Y');
$month = isset($_GET['month']) ? $_GET['month'] : date('n');
$calc_type = isset($_GET['calc_type']) ? $_GET['calc_type'] : '';
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$page_num = isset($_GET['page_num']) ? $_GET['page_num'] : 10;

// 쇼플 판매정보
$shople = Core::loader('shople');
$shopleCfg = $shople->cfg;

// 년도별 정산 정보 가져오기
$method = 'GET_CALCULATE';

$data = array(
	'year' => $year,
	'month' => $month
);
$rs = $shople->request($method,$param,$data);
$overview = $rs['body'][$month];

// 정산 상세 리스트
$method = 'GET_CALCULATELIST';
$param = array(
	// 페이징 변수
	'page' => $page,
	'page_num' => $page_num,
);
$data = array(
	'year' => $year,
	'month' => $month,
	'calc_type' => $calc_type
);

$rs = $shople->request($method,$param,$data);
$arRow = $rs['body'];

// 페이징
$pg = Core::loader('page',$page,$page_num);
$page_navi = $pg->getNavi($rs['records']);
?>

<div class="title title_top">정산리스트<span>구매 확정되어 정산처리된 내역을 확인하실 수 있습니다.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=shople&no=9')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<form name=frmList>
<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td>정산상태</td>
	<td class="noline">
		<label><input type="radio" name="calc_type" value="" <?=$calc_type == '' ? 'checked' : ''?>>전체</label>
		<label><input type="radio" name="calc_type" value="정산" <?=$calc_type == '정산' ? 'checked' : ''?>>정산완료</label>
		<label><input type="radio" name="calc_type" value="공제" <?=$calc_type == '공제' ? 'checked' : ''?>>공제정산</label>
	</td>
</tr>
<tr>
	<td>기간검색</td>
	<td>
		<select name="year">
			<? for ($i=2010,$max=date('Y');$i<=$max;$i++) { ?>
			<option value="<?=$i?>" <?=$i == $year ? 'selected' : '' ?>><?=$i?>년</option>
			<? } ?>
		</select>

		<select name="month">
			<? for ($i=1;$i<=12;$i++) { ?>
			<option value="<?=$i?>" <?=$i == $month ? 'selected' : '' ?>><?=$i?>월</option>
			<? } ?>
		</select>

	</td>
</tr>
</table>

<div class=button_top><input type=image src="../img/btn_search2.gif"></div>

<div style="padding-top:15px"></div>


</form>


<div class=title>정산현황</div>
<table class=tb>
<col width="25%">
<col width="25%">
<col width="25%">
<col width="25%">
<tr bgcolor="#F6F6F6" align="center" height="25">
	<td>판매</td>
	<td>구매확정</td>
	<td>정산(+)</td>
	<td>공제정산(-)</td>
</tr>
<tr align="center" height="25">
	<td><?=number_format($overview['판매']['cnt'])?>건</td>
	<td><?=number_format($overview['확정']['cnt'])?>건</td>
	<td><?=number_format($overview['정산']['cnt'])?>건</td>
	<td><?=number_format($overview['공제']['cnt'])?>건</td>
</tr>
</table>


	</thead>

<div class=title>정산내역</div>
<div class="pageInfo ver8" style="">총 <b><?=$pg->recode[total]?></b>건, <b><?=$pg->page[now]?></b> of <?=$pg->page[total]?> Pages</div>

<table width="100%" cellpadding="0" cellspacing="0" border="0" class="gd_grid">
<col width="60">
<col width="">
<col width="120">
<col width="130">
<col width="80">
<col width="80">
<col width="80">
<col width="80">
<col width="60">
<thead>
<tr>
	<th>번호</th>
	<th>상품명</th>
	<th>주문번호</th>
	<th>구매확정일</th>
	<th>결제금액</th>
	<th>정산금액</th>
	<th>공제정산금액</th>
	<th>정산일</th>
	<th>처리상태</th>
</tr>
</thead>
<tbody>
<?
for ($i=0,$max=sizeof($arRow);$i<$max;$i++) {
	$row = $arRow[$i];
?>
<tr>
	<td class="numeric"><?=$row['rowNo']?></td>
	<td class="al"><?=$row['prdNm']?></td>
	<td class="numeric"><?=$row['ordNo']?></td>
	<td class="date"><?=$row['pocnfrmDt']?></td>
	<td class="numeric"><?=number_format($row['selPrc'])?></td>
	<td class="numeric blue"><?=($row['calc'] > 0 ? number_format($row['calc']) : '-')?></td>
	<td class="numeric red"><?=($row['calc'] < 0 ? number_format($row['calc']) : '-')?></td>
	<td class="date"><?=$row['calc_date']?></td>
	<td><?=($row['calc_stats'] ? $row['calc_stats'] : '미처리')?></td>
</tr>
<? } ?>
</tbody>
</table>

<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr><td align=center><div class=pageNavi><font class=ver8><?=$page_navi?></font></div></td>
</tr></table>


<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle"/>정산은 월별로 진행되며, 1일 부터 말일까지의 구매확정 주문내역을 기준으로 산출됩니다.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

<? include "../_footer.php"; ?>
