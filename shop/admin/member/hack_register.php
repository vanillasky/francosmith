<?

include "../_header.popup.php";

if ($_GET[mode]=="modify"){
	$data = $db->fetch("select * from ".GD_LOG_HACK." where sno='" . $_GET['sno'] . "'",1);

	$checked = array();
	$checked[itemcd] = array();
	foreach( codeitem('hack') as $k => $v ){
		if ($data[itemcd]&pow(2,$k)) $checked[itemcd][] = $v;
	}
}
?>

<form name=form method=post action="hack_indb.php" onsubmit="return chkForm(this)">
<input type=hidden name=mode value="<?=$_GET[mode]?>">
<input type=hidden name=sno value="<?=$_GET[sno]?>">

<div class="title title_top">회원탈퇴내역 상세내용<span></span></div>

<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>아이디</td>
	<td><font class=ver8><?=$data[m_id]?></font></td>
</tr>
<tr>
	<td>이름</td>
	<td><font class=ver8><?=$data[name]?></font></td>
</tr>
<tr>
	<td>처리형태</td>
	<td><font class=extext><b><?=($data[actor] == '1' ? '본인탈퇴' : '강제삭제')?></b></font></td>
</tr>
<tr>
	<td>탈퇴일</td>
	<td><font class=ver8><?=$data[regdt]?> &nbsp;&nbsp;(<?=$data[ip]?>)</td>
</tr>
<tr>
	<td>불편사항</td>
	<td>
	<font class=small>
	<ol style="margin-left:23;margin-bottom:5;margin-top:10;">
	<? foreach( $checked[itemcd] as $k => $v ){?>
		<li><?=$v?></li>
	<? } ?>
	</ol>
	</td>
</tr>
<tr>
	<td>충고말씀</td>
	<td><textarea name="reason" cols=60 rows=6 style="width:90%;" class=tline><?=$data['reason']?></textarea></td>
</tr>
<tr>
	<td>관리메모</td>
	<td><textarea name="adminMemo" cols=60 rows=5 style="width:90%;" class=tline><?=$data['adminMemo']?></textarea></td>
</tr>
</table>

<div class="button_popup">
<input type=image src="../img/btn_confirm_s.gif">
<a href="javascript:parent.closeLayer()"><img src="../img/btn_cancel_s.gif"></a>
</div>

</form>

<script>table_design_load();</script>