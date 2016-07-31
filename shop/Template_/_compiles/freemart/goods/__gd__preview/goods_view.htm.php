<?php /* Template_ 2.2.7 2015/11/18 21:46:32 /www/francotr3287_godo_co_kr/shop/data/skin/freemart/goods/goods_view.htm 000124622 */  $this->include_("dataGoodsRelation","commoninfo");
if (is_array($GLOBALS["opt"])) $TPL__opt_1=count($GLOBALS["opt"]); else if (is_object($GLOBALS["opt"]) && in_array("Countable", class_implements($GLOBALS["opt"]))) $TPL__opt_1=$GLOBALS["opt"]->count();else $TPL__opt_1=0;
if (is_array($GLOBALS["opt1img"])) $TPL__opt1img_1=count($GLOBALS["opt1img"]); else if (is_object($GLOBALS["opt1img"]) && in_array("Countable", class_implements($GLOBALS["opt1img"]))) $TPL__opt1img_1=$GLOBALS["opt1img"]->count();else $TPL__opt1img_1=0;
if (is_array($TPL_VAR["t_img"])) $TPL_t_img_1=count($TPL_VAR["t_img"]); else if (is_object($TPL_VAR["t_img"]) && in_array("Countable", class_implements($TPL_VAR["t_img"]))) $TPL_t_img_1=$TPL_VAR["t_img"]->count();else $TPL_t_img_1=0;
if (is_array($TPL_VAR["ex"])) $TPL_ex_1=count($TPL_VAR["ex"]); else if (is_object($TPL_VAR["ex"]) && in_array("Countable", class_implements($TPL_VAR["ex"]))) $TPL_ex_1=$TPL_VAR["ex"]->count();else $TPL_ex_1=0;
if (is_array($GLOBALS["addopt_inputable"])) $TPL__addopt_inputable_1=count($GLOBALS["addopt_inputable"]); else if (is_object($GLOBALS["addopt_inputable"]) && in_array("Countable", class_implements($GLOBALS["addopt_inputable"]))) $TPL__addopt_inputable_1=$GLOBALS["addopt_inputable"]->count();else $TPL__addopt_inputable_1=0;
if (is_array($GLOBALS["optnm"])) $TPL__optnm_1=count($GLOBALS["optnm"]); else if (is_object($GLOBALS["optnm"]) && in_array("Countable", class_implements($GLOBALS["optnm"]))) $TPL__optnm_1=$GLOBALS["optnm"]->count();else $TPL__optnm_1=0;
if (is_array($GLOBALS["addopt"])) $TPL__addopt_1=count($GLOBALS["addopt"]); else if (is_object($GLOBALS["addopt"]) && in_array("Countable", class_implements($GLOBALS["addopt"]))) $TPL__addopt_1=$GLOBALS["addopt"]->count();else $TPL__addopt_1=0;
if (is_array($TPL_VAR["a_coupon"])) $TPL_a_coupon_1=count($TPL_VAR["a_coupon"]); else if (is_object($TPL_VAR["a_coupon"]) && in_array("Countable", class_implements($TPL_VAR["a_coupon"]))) $TPL_a_coupon_1=$TPL_VAR["a_coupon"]->count();else $TPL_a_coupon_1=0;
if (is_array($TPL_VAR["extra_info"])) $TPL_extra_info_1=count($TPL_VAR["extra_info"]); else if (is_object($TPL_VAR["extra_info"]) && in_array("Countable", class_implements($TPL_VAR["extra_info"]))) $TPL_extra_info_1=$TPL_VAR["extra_info"]->count();else $TPL_extra_info_1=0;?>
<!-- gdpart mode="open" fid="goods/goods_view.htm tpl_0" --><!-- gdline 1"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<!-- gdline 2"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><?php $this->print_("header",$TPL_SCP,1);?>

<!-- gdline 3"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->
<!-- gdline 4"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><script src="/shop/lib/js/countdown.js"></script>
<!-- gdline 5"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><style>
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
<!-- gdline 34"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->
<!-- gdline 35"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><script>
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
<!-- gdline 225"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><script language="javascript">
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

	var tooltip = document.getElementById('el-godo-tooltip-related');
	tooltip.innerText = obj.getAttribute('tooltip');

	var pos_x = event.clientX + document.body.scrollLeft + document.documentElement.scrollLeft;
	var pos_y = event.clientY + document.body.scrollTop + document.documentElement.scrollTop;

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
<!-- gdline 343"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><div align=right class="detail_view_curr_pos">HOME > <?php echo currPosition($TPL_VAR["category"])?></div><p>
<!-- gdline 344"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->
<!-- gdline 345"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->
<!-- gdline 346"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><div class="indiv"><!-- Start indiv -->
<!-- gdline 347"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->
<!-- gdline 348"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><div style="height:27px;">
<!-- gdline 349"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><div style="float:left;"><?php echo $GLOBALS["prevView"]?></div>
<!-- gdline 350"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><div style="float:right;"><?php echo $GLOBALS["nextView"]?></div>
<!-- gdline 351"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><div style="clear:both"></div>
<!-- gdline 352"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --></div>
<!-- gdline 353"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->
<!-- gdline 354"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><!-- 상품 이미지 -->
<!-- gdline 355"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><div style="margin:0px auto 0px auto">
<!-- gdline 356"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><div style="width:49%;float:left;text-align:center;">
<!-- gdline 357"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><div style="padding-bottom:10"><span onclick="popup('goods_popup_large.php?goodsno=<?php echo $TPL_VAR["goodsno"]?>',800,600)" style="cursor:pointer"><!--디테일뷰수정--><?php if($TPL_VAR["detailView"]=='y'){?><?php if($TPL_VAR["sc_img"][ 0]){?><?php echo goodsimg($TPL_VAR["sc_img"][ 0], 300,'id="objImg"','','zoom_view')?><?php }else{?><?php echo goodsimg($TPL_VAR["r_img"][ 0], 300,'id="objImg"','','zoom_view')?><?php }?><?php }else{?><?php echo goodsimg($TPL_VAR["r_img"][ 0], 300,'id=objImg')?><?php }?><!--디테일뷰수정--></span></div>
<!-- gdline 358"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><div style="padding-bottom:10">
<!-- gdline 359"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><img src="/shop/data/skin/freemart/img/common/btn_zoom.gif" onclick="popup('goods_popup_large.php?goodsno=<?php echo $TPL_VAR["goodsno"]?>',800,600)" style="cursor:pointer" align=absmiddle>
<!-- gdline 360"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --></div>
<!-- gdline 361"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><div align=center>
<!-- gdline 362"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><?php if($TPL_t_img_1){$TPL_I1=-1;foreach($TPL_VAR["t_img"] as $TPL_V1){$TPL_I1++;?>
<!-- gdline 363"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><?php if($TPL_VAR["detailView"]=='y'){?>
<!-- gdline 364"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><?php echo goodsimg($TPL_V1, 45,"onmouseover='chgImg(this)' ssrc='".$TPL_VAR["sc_img"][$TPL_I1]."' lsrc='".$TPL_VAR["r_img"][$TPL_I1]."' class=hand style='border-width:1; border-style:solid; border-color:#cccccc'")?>

<!-- gdline 365"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><?php }else{?>
<!-- gdline 366"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><?php echo goodsimg($TPL_V1, 45,"onmouseover='chgImg(this)' class=hand style='border-width:1; border-style:solid; border-color:#cccccc'")?>

<!-- gdline 367"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><?php }?>
<!-- gdline 368"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><?php }}?>
<!-- gdline 369"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --></div>
<!-- gdline 370"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --></div>
<!-- gdline 371"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->
<!-- gdline 372"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><!-- 상품 스펙 리스트 -->
<!-- gdline 373"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><div id=goods_spec style="width:49%;float:left;">
<!-- gdline 374"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><!--디테일뷰수정--><?php if($TPL_VAR["detailView"]=='y'){?><div id="zoom_view" style="display:none; position:absolute; width:340px; height:370px;"></div><?php }?><!--디테일뷰수정-->
<!-- gdline 375"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><form name=frmView method=post onsubmit="return false">
<!-- gdline 376"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><input type=hidden name=mode value="addItem">
<!-- gdline 377"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><input type=hidden name=goodsno value="<?php echo $TPL_VAR["goodsno"]?>">
<!-- gdline 378"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><input type=hidden name=goodsCoupon value="<?php echo $TPL_VAR["coupon"]?>">
<!-- gdline 379"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><?php if($TPL_VAR["min_ea"]> 1){?><input type="hidden" name="min_ea" value="<?php echo $TPL_VAR["min_ea"]?>"><?php }?>
<!-- gdline 380"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><?php if($TPL_VAR["max_ea"]!='0'){?><input type="hidden" name="max_ea" value="<?php echo $TPL_VAR["max_ea"]?>"><?php }?>
<!-- gdline 381"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><div style="padding:10px 0 10px 5px" align=left>
<!-- gdline 382"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><b style="font:bold 12pt 돋움;">
<!-- gdline 383"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><?php if($TPL_VAR["clevel"]=='0'||$TPL_VAR["slevel"]>=$TPL_VAR["clevel"]){?>
<!-- gdline 384"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><?php echo $TPL_VAR["goodsnm"]?>

<!-- gdline 385"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><?php }elseif($TPL_VAR["slevel"]<$TPL_VAR["clevel"]&&$TPL_VAR["auth_step"][ 1]=='Y'){?>
<!-- gdline 386"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><?php echo $TPL_VAR["goodsnm"]?>

<!-- gdline 387"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><?php }?>
<!-- gdline 388"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --></b>
<!-- gdline 389"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --></div>
<!-- gdline 390"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><div style="padding:0 0 10px 5px;font:11px dotum;letter-spacing:-1px;color:#666666"><?php echo $TPL_VAR["shortdesc"]?></div>
<!-- gdline 391"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><?php if($TPL_VAR["clevel"]=='0'||$TPL_VAR["slevel"]>=$TPL_VAR["clevel"]||($TPL_VAR["slevel"]<$TPL_VAR["clevel"]&&$TPL_VAR["auth_step"][ 2]=='Y')){?>
<!-- gdline 392"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><table border=0 cellpadding=0 cellspacing=0 class=top>
<!-- gdline 393"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<tr><td height=2></td></tr>
<!-- gdline 394"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php if($TPL_VAR["sales_status"]=='ing'){?>
<!-- gdline 395"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<!--tr><td colspan="2"><span style="padding-bottom:5px; padding-left:14px; color:#EF1C21">절찬리 판매중!!</span></td></tr-->
<!-- gdline 396"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php }elseif($TPL_VAR["sales_status"]=='range'){?>
<!-- gdline 397"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<tr><th>남은시간 :</th><td><span id="el-countdown-1" style="padding-bottom:5px;font:13pt bold;color:#EF1C21"></span></td></tr>
<!-- gdline 398"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<script type="text/javascript">
	Countdown.init('<?php echo date('Y-m-d H:i:s',$TPL_VAR["sales_range_end"])?>', 'el-countdown-1');
	</script>
<!-- gdline 401"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php }elseif($TPL_VAR["sales_status"]=='before'){?>
<!-- gdline 402"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<tr><td colspan="2"><span style="padding-bottom:5px; padding-left:14px; color:#EF1C21"><?php echo date('Y-m-d H:i:s',$TPL_VAR["sales_range_start"])?> 판매시작합니다.</span></td></tr>
<!-- gdline 403"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php }elseif($TPL_VAR["sales_status"]=='end'){?>
<!-- gdline 404"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<tr><td colspan="2"><span style="padding-bottom:5px; padding-left:14px; color:#EF1C21">판매가 종료되었습니다.</span></td></tr>
<!-- gdline 405"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php }?>
<!-- gdline 406"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->
<!-- gdline 407"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php if($TPL_VAR["runout"]&&$GLOBALS["cfg_soldout"]["price"]=='image'){?>
<!-- gdline 408"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<tr><th>판매가격 :</th><td><img src="../data/goods/icon/custom/soldout_price"></td></tr>
<!-- gdline 409"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php }elseif($TPL_VAR["runout"]&&$GLOBALS["cfg_soldout"]["price"]=='string'){?>
<!-- gdline 410"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<tr><th>판매가격 :</th><td><b><?php echo $GLOBALS["cfg_soldout"]["price_string"]?></b></td></tr>
<!-- gdline 411"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php }elseif(!$TPL_VAR["strprice"]){?>
<!-- gdline 412"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<tr>
<!-- gdline 413"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->		<th>판매가격 :</th>
<!-- gdline 414"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->		<td>
<!-- gdline 415"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->		<b><span id=price><?php echo number_format($TPL_VAR["price"])?></span>원</b>
<!-- gdline 416"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->		</td>
<!-- gdline 417"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	</tr>
<!-- gdline 418"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php if($TPL_VAR["special_discount_amount"]){?>
<!-- gdline 419"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<tr>
<!-- gdline 420"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->		<th>상품할인금액 :</th>
<!-- gdline 421"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->		<td style="font-weight:bold">-<?php echo number_format($TPL_VAR["special_discount_amount"])?>원</span></b></td>
<!-- gdline 422"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	</tr>
<!-- gdline 423"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php }?>
<!-- gdline 424"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php if($TPL_VAR["memberdc"]){?>
<!-- gdline 425"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<tr>
<!-- gdline 426"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->		<th>회원할인가 :</th>
<!-- gdline 427"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->		<td style="font-weight:bold"><span id=obj_realprice><?php echo number_format($TPL_VAR["realprice"])?>원&nbsp;(-<?php echo number_format($TPL_VAR["memberdc"])?>원)</span></b></td>
<!-- gdline 428"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	</tr>
<!-- gdline 429"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php }?>
<!-- gdline 430"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php if($TPL_VAR["coupon"]){?>
<!-- gdline 431"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<tr><th>쿠폰적용가 :</th>
<!-- gdline 432"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<td>
<!-- gdline 433"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<span id=obj_coupon style="font-weight:bold;color:#EF1C21"><?php echo number_format($TPL_VAR["couponprice"])?>원&nbsp;(-<?php echo number_format($TPL_VAR["coupon"])?>원)</span>
<!-- gdline 434"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<div><?php echo $TPL_VAR["about_coupon"]?></div>
<!-- gdline 435"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	</td></tr>
<!-- gdline 436"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php }?>
<!-- gdline 437"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php if($TPL_VAR["consumer"]){?>
<!-- gdline 438"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<tr>
<!-- gdline 439"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->		<th>소비자가격 :</th>
<!-- gdline 440"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->		<td>
<!-- gdline 441"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->		<span id=consumer><?php echo number_format($TPL_VAR["consumer"])?></span>원
<!-- gdline 442"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->		</td>
<!-- gdline 443"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	</tr>
<!-- gdline 444"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php }?>
<!-- gdline 445"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<tr><th>적립금 :</th><td><span id=reserve><?php echo number_format($TPL_VAR["reserve"])?></span>원</td></tr>
<!-- gdline 446"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php if($TPL_VAR["naverNcash"]=='Y'){?>
<!-- gdline 447"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<tr id="naver-mileage-accum" style="display: none;">
<!-- gdline 448"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->		<th>네이버&nbsp;&nbsp;<br/>마일리지 :</th>
<!-- gdline 449"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->		<td>
<!-- gdline 450"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->			<?php if($TPL_VAR["exception"]){?>
<!-- gdline 451"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->			<?php echo $TPL_VAR["exception"]?>

<!-- gdline 452"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->			<?php }else{?>
<!-- gdline 453"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->			<span id="naver-mileage-accum-rate" style="font-weight:bold;color:#1ec228;"></span> 적립
<!-- gdline 454"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->			<?php }?>
<!-- gdline 455"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->			<img src="<?php echo $GLOBALS["cfg"]["rootDir"]?>/proc/naver_mileage/images/n_mileage_info4.png" onclick="javascript:mileage_info();" style="cursor: pointer; vertical-align: middle;">
<!-- gdline 456"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->		</td>
<!-- gdline 457"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	</tr>
<!-- gdline 458"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php }?>
<!-- gdline 459"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php if($TPL_VAR["coupon_emoney"]){?>
<!-- gdline 460"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<tr><th>쿠폰적립금 :</th>
<!-- gdline 461"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<td>
<!-- gdline 462"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<span id=obj_coupon_emoney style="font-weight:bold;color:#EF1C21"></span> &nbsp;<span style="font:bold 9pt tahoma; color:#FF0000" ><?php echo number_format($TPL_VAR["coupon_emoney"])?>원</span>
<!-- gdline 463"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	</td></tr>
<!-- gdline 464"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php }?>
<!-- gdline 465"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php if($TPL_VAR["delivery_type"]== 1){?>
<!-- gdline 466"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<tr><th>배송비 :</th><td>무료배송</td></tr>
<!-- gdline 467"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php }elseif($TPL_VAR["delivery_type"]== 2){?>
<!-- gdline 468"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<tr><th>개별배송비 :</th><td><?php echo number_format($TPL_VAR["goods_delivery"])?>원</td></tr>
<!-- gdline 469"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php }elseif($TPL_VAR["delivery_type"]== 3){?>
<!-- gdline 470"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<tr><th>착불배송비 :</th><td><?php echo number_format($TPL_VAR["goods_delivery"])?>원</td></tr>
<!-- gdline 471"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php }elseif($TPL_VAR["delivery_type"]== 4){?>
<!-- gdline 472"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<tr><th>고정배송비 :</th><td><?php echo number_format($TPL_VAR["goods_delivery"])?>원</td></tr>
<!-- gdline 473"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php }elseif($TPL_VAR["delivery_type"]== 5){?>
<!-- gdline 474"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<tr><th>수량별배송비 :</th><td><?php echo number_format($TPL_VAR["goods_delivery"])?>원 (수량에 따라 배송비가 추가됩니다.)</td></tr>
<!-- gdline 475"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php }?>
<!-- gdline 476"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php }else{?>
<!-- gdline 477"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<tr><th>판매가격 :</th><td><b><?php echo $TPL_VAR["strprice"]?></b></td></tr>
<!-- gdline 478"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php }?>
<!-- gdline 479"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --></table>
<!-- gdline 480"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><?php }?>
<!-- gdline 481"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><table border=0 cellpadding=0 cellspacing=0>
<!-- gdline 482"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<tr><td height=5></td></tr>
<!-- gdline 483"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php if($TPL_VAR["goods_status"]){?><tr height><th>상품상태 :</th><td><?php echo $TPL_VAR["goods_status"]?></td></tr><?php }?>
<!-- gdline 484"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php if($TPL_VAR["manufacture_date"]){?><tr height><th>제조일자 :</th><td><?php echo $TPL_VAR["manufacture_date"]?></td></tr><?php }?>
<!-- gdline 485"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php if($TPL_VAR["effective_date_start"]){?><tr height><th>유효일자 :</th><td><?php echo $TPL_VAR["effective_date_start"]?> ~ <?php echo $TPL_VAR["effective_date_end"]?></td></tr><?php }?>
<!-- gdline 486"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php if($TPL_VAR["delivery_method"]){?><tr height><th>배송방법 :</th><td><?php echo $TPL_VAR["delivery_method"]?></td></tr><?php }?>
<!-- gdline 487"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php if($TPL_VAR["delivery_area"]){?><tr height><th>배송지역 :</th><td><?php echo $TPL_VAR["delivery_area"]?></td></tr><?php }?>
<!-- gdline 488"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php if($TPL_VAR["goodscd"]){?><tr height><th>제품코드 :</th><td><?php echo $TPL_VAR["goodscd"]?></td></tr><?php }?>
<!-- gdline 489"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php if($TPL_VAR["origin"]){?><tr><th>원산지 :</th><td><?php echo $TPL_VAR["origin"]?></td></tr><?php }?>
<!-- gdline 490"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php if($TPL_VAR["maker"]){?><tr><th>제조사 :</th><td><?php echo $TPL_VAR["maker"]?></td></tr><?php }?>
<!-- gdline 491"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php if($TPL_VAR["brand"]){?><tr><th>브랜드 :</th><td><?php echo $TPL_VAR["brand"]?> <a href="<?php echo url("goods/goods_brand.php?")?>&brand=<?php echo $TPL_VAR["brandno"]?>">[브랜드바로가기]</a></td></tr><?php }?>
<!-- gdline 492"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php if($TPL_VAR["launchdt"]){?><tr><th>출시일 :</th><td><?php echo $TPL_VAR["launchdt"]?></td></tr><?php }?>
<!-- gdline 493"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php if($TPL_ex_1){foreach($TPL_VAR["ex"] as $TPL_K1=>$TPL_V1){?><tr><th><?php echo $TPL_K1?> :</th><td><?php echo $TPL_V1?></td></tr><?php }}?>
<!-- gdline 494"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->
<!-- gdline 495"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php if(!$GLOBALS["opt"]){?>
<!-- gdline 496"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<tr><th>구매수량 :</th>
<!-- gdline 497"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<td>
<!-- gdline 498"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php if(!$TPL_VAR["runout"]){?>
<!-- gdline 499"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<div style="float:left;"><input type=text name=ea size=2 value=<?php if($TPL_VAR["min_ea"]){?><?php echo $TPL_VAR["min_ea"]?><?php }else{?>1<?php }?> class=line style="text-align:right;height:18px" step="<?php if($TPL_VAR["sales_unit"]){?><?php echo $TPL_VAR["sales_unit"]?><?php }else{?>1<?php }?>" min="<?php if($TPL_VAR["min_ea"]){?><?php echo $TPL_VAR["min_ea"]?><?php }else{?>1<?php }?>" max="<?php if($TPL_VAR["max_ea"]){?><?php echo $TPL_VAR["max_ea"]?><?php }else{?>0<?php }?>" onblur="chg_cart_ea(frmView.ea,'set');"></div>
<!-- gdline 500"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<div style="float:left;padding-left:3">
<!-- gdline 501"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<div style="padding:1 0 2 0"><img src="/shop/data/skin/freemart/img/common/btn_plus.gif" onClick="chg_cart_ea(frmView.ea,'up')" style="cursor:pointer"></div>
<!-- gdline 502"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<div><img src="/shop/data/skin/freemart/img/common/btn_minus.gif" onClick="chg_cart_ea(frmView.ea,'dn')" style="cursor:pointer"></div>
<!-- gdline 503"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	</div>
<!-- gdline 504"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<div style="padding-top:3; float:left">개</div>
<!-- gdline 505"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<div style="padding-left:10px;float:left" class="stxt">
<!-- gdline 506"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php if($TPL_VAR["min_ea"]> 1){?><div>최소구매수량 : <?php echo $TPL_VAR["min_ea"]?>개</div><?php }?>
<!-- gdline 507"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php if($TPL_VAR["max_ea"]!='0'){?><div>최대구매수량 : <?php echo $TPL_VAR["max_ea"]?>개</div><?php }?>
<!-- gdline 508"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php if($TPL_VAR["sales_unit"]> 1){?><div>묶음주문단위 : <?php echo $TPL_VAR["sales_unit"]?>개</div><?php }?>
<!-- gdline 509"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	</div>
<!-- gdline 510"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php }else{?>
<!-- gdline 511"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	품절된 상품입니다
<!-- gdline 512"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php }?>
<!-- gdline 513"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	</td></tr>
<!-- gdline 514"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php }else{?>
<!-- gdline 515"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<input type=hidden name=ea step="<?php if($TPL_VAR["sales_unit"]){?><?php echo $TPL_VAR["sales_unit"]?><?php }else{?>1<?php }?>" min="<?php if($TPL_VAR["min_ea"]){?><?php echo $TPL_VAR["min_ea"]?><?php }else{?>1<?php }?>" max="<?php if($TPL_VAR["max_ea"]){?><?php echo $TPL_VAR["max_ea"]?><?php }else{?>0<?php }?>" value=<?php if($TPL_VAR["min_ea"]){?><?php echo $TPL_VAR["min_ea"]?><?php }else{?>1<?php }?>>
<!-- gdline 516"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php }?>
<!-- gdline 517"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->
<!-- gdline 518"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php if($TPL_VAR["chk_point"]){?>
<!-- gdline 519"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<tr><th>고객선호도 :</th><td><?php if((is_array($TPL_R1=array_fill( 0,$TPL_VAR["chk_point"],''))&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?>★<?php }}?></td></tr>
<!-- gdline 520"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php }?>
<!-- gdline 521"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php if($TPL_VAR["icon"]){?><tr><th>제품상태 :</th><td><?php echo $TPL_VAR["icon"]?></td></tr><?php }?>
<!-- gdline 522"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --></table>
<!-- gdline 523"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->
<!-- gdline 524"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><?php if(!$TPL_VAR["strprice"]){?>
<!-- gdline 525"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->
<!-- gdline 526"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><!-- 추가 옵션 입력형 -->
<!-- gdline 527"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><?php if($GLOBALS["addopt_inputable"]){?>
<!-- gdline 528"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><table border=0 cellpadding=0 cellspacing=0 class=top>
<!-- gdline 529"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php if($TPL__addopt_inputable_1){$TPL_I1=-1;foreach($GLOBALS["addopt_inputable"] as $TPL_K1=>$TPL_V1){$TPL_I1++;?>
<!-- gdline 530"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<tr><th><?php echo $TPL_K1?> :</th>
<!-- gdline 531"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<td>
<!-- gdline 532"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->		<input type="hidden" name="_addopt_inputable[]" value="">
<!-- gdline 533"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->		<input type="text" name="addopt_inputable[]" label="<?php echo $TPL_K1?>" option-value="<?php echo $TPL_V1["sno"]?>^<?php echo $TPL_K1?>^<?php echo $TPL_V1["opt"]?>^<?php echo $TPL_V1["addprice"]?>" value="" <?php if($GLOBALS["addopt_inputable_req"][$TPL_I1]){?>required fld_esssential<?php }?> maxlength="<?php echo $TPL_V1["opt"]?>">
<!-- gdline 534"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	</td></tr>
<!-- gdline 535"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php }}?>
<!-- gdline 536"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --></table>
<!-- gdline 537"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><?php }?>
<!-- gdline 538"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->
<!-- gdline 539"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><!-- 필수 옵션 일체형 -->
<!-- gdline 540"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><?php if($GLOBALS["opt"]&&$GLOBALS["typeOption"]=="single"){?>
<!-- gdline 541"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><table border=0 cellpadding=0 cellspacing=0 class=top>
<!-- gdline 542"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<tr><td height=6></td></tr>
<!-- gdline 543"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<tr><th  valign="top"><?php echo $TPL_VAR["optnm"]?> :</th>
<!-- gdline 544"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<td>
<!-- gdline 545"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<div>
<!-- gdline 546"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<select name="opt[]" onchange="chkOption(this);chkOptimg();nsGodo_MultiOption.set();" required fld_esssential msgR="<?php echo $TPL_VAR["optnm"]?> 선택을 해주세요">
<!-- gdline 547"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<option value="">== 옵션선택 ==
<!-- gdline 548"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php if($TPL__opt_1){foreach($GLOBALS["opt"] as $TPL_V1){?><?php if((is_array($TPL_R2=$TPL_V1)&&!empty($TPL_R2)) || (is_object($TPL_R2) && in_array("Countable", class_implements($TPL_R2)) && $TPL_R2->count() > 0)) {foreach($TPL_R2 as $TPL_V2){?>
<!-- gdline 549"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<option value="<?php echo $TPL_V2["opt1"]?><?php if($TPL_V2["opt2"]){?>|<?php echo $TPL_V2["opt2"]?><?php }?>" <?php if($TPL_VAR["usestock"]&&!$TPL_V2["stock"]){?> disabled class=disabled<?php }?>><?php echo $TPL_V2["opt1"]?><?php if($TPL_V2["opt2"]){?>/<?php echo $TPL_V2["opt2"]?><?php }?> <?php if($TPL_V2["price"]!=$TPL_VAR["price"]){?>(<?php echo number_format($TPL_V2["price"])?>원)<?php }?>
<!-- gdline 550"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php if($TPL_VAR["usestock"]&&!$TPL_V2["stock"]){?> [품절]<?php }?>
<!-- gdline 551"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php }}?><?php }}?>
<!-- gdline 552"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	</select></div>
<!-- gdline 553"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	</td>
<!-- gdline 554"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	</tr>
<!-- gdline 555"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<tr><td height=6></td></tr>
<!-- gdline 556"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --></table>
<!-- gdline 557"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><?php }?>
<!-- gdline 558"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->
<!-- gdline 559"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><!-- 필수 옵션 분리형 -->
<!-- gdline 560"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><?php if($GLOBALS["opt"]&&$GLOBALS["typeOption"]=="double"){?>
<!-- gdline 561"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><table border=0 cellpadding=0 cellspacing=0 class=top>
<!-- gdline 562"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<tr><td height=6></td></tr>
<!-- gdline 563"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php if($TPL__optnm_1){$TPL_I1=-1;foreach($GLOBALS["optnm"] as $TPL_V1){$TPL_I1++;?>
<!-- gdline 564"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<tr><th valign="top" ><?php echo $TPL_V1?> :</th>
<!-- gdline 565"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<td >
<!-- gdline 566"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->
<!-- gdline 567"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<!-- 옵션 선택 -->
<!-- gdline 568"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<div>
<!-- gdline 569"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php if(!$TPL_I1){?>
<!-- gdline 570"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<div>
<!-- gdline 571"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<select name="opt[]" onchange="subOption(this);chkOptimg();selicon(this);nsGodo_MultiOption.set();" required fld_esssential msgR="<?php echo $TPL_V1?> 선택을 해주세요">
<!-- gdline 572"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<option value="">== 옵션선택 ==
<!-- gdline 573"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php if((is_array($TPL_R2=($GLOBALS["opt"]))&&!empty($TPL_R2)) || (is_object($TPL_R2) && in_array("Countable", class_implements($TPL_R2)) && $TPL_R2->count() > 0)) {foreach($TPL_R2 as $TPL_K2=>$TPL_V2){?><option value="<?php echo $TPL_K2?>"><?php echo $TPL_K2?><?php }}?>
<!-- gdline 574"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	</select>
<!-- gdline 575"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	</div>
<!-- gdline 576"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php }else{?>
<!-- gdline 577"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<select name="opt[]" onchange="chkOption(this);selicon(this);nsGodo_MultiOption.set();" required fld_esssential msgR="<?php echo $TPL_V1?> 선택을 해주세요"><option value="">==선택==</select>
<!-- gdline 578"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php }?>
<!-- gdline 579"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	</div>
<!-- gdline 580"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->
<!-- gdline 581"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<!-- 옵션 이미지 아이콘 -->
<!-- gdline 582"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php if($TPL_VAR["optkind"][$TPL_I1]=='img'){?>
<!-- gdline 583"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php if(!$TPL_I1){?>
<!-- gdline 584"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->		<?php if((is_array($TPL_R2=$GLOBALS["opticon"][$TPL_I1])&&!empty($TPL_R2)) || (is_object($TPL_R2) && in_array("Countable", class_implements($TPL_R2)) && $TPL_R2->count() > 0)) {$TPL_I2=-1;foreach($TPL_R2 as $TPL_K2=>$TPL_V2){$TPL_I2++;?>
<!-- gdline 585"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->		<div style='width:43px;float:left;padding:5 0 5 0'><a href="javascript:click_opt_fastion('<?php echo $TPL_I1?>','<?php echo $TPL_I2?>','<?php echo $GLOBALS["opt"][$TPL_K2][$TPL_I2]["opt1"]?>');" name="icon[]"><img width="40" id="opticon0_<?php echo $TPL_I2?>" id="opticon_<?php echo $TPL_I1?>_<?php echo $TPL_I2?>" style="border:1px #cccccc solid" src='../data/goods/<?php echo $TPL_V2?>'  onmouseover="onicon(this);chgOptimg('<?php echo $TPL_K2?>');" onmouseout="outicon(this)" onclick="clicon(this)"></a></div>
<!-- gdline 586"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->		<?php }}?>
<!-- gdline 587"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php }else{?>
<!-- gdline 588"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<div id="dtdopt2"></div>
<!-- gdline 589"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php }?>
<!-- gdline 590"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php }?>
<!-- gdline 591"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->
<!-- gdline 592"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<!-- 옵션 색상타입 아이콘 -->
<!-- gdline 593"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php if($TPL_VAR["optkind"][$TPL_I1]=='color'){?>
<!-- gdline 594"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php if(!$TPL_I1){?>
<!-- gdline 595"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->		<?php if((is_array($TPL_R2=$GLOBALS["opticon"][$TPL_I1])&&!empty($TPL_R2)) || (is_object($TPL_R2) && in_array("Countable", class_implements($TPL_R2)) && $TPL_R2->count() > 0)) {$TPL_I2=-1;foreach($TPL_R2 as $TPL_K2=>$TPL_V2){$TPL_I2++;?>
<!-- gdline 596"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->		<div style='width:18px;float:left;padding-top:5px ' ><a href="javascript:click_opt_fastion('<?php echo $TPL_I1?>','<?php echo $TPL_I2?>','<?php echo $TPL_K2?>');" style="cursor:hand;"  name="icon[]"><span  style="float:left;width:15;height:15;border:1px #cccccc solid;background-color:#<?php echo $TPL_V2?>" onmouseover="onicon(this);chgOptimg('<?php echo $TPL_K2?>');" onmouseout="outicon(this)" onclick="clicon(this)"></span></a></div>
<!-- gdline 597"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->		<?php }}?>
<!-- gdline 598"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php }else{?>
<!-- gdline 599"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<div id="dtdopt2"></div>
<!-- gdline 600"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php }?>
<!-- gdline 601"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php }?>
<!-- gdline 602"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->
<!-- gdline 603"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<input type="hidden" name="opt_txt[]" value="">
<!-- gdline 604"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	</td></tr>
<!-- gdline 605"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php }}?>
<!-- gdline 606"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<tr><td height=6></td></tr>
<!-- gdline 607"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --></table>
<!-- gdline 608"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><script>subOption(document.getElementsByName('opt[]')[0])</script>
<!-- gdline 609"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><?php }?>
<!-- gdline 610"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->
<!-- gdline 611"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><!-- 추가 옵션 -->
<!-- gdline 612"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><table border=0 cellpadding=0 cellspacing=0 class=sub>
<!-- gdline 613"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php if($TPL__addopt_1){$TPL_I1=-1;foreach($GLOBALS["addopt"] as $TPL_K1=>$TPL_V1){$TPL_I1++;?>
<!-- gdline 614"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<tr><th><?php echo $TPL_K1?> :</th>
<!-- gdline 615"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<td>
<!-- gdline 616"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php if($GLOBALS["addoptreq"][$TPL_I1]){?>
<!-- gdline 617"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<select name="addopt[]" required fld_esssential label="<?php echo $TPL_K1?>" onchange="nsGodo_MultiOption.set();">
<!-- gdline 618"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<option value="">==<?php echo $TPL_K1?> 선택==
<!-- gdline 619"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php }else{?>
<!-- gdline 620"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<select name="addopt[]" label="<?php echo $TPL_K1?>" onchange="nsGodo_MultiOption.set();">
<!-- gdline 621"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<option value="">==<?php echo $TPL_K1?> 선택==
<!-- gdline 622"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<option value="-1">선택안함
<!-- gdline 623"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php }?>
<!-- gdline 624"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php if((is_array($TPL_R2=$TPL_V1)&&!empty($TPL_R2)) || (is_object($TPL_R2) && in_array("Countable", class_implements($TPL_R2)) && $TPL_R2->count() > 0)) {foreach($TPL_R2 as $TPL_V2){?>
<!-- gdline 625"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<option value="<?php echo $TPL_V2["sno"]?>^<?php echo $TPL_K1?>^<?php echo $TPL_V2["opt"]?>^<?php echo $TPL_V2["addprice"]?>"><?php echo $TPL_V2["opt"]?>

<!-- gdline 626"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php if($TPL_V2["addprice"]){?>(<?php echo number_format($TPL_V2["addprice"])?>원 추가)<?php }?>
<!-- gdline 627"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php }}?>
<!-- gdline 628"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	</select>
<!-- gdline 629"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	</td></tr>
<!-- gdline 630"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php }}?>
<!-- gdline 631"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --></table>
<!-- gdline 632"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->
<!-- gdline 633"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->
<!-- gdline 634"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><!-- ? 옵션 있으면 -->
<!-- gdline 635"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><script>
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
				for (var i=0,m=addopt.length;i<m ;i++ ) {

					if (typeof addopt[i] == 'object') {
						_data.addopt.push(addopt[i].value);
					}

				}

				// 입력 옵션
				var addopt_inputable = document.getElementsByName('addopt_inputable[]');
				for (var i=0,m=addopt_inputable.length;i<m ;i++ ) {

					if (typeof addopt_inputable[i] == 'object') {
						var v = addopt_inputable[i].value.trim();
						if (v) {
							var tmp = addopt_inputable[i].getAttribute("option-value").split('^');
							tmp[2] = v;
							_data.addopt_inputable.push(tmp.join('^'));
						}

						// 필드값 초기화
						addopt_inputable[i].value = '';

					}

				}

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
				html += '<div style="padding:1 0 2 0"><img src="/shop/data/skin/freemart/img/common/btn_multioption_ea_up.gif" onClick="nsGodo_MultiOption.ea(\'up\',\''+key+'\');" style="cursor:pointer"></div>';
				html += '<div><img src="/shop/data/skin/freemart/img/common/btn_multioption_ea_down.gif" onClick="nsGodo_MultiOption.ea(\'down\',\''+key+'\');" style="cursor:pointer"></div>';
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
				html += '<a href="javascript:void(0);" onClick="nsGodo_MultiOption.del(\''+key+'\');return false;"><img src="/shop/data/skin/freemart/img/common/btn_multioption_del.gif"></a>';
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
<!-- gdline 1121"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->
<!-- gdline 1122"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><style type="text/css">
.goods-multi-option {display:none;}
.goods-multi-option table {border:1px solid #D3D3D3;}
.goods-multi-option table td {border-bottom:1px solid #D3D3D3;padding:10px;}
</style>
<!-- gdline 1127"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><div id="el-multi-option-display" class="goods-multi-option">
<!-- gdline 1128"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<table border="0" cellpadding="0" cellspacing="0">
<!-- gdline 1129"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<col width=""><col width="50"><col width="80">
<!-- gdline 1130"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	</table>
<!-- gdline 1131"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->
<!-- gdline 1132"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<div style="font-size:12px;text-align:right;padding:10px 20px 10px 0;border-bottom:1px solid #D3D3D3;margin-bottom:5px;">
<!-- gdline 1133"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->		<img src="/shop/data/skin/freemart/img/common/btn_multioption_br.gif" align="absmiddle"> 총 금액 : <span style="color:#E70103;font-weight:bold;" id="el-multi-option-total-price"></span>
<!-- gdline 1134"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	</div>
<!-- gdline 1135"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --></div>
<!-- gdline 1136"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><!-- / -->
<!-- gdline 1137"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->
<!-- gdline 1138"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><?php }?>
<!-- gdline 1139"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><?php echo $TPL_VAR["cyworldScrap"]?>

<!-- gdline 1140"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><?php echo $TPL_VAR["snsBtn"]?>

<!-- gdline 1141"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><?php if($TPL_VAR["setGoodsConfig"]=='Y'){?>
<!-- gdline 1142"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><a href="../setGoods/?cody=<?php echo $TPL_VAR["goodsno"]?>"><img src="/shop/data/skin/freemart/img/common/btn_codylink.gif"></a>
<!-- gdline 1143"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><?php }?>
<!-- gdline 1144"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><!-- 각종 버튼 -->
<!-- gdline 1145"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><div style="width:330px;">
<!-- gdline 1146"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><?php if($TPL_VAR["stocked_noti"]){?>
<!-- gdline 1147"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><div style="margin-bottom: 7px">품절된 옵션상품은 재입고 알림 신청을 통해서 입고 시 알림 서비스를 받으실 수 있습니다.</div>
<!-- gdline 1148"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><?php }?>
<!-- gdline 1149"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><?php if(!$TPL_VAR["strprice"]&&!$TPL_VAR["runout"]&&($TPL_VAR["sales_status"]=='ing'||$TPL_VAR["sales_status"]=='range')){?>
<!-- gdline 1150"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><?php if($TPL_VAR["clevel"]=='0'||$TPL_VAR["slevel"]>=$TPL_VAR["clevel"]){?>
<!-- gdline 1151"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><a href="javascript:act('../order/order')"><img src="/shop/data/skin/freemart/img/common/btn_direct_buy.gif"></a>
<!-- gdline 1152"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><a href="javascript:cartAdd(frmView,'<?php echo $TPL_VAR["cartCfg"]->redirectType?>')"><img src="/shop/data/skin/freemart/img/common/btn_cart.gif"></a>
<!-- gdline 1153"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><a href="javascript:act('../mypage/mypage_wishlist')"><img src="/shop/data/skin/freemart/img/common/btn_wish_m_un.gif"></a>
<!-- gdline 1154"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><?php }?>
<!-- gdline 1155"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><?php }?>
<!-- gdline 1156"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><?php if($TPL_VAR["stocked_noti"]){?>
<!-- gdline 1157"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><a href="javascript:fnRequestStockedNoti('<?php echo $TPL_VAR["goodsno"]?>');"><img src="/shop/data/skin/freemart/img/stocked_noti/btn_alarm_2.gif"></a>
<!-- gdline 1158"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><?php }?>
<!-- gdline 1159"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><a href="<?php echo url("goods/goods_list.php?")?>&category=<?php echo $_GET["category"]?>"><img src="/shop/data/skin/freemart/img/common/btn_list.gif"></a>
<!-- gdline 1160"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --></div>
<!-- gdline 1161"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><div><?php echo $TPL_VAR["naverCheckout"]?></div>
<!-- gdline 1162"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><div><?php echo $TPL_VAR["auctionIpayBtn"]?></div>
<!-- gdline 1163"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><div><?php echo $TPL_VAR["qrcode_view"]?></div>
<!-- gdline 1164"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><?php echo $TPL_VAR["plusCheeseBtn"]?>

<!-- gdline 1165"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --></form>
<!-- gdline 1166"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --></div>
<!-- gdline 1167"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --></div><p>
<!-- gdline 1168"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->
<!-- gdline 1169"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><table style="clear:both;" border=0 cellpadding=0 cellspacing=0>
<!-- gdline 1170"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><tr>
<!-- gdline 1171"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<td>
<!-- gdline 1172"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->
<!-- gdline 1173"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->		<table border=0 cellpadding=0 cellspacing=0 style="width:100%;margin-top:5px;">
<!-- gdline 1174"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<tr>
<!-- gdline 1175"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->		<TD class="spec_line_title" >Detail View</TD>
<!-- gdline 1176"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->		
<!-- gdline 1177"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	</tr>
<!-- gdline 1178"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	</table>
<!-- gdline 1179"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->
<!-- gdline 1180"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php if($TPL_VAR["coupon"]||$TPL_VAR["coupon_emoney"]){?>
<!-- gdline 1181"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<!-- 할인쿠폰 다운받기 -->
<!-- gdline 1182"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<div style="padding:10px 0">
<!-- gdline 1183"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<table>
<!-- gdline 1184"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<tr>
<!-- gdline 1185"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->		<td><img src="/shop/data/skin/freemart/img/common/coupon_txt.gif"></td>
<!-- gdline 1186"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->		<td>
<!-- gdline 1187"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->		<table border=0 cellpadding=0 cellspacing=0>
<!-- gdline 1188"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->		<tr>
<!-- gdline 1189"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->		<?php if($TPL_a_coupon_1){$TPL_I1=-1;foreach($TPL_VAR["a_coupon"] as $TPL_V1){$TPL_I1++;?>
<!-- gdline 1190"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->		<?php if($TPL_I1% 3== 0){?>
<!-- gdline 1191"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->		</tr><tr>
<!-- gdline 1192"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->		<?php }?>
<!-- gdline 1193"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->
<!-- gdline 1194"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->		<td onclick="ifrmHidden.location.href='<?php echo url("proc/dn_coupon_goods.php?")?>&goodsno=<?php echo $TPL_VAR["goodsno"]?>&couponcd=<?php echo $TPL_V1["couponcd"]?>'" class=hand>
<!-- gdline 1195"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->
<!-- gdline 1196"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->		<?php if($TPL_V1["coupon_img"]== 4){?>
<!-- gdline 1197"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->		<div style="font:bold 12px tahoma;color:#FF0000;text-align:center;padding:19px 40px 0 0;width:140px;height:54px;background:url('<?php echo $TPL_VAR["coupon_img_path"]?><?php echo ($TPL_V1["coupon_img_file"])?>') no-repeat;" onmouseover="cp_explain('<?php echo $TPL_I1?>');" onmouseout="cp_explain_close('<?php echo $TPL_I1?>');"><?php echo $GLOBALS["r_couponAbility"][$TPL_V1["ability"]]?><?php if(substr($TPL_V1["price"], - 1)!="%"){?><?php echo number_format($TPL_V1["price"])?>원<?php }else{?><?php echo $TPL_V1["price"]?><?php }?></div>
<!-- gdline 1198"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->		<?php }else{?>
<!-- gdline 1199"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->		<div style="font:bold 12px tahoma;color:#FF0000;text-align:center;padding:19px 40px 0 0;width:140px;height:54px;background:url('/shop/data/skin/freemart/img/common/coupon0<?php echo ($TPL_V1["coupon_img"]+ 1)?>.gif');" onmouseover="cp_explain('<?php echo $TPL_I1?>');" onmouseout="cp_explain_close('<?php echo $TPL_I1?>');"><?php echo $GLOBALS["r_couponAbility"][$TPL_V1["ability"]]?><?php if(substr($TPL_V1["price"], - 1)!="%"){?><?php echo number_format($TPL_V1["price"])?>원<?php }else{?><?php echo $TPL_V1["price"]?><?php }?></div>
<!-- gdline 1200"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->		<?php }?>
<!-- gdline 1201"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->		<div style="padding:1 1 1 1;display:none;position:absolute;z-index:10;cursor:hand;background-color=FF0000;font:bold 12px tahoma;color:#FF0000;text-align:center;width:300px;height:100px;" id="cp_explain<?php echo $TPL_I1?>" onclick="cp_explain_close('<?php echo $TPL_I1?>');">
<!-- gdline 1202"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->			<table style="padding:0 0 0 0;background-color=FFFFFF;width:300px;height:100px"><tr><td>
<!-- gdline 1203"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->			<div style="color:#FF0000;text-align:center;width:300px;font:bold 12px tahoma;"><?php echo $TPL_V1["coupon_name"]?></div>
<!-- gdline 1204"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->			<div style="padding:5 0 0 0;color:#FF0000;text-align:center;width:300px;font:bold 12px tahoma;"><?php echo $TPL_V1["coupon_detail"]?></div>
<!-- gdline 1205"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->			<?php if($TPL_V1["coupon_priodtype"]== 0){?>
<!-- gdline 1206"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->			<div style="padding:10 0 0 0;text-align:center;"><?php echo $TPL_V1["coupon_sdate"]?> 부터</div>
<!-- gdline 1207"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->			<div style="text-align:center;"><?php echo $TPL_V1["coupon_edate"]?> 까지</div>
<!-- gdline 1208"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->			<?php }else{?>
<!-- gdline 1209"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->			<div style="padding:10 0 0 0;text-align:center;">발급 후 <?php echo $TPL_V1["coupon_sdate"]?> 일 간 사용 가능</div>
<!-- gdline 1210"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->			<?php }?>
<!-- gdline 1211"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->			<?php if($TPL_V1["payMethodStr"]){?>
<!-- gdline 1212"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->			<div style="padding:0 0 0 0;text-align:center;width:300px;"><?php echo $TPL_V1["payMethodStr"]?></div>
<!-- gdline 1213"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->			<?php }?>
<!-- gdline 1214"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->			</td></tr></table>
<!-- gdline 1215"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->		</div>
<!-- gdline 1216"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->		</td>
<!-- gdline 1217"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->
<!-- gdline 1218"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->		<?php }}?>
<!-- gdline 1219"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->		</tr>
<!-- gdline 1220"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->		</table>
<!-- gdline 1221"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->		</td>
<!-- gdline 1222"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	</tr>
<!-- gdline 1223"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	</table>
<!-- gdline 1224"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	</div>
<!-- gdline 1225"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php }?>
<!-- gdline 1226"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->
<!-- gdline 1227"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php if($TPL_VAR["use_external_video"]){?>
<!-- gdline 1228"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<div style="padding:10px 0" id="external-video">
<!-- gdline 1229"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->		<?php echo youtubePlayer($TPL_VAR["external_video_url"],$TPL_VAR["external_video_size_type"],$TPL_VAR["external_video_width"],$TPL_VAR["external_video_height"])?>

<!-- gdline 1230"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	</div>
<!-- gdline 1231"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php }?>
<!-- gdline 1232"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->
<!-- gdline 1233"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php if($TPL_extra_info_1){?>
<!-- gdline 1234"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->		<style>
		table.extra-information {background:#e0e0e0;margin:30px 0 60px 0;}
		table.extra-information th,
		table.extra-information td {font-weight:normal;text-align:left;padding-left:15px;background:#ffffff;font-family:Dotum;font-size:11px;height:28px;}

		table.extra-information th {width:15%;background:#f5f5f5;color:#515151;}
		table.extra-information td {width:35%;color:#666666;}

		</style>
<!-- gdline 1243"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->		<table width=100% border=0 cellpadding=0 cellspacing=1 class="extra-information">
<!-- gdline 1244"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->		<tr>
<!-- gdline 1245"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->		<?php if($TPL_extra_info_1){foreach($TPL_VAR["extra_info"] as $TPL_K1=>$TPL_V1){?>
<!-- gdline 1246"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->			<th><?php echo $TPL_V1["title"]?></th>
<!-- gdline 1247"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->			<td <?php if($TPL_V1["colspan"]> 1){?>colspan="<?php echo $TPL_V1["colspan"]?>"<?php }?>><?php echo $TPL_V1["desc"]?></td>
<!-- gdline 1248"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->			<?php if($TPL_V1["nkey"]&&(!$GLOBALS["extra_info"][$TPL_V1["nkey"]]||$TPL_K1% 2== 0)){?>
<!-- gdline 1249"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->			</tr><tr>
<!-- gdline 1250"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->			<?php }?>
<!-- gdline 1251"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->		<?php }}?>
<!-- gdline 1252"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->		</tr>
<!-- gdline 1253"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->		</table>
<!-- gdline 1254"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php }?>
<!-- gdline 1255"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->
<!-- gdline 1256"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<!-- 상세 설명 -->
<!-- gdline 1257"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<div id=contents style="width:100%;padding:10 10 10 10;overflow:hidden;">
<!-- gdline 1258"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	
<!-- gdline 1259"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php echo $TPL_VAR["longdesc"]?>

<!-- gdline 1260"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	</div>
<!-- gdline 1261"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->
<!-- gdline 1262"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<!-- 관련상품 -->
<!-- gdline 1263"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<table border=0 cellpadding=0 cellspacing=0 style="width:100%">
<!-- gdline 1264"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<tr>
<!-- gdline 1265"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->		<TD class="spec_line_title">Related Goods</TD>
<!-- gdline 1266"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	</tr>
<!-- gdline 1267"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	</table>
<!-- gdline 1268"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<div style="padding:10 10 10 10;overflow:hidden">
<!-- gdline 1269"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<table width=100% border=0 cellpadding=0 cellspacing=0>
<!-- gdline 1270"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<tr><td height=10></td></tr>
<!-- gdline 1271"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<tr>
<!-- gdline 1272"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->		<?php if((is_array($TPL_R1=dataGoodsRelation($TPL_VAR["goodsno"],$GLOBALS["cfg_related"]["max"]))&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {$TPL_I1=-1;foreach($TPL_R1 as $TPL_V1){$TPL_I1++;?>
<!-- gdline 1273"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->		<?php if($TPL_I1&&$TPL_I1%$GLOBALS["cfg_related"]["horizontal"]== 0){?></tr><tr><td height=10></td></tr><tr><?php }?>
<!-- gdline 1274"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->		<td align=center valign=top width="<?php echo  100/$GLOBALS["cfg_related"]["horizontal"]?>%">
<!-- gdline 1275"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->		<?php if($GLOBALS["cfg_related"]["dp_image"]){?><div><a href="<?php echo url("goods/goods_view.php?")?>&goodsno=<?php echo $TPL_V1["goodsno"]?>" <?php if($GLOBALS["cfg_related"]["link_type"]=='blank'){?> target="_blank"<?php }?> <?php if($GLOBALS["cfg_related"]["dp_shortdesc"]){?>onmouseover="fnGodoTooltipShow_(this)" onmousemove="fnGodoTooltipShow_(this)" onmouseout="fnGodoTooltipHide_(this)" tooltip="<?php echo $TPL_V1["shortdesc"]?>"<?php }?> ><?php echo goodsimg($TPL_V1["img_s"],$GLOBALS["cfg_related"]["size"])?></a></div><?php }?>
<!-- gdline 1276"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->		<?php if($GLOBALS["cfg_related"]["use_cart"]){?><div><a href="javascript:void(0);" onClick="fnPreviewGoods_(<?php echo $TPL_V1["goodsno"]?>);"><img src="<?php echo $GLOBALS["cfg_related"]["cart_icon"]?>"></a></div><?php }?>
<!-- gdline 1277"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->		<?php if($GLOBALS["cfg_related"]["dp_goodsnm"]){?><div style="padding:5"><a href="<?php echo url("goods/goods_view.php?")?>&goodsno=<?php echo $TPL_V1["goodsno"]?>" <?php if($GLOBALS["cfg_related"]["dp_shortdesc"]){?>onmouseover="fnGodoTooltipShow_(this)" onmousemove="fnGodoTooltipShow_(this)" onmouseout="fnGodoTooltipHide_(this)" tooltip="<?php echo $TPL_V1["shortdesc"]?>"<?php }?>><?php echo $TPL_V1["goodsnm"]?></a></div><?php }?>
<!-- gdline 1278"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->		<?php if($GLOBALS["cfg_related"]["dp_price"]){?><div><?php if($TPL_V1["strprice"]){?><?php echo $TPL_V1["strprice"]?><?php }else{?><b><?php echo number_format($TPL_V1["price"])?>원<?php }?></b></div><?php }?>
<!-- gdline 1279"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->		<?php if($TPL_V1["icon"]){?><div style="padding:5"><?php echo $TPL_V1["icon"]?></div><?php }?>
<!-- gdline 1280"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->
<!-- gdline 1281"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->		</td>
<!-- gdline 1282"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->		<?php }}?>
<!-- gdline 1283"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	</tr>
<!-- gdline 1284"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	</table>
<!-- gdline 1285"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	</div>
<!-- gdline 1286"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->
<!-- gdline 1287"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<div id="el-godo-tooltip-related" style="z-index:1000;display:none;position:absolute;top:0;left:0;width:<?php echo $GLOBALS["cfg_related"]["size"]?>px;padding:10px; -moz-opacity:.70; filter:alpha(opacity=70); opacity:.70;line-height:140%;" class="godo-tooltip-related">
<!-- gdline 1288"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	</div>
<!-- gdline 1289"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->
<!-- gdline 1290"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<!-- 상품 공통 정보 시작 -->
<!-- gdline 1291"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->
<!-- gdline 1292"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php if((is_array($TPL_R1=commoninfo())&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?>
<!-- gdline 1293"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<table border=0 cellpadding=0 cellspacing=0>
<!-- gdline 1294"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<tr>
<!-- gdline 1295"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->		<TD style="background: URL(/shop/data/skin/freemart/img/common/bar_detail_l.gif) no-repeat;" nowrap width="10" height="24"></TD>
<!-- gdline 1296"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->		<TD style="background: URL(/shop/data/skin/freemart/img/common/bar_detail_c.gif) repeat-x; font-weight:bold;" width='100%'><?php echo $TPL_V1["title"]?>2</TD>
<!-- gdline 1297"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->		<TD style="background: URL(/shop/data/skin/freemart/img/common/bar_detail_r.gif) no-repeat;" nowrap width="10" height="24">3</TD>
<!-- gdline 1298"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	</tr>
<!-- gdline 1299"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	</table>
<!-- gdline 1300"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<div style="width:100%;padding:10 10 10 10;overflow:hidden">
<!-- gdline 1301"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<table cellspacing=0 cellpadding=0>
<!-- gdline 1302"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<tr>
<!-- gdline 1303"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->		<td><?php echo $TPL_V1["info"]?></td>
<!-- gdline 1304"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	</tr>
<!-- gdline 1305"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	</table>
<!-- gdline 1306"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	</div>
<!-- gdline 1307"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php }}?>
<!-- gdline 1308"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<div style="margin-bottom:20px;"></div>
<!-- gdline 1309"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->
<!-- gdline 1310"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<!-- 상품 공통 정보 종료 -->
<!-- gdline 1311"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->
<!-- gdline 1312"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php if(strpos($this->template_dir,'/easy')&&is_file($this->template_dir.'/proc/_goods_guide.htm')){?>
<!-- gdline 1313"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php echo $this->define('tpl_include_file_1','proc/_goods_guide.htm')?> <?php $this->print_("tpl_include_file_1",$TPL_SCP,1);?>

<!-- gdline 1314"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php }else{?>
<!-- gdline 1315"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<!-- 배송안내 -->
<!-- gdline 1316"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	
<!-- gdline 1317"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<?php }?>
<!-- gdline 1318"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<div class="flyer" style="margin-left:120px;">
<!-- gdline 1319"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->		<?php if(include('proc/shopping_info.htm')){?>
<!-- gdline 1320"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->		<span>11111</span>
<!-- gdline 1321"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->			<?php }?>
<!-- gdline 1322"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->		<img class="flyer" src="http://francosmith.godohosting.com/shop/customer_notice_01.jpg"/>	
<!-- gdline 1323"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->		<img class="flyer" src="http://francosmith.godohosting.com/shop/customer_notice_02.jpg"/>
<!-- gdline 1324"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->			
<!-- gdline 1325"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	</div>
<!-- gdline 1326"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<!-- 상품 사용기 -->
<!-- gdline 1327"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<iframe id="inreview" src="./goods_review_list.php?goodsno=<?php echo $TPL_VAR["goodsno"]?>" frameborder="0" marginwidth="0" marginheight="0" width="100%" height="" scrolling="no"></iframe>
<!-- gdline 1328"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->
<!-- gdline 1329"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<!-- 상품 질문과답변 -->
<!-- gdline 1330"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<iframe id="inqna" src="./goods_qna_list.php?goodsno=<?php echo $TPL_VAR["goodsno"]?>" frameborder="0" marginwidth="0" marginheight="0" width="100%" height="" scrolling="no"></iframe>
<!-- gdline 1331"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->
<!-- gdline 1332"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->
<!-- gdline 1333"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	</td>
<!-- gdline 1334"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --></tr>
<!-- gdline 1335"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->
<!-- gdline 1336"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --></table>
<!-- gdline 1337"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->
<!-- gdline 1338"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --></div><!-- End indiv -->
<!-- gdline 1339"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->
<!-- gdline 1340"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><div style="display:none;position:absolute;z-index:10;cursor:hand;" id="qrExplain" onclick="qrExplain_close();">
<!-- gdline 1341"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><table cellpadding="0" cellspacing="0" border="0">
<!-- gdline 1342"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><tr>
<!-- gdline 1343"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<td width="4" height="271" valign="top" background="/shop/data/skin/freemart/img/common/page02_detail_blt.gif" style="background-repeat:no-repeat"></td>
<!-- gdline 1344"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->	<td  width="285" height="271" valign="top" background="/shop/data/skin/freemart/img/common/page02_detail.gif"></td>
<!-- gdline 1345"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --></tr>
<!-- gdline 1346"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --></table>
<!-- gdline 1347"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><div style='width:289' onclick="qrExplain_close();" style="cursor:hand;text-align:center">[닫기]</div>
<!-- gdline 1348"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --></div>
<!-- gdline 1349"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" -->
<!-- gdline 1350"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><?php $this->print_("footer",$TPL_SCP,1);?>

<!-- gdline 1351"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><!--디테일뷰수정-->
<!-- gdline 1352"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><?php if($TPL_VAR["detailView"]=='y'){?>
<!-- gdline 1353"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><script type="text/javascript">
var objImg = document.getElementById("objImg");
objImg.setAttribute("lsrc", objImg.getAttribute("src").replace("/t/", "/").replace("_sc.", '.'));
ImageScope.setImage(objImg, beforeScope, afterScope);
</script>
<!-- gdline 1358"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><?php }?>
<!-- gdline 1359"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><!--디테일뷰수정-->
<!-- gdline 1360"/goods/goods_view.htm|/../..//_skin_preview/skin/freemart/goods/goods_view.htm|goods/goods_view.htm tpl_0" --><!-- gdpart mode="close" fid="goods/goods_view.htm tpl_0" -->