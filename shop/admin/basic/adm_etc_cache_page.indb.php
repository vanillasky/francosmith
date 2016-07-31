<?php

include '../lib.php';

$templateCache = Core::loader('TemplateCache');
$qfile = Core::loader('qfile');

switch ($_REQUEST['mode']) {
	case 'clearCache':
		if ($_GET['page'] === 'main') {
			$templateCache->clearCache('{:PC:}/main/index.php');
		}
		else if ($_GET['page'] === 'categoryList') {
			$templateCache->clearCache('{:PC:}/goods/goods_list.php');
		}
		else if ($_GET['page'] === 'boardList') {
			$templateCache->clearCache('{:PC:}/board/list.php');
		}
		else if ($_GET['page'] === 'goodsBoardList') {
			$templateCache->clearCache('{:PC:}/goods/goods_qna_list.php');
			$templateCache->clearCache('{:PC:}/goods/goods_review_list.php');
		}
		else if ($_GET['page'] === 'mobileMain') {
			$templateCache->clearCache('{:MOBILE:}/index.php');
		}
		else if ($_GET['page'] === 'mobileBoardList') {
			$templateCache->clearCache('{:MOBILE:}/board/list.php');
		}
		else {
			$templateCache->clearCache();
		}
		
		msg('캐시가 갱신되었습니다.');
		break;
	case 'save':
		$configPath = dirname(__FILE__).'/../../conf/cache.page.cfg.php';

		$qfile->open($configPath);
		$qfile->write('<?php'.PHP_EOL);
		$qfile->write('$cacheConfig = array();'.PHP_EOL);
		$qfile->write('$cacheConfig["page"] = array();'.PHP_EOL);
		$qfile->write('$cacheConfig["page"]["cacheUseType"] = "'.$_POST['cacheUseType'].'";'.PHP_EOL);
		$qfile->write('$cacheConfig["page"]["expireInterval"] = '.$_POST['expireInterval'].';'.PHP_EOL);
		$qfile->write('$cacheConfig["page"]["pageExpireInterval"] = array();'.PHP_EOL);
		$qfile->write('$cacheConfig["page"]["pageExpireInterval"]["{:PC:}/main/index.php"] = '.$_POST['expireInterval_pc_main_index'].';'.PHP_EOL);
		$qfile->write('$cacheConfig["page"]["pageExpireInterval"]["{:PC:}/goods/goods_list.php"] = '.$_POST['expireInterval_pc_goods_goods_list'].';'.PHP_EOL);
		$qfile->write('$cacheConfig["page"]["pageExpireInterval"]["{:PC:}/board/list.php"] = '.$_POST['expireInterval_pc_board_list'].';'.PHP_EOL);
		$qfile->write('$cacheConfig["page"]["pageExpireInterval"]["{:PC:}/goods/goods_qna_list.php"] = '.$_POST['expireInterval_pc_goods_goods_review_and_qna'].';'.PHP_EOL);
		$qfile->write('$cacheConfig["page"]["pageExpireInterval"]["{:PC:}/goods/goods_review_list.php"] = '.$_POST['expireInterval_pc_goods_goods_review_and_qna'].';'.PHP_EOL);
		$qfile->write('$cacheConfig["page"]["pageExpireInterval"]["{:MOBILE:}/index.php"] = '.$_POST['expireInterval_mobile_main_index'].';'.PHP_EOL);
		$qfile->write('$cacheConfig["page"]["pageExpireInterval"]["{:MOBILE:}/board/list.php"] = '.$_POST['expireInterval_mobile_board_list'].';'.PHP_EOL);
		$qfile->write('?>');
		$qfile->close();
		chmod($configPath, 0707);

		$templateCache->clearCache();

		msg('정상적으로 저장되었습니다.');
		break;
}