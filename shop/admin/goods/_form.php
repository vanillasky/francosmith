<?
@include_once "../../conf/config.mobileShop.php";
@include_once "../../conf/qr.cfg.php";
@include_once "../../conf/config.purchase.php";
@include "../../conf/my_icon.php";

if (!$_GET[mode]) $_GET[mode] = "register";
if ($_GET[mode]=="register"){
	$checked[usedelivery][0] = $checked[open][1] = $checked[open_mobile][1] = $checked[opttype][single] = "checked";
	$hidden[sort] = "style='display:none'";
}

$r_maker[''] = $r_origin[''] = "-- 목록보기 --";
$str_img	= array(
			"i"	=> "메인이미지",
			"s"	=> "리스트이미지",
			"m"	=> "상세이미지",
			"l"	=> "확대(원본)이미지",
			"mobile"	=> "모바일용이미지"
			);

### 제조사
$query = "select distinct maker from ".GD_GOODS."";
$res = $db->query($query);
while ($data=$db->fetch($res)) if ($data[maker]) $r_maker[$data[maker]] = $data[maker];

### 원산지
$query = "select distinct origin from ".GD_GOODS."";
$res = $db->query($query);
while ($data=$db->fetch($res)) if ($data[origin]) $r_origin[$data[origin]] = $data[origin];

/// 아이콘 갯수
$r_myicon = isset($r_myicon) ? (array)$r_myicon : array();
for ($i=0;$i<=7;$i++) if (!isset($r_myicon[$i])) $r_myicon[$i] = '';
$cnt_myicon = sizeof($r_myicon);

### 관련 상품 (, 로 연결된 상품번호)
$related_goodsnos = '';
if ($_GET[mode]=="modify"){

	$goodsno = $_GET[goodsno];

	### 멀티카테고리
	$query = "select category,sort from ".GD_GOODS_LINK." where goodsno='$goodsno' order by category";
	$res = $db->query($query);
	while ($data=$db->fetch($res)) $r_category[$data[category]] = $data[sort];

	### 상품 정보 가져오기
	$data = $db->fetch("select * from ".GD_GOODS." where goodsno='$goodsno'",1);
	$_extra_info = $data['extra_info'];	$data = array_map("slashes",$data); $data['extra_info'] = $_extra_info;	// extra_info 는 json 스트링이므로 slashes 함수를 이용하면 안됨.
	$data[launchdt] = str_replace(array('-','00000000'),'',$data[launchdt]);
	$ex_title = explode("|",$data[ex_title]);

	### QR 사용 정보 가져오기
	$qrdata = $db->fetch("select count(*) from ".GD_QRCODE." where qr_type='goods' and contsNo=$goodsno");
	if($qrdata[0]>0){ $data['qrcode'] = "y"; }else{ $data['qrcode'] = "n";}

	for ($i=0;$i<$cnt_myicon;$i++) if ($data[icon]&pow(2,$i)) $checked[icon][pow(2,$i)] = "checked";

	### 관련상품 리스트 (패치후 수정하지 않은 데이터는 수정 코드 실행)
	if (fixRelationGoods($data['goodsno'])) $data[relation] = 'new_type';

	if ($data[relation]){

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

		ORDER BY sort ASC
		";

		$rs = $db->query($query);
		while ($v = $db->fetch($rs,1)) {
			if ($v[usestock] && $v[totstock] < 1) $v[runout] = 1;
			$r_relation[] = $v;
		}
	}

} else {
	$data[tax] = 1;
	$data[open] = $data[relationis] = $data[open_mobile] = 0;

	$data[goodsno] = '';	// 임시로 상품 번호 생성
}

if($data[goods_deli_type] == '선불' || !$data[goods_deli_type])$goods_deli_type = 0;
if(!$data['use_emoney']) $data['use_emoney'] = 0;
if(!$data['delivery_type']) $data['delivery_type'] = 0;

else $goods_deli_type = 1;
if(!$data['detailView']) $data['detailView'] = 'n'; // 디테일뷰 설정

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
$checked['detailView'][$data['detailView']] = "checked"; // 디테일뷰 설정
$checked['qrcode'][$data['qrcode']] = "checked";  // qrcode 설정
if(!$data['opt1kind'])$data['opt1kind'] = "img";
if(!$data['opt2kind'])$data['opt2kind'] = "img";
$checked['opt1kind'][$data['opt1kind']] = "checked";
$checked['opt2kind'][$data['opt2kind']] = "checked";
$checked['use_stocked_noti'][$data[0]['use_stocked_noti']] = "checked";

$useEx = ($data[ex_title]) ? 1 : 0;
$checked[useEx][$useEx] = "checked";
$display[useEx] = ($useEx) ? "block" : "none";

$img_i = explode("|",$data[img_i]);
$img_s = explode("|",$data[img_s]);
$img_m = explode("|",$data[img_m]);
$img_l = explode("|",$data[img_l]);
$img_mobile = explode("|",$data[img_mobile]);

$imgs = $urls = array(
		'l'	=> $img_l,
		'm'	=> $img_m,
		's'	=> $img_s,
		'i'	=> $img_i,
		'mobile'	=> $img_mobile
		);

// 이미지 주소가 url일때 처리
$checked[image_attach_method][file] = $checked[image_attach_method][url] = 'checked';

if (preg_match('/^http(s)?:\/\//',$img_l[0])) {
	$checked[image_attach_method][file] = '';
	$imgs	= array(
			'l'	=> array(''),
			'm'	=> array(''),
			's'	=> array(''),
			'i'	=> array(''),
			'mobile' => array('')
			);
}
else {
	$urls	= array(
			'l'	=> array(''),
			'm'	=> array(''),
			's'	=> array(''),
			'i'	=> array(''),
			'mobile' => array('')
			);
	$checked[image_attach_method][url] = '';
}
// eof 2011-01-21

### 필수옵션
$optnm = explode("|",$data[optnm]);
$query = "select * from ".GD_GOODS_OPTION." where goodsno='$goodsno' order by sno asc";
$res = $db->query($query);
while ($tmp=$db->fetch($res)){
	$tmp = array_map("htmlspecialchars",$tmp);
	$opt1[] = $tmp[opt1];
	$opt2[] = $tmp[opt2];
	$opt[$tmp[opt1]][$tmp[opt2]] = $tmp;

	### 총재고량 계산
	$stock += $tmp[stock];

	### 옵션이미지
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

### 기본 가격 할당
$price	  = $opt[$opt1[0]][$opt2[0]][price];
$consumer = $opt[$opt1[0]][$opt2[0]][consumer];
$supply	  = $opt[$opt1[0]][$opt2[0]][supply];
$reserve  = $opt[$opt1[0]][$opt2[0]][reserve];

### 추가옵션
$r_addoptnm = explode("|",$data[addoptnm]);
for ($i=0;$i<count($r_addoptnm);$i++){
	list ($addoptnm[],$addoptreq) = explode("^",$r_addoptnm[$i]);
	if ($addoptreq) $checked[addoptreq][$i] = "checked";
}

$query = "select * from ".GD_GOODS_ADD." where goodsno='$goodsno' order by step,sno";
$res = $db->query($query);
while ($tmp=$db->fetch($res)){
	$addopt[$tmp[step]][] = $tmp;
}

$useAdd = ($addopt) ? 1 : 0;
$checked[useAdd][$useAdd] = "checked";
$display[useAdd] = ($useAdd) ? "block" : "none";

if (!$addopt) $addopt = array(array(''));

### 아이콘 설정 적용
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

$colorList = array();
$CL_rs = $db->query("SELECT itemnm FROM ".GD_CODE." WHERE groupcd = 'colorList' ORDER BY sort");
while($CL_row = $db->fetch($CL_rs)) $colorList[] = $CL_row['itemnm'];

$level_query = $db->_query_print("SELECT grpnm, level FROM ".GD_MEMBER_GRP." WHERE 1=1 ORDER BY sno ASC");
$res_level = $db->_select($level_query);

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

/* 옵션 부분 삭제 */
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

/*** 폼체크 ***/
function chkForm2(obj)
{
	if (typeof(obj['category[]'])=="undefined"){
		if (document.getElementsByName("cate[]")[0].value) exec_add();
		else {
			alert("카테고리를 등록해주세요");
			document.getElementsByName("cate[]")[0].focus();
			return false;
		}
	}
	if(!chkTitle()){
		alert('항목명은 중복될 수 없습니다.');
		return false;
	}
	if (!chkOption()) return false;
	if (!chkForm(obj)) return false;

	if (typeof nsInformationByGoods == 'object') if (!nsInformationByGoods.formValidator()) return false;

	try {
		if(obj.useblog[0].checked) {
			if (!chkBlog(obj)) return false;
		}
	} catch(e) {}

<? if($purchaseSet['usePurchase'] == "Y" && $_GET['mode'] == "register") { ?>
	if(document.getElementById('objPurchaseOption').style.display != "none") {
		var pao = document.getElementsByName('purchaseApplyOption');
		if(!pao[0].checked && pao[0].checked) {
			alert("옵션을 추가하시려면 사입처 등록 유형을 선택해 주셔야 합니다.");
			pao[0].focus();
			return false;
		}

		if(pao[0].checked) {
			if(!obj.pchsno.value) {
				alert("사입처를 선택해 주세요.");
				obj.pchsno.focus();
				return false;
			}

			if(!obj.pchs_pchsdt.value) {
				alert("사입일을 입력해 주세요.");
				obj.pchs_pchsdt.focus();
				return false;
			}
		}
	}

	var ar_stock = document.getElementsByName('option[stock][]');
	for(i = 0; i < ar_stock.length; i++) ar_stock[i].disabled = false;
<? } ?>

	// 관련 상품 정보
	nsRelatedGoods.make();

	document.getElementById("formBtn").disabled=true;
	return true;
}

/*** 상품 카테고리 선택 ***/
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
		alert('수정할 카테고리를 선택해주세요');
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
		alert('카테고리를 선택해주세요');
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

/*** 상품 가격/재고 ***/
function addopt1()
{
	var name;
	var fm = document.forms[0];
	var tbOption = document.getElementById('tbOption');
	var Rcnt = tbOption.rows.length;
	oTr = tbOption.insertRow(-1);
	oTr.id = "trid_" + Rcnt;

	for (i=0;i<tbOption.rows[0].cells.length;i++){
		oTd = oTr.insertCell(-1);
		switch (i){
			case 0: oTd.innerHTML = "<input type=text class='opt gray' name=opt1[] value='옵션명1' required label='1차옵션명' ondblclick=\"delopt1part('"+oTr.id+"')\" onclick='chkOptName(this)' onblur=\"chkOptNameOver(this)\">";
			break;
			case 1:	oTd.innerHTML = "<input type=text name=option[price][] class='opt gray' value='" + fm.price.value + "'>"; break;
			case 2:	oTd.innerHTML = "<input type=text name=option[consumer][] class='opt gray' value='" + fm.consumer.value + "'>"; break;
			case 3:	oTd.innerHTML = "<input type=text name=option[supply][] class='opt gray' value='" + fm.supply.value + "'>"; break;
			case 4:	oTd.innerHTML = "<input type=text name=option[reserve][] class='opt gray' value='" + fm.reserve.value + "'>"; break;
			default:
<? if($purchaseSet['usePurchase'] == "Y" && $_GET['mode'] == "register") { ?>
				var pao = document.getElementsByName('purchaseApplyOption');
				if(pao[0].checked) {
					oTd.innerHTML = "<input type=text name=option[stock][] class='opt gray' value='재고' onclick='chkOptName(this)' onblur=\"chkOptNameOver(this)\"><input type=hidden name=option[optno][]>";
				}
				else {
					oTd.innerHTML = "<input type=text name=option[stock][] class='opt gray' value='등록 후 재고 입력' onclick='chkOptName(this)' onblur=\"chkOptNameOver(this)\" disabled=\"true\"><input type=hidden name=option[optno][]>";
				}
<? } else { ?>
				oTd.innerHTML = "<input type=text name=option[stock][] class='opt gray' value='재고' onclick='chkOptName(this)' onblur=\"chkOptNameOver(this)\"><input type=hidden name=option[optno][]>";
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
		alert('1차옵션을 먼저 추가해주세요');
		return;
	}

	var Ccnt = tbOption.rows[0].cells.length;

	for (i=0;i<tbOption.rows.length;i++){
		oTd = tbOption.rows[i].insertCell(-1);
		if(!i)oTd.id = "tdid_"+Ccnt;
<? if($purchaseSet['usePurchase'] == "Y" && $_GET['mode'] == "register") { ?>
		var pao = document.getElementsByName('purchaseApplyOption');
		if(pao[0].checked) {
			oTd.innerHTML = (i) ? "<input type=text name=option[stock][] class='opt gray'  value='재고' onclick='chkOptName(this)' onblur=\"chkOptNameOver(this)\"><input type=hidden name=option[optno][]>" : "<input type=text class='opt gray' name=opt2[] value='옵션명2' required label='2차옵션명' ondblclick=\"delopt2part('"+oTd.id+"')\" onclick='chkOptName(this)' onblur=\"chkOptNameOver(this)\">";
		}
		else {
			oTd.innerHTML = (i) ? "<input type=text name=option[stock][] class='opt gray'  value='등록 후 재고 입력' onclick='chkOptName(this)' onblur=\"chkOptNameOver(this)\" disabled=\"true\"><input type=hidden name=option[optno][]>" : "<input type=text class='opt gray' name=opt2[] value='옵션명2' required label='2차옵션명' ondblclick=\"delopt2part('"+oTd.id+"')\" onclick='chkOptName(this)' onblur=\"chkOptNameOver(this)\">";
		}
<? } else { ?>
		oTd.innerHTML = (i) ? "<input type=text name=option[stock][] class='opt gray'  value='재고' onclick='chkOptName(this)' onblur=\"chkOptNameOver(this)\"><input type=hidden name=option[optno][]>" : "<input type=text class='opt gray' name=opt2[] value='옵션명2' required label='2차옵션명' ondblclick=\"delopt2part('"+oTd.id+"')\" onclick='chkOptName(this)' onblur=\"chkOptNameOver(this)\">";
<? } ?>
	}
	addopt2_fashion();
}
function delopt1()
{
	var tbOption = document.getElementById('tbOption');
	if (tbOption.rows.length>2) tbOption.deleteRow(-1);
	delopt1_fashion();
}
function delopt2()
{
	var tbOption = document.getElementById('tbOption');
	if (tbOption.rows[0].cells.length<7) return;
	for (i=0;i<tbOption.rows.length;i++){
		tbOption.rows[i].deleteCell(-1);
	}
	delopt2_fashion();
}

/*** 추가옵션 ***/
function add_addopt()
{
	var tbAdd = document.getElementById('tbAdd');
	oTr = tbAdd.insertRow(-1);
	oTd = oTr.insertCell(-1);
	oTd.innerHTML = "<input type=text name=addoptnm[]> <a href='javascript:void(0)' onClick='add_subadd(this)'><img src='../img/i_proadd.gif' align=absmiddle></a>&nbsp;<a href='javascript:void(0)' onClick='del_subadd(this)'><img src='../img/btn_listdel.gif' align=absmiddle /></a>";
	oTd = oTr.insertCell(-1);
	oTd.colSpan = 2;
	oTd.innerHTML = "\
	<table>\
	<tr>\
		<td><input type=text name=addopt[opt][" + (oTr.rowIndex-1) + "][] style='width:270px'> 선택시</td>\
		<td>판매금액에 <input type=text name=addopt[addprice][" + (oTr.rowIndex-1) + "][] size=9> 원 추가 <input type=hidden name=addopt[addno][] value=''></td>\
	</tr>\
	</table>\
	";
	oTd = oTr.insertCell(-1);
	oTd.className = "noline";
	oTd.align = "center";
	oTd.innerHTML = "<input type=checkbox name=addoptreq[" + (oTr.rowIndex-1) + "]>";
}
function del_addopt()
{
	var tbOption = document.getElementById('tbAdd');
	if (tbOption.rows.length>2) tbOption.deleteRow(-1);
}
function add_subadd(obj)
{
	var idx = obj.parentNode.parentNode.rowIndex - 1;
	obj = obj.parentNode.parentNode.getElementsByTagName("TD")[1].getElementsByTagName('table')[0];
	oTr = obj.insertRow(-1);
	oTd = oTr.insertCell(-1);
	oTd.innerHTML = "<input type=hidden name=addopt[sno][" + idx + "][]><input type=text name=addopt[opt][" + idx + "][] style='width:270px'> 선택시";
	oTd = oTr.insertCell(-1);
	oTd.innerHTML = "판매금액에 <input type=text name=addopt[addprice][" + idx + "][] size=9> 원 추가 <input type=hidden name=addopt[addno][] value=''>";
}
function del_subadd(obj)
{
	var idx = obj.parentNode.parentNode.rowIndex - 1;
	obj = obj.parentNode.parentNode.childNodes(1).getElementsByTagName('table')[0];
	if(obj.rows.length<2){
		alert('삭제할 항목이 없습니다.');
		return false;
	}
	obj.deleteRow();
}

/*** 관련상품 ***/
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

/*** 상품 이미지 ***/
function preview(obj)
{
	var tmp = obj.parentNode.parentNode.parentNode.childNodes(2);
	tmp.innerHTML = "<img src='" + obj.value + "' width=20 onload='if(this.height>this.width){this.height=20}' style='border:1 solid #cccccc' onclick=popupImg(this.src,'../') class=hand>";
}
function addfld(obj)
{
	var tb = document.getElementById(obj);
	oTr = tb.insertRow(-1);
	oTd = oTr.insertCell(-1);
	oTd.innerHTML = "<a href='javascript:void(0)' onClick='delfld(this)'><img src='../img/i_del.gif' align=absmiddle></a>	<span>" + tb.rows[0].cells[0].getElementsByTagName('span')[0].innerHTML + "</span>";
	oTd.getElementsByTagName('input')[0].value='';
	oTd = oTr.insertCell(-1);
	oTd = oTr.insertCell(-1);
}
function delfld(obj)
{
	var tb = obj.parentNode.parentNode.parentNode.parentNode;
	tb.deleteRow(obj.parentNode.parentNode.rowIndex);
}

/*** 자동으로 가격필드에 입력값 저장 ***/
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
		if(pao[0].checked) { // 사입처 동일 적용
			disabledStyle(document.fm.stock, "t"); // 재고량 X
			disabledStyle(document.fm.pchs_stock, "t"); // 입고량 X
			disabledStyle(document.fm.pchsno, "f"); // 사입처 O
			disabledStyle(document.fm.pchs_pchsdt, "f"); // 사입일 O
		}
		else if(pao[1].checked) { // 사입처 개별 적용
			disabledStyle(document.fm.stock, "t"); // 재고량 X
			disabledStyle(document.fm.pchsno, "t"); // 사입처 X
			disabledStyle(document.fm.pchs_stock, "t"); // 입고량 X
			disabledStyle(document.fm.pchs_pchsdt, "t"); // 사입일 X
		}
		else {
			disabledStyle(document.fm.pchs_stock, "t"); // 입고량 X
		}
	}
	else {
		// 재고량, 사입처, 매입가, 입고량, 사입일 모두 사용 가능하도록
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

	if(val == "1") { // 사입처 동일 적용
		disabledStyle(document.fm.stock, "t"); // 재고량 X
		disabledStyle(document.fm.pchs_stock, "t"); // 입고량 X
		disabledStyle(document.fm.pchsno, "f"); // 사입처 O
		disabledStyle(document.fm.pchs_pchsdt, "f"); // 사입일 O
	}
	else { // 사입처 개별 적용
		disabledStyle(document.fm.stock, "t"); // 재고량 X
		disabledStyle(document.fm.pchsno, "t"); // 사입처 X
		disabledStyle(document.fm.pchs_stock, "t"); // 입고량 X
		disabledStyle(document.fm.pchs_pchsdt, "t"); // 사입일 X
	}

	for(i = 0; i < ar_stock.length; i++) {
		if(val == "1") {
			ar_stock[i].value = "재고";
			ar_stock[i].disabled = false;
		}
		else {
			ar_stock[i].value = "등록 후 재고 입력";
			ar_stock[i].disabled = true;
		}
	}
}

function chkOptName(obj){
 if(obj.value=='옵션명2' || obj.value=='옵션명1'){
  obj.className = 'fldtitle';
  obj.value = '';
 }
 if(obj.value=='재고'){
  obj.className = 'opt';
  obj.value = '';
 }
}

function chkOptNameOver(obj){
 if(obj.value == ''){
  obj.className = 'opt gray';
  if(obj.name == 'opt1[]') obj.value = '옵션명1';
  if(obj.name == 'opt2[]') obj.value = '옵션명2';
  if(obj.name == 'option[stock][]') obj.value = '재고';
 }
}

function chkOption(){
	var obj = document.getElementsByName('opt1[]');
	var chk = false;
	for(var i=0;i < obj.length;i++){
		 chkOptName(obj[i]);
		 if(obj[i].value == '' && obj.length > 1){
			alert('옵션 1은 필수 항목입니다.');
			obj[i].focus();
			return false;
		 }
		 if( (obj[i].value || obj.length > 1) && !chk) chk = true;
	}

	var obj = document.getElementsByName('opt2[]');
	for(var i=0;i < obj.length;i++){
		chkOptName(obj[i]);
		if(chk && obj[i].value == '' && obj.length > 1){
			alert('옵션 2은 필수 항목입니다.');
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

// 자동리사이즈
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
		alert('원본이미지 먼저 등록하세요.');
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
		alert("블로그의 포스트 분류는 필수입니다");
		return false;
	}
	return true;
}

function chkSchPchs() {
	if(document.fm.pchsno.disabled == false) {
		window.open('../goods/popup.purchase_find.php', 'purchaseSearchPop', 'width=640,height=450');
	}
}

// 스마트 검색 : rgb코드 -> 16진수코드
function convColor(colorCode) {
	if(colorCode.toLowerCase().indexOf('rgb') == 0) {
		colorCode = colorCode.toLowerCase().replace(/rgb/g, '');
		colorCode = colorCode.toLowerCase().replace(/\(/g, '');
		colorCode = colorCode.toLowerCase().replace(/\)/g, '');
		colorCode = colorCode.toLowerCase().replace(/ /g, '');

		colorCode_tempList = colorCode.split(',');
		colorCode = "";

		for(i = 0; i < colorCode_tempList.length; i++) {
			tmpCode = parseInt(colorCode_tempList[i]).toString(16);
			if(String(tmpCode).length == 1) tmpCode = "0" + tmpCode;
			colorCode += tmpCode;
		}
		colorCode = "#" + colorCode;
	}

	return colorCode;
}

// 스마트 검색 : 색 선택
function selectColor(targetColor) {
	targetColor = convColor(targetColor);

	targetColor = targetColor.toUpperCase();
	tempColor = $("color");

	if(tempColor.value.indexOf(targetColor) != -1) return false;
	else tempColor.value = tempColor.value + targetColor;

	if(tempColor.value) color2Tag('selectedColor');
}

// 스마트 검색 : 선택된 색상을 표시
function color2Tag(targetID) {
	var colorTag = $(targetID);
	var colorText = $("color").value;
	var tempColor = "";

	colorTag.innerHTML = "";
	for(i = 0; i < colorText.length; i = i + 7) {
		tempColor = colorText.substr(i, 7);
		if(tempColor) colorTag.innerHTML += "<div href=\"javascript:;\" style=\"background-color:" + tempColor + "\" class=\"paletteColor_selected\" ondblclick=\"deleteColor('" + targetID + "', this.style.backgroundColor);\"></div>\n";
	}

	if(colorTag.innerHTML) {
		colorTag.innerHTML += "<div style=\"clear:left;\"></div>";
	}
	else {
		colorTag.innerHTML = "&nbsp;";
	}
}

// 스마트 검색 : 색상 제거
function deleteColor(targetID, delColor) {
	delColor = convColor(delColor);

	delColor = delColor.toUpperCase();
	$("color").value = $("color").value.toUpperCase();
	$("color").value = $("color").value.replace(delColor, "");
	color2Tag(targetID);
}

</script>
<style type="text/css">
	.paletteColor { width:15px; height:15px; cursor:pointer; border:1px #DDDDDD solid; }
	.paletteColor_selected { float:left; width:15px; height:15px; margin:1px; cursor:pointer; border:1px #DDDDDD solid; }
	.selColorText { margin-top:8px; font-size:11px; font-family:dotum; color:#0070C0; float:left; cursor:pointer; }

	#selectedColor { float:left; }
	#colorList td { padding:5px 0px; border-bottom:1px #DCD8D6 solid; }
</style>


<table width=800 cellpadding=0 cellspacing=0>
<tr><td align=center><div id=goods_form><? include "../proc/warning_disk_msg.php"; # not_delete  ?></td></tr></table>


<form name=fm method=post action="indb.goods.php" enctype="multipart/form-data" onsubmit="return chkForm2(this)">
<input type=hidden name=mode value="<?=$_GET[mode]?>">
<input type=hidden name=goodsno value="<?=$goodsno?>">
<input type=hidden name=returnUrl value="<?=$returnUrl?>">

<? if ($goodsno) { ?>
<div style="padding:8px 13px;background:#f7f7f7;border:3px solid #C6C6C6;margin-bottom:18px;" id="goodsInfoBox">
	<div><font class=def>고유번호:</font> <span style="color:#FF7200;font:bold 14px verdana"><?=$goodsno?></span></div>
</div>
<? } ?>
<!-- 상품 카테고리 선택 -->
<div class="title title_top">상품분류정보<span>한상품에 여러개의 분류를 등록할 수 있습니다&nbsp;(다중분류기능지원)</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=3')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
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
<div class=noline><input type=checkbox name=sortTop><font class=small color=444444>체크시 이상품을 위에 등록된 해당 각 분류페이지의 최상단에 보여지게합니다</font></div>
<div class=noline style="padding-left:3;padding-bottom:10px"><font color=627dce>※</font> <font class=extext>주의: 상품분류(카테고리)가 먼저 등록되어 있어야 상품등록이 가능합니다.</font> <a href="/shop/admin/goods/category.php" target=blank><font class=extext_l>[상품분류(카테고리) 등록하기]</font></a></div>
<div style="border-bottom:3px #efefef solid;padding-top:10px"></div>

<!-- 인터파크_카테고리 -->
<div id="interpark_category"></div>
<div style="border-bottom:3px #efefef solid;padding-top:10px"></div>

<!-- 상품기본정보 -->
<div class=title>상품기본정보<span>제조사, 원산지, 브랜드가 없는 경우 입력안해도 됩니다 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=3')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<table class=tb>
<col class=cellC><col class=cellL><col class=cellC><col class=cellL>
<tr>
	<td width=120 nowrap>상품명</td>
	<td width=50%><div style="height:25;padding-top:5"><input type=text name=goodsnm style="width:100%" value="<?=$data[goodsnm]?>" required label="상품명" class="line"></div><div style="height:23"><input type=checkbox name="meta_title" value="1" class=null <?=$checked[meta_title][1]?>>상품명을 상품상세페이지의 타이틀 태그에 입력됩니다.</div></td>
	<td width=120 nowrap>상품코드</td>
	<td width=50%><input type=text name=goodscd style="width:100%" value="<?=$data[goodscd]?>" class="line"></td>
</tr>
<tr>
	<td>제조사</td>
	<td>
	<input type=text name=maker value="<?=$data[maker]?>" class="line">
	<select onchange="this.form.maker.value=this.value;this.form.maker.focus()">
	<? foreach ($r_maker as $k=>$v){ ?><option value="<?=$k?>"><?=$v?><? } ?>
	</select>
	</td>
	<td>원산지</td>
	<td>
	<input type=text name=origin value="<?=$data[origin]?>" class="line">
	<select onchange="this.form.origin.value=this.value;this.form.origin.focus()">
	<? foreach ($r_origin as $k=>$v){ ?><option value="<?=$k?>"><?=$v?><? } ?>
	</select>
	</td>
</tr>
<tr>
	<td>브랜드</td>
	<td>
	<select name=brandno>
	<option value="">-- 브랜드 선택 --
	<?
	$res = $db->query("select * from ".GD_GOODS_BRAND." order by sort");
	while ($tmp=$db->fetch($res)){
	?>
	<option value="<?=$tmp[sno]?>" <?=$selected[brandno][$tmp[sno]]?>><?=$tmp[brandnm]?>
	<? } ?>
	</select>
	<font class=small1 color=444444><a href="brand.php" target=_blank><font class=extext_l><img src="../img/btn_brand_add.gif"></a>
	</td>
	<td>출시일</td>
	<td>
	<input type="text" name="launchdt" value="<?=$data[launchdt]?>" onclick="calendar()" onkeydown="onlynumber()" class="line"> <font class=ver71 color=627dce>ex) 20080321</font>
	<div style="padding-top:3px"><font class=extext>네이버 지식쇼핑 입점시 인기도(노출순위)를 결정짓는 중요한 요소입니다</font></div>
	</td>
</tr>
<script>

	</script>
<tr>
	<td>아이콘</td>
	<td class=noline>
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
	<div style="padding:5px 0px 5px 5px"><font class=extext>다른 아이콘으로 쉽게 바꿀수 있습니다</font> <a href="javascript:popup('popup.myicon.php',510,550)"><img src="../img/btn_icon_change.gif" align=absmiddle></a></div>
	</td>
	<td>상품 대표색상</td>
	<td class=noline>
		<input type="hidden" name="color" id="color" value="<?=$data['color']?>" />
		<div><table border="0" cellpadding="0" cellspacing="2" bgcolor="#FFFFFF"><tr><?
	for($i = 0, $imax = count($colorList); $i < $imax; $i++) {
		echo "<td><div class=\"paletteColor\" style=\"background-color:#".$colorList[$i].";\" onclick=\"selectColor(this.style.backgroundColor)\"></div></td>";
		if($imax / 2 == $i + 1) echo "</tr><tr>";
	}
		?></tr></table></div>
		<div class="selColorText">선택색상 :&nbsp;</div><div id="selectedColor" title="선택된 색은 더블클릭으로 삭제하실 수 있습니다.">&nbsp;</div>
		<div style="padding:5px 0px 0px 0px; clear:left;"><font class=extext>상품 색상 검색시에 사용됩니다.</font></div>
	</td>
</tr>
<tr>
	<td>상품출력여부</td>
	<td class=noline><input type=checkbox name=open value=1 <?=$checked[open][1]?>>보이기
	<font class=extext>(체크해제시 화면에서 안보임)</font></td>
	<td>모바일샵 출력여부</td>
	<td class=noline>
		<?php if($cfgMobileShop['vtype_goods']=='1'){?>
		<input type=checkbox name=open_mobile value=1 <?=$checked[open_mobile][1]?>>보이기
		<font class=extext>(체크해제시 모바일샵 화면에서 안보임)</font>
		<?php }else{?>
		<input type=hidden name=open_mobile value="<?php echo $data['open'];?>" />
		<font class="red">상품출력여부와 동일하게 적용되도록 설정되어있습니다.</font>
		<?php }?>
	</td>
</tr>
<tr>
	<td>유사검색어</td>
	<td colspan=3>
	<div style='padding-top:5px'><input type=text name=keyword value="<?=$data[keyword]?>" style="width:100%" class="line"></div>
	<div style="height:23;padding-top:5px" class=extext>상품상세 페이지의 메타태그와 상품 검색시 키워드로 사용하실 수 있습니다.</font></div>
	</td>
</tr>
</table>
<div style="padding-top:20px"></div>
<div style="border-top:3px #efefef solid;"></div>
<!-- 상품추가정보 -->
<div class=title>상품추가정보<span>상품특성에 맞게 항목을 추가할 수 있습니다 (예. 감독, 저자, 출판사, 유통사, 상품영문명 등) <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=3')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></span>
<a href="javascript:popup('http://guide.godo.co.kr/guide/php/ex_infoadd.html',650,610)"><img src="../img/icon_sample.gif" border="0" align=absmiddle hspace=2></a></div>
<div class=noline style="padding-bottom:5px">
<input type="radio" name="useEx" <?=$checked[useEx][1]?> onclick="openLayer('tbEx','block')" onfocus="blur()" value="1" /> 사용
<input type="radio" name="useEx" <?=$checked[useEx][0]?> onclick="openLayer('tbEx','none')" onfocus="blur()" value="0" /> 사용안함
</div>
<table id=tbEx class=tb style="display:<?=$display[useEx]?>">
<col class=cellC><col class=cellL><col class=cellC><col class=cellL>
<tr>
	<? for ($i=0;$i<6;$i++){ $ex = "ex".($i+1); ?>
	<td><input type=text name="title[]" class="exTitle gray" value="<?=$ex_title[$i]?>" onblur="if(!chkTitle())alert('항목명은 중복될 수 없습니다.')"></td>
	<td width=50%><input type=text name="ex[]" value="<?=$data[$ex]?>" style="width:100%"></td>
	<? if ($i%2){ ?></tr><tr><? } ?>
	<? } ?>
</tr>
</table>
<div style="border-bottom:3px #efefef solid;padding-top:20px"></div>

<!-- 상품적립금 -->
<div class=title>적립금<span>이 상품 주문시 적립되는 적립금을 설정합니다.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=3')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<div class=noline style="padding-bottom:5px">
<div><input type=radio name="use_emoney" <?=$checked[use_emoney][0]?> value="0" onfocus=blur()> 적립금설정의 정책을 적용합니다. <font class=extext>(이 상품의 적립금을 <a href="../basic/emoney.php" target="_blank"><font class=extext_l>[기본관리 > 적립금설정 > 상품 적립금 지급에 대한 정책]</font></a> 에서 설정한 정책을 따릅니다)</font></div>
<div><input type=radio name="use_emoney" <?=$checked[use_emoney][1]?> value="1" onfocus=blur()> 적립금을 따로 입력합니다. <font class=extext>(이 상품의 적립금을 바로 아래의 <b>가격/재고/배송비</b>에서 등록한 적립금으로 제공합니다)</font></div>
</div>
<div style="border-bottom:3px #efefef solid;padding-top:20px"></div>

<!-- 사입처정보 -->
<?
	if($purchaseSet['usePurchase'] == "Y" && $_GET['mode'] == "register") {
		if($goodsno) $pchsData = $db->fetch("SELECT * FROM ".GD_PURCHASE_GOODS." WHERE goodsno = '$goodsno' ORDER BY pchsdt DESC LIMIT 1");
?>
<div class=title>사입처 정보</div>
<div style="height:5px;font:0"></div>
<table cellpadding="3" cellspacing="1" border="0" bgcolor="#E6E6E6" width="100%">
<tr>
	<td style="width:110px; height:32px; padding-left:10px; background:#F6F6F6; color:#333333; font-weight:bold;">사입처</td>
	<td style="width:250px; padding-left:10px; background:#FFFFFF; color:#333333;">
		<select name="pchsno" id="pchsno"<?=($_GET['mode'] == "modify") ? " disabled=\"true\"" : ""?>>
			<option value="">사입처선택</option>
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
		<a href="javascript:;" onclick="chkSchPchs()"><img src="../img/purchase_find.gif" title="사입처 검색" align="absmiddle" /></a>
	</td>
	<td style="width:110px; height:32px; padding-left:10px; background:#F6F6F6; color:#333333; font-weight:bold;">사입일</td>
	<td style="padding-left:10px; background:#FFFFFF; color:#333333;"><input type=text name=pchs_pchsdt size=10 value="" onclick="calendar()" onkeydown="onlynumber()" class="line"<?=($_GET['mode'] == "modify") ? " disabled=\"true\"" : ""?>></td>
</tr>
</table>
<div style="padding:10px;">
<font class=extext>- 사입처 변경 후 저장 하시면 해당 사입처로 사입 이력이 저장 됩니다.<br />
- 이미 사입처 연동 사용중인 상품을  “사용 안 함”으로 변경 시 이 후 이력이 저장 되지 않습니다.<br />
* 주의: 사입일이 지정 되어 있어야 상품등록이 가능합니다.</font>
</div>
<div style="border-bottom:3px #efefef solid;padding-top:10px"></div>
<? } ?>

<!-- 상품 가격/재고 -->
<div class=title>가격/재고<span>사이즈, 색상 등에 의해 가격이 여러개인 경우 가격옵션을 추가할 수 있습니다 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=3')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<table>
<? if($purchaseSet['usePurchase'] == "Y" && $_GET['mode'] == "register") { ?>
<tr>
	<td>판매가</td><td><input type=text name=price size=10 value="<?=$price?>" onchange="autoPrice(this)" onblur="autoPrice(this)" onkeydown="autoPrice(this)" class="line"></td>
	<td style="padding-left:10px">정가</td><td><input type=text name=consumer size=10 value="<?=$consumer?>" onchange="autoPrice(this)" onblur="autoPrice(this)" onkeydown="autoPrice(this)" class="line"></td>
	<td style="padding-left:10px">적립금</td><td colspan="3"><input type=text name=reserve size=10 value="<?=$reserve?>" onchange="autoPrice(this)" onblur="autoPrice(this)" onkeydown="autoPrice(this)" class="line"></td>
</tr>
<tr>
	<td>재고량</td><td><input type=text name=stock size=10 value="<?=$stock?>" onchange="autoPrice(this)" onblur="autoPrice(this)" onkeydown="autoPrice(this)" class="line"></td>
	<td style="padding-left:10px">입고량</td><td><input type=text name=pchs_stock size=10 value="" class="line"<?=($_GET['mode'] == "modify") ? " disabled=\"true\"" : ""?>></td>
	<td style="padding-left:10px">매입가</td><td><input type=text name=supply size=10 value="<?=$supply?>" onchange="autoPrice(this)" onblur="autoPrice(this)" onkeydown="onlynumber();autoPrice(this)" class="line"></td>
</tr>
<? } else { ?>
<tr>
	<td>판매가</td><td><input type=text name=price size=10 value="<?=$price?>" onchange="autoPrice(this)" onblur="autoPrice(this)" onkeydown="autoPrice(this)" class="line"></td>
	<td style="padding-left:10px">정가</td><td><input type=text name=consumer size=10 value="<?=$consumer?>" onchange="autoPrice(this)" onblur="autoPrice(this)" onkeydown="autoPrice(this)" class="line"></td>
	<td style="padding-left:10px">매입가</td><td><input type=text name=supply size=10 value="<?=$supply?>" onchange="autoPrice(this)" onblur="autoPrice(this)" onkeydown="autoPrice(this)" class="line"></td>
	<td style="padding-left:10px">적립금</td><td><input type=text name=reserve size=10 value="<?=$reserve?>" onchange="autoPrice(this)" onblur="autoPrice(this)" onkeydown="autoPrice(this)" class="line"></td>
	<td style="padding-left:10px">재고량</td><td><input type=text name=stock size=10 value="<?=$stock?>" onchange="autoPrice(this)" onblur="autoPrice(this)" onkeydown="autoPrice(this)" class="line"></td>
</tr>
<? } ?>
</table>

<div style="height:5px;font:0"></div>
<table class=tb>
<col class=cellC><col class=cellL><col class=cellC><col class=cellL>
<tr>
	<td width=120 nowrap>재고량연동</td>
	<td width=50% class=noline><input type=checkbox name=usestock <?=$checked[usestock][o]?>> 주문시 재고량빠짐
	<div style="padding-top:3px"><font class=extext>(체크안하면 재고량 상관없이 무한정판매)</font></div></td>
	<td width=120 nowrap>품절상품</td>
	<td width=50% class=noline><input type=checkbox name=runout value=1 <?=$checked[runout][1]?>> 품절된 상품입니다</td>
</tr>
<tr>
	<td width=120 nowrap>구매수량 설정</td>
	<td>
	최소구매수량 : <input type="text" name="min_ea" size=5 value="<?=$data['min_ea']?>"> &nbsp;
	최대구매수량 : <input type="text" name="max_ea" size=5 value="<?=$data['max_ea']?>">
	<div style="padding-top:3px"><span class=extext>0 이면 제한이 없습니다.<br/>설정된 구매수량(최소구매수량, 최대구매수량)은 각 주문 한건에 대한 제한사항입니다.</span></div>
	</td>
	<td width=120 nowrap>재입고 알림</td>
	<td width=50% class=noline>
	<input type=checkbox name=use_stocked_noti value=1 <?=$checked[use_stocked_noti][1]?>> 상품 재입고 알림 사용
	<div style="padding-top:3px"><font class=extext>(상품/옵션 품절시 상세페이지에 재입고 알림신청 버튼 노출)</font></div></td>
	</td>
</tr>
<tr>
	<td>과세/비과세</td>
	<td class=noline>
	<input type=radio name=tax value=1 <?=$checked[tax][1]?>> 과세
	<input type=radio name=tax value=0 <?=$checked[tax][0]?>> 비과세
	</td>
	<td>가격대체문구</td>
	<td><input type=text name=strprice value="<?=$data[strprice]?>" class="line"></td>
</tr>
<script>
function chk_delivery_type(){
	var obj = document.getElementsByName('delivery_type');
	<?/*
	[0] : 기본 배송 정책에 따름
	[1] : 무료배송
	[2] : 상품별 배송비 (더이상 사용하지 않음)
	[4] : 고정 배송비
	[5] : 수량별 배송비
	[3] : 착불 배송비
	*/?>
	// 배송비 필드 숨김
	var k = 0;
	$w('0 1 2 4 5 3').each(function(v){
		if ($('gdi' + v)) $('gdi' + v).setStyle({display: (obj[k].checked == true)  ? 'inline' : 'none' });
		k++;
	});
	return;
}
</script>
</table>

<div style="padding: 10px 10px 10px 0px"><a href="javascript:vOption()" onfocus=blur()><img src="../img/btn_priceopt_add.gif" align=absmiddle></a> <font class=small color=444444>이 상품의 옵션이 여러개인경우 등록하세요 (색상, 사이즈 등)</font>
<a href="javascript:popup('http://guide.godo.co.kr/guide/php/ex_infoprice.html',730,700)"><img src="../img/icon_sample.gif" border="0" align=absmiddle></a></div>

<div id="objPurchaseOption" style="display:none;"><table cellpadding="4" cellspacing="0" border="0" style="width:500px; border:1px #DDDDDD solid;margin:10px 0px;">
<tr>
	<td>
		<input type="radio" id="purchaseAllApply" name="purchaseApplyOption" style="border:0px;" value="1" onclick="document.getElementById('objOption').style.display = 'block';chkPurchaseOption(this.value);" /><label for="purchaseAllApply">사입처 동일 적용</label> <span class="extext">추가옵션이 동일한 사입처에서 입고 된 경우</span>
	</td>
</tr>
<tr>
	<td>
		<input type="radio" id="purchaseEachApply" name="purchaseApplyOption" style="border:0px;" value="2" onclick="document.getElementById('objOption').style.display = 'block';chkPurchaseOption(this.value);" /><label for="purchaseEachApply">사입처 개별 적용</label> <span class="extext">추가옵션이 각각 다른 사입처에서 입고 된 경우</span>
	</td>
</tr>
</table></div>

<div id=objOption style="display:none">
<div style="padding-bottom:10">
<font class=small color=black><b>옵션명1</b> : <input type=text name=optnm[] value="<?=$optnm[0]?>">
<a href="javascript:addopt1()" onfocus=blur()><img src="../img/i_add.gif" align=absmiddle></a> <a href="javascript:delopt1()" onfocus=blur()><img src="../img/i_del.gif" align=absmiddle></a><span style="width:20"></span>
<b>옵션명2</b></font> : <input type=text name=optnm[] value="<?=$optnm[1]?>">
<a href="javascript:addopt2()" onfocus=blur()><img src="../img/i_add.gif" align=absmiddle></a> <a href="javascript:delopt2()" onfocus=blur()><img src="../img/i_del.gif" align=absmiddle></a><span style="width:20"></span>
<span class=noline><b>옵션출력방식</b> :
<input type=radio name=opttype value="single" <?=$checked[opttype][single]?>> 일체형
<input type=radio name=opttype value="double" <?=$checked[opttype][double]?>> 분리형
</span>
</div>
<?if(count($opt)>1 || $opt1[0] != null || $opt2[0] != null){?><script>vOption();</script><?}?>
<div style="margin:10px 0"><font class=extext>등록한 옵션명1과 옵션명2를 더블클릭하시여 옵션을 삭제하실 수 있습니다.</font></div>
<div style="margin:10px 0"><font class=extext><span style="color:red">[※ 주의]</span> 등록한 옵션명 수정 및 삭제시, 기존 옵션명의 재고를 포함한 모든 정보는 복원되지 않으며, 변경된 정보로 Update 됩니다.</font></div>
<table id=tbOption border=1 bordercolor=#cccccc style="border-collapse:collapse">
<tr align=center>
	<td width=116></td>
	<td><span style="color:#333333;font-weight:bold;">판매가</span></td>
	<td><span style="color:#333333;font-weight:bold;">정가</span></td>
	<td><span style="color:#333333;font-weight:bold;">매입가</span></td>
	<td><span style="color:#333333;font-weight:bold;">적립금</span></td>
	<?
		$j=4;
		foreach ($opt2 as $v){
		$j++;
	?>
	<td id='tdid_<?=$j?>'><input type="text" name="opt2[]" <? if($v != '') { ?>class=fldtitle value="<?=$v?>"<? } else { ?>class="opt gray" value='옵션명2'<? } ?> ondblclick="delopt2part('tdid_<?=$j?>')" onclick="chkOptName(this)" onblur="chkOptNameOver(this)"></td>
	<? } ?>
</tr>
	<?
	$i=0;
	$op2=$opt2[0]; foreach ($opt1 as $op1){
	$i++;
	?>
<tr id="trid_<?=$i?>">
	<td width=116 nowrap><input type=text name=opt1[] <?if($op1 != ''){?>class=fldtitle value="<?=$op1?>"<?}else{?>class="opt gray" value='옵션명1'<?}?> <?if($i != 1){?>ondblclick="delopt1part('trid_<?=$i?>')"<?}?> onclick="chkOptName(this)" onblur="chkOptNameOver(this)"></td>
	<td><input type=text name=option[price][] class="opt gray" value="<?=$opt[$op1][$op2][price]?>"></td>
	<td><input type=text name=option[consumer][] class="opt gray" value="<?=$opt[$op1][$op2][consumer]?>"></td>
	<td><input type=text name=option[supply][] class="opt gray" value="<?=$opt[$op1][$op2][supply]?>"></td>
	<td><input type=text name=option[reserve][] class="opt gray" value="<?=$opt[$op1][$op2][reserve]?>"></td>
	<? foreach ($opt2 as $op2){ ?>
	<td><input type=text name=option[stock][] <?if($opt[$op1][$op2][stock]){?>class="opt" value="<?=$opt[$op1][$op2][stock]?>"<?}else{?>class="opt gray" value="재고"<?}?> onclick="chkOptName(this)" onblur="chkOptNameOver(this)"><input type=hidden name=option[optno][] value="<?=$opt[$op1][$op2][optno]?>"></td>
	<? } ?>
</tr>
<? } ?>
</table>
<div style="padding-top:10px">
	<select name="dopt" style="width:125">
		<option value=''>옵션바구니 선택</option>
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
<span style="color:#627dce;">&#149;</span> <span class="extext">옵션명 1 : 옵션명1은 옵션별 상품의 가격 차이가 있는 경우 입력하는 정보 입니다.<br/>
<span style="color:#fff;">__________</span> ex) 색상별 가격차가 있고 등록한 색상에 사이즈 구분이 있는 경우 옵션명 1에 색상을 입력하고 옵션명 2에 사이즈를 입력해야 합니다.<br/></span>
<span style="color:#627dce;">&#149;</span> <span class="extext">옵션명 2 : 옵셥명 2는 옵션명1의 하위 옵션 정보를 입력해야 합니다.<br/>
<span style="color:#fff;">__________</span> ex) 옵션명 1: 빨강 옵션명 2: 대, 중, 소 → 빨강색 상품에 사이즈가 대,중,소가 있음을 의미합니다.</span>
</div>

<?include "_form.fashion.php";?>
<p />
</div>
<div style="border-bottom:3px #efefef solid;padding-top:20px"></div>

<!-- 추가옵션 -->
<div class=title>추가옵션/추가상품/사은품<span>추가옵션을 무제한 등록할 수 있으며, 추가상품을 판매하거나 사은품을 제공할 수도 있습니다 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=3')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a>
<a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=3')"><img src="../img/icon_sample.gif" border="0" align=absmiddle></a></div>
<div class=noline style="padding-bottom:5px">
<input type="radio" name="useAdd" <?=$checked[useAdd][1]?> onclick="openLayer('tbAddWrap','block')" onfocus="blur()" value="1" /> 사용
<input type="radio" name="useAdd" <?=$checked[useAdd][0]?> onclick="openLayer('tbAddWrap','none')" onfocus="blur()" value="0" /> 사용안함
<span style="padding-left:7px;color:#627dce">※</span> <span class="extext">추가옵션/추가상품/사은품 으로 지정한 상품은 적립금 지급 및 할인, 쿠폰 적용대상에 포함되지 않습니다.
</span>
</div>

<a href="javascript:add_addopt()"><img src="../img/i_addoption.gif" align=absmiddle></a>
<a href="javascript:del_addopt()"><img src="../img/i_deloption.gif" align=absmiddle></a>
<span class=small1 style="padding-left:5px">(옵션명에 아무 내용도 입력하지 않으면 해당 옵션은 삭제처리됩니다)</span>

<div style="height:7px"></div>

<div id="tbAddWrap" style="display:<?=$display[useAdd]?>">
<table id=tbAdd  border=2 bordercolor=#cccccc style="border-collapse:collapse">
<tr bgcolor=#f7f7f7 align=center>
	<td>옵션명 <font class=small>(예. 악세사리)</font></td>
	<td>항목명 <font class=small>(예. 열쇠고리)</font></td>
	<td>가격 <font class=small color=444444>(무료일때는 0원입력)</font></td>
	<td>구매시필수</td>
</tr>
<col valign=top style="padding-top:5px">
<col span=2><col align=center valign=top style="padding-top:5px">
<? foreach ($addopt as $k=>$v){ ?>
<tr>
	<td>
	<input type=text name=addoptnm[] value="<?=$addoptnm[$k]?>">
	<a href="javascript:void(0)" onClick="add_subadd(this)"><img src="../img/i_proadd.gif" align=absmiddle border=0></a>&nbsp;<a href="javascript:void(0)" onClick="del_subadd(this)"><img src="../img/btn_listdel.gif" align=absmiddle border=0/></a>
	</td>
	<td colspan=2>

	<table>
	<col><col align=center>
	<? foreach ($v as $v2){ ?>
	<tr>
		<td><input type=hidden name=addopt[sno][<?=$k?>][] value="<?=$v2[sno]?>"><input type=text name=addopt[opt][<?=$k?>][] value="<?=$v2[opt]?>" style="width:270px"> 선택시</td>
		<td>판매금액에 <input type=text name=addopt[addprice][<?=$k?>][]  size=9 value="<?=$v2[addprice]?>"> 원 추가<input type=hidden name=addopt[addno][] value="<?=$v2['addno']?>"></td>
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
		추가 옵션 데이터 생성.
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
		$rdopt[option] = str_replace("\n","",gd_json_encode($rdopt[option]));	// php4 환경이므로 임시 함수 추가 하였음.

		$arDoptExtend[] = $rdopt;
	}
	?>

	<div style="padding-top:10px">
		<select name="dopt_extend" style="width:125">
			<option value=''>옵션바구니 선택</option>
			<? foreach ($arDoptExtend as $k => $val) { ?>
			<option value='<?=$val[sno]?>'><?=$val[title]?></option>
			<? } ?>
		</select>&nbsp;&nbsp;<a href="javascript:fnApplyDoptExtendData()"><img src="../img/btn_optionbasket.gif" border="0" align="absmiddle"></a>
		<a href="javascript:popupLayer('popup.dopt_extend_list.php',850,600);"><img src="../img/btn_optionbasket_admin.gif" border="0" align="absmiddle"></a>
	</div>
</div>

<div class=title>상품별 배송비 적용<span>기본 관리 > 기본배송정책과 별도로 상품별로 배송비를 적용할 수 있습니다.<a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=3')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<table class="tb">
<col class=cellC width="120"><col class=cellL>
<tr>
	<td rowspan="6">상품별 배송비</td>
	<td><label class="noline"><input type="radio" name="delivery_type" value="0" <?=$checked[delivery_type][0]?> onclick="chk_delivery_type();"> 기본 배송 정책에 따름</label> <span class="extext">배송/택배사 설정>기본배송정책에서 설정한 기본배송비가 청구 됩니다.</span></td>
</tr>
<tr>
	<td><label class="noline"><input type="radio" name="delivery_type" value="1" <?=$checked[delivery_type][1]?> onclick="chk_delivery_type();"> 무료배송</label></td>
</tr>
<tr>
	<td><label class="noline"><input type="radio" name="delivery_type" value="2" <?=$checked[delivery_type][2]?> onclick="chk_delivery_type();" disabled> 상품별 배송비</label> <span style="display:none;" id="gdi2">&nbsp;<input type="text" class="line" name="goods_delivery2" value="<?=$data['goods_delivery']?>" size="8" onkeydown="onlynumber()" disabled>원</span> <span class="extext">고정 배송비와 수량별 배송비가 합쳐진 기존상품에만 적용된 구 기능으로 상품별 배송비 변경 및 추가 상품 등록시 선택할 수 없는 기능입니다.</span></td>
</tr>
<tr>
	<td><label class="noline"><input type="radio" name="delivery_type" value="4" <?=$checked[delivery_type][4]?> onclick="chk_delivery_type();"> 고정 배송비</label> <span style="display:none;" id="gdi4">&nbsp;<input type="text" class="line" name="goods_delivery4" value="<?=$data['goods_delivery']?>" size="8" onkeydown="onlynumber()">원</span> <span class="extext">수량/주문금액과 상관없이 고정 배송비가 청구 됩니다. 옵션별 상품 추가시에 동일 상품명과 묶음으로 배송비가 청구 됩니다.</span></td>
</tr>
<tr>
	<td><label class="noline"><input type="radio" name="delivery_type" value="5" <?=$checked[delivery_type][5]?> onclick="chk_delivery_type();"> 수량별 배송비</label> <span style="display:none;" id="gdi5">&nbsp;<input type="text" class="line" name="goods_delivery5" value="<?=$data['goods_delivery']?>" size="8" onkeydown="onlynumber()">원</span> <span class="extext">상품 수량에 따라 배송비가 증가하여 청구 됩니다.</span></td>
</tr>
<tr>
	<td><label class="noline"><input type="radio" name="delivery_type" value="3" <?=$checked[delivery_type][3]?> onclick="chk_delivery_type();"> 착불 배송비</label> <span style="display:none;" id="gdi3">&nbsp;<input type="text" class="line" name="goods_delivery3" value="<?=$data['goods_delivery']?>" size="8" onkeydown="onlynumber()">원</span></td>
</tr>
</table>
<div style="padding-top:10px">
<span class="extext">기본배송정책과 상품별 배송비 정책은 <a href="../basic/delivery.php" target=_blank><font class=extext_l>[기본관리 > 배송/택배사 설정]</font></a> 에서 관리 하실 수 있습니다.</span><br>
<span class="extext">※상품별 배송비 정책을 고려하여 신중하게 설정하여 주세요.</span>
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

			// 셀렉트 박스 옵션 삭제
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
			// alert('새로운 옵션은 새로고침 하셔야 반영됩니다.');

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

		// 옵션 갯수만큼.
		for (i=0;i<opt_data_size ;i++)
		{
			data = opt_data[i];

			// 옵션 행이 모자르면 추가.
			if (opt_data_size > addoptnm.length) add_addopt();
			else if (opt_data_size < addoptnm.length) del_addopt();

			// 값 입력.
			addoptnm[i].value = data.name;					// 옵션명
			document.getElementsByName('addoptreq['+i+']')[0].checked =  (data.require == true) ? true : false;					// 옵션별 구매시 필수 사항

			// 옵션 항목
			items = data.options;
			items_length = items.length;
			addopt_opt = document.getElementsByName('addopt[opt]['+i+'][]');
			addopt_price = document.getElementsByName('addopt[addprice]['+i+'][]');

			// 옵션의 항목 갯수만큼.
			for (j=0;j < items_length ;j++) {

				// 항목 행이 모자르면 추가.
				if (items_length > addopt_opt.length) add_subadd(addoptnm[i]);
				else if (items_length < addopt_opt.length) {
					var rpt = addopt_opt.length - items_length;
					for (k=0;k<rpt ; k++) del_subadd(addoptnm[i]);
				}

				// 값 입력.
				addopt_opt[j].value = items[j].name;		// 항목명
				addopt_price[j].value = items[j].price;		// 항목별 추가금액

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
			popupLayer('./popup.related.register.php?goodsno=' + this.goodsno,750,600);
		}
		,
		init : function() {

			$('el-related-goodslist').observe('click',function(){
				nsRelatedGoods.sort._set();
			});

			document.observe('keydown',function(){
				nsRelatedGoods.sort.move();
			});

		},
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

					// 데이터 가공
					r.type = r.r_type == 'couple' ? '1' : '0';
					r.img = '<img src="../../data/goods/' + r.img_s + '" width=40 />';
					r.runout = r.runout == 1 ? '<div style="padding-top:3px"><img src="../../data/skin/<?=$cfg[tplSkin]?>/img/icon/good_icon_soldout.gif"></div>' : '';
					r.price = comma(r.price);
					r.range = '';

					if (!r.r_start && !r.r_end) r.range = '지속노출';
					else {
						if (r.r_start) r.range = r.r_start;
						r.range += ' ~ ';
						if (r.r_end) r.range += r.r_end;
					}

					// 삽입
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
				alert('기간 설정할 관련상품을 선택해 주세요.');
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

			if (noti) alert('추가되었습니다.');

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
		,
		sort : {
			_row : null,
			_set : function() {	// click event;
				var self = nsRelatedGoods;

				var el = Element.up(event.srcElement,'tr');
				if (el.rowIndex != 0) {
					if (self.sort._row == el) {
						el.setStyle({backgroundColor:''});
						self.sort._row = null;
					}
					else {
						if (self.sort._row != null) self.sort._row.setStyle({backgroundColor:''});
						el.setStyle({backgroundColor:'#FFF4E6'});
						self.sort._row = el;
					}
				}
				self = null;
			},
			move : function() {	// keydown event;
				var self = nsRelatedGoods;

				var _k = event.keyCode;
				if (self.sort._row != null && (_k != 38 || _k != 40)) {

					// 이동
					var table = $('el-related-goodslist');
					var _oidx = self.sort._row.rowIndex;
					var _nidx = _oidx + (_k == 38 ? -1 : 1);
					if (_nidx >= table.rows.length) _nidx = 1;
					else if (_nidx < 1) _nidx = table.rows.length - 1;

					if (typeof table.moveRow == 'undefined') {
						// ff, chrome 등 지원 안함.

						return;
					}
					else {
						table.moveRow(self.sort._row.rowIndex, _nidx);
					}

					// relation 재 정렬
					_nidx = _nidx - 1;
					_oidx = _oidx - 1;

					if (_oidx == 0 && _nidx == (self.relation.size() - 1)) {
						self.relation.push( self.relation[_oidx] );
						self.relation.shift();
					}
					else if (_oidx == (self.relation.size() - 1) && _nidx == 0) {
						self.relation.unshift( self.relation[_oidx] );
						self.relation.pop();
					}
					else {
						var tmp = self.relation[_nidx];
						self.relation[_nidx] = self.relation[_oidx];
						self.relation[_oidx] = tmp;
					}

					Event.stop(event);
				}
				self = null;
			}
		}
	}
}();

Event.observe(document, 'dom:loaded', function(){
	nsRelatedGoods.init();
}, false);

</script>

<div style="border-bottom:3px #efefef solid;padding-top:20px"></div>

<!-- 관련상품 -->
<div class=title>관련상품<span>이상품과 관련있는 상품을 추천하세요 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=3')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<table class=tb>
	<col class=cellC width="120"><col class=cellL>
	<tr>
		<td>관련상품 노출방식</td>
		<td class="noline">
			<label><input type=radio name=relationis value=0 onfocus=blur() onclick="openLayer('divRefer','none');" <?=$checked[relationis][0]?>>자동 <font class=small color=#5A5A5A>(같은 분류 상품이 무작위로 보여짐)</font></label>
			<label><input type=radio name=relationis value=1 onfocus=blur() onclick="openLayer('divRefer','block');" <?=$checked[relationis][1]?>>수동 <font class=small color=#5A5A5A>(아래 직접 선택등록)</font></label>
		</td>
	</tr>

	</table>

	<div id=divRefer style="display:<?=$display[relationis]?>;margin-top:10px;">
	<input type="hidden" name="relation" id="el-relation" value="">

	<p style="margin:0 0 5px 0;">
		현재 관련상품 : <span id="el-related-goods-count"><?=sizeof($r_relation)?></span> 개
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
		<th><a href="javascript:void(0);" onClick="nsRelatedGoods.select();">선택</a></th>
		<th>서로등록</th>
		<th></th>
		<th>등록된 관련상품</th>
		<th>관련상품 설정기간</th>
		<th>등록일</th>
		<th>삭제</th>
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
			if (!$v[r_start] && !$v[r_end]) echo '지속노출';
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
		※ 서로등록<br>
		- <img src="../img/icn_1.gif" align="absmiddle"> : 본 상품이 서로등록 상품과 관련상품으로 동시에 등록됩니다. 삭제시 양쪽모두 자동으로 관련상품 목록에서 제외됩니다. <br>
		- <img src="../img/icn_0.gif" align="absmiddle"> : 본 상품이 관련상품으로 서로등록 되지 않으며, 본 상품의 관련상품 목록에만 등록됩니다. <br>
		- 관련상품 노출방식을 ‘자동’으로 설정할 경우, 서로등록과 상관없이 무조건 같은 분류의 상품이 랜덤으로 보여집니다.<br>

		※ 관련상품 노출형태 설정은 ‘상품관리 > 관련상품 노출 설정’ 에서 하실 수 있습니다.  <a href="../goods/related.php" target=_blank><font class=extext_l>[관련상품 노출 설정]</font></a> 바로가기
	</p>

	</div>

<div style="border-bottom:3px #efefef solid;padding-top:20px"></div>
<!--  qr code 설정 -->
<? if($qrCfg['useGoods'] == "y"){ ?>
<div class=title>QR Code 노출<span>상품 상세보기에 QR Code 를 보여줍니다. <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=2')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<div style="padding-bottom:5px" class=noline>
<input type=radio name=qrcode value=y onfocus=blur()  <?=$checked['qrcode']['y']?>>사용
<input type=radio name=qrcode value=n onfocus=blur()  <?=$checked['qrcode']['n']?>>사용안함
<?
		if($data['qrcode'] == 'y'){
			require "../../lib/qrcode.class.php";
			$QRCode = Core::loader('QRCode');
			echo  $QRCode->get_GoodsViewTag($goodsno, "goods_down");
		}
?>
</div>
<!-- qr code 설정 -->
<div style="border-bottom:3px #efefef solid;padding-top:20px"></div>
<? } ?>
<!-- 상품 디테일뷰 -->
<div class=title>상품이미지 돋보기 효과<span>상품상세이미지에 마우스를 오버하여 상품이미지를 확대하여 볼 수 있는 기능입니다. <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=3')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></span></div>
<div style="padding-bottom:5px" class=noline>
	<label><input type="radio" name="detailView" value="y" <?=$checked['detailView']['y']?> onclick="document.getElementById('detailViewCmt').style.display='block';" />사용</label>
	<label><input type="radio" name="detailView" value="n" <?=$checked['detailView']['n']?> onclick="document.getElementById('detailViewCmt').style.display='none';" />사용안함</label>
</div>
<div id='detailViewCmt' style="width:660px;border:solid 1px #cccccc; margin-bottom:5px; <? if($data['detailView']=='n') {?>display:none;<?}?>">
	<div style="margin:1px; background-color:#f8f8f8; padding:7px 10px; line-height:1.3em;">
		<div style="margin-bottom:2px">
		<div>※	<font class="small1" color="#444444">[상품이미지 돋보기 효과] 기능을 사용하기 위해서는, 아래 상품이미지 등록시 <font color="#FF0000">상세이미지</font>에 큰 사이즈의 이미지를 넣어야 합니다.<br />
		: 상세이미지에 마우스 오버시에 나타나는 확대이미지를 입력해야 합니다. 500px~800px 정도의 이미지를 권장합니다.</font></div>
		<div>※ <font class="small1" color="#444444">상세이미지 입력란에 이미지를 넣으면 자동으로 상세이미지와 마우스 오버시 보이는 큰 이미지가 등록됩니다.</font></div>
		<div>※ <font class="small1" color="#444444">확대(원본)이미지 입력란에 이미지를 넣고 [자동리사이즈 사용] 기능을 이용하여 상세이미지를 등록하시면, [상품이미지 돋보기 효과]
		기능은 사용이 불가능 합니다. 꼭, 상세이미지에 직접 등록하셔야 합니다.</font></div>
		</div>
	</div>
</div>
<div style="border-bottom:3px #efefef solid;padding-top:20px"></div>
<!-- 상품 디테일뷰 -->

<!-- 상품 이미지 -->
<div class=title>상품 이미지<span>아래 자동리사이즈 되는 기능을 활용하면 더욱 편리합니다. <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=3')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></span></div>

<!-- 이미지 등록방식 선택 -->
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>이미지등록방식</td>
	<td class="noline">
	<label><input type="radio" name="image_attach_method" value="file" onClick="fnSetImageAttachForm();" <?=$checked[image_attach_method][file]?>>직접 업로드</label>
	<label><input type="radio" name="image_attach_method" value="url"  onClick="fnSetImageAttachForm();" <?=$checked[image_attach_method][url]?>>이미지호스팅 URL 입력</label>

	</td>
</tr>
</table>

<div id="image_attach_method_upload_wrap">
<!-- 이미지 직접 업로드 -->
<div style="width:660px;border:solid 1px #cccccc; margin:5px 0 5px 0">
<div style="margin:1px; background-color:#f8f8f8; padding:7px 10px; line-height:1.3em;">
	<div style="margin-bottom:2px">
	<font class="small1" color="#444444">처음 상품이미지를 등록하신다면, 반드시 <a href="../goods/imgsize.php" target=_blank><img src="../img/i_imgsize.gif" border=0 align=absmiddle></a> 먼저 설정하세요!&nbsp;&nbsp;
	그리고 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=16')"><img src="../img/btn_resize_knowhow.gif" border=0 align=absmiddle></a> 을 꼭 필독하세요!</font></a>
	</div>
	<div>※ <font class="small1" color="#444444">자동리사이즈는 확대(원본)이미지만 등록하면 나머지 이미지들은 자동으로 리사이징 되는 간편한 기능입니다.</font></div>
	<div>※ <font class="small1" color="#444444">이미지파일의 용량은 모두 합해서 <?=ini_get('upload_max_filesize')?>B까지만 등록할 수 있습니다.</font></div>
</div>
</div>

<table class=tb>
<col class=cellC><col class=cellL><col class=cellC><col class=cellL>
<? foreach ($imgs as $k=>$v){ $t = array_map("toThumb",$v); ?>
<tr>
	<td>
	<?=$str_img[$k]?>
	<? if ($k!="l"){ ?>
	<div class=noline style="font:11px dotum;letter-spacing:-1px;"><input type=checkbox name=copy_<?=$k?> onclick="return chkImgCopy(this.form)" title="원본이미지를 이용한 자동리사이징"> <font class=extext><b>자동리사이즈 사용</b></font></div>
	<div style="padding-left:24px;"><font class=extext>(가로 <?=$cfg['img_'.$k]?> 픽셀)</font></div>
	<? } else { ?>
	<div class=noline style="font:11px dotum;letter-spacing:-1px;"><input type=checkbox onclick="return chkImgBox(this, this.form)" title="원본이미지를 이용한 자동리사이징"> <font class=extext><b>자동리사이즈 사용</b></font></div>
	<? } ?>
	</td>
	<td>

	<table id="tb_<?=$k?>">
	<col valign=top span=2>
	<? for ($i=0;$i<count($v);$i++){ ?>
	<tr>
		<td>
		<? if (!in_array($k,array("i","s","mobile"))){ if (!$i){ ?>
		<a href="javascript:addfld('tb_<?=$k?>')"><img src="../img/i_add.gif" align=absmiddle></a>
		<? } else { ?><font color=white>.........</font>
		<? }} else { ?><font color=white>.........</font>
		<? } ?>
		<span><input type=file name=img_<?=$k?>[] style="width:300px" onChange="preview(this)"></span>
		</td>
		<td>
		<? if ($v[$i]){ ?>
		<div style="padding:0 0" class=noline><input type=checkbox name=del[img_<?=$k?>][<?=$i?>]><font class=small color=#585858>삭제 (<?=$v[$i]?>)</font></div>
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

<? if ($k == 'l'){ ?>
</table>
<table class=tb>
<col class=cellC><col class=cellL><col class=cellC><col class=cellL>
<? } ?>

<? } ?>
</table>
<!-- //이미지 직접 업로드 -->
</div>

<div id="image_attach_method_link_wrap">
<!-- 이미지 호스팅 URL 입력 -->
	<div style="width:660px;border:solid 1px #cccccc; margin:5px 0 5px 0">
	<div style="margin:1px; background-color:#f8f8f8; padding:7px 10px; line-height:1.3em;">
		<div style="margin-bottom:2px">
		<font class="small1" color="#444444">이미지 호스팅에 등록된 이미지의 웹 주소를 복사하여 붙여 넣기 하시면 상품 이미지가 등록됩니다.</font> <br>
		<font class="small" color="#444444">ex) http://godohosting.com/img/img.jpg</font>
		</div>
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
<!-- //이미지 호스팅 URL 입력 -->
</div>
<script>
fnSetImageAttachForm();
</script>
<!--// 이미지 등록방식 선택 -->
<div style="border-bottom:3px #efefef solid;padding-top:30px"></div>

<!-- 상품 필수 정보 -->
<div class=title>상품 필수 정보<span>상품 필수(상세)정보를 등록합니다. <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=3')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<div style="width:660px;border:solid 1px #cccccc; margin:5px 0 5px 0">
<div style="margin:1px; background-color:#f8f8f8; padding:7px 10px; line-height:1.3em;">
	<div style="margin-bottom:5px">
	※ <a href="http://www.ftc.go.kr/policy/legi/legiView.jsp?lgslt_noti_no=112" target="_blank"><span class="small1" style="text-decoration:underline;">공정거래위원회에서 공고한 전자상거래법 상품정보제공 고시에 관한 내용을 필독해 주세요!</span></a>
	</div>
	<div class="small">전자상거래법에 의거하여 판매상품의 필수(상세)정보 등록이 필요합니다.</div>
	<div class="small"><a href="javascript:void(0);" onClick="nsInformationByGoods.overview()"><img src="../img/btn_gw_view.gif" align="absmiddle"></a>를 참고하여 상품필수 정보를 등록하여 주세요.</div>
	<div class="small">등록된 정보는 쇼핑몰 상품상세페이지에 상품기본정보 아래에 표형태로 출력되어 보여집니다.</div>
</div>
</div>

<div style="margin:10px;" class="small">
항목추가 : <a href="javascript:void(0);" onClick="nsInformationByGoods.add4row();"><img src="../img/btn_ad2.gif" align="absmiddle"></a> <a href="javascript:void(0);" onClick="nsInformationByGoods.add2row();"><img src="../img/btn_ad1.gif" align="absmiddle"></a> 항목과 내용 란에 아무 내용도 입력하지 않으면 저장되지 않습니다.
</div>

<table id="el-extra-info-table" class=tb style="table-layout:fixed;">
<col class="cellC"><col class="cellL"><col class="cellC"><col class="cellL"><col width="47">
<thead>
<tr>
	<th>항목</th>
	<th>내용</th>
	<th>항목</th>
	<th>내용</th>
	<th>-</th>
</tr>
</thead>
<tbody>
<?
$rowidx = 0;

if ($data['extra_info']) {

	$extra_info = gd_json_decode($data['extra_info']);
	$keys = array_keys($extra_info);

	if (!empty($keys)) {
		for ($i=min($keys),$m=max($keys);$i<=$m;$i++) {

			$next_key = $i + 1 <= $m ? $i + 1 : null;

			if (!isset($extra_info[$i])) continue;

			if ($i % 2 == 1 && !isset($extra_info[$next_key])) {
				$colspan = 3;
			}
			else {
				$colspan = 1;
			}

			$extra_info[$i]['title'] = htmlspecialchars(stripslashes($extra_info[$i]['title']));
			$extra_info[$i]['desc'] = htmlspecialchars(stripslashes($extra_info[$i]['desc']));

			if($i % 2 != 0) echo '<tr>';
			echo '
				<td><input type="text" name="extra_info_title['.$i.']" style="width:100%" value="'.$extra_info[$i]['title'].'"></td>
				<td '.($colspan > 1 ? 'colspan="'.$colspan.'"' : '').'><input type="text" name="extra_info_desc['.$i.']" style="width:100%" value="'.$extra_info[$i]['desc'].'"></td>
			';

			if ((!isset($extra_info[$next_key]) || $i % 2 == 0)) echo '<td><a href="javascript:void(0);" onClick="nsInformationByGoods.delrow();"><img src="../img/i_del.gif"></a></td></tr>'.PHP_EOL.PHP_EOL;

		}

		$rowidx = ($m % 2) == 0 ? $m : ++$m;	// index 보정
	}

}
?>
</tbody>
</table>

<script type="text/javascript">
var nsInformationByGoods = function() {
	return {

		adding : false,
		rowidx : <?=$rowidx?>,
		overview : function() {
			popup2('./information.by.goods.php',600,650,'0');
		},
		_addrow : function(size) {

			if (this.adding == true) return;

			this.adding = true;

			var o = $('el-extra-info-table');

			// size = 4 or 2;
			var tr = o.insertRow(-1),td;

			switch(size) {
				case 4:

					this.rowidx++;

					td = tr.insertCell(-1);
					td.innerHTML = '<input type="text" name="extra_info_title[' + this.rowidx + ']" style="width:100%">';

					td = tr.insertCell(-1);
					td.innerHTML = '<input type="text" name="extra_info_desc[' + this.rowidx + ']" style="width:100%">';

					this.rowidx++;

					td = tr.insertCell(-1);
					td.innerHTML = '<input type="text" name="extra_info_title[' + this.rowidx + ']" style="width:100%">';

					td = tr.insertCell(-1);
					td.innerHTML = '<input type="text" name="extra_info_desc[' + this.rowidx + ']" style="width:100%">';

					break;
				case 2:

					this.rowidx++;

					td = tr.insertCell(-1);
					td.innerHTML = '<input type="text" name="extra_info_title[' + this.rowidx + ']" style="width:100%">';

					td = tr.insertCell(-1);
					td.innerHTML = '<input type="text" name="extra_info_desc[' + this.rowidx + ']" style="width:100%">';
					td.colSpan = 3;

					this.rowidx++;

					//

					break;
			}

			td = tr.insertCell(-1);
			td.innerHTML = '<a href="javascript:void(0);" onClick="nsInformationByGoods.delrow();"><img src="../img/i_del.gif"></a>';

			this.adding = false;

		},
		delrow : function() {
			/*
			idx = el.rowIndex;
			var obj = document.getElementById('objCategory');
			obj.deleteRow(idx);
			*/
			var o = $('el-extra-info-table');
			//var tr = event.srcElement.up('tr');
			var tr = event.srcElement.parentElement.parentElement.parentElement;
			o.deleteRow(tr.rowIndex);
		},
		add4row : function() {
			this._addrow(4);
		},
		add2row : function() {
			this._addrow(2);
		},
		formValidator : function() {

			try
			{
				$$('input[name^="extra_info_title"], input[name^="extra_info_desc"]').each(function(el){
					if (! el.value.trim()) {
						el.focus();
						throw 'error';
					}
				});
			}
			catch (e) {
				alert('상품필수정보에 누락된 항목이 없는지 확인해 주세요.');
				return false;
			}

			return true;
		}
	}
}();
</script>

<div style="border-bottom:3px #efefef solid;padding-top:30px"></div>

<!-- 상품 설명 -->
<div class=title>상품 설명 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=3')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a>  <font class=small1 color=444444>아래 <img src="../img/up_img.gif" border=0 align=absmiddle hspace=2>를 눌러 이미지를 등록하세요.</font> &nbsp;<font color=E6008D>※</font><font class=small1 color=444444><font color=E6008D> 모든 이미지파일의 외부링크 (옥션, G마켓 등의 오픈마켓 포함)</font>는 지원되지 않습니다.</div>

<table border=1 bordercolor=#cccccc style="border-collapse:collapse">
<tr><td>
<table cellpadding=0 cellspacing=0 bgcolor=f8f8f8>
<tr><td style="padding:10 10 5 10"><font class=small1 color=444444><font color=E6008D>이미지 외부링크</font> 및 <font color=E6008D>오픈마켓</font> 판매를 위한 이미지를 등록하시려면 <font color=E6008D>반드시 이미지호스팅 서비스</font>를 이용하셔야 합니다.</a></td></tr>
<tr><td style="padding:0 10 7 10"><font class=small1 color=444444>이미지호스팅을 신청하셨다면 <a href="javascript:popup('http://image.godo.co.kr/login/imghost_login.php',980,700)" name=navi><img src="../img/btn_imghost_admin.gif" align=absmiddle></a>, 아직 신청안하셨다면 <a href="http://hosting.godo.co.kr/imghosting/service_info.php" target=_blank><img src="../img/btn_imghost_infoview.gif" align=absmiddle></a> 를 참조하세요!</td></tr>
</table>
</td></tr></table>

<div style="padding-top:5"></div>

<table class=tb>
<col class=cellC><col class=cellL><col class=cellC><col class=cellL>
<tr>
	<td>짧은설명</td>
	<td>
	<textarea name=shortdesc style="width:100%;height:20px;overflow:visible" class=tline><?=$data[shortdesc]?></textarea>
	</td>
</tr>
</table>
<div style="height:6px;font:0"></div>

<div class="noline" style="padding:5px 0 0 5px;border-bottom:3px solid #999;">
	<a name="tabLongdesc"></a>
	<input type="button" id="btn_longdesc_normal" value="일반 상세설명" style="width:85px;height:25px;cursor:hand;background-color:#999;color:#fff;" onclick="tabLongdescShow(this);" />
	<input type="button" id="btn_longdesc_mobile" value="모바일 상세설명" style="width:100px;height:25px;cursor:hand;background-color:#f0f0f0;color:#000;" onclick="tabLongdescShow(this);" />
</div>

<div id="ta_longdesc"><textarea name=longdesc style="width:100%;height:400px" type=editor><?=$data[longdesc]?></textarea></div>
<div id="ta_mlongdesc" style="display:none;"><textarea name="mlongdesc" style="width:100%;height:400px;" type=editor><?=$data[mlongdesc]?></textarea></div>
<div style="border-bottom:3px #efefef solid;padding-top:20px"></div>

<?php
$blogshop = new blogshop();


if($_GET['mode'] == "modify" && $data['useblog']=='y') {
	$goodsno = $_GET[goodsno];
	$blogshop_result = $blogshop->get_goods_from_godoshop_key($goodsno);
}
?>
<? if($blogshop->linked) : ?>
<!-- 블로그 영역 -->
<div class=title>블로그에 상품정보 보내기</span></div>
위 상품을 블로그의 상품포스트로 활용하시겠습니까?
<? if($blogshop_result['godoshop_key']) : ?>
	<input type="radio" name="useblog" value="y" class="null" onclick="_ID('blogarea').style.display='block';" checked>예
	<input type="radio" name="useblog" value="n" class="null" onclick="_ID('blogarea').style.display='none';">아니요
<? else: ?>
	<input type="radio" name="useblog" value="y" class="null" onclick="_ID('blogarea').style.display='block';">예
	<input type="radio" name="useblog" value="n" class="null" onclick="_ID('blogarea').style.display='none';" checked>아니요
<? endif; ?>

<br>
<? if($blogshop_result['godoshop_key']) : ?>
	<div id="blogarea" style='display:block'>
<? else: ?>
	<div id="blogarea" style='display:none'>
<? endif; ?>
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td width=120 nowrap>포스트 분류</td>
	<td>
		<input type="hidden" name="blog_cate_no" id="blog_cate_no" value="<?=$blogshop_result['cate']['cate_no']?>">
		<input type="text" name="blog_catnm" id="blog_catnm" style="width:150px" value="<?=$blogshop_result['cate']['catnm']?>" class="line" readonly>
		<input type="button" value=" 찾아보기... " onclick="popup('popup.blog.category.php',630,600)">
	</td>
</tr>
<tr>
	<td width=120 nowrap>포스트 주제</td>
	<td>
		<input type="hidden" name="blog_part_no" id="blog_part_no" value="<?=$blogshop_result['part_no']?>">
		<input type="text" name="blog_part_title" id="blog_part_title" style="width:150px" class="line" readonly
		value="<?=$blogshop_result['part_name']?>"
		>
		<input type="button" value=" 찾아보기... " onclick="popup('popup.blog.part.php',630,600)">
	</td>
</tr>
<tr>
	<td width=120 nowrap>포스트 제목</td>
	<td>상품이름과 동일합니다</td>
</tr>
<tr>
	<td width=120 nowrap>포스트 내용</td>
	<td>상품설명과 동일합니다</td>
</tr>

<tr>
	<td width=120 nowrap>트랙백</td>
	<td><input type="text" name="blog_trackback"  style="width:400px" class="line"> (http:// 로 시작 하는 트랙백 주소를 입력하세요)</td>
</tr>
<tr>
	<td width=120 nowrap>태그달기</td>
	<td>
	<? if($blogshop_result['tag']): ?>
		<input type="text" name="blog_tag" style="width:300px" value="<?=implode(',',$blogshop_result['tag'])?>" class="line"> (쉼표로 구분합니다)
	<? else: ?>
		<input type="text" name="blog_tag" style="width:300px" class="line"> (쉼표로 구분합니다)
	<? endif; ?>

	</td>
</tr>
</table>
<br><br>
<table border=1 bordercolor=#cccccc style="border-collapse:collapse">
<tr><td>
<table cellpadding=0 cellspacing=0 bgcolor=f8f8f8>
<tr><td style="padding:10 10 5 10"><font class=small1 color=444444><font color=E6008D>포스트 보내기</font>
메타블로그 사이트에 글을 보낼 수 있습니다.
<a href="http://landing.inicis.com/blogshop_landing/info/info_04_1.php?no=6" target="_blank"><img src="../img/btn_mblog.gif" hspace="30" align="absmiddle"></a>
</font>
</td></tr>
<tr><td style="padding:0 10 7 10"><font class=small1 color=444444>
올블로그, 블로그코리아, 믹시, 레뷰, Daum View 등의 여러 메타블로그에 글을 보낼 수 있어<br>
무료로 쇼핑몰을 홍보할 수 있습니다.
<a href="http://landing.inicis.com/blogshop_landing/info/info_04_1.php?no=5" target="_blank">
<img src="../img/btn_mblog_write.gif" style="margin-left:130px" align="absmiddle" ></a>
</font></td></tr>
</table>
</td></tr></table>
</div>
<div style="border-bottom:3px #efefef solid;padding-top:20px"></div>
<? endif; ?>

<!-- 관리 메모 -->
<div class=title>관리 메모 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=3')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<textarea name=memo style="width:100%;height:60px" class=tline><?=$data[memo]?></textarea>

<div class=button>

<input type=image src="../img/btn_<?=$_GET[mode]?>.gif" id="formBtn" >
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

<!-- 웹에디터 활성화 스크립트 -->
<script src="../../lib/meditor/mini_editor.js"></script>
<script>mini_editor("../../lib/meditor/");chk_delivery_type();color2Tag('selectedColor');</script>
<SCRIPT LANGUAGE="JavaScript" SRC="../proc/warning_disk_js.php"><!-- not_delete --></SCRIPT>
<? @include dirname(__FILE__) . "/../interpark/_goods_form.php"; // 인터파크_인클루드 ?>
