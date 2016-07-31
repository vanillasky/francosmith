<?php

include dirname(__FILE__).'/../lib/library.php';
include dirname(__FILE__).'/../Template_/Template_.class.php';
include dirname(__FILE__).'/../conf/design_basic_'.$cfg['tplSkin'].'.php';
include dirname(__FILE__).'/../conf/config.mobileShop.php';

$tpl = new Template_;
$tpl->template_dir = dirname(__FILE__).'/../data/skin/'.$cfg['tplSkin'];
$tpl->compile_dir = dirname(__FILE__).'/../Template_/_compiles/'.$cfg['tplSkin'];
$tpl->prefilter = "adjustPath|include_file|capture_print|sitelinkConvert|systemHeadTag";
$schedule = explode(',', $_GET['schedule']);

$returnData = array();

$templateCache = Core::loader('TemplateCache', parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH));

// 기본제공정보
$returnData['cacheExpireInterval'] = $templateCache->getExpireInteval();
if ($sess) {
	$returnData['member'] = array();
	$returnData['member']['name'] = iconv('EUC-KR', 'UTF-8', $member['name']);
}

// 네이버공통 유입스크립트
include dirname(__FILE__).'/onload_async_loader/partial_naver_common_inflow_script_async_loader.inc';

// 메인 팝업창
include dirname(__FILE__).'/onload_async_loader/partial_popup_async_loader.inc';

// 회원 로그인상태에 따른 메뉴표시
include dirname(__FILE__).'/onload_async_loader/partial_user_status_async_loader.inc';

// 마이페이지 레이어박스
include dirname(__FILE__).'/onload_async_loader/partial_mybox_async_loader.inc';

// 쿠폰발급알림 레이어 박스
include dirname(__FILE__).'/onload_async_loader/partial_coupon_layer_async_loader.inc';

// 등급변경알림 레이어 박스
include dirname(__FILE__).'/onload_async_loader/partial_level_layer_async_loader.inc';

// 오늘본 상품
include dirname(__FILE__).'/onload_async_loader/partial_today_goods_async_loader.inc';

// 카테고리 리스트
include dirname(__FILE__).'/onload_async_loader/partial_category_async_loader.inc';

echo json_encode($returnData);