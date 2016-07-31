<?
$scriptLoad='
	<link rel="stylesheet" type="text/css" href="../DynamicTree.css">
	<script src="../DynamicTree.js"></script>
	<script src="../DynamicTreeSorting.js"></script>
';

include "../_header.popup.php";
@include_once "../../lib/pAPI.class.php";
@include "../../lib/json.class.php";
$pAPI = new pAPI();
$json = new Services_JSON(16);

if($_GET['no']) {
	$query = $db->_query_print('SELECT * FROM '.GD_SHOPTOUCH_DISPLAY.' WHERE no=[i]', $_GET['no'] );

	$data = $db->_select($query);
	$data =$data[0];

	if($data['link_type'] == '1' && $data['category']) {
		$tmp_data = $pAPI->getMainMenuItem($godo['sno'], $data['category']);		
		$cate_data = $json->decode($tmp_data);
		### 초기값 setting ###
		$data['link_type'] = '1';
		$data['image_up'] = '1';

		$link_path_nm = $cate_data['name'];
		$link_path = $data['category'];

	}
	else if($data['link_type'] == '2' && $data['goodsno']) {
		$goods_query = $db->_query_print('SELECT g.goodsnm, sg.img_shoptouch FROM '.GD_GOODS.' g LEFT JOIN '.GD_SHOPTOUCH_GOODS.' sg ON g.goodsno = sg.goodsno WHERE g.goodsno=[i]', $data['goodsno']);
		$goods_data = $db->_select($goods_query);
		$goods_data = $goods_data[0];
		$link_path_nm = $goods_data['goodsnm'];
		$link_path = $data['goodsno'];

		//$tmp_img = explode('|', $goods_data['img_shoptouch']);
		//$data['main_img'] = $tmp_img[0];

	}
}

$checked['link_type'][$data['link_type']] = 'checked';
$checked['image_up'][$data['image_up']] = 'checked';

$display['link_type']['1'] = 'display:none;';
$display['link_type']['2'] = 'display:none;';
$display['link_type']['3'] = 'display:none;';

$display['link_type'][$data['link_type']] = 'display:block;';

if($data['link_type'] == '3') {
	$display['link_path'] = 'display:none;';
}
else {
	$display['link_path'] = 'display:block;';
}
	
?>

<script type="text/javascript">
function chkForm2(obj) {
	return chkForm(obj);
	parent.saveHistory(parent.form);
}

/*** 분류트리 하부노드 로딩 ***/
function openTree(obj, chkable) {
	tree.sorting.ready(obj);
	
	var path_nm = form.link_path_nm;
	path_nm.value = obj.innerText
	
	var path = form.link_path;
	path.value = obj.getElementsByTagName('INPUT')[0].value;
}

function loadHistory(category, chkable) {
	var len = category.length / 3;
	var el = "cate" + len + "[]";
	var obj = document.getElementsByName(el);
	for (i=0;i<obj.length;i++){
		if (obj[i].value==category){
			openTree(obj[i].parentNode, chkable);
			break;
		}
	}
}

function changePath(no) {
	document.getElementById('link_type_1').style.display = 'none';
	document.getElementById('link_type_2').style.display = 'none';
	//document.getElementById('link_image_2').style.display = 'none';
	document.getElementById('link_type_3').style.display = 'none';
	document.getElementById('link_path_div').style.display = 'block';

	document.getElementById('link_type_' + no).style.display = 'block';

	if(no == '2') {
		//document.getElementById('link_image_2').style.display = 'block';
	}
	else if(no == '3') {
		document.getElementById('link_path_div').style.display = 'none';
		/*
		if(document.getElementsByName('image_up')[0].checked == true) {			
			document.getElementsByName('image_up')[1].checked = true;
		}
		*/
	}
	else {
		/*
		if(document.getElementsByName('image_up')[0].checked == true) {			
			document.getElementsByName('image_up')[1].checked = true;
		}
		*/
	}

	var frm = document.form;
	frm.link_path_nm.value = '';
	frm.link_path.value = '';
}

function requestCategoryIpad(ele_idx, category) {

	var url = 'ajax.category_shopTouch.php?dummy=' + new Date().getTime();

	if(category != '') {
		var ajax = new Ajax.Request(url, 
			{
				method: 'post',
				parameters : 'category='+category,
				onComplete : function() {
					try {
						var reqResult = ajax.transport;
						var cate_data = eval(reqResult.responseText);
						
						var eleSelect = document.getElementsByName('category[]');
						
						removeOption(eleSelect[ele_idx]);
						addOption(eleSelect[ele_idx], cate_data);
					}
					catch(e) {
						alert(e);
					}
				}	
			}
		);
	}
}

function addOption(obj, option_data) {
	for(var i=0; i< option_data.length; i++) {
		obj.add(new Option(option_data[i].catnm, option_data[i].category));
	}
}

function removeOption(obj) {
	for (var i=obj.options.length-1; i > 0; i--) {
		obj.options.remove(i);
	}
}

function list_goods() {
	var category = '';
	var ifrm = document.getElementById('ifrm_goods');
	var goodsnm = document.getElementById('goodsnm').value;
	ifrm.src = "iframe.shopTouch_goodslist.php?goodsnm=" + goodsnm;
}

function selectGoods(goodsnm, goodsno) {
	var frm = document.form;
	frm.link_path_nm.value = goodsnm;
	frm.link_path.value = goodsno;
}

function checkImage(goodsno, img_nm, img_type) {
	var params = '';
	var frm = document.form;

	if(img_type == 1) {
		params = '?img_nm='+img_nm;
	}
	else{
		if(goodsno == '') {
			goodsno = frm.link_path.value;
			if(goodsno == '') {
				alert("상품을 선택해 주세요");
				return;
			}
		}
		params = '?goodsno='+goodsno;
	}

	window.open('popup.main_image.php' + params, '이미지확인', 'width=300,height=400, menubar=no, status=no');
}

function chkFormDisplay(frm) {
	if(!frm.link_type[0].checked && !frm.link_type[1].checked && !frm.link_type[2].checked) {
		alert('팝업을 설정해 주시기 바랍니다.');
		return false;
	}

	if(frm.link_type[0].checked || frm.link_type[1].checked) {
		if(!frm.link_path_nm.value || !frm.link_path.value) {
			alert('이동경로를 선택해 주시기 바랍니다.');
			return false;
		}
	}
	else if(frm.link_type[2].checked) {
		if(!frm.link_url.value) {
			alert('URL을 입력해 주시기 바랍니다.');
			return false;
		}
	}
}

</script>

<form name=form method=post action="indb.php" onsubmit="return chkFormDisplay(this)" enctype="multipart/form-data">
<input type=hidden name=mode value="popup_display">
<input type=hidden name=no value="<?=$_GET['no']?>">

<div class="title title_top">팝업 클릭 시 경로 설정</div>

<table class=tb>
<col class=cellC><col class=cellL>
<tr height=26>
	<td colspan="2" class=noline>
		<label><input type="radio" name="link_type" value="1" <?=$checked['link_type']['1']?> onclick="javascript:changePath(this.value);"/>상품 리스트로 이동</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<label><input type="radio" name="link_type" value="2" <?=$checked['link_type']['2']?> onclick="javascript:changePath(this.value);"/>상품 상세 페이지로 이동</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<label><input type="radio" name="link_type" value="3" <?=$checked['link_type']['3']?> onclick="javascript:changePath(this.value);"/>URL 입력</label>
	</td>
</tr>
<tr>
	<td colspan="2">
		<div id="link_type_1" style="height:200px;<?=$display['link_type']['1']?>">
			<div id="cate_tree" style="width:100%;height:98%;background-color:#FFFFFF;border:solid 1px;border-color:#cccccc;overflow:auto;padding:10px;margin:0px 5px 3px 0px;">
				<div class="DynamicTree"><div class="wrap" id="tree">
				</div></div>
			</div>
		</div>
		<div id="link_type_2" style="height:200px;<?=$display['link_type']['2']?>">
			<input type="text" id="goodsnm" name="goodsnm" value="" />
			<a href="javascript:list_goods('category')"><img src="../img/i_search.gif" align=absmiddle></a>
			<div style="margin-top:5px;">
				<iframe id="ifrm_goods" style="width:100%;height:90%;border:solid 1px;border-color:#cccccc;oveflow-x:hidden" frameborder=0 scrolling="yes" ></iframe>
			</div>
		</div>
		<div id="link_type_3" style="<?=$display['link_type']['3']?>">
			http://<input type="text" name="link_url" value="<?=$data['link_url']?>" size="90" />
		</div>
	</td>
</tr>
</table>

<div id="link_path_div" style="margin-top:5px;<?=$display['link_path']?>">
	<table class=tb>
	<col class=cellC><col class=cellL>
	<tr height=26>
		<td>이동경로</td>
		<td>
			<input type="text" name="link_path_nm" value="<?=$link_path_nm?>" size="50" readonly/>
			<input type="text" name="link_path" value="<?=$link_path?>" readonly/>
		</td>
	</tr>
	</table>
</div>


<div style="height:10px;"></div>
<div class="title title_top">팝업명 설정</div>

<table class=tb>
<col class=cellC><col class=cellL>
<tr height=26>
	<td>팝업명</td>
	<td colspan="2">
		<input type="text" name="popup_nm" size="50" maxlength="25" value="<?=$data['popup_nm']?>" />
	</td>
</tr>
</table>

<div style="height:10px;"></div>
<div class="title title_top">메인 이미지 설정</div>

<table class=tb>
<col class=cellL><col class=cellL>
<!--
<tr height=26>
	<td colspan="2" class=noline>
		<div id="link_image_2" style="margin-bottom:5px;<?=$display['link_type']['2']?>">
			<label><input type="radio" name="image_up" value="2" <?=$checked['image_up']['2']?>/>원본 이미지 연동</label>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:checkImage('<?=$data['goodsno']?>', '', 2);"><img src="../img/btn_img_check.gif" alt="이미지확인" align="absmiddle" /></a>
		</div>
		<label><input type="radio" name="image_up" value="1" <?=$checked['image_up']['1']?>/>쇼핑몰 App 이미지 업로드</label>
		<input type="file" name="img[]" >
		<input type="hidden" name="main_img[]" value="<?=$data['main_img']?>" />
		<? if($data['main_img']) { ?>
			<? if($data['image_up'] == '1') { ?>
			&nbsp;<a href="javascript:popupImg('<?='../data/shoptouch/popup/'.$data['main_img']?>','../');"><img src="../img/btn_img_check.gif" alt="이미지확인" align="absmiddle" /></a>
			<? } else { ?>
			&nbsp;<a href="javascript:popupImg('<?=$data['main_img']?>','../');"><img src="../img/btn_img_check.gif" alt="이미지확인" align="absmiddle" /></a>
			<? } ?>
		<? } else { ?>
			<span class="small"><font class="extext">이미지를 등록해주세요.</font></span>
		<? } ?>
	</td>
</tr>
-->
<tr height=26>
	<td colspan="2" class=noline>
		<label><input type="hidden" name="image_up" value="1" />이미지 업로드</label>
		<input type="file" name="img[]" >
		<input type="hidden" name="main_img[]" value="<?=$data['main_img']?>" />
		<? if($data['main_img']) { ?>
			<? if($data['image_up'] == '1') { ?>
			&nbsp;<a href="javascript:popupImg('<?='../data/shoptouch/popup/'.$data['main_img']?>','../');"><img src="../img/btn_img_check.gif" alt="이미지확인" align="absmiddle" /></a>
			<? } else { ?>
			&nbsp;<a href="javascript:popupImg('<?=$data['main_img']?>','../');"><img src="../img/btn_img_check.gif" alt="이미지확인" align="absmiddle" /></a>
			<? } ?>
		<? } else { ?>
			<span class="small"><font class="extext">이미지를 등록해주세요.</font></span>
		<? } ?>
	</td>
</tr>

</table>

<div style="margin-top:5px;">
	<span class="small"><font class="extext">※ 권장 사이즈 이미지를 등록하셔야 품질 저하 없이 출력 됩니다. (권장 사이즈 : 400px X 450px) </font></span>
</div>
<div class="button_popup">
<input type=image src="../img/btn_confirm_s.gif">
<a href="javascript:parent.closeLayer()"><img src="../img/btn_cancel_s.gif"></a>
</div>

</form>

<script>table_design_load();</script>

<script type="text/javascript">
var tree = new DynamicTree("tree");
tree.category = '<?=$_GET[category]?>';
tree.init('shoptouch');
tree.Sorting();

</script>
