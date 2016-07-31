<?php /* Template_ 2.2.7 2014/10/13 00:33:16 /www/francotr3287_godo_co_kr/shop/data/skin/freemart/outline/footer/standard.htm 000017050 */  $this->include_("dataBank");?>
<!-- gdpart mode="open" fid="goods/goods_view.htm footer_inc_6" --><!-- gdline 1"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" --><!-- gdline 2"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" --><div id="footer_top" align="<?php echo $GLOBALS["cfg"]['shopAlign']?>">
<!-- gdline 3"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" --><table width="<?php echo $GLOBALS["cfg"]['shopSize']?>px" id="cs_contents" border="0" cellpadding="0" cellspacing="0">
<!-- gdline 4"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->	<tr>
<!-- gdline 5"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->		<td valign="top" width="210" style="padding:10px 0 0 50px;"> 
<!-- gdline 6"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->		<!-- 고객센터 01 : Start -->
<!-- gdline 7"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->		<div id="cscenter">
<!-- gdline 8"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->			
<!-- gdline 9"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->			<div class="cstel"><span class="fotter_info_tel">Tel:</span> <?php echo $GLOBALS["cfg"]['compPhone']?></div>
<!-- gdline 10"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->			<div class="cstel"><span class="fotter_info">Fax:</span> <?php echo $GLOBALS["cfg"]['compFax']?></div>
<!-- gdline 11"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->			<dl>
<!-- gdline 12"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->				<dd>MON - FRI <span class="txt">am 10:00 - pm 17:00</span> </dd>
<!-- gdline 13"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->			</dl>
<!-- gdline 14"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->		</div>
<!-- gdline 15"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->		<!-- 고객센터 01 : End --> 
<!-- gdline 16"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->		</td>
<!-- gdline 17"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->		<td valign="top" width="160" style="padding:10px 0 0 0;" >
<!-- gdline 18"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->			<!-- 메인 무통장입금 : Start -->
<!-- gdline 19"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->			<table id="bankinfo" border="0" cellpadding="0" cellspacing="0">
<!-- gdline 20"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->				<tr>
<!-- gdline 21"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->					<td><span class="footer_h2">Bank Info</span></td>
<!-- gdline 22"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->				</tr>
<!-- gdline 23"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->				<?php if((is_array($TPL_R1=dataBank())&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?>
<!-- gdline 24"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->				<tr>
<!-- gdline 25"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->					<td style="padding-top:5px;"><span class="footer_h3"><?php echo $TPL_V1["bank"]?></span></td>
<!-- gdline 26"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->				</tr>
<!-- gdline 27"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->				<tr>
<!-- gdline 28"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->					<td style="padding-top:5px;"><span class="footer_h4"><?php echo $TPL_V1["account"]?></span><span class="footer_h3"> <?php echo $TPL_V1["name"]?></span></td>
<!-- gdline 29"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->				</tr>
<!-- gdline 30"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->				
<!-- gdline 31"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->				<?php }}?> 
<!-- gdline 32"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->			</table>
<!-- gdline 33"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->			<!-- 메인 무통장입금 : End --> 
<!-- gdline 34"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->		</td>
<!-- gdline 35"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->		<td><img src="/shop/data/images/web/logo-bottom_01.jpg"></td>
<!-- gdline 36"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->		<td valign="top" style="padding:5px 0 0 0;">
<!-- gdline 37"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->			<table id="about" width="100%" border="0" cellpadding="0" cellspacing="0">
<!-- gdline 38"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->				<tr>
<!-- gdline 39"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->					<td class="bold" align="right">
<!-- gdline 40"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->						<a href="<?php echo url("service/guide.php")?>&">이용안내</a> &nbsp;<span style="color:#e0e0e0;">|</span>&nbsp; 
<!-- gdline 41"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->						<a href="<?php echo url("service/cooperation.php")?>&">제휴안내</a>&nbsp;<span style="color:#e0e0e0;">|</span>&nbsp; 
<!-- gdline 42"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->						<a href="<?php echo url("service/private.php")?>&">개인정보처리방침</a>
<!-- gdline 43"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->					</td>
<!-- gdline 44"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->					<td width="10px;"></td>
<!-- gdline 45"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->				</tr>
<!-- gdline 46"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->				<tr>
<!-- gdline 47"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->					<td class="bold" align="right">
<!-- gdline 48"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->					<a href="javascript:popup('../proc/popup_email.php?to=<?php echo $GLOBALS["cfg"]['adminEmail']?>&hidden=1',650,600)"><?php echo $GLOBALS["cfg"]['adminEmail']?></a>
<!-- gdline 49"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->					</td>
<!-- gdline 50"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->					<td width="10px;"></td>
<!-- gdline 51"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->				</tr>
<!-- gdline 52"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->				<tr>
<!-- gdline 53"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->					<td align="right" class="footer_h4">
<!-- gdline 54"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->						<small>Copyright &copy; <script type="text/javascript">
var d = new Date();
document.write(d.getFullYear())
							</script>&nbsp;Francosmith</small>
<!-- gdline 58"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->
<!-- gdline 59"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->					</td>
<!-- gdline 60"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->					<td width="10px;"></td>
<!-- gdline 61"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->					
<!-- gdline 62"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->				</tr>
<!-- gdline 63"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->			</table>
<!-- gdline 64"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->		</td>
<!-- gdline 65"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->	</tr>
<!-- gdline 66"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->	
<!-- gdline 67"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->		
<!-- gdline 68"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" --></table>
<!-- gdline 69"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" --><table>
<!-- gdline 70"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" --><tr>
<!-- gdline 71"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->	<td>
<!-- gdline 72"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->		<div id="footer_bottom">
<!-- gdline 73"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->			<div id="footer_marks">
<!-- gdline 74"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->				<div class="logos" >
<!-- gdline 75"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->				<!-- KB에스크로 이체 인증마크 적용 시작 -->
<!-- gdline 76"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->				<script>
					function onPopKBAuthMark()
					{
						window.open('','KB_AUTHMARK','height=604, width=648, status=yes, toolbar=no, menubar=no,location=no');
						document.KB_AUTHMARK_FORM.action='http://escrow1.kbstar.com/quics';
						document.KB_AUTHMARK_FORM.target='KB_AUTHMARK';
						document.KB_AUTHMARK_FORM.submit();
					}
				</script>
<!-- gdline 85"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->				<form name="KB_AUTHMARK_FORM" method="get">
<!-- gdline 86"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->					<input type="hidden" name="page" value="B009111"/>
<!-- gdline 87"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->					<input type="hidden" name="cc" value="b010807:b008491"/>
<!-- gdline 88"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->					<input type="hidden" name="mHValue" value='6dd1f98de45d450a592f0aa96b9c7784201403211310930'/>
<!-- gdline 89"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->				</form>
<!-- gdline 90"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->				<a href="#" onclick="javascript:onPopKBAuthMark();return false;">
<!-- gdline 91"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->					<img src="http://img1.kbstar.com/img/escrow/escrowcmark.gif" border="0"/>
<!-- gdline 92"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->				</a>
<!-- gdline 93"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->				<!-- KB에스크로이체 인증마크 적용 종료 -->
<!-- gdline 94"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->				</div>
<!-- gdline 95"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->				<div id="logo_cj"><img src="/shop/data/images/web/icon/icon_cj.png"></div>
<!-- gdline 96"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->				<div id="logo_lock"><img src="/shop/data/images/web/icon/icon_lock.png"></div>
<!-- gdline 97"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->				<div id="logo_insurance"><img src="/shop/data/images/web/icon/icon_insuranace.jpg"></div>
<!-- gdline 98"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->			</div>
<!-- gdline 99"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->			
<!-- gdline 100"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->		</div>
<!-- gdline 101"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->		<div id="footer_company">
<!-- gdline 102"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->			<p class="line_h20">
<!-- gdline 103"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->				<span class="txt">회사:</span><?php echo $GLOBALS["cfg"]['compName']?><span class="bar"></span>
<!-- gdline 104"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->				<span class="txt">대표:</span><?php echo $GLOBALS["cfg"]['ceoName']?> <span class="bar"></span> 
<!-- gdline 105"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->				<span class="txt">사업자등록번호:</span><?php echo $GLOBALS["cfg"]['compSerial']?> <a href="http://www.ftc.go.kr/info/bizinfo/communicationList.jsp" target="_blank">
<!-- gdline 106"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->				[조회]</a><span class="bar"></span>
<!-- gdline 107"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->				<span class="txt">통신판매업신고:</span><?php echo $GLOBALS["cfg"]['orderSerial']?><span class="bar"></span> 
<!-- gdline 108"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->				<span class="txt">개인정보관리자:</span><?php echo $GLOBALS["cfg"]['adminName']?>

<!-- gdline 109"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->				<span class="txt">주소:</span><?php echo $GLOBALS["cfg"]['address']?> <span class="bar"></span>
<!-- gdline 110"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->			</p>
<!-- gdline 111"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->			
<!-- gdline 112"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->		</div>
<!-- gdline 113"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->	</td>
<!-- gdline 114"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" --></tr>	
<!-- gdline 115"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" --></table>
<!-- gdline 116"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" --></div>
<!-- gdline 117"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" -->
<!-- gdline 118"/outline/footer/standard.htm|/outline/footer/standard.htm|goods/goods_view.htm footer_inc_6" --><!-- gdpart mode="close" fid="goods/goods_view.htm footer_inc_6" -->