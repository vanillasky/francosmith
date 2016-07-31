<?php /* Template_ 2.2.7 2014/03/05 23:19:40 /www/francotr3287_godo_co_kr/shop/data/skin/campingyo/service/customer.htm 000004789 */  $this->include_("dataBank","dataFaqBest");?>
<?php $this->print_("header",$TPL_SCP,1);?>


<!-- 상단이미지 || 현재위치 -->
<TABLE width=100% cellpadding=0 cellspacing=0 border=0>
<tr>
	<td><img src="/shop/data/skin/campingyo/img/common/title_cs.gif" border=0></td>
</tr>
<TR>
	<td class="path">HOME > <B>고객센터</B></td>
</TR>
</TABLE>


<div class="indiv"><!-- Start indiv -->

<TABLE width=665 cellpadding=0 cellspacing=0 border=0>
<TR>
	<TD width=349><img src="/shop/data/skin/campingyo/img/common/cs_img_01.gif"></TD>
	<td background="/shop/data/skin/campingyo/img/common/cs_img_02.gif" align=center style="padding-top:27">
	<TABLE width=300 cellpadding=0 cellspacing=0 border=0>
	<TR>
		<TD width=60% style="border-right:1px solid #BDBDBD; padding-right:5">
		<TABLE cellpadding=0 cellspacing=0 border=0>
		<TR>
			<TD width=50 align=right style="font:8pt tahoma;line-height:24px">TEL :<br>FAX :<br>E-MAIL :</TD>
			<td style="font:8pt tahoma;font-weight:bold;line-height:24px; padding-left:5"><?php echo $GLOBALS["cfg"]['compPhone']?><br><?php echo $GLOBALS["cfg"]['compFax']?><br><?php echo $GLOBALS["cfg"]['adminEmail']?></td>
		</TR>
		</TABLE>
		</TD>
		<td style="font:8pt 돋움;line-height:20px; padding-left:10">평&nbsp;&nbsp;&nbsp;일 09:00 ~ 18:00<br>
		토요일 09:00 ~ 13:00<br>
		일요일 공휴일 휴무</td>
	</TR>
	</TABLE>
	</td>
</TR>
</TABLE>



<br>
<TABLE width=665 cellpadding=0 cellspacing=0 border=0>
<TR>
	<col valign=top>
	<col valign=top align=center>
	<col valign=top align=right>
	<td>
	<TABLE cellpadding=0 cellspacing=0 border=0>
	<!-- 입금계좌 -->

	<TR>
		<TD><img src="/shop/data/skin/campingyo/img/common/cs_top_01.gif"></TD>
	</TR>
	<tr>
		<td height=99 valign=top style="border-left:1px solid #E6E6E6; border-right:1px solid #E6E6E6; padding-left:7; font:8pt 돋움; color:#4C4C4C; line-height:20px"><?php if((is_array($TPL_R1=dataBank())&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?>
		<?php echo $TPL_V1["bank"]?>(<?php echo $TPL_V1["name"]?>) <?php echo $TPL_V1["account"]?><br>
<?php }}?></td>
	</tr>
	<TR>
		<TD><img src="/shop/data/skin/campingyo/img/common/cs_bottom_01.gif"></TD>
	</TR>
	</TABLE>
	</td>
	<td>
	<!-- 마이쇼핑 -->
	<TABLE cellpadding=0 cellspacing=0 border=0>
	<TR>
		<TD><img src="/shop/data/skin/campingyo/img/common/cs_top_02.gif"></TD>
	</TR>
	<TR>
		<TD height=99 class=stxt style="padding-left:15; border-left:1px solid #E6E6E6; border-right:1px solid #E6E6E6">
		- <A HREF="<?php echo url("mypage/mypage_orderlist.php")?>&">주문내역/배송조회</A><br>
		- <A HREF="<?php echo url("mypage/mypage_emoney.php")?>&">적립금내역</A><br>
		- <A HREF="<?php echo url("mypage/mypage_coupon.php")?>&">할인쿠폰내역</A><br>
		- <A HREF="<?php echo url("mypage/mypage_wishlist.php")?>&">상품보관함</A><br>
		- <A HREF="<?php echo url("mypage/mypage_qna.php")?>&">1:1문의게시판</A><br>
		- <A HREF="<?php echo url("mypage/mypage_review.php")?>&">나의 상품후기</A></TD>
	</TR>
	<TR>
		<TD><img src="/shop/data/skin/campingyo/img/common/cs_bottom_01.gif"></TD>
	</TR>
	</TABLE>
	</td>
	<td>
	<!-- 자주하는 질문 -->
	<TABLE cellpadding=0 cellspacing=0 border=0>
	<TR>
		<TD><a href="<?php echo url("service/faq.php")?>&"><img src="/shop/data/skin/campingyo/img/common/cs_top_03.gif"></a></TD>
	</TR>
	<TR>
		<TD class=stxt style="padding-left:15; border-left:1px solid #E6E6E6; border-right:1px solid #E6E6E6" valign=top>
<?php if((is_array($TPL_R1=dataFaqBest( 5, 35))&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {$TPL_I1=-1;foreach($TPL_R1 as $TPL_V1){$TPL_I1++;?>
		<div style="margin:4 4 6 0;"><a href="<?php echo url("service/faq.php?")?>&ssno=<?php echo $TPL_V1["sno"]?>" class=stxt style="line-height:normal;"><img src="/shop/data/skin/campingyo/img/common/cs_num_0<?php echo $TPL_I1+ 1?>.gif" align=absmiddle><?php echo $TPL_V1["question"]?></a></div>
<?php }}?>
		</TD>
	</TR>
	<TR>
		<TD><img src="/shop/data/skin/campingyo/img/common/cs_bottom_01.gif"></TD>
	</TR>
	</TABLE>
	</td>
</TR>
</TABLE>
<br>


<TABLE width=665 cellpadding=0 cellspacing=0 border=0>
<TR>
	<TD><A HREF="<?php echo url("mypage/mypage_qna.php")?>&"><img src="/shop/data/skin/campingyo/img/common/cs_img_03.gif"></A></TD>
	<TD><a href="<?php echo url("service/guide.php")?>&"><img src="/shop/data/skin/campingyo/img/common/cs_img_04.gif"></a></TD>
</TR>
</TABLE>

</div><!-- End indiv -->

<?php $this->print_("footer",$TPL_SCP,1);?>