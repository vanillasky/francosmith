<?
$_meta_site_info['cost'] = array('name'=>'코스트','url'=>'http://www.cost.co.kr');

// 코스트 뷰
class social_meta_view_cost extends social_meta_view {

	var $data = array();

	function social_meta_view_cost() {
		$this->goods_only = 'A';
		$this->encode = 'UTF-8';
	}

	function make( $data = array() ) {

		$this->cfg = $this->encode($this->cfg);

		$_xml  = '';
		$_xml .= '<?xml version=\'1.0\' encoding=\'utf-8\'?>';
		$_xml .= '<dailycost>';


		$keys = array_keys($data);
		for($i=0,$max=sizeof($keys);$i<$max;$i++) {

			$row = $data[ $keys[$i] ];

			$row = $this->encode($row);

			$_xml .= '
			<saleitem>
				<saleid>'.$row['tgsno'].'</saleid>
				<title><![CDATA['.$row['goodsnm'].']]></title>
				<descript><![CDATA['.$row['shortdesc'].']]></descript>
				<areacode></areacode>
				<areatxt><![CDATA['.$row['area'].']]></areatxt>
				<category></category>
				<categorytxt><![CDATA[]]></categorytxt>
				<price>'.$row['consumer'].'</price>
				<dcprice>'.$row['price'].'</dcprice>
				<dcpercent>'.$row['dc_rate'].'</dcpercent>
				<imgurl><![CDATA['.$row['image'].']]></imgurl>
				<linkurl><![CDATA['.$row['url'].']]></linkurl>
				<enddate><![CDATA['.$row['enddt'].']]></enddate>
				<maxcnt>'.$row['maxstock'].'</maxcnt>
				<minicnt>'.$row['limit_ea'].'</minicnt>
				<salecnt>'.$row['buyercnt'].'</salecnt>
				<address><![CDATA['.$row['usable_spot_address'].' '.$row['usable_spot_address_ext'].']]></address>
				<phone><![CDATA['.$row['usable_spot_phone'].']]></phone>
			</saleitem>
			';

		}	// for


		$_xml .= '</dailycost>';

		return $_xml;

	}
}
?>