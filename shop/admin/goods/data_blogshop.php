<?
$location = "상품관리 > 블로그샵 상품정보 일괄이전";
include "../_header.php";
$blogshop = new blogshop();
$result=array();
if($blogshop->linked) {
	$result = $blogshop->get_inip2p_goods();

}

?>
<script src="../../lib/js/categoryBox.js"></script>
<script type="text/javascript">
function confirmBox() {
	
	var ar_chk=document.getElementsByName('chk[]');
	var is_checked=false;
	for(i=0;i<ar_chk.length;i++) {
		if(ar_chk[i].checked) {
			is_checked=true;
		}
	}
	if(!is_checked) {
		alert("전환시킬 상품을 선택해 주세요");
	}
	
	var is_selected=false;
	var ar_cate=document.getElementsByName('cate[]');
	for(i=0;i<ar_cate.length;i++) {
		if(ar_cate[i].selectedIndex) {
			is_selected=true;
		}
	}
	
	if(!is_selected) {
		alert("쇼핑몰의 카테고리를 선택해주세요");
	}

	if(is_selected && is_checked) {
		return true;
	}
	else {
		return false;
	}
}
</script>

<form name="fmList" method="post" onsubmit="return confirmBox()" action="data_blogshop.process.php">

<div class="title title_top">블로그샵 상품정보 일괄이전<span>이니P2P와 연동된 블로그샵 상품을 쇼핑몰 상품으로 전환시킵니다</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=21')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a></div>
<br>

<div style="padding:8px 13px;backg서ound:#f7f7f7;border:3px solid #C6C6C6;margin-bottom:18px;color:#777777;" id="goodsInfoBox">
<div><font color="#EA0095"><b>필독! 블로그샵 상품 이동에 대해서</b></font></div>
<div style="padding-top:2">① 블로그샵을 이용하시다가 쇼핑몰을 추가하신 경우 블로그샵에 있는 INIP2P연동상품을 쇼핑몰연동상품으로 전환하는 기능입니다.</div>
<div style="padding-top:2">② 전환된 이후에는 블로그샵에 있는 상품구매버튼 클릭시 INIP2P가 아닌 쇼핑몰로 이동됩니다.</div>
<div style="padding-top:2">③ ...</div>

</div>

<div style="padding:10 0 5 5"><font class="def1" color="#EA0095"><b><font size="3">①</font> 쇼핑몰 상품으로 전환 할 블로그샵 상품을 선택하세요</b></font></div>
<table width=100% cellpadding=0 cellspacing=0 border=0>
<col width=70><col width=70><col><col width=150>
<tr><td class=rnd colspan=5></td></tr>
<tr height=35 bgcolor=4a3f38>
	<th><a href="javascript:chkBox(document.getElementsByName('chk[]'),'rev')" class=white><font class=small1><b>선택</b></a></th>
	<th><font class=small1 color=white><b>번호</b></th>
	<th><font class=small1 color=white><b>상품명</b></th>
	<th><font class=small1 color=white><b>가격</b></th>
</tr>
<tr><td class=rnd colspan=5></td></tr>

<? foreach ($result as $k=>$v) :?>
<tr><td height=4 colspan=12></td></tr>
<tr>
	<td align=center class="noline"><input type='checkbox' name="chk[]" value="<?=$v['goodsno']?>"></td>
	<td align=center><font class="ver8" color="#616161"><?=($k+1)?></td>
	<td><?=$v['goodsnm']?></td>
	<td align=center><?=number_format($v['price'])?>원</td>
</tr>
<tr><td height=4></td></tr>
<tr><td colspan=12 class=rndline></td></tr>
<? endforeach; ?>
</table>

<br><br>
<div style="padding:10 0 5 5"><font class="def1" color="#EA0095"><b><font size="3">②</font> 이전 될 쇼핑몰 카테고리를 선택 합니다</b></font></div>

<div style="padding:5px;border:1px solid #cccccc">
	<script>new categoryBox('cate[]',4,'<?=$category?>');</script>
</div>




<div style="text-align:center">
<input type="submit" value="다음" style="width:150px;height:40px;padding:15px;margin:20px">
</div>
</form>

<? include "../_footer.php"; ?>