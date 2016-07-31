<?
$_meta_site_info['anycoupon'] = array('name'=>'¾Ö´ÏÄíÆù','url'=>'http://www.anycoupon.co.kr');

// ¾Ö´ÏÄíÆù ºä
class social_meta_view_anycoupon extends social_meta_view {

	var $data = array();

	function social_meta_view_anycoupon() {
		$this->goods_only = 'A';
		$this->encode = 'UTF-8';
	}

	function make( $data = array() ) {

		$this->cfg = $this->encode($this->cfg);

		$_xml  = '';
		$_xml .= '<?xml version=\'1.0\' encoding=\'utf-8\'?>';
		$_xml .= '<rss version=\'2.0\' xmlns:dc=\'http://purl.org/dc/elements/1.1/\'>';
		$_xml .= '<channel>';
		$_xml .= '<title><![CDATA['.$this->cfg['shopName'].']]></title>';
		$_xml .= '<link><![CDATA[http://'.$this->cfg['shopUrl'].']]></link>';
		$_xml .= '<description></description>';

		$keys = array_keys($data);
		for($i=0,$max=sizeof($keys);$i<$max;$i++) {

			$row = $data[ $keys[$i] ];

			$row = $this->encode($row);

			$_xml .= '
			<item>
				<title><![CDATA['.$row['goodsnm'].']]></title>
				<link><![CDATA['.$row['url'].']]></link>
				<description><![CDATA['.$row['shortdesc'].']]></description>
				<categoryname><![CDATA[]]></categoryname>
				<areaname><![CDATA['.$row['area'].']]></areaname>
				<image><![CDATA['.$row['image'].']]></image>
				<originprice>'.$row['consumer'].'</originprice>
				<dcpercent>'.$row['dc_rate'].'</dcpercent>
				<dcprice>'.$row['price'].'</dcprice>
				<mincount>'.$row['limit_ea'].'</mincount>
				<maxcount>'.$row['maxstock'].'</maxcount>
				<maxorder></maxorder>
				<curcount>'.$row['buyercnt'].'</curcount>
				<close>1</close>
				<startdate><![CDATA['.$row['startdt'].']]></startdate>
				<enddate><![CDATA['.$row['enddt'].']]></enddate>
				<pubdate></pubdate>
				<shopname><![CDATA['.$this->cfg['shopName'].']]></shopname>
				<address><![CDATA['.$row['usable_spot_address'].' '.$row['usable_spot_address_ext'].']]></address>
				<lng></lng>
				<lat></lat>
			</item>
			';

		}	// for


		$_xml .= '</channel>';
		$_xml .= '</rss>';

		return $_xml;

	}
}


?>