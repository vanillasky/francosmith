<?
$_meta_site_info['alltwt'] = array('name'=>'¿ÃÆ®À­','url'=>'http://alltwt.net');

// ¿ÃÆ®À­ ºä
class social_meta_view_alltwt extends social_meta_view {

	var $data = array();

	function social_meta_view_alltwt() {
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
				  <category><![CDATA[]]></category>
				  <area><![CDATA['.$row['area'].']]></area>
				  <img><![CDATA['.$row['image'].']]></img>
				  <nprice>'.$row['consumer'].'</nprice>
				  <rprice>'.$row['price'].'</rprice>
				  <dcrate>'.$row['dc_rate'].'</dcrate>
				  <mincnt>'.$row['limit_ea'].'</mincnt>
				  <maxcnt>'.$row['maxstock'].'</maxcnt>
				  <salecnt>'.$row['buyercnt'].'</salecnt>
				  <startdate><![CDATA['.$row['startdt'].']]></startdate>
				  <enddate><![CDATA['.$row['enddt'].']]></enddate>
				  <description><![CDATA['.$row['shortdesc'].']]></description>
			  </item>
			';

		}	// for

		$_xml .= '</channel>';
		$_xml .= '</rss>';

		return $_xml;

	}
}
?>