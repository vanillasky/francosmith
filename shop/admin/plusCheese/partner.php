<?
$location = "네이버 체크아웃 > 네이트체크아웃 설정/관리";
include "../_header.php";

$strPath = "../../conf/naverCheckout.cfg.php";
if(file_exists($strPath)) require $strPath;

if(!$checkoutCfg['testYn'])$checkoutCfg['testYn']='n';
if(!$checkoutCfg['useYn'])$checkoutCfg['useYn']='n';
if(!$checkoutCfg['detailImg'])$checkoutCfg['detailImg']=0;
if(!$checkoutCfg['cartImg'])$checkoutCfg['cartImg']=0;

$checked['testYn'][$checkoutCfg['testYn']] = "checked";
$checked['useYn'][$checkoutCfg['useYn']] = "checked";
$checked['detailImg'][$checkoutCfg['detailImg']] = "checked";
$checked['cartImg'][$checkoutCfg['cartImg']] = "checked";

if($checkoutCfg[e_exceptions]){
	$res = $db->query("select * from gd_goods where goodsno in (".implode(',',$checkoutCfg['e_exceptions']).")");
	while($tmp = $db->fetch($res))$e_exceptions[] = $tmp;
}

// 주문API
$config = Core::loader('config');
$checkoutapi = $config->load('checkoutapi');
?>
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
	for(var i=0;i<lyr.getElementsByTagName('a').length;i++) {
		lyr.getElementsByTagName('a')[i].style.visibility = 'hidden';
	}
	for(var i=0;i<chk.length;i++)
	{
		if(chk[0].checked == true) {
			lyr.disabled=false;
			for(var i=0;i<lyr.getElementsByTagName('a').length;i++) {
				lyr.getElementsByTagName('a')[i].style.visibility="visible";
			}
			if (document.getElementById('naverCheckoutApiRequest') != null) {
				document.getElementById('sub_api').disabled=true;
			} else {
				document.getElementById('sub_api').disabled=false;
			}
		}
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
	var els = document.forms[0][name+'[]'];
	for (i=0;i<els.length;i++) if (els[i].value) category = els[i].value;
	var ifrm = eval("ifrm_" + name);
	var goodsnm = eval("document.forms[0].search_" + name + ".value");
	ifrm.location.href = "../goods/_goodslist.php?name=" + name + "&category=" + category + "&goodsnm=" + goodsnm;
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
function checkForm(f)
{
	var obj = f.useYn;
	if(obj[0].checked && !chkForm(f)) return false;
	return true;
}
function set_imgColorOtion(se1){
	var t = 1;
	var i = 0;
	var k = 0;
	var se2 = document.getElementsByName('imgColor')[0];
	if(se1.selectedIndex >= 2) t = 3;

	for ( i = se2.length-1 ; i > -1 ; i--) {
		se2.options[i].value = null;
		se2.options[i] = null;
	}
	for (i=0;i<t;i++){
		k = i+1;
		se2.options[i] = new Option(k+'색상');
		se2.options[i].value = i+1;
	}
}
function preview(){
	var se1 = document.getElementsByName('imgType')[0];
	var se2 = document.getElementsByName('imgColor')[0];
	var img = '';
	img = se1.options[se1.selectedIndex].value + se2.options[se2.selectedIndex].value;
	document.getElementById('previewImg').innerHTML = "<img src='http://gongji.godo.co.kr/userinterface/naverCheckout/images/"+img+"'/>";
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
	oTd.innerHTML = "\<input type=text name=e_category[] value='" + ret + "' style='display:none'>";
	oTd = oTr.insertCell();
	oTd.innerHTML = "<a href='javascript:void(0)' onClick='cate_del(this.parentNode.parentNode)'><img src='../img/i_del.gif' align=absmiddle></a>";
}

function cate_del(el)
{
	idx = el.rowIndex;
	var obj = document.getElementById('objCategory');
	obj.deleteRow(idx);
}
document.onkeydown = keydnTree;
</script>

<div style="width:800">

<form method="post" action="indb.php" onsubmit="return checkForm(this)" target="ifrmHidden" />

<div class="title title_top">네이버 체크아웃 설정/관리 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=marketing&no=14')"><img src="../img/btn_q.gif"></a></div>
<table border="1" bordercolor="#e1e1e1" style="border-collapse:collapse" width="100%">
<col class="cellC"><col class="cellL">
<tr height="30">
	<td>사용여부</td>
	<td class="noline">
	<label><input type="radio" name="useYn" value="y" onclick="chkYn();" <?php echo $checked['useYn']['y'];?>/>사용</label><label><input type="radio" name="useYn" value="n" <?php echo $checked['useYn']['n'];?> onclick="chkYn();" />사용안함</label>
	</td>
</tr>
<tr height="30">
	<td>테스트하기</td>
	<td class="noline">
	<label><input type="radio" name="testYn" value="y"  <?php echo $checked['testYn']['y'];?>/>사용</label><label><input type="radio" name="testYn" value="n" <?php echo $checked['testYn']['n'];?> />사용안함</label>
	<div style="padding-top:5;" class="small1 extext">
	<div>테스트를 사용에 설정하시면 관리자로 로긴한 상태에서만 체크아웃 버튼이 보여집니다.</div>
	<div>실제 서비스가 되지 않으며 체크아웃 기능 또한 네이버의 테스트 서버로 연동되게 됩니다.</div>
	</div>
	</td>
</tr>
<tr height="30">
	<td>착불 배송비</td>
	<td>
	<input type="text" name="collect" value="<?php echo $checkoutCfg['collect'];?>" onKeyDown="onlynumber()" required />
	</td>
</tr>
</table>
<p/>
<div id="sub" disabled>

<div class="title title_top">네이버 체크아웃 인증설정</div>
<table border="1" bordercolor="#e1e1e1" style="border-collapse:collapse" width="100%">
<col class="cellC"><col class="cellL">
<tr>
	<td height="50">네이버 가맹점 ID</td>
	<td>
	<input type="text" name="naverId" value="<?php echo $checkoutCfg['naverId'];?>" required msgR="네이버 가맹점 ID는 필수입니다." />
	</td>
</tr>
<tr>
	<td height="50">연동 인증키</td>
	<td>
	<input type="text" style="width:400" name="connectId" value="<?php echo $checkoutCfg['connectId'];?>" required msgR="연동 인증키는 필수입니다." />
	</td>
</tr>
<tr>
	<td height="50">이미지 인증키</td>
	<td>
	<input type="text" style="width:400" name="imageId" value="<?php echo $checkoutCfg['imageId'];?>"  required msgR="이미지 인증키는 필수입니다." />
	</td>
</tr>
</table>
<p/>
<div class="title title_top">네이버 체크아웃 버튼 선택</div>
<table border="1" bordercolor="#e1e1e1" style="border-collapse:collapse" width="100%">
<col class="cellC"><col class="cellL">
<tr>
	<td height="30">버튼선택</td>
	<td>
	<div style="padding:5 0 5 0;">
	<select name="imgType" onchange="set_imgColorOtion(this);preview();">
	<option value='A'>A타입</option>
	<option value='B'>B타입</option>
	<option value='C'>C타입</option>
	<option value='D'>D타입</option>
	<option value='E'>E타입</option>
	</select>
	<select name="imgColor" onchange="preview()">
	<option value='1'>1색상</option>
	<option value='2'>2색상</option>
	<option value='3'>3색상</option>
	</select>
	</div>
	<div style="padding:0 0 5 0;" id="previewImg"></div>
	</td>
</tr>
</table>
<p/>

<div class="title title_top">네이버 체크아웃 예외상품설정</div>
<table border="1" bordercolor="#e1e1e1" style="border-collapse:collapse" width="100%">
<col class="cellC"><col class="cellL">
<tr>
	<td height="50">예외 상품</td>
	<td>
	<div id=divExceptions style="display:<?=$display[relationis]?>;position:relative;z-index:99">
	<div style="padding-bottom:3px">
	<script>new categoryBox('exceptions[]',4,'','');</script>
	<input type=text name=search_exceptions onkeydown="return go_list_goods('exceptions')">
	<a href="javascript:list_goods('exceptions')"><img src="../img/i_search.gif" align=absmiddle></a>
	<a href="javascript:view_goods('exceptions')"><img src="../img/i_openclose.gif" align=absmiddle></a>
	</div>
	<div id=obj_exceptions class=box1><iframe id=ifrm_exceptions style="width:100%;height:100%" frameborder=0></iframe></div>
	<div id=obj2_exceptions class="box2 scroll" onselectstart="return false" onmousewheel="return iciScroll(this)">
		<div class=boxTitle>- 등록된 관련상품 <font class=small color=#F2F2F2>(삭제하려면 더블클릭)</font></div>
		<table id=tb_exceptions class=tb>
		<col width=50>
		<? if ($e_exceptions){ foreach ($e_exceptions as $v){ ?>
		<tr onclick="spoit('exceptions',this)" ondblclick=remove('exceptions',this) class=hand>
			<td width=50 nowrap><a href="../../goods/goods_view.php?goodsno=<?=$v[goodsno]?>" target=_blank><?=goodsimg($v[img_s],40,'',1)?></a></td>
			<td width=100%>
			<div><?=$v[goodsnm]?></div>
			<b><?=number_format($v[price])?></b>
			<input type=hidden name=e_exceptions[] value="<?=$v[goodsno]?>">
			</td>
		</tr>
		<? }} ?>
		</table>
	</div>
	<div id=exceptionsX style="padding-top:3px"></div>
	</div>
	<script>react_goods('exceptions');</script>
	</td>
</tr>
<tr>
	<td height="50">예외 카테고리</td>
	<td>
	<div style="padding:5 0 0 5"><script>new categoryBox('cate[]',4,'','');</script>
	<a href="javascript:exec_add()"><img src="../img/btn_coupon_cate.gif"></a></div>
	<div class="box" style="padding:10 0 10 10">
	<table cellpadding="8" cellspacing=0 id="objCategory" bgcolor="f3f3f3" border="0" bordercolor="#cccccc" style="border-collapse:collapse">
	<?
	if ($checkoutCfg['e_category']){ foreach ($checkoutCfg['e_category'] as $k){ ?>
	<tr>
		<td id="currPosition"><?=strip_tags(currPosition($k))?></td>
		<td><input type="text" name="e_category[]" value="<?=$k?>" style="display:none">
		<td><a href="javascript:void(0)" onClick="cate_del(this.parentNode.parentNode)"><img src="../img/i_del.gif" border=0 align=absmiddle></a>
		</td>
	</tr>
	<? }} ?>
	</table>
	</div>
	</td>
</tr>
</table>
<p/>
<div class="title title_top">쇼핑몰에 네이버 체크아웃 버튼 삽입하기 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=marketing&no=15')"><img src="../img/btn_q.gif"></a></div>
<table border="1" bordercolor="#e1e1e1" style="border-collapse:collapse" width="100%">
<col class="cellC"><col class="cellL">
<tr>
	<td height="50">치환코드</td>
	<td>
	<div div style="padding-top:5;">{naverCheckout} <img class="hand" src="../img/i_copy.gif" onclick="copy_txt('{naverCheckout}')" alt="복사하기" align="absmiddle"/></div>
	<div style="padding-top:10;" class="small1 extext">
	<div>복사하신 <b>치환코드</b>를 <b>상품상세화면</b>과 <b>장바구니</b> 페이지에 삽입하시면 체크아웃 기능이 동작합니다.</div>
	</div>
	</td>
</tr>
<tr>
	<td><div>치환코드</div><div style="padding:5 0 5 0">삽입 방법</div></td>
	<td>
	<div style="padding-top:5"><a href="../../admin/design/codi.php" target="_blank">"쇼핑몰 관리자 > 디자인관리"</a> 좌측 트리 메뉴에서 "상품 > 상품상세화면" 메뉴,</div>
	<div style="padding:5 0 5 0"><a href="../../admin/design/codi.php" target="_blank">"쇼핑몰 관리자 > 디자인관리"</a> 좌측 트리 메뉴에서 "상품 > 장바구니" 메뉴 클릭</div>
	<div style="padding:0 0 5 0">[바로구매] 또는 [주문하기] 버튼 아래에 치환코드 삽입을 권장합니다.</div>
	</td>
</tr>
</table>
<p/>
<div class="title title_top">주문연동하기 <span>네이버 체크아웃을 통해 발생하는 주문건과 문의건을 샵관리자페이지에서 확인할 수 있습니다.</span></div>
<div style="text-align:center; font-weight:bold; border:solid #e1e1e1 0; border-width:1px 1px 0 1px; background:#F6F6F6; padding:10px;">
	<?php if($checkoutapi['cryptkey']) { ?>
	네이버 체크아웃 API 연동중입니다.
	<?php } else { ?>
	<a href="indb.api.php" target="ifrmHidden" id="naverCheckoutApiRequest">[네이버 체크아웃 API 신청하기]</a>
	<?php } ?>
</div>
<table border="1" bordercolor="#e1e1e1" style="border-collapse:collapse" width="100%" id="sub_api" disabled="disabled">
<col class="cellC"><col class="cellL">
<tr height="30">
	<td>재고연동</td>
	<td class="noline">
	<label><input type="radio" name="linkStock" value="y" <?=frmChecked($checkoutapi['linkStock'],'y')?>/>사용</label>
	<label><input type="radio" name="linkStock" value="n" <?=frmChecked($checkoutapi['linkStock'],'n')?>/>사용안함</label>
	<span class="small1 extext">주문을 연동한 후 설정하면 주문내역이 취합될 때 적용됩니다.</span>
	</td>
</tr>
<tr height="30">
	<td>주문통합관리</td>
	<td class="noline">
	<label><input type="radio" name="integrateOrder" value="y" <?=frmChecked($checkoutapi['integrateOrder'],'y')?>/>사용</label>
	<label><input type="radio" name="integrateOrder" value="n" <?=frmChecked($checkoutapi['integrateOrder'],'n')?>/>사용안함</label>
	<span class="small1 extext">쇼핑몰 주문관리에서 주문을 통합하여 관리합니다.</span>
	</td>
</tr>
</table>

<div class=button>
<input type="image" src="../img/btn_save.gif">
<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a>
</div>
</div>
</form>



<div style="clear:both;" id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class="small_ex">
<tr>
<td>
	<div>반드시 네이버 체크아웃 심사가 완료 되어 서비스를 사용하실 수 있으실 때 사용여부를 사용으로 설정하십시요.</div>
</td>
</tr>
</table>
</div>

</div>
<script type="text/javascript">
	cssRound('MSG01');
	chkYn();
	preview();
</script>
<? include "../_footer.php"; ?>
