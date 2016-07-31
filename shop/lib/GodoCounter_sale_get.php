<?
//==============================================================================//
//		PROGRAM Title : Godo-Shoppingmall Counter
//		Company Name  : (주) 플라이폭스 - 고도몰
//
//		Version       : 2.0 Version (Get)
//		Create Date   : 2006.06.01
//		Update Date   : ----,--,--
//		Programer     : 박선희(신동규)
//		Copyright (C)2004 flyfox.co.kr , All rights reserved.
//==============================================================================//





class Counter_Sale_Get
{
	var $week_text = array( '0' => '일', '1' => '월', '2' => '화', '3' => '수', '4' => '목', '5' => '금', '6' => '토' );
	var $putyn = 'n';

	function Counter_Sale_Get()
	{
		global $db;

		$query = "
		create temporary table ".GD_ORDER."_sale(
			ordno  bigint(20) unsigned NOT NULL default '0' primary key,
			orddt date default NULL,
			settlekind char(1) NOT NULL,
			settleprice int(11) NOT NULL default '0',
			cancelprice int(11) NOT NULL default '0',
			cyn enum('y','n','r') NOT NULL default 'n'
		)
		";
		$db->query($query); // 임시 IP TABLE 생성
	}

	function multi_array_sum( $arr, $key )
	{
		$tmp = array();
		foreach ( $arr as $row ) $tmp[] = $row[ $key ];
		return array_sum( $tmp );
	}

	function put_order_sale( $Dates )
	{
		global $db;

		$res = $db->query( "SELECT ordno, date_format(orddt,'%Y-%m-%d') as orddt, settlekind, settleprice, goodsprice, delivery, coupon, emoney, memberdc, enuri, cyn from ".GD_ORDER." where date_format(orddt,'%Y%m%d') >= '" . $Dates[0] . "' AND date_format(orddt,'%Y%m%d') <= '" . $Dates[1] . "'" );
		while ( $row = $db->fetch( $res, "MYSQL_ASSOC" ) ){

			$sub = $db->query( "select price, ea, memberdc, istep from ".GD_ORDER_ITEM." where ordno='{$row['ordno']}' order by sno" );

			$goodsprice = $memberdc = 0;
			while ($item=$db->fetch($sub)){
				if ($item[istep]<40) $goodsprice += $item[price] * $item[ea];
				if ($item[istep]<10) $memberdc += $item[memberdc];
			}

			### 할인액 계산
			$discount = $memberdc + $row[emoney] + $row[coupon] + $row[enuri];

			### 실데이타 계산으로 결제금액 산출
			$settleprice = $goodsprice + $row[delivery] - $discount;

			$cancelprice = 0;
			if ( $row['cyn'] == 'y' ) $cancelprice = $row['settleprice'] - $settleprice;
			else if ( $row['cyn'] == 'r' ) $cancelprice = $row['settleprice'];

			$db->query( "INSERT INTO ".GD_ORDER."_sale VALUES ('{$row['ordno']}', '{$row['orddt']}', '{$row['settlekind']}', '{$row['settleprice']}', '{$cancelprice}', '{$row['cyn']}')" );
		}

		$this->putyn = 'y';
	}

	function get_saleday( $Dates )
	{
		global $db;
		$recode = array();

		if ( $this->putyn != 'y' ) $this->put_order_sale( $Dates );

		$res = $db->query( "select orddt,
				count(ordno) as totcnt,
				count(case when cyn='n' then 1 else null end) as n_cnt,
				count(case when cyn='y' then 1 else null end) as y_cnt,
				count(case when cyn='r' then 1 else null end) as r_cnt,
				sum(settleprice) as totprice,
				sum(case when cyn='n' then settleprice else 0 end) as n_price,
				sum(case when cyn='y' then settleprice else 0 end) as y_price,
				case when cyn='r' then settleprice else sum(case when cancelprice > 0 then cancelprice else 0 end) end  as r_price
				from ".GD_ORDER."_sale
				group by orddt order by orddt" );

		while ( $row = $db->fetch( $res, "MYSQL_ASSOC" ) ){
			$unix = mktime( 0, 0, 0, substr( $row['orddt'], 5, 2 ), substr( $row['orddt'], 8, 2 ), substr( $row['orddt'], 0, 4 ) );
			$row['etctext'] = ' (' . $this->week_text[ date( 'w', $unix ) ] . ')';
			$recode[] = $row;
		}

		return $recode;
	}

	function get_salekind( $Dates )
	{
		global $db;
		$recode = array();

		if ( $this->putyn != 'y' ) $this->put_order_sale( $Dates );

		$res = $db->query( "select settlekind,
				count(ordno) as totcnt,
				count(case when cyn='n' then 1 else null end) as n_cnt,
				count(case when cyn='y' then 1 else null end) as y_cnt,
				count(case when cyn='r' then 1 else null end) as r_cnt,
				sum(settleprice) as totprice,
				sum(case when cyn='n' then settleprice else 0 end) as n_price,
				sum(case when cyn='y' then settleprice else 0 end) as y_price,
				case when cyn='r' then settleprice else sum(case when cancelprice > 0 then cancelprice else 0 end) end  as r_price
				from ".GD_ORDER."_sale
				group by settlekind order by settlekind" );

		while ( $row = $db->fetch( $res, "MYSQL_ASSOC" ) ) $recode[] = $row;

		return $recode;
	}
}

$counter_sale_get = new Counter_Sale_Get();