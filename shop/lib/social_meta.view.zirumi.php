<?
$_meta_site_info['zirumi'] = array('name'=>'지르미','url'=>'http://www.zirumi.com');

// 지르미 뷰
class social_meta_view_zirumi extends social_meta_view {

	var $data = array();

	function social_meta_view_zirumi() {
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
		$_xml .= '<language>ko</language>';

		$keys = array_keys($data);
		for($i=0,$max=sizeof($keys);$i<$max;$i++) {

			$row = $data[ $keys[$i] ];

			$row = $this->encode($row);

			$_xml .= '
			<item>
				<title><![CDATA['.$row['goodsnm'].']]></title>
				<link><![CDATA['.$row['url'].']]></link>
				<description><![CDATA['.$row['shortdesc'].']]></description>
				<goods_img><![CDATA['.$row['image'].']]></goods_img>
				<saleprice>'.$row['price'].'</saleprice>
				<area><![CDATA['.$row['area'].']]></area>
			</item>
			';

		}	// for



		$_xml .= '</channel>';
		$_xml .= '</rss>';

		return $_xml;

	}
}
?>