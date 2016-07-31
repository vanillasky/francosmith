<?php /* Template_ 2.2.7 2016/04/29 18:24:41 /www/francotr3287_godo_co_kr/shop/data/skin/freemart/goods/goods_cart.htm 000006277 */ ?>
<?php $this->print_("header",$TPL_SCP,1);?>


<script type="text/javascript">
var nsGodo_CartAction = function() {

	function popup(url,w_name, w_width,w_height) {
		var x = (screen.availWidth - w_width) / 2;
		var y = (screen.availHeight - w_height) / 2;
		return window.open(url,w_name,"width="+w_width+",height="+w_height+",top="+y+",left="+x+",scrollbars=no");
	}

	return {
		cart_type : '<?php if($_GET["cart_type"]=='todayshop'){?>todayshop<?php }else{?>regular<?php }?>',
		data : [],
		pushdata: function(obj) {
			this.data.push(obj);
		},
		editOption : function(idx) {
			popup('../goods/popup_goods_cart_edit.php?idx='+idx+'&cart_type='+ this.cart_type,'WIN_CARTOPTION',350,500);
		},
		wishList : function() {

			if (this.cart_type == 'todayshop') {
				alert('투데이샵 상품은 상품보관함에 담을 수 없습니다.');
				return false;
			}

			if (!this.check()) {
				alert('보관할 상품을 선택해 주세요.');
				return false;
			}

			var f = document.frmCart;
			f.action = '../mypage/mypage_wishlist.php';
			f.mode.value = 'addItemFromCart';
			f.submit();
		},
		order : function() {

			if (!this.check()) {
				alert('주문할 상품을 선택해 주세요.');
				return false;
			}
<?php if(!$GLOBALS["_SESSION"]['adult']){?>
			if (!this.adultcheck()) {
				alert('성인인증이 필요한 상품/컨텐츠 가 포함되어 있습니다.\n\n성인(본인) 인증 후 주문하기를 진행해 주세요.');
				location.href="<?php echo url("main/intro_adult.php?returnUrl=../goods/goods_cart.php")?>&";
				return false;
			}
<?php }?>

			var f = document.frmCart;
			f.action = (this.cart_type == 'regular') ? '../order/order.php' : '../todayshop/order.php' ;
			f.mode.value = 'setOrder';
			f.submit();
		},
		del : function() {

			if (!this.check()) {
				alert('삭제할 상품을 선택해 주세요.');
				return false;
			}

			var f = document.frmCart;
			f.action = (this.cart_type == 'regular') ? '../goods/goods_cart.php' : '../todayshop/today_cart.php' ;
			f.mode.value = 'delItems';
			f.submit();
		},
		check : function() {

			var chks = document.getElementsByName('idxs[]');
			var cnt = 0;

			for (var i=0,m=chks.length;i<m ;i++) {
				if (chks[i].checked == true) cnt++
			}

			return cnt > 0 ? true : false;

		},
		adultcheck : function() {
			var chks = document.getElementsByName('idxs[]');
			var cnt = 0;

			for (var i=0,m=chks.length;i<m ;i++) {
				if (chks[i].checked == true){
					if(document.getElementsByName('adultpro[]')[i].value > 0) cnt++
				}
			}
			
			return cnt > 0 ? false : true;

		},
		recalc : function() {

			var chks = document.getElementsByName('idxs[]');

			var total_reserve = 0;
			var total_price = 0;
			var total_special_discount_amount = 0;
			var total_memberdc = 0;


			for (var i=0,m=chks.length;i<m ;i++) {
				if (chks[i].checked == true) {
					total_price += parseInt(this.data[i].price * this.data[i].ea );
					total_special_discount_amount += parseInt(this.data[i].special_discount_amount* this.data[i].ea );
					total_memberdc += parseInt(this.data[i].memberdc* this.data[i].ea );
					total_reserve += parseInt(this.data[i].reserve); 
				}
			}

			total_special_discount_amount += total_memberdc;

			document.getElementById('el-orderitem-total-reserve').innerText = comma(total_reserve);
			document.getElementById('el-orderitem-total-price').innerText = comma(total_price);
			document.getElementById('el-orderitem-total-special_discount_amount').innerText = comma(total_special_discount_amount);
			document.getElementById('el-orderitem-total-result').innerText = comma(total_price - total_special_discount_amount);
		}
	}
}();
</script>

<div class="page-wrapper">
	<div class="page_title_div">
		<div class="page_title" >Your Shopping Cart</div>
		<div class="page_path" ><a href="/shop/">HOME</a> &gt; <a href="/shop/mypage/mypage.php">마이페이지</a> &gt; <span class='bold'>장바구니</span></div>
	</div>
	<div class="page_title_line"  style="clear:both;"></div>
	
	<div class="indiv"><!-- Start indiv -->
<?php if($TPL_VAR["use_todayshop_cart"]=='y'){?>
			<a href="<?php echo url("goods/goods_cart.php")?>&"><img src="/shop/data/skin/freemart/img/common/btn_basket_shopping.gif"></a>
			<a href="<?php echo url("goods/goods_cart.php?")?>&cart_type=todayshop"><img src="/shop/data/skin/freemart/img/common/btn_basket_today.gif"></a>
<?php }?>
	
		<form name=frmCart method=post>
			<input type=hidden name=mode value=modItem>
		<br>
		<?php echo $this->define('tpl_include_file_1',"proc/orderitem.htm")?> <?php $this->print_("tpl_include_file_1",$TPL_SCP,1);?>

		</form>
	
		<br>
	
		<TABLE width=100% cellpadding=0 cellspacing=0 border=0>
		<tr>
			<td align=center>
<?php if(count($TPL_VAR["cart"]->item)){?>
				<a href="javascript:void(0);" onClick="nsGodo_CartAction.del();return false;" onFocus="blur()"><img src="/shop/data/skin/freemart/img/common/btn_check_delete.gif" border=0></a>&nbsp;
				<a href="javascript:void(0);" onClick="nsGodo_CartAction.order();return false;" onFocus="blur()"><img src="/shop/data/skin/freemart/img/common/btn_check_order.gif" border=0></a>&nbsp;
				<a href="javascript:void(0);" onClick="nsGodo_CartAction.wishList();return false;" onFocus="blur()"><img src="/shop/data/skin/freemart/img/common/btn_check_wslist.gif" border=0></a>&nbsp;
<?php }?>
				<a href="javascript:history.back();"onFocus="blur()"><img src="/shop/data/skin/freemart/img/common/btn_back2.gif" border=0></a>&nbsp;
				<a href="<?php echo url("goods/goods_cart.php?")?>&mode=empty" onFocus="blur()"><img src="/shop/data/skin/freemart/img/common/btn_empty2.gif" border=0></a>&nbsp;
				<a href="<?php echo url("index.php")?>&" onFocus="blur()"><img src="/shop/data/skin/freemart/img/common/btn_continue2.gif" border=0></a></td>
		</tr>
		</TABLE>
	
		<div align="center"><?php echo $TPL_VAR["naverCheckout"]?></div>
		<div align="center"><?php echo $TPL_VAR["auctionIpayBtn"]?></div>
	
	</div><!-- End indiv -->
	
	<div style="width:100%; padding-top:30px;"></div>
</div>
<?php $this->print_("footer",$TPL_SCP,1);?>