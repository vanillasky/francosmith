<?
$_meta_site_info['socialsquare'] = array('name'=>'¼Ò¼È½ºÄù¾î','url'=>'http://www.socialsquare.co.kr');

// ¼Ò¼È½ºÄù¾î ºä
class social_meta_view_socialsquare extends social_meta_view {

	var $data = array();

	function social_meta_view_socialsquare() {
		$this->goods_only = 'A';
		$this->encode = 'UTF-8';
	}

	function make( $data = array() ) {

		$this->cfg = $this->encode($this->cfg);

		$_xml  = '';
		$_xml .= '<?xml version=\'1.0\' encoding=\'utf-8\'?>';
		$_xml .= '<products>';


		$keys = array_keys($data);
		for($i=0,$max=sizeof($keys);$i<$max;$i++) {

			$row = $data[ $keys[$i] ];

			$row = $this->encode($row);

			$_xml .= '
			<product>
				<meta_id><![CDATA['.$row['tgsno'].']]></meta_id>
				<name><![CDATA['.$row['goodsnm'].']]></name>
				<url><![CDATA['.$row['url'].']]></url>
				<kind><![CDATA[]]></kind>
				<region><![CDATA['.$row['area'].']]></region>
				<image><![CDATA['.$row['image'].']]></image>
				<original>'.$row['consumer'].'</original>
				<price>'.$row['price'].'</price>
				<min_count>'.$row['limit_ea'].'</min_count>
				<max_count>'.$row['maxstock'].'</max_count>
				<now_count>'.$row['buyercnt'].'</now_count>
				<sold>0</sold>
				<end_at><![CDATA['.$row['enddt'].']]></end_at>
				<addr><![CDATA['.$row['usable_spot_address'].' '.$row['usable_spot_address_ext'].']]></addr>
				<lati></lati>
				<long></long>
				<blog><![CDATA['.$row['shortdesc'].']]></blog>
			</product>
			';

		}	// for


		$_xml .= '</products>';

		return $_xml;

	}
}
?>