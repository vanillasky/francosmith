<?
$_meta_site_info['deals'] = array('name'=>'具饶家既目赣胶','url'=>'http://deals.yahoo.co.kr');

// 具饶家既目赣胶 轰
class social_meta_view_deals extends social_meta_view {

	var $data = array();

	function social_meta_view_deals() {
		$this->goods_only = 'A';
		$this->encode = 'UTF-8';
	}

	function make( $data = array() ) {

		$this->cfg = $this->encode($this->cfg);

		$_xml  = '';
		$_xml .= '<?xml version=\'1.0\' encoding=\'utf-8\'?>';
		$_xml .= '<channel>';
		$_xml .= '<company><![CDATA['.$this->cfg['shopName'].']]></company>';
		$_xml .= '<link><![CDATA[http://'.$this->cfg['shopUrl'].']]></link>';
		$_xml .= '<desc><![CDATA[]]></desc>';
		$_xml .= '<help><![CDATA['.$row['usable_spot_phone'].']]></help>';
		$_xml .= '<items>';

		$keys = array_keys($data);
		for($i=0,$max=sizeof($keys);$i<$max;$i++) {

			$row = $data[ $keys[$i] ];

			$row = $this->encode($row);

			$row['startdt'] = preg_replace('/[^0-9]/','',$row['startdt']);
			$row['enddt'] = preg_replace('/[^0-9]/','',$row['enddt']);

			$_xml .= '
			<item>
				<target_url><![CDATA['.$row['url'].']]></target_url>
				<image><![CDATA['.$row['image'].']]></image>
				<subject><![CDATA['.$row['goodsnm'].']]></subject>
				<memo><![CDATA['.$row['shortdesc'].']]></memo>
				<category></category>
				<region></region>
				<price>'.$row['price'].'</price>
				<ori_price>'.$row['consumer'].'</ori_price>
				<discount><![CDATA['.$row['dc_rate'].']]></discount>
				<start_date><![CDATA['.$row['startdt'].']]></start_date>
				<end_date><![CDATA['.$row['enddt'].']]></end_date>
				<svc_name><![CDATA['.$this->cfg['shopName'].']]></svc_name>
				<svc_addr><![CDATA['.$row['usable_spot_address'].' '.$row['usable_spot_address_ext'].']]></svc_addr>
				<end_yn>N</end_yn>
				<min>'.$row['limit_ea'].'</min>
				<max>'.$row['maxstock'].'</max>
				<sale>'.$row['buyercnt'].'</sale>
				<expire><![CDATA['.$row['useenddt'].']]></expire>
				<holiday_yn></holiday_yn>
				<park_yn></park_yn>
				<reply_count></reply_count>
			</item>
			';

		}	// for


		$_xml .= '</items>';
		$_xml .= '</channel>';

		return $_xml;

	}
}
?>