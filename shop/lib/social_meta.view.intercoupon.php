<?
$_meta_site_info['intercoupon'] = array('name'=>'ÀÎÅÍÄíÆù','url'=>'http://www.intercoupon.co.kr');

// ÀÎÅÍÄíÆù ºä
class social_meta_view_intercoupon extends social_meta_view {

	var $data = array();

	function social_meta_view_intercoupon() {
		$this->goods_only = 'A';
		$this->encode = 'UTF-8';
	}

	function make( $data = array() ) {

		$this->cfg = $this->encode($this->cfg);

		$_xml  = '';
		$_xml .= '<?xml version=\'1.0\' encoding=\'utf-8\'?>';
		$_xml .= '<channel>';
		$_xml .= '<ver>0</ver>';



		$keys = array_keys($data);
		for($i=0,$max=sizeof($keys);$i<$max;$i++) {

			$row = $data[ $keys[$i] ];

			$row = $this->encode($row);

			$_xml .= '
			<item>
				<title><![CDATA['.$row['goodsnm'].']]></title>
				<link><![CDATA['.$row['url'].']]></link>
				<time_start><![CDATA['.$row['startdt'].']]></time_start>
				<time_end><![CDATA['.$row['enddt'].']]></time_end>
				<price_original>'.$row['consumer'].'</price_original>
				<price_now>'.$row['price'].'</price_now>
				<sale_percent>'.$row['dc_rate'].'</sale_percent>
				<sell_count>'.$row['buyercnt'].'</sell_count>
				<count_min>'.$row['limit_ea'].'</count_min>
				<count_max>'.$row['maxstock'].'</count_max>
				<photo1><![CDATA['.$row['image'].']]></photo1>
				<photo2><![CDATA[]]></photo2>
				<type><![CDATA[c]]></type>
				<area><![CDATA['.$row['area'].']]></area>
				<area2></area2>
				<category><![CDATA[]]></category>
				<category2></category2>
				<addr><![CDATA['.$row['usable_spot_address'].' '.$row['usable_spot_address_ext'].']]></addr>
				<latitude></latitude>
				<longitude></longitude>
				<desc_text><![CDATA['.$row['shortdesc'].']]></desc_text>
				<desc_html><![CDATA[<P>'.$row['shortdesc'].'</P>]]></desc_html>
			</item>
			';

		}	// for


		$_xml .= '</channel>';

		return $_xml;

	}
}
?>