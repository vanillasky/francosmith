<?php
@require "../../lib/library.php";

// �����̼� ��ǰ DB ���� ���̺� ���� �� ���� ������ ���ο���
$db->query("CREATE TABLE IF NOT EXISTS gd_todayshop_goods_merged LIKE ".GD_TODAYSHOP_GOODS);
$db->query("TRUNCATE TABLE ".GD_TODAYSHOP_GOODS_MERGED);

$query = "SHOW COLUMNS FROM ".GD_GOODS;
$rs = $db->query($query);

$gd_goods = array();

while ($row = mysql_fetch_assoc($rs)) {

	if (! ($chk = $db->fetch("SHOW COLUMNS FROM gd_todayshop_goods_merged WHERE Field = '".$row['Field']."'",1))) {

		$query  = "ALTER TABLE `gd_todayshop_goods_merged` ADD `".$row['Field']."` ".$row['Type']."";
		$query .= ($row['Null'] == 'NO') ? ' NOT NULL ' : ' NULL ';
		$query .= (!is_null($row['Default'])) ? ' DEFAULT \''.$row['Default'].'\'' : '';
		$query .= ' AFTER '.$preField;

		$preField = $row['Field'];

		$db->query($query);
	}
	else {
		$preField = $chk['Field'];
	}

	$gd_goods[] = $row['Field'];
}


$query = "SHOW COLUMNS FROM ".GD_TODAYSHOP_GOODS;
$rs = $db->query($query);

$gd_todayshop_goods = array();

while ($row = mysql_fetch_assoc($rs)) {
	$gd_todayshop_goods[] = $row['Field'];
}

// ��ġ�� �迭 ����
foreach($gd_todayshop_goods as $k => $v) if (in_array($v, $gd_goods)) unset($gd_todayshop_goods[$k]);

// ��
$gd_todayshop_goods_merged = array_merge($gd_goods,$gd_todayshop_goods);

// alias �߰�.
foreach($gd_todayshop_goods as $k => $v) $gd_todayshop_goods[$k] = 'TG.'.$v;
foreach($gd_goods as $k => $v) $gd_goods[$k] = 'G.'.$v;

$query = "
	INSERT gd_todayshop_goods_merged (".(implode(",",$gd_todayshop_goods_merged)).")
	SELECT
		".(implode(",",$gd_goods))." ,
		".(implode(",",$gd_todayshop_goods))."
	FROM ".GD_GOODS." AS G
	INNER JOIN ".GD_TODAYSHOP_GOODS." AS TG
	ON G.goodsno = TG.goodsno
";
$db->query($query);
?>
���� �Ϸ�.