<?
$_meta_site_info['zilumi'] = array('name'=>'Áö¸§´åÄÄ','url'=>'http://www.zilumi.com');

// Áö¸§´åÄÄ ºä
class social_meta_view_zilumi extends social_meta_view {

	var $data = array();

	function social_meta_view_zilumi() {
		$this->goods_only = 'A';
		$this->encode = 'UTF-8';
	}

	function make( $data = array() ) {

		$this->cfg = $this->encode($this->cfg);

		$_xml  = '';
		$_xml .= '<?xml version=\'1.0\' encoding=\'utf-8\'?>';
		$_xml .= '<products>';
		$_xml .= '<url><![CDATA[http://'.$this->cfg['shopUrl'].']]></url>';


		$keys = array_keys($data);
		for($i=0,$max=sizeof($keys);$i<$max;$i++) {

			$row = $data[ $keys[$i] ];

			$row = $this->encode($row);

			$_xml .= '
			<product>
				<pid><![CDATA['.$row['tgsno'].']]></pid>
				<mimg><![CDATA['.$row['image'].']]></mimg>
				<url><![CDATA['.$row['url'].']]></url>
				<caution></caution>
				<area1><![CDATA['.$row['area'].']]></area1>
				<area2><![CDATA[]]></area2>
				<category><![CDATA[]]></category>
				<name><![CDATA['.$row['goodsnm'].']]></name>
				<desc></desc>
				<detail><![CDATA['.$row['shortdesc'].']]></detail>
				<price>'.$row['consumer'].'</price>
				<saleprice>'.$row['price'].'</saleprice>
				<salerate>'.$row['dc_rate'].'</salerate>
				<mincnt>'.$row['limit_ea'].'</mincnt>
				<maxcnt>'.$row['maxstock'].'</maxcnt>
				<stock></stock>
				<salecnt>'.$row['buyercnt'].'</salecnt>
				<startdate><![CDATA['.$row['startdt'].']]></startdate>
				<enddate><![CDATA['.$row['enddt'].']]></enddate>
				<shop_location></shop_location>
				<shop_name><![CDATA['.$this->cfg['shopName'].']]></shop_name>
				<shop_addr><![CDATA['.$row['usable_spot_address'].' '.$row['usable_spot_address_ext'].']]></shop_addr>
			</product>
			';

		}	// for


		$_xml .= '</products>';

		return $_xml;

	}
}
?>