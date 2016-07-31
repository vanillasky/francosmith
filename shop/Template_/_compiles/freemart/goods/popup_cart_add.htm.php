<?php /* Template_ 2.2.7 2016/01/12 13:09:27 /www/francotr3287_godo_co_kr/shop/data/skin/freemart/goods/popup_cart_add.htm 000001863 */ ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title> New Document </title>
	<meta name="Generator" content="EditPlus">
	<meta name="Author" content="">
	<meta name="Keywords" content="">
	<meta name="Description" content="">
<script src="/shop/data/skin/freemart/common.js"></script>
<style>
body{margin:0}
</style>
</head>
<body>
<table width="380" height="335" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td height="42" width="324" bgcolor="#434343"  style="padding-left:20px"><img src="/shop/data/skin/freemart/img/common/tit_cart.gif"/></td>
		<td height="42" bgcolor="#434343"  ><a href="javascript:closeCenterLayer()"><img src="/shop/data/skin/freemart/img/common/btn_close.gif" border="0"/></a></td>
	</tr>
	<tr>
		<td height="145" align="center" style="padding-top:18px" colspan="3" valign="top"><img src="/shop/data/skin/freemart/img/common/img_cart.gif" /></td>
	</tr>			
	<tr>
		<td height="35" align="center" style="font-weight:bold" colspan="2" >장바구니에 담았습니다.<br/>지금 확인하시겠습니까?</td>
	</tr>	
	<tr>
		<td style="padding-top:5px" align="center" colspan="2">
<?php if($_REQUEST["preview"]=='y'){?>
			<a href="javascript:opener.location.href='<?php echo url("goods/goods_cart.php")?>&';self.close()" target="_top">
<?php }else{?>
			<a href="<?php echo url("goods/goods_cart.php")?>&" target="_top">
<?php }?>
			<img src="/shop/data/skin/freemart/img/common/btn_cart_go.gif" border="0" /></a>
			&nbsp;&nbsp;<a href="javascript:parent.location.reload();"><img src="/shop/data/skin/freemart/img/common/btn_shopping.gif" border="0" /></a>
		</td>
	</tr>
</table>
</body>
</html>