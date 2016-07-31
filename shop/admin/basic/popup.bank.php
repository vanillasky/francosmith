<?
include "../_header.popup.php";

$title = ($_GET['mode']=="modBank") ? "결제은행수정" : "결제은행등록";

if ($_GET['mode']=="modBank"){
	if (!$_GET[sno]) msg("수정할 내용이 존재하지 않습니다",-1);
	$data = $db->fetch("select * from ".GD_LIST_BANK." where sno='$_GET[sno]'");
}
?>

<form action="indb.php" method=post onsubmit="return chkForm(this)">
<input type=hidden name=mode value="<?=$_GET[mode]?>">
<input type=hidden name=sno value="<?=$_GET[sno]?>">

<div class="title title_top"><?=$title?><span>결제은행을 등록/수정하세요</span></div>
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>은행명</td>
	<td><input type=text name=bank value="<?=$data[bank]?>" required fld_esssential class=line></td>
</tr>
<tr>
	<td>계좌번호</td>
	<td><input type=text name=account class=lline value="<?=$data[account]?>" required fld_esssential class=line></td>
</tr>
<tr>
	<td>예금주</td>
	<td><input type=text name=name value="<?=$data[name]?>" required fld_esssential class=line></td>
</tr>
</table>

<div style="padding:7px 0 0 0px" align=center><input type=image src="../img/btn_regist.gif" class=null></div>

</form>

<script>table_design_load();</script>