<?php

// 등급변경알림 레이어
if (in_array('myLevelLayer', $schedule)) {
	$returnData['myLevelLayer'] = array();
	list($name) = $db->fetch('SELECT name FROM '.GD_MEMBER.' WHERE m_no='.$sess['m_no']);
	list($currentLevel, $previousLevel, $currentGroupName, $previousGroupName) = $db->fetch('SELECT log.current_level, log.previous_level, grp1.grpnm, grp2.grpnm'
		. ' FROM (SELECT _log.* FROM '.GD_MEMBER_GRP_CHANGED_LOG.' AS _log WHERE _log.m_no='.$sess['m_no'].' AND _log.current_level != _log.previous_level) AS log'
		. ' LEFT JOIN '.GD_MEMBER_GRP.' AS grp1 ON log.current_level = grp1.level'
		. ' LEFT JOIN '.GD_MEMBER_GRP.' AS grp2 ON log.previous_level = grp2.level');
	if ($previousGroupName && $currentGroupName && !$_COOKIE['cache_lnoti.'.$previousLevel.'.'.$currentLevel]) {
		$returnData['myLevelLayer']['name'] = iconv('EUC-KR', 'UTF-8', $name);
		$returnData['myLevelLayer']['previousLevel'] = $previousLevel;
		$returnData['myLevelLayer']['currentLevel'] = $currentLevel;
		$returnData['myLevelLayer']['previousGroupName'] = iconv('EUC-KR', 'UTF-8', $previousGroupName);
		$returnData['myLevelLayer']['currentGroupName'] = iconv('EUC-KR', 'UTF-8', $currentGroupName);
	}
}