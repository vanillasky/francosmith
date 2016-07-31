<?php

include dirname(__FILE__).'/../lib.php';

if (!$_POST['mode']) exit;

$goodsSort = Core::loader('GoodsSort');

switch ($_POST['mode']) {
	case 'fetchGoodsListByCategory':
		if (!$_POST['category'] || !$_POST['page'] || !$_POST['limit']) exit;
		$category = $_POST['category'];
		$page = (int)$_POST['page'];
		$limit = (int)$_POST['limit'];

		$thisPage = $goodsSort->fetchGoodsListByCategory($category, $page, $limit);

		exit(gd_json_encode($thisPage));

	case 'applyModified':
		if (!$_POST['category'] || (!$_POST['sortSet'] && !$_POST['openSet'])) exit;
		$category = $_POST['category'];
		$sortSet = $_POST['sortSet'];
		$openSet = $_POST['openSet'];

		$result = array();
		$result['result'] = $goodsSort->applyModified($category, $sortSet, $openSet);
		exit(gd_json_encode($result));

	case 'changeCategorySortType':
		if (!$_POST['category'] || !$_POST['sortType']) exit;
		$category = $_POST['category'];
		$sortType = $_POST['sortType'];

		$result = array();
		$result['result'] = $goodsSort->changeCategorySortType($category, $sortType);
		exit(gd_json_encode($result));

	case 'changeManualSortOnLinkGoodsPosition':
		if (!$_POST['category'] || !$_POST['manualSortOnLinkGoodsPosition']) exit;
		$category = $_POST['category'];
		$manualSortOnLinkGoodsPosition = $_POST['manualSortOnLinkGoodsPosition'];

		$result = array();
		$result['result'] = $goodsSort->changeManualSortOnLinkGoodsPosition($category, $manualSortOnLinkGoodsPosition);
		exit(gd_json_encode($result));

	case 'selectionMovePage':
		if (!$_POST['category'] || !$_POST['currentPage'] || !$_POST['targetPage'] || !$_POST['limit'] || !$_POST['selectedSortSet'] || !$_POST['position']) exit;
		$category = $_POST['category'];
		$currentPage = $_POST['currentPage'];
		$targetPage = $_POST['targetPage'];
		$limit = $_POST['limit'];
		$selectedSortSet = $_POST['selectedSortSet'];
		$position = $_POST['position'];

		$result = array();
		$result['result'] = $goodsSort->movePageSelection($category, $selectedSortSet, $currentPage, $targetPage, $limit, $position);
		exit(gd_json_encode($result));

	case 'optimizeManualSort':
		if (!$_POST['category']) exit;
		$category = $_POST['category'];

		$result = array();
		$result['result'] = $goodsSort->optimizeManualSort($category);
		exit(gd_json_encode($result));

	case 'saveConfig':
		if (!$_POST['viewType'] && !$_POST['imageSize'] && !$_POST['limitRows']) exit;
		$viewType = $_POST['viewType'];
		$imageSize = $_POST['imageSize'];
		$limitRows = $_POST['limitRows'];

		if (strlen($viewType) > 0) $goodsSort->saveConfig('viewType', $viewType);
		if (strlen($imageSize) > 0) $goodsSort->saveConfig('imageSize', $imageSize);
		if (strlen($limitRows) > 0) $goodsSort->saveConfig('limitRows', $limitRows);

		$result = array();
		$result['result'] = true;
		exit(gd_json_encode($result));
}

?>
