<?php /* Template_ 2.2.7 2014/07/30 21:43:01 /www/francotr3287_godo_co_kr/shop/data/skin/freemart/mypage/_myCouponLayer.htm 000002948 */ 
if (is_array($TPL_VAR["mycouponinfo"])) $TPL_mycouponinfo_1=count($TPL_VAR["mycouponinfo"]); else if (is_object($TPL_VAR["mycouponinfo"]) && in_array("Countable", class_implements($TPL_VAR["mycouponinfo"]))) $TPL_mycouponinfo_1=$TPL_VAR["mycouponinfo"]->count();else $TPL_mycouponinfo_1=0;?>
<div id="MyCouponBox" style="z-index:9999;position:absolute;width:187px;height:220px;display:block;text-align:center;">

<div><img src="/shop/data/skin/freemart/img/common/title_coupon_popup.gif"></div>
<div style="padding:10px 25px 0 30px;width:296px;background:url(/shop/data/skin/freemart/img/common/bg_coupon_popup.gif) repeat-y top left;text-align:left;line-height:150%;font-size:12px;font-family:돋움; color:#464646; letter-spacing:-1; ">
<?php if($TPL_mycouponinfo_1){foreach($TPL_VAR["mycouponinfo"] as $TPL_K1=>$TPL_V1){?>
	<div>
<?php if($TPL_K1== 0){?>
		<strong><?php echo $TPL_V1["name"]?></strong> 회원님 앞으로
<?php }?>
		<div style="color:red; font-weight:bold;"><?php echo $TPL_V1["coupon"]?></div>[ <?php echo $TPL_V1["summa"]?> ] <br />이 발급되었습니다.
		<div style="height:4px"></div>
	</div>
<?php }}?>
</div>
<div style="background: url(/shop/data/skin/freemart/img/common/bottom_coupon_popup.gif); text-align:bottom; height:20px">
</div>

<div style="border-left:3px solid #DDDDDD;border-right:3px solid #DDDDDD;background:#ffffff;">
<a href="javascript:void(0);" onClick="document.getElementById('MyCouponBox').style.display='none'"><img src="/shop/data/skin/freemart/img/common/btn_coupon_check.gif"></a>
<a href="javascript:void(0);" onClick="go_couponlist()"><img src="/shop/data/skin/freemart/img/common/btn_coupon_list.gif"></a>
</div>


<div><img src="/shop/data/skin/freemart/img/common/foot01_member_level.gif"></div>
</div>

<script>
function go_couponlist(){
	document.getElementById('MyCouponBox').style.display='none';
	document.location.href = '../mypage/mypage_coupon.php';
	return false;
}

function fnMyCouponBoxPosition(w,h) {	// 가로, 세로
	var _doc_size = {
		width : document.body.scrollWidth || document.documentElement.scrollWidth,
		height: document.body.scrollHeight || document.documentElement.scrollHeight
	}


	var _win_size = {
		width : window.innerWidth	|| (window.document.documentElement.clientWidth	|| window.document.body.clientWidth),
		height: window.innerHeight	|| (window.document.documentElement.clientHeight|| window.document.body.clientHeight)
	}
	
	with (document.getElementById('MyCouponBox').style) {
		position = "absolute";
		width = w + 'px'; 
		height = h + 'px';
		zIndex = 9999;
		left = (_win_size.width + w) / 2 - w + 100  + 'px';
		top = ((_win_size.height + h) / 2 - h) + document.body.scrollTop -150 + 'px'
		display = "block";
	};
}

fnMyCouponBoxPosition(296, 200);
</script>