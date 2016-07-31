<?php /* Template_ 2.2.7 2016/05/29 16:16:37 /www/francotr3287_godo_co_kr/shop/data/skin/freemart/outline/footer/standard.htm 000004967 */  $this->include_("dataBank");?>
<div id="footer_wrapper" class="bottom_grey" align="center" >
	<div style="width:<?php echo $GLOBALS["cfg"]['shopSize']?>px; padding:0; margin:0" > 
		<div class="footer-block" style="width:220px;">
			<div class="block-title">
				<span>입금안내</span>
			</div>
			<div class="content-wrapper">
<?php if((is_array($TPL_R1=dataBank())&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?>
				<span class="footer-content"><?php echo $TPL_V1["bank"]?></span>
				<span class="footer-content"><?php echo $TPL_V1["account"]?></span>
				<span class="footer-content">예금주 <?php echo $TPL_V1["name"]?></span>
<?php }}?> 
			</div>
			
		</div>
		
		<div class="footer-block">
			<div class="block-title">
				<span>상담/문의</span>
			</div>
			<div class="footer-phone">
				<span>TEL. <?php echo $GLOBALS["cfg"]['compPhone']?></span>
				<span style="margin-left:10px;">FAX. <?php echo $GLOBALS["cfg"]['compFax']?></span>
			</div>
			<div class="footer-biz-time">
				<ul>
						<li>상담시간: AM 9:30 ~ PM 6:00 (월~금)</li>
				</ul>
			</div>
		</div>
		
		
		<div class="footer-block social-footer">
			<div class="block-title">
				<span style="padding-left:10px;">Keep In Touch</span>
			</div>
			<ul>
				<li><a id="facebook" target="_blank" href="https://www.facebook.com/FrancosmithTools"></a></li>
				<li><a id="twitter" target="_blank" href="#"></a></li>
				<li><a id="googleplus" target="_blank" href="#"></a></li>
				<li><a id="pinterest" target="_blank" href="#""></a></li>
				<li><a id="instagram" target="_blank" href="#"/"></a></li>
				<!--<li><a id="youtube" target="_blank" href="#""></a></li>-->
			</ul>
		</div>
		
		
	</div>	
	
	<p style="clear:both; height:10px;padding:0; margin:0">&nbsp;</p>
	<hr class="line-white">
	
	<div style="width:<?php echo $GLOBALS["cfg"]['shopSize']?>px; padding:0; margin:0;" > 	
		<div class="footer-block" style="width:220px;">
			<div class="block-title">
				<span>YOUR ORDER</span>
			</div>
			<div class="footer-biz-time">
				<ul>
					<li class="quick-link"><a href="/shop/mypage/mypage.php">My Account</a></li>
					<li class="quick-link"><a href="/shop/mypage/mypage_orderlist.php">주문조회</a></li>
					<li class="quick-link"><a href="https://www.doortodoor.co.kr/parcel/pa_004.jsp" target="new">배송조회</a></li>
					<li class="quick-link"><a href="/shop/service/customer.php">고객서비스</a></li>
					<li class="quick-link"><a href="/shop/main/html.php?htmid=service/return_policy.htm">배송/반품안내</a></li>
				</ul>
			</div>
		</div>
		
		
		<div class="footer-block" style="width:220px;">
			<div class="block-title">
				<span>THE KNOWLEDGE</span>
			</div>
			<div class="footer-biz-time">
				<ul>
					<li class="quick-link"><a href="http://blog.naver.com/francosmith">블로그</a></li>
					<li class="quick-link"><a href="/shop/board/list.php?id=qna">Q&amp;A</a></li>
					<li class="quick-link"><a href="/shop/board/list.php?id=bbs">자료실</a></li>
					<li class="quick-link"><a href="/shop/board/list.php?id=buyingguide">구매가이드</a></li>
				</ul>
			</div>
		</div>
		
		<div class="footer-block" style="width:220px;">
			<div class="block-title">
				<span>ANNOUNCEMENT</span>
			</div>
			<div class="footer-biz-time">
				<ul>
					<li class="quick-link"><a href="/shop/board/list.php?id=notice">공지사항</a></li>
					<li class="quick-link"><a href="http://francosmith.com/shop/service/faq.php?&faq_sword=">쇼핑 FAQ</a></li>
				</ul>
			</div>
		</div>
		
	</div>
</div>

<div style="width:100%; padding:0; margin:0 auto;" align="center" >
<div style="width:990px; background-color:#fff" > 	
		
		<div id="footer_company">
			<p class="line_h20">
				<span class="txt">회사:</span><?php echo $GLOBALS["cfg"]['compName']?><span class="bar"></span>
				<span class="txt">대표:</span><?php echo $GLOBALS["cfg"]['ceoName']?> <span class="bar"></span> 
				<span class="txt">사업자등록번호:</span><?php echo $GLOBALS["cfg"]['compSerial']?> <a href="http://www.ftc.go.kr/info/bizinfo/communicationList.jsp" target="_blank">
				[조회]</a><span class="bar"></span>
				<span class="txt">통신판매업신고:</span><?php echo $GLOBALS["cfg"]['orderSerial']?><span class="bar"></span> 
				<!--<span class="txt">개인정보관리자:</span><?php echo $GLOBALS["cfg"]['adminName']?> -->
				<span class="txt">주소:</span><?php echo $GLOBALS["cfg"]['address']?> <span class="bar"></span>
				<p>
					<span style="clear:both;">COPYRIGHT ⓒ <script type="text/javascript">document.write(new Date().getFullYear())</script> FRANCOSMITH.COM ALL RIGHTS RESERVED</span>
				</p>
			</p>
			
		</div>
</div>
</div>