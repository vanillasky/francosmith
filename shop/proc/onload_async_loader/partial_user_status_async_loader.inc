<?php

// 회원 로그인상태
if (in_array('userStatus', $schedule)) {
	if ($sess) {
		$returnData['userStatus'] = true;
	}
	else {
		$returnData['userStatus'] = false;
	}
}