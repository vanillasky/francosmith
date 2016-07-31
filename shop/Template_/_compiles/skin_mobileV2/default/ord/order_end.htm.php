<?php /* Template_ 2.2.7 2013/05/28 10:37:12 /www/francotr3287_godo_co_kr/shop/data/skin_mobileV2/default/ord/order_end.htm 000005032 */ ?>
<?php $this->print_("header",$TPL_SCP,1);?>


<?php  $TPL_VAR["page_title"] = "주문완료";?>
<?php $this->print_("sub_header",$TPL_SCP,1);?>

<style type="text/css">
section#order_end {background:#FFFFFF; padding:none; margin:none;}
section#order_end {background:#FFFFFF; padding:12px;font-family:dotum;font-size:12px;}
section#order_end .sub_title{height:22px; line-height:22px; color:#436693; font-weight:bold; font-size:12px;}
section#order_end .sub_title .point {width:4px; height:22px; background:url('/shop/data/skin_mobileV2/default/common/img/bottom/icon_guide.png') no-repeat center left; float:left; margin-right:7px;}
section#order_end table{border:none; border-top:solid 1px #dbdbdb;width:100%; margin-bottom:20px;}
section#order_end table td{padding:8px 0px 8px 10px; vertical-align:middle; border-bottom:solid 1px #dbdbdb;}
section#order_end table th{text-align:center; background:#f5f5f5; width:100px; vertical-align:middle; border-bottom:solid 1px #dbdbdb; color:#353535; font-size:12px;}

section#order_end table td input[type=text], input[type=password], input[type=email], input[type=number], select{height:21px;}
section#order_end table td textarea{width:95%;height:116px;}
section#order_end .btn_center {margin:auto; width:198px; height:34px; margin-top:20px; margin-bottom:20px;}
section#order_end .btn_center .submit{border:none; background:url('/shop/data/skin_mobileV2/default/common/img/layer/btn_red01_off.png') no-repeat; color:#FFFFFF; font-size:14px; width:94px; height:34px; float:left; font-family:dotum; line-height:34px;}
section#order_end .btn_center .cancel{border:none; background:url('/shop/data/skin_mobileV2/default/common/img/layer/btn_black01_off.png') no-repeat; color:#FFFFFF; font-size:14px; width:94px; height:34px; float:right; font-family:dotum; line-height:34px;}

.max_width{width:95%;}
</style>
<section id="order_end">

	<table>
	<col width="100" />
<?php if($TPL_VAR["settlekind"]=="a"){?>
	<tr>
		<th>입금은행</h>
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
	<tr>
		<th>입금금액</th>
		<td><b><?php echo number_format($TPL_VAR["settleprice"])?>원</b></td>
	</tr>
<?php }elseif($TPL_VAR["settlekind"]=="c"){?>
	<tr>
		<th>결제카드</th>
		<td><?php echo $_GET["card_nm"]?></td>
	</tr>
	<tr>
		<th>결제금액</th>
		<td><b><?php echo number_format($TPL_VAR["settleprice"])?>원</b></td>
	</tr>
<?php }elseif($TPL_VAR["settlekind"]=="o"){?>
	<tr>
		<th>결제방법</th>
		<td>계좌이체</td>
	</tr>
	<tr>
		<th>결제금액</th>
		<td><b><?php echo number_format($TPL_VAR["settleprice"])?>원</b></td>
	</tr>
<?php }elseif($TPL_VAR["settlekind"]=="v"){?>
	<tr>
		<th>가상계좌</th>
		<td><?php echo $TPL_VAR["vAccount"]?></td>
	</tr>
	<tr>
		<th>결제금액</th>
		<td><b><?php echo number_format($TPL_VAR["settleprice"])?>원</b></td>
	</tr>
<?php }elseif($TPL_VAR["settlekind"]=="d"){?>
	<tr>
		<th>결제방법</th>
		<td>전액할인 결제 (적립금 사용)</td>
	</tr>
<?php }?>
	<tr>
		<th>상품가격</th>
		<td><?php echo number_format($TPL_VAR["goodsprice"])?>원</td>
	</tr>
	<tr>
		<th>배송비</th>
		<td><?php if($TPL_VAR["deli_msg"]){?><?php echo $TPL_VAR["deli_msg"]?><?php }else{?><?php echo number_format($TPL_VAR["delivery"])?>원<?php }?></td>
	</tr>
<?php if($TPL_VAR["memberdc"]){?>
	<tr>
		<th>회원할인</th>
		<td><?php echo number_format($TPL_VAR["memberdc"])?>원</td>
	</tr>
<?php }?>
<?php if($TPL_VAR["coupon"]){?>
	<tr>
		<th>쿠폰할인</th>
		<td><?php echo number_format($TPL_VAR["coupon"])?>원</td>
	</tr>
<?php }?>
<?php if($TPL_VAR["coupon_emoney"]){?>
	<tr>
		<th>쿠폰적립</th>
		<td><?php echo number_format($TPL_VAR["coupon_emoney"])?>원</td>
	</tr>
<?php }?>
	<tr>
		<th>적립금결제</th>
		<td><b><?php echo number_format($TPL_VAR["emoney"])?>원</b></td>
	</tr>

	<?php echo $TPL_VAR["NaverMileageAmount"]?>

	</table>

	<table>
	<col width="100" />
	<tr>
		<th>주문번호</th>
		<td><?php echo $TPL_VAR["ordno"]?></td>
	</tr>
	<tr>
		<th>주문자명</th>
		<td><?php echo $TPL_VAR["nameOrder"]?></td>
	</tr>
	<tr>
		<th>주문일자</th>
		<td><?php echo $TPL_VAR["orddt"]?></td>
	</tr>
	<tr>
		<th>주문금액</th>
		<td><?php echo number_format($TPL_VAR["settleprice"])?>원</td>
	</tr>
<?php if($TPL_VAR["cashreceipt_useopt"]=='0'||$TPL_VAR["cashreceipt_useopt"]=='1'){?>
	<tr>
		<th>현금영수증</th>
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
</section>

<?php echo $TPL_VAR["NaverOrderCompleteData"]?>


<?php $this->print_("footer",$TPL_SCP,1);?>