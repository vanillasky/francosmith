<?
header ("Cache-Control: no-cache, must-revalidate");
header ("Pragma: no-cache");

include "../lib/library.php";

if ($sess['m_id']) {

	// 회원 정보
	$mb = $db->fetch("SELECT mobile,email  FROM ".GD_MEMBER." WHERE m_id = '".$sess['m_id']."'",1);

	// 구독 신청 정보가 있는가?
	if (($subscribe = $db->fetch("SELECT sno FROM ".GD_TODAYSHOP_SUBSCRIBE." WHERE m_id = '".$sess['m_id']."'",1)) != false) {
		// update..
		$query = "
		UPDATE ".GD_TODAYSHOP_SUBSCRIBE." SET
			category = '".$_POST['interest_category']."'
		WHERE m_id = '".$sess['m_id']."'
		";
	}
	else {
		// insert..
		$query = "
		INSERT INTO ".GD_TODAYSHOP_SUBSCRIBE." SET
			m_id = '".$sess['m_id']."',
			category = '".$_POST['interest_category']."'
		";
	}

	$db->query($query);

	msg('관심분류 선택이 성공적으로 처리되었습니다.');
	$_SERVER['HTTP_REFERER'] = str_replace('interest=1','',$_SERVER[HTTP_REFERER]);
	go($_SERVER[HTTP_REFERER]);
}
else {
	go($_SERVER[HTTP_REFERER]);
}
?>