<?php /* Template_ 2.2.7 2016/05/14 16:14:58 /www/francotr3287_godo_co_kr/shop/data/skin/freemart/goods/goods_preview.htm 000041486 */ 
if (is_array($GLOBALS["opt"])) $TPL__opt_1=count($GLOBALS["opt"]); else if (is_object($GLOBALS["opt"]) && in_array("Countable", class_implements($GLOBALS["opt"]))) $TPL__opt_1=$GLOBALS["opt"]->count();else $TPL__opt_1=0;
if (is_array($GLOBALS["opt1img"])) $TPL__opt1img_1=count($GLOBALS["opt1img"]); else if (is_object($GLOBALS["opt1img"]) && in_array("Countable", class_implements($GLOBALS["opt1img"]))) $TPL__opt1img_1=$GLOBALS["opt1img"]->count();else $TPL__opt1img_1=0;
if (is_array($TPL_VAR["t_img"])) $TPL_t_img_1=count($TPL_VAR["t_img"]); else if (is_object($TPL_VAR["t_img"]) && in_array("Countable", class_implements($TPL_VAR["t_img"]))) $TPL_t_img_1=$TPL_VAR["t_img"]->count();else $TPL_t_img_1=0;
if (is_array($TPL_VAR["a_coupon"])) $TPL_a_coupon_1=count($TPL_VAR["a_coupon"]); else if (is_object($TPL_VAR["a_coupon"]) && in_array("Countable", class_implements($TPL_VAR["a_coupon"]))) $TPL_a_coupon_1=$TPL_VAR["a_coupon"]->count();else $TPL_a_coupon_1=0;
if (is_array($TPL_VAR["ex"])) $TPL_ex_1=count($TPL_VAR["ex"]); else if (is_object($TPL_VAR["ex"]) && in_array("Countable", class_implements($TPL_VAR["ex"]))) $TPL_ex_1=$TPL_VAR["ex"]->count();else $TPL_ex_1=0;
if (is_array($GLOBALS["addopt_inputable"])) $TPL__addopt_inputable_1=count($GLOBALS["addopt_inputable"]); else if (is_object($GLOBALS["addopt_inputable"]) && in_array("Countable", class_implements($GLOBALS["addopt_inputable"]))) $TPL__addopt_inputable_1=$GLOBALS["addopt_inputable"]->count();else $TPL__addopt_inputable_1=0;
if (is_array($GLOBALS["optnm"])) $TPL__optnm_1=count($GLOBALS["optnm"]); else if (is_object($GLOBALS["optnm"]) && in_array("Countable", class_implements($GLOBALS["optnm"]))) $TPL__optnm_1=$GLOBALS["optnm"]->count();else $TPL__optnm_1=0;
if (is_array($GLOBALS["addopt"])) $TPL__addopt_1=count($GLOBALS["addopt"]); else if (is_object($GLOBALS["addopt"]) && in_array("Countable", class_implements($GLOBALS["addopt"]))) $TPL__addopt_1=$GLOBALS["addopt"]->count();else $TPL__addopt_1=0;?>
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

<!--[if lt IE 9]>
	<script src="/shop/data/js/jquery-1.11.3.min.js"></script> 
<![endif]-->

<!--[if gte IE 9 ]><!-->
   <script src="/shop/data/js/jquery-2.2.3.min.js" ></script> 
<!-- [endif]-->
	
<!-- [if !IE]> <!-->
	<script src="/shop/data/js/jquery-1.11.3.min.js"></script> 
<!--  [endif]-->

<title>상품 미리보기</title>


<script src="/shop/data/skin/freemart/common.js"></script>
<script src="/shop/lib/js/countdown.js"></script>
<script src="/shop/data/skin/freemart/js/option-handler.js" type="text/javascript"></script>
<script src="/shop/data/skin/freemart/js/view_goods.js" type="text/javascript"></script>
<script src="/js/mall.js"></script>

<link rel="styleSheet" href="/shop/data/skin/freemart/shop_layout.css">
<link rel="styleSheet" href="/shop/data/skin/freemart/prod.css">
<link rel="styleSheet" href="/shop/data/skin/freemart/table.css">	
<link rel="styleSheet" href="/shop/data/skin/freemart/font.css" />	
<link rel="styleSheet" href="/shop/data/skin/freemart/button.css" />	
	
		
<link rel="styleSheet" href="/shop/data/skin/freemart/style.css">
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
</style>
<script>
function fitwin()
{
	window.resizeTo(900,650);
	var borderY = document.body.clientHeight;

	width	= 900;
	height	= 700;

	windowX = (window.screen.width-width)/2;
	windowY = (window.screen.height-height)/2;

	if(width>screen.width){
		width = screen.width;
		windowX = 0;
	}
	
	if(height>screen.height){
		height = screen.height;
		windowY = 0;
	}

	window.moveTo(windowX,windowY);
	window.resizeTo(width,height);
}

function viewDetailPage(goodsno) {
	this.opener.location.href="/shop/goods/goods_view.php?goodsno="+goodsno+"&category=";
	this.close();
}


</script>
<script>

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
opt['<?php echo $TPL_I1+ 1?>'] = new Array("('== 옵션선택 ==','')",<?php if((is_array($TPL_R2=$TPL_V1)&&!empty($TPL_R2)) || (is_object($TPL_R2) && in_array("Countable", class_implements($TPL_R2)) && $TPL_R2->count() > 0)) {$TPL_S2=count($TPL_R2);$TPL_I2=-1;foreach($TPL_R2 as $TPL_V2){$TPL_I2++;?>"('<?php echo $TPL_V2["opt2"]?><?php if($TPL_V2["price"]!=$TPL_VAR["price"]){?> (<?php echo number_format($TPL_V2["price"])?>원)<?php }?><?php if($TPL_VAR["usestock"]&&!$TPL_V2["stock"]){?> [품절]<?php }?>','<?php echo $TPL_V2["opt2"]?>','<?php if($TPL_VAR["usestock"]&&!$TPL_V2["stock"]){?>soldout<?php }?>')"<?php if($TPL_I2!=$TPL_S2- 1){?>,<?php }?><?php }}?>);
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
}

function chkOption(obj)
{
	if (!selectDisabled(obj)) return false;
}


function cartAdd(form,redirectType, frmTarget)
{
	var opt_cnt = 0, data;
	var actClose=false;
	
	if(typeof nsGodo_MultiOption!='undefined'){
		nsGodo_MultiOption.clearField();

		for (var k in nsGodo_MultiOption.data) {
			data = nsGodo_MultiOption.data[k];
			if (data && typeof data == 'object') {
				nsGodo_MultiOption.addField(data, opt_cnt);
				opt_cnt++;
			}
		}
	}

	if(typeof chkGoodsForm!='undefined') {
		if (opt_cnt < 1) {
			if (!chkGoodsForm(form))return;
		}
	}
	else{
		if (!chkForm(form))return;
	}

	if (redirectType=='Direct')
	{
		if (frmTarget=='opener'){
			opener.name="mainPage";
			form.target="mainPage";
			actClose=true;
		}
		
		var isCody=(typeof form.cody == 'object')?form.cody.value:'n';
		var dirPath=(isCody=='y')?'../../goods/':'';
		form.action = dirPath+'goods_cart.php';
		form.submit();
	}
	else if(redirectType=='Confirm'){
		layerCartAdd(form);
	}

	if(actClose)self.close();
	return;
}


function act(target,frmTarget)
{
	var form = document.frmView;
	var actClose=false;
	form.action = target + ".php";
	if (frmTarget=='opener'){
		opener.name="mainPage";
		form.target="mainPage";
		actClose=true;
	}

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
		if (chkGoodsForm(form))
			form.submit();
		else
			return;
	}
	
	if(actClose)self.close();

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
// 네이버 마일리지 추가 내용 2011.06.10
function mileage_info(status) {
	document.getElementById("n_mileage").style.display = status;
	document.getElementById("n_mileage").style.left = document.body.scrollLeft + event.clientX;
	document.getElementById("n_mileage").style.top = document.body.scrollTop + event.clientY;
}
// 네이버 마일리지 추가 내용 2011.06.10
<?php }?>

function fnRequestStockedNoti(goodsno) {

	popup('./popup_request_stocked_noti.php?goodsno='+goodsno,360,160);

}

var nsGodo_MultiOption = new GMultiOption( <?php if($TPL_VAR["runout"]){?>true<?php }else{?>false<?php }?> );

</script>
<?php echo $TPL_VAR["systemHeadTagEnd"]?>

</head>

<body onload="javascript:fitwin();">

<div class="indiv"><!-- Start indiv -->
	<div id="goods-view-body">
		<div id="goods-view-img">
			<div class="goods-view-thumb" >
				<span onclick="popup('goods_popup_large.php?goodsno=<?php echo $TPL_VAR["goodsno"]?>',800,600)" style="cursor:pointer;">
				<!--디테일뷰수정--><?php if($TPL_VAR["detailView"]=='y'){?><?php if($TPL_VAR["sc_img"][ 0]){?><?php echo goodsimg($TPL_VAR["sc_img"][ 0], 300,'id="objImg"','','zoom_view')?><?php }else{?><?php echo goodsimg($TPL_VAR["r_img"][ 0], 300,'id="objImg"','','zoom_view')?><?php }?><?php }else{?><?php echo goodsimg($TPL_VAR["r_img"][ 0], 300,'id=objImg')?><?php }?><!--디테일뷰수정--></span>
			</div>
			
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
							<span class="price-sale-ratio">SALE <?php echo $TPL_VAR["special_discount_amount"]/$TPL_VAR["price"]* 100?>%↓</span>
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
						<td class="goods-view-delivery" >
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
								<span class="price-title f14">수량별배송비:</span><?php echo number_format($TPL_VAR["goods_delivery"])?>원 (수량에 따라 배송비가 추가됩니다.)
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
<?php if($TPL_ex_1){foreach($TPL_VAR["ex"] as $TPL_K1=>$TPL_V1){?><tr><th><?php echo $TPL_K1?> :</th><td><?php echo $TPL_V1?></td></tr><?php }}?>
			
<?php if(!$GLOBALS["opt"]){?>
				<tr>
					<td>
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
				
				<button class="button-big-wide button-red" onclick="cartAdd(frmView,'Direct', 'opener')">장바구니</button>
				<button class="button-big-wide button-dark" onclick="act('../order/order', 'opener')">바로구매</button>
				<button class="button-big-wide button-grey-b" onclick="act('../mypage/mypage_wishlist', 'opener')">WishList</button>
				
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
		
	</div>
	
	
</div><!-- End indiv -->


<div style="width:100%; height:50px; margin-top:10px; position:absolute; bottom:0;">
<div style="position:relative; float:right; margin-top:10px; margin-right:10px; bottom:0;">
	<button class="button-medium button-red" onclick="viewDetailPage(<?php echo $TPL_VAR["goodsno"]?>);">상세 페이지 보기</button>
</div>
</div>


<script type="text/javascript">
	// SNS 공유버튼 바인딩
	var domain = "http://francosmith.com/";
	var goods_url = "shop/goods/goods_view.php?goodsno=<?php echo $TPL_VAR["goodsno"]?>";
	var goods_name = '<?php echo $TPL_VAR["goodsnm"]?>';
	var goods_image = 'shop/data/goods/<?php echo $TPL_VAR["r_img"][ 0]?>';
	
	var jq = jQuery.noConflict();
	jq(".share-buttons span").each(function() {
		bind_share_button(this.id, domain+goods_url, goods_name, domain+goods_image);			  
	});
</script>
		
<!--디테일뷰수정-->
<?php if($TPL_VAR["detailView"]=='y'){?>
<script type="text/javascript">
var objImg = document.getElementById("objImg");
objImg.setAttribute("lsrc", objImg.getAttribute("src").replace("/t/", "/").replace("_sc.", '.'));
ImageScope.setImage(objImg, beforeScope, afterScope);
</script>
<?php }?>
<!--디테일뷰수정-->


<iframe name="ifrmHidden" src='/shop/blank.php' style="display:none;width:100%;height:600"></iframe>

</body>
</html>