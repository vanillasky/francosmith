<?php /* Template_ 2.2.7 2014/03/05 23:19:40 /www/francotr3287_godo_co_kr/shop/data/skin/campingyo/mypage/mypage_wishlist.htm 000004163 */ 
if (is_array($TPL_VAR["loop"])) $TPL_loop_1=count($TPL_VAR["loop"]); else if (is_object($TPL_VAR["loop"]) && in_array("Countable", class_implements($TPL_VAR["loop"]))) $TPL_loop_1=$TPL_VAR["loop"]->count();else $TPL_loop_1=0;?>
<?php $this->print_("header",$TPL_SCP,1);?>


<!-- 상단이미지 || 현재위치 -->
<TABLE width=100% cellpadding=0 cellspacing=0 border=0>
<tr>
	<td><img src="/shop/data/skin/campingyo/img/common/title_wishlist.gif" border=0></td>
</tr>
<TR>
	<td class="path">HOME > 마이페이지 > <B>상품보관함</B></td>
</TR>
</TABLE>


<div class="indiv"><!-- Start indiv -->

<script>
function act(mode)
{
	var fm = document.frmWish;
	if (isChked('sno[]')){
		fm.mode.value = mode;
		fm.submit();
	}
}
</script>


<form name=frmWish method=post>
<input type=hidden name=mode>

<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr><td height=2 bgcolor="#303030" colspan=10></td></tr>
<tr bgcolor=#F0F0F0 height=23 class=input_txt>
	<th><a href="javascript:chkBox('sno[]','rev')">선택</a></th>
	<th colspan=2>상품정보</th>
	<th>적립금</th>
	<th>판매가</th>
	<th>보관날짜</th>
</tr>
<tr><td height=1 bgcolor="#D6D6D6" colspan=10></td></tr>
<col width=30 align=center><col width=60 align=center><col><col width=60 align=center>
<col width=80 align=right style="padding-right:10"><col width=100 align=center>
<?php if($TPL_loop_1){foreach($TPL_VAR["loop"] as $TPL_V1){?>
<input type=hidden name=goodsno[<?php echo $TPL_V1["sno"]?>] value="<?php echo $TPL_V1["goodsno"]?>">
<input type=hidden name=opt[<?php echo $TPL_V1["sno"]?>][] value="<?php echo implode('|',$TPL_V1["opt"])?>">
<?php if((is_array($TPL_R2=$TPL_V1["r_addopt"])&&!empty($TPL_R2)) || (is_object($TPL_R2) && in_array("Countable", class_implements($TPL_R2)) && $TPL_R2->count() > 0)) {foreach($TPL_R2 as $TPL_V2){?><input type=hidden name=addopt[<?php echo $TPL_V1["sno"]?>][] value="<?php echo $TPL_V2?>"><?php }}?>
<?php if((is_array($TPL_R2=$TPL_V1["r_addopt_inputable"])&&!empty($TPL_R2)) || (is_object($TPL_R2) && in_array("Countable", class_implements($TPL_R2)) && $TPL_R2->count() > 0)) {foreach($TPL_R2 as $TPL_V2){?><input type=hidden name=addopt_inputable[<?php echo $TPL_V1["sno"]?>][] value="<?php echo $TPL_V2?>"><?php }}?>
<tr>
	<td><input type=checkbox name=sno[] value="<?php echo $TPL_V1["sno"]?>">
	<td height=60>
	<a href="<?php echo url("goods/goods_view.php?")?>&goodsno=<?php echo $TPL_V1["goodsno"]?>"><?php echo goodsimg($TPL_V1["img_s"], 40)?></a>
	</td>
	<td>
	<div><a href="<?php echo url("goods/goods_view.php?")?>&goodsno=<?php echo $TPL_V1["goodsno"]?>"><?php echo $TPL_V1["goodsnm"]?></a> <?php if($TPL_V1["opt"]){?>[<?php echo implode("/",$TPL_V1["opt"])?>]<?php }?></div>
<?php if($TPL_V1["addopt"]){?>
	<div>추가옵션 : <?php if((is_array($TPL_R2=$TPL_V1["addopt"])&&!empty($TPL_R2)) || (is_object($TPL_R2) && in_array("Countable", class_implements($TPL_R2)) && $TPL_R2->count() > 0)) {foreach($TPL_R2 as $TPL_V2){?>[<?php echo $TPL_V2["optnm"]?>:<?php echo $TPL_V2["opt"]?>]<?php }}?></div>
<?php }?>
<?php if($TPL_V1["addopt_inputable"]){?>
	<div>입력옵션 : <?php if((is_array($TPL_R2=$TPL_V1["addopt_inputable"])&&!empty($TPL_R2)) || (is_object($TPL_R2) && in_array("Countable", class_implements($TPL_R2)) && $TPL_R2->count() > 0)) {foreach($TPL_R2 as $TPL_V2){?>[<?php echo $TPL_V2["optnm"]?>:<?php echo $TPL_V2["opt"]?>]<?php }}?></div>
<?php }?>
	</td>
	<td><?php echo number_format($TPL_V1["reserve"])?>원</td>
	<td><?php echo number_format($TPL_V1["price"]+$TPL_V1["addprice"])?>원</td>
	<td><?php echo substr($TPL_V1["regdt"], 0, 10)?></td>
</tr>
<tr><td colspan=10 height=1 bgcolor=#DEDEDE></td></tr>
<?php }}?>
</table><p>

<center>
<a href="javascript:act('delItem')"><img src="/shop/data/skin/campingyo/img/common/btn_select_delete.gif"></a>
<a href="javascript:act('cart')"><img src="/shop/data/skin/campingyo/img/common/btn_cartin.gif"></a>
</center>

</form>

<div class="pagediv"><?php echo $TPL_VAR["pg"]->page['navi']?></div>

</div><!-- End indiv -->

<?php $this->print_("footer",$TPL_SCP,1);?>