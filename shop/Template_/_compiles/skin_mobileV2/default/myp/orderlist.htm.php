<?php /* Template_ 2.2.7 2013/05/27 11:58:31 /www/francotr3287_godo_co_kr/shop/data/skin_mobileV2/default/myp/orderlist.htm 000003727 */ 
if (is_array($TPL_VAR["loop"])) $TPL_loop_1=count($TPL_VAR["loop"]); else if (is_object($TPL_VAR["loop"]) && in_array("Countable", class_implements($TPL_VAR["loop"]))) $TPL_loop_1=$TPL_VAR["loop"]->count();else $TPL_loop_1=0;?>
<?php $this->print_("header",$TPL_SCP,1);?>


<?php  $TPL_VAR["page_title"] = "주문내역";?>
<?php $this->print_("sub_header",$TPL_SCP,1);?>

<style type="text/css">
section#norderlist{background:#FFFFFF;}

section#norderlist {background:#FFFFFF; padding:12px;font-family:dotum;font-size:12px;}
section#norderlist .sub_title{height:25px; line-height:25px; color:#436693; font-weight:bold; font-size:12px; margin-bottom:10px;}
section#norderlist .sub_title .point {width:4px; height:22px; background:url('/shop/data/skin_mobileV2/default/common/img/bottom/icon_guide.png') no-repeat center left; float:left; margin-right:7px;}
section#norderlist table{border:none; border-top:solid 1px #dbdbdb;width:100%; margin-bottom:20px;}
section#norderlist table td{padding:8px 0px 8px 10px; vertical-align:middle; border-bottom:solid 1px #dbdbdb;}
section#norderlist table th{padding:8px 0px 8px 0px; text-align:center; background:#f5f5f5; width:70px; vertical-align:middle; border-bottom:solid 1px #dbdbdb; color:#353535; font-size:12px;}
section#norderlist .goods-nm{color:#353535; font-weight:bold; fonst-size:14px; margin-bottom:5px; overflow:hidden;text-overflow:ellipsis;white-space:nowrap; }
section#norderlist .goods-price{color:#f03c3c; font-size:12px;}
section#norderlist .ord_more_btn{background:url('/shop/data/skin_mobileV2/default/common/img/nmyp/btn_view_off.png') no-repeat; width:73px; height:25px; border:none; color:#FFFFFF; text-align:center;line-height:25px; float:right;}
section#norderlist .ord_more_btn:active;{background:url('/shop/data/skin_mobileV2/default/common/img/nmyp/btn_view_on.png') no-repeat;}

section#norderlist .more-btn {width:296px; margin:auto; background:url("/shop/data/skin_mobileV2/default/common/img/nlist/btn_more_off.png") no-repeat; text-align:center; height:38px; color:#ffffff; line-height:38px; font-size:14px; margin-top:18px; }
section#norderlist .more-btn:active {background:url("/shop/data/skin_mobileV2/default/common/img/nlist/btn_more_on.png") no-repeat; }{background:url('/shop/data/skin_mobileV2/default/common/img/nmyp/btn_view_on.png') no-repeat;}
</style>

<script type="text/javascript">
$(document).ready(function(){
	if($("#norderlist-area table").length < 10) {
		$(".more-btn").hide();
	}
});
</script>

<section id="norderlist" class="content">
<div id="norderlist-area">
<?php if($TPL_loop_1){foreach($TPL_VAR["loop"] as $TPL_V1){?>
<div class="sub_title"><div class="point"></div>주문번호 : <?php echo $TPL_V1["ordno"]?><button class="ord_more_btn" onclick="javascript:location.href='./orderview.php?ordno=<?php echo $TPL_V1["ordno"]?>';">상세보기</button></div>
<table>
<tr>
	<th>상품명</th>
	<td class="goods-nm"><?php echo $TPL_V1["goodsnm"]?></td>
</tr>
<tr>
	<th>주문일시</th>
	<td><?php echo $TPL_V1["orddt"]?></td>
</tr>
<tr>
	<th>결제방법</th>
	<td><?php echo $TPL_V1["str_settlekind"]?></td>
</tr>
<tr>
	<th>주문금액</th>
	<td class="goods-price"><?php echo number_format($TPL_V1["settleprice"])?>원</td>
</tr>
<tr>
	<th>주문상태</th>
	<td><?php echo $TPL_V1["str_step"]?></td>
</tr>
</table>
<?php }}else{?>
<table>
<tr>
	<td>주문 내역이 없습니다</td>
</tr>
</table>
<?php }?>
</div>

<div class="more-btn" onclick="javascript:getOrderListData();">더보기</div>

</section>

<?php $this->print_("footer",$TPL_SCP,1);?>