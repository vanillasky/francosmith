<?php
	include dirname(__FILE__) . "/../_shopTouch_header.php";
	@include $shopRootDir . "/lib/page.class.php";
	
	if (!$sess && !$_COOKIE[guest_ordno]) msg("로그인 하셔야 본 서비스를 이용하실 수 있습니다.", "../shopTouch_mem/login.php?returnUrl=$_SERVER[PHP_SELF]");

	$db_table = "".GD_ORDER."";
	if ($sess[m_no]) $where[] = "m_no = '$sess[m_no]'";
	else {
		$where[] = "ordno = '$_COOKIE[guest_ordno]'";
		$where[] = "nameOrder = '$_COOKIE[guest_nameOrder]'";
		$where[] = "m_no = ''";
	}

	$pg = new Page($_GET[page],10);
	$pg->setQuery($db_table,$where,"ordno desc");
	$pg->exec();

	$res = $db->query($pg->query);
	$idx = 0;
	while ($data=$db->fetch($res)){
		$idx ++;
		$data[str_step] = (!$data[step2]) ? $r_step[$data[step]] : $r_step2[$data[step2]];
		$data[str_settlekind] = $r_settlekind[$data[settlekind]];
		if($data[prn_settleprice]) $data[settleprice] = $data[prn_settleprice];
		$data[idx] = $idx;
		$loop[] = $data;
	}

	setcookie("v_guest_ordno", $_COOKIE[guest_ordno], 0, '/');
	setcookie("v_guest_nameOrder", $_COOKIE[guest_nameOrder], 0, '/');

	setcookie("guest_ordno",'',0,'/');
	setcookie("guest_nameOrder",'',0,'/');

	$tpl->assign('loop',$loop);
	$tpl->assign('pg',$pg);
	$tpl->print_('tpl');
?>