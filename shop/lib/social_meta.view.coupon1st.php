<?
$_meta_site_info['coupon1st'] = array('name'=>'ÄíÆùÀÏ¹ø°¡','url'=>'http://www.coupon1st.co.kr');

// ÄíÆùÀÏ¹ø°¡ ºä
class social_meta_view_coupon1st extends social_meta_view {

	var $data = array();

	function social_meta_view_coupon1st() {
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
				<time_start>'.$row['startdt'].'</time_start>
				<time_end>'.$row['enddt'].'</time_end>
				<price_original>'.$row['consumer'].'</price_original>
				<price_now>'.$row['price'].'</price_now>
				<sale_percent>'.$row['dc_rate'].'</sale_percent>
				<sell_count>'.$row['buyercnt'].'</sell_count>
				<count_min>'.$row['limit_ea'].'</count_min>
				<count_max>'.$row['maxstock'].'</count_max>
				<photo1><![CDATA['.$row['image'].']]></photo1>
				<photo2><![CDATA[]]></photo2>
				<photo3><![CDATA[]]></photo2>
				<photo4><![CDATA[]]></photo2>
				<photo5><![CDATA[]]></photo2>
				<type>c</type>
				<area><![CDATA['.$row['area'].']]></area>
				<area2><![CDATA[]]></area2>
				<category><![CDATA[]]></category>
				<category2><![CDATA[]]></category2>
				<addr><![CDATA[]]></addr>
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
