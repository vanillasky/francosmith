<?php

$location = '기타관리 > 웹사이트 속도 향상 설정';
include '../_header.php';

$dbCache = Core::loader('dbcache');
$dbCacheConfig = $dbCache->loadConfig();

?>
<script type="text/javascript">
var IntervarId;
jQuery(document).ready(function(){
	jQuery("#cache-use-type-none").click(function(){
		jQuery("#db-cache-form").addClass("none").removeClass("default");
	});
	jQuery("#cache-use-type-default").click(function(){
		jQuery("#db-cache-form").removeClass("none").addClass("default");
	});

	jQuery("#cache-use-type-<?php echo $dbCacheConfig['cacheUseType']; ?>").click();
	jQuery("#clear-cache").click(function(){
		ifrmHidden.location.href = "./adm_etc_cache_db.indb.php?mode=clearCache";
	});

});
</script>
<style type="text/css">
	#cache-target-db, #cache-target-mobile-db {
		padding-left: 0;
	}
	#cache-target-db li, #cache-target-mobile-db li {
		overflow: hidden;
		margin: 10px 0;
	}
	#cache-target-db li span, #cache-target-mobile-db li span {
		float: left;
	}
	#cache-target-db li select.page-expire-interval, #cache-target-mobile-db li select.page-expire-interval, button.clear-cache {
		float: right;
	}
	#db-cache-form.none tr.enable {
		display: none;
	}
	#db-cache-form.default .expire-interval-guide, #db-cache-form.default button.clear-cache, #db-cache-form.default .page-expire-interval {
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
	웹사이트 속도 향상 설정
	<a href="javascript:manual('<?php echo $guideUrl; ?>board/view.php?id=basic&no=44')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a>
</div>

<div style="border: solid #000 1px; padding: 15px; margin-top: 10px; padding-right:10px;">
	<div style="color:#666666;">
	웹사이트 속도 향상 설정은 주요 웹페이지에 ‘캐시(cache)’기능을 적용하여 방문객이 해당 페이지 접속 시 미리 저장된 조회 결과 내용을 표시함으로써,
	서버의 과부하를 예방할 수 있고 조회속도도 향상시켜 쾌적한 쇼핑환경을 만들 수 있는 기능입니다.
	</div>
	<div style="padding-top:7px; color:#ff0000;">
	* 주의 : 캐시 적용 시 저장된 내용을 표시하기 때문에 처리속도는 빨라지지만 갱신주기만큼 쇼핑몰 화면에 반영되는 시간 차이가 발생할 수 있습니다.
	단, 상품의 품절 등 주요 항목에 대해서는 캐시 갱신 주기와 관계없이 변경된 내용이 즉시 반영됩니다.
	<a href="javascript:manual('<?php echo $guideUrl; ?>board/view.php?id=basic&no=44')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a>
	</div>
</div>
<div style="padding-top:10px;"></div>

<form id="db-cache-form" class="admin-form" method="post" target="ifrmHidden" action="adm_etc_cache_db.indb.php">
	<input type="hidden" name="mode" value="save"/>
	<table class="admin-form-table" style="width: 700px;">
		<tr>
			<th style="width: 150px;">사용여부</th>
			<td colspan="2">
				<input id="cache-use-type-none" type="radio" name="cacheUseType" value="none"/>
				<label for="cache-use-type-none">사용안함</label>
				<input id="cache-use-type-default" type="radio" name="cacheUseType" value="default"/>
				<label for="cache-use-type-default">사용함</label>
			</td>
		</tr>
		<tr class="enable">
			<th>갱신 주기</th>
			<td colspan="2">
				<b>30초</b>마다 자동 갱신됩니다.
				<img id="clear-cache" src="../img/btn_renew.gif"/>
			</td>
		</tr>
		<tr class="enable">
			<th rowspan="2">캐시 적용 대상 페이지</th>
			<td style="background-color: #f6f6f6; text-align:center; width:300px;">온라인샵</td>
			<td style="background-color: #f6f6f6; text-align:center; width:300px;">모바일샵</td>
		</tr>
		<tr class="enable">
			<td>
				<ul id="cache-target-db">
					<li>ㆍ메인상품진열</li>
					<li>ㆍ카테고리 상품 리스트</li>
					<li>ㆍ관련상품/상품문의/상품후기 리스트<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(상품상세페이지 내)</li>
				</ul>
			</td>
			<td>
				<ul id="cache-target-mobile-db">
					<li>ㆍ메인상품진열</li>
					<li>ㆍ카테고리 상품 리스트</li>
					<li>ㆍ상품문의/상품후기 리스트<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(상품상세페이지 내)</li>
				</ul>
			</td>
		</tr>
	</table>

	<div style="width: 700px; padding: 20px; text-align: center;">
		<input type="image" src="../img/btn_save.gif">
	</div>
</form>
</div>

<ul class="admin-simple-faq" style="margin-top: 15px;">
	<li style="margin-top: 5px; list-style: none; background: none;">
		<div style="font-weight: bold;">캐시란?</div>
		<div>
			: 자주 사용하는 데이터나 결과값을 미리 저장한 뒤 동일한 내용을 요청할 때 저장된 내용을 표시함으로써 처리 속도를 향상시킬 수 있는 기능입니다.
		</div>
		<div style="margin-top: 10px;">수동갱신하기 : 클릭 시 현재 상품데이터를 기준으로 캐시에 반영합니다.</div>
	</li>
</ul>

<?php

include '../_footer.php';

?>