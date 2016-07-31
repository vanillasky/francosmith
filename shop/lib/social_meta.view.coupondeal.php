<?
$_meta_site_info['coupondeal'] = array('name'=>'쿠폰딜','url'=>'http://www.coupondeal.co.kr');

// 쿠폰딜 뷰
class social_meta_view_coupondeal extends social_meta_view {

	var $data = array();

	function social_meta_view_coupondeal() {
		$this->goods_only = 'A';
		$this->encode = 'UTF-8';
	}

	function make( $data = array() ) {

		$this->cfg = $this->encode($this->cfg);

		$_xml  = '';
		$_xml .= '<?xml version=\'1.0\' encoding=\'utf-8\'?>';
		$_xml .= '<rss version=\'2.0\' xmlns:dc=\'http://purl.org/dc/elements/1.1/\'>';
		$_xml .= '<channel>';
		$_xml .= '<title><![CDATA['.$this->cfg['shopName'].']]></title>';
		$_xml .= '<link><![CDATA[http://'.$this->cfg['shopUrl'].']]></link>';
		$_xml .= '<description></description>';


		$keys = array_keys($data);
		for($i=0,$max=sizeof($keys);$i<$max;$i++) {

			$row = $data[ $keys[$i] ];

			switch($row['usable_spot_type']) {
				case '맛집,식품':
					$row['usable_spot_type'] = '맛집/식품';
					break;
				case '패션,뷰티':
					$row['usable_spot_type'] = '패션/뷰티';
					break;
				case '생활,건강':
					$row['usable_spot_type'] = '건강/생활';
					break;
				case '출산,유아동':
					$row['usable_spot_type'] = '출산/유아';
					break;
				case '디지털,가전':
					$row['usable_spot_type'] = '가전/디지털';
					break;
				case '스포츠,레저':
					$row['usable_spot_type'] = '스포츠/레저';
					break;
				case '여행,서비스':
					$row['usable_spot_type'] = '여행/서비스';
					break;
				case '도서,문화,취미':
					$row['usable_spot_type'] = '도서/문화/취미';
					break;
				case '자동차':
					$row['usable_spot_type'] = '차량/용품';
					break;
				case '가구,침구':
					$row['usable_spot_type'] = '가구/침구';
					break;
				default:
					$row['usable_spot_type'] = '기타';
					break;
			}

			$row = $this->encode($row);

			$_xml .= '
			<item>
				<title><![CDATA['.$row['goodsnm'].']]></title>
				<link><![CDATA['.$row['url'].']]></link>
				<description><![CDATA['.$row['shortdesc'].']]></description>
				<categoryname><![CDATA['.$row['usable_spot_type'].']]></categoryname>
				<areaname><![CDATA['.$row['area'].']]></areaname>
				<address><![CDATA['.$row['usable_spot_address'].' '.$row['usable_spot_address_ext'].']]></address>
				<image><![CDATA['.$row['image'].']]></image>
				<originprice>'.$row['consumer'].'</originprice>
				<dcprice>'.$row['price'].'</dcprice>
				<dcpercent>'.$row['dc_rate'].'</dcpercent>
				<mincount>'.$row['limit_ea'].'</mincount>
				<maxcount>'.$row['maxstock'].'</maxcount>
				<curcount>'.$row['buyercnt'].'</curcount>
				<close>'.$row['buyercnt'].'</close>
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