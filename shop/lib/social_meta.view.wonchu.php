<?
$_meta_site_info['wonchu'] = array('name'=>'¿øÃò','url'=>'http://www.wonchu.com');

// ¿øÃò ºä
class social_meta_view_wonchu extends social_meta_view {

	var $data = array();

	function social_meta_view_wonchu() {
		$this->goods_only = 'A';
		$this->encode = 'UTF-8';
	}

	function make( $data = array() ) {

		$this->cfg = $this->encode($this->cfg);

		$_xml  = '';
		$_xml .= '<?xml version=\'1.0\' encoding=\'utf-8\'?>';
		$_xml .= '<mall>';


		$keys = array_keys($data);
		for($i=0,$max=sizeof($keys);$i<$max;$i++) {

			$row = $data[ $keys[$i] ];

			$row['enddt'] = date('Y-m-d', strtotime($row['enddt']));

			$row = $this->encode($row);

			$_xml .= '
			<item>
				<name><![CDATA['.$row['goodsnm'].']]></name>
				<desc><![CDATA['.$row['shortdesc'].']]></desc>
				<oprice>'.$row['consumer'].'</oprice>
				<dprice>'.$row['price'].'</dprice>
				<edate><![CDATA['.$row['enddt'].']]></edate>
				<image><![CDATA['.$row['image'].']]></image>
				<oimage><![CDATA['.$row['image'].']]></oimage>
				<url><![CDATA['.$row['url'].']]></url>
				<min>'.$row['limit_ea'].'</min>
				<max>'.$row['maxstock'].'</max>
				<now>'.$row['buyercnt'].'</now>
				<pos><![CDATA['.$row['area'].']]></pos>
				<addr><![CDATA['.$row['usable_spot_address'].' '.$row['usable_spot_address_ext'].']]></addr>
				<exp><![CDATA[]]></exp>
				<state>1</state>
				<lat></lat>
				<lng></lng>
				<cate><![CDATA[]]></cate>
			</item>
			';

		}	// for


		$_xml .= '</mall>';

		return $_xml;

	}
}
?>