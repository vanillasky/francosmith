<?
$_meta_site_info['opendays'] = array('name'=>'¿ÀÇÂµ¥ÀÌÁî','url'=>'http://www.opendays.co.kr');

// ¿ÀÇÂµ¥ÀÌÁî ºä
class social_meta_view_opendays extends social_meta_view {

	var $data = array();

	function social_meta_view_opendays() {
		$this->goods_only = 'A';
		$this->encode = 'UTF-8';
	}

	function make( $data = array() ) {

		$this->cfg = $this->encode($this->cfg);

		$_xml  = '';
		$_xml .= '<?xml version=\'1.0\' encoding=\'utf-8\'?>';
		$_xml .= '<rss version=\'2.0\' xmlns:dc=\'http://purl.org/dc/elements/1.1/\'>';
		$_xml .= '<channel>';
		$_xml .= '	<title><![CDATA['.$this->cfg['shopName'].']]></title>';
		$_xml .= '	<link><![CDATA[http://'.$this->cfg['shopUrl'].']]></link>';
		$_xml .= '	<description></description>';
		$_xml .= '	<language>ko</language>';


		$keys = array_keys($data);
		for($i=0,$max=sizeof($keys);$i<$max;$i++) {

			$row = $data[ $keys[$i] ];

			$row = $this->encode($row);

			$_xml .= '
			<item>
				<title><![CDATA['.$row['goodsnm'].']]></title>
				<link><![CDATA['.$row['url'].']]></link>
				<image1><![CDATA['.$row['image'].']]></image1>
				<buyCount>'.$row['buyercnt'].'</buyCount>
				<maxCount>'.$row['maxstock'].'</maxCount>
				<minCount>'.$row['limit_ea'].'</minCount>
				<price>'.$row['price'].'</price>
				<price0>'.$row['consumer'].'</price0>
				<addr0><![CDATA['.$row['usable_spot_address'].' '.$row['usable_spot_address_ext'].']]></addr0>
				<addr1></addr1>
				<description><![CDATA['.$row['shortdesc'].']]></description>
				<dc:date><![CDATA['.$row['startdt'].']]></dc:date>
				<category0><![CDATA['.$row['area'].']]></category0>
				<category1><![CDATA[]]></category1>
			</item>
			';

		}	// for


		$_xml .= '</channel>';
		$_xml .= '</rss>';

		return $_xml;

	}
}
?>