<?
/*********************************************************
* 파일명     :  popup_list.php
* 프로그램명 :	모바일샵 팝업리스트
* 작성자     :  kth
* 생성일     :  2013.05.09
**********************************************************/

$location = "모바일샵 > 팝업창 관리";
include "../_header.php";

$mpopup_no = $_GET['mpopup_no'];

if($mpopup_no) {
	$popup_query = $db->_query_print('SELECT * FROM '.GD_MOBILEV2_POPUP.' WHERE mpopup_no=[i]', $mpopup_no);
	$res_popup = $db->_select($popup_query);
	$popup_data = $res_popup[0];

}
else {
	# 기본값 설정 #
	$popup_data['open_type'] = '0';
	$popup_data['popup_type'] = '0';
}

$checked['open_type'][$popup_data['open_type']] = 'checked';
$checked['popup_type'][$popup_data['popup_type']] = 'checked';

$selected['start_time'][$popup_data['start_time']] = 'selected';
$selected['end_time'][$popup_data['end_time']] = 'selected';
?>
<script type="text/javascript">

function chkform1(frm) {
	if(frm.popup_title.value == "") {
		alert('제목을 입력해 주세요');
		frm.popup_title.focus();
		return false;
	}

	if(frm.open_type[1].checked) {
		if(frm.start_date.value == "") {
			alert('노출기간 날짜를 설정해주세요.');
			frm.start_date.focus();
			return false;
		}

		if(frm.end_date.value == "") {
			alert('노출기간 날짜를 설정해주세요.');
			frm.end_date.focus();
			return false;
		}
	}
}

function chkOpenType(){
	if(document.getElementById("open_type1").checked) document.getElementById('time_set').style.display='block';
	else document.getElementById('time_set').style.display='none';
}

function chkPopupType() {
	if(document.getElementById("popup_type1").checked) {
		document.getElementById('image').style.display='none';
		document.getElementById('editor').style.display='block';
	} else {
		document.getElementById('image').style.display='block';
		document.getElementById('editor').style.display='none';
	}

}
</script>
<div class="title title_top">모바일샵 팝업창 만들기 <span>모바일샵 메인에 노출되는 팝업창을 만들 수 있습니다</span><a href="javascript:manual('<?=$guideUrl?>board/view.php?id=mobileshopV2&no=14')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<form name="form" method="post" action="indb.php" onsubmit="return chkform1(this)" enctype="multipart/form-data">
<input type="hidden" name="mode" value="popup_regist" />
<input type="hidden" name="mpopup_no" id="mpopup_no" value="<?=$mpopup_no?>" />
<table class="tb">
<col class="cellC"><col class="cellL">
<tbody style="height:26px;">
<tr>
	<td>팝업제목</td>
	<td>
		<input type="text" name="popup_title" size="50" value="<?=$popup_data['popup_title']?>" />
	</td>
</tr>
<tr>
	<td>노출기간</td>
	<td >
		<div style="position:relative; height:25px;">
			<div style="position:absolute;">
				<label class="noline"><input type="radio" name="open_type" id="open_type0" value="0" onclick="chkOpenType();" <?=$checked['open_type']['0']?> required="required" /> 계속</label>
				<label class="noline"><input type="radio" name="open_type" id="open_type1" value="1" onclick="chkOpenType();" <?=$checked['open_type']['1']?> required="required" /> 기간설정</label>
			</div>
			<div id="time_set" style="position:relative; left:130px; display:none;">
				<input type="text" name="start_date" size="10" value="<?=$popup_data['start_date']?>" onclick="calendar(event);" class="cline" />&nbsp;
				<select name="start_time">
					<? for($i=0; $i<24; $i++) { ?>
					<option value="<?=$i?>" <?=$selected['start_time'][$i]?>><?=$i?>시</option>
					<? } ?>
				</select>
				-
				<input type="text" name="end_date" size="10" value="<?=$popup_data['end_date']?>" onclick="calendar(event);" class="cline" />&nbsp;
				<select name="end_time">
					<? for($i=0; $i<24; $i++) { ?>
					<option value="<?=$i?>" <?=$selected['end_time'][$i]?>><?=$i?>시</option>
					<? } ?>
				</select>
			</div>
		</div>
	</td>
</tr>
<tr>
	<td>팝업내용</td>
	<td>
			<div style="height:25px;">
				<label class="noline"><input type="radio" name="popup_type" id="popup_type0" value="0" onclick="chkPopupType();" <?=$checked['popup_type']['0']?> required="required" /> 이미지 업로드</label>
				<label class="noline"><input type="radio" name="popup_type" id="popup_type1" value="1" onclick="chkPopupType();" <?=$checked['popup_type']['1']?> required="required" /> 텍스트(에디터)입력</label>
			</div>

			<div id="image">
				<input type="file" name="popup_img" size="50" />
				<a href="javascript:webftpinfo( '<?=( $popup_data['popup_img'] != '' ? '/data/m/upload_img/'. $popup_data['popup_img'] : '' )?>' );"><img src="../img/codi/icon_imgview.gif" border="0" alt="이미지 보기" align="absmiddle"></a>
				<? if ( $popup_data['popup_img'] != '' ){ ?>&nbsp;&nbsp;<span class="noline"><input type="checkbox" name="del_popup_img" value="Y">삭제</span><? } ?>
				<input type="hidden" name="popup_img_hidden" value="<?=$popup_data['popup_img']?>" />
				<br><span class="extext">권장 사이즈 : 308px X 244px</span>
			</div>

			<div id="editor" style="position:relative; display:none;">
				<textarea name="popup_body" style="width:100%;height:300px" type=editor><?=stripslashes($popup_data['popup_body'])?></textarea>
				<script src="../../lib/meditor/mini_editor.js"></script>
				<script>mini_editor("../../lib/meditor/");</script>
			</div>

	</td>
</tr>
<tr>
	<td>팝업연결 링크 URL</td>
	<td>
		<input type=text name="link_url" size="100" value="<?=$popup_data['link_url']?>" />
		<br><span class="extext">"http://”는 제외하고 입력합니다.</span>
	</td>
</tr>
</table>
<div class=button>

<? if($mpopup_no) { ?>
	<input type=image src="../img/btn_modify.gif">
<? }else{ ?>
	<input type=image src="../img/btn_register.gif">
<? } ?>
<a href="mobile_popup_list.php"><img src="../img/btn_list.gif" border="0" /></a>
</div>
</form>
<script>chkOpenType(); chkPopupType(); </script>
<? include "../_footer.php"; ?>