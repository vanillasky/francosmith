<?
$_meta_site_info['ilikeclick'] = array('name'=>'아이라이크','url'=>'http://social.ilikeclick.com');

// 아이라이크 뷰
class social_meta_view_ilikeclick extends social_meta_view {

	var $data = array();

	function social_meta_view_ilikeclick() {
		$this->goods_only = 'A';
		$this->encode = 'UTF-8';
	}

	function make( $data = array() ) {

		$this->cfg = $this->encode($this->cfg);

		$_xml  = '';
		$_xml .= '<?xml version=\'1.0\' encoding=\'utf-8\'?>';
		$_xml .= '<deals>';


		$keys = array_keys($data);
		for($i=0,$max=sizeof($keys);$i<$max;$i++) {

			$row = $data[ $keys[$i] ];

			$row = $this->encode($row);

			$_xml .= '
			<item>
				<title><![CDATA['.$row['goodsnm'].']]></title>
				<url><![CDATA['.$row['url'].']]></url>
				<division><![CDATA[]]></division>
				<region><![CDATA['.$row['area'].']]></region>
				<description><![CDATA['.$row['shortdesc'].']]></description>
				<original_price>'.$row['consumer'].'</original_price>
				<sale_price>'.$row['price'].'</sale_price>
				<sale_rate>'.$row['dc_rate'].'</sale_rate>
				<start_at><![CDATA['.$row['startdt'].']]></start_at>
				<end_at><![CDATA['.$row['enddt'].']]></end_at>
				<min_count>'.$row['limit_ea'].'</min_count>
				<max_count>'.$row['maxstock'].'</max_count>
				<now_count>'.$row['buyercnt'].'</now_count>
				<image1><![CDATA['.$row['image'].']]></image1>
				<image2><![CDATA[]]></image2>
				<shops>
					<shop_name><![CDATA['.$this->cfg['shopName'].']]></shop_name>
					<shop_tel><![CDATA['.$row['usable_spot_phone'].']]></shop_tel>
					<shop_address><![CDATA['.$row['usable_spot_address'].' '.$row['usable_spot_address_ext'].']]></shop_address>
				</shops>
			</item>
			';

		}	// for


		$_xml .= '</deals>';

		return $_xml;

	}
}
?>