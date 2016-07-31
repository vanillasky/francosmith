<?php /* Template_ 2.2.7 2016/07/27 12:12:43 /www/francotr3287_godo_co_kr/shop/data/skin/freemart/outline/header/main.htm 000005240 */  $this->include_("dataBanner");?>
<a name="top"></a>
<div id="shop_top_area">
	<div id="top_area_menu">
		<ul id="top_links" class="ul_right">
			
<?php if(!$GLOBALS["sess"]){?>
			<li><a href="<?php echo url("member/login.php")?>&">로그인</a></li>
			<li class="top_ul_line">|</li>
			<li><a href="<?php echo url("member/join.php")?>&">회원가입</a></li>
			<li class="top_ul_line">|</li>
<?php }else{?>
			<li><a href="<?php echo url("member/logout.php")?>&">로그아웃</a></li>
			<li class="top_ul_line">|</li>
<?php }?>
			<li><a href="<?php echo url("mypage/mypage.php?")?>&&" <?php if($TPL_VAR["useMypageLayerBox"]=='y'){?>onClick="return fnMypageLayerBox(<?php if($GLOBALS["sess"]){?>true<?php }?>);"<?php }?>>마이페이지</a></li>
			<li class="top_ul_line">|</li>
			<li><a href="<?php echo url("mypage/mypage_orderlist.php")?>&">주문/배송</a></li>
			<li class="top_ul_line">|</li>
			<li><a href="<?php echo url("service/customer.php")?>&">고객센터</a></li>
			<li class="top_ul_line">|</li>
			<li><a href="/shop/goods/goods_list.php?&category=031002001" class="top_ul_font">개인결제</a>
			</li>
			<li class="top_ul_line">|</li>
			<li><a href="<?php echo url("goods/goods_cart.php")?>&">장바구니(<?php echo $GLOBALS["sess"]["cart_count"]?>)</a></li>
			<li class="top_ul_line">
				<a href="#" id="contact"><span>1599-7835<span></a>	
			</li>
		</ul>
	</div>
</div>

<div id="header_main">
	<div id="top_1">
		<div id="top_board_layer" style="width:<?php echo $GLOBALS["cfg"]['shopSize']?>px;">
			<div id="top_logo"><!-- 배너관리에서 수정가능 --><?php if((is_array($TPL_R1=dataBanner( 90))&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?><?php echo $TPL_V1["tag"]?><?php }}?></div>	
			<div id="top_search">
				<script>
				function add_param_submit (param_nm, param_val){
					var frm = document.searchForm;
					if( param_nm == 'sword') frm.sword.value = param_val;
					else frm.disp_type.value = param_val;
					frm.submit();
				}
				</script>
				<!-- 검색 시작----------------------------------->
				<form name="searchForm" action="<?php echo url("goods/goods_search.php")?>&" onsubmit="return chkForm(this)">
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
				<div id="top_keywords">
					<span><?php if($GLOBALS["s_type"]['keyword']){?>인기검색어: <?php echo $GLOBALS["s_type"]['keyword']?><?php }?></span>	
				</div>
				<table cellpadding="0" cellspacing="0" border="0" class="search_table">
				<tr>
					<td class="search_td"><input name="sword" type=text id="<?php echo $TPL_VAR["id"]?>" class="search_input" onkeyup="<?php echo $TPL_VAR["onkeyup"]?>" onclick="<?php echo $TPL_VAR["onclick"]?>" value="<?php echo $TPL_VAR["value"]?>" required label="검색어"></td>
					<td class="search_btn_top top_red"><button type="submit" title="Search" class="button search-button"></td>
				</tr>
				</table>
				</form>
				<!-- 검색 끝-------------------------------------->
			</div>
			<div id="top_right">
			</div>
		</div>
	</div>
	
	<!--<div id="top_cate" style="width:<?php echo $GLOBALS["cfg"]['shopSize']?>px;">
		<ul>
			<li class="boldl"><a href="javascript:showonlyone('brands')"><span>Brands</span></a></li>	-->
			<!--<li class="boldl"><a href="javascript:showonlyone('all_menu')">Categories</a></li>-->
					
		<!--
		<div id="top_navi">
			<div style="position:relative;width:100%;">
				<table>
					<tr style="height:36px;padding:0 0 0 0;">
						<td class="navi_menu">
							<a href="javascript:showonlyone('brands')" class="catenavi"><span style="font-size:11px;color:#fff;" >Shop By Brand </span></a>
					    </td>
						<td style="color:#fff;">|</td>
						<td class="navi_menu"><a href="javascript:showonlyone('all_menu')" class="catenavi"><span style="font-size:11px;color:#fff;">All<br>Categories</span></a><td>
						
					</tr>
				</table>
			</div>
			
		</div>
		-->
		
		
		
		<!--</ul>
	</div>-->	
	
	<!-- 카테고리 메뉴 시작 -->
	<!-- 관련 세부소스는 '기타/추가페이지(proc) > 카테고리메뉴- menuCategory.htm' 안에 있습니다 -->
<?php $this->print_("menuCategory",$TPL_SCP,1);?>

	<!-- 카테고리 메뉴 끝 -->
			
</div>


		
<!-- 상단파일 종료 -->