<?php /* Template_ 2.2.7 2015/07/01 09:55:51 /www/francotr3287_godo_co_kr/shop/data/skin/standard/order/order_end.htm 000006241 */ ?>
<?php $this->print_("header",$TPL_SCP,1);?>


<!-- 상단이미지 || 현재위치 -->
<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr>
	<td><img src="/shop/data/skin/standard/img/common/title_order_finish.gif" border=0></td>
</tr>
<tr>
	<td class="path">home > <b>주문완료</b></td>
</tr>
<tr>
	<td align=center style="padding:10 0 10 0"><img src="/shop/data/skin/standard/img/common/order_complete.gif" border=0></td>
</tr>
</table><p>


<div class="indiv"><!-- Start indiv -->

<table width=100% style="border:1px solid #DEDEDE" cellpadding=0 cellspacing=0>
<tr>
	<td width=150 valign=top align=right bgcolor="#F3F3F3" style="padding-top:5px"><img src="/shop/data/skin/standard/img/common/order_step_end.gif"></td>
	<td style="border:5px solid #F3F3F3; padding:5px 10px;">

	<table width=100% cellpadding=2>
	<col width=100>
<?php if($TPL_VAR["settleInflow"]=="payco"){?>
	<tr>
		<td>결제구분</td>
		<td>페이코 서비스</td>
	</tr>
	<tr>
		<td>결제수단</td>
		<td><?php echo $TPL_VAR["paycoSettleKind"]?></td>
	</tr>
<?php }?>
<?php if($TPL_VAR["settlekind"]=="a"){?>
	<tr>
		<td>입금은행</td>
		<td><?php echo $TPL_VAR["bank"]?></td>
	</tr>
	<tr>
		<td>입금계좌</td>
		<td><?php echo $TPL_VAR["account"]?></td>
	</tr>
	<tr>
		<td>예금주명</td>
		<td><?php echo $TPL_VAR["name"]?></td>
	</tr>
	<tr>
		<td>입금자명</td>
		<td><?php echo $TPL_VAR["bankSender"]?></td>
	</tr>
	<tr>
		<td>입금금액</td>
		<td><b><?php echo number_format($TPL_VAR["settleprice"])?>원</b></td>
	</tr>
<?php }elseif($TPL_VAR["settlekind"]=="c"){?>
	<tr>
		<td>결제카드</td>
		<td><?php echo $_GET["card_nm"]?></td>
	</tr>
	<tr>
		<td>결제금액</td>
		<td><b><?php echo number_format($TPL_VAR["settleprice"])?>원</b></td>
	</tr>
<?php }elseif($TPL_VAR["settlekind"]=="o"){?>
	<tr>
		<td>결제방법</td>
		<td>계좌이체</td>
	</tr>
	<tr>
		<td>결제금액</td>
		<td><b><?php echo number_format($TPL_VAR["settleprice"])?>원</b></td>
	</tr>
<?php }elseif($TPL_VAR["settlekind"]=="v"){?>
	<tr>
		<td>가상계좌</td>
		<td><?php echo $TPL_VAR["vAccount"]?></td>
	</tr>
	<tr>
		<td>결제금액</td>
		<td><b><?php echo number_format($TPL_VAR["settleprice"])?>원</b></td>
	</tr>
<?php }elseif($TPL_VAR["settlekind"]=="y"){?>
	<tr>
		<td>결제방법</td>
		<td>옐로페이</td>
	</tr>
	<tr>
		<td>결제금액</td>
		<td><b><?php echo number_format($TPL_VAR["settleprice"])?>원</b></td>
	</tr>
<?php }elseif($TPL_VAR["settlekind"]=="d"){?>
	<tr>
		<td>결제방법</td>
		<td>전액할인 결제 (적립금 사용)</td>
	</tr>
<?php }elseif($TPL_VAR["settlekind"]=="u"){?>
	<tr>
		<td>결제카드</td>
		<td><?php echo $_GET["card_nm"]?></td>
	</tr>
	<tr>
		<td>결제금액</td>
		<td><b><?php echo number_format($TPL_VAR["settleprice"])?>원</b></td>
	</tr>
<?php }?>
	<tr>
		<td>상품가격</td>
		<td><?php echo number_format($TPL_VAR["goodsprice"])?>원</td>
	</tr>
	<tr>
		<td>배송비</td>
		<td><?php if($TPL_VAR["deli_msg"]){?><?php echo $TPL_VAR["deli_msg"]?><?php }else{?><?php echo number_format($TPL_VAR["delivery"])?>원<?php }?></td>
	</tr>
<?php if($TPL_VAR["o_special_discount_amount"]){?>
	<tr>
		<td>상품할인</td>
		<td><?php echo number_format($TPL_VAR["o_special_discount_amount"])?>원</td>
	</tr>
<?php }?>
<?php if($TPL_VAR["memberdc"]){?>
	<tr>
		<td>회원할인</td>
		<td><?php echo number_format($TPL_VAR["memberdc"])?>원</td>
	</tr>
<?php }?>
<?php if($GLOBALS["naver_mileage"]){?>
	<tr>
		<td>네이버마일리지</td>
		<td><?php echo number_format($GLOBALS["naver_mileage"])?>원</td>
	</tr>
<?php }?>
<?php if($GLOBALS["naver_cash"]){?>
	<tr>
		<td>네이버캐쉬</td>
		<td><?php echo number_format($GLOBALS["naver_cash"])?>원</td>
	</tr>
<?php }?>
<?php if($TPL_VAR["coupon"]){?>
	<tr>
		<td>쿠폰할인</td>
		<td><?php echo number_format($TPL_VAR["coupon"])?>원<?php if($TPL_VAR["about_coupon"]){?> (어바웃쿠폰 <?php echo number_format($TPL_VAR["about_coupon"])?>원)<?php }?></td>
	</tr>
<?php }?>
<?php if($TPL_VAR["coupon_emoney"]){?>
	<tr>
		<td>쿠폰적립</td>
		<td><?php echo number_format($TPL_VAR["coupon_emoney"])?>원</td>
	</tr>
<?php }?>
	<tr>
		<td>적립금결제</td>
		<td><b><?php echo number_format($TPL_VAR["emoney"])?>원</b></td>
	</tr>
<?php if($TPL_VAR["eggyn"]=='y'){?>
	<tr>
		<td>전자보증보험</td>
		<td><a href="javascript:popupEgg('<?php echo $GLOBALS["egg"]["usafeid"]?>', '<?php echo $TPL_VAR["ordno"]?>')"><font color=#0074BA><b><u><?php echo $TPL_VAR["eggno"]?> <font class=small><b>[보증서출력]</b></font></u></b></font></a><div style="padding-top:5px"><font class=small color=444444>마이페이지의 <A HREF="<?php echo url("mypage/mypage_orderview.php?")?>&ordno=<?php echo $TPL_VAR["ordno"]?>"><font class=small color=#0074BA><b><u>주문상세내역</u></b></font></A>에서도 언제든 출력이 가능합니다.</font></div></td>
	</tr>
<?php }elseif($TPL_VAR["eggyn"]=='f'){?>
	<tr>
		<td>전자보증보험</td>
		<td>보증서 발급이 실패되었습니다. 마이페이지의 <A HREF="<?php echo url("mypage/mypage_orderview.php?")?>&ordno=<?php echo $TPL_VAR["ordno"]?>"><font color=#0074BA><b><u>주문상세내역</u></b></font></A>에서 재발급이 가능합니다.</td>
	</tr>
<?php }?>
	<tr><td height=3></td></tr>
	<tr><td height=1 bgcolor=#efefef colspan=2 style="font-size:0px;"></td></tr>
	<tr><td height=3></td></tr>
	<tr>
		<td>주문번호</td>
		<td><?php echo $TPL_VAR["ordno"]?></td>
	</tr>
	<tr>
		<td>주문자명</td>
		<td><?php echo $TPL_VAR["nameOrder"]?></td>
	</tr>
	<tr>
		<td>주문일자</td>
		<td><?php echo $TPL_VAR["orddt"]?></td>
	</tr>
	<tr>
		<td>주문금액</td>
		<td><?php echo number_format($TPL_VAR["settleprice"])?>원</td>
	</tr>
<?php if($TPL_VAR["cashreceipt_useopt"]=='0'||$TPL_VAR["cashreceipt_useopt"]=='1'){?>
	<tr>
		<td>현금영수증</td>
		<td>
<?php if($TPL_VAR["cashreceipt_useopt"]=='0'){?>
			소득공제용
<?php }else{?>
			지출증빙용
<?php }?>
			현금영수증 신청
		</td>
	</tr>
<?php }?>
	</table>
	</td>
</tr>
</table><p>

<div style="width:100%; text-align:center; padding:10"><A HREF="<?php echo url("index.php")?>&"><img src="/shop/data/skin/standard/img/common/btn_confirm.gif" border=0></A></div>

</div><!-- End indiv -->

<?php echo $TPL_VAR["naverCommonInflowScript"]->getOrderCompleteData($_GET["ordno"])?>


<?php $this->print_("footer",$TPL_SCP,1);?>