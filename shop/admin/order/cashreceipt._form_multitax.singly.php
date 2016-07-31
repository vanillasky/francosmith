<?

include '../../conf/config.php';
include '../../conf/config.pay.php';
if ($cfg['settlePg'] !== '' && file_exists('../../conf/pg.'. $cfg['settlePg'] .'.php')){
	include '../../conf/pg.'. $cfg['settlePg'] .'.php';
}

$pgs = array('inicis' => 'KG이니시스', 'inipay' => 'KG이니시스', 'allat' => '삼성올앳', 'allatbasic' => '삼성올앳', 'dacom' => 'LG U+', 'lgdacom' => 'LG U+', 'kcp'=>'KCP', 'agspay'=>'올더게이트', 'easypay'=>'이지페이', 'settlebank'=>'세틀뱅크');
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
<input type="hidden" name="amount" value="">
<input type="hidden" name="supply" value="">
<input type="hidden" name="tax" value="">
<input type="hidden" name="taxfree" value="">
<input type="hidden" name="surtax" value="">

<table class="tb">
<col class="cellC"><col class="cellL"><col class="cellC"><col class="cellL">
<tr>
	<td>전자지불(PG)</td>
	<td colspan="3"><?=($pgCompany != '' ? $pgCompany : '전자지불(PG)를 먼저 신청/설정하세요.')?> &nbsp; <span class="small4">(현금영수증 <?=($pg['receipt'] == 'Y' ? '사용중' : '발급안함')?>)</span></td>
</tr>
<? if($_GET['ordno']){ ?>
<tr>
	<td>주문번호</td>
	<td colspan="3"><?=$_GET['ordno']?></td>
</tr>
<? } ?>
<tr>
	<td>주문자명</td>
	<td width="20%"><input type="text" name="buyername" value="<?=$indata['buyername']?>" style="width:100%; height:30px; font-size:25px;" required label="주문자명"></td>
	<td>연락처<br>(KCP 필수정보)</td>
	<td>
		&#149; 이메일 <font color="white">---</font> <input type="text" name="buyeremail" value="<?=$indata['buyeremail']?>" style="width:156px" <?=$required['buyeremail']?> label="이메일" option="regEmail"><br>
		&#149; 전화번호&nbsp;
		<input type="text" name="buyerphone[]" size="4" maxlength="4" value="<?=$indata['phone'][0]?>" <?=$required['buyerphone']?> label="전화번호" option="regNum"> -
		<input type="text" name="buyerphone[]" size="4" maxlength="4" value="<?=$indata['phone'][1]?>" <?=$required['buyerphone']?> label="전화번호" option="regNum"> -
		<input type="text" name="buyerphone[]" size="4" maxlength="4" value="<?=$indata['phone'][2]?>" <?=$required['buyerphone']?> label="전화번호" option="regNum">
	</td>
</tr>
<tr>
	<td>상품명</td>
	<td colspan="3">
		<input type="text" name="goodsnm" value="<?=$indata['goodsnm']?>" style="width:300px;" required label="상품명" onkeyup="chkLen(this)" onchange="chkLen(this)">
		<font class="small" color="#555555"><span id="vLength" style="color:#e65100;">0</span>/30 Bytes</font>
	</td>
</tr>
<tr>
	<td>상품가격</td>
	<td colspan="3" style="padding:5px;">
	<div>
	<input type="radio" name="compType" value="0" class="null" <?=$checked['compType']['0']?> onclick="autoPrice()"> 과세
	<span class="small4" style="color:#6d6d6d">(판매물품에 부가세가 있음)</span>
	<input type="radio" name="compType" value="1" class="null" <?=$checked['compType']['1']?> onclick="autoPrice()"> 면세
	<span class="small4" style="color:#6d6d6d">(판매물품에 부가세가 없음)</span>
	<input type="radio" name="compType" value="2" class="null" <?=$checked['compType']['2']?> onclick="autoPrice()"> 과세 + 면세
	<span class="small4" style="color:#6d6d6d">(반드시 계약 PG사에 별도 신청한 경우에만 사용)</span>
	</div>
	<div id="single_tax">
	발행액 : <input type="text" name="tamount" value="0" size="10" required label="발행액" option="regNum" onchange="autoPrice()" onblur="autoPrice()" onkeyup="autoPrice()"><br>
	공급액 : <input type="text" name="tsupply" value="0" size="10" readonly><br>
	부가세 : <input type="text" name="tsurtax" value="0" size="10" readonly>
	</div>
	<div id="multi_tax" style="display:none">
	발행액 : <input type="text" name="mamount" value="0" style="text-align: right" size="10" required label="발행액" option="regNum" onchange="autoPrice()" onblur="autoPrice()" onkeyup="autoPrice()"> 원<br>
	공급액 : <input type="text" name="msupply" value="0" style="text-align: right" size="10" readonly> 원
	( 과세액 : <input type="text" name="mtax" value="0" style="text-align: right" size="6" readonly> 원
	+ 면세액 : <input type="text" name="mtaxfree" value="0" style="text-align: right" size="6" required label="면세액" option="regNum" onchange="autoPrice()" onblur="autoPrice()" onkeyup="autoPrice()"> 원 )<br>
	부가세 : <input type="text" name="msurtax" value="0" style="text-align: right" size="10" readonly> 원
	</div>
	</td>
</tr>
<tr>
	<td>발행용도</td>
	<td colspan="3" style="padding:5px;">
	<input type="radio" name="useopt" value="0" onclick="setUseopt()" class="null" checked> 개인소득공제용 <font color="white">----------</font>
	<input type="radio" name="useopt" value="1" onclick="setUseopt()" class="null"> 사업자지출증빙용
	<div style="border:solid 1px #dddddd; width:300px; padding:5px; background-color:#F6F6F6; margin-top:5px;">
		<div style="float:left;padding-top:3px;">
			<span id="cert_0" style="display:block;">휴대폰번호</span>
			<span id="cert_1" style="display:none;">사업자번호<font color="white">--------</font></span>
		</div>
		<input type="text" name="certno" value="<?=$indata['buyerphone']?>" required label="휴대폰번호" option="regNum"> <span class="small">("-" 생략)</span>
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
		alert("30byte까지만 입력이 가능합니다");
		obj.value = strCut(str,30);
	}
	_ID('vLength').innerHTML = chkByte(obj.value);
}
chkLen(document.getElementsByName('goodsnm')[0]);

function autoPrice() // 공급가액,부가세 계산
{
	var amount = 0;		// 금액
	var tax = 0;		// 과세
	var taxfree = 0;	// 면세
	var supply = 0;		// 공급가액
	var surtax = 0;		// 부가세
	var taxtype = '';	// 과세,면세 / 복합과세 구분
	if (document.getElementsByName('compType')[0].checked || document.getElementsByName('compType')[1].checked){ // 과세,면세
		taxtype = 't';
		// 과세, 면세 html display
		document.getElementById("single_tax").style.display = '';
		document.getElementById("multi_tax").style.display = 'none';

		amount = parseInt(document.getElementsByName(taxtype+'amount')[0].value,10);
		if (document.getElementsByName('compType')[0].checked){ // 과세
			supply	= Math.round( amount / 1.1 );
			surtax	= amount - supply;
		} else { // 면세
			supply	= amount;
			taxfree	= amount;
			surtax	= 0;
		}
	} else { // 복합과세
		taxtype = 'm';
		// 복합과세 html display
		document.getElementById("single_tax").style.display = 'none';
		document.getElementById("multi_tax").style.display = '';

		if(!document.getElementsByName(taxtype+'amount')[0].value){
			document.getElementsByName(taxtype+'amount')[0].value = 0;
		}
		if(!document.getElementsByName(taxtype+'taxfree')[0].value){
			document.getElementsByName(taxtype+'taxfree')[0].value = 0;
		}

		amount = parseInt(document.getElementsByName(taxtype+'amount')[0].value,10);
		taxfree = parseInt(document.getElementsByName(taxtype+'taxfree')[0].value,10);
		tax = amount - taxfree;
		tax = Math.ceil( tax / 1.1 );
		supply	= tax + taxfree;
		surtax	= amount - supply;
		document.getElementsByName(taxtype+'tax')[0].value = tax;
		document.getElementsByName(taxtype+'taxfree')[0].value = taxfree;
	}
	document.getElementsByName(taxtype+'supply')[0].value = supply;
	document.getElementsByName(taxtype+'surtax')[0].value = surtax;

	document.getElementsByName('amount')[0].value = amount;
	document.getElementsByName('tax')[0].value = tax;
	document.getElementsByName('taxfree')[0].value = taxfree;
	document.getElementsByName('supply')[0].value = supply;
	document.getElementsByName('surtax')[0].value = surtax;
}

function chkForm2(fobj)
{
	if (fobj.getAttribute('pg') == ''){
		alert('"쇼핑몰기본관리" 에서 전자지불(PG)를 먼저 신청/설정하세요');
		return false;
	}
	if (fobj.getAttribute('receipt') != 'Y'){
		alert('"현금영수증 발행설정" 에서 현금영수증 사용여부를 먼저 설정하세요.');
		return false;
	}

	if (chkForm(fobj) === false) return false;

	var certNo = fobj.certno.value;
	if (fobj.useopt[0].checked)
	{
		if (certNo.length != 10 && certNo.length != 11 )
		{
			alert("휴대폰번호를 정확히 입력해 주시기 바랍니다.");
			fobj.certno.focus();
			return false;
		}
		if ((certNo.length == 11 ||certNo.length == 10) &&  certNo.substring(0,2) != "01" )
		{
			alert("휴대폰 번호에 오류가 있습니다. 다시 확인 하십시오. ");
			fobj.certno.focus();
			return false;
		}
	}
	else if (fobj.useopt[1].checked)
	{
		if (certNo.length != 10)
		{
			alert("사업자번호를 정확히 입력해 주시기 바랍니다.");
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
			alert("사업자등록번호에 오류가 있습니다. 다시 확인하십시오.");
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
		useopt[0].form.certno.setAttribute('label', '휴대폰번호');
	}
	else if (useopt[1].checked)
	{
		_ID('cert_0').style.display = "none";
		_ID('cert_1').style.display = "block";
		useopt[1].form.certno.setAttribute('label', '사업자번호');
	}
}
//-->
</script>