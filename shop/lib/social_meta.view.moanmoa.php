<?
$_meta_site_info['moanmoa'] = array('name'=>'ÄíÆùÀ¯','url'=>'http://www.moanmoa.com');

// ÄíÆùÀ¯ ºä
class social_meta_view_moanmoa extends social_meta_view {

	var $data = array();

	function social_meta_view_moanmoa() {
		$this->goods_only = 'A';
		$this->encode = 'UTF-8';
	}

	function make( $data = array() ) {

		$this->cfg = $this->encode($this->cfg);

		$_xml  = '';
		$_xml .= '<?xml version=\'1.0\' encoding=\'utf-8\'?>';
		$_xml .= '<channel>';


		$keys = array_keys($data);
		for($i=0,$max=sizeof($keys);$i<$max;$i++) {

			$row = $data[ $keys[$i] ];

			$row = $this->encode($row);

			$_xml .= '
			<item>
				<type><![CDATA[C]]></type>
				<title><![CDATA['.$row['goodsnm'].']]></title>
				<link><![CDATA['.$row['url'].']]></link>
				<photo1><![CDATA['.$row['image'].']]></photo1>
				<price_original>'.$row['consumer'].'</price_original>
				<price_now>'.$row['price'].'</price_now>
				<sale_percent>'.$row['dc_rate'].'</sale_percent>
				<count_max>'.$row['maxstock'].'</count_max>
				<count_min>'.$row['limit_ea'].'</count_min>
				<sell_count>'.$row['buyercnt'].'</sell_count>
				<area><![CDATA['.$row['area'].']]></area>
				<category><![CDATA[]]></category>
				<shop><![CDATA['.$this->cfg['shopName'].']]></shop>
				<addr><![CDATA['.$row['usable_spot_address'].' '.$row['usable_spot_address_ext'].']]></addr>
				<phone><![CDATA['.$row['usable_spot_phone'].']]></phone>
				<lat></lat>
				<lng></lng>
				<time_start><![CDATA['.$row['startdt'].']]></time_start>
				<time_end><![CDATA['.$row['enddt'].']]></time_end>
				<desc_text><![CDATA['.$row['shortdesc'].']]></desc_text>
				<status>Y</status>
			</item>
			';

		}	// for


		$_xml .= '</channel>';

		return $_xml;

	}
}
?>
