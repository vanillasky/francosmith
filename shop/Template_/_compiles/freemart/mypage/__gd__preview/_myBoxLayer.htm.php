<?php /* Template_ 2.2.7 2014/10/12 13:26:43 /www/francotr3287_godo_co_kr/shop/data/skin/freemart/mypage/_myBoxLayer.htm 000006261 */ ?>
<!-- gdpart mode="open" fid="goods/goods_view.htm tpl_2" --><!-- gdline 1"/mypage/_myBoxLayer.htm|/mypage/_myBoxLayer.htm|goods/goods_view.htm tpl_2" --><!-- gdline 2"/mypage/_myBoxLayer.htm|/mypage/_myBoxLayer.htm|goods/goods_view.htm tpl_2" -->
<!-- gdline 3"/mypage/_myBoxLayer.htm|/mypage/_myBoxLayer.htm|goods/goods_view.htm tpl_2" --><div id="MypageLayerBox" style="z-index:1000;position:absolute;border:1px solid #363636;background:#F6F6F6;width:187px;height:220px;display:none;text-align:center;">
<!-- gdline 4"/mypage/_myBoxLayer.htm|/mypage/_myBoxLayer.htm|goods/goods_view.htm tpl_2" -->
<!-- gdline 5"/mypage/_myBoxLayer.htm|/mypage/_myBoxLayer.htm|goods/goods_view.htm tpl_2" -->	<div style="float:right;">
<!-- gdline 6"/mypage/_myBoxLayer.htm|/mypage/_myBoxLayer.htm|goods/goods_view.htm tpl_2" -->		<a href="javascript:void(0);" onClick="document.getElementById('MypageLayerBox').style.display='none';"><img src="/shop/data/skin/freemart/img/main/close.gif"></a>
<!-- gdline 7"/mypage/_myBoxLayer.htm|/mypage/_myBoxLayer.htm|goods/goods_view.htm tpl_2" -->	</div>
<!-- gdline 8"/mypage/_myBoxLayer.htm|/mypage/_myBoxLayer.htm|goods/goods_view.htm tpl_2" -->
<!-- gdline 9"/mypage/_myBoxLayer.htm|/mypage/_myBoxLayer.htm|goods/goods_view.htm tpl_2" -->	<div style="clear:both;font-size:11px;margin:5px 0 3px 0;letter-spacing:-1px;">
<!-- gdline 10"/mypage/_myBoxLayer.htm|/mypage/_myBoxLayer.htm|goods/goods_view.htm tpl_2" -->	<?php echo $GLOBALS["member"]["name"]?> 님은 <?php if($GLOBALS["sess"]["grpnm_disp_type"]=='icon'){?><img src="../data/member/icon/<?php echo $GLOBALS["sess"]["grpnm_icon"]?>" align="absbottom"><?php }?> <font style="font-weight:bold;" color=#4B4B4B><?php echo $GLOBALS["sess"]["grpnm"]?></font> 입니다.
<!-- gdline 11"/mypage/_myBoxLayer.htm|/mypage/_myBoxLayer.htm|goods/goods_view.htm tpl_2" -->	</div>
<!-- gdline 12"/mypage/_myBoxLayer.htm|/mypage/_myBoxLayer.htm|goods/goods_view.htm tpl_2" -->
<!-- gdline 13"/mypage/_myBoxLayer.htm|/mypage/_myBoxLayer.htm|goods/goods_view.htm tpl_2" -->	<div style="width:170px;background:#ffffff;border:1px solid #E6E6E6;padding:8px;">
<!-- gdline 14"/mypage/_myBoxLayer.htm|/mypage/_myBoxLayer.htm|goods/goods_view.htm tpl_2" -->		<table width="100%">
<!-- gdline 15"/mypage/_myBoxLayer.htm|/mypage/_myBoxLayer.htm|goods/goods_view.htm tpl_2" -->		<!--
		<tr>
			<td class="small1" width="60">ㆍ총구매액</td><td class="small1" align="right"><font class=v71 color=#ff4810><?php echo number_format($GLOBALS["sess"]["sum_sale"])?></font> 원</td>
		</tr>
		-->
<!-- gdline 20"/mypage/_myBoxLayer.htm|/mypage/_myBoxLayer.htm|goods/goods_view.htm tpl_2" -->		<tr>
<!-- gdline 21"/mypage/_myBoxLayer.htm|/mypage/_myBoxLayer.htm|goods/goods_view.htm tpl_2" -->			<td class="small1">ㆍ적립금</td><td class="small1" align="right"><font class=v71 color=#ff4810><?php echo number_format($GLOBALS["sess"]["emoney"])?></font> 원</td>
<!-- gdline 22"/mypage/_myBoxLayer.htm|/mypage/_myBoxLayer.htm|goods/goods_view.htm tpl_2" -->		</tr>
<!-- gdline 23"/mypage/_myBoxLayer.htm|/mypage/_myBoxLayer.htm|goods/goods_view.htm tpl_2" -->		<tr>
<!-- gdline 24"/mypage/_myBoxLayer.htm|/mypage/_myBoxLayer.htm|goods/goods_view.htm tpl_2" -->			<td class="small1">ㆍ할인쿠폰</td><td class="small1" align="right"><font class=v71 color=#ff4810><?php echo number_format($GLOBALS["sess"]["cnt_coupon"])?></font> 원</td>
<!-- gdline 25"/mypage/_myBoxLayer.htm|/mypage/_myBoxLayer.htm|goods/goods_view.htm tpl_2" -->		</tr>
<!-- gdline 26"/mypage/_myBoxLayer.htm|/mypage/_myBoxLayer.htm|goods/goods_view.htm tpl_2" -->		</table>
<!-- gdline 27"/mypage/_myBoxLayer.htm|/mypage/_myBoxLayer.htm|goods/goods_view.htm tpl_2" -->	</div>
<!-- gdline 28"/mypage/_myBoxLayer.htm|/mypage/_myBoxLayer.htm|goods/goods_view.htm tpl_2" -->
<!-- gdline 29"/mypage/_myBoxLayer.htm|/mypage/_myBoxLayer.htm|goods/goods_view.htm tpl_2" -->	<div style="width:170px;padding:8px;">
<!-- gdline 30"/mypage/_myBoxLayer.htm|/mypage/_myBoxLayer.htm|goods/goods_view.htm tpl_2" -->		<table width="100%">
<!-- gdline 31"/mypage/_myBoxLayer.htm|/mypage/_myBoxLayer.htm|goods/goods_view.htm tpl_2" -->		<tr>
<!-- gdline 32"/mypage/_myBoxLayer.htm|/mypage/_myBoxLayer.htm|goods/goods_view.htm tpl_2" -->			<td class="small1" width="60">ㆍ장바구니</td><td class="small1" align="right"><a href="<?php echo url("goods/goods_cart.php")?>&"><font class=v71 color=#2246F6><?php echo number_format($GLOBALS["sess"]["cart_count"])?></font></a> 개</td>
<!-- gdline 33"/mypage/_myBoxLayer.htm|/mypage/_myBoxLayer.htm|goods/goods_view.htm tpl_2" -->		</tr>
<!-- gdline 34"/mypage/_myBoxLayer.htm|/mypage/_myBoxLayer.htm|goods/goods_view.htm tpl_2" -->		<tr>
<!-- gdline 35"/mypage/_myBoxLayer.htm|/mypage/_myBoxLayer.htm|goods/goods_view.htm tpl_2" -->			<td class="small1">ㆍ위시리스트</td><td class="small1" align="right"><a href="<?php echo url("mypage/mypage_wishlist.php")?>&"><font class=v71 color=#2246F6><?php echo number_format($GLOBALS["sess"]["wish_count"])?></font></a> 개</td>
<!-- gdline 36"/mypage/_myBoxLayer.htm|/mypage/_myBoxLayer.htm|goods/goods_view.htm tpl_2" -->		</tr>
<!-- gdline 37"/mypage/_myBoxLayer.htm|/mypage/_myBoxLayer.htm|goods/goods_view.htm tpl_2" -->		</table>
<!-- gdline 38"/mypage/_myBoxLayer.htm|/mypage/_myBoxLayer.htm|goods/goods_view.htm tpl_2" -->	</div>
<!-- gdline 39"/mypage/_myBoxLayer.htm|/mypage/_myBoxLayer.htm|goods/goods_view.htm tpl_2" -->
<!-- gdline 40"/mypage/_myBoxLayer.htm|/mypage/_myBoxLayer.htm|goods/goods_view.htm tpl_2" -->	<div>
<!-- gdline 41"/mypage/_myBoxLayer.htm|/mypage/_myBoxLayer.htm|goods/goods_view.htm tpl_2" -->		<a href="<?php echo url("member/myinfo.php")?>&"><img src="/shop/data/skin/freemart/img/main/btn_mypage_go.gif"></a>
<!-- gdline 42"/mypage/_myBoxLayer.htm|/mypage/_myBoxLayer.htm|goods/goods_view.htm tpl_2" -->	</div>
<!-- gdline 43"/mypage/_myBoxLayer.htm|/mypage/_myBoxLayer.htm|goods/goods_view.htm tpl_2" -->
<!-- gdline 44"/mypage/_myBoxLayer.htm|/mypage/_myBoxLayer.htm|goods/goods_view.htm tpl_2" --></div><!-- gdpart mode="close" fid="goods/goods_view.htm tpl_2" -->