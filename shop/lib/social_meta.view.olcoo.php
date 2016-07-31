<?
$_meta_site_info['olcoo'] = array('name'=>'¿ÃÄí','url'=>'http://www.olcoo.com');

// ¿ÃÄí ºä
class social_meta_view_olcoo extends social_meta_view {

	var $data = array();

	function social_meta_view_olcoo() {
		$this->goods_only = 'A';
		$this->encode = 'UTF-8';
	}

	function make( $data = array() ) {

		$this->cfg = $this->encode($this->cfg);

		$_xml  = '';
		$_xml .= '<?xml version=\'1.0\' encoding=\'utf-8\'?>';
		$_xml .= '<olcoo_sync>';
		$_xml .= '<olcoo_sync_ver>3</olcoo_sync_ver>';
		$_xml .= '<name><![CDATA['.$this->cfg['shopName'].']]></name>';
		$_xml .= '<url><![CDATA[http://'.$this->cfg['shopUrl'].']]></url>';
		$_xml .= '<deals>';

		$keys = array_keys($data);
		for($i=0,$max=sizeof($keys);$i<$max;$i++) {

			$row = $data[ $keys[$i] ];

			$row = $this->encode($row);

			$_xml .= '
			<deal>
				<sync_id><![CDATA['.$row['tgsno'].']]></sync_id>
				<start_at><![CDATA['.$row['startdt'].']]></start_at>
				<end_at><![CDATA['.$row['enddt'].']]></end_at>
				<deal_end_at><![CDATA[]]></deal_end_at>
				<url><![CDATA['.$row['url'].']]></url>
				<mobile_url></mobile_url>
				<price>'.$row['consumer'].'</price>
				<off_price>'.$row['price'].'</off_price>
				<sale_count>'.$row['buyercnt'].'</sale_count>
				<max_count>'.$row['maxstock'].'</max_count>
				<title><![CDATA['.$row['goodsnm'].']]></title>
				<sub_title><![CDATA[]]></sub_title>
				<seller><![CDATA['.$this->cfg['shopName'].']]></seller>
				<seller_add><![CDATA['.$row['usable_spot_address'].' '.$row['usable_spot_address_ext'].']]></seller_add>
				<area><![CDATA['.$row['area'].']]></area>
				<area_desc></area_desc>
				<category><![CDATA[]]></category>
				<images></images>
			</deal>
			';

		}	// for


		$_xml .= '</deals>';
		$_xml .= '</olcoo_sync>';

		return $_xml;

	}
}
?>