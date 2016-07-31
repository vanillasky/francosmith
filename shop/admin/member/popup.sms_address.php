<?

include "../_header.popup.php";

### 그룹명 가져오기
$query = "SELECT sms_grp FROM ".GD_SMS_ADDRESS." GROUP BY sms_grp ORDER BY sms_grp ASC";
$res = $db->query($query);
while ($data=$db->fetch($res)) $r_grp[] = $data['sms_grp'];

if(!$_GET['mode']){
	$data = $db->fetch("SELECT * FROM ".GD_SMS_ADDRESS." WHERE sno='".$_GET['sno']."'");
	extract($data);
	$sms_mobile	= explode("-",$sms_mobile);
	$selected['grp'][$sms_grp]	= "selected";
	$checked['sex'][$sex]		= "checked";
}
?>

<div class="title title_top">SMS 주소록 정보</div>

<form name="frmMember" method="post" enctype="multipart/form-data" action="./indb.php" onsubmit="return chkForm(this);">
<input type="hidden" name="mode" value="sms_address_add<?=$_GET['mode'] =='excel' ? '_by_excel' : ''?>">
<input type="hidden" name="sno" value="<?=$_GET['sno']?>">
<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td>그룹</td>
	<td>
	<div>
	<span class="noline"><input type="radio" name="grp_chk" value="Def" checked />기존그룹명 : </span>
	<select name="sms_grp">
	<option value="">==그룹선택==</option>
	<? foreach( $r_grp as $v ){ ?>
	<option value="<?=$v?>" <?=$selected['grp'][$v]?>><?=$v?></option>
	<? } ?>
	</select>
	</div>
	<div>
	<span class="noline"><input type="radio" name="grp_chk" value="New" />신규그룹명 : </span>
	<input type="text" NAME="sms_grp_new" value="" class="line"/></td>
	</div>
</tr>
<? if ( $_GET['mode'] === 'excel' ) { ?>
<tr>
	<td>엑셀 파일</td>
	<td><input type="file" NAME="xls_file" value="" require  class="line" /></td>
</tr>
</table>
<p>
쉼표(,)로 구분된 엑셀 csv 파일(.csv) 만 업로드 가능합니다. <a href="../data/csv_sms.xls">[샘플 다운로드]</a><br>
이름, 핸드폰번호, 성별은 필수 사항입니다.
</p>
<? } else { ?>
<tr>
	<td>이름</td>
	<td><input type="text" NAME="sms_name" value="<?=$sms_name?>" require  class="line" /></td>
</tr>
<tr>
	<td>핸드폰번호</td>
	<td>
	<input type="text" NAME="sms_mobile[]" size="4" maxlength="3" value="<?=$sms_mobile[0]?>" onkeydown="onlynumber();" require  class="line" /> -
	<input type="text" NAME="sms_mobile[]" size="4" maxlength="4" value="<?=$sms_mobile[1]?>" onkeydown="onlynumber();" require class="line" /> -
	<input type="text" NAME="sms_mobile[]" size="4" maxlength="4" value="<?=$sms_mobile[2]?>" onkeydown="onlynumber();" require class="line" />
	</td>
</tr>
<tr>
	<td>성별</td>
	<td class="noline">
	<input type="radio" name="sex" value="M" <?=$checked['sex']['M']?> />남자
	<input type="radio" name="sex" value="F" <?=$checked['sex']['F']?> />여자
	</td>
</tr>
<tr>
	<td>비고</td>
	<td><input type="text" NAME="sms_etc" value="<?=$sms_etc?>" style="width:100%"  class="line" /></td>
</tr>
</table>
<? } ?>
<p>

<div class="button_popup" align="center">
<input type="image" src="../img/btn_confirm_s.gif" />
</div>

</form>

<script>table_design_load();</script>