<?php
$mode = ($_GET['mode']=='modify'?'modify':'add');

if($mode=='add') {
	$location = "출석체크관리 > 출석체크 등록";
}
else {
	$location = "출석체크관리 > 출석체크 수정";
}

include "../_header.php";

$attd = Core::loader('attendance');

if($mode=='modify') {
	$attendance_no = (int)$_GET['attendance_no'];
	$query = "select * from gd_attendance where attendance_no = '{$attendance_no}'";
	$result = $db->_select($query);
	$result=$result[0];
}




?>
<script type="text/javascript">

document.observe("dom:loaded", function() {

	var frm = $('frmField');

	// 스킨상단 타입 : HTML편집하기 클릭시
	var click_rdo_design_head_type_html = function() {
		$('file_design_head_file').disabled=true;
		frm.getInputs('radio','design_head_image').invoke('disable');
	}

	// 스킨상단 타입 : 이미지 선택 클릭시
	var click_rdo_design_head_type_image = function() {
		$('file_design_head_file').disabled=true;
		frm.getInputs('radio','design_head_image').invoke('enable');
	}

	// 스킨상단 타입 : 이미지 직접 올리기 클릭시
	var click_rdo_design_head_type_upload = function() {
		$('file_design_head_file').disabled=false;
		frm.getInputs('radio','design_head_image').invoke('disable');
	}


	$('rdo_mobile_useyn_n').observe('click', function(event){
		$('rdo_check_method_stamp').disabled = false;
		$('rdo_check_method_comment').disabled = false;
	});
	$('rdo_mobile_useyn_y').observe('click', function(event){
		if ($('rdo_check_method_stamp').checked || $('rdo_check_method_comment').checked) {
			$('rdo_check_method_stamp').disabled = true;
			$('rdo_check_method_comment').disabled = true;
			$('rdo_check_method_login').checked = true;
			frm.select('[name=designArea]').invoke('hide');
			frm.select('[name=designAreaLogin]').invoke('show');
		}
		else {
			$('rdo_check_method_stamp').disabled = true;
			$('rdo_check_method_comment').disabled = true;
		}
	});

	$('rdo_condition_type_straight').observe('click',function(event){
		$('ipt_straight_period').disabled=false;
		$('ipt_sum_period').disabled=true;
	});
	$('rdo_condition_type_sum').observe('click',function(event){
		$('ipt_straight_period').disabled=true;
		$('ipt_sum_period').disabled=false;
	});

	$('rdo_provide_method_manual').observe('click',function(event) {
		//$('ipt_auto_reserve').disabled=true;
	});
	/* 자동적립금지급
	$('rdo_provide_method_auto').observe('click',function(event) {
		$('ipt_auto_reserve').disabled=false;
	});
	*/
	$('rdo_check_method_stamp').observe('click',function(event) {
		$('rdo_design_body_1').disabled=false;
		$('rdo_design_body_2').disabled=false;
		$('rdo_design_body_3').disabled=true;
		$('rdo_design_body_4').disabled=true;
		frm.select('[name=designArea]').invoke('show');
		frm.select('[name=designAreaLogin]').invoke('hide');
	});

	$('rdo_check_method_comment').observe('click',function(event) {
		$('rdo_design_body_1').disabled=true;
		$('rdo_design_body_2').disabled=true;
		$('rdo_design_body_3').disabled=false;
		$('rdo_design_body_4').disabled=false;
		frm.select('[name=designArea]').invoke('show');
		frm.select('[name=designAreaLogin]').invoke('hide');
	});


	$('rdo_check_method_login').observe('click',function(event) {
		frm.select('[name=designArea]').invoke('hide');
		frm.select('[name=designAreaLogin]').invoke('show');
	});

	$('rdo_check_message_type_select').observe('click',function(event){
		$('sel_check_message_select').disabled=false;
		$('ipt_check_message_custom').disabled=true;
	});

	$('rdo_check_message_type_custom').observe('click',function(event){
		$('sel_check_message_select').disabled=true;
		$('ipt_check_message_custom').disabled=false;
	});

	$('rdo_check_message_type_none').observe('click',function(event){
		$('sel_check_message_select').disabled=true;
		$('ipt_check_message_custom').disabled=true;
	});

	$('rdo_design_head_type_html').observe('click',function(event){
		click_rdo_design_head_type_html();
	});

	$('rdo_design_head_type_image').observe('click',function(event){
		click_rdo_design_head_type_image();
	});

	$('rdo_design_head_type_upload').observe('click',function(event){
		click_rdo_design_head_type_upload();
	});

	$('rdo_design_stamp_default').observe('click',function(event){
		$('file_design_stamp_upload').disabled=true;
	});

	$('rdo_design_stamp_upload').observe('click',function(event){
		$('file_design_stamp_upload').disabled=false;
	});

	<? if($mode=='add'): ?>
		$('rdo_mobile_useyn_n').checked = true;
		$('rdo_condition_type_straight').checked=true;
		$('ipt_straight_period').disabled=false;
		$('ipt_sum_period').disabled=true;

		$('rdo_provide_method_manual').checked=true;
		//$('ipt_auto_reserve').disabled=true;

		$('rdo_check_method_stamp').checked=true;

		$('rdo_check_message_type_select').checked=true;
		$('sel_check_message_select').disabled=false;
		$('ipt_check_message_custom').disabled=true;

		$('rdo_design_head_type_html').checked=true;
		$('file_design_head_file').disabled=true;

		frm.getInputs('radio','design_head_image').invoke('disable');
		frm.getInputs('radio','design_body')[0].checked=true;
		$('rdo_design_stamp_default').checked=true;

		$('rdo_design_body_3').disabled=true;
		$('rdo_design_body_4').disabled=true;
		frm.select('[name=designAreaLogin]').invoke('hide');
	<? elseif($mode=='modify'): ?>
		frm.setValue('mobile_useyn',"<?php echo $result['mobile_useyn']; ?>");
		frm.setValue('name',"<?=$result['name']?>");
		frm.setValue('condition_type',"<?=$result['condition_type']?>");
		frm.setValue('condition_period',"<?=$result['condition_period']?>");
		frm.setValue('provide_method',"<?=$result['provide_method']?>");
		//frm.setValue('auto_reserve',"<?=$result['auto_reserve']?>");
		frm.setValue('check_method',"<?=$result['check_method']?>");
		frm.setValue('check_message_type',"<?=$result['check_message_type']?>");
		frm.setValue('check_message_select',"<?=$result['check_message_select']?>");
		frm.setValue('check_message_custom',"<?=$result['check_message_custom']?>");
		frm.setValue('design_head_type',"<?=$result['design_head_type']?>");
		frm.setValue('design_head_image',"<?=$result['design_head_image']?>");
		frm.setValue('design_body',"<?=$result['design_body']?>");
		frm.setValue('design_stamp',"<?=$result['design_stamp']?>");

		frm.start_date.disabled=true;
		frm.end_date.disabled=true;

		frm.getInputs('radio','mobile_useyn').invoke('disable');
		frm.getInputs('radio','condition_type').invoke('disable');
		frm.getInputs('radio','provide_method').invoke('disable');
		frm.getInputs('text','condition_period').invoke('disable');
		//frm.getInputs('text','auto_reserve').invoke('disable');
		frm.getInputs('radio','check_method').invoke('disable');
		frm.getInputs('radio','check_message_type').invoke('disable');


		$('sel_check_message_select').disabled=true;
		$('ipt_check_message_custom').disabled=true;

		if($('rdo_design_head_type_html').checked) {
			frm.getInputs('radio','design_head_image').invoke('disable');
			$('file_design_head_file').disabled=true;
		}

		if($('rdo_design_head_type_image').checked) {
			$('file_design_head_file').disabled=true;
		}

		if($('rdo_design_head_type_upload').checked) {
			frm.getInputs('radio','design_head_image').invoke('disable');
		}

		if($('rdo_design_stamp_default').checked) {
			$('file_design_stamp_upload').disabled=true;
		}

		if($('rdo_check_method_stamp').checked) {
			$('rdo_design_body_1').disabled=false;
			$('rdo_design_body_2').disabled=false;
			$('rdo_design_body_3').disabled=true;
			$('rdo_design_body_4').disabled=true;
			frm.select('[name=designArea]').invoke('show');
			frm.select('[name=designAreaLogin]').invoke('hide');
		}
		if($('rdo_check_method_comment').checked) {
			$('rdo_design_body_1').disabled=true;
			$('rdo_design_body_2').disabled=true;
			$('rdo_design_body_3').disabled=false;
			$('rdo_design_body_4').disabled=false;
			frm.select('[name=designArea]').invoke('show');
			frm.select('[name=designAreaLogin]').invoke('hide');
		}
		if($('rdo_check_method_login').checked) {
			frm.select('[name=designArea]').invoke('hide');
			frm.select('[name=designAreaLogin]').invoke('show');
		}
	<? endif; ?>

});

function chkValidation(frm) {
	if(frm.name.value.length==0) {
		alert('출석체크명은 필수입니다');
		frm.name.focus();
		return false;
	}
	if(frm.start_date.value.length==0) {
		alert('진행기간을 입력해주세요');
		return false;
	}
	if(frm.end_date.value.length==0) {
		alert('진행기간을 입력해주세요');
		return false;
	}

	var tmp=false;
	if($('rdo_design_body_1').disabled==false && $('rdo_design_body_1').checked==true) {
		tmp=true;
	}
	if($('rdo_design_body_2').disabled==false && $('rdo_design_body_2').checked==true) {
		tmp=true;
	}
	if($('rdo_design_body_3').disabled==false && $('rdo_design_body_3').checked==true) {
		tmp=true;
	}
	if($('rdo_design_body_4').disabled==false && $('rdo_design_body_4').checked==true) {
		tmp=true;
	}
	if(tmp==false) {
		alert('스킨본문 디자인을 선택해주세요');
		return false;
	}
	return true;
}

function copyText(text) {
	window.clipboardData.setData('Text',text);
	alert('클립보드에 복사되었습니다');
}
</script>

<form name="frmField" method="post" action="attendance.indb.php" id="frmField" onsubmit="return chkValidation(this)" enctype="multipart/form-data" target="ifrmHidden">
<input type="hidden" name="attendance_no" value="<?=$result['attendance_no']?>">
<input type="hidden" name="mode" value="<?=$mode?>">

<? if($mode=='add'): ?>
	<div class="title title_top">출석체크 등록 <span>출석체크 이벤트를 등록하실 수 있습니다.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=event&no=17')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<? else: ?>
	<div class="title title_top">출석체크 수정 <span>출석체크 이벤트를 수정하실 수 있습니다.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=event&no=17')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<? endif; ?>


<table class="tb">
<col class="cellC"><col class="cellC"><col class="cellL">
<tr>
	<td colspan="2" style="width:150px" nowrap>출석체크명</td>
	<td width="100%">
		<input type="text" name="name"  class="cline" size="70">
	</td>
</tr>
<tr>
	<td colspan="2">출석체크 진행기간</td>
	<td >
		<input type="text" name="start_date" size="10" value="<?=str_replace('-','',$result['start_date'])?>" onkeydown="onlynumber();" onclick="calendar(event);" class="cline" /> ~
		<input type="text" name="end_date" size="10" value="<?=str_replace('-','',$result['end_date'])?>" onkeydown="onlynumber();" onclick="calendar(event);" class="cline" />
	</td>
</tr>
<tr>
	<td colspan="2">
		모바일샵 출석체크<br/>
		사용 여부
	</td>
	<td class="noline">
		<input id="rdo_mobile_useyn_n" type="radio" name="mobile_useyn" value="n"/>
		<label for="rdo_mobile_useyn_n">사용안함</label>
		<input id="rdo_mobile_useyn_y" type="radio" name="mobile_useyn" value="y"/>
		<label for="rdo_mobile_useyn_y">사용</label>
		<div class="extext" style="margin-top: 5px;">
			모바일샵V2에서 출석체크를 사용할 수 있게됩니다. (Default 스킨 제외)<br/>
			사용시 '회원로그인시 자동' 방법으로 사용 가능하며, PC버전 쇼핑몰과 공통으로 사용됩니다.
		</div>
	</td>
</tr>
<tr>
	<td rowspan="2">출석체크</td>
	<td>조건</td>
	<td class="noline">
		<input type="radio" name="condition_type" value="straight" id="rdo_condition_type_straight"  >매일 출석형 - 출석체크 진행기간내에 매일 연속
		<input type="text" name="condition_period" size="3" style="border:1px solid #cccccc" id="ipt_straight_period">일
		이상 출석하면 혜택을 제공한다<br>
		<input type="radio" name="condition_type" value="sum" id="rdo_condition_type_sum">횟수 출석형 - 출석체크 진행기간내에
		<input type="text" name="condition_period" size="3" style="border:1px solid #cccccc" id="ipt_sum_period" >일 이상 출석하면 혜택을 제공한다<br>
	</td>
</tr>
<tr>
	<td>혜택</td>
	<td class="noline">
		<input type="radio" name="provide_method" value="manual" id="rdo_provide_method_manual">적립금 수동지급 - 출석체크기간 종료 후 운영자께서 출석자명단에서 수동으로 지급<br>
		<!--
		<input type="radio" name="provide_method" value="auto" id="rdo_provide_method_auto">적립금 자동 지급 - 출첵기간 종료 후 출첵 조건을 달성한 회원에게
		<input type="text" name="auto_reserve" size="5" style="border:1px solid #cccccc" id="ipt_auto_reserve">원을 자동 지급
		-->
	</td>
</tr>
<tr>
	<td rowspan="2">출석체크</td>
	<td>방법</td>
	<td class="noline">
		<input type="radio" name="check_method" value="stamp" id="rdo_check_method_stamp">출첵 스템프(도장) 찍기 (1일 1회 인정)<br>
		<input type="radio" name="check_method" value="comment" id="rdo_check_method_comment">출첵 댓글 달기 (1일 1회 인정)<br>
		<input type="radio" name="check_method" value="login" id="rdo_check_method_login">회원로그인시 자동 (1일 1회 인정)<br>
		<span class="extext">모바일샵 출석체크 사용시 '회원로그인시 자동' 방법만 사용 가능합니다.</span>
	</td>
</tr>
<tr>
	<td>메세지</td>
	<td class="noline">
		<input type="radio" name="check_message_type" value="select" id="rdo_check_message_type_select">선택 &nbsp; &nbsp; &nbsp; &nbsp;
		<select name="check_message_select" id="sel_check_message_select">
		<option value="1">짝짝짝~ 출석체크 이벤트에 참여하셨어요~</option>
		<option value="2">추카추카~ 출석체크 이벤트에 참여하셨어요 ^^</option>
		<option value="3">콩크레츄에이션~ 출석체크에 성공하셨어요</option>
		<option value="4">쿠~욱 출석체크 도장을 찍으셨어요!</option>
		</select>

		<br>
		<input type="radio" name="check_message_type" value="custom" id="rdo_check_message_type_custom">직접입력 &nbsp;
		<input type="text" name="check_message_custom" size="40" style="border:1px solid #cccccc" id="ipt_check_message_custom"  value="<?=h($result['check_message_custom]'])?>">
		<br>
		<input type="radio" name="check_message_type" value="none" id="rdo_check_message_type_none">없음<br>
	</td>
</tr>
<tr name="designArea">
	<td rowspan="3">출석체크디자인</td>
	<td>스킨상단</td>
	<td class="noline">
		<input type="radio" name="design_head_type" value="html" id="rdo_design_head_type_html">HTML로 편집하기<br>
		<textarea name="design_head_html" style="width:100%;height:200px" id="txt_design_head_html" type="editor"><?=$result['design_head_html']?></textarea><br>
		<script src="../../lib/meditor/mini_editor.js"></script>
		<script>mini_editor("../../lib/meditor/");</script>
		<br>
		<input type="radio" name="design_head_type" value="image" id="rdo_design_head_type_image"> 이미지 선택<br>
			<table border="0" style="margin-left:20px" cellpadding="5">
			<tr>
			<td align="center">
				<img src="../../data/attendance/thumb_header_image_1.gif"><br>
				<input type="radio" name="design_head_image" value="1">
			</td>
			<td align="center">
				<img src="../../data/attendance/thumb_header_image_2.gif"><br>
				<input type="radio" name="design_head_image" value="2">
			</td>
			<td align="center">
				<img src="../../data/attendance/thumb_header_image_3.gif"><br>
				<input type="radio" name="design_head_image" value="3">
			</td>
			<td align="center">
				<img src="../../data/attendance/thumb_header_image_4.gif"><br>
				<input type="radio" name="design_head_image" value="4">
			</td>
			</table>
		<br>
		<input type="radio" name="design_head_type" value="upload" id="rdo_design_head_type_upload"> 이미지 직접 올리기<br>
		&nbsp; &nbsp; <input type="file" name="design_head_file" id="file_design_head_file">
	</td>
</tr>
<tr name="designArea">
	<td>스킨본문</td>
	<td class="noline">
		<table border="0" cellpadding="5">
		<tr>
		<td align="center">
			스템프용 스킨<br>
			<img src="../../data/attendance/thumb_body_image_1.gif"><br>
			<input type="radio" name="design_body" value="1" id="rdo_design_body_1">
		</td>
		<td align="center">
			스템프용 스킨<br>
			<img src="../../data/attendance/thumb_body_image_2.gif"><br>
			<input type="radio" name="design_body" value="2" id="rdo_design_body_2">
		</td>
		<td align="center">
			댓글용 스킨<br>
			<img src="../../data/attendance/thumb_body_image_3.gif"><br>
			<input type="radio" name="design_body" value="3" id="rdo_design_body_3">
		</td>
		<td align="center">
			댓글용 스킨<br>
			<img src="../../data/attendance/thumb_body_image_4.gif"><br>
			<input type="radio" name="design_body" value="4" id="rdo_design_body_4">
		</td>
		</table>
	</td>
</tr>
<tr name="designArea">
	<td>스템프</td>
	<td class="noline">
		스템프(도장) 이미지는 출석체크 방법을 스템프로 선택하셨을 경우에만 적용 됩니다<br>
		<input type="radio" name="design_stamp" value="default" id="rdo_design_stamp_default"> 기본 이미지 사용<br><br>
		<input type="radio" name="design_stamp" value="upload"  id="rdo_design_stamp_upload"> 이미지 직접 올리기 (권장사이즈 50x50px)<br>
		&nbsp; &nbsp; <input type="file" name="design_stamp_upload" id="file_design_stamp_upload">
	</td>
</tr>
<? if($mode=='add'): ?>
<tr name="designArea">
	<td colspan="2">출석체크페이지 URL</td>
	<td class="noline">
		등록완료 후 출석체크페이지 URL이 자동 생성되어집니다.
	</td>
</tr>
<? else: ?>
<tr name="designArea">
	<td colspan="2">출석체크페이지 URL</td>
	<td class="noline">
		<a href="../../member/attendance.php?attendance_no=<?=$attendance_no?>" target="_blank">../member/attendance.php?attendance_no=<?=$attendance_no?></a>
		<img src="../img/btn_s_urlcopy2.gif" align="absmiddle" style="cursor:pointer" onclick="copyText('../member/attendance.php?attendance_no=<?=$attendance_no?>')">
		<img src="../img/btn_s_urlcopy1.gif" align="absmiddle" style="cursor:pointer" onclick="copyText('<?=$sitelink->link('member/attendance.php?attendance_no='.$attendance_no,'auto',true)?>')">

	</td>
</tr>
<? endif; ?>
<tr name="designAreaLogin">
	<td colspan="2">출석체크디자인</td>
	<td class="noline">
		회원 로그인시 자동으로 출석이 체크되는 방식으로 별도의 출석체크 페이지를 필요로 하지 않습니다
	</td>
</tr>
</table>

<div class="button">
<input type="image" src="../img/btn_register.gif">
</div>

</form>

<? include "../_footer.php"; ?>
