<?

$location = "네이버 마일리지 > 네이버 마일리지 설정/관리";
include "../_header.php";

if (get_magic_quotes_gpc()) {
	stripslashes_all($_POST);
	stripslashes_all($_GET);
}

$out = readurl("http://gongji.godo.co.kr/userinterface/serviceIp/naver_mileage.php");
$arr = explode(chr(10),$out);
$ret = 0;
foreach($arr as $v){
	$v = trim($v);
	if($v&&preg_match('/'.$v.'/',$_SERVER['REMOTE_ADDR']))$ret = 1;
}

if($_SERVER['REQUEST_METHOD']=='POST')
{
	if(class_exists('NaverCommonScript', false)===false) include dirname(__FILE__).'/../../lib/naverCommonInflowScript.class.php';
	$naverCommonInflowScript = new NaverCommonInflowScript();
	if($naverCommonInflowScript->isEnabled===false) exit('
	<script type="text/javascript">
	alert("네이버 공통인증키를 저장하셔야 설정하실수 있습니다.");
	location.href="ncash_set.php";
	</script>');
}

if($_POST['save'] == 'setting'){

	$e_exceptions = serialize((array)$_POST['e_exceptions']);
	$e_category = serialize((array)$_POST['e_category']);

	$_POST['api_id'] = trim($_POST['api_id']);
	$_POST['api_key'] = trim($_POST['api_key']);

	if($ret){
		$config_ncash = array(
			'useyn'=>(string)$_POST['useyn'],
			'api_id'=>(string)$_POST['api_id'],
			'api_key'=>(string)$_POST['api_key'],
			'save_mode'=>(string)$_POST['save_mode'],
			'e_exceptions'=>$e_exceptions,
			'e_category'=>$e_category,
			'status'=>$_POST['status'],
			'mobileStatus'=>$_POST['mobileStatus'],
			'exceptionyn'=>$_POST['exceptionyn'],
		);
	}else{
		$config_ncash = array(
			'api_id'=>(string)$_POST['api_id'],
			'api_key'=>(string)$_POST['api_key'],
			'save_mode'=>(string)$_POST['save_mode'],
			'e_exceptions'=>$e_exceptions,
			'e_category'=>$e_category,
		);
	}
	$config->save('ncash',$config_ncash);

	echo "
	<script>
	alert('저장되었습니다');
	location.href='ncash_set.php';
	</script>
	";
	exit;
}

$e_exceptions = $e_category = "";

$load_config_ncash = $config->load('ncash');

if($_POST['getAccumRate'] == 'Y' && $load_config_ncash['useyn'] == 'Y'){
	$naverNcash = Core::loader('naverNcash', true);
	$naverNcash->getAccumRate();
	echo "<script>location.href='ncash_set.php';</script>";
	exit;
}

$load_e_exceptions = unserialize($load_config_ncash['e_exceptions']);
$e_category = unserialize($load_config_ncash['e_category']);

if($load_e_exceptions){
	$res = $db->query("select goodsno, goodsnm from gd_goods where goodsno in (".implode(',',$load_e_exceptions).")");
	while($tmp = $db->fetch($res)) $e_exceptions[] = $tmp;
}

$checked['useyn'][$load_config_ncash['useyn']] = "checked";
$checked['save_mode'][$load_config_ncash['save_mode']] = "checked";
$checked['status'][$load_config_ncash['status']] = "checked";
$checked['mobileStatus'][$load_config_ncash['mobileStatus']] = 'checked';
$checked['exceptionyn'][$load_config_ncash['exceptionyn']] = "checked";

?>

<?php include dirname(__FILE__).'/../naverCommonInflowScript/configure.php'; ?>

<style type="text/css">
img {border:none;}
</style>
<script type="text/javascript">
var iciRow, preRow, nameObj;
function chkYn()
{
	var chk = document.getElementsByName('useYn');
	var lyr = document.getElementById('sub');
	lyr.disabled=true;
	for(var i=0;i<chk.length;i++)
	{
		if(chk[0].checked == true) lyr.disabled=false;
	}
}
function copy_txt(val)
{
	window.clipboardData.setData('Text', val);
}
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
	var els = document.forms['fm'][name+'[]'];
	for (i=0;i<els.length;i++) if (els[i].value) category = els[i].value;
	var ifrm = document.getElementById("ifrm_" + name);
	var goodsnm = eval("document.forms['fm'].search_" + name + ".value");
	ifrm.contentWindow.location.href = "../goods/_goodslist.php?name=" + name + "&category=" + category + "&goodsnm=" + goodsnm;
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
	if (objTop.moveRow) {
		objTop.moveRow(iciRow.rowIndex,nextPos);
	} else {
		if(idx > 0 && nextPos != 0) nextPos += idx;
		var beforeRow = objTop.rows[nextPos];
		iciRow.parentNode.insertBefore(iciRow, beforeRow);
	}
	react_goods(nameObj);
}
function keydnTree(e)
{
	if (iciRow==null) return;
	e = e ? e : event;
	switch (e.keyCode){
		case 38: moveTree(-1); return false;
		case 40: moveTree(1); return false;
	}
}
function checkForm(f)
{
	var r_useyn = document.getElementsByName('useyn');
	for( var i = 0 ; i < r_useyn.length; i++ ){
		if(r_useyn[i].checked){
			useyn = r_useyn[i].value;
		}
	}
	if(useyn == 'Y'){
		if(document.getElementById('api_id').value == ''){ alert('API ID를 입력해주세요.'); return false; }
		if(document.getElementById('api_key').value == ''){ alert("API Key를 입력해주세요."); return false; }
	}

	return true;
}
function chk_add_category(cate)
{
	var i=0;
	var j=0;
	var category = document.getElementsByName('e_category[]');
	for(i=0;i<category.length;i++){
		for(j=3;j<=cate.length;j=j+3){
			if(cate.substring(0,j)==category[i].value)return false;
		}
	}
	return true;
}
function exec_add()
{
	var ret;
	var str = new Array();
	var obj = document.forms['fm']['cate[]'];
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
	if(!chk_add_category(ret)){
		alert('중복된 카테고리 입니다.');
		return;
	}
	var obj = document.getElementById('objCategory');
	oTr = obj.insertRow();
	oTd = oTr.insertCell();
	oTd.id = "currPosition";
	oTd.innerHTML = str.join(" > ");
	oTd = oTr.insertCell();
	oTd.innerHTML = "\<input type='text' name='e_category[]' value='" + ret + "' style='display:none' />";
	oTd = oTr.insertCell();
	oTd.innerHTML = "<a href='javascript:void(0)' onClick='cate_del(this.parentNode.parentNode)'><img src='../img/i_del.gif' align='absmiddle' /></a>";
}

function cate_del(el)
{
	idx = el.rowIndex;
	var obj = document.getElementById('objCategory');
	obj.deleteRow(idx);
}

function getAccumRate1()
{
	var fm = document.fm;
	fm.save.value = '';
	fm.getAccumRate.value = 'Y';
	fm.action = "ncash_set.php";
	fm.submit();
}

function use_check(status)
{
	if(status == 'Y'){
		document.getElementById('api_id').disabled = false;
		document.getElementById('api_key').disabled = false;
	}else if(status == 'N'){
		document.getElementById('api_id').disabled = true;
		document.getElementById('api_key').disabled = true;
	}
}

document.onkeydown = keydnTree;
</script>

<div class="title title_top">네이버 마일리지 설정/관리</div>

<form name="fm" method="post" onsubmit="return checkForm(this);" style="width: 800px;" id="naver-service-configure">
<input type="hidden" name="save" value="setting">
<input type="hidden" name="getAccumRate" value="">
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>외부인증&nbsp;아이디</td>
	<td>
		<input type="text" name="api_id" id="api_id" value="<?=$load_config_ncash['api_id']?>" maxlength="6" <? if($checked['useyn']['N']){?>disabled="true"<?}?>><span class="small"><font class="extext">&nbsp;(발행받은 가맹점 연동 아이디)</font></span>
	</td>
</tr>
<tr>
	<td>인증키</td>
	<td>
		<input type="text" name="api_key" id="api_key" value="<?=$load_config_ncash['api_key']?>" maxlength="40" size="70" <? if($checked['useyn']['N']){?>disabled="true"<?}?>><span class="small"><font class="extext">&nbsp;(발행받은 가맹점 API Key)</font></span>
	</td>
</tr>
<tr>
	<td>기본 적립률</td>
	<td>
		<?=$load_config_ncash['baseAccumRate']?>%<span class="small"><font class="extext">&nbsp;(<?=$load_config_ncash['RateDate']?> 기준)</font>&nbsp;<img src="../img/btn_refresh.gif" onclick="getAccumRate1();" style="vertical-align:middle;"></span><br/>
		<div class="extext" style="padding-top:5px;">※ 적립률을 변경했을 시 버튼을 눌러 최신 적립률로 업데이트를 합니다.<br/>
		&nbsp;&nbsp;- 해당 버튼을 한번에 여러번 누르게 되면 과도한 트래픽 유발로 인하여 적립률 통신이 제한 될 수 있으므로 네이버 마일리지 관리자페이지에서 갱신되었을때만 이용하시기 바랍니다.<br/>
		&nbsp;&nbsp;- 추가적립률은 쇼핑몰페이지에서 자동갱신 됩니다.
		</div>
	</td>
</tr>
</table>

<div style="padding-top:20px"></div>


<div class="title title_top">네이버 마일리지 적립금 정책 설정</div>
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>상품 적립금<br/>지급 정책</td>
	<td class="noline">
		<input type="radio" name="save_mode" value="both" <?=$checked['save_mode']['both']?> />결제 적립금을 쇼핑몰 적립금과 네이버 마일리지 둘 다 적립합니다.<br/>
		<input type="radio" name="save_mode" value="ncash" <?=$checked['save_mode']['ncash']?> />결제 적립금을 네이버 마일리지로만 적립합니다.<br/>
		<input type="radio" name="save_mode" value="choice" <?=$checked['save_mode']['choice']?><?=$checked['save_mode']['']?> />결제 적립금을 네이버 마일리지 또는 쇼핑몰 적립금 중 고객이 선택하여 적립합니다.<br/>
		<div class="extext" style="padding-top:5px;">
			※ 고객이 상품 구매 금액을 적립하고자 할 때 적립하는 유형에 대한 설정을 하는 항목입니다.<br/>
			&nbsp;&nbsp;- <b>'결제 적립금을 쇼핑몰 적립금과 네이버 마일리지 둘 다 적립합니다.'</b>로 선택할 경우, 쇼핑몰 적립과 네이버 마일리지로 동시에 적립됩니다.<br/>
			&nbsp;&nbsp;- <b>'결제 적립금을 네이버 마일리지로만 적립합니다.'</b>로 선택할 경우, 쇼핑몰 적립금은 적립되지 않고 네이버 마일리지로만 적립됩니다.<br/>
			&nbsp;&nbsp;- <b>'결제 적립금을 네이버 마일리지 또는 쇼핑몰 적립금 중 고객이 선택하여 적립합니다.'</b>로 선택할 경우, 고객이 주문서 페이지에서 쇼핑몰로 적립할지, 네이버 마일리지로 적립할지 선택하여 해당 내용에 따라 적립되게 됩니다.<br/>
		</div>
	</td>
</tr>
</table>

<?	
	if($ret){
?>


<div style="padding-top:20px"></div>

<div class="title title_top">네이버 마일리지 연동 설정</div>
<table class=tb>
<col class=cellC><col class=cellL>
<col class=cellC><col class=cellL>
<tr>
	<td>연동 상태</td>
	<td class="noline">
		<input type="radio" name="status" value="test" <?=$checked['status']['test']?><?=$checked['status']['']?> />가오픈&nbsp;
		<input type="radio" name="status" value="real" <?=$checked['status']['real']?> />실오픈<br/>
	</td>
	<td>예외상품설정<br/>지원여부</td>
	<td class="noline">
		<input type="radio" name="exceptionyn" value="N" <?=$checked['exceptionyn']['N']?><?=$checked['exceptionyn']['']?> />예외상품설정 불가능&nbsp;
		<input type="radio" name="exceptionyn" value="Y" <?=$checked['exceptionyn']['Y']?> />예외상품설정 가능<br/>
	</td>
</tr>
<tr>
	<td>모바일 연동상태</td>
	<td class="noline">
		<input type="radio" name="mobileStatus" value="test" <?=$checked['mobileStatus']['test']?><?=$checked['mobileStatus']['']?> />가오픈&nbsp;
		<input type="radio" name="mobileStatus" value="real" <?=$checked['mobileStatus']['real']?> />실오픈<br/>
	</td>
	<td>사용여부</td>
	<td class="noline" colspan="3">
		<input type="radio" name="useyn" value="Y" onclick="javascript:use_check('Y');" <?=$checked['useyn']['Y']?><?=$checked['useyn']['']?> />사용함 <input type="radio" name="useyn" value="N" onclick="javascript:use_check('N');" <?=$checked['useyn']['N']?> />사용안함(네이버 마일리지 해지)
		<br/>
	</td>
</tr>
</table>

<?
	}
?>

<div style="padding-top:20px"></div>

<div <? if($load_config_ncash['exceptionyn'] != 'Y') echo 'style="display:none;"'; ?>>
<div class="title title_top">네이버 마일리지 예외상품설정</div>
<table border="1" bordercolor="#e1e1e1" style="border-collapse:collapse" width="100%">
<col class="cellC"><col class="cellL">
<tr>
	<td height="50">예외 상품</td>
	<td>
	<div id="divExceptions" style="display:<?=$display['relationis']?>;position:relative;z-index:99">
	<div style="padding-bottom:3px">
	<script>new categoryBox('exceptions[]',4,'','','fm');</script>
	<input type=text name="search_exceptions" onkeydown="return go_list_goods('exceptions')">
	<a href="javascript:list_goods('exceptions')"><img src="../img/i_search.gif" align="absmiddle" /></a>
	<a href="javascript:view_goods('exceptions')"><img src="../img/i_openclose.gif" align="absmiddle" /></a>
	</div>
	<div id="obj_exceptions" class=box1><iframe id="ifrm_exceptions" style="width:100%;height:100%" frameborder=0></iframe></div>
	<div id="obj2_exceptions" class="box2 scroll" onselectstart="return false" onmousewheel="return iciScroll(this)">
		<div class="boxTitle">- 등록된 관련상품 <font class="small" color="#F2F2F2">(삭제하려면 더블클릭)</font></div>
		<table id="tb_exceptions" class="tb">
		<col width=50>
		<?
		if ($e_exceptions) {
			foreach ($e_exceptions as $v) {
		?>
		<tr onclick="spoit('exceptions',this)" ondblclick="remove('exceptions',this)" class="hand">
			<td width="50" nowrap><a href="../../goods/goods_view.php?goodsno=<?=$v['goodsno']?>" target="_blank"><?=goodsimg($v['img_s'],40,'',1)?></a></td>
			<td width="100%">
			<div><?=$v['goodsnm']?></div>
			<input type="hidden" name="e_exceptions[]" value="<?=$v['goodsno']?>">
			</td>
		</tr>
		<?
			}
		}
		?>
		</table>
	</div>
	<div id="exceptionsX" style="padding-top:3px"></div>
	</div>
	<script>react_goods('exceptions');</script>
	</td>
</tr>
<tr>
	<td height="50">예외 카테고리</td>
	<td>
	<div style="padding-top:5"><script>new categoryBox('cate[]',4,'','','fm');</script>
	<a href="javascript:exec_add()"><img src="../img/btn_coupon_cate.gif"></a></div>
	<div class="box" style="padding:10 0 10 10">
	<table cellpadding="8" cellspacing=0 id="objCategory" bgcolor="f3f3f3" border="0" bordercolor="#cccccc" style="border-collapse:collapse">
	<?
	if ($e_category) {
		foreach ($e_category as $k) {
	?>
	<tr>
		<td id="currPosition"><?=strip_tags(currPosition($k))?></td>
		<td><input type="text" name="e_category[]" value="<?=$k?>" style="display:none">
		<td><a href="javascript:void(0)" onClick="cate_del(this.parentNode.parentNode)"><img src="../img/i_del.gif" border="0" align="absmiddle"></a>
		</td>
	</tr>
	<?
		}
	}
	?>
	</table>
	</div>
	</td>
</tr>
</table>
<div class="extext" style="padding-top:5px;">
	※ <b>네이버 마일리지 적립 제외 상품</b><br/>
	&nbsp;&nbsp;- 현가 제품(상품권, 귀금속, 쌀)은 네이버 마일리지 적립 및 사용대상에서 제외 됩니다.<br/>
	&nbsp;&nbsp;- 해당 상품을 포함하여 거래가 발생한 경우에는 총 주문 금액에서 해당 상품의 주문 금액을 제외한 금액을 제외한 금액을 산정하여<br/>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;네이버에 전달 해야 하므로, <b>상품권, 귀금속, 쌀 등의 상품은 예외 상품으로 설정해 주시길 바랍니다.</b><br/>
</div>
</div>
<div class=button_top><input type=image src="../img/btn_save.gif"></div>
</font>
</form>

<?include "../_footer.php"; ?>
