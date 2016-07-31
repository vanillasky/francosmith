<?
$_meta_site_info['tcho'] = array('name'=>'티켓초이스','url'=>'http://www.tcho.co.kr');

// 티켓초이스 뷰
class social_meta_view_tcho extends social_meta_view {

	var $data = array();

	function social_meta_view_tcho() {
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

			$tmp = explode(' ',$row['usable_spot_address']);
			$row['area'] = array_shift($tmp);
			$row['area2'] = array_shift($tmp);
			if ($row['area'] == '충남' || $row['area'] == '충북') $row['area'] = '충청';

			switch($row['usable_spot_type']) {
				case '맛집,식품':
					$row['usable_spot_type'] = '맛집/외식';
					break;
				case '패션,뷰티':
					$row['usable_spot_type'] = '뷰티/미용';
					break;
				case '스포츠,레저':
					$row['usable_spot_type'] = '여가/스포츠';
					break;
				case '자동차':
				case '출산,유아동':
				case '디지털,가전':
				case '가구,침구':
				case '생활,건강':
					$row['usable_spot_type'] = '생활/건강';
					break;
				case '도서,문화,취미':
					$row['usable_spot_type'] = '공연/문화';
					break;
				case '여행,서비스':
					$row['usable_spot_type'] = '여행/레저';
					break;
				default:
					$row['usable_spot_type'] = '기타';
					break;
			}

			// 상시 판매 일때 (=startdt, enddt 가 null 일때)
			if ($row['startdt'] == null && $row['enddt'] == null) {
				$row['startdt'] = $row['updatedt'];
				$row['enddt'] = date('Y-m-d H:i:s', time() + 2592000);
			}

			$row = $this->encode($row);

			$_xml .= '
			<product>
				<prod_id>'.$row['tgsno'].'</prod_id>
				<name><![CDATA['.$row['goodsnm'].']]></name>
				<url><![CDATA['.$row['url'].']]></url>
				<descript><![CDATA['.$row['shortdesc'].']]></descript>
				<image><![CDATA['.$row['image'].']]></image>
				<startdate><![CDATA['.$row['startdt'].']]></startdate>
				<enddate><![CDATA['.$row['enddt'].']]></enddate>
				<ticketstartdate><![CDATA['.$row['usestartdt'].']]></ticketstartdate>
				<ticketenddate><![CDATA['.$row['useenddt'].']]></ticketenddate>
				<price><![CDATA['.$row['consumer'].']]></price>
				<saleprice><![CDATA['.$row['price'].']]></saleprice>
				<salerate><![CDATA['.$row['dc_rate'].']]></salerate>
				<mincnt><![CDATA['.$row['limit_ea'].']]></mincnt>
				<maxcnt><![CDATA['.$row['maxstock'].']]></maxcnt>
				<salecnt><![CDATA['.$row['buyercnt'].']]></salecnt>
				<shop_name><![CDATA['.$row['usable_spot_name'].']]></shop_name>
				<shop_tel><![CDATA['.$row['usable_spot_phone'].']]></shop_tel>
				<shop_address><![CDATA['.$row['usable_spot_address'].' '.$row['usable_spot_address_ext'].']]></shop_address>
				<region1><![CDATA['.$row['area'].']]></region1>
				<region2><![CDATA['.$row['area2'].']]></region2>
				<category><![CDATA['.$row['usable_spot_type'].']]></category>
				<mobilesupportpayment>N</mobilesupportpayment>
			</product>
			';

		}	// for


		$_xml .= '</products>';

		return $_xml;

	}
}
?>