<?

$scriptLoad='<script src="../todayshop/codi/_codi.js"></script>';
include "../_header.popup.php";

if($_GET['file']) $_GET['design_file']	= "popup/" . $_GET['file'];

include_once dirname(__FILE__) . "/codi/code.class.php";
$codi = new codi;

if($_GET['design_file']) $data_dir	= $codi->get_dirinfo( $dirpath = dirname( $_GET['design_file'] ) );	# Directory Data
if($_GET['design_file']) $data_file	= $codi->get_fileinfo( $_GET['design_file'] );						# File Data

$data_dir['inc']	= 'file';
$data_file['real_linkurl'] = '../../' . $data_file['linkurl'];

$file_name	= substr($data_file['name'],0,-4);
?>

<form method="post" name="fm" action="../todayshop/codi/indb.php?design_file=<?=$_GET['design_file']?>" onsubmit="return chkForm( this );" enctype="multipart/form-data">
<input type="hidden" name="mode" value="popupConf" />

<div class="title title_top">메인팝업창 등록<span>메인 팝업창에 대한 설정을 추가 변경하실 수 있습니다</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=design&no=7')"><img src="../img/btn_q.gif" border="0" align="absmiddle" /></a></div>
<?=$workSkinTodayStr?>
<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td>팝업제목</td>
	<td><input type="text" name="text" size="60" value="<?=$data_file['text']?>" class="line"></td>
</tr>
<tr>
	<td>팝업파일명</td>
	<td>
	<? if($_GET['file']){?>
	<input type="hidden" name="name" value="<?=$file_name?>"><?=$_GET['file']?>
	<?}else{?>
	<font class=ver811><input type="text" name="name" value="<?=$file_name?>" style="ime-mode:disabled;">.htm</font> ※ 사용하고자하는 팝업화일의 화일명을 넣으셔야합니다.(영문만 가능)
	<?}?>
	</td>
</tr>
<tr>
	<td>출력여부</td>
	<td class="noline">
	<input type="radio" name="popup_use" value="Y" <?=( $data_file['popup_use'] == 'Y' ? 'checked' : '' );?> />출력
	<input type="radio" name="popup_use" value="N" <?=( $data_file['popup_use'] != 'Y' ? 'checked' : '' );?> />미출력
	</td>
</tr>
<tr>
	<td>창위치</td>
	<td>
	상단에서 : <input type="text" name="popup_spotw" size="6" class="right line" value="<?=$data_file['popup_spotw']?>" onkeydown="onlynumber();" /> <font class=ver811>pixel</font><br />
	좌측에서 : <input type="text" name="popup_spoth" size="6" class="right line" value="<?=$data_file['popup_spoth']?>" onkeydown="onlynumber();" /> <font class=ver811>pixel</font>
	</td>
</tr>
<tr>
	<td>창크기</td>
	<td>
	가로크기 : <input type="text" name="popup_sizew" size="6" class="right line" value="<?=$data_file['popup_sizew']?>" onkeydown="onlynumber();" /> <font class=ver811>pixel</font><br />
	세로크기 : <input type="text" name="popup_sizeh" size="6" class="right line" value="<?=$data_file['popup_sizeh']?>" onkeydown="onlynumber();" /> <font class=ver811>pixel</font>
	</td>
</tr>

<tr>
	<td>특정기간동안<br>무조건 노출</td>
	<td>

	<div class="noline"><input type="radio" name="popup_dt2tm" value="Y" <?=( $data_file['popup_dt2tm'] == 'Y' ? 'checked' : '' );?> /> 특정기간동안 팝업창이 열립니다.</div>
	<div>
	시작일 : <input type="text" name="popup_sdt_tg" size="10" maxlength="8" class="line" value="<?=( $data_file['popup_dt2tm'] == 'Y' ? $data_file['popup_sdt'] : '' )?>" onkeydown="onlynumber();" onclick="calendar(event);" />
	시작시간 : <input type="text" name="popup_stime_tg" size="6" maxlength="4" class="line" value="<?=( $data_file['popup_dt2tm'] == 'Y' ? $data_file['popup_stime'] : '' )?>" onkeydown="onlynumber();" /> <font color=627dce class=ver8>ex) 20080415</font>
	<div>
	</div>
	종료일 : <input type="text" name="popup_edt_tg" size="10" maxlength="8" class="line" value="<?=( $data_file['popup_dt2tm'] == 'Y' ? $data_file['popup_edt'] : '' )?>" onkeydown="onlynumber();" onclick="calendar(event);" />
	종료시간 : <input type="text" name="popup_etime_tg" size="6" maxlength="4" class="line" value="<?=( $data_file['popup_dt2tm'] == 'Y' ? $data_file['popup_etime'] : '' )?>" onkeydown="onlynumber();" /> <font color=627dce class=ver8>ex) 오전6시: 0600, 밤12시: 2400</font>
	</div>
	</td>
</tr>

<tr>
	<td>특정기간내에<br>특정시간에만 노출</td>
	<td>
	<div class="noline"><input type="radio" name="popup_dt2tm" value="" <?=( $data_file['popup_dt2tm'] == '' ? 'checked' : '' );?> /> 특정기간동안 특정한 시간에만 팝업창이 열립니다.</div>
	<div>
	시작일 : <input type="text" name="popup_sdt" size="10" maxlength="8" class="line" value="<?=( $data_file['popup_dt2tm'] == '' ? $data_file['popup_sdt'] : '' )?>" onkeydown="onlynumber();" onclick="calendar(event);" />
	종료일 : <input type="text" name="popup_edt" size="10" maxlength="8" class="line" value="<?=( $data_file['popup_dt2tm'] == '' ?$data_file['popup_edt'] : '' )?>" onkeydown="onlynumber();" onclick="calendar(event);" /> <font color=627dce class=ver8>ex) 20080415</font>
	</div>
	<div>
	오픈시간 : <input type="text" name="popup_stime" size="6" maxlength="4" class="line" value="<?=( $data_file['popup_dt2tm'] == '' ?$data_file['popup_stime'] : '' )?>" onkeydown="onlynumber();" />
	클로즈시간 : <input type="text" name="popup_etime" size="6" maxlength="4" class="line" value="<?=( $data_file['popup_dt2tm'] == '' ?$data_file['popup_etime'] : '' )?>" onkeydown="onlynumber();" /> <font color=627dce class=ver8>ex) 오전6시: 0600, 밤12시: 2400</font>
	</div>
	</td>
</tr>
<tr>
	<td>창타입</td>
	<td class="noline">
	<input type="radio" name="popup_type" value="" <?=( $data_file['popup_type'] == '' ? 'checked' : '' );?> />일반 윈도우팝업창
	<input type="radio" name="popup_type" value="layerMove" <?=( $data_file['popup_type'] == 'layerMove' ? 'checked' : '' );?> />이동레이어
	<input type="radio" name="popup_type" value="layer" <?=( $data_file['popup_type'] == 'layer' ? 'checked' : '' );?> />고정레이어
	</td>
</tr>
</table>

<div style="padding-top:10;"></div>


<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
<tr valign="top">
	<td>
<?
{ // 디자인코디툴

	$tmp = array();
	$tmp['t_name']		= 'content';
	$tmp['t_width']		= '100%';
	$tmp['t_rows']		= 20;
	$tmp['t_property']	= ' required label="HTML 소스"';
	$tmp['tplFile']		= "/" . $_GET['design_file'];

	echo "<script>DCTM.write('{$tmp['t_name']}', '{$tmp['t_width']}', '{$tmp['t_rows']}', '{$tmp['t_property']}', '{$tmp['tplFile']}');</script>";
}
?>
	</td>
</tr>
</table>

<div style="padding:20px" align="center" class="noline">
<input type="image" src="../img/btn_save.gif" alt="저장하기" />
<a href="iframe.popup_list.php"><img src="../img/btn_list.gif" border="0" /></a>
</div>

</form>

<div id="codi_replacecode"><script>DCRM.write('<?=$_GET['design_file']?>');</script></div>



<script>
table_design_load();
setHeight_ifrmCodi();
</script>