<?
$location = "네이버 페이 > 네이버 페이 설정/관리";
include "../_header.php";
include "../../conf/fieldset.php";

$strPath = "../../conf/naverCheckout.cfg.php";
if(file_exists($strPath)) require $strPath;

if(!$checkoutCfg['testYn'])$checkoutCfg['testYn']='n';
if(!$checkoutCfg['useYn'])$checkoutCfg['useYn']='n';
if(!$checkoutCfg['detailImg'])$checkoutCfg['detailImg']=0;
if(!$checkoutCfg['cartImg'])$checkoutCfg['cartImg']=0;
if(!$checkoutCfg['ncMemberYn'])$checkoutCfg['ncMemberYn']='n';
if(!$checkoutCfg['imgType'])$checkoutCfg['imgType']="A";
if(!$checkoutCfg['imgColor'])$checkoutCfg['imgColor']="1";
if(!$checkoutCfg['mobileButtonTarget'])$checkoutCfg['mobileButtonTarget']="self";

$checked['testYn'][$checkoutCfg['testYn']] = "checked";
$checked['useYn'][$checkoutCfg['useYn']] = "checked";
$checked['detailImg'][$checkoutCfg['detailImg']] = "checked";
$checked['cartImg'][$checkoutCfg['cartImg']] = "checked";
$checked['ncMemberYn'][$checkoutCfg['ncMemberYn']] = "checked";
$checked['mobileButtonTarget'][$checkoutCfg['mobileButtonTarget']] = "checked";
$selected['imgType'][$checkoutCfg['imgType']] = "selected";
$selected['imgColor'][$checkoutCfg['imgColor']] = "selected";
$selected['mobileImgType'][$checkoutCfg['mobileImgType']] = "selected";
$selected['mobileImgColor'][$checkoutCfg['mobileImgColor']] = "selected";

// 회원인증절차 설정
if($joinset['status'] == '1') $joinsetStatus = "<span style=\"color:#0000FF;\">인증절차없음 (사용가능)</span>";
else $joinsetStatus = "<span style=\"color:#FF0000;\">관리자 인증 후 가입 (사용불가)</span>";

// 회원가입설정 정보
$resnoUse = ($checked['useField']['resno'] == "checked") ? "<span style=\"color:#0000FF;\">O 체크</span>" : "<span style=\"color:#FF0000;\">X 미체크</span>";
$resnoReq = ($checked['reqField']['resno'] == "checked") ? "<span style=\"color:#0000FF;\">O 체크</span>" : "<span style=\"color:#FF0000;\">X 미체크</span>";

// 실명확인/아이핀 설정
if(($ipin['useyn'] == 'y' && $ipin['id']) && !($realname['useyn'] == 'y' && $realname['id'])) {
	$ipinStatus = ($ipin['useyn'] == 'y' && $ipin['id']) ? "<span style=\"color:#FF0000;\">사용</span>" : "<span style=\"color:#FF0000;\">사용안함</span>";
	$realStatus = ($realname['useyn'] == 'y' && $realname['id']) ? "<span style=\"color:#FF0000;\">사용</span>" : "<span style=\"color:#FF0000;\">사용안함</span>";

} else {
	$ipinStatus = ($ipin['useyn'] == 'y' && $ipin['id']) ? "<span style=\"color:#0000FF;\">사용</span>" : "<span style=\"color:#0000FF;\">사용안함</span>";
	$realStatus = ($realname['useyn'] == 'y' && $realname['id']) ? "<span style=\"color:#0000FF;\">사용</span>" : "<span style=\"color:#0000FF;\">사용안함</span>";
}

if($checkoutCfg[e_exceptions]){
	$res = $db->query("select * from gd_goods where goodsno in (".implode(',',$checkoutCfg['e_exceptions']).")");
	while($tmp = $db->fetch($res))$e_exceptions[] = $tmp;
}

// 주문API
$config = Core::loader('config');
$checkoutapi = $config->load('checkoutapi');

// 부가 서비스 URL
$tmpProtocol = explode("/", $_SERVER['SERVER_PROTOCOL']);
$tmpURL = ($cfg['shopUrl']) ? str_replace("/", "", $cfg['shopUrl']) : str_replace("/", "", $_SERVER['HTTP_HOST']);
$idPlusURLHeader = strtolower($tmpProtocol[0])."://".$tmpURL.$cfg['rootDir'];

// 주문통합 설정 (택배사 연동에 필요하므로 로딩)
	@include(dirname(__FILE__).'/../order/_cfg.integrate.php');
?>

<?php include dirname(__FILE__).'/../naverCommonInflowScript/configure.php'; ?>

<style type="text/css">
img {border:none;}
</style>
<script type="text/javascript">
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
function set_mobileImgColorOtion(se1){
	var t = 1;
	var i = 0;
	var k = 0;
	var se2 = document.getElementsByName('mobileImgColor')[0];
	t = 1;

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
function mobilePreview(){
	var se1 = document.getElementsByName('mobileImgType')[0];
	var se2 = document.getElementsByName('mobileImgColor')[0];
	var img = '';
	img = se1.options[se1.selectedIndex].value + se2.options[se2.selectedIndex].value;
	document.getElementById('previewMobileImg').innerHTML = "<img src='http://gongji.godo.co.kr/userinterface/naverCheckout/images/"+img+"'/>";
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

function showExample(layerID) {
	exObj = document.getElementById(layerID);
	if(exObj.style.display == "none") exObj.style.display = "";
	else exObj.style.display = "none"
}

window.onload = function() {
	sel_imgType = document.getElementById('imgType');
	sel_imgColor = document.getElementById('imgColor');
	imgColor_value = "<?=$checkoutCfg['imgColor']?>";

	set_imgColorOtion(sel_imgType);
	if(imgColor_value) {
		sel_imgColor.options[imgColor_value - 1].selected = true;
	}

	preview();

	sel_mobileImgType = document.getElementById('mobileImgType');
	sel_mobileImgColor = document.getElementById('mobileImgColor');
	mobileImgColor_value = "<?=$checkoutCfg['mobileImgColor']?>";

	set_mobileImgColorOtion(sel_mobileImgType);
	if(mobileImgColor_value) {
		sel_mobileImgColor.options[mobileImgColor_value - 1].selected = true;
	}

	mobilePreview();
}
</script>

<div style="width:800px">

<form name="fm" method="post" action="indb.php" onsubmit="return checkForm(this)" target="ifrmHidden" id="naver-service-configure"/>

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
	<td>배송업체 선택</td>
	<td>
		<select name="default_dlv_company">
			<? foreach ($integrate_cfg['dlv_company']['checkout'] as $k => $v) { ?>
			<option value="<?=$k?>" <?=($checkoutCfg['default_dlv_company'] == $k) ? 'selected' : ''?>><?=$v?></option>
			<? } ?>
		</select>
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
	<td height="30">PC 용<br/>버튼선택</td>
	<td>
	<div style="padding:5 0 5 0;">
	<select name="imgType" id="imgType" onchange="set_imgColorOtion(this);preview();">
	<option value='A' <?=$selected['imgType']['A']?>>A타입</option>
	<option value='B' <?=$selected['imgType']['B']?>>B타입</option>
	<option value='C' <?=$selected['imgType']['C']?>>C타입</option>
	<option value='D' <?=$selected['imgType']['D']?>>D타입</option>
	<option value='E' <?=$selected['imgType']['E']?>>E타입</option>
	</select>
	<select name="imgColor" id="imgColor" onchange="preview()">
	<option value='1'>1색상</option>
	<option value='2'>2색상</option>
	<option value='3'>3색상</option>
	</select>
	</div>
	<div style="padding:0 0 5 0;" id="previewImg"></div>
	</td>
</tr>
<tr>
	<td height="30">모바일 용<br/>버튼선택</td>
	<td>
	<div style="padding:5px 0 5px 0;">
	<select name="mobileImgType" id="mobileImgType" onchange="set_mobileImgColorOtion(this);mobilePreview();">
	<option value="MA" <?=$selected['mobileImgType']['MA']?>>MA타입</option>
	<option value="MB" <?=$selected['mobileImgType']['MB']?>>MB타입</option>
	</select>
	<select name="mobileImgColor" id="mobileImgColor" onchange="mobilePreview()">
	<option value="1">1색상</option>
	<option value="2">2색상</option>
	</select>
	</div>
	<div style="padding:0 0 5px 0;" id="previewMobileImg"></div>
	<div style="padding: 10px 0" class="noline">
		<span>버튼링크 타겟 : </span>
		<input type="radio" name="mobileButtonTarget" value="self" id="mobileButtonTarget-self" <?php echo $checked['mobileButtonTarget']['self']; ?>/>
		<label for="mobileButtonTarget-self">현재창</label>
		<input type="radio" name="mobileButtonTarget" value="new" id="mobileButtonTarget-new" <?php echo $checked['mobileButtonTarget']['new']; ?>/>
		<label for="mobileButtonTarget-new">새창</label>
	</div>
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
		<div style="padding:5px 0px 0px 5px;"><a href="javascript:;" onclick="javascript:popupGoodschoice('e_exceptions[]', 'exceptionsX');"><img src="../img/btn_goodsChoice.gif" class="hand" align="absmiddle" border="0" /></a></div>
		<div style="padding:5px 0px 0px 5px;"><font class="extext">※주의: 상품선택 후 반드시 하단 등록(수정)버튼을 누르셔야 최종 저장이 됩니다.</font></div>
		<div id="exceptionsX" style="padding:3px 0px 0px 5px;">
			<?php
				if ($e_exceptions){
					foreach ($e_exceptions as $v){
			?>
				<a href="../../goods/goods_view.php?goodsno=<?php echo $v['goodsno']; ?>" target="_blank"><?php echo goodsimg($v['img_s'], '40,40', '', 1); ?></a>
				<input type=hidden name="e_exceptions[]" value="<?php echo $v['goodsno']; ?>" />
			<?php
					}
				}
			?>
		</div>
	</div>
	</td>
</tr>
<tr>
	<td height="50">예외 카테고리</td>
	<td>
	<div style="padding:5 0 0 5"><script>new categoryBox('cate[]',4,'','','fm');</script>
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
	<td><div>PC 용 치환코드</div><div style="padding:5 0 5 0">삽입 방법</div></td>
	<td>
	<div style="padding-top:5"><a href="../../admin/design/codi.php" target="_blank">"쇼핑몰 관리자 > 디자인관리"</a> 좌측 트리 메뉴에서 "상품 > 상품상세화면" 메뉴,</div>
	<div style="padding:5 0 5 0"><a href="../../admin/design/codi.php" target="_blank">"쇼핑몰 관리자 > 디자인관리"</a> 좌측 트리 메뉴에서 "상품 > 장바구니" 메뉴 클릭</div>
	<div style="padding:0 0 5 0">[바로구매] 또는 [주문하기] 버튼 아래에 치환코드 삽입을 권장합니다.</div>
	</td>
</tr>
<tr>
	<td><div>모바일 용 치환코드</div><div style="padding:5 0 5 0">삽입 방법</div></td>
	<td>
	<div style="padding-top:5"><a href="../../admin/mobileShop/codi.php" target="_blank">"쇼핑몰 관리자 > 모바일샵 > 모바일샵 디자인관리"</a> 좌측 트리 메뉴에서 "상품 > 상품상세화면" 메뉴,</div>
	<div style="padding:5 0 5 0"><a href="../../admin/mobileShop/codi.php" target="_blank">"쇼핑몰 관리자 > 모바일샵 > 모바일샵 디자인관리"</a> 좌측 트리 메뉴에서 "상품 > 장바구니" 메뉴 클릭</div>
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
</script>
<? include "../_footer.php"; ?>