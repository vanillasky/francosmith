<?php
$scriptLoad='<link rel="styleSheet" href="./_inc/style.css">';
$location = "쇼플 > 월별 정산리스트";

include "../_header.php";
require_once ('./_inc/config.inc.php');

// 파라미터
$year = isset($_GET['year']) ? $_GET['year'] : date('Y');

// 쇼플 판매정보
$shople = Core::loader('shople');
$shopleCfg = $shople->cfg;

// 년도별 정산 정보 가져오기
$method = 'GET_CALCULATE';
$param = array();
$data = array(
	'year' => $year
);
$rs = $shople->request($method,$param,$data);
$sheet = $rs['body'];

// 입점 정보 가져오기
$method = 'GET_CALCULATEINFO';
$param = array();
$data = array();
$rs = $shople->request($method,$param,$data);
$shop_info = $rs['body'];
?>

<div class="title title_top">월별 정산리스트<span>정산 처리된 내역을 월별로 확인하실 수 있습니다.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=shople&no=9')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>


<div class=title>정산정보</div>
<table class=tb>
<col class=cellC><col class=cellL><col class=cellC><col class=cellL><col class=cellC><col class=cellL>
<tr>
	<td>업체명</td>
	<td><?=$shop_info['shop_name']?></td>

	<td>정산율</td>
	<td><?=$shop_info['calc_rate']?>%</td>

	<td>정산일</td>
	<td>매월 <?=$shop_info['calc_day']?>일</td>
</tr>
</table>






<div class=title>정산내역</div>

<div style="margin:0 0 10px 0;">
	<select name="year" onChange="location.href='<?=$_SERVER['PHP_SELF']?>?year='+this.value;">
		<? for ($i=2010,$max=date('Y');$i<=$max;$i++) { ?>
		<option value="<?=$i?>" <?=$i == $year ? 'selected' : '' ?>><?=$i?>년</option>
		<? } ?>
	</select>
</div>


<table width="100%" cellpadding="0" cellspacing="0" border="0" class="gd_grid">
<col width="80">
<col width="80">
<col width="150">
<col width="150">
<col>
<col width="150">
<col width="150">
<col width="70">

<thead>
<tr class="rndbg">
	<th>판매월</th>
	<th>판매건</th>
	<th>판매금액</th>
	<th>구매확정건</th>
	<th>정산금액</th>
	<th>공제금액</th>
	<th>총정산금액</th>
	<th>상세내역</th>
</tr>
</thead>
<tbody>
<?
$overview['총매출액'] = $overview['총정산액'] = 0;
for ($i=1;$i<=12;$i++) {
	$row = isset($sheet[$i]) ? $sheet[$i] : $_default;

	// 총 매출,정산
	$overview['총매출액'] += $row['판매']['amount'];
	$overview['총정산액'] += $row['확정']['amount'] - $row['공제']['amount'];
?>
<tr>
	<td class="numeric"><?=$year?>년 <?=sprintf('%02d',$i)?>월</td>
	<td class="numeric"><?=number_format($row['판매']['cnt'])?></td>
	<td class="numeric"><?=number_format($row['판매']['amount'])?>원</td>
	<td class="numeric"><?=number_format($row['확정']['cnt'])?></td>
	<td class="numeric"><?=number_format($row['정산']['amount'])?>원</td>
	<td class="numeric"><?=number_format($row['공제']['cnt'])?>건 / <span class="red"><?=number_format($row['공제']['amount'] * -1)?>원</span></td>
	<td class="numeric blue"><?=number_format($row['정산']['amount'] - $row['공제']['amount'])?>원</td>
	<td><a href="./calculate.detail.php?year=<?=$year?>&month=<?=$i?>">보기</td>
</tr>
<? } ?>
</tbody>
<tr bgcolor="#f7f7f7">
	<td colspan="20" class="overview bold ar">총 매출액: <?=number_format($overview['총매출액'])?>원</td>
</tr>
<tr bgcolor="#f7f7f7">
	<td colspan="20" class="overview bold ar">총 정산액: <?=number_format($overview['총정산액'])?>원</td>
</tr>
</table>


<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle"/>정산은 월별로 진행되며, 1일 부터 말일까지의 구매확정 주문내역을 기준으로 산출됩니다.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

<? include "../_footer.php"; ?>
