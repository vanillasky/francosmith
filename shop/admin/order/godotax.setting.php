<?php
$location = "고도전자세금계산서 > 고도전자세금계산서 설정";
include "../_header.php";
$config_pay = $config->load('configpay');
$config_tax = $config_pay['tax'];
$config_godotax = $config->load('godotax');

?>
<form method="post" action="../order/godotax.setting.indb.php" target="ifrmHidden" id="frmTax">
<div class="title title_top">고도빌 신청/관리<span>고도빌(전자세금계산서) 서비스를 신청 및 관리하는 페이지 입니다.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=order&no=20')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a></div>
<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td>발행 사용여부</td>
	<td class="noline">
	<input type="radio" name=useyn value='y' <?=frmChecked($config_tax['useyn'],'y')?>> 사용
	<input type="radio" name=useyn value='n' <?=frmChecked($config_tax['useyn'],'n')?>> 사용안함
	</td>
</tr>
<tr>
	<td>발행 결제조건</td>
	<td class=noline>
	<input type=checkbox name=use_a <?=frmChecked($config_tax['use_a'],'on')?> value="on"> 무통장입금
	<input type=checkbox name=use_c disabled> 신용카드
	<input type=checkbox name=use_o <?=frmChecked($config_tax['use_o'],'on')?> value="on"> 계좌이체
	<input type=checkbox name=use_v <?=frmChecked($config_tax['use_v'],'on')?> value="on"> 가상계좌
	</td>
</tr>
<tr>
	<td>발행 시작단계</td>
	<td class=noline>
	<input type=radio name=step value='1' <?=frmChecked($config_tax['step'],'1')?>> 입금확인
	<input type=radio name=step value='2' <?=frmChecked($config_tax['step'],'2')?>> 배송준비중
	<input type=radio name=step value='3' <?=frmChecked($config_tax['step'],'3')?>> 배송중
	<input type=radio name=step value='4' <?=frmChecked($config_tax['step'],'4')?>> 배송완료
	</td>
</tr>
</table>
<br><br>
<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td>고도빌 회원 ID</td>
	<td>
		<input type="text" name="godotax_site_id" value="<?=$config_godotax['site_id']?>" class="line" style="width:170px" maxlength="16">
	</td>
</tr>
<tr>
	<td>고도빌 API_KEY</td>
	<td>
		<input type="text" name="godotax_api_key" value="<?=$config_godotax['api_key']?>" class="line" style="width:300px" maxlength="32"><br>
		<div class="extext" style="padding-top: 3px;">
		 고도빌 홈페이지에서 로그인 후, 로그인박스에 있는 [API KEY] 버튼을 클릭하면 확인할 수 있습니다. <br>
		 API KEY 값을 복사하여, 삽입하시면 됩니다.
		</div>
	</td>
</tr>

</table>

<div style="position:relative;">
	<div class=button >
	<input type=image src="../img/btn_save.gif">
	</div>
	<a href="http://www.godobill.com" target="_blank" style="display:block;position:absolute;right:10px;top:0px"><img src="../img/btn_godobill_go2.gif"></a>
</div>

</form>

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">발행 결제조건에서 신용카드의 경우에는 세금계산서 발급이 불가능합니다.
신용카드 매출전표를 세금계산서 대용으로 사용하면 됩니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">고도빌 회원ID, 고도빌 API_KEY 는 고도빌에 회원가입을 하면 발급이 되는 정보입니다. 고도빌 홈페이지에서 확인 후 입력하시면 됩니다.
</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>
<? include "../_footer.php"; ?>