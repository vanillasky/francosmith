<?
$_meta_site_info['couponmonster'] = array('name'=>'ÄíÆù¸ó½ºÅÍ','url'=>'http://www.couponmonster.co.kr');

// ÄíÆù¸ó½ºÅÍ ºä
class social_meta_view_couponmonster extends social_meta_view {

	var $data = array();

	function social_meta_view_couponmonster() {
		$this->goods_only = 'A';
		$this->encode = 'UTF-8';
	}

	function make( $data = array() ) {

		$this->cfg = $this->encode($this->cfg);

		$_xml  = '';
		$_xml .= '<?xml version=\'1.0\' encoding=\'utf-8\'?>';
		$_xml .= '<items>';


		$keys = array_keys($data);
		for($i=0,$max=sizeof($keys);$i<$max;$i++) {

			$row = $data[ $keys[$i] ];

			$row = $this->encode($row);

			$_xml .= '
			<item>
				<url><![CDATA['.$row['url'].']]></url>
				<region><![CDATA['.$row['area'].']]></region>
				<category><![CDATA[]]></category>
				<image><![CDATA['.$row['image'].']]></image>
				<title><![CDATA['.$row['goodsnm'].']]></title>
				<descript><![CDATA['.$row['shortdesc'].']]></descript>
				<price>'.$row['consumer'].'</price>
				<sale_price>'.$row['price'].'</sale_price>
				<sale_rate>'.$row['dc_rate'].'</sale_rate>
				<max_cnt>'.$row['maxstock'].'</max_cnt>
				<min_cnt>'.$row['limit_ea'].'</min_cnt>
				<now_cnt>'.$row['buyercnt'].'</now_cnt>
				<address><![CDATA['.$row['usable_spot_address'].' '.$row['usable_spot_address_ext'].']]></address>
				<tel><![CDATA['.$row['usable_spot_phone'].']]></tel>
				<start_date><![CDATA['.$row['startdt'].']]></start_date>
				<limit_date><![CDATA['.$row['enddt'].']]></limit_date>
			</item>
			';

		}	// for


		$_xml .= '</items>';

		return $_xml;

	}
}
?>