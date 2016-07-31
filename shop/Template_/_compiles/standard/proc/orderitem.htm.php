<?php /* Template_ 2.2.7 2015/07/01 09:55:51 /www/francotr3287_godo_co_kr/shop/data/skin/standard/proc/orderitem.htm 000008405 */ ?>
<style media="screen">
table.orderitem-list {width:100%;}
table.orderitem-list thead tr th {border-top:2px solid #303030;border-bottom:1px solid #d6d6d6;background:#f0f0f0;height:25px;}
table.orderitem-list tbody tr td {border-bottom:1px solid #d6d6d6;padding:3px;}
table.orderitem-list tbody tr td table td {border:none;}
table.orderitem-list tfoot tr td {border-bottom:1px solid #efefef;background:#f7f7f7;height:25px;text-align:right;}
table.orderitem-list tfoot tr td table td {border:none;}
</style>

<table cellpadding=0 cellspacing=0 border=0 class="orderitem-list">
<?php if($GLOBALS["orderitem_mode"]=="cart"){?><col width=30><?php }?><col width=60><col><col width=60><col width=80><col width=50><col width=80>
<thead>
<tr>
<?php if($GLOBALS["orderitem_mode"]=="cart"){?><th class="input_txt"><a href="javascript:void(0);" onClick="chkBox('idxs[]','rev');nsGodo_CartAction.recalc();">선택</a></th><?php }?>
	<th colspan=2 class="input_txt" width=200>상품정보</th>
	<th class="input_txt">적립금</th>
	<th class="input_txt">판매가</th>
	<th class="input_txt">수량</th>
<?php if($TPL_VAR["cart"]->special_discount_amount){?>
	<th class="input_txt">상품할인</th>
<?php }?>
<?php if($TPL_VAR["cart"]->dcprice){?>
	<th class="input_txt">회원할인</th>
<?php }?>
	<th class="input_txt">배송비</th>
	<th class="input_txt">합계</th>
</tr>
</thead>
<tbody>

<?php if((is_array($TPL_R1=$TPL_VAR["cart"]->item)&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {$TPL_I1=-1;foreach($TPL_R1 as $TPL_V1){$TPL_I1++;?>
<tr>
	<input type="hidden" name="adultpro[]" value="<?php echo $TPL_V1["use_only_adult"]?>">
<?php if($GLOBALS["orderitem_mode"]=="cart"){?>
	<td align=center><input type="checkbox" name="idxs[]" value="<?php echo $TPL_I1?>" checked onClick="nsGodo_CartAction.recalc();"></td>
<?php }?>
	<td height=60 align=center>
<?php if($TPL_V1["todaygoods"]=='y'){?><a href="<?php echo url("todayshop/today_goods.php?")?>&tgsno=<?php echo $TPL_V1["tgsno"]?>"><?php echo goodsimgTS($TPL_V1["img"], 40)?></a>
<?php }else{?><a href="<?php echo url("goods/goods_view.php?")?>&goodsno=<?php echo $TPL_V1["goodsno"]?>"><?php echo goodsimg($TPL_V1["img"], 40)?></a>
<?php }?>
	</td>
	<td>
	<div><?php echo $TPL_V1["goodsnm"]?></div>
	<div style="margin:5px 0 5px 0;overflow:hidden;height:1px;background:url(/shop/data/skin/standard/img/common/line2.gif) repeat-x top left;"></div>
<?php if($TPL_V1["opt"]){?>선택옵션 : [<?php echo implode("/",$TPL_V1["opt"])?>]<?php }?>
<?php if($TPL_V1["select_addopt"]){?>
	<br>추가옵션 : <?php if((is_array($TPL_R2=$TPL_V1["select_addopt"])&&!empty($TPL_R2)) || (is_object($TPL_R2) && in_array("Countable", class_implements($TPL_R2)) && $TPL_R2->count() > 0)) {foreach($TPL_R2 as $TPL_V2){?>[<?php echo $TPL_V2["optnm"]?>:<?php echo $TPL_V2["opt"]?>]<?php }}?>
<?php }?>
<?php if($TPL_V1["input_addopt"]){?>
	<br>입력옵션 : <?php if((is_array($TPL_R2=$TPL_V1["input_addopt"])&&!empty($TPL_R2)) || (is_object($TPL_R2) && in_array("Countable", class_implements($TPL_R2)) && $TPL_R2->count() > 0)) {foreach($TPL_R2 as $TPL_V2){?>[<?php echo $TPL_V2["optnm"]?>:<?php echo $TPL_V2["opt"]?>]<?php }}?>
<?php }?>
<?php if($GLOBALS["orderitem_mode"]=="cart"){?>
	<div style="margin:5px 0 5px 0"><a href="javascript:void(0);" onClick="nsGodo_CartAction.editOption('<?php echo $TPL_I1?>');"><img src="/shop/data/skin/standard/img/common/btn_check_modify.gif"></a></div>
<?php }?>
	</td>
	<td align=center><?php echo number_format($TPL_V1["reserve"])?>원</td>
	<td align=right style="padding-right:10"><?php echo number_format($TPL_V1["price"]+$TPL_V1["addprice"])?>원</td>
	<td align=center>
<?php if($GLOBALS["orderitem_mode"]=="cart"){?>
		<table cellpadding=0 cellspacing=0 border=0>
		<tr>
			<td><input type=text name=ea[] step="<?php if($TPL_V1["sales_unit"]){?><?php echo $TPL_V1["sales_unit"]?><?php }else{?>1<?php }?>" min="<?php if($TPL_V1["min_ea"]){?><?php echo $TPL_V1["min_ea"]?><?php }else{?>1<?php }?>" max="<?php if($TPL_V1["max_ea"]){?><?php echo $TPL_V1["max_ea"]?><?php }else{?>0<?php }?>" size=2 value="<?php echo $TPL_V1["ea"]?>" class=line style="text-align:right;" onkeydown="onlynumber()" onblur="chg_cart_ea(this,'set')"></td>
			<td><div style="padding-bottom:2px"><img src="/shop/data/skin/standard/img/common/btn_plus.gif" onClick="chg_cart_ea(frmCart['ea[]'],'up',<?php echo $TPL_I1?>)" style="cursor:pointer"></div><img src="/shop/data/skin/standard/img/common/btn_minus.gif" onClick="chg_cart_ea(frmCart['ea[]'],'dn',<?php echo $TPL_I1?>)" style="cursor:pointer"></td>
			<td><input type=image src="/shop/data/skin/standard/img/common/sbtn_mod.gif"></td>
		</tr>
		</table>
<?php }else{?>
		<?php echo $TPL_V1["ea"]?>개
<?php }?>
	</td>
<?php if($TPL_VAR["cart"]->special_discount_amount){?>
	<td>
	<div style="text-align:center;margin-top:3px"><?php echo number_format($TPL_V1["special_discount_amount"])?>원</div>
	</td>
<?php }?>
<?php if($TPL_V1["memberdc"]){?>
	<td align=center><?php echo number_format($TPL_V1["memberdc"])?>원</td>
<?php }?>
<?php if($TPL_VAR["orderitem_rowspan"][$TPL_I1]> 0){?>
	<td align=center rowspan="<?php echo $TPL_VAR["orderitem_rowspan"][$TPL_I1]?>">
<?php if($TPL_V1["delivery_type"]== 1){?>
		무료배송
<?php }elseif($TPL_V1["delivery_type"]== 2&&$TPL_V1["goods_delivery"]){?>
		개별배송
		<div>(<?php echo number_format($TPL_V1["goods_delivery"])?>원)</div>
<?php }elseif($TPL_V1["delivery_type"]== 3&&$TPL_V1["goods_delivery"]){?>
		착불배송
		<div>(<?php echo number_format($TPL_V1["goods_delivery"])?>원)</div>
<?php }elseif($TPL_V1["delivery_type"]== 4&&$TPL_V1["goods_delivery"]){?>
		고정배송
		<div>(<?php echo number_format($TPL_V1["goods_delivery"])?>원)</div>
<?php }elseif($TPL_V1["delivery_type"]== 5&&$TPL_V1["goods_delivery"]){?>
		수량별배송
		<div>(<?php echo number_format($TPL_V1["goods_delivery"]*$TPL_V1["ea"])?>원)</div>
<?php }else{?>
		<div id="el-default-delivery">
		기본배송
		</div>
<?php }?>
	</td>
<?php }?>

	<td align=right style="padding-right:10"><?php echo number_format(($TPL_V1["price"]+$TPL_V1["addprice"])*$TPL_V1["ea"])?>원</td>
</tr>
<?php if($GLOBALS["orderitem_mode"]=="cart"){?>
<script>nsGodo_CartAction.pushdata({reserve:<?php echo $TPL_V1["reserve"]?>,price:<?php echo ($TPL_V1["price"]+$TPL_V1["addprice"])?>,ea:<?php echo $TPL_V1["ea"]?>,special_discount_amount:<?php echo $TPL_V1["special_discount_amount"]?>,memberdc:<?php echo $TPL_V1["memberdc"]?>});</script>
<?php }?>
<?php }}?>
</tbody>

<tfoot>
<tr>
	<td colspan=10>

	<table style="display:block;float:right;">
	<tr>
		<td align=right width=80 nowrap>상품합계금액</td>
		<td align=right style="font-weight:bold;padding-left:25px"><span id="el-orderitem-total-price"><?php echo number_format($TPL_VAR["cart"]->goodsprice)?></span>원&nbsp;</td>
	</tr>
<?php if($TPL_VAR["cart"]->special_discount_amount||$TPL_VAR["cart"]->dcprice){?>
	<tr>
		<td align=right width=80 nowrap>할인금액</td>
		<td align=right style="font-weight:bold;padding-left:25px"><span id="el-orderitem-total-special_discount_amount"><?php echo number_format($TPL_VAR["cart"]->special_discount_amount+$TPL_VAR["cart"]->dcprice)?></span>원&nbsp;</td>
	</tr>
	<tr>
		<td align=right width=80 nowrap>할인적용금액</td>
		<td align=right style="font-weight:bold;padding-left:25px" class=red><span id="el-orderitem-total-result"><?php echo number_format($TPL_VAR["cart"]->goodsprice-($TPL_VAR["cart"]->special_discount_amount+$TPL_VAR["cart"]->dcprice))?></span>원&nbsp;</td>
	</tr>
<?php }?>
	<tr>
		<td align=right><!-- <?php if($GLOBALS["set"]["emoney"]["emoney_standard"]== 1){?> -->받으실예상적립금<!-- <?php }else{?> -->받으실적립금<!-- <?php }?> --></td>
		<td align=right style="font-weight:bold;padding-left:25px"><span id="el-orderitem-total-reserve"><?php echo number_format($TPL_VAR["cart"]->bonus)?></span>원&nbsp;</td>
	</tr>
<?php if($TPL_VAR["view_aboutdc"]){?>
	<tr>
		<td align=right>어바웃쿠폰</td>
		<td align=right style="font-weight:bold;padding-left:25px"><?php echo number_format($TPL_VAR["about_coupon"])?>원&nbsp;</td>
	</tr>
<?php }?>
	</table>

	</td>
</tr>
</tfoot>
</table>