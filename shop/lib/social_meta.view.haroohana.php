<?
$_meta_site_info['haroohana'] = array('name'=>'하루하나','url'=>'http://www.haroohana.com');

// 하루하나 뷰
class social_meta_view_haroohana extends social_meta_view {

	var $data = array();

	function social_meta_view_haroohana() {
		$this->goods_only = 'A';
		$this->encode = 'UTF-8';
	}

	function make( $data = array() ) {

		$this->cfg = $this->encode($this->cfg);

		$_xml  = '';
		$_xml .= '<?xml version=\'1.0\' encoding=\'utf-8\'?>';
		$_xml .= '<root>';
		$_xml .= '<coupons>';

		$keys = array_keys($data);
		for($i=0,$max=sizeof($keys);$i<$max;$i++) {

			$row = $data[ $keys[$i] ];

			$row['startdt'] = date('YmdH', strtotime($row['startdt']));
			$row['enddt'] = date('YmdH', strtotime($row['enddt']));


			$row = $this->encode($row);

			$_xml .= '
			<coupon>
				<id>'.$row['tgsno'].'</id>
				<title><![CDATA['.$row['goodsnm'].']]></title>
				<category></category>
				<description><![CDATA['.$row['shortdesc'].']]></description>
				<original_price>'.$row['consumer'].'</original_price>
				<discount_price>'.$row['price'].'</discount_price>
				<delivery_fee_yn><![CDATA[N]]></delivery_fee_yn>
				<image><![CDATA['.$row['image'].']]></image>
				<link><![CDATA['.$row['url'].']]></link>
				<mobile_link></mobile_link>
				<start_date>'.$row['startdt'].'</start_date>
				<end_date>'.$row['enddt'].'</end_date>
				<coupon_sale_max>'.$row['maxstock'].'</coupon_sale_max>
				<coupon_sale_count>'.$row['buyercnt'].'</coupon_sale_count>
				<coupon_company><![CDATA['.$this->cfg['shopName'].']]></coupon_company>
				<coupon_sale_condition>'.$row['limit_ea'].'</coupon_sale_condition>
				<coupon_start_date><![CDATA['.$row['usestartdt'].']]></coupon_start_date>
				<coupon_end_date><![CDATA['.$row['useenddt'].']]></coupon_end_date>
				<coupon_address><![CDATA['.$row['usable_spot_address'].' '.$row['usable_spot_address_ext'].']]></coupon_address>
				<coupon_latitude></coupon_latitude>
				<coupon_longitude></coupon_longitude>
				<coupon_phone>'.$row['usable_spot_phone'].'</coupon_phone>
			</coupon>
			';

		}	// for

		$_xml .= '</coupons>';
		$_xml .= '</root>';

		return $_xml;

	}
}
?>