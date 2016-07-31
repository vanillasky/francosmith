<?
$_meta_site_info['socury'] = array('name'=>'家捻府','url'=>'http://www.socury.com');

// 家捻府 轰
class social_meta_view_socury extends social_meta_view {

	var $data = array();

	function social_meta_view_socury() {
		$this->goods_only = 'A';
		$this->encode = 'UTF-8';
	}

	function make( $data = array() ) {

		$this->cfg = $this->encode($this->cfg);

		$_xml  = '';
		$_xml .= '<?xml version=\'1.0\' encoding=\'utf-8\'?>';
		$_xml .= '<items>';


		$keys = array_keys($data);
		for($i=0,$max=sizeof($keys);$i<$max;$i++) {

			$row = $data[ $keys[$i] ];

			$row = $this->encode($row);

			$_xml .= '
			<item>
				<url><![CDATA['.$row['url'].']]></url>
				<category><![CDATA[]]></category>
				<address><![CDATA['.$row['usable_spot_address'].' '.$row['usable_spot_address_ext'].']]></address>
				<city><![CDATA['.$row['area'].']]></city>
				<venue><![CDATA[]]></venue>
				<location></location>
				<subject><![CDATA['.$row['goodsnm'].']]></subject>
				<image><![CDATA['.$row['image'].']]></image>
				<oc>'.$row['consumer'].'</oc>
				<dc>'.$row['price'].'</dc>
				<dcrate>'.$row['dc_rate'].'</dcrate>
				<mincnt>'.$row['limit_ea'].'</mincnt>
				<maxcnt>'.$row['maxstock'].'</maxcnt>
				<salecnt>'.$row['buyercnt'].'</salecnt>
				<startdate><![CDATA['.$row['startdt'].']]></startdate>
				<limitdate><![CDATA['.$row['enddt'].']]></limitdate>
				<expire><![CDATA['.$row['useenddt'].']]></expire>
			</item>
			';

		}	// for


		$_xml .= '</items>';

		return $_xml;

	}
}
?>