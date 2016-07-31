<?php /* Template_ 2.2.7 2015/10/19 19:12:58 /www/francotr3287_godo_co_kr/shop/data/skin/standard/outline/footer/main_footer.htm 000004765 */  $this->include_("dataBank");?>
<div id="footer_top" align="<?php echo $GLOBALS["cfg"]['shopAlign']?>">
<table width="<?php echo $GLOBALS["cfg"]['shopSize']?>px" id="cs_contents" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td valign="top" width="210" style="padding:38px 0 0 50px;"> 
		<!-- 고객센터 01 : Start -->
		<div id="cscenter">
			<div style=""><a href="<?php echo url("service/customer.php")?>&"><img src="/shop/data/skin/standard/img/main/callcenter.gif"></a></div>
			<div class="cstel"><?php echo $GLOBALS["cfg"]["customerPhone"]?></div>
			<dl>
				<dd><span class="txt"><?php echo $GLOBALS["cfg"]["customerHour"]?></span></dd>
			</dl>
		</div>
		<!-- 고객센터 01 : End --> 
		</td>
		<td valign="top" width="189" style="padding:38px 0;">
			<!-- 메인 무통장입금 : Start -->
			<table id="bankinfo" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td style="padding-bottom:24px;"><img src="/shop/data/skin/standard/img/main/banking.gif"></td>
				</tr>
<?php if((is_array($TPL_R1=dataBank())&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?>
				<tr>
					<td style="padding-top:5px;"><?php echo $TPL_V1["bank"]?></td>
				</tr>
				<tr>
					<td><span class="bold"><?php echo $TPL_V1["account"]?></span> <?php echo $TPL_V1["name"]?></td>
				</tr>
				<tr>
					<td></td>
				</tr>
<?php }}?> 
			</table>
			<!-- 메인 무통장입금 : End --> 
		</td>
		<td valign="top" width="173" style="padding-top:38px">  
			<table id="about" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td align="left" style="padding-bottom:28px;"><img src="/shop/data/skin/standard/img/main/about.gif"></td>
				</tr>
				<tr>
					<td align="left" class="bold"><a href="<?php echo url("service/private.php")?>&" style="color:#fb4b12;"><strong>개인정보취급방침</strong></a></td>
				</tr>
				<tr>
					<td align="left"><a href="<?php echo url("service/company.php")?>&">회사소개</a> &nbsp;<span style="color:#e0e0e0;">|</span>&nbsp; <a href="<?php echo url("service/agreement.php")?>&">이용약관</a></td>
				</tr>
				<tr>
					<td align="left"><a href="<?php echo url("service/guide.php")?>&">이용안내</a> &nbsp;<span style="color:#e0e0e0;">|</span>&nbsp; <a href="<?php echo url("service/cooperation.php")?>&">제휴안내</a></td>
				</tr>
				<tr>
					<td align="left"><a href="<?php echo url("service/sitemap.php")?>&">SITE MAP</a></td>
				</tr>
			</table>
		</td>
		<td valign="top" width="330" style="padding:38px 0">
			<table id="info" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td width="20" rowspan="2" style="border-left:solid 1px #e5e5e5;"></td>
					<td align="left" style="padding-bottom:28px;"><img src="/shop/data/skin/standard/img/main/info.gif"></td>
				</tr>
				<tr>
					<td>
						<div id="footer_company">
							<p class="line_h20"><span class="txt">회사</span> <?php echo $GLOBALS["cfg"]['compName']?> </p>
							<p><span class="txt">주소</span> <?php echo $GLOBALS["cfg"]['address']?> </p>
							<p class="line_h20">
								<span class="txt">대표</span> <?php echo $GLOBALS["cfg"]['ceoName']?>

								<span class="bar"> <span style="color:#e0e0e0;">|</span>  </span>
								<span class="txt">사업자등록번호</span> <?php echo $GLOBALS["cfg"]['compSerial']?> <a href="http://www.ftc.go.kr/info/bizinfo/communicationList.jsp" target="_blank"><img src="/shop/data/skin/standard/img/common/btn_business.gif" style="vertical-align:text-bottom;"></a>
							</p>
							<p>
								<span class="txt">통신판매업신고번호</span> <?php echo $GLOBALS["cfg"]['orderSerial']?>

								<span class="bar"> <span style="color:#e0e0e0;">|</span>  </span>
								<span class="txt">개인정보관리자</span> <?php echo $GLOBALS["cfg"]['adminName']?>

							</p>
							<p class="line_h20">
								<span class="txt">팩스번호</span> <?php echo $GLOBALS["cfg"]['compFax']?>

								<span class="bar"> <span style="color:#e0e0e0;">|</span>  </span>
								<span class="txt">메일</span> <a href="javascript:popup('../proc/popup_email.php?to=<?php echo $GLOBALS["cfg"]['adminEmail']?>&hidden=1',650,600)"><?php echo $GLOBALS["cfg"]['adminEmail']?></a>
							</p>
							<p class="line_h20" style="padding-top:10px;">
								Copyright ⓒ <strong><?php echo $GLOBALS["cfg"]['shopUrl']?></strong> All right reserved
							</p>
							<img src="/shop/data/skin/standard/img/common/footer_godomall.png">
						</div>
					</td>
				</tr>
			</table>
		</td>
	</tr>	
</table>
</div>