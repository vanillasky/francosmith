<?php /* Template_ 2.2.7 2016/04/10 14:52:20 /www/francotr3287_godo_co_kr/shop/data/skin/freemart/mypage/mypage_wishlist.htm 000004727 */ 
if (is_array($TPL_VAR["loop"])) $TPL_loop_1=count($TPL_VAR["loop"]); else if (is_object($TPL_VAR["loop"]) && in_array("Countable", class_implements($TPL_VAR["loop"]))) $TPL_loop_1=$TPL_VAR["loop"]->count();else $TPL_loop_1=0;?>
<?php $this->print_("header",$TPL_SCP,1);?>


<!-- 상단이미지 || 현재위치 -->
<div class="page_title_div">
	<div class="page_title">Wish List</div>
	<div class="page_path"><a href="/shop/">HOME</a> &gt; <a href="/shop/mypage/mypage.php">마이페이지</a> &gt; <span class='bold'>상품보관함</span></div>
</div>
<div class="page_title_line"></div>

<div style="width:100%; padding-top:20px;"></div>
<script>
function act(mode, msg)
{
	var fm = document.frmWish;
	if(!isChked('sno[]', msg)) return;
	
	fm.mode.value = mode;
	fm.submit();
}

function isChked(El,msg)
{
	var result  = false;
	if (!El) return;
	if (typeof(El)!="object") El = document.getElementsByName(El);
	
	for (i=0;i<El.length;i++) {
		if (El[i].checked) {
			result = true;
			break;
		}
	}
	
	if(!result) {
		alert ("선택된 사항이 없습니다");
		return false;
	}
	
	alert(msg);
	return false;
}

</script>
	
<div class="indiv"><!-- Start indiv -->
	
	<form name=frmWish method=post onsubmit="return false;">
	<input type=hidden name=mode>
	
	<table class="mypage-board-table">
	<colgroup>
		<col width=50 align=center>
		<col width=60 align=center>
		<col align="left">
		<col width=60 align=center>
		<col width=80 align=right style="padding-right:10">
		<col width=100 align=center>
	</colgroup>
	<tr class="mypage-board-title">
		<th><input type="checkbox" onclick="chkBox('sno[]','rev')"></th>
		<th colspan=2>제품명</th>
		<th>적립금</th>
		<th>판매가</th>
		<th>보관날짜</th>
	</tr>
		
<?php if($TPL_loop_1){foreach($TPL_VAR["loop"] as $TPL_V1){?>
	
	<tr style="border-bottom:1px solid #ededed">
		<td align="center">
			<input type=hidden name=goodsno[<?php echo $TPL_V1["sno"]?>] value="<?php echo $TPL_V1["goodsno"]?>">
			<input type=hidden name=opt[<?php echo $TPL_V1["sno"]?>][] value="<?php echo implode('|',$TPL_V1["opt"])?>">
<?php if((is_array($TPL_R2=$TPL_V1["r_addopt"])&&!empty($TPL_R2)) || (is_object($TPL_R2) && in_array("Countable", class_implements($TPL_R2)) && $TPL_R2->count() > 0)) {foreach($TPL_R2 as $TPL_V2){?><input type=hidden name=addopt[<?php echo $TPL_V1["sno"]?>][] value="<?php echo $TPL_V2?>"><?php }}?>
<?php if((is_array($TPL_R2=$TPL_V1["r_addopt_inputable"])&&!empty($TPL_R2)) || (is_object($TPL_R2) && in_array("Countable", class_implements($TPL_R2)) && $TPL_R2->count() > 0)) {foreach($TPL_R2 as $TPL_V2){?><input type=hidden name=addopt_inputable[<?php echo $TPL_V1["sno"]?>][] value="<?php echo $TPL_V2?>"><?php }}?>
	
			<input type=checkbox name=sno[] value="<?php echo $TPL_V1["sno"]?>">
		</td>
		<td height=60>
			<a href="<?php echo url("goods/goods_view.php?")?>&goodsno=<?php echo $TPL_V1["goodsno"]?>"><?php echo goodsimg($TPL_V1["img_s"], 40)?></a>
		</td>
		<td align="left">
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
	
<?php }}?>
	</table>
	
	<div class="pagediv"><?php echo $TPL_VAR["pg"]->page['navi']?></div>
	
	<div class="std-div" style="text-align:center">
		<button class="button-big button-dark" onclick="act('delItem', '정말 삭제하시겠습니까?')">선택삭제</button>
		<button class="button-big button-dark" onclick="act('cart')">장바구니담기</button>
	</div>
	</form>
	
	<p></p>

</div><!-- End indiv -->

<?php $this->print_("footer",$TPL_SCP,1);?>