<?
return;
$_meta_site_info['dahalin'] = array('name'=>'다할인','url'=>'http://www.dahalin.co.kr/');

// 다할인 뷰
class social_meta_view_dahalin extends social_meta_view {

	var $data = array();

	function social_meta_view_dahalin() {
		$this->goods_only = 'A';
		$this->encode = 'UTF-8';
	}

	function make( $data = array() ) {

		$this->cfg = $this->encode($this->cfg);

		$_xml  = '';
		$_xml .= '<rss version=\'2.0\'>';
		$_xml .= '<channel>';


		$keys = array_keys($data);
		for($i=0,$max=sizeof($keys);$i<$max;$i++) {

			$row = $data[ $keys[$i] ];

			switch($row['usable_spot_type']) {
				case '맛집,식품':
					$row['usable_spot_type'] = 'EZ';
					break;
				case '패션,뷰티':
					$row['usable_spot_type'] = 'DZ';
					break;
				case '생활,건강':
					$row['usable_spot_type'] = 'FZ';
					break;
				case '출산,유아동':
					$row['usable_spot_type'] = 'AZ';
					break;
				case '디지털,가전':
					$row['usable_spot_type'] = 'CZ';
					break;
				case '스포츠,레저':
				case '여행,서비스':
					$row['usable_spot_type'] = 'HZ';
					break;
				case '도서,문화,취미':
				case '자동차':
				case '가구,침구':
				default:
					$row['usable_spot_type'] = 'ZZ';
					break;
			}


			$row = $this->encode($row);

			$_xml .= '
			<item>
				<guid><![CDATA['.$row['tgsno'].']]></guid>
				<link><![CDATA['.$row['url'].']]></link>
				<title><![CDATA[['.$row['goodsnm'].']]></title>
				<subtitle><![CDATA['.$row['goodsnm'].']]></subtitle>
				<description><![CDATA['.$row['shortdesc'].']]></description>
				<category><![CDATA['.$row['usable_spot_type'].']]></category>
				<minCnt><![CDATA['.$row['limit_ea'].']]></minCnt>
				<maxCnt><![CDATA['.$row['maxstock'].']]></maxCnt>
				<curCnt><![CDATA['.$row['buyercnt'].']]></curCnt>
				<pubDate><![CDATA['.$row['regdt'].']]></pubDate>
				<image><![CDATA['.$row['image'].']]></image>
				<price><![CDATA['.$row['consumer'].']]></price>
				<dcPrice><![CDATA['.$row['price'].']]></dcPrice>
				<dcRate><![CDATA['.$row['dc_rate'].']]></dcRate>
				<dcInfo></dcInfo>
				<shipFree>'.(($row['goodstype'] != 'coupon' && $row['delivery_type'] != 1) ? 'N' : 'Y').'</shipFree>
				<itemInfo></itemInfo>
				<begin><![CDATA['.$row['startdt'].']]></begin>
				<end><![CDATA['.$row['enddt'].']]></end>
				<update><![CDATA['.$row['updatedt'].']]></update>
			</item>
			';

		}	// for

		$_xml .= '</channel>';
		$_xml .= '</rss>';

		return $_xml;

	}

}

?>