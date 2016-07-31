<?
$_meta_site_info['coupot'] = array('name'=>'ÄíÆÌ','url'=>'http://www.coupot.co.kr');

// ÄíÆÌ ºä
class social_meta_view_coupot extends social_meta_view {

	var $data = array();

	function social_meta_view_coupot() {
		$this->goods_only = 'A';
		$this->encode = 'UTF-8';
	}

	function make( $data = array() ) {

		$this->cfg = $this->encode($this->cfg);

		$_xml  = '';
		$_xml .= '<?xml version=\'1.0\' encoding=\'utf-8\'?>';
		$_xml .= '<rss version=\'2.0\' xmlns:dc=\'http://purl.org/dc/elements/1.1/\'>';
		$_xml .= '<channel>';
		$_xml .= '	<title><![CDATA['.$this->cfg['shopName'].']]></title>';
		$_xml .= '	<link><![CDATA[http://'.$this->cfg['shopUrl'].']]></link>';
		$_xml .= '	<description></description>';

		$keys = array_keys($data);
		for($i=0,$max=sizeof($keys);$i<$max;$i++) {

			$row = $data[ $keys[$i] ];

			$row = $this->encode($row);

			$_xml .= '
			<item>
				<type><![CDATA[C]]></type>
				<title><![CDATA['.$row['goodsnm'].']]></title>
				<link><![CDATA['.$row['url'].']]></link>
				<image><![CDATA['.$row['image'].']]></image>
				<description><![CDATA['.$row['shortdesc'].']]></description>
				<category><![CDATA[]]></category>
				<area><![CDATA['.$row['area'].']]></area>
				<price_original>'.$row['consumer'].'</price_original>
				<price_discount>'.$row['price'].'</price_discount>
				<price_percent>'.$row['dc_rate'].'</price_percent>
				<count_max>'.$row['maxstock'].'</count_max>
				<count_now>'.$row['buyercnt'].'</count_now>
				<date_start><![CDATA['.$row['startdt'].']]></date_start>
				<date_end><![CDATA['.$row['enddt'].']]></date_end>
			</item>
			';

		}	// for


		$_xml .= '</channel>';
		$_xml .= '</rss>';

		return $_xml;

	}
}
?>