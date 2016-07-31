<?php /* Template_ 2.2.7 2016/04/29 17:45:21 /www/francotr3287_godo_co_kr/shop/data/skin/freemart/proc/popup_coupon.htm 000007511 */ ?>
<script src="/shop/data/skin/freemart/common.js"></script>
<link rel="styleSheet" href="/shop/data/skin/freemart/style.css">

<script type="text/javascript">
var arCoupon = new Array();
<!--
<?php if((is_array($TPL_R1=($GLOBALS["loop"]))&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {$TPL_I1=-1;foreach($TPL_R1 as $TPL_V1){$TPL_I1++;?>
arCoupon[<?php echo $TPL_I1?>] = new Array('<?php echo $TPL_V1["apr"]?>','<?php echo $TPL_V1["ability"]?>','<?php echo $TPL_V1["sno"]?>','<?php echo $TPL_V1["pay_method"]?>');
<?php }}?>
var bank_osd = null;
function chk_settlekind(mod){
	var settlekind = opener.document.getElementsByName('settlekind');
	if(mod){
		for(var j=0;j<settlekind.length;j++){
			if(settlekind[j].value == 'a'){
				settlekind[j].disabled = false;
				settlekind[j].checked = true;
			}else{
				settlekind[j].disabled = true;
			}
		}
		prn_msg();
	}else{
		for(var j=0;j<settlekind.length;j++){
			settlekind[j].disabled = false;
		}
	}
}
function clear_msg(){
	var obj = document.getElementById('coupon_msg');
	obj.style.display = "none";

	bank_osd = null;
}
function prn_msg(){

	var obj = document.getElementById('coupon_msg');

	obj.style.display = "block";

	clearTimeout(bank_osd);
	bank_osd = setTimeout("clear_msg()",3000);

}
function calcuCoupon(obj)
{
	var chk = document.getElementsByName(obj.name);
	var apply_coupon = opener.document.getElementById('apply_coupon');
	var coupon_typinfo = opener.document.getElementById('coupon_typinfo');
	var del_coupon = opener.document.getElementById('del_coupon');
	var emoney = opener.document.getElementById('emoney');
	var coupon_price = 0; var coupon_emoney = 0; var sno='';
	var dc = 0; var abi = 0;
	var chkCash = false;

	// 중복 사용 체크
	var dup = <?php if($GLOBALS["set"]["emoney"]["useduplicate"]=='1'){?>true<?php }else{?>false<?php }?>;
	if (parseInt(emoney.value) > 0 && !dup)
	{
		alert('적립금과 쿠폰 사용이 중복적용되지 않습니다.');
		obj.checked = false;
		return false;
	}
	apply_coupon.innerHTML = '';
	for(i=0;i<chk.length;i++){
		if(chk[i].checked){
			if(arCoupon[i][3] == 'cash' || arCoupon[i][3] == '1') {
				chkCash = true;
				coupon_typinfo.style.display = "block";
			}
			dc = arCoupon[i][0];
			abi = arCoupon[i][1];
			sno = arCoupon[i][2];
			if(abi == 0)coupon_price += parseInt(dc);
			else coupon_emoney += parseInt(dc);
			apply_coupon.innerHTML += "<input type='hidden' name='apply_coupon[]' value='"+ sno +"'>";
		}
	}
	chk_settlekind(chkCash);
	document.getElementById('coupon_price').innerHTML = comma(coupon_price);
	document.getElementById('coupon_emoney').innerHTML = comma(coupon_emoney);

	if((coupon_price || coupon_emoney) && '2' == '<?php echo $GLOBALS["cfgCoupon"]["range"]?>')opener.document.getElementById('memberdc').innerHTML = 0;

	opener.document.frmOrder.coupon.value = comma(coupon_price);
	opener.document.frmOrder.coupon_emoney.value = comma(coupon_emoney);
	opener.chk_emoney(opener.document.frmOrder.emoney);
	opener.getDelivery();
	opener.calcu_settle();

	del_coupon.style.visibility = "";
}

function view_goods(idx,max){
	var obj = document.getElementById('goodsnm_'+idx);
	if(obj.style.display=="block") obj.style.display="none";
	else  obj.style.display="block";
	for(var i=0;i<max;i++){
		if(i != idx) document.getElementById('goodsnm_'+i).style.display="none";
	}
}

function fnCheckOrderPage(){

	if (typeof opener != 'object') self.close();

	if (typeof window.attachEvent != "undefined") {
		opener.window.attachEvent("onunload", function(){
			self.close();
		});
	}
	else {
		opener.window.addEventListener("unload", function(){
			self.close();
		}, false);
	}
}
-->
</script>
<style>
.applyGoods { display:none;position:relative }
.applyGoods div { position:absolute;left:-180;background-color:#ffffff;width:400px;border:3px solid #000000;padding:5px 5px 5px 5px }
.msg { display:none;position:relative }
.msg div { position:absolute;left:-180;top:-50;background-color:#ffffff;width:400px;border:3px solid #000000;padding:5px 5px 5px 5px }
</style>
<body bgcolor="#000000" style="padding:10px;"  onLoad="fnCheckOrderPage();">

<table height=100% width=100% cellpadding=0 cellspacing=0 border=0 bgcolor="#FFFFFF">
<tr>
	<td style="background:#000000; border-bottom:2px solid #DDDDDD"><img src="/shop/data/skin/freemart/img/common/popup_title_coupon.gif"></td>
</tr>
<tr>
	<td  align=center valign=top>
	<div class="msg" id="coupon_msg">
	<div>무통장 입금 사용시 가능한 쿠폰을 선택하였습니다.<br/>결제수단은 무통장입금만 이용하실 수 있습니다.</div>
	</div>
	<div style="height:15; font-size:0pt"></div>
	<div style="float:right;padding-right:15px;"><b>쿠폰 할인액 : <span id=coupon_price>0</span>원</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>쿠폰 적립액 : <span id=coupon_emoney>0</span>원</b></div>
	<table width=95% border=2 bordercolor=#D6D6D6 frame="hsides" rules="rows" style="border-collapse:collapse;" cellpadding=4 id=tb>
	<col width=30 align=center><col width=100><col width=150 align=center><col width=110 align=center><col width=50 align=center><col width=100 align=center>
	<tr bgcolor=#F0F0F0 height=23 class=input_txt>
		<th>선택</th>
		<th>쿠폰</th>
		<th>적용상품</th>
		<th>종료일</th>
		<th>기능</th>
		<th>할인/적립</th>
	</tr>
<?php if((is_array($TPL_R1=($GLOBALS["loop"]))&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {$TPL_S1=count($TPL_R1);$TPL_I1=-1;foreach($TPL_R1 as $TPL_V1){$TPL_I1++;?>
	<tr height=40>
		<td>
<?php if($GLOBALS["cfgCoupon"]["double"]){?>
		<input type="checkbox" name='coupon[]' onclick="calcuCoupon(this)" value='<?php echo $TPL_V1["couponcd"]?>'>
<?php }else{?>
		<input type="radio" name='coupon[]' onclick="calcuCoupon(this)" value='<?php echo $TPL_V1["couponcd"]?>'>
<?php }?>
		</td>
		<td><?php echo $TPL_V1["coupon"]?><?php if($TPL_V1["payMethod"]== 1){?><div class="small red">무통장 입금으로 결제시에만 적용됩니다.</div><?php }?></td>
		<td>
<?php if($TPL_V1["goodsnms"]){?><div><button onclick="view_goods(<?php echo $TPL_I1?>,<?php echo $TPL_S1?>);">적용상품보기</button></div><?php }else{?>-<?php }?>
		<div class="applyGoods" id="goodsnm_<?php echo $TPL_I1?>">
		<div><!-- <?php if((is_array($TPL_R2=$TPL_V1["goodsnms"])&&!empty($TPL_R2)) || (is_object($TPL_R2) && in_array("Countable", class_implements($TPL_R2)) && $TPL_R2->count() > 0)) {foreach($TPL_R2 as $TPL_V2){?> --><?php echo $TPL_V2?><br/><!-- <?php }}?> --></div>
		</div>
		</td>
		<td><?php if($TPL_V1["priodtype"]== 1){?> 발급 후 <?php echo $TPL_V1["sdate"]?><?php }else{?><?php echo substr($TPL_V1["edate"], 0, 10)?><?php }?></td>
		<td><?php echo $GLOBALS["r_couponAbility"][$TPL_V1["ability"]]?></td>
		<td>
<?php if(substr($TPL_V1["price"], - 1)!='%'){?>
		<?php echo number_format($TPL_V1["price"])?>원
<?php }else{?>
		<?php echo number_format($TPL_V1["price"])?>%
<?php }?>
		<br/>
		(<?php echo number_format($TPL_V1["apr"])?>원)
		</td>
	</tr>
<?php }}?>
	</table>
	<div style="padding:10px" align=center><a href="javascript:window.close()"><img src="/shop/data/skin/freemart/img/common/btn_confirm_red.gif"></a></div>
	</td>
</tr>


<tr>
	<td valign=bottom align=right style="padding:0 14 6 0;"><A HREF="javascript:this.close()" onFocus="blur()"><img src="/shop/data/skin/freemart/img/common/popup_close.gif"></A></td>
</tr>
</table>
</body>
</html>