<?

// C1. ���̺귯�� �ε�
include "lib/library.php";

// C2. ��������
$query[] = "
CREATE TABLE `gd_auto_main_display_odd` (
  `goodsno` int(10) NOT NULL,
  `sort2_7` int(11) NOT NULL DEFAULT '0',
  `sort2_15` int(11) NOT NULL DEFAULT '0',
  `sort3_7` int(11) NOT NULL DEFAULT '0',
  `sort3_15` int(11) NOT NULL DEFAULT '0',
  `sort4_7` int(11) NOT NULL DEFAULT '0',
  `sort4_15` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY  (`goodsno`)
) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$query[] = "
CREATE TABLE `gd_auto_main_display_even` (
  `goodsno` int(10) NOT NULL,
  `sort2_7` int(11) NOT NULL DEFAULT '0',
  `sort2_15` int(11) NOT NULL DEFAULT '0',
  `sort3_7` int(11) NOT NULL DEFAULT '0',
  `sort3_15` int(11) NOT NULL DEFAULT '0',
  `sort4_7` int(11) NOT NULL DEFAULT '0',
  `sort4_15` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY  (`goodsno`)
) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$query[] = "ALTER TABLE `gd_mobile_design` add column `sort_type` int(10) NOT NULL DEFAULT '1' COMMENT '���� ��ǰ���� ����' AFTER `disp_cnt`;";
$query[] = "ALTER TABLE `gd_mobile_design` add column `select_date` int(10) DEFAULT NULL COMMENT '���� ��ǰ���� �Ⱓ' AFTER `sort_type`;";
$query[] = "ALTER TABLE `gd_mobile_design` add column `mobile_categoods` varchar(255) DEFAULT NULL COMMENT '���� ��� ī�װ�' AFTER `select_date`;";
$query[] = "ALTER TABLE `gd_mobile_design` add column `price` varchar(30) DEFAULT NULL COMMENT '��ǰ����' AFTER `mobile_categoods`;";
$query[] = "ALTER TABLE `gd_mobile_design` add column `stock_type` varchar(10) DEFAULT NULL COMMENT '��ǰ��� Ÿ��' AFTER `price`;";
$query[] = "ALTER TABLE `gd_mobile_design` add column `stock_amount` varchar(30) DEFAULT NULL COMMENT '��ǰ������' AFTER `stock_type`;";
$query[] = "ALTER TABLE `gd_mobile_design` add column `regdt` int(10) DEFAULT NULL COMMENT '��ǰ�����' AFTER `stock_amount`;";

// C3. �����߻�����
$occursError = false;

// C4. ���� ����
if (strtoupper(get_class($db)) === 'GODO_DB') { // GODO DB��ü�϶�(����4 �̻�)
	foreach ($query as $v) {
		$db->query($v);
		if ($db->errorCode()) {
			debug($db->errorInfo());
			$occursError = true;
		}
	}
}
else if (strtoupper(get_class($db)) === 'DB') { // DB��ü�϶�(����1,2,3)
	foreach ($query as $v) {
		$db->query($v);
		if (mysql_errno($db->db_conn)) {
			debug(mysql_error($db->db_conn));
			$occursError = true;
		}
	}
}
else { // ������ DB��ü�� �ƴѰ��
	debug('DB��ü�� ã�� �� �����ϴ�. �����ͷ� �����ֽñ� �ٶ��ϴ�.');
	$occursError = true;
}

// C5. ������ �߻����� �ʾҴٸ� ��ġ�������� ���
if ($occursError === false) debug('���������� DB��ġ�� �Ϸ�Ǿ����ϴ�.');

?>