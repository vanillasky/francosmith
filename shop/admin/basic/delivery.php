<?

$location = "�⺻���� > ���/�ù���å";
include "../_header.php";
include "../../conf/config.pay.php";

if(!file_exists("../../conf/area.delivery.php")){
	$dmode = 0;
	### ������ �����ۺ� ���������� ���� ��� ����
	include "setAreaName.inc.php";
	echo("<script>location.reload();</script>");
	exit;
}
if($set['delivery']['over'] &&!$set['delivery']['overAdd'] ){
	$dmode = 3;
	### �߰���ۺ� ������� �߰���ۺ�� ����
	include "setAreaName.inc.php";
	echo("<script>location.reload();</script>");
	exit;
}
@include "../../conf/area.delivery.php";
$arr_area = explode('|',$r_area[deliveryArea]);

$tmp = explode('|',$set['r_delivery']['title']);
foreach($tmp as $v) $r_set[$v] = $set[$v];
$set = $set['delivery'];
if(!$set['deliverynm'])$set['deliverynm'] = '�⺻���';

if(!$set[basis])$set[basis] = 0;
$checked[basis][$set[basis]] = "checked";
if(!$set[freeDelivery])$set[freeDelivery] = 0;
$checked[freeDelivery][$set[freeDelivery]] = "checked";
if(!$set[goodsDelivery])$set[goodsDelivery] = 0;
$checked[goodsDelivery][$set[goodsDelivery]] = "checked";
if(!$set[area_deli_type])$set[area_deli_type] = 0;
$checked[area_deli_type][$set[area_deli_type]] = "checked";
if(!$set['deliveryOrder'])$set['deliveryOrder'] = 0;
$checked['deliveryOrder'][$set['deliveryOrder']] = "checked";

$over = explode("|",$set[over]);
$overAdd = explode("|",$set[overAdd]);
$overZipcode = explode("|",$set[overZipcode]);
$overAddZip = explode("|",$set[overAddZip]);
$areaZip1 = explode("|",$set[areaZip1]);
$areaZip2 = explode("|",$set[areaZip2]);

### ��۾�ü ����
$query = "select * from ".GD_LIST_DELIVERY." order by deliverycomp";
$res = $db->query($query);
$k = 1;
while ($data=$db->fetch($res)){
	$delivery_tmp[] = $data;
	if ($data['useyn']=="y"){
		if($data['deliveryno'] == $set[defaultDelivery]){
			$delivery[0] = $data;
		}else{
			$delivery[$k] = $data;
			$k++;
		}
	}
}
@ksort($delivery);

//new ������ �߰� ��ۺ�
@include './popup.newAreaDeliveryLib.func.php';
$newAreaDeliveryCount = newAreaTotalCount();
$skinPatchMsg = newAreaSkinCheck();

// ������ �� ������ �߰� ��ۺ�
if (isset($set['add_extra_fee']) === true) {
	$tmp_add_extra_fee					= $set['add_extra_fee'];	// ���� ���Ž� ����, �ش� ���� ���̻� ��� ����
} else {
	$tmp_add_extra_fee					= 1;						// �⺻ ���� ������ �߰� ��ۺ� �������� ó��
}
if (isset($set['add_extra_fee_basic']) === false) {					// "�⺻ �����å�� ���� ���Ǻ� ������ ���"���� �⺻�� (���� ���Ž� �Ǵ� ������ �߰� ��ۺ� ����)
	$set['add_extra_fee_basic']			= $tmp_add_extra_fee;
}
if (isset($set['add_extra_fee_free']) === false) {					// "������ ��ǰ �ֹ���"�� ��� �⺻�� (���� ���Ž� �Ǵ� ������ �߰� ��ۺ� ����)
	$set['add_extra_fee_free']			= $tmp_add_extra_fee;
}
if (isset($set['add_extra_fee_memberGroup']) === false) {			// "ȸ�� �׷� ���ÿ� ���� ��ۺ� ������ ���"���� �⺻�� (���� ���Ž� �Ǵ� ������ �߰� ��ۺ� ����)
	$set['add_extra_fee_memberGroup']	= $tmp_add_extra_fee;
}
unset($tmp_add_extra_fee);

// ������ �߰� ��ۺ� ���� �ΰ� �⺻��
if (isset($set['add_extra_fee_duplicate_free']) === false) {
	$set['add_extra_fee_duplicate_free']		= 1;				// ������ ��ǰ �ֹ��� �⺻���� "�׸� �ߺ� �ΰ�" �� ó��
}
if (isset($set['add_extra_fee_duplicate_fixEach']) === false) {
	$set['add_extra_fee_duplicate_fixEach']		= 1;				// ���� ��ۺ� ��ǰ �ֹ��� �⺻���� "�׸� �ߺ� �ΰ�" �� ó��
}
?>

<script>

var fm, selL, selR, tbOver;

function move(direct)
{
	if (direct=="right"){
		for (i=selL.options.selectedIndex;i<selL.options.length;i++){
			if (selL.options[i].selected==true){
				if (chkOption(selL.options[i].value)) selR.options[selR.options.length] = new Option(selL.options[i].text,selL.options[i].value);
			}
		}
	} else {
		for (i=selR.options.selectedIndex;i<selR.options.length;i++){
			if (selR.options[i].selected==true){
				selR.options.remove(i--);
			}
		}
	}
}

function chkOption(val)
{
	for (z=0;z<selR.options.length;z++){
		if (selR.options[z].value==val) return false;
	}
	return true;
}

function chkForm2(obj)
{
	if (!chkForm(obj)) return false;
	for (i=0;i<selR.options.length;i++) selR.options[i].selected = true;
	return true;
}

function registerDelivery()
{
	popupLayer('popup.delivery.php?mode=registerDelivery',500,300);
}

function modifyDelivery()
{
	var arg;
	if (selL.selectedIndex!=-1){
		arg = "mode=modifyDelivery&no=" + selL.options[selL.selectedIndex].value;
		popupLayer('popup.delivery.php?'+arg,500,300);
	} else alert("������ �ù�縦 �������ּ���");
}

function addOver()
{
	var idx = tbOver.rows.length / 2;
	oTr = tbOver.insertRow();
	oTd = oTr.insertCell();
	var tmp = "�Ʒ� ������ ��ۺ� <input type=text name=\"overAdd[]\" value=\"\" class=\"rline\"> ���� �߰� �մϴ�. <a href=\"javascript:popup('popup.areaDelivery.php?idx="+idx+"',300,300);\"><img src=\"../img/btn_area_search.gif\" align=\"absmiddle\" value=\"�����˻��ϱ�\" /></a><div class=extext style=\"padding-top:5px\">(�ݵ�� <b>'�����˻��ϱ�'</b>�� ������ ������ �߰��ϼ���)</font></div>";
	oTd.innerHTML = tmp;
	oTd = oTr.insertCell();
	oTd.innerHTML = "<a href='javascript:void(0)' onClick='delOver(this)'><img src='../img/i_del.gif'></a>";
	oTr = tbOver.insertRow();
	oTd = oTr.insertCell();
	oTd.colSpan = 2;
	oTd.innerHTML = "<textarea name=overZipcodeName[] style='width:100%;height:50px' required label='�������������ȣ'></textarea>";
	requiredOver();
}

function addOverZip()
{
	var tbl = document.getElementById('tbOverZip');
	var idx = tbl.rows.length / 2;
	oTr = tbl.insertRow();
	oTd = oTr.insertCell();
	var tmp = "�Ʒ� ������ ��ۺ� <input type=text name=\"overAddZip[]\" value=\"\" class=\"rline\"> ���� �߰� �մϴ�.";
	oTd.innerHTML = tmp;
	oTd = oTr.insertCell();
	oTd.innerHTML = "<a href=\"javascript:void(0)\" onClick=\"delOverZip(this)\"><img src=\"../img/i_del.gif\"></a>";
	oTr = tbl.insertRow();
	oTd = oTr.insertCell();
	oTd.colSpan = 2;
	oTd.innerHTML = "<div><a href=\"javascript:popup('../proc/popup_zipcode.delivery.php?idx="+idx+"',400,300)\"><img src=\"../img/btn_zipcode.gif\" border=\"0\" align=\"absmiddle\"></a> <input type='text' name='areaZip1[]' size=\"6\" readonly>���� <input type='text' name='areaZip2[]' size=\"6\" readonly>����</div><div class=extext style=\"padding-top:5px\">(�ݵ�� <b>'�����ȣ�˻�'</b>�� ������ ������ Ȯ�� �� �߰��ϼ���)</div>";
	requiredOver();
}

function delOver(obj)
{
	var idx = obj.parentNode.parentNode.rowIndex;
	tbOver.deleteRow(idx+1);
	tbOver.deleteRow(idx);
	requiredOver();
}

function delOverZip(obj)
{
	var tbl = document.getElementById('tbOverZip');
	var idx = obj.parentNode.parentNode.rowIndex;
	tbl.deleteRow(idx+1);
	tbl.deleteRow(idx);
	requiredOver();
}

function addDelivery(){
	var tbl_delivery = document.getElementById('tbl_delivery');
	oTr = tbl_delivery.insertRow(tbl_delivery.rows.length);
	oTr.height = "30";

	oTd = oTr.insertCell(0);
	oTd.className = "center";
	oTd.innerHTML = tbl_delivery.rows.length - 4;

	oTd = oTr.insertCell(1);
	oTd.className = "center";
	oTd.innerHTML = "<input type=text name=\"r_delivery[]\" size=10 required>";

	oTd = oTr.insertCell(2);
	oTd.className="ver81";
	oTd.innerHTML = "�� ���ž��� <input type=text name=\"r_free[]\" size=9 class=right onkeydown=\"onlynumber();\"> �� �̻��� �� ��ۺ� ����, �̸��� �� <select name=\"r_deliType[]\" onchange=\"chkDeliveryType(this)\"><option value=\"����\">����</option><option value=\"�ĺ�\">����</option></select><span> <input type=text name=\"r_default[]\" size=8 class=right onkeydown=\"onlynumber()\"> �� ��ۺ� �ΰ�</span><span style=\"display:none;\"> ��� �޽��� : <input type=\"text\" name=\"r_default_msg[]\" size=\"18\" class=\"lline\"></span>";

	oTd = oTr.insertCell(3);
	oTd.className = 'center';
	oTd.innerHTML = "<a href=\"javascript:void(0)\" onClick=\"delDelivery(this)\"><img src=\"../img/btn_delete_new.gif\"></a>";

}

function chkDeliveryType(obj){
	if(obj){
	obj.parentNode.getElementsByTagName('span')[0].style.display = obj.parentNode.getElementsByTagName('span')[1].style.display =  "none";
	obj.parentNode.getElementsByTagName('span')[ obj.selectedIndex ].style.display = "inline";
	}
}

function delDelivery(obj){
	var tbl_delivery = document.getElementById('tbl_delivery');
	var idx = obj.parentNode.parentNode.rowIndex;
	tbl_delivery.deleteRow(idx);
}

function chkAreaDeli(){
	var obj = document.getElementsByName('area_deli_type');
	var tbl = document.getElementById('tbOver');

	var tblZip = document.getElementById('tbOverZip');
	var tblNew = document.getElementById('tbOverNew');

	if(obj[0].checked == true){
		tblNew.style.display = 'block';
		tbl.style.display = 'none';
		tblZip.style.display = 'none';
	}else if(obj[1].checked == true){
		tblNew.style.display = 'none';
		tbl.style.display = 'block';
		tblZip.style.display = 'none';
	}else if(obj[2].checked == true){
		tblNew.style.display = 'none';
		tbl.style.display = 'none';
		tblZip.style.display = 'block';
	}
}

function requiredOver()
{
	var obj = document.getElementsByName('area_deli_type');
	var overAdd = document.getElementsByName('overAdd[]');
	var overAddZip = document.getElementsByName('overAddZip[]');
	var zipcode = document.getElementsByName('overZipcodeName[]');
	var zipcode1 = document.getElementsByName('areaZip1[]');
	var zipcode2 = document.getElementsByName('areaZip2[]');

	var required = (overAdd.length > 1 ? true : false);
	var requiredZip = (overAddZip.length > 1 ? true : false);

	if(obj[0].checked == true) required = false;
	if(obj[1].checked == true) requiredZip = false;

	for (var i = 0; i < overAdd.length; i++)
	{
		if (required == true){
			overAdd[i].setAttribute('required', '');
			overAdd[i].setAttribute('label', '�����ۺ�');
			zipcode[i].setAttribute('required', '');
			zipcode[i].setAttribute('label', '��������');
		}
		else {
			overAdd[i].removeAttribute('required');
			overAdd[i].removeAttribute('label');
			zipcode[i].removeAttribute('required');
			zipcode[i].removeAttribute('label');
		}
	}

	for (var i = 0; i < overAddZip.length; i++)
	{
		if (requiredZip == true){
			overAddZip[i].setAttribute('required', '');
			overAddZip[i].setAttribute('label', '�����ۺ�');
			zipcode1[i].setAttribute('required', '');
			zipcode1[i].setAttribute('label', '��������');
			zipcode2[i].setAttribute('required', '');
			zipcode2[i].setAttribute('label', '��������');
		}
		else {
			overAddZip[i].removeAttribute('required');
			overAddZip[i].removeAttribute('label');
			zipcode1[i].removeAttribute('required');
			zipcode1[i].removeAttribute('label');
			zipcode2[i].removeAttribute('required');
			zipcode2[i].removeAttribute('label');
		}
	}
}

function addNewAreaDelivery(srcType)
{
	if(_ID('newAreaLayerBg1') && _ID('newAreaLayerObj1')) addNewAreaDeliveryClose();

	if(srcType=='migration'){
		var layerWidth = 500;
		var layerHeight = 300;
	}else{
		var layerWidth = 800;
		var layerHeight = 650;
	}

	var pixelBorder = 3;

	layerWidth += pixelBorder * 2;
	layerHeight += pixelBorder * 2;

	var bodyW = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
	var bodyH = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;

	var posX = (bodyW - layerWidth) / 2;
	var posY = (bodyH - layerHeight) / 2;

	// ��׶��� ���̾�
	var obj = document.createElement("div");
	with (obj.style){
		position		= "absolute";
		left			= 0 + 'px';
		top				= 0 + 'px';
		width			= "100%";
		height			= document.body.scrollHeight+'px';
		backgroundColor = "#000000";
		filter			= "Alpha(Opacity=80)";
		opacity			= "0.5";
	}
	obj.id = "newAreaLayerBg1";
	document.body.appendChild(obj);

	// ���������� ���̾�
	var obj = document.createElement("div");
	with (obj.style){
		position		= "absolute";
		left			= posX + document.body.scrollLeft +'px';
		top				= posY + document.body.scrollTop +'px';
		width			= layerWidth + 'px';
		height			= layerHeight + 'px';
		backgroundColor = "#ffffff";
		border			= "3px solid #000000";
	}
	obj.id = "newAreaLayerObj1";
	document.body.appendChild(obj);

	/*** Ÿ��Ʋ�� ���̾� ***/
	if(srcType=='migration'){
		var bottom = document.createElement("div");
		with (bottom.style){
			position		= "absolute";
			width			= '100%';
			height			= '12px';
			textAlign		= "center";
			bottom			= '0px',
			backgroundColor = "#000000";
			color			= "#ffffff";
			font			= "bold 8pt tahoma; letter-spacing:0px";
		}
		bottom.innerHTML = "<a href='javascript:addNewAreaDeliveryClose()' class='white'>X close</a>";
		obj.appendChild(bottom);
	}

	/*** ���������� ***/
	var ifrm = document.createElement("iframe");
	with (ifrm.style){
		width = layerWidth - 6 +'px';
		height = layerHeight - pixelBorder * 2 - 3 +'px';
	}

	ifrm.id = 'newAreaIframe';
	ifrm.name = 'newAreaIframe';
	ifrm.frameBorder = 0;
	obj.appendChild(ifrm);
	if(srcType=='migration') ifrm.src = 'popup.newAreaDeliveryMigration.php';
	else ifrm.src = 'popup.newAreaDelivery.php';
}

function addNewAreaDeliveryClose()
{
	_ID('newAreaLayerObj1').parentNode.removeChild( _ID('newAreaLayerObj1') );
	_ID('newAreaLayerBg1').parentNode.removeChild( _ID('newAreaLayerBg1') );
}

window.onload = function(){
	chkDeliveryType(document.getElementsByName('deliveryType')[0]);
	<?
	$i=0;
	foreach($r_set as $v){
	?>
	chkDeliveryType(document.getElementsByName('r_deliType[]')[<?=$i?>]);
	<?
	$i++;
	}
	?>
	fm = document.forms[0];
	selL = fm.delivery_tmp;
	selR = fm['delivery[]'];
	tbOver = document.getElementById('tbOver');
	chkAreaDeli();
	requiredOver();
}

</script>

<form method=post action="indb.php" onsubmit="return chkForm2(this)" name='form'>
<input type=hidden name=mode value="delivery">

<div class="title title_top">�����å<span>��ۺ�� �� ��۰��� ��å�� ���ϼ���</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=3')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a></div>


<div style="padding: 20px 0px 5px 13px"><b>1. �⺻ �����å</b> <font class=extext>(��۹���� ������ ����� �� �ֽ��ϴ�) (��. ���Ϲ��, ������ ��)</font></div>

<table width=100% cellpadding=0 cellspacing=0 border=0 id="tbl_delivery">
<tr>
	<td class=rnd colspan=12></td>
</tr>
<tr class=rndbg>
	<th width="50">����</th>
	<th width="120">��۹��</th>
	<th>��ۺ�</th>
	<th width="50">����</th>
</tr>
<tr>
<td class=rnd colspan=12></td>
</tr>
<tr><td colspan=20 height=10></td></tr>
<?
if($set['deliveryType'] == '����' || !$set['deliveryType'])$selected['deliveryType'][0] = " selected";
else $selected['deliveryType'][1] = " selected";
?>
<tr height=30>
	<td class="center">1</td>
	<td class="center ver81"><input type=text name="deliverynm" size=10 value="<?=$set['deliverynm']?>" class="line" required></td>
	<td class="ver81">�� ���ž��� <input type=text name="free" value="<?=$set['free']?>" size=9 class="rline" onkeydown="onlynumber();"> �� �̻��� �� ��ۺ� ����, �̸��� ��
			<?if ( !preg_match( "/^rental_mxfree/i", $godo[ecCode] ) ){?><select name="deliveryType" onchange="chkDeliveryType(this)">
			<option value="����"<?=$selected['deliveryType'][0]?>>����</option>
			<option value="�ĺ�"<?=$selected['deliveryType'][1]?>>����</option>
			</select><?}else{?><input type="hidden" name="deliveryType" value="����" class="rline"><?}?><span style="display:none;"> <input type="text" name="default" value="<?=$set['default']?>" size="8"  class="rline" onkeydown="onlynumber()"> �� ��ۺ� �ΰ�</span><span style="display:none;"> ��۸޼��� : <input type="text" name="default_msg" value="<?=$set['default_msg']?>" size="20" style="width:120" class="lline" ></span>
	</td>
	<td class="center">-</td>
</tr>
<?
	if ( !preg_match( "/^rental_mxfree/i", $godo[ecCode] ) ){
	$num=1;
	foreach($r_set as $k => $v){
		$num++;
		$selected[r_deliType][0] = $selected[r_deliType][1] = "";
		if($v[r_deliType]=='����') $selected[r_deliType][0] = " selected";
		else  $selected[r_deliType][1] = " selected";

		if($k){
	?>

<tr height=30>
	<td class="center"><?=$num?></td>
	<td class="center"><input type=text name="r_delivery[]" size=10 value="<?=$k?>" class="line" required></td>
	<td class="ver81">�� ���ž��� <input type=text name="r_free[]" size=9 class="rline" value="<?=$v[r_free]?>" onkeydown="onlynumber();"> �� �̻��� �� ��ۺ� ����, �̸��� ��
			<select name="r_deliType[]" onchange="chkDeliveryType(this)">
			<option value="����"<?=$selected[r_deliType][0]?>>����</option>
			<option value="�ĺ�"<?=$selected[r_deliType][1]?>>����</option>
			</select><span style="display:none;"> <input type=text name="r_default[]" size=8 class="rline" value="<?=$v['r_default']?>" onkeydown="onlynumber()"> �� ��ۺ� �ΰ�</span><span style="display:none;"> ��۸޼��� : <input type="text" name="r_default_msg[]" value="<?=$v['r_default_msg']?>" size="20" style="width:120" class="lline"></span>
	</td>
	<td class="center"><a href="javascript:void(0)" onClick="delDelivery(this)"><img src="../img/btn_delete_new.gif"></a></td>
</tr>
<?}}}?>



</table>
<table width=100%>
<tr><td colspan=20 height=10></td></tr>
<tr><td colspan=20 height=1 bgcolor=e2e2e2></td></tr>
<tr>
	<td class="extext"><div style="padding-top:4px"></div>* ���Ҽ����� ���Ҹ޽����� �ʼ��� �Է��ϼž� �ϸ� �ϴ��� ��� ��ư�� Ŭ���ϼž� ������ ����˴ϴ�.	</td>
</tr>
</table>
<?if ( !preg_match( "/^rental_mxfree/i", $godo[ecCode] ) ){?><div style="padding:10px 0px 20px 0px" align="center"><a href="javascript:addDelivery();"><img align="absmiddle" src="../img/btn_delivery_plus.gif"  class="null" /></a></div><?}?>


<br>

<div style="padding: 10px 0px 5px 13px"><b>2. ��ǰ�� �����å</b> <font class=extext>(��ǰ���� ��ۺ� å���� �� �ֽ��ϴ�)</font></div>

<table class=tb>
<col class=cellC><col class=cellL>
<?if ( !preg_match( "/^rental_mxfree/i", $godo[ecCode] ) ){?>
<tr>
	<td>������ ��ǰ</td>
	<td>
		<div><input type="radio" name="freeDelivery" value="0" class="null" <?=$checked[freeDelivery][0]?>>������ ��ǰ�� ���� �ֹ����� ���, �����ۻ�ǰ�� ��ۺ� ����� �մϴ�.</div>
		<div><input type="radio" name="freeDelivery" value="1" class="null" <?=$checked[freeDelivery][1]?>>������ ��ǰ�� ���� �ֹ����� ���, �ش� �ֹ����� ��ۺ� �Բ� ����� �մϴ�.</div>
	</td>
</tr>
<tr>
	<td>��ǰ�� ��ۺ�</td>
	<td>
		<div><input type="radio" name="goodsDelivery" value="0" class="null" <?=$checked[goodsDelivery][0]?>>��ǰ�� 2���̻� �ֹ���, ��ǰ�� ��ۺ�� �⺻��ۺ� �ջ��� �ݾ��� ��ۺ�� �մϴ�.</div>
		<div><input type="radio" name="goodsDelivery" value="1" class="null" <?=$checked[goodsDelivery][1]?>>��ǰ�� 2���̻� �ֹ���, ��ǰ�� ��ۺ�� �⺻��ۺ� �� �� ū ��ۺ�� �մϴ�.</div>
		<div><input type="radio" name="goodsDelivery" value="2" class="null" <?=$checked[goodsDelivery][2]?>>��ǰ�� 2���̻� �ֹ���, ��ǰ�� ��ۺ��� ���հ� �⺻��ۺ� �� �� ū ��ۺ�� �մϴ�.</div>
	</td>
</tr>
<input type=hidden name='basis' value='<?=$set[basis]?>' />
<?}else{?>
<input type=hidden name='basis' value='0' />
<?}?>
</table>

<div style="padding: 25px 0px 5px 13px"><b>3. ������ �����å</b> <font class=extext>(�����갣 �� �������� ��۱ݾ��� ������ �� �ֽ��ϴ�)</font></div>

<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td style="width: 130px;">
		<div style="padding-left:10">������ ��۱ݾ�</div>
		<div style="font-weight:normal"><input type="radio" name="area_deli_type" value='2' class="null" onclick="chkAreaDeli()" <?=$checked[area_deli_type][2]?> />������/���θ� ����</div>
		<div style="font-weight:normal"><input type="radio" name="area_deli_type" value='0' class="null" onclick="chkAreaDeli()" <?=$checked[area_deli_type][0]?> />���������� ����</div>
		<div style="font-weight:normal"><input type="radio" name="area_deli_type" value='1' class="null" onclick="chkAreaDeli()" <?=$checked[area_deli_type][1]?> />�����ȣ�� ����</div>
	</td>
	<td>
	<table id="tbOverNew" width="100%">
	<colgroup>
		<col width="100" />
		<col width="*" />
	</colgroup>
	<tr>
		<td width="100">
			<?php if( $newAreaDeliveryCount < 1 && ($set['delivery']['areaZip1'] || $r_area[deliveryArea])){ ?>
				<a href="javascript:addNewAreaDelivery('migration');">
			<?php } else { ?>
				<a href="javascript:addNewAreaDelivery('normal');">
			<?php } ?>
			<img src="../img/btn_placeaddNew.gif"></a>
			&nbsp;
		</td>
		<td valign="top" style="padding-top: 6px;">���� <span class="extext_t" style="font-weight: bold;"><?php echo number_format($newAreaDeliveryCount); ?>��</span>�� ������ ��ϵǾ� �ֽ��ϴ�.</td>
	</tr>
	<tr>
		<td colspan=2>
			<table width="100%" cellpadding="0" cellspacing="0" border="0" style="font: 9pt dotum;">
			<colgroup>
				<col width="30" />
				<col width="*" />
			</colgroup>
			<tr height="25">
				<td style="padding: 10px 0px 5px 0px; font-weight: bold;" colspan="2">��������/���θ� �����̶�?</td>
			</tr>
			<tr height="25">
				<td style="padding-left: 10px;"><img src="../img/icon_lotnumber.gif" /></td>
				<td style="padding: 3px 0px 0px 3px;">���� �ּҴ� �⺻! ���θ� ���ּұ��� ����� �� �ֽ��ϴ�!</td>
			</tr>
			<tr height="25">
				<td style="padding-left: 10px;"><img src="../img/icon_post.gif" /></td>
				<td style="padding: 3px 0px 0px 3px;">������ �����ȣ ������ ���� �ʾƵ� �ּ��� �Է¸����� ������ �Ϸ�˴ϴ�!</td>
			</tr>
			<tr height="25">
				<td style="padding-left: 10px;"><img src="../img/icon_area.gif" /></td>
				<td style="padding: 3px 0px 0px 3px;">'�õ���(��) ���鵿(��)'�� ���� �������� ���� ���� �����մϴ�!</td>
			</tr>
			<tr height="25">
				<td style="padding-left: 10px;"><img src="../img/icon_excel.gif" /></td>
				<td style="padding: 3px 0px 0px 3px;">���������� �̿��� �ϰ� ����� �����մϴ�!</td>
			</tr>
			</table>
			<?php if($skinPatchMsg === false){ ?>
			<div class="extext_t">�� <strong><u>[���� ��Ų��ġ ���� �ʿ�]</u></strong> -> ��Ų ��ġ�� �������� ���� ��� ���ֹ��� �ۼ����������� ������ �߰���ۺ� ������� �ʽ��ϴ�.(��, ���� �� ������ �߰���ۺ� �����ݾ׿� ���ԵǹǷ� <span style="text-decoration: underline;">��ȥ�� ������ ���Ͽ� �ݵ�� ��Ų ��ġ�� ����</span>�Ͽ� �ֽñ� �ٶ��ϴ�.)&nbsp;<a href="http://www.godo.co.kr/customer_center/patch.php?sno=2113" target="_blank"><img src="../img/btn_detail.gif" style="border: 0px; vertical-align: middle;" /></a>
			<?php } ?>
	</tr>
	</table>
	<table id="tbOver" width="100%">
	<col><col align="right">
	<? if ($overAdd){ foreach ($overAdd as $k=>$v){ ?>
	<tr>
		<td>
		<table>
		<tr>
			<td>�Ʒ� ������ ��ۺ� <input type="text" name="overAdd[]" value="<?=$overAdd[$k]?>" class="rline"> ���� �߰� �մϴ�.</td>
			<td><a href="javascript:popup('popup.areaDelivery.php?idx=<?=$k?>',300,300);"><img src="../img/btn_area_search.gif" align="absmiddle" value="�����˻��ϱ�" /></a></td>
		</tr>
		</table>
		<div class=extext style="padding-top:5px">(�ݵ�� <b>'�����˻��ϱ�'</b>�� ������ ������ �߰��ϼ���)</font></div>
		</td>
		<td><? if (!$k){ ?><a href="javascript:addOver()"><img src="../img/btn_placeadd.gif"></a><? } else { ?><a href="javascript:void(0)" onClick="delOver(this)"><img src="../img/i_del.gif"></a><? } ?></td>
	</tr>
	<tr>
		<td colspan=2><textarea name="overZipcodeName[]" style="width:100%;height:50px" class="tline"><?=$arr_area[$k]?></textarea>
	</tr>
	<? }} ?>
	</table>
	<table id="tbOverZip" width="100%">
		<col><col align="right">
	<? if ($overAddZip){ foreach ($overAddZip as $k=>$v){ ?>
	<tr>
		<td>�Ʒ� ������ ��ۺ� <input type="text" name="overAddZip[]" value="<?=$overAddZip[$k]?>" class="rline"> ���� �߰� �մϴ�.</td>
		<td><? if (!$k){ ?><a href="javascript:addOverZip()"><img src="../img/btn_placeadd.gif"></a><? } else { ?><a href="javascript:void(0)" onClick="delOverZip(this)"><img src="../img/i_del.gif"></a><? } ?></td>
	</tr>
	<tr>
		<td>
		<div><a href="javascript:popup('../proc/popup_zipcode.delivery.php?idx=<?=$k?>',500,340)"><img src="../img/btn_zipcode.gif" border="0" align="absmiddle"></a> <input type='text' name='areaZip1[]' size="6" value="<?=$areaZip1[$k]?>" readonly>���� <input type='text' name='areaZip2[]' value="<?=$areaZip2[$k]?>" size="6" readonly>����</div>
		<div class=extext style="padding-top:5px">(�ݵ�� <b>'�����ȣ�˻�'</b>�� ������ ������ Ȯ�� �� �߰��ϼ���)</div>
		</td>
	</tr>
	<? }} ?>
	</table>

<div class="extext_t">* �Ϲ������� ������ ��۱ݾ��� ����ȭ�ϴ� ���� ��, �����갣 � �ش�˴ϴ�. ( ��: ���ֵ�,�︪�� ��)</div>

	</td>
</tr>
<tr>
	<td>������ �� <br>������ �߰� ��ۺ�</td>
	<td>
		<table class="tb">
		<col class="cellC"><col>
		<tr>
			<td width="130">�����ۺ� ��ǰ �ֹ���</td>
			<td>
				<label><input type="radio" name="add_extra_fee_free" value="1" <?=$set['add_extra_fee_free'] == '1' ? 'checked' : '' ?> /> ������ �߰� ��ۺ� ����</label>
				<div class="extext" style="padding-top:5px">�����ۺ� ��ǰ �ֹ��ÿ��� ������ �߰� ��ۺ� �޽��ϴ�.</div>

				<label><input type="radio" name="add_extra_fee_free" value="0" <?=$set['add_extra_fee_free'] == '0' ? 'checked' : '' ?> /> ������ �߰� ��ۺ� ���� ����</label>
				<div class="extext" style="padding-top:5px">�����ۺ� ��ǰ �ֹ��� ������ �߰� ��ۺ� ���� �ʽ��ϴ�.</div>
			</td>
		</tr>

		<tr>
			<td>�⺻ �����å�� ����<br>���Ǻ� ������ ���</td>
			<td>
				<label><input type="radio" name="add_extra_fee_basic" value="1" <?=$set['add_extra_fee_basic'] == '1' ? 'checked' : '' ?> /> ������ �߰� ��ۺ� ����</label>
				<div class="extext" style="padding-top:5px">�⺻ �����å�� ���� �����ݾ� �̻��϶� ��ۺ� ������ ��쿡�� ������ �߰� ��ۺ� �޽��ϴ�.</div>

				<label><input type="radio" name="add_extra_fee_basic" value="0" <?=$set['add_extra_fee_basic'] == '0' ? 'checked' : '' ?> /> ������ �߰� ��ۺ� ���� ����</label>
				<div class="extext" style="padding-top:5px">�⺻ �����å�� ���� �����ݾ� �̻��϶� ��ۺ� ������ ��� ������ �߰� ��ۺ� ���� �ʽ��ϴ�.</div>
			</td>
		</tr>

		<tr>
			<td>ȸ�� �׷� ���ÿ� ����<br>��ۺ� ������ ���</td>
			<td>
				<label><input type="radio" name="add_extra_fee_memberGroup" value="1" <?=$set['add_extra_fee_memberGroup'] == '1' ? 'checked' : '' ?> /> ������ �߰� ��ۺ� ����</label>
				<div class="extext" style="padding-top:5px">������ ������ �����Ǵ� ȸ���׷� ȸ���� �ֹ��ÿ��� ������ �߰� ��ۺ� �޽��ϴ�.</div>

				<label><input type="radio" name="add_extra_fee_memberGroup" value="0" <?=$set['add_extra_fee_memberGroup'] == '0' ? 'checked' : '' ?> /> ������ �߰� ��ۺ� ���� ����</label>
				<div class="extext" style="padding-top:5px">������ ������ �����Ǵ� ȸ���׷� ȸ���� �ֹ��� ������ �߰� ��ۺ� ���� �ʽ��ϴ�.</div>
			</td>
		</tr>
		</table>
	</td>
</tr>


<tr>
	<td>�����å�� ����<br>������ �߰� ��ۺ�
	<td>
		<!-- ������ۻ�ǰ (���̻� ������� ����) �� ���� ���� ���Ž� ����� -->
		<input type="hidden" name="add_extra_fee_duplicate_each" value="1" />

		<table>
		<tr>
			<td>���� ��ۺ� ��ǰ 2���� �̻� �ֹ���</td>
			<td><label><input type="radio" name="add_extra_fee_duplicate_free" value="1" <?=$set['add_extra_fee_duplicate_free'] == '1' ? 'checked' : '' ?> /> ��ǰ �������� �ΰ�</label></td>
			<td><label><input type="radio" name="add_extra_fee_duplicate_free" value="0" <?=$set['add_extra_fee_duplicate_free'] == '0' ? 'checked' : '' ?> /> �ѹ��� �ΰ�</label></td>
		</tr>
		<tr>
			<td>���� ��ۺ� ��ǰ 2���� �̻� �ֹ���</td>
			<td><label><input type="radio" name="add_extra_fee_duplicate_fixEach" value="1" <?=$set['add_extra_fee_duplicate_fixEach'] == '1' ? 'checked' : '' ?> /> ��ǰ �������� �ΰ�</label></td>
			<td><label><input type="radio" name="add_extra_fee_duplicate_fixEach" value="0" <?=$set['add_extra_fee_duplicate_fixEach'] == '0' ? 'checked' : '' ?> /> �ѹ��� �ΰ�</label></td>
		</tr>
		<tr>
			<td colspan="3"><div class="extext" style="padding-top:5px">�����ۺ�, ������ۺ� ��ǰ 2���� �̻� �ֹ��� ������ �߰� ��ۺ� �ΰ� ����� �����մϴ�.</div></td>
		</tr>
		</table>

		<div class="extext" style="padding-top:15px">�� �⺻��ۺ� ��ǰ �ֹ��� (���Ǻ� ������ ����) ������ ������� ������ �߰� ��ۺ�� �ѹ��� �޽��ϴ�.</div>
		<div class="extext" style="padding-top:5px">�� ���ҹ�ۺ� ��ǰ �ֹ��� ������ �߰� ��ۺ�� ���� �ʽ��ϴ�.</div>
		<div class="extext" style="padding-top:5px">�� ��������ۺ� ��ǰ �ֹ��� �ֹ� �������� ������ �߰� ��ۺ� �޽��ϴ�.</div>
	</td>
</tr>
</table>

<div style="padding: 25px 0px 5px 13px"><b>4. ��ۺ� �������</b> <font class=extext>(�⺻ �����å ��ۺ� ���� ������ �����մϴ�.)</font></div>
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>��ۺ� ����</td>
	<td>
		<div><input type="radio" name="deliveryOrder" value="0" class="null" <?=$checked['deliveryOrder']['0']?>>�ֹ���ǰ�� ������ ����</div>
		<div><input type="radio" name="deliveryOrder" value="1" class="null" <?=$checked['deliveryOrder']['1']?>>�ֹ���ǰ�� ���� ����</div>
	</td>
</tr>
</table>
<div class="extext_t">* �ֹ���ǰ�� ������ ���� ���� �� �ֹ� ��ǰ�ݾ׿��� ����(��������,ȸ������,��ǰ����)�� ����� �ݾ��� �������� �����å�� �����մϴ�.<br />* �ֹ���ǰ�� ���� ���� ���� �� �ֹ� ��ǰ�ݾ��� �������� �����å�� �����մϴ�.</div>
<br />

<div class=title title_top style="position:relative;padding-bottom:15px">
<font color=black>�ù��/������� ����</font>
<span>����ϴ� �ù�縦 �����ϰ� ������� �ּҸ� ��������</font></span>
<a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=3')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a>
<div style="position:absolute;left:100%;width:231px;height:44px;margin-left:-240px;margin-top:-15px"><a href="../order/post_introduce.php"><img src="../img/btn_postoffic_reserve_go.gif"></a></div>
</div>
<div class="rndline2"></div>
<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
<td>
	<table cellpadding="0" cellspacing="10" border="0">
	<tr>
	<td>&nbsp;&nbsp;<img src="../img/arrow_downorg.gif" align=absmiddle> <font class=man>�ù�� ��ü����Ʈ </font><font class=small1>(����Ŭ���ϼ���)</font></td>
	<td></td>
	<td>&nbsp;&nbsp;<img src="../img/arrow_downorg.gif" align=absmiddle> <font class=man>�̿� �ù�� </font><font class=small1>(������ ����Ŭ��)</font></td>
	</tr>
	<tr>
	<td>
	<select name=delivery_tmp multiple style="width:200px;height:156px" ondblclick="move('right')">
	<? foreach ($delivery_tmp as $v){ ?>
	<option value="<?=$v[deliveryno]?>"><?=$v[deliverycomp]?>
	<? } ?>
	</select>
	</td>
	<td style="font-size:36px">
	<a href="javascript:move('right')"><font class="color_r">��</font></a><p>
	<a href="javascript:move('left')"><font class="color_l">��</font></a>
	</td>
	<td>
	<select name=delivery[] multiple style="width:200px;height:156px" ondblclick="move('left')">
	<? foreach ($delivery as $v){ ?>
	<option value="<?=$v[deliveryno]?>"><?=$v[deliverycomp]?>
	<? } ?>
	</select>
	</td>
	</tr>
	</table>


	<table border=0 cellpadding=0 cellspacing=0>
	<tr>
	<td style="padding-left:3px;" class="extext"><!--<font class=small1 color=444444>* ����Ʈ�� ����Ͻ� �ù�簡 ���ٸ�&nbsp; <a href="javascript:registerDelivery()"><img src="../img/btn_deliadd.gif" border=0 vspace=2 align=absmiddle></a> �ϼ���.<br>-->* ��������ּҸ� �����Ϸ��� ���� �ù�� ��ü����Ʈ���� �ù�縦 �����ϰ�&nbsp; <a href="javascript:modifyDelivery()"><img src="../img/btn_deliedit.gif" border=0 vspace=2 align=absmiddle></a> �� ��������.
	<div style="padding-top:4px"></div>
	* ��������̶� �ֹ��� ���� �������������� ���� ��ۻ��¸� Ȯ���ϴ� ���Դϴ�.
	<div style="padding-top:4px"></div>
	* �� ó�� ���õǾ��� ��ۻ簡 �⺻ ��ۻ� �Դϴ�.
	</td>
	</tr>
	</table>
	<div style="padding-top:10px"></div>
</td>
</tr>
</table>
<div class="rndline2"></div>

<div class=button>
<input type=image src="../img/btn_register.gif">
<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a>
</div>

</form>
<? include "../_footer.php"; ?>