<?php /* Template_ 2.2.7 2016/05/04 13:04:16 /www/francotr3287_godo_co_kr/shop/data/skin/freemart/mypage/mypage_orderlist.htm 000003035 */ 
if (is_array($TPL_VAR["loop"])) $TPL_loop_1=count($TPL_VAR["loop"]); else if (is_object($TPL_VAR["loop"]) && in_array("Countable", class_implements($TPL_VAR["loop"]))) $TPL_loop_1=$TPL_VAR["loop"]->count();else $TPL_loop_1=0;?>
<?php $this->print_("header",$TPL_SCP,1);?>


<!-- 상단이미지 || 현재위치 -->
<div class="page_title_div">
	<div class="page_title">Order List</div>
	<div class="page_path"><a href="/shop/">HOME</a> &gt; <a href="/shop/mypage/mypage.php?&">마이페이지</a> &gt; <span class='bold'>주문내역/배송조회</span></div>
</div>
<div class="page_title_line"></div>

<div style="width:100%; padding-top:20px;"></div>

<div class="indiv"><!-- Start indiv -->

<script>
function order_confirm(ordno)
{
	var fm = document.frmOrderList;
	fm.mode.value = "confirm";
	fm.ordno.value = ordno;
	fm.action = "indb.php";
	if (confirm('주문하신 상품을 수령하셨습니까?')) fm.submit();
}
</script>

<form name=frmOrderList method=post>
<input type=hidden name=mode>
<input type=hidden name=ordno>

	<table class="mypage-board-table">
	<tr class="mypage-board-title">
		<th>번호</th>
		<th>구분</th>
		<th>주문일시</th>
		<th>주문번호</th>
		<th>결제방법</th>
		<th>주문금액</th>
		<th>취소금액</th>
		<th>주문상태</th>
		<th>수령확인</th>
		<th>상세보기</th>
	</tr>
<?php if($TPL_loop_1){$TPL_I1=-1;foreach($TPL_VAR["loop"] as $TPL_V1){$TPL_I1++;?>
	<tr height=25 onmouseover=this.style.background="#F7F7F7" onmouseout=this.style.background="" style="border-bottom:1px solid #ededed">
		<td height=30 align=center><?php echo $TPL_VAR["pg"]->idx-$TPL_I1?></td>
		<td align=center><?php echo $TPL_V1["ordertypestr"]?></td>
		<td align=center><?php echo $TPL_V1["orddt"]?></td>
		<td align=center><a href="<?php echo url("mypage/mypage_orderview.php?")?>&ordno=<?php echo $TPL_V1["ordno"]?>"><?php echo $TPL_V1["ordno"]?></a></td>
		<td align=center><?php echo $TPL_V1["str_settlekind"]?></td>
		<td align=right style="padding-right:20"><?php echo number_format($TPL_V1["settleprice"])?></td>
		<td align=right style="padding-right:20"><?php echo number_format($TPL_V1["canceled_price"])?></td>
		<td align=center><FONT COLOR="#007FC8"><?php echo $TPL_V1["str_step"]?></FONT></td>
		<td align=center>
<?php if($TPL_V1["step"]== 3&&!$TPL_V1["step2"]){?>
		<a href="javascript:order_confirm(<?php echo $TPL_V1["ordno"]?>)"><img src="/shop/data/skin/freemart/img/common/btn_receive.gif"></a>
<?php }elseif($TPL_V1["escrowconfirm"]== 2){?>수령
<?php }?>
		</td>
		<td align=center><a href="<?php echo url("mypage/mypage_orderview.php?")?>&ordno=<?php echo $TPL_V1["ordno"]?>"><img src="/shop/data/skin/freemart/img/common/btn_detailview.gif"></a></td>
	</tr>
<?php }}?>
	</table>

<div class="pagediv"><?php echo $TPL_VAR["pg"]->page['navi']?></div>

</form>

</div><!-- End indiv -->

<?php $this->print_("footer",$TPL_SCP,1);?>