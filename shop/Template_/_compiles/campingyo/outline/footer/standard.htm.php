<?php /* Template_ 2.2.7 2014/04/02 01:49:33 /www/francotr3287_godo_co_kr/shop/data/skin/campingyo/outline/footer/standard.htm 000006195 */  $this->include_("dataBanner","displaySSLSeal","displayEggBanner");?>
<table cellpadding='0' cellspacing='0' border='0' width='100%' height="172" style="border-top:solid 1px #353535; border-bottom:solid 1px #d5d5d5">
	<tr>
		<td valign="top" height="36" style="padding-top:3px; background-color:#f7f7f7; border-bottom:solid 1px #d5d5d5;">
			<table border='0' cellpadding='0' width="<?php echo $GLOBALS["cfg"]['shopSize']?>" align="<?php echo $GLOBALS["cfg"]['shopAlign']?>" cellspacing='0' style="font-size:12px;" >
				<tr>
					<td height="36">
						<div style="line-height:17px;"> <a href="<?php echo url("service/company.php")?>&" style="color:#333;">회사소개 </a> <font color="#333"> | </font> <a href="<?php echo url("service/agreement.php")?>&" style="color:#333;">이용약관</a> <font color="#333"> | </font> <a href="<?php echo url("service/private.php")?>&" style="color:#333;"><strong>개인정보취급방침</strong></a> <font color="#333"> | </font> <a href="<?php echo url("service/guide.php")?>&" style="color:#333;">이용안내</a> <font color="#333"> | </font> <a href="<?php echo url("service/cooperation.php")?>&" style="color:#333;">제휴안내</a> <font color="#333"> | </font> <a href="<?php echo url("service/sitemap.php")?>&" style="color:#333;">사이트맵</a> </div>
					</td>
					<td align="right"><a href="#top"><img src="/shop/data/skin/campingyo/img/main/btn_top.gif"></a></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td valign="top">
			<table border='0' cellpadding='0' width="<?php echo $GLOBALS["cfg"]['shopSize']?>" align="<?php echo $GLOBALS["cfg"]['shopAlign']?>" cellspacing='0' style="font-size:12px;" >
				<tr>
					<td style="padding-top:10px;"> 
						<!------------ 하단 카피라이트  시작 --------------->
						<table width='100%' border='0'>
							<tr>
								<td style="font-size:12px; color:#666;">
									<div style="line-height:17px;">
									<font color='#33a5c0'>회사</font> <?php echo $GLOBALS["cfg"]['compName']?>

									<font color="#e0e0e0"> | </font> 
									<font color='#33a5c0'>주소</font> <?php echo $GLOBALS["cfg"]['address']?>

									</div>
									<div style="line-height:17px;"> 
									<font color='#33a5c0'>대표</font> <?php echo $GLOBALS["cfg"]['ceoName']?>

									<font color="#e0e0e0"> | </font> 
									<font color='#33a5c0'>사업자등록번호</font> <?php echo $GLOBALS["cfg"]['compSerial']?> <a href="http://www.ftc.go.kr/info/bizinfo/communicationList.jsp" target="_blank"><img src="/shop/data/skin/campingyo/img/common/btn_business.gif" style="vertical-align:top;"></a>
									<font color="#e0e0e0"> | </font> 
									<font color='#33a5c0'>통신판매업신고번호</font> <?php echo $GLOBALS["cfg"]['orderSerial']?>

									<!--<font color="#e0e0e0"> | </font> -->
									</div>
									
									<div style="line-height:17px;"> 
									<font color='#33a5c0'>전화번호</font> <?php echo $GLOBALS["cfg"]['compPhone']?>

									<font color="#e0e0e0"> | </font> 
									<font color='#33a5c0'>팩스번호</font> <?php echo $GLOBALS["cfg"]['compFax']?>

									<!--<font color="#e0e0e0"> | </font> -->
									
									</div>
									<div style="line-height:17px;">
										<font color='#33a5c0'>개인정보관리자</font> <?php echo $GLOBALS["cfg"]['adminName']?>

										<font color='#33a5c0'>메일</font> <a href="javascript:popup('../proc/popup_email.php?to=<?php echo $GLOBALS["cfg"]['adminEmail']?>&hidden=1',650,600)"><?php echo $GLOBALS["cfg"]['adminEmail']?></a> 
									</div>
									<!--
									<div style="line-height:17px;"> 
									<font color='#33a5c0'>호스팅제공</font> (주)고도소프트 
									<img src="/shop/data/skin/campingyo/img/common/footer_godomall.png" style="vertical-align:middle;">
									</div>
									<div style="line-height:20px;"> Copyright ⓒ <b><?php echo $GLOBALS["cfg"]['shopUrl']?></b> All right reserved </div>
									-->
								</td>
								<td><!-- KB에스크로 이체 인증마크 적용 시작 -->
									<script>
									function onPopKBAuthMark()
									{
									window.open('','KB_AUTHMARK','height=604, width=648, status=yes, toolbar=no, menubar=no,location=no');
									document.KB_AUTHMARK_FORM.action='http://escrow1.kbstar.com/quics';
									document.KB_AUTHMARK_FORM.target='KB_AUTHMARK';
									document.KB_AUTHMARK_FORM.submit();
									}
									</script>
									<form name="KB_AUTHMARK_FORM" method="get">
									<input type="hidden" name="page" value="B009111"/>
									<input type="hidden" name="cc" value="b010807:b008491"/>
									<input type="hidden" name="mHValue" value='6dd1f98de45d450a592f0aa96b9c7784201403211310930'/>
									</form>
									<a href="#" onclick="javascript:onPopKBAuthMark();return false;">
									<img src="http://img1.kbstar.com/img/escrow/escrowcmark.gif" border="0"/>
									</a>
									<!-- KB에스크로이체 인증마크 적용 종료 -->
								</td>
								<!------------------ 상단로고 시작 ------------------->
								<td align="right"><!-- 배너관리에서 수정가능 --><?php if((is_array($TPL_R1=dataBanner( 91))&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?><?php echo $TPL_V1["tag"]?><?php }}?></td>
								<!------------------ 상단로고 끝 -------------------> 
							</tr>
						</table>
						<!------------ 하단 카피라이트  끝 ---------------> 
					</td>
					<!------------------ SSL seal 시작 -------------------> 
					<?php echo displaySSLSeal()?>

					<!------------------ SSL seal 끝 -------------------> 
					<!------------------ 구매안전서비스 표시 시작 -------------------> 
					<?php echo displayEggBanner()?>

					

					<!------------------ 구매안전서비스 표시 끝 -------------------> 
					<!------------------ 옥션 iPay Logo 표시 시작 -------------------> 
					<?php echo auctionIpayLogo()?>

					<!------------------ 옥션 iPay Logo 표시 끝 -------------------> 
				</tr>
			</table>
		</td>
	</tr>
</table>