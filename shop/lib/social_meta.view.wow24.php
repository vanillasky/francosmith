<?
$_meta_site_info['wow24'] = array('name'=>'客快24','url'=>'http://www.wow24.net');

// 客快24 轰
class social_meta_view_wow24 extends social_meta_view {

	var $data = array();

	function social_meta_view_wow24() {
		$this->goods_only = 'A';
		$this->encode = 'UTF-8';
	}

	function make( $data = array() ) {

		$this->cfg = $this->encode($this->cfg);

		$_xml  = '';
		$_xml .= '<?xml version=\'1.0\' encoding=\'utf-8\'?>';
		$_xml .= '<deals>';


		$keys = array_keys($data);
		for($i=0,$max=sizeof($keys);$i<$max;$i++) {

			$row = $data[ $keys[$i] ];

			$row['status'] = '魄概';
			$row['event'] = '固柳青';

			$row['startdt'] = date('m岿 d老 H:i',strtotime($row['startdt']));
			$row['enddt'] = date('m岿 d老 H:i',strtotime($row['enddt']));


			$row = $this->encode($row);

			$_xml .= '
			<item>
				<image><![CDATA['.$row['image'].']]></image>
				<name><![CDATA['.$this->cfg['shopName'].']]></name>
				<url><![CDATA['.$row['url'].']]></url>
				<title><![CDATA['.$row['goodsnm'].']]></title>
				<original><![CDATA['.$row['consumer'].']]></original>
				<price><![CDATA['.$row['price'].']]></price>
				<discount><![CDATA['.$row['dc_rate'].']]></discount>
				<start_at><![CDATA['.$row['startdt'].']]></start_at>
				<end_at><![CDATA['.$row['enddt'].']]></end_at>
				<min_count><![CDATA['.$row['limit_ea'].']]></min_count>
				<max_count><![CDATA['.$row['maxstock'].']]></max_count>
				<now_count><![CDATA[0]]></now_count>
				<category><![CDATA['.$row['area'].']]></category>
				<event><![CDATA['.$row['event'].']]></event>
				<status><![CDATA['.$row['status'].']]></status>
				<address><![CDATA['.$row['usable_spot_address'].' '.$row['usable_spot_address_ext'].']]></address>
			</item>
			';

		}	// for


		$_xml .= '</deals>';

		return $_xml;

	}
}
?>