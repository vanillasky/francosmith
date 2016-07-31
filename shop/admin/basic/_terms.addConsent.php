<?
// 추가동의항목 최대 생성 갯수
$consentLimit = 5;
$defaultTermsContents = "■ 수집하는 개인정보 항목 :\n■ 개인정보의 수집 및 이용목적 :\n■ 개인정보의 보유 및 이용기간 :";
$datas = array();

$result = $db->query("SELECT * FROM ".GD_CONSENT." ORDER BY sno");
while ($data = $db->fetch($result)){
	$datas[] = $data;
}
?>

<!-- 추가 동의 항목 -->
<input type="hidden" name="delConsentSno" id="delConsentSno" value="">
<?
for($k=0; $k<$consentLimit; $k++){
	$checked['useyn'][$k]['y'] = $datas[$k]['useyn'] == 'y' ? 'checked = "checked"' : '';
	$checked['useyn'][$k]['n'] = $datas[$k]['useyn'] == 'n' || !$datas[$k]['useyn'] ? 'checked = "checked"' : '';
	$checked['requiredyn'][$k]['y'] = $datas[$k]['requiredyn'] == 'y' ? 'checked = "checked"' : '';
	$checked['requiredyn'][$k]['n'] = $datas[$k]['requiredyn'] == 'n' || !$datas[$k]['requiredyn'] ? 'checked = "checked"' : '';
	$termsContents = $datas[$k]['termsContent'] ? $datas[$k]['termsContent'] : $defaultTermsContents ;
?>
<div class="consent-div" id="consent-div-<?echo $k?>">
<input type="hidden" name="consentSno[<?echo $k?>]" value="<?echo $datas[$k]['sno']?>">
<table class="admin-form-table" style="width:1000px; margin-bottom:10px;">
<tr>
	<td width="114">추가 항목명</td>
	<td width="886">
		<input type="text" name="title[<?echo $k?>]" value="<?echo $datas[$k]['title']?>" style="width:90%;">
		<? if ($k != 0) echo "<span style='float:right;' onclick=\"delConsent('".$k."')\" class='hand'><img src='../img/btn_terms_del.gif'></span>";?>
	</td>
</tr>
<tr>
	<td>사용 여부</td>
	<td>
		<input type="radio" name="useyn[<?echo $k?>]" value="y" <?echo $checked['useyn'][$k]['y']?>> 사용함
		<input type="radio" name="useyn[<?echo $k?>]" value="n" <?echo $checked['useyn'][$k]['n']?>> 사용안함
	</td>
</tr>
<tr>
	<td>필수 여부</td>
	<td>
		<input type="radio" name="requiredyn[<?echo $k?>]" value="y" <?echo $checked['requiredyn'][$k]['y']?>> 필수
		<input type="radio" name="requiredyn[<?echo $k?>]" value="n" <?echo $checked['requiredyn'][$k]['n']?>> 선택
	</td>
</tr>
<tr>
	<td colspan="2"><textarea name="termsContent[<?echo $k?>]" style="width:988px;"><?echo $termsContents?></textarea></td>
</tr>
</table>
</div>
<?
if ($k === 0) echo "<div class='right' style='width:1000px; margin-bottom:10px;'><span onclick='addConsent()' class='hand'><img src='../img/btn_terms_add.gif'></span></div>";
}
?>

<div id="consent-area"></div>

<div style="padding:5px 0 30px 5px;">
	<span class="small" style="line-height: 150%;"> 
		- 쇼핑몰이름은 치환코드{shopName}로 제공되어 기본정보 설정에 등록된 “쇼핑몰이름”이 자동으로 표시됩니다.<br />
		- <span class="termsFontWeightBold">등록한 내용은 [회원가입 > 개인정보수집 및 이용동의 항목]</span>에 표시됩니다.
	</span>
</div>

<table cellpadding="0" cellspacing="0" width="100%" border="0">
<tr>
	<td class="termsPadding">
		<span class="termsFontWeightBold termsFontColorRed">※ 2015년 10월 29일 이전 제작 무료 스킨</span>을 사용하시는 경우 <span class="termsFontWeightBold termsTextUnderline">반드시 스킨패치를 적용</span>해야 기능 사용이 가능합니다. <a href="http://www.godo.co.kr/customer_center/patch.php?sno=2257" target="_blank" class="termsFontColorSky termsTextUnderline termsFontWeightBold">[패치 바로가기]</a>
	</td>
</tr>
</table>

<div class="button"><?php echo $termsButtons; ?></div>

<div id="MSG08">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr>
	<td>
	· 공통사항<br />
	- 개인정보취급방침 및 수집,이용 동의에 입력된 샘플 각각의 내용을 참고하여 실제 쇼핑몰 운영에 적합한 내용으로 수정하여 등록합니다.<br /><br />

	· 개인정보 수집 및 이용동의 내용(회원가입)<br />
	- '개인정보취급방침 전체 내용＇에서 개인정보의 수집·이용목적,수집하는 개인정보의 항목,개인정보의 보유 및 이용 기간만 입력을 합니다.<br />
	- 주의 : 3가지 사항의 누락이나 개인정보취급방침 전문 게재를 통한 일괄 동의는 위반사항입니다.<br />
	- 추가된 항목은 회원가입 시 동의항목에 추가됩니다.<br />
	- 동의 여부는 회원 CRM 정보에서 확인하실 수 있습니다.<br />
	</td>
</tr>
</table>
</div>

<script type="text/javascript">
var consentLimit = "<?echo $consentLimit?>";
var consentArray = {};
var hasConsentSno = 1;

for(var k=0; k<consentLimit; k++){
	if (k === 0){
		consentArray[k] = '';
	} else {
		var consentSno = document.getElementsByName('consentSno['+k+']')[0].value;

		if (consentSno){
			hasConsentSno++;
		} else {
			consentArray[k] = $('consent-div-'+k).innerHTML;
			$('consent-div-'+k).remove();
		}
	}
}

function addConsent(){
	if (hasConsentSno >= consentLimit){
		alert("추가 동의 항목은 최대 "+consentLimit+"개까지만 등록이 가능합니다.");
	} else {
		var maxConsentSno = Math.max.apply(Math, Object.keys(consentArray));
		var addContent = "<div class=\"consent-div\" id=\"consent-div-"+maxConsentSno+"\">";
		addContent += consentArray[maxConsentSno];
		addContent += "</div>";

		new Insertion.Bottom('consent-area',addContent);
		document.getElementsByName('title['+maxConsentSno+']')[0].value = '';
		document.getElementsByName('termsContent['+maxConsentSno+']')[0].value = '<?echo str_replace("\n", "\\n", $defaultTermsContents)?>';
		document.getElementsByName('useyn['+maxConsentSno+']')[1].checked = true;
		document.getElementsByName('requiredyn['+maxConsentSno+']')[1].checked = true;
		delete consentArray[maxConsentSno];
		hasConsentSno++;
	}
}

function delConsent(key){
	document.getElementById('delConsentSno').value += ',' + document.getElementsByName('consentSno['+key+']')[0].value;
	document.getElementsByName('consentSno['+key+']')[0].value = '';

	consentArray[key] = $('consent-div-'+key).innerHTML;
	$('consent-div-'+key).remove();
	hasConsentSno--;
}
</script>