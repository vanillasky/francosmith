<?

include "../_header.popup.php";

if ($_GET['mode']=="modify"){
	$data = $db->fetch("select * from ".GD_CODE." where sno='" . $_GET['sno'] . "'",1);
}
else {
	$data['groupcd'] = $_GET['groupcd'];
}

list( $groupnm ) = $db->fetch("SELECT itemnm FROM ".GD_CODE." WHERE groupcd='' and itemcd='" . $data['groupcd'] . "'"); # 코드분류명
?>

<form name="form" method="post" action="data_code_indb.php" onsubmit="return chkForm(this);">
<input type="hidden" name="mode" value="<?=$_GET['mode']?>" />
<input type="hidden" name="sno" value="<?=$_GET['sno']?>" />
<input type="hidden" name="groupcd" value="<?=$data['groupcd']?>" />

<div class="title title_top">코드 <?=( $_GET['mode'] == "modify" ? '수정' : '등록' )?><span></span></div>

<table class="tb">
<col class="cellC"><col class="cellL">
<tr height="26">
	<td>분류</td>
	<td><?=$groupnm?></td>
</tr>
<tr>
	<td>코드번호</td>
	<td><input type="text" name="itemcd" size="5"  maxlength="2" value="<?echo( $data['itemcd'] )?>" onKeyDown="onlynumber();"> 2자리</td>
</tr>
<tr>
	<td>코드명</td>
	<td><input type="text" name="itemnm" size="60"  maxlength="30" value="<?echo( $data['itemnm'] )?>"> 30자리</td>
</tr>
</table>

<div class="button_popup">
<input type="image" src="../img/btn_confirm_s.gif" />
<a href="javascript:parent.closeLayer()"><img src="../img/btn_cancel_s.gif" /></a>
</div>

</form>

<script>table_design_load();</script>