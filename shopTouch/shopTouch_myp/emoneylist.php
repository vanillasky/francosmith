<?php
	include dirname(__FILE__) . "/../_shopTouch_header.php";
	@include $shopRootDir . "/lib/page.class.php";

	chkMemberShopTouch();

	list ($name, $emoney) = $db->fetch("select name, emoney from ".GD_MEMBER." where m_no='$sess[m_no]'"); # ���� ������

	list ($total) = $db->fetch("select count(*) from ".GD_LOG_EMONEY." where m_no='$sess[m_no]'"); # �� ���ڵ��

	### ���
	$pg = new Page($_GET[page],10);
	$db_table = "".GD_LOG_EMONEY."";
	$pg->field = "*, date_format( regdt, '%Y.%m.%d' ) as regdts"; # �ʵ� ����
	$where[] = "m_no='$sess[m_no]'";
	$pg->setQuery($db_table,$where,$orderby="regdt desc");
	$pg->exec();

	$res = $db->query($pg->query);
	while ($data=$db->fetch($res)){
		$data['idx'] = $pg->idx--;
		$loop[] = $data;
	}

	$tpl->assign('loop',$loop);
	$tpl->assign('pg',$pg);
	$tpl->print_('tpl');
?>