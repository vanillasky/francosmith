<?
/*********************************************************
* 파일명     :  event_register.php
* 프로그램명 :	모바일샵 이벤트 등록
* 작성자     :  dn
* 생성일     :  2012.05.11
**********************************************************/

$location = "모바일샵 > 이벤트 등록";
include "../_header.php";
include "../../conf/design.main.php";
@include_once('../../lib/json.class.php');

$mevent_no = $_GET['mevent_no'];

if($mevent_no) {
	$event_query = $db->_query_print('SELECT * FROM '.GD_MOBILE_EVENT.' WHERE mevent_no=[i]', $mevent_no);
	$res_event = $db->_select($event_query);
	$event_data = $res_event[0];
	$event_data['start_date'] = substr($event_data['start_date'], 0, 4).substr($event_data['start_date'], 5, 2).substr($event_data['start_date'], 8, 2);
	$event_data['end_date'] = substr($event_data['end_date'], 0, 4).substr($event_data['end_date'], 5, 2).substr($event_data['end_date'], 8, 2);

	if($event_data['category']) {
		$cate_query = $db->_query_print('SELECT catnm FROM '.GD_CATEGORY.' gd_category WHERE category=[s]', $event_data['category']);
		$res_cate = $db->_select($cate_query);
		$event_data['catnm'] = $res_cate[0]['catnm'];
	}

	if($event_data['tpl'] == 'tpl_05') {
		$json = new Services_JSON(16);
		$tab_data = $json->decode($event_data['tpl_opt']);
		$display_query = $db->_query_print('SELECT md.goodsno, md.tab_no, g.img_s, g.img_mobile, g.goodsnm, go.price FROM '.GD_MOBILE_DISPLAY.' md LEFT JOIN '.GD_GOODS.' g ON md.goodsno=g.goodsno LEFT JOIN '.GD_GOODS_OPTION.' go ON g.goodsno=go.goodsno and go_is_deleted <> \'1\' WHERE md.mevent_no=[i] AND go.link=1 ORDER BY md.sort ASC', $mevent_no);

		$res_display = $db->_select($display_query);

		foreach($res_display as $row_display) {
			$loop[$row_display['tab_no']][] = $row_display;
		}
	}
	else {

		$display_query = $db->_query_print('SELECT md.goodsno, g.img_s, g.img_mobile, g.goodsnm, go.price FROM '.GD_MOBILE_DISPLAY.' md LEFT JOIN '.GD_GOODS.' g ON md.goodsno=g.goodsno LEFT JOIN '.GD_GOODS_OPTION.' go ON g.goodsno=go.goodsno and go_is_deleted <> \'1\' WHERE md.mevent_no=[i] AND go.link=1 ORDER BY md.sort ASC', $mevent_no);
		$res_display = $db->_select($display_query);

		$loop = $res_display;
	}

}

$checked['tpl'][$event_data['tpl']] = 'checked';

$arr_tpl_type = array(1 => '갤러리형');	# 갤러리형은 지원안함
$arr_tpl_type[] = '리스트형';				# 리스트형은 지원안함
$arr_tpl_type[] = '상품스크롤형';
$arr_tpl_type[] = '이미지스크롤형';
$arr_tpl_type[] = '탭';
?>
<style>
.display-type-wrap {width:94px;float:left;margin:3px;}
.display-type-wrap img {border:none;width:94px;height:72px;}
.display-type-wrap div {text-align:center;}
xmp.extra-display-form-tplsrc {margin:0;font-size:11px;}
</style>
<script type="text/javascript">
function setTplType(tpl_no) {

	$('line-cnt').style.display = 'none';
	$('disp-cnt').style.display = 'none';
	$('display-type').style.display = 'none';
	$('tab-config').style.display = 'none';

	setDisabled($('line-cnt'), true);
	setDisabled($('disp-cnt'), true);
	setDisabled($('display-type'), true);
	setDisabled($('tab-config'), true);

	switch (tpl_no) {
		case 'tpl_05' :
			$('line-cnt').style.display = '';
			$('disp-cnt').style.display = '';
			$('tab-config').style.display = '';
			setDisabled($('line-cnt'), false);
			setDisabled($('disp-cnt'), false);
			setDisabled($('tab-config'), false);
			changeTabNum($('tab_num').value);
			break;
		default :
			$('line-cnt').style.display = '';
			$('disp-cnt').style.display = '';
			$('display-type').style.display = '';
			setDisabled($('line-cnt'), false);
			setDisabled($('disp-cnt'), false);
			setDisabled($('display-type'), false);
			break;
	}

}

function setDisabled(obj, bool_disabled) {
	var inputs = obj.getElementsByTagName('input');

	for(var i=0; i<inputs.length; i++) {
		inputs[i].disabled = bool_disabled;
	}

	var selects = obj.getElementsByTagName('select');

	for(var i=0; i<selects.length; i++) {
		selects[i].disabled = bool_disabled;
	}

}
function setInitialConfig() {

	var arr_tpl = document.getElementsByName('tpl');

	var tpl_no = '';
	for (var i=0; i<arr_tpl.length; i++) {
		if(arr_tpl[i].checked == true) {
			tpl_no = arr_tpl[i].value;
		}
	}

	if(tpl_no) {
		setTplType(tpl_no);
	}

	if(tpl_no != 'tpl_05' && tpl_no != 'tpl_07') {

		var arr_display_type = document.getElementsByName('display_type');

		var display_type_no = '';
		for (var i=0; i<arr_display_type.length; i++) {
			if(arr_display_type[i].checked == true) {
				display_type_no = arr_display_type[i].value;
			}
		}

		setDisplayType(display_type_no);
	}
	else if(tpl_no == 'tpl_05') {
		changeTabNum($('tab_num').value);
	}
	else if(tpl_no == 'tpl_07') {
		changeBannerNum($('banner_num').value);
	}

}

function changeTabNum(num) {
	var tbl = $('tab-config-tbl');

	for(var i=0; i<4; i++) {
		var tab_num = i + 1;

		$('tab-name'+tab_num).style.display = 'none';
		$('tab-goods'+tab_num).style.display = 'none';

		setDisabled($('tab-name'+tab_num), true);
		setDisabled($('tab-goods'+tab_num), true);
	}

	for(i=0; i<num; i++) {
		var tab_num = i + 1;

		$('tab-name'+tab_num).style.display = '';
		$('tab-goods'+tab_num).style.display = '';

		setDisabled($('tab-name'+tab_num), false);
		setDisabled($('tab-goods'+tab_num), false);
	}
}

function setInitialConfig() {

	var arr_tpl = document.getElementsByName('tpl');

	var tpl_no = '';
	for (var i=0; i<arr_tpl.length; i++) {
		if(arr_tpl[i].checked == true) {
			tpl_no = arr_tpl[i].value;
		}
	}

	if(tpl_no) {
		setTplType(tpl_no);
	}

	if(tpl_no == 'tpl_05') {
		changeTabNum($('tab_num').value);
	}

}

document.observe('dom:loaded', function() {
	setInitialConfig();
});

</script>
<div class="title title_top">이벤트 등록 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=mobileshopV2&no=12')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<form name="form" method="post" action="indb.php" onsubmit="return chkForm(this)" enctype="multipart/form-data">
<input type="hidden" name="mode" value="event_regist" />
<input type="hidden" name="mevent_no" id="mevent_no" value="<?=$mevent_no?>" />
<table class="tb">
<col class="cellC"><col class="cellL">
<tbody style="height:26px;">
<tr>
	<td>이벤트제목</td>
	<td>
		<input type="text" name="event_title" size="50" value="<?=$event_data['event_title']?>" required="required" />
	</td>
</tr>
<tr>
	<td>이벤트기간</td>
	<td>
		<input type="text" name="start_date" size="10" value="<?=$event_data['start_date']?>" onclick="calendar(event);" class="cline" />&nbsp;~&nbsp;<input type="text" name="end_date" size="10" value="<?=$event_data['end_date']?>" onclick="calendar(event);" class="cline" />
	</td>
</tr>
<tr>
	<td>이벤트내용<br />디자인 & HTML입력</td>
	<td>
		<textarea name="event_body" style="width:100%;height:300px" type=editor><?=stripslashes($event_data['event_body'])?></textarea>
		<script src="../../lib/meditor/mini_editor.js"></script>
		<script>mini_editor("../../lib/meditor/");</script>
	</td>
</tr>
<tr>
	<td>분류(카테고리)<br/ >만들기</td>
	<td >
		<div>
			<input type="text" name="catnm" size="50" value="<?=$event_data['catnm']?>" maxlen="30" />
			<input type="hidden" name="category" value="<?=$event_data['category']?>" />
		</div>
		<div>
			<span class="extext">
				* 일반 카테고리와 똑같은 기능의 1차 카테고리가 자동으로 만들어지고, 분류감춤모드로 설정됩니다<br />
				* 분류를 만든 후, 분류보임으로 수정 또는 분류를 삭제하려면 <a href="../goods/category.php" target="_blank"><font class="extext_l">[카테고리 관리]</font></a> 에서 관리하세요
			</span>
		</div>
	</td>
</tr>
<tr>
	<td>디스플레이유형</td>
	<td >
		<? for ($i=3;$i<count($arr_tpl_type)+1;$i++) { ?>
		<div class="display-type-wrap">
			<img src="../img/m_goodalign_style_<?=sprintf('%02d',$i)?>.jpg"  alt="<?=$arr_tpl_type[$i]?>" />
			<div class="noline">
				<input type="radio" name="tpl" value="tpl_<?=sprintf('%02d',$i)?>" <?=$checked['tpl']['tpl_'.sprintf('%02d',$i)]?> onClick="javascript:setTplType(this.value); "required="required"  />
			</div>
		</div>
		<? } ?>
	</td>
</tr>
<tr id="line-cnt" style="display:none;">
	<td>출력 라인수</td>
	<td><input type="text" name="line_cnt" value="<?=$event_data['line_cnt']?>" class="rline" disabled /> 개 <font class="extext">메인페이지에 보여지는 라인수입니다</td>
</tr>
<tr id="disp-cnt" style="display:none;">
	<td>라인당 상품수</td>
	<td><input type="text" name="disp_cnt" value="<?=$event_data['disp_cnt']?>" class="rline" disabled /> 개 <font class="extext">한줄에 보여지는 상품수입니다</td>
</tr>
<tr id="display-type" style="display:none;">
	<td>상품진열<br />
		<a href="javascript:popup('http://guide.godo.co.kr/guide/php/ex_display.html',850,523)"><font class="extext_l">[상품순서변경 방법]</font></a>
	</td>
	<td>
		<div style="padding:5px 0px 0px 0px;"><img src="../img/btn_goodsChoice.gif" class="hand" onclick="javascript:popupGoodschoice('e_step[]', 'stepX');" align="absmiddle" /> <font class="extext">※주의: 상품선택 후 반드시 하단 등록(수정)버튼을 누르셔야 최종 저장이 됩니다.</font></div>
		<div id="stepX" style="padding-top:3px;">
			<?php
			if ($loop){
				foreach ($loop as $v){
			?>
				<a href="../../goods/goods_view.php?goodsno=<?php echo $v['goodsno']; ?>" target="_blank"><?php echo goodsimg($v['img_s'], '40,40', '', 1); ?></a>
				<input type=hidden name="e_step[]" value="<?php echo $v['goodsno']; ?>" />
			<?php
				}
			}
			?>
		</div>
	</td>

</tr>
<tr id="tab-config" style="display:none;">
	<td>탭설정</td>
	<td>
		<div>
			<table id="tab-config-tbl" class="tb">
			<col class="cellC" /><col class="cellL" />
			<tr>
				<th>탭 개수</th>
				<td class="noline">
				<select name="tab_num" id="tab_num" onChange="javascript:changeTabNum(this.value);" disabled >
					<? for($i = 1; $i < 5; $i++) { ?>
					<option value="<?=$i?>" <?if($tab_data['tab_num'] == $i){?> selected <?}?>><?=$i?></option>
					<? } ?>
				</select> 개
				<font class="extext">탭의 개수 입니다.</font>
				</td>
			</tr>
			<? for($i = 1; $i < 5; $i++) {?>
			<tr id="tab-name<?=$i?>" <? if($i != 1) { ?>style="display:none;" <? } ?>>
				<th><?=$i?>번탭 이름</th>
				<td>
					<input type="text" name="tab_name[]" value="<?=$tab_data['tab_name'][$i]?>" class="rline" disabled />
				</td>
			</tr>
			<tr id="tab-goods<?=$i?>" <? if($i != 1) { ?>style="display:none;" <? } ?>>
				<th><?=$i?>번탭 상품진열</th>
				<td>
					<div style="padding:5px 0px 0px 0px;"><img src="../img/btn_goodsChoice.gif" class="hand" onclick="javascript:popupGoodschoice('e_tab_step<?=$i?>[]', 'tab_step<?=$i?>X');" align="absmiddle" /> <font class="extext">※주의: 상품선택 후 반드시 하단 등록(수정)버튼을 누르셔야 최종 저장이 됩니다.</font></div>
					<div id="tab_step<?=$i?>X" style="padding-top:3px;">
						<?php
						if ($loop[$i]){
							foreach ($loop[$i] as $v){
						?>
							<a href="../../goods/goods_view.php?goodsno=<?php echo $v['goodsno']; ?>" target="_blank"><?php echo goodsimg($v['img_s'], '40,40', '', 1); ?></a>
							<input type=hidden name="e_tab_step<?=$i?>[]" value="<?php echo $v['goodsno']; ?>" />
						<?php
							}
						}
						?>
					</div>
				</td>
			</tr>
			<? } ?>
			</table>
		</div>
	</td>
</tr>
</table>
<div class=button>
<? if($mevent_no) { ?>
	<input type=image src="../img/btn_modify.gif">
<? }else{ ?>
	<input type=image src="../img/btn_register.gif">
<? } ?>
</div>
</form>
<? include "../_footer.php"; ?>