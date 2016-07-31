<?
$_meta_site_info['olcoupon'] = array('name'=>'올쿠폰','url'=>'http://www.olcoupon.com/');

// 올쿠폰 뷰
class social_meta_view_olcoupon extends social_meta_view {

	var $data = array();

	function social_meta_view_olcoupon() {
		$this->goods_only = 'A';
		$this->encode = 'UTF-8';
	}

	function make( $data = array() ) {

		$this->cfg = $this->encode($this->cfg);

		$_xml  = '';
		$_xml .= '<?xml version=\'1.0\' encoding=\'utf-8\'?>';
		$_xml .= '<items ver="1.0">';


		$keys = array_keys($data);
		for($i=0,$max=sizeof($keys);$i<$max;$i++) {
			$row = $data[ $keys[$i] ];

			$row['address'] = ($row['goodstype'] == 'coupon') ? $row['usable_spot_address'].' '.$row['usable_spot_address_ext'] : '배송';

			/*
				종료시간은
				2010-11-22 23:59:59 와 같이
				분:초 = 59:59 형태로 고정되어야 함
			*/
			$_tmp = strtotime($row['enddt']) - 3540;
			$row['enddt'] = date('Y-m-d H', $_tmp).':59:59';

			$row = $this->encode($row);

			$_xml .= '
			<item>
				<url><![CDATA['.$row['url'].']]></url>
				<title><![CDATA['.$row['goodsnm'].']]></title>
				<desc><![CDATA['.$row['shortdesc'].']]></desc>
				<address><![CDATA['.$row['address'].']]></address>
				<oprice>'.$row['consumer'].'</oprice>
				<dprice>'.$row['price'].'</dprice>
				<dcrate>'.$row['dc_rate'].'</dcrate>
				<maxcnt>'.$row['maxstock'].'</maxcnt>
				<mincnt>'.$row['limit_ea'].'</mincnt>
				<nowcnt>'.$row['buyercnt'].'</nowcnt>
				<start_date>'.$row['startdt'].'</start_date>
				<end_date>'.$row['enddt'].'</end_date>
				<expire_date>'.$row['useenddt'].'</expire_date>
				<photo><![CDATA['.$row['image'].']]></photo>
			</item>
			';

		}	// for


		$_xml .= '</items>';

		return $_xml;

	}

}

?>