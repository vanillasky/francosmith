<?

include "../_header.popup.php";
include "../../lib/page.class.php";

### ȸ������
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

<div class="title title_top">ȸ�� ��������</div>
<div style="padding:5px;"></div>

<div style="display:<?=$display_figure?>;">
<div style="padding-left:3"><b>[���� ��ġ��]</b></div>
<div style="padding-top:3"></div>

<table width=100% border=1 bordercolor=#cccccc style="border-collapse:collapse">
<tr style="background:#302D2A; height:25px; line-height:25px; text-align:center; color:#ffffff; font-weight:bold;">
	<th width="30%"></th>
	<th width="35%">�� ��ü</th>
	<th width="35%">����ϼ� �߰�����</th>
</tr>
<tr style="height:25px; line-height:25px;">
	<td style="text-align:center; font-weight:bold;">���űݾ�</td>
	<td style="text-align:right; padding-right:5px;"> <?=number_format($data[pc][OrderPrice])?> ��</td>
	<td style="text-align:right; padding-right:5px;"> <?=number_format($data[mobile][OrderPrice])?> ��</td>
</tr>
<tr style="height:25px; line-height:25px;">
	<td style="text-align:center; font-weight:bold;">����Ƚ��</td>
	<td style="text-align:right; padding-right:5px;"> <?=number_format($data[pc][OrderCount])?> ȸ</td>
	<td style="text-align:right; padding-right:5px;"> <?=number_format($data[mobile][OrderCount])?> ȸ</td>
</tr>
<tr style="height:25px; line-height:25px;">
	<td style="text-align:center; font-weight:bold;">�����ı�</td>
	<td style="text-align:right; padding-right:5px;"> <?=number_format($data[pc][ReviewCount])?> ȸ</td>
	<td style="text-align:right; padding-right:5px;"> <?=number_format($data[mobile][ReviewCount])?> ȸ</td>
</tr>
</table>
</div>

<div style="display:<?=$display_point?>;">
<div style="padding-left:3"><b>[���� ������]</b></div>
<div style="padding-top:3"></div>

<table width=100% border=1 bordercolor=#cccccc style="border-collapse:collapse">
<tr style="background:#302D2A; height:25px; line-height:25px; text-align:center; color:#ffffff; font-weight:bold;">
	<th width="30%"></th>
	<th width="35%">PC��</th>
	<th width="35%">����Ͽ�</th>
</tr>
<tr style="height:25px; line-height:25px;">
	<td style="text-align:center; font-weight:bold;">��������</td>
	<td style="text-align:right; padding-right:5px;"> <?=number_format($data[pc])?> ��</td>
	<td style="text-align:right; padding-right:5px;"> <?=number_format($data[mobile])?> ��</td>
</tr>
</table>
</div>