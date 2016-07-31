<?
$_meta_site_info['tigle'] = array('name'=>'Æ¼±Û','url'=>'http://www.tigle.net');

// Æ¼±Û ºä
class social_meta_view_tigle extends social_meta_view {

	var $data = array();

	function social_meta_view_tigle() {
		$this->goods_only = 'A';
		$this->encode = 'UTF-8';
	}

	function make( $data = array() ) {

		$this->cfg = $this->encode($this->cfg);

		$_xml  = '';
		$_xml .= '<?xml version=\'1.0\' encoding=\'utf-8\'?>';
		$_xml .= '<coupon_feed>';
		$_xml .= '<doc_ver><![CDATA[ 1 ]]></doc_ver>';
		$_xml .= '<name><![CDATA['.$this->cfg['shopName'].']]></name>';
		$_xml .= '<url><![CDATA[http://'.$this->cfg['shopUrl'].']]></url>';
		$_xml .= '<logo_image><![CDATA[]]></logo_image>';
		$_xml .= '<deals>';


		$keys = array_keys($data);
		for($i=0,$max=sizeof($keys);$i<$max;$i++) {

			$row = $data[ $keys[$i] ];

			$row = $this->encode($row);

			$_xml .= '
			<deal>
				<title><![CDATA['.$row['goodsnm'].']]></title>
				<local><![CDATA['.$row['area'].']]></local>
				<category><![CDATA[]]></category>
				<description><![CDATA['.$row['shortdesc'].']]></description>
				<original><![CDATA['.$row['consumer'].']]></original>
				<price><![CDATA['.$row['price'].']]></price>
				<discount><![CDATA['.$row['dc_rate'].']]></discount>
				<min_count><![CDATA['.$row['limit_ea'].']]></min_count>
				<now_count><![CDATA[0]]></now_count>
				<max_count><![CDATA['.$row['maxstock'].']]></max_count>
				<start_at><![CDATA['.$row['startdt'].']]></start_at>
				<end_at><![CDATA['.$row['enddt'].']]></end_at>
				<image><![CDATA['.$row['image'].']]></image>
				<url><![CDATA['.$row['url'].']]></url>
			</deal>
			';

		}	// for

		$_xml .= '</deals>';
		$_xml .= '</coupon_feed>';

		return $_xml;

	}
}
?>