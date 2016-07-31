<?
$_meta_site_info['dcdaily'] = array('name'=>'叼揪单老府','url'=>'http://www.dcdaily.co.kr');

// 叼揪单老府 轰
class social_meta_view_dcdaily extends social_meta_view {

	var $data = array();

	function social_meta_view_dcdaily() {
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
				<title><![CDATA['.$row['goodsnm'].']]></title>
				<link><![CDATA['.$row['url'].']]></link>
				<description><![CDATA['.$row['shortdesc'].']]></description>
				<categoryname><![CDATA[]]></categoryname>
				<areaname><![CDATA['.$row['area'].']]></areaname>
				<address><![CDATA['.$row['usable_spot_address'].' '.$row['usable_spot_address_ext'].']]></address>
				<image><![CDATA['.$row['image'].']]></image>
				<originprice>'.$row['consumer'].'</originprice>
				<dcprice>'.$row['price'].'</dcprice>
				<dcpercent>'.$row['dc_rate'].'</dcpercent>
				<mincount>'.$row['limit_ea'].'</mincount>
				<maxcount>'.$row['maxstock'].'</maxcount>
				<curcount>'.$row['buyercnt'].'</curcount>
				<close>0</close>
				<startdate><![CDATA['.$row['startdt'].']]></startdate>
				<enddate><![CDATA['.$row['enddt'].']]></enddate>
			</item>
			';

		}	// for


		$_xml .= '</channel>';
		$_xml .= '</rss>';

		return $_xml;

	}
}
?>