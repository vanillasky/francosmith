<?php /* Template_ 2.2.7 2016/04/11 20:05:47 /www/francotr3287_godo_co_kr/shop/data/skin/freemart/mypage/mypage_coupon.htm 000003895 */ 
if (is_array($TPL_VAR["goods"])) $TPL_goods_1=count($TPL_VAR["goods"]); else if (is_object($TPL_VAR["goods"]) && in_array("Countable", class_implements($TPL_VAR["goods"]))) $TPL_goods_1=$TPL_VAR["goods"]->count();else $TPL_goods_1=0;?>
<?php $this->print_("header",$TPL_SCP,1);?>


<!-- 상단이미지 || 현재위치 -->
<div class="page_title_div">
	<div class="page_title">YOUR COUPONS</div>
	<div class="page_path"><a href="/shop/">HOME</a> &gt; <a href="/shop/mypage/mypage.php">마이페이지</a> &gt; <span class='bold'>할인쿠폰내역</span></div>
</div>
<div class="page_title_line"></div>



<div class="indiv"><!-- Start indiv -->

	<div style="height:20px"></div>

	<section class="tabs fullwidth">
	    <input id="tab-1" type="radio" name="radio-set" <?php if($GLOBALS["tab01"]=='on'){?>checked<?php }?> style="cursor:pointer" onclick="location.href='<?php echo url("mypage/mypage_coupon.php?")?>&tab=all'"/>
	    <label for="tab-1" class="tab-label-1" style="cursor:pointer" >전체 할인쿠폰</label>
	
	    <input id="tab-2" type="radio" name="radio-set" <?php if($GLOBALS["tab03"]=='on'){?>checked<?php }?>  style="cursor:pointer" onclick="location.href='<?php echo url("mypage/mypage_coupon.php?")?>&tab=wait'"/>
	    <label for="tab-2" class="tab-label-2" style="cursor:pointer">보유한 할인쿠폰</label>
	
	    <input id="tab-3" type="radio" name="radio-set" <?php if($GLOBALS["tab02"]=='on'){?>checked<?php }?>  style="cursor:pointer" onclick="location.href='<?php echo url("mypage/mypage_coupon.php?")?>&tab=used'" />
	    <label for="tab-3" class="tab-label-3" style="cursor:pointer">사용한 할인쿠폰</label>
	
	    <div class="clear-shadow"></div>
					
	    <div>
	        <div class="content-1">
	            <table class="mypage-board-table">
					<col width=170>
					<col>
					<col width=100 align=center>
					<col width=60 align=center>
					<col width=70 align=center>
					<col width=70 align=center>
					<tr class="mypage-board-title">
						<th>쿠폰</th>
						<th>적용상품</th>
						<th>사용일 및 기간</th>
						<th>기능</th>
						<th>할인/적립</th>
						<th>사용여부</th>
					</tr>
<?php if($TPL_goods_1){foreach($TPL_VAR["goods"] as $TPL_V1){?>
					<tr height=25 style="border-bottom:1px solid #ededed">
						<td><div style="text-overflow:ellipsis;overflow:hidden;width:200px;padding-left:10px;line-height:18px;" nowrap>[<?php echo $TPL_V1["coupon"]?>]</div>
							<div style="text-overflow:ellipsis;overflow:hidden;width:200px;padding-left:10px;line-height:18px;" nowrap><?php echo $TPL_V1["summa"]?></div>
						</td>
						<td><div style="text-overflow:ellipsis;overflow:hidden;width:200px;padding-left:10px;line-height:18px;" nowrap><?php if($TPL_V1["goodsnm"]){?><a href="<?php echo url("goods/goods_view.php?")?>&goodsno=<?php echo $TPL_V1["goodsno"]?>"><?php echo $TPL_V1["goodsnm"]?></a><?php }else{?> - <?php }?></div></td>
						<td><?php echo $TPL_V1["dataStr"]?></td>
						<td><?php echo $GLOBALS["r_couponAbility"][$TPL_V1["ability"]]?></td>
						<td><?php echo number_format($TPL_V1["price"])?><?php if(substr($TPL_V1["price"], - 1)!='%'){?>원<?php }else{?>%<?php }?></td>
						<td><?php if($TPL_V1["cnt"]=='미사용'){?><FONT COLOR="#007FC8"><?php echo $TPL_V1["cnt"]?></FONT><?php }else{?><?php echo $TPL_V1["cnt"]?><?php }?></td>
					</tr>
					
<?php }}?>
					</table>
	        </div>
	        
	    </div>
	</section>

	<div style="height:12px"></div>
	<div align="right" style="padding-top:5">
		<button class="button-dark button-big" onclick="popup('../mypage/paper_coupon.php?cnum=',350,200)">페이퍼 쿠폰번호 인증</button> 
	</div>
	<div style="height:30px"></div>
</div><!-- End indiv -->

<?php $this->print_("footer",$TPL_SCP,1);?>