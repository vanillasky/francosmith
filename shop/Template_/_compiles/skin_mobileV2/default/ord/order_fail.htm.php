<?php /* Template_ 2.2.7 2013/05/27 11:58:31 /www/francotr3287_godo_co_kr/shop/data/skin_mobileV2/default/ord/order_fail.htm 000003117 */ ?>
<?php $this->print_("header",$TPL_SCP,1);?>


<?php  $TPL_VAR["page_title"] = "주문실패";?>
<?php $this->print_("sub_header",$TPL_SCP,1);?>

<style type="text/css">
section#order_fail {background:#FFFFFF; padding:none; margin:none;}
section#order_fail {background:#FFFFFF; padding:12px;font-family:dotum;font-size:12px;}
section#order_fail .sub_title{height:22px; line-height:22px; color:#436693; font-weight:bold; font-size:12px;}
section#order_fail .sub_title .point {width:4px; height:22px; background:url('/shop/data/skin_mobileV2/default/common/img/bottom/icon_guide.png') no-repeat center left; float:left; margin-right:7px;}
section#order_fail table{border:none; border-top:solid 1px #dbdbdb;width:100%; margin-bottom:20px;}
section#order_fail table td{padding:8px 0px 8px 10px; vertical-align:middle; border-bottom:solid 1px #dbdbdb;}
section#order_fail table th{text-align:center; background:#f5f5f5; width:100px; vertical-align:middle; border-bottom:solid 1px #dbdbdb; color:#353535; font-size:12px;}

section#order_fail table td input[type=text], input[type=password], input[type=email], input[type=number], select{height:21px;}
section#order_fail table td textarea{width:95%;height:116px;}
section#order_fail .btn_center {margin:auto; width:198px; height:34px; margin-top:20px; margin-bottom:20px;}
section#order_fail .btn_center .submit{border:none; background:url('/shop/data/skin_mobileV2/default/common/img/layer/btn_red01_off.png') no-repeat; color:#FFFFFF; font-size:14px; width:94px; height:34px; float:left; font-family:dotum; line-height:34px;}
section#order_fail .btn_center .cancel{border:none; background:url('/shop/data/skin_mobileV2/default/common/img/layer/btn_black01_off.png') no-repeat; color:#FFFFFF; font-size:14px; width:94px; height:34px; float:right; font-family:dotum; line-height:34px;}

.max_width{width:95%;}
</style>

<section id="order_fail">
<?php if($_GET["ordno"]){?>
	<table>
<?php if($TPL_VAR["settlekind"]=="o"){?>
	<tr>
		<th>결제방법</th>
		<td>계좌이체</td>
	</tr>
	<tr>
		<th>결과</th>
		<td>주문이 실패되었습니다</td>
	</tr>
<?php }elseif($TPL_VAR["settlekind"]=="c"){?>
	<tr>
		<th>결제방법</th>
		<td>신용카드</td>
	</tr>
	<tr>
		<th>결과</th>
		<td>주문이 실패되었습니다</td>
	</tr>
<?php }?>
<?php if($TPL_VAR["step"]== 0&&$TPL_VAR["step2"]> 50&&in_array($TPL_VAR["settlekind"],array('c','o','v'))&&$TPL_VAR["pgfailreason"]){?><!-- 결제실패사유 -->
	<tr>
		<th>결제실패사유</th>
		<td><?php echo $TPL_VAR["pgfailreason"]?></td>
	</tr>
<?php }?>
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
	</table>
<?php }?>
</section>

<?php $this->print_("footer",$TPL_SCP,1);?>