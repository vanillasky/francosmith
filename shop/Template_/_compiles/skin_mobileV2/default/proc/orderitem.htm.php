<?php /* Template_ 2.2.7 2013/10/15 11:30:18 /www/francotr3287_godo_co_kr/shop/data/skin_mobileV2/default/proc/orderitem.htm 000007961 */ ?>
<style type="text/css">

.order_item_list .goods-list-item {min-height:130px;padding:12px;}
.order_item_list .goods-list-item-gray {background:#f5f4f5;}
.order_item_list .goods-list-item .goods-list-chk{width:20px; height:100px; float:left; margin-top:15px; line-height:100px;}
.order_item_list .goods-list-item .goods-list-chk input[type=checkbox]{width:10px;}
.order_item_list .goods-list-item .goods-list-img{width:100px; height:100px; border:solid 1px #dbdbdb; float:left; margin-top:15px;}
.order_item_list .goods-list-item .goods-list-img img{width:100%; height:100%;}
.order_item_list .goods-list-item .goods-list-info{float:left; margin-left:10px;width:53%;}
.order_item_list .goods-list-item .goods-list-info .goods-nm{color:#353535; font-weight:bold; fonst-size:14px; margin-bottom:5px; overflow:hidden;text-overflow:ellipsis;white-space:nowrap; }
.order_item_list .goods-list-item .goods-list-info .goods-option{color:#666666; font-size:12px; margin-bottom:2px;}
.order_item_list .goods-list-item .goods-list-info .goods-price{color:#666666; font-size:12px; margin-bottom:2px;}
.order_item_list .goods-list-item .goods-list-info .goods-price .red{color:#f03c3c; font-size:14px; font-weight:bold;}
.order_item_list .goods-list-item .goods-list-info .goods-dc{color:#666666; font-size:12px; margin-bottom:2px;}
.order_item_list .goods-list-item .goods-list-info .goods-dc .blue{color:#436693; font-size:12px; font-weight:bold;}
.order_item_list .goods-list-item .goods-list-info .goods-reserve{color:#666666; font-size:12px; margin-bottom:2px;}
.order_item_list .goods-list-item .goods-list-info .goods-delivery{color:#666666; font-size:12px; margin-bottom:2px;}
.order_item_list .goods-list-item .goods-list-info .goods-ea{color:#666666; font-size:12px; margin-bottom:2px;}
.order_item_list .goods-list-item .goods-list-info .goods-nvmileage{color:#666666; font-size:12px; margin-bottom:2px;}
.order_item_list .goods-list-item .goods-list-arrow{width:15px; height:21px; background:url("/shop/data/skin_mobileV2/default/common/img/nlist/arrow01.png") no-repeat; float:right; margin-top:40px;}

.order_item_list .goods-list-item .option-modify-btn{width:31px; height:21px; background:url("/shop/data/skin_mobileV2/default/common/img/cart/btn_modify_off.png") no-repeat; float:right; border:none; color:#ffffff;}
.order_item_list .goods-list-item .option-modify-btn:active {background:url("/shop/data/skin_mobileV2/default/common/img/cart/btn_modify_on.png") no-repeat;}
.order_item_list .goods-list-item:after {visibility:hidden;display:block;content:" ";clear:both;height:0;}
</style>
<div class="order_item_list">
<?php if((is_array($TPL_R1=$TPL_VAR["cart"]->item)&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {$TPL_I1=-1;foreach($TPL_R1 as $TPL_K1=>$TPL_V1){$TPL_I1++;?>

<div class="goods-list-item <?php if($TPL_I1% 2== 1){?>goods-list-item-gray<?php }?>">
	<div class="goods-list-chk"><?php if($GLOBALS["orderitem_mode"]=="cart"){?><input type=checkbox name=idxs[] value="<?php echo $TPL_I1?>"  onClick="nsGodo_CartAction.recalc();"/><?php }?></div>
	<div class="goods-list-img"><a href="../goods/view.php?goodsno=<?php echo $TPL_V1["goodsno"]?>"><?php echo goodsimgMobile($TPL_V1["img"], 100)?></a></div>
	<div class="goods-list-info">
		<div class="goods-nm"><a href="../goods/view.php?goodsno=<?php echo $TPL_V1["goodsno"]?>"><?php echo strcut($TPL_V1["goodsnm"], 100)?></a></div>
<?php if($GLOBALS["orderitem_mode"]=="cart"){?>
		<div class="goods-option-modify">
			<button class="option-modify-btn" data-id="<?php echo $TPL_K1?>" data-goodsno="<?php echo $TPL_V1["goodsno"]?>" data-option1="<?php echo $TPL_V1["opt"][ 0]?>" data-option2="<?php echo $TPL_V1["opt"][ 1]?>" data-ea="<?php echo $TPL_V1["ea"]?>" data-addsno="<?php if((is_array($TPL_R2=$TPL_V1["addopt"])&&!empty($TPL_R2)) || (is_object($TPL_R2) && in_array("Countable", class_implements($TPL_R2)) && $TPL_R2->count() > 0)) {foreach($TPL_R2 as $TPL_V2){?><?php echo $TPL_V2["sno"]?>|<?php }}?>" type="button"></button>
		</div>
<?php }?>
		<div class="goods-option">
<?php if($TPL_V1["opt"]){?><div>선택옵션 : [<?php echo implode("/",$TPL_V1["opt"])?>]</div><?php }?>
<?php if($TPL_V1["select_addopt"]){?>
<?php if((is_array($TPL_R2=$TPL_V1["select_addopt"])&&!empty($TPL_R2)) || (is_object($TPL_R2) && in_array("Countable", class_implements($TPL_R2)) && $TPL_R2->count() > 0)) {foreach($TPL_R2 as $TPL_V2){?><div>추가옵션 : [<?php echo $TPL_V2["optnm"]?>:<?php echo $TPL_V2["opt"]?>]</div><?php }}?>
<?php }?>
<?php if($TPL_V1["input_addopt"]){?>
<?php if((is_array($TPL_R2=$TPL_V1["input_addopt"])&&!empty($TPL_R2)) || (is_object($TPL_R2) && in_array("Countable", class_implements($TPL_R2)) && $TPL_R2->count() > 0)) {foreach($TPL_R2 as $TPL_V2){?><div>입력옵션 : [<?php echo $TPL_V2["optnm"]?>:<?php echo $TPL_V2["opt"]?>]</div><?php }}?>
<?php }?>
		</div>
		<div class="goods-price">상품가격 : <span class="red"><?php echo number_format($TPL_V1["price"]+$TPL_V1["addprice"])?>원</span></div>
<?php if($TPL_V1["goods_discount_price"]){?>
		<div class="goods-dc">모바일할인 : <span class="blue"><?php echo number_format($TPL_V1["goods_discount_price"])?>원</span></div>
<?php }?>
<?php if($TPL_V1["reserve"]){?>
		<div class="goods-reserve">적립금 :<?php echo number_format($TPL_V1["reserve"])?>원</div>
<?php }?>
<?php if($TPL_V1["delivery_type"]){?>
		<div class="goods-delivery">배송비 :
<?php if($TPL_V1["delivery_type"]== 1){?>
			(무료배송)
<?php }elseif(($TPL_V1["delivery_type"]== 2||$TPL_V1["delivery_type"]== 4)&&$TPL_V1["goods_delivery"]> 0){?>
			(개별배송 : <?php echo number_format($TPL_V1["goods_delivery"])?>원 )
<?php }elseif($TPL_V1["delivery_type"]== 5&&$TPL_V1["goods_delivery"]> 0){?>
			(수량별배송 : <?php echo number_format($TPL_V1["goods_delivery"]*$TPL_V1["ea"])?>원 )
<?php }elseif($TPL_V1["delivery_type"]== 3&&$TPL_V1["goods_delivery"]> 0){?>
			<div>(착불배송 : <?php echo number_format($TPL_V1["goods_delivery"])?>원 )</div>
<?php }?>
		</div>
<?php }?>
		<div class="goods-ea">수량 : <?php echo $TPL_V1["ea"]?>개</div>
<?php if($TPL_V1["NaverMileageAccum"]){?><div class="goods-nvmileage" data-goodsno="<?php echo $TPL_V1["goodsno"]?>"></div><?php }?>
		<div class="goods-price">합계 : <span class="red"><?php echo number_format((($TPL_V1["price"]+$TPL_V1["addprice"])*$TPL_V1["ea"])-($TPL_V1["goods_discount_price"]*$TPL_V1["ea"]))?>원</span></div>
	</div>

</div>
<?php if($GLOBALS["orderitem_mode"]=="cart"){?>
	<script>nsGodo_CartAction.pushdata({reserve:<?php echo $TPL_V1["reserve"]?>,price:<?php echo (($TPL_V1["price"]-$TPL_V1["goods_discount_price"])+$TPL_V1["addprice"])?>,ea:<?php echo $TPL_V1["ea"]?>});</script>
<?php }?>
<?php }}?>
</div>


<?php echo $TPL_VAR["NaverMileageAccum"]?>

<?php if($GLOBALS["orderitem_mode"]=="cart"){?>
<div class="btn_area">
	<div id="all-chk-btn"><div id="all-chk-btn-effect" onclick="javascript:chkBox('idxs[]','rev');nsGodo_CartAction.recalc();" >전체 선택</div></div>
	<div id="chk-del-btn"><div id="chk-del-btn-effect" onclick="javascript:act('delItem');" id="all-chk-btn">선택삭제</div></div>
	<div id="all-del-btn"><div id="all-del-btn-effect" onclick="javascript:location.href='cart.php?mode=empty';" id="all-chk-btn">비우기</div></div>
</div>
<?php }?>

<div class="sum_area">
	<div class="board_area">
		<div class="title">
			<div>상품 합계금액 : </div>
			<div>받으실 적립금 : </div>
		</div>
		<div class="price">
			<div id="el-orderitem-total-price"><?php echo number_format($TPL_VAR["cart"]->goodsprice)?>원</div>
			<div id="el-orderitem-total-reserve"><?php echo number_format($TPL_VAR["cart"]->bonus)?>원</div>
		</div>
	</div>
	<div class="clearb"></div>
</div>