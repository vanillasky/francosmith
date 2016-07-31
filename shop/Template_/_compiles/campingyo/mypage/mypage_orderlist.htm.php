<?php /* Template_ 2.2.7 2014/03/05 23:19:40 /www/francotr3287_godo_co_kr/shop/data/skin/campingyo/mypage/mypage_orderlist.htm 000003025 */ 
if (is_array($TPL_VAR["loop"])) $TPL_loop_1=count($TPL_VAR["loop"]); else if (is_object($TPL_VAR["loop"]) && in_array("Countable", class_implements($TPL_VAR["loop"]))) $TPL_loop_1=$TPL_VAR["loop"]->count();else $TPL_loop_1=0;?>
<?php $this->print_("header",$TPL_SCP,1);?>


<!-- 상단이미지 || 현재위치 -->
<TABLE width=100% cellpadding=0 cellspacing=0 border=0>
<tr>
	<td><img src="/shop/data/skin/campingyo/img/common/title_orderlist.gif" border=0></td>
</tr>
<TR>
	<td class="path">HOME > 마이페이지 > <B>주문내역/배송조회</B></td>
</TR>
</TABLE>


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

<br><table width=100% cellpadding=0 cellspacing=0 border=0>
<tr><td height=2 bgcolor="#303030" colspan=10></td></tr>
<tr bgcolor=#F0F0F0 height=23 class=input_txt>
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
<tr><td height=1 bgcolor="#D6D6D6" colspan=10></td></tr>
<?php if($TPL_loop_1){$TPL_I1=-1;foreach($TPL_VAR["loop"] as $TPL_V1){$TPL_I1++;?>
<tr>
	<td height=30 align=center><?php echo $TPL_VAR["pg"]->idx-$TPL_I1?></td>
	<td align=center><?php echo $TPL_V1["ordertypestr"]?></td>
	<td align=center><?php echo $TPL_V1["orddt"]?></td>
	<td align=center><a href="<?php echo url("mypage/mypage_orderview.php?")?>&ordno=<?php echo $TPL_V1["ordno"]?>"><?php echo $TPL_V1["ordno"]?></a></td>
	<td align=center><?php echo $TPL_V1["str_settlekind"]?></td>
	<td align=right style="padding-right:20"><?php echo number_format($TPL_V1["settleprice"])?></td>
	<td align=right style="padding-right:20"><?php echo number_format($TPL_V1["canceled_price"])?></td>
	<td align=center class=stxt><FONT COLOR="#007FC8"><?php echo $TPL_V1["str_step"]?></FONT></td>
	<td align=center>
<?php if($TPL_V1["step"]== 3&&!$TPL_V1["step2"]){?>
	<a href="javascript:order_confirm(<?php echo $TPL_V1["ordno"]?>)"><img src="/shop/data/skin/campingyo/img/common/btn_receive.gif"></a>
<?php }elseif($TPL_V1["escrowconfirm"]== 2){?>수령
<?php }?>
	</td>
	<td align=center><a href="<?php echo url("mypage/mypage_orderview.php?")?>&ordno=<?php echo $TPL_V1["ordno"]?>"><img src="/shop/data/skin/campingyo/img/common/btn_detailview.gif"></a></td>
</tr>
<tr><td colspan=10 height=1 bgcolor="#ECECEC"></td></tr>
<?php }}?>
</table>

<div class="pagediv"><?php echo $TPL_VAR["pg"]->page['navi']?></div>

</form>

</div><!-- End indiv -->

<?php $this->print_("footer",$TPL_SCP,1);?>