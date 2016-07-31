<? 
include "../../../lib/library.php";
include "../../../conf/config.php";
include "../../../conf/pg.$cfg[settlePg].php";
include "../../../conf/pg.escrow.php";

$ordno = $_GET[ordno];

$query = "
select 
	*  
from 
	".GD_ORDER." a
	left join ".GD_LIST_DELIVERY." b on a.deliveryno = b.deliveryno
where
	a.ordno = '$ordno'
";
$data = $db->fetch($query);
?>
<html>
<head>
<title>*** KCP Online Payment System [Escrow PHP Version] ***</title>
<link href="css/sample.css" rel="stylesheet" type="text/css">
<script language='javascript'>
function  jsf__go_mod( form )
{
    if ( form.mod_type.value != 'mod_type_not_sel' )
    {
        if ( form.tno.value.length < 14 )
        {
            alert( "KCP 거래 번호를 입력하세요" );
            form.tno.focus();
            form.tno.select();
        }
        else
        {
            openwin = window.open( 'proc_win.html', 'proc_win', 'width=449, height=209, top=300, left=300' );
            form.submit();
        }
    }
    else
    {
        alert( "거래 구분을 선택하여 주십시요." );
        form.mod_type.focus();
    }
}

function typeChk( form )
{
    if (form.mod_type.selectedIndex == 1)
    {
        type_STE2N4.style.display = "none";
        type_STE5.style.display = "none";
        type_STE1.style.display = "block";
    }
    else if (form.mod_type.selectedIndex == 2 || form.mod_type.selectedIndex == 4)
    {
        type_STE1.style.display = "none";
        type_STE5.style.display = "none";
        type_STE2N4.style.display = "block";
    }
    else if (form.mod_type.selectedIndex == 5)
    {
        type_STE1.style.display = "none";
        type_STE2N4.style.display = "none";
        type_STE5.style.display = "block";
    }
    else
    {
        type_STE1.style.display = "none";
        type_STE2N4.style.display = "none";
        type_STE5.style.display = "none";
    }
}

function selfDeliChk( form )
{
    if (form.self_deli_yn.checked)
    {
        form.deli_numb.value = "0000";
        form.deli_corp.value = "자가배송";
    }
    else
    {
        form.deli_numb.value = "";
        form.deli_corp.value = "";
    }
}

function acntUseChk( form )
{
    if (form.acnt_use_yn.checked)
    {
        type_RFND.style.display = "block";
        form.acnt_yn.value = "Y";
    }
    else
    {
        type_RFND.style.display = "none";
        form.acnt_yn.value = "N";
    }
}

</script>
<body>

<form name="mod_escrow_form" action="card_return.php" method="post">
<input type=hidden name=ordno value="<?=$ordno?>">
<input type='hidden' name='site_cd'  value='<?=$pg[id]?>' >
<input type='hidden' name='site_key' value="<?=$pg['key']?>">
<input type='hidden' name='req_tx'   value='mod_escrow'>
<input type='hidden' name='acnt_yn'  value='N'>

<table border='0' cellpadding='0' cellspacing='1' width='500' align='center'>
    <tr>
        <td align="left" height="25"><img src="./img/KcpLogo.jpg" border="0" width="65" height="50"></td>
        <td align='right' class="txt_main">KCP Online Payment System [ESCROW PHP Version]</td>
    </tr>
    <tr>
        <td bgcolor="CFCFCF" height='3' colspan='2'></td>
    </tr>
    <tr>
        <td colspan="2">
            <br>
            <table width="90%" align="center">
                <tr>
                    <td bgcolor="CFCFCF" height='2'></td>
                </tr>
                <tr>
                    <td align="center"> 에스크로 상태변경 요청 </td>
                </tr>
                <tr>
                    <td bgcolor="CFCFCF" height='2'></td>
                </tr>
            </table>
            <table width="90%" align="center">
                <tr>
                    <td>구분</td>
                    <td>
					<!--<select name="mod_type" onChange="javascript:typeChk(this.form);">
                            <option value="mod_type_not_sel" selected>선택하십시오</option>
                            <option value="STE1">배송시작</option>
                            <option value="STE2">즉시취소</option>
                            <option value="STE3">정산보류</option>
                            <option value="STE4">취소</option>
                            <option value="STE5">발급계좌해지</option>
                        </select>-->
						<select name="mod_type" onChange="javascript:typeChk(this.form);">
							<option value="mod_type_not_sel">선택하십시오</option>
							<option value="STE1" selected>배송시작</option>
						</select>
                    </td>
                </tr>
                <tr>
                    <td width="158">KCP 거래번호</td>
                    <td>
                        <input type='text' name='tno' value='<?=$data[escrowno]?>' size='20' maxlength='14'>
                    </td>
                </tr>
            </table>
            <span id="type_STE1" style="display:none">
            <table width="90%" align="center">
                <tr>
                    <td width="158">자가배송 여부</td>
                    <td>
                        자가배송의 경우 체크&nbsp;<input type='checkbox' name='self_deli_yn' onClick='selfDeliChk(this.form)'>
                    </td>
                </tr>
                <tr>
                    <td width="158">운송장 번호</td>
                    <td>
                        <input type='text' name='deli_numb' size='20' maxlength='25' value="<?=$data[deliverycode]?>">">
                    </td>
                </tr>
                <tr>
                    <td width="158">택배 업체명</td>
                    <td>
                        <input type='text' name='deli_corp' value='<?=$data[deliverycomp]?>' size='20' maxlength='25'>
                    </td>
                </tr>
            </table>
            </span>
            <span id="type_STE2N4" style="display:none">
            <table width="90%" align="center">
                <tr>
                    <td width="158">계좌이체, 가상계좌 거래</td>
                    <td>
                        계좌이체, 가상계좌 취소&nbsp;<input type='checkbox' name='acnt_use_yn' onClick='acntUseChk(this.form)'>
                    </td>
                </tr>
            </table>
            <div id="type_RFND" style="display:none">
            <table width="90%" align="center">
                <tr>
                    <td width="158">환불수취계좌번호</td>
                    <td>
                        <input type='text' name='refund_account' value='' size='23' maxlength='50'>
                    </td>
                </tr>
                <tr>
                    <td width="158">환불수취계좌주명</td>
                    <td>
                        <input type='text' name='refund_nm' value='' size='23' maxlength='50'>
                    </td>
                </tr>
                <tr>
                    <td width="158">환불수취은행코드</td>
                    <td>
                        <select name='bank_code'>
                            <option value="bank_code_not_sel" selected>선택</option>
                            <option value="39">경남은행</option>
                            <option value="03">기업은행</option>
                            <option value="32">부산은행</option>
                            <option value="07">수협중앙회</option>
                            <option value="48">신협</option>
                            <option value="71">우체국</option>
                            <option value="23">제일은행</option>
                            <option value="06">주택은행</option>
                            <option value="81">하나은행</option>
                            <option value="34">광주은행</option>
                            <option value="11">농협중앙회</option>
                            <option value="02">산업은행</option>
                            <option value="53">시티은행</option>
                            <option value="05">외환은행</option>
                            <option value="09">장기신용</option>
                            <option value="35">제주은행</option>
                            <option value="16">축협중앙회</option>
                            <option value="27">한미은행</option>
                            <option value="04">국민은행</option>
                            <option value="31">대구은행</option>
                            <option value="25">서울은행</option>
                            <option value="26">신한은행</option>
                            <option value="20">우리은행</option>
                            <option value="37">전북은행</option>
                            <option value="21">조흥은행</option>
                            <option value="83">평화은행</option>
                        </select>
                    </td>
                </tr>
            </table>
            </div>
            </span>
            <span id="type_STE5" style="display:none">
            <table width="90%" align="center">
                <tr>
                    <td colspan="2">발급계좌해지 요청은 가상계좌 결제에 대해서만 이용하시기 바랍니다.</td>
                </tr>
            </table>
            </span>
            <table width="90%" align="center">
                <tr>
                    <td colspan="2" align="center">
                        <input type="button" value="확 인" class="box" onclick='jsf__go_mod( this.form )'>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td bgcolor="CFCFCF" height='3' colspan='2'></td>
    </tr>
    <tr>
        <td colspan='2' align="center" height='25'>ⓒ Copyright 2006. KCP Inc.  All Rights Reserved.</td>
    </tr>
</table>

</form>
<script>
jsf__go_mod( document.mod_escrow_form );
</script>
</body>
</html>
