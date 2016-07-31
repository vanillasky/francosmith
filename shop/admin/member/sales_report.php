<?

include "../_header.popup.php";
include "../../lib/page.class.php";

### 회원정보
$memInfo = $db->fetch("select * from ".GD_MEMBER." where m_no='$_GET[m_no]'");

$member_grp = Core::loader('member_grp');
$grp_ruleset = $member_grp->ruleset;

$data = $member_grp->_get_report($memInfo[m_no]);

if($data[type] == "point")	{
	$display_figure = "none";
	$display_point = "block";
} else {
	$display_figure = "block";
	$display_point = "none";
}
?>

<div class="title title_top">회원 실적보기</div>
<div style="padding:5px;"></div>

<div style="display:<?=$display_figure?>;">
<div style="padding-left:3"><b>[실적 수치제]</b></div>
<div style="padding-top:3"></div>

<table width=100% border=1 bordercolor=#cccccc style="border-collapse:collapse">
<tr style="background:#302D2A; height:25px; line-height:25px; text-align:center; color:#ffffff; font-weight:bold;">
	<th width="30%"></th>
	<th width="35%">샵 전체</th>
	<th width="35%">모바일샵 추가실적</th>
</tr>
<tr style="height:25px; line-height:25px;">
	<td style="text-align:center; font-weight:bold;">구매금액</td>
	<td style="text-align:right; padding-right:5px;"> <?=number_format($data[pc][OrderPrice])?> 원</td>
	<td style="text-align:right; padding-right:5px;"> <?=number_format($data[mobile][OrderPrice])?> 원</td>
</tr>
<tr style="height:25px; line-height:25px;">
	<td style="text-align:center; font-weight:bold;">구매횟수</td>
	<td style="text-align:right; padding-right:5px;"> <?=number_format($data[pc][OrderCount])?> 회</td>
	<td style="text-align:right; padding-right:5px;"> <?=number_format($data[mobile][OrderCount])?> 회</td>
</tr>
<tr style="height:25px; line-height:25px;">
	<td style="text-align:center; font-weight:bold;">구매후기</td>
	<td style="text-align:right; padding-right:5px;"> <?=number_format($data[pc][ReviewCount])?> 회</td>
	<td style="text-align:right; padding-right:5px;"> <?=number_format($data[mobile][ReviewCount])?> 회</td>
</tr>
</table>
</div>

<div style="display:<?=$display_point?>;">
<div style="padding-left:3"><b>[실적 점수제]</b></div>
<div style="padding-top:3"></div>

<table width=100% border=1 bordercolor=#cccccc style="border-collapse:collapse">
<tr style="background:#302D2A; height:25px; line-height:25px; text-align:center; color:#ffffff; font-weight:bold;">
	<th width="30%"></th>
	<th width="35%">PC용</th>
	<th width="35%">모바일용</th>
</tr>
<tr style="height:25px; line-height:25px;">
	<td style="text-align:center; font-weight:bold;">실적점수</td>
	<td style="text-align:right; padding-right:5px;"> <?=number_format($data[pc])?> 점</td>
	<td style="text-align:right; padding-right:5px;"> <?=number_format($data[mobile])?> 점</td>
</tr>
</table>
</div>