<?php
$mode = ($_GET['mode']=='modify'?'modify':'add');

if($mode=='add') {
	$location = "�⼮üũ���� > �⼮üũ ���";
}
else {
	$location = "�⼮üũ���� > �⼮üũ ����";
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

	// ��Ų��� Ÿ�� : HTML�����ϱ� Ŭ����
	var click_rdo_design_head_type_html = function() {
		$('file_design_head_file').disabled=true;
		frm.getInputs('radio','design_head_image').invoke('disable');
	}

	// ��Ų��� Ÿ�� : �̹��� ���� Ŭ����
	var click_rdo_design_head_type_image = function() {
		$('file_design_head_file').disabled=true;
		frm.getInputs('radio','design_head_image').invoke('enable');
	}

	// ��Ų��� Ÿ�� : �̹��� ���� �ø��� Ŭ����
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
	/* �ڵ�����������
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
		alert('�⼮üũ���� �ʼ��Դϴ�');
		frm.name.focus();
		return false;
	}
	if(frm.start_date.value.length==0) {
		alert('����Ⱓ�� �Է����ּ���');
		return false;
	}
	if(frm.end_date.value.length==0) {
		alert('����Ⱓ�� �Է����ּ���');
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
		alert('��Ų���� �������� �������ּ���');
		return false;
	}
	return true;
}

function copyText(text) {
	window.clipboardData.setData('Text',text);
	alert('Ŭ�����忡 ����Ǿ����ϴ�');
}
</script>

<form name="frmField" method="post" action="attendance.indb.php" id="frmField" onsubmit="return chkValidation(this)" enctype="multipart/form-data" target="ifrmHidden">
<input type="hidden" name="attendance_no" value="<?=$result['attendance_no']?>">
<input type="hidden" name="mode" value="<?=$mode?>">

<? if($mode=='add'): ?>
	<div class="title title_top">�⼮üũ ��� <span>�⼮üũ �̺�Ʈ�� ����Ͻ� �� �ֽ��ϴ�.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=event&no=17')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<? else: ?>
	<div class="title title_top">�⼮üũ ���� <span>�⼮üũ �̺�Ʈ�� �����Ͻ� �� �ֽ��ϴ�.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=event&no=17')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<? endif; ?>


<table class="tb">
<col class="cellC"><col class="cellC"><col class="cellL">
<tr>
	<td colspan="2" style="width:150px" nowrap>�⼮üũ��</td>
	<td width="100%">
		<input type="text" name="name"  class="cline" size="70">
	</td>
</tr>
<tr>
	<td colspan="2">�⼮üũ ����Ⱓ</td>
	<td >
		<input type="text" name="start_date" size="10" value="<?=str_replace('-','',$result['start_date'])?>" onkeydown="onlynumber();" onclick="calendar(event);" class="cline" /> ~
		<input type="text" name="end_date" size="10" value="<?=str_replace('-','',$result['end_date'])?>" onkeydown="onlynumber();" onclick="calendar(event);" class="cline" />
	</td>
</tr>
<tr>
	<td colspan="2">
		����ϼ� �⼮üũ<br/>
		��� ����
	</td>
	<td class="noline">
		<input id="rdo_mobile_useyn_n" type="radio" name="mobile_useyn" value="n"/>
		<label for="rdo_mobile_useyn_n">������</label>
		<input id="rdo_mobile_useyn_y" type="radio" name="mobile_useyn" value="y"/>
		<label for="rdo_mobile_useyn_y">���</label>
		<div class="extext" style="margin-top: 5px;">
			����ϼ�V2���� �⼮üũ�� ����� �� �ְԵ˴ϴ�. (Default ��Ų ����)<br/>
			���� 'ȸ���α��ν� �ڵ�' ������� ��� �����ϸ�, PC���� ���θ��� �������� ���˴ϴ�.
		</div>
	</td>
</tr>
<tr>
	<td rowspan="2">�⼮üũ</td>
	<td>����</td>
	<td class="noline">
		<input type="radio" name="condition_type" value="straight" id="rdo_condition_type_straight"  >���� �⼮�� - �⼮üũ ����Ⱓ���� ���� ����
		<input type="text" name="condition_period" size="3" style="border:1px solid #cccccc" id="ipt_straight_period">��
		�̻� �⼮�ϸ� ������ �����Ѵ�<br>
		<input type="radio" name="condition_type" value="sum" id="rdo_condition_type_sum">Ƚ�� �⼮�� - �⼮üũ ����Ⱓ����
		<input type="text" name="condition_period" size="3" style="border:1px solid #cccccc" id="ipt_sum_period" >�� �̻� �⼮�ϸ� ������ �����Ѵ�<br>
	</td>
</tr>
<tr>
	<td>����</td>
	<td class="noline">
		<input type="radio" name="provide_method" value="manual" id="rdo_provide_method_manual">������ �������� - �⼮üũ�Ⱓ ���� �� ��ڲ��� �⼮�ڸ�ܿ��� �������� ����<br>
		<!--
		<input type="radio" name="provide_method" value="auto" id="rdo_provide_method_auto">������ �ڵ� ���� - ��ý�Ⱓ ���� �� ��ý ������ �޼��� ȸ������
		<input type="text" name="auto_reserve" size="5" style="border:1px solid #cccccc" id="ipt_auto_reserve">���� �ڵ� ����
		-->
	</td>
</tr>
<tr>
	<td rowspan="2">�⼮üũ</td>
	<td>���</td>
	<td class="noline">
		<input type="radio" name="check_method" value="stamp" id="rdo_check_method_stamp">��ý ������(����) ��� (1�� 1ȸ ����)<br>
		<input type="radio" name="check_method" value="comment" id="rdo_check_method_comment">��ý ��� �ޱ� (1�� 1ȸ ����)<br>
		<input type="radio" name="check_method" value="login" id="rdo_check_method_login">ȸ���α��ν� �ڵ� (1�� 1ȸ ����)<br>
		<span class="extext">����ϼ� �⼮üũ ���� 'ȸ���α��ν� �ڵ�' ����� ��� �����մϴ�.</span>
	</td>
</tr>
<tr>
	<td>�޼���</td>
	<td class="noline">
		<input type="radio" name="check_message_type" value="select" id="rdo_check_message_type_select">���� &nbsp; &nbsp; &nbsp; &nbsp;
		<select name="check_message_select" id="sel_check_message_select">
		<option value="1">¦¦¦~ �⼮üũ �̺�Ʈ�� �����ϼ̾��~</option>
		<option value="2">��ī��ī~ �⼮üũ �̺�Ʈ�� �����ϼ̾�� ^^</option>
		<option value="3">��ũ�����̼�~ �⼮üũ�� �����ϼ̾��</option>
		<option value="4">��~�� �⼮üũ ������ �����̾��!</option>
		</select>

		<br>
		<input type="radio" name="check_message_type" value="custom" id="rdo_check_message_type_custom">�����Է� &nbsp;
		<input type="text" name="check_message_custom" size="40" style="border:1px solid #cccccc" id="ipt_check_message_custom"  value="<?=h($result['check_message_custom]'])?>">
		<br>
		<input type="radio" name="check_message_type" value="none" id="rdo_check_message_type_none">����<br>
	</td>
</tr>
<tr name="designArea">
	<td rowspan="3">�⼮üũ������</td>
	<td>��Ų���</td>
	<td class="noline">
		<input type="radio" name="design_head_type" value="html" id="rdo_design_head_type_html">HTML�� �����ϱ�<br>
		<textarea name="design_head_html" style="width:100%;height:200px" id="txt_design_head_html" type="editor"><?=$result['design_head_html']?></textarea><br>
		<script src="../../lib/meditor/mini_editor.js"></script>
		<script>mini_editor("../../lib/meditor/");</script>
		<br>
		<input type="radio" name="design_head_type" value="image" id="rdo_design_head_type_image"> �̹��� ����<br>
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
		<input type="radio" name="design_head_type" value="upload" id="rdo_design_head_type_upload"> �̹��� ���� �ø���<br>
		&nbsp; &nbsp; <input type="file" name="design_head_file" id="file_design_head_file">
	</td>
</tr>
<tr name="designArea">
	<td>��Ų����</td>
	<td class="noline">
		<table border="0" cellpadding="5">
		<tr>
		<td align="center">
			�������� ��Ų<br>
			<img src="../../data/attendance/thumb_body_image_1.gif"><br>
			<input type="radio" name="design_body" value="1" id="rdo_design_body_1">
		</td>
		<td align="center">
			�������� ��Ų<br>
			<img src="../../data/attendance/thumb_body_image_2.gif"><br>
			<input type="radio" name="design_body" value="2" id="rdo_design_body_2">
		</td>
		<td align="center">
			��ۿ� ��Ų<br>
			<img src="../../data/attendance/thumb_body_image_3.gif"><br>
			<input type="radio" name="design_body" value="3" id="rdo_design_body_3">
		</td>
		<td align="center">
			��ۿ� ��Ų<br>
			<img src="../../data/attendance/thumb_body_image_4.gif"><br>
			<input type="radio" name="design_body" value="4" id="rdo_design_body_4">
		</td>
		</table>
	</td>
</tr>
<tr name="designArea">
	<td>������</td>
	<td class="noline">
		������(����) �̹����� �⼮üũ ����� �������� �����ϼ��� ��쿡�� ���� �˴ϴ�<br>
		<input type="radio" name="design_stamp" value="default" id="rdo_design_stamp_default"> �⺻ �̹��� ���<br><br>
		<input type="radio" name="design_stamp" value="upload"  id="rdo_design_stamp_upload"> �̹��� ���� �ø��� (��������� 50x50px)<br>
		&nbsp; &nbsp; <input type="file" name="design_stamp_upload" id="file_design_stamp_upload">
	</td>
</tr>
<? if($mode=='add'): ?>
<tr name="designArea">
	<td colspan="2">�⼮üũ������ URL</td>
	<td class="noline">
		��ϿϷ� �� �⼮üũ������ URL�� �ڵ� �����Ǿ����ϴ�.
	</td>
</tr>
<? else: ?>
<tr name="designArea">
	<td colspan="2">�⼮üũ������ URL</td>
	<td class="noline">
		<a href="../../member/attendance.php?attendance_no=<?=$attendance_no?>" target="_blank">../member/attendance.php?attendance_no=<?=$attendance_no?></a>
		<img src="../img/btn_s_urlcopy2.gif" align="absmiddle" style="cursor:pointer" onclick="copyText('../member/attendance.php?attendance_no=<?=$attendance_no?>')">
		<img src="../img/btn_s_urlcopy1.gif" align="absmiddle" style="cursor:pointer" onclick="copyText('<?=$sitelink->link('member/attendance.php?attendance_no='.$attendance_no,'auto',true)?>')">

	</td>
</tr>
<? endif; ?>
<tr name="designAreaLogin">
	<td colspan="2">�⼮üũ������</td>
	<td class="noline">
		ȸ�� �α��ν� �ڵ����� �⼮�� üũ�Ǵ� ������� ������ �⼮üũ �������� �ʿ�� ���� �ʽ��ϴ�
	</td>
</tr>
</table>

<div class="button">
<input type="image" src="../img/btn_register.gif">
</div>

</form>

<? include "../_footer.php"; ?>
