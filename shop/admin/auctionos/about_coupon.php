<?
$location = "어바웃 > 어바웃쿠폰 설정";
include "../_header.php";
@include "../../conf/auctionos.php";
$aboutcoupon = $config->load('aboutcoupon');

if (!$aboutcoupon['use_aboutcoupon']) $aboutcoupon['use_aboutcoupon'] = 'N';
if (!$aboutcoupon['use_test']) $aboutcoupon['use_test'] = 'Y';

$checked['use_aboutcoupon'][$aboutcoupon['use_aboutcoupon']] = "checked";
$checked['use_test'][$aboutcoupon['use_test']] = "checked";
?>
<div class="title title_top">어바웃쿠폰 설정 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=marketing&no=21')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<form name=form method=post action="indb.php" onsubmit="return chkForm(this)">
<input type=hidden name=mode value="aboutcoupon">

<table class="tb" border="0">
<col class="cellC"><col class="cellL">

<tr class='noline'>
	<td>어바웃쿠폰 사용</td>
	<td>
		<input type='radio' name='use_aboutcoupon' value='Y' <?=$checked['use_aboutcoupon']['Y']?> />about쿠폰 연동사용함
		<input type='radio' name='use_aboutcoupon' value='N' <?=$checked['use_aboutcoupon']['N']?> />about쿠폰 연동사용안함
		<P>
		<div style="overflow:inline;"><span class="extext" style="padding-left:5">어바웃 쿠폰연동을 사용함으로 설정하시면, 어바웃에서 제시한 기준에 따라 어바웃에서 유입된 것으로 판단되는 경우, 모든 상품에 대해 상품쿠폰 적용이 자동으로 처리됩니다.</span>
		</div>
		<div style="overflow:inline;"><span class="extext" style="padding-left:5">어바웃 쿠폰연동 사용함 으로 선택하시면, 모든 상품에 대해 기한제한없는 회원 다운로드 쿠폰이 자동발급됩니다.</span></div>
		<div style="overflow:inline;"><span class="extext" style="padding-left:5">어바웃 쿠폰은 쿠폰사용여부 설정과 관련없이 적용됩니다.</span></div>
		<div style="overflow:inline;"><span class="extext" style="padding-left:5">어바웃 쿠폰은 복수구매시 상품개수만큼 적용됩니다.</span></div>
		<div style="overflow:inline;"><span class="extext" style="padding-left:5">어바웃 쿠폰은 사용후, 다음 주문시에도 다운로드하여 사용가능합니다.</span></div>
	</td>
</tr>
<tr >
	<td>쿠폰적용기간</td>
	<td colspan=3>
	<input type=text name=regdt[] value="<?=$aboutcoupon[startdate]?>" onclick="calendar(event)" onkeydown="onlynumber()" class="cline" required> -
	<input type=text name=regdt[] value="<?=$aboutcoupon[enddate]?>" onclick="calendar(event)" onkeydown="onlynumber()" class="cline" required>	
	</td>
</tr>
<tr >
	<td>쿠폰레이어 위치</td>
	<td>
		좌측 위치 : <input type='input' name='left_loc' size=10  value='<?=$aboutcoupon[left_loc]?>'required >
		상단 좌표 : <input type='input' name='top_loc' size=10 value='<?=$aboutcoupon[top_loc]?>' required >
	</td>
</tr>
<tr class='noline'>
	<td>어바웃쿠폰 테스트</td>
	<td>
		<input type='radio' name='use_test' value='Y' <?=$checked['use_test']['Y']?> />about쿠폰 연동 테스트적용<br>
		<input type='radio' name='use_test' value='N' <?=$checked['use_test']['N']?> />about쿠폰 연동 테스트아님
		<p>
		<div style="overflow:inline;"><span class="extext" style="padding-left:5">어바웃 쿠폰연동 테스트적용을 선택하면 어바웃쿠폰연동 사용시에도 관리자 로그인시에만 어바웃쿠폰이 노출되고, 적용됩니다.</span></div>
		<div style="overflow:inline;"><span class="extext" style="padding-left:5">이 기능은 어바웃쿠폰의 정상적인 적용을 확인하기 위한 것 입니다.</span></div>
	</td>
</tr>
</table>
<p/>
<div align="center"><input type="image" src="../img/btn_naver_install.gif" align="absmiddle" border="0"></div>
</form>

<p/>

<div id="MSG01">
<table cellpadding="2" cellspacing="0" border=0 class="small_ex">
<tr><td>
<div style="padding-top:6;"></div>
<img src="../img/icon_list.gif" align="absmiddle"><span class="color_ffe">어바웃 연동쿠폰이란?</span><BR>
&nbsp;&nbsp;어바웃 쿠폰연동 사용함 으로 선택하시면, 모든 상품에 대해 기한제한없는 회원 다운로드 쿠폰이 자동발급됩니다.<br>
&nbsp;&nbsp;어바웃 쿠폰은 쿠폰사용여부 설정과 관련없이 적용됩니다.<br>
&nbsp;&nbsp;어바웃 쿠폰은 복수구매시 상품개수만큼 적용됩니다.<br>
&nbsp;&nbsp;어바웃 쿠폰은 사용후, 다음 주문시에도 다운로드하여 사용가능합니다.<br>
<br>
<br>
<img src="../img/icon_list.gif" align="absmiddle"><span class="color_ffe"> 어바웃 연동쿠폰 테스트이란?</span><BR>
&nbsp;&nbsp;어바웃 쿠폰연동 기능의 정상여부를 확인하기 위해, 어바웃 쿠폰연동 사용함 으로 설정 후, <br>
&nbsp;&nbsp;어바웃 쿠폰연동 테스트적용을 선택하면, 관리자로 로그인 시에서 어바웃 쿠폰이 노출되고 적용되는 기능입니다.<br>
<br>
</td></tr>
</table>
</div>
<script>cssRound('MSG01','#F7F7F7')</script>
<? include "../_footer.php"; ?>