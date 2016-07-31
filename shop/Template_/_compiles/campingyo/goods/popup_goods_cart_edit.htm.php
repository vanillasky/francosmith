<?php /* Template_ 2.2.7 2014/03/05 23:19:27 /www/francotr3287_godo_co_kr/shop/data/skin/campingyo/goods/popup_goods_cart_edit.htm 000020751 */ 
if (is_array($GLOBALS["opt"])) $TPL__opt_1=count($GLOBALS["opt"]); else if (is_object($GLOBALS["opt"]) && in_array("Countable", class_implements($GLOBALS["opt"]))) $TPL__opt_1=$GLOBALS["opt"]->count();else $TPL__opt_1=0;
if (is_array($GLOBALS["opt1img"])) $TPL__opt1img_1=count($GLOBALS["opt1img"]); else if (is_object($GLOBALS["opt1img"]) && in_array("Countable", class_implements($GLOBALS["opt1img"]))) $TPL__opt1img_1=$GLOBALS["opt1img"]->count();else $TPL__opt1img_1=0;
if (is_array($GLOBALS["addopt_inputable"])) $TPL__addopt_inputable_1=count($GLOBALS["addopt_inputable"]); else if (is_object($GLOBALS["addopt_inputable"]) && in_array("Countable", class_implements($GLOBALS["addopt_inputable"]))) $TPL__addopt_inputable_1=$GLOBALS["addopt_inputable"]->count();else $TPL__addopt_inputable_1=0;
if (is_array($GLOBALS["optnm"])) $TPL__optnm_1=count($GLOBALS["optnm"]); else if (is_object($GLOBALS["optnm"]) && in_array("Countable", class_implements($GLOBALS["optnm"]))) $TPL__optnm_1=$GLOBALS["optnm"]->count();else $TPL__optnm_1=0;
if (is_array($GLOBALS["addopt"])) $TPL__addopt_1=count($GLOBALS["addopt"]); else if (is_object($GLOBALS["addopt"]) && in_array("Countable", class_implements($GLOBALS["addopt"]))) $TPL__addopt_1=$GLOBALS["addopt"]->count();else $TPL__addopt_1=0;?>
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<script src="/shop/data/skin/campingyo/common.js"></script>
<link rel="styleSheet" href="/shop/data/skin/campingyo/style.css">
</head>

<script type="text/javascript">
var price = new Array();
var reserve = new Array();
var consumer = new Array();
var memberdc = new Array();
var realprice = new Array();
var couponprice = new Array();
var coupon = new Array();
var cemoney = new Array();
var opt1img = new Array();
var opt2icon = new Array();
var opt2kind = "<?php echo $TPL_VAR["optkind"][ 1]?>";
var oldborder = "";
<?php if($TPL__opt_1){$TPL_I1=-1;foreach($GLOBALS["opt"] as $TPL_V1){$TPL_I1++;?><?php if((is_array($TPL_R2=$TPL_V1)&&!empty($TPL_R2)) || (is_object($TPL_R2) && in_array("Countable", class_implements($TPL_R2)) && $TPL_R2->count() > 0)) {$TPL_I2=-1;foreach($TPL_R2 as $TPL_V2){$TPL_I2++;?>
<?php if($TPL_I1== 0&&$TPL_I2== 0){?>
var fkey = '<?php echo addslashes($TPL_V2["opt1"])?><?php if($TPL_V2["opt2"]){?>|<?php echo addslashes($TPL_V2["opt2"])?><?php }?>';
<?php }?>
price['<?php echo addslashes($TPL_V2["opt1"])?><?php if($TPL_V2["opt2"]){?>|<?php echo addslashes($TPL_V2["opt2"])?><?php }?>'] = <?php echo $TPL_V2["price"]?>;
reserve['<?php echo addslashes($TPL_V2["opt1"])?><?php if($TPL_V2["opt2"]){?>|<?php echo addslashes($TPL_V2["opt2"])?><?php }?>'] = <?php echo $TPL_V2["reserve"]?>;
consumer['<?php echo addslashes($TPL_V2["opt1"])?><?php if($TPL_V2["opt2"]){?>|<?php echo addslashes($TPL_V2["opt2"])?><?php }?>'] = <?php echo $TPL_V2["consumer"]?>;
memberdc['<?php echo addslashes($TPL_V2["opt1"])?><?php if($TPL_V2["opt2"]){?>|<?php echo addslashes($TPL_V2["opt2"])?><?php }?>'] = <?php echo $TPL_V2["memberdc"]?>;
realprice['<?php echo addslashes($TPL_V2["opt1"])?><?php if($TPL_V2["opt2"]){?>|<?php echo addslashes($TPL_V2["opt2"])?><?php }?>'] = <?php echo $TPL_V2["realprice"]?>;
coupon['<?php echo addslashes($TPL_V2["opt1"])?><?php if($TPL_V2["opt2"]){?>|<?php echo addslashes($TPL_V2["opt2"])?><?php }?>'] = <?php echo $TPL_V2["coupon"]?>;
couponprice['<?php echo addslashes($TPL_V2["opt1"])?><?php if($TPL_V2["opt2"]){?>|<?php echo addslashes($TPL_V2["opt2"])?><?php }?>'] = <?php echo $TPL_V2["couponprice"]?>;
cemoney['<?php echo addslashes($TPL_V2["opt1"])?><?php if($TPL_V2["opt2"]){?>|<?php echo addslashes($TPL_V2["opt2"])?><?php }?>'] = <?php echo $TPL_V2["coupon_emoney"]?>;
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
opt['<?php echo $TPL_I1+ 1?>'] = new Array("('== 옵션선택 ==','')",<?php if((is_array($TPL_R2=$TPL_V1)&&!empty($TPL_R2)) || (is_object($TPL_R2) && in_array("Countable", class_implements($TPL_R2)) && $TPL_R2->count() > 0)) {$TPL_S2=count($TPL_R2);$TPL_I2=-1;foreach($TPL_R2 as $TPL_V2){$TPL_I2++;?>"('<?php echo $TPL_V2["opt2"]?><?php if($TPL_V2["price"]!=$TPL_VAR["price"]){?> (<?php echo number_format($TPL_V2["price"])?>원)<?php }?><?php if($TPL_VAR["usestock"]&&!$TPL_V2["stock"]){?> [품절]<?php }?>','<?php echo $TPL_V2["opt2"]?>','<?php if($TPL_VAR["usestock"]&&!$TPL_V2["stock"]){?>soldout<?php }?>','<?php if($TPL_V2["optno"]==$TPL_VAR["item"]["optno"]){?>selected<?php }?>')"<?php if($TPL_I2!=$TPL_S2- 1){?>,<?php }?><?php }}?>);
<?php }}?>
function subOption(obj)
{
	var el = document.getElementsByName('opt[]');
	var sub = opt[obj.selectedIndex];
	var aaaaaaaaaa = 0;
	if (typeof el[1] == 'undefined') return;
	while (el[1].length>0) el[1].options[el[1].options.length-1] = null;
	for (i=0;i<sub.length;i++){
		var div = sub[i].replace("')","").split("','");
		eval("el[1].options[i] = new Option" + sub[i]);
		if (div[2]=="soldout"){
			el[1].options[i].style.color = "#808080";
			el[1].options[i].setAttribute('disabled','disabled');
		}
		if (div[3]=="selected"){
			aaaaaaaaaa = i;
		}
	}
	if (aaaaaaaaaa > 0) el[1].selectedIndex = aaaaaaaaaa;
	else {
		el[1].selectedIndex = el[1].preSelIndex = 0;
	}

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
	var objImg = document.getElementById('objImg');
	if(opt1img[opt1]){
		objImg.src = (/^http(s)?:\/\//.test(opt1img[opt1])) ? opt1img[opt1] : "../data/goods/"+opt1img[opt1];
	}else{
		objImg.src = (/^http(s)?:\/\//.test('<?php echo $TPL_VAR["img_s"]?>')) ? '<?php echo $TPL_VAR["img_s"]?>' : "../data/goods/<?php echo $TPL_VAR["img_s"]?>";
	}
}

function chkOption(obj)
{
	if (!selectDisabled(obj)) return false;

	var opt = document.getElementsByName('opt[]');
	var opt1 = opt[0].value; var opt2 = '';
	if (typeof(opt[1])!="undefined") opt2 = "|" + opt[1].value;
	var key = opt1 + opt2;
	if (opt[0].selectedIndex == 0) key = fkey;
	key = key.replace('"','&quot;');
	if (typeof(price[key])!="undefined"){
		if (document.getElementById('price')) document.getElementById('price').innerHTML = comma(price[key]);
		if (document.getElementById('reserve')) document.getElementById('reserve').innerHTML = comma(reserve[key]);
		if (document.getElementById('consumer')) document.getElementById('consumer').innerHTML = comma(consumer[key]);
		if (document.getElementById('obj_realprice'))document.getElementById('obj_realprice').innerHTML = comma(realprice[key]) +'원&nbsp;(-'+comma(memberdc[key])+'원)';
		if (document.getElementById('obj_coupon'))document.getElementById('obj_coupon').innerHTML = comma( couponprice[key]) +'원&nbsp;(-'+comma(coupon[key])+'원)';
		if (document.getElementById('obj_coupon_emoney'))document.getElementById('obj_coupon_emoney').innerHTML = comma(cemoney[key]);
	}
}

function act(form)
{
	if(!(form.ea.value>0))
	{
		alert("구매수량은 1개 이상만 가능합니다");
		return false;
	}
	else {
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
}
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
</script>

<body style="margin:10px">

<img src="/shop/data/skin/campingyo/img/common/cart_option_edit_title.gif">


<form name="frmCartOption" method="post" action="<?php echo url("goods/goods_cart.php?")?>&cart_type=<?php echo $_GET["cart_type"]?>" onsubmit="return act(this);">
<input type="hidden" name="mode" value="editOption">
<input type="hidden" name="idx" value="<?php echo $_GET["idx"]?>">

<div style="width:100%;height:420px;border:1px solid #D0D0D0;padding:10px;overflow-y:auto;">

<table border="0">
<tr>
<td>
<?php if($TPL_VAR["todaygoods"]=='y'){?><a href="<?php echo url("todayshop/today_goods.php?")?>&tgsno=<?php echo $TPL_VAR["tgsno"]?>"><?php echo goodsimgTS($TPL_VAR["img_s"], 80,'id=objImg')?></a>
<?php }else{?><a href="<?php echo url("goods/goods_view.php?")?>&goodsno=<?php echo $TPL_VAR["goodsno"]?>"><?php echo goodsimg($TPL_VAR["img_s"], 80,'id=objImg')?></a>
<?php }?>
</td>
<td>
	<?php echo $TPL_VAR["goodsnm"]?>

</td>
</tr>
</table>

<div style="margin:15px 0 15px 0;overflow:hidden;height:1px;background:url(/shop/data/skin/campingyo/img/common/line2.gif) repeat-x top left;"></div>

<!-- 추가 옵션 입력형 -->
<?php if($TPL__addopt_inputable_1){$TPL_I1=-1;foreach($GLOBALS["addopt_inputable"] as $TPL_K1=>$TPL_V1){$TPL_I1++;?>
<div style="margin-bottom:10px;">
<span style="display:block;margin-bottom:5px;font-weight:bold;color:#565656;"><img src="/shop/data/skin/campingyo/img/common/btn_multioption_br.gif" align="absmiddle"> <?php echo $TPL_K1?></span>
<div>
	<input type="hidden" name="_addopt_inputable[]" value="">
	<input type="text" name="addopt_inputable[]" label="<?php echo $TPL_K1?>" option-value="<?php echo $TPL_V1["sno"]?>^<?php echo $TPL_K1?>^<?php echo $TPL_V1["opt"]?>^<?php echo $TPL_V1["addprice"]?>" value="<?php echo $TPL_V1["value"]?>" <?php if($GLOBALS["addopt_inputable_req"][$TPL_I1]){?>required fld_esssential<?php }?> maxlength="<?php echo $TPL_V1["opt"]?>">
</div>
</select>
</div>
<?php }}?>


<!-- 필수 옵션 일체형 -->
<?php if($GLOBALS["opt"]&&$GLOBALS["typeOption"]=="single"){?>
<div style="margin-bottom:10px;">
<span style="display:block;margin-bottom:5px;font-weight:bold;color:#565656;"><img src="/shop/data/skin/campingyo/img/common/btn_multioption_br.gif" align="absmiddle"> 옵션명 (<?php echo $TPL_VAR["optnm"]?>)</span>
<div><select style="width:100%" name="opt[]" onchange="chkOption(this);chkOptimg();" required msgR="<?php echo $TPL_VAR["optnm"]?> 선택을 해주세요">
	<option value="">== 옵션선택 ==
<?php if($TPL__opt_1){foreach($GLOBALS["opt"] as $TPL_V1){?><?php if((is_array($TPL_R2=$TPL_V1)&&!empty($TPL_R2)) || (is_object($TPL_R2) && in_array("Countable", class_implements($TPL_R2)) && $TPL_R2->count() > 0)) {foreach($TPL_R2 as $TPL_V2){?>
	<option value="<?php echo $TPL_V2["opt1"]?><?php if($TPL_V2["opt2"]){?>|<?php echo $TPL_V2["opt2"]?><?php }?>" <?php if($TPL_VAR["usestock"]&&!$TPL_V2["stock"]){?> disabled class=disabled<?php }?> <?php if($TPL_V2["optno"]==$TPL_VAR["item"]["optno"]){?>selected<?php }?>><?php echo $TPL_V2["opt1"]?><?php if($TPL_V2["opt2"]){?>/<?php echo $TPL_V2["opt2"]?><?php }?> <?php if($TPL_V2["price"]!=$TPL_VAR["price"]){?>(<?php echo number_format($TPL_V2["price"])?>원)<?php }?>
<?php if($TPL_VAR["usestock"]&&!$TPL_V2["stock"]){?> [품절]<?php }?>
<?php }}?><?php }}?>
	</select></div>
</select>
</div>
<?php }?>


<!-- 필수 옵션 분리형 -->
<?php if($GLOBALS["opt"]&&$GLOBALS["typeOption"]=="double"){?>
<?php if($TPL__optnm_1){$TPL_I1=-1;foreach($GLOBALS["optnm"] as $TPL_V1){$TPL_I1++;?>
	<div style="margin-bottom:10px;">
	<span style="display:block;margin-bottom:5px;font-weight:bold;color:#565656;"><img src="/shop/data/skin/campingyo/img/common/btn_multioption_br.gif" align="absmiddle"> 옵션명 <?php echo $TPL_I1+ 1?> (<?php echo $TPL_V1?>)</span>

	<!-- 옵션 선택 -->
<?php if(!$TPL_I1){?>
	<div>
	<select style="width:100%" name="opt[]" onchange="subOption(this);chkOptimg();selicon(this);" required msgR="<?php echo $TPL_V1?> 선택을 해주세요">
	<option value="">== 옵션선택 ==
<?php if((is_array($TPL_R2=($GLOBALS["opt"]))&&!empty($TPL_R2)) || (is_object($TPL_R2) && in_array("Countable", class_implements($TPL_R2)) && $TPL_R2->count() > 0)) {foreach($TPL_R2 as $TPL_K2=>$TPL_V2){?><option value="<?php echo $TPL_K2?>" <?php if($TPL_K2==$TPL_VAR["item"]["opt"][ 0]){?>selected<?php }?>><?php echo $TPL_K2?><?php }}?>
	</select>
	</div>
<?php }else{?>
	<select style="width:100%" name="opt[]" onchange="chkOption(this);selicon(this);" required msgR="<?php echo $TPL_V1?> 선택을 해주세요"><option value="">==선택==</select>
<?php }?>


	<!-- 옵션 이미지 아이콘 -->
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
	</div>
	<script>subOption(document.getElementsByName('opt[]')[0])</script>
<?php }}?>
<?php }?>



<!-- 추가 옵션 -->
<?php if($TPL__addopt_1){$TPL_I1=-1;foreach($GLOBALS["addopt"] as $TPL_K1=>$TPL_V1){$TPL_I1++;?>
<div style="margin-bottom:10px;">
<span style="display:block;margin-bottom:5px;font-weight:bold;color:#565656;"><img src="/shop/data/skin/campingyo/img/common/btn_multioption_br.gif" align="absmiddle"> <?php echo $TPL_K1?></span>
<div><select style="width:100%" name="addopt[]"<?php if($GLOBALS["addoptreq"][$TPL_I1]){?> required label="<?php echo $TPL_K1?>"<?php }?>>
	<option value="">==<?php echo $TPL_K1?> 선택==
<?php if(!$GLOBALS["addoptreq"][$TPL_I1]){?><option value="-1">선택안함<?php }?>
<?php if((is_array($TPL_R2=$TPL_V1)&&!empty($TPL_R2)) || (is_object($TPL_R2) && in_array("Countable", class_implements($TPL_R2)) && $TPL_R2->count() > 0)) {foreach($TPL_R2 as $TPL_V2){?>
	<option value="<?php echo $TPL_V2["sno"]?>^<?php echo $TPL_K1?>^<?php echo $TPL_V2["opt"]?>^<?php echo $TPL_V2["addprice"]?>" <?php if(in_array($TPL_V2["sno"],$TPL_VAR["item"]["addopt_sno"])){?>selected<?php }?>><?php echo $TPL_V2["opt"]?>

<?php if($TPL_V2["addprice"]){?>(<?php echo number_format($TPL_V2["addprice"])?>원 추가)<?php }?>
<?php }}?>
	</select></div>
</select>
</div>
<?php }}?>


<div style="margin:15px 0 5px 0;overflow:hidden;height:1px;background:url(/shop/data/skin/campingyo/img/common/line.gif) repeat-x top left;"></div>

<!-- 수량 -->
<div style="float:left;font-weight:bold;color:#565656;padding-top:6px;"><img src="/shop/data/skin/campingyo/img/common/btn_multioption_br.gif" align="absmiddle"> 수량</div>

<div style="float:left;margin-left:5px;"><input type=text name=ea step="<?php if($TPL_VAR["item"]["sales_unit"]){?><?php echo $TPL_VAR["item"]["sales_unit"]?><?php }else{?>1<?php }?>" min="<?php if($TPL_VAR["item"]["min_ea"]){?><?php echo $TPL_VAR["item"]["min_ea"]?><?php }else{?>1<?php }?>" max="<?php if($TPL_VAR["item"]["max_ea"]){?><?php echo $TPL_VAR["item"]["max_ea"]?><?php }else{?>0<?php }?>" size=2 value='<?php echo $TPL_VAR["item"]["ea"]?>' style="border:1px solid #D3D3D3;width:30px;text-align:right;height:20px" onblur="chg_cart_ea(this, 'set');"></div>
<div style="float:left;padding-left:3">
<div style="padding:1 0 2 0"><img src="/shop/data/skin/campingyo/img/common/btn_multioption_ea_up.gif" onClick="chg_cart_ea(frmCartOption.ea,'up')" style="cursor:pointer"></div>
<div><img src="/shop/data/skin/campingyo/img/common/btn_multioption_ea_down.gif" onClick="chg_cart_ea(frmCartOption.ea,'dn')" style="cursor:pointer"></div>
</div>

</div>

<div style="text-align:center;padding:10px;">
	<input type="image" src="/shop/data/skin/campingyo/img/common/btn_modify.gif">
	<a href="javascript:void(0);" onClick="self.close();"><img src="/shop/data/skin/campingyo/img/common/btn_cancel3.gif"></a>
</div>

</form>
</body>
</html>