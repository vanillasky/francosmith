<?
/*********************************************************
* ���ϸ�     :  disp_cate_form.php
* ���α׷��� :	�з����� ��
* �ۼ���     :  dn
* ������     :  2012.05.10
**********************************************************/

include "../_header.popup.php";
@include_once "../../conf/config.mobileShop.php";

$data = $db->fetch("select * from ".GD_CATEGORY." where category='$_GET[category]'",1);
list($cntGoods) = $db->fetch("select count(distinct goodsno) from ".GD_GOODS_LINK." where category like '$data[category]%'");
@include "../../conf/category/$data[category].php";

### �׷����� ��������
$res = $db->query("select * from gd_member_grp order by level");
while($tmp = $db->fetch($res))$r_grp[] = $tmp;

### ���� �з��̹���
if($data[useimg])$imgName = getCategoryImg($_GET[category]);

$arr_tpl_type[1] = array('name'=>'��������', 'top'=>'y', 'bottom'=>'y');
$arr_tpl_type[2] = array('name'=>'����Ʈ��', 'top'=>'y', 'bottom'=>'y');
$arr_tpl_type[3] = array('name'=>'��ǰ��ũ����', 'top'=>'y', 'bottom'=>'y');
$arr_tpl_type[4] = array('name'=>'�̹�����ũ����', 'top'=>'y', 'bottom'=>'y');
$arr_tpl_type[5] = array('name'=>'��', 'top'=>'y', 'bottom'=>'n');
$arr_tpl_type[6] = array('name'=>'�Ű���', 'top'=>'n', 'bottom'=>'n');
$arr_tpl_type[7] = array('name'=>'��ʷѸ���', 'top'=>'y', 'bottom'=>'n');

if($_GET['category']) {
	$design_query = $db->_query_print('SELECT * FROM '.GD_MOBILE_DESIGN.' WHERE page_type=[s] AND title=[s]', 'cate', $_GET['category']);
	$design_res = $db->_select($design_query);

	$design_data = Array();
	if(!empty($design_res) && is_array($design_res)) {
		foreach($design_res as $design_row) {
			$design_data[$design_row['temp1']] = $design_row;
		}
	}

	$checked['hidden_mobile'][$data['hidden_mobile']] = 'checked';
	$checked['tpl'][$design_data['top']['tpl']] = 'checked';
	$checked['b_tpl'][$design_data['bottom']['tpl']] = 'checked';
	$checked['display_type'][$design_data['top']['display_type']] = 'checked';

	$loop = Array();

	switch($design_data['top']['tpl']) {
		case 'tpl_05' :
			$tab_data = $json->decode($design_data['top']['tpl_opt']);

			$display_query = $db->_query_print('SELECT md.goodsno, md.tab_no, g.img_s, g.img_mobile, g.goodsnm, go.price FROM '.GD_MOBILE_DISPLAY.' md LEFT JOIN '.GD_GOODS.' g ON md.goodsno=g.goodsno LEFT JOIN '.GD_GOODS_OPTION.' go ON g.goodsno=go.goodsno and go_is_deleted <> \'1\' WHERE md.mdesign_no=[i] AND go.link=1 ORDER BY md.sort ASC', $design_data['top']['mdesign_no']);

			$res_display = $db->_select($display_query);

			foreach($res_display as $row_display) {
				$loop[$row_display['tab_no']][] = $row_display;
			}
			break;
		case 'tpl_07' :
			$banner_data = $json->decode($design_data['top']['tpl_opt']);

			$display_query = $db->_query_print('SELECT * FROM '.GD_MOBILE_DISPLAY.' WHERE mdesign_no=[i] ORDER BY sort ASC', $design_data['top']['mdesign_no']);
			$res_display = $db->_select($display_query);

			foreach($res_display as $row_display) {
				$loop[$row_display['banner_no']] = $row_display['temp1'];
			}
			break;
		default :
			if($design_data['top']['display_type'] == '1') {
				$display_query = $db->_query_print('SELECT md.goodsno, g.img_s, g.img_mobile, g.goodsnm, go.price FROM '.GD_MOBILE_DISPLAY.' md LEFT JOIN '.GD_GOODS.' g ON md.goodsno=g.goodsno LEFT JOIN '.GD_GOODS_OPTION.' go ON g.goodsno=go.goodsno and go_is_deleted <> \'1\' WHERE md.mdesign_no=[i] AND go.link=1 ORDER BY md.sort ASC', $design_data['top']['mdesign_no']);
				$res_display = $db->_select($display_query);

				$loop = $res_display;

			}
			else {
				$display_query = $db->_query_print('SELECT category, temp2 FROM '.GD_MOBILE_DISPLAY.' WHERE mdesign_no=[i] ORDER BY sort ASC', $design_data['top']['mdesign_no']);
				$res_display = $db->_select($display_query);

				if($design_data['top']['display_type'] == '2') {

					$loop['categoods'] = $res_display;
				}
				else if($design_data['display_type'] == '3') {
					$loop['catelist'] = $res_display;
				}
			}
			break;
	}
}
?>

<style>
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
</style>
<script>
/*** ���û�ǰ ***/
function open_box(name,isopen)
{
	var mode;
	var isopen = (isopen || document.getElementById('obj_'+name).style.display!="block") ? true : false;
	mode = (isopen) ? "block" : "none";
	document.getElementById('obj_'+name).style.display = document.getElementById('obj2_'+name).style.display = mode;
	if (document.getElementById('obj_'+name).style.display!="block") iciRow = null;
}
function list_goods(name)
{
	var category = '';
	open_box(name,true);
	var els = document.forms[0][name+'[]'];
	for (i=0;i<els.length;i++) if (els[i].value) category = els[i].value;
	var ifrm = document.getElementById('ifrm_' + name);
	var goodsnm = eval("document.forms[0].search_" + name + ".value");
	ifrm.src = "_goodslist.php?name=" + name + "&category=" + category + "&goodsnm=" + goodsnm;
}
function go_list_goods(name){
	if (event.keyCode==13){
		list_goods(name);
		return false;
	}
}
function view_goods(name)
{
	open_box(name,false);
}
function moveEvent(obj, name)
{
	obj.onclick = function(){ spoit(name,this); }
	obj.ondblclick = function(){ remove(name,this); }
}
function remove(name,obj)
{
	var tb = document.getElementById('tb_'+name);
	tb.deleteRow(obj.rowIndex);
	react_goods(name);
}

function react_goods(name)
{
	var tmp = new Array();
	var obj = document.getElementById('tb_'+name);
	for (i=0;i<obj.rows.length;i++){
		tmp[tmp.length] = "<div style='float:left;border:1px solid #cccccc;margin:1px;' title='" + obj.rows[i].cells[1].getElementsByTagName('div')[0].innerHTML + "'>" + obj.rows[i].cells[0].innerHTML + "</div>";
	}
	document.getElementById(name+'X').innerHTML = tmp.join("") + "<div style='clear:both'>";
	parent.document.getElementById('ifrmCategory').style.height = document.body.scrollHeight;
}

var iciRow, preRow, nameObj;
function spoit(name,obj)
{
	nameObj = name;
	iciRow = obj;
	iciHighlight();
}
function iciHighlight()
{
	if (preRow) preRow.style.backgroundColor = "";
	iciRow.style.backgroundColor = "#FFF4E6";
	preRow = iciRow;
}
function moveTree(idx)
{
	if (document.getElementById("obj_"+nameObj).style.display!="block") return;
	var objTop = iciRow.parentNode.parentNode;
	var nextPos = iciRow.rowIndex+idx;
	if (nextPos==objTop.rows.length) nextPos = 0;

	var cln1 = iciRow.cells[0].cloneNode(true);
	var cln2 = iciRow.cells[1].cloneNode(true);
	objTop.deleteRow(iciRow.rowIndex);
	oTr = objTop.insertRow(nextPos);
	oTd = oTr.appendChild(cln1);
	oTd = oTr.appendChild(cln2);
	oTr.className = "hand";
	oTr.onclick = function(){ spoit(nameObj,this); }
	oTr.ondblclick = function(){ remove(nameObj,this); }

	iciRow = oTr;
	iciHighlight();
	react_goods(nameObj);
}
function keydnTree(e)
{
	if (iciRow==null) return;
	e = e ? e : event;
	switch (e.keyCode){
		case 38: moveTree(-1); break;
		case 40: moveTree(1); break;
	}
	return false;
}
document.onkeydown = keydnTree;


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

	$('line-cnt').style.display = 'none';
	$('disp-cnt').style.display = 'none';
	$('banner-width').style.display = 'none';
	$('banner-height').style.display = 'none';
	$('display-type').style.display = 'none';
	$('tab-config').style.display = 'none';
	$('banner-config').style.display = 'none';

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
			setDisabled($('line-cnt'), false);
			setDisabled($('disp-cnt'), false);
			setDisabled($('tab-config'), false);
			changeTabNum($('tab_num').value);
			break;
		case 'tpl_06' :
			$('disp-cnt').style.display = '';
			$('display-type').style.display = '';
			setDisabled($('disp-cnt'), false);
			setDisabled($('display-type'), false);
			break;
		case 'tpl_07' :
			$('banner-width').style.display = '';
			$('banner-height').style.display = '';
			$('banner-config').style.display = '';
			setDisabled($('banner-width'), false);
			setDisabled($('banner-height'), false);
			setDisabled($('banner-config'), false);
			changeBannerNum($('banner_num').value);
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

	setFrameHeight();
}

function setFrameHeight() {

	parent.document.getElementById('ifrmCategory').style.height = document.body.scrollHeight;
}


document.observe('dom:loaded', function() {
	table_design_load();
	if($('category').value) {
		setInitialConfig();
	}
	cssRound('MSG01');

	setFrameHeight();
});

</script>


<form name="form" method="post" action="indb.php" onsubmit="return chkForm(this)" enctype="multipart/form-data">
<input type="hidden" name="mode" value="disp_category" />
<input type="hidden" name="category" id="category" value="<?=$_GET['category']?>" />
<div class="title_sub" style="margin:0;">�з������� ����<span>�з������� ������ Ȯ�� �մϴ�</span></div>

<table class=tb>
<col class=cellC><col class=cellL>
<tbody style="height:26px">
<tr>
	<td>����з�</td>
	<td>
	<?=($_GET[category])?currPosition($data['category'],1):"��ü�з�";?>
	<a href="../../../m/goods/goods_list.php?category=<?=$_GET['category']?>" target=_blank><img src="../img/i_nowview.gif" border=0 align=absmiddle hspace=10></a>
	</td>
</tr>
<tr>
	<td>�� �з��� ��ǰ��</td>
	<td><b><?=number_format($cntGoods)?></b>���� ��ϵǾ� �ֽ��ϴ�. <font class=extext>(�����з����� ����)</font></td>
</tr>
<tr>
	<td>����ϼ����� ���߱�</td>
	<td class=noline>
		<?php if($cfgMobileShop['vtype_category']=='1'){?>
			<? if (getCateHideCnt(substr($data[category],0,-3),'mobile')){ ?>
				<input type=hidden name=hidden_mobile value='<?=$data['hidden_mobile']?>'> <font class=small1 color=E83700>�����з��� �����̹Ƿ� �ڵ����� <font color=0074BA>(�� �з��� ���̰� �Ϸ��� ����, �����з��� ���̴� ���·� �ٲٰ��� �����ϼ���)</font>
			<? } else { ?>
				<input type=radio name=hidden_mobile value=1 <?=$checked['hidden_mobile'][1]?>> ���߱�
				<input type=radio name=hidden_mobile value=0 <?=$checked['hidden_mobile'][0]?>> ���̱�
			<? } ?>
		<?php }else{?>
			<input type=hidden name=hidden_mobile value="<?php echo $data['hidden'];?>" />
		<font class="red">e���� �з����߱�� �����ϰ� ����ǵ��� �����Ǿ��ֽ��ϴ�.</font>
		<?php }?>
	</td>
</tr>
</table>

<? if ($_GET[category]){ ?>
<div class="title_sub">�з������� ��ܺκ� �ٹ̱�<span>�з��������� ��ܿ� ������ ��ǰ�� �����ϰ� HTML�� �̿��Ͽ� �ٹ̱��մϴ�</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=6')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>���÷�������</td>
	<td>
	<? for ($i=3;$i<count($arr_tpl_type)+1;$i++) { ?>
	<? if ($arr_tpl_type[$i]['top'] == 'n') continue; ?>
	<div class="display-type-wrap">
		<img src="../img/m_goodalign_style_<?=sprintf('%02d',$i)?>.gif"  alt="<?=$arr_tpl_type[$i]['name']?>" />
		<div class="noline">
			<input type="radio" name="tpl" value="tpl_<?=sprintf('%02d',$i)?>" <?=$checked['tpl']['tpl_'.sprintf('%02d',$i)]?> onClick="javascript:setTplType(this.value); "required="required"  />
		</div>
	</div>
	<? } ?>
	</td>
</tr>
<tr id="line-cnt" style="display:none;">
	<td>��� ���μ�</td>
	<td><input type="text" name="line_cnt" value="<?=$design_data['top']['line_cnt']?>" class="rline" disabled /> �� <font class="extext">������������ �������� ���μ��Դϴ�</td>
</tr>
<tr id="disp-cnt" style="display:none;">
	<td>���δ� ��ǰ��</td>
	<td><input type="text" name="disp_cnt" value="<?=$design_data['top']['disp_cnt']?>" class="rline" disabled /> �� <font class="extext">���ٿ� �������� ��ǰ���Դϴ�</td>
</tr>
<tr id="banner-width" style="display:none;">
	<td>��� ����ũ��</td>
	<td><input type="text" name="banner_width" value="<?=$design_data['top']['banner_width']?>" class="rline" disabled /> px <font class="extext">����̹����� ����ũ�⸦ �����մϴ�</td>
</tr>
<tr id="banner-height" style="display:none;">
	<td>��� ����ũ��</td>
	<td><input type="text" name="banner_height" value="<?=$design_data['top']['banner_height']?>" class="rline" disabled /> px <font class="extext">����̹����� ����ũ�⸦ �����մϴ�</td>
</tr>
<tr id="display-type" style="display:none;">
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
					<div style="padding-top:5px;z-index:-10">
						<script>new categoryBox('step[]',4,'','disabled');</script>
						<input type=text name="search_step" onkeydown="return go_list_goods('step');">
						<a href="javascript:list_goods('step')"><img src="../img/i_search.gif" align="absmiddle" /></a>
						<a href="javascript:view_goods('step')"><img src="../img/i_openclose.gif" align="absmiddle" /></a>
					</div>
					<div style="position:relative;z-index:1000;">
						<div id="obj_step" class="box1">
							<iframe id="ifrm_step" style="width:100%;height:100%" frameborder="0"></iframe>
						</div>
						<div id="obj2_step" class="box2 scroll" onselectstart="return false;" onmousewheel="return iciScroll(this);">
						<div class="boxTitle">- ���λ�ǰ���÷��� <font class="small1" style="color:#FFFFFF;">(������ ����Ŭ��)</font></div>
						<table id="tb_step" class="tb">
						<col width="50">
						<? if ($loop){ foreach ($loop as $v){ ?>
						<tr onclick="spoit('step',this);" ondblclick="remove('step',this);" class="hand">
							<td width="50" nowrap><a href="../../goods/goods_view.php?goodsno=<?=$v['goodsno']?>" target="_blank"><?=goodsimg($v['img_s'],40,'',1)?></a></td>
							<td width="100%">
							<div><?=$v['goodsnm']?></div>
							<b><?=number_format($v['price'])?></b>
							<input type="hidden" name="e_step[]" value="<?=$v['goodsno']?>" />
							</td>
						</tr>
						<? }} ?>
						</table>
						</div>
					</div>
					<div style="padding-top:2px;z-index:1;"></div>
					<div id="stepX" style="padding-top:3px"></div>
					<script type="text/javascript">react_goods('step');</script>
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
					<div style="z-index:-10">
						<script>new categoryBox('tab_step<?=$i?>[]',4,'','disabled');</script>
						<input type=text name="search_tab_step<?=$i?>" onkeydown="return go_list_goods('tab_step<?=$i?>');">
						<a href="javascript:list_goods('tab_step<?=$i?>')"><img src="../img/i_search.gif" align="absmiddle" /></a>
						<a href="javascript:view_goods('tab_step<?=$i?>')"><img src="../img/i_openclose.gif" align="absmiddle" /></a>
					</div>
					<div style="position:relative;z-index:1000;">
						<div id="obj_tab_step<?=$i?>" class="box1">
							<iframe id="ifrm_tab_step<?=$i?>" style="width:100%;height:100%" frameborder="0"></iframe>
						</div>
						<div id="obj2_tab_step<?=$i?>" class="box2 scroll" onselectstart="return false;" onmousewheel="return iciScroll(this);" >

							<div class="boxTitle">- ���λ�ǰ���÷��� <font class="small1" style="color:#FFFFFF;">(������ ����Ŭ��)</font></div>
							<table id="tb_tab_step<?=$i?>" class="tb">
							<col width="50">
							<? if ($loop[$i]){ foreach ($loop[$i] as $v){ ?>
							<tr onclick="spoit('tab_step<?=$i?>',this);" ondblclick="remove('tab_step<?=$i?>',this);" class="hand">
								<td width="50" nowrap><a href="../../goods/goods_view.php?goodsno=<?=$v['goodsno']?>" target="_blank"><?=goodsimg($v['img_s'],40,'',1)?></a></td>
								<td width="100%">
								<div><?=$v['goodsnm']?></div>
								<b><?=number_format($v['price'])?></b>
								<input type="hidden" name="e_tab_step<?=$i?>[]" value="<?=$v['goodsno']?>" />
								</td>
							</tr>
							<? }} ?>
							</table>
						</div>
						<div style="z-index:1;"></div>
						<div id="tab_step<?=$i?>X" style="padding-top:3px"></div>
						<script type="text/javascript">react_goods('tab_step<?=$i?>');</script>
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
				<font class="extext">����� ���� �Դϴ�.</font>
				</td>
			</tr>
			<? for($i = 1; $i < 6; $i++) {?>
			<tr id="banner-img<?=$i?>" <? if($i != 1) { ?>style="display:none;" <? } ?>>
				<th><?=$i?>����� �̹���</th>
				<td>
					<input type="file" name="banner_img[]" size="50" />
					<a href="javascript:webftpinfo( '<?=( $banner_data['banner_img'][$i] != '' ? '/data/m/upload_img/'.$banner_data['banner_img'][$i] : '' )?>' );"><img src="../img/codi/icon_imgview.gif" border="0" alt="�̹��� ����" align="absmiddle"></a>
					<? if ( $banner_data['banner_img'][$i] != '' ){ ?>&nbsp;&nbsp;<span class="noline"><input type="checkbox" name="del_banner_img[<?=$i?>]" value="Y">����</span><? } ?>
					<input type="hidden" name="banner_img_hidden[<?=$i?>]" value="<?=$banner_data['banner_img'][$i]?>" />
				</td>
			</tr>
			<tr id="banner-link<?=$i?>" <? if($i != 1) { ?>style="display:none;" <? } ?>>
				<th><?=$i?>����� ��ũ URL</th>
				<td>
					<input type="text" name="banner_link[<?=$i?>]" value="<?=$loop[$i]?>" class="line" style="width:400px;" disabled />
				</td>
			</tr>
			<? } ?>
			</table>
		</div>
	</td>
</tr>
<tr>
	<td>��ܲٹ̱�<br><font class=extext>(HTML ��ư�� ������ �ҽ������� �����մϴ�)</font></td>
	<td height=300 style="padding:5px">
	<textarea name="mobile_body" style="width:100%;height:300px" type=editor><?=stripslashes($design_data['top']['text_temp1'])?></textarea>
	<script src="../../lib/meditor/mini_editor.js"></script>
	<script>mini_editor("../../lib/meditor/");</script>
	</td>
</tr>
</table>

<div class="title_sub">�з������� ����Ʈ�κ� �ٹ̱�<span>��ǰ�з������� �ϴ��� ����Ʈ�κ��� �ٹӴϴ�</span></div>

<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>���÷�������</td>
	<td>
	<? for ($i=1;$i<count($arr_tpl_type)+1;$i++) { ?>
	<? if ($arr_tpl_type[$i]['bottom'] == 'n') continue; ?>
	<div class="display-type-wrap">
		<img src="../img/m_goodalign_style_<?=sprintf('%02d',$i)?>.gif"  alt="<?=$arr_tpl_type[$i]['name']?>" />
		<div class="noline">
			<input type="radio" name="b_tpl" value="tpl_<?=sprintf('%02d',$i)?>" <?=$checked['tpl']['tpl_'.sprintf('%02d',$i)]?> required="required"  />
		</div>
	</div>
	<? } ?>
	</td>
</tr>
<tr>
	<td>��� ���μ�</td>
	<td><input type="text" name="b_line_cnt" value="<?=$design_data['bottom']['line_cnt']?>" class="rline" /> �� <font class="extext">�з��������� �������� ���μ��Դϴ�</td>
</tr>
<tr>
	<td>���δ� ��ǰ��</td>
	<td><input type="text" name="b_disp_cnt" value="<?=$design_data['bottom']['disp_cnt']?>" class="rline" /> �� <font class="extext">���ٿ� �������� ��ǰ���Դϴ�</td>
</tr>
<tr>
	<td>�����з� ���� ����</td>
	<td><?if($_GET[category]){?><input type="checkbox" name="chkdesign" value="1" class="null">�����з����� ������ ������ ������� �����ϰ� �����մϴ�.<?}?>
	<div style="padding-top:3px" class=extext>���� '�з������� ��ܺκ� �ٹ̱�� '�з������� ����Ʈ�κ� �ٹ̱�'���� ������ ������ �����з����� �����ϰ� �����Ű�� ����Դϴ�</div></td>
</tr>
</table>

<? } ?>

<div class="button"><input type=image src="../img/btn_modify.gif"></div>

</form>

<div id=MSG01>
<table class="small_ex">
<tr><td>
<img src="../img/icon_list.gif" align=absmiddle>��ǰ�з�Ž���⿡�� 1���з������ (�ֻ����з�)�� ������ 1���з��� ������ �� �ֽ��ϴ�.<br>
<img src="../img/icon_list.gif" align=absmiddle>�з���������ܿ��� �̺�Ʈ�� ��ʸ� ��ġ�Ͽ� ����ȭ�� �� �ְ� �������غ�����.<br>
<img src="../img/icon_list.gif" align=absmiddle>�з����������� �ش�з��� ������ Ű������ �����̵�Ű���� �����ϰ� ������ ���� �����մϴ�.
</table>
</div>

