<?php
$location = '�⺻���� > ���/����������޹�ħ ����';
include '../_header.php';
@include '../../conf/terms/terms.config.php';

$checked['private2YN'][$cfg['private2YN']] = 'checked';
$checked['private3YN'][$cfg['private3YN']] = 'checked';

$termsFilePath = dirname(__FILE__) . '/../../conf/terms/';
$termsTitleArr = array(
	1 => '���θ� �̿���',
	2 => '����������޹�ħ',
	3 => '������������ ��<br />�̿뵿��(ȸ������)',
	4 => '������������ ��<br />�̿뵿��(��ȸ��)',
	5 => '������������ ��<br />�̿뵿��(����)',
	6 => '�������� ��3�� ����<br />(ȸ������)',
	7 => '�������� �����Ź<br />(ȸ������)',
	8 => '�߰� ���� �׸�<br />(ȸ������)'
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

<div class="title title_top">���/�������� ����<span>���θ��� �̿��� �� ����������޹�ħ �������� ǥ�õ˴ϴ�.
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
			<td>��</td>
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
 * �߰������׸��Է½� �ʼ��׸� üũ (`��뿩��`�� `���`���� ���ְ� ���� ������ ������)
 * 
 * ���� hasConsentSno�� _terms.addConsent.php Line.96���� ����, ���� �߰��� ���� �׸��� ����
 */
function chkTermsForm()
{
	if (!hasConsentSno) return true;
	else {
		for(var i=0; i<hasConsentSno; i++){
			if (document.getElementsByName('useyn['+i+']')[0].checked === true && !document.getElementsByName('termsContent['+i+']')[0].value){
				alert("�߰� ���� �׸��� ������ �Է����ּ���.");
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