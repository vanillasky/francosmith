<style>
/*
	gnb
*/

div.main-gnb {
	width:100%;
	height:99px;
	background:url('../img/header/gnb_menu_bg.gif') repeat-x left bottom;	
}
div.main-gnb * {
	margin:0;
	padding:0;
	font-size: 11px;
	font-family:dotum,'돋움',sans-serif;
	line-height: 1.25;	
}
div.main-gnb img {
	border:none;
	vertical-align:top;
}
div.main-gnb ul, ol , li{
	 list-style:none;
}
div.main-gnb input.radio {
	width:13px;
	height:13px;
	vertical-align:top;
}
div.main-gnb input.checkbox {
	width:13px;
	height:13px;
	vertical-align:top;
}
div.main-gnb input.image {
	vertical-align:top;
	border:none;
}
div.main-gnb input.button {
	border:none;
	background:none;
	cursor:pointer;
}
div.main-gnb div.top-menu {
	width:1002px;
	height:31px;
}
div.main-gnb div.top-menu h1 {
	float:left;
	padding-left:14px;
}
div.main-gnb div.top-menu div.search {
	float:right;
}
div.main-gnb div.top-menu div.search div.choice {
	float:left;
	padding:10px 0 0;
}
div.main-gnb div.top-menu div.search input.radio {
	float:left;
	margin-left:10px;
}
div.main-gnb div.top-menu div.search label {
	position:relative;
	top:2px;
	float:left;
	margin-left:3px;
	color:#555;
	letter-spacing:-1px;
}
div.main-gnb div.top-menu div.search div.input-text {
	position:relative;
	float:left;
	padding:5px 0 0 5px;
}
div.main-gnb div.top-menu div.search div.input-text input.text {
	float:left;
	width:140px;
	height:20px;
	padding:3px 21px 0 6px;	
	background:url('../img/header/topmenu_bg_arrow.gif') no-repeat right 50%;
	border:1px solid #c5c5c5;
	-moz-box-sizing: border-box;
    -webkit-box-sizing: border-box;
    box-sizing: border-box;
}
div.main-gnb div.top-menu div.search div.input-text .image {
	float:left;
	margin-left:2px;
	cursor:pointer;
}
div.main-gnb div.top-menu div.search div.input-text ul.autocomplete {
	overflow-y:auto;
	overflow-x:hidden;
	position:absolute;
	top:27px;
	left:5px;
	width:165px;
	height:115px;
	background:#fff;
	border:1px solid #c5c5c5;
}
div.main-gnb div.top-menu div.search div.input-text ul.autocomplete li {
	padding:5px 5px 0;
	cursor:pointer;
}
div.main-gnb div.top-menu div.search div.input-text ul.autocomplete li.on {
	color:#1C9BF1;
}
div.main-gnb div.top-menu div.quick {
	float:right;
	padding:9px 40px 0 45px;
}
div.main-gnb div.top-menu div.quick input.checkbox {
	vertical-align:middle;
}
div.main-gnb div.top-menu div.quick label {
	position:relative;
	top:2px;
	letter-spacing:-1px;
}
div.main-gnb div.top-menu div.btn-right {
	float:right;
	padding:5px 0 0;
}
div.main-gnb div.top-menu div.btn-right a.gnb-button-white {
	display:inline;
	float:left;
	width:56px;
	height:14px;
	margin-left:3px;
	padding:6px 0 0;
	background:url('../img/header/topmenu_btn_square.gif') no-repeat left top;
	color:#555;
	letter-spacing:-1px;
	text-align:center;
}
div.main-gnb div.top-menu div.btn-right a.gnb-button-black {
	display:inline;
	float:left;
	width:56px;
	height:14px;
	margin-left:3px;
	padding:6px 0 0;
	background:url('../img/header/topmenu_btn_square_black.gif') no-repeat left top;
	color:#fff;
	letter-spacing:-1px;
	text-align:center;
}
div.main-gnb ul.bottom-menu {
	overflow:hidden;
	width:1002px;
}
div.main-gnb ul.bottom-menu li {
	float:left;
}
</style>
<div class="main-gnb">
	<div class="top-menu">
		<h1><a href="../index.php"><img src="../img/godo_logo.gif" alt="고도소프트" /></a></h1>

		<!--------------- 관리버튼 --------------------->
		<div class="btn-right">
			<? if($godo['shople'] == 'y'): ?>
			<a href="../shople/index.php" class="gnb-button-white">쇼플</a>
			<? endif; ?>
			<a href="<?=$sitelink->link('admin/todayshop/index.php', 'regular')?>" class="gnb-button-black">투데이샵</a>
			<a href="http://www.godo.co.kr" target="_blank" class="gnb-button-white">마이고도</a>
			<a href="../../../index.php" target="_blank" class="gnb-button-white">내쇼핑몰</a>
			<? if($blogshop->linked): ?>
			<a href="<?=$blogshop->config['iframe_url']?>/../" target="_blank" class="gnb-button-white">내블로그</a>
			<a href="../blog/index.php" class="gnb-button-white">블로그관리</a>
			<? endif; ?>
			<a href="../../member/logout.php?referer=../admin/login/login.php" class="gnb-button-white">로그아웃</a>
		</div>
		<!--------------- 관리버튼 --------------------->

		<!--------------- 퀵메뉴 --------------------->
		<div class="quick">
			<input type="checkbox" class="checkbox" id="el-use-context-menu" onClick="nsGodoContextMenu.toggle();" title="퀵메뉴" />
			<label for="quickMenu">퀵메뉴</label>
			<a href="javascript:popup('http://www.godo.co.kr/main/better_godomall.php?code=newservice&postNo=25',1200,850)"><img src="../img/header/topmenu_btn_quick.gif" alt="퀵메뉴란" /></a>
		</div>
		<!--------------- 퀵메뉴 --------------------->
		
		<!--------------- 메뉴검색, 회원CRM 시작 --------------------->
		<div class="search">
			<script language="JavaScript" type="text/JavaScript">
				/*** 관리자 CRM or 메뉴 검색 ***/
				var nsQuickSearch = function() {
					return {
						currentMenu : null,
						currentSeq : 0,
						menuData : null,
						menuSet: function(idx) {

							if (!this.menuData) return false;

							this.currentMenu = this.menuData[idx];

							$('el-quick-search-keyword').value = this.currentMenu.title;
							$('el-quick-search-keyword').focus();

							this.currentSeq = idx;
							this.highlight(false);

						},
						changeMode : function(mode) {

							this.menuData = null;
							this.currentMenu = null;
							this.currentSeq = 0;
							$('el-quick-search-keyword').value = '';
							$('el-quick-search-keyword-suggest').hide();

							//changeMode
							/*/
							var today = new Date();
							var expire_date = new Date(today.getTime() + 31536000);
							/*/
							var expire_date = 0;
							/**/
							setCookie( '_admin_quick_search_mode', mode , expire_date, '/');

						},
						isMenuSearch : function() {
							return $('el-quick-search-menu').checked ? true : false;
						},
						suggest : function() {

							// 메뉴 검색
							if (this.isMenuSearch()) {

								var keyword = $('el-quick-search-keyword').value;

								var self = this;

								self.currentSeq = 0;
								self.menuData = null;
								self.currentMenu = null;

								var ajax = new Ajax.Request('../proc/ax.menu_finder.php', {
									method: "post",
									parameters: 'keyword='+keyword,
									asynchronous: true,
									onComplete: function(response) { if (response.status == 200) {

										self.menuData = response.responseText.evalJSON(true);

										if (self.menuData.size() > 0) {
											var html = '<ul>';

											self.menuData.each(function(row, idx){

												html += '<li onclick="nsQuickSearch.menuSet('+idx+');"';
												if (idx == 0) html += ' class="on"';
												html += '>';

												html += row.title;
												html += '</li>';

											});

											html += '</ul>';

											$('el-quick-search-keyword-suggest').update(html);
											$('el-quick-search-keyword-suggest').show();

										}
										else {
											$('el-quick-search-keyword-suggest').hide();
										}

									}}
								});
							}
							// crm 검색
							else {
								return;
							}
						},
						go : function () {

							// 검색어
							var keyword = $('el-quick-search-keyword').value;

							// 메뉴 검색
							if (this.isMenuSearch()) {

								if( !keyword ){
									alert('검색할 메뉴명을 넣어주세요.');
									return false;
								}

								if (this.currentMenu) {
									var url = this.currentMenu.url;
									window.location.replace(url);
								}
								else {
									alert('찾으시는 메뉴가 없습니다.');
									return false;
								}

							}
							// crm 검색
							else {

								if( !keyword ){
									alert('검색할 회원정보(아이디 or 이름)를 넣어주세요.');
									return false;
								}
								window.open('../member/popup.list.php?skey=all&sword='+keyword,'crmAdminPopUp','width=800,height=600,scrollbars=1');
							}

						},
						highlight : function(scroll) {

							var self = this;

							var div  = $('el-quick-search-keyword-suggest');
							var ul	 = div.down();
							var lis	 = ul.childElements();
							var _height = div.getHeight();

							lis.each(function(li,idx){
								if (idx == self.currentSeq) {

									li.addClassName('on');

									if (scroll)
									{
										var __height = li.getHeight() * (idx + 1) + 10;

										if (_height < __height) {
											div.scrollTop = __height - _height;
										}
										else if (_height > __height) {
											div.scrollTop = 0;
										}
									}

								}
								else {
									li.removeClassName('on');
								}
							});

						},
						suggestMove : function(direction) {

							if (!this.menuData) return false;

							var self = this;
							var lis  = $('el-quick-search-keyword-suggest').down().childElements();

							self.currentMenu = null;
							self.currentSeq = parseInt(self.currentSeq) + parseInt(direction);

							if (self.currentSeq < 0) {
								self.currentSeq = lis.size() - 1;
							}
							else if (self.currentSeq >= lis.size()) {
								self.currentSeq = 0;
							}

							self.highlight(true);


						},
						start : function() {

							if (event.keyCode == 40) {	// 아래
								if (this.isMenuSearch()) {
									this.suggestMove(1);
								}
							}
							else if (event.keyCode == 38) {// 위
								if (this.isMenuSearch()) {
									this.suggestMove(-1);
								}
							}
							else if (event.keyCode == 13) {
								if (!this.isMenuSearch()) {
									this.go();
								}
								else {
									if (this.currentMenu == null) {
										this.menuSet( this.currentSeq );
									}
									else {
										this.go();
									}

								}
							}
							else {
								this.suggest();
							}

						}
					}
				}();

				</script>
				<div class="choice">
					<input type="radio" class="radio" name="quick-search" id="el-quick-search-menu" onClick="nsQuickSearch.changeMode('menu');" value="menu" <?=($_COOKIE['_admin_quick_search_mode'] == 'menu') ? 'checked' : ''?> title="관리자 메뉴검색" />
					<label for="condition1">관리자 메뉴검색</label>
					<input type="radio" class="radio" name="quick-search" id="el-quick-search-crm" onClick="nsQuickSearch.changeMode('crm');" value="crm" <?=($_COOKIE['_admin_quick_search_mode'] != 'menu') ? 'checked' : ''?> title="회원검색" />
					<label for="condition2">회원검색</label>
				</div>
				<div class="input-text">
					<input type="text" class="text" name="el-quick-search-keyword" id="el-quick-search-keyword" value="" onkeyup="nsQuickSearch.start();" title="검색어 입력" />
					<img src="../img/header/topmenu_btn_search.gif" class="image" onclick="nsQuickSearch.go();" alt="검색" />
					<ul class="autocomplete" id="el-quick-search-keyword-suggest" style="display:none;"></ul>
				</div>
		</div>
		<!--------------- 메뉴검색, 회원CRM 시작 --------------------->
	</div>

	<!--------------- 네비게이션 --------------------->
	<ul class="bottom-menu">
		<li><a href="<?=$sitelink->link('admin/basic/default.php', 'regular')?>"><img src="../img/header/gnb_menu1<?=$over['basic']?>.gif" alt="기본설정" /></a></li>
		<li><a href="<?=$sitelink->link('admin/design/codi.php', 'regular')?>"><img src="../img/header/gnb_menu2<?=$over['design']?>.gif" alt="디자인" /></a></li>
		<li><a href="<?=$sitelink->link('admin/goods/list.php', 'regular')?>"><img src="../img/header/gnb_menu3<?=$over['goods']?>.gif" alt="상품" /></a></li>
		<li><a href="<?=$sitelink->link('admin/order/list.php?mode=group&period=0&first=1', 'regular')?>"><img src="../img/header/gnb_menu4<?=$over['order']?>.gif" alt="주문" /></a></li>
		<li><a href="<?=$sitelink->link('admin/member/list.php', 'regular')?>"><img src="../img/header/gnb_menu5<?=$over['member']?>.gif" alt="회원" /></a></li>
		<li><a href="<?=$sitelink->link('admin/board/list_management.php', 'regular')?>"><img src="../img/header/gnb_menu6<?=$over['board']?>.gif" alt="게시판" /></a></li>
		<li><a href="<?=$sitelink->link('admin/event/list.php', 'regular')?>"><img src="../img/header/gnb_menu7<?=$over['promotion']?>.gif" alt="프로모션" /></a></li>
		<li><a href="<?=$sitelink->link('admin/marketing/main.php', 'regular')?>"><img src="../img/header/gnb_menu8<?=$over['marketing']?>.gif" alt="마케팅" /></a></li>
		<li><a href="<?=$sitelink->link('admin/log/index.php', 'regular')?>"><img src="../img/header/gnb_menu9<?=$over['log']?>.gif" alt="통계" /></a></li>
		<li><a href="<?=$sitelink->link('admin/'.$mobileShop.'/index.php', 'regular')?>"><img src="../img/header/gnb_menu10<?=$over[$mobileShop]?>.gif" alt="모바일샵" /></a></li>
		<li><a href="<?=$sitelink->link('admin/selly/index.php', 'regular')?>"><img src="../img/header/gnb_menu11<?=$over['selly']?>.gif" alt="셀리" /></a></li>
		<li><a href="<?=$sitelink->link('admin/overseas/intro.php', 'regular')?>"><img src="../img/header/gnb_menu13<?=$over['overseas']?>.gif" alt="해외판매" /></a></li>
		<li><a href="<?=$sitelink->link('admin/etc/index.php', 'regular')?>"><img src="../img/header/gnb_menu14<?=$over['etc']?>.gif" alt="운영지원" /></a></li>
	</ul>
	<!--------------- 네비게이션 --------------------->
</div>