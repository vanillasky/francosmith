<?php /* Template_ 2.2.7 2014/03/05 23:19:40 /www/francotr3287_godo_co_kr/shop/data/skin/campingyo/order/cash_receipt/lgdacom.htm 000004994 */ ?>
<!-- eCredit.js에서 제공하는 현금영수증출력함수(showCashReceipts) 사용 -->
<script language="JavaScript" src="http://pgweb.dacom.net/WEB_SERVER/js/receipt_link.js"></script>
<script language="javascript">
function chkreceiptFrm(fobj)
{
	var certNo = fobj.ssn.value;
	if (fobj.usertype[0].checked)
	{
		if (certNo.length != 10 && certNo.length != 11)
		{
			alert("휴대폰번호를 정확히 입력해 주시기 바랍니다.");
			fobj.ssn.focus();
			return false;
		}
		else if ((certNo.length == 11 ||certNo.length == 10) &&  certNo.substring(0,2) != "01" )
		{
			alert("휴대폰 번호에 오류가 있습니다. 다시 확인 하십시오. ");
			fobj.ssn.focus();
			return false;
		}
	}
	else if (fobj.usertype[1].checked)
	{
		if (certNo.length != 10)
		{
			alert("사업자번호를 정확히 입력해 주시기 바랍니다.");
			fobj.ssn.focus();
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
			fobj.ssn.focus();
			return false;
		}
	}

	var chr;
	for (var i=0; i < certNo.length; i++){
		chr = certNo.substr(i, 1);
		if( chr < '0' || chr > '9') {
			alert("숫자가 아닌 문자가 추가되어 있습니다. 다시 확인 하십시오.");
			fobj.ssn.focus();
			return false;
		}
	}

	if(confirm("현금영수증을 발행하시겠습니까?") === false) return false;

	return true;
}

function  display_cert(robj)
{
	if (robj.checked && robj.value == "1")
	{
		_ID('cert_0').style.display = "block";
		_ID('cert_1').style.display = "none";
	}
	else if (robj.checked && robj.value == "2")
	{
		_ID('cert_0').style.display = "none";
		_ID('cert_1').style.display = "block";
	}
}
</script>
<table width="100%" style="border:1px solid #DEDEDE" cellpadding="0" cellspacing="0">
<tr>
	<td width="150" valign="top" align="right" bgcolor="#F3F3F3"></td>
	<td id="orderbox">

	<table>
	<col width="100">
	<tr>
		<td>현금영수증</td>
		<td>
<?php if($TPL_VAR["cashreceipt"]&&$TPL_VAR["settlekind"]=="o"){?><!-- 계좌이체 -->
		<a href="javascript:showCashReceipts('<?php echo $GLOBALS["pg"]["id"]?>','<?php echo $TPL_VAR["ordno"]?>','001','BANK','service')">현금영수증출력</a>

<?php }elseif($TPL_VAR["cashreceipt"]&&$TPL_VAR["settlekind"]=="v"){?><!-- 무통장입금(가상계좌) -->
		<a href="javascript:showCashReceipts('<?php echo $GLOBALS["pg"]["id"]?>','<?php echo $TPL_VAR["ordno"]?>','001','CAS','service')">현금영수증출력</a>

<?php }elseif($TPL_VAR["cashreceipt"]&&$TPL_VAR["settlekind"]=="a"){?><!-- 자체 무통장입금 -->
		<a href="javascript:showCashReceipts('<?php echo $GLOBALS["pg"]["id"]?>','<?php echo $TPL_VAR["ordno"]?>','001','CR','service')">현금영수증출력</a>

<?php }elseif($TPL_VAR["cashreceipt"]==''&&$TPL_VAR["step"]== 0){?>
		입금하셔야 현금영수증을 발급하실 수 있습니다.

<?php }elseif($TPL_VAR["cashreceipt"]==''&&$TPL_VAR["step2"]){?>
		취소중이거나 취소된 주문은 현금영수증을 발급하실 수 없습니다.

<?php }elseif($TPL_VAR["cashreceipt"]==''&&$TPL_VAR["step"]&&!$TPL_VAR["step2"]&&$GLOBALS["set"]["receipt"]["period"]&&$TPL_VAR["orddt"]&&(strtotime($TPL_VAR["orddt"])+( 86400*$GLOBALS["set"]["receipt"]["period"]))<time()){?>
		주문일로부터 <?php echo $GLOBALS["set"]["receipt"]["period"]?>일이 경과하여 발행할 수 없습니다. (<?php echo date('y-m-d H:i',(strtotime($TPL_VAR["orddt"])+( 86400*$GLOBALS["set"]["receipt"]["period"])))?>)

<?php }elseif($TPL_VAR["cashreceipt"]==''&&$TPL_VAR["step"]&&!$TPL_VAR["step2"]){?>
		<form name="receiptFrm" method="post" action="<?php echo url("order/card/lgdacom/CashReceipt.php")?>&" onSubmit="return chkreceiptFrm(this)" target="ifrmHidden">
		<input type="hidden" name="ordno" value="<?php echo $TPL_VAR["ordno"]?>">
		<input type="hidden" name="method" value="auth">

		<table>
		<tr>
			<td width="100">발행용도</td>
			<td>
			<input type="radio" name="usertype" value="1" onClick="display_cert(this)" checked>소득공제용
			<input type="radio" name="usertype" value="2" onClick="display_cert(this)">지출증빙용
			</td>
		</tr>
		<tr>
			<td>
			<span id="cert_0" style="display:block;">휴대폰번호</span>
			<span id="cert_1" style="display:none;">사업자번호</span>
			</td>
			<td><input type="text" name="ssn" value="<?php echo str_replace('-','',$TPL_VAR["mobileOrder"])?>" class="line"> <span class="small">("-" 생략)</span></td>
		</tr>
		</table>
		</form>
		<input type="button" value="현금영수증발급요청" name="app_btn" onClick="javascript:if (chkreceiptFrm(document.receiptFrm)) document.receiptFrm.submit();">
<?php }?>
	</table>

	</td>
</tr>
</table><div style="font-size:0;height:5px"></div>