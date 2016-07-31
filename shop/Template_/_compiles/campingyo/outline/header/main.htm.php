<?php /* Template_ 2.2.7 2014/04/09 11:27:14 /www/francotr3287_godo_co_kr/shop/data/skin/campingyo/outline/header/main.htm 000004542 */  $this->include_("dataBanner");?>
<a name="top"></a>
<div >
	<table width=100% cellpadding=0 cellspacing=0 border=0>
		<tr>
			<td>
				<table width="<?php echo $GLOBALS["cfg"]['shopSize']?>" height=129 align="<?php echo $GLOBALS["cfg"]['shopAlign']?>" cellpadding=0 cellspacing=0 border=0 >
					<tr style="background:url(/shop/data/skin/campingyo/img/main/top_bg.jpg) repeat-x;">
						<td valign="top" height="33"><a href="javascript:window.external.AddFavorite('http://<?php echo $GLOBALS["cfg"]['shopUrl']?>','<?php echo $GLOBALS["cfg"]['shopName']?>');"><img src="/shop/data/skin/campingyo/img/main/bookmark.jpg"></a><a href="/shop/mypage/mypage_orderlist.php"><img src="/shop/data/skin/campingyo/img/main/delivery.jpg"></a></td>
						<td align="right" valign="top" style="vertical-align:middle; font-size:11px; color:#999ca3; text-align:right;"> 
							<!-- 로그인/회원정보/위시리스/아이디찾기 시작----> 
<?php if(!$GLOBALS["sess"]){?> 
							<a href="<?php echo url("member/login.php")?>&" style="color:#999ca3;">로그인</a> <span style="color:#5d626b3;">&nbsp;|&nbsp;</span> <a href="<?php echo url("member/join.php")?>&" style="color:#999ca3;">회원가입</a> 
<?php }else{?> 
							<a href="<?php echo url("member/logout.php")?>&" style="color:#999ca3;">로그아웃</a> 
<?php }?> 
							<span style="color:#5d626b3;">&nbsp;|&nbsp;</span> <a href="<?php echo url("member/myinfo.php?")?>&&" <?php if($TPL_VAR["useMypageLayerBox"]=='y'){?>onClick="return fnMypageLayerBox(<?php if($GLOBALS["sess"]){?>true<?php }?>);"<?php }?> style="color:#999ca3;">마이페이지</a> <span style="color:#5d626b3;">&nbsp;|&nbsp;</span> <a href="<?php echo url("service/customer.php")?>&" style="color:#999ca3;">고객센터</a> <span style="color:#5d626b3;">&nbsp;|&nbsp;</span> <a href="<?php echo url("goods/goods_cart.php")?>&" style="color:#999ca3;">장바구니</a> 
							<!-- 로그인/회원정보/위시리스/아이디찾기 끝------> 
						</td>
						<td width="154" align="right"> 
							<!-- 검색 시작----------------------------------->
							<form action="<?php echo url("goods/goods_search.php")?>&" onsubmit="return chkForm(this)" style="height:33px; padding:0px; margin:0px; border:none;">
								<input type=hidden name=searched value="Y">
								<input type=hidden name=log value="1">
								<input type=hidden name=skey value="all">
								<input type="hidden" name="hid_pr_text" value="<?php echo $GLOBALS["s_type"]['pr_text']?>" />
								<input type="hidden" name="hid_link_url" value="<?php echo $GLOBALS["s_type"]['link_url']?>" />
								<input type="hidden" id="edit" name="edit" value=""/>
<?php if($GLOBALS["s_type"]['keyword_chk']=='on'&&$GLOBALS["s_type"]['pr_text']&&!$_GET['sword']){?>
								<?php
									 $TPL_VAR["id"] = "sword";
									 $TPL_VAR["onkeyup"] = "document.getElementById('edit').value='Y'";
									 $TPL_VAR["onclick"] = "document.getElementById('sword').value=''";
									 $TPL_VAR["value"] =  $GLOBALS["s_type"]['pr_text'];
								?>
<?php }else{?>
								<?php
									 $TPL_VAR["value"] =  $_GET['sword'];
								?>
<?php }?>
								<table width="146" cellpadding="0" cellspacing="0" border="0" style="background:url(/shop/data/skin/campingyo/img/main/search_icon.jpg) no-repeat;">
									<tr>
										<td width="123" height="33" align="right" style="padding-top:4px;"><input name=sword type=text id="<?php echo $TPL_VAR["id"]?>" style="border:0px;background-color:transparent; width:123px; height:20px; color:#71737a;" onkeyup="<?php echo $TPL_VAR["onkeyup"]?>" onclick="<?php echo $TPL_VAR["onclick"]?>" value="<?php echo $TPL_VAR["value"]?>" required label="검색어"></td>
										<td width="23" align="right"><input type=image src="/shop/data/skin/campingyo/img/main/search.jpg"></td>
									</tr>
								</table>
							</form>
							<!-- 검색 끝--------------------------------------> 
						</td>
					</tr>
					<tr> 
						<!------------------ 상단로고 시작 ------------------->
						<td colspan="3"><!-- 배너관리에서 수정가능 --><?php if((is_array($TPL_R1=dataBanner( 90))&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?><?php echo $TPL_V1["tag"]?><?php }}?></td>
						<!------------------ 상단로고 끝 -------------------> 
					</tr>
				</table>
			</td>
		</tr>
	</table>
</div>