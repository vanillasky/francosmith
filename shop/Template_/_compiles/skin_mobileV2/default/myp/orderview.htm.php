<?php /* Template_ 2.2.7 2016/01/09 11:08:42 /www/francotr3287_godo_co_kr/shop/data/skin_mobileV2/default/myp/orderview.htm 000008539 */ 
if (is_array($TPL_VAR["item"])) $TPL_item_1=count($TPL_VAR["item"]); else if (is_object($TPL_VAR["item"]) && in_array("Countable", class_implements($TPL_VAR["item"]))) $TPL_item_1=$TPL_VAR["item"]->count();else $TPL_item_1=0;?>
<?php $this->print_("header",$TPL_SCP,1);?>


<?php  $TPL_VAR["page_title"] = "주문내역 상세";?>
<?php $this->print_("sub_header",$TPL_SCP,1);?>

<style type="text/css">
section#orderview { background:#FFFFFF; padding:12px;}
section#orderview ul{list-style:none;}

section#orderview .btn_area { text-align:center; }
section#orderview .btn_recoupon { margin:0 auto; margin-top:10px; width:80px; height:27px; line-height:27px; font-size:12px; color:#FFFFFF; font-weight:normal;text-align:center; background:#808591; font-family:dotum; border-radius:3px; }
section#orderview .inblock { display:inline-block; }
</style>
<script type="text/javascript">
	$(function () {
		$("#couponCancel").bind("click", function () {
			if(confirm("쿠폰 사용내역을 취소하고 미사용 상태로 변경하시겠습니까?")) {
				$("input[name=mode]").val("recoverCoupon");
				$("#frm").submit();
			}
		});
	});
</script>

<section id="orderview" class="content">
	<form name="frm" id="frm" method="post" action="indb.php">
	<input type="hidden" name="mode" />
	<input type="hidden" name="ordno" value="<?php echo $TPL_VAR["ordno"]?>" />
	<div class="item_list">
		<h4 class="hidden">주문상품</h4>
		<ul>
<?php if($TPL_item_1){foreach($TPL_VAR["item"] as $TPL_V1){?>
			<li>
				<dl>
					<dt class="hidden">상품이미지</dt>
					<dd class="oil_img"><a href="../goods/view.php?goodsno=<?php echo $TPL_V1["goodsno"]?>"><?php echo goodsimgMobile(array($TPL_V1["img_mobile"],$TPL_V1["img_i"],$TPL_V1["img_s"],$TPL_V1["img_m"]),"60,60")?></a></dd>
					<dt class="hidden">상품명</dt>
					<dd class="oil_name"><a href="../goods/view.php?goodsno=<?php echo $TPL_V1["goodsno"]?>"><?php echo strcut($TPL_V1["goodsnm"], 100)?></a></dd>
					<dt class="hidden">옵션</dt>
					<dd class="oil_option">
<?php if($TPL_V1["opt1"]){?><div>[<?php echo $TPL_V1["opt1"]?><?php if($TPL_V1["opt2"]){?>/<?php echo $TPL_V1["opt2"]?><?php }?>]</div><?php }?>
<?php if($TPL_V1["addopt"]){?><div>[<?php echo str_replace("^","] [",$TPL_V1["addopt"])?>]</div><?php }?>
					</dd>
					<dt class="oil_price_title blt">판매가 : </dt>
					<dd class="oil_price"><?php echo number_format($TPL_V1["price"])?>원</dd>
					<dt class="oil_ea_title blt">수량 : </dt>
					<dd class="oil_ea"><?php echo $TPL_V1["ea"]?>개</dd>
					<dt class="oil_dstep_title blt">배송상태 : </dt>
					<dd class="oil_dstep"><?php echo $GLOBALS["r_istep"][$TPL_V1["istep"]]?></dd>
				</dl>
			</li>
<?php }}?>
		</ul>
	</div>
	
	<div class="info">
		<!-- 01 주문자정보 -->
		<h4>주문자정보</h4>
		<table>
		<tr>
			<th>주문자명</th>		<td><?php echo $TPL_VAR["nameOrder"]?></td>
		</tr>
		<tr>
			<th>주문자 전화</th>	<td><?php echo $TPL_VAR["phoneOrder"]?></td>
		</tr>
		<tr>
			<th>주문자 핸드폰</th>	<td><?php echo $TPL_VAR["mobileOrder"]?></td>
		</tr>
		<tr>
			<th>이메일</th>			<td><?php echo $TPL_VAR["email"]?></td>
		</tr>
		</table>

		<!-- 02 배송정보 -->
		<h4>배송정보</h4>
		<table>
		<tr>
			<th>받는자명</th>		<td><?php echo $TPL_VAR["nameReceiver"]?></td>
		</tr>
		<tr>
			<th>받는자 전화</th>	<td><?php echo $TPL_VAR["phoneReceiver"]?></td>
		</tr>
		<tr>
			<th>받는자 핸드폰</th>	<td><?php echo $TPL_VAR["mobileReceiver"]?></td>
		</tr>
		<tr>
			<th>우편번호</th>		<td><?php echo $TPL_VAR["zipcode"]?></td>
		</tr>
		<tr>
			<th>주소</th>			<td><?php echo $TPL_VAR["address"]?> <?php echo $TPL_VAR["address_sub"]?><div style="padding-top:5px;font:12px dotum;color:#999;"><?php echo $TPL_VAR["road_address"]?> <?php echo $TPL_VAR["address_sub"]?></div></td>
		</tr>
<?php if($TPL_VAR["memo"]){?>
		<tr>
			<th>배송메세지</th>		<td><?php echo $TPL_VAR["memo"]?></td>
		</tr>
<?php }?>
<?php if($TPL_VAR["deliverycode"]){?>
		<tr>
			<th>송장번호</th>		<td><?php echo $TPL_VAR["deliverycomp"]?> <?php echo $TPL_VAR["deliverycode"]?></td>
		</tr>
<?php }?>
		</table>

		<!-- 03 결제금액 -->
		<h4>결제금액</h4>
		<table>
		<tr>
			<th>총주문금액</th>
			<td><span id="paper_goodsprice"><?php echo number_format($TPL_VAR["goodsprice"])?></span>원</td>
		</tr>
		<tr>
			<th>배송비</th>
			<td>
				<div id="paper_delivery_msg1" <?php if($TPL_VAR["deli_msg"]){?>style="display:none"<?php }?>><span id="paper_delivery"><?php echo number_format($TPL_VAR["delivery"])?></span>원</div>
				<div id="paper_delivery_msg2" style="float:left;margin:0;" <?php if(!$TPL_VAR["deli_msg"]){?>style="display:none"<?php }?>><?php echo $TPL_VAR["deli_msg"]?></div>
			</td>
		</tr>
		<tr>
			<th>회원할인</th>
				<td>- <span id="paper_memberdc"><?php echo number_format($TPL_VAR["memberdc"])?></span>원</td>
				
		</tr>
		<tr>
			<th>쿠폰할인</th>
				<td>- <span id="paper_coupon"><?php echo number_format($TPL_VAR["coupon"])?></span>원</td>
		</tr>
		<tr>
			<th>적립금 사용</th>
				<td>- <span id="paper_emoney"><?php echo number_format($GLOBALS["data"]["emoney"])?></span>원</td>
		</tr>
		<?php echo $TPL_VAR["NaverMileageAmount"]?>

<?php if($TPL_VAR["eggFee"]){?>
		<tr>
			<th>보증보험 수수료</th>
			<td><span id="paper_eggfee"><?php echo number_format($TPL_VAR["eggFee"])?></span>원</td>
		</tr>
<?php }?>
		<tr>
			<th>결제금액</th>
			<td><b><span id="paper_settlement"><?php echo number_format($TPL_VAR["settleprice"])?></span>원</b></td>
		</tr>
		</table>

		<!-- 04 결제수단 -->
		<h4>결제수단</h4>
		<table>
<?php if($TPL_VAR["settlekind"]=="a"){?>
		<tr>
			<th>입금은행</th>
			<td><?php echo $TPL_VAR["bank"]?></td>
		</tr>
		<tr>
			<th>입금계좌</th>
			<td><?php echo $TPL_VAR["account"]?></td>
		</tr>
		<tr>
			<th>예금주명</th>
			<td><?php echo $TPL_VAR["name"]?></td>
		</tr>
		<tr>
			<th>입금자명</th>
			<td><?php echo $TPL_VAR["bankSender"]?></td>
		</tr>
<?php }elseif($TPL_VAR["settlekind"]=="c"){?>
		<tr>
			<th>결제방법</th>
			<td>신용카드</td>
		</tr>
<?php }elseif($TPL_VAR["settlekind"]=="o"){?>
		<tr>
			<th>결제방법</th>
			<td>계좌이체</td>
		</tr>
<?php }elseif($TPL_VAR["settlekind"]=="v"){?>
		<tr>
			<th>가상계좌</th>
			<td><?php echo $TPL_VAR["vAccount"]?></td>
		</tr>
<?php }elseif($TPL_VAR["settlekind"]=="p"){?>
		<tr>
			<th>결제방법</th>
			<td>포인트결제</td>
		</tr>
<?php }elseif($TPL_VAR["settlekind"]=="d"){?>
		<tr>
			<th>결제방법</th>
			<td>전액할인 결제 (적립금 사용)</td>
		</tr>
<?php if($TPL_VAR["memberdc"]){?>
		<tr>
			<th>회원할인</th>
			<td id="memberdc"><?php echo number_format($TPL_VAR["memberdc"])?>원</td>
		</tr>
<?php }?>
<?php if($TPL_VAR["coupon"]){?>
		<tr>
			<th>쿠폰할인</th>
			<td><?php echo number_format($TPL_VAR["coupon"])?>원</td>
		</tr>
<?php }?>
		<tr>
			<th>적립금결제</th>
			<td><b><?php echo number_format($TPL_VAR["emoney"])?>원</b></td>
		</tr>
<?php }?>
<?php if($TPL_VAR["step"]== 0&&$TPL_VAR["step2"]== 54&&in_array($TPL_VAR["settlekind"],array('c','o','v'))&&$TPL_VAR["pgfailreason"]){?><!-- 결제실패사유 -->
		<tr>
			<th>결제실패사유</th>
			<td><?php echo $TPL_VAR["pgfailreason"]?></td>
		</tr>
<?php }?>
<?php if($TPL_VAR["eggyn"]=='y'){?>
		<tr>
			<th>전자보증보험</th>
			<td><a href="javascript:popupEgg('<?php echo $GLOBALS["egg"]["usafeid"]?>', '<?php echo $TPL_VAR["ordno"]?>')"><font color=#0074BA><b><u><?php echo $TPL_VAR["eggno"]?> <font class=small>(보증서출력)</a></td>
		</tr>
<?php }elseif($GLOBALS["egg"]["use"]=='N'&&$TPL_VAR["eggyn"]=='f'){?>
		<tr>
			<th>전자보증보험</th>
			<td>보증서 발급 실패.</td>
		</tr>
<?php }elseif($GLOBALS["egg"]["use"]=='Y'&&$TPL_VAR["eggyn"]=='f'){?>
		<tr>
			<th>전자보증보험</th>
			<td>보증서 발급 실패. 재발급 받으세요.</td>
		</tr>
<?php }?>
		</table>

	</div>
				
	<div class="btn_area">
<?php if($GLOBALS["cfg"]["reOrder"]=='y'){?>
	<div class="btn_reorder inblock" onclick="window.location.href='settle.php?ordno=<?php echo $TPL_VAR["ordno"]?>'">재주문</div>
<?php }?>
<?php if($TPL_VAR["step2"]>= 50&&$GLOBALS["cfg"]["RecoverCoupon"]=='y'&&$TPL_VAR["recovery_coupon"]=='n'){?>
	<div class="btn_recoupon inblock" id="couponCancel">쿠폰사용취소</div>
<?php }?>
	</div>

</form>

</section>

<?php $this->print_("footer",$TPL_SCP,1);?>