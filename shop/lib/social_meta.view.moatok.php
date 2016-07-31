<?
$_meta_site_info['moatok'] = array('name'=>'모아톡','url'=>'http://moatok.com');

// 모아톡 뷰
class social_meta_view_moatok extends social_meta_view {

	var $data = array();

	function social_meta_view_moatok() {
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

			if ($row['usable_spot_type'] == '여행,서비스')
				$row['usable_spot_type'] = '여행';
			else
				$row['usable_spot_type'] = '이벤트';


			if ($row['goodstype'] == 'goods') {
				$row['usable_spot_address'] = '전국배송';
				$row['usable_spot_address_ext'] = '';
			}

			$row = $this->encode($row);

			$_xml .= '
			<item>
				<product_code><![CDATA['.$row['tgsno'].']]></product_code>
				<category><![CDATA['.$row['usable_spot_type'].']]></category>
				<addr><![CDATA['.$row['usable_spot_address'].' '.$row['usable_spot_address_ext'].']]></addr>
				<title><![CDATA['.$row['goodsnm'].']]></title>
				<time_start><![CDATA['.$row['startdt'].']]></time_start>
				<time_end><![CDATA['.$row['enddt'].']]></time_end>
				<price_pub>'.$row['consumer'].'</price_pub>
				<price_sale>'.$row['price'].'</price_sale>
				<sale>'.$row['dc_rate'].'</sale>
				<link><![CDATA['.$row['url'].']]></link>
				<description><![CDATA['.$row['shortdesc'].']]></description>
				<cnt_sale>'.$row['buyercnt'].'</cnt_sale>
				<cnt_min>'.$row['limit_ea'].'</cnt_min>
				<cnt_max>'.$row['maxstock'].'</cnt_max>
				<pic_small><![CDATA['.$row['image'].']]></pic_small>
				<pic_1><![CDATA[]]></pic_1>
				<pic_2><![CDATA[]]></pic_2>
				<lng></lng>
				<lat></lat>
			</item>
			';

		}	// for


		$_xml .= '</channel>';
		$_xml .= '</rss>';

		return $_xml;

	}
}
?>