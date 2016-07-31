<?
$_meta_site_info['couponpark'] = array('name'=>'ÄíÆùÆÄÅ©','url'=>'http://www.couponpark.co.kr');

// ÄíÆùÆÄÅ© ºä
class social_meta_view_couponpark extends social_meta_view {

	var $data = array();

	function social_meta_view_couponpark() {
		$this->goods_only = 'A';
		$this->encode = 'UTF-8';
	}

	function make( $data = array() ) {

		$this->cfg = $this->encode($this->cfg);

		$_xml  = '';
		$_xml .= '<?xml version=\'1.0\' encoding=\'utf-8\'?>';
		$_xml .= '<channel>';
		$_xml .= '<title><![CDATA['.$this->cfg['shopName'].']]></title>';
		$_xml .= '<link><![CDATA[http://'.$this->cfg['shopUrl'].']]></link>';
		$_xml .= '<description></description>';

		$keys = array_keys($data);
		for($i=0,$max=sizeof($keys);$i<$max;$i++) {

			$row = $data[ $keys[$i] ];

			$row = $this->encode($row);

			$_xml .= '
			<item>
				<link><![CDATA['.$row['url'].']]></link>
				<category><![CDATA[]]></category>
				<title><![CDATA['.$row['goodsnm'].']]></title>
				<description><![CDATA['.$row['shortdesc'].']]></description>
				<img_url><![CDATA['.$row['image'].']]></img_url>
				<startdate><![CDATA['.$row['startdt'].']]></startdate>
				<enddate><![CDATA['.$row['enddt'].']]></enddate>
				<original_price>'.$row['consumer'].'</original_price>
				<down_price>'.$row['price'].'</down_price>
				<down_percent>'.$row['dc_rate'].'</down_percent>
				<success_count>'.$row['maxstock'].'</success_count>
				<now_count>'.$row['buyercnt'].'</now_count>
				<end>0</end>
				<addr><![CDATA['.$row['usable_spot_address'].' '.$row['usable_spot_address_ext'].']]></addr>
			</item>
			';

		}	// for


		$_xml .= '</channel>';

		return $_xml;

	}
}
?>