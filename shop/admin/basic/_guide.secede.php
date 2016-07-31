<!-- 탈퇴안내 -->
<table cellpadding="0" cellspacing="0" width="1000" border="0" class="guidePaddingRight20">
<tr>
	<td><textarea name="guideSecede"><?php include $guideFilePath . 'guideSecede.txt'; ?></textarea></td>
</tr>
</table>

<div style="padding:5px 0 30px 5px;">
	<span class="small" style="line-height: 150%;"> 
		- 쇼핑몰이름은 치환코드{_cfg[‘shopName']}로 제공되어 기본정보 설정에 등록된 “쇼핑몰이름”이 자동으로 표시됩니다.<br />
		- <span class="guideFontWeightBold">등록한 내용은 [탈퇴안내 페이지]</span>에 표시됩니다.
	</span>
</div>

<table cellpadding="0" cellspacing="0" width="100%" border="0">
<tr>
	<td class="guidePadding">
		<span class="guideFontWeightBold guideFontColorRed">※ 2014년 07월 31일 이전 제작 무료 스킨</span>을 사용하시는 경우 <span class="guideFontWeightBold guideTextUnderline">반드시 스킨패치를 적용</span>해야 기능 사용이 가능합니다. <a href="http://www.godo.co.kr/customer_center/patch.php?sno=2064" target="_blank" class="guideFontColorSky guideTextUnderline guideFontWeightBold">[패치 바로가기]</a>
	</td>
</tr>
<tr>
	<td class="guidePadding">
	- 스킨패치 후에는 디자인관리 페이지에서 제공하던 이용/탈퇴안내 관련 텍스트(TXT)파일은 더 이상 사용하지 않으므로 쇼핑몰 정책에 따른 이용안내 및 탈퇴안내 관련 내용을<br /> 
	위의 각 입력 항목에 입력 또는 수정하여 완성해 주시기 바랍니다.
	</td>
</tr>
</table>

<div class="button"><?php echo $guideButtons; ?></div>

<div id="MSG02">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr>
	<td>
		<strong>· 탈퇴안내 내용</strong><br />
		<span class="guidePaddingLeft">- 탈퇴안내에 입력된 샘플 내용을 참고하여 실제 쇼핑몰 운영에 적합한 내용으로 수정하여 등록합니다.</span><br />
		<span class="guidePaddingLeft">- 회원탈퇴 페이지에 나오며, 회원의 쇼핑몰 탈퇴 시 안내사항을 입력합니다.</span>
	</td>
</tr>
</table>
</div>