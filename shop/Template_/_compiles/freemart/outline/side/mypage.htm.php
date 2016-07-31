<?php /* Template_ 2.2.7 2016/04/07 16:56:37 /www/francotr3287_godo_co_kr/shop/data/skin/freemart/outline/side/mypage.htm 000002837 */  $this->include_("dataBanner");?>
<div id="left_mypage" style="width:<?php echo $GLOBALS["cfg"]['shopSideSize']?>px;">
	<div class="title_mypage"><a href="<?php echo url("mypage/mypage.php")?>&">MY ACCOUNT</a></div>
	<div class="line_cs"></div>
	<div class="line_mypage"></div>
	<!-- 쇼핑정보박스 : START -->
	<div id="mem_box">
		<span style="font-weight:bold; color:#343434; padding-left:3px;"><?php echo $GLOBALS["member"]["name"]?></span> 님의 쇼핑정보
		<div class="line_mypage2"></div>
		<table cellpadding=0 cellspacing=0 border=0>
		<tr><td align="left"><?php $this->print_("myBox",$TPL_SCP,1);?></td></tr>
		</table>
	</div>
	<!-- 쇼핑정보박스 : END -->

	<!-- 마이페이지 메뉴 시작 -->
	<div class="mypage-separator"></div>
	
	<div id="mypage-menu">
		<ul>
			<li><a href="<?php echo url("member/myinfo.php")?>&">회원정보수정</a></li>
			<li><a href="<?php echo url("member/hack.php")?>&">회원탈퇴</a></li>
		</ul>
	</div>
	
	<div class="mypage-separator"></div>
	
	<div id="mypage-menu">
		<ul>
			<li><a href="<?php echo url("mypage/mypage_orderlist.php")?>&" >주문내역/배송조회</a></li>
			<li><a href="<?php echo url("mypage/mypage_emoney.php")?>&" >적립금내역</a></li>
			<li><a href="<?php echo url("mypage/mypage_coupon.php")?>&" >할인쿠폰내역</a></li>
			<li><a href="<?php echo url("mypage/mypage_wishlist.php")?>&" >상품보관함</a></li>
		</ul>
	</div>
	
	<div class="mypage-separator"></div>
	
	<div id="mypage-menu">
		<ul>
			<li><a href="<?php echo url("mypage/mypage_qna.php")?>&" style="color:#525252">1:1 문의게시판</a></li>
			<li><a href="<?php echo url("mypage/mypage_review.php")?>&" style="color:#525252">나의 상품후기</a></li>
			<li><a href="<?php echo url("mypage/mypage_qna_goods.php")?>&" style="color:#525252">나의 상품문의</a></li>
			<li><a href="<?php echo url("mypage/mypage_today.php")?>&" style="color:#525252">최근 본 상품 목록</a></li>
		</ul>
	</div>
	<!-- 마이페이지 메뉴 끝 -->
</div>

<!-- 메인왼쪽배너 : Start -->
<table cellpadding="0" cellspacing="0" border="0"width=100%>
<tr><td align="left"><!-- (배너관리에서 수정가능) --><?php if((is_array($TPL_R1=dataBanner( 4))&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?><?php echo $TPL_V1["tag"]?><?php }}?></td></tr>
<tr><td align="left"><!-- (배너관리에서 수정가능) --><?php if((is_array($TPL_R1=dataBanner( 5))&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?><?php echo $TPL_V1["tag"]?><?php }}?></td></tr>
</table>
<!-- 메인왼쪽배너 : End -->