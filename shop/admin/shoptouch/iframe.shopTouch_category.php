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

$ar_display_type = array(1 => '���ø�1');
$ar_display_type[] = '���ø�2';
$ar_display_type[] = '���ø�3';
$ar_display_type[] = '���ø�4';

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
	
	$('resBoard').innerHTML = " <span class=\"small\" style=\"color:#1D8E0D;font-weight:bold;\">ī�װ� ���� ���Դϴ�. â�� ���� �����ּ���.</span>";
	
	var url = "./indb.php?mode=autoCreateCategory";
	
	new Ajax.Request(url, {
		method: "get",
		asynchronous: true, 
		onSuccess: function(transport) {
			
			var rtnFullStr = transport.responseText;
			
			var resMsg;
			var bool_success;
			if(!rtnFullStr || rtnFullStr == 'FAIL') {
				resMsg = "ī�װ� �ڵ������� �����߽��ϴ�. ����Ŀ� �ٽ� �õ��� �ּ���.";
				bool_success = false;
			}
			else {
				rtnStr = rtnFullStr.split("||");
				bool_success = true;
				
				resMsg = '1�� �з� : ' + rtnStr[0] + ' / 2�� �з� : ' + rtnStr[1] + ' ī�װ��� ���� �߽��ϴ�.';
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

	// ���÷��� ���� ����
	function fnSetExtraOption(gid, tid) {	// ���� �׷� ����, ���� Ÿ�� ��ȣ
		if (tid == '��ǰ�̵���' || tid == '�Ѹ�' || tid == '��ũ��' || tid == '��') {
			alert('�ش� ���÷��� ������ ����� �� �����ϴ�.');
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
		alert("e���� ī�װ��� �����Ͻ��� �߰� ��ư�� ���� �ֽñ� �ٶ��ϴ�.");
		return;
	}

	var enamoo_cate = document.getElementsByName("enamoo_cate[]");

	for(var j=0; j< enamoo_cate.length; j++) {

		if(enamoo_cate[j].value == cate_val) {
			alert("�̹� ���ε� ī�װ� �Դϴ�.");
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

<div class="title_sub" style="margin:0">�з������/����/����<span>�з����� �����ϰ� ����, �����մϴ�. <font class=extext>(�Է��� �ݵ�� �Ʒ� ������ư�� ��������)</font></span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=shoppingapp&no=8')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<table class=tb>
<col class=cellC><col class=cellL>
<tbody style="height:26px">
<? if(!$category) { ?>
<tr>
	<td>�з� �ڵ� ����</td>
	<td>
	<div><span onclick="javascript:autoCreateCategory();" style="cursor:hand;"><img src="../img/btn_category_auto.gif" align="absmiddle" /></span><div id="resBoard"></div></div>
	<font class=extext>���θ��� ��� �Ǿ��ִ� 1��, 2�� �з��� �ڵ����� �߰� �մϴ�.</font>
	</td>
</tr>
<? } ?>
<tr>
	<td>����з�</td>
	<td>
	<?=($category)?$data['name']:"��ü�з�";?>
	</td>
</tr>
<? if ($category){ ?>
<tr>
	<td>����з��� ����</td>
	<td>
	<input type=text name=catnm class=lline required value="<?=$data['name']?>" label="����з���" maxlen="100">
	&nbsp; �з��ڵ� : <b><?=$category?></b>
	<div style='font:0;height:5'></div>
	<div class=extext style="font-weight:bold">�з��� ����</div>
	<div class=extext>- �Ʒ� �������� ����Ͻø� �ؽ�Ʈ�� �������� ���� ���� �˴ϴ�.</div>
	</td>
</tr>
<tr>
	<td>������ ����</td>
	<td>
	<input type=file name="img[]"> <input type="checkbox" name="chkimg_0" value="1" class="null"> ����
	<div><span><font class="extext">(��������� : 22px X 22px)</font></span></div>
	<?if($data['icon']){?>
	<div><img src="<?=$data['icon']?>"></div>
	<input type="hidden" name="h_img" value="<?=$data['icon']?>">
	<?}?>
	</td>
</tr>
<tr>
	<td>�з����߱�</td>
	<td class=noline>
	<input type=radio name="visible" value="false" <?=$checked['visible']['false']?>> ���߱�
	<input type=radio name="visible" value="true" <?=$checked['visible']['true']?>> ���̱�
	</td>
</tr>
<? } ?>
<? if (!$data['parent_idx']){ ?>
<tr>
	<td>�����з� �����</td>
	<td><input type=text name=sub  label="�����з�����" maxlen="30" class="line"> <font class=extext>����з��� �����з��� �����մϴ�</font></td>
</tr>
<? } ?>
<? if($category) { ?>
<tr>
	<td>�з�����</td>
	<td><a href="javascript:if (document.form.category.value) parent.popupLayer('popup.delCategory.php?category='+document.form.category.value);else alert('��ü�з��� ��������� �ƴմϴ�');"><img src="../img/i_del.gif" border=0 align=absmiddle></a> <font class=extext>�з������� �����з��� �Բ� �����˴ϴ�. ������ �����ϼ���.</font></td>
</tr>
<? } ?>
</tbody>
</table>
<? if($category) { ?>
<div style="width:100%;height:20px;"></div>
<div class="title_sub" style="margin:0">e���� ī�װ� ����<span><font class=extext>(������ e���� ī�װ��� ���� �� �߰� ��ư�� ��������)</font></span></div>
<div style="width:100%;height:50px;">
	<? if($category == '') { ?>
		<div><div><img src="../img/img_check.gif" align="absmiddle"> ���θ� App ī�װ� Ʈ������ ī�װ��� ���� ������ �ּ���.</div></div>
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

<div class="title_sub" style="margin:0">���θ� App ī�װ��� ����� e���� ī�װ�<span><font class=extext>(������ �ݵ�� �Ʒ� ������ư�� ��������)</font></span><a href="javascript:manual('<?=$guideUrl?>board/view.php?id=shoppingapp&no=9')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
</div>
<div style="width:100%;height:122px;overflow-Y:auto;">
	<? if($category == '') { ?>
		<div><div><img src="../img/img_check.gif" align="absmiddle"> ���θ� App ī�װ� Ʈ������ ī�װ��� ���� ������ �ּ���.</div></div>
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
					echo "������ ī�װ�<span><font class=extext>(������ư�� ���� �����Ͻñ� �ٶ��ϴ�.)</font></span>";
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
<tr><td><img src="../img/icon_list.gif" align="absmiddle">���θ� App �з�Ž���⿡�� 1�� �з������(�ֻ����з�)�� ������ 1���з��� ������ �� �ֽ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">ī�װ� �ڵ����� ��ư�� ������ ���� ��ǰ�з��� 1��, 2�� ī�װ��� ���θ� App �з��� �ڵ� �����˴ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">ī�װ� �ڵ������� ��� ������ �� ������ ī�װ��� �߰� �ǿ��� �����Ͻñ� �ٶ��ϴ�. </td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�ڵ������Ϸ��� ī�װ��� ������� �ӵ��� ������ ������ ���� �Ͻñ� �ٶ��ϴ�.</td></tr>
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