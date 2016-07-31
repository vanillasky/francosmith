<?php

/* Return Member Data Function */

/*-------------------------------------------------------------------
{ = this->assign( 'm_info', dataMember() ) // 회원정보 }
{=m_info.emoney }
-------------------------------------------------------------------*/

function dataMember(){

	global $db, $sess;

	$member = array();
	$member = $db->fetch("select * from ".GD_MEMBER." where m_no='$sess[m_no]'", 1);

	return $member;
}
?>
