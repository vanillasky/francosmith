<?
$_meta_site_info['tmong'] = array('name'=>'Æ¼¸ù','url'=>'http://www.tmong.kr');

// Æ¼¸ù ºä
class social_meta_view_tmong extends social_meta_view {

	var $data = array();

	function social_meta_view_tmong() {
		$this->goods_only = 'A';
		$this->encode = 'UTF-8';
	}

	function make( $data = array() ) {

		$this->cfg = $this->encode($this->cfg);

		$_xml  = '';
		$_xml .= '<?xml version=\'1.0\' encoding=\'utf-8\'?>';
		$_xml .= '<timong>';
		$_xml .= '<name><![CDATA['.$this->cfg['shopName'].']]></name>';
		$_xml .= '<home><![CDATA[http://'.$this->cfg['shopUrl'].']]></home>';
		$todayshop_logo = $_SERVER['DOCUMENT_ROOT'].'/shop/data/todayshop/todayshop_logo.jpg';
		if (is_file($todayshop_logo)) {
			$_img_url = str_replace($_SERVER['DOCUMENT_ROOT'],'http://'.$_SERVER['SERVER_NAME'],$todayshop_logo);
			$_xml .= '<logo_image><![CDATA['.$_img_url.']]></logo_image>';
		}
		$_xml .= '<products>';

		$keys = array_keys($data);
		for($i=0,$max=sizeof($keys);$i<$max;$i++) {

			$row = $data[ $keys[$i] ];

			$row = $this->encode($row);

			$_xml .= '
			<product>
				<start_dt><![CDATA['.$row['startdt'].']]></start_dt>
				<end_dt><![CDATA['.$row['enddt'].']]></end_dt>
				<title><![CDATA['.$row['goodsnm'].']]></title>
				<description><![CDATA['.$row['shortdesc'].']]></description>
				<url><![CDATA['.$row['url'].']]></url>
				<original>'.$row['consumer'].'</original>
				<discount>'.$row['dc_rate'].'</discount>
				<price>'.$row['price'].'</price>
				<max_count>'.$row['maxstock'].'</max_count>
				<min_count>'.$row['limit_ea'].'</min_count>
				<now_count>'.$row['buyercnt'].'</now_count>
				<shops>
					<shop>
						<shop_name><![CDATA['.$this->cfg['shopName'].']]></shop_name>
						<shop_tel><![CDATA['.$row['usable_spot_phone'].']]></shop_tel>
						<shop_address><![CDATA['.$row['usable_spot_address'].' '.$row['usable_spot_address_ext'].']]></shop_address>
						<region><![CDATA['.$row['area'].']]></region>
					</shop>
				</shops>
				<images>
					<image><![CDATA['.$row['image'].']]></image>
					<image><![CDATA[]]></image>
					<image><![CDATA[]]></image>
					<image><![CDATA[]]></image>
				</images>
			</product>
			';

		}	// for


		$_xml .= '</products>';
		$_xml .= '</timong>';

		return $_xml;

	}
}
?>