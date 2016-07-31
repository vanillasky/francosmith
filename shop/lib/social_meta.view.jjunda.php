<?
$_meta_site_info['jjunda'] = array('name'=>'Â¾´ÙÂÀ³Ý','url'=>'http://www.jjunda.net/');

// Â¾´ÙÂÀ³Ý ºä
class social_meta_view_jjunda extends social_meta_view {

	var $data = array();

	function social_meta_view_jjunda() {
		$this->goods_only = 'A';
		$this->encode = 'UTF-8';
	}

	function make( $data = array() ) {

		$this->cfg = $this->encode($this->cfg);

		$_xml  = '';
		$_xml .= '<?xml version="1.0" encoding="utf-8"?>';
		$_xml .= '<items>';

		$keys = array_keys($data);
		for($i=0,$max=sizeof($keys);$i<$max;$i++) {
			$row = $data[ $keys[$i] ];

			$row = $this->encode($row);

			$_xml .= '
			<item>
				<product_number><![CDATA['.$row[tgsno].']]></product_number>
				<product_category><![CDATA['.$row[usable_spot_type].']]></product_category>
				<product_fullname><![CDATA['.$row[goodsnm].']]></product_fullname>
				<product_desc><![CDATA['.$row[shortdesc].']]></product_desc>
				<product_area><![CDATA['.$row[area].']]></product_area>
				<shop_zipcode>'.$row[usable_spot_post].'</shop_zipcode>
				<shop_address1>'.$row[address1].'</shop_address1>
				<shop_address2>'.$row[address2].'</shop_address2>
				<shop_address3>'.$row[address3].'</shop_address3>
				<link_web>'.$row[url].'</link_web>
				<link_image>'.$row[image].'</link_image>
				<price_original>'.$row[consumer].'</price_original>
				<price_discount>'.$row[price].'</price_discount>
				<price_percent>'.$row[dc_rate].'</price_percent>
				<sales_cur>'.$row[buyercnt].'</sales_cur>
				<sales_stock>'.$row[maxstock].'</sales_stock>
				<sales_per>'.$row[limit_ea].'</sales_per>
				<date_start>'.$row[startdt].'</date_start>
				<date_end>'.$row[enddt].'</date_end>
				<etc_etc>'.$row[company_name].'</etc_etc>
				<etc_statusYN>'.$row[company_phone].'</etc_statusYN>
			</item>
			';
		}	// for
		$_xml .= '</items>';

		return $_xml;
	}

}
?>