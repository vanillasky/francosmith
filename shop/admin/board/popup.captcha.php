<?

include "../_header.popup.php";
@include "../../conf/captcha.php";

if (is_array($captcha)) $captcha = array_map("slashes",$captcha);

?>

<div class="title title_top">자동등록방지문자 이미지 설정<span>자동등록방지문자 관련사항을 설정하세요</span></div>

<form method=post action="indb.php" onsubmit="return chkForm(this)">
<input type=hidden name=mode value="captcha">

<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>이미지 배경색</td><td><input type=text name=captcha[bgcolor] value="<?=$captcha['bgcolor']?>" maxlength="6" style="width:100;"> <a href="javascript:colortable();"><img src="../img/codi/btn_colortable.gif" border="0" alt="색상표 보기" align="absmiddle"></a> &nbsp;<font class=extext>기본색상값<font class=small>(FFFFFF)</font>을 사용하려면 공란으로 두세요</td>
</tr>
<tr>
	<td>이미지 글자색</td><td><input type=text name=captcha[color] value="<?=$captcha['color']?>" maxlength="6" style="width:100;"> <a href="javascript:colortable();"><img src="../img/codi/btn_colortable.gif" border="0" alt="색상표 보기" align="absmiddle"></a> &nbsp;<font class=extext>기본색상값<font class=small>(262626)</font>을 사용하려면 공란으로 두세요</td>
</tr>
<tr>
	<td height=50>현재 적용된<br>등록방지 이미지</td><td><IMG src="../../proc/captcha.php" align="absmiddle">&nbsp;&nbsp;<font class=small1 color=666666>아래 확인을 누르면 적용된 이미지가 보입니다</font></td>
</tr>
</table>

<div style="margin-bottom:10px;padding-top:10;" class=noline align=center>
<input type="image" src="../img/btn_confirm_s.gif">
</div>

</form>


<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">이곳에서 설정하면 사용하는 모든 게시판의 자동등록방지문자에 적용됩니다.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

<script>table_design_load();</script>