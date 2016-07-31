<?php /* Template_ 2.2.7 2015/11/16 19:50:41 /www/francotr3287_godo_co_kr/shop/data/skin/freemart/proc/menuCategory.htm 000044150 */  $this->include_("dataCategory");?>
<!-- gdpart mode="open" fid="goods/goods_view.htm menuCategory_4" --><!-- gdline 1"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" --><!-- gdline 2"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" --><div id="t_cate">
<!-- gdline 3"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->	<?php if($GLOBALS["cfg"]["subCategory"]!= 2){?>
<!-- gdline 4"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->	<div id="r_cate">
<!-- gdline 5"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->		<table cellpadding=0 cellspacing=0 id=menuLayer align=center border="0" width="100%">
<!-- gdline 6"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->			<tr class="catebar_tr" >
<!-- gdline 7"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->			<?php if((is_array($TPL_R1=dataCategory($GLOBALS["cfg"]["subCategory"], 1))&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?>
<!-- gdline 8"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->				<td id='level1' class="catebar"  >
<!-- gdline 9"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->					<a href="<?php echo url("goods/goods_list.php?")?>&category=<?php echo $TPL_V1["category"]?>"><?php echo $TPL_V1["catnm"]?></a>
<!-- gdline 10"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->				<?php if($TPL_V1["sub"]){?>
<!-- gdline 11"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->				<div id="subMenuWrapper">
<!-- gdline 12"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->					<div class="subLayer">
<!-- gdline 13"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->						<table width="100%" cellspacing=0 cellpadding="0" border="0" id="table_arrow" style="background-color:#376e88">
<!-- gdline 14"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->						<tr>
<!-- gdline 15"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->							<td><img src="/shop/data/images/web/icon/icon_arrow.gif"></td>
<!-- gdline 16"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->						</tr>
<!-- gdline 17"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->						</table>
<!-- gdline 18"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->						<div id="subcategory">
<!-- gdline 19"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->						<table width="100%" cellspacing=0 cellpadding="0" border="0" id="table_cate">
<!-- gdline 20"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->							
<!-- gdline 21"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->							<?php if((is_array($TPL_R2=$TPL_V1["sub"])&&!empty($TPL_R2)) || (is_object($TPL_R2) && in_array("Countable", class_implements($TPL_R2)) && $TPL_R2->count() > 0)) {foreach($TPL_R2 as $TPL_V2){?>
<!-- gdline 22"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->							<tr>
<!-- gdline 23"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->								<td align="left" nowrap class="cate" onclick="javascript:location.href='<?php echo url("goods/goods_list.php?")?>&category=<?php echo $TPL_V2["category"]?>'"><a href="<?php echo url("goods/goods_list.php?")?>&category=<?php echo $TPL_V2["category"]?>"><?php echo $TPL_V2["catnm"]?></a></td>
<!-- gdline 24"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->							</tr>
<!-- gdline 25"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->							<?php }}?>
<!-- gdline 26"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->						</table>
<!-- gdline 27"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->						</div>
<!-- gdline 28"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->					</div>
<!-- gdline 29"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->				</div>
<!-- gdline 30"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->				<?php }?>
<!-- gdline 31"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->			</td>
<!-- gdline 32"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->				<td style="text-align:center;color:#ccc;width:2px;">|</td>	
<!-- gdline 33"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->			<?php }}?>
<!-- gdline 34"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->			
<!-- gdline 35"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->		</tr>
<!-- gdline 36"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->		</table>
<!-- gdline 37"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->		<?php if($GLOBALS["cfg"]["subCategory"]){?>
<!-- gdline 38"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->		<script>_execSubLayerTop();</script>
<!-- gdline 39"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->		<?php }?>
<!-- gdline 40"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->		
<!-- gdline 41"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->	</div>
<!-- gdline 42"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->	
<!-- gdline 43"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->	<div id="sale_menu">
<!-- gdline 44"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->		<div id="sale_icon"><img src="/shop/data/images/web/icon/1412717979_label_sale.png"></div>
<!-- gdline 45"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->		
<!-- gdline 46"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->	</div>
<!-- gdline 47"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->	
<!-- gdline 48"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->	<div id="brands" class="main_navi" style="display:none;">
<!-- gdline 49"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->		<div id="DB_navi38_2">
<!-- gdline 50"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->			<div class="DB_etc4_bar">
<!-- gdline 51"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->					<a href="javascript:showonlyone('brands');"><img src="/shop/data/images/web/icon/Close_Box_Red.png" border="0" align="right"></a>
<!-- gdline 52"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->				</div>
<!-- gdline 53"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->			<div id="DB_etc4">
<!-- gdline 54"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->				<ul class="DB_btn">
<!-- gdline 55"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->					<li class="DB_all"><a href="#DB" class="DB_select">all</a></li>
<!-- gdline 56"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->					<li class="gap"></li>
<!-- gdline 57"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->					<li class="DB_k1"><a href="#DB">ㄱ</a></li>
<!-- gdline 58"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->					<li class="DB_k2"><a href="#DB">ㄴ</a></li>
<!-- gdline 59"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->					<li class="DB_k3"><a href="#DB">ㄷ</a></li>
<!-- gdline 60"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->					<li class="DB_k4"><a href="#DB">ㄹ</a></li>
<!-- gdline 61"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->					<li class="DB_k5"><a href="#DB">ㅁ</a></li>
<!-- gdline 62"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->					<li class="DB_k6"><a href="#DB">ㅂ</a></li>
<!-- gdline 63"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->					<li class="DB_k7"><a href="#DB">ㅅ</a></li>
<!-- gdline 64"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->					<li class="DB_k8"><a href="#DB">ㅇ</a></li>
<!-- gdline 65"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->					<li class="DB_k9"><a href="#DB">ㅈ</a></li>
<!-- gdline 66"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->					<li class="DB_k10"><a href="#DB">ㅊ</a></li>
<!-- gdline 67"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->					<li class="DB_k11"><a href="#DB">ㅋ</a></li>
<!-- gdline 68"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->					<li class="DB_k12"><a href="#DB">ㅌ</a></li>
<!-- gdline 69"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->					<li class="DB_k13"><a href="#DB">ㅍ</a></li>
<!-- gdline 70"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->					<li class="DB_k14"><a href="#DB">ㅎ</a></li>
<!-- gdline 71"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->					<li class="gap"></li>
<!-- gdline 72"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->					<li class="DB_k15"><a href="#DB">A</a></li>
<!-- gdline 73"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->					<li class="DB_k16"><a href="#DB">B</a></li>
<!-- gdline 74"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->					<li class="DB_k17"><a href="#DB">C</a></li>
<!-- gdline 75"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->					<li class="DB_k18"><a href="#DB">D</a></li>
<!-- gdline 76"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->					<li class="DB_k19"><a href="#DB">E</a></li>
<!-- gdline 77"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->					<li class="DB_k20"><a href="#DB">F</a></li>
<!-- gdline 78"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->					<li class="DB_k21"><a href="#DB">G</a></li>
<!-- gdline 79"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->					<li class="DB_k22"><a href="#DB">H</a></li>
<!-- gdline 80"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->					<li class="DB_k23"><a href="#DB">I</a></li>
<!-- gdline 81"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->					<li class="DB_k24"><a href="#DB">J</a></li>
<!-- gdline 82"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->					<li class="DB_k25"><a href="#DB">K</a></li>
<!-- gdline 83"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->					<li class="DB_k26"><a href="#DB">L</a></li>
<!-- gdline 84"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->					<li class="DB_k27"><a href="#DB">M</a></li>
<!-- gdline 85"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->					<li class="DB_k28"><a href="#DB">N</a></li>
<!-- gdline 86"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->					<li class="DB_k29"><a href="#DB">O</a></li>
<!-- gdline 87"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->					<li class="DB_k30"><a href="#DB">P</a></li>
<!-- gdline 88"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->					<li class="DB_k31"><a href="#DB">Q</a></li>
<!-- gdline 89"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->					<li class="DB_k32"><a href="#DB">R</a></li>
<!-- gdline 90"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->					<li class="DB_k33"><a href="#DB">S</a></li>
<!-- gdline 91"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->					<li class="DB_k34"><a href="#DB">T</a></li>
<!-- gdline 92"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->					<li class="DB_k35"><a href="#DB">U</a></li>
<!-- gdline 93"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->					<li class="DB_k36"><a href="#DB">V</a></li>
<!-- gdline 94"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->					<li class="DB_k37"><a href="#DB">W</a></li>
<!-- gdline 95"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->					<li class="DB_k38"><a href="#DB">X</a></li>
<!-- gdline 96"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->					<li class="DB_k39"><a href="#DB">Y</a></li>
<!-- gdline 97"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->					<li class="DB_k40"><a href="#DB">Z</a></li>
<!-- gdline 98"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->					<li class="gap"></li>
<!-- gdline 99"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->					<li class="DB_eng"><a href="#DB">eng</a></li>
<!-- gdline 100"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->					<li class="DB_etc"><a href="#DB">etc</a></li>
<!-- gdline 101"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->				</ul>
<!-- gdline 102"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->				<ul class="DB_list">
<!-- gdline 103"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->					
<!-- gdline 104"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->					<li><a href="/shop/goods/goods_brand.php?&brand=64" data-key="ㄱ">가야</a></li>
<!-- gdline 105"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->					<li><a href="/shop/goods/goods_brand.php?&brand=" data-key="ㄱ">고릴라글루</a></li>
<!-- gdline 106"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->					<li><a href="/shop/goods/goods_brand.php?&brand=" data-key="ㄱ">가또블랑코</a></li>
<!-- gdline 107"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->					<li><a href="/shop/goods/goods_brand.php?&brand=" data-key="ㄴ">나렉스</a></li>
<!-- gdline 108"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->					<li><a href="/shop/goods/goods_brand.php?&brand=" data-key="ㄷ">디어포스</a></li>
<!-- gdline 109"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->					<li><a href="/shop/goods/goods_brand.php?&brand=" data-key="ㄹ">리넬슨</a></li>
<!-- gdline 110"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->					
<!-- gdline 111"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->                    <li><a href="/shop/goods/goods_brand.php?&brand=" data-key="ㄹ">로버트 솔비</a></li>
<!-- gdline 112"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->                    <li><a href="/shop/goods/goods_brand.php?&brand=" data-key="ㄹ">루녹스</a></li>
<!-- gdline 113"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->                    <li><a href="/shop/goods/goods_brand.php?&brand=" data-key="ㄹ">료비</a></li>
<!-- gdline 114"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->                    <li><a href="/shop/goods/goods_brand.php?&brand=" data-key="ㅁ">마이크로지그</a></li>
<!-- gdline 115"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->                    <li><a href="/shop/goods/goods_brand.php?&brand=" data-key="ㅁ">마코</a></li>
<!-- gdline 116"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->                    <li><a href="/shop/goods/goods_brand.php?&brand=" data-key="ㅁ">매그스위치</a></li>
<!-- gdline 117"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->                    <li><a href="/shop/goods/goods_brand.php?&brand=" data-key="ㅁ">명화금속</a></li>
<!-- gdline 118"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->                    
<!-- gdline 119"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->                    <li><a href="/shop/goods/goods_brand.php?&brand=" data-key="ㅂ">빅마크</a></li>
<!-- gdline 120"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->					<li><a href="/shop/goods/goods_brand.php?&brand=" data-key="ㅂ">베세이</a></li>
<!-- gdline 121"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->                    <li><a href="/shop/goods/goods_brand.php?&brand=" data-key="ㅂ">본덱스</a></li>
<!-- gdline 122"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->					<li><a href="/shop/goods/goods_brand.php?&brand=" data-key="ㅅ">스마토</a></li>
<!-- gdline 123"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->					<li><a href="/shop/goods/goods_brand.php?&brand=" data-key="ㅅ">스타엠</a></li>
<!-- gdline 124"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->					<li><a href="/shop/goods/goods_brand.php?&brand=" data-key="ㅅ">스타빌라</a></li>
<!-- gdline 125"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->                    <li><a href="/shop/goods/goods_brand.php?&brand=" data-key="ㅅ">샤프톤</a></li>
<!-- gdline 126"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->					<li><a href="/shop/goods/goods_brand.php?&brand=" data-key="ㅇ">어윈</a></li>
<!-- gdline 127"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->                    <li><a href="/shop/goods/goods_brand.php?&brand=" data-key="ㅇ">오성정밀화학</a></li>
<!-- gdline 128"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->                    <li><a href="/shop/goods/goods_brand.php?&brand=" data-key="ㅇ">오스모</a></li>
<!-- gdline 129"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->					<li><a href="/shop/goods/goods_brand.php?&brand=" data-key="ㅇ">용수</a></li>
<!-- gdline 130"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->
<!-- gdline 131"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->                    <li><a href="/shop/goods/goods_brand.php?&brand=" data-key="ㅈ">제타</a></li>	
<!-- gdline 132"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->                    <li><a href="/shop/goods/goods_brand.php?&brand=" data-key="ㅊ">철마</a></li>
<!-- gdline 133"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->                    <li><a href="/shop/goods/goods_brand.php?&brand=" data-key="ㅋ">카쿠리</a></li>
<!-- gdline 134"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->                    <li><a href="/shop/goods/goods_brand.php?&brand=" data-key="ㅋ">크레그</a></li>
<!-- gdline 135"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->                    <li><a href="/shop/goods/goods_brand.php?&brand=" data-key="ㅋ">클린-스트립</a></li>
<!-- gdline 136"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->                    	
<!-- gdline 137"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->					<li><a href="/shop/goods/goods_brand.php?&brand=" data-key="ㅌ">타이트본드</a></li>
<!-- gdline 138"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->                    <li><a href="/shop/goods/goods_brand.php?&brand=" data-key="ㅌ">타지마</a></li>
<!-- gdline 139"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->                    <li><a href="/shop/goods/goods_brand.php?&brand=" data-key="ㅌ">토맥</a></li>
<!-- gdline 140"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->                    <li><a href="/shop/goods/goods_brand.php?&brand=" data-key="ㅍ">파텍스</a></li>
<!-- gdline 141"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->                    <li><a href="/shop/goods/goods_brand.php?&brand=" data-key="ㅍ">피닉스라이트</a></li>
<!-- gdline 142"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->                    <li><a href="/shop/goods/goods_brand.php?&brand=" data-key="ㅍ">피스카스</a></li>
<!-- gdline 143"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->                    <li><a href="/shop/goods/goods_brand.php?&brand=" data-key="ㅍ">페일</a></li>
<!-- gdline 144"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->					<li><a href="/shop/goods/goods_brand.php?&brand=" data-key="ㅎ">핸켈</a></li>
<!-- gdline 145"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->					
<!-- gdline 146"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->						
<!-- gdline 147"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->                        
<!-- gdline 148"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->                        
<!-- gdline 149"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->                        <li><a href="/shop/goods/goods_brand.php?&brand=51" data-key="A">Aplus</a></li>
<!-- gdline 150"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->                        <li><a href="/shop/goods/goods_brand.php?&brand=54" data-key="B">Bessey</a></li>
<!-- gdline 151"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->                        <li><a href="/shop/goods/goods_brand.php?&brand=" data-key="B">BONDEX</a></li>
<!-- gdline 152"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->                        <li><a href="/shop/goods/goods_brand.php?&brand=9" data-key="C">C.H. HANSON</a></li>
<!-- gdline 153"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->                        <li><a href="/shop/goods/goods_brand.php?&brand=10" data-key="C">CMT</a></li>
<!-- gdline 154"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->                        <li><a href="/shop/goods/goods_brand.php?&brand=48" data-key="C">COLT</a></li>
<!-- gdline 155"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->                        <li><a href="/shop/goods/goods_brand.php?&brand=" data-key="D">DEERFOS</a></li>
<!-- gdline 156"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->                        <li><a href="/shop/goods/goods_brand.php?&brand=" data-key="D">DKP</a></li>
<!-- gdline 157"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->                        <li><a href="/shop/goods/goods_brand.php?&brand=" data-key="D">DMS</a></li>
<!-- gdline 158"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->                        <li><a href="/shop/goods/goods_brand.php?&brand=" data-key="D">DMT</a></li>
<!-- gdline 159"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->                        <li><a href="/shop/goods/goods_brand.php?&brand=" data-key="E">EIGHT</a></li>
<!-- gdline 160"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->                        <li><a href="/shop/goods/goods_brand.php?&brand=" data-key="F">FESTOOL</a></li>
<!-- gdline 161"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->                        <li><a href="/shop/goods/goods_brand.php?&brand=" data-key="F">Fiskars</a></li>
<!-- gdline 162"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->                        <li><a href="/shop/goods/goods_brand.php?&brand=" data-key="F">Francosmith</a></li>
<!-- gdline 163"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->                        <li><a href="/shop/goods/goods_brand.php?&brand=64" data-key="G">GAYA</a></li>
<!-- gdline 164"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->                        <li><a href="/shop/goods/goods_brand.php?&brand=" data-key="G">Gorilla Glue</a></li>
<!-- gdline 165"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->                        <li><a href="/shop/goods/goods_brand.php?&brand=" data-key="H">HANKEL</a></li>
<!-- gdline 166"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->                        <li><a href="/shop/goods/goods_brand.php?&brand=" data-key="I">IRWIN</a></li>
<!-- gdline 167"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->                        <li><a href="/shop/goods/goods_brand.php?&brand=" data-key="J">JET</a></li>
<!-- gdline 168"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->                        <li><a href="/shop/goods/goods_brand.php?&brand=" data-key="J">Jorgensen</a></li>
<!-- gdline 169"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->                        <li><a href="/shop/goods/goods_brand.php?&brand=" data-key="K">Kakuri</a></li>
<!-- gdline 170"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->                        <li><a href="/shop/goods/goods_brand.php?&brand=" data-key="K">Klean-Strip</a></li>
<!-- gdline 171"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->                        <li><a href="/shop/goods/goods_brand.php?&brand=" data-key="K">Kreg</a></li>
<!-- gdline 172"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->                        <li><a href="/shop/goods/goods_brand.php?&brand=" data-key="L">Leigh</a></li>
<!-- gdline 173"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->                        <li><a href="/shop/goods/goods_brand.php?&brand=" data-key="L">Lie-Nielsen</a></li>
<!-- gdline 174"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->                        <li><a href="/shop/goods/goods_brand.php?&brand=" data-key="L">Lunox</a></li>
<!-- gdline 175"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->                        <li><a href="/shop/goods/goods_brand.php?&brand=" data-key="M">MagSwitch</a></li>
<!-- gdline 176"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->                        <li><a href="/shop/goods/goods_brand.php?&brand=" data-key="M">MAKO</a></li>
<!-- gdline 177"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->                        <li><a href="/shop/goods/goods_brand.php?&brand=" data-key="M">MHG</a></li>
<!-- gdline 178"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->                        <li><a href="/shop/goods/goods_brand.php?&brand=" data-key="M">Micro Jiig</a></li>
<!-- gdline 179"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->                        <li><a href="/shop/goods/goods_brand.php?&brand=" data-key="N">Narex</a></li>
<!-- gdline 180"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->                        <li><a href="/shop/goods/goods_brand.php?&brand=" data-key="O">OSMO</a></li>
<!-- gdline 181"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->                        <li><a href="/shop/goods/goods_brand.php?&brand=" data-key="P">PFEIL</a></li>
<!-- gdline 182"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->                        <li><a href="/shop/goods/goods_brand.php?&brand=" data-key="P">PHOENIX LIGHT</a></li>
<!-- gdline 183"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->                        <li><a href="/shop/goods/goods_brand.php?&brand=" data-key="P">PowerMatic</a></li>
<!-- gdline 184"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->                        <li><a href="/shop/goods/goods_brand.php?&brand=" data-key="R">Robert Sorby</a></li>
<!-- gdline 185"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->                        <li><a href="/shop/goods/goods_brand.php?&brand=" data-key="R">RYOBI</a></li>
<!-- gdline 186"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->                        <li><a href="/shop/goods/goods_brand.php?&brand=" data-key="S">SHAPTON</a></li>
<!-- gdline 187"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->                        <li><a href="/shop/goods/goods_brand.php?&brand=" data-key="S">SHINWA</a></li>
<!-- gdline 188"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->                        <li><a href="/shop/goods/goods_brand.php?&brand=" data-key="S">SMATO</a></li>
<!-- gdline 189"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->                        <li><a href="/shop/goods/goods_brand.php?&brand=" data-key="S">STABILA</a></li>
<!-- gdline 190"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->                        <li><a href="/shop/goods/goods_brand.php?&brand=" data-key="S">STAR-M</a></li>
<!-- gdline 191"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->                        <li><a href="/shop/goods/goods_brand.php?&brand=" data-key="T">TAJIMA</a></li>
<!-- gdline 192"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->                        <li><a href="/shop/goods/goods_brand.php?&brand=" data-key="T">Titebond</a></li>
<!-- gdline 193"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->                        <li><a href="/shop/goods/goods_brand.php?&brand=" data-key="T">Tormek</a></li>
<!-- gdline 194"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->                        <li><a href="/shop/goods/goods_brand.php?&brand=" data-key="U">UTOOLS</a></li>
<!-- gdline 195"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->                        <li><a href="/shop/goods/goods_brand.php?&brand=" data-key="U">UVEX</a></li>
<!-- gdline 196"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->                        <li><a href="/shop/goods/goods_brand.php?&brand=" data-key="V">VICMARC</a></li>
<!-- gdline 197"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->                        <li><a href="/shop/goods/goods_brand.php?&brand=" data-key="W">WATCO</a></li>
<!-- gdline 198"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->                        <li><a href="/shop/goods/goods_brand.php?&brand=" data-key="X">MENU10</a></li>
<!-- gdline 199"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->                        <li><a href="/shop/goods/goods_brand.php?&brand=" data-key="Z">ZETA</a></li>
<!-- gdline 200"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->                    
<!-- gdline 201"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->                        <li><a href="#" data-key="eng">AKG</a></li>
<!-- gdline 202"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->						<li><a href="#" data-key="eng">SPIGEN</a></li>
<!-- gdline 203"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->						<li><a href="#" data-key="eng">SGPAKG</a></li>
<!-- gdline 204"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->						<li><a href="#" data-key="eng">NIKE</a></li>
<!-- gdline 205"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->						<li><a href="#" data-key="eng">SAMSUNG</a></li>
<!-- gdline 206"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->						<li><a href="#" data-key="eng">APPLE</a></li>
<!-- gdline 207"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->						<li><a href="#" data-key="eng">SOLNY</a></li>
<!-- gdline 208"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->                    
<!-- gdline 209"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->                        
<!-- gdline 210"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->				</ul>
<!-- gdline 211"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->			</div>
<!-- gdline 212"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->			<div id="rc_brand" >
<!-- gdline 213"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->				<ul>
<!-- gdline 214"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->					<li><a href="/shop/goods/goods_brand.php?&brand=65"><img src="http://francosmith.godohosting.com/main/brand/vicmarc.jpg" border="0"></a></li>
<!-- gdline 215"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->					<li><a href="/shop/goods/goods_brand.php?&brand=14"><img src="http://francosmith.godohosting.com/main/brand/pfeil.jpg" border="0"></a></li>
<!-- gdline 216"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->					<li><a href="/shop/goods/goods_brand.php?&brand=2"><img src="http://francosmith.godohosting.com/main/brand/microjig.jpg" border="0"></a></li>
<!-- gdline 217"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->				</ul>
<!-- gdline 218"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->			</div>
<!-- gdline 219"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->		</div>
<!-- gdline 220"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->	</div>
<!-- gdline 221"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->		
<!-- gdline 222"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->	
<!-- gdline 223"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->	<div id="all_menu" class="main_navi" style="display:none;">
<!-- gdline 224"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->		<div class="DB_etc4_bar">
<!-- gdline 225"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->			<a href="javascript:showonlyone('all_menu');"><img src="/shop/data/images/web/icon/Close_Box_Red.png" border="0"></a>
<!-- gdline 226"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->		</div>
<!-- gdline 227"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->		<div>
<!-- gdline 228"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->		  <ul>
<!-- gdline 229"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->		  	  <li class="l1">수공구</li>
<!-- gdline 230"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->			  <li class="l2">끌(Chisel)</li>
<!-- gdline 231"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->		  </ul>
<!-- gdline 232"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->		</div>
<!-- gdline 233"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->		<div>
<!-- gdline 234"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->		  <ul>
<!-- gdline 235"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->		  	  <li class="l1">수공구</li>
<!-- gdline 236"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->			  <li class="l2">끌(Chisel)</li>
<!-- gdline 237"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->		  </ul>
<!-- gdline 238"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->		</div>
<!-- gdline 239"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->	</div>
<!-- gdline 240"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->	
<!-- gdline 241"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->	
<!-- gdline 242"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->	<?php }else{?>
<!-- gdline 243"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->	<table cellpadding=0 cellspacing=0 border=0 class="cateUnfold" align=center>
<!-- gdline 244"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->	<tr>
<!-- gdline 245"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->		<?php if((is_array($TPL_R1=dataCategory($GLOBALS["cfg"]["subCategory"], 1))&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?>
<!-- gdline 246"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->		<td style="<?php if(strpos($TPL_V1["catnm"],'img')===false){?>padding:5px 19px 0 19px<?php }?>" class="catebar"><a href="<?php echo url("goods/goods_list.php?")?>&category=<?php echo $TPL_V1["category"]?>"><?php echo $TPL_V1["catnm"]?></a>
<!-- gdline 247"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->			<?php if($TPL_V1["sub"]){?>
<!-- gdline 248"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->			<div class="catesub">
<!-- gdline 249"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->				<table>
<!-- gdline 250"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->				<?php if((is_array($TPL_R2=$TPL_V1["sub"])&&!empty($TPL_R2)) || (is_object($TPL_R2) && in_array("Countable", class_implements($TPL_R2)) && $TPL_R2->count() > 0)) {foreach($TPL_R2 as $TPL_V2){?>
<!-- gdline 251"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->				<tr>
<!-- gdline 252"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->					<td class="cate"><a href="<?php echo url("goods/goods_list.php?")?>&category=<?php echo $TPL_V2["category"]?>">- <?php echo $TPL_V2["catnm"]?></a></td>
<!-- gdline 253"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->				</tr>
<!-- gdline 254"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->				<?php }}?>
<!-- gdline 255"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->				</table>
<!-- gdline 256"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->			</div>
<!-- gdline 257"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->			<?php }?>
<!-- gdline 258"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->		</td>
<!-- gdline 259"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->		<?php }}?>
<!-- gdline 260"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->	</tr>
<!-- gdline 261"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->	</table>
<!-- gdline 262"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->	<?php }?>
<!-- gdline 263"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->	
<!-- gdline 264"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->	
<!-- gdline 265"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->
<!-- gdline 266"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->
<!-- gdline 267"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->	
<!-- gdline 268"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" --></div>
<!-- gdline 269"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->
<!-- gdline 270"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" -->
<!-- gdline 271"/proc/menuCategory.htm|/proc/menuCategory.htm|goods/goods_view.htm menuCategory_4" --><!-- gdpart mode="close" fid="goods/goods_view.htm menuCategory_4" -->