<?php
$location = '기본관리 > 약관/개인정보취급방침 설정';
include '../_header.php';
@include '../../conf/terms/terms.config.php';

$checked['private2YN'][$cfg['private2YN']] = 'checked';
$checked['private3YN'][$cfg['private3YN']] = 'checked';

$termsFilePath = dirname(__FILE__) . '/../../conf/terms/';
$termsTitleArr = array(
	1 => '쇼핑몰 이용약관',
	2 => '개인정보취급방침',
	3 => '개인정보수집 및<br />이용동의(회원가입)',
	4 => '개인정보수집 및<br />이용동의(비회원)',
	5 => '개인정보수집 및<br />이용동의(문의)',
	6 => '개인정보 제3자 제공<br />(회원가입)',
	7 => '개인정보 취급위탁<br />(회원가입)',
	8 => '추가 동의 항목<br />(회원가입)'
);
$termsIncludeArr = array(
	1 => '_terms.agreement.php',
	2 => '_terms.policyCollection1.php',
	3 => '_terms.policyCollection2.php',
	4 => '_terms.policyCollection3.php',
	5 => '_terms.policyCollection4.php',
	6 => '_terms.thirdPerson.php',
	7 => '_terms.entrust.php',
	8 => '_terms.addConsent.php'
);
$termsButtons		= "
	<input type='image' src='../img/btn_register.gif' />
	<a href='javascript:history.back();'><img src='../img/btn_cancel.gif' /></a>
";

$termsIncludeArrCount = @count($termsIncludeArr);
?>
<style>
a:hover										{ font-weight: bold; color:#2E64FE; text-decoration:underline; }
textarea									{ width: 1000px; height: 300px; padding: 20px 0 0 20px; }
.termsTable									{ width: 910px; height: 50px; }
.termsTable .termsTableSmall				{ width: 124px; height: 50px; }
.termsTableBorder							{ border: 1px #D8D8D8 solid; }
.termsBorderBottomZero						{ border-bottom-width: 0px; }
.termsBorderRightZero						{ border-right-width: 0px; }
.termsTrHeight30							{ height: 30px; }
.termsTdWidth100							{ width: 100px; }
.termsPadding								{ padding: 5px 0 5px 5px; }
.termsPaddingLeft							{ padding-left: 10px; }
.termsFontColorRed							{ color: #FF0000; }
.termsFontColorSky							{ color: #0080FF; }
.termsBgColorSGray							{ background-color: #A4A4A4; }
.termsBgColorGray							{ background-color: #F2F2F2; }
.termsBgColorWhite							{ background-color: #FFFFFF; }
.termsFontWeightBold						{ font-weight: bold; }
.termsTextUnderline							{ text-decoration:underline; }
</style>

<form name="form" method="post" action="indb.php" onsubmit="return chkTermsForm()" target="ifrmHidden">
<input type="hidden" name="mode" value="terms" />

<div class="title title_top">약관/개인정보 설정<span>쇼핑몰의 이용약관 및 개인정보취급방침 페이지에 표시됩니다.
</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=2')"><img src="../img/btn_q.gif" border="0" align="absmiddle" /></a></div>


<table cellpadding="0" cellspacing="0" class="termsTable center" border="0">
<tr>
	<?php 
	$subClass = 'termsBorderRightZero';
	for($i=1; $i<=$termsIncludeArrCount; $i++){
		if($i == 8) $subClass = '';
	?>
	<td class="termsBorderBottomZero termsTableBorder <?php echo $subClass; ?>">
		<table cellpadding="0" cellspacing="0" border="0" class="termsTableSmall hand" id="termsTab<?php echo $i; ?>" onclick="javascript:termsTab('<?php echo $i; ?>');">
		<tr>
			<td class="center"><?php echo $termsTitleArr[$i]; ?></td>
			<td>▼</td>
		</tr>
		</table>
	</td>
	<?php } ?>
</tr>
</table>

<?php for($i=1; $i<=$termsIncludeArrCount; $i++){ ?>
<div id="termsForm<?php echo $i; ?>">
	<?php include $termsIncludeArr[$i]; ?>
</div>
<?php } ?>

</form>

<script>
function termsTab(idx)
{
	var termsForm;
	var termsTab;
	var termsArrCount = '<?=$termsIncludeArrCount?>';

	for(var i=1; i<=termsArrCount; i++){
		termsForm	= document.getElementById('termsForm' + i);
		termsTab	= document.getElementById('termsTab' + i);
		
		if(idx == i) {
			termsForm.style.display			= '';
			termsTab.style.backgroundColor	= '#FFFFFF';
		} else {
			termsForm.style.display			= 'none';
			termsTab.style.backgroundColor	= '#F2F2F2';
		}
	}
}

/*
 * chkTermsForm()
 * 
 * 추가동의항목입력시 필수항목 체크 (`사용여부`가 `사용`으로 되있고 동의 내용이 없을때)
 * 
 * 변수 hasConsentSno는 _terms.addConsent.php Line.96에서 설정, 현재 추가된 동의 항목의 갯수
 */
function chkTermsForm()
{
	if (!hasConsentSno) return true;
	else {
		for(var i=0; i<hasConsentSno; i++){
			if (document.getElementsByName('useyn['+i+']')[0].checked === true && !document.getElementsByName('termsContent['+i+']')[0].value){
				alert("추가 동의 항목의 내용을 입력해주세요.");
				document.getElementsByName('termsContent['+i+']')[0].focus();
				return false;
			}
		}
	}
}

termsTab('1');
cssRound('MSG01');
cssRound('MSG02');
cssRound('MSG03');
cssRound('MSG04');
cssRound('MSG05');
cssRound('MSG06');
cssRound('MSG07');
cssRound('MSG08');
</script>
<?php include '../_footer.php'; ?>