<?
$_meta_site_info['couponshuttle'] = array('name'=>'ÄíÆù¼ÅÆ²','url'=>'http://www.couponshuttle.com');

// ÄíÆù¼ÅÆ² ºä
class social_meta_view_couponshuttle extends social_meta_view {

	var $data = array();

	function social_meta_view_couponshuttle() {
		$this->goods_only = 'A';
		$this->encode = 'UTF-8';
	}

	function make( $data = array() ) {

		$this->cfg = $this->encode($this->cfg);

		$_xml  = '';
		$_xml .= '<?xml version=\'1.0\' encoding=\'utf-8\'?>';
		$_xml .= '<coupon_feed>';
		$_xml .= '<name><![CDATA['.$this->cfg['shopName'].']]></name>';
		$_xml .= '<url><![CDATA[http://'.$this->cfg['shopUrl'].']]></url>';
		$_xml .= '<deals>';


		$keys = array_keys($data);
		for($i=0,$max=sizeof($keys);$i<$max;$i++) {

			$row = $data[ $keys[$i] ];

			$row = $this->encode($row);

			$_xml .= '
			<deal>
				<title><![CDATA['.$row['goodsnm'].']]></title>
				<description><![CDATA['.$row['shortdesc'].']]></description>
				<url><![CDATA['.$row['url'].']]></url>
				<original>'.$row['consumer'].'</original>
				<discount>'.$row['dc_rate'].'</discount>
				<price>'.$row['price'].'</price>
				<now_count>'.$row['buyercnt'].'</now_count>
				<max_count>'.$row['maxstock'].'</max_count>
				<min_count>'.$row['limit_ea'].'</min_count>
				<images></images>
				<start_at><![CDATA['.$row['startdt'].']]></start_at>
				<end_at><![CDATA['.$row['enddt'].']]></end_at>
				<category><![CDATA[]]></category>
				<shops>
					<shop>
						<shop_address><![CDATA['.$row['usable_spot_address'].' '.$row['usable_spot_address_ext'].']]></shop_address>
						<shop_name><![CDATA['.$this->cfg['shopName'].']]></shop_name>
						<shop_tel><![CDATA['.$row['usable_spot_phone'].']]></shop_tel>
						<region><![CDATA[]]>'.$row['area'].'</region>
					</shop>
				</shops>
			</deal>
			';

		}	// for

		$_xml .= '</deals>';
		$_xml .= '</coupon_feed>';

		return $_xml;

	}
}
?>