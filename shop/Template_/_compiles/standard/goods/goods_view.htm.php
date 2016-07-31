<?php /* Template_ 2.2.7 2015/10/12 11:03:26 /www/francotr3287_godo_co_kr/shop/data/skin/standard/goods/goods_view.htm 000063001 */  $this->include_("dataGoodsRelation","commoninfo");
if (is_array($GLOBALS["opt"])) $TPL__opt_1=count($GLOBALS["opt"]); else if (is_object($GLOBALS["opt"]) && in_array("Countable", class_implements($GLOBALS["opt"]))) $TPL__opt_1=$GLOBALS["opt"]->count();else $TPL__opt_1=0;
if (is_array($GLOBALS["opt1img"])) $TPL__opt1img_1=count($GLOBALS["opt1img"]); else if (is_object($GLOBALS["opt1img"]) && in_array("Countable", class_implements($GLOBALS["opt1img"]))) $TPL__opt1img_1=$GLOBALS["opt1img"]->count();else $TPL__opt1img_1=0;
if (is_array($TPL_VAR["t_img"])) $TPL_t_img_1=count($TPL_VAR["t_img"]); else if (is_object($TPL_VAR["t_img"]) && in_array("Countable", class_implements($TPL_VAR["t_img"]))) $TPL_t_img_1=$TPL_VAR["t_img"]->count();else $TPL_t_img_1=0;
if (is_array($TPL_VAR["ex"])) $TPL_ex_1=count($TPL_VAR["ex"]); else if (is_object($TPL_VAR["ex"]) && in_array("Countable", class_implements($TPL_VAR["ex"]))) $TPL_ex_1=$TPL_VAR["ex"]->count();else $TPL_ex_1=0;
if (is_array($GLOBALS["addopt_inputable"])) $TPL__addopt_inputable_1=count($GLOBALS["addopt_inputable"]); else if (is_object($GLOBALS["addopt_inputable"]) && in_array("Countable", class_implements($GLOBALS["addopt_inputable"]))) $TPL__addopt_inputable_1=$GLOBALS["addopt_inputable"]->count();else $TPL__addopt_inputable_1=0;
if (is_array($GLOBALS["optnm"])) $TPL__optnm_1=count($GLOBALS["optnm"]); else if (is_object($GLOBALS["optnm"]) && in_array("Countable", class_implements($GLOBALS["optnm"]))) $TPL__optnm_1=$GLOBALS["optnm"]->count();else $TPL__optnm_1=0;
if (is_array($GLOBALS["addopt"])) $TPL__addopt_1=count($GLOBALS["addopt"]); else if (is_object($GLOBALS["addopt"]) && in_array("Countable", class_implements($GLOBALS["addopt"]))) $TPL__addopt_1=$GLOBALS["addopt"]->count();else $TPL__addopt_1=0;
if (is_array($TPL_VAR["a_coupon"])) $TPL_a_coupon_1=count($TPL_VAR["a_coupon"]); else if (is_object($TPL_VAR["a_coupon"]) && in_array("Countable", class_implements($TPL_VAR["a_coupon"]))) $TPL_a_coupon_1=$TPL_VAR["a_coupon"]->count();else $TPL_a_coupon_1=0;
if (is_array($TPL_VAR["extra_info"])) $TPL_extra_info_1=count($TPL_VAR["extra_info"]); else if (is_object($TPL_VAR["extra_info"]) && in_array("Countable", class_implements($TPL_VAR["extra_info"]))) $TPL_extra_info_1=$TPL_VAR["extra_info"]->count();else $TPL_extra_info_1=0;?>
<?php $this->print_("header",$TPL_SCP,1);?>


<script src="/shop/lib/js/countdown.js"></script>
<style>
/* goods_spec list */
#goods_spec table {
	width:100%;
}
#goods_spec .top {
	border-top-width:1; border-top-style:solid;border-top-color:#DDDDDD;
	border-bottom-width:1; border-bottom-style:solid;border-bottom-color:#DDDDDD;
	background:#f7f7f7;
}
#goods_spec .sub {
	border-bottom-width:1; border-bottom-style:solid;border-bottom-color:#DDDDDD;
	margin-bottom:10;
}
#goods_spec th, #goods_spec td {
	padding:3px;
}
#goods_spec th {
	width: 80px;
	text-align:right;
	font-weight:normal;
}
#goods_spec td {
	text-align:left;
}

.godo-tooltip-related {background:#000000;color:#ffffff;}

</style>

<script>
function resizeFrameHeight(onm,height) {
	document.getElementById(onm).height = height;
}

function resizeFrameWidth(onm,width) {
	document.getElementById(onm).width = width;
}

var price = new Array();
var reserve = new Array();
var consumer = new Array();
var memberdc = new Array();
var realprice = new Array();
var couponprice = new Array();
var special_discount_amount = new Array();
var coupon = new Array();
var cemoney = new Array();
var opt1img = new Array();
var opt2icon = new Array();
var opt2kind = "<?php echo $TPL_VAR["optkind"][ 1]?>";
var oldborder = "";
<?php if($TPL__opt_1){$TPL_I1=-1;foreach($GLOBALS["opt"] as $TPL_V1){$TPL_I1++;?><?php if((is_array($TPL_R2=$TPL_V1)&&!empty($TPL_R2)) || (is_object($TPL_R2) && in_array("Countable", class_implements($TPL_R2)) && $TPL_R2->count() > 0)) {$TPL_I2=-1;foreach($TPL_R2 as $TPL_V2){$TPL_I2++;?>
<?php if($TPL_I1== 0&&$TPL_I2== 0){?>
var fkey = '<?php echo get_js_compatible_key($TPL_V2["opt1"])?><?php if($TPL_V2["opt2"]){?>|<?php echo get_js_compatible_key($TPL_V2["opt2"])?><?php }?>';
<?php }?>
price['<?php echo get_js_compatible_key($TPL_V2["opt1"])?><?php if($TPL_V2["opt2"]){?>|<?php echo get_js_compatible_key($TPL_V2["opt2"])?><?php }?>'] = <?php echo $TPL_V2["price"]?>;
reserve['<?php echo get_js_compatible_key($TPL_V2["opt1"])?><?php if($TPL_V2["opt2"]){?>|<?php echo get_js_compatible_key($TPL_V2["opt2"])?><?php }?>'] = <?php echo $TPL_V2["reserve"]?>;
consumer['<?php echo get_js_compatible_key($TPL_V2["opt1"])?><?php if($TPL_V2["opt2"]){?>|<?php echo get_js_compatible_key($TPL_V2["opt2"])?><?php }?>'] = <?php echo $TPL_V2["consumer"]?>;
memberdc['<?php echo get_js_compatible_key($TPL_V2["opt1"])?><?php if($TPL_V2["opt2"]){?>|<?php echo get_js_compatible_key($TPL_V2["opt2"])?><?php }?>'] = <?php echo $TPL_V2["memberdc"]?>;
realprice['<?php echo get_js_compatible_key($TPL_V2["opt1"])?><?php if($TPL_V2["opt2"]){?>|<?php echo get_js_compatible_key($TPL_V2["opt2"])?><?php }?>'] = <?php echo $TPL_V2["realprice"]?>;
coupon['<?php echo get_js_compatible_key($TPL_V2["opt1"])?><?php if($TPL_V2["opt2"]){?>|<?php echo get_js_compatible_key($TPL_V2["opt2"])?><?php }?>'] = <?php echo $TPL_V2["coupon"]?>;
couponprice['<?php echo get_js_compatible_key($TPL_V2["opt1"])?><?php if($TPL_V2["opt2"]){?>|<?php echo get_js_compatible_key($TPL_V2["opt2"])?><?php }?>'] = <?php echo $TPL_V2["couponprice"]?>;
cemoney['<?php echo get_js_compatible_key($TPL_V2["opt1"])?><?php if($TPL_V2["opt2"]){?>|<?php echo get_js_compatible_key($TPL_V2["opt2"])?><?php }?>'] = <?php echo $TPL_V2["coupon_emoney"]?>;
special_discount_amount['<?php echo get_js_compatible_key($TPL_V2["opt1"])?><?php if($TPL_V2["opt2"]){?>|<?php echo get_js_compatible_key($TPL_V2["opt2"])?><?php }?>'] = <?php echo $TPL_V2["special_discount_amount"]?>;
<?php }}?><?php }}?>
<?php if($TPL__opt1img_1){foreach($GLOBALS["opt1img"] as $TPL_K1=>$TPL_V1){?>
opt1img['<?php echo $TPL_K1?>'] = "<?php echo $TPL_V1?>";
<?php }}?>
<?php if((is_array($TPL_R1=$GLOBALS["opticon"][ 1])&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_K1=>$TPL_V1){?>
opt2icon['<?php echo $TPL_K1?>'] = "<?php echo $TPL_V1?>";
<?php }}?>

/* 필수 옵션 분리형 스크립트 start */
var opt = new Array();
opt[0] = new Array("('1차옵션을 먼저 선택해주세요','')");
<?php if($TPL__opt_1){$TPL_I1=-1;foreach($GLOBALS["opt"] as $TPL_V1){$TPL_I1++;?>
opt['<?php echo $TPL_I1+ 1?>'] = new Array("('== 옵션선택 ==','')",<?php if((is_array($TPL_R2=$TPL_V1)&&!empty($TPL_R2)) || (is_object($TPL_R2) && in_array("Countable", class_implements($TPL_R2)) && $TPL_R2->count() > 0)) {$TPL_S2=count($TPL_R2);$TPL_I2=-1;foreach($TPL_R2 as $TPL_V2){$TPL_I2++;?>"('<?php echo addslashes(addslashes($TPL_V2["opt2"]))?><?php if($TPL_V2["price"]!=$TPL_VAR["price"]){?> (<?php echo number_format($TPL_V2["price"])?>원)<?php }?><?php if($TPL_VAR["usestock"]&&!$TPL_V2["stock"]){?> [품절]<?php }?>','<?php echo addslashes(addslashes($TPL_V2["opt2"]))?>','<?php if($TPL_VAR["usestock"]&&!$TPL_V2["stock"]){?>soldout<?php }?>')"<?php if($TPL_I2!=$TPL_S2- 1){?>,<?php }?><?php }}?>);
<?php }}?>
function subOption(obj)
{
	var el = document.getElementsByName('opt[]');
	var sub = opt[obj.selectedIndex];
	while (el[1].length>0) el[1].options[el[1].options.length-1] = null;
	for (i=0;i<sub.length;i++){
		var div = sub[i].replace("')","").split("','");
		eval("el[1].options[i] = new Option" + sub[i]);
		if (div[2]=="soldout"){
			el[1].options[i].style.color = "#808080";
			el[1].options[i].setAttribute('disabled','disabled');
		}
	}
	el[1].selectedIndex = el[1].preSelIndex = 0;
	if (el[0].selectedIndex == 0) chkOption(el[1]);

	if(el[0].selectedIndex != '0'){
		var txt = document.getElementsByName('opt_txt[]');
		var vidx = el[0].selectedIndex - 1;
		var v = el[0][el[0].selectedIndex].value;
		txt[0].value = v + '|' + vidx;
		subOption_fashion();
	}
}
/* 필수 옵션 분리형 스크립트 end */

function chkOptimg(){
	var opt = document.getElementsByName('opt[]');
	var key = opt[0].selectedIndex;
	var opt1 = opt[0][key].value;
	var ropt = opt1.split('|');
	chgOptimg(ropt[0])
}

function chgOptimg(opt1){
	if(opt1img[opt1]){
		objImg.src = (/^http(s)?:\/\//.test(opt1img[opt1])) ? opt1img[opt1] : "../data/goods/"+opt1img[opt1];
	}else{
		objImg.src = (/^http(s)?:\/\//.test('<?php echo $TPL_VAR["r_img"][ 0]?>')) ? '<?php echo $TPL_VAR["r_img"][ 0]?>' : "../data/goods/<?php echo $TPL_VAR["r_img"][ 0]?>";
	}

<?php if($TPL_VAR["detailView"]=='y'){?>
	objImg.setAttribute("lsrc", objImg.src);
	ImageScope.setImage(objImg, beforeScope, afterScope);
<?php }?>
}

function chkOption(obj)
{
	if (!selectDisabled(obj)) return false;
}

function buyableMember(buyable) // 회원 전용 구매 상품 - 일반결제
{
	if(buyable == 'buyable2')
	{
		if(confirm("회원 전용 구매 상품입니다. 로그인 화면으로 이동합니다.")){
			var returnUrl = "<?php echo urlencode($TPL_VAR["returnUrl"])?>";
			location.href = "../member/login.php?returnUrl=" + returnUrl;
		}
		else {
			return;
		}
	}
	else if(buyable == 'buyable3') {
		alert("특정 회원 전용 구매 상품입니다.");
	}
}

function act(target)
{
	var form = document.frmView;
	form.action = target + ".php";

	var opt_cnt = 0, data;

	nsGodo_MultiOption.clearField();

	for (var k in nsGodo_MultiOption.data) {
		data = nsGodo_MultiOption.data[k];
		if (data && typeof data == 'object') {
			nsGodo_MultiOption.addField(data, opt_cnt);
			opt_cnt++;
		}
	}

	if (opt_cnt > 0) {
		form.submit();
	}
	else {
		if (chkGoodsForm(form)) form.submit();
	}

	return;
}

function chgImg(obj)
{
	var objImg = document.getElementById('objImg');
	if (obj.getAttribute("ssrc")) objImg.src = obj.src.replace(/\/t\/[^$]*$/g, '/')+obj.getAttribute("ssrc");
	else objImg.src = obj.src.replace("/t/","/");
<?php if($TPL_VAR["detailView"]=='y'){?>
	// 디테일뷰 추가내용 2010.11.09
	if (obj.getAttribute("lsrc")) objImg.setAttribute("lsrc", obj.src.replace(/\/t\/[^$]*$/g, '/')+obj.getAttribute("lsrc"));
	else objImg.setAttribute("lsrc", obj.getAttribute("src").replace("/t/", "/").replace("_sc.", '.'));
	ImageScope.setImage(objImg, beforeScope, afterScope);
	// 디테일뷰 추가내용 2010.11.09
<?php }?>
}

function innerImgResize()	// 본문 이미지 크기 리사이징
{
	var objContents = document.getElementById('contents');
	var innerWidth = 645;
	var img = objContents.getElementsByTagName('img');
	for (var i=0;i<img.length;i++){
		img[i].onload = function(){
			if (this.width>innerWidth) this.width = innerWidth;
		};
	}
}

<?php if($TPL_VAR["detailView"]=='y'){?>
// 디테일뷰 추가내용 2010.11.09
function beforeScope() {
	document.getElementsByName("frmView")[0].style.visibility = "hidden";
}

function afterScope() {
	document.getElementsByName("frmView")[0].style.visibility = "visible";
}
// 디테일뷰 추가내용 2010.11.09
<?php }?>

<?php if($TPL_VAR["naverNcash"]=='Y'){?>
function mileage_info() {
	window.open("http://static.mileage.naver.net/static/20130708/ext/intro.html", "mileageIntroPopup", "width=404, height=412, status=no, resizable=no");
}
<?php }?>

function qr_explain()
{
	var qrExplainObj = document.getElementById("qrExplain");

	qrExplainObj.style.top = event.clientY + document.body.scrollTop - 15;
	qrExplainObj.style.left = event.clientX + document.body.scrollLeft + 40;
	qrExplainObj.style.display = "block";
}

function qrExplain_close()
{
	var qrExplainObj = document.getElementById("qrExplain");
	qrExplainObj.style.display = "none";
}

</script>
<script language="javascript">
// 패션 기능관련 스크립트
function click_opt_fastion(idx,vidx,v){
	var el = document.getElementsByName('opt_txt[]');
	el[idx].value = v + '|' + vidx;

	if(idx == 0){
		var obj = document.getElementsByName('opt[]')[0];
		obj.selectedIndex = parseInt(vidx)+1 ;
		subOption(obj);
		chkOptimg();
	}else if(idx == 1){
		var obj = document.getElementsByName('opt[]')[1];
		obj.selectedIndex = vidx;
		chkOption(obj);
	}
}

function subOption_fashion()
{
	var el = document.getElementsByName('opt_txt[]');
	var el2 = document.getElementById('dtdopt2');
	var idx = el[0].value.split("|");
	var vidx = parseInt(idx[1])+1;
	var sub = opt[vidx];
	if(el2)el2.innerHTML = '';
	var n = 1;
	for (i=0;i<sub.length;i++){
		var div = sub[i].replace("')","").split("','");
		if(div[1]){
			if(opt2kind == 'img'){
				if(el2)el2.innerHTML += "<div style='width:43px;float:left;padding:5 0 5 0'><a href=\"javascript:click_opt_fastion('1','"+i+"','"+div[1]+"');nsGodo_MultiOption.set();\" name='icon2[]'><img id='opticon1_"+i+"' width='40' src='../data/goods/"+opt2icon[div[1]]+"' style='border:1px #cccccc solid' onmouseover=\"onicon(this);\" onmouseout=\"outicon(this)\" onclick=\"clicon(this)\"></a></div>";
			}else{
				if(el2)el2.innerHTML += "<div style='width:18px;float:left;padding-top:5px'><a href=\"javascript:click_opt_fastion('1','"+i+"','"+div[1]+"');subOption_fashion();nsGodo_MultiOption.set();\" name='icon2[]'><span style=\"float:left;width:15;height:15;border:1px #cccccc solid;background-color:#"+opt2icon[div[1]]+"\" onmouseover=\"onicon(this);\" onmouseout=\"outicon(this)\" onclick=\"clicon(this)\"></span></a></div>";
			}
		}else n++;
	}
}

function onicon(obj){
	oldborder = obj.style.border;
	obj.style.border="1 #333333 solid";
}
function outicon(obj){
	obj.style.border = oldborder;
}

function clicon(obj){
	var p = obj.parentNode.name;
	var ac = document.getElementsByName(p);
	if(ac){
		for(var i=0;i<ac.length;i++){
			ac[i].childNodes[0].style.border = "1 #cccccc solid";
		}
		obj.style.border="1 #333333 solid";
		oldborder="1 #333333 solid";
	}
}

function selicon(obj){
	var el = document.getElementsByName('opt[]');
	var idx = obj.selectedIndex - 1;
	if(obj == el[0]){
		var v = document.getElementsByName('icon[]');
		if (v.length > 0) {
			v = v[idx].childNodes[0];
			clicon(v);
		}

	}else{
		var v = document.getElementsByName('icon2[]');
		if (v.length > 0) {
			v = v[idx].childNodes[0];
			clicon(v);
		}
	}
}

function cp_explain(obj)
{
	var cp_explainObj = document.getElementById("cp_explain" + obj);
	cp_explainObj.style.top = event.clientY + document.body.scrollTop;
	cp_explainObj.style.left = event.clientX + document.body.scrollLeft ;
	cp_explainObj.style.display = "block";
}

function cp_explain_close(obj)
{
	var cp_explainObj = document.getElementById("cp_explain"+ obj);
	cp_explainObj.style.display = "none";
}
function fnRequestStockedNoti(goodsno) {
	window.open('./popup_request_stocked_noti.php?goodsno='+goodsno,360,230, 'scrollbars=no');
}

function fnPreviewGoods_(goodsno) {
	popup('../goods/goods_view.php?goodsno='+goodsno+'&preview=y','800','450');
}

function fnGodoTooltipShow_(obj) {

	var pos_x =0;
	var pos_y =0;
	var tooltip = document.getElementById('el-godo-tooltip-related');
	tooltip.innerText = obj.getAttribute('tooltip');

	if (document.documentElement.scrollTop > 0) {
		pos_x = event.clientX + document.documentElement.scrollLeft;
		pos_y = event.clientY + document.documentElement.scrollTop;
	} else {
		pos_x = event.clientX + document.body.scrollLeft;
		pos_y = event.clientY + document.body.scrollTop;
	}

	tooltip.style.top = (pos_y + 10) + 'px';
	tooltip.style.left = (pos_x + 10) + 'px';
	tooltip.style.display = 'block';
}

function fnGodoTooltipHide_(obj) {
	var tooltip = document.getElementById('el-godo-tooltip-related');
	tooltip.innerText = '';
	tooltip.style.display = 'none';
}
</script>
<div align=right style="padding:5px 10px;">HOME > <?php echo currPosition($TPL_VAR["category"])?></div><p>


<div class="indiv"><!-- Start indiv -->

<div style="height:27px;">
<div style="float:left;"><?php echo $GLOBALS["prevView"]?></div>
<div style="float:right;"><?php echo $GLOBALS["nextView"]?></div>
<div style="clear:both"></div>
</div>

<!-- 상품 이미지 -->
<div style="margin:0px auto 0px auto">
<div style="width:49%;float:left;text-align:center;">
<div style="padding-bottom:10"><span onclick="popup('goods_popup_large.php?goodsno=<?php echo $TPL_VAR["goodsno"]?>',800,600)" style="cursor:pointer"><!--디테일뷰수정--><?php if($TPL_VAR["detailView"]=='y'){?><?php if($TPL_VAR["sc_img"][ 0]){?><?php echo goodsimg($TPL_VAR["sc_img"][ 0], 300,'id="objImg"','','zoom_view')?><?php }else{?><?php echo goodsimg($TPL_VAR["r_img"][ 0], 300,'id="objImg"','','zoom_view')?><?php }?><?php }else{?><?php echo goodsimg($TPL_VAR["r_img"][ 0], 300,'id=objImg')?><?php }?><!--디테일뷰수정--></span></div>
<div style="padding-bottom:10">
<img src="/shop/data/skin/standard/img/common/btn_zoom.gif" onclick="popup('goods_popup_large.php?goodsno=<?php echo $TPL_VAR["goodsno"]?>',800,600)" style="cursor:pointer" align=absmiddle>
</div>
<div align=center>
<?php if($TPL_t_img_1){$TPL_I1=-1;foreach($TPL_VAR["t_img"] as $TPL_V1){$TPL_I1++;?>
<?php if($TPL_VAR["detailView"]=='y'){?>
<?php echo goodsimg($TPL_V1, 45,"onmouseover='chgImg(this)' ssrc='".$TPL_VAR["sc_img"][$TPL_I1]."' lsrc='".$TPL_VAR["r_img"][$TPL_I1]."' class=hand style='border-width:1; border-style:solid; border-color:#cccccc'")?>

<?php }else{?>
<?php echo goodsimg($TPL_V1, 45,"onmouseover='chgImg(this)' class=hand style='border-width:1; border-style:solid; border-color:#cccccc'")?>

<?php }?>
<?php }}?>
</div>
</div>

<!-- 상품 스펙 리스트 -->
<div id=goods_spec style="width:49%;float:left;">
<!--디테일뷰수정--><?php if($TPL_VAR["detailView"]=='y'){?><div id="zoom_view" style="display:none; position:absolute; width:340px; height:370px;"></div><?php }?><!--디테일뷰수정-->
<form name=frmView method=post onsubmit="return false">
<input type=hidden name=mode value="addItem">
<input type=hidden name=goodsno value="<?php echo $TPL_VAR["goodsno"]?>">
<input type=hidden name=goodsCoupon value="<?php echo $TPL_VAR["coupon"]?>">
<?php if($TPL_VAR["min_ea"]> 1){?><input type="hidden" name="min_ea" value="<?php echo $TPL_VAR["min_ea"]?>"><?php }?>
<?php if($TPL_VAR["max_ea"]!='0'){?><input type="hidden" name="max_ea" value="<?php echo $TPL_VAR["max_ea"]?>"><?php }?>
<div style="padding:10px 0 10px 5px" align=left>
<b style="font:bold 12pt 돋움;">
<?php if($TPL_VAR["clevel"]=='0'||$TPL_VAR["slevel"]>=$TPL_VAR["clevel"]){?>
<?php echo $TPL_VAR["goodsnm"]?>

<?php }elseif($TPL_VAR["slevel"]<$TPL_VAR["clevel"]&&$TPL_VAR["auth_step"][ 1]=='Y'){?>
<?php echo $TPL_VAR["goodsnm"]?>

<?php }?>
</b>
</div>
<div style="padding:0 0 10px 5px;font:11px dotum;letter-spacing:-1px;color:#666666"><?php echo $TPL_VAR["shortdesc"]?></div>
<?php if($TPL_VAR["clevel"]=='0'||$TPL_VAR["slevel"]>=$TPL_VAR["clevel"]||($TPL_VAR["slevel"]<$TPL_VAR["clevel"]&&$TPL_VAR["auth_step"][ 2]=='Y')){?>
<table border=0 cellpadding=0 cellspacing=0 class=top>
	<tr><td height=2></td></tr>
<?php if($TPL_VAR["sales_status"]=='ing'){?>
	<!--tr><td colspan="2"><span style="padding-bottom:5px; padding-left:14px; color:#EF1C21">절찬리 판매중!!</span></td></tr-->
<?php }elseif($TPL_VAR["sales_status"]=='range'){?>
	<tr><th>남은시간 :</th><td><span id="el-countdown-1" style="padding-bottom:5px;font:13pt bold;color:#EF1C21"></span></td></tr>
	<script type="text/javascript">
	Countdown.init('<?php echo date('Y-m-d H:i:s',$TPL_VAR["sales_range_end"])?>', 'el-countdown-1');
	</script>
<?php }elseif($TPL_VAR["sales_status"]=='before'){?>
	<tr><td colspan="2"><span style="padding-bottom:5px; padding-left:14px; color:#EF1C21"><?php echo date('Y-m-d H:i:s',$TPL_VAR["sales_range_start"])?> 판매시작합니다.</span></td></tr>
<?php }elseif($TPL_VAR["sales_status"]=='end'){?>
	<tr><td colspan="2"><span style="padding-bottom:5px; padding-left:14px; color:#EF1C21">판매가 종료되었습니다.</span></td></tr>
<?php }?>

<?php if($TPL_VAR["runout"]&&$GLOBALS["cfg_soldout"]["price"]=='image'){?>
	<tr><th>판매가격 :</th><td><img src="../data/goods/icon/custom/soldout_price"></td></tr>
<?php }elseif($TPL_VAR["runout"]&&$GLOBALS["cfg_soldout"]["price"]=='string'){?>
	<tr><th>판매가격 :</th><td><b><?php echo $GLOBALS["cfg_soldout"]["price_string"]?></b></td></tr>
<?php }elseif(!$TPL_VAR["strprice"]){?>
	<tr>
		<th>판매가격 :</th>
		<td>
		<b><span id=price><?php echo number_format($TPL_VAR["price"])?></span>원</b>
		</td>
	</tr>
<?php if($TPL_VAR["special_discount_amount"]){?>
	<tr>
		<th>상품할인금액 :</th>
		<td style="font-weight:bold">-<?php echo number_format($TPL_VAR["special_discount_amount"])?>원</span></b></td>
	</tr>
<?php }?>
<?php if($TPL_VAR["memberdc"]){?>
	<tr>
		<th>회원할인가 :</th>
		<td style="font-weight:bold"><span id=obj_realprice><?php echo number_format($TPL_VAR["realprice"])?>원&nbsp;(-<?php echo number_format($TPL_VAR["memberdc"])?>원)</span></b></td>
	</tr>
<?php }?>
<?php if($TPL_VAR["coupon"]){?>
	<tr><th>쿠폰적용가 :</th>
	<td>
	<span id=obj_coupon style="font-weight:bold;color:#EF1C21"><?php echo number_format($TPL_VAR["couponprice"])?>원&nbsp;(-<?php echo number_format($TPL_VAR["coupon"])?>원)</span>
	<div><?php echo $TPL_VAR["about_coupon"]?></div>
	</td></tr>
<?php }?>
<?php if($TPL_VAR["consumer"]){?>
	<tr>
		<th>소비자가격 :</th>
		<td>
		<span id=consumer><?php echo number_format($TPL_VAR["consumer"])?></span>원
		</td>
	</tr>
<?php }?>
	<tr><th>적립금 :</th><td><span id=reserve><?php echo number_format($TPL_VAR["reserve"])?></span>원</td></tr>
<?php if($TPL_VAR["naverNcash"]=='Y'){?>
	<tr id="naver-mileage-accum" style="display: none;">
		<th>네이버&nbsp;&nbsp;<br/>마일리지 :</th>
		<td>
<?php if($TPL_VAR["exception"]){?>
			<?php echo $TPL_VAR["exception"]?>

<?php }else{?>
			<span id="naver-mileage-accum-rate" style="font-weight:bold;color:#1ec228;"></span> 적립
<?php }?>
			<img src="<?php echo $GLOBALS["cfg"]["rootDir"]?>/proc/naver_mileage/images/n_mileage_info4.png" onclick="javascript:mileage_info();" style="cursor: pointer; vertical-align: middle;">
		</td>
	</tr>
<?php }?>
<?php if($TPL_VAR["coupon_emoney"]){?>
	<tr><th>쿠폰적립금 :</th>
	<td>
	<span id=obj_coupon_emoney style="font-weight:bold;color:#EF1C21"></span> &nbsp;<span style="font:bold 9pt tahoma; color:#FF0000" ><?php echo number_format($TPL_VAR["coupon_emoney"])?>원</span>
	</td></tr>
<?php }?>
<?php if($TPL_VAR["delivery_type"]== 1){?>
	<tr><th>배송비 :</th><td>무료배송</td></tr>
<?php }elseif($TPL_VAR["delivery_type"]== 2){?>
	<tr><th>개별배송비 :</th><td><?php echo number_format($TPL_VAR["goods_delivery"])?>원</td></tr>
<?php }elseif($TPL_VAR["delivery_type"]== 3){?>
	<tr><th>착불배송비 :</th><td><?php echo number_format($TPL_VAR["goods_delivery"])?>원</td></tr>
<?php }elseif($TPL_VAR["delivery_type"]== 4){?>
	<tr><th>고정배송비 :</th><td><?php echo number_format($TPL_VAR["goods_delivery"])?>원</td></tr>
<?php }elseif($TPL_VAR["delivery_type"]== 5){?>
	<tr><th>수량별배송비 :</th><td><?php echo number_format($TPL_VAR["goods_delivery"])?>원 (수량에 따라 배송비가 추가됩니다.)</td></tr>
<?php }?>
<?php }else{?>
	<tr><th>판매가격 :</th><td><b><?php echo $TPL_VAR["strprice"]?></b></td></tr>
<?php }?>
</table>
<?php }?>
<table border=0 cellpadding=0 cellspacing=0>
	<tr><td height=5></td></tr>
<?php if($TPL_VAR["goods_status"]){?><tr height><th>상품상태 :</th><td><?php echo $TPL_VAR["goods_status"]?></td></tr><?php }?>
<?php if($TPL_VAR["manufacture_date"]){?><tr height><th>제조일자 :</th><td><?php echo $TPL_VAR["manufacture_date"]?></td></tr><?php }?>
<?php if($TPL_VAR["effective_date_start"]){?><tr height><th>유효일자 :</th><td><?php echo $TPL_VAR["effective_date_start"]?> ~ <?php echo $TPL_VAR["effective_date_end"]?></td></tr><?php }?>
<?php if($TPL_VAR["delivery_method"]){?><tr height><th>배송방법 :</th><td><?php echo $TPL_VAR["delivery_method"]?></td></tr><?php }?>
<?php if($TPL_VAR["delivery_area"]){?><tr height><th>배송지역 :</th><td><?php echo $TPL_VAR["delivery_area"]?></td></tr><?php }?>
<?php if($TPL_VAR["goodscd"]){?><tr height><th>제품코드 :</th><td><?php echo $TPL_VAR["goodscd"]?></td></tr><?php }?>
<?php if($TPL_VAR["origin"]){?><tr><th>원산지 :</th><td><?php echo $TPL_VAR["origin"]?></td></tr><?php }?>
<?php if($TPL_VAR["maker"]){?><tr><th>제조사 :</th><td><?php echo $TPL_VAR["maker"]?></td></tr><?php }?>
<?php if($TPL_VAR["brand"]){?><tr><th>브랜드 :</th><td><?php echo $TPL_VAR["brand"]?> <a href="<?php echo url("goods/goods_brand.php?")?>&brand=<?php echo $TPL_VAR["brandno"]?>">[브랜드바로가기]</a></td></tr><?php }?>
<?php if($TPL_VAR["launchdt"]){?><tr><th>출시일 :</th><td><?php echo $TPL_VAR["launchdt"]?></td></tr><?php }?>
<?php if($TPL_ex_1){foreach($TPL_VAR["ex"] as $TPL_K1=>$TPL_V1){?><tr><th><?php echo $TPL_K1?> :</th><td><?php echo $TPL_V1?></td></tr><?php }}?>

<?php if(!$GLOBALS["opt"]){?>
	<tr><th>구매수량 :</th>
	<td>
<?php if(!$TPL_VAR["runout"]){?>
	<div style="float:left;"><input type=text name=ea size=2 value=<?php if($TPL_VAR["min_ea"]){?><?php echo $TPL_VAR["min_ea"]?><?php }else{?>1<?php }?> class=line style="text-align:right;height:18px" step="<?php if($TPL_VAR["sales_unit"]){?><?php echo $TPL_VAR["sales_unit"]?><?php }else{?>1<?php }?>" min="<?php if($TPL_VAR["min_ea"]){?><?php echo $TPL_VAR["min_ea"]?><?php }else{?>1<?php }?>" max="<?php if($TPL_VAR["max_ea"]){?><?php echo $TPL_VAR["max_ea"]?><?php }else{?>0<?php }?>" onblur="chg_cart_ea(frmView.ea,'set');"></div>
	<div style="float:left;padding-left:3">
	<div style="padding:1 0 2 0"><img src="/shop/data/skin/standard/img/common/btn_plus.gif" onClick="chg_cart_ea(frmView.ea,'up')" style="cursor:pointer"></div>
	<div><img src="/shop/data/skin/standard/img/common/btn_minus.gif" onClick="chg_cart_ea(frmView.ea,'dn')" style="cursor:pointer"></div>
	</div>
	<div style="padding-top:3; float:left">개</div>
	<div style="padding-left:10px;float:left" class="stxt">
<?php if($TPL_VAR["min_ea"]> 1){?><div>최소구매수량 : <?php echo $TPL_VAR["min_ea"]?>개</div><?php }?>
<?php if($TPL_VAR["max_ea"]!='0'){?><div>최대구매수량 : <?php echo $TPL_VAR["max_ea"]?>개</div><?php }?>
<?php if($TPL_VAR["sales_unit"]> 1){?><div>묶음주문단위 : <?php echo $TPL_VAR["sales_unit"]?>개</div><?php }?>
	</div>
<?php }else{?>
	품절된 상품입니다
<?php }?>
	</td></tr>
<?php }else{?>
	<input type=hidden name=ea step="<?php if($TPL_VAR["sales_unit"]){?><?php echo $TPL_VAR["sales_unit"]?><?php }else{?>1<?php }?>" min="<?php if($TPL_VAR["min_ea"]){?><?php echo $TPL_VAR["min_ea"]?><?php }else{?>1<?php }?>" max="<?php if($TPL_VAR["max_ea"]){?><?php echo $TPL_VAR["max_ea"]?><?php }else{?>0<?php }?>" value=<?php if($TPL_VAR["min_ea"]){?><?php echo $TPL_VAR["min_ea"]?><?php }else{?>1<?php }?>>
<?php }?>

<?php if($TPL_VAR["chk_point"]){?>
	<tr><th>고객선호도 :</th><td><?php if((is_array($TPL_R1=array_fill( 0,$TPL_VAR["chk_point"],''))&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?>★<?php }}?></td></tr>
<?php }?>
<?php if($TPL_VAR["icon"]){?><tr><th>제품상태 :</th><td><?php echo $TPL_VAR["icon"]?></td></tr><?php }?>
</table>

<?php if(!$TPL_VAR["strprice"]){?>

<!-- 추가 옵션 입력형 -->
<?php if($GLOBALS["addopt_inputable"]){?>
<table border=0 cellpadding=0 cellspacing=0 class=top>
<?php if($TPL__addopt_inputable_1){$TPL_I1=-1;foreach($GLOBALS["addopt_inputable"] as $TPL_K1=>$TPL_V1){$TPL_I1++;?>
	<tr><th><?php echo $TPL_K1?> :</th>
	<td>
		<input type="hidden" name="_addopt_inputable[]" value="">
		<input type="text" name="addopt_inputable[]" label="<?php echo $TPL_K1?>" option-value="<?php echo $TPL_V1["sno"]?>^<?php echo $TPL_K1?>^<?php echo $TPL_V1["opt"]?>^<?php echo $TPL_V1["addprice"]?>" value="" <?php if($GLOBALS["addopt_inputable_req"][$TPL_I1]){?>required fld_esssential<?php }?> maxlength="<?php echo $TPL_V1["opt"]?>">
	</td></tr>
<?php }}?>
</table>
<?php }?>

<!-- 필수 옵션 일체형 -->
<?php if($GLOBALS["opt"]&&$GLOBALS["typeOption"]=="single"){?>
<table border=0 cellpadding=0 cellspacing=0 class=top>
	<tr><td height=6></td></tr>
	<tr><th  valign="top"><?php echo $TPL_VAR["optnm"]?> :</th>
	<td>
	<div>
	<select name="opt[]" onchange="chkOption(this);chkOptimg();nsGodo_MultiOption.set();" required fld_esssential msgR="<?php echo $TPL_VAR["optnm"]?> 선택을 해주세요">
	<option value="">== 옵션선택 ==
<?php if($TPL__opt_1){foreach($GLOBALS["opt"] as $TPL_V1){?><?php if((is_array($TPL_R2=$TPL_V1)&&!empty($TPL_R2)) || (is_object($TPL_R2) && in_array("Countable", class_implements($TPL_R2)) && $TPL_R2->count() > 0)) {foreach($TPL_R2 as $TPL_V2){?>
	<option value="<?php echo $TPL_V2["opt1"]?><?php if($TPL_V2["opt2"]){?>|<?php echo $TPL_V2["opt2"]?><?php }?>" <?php if($TPL_VAR["usestock"]&&!$TPL_V2["stock"]){?> disabled class=disabled<?php }?>><?php echo $TPL_V2["opt1"]?><?php if($TPL_V2["opt2"]){?>/<?php echo $TPL_V2["opt2"]?><?php }?> <?php if($TPL_V2["price"]!=$TPL_VAR["price"]){?>(<?php echo number_format($TPL_V2["price"])?>원)<?php }?>
<?php if($TPL_VAR["usestock"]&&!$TPL_V2["stock"]){?> [품절]<?php }?>
<?php }}?><?php }}?>
	</select></div>
	</td>
	</tr>
	<tr><td height=6></td></tr>
</table>
<?php }?>

<!-- 필수 옵션 분리형 -->
<?php if($GLOBALS["opt"]&&$GLOBALS["typeOption"]=="double"){?>
<table border=0 cellpadding=0 cellspacing=0 class=top>
	<tr><td height=6></td></tr>
<?php if($TPL__optnm_1){$TPL_I1=-1;foreach($GLOBALS["optnm"] as $TPL_V1){$TPL_I1++;?>
	<tr><th valign="top" ><?php echo $TPL_V1?> :</th>
	<td >

	<!-- 옵션 선택 -->
	<div>
<?php if(!$TPL_I1){?>
	<div>
	<select name="opt[]" onchange="subOption(this);chkOptimg();selicon(this);nsGodo_MultiOption.set();" required fld_esssential msgR="<?php echo $TPL_V1?> 선택을 해주세요">
	<option value="">== 옵션선택 ==
<?php if((is_array($TPL_R2=($GLOBALS["opt"]))&&!empty($TPL_R2)) || (is_object($TPL_R2) && in_array("Countable", class_implements($TPL_R2)) && $TPL_R2->count() > 0)) {foreach($TPL_R2 as $TPL_K2=>$TPL_V2){?><option value="<?php echo $TPL_K2?>"><?php echo $TPL_K2?><?php }}?>
	</select>
	</div>
<?php }else{?>
	<select name="opt[]" onchange="chkOption(this);selicon(this);nsGodo_MultiOption.set();" required fld_esssential msgR="<?php echo $TPL_V1?> 선택을 해주세요"><option value="">==선택==</select>
<?php }?>
	</div>

	<!-- 옵션 이미지 아이콘 -->
<?php if($TPL_VAR["optkind"][$TPL_I1]=='img'){?>
<?php if(!$TPL_I1){?>
<?php if((is_array($TPL_R2=$GLOBALS["opticon"][$TPL_I1])&&!empty($TPL_R2)) || (is_object($TPL_R2) && in_array("Countable", class_implements($TPL_R2)) && $TPL_R2->count() > 0)) {$TPL_I2=-1;foreach($TPL_R2 as $TPL_K2=>$TPL_V2){$TPL_I2++;?>
		<div style='width:43px;float:left;padding:5 0 5 0'><a href="javascript:click_opt_fastion('<?php echo $TPL_I1?>','<?php echo $TPL_I2?>','<?php echo $GLOBALS["opt"][$TPL_K2][$TPL_I2]["opt1"]?>');" name="icon[]"><img width="40" id="opticon0_<?php echo $TPL_I2?>" id="opticon_<?php echo $TPL_I1?>_<?php echo $TPL_I2?>" style="border:1px #cccccc solid" src='../data/goods/<?php echo $TPL_V2?>'  onmouseover="onicon(this);chgOptimg('<?php echo $TPL_K2?>');" onmouseout="outicon(this)" onclick="clicon(this)"></a></div>
<?php }}?>
<?php }else{?>
	<div id="dtdopt2"></div>
<?php }?>
<?php }?>

	<!-- 옵션 색상타입 아이콘 -->
<?php if($TPL_VAR["optkind"][$TPL_I1]=='color'){?>
<?php if(!$TPL_I1){?>
<?php if((is_array($TPL_R2=$GLOBALS["opticon"][$TPL_I1])&&!empty($TPL_R2)) || (is_object($TPL_R2) && in_array("Countable", class_implements($TPL_R2)) && $TPL_R2->count() > 0)) {$TPL_I2=-1;foreach($TPL_R2 as $TPL_K2=>$TPL_V2){$TPL_I2++;?>
		<div style='width:18px;float:left;padding-top:5px ' ><a href="javascript:click_opt_fastion('<?php echo $TPL_I1?>','<?php echo $TPL_I2?>','<?php echo $TPL_K2?>');" style="cursor:hand;"  name="icon[]"><span  style="float:left;width:15;height:15;border:1px #cccccc solid;background-color:#<?php echo $TPL_V2?>" onmouseover="onicon(this);chgOptimg('<?php echo $TPL_K2?>');" onmouseout="outicon(this)" onclick="clicon(this)"></span></a></div>
<?php }}?>
<?php }else{?>
	<div id="dtdopt2"></div>
<?php }?>
<?php }?>

	<input type="hidden" name="opt_txt[]" value="">
	</td></tr>
<?php }}?>
	<tr><td height=6></td></tr>
</table>
<script>subOption(document.getElementsByName('opt[]')[0])</script>
<?php }?>

<!-- 추가 옵션 -->
<table border=0 cellpadding=0 cellspacing=0 class=sub>
<?php if($TPL__addopt_1){$TPL_I1=-1;foreach($GLOBALS["addopt"] as $TPL_K1=>$TPL_V1){$TPL_I1++;?>
	<tr><th><?php echo $TPL_K1?> :</th>
	<td>
<?php if($GLOBALS["addoptreq"][$TPL_I1]){?>
	<select name="addopt[]" required fld_esssential label="<?php echo $TPL_K1?>" onchange="nsGodo_MultiOption.set();">
	<option value="">==<?php echo $TPL_K1?> 선택==
<?php }else{?>
	<select name="addopt[]" label="<?php echo $TPL_K1?>" onchange="nsGodo_MultiOption.set();">
	<option value="">==<?php echo $TPL_K1?> 선택==
	<option value="-1">선택안함
<?php }?>
<?php if((is_array($TPL_R2=$TPL_V1)&&!empty($TPL_R2)) || (is_object($TPL_R2) && in_array("Countable", class_implements($TPL_R2)) && $TPL_R2->count() > 0)) {foreach($TPL_R2 as $TPL_V2){?>
	<option value="<?php echo $TPL_V2["sno"]?>^<?php echo $TPL_K1?>^<?php echo $TPL_V2["opt"]?>^<?php echo $TPL_V2["addprice"]?>"><?php echo $TPL_V2["opt"]?>

<?php if($TPL_V2["addprice"]){?>(<?php echo number_format($TPL_V2["addprice"])?>원 추가)<?php }?>
<?php }}?>
	</select>
	</td></tr>
<?php }}?>
</table>


<!-- ? 옵션 있으면 -->
<script>
var nsGodo_MultiOption = function() {

	function size(e) {

		var cnt = 0;
		var type = '';

		for (var i in e) {
			cnt++;
		}

		return cnt;
	}

	return {
		_soldout : <?php if($TPL_VAR["runout"]){?>true<?php }else{?>false<?php }?>,
		data : [],
		data_size : 0,
		_optJoin : function(opt) {

			var a = [];

			for (var i=0,m=opt.length;i<m ;i++)
			{
				if (typeof opt[i] != 'undefined' && opt[i] != '')
				{
					a.push(opt[i]);
				}
			}

			return a.join(' / ');

		},
		getFieldTag : function (name, value) {
			var el = document.createElement('input');
			el.type = "hidden";
			el.name = name;
			el.value = value;

			return el;

		},
		clearField : function() {

			var form = document.getElementsByName('frmView')[0];

			var el;

			for (var i=0,m=form.elements.length;i<m ;i++) {
				el = form.elements[i];

				if (typeof el == 'undefined' || el.tagName == "FIELDSET") continue;

				if (/^multi\_.+/.test(el.name)) {
					el.parentNode.removeChild(el);
					i--;
				}

			}

		},
		addField : function(obj, idx) {

			var _tag;
			var form = document.getElementsByName('frmView')[0];

			for(var k in obj) {

				if (typeof obj[k] == 'undefined' || typeof obj[k] == 'function' || (k != 'opt' && k != 'addopt' && k != 'ea' && k != 'addopt_inputable')) continue;

				switch (k)
				{
					case 'ea':
						_tag = this.getFieldTag('multi_'+ k +'['+idx+']', obj[k]);
						form.appendChild(_tag);
						break;
					case 'addopt_inputable':
					case 'opt':
					case 'addopt':
						//hasOwnProperty
						for(var k2 in obj[k]) {
							if (typeof obj[k][k2] == 'function') continue;
							_tag = this.getFieldTag('multi_'+ k +'['+idx+'][]', obj[k][k2]);
							form.appendChild(_tag);
						}

						break;
					default :
						continue;
						break;
				}
			}
		},
		set : function() {

			var add = true;

			// 선택 옵션
			var opt = document.getElementsByName('opt[]');
			for (var i=0,m=opt.length;i<m ;i++ )
			{
				if (typeof(opt[i])!="undefined") {
					if (opt[i].value == '') add = false;
				}
			}

			// 추가 옵션?
			var addopt = document.getElementsByName('addopt[]');
			for (var i=0,m=addopt.length;i<m ;i++ )
			{
				if (typeof(addopt[i])!="undefined") {
					if (addopt[i].value == '' /*&& addopt[i].getAttribute('required') != null*/) add = false;
				}
			}

			// 입력 옵션은 이곳에서 체크 하지 않는다.
			if (add == true)
			{
				this.add();
			}
		},
		del : function(key) {

			this.data[key] = null;
			var tr = document.getElementById(key);
			tr.parentNode.removeChild(tr);
			this.data_size--;

			// 총 금액
			this.totPrice();

		},
		add : function() {

			var self = this;

			if (self._soldout)
			{
				alert("품절된 상품입니다.");
				return;
			}

			var form = document.frmView;
			if(!(form.ea.value>0))
			{
				alert("구매수량은 1개 이상만 가능합니다");
				return;
			}
			else
			{
				try
				{
					var step = form.ea.getAttribute('step');
					if (form.ea.value % step > 0) {
						alert('구매수량은 '+ step +'개 단위로만 가능합니다.');
						return;
					}
				}
				catch (e)
				{}
			}

			if (chkGoodsForm(form)) {

				var _data = {};

				_data.ea = document.frmView.ea.value;
				_data.sales_unit = document.frmView.ea.getAttribute('step') || 1;
				_data.opt = new Array;
				_data.addopt = new Array;
				_data.addopt_inputable = new Array;

				// 기본 옵션
				var opt = document.getElementsByName('opt[]');

				if (opt.length > 0) {

					_data.opt[0] = opt[0].value;
					_data.opt[1] = '';
					if (typeof(opt[1]) != "undefined") _data.opt[1] = opt[1].value;

					var key = _data.opt[0] + (_data.opt[1] != '' ? '|' + _data.opt[1] : '');

					// 가격
					if (opt[0].selectedIndex == 0) key = fkey;
					key = self.get_key(key);	// get_js_compatible_key 참고

					if (typeof(price[key])!="undefined"){

						_data.price = price[key];
						_data.reserve = reserve[key];
						_data.consumer = consumer[key];
						_data.realprice = realprice[key];
						_data.couponprice = couponprice[key];
						_data.coupon = coupon[key];
						_data.cemoney = cemoney[key];
						_data.memberdc = memberdc[key];
						_data.special_discount_amount = special_discount_amount[key];

					}
					else {
						// @todo : 메시지 정리
						alert('추가할 수 없음.');
						return;
					}

				}
				else {
					// 옵션이 없는 경우(or 추가 옵션만 있는 경우) 이므로 멀티 옵션 선택은 불가.
					return;
				}

				// 추가 옵션
				var addopt = document.getElementsByName('addopt[]');
				var tmp_arr_addopt	= [];	// 추가옵션 체크키
				var addopt_key		= '';	// 추가옵션 키
				for (var i=0,m=addopt.length;i<m ;i++ ) {

					if (typeof addopt[i] == 'object') {
						_data.addopt.push(addopt[i].value);

						// 추가옵션 키 정렬
						tmp_arr_addopt	= addopt[i].value.split('^');
						addopt_key		= addopt_key + tmp_arr_addopt[0];
					}

				}
				// 추가옵션 키 생성
				if (addopt_key) {
					addopt_key	= self.get_key(addopt_key);
				}

				// 입력 옵션
				var addopt_inputable = document.getElementsByName('addopt_inputable[]');
				var addopt_input_key	= '';	// 입력옵션 키
				for (var i=0,m=addopt_inputable.length;i<m ;i++ ) {

					if (typeof addopt_inputable[i] == 'object') {
						var v = addopt_inputable[i].value.trim();
						if (v) {
							var tmp = addopt_inputable[i].getAttribute("option-value").split('^');
							tmp[2] = v;
							_data.addopt_inputable.push(tmp.join('^'));

							// 입력옵션 키 정렬
							addopt_input_key	= addopt_input_key + v;
						}

						// 필드값 초기화
						addopt_inputable[i].value = '';

					}

				}
				// 입력옵션 키 생성
				if (addopt_input_key) {
					addopt_input_key	= self.get_key(addopt_input_key);
				}

				// 상품키 재세팅
				var key	= key + (addopt_key != '' ? '^' + addopt_key : '') + (addopt_input_key != '' ? '^' + addopt_input_key : '');

				// 이미 추가된 옵션인지
				if (self.data[key] != null)
				{
					alert('이미 추가된 옵션입니다.');
					return false;
				}

				// 옵션 박스 초기화
				for (var i=0,m=addopt.length;i<m ;i++ )
				{
					if (typeof addopt[i] == 'object') {
						addopt[i].selectedIndex = 0;
					}
				}
				//opt[0].selectedIndex = 0;
				//subOption(opt[0]);

				document.getElementById('el-multi-option-display').style.display = 'block';

				// 행 추가
				var childs = document.getElementById('el-multi-option-display').childNodes;
				for (var k in childs)
				{
					if (childs[k].tagName == 'TABLE') {
						var table = childs[k];
						break;
					}
				}

				var td, tr = table.insertRow(0);
				var html = '';

				tr.id = key;

				// 입력 옵션명
				td = tr.insertCell(-1);
				html = '<div style="font-size:11px;color:#010101;padding:3px 0 0 8px;">';
				var tmp,tmp_addopt = [];
				for (var i=0,m=_data.addopt_inputable.length;i<m ;i++ )
				{
					tmp = _data.addopt_inputable[i].split('^');
					if (tmp[2]) tmp_addopt.push(tmp[2]);
				}
				html += self._optJoin(tmp_addopt);
				html += '</div>';

				// 옵션명
				html += '<div style="font-size:11px;color:#010101;padding:3px 0 0 8px;">';
				html += self._optJoin(_data.opt);
				html += '</div>';

				// 추가 옵션명
				html += '<div style="font-size:11px;color:#A0A0A0;padding:3px 0 0 8px;">';
				var tmp,tmp_addopt = [];
				for (var i=0,m=_data.addopt.length;i<m ;i++ )
				{
					tmp = _data.addopt[i].split('^');
					if (tmp[2]) tmp_addopt.push(tmp[2]);
				}
				html += self._optJoin(tmp_addopt);
				html += '</div>';

				td.innerHTML = html;

				// 수량
				td = tr.insertCell(-1);
				html = '';
				html += '<div style="float:left;"><input type=text name=_multi_ea[] id="el-ea-'+key+'" size=2 value='+ _data.ea +' style="border:1px solid #D3D3D3;width:30px;text-align:right;height:20px" onblur="nsGodo_MultiOption.ea(\'set\',\''+key+'\',this.value);"></div>';
				html += '<div style="float:left;padding-left:3">';
				html += '<div style="padding:1 0 2 0"><img src="/shop/data/skin/standard/img/common/btn_multioption_ea_up.gif" onClick="nsGodo_MultiOption.ea(\'up\',\''+key+'\');" style="cursor:pointer"></div>';
				html += '<div><img src="/shop/data/skin/standard/img/common/btn_multioption_ea_down.gif" onClick="nsGodo_MultiOption.ea(\'down\',\''+key+'\');" style="cursor:pointer"></div>';
				html += '</div>';
				td.innerHTML = html;

				// 옵션가격
				_data.opt_price = _data.price;
				for (var i=0,m=_data.addopt.length;i<m ;i++ )
				{
					tmp = _data.addopt[i].split('^');
					if (tmp[3]) _data.opt_price = _data.opt_price + parseInt(tmp[3]);
				}
				for (var i=0,m=_data.addopt_inputable.length;i<m ;i++ )
				{
					tmp = _data.addopt_inputable[i].split('^');
					if (tmp[3]) _data.opt_price = _data.opt_price + parseInt(tmp[3]);
				}
				td = tr.insertCell(-1);
				td.style.cssText = 'padding-right:10px;text-align:right;font-weight:bold;color:#6A6A6A;';
				html = '';
				html += '<span id="el-price-'+key+'">'+comma( _data.opt_price *  _data.ea) + '원</span>';
				html += '<a href="javascript:void(0);" onClick="nsGodo_MultiOption.del(\''+key+'\');return false;"><img src="/shop/data/skin/standard/img/common/btn_multioption_del.gif"></a>';
				td.innerHTML = html;

				self.data[key] = _data;
				self.data_size++;

				// 총 금액
				self.totPrice();


			}
		},
		ea : function(dir, key,val) {	// up, down

			var min_ea = 0, max_ea = 0, remainder = 0;

			if (document.frmView.min_ea) min_ea = parseInt(document.frmView.min_ea.value);
			if (document.frmView.max_ea) max_ea = parseInt(document.frmView.max_ea.value);

			if (dir == 'up') {
				this.data[key].ea = (max_ea != 0 && max_ea <= this.data[key].ea) ? max_ea : parseInt(this.data[key].ea) + parseInt(this.data[key].sales_unit);
			}
			else if (dir == 'down')
			{
				if ((parseInt(this.data[key].ea) - 1) > 0)
				{
					this.data[key].ea = (min_ea != 0 && min_ea >= this.data[key].ea) ? min_ea : parseInt(this.data[key].ea) - parseInt(this.data[key].sales_unit);
				}

			}
			else if (dir == 'set') {

				if (val && !isNaN(val))
				{
					val = parseInt(val);

					if (max_ea != 0 && val > max_ea)
					{
						val = max_ea;
					}
					else if (min_ea != 0 && val < min_ea) {
						val = min_ea;
					}
					else if (val < 1)
					{
						val = parseInt(this.data[key].sales_unit);
					}

					remainder = val % parseInt(this.data[key].sales_unit);

					if (remainder > 0) {
						val = val - remainder;
					}

					this.data[key].ea = val;

				}
				else {
					alert('수량은 1 이상의 숫자로만 입력해 주세요.');
					return;
				}
			}

			document.getElementById('el-ea-'+key).value = this.data[key].ea;
			document.getElementById('el-price-'+key).innerText = comma(this.data[key].ea * this.data[key].opt_price) + '원';

			// 총금액
			this.totPrice();

		},
		totPrice : function() {
			var self = this;
			var totprice = 0;
			for (var i in self.data)
			{
				if (self.data[i] !== null && typeof self.data[i] == 'object') totprice += self.data[i].opt_price * self.data[i].ea;
			}

			document.getElementById('el-multi-option-total-price').innerText = comma(totprice) + '원';
		},
		get_key : function(str) {

			str = str.replace(/&/g, "&amp;").replace(/\"/g,'&quot;').replace(/</g,'&lt;').replace(/>/g,'&gt;');

			var _key = "";

			for (var i=0,m=str.length;i<m;i++) {
				_key += str.charAt(i) != '|' ? str.charCodeAt(i) : '|';
			}

			return _key.toUpperCase();
		}
	}
}();

function chkGoodsForm(form) {

	if (form.min_ea)
	{
		if (parseInt(form.ea.value) < parseInt(form.min_ea.value))
		{
			alert('최소구매수량은 ' + form.min_ea.value+'개 입니다.');
			return false;
		}
	}

	if (form.max_ea)
	{
		if (parseInt(form.ea.value) > parseInt(form.max_ea.value))
		{
			alert('최대구매수량은 ' + form.max_ea.value+'개 입니다.');
			return false;
		}
	}

	try
	{
		var step = form.ea.getAttribute('step');
		if (form.ea.value % step > 0) {
			alert('구매수량은 '+ step +'개 단위만 가능합니다.');
			return false;
		}
	}
	catch (e)
	{}

	var res = chkForm(form);

	// 입력옵션 필드값 설정
	if (res)
	{
		var addopt_inputable = document.getElementsByName('addopt_inputable[]');
		for (var i=0,m=addopt_inputable.length;i<m ;i++ ) {

			if (typeof addopt_inputable[i] == 'object') {
				var v = addopt_inputable[i].value.trim();
				if (v) {
					var tmp = addopt_inputable[i].getAttribute("option-value").split('^');
					tmp[2] = v;
					v = tmp.join('^');
				}
				else {
					v = '';
				}
				document.getElementsByName('_addopt_inputable[]')[i].value = v;
			}
		}
	}

	return res;

}

</script>

<style type="text/css">
.goods-multi-option {display:none;}
.goods-multi-option table {border:1px solid #D3D3D3;}
.goods-multi-option table td {border-bottom:1px solid #D3D3D3;padding:10px;}
</style>
<div id="el-multi-option-display" class="goods-multi-option">
	<table border="0" cellpadding="0" cellspacing="0">
	<col width=""><col width="50"><col width="80">
	</table>

	<div style="font-size:12px;text-align:right;padding:10px 20px 10px 0;border-bottom:1px solid #D3D3D3;margin-bottom:5px;">
		<img src="/shop/data/skin/standard/img/common/btn_multioption_br.gif" align="absmiddle"> 총 금액 : <span style="color:#E70103;font-weight:bold;" id="el-multi-option-total-price"></span>
	</div>
</div>
<!-- / -->

<?php }?>
<?php echo $TPL_VAR["cyworldScrap"]?>

<?php echo $TPL_VAR["snsBtn"]?>

<?php if($TPL_VAR["setGoodsConfig"]=='Y'){?>
<a href="../setGoods/?cody=<?php echo $TPL_VAR["goodsno"]?>"><img src="/shop/data/skin/standard/img/common/btn_codylink.gif"></a>
<?php }?>
<!-- 각종 버튼 -->
<div style="width:330px;">
<?php if($TPL_VAR["stocked_noti"]){?>
<div style="margin-bottom: 7px">품절된 옵션상품은 재입고 알림 신청을 통해서 입고 시 알림 서비스를 받으실 수 있습니다.</div>
<?php }?>
<?php if(!$TPL_VAR["strprice"]&&!$TPL_VAR["runout"]&&($TPL_VAR["sales_status"]=='ing'||$TPL_VAR["sales_status"]=='range')){?>
<?php if($TPL_VAR["clevel"]=='0'||$TPL_VAR["slevel"]>=$TPL_VAR["clevel"]){?>
<?php if($TPL_VAR["goodsBuyable"]===true){?>
	<a href="javascript:act('../order/order')"><img src="/shop/data/skin/standard/img/common/btn_direct_buy.gif"></a>
	<a href="javascript:cartAdd(frmView,'<?php echo $TPL_VAR["cartCfg"]->redirectType?>')"><img src="/shop/data/skin/standard/img/common/btn_cart.gif"></a>
	<a href="javascript:act('../mypage/mypage_wishlist')"><img src="/shop/data/skin/standard/img/common/btn_wish_m_un.gif"></a>
<?php }else{?>
	<a href="javascript:buyableMember('<?php echo $TPL_VAR["goodsBuyable"]?>')"><img src="/shop/data/skin/standard/img/common/btn_direct_buy.gif"></a>
	<a href="javascript:buyableMember('<?php echo $TPL_VAR["goodsBuyable"]?>')"><img src="/shop/data/skin/standard/img/common/btn_cart.gif"></a>
	<a href="javascript:buyableMember('<?php echo $TPL_VAR["goodsBuyable"]?>')"><img src="/shop/data/skin/standard/img/common/btn_wish_m_un.gif"></a>
<?php }?>
<?php }?>
<?php }?>
<?php if($TPL_VAR["stocked_noti"]){?>
<a href="javascript:fnRequestStockedNoti('<?php echo $TPL_VAR["goodsno"]?>');"><img src="/shop/data/skin/standard/img/stocked_noti/btn_alarm_2.gif"></a>
<?php }?>
<a href="<?php echo url("goods/goods_list.php?")?>&category=<?php echo $_GET["category"]?>"><img src="/shop/data/skin/standard/img/common/btn_list.gif"></a>
</div>
<div><?php echo $TPL_VAR["Payco"]?></div>
<div><?php echo $TPL_VAR["naverCheckout"]?></div>
<div><?php echo $TPL_VAR["auctionIpayBtn"]?></div>
<div><?php echo $TPL_VAR["qrcode_view"]?></div>
<?php echo $TPL_VAR["plusCheeseBtn"]?>

</form>
</div>
</div><p>

<table style="clear:both" border=0 cellpadding=0 cellspacing=0>
<tr>
	<td>

	<table border=0 cellpadding=0 cellspacing=0>
	<tr>
		<TD style="background: URL(/shop/data/skin/standard/img/common/bar_detail_01.gif) no-repeat;" nowrap width="120" height="24"></TD>
		<TD style="background: URL(/shop/data/skin/standard/img/common/bar_detail_01_bg.gif) repeat-x;" width='90%'></TD>
		<TD style="background: URL(/shop/data/skin/standard/img/common/bar_detail_01_right.gif) no-repeat;" nowrap width="10" height="24"></TD>
	</tr>
	</table>

<?php if($TPL_VAR["coupon"]||$TPL_VAR["coupon_emoney"]){?>
	<!-- 할인쿠폰 다운받기 -->
	<div style="padding:10px 0">
	<table>
	<tr>
		<td><img src="/shop/data/skin/standard/img/common/coupon_txt.gif"></td>
		<td>
		<table border=0 cellpadding=0 cellspacing=0>
		<tr>
<?php if($TPL_a_coupon_1){$TPL_I1=-1;foreach($TPL_VAR["a_coupon"] as $TPL_V1){$TPL_I1++;?>
<?php if($TPL_I1% 3== 0){?>
		</tr><tr>
<?php }?>

		<td onclick="ifrmHidden.location.href='<?php echo url("proc/dn_coupon_goods.php?")?>&goodsno=<?php echo $TPL_VAR["goodsno"]?>&couponcd=<?php echo $TPL_V1["couponcd"]?>'" class=hand>

<?php if($TPL_V1["coupon_img"]== 4){?>
		<div style="font:bold 12px tahoma;color:#FF0000;text-align:center;padding:19px 40px 0 0;width:140px;height:54px;background:url('<?php echo $TPL_VAR["coupon_img_path"]?><?php echo ($TPL_V1["coupon_img_file"])?>') no-repeat;" onmouseover="cp_explain('<?php echo $TPL_I1?>');" onmouseout="cp_explain_close('<?php echo $TPL_I1?>');"><?php echo $GLOBALS["r_couponAbility"][$TPL_V1["ability"]]?><?php if(substr($TPL_V1["price"], - 1)!="%"){?><?php echo number_format($TPL_V1["price"])?>원<?php }else{?><?php echo $TPL_V1["price"]?><?php }?></div>
<?php }else{?>
		<div style="font:bold 12px tahoma;color:#FF0000;text-align:center;padding:19px 40px 0 0;width:140px;height:54px;background:url('/shop/data/skin/standard/img/common/coupon0<?php echo ($TPL_V1["coupon_img"]+ 1)?>.gif');" onmouseover="cp_explain('<?php echo $TPL_I1?>');" onmouseout="cp_explain_close('<?php echo $TPL_I1?>');"><?php echo $GLOBALS["r_couponAbility"][$TPL_V1["ability"]]?><?php if(substr($TPL_V1["price"], - 1)!="%"){?><?php echo number_format($TPL_V1["price"])?>원<?php }else{?><?php echo $TPL_V1["price"]?><?php }?></div>
<?php }?>
		<div style="padding:1 1 1 1;display:none;position:absolute;z-index:10;cursor:hand;background-color=FF0000;font:bold 12px tahoma;color:#FF0000;text-align:center;width:300px;height:100px;" id="cp_explain<?php echo $TPL_I1?>" onclick="cp_explain_close('<?php echo $TPL_I1?>');">
			<table style="padding:0 0 0 0;background-color=FFFFFF;width:300px;height:100px"><tr><td>
			<div style="color:#FF0000;text-align:center;width:300px;font:bold 12px tahoma;"><?php echo $TPL_V1["coupon_name"]?></div>
			<div style="padding:5 0 0 0;color:#FF0000;text-align:center;width:300px;font:bold 12px tahoma;"><?php echo $TPL_V1["coupon_detail"]?></div>
<?php if($TPL_V1["coupon_priodtype"]== 0){?>
			<div style="padding:10 0 0 0;text-align:center;"><?php echo $TPL_V1["coupon_sdate"]?> 부터</div>
			<div style="text-align:center;"><?php echo $TPL_V1["coupon_edate"]?> 까지</div>
<?php }else{?>
			<div style="padding:10 0 0 0;text-align:center;">발급 후 <?php echo $TPL_V1["coupon_sdate"]?> 일 간 사용 가능</div>
<?php }?>
<?php if($TPL_V1["payMethodStr"]){?>
			<div style="padding:0 0 0 0;text-align:center;width:300px;"><?php echo $TPL_V1["payMethodStr"]?></div>
<?php }?>
			</td></tr></table>
		</div>
		</td>

<?php }}?>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</div>
<?php }?>

<?php if($TPL_VAR["use_external_video"]){?>
	<div style="padding:10px 0" id="external-video">
		<?php echo youtubePlayer($TPL_VAR["external_video_url"],$TPL_VAR["external_video_size_type"],$TPL_VAR["external_video_width"],$TPL_VAR["external_video_height"])?>

	</div>
<?php }?>

<?php if($TPL_extra_info_1){?>
		<style>
		table.extra-information {background:#e0e0e0;margin:30px 0 60px 0;}
		table.extra-information th,
		table.extra-information td {font-weight:normal;text-align:left;padding-left:15px;background:#ffffff;font-family:Dotum;font-size:11px;height:28px;}

		table.extra-information th {width:15%;background:#f5f5f5;color:#515151;}
		table.extra-information td {width:35%;color:#666666;}

		</style>
		<table width=100% border=0 cellpadding=0 cellspacing=1 class="extra-information">
		<tr>
<?php if($TPL_extra_info_1){foreach($TPL_VAR["extra_info"] as $TPL_K1=>$TPL_V1){?>
			<th><?php echo $TPL_V1["title"]?></th>
			<td <?php if($TPL_V1["colspan"]> 1){?>colspan="<?php echo $TPL_V1["colspan"]?>"<?php }?>><?php echo $TPL_V1["desc"]?></td>
<?php if($TPL_V1["nkey"]&&(!$GLOBALS["extra_info"][$TPL_V1["nkey"]]||$TPL_K1% 2== 0)){?>
			</tr><tr>
<?php }?>
<?php }}?>
		</tr>
		</table>
<?php }?>

	<!-- 상세 설명 -->
	<div id=contents style="width:100%;padding:10 10 10 10;overflow:hidden;"><table width=100%>
	<tr><td>
	<?php echo $TPL_VAR["longdesc"]?></td></table>
	</div>

	<!-- 관련상품 -->
	<table border=0 cellpadding=0 cellspacing=0>
	<tr>
		<TD style="background: URL(/shop/data/skin/standard/img/common/bar_detail_02.gif) no-repeat;" nowrap width="137" height="24"></TD>
		<TD style="background: URL(/shop/data/skin/standard/img/common/bar_detail_02_bg.gif) repeat-x;" width='90%'></TD>
		<TD style="background: URL(/shop/data/skin/standard/img/common/bar_detail_02_right.gif) no-repeat;" nowrap width="10" height="24"></TD>
	</tr>
	</table>
	<div style="padding:10 10 10 10;overflow:hidden">
	<table width=100% border=0 cellpadding=0 cellspacing=0>
	<tr><td height=10></td></tr>
	<tr>
<?php if((is_array($TPL_R1=dataGoodsRelation($TPL_VAR["goodsno"],$GLOBALS["cfg_related"]["max"]))&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {$TPL_I1=-1;foreach($TPL_R1 as $TPL_V1){$TPL_I1++;?>
<?php if($TPL_I1&&$TPL_I1%$GLOBALS["cfg_related"]["horizontal"]== 0){?></tr><tr><td height=10></td></tr><tr><?php }?>
		<td align=center valign=top width="<?php echo  100/$GLOBALS["cfg_related"]["horizontal"]?>%">
<?php if($GLOBALS["cfg_related"]["dp_image"]){?><div><a href="<?php echo url("goods/goods_view.php?")?>&goodsno=<?php echo $TPL_V1["goodsno"]?>" <?php if($GLOBALS["cfg_related"]["link_type"]=='blank'){?> target="_blank"<?php }?> <?php if($GLOBALS["cfg_related"]["dp_shortdesc"]){?>onmouseover="fnGodoTooltipShow_(this)" onmousemove="fnGodoTooltipShow_(this)" onmouseout="fnGodoTooltipHide_(this)" tooltip="<?php echo $TPL_V1["shortdesc"]?>"<?php }?> ><?php echo goodsimg($TPL_V1["img_s"],$GLOBALS["cfg_related"]["size"])?></a></div><?php }?>
<?php if($GLOBALS["cfg_related"]["use_cart"]){?><div><a href="javascript:void(0);" onClick="fnPreviewGoods_(<?php echo $TPL_V1["goodsno"]?>);"><img src="<?php echo $GLOBALS["cfg_related"]["cart_icon"]?>"></a></div><?php }?>
<?php if($GLOBALS["cfg_related"]["dp_goodsnm"]){?><div style="padding:5"><a href="<?php echo url("goods/goods_view.php?")?>&goodsno=<?php echo $TPL_V1["goodsno"]?>" <?php if($GLOBALS["cfg_related"]["dp_shortdesc"]){?>onmouseover="fnGodoTooltipShow_(this)" onmousemove="fnGodoTooltipShow_(this)" onmouseout="fnGodoTooltipHide_(this)" tooltip="<?php echo $TPL_V1["shortdesc"]?>"<?php }?>><?php echo $TPL_V1["goodsnm"]?></a></div><?php }?>
<?php if($GLOBALS["cfg_related"]["dp_price"]){?><div><?php if($TPL_V1["strprice"]){?><?php echo $TPL_V1["strprice"]?><?php }else{?><b><?php echo number_format($TPL_V1["price"])?>원<?php }?></b></div><?php }?>
<?php if($TPL_V1["icon"]){?><div style="padding:5"><?php echo $TPL_V1["icon"]?></div><?php }?>

		</td>
<?php }}?>
	</tr>
	</table>
	</div>

	<div id="el-godo-tooltip-related" style="z-index:1000;display:none;position:absolute;top:0;left:0;width:<?php echo $GLOBALS["cfg_related"]["size"]?>px;padding:10px; -moz-opacity:.70; filter:alpha(opacity=70); opacity:.70;line-height:140%;" class="godo-tooltip-related">
	</div>

	<!-- 상품 공통 정보 시작 -->

<?php if((is_array($TPL_R1=commoninfo())&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?>
	<table border=0 cellpadding=0 cellspacing=0>
	<tr>
		<TD style="background: URL(/shop/data/skin/standard/img/common/bar_detail_l.gif) no-repeat;" nowrap width="10" height="24"></TD>
		<TD style="background: URL(/shop/data/skin/standard/img/common/bar_detail_c.gif) repeat-x; font-weight:bold;" width='100%'><?php echo $TPL_V1["title"]?></TD>
		<TD style="background: URL(/shop/data/skin/standard/img/common/bar_detail_r.gif) no-repeat;" nowrap width="10" height="24"></TD>
	</tr>
	</table>
	<div style="width:100%;padding:10 10 10 10;overflow:hidden">
	<table cellspacing=0 cellpadding=0>
	<tr>
		<td><?php echo $TPL_V1["info"]?></td>
	</tr>
	</table>
	</div>
<?php }}?>
	<div style="margin-bottom:20px;"></div>

	<!-- 상품 공통 정보 종료 -->

<?php if(strpos($this->template_dir,'/easy')&&is_file($this->template_dir.'/proc/_goods_guide.htm')){?>
	<?php echo $this->define('tpl_include_file_1','proc/_goods_guide.htm')?> <?php $this->print_("tpl_include_file_1",$TPL_SCP,1);?>

<?php }else{?>
	<!-- 배송안내 -->
	<table border=0 cellpadding=0 cellspacing=0>
	<tr>
		<TD style="background: URL(/shop/data/skin/standard/img/common/bar_detail_06.gif) no-repeat;" nowrap width="112" height="24"></TD>
		<TD style="background: URL(/shop/data/skin/standard/img/common/bar_detail_06_bg.gif) repeat-x;" width='90%'></TD>
		<TD style="background: URL(/shop/data/skin/standard/img/common/bar_detail_06_right.gif) no-repeat;" nowrap width="10" height="24"></TD>
	</tr>
	</table>
	<div style="width:100%;padding:10 10 10 10;overflow:hidden">

	<table cellspacing=0 cellpadding=0>
	<col valign=top width=14>
	<tr>
		<td><img src="/shop/data/skin/standard/img/dot.gif"></td>
		<td>배송비 : 기본배송료는 <?php if($GLOBALS["set"]["delivery"]["default"]){?><?php echo number_format($GLOBALS["set"]["delivery"]["default"])?>원<?php }else{?>무료<?php }?> 입니다. (도서,산간,오지 일부지역은 배송비가 추가될 수 있습니다) <?php if($GLOBALS["set"]["delivery"]["free"]){?>&nbsp;<?php echo number_format($GLOBALS["set"]["delivery"]["free"])?>원 이상 구매시 무료배송입니다.<?php }?></td></tr>
	<tr>
		<td><img src="/shop/data/skin/standard/img/dot.gif"></td>
		<td>본 상품의 평균 배송일은 일입니다.(입금 확인 후) 설치 상품의 경우 다소 늦어질수 있습니다.[배송예정일은 주문시점(주문순서)에 따른 유동성이 발생하므로 평균 배송일과는 차이가 발생할 수 있습니다.] </td></tr>
	<tr>
		<td><img src="/shop/data/skin/standard/img/dot.gif"></td>
		<td>본 상품의 배송 가능일은 일 입니다.
			배송 가능일이란 본 상품을 주문 하신 고객님들께 상품 배송이 가능한 기간을 의미합니다. (단, 연휴 및 공휴일은 기간 계산시 제외하며 현금 주문일 경우 입금일 기준 입니다.)</td></tr>
	</table>

	</div>

	<!-- 교환및반품안내 -->
	<table border=0 cellpadding=0 cellspacing=0>
	<tr>
		<TD style="background: URL(/shop/data/skin/standard/img/common/bar_detail_05.gif) no-repeat;" nowrap width="198" height="24"></TD>
		<TD style="background: URL(/shop/data/skin/standard/img/common/bar_detail_05_bg.gif) repeat-x;" width='90%'></TD>
		<TD style="background: URL(/shop/data/skin/standard/img/common/bar_detail_05_right.gif) no-repeat;" nowrap width="10" height="24"></TD>
	</tr>
	</table>
	<div style="width:100%;padding:10 10 10 10;overflow:hidden">

	<table cellspacing=0 cellpadding=0>
	<col valign=top width=14>
	<tr>
		<td><img src="/shop/data/skin/standard/img/dot.gif"></td>
		<td>상품 청약철회 가능기간은 상품 수령일로 부터 일 이내 입니다.</td></tr>
	<tr>
		<td><img src="/shop/data/skin/standard/img/dot.gif"></td>
		<td>상품 택(tag)제거 또는 개봉으로 상품 가치 훼손 시에는 일 이내라도 교환 및 반품이 불가능합니다.</td></tr>
	<tr>
		<td><img src="/shop/data/skin/standard/img/dot.gif"></td>
		<td>저단가 상품, 일부 특가 상품은 고객 변심에 의한 교환, 반품은 고객께서 배송비를 부담하셔야 합니다(제품의 하자,배송오류는 제외)</td></tr>
	<tr>
		<td><img src="/shop/data/skin/standard/img/dot.gif"></td>
		<td>일부 상품은 신모델 출시, 부품가격 변동 등 제조사 사정으로 가격이 변동될 수 있습니다.</td></tr>
	<tr>
		<td><img src="/shop/data/skin/standard/img/dot.gif"></td>
		<td>신발의 경우, 실외에서 착화하였거나 사용흔적이 있는 경우에는 교환/반품 기간내라도 교환 및 반품이 불가능 합니다.</td></tr>
	<tr>
		<td><img src="/shop/data/skin/standard/img/dot.gif"></td>
		<td>수제화 중 개별 주문제작상품(굽높이,발볼,사이즈 변경)의 경우에는 제작완료, 인수 후에는 교환/반품기간내라도 교환 및 반품이 불가능 합니다. </td></tr>
	<tr>
		<td><img src="/shop/data/skin/standard/img/dot.gif"></td>
		<td>수입,명품 제품의 경우, 제품 및 본 상품의 박스 훼손, 분실 등으로 인한 상품 가치 훼손 시 교환 및 반품이 불가능 하오니, 양해 바랍니다.</td></tr>
	<tr>
		<td><img src="/shop/data/skin/standard/img/dot.gif"></td>
		<td>일부 특가 상품의 경우, 인수 후에는 제품 하자나 오배송의 경우를 제외한 고객님의 단순변심에 의한 교환, 반품이 불가능할 수 있사오니, 각 상품의 상품상세정보를 꼭 참조하십시오. </td></tr>
	</table>

	</div>

	<!-- 결제안내
	<div style="height:26px; padding-top:10"><img src="/shop/data/skin/standard/img/common/bar_detail_07.gif"></div>
	<div style="width:100%;padding:10 10 10 10;overflow:hidden"></div>
	-->
<?php }?>

	<!-- 상품 사용기 -->
	<iframe id="inreview" src="./goods_review_list.php?goodsno=<?php echo $TPL_VAR["goodsno"]?>" frameborder="0" marginwidth="0" marginheight="0" width="100%" height="" scrolling="no"></iframe>

	<!-- 상품 질문과답변 -->
	<iframe id="inqna" src="./goods_qna_list.php?goodsno=<?php echo $TPL_VAR["goodsno"]?>" frameborder="0" marginwidth="0" marginheight="0" width="100%" height="" scrolling="no"></iframe>


	</td>
</tr>

</table>

</div><!-- End indiv -->

<div style="display:none;position:absolute;z-index:10;cursor:hand;" id="qrExplain" onclick="qrExplain_close();">
<table cellpadding="0" cellspacing="0" border="0">
<tr>
	<td width="4" height="271" valign="top" background="/shop/data/skin/standard/img/common/page02_detail_blt.gif" style="background-repeat:no-repeat"></td>
	<td  width="285" height="271" valign="top" background="/shop/data/skin/standard/img/common/page02_detail.gif"></td>
</tr>
</table>
<div style='width:289' onclick="qrExplain_close();" style="cursor:hand;text-align:center">[닫기]</div>
</div>

<?php $this->print_("footer",$TPL_SCP,1);?>

<!--디테일뷰수정-->
<?php if($TPL_VAR["detailView"]=='y'){?>
<script type="text/javascript">
var objImg = document.getElementById("objImg");
objImg.setAttribute("lsrc", objImg.getAttribute("src").replace("/t/", "/").replace("_sc.", '.'));
ImageScope.setImage(objImg, beforeScope, afterScope);
</script>
<?php }?>
<!--디테일뷰수정-->