<?
// �߰������׸� �ִ� ���� ����
$consentLimit = 5;
$defaultTermsContents = "�� �����ϴ� �������� �׸� :\n�� ���������� ���� �� �̿���� :\n�� ���������� ���� �� �̿�Ⱓ :";
$datas = array();

$result = $db->query("SELECT * FROM ".GD_CONSENT." ORDER BY sno");
while ($data = $db->fetch($result)){
	$datas[] = $data;
}
?>

<!-- �߰� ���� �׸� -->
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
	<td width="114">�߰� �׸��</td>
	<td width="886">
		<input type="text" name="title[<?echo $k?>]" value="<?echo $datas[$k]['title']?>" style="width:90%;">
		<? if ($k != 0) echo "<span style='float:right;' onclick=\"delConsent('".$k."')\" class='hand'><img src='../img/btn_terms_del.gif'></span>";?>
	</td>
</tr>
<tr>
	<td>��� ����</td>
	<td>
		<input type="radio" name="useyn[<?echo $k?>]" value="y" <?echo $checked['useyn'][$k]['y']?>> �����
		<input type="radio" name="useyn[<?echo $k?>]" value="n" <?echo $checked['useyn'][$k]['n']?>> ������
	</td>
</tr>
<tr>
	<td>�ʼ� ����</td>
	<td>
		<input type="radio" name="requiredyn[<?echo $k?>]" value="y" <?echo $checked['requiredyn'][$k]['y']?>> �ʼ�
		<input type="radio" name="requiredyn[<?echo $k?>]" value="n" <?echo $checked['requiredyn'][$k]['n']?>> ����
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
		- ���θ��̸��� ġȯ�ڵ�{shopName}�� �����Ǿ� �⺻���� ������ ��ϵ� �����θ��̸����� �ڵ����� ǥ�õ˴ϴ�.<br />
		- <span class="termsFontWeightBold">����� ������ [ȸ������ > ������������ �� �̿뵿�� �׸�]</span>�� ǥ�õ˴ϴ�.
	</span>
</div>

<table cellpadding="0" cellspacing="0" width="100%" border="0">
<tr>
	<td class="termsPadding">
		<span class="termsFontWeightBold termsFontColorRed">�� 2015�� 10�� 29�� ���� ���� ���� ��Ų</span>�� ����Ͻô� ��� <span class="termsFontWeightBold termsTextUnderline">�ݵ�� ��Ų��ġ�� ����</span>�ؾ� ��� ����� �����մϴ�. <a href="http://www.godo.co.kr/customer_center/patch.php?sno=2257" target="_blank" class="termsFontColorSky termsTextUnderline termsFontWeightBold">[��ġ �ٷΰ���]</a>
	</td>
</tr>
</table>

<div class="button"><?php echo $termsButtons; ?></div>

<div id="MSG08">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr>
	<td>
	�� �������<br />
	- ����������޹�ħ �� ����,�̿� ���ǿ� �Էµ� ���� ������ ������ �����Ͽ� ���� ���θ� ��� ������ �������� �����Ͽ� ����մϴ�.<br /><br />

	�� �������� ���� �� �̿뵿�� ����(ȸ������)<br />
	- '����������޹�ħ ��ü ���룧���� ���������� �������̿����,�����ϴ� ���������� �׸�,���������� ���� �� �̿� �Ⱓ�� �Է��� �մϴ�.<br />
	- ���� : 3���� ������ �����̳� ����������޹�ħ ���� ���縦 ���� �ϰ� ���Ǵ� ���ݻ����Դϴ�.<br />
	- �߰��� �׸��� ȸ������ �� �����׸� �߰��˴ϴ�.<br />
	- ���� ���δ� ȸ�� CRM �������� Ȯ���Ͻ� �� �ֽ��ϴ�.<br />
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
		alert("�߰� ���� �׸��� �ִ� "+consentLimit+"�������� ����� �����մϴ�.");
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