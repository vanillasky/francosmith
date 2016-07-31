<?
/*********************************************************
* ���ϸ�     :  disp_main_form.php
* ���α׷��� :	�������� ��
* �ۼ���     :  dn
* ������     :  2012.04.26
**********************************************************/

@include('../_header.popup.php');
@include('../../lib/json.class.php');

$mdesign_no = $_GET['mdesign_no'];
$content_no = $_GET['content_no'];

$design_query = $db->_query_print('SELECT * FROM '.GD_MOBILE_DESIGN.' WHERE mdesign_no=[i]', $mdesign_no);
$res_design = $db->_select($design_query);
$design_data = $res_design[0];
$design_data['sort_type'] = $design_data['sort_type'] ? $design_data['sort_type'] : 1;
$design_data['select_date'] = $design_data['select_date'] ? $design_data['select_date'] : 7;
$design_data['regdt'] = $design_data['regdt'] ? $design_data['regdt'] : '';
$mobile_categoods_arr = array_filter(explode(",",$design_data['mobile_categoods']));
$price_arr = array_filter(explode(",",$design_data['price']));
$stock_amount_arr = array_filter(explode(",",$design_data['stock_amount']));

$json = new Services_JSON(16);
$goodsDisplay = Core::loader('Mobile2GoodsDisplay');
$mobileGoodsDisplay = $goodsDisplay->isMobileDisplay();

$mainAutoSort = Core::loader('mainAutoSort');

$arr_tpl_type = array(1 => '��������');
$arr_tpl_type[] = '����Ʈ��';
$arr_tpl_type[] = '��ǰ��ũ����';
$arr_tpl_type[] = '�̹�����ũ����';
$arr_tpl_type[] = '��';
$arr_tpl_type[] = '�Ű���';
$arr_tpl_type[] = '��ʷѸ���';

//���� ��ǰ���� ����
$ar_sort_type = array(1 => '��������');
$ar_sort_type[] = '�α��(�Ǹűݾ�)';
$ar_sort_type[] = '�α��(�ǸŰ���)';
$ar_sort_type[] = '��ǰ�򰡼�';

$checked['chk'][$design_data['chk']] = 'checked';
$checked['tpl'][$design_data['tpl']] = 'checked';
$checked['stock_type'][$design_data['stock_type']] = 'checked';
$checked['display_type'][$design_data['display_type']] = 'checked';
$checked['sort_type'][$design_data['sort_type']] = 'checked';
$selected['select_date'][$design_data['select_date']] = 'selected';
$selected['regdt'][$design_data['regdt']] = 'selected';

$loop = Array();

switch($design_data['tpl']) {
	case 'tpl_05' :
		$tab_data = $json->decode($design_data['tpl_opt']);

		$display_query = $db->_query_print('SELECT md.goodsno, md.tab_no, g.img_s, g.img_mobile, g.goodsnm, go.price FROM '.GD_MOBILE_DISPLAY.' md LEFT JOIN '.GD_GOODS.' g ON md.goodsno=g.goodsno LEFT JOIN '.GD_GOODS_OPTION.' go ON g.goodsno=go.goodsno and go_is_deleted <> \'1\' WHERE md.mdesign_no=[i] AND go.link=1 ORDER BY md.sort ASC', $mdesign_no);

		$res_display = $db->_select($display_query);

		foreach($res_display as $row_display) {
			$loop[$row_display['tab_no']][] = $row_display;
		}
		break;
	case 'tpl_07' :
		$banner_data = $json->decode($design_data['tpl_opt']);

		$display_query = $db->_query_print('SELECT * FROM '.GD_MOBILE_DISPLAY.' WHERE mdesign_no=[i] ORDER BY sort ASC', $mdesign_no);
		$res_display = $db->_select($display_query);

		foreach($res_display as $row_display) {
			$loop[$row_display['banner_no']] = $row_display['temp1'];
		}
		break;
	default :
		if (!$design_data['sort_type'] || $design_data['sort_type'] == '1') {
			if($design_data['display_type'] == '1') {
				$display_query = $db->_query_print('SELECT md.goodsno, g.img_s, g.img_mobile, g.goodsnm, go.price FROM '.GD_MOBILE_DISPLAY.' md LEFT JOIN '.GD_GOODS.' g ON md.goodsno=g.goodsno LEFT JOIN '.GD_GOODS_OPTION.' go ON g.goodsno=go.goodsno and go_is_deleted <> \'1\' WHERE md.mdesign_no=[i] AND go.link=1 ORDER BY md.sort ASC', $mdesign_no);
				$res_display = $db->_select($display_query);

				$loop = $res_display;
			} else {
				$display_query = $db->_query_print('SELECT category, temp2 FROM '.GD_MOBILE_DISPLAY.' WHERE mdesign_no=[i] ORDER BY sort ASC', $mdesign_no);
				$res_display = $db->_select($display_query);

				if($design_data['display_type'] == '2') {

					$loop['categoods'] = $res_display;
				}
				else if($design_data['display_type'] == '3') {
					$loop['catelist'] = $res_display;
				}
			}
		} else {
			list($add_table, $add_where, $add_order) = $mainAutoSort->getSortTerms($mobile_categoods_arr, $price_arr, $design_data['stock_type'], $stock_amount_arr, $design_data['regdt'], 'sort'.$design_data['sort_type']."_".$design_data['select_date']);

			$display_query = "
				SELECT 
					".$mainAutoSort->use_table.".goodsno,".GD_GOODS.".goodsnm,".GD_GOODS.".img_s, ".GD_GOODS_OPTION.".price
				FROM 
					".$mainAutoSort->use_table."
					".$add_table."
				where
					".GD_GOODS.".open AND ".GD_GOODS_OPTION.".link=1
					".$add_where."
				".$add_order."
				limit
					".$mainAutoSort->sort_limit."
			";
			$res_display = $db->_select($display_query);
			$auto_loop = $res_display;
		}
		break;
}

?>
<style>
.display-type-wrap {width:94px;float:left;margin:3px;}
.display-type-wrap img {border:none;width:94px;height:72px;}
.display-type-wrap div {text-align:center;}

xmp.extra-display-form-tplsrc {margin:0;font-size:11px;}
.add_categoods_box {float:left; background:#f3f3f3; padding:5px; margin-top:5px; display:block; clear:both;}
</style>

<script type="text/javascript">
<?php if ($mobileGoodsDisplay) { ?>
var autoDisplayGuideElement = window.parent.window.document.getElementById("auto-display-guide");
if (autoDisplayGuideElement) autoDisplayGuideElement.style.display = "none";
<?php } ?>

function addCate(name) {
	var cate = document.getElementsByName("step_"+name+"[]");
	var cate_nm = "";
	var cate_val = "";

	for(var i =0; i< cate.length; i++) {


		if(cate[i].value != "") {
			cate_val = cate[i].value;

			if(i == 0) {
				cate_nm = cate[i].options[cate[i].selectedIndex].text;
			}
			else {
				cate_nm += " > " + cate[i].options[cate[i].selectedIndex].text;
			}
		}
	}

	if(cate_val == "") {
		alert("ī�װ��� ������ �ּ���.");
		cate[0].focus();
		return;
	}

	var cate_hidden = document.getElementsByName(name+'[]');

	for(var j=0; j< cate_hidden.length; j++) {

		if(cate_hidden[j].value == cate_val) {
			alert("�̹� �߰��� ī�װ� �Դϴ�.");
			return;
		}
	}

	var id = document.getElementById('tb_'+name);
    var len = id.rows.length;
    var newRow = id.insertRow(len);
	newRow.id = 'tr_'+name+len;

    var td0 = newRow.insertCell(0);
	td0.colSpan="2";
	td0.style.fontWeight="normal";

	var html_str = '<div>' + cate_nm + ' <input type="hidden" name="'+name+'[]" value="'+cate_val+'" />';
	html_str += '&nbsp;&nbsp;&nbsp;<a href="javascript:delCate(\''+name+'\', \''+ newRow.id +'\');"><img src="../img/i_del.gif" align=absmiddle /></a></div>';

	if(name == 'catelist') {
		html_str += '<div><input type="file" name="cate_img[]" class="rline" size="50" /></div>';
	}

	td0.innerHTML = html_str;
	setFrameHeight();
}

function delCate(name, tr_id) {

	var id = document.getElementById('tb_' + name);
	var tr = document.getElementById(tr_id);
	if(name=='catelist') {

		var ele = tr.getElementsByTagName('INPUT');
		if(ele.length == 4) {
			if(ele[3].value) {
				var mode = "del_upload_img";
				var key;
				var ajax = new Ajax.Request('./indb.php', {
					method: "post",
					parameters: 'mode='+mode+'&img_name='+ele[3].value,
					asynchronous: false,
					onComplete: function(response) { if (response.status == 200) {

					}}
				});
			}
		}
	}

    id.deleteRow(tr.rowIndex);
}

function setTplType(tpl_no) {
	var display_type_value = $$("input:checked[name='display_type']").length > 0 ? $$("input:checked[name='display_type']")[0].value : '';
	var sort_type_value = $$("input:checked[name='sort_type']").length > 0 ? $$("input:checked[name='sort_type']")[0].value : '';
	var sort_type_view = display_type_view = 'none';

	if (sort_type_value != 1) sort_type_view = '';
	if (sort_type_value == 1) display_type_view = '';
	
	$('line-cnt').style.display = 'none';
	$('disp-cnt').style.display = 'none';
	$('banner-width').style.display = 'none';
	$('banner-height').style.display = 'none';
	$('display-type').style.display = 'none';
	$('tab-config').style.display = 'none';
	$('banner-config').style.display = 'none';
	$('banner-desc').style.display = 'none';
	$('magazine-desc').style.display = 'none';

	setDisabled($('line-cnt'), true);
	setDisabled($('disp-cnt'), true);
	setDisabled($('banner-width'), true);
	setDisabled($('banner-height'), true);
	setDisabled($('display-type'), true);
	setDisabled($('tab-config'), true);
	setDisabled($('banner-config'), true);

	switch (tpl_no) {
		case 'tpl_05' :
			$('line-cnt').style.display = '';
			$('disp-cnt').style.display = '';
			$('tab-config').style.display = '';
			$('sort_type_tr').style.display = 'none';
			$('display-type').style.display = 'none';
			setDisabled($('line-cnt'), false);
			setDisabled($('disp-cnt'), false);
			setDisabled($('tab-config'), false);
			changeTabNum($('tab_num').value);
			$$('.auto_main_sort_tr').each(function(e){
				e.setStyle({display:'none'});
			});
			break;
		case 'tpl_06' :
			$('display-type').style.display = display_type_view;
			$('banner-height').style.display = '';
			$('magazine-desc').style.display = '';
			$('sort_type_tr').style.display = '';
			setDisabled($('display-type'), false);
			setDisabled($('banner-height'), false);
			$$('.auto_main_sort_tr').each(function(e){
				e.setStyle({display:sort_type_view});
			});
			break;
		case 'tpl_07' :
			$('banner-width').style.display = '';
			$('banner-height').style.display = '';
			$('banner-config').style.display = '';
			$('banner-desc').style.display = '';
			$('sort_type_tr').style.display = 'none';
			$('display-type').style.display = 'none';
			setDisabled($('banner-width'), false);
			setDisabled($('banner-height'), false);
			setDisabled($('banner-config'), false);
			changeBannerNum($('banner_num').value);
			$$('.auto_main_sort_tr').each(function(e){
				e.setStyle({display:'none'});
			});
			break;
		default :
			$('line-cnt').style.display = '';
			$('disp-cnt').style.display = '';
			$('display-type').style.display = display_type_view;
			$('sort_type_tr').style.display = '';
			setDisabled($('line-cnt'), false);
			setDisabled($('disp-cnt'), false);
			setDisabled($('display-type'), false);
			$$('.auto_main_sort_tr').each(function(e){
				e.setStyle({display:sort_type_view});
			});
			break;
	}

	setFrameHeight();
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

function setDisplayType(disp_no) {
	var sort_type_value = $$("input:checked[name='sort_type']").length > 0 ? $$("input:checked[name='sort_type']")[0].value : '';
	
	$('display-type-goodslist').style.display = 'none';
	$('display-type-categoodslist').style.display = 'none';
	$('display-type-catelist').style.display = 'none';

	setDisabled($('display-type-goodslist'), true);
	setDisabled($('display-type-categoodslist'), true);
	setDisabled($('display-type-catelist'), true);

	switch (disp_no) {
		case '1' :
			$('display-type-goodslist').style.display = '';
			setDisabled($('display-type-goodslist'), false);
			break;
		case '2' :
			$('display-type-categoodslist').style.display = '';
			setDisabled($('display-type-categoodslist'), false);
			break;
		case '3' :
			$('display-type-catelist').style.display = '';
			setDisabled($('display-type-catelist'), false);
			break;
	}

	sort_type_view(sort_type_value);
	setFrameHeight();

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

	setFrameHeight();
}

function changeBannerNum(num) {
	var tbl = $('banner-config-tbl');

	for(var i=0; i<5; i++) {
		var banner_num = i + 1;

		$('banner-img'+banner_num).style.display = 'none';
		$('banner-link'+banner_num).style.display = 'none';

		setDisabled($('banner-img'+banner_num), true);
		setDisabled($('banner-link'+banner_num), true);
	}

	for(i=0; i<num; i++) {
		var banner_num = i + 1;

		$('banner-img'+banner_num).style.display = '';
		$('banner-link'+banner_num).style.display = '';

		setDisabled($('banner-img'+banner_num), false);
		setDisabled($('banner-link'+banner_num), false);
	}

	setFrameHeight();
}

function setInitialConfig() {

	var arr_tpl = document.getElementsByName('tpl');
	var sort_type_value = $$("input:checked[name='sort_type']").length > 0 ? $$("input:checked[name='sort_type']")[0].value : '';
	var display_type_disabled = sort_type_value == 1 ? false : true;

	setDisabled($('display-type'), display_type_disabled);

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
	
	sort_type_view(sort_type_value);
	setFrameHeight();
}

function setFrameHeight() {
	parent.document.getElementById('ifrm_form<?=$mdesign_no?>').style.height = document.body.scrollHeight;
}

function delDesignForm(idx) {

	if (confirm('�߰���ǰ���� ���� ���� �Ͻðڽ��ϱ�?'))
	{
		var mode = 'design_delete';

		var ajax = new Ajax.Request('./indb.php', {
			method: "post",
			parameters: 'mode='+mode+'&mdesign_no='+idx,
			asynchronous: false,
			onComplete: function(response) { if (response.status == 200) {
				var json = response.responseText.evalJSON(true);

				if(json.result == 'OK') {
					parent.location.reload();
				}
				else {
					alert('��ǰ ���� ���� ���� �� ������ �߻� �Ͽ����ϴ�. �ٽ� �õ����ּ���.');
					return;
				}
			}}
		});
	}
}

function modDesignForm() {

	var elem = document.getElementById("form");
	if(chkForm(elem)) {
		if ($$("input[type=checkbox][name='chk']")[0].checked === true && $$("input:checked[name='sort_type']")[0].value != '1') {
			if (!$$("input[name='mobile_categoods[]']").length) {
				alert("���� ��� ī�װ��� ������ �ּ���.");
				document.getElementsByName('step_mobile_categoods[]')[0].focus();
				return;
			} else {
				var width = document.body.scrollWidth;
				var height = document.body.scrollHeight;
				var imgPosition = document.body.scrollTop + (document.body.clientHeight/2 - 58);
				$('onLoading-hide-layer').setStyle({width:width+'px',height:height+'px',display:'block'});
				$('onLoading-img').setStyle({margin:imgPosition+'px 0 0 0'});
			}
		}
		elem.submit();
	}
}

function copyDisplaySrc(obj_id) {

	if($(obj_id).innerHTML){
		var obj_id_text = $(obj_id).innerHTML.replace(/&lt;/g,'<').replace(/&gt;/g,'>');
	}

	if ( window.clipboardData ) {
		alert("�ڵ带 �����Ͽ����ϴ�. \n���ϴ� ���� �ٿ��ֱ�(Ctrl+V)�� �Ͻø� �˴ϴ�~");
		window.clipboardData.setData("Text", obj_id_text);
	} else {
		prompt("�ڵ带 Ŭ������� ����(Ctrl+C) �Ͻð�. \n���ϴ� ���� �ٿ��ֱ�(Ctrl+V)�� �Ͻø� �˴ϴ�~", obj_id_text);
	}
}
document.observe('dom:loaded', function() {
	table_design_load();
	setInitialConfig();
});

function modTimeout()
{
	var formElement = document.getElementById("form");
	if (!chkForm(formElement)) {
		return false;
	}
	setTimeout(function() { modDesignForm() }, 500);
	return true;
}

function imgLoadErr(obj) {
	obj.src = "/shop/admin/img/img_blank.jpg";
}

function sort_type_view(value){
	var tpl_value = $$("input:checked[name='tpl']").length > 0 ? $$("input:checked[name='tpl']")[0].value : '';
	var display_type_value = $$("input:checked[name='display_type']").length > 0 ? $$("input:checked[name='display_type']")[0].value : '';
	var auto_main_sort_view = display_type_goodslist_view = 'none';

	if (tpl_value != 'tpl_05' && tpl_value != 'tpl_07') {
		if (value == 1) {
			display_type_goodslist_view = '';
		} else {
			auto_main_sort_view = '';
		}
	}
	
	$('display-type').style.display = display_type_goodslist_view;
	$$('.auto_main_sort_tr').each(function(e,key){
		e.setStyle({display:auto_main_sort_view});
	});

	setFrameHeight();
}

function mobile_addCate(name,idx) {
	var cate = document.getElementsByName("step_"+name+"[]");
	var cate_nm = "";
	var cate_val = "";

	for(var i =0; i< cate.length; i++) {


		if(cate[i].value != "") {
			cate_val = cate[i].value;

			if(i == 0) {
				cate_nm = cate[i].options[cate[i].selectedIndex].text;
			}
			else {
				cate_nm += " > " + cate[i].options[cate[i].selectedIndex].text;
			}
		}
	}

	if(cate_val == "") {
		alert("ī�װ��� ������ �ּ���.");
		cate[0].focus();
		return;
	}

	var cate_hidden = document.getElementsByName(name+'[]');

	for(var j=0; j< cate_hidden.length; j++) {

		if(cate_hidden[j].value == cate_val) {
			alert("�̹� �߰��� ī�װ� �Դϴ�.");
			return;
		}
	}
	
	var html_str = '<div id="add_categoods_'+cate_val+'">';
	html_str += '<div class="add_categoods_box">'+cate_nm+' &nbsp; <a href="javascript:mobile_delCate(\''+cate_val+'\');"><img src="../img/i_del.gif" align=absmiddle /></a></div>';
	html_str += '<input type="hidden" name="mobile_categoods[]" value="'+cate_val+'">';
	html_str += '</div>';

	new Insertion.Bottom('add_categoods_area',html_str);
	setFrameHeight();
}

function mobile_delCate(value){
	$('add_categoods_'+value).remove();
	setFrameHeight();
}

function more_terms(){
	if ($('more_terms').style.display == 'none') {
		$('more_terms').style.display = '';
		$('more_terms_btn').writeAttribute('src','../img/disp_btn_close.gif');
	} else {
		$('more_terms').style.display = 'none';
		$('more_terms_btn').writeAttribute('src','../img/disp_btn_open.gif');
	}
	setFrameHeight();
}
</script>
<style>
#onLoading-hide-layer {position:absolute; z-index:100; top:0; left:0; width:100%; height:100%; text-align:center; background:rgb(68, 81, 91); filter:alpha(opacity=70); opacity:0.7; display:none;}
</style>
<div id="onLoading-hide-layer"><img src="../img/admin_progress.gif" id="onLoading-img" /></div>

<form id="form" name="frmMainDisp" action="indb.php" method="post" enctype="multipart/form-data" target="ifrmHidden">
<input type="hidden" name="mode" value="disp_main">
<input type="hidden" name="mdesign_no" value="<?=$mdesign_no?>">
<input type="hidden" name="content_no" value="<?=$content_no?>">

<div class="title_sub"><?if($content_no){ echo '��ǰ����'.$content_no; } else { echo '�߰���ǰ����'; } ?> <a href="javascript:modDesignForm();"><img src="../img/i_edit.gif" align="absmiddle" /></a> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=mobileshopV2&no=4')"><img src="../img/btn_q.gif"  align="absmiddle" /></a></div>
<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td>����<?php if ($mobileGoodsDisplay) { ?> <img src="<?php echo $cfg['rootDir']; ?>/admin/img/icons/bullet_compulsory.gif" style="vertical-align: middle;"/><?php } ?></td>
	<td>
		<input type="text" name="title" value="<?=$design_data['title']?>" class="lline"<?php if ($mobileGoodsDisplay) { ?> fld_esssential msgR="[����] �� �ʼ� �Է»��� �Դϴ�."<?php } ?> />
	</td>
</tr>
<tr>
	<td>
		�������
		<? if(!$content_no){ ?>
		<a href="javascript:delDesignForm('<?=$mdesign_no?>');"><img src="../img/i_del.gif" align="absmiddle"></a>
		<? } ?>
	</td>
	<td class="noline">
		<label><input type="checkbox" name="chk" <?=$checked['chk']['on']?> /> üũ�� ������������ ����̵˴ϴ�</label>
	</td>
</tr>
<tr>
	<td>���÷�������<?php if ($mobileGoodsDisplay) { ?> <img src="<?php echo $cfg['rootDir']; ?>/admin/img/icons/bullet_compulsory.gif" style="vertical-align: middle;"/><?php } ?></td>
	<td>
	<? for ($i=3;$i<count($arr_tpl_type)+1;$i++) { ?>
	<div class="display-type-wrap">
		<img src="../img/m_goodalign_style_<?=sprintf('%02d',$i)?>.jpg"  alt="<?=$arr_tpl_type[$i]?>" />
		<div class="noline">
			<input type="radio" name="tpl" value="tpl_<?=sprintf('%02d',$i)?>" <?=$checked['tpl']['tpl_'.sprintf('%02d',$i)]?> onClick="javascript:setTplType(this.value); "<?php if ($mobileGoodsDisplay) { ?> fld_esssential msgR="[���÷�������] ������ �ʼ� �Դϴ�."<?php } ?> />
		</div>
	</div>
	<? } ?>
	</td>
</tr>
<tr id="line-cnt" style="display:none;">
	<td>��� ���μ�</td>
	<td><input type="text" name="line_cnt" value="<?=$design_data['line_cnt']?>" class="rline" disabled /> �� <font class="extext">������������ �������� ���μ��Դϴ�</td>
</tr>
<tr id="disp-cnt" style="display:none;">
	<td>���δ� ��ǰ��</td>
	<td><input type="text" name="disp_cnt" value="<?=$design_data['disp_cnt']?>" class="rline" disabled /> �� <font class="extext">���ٿ� �������� ��ǰ���Դϴ�</td>
</tr>
<tr id="banner-width" style="display:none;">
	<td>�̹��� ����ũ��</td>
	<td><input type="text" name="banner_width" value="<?=$design_data['banner_width']?>" class="rline" disabled /> px <font class="extext">����̹����� ����ũ�⸦ �����մϴ�</td>
</tr>
<tr id="banner-height" style="display:none;">
	<td>�̹��� ����ũ��</td>
	<td><input type="text" name="banner_height" value="<?=$design_data['banner_height']?>" class="rline" disabled /> px
	<span id="banner-desc" style="display:none"><font class="extext">����̹����� ����ũ�⸦ �����մϴ�</span>
	<span id="magazine-desc" style="display:none"><font class="extext" style="font-size:12px"><br>�Ű����������� ��ǰ�� ���̹����� �̿��Ͽ� ȭ�鿡 �����մϴ�.<br>���δ� ����Ʈ����� ȭ��ũ�⿡ ����ϴ�.<br>���μ��� ������� ���� ��, �̹����� ����ũ�Ⱑ ������ ����ũ�⸦ ����� ������ ���� ó���˴ϴ�.<br>  ����ũ�⸦ 0���� �����ϴ� ���, ����ũ�⿡ ������ ���� �ʽ��ϴ�.</span>
	</td>
</tr>
<tr id="sort_type_tr">
	<td>���� ��ǰ���� ����</td>
	<td>
		<? foreach ($ar_sort_type as $key => $value){?>
		<label><input type="radio" name="sort_type" value="<?=$key?>" <?=$checked['sort_type'][$key]?> onclick="sort_type_view('<?=$key?>')"><?=$value?></label>
		<? } ?><br />
		<font class=extext>* ���÷��������� ���� ������������ ������ ��� �������� ���ظ� ����� �� �ֽ��ϴ�.<br />* �α�� �� ��ǰ�򰡼� ���� ���� �� ��ǰ�� �ִ� <?=$mainAutoSort->sort_limit?>�� ������ ������ �˴ϴ�.</font>
	</td>
</tr>
<tr id="display-type">
	<td>���� ��� ����</td>
	<td class="noline"><label><input type="radio" name="display_type" value="1" <?=$checked['display_type']['1'] ?> onClick="javascript:setDisplayType(this.value);" disabled />��ǰ����</label>
		<label><input type="radio" name="display_type" value="2" <?=$checked['display_type']['2'] ?> onClick="javascript:setDisplayType(this.value);" disabled />ī�װ� �� ��ǰ����</label>
		<label><input type="radio" name="display_type" value="3" <?=$checked['display_type']['3'] ?> onClick="javascript:setDisplayType(this.value);" disabled />ī�װ��� �̵�</label>
		<div id="display-type-goodslist" style="display:none;">
			<table class="tb">
			<col class="cellC"><col class="cellL">
			<tr>
				<td>��ǰ����<br />
					<a href="javascript:popup('http://guide.godo.co.kr/guide/php/ex_display.html',850,523)"><font class="extext_l">[��ǰ�������� ���]</font></a>
				</td>
				<td>
					<div style="padding-top:5px;z-index:-10"><img src="../img/btn_goodsChoice.gif" class="hand" onclick="javascript:popupGoodschoice('e_step[]', 'stepX');" align="absmiddle" /> <font class="extext">������: ��ǰ���� �� �ݵ�� �ϴ� �����ư�� �����ž� ���� ������ �˴ϴ�.</font></div>
					<div style="position:relative;z-index:1000;">
						<div id="stepX" style="padding-top:3px">
							<? if ($loop){ foreach ($loop as $v){ ?>
								<?=goodsimg($v['img_s'],'40,40','',1)?>
								<input type="hidden" name="e_step[]" value="<?=$v['goodsno']?>" />
							<? }} ?>
						</div>
					</div>
				</td>
			</tr>
			</table>
		</div>
		<div id="display-type-categoodslist" style="display:none;">
			<table class="tb" id="tb_categoods">
			<col class="cellC" /><col class="cellL" />
			<tr>
				<td>ī�װ� �� ��ǰ����</td>
				<td>
					<script>new categoryBox('step_categoods[]',4,'','disabled');</script>
					<a href="javascript:addCate('categoods');"><img src="../img/i_add.gif" align="absmiddle" /></a>
				</td>
			</tr>
			<?
			$i = 0;
			if(!empty($loop['categoods']) && is_array($loop['categoods'])){
				foreach($loop['categoods'] as $v) {
			?>
				<tr id="tr_categoods<?=$i?>">
					<td colspan="2" style="font-weight:normal;">
					<? if(strip_tags(currPosition($v['category']))) { ?>
						<?=strip_tags(currPosition($v['category']))?>
					<? } else { ?>
						������ ī�װ�<span><font class=extext>(������ư�� ���� �����Ͻñ� �ٶ��ϴ�.)</font></span>
					<? } ?>
					<input type="hidden" name="categoods[]" value="<?=$v['category']?>" />&nbsp;&nbsp;&nbsp;
					<a href="javascript:delCate('categoods', 'tr_categoods<?=$i?>');"><img src="../img/i_del.gif" align=absmiddle /></a>
					</td>
				</tr>
			<?
				$i ++;
				}
			}
			?>
			</table>
		</div>
		<div id="display-type-catelist" style="display:none;">
			<table class="tb" id="tb_catelist">
			<col class="cellC" /><col class="cellL" />
			<tr>
				<td>ī�װ��� �̵�</td>
				<td>
					<script>new categoryBox('step_catelist[]',4,'','disabled');</script>
					<a href="javascript:addCate('catelist');"><img src="../img/i_add.gif" align="absmiddle" /></a>
				</td>
			</tr>
			<?
			$i = 1;
			if(!empty($loop['catelist']) && is_array($loop['catelist'])){
				foreach($loop['catelist'] as $v) {
			?>
				<tr id="tr_catelist<?=$i?>">
					<td colspan="2" style="font-weight:normal;">
					<? if(strip_tags(currPosition($v['category']))) { ?>
						<?=strip_tags(currPosition($v['category']))?>
					<? } else { ?>
						������ ī�װ�<span><font class=extext>(������ư�� ���� �����Ͻñ� �ٶ��ϴ�.)</font></span>
					<? } ?>
					<input type="hidden" name="catelist[]" value="<?=$v['category']?>" />&nbsp;&nbsp;&nbsp;
					<a href="javascript:delCate('catelist', 'tr_catelist<?=$i?>');"><img src="../img/i_del.gif" align=absmiddle /></a>
					<? if(strip_tags(currPosition($v['category']))) { ?>
					<div>
						<input type="file" name="cate_img[]" size="50" />
						<a href="javascript:webftpinfo( '<?=( $v['temp2'] != '' ? '/data/m/upload_img/'.$v['temp2'] : '' )?>' );"><img src="../img/codi/icon_imgview.gif" border="0" alt="�̹��� ����" align="absmiddle"></a>
						<? if ( $v['temp2'] != '' ){ ?>&nbsp;&nbsp;<span class="noline"><input type="checkbox" name="del_cate_img[<?=$i?>]" value="Y">�̹��� ����</span><? } ?>
						<input type="hidden" name="cate_img_hidden[<?=$i?>]" value="<?=$v['temp2']?>" />
					</div>

					<? } ?>
					</td>
				</tr>
			<?
				$i ++;
				}

			}
			?>
			</table>
		</div>
	</td>
</tr>
<tr id="tab-config" style="display:none;">
	<td>�Ǽ���</td>
	<td>
		<div>
			<table id="tab-config-tbl" class="tb">
			<col class="cellC" /><col class="cellL" />
			<tr>
				<th>�� ����</th>
				<td class="noline">
				<select name="tab_num" id="tab_num" onChange="javascript:changeTabNum(this.value);" disabled >
					<? for($i = 1; $i < 5; $i++) { ?>
					<option value="<?=$i?>" <?if($tab_data['tab_num'] == $i){?> selected <?}?>><?=$i?></option>
					<? } ?>
				</select> ��
				<font class="extext">���� ���� �Դϴ�.</font>
				</td>
			</tr>
			<? for($i = 1; $i < 5; $i++) {?>
			<tr id="tab-name<?=$i?>" <? if($i != 1) { ?>style="display:none;" <? } ?>>
				<th><?=$i?>���� �̸�</th>
				<td>
					<input type="text" name="tab_name[]" value="<?=$tab_data['tab_name'][$i]?>" class="rline" disabled />
				</td>
			</tr>
			<tr id="tab-goods<?=$i?>" <? if($i != 1) { ?>style="display:none;" <? } ?>>
				<th><?=$i?>���� ��ǰ����</th>
				<td>
					<div style="z-index:-10"><img src="../img/btn_goodsChoice.gif" class="hand" onclick="javascript:popupGoodschoice('e_tab_step<?=$i?>[]', 'tab_step<?=$i?>X');" align="absmiddle" /> <font class="extext">������: ��ǰ���� �� �ݵ�� �ϴ� �����ư�� �����ž� ���� ������ �˴ϴ�.</font></div>
					<div style="position:relative;z-index:1000;">
						<div id="tab_step<?=$i?>X" style="padding-top:3px">
							<? if ($loop[$i]){ foreach ($loop[$i] as $v){ ?>
								<?=goodsimg($v['img_s'],'40,40','',1)?>
								<input type="hidden" name="e_tab_step<?=$i?>[]" value="<?=$v['goodsno']?>" />
							<? }} ?>
						</div>
					</div>
				</td>
			</tr>
			<? } ?>
			</table>
		</div>
	</td>
</tr>
<tr id="banner-config" style="display:none;">
	<td>��ʼ���</td>
	<td>
		<div>
			<table id="banner-config-tbl" class="tb">
			<col class="cellC" /><col class="cellL" />
			<tr>
				<th>��� ����</th>
				<td class="noline">
				<select name="banner_num" id="banner_num" onChange="javascript:changeBannerNum(this.value);" disabled >
					<? for($i = 1; $i < 6; $i++) { ?>
					<option value="<?=$i?>" <?if($banner_data['banner_num'] == $i){?> selected <?}?> ><?=$i?></option>
					<? } ?>
				</select> ��
				<font class="extext">����� ���� �Դϴ�.  <div style="float:left;font-size:12px;">����̹����� ������, �̺�Ʈ������ ��ǰ������ ������� �ʽ��ϴ�.</div></font>
				</td>
			</tr>
			<? for($i = 1; $i < 6; $i++) {?>
			<tr id="banner-img<?=$i?>" <? if($i != 1) { ?>style="display:none;" <? } ?>>
				<th><?=$i?>����� �̹���</th>
				<td>
					<input type="file" name="banner_img[]" size="50" /> <img src="<?='../../data/m/upload_img/'.$banner_data['banner_img'][$i]?>" width=30px height=30px onerror="imgLoadErr(this)"/>
					<a href="javascript:webftpinfo( '<?=( $banner_data['banner_img'][$i] != '' ? '/data/m/upload_img/'.$banner_data['banner_img'][$i] : '' )?>' );"><img src="../img/codi/icon_imgview.gif" border="0" alt="�̹��� ����" align="absmiddle"></a>
					<? if ( $banner_data['banner_img'][$i] != '' ){ ?>&nbsp;&nbsp;<span class="noline"><input type="checkbox" name="del_banner_img[<?=$i?>]" value="Y">����</span><? } ?>
					<input type="hidden" name="banner_img_hidden[<?=$i?>]" value="<?=$banner_data['banner_img'][$i]?>" />
				</td>
			</tr>
			<tr id="banner-link<?=$i?>" <? if($i != 1) { ?>style="display:none;" <? } ?>>
				<th><?=$i?>����� ��ũ URL</th>
				<td>
					<input type="text" name="banner_link[<?=$i?>]" value="<?=$loop[$i]?>" class="line" style="width:400px;" disabled />
					<font class="extext">��ũURL�� �տ� ��������(http://)�����ڴ� �����ؾ� �մϴ�.</font>
				</td>
			</tr>
			<? } ?>
			</table>
		</div>
	</td>
</tr>
<tr class="auto_main_sort_tr" style="display:none;">
	<td>���� ��ǰ���� �Ⱓ</td>
	<td>
		�ֱ� 
		<select name="select_date">
		<option value="7" <?=$selected['select_date'][7]?>>7��</option>
		<option value="15" <?=$selected['select_date'][15]?>>15��</option>
		</select>
	</td>
</tr>
<tr class="auto_main_sort_tr" style="display:none;">
	<td>���� ��� ī�װ�</td>
	<td>
		<script>new categoryBox('step_mobile_categoods[]',4,'','');</script>
		<a href="javascript:mobile_addCate('mobile_categoods','<?=$i?>');"><img src="../img/i_add.gif" align="absmiddle" /></a>
		<div id="add_categoods_area">
		<? if (is_array($mobile_categoods_arr) && count($mobile_categoods_arr)>0){ foreach($mobile_categoods_arr as $key => $value){ ?>
		<div id="add_categoods_<?=$value?>">
			<div class="add_categoods_box"><?=strip_tags(currPosition($value))?> &nbsp; <a href="javascript:mobile_delCate('<?=$value?>');"><img src="../img/i_del.gif" align=absmiddle /></a></div>
			<input type="hidden" name="mobile_categoods[]" value="<?=$value?>">
		</div>
		<? }} ?>
		</div>
	</td>
</tr>
<tr class="auto_main_sort_tr" style="display:none;">
	<td>���� ��� �߰�����</td>
	<td>
		<a onclick="more_terms('<?=$i?>')" class="hand"><img src="../img/disp_btn_<?=array_filter($cfg_step[$i]['price']) || array_filter($cfg_step[$i]['stock_amount']) || $cfg_step[$i]['regdt'] ? "close" :"open"?>.gif" id="more_terms_btn" /></a><br />
		<table id="more_terms" class=tb style="display:<?=array_filter($price_arr) || array_filter($stock_amount_arr) || $design_data['regdt'] ? "" :"none"?>;">
		<col class=cellC><col class=cellL>
		<tr>
			<td>���� ��ǰ����</td>
			<td>
				<input type=text name="price[]" value="<?=$price_arr[0]?>" onkeydown="onlynumber()" size="15" class="ar"> �� -
				<input type=text name="price[]" value="<?=$price_arr[1]?>" onkeydown="onlynumber()" size="15" class="ar"> ��
			</td>
		</tr>
		<tr>
			<td>���� ��ǰ������</td>
			<td>
				<label><input name="stock_type" value="product" type="radio" <?=$checked['stock_type']['product']?> />��ǰ���(ǰ����� ��)</label>
				<label><input name="stock_type" value="item" type="radio" <?=$checked['stock_type']['item']?> />ǰ�����</label>
				<div>
					<input type=text name="stock_amount[]" value="<?=$stock_amount_arr[0]?>" onkeydown="onlynumber()" size="15" class="ar"> �� -
					<input type=text name="stock_amount[]" value="<?=$stock_amount_arr[1]?>" onkeydown="onlynumber()" size="15" class="ar"> ��
				</div>

				<p class="help">
					<font color="blue">��ǰ���:</font> ��ǰ�� ǰ��(���ݿɼ�)�� ��� ������ ������ ���մϴ�. �ֹ��� �������(�������)�� ��ǰ�� ��������� �˴ϴ�. <br/>
					<font color="blue">ǰ�����:</font> ǰ��(���ݿɼ�) ���� ��� ������ ���մϴ�. �ֹ��� �������(�������)�� ��ǰ�� ��������� �˴ϴ�.
				</p>
			</td>
		</tr>
		<tr>
			<td>���� ��ǰ�����</td>
			<td>
				�ֱ� <select name="regdt">
					<option value="" <?=$design_data['regdt'] =='' ? 'selected' : ''?>>��ü</option>
					<option value="7" <?=$design_data['regdt'] =='7' ? 'selected' : ''?>>7��</option>
					<option value="15" <?=$design_data['regdt'] =='15' ? 'selected' : ''?>>15��</option>
					<option value="30" <?=$design_data['regdt'] =='30' ? 'selected' : ''?>>30��</option>
					<option value="60" <?=$design_data['regdt'] =='60' ? 'selected' : ''?>>60��</option>
				</select>
			</td>
		</tr>
		</table>
	</td>
</tr>
<tr class="auto_main_sort_tr">
	<td>���� ��ǰ</td>
	<td>
	<font class=extext>* ������ �α��� �� ���� �������� ������ ��ǰ�� ����˴ϴ�. (������ ��ǰ�� ���ܵ˴ϴ�.) </font><br />
	<? if ($auto_loop){ foreach ($auto_loop as $v){ ?>
		<?=goodsimg($v['img_s'],'40,40','',1)?>
	<? }} else { ?>
		<? if ($design_data['sort_type'] && $design_data['sort_type'] != 1) {?><span style="color:#ff0000;">�� ���� ��ǰ���� ���ؿ� �´� ��ǰ�� �����ϴ�. ���������� �����Ͻþ� ������ ��ǰ�� ���� �������ּ���.</span><br /><? } ?>
		<span style="color:#ff0000;">�� ���Ǽ��� �� �ݵ�� �ϴ� [����]��ư�� �����ž� ��ǰ�� �����˴ϴ�.</span>
	<? } ?>
	</td>
</tr>
</table>
</form>
