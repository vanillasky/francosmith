<?php /* Template_ 2.2.7 2016/04/21 13:53:40 /www/francotr3287_godo_co_kr/shop/data/skin/freemart/service/customer.htm 000003581 */ ?>
<?php $this->print_("header",$TPL_SCP,1);?>




<!-- 상단이미지 || 현재위치 -->
<div class="page_title_div">
	<div class="page_title">&nbsp;</div>
	<div class="page_path"><a href="/shop/">HOME</a> &gt; <span class='bold'>고객센터</span></div>
</div>
<div class="page_title_line"></div>

<div style="width:100%; padding-top:20px;"></div>


<div class="indiv" style="padding-bottom:20px;"><!-- Start indiv -->

<div id="cs-banner"></div>

<div class="top-15" >
	<div class="center-comment-box" style="width:90%">
		<span class="large-comment">친절한 쇼핑안내, 최선을 다해 친절하게 상담해드립니다.</span>
		<div class="dotted-bline"></div>
	</div>
	
	<div id="cs-faq-search-box" class="position:relative;">
		<form name="search-form" action="/shop/service/faq.php">
		<div style="width:160px; float:left; display:inline-block; padding-top:20px;padding-left:10px">
			<span class="large-comment">무엇을 도와드릴까요?</span>
		</div>
		
		<div style="float:left;display:inline-block;padding-top:10px;">
			<table class="cs-search_table">
			<tr>
				<td class="cs-search_td"><input name=sword type=text id="<?php echo $TPL_VAR["id"]?>" class="cs-search_input" onkeyup="<?php echo $TPL_VAR["onkeyup"]?>" onclick="<?php echo $TPL_VAR["onclick"]?>" value="<?php echo $TPL_VAR["value"]?>" required label="검색어"></td>
				<td class="search_btn_top top_red"><button type="submit" title="Search" class="button cs-search-button"></td>
			</tr>
			</table>
		</div>
		</form>		
		<div style="width:100px; margin-left:3px; float:left; padding-top:8px;">
			<button class="button-big-cs button-dark" onClick='javascript:goFaq();'>FAQ 더보기</button>
		</div>
		
	</div>
</div>

<div class="top-15" style="width:90%; margin:0 auto;">
	<div class="left-comment-box" style="text-align:left">
		<p><span class="cs-title">Email</span><span class="cs-data">cs@francosmith.com</span></p>
		<p><span class="cs-title">카톡 주문/상담</span><span class="cs-data">@francosmith</span></p>
		<p><span class="cs-title">제휴/도매문의</span><span class="cs-data">mailroom@francosmith.com</span></p>
		<p><span class="cs-title">목공/목선반 교육</span><span class="cs-data">shkim@francosmith.com</span></p>
		<p><span class="cs-title">공방창업 컨설팅</span><span class="cs-data">shkim@francosmith.com</span></p>
	</div>
</div>

<script type="text/javascript">
	function goFaq() {
		location.href="/shop/service/faq.php?faq_sword=";
	}
</script>


<div class="top-15" style="width:90%; margin:0 auto;">
	<div class="left-comment-box" style="text-align:left; border-top:1px dotted #C9242B">
		<p class="bline"><span class="cs-title boldf">주문상담</span><span class="cs-title">오전 09:30 ~ 오후 6시(월~금)</span></p>
		<p class="bline"><span class="cs-title boldf">제품문의</span><span class="cs-title">오전 09:30 ~ 오후 4시(월~금)</span></p>
		<p class="bline"><span class="cs-title boldf">비즈니스</span><span class="cs-title">오전 09:30 ~ 오후 4시(월~금)</span></p>
		<p class="bline"><span class="cs-title boldf">교육문의</span><span class="cs-title">오전 10:00 ~ 오후 5시(월~토)</span><span class="cs-title">TEL) 010-5472-4755</span></p>
		<p class="bline"><span class="cs-title boldf">공방창업</span><span class="cs-title">오전 10:00 ~ 오후 5시(월~토)</span><span class="cs-title">TEL) 010-5472-4755</span></p>
	</div>
</div>


</div><!-- End indiv -->

<?php $this->print_("footer",$TPL_SCP,1);?>