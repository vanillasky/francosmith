<?
$_meta_site_info['dgda'] = array('name'=>'디지다','url'=>'http://www.dgda.co.kr');

// 디지다 뷰
class social_meta_view_dgda extends social_meta_view {

	var $data = array();

	function social_meta_view_dgda() {
		$this->goods_only = 'A';
		$this->encode = 'UTF-8';
	}

	function make( $data = array() ) {

		$this->cfg = $this->encode($this->cfg);

		$_xml  = '';
		$_xml .= '<?xml version=\'1.0\' encoding=\'utf-8\'?>';
		$_xml .= '<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/">';
		$_xml .= '<items>';


		$keys = array_keys($data);
		for($i=0,$max=sizeof($keys);$i<$max;$i++) {

			$row = $data[ $keys[$i] ];

			$row = $this->encode($row);

			$_xml .= '
			<item>
				<url><![CDATA['.$row['url'].']]></url>
				<category><![CDATA[]]></category>
				<address><![CDATA['.$row['usable_spot_address'].' '.$row['usable_spot_address_ext'].']]></address>
				<location><![CDATA['.$row['area'].']]></location>
				<title><![CDATA['.$row['goodsnm'].']]></title>
				<content><![CDATA['.$row['shortdesc'].']]></content>
				<image><![CDATA['.$row['image'].']]></image>
				<price>'.$row['consumer'].'</price>
				<dcprice>'.$row['price'].'</dcprice>
				<dcrate>'.$row['dc_rate'].'</dcrate>
				<mincnt>'.$row['limit_ea'].'</mincnt>
				<maxcnt>'.$row['maxstock'].'</maxcnt>
				<salecnt>'.$row['buyercnt'].'</salecnt>
				<startdate><![CDATA['.$row['startdt'].']]></startdate>
				<limitdate><![CDATA['.$row['enddt'].']]></limitdate>
				<expire><![CDATA['.$row['useenddt'].']]></expire>
			</item>
			';

		}	// for


		$_xml .= '</items>';
		$_xml .= '</rss>';

		return $_xml;

	}
}
?>