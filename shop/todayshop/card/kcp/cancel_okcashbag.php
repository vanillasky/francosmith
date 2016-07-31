<?
include "../../../admin/lib.php";
include "../../../conf/config.php";
@include "../../../conf/pg.cashbag.php";
if( $cashbag['usesettle'] == "on" && $cashbag['code'] && $cashbag['key'] ){
?>
<html>
<head>
<title>*** KCP Online Payment System [PHP Version] ***</title>
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

</script>
<body>

<form name="mod_form" action="card_return.php" method="post">

<input type='hidden' name='site_cd'  value='<?=$cashbag['code']?>' >
<input type='hidden' name='site_key' value='<?=$cashbag['key']?>'>
<input type='hidden' name='req_tx'   value='mod'>

<table border='0' cellpadding='0' cellspacing='1' width='500' align='center'>
    <tr>
        <td align="left" height="25"><img src="./img/KcpLogo.jpg" border="0" width="65" height="50"></td>
        <td align='right' class="txt_main">KCP Online Payment System [PHP Version]</td>
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
                    <td align="center"> 취소/매입 요청 </td>
                </tr>
                <tr>
                    <td bgcolor="CFCFCF" height='2'></td>
                </tr>
            </table>
            <table width="90%" align="center">
                <tr>
                    <td>구분</td>
                    <td>
                        <select name='mod_type'>
                            <option value='STSC'>취소</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>KCP 거래번호</td>
                    <td>
                        <input type='text' name='tno' size='20' maxlength='14' value="<?=$_GET['tno']?>">
                    </td>
                </tr>
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
</body>
</html>
<?
}else{
	msg('캐쉬백 미사용중',0);
	echo("<script>self.close();</script>");
}
?>