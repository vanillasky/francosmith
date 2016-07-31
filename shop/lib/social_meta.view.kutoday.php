<?
$_meta_site_info['kutoday'] = array('name'=>'ÄíÅõµ¥ÀÌ','url'=>'http://kutoday.co.kr');

// ÄíÅõµ¥ÀÌ ºä
class social_meta_view_kutoday extends social_meta_view {

	var $data = array();

	function social_meta_view_kutoday() {
		$this->goods_only = 'A';
		$this->encode = 'UTF-8';
	}

	function make( $data = array() ) {

		$this->cfg = $this->encode($this->cfg);

		$_xml  = '';
		$_xml .= '<?xml version=\'1.0\' encoding=\'utf-8\'?>';
		$_xml .= '<rss version=\'2.0\' xmlns:dc=\'http://purl.org/dc/elements/1.1/\'>';
		$_xml .= '<channel>';


		$keys = array_keys($data);
		for($i=0,$max=sizeof($keys);$i<$max;$i++) {

			$row = $data[ $keys[$i] ];

			$row = $this->encode($row);

			$_xml .= '
			<item>
				<linkUrl><![CDATA['.$row['url'].']]></linkUrl>
				<title><![CDATA['.$row['goodsnm'].']]></title>
				<subtitle></subtitle>
				<description><![CDATA['.$row['shortdesc'].']]></description>
				<storeName><![CDATA['.$this->cfg['shopName'].']]></storeName>
				<storeAddr><![CDATA['.$row['usable_spot_address'].' '.$row['usable_spot_address_ext'].']]></storeAddr>
				<storePhone><![CDATA['.$row['usable_spot_phone'].']]></storePhone>
				<storePark></storePark>
				<author><![CDATA[]]></author>
				<category><![CDATA[]]></category>
				<area><![CDATA['.$row['area'].']]></area>
				<minCnt>'.$row['limit_ea'].'</minCnt>
				<maxCnt>'.$row['maxstock'].'</maxCnt>
				<curCnt>'.$row['buyercnt'].'</curCnt>
				<pubDate><![CDATA[]]></pubDate>
				<sImage><![CDATA['.$row['image'].']]></sImage>
				<mImage><![CDATA['.$row['image'].']]></mImage>
				<price>'.$row['consumer'].'</price>
				<dcPrice>'.$row['price'].'</dcPrice>
				<dcRate>'.$row['dc_rate'].'</dcRate>
				<dcInfo></dcInfo>
				<shipFree><![CDATA[]]></shipFree>
				<itemInfo></itemInfo>
				<sDate><![CDATA['.$row['startdt'].']]></sDate>
				<eDate><![CDATA['.$row['enddt'].']]></eDate>
				<mDate><![CDATA[]]></mDate>
			</item>
			';

		}	// for


		$_xml .= '</channel>';
		$_xml .= '</rss>';

		return $_xml;

	}
}
?>