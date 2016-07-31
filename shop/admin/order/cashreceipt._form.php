<?

include '../../conf/config.php';
include '../../conf/config.pay.php';
if ($cfg['settlePg'] !== '' && file_exists('../../conf/pg.'. $cfg['settlePg'] .'.php')){
	include '../../conf/pg.'. $cfg['settlePg'] .'.php';
}

$pgs = array('inicis' => 'KG�̴Ͻý�', 'inipay' => 'KG�̴Ͻý�', 'allat' => '�Ｚ�þ�', 'allatbasic' => '�Ｚ�þ�', 'dacom' => 'LG U+', 'lgdacom' => 'LG U+', 'kcp'=>'KCP', 'agspay'=>'�ô�����Ʈ', 'easypay'=>'��������', 'settlebank'=>'��Ʋ��ũ');
$pgCompany = $pgs[ $cfg['settlePg'] ];
if ($pgCompany == '') $pgCompany = strtoupper($cfg['settlePg']);

if ($set['receipt']['compType'] == '') $set['receipt']['compType'] = '0';
$checked['compType'][$set['receipt']['compType']] = 'checked';

$required['buyeremail'] = ($cfg['settlePg'] == 'kcp' ? 'required' : '');
$required['buyerphone'] = ($cfg['settlePg'] == 'kcp' ? 'required' : '');

if ($_GET['ordno'])
{
	include '../../lib/cashreceipt.class.php';
	$cashreceipt = new cashreceipt();
	$indata = $cashreceipt->getOrder($_GET['ordno']);

	$indata['phone'] = explode('-',$indata['mobileOrder']);
	if ($required['buyeremail'] != 'required') unset($indata['buyeremail']);
	if ($required['buyerphone'] != 'required') unset($indata['phone']);
}

?>

<form method="post" action="../order/cashreceipt.indb.php" onsubmit="return chkForm2(this)" pg="<?=$cfg['settlePg']?>" receipt="<?=$pg['receipt']?>">
<input type="hidden" name="mode" value="put">
<input type="hidden" name="ordno" value="<?=$_GET['ordno']?>">

<table class="tb">
<col class="cellC"><col class="cellL"><col class="cellC"><col class="cellL">
<tr>
	<td>��������(PG)</td>
	<td colspan="3"><?=($pgCompany != '' ? $pgCompany : '��������(PG)�� ���� ��û/�����ϼ���.')?> &nbsp; <span class="small4">(���ݿ����� <?=($pg['receipt'] == 'Y' ? '�����' : '�߱޾���')?>)</span></td>
</tr>
<? if($_GET['ordno']){ ?>
<tr>
	<td>�ֹ���ȣ</td>
	<td colspan="3"><?=$_GET['ordno']?></td>
</tr>
<? } ?>
<tr>
	<td>�ֹ��ڸ�</td>
	<td width="20%"><input type="text" name="buyername" value="<?=$indata['buyername']?>" style="width:100%; height:30px; font-size:25px;" required label="�ֹ��ڸ�"></td>
	<td>����ó<br>(KCP �ʼ�����)</td>
	<td>
		&#149; �̸��� <font color="white">---</font> <input type="text" name="buyeremail" value="<?=$indata['buyeremail']?>" style="width:156px" <?=$required['buyeremail']?> label="�̸���" option="regEmail"><br>
		&#149; ��ȭ��ȣ&nbsp;
		<input type="text" name="buyerphone[]" size="4" maxlength="4" value="<?=$indata['phone'][0]?>" <?=$required['buyerphone']?> label="��ȭ��ȣ" option="regNum"> -
		<input type="text" name="buyerphone[]" size="4" maxlength="4" value="<?=$indata['phone'][1]?>" <?=$required['buyerphone']?> label="��ȭ��ȣ" option="regNum"> -
		<input type="text" name="buyerphone[]" size="4" maxlength="4" value="<?=$indata['phone'][2]?>" <?=$required['buyerphone']?> label="��ȭ��ȣ" option="regNum">
	</td>
</tr>
<tr>
	<td>��ǰ��</td>
	<td colspan="3">
		<input type="text" name="goodsnm" value="<?=$indata['goodsnm']?>" style="width:300px;" required label="��ǰ��" onkeyup="chkLen(this)" onchange="chkLen(this)">
		<font class="small" color="#555555"><span id="vLength" style="color:#e65100;">0</span>/30 Bytes</font>
	</td>
</tr>
<tr>
	<td>��ǰ����</td>
	<td colspan="3" style="padding:5px;">
	<div>
	<input type="radio" name="compType" value="0" class="null" <?=$checked['compType']['0']?> onclick="autoPrice(this.form.amount)"> ����
	<span class="small4" style="color:#6d6d6d">(�ǸŹ�ǰ�� �ΰ����� ����)</span>
	<input type="radio" name="compType" value="1" class="null" <?=$checked['compType']['1']?> onclick="autoPrice(this.form.amount)"> �鼼
	<span class="small4" style="color:#6d6d6d">(�ǸŹ�ǰ�� �ΰ����� ����)</span>
	</div>

	����� : <input type="text" name="amount" value="<?=$indata['amount']?>" size="10" required label="�����" option="regNum" onchange="autoPrice(this)" onblur="autoPrice(this)" onkeyup="autoPrice(this)"><br>
	���޾� : <input type="text" name="supply" value="<?=$indata['supply']?>" size="10" readonly><br>
	�ΰ��� : <input type="text" name="surtax" value="<?=$indata['surtax']?>" size="10" readonly>
	</td>
</tr>
<tr>
	<td>����뵵</td>
	<td colspan="3" style="padding:5px;">
	<input type="radio" name="useopt" value="0" onclick="setUseopt()" class="null" checked> ���μҵ������ <font color="white">----------</font>
	<input type="radio" name="useopt" value="1" onclick="setUseopt()" class="null"> ���������������
	<div style="border:solid 1px #dddddd; width:300px; padding:5px; background-color:#F6F6F6; margin-top:5px;">
		<div style="float:left;padding-top:3px;">
			<span id="cert_0" style="display:block;">�޴�����ȣ</span>
			<span id="cert_1" style="display:none;">����ڹ�ȣ<font color="white">--------</font></span>
		</div>
		<input type="text" name="certno" value="<?=$indata['buyerphone']?>" required label="�޴�����ȣ" option="regNum"> <span class="small">("-" ����)</span>
	</div>
	</td>
</tr>
</table>

<? if($_GET['ordno']){ ?>
<div class="button" style="margin:15px;">
<input type="image" src="../img/btn_confirm_s.gif">
</div>
<? } else { ?>
<div class="button">
<input type="image" src="../img/btn_confirm.gif">
<a href="../order/cashreceipt.list.php"><img src="../img/btn_cancel.gif"></a>
</div>
<? } ?>

</form>


<script language="javascript">
<!--
function chkLen(obj)
{
	str = obj.value;
	if (chkByte(str)>30){
		alert("30byte������ �Է��� �����մϴ�");
		obj.value = strCut(str,30);
	}
	_ID('vLength').innerHTML = chkByte(obj.value);
}
chkLen(document.getElementsByName('goodsnm')[0]);

function autoPrice(price) // ���޾�,�ΰ��� ���
{
	if (!price.value) price.value = 0;
	if (document.getElementsByName('compType')[1].checked) // �鼼/���̻����
	{
		var supply	= price.value;
		var surtax	= 0;
	}
	else { // ���������
		var supply	= Math.round( price.value / 1.1 );
		var surtax	= price.value - supply;
	}

	document.getElementsByName('supply')[0].value = supply;
	document.getElementsByName('surtax')[0].value = surtax;
}

function chkForm2(fobj)
{
	if (fobj.getAttribute('pg') == ''){
		alert('"���θ��⺻����" ���� ��������(PG)�� ���� ��û/�����ϼ���');
		return false;
	}
	if (fobj.getAttribute('receipt') != 'Y'){
		alert('"���ݿ����� ���༳��" ���� ���ݿ����� ��뿩�θ� ���� �����ϼ���.');
		return false;
	}

	if (chkForm(fobj) === false) return false;

	var certNo = fobj.certno.value;
	if (fobj.useopt[0].checked)
	{
		if (certNo.length != 10 && certNo.length != 11 )
		{
			alert("�޴�����ȣ�� ��Ȯ�� �Է��� �ֽñ� �ٶ��ϴ�.");
			fobj.certno.focus();
			return false;
		}
		if ((certNo.length == 11 ||certNo.length == 10) &&  certNo.substring(0,2) != "01" )
		{
			alert("�޴��� ��ȣ�� ������ �ֽ��ϴ�. �ٽ� Ȯ�� �Ͻʽÿ�. ");
			fobj.certno.focus();
			return false;
		}
	}
	else if (fobj.useopt[1].checked)
	{
		if (certNo.length != 10)
		{
			alert("����ڹ�ȣ�� ��Ȯ�� �Է��� �ֽñ� �ٶ��ϴ�.");
			fobj.certno.focus();
			return false;
		}
		var sum = 0;
		var getlist = new Array(10);
		var chkvalue = new Array("1","3","7","1","3","7","1","3","5");
		for (var i=0; i<10; i++) { getlist[i] = certNo.substring(i, i+1); }
		for (var i=0; i<9; i++) { sum += getlist[i]*chkvalue[i]; }
		sum = sum + parseInt((getlist[8]*5)/10);
		sidliy = sum % 10;
		sidchk = 0;
		if (sidliy != 0) { sidchk = 10 - sidliy; }
		else { sidchk = 0; }
		if (sidchk != getlist[9]) {
			alert("����ڵ�Ϲ�ȣ�� ������ �ֽ��ϴ�. �ٽ� Ȯ���Ͻʽÿ�.");
			fobj.certno.focus();
			return false;
		}
	}
	return true;
}

function setUseopt()
{
	var useopt = document.getElementsByName('useopt');
	if (useopt[0].checked)
	{
		_ID('cert_0').style.display = "block";
		_ID('cert_1').style.display = "none";
		useopt[0].form.certno.setAttribute('label', '�޴�����ȣ');
	}
	else if (useopt[1].checked)
	{
		_ID('cert_0').style.display = "none";
		_ID('cert_1').style.display = "block";
		useopt[1].form.certno.setAttribute('label', '����ڹ�ȣ');
	}
}
//-->
</script>