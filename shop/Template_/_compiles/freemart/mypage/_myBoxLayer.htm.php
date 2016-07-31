<?php /* Template_ 2.2.7 2016/04/06 18:53:16 /www/francotr3287_godo_co_kr/shop/data/skin/freemart/mypage/_myBoxLayer.htm 000002080 */ ?>
<div id="MypageLayerBox" style="z-index:1000;position:absolute;border:1px solid #363636;background:#F6F6F6;width:187px;height:220px;display:none;text-align:center;">
	<div style="float:right;">
		<a href="javascript:void(0);" onClick="document.getElementById('MypageLayerBox').style.display='none';"><img src="/shop/data/skin/freemart/img/main/close.gif"></a>
	</div>

	<div style="clear:both;font-size:11px;margin:5px 0 3px 0;letter-spacing:-1px;">
	<?php echo $GLOBALS["member"]["name"]?> 님은 <?php if($GLOBALS["sess"]["grpnm_disp_type"]=='icon'){?><img src="../data/member/icon/<?php echo $GLOBALS["sess"]["grpnm_icon"]?>" align="absbottom"><?php }?> <font style="font-weight:bold;" color=#4B4B4B><?php echo $GLOBALS["sess"]["grpnm"]?></font> 입니다.
	</div>

	<div class="my-points">
		<dl>
			<dt class="small1">ㆍ적립금</dt>
			<dd class="small1"><font class=v71 color=#ff4810><?php echo number_format($GLOBALS["sess"]["emoney"])?></font> 원</dd>
			
			<dt class="small1">ㆍ할인쿠폰</dt>
			<dd class="small1"><font class=v71 color=#ff4810><?php echo number_format($GLOBALS["sess"]["cnt_coupon"])?></font> 원</dd>
		</dl>
	</div>

	<div class="my-cart">
		<dl>
			<dt class="small1">ㆍ장바구니</dt>
			<dd class="small1"><a href="<?php echo url("goods/goods_cart.php")?>&"><font class=v71 color=#2246F6><?php echo number_format($GLOBALS["sess"]["cart_count"])?></font></a> 개</dd>
			
			<dt class="small1">ㆍ위시리스트</dt>
			<dd class="small1"><a href="<?php echo url("mypage/mypage_wishlist.php")?>&"><font class=v71 color=#2246F6><?php echo number_format($GLOBALS["sess"]["wish_count"])?></font></a> 개</dd>
		</dl>
	
	</div>

	<div>
		<a href="<?php echo url("mypage/mypage.php")?>&"><img src="/shop/data/skin/freemart/img/main/btn_mypage_go.gif"></a>
		<!--<a href="<?php echo url("member/myinfo.php")?>&"><img src="/shop/data/skin/freemart/img/main/btn_mypage_go.gif"></a>-->
	</div>

</div>