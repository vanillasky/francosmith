<?

$scriptLoad='<script src="./js/common.js"></script>';
include "../_header.popup.php";

$goodsno = $_GET['goodsno'];
list($cnt) = $db->fetch("select count(*) from ".GD_OPENMARKET_GOODS." where goodsno='{$goodsno}'");
$mode = ($cnt ? "modify" : "register");

$r_maker[''] = $r_originnm[''] = $r_brandnm[''] = "-- ��Ϻ��� --";

### ������
$query = "select distinct maker from ".GD_GOODS;
$res = $db->query($query);
while ($data=$db->fetch($res)) if ($data['maker']) $r_maker[$data['maker']] = $data['maker'];

### ������
$handle = @fopen("./_origin.txt", "r");
if ($handle) {
    while (!feof($handle)) {
        $buffer = fgets($handle, 4096);
        $r_originnm[$buffer] = $buffer;
    }
    fclose($handle);
}

### �귣��
$query = "select * from ".GD_GOODS_BRAND." order by sort";
$res = $db->query($query);
while ($data=$db->fetch($res)) if ($data['brandnm']) $r_brandnm[$data['brandnm']] = $data['brandnm'];

### ��ǰ ���� ��������
if ($mode == "register")
{
	$data = $db->fetch("select * from ".GD_GOODS." where goodsno='$goodsno'",1);
	$data = array_map("slashes",$data);
	$data['age_flag'] = 'N';

	### ������
	if (in_array($data['origin'], array('����', '�ѱ�', '���ѹα�')) === true){
		$data['origin_kind'] = 1;
	}
	else {
		$data['origin_kind'] = 2;
		$data['origin_name'] = $data['origin'];
	}

	### �귣���
	list($data['brandnm']) = $db->fetch("select brandnm from ".GD_GOODS_BRAND." where sno='{$data['brandno']}'");

	### ���¸��� �з��ڵ�
	list($data['category']) = $db->fetch("select openmarket from ".GD_GOODS_LINK." as a left join ".GD_CATEGORY." as b on a.category = b.category  where openmarket!='' and goodsno='{$data['goodsno']}' order by a.category limit 1");

	### �ʼ��ɼ�
	$optnm = explode("|",$data['optnm']);
	$query = "select * from ".GD_GOODS_OPTION." where goodsno='$goodsno'";
	$res = $db->query($query);
	while ($tmp=$db->fetch($res)){
		$tmp = array_map("htmlspecialchars",$tmp);
		$opt1[] = $tmp['opt1'];
		$opt2[] = $tmp['opt2'];
		$opt[$tmp['opt1']][$tmp['opt2']] = $tmp;

		### ����� ���
		$stock += $tmp['stock'];
	}
	if ($opt1) $opt1 = array_unique($opt1);
	if ($opt2) $opt2 = array_unique($opt2);
	if (!$opt){
		$opt1 = array('');
		$opt2 = array('');
	}

	### �⺻ ���� �Ҵ�
	$data['price']	  = $opt[$opt1[0]][$opt2[0]]['price'];
	$data['consumer'] = $opt[$opt1[0]][$opt2[0]]['consumer'];
}
else {
	$data = $db->fetch("select * from ".GD_OPENMARKET_GOODS." where goodsno='$goodsno'",1);
	$data = array_map("slashes",$data);

	### �ʼ��ɼ�
	$optnm = explode("|",$data['optnm']);
	$query = "select * from ".GD_OPENMARKET_GOODS_OPTION." where goodsno='$goodsno'";
	$res = $db->query($query);
	while ($tmp=$db->fetch($res)){
		$tmp = array_map("htmlspecialchars",$tmp);
		$opt1[] = $tmp['opt1'];
		$opt2[] = $tmp['opt2'];
		$opt[$tmp['opt1']][$tmp['opt2']] = $tmp;

		### ����� ���
		$stock += $tmp['stock'];
	}
	if ($opt1) $opt1 = array_unique($opt1);
	if ($opt2) $opt2 = array_unique($opt2);
	if (!$opt){
		$opt1 = array('');
		$opt2 = array('');
	}
}

$checked['origin_kind'][$data['origin_kind']] = "checked";
$checked['tax'][$data['tax']] = "checked";
$checked['usestock'][$data['usestock']] = "checked";
$checked['runout'][$data['runout']] = "checked";
$checked['age_flag'][$data['age_flag']] = "checked";
$checked['noSameShipAS'][$data['noSameShipAS']] = "checked";

$img_m = explode("|",$data['img_m']);

### ȯ��(��ۤ�A/S)
if ($data['noSameShipAS'] != 'o')
{
	@include "../../conf/openmarket.php";
	if (isset($omCfg) === true) $data = array_merge($data, $omCfg);
}

$checked['ship_type'][$data['ship_type']] = "checked";
$checked['ship_pay'][$data['ship_pay']] = "checked";

if ($data['ship_type'] == '0'){
	$data['ship_price_0'] = $data['ship_price'];
}
else if ($data['ship_type'] == '5'){
	$data['ship_price_5'] = $data['ship_price'];
	$data['ship_base_5'] = $data['ship_base'];
}
else if ($data['ship_type'] == '4'){
	$data['ship_price_4'] = $data['ship_price'];
	$data['ship_base_4'] = $data['ship_base'];
}

?>

<script>
/* �ɼ� �κ� ���� */
function delopt1part(rid)
{
	var obj = document.getElementById(rid);
	var tbOption = document.getElementById('tbOption');
	if (tbOption.rows.length>2) tbOption.deleteRow(obj.rowIndex);
}
function delopt2part(cid)
{
	var obj = document.getElementById(cid);
	var tbOption = document.getElementById('tbOption');

	if (tbOption.rows[0].cells.length<3) return;
	for (i=0;i<tbOption.rows.length;i++){
		tbOption.rows[i].deleteCell(obj.cellIndex);
	}
}

/*** ��üũ ***/
function chkForm2(obj)
{
	if (!chkOption()) return false;
	if (!chkForm(obj)) return false;
}

/*** ��ǰ ����/��� ***/
function addopt1()
{
	var name;
	var fm = document.forms[0];
	var tbOption = document.getElementById('tbOption');
	var Rcnt = tbOption.rows.length;
	oTr = tbOption.insertRow();
	oTr.id = "trid_" + Rcnt;

	for (i=0;i<tbOption.rows[0].cells.length;i++){
		oTd = oTr.insertCell();
		switch (i){
			case 0: oTd.innerHTML = "<input type='text' class='opt gray' name=opt1[] value='�ɼǸ�1' required label='1���ɼǸ�' ondblclick=\"delopt1part('"+oTr.id+"')\" onclick='chkOptName(this)' onblur=\"chkOptNameOver(this)\">";
			break;
			default: oTd.innerHTML = "<input type='text' name=option[stock][] class='opt gray' value='���' onclick='chkOptName(this)' onblur=\"chkOptNameOver(this)\">"; break;
		}
	}
}
function addopt2()
{
	var name;
	var tbOption = document.getElementById('tbOption');
	if (tbOption.rows.length<3){
		alert('1���ɼ��� ���� �߰����ּ���');
		return;
	}

	var Ccnt = tbOption.rows[0].cells.length;

	for (i=0;i<tbOption.rows.length;i++){
		oTd = tbOption.rows[i].insertCell();
		if(!i)oTd.id = "tdid_"+Ccnt;
		oTd.innerHTML = (i) ? "<input type='text' name=option[stock][] class='opt gray'  value='���' onclick='chkOptName(this)' onblur=\"chkOptNameOver(this)\">" : "<input type='text' class='opt gray' name=opt2[] value='�ɼǸ�2' required label='2���ɼǸ�' ondblclick=\"delopt2part('"+oTd.id+"')\" onclick='chkOptName(this)' onblur=\"chkOptNameOver(this)\">";
	}
}
function delopt1()
{
	var tbOption = document.getElementById('tbOption');
	if (tbOption.rows.length>2) tbOption.deleteRow();
}
function delopt2()
{
	var tbOption = document.getElementById('tbOption');
	if (tbOption.rows[0].cells.length<7) return;
	for (i=0;i<tbOption.rows.length;i++){
		tbOption.rows[i].deleteCell();
	}
}

/*** �ڵ����� �����ʵ忡 �Է°� ���� ***/
function autoPrice(obj)
{
	var name = obj.name;
	var el = document.getElementsByName('option[' + name + '][]');
	el[0].value = obj.value;
}

function vOption()
{
	document.fm.stock.disabled = !document.fm.stock.disabled;
	openLayer('objOption');
}

function chkOptName(obj){
	if(obj.value=='�ɼǸ�2' || obj.value=='�ɼǸ�1'){
		obj.className = 'fldtitle';
		obj.value = '';
	}
	if(obj.value=='���'){
		obj.className = 'opt';
		obj.value = '';
	}
}

function chkOptNameOver(obj){
	if(obj.value==''){
		obj.className = 'opt gray';
		if(obj.name == 'opt1[]') obj.value = '�ɼǸ�1';
		if(obj.name == 'opt2[]') obj.value = '�ɼǸ�2';
		if(obj.name == 'option[stock][]') obj.value = '���';
	}
}

function chkOption(){
	var obj = document.getElementsByName('opt1[]');
	var chk = false;
	for(var i=0;i < obj.length;i++){
		 chkOptName(obj[i]);
		 if(obj[i].value == '' && obj.length > 1){
			alert('�ɼ� 1�� �ʼ� �׸��Դϴ�.');
			obj[i].focus();
			return false;
		 }
		 if( (obj[i].value || obj.length > 1) && !chk) chk = true;
	}

	var obj = document.getElementsByName('opt2[]');
	for(var i=0;i < obj.length;i++){
		chkOptName(obj[i]);
		if(chk && obj[i].value == '' && obj.length > 1){
			alert('�ɼ� 2�� �ʼ� �׸��Դϴ�.');
			obj[i].focus();
			return false;
		}
	}

	var obj = document.getElementsByName('option[stock][]');
	for(var i=0;i < obj.length;i++){
		chkOptName(obj[i]);
	}

	return true;
}

/*** ��ۤ�A/S ***/
function setDisabled()
{
	obj = document.getElementsByName('noSameShipAS')[0];
	isDisabled = (obj.checked == true ? false : true);
	inputObj = _ID('shipAS').getElementsByTagName('input');
	for (j = 0; j < inputObj.length; j++){
		inputObj[j].disabled = isDisabled;
		if (inputObj[j].type == 'text') inputObj[j].style.backgroundColor = (isDisabled ? '#DDDDDD' : '#FFFFFF');
	}
	if (obj.checked) setShipDisabled();
}

function setShipDisabled()
{
	obj = document.getElementsByName('ship_type');
	for (i = 0; i < obj.length; i++){
		isDisabled = (obj[i].checked == true ? false : true);
		inputObj = obj[i].parentNode.parentNode.getElementsByTagName('td')[1].getElementsByTagName('input');

		for (j = 0; j < inputObj.length; j++){
			inputObj[j].disabled = isDisabled;
			inputObj[j].style.backgroundColor = (isDisabled ? '#DDDDDD' : '#FFFFFF');
		}
	}
}
</script>

<div class="title title_top" style="margin-top:10px;">���¸��� ��ǰ ������� <span>�Ŵ����� ������ ����Ÿ���� Ȯ�� �� ���������� ����մϴ�. &nbsp;&nbsp; <font color="#FF1800"><b>*</b></font> ǥ�õ� �׸��� �ʼ��Է»����Դϴ�.</span></div>

<? if ($mode == 'modify'){ ?>
<div style="padding:10px 10px; margin:10px 0 30px 0; background-color:#F7F7F7; color:#70B600; font:9pt Gulim; font-weight:bold;">
����� :<?=$data['regdt']?>, &nbsp;&nbsp;&nbsp; ������ : <?=$data['moddt']?>
</div>
<? } ?>

<form name="fm" method="post" action="./indb.goods.php" enctype="multipart/form-data" onsubmit="return chkForm2(this)" target="ifrmHidden">
<input type="hidden" name="mode" value="<?=$mode?>">
<input type="hidden" name="goodsno" value="<?=$goodsno?>">

<!-- ī�װ� ���� -->
<input type="hidden" name="category" value="<?=$data['category']?>" id="catnm" required label="���¸��� ǥ�غз�">
<div class="title2">&nbsp;<img src="../img/icon_process.gif" align="absmiddle"><font class="def1" color="#0074BA"><b> �� ���¸��� ǥ�غз� ��Ī</b></font> <font class="small1" color="#6d6d6d">(�� ���θ� ��ǰ�� ���¸��� ǥ�غз��� ��Ī�Ͽ� ����ϼ���.)</font></div>
<div class="box" style="padding-left:0px">
<table width="100%" cellpadding=1 cellspacing=0 border=1 bordercolor="#cccccc" style="border-collapse:collapse">
<tr>
	<td style="padding:20px 10px" bgcolor=f8f8f8 id="catnm_text"><script>callCateNm('<?=$data['category']?>','catnm','link');</script></td>
</tr>
</table>
</div>

<!-- ��ǰ�⺻���� -->
<div style="height:30px"></div>
<div class="title2">&nbsp;<img src="../img/icon_process.gif" align="absmiddle"><font class="def1" color="#0074BA"><b> �� ��ǰ�⺻����</b></font> <font class="small1" color="#6d6d6d">(��ǰ��, �𵨸�, ������, ������, �귣����� Ȯ���Ͻð�, ���¸��Ͽ� ����ϱ� ���� ������ �ʿ��� �κ��� �����ϼ���.)</font></div>
<table class="tb">
<col class="cellC"><col class="cellL"><col class="cellC"><col class="cellL">
<tr>
	<td width="120" nowrap>��ǰ��<font color="#FF1800"><b>*</b></font></td>
	<td width="50%"><input type="text" name="goodsnm" style="width:100%" value="<?=$data['goodsnm']?>" required label="��ǰ��"></td>
	<td width="120" nowrap>�𵨸�(��ǰ�ڵ�)<font color="#FF1800"><b>*</b></font></td>
	<td width="50%"><input type="text" name="goodscd" style="width:100%" value="<?=$data['goodscd']?>" required label="�𵨸�"></td>
</tr>
<tr>
	<td>������<font color="#FF1800"><b>*</b></font></td>
	<td>
	<input type="text" name="maker" value="<?=$data['maker']?>" required label="������">
	<select onchange="this.form.maker.value=this.value;this.form.maker.focus()">
	<? foreach ($r_maker as $k=>$v){ ?><option value="<?=$k?>"><?=$v?><? } ?>
	</select>
	</td>
	<td rowspan="3">������<font color="#FF1800"><b>*</b></font></td>
	<td rowspan="3">
	<div>
	<input type="radio" name="origin_kind" value="1" <?=$checked['origin_kind'][1]?> required label="������ ����" class="null"> ����
	<input type="radio" name="origin_kind" value="2" <?=$checked['origin_kind'][2]?> required label="������ ����" class="null"> ����
	<input type="radio" name="origin_kind" value="3" <?=$checked['origin_kind'][3]?> required label="������ ����" class="null"> ��
	</div>
	<div><input type="text" name="origin_name" value="<?=$data['origin_name']?>" style="width:170px"></div>
	<select onchange="this.form.origin_name.value=this.value;this.form.origin_name.focus()">
	<? foreach ($r_originnm as $k=>$v){ ?><option value="<?=$k?>"><?=$v?><? } ?>
	</select>
	</td>
</tr>
<tr>
	<td>�귣��<font color="#FF1800"><b>*</b></font></td>
	<td>
	<input type="text" name="brandnm" value="<?=$data['brandnm']?>" >
	<select onchange="this.form.brandnm.value=this.value;this.form.brandnm.focus()">
	<? foreach ($r_brandnm as $k=>$v){ ?><option value="<?=$k?>"><?=$v?><? } ?>
	</select>
	</td>
</tr>
<tr>
	<td>�̿���</td>
	<td class="noline">
	<input type="radio" name="age_flag" value="N" <?=$checked['age_flag']['N']?>> �̼����� ���Ű���
	<input type="radio" name="age_flag" value="Y" <?=$checked['age_flag']['Y']?>> �̼����� ���ԺҰ�
	</td>
</tr>
</table>

<!-- ����/��� -->
<div style="height:30px"></div>
<div class="title2">&nbsp;<img src="../img/icon_process.gif" align="absmiddle"><font class="def1" color="#0074BA"><b> �� ����/���</b></font> <font class="small1" color="#6d6d6d">(����, ��� Ȯ���Ͻð�, ���¸��Ͽ� ����ϱ� ���� ������ �ʿ��� �κ��� �����ϼ���.)</font></div>
<table class="tb">
<col class="cellC"><col class="cellL"><col class="cellC"><col class="cellL">
<tr>
	<td width="120" nowrap>�ǸŰ�<font color="#FF1800"><b>*</b></font></td>
	<td width="50%"><input type="text" name="price" size="10" value="<?=$data['price']?>" required label="�ǸŰ�">��</td>
	<td width="120" nowrap>���<font color="#FF1800"><b>*</b></font></td>
	<td width="50%"><input type="text" name="stock" size="10" value="<?=$stock?>" onchange="autoPrice(this)" onblur="autoPrice(this)" onkeydown="autoPrice(this)">��</td>
</tr>
<tr>
	<td>����<font color="#FF1800"><b>*</b></font></td>
	<td><input type="text" name="consumer" size="10" value="<?=$data['consumer']?>" required label="����">��</td>
	<td>�ִ뱸�� ������</td>
	<td><input type="text" name="max_count" size="10" value="<?=$data['max_count']?>">�� <font class="small1" color="#6d6d6d">������ ������ 0�� �Է��ϼ���.</font></td>
</tr>
<tr>
	<td>�������</td>
	<td class=noline><input type=checkbox name=usestock <?=$checked[usestock][o]?>> �ֹ��� ������� <font class=small color=444444>(üũ���ϸ� ��� ������� �������Ǹ�)</font></td>
	<td>ǰ����ǰ</td>
	<td class=noline><input type=checkbox name=runout value=1 <?=$checked[runout][1]?>> ǰ���� ��ǰ�Դϴ�</td>
</tr>
<tr>
	<td>����/�����<font color="#FF1800"><b>*</b></font></td>
	<td class="noline">
	<input type="radio" name="tax" value="1" <?=$checked['tax'][1]?> required label="����/�����"> ����
	<input type="radio" name="tax" value="0" <?=$checked['tax'][0]?> required label="����/�����"> �����
	</td>
	<td></td>
	<td></td>
</tr>
</table>

<div style="margin:10px 0">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:vOption()" onfocus="blur()"><img src="../img/btn_priceopt_add.gif" align="absmiddle"></a> <font class="small" color="#444444">�̻�ǰ�� �ɼ��� �������ΰ�� ����ϼ���</font></div>

<div id="objOption" style="display:none; margin-left:20px;">
<div style="padding-bottom:10px">
<font class="small" color="black"><b>�ɼǸ�1</b> : <input type="text" name="optnm[]" value="<?=$optnm[0]?>">
<a href="javascript:addopt1()" onfocus="blur()"><img src="../img/i_add.gif" align="absmiddle"></a> <a href="javascript:delopt1()" onfocus="blur()"><img src="../img/i_del.gif" align="absmiddle"></a><span style="width:20px"></span>
<b>�ɼǸ�2</b></font> : <input type="text" name="optnm[]" value="<?=$optnm[1]?>">
<a href="javascript:addopt2()" onfocus="blur()"><img src="../img/i_add.gif" align="absmiddle"></a> <a href="javascript:delopt2()" onfocus="blur()"><img src="../img/i_del.gif" align="absmiddle"></a><span style="width:20px"></span>
</div>
<?if(count($opt)>1 || $opt1[0] != null || $opt2[0] != null){?><script>vOption();</script><?}?>
<div style="margin:10px 0"><font class="small" color="#444444">����� �ɼǸ�1�� �ɼǸ�2�� ����Ŭ���Ͻÿ� �ɼ��� �����Ͻ� �� �ֽ��ϴ�.<br>
�ɼǸ�1�� �ִ� 9�� �̳�, �ɼǸ�2�� �ִ� 30�������� �Է��Ͻ� �� �ֽ��ϴ�. ������ �ʰ��� �����ʹ� �ݿ����� ���� �� �ֽ��ϴ�.</font></div>
<table id="tbOption" border="1" bordercolor="#cccccc" style="border-collapse:collapse">
<tr align="center">
	<td width="116"></td>
	<?
		$j=4;
		foreach ($opt2 as $v){
		$j++;
	?>
	<td id="tdid_<?=$j?>"><input type="text" name="opt2[]" <?if($v != ''){?>class="fldtitle" value="<?=$v?>"<?}else{?>class="opt gray" value="�ɼǸ�2"<?}?> <?if($j>5){?> ondblclick="delopt2part('tdid_<?=$j?>')"<?}?> onclick="chkOptName(this)" onblur="chkOptNameOver(this)"></td>
	<? } ?>
</tr>
	<?
	$i=0;
	$op2=$opt2[0]; foreach ($opt1 as $op1){
	$i++;
	?>
<tr id="trid_<?=$i?>">
	<td width="116" nowrap><input type="text" name="opt1[]" <?if($op1 != ''){?>class="fldtitle" value="<?=$op1?>"<?}else{?>class="opt gray" value="�ɼǸ�1"<?}?> <?if($i != 1){?>ondblclick="delopt1part('trid_<?=$i?>')"<?}?> onclick="chkOptName(this)" onblur="chkOptNameOver(this)"></td>
	<? foreach ($opt2 as $op2){ ?>
	<td><input type="text" name="option[stock][]" <?if($opt[$op1][$op2]['stock']){?>class="opt" value="<?=$opt[$op1][$op2]['stock']?>"<?}else{?>class="opt gray" value="���"<?}?> onclick="chkOptName(this)" onblur="chkOptNameOver(this)"></td>
	<? } ?>
</tr>
<? } ?>
</table>
</div>

<!-- ��ǰ �̹��� -->
<div style="height:30px"></div>
<div class="title2">&nbsp;<img src="../img/icon_process.gif" align="absmiddle"><font class="def1" color="#0074BA"><b> �� ��ǰ �̹���</b></font> <font class="small1" color="#6d6d6d">(���¸��Ͽ� ������ �̹����� Ȯ���Ͻð�, ���¸��Ͽ� ����ϱ� ���� ������ �ʿ��� �κ��� �����ϼ���.)</font></div>
<table class="tb">
<col class="cellC"><col class="cellL"><col class="cellC"><col class="cellL">
<? $t = array_map("toThumb",$img_m); ?>
<tr>
	<td>���̹���</td>
	<td>

	<table>
	<col valign="top" span="2">
	<? for ($i=0;$i<4;$i++){ ?>
	<tr>
		<td>
		<span><input type="file" name="img_m[]" style="width:300px"></span>
		</td>
		<td>
		<?=goodsimg($t[$i],20,"style='border:1px solid #cccccc' onclick=popupImg('../data/goods/$img_m[$i]','../') class=hand",2)?>
		</td>
		<td>
		<? if ($img_m[$i]){ ?>
		<div style="padding:0" class="noline"><input type="checkbox" name="del[img_m][<?=$i?>]"><font class="small" color="#585858">���� (<?=$img_m[$i]?>)</font></div>
		<? } ?>
		</td>
	</tr>
	<? } ?>
	</table>

	</td>
</tr>
</table>

<!-- ��ǰ ���� -->
<div style="height:30px"></div>
<div class="title2">&nbsp;<img src="../img/icon_process.gif" align="absmiddle"><font class="def1" color="#0074BA"><b> �� ��ǰ ����</b></font> <font class="small1" color="#6d6d6d">(ȫ������ �� ��ǰ������ Ȯ���Ͻð�, ���¸��Ͽ� ����ϱ� ���� ������ �ʿ��� �κ��� �����ϼ���.)</font></div>

<table border="1" bordercolor="#cccccc" style="border-collapse:collapse">
<tr><td>
<table cellpadding="0" cellspacing="0" bgcolor="#f8f8f8">
<tr><td style="padding:10px 10px 5px 10px"><font class="small1" color="#444444"><font color="#E6008D">�̹��� �ܺθ�ũ</font> �� <font color="#E6008D">���¸���</font> �ǸŸ� ���� �̹����� ����Ͻ÷��� <font color="#E6008D">�ݵ�� �̹���ȣ���� ����</font>�� �̿��ϼž� �մϴ�.</a></td></tr>
<tr><td style="padding:0 10px 7px 10px"><font class="small1" color="#444444">�̹���ȣ������ ��û�ϼ̴ٸ� <a href="javascript:popup('http://image.godo.co.kr/login/imghost_login.php',980,700)" name="navi"><img src="../img/btn_imghost_admin.gif" align="absmiddle"></a>, ���� ��û���ϼ̴ٸ� <a href="http://hosting.godo.co.kr/imghosting/service_info.php" target="_blank"><img src="../img/btn_imghost_infoview.gif" align="absmiddle"></a> �� �����ϼ���!</td></tr>
</table>
</td></tr></table>

<div style="padding-top:5px"></div>

<table class="tb">
<col class="cellC"><col class="cellL"><col class="cellC"><col class="cellL">
<tr>
	<td>ȫ������</td>
	<td>
	<input name="shortdesc" style="width:400px;" class="line" maxlength="25" value="<?=htmlspecialchars($data['shortdesc'])?>" onkeydown="chkLen(this, 25, 'sLength')" onkeyup="chkLen(this, 25, 'sLength')">
	(<span id="sLength">0</span>/25)
	<div class="small1" style="color:#6d6d6d; padding-top:5px;">(ȫ���� ���� �߰������� * ǥ�ÿ� �Բ� ��ǰ�� �ϴܿ� ����Ǹ�,�˻���δ� ������� �ʽ��ϴ�. ��/���� 25�� �̳��� �Է��ϼž� �մϴ�.)</div>
	<script>_ID('sLength').innerHTML = document.getElementsByName('shortdesc')[0].value.length;</script>
	</td>
</tr>
</table>
<div style="height:6px;font-size:0"></div>

<textarea name="longdesc" style="width:100%;height:400px" type="editor"><?=$data['longdesc']?></textarea>

<!-- ��ۤ�A/S -->
<div style="height:30px"></div>
<div class="title2">&nbsp;<img src="../img/icon_process.gif" align="absmiddle"><font class="def1" color="#0074BA"><b> �� ��ۤ�A/S</b></font> <font class="small1" color="#6d6d6d">(��� �� A/S�� ��ǰ���� ������ �� �ֽ��ϴ�.)</font></div>

<div style="border:solid 1px #EBEBEB; background-color:#F6F6F6; padding:5px;"><input type="checkbox" name="noSameShipAS" value="o" class="null" <?=$checked['noSameShipAS']['o']?> onclick="setDisabled()">���������� ������� �ʰ� ������ �����մϴ�.</div>

<table class="tb" id="shipAS">
<col class="cellC"><col class="cellL">
<tr>
	<td>��ۺ� ����</td>
	<td>
	<table cellpadding="0" cellspacing="0">
	<col width="120">
	<tr height="25">
		<td><input type="radio" name="ship_type" value="3" class="null" <?=$checked['ship_type'][3]?> onclick="setShipDisabled();" disabled> ����</td>
		<td></td>
	</tr>
	<tr height="25">
		<td><input type="radio" name="ship_type" value="0" class="null" <?=$checked['ship_type'][0]?> onclick="setShipDisabled();" disabled> ����</td>
		<td><input type="text" name="ship_price" value="<?=$data['ship_price_0']?>" size=8 class=right onkeydown="onlynumber()" disabled> �� ��ۺ� �ΰ�</td>
	</tr>
	<tr height="25">
		<td><input type="radio" name="ship_type" value="5" class="null" <?=$checked['ship_type'][5]?> onclick="setShipDisabled();" disabled> �������Ǻ� ����</td>
		<td>
		�� ���ž��� <input type="text" name="ship_base" value="<?=$data['ship_base_5']?>" size=9 class=right onkeydown="onlynumber()" disabled> �� �̻��� �� ��ۺ� ����, �̸��� �� <input type="text" name="ship_price" value="<?=$data['ship_price_5']?>" size=8 class=right onkeydown="onlynumber()" disabled> �� ��ۺ� �ΰ�
		</td>
	</tr>
	<tr height="25">
		<td><input type="radio" name="ship_type" value="4" class="null" <?=$checked['ship_type'][4]?> onclick="setShipDisabled();" disabled> �������Ǻ� ����</td>
		<td>
		�� ���ŷ��� <input type="text" name="ship_base" value="<?=$data['ship_base_4']?>" size=9 class=right onkeydown="onlynumber()" disabled> �� �̻��� �� ��ۺ� ����, �̸��� �� <input type="text" name="ship_price" value="<?=$data['ship_price_4']?>" size=8 class=right onkeydown="onlynumber()" disabled> �� ��ۺ� �ΰ�
		</td>
	</tr>
	</table>
	</td>
</tr>
<tr>
	<td>��ۺ� ������</td>
	<td>
		<input type="radio" name="ship_pay" value="Y" class="null" <?=$checked['ship_pay']['Y']?> disabled> ������
		<input type="radio" name="ship_pay" value="N" class="null" <?=$checked['ship_pay']['N']?> disabled> ����
	</td>
<tr>
	<td>A/S ����<br>(�ȳ�����)</td>
	<td>
	<input name="as_info" style="width:500px;" class="line" maxlength="40" value="<?=htmlspecialchars($data['as_info'])?>" onkeydown="chkLen(this, 40, 'vLength')" onkeyup="chkLen(this, 40, 'vLength')">
	(<span id="vLength">0</span>/40)
	<div class="small1" style="color:#6d6d6d; padding-top:5px;">(A/S ����ó,�Ⱓ ���� �Է��ϼ���. ��/���� 40�� �̳��� �Է��ϼž� �մϴ�.)</div>
	<script>_ID('vLength').innerHTML = document.getElementsByName('as_info')[0].value.length;</script>
	</td>
</tr>
</table>


<div class="button">
<input type="image" src="../img/btn_openmarket_register_s.gif" alt="���¸����ǸŰ����� ��ǰ����">
</div>
</form>

<!-- �������� Ȱ��ȭ ��ũ��Ʈ -->
<script src="../../lib/meditor/mini_editor.js"></script>
<script>mini_editor("../../lib/meditor/");</script>
<SCRIPT LANGUAGE="JavaScript" SRC="../proc/warning_disk_js.php"><!-- not_delete --></SCRIPT>
<script>table_design_load();</script>
<script>setDisabled();</script>

<div style="padding-top:15px"></div>
</body>
</html>