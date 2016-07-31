<? 
include "../../../lib/library.php";
include "../../../conf/config.php";
//include "../../../conf/pg.$cfg[settlePg].php";
include "../../../conf/pg.escrow.php";

// �����̼� ������� ��� PG ���� ��ü
resetPaymentGateway();

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
            alert( "KCP �ŷ� ��ȣ�� �Է��ϼ���" );
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
        alert( "�ŷ� ������ �����Ͽ� �ֽʽÿ�." );
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
        form.deli_corp.value = "�ڰ����";
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
                    <td align="center"> ����ũ�� ���º��� ��û </td>
                </tr>
                <tr>
                    <td bgcolor="CFCFCF" height='2'></td>
                </tr>
            </table>
            <table width="90%" align="center">
                <tr>
                    <td>����</td>
                    <td>
					<!--<select name="mod_type" onChange="javascript:typeChk(this.form);">
                            <option value="mod_type_not_sel" selected>�����Ͻʽÿ�</option>
                            <option value="STE1">��۽���</option>
                            <option value="STE2">������</option>
                            <option value="STE3">���꺸��</option>
                            <option value="STE4">���</option>
                            <option value="STE5">�߱ް�������</option>
                        </select>-->
						<select name="mod_type" onChange="javascript:typeChk(this.form);">
							<option value="mod_type_not_sel">�����Ͻʽÿ�</option>
							<option value="STE1" selected>��۽���</option>
						</select>
                    </td>
                </tr>
                <tr>
                    <td width="158">KCP �ŷ���ȣ</td>
                    <td>
                        <input type='text' name='tno' value='<?=$data[escrowno]?>' size='20' maxlength='14'>
                    </td>
                </tr>
            </table>
            <span id="type_STE1" style="display:none">
            <table width="90%" align="center">
                <tr>
                    <td width="158">�ڰ���� ����</td>
                    <td>
                        �ڰ������ ��� üũ&nbsp;<input type='checkbox' name='self_deli_yn' onClick='selfDeliChk(this.form)'>
                    </td>
                </tr>
                <tr>
                    <td width="158">����� ��ȣ</td>
                    <td>
                        <input type='text' name='deli_numb' size='20' maxlength='25' value="<?=$data[deliverycode]?>">">
                    </td>
                </tr>
                <tr>
                    <td width="158">�ù� ��ü��</td>
                    <td>
                        <input type='text' name='deli_corp' value='<?=$data[deliverycomp]?>' size='20' maxlength='25'>
                    </td>
                </tr>
            </table>
            </span>
            <span id="type_STE2N4" style="display:none">
            <table width="90%" align="center">
                <tr>
                    <td width="158">������ü, ������� �ŷ�</td>
                    <td>
                        ������ü, ������� ���&nbsp;<input type='checkbox' name='acnt_use_yn' onClick='acntUseChk(this.form)'>
                    </td>
                </tr>
            </table>
            <div id="type_RFND" style="display:none">
            <table width="90%" align="center">
                <tr>
                    <td width="158">ȯ�Ҽ�����¹�ȣ</td>
                    <td>
                        <input type='text' name='refund_account' value='' size='23' maxlength='50'>
                    </td>
                </tr>
                <tr>
                    <td width="158">ȯ�Ҽ�������ָ�</td>
                    <td>
                        <input type='text' name='refund_nm' value='' size='23' maxlength='50'>
                    </td>
                </tr>
                <tr>
                    <td width="158">ȯ�Ҽ��������ڵ�</td>
                    <td>
                        <select name='bank_code'>
                            <option value="bank_code_not_sel" selected>����</option>
                            <option value="39">�泲����</option>
                            <option value="03">�������</option>
                            <option value="32">�λ�����</option>
                            <option value="07">�����߾�ȸ</option>
                            <option value="48">����</option>
                            <option value="71">��ü��</option>
                            <option value="23">��������</option>
                            <option value="06">��������</option>
                            <option value="81">�ϳ�����</option>
                            <option value="34">��������</option>
                            <option value="11">�����߾�ȸ</option>
                            <option value="02">�������</option>
                            <option value="53">��Ƽ����</option>
                            <option value="05">��ȯ����</option>
                            <option value="09">���ſ�</option>
                            <option value="35">��������</option>
                            <option value="16">�����߾�ȸ</option>
                            <option value="27">�ѹ�����</option>
                            <option value="04">��������</option>
                            <option value="31">�뱸����</option>
                            <option value="25">��������</option>
                            <option value="26">��������</option>
                            <option value="20">�츮����</option>
                            <option value="37">��������</option>
                            <option value="21">��������</option>
                            <option value="83">��ȭ����</option>
                        </select>
                    </td>
                </tr>
            </table>
            </div>
            </span>
            <span id="type_STE5" style="display:none">
            <table width="90%" align="center">
                <tr>
                    <td colspan="2">�߱ް������� ��û�� ������� ������ ���ؼ��� �̿��Ͻñ� �ٶ��ϴ�.</td>
                </tr>
            </table>
            </span>
            <table width="90%" align="center">
                <tr>
                    <td colspan="2" align="center">
                        <input type="button" value="Ȯ ��" class="box" onclick='jsf__go_mod( this.form )'>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td bgcolor="CFCFCF" height='3' colspan='2'></td>
    </tr>
    <tr>
        <td colspan='2' align="center" height='25'>�� Copyright 2006. KCP Inc.  All Rights Reserved.</td>
    </tr>
</table>

</form>
<script>
jsf__go_mod( document.mod_escrow_form );
</script>
</body>
</html>
