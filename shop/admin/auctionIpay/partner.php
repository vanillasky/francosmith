<?
$location = "옥션 iPay 결제 > 옥션 iPay 결제 설정";
include "../_header.php";
include "../../lib/auctionIpay.class.php";

$strPath = "../../conf/auctionIpay.cfg.php";
if(file_exists($strPath)) require $strPath;

if(!isset($auctionIpayCfg['testYn'])) $auctionIpayCfg['testYn']='n';
if(!isset($auctionIpayCfg['useYn'])) $auctionIpayCfg['useYn']='n';
if(!isset($auctionIpayCfg['paymentrule'])) $auctionIpayCfg['paymentrule']=0;
if(!isset($auctionIpayCfg['btnType'])) $auctionIpayCfg['btnType'] = 'http://pics.auction.co.kr/ipay/btn/btn_ipay.gif';
if(!isset($auctionIpayCfg['logoType'])) $auctionIpayCfg['logoType'] = '../admin/img/logo_ipay01.gif';
$auctionIpayCfg['backurl'] = $auctionIpayCfg['redirecturl'] = 'http://'.$_SERVER['SERVER_NAME'];

$checked['testYn'][$auctionIpayCfg['testYn']] = 'checked="checked"';
$checked['useYn'][$auctionIpayCfg['useYn']] = 'checked="checked"';
$checked['paymentrule'][$auctionIpayCfg['paymentrule']] = 'checked="checked"';
$checked['btnType'][$auctionIpayCfg['btnType']] = 'checked="checked"';
$checked['logoType'][$auctionIpayCfg['logoType']] = 'checked="checked"';

if($auctionIpayCfg['e_exceptions']){
	$res = $db->query("select * from gd_goods where goodsno in (".implode(',',$auctionIpayCfg['e_exceptions']).")");
	while($tmp = $db->fetch($res)) $e_exceptions[] = $tmp;
}

// 주문통합 설정 (택배사 연동에 필요하므로 로딩)
	@include(dirname(__FILE__).'/../order/_cfg.integrate.php');
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
	var els = document.forms[0][name+'[]'];
	for (i=0;i<els.length;i++) if (els[i].value) category = els[i].value;
	var ifrm = document.getElementById("ifrm_" + name);
	var goodsnm = eval("document.forms[0].search_" + name + ".value");
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
	var obj = f.useYn;
	if(obj[0].checked && !chkForm(f)) return false;
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
document.onkeydown = keydnTree;
</script>

<div style="width:800">

<form method="post" action="indb.php" onsubmit="return checkForm(this)" target="ifrmHidden" />

<div class="title title_top">옥션 iPay 설정 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=marketing&no=16')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<table border="1" bordercolor="#e1e1e1" style="border-collapse:collapse" width="100%">
<col class="cellC"><col class="cellL">
<tr>
	<td height="50">옥션 seller id</td>
	<td>
		<input type="text" name="sellerid" value="<?=$auctionIpayCfg['sellerid'];?>" required msgR="옥션 seller id는 필수입니다." />
	</td>
</tr>
<tr>
	<td height="50">인증키</td>
	<td>
		<div style="margin:0px; padding:0px;" class="small1 extext"><a href="javascript:popup('http://ipay.auction.co.kr/ipay/SellerRegister.aspx?url=<?=urlencode("http://ipaymall.godo.co.kr/auction_ipaymall_ticket.php");?>&return_url=<?=urlencode($_SERVER['HTTP_HOST'])?>',493,672);"><img src="../img/btn_id_return.gif" align="absmiddle" /></a> 옥션 로그인 후 iPay 수정하기 창에서 정보수정 버튼을 눌러주시기 바랍니다.</div>
		<div><textarea name="ticket" style="width:600px; height:50px;" required msgR="인증키는 필수입니다." ><?=$auctionIpayCfg['ticket'];?></textarea></div>
	</td>
</tr>
<tr>
	<td height="50">옥션 iPay 로고 삽입</td>
	<td class="noline">
		<label><input type="radio" name="logoType" value="../admin/img/logo_ipay01.gif" <?=$checked['logoType']['../admin/img/logo_ipay01.gif']?> /><img src="../../admin/img/logo_ipay01.gif" align="absmiddle" /></label>
		<label><input type="radio" name="logoType" value="../admin/img/logo_ipay02.gif" <?=$checked['logoType']['../admin/img/logo_ipay02.gif']?> /><img src="../../admin/img/logo_ipay02.gif" align="absmiddle" /></label>
		<div style="padding-top:5;">{=auctionIpayLogo()} <img class="hand" src="../img/i_copy.gif" onclick="copy_txt('{auctionIpayLogo()}')" alt="복사하기" align="absmiddle"/></div>
		<div style="padding-top:10;" class="small1 extext">
			<div>복사하신 <b>치환코드</b>를 페이지에 삽입하시면 옥션 iPay 로고가 출력됩니다.</div>
		</div>
	</td>
</tr>
</table>
<p/>

<div class="title title_top">옥션 iPay 결제 설정 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=marketing&no=16')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<table border="1" bordercolor="#e1e1e1" style="border-collapse:collapse" width="100%">
<col class="cellC"><col class="cellL">
<tr height="30">
	<td>사용여부</td>
	<td class="noline">
	<label><input type="radio" name="useYn" value="y" onclick="chkYn();" <?=$checked['useYn']['y'];?>/>사용</label><label><input type="radio" name="useYn" value="n" <?=$checked['useYn']['n'];?> onclick="chkYn();" />사용안함</label>
	<br/>
	<span class="extext">옥션 iPay 결제 내역은 옥션의 판매관리 페이지에서만 확인이 가능합니다.</span>
	</td>
</tr>
<tr height="30">
	<td>테스트하기</td>
	<td class="noline">
	<label><input type="radio" name="testYn" value="y" <?=$checked['testYn']['y'];?>/>사용</label><label><input type="radio" name="testYn" value="n" <?=$checked['testYn']['n'];?> />사용안함</label>
	<div style="padding-top:5;" class="small1 extext">
	<div>테스트를 사용에 설정하시면 관리자로 로긴한 상태에서만 옥션iPay 버튼이 보여집니다.</div>
	</div>
	</td>
</tr>
<tr>
	<td height="50">결제수단</td>
	<td class="noline">
		<label><input type="radio" name="paymentrule" value="0" <?=$checked['paymentrule'][0];?> required msgR="결제수단은 필수입니다." />모두 가능</label>
		<label><input type="radio" name="paymentrule" value="1" <?=$checked['paymentrule'][1];?> required msgR="결제수단은 필수입니다." />카드 불가</label>
		<label><input type="radio" name="paymentrule" value="2" <?=$checked['paymentrule'][2];?> required msgR="결제수단은 필수입니다." />무통장입금 불가</label>
		<label><input type="radio" name="paymentrule" value="3" <?=$checked['paymentrule'][3];?> required msgR="결제수단은 필수입니다." />카드 휴대폰 불가</label>
		<div style="padding-top:5;" class="small1 extext">
		<div>결제금액 1000원 이하는 신용카드 결제가 불가능합니다.</div>
		</div>
	</td>
</tr>
<tr height="30">
	<td>배송업체 선택</td>
	<td>
		<select name="default_dlv_company">
			<? foreach ($integrate_cfg['dlv_company']['ipay'] as $k => $v) { ?>
			<option value="<?=$k?>" <?=($auctionIpayCfg['default_dlv_company'] == $k) ? 'selected' : ''?>><?=$v?></option>
			<? } ?>
		</select>
	</td>
</tr>
<tr style="display:none">
	<td height="50">BACK URL</td>
	<td>
		<input type="text" name="backurl" value="<?=$auctionIpayCfg['backurl'];?>" style="width:600px;" required msgR="back url은 필수입니다." />
		<div style="padding-top:5;" class="small1 extext">
		<div>주문서에서 되돌아갈 고객사 URL</div>
		</div>
	</td>
</tr>
<tr style="display:none">
	<td height="50">REDIRECT URL</td>
	<td>
		<input type="text" name="redirecturl" value="<?=$auctionIpayCfg['redirecturl'];?>" style="width:600px;" required msgR="redirect url은 필수입니다." />
		<div style="padding-top:5;" class="small1 extext">
		<div>결제완료후 이동 URL</div>
		</div>
	</td>
</tr>
<tr>
	<td height="30">버튼선택</td>
	<td>
	<div style="padding:5 0 5 0;" class="noline">
		<label><input type="radio" name="btnType" value="http://pics.auction.co.kr/ipay/btn/btn_ipay.gif" <?=$checked['btnType']['http://pics.auction.co.kr/ipay/btn/btn_ipay.gif']?> /><img src="http://pics.auction.co.kr/ipay/btn/btn_ipay.gif" align="absmiddle" /></label>
		<label><input type="radio" name="btnType" value="http://pics.auction.co.kr/ipay/btn/btn_ipay02.gif" <?=$checked['btnType']['http://pics.auction.co.kr/ipay/btn/btn_ipay02.gif']?> /><img src="http://pics.auction.co.kr/ipay/btn/btn_ipay02.gif" align="absmiddle" /></label>
		<label><input type="radio" name="btnType" value="http://pics.auction.co.kr/ipay/btn/btn_ipay05.gif" <?=$checked['btnType']['http://pics.auction.co.kr/ipay/btn/btn_ipay05.gif']?> /><img src="http://pics.auction.co.kr/ipay/btn/btn_ipay05.gif" align="absmiddle" /></label>
	</div>
</tr>
</table>
<p/>

<div id="sub" disabled>

<div class="title title_top">옥션 iPay 결제 예외상품설정 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=marketing&no=16')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<table border="1" bordercolor="#e1e1e1" style="border-collapse:collapse" width="100%">
<col class="cellC"><col class="cellL">
<tr>
	<td height="50">예외 상품</td>
	<td>
	<div id="divExceptions" style="display:<?=$display['relationis']?>;position:relative;z-index:99">
	<div style="padding-bottom:3px">
	<script>new categoryBox('exceptions[]',4,'','');</script>
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
			<b><?=number_format($v['price'])?></b>
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
	<div style="padding-top:5"><script>new categoryBox('cate[]',4,'','');</script>
	<a href="javascript:exec_add()"><img src="../img/btn_coupon_cate.gif"></a></div>
	<div class="box" style="padding:10 0 10 10">
	<table cellpadding="8" cellspacing=0 id="objCategory" bgcolor="f3f3f3" border="0" bordercolor="#cccccc" style="border-collapse:collapse">
	<?
	if ($auctionIpayCfg['e_category']) {
		foreach ($auctionIpayCfg['e_category'] as $k) {
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
<p/>
<div class="title title_top">쇼핑몰에 옥션 iPay 결제 버튼 삽입하기 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=marketing&no=16')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<table border="1" bordercolor="#e1e1e1" style="border-collapse:collapse" width="100%">
<col class="cellC"><col class="cellL">
<tr>
	<td height="50">치환코드</td>
	<td>
	<div style="padding-top:5;">{auctionIpayBtn} <img class="hand" src="../img/i_copy.gif" onclick="copy_txt('{auctionIpayBtn}')" alt="복사하기" align="absmiddle"/></div>
	<div style="padding-top:10;" class="small1 extext">
	<div>복사하신 <b>치환코드</b>를 <b>상품상세화면</b>과 <b>장바구니</b> 페이지에 삽입하시면 옥션 iPay 기능이 동작합니다.</div>
	<div>상품 리스트 이미지가 없는 상품(포함된 장바구니)의 경우 버튼이 출력되지 않습니다.</div>
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
	<div>반드시 옥션 iPay 심사가 완료 되어 서비스를 사용하실 수 있으실 때 사용여부를 사용으로 설정하십시요.</div>
	<div>iPay 결제는 상품의 상세페이지, 장바구니에 “Auction iPay 구매하기” 버튼이 노출되며, Auction 관리자 페이지에서만 주문 내역을 확인할 수 있습니다.</div>
	<div>상품 리스트 이미지가 없는 상품(이 포함된 장바구니)의 경우 iPay 결제 버튼이 출력되지 않습니다.</div>
</td>
</tr>
</table>
</div>

</div>
<script type="text/javascript">
	cssRound('MSG01');
	chkYn();
</script>
<? include "../_footer.php"; ?>