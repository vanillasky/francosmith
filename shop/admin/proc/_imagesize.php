<div class="title title_top">상품 이미지 사이즈 세팅하기 <span>상품이미지의 쓰임새에 따라 사이즈를 정합니다</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=16')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a></div>

<div style="padding:10 0 5 15"><font class=extext>아래 각 이미지 사이즈를 입력하세요. 가로사이즈의 픽셀값을 입력하면 됩니다.</div>
<div style="padding:0 0 5 15">상품이미지 등록이 <font color=E6008D>처음</font>이세요? <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=16')"><img src="../img/btn_resize_knowhow.gif" border=0 align=absmiddle></a> 을 꼭 필독하세요!</font></div>
<!--<div style="padding:1 0 0 15"><font color=E6008D>정사각형, 직사각형 이미지 둘 다 무방합니다.</font></div>
<div style="padding:1 0 5 15"><font color=E6008D>이미지의 가로/세로 비율이 달라도 그 비율에 맞게 자동리사이징 됩니다.</font></div>
-->

<form method=post action="../proc/indb.php">
<input type=hidden name=mode value="imgSize">
<table class=tb>
<col class=cellC><col class=cellL>
<tr height=33>
	<td rowspan="5">PC 이미지</td>
	<td>확대(원본)이미지</td>
	<td>가로 <input type=text name=img_l value="<?=$cfg[img_l]?>" size=5 class=rline> 픽셀 <font class=extext>(확대보기를 눌렀을때 보여지는 가장 큰 이미지 - 500 ~ 700 픽셀을권장합니다)</font></td>
</tr>
<tr height=33>
	<td>상세이미지</td>
	<td>가로 <input type=text name=img_m value="<?=$cfg[img_m]?>" size=5 class=rline> 픽셀 <font class=extext>(상품상세페이지에 보여지는 이미지)</td>
</tr>
<tr height=33>
	<td>리스트이미지</td>
	<td>가로 <input type=text name=img_s value="<?=$cfg[img_s]?>" size=5 class=rline> 픽셀 <font class=extext>(상품분류리스트에 보여지는 이미지)</td>
</tr>
<tr height=33>
	<td>메인이미지</td>
	<td>가로 <input type=text name=img_i value="<?=$cfg[img_i]?>" size=5 class=rline> 픽셀 <font class=extext>(메인페이지에 보여지는 이미지)</td>
</tr>
<tr height=33>
	<td>(구)모바일이미지</td>
	<td>가로 <input type=text name=img_mobile value="<?=$cfg[img_mobile]?>" size=5 class=rline> 픽셀 <font class=extext>(메인페이지에 보여지는 이미지)</td>
</tr>
</table><p>

<table class=tb>
<col class=cellC><col class=cellL>
<tr height=33>
	<td rowspan="4">모바일샵 이미지</td>
	<td>확대(원본)이미지</td>
	<td>가로 <input type=text name=img_z value="<?=$cfg[img_z]?>" size=5 class=rline> 픽셀 <font class=extext>(확대보기를 눌렀을때 보여지는 가장 큰 이미지 - 500 ~ 700 픽셀을권장합니다)</font></td>
</tr>
<tr height=33>
	<td>상세이미지</td>
	<td>가로 <input type=text name=img_y value="<?=$cfg[img_y]?>" size=5 class=rline> 픽셀 <font class=extext>(상품상세페이지에 보여지는 이미지)</td>
</tr>
<tr height=33>
	<td>리스트이미지</td>
	<td>가로 <input type=text name=img_x value="<?=$cfg[img_x]?>" size=5 class=rline> 픽셀 <font class=extext>(상품분류리스트에 보여지는 이미지)</td>
</tr>
<tr height=33>
	<td>메인이미지</td>
	<td>가로 <input type=text name=img_w value="<?=$cfg[img_w]?>" size=5 class=rline> 픽셀 <font class=extext>(메인페이지에 보여지는 이미지)</td>
</tr>
</table><p>

<div class="button_popup">
<input type=image src="../img/btn_register.gif">&nbsp;&nbsp;
<a href="javascript:parent.closeLayer()"><img src="../img/btn_cancel.gif"></a>
</div>

</form>

<div style="padding-top:15"></div>


<font color=0074BA>
<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">확대 (원본) 이미지: 상품상세페이지에서 확대보기 버튼을 눌렀을때 보이는 가장 큰 이미지입니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">확대 (원본) 이미지는 자동리사이즈 기능 사용시 원본으로 쓰이는 이미지이므로 높은 퀄리티로 저장하세요.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">상품상세이미지: 상품상세페이지에서 보여지는 이미지입니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">리스트이미지: 카테고리를 눌렀을 때 리스트상에서 보여지는 이미지입니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">메인이미지: 메인페이지에서 보여지는 이미지입니다.</td></tr>

<!--
<tr><td><img src="../img/icon_list.gif" align="absmiddle"><font color=0074BA></font>는 확대(원본)이미지 하나로 여러개의 이미지로 복사저장하는 기능입니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"><font color=E6008D>이미지의 가로/세로 비율이 달라도 그 비율에 맞게 자동리사이징 됩니다.</font></td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"><font color=0074BA>확대(원본)이미지를 가로세로 500픽셀 이상</font>의 높은 퀄러티로 만들어 등록하세요.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">만약 상품을 등록하다가 중간에 변경된 사이즈를 적용하려면 이전에 등록한 상품들도 원본이미지를<br>&nbsp;&nbsp;이용하여 다시 한번 적용시켜줘야 합니다. 처음 세팅때 사이즈를 잘 고려하세요.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">GD기능을 이용하지 않고 <font color=0074BA>각각의 상품이미지를 수동등록해도 무방합니다.</font><bR>&nbsp;&nbsp;단, 시간이 걸리는 단점이 있습니다.</td></tr>
-->
</table>
</div>
<script>cssRound('MSG01')</script>



<script>table_design_load();</script>
