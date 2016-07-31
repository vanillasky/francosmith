<?php

$location = '기타관리 > 쇼핑몰 로딩 속도 설정';
include '../_header.php';

$templateCache = Core::loader('TemplateCache');
$pageCacheConfig = $templateCache->loadConfig();

?>
<script type="text/javascript">
jQuery(document).ready(function(){
	jQuery("#cache-use-type-none").click(function(){
		jQuery("#page-cache-form").addClass("none").removeClass("default").removeClass("advanced");
	});
	jQuery("#cache-use-type-default").click(function(){
		jQuery("#page-cache-form").removeClass("none").addClass("default").removeClass("advanced");
	});
	jQuery("#cache-use-type-advanced").click(function(){
		jQuery("#page-cache-form").removeClass("none").removeClass("default").addClass("advanced");
	});
	jQuery("#cache-use-type-<?php echo $pageCacheConfig['cacheUseType']; ?>").click();
	jQuery("select[name=expireInterval] option[value=<?php echo $pageCacheConfig['expireInterval']; ?>]").attr("selected", true);
	jQuery("select[name=expireInterval_pc_main_index] option[value=<?php echo $pageCacheConfig['pageExpireInterval']['{:PC:}/main/index.php']; ?>]").attr("selected", true);
	jQuery("select[name=expireInterval_pc_goods_goods_list] option[value=<?php echo $pageCacheConfig['pageExpireInterval']['{:PC:}/goods/goods_list.php']; ?>]").attr("selected", true);
	jQuery("select[name=expireInterval_pc_board_list] option[value=<?php echo $pageCacheConfig['pageExpireInterval']['{:PC:}/board/list.php']; ?>]").attr("selected", true);
	jQuery("select[name=expireInterval_pc_goods_goods_review_and_qna] option[value=<?php echo $pageCacheConfig['pageExpireInterval']['{:PC:}/goods/goods_qna_list.php']; ?>]").attr("selected", true);
	jQuery("select[name=expireInterval_mobile_main_index] option[value=<?php echo $pageCacheConfig['pageExpireInterval']['{:MOBILE:}/index.php']; ?>]").attr("selected", true);
	jQuery("select[name=expireInterval_mobile_board_list] option[value=<?php echo $pageCacheConfig['pageExpireInterval']['{:MOBILE:}/board/list.php']; ?>]").attr("selected", true);
	jQuery("#clear-cache").click(function(){
		ifrmHidden.location.href = "./adm_etc_cache_page.indb.php?mode=clearCache";
	});
	jQuery(".clear-cache").click(function(){
		ifrmHidden.location.href = "./adm_etc_cache_page.indb.php?mode=clearCache&page=" + this.getAttribute("data-page");
	});
});
</script>
<style type="text/css">
	#cache-target-page, #cache-target-mobile-page {
		padding-left: 0;
	}
	#cache-target-page li, #cache-target-mobile-page li {
		overflow: hidden;
		margin: 10px 0;
		list-style: none;
	}
	#cache-target-page li span, #cache-target-mobile-page li span {
		float: left;
	}
	#cache-target-page li select.page-expire-interval, #cache-target-mobile-page li select.page-expire-interval, button.clear-cache {
		float: right;
	}
	#page-cache-form.none tr.enable {
		display: none;
	}
	#page-cache-form.default .expire-interval-guide, #page-cache-form.default button.clear-cache, #page-cache-form.default .page-expire-interval {
		display: none;
	}
	#page-cache-form.advanced .expire-interval, #page-cache-form.advanced #clear-cache {
		display: none;
	}
	#clear-cache {
		cursor: pointer;
	}
	button.clear-cache {
		display: block;
		background-image: url("../img/btn_renew.gif");
		background-repeat: no-repeat;
		border: none;
		text-indent: -1000px;
		width: 64px;
		height: 14px;
		margin: 3px;
		cursor: pointer;
	}
</style>

<div class="title title_top">
	웹페이지 캐시 설정
	<a href="javascript:manual('<?php echo $guideUrl; ?>board/view.php?id=basic&no=43')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a>
</div>
<form id="page-cache-form" class="admin-form" method="post" target="ifrmHidden" action="adm_etc_cache_page.indb.php">
	<input type="hidden" name="mode" value="save"/>
	<table class="admin-form-table" style="width: 800px;">
		<tr>
			<th style="width: 150px;">웹페이지 캐시 사용여부</th>
			<td colspan="2">
				<input id="cache-use-type-none" type="radio" name="cacheUseType" value="none"/>
				<label for="cache-use-type-none">사용안함</label>
				<input id="cache-use-type-default" type="radio" name="cacheUseType" value="default"/>
				<label for="cache-use-type-default">기본설정 사용</label>
				<input id="cache-use-type-advanced" type="radio" name="cacheUseType" value="advanced"/>
				<label for="cache-use-type-advanced">고급설정 사용</label>
			</td>
		</tr>
		<tr class="enable">
			<th>갱신 주기</th>
			<td colspan="2">
				<span class="expire-interval-guide">각 적용 대상 페이지별로 개별 설정합니다.</span>
				<select class="expire-interval" name="expireInterval">
					<option value="5">5초</option>
					<option value="10">10초</option>
					<option value="30">30초</option>
					<option value="60">1분</option>
				</select>
				<img id="clear-cache" src="../img/btn_renew.gif"/>
			</td>
		</tr>
		<tr class="enable">
			<th rowspan="2">캐시 적용 대상 페이지</th>
			<td style="background-color: #f6f6f6; text-align: center; width: 325px;">온라인샵</td>
			<td style="background-color: #f6f6f6; text-align: center; width: 325px;">모바일샵</td>
		</tr>
		<tr class="enable">
			<td>
				<ul id="cache-target-page">
					<li>
						<span><span style="font-weight: bold; font-size: 16px;">ㆍ</span>메인페이지</span>
						<button class="clear-cache" data-page="main" class="clear-cache-page" type="button">수동갱신하기</button>
						<select class="page-expire-interval" name="expireInterval_pc_main_index">
							<option value="0">사용안함</option>
							<option value="5">5초</option>
							<option value="10">10초</option>
							<option value="30">30초</option>
							<option value="60">1분</option>
						</select>
					</li>
					<li>
						<span><span style="font-weight: bold; font-size: 16px;">ㆍ</span>카테고리 리스트</span>
						<button class="clear-cache" data-page="categoryList" class="clear-cache-page" type="button">수동갱신하기</button>
						<select class="page-expire-interval" name="expireInterval_pc_goods_goods_list">
							<option value="0">사용안함</option>
							<option value="5">5초</option>
							<option value="10">10초</option>
							<option value="30">30초</option>
							<option value="60">1분</option>
						</select>
					</li>
					<li>
						<span><span style="font-weight: bold; font-size: 16px;">ㆍ</span>게시판 리스트</span>
						<button class="clear-cache" data-page="boardList" class="clear-cache-page" type="button">수동갱신하기</button>
						<select class="page-expire-interval" name="expireInterval_pc_board_list">
							<option value="0">사용안함</option>
							<option value="5">5초</option>
							<option value="10">10초</option>
							<option value="30">30초</option>
							<option value="60">1분</option>
						</select>
					</li>
					<li>
						<span><span style="font-weight: bold; font-size: 16px;">ㆍ</span>상품문의/후기 리스트<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(상품상세페이지 내)</span>
						<button class="clear-cache" data-page="goodsBoardList" class="clear-cache-page" type="button">수동갱신하기</button>
						<select class="page-expire-interval" name="expireInterval_pc_goods_goods_review_and_qna">
							<option value="0">사용안함</option>
							<option value="5">5초</option>
							<option value="10">10초</option>
							<option value="30">30초</option>
							<option value="60">1분</option>
						</select>
					</li>
				</ul>
			</td>
			<td style="vertical-align: top;">
				<ul id="cache-target-mobile-page">
					<li>
						<span><span style="font-weight: bold; font-size: 16px;">ㆍ</span>메인페이지</span>
						<button class="clear-cache" data-page="mobileMain" class="clear-cache-page" type="button">수동갱신하기</button>
						<select class="page-expire-interval" name="expireInterval_mobile_main_index">
							<option value="0">사용안함</option>
							<option value="5">5초</option>
							<option value="10">10초</option>
							<option value="30">30초</option>
							<option value="60">1분</option>
						</select>
					</li>
					<li>
						<span><span style="font-weight: bold; font-size: 16px;">ㆍ</span>게시판 리스트</span>
						<button class="clear-cache" data-page="mobileBoardList" class="clear-cache-page" type="button">수동갱신하기</button>
						<select class="page-expire-interval" name="expireInterval_mobile_board_list">
							<option value="0">사용안함</option>
							<option value="5">5초</option>
							<option value="10">10초</option>
							<option value="30">30초</option>
							<option value="60">1분</option>
						</select>
					</li>
				</ul>
			</td>
		</tr>
	</table>

	<?php if ($templateCache->checkSkinPatch() === false) { ?>
	<div style="margin: 15px 0 5px 0;">
		<span style="color: #627dce; width: 800px;">
			<span style="font-weight: bold;">※ 관련 스킨 패치 적용 필요</span>
			-&gt;
		</span>
		<span style="color: #ff0000; width: 800px; font-weight: bold;">
			스킨 패치를 적용하지 않을 경우 고객에게 <span style="text-decoration: underline;">잘못된 정보가 표시</span>될 수 있습니다.
		</span>
		<a href="http://www.godo.co.kr/customer_center/patch.php?sno=2114" target="_blank" style="font-weight: bold;">
			<img src="../img/btn_detail.gif"/>
		</a>
	</div>
	(이 메시지는 '캐시 적용 대상 페이지'에 명시된 페이지 중 한 페이지라도 패치가 적용되어 있지 않은 경우 표시됩니다.
	만약, 위 주의사항을 확인하였고 스킨 패치를 하지 않더라도 쇼핑몰 운영에 문제가 없다고 판단하시면 이 메시지를 무시하셔도 됩니다.)
	<?php } ?>

	<div style="width: 800px; padding: 20px; text-align: center;">
		<input type="image" src="../img/btn_save.gif">
	</div>
</form>
<?php

include '../_footer.php';

?>