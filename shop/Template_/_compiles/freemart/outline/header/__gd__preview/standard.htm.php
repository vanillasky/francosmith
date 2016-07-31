<?php /* Template_ 2.2.7 2015/11/15 16:52:58 /www/francotr3287_godo_co_kr/shop/data/skin/freemart/outline/header/standard.htm 000016089 */  $this->include_("dataBanner");?>
<!-- gdpart mode="open" fid="goods/goods_view.htm header_inc_3" --><!-- gdline 1"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" --><!-- gdline 2"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->
<!-- gdline 3"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" --><a name="top"></a>
<!-- gdline 4"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" --><div id="header_main">
<!-- gdline 5"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->	<div id="top_1" style="width:<?php echo $GLOBALS["cfg"]['shopSize']?>px; position:relative;">
<!-- gdline 6"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->		<div id="top_logo"><!-- 배너관리에서 수정가능 --><?php if((is_array($TPL_R1=dataBanner( 90))&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?><?php echo $TPL_V1["tag"]?><?php }}?></div>
<!-- gdline 7"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->		<div id="top_menu_layer">
<!-- gdline 8"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->			<ul id="top_menu">
<!-- gdline 9"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->				<?php if(!$GLOBALS["sess"]){?>
<!-- gdline 10"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->				<li><a href="<?php echo url("member/login.php")?>&" class="top_ul_font">로그인</a></li>
<!-- gdline 11"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->				<li class="top_ul_line">|</li>
<!-- gdline 12"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->				<li><a href="<?php echo url("member/join.php")?>&" class="top_ul_font">회원가입</a></li>
<!-- gdline 13"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->				<li class="top_ul_line">|</li>
<!-- gdline 14"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->				<?php }else{?>
<!-- gdline 15"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->				<li><a href="<?php echo url("member/logout.php")?>&" class="top_ul_font">로그아웃</a></li>
<!-- gdline 16"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->				<li class="top_ul_line">|</li>
<!-- gdline 17"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->				<?php }?>
<!-- gdline 18"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->				<li><a href="<?php echo url("member/myinfo.php?")?>&&" <?php if($TPL_VAR["useMypageLayerBox"]=='y'){?>onClick="return fnMypageLayerBox(<?php if($GLOBALS["sess"]){?>true<?php }?>);"<?php }?> class="top_ul_font">마이페이지</a></li>
<!-- gdline 19"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->				<li class="top_ul_line">|</li>
<!-- gdline 20"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->				<li><a href="<?php echo url("mypage/mypage_orderlist.php")?>&" class="top_ul_font">주문/배송</a></li>
<!-- gdline 21"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->				<li class="top_ul_line">|</li>
<!-- gdline 22"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->				<li><a href="<?php echo url("service/customer.php")?>&" class="top_ul_font">고객센터</a></li>
<!-- gdline 23"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->				<li class="top_ul_line">|</li>
<!-- gdline 24"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->				<li><img src="/shop/data/images/web/icon/moneybag.jpg" width="11" height="10" valign="absmiddle">
<!-- gdline 25"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->					<a href="/shop/goods/goods_list.php?&category=029002" class="top_ul_font">개인결제</a>
<!-- gdline 26"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->				</li>
<!-- gdline 27"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->				<li class="top_ul_line">|</li>
<!-- gdline 28"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->				<li><img src="/shop/data/images/web/icon/shopping-cart-24.png" width="11" height="10"><a href="<?php echo url("goods/goods_cart.php")?>&" class="top_ul_font">장바구니</a></li>
<!-- gdline 29"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->				<li class="top_ul_line"></li>
<!-- gdline 30"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->			</ul>
<!-- gdline 31"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->		</div>
<!-- gdline 32"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->		<div id="top_board_layer" style="width:<?php echo $GLOBALS["cfg"]['shopSize']?>px;">
<!-- gdline 33"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->			<div id="top_desc">
<!-- gdline 34"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->				<div id="top_call"><span class="footer_h4">031-819-2375&nbsp;&nbsp;&nbsp;</span><span class="footer_h5">MON - FRI 10:00 - 17:00</span></div>
<!-- gdline 35"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->				<!-- <div id="top_sale"><img src="/shop/data/images/web/icon/1412717979_label_sale.png"></div>
				<div id="top_sale_link"><a class="white" href="/shop/goods/goods_list.php?&category=029001">한정수랑/특가상품</a></div>	
				<div id="top_sale_time"><a class="white" href="/shop/goods/goods_list.php?&category=029003">반짝세일</a></div>	-->
<!-- gdline 38"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->			</div>
<!-- gdline 39"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->			<!--<div id="top_back"><img src="http://francosmith.godohosting.com/shop/top-back.jpg"/></div>-->
<!-- gdline 40"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->			<div id="top_search">
<!-- gdline 41"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->				<!-- 검색 시작----------------------------------->
<!-- gdline 42"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->				<form action="<?php echo url("goods/goods_search.php")?>&" onsubmit="return chkForm(this)">
<!-- gdline 43"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->				<input type=hidden name=searched value="Y">
<!-- gdline 44"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->				<input type=hidden name=log value="1">
<!-- gdline 45"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->				<input type=hidden name=skey value="all">
<!-- gdline 46"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->				<input type="hidden" name="hid_pr_text" value="<?php echo $GLOBALS["s_type"]['pr_text']?>" />
<!-- gdline 47"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->				<input type="hidden" name="hid_link_url" value="<?php echo $GLOBALS["s_type"]['link_url']?>" />
<!-- gdline 48"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->				<input type="hidden" id="edit" name="edit" value=""/>
<!-- gdline 49"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->				<?php if($GLOBALS["s_type"]['keyword_chk']=='on'&&$GLOBALS["s_type"]['pr_text']&&!$_GET['sword']){?>
<!-- gdline 50"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->				<?php
					 $TPL_VAR["id"] = "sword";
					 $TPL_VAR["onkeyup"] = "document.getElementById('edit').value='Y'";
					 $TPL_VAR["onclick"] = "document.getElementById('sword').value=''";
					 $TPL_VAR["value"] =  $GLOBALS["s_type"]['pr_text'];
				?>
<!-- gdline 56"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->				<?php }else{?>
<!-- gdline 57"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->				<?php
					 $TPL_VAR["value"] =  $_GET['sword'];
				?>
<!-- gdline 60"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->				<?php }?>
<!-- gdline 61"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->				<table cellpadding="0" cellspacing="0" border="0" class="search_table">
<!-- gdline 62"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->				<tr>
<!-- gdline 63"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->					<td class="search_td"><input name=sword type=text id="<?php echo $TPL_VAR["id"]?>" class="search_input" onkeyup="<?php echo $TPL_VAR["onkeyup"]?>" onclick="<?php echo $TPL_VAR["onclick"]?>" value="<?php echo $TPL_VAR["value"]?>" required label="검색어"></td>
<!-- gdline 64"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->					<td class="search_btn_top"><input type=image src="/shop/data/images/web/icon/serch_btn.jpg"></td>
<!-- gdline 65"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->				</tr>
<!-- gdline 66"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->				</table>
<!-- gdline 67"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->				</form>
<!-- gdline 68"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->				<!-- 검색 끝-------------------------------------->
<!-- gdline 69"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->			</div>
<!-- gdline 70"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->		</div>
<!-- gdline 71"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->		
<!-- gdline 72"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->	</div>
<!-- gdline 73"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->	
<!-- gdline 74"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->	<div id="top_cate" style="width:<?php echo $GLOBALS["cfg"]['shopSize']?>px;">
<!-- gdline 75"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->		<div id="top_navi">
<!-- gdline 76"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->			<div style="position:relative;width:100%;">
<!-- gdline 77"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->				<table>
<!-- gdline 78"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->					<tr style="height:36px;padding:0 0 0 0;">
<!-- gdline 79"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->						<td class="navi_menu">
<!-- gdline 80"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->							<a href="javascript:showonlyone('brands')" class="catenavi">BRAND</a>
<!-- gdline 81"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->					    </td>
<!-- gdline 82"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->						<td style="color:#ccc;">|</td>
<!-- gdline 83"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->						<td class="navi_menu"><a href="javascript:showonlyone('all_menu')" class="catenavi">ALL</a></td>
<!-- gdline 84"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->						
<!-- gdline 85"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->					</tr>
<!-- gdline 86"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->				</table>
<!-- gdline 87"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->			</div>
<!-- gdline 88"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->			
<!-- gdline 89"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->		</div>
<!-- gdline 90"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->		
<!-- gdline 91"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->		
<!-- gdline 92"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->		
<!-- gdline 93"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->		
<!-- gdline 94"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->		<!-- 카테고리 메뉴 시작 -->
<!-- gdline 95"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->		<!-- 관련 세부소스는 '기타/추가페이지(proc) > 카테고리메뉴- menuCategory.htm' 안에 있습니다 -->
<!-- gdline 96"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->		<?php $this->print_("menuCategory",$TPL_SCP,1);?>

<!-- gdline 97"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->		<!-- 카테고리 메뉴 끝 -->
<!-- gdline 98"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->		
<!-- gdline 99"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->		
<!-- gdline 100"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->		
<!-- gdline 101"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->		
<!-- gdline 102"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->	</div>	
<!-- gdline 103"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->		
<!-- gdline 104"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" -->	
<!-- gdline 105"/outline/header/standard.htm|/outline/header/standard.htm|goods/goods_view.htm header_inc_3" --></div><!-- gdpart mode="close" fid="goods/goods_view.htm header_inc_3" -->