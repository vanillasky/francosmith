<?php

// ȸ�� �α��λ���
if (in_array('userStatus', $schedule)) {
	if ($sess) {
		$returnData['userStatus'] = true;
	}
	else {
		$returnData['userStatus'] = false;
	}
}