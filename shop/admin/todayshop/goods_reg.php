<?

$location = "�����̼� > ��ǰ���";
include "../_header.php";

$todayShop = &load_class('todayshop', 'todayshop');
if (!$todayShop->auth()) {
	msg(' ���� ��û�ȳ��� ���� �����ͷ� �������ֽñ� �ٶ��ϴ�.', -1);
}

# ��ϼ� ���� üũ
list ($cntGoods) = $db->fetch("select count(*) from ".GD_GOODS, 1);
if ($godo[maxGoods]!="unlimited" && $godo[maxGoods]<=$cntGoods){
	echo "
	<div style='border:5 solid #B8B8DC;padding:8px;background:#f7f7f7'><b>�� ��ǰ�� ����� ���ѵ� �����Դϴ�</b></div><p>
	";
}

$returnUrl = ($_GET['returnUrl']) ? $_GET['returnUrl'] : $_SERVER['HTTP_REFERER'];
$btn_list = "<a href='".$returnUrl."'><img src='../img/btn_list.gif'></a>";

if (!in_array($_GET['mode'], array('register', 'modify', 'copy'))) $_GET['mode'] = 'register';

$r_maker[''] = $r_origin[''] = "-- ��Ϻ��� --";
$str_img	= array(
			"m"	=> "�����̹���",
			"i"	=> "����Ʈ�̹���",
			"s"	=> "������̹���",
			);

### ������
$query = 'SELECT DISTINCT maker FROM '.GD_GOODS."";
$res = $db->query($query);
while ($data=$db->fetch($res)) if ($data['maker']) $r_maker[$data['maker']] = $data['maker'];

### ������
$query = "SELECT DISTINCT origin FROM ".GD_GOODS."";
$res = $db->query($query);
while ($data=$db->fetch($res, 1)) if ($data['origin']) $r_origin[$data['origin']] = $data['origin'];

// ��ǰ��Ͻ� ���� ������
$formmode = $_GET['mode'];

switch($_GET['mode']) {
	case 'modify': {
		$tgsno = $_GET['tgsno'];

		### ��Ƽī�װ�
		$query = "select category from ".GD_TODAYSHOP_LINK." where tgsno='$tgsno' order by category";
		$res = $db->query($query);
		while ($data=$db->fetch($res)) $r_category[$data[category]] = $data[sort];

		### ��ǰ ���� ��������
		$data = $db->fetch("SELECT *, IF ((tg.enddt IS NOT NULL AND tg.enddt < now()) OR g.runout=1, 'y', 'n') AS tgout FROM ".GD_TODAYSHOP_GOODS." AS tg JOIN ".GD_GOODS." AS g ON tg.goodsno=g.goodsno WHERE tg.tgsno='".$tgsno."'",1);
		if (empty($data)===false) {
			$data = array_map("slashes",$data);
			$data['launchdt'] = str_replace(array('-','00000000'),'',$data['launchdt']);
			$ex_title = explode("|",$data['ex_title']);
			$startdt = explode(" ", $data['startdt']);
			$enddt = explode(" ", $data['enddt']);
			$starttime = explode(":", $startdt[1]);
			$endtime = explode(":", $enddt[1]);
			$data['start']['dt'] = str_replace('-', '', $startdt[0]);
			$data['end']['dt'] = str_replace('-', '', $enddt[0]);
			$data['start']['hour'] = $starttime[0];
			$data['start']['min'] = $starttime[1];
			$data['end']['hour'] = $endtime[0];
			$data['end']['min'] = $endtime[1];
			$data['usestartdt'] = str_replace('-', '', $data['usestartdt']);
			$data['useenddt'] = str_replace('-', '', $data['useenddt']);

			$goodsno = $data['goodsno'];

			if ($data['tgout'] == 'y') {
				$formmode = 'aftersale';
			}
			break;
		}
		else msg("�߸��� ��ǰ��ȣ�Դϴ�.");
	}
	case 'register': {
		$data['usestock'] = 'o';
		$data['visible'] = 1;
		$data['tax'] = 1;
		$data['opttype'] = 'single';
		$data['showtimer'] = 'n';
		$data['showpercent'] = 'y';
		$data['showstock'] = 'n';
		$hidden['sort'] = "style='display:none'";
		$data['sms'] = '['.$cfg['shopName'].'] http://'.$_SERVER['HTTP_HOST'];
		break;
	}
	case 'copy': {
		$goodsno = $_GET['goodsno'];

		### ��ǰ ���� ��������
		$data = $db->fetch("select * from ".GD_GOODS." where goodsno='".$goodsno."'",1);
		if (empty($data)===false) $data = array_map("slashes",$data);
		$data['launchdt'] = str_replace(array('-','00000000'),'',$data['launchdt']);
		$ex_title = explode("|",$data[ex_title]);
		$_GET['mode'] = $formmode = 'register';
		$data['sms'] = '['.$cfg['shopName'].'] http://'.$_SERVER['HTTP_HOST'];
		$data['showpercent'] = 'y';
		$data['showstock'] = 'n';
		$data['showtimer'] = 'n';
		break;
	}
}

if($data['goods_deli_type'] == '����' || !$data['goods_deli_type']) $goods_deli_type = 0;
if(!$data['use_emoney']) $data['use_emoney'] = 0;
if(!$data['delivery_type']) $data['delivery_type'] = 0;
else $goods_deli_type = 1;
if (!$data['usememberdc']) $data['usememberdc'] = 'n';
//if ($data['usestock'] && $data['totstock'] == 0) $data['runout'] = 1;
if (!$data['usestock']) $disabled['showstock']['y'] = 'disabled="disabled"'; // ��� �����϶��� ��� ���Ⱑ��.
if (!$data['goodstype']) $data['goodstype'] = 'goods';
if (!$data['limit_ea']) $data['limit_ea'] = 0;
if (!$data['processtype']) $data['processtype'] = 'i';

$checked['visible'][$data['visible']] = "checked";
$checked['tax'][$data['tax']] = "checked";
$checked['usestock'][$data['usestock']] = "checked";
$checked['opttype'][$data['opttype']] = "checked";
$checked['delivery_type'][$data['delivery_type']] = "checked";
$checked['meta_title'][$data['meta_title']] = "checked";
$checked['usedelivery'][$data['usedelivery']] = "checked";
$checked['use_emoney'][$data['use_emoney']] = "checked";
$checked['opttype'][$data['opttype']] = "checked";
$checked['showtimer'][$data['showtimer']] = "checked";
$checked['showpercent'][$data['showpercent']] = "checked";
$checked['showbuyercnt'][$data['showbuyercnt']] = "checked";
$checked['showstock'][$data['showstock']] = "checked";
$checked['usememberdc'][$data['usememberdc']] = "checked";
$checked['goodstype'][$data['goodstype']] = "checked";
$checked['processtype'][$data['processtype']] = "checked";
$checked['runout'][$data['runout']] = "checked";
$checked['fakestock2real'][$data['fakestock2real']] = "checked";

$selected['goods_deli_type'][$goods_deli_type] = "selected";
$selected['company'][$data['company']] = "selected";

$useEx = ($data[ex_title]) ? 1 : 0;
$checked[useEx][$useEx] = "checked";
$display[useEx] = ($useEx) ? "block" : "none";

$img_i = explode("|",$data['img_i']);
$img_s = explode("|",$data['img_s']);
$img_m = explode("|",$data['img_m']);

$imgs	= $urls = array(
		'm'	=> $img_m,
		'i'	=> $img_i,
		's'	=> $img_s,
		);

// �̹��� �ּҰ� url�϶� ó��
$checked[image_attach_method][file] = $checked[image_attach_method][url] = 'checked';

if (preg_match('/^http(s)?:\/\//',$img_m[0])) {
	$checked[image_attach_method][file] = '';
	$imgs	= array(
			'm'	=> array(''),
			's'	=> array(''),
			'i'	=> array(''),
			);
}
else {
	$urls	= array(
			'm'	=> array(''),
			's'	=> array(''),
			'i'	=> array(''),
			);
	$checked[image_attach_method][url] = '';
}

### �ʼ��ɼ�
$optnm = explode("|", $data['optnm']);
if ($data['goodsno']) {
	$query = "SELECT * FROM ".GD_GOODS_OPTION." WHERE goodsno='".$data['goodsno']."' and go_is_deleted <> '1'";
	$res = $db->query($query);
	while ($tmp=$db->fetch($res, 1)){
		$tmp = array_map("htmlspecialchars",$tmp);
		$opt1[] = $tmp['opt1'];
		$opt2[] = $tmp['opt2'];
		$opt[$tmp['opt1']][$tmp['opt2']] = $tmp;

		### ����� ���
		$stock += $tmp['stock'];
	}
}
if ($opt1) $opt1 = array_unique($opt1);
if ($opt2) $opt2 = array_unique($opt2);
if (!$opt){
	$opt1 = array('');
	$opt2 = array('');
}

### �⺻ ���� �Ҵ�
$consumer	  = $opt[$opt1[0]][$opt2[0]]['consumer'];
$price = $opt[$opt1[0]][$opt2[0]]['price'];
$reserve  = $opt[$opt1[0]][$opt2[0]]['reserve'];

### �߰��ɼ�
$r_addoptnm = explode("|",$data[addoptnm]);
for ($i=0;$i<count($r_addoptnm);$i++){
	list ($addoptnm[],$addoptreq) = explode("^",$r_addoptnm[$i]);
	if ($addoptreq) $checked[addoptreq][$i] = "checked";
}

$query = "select * from ".GD_GOODS_ADD." where goodsno='$goodsno' order by sno";
$res = $db->query($query);
while ($tmp=$db->fetch($res)){
	$addopt[$tmp[step]][] = $tmp;
}
unset($res);

$useAdd = ($addopt) ? 1 : 0;
$checked[useAdd][$useAdd] = "checked";
$display[useAdd] = ($useAdd) ? "block" : "none";

if (!$addopt) $addopt = array(array(''));

// SMS Ư������
$spChr = array('��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��');

// ���޾�ü ��������
$res = $db->query("SELECT cp_sno, cp_name FROM ".GD_TODAYSHOP_COMPANY);
while($tmpData = $db->fetch($res, 1)) $cpData[] = array('cp_sno'=>$tmpData['cp_sno'], 'cp_name'=>$tmpData['cp_name']);
unset($res);

// SMS ����Ʈ ��������
$sms = &load_class('sms', 'sms');
$smsPt = preg_replace('/[^0-9-]*/', '', $sms->smsPt);
unset($sms);
?>
<script>
function applydopt(){
	var obj = document.getElementById('dopt');
	var k = obj.selectedIndex;
	if(obj[k].value) {
		ifrmHidden.location.href="../goods/popup.dopt_register.php?mode=dopt_apply&sno="+obj[k].value;
	}
}

/* �ɼ� �κ� ���� */
function delopt1part(rid)
{
	var obj = document.getElementById(rid);
	var tbOption = document.getElementById('tbOption');
	if (tbOption.rows.length>2) tbOption.deleteRow(obj.rowIndex);
}
function delopt2part(cid)
{
	var delCellIndex = document.getElementById(cid).cellIndex;
	var tbOption = document.getElementById('tbOption');

	if (tbOption.rows[0].cells.length<6) return;
	for (i=0;i<tbOption.rows.length;i++){
		tbOption.rows[i].deleteCell(delCellIndex);
	}
}

/*** ��üũ ***/
function chkForm2(obj)
{
	<? if ($formmode == 'aftersale') { ?>
		if (!confirm("�ǸŰ� �Ϸ�� ��ǰ�� ���Ŵ޼��ο��� �Ǹż��� ���⼳���� ������ �����մϴ�.\n�����Ͻðڽ��ϱ�?\n(��� �ִ� ��ǰ�� ǰ�����ε� ���������մϴ�.)")) return false;
	<? } else { ?>
	if (obj.start_dt.value && !obj.start_hour.value) obj.start_hour.value = '00';
	if (obj.start_dt.value && !obj.start_min.value) obj.start_min.value = '00';
	if (obj.end_dt.value && !obj.end_hour.value) obj.end_hour.value = '23';
	if (obj.end_dt.value && !obj.end_min.value) obj.end_min.value = '59';

	if (obj.processtype[1].checked && (!obj.end_dt.value || !obj.end_hour.value || !obj.end_min.value)) {
		alert("�ϰ��߼�/��� ��ǰ�� ���� ����Ⱓ�� �ʿ��մϴ�.");
		return false;
	}
	<? } ?>

	if(!chkTitle()){
		alert('�׸���� �ߺ��� �� �����ϴ�.');
		return false;
	}

	if (!chkOption()) return false;
	if (!chkForm(obj)) return false;
	return true;
}

/*** ��ǰ ī�װ� ���� ***/
var idxCategory;
var preCurrposSel;

function cate_mod(obj,el)
{
	el.style.background = "#EFF5F9";
	idx = el.rowIndex;
	var objX = document.getElementsByName('category[]');
	var val = objX[idx].value;
	idxCategory = idx;
	if (preCurrposSel && preCurrposSel!=el) preCurrposSel.style.background = "#FFFFFF";
	preCurrposSel = el;
	categoryBox_request(obj,val);
}
function cate_del(el)
{
	idx = el.rowIndex;
	var obj = document.getElementById('objCategory');
	obj.deleteRow(idx);
}
function exec_mod()
{
	if (typeof(idxCategory)=="undefined"){
		alert('������ ī�װ��� �������ּ���');
		return;
	}
	var ret;
	var str = new Array();
	var obj = document.forms[0]['cate[]'];
	for (i=0;i<obj.length;i++){
		if (obj[i].value){
			str[str.length] = obj[i][obj[i].selectedIndex].text;
			ret = obj[i].value;
		}
	}
	if (!ret) return;
	obj = document.getElementsByName('category[]');
	if (obj[idxCategory]) obj[idxCategory].value = ret;
	obj = document.all.currPosition;
	if (obj){
		if (!(obj.length>0)) obj = new Array(obj);
		obj[idxCategory].innerHTML = str.join(" > ");
	}
}
function exec_add()
{
	var ret;
	var str = new Array();
	var obj = document.forms[0]['cate[]'];
	for (i=0;i<obj.length;i++){
		if (obj[i].value){
			str[str.length] = obj[i][obj[i].selectedIndex].text;
			ret = obj[i].value;
		}
	}
	if (!ret){
		alert('ī�װ��� �������ּ���');
		return;
	}
	var linkobj = document.getElementsByName("category[]");
	for(var i = 0; i < linkobj.length; i++) {
		if (linkobj[i].value == ret) return;
	}
	var obj = document.getElementById('objCategory');
	oTr = obj.insertRow();
	oTd = oTr.insertCell();
	oTd.id = "currPosition";
	oTd.innerHTML = str.join(" > ");
	oTd = oTr.insertCell();
	oTd.innerHTML = "<input type=text name=category[] value='" + ret + "' style='display:none'>";
	oTd = oTr.insertCell();
	oTd.innerHTML = "<!--<img src='../img/i_select.gif' onClick=\"cate_mod(document.forms[0]['cate[]'][0],this.parentNode.parentNode)\" class=hand>--> <a href='javascript:void(0)' onClick='cate_del(this.parentNode.parentNode)'><img src='../img/i_del.gif' align=absmiddle></a>";
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
			case 0: oTd.innerHTML = "<input type=text class='opt gray' name=opt1[] value='�ɼǸ�1' required label='1���ɼǸ�' ondblclick=\"delopt1part('"+oTr.id+"')\" onclick='chkOptName(this)' onblur=\"chkOptNameOver(this)\">";
			break;
			case 1:	oTd.innerHTML = "<input type=text name=option[consumer][] class='opt gray' value='" + fm.consumer.value + "'>"; break;
			case 2:	oTd.innerHTML = "<input type=text name=option[price][] class='opt gray' value='" + fm.price.value + "'>"; break;
			case 3:	oTd.innerHTML = "<input type=text name=option[reserve][] class='opt gray' value='" + fm.reserve.value + "'>"; break;
			default: oTd.innerHTML = "<input type=text name=option[stock][] class='opt gray' value='���' onclick='chkOptName(this)' onblur=\"chkOptNameOver(this)\"><input type=hidden name=option[optno][]>"; break;
		}
	}
}
function addopt2(bNotChcked)
{
	var name;
	var tbOption = document.getElementById('tbOption');
	if (tbOption.rows.length<3 && !bNotChcked){
		alert('1���ɼ��� ���� �߰����ּ���');
		return;
	}

	var Ccnt = tbOption.rows[0].cells.length;

	for (i=0;i<tbOption.rows.length;i++){
		oTd = tbOption.rows[i].insertCell();
		if(!i)oTd.id = "tdid_"+Ccnt;
		oTd.innerHTML = (i) ? "<input type=text name=option[stock][] class='opt gray'  value='���' onclick='chkOptName(this)' onblur=\"chkOptNameOver(this)\"><input type=hidden name=option[optno][]>" : "<input type=text class='opt gray' name=opt2[] value='�ɼǸ�2' required label='2���ɼǸ�' ondblclick=\"delopt2part('"+oTd.id+"')\" onclick='chkOptName(this)' onblur=\"chkOptNameOver(this)\">";
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
	if (tbOption.rows[0].cells.length<5) return;
	for (i=0;i<tbOption.rows.length;i++){
		tbOption.rows[i].deleteCell();
	}
}

/*** �߰��ɼ� ***/
function add_addopt()
{
	var tbAdd = document.getElementById('tbAdd');
	oTr = tbAdd.insertRow();
	oTd = oTr.insertCell();
	oTd.innerHTML = "<input type=text name=addoptnm[]> <a href='javascript:void(0)' onClick='add_subadd(this)'><img src='../img/i_proadd.gif' align=absmiddle></a>";
	oTd = oTr.insertCell();
	oTd.colSpan = 2;
	oTd.innerHTML = "\
	<table>\
	<tr>\
		<td><input type=text name=addopt[opt][" + (oTr.rowIndex-1) + "][] style='width:270px'> ���ý�</td>\
		<td>�Ǹűݾ׿� <input type=text name=addopt[addprice][" + (oTr.rowIndex-1) + "][] size=9> �� �߰�</td>\
	</tr>\
	</table>\
	";
	oTd = oTr.insertCell();
	oTd.className = "noline";
	oTd.innerHTML = "<input type=checkbox name=addoptreq[" + (oTr.rowIndex-1) + "]>";
}
function del_addopt()
{
	var tbOption = document.getElementById('tbAdd');
	if (tbOption.rows.length>2) tbOption.deleteRow();
}
function add_subadd(obj)
{
	var idx = obj.parentNode.parentNode.rowIndex - 1;
	obj = obj.parentNode.parentNode.childNodes(1).getElementsByTagName('table')[0];
	oTr = obj.insertRow();
	oTd = oTr.insertCell();
	oTd.innerHTML = "<input type=text name=addopt[opt][" + idx + "][] style='width:270px'> ���ý�";
	oTd = oTr.insertCell();
	oTd.innerHTML = "�Ǹűݾ׿� <input type=text name=addopt[addprice][" + idx + "][] size=9> �� �߰�";
}

/*** ��ǰ �̹��� ***/
function preview(obj)
{
	var tmp = obj.parentNode.parentNode.parentNode.childNodes(2);
	tmp.innerHTML = "<img src='" + obj.value + "' width=20 onload='if(this.height>this.width){this.height=20}' style='border:1 solid #cccccc' onclick=popupImg(this.src,'../') class=hand>";
}
function addfld(obj)
{
	var tb = document.getElementById(obj);
	oTr = tb.insertRow();
	oTd = oTr.insertCell();
	oTd.innerHTML = "<a href='javascript:void(0)' onClick='delfld(this)'><img src='../img/i_del.gif' align=absmiddle></a>	<span>" + tb.rows[0].cells[0].getElementsByTagName('span')[0].innerHTML + "</span>";
	oTd = oTr.insertCell();
	oTd = oTr.insertCell();
}
function delfld(obj)
{
	var tb = obj.parentNode.parentNode.parentNode.parentNode;
	tb.deleteRow(obj.parentNode.parentNode.rowIndex);
}

/*** �ڵ����� �����ʵ忡 �Է°� ���� ***/
function autoPrice(obj)
{
	var name = obj.name;
	var el = document.getElementsByName("option[" + name + "][]");
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
	if(obj.value == ''){
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

// �ڵ���������
function chkImgCopy(fobj)
{
	var exist = false;
	for(var i=0; i < document.getElementsByName('img_m[]').length; i++)
	{
		if(document.getElementsByName('img_m[]')[i].value != ''){
			exist = true;
			break;
		}
		else if(document.getElementsByName('del[img_m]['+i+']')[0] != null && document.getElementsByName('del[img_m]['+i+']')[0].checked == false){
			exist = true;
			break;
		}
	}

	if(exist == false){
		alert('�����̹��� ���� ����ϼ���.');
		return false;
	}

	for(var i=0; i < document.getElementsByName('img_m[]').length; i++)
		document.getElementsByName('img_m[]')[i].disabled = fobj.copy_m.checked;
	for(var i=0; i < document.getElementsByName('img_s[]').length; i++)
		document.getElementsByName('img_s[]')[i].disabled = fobj.copy_s.checked;
}
function chkImgBox(obj, fobj)
{
	fobj.copy_m.checked = obj.checked;
	fobj.copy_s.checked = obj.checked;
	var res = chkImgCopy(fobj);
	if (res === false){
		obj.checked = fobj.copy_m.checked = fobj.copy_s.checked = false;
	}
}
function chkTitle(){
	var obj = document.getElementsByName('title[]');
	for(var i=0;i<obj.length;i++){
		for(var j=0;j<obj.length;j++){
			if(i!=j && obj[i].value == obj[j].value && obj[i].value && obj[j].value ){
				return false;
			}
		}
	}
	return true;
}
function onlyhour() {
	onlynumber();
	var e = event.keyCode;
	var value = event.srcElement.value;
	if (e>=48 && e<=57) value += e - 48;
	if (e>=96 && e<=105) value += e - 96;
	if (value >= 0 && value <= 23) return;

	event.returnValue = false;
}
function onlyminute() {
	onlynumber();
	var e = event.keyCode;
	var value = event.srcElement.value;
	if (e>=48 && e<=57) value += e - 48;
	if (e>=96 && e<=105) value += e - 96;
	if (value >= 0 && value <= 59) return;

	event.returnValue = false;
}

//SMS
function insChr(str) {
	var fm = document.fm;
	fm.sms.value = fm.sms.value + str.replace(/\s/g, "");
	chkLength(fm.sms);
}

function chkLength(obj) {
	str = obj.value;
	document.getElementById('vLength').value = chkByte(str);
	if (chkByte(str)>80) {
		document.getElementById('vLength').style.color = "#FF0000";
//		chkLength(obj);
	}
	else {
		document.getElementById('vLength').style.color = "";
	}
}

//���º� �ʵ� ���̱�/�����
function showBlock(id, status) {
	document.getElementById(id).style.display = status;
}

function setGoodsType(t) {
	switch(t) {
		case 'goods': {
			showBlock('usedtBlock', 'none');
			showBlock('deliveryBlock', 'block');
			document.getElementsByName("delivery_type")[0].checked = "checked";
			break;
		}
		case 'coupon': {
			showBlock('usedtBlock', 'block');
			showBlock('deliveryBlock', 'none');
			document.getElementsByName("delivery_type")[1].checked = "checked";
			break;
		}
		case 'limit' :	// ���,�ϰ� ����
			var o = document.fm.limit_ea;
//			o.value = 0;
			o.disabled = false;
			var o2 = document.fm.fakestock;
//			o2.value = 0;
			o2.disabled = false;
			var o3 = document.fm.fakestock2real;
			o3.disabled = false;

			$$('input[name="showbuyercnt"]').each(function(el){
				el.writeAttribute({disabled: true,checked: (el.readAttribute('value') == 'y' ? true : false)});
			});
			break;

		case 'unlimit' :	// ���,�ϰ� ����
			var o = document.fm.limit_ea;
			o.value = 0;
			o.disabled = true;
			var o2 = document.fm.fakestock;
			o2.value = 0;
			o2.disabled = true;
			var o3 = document.fm.fakestock2real;
			o3.checked = false;
			o3.disabled = true;

			$$('input[name="showbuyercnt"]').each(function(el){
				el.writeAttribute({disabled: false,checked: (el.readAttribute('value') == 'n' ? true : false)});
			});
			break;
		case 'usestock' : {
			var obj = document.getElementsByName("showstock");
			if (arguments[1]) {
				if (obj[0]) obj[0].disabled = "";
				if (obj[1]) obj[1].disabled = "";
			}
			else {
				if (obj[0]) obj[0].disabled = "disabled";
				if (obj[1]) obj[1].checked = "checked";
			}
			break;
		}
		case 'showbuyercnt' :
			var o2 = document.fm.fakestock;
			o2.value = 0;
			o2.disabled = false;

			break;
		case 'hidebuyercnt' :
			var o2 = document.fm.fakestock;
			o2.value = 0;
			o2.disabled = true;
			break;
	}
	chk_delivery_type();
}

function fnChangeReserveType(v) {
	var f = document.fm;
	f.reserve.disabled = (v == 0) ? true : false;
}

// �ǸſϷ� �� ��ǰ������ ���� ������
function init_form() {
	fnChangeReserveType(<?=($checked[use_emoney][0] == 'checked') ? 0 : 1?>);
<? if ($formmode == 'aftersale') { ?>
	var f = document.fm;
	var objs = f['all'];

	for(var i = 0; i < objs.length; i++) {
		if (objs[i].tagName.match(/(INPUT|TEXTAREA|SELECT)/gi)) objs[i].disabled = "disabled";
		else {
			if (objs[i].onclick) objs[i].onclick = null;
			if (objs[i].href && objs[i].href.match(/^javascript:/gi)) objs[i].href = "javascript:";
		}
	}
	document.getElementById("btn_save").disabled = "";
	document.getElementsByName("mode")[0].disabled = "";
	document.getElementsByName("tgsno")[0].disabled = "";
	document.getElementsByName("returnUrl")[0].disabled = "";
	document.getElementsByName("limit_ea")[0].disabled = "";
	document.getElementsByName("fakestock")[0].disabled = "";
	document.getElementsByName("fakestock2real")[0].disabled = "";
	<? if ($data['totstock'] > 0) { ?>
	document.getElementsByName("runout")[0].disabled = "";
	<? } ?>
<? } ?>
}

	function fnSetImageAttachForm() {

	var m, obj = document.fm.image_attach_method;

	for (var i=0; i <obj.length; i++) {
		if (obj[i].checked)
		  var m = obj[i].value;
	}

	if (m == 'file') {
		document.getElementById('image_attach_method_upload_wrap').style.display = 'block';
		document.getElementById('image_attach_method_link_wrap').style.display = 'none';
	}
	else {
		document.getElementById('image_attach_method_upload_wrap').style.display = 'none';
		document.getElementById('image_attach_method_link_wrap').style.display = 'block';
	}

}
</script>
<script type="text/javascript" src="todayshop.js"></script>

<table width=800 cellpadding=0 cellspacing=0>
<tr><td align=center><div id=goods_form><? include "../proc/warning_disk_msg.php"; # not_delete  ?></td></tr></table>

<form name=fm method=post action="indb.goods_reg.php" enctype="multipart/form-data" onsubmit="return chkForm2(this)" target="ifrmHidden">
<input type=hidden name=mode value="<?=$formmode?>">
<input type=hidden name=tgsno value="<?=$tgsno?>">
<input type=hidden name=returnUrl value="<?=$returnUrl?>">

<? if ($tgsno) { ?>
<div style="padding:8px 13px;background:#f7f7f7;border:3px solid #C6C6C6;margin-bottom:18px;" id="goodsInfoBox">
	<div><font class=def>������ȣ:</font> <span style="color:#FF7200;font:bold 14px verdana"><?=$tgsno?></span></div>
</div>
<? } else { ?>
<div style="display:inline-block">
	<div style="float:right"><a onclick="popupLayer('./popup.goods_copy.php', 800, 600)" style="cursor:pointer"><img src="../img/btn_goods_copy.gif" /></a></div>
</div>
<? } ?>
<!-- ���� ī�װ� ���� -->
<div class="title title_top">��ǰ�з�(����)����<span>�ѻ�ǰ�� �������� �з��� ����� �� �ֽ��ϴ�&nbsp;(���ߺз��������)</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=todayshop&no=8')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<div class="box" style="padding-left:3">
<table width=790 cellpadding=0 cellspacing=1 border=1 bordercolor=#cccccc style="border-collapse:collapse">
<tr><td style="padding:7 7 7 10" bgcolor=f8f8f8>
<table width=100% cellpadding=0 cellspacing=1 id=objCategory>
<col><col width=50 style="padding-right:10"><col width=52 align=right>
<? if ($r_category){ foreach ($r_category as $k=>$v){ ?>
<tr>
	<td id=currPosition><?=strip_tags(currPositionTS($k))?></td>
	<td>
		<input type=text name=category[] value="<?=$k?>" style="display:none">
	</td>
	<td>
	<!--<img src="../img/i_select.gif" border=0 onClick="cate_mod(document.forms[0]['cate[]'][0],this.parentNode.parentNode)" class=hand>-->
	<a href="javascript:void(0)" onClick="cate_del(this.parentNode.parentNode)"><img src="../img/i_del.gif" border=0 align=absmiddle></a>
	</td>
</tr>
<? }} ?>
</table>
    </td>
</tr>
</table>
</div>

<div style="padding-top:10">
<table>
<tr>
	<td>
		<select name="cate[]" class="select" multiple="multiple" style='width:160px;height:96' onchange="category.change(this)">
			<option value="">= 1�� �з�=</option>
		</select>
		<select name="cate[]" class="select" multiple="multiple" style='width:160px;height:96' onchange="category.change(this)">
			<option value="">= 2�� �з�=</option>
		</select>
		<select name="cate[]" class="select" multiple="multiple" style='width:160px;height:96' onchange="category.change(this)">
			<option value="">= 3�� �з�=</option>
		</select>
		<select name="cate[]" class="select" multiple="multiple" style='width:160px;height:96'>
			<option value="">= 4�� �з�=</option>
		</select>
		<script type="text/javascript">
			var category = new Category("cate[]");
			category.select("<?=$category?>");
		</script>
	</td>
	<td valign=top>
    <table width=100% cellpadding=0 cellspacing=0 id=objCategory>
    <tr><td height=55 valign=top><a href="javascript:exec_add()"><img src="../img/i_regist_l.gif" vspace="4"></a><br>
    <!--<tr><td><a href="javascript:exec_mod()"><img src="../img/i_change.gif"></a></td></tr>-->
    </table>
	</td>
</tr>
</table>
</div>
<div class=noline style="padding-left:3;padding-bottom:10px"><a href="/shop/admin/todayshop/category.php" target=blank><font class=extext_l>[��ǰ�з�(����) ����ϱ�]</font></a></div>
<div style="border-bottom:3px #efefef solid;padding-top:10px"></div>

<!-- ��ǰ�⺻���� -->
<div class=title style="margin-top:0px">��ǰ�⺻����<span>*�� �ʼ� �Է� �����Դϴ�. <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=todayshop&no=8')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<table class=tb>
<col class=cellC><col class=cellL><col class=cellC><col class=cellL>
<tr>
	<td width=120 nowrap>��ǰ��*</td>
	<td width=50%>
		<div style="height:25;padding-top:5">
			<input type=text name=goodsnm style="width:100%" value="<?=$data['goodsnm']?>" required label="��ǰ��" class="line"></div><div style="height:23">
			<label><input type=checkbox name="meta_title" value="1" class=null <?=$checked['meta_title'][1]?>>��ǰ���� ��ǰ���������� Ÿ��Ʋ �±׿� �Էµ˴ϴ�.</label>
		</div>
	</td>
	<td width=120 nowrap>��ǰ�ڵ�</td>
	<td width=50%><input type=text name=goodscd style="width:100%" value="<?=$data['goodscd']?>" class="line"></td>
</tr>
<tr>
	<td>������</td>
	<td>
		<input type=text name=maker value="<?=$data['maker']?>" class="line">
		<select onchange="this.form.maker.value=this.value;this.form.maker.focus()">
		<? foreach ($r_maker as $k=>$v){ ?><option value="<?=$k?>"><?=$v?><? } ?>
		</select>
	</td>
	<td>������</td>
	<td>
		<input type=text name=origin value="<?=$data['origin']?>" class="line">
		<select onchange="this.form.origin.value=this.value;this.form.origin.focus()">
		<? foreach ($r_origin as $k=>$v){ ?><option value="<?=$k?>"><?=$v?><? } ?>
		</select>
	</td>
</tr>
<tr>
	<td>���޾�ü</td>
	<td>
		<select name="company">
		<option value="">= ���޾�ü ���� =</option>
		<? for ($i = 0; $i < count($cpData); $i++){ ?>
		<option value="<?=$cpData[$i]['cp_sno']?>" <?=$selected['company'][$cpData[$i]['cp_sno']]?>><?=$cpData[$i]['cp_name']?></option>
		<? } ?>
		</select>
	</td>
	<td>��ǰ����</td>
	<td>
		<label class="noline"><input type="radio" name="goodstype" value="goods" <?=$checked['goodstype']['goods']?> onclick="setGoodsType('goods')" />�ǹ�</label>
		<label class="noline"><input type="radio" name="goodstype" value="coupon" <?=$checked['goodstype']['coupon']?> onclick="setGoodsType('coupon')" />����</label>
		<div id="usedtBlock" <?if ($data['goodstype']=='goods') {?>style="display:none;"<?}?>>
			(��ȿ�Ⱓ :
			<input type=text name="usestartdt" value="<?=$data['usestartdt']?>" onclick="calendar(event)" onkeydown="onlynumber()" class="cline" size="8" maxlength="8" />
			 -
			<input type=text name="useenddt" value="<?=$data['useenddt']?>" onclick="calendar(event)" onkeydown="onlynumber()" class="cline" size="8" maxlength="8" />)
		</div>
	</td>
</tr>
<tr>
	<td>��ǰ��¿���</td>
	<td class=noline>
		<label><input type=checkbox name=visible value=1 <?=$checked['visible'][1]?>>���̱�</label>
		<font class=extext>üũ������ ȭ�鿡�� �Ⱥ���</font>
	</td>
	<td>ó������</td>
	<td class="noline">
		<label><input type=radio name=processtype value='i' <?=$checked['processtype']['i']?> onClick="setGoodsType('unlimit');">��ù߼�/���</label>
		<label><input type=radio name=processtype value='b' <?=$checked['processtype']['b']?> onClick="setGoodsType('limit');">�ϰ��߼�/���</label>
		<div style="height:23;padding-top:5px" class=extext>�ϰ��߼�/��� �����ÿ� �� ���ΰ� ������ ��ǥ ���� �ο��� ����� �� �ֽ��ϴ�.</div>

	</td>
</tr>
<tr>
	<td>����Ⱓ</td>
	<td colspan="3">
		<input type=text name="start_dt" value="<?=$data['start']['dt']?>" onclick="calendar(event)" onkeydown="onlynumber()" class="cline" size="8" maxlength="8" />
		<input type="text" name="start_hour" value="<?=$data['start']['hour']?>" onkeydown="onlyhour()" class="cline" size="2" maxlength="2" /> : <input type="text" name="start_min" value="<?=$data['start']['min']?>" onkeydown="onlyminute()" class="cline" size="2" maxlength="2" /> -
		<input type=text name="end_dt" value="<?=$data['end']['dt']?>" onclick="calendar(event)" onkeydown="onlynumber()" class="cline" size="8" maxlength="8" />
		<input type="text" name="end_hour" value="<?=$data['end']['hour']?>" onkeydown="onlyhour()" class="cline" size="2" maxlength="2" /> : <input type="text" name="end_min" value="<?=$data['end']['min']?>" onkeydown="onlyminute()" class="cline" size="2" maxlength="2" />
		<div style="height:23;padding-top:5px" class=extext>����Ⱓ�� �ð� ���Է½� 00:00~23:59���� �ڵ� �Էµ˴ϴ�.</div>
		<div style="height:23;" class=extext>����Ⱓ�� �Է����� ������ ������ ���� ����Ǹ�, ����ǰ ������ �ǸſϷ�˴ϴ�.</div>
</div>
	</td>
</tr>
<tr>
	<td>�����ð�����</td>
	<td colspan=3 class=noline>
		<label><input type=radio name=showtimer value='y' <?=$checked['showtimer']['y']?> />���</label>
		<label><input type=radio name=showtimer value='n' <?=$checked['showtimer']['n']?> />�̻��</label>
		<font class=extext>üũ������ ȭ�鿡�� �Ⱥ���.</font>
	</td>
</tr>
<tr>
	<td>����˻���</td>
	<td colspan=3>
		<div style='padding-top:5px'><input type=text name=keyword value="<?=$data[keyword]?>" style="width:100%" class="line"></div>
		<div style="height:23;padding-top:5px" class=extext>��ǰ�� �������� ��Ÿ�±׿� ��ǰ �˻��� Ű����� ����Ͻ� �� �ֽ��ϴ�.</div>
	</td>
</tr>
</table>
<div style="padding-top:20px"></div>
<div style="border-top:3px #efefef solid;"></div>

<!-- ��ǰ������ -->
<div class=title>������<span>�� ��ǰ �ֹ��� �����Ǵ� �������� �����մϴ�.</span></div>
<div class=noline style="padding-bottom:5px">
<div><input type=radio name="use_emoney" <?=$checked[use_emoney][0]?> value="0" onfocus=blur() onClick="fnChangeReserveType(0);"> �����ݼ����� ��å�� �����մϴ�. <font class=extext>(�� ��ǰ�� �������� <a href="../basic/emoney.php" target="_blank"><font class=extext_l>[�⺻���� > �����ݼ��� > ��ǰ ������ ���޿� ���� ��å]</font></a> ���� ������ ��å�� �����ϴ�)</font></div>
<div><input type=radio name="use_emoney" <?=$checked[use_emoney][1]?> value="1" onfocus=blur() onClick="fnChangeReserveType(1);"> �������� ���� �Է��մϴ�. <font class=extext>(�� ��ǰ�� �������� �ٷ� �Ʒ��� <b>����/���/��ۺ�</b>���� ����� ���������� �����մϴ�)</font></div>
</div>
<div style="border-bottom:3px #efefef solid;padding-top:20px"></div>

<!-- ��ǰ ����/��� -->
<div class=title>����/���/��ۺ�<span>������, ���� � ���� ������ �������� ��� ���ݿɼ��� �߰��� �� �ֽ��ϴ� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=todayshop&no=8')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<div style="height:5px;font:0"></div>
<table class=tb>
<col class=cellC><col class=cellL><col class=cellC><col class=cellL>
<tr>
	<td width=120 nowrap>�ǸŰ�*</td>
	<td width=50%>
		<input type=text name=consumer size=10 value="<?=$consumer?>" onchange="autoPrice(this)" onblur="autoPrice(this)" onkeydown="autoPrice(this)" class="line" required="required">��
	</td>
	<td width=140 nowrap <?if($formmode=='aftersale') {?>style="background-color:#FFA947;"<?}?>>���Ŵ޼��ο�/���ΰ�*</td>
	<td width=50% <?if($formmode=='aftersale') {?>style="background-color:#FFDAAF;"<?}?>>
		<input type="text" name="limit_ea" size=5 value="<?=$data['limit_ea']?>" <?=($checked['processtype']['i']) ? 'disabled' : ''?>>�� �̻� ���Ž� <input type=text name=price size=10 value="<?=$price?>" onchange="autoPrice(this)" onblur="autoPrice(this)" onkeydown="autoPrice(this)" class="line" required="required" label="���ΰ�">��
		<div style="padding-top:3px"><font class=extext>0���� ��� ���Ŵ޼����Ѿ���.</font></div>
	</td>
</tr>
<tr>
	<td nowrap <?if($formmode=='aftersale' && $data['totstock'] > 0) {?>style="background-color:#FFA947;"<?}?>>���*</td>
	<td <?if($formmode=='aftersale' && $data['totstock'] > 0) {?>style="background-color:#FFA947;"<?}?>>
		<input type=text name=stock size=10 value="<?=$stock?>" onchange="autoPrice(this)" onblur="autoPrice(this)" onkeydown="autoPrice(this)" class="line" label="���" required="required">
		<span class="extext">����� ������� ������ ��ǰ���°� ǰ���� ��ϵ˴ϴ�.</span>
		<div class=noline>
			<label><input type="checkbox" name="runout" value="1" <?=$checked['runout']['1']?> />ǰ��</label>
		</div>
	</td>
	<td nowrap <?if($formmode=='aftersale') {?>style="background-color:#FFA947;"<?}?>>�Ǹż��� ���⼳�� <img src="../img/btn_question.gif" style="cursor:pointer;" class="godo-tooltip" tooltip="������ ��ġ�� ���� �Ǹŵ� ������ �ջ�Ǿ� ��ǰ �������� ���� �˴ϴ�.<br>EX) 50(�� �Ǹż���) + 10(�Ǹų��� ���� ������) = 60 (��ǰ ������ ���� ��ġ)" /></td>
	<td <?if($formmode=='aftersale') {?>style="background-color:#FFDAAF;"<?}?>>
		<input type=text name=fakestock size=10 value="<?=$data['fakestock']?>" class="line"<?=($checked['processtype']['i'] && !$checked['showbuyercnt']['y']) ? 'disabled' : ''?> onkeydown="onlynumber()" />
		<div style="margin-top:3px;"><font class=extext>���� �Ǹŵ� ������ �ջ�Ǿ ��µ˴ϴ�. �ϰ��߼� ��ǰ�ÿ��� ����մϴ�.</font></div>
	</td>
</tr>
<tr>
	<td nowrap>�������</td>
	<td class=noline><label><input type=checkbox name=usestock <?=$checked['usestock']['o']?> onclick="setGoodsType(this.name, this.checked)" /> �ֹ��� �������</label>
	<div style="padding-top:3px"><font class=extext>üũ ���ϸ� ��� ������� ������ �Ǹ�, ��� ������ �ڵ����� �Ǹ������ ����</font></div></td>
	<td nowrap <?if($formmode=='aftersale') {?>style="background-color:#FFA947;"<?}?>>&nbsp;</td>
	<td class=noline <?if($formmode=='aftersale') {?>style="background-color:#FFDAAF;"<?}?>>
		<label><input type="checkbox" name="fakestock2real" value="1" <?=$checked['fakestock2real'][1]?> <?=($checked['processtype']['i']) ? 'disabled' : ''?>> �Ǹż��� ���⼳���� �� �Ǹŷ��� �ջ� <img src="../img/btn_question.gif" style="cursor:pointer;"  class="godo-tooltip" tooltip="�ջ�Ǿ� �������� �Ǹ� ��ġ�� �ǸſϷ� �� �� �Ǹŷ��� �Բ� �ջ� �ϰ��� �ϴ� ��� ������ �ּ���.<br>�Ǹż��� ����� ���� �߰��� �Ǹż������� ���θ� ���������� ���ŷ��� �޼��� ������ ����ǳ� �����δ� ���Ŵ޼� �ο��� �������� ���� �ǸŽ��а� �� �� �ִ� ��쿡 ����մϴ�." /></label>
		<div style="margin-top:3px;"><font class=extext>üũ�� ���Ƿ� ������ �Ǹ� ������ ���� �Ǹŷ��� ������ �ջ�˴ϴ�.</font></div>
	</td>
</tr>
<tr>
	<td nowrap>��� ����</td>
	<td class=noline>
		<label><input type="radio" name="showstock" value='y' <?=$checked['showstock']['y']?> <?=$disabled['showstock']['y']?> /> ���̱�</label>
		<label><input type="radio" name="showstock" value='n' <?=$checked['showstock']['n']?> /> �����</label>
		<div style="padding-top:3px"><font class=extext>���ο� ������ ���⿩��</font></div>
	</td>
	<td nowrap>���ż��� ����</td>
	<td>
	�ּұ��ż��� : <input type="text" name="min_ea" size=5 value="<?=$data['min_ea']?>"> &nbsp;
	�ִ뱸�ż��� : <input type="text" name="max_ea" size=5 value="<?=$data['max_ea']?>">
	<div style="padding-top:3px"><font class=extext>0�̸� ������ �����ϴ�</font></div>
	</td>
</tr>
<tr>
	<td nowrap>������ ����</td>
	<td class=noline>
		<label><input type="radio" name="showpercent" value="y" <?=$checked['showpercent']['y']?> /> ���</label>
		<label><input type="radio" name="showpercent" value="n" <?=$checked['showpercent']['n']?> />�̻��</label>
	</td>
	<td nowrap>�Ǹż��� ����</td>
	<td class="noline">
		<label><input type="radio" name="showbuyercnt" value="y" <?=$checked['showbuyercnt']['y']?> onClick="setGoodsType('showbuyercnt');"/> ���</label>
		<label><input type="radio" name="showbuyercnt" value="n" <?=$checked['showbuyercnt']['n']?> onClick="setGoodsType('hidebuyercnt');"/>�̻��</label>
	</td>
</tr>
<tr>
	<td>����/�����</td>
	<td class=noline>
		<label><input type=radio name=tax value=1 <?=$checked['tax'][1]?>> ����</label>
		<label><input type=radio name=tax value=0 <?=$checked['tax'][0]?>> �����</label>
	</td>
	<td nowrap>��޺����� ����</td>
	<td class=noline>
		<label><input type="radio" name="usememberdc" value="y" <?=$checked['usememberdc']['y']?> /> ���</label>
		<label><input type="radio" name="usememberdc" value="n" <?=$checked['usememberdc']['n']?> />�̻��</label>
	</td>
</tr>
<script type="text/javascript">
function chk_delivery_type(){
	var obj = document.getElementsByName('delivery_type');
	if(obj[2].checked == true) document.getElementById('gdi').style.display="inline";
	else document.getElementById('gdi').style.display="none";

	if(obj[3].checked == true) document.getElementById('gdi2').style.display="inline";
	else document.getElementById('gdi2').style.display="none";
}
</script>
<tr id="deliveryBlock" <?if ($data['goodstype']=='coupon') {?>style="display:none"<?}?>>
	<td>��ۺ�</td>
	<td colspan=3>
	<table cellspacing="0" cellpadding="0" border="0">
	<tr height=40>
		<td>
			<label><input type="radio" name="delivery_type" value="0" <?=$checked['delivery_type'][0]?> class="null" onclick="chk_delivery_type();">�⺻�����å�� ����</label>
			<label><input type="radio" name="delivery_type" value="1" <?=$checked['delivery_type'][1]?> class="null" onclick="chk_delivery_type();"> ������</label>
			<label><input type="radio" name="delivery_type" value="2" <?=$checked['delivery_type'][2]?> class="null" onclick="chk_delivery_type();">��ǰ�� ��ۺ� �Է�</label>
			<span style="display:none;" id="gdi">&nbsp;<input type="text" name="goods_delivery" value="<?=$data['goods_delivery']?>" size="8" onkeydown="onlynumber()">��</span>
			<label><input type="radio" name="delivery_type" value="3" <?=$checked['delivery_type'][3]?> class="null" onclick="chk_delivery_type();">���ҹ�ۺ�</label>
			<span style="display:none;" id="gdi2">&nbsp;<input type="text" name="goods_delivery2" value="<?=$data['goods_delivery']?>" size="8" onkeydown="onlynumber()">��</span>
		</td>
	</tr>
	</table>
	<div><font class=extext>�⺻�����å�� ��ǰ�� ��ۺ� ��å�� <a href="../basic/delivery.php" target=_blank><font class=extext_l>[�⺻���� > ���/�ù�� ����]</font></a> ���� ���� �Ͻ� �� �ֽ��ϴ�.</font></div>
	</td>
</tr>
<tr>
	<td>������</td>
	<td colspan=3>
		<input type=text name=reserve size=10 value="<?=$reserve?>" onchange="autoPrice(this)" onblur="autoPrice(this)" onkeydown="autoPrice(this)" class="line">��

	</td>
</tr>
</table>

<div style="padding: 10px 10px 10px 0px"><a href="javascript:vOption()" onfocus=blur()><img src="../img/btn_priceopt_add.gif" align=absmiddle></a> <font class=small color=444444>�̻�ǰ�� �ɼ��� �������ΰ�� ����ϼ��� (����, ������ ��)</font>
<a href="javascript:popup('http://guide.godo.co.kr/guide/php/ex_infoprice.html',730,700)"><img src="../img/icon_sample.gif" border="0" align=absmiddle></a></div>

<div id=objOption style="display:none">
<div style="padding-bottom:10">
<font class=small color=black><b>�ɼǸ�1</b> : <input type=text name=optnm[] value="<?=$optnm[0]?>">
<a href="javascript:addopt1()" onfocus=blur()><img src="../img/i_add.gif" align=absmiddle></a> <a href="javascript:delopt1()" onfocus=blur()><img src="../img/i_del.gif" align=absmiddle></a><span style="width:20"></span>
<b>�ɼǸ�2</b></font> : <input type=text name=optnm[] value="<?=$optnm[1]?>">
<a href="javascript:addopt2()" onfocus=blur()><img src="../img/i_add.gif" align=absmiddle></a> <a href="javascript:delopt2()" onfocus=blur()><img src="../img/i_del.gif" align=absmiddle></a><span style="width:20"></span>
<input type="hidden" name="opttype" value="single" />
<!--
<span class=noline><b>�ɼ���¹��</b> :
<input type=radio name=opttype value="single" <?=$checked['opttype']['single']?>> ��ü��
<input type=radio name=opttype value="double" <?=$checked['opttype']['double']?>> �и���
</span>
-->
</div>
<?if(count($opt)>1 || $opt1[0] != null || $opt2[0] != null){?><script>vOption();</script><?}?>
<div style="margin:10px 0"><font class=extext>����� �ɼǸ�1�� �ɼǸ�2�� ����Ŭ���Ͻÿ� �ɼ��� �����Ͻ� �� �ֽ��ϴ�.</font></div>
<table id=tbOption border=1 bordercolor=#cccccc style="border-collapse:collapse">
<tr align=center>
	<td width=116></td>
	<td><span style="color:#333333;font-weight:bold;">�ǸŰ�</span></td>
	<td><span style="color:#333333;font-weight:bold;">���ΰ�</span></td>
	<td><span style="color:#333333;font-weight:bold;">������</span></td>
<?
	$j = 3;
	if (is_array($opt2) && empty($opt2) === false) {
		foreach ($opt2 as $v) {
		$j++;
?>
	<td id='tdid_<?=$j?>'><input type=text name="opt2[]" <?if($v != ''){?>class=fldtitle value="<?=$v?>"<?}else{?>class="opt gray" value='�ɼǸ�2'<?}?> ondblclick="delopt2part('tdid_<?=$j?>')" onclick="chkOptName(this)" onblur="chkOptNameOver(this)"></td>
<?
		}
	}
?>
</tr>
<?
	$i = 0;
	$op2	 = $opt2[0];
	if (is_array($opt1) && empty($opt2) === false) {
		foreach ($opt1 as $op1) {
			$i++;
?>
<tr id="trid_<?=$i?>">
	<td width=116 nowrap><input type=text name="opt1[]" <?if($op1 != ''){?>class=fldtitle value="<?=$op1?>"<?}else{?>class="opt gray" value='�ɼǸ�1'<?}?> <?if($i != 1){?>ondblclick="delopt1part('trid_<?=$i?>')"<?}?> onclick="chkOptName(this)" onblur="chkOptNameOver(this)"></td>
	<td><input type=text name="option[consumer][]" class="opt gray" value="<?=$opt[$op1][$op2]['consumer']?>"></td>
	<td><input type=text name="option[price][]" class="opt gray" value="<?=$opt[$op1][$op2]['price']?>"></td>
	<td><input type=text name="option[reserve][]" class="opt gray" value="<?=$opt[$op1][$op2]['reserve']?>"></td>
	<? foreach ($opt2 as $op2){ ?>
	<td><input type=text name="option[stock][]" <?if($opt[$op1][$op2]['stock']){?>class="opt" value="<?=$opt[$op1][$op2]['stock']?>"<?}else{?>class="opt gray" value="���"<?}?> onclick="chkOptName(this)" onblur="chkOptNameOver(this)"><input type=hidden name="option[optno][]" value="<?=$opt[$op1][$op2]['optno']?>"></td>
	<? } ?>
</tr>
<?
		}
	}
?>
</table>
<div style="padding-top:10px">
	<select name="dopt" style="width:125">
		<option value=''>�ɼǹٱ��� ����</option>
		<?
		$query = "SELECT * FROM ".GD_DOPT." ORDER BY sno DESC";
		$res = $db->query($query);
		while($rdopt = $db ->fetch($res, 1)){
			$l = strlen($rdopt['title']);
			if($l > 20){
				$rdopt['title'] = strcut($rdopt['title'],20);
			}
		?>
		<option value='<?=$rdopt['sno']?>'><?=$rdopt['title']?></option>
		<?}?>
	</select>&nbsp;&nbsp;<a href="javascript:applydopt()"><img src="../img/btn_optionbasket.gif" border="0" align="absmiddle"></a>
	<a href="javascript:popupLayer('../goods/popup.dopt_list.php',800,600)"><img src="../img/btn_optionbasket_admin.gif" border="0" align="absmiddle"></a>
</div>
<p />
</div>

<div style="width:660px;border:solid 1px #cccccc; margin:5px 0 5px 0">
	<div style="margin:1px; background-color:#f8f8f8; padding:7px 10px; line-height:1.3em;">
		<div>�� <font class="small1" color="#444444">��ǰ ���������� �ɼǺ� ��� ������ ǥ���ϰ��� �ϴ� ��� ��� ������ �ֹ��� ��� �������� üũ�� �ּž� �մϴ�.</font></div>
	</div>
</div>

<div style="border-bottom:3px #efefef solid;padding-top:20px"></div>

<!-- �߰��ɼ� -->
<div class=title>�߰��ɼ�/�߰���ǰ/����ǰ<span>�߰��ɼ��� ������ ����� �� ������, �߰���ǰ�� �Ǹ��ϰų� ����ǰ�� ������ ���� �ֽ��ϴ� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=todayshop&no=8')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<div class=noline style="padding-bottom:5px">
<label><input type="radio" name="useAdd" <?=$checked[useAdd][1]?> onclick="openLayer('tbAdd','block')" onfocus="blur()" value="1" /> ���</label>
<label><input type="radio" name="useAdd" <?=$checked[useAdd][0]?> onclick="openLayer('tbAdd','none')" onfocus="blur()" value="0" /> ������</label>
</div>

<a href="javascript:add_addopt()"><img src="../img/i_addoption.gif" align=absmiddle></a>
<a href="javascript:del_addopt()"><img src="../img/i_deloption.gif" align=absmiddle></a>
<span class=small1 style="padding-left:5px">�ɼǸ� �ƹ� ���뵵 �Է����� ������ �ش� �ɼ��� ����ó���˴ϴ�.</span>

<div style="height:7px"></div>

<table id=tbAdd style="display:<?=$display[useAdd]?>" border=2 bordercolor=#cccccc style="border-collapse:collapse">
<tr bgcolor=#f7f7f7 align=center>
	<td>�ɼǸ� <font class=small>(��. �Ǽ��縮)</font></td>
	<td>�׸�� <font class=small>(��. �����)</font></td>
	<td>���� <font class=small color=444444>(�����϶��� 0���Է�)</font></td>
	<td>���Ž��ʼ�</td>
</tr>
<col valign=top style="padding-top:5px">
<col span=2><col align=center valign=top style="padding-top:5px">
<? foreach ($addopt as $k=>$v){ ?>
<tr>
	<td>
	<input type=text name=addoptnm[] value="<?=$addoptnm[$k]?>">
	<a href="javascript:void(0)" onClick="add_subadd(this)"><img src="../img/i_proadd.gif" align=absmiddle border=0></a>
	</td>
	<td colspan=2>

	<table>
	<col><col align=center>
	<? foreach ($v as $v2){ ?>
	<tr>
		<td><input type=text name=addopt[opt][<?=$k?>][] value="<?=$v2[opt]?>" style="width:270px"> ���ý�</td>
		<td>�Ǹűݾ׿� <input type=text name=addopt[addprice][<?=$k?>][]  size=9 value="<?=$v2[addprice]?>"> �� �߰�</td>
	</tr>
	<? } ?>
	</table>

	</td>
	<td class=noline align=center><input type=checkbox name=addoptreq[<?=$k?>] value="o" <?=$checked[addoptreq][$k]?>></td>
</tr>
<? } ?>
</table>
<div style="border-bottom:3px #efefef solid;padding-top:20px"></div>

<!-- ��ǰ �̹��� -->
<div class=title>��ǰ �̹���<span><a href="javascript:manual('<?=$guideUrl?>board/view.php?id=todayshop&no=8')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></span></div>

<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>�̹�����Ϲ��</td>
	<td class="noline">
	<label><input type="radio" name="image_attach_method" value="file" onClick="fnSetImageAttachForm();" <?=$checked[image_attach_method][file]?>>���� ���ε�</label>
	<label><input type="radio" name="image_attach_method" value="url"  onClick="fnSetImageAttachForm();" <?=$checked[image_attach_method][url]?>>�̹���ȣ���� URL �Է�</label>

	</td>
</tr>
</table>

<div id="image_attach_method_upload_wrap">

	<div style="width:660px;border:solid 1px #cccccc; margin:5px 0 5px 0">
		<div style="margin:1px; background-color:#f8f8f8; padding:7px 10px; line-height:1.3em;">
			<div>�� <font class="small1" color="#444444">�̹��������� �뷮�� ��� ���ؼ� <?=ini_get('upload_max_filesize')?>B������ ����� �� �ֽ��ϴ�.</font></div>
		</div>
	</div>

	<!-- �̹��� ���� ���ε� -->
	<table class=tb>
	<col class=cellC><col class=cellL><col class=cellC><col class=cellL>
	<?
	$imgSizeStr['m'] = '573';
	$imgSizeStr['s'] = '70';
	$imgSizeStr['i'] = '200';
	foreach ($imgs as $k => $v){ $t = array_map("toThumb",$v);
	?>
	<tr>
		<td>
			<?=$str_img[$k]?>*
			<div style="padding-left:24px;"><font class=extext>(���� <?=$imgSizeStr[$k]?> �ȼ�)</font></div>
		</td>
		<td>
			<table id="tb_<?=$k?>">
			<col valign=top span=2>
			<? for ($i=0;$i<count($v);$i++){ ?>
			<tr>
				<td>
				<? if ($k == "m"){ if (!$i){ ?>
				<a href="javascript:addfld('tb_<?=$k?>')"><img src="../img/i_add.gif" align=absmiddle></a>
				<? } else { ?><font color=white>.........</font>
				<? }} else { ?><font color=white>.........</font>
				<? } ?>
				<span><input type=file name=img_<?=$k?>[] style="width:300px" onChange="preview(this)"></span>
				</td>
				<td>
				<? if ($v[$i]){ ?>
				<div style="padding:0 0" class=noline><input type=checkbox name=del[img_<?=$k?>][<?=$i?>]><font class=small color=#585858>���� (<?=$v[$i]?>)</font></div>
				<? } ?>
				</td>
				<td>
				<?=goodsimg($t[$i],20,"style='border:1 solid #cccccc' onclick=popupImg('../data/goods/$v[$i]','../') class=hand",2)?>
				</td>
			</tr>
			<? } ?>
			</table>
		</td>
	</tr>
	<? } ?>
	</table>
	<!-- //�̹��� ���� ���ε� -->
</div>

<div id="image_attach_method_link_wrap">
<!-- �̹��� ȣ���� URL �Է� -->
	<div style="width:660px;border:solid 1px #cccccc; margin:5px 0 5px 0">
		<div style="margin:1px; background-color:#f8f8f8; padding:7px 10px; line-height:1.3em;">
			<div>�� <font class="small1" color="#444444">�̹��� ȣ���ÿ� ��ϵ� �̹����� �� �ּҸ� �����Ͽ� �ٿ� �ֱ� �Ͻø� ��ǰ �̹����� ��ϵ˴ϴ�.</font></div>
			<div>�� <font class="small1" color="#444444">ex) http://godohosting.com/img/img.jpg</font></div>
		</div>
	</div>

	<table class=tb>
	<col class=cellC><col class=cellL><col class=cellC><col class=cellL>
	<? foreach ($urls as $k=>$v) { ?>
	<tr>
		<td>
		<?=$str_img[$k]?>
		</td>
		<td>

		<table id="tbl_<?=$k?>">
		<col valign=top span=2>
		<? for ($i=0;$i<count($v);$i++){ ?>
		<?
			if ($v[$i] && ! preg_match('/^http:\/\//',$v[$i])) $v[$i] = 'http://'.$_SERVER['SERVER_NAME'].'/shop/data/goods/'.$v[$i];
			?>
		<tr>
			<td>
			<? if (!in_array($k,array("i","s","mobile"))){ if (!$i){ ?>
			<a href="javascript:addfld('tbl_<?=$k?>')"><img src="../img/i_add.gif" align=absmiddle></a>
			<? } else { ?><font color=white>.........</font>
			<? }} else { ?><font color=white>.........</font>
			<? } ?>
			<span><input type=text name=url_<?=$k?>[] style="width:430px" onChange="preview(this)" value="<?=$v[$i]?>"></span>
			</td>
			<td>
			<?=goodsimg($v[$i],20,"style='border:1 solid #cccccc' onclick=popupImg('$v[$i]','../') class=hand",2)?>
			</td>
		</tr>
		<? } ?>
		</table>

		</td>
	</tr>

	<? if ($k == 'l'){ ?>
	</table>
	<table class=tb>
	<col class=cellC><col class=cellL><col class=cellC><col class=cellL>
	<? } ?>

	<? } ?>
	</table>
<!-- //�̹��� ȣ���� URL �Է� -->
</div>
<script>
fnSetImageAttachForm();
</script>

<div style="border-bottom:3px #efefef solid;padding-top:30px"></div>

<div class=title>��ǰ Ÿ��Ʋ ��� <a href="javascript:popup('http://guide.godo.co.kr/guide/php/ex_todayshop_title_banner.html',650,560)"><img src="../img/icon_sample.gif" border="0" align=absmiddle></a></div>
<div style="width:660px;border:solid 1px #cccccc; margin:5px 0 5px 0">
	<div style="margin:1px; background-color:#f8f8f8; padding:7px 10px; line-height:1.3em;">
		<div>�� <font class="small1" color="#444444">��ǰ �̹��� ��ܿ� �߰��Ǵ� ����Ÿ��Ʋ ��� �Դϴ�. ��ǰ�� �ܿ� ������ ��ǰ ȫ�� ������ �߰��� �� �ֽ��ϴ�.</font></div>
	</div>
</div>
<div id="extra_header"><textarea name="extra_header" style="width:100%;height:150px" type=editor><?=$data['extra_header']?></textarea></div>

<div style="border-bottom:3px #efefef solid;padding-top:30px"></div>

<!-- ��ǰ ���� -->
<div class=title>��ǰ ���� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=todayshop&no=8')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a>  <font class=small1 color=444444>�Ʒ� <img src="../img/up_img.gif" border=0 align=absmiddle hspace=2>�� ���� �̹����� ����ϼ���.</font> &nbsp;<font color=E6008D>��</font><font class=small1 color=444444><font color=E6008D> ��� �̹��������� �ܺθ�ũ (����, G���� ���� ���¸��� ����)</font>�� �������� �ʽ��ϴ�.</div>

<table border=1 bordercolor=#cccccc style="border-collapse:collapse">
<tr><td>
<table cellpadding=0 cellspacing=0 bgcolor=f8f8f8>
<tr><td style="padding:10 10 5 10"><font class=small1 color=444444><font color=E6008D>�̹��� �ܺθ�ũ</font> �� <font color=E6008D>���¸���</font> �ǸŸ� ���� �̹����� ����Ͻ÷��� <font color=E6008D>�ݵ�� �̹���ȣ���� ����</font>�� �̿��ϼž� �մϴ�.</a></td></tr>
<tr><td style="padding:0 10 7 10"><font class=small1 color=444444>�̹���ȣ������ ��û�ϼ̴ٸ� <a href="javascript:popup('http://image.godo.co.kr/login/imghost_login.php',980,700)" name=navi><img src="../img/btn_imghost_admin.gif" align=absmiddle></a>, ���� ��û���ϼ̴ٸ� <a href="http://hosting.godo.co.kr/imghosting/service_info.php" target=_blank><img src="../img/btn_imghost_infoview.gif" align=absmiddle></a> �� �����ϼ���!</td></tr>
</table>
</td></tr></table>

<div style="padding-top:5"></div>
<table class=tb>
<col class=cellC><col class=cellL><col class=cellC><col class=cellL>
<tr>
	<td>ª������</td>
	<td>
	<textarea name=shortdesc style="width:100%;height:20px;overflow:visible" class=tline><?=$data['shortdesc']?></textarea>
	<div style="margin-top:5px;line-height:160%;" class="extext">������ ��ǰ ������ �ʼ� �Է»������� ������ ��Ÿ����Ʈ ������ ���� �ʿ��� �����Դϴ�.<br> ��Ÿ����Ʈ ������ ���ϴ� ��� ª�� ������ �ۼ����ּ���.</div>
	</td>
</tr>
</table>
<div style="height:6px;font:0"></div>

<div id="ta_longdesc"><textarea name=longdesc style="width:100%;height:400px" type=editor><?=$data['longdesc']?></textarea></div>
<div style="border-bottom:3px #efefef solid;padding-top:20px"></div>

<!-- ���� �޸� -->
<div class=title>���� �޸� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=todayshop&no=8')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<textarea name=memo style="width:100%;height:60px" class=tline><?=$data['memo']?></textarea>
<div style="border-bottom:3px #efefef solid;padding-top:20px"></div>


<!-- ��Ÿ����Ʈ ���� ���� -->
<div class=title>��Ÿ����Ʈ ���� ����</div>

<p style="width:660px;border:solid 1px #cccccc; margin-bottom:5px;padding:7px 10px;font-color:#444;background-color:#f8f8f8;line-height:1.3em;">
��Ÿ����Ʈ ������ ���� �ʿ��� ���� �Դϴ�. <br>
��ǰ ȫ���� ���� ��Ÿ����Ʈ�� ������ ���Ͻô� ��� �Ʒ��� ������ �ݵ�� �Է��� �ּž� �մϴ�. <br>
(�Էµ��� ���� ��ǰ�� ��Ÿ����Ʈ�� ������� �ʽ��ϴ�.)
</p>

<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td width=120 nowrap>
		��ǰ ���ó �Է�<br>
		<span style="font-weight:normal;">(��Ÿ ����Ʈ ���� ����)</span>
	</td>
	<td>
		<table width="100%">
		<tr>
			<td>��ȣ��</td>
			<td>
				<input type="text" name="usable_spot_name" style="width:100%" value="<?=$data['usable_spot_name']?>" label="����ó" class="line">
			</td>
		</tr>
		<tr>
			<td valign="top" style="padding-top:8px;" width="40">�ּ�</td>
			<td>
				<? $_post = explode("-",$data['usable_spot_post']) ?>
				<input type="text" name="zipcode[]" style="width:35px" value="<?=array_shift($_post)?>" class="line" label="�����ȣ">
				-
				<input type="text" name="zipcode[]" style="width:35px" value="<?=array_shift($_post)?>" class="line" label="�����ȣ">

				<a href="javascript:popup('../proc/popup_zipcode.php?form=opener.document.fm',400,500)"><img src="../img/btn_zipcode.gif" align=absmiddle></a>

				<input type="text" name="address" style="width:100%" value="<?=$data['usable_spot_address']?>" label="�ּ�" class="line" readonly>

				<input type="text" name="address_ext" style="width:100%" value="<?=$data['usable_spot_address_ext']?>" label="�ּ�" class="line">

			</td>
		</tr>
		<tr>
			<td>����ó</td>
			<td>
				<input type="text" name="usable_spot_phone" style="width:100%" value="<?=$data['usable_spot_phone']?>" label="����ó" class="line">
			</td>
		</tr>
		</table>

	</td>
</tr>
<tr>
	<td nowrap>
		ī�װ� ����
	</td>
	<td>
		<? $_ar_usable_spot_type = array('����,��ǰ','�м�,��Ƽ','�ڵ���','������,����','����,ħ��','��Ȱ,�ǰ�','���,���Ƶ�','������,����','����,��ȭ,���','����,����','��Ÿ'); ?>
		<select name="usable_spot_type">
			<? foreach($_ar_usable_spot_type as $v) { ?>
			<option value="<?=$v?>" <?=($v == $data['usable_spot_type'] ? 'selected' : '' )?>><?=$v?></option>
			<? } ?>
		</select>

	</td>
</tr>
</table>

<div class=button>
	<input type=image id="btn_save" src="../img/btn_<?=$_GET[mode]?>.gif">
	<?=$btn_list?>
	<?if($_GET['tgsno']){?>&nbsp;<a href="../../todayshop/today_goods.php?tgsno=<?=$_GET['tgsno']?>" target="_blank"><img src="../img/btn_goods_view.gif"></a><?}?>
</div>
</form>
</div>

<script type="text/javascript">init_form();</script>
<script type="text/javascript" src="../godo_ui.js"></script>

<!-- �������� Ȱ��ȭ ��ũ��Ʈ -->
<script src="../../lib/meditor/mini_editor.js"></script>
<script>mini_editor("../../lib/meditor/");chk_delivery_type();</script>
<SCRIPT LANGUAGE="JavaScript" SRC="../proc/warning_disk_js.php"><!-- not_delete --></SCRIPT>
<?include "../_footer.php";?>
