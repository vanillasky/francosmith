<?
$location = "����������� > ������ɼ���";
include "../_header.php";
@include "../../conf/coupon.php";

if(!$cfgCoupon['use_yn'])$cfgCoupon['use_yn']=0;
if(!$cfgCoupon['range'])$cfgCoupon['range']=0;
if(!$cfgCoupon['double'])$cfgCoupon['double']=0;

$checked['use_yn'][$cfgCoupon['use_yn']] = "checked";
$checked['range'][$cfgCoupon['range']] = "checked";
$checked['double'][$cfgCoupon['double']] = "checked";
?>
<div class="title title_top">������ɼ���<span>������� ���� �� ����� �����մϴ�. <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=event&no=11')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<form method=post action="indb.coupon.php" onsubmit="return chkForm(this)">
<input type=hidden name=mode value="config">
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>������� ��뿩��</td>
	<td><input type=radio name="cfgCoupon[use_yn]" value="1" class=null <?=$checked['use_yn'][1]?>> ���&nbsp; <input type=radio name="cfgCoupon[use_yn]" value="0" class=null <?=$checked['use_yn'][0]?>> �����������</td>
</tr>
<tr>
	<td>�ߺ����� ����<br/>(����+ȸ������)</td>
	<td><input type=radio name="cfgCoupon[range]" value="0" class=null <?=$checked['range'][0]?>> ����, ȸ������ ���� ��밡��<br/>
	<input type=radio name="cfgCoupon[range]" value="2" class=null <?=$checked['range'][2]?>> ������ ��밡�� <font class=extext>(������ ȸ������ ������ �� �� ������ ���, ������ ��� �����ϵ��� �մϴ�.)</font><br/>
<input type=radio name="cfgCoupon[range]" value="1" class=null <?=$checked['range'][1]?>> ȸ�����ø� ��밡�� <font class=extext>(������ ȸ������ ������ �� �� ������ ���, ȸ�����ø� ��� �����ϵ��� �մϴ�.)</font>
</td>
</tr>
<tr><td colspan=2 bgcolor=white align=left valign=top>
<div style="padding:3 0 0 15"><font class=extext>����(����/����) �� ȸ���׷쿡 ���� ����(����/����) �� ������ ���� ��밡�� �� �� �� �ϳ��� ��밡�� �ϵ��� ���ϴ� ����Դϴ�.</div>
<div style="padding:2 0 0 15">ȸ���׷캰 ������ <a href="../member/group.php" target="_blank">[ ȸ������ > ȸ���׷���� ]</a> �޴����� �����Ͻ� �� �ֽ��ϴ�.</div>
<div style="padding-top:10"></div></font></td></tr>
<tr>
	<td>���� �������</td>
	<td><input type=radio name="cfgCoupon[double]" value="1" class=null <?=$checked['double'][1]?>> �ϳ��� �ֹ��� ���� ���� ��밡��&nbsp; <input type=radio name="cfgCoupon[double]" value="0" class=null <?=$checked['double'][0]?>> �ϳ��� �ֹ����� ���� �Ѱ��� ������ ���</td>
</tr>
<tr><td colspan=2 bgcolor=white align=left valign=top>
<div style="padding:3 0 0 15"><font  class=extext>�ϳ��� �ֹ��� ������ ����� �� �ִ� ������ ���� �� �� �ֽ��ϴ�.</div>
<div style="padding:2 0 0 15">�������� ������ ������ ���, �� �ֹ��� �������� ������ ����ϰ� �� ������, �Ѱ��� ������ ����ϰ� �� ������ ���մϴ�.</font></div>
<div style="padding-top:10"></div></td>
</tr>
</table>
<div class=button>
<input type=image src="../img/btn_modify.gif">
<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a>
</div>
</form>
<? include "../_footer.php"; ?>
