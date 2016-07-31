<?
$_meta_site_info['hiticket'] = array('name'=>'하이티켓','url'=>'http://www.hiticket.co.kr');

// 하이티켓 뷰
class social_meta_view_hiticket extends social_meta_view {

	var $data = array();

	function social_meta_view_hiticket() {
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
				<product_number><![CDATA['.$row['tgsno'].']]></product_number>
				<product_category><![CDATA[]]></product_category>
				<product_fullname><![CDATA['.$row['goodsnm'].']]></product_fullname>
				<product_desc><![CDATA['.$row['shortdesc'].']]></product_desc>
				<product_area><![CDATA['.$row['area'].']]></product_area>
				<shop_zipcode></shop_zipcode>
				<shop_address1><![CDATA['.$row['usable_spot_address'].' '.$row['usable_spot_address_ext'].']]></shop_address1>
				<shop_address2></shop_address2>
				<shop_address3></shop_address3>
				<link_web>'.$row['url'].'</link_web>
				<link_image>'.$row['image'].'</link_image>
				<price_original>'.$row['consumer'].'</price_original>
				<price_discount>'.$row['price'].'</price_discount>
				<price_percent>'.$row['dc_rate'].'</price_percent>
				<sales_max>'.$row['maxstock'].'</sales_max>
				<sales_min>'.$row['limit_ea'].'</sales_min>
				<sales_cur>'.$row['buyercnt'].'</sales_cur>
				<sales_stock></sales_stock>
				<sales_per></sales_per>
				<date_start>'.$row['startdt'].'</date_start>
				<date_end>'.$row['enddt'].'</date_end>
				<etc_etc><![CDATA['.$this->cfg['shopName'].']]></etc_etc>
				<etc_statusYN>Y</etc_statusYN>
				<etc_eventYN></etc_eventYN>
				<map_location_ver></map_location_ver>
				<map_location_hor></map_location_hor>
			</item>
			';

		}	// for


		$_xml .= '</items>';

		return $_xml;

	}
}
?>