<?
$_meta_site_info['socialcoms'] = array('name'=>'소셜커머스닷컴','url'=>'http://www.socialcoms.com/');

// 소셜커머스닷컴 뷰
class social_meta_view_socialcoms extends social_meta_view {

	var $data = array();

	function social_meta_view_socialcoms() {
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

			switch($row['usable_spot_type']) {
				case '맛집,식품':
					$row['usable_spot_type'] = '맛집';
					break;
				case '패션,뷰티':
				case '자동차':
				case '스포츠,레저':
				case '가구,침구':
				case '생활,건강':
				case '출산,유아동':
				case '디지털,가전':
				case '도서,문화,취미':
					$row['usable_spot_type'] = '뷰티/생활';
					break;
				case '여행,서비스':
					$row['usable_spot_type'] = '여행/레져';
					break;
				default:
					$row['usable_spot_type'] = '기타';
					break;
			}

			if ($row['area'] == '서울') {

				// 강남 or 강북
				$_gu = array_pop(explode(" ",$row['address1']));


			}
			else if (preg_match('/(경기|인천|대전|대구|부산|광주)/',$row['area'])) {

			}
			else {
				$row['area'] = '기타';

			}

			$row = $this->encode($row);

			$_xml .= '
			<product>
				<url>'.$row['url'].'</url>
				<division>'.$row['usable_spot_type'].'</division>
				<region>'.$row['area'].'</region>
				<name>'.$row['goodsnm'].'</name>
				<image1>'.$row['image'].'</image1>
				<image2>'.$row['image'].'</image2>
				<image3>'.$row['image'].'</image3>
				<descript>'.$row['shortdesc'].'</descript>
				<address>'.$row['address1'].' '.$row['address2'].' '.$row['address3'].'</address>
				<price>'.$row['consumer'].'</price>
				<saleprice>'.$row['price'].'</saleprice>
				<salerate>'.$row['dc_rate'].'</salerate>
				<fullcount>'.$row['maxstock'].'</fullcount>
				<mincnt>'.$row['limit_ea'].'</mincnt>
				<maxcnt>'.$row['stock'].'</maxcnt>
				<salecnt>'.$row['buyercnt'].'</salecnt>
				<limitdate>'.$row['enddt'].'</limitdate>
				<lng></lng>
				<lat></lat>
			</product>
			';

		}	// for


		$_xml .= '</products>';

		return $_xml;

	}

}

?>