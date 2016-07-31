<?
include "../_header.popup.php";
@include_once "../../lib/pAPI.class.php";
@include_once "../../lib/json.class.php";
$pAPI = new pAPI();
$json = new Services_JSON(16);

$category = $_GET['category'];
$tmp_data = $pAPI->getMainMenuItem($godo['sno'], $category);
$data = $json->decode($tmp_data);
$checked['visible'][$data['visible']] = "checked";

$ar_display_type = array(1 => '템플릿1');
$ar_display_type[] = '템플릿2';
$ar_display_type[] = '템플릿3';
$ar_display_type[] = '템플릿4';

$use_arr['tp_type'] = 'menu';
$use_arr['menu_idx'] = $_GET['category'];
$use_arr['in_data'] = 'true';
$tmp_data_template = $pAPI->getUseTemplate($godo['sno'], $use_arr);

$data_template = $json->decode($tmp_data_template);
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
	switch (e.keyCode) {
		case 38: moveTree(-1); break;
		case 40: moveTree(1); break;
	}
	return false;
}

function autoCreateCategory() {
	
	var oDiv = document.createElement('DIV');
	var cDiv = document.body.appendChild(oDiv);
	var oImg = document.createElement('IMG');
	var cImg = cDiv.appendChild(oImg);
	cImg.src = '../img/loading.gif';
	with (cDiv.style) {
		position = 'absolute';
		border = 'solid 1px #dddddd';
		filter = "Alpha(Opacity=90)";
		opacity = "0.9";
	}

	cDiv.style.left = window.event.clientX + document.body.scrollLeft - 30;
	cDiv.style.top = window.event.clientY + document.body.scrollTop + 23;
	
	$('resBoard').innerHTML = " <span class=\"small\" style=\"color:#1D8E0D;font-weight:bold;\">카테고리 생성 중입니다. 창을 닫지 말아주세요.</span>";
	
	var url = "./indb.php?mode=autoCreateCategory";
	
	new Ajax.Request(url, {
		method: "get",
		asynchronous: true, 
		onSuccess: function(transport) {
			
			var rtnFullStr = transport.responseText;
			
			var resMsg;
			var bool_success;
			if(!rtnFullStr || rtnFullStr == 'FAIL') {
				resMsg = "카테고리 자동생성을 실패했습니다. 잠시후에 다시 시도해 주세요.";
				bool_success = false;
			}
			else {
				rtnStr = rtnFullStr.split("||");
				bool_success = true;
				
				resMsg = '1차 분류 : ' + rtnStr[0] + ' / 2차 분류 : ' + rtnStr[1] + ' 카테고리를 생성 했습니다.';
			}

			if(bool_success) {
				$('resBoard').innerHTML = " <span class=\"small\" style=\"color:#1D8E0D;font-weight:bold;\">" + resMsg + "</span>";
				cDiv.style.display = 'none';
			}
			else {
				$('resBoard').innerHTML = " <span class=\"small\" style=\"color:#FF6C68;font-weight:bold;\">" + resMsg + "</span>";
				cDiv.style.display = 'none';
			}

		}
		
	});

}
document.onkeydown = keydnTree;

	// 디스플레이 유형 관련
	function fnSetExtraOption(gid, tid) {	// 진열 그룹 순번, 진열 타입 번호
		if (tid == '상품이동형' || tid == '롤링' || tid == '스크롤' || tid == '탭') {
			alert('해당 디스플레이 유형은 사용할 수 없습니다.');
			return false;
		}
		var oTpl = $(tid);

		var data = <?=$lstcfg ? gd_json_encode($lstcfg) : '{}'?>;
		data.checked = {};
		data.gid = gid;

		$H(data).each(function(pair){
			if (pair.key.indexOf('dOpt') > -1 && pair.value) {
				eval('data.checked.'+ pair.key +' = ["",""];');
				eval('data.checked.'+ pair.key +'['+eval('pair.value.'+gid)+'] = "checked";');
			}
			else if (pair.key.indexOf('alphaRate') > -1 && pair.value)
			{
				data.alphaRate = eval('pair.value.'+gid);
			}
		});


		if (oTpl != null) {
			var tpl = new Template( oTpl.innerHTML.unescapeHTML() );

			var html = tpl.evaluate(data);
			$('gList_').style.display = 'block';

			$('extra-config-wrap-display-type-'+gid).update( html );
			$('extra-config-display-type-'+gid).style.display = 'block';

		}
		else {
			$('extra-config-wrap-display-type-'+gid).update('');
			$('extra-config-display-type-'+gid).style.display = 'none';
		}
	}

function addCate() {
	var cate = document.getElementsByName("cate[]");
	
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
		alert("e나무 카테고리를 선택하신후 추가 버튼을 눌러 주시기 바랍니다.");
		return;
	}

	var enamoo_cate = document.getElementsByName("enamoo_cate[]");

	for(var j=0; j< enamoo_cate.length; j++) {

		if(enamoo_cate[j].value == cate_val) {
			alert("이미 매핑된 카테고리 입니다.");
			return;
		}
	}
	
	var id = document.getElementById('enamoo_category_add');
    var len = id.rows.length;
    var newRow = id.insertRow(len);
	newRow.id = 'cate_tr_' + len;
    var td0 = newRow.insertCell(0);
    var td1 = newRow.insertCell(1);
	td0.innerHTML = cate_nm + ' <input type="hidden" name="enamoo_cate[]" value="'+cate_val+'" />';
	td1.innerHTML = '<a href="javascript:delCate(\''+ newRow.id +'\');"><img src="../img/i_del.gif" align=absmiddle /></a>';	
}

function delCate(tr_id) {
	var id = document.getElementById('enamoo_category_add');
	var tr = document.getElementById(tr_id);
    id.deleteRow(tr.rowIndex);
}

</script>

<form name=form method=post action="indb.php" onsubmit="return chkForm(this)" enctype="multipart/form-data">
<input type=hidden name=mode value="mod_category">
<input type=hidden name=category value="<?=$category?>">
<input type=hidden name=order_number value="<?=$data['order_number']?>">

<div class="title_sub" style="margin:0">분류만들기/수정/삭제<span>분류명을 생성하고 수정, 삭제합니다. <font class=extext>(입력후 반드시 아래 수정버튼을 누르세요)</font></span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=shoppingapp&no=8')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<table class=tb>
<col class=cellC><col class=cellL>
<tbody style="height:26px">
<? if(!$category) { ?>
<tr>
	<td>분류 자동 생성</td>
	<td>
	<div><span onclick="javascript:autoCreateCategory();" style="cursor:hand;"><img src="../img/btn_category_auto.gif" align="absmiddle" /></span><div id="resBoard"></div></div>
	<font class=extext>쇼핑몰에 등록 되어있는 1차, 2차 분류를 자동으로 추가 합니다.</font>
	</td>
</tr>
<? } ?>
<tr>
	<td>현재분류</td>
	<td>
	<?=($category)?$data['name']:"전체분류";?>
	</td>
</tr>
<? if ($category){ ?>
<tr>
	<td>현재분류명 수정</td>
	<td>
	<input type=text name=catnm class=lline required value="<?=$data['name']?>" label="현재분류명" maxlen="100">
	&nbsp; 분류코드 : <b><?=$category?></b>
	<div style='font:0;height:5'></div>
	<div class=extext style="font-weight:bold">분류명 노출</div>
	<div class=extext>- 아래 아이콘을 등록하시면 텍스트와 아이콘이 같이 노출 됩니다.</div>
	</td>
</tr>
<tr>
	<td>아이콘 수정</td>
	<td>
	<input type=file name="img[]"> <input type="checkbox" name="chkimg_0" value="1" class="null"> 삭제
	<div><span><font class="extext">(권장사이즈 : 22px X 22px)</font></span></div>
	<?if($data['icon']){?>
	<div><img src="<?=$data['icon']?>"></div>
	<input type="hidden" name="h_img" value="<?=$data['icon']?>">
	<?}?>
	</td>
</tr>
<tr>
	<td>분류감추기</td>
	<td class=noline>
	<input type=radio name="visible" value="false" <?=$checked['visible']['false']?>> 감추기
	<input type=radio name="visible" value="true" <?=$checked['visible']['true']?>> 보이기
	</td>
</tr>
<? } ?>
<? if (!$data['parent_idx']){ ?>
<tr>
	<td>하위분류 만들기</td>
	<td><input type=text name=sub  label="하위분류생성" maxlen="30" class="line"> <font class=extext>현재분류의 하위분류를 생성합니다</font></td>
</tr>
<? } ?>
<? if($category) { ?>
<tr>
	<td>분류삭제</td>
	<td><a href="javascript:if (document.form.category.value) parent.popupLayer('popup.delCategory.php?category='+document.form.category.value);else alert('전체분류는 삭제대상이 아닙니다');"><img src="../img/i_del.gif" border=0 align=absmiddle></a> <font class=extext>분류삭제시 하위분류도 함께 삭제됩니다. 신중히 삭제하세요.</font></td>
</tr>
<? } ?>
</tbody>
</table>
<? if($category) { ?>
<div style="width:100%;height:20px;"></div>
<div class="title_sub" style="margin:0">e나무 카테고리 연동<span><font class=extext>(연동할 e나무 카테고리를 선택 후 추가 버튼을 누르세요)</font></span></div>
<div style="width:100%;height:50px;">
	<? if($category == '') { ?>
		<div><div><img src="../img/img_check.gif" align="absmiddle"> 쇼핑몰 App 카테고리 트리에서 카테고리를 먼저 선택해 주세요.</div></div>
	<? } else { ?>
		<table class="tb">
		<tr>
			<td>
			<script>new categoryBox('cate[]',4,'');</script>
			<a href="javascript:addCate();"><img src="../img/i_add.gif" align=absmiddle /></a>
			</td>
		</tr>
		</table>
	<? } ?>
</div>

<div class="title_sub" style="margin:0">쇼핑몰 App 카테고리에 적용된 e나무 카테고리<span><font class=extext>(편집후 반드시 아래 수정버튼을 누르세요)</font></span><a href="javascript:manual('<?=$guideUrl?>board/view.php?id=shoppingapp&no=9')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
</div>
<div style="width:100%;height:122px;overflow-Y:auto;">
	<? if($category == '') { ?>
		<div><div><img src="../img/img_check.gif" align="absmiddle"> 쇼핑몰 App 카테고리 트리에서 카테고리를 먼저 선택해 주세요.</div></div>
	<? } else { ?>	
		<table class="tb" id="enamoo_category_add">
		<? 
		$i = 0;
		if(!empty($data_template) && is_array($data_template)){
			foreach($data_template as $row_data) { 
				
				if($row_data['type'] == 'category' && $row_data['value']) {
		?>
			<tr id="cate_tr_<?=$i?>">
				<td>
				<? if(strip_tags(currPosition($row_data['value']))) {
					echo strip_tags(currPosition($row_data['value']));
				}
				else {
					echo "삭제된 카테고리<span><font class=extext>(삭제버튼을 눌러 삭제하시기 바랍니다.)</font></span>";
				}
				?>
				<input type="hidden" name="enamoo_cate[]" value="<?=$row_data['value']?>" /></td>
				<td><a href="javascript:delCate('cate_tr_<?=$i?>');"><img src="../img/i_del.gif" align=absmiddle /></a></td>
			</tr>
		<? 
				}
			$i ++;
			}		
		} 
		?>
		</table>
	<? } ?>
</div>
<? } ?>
<div class="button"><input type=image src="../img/btn_modify.gif"></div>

</form>

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">쇼핑몰 App 분류탐색기에서 1차 분류만들기(최상위분류)를 누르면 1차분류를 생성할 수 있습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">카테고리 자동생성 버튼을 누르면 기존 상품분류의 1차, 2차 카테고리가 쇼핑몰 App 분류로 자동 생성됩니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">카테고리 자동생성의 경우 생성을 할 때마다 카테고리가 추가 되오니 주의하시기 바랍니다. </td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">자동생성하려는 카테고리가 많을경우 속도가 느릴수 있으니 주의 하시기 바랍니다.</td></tr>
</table>
</div>

<script>cssRound('MSG01')</script>

<script>
table_design_load();
window.onload = function(){
	parent.document.getElementById('ifrmCategory').style.height = document.body.scrollHeight;
}
<? if ($_GET[focus]=="sub"){ ?>
document.form.sub.focus();
<? } ?>
</script>