<?
$_meta_site_info['banggab'] = array('name'=>'¹Ý°ª´åÄÄ','url'=>'http://www.banggab.com');

// ¹Ý°ª´åÄÄ ºä
class social_meta_view_banggab extends social_meta_view {

	var $data = array();

	function social_meta_view_banggab() {
		$this->goods_only = 'A';
		$this->encode = 'UTF-8';
	}

	function make( $data = array() ) {

		$this->cfg = $this->encode($this->cfg);

		$_xml  = '';
		$_xml .= '<?xml version=\'1.0\' encoding=\'utf-8\'?>';
		$_xml .= '<coupon_feed>';
		$_xml .= '<doc_ver>1/<doc_ver>';
		$_xml .= '<name><![CDATA['.$this->cfg['shopName'].']]>/<name>';
		$_xml .= '<url><![CDATA[»çÀÌÆ®ÁÖ¼Ò]]>/<url>';
		$_xml .= '<logo_image><![CDATA[logo_image]]>/<logo_image>';
		$_xml .= '<deals>';


		$keys = array_keys($data);
		for($i=0,$max=sizeof($keys);$i<$max;$i++) {

			$row = $data[ $keys[$i] ];

			$row = $this->encode($row);

			$_xml .= '
			<deal>
				  <meta_id><![CDATA['.$row['tgsno'].']]></meta_id>
				  <cate_num></cate_num>
				  <area><![CDATA['.$row['area'].']]></area>
				  <title><![CDATA['.$row['goodsnm'].']]></title>
				  <description><![CDATA['.$row['shortdesc'].']]></description>
				  <original>'.$row['consumer'].'</original>
				  <price>'.$row['price'].'</price>
				  <discount>'.$row['dc_rate'].'</discount>
				  <min_count>'.$row['limit_ea'].'</min_count>
				  <now_count>'.$row['buyercnt'].'</now_count>
				  <max_count>'.$row['maxstock'].'</max_count>
				  <end_at><![CDATA['.$row['enddt'].']]></end_at>
				  <image><![CDATA['.$row['image'].']]></image>
				  <url><![CDATA['.$row['url'].']]></url>
			  </deal>
			';

		}	// for


		$_xml .= '</deals>';
		$_xml .= '</coupon_feed>';

		return $_xml;

	}
}
?>