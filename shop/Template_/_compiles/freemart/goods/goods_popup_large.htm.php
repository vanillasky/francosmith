<?php /* Template_ 2.2.7 2016/03/25 11:44:43 /www/francotr3287_godo_co_kr/shop/data/skin/freemart/goods/goods_popup_large.htm 000003877 */ 
if (is_array($TPL_VAR["t_img"])) $TPL_t_img_1=count($TPL_VAR["t_img"]); else if (is_object($TPL_VAR["t_img"]) && in_array("Countable", class_implements($TPL_VAR["t_img"]))) $TPL_t_img_1=$TPL_VAR["t_img"]->count();else $TPL_t_img_1=0;?>
<script>
function fitwin()
{
	window.resizeTo(150,150);
	
	var borderX = 150 - document.body.clientWidth;
	var borderY = 150 - document.body.clientHeight;

	if(document.body.clientWidth > 150)borderX = 50;
	if(document.body.clientHeight > 150)borderY = 50;
	
	width	= document.body.scrollWidth + borderX;
	height	= document.body.scrollHeight + borderY;
	windowX = (window.screen.width-width)/2;
	windowY = (window.screen.height-height)/2;

	if(width>screen.width){
		width = screen.width;
		windowX = 0;
	}
	if(height>screen.height-50){
		height = screen.height-50;
		windowY = 0;
	}

	window.moveTo(windowX,windowY);
	window.resizeTo(width,height);
}
</script>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<meta name="description" content="<?php echo $GLOBALS["meta_title"]?>">
<meta name="keywords" content="<?php echo $GLOBALS["meta_keywords"]?>">
<title><?php echo $GLOBALS["meta_title"]?></title>
<link rel="styleSheet" href="/shop/data/skin/freemart/style.css">
<script src="/shop/data/skin/freemart/common.js"></script>
<script>
function chgImg(obj)
{
	var objImg = document.getElementById('objImg');
	objImg.src = obj.src.replace("/t/","/");
}
</script>

<?php echo copyProtect()?>

<body style="margin:0" <?php echo copyProtect(true)?>>

<table width=100% height=100% cellpadding=0 cellspacing=0 border=0>
<tr>
	<td height=100% style="border-width:10px; border-style:solid; border-color:#000000;padding:10px" valign=top>
	<table width=100% height=100% cellpadding=0 cellspacing=0 border=0>
	<tr>
		<td align=center>
		<table width=100% cellpadding=0 cellspacing=0 border=0>
		<tr>
			<td width=<?php echo $GLOBALS["cfg"]["img_l"]+ 0?>><?php echo goodsimg($TPL_VAR["r_img"][ 0],$GLOBALS["cfg"]["img_l"]+ 0,'id=objImg')?></td>
			<td width=250 nowrap height=100% valign=top align=right style="padding-left:10px">

			<table width=100% height=100% cellpadding=5 cellspacing=0 border=0>
			<tr><td colspan=2 height=2 bgcolor="#000000" style="padding:0px"></td></tr>
			<tr><td colspan=2 height=2 style="padding:0px"></td></tr>
			<tr><td colspan=2 height=1 bgcolor="#B4B4B4" style="padding:0px"></td></tr>
			<tr><td colspan=2 bgcolor="#F6F6F6"><B><?php echo $TPL_VAR["goodsnm"]?></B></td></tr>
			<tr><td colspan=2 height=1 bgcolor="#C5C5C5" style="padding:0px"></td></tr>
			<!-- <tr><td nowrap width=40>설명</td><td><?php echo $TPL_VAR["shortdesc"]?></td></tr> -->
			<tr><td width=40 class=stxt><B>가격</B></td><td><FONT COLOR="#007FC8"><?php echo number_format($TPL_VAR["price"])?></FONT></td></tr>
			<tr><td colspan=2 height=1 bgcolor="#DDDDDD" style="padding:0px"></td></tr>
			<tr><td class=stxt><B>적립금</B></td><td><?php echo number_format($TPL_VAR["reserve"])?></td></tr>
			<tr><td colspan=2 height=1 bgcolor="#DDDDDD" style="padding:0px"></td></tr>
			<tr><td colspan=2 height=2 style="padding:0px"></td></tr>
			<tr><td colspan=2 height=2 bgcolor="#000000" style="padding:0px"></td></tr>
			<tr>
				<td height=100% colspan=2 valign=bottom align=right>
<?php if($TPL_t_img_1){foreach($TPL_VAR["t_img"] as $TPL_V1){?>
				<?php echo goodsimg($TPL_V1, 45,"onclick='chgImg(this)' class=hand style='border-width:1; border-style:solid; border-color:#cccccc'")?>

<?php }}?>
				</td>
			</tr>
			</table>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	<tr><td align=right><A HREF="javascript:this.close()" onFocus="blur()"><img src="/shop/data/skin/freemart/img/common/popup_close.gif"></A></td></tr>
	</table>
	</td>
</tr>
</table>

</body>
</html>