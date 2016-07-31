<?
$_meta_site_info['ticketone'] = array('name'=>'티켓원','url'=>'http://www.ticketone.co.kr');

// 티켓원 뷰
class social_meta_view_ticketone extends social_meta_view {

	var $data = array();

	function social_meta_view_ticketone() {
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
				<division><![CDATA[]]></division>
				<region><![CDATA['.$row['area'].']]></region>
				<name><![CDATA['.$row['goodsnm'].']]></name>
				<descript><![CDATA['.$row['shortdesc'].']]></descript>
				<address><![CDATA['.$row['usable_spot_address'].' '.$row['usable_spot_address_ext'].']]></address>
				<price>'.$row['consumer'].'</price>
				<saleprice>'.$row['price'].'</saleprice>
				<salerate>'.$row['dc_rate'].'</salerate>
				<fullcount>'.$row['maxstock'].'</fullcount>
				<mincnt>'.$row['limit_ea'].'</mincnt>
				<maxcnt>'.$row['maxstock'].'</maxcnt>
				<salecnt>'.$row['buyercnt'].'</salecnt>
				<limitdate><![CDATA['.$row['enddt'].']]></limitdate>
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