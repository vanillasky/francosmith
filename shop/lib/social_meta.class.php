<?
class social_meta {

	var $auth = false;

	var $view;
	var $goods_only = 'A';	// A : 전체, C : 쿠폰, G : 실물
	var $url;
	var $cfg;

	var $sites = array();

	function social_meta() {

		global $cfg;

		$this->cfg = $cfg;

		$this->url = "http://".$cfg['shopUrl'].$cfg['rootDir'];

		$_file = dirname(__FILE__)."/../conf/godomall.cfg.php";

		if (is_file($_file)) {
			$_file = file($_file);
			$_godo = decode($_file[1],1);
		}

		$this->auth = (strtoupper($_godo['todayshop']) == 'Y') ? true : false;

		// 데이터 뷰 클래스 파일 불러들이기
		$this->_include_view();

	}

	function _include_view() {
		$path = dirname(__FILE__);
		$d  = opendir($path);
		$_meta_site_info = array();
		while (false !== ($filename = readdir($d))) {
			if (preg_match('/^social_meta\.view\.[a-z]+\.php$/',$filename)) {
				include($filename);
			}
		}

		// 사이트 이름순 정렬
		$_sortkey = array();
		foreach($_meta_site_info as $k => $v) {
			$_sortkey[$k] = $v['name'];
		}

		array_multisort($_sortkey, SORT_ASC, $_meta_site_info);

		$this->sites = $_meta_site_info;

	}


	function set($metakey) {

		// 사용여부 체크



		$_view_name = 'social_meta_view_'.$metakey;

		if (class_exists($_view_name, false)) {
			$this->view = & new $_view_name;
			$this->view->cfg = $this->cfg;

		}
		else {
			$this->view = false;
		}

	}

	function make() {

		if ($this->view !== false) {
			return $this->view->make( $this->_get_goods() );
		}
		else {
			return '';
		}

	}


	function _get_goods() {

		global $db;

		$data = array();

		$query = "
		SELECT

			TG.goodsno, TG.tgsno, TG.startdt, TG.enddt, TG.sms, TG.limit_ea, TG.showtimer,  TG.buyercnt, TG.fakestock, TG.usestartdt, TG.useenddt , TG.goodstype,
			TG.usable_spot_name, TG.usable_spot_post, TG.usable_spot_address, TG.usable_spot_address_ext, TG.usable_spot_phone, TG.usable_spot_type,
			TG.goodsnm, TG.img_i, TG.img_s, TG.keyword, TG.shortdesc, TG.updatedt, TG.regdt, TG.usestock, TG.delivery_type,

			SUM(GO.stock) AS stock,

			GO.price, GO.consumer,

			GC.cp_name AS company_name,
			GC.cp_address_post AS company_address_post,
			GC.cp_address AS company_address,
			GC.cp_phone  AS company_phone,
			IF (
				COALESCE(TG.enddt,	 NOW()) < NOW() OR TG.runout=1,
				'y', 'n'
				) AS tgout

		FROM ".GD_TODAYSHOP_GOODS_MERGED." AS TG

		LEFT JOIN ".GD_GOODS_OPTION." AS GO ON TG.goodsno=GO.goodsno and go_is_deleted <> '1' and go_is_display = '1'

		LEFT JOIN ".GD_TODAYSHOP_COMPANY." AS GC ON TG.company = GC.cp_sno

		WHERE TG.visible=1
			AND COALESCE(TG.startdt, NOW()) <= NOW()
			AND COALESCE(TG.enddt,	 NOW()) >= NOW()
			AND (
				TG.usable_spot_name <> '' AND
				TG.usable_spot_post <> '' AND
				TG.usable_spot_address <> '' AND
				TG.usable_spot_address_ext <> '' AND
				TG.usable_spot_phone <> ''
			)

		GROUP BY TG.goodsno
		";

		$rs = $db->query($query);

		while ($row = $db->fetch($rs,1)) {

			if ($row['tgout'] == 'y') continue;

			$row['dc_rate'] = round(100 - ($row['price'] * 100 / $row['consumer']));

			$row['url'] = $this->url . '/todayshop/today_goods.php?tgsno=' . $row['tgsno'];
			$row['image'] = $row['img_i'] ? (preg_match('/^http:\/\//',$row['img_i']) ? $row['img_i'] : $this->url . '/data/goods/' . $row['img_i'] ) : '';

			// 사용처 주소 분리
			$_addresses = explode(" ",$row['usable_spot_address']);

			if (!empty($_addresses)) {
				$row['area'] = array_shift($_addresses);	// 첫번째가 지역임.
				$row['address1'] = $row['area'];
				do $row['address1'] .= ' '.array_shift($_addresses);
				while (preg_match('/(동|읍|면)$/',$row['address1']));
				$row['address2'] = array_shift($_addresses);
				$row['address3'] = implode(' ',$_addresses).' '.$row['usable_spot_address_ext'] ;
			}

			$row[maxstock] = ($row['usestock'] == 'o') ? ($row[stock] + $row[buyercnt]) : $row[stock];	// 최초 재고량 ( = 현 재고량 + 판매량(fakestock 더하기 전)
			$row[buyercnt] = $row[buyercnt] + $row[fakestock];	// 현재 판매량

			$data[] = $row;
		}

		return $data;
	}

}	// eof social_meta;


class social_meta_view {

	var $cfg;
	var $list_max;
	var $goods_only;
	var $encode = 'EUC-KR';

	function encode($var) {

		if ($this->encode == 'EUC-KR') return $var;

		if (is_array($var)) {

			foreach($var as $k=>$v) {
				$var[$k] =  iconv('EUC-KR','UTF-8',$v);
			}
		}
		else {
			$var = iconv('EUC-KR','UTF-8',$var);
		}

		return $var;
	}

	function get_area() {	// implements

	}


}
?>
