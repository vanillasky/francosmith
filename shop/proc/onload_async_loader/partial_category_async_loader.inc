<?php

// ī�װ��� ����Ʈ
if (in_array('category', $schedule)) {
	$tpl->include_('dataCategory');
	$returnData['category'] = array();
	foreach (dataCategory($cfg['subCategory'], 1) as $category) {
		$category['catnm'] = iconv('EUC-KR', 'UTF-8', $category['catnm']);
		if ($category['sub']) {
			foreach ($category['sub'] as $index => $subCategory) {
				$category['sub'][$index]['catnm'] = iconv('EUC-KR', 'UTF-8', $subCategory['catnm']);
			}
		}
		$returnData['category'][] = $category;
	}
}