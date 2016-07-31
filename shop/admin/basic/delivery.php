<?

$location = "기본관리 > 배송/택배정책";
include "../_header.php";
include "../../conf/config.pay.php";

if(!file_exists("../../conf/area.delivery.php")){
	$dmode = 0;
	### 지역별 차등배송비 설정파일이 없을 경우 생성
	include "setAreaName.inc.php";
	echo("<script>location.reload();</script>");
	exit;
}
if($set['delivery']['over'] &&!$set['delivery']['overAdd'] ){
	$dmode = 3;
	### 추가배송비가 없을경우 추가배송비로 변경
	include "setAreaName.inc.php";
	echo("<script>location.reload();</script>");
	exit;
}
@include "../../conf/area.delivery.php";
$arr_area = explode('|',$r_area[deliveryArea]);

$tmp = explode('|',$set['r_delivery']['title']);
foreach($tmp as $v) $r_set[$v] = $set[$v];
$set = $set['delivery'];
if(!$set['deliverynm'])$set['deliverynm'] = '기본배송';

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

### 배송업체 정보
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

//new 지역별 추가 배송비
@include './popup.newAreaDeliveryLib.func.php';
$newAreaDeliveryCount = newAreaTotalCount();
$skinPatchMsg = newAreaSkinCheck();

// 무료배송 시 지역별 추가 배송비
if (isset($set['add_extra_fee']) === true) {
	$tmp_add_extra_fee					= $set['add_extra_fee'];	// 기존 레거시 보장, 해당 값은 더이상 사용 안함
} else {
	$tmp_add_extra_fee					= 1;						// 기본 값은 지역별 추가 배송비 받음으로 처리
}
if (isset($set['add_extra_fee_basic']) === false) {					// "기본 배송정책에 의한 조건부 무료인 경우"에서 기본값 (기존 레거시 또는 지역별 추가 배송비 받음)
	$set['add_extra_fee_basic']			= $tmp_add_extra_fee;
}
if (isset($set['add_extra_fee_free']) === false) {					// "무료배송 상품 주문시"인 경우 기본값 (기존 레거시 또는 지역별 추가 배송비 받음)
	$set['add_extra_fee_free']			= $tmp_add_extra_fee;
}
if (isset($set['add_extra_fee_memberGroup']) === false) {			// "회원 그룹 혜택에 의한 배송비 무료인 경우"에서 기본값 (기존 레거시 또는 지역별 추가 배송비 받음)
	$set['add_extra_fee_memberGroup']	= $tmp_add_extra_fee;
}
unset($tmp_add_extra_fee);

// 지역별 추가 배송비 다중 부과 기본값
if (isset($set['add_extra_fee_duplicate_free']) === false) {
	$set['add_extra_fee_duplicate_free']		= 1;				// 무료배송 상품 주문시 기본값은 "항목별 중복 부과" 로 처리
}
if (isset($set['add_extra_fee_duplicate_fixEach']) === false) {
	$set['add_extra_fee_duplicate_fixEach']		= 1;				// 고정 배송비 상품 주문시 기본값은 "항목별 중복 부과" 로 처리
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
	} else alert("수정할 택배사를 선택해주세요");
}

function addOver()
{
	var idx = tbOver.rows.length / 2;
	oTr = tbOver.insertRow();
	oTd = oTr.insertCell();
	var tmp = "아래 지역의 배송비를 <input type=text name=\"overAdd[]\" value=\"\" class=\"rline\"> 원을 추가 합니다. <a href=\"javascript:popup('popup.areaDelivery.php?idx="+idx+"',300,300);\"><img src=\"../img/btn_area_search.gif\" align=\"absmiddle\" value=\"지역검색하기\" /></a><div class=extext style=\"padding-top:5px\">(반드시 <b>'지역검색하기'</b>를 눌러서 지역을 추가하세요)</font></div>";
	oTd.innerHTML = tmp;
	oTd = oTr.insertCell();
	oTd.innerHTML = "<a href='javascript:void(0)' onClick='delOver(this)'><img src='../img/i_del.gif'></a>";
	oTr = tbOver.insertRow();
	oTd = oTr.insertCell();
	oTd.colSpan = 2;
	oTd.innerHTML = "<textarea name=overZipcodeName[] style='width:100%;height:50px' required label='차등지역우편번호'></textarea>";
	requiredOver();
}

function addOverZip()
{
	var tbl = document.getElementById('tbOverZip');
	var idx = tbl.rows.length / 2;
	oTr = tbl.insertRow();
	oTd = oTr.insertCell();
	var tmp = "아래 지역의 배송비를 <input type=text name=\"overAddZip[]\" value=\"\" class=\"rline\"> 원을 추가 합니다.";
	oTd.innerHTML = tmp;
	oTd = oTr.insertCell();
	oTd.innerHTML = "<a href=\"javascript:void(0)\" onClick=\"delOverZip(this)\"><img src=\"../img/i_del.gif\"></a>";
	oTr = tbl.insertRow();
	oTd = oTr.insertCell();
	oTd.colSpan = 2;
	oTd.innerHTML = "<div><a href=\"javascript:popup('../proc/popup_zipcode.delivery.php?idx="+idx+"',400,300)\"><img src=\"../img/btn_zipcode.gif\" border=\"0\" align=\"absmiddle\"></a> <input type='text' name='areaZip1[]' size=\"6\" readonly>부터 <input type='text' name='areaZip2[]' size=\"6\" readonly>까지</div><div class=extext style=\"padding-top:5px\">(반드시 <b>'우편번호검색'</b>을 눌러서 지역을 확인 후 추가하세요)</div>";
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
	oTd.innerHTML = "총 구매액이 <input type=text name=\"r_free[]\" size=9 class=right onkeydown=\"onlynumber();\"> 원 이상일 때 배송비 무료, 미만일 때 <select name=\"r_deliType[]\" onchange=\"chkDeliveryType(this)\"><option value=\"선불\">선불</option><option value=\"후불\">착불</option></select><span> <input type=text name=\"r_default[]\" size=8 class=right onkeydown=\"onlynumber()\"> 원 배송비 부과</span><span style=\"display:none;\"> 배송 메시지 : <input type=\"text\" name=\"r_default_msg[]\" size=\"18\" class=\"lline\"></span>";

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
			overAdd[i].setAttribute('label', '차등배송비');
			zipcode[i].setAttribute('required', '');
			zipcode[i].setAttribute('label', '차등지역');
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
			overAddZip[i].setAttribute('label', '차등배송비');
			zipcode1[i].setAttribute('required', '');
			zipcode1[i].setAttribute('label', '차등지역');
			zipcode2[i].setAttribute('required', '');
			zipcode2[i].setAttribute('label', '차등지역');
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

	// 백그라운드 레이어
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

	// 내용프레임 레이어
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

	/*** 타이틀바 레이어 ***/
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

	/*** 아이프레임 ***/
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

<div class="title title_top">배송정책<span>배송비용 및 배송관련 정책을 정하세요</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=3')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a></div>


<div style="padding: 20px 0px 5px 13px"><b>1. 기본 배송정책</b> <font class=extext>(배송방법을 여러개 등록할 수 있습니다) (예. 당일배송, 퀵서비스 등)</font></div>

<table width=100% cellpadding=0 cellspacing=0 border=0 id="tbl_delivery">
<tr>
	<td class=rnd colspan=12></td>
</tr>
<tr class=rndbg>
	<th width="50">순번</th>
	<th width="120">배송방법</th>
	<th>배송비</th>
	<th width="50">삭제</th>
</tr>
<tr>
<td class=rnd colspan=12></td>
</tr>
<tr><td colspan=20 height=10></td></tr>
<?
if($set['deliveryType'] == '선불' || !$set['deliveryType'])$selected['deliveryType'][0] = " selected";
else $selected['deliveryType'][1] = " selected";
?>
<tr height=30>
	<td class="center">1</td>
	<td class="center ver81"><input type=text name="deliverynm" size=10 value="<?=$set['deliverynm']?>" class="line" required></td>
	<td class="ver81">총 구매액이 <input type=text name="free" value="<?=$set['free']?>" size=9 class="rline" onkeydown="onlynumber();"> 원 이상일 때 배송비 무료, 미만일 때
			<?if ( !preg_match( "/^rental_mxfree/i", $godo[ecCode] ) ){?><select name="deliveryType" onchange="chkDeliveryType(this)">
			<option value="선불"<?=$selected['deliveryType'][0]?>>선불</option>
			<option value="후불"<?=$selected['deliveryType'][1]?>>착불</option>
			</select><?}else{?><input type="hidden" name="deliveryType" value="선불" class="rline"><?}?><span style="display:none;"> <input type="text" name="default" value="<?=$set['default']?>" size="8"  class="rline" onkeydown="onlynumber()"> 원 배송비 부과</span><span style="display:none;"> 배송메세지 : <input type="text" name="default_msg" value="<?=$set['default_msg']?>" size="20" style="width:120" class="lline" ></span>
	</td>
	<td class="center">-</td>
</tr>
<?
	if ( !preg_match( "/^rental_mxfree/i", $godo[ecCode] ) ){
	$num=1;
	foreach($r_set as $k => $v){
		$num++;
		$selected[r_deliType][0] = $selected[r_deliType][1] = "";
		if($v[r_deliType]=='선불') $selected[r_deliType][0] = " selected";
		else  $selected[r_deliType][1] = " selected";

		if($k){
	?>

<tr height=30>
	<td class="center"><?=$num?></td>
	<td class="center"><input type=text name="r_delivery[]" size=10 value="<?=$k?>" class="line" required></td>
	<td class="ver81">총 구매액이 <input type=text name="r_free[]" size=9 class="rline" value="<?=$v[r_free]?>" onkeydown="onlynumber();"> 원 이상일 때 배송비 무료, 미만일 때
			<select name="r_deliType[]" onchange="chkDeliveryType(this)">
			<option value="선불"<?=$selected[r_deliType][0]?>>선불</option>
			<option value="후불"<?=$selected[r_deliType][1]?>>착불</option>
			</select><span style="display:none;"> <input type=text name="r_default[]" size=8 class="rline" value="<?=$v['r_default']?>" onkeydown="onlynumber()"> 원 배송비 부과</span><span style="display:none;"> 배송메세지 : <input type="text" name="r_default_msg[]" value="<?=$v['r_default_msg']?>" size="20" style="width:120" class="lline"></span>
	</td>
	<td class="center"><a href="javascript:void(0)" onClick="delDelivery(this)"><img src="../img/btn_delete_new.gif"></a></td>
</tr>
<?}}}?>



</table>
<table width=100%>
<tr><td colspan=20 height=10></td></tr>
<tr><td colspan=20 height=1 bgcolor=e2e2e2></td></tr>
<tr>
	<td class="extext"><div style="padding-top:4px"></div>* 착불설정시 착불메시지는 필수로 입력하셔야 하며 하단의 등록 버튼을 클릭하셔야 설정이 적용됩니다.	</td>
</tr>
</table>
<?if ( !preg_match( "/^rental_mxfree/i", $godo[ecCode] ) ){?><div style="padding:10px 0px 20px 0px" align="center"><a href="javascript:addDelivery();"><img align="absmiddle" src="../img/btn_delivery_plus.gif"  class="null" /></a></div><?}?>


<br>

<div style="padding: 10px 0px 5px 13px"><b>2. 상품별 배송정책</b> <font class=extext>(상품별로 배송비를 책정할 수 있습니다)</font></div>

<table class=tb>
<col class=cellC><col class=cellL>
<?if ( !preg_match( "/^rental_mxfree/i", $godo[ecCode] ) ){?>
<tr>
	<td>무료배송 상품</td>
	<td>
		<div><input type="radio" name="freeDelivery" value="0" class="null" <?=$checked[freeDelivery][0]?>>무료배송 상품을 같이 주문했을 경우, 무료배송상품만 배송비를 무료로 합니다.</div>
		<div><input type="radio" name="freeDelivery" value="1" class="null" <?=$checked[freeDelivery][1]?>>무료배송 상품을 같이 주문했을 경우, 해당 주문건의 배송비를 함께 무료로 합니다.</div>
	</td>
</tr>
<tr>
	<td>상품별 배송비</td>
	<td>
		<div><input type="radio" name="goodsDelivery" value="0" class="null" <?=$checked[goodsDelivery][0]?>>상품을 2개이상 주문시, 상품별 배송비와 기본배송비를 합산한 금액을 배송비로 합니다.</div>
		<div><input type="radio" name="goodsDelivery" value="1" class="null" <?=$checked[goodsDelivery][1]?>>상품을 2개이상 주문시, 상품별 배송비와 기본배송비 중 더 큰 배송비로 합니다.</div>
		<div><input type="radio" name="goodsDelivery" value="2" class="null" <?=$checked[goodsDelivery][2]?>>상품을 2개이상 주문시, 상품별 배송비의 총합과 기본배송비 중 더 큰 배송비로 합니다.</div>
	</td>
</tr>
<input type=hidden name='basis' value='<?=$set[basis]?>' />
<?}else{?>
<input type=hidden name='basis' value='0' />
<?}?>
</table>

<div style="padding: 25px 0px 5px 13px"><b>3. 지역별 배송정책</b> <font class=extext>(도서산간 등 지역별로 배송금액을 지정할 수 있습니다)</font></div>

<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td style="width: 130px;">
		<div style="padding-left:10">지역별 배송금액</div>
		<div style="font-weight:normal"><input type="radio" name="area_deli_type" value='2' class="null" onclick="chkAreaDeli()" <?=$checked[area_deli_type][2]?> />지역명/도로명 설정</div>
		<div style="font-weight:normal"><input type="radio" name="area_deli_type" value='0' class="null" onclick="chkAreaDeli()" <?=$checked[area_deli_type][0]?> />지역명으로 설정</div>
		<div style="font-weight:normal"><input type="radio" name="area_deli_type" value='1' class="null" onclick="chkAreaDeli()" <?=$checked[area_deli_type][1]?> />우편번호로 설정</div>
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
		<td valign="top" style="padding-top: 6px;">현재 <span class="extext_t" style="font-weight: bold;"><?php echo number_format($newAreaDeliveryCount); ?>개</span>의 지역이 등록되어 있습니다.</td>
	</tr>
	<tr>
		<td colspan=2>
			<table width="100%" cellpadding="0" cellspacing="0" border="0" style="font: 9pt dotum;">
			<colgroup>
				<col width="30" />
				<col width="*" />
			</colgroup>
			<tr height="25">
				<td style="padding: 10px 0px 5px 0px; font-weight: bold;" colspan="2">※지역명/도로명 설정이란?</td>
			</tr>
			<tr height="25">
				<td style="padding-left: 10px;"><img src="../img/icon_lotnumber.gif" /></td>
				<td style="padding: 3px 0px 0px 3px;">지번 주소는 기본! 도로명 신주소까지 등록할 수 있습니다!</td>
			</tr>
			<tr height="25">
				<td style="padding-left: 10px;"><img src="../img/icon_post.gif" /></td>
				<td style="padding: 3px 0px 0px 3px;">복잡한 우편번호 설정을 하지 않아도 주소지 입력만으로 설정이 완료됩니다!</td>
			</tr>
			<tr height="25">
				<td style="padding-left: 10px;"><img src="../img/icon_area.gif" /></td>
				<td style="padding: 3px 0px 0px 3px;">'시도구(군) 읍면동(리)'의 세부 지역까지 제한 없이 지원합니다!</td>
			</tr>
			<tr height="25">
				<td style="padding-left: 10px;"><img src="../img/icon_excel.gif" /></td>
				<td style="padding: 3px 0px 0px 3px;">엑셀파일을 이용한 일괄 등록을 지원합니다!</td>
			</tr>
			</table>
			<?php if($skinPatchMsg === false){ ?>
			<div class="extext_t">※ <strong><u>[관련 스킨패치 적용 필요]</u></strong> -> 스킨 패치를 적용하지 않을 경우 ‘주문서 작성‘페이지에 지역별 추가배송비가 노출되지 않습니다.(단, 결제 시 지역별 추가배송비가 결제금액에 포함되므로 <span style="text-decoration: underline;">고객혼란 방지를 위하여 반드시 스킨 패치를 적용</span>하여 주시기 바랍니다.)&nbsp;<a href="http://www.godo.co.kr/customer_center/patch.php?sno=2113" target="_blank"><img src="../img/btn_detail.gif" style="border: 0px; vertical-align: middle;" /></a>
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
			<td>아래 지역의 배송비에 <input type="text" name="overAdd[]" value="<?=$overAdd[$k]?>" class="rline"> 원을 추가 합니다.</td>
			<td><a href="javascript:popup('popup.areaDelivery.php?idx=<?=$k?>',300,300);"><img src="../img/btn_area_search.gif" align="absmiddle" value="지역검색하기" /></a></td>
		</tr>
		</table>
		<div class=extext style="padding-top:5px">(반드시 <b>'지역검색하기'</b>를 눌러서 지역을 추가하세요)</font></div>
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
		<td>아래 지역의 배송비에 <input type="text" name="overAddZip[]" value="<?=$overAddZip[$k]?>" class="rline"> 원을 추가 합니다.</td>
		<td><? if (!$k){ ?><a href="javascript:addOverZip()"><img src="../img/btn_placeadd.gif"></a><? } else { ?><a href="javascript:void(0)" onClick="delOverZip(this)"><img src="../img/i_del.gif"></a><? } ?></td>
	</tr>
	<tr>
		<td>
		<div><a href="javascript:popup('../proc/popup_zipcode.delivery.php?idx=<?=$k?>',500,340)"><img src="../img/btn_zipcode.gif" border="0" align="absmiddle"></a> <input type='text' name='areaZip1[]' size="6" value="<?=$areaZip1[$k]?>" readonly>부터 <input type='text' name='areaZip2[]' value="<?=$areaZip2[$k]?>" size="6" readonly>까지</div>
		<div class=extext style="padding-top:5px">(반드시 <b>'우편번호검색'</b>을 눌러서 지역을 확인 후 추가하세요)</div>
		</td>
	</tr>
	<? }} ?>
	</table>

<div class="extext_t">* 일반적으로 지역별 배송금액을 차별화하는 경우는 섬, 도서산간 등에 해당됩니다. ( 예: 제주도,울릉도 등)</div>

	</td>
</tr>
<tr>
	<td>무료배송 시 <br>지역별 추가 배송비</td>
	<td>
		<table class="tb">
		<col class="cellC"><col>
		<tr>
			<td width="130">무료배송비 상품 주문시</td>
			<td>
				<label><input type="radio" name="add_extra_fee_free" value="1" <?=$set['add_extra_fee_free'] == '1' ? 'checked' : '' ?> /> 지역별 추가 배송비 받음</label>
				<div class="extext" style="padding-top:5px">무료배송비 상품 주문시에도 지역별 추가 배송비를 받습니다.</div>

				<label><input type="radio" name="add_extra_fee_free" value="0" <?=$set['add_extra_fee_free'] == '0' ? 'checked' : '' ?> /> 지역별 추가 배송비 받지 않음</label>
				<div class="extext" style="padding-top:5px">무료배송비 상품 주문시 지역별 추가 배송비를 받지 않습니다.</div>
			</td>
		</tr>

		<tr>
			<td>기본 배송정책에 의한<br>조건부 무료인 경우</td>
			<td>
				<label><input type="radio" name="add_extra_fee_basic" value="1" <?=$set['add_extra_fee_basic'] == '1' ? 'checked' : '' ?> /> 지역별 추가 배송비 받음</label>
				<div class="extext" style="padding-top:5px">기본 배송정책에 의해 일정금액 이상일때 배송비 무료일 경우에도 지역별 추가 배송비를 받습니다.</div>

				<label><input type="radio" name="add_extra_fee_basic" value="0" <?=$set['add_extra_fee_basic'] == '0' ? 'checked' : '' ?> /> 지역별 추가 배송비 받지 않음</label>
				<div class="extext" style="padding-top:5px">기본 배송정책에 의해 일정금액 이상일때 배송비 무료일 경우 지역별 추가 배송비를 받지 않습니다.</div>
			</td>
		</tr>

		<tr>
			<td>회원 그룹 혜택에 의한<br>배송비 무료인 경우</td>
			<td>
				<label><input type="radio" name="add_extra_fee_memberGroup" value="1" <?=$set['add_extra_fee_memberGroup'] == '1' ? 'checked' : '' ?> /> 지역별 추가 배송비 받음</label>
				<div class="extext" style="padding-top:5px">무료배송 혜택이 제공되는 회원그룹 회원의 주문시에도 지역별 추가 배송비를 받습니다.</div>

				<label><input type="radio" name="add_extra_fee_memberGroup" value="0" <?=$set['add_extra_fee_memberGroup'] == '0' ? 'checked' : '' ?> /> 지역별 추가 배송비 받지 않음</label>
				<div class="extext" style="padding-top:5px">무료배송 혜택이 제공되는 회원그룹 회원의 주문시 지역별 추가 배송비를 받지 않습니다.</div>
			</td>
		</tr>
		</table>
	</td>
</tr>


<tr>
	<td>배송정책에 따른<br>지역별 추가 배송비
	<td>
		<!-- 개별배송상품 (더이상 사용하지 않음) 에 대한 기존 레거시 보장용 -->
		<input type="hidden" name="add_extra_fee_duplicate_each" value="1" />

		<table>
		<tr>
			<td>무료 배송비 상품 2종류 이상 주문시</td>
			<td><label><input type="radio" name="add_extra_fee_duplicate_free" value="1" <?=$set['add_extra_fee_duplicate_free'] == '1' ? 'checked' : '' ?> /> 상품 종류별로 부과</label></td>
			<td><label><input type="radio" name="add_extra_fee_duplicate_free" value="0" <?=$set['add_extra_fee_duplicate_free'] == '0' ? 'checked' : '' ?> /> 한번만 부과</label></td>
		</tr>
		<tr>
			<td>고정 배송비 상품 2종류 이상 주문시</td>
			<td><label><input type="radio" name="add_extra_fee_duplicate_fixEach" value="1" <?=$set['add_extra_fee_duplicate_fixEach'] == '1' ? 'checked' : '' ?> /> 상품 종류별로 부과</label></td>
			<td><label><input type="radio" name="add_extra_fee_duplicate_fixEach" value="0" <?=$set['add_extra_fee_duplicate_fixEach'] == '0' ? 'checked' : '' ?> /> 한번만 부과</label></td>
		</tr>
		<tr>
			<td colspan="3"><div class="extext" style="padding-top:5px">무료배송비, 고정배송비 상품 2종류 이상 주문시 지역별 추가 배송비 부과 방법을 설정합니다.</div></td>
		</tr>
		</table>

		<div class="extext" style="padding-top:15px">※ 기본배송비 상품 주문시 (조건부 무료배송 포함) 수량에 관계없이 지역별 추가 배송비는 한번만 받습니다.</div>
		<div class="extext" style="padding-top:5px">※ 착불배송비 상품 주문시 지역별 추가 배송비는 받지 않습니다.</div>
		<div class="extext" style="padding-top:5px">※ 수량별배송비 상품 주문시 주문 수량별로 지역별 추가 배송비를 받습니다.</div>
	</td>
</tr>
</table>

<div style="padding: 25px 0px 5px 13px"><b>4. 배송비 적용기준</b> <font class=extext>(기본 배송정책 배송비 적용 기준을 설정합니다.)</font></div>
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>배송비 적용</td>
	<td>
		<div><input type="radio" name="deliveryOrder" value="0" class="null" <?=$checked['deliveryOrder']['0']?>>주문상품의 결제가 기준</div>
		<div><input type="radio" name="deliveryOrder" value="1" class="null" <?=$checked['deliveryOrder']['1']?>>주문상품의 정상가 기준</div>
	</td>
</tr>
</table>
<div class="extext_t">* 주문상품의 결제가 기준 선택 시 주문 상품금액에서 할인(쿠폰할인,회원할인,상품할인)이 적용된 금액을 기준으로 배송정책을 적용합니다.<br />* 주문상품의 정상가 기준 선택 시 주문 상품금액을 기준으로 배송정책을 적용합니다.</div>
<br />

<div class=title title_top style="position:relative;padding-bottom:15px">
<font color=black>택배사/배송추적 설정</font>
<span>사용하는 택배사를 선택하고 배송추적 주소를 넣으세요</font></span>
<a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=3')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a>
<div style="position:absolute;left:100%;width:231px;height:44px;margin-left:-240px;margin-top:-15px"><a href="../order/post_introduce.php"><img src="../img/btn_postoffic_reserve_go.gif"></a></div>
</div>
<div class="rndline2"></div>
<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
<td>
	<table cellpadding="0" cellspacing="10" border="0">
	<tr>
	<td>&nbsp;&nbsp;<img src="../img/arrow_downorg.gif" align=absmiddle> <font class=man>택배사 전체리스트 </font><font class=small1>(더블클릭하세요)</font></td>
	<td></td>
	<td>&nbsp;&nbsp;<img src="../img/arrow_downorg.gif" align=absmiddle> <font class=man>이용 택배사 </font><font class=small1>(삭제시 더블클릭)</font></td>
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
	<a href="javascript:move('right')"><font class="color_r">▶</font></a><p>
	<a href="javascript:move('left')"><font class="color_l">◀</font></a>
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
	<td style="padding-left:3px;" class="extext"><!--<font class=small1 color=444444>* 리스트에 계약하신 택배사가 없다면&nbsp; <a href="javascript:registerDelivery()"><img src="../img/btn_deliadd.gif" border=0 vspace=2 align=absmiddle></a> 하세요.<br>-->* 배송추적주소를 수정하려면 왼쪽 택배사 전체리스트에서 택배사를 선택하고&nbsp; <a href="javascript:modifyDelivery()"><img src="../img/btn_deliedit.gif" border=0 vspace=2 align=absmiddle></a> 을 누르세요.
	<div style="padding-top:4px"></div>
	* 배송추적이란 주문한 고객이 마이페이지에서 직접 배송상태를 확인하는 것입니다.
	<div style="padding-top:4px"></div>
	* 맨 처음 선택되어진 배송사가 기본 배송사 입니다.
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