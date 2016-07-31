<?php /* Template_ 2.2.7 2014/03/05 23:19:40 /www/francotr3287_godo_co_kr/shop/data/skin/campingyo/mypage/mypage_coupon.htm 000004974 */ 
if (is_array($TPL_VAR["goods"])) $TPL_goods_1=count($TPL_VAR["goods"]); else if (is_object($TPL_VAR["goods"]) && in_array("Countable", class_implements($TPL_VAR["goods"]))) $TPL_goods_1=$TPL_VAR["goods"]->count();else $TPL_goods_1=0;?>
<?php $this->print_("header",$TPL_SCP,1);?>


<!-- 상단이미지 || 현재위치 -->
<TABLE width=100% cellpadding=0 cellspacing=0 border=0>
<tr>
	<td><img src="/shop/data/skin/campingyo/img/common/title_coupon.gif" border=0></td>
</tr>
<TR>
	<td class="path">HOME > 마이페이지 > <B>할인쿠폰내역</B></td>
</TR>
</TABLE>


<div class="indiv"><!-- Start indiv -->
<div style="height:20px"></div>
<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr><td align="center">
<a href="<?php echo url("mypage/mypage_coupon.php?")?>&tab=all"><img src="/shop/data/skin/campingyo/img/common/sale_btn_01<?php echo $GLOBALS["tab01"]?>.gif" alt="전체" id="sale_btn_01" onmouseover="tab_over(1,'on');" onmouseout="tab_over(1,'off');"/></a>&nbsp;
<a href="<?php echo url("mypage/mypage_coupon.php?")?>&tab=wait"><img src="/shop/data/skin/campingyo/img/common/sale_btn_03<?php echo $GLOBALS["tab03"]?>.gif" alt="보유내역" id="sale_btn_03"  onmouseover="tab_over(3,'on');" onmouseout="tab_over(3,'off');"/></a>&nbsp;
<a href="<?php echo url("mypage/mypage_coupon.php?")?>&tab=used"><img src="/shop/data/skin/campingyo/img/common/sale_btn_02<?php echo $GLOBALS["tab02"]?>.gif" alt="사용내역" id="sale_btn_02"  onmouseover="tab_over(2,'on');" onmouseout="tab_over(2,'off');"/></a>
</td></tr>
</table>
<SCRIPT LANGUAGE="JavaScript">
<!--
	function tab_over(tab, val){
		var getTab1 = "<?php echo $GLOBALS["tab01"]?>";
		var getTab2 = "<?php echo $GLOBALS["tab02"]?>";
		var getTab3 = "<?php echo $GLOBALS["tab03"]?>";

		var img1 = document.getElementById("sale_btn_01");
		var img2 = document.getElementById("sale_btn_02");
		var img3 = document.getElementById("sale_btn_03");

		if(getTab1 != "on")img1.src = "/shop/data/skin/campingyo/img/common/sale_btn_01off.gif";
		if(getTab2 != "on")img2.src = "/shop/data/skin/campingyo/img/common/sale_btn_02off.gif";
		if(getTab3 != "on")img3.src = "/shop/data/skin/campingyo/img/common/sale_btn_03off.gif";

		if(tab == 1){
			if(val == "on")img1.src = "/shop/data/skin/campingyo/img/common/sale_btn_01on.gif"
			else if(getTab1 != "on")img1.src = "/shop/data/skin/campingyo/img/common/sale_btn_01off.gif";
		}else if(tab == 2){
			if(val == "on")img2.src = "/shop/data/skin/campingyo/img/common/sale_btn_02on.gif"
			else if(getTab2 != "on")img2.src = "/shop/data/skin/campingyo/img/common/sale_btn_02off.gif";
		}else if(tab == 3){
			if(val == "on")img3.src = "/shop/data/skin/campingyo/img/common/sale_btn_03on.gif"
			else if(getTab3 != "on")img3.src = "/shop/data/skin/campingyo/img/common/sale_btn_03off.gif";
		}
	}
//-->
</SCRIPT>
<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr><td height=2 bgcolor="#303030" colspan=10></td></tr>
<col width=200><col><col width=100 align=center><col width=50 align=center><col width=50 align=center><col width=50 align=center>
<tr bgcolor=#F0F0F0 height=23 class=input_txt>
	<th>쿠폰</th>
	<th>적용상품</th>
	<th>사용일 및 기간</th>
	<th>기능</th>
	<th>할인/적립</th>
	<th>사용여부</th>
</tr>
<tr><td height=1 bgcolor="#D6D6D6" colspan=10></td></tr>
<?php if($TPL_goods_1){foreach($TPL_VAR["goods"] as $TPL_V1){?>
<tr height=25>
	<td><div style="text-overflow:ellipsis;overflow:hidden;width:200px;padding-left:10px;line-height:18px;" nowrap>[<?php echo $TPL_V1["coupon"]?>]</div>
		<div style="text-overflow:ellipsis;overflow:hidden;width:200px;padding-left:10px;line-height:18px;" nowrap><?php echo $TPL_V1["summa"]?></div>
	</td>
	<td><div style="text-overflow:ellipsis;overflow:hidden;width:200px;padding-left:10px;line-height:18px;" nowrap><?php if($TPL_V1["goodsnm"]){?><a href="<?php echo url("goods/goods_view.php?")?>&goodsno=<?php echo $TPL_V1["goodsno"]?>"><?php echo $TPL_V1["goodsnm"]?></a><?php }else{?> - <?php }?></div></td>
	<td><?php echo $TPL_V1["dataStr"]?></td>
	<td><?php echo $GLOBALS["r_couponAbility"][$TPL_V1["ability"]]?></td>
	<td><?php echo number_format($TPL_V1["price"])?><?php if(substr($TPL_V1["price"], - 1)!='%'){?>원<?php }else{?>%<?php }?></td>
	<td><?php if($TPL_V1["cnt"]=='미사용'){?><FONT COLOR="#007FC8"><?php echo $TPL_V1["cnt"]?></FONT><?php }else{?><?php echo $TPL_V1["cnt"]?><?php }?></td>
</tr>
<tr><td colspan=7 height=1 bgcolor="#ECECEC"></td></tr>
<?php }}?>
</table>
<div style="height:12px"></div>
<div align="right" style="padding-top:5">
<img src="/shop/data/skin/campingyo/img/common/btn_paper.gif" onclick="popup('../mypage/paper_coupon.php',350,200)" style="cursor:hand">
</div>
<div style="height:30px"></div>
</div><!-- End indiv -->

<?php $this->print_("footer",$TPL_SCP,1);?>