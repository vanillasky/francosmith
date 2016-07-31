<?
@include_once "../../conf/config.mobileShop.php";
@include_once "../../conf/qr.cfg.php";
@include_once "../../conf/config.purchase.php";
@include "../../conf/my_icon.php";

if (!$_GET[mode]) $_GET[mode] = "register";
if ($_GET[mode]=="register"){
	$checked[usedelivery][0] = $checked[open][1] = $checked[open_mobile][1] = $checked[open_shoptouch][1] = $checked[opttype][single] = "checked";
	$hidden[sort] = "style='display:none'";
}

$r_maker[''] = $r_origin[''] = "-- ��Ϻ��� --";
$str_img	= array(
			"shoptouch"	=> "���θ� App(����)�̹���"
			);

### ������
$query = "select distinct maker from ".GD_GOODS."";
$res = $db->query($query);
while ($data=$db->fetch($res)) if ($data[maker]) $r_maker[$data[maker]] = $data[maker];

### ������
$query = "select distinct origin from ".GD_GOODS."";
$res = $db->query($query);
while ($data=$db->fetch($res)) if ($data[origin]) $r_origin[$data[origin]] = $data[origin];

/// ������ ����
$r_myicon = isset($r_myicon) ? (array)$r_myicon : array();
for ($i=0;$i<=7;$i++) if (!isset($r_myicon[$i])) $r_myicon[$i] = '';
$cnt_myicon = sizeof($r_myicon);

### ���� ��ǰ (, �� ����� ��ǰ��ȣ)
$related_goodsnos = '';
if ($_GET[mode]=="modify"){

	$goodsno = $_GET[goodsno];

	### ��Ƽī�װ�
	$query = "select category,sort from ".GD_GOODS_LINK." where goodsno='$goodsno' order by category";
	$res = $db->query($query);
	while ($data=$db->fetch($res)) $r_category[$data[category]] = $data[sort];

	### ��ǰ ���� ��������
	$data = $db->fetch("SELECT g.*, sg.img_shoptouch, sg.slongdesc, sg.open_shoptouch FROM ".GD_GOODS." g JOIN ".GD_SHOPTOUCH_GOODS." sg ON g.goodsno=sg.goodsno WHERE g.goodsno='$goodsno'",1);
	$data = array_map("slashes",$data);

	$data[launchdt] = str_replace(array('-','00000000'),'',$data[launchdt]);
	$ex_title = explode("|",$data[ex_title]);

	### QR ��� ���� ��������
	$qrdata = $db->fetch("select count(*) from ".GD_QRCODE." where qr_type='goods' and contsNo=$goodsno");
	if($qrdata[0]>0){ $data['qrcode'] = "y"; }else{ $data['qrcode'] = "n";}

	for ($i=0;$i<$cnt_myicon;$i++) if ($data[icon]&pow(2,$i)) $checked[icon][pow(2,$i)] = "checked";

	### ���û�ǰ ����Ʈ (��ġ�� �������� ���� �����ʹ� ���� �ڵ� ����)
	if ($data[relation]){

		// �� ������ ����
		if ($data[relation] != 'new_type') {
			$query = "
			select
				a.goodsno,a.goodsnm,a.img_s,b.price
			from
				".GD_GOODS." a,
				".GD_GOODS_OPTION." b
			where
				a.goodsno=b.goodsno and link
				and a.goodsno in ($data[relation])
			";
			$res = $db->query($query);
			$arr_relation=array();
			while ($rel=$db->fetch($res)) $arr_relation[] = $rel;
			$arr  = explode(',',$data[relation]);
			foreach($arr as $k2 => $v2) foreach($arr_relation as $k => $v)if($v2 == $v[goodsno]) {
				$query = "
				INSERT INTO ".GD_GOODS_RELATED." SET
					goodsno		= '".$data['goodsno']."',
					r_type		= 'single',
					r_goodsno		= '".$v['goodsno']."',
					r_start		= NULL,
					r_end		= NULL,
					regdt		= NOW()
				";
				$db->query($query);
				$r_relation[] = $v;
			}

			$db->query("UPDATE ".GD_GOODS." SET relation = 'new_type' WHERE goodsno = '".$data['goodsno']."'");
		}

		$r_relation = array();

		$query = "
		SELECT
			G.goodsno, G.goodsnm, G.img_s, O.price, G.totstock, G.usestock, G.runout,
			R.r_type, R.r_start, R.r_end, R.regdt AS r_regdt

		FROM ".GD_GOODS_RELATED." AS R

		INNER JOIN ".GD_GOODS." AS G
		ON R.r_goodsno = G.goodsno
		INNER JOIN ".GD_GOODS_OPTION." AS O
		ON G.goodsno = O.goodsno AND O.link = 1

		WHERE
			R.goodsno = $data[goodsno]
		";

		$rs = $db->query($query);
		while ($v = $db->fetch($rs,1)) {
			if ($v[usestock] && $v[totstock] < 1) $v[runout] = 1;
			$r_relation[] = $v;
		}
	}
} else {
	$data[tax] = 1;
	$data[open] = $data[relationis] = $data[open_mobile] = $data[open_shoptouch] = 0;

	$data[goodsno] = '';	// �ӽ÷� ��ǰ ��ȣ ����
}

if($data[goods_deli_type] == '����' || !$data[goods_deli_type])$goods_deli_type = 0;
if(!$data['use_emoney']) $data['use_emoney'] = 0;
if(!$data['delivery_type']) $data['delivery_type'] = 0;

else $goods_deli_type = 1;
if(!$data['detailView']) $data['detailView'] = 'n'; // �����Ϻ� ����

$selected[brandno][$data[brandno]] = "selected";
$checked[open][$data[open]] = "checked";
$checked[open_mobile][$data[open_mobile]] = "checked";
$checked[tax][$data[tax]] = "checked";
$checked[usestock][$data[usestock]] = "checked";
$checked[runout][$data[runout]] = "checked";
$checked[relationis][$data[relationis]] = "checked";
$checked[opttype][$data[opttype]] = "checked";
$display[relationis] = ($data[relationis]) ? "block" : "none";
$display[relation] = ($data[relationis]) ? "block" : "none";
$checked[delivery_type][$data[delivery_type]] = "checked";
$selected[goods_deli_type][$goods_deli_type] = "selected";
$checked['meta_title'][$data['meta_title']] = "checked";
$checked['use_emoney'][$data['use_emoney']] = "checked";
$checked['detailView'][$data['detailView']] = "checked"; // �����Ϻ� ����
$checked['qrcode'][$data['qrcode']] = "checked";  // qrcode ����
if(!$data['opt1kind'])$data['opt1kind'] = "img";
if(!$data['opt2kind'])$data['opt2kind'] = "img";
$checked['opt1kind'][$data['opt1kind']] = "checked";
$checked['opt2kind'][$data['opt2kind']] = "checked";
$checked['use_stocked_noti'][$data['use_stocked_noti']] = "checked";

### ���θ� App �������� dn 2011-111-14 ###
$checked['open_shoptouch'][$data['open_shoptouch']] = "checked";

$useEx = ($data[ex_title]) ? 1 : 0;
$checked[useEx][$useEx] = "checked";
$display[useEx] = ($useEx) ? "block" : "none";

$img_shoptouch = explode("|",$data[img_shoptouch]);

$imgs = $urls = array(
		'shoptouch'	=> $img_shoptouch
		);


// eof 2011-01-21

### �ʼ��ɼ�
$optnm = explode("|",$data[optnm]);
$query = "select * from ".GD_GOODS_OPTION." where goodsno='$goodsno'";
$res = $db->query($query);
while ($tmp=$db->fetch($res)){
	$tmp = array_map("htmlspecialchars",$tmp);
	$opt1[] = $tmp[opt1];
	$opt2[] = $tmp[opt2];
	$opt[$tmp[opt1]][$tmp[opt2]] = $tmp;

	### ����� ���
	$stock += $tmp[stock];

	### �ɼ��̹���
	$opt1img[$tmp['opt1']] = $tmp['opt1img'];
	$opt1icon[$tmp['opt1']] = $tmp['opt1icon'];
	$opt2icon[$tmp['opt2']] = $tmp['opt2icon'];
}
if ($opt1) $opt1 = array_unique($opt1);
if ($opt2) $opt2 = array_unique($opt2);
if (!$opt){
	$opt1 = array('');
	$opt2 = array('');
}

### �⺻ ���� �Ҵ�
$price	  = $opt[$opt1[0]][$opt2[0]][price];
$consumer = $opt[$opt1[0]][$opt2[0]][consumer];
$supply	  = $opt[$opt1[0]][$opt2[0]][supply];
$reserve  = $opt[$opt1[0]][$opt2[0]][reserve];

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

$useAdd = ($addopt) ? 1 : 0;
$checked[useAdd][$useAdd] = "checked";
$display[useAdd] = ($useAdd) ? "block" : "none";

if (!$addopt) $addopt = array(array(''));

### ������ ���� ����
$arr = array('good_icon_new.gif','good_icon_recomm.gif','good_icon_special.gif','good_icon_popular.gif','good_icon_event.gif','good_icon_reserve.gif','good_icon_best.gif','good_icon_sale.gif');

for($i=0;$i<$cnt_myicon;$i++){
	if($r_myicon[$i])$img = "<img src='../../data/my_icon/".$r_myicon[$i]."'";
	else $img = "<img src='../../data/skin/".$cfg[tplSkin]."/img/icon/".$arr[$i]."'";

	$ti_date = substr($data[regdt],0,10);
	$r_date = explode('-',$ti_date);

	if($r_myicondt[$i]){
		$date = date('Ymd',mktime(0, 0, 0, $r_date[1], $r_date[2]+$r_myicondt[$i], (int)$r_date[0]));
		if($date < date('Ymd',time())){
			$img .= " style='filter:alpha(opacity=15)'";
		}
	}
	$img .= ">";
	$r_icon[] = $img;
}

?>
<script>
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

function applydopt(){
	var obj = document.getElementById('dopt');
	var k = obj.selectedIndex;
	if( obj[k].value ){
		ifrmHidden.location.href="popup.dopt_register.php?mode=dopt_apply&sno="+obj[k].value;
	}
}

/* �ɼ� �κ� ���� */
function delopt1part(rid)
{
	var obj = document.getElementById(rid);
	var tbOption = document.getElementById('tbOption');
	var idx = obj.rowIndex;
	if (tbOption.rows.length>2) tbOption.deleteRow(obj.rowIndex);
	delopt1part_fashion(idx);
}

function delopt2part(cid)
{
	var delCellIndex = document.getElementById(cid).cellIndex;
	var tbOption = document.getElementById('tbOption');
	var idx = obj.cellIndex;
	if (tbOption.rows[0].cells.length<7) return;
	for (i=tbOption.rows.length-1;i>=0;i--){
		tbOption.rows[i].deleteCell(delCellIndex);
	}
	delopt2part_fashion(idx);
}

/*** ��üũ ***/
function chkForm2(obj)
{
	if (typeof(obj['category[]'])=="undefined"){
		if (document.getElementsByName("cate[]")[0].value) exec_add();
		else {
			alert("ī�װ��� ������ּ���");
			document.getElementsByName("cate[]")[0].focus();
			return false;
		}
	}
	if(!chkTitle()){
		alert('�׸���� �ߺ��� �� �����ϴ�.');
		return false;
	}
	if (!chkOption()) return false;
	if (!chkForm(obj)) return false;
	try {
		if(obj.useblog[0].checked) {
			if (!chkBlog(obj)) return false;
		}
	} catch(e) {}

<? if($purchaseSet['usePurchase'] == "Y" && $_GET['mode'] == "register") { ?>
	if(document.getElementById('objPurchaseOption').style.display != "none") {
		var pao = document.getElementsByName('purchaseApplyOption');
		if(!pao[0].checked && pao[0].checked) {
			alert("�ɼ��� �߰��Ͻ÷��� ����ó ��� ������ ������ �ּž� �մϴ�.");
			pao[0].focus();
			return false;
		}

		if(pao[0].checked) {
			if(!obj.pchsno.value) {
				alert("����ó�� ������ �ּ���.");
				obj.pchsno.focus();
				return false;
			}

			if(!obj.pchs_pchsdt.value) {
				alert("�������� �Է��� �ּ���.");
				obj.pchs_pchsdt.focus();
				return false;
			}
		}
	}

	var ar_stock = document.getElementsByName('option[stock][]');
	for(i = 0; i < ar_stock.length; i++) ar_stock[i].disabled = false;
<? } ?>

	// ���� ��ǰ ����
	nsRelatedGoods.make();

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
	var obj = document.getElementById('objCategory');
	oTr = obj.insertRow();
	oTd = oTr.insertCell();
	oTd.id = "currPosition";
	oTd.innerHTML = str.join(" > ");
	oTd = oTr.insertCell();
	oTd.innerHTML = "\
	<input type=text name=category[] value='" + ret + "' style='display:none'>\
	<input type=text name=sort[] value='<?=time()?>' class='sortBox right' maxlength=10 <?=$hidden[sort]?>>\
	";
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
			case 1:	oTd.innerHTML = "<input type=text name=option[price][] class='opt gray' value='" + fm.price.value + "'>"; break;
			case 2:	oTd.innerHTML = "<input type=text name=option[consumer][] class='opt gray' value='" + fm.consumer.value + "'>"; break;
			case 3:	oTd.innerHTML = "<input type=text name=option[supply][] class='opt gray' value='" + fm.supply.value + "'>"; break;
			case 4:	oTd.innerHTML = "<input type=text name=option[reserve][] class='opt gray' value='" + fm.reserve.value + "'>"; break;
			default:
<? if($purchaseSet['usePurchase'] == "Y" && $_GET['mode'] == "register") { ?>
				var pao = document.getElementsByName('purchaseApplyOption');
				if(pao[0].checked) {
					oTd.innerHTML = "<input type=text name=option[stock][] class='opt gray' value='���' onclick='chkOptName(this)' onblur=\"chkOptNameOver(this)\"><input type=hidden name=option[optno][]>";
				}
				else {
					oTd.innerHTML = "<input type=text name=option[stock][] class='opt gray' value='��� �� ��� �Է�' onclick='chkOptName(this)' onblur=\"chkOptNameOver(this)\" disabled=\"true\"><input type=hidden name=option[optno][]>";
				}
<? } else { ?>
				oTd.innerHTML = "<input type=text name=option[stock][] class='opt gray' value='���' onclick='chkOptName(this)' onblur=\"chkOptNameOver(this)\"><input type=hidden name=option[optno][]>";
<? } ?>
			break;
		}
	}
	addopt1_fashion();
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
<? if($purchaseSet['usePurchase'] == "Y" && $_GET['mode'] == "register") { ?>
		var pao = document.getElementsByName('purchaseApplyOption');
		if(pao[0].checked) {
			oTd.innerHTML = (i) ? "<input type=text name=option[stock][] class='opt gray'  value='���' onclick='chkOptName(this)' onblur=\"chkOptNameOver(this)\"><input type=hidden name=option[optno][]>" : "<input type=text class='opt gray' name=opt2[] value='�ɼǸ�2' required label='2���ɼǸ�' ondblclick=\"delopt2part('"+oTd.id+"')\" onclick='chkOptName(this)' onblur=\"chkOptNameOver(this)\">";
		}
		else {
			oTd.innerHTML = (i) ? "<input type=text name=option[stock][] class='opt gray'  value='��� �� ��� �Է�' onclick='chkOptName(this)' onblur=\"chkOptNameOver(this)\" disabled=\"true\"><input type=hidden name=option[optno][]>" : "<input type=text class='opt gray' name=opt2[] value='�ɼǸ�2' required label='2���ɼǸ�' ondblclick=\"delopt2part('"+oTd.id+"')\" onclick='chkOptName(this)' onblur=\"chkOptNameOver(this)\">";
		}
<? } else { ?>
		oTd.innerHTML = (i) ? "<input type=text name=option[stock][] class='opt gray'  value='���' onclick='chkOptName(this)' onblur=\"chkOptNameOver(this)\"><input type=hidden name=option[optno][]>" : "<input type=text class='opt gray' name=opt2[] value='�ɼǸ�2' required label='2���ɼǸ�' ondblclick=\"delopt2part('"+oTd.id+"')\" onclick='chkOptName(this)' onblur=\"chkOptNameOver(this)\">";
<? } ?>
	}
	addopt2_fashion();
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
	oTd.innerHTML = "<input type=hidden name=addopt[sno][" + idx + "][]><input type=text name=addopt[opt][" + idx + "][] style='width:270px'> ���ý�";
	oTd = oTr.insertCell();
	oTd.innerHTML = "�Ǹűݾ׿� <input type=text name=addopt[addprice][" + idx + "][] size=9> �� �߰�";
}
function del_subadd(obj)
{
	var idx = obj.parentNode.parentNode.rowIndex - 1;
	obj = obj.parentNode.parentNode.childNodes(1).getElementsByTagName('table')[0];
	obj.deleteRow();
}

/*** ���û�ǰ ***/
function open_box(name,isopen)
{
	var mode;
	var isopen = (isopen || document.getElementById('obj_'+name).style.display!="block") ? true : false;
	mode = (isopen) ? "block" : "none";
	document.getElementById('obj_'+name).style.display = document.getElementById('obj2_'+name).style.display = mode;
}
function list_goods(name)
{
	var category = '';
	open_box(name,true);
	var els = document.forms[0][name+'[]'];
	for (i=0;i<els.length;i++) if (els[i].value) category = els[i].value;
	var ifrm = eval("ifrm_" + name);
	var goodsnm = eval("document.forms[0].search_" + name + ".value");
	ifrm.location.href = "_goodslist.php?name=" + name + "&category=" + category + "&goodsnm=" + goodsnm;
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
		tmp[tmp.length] = "<div style='float:left;width:0;border:1 solid #cccccc;margin:1px;' title='" + obj.rows[i].cells[1].getElementsByTagName('div')[0].innerText + "'>" + obj.rows[i].cells[0].innerHTML + "</div>";
	}
	document.getElementById(name+'X').innerHTML = tmp.join("") + "<div style='clear:both'>";
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
	objTop.moveRow(iciRow.rowIndex,nextPos);
	react_goods(nameObj);
}
function keydnTree()
{
	if (iciRow==null) return;
	switch (event.keyCode){
		case 38: moveTree(-1); break;
		case 40: moveTree(1); break;
	}
	return false;
}
document.onkeydown = keydnTree;

/*** ��ǰ �̹��� ***/
function preview(obj)
{
	var tmp = obj.parentNode.parentNode.parentNode.childNodes(2);
	tmp.innerHTML = "<img src='" + obj.value + "' width=20 onload='if(this.height>this.width){this.height=20}' style='border:1 solid #cccccc' onclick=popupImg(this.src,'../') class=hand>";
}

function addfldShoptouch(obj)
{
	var tb = document.getElementById(obj);
	if(tb.rows.length > 10) {
		alert("���θ� App�� �̹����� �ִ� 10������ ��� �����մϴ�.");
		return;
	}

	oTr = tb.insertRow();
	oTd = oTr.insertCell();
	oTd.innerHTML = "<a href='javascript:void(0)' onClick='delfld(this)'><img src='../img/i_del.gif' align=absmiddle></a>	<span>" + tb.rows[0].cells[0].getElementsByTagName('span')[0].innerHTML + "</span>";
	oTd.getElementsByTagName('input')[0].value='';
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
	var el = document.getElementsByName('option[' + name + '][]');
	el[0].value = obj.value;
}

function vOption()
{
<? if($purchaseSet['usePurchase'] == "Y" && $_GET['mode'] == "register") { ?>
	openLayer('objPurchaseOption');

	var pao = document.getElementsByName('purchaseApplyOption');

	if(document.getElementById('objPurchaseOption').style.display != "none") {
		if(pao[0].checked) { // ����ó ���� ����
			disabledStyle(document.fm.stock, "t"); // ��� X
			disabledStyle(document.fm.pchs_stock, "t"); // �԰� X
			disabledStyle(document.fm.pchsno, "f"); // ����ó O
			disabledStyle(document.fm.pchs_pchsdt, "f"); // ������ O
		}
		else if(pao[1].checked) { // ����ó ���� ����
			disabledStyle(document.fm.stock, "t"); // ��� X
			disabledStyle(document.fm.pchsno, "t"); // ����ó X
			disabledStyle(document.fm.pchs_stock, "t"); // �԰� X
			disabledStyle(document.fm.pchs_pchsdt, "t"); // ������ X
		}
		else {
			disabledStyle(document.fm.pchs_stock, "t"); // �԰� X
		}
	}
	else {
		// ���, ����ó, ���԰�, �԰�, ������ ��� ��� �����ϵ���
		disabledStyle(document.fm.stock, "f");
		disabledStyle(document.fm.pchsno, "f");
		disabledStyle(document.fm.supply, "f");
		disabledStyle(document.fm.pchs_stock, "f");
		disabledStyle(document.fm.pchs_pchsdt, "f");
	}

	if(pao[0].checked || pao[1].checked) {
		document.getElementById('objOption').style.display = document.getElementById('objPurchaseOption').style.display;
	}
<? } else { ?>
	document.fm.stock.disabled = !document.fm.stock.disabled;
	openLayer('objOption');
<? } ?>
}

function disabledStyle(obj, st) {
	switch(st) {
		case "t" :
			obj.disabled = true;
			obj.style.background = "#EEEEEE";
			break;
		case "f" :
			obj.disabled = false;
			obj.style.background = "#FFFFFF";
			break;
	}
}

function chkPurchaseOption(val) {
	var ar_stock = document.getElementsByName('option[stock][]');

	if(val == "1") { // ����ó ���� ����
		disabledStyle(document.fm.stock, "t"); // ��� X
		disabledStyle(document.fm.pchs_stock, "t"); // �԰� X
		disabledStyle(document.fm.pchsno, "f"); // ����ó O
		disabledStyle(document.fm.pchs_pchsdt, "f"); // ������ O
	}
	else { // ����ó ���� ����
		disabledStyle(document.fm.stock, "t"); // ��� X
		disabledStyle(document.fm.pchsno, "t"); // ����ó X
		disabledStyle(document.fm.pchs_stock, "t"); // �԰� X
		disabledStyle(document.fm.pchs_pchsdt, "t"); // ������ X
	}

	for(i = 0; i < ar_stock.length; i++) {
		if(val == "1") {
			ar_stock[i].value = "���";
			ar_stock[i].disabled = false;
		}
		else {
			ar_stock[i].value = "��� �� ��� �Է�";
			ar_stock[i].disabled = true;
		}
	}
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

function tabLongdescShow(btnObj){
	var btnId = btnObj.getAttribute('id');
	var btnObj_normal = document.getElementById('btn_longdesc_normal');
	var btnObj_mobile = document.getElementById('btn_longdesc_mobile');
	if(btnId=='btn_longdesc_normal'){
		document.getElementById('ta_longdesc').style.display='block';
		document.getElementById('ta_mlongdesc').style.display='none';
		btnObj_normal.style.backgroundColor='#999';
		btnObj_normal.style.color='#fff';
		btnObj_mobile.style.backgroundColor='#f0f0f0';
		btnObj_mobile.style.color='#000';
	}
	else if(btnId=='btn_longdesc_mobile'){
		document.getElementById('ta_longdesc').style.display='none';
		document.getElementById('ta_mlongdesc').style.display='block';
		btnObj_normal.style.backgroundColor='#f0f0f0';
		btnObj_normal.style.color='#000';
		btnObj_mobile.style.backgroundColor='#999';
		btnObj_mobile.style.color='#fff';
	}
}

// �ڵ���������
function chkImgCopy(fobj)
{
	var exist = false;
	for(var i=0; i < document.getElementsByName('img_l[]').length; i++)
	{
		if(document.getElementsByName('img_l[]')[i].value != ''){
			exist = true;
			break;
		}
		else if(document.getElementsByName('del[img_l]['+i+']')[0] != null && document.getElementsByName('del[img_l]['+i+']')[0].checked == false){
			exist = true;
			break;
		}
	}
	if(exist == false){
		alert('�����̹��� ���� ����ϼ���.');
		return false;
	}

	var limgTable = _ID('tb_l').parentNode.parentNode.parentNode.parentNode;
	if(fobj.copy_i.checked || fobj.copy_s.checked || fobj.copy_m.checked)
	{
		if(limgTable.style.outline != null)
			limgTable.style.outline = 'solid 5px #627DCE';
		else
			limgTable.style.border = 'solid 5px #627DCE';
	}
	else {
		if(limgTable.style.outline != null)
			limgTable.style.outline = 'none';
		else
			limgTable.style.border = 'solid 1px #EBEBEB';
	}
	for(var i=0; i < document.getElementsByName('img_m[]').length; i++)
		document.getElementsByName('img_m[]')[i].disabled = fobj.copy_m.checked;
	for(var i=0; i < document.getElementsByName('img_s[]').length; i++)
		document.getElementsByName('img_s[]')[i].disabled = fobj.copy_s.checked;
	for(var i=0; i < document.getElementsByName('img_i[]').length; i++)
		document.getElementsByName('img_i[]')[i].disabled = fobj.copy_i.checked;
	for(var i=0; i < document.getElementsByName('img_mobile[]').length; i++)
		document.getElementsByName('img_mobile[]')[i].disabled = fobj.copy_mobile.checked;
}
function chkImgBox(obj, fobj)
{
	fobj.copy_m.checked = obj.checked;
	fobj.copy_s.checked = obj.checked;
	fobj.copy_i.checked = obj.checked;
	var res = chkImgCopy(fobj);
	if (res === false){
		obj.checked = fobj.copy_m.checked = fobj.copy_s.checked = fobj.copy_i.checked = false;
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
function chkBlog() {
	var f=document.fm;
	if(!f.blog_cate_no.value) {
		alert("��α��� ����Ʈ �з��� �ʼ��Դϴ�");
		return false;
	}
	return true;
}

function chkSchPchs() {
	if(document.fm.pchsno.disabled == false) {
		window.open('../goods/popup.purchase_find.php', 'purchaseSearchPop', 'width=640,height=450');
	}
}
</script>

<table width=800 cellpadding=0 cellspacing=0>
<tr><td align=center><div id=goods_form><? include "../proc/warning_disk_msg.php"; # not_delete  ?></td></tr></table>


<form name=fm method=post action="indb.shopTouch_goods.php" enctype="multipart/form-data" onsubmit="return chkForm2(this)">
<input type=hidden name=mode value="<?=$_GET[mode]?>">
<input type=hidden name=goodsno value="<?=$goodsno?>">
<input type=hidden name=returnUrl value="<?=$returnUrl?>">

<? if ($goodsno) { ?>
<div style="padding:8px 13px;background:#f7f7f7;border:3px solid #C6C6C6;margin-bottom:18px;" id="goodsInfoBox">
	<div><font class=def>������ȣ:</font> <span style="color:#FF7200;font:bold 14px verdana"><?=$goodsno?></span></div>
</div>
<? } ?>
<!-- ��ǰ ī�װ� ���� -->
<div class="title title_top">��ǰ�з�����<span>�ѻ�ǰ�� �������� e���� �з��� ����� �� �ֽ��ϴ�&nbsp;(���ߺз��������)</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=shoppingapp&no=6')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<div class="box" style="padding-left:3">
<table width=790 cellpadding=0 cellspacing=1 border=1 bordercolor=#cccccc style="border-collapse:collapse">
<tr><td style="padding:7 7 7 10" bgcolor=f8f8f8>
<table width=100% cellpadding=0 cellspacing=1 id=objCategory>
<col><col width=50 style="padding-right:10"><col width=52 align=right>
<? if ($r_category){ foreach ($r_category as $k=>$v){ ?>
<tr>
	<td id=currPosition><?=strip_tags(currPosition($k))?></td>
	<td>
	<input type=text name=category[] value="<?=$k?>" style="display:none">
	<input type=text name=sort[] value="<?=-$v?>" class="sortBox right" maxlength=10 <?=$hidden[sort]?>>
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
	<script>new categoryBox('cate[]',4,'','multiple');</script>
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
<div class=noline><input type=checkbox name=sortTop><font class=small color=444444>üũ�� �̻�ǰ�� ���� ��ϵ� �ش� �� �з��������� �ֻ�ܿ� ���������մϴ�</font></div>
<div class=noline style="padding-left:3;padding-bottom:10px"><font color=627dce>��</font> <font class=extext>����: ��ǰ�з�(ī�װ�)�� ���� ��ϵǾ� �־�� ��ǰ����� �����մϴ�.</font> <a href="/shop/admin/goods/category.php" target=blank><font class=extext_l>[��ǰ�з�(ī�װ�) ����ϱ�]</font></a></div>
<div style="border-bottom:3px #efefef solid;padding-top:10px"></div>

<!-- ������ũ_ī�װ� -->
<div id="interpark_category"></div>
<div style="border-bottom:3px #efefef solid;padding-top:10px"></div>

<!-- ��ǰ�⺻���� -->
<div class=title>��ǰ�⺻����<span>������, ������, �귣�尡 ���� ��� �Է¾��ص� �˴ϴ� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=shoppingapp&no=6')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<table class=tb>
<col class=cellC><col class=cellL><col class=cellC><col class=cellL>
<tr>
	<td width=120 nowrap>��ǰ��</td>
	<td width=50%><div style="height:25;padding-top:5"><input type=text name=goodsnm style="width:100%" value="<?=$data[goodsnm]?>" required label="��ǰ��" class="line"></div><div style="height:23"><input type=checkbox name="meta_title" value="1" class=null <?=$checked[meta_title][1]?>>��ǰ���� ��ǰ���������� Ÿ��Ʋ �±׿� �Էµ˴ϴ�.</div></td>
	<td width=120 nowrap>��ǰ�ڵ�</td>
	<td width=50%><input type=text name=goodscd style="width:100%" value="<?=$data[goodscd]?>" class="line"></td>
</tr>
<tr>
	<td>������</td>
	<td>
	<input type=text name=maker value="<?=$data[maker]?>" class="line">
	<select onchange="this.form.maker.value=this.value;this.form.maker.focus()">
	<? foreach ($r_maker as $k=>$v){ ?><option value="<?=$k?>"><?=$v?><? } ?>
	</select>
	</td>
	<td>������</td>
	<td>
	<input type=text name=origin value="<?=$data[origin]?>" class="line">
	<select onchange="this.form.origin.value=this.value;this.form.origin.focus()">
	<? foreach ($r_origin as $k=>$v){ ?><option value="<?=$k?>"><?=$v?><? } ?>
	</select>
	</td>
</tr>
<tr>
	<td>�귣��</td>
	<td>
	<select name=brandno>
	<option value="">-- �귣�� ���� --
	<?
	$res = $db->query("select * from ".GD_GOODS_BRAND." order by sort");
	while ($tmp=$db->fetch($res)){
	?>
	<option value="<?=$tmp[sno]?>" <?=$selected[brandno][$tmp[sno]]?>><?=$tmp[brandnm]?>
	<? } ?>
	</select>
	<font class=small1 color=444444><a href="brand.php" target=_blank><font class=extext_l><img src="../img/btn_brand_add.gif"></a>
	</td>
	<td>�����</td>
	<td>
	<input type="text" name="launchdt" value="<?=$data[launchdt]?>" onclick="calendar()" onkeydown="onlynumber()" class="line"> <font class=ver71 color=627dce>ex) 20080321</font>
	<div style="padding-top:3px"><font class=extext>���̹� ���ļ��� ������ �α⵵(�������)�� �������� �߿��� ����Դϴ�</font></div>
	</td>
</tr>
<script>

	</script>
<tr>
	<td rowspan=3 >������</td>
	<td rowspan=3  class=noline>
	<table cellpadding=0 cellspacing=0>
	<col style="padding-right:6px" span=4>
	<tr>
	<?
		for($j=0;$j<$cnt_myicon;$j++){
			if( $j && $j % 4 == 0 ){ echo "</tr><tr>";}
			echo "<td><input type=checkbox name=icon[] value=".pow(2,$j)." ".$checked[icon][pow(2,$j)].">".$r_icon[$j]."</td>";
		}
	?>
	</tr>
	</table>
	<div style="padding:5px 0px 5px 5px"><font class=extext>�ٸ� ���������� ���� �ٲܼ� �ֽ��ϴ�</font> <a href="javascript:popup('popup.myicon.php',510,550)"><img src="../img/btn_icon_change.gif" align=absmiddle></a></div>


	</td>
	<td>��ǰ��¿���</td>
	<td class=noline><input type=checkbox name=open value=1 <?=$checked[open][1]?>>���̱�
	<font class=extext>(üũ������ ȭ�鿡�� �Ⱥ���)</font></td>
</tr>
<tr>
	<td>����ϼ� ��¿���</td>
	<td class=noline>
		<?php if($cfgMobileShop['vtype_goods']=='1'){?>
		<input type=checkbox name=open_mobile value=1 <?=$checked[open_mobile][1]?>>���̱�
		<font class=extext>(üũ������ ����ϼ� ȭ�鿡�� �Ⱥ���)</font>
		<?php }else{?>
		<input type=hidden name=open_mobile value="<?php echo $data['open'];?>" />
		<font class="red">��ǰ��¿��ο� �����ϰ� ����ǵ��� �����Ǿ��ֽ��ϴ�.</font>
		<?php }?>
	</td>
</tr>
<tr>
	<td>���θ� App ��¿���</td>
	<td class=noline>
		<input type=checkbox name=open_shoptouch value=1 <?=$checked['open_shoptouch'][1]?>>���̱�
		<font class=extext>(üũ������ ���θ� App ȭ�鿡�� �Ⱥ���)</font>
	</td>
</tr>
<tr>
	<td>����˻���</td>
	<td colspan=3>
	<div style='padding-top:5px'><input type=text name=keyword value="<?=$data[keyword]?>" style="width:100%" class="line"></div>
	<div style="height:23;padding-top:5px" class=extext>��ǰ�� �������� ��Ÿ�±׿� ��ǰ �˻��� Ű����� ����Ͻ� �� �ֽ��ϴ�.</font></div>
	</td>
</tr>
</table>
<div style="padding-top:20px"></div>
<div style="border-top:3px #efefef solid;"></div>
<!-- ��ǰ�߰����� -->
<div class=title>��ǰ�߰�����<span>��ǰƯ���� �°� �׸��� �߰��� �� �ֽ��ϴ� (��. ����, ����, ���ǻ�, �����, ��ǰ������ ��) <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=shoppingapp&no=6')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></span>
<a href="javascript:popup('http://guide.godo.co.kr/guide/php/ex_infoadd.html',650,610)"><img src="../img/icon_sample.gif" border="0" align=absmiddle hspace=2></a></div>
<div class=noline style="padding-bottom:5px">
<input type="radio" name="useEx" <?=$checked[useEx][1]?> onclick="openLayer('tbEx','block')" onfocus="blur()" value="1" /> ���
<input type="radio" name="useEx" <?=$checked[useEx][0]?> onclick="openLayer('tbEx','none')" onfocus="blur()" value="0" /> ������
</div>
<table id=tbEx class=tb style="display:<?=$display[useEx]?>">
<col class=cellC><col class=cellL><col class=cellC><col class=cellL>
<tr>
	<? for ($i=0;$i<6;$i++){ $ex = "ex".($i+1); ?>
	<td><input type=text name="title[]" class="exTitle gray" value="<?=$ex_title[$i]?>" onblur="if(!chkTitle())alert('�׸���� �ߺ��� �� �����ϴ�.')"></td>
	<td width=50%><input type=text name="ex[]" value="<?=$data[$ex]?>" style="width:100%"></td>
	<? if ($i%2){ ?></tr><tr><? } ?>
	<? } ?>
</tr>
</table>
<div style="border-bottom:3px #efefef solid;padding-top:20px"></div>

<!-- ��ǰ������ -->
<div class=title>������<span>�� ��ǰ �ֹ��� �����Ǵ� �������� �����մϴ�.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=shoppingapp&no=6')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<div class=noline style="padding-bottom:5px">
<div><input type=radio name="use_emoney" <?=$checked[use_emoney][0]?> value="0" onfocus=blur()> �����ݼ����� ��å�� �����մϴ�. <font class=extext>(�� ��ǰ�� �������� <a href="../basic/emoney.php" target="_blank"><font class=extext_l>[�⺻���� > �����ݼ��� > ��ǰ ������ ���޿� ���� ��å]</font></a> ���� ������ ��å�� �����ϴ�)</font></div>
<div><input type=radio name="use_emoney" <?=$checked[use_emoney][1]?> value="1" onfocus=blur()> �������� ���� �Է��մϴ�. <font class=extext>(�� ��ǰ�� �������� �ٷ� �Ʒ��� <b>����/���/��ۺ�</b>���� ����� ���������� �����մϴ�)</font></div>
</div>
<div style="border-bottom:3px #efefef solid;padding-top:20px"></div>

<!-- ����ó���� -->
<?
	if($purchaseSet['usePurchase'] == "Y" && $_GET['mode'] == "register") {
		if($goodsno) $pchsData = $db->fetch("SELECT * FROM ".GD_PURCHASE_GOODS." WHERE goodsno = '$goodsno' ORDER BY pchsdt DESC LIMIT 1");
?>
<div class=title>����ó ����</div>
<div style="height:5px;font:0"></div>
<table cellpadding="3" cellspacing="1" border="0" bgcolor="#E6E6E6" width="100%">
<tr>
	<td style="width:110px; height:32px; padding-left:10px; background:#F6F6F6; color:#333333; font-weight:bold;">����ó</td>
	<td style="width:250px; padding-left:10px; background:#FFFFFF; color:#333333;">
		<select name="pchsno" id="pchsno"<?=($_GET['mode'] == "modify") ? " disabled=\"true\"" : ""?>>
			<option value="">����ó����</option>
<?
	$sql_pchs = "SELECT * FROM ".GD_PURCHASE." ORDER BY comnm ASC";
	$rs_pchs = $db->query($sql_pchs);
	for($i = 0; $row_pchs = $db->fetch($rs_pchs); $i++) {
?>
			<option value="<?=$row_pchs['pchsno']?>"<?=($row_pchs['pchsno'] == $pchsData['pchsno']) ? "selected" : ""?>><?=$row_pchs['comnm']?></option>
<?
	}
?>
		</select>
		<a href="javascript:;" onclick="chkSchPchs()"><img src="../img/purchase_find.gif" title="����ó �˻�" align="absmiddle" /></a>
	</td>
	<td style="width:110px; height:32px; padding-left:10px; background:#F6F6F6; color:#333333; font-weight:bold;">������</td>
	<td style="padding-left:10px; background:#FFFFFF; color:#333333;"><input type=text name=pchs_pchsdt size=10 value="" onclick="calendar()" onkeydown="onlynumber()" class="line"<?=($_GET['mode'] == "modify") ? " disabled=\"true\"" : ""?>></td>
</tr>
</table>
<div style="padding:10px;">
<font class=extext>- ����ó ���� �� ���� �Ͻø� �ش� ����ó�� ���� �̷��� ���� �˴ϴ�.<br />
- �̹� ����ó ���� ������� ��ǰ��  ����� �� �ԡ����� ���� �� �� �� �̷��� ���� ���� �ʽ��ϴ�.<br />
* ����: �������� ���� �Ǿ� �־�� ��ǰ����� �����մϴ�.</font>
</div>
<div style="border-bottom:3px #efefef solid;padding-top:10px"></div>
<? } ?>

<!-- ��ǰ ����/��� -->
<div class=title>����/���/��ۺ�<span>������, ���� � ���� ������ �������� ��� ���ݿɼ��� �߰��� �� �ֽ��ϴ� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=shoppingapp&no=6')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<table>
<? if($purchaseSet['usePurchase'] == "Y" && $_GET['mode'] == "register") { ?>
<tr>
	<td>�ǸŰ�</td><td><input type=text name=price size=10 value="<?=$price?>" onchange="autoPrice(this)" onblur="autoPrice(this)" onkeydown="autoPrice(this)" class="line"></td>
	<td style="padding-left:10px">����</td><td><input type=text name=consumer size=10 value="<?=$consumer?>" onchange="autoPrice(this)" onblur="autoPrice(this)" onkeydown="autoPrice(this)" class="line"></td>
	<td style="padding-left:10px">������</td><td colspan="3"><input type=text name=reserve size=10 value="<?=$reserve?>" onchange="autoPrice(this)" onblur="autoPrice(this)" onkeydown="autoPrice(this)" class="line"></td>
</tr>
<tr>
	<td>���</td><td><input type=text name=stock size=10 value="<?=$stock?>" onchange="autoPrice(this)" onblur="autoPrice(this)" onkeydown="autoPrice(this)" class="line"></td>
	<td style="padding-left:10px">�԰�</td><td><input type=text name=pchs_stock size=10 value="" class="line"<?=($_GET['mode'] == "modify") ? " disabled=\"true\"" : ""?>></td>
	<td style="padding-left:10px">���԰�</td><td><input type=text name=supply size=10 value="<?=$supply?>" onchange="autoPrice(this)" onblur="autoPrice(this)" onkeydown="onlynumber();autoPrice(this)" class="line"></td>
</tr>
<? } else { ?>
<tr>
	<td>�ǸŰ�</td><td><input type=text name=price size=10 value="<?=$price?>" onchange="autoPrice(this)" onblur="autoPrice(this)" onkeydown="autoPrice(this)" class="line"></td>
	<td style="padding-left:10px">����</td><td><input type=text name=consumer size=10 value="<?=$consumer?>" onchange="autoPrice(this)" onblur="autoPrice(this)" onkeydown="autoPrice(this)" class="line"></td>
	<td style="padding-left:10px">���԰�</td><td><input type=text name=supply size=10 value="<?=$supply?>" onchange="autoPrice(this)" onblur="autoPrice(this)" onkeydown="autoPrice(this)" class="line"></td>
	<td style="padding-left:10px">������</td><td><input type=text name=reserve size=10 value="<?=$reserve?>" onchange="autoPrice(this)" onblur="autoPrice(this)" onkeydown="autoPrice(this)" class="line"></td>
	<td style="padding-left:10px">���</td><td><input type=text name=stock size=10 value="<?=$stock?>" onchange="autoPrice(this)" onblur="autoPrice(this)" onkeydown="autoPrice(this)" class="line"></td>
</tr>
<? } ?>
</table>

<div style="height:5px;font:0"></div>
<table class=tb>
<col class=cellC><col class=cellL><col class=cellC><col class=cellL>
<tr>
	<td width=120 nowrap>�������</td>
	<td width=50% class=noline><input type=checkbox name=usestock <?=$checked[usestock][o]?>> �ֹ��� �������
	<div style="padding-top:3px"><font class=extext>(üũ���ϸ� ��� ������� �������Ǹ�)</font></div></td>
	<td width=120 nowrap>ǰ����ǰ</td>
	<td width=50% class=noline><input type=checkbox name=runout value=1 <?=$checked[runout][1]?>> ǰ���� ��ǰ�Դϴ�</td>
</tr>
<tr>
	<td width=120 nowrap>���ż��� ����</td>
	<td>
	�ּұ��ż��� : <input type="text" name="min_ea" size=5 value="<?=$data['min_ea']?>"> &nbsp;
	�ִ뱸�ż��� : <input type="text" name="max_ea" size=5 value="<?=$data['max_ea']?>">(0�̸� ������ �����ϴ�)
	</td>
	<td width=120 nowrap>&nbsp;</td>
	<td width=50% class=noline>
	<input type=checkbox name=use_stocked_noti value=1 <?=$checked[use_stocked_noti][1]?>> ��ǰ ���԰� �˸� ���
	<div style="padding-top:3px"><font class=extext>(ǰ���Ǹ� ���������� ���԰� �˸���û ��ư�� ����˴ϴ�)</font></div></td>
	</td>
</tr>
<tr>
	<td>����/�����</td>
	<td class=noline>
	<input type=radio name=tax value=1 <?=$checked[tax][1]?>> ����
	<input type=radio name=tax value=0 <?=$checked[tax][0]?>> �����
	</td>
	<td>���ݴ�ü����</td>
	<td><input type=text name=strprice value="<?=$data[strprice]?>" class="line"></td>
</tr>
<script>
function chk_delivery_type(){
	var obj = document.getElementsByName('delivery_type');
	if(obj[2].checked == true) document.getElementById('gdi').style.display="inline";
	else document.getElementById('gdi').style.display="none";

	if(obj[3].checked == true) document.getElementById('gdi2').style.display="inline";
	else document.getElementById('gdi2').style.display="none";
}
</script>
<tr>
	<td>��ۺ�</td>
	<td colspan=3>
	<table cellspacing="0" cellpadding="0" border="0">
	<tr height=40>
		<td>
		<input type="radio" name="delivery_type" value="0" <?=$checked[delivery_type][0]?> class="null" onclick="chk_delivery_type();">�⺻�����å�� ����&nbsp;<input type="radio" name="delivery_type" value="1" <?=$checked[delivery_type][1]?> class="null" onclick="chk_delivery_type();"> ������
		&nbsp;<input type="radio" name="delivery_type" value="2" <?=$checked[delivery_type][2]?> class="null" onclick="chk_delivery_type();">��ǰ�� ��ۺ� �Է�
		<span style="display:none;" id="gdi">&nbsp;<input type="text" name="goods_delivery" value="<?=$data['goods_delivery']?>" size="8" onkeydown="onlynumber()">��</span>
		&nbsp;<input type="radio" name="delivery_type" value="3" <?=$checked[delivery_type][3]?> class="null" onclick="chk_delivery_type();">���ҹ�ۺ�
		<span style="display:none;" id="gdi2">&nbsp;<input type="text" name="goods_delivery2" value="<?=$data['goods_delivery']?>" size="8" onkeydown="onlynumber()">��</span></td>
	</tr>
	</table>
	<div><font class=extext>�⺻�����å�� ��ǰ�� ��ۺ� ��å�� <a href="../basic/delivery.php" target=_blank><font class=extext_l>[�⺻���� > ���/�ù�� ����]</font></a> ���� ���� �Ͻ� �� �ֽ��ϴ�.</font></div>
	</td>
</tr>
</table>

<div style="padding: 10px 10px 10px 0px"><a href="javascript:vOption()" onfocus=blur()><img src="../img/btn_priceopt_add.gif" align=absmiddle></a> <font class=small color=444444>�� ��ǰ�� �ɼ��� �������ΰ�� ����ϼ��� (����, ������ ��)</font>
<a href="javascript:popup('http://guide.godo.co.kr/guide/php/ex_infoprice.html',730,700)"><img src="../img/icon_sample.gif" border="0" align=absmiddle></a></div>

<div id="objPurchaseOption" style="display:none;"><table cellpadding="4" cellspacing="0" border="0" style="width:500px; border:1px #DDDDDD solid;margin:10px 0px;">
<tr>
	<td>
		<input type="radio" id="purchaseAllApply" name="purchaseApplyOption" style="border:0px;" value="1" onclick="document.getElementById('objOption').style.display = 'block';chkPurchaseOption(this.value);" /><label for="purchaseAllApply">����ó ���� ����</label> <span class="extext">�߰��ɼ��� ������ ����ó���� �԰� �� ���</span>
	</td>
</tr>
<tr>
	<td>
		<input type="radio" id="purchaseEachApply" name="purchaseApplyOption" style="border:0px;" value="2" onclick="document.getElementById('objOption').style.display = 'block';chkPurchaseOption(this.value);" /><label for="purchaseEachApply">����ó ���� ����</label> <span class="extext">�߰��ɼ��� ������ ����ó���� �԰� �� ���</span>
	</td>
</tr>
</table></div>

<div id=objOption style="display:none">
<div style="padding-bottom:10">
<font class=small color=black><b>�ɼǸ�1</b> : <input type=text name=optnm[] value="<?=$optnm[0]?>">
<a href="javascript:addopt1()" onfocus=blur()><img src="../img/i_add.gif" align=absmiddle></a> <a href="javascript:delopt1()" onfocus=blur()><img src="../img/i_del.gif" align=absmiddle></a><span style="width:20"></span>
<b>�ɼǸ�2</b></font> : <input type=text name=optnm[] value="<?=$optnm[1]?>">
<a href="javascript:addopt2()" onfocus=blur()><img src="../img/i_add.gif" align=absmiddle></a> <a href="javascript:delopt2()" onfocus=blur()><img src="../img/i_del.gif" align=absmiddle></a><span style="width:20"></span>
<span class=noline><b>�ɼ���¹��</b> :
<input type=radio name=opttype value="single" <?=$checked[opttype][single]?>> ��ü��
<input type=radio name=opttype value="double" <?=$checked[opttype][double]?>> �и���
</span>
</div>
<?if(count($opt)>1 || $opt1[0] != null || $opt2[0] != null){?><script>vOption();</script><?}?>
<div style="margin:10px 0"><font class=extext>����� �ɼǸ�1�� �ɼǸ�2�� ����Ŭ���Ͻÿ� �ɼ��� �����Ͻ� �� �ֽ��ϴ�.</font></div>
<table id=tbOption border=1 bordercolor=#cccccc style="border-collapse:collapse">
<tr align=center>
	<td width=116></td>
	<td><span style="color:#333333;font-weight:bold;">�ǸŰ�</span></td>
	<td><span style="color:#333333;font-weight:bold;">����</span></td>
	<td><span style="color:#333333;font-weight:bold;">���԰�</span></td>
	<td><span style="color:#333333;font-weight:bold;">������</span></td>
	<?
		$j=4;
		foreach ($opt2 as $v){
		$j++;
	?>
	<td id='tdid_<?=$j?>'><input type="text" name="opt2[]" <? if($v != '') { ?>class=fldtitle value="<?=$v?>"<? } else { ?>class="opt gray" value='�ɼǸ�2'<? } ?> ondblclick="delopt2part('tdid_<?=$j?>')" onclick="chkOptName(this)" onblur="chkOptNameOver(this)"></td>
	<? } ?>
</tr>
	<?
	$i=0;
	$op2=$opt2[0]; foreach ($opt1 as $op1){
	$i++;
	?>
<tr id="trid_<?=$i?>">
	<td width=116 nowrap><input type=text name=opt1[] <?if($op1 != ''){?>class=fldtitle value="<?=$op1?>"<?}else{?>class="opt gray" value='�ɼǸ�1'<?}?> <?if($i != 1){?>ondblclick="delopt1part('trid_<?=$i?>')"<?}?> onclick="chkOptName(this)" onblur="chkOptNameOver(this)"></td>
	<td><input type=text name=option[price][] class="opt gray" value="<?=$opt[$op1][$op2][price]?>"></td>
	<td><input type=text name=option[consumer][] class="opt gray" value="<?=$opt[$op1][$op2][consumer]?>"></td>
	<td><input type=text name=option[supply][] class="opt gray" value="<?=$opt[$op1][$op2][supply]?>"></td>
	<td><input type=text name=option[reserve][] class="opt gray" value="<?=$opt[$op1][$op2][reserve]?>"></td>
	<? foreach ($opt2 as $op2){ ?>
	<td><input type=text name=option[stock][] <?if($opt[$op1][$op2][stock]){?>class="opt" value="<?=$opt[$op1][$op2][stock]?>"<?}else{?>class="opt gray" value="���"<?}?> onclick="chkOptName(this)" onblur="chkOptNameOver(this)"><input type=hidden name=option[optno][] value="<?=$opt[$op1][$op2][optno]?>"></td>
	<? } ?>
</tr>
<? } ?>
</table>
<div style="padding-top:10px">
	<select name="dopt" style="width:125">
		<option value=''>�ɼǹٱ��� ����</option>
		<?
		$query = "select * from ".GD_DOPT." order by sno desc";
		$res = $db->query($query);
		while($rdopt = $db ->fetch($res)){
			$l = strlen($rdopt[title]);
			if($l > 20){
				$rdopt[title] = strcut($rdopt[title],20);
			}
		?>
		<option value='<?=$rdopt[sno]?>'><?=$rdopt[title]?></option>
		<?}?>
	</select>&nbsp;&nbsp;<a href="javascript:applydopt()"><img src="../img/btn_optionbasket.gif" border="0" align="absmiddle"></a>
	<a href="javascript:popupLayer('popup.dopt_list.php',800,600)"><img src="../img/btn_optionbasket_admin.gif" border="0" align="absmiddle"></a>
</div>

<div style="padding:10px 0;">
<span style="color:#627dce;">&#149;</span> <span class="extext">�ɼǸ� 1 : �ɼǸ�1�� �ɼǺ� ��ǰ�� ���� ���̰� �ִ� ��� �Է��ϴ� ���� �Դϴ�.<br/>
<span style="color:#fff;">__________</span> ex) ���� �������� �ְ� ����� ���� ������ ������ �ִ� ��� �ɼǸ� 1�� ������ �Է��ϰ� �ɼǸ� 2�� ����� �Է��ؾ� �մϴ�.<br/></span>
<span style="color:#627dce;">&#149;</span> <span class="extext">�ɼǸ� 2 : �ɼʸ� 2�� �ɼǸ�1�� ���� �ɼ� ������ �Է��ؾ� �մϴ�.<br/>
<span style="color:#fff;">__________</span> ex) �ɼǸ� 1: ���� �ɼǸ� 2: ��, ��, �� �� ������ ��ǰ�� ����� ��,��,�Ұ� ������ �ǹ��մϴ�.</span>
</div>

<?include "../goods/_form.fashion.php";?>
<p />
</div>
<div style="border-bottom:3px #efefef solid;padding-top:20px"></div>

<!-- �߰��ɼ� -->
<div class=title>�߰��ɼ�/�߰���ǰ/����ǰ<span>�߰��ɼ��� ������ ����� �� ������, �߰���ǰ�� �Ǹ��ϰų� ����ǰ�� ������ ���� �ֽ��ϴ� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=shoppingapp&no=6')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<div class=noline style="padding-bottom:5px">
<input type="radio" name="useAdd" <?=$checked[useAdd][1]?> onclick="openLayer('tbAddWrap','block')" onfocus="blur()" value="1" /> ���
<input type="radio" name="useAdd" <?=$checked[useAdd][0]?> onclick="openLayer('tbAddWrap','none')" onfocus="blur()" value="0" /> ������
</div>

<a href="javascript:add_addopt()"><img src="../img/i_addoption.gif" align=absmiddle></a>
<a href="javascript:del_addopt()"><img src="../img/i_deloption.gif" align=absmiddle></a>
<span class=small1 style="padding-left:5px">(�ɼǸ� �ƹ� ���뵵 �Է����� ������ �ش� �ɼ��� ����ó���˴ϴ�)</span>

<div style="height:7px"></div>

<div id="tbAddWrap" style="display:<?=$display[useAdd]?>">
<table id=tbAdd  border=2 bordercolor=#cccccc style="border-collapse:collapse">
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
		<td><input type=hidden name=addopt[sno][<?=$k?>][] value="<?=$v2[sno]?>"><input type=text name=addopt[opt][<?=$k?>][] value="<?=$v2[opt]?>" style="width:270px"> ���ý�</td>
		<td>�Ǹűݾ׿� <input type=text name=addopt[addprice][<?=$k?>][]  size=9 value="<?=$v2[addprice]?>"> �� �߰�</td>
	</tr>
	<? } ?>
	</table>

	</td>
	<td class=noline align=center><input type=checkbox name=addoptreq[<?=$k?>] value="o" <?=$checked[addoptreq][$k]?>></td>
</tr>
<? } ?>
</table>

<?

	/**
		2011-01-12 by x-ta-c
		�߰� �ɼ� ������ ����.
	 */
	$arDoptExtend = array();
	$query = "select * from ".GD_DOPT_EXTEND." order by sno desc";
	$res = $db->query($query);
	while($rdopt = $db ->fetch($res)){
		$l = strlen($rdopt[title]);

		if($l > 20){
			$rdopt[title] = strcut($rdopt[title],20);
		}

		$rdopt[option] = !empty($rdopt[option]) ? unserialize($rdopt[option]) : $_tmp;
		$rdopt[option] = str_replace("\n","",gd_json_encode($rdopt[option]));	// php4 ȯ���̹Ƿ� �ӽ� �Լ� �߰� �Ͽ���.

		$arDoptExtend[] = $rdopt;
	}
	?>

	<div style="padding-top:10px">
		<select name="dopt_extend" style="width:125">
			<option value=''>�ɼǹٱ��� ����</option>
			<? foreach ($arDoptExtend as $k => $val) { ?>
			<option value='<?=$val[sno]?>'><?=$val[title]?></option>
			<? } ?>
		</select>&nbsp;&nbsp;<a href="javascript:fnApplyDoptExtendData()"><img src="../img/btn_optionbasket.gif" border="0" align="absmiddle"></a>
		<a href="javascript:popupLayer('popup.dopt_extend_list.php',850,600);"><img src="../img/btn_optionbasket_admin.gif" border="0" align="absmiddle"></a>
	</div>

	<div style="padding-top:10px"><span style="color:#627dce">��</span> <span class="extext">�߰��ɼ�/�߰���ǰ/����ǰ���� ������ ��ǰ�� ���� �� ȸ�������� ������� �ʽ��ϴ�</span></div>
</div>

<script type="text/javascript">
var json_dopt_extend_data = new Array;
<? foreach ($arDoptExtend as $sno => $val) { ?>
json_dopt_extend_data[<?=$val[sno]?>] = <?=$val[option]?>;
<? } ?>

function fnReloadDoptExtendData() {

	new Ajax.Request('./ax_dopt_extend_loader.php', {
		method:'post',
		onSuccess: function(transport){

			json_dopt_extend_data = new Array;

			// ����Ʈ �ڽ� �ɼ� ����
			var opt, sel = document.fm.dopt_extend;

			while (sel.length > 1)
			{
				sel.remove( sel.length - 1 );
			}

			var data = eval(transport.responseText);

			for (i=0;i<data.length ;i++ )
			{
				json_dopt_extend_data[ data[i].sno ] = eval(data[i].option);
				opt = document.createElement('option');
				opt.text = data[i].title;
				opt.value =  data[i].sno;

				sel.options.add(opt, sel.length + 1 );
			}

		},
		onFailure: function(){
			// alert('���ο� �ɼ��� ���ΰ�ħ �ϼž� �ݿ��˴ϴ�.');

		}
	});




}

function fnApplyDoptExtendData() {
	var key = document.fm.dopt_extend.value;

	if (key)
	{
		//
		var
			addoptnm = document.getElementsByName('addoptnm[]'),
			addopt_opt,addopt_price,
			opt_data = json_dopt_extend_data[key],
			opt_data_size = opt_data.length,
			data,items,items_length,
			i,j;

		// �ɼ� ������ŭ.
		for (i=0;i<opt_data_size ;i++)
		{
			data = opt_data[i];

			// �ɼ� ���� ���ڸ��� �߰�.
			if (opt_data_size > addoptnm.length) add_addopt();
			else if (opt_data_size < addoptnm.length) del_addopt();

			// �� �Է�.
			addoptnm[i].value = data.name;					// �ɼǸ�
			document.getElementsByName('addoptreq['+i+']')[0].checked =  (data.require == true) ? true : false;					// �ɼǺ� ���Ž� �ʼ� ����

			// �ɼ� �׸�
			items = data.options;
			items_length = items.length;
			addopt_opt = document.getElementsByName('addopt[opt]['+i+'][]');
			addopt_price = document.getElementsByName('addopt[addprice]['+i+'][]');

			// �ɼ��� �׸� ������ŭ.
			for (j=0;j < items_length ;j++) {

				// �׸� ���� ���ڸ��� �߰�.
				if (items_length > addopt_opt.length) add_subadd(addoptnm[i]);
				else if (items_length < addopt_opt.length) {
					var rpt = addopt_opt.length - items_length;
					for (k=0;k<rpt ; k++) del_subadd(addoptnm[i]);
				}

				// �� �Է�.
				addopt_opt[j].value = items[j].name;		// �׸��
				addopt_price[j].value = items[j].price;		// �׸� �߰��ݾ�

			} // for--

		} // for--

		//
	}

}

var nsRelatedGoods = function() {

	function popup(url,w_width,w_height,scroll) {

		popupLayer(url, w_width, w_height);return;
		return;

		var x = (screen.availWidth - w_width) / 2;
		var y = (screen.availHeight - w_height) / 2;
		var sc = "scrollbars=yes";
		return window.open(url,"","width="+w_width+",height="+w_height+",top="+y+",left="+x+","+sc);

	}

	return {
		relation : <?=!empty($r_relation) ? gd_json_encode($r_relation) : '[]'?>,
		goodsno : '<?=$data[goodsno]?>',
		register : function() {
			popup('./popup.related.register.php?goodsno=' + this.goodsno,750,600);
		}
		,
		list : function() {

			if (this.relation.size() > 0)
			{
				var el = $('el-related-goodslist');
				var i=0;
				var _row = new Template('\
											<tr align="center">\
												<td class="noline"><input type="checkbox" name="related_chk[]" value="#{goodsno}"></td>\
												<td><a href="javascript:void(0);" onClick="nsRelatedGoods.changetype();"><img src="../img/icn_#{type}.gif"></a></td>\
												<td><a href="../../goods/goods_view.php?goodsno=#{goodsno}" target=_blank>#{img}</a></td>\
												<td align="left">\
													#{goodsnm}\
													<p style="margin:0;"><b>#{price}</b></p>\
													#{runout}\
												</td>\
												<td>#{range}</td>\
												<td>#{r_regdt}</td>\
												<td><a href="javascript:void(0);" onClick="nsRelatedGoods.del();"><img src="../img/btn_delete_new.gif"></a></td>\
											</tr>\
											');

				$A(el.down('tbody').rows).each(function(tr){
					if (i > 0) Element.remove(tr);
					i++;
				});

				var r;

				for (i=0,m=this.relation.size();i<m ;i++ ) {
					r = this.relation[i];

					// ������ ����
					r.type = r.r_type == 'couple' ? '1' : '0';
					r.img = '<img src="../../data/goods/' + r.img_s + '" width=40 />';
					r.runout = r.runout == 1 ? '<div style="padding-top:3px"><img src="../../data/skin/<?=$cfg[tplSkin]?>/img/icon/good_icon_soldout.gif"></div>' : '';
					r.price = comma(r.price);
					r.range = '';

					if (!r.r_start && !r.r_end) r.range = '���ӳ���';
					else {
						if (r.r_start) r.range = r.r_start;
						r.range += ' ~ ';
						if (r.r_end) r.range += r.r_end;
					}

					// ����
					el.down('tbody').insert({ bottom: _row.evaluate(r) });
				}

				$('el-related-goods-count').update( this.relation.size() );
			}



		}
		,
		undo : function() {
			this.relation = <?=!empty($r_relation) ? gd_json_encode($r_relation) : '[]'?>;
			this.list();
		}
		,
		range : function() {

			var chks = $$('input[name="related_chk[]"]:checked');

			if (chks.size() < 1) {
				alert('�Ⱓ ������ ���û�ǰ�� ������ �ּ���.');
				return false;
			}

			var param = 'goodsno=' + this.goodsno;

			chks.each(function(chk){
				param+= '&chk[]='+chk.value;
			});

			popup('./popup.related.range.php?' + param,380,230);
		}
		,
		isExist : function(data) {

			for (var i=0, m=this.relation.size();i<m ;i++ ) {
				if (data.goodsno == this.relation[i].goodsno) return true;
			}

			return false;

		}
		,
		add : function(data) {

			var noti = false;

			if (data.length > 0) {

				for (var i=0,m=data.length;i<m ;i++ ) {
					if (! this.isExist(data[i])) {
						this.relation.push(data[i]);
						noti = true;
					}
				}
			}

			if (noti) alert('�߰��Ǿ����ϴ�.');

			this.list();
		}
		,
		set : function(data) {
			var noti = false;

			if (data.length > 0) {

				for (var i=0, m=this.relation.size();i<m ;i++ ) {

					for (var j=0,n=data.length;j<n ;j++ ) {

						if (this.relation[i].goodsno == data[j].goodsno) Object.extend(this.relation[i],data[j]);

					}
				}
			}
			this.list();

		}
		,
		del : function(act) {

			if (act == 'multi')
				var chks = $$('input[name="related_chk[]"]:checked');
			else {
				var tr = Element.up(event.srcElement,'tr');
				var chks = Selector.findChildElements(tr , ['input[name="related_chk[]"]'] );
			}


			for (var j=0,n=chks.size();j<n ;j++ ) {
				for (var i=0, m=this.relation.size();i<m ;i++ ) {
					if (this.relation[i].goodsno == chks[j].value) {
						this.relation[i] = {};
						chks[j].up(1).remove();
					}
				}
			}
			$('el-related-goods-count').update( parseInt($('el-related-goods-count').innerText) - n );
		}
		,
		changetype : function(act,typ) {

			var img;

			if (act == 'multi') {
				var chks = $$('input[name="related_chk[]"]:checked');
			}
			else {
				var tr = Element.up(event.srcElement,'tr');
				var chks = Selector.findChildElements(tr , ['input[name="related_chk[]"]'] );

			}

			for (var j=0,n=chks.size();j<n ;j++ ) {
				for (var i=0, m=this.relation.size();i<m ;i++ ) {
					if (this.relation[i].goodsno == chks[j].value) {
						if (typ) this.relation[i].r_type = typ;
						else this.relation[i].r_type = (this.relation[i].r_type == 'couple') ? 'single' : 'couple';
						img = Selector.findChildElements(chks[j].up(1) , ['img[src*="/img/icn_"]']);
						img[0].src = '../img/icn_'+ (this.relation[i].r_type == 'couple' ? '1' : '0') +'.gif';
					}
				}
			}
		}
		,
		select : function() {
			var i=0;
			var b_checked = false;
			$$('input[name="related_chk[]"]').each(function(chk){
				if (i == 0) b_checked = !chk.checked;
				chk.checked = b_checked;
				i++;
			});
		}
		,
		make : function() {

			var json = Object.toJSON(this.relation);

			$('el-relation').setValue(json);

		}
	}
}();

</script>

<div style="border-bottom:3px #efefef solid;padding-top:20px"></div>

<!-- ���û�ǰ -->
<div class=title>���û�ǰ<span>�̻�ǰ�� �����ִ� ��ǰ�� ��õ�ϼ��� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=shoppingapp&no=6')"><img src="../img/icon_sample.gif" border="0" align=absmiddle></a></div>

<table class=tb>
	<col class=cellC width="120"><col class=cellL>
	<tr>
		<td>���û�ǰ ������</td>
		<td class="noline">
			<label><input type=radio name=relationis value=0 onfocus=blur() onclick="openLayer('divRefer','none');" <?=$checked[relationis][0]?>>�ڵ� <font class=small color=#5A5A5A>(���� �з� ��ǰ�� �������� ������)</font></label>
			<label><input type=radio name=relationis value=1 onfocus=blur() onclick="openLayer('divRefer','block');" <?=$checked[relationis][1]?>>���� <font class=small color=#5A5A5A>(�Ʒ� ���� ���õ��)</font></label>
		</td>
	</tr>

	</table>

	<div id=divRefer style="display:<?=$display[relationis]?>;margin-top:10px;">
	<input type="hidden" name="relation" id="el-relation" value="">

	<p style="margin:0 0 5px 0;">
		���� ���û�ǰ : <span id="el-related-goods-count"><?=sizeof($r_relation)?></span> ��
		<a href="javascript:void(0);" onClick="nsRelatedGoods.register();"><img src="../img/btn_goods_check.gif" align="absmiddle"></a>
		<a href="javascript:void(0);" onClick="nsRelatedGoods.undo();"><img src="../img/btn_reset.gif" align="absmiddle"></a>
	</p>

	<table border="1" id="el-related-goodslist" bordercolor=#cccccc style="border-collapse:collapse" width="750">
	<col width="40">
	<col width="55">
	<col width="40">
	<col width="">
	<col width="130">
	<col width="130">
	<col width="40">
	<tr height="25">
		<th><a href="javascript:void(0);" onClick="nsRelatedGoods.select();">����</a></th>
		<th>���ε��</th>
		<th></th>
		<th>��ϵ� ���û�ǰ</th>
		<th>���û�ǰ �����Ⱓ</th>
		<th>�����</th>
		<th>����</th>
	</tr>
	<? if ($r_relation){ foreach ($r_relation as $v){ ?>
	<tr align="center">
		<td class="noline"><input type="checkbox" name="related_chk[]" value="<?=$v[goodsno]?>"></td>
		<td><a href="javascript:void(0);" onClick="nsRelatedGoods.changetype();"><img src="../img/icn_<?=$v[r_type] == 'couple' ? '1' : '0'?>.gif"></a></td>
		<td><a href="../../goods/goods_view.php?goodsno=<?=$v[goodsno]?>" target=_blank><?=goodsimg($v[img_s],40,'',1)?></a></td>
		<td align="left">
			<?=$v[goodsnm]?>
			<p style="margin:0;"><b><?=number_format($v[price])?></b></p>
			<? if ($v[runout]){ ?><div style="padding-top:3px"><img src="../../data/skin/<?=$cfg[tplSkin]?>/img/icon/good_icon_soldout.gif"></div><? } ?>
		</td>
		<td>
			<?
			if (!$v[r_start] && !$v[r_end]) echo '���ӳ���';
			else {
				if ($v[r_start]) echo $v[r_start];
				echo ' ~ ';
				if ($v[r_end]) echo $v[r_end];
			}
			?>
		</td>
		<td><?=$v[r_regdt]?></td>
		<td><a href="javascript:void(0);" onClick="nsRelatedGoods.del();"><img src="../img/btn_delete_new.gif"></a></td>
	</tr>
	<? }} ?>
	</table>

	<table border="0" width="750">
	<tr>
		<td align="left">
			<a href="javascript:void(0);" onClick="nsRelatedGoods.changetype('multi','couple');"><img src="../img/btn_yes.gif"></a>
			<a href="javascript:void(0);" onClick="nsRelatedGoods.changetype('multi','single');"><img src="../img/btn_no.gif"></a>
		</td>
		<td align="right">
			<a href="javascript:void(0);" onClick="nsRelatedGoods.del('multi');"><img src="../img/btn_select_delete.gif"></a>
			<a href="javascript:void(0);" onClick="nsRelatedGoods.range();"><img src="../img/btn_dayset.gif"></a>
		</td>
	</tr>
	</table>

	<p class="extext">
		�� ���ε��<br>
		- <img src="../img/icn_1.gif" align="absmiddle"> : �� ��ǰ�� ���ε�� ��ǰ�� ���û�ǰ���� ���ÿ� ��ϵ˴ϴ�. ������ ���ʸ�� �ڵ����� ���û�ǰ ��Ͽ��� ���ܵ˴ϴ�. <br>
		- <img src="../img/icn_0.gif" align="absmiddle"> : �� ��ǰ�� ���û�ǰ���� ���ε�� ���� ������, �� ��ǰ�� ���û�ǰ ��Ͽ��� ��ϵ˴ϴ�. <br>

		�� ���û�ǰ �������� ������ ����ǰ���� > ���û�ǰ ���� ������ ���� �Ͻ� �� �ֽ��ϴ�.  <a href="../goods/related.php" target=_blank><font class=extext_l>[���û�ǰ ���� ����]</font></a> �ٷΰ���
	</p>

	</div>

<div style="border-bottom:3px #efefef solid;padding-top:20px"></div>
<!--  qr code ���� -->
<? if($qrCfg['useGoods'] == "y"){ ?>
<div class=title>QR Code ����<span>��ǰ �󼼺��⿡ QR Code �� �����ݴϴ�. <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=shoppingapp&no=6')"><img src="../img/icon_sample.gif" border="0" align=absmiddle></a></div>
<div style="padding-bottom:5px" class=noline>
<input type=radio name=qrcode value=y onfocus=blur()  <?=$checked['qrcode']['y']?>>���
<input type=radio name=qrcode value=n onfocus=blur()  <?=$checked['qrcode']['n']?>>������
<?
		if($data['qrcode'] == 'y'){
			require "../../lib/qrcode.class.php";
			$QRCode = Core::loader('QRCode');
			echo  $QRCode->get_GoodsViewTag($goodsno, "goods_down");
		}
?>
</div>
<!-- qr code ���� -->
<div style="border-bottom:3px #efefef solid;padding-top:20px"></div>
<? } ?>
<!-- ��ǰ �����Ϻ� -->
<div class=title>��ǰ�̹��� ������ ȿ��<span>��ǰ���̹����� ���콺�� �����Ͽ� ��ǰ�̹����� Ȯ���Ͽ� �� �� �ִ� ����Դϴ�. <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=shoppingapp&no=6')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></span></div>
<div style="padding-bottom:5px" class=noline>
	<label><input type="radio" name="detailView" value="y" <?=$checked['detailView']['y']?> onclick="document.getElementById('detailViewCmt').style.display='block';" />���</label>
	<label><input type="radio" name="detailView" value="n" <?=$checked['detailView']['n']?> onclick="document.getElementById('detailViewCmt').style.display='none';" />������</label>
</div>
<div id='detailViewCmt' style="width:660px;border:solid 1px #cccccc; margin-bottom:5px; <? if($data['detailView']=='n') {?>display:none;<?}?>">
	<div style="margin:1px; background-color:#f8f8f8; padding:7px 10px; line-height:1.3em;">
		<div style="margin-bottom:2px">
		<div>��	<font class="small1" color="#444444">[��ǰ�̹��� ������ ȿ��] ����� ����ϱ� ���ؼ���, �Ʒ� ��ǰ�̹��� ��Ͻ� <font color="#FF0000">���̹���</font>�� ū �������� �̹����� �־�� �մϴ�.<br />
		: ���̹����� ���콺 �����ÿ� ��Ÿ���� Ȯ���̹����� �Է��ؾ� �մϴ�. 500px~800px ������ �̹����� �����մϴ�.</font></div>
		<div>�� <font class="small1" color="#444444">���̹��� �Է¶��� �̹����� ������ �ڵ����� ���̹����� ���콺 ������ ���̴� ū �̹����� ��ϵ˴ϴ�.</font></div>
		<div>�� <font class="small1" color="#444444">Ȯ��(����)�̹��� �Է¶��� �̹����� �ְ� [�ڵ��������� ���] ����� �̿��Ͽ� ���̹����� ����Ͻø�, [��ǰ�̹��� ������ ȿ��]
		����� ����� �Ұ��� �մϴ�. ��, ���̹����� ���� ����ϼž� �մϴ�.</font></div>
		</div>
	</div>
</div>
<div style="border-bottom:3px #efefef solid;padding-top:20px"></div>
<!-- ��ǰ �����Ϻ� -->

<!-- ��ǰ �̹��� -->
<div class=title>���θ� App ��ǰ �̹���<span>���θ� App ���� ��ǰ�̹��� �Դϴ�. �����Ͻ÷��� �ݵ�� ������ üũ �Ͻð� ������ �ٽ� �÷��ּ���.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=shoppingapp&no=6')"><img src="../img/icon_sample.gif" border="0" align=absmiddle></a></div>
<!-- ���� �̳��� �̹����� �������� �ʱ� ���� -->
<input type="hidden" name="img_i" value="<?=$data['img_i']?>" />
<input type="hidden" name="img_s" value="<?=$data['img_s']?>" />
<input type="hidden" name="img_m" value="<?=$data['img_m']?>" />
<input type="hidden" name="img_l" value="<?=$data['img_l']?>" />
<input type="hidden" name="img_mobile" value="<?=$data['img_mobile']?>" />

<table class=tb>
<col class=cellC><col class=cellL><col class=cellC><col class=cellL>
<? foreach ($imgs as $k=>$v){ ?>
<tr>
	<td>
	<?=$str_img[$k]?>
	</td>
	<td>

	<table id="tb_<?=$k?>">
	<col valign=top span=2>
	<? for ($i=0;$i<count($v);$i++){ ?>
	<tr>
		<td>
		<a href="javascript:addfldShoptouch('tb_<?=$k?>')"><img src="../img/i_add.gif" align=absmiddle></a>
		<span><input type=file name=img_<?=$k?>[] style="width:300px" onChange="preview(this)"></span>
		</td>
		<td>
		<? if ($v[$i]){ ?>
		<div style="padding:0 0" class=noline><input type=checkbox name=del[img_<?=$k?>][<?=$i?>]><font class=small color=#585858>����</font></div>
		<input type="hidden" name="img_<?=$k?>_old[]" value="<?=$v[$i]?>" />
		<? } ?>
		</td>
		<td>
		<img src='<?=$v[$i]?>' width=20 style='border:1 solid #cccccc' onclick="popupImg('<?=$v[$i]?>', '../');" class=hand onerror=this.style.display='none' />
		</td>
	</tr>
	<? } ?>
<? } ?>
	</table>

	</td>
</tr>
</table>

</div>

<div style="border-bottom:3px #efefef solid;padding-top:30px"></div>

<!-- ��ǰ ���� -->
<div class=title>��ǰ ���� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=shoppingapp&no=6')"><img src="../img/icon_sample.gif" border="0" align=absmiddle></a>  <font class=small1 color=444444>�Ʒ� <img src="../img/up_img.gif" border=0 align=absmiddle hspace=2>�� ���� �̹����� ����ϼ���.</font> &nbsp;<font color=E6008D>��</font><font class=small1 color=444444><font color=E6008D> ��� �̹��������� �ܺθ�ũ (����, G���� ���� ���¸��� ����)</font>�� �������� �ʽ��ϴ�.</div>

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
	<textarea name=shortdesc style="width:100%;height:20px;overflow:visible" class=tline><?=$data[shortdesc]?></textarea>
	</td>
</tr>
</table>
<div style="height:6px;font:0"></div>

<div class="noline" style="padding:5px 0 0 5px;border-bottom:3px solid #999;">
	<a name="tabLongdesc"></a>
	<input type="button" id="btn_longdesc_shoptouch" value="���θ� App�󼼼���" style="width:85px;height:25px;cursor:hand;background-color:#999;color:#fff;" onclick="tabLongdescShow(this);" />
</div>

<div id="ta_slongdesc"><textarea name=slongdesc style="width:100%;height:400px" type=editor><?=$data[slongdesc]?></textarea></div>
<div style="border-bottom:3px #efefef solid;padding-top:20px"></div>


<!-- ���� �޸� -->
<div class=title>���� �޸� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=shoppingapp&no=6')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<textarea name=memo style="width:100%;height:60px" class=tline><?=$data[memo]?></textarea>

<div class=button>

<input type=image src="../img/btn_<?=$_GET[mode]?>.gif">
<? if ($_GET[mode]=="modify"){ ?>
<!--<a href="javascript:copy()"><img src="../img/btn_copy.gif"></a>-->
<? } ?>
<?=$btn_list?>
<?if($_GET['goodsno']){?>&nbsp;<a href="../../goods/goods_view.php?goodsno=<?=$_GET['goodsno']?>" target="_blank"><img src="../img/btn_goods_view.gif"></a><?}?>
</div>
</form>
</div>

<? if ($_GET['call']=='tabLongdescShow'){?>
<script>tabLongdescShow(_ID('btn_longdesc_mobile'));</script>
<? }?>

<!-- �������� Ȱ��ȭ ��ũ��Ʈ -->
<script src="../../lib/meditor/mini_editor.js"></script>
<script>mini_editor("../../lib/meditor/");chk_delivery_type();</script>
<SCRIPT LANGUAGE="JavaScript" SRC="../proc/warning_disk_js.php"><!-- not_delete --></SCRIPT>
<? @include dirname(__FILE__) . "/../interpark/_goods_form.php"; // ������ũ_��Ŭ��� ?>
