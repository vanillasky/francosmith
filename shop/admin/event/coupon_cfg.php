<?
$location = "쿠폰발행관리 > 쿠폰기능설정";
include "../_header.php";
@include "../../conf/coupon.php";

if(!$cfgCoupon['use_yn'])$cfgCoupon['use_yn']=0;
if(!$cfgCoupon['range'])$cfgCoupon['range']=0;
if(!$cfgCoupon['double'])$cfgCoupon['double']=0;

$checked['use_yn'][$cfgCoupon['use_yn']] = "checked";
$checked['range'][$cfgCoupon['range']] = "checked";
$checked['double'][$cfgCoupon['double']] = "checked";
?>
<div class="title title_top">쿠폰기능설정<span>쿠폰사용 여부 및 기능을 설정합니다. <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=event&no=11')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<form method=post action="indb.coupon.php" onsubmit="return chkForm(this)">
<input type=hidden name=mode value="config">
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>쿠폰기능 사용여부</td>
	<td><input type=radio name="cfgCoupon[use_yn]" value="1" class=null <?=$checked['use_yn'][1]?>> 사용&nbsp; <input type=radio name="cfgCoupon[use_yn]" value="0" class=null <?=$checked['use_yn'][0]?>> 사용하지않음</td>
</tr>
<tr>
	<td>중복적용 여부<br/>(쿠폰+회원혜택)</td>
	<td><input type=radio name="cfgCoupon[range]" value="0" class=null <?=$checked['range'][0]?>> 쿠폰, 회원혜택 동시 사용가능<br/>
	<input type=radio name="cfgCoupon[range]" value="2" class=null <?=$checked['range'][2]?>> 쿠폰만 사용가능 <font class=extext>(쿠폰과 회원혜택 적용이 둘 다 가능할 경우, 쿠폰만 사용 가능하도록 합니다.)</font><br/>
<input type=radio name="cfgCoupon[range]" value="1" class=null <?=$checked['range'][1]?>> 회원혜택만 사용가능 <font class=extext>(쿠폰과 회원혜택 적용이 둘 다 가능할 경우, 회원혜택만 사용 가능하도록 합니다.)</font>
</td>
</tr>
<tr><td colspan=2 bgcolor=white align=left valign=top>
<div style="padding:3 0 0 15"><font class=extext>쿠폰(할인/적립) 과 회원그룹에 따른 혜택(할인/적립) 두 가지를 동시 사용가능 및 둘 중 하나만 사용가능 하도록 정하는 기능입니다.</div>
<div style="padding:2 0 0 15">회원그룹별 혜택은 <a href="../member/group.php" target="_blank">[ 회원관리 > 회원그룹관리 ]</a> 메뉴에서 설정하실 수 있습니다.</div>
<div style="padding-top:10"></div></font></td></tr>
<tr>
	<td>쿠폰 사용제한</td>
	<td><input type=radio name="cfgCoupon[double]" value="1" class=null <?=$checked['double'][1]?>> 하나의 주문에 여러 쿠폰 사용가능&nbsp; <input type=radio name="cfgCoupon[double]" value="0" class=null <?=$checked['double'][0]?>> 하나의 주문에는 오직 한개의 쿠폰만 사용</td>
</tr>
<tr><td colspan=2 bgcolor=white align=left valign=top>
<div style="padding:3 0 0 15"><font  class=extext>하나의 주문에 쿠폰을 사용할 수 있는 갯수를 제한 할 수 있습니다.</div>
<div style="padding:2 0 0 15">여러개의 쿠폰을 발행한 경우, 한 주문당 여러개의 쿠폰을 사용하게 할 것인지, 한개의 쿠폰만 사용하게 할 것인지 정합니다.</font></div>
<div style="padding-top:10"></div></td>
</tr>
</table>
<div class=button>
<input type=image src="../img/btn_modify.gif">
<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a>
</div>
</form>
<? include "../_footer.php"; ?>
