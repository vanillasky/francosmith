<?php /* Template_ 2.2.7 2016/04/11 20:06:15 /www/francotr3287_godo_co_kr/shop/data/skin/freemart/mypage/paper_coupon.htm 000001586 */ ?>
<html>
	
<script src="/shop/data/skin/freemart/common.js"></script>
<link rel="styleSheet" href="/shop/data/skin/freemart/style.css">

<body bgcolor="#000000" style="padding:10px;">
<!--{ ? $cnum=$_REQUEST['cnum'];  }--> 
<form method="post" action="<?php echo url("mypage/indb.paper.php")?>&" onsubmit="return chkForm(this)">
<input type="hidden" name="sno" value="<?php echo $GLOBALS["sno"]?>"/>
<table height=100% width=100% cellpadding=0 cellspacing=0 border=0 bgcolor="#FFFFFF">
<tr>
	<td style="background:#000000; border-bottom:2px solid #DDDDDD"><img src="/shop/data/skin/freemart/img/common/popup_title_coupon.gif"></td>
</tr>
<tr>
	<td align=center height="100">
	<div>
		<input type="text" name="coupon_number[]" size="5" maxlength="4" label="ÄíÆù¹øÈ£" value="<?php echo $_GET['cnum']?>" required/>-
	<input type="text" name="coupon_number[]" size="5" maxlength="4" label="ÄíÆù¹øÈ£" required/>-
	<input type="text" name="coupon_number[]" size="5" maxlength="4" label="ÄíÆù¹øÈ£" required/>-
	<input type="text" name="coupon_number[]" size="5" maxlength="4" label="ÄíÆù¹øÈ£" required/>
	</div>
	<div style="padding-top:10">
	<input type="submit" value="È®ÀÎ"/>
	</div>
	</td>
</tr>
<tr>
	<td valign=bottom align=right style="padding:0 14 6 0;"><A HREF="javascript:this.close()" onFocus="blur()"><img src="/shop/data/skin/freemart/img/common/popup_close.gif"></A></td>
</tr>
</table>
</form>
</body>
</html>