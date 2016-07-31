<?php /* Template_ 2.2.7 2014/11/03 14:24:29 /www/francotr3287_godo_co_kr/shop/data/skin/standard/outline/header/main.htm 000006237 */  $this->include_("dataBanner");?>
<a name="top"></a>
<div id="header_main">
	<div id="top_1" style="width:<?php echo $GLOBALS["cfg"]['shopSize']?>px;">
		<div id="top_logo"><!-- 배너관리에서 수정가능 --><?php if((is_array($TPL_R1=dataBanner( 90))&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?><?php echo $TPL_V1["tag"]?><?php }}?></div>
		<div class="div_line"><!-- --></div>
		<div>
			<ul id="top_menu">
<?php if($TPL_VAR["page_cache_enabled"]){?>
				<li class="user-status-logout" style="display: none;"><a href="<?php echo url("member/login.php")?>&"><img src="/shop/data/skin/standard/img/main/topmenu_login.jpg"></a></li>
				<li class="user-status-logout" style="display: none;"><img src="/shop/data/skin/standard/img/main/topmenu_bar.jpg"></li>
				<li class="user-status-logout" style="display: none;"><a href="<?php echo url("member/join.php")?>&"><img src="/shop/data/skin/standard/img/main/topmenu_join.jpg"></a></li>
				<li class="user-status-logout" style="display: none;"><img src="/shop/data/skin/standard/img/main/topmenu_bar.jpg"></li>
				<li class="user-status-login" style="display: none;"><a href="<?php echo url("member/logout.php")?>&"><img src="/shop/data/skin/standard/img/main/topmenu_logout.jpg"></a></li>
				<li class="user-status-login" style="display: none;"><img src="/shop/data/skin/standard/img/main/topmenu_bar.jpg"></li>
				<li class="user-status-login" style="display: none;"><a href="<?php echo url("member/myinfo.php?")?>&&" <?php if($TPL_VAR["useMypageLayerBox"]=='y'){?>onClick="return fnMypageLayerBox(<?php if($GLOBALS["sess"]){?>true<?php }?>);"<?php }?>><img src="/shop/data/skin/standard/img/main/topmenu_mypage.jpg"></a></li>
				<li class="user-status-login" style="display: none;"><img src="/shop/data/skin/standard/img/main/topmenu_bar.jpg"></li>
<?php }else{?>
<?php if(!$GLOBALS["sess"]){?>
				<li><a href="<?php echo url("member/login.php")?>&"><img src="/shop/data/skin/standard/img/main/topmenu_login.jpg"></a></li>
				<li><img src="/shop/data/skin/standard/img/main/topmenu_bar.jpg"></li>
				<li><a href="<?php echo url("member/join.php")?>&"><img src="/shop/data/skin/standard/img/main/topmenu_join.jpg"></a></li>
				<li><img src="/shop/data/skin/standard/img/main/topmenu_bar.jpg"></li>
<?php }else{?>
				<li><a href="<?php echo url("member/logout.php")?>&"><img src="/shop/data/skin/standard/img/main/topmenu_logout.jpg"></a></li>
				<li><img src="/shop/data/skin/standard/img/main/topmenu_bar.jpg"></li>
				<li><a href="<?php echo url("member/myinfo.php?")?>&&" <?php if($TPL_VAR["useMypageLayerBox"]=='y'){?>onClick="return fnMypageLayerBox(<?php if($GLOBALS["sess"]){?>true<?php }?>);"<?php }?>><img src="/shop/data/skin/standard/img/main/topmenu_mypage.jpg"></a></li>
				<li><img src="/shop/data/skin/standard/img/main/topmenu_bar.jpg"></li>
<?php }?>
<?php }?>
				<li><a href="<?php echo url("mypage/mypage_orderlist.php")?>&"><img src="/shop/data/skin/standard/img/main/topmenu_order.jpg"></a></li>
				<li><img src="/shop/data/skin/standard/img/main/topmenu_bar.jpg"></li>
				<li><a href="<?php echo url("service/customer.php")?>&"><img src="/shop/data/skin/standard/img/main/topmenu_cs.jpg"></a></li>
				<li><img src="/shop/data/skin/standard/img/main/topmenu_bar.jpg"></li>
				<li><a href="<?php echo url("goods/goods_cart.php")?>&"><img src="/shop/data/skin/standard/img/main/topmenu_cart.jpg"></a></li>
                 <li><img src="/shop/data/skin/standard/img/main/topmenu_bar.jpg"></li>
				<li><a href="<?php echo url("goods/goods_qna.php")?>&"><img src="/shop/data/skin/standard/img/main/topmenu_qna.gif"></a></li>
			</ul>
		</div>
		
			<div id="top_search" class="search_pd">
                <!-- 검색 시작----------------------------------->
                <form action="<?php echo url("goods/goods_search.php")?>&" onsubmit="return chkForm(this)" style="margin:0; padding:0; border:none;">
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
                <table cellpadding="0" cellspacing="0" border="0" class="search_table">
                <tr>
                    <td class="search_td"><input name=sword type=text id="<?php echo $TPL_VAR["id"]?>" class="search_input" onkeyup="<?php echo $TPL_VAR["onkeyup"]?>" onclick="<?php echo $TPL_VAR["onclick"]?>" value="<?php echo $TPL_VAR["value"]?>" required label="검색어"></td>
                    <td class="search_btn"><input type=image src="/shop/data/skin/standard/img/main/btn_search.gif"></td>
                </tr>
                </table>
                </form>
                <!-- 검색 끝-------------------------------------->
            </div>

		<div class="div_line"><!-- --></div>
		<div id="top_cate">
			<!-- 카테고리 메뉴 시작 -->
			<!-- 관련 세부소스는 '기타/추가페이지(proc) > 카테고리메뉴- menuCategory.htm' 안에 있습니다 -->
<?php $this->print_("menuCategory",$TPL_SCP,1);?>

			<!-- 카테고리 메뉴 끝 -->
		</div>
		<div class="div_line"><!-- --></div>
	</div>
</div>