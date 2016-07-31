<?
$_meta_site_info['banprice'] = array('name'=>'반가격닷컴','url'=>'http://www.banprice.co.kr');

// 반가격닷컴 뷰
class social_meta_view_banprice extends social_meta_view {

	var $data = array();

	function social_meta_view_banprice() {
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
		$_xml .= '<noticeTitle></noticeTitle>';
		$_xml .= '<noticeHtml></noticeHtml>';
		$_xml .= '<eventTitle></eventTitle>';
		$_xml .= '<eventHtml></eventHtml>';
		$_xml .= '<eventLink></eventLink>';

		$keys = array_keys($data);
		for($i=0,$max=sizeof($keys);$i<$max;$i++) {

			$row = $data[ $keys[$i] ];

			//카테고리 코드값 입력 01(맛집),(식품),(패션),04(뷰티),(디지털) ,06(가전),(유아동),08(출산),(생활),10(건강),(문화),12(공연),(여행),(레저)
			switch($row['usable_spot_type']) {
				case '맛집,식품':
					$row['usable_spot_type'] = '01';
					break;
				case '패션,뷰티':
					$row['usable_spot_type'] = '03';
					break;
				case '생활,건강':
					$row['usable_spot_type'] = '09';
					break;
				case '출산,유아동':
					$row['usable_spot_type'] = '07';
					break;
				case '디지털,가전':
					$row['usable_spot_type'] = '05';
					break;
				case '스포츠,레저':
					$row['usable_spot_type'] = '14';
					break;
				case '여행,서비스':
					$row['usable_spot_type'] = '13';
					break;
				case '도서,문화,취미':
					$row['usable_spot_type'] = '11';
					break;
				case '자동차':
					$row['usable_spot_type'] = '09';
					break;
				case '가구,침구':
					$row['usable_spot_type'] = '09';
					break;
				default:
					$row['usable_spot_type'] = '';
					break;
			}


			$row = $this->encode($row);

			$_xml .= '
			<item>
			  <title><![CDATA['.$row['goodsnm'].']]></title>
			  <pricePub>'.$row['consumer'].'</pricePub>
			  <priceSale>'.$row['price'].'</priceSale>
			  <discount>'.$row['dc_rate'].'</discount>
			  <link><![CDATA['.$row['url'].']]></link>
			  <startDt><![CDATA['.$row['startdt'].']]></startDt>
			  <endDt><![CDATA['.$row['enddt'].']]></endDt>
			  <nowCount>'.$row['buyercnt'].'</nowCount>
			  <nowStock>'.$row['maxstock'].'</nowStock>
			  <category><![CDATA['.$row['usable_spot_type'].']]></category>
			  <areaName><![CDATA['.$row['area'].']]></areaName>
			  <description><![CDATA['.$row['shortdesc'].']]></description>
			  <descriptionHtml><![CDATA[<P>'.$row['shortdesc'].'</P>]]></descriptionHtml>
			  <movieLink></movieLink>
			  <keyword></keyword>
			  <talkLink></talkLink>
			  <status>1</status>
			  <sendStartDt></sendStartDt>
			  <sendEndDt></sendEndDt>
			  <pubDate></pubDate>
			</item>
			';

		}	// for


		$_xml .= '</channel>';
		$_xml .= '</rss>';

		return $_xml;

	}
}
?>