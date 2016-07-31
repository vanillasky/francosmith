<?
$_meta_site_info['5829'] = array('name'=>'오빠이거','url'=>'http://5829.co.kr');

// 오빠이거 뷰
class social_meta_view_5829 extends social_meta_view {

	var $data = array();

	function social_meta_view_5829() {
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

			$row = $this->encode($row);

			$_xml .= '
			<product>
				<category><![CDATA[]]></category>
				<name><![CDATA['.$row['goodsnm'].']]></name>
				<description><![CDATA['.$row['shortdesc'].']]></description>
				<price_original>'.$row['consumer'].'</price_original>
				<price_rate>'.$row['dc_rate'].'</price_rate>
				<price_discount>'.$row['price'].'</price_discount>
				<sdate><![CDATA['.$row['startdt'].']]></sdate>
				<edate><![CDATA['.$row['enddt'].']]></edate>
				<images>
					<image><![CDATA['.$row['image'].']]></image>
				</images>
				<url><![CDATA['.$row['url'].']]></url>
				<min_cnt>'.$row['limit_ea'].'</min_cnt>
				<max_cnt>'.$row['maxstock'].'</max_cnt>
				<sale_cnt>'.$row['buyercnt'].'</sale_cnt>
				<shops>
					<shop>
						<shop_name><![CDATA['.$this->cfg['shopName'].']]></shop_name>
						<shop_address><![CDATA['.$row['usable_spot_address'].' '.$row['usable_spot_address_ext'].']]></shop_address>
						<shop_tel><![CDATA['.$row['usable_spot_phone'].']]></shop_tel>
						<latitude></latitude>
						<longitude></longitude>
					</shop>
				</shops>
			</product>
			';

		}	// for


		$_xml .= '</products>';

		return $_xml;

	}
}
?>
