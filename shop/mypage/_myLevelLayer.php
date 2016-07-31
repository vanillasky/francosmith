<?
global $db, $sess;

if ($sess[m_no]){

	### 등급 수정 정보 가져오기
	$query = "
		select
				a.name,
				b.*,
				grp1.grpnm as current_grpnm,
				grp2.grpnm as previous_grpnm

		from ".GD_MEMBER." a

		left join ".GD_MEMBER_GRP_CHANGED_LOG." b on a.m_no=b.m_no

		left join ".GD_MEMBER_GRP." grp1 on b.current_level = grp1.level

		left join ".GD_MEMBER_GRP." grp2 on b.previous_level = grp2.level

		where a.m_no='$sess[m_no]'
	";
	$tmp = $db->fetch($query,1);

	if ($tmp) {
		$db->query("update ".GD_MEMBER_GRP_CHANGED_LOG." set notified = 1 where m_no = '$sess[m_no]'");
	}

}

$tpl = &$this;
$tpl->define('tpl', 'mypage/_myLevelLayer.htm');
$tpl->assign('mygroupinfo', $tmp);
$tpl->print_('tpl');
?>