<?
$_meta_site_info['daoneday'] = array('name'=>'다원데이','url'=>'http://www.daoneday.com');

// 다원데이 뷰
class social_meta_view_daoneday extends social_meta_view {

	var $data = array();

	function social_meta_view_daoneday() {
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
				<url>'.$row['url'].'</url>
				<logourl />
				<sitename>'.$this->cfg['shopName'].'</sitename>
				<division></division>
				<region><![CDATA['.$row['area'].']]></region>
				<pid><![CDATA['.$row['tgsno'].']]></pid>
				<name><![CDATA['.$row['goodsnm'].']]></name>
				<image1><![CDATA['.$row['image'].']]></image1>
				<descript><![CDATA['.$row['shortdesc'].']]></descript>
				<address><![CDATA['.$row['usable_spot_address'].' '.$row['usable_spot_address_ext'].']]></address>
				<price><![CDATA['.$row['consumer'].']]></price>
				<saleprice><![CDATA['.$row['price'].']]></saleprice>
				<salerate><![CDATA['.$row['dc_rate'].']]></salerate>
				<fullcount></fullcount>
				<mincnt><![CDATA['.$row['limit_ea'].']]></mincnt>
				<maxcnt><![CDATA['.$row['maxstock'].']]></maxcnt>
				<salecnt><![CDATA['.$row['buyercnt'].']]></salecnt>
				<startdate><![CDATA['.$row['startdt'].']]></startdate>
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