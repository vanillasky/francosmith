<?php /* Template_ 2.2.7 2013/08/07 14:16:20 /www/francotr3287_godo_co_kr/shop/data/skin_mobileV2/default/myp/wishlist.htm 000006428 */ 
if (is_array($TPL_VAR["loop"])) $TPL_loop_1=count($TPL_VAR["loop"]); else if (is_object($TPL_VAR["loop"]) && in_array("Countable", class_implements($TPL_VAR["loop"]))) $TPL_loop_1=$TPL_VAR["loop"]->count();else $TPL_loop_1=0;?>
<?php $this->print_("header",$TPL_SCP,1);?>

<?php  $TPL_VAR["page_title"] = "찜목록";?>
<?php $this->print_("sub_header",$TPL_SCP,1);?>


<script>
function act(mode)
{
	var fm = document.frmWish;
	if (isChked('sno[]')){

		if (mode == 'cart') {
			var runout = document.getElementsByName('runout[]');
			var chks = document.getElementsByName('sno[]');

			for (var i=0,m=chks.length;i<m ;i++) {
				if (chks[i].checked == true) {
					if (runout[i].value == 'T') {
						alert("품절 상품은 장바구니에 담을 수 없습니다");
						return;
					}
				}
			}
		}
		fm.mode.value = mode;
		fm.submit();
	}
}
</script>

<style type="text/css">
section#wishlist { background:#FFFFFF; }
.goods-area .goods-list-item {height:100px;padding:12px;}
.goods-area .goods-list-item-gray {background:#f5f4f5;}
.goods-area .goods-list-item .goods-list-chk{width:20px; height:100px; float:left; line-height:100px;}
.goods-area .goods-list-item .goods-list-chk input[type=checkbox]{width:10px;}
.goods-area .goods-list-item .goods-list-img{width:100px; height:100px; border:solid 1px #dbdbdb; float:left; }
.goods-area .goods-list-item .goods-list-img img{width:100%; height:100%;}
.goods-area .goods-list-item .goods-list-info{float:left; margin-left:10px;max-width:53%;}
.goods-area .goods-list-item .goods-list-info .goods-nm{color:#353535; font-weight:bold; fonst-size:14px; margin-bottom:5px; overflow:hidden;text-overflow:ellipsis;white-space:nowrap; }
.goods-area .goods-list-item .goods-list-info .goods-option{color:#666666; font-size:12px; margin-bottom:2px;}
.goods-area .goods-list-item .goods-list-info .goods-price{color:#666666; font-size:12px; margin-bottom:2px;}
.goods-area .goods-list-item .goods-list-info .goods-price .red{color:#f03c3c; font-size:14px; font-weight:bold;}
.goods-area .goods-list-item .goods-list-info .goods-dc{color:#666666; font-size:12px; margin-bottom:2px;}
.goods-area .goods-list-item .goods-list-info .goods-dc .blue{color:#436693; font-size:12px; font-weight:bold;}
.goods-area .goods-list-item .goods-list-info .goods-reserve{color:#666666; font-size:12px; margin-bottom:2px;}
.goods-area .goods-list-item .goods-list-info .goods-delivery{color:#666666; font-size:12px; margin-bottom:2px;}
.goods-area .goods-list-item .goods-list-info .goods-ea{color:#666666; font-size:12px; margin-bottom:2px;}
.goods-area .goods-list-item .goods-list-info .goods-nvmileage{color:#666666; font-size:12px; margin-bottom:2px;}
.goods-area .goods-list-item .goods-list-arrow{width:15px; height:21px; background:url("/shop/data/skin_mobileV2/default/common/img/nlist/arrow01.png") no-repeat; float:right; margin-top:40px;}
</style>

<section id="wishlist" class="content">
<form name=frmWish method=post>
<input type=hidden name=mode>

<div class="goods-area">
<?php if($TPL_loop_1){$TPL_I1=-1;foreach($TPL_VAR["loop"] as $TPL_V1){$TPL_I1++;?>
	<div class="goods-list-item <?php if($TPL_I1% 2== 1){?>goods-list-item-gray<?php }?>">
		<input type=hidden name=goodsno[<?php echo $TPL_V1["sno"]?>] value="<?php echo $TPL_V1["goodsno"]?>" />
		<input type=hidden name=opt[<?php echo $TPL_V1["sno"]?>][] value="<?php echo implode('|',$TPL_V1["opt"])?>" />
		<input type=hidden name=runout[] value="<?php echo $TPL_V1["item_runout"]?>" />
<?php if((is_array($TPL_R2=$TPL_V1["r_addopt"])&&!empty($TPL_R2)) || (is_object($TPL_R2) && in_array("Countable", class_implements($TPL_R2)) && $TPL_R2->count() > 0)) {foreach($TPL_R2 as $TPL_V2){?><input type=hidden name=addopt[<?php echo $TPL_V1["sno"]?>][] value="<?php echo $TPL_V2?>"><?php }}?>
<?php if((is_array($TPL_R2=$TPL_V1["r_addopt_inputable"])&&!empty($TPL_R2)) || (is_object($TPL_R2) && in_array("Countable", class_implements($TPL_R2)) && $TPL_R2->count() > 0)) {foreach($TPL_R2 as $TPL_V2){?><input type=hidden name=addopt_inputable[<?php echo $TPL_V1["sno"]?>][] value="<?php echo $TPL_V2?>"><?php }}?>
		<div class="goods-list-chk"><input type=checkbox name=sno[] value="<?php echo $TPL_V1["sno"]?>" /></div>
		<div class="goods-list-img"><a href="../goods/view.php?goodsno=<?php echo $TPL_V1["goodsno"]?>"><?php echo goodsimgMobile($TPL_V1["img_s"], 100)?></a></div>
		<div class="goods-list-info">
			<div class="goods-nm"><a href="../goods/view.php?goodsno=<?php echo $TPL_V1["goodsno"]?>"><?php echo strcut($TPL_V1["goodsnm"], 100)?></a></div>
			<div class="goods-option">
<?php if($TPL_V1["opt"]){?><div>선택옵션 : [<?php echo implode("/",$TPL_V1["opt"])?>]</div><?php }?>

<?php if($TPL_V1["addopt"]){?>
					<div>추가옵션 : <?php if((is_array($TPL_R2=$TPL_V1["addopt"])&&!empty($TPL_R2)) || (is_object($TPL_R2) && in_array("Countable", class_implements($TPL_R2)) && $TPL_R2->count() > 0)) {foreach($TPL_R2 as $TPL_V2){?>[<?php echo $TPL_V2["optnm"]?>:<?php echo $TPL_V2["opt"]?>]<?php }}?></div>
<?php }?>
<?php if($TPL_V1["addopt_inputable"]){?>
					<div>입력옵션 : <?php if((is_array($TPL_R2=$TPL_V1["addopt_inputable"])&&!empty($TPL_R2)) || (is_object($TPL_R2) && in_array("Countable", class_implements($TPL_R2)) && $TPL_R2->count() > 0)) {foreach($TPL_R2 as $TPL_V2){?>[<?php echo $TPL_V2["optnm"]?>:<?php echo $TPL_V2["opt"]?>]<?php }}?></div>
<?php }?>

<?php if($TPL_V1["item_runout"]=='T'){?><div style="color:red;">[품절]</div> <?php }?>
			</div>
			<div class="goods-price">상품가격 : <span class="red"><?php echo number_format($TPL_V1["price"]+$TPL_V1["addprice"])?>원</span></div>
			<div class="goods-reserve">적립금 :<?php echo number_format($TPL_V1["reserve"])?>원</div>

		</div>
	</div>
<?php }}?>
</div>

</form>

<div class="btn_area">
	<div id="all-chk-btn"><div id="all-chk-btn-effect" onclick="javascript:chkBox('sno[]','rev');" >전체 선택</div></div>
	<div id="chk-del-btn"><div id="chk-del-btn-effect" onclick="javascript:act('delItem');" id="all-chk-btn">선택삭제</div></div>
	<div id="all-del-btn"><div id="all-del-btn-effect" onclick="javascript:act('cart');" id="all-chk-btn">장바구니담기</div></div>
</div>

</section>

<?php $this->print_("footer",$TPL_SCP,1);?>