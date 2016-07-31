<?
$_meta_site_info['couponchart'] = array('name'=>'ÄíÆùÂ÷Æ®','url'=>'http://www.couponchart.co.kr/');

// ÄíÆùÂ÷Æ® ºä
class social_meta_view_couponchart extends social_meta_view {

	var $data = array();

	function social_meta_view_couponchart() {
		$this->goods_only = 'A';
		$this->encode = 'UTF-8';
	}

	function make( $data = array() ) {

		$this->cfg = $this->encode($this->cfg);

		$_xml  = '';
		$_xml .= '<?xml version=\'1.0\' encoding=\'utf-8\'?>';
		$_xml .= '<products>';


		$keys = array_keys($data);
		for($i=0,$max=sizeof($keys);$i<$max;$i++) {
			$row = $data[ $keys[$i] ];

			if ($row['goodstype'] == 'goods') {
				$row['usable_spot_address'] = 'Àü±¹¹è¼Û';
				$row['usable_spot_address_ext'] = '';
			}

			$row = $this->encode($row);

			$_xml .= '
			<product>
				<product_id>'.$row['tgsno'].'</product_id>
				<product_url><![CDATA['.$row['url'].']]></product_url>
				<product_title><![CDATA['.$row['goodsnm'].']]></product_title>
				<product_desc>'.$row['shortdesc'].'</product_desc>
				<map_url></map_url>
				<sale_start>'.$row['startdt'].'</sale_start>
				<sale_end>'.$row['enddt'].'</sale_end>
				<coupon_use_start>'.$row['usestartdt'].'</coupon_use_start>
				<coupon_use_end>'.$row['useenddt'].'</coupon_use_end>
				<shop_name><![CDATA['.$row['usable_spot_name'].']]></shop_name>
				<shop_tel><![CDATA['.$row['usable_spot_phone'].']]></shop_tel>
				<shop_address><![CDATA['.$row['usable_spot_address'].' '.$row['usable_spot_address_ext'].']]></shop_address>
				<buy_count>'.$row['buyercnt'].'</buy_count>
				<buy_limit>'.$row['limit_ea'].'</buy_limit>
				<buy_max>'.$row['maxstock'].'</buy_max>
				<price_normal>'.$row['consumer'].'</price_normal>
				<price_discount>'.$row['price'].'</price_discount>
				<discount_rate>'.$row['dc_rate'].'</discount_rate>
				<image_url1>'.$row['image'].'</image_url1>
				<image_url2></image_url2>
			</product>
			';

		}	// for


		$_xml .= '</products>';

		return $_xml;

	}

}

?>