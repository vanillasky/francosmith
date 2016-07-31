<?php /* Template_ 2.2.7 2012/10/04 18:27:50 /www/francotr3287_godo_co_kr/shop/data/skin_today/today/todayshop/today_cart.htm 000004339 */ ?>
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
		recalc : function() {

			var chks = document.getElementsByName('idxs[]');

			var total_reserve = 0;
			var total_price = 0;

			for (var i=0,m=chks.length;i<m ;i++) {
				if (chks[i].checked == true) {
					total_price += parseInt(this.data[i].price);
					total_reserve += parseInt(this.data[i].reserve);
				}
			}

			document.getElementById('el-orderitem-total-reserve').innerText = comma(total_reserve);
			document.getElementById('el-orderitem-total-price').innerText = comma(total_price);
		}
	}
}();
</script>

<!-- 상단이미지 || 현재위치 -->
<TABLE width=100% cellpadding=0 cellspacing=0 border=0>
<tr>
<td><img src="/shop/data/skin_today/today/img/list_basket_title01.gif" border=0></td>
</tr>
<TR>
<td class="path">HOME > <B>장바구니</B></td>
</TR>
</TABLE>



<div class="indiv"><!-- Start indiv -->
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
	<a href="javascript:void(0);" onClick="nsGodo_CartAction.del()" onFocus="blur()"><img src="/shop/data/skin_today/today/img/common/btn_check_delete.gif" border=0></a>&nbsp;
	<a href="javascript:void(0);" onClick="nsGodo_CartAction.order()" onFocus="blur()"><img src="/shop/data/skin_today/today/img/common/btn_check_order.gif" border=0></a>&nbsp;
<?php }?>
<a href="javascript:history.back();"onFocus="blur()"><img src="/shop/data/skin_today/today/img/common/btn_back2.gif" border=0></a>&nbsp;
<a href="<?php echo url("todayshop/today_cart.php?")?>&mode=empty" onFocus="blur()"><img src="/shop/data/skin_today/today/img/common/btn_empty2.gif" border=0></a>&nbsp;
<a href="<?php echo url("index.php")?>&" onFocus="blur()"><img src="/shop/data/skin_today/today/img/common/btn_continue2.gif" border=0></a></td>
</tr>
</TABLE>
<div align="center"><?php echo $TPL_VAR["naverCheckout"]?></div>
<div align="center"><?php echo $TPL_VAR["auctionIpay"]?></div>

</div><!-- End indiv -->

<?php $this->print_("footer",$TPL_SCP,1);?>