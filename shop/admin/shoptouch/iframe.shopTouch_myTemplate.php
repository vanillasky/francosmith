<?
include "../_header.popup.php";
@include "../../lib/pAPI.class.php";
@include "../../lib/json.class.php";
$pAPI = new pAPI();
$json = new Services_JSON(16);

if($_GET['menu_idx'] == 'main') {
	$arr['tp_type'] = 'main';
    $use_arr['tp_type'] = 'main';
}
else if($_GET['menu_idx'] == 'detail') {
	$arr['tp_type'] = 'detail';
    $use_arr['tp_type'] = 'detail';
}
else {
	$arr['tp_type'] = 'menu';
    $use_arr['tp_type'] = 'menu';
	$use_arr['menu_idx'] = $_GET['menu_idx'];
}
$arr['from'] = 0;
$arr['to'] = 100;

$tmp_template = $pAPI->getMyTemplate($godo['sno'], $arr);
$arr_template = $json->decode($tmp_template);


$use_template = $pAPI->getUseTemplate($godo['sno'], $use_arr);
$arr_use_template = $json->decode($use_template);

if(is_array($arr_use_template)) {
	$use_template_idx = $arr_use_template['tp_idx'];
}

$cnt_template = count($arr_template);
$slide_width = 196 * $cnt_template;

if($slide_width < 196 * 4) {
	$slide_width = 196 * 4;
}

if($cnt_template) {
	$template_page_num = floor(($cnt_template / 4));

	if($cnt_template % 4 != 0) {
		$template_page_num += 1;
	}
}
else {
	$template_page_num = 1;
}
?>
<style type="text/css">
body {margin:0}
#extra-display-form-wrap {}
.display-type-config-tpl {display:none;}
.display-type-wrap {width:94px;float:left;margin:3px;}
.display-type-wrap img {border:none;width:94px;height:72px;}
.display-type-wrap div {text-align:center;}

.display-type-config {width:100%;background:#e6e6e6;border:2px dotted #f54c01;}
.display-type-config  th, .display-type-config  td {font-weight:normal;text-align:left;}
.display-type-config  th {width:100px;background:#f6f6f6;}
.display-type-config  td {background:#ffffff;}

.template-box {border:solid 2px #e5e5e5;  height:185px; width:830px; position:absolute; overflow:hidden;}
.template-slide {height:185px; position:absolute; float:left; overflow:hidden; left:20px;}
.template-info {height:30px; font-family:돋움; font-size:12px; color:#999999; font-weight:bold; position:relative;}
.template-border {border:solid 2px #FFFFFF; width:170px; height:160px; float:left; margin:5px 11px 10px 11px; }
.template {width:160px; height:160px; float:left; margin:0px 5px 0px 5px;}

.template-arrow-left { width:20px; height:185px; float:left; padding:0px 5px 0px 5px;line-height:160px; background-color:#ffffff; position:relative; z-index:99; }
.template-arrow-right {width:20px; height:185px; float:right; padding:0px 5px 0px 5px;line-height:160px; position:relative; background-color:#ffffff; z-index:99; }
.template-name {width:160px; height:10px; padding: 5px 0px 5px 0px; text-align:center; position:relative; font:8pt dotum; color:#6D6D6D}
.template-thumb {width:160px; background-color:#e6e6e6; height:110px; text-align:center; position:relative;}
.template-btn {width:160px; height:20px; padding: 5px 0px 5px 0px; text-align:center; position:relative;}
.template-btn-left {width:80px; height:20px; text-align:left; position:relative; float:left;}
.template-btn-right {width:80px; height:20px; text-align:right; position:relative; float:left;}
.template-use {border:dashed 2px #FF0000;}
</style>
<script type="text/javascript">
function setTemplate(tp_idx) {
	if(confirm('템플릿을 적용하시겠습니까?')) {
		var frm = document.form;
		frm.mode.value="design_template";
		frm.tp_idx.value = tp_idx;
		frm.submit();
	}
}

function editTemplate(tp_idx) {
	if(parent.editTemplate) {
		parent.editTemplate(tp_idx);
	}
}

function deleteTemplate(tp_idx) {
	<? if($use_template_idx) { ?>
		if(tp_idx == "<?=$use_template_idx?>") {
			alert('현재 적용되어 있는 템플릿은 삭제 하실 수 없습니다.');
			return;
		}
	<? } ?>
	if(confirm('템플릿을 삭제하시겠습니까?')) {
		var frm = document.form;
		frm.mode.value= "del_template";
		frm.tp_idx.value = tp_idx;
		frm.submit();
	}
	
}

var i =0;
var slide;
var f_left;
var now_left;
var interval;
var template_pagenum;
var now_pagenum = 1;

template_pagenum = <?=$template_page_num?>;

function nextTemplate() {
	i = i + 196;

	if(i == 196 * 4 || i > 196 * 4) { clearInterval(interval); }
	
	f_left = f_left + 196;
	slide.style.left = f_left;
}

function prevTemplate() {
	
	i = i + 196;

	if(i == 196 * 4 || i > 196 * 4) { clearInterval(interval); }

	f_left = f_left - 196;
	slide.style.left = f_left;
}

function moveTemplate(move_type) {
	i = 0;
	if(move_type == 'prev') {
		if(now_pagenum == 1) {
			return;
		}

		interval = setInterval("nextTemplate()", 30);
		now_pagenum = now_pagenum - 1;
	}
	else {

		if(now_pagenum == template_pagenum) {
			return;
		}

		interval = setInterval("prevTemplate()", 30);
		now_pagenum = now_pagenum + 1;
	}

	if(now_pagenum == 1) {
		document.getElementById('prev_arrow').src = '../img/btn_b_next_off.gif';
	}
	else {
		document.getElementById('prev_arrow').src = '../img/btn_b_next.gif';
	}
	
	if(now_pagenum == template_pagenum) {
		document.getElementById('next_arrow').src = '../img/btn_b_prev_off.gif';
	}
	else {
		document.getElementById('next_arrow').src = '../img/btn_b_prev.gif';
	}
}

</script>
<form name=form method=post action="indb.php" onsubmit="return chkForm(this)" enctype="multipart/form-data">
<input type=hidden name=mode value="design_template">
<input type=hidden name="menu_idx" value="<?=$_GET['menu_idx']?>">
<input type=hidden name="tp_idx" value="">
<div class="template-box">
	
	<div class="template-arrow-left"><div style="position:absolute;top:45%;left:10px;"><a href="javascript:moveTemplate('prev');"><img id="prev_arrow" src="../img/btn_b_next.gif" alt="이전" align="absmiddle" ></a></div></div>
	<div class="template-slide" id="template_slide" style="width:<?=$slide_width?>;">
	<? if($_GET['menu_idx'] == '') { ?>
		<div class="template-info"><div style="position:absolute;top:50%;"><img src="../img/img_check.gif" align="absmiddle"> 카테고리 트리에서 카테고리를 먼저 선택해 주세요.</div></div>
	<? } else { ?>
	<? 

		if(empty($arr_template)) { ?>
		<div class="template-info"><div style="position:absolute;top:50%;">아래 템플릿 선택에서 편집 하시면 나의 템플릿에 추가 됩니다.</div></div>
		<? } else {

		$i = 0;
		$use_i = 0;
		if(!empty($arr_template) && is_array($arr_template)) {
			foreach($arr_template as $row_template) {
				$use_class = '';
				if(is_array($row_template)) {
					if($use_template_idx === $row_template['tp_idx']) {
						$use_class = 'template-use';
						$use_i = $i;
					}
	?>
	<div class="template-border <?=$use_class?>">
	<div class="template " id="div_template_<?=$i?>">
		<div class="template-name"><?=$row_template['name']?></div>
		<div class="template-thumb"><img style="width:160px;height:110px;" src="<?=$row_template['thumb_160x110']?>" onError="this.src='../img/no_thumnail.gif'" /></div>
		<div class="template-btn">
			<div class="template-btn-left"><a href="javascript:editTemplate('<?=$row_template['tp_idx']?>');"><img src="../img/btn_s_edit.gif" alt="편집"></a><a href="javascript:deleteTemplate('<?=$row_template['tp_idx']?>');"><img src="../img/btn_s_del.gif" alt="삭제"></a></div>
			<div class="template-btn-right"><a href="javascript:setTemplate('<?=$row_template['tp_idx']?>');"><img src="../img/btn_s_apply.gif" alt="적용"></a></div>
		</div>
	</div>
	</div>
	<? 
					$i ++;
				} 
			}
		}
	}
	?>
	<? } ?>
	</div>	
	<div class="template-arrow-right"><div style="position:absolute;top:45%;right:10px;"><a href="javascript:moveTemplate('next');"><img id="next_arrow" src="../img/btn_b_prev.gif" alt="다음"></a></div></div>

</div>

</form>

<script>
table_design_load();
window.onload = function(){
	parent.document.getElementById('ifrmMyTemplate').style.height = document.body.scrollHeight + 20;
}

slide = document.getElementById('template_slide');
f_left = 20;

<? 
$use_i = $use_i;
$val_use_i = floor($use_i/4);

for($i = 0; $i<$val_use_i; $i++) {
?>
	i = i - (196 * 4);

	f_left = f_left - (196 * 4);
	slide.style.left = f_left;
	now_pagenum = now_pagenum + 1;
<? } ?>

if(now_pagenum == 1) {
	document.getElementById('prev_arrow').src = '../img/btn_b_next_off.gif';
}

if(template_pagenum == 1) {
	document.getElementById('next_arrow').src = '../img/btn_b_prev_off.gif';
}

if(now_pagenum == template_pagenum) {
	document.getElementById('next_arrow').src = '../img/btn_b_prev_off.gif';
}

</script>