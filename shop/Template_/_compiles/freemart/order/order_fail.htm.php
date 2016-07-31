<?php /* Template_ 2.2.7 2016/04/29 20:06:35 /www/francotr3287_godo_co_kr/shop/data/skin/freemart/order/order_fail.htm 000002838 */ ?>
<?php $this->print_("header",$TPL_SCP,1);?>




<div class="page-wrapper">
	
	<div class="page_title_div">
		<div class="page_title">주문실패</div>
		<div class="page_path"><a href="/shop/">HOME</a> &gt; <span class='bold'>주문실패</span></div>
	</div>
	<div class="page_title_line"></div>

	<div style="width:100%; padding-top:20px;"></div>
	
	<div class="indiv"><!-- Start indiv -->

		<table class="order-table-wrapper" >
		<tr>
			<td width=150 valign=top align=right bgcolor="#F3F3F3" style="padding-top:5px"><img src="/shop/data/skin/freemart/img/common/order_step_end.gif"></td>
			<td style="border:5px solid #F3F3F3; padding:5px 10px;">
				<table width=100% cellpadding=2>
				<col width=100>
<?php if($TPL_VAR["settlekind"]=="o"){?>
				<tr>
					<td>결제방법</td>
					<td>계좌이체</td>
				</tr>
				<tr>
					<td>결과</td>
					<td>주문이 실패되었습니다</td>
				</tr>
<?php }elseif($TPL_VAR["settlekind"]=="c"){?>
				<tr>
					<td>결제방법</td>
					<td>신용카드</td>
				</tr>
				<tr>
					<td>결과</td>
					<td>주문이 실패되었습니다</td>
				</tr>
<?php }elseif($TPL_VAR["settlekind"]=="y"){?>
				<tr>
					<td>결제방법</td>
					<td>옐로페이</td>
				</tr>
				<tr>
					<td>결과</td>
					<td>주문이 실패되었습니다</td>
				</tr>
<?php }?>
<?php if($TPL_VAR["step"]== 0&&$TPL_VAR["step2"]> 50&&in_array($TPL_VAR["settlekind"],array('c','o','v','y'))&&$TPL_VAR["pgfailreason"]){?><!-- 결제실패사유 -->
				<tr>
					<td>결제실패사유</td>
					<td><?php echo $TPL_VAR["pgfailreason"]?></td>
				</tr>
<?php }?>
<?php if($TPL_VAR["eggyn"]=='f'){?>
				<tr>
					<td>전자보증보험</td>
					<td>보증서 발급 실패로 주문이 실패되었습니다.</td>
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
				</table>
			</td>
		</tr>
		</table>
		<p></p>

		<div style="width:100%; text-align:center; padding:10">
			<!-- <A HREF="<?php echo url("index.php")?>&"><img src="/shop/data/skin/freemart/img/common/btn_confirm.gif" border=0></A> -->
			<buuton class="button-dark button-big-wide" onclick="location.href='<?php echo url("index.php")?>&'">홈으로</buuton>
		</div>
		
	</div><!-- End indiv -->
	
	<div style="width:100%; padding-top:30px;"></div>
</div>	

<?php $this->print_("footer",$TPL_SCP,1);?>