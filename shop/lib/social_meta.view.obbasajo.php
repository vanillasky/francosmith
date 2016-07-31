<?
$_meta_site_info['obbasajo'] = array('name'=>'¿Àºü»çÁà','url'=>'http://www.obbasajo.com');

// ¿Àºü»çÁà ºä
class social_meta_view_obbasajo extends social_meta_view {

	var $data = array();

	function social_meta_view_obbasajo() {
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
				<url><![CDATA['.$row['url'].']]></url>
				<logourl><![CDATA[]]></logourl>
				<mainimage><![CDATA['.$row['image'].']]></mainimage>
				<bigdeal><![CDATA[]]></bigdeal>
				<division><![CDATA[]]></division>
				<region><![CDATA['.$row['area'].']]></region>
				<name><![CDATA['.$row['goodsnm'].']]></name>
				<blogkey></blogkey>
				<sitename><![CDATA['.$this->cfg['shopName'].']]></sitename>
				<image1></image1>
				<image2></image2>
				<image3></image3>
				<descript><![CDATA['.$row['shortdesc'].']]></descript>
				<address><![CDATA['.$row['usable_spot_address'].' '.$row['usable_spot_address_ext'].']]></address>
				<price>'.$row['consumer'].'</price>
				<saleprice>'.$row['price'].'</saleprice>
				<salerate>'.$row['dc_rate'].'</salerate>
				<fullcount>'.$row['maxstock'].'</fullcount>
				<mincnt>'.$row['limit_ea'].'</mincnt>
				<maxcnt>'.$row['maxstock'].'</maxcnt>
				<salecnt>'.$row['buyercnt'].'</salecnt>
				<startdate><![CDATA['.$row['startdt'].']]></startdate>
				<enddate><![CDATA['.$row['enddt'].']]></enddate>
				<lng></lng>
				<lat></lat>
			</product>
			';

		}	// for


		$_xml .= '</products>';

		return $_xml;

	}
}
?>