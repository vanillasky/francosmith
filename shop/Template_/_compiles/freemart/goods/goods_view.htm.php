<?php /* Template_ 2.2.7 2016/07/27 11:37:37 /www/francotr3287_godo_co_kr/shop/data/skin/freemart/goods/goods_view.htm 000050942 */  $this->include_("dataGoodsRelation","commoninfo");
if (is_array($GLOBALS["opt"])) $TPL__opt_1=count($GLOBALS["opt"]); else if (is_object($GLOBALS["opt"]) && in_array("Countable", class_implements($GLOBALS["opt"]))) $TPL__opt_1=$GLOBALS["opt"]->count();else $TPL__opt_1=0;
if (is_array($GLOBALS["opt1img"])) $TPL__opt1img_1=count($GLOBALS["opt1img"]); else if (is_object($GLOBALS["opt1img"]) && in_array("Countable", class_implements($GLOBALS["opt1img"]))) $TPL__opt1img_1=$GLOBALS["opt1img"]->count();else $TPL__opt1img_1=0;
if (is_array($TPL_VAR["t_img"])) $TPL_t_img_1=count($TPL_VAR["t_img"]); else if (is_object($TPL_VAR["t_img"]) && in_array("Countable", class_implements($TPL_VAR["t_img"]))) $TPL_t_img_1=$TPL_VAR["t_img"]->count();else $TPL_t_img_1=0;
if (is_array($TPL_VAR["a_coupon"])) $TPL_a_coupon_1=count($TPL_VAR["a_coupon"]); else if (is_object($TPL_VAR["a_coupon"]) && in_array("Countable", class_implements($TPL_VAR["a_coupon"]))) $TPL_a_coupon_1=$TPL_VAR["a_coupon"]->count();else $TPL_a_coupon_1=0;
if (is_array($TPL_VAR["ex"])) $TPL_ex_1=count($TPL_VAR["ex"]); else if (is_object($TPL_VAR["ex"]) && in_array("Countable", class_implements($TPL_VAR["ex"]))) $TPL_ex_1=$TPL_VAR["ex"]->count();else $TPL_ex_1=0;
if (is_array($GLOBALS["addopt_inputable"])) $TPL__addopt_inputable_1=count($GLOBALS["addopt_inputable"]); else if (is_object($GLOBALS["addopt_inputable"]) && in_array("Countable", class_implements($GLOBALS["addopt_inputable"]))) $TPL__addopt_inputable_1=$GLOBALS["addopt_inputable"]->count();else $TPL__addopt_inputable_1=0;
if (is_array($GLOBALS["optnm"])) $TPL__optnm_1=count($GLOBALS["optnm"]); else if (is_object($GLOBALS["optnm"]) && in_array("Countable", class_implements($GLOBALS["optnm"]))) $TPL__optnm_1=$GLOBALS["optnm"]->count();else $TPL__optnm_1=0;
if (is_array($GLOBALS["addopt"])) $TPL__addopt_1=count($GLOBALS["addopt"]); else if (is_object($GLOBALS["addopt"]) && in_array("Countable", class_implements($GLOBALS["addopt"]))) $TPL__addopt_1=$GLOBALS["addopt"]->count();else $TPL__addopt_1=0;
if (is_array($TPL_VAR["extra_info"])) $TPL_extra_info_1=count($TPL_VAR["extra_info"]); else if (is_object($TPL_VAR["extra_info"]) && in_array("Countable", class_implements($TPL_VAR["extra_info"]))) $TPL_extra_info_1=$TPL_VAR["extra_info"]->count();else $TPL_extra_info_1=0;
if (is_array($GLOBALS["todayGoodsList"])) $TPL__todayGoodsList_1=count($GLOBALS["todayGoodsList"]); else if (is_object($GLOBALS["todayGoodsList"]) && in_array("Countable", class_implements($GLOBALS["todayGoodsList"]))) $TPL__todayGoodsList_1=$GLOBALS["todayGoodsList"]->count();else $TPL__todayGoodsList_1=0;?>
<?php $this->print_("header",$TPL_SCP,1);?>


<script src="/shop/lib/js/countdown.js"></script>
<script src="/shop/data/skin/freemart/js/option-handler.js" type="text/javascript"></script>
<script src="/shop/data/skin/freemart/js/view_goods.js" type="text/javascript"></script>
	
<style>

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
	if (obj.getAttribute("lsrc")) { 
		if (obj.getAttribute("lsrc").match( (/^http(s)?:/)) != null) { 
			objImg.setAttribute("lsrc", obj.getAttribute("lsrc")); 
		} 
		else { 
			objImg.setAttribute("lsrc", obj.src.replace(/\/t\/[^$]*$/g, '/')+obj.getAttribute("lsrc")); 
		} 
	}
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
<script type="text/javascript">

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

var nsGodo_MultiOption = new GMultiOption( <?php if($TPL_VAR["runout"]){?>true<?php }else{?>false<?php }?> );
</script>

<div class="page-wrapper">
	<div class="detail_view_curr_pos">HOME > <?php echo currPosition($TPL_VAR["category"])?></div>
	<div class="indiv"><!-- Start indiv -->
		
		<div id="goods-view-body">
			<div id="goods-view-img">
				<div class="goods-view-thumb" >
					<span onclick="popup('goods_popup_large.php?goodsno=<?php echo $TPL_VAR["goodsno"]?>',800,600)" style="cursor:pointer;">
					<!--디테일뷰수정--><?php if($TPL_VAR["detailView"]=='y'){?><?php if($TPL_VAR["sc_img"][ 0]){?><?php echo goodsimg($TPL_VAR["sc_img"][ 0], 300,'id="objImg"','','zoom_view')?><?php }else{?><?php echo goodsimg($TPL_VAR["r_img"][ 0], 300,'id="objImg"','','zoom_view')?><?php }?><?php }else{?><?php echo goodsimg($TPL_VAR["r_img"][ 0], 300,'id=objImg')?><?php }?><!--디테일뷰수정--></span>
				</div>
				
				<!-- 
				<div class="btn">
					<img src="/shop/data/skin/freemart/img/icon/zoom-32.png" onclick="popup('goods_popup_large.php?goodsno=<?php echo $TPL_VAR["goodsno"]?>',800,600)">
				</div>
				 -->
				 
				<div class="goods-view-more">
<?php if($TPL_t_img_1){$TPL_I1=-1;foreach($TPL_VAR["t_img"] as $TPL_V1){$TPL_I1++;?>
<?php if($TPL_VAR["detailView"]=='y'){?>
					<?php echo goodsimg($TPL_V1, 45,"onmouseover='chgImg(this)' ssrc='".$TPL_VAR["sc_img"][$TPL_I1]."' lsrc='".$TPL_VAR["r_img"][$TPL_I1]."' style='cursor:pointer; border:1px solid #cccccc'")?>

<?php }else{?>
					<?php echo goodsimg($TPL_V1, 45,"onmouseover='chgImg(this)' class=hand style='border:1px solid #cccccc'")?>

<?php }?>
<?php }}?>
				</div>
			</div>
			
			<div class="goods-view-spec-header">
				<div class="goods-view-name" >
					<div class="goods-view-title">
<?php if($TPL_VAR["clevel"]=='0'||$TPL_VAR["slevel"]>=$TPL_VAR["clevel"]){?>
						<?php echo $TPL_VAR["goodsnm"]?>

<?php }elseif($TPL_VAR["slevel"]<$TPL_VAR["clevel"]&&$TPL_VAR["auth_step"][ 1]=='Y'){?>
						<?php echo $TPL_VAR["goodsnm"]?>

<?php }?>
						<div class="goods-view-shortdesc"><?php echo $TPL_VAR["shortdesc"]?></div>
<?php if($TPL_VAR["goodscd"]){?><div class="goods-view-shortdesc">Code: <?php echo $TPL_VAR["goodscd"]?></div><?php }?>
						<div class="brand-star">
							<div id="brand-link"><?php if($TPL_VAR["brand"]){?><span><?php echo $TPL_VAR["brand"]?><a href="<?php echo url("goods/goods_brand.php?")?>&brand=<?php echo $TPL_VAR["brandno"]?>">&nbsp;[브랜드바로가기]</a></span><?php }?></div>
							<div id="goods-att"><?php if($TPL_VAR["chk_point"]){?><span class="star"><?php if((is_array($TPL_R1=array_fill( 0,$TPL_VAR["chk_point"],''))&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?>★<?php }}?></span><?php }?></div>
						</div>
					</div>
					
					<div class="goods-view-qrcode">
						<iframe src='/shop/lib/qrcodeImgMaker.php?s=2&d=http://francosmith.com/shop/goods/goods_view.php?goodsno=<?php echo $TPL_VAR["goodsno"]?>&o=http://francosmith.com/shop/goods/goods_view.php?goodsno=<?php echo $TPL_VAR["goodsno"]?>' marginheight='0' marginwidth='0' frameBorder='0' scrolling='no' allowTransparency='true' width="130px" height="130px"  ></iframe>
					</div>
				</div>
			</div>
			
			<!-- 상품 스펙 리스트 -->
			<div id="goods_spec">
			<!--디테일뷰수정-->
<?php if($TPL_VAR["detailView"]=='y'){?>
				<div id="zoom_view" style="display:none; position:absolute; width:340px; height:370px;"></div>
<?php }?>
				
				<!--디테일뷰수정-->
				<form name=frmView method=post onsubmit="return false">
					<input type=hidden name=mode value="addItem">
					<input type=hidden name=goodsno value="<?php echo $TPL_VAR["goodsno"]?>">
					<input type=hidden name=goodsCoupon value="<?php echo $TPL_VAR["coupon"]?>">
<?php if($TPL_VAR["min_ea"]> 1){?><input type="hidden" name="min_ea" value="<?php echo $TPL_VAR["min_ea"]?>"><?php }?>
<?php if($TPL_VAR["max_ea"]!='0'){?><input type="hidden" name="max_ea" value="<?php echo $TPL_VAR["max_ea"]?>"><?php }?>
				
<?php if($TPL_VAR["clevel"]=='0'||$TPL_VAR["slevel"]>=$TPL_VAR["clevel"]||($TPL_VAR["slevel"]<$TPL_VAR["clevel"]&&$TPL_VAR["auth_step"][ 2]=='Y')){?>
				<table id="goods-view-price-box" class="top1">
					<tr><td height=2></td></tr>
<?php if($TPL_VAR["sales_status"]=='ing'){?>
					<!--<tr><td><span style="padding-bottom:5px; padding-left:14px; color:#EF1C21">절찬리 판매중!!</span></td></tr>-->
<?php }elseif($TPL_VAR["sales_status"]=='range'){?>
					<tr>
						<!--  <th>남은시간 :</th>-->
						<td><span class="price-title">남은시간:</span><span id="el-countdown-1" class="timer"></span></td>
					</tr>
					<script type="text/javascript">
						Countdown.init('<?php echo date('Y-m-d H:i:s',$TPL_VAR["sales_range_end"])?>', 'el-countdown-1');
					</script>
<?php }elseif($TPL_VAR["sales_status"]=='before'){?>
					<tr><td><span style="padding-bottom:5px; padding-left:14px; color:#EF1C21"><?php echo date('Y-m-d H:i:s',$TPL_VAR["sales_range_start"])?> 판매시작합니다.</span></td></tr>
<?php }elseif($TPL_VAR["sales_status"]=='end'){?>
					<tr><td><span style="padding-bottom:5px; padding-left:14px; color:#EF1C21">판매가 종료되었습니다.</span></td></tr>
<?php }?>
		
<?php if($TPL_VAR["runout"]&&$GLOBALS["cfg_soldout"]["price"]=='image'){?>
					<tr>
						<!-- <th>판매가격 :</th>-->
						<td><img src="../data/goods/icon/custom/soldout_price"></td>
					</tr>
<?php }elseif($TPL_VAR["runout"]&&$GLOBALS["cfg_soldout"]["price"]=='string'){?>
					<tr>
						<!-- <th>판매가격 :</th>-->
						<td><b><?php echo $GLOBALS["cfg_soldout"]["price_string"]?></b></td>
					</tr>
					
<?php }elseif(!$TPL_VAR["strprice"]){?>
<?php if($TPL_VAR["special_discount_amount"]){?>
						<tr>
							<!-- <th>판매금액 :</th> -->
							<td>
								<span class="price-normal price-strike" id="nprice"><?php echo number_format($TPL_VAR["price"])?></span><span class="price-won">원</span><span class="slash">/</span>
								<span class="price-sale" id="price-amount"><?php echo number_format($TPL_VAR["price"]-$TPL_VAR["special_discount_amount"])?></span><span class="price-won">원</span><span class="slash">/</span>
								<span class="price-sale-ratio">SALE <?php echo round($TPL_VAR["special_discount_amount"]/$TPL_VAR["price"]* 100)?>%↓</span>
							</td>
						</tr>
<?php }elseif(!$TPL_VAR["special_discount_amount"]&&$TPL_VAR["coupon"]){?>
						<tr>
							<!-- <th>쿠폰적용가 :</th> -->	
							<td>
								<span class="price-normal price-strike" id="nprice"><?php echo number_format($TPL_VAR["price"])?></span><span class="price-won">원</span><span class="slash">/</span>
								<span class="price-sale" id="cprice"><?php echo number_format($TPL_VAR["couponprice"])?></span><span class="price-won">원</span><span class="slash">/</span>
								<span class="price-sale-ratio">SALE <?php echo $TPL_VAR["coupon"]/$TPL_VAR["price"]* 100?>%↓</span><span class="sale-txt">(쿠폰적용시)</span>
								<span class="slash"></span>
<?php if($TPL_a_coupon_1){foreach($TPL_VAR["a_coupon"] as $TPL_V1){?>
								<span class="price-sale"><button onclick="ifrmHidden.location.href='<?php echo url("proc/dn_coupon_goods.php?")?>&goodsno=<?php echo $TPL_VAR["goodsno"]?>&couponcd=<?php echo $TPL_V1["couponcd"]?>'"  class="medium button-dark">쿠폰받기</button></span>
								<!-- <?php }}?> -->
								<!-- 
								<span id=obj_coupon style="font-weight:bold;color:#EF1C21"><?php echo number_format($TPL_VAR["couponprice"])?>원&nbsp;(-<?php echo number_format($TPL_VAR["coupon"])?>원)</span>
								<div><?php echo $TPL_VAR["about_coupon"]?></div>
								 -->
							</td>
						</tr>
<?php }elseif($TPL_VAR["memberdc"]){?>
						<tr>
							<!-- <th>회원할인가 :</th> -->
							<td>
								<span class="price-normal price-strike" id="nprice"><?php echo number_format($TPL_VAR["price"])?></span><span class="price-won">원</span><span class="slash">/</span>
								<span class="price-sale" id="price-amount"><?php echo number_format($TPL_VAR["price"]-$TPL_VAR["memberdc"])?></span><span class="price-won">원</span><span class="slash">/</span>
								<span class="price-sale-ratio">SALE <?php echo $TPL_VAR["memberdc"]/$TPL_VAR["price"]* 100?>%↓</span><span class="sale-txt">(회원할인)</span>
							</td>
						</tr>
<?php }else{?>
						<tr>
							<!-- <th>판매금액 :</th> -->
							<td>
								<span class="price-sale" id="nprice"><?php echo number_format($TPL_VAR["price"])?></span><span class="price-won">원</span>
							</td>
						</tr>
<?php }?>
						
<?php if($TPL_VAR["consumer"]){?>
						<tr>
							<!-- <th>소비자가격 :</th> -->
							<td>
								<span id="consumer"><?php echo number_format($TPL_VAR["consumer"])?></span>원
							</td>
						</tr>
<?php }?>
						
						<tr>
							<!-- <th>예상적립금 :</th> -->
							<td><span class="price-title">예상 적립금:</span><span id=reserve><?php echo number_format($TPL_VAR["reserve"])?></span>원&nbsp;<span class="price-title">(할인 상품은 결제금액 기준으로 적립)</span></td>
						</tr>
						
						
<?php if($TPL_VAR["naverNcash"]=='Y'){?>
						<tr id="naver-mileage-accum" style="display: none;">
							<!-- <th>네이버&nbsp;&nbsp;<br/>마일리지 :</th> -->
							<td>
<?php if($TPL_VAR["exception"]){?>
									<?php echo $TPL_VAR["exception"]?>

<?php }else{?>
								<span class="price-title">네이버&nbsp;&nbsp;<br/>마일리지:</span>
								<span id="naver-mileage-accum-rate" style="font-weight:bold;color:#1ec228;"></span> 적립
<?php }?>
								<img src="<?php echo $GLOBALS["cfg"]["rootDir"]?>/proc/naver_mileage/images/n_mileage_info4.png" onclick="javascript:mileage_info();" style="cursor: pointer; vertical-align: middle;">
							</td>
						</tr>
<?php }?>
						
<?php if($TPL_VAR["coupon_emoney"]){?>
						<tr>
							<!-- <th>쿠폰적립금:</th> -->
							<td>
								<span class="price-title">쿠폰적립금:</span><span id=obj_coupon_emoney style="font-weight:bold;color:#EF1C21"></span> &nbsp;<span style="font:bold 9pt tahoma; color:#FF0000" ><?php echo number_format($TPL_VAR["coupon_emoney"])?>원</span>
							</td></tr>
<?php }?>
						
						
<?php if($TPL_VAR["delivery_type"]== 0){?>
						<tr>
							<!-- <th>배송비 :</th>-->
							<td class="goods-view-delivery">
								<div class="delivery-info">
									<span class="price-title"><?php echo number_format( 2500)?>원&nbsp;(10만원이상 구매시 무료배송)</span>
									<span>/</span>
									<span class="price-title"><!-- <?php if($TPL_VAR["delivery_method"]){?> --> <?php echo $TPL_VAR["delivery_method"]?> <!-- <?php }else{?> -->1~3일<!-- <?php }?> --></span>
								</div>
							</td>
						</tr>
<?php }elseif($TPL_VAR["delivery_type"]== 1){?>
						<tr>
							<!-- <th>배송비 :</th>-->
							<td class="goods-view-delivery">
								<div class="delivery-info">
									<span class="price-title f14">무료배송</span>
									<span>/</span>
									<span class="price-title"><!-- <?php if($TPL_VAR["delivery_method"]){?> --> <?php echo $TPL_VAR["delivery_method"]?> <!-- <?php }else{?> -->1~3일<!-- <?php }?> --></span>
								</div>
							</td>
						</tr>
<?php }elseif($TPL_VAR["delivery_type"]== 2){?>
						<tr>
							<!-- <th>개별배송비 :</th>-->
							<td class="goods-view-delivery">
								<div class="delivery-info">
									<span class="price-title f14">개별배송비:</span><span><?php echo number_format($TPL_VAR["goods_delivery"])?>원</span>
									<span>/</span>
									<span class="price-title"><!-- <?php if($TPL_VAR["delivery_method"]){?> --> <?php echo $TPL_VAR["delivery_method"]?> <!-- <?php }else{?> -->1~3일<!-- <?php }?> --></span>
								</div>
							</td>
						</tr>
<?php }elseif($TPL_VAR["delivery_type"]== 3){?>
						<tr>
							<!--  <th>착불배송비:</th>-->
							<td class="goods-view-delivery">
								<div class="delivery-info">
									<span class="price-title f14">착불배송</span><!-- <?php echo number_format($TPL_VAR["goods_delivery"])?>원-->
									<span>/</span>
									<span class="price-title"><!-- <?php if($TPL_VAR["delivery_method"]){?> --> <?php echo $TPL_VAR["delivery_method"]?> <!-- <?php }else{?> -->1~3일<!-- <?php }?> --></span>
								</div>
										
							</td>
						</tr>
<?php }elseif($TPL_VAR["delivery_type"]== 4){?>
						<tr>
							<!-- <th>고정배송비 :</th>-->
							<td class="goods-view-delivery">
								<div class="delivery-info">
									<span class="price-title f14">고정배송비: </span><span><?php echo number_format($TPL_VAR["goods_delivery"])?>원</span> 
									<span>/</span>
									<span class="price-title"><!-- <?php if($TPL_VAR["delivery_method"]){?> --> <?php echo $TPL_VAR["delivery_method"]?> <!-- <?php }else{?> -->1~3일<!-- <?php }?> --></span>
								</div>
							</td>
						</tr>
<?php }elseif($TPL_VAR["delivery_type"]== 5){?>
						<tr>
							<!-- <th>수량별배송비 :</th> -->
							<td class="goods-view-delivery">
								<div class="delivery-info">
									<span class="price-title f14">수량별배송비:</span><span><?php echo number_format($TPL_VAR["goods_delivery"])?>원 (수량에 따라 배송비가 추가됩니다.)</span>
									<span>/</span>
									<span class="price-title"><!-- <?php if($TPL_VAR["delivery_method"]){?> --> <?php echo $TPL_VAR["delivery_method"]?> <!-- <?php }else{?> -->1~3일<!-- <?php }?> --></span>
								</div>
							</td>
						</tr>
<?php }?>
						
<?php }?>	
				</table>
				<!-- <?php }?> -->
				
				
				<table class="top1">
<?php if($TPL_VAR["manufacture_date"]){?><tr><th>제조일자 :</th><td><?php echo $TPL_VAR["manufacture_date"]?></td></tr><?php }?>
<?php if($TPL_VAR["effective_date_start"]){?><tr><th>유효일자 :</th><td><?php echo $TPL_VAR["effective_date_start"]?> ~ <?php echo $TPL_VAR["effective_date_end"]?></td></tr><?php }?>
<?php if($TPL_VAR["delivery_area"]){?><tr><th>배송지역 :</th><td><?php echo $TPL_VAR["delivery_area"]?></td></tr><?php }?>
<?php if($TPL_VAR["origin"]){?><!-- <tr><th>원산지 :</th><td><?php echo $TPL_VAR["origin"]?></td></tr>--><?php }?>
<?php if($TPL_VAR["launchdt"]){?><tr><th>출시일 :</th><td><?php echo $TPL_VAR["launchdt"]?></td></tr><?php }?>
					
<?php if($TPL_ex_1){foreach($TPL_VAR["ex"] as $TPL_K1=>$TPL_V1){?>
					<tr>
						<td>
							<div style="margin-left:10px; padding-left:35px;clear:both;"">
								<span class="price-title">- <?php echo $TPL_K1?>:</span>
								<span class="price-title"><?php echo $TPL_V1?></span>
							</div>
						</td>
					</tr>
<?php }}?>
				
<?php if(!$GLOBALS["opt"]){?>
					<tr >
						<td style="border-top:1px solid #fff; padding-top:15px;">
<?php if(!$TPL_VAR["runout"]){?>
							<div class="qty-title"><span class="v-middle">주문수량</span></div>
							<div style="inline-block;float:left;">
								<input type=text name=ea size=2 value=<?php if($TPL_VAR["min_ea"]){?><?php echo $TPL_VAR["min_ea"]?><?php }else{?>1<?php }?> class=line style="text-align:right;height:20px;font-size:14px;" step="<?php if($TPL_VAR["sales_unit"]){?><?php echo $TPL_VAR["sales_unit"]?><?php }else{?>1<?php }?>" min="<?php if($TPL_VAR["min_ea"]){?><?php echo $TPL_VAR["min_ea"]?><?php }else{?>1<?php }?>" max="<?php if($TPL_VAR["max_ea"]){?><?php echo $TPL_VAR["max_ea"]?><?php }else{?>0<?php }?>" onblur="chg_cart_ea(frmView.ea,'set');">
							</div>
							<!-- 
							<div style="float:left;padding-left:3">
								<div style="padding:1 0 2 0"><img src="/shop/data/skin/freemart/img/common/btn_plus.gif" onClick="chg_cart_ea(frmView.ea,'up')" style="cursor:pointer"></div>
								<div><img src="/shop/data/skin/freemart/img/common/btn_minus.gif" onClick="chg_cart_ea(frmView.ea,'dn')" style="cursor:pointer"></div>
							</div>
							 -->
							 <!-- <div class="qty-ea"><span class="v-middle">개</span></div>-->
							 <!-- <div style="padding-top:3; float:left">개</div>  -->
							 
							<div style="padding-left:10px;float:left" class="stxt">
<?php if($TPL_VAR["min_ea"]> 1){?><div>최소구매수량 : <?php echo $TPL_VAR["min_ea"]?>개</div><?php }?>
<?php if($TPL_VAR["max_ea"]!='0'){?><div>최대구매수량 : <?php echo $TPL_VAR["max_ea"]?>개</div><?php }?>
<?php if($TPL_VAR["sales_unit"]> 1){?><div>묶음주문단위 : <?php echo $TPL_VAR["sales_unit"]?>개</div><?php }?>
							</div>
<?php }else{?>
							<span class="price-title v-middle" style="padding-left:10px;">품절된 상품입니다</span>
<?php }?>
						</td>
					</tr>
					<tr><td height="5"></td></tr>
<?php }else{?>
						<input type=hidden name=ea step="<?php if($TPL_VAR["sales_unit"]){?><?php echo $TPL_VAR["sales_unit"]?><?php }else{?>1<?php }?>" min="<?php if($TPL_VAR["min_ea"]){?><?php echo $TPL_VAR["min_ea"]?><?php }else{?>1<?php }?>" max="<?php if($TPL_VAR["max_ea"]){?><?php echo $TPL_VAR["max_ea"]?><?php }else{?>0<?php }?>" value=<?php if($TPL_VAR["min_ea"]){?><?php echo $TPL_VAR["min_ea"]?><?php }else{?>1<?php }?>>
<?php }?>
				</table>

<?php if(!$TPL_VAR["strprice"]){?>
				<!-- 추가 옵션 입력형 -->
<?php if($GLOBALS["addopt_inputable"]){?>
				<table class="top1">
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
				<table class="top1">
					<tr><td height="3px"></td></tr>
					<tr>
						<td>
							<span class="option-title"><?php echo $TPL_VAR["optnm"]?></span>
							<div class="option-select1">
								<select class="soflow" name="opt[]" onchange="chkOption(this);chkOptimg();nsGodo_MultiOption.set();updateUnitPrice(this, <?php echo $TPL_VAR["price"]?>, <?php echo $TPL_VAR["special_discount_amount"]?>, nsGodo_MultiOption);" required fld_esssential msgR="<?php echo $TPL_VAR["optnm"]?> 선택을 해주세요">
									<option value="">== 옵션선택 ==
<?php if($TPL__opt_1){foreach($GLOBALS["opt"] as $TPL_V1){?><?php if((is_array($TPL_R2=$TPL_V1)&&!empty($TPL_R2)) || (is_object($TPL_R2) && in_array("Countable", class_implements($TPL_R2)) && $TPL_R2->count() > 0)) {foreach($TPL_R2 as $TPL_V2){?>
										<option value="<?php echo $TPL_V2["opt1"]?><?php if($TPL_V2["opt2"]){?>|<?php echo $TPL_V2["opt2"]?><?php }?>" <?php if($TPL_VAR["usestock"]&&!$TPL_V2["stock"]){?> disabled class=disabled<?php }?>><?php echo $TPL_V2["opt1"]?><?php if($TPL_V2["opt2"]){?>/<?php echo $TPL_V2["opt2"]?><?php }?> <?php if($TPL_V2["price"]!=$TPL_VAR["price"]){?>(<?php echo number_format($TPL_V2["price"])?>원)<?php }?>
<?php if($TPL_VAR["usestock"]&&!$TPL_V2["stock"]){?> [품절]<?php }?>
<?php }}?><?php }}?>
								</select>
							</div>
						</td>
					</tr>
					<tr><td height=6></td></tr>
				</table>
<?php }?>


				<!-- 필수 옵션 분리형 -->
<?php if($GLOBALS["opt"]&&$GLOBALS["typeOption"]=="double"){?>
				<table class="top1">
					<tr><td height="3px"></td></tr>
<?php if($TPL__optnm_1){$TPL_I1=-1;foreach($GLOBALS["optnm"] as $TPL_V1){$TPL_I1++;?>
					<tr>
						<td>
							<span class="option-title"><?php echo $TPL_V1?></span>
							<!-- 옵션 선택 -->
							<div class="option-select1">
<?php if(!$TPL_I1){?>
								<select name="opt[]" class="soflow" onchange="subOption(this);chkOptimg();selicon(this);nsGodo_MultiOption.set();" required fld_esssential msgR="<?php echo $TPL_V1?> 선택을 해주세요">
									<option value="">== 옵션선택 ==
<?php if((is_array($TPL_R2=($GLOBALS["opt"]))&&!empty($TPL_R2)) || (is_object($TPL_R2) && in_array("Countable", class_implements($TPL_R2)) && $TPL_R2->count() > 0)) {foreach($TPL_R2 as $TPL_K2=>$TPL_V2){?><option value="<?php echo $TPL_K2?>"><?php echo $TPL_K2?><?php }}?>
								</select>
<?php }else{?>	
								<select name="opt[]" class="soflow" onchange="chkOption(this);selicon(this);nsGodo_MultiOption.set();" required fld_esssential msgR="<?php echo $TPL_V1?> 선택을 해주세요"><option value="">==선택==</select>
<?php }?>
							</div>
							<input type="hidden" name="opt_txt[]" value="">	
						</td>
					</tr>
<?php }}?>
					<tr><td height=6></td></tr>
				</table>
				<script>subOption(document.getElementsByName('opt[]')[0])</script>
<?php }?>

				<!-- 추가 옵션 -->
<?php if($TPL__addopt_1){$TPL_I1=-1;foreach($GLOBALS["addopt"] as $TPL_K1=>$TPL_V1){$TPL_I1++;?>
				<table class="top1"><!--  class=sub>-->
					<tr>
						<td><span class="option-title"><?php echo $TPL_K1?></span>
							<div class="option-select1">
<?php if($GLOBALS["addoptreq"][$TPL_I1]){?>
							
							<select class="soflow" name="addopt[]" required fld_esssential label="<?php echo $TPL_K1?>" onchange="nsGodo_MultiOption.set();">
								<option value="">==<?php echo $TPL_K1?> 선택==</option>
<?php }else{?>
							<select class="soflow" name="addopt[]" label="<?php echo $TPL_K1?>" onchange="nsGodo_MultiOption.set(); updatePrice(this, <?php echo $TPL_VAR["price"]?>, <?php echo $TPL_VAR["special_discount_amount"]?>);">
								<option value="">==<?php echo $TPL_K1?> 선택==</option>
								<option value="-1">선택안함</option>
<?php }?>
<?php if((is_array($TPL_R2=$TPL_V1)&&!empty($TPL_R2)) || (is_object($TPL_R2) && in_array("Countable", class_implements($TPL_R2)) && $TPL_R2->count() > 0)) {foreach($TPL_R2 as $TPL_V2){?>
							<option value="<?php echo $TPL_V2["sno"]?>^<?php echo $TPL_K1?>^<?php echo $TPL_V2["opt"]?>^<?php echo $TPL_V2["addprice"]?>"><?php echo $TPL_V2["opt"]?>

<?php if($TPL_V2["addprice"]){?>(<?php echo number_format($TPL_V2["addprice"])?>원 추가)<?php }?>
							</option>
<?php }}?>
							</select>
						</div>
						</td>
					</tr>
				</table>
<?php }}?>

				<!-- ? 옵션 있으면 see 옵션백업.js-->
				
				<div id="fb-root"></div>
				<script>
					(function(d, s, id) {
					  var js, fjs = d.getElementsByTagName(s)[0];
					  if (d.getElementById(id)) return;
					  js = d.createElement(s); js.id = id;
					  js.src = "//connect.facebook.net/ko_KR/sdk.js#xfbml=1&version=v2.5";
					  fjs.parentNode.insertBefore(js, fjs);
					}(document, 'script', 'facebook-jssdk'));
				</script>

				<div id="el-multi-option-display" class="goods-multi-option">
					<table style="border-collapse:collapse;">
						<col width=""><col width="60"><col width="150">
					</table>
				
					<div id="price-total">
						<div id="prod-price-amount" class="amount">					
						  <span class="payment-title">합계금액:</span><span class="payment-amount bold" id="el-multi-option-total-price"></span>
						</div> 
					</div>
					
					<div id="prod-price-discount" style="display:none;">
					  	<div id="discount-amount" class="amount-noline"><span class="payment-title">할인금액:</span><span class="payment-amount pred"  id="el-multi-option-discount-amount"></span></div>
					  	<div id="payment-amount" class="amount"><span class="payment-title">결제금액:</span><span class="payment-amount pred bold"  id="el-multi-option-payment-amount"></span></div>
					</div>
						
				</div>
				
				<!-- / -->

<?php }?>
				<?php echo $TPL_VAR["cyworldScrap"]?>

				<?php echo $TPL_VAR["snsBtn"]?>



<?php if($TPL_VAR["setGoodsConfig"]=='Y'){?>
				<a href="../setGoods/?cody=<?php echo $TPL_VAR["goodsno"]?>"><img src="/shop/data/skin/freemart/img/common/btn_codylink.gif"></a>
<?php }?>
				<!-- 각종 버튼 -->
				<div class="shop_buttons">
<?php if($TPL_VAR["stocked_noti"]){?>
					<div class="backorder_txt">&nbsp;재입고 알림 신청을 하시면 입고시 알림 서비스를 받으실 수 있습니다.</div>
<?php }?>
<?php if(!$TPL_VAR["strprice"]&&!$TPL_VAR["runout"]&&($TPL_VAR["sales_status"]=='ing'||$TPL_VAR["sales_status"]=='range')){?>
<?php if($TPL_VAR["clevel"]=='0'||$TPL_VAR["slevel"]>=$TPL_VAR["clevel"]){?>
					
					<button class="button-big-wide button-red" onclick="cartAdd(frmView,'<?php echo $TPL_VAR["cartCfg"]->redirectType?>')">장바구니</button>
					<button class="button-big-wide button-dark" onclick="act('../order/order')">바로구매</button>
					<button class="button-big-wide button-grey-b" onclick="act('../mypage/mypage_wishlist')">WishList</button>
					
<?php }?>
<?php }?>
<?php if($TPL_VAR["stocked_noti"]){?>
					<button class="button-big button-cart" onclick="fnRequestStockedNoti('<?php echo $TPL_VAR["goodsno"]?>');">재입고 알림 신청</button>
					<!--<a href="javascript:fnRequestStockedNoti('<?php echo $TPL_VAR["goodsno"]?>');"><img src="/shop/data/skin/freemart/img/common/btn_backorder_alert.png"></a>-->
<?php }?>
					<!--<a href="<?php echo url("goods/goods_list.php?")?>&category=<?php echo $_GET["category"]?>"><img src="/shop/data/skin/freemart/img/common/btn_list.gif"></a>-->
				</div>

				<!--  SNS Share -->
				<div class="share-buttons">
					<span id='sb_facebook' ></span> 
				  	<span id='sb_twitter'></span> 
				  	<span id='sb_googleplus'></span> 
				  	<span id='sb_pinterest'></span> 
				  	<span id='sb_reddit'></span>
				  	<div id="sns_count">
					  	<div class="fb-like" data-href="http://francosmith.com/shop/goods/goods_view.php?goodsno=<?php echo $TPL_VAR["goodsno"]?>" data-layout="button_count" data-width="90" data-action="like" data-show-faces="true"></div>
				  	</div>
				</div>

				<div id="naver-pay"><?php echo $TPL_VAR["naverCheckout"]?></div>
				<div><?php echo $TPL_VAR["auctionIpayBtn"]?></div>
				<?php echo $TPL_VAR["plusCheeseBtn"]?>

				</form>
			</div><!--  goods-view-spec -->
		</div><!-- goods-view-body -->

		
		<!-- <table style="clear:both;" border="5">
		<tr>
			<td> -->
		<div id="in-detail-wrapper">	
			<div class="dot-line"></div>
			
<?php if($TPL_VAR["coupon"]||$TPL_VAR["coupon_emoney"]){?>
			<!-- 할인쿠폰 다운받기 -->
			<div style="clear:both; width:100%; margin:0 auto; display:inline-block;height:70px; padding-top:10px;">
				<div style="margin:0 auto; padding:10 0 0 0; width:600px; height:70px; border-bottom:1px dotted #cccccc" >
					<div style="float:left; margin-right:20px; display:inline-block;"><img src="/shop/data/skin/freemart/img/common/coupon_txt.gif"></div>
<?php if($TPL_a_coupon_1){$TPL_I1=-1;foreach($TPL_VAR["a_coupon"] as $TPL_V1){$TPL_I1++;?>
					<div onclick="ifrmHidden.location.href='<?php echo url("proc/dn_coupon_goods.php?")?>&goodsno=<?php echo $TPL_VAR["goodsno"]?>&couponcd=<?php echo $TPL_V1["couponcd"]?>'" class=hand>
<?php if($TPL_V1["coupon_img"]== 4){?>
						<div style="margin-top:5px; float:left;font:bold 12px tahoma;color:#FF0000;text-align:center;padding:19px 40px 0 0;width:140px;height:54px;background:url('<?php echo $TPL_VAR["coupon_img_path"]?><?php echo ($TPL_V1["coupon_img_file"])?>') no-repeat;" onmouseover="cp_explain('<?php echo $TPL_I1?>');" onmouseout="cp_explain_close('<?php echo $TPL_I1?>');"><?php echo $GLOBALS["r_couponAbility"][$TPL_V1["ability"]]?><?php if(substr($TPL_V1["price"], - 1)!="%"){?><?php echo number_format($TPL_V1["price"])?>원<?php }else{?><?php echo $TPL_V1["price"]?><?php }?></div>
<?php }else{?>
						<div style="margin-top:5px; float:left;font:bold 12px tahoma;color:#FF0000;text-align:center;padding:19px 20px 0 0;width:140px;height:54px;background:url('/shop/data/skin/freemart/img/common/coupon0<?php echo ($TPL_V1["coupon_img"]+ 1)?>.gif') no-repeat;" ><span style="position:relative; top:10px;"><?php echo $GLOBALS["r_couponAbility"][$TPL_V1["ability"]]?><?php if(substr($TPL_V1["price"], - 1)!="%"){?><?php echo number_format($TPL_V1["price"])?>원<?php }else{?><?php echo $TPL_V1["price"]?><?php }?></span></div>
						<div style="float:left;margin-top:5px;" >
							<div style="padding:0 0 0 0;color:#FF0000;text-align:center;width:200px;font:bold 12px tahoma;"><?php echo $TPL_V1["coupon_detail"]?></div>
<?php if($TPL_V1["coupon_priodtype"]== 0){?>
							<div style="padding:10 0 0 0;text-align:center;"><?php echo $TPL_V1["coupon_sdate"]?> 부터</div>
							<div style="text-align:center;"><?php echo $TPL_V1["coupon_edate"]?> 까지</div>
<?php }else{?>
							<div style="padding:10 0 0 0;text-align:center;">발급 후 <?php echo $TPL_V1["coupon_sdate"]?> 일 간 사용 가능</div>
<?php }?>
<?php if($TPL_V1["payMethodStr"]){?>
							<div style="padding:0 0 0 0;text-align:center;width:200px;"><?php echo $TPL_V1["payMethodStr"]?></div>
<?php }?>
						</div>
<?php }?>
<?php }}?>
					</div>
				</div>
			</div>
			
<?php }?>
			<!-- 할인쿠폰 다운 END -->
				
			<!-- 상품공시 정보 -->
<?php if($TPL_extra_info_1){?>
			<div class="extra-information-title">상품정보제공 고시</div>
			<table class="extra-information">
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
			<div class="dot-line"></div>
<?php }?>
			
			<div class="label-link">
				<span id="accessery"><a href="#related-goods-container">Related/Accessories</a></span>
				<span id="features"><a href="#goods-desc-contents">Features</a></span>
				<span id="sepcification"><a href="#specifications">Specification</a></span>
				<span id="reviews"><a href="#review-container">Reviews</a></span>
				<span id="return-link"><a href="#delivery-info">Delivery/Returns</a></span>
				
			</div> 
			<div class="dot-line"></div>
			
			<div id="related-goods-container" >
				<table  id="related-goods">
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
					
			<!-- 상세 설명 -->
			<div id="goods-desc-contents"">
			<?php echo $TPL_VAR["longdesc"]?>

			<script>setViewMoreFrom('View More From', 'view-more-catetory', "<?php echo currPosition($TPL_VAR["category"])?>");</script>
			</div>
			<div class="dot-line"></div>
			
<?php if($TPL_VAR["use_external_video"]){?>
			<div style="padding:10px 0" id="external-video">
				<?php echo youtubePlayer($TPL_VAR["external_video_url"],$TPL_VAR["external_video_size_type"],$TPL_VAR["external_video_width"],$TPL_VAR["external_video_height"])?>

			</div>
<?php }?>
			
			<!-- 상품 공통 정보 시작 -->
<?php if((is_array($TPL_R1=commoninfo())&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?>
			<table border=0 cellpadding=0 cellspacing=0>
			<tr>
				<TD style="background: URL(/shop/data/skin/freemart/img/common/bar_detail_l.gif) no-repeat;" nowrap width="10" height="24"></TD>
				<TD style="background: URL(/shop/data/skin/freemart/img/common/bar_detail_c.gif) repeat-x; font-weight:bold;" width='100%'><?php echo $TPL_V1["title"]?>2</TD>
				<TD style="background: URL(/shop/data/skin/freemart/img/common/bar_detail_r.gif) no-repeat;" nowrap width="10" height="24">3</TD>
			</tr>
			</table>
			<div style="width:100%;padding:10 10 10 10;overflow:hidden">
				<table cellspacing=0 cellpadding=0>
				<tr>
					<td><?php echo $TPL_V1["info"]?></td>
				</tr>
				</table>
			</div>
			<div style="margin-bottom:20px;"></div>
<?php }}?>
			<!-- 상품 공통 정보 종료 -->
			
			
			<!-- 상품 사용기 -->
			<div id="review-container" style="margin-top:20px; width:100%;">
				<iframe id="inreview" src="./goods_review_list.php?goodsno=<?php echo $TPL_VAR["goodsno"]?>" frameborder="0" marginwidth="0" marginheight="0" scrolling="no" style="position:relative; width:100%"></iframe>
			</div>
			
			<div id="qna-container" style="margin-top:20px; width:100%;">
			<!-- 상품 질문과답변 -->
			<iframe id="inqna" src="./goods_qna_list.php?goodsno=<?php echo $TPL_VAR["goodsno"]?>" frameborder="0" marginwidth="0" marginheight="0"  scrolling="no" style="position:relative; width:100%"></iframe>
			</div>
		
			
			<div id="recent-view-goods-container">
				<div><h4>최근 본 상품</h4></div>
				<div class="single-row" >
<?php if($TPL__todayGoodsList_1){foreach($GLOBALS["todayGoodsList"] as $TPL_V1){?>
					<div class="goods-thumb" ><a href="<?php echo url("goods/goods_view.php?")?>&goodsno=<?php echo $TPL_V1["goodsno"]?>"><?php echo goodsimg($TPL_V1["img"], 70)?></a></div>
<?php }}?>
				</div>
			</div>
			
			<div id="return-title">
				<h2>배송/반품 안내</h2>
			</div>
			
			<div id="delivery-info" style="margin-top:20px; padding-bottom:20px;width:100%;">
				<?php echo $this->define('tpl_include_file_1','proc/shopping_info.htm')?> <?php $this->print_("tpl_include_file_1",$TPL_SCP,1);?>

			</div>
			
			
			
		<!-- 
			</td>
		</tr>
		</table>
		 -->
		</div>
	</div>
	<!-- End indiv -->

		<div style="display:none;position:absolute;z-index:10;cursor:hand;" id="qrExplain" onclick="qrExplain_close();">
		<table>
		<tr>
			<td width="4" height="271" valign="top" background="/shop/data/skin/freemart/img/common/page02_detail_blt.gif" style="background-repeat:no-repeat"></td>
			<td  width="285" height="271" valign="top" background="/shop/data/skin/freemart/img/common/page02_detail.gif"></td>
		</tr>
		</table>
		<div style='width:289' onclick="qrExplain_close();" style="cursor:hand;text-align:center">[닫기]</div>
		</div>
		
		<script type="text/javascript">
			// SNS 공유버튼 바인딩
			var domain = "http://francosmith.com/";
			var goods_url = "shop/goods/goods_view.php?goodsno=<?php echo $TPL_VAR["goodsno"]?>";
			var goods_name = "<?php echo str_replace("\"","\\\"",$TPL_VAR["goodsnm"])?>" ;
			var goods_image = 'shop/data/goods/<?php echo $TPL_VAR["r_img"][ 0]?>';
			var jq = jQuery.noConflict();
			jq(".share-buttons span").each(function() {
				bind_share_button(this.id, domain+goods_url, goods_name, domain+goods_image);			  
			});
		</script>
		
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
</div><!--  END page-wrapper -->