<?

// C1. 라이브러리 로드
include "lib/library.php";

// C2. 실행쿼리
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
$query[] = "ALTER TABLE `gd_mobile_design` add column `sort_type` int(10) NOT NULL DEFAULT '1' COMMENT '진열 상품선정 기준' AFTER `disp_cnt`;";
$query[] = "ALTER TABLE `gd_mobile_design` add column `select_date` int(10) DEFAULT NULL COMMENT '진열 상품선정 기간' AFTER `sort_type`;";
$query[] = "ALTER TABLE `gd_mobile_design` add column `mobile_categoods` varchar(255) DEFAULT NULL COMMENT '진열 대상 카테고리' AFTER `select_date`;";
$query[] = "ALTER TABLE `gd_mobile_design` add column `price` varchar(30) DEFAULT NULL COMMENT '상품가격' AFTER `mobile_categoods`;";
$query[] = "ALTER TABLE `gd_mobile_design` add column `stock_type` varchar(10) DEFAULT NULL COMMENT '상품재고 타입' AFTER `price`;";
$query[] = "ALTER TABLE `gd_mobile_design` add column `stock_amount` varchar(30) DEFAULT NULL COMMENT '상품재고수량' AFTER `stock_type`;";
$query[] = "ALTER TABLE `gd_mobile_design` add column `regdt` int(10) DEFAULT NULL COMMENT '상품등록일' AFTER `stock_amount`;";

// C3. 에러발생여부
$occursError = false;

// C4. 쿼리 실행
if (strtoupper(get_class($db)) === 'GODO_DB') { // GODO DB객체일때(시즌4 이상)
	foreach ($query as $v) {
		$db->query($v);
		if ($db->errorCode()) {
			debug($db->errorInfo());
			$occursError = true;
		}
	}
}
else if (strtoupper(get_class($db)) === 'DB') { // DB객체일때(시즌1,2,3)
	foreach ($query as $v) {
		$db->query($v);
		if (mysql_errno($db->db_conn)) {
			debug(mysql_error($db->db_conn));
			$occursError = true;
		}
	}
}
else { // 지정된 DB객체가 아닌경우
	debug('DB객체를 찾을 수 없습니다. 고객센터로 문의주시기 바랍니다.');
	$occursError = true;
}

// C5. 에러가 발생하지 않았다면 패치성공여부 출력
if ($occursError === false) debug('정상적으로 DB패치가 완료되었습니다.');

?>