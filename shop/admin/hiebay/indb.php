<?
	@include "../lib.php";
	$ignoreToken = true;
	include "./checker.php";
	@include dirname(__FILE__)."/../../conf/config.php";

	$file = dirname(__FILE__)."/../../conf/godomall.cfg.php";
	$file = file($file);
	$godo = decode($file[1],1);

	$goodsno = ($_GET['goodsno']) ? trim($_GET['goodsno']) : "";
	list($requrl) = $db->fetch("SELECT value FROM ".GD_ENV." WHERE category = 'forseller' AND name = 'requrl' LIMIT 1");

	// 이 페이지에서 사용할 사용자 함수 S
		if(!function_exists('json_decode')){
			function json_decode($content, $assoc=false) {
				require_once '../../lib/json.class.php';

				if($assoc) $json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
				else $json = new Services_JSON;

				return $json->decode($content);
			}
		}

		function debug3($data) {
			print "<xmp style=\"font:9pt 'Courier New';background:#000000;color:#00ff00;padding:10\">";
			print_r($data);
			print "</xmp>";
		}

		function http_build_query2($data, $prefix='', $sep='', $key='') {
			$ret = array();
			foreach ((array)$data as $k => $v) {
				if (is_int($k) && $prefix != null) {
					$k = urlencode($prefix . $k);
				}
				if ((!empty($key)) || ($key === 0))  $k = $key.'['.urlencode($k).']';
				if (is_array($v) || is_object($v)) {
					array_push($ret, http_build_query2($v, '', $sep, $k));
				} else {
					array_push($ret, $k.'='.urlencode($v));
				}
			}
			if (empty($sep)) $sep = ini_get('arg_separator.output');
			return implode($sep, $ret);
		}

		function curl($p) {
			$curl=curl_init($p['url']);
			if (is_resource($curl)===true) {
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				if (strtolower($p['method'])==='post') {
					curl_setopt($curl, CURLOPT_POST, true);
					curl_setopt($curl, CURLOPT_POSTFIELDS, (is_array($p['params'])===true) ? http_build_query2($p['params'], '', '&') : $p['params']);
				}
				$response=curl_exec($curl);
				curl_close($curl);
			}
			return $response;
		}

		// 현 쇼핑몰의 상품 이미지의 URL 생성
		function fullUrlImg($imgVal) {
			global $_SERVER, $cfg;

			$temp_new_ar = array();
			$temp_ar = explode("|", $imgVal);

			for($i = 0, $imax = count($temp_ar); $i < $imax; $i++) {
				if($temp_ar[$i] && !preg_match("/^http\:\/\//", $temp_ar[$i])) $temp_new_ar[] = "http://".$_SERVER["HTTP_HOST"].$cfg['rootDir']."/data/goods/".$temp_ar[$i];
				else $temp_new_ar[] = $temp_ar[$i];
			}

			return implode("|", $temp_new_ar);
		}

		function fullUrlInDesc($desc) {
			global $_SERVER, $cfg;

			$desc = str_replace("<img", "<img", $desc);
			$desc = str_replace("<IMG", "<img", $desc);
			if(preg_match("/<img src=\"".str_replace("/", "\/", $cfg['rootDir'])."/", $desc)) {
				$desc = str_replace('<img src="'.$cfg['rootDir'], '<img src="'."http://".$_SERVER["HTTP_HOST"].$cfg['rootDir'], $desc);
			}

			return $desc;
		}

		function fsProc($val) {
			return urlencode(iconv("EUC-KR", "UTF-8", $val));
		}
	// 이 페이지에서 사용할 사용자 함수 S

	if(!$goodsno) {
		echo "1||상품번호가 전송되지 않았습니다.||";
	}
	else {
		// make query
			$sql = "SELECT * FROM ".GD_GOODS." WHERE goodsno = '".$_GET['goodsno']."'";
			$rs = $db->query($sql);
			$row = $db->fetch($rs);

		// make category
			list($tmpCate) = $db->fetch("SELECT category FROM ".GD_GOODS_LINK." WHERE goodsno = '".$row['goodsno']."' ORDER BY LENGTH(category) DESC, sno ASC"); // 카테고리 번호
			if($tmpCate) for($i = 3, $imax = strlen($tmpCate); $i <= $imax; $i = $i + 3) {
				if($tmpWhere) $tmpWhere .= " OR";
				$tmpWhere .= " category = '".substr($tmpCate, 0, $i)."'";
			}
			if($tmpWhere) $tmpWhere = " (".$tmpWhere." ) ";
			$rs_cate = $db->query("SELECT catnm FROM ".GD_CATEGORY." WHERE $tmpWhere ORDER BY LENGTH(category) ASC");
			$row['goodscate'] = "";
			while($row_cate = $db->fetch($rs_cate)) { // 카테고리 이름 추출
				if($row['goodscate']) $row['goodscate'] = $row['goodscate'].iconv("EUC-KR", "UTF-8", ",");
				$row['goodscate'] .= fsProc($row_cate['catnm']);
			}

		list($row['brandnm']) = $db->fetch("SELECT brandnm FROM ".GD_GOODS_BRAND." WHERE sno = '".$row['brandno']."'"); // brand name

		// make option
			$row['opts'] = "";
			$rs_opt = $db->query("SELECT * FROM ".GD_GOODS_OPTION." WHERE goodsno = '".$row['goodsno']."'");
			while($row_opt = $db->fetch($rs_opt)) {
				if($row['opts']) $row['opts'] .= iconv("EUC-KR", "UTF-8", "|");
				$row['opts'] .= fsProc($row_opt['opt1']).iconv("EUC-KR", "UTF-8", "^").fsProc($row_opt['opt2']).iconv("EUC-KR", "UTF-8", "^").fsProc($row_opt['price']).iconv("EUC-KR", "UTF-8", "^").fsProc($row_opt['consumer']).iconv("EUC-KR", "UTF-8", "^").fsProc($row_opt['supply']).iconv("EUC-KR", "UTF-8", "^").fsProc($row_opt['reserve']).iconv("EUC-KR", "UTF-8", "^").fsProc($row_opt['stock']);
			}

		// make add option
			$row['addopts'] = "";
			$rs_add = $db->query("SELECT * FROM ".GD_GOODS_ADD." WHERE goodsno = '".$row['goodsno']."'");
			while($row_add = $db->fetch($rs_add)) {
				if($row['addopts']) $row['addopts'] .= "|";
				$row['addopts'] .= $row_add['opt'];
			}

		$items[$i] = array();

		$items[$i]['goodsno']		= fsProc($row['goodsno']);				//상품번호
		$items[$i]['goodsnm']		= fsProc($row['goodsnm']);				//상품명(필수)
		$items[$i]['goodscate']		= $row['goodscate'];					//상품분류(필수)
		$items[$i]['goodscd']		= fsProc($row['goodscd']);				//상품코드
		$items[$i]['origin']		= fsProc($row['origin']);				//원산지
		$items[$i]['maker']			= fsProc($row['maker']);				//제조사
		$items[$i]['brandnm']		= fsProc($row['brandnm']);				//브랜드번호
		$items[$i]['tax']			= fsProc($row['tax']);					//과세/비과세
		$items[$i]['delivery_type']	= fsProc($row['delivery_type']);		//배송정책
		$items[$i]['keyword']		= fsProc($row['keyword']);				//유사검색어
		$items[$i]['strprice']		= fsProc($row['strprice']);				//가격대체문구
		$items[$i]['shortdesc']		= fsProc($row['shortdesc']);			//짧은설명
		$items[$i]['longdesc']		= fsProc(fullUrlInDesc($row['longdesc']));				//상품설명
		$items[$i]['img_i']			= fsProc(fullUrlImg($row['img_i']));	//메인이미지
		$items[$i]['img_s']			= fsProc(fullUrlImg($row['img_s']));	//리스트이미지
		$items[$i]['img_m']			= fsProc(fullUrlImg($row['img_m']));	//상세이미지
		$items[$i]['img_l']			= fsProc(fullUrlImg($row['img_l']));	//확대이미지
		$items[$i]['memo']			= fsProc($row['memo']);					//관리 메모
		$items[$i]['regdt']			= fsProc(date('Y-m-d H:i:s'));			//등록일
		$items[$i]['open']			= fsProc($row['open']);					//상품출력여부
		$items[$i]['runout']		= fsProc($row['runout']);				//품절상품
		$items[$i]['usestock']		= fsProc($row['usestock']);				//재고량연동
		$items[$i]['opttype']		= fsProc($row['opttype']);				//옵션출력방식
		$items[$i]['optnm']			= fsProc($row['optnm']);				//가격/재고 옵션명 (Item Variations)
		$items[$i]['opts']			= $row['opts'];							//가격/재고 옵션목록 (Item Variations)
		$items[$i]['addoptnm']		= fsProc($row['addoptnm']);				//추가상품제목
		$items[$i]['addopts']		= fsProc($row['addopts']);				//추가상품목록
		$items[$i]['ex_title']		= fsProc($row['ex_title']);				//상품추가정보 제목 (Item Specifics)
		$items[$i]['ex1']			= fsProc($row['ex1']);					//상품추가정보1 (Item Specifics Value)
		$items[$i]['ex2']			= fsProc($row['ex2']);					//상품추가정보2 (Item Specifics Value)
		$items[$i]['ex3']			= fsProc($row['ex3']);					//상품추가정보3 (Item Specifics Value)
		$items[$i]['ex4']			= fsProc($row['ex4']);					//상품추가정보4 (Item Specifics Value)
		$items[$i]['ex5']			= fsProc($row['ex5']);					//상품추가정보5 (Item Specifics Value)
		$items[$i]['ex6']			= fsProc($row['ex6']);					//상품추가정보6 (Item Specifics Value)
		$items[$i]['relationis']	= fsProc($row['relationis']);			//관련상품방식
		$items[$i]['relation']		= fsProc($row['relation']);				//관련상품번호
		$items[$i]['meta_title']	= fsProc($row['meta_title']);			//타이틀태그설정

		if($_REQUEST['test']) {
			echo "<textarea style='width:100%; height:700px;'>items[".$goodsno."] = array(\n";
			foreach($items[$i] as $k => $v) echo "\t\"$k\" => \"".iconv("UTF-8", "EUC-KR", urldecode($v))."\"\n";
			echo ")</textarea>";
			exit();
		}

		$data = array(
			'url'		=> $fsConfig['apiUrl'].'/godo/godo-export',
			'method'	=> 'post', //전송 메소드
			'params'	=> array(
				'token'		=> $fsConfig['token'],
				'items'		=> $items

			)
		);
		$response = curl($data);
		$json_decode = json_decode($response);

		if($json_decode->Ack!='Success') { //eBay API Response
			echo "1||";
			echo iconv("UTF-8", "EUC-KR", urldecode($json_decode->Errors->LongMessage));
			echo "||";
			$db->query("INSERT INTO ".GD_GOODS_OPENMARKET." SET goodsno = '".$row['goodsno']."', rescode = '".$json_decode->Ack."', resmsg = '".iconv("UTF-8", "EUC-KR", urldecode($json_decode->Errors->LongMessage))."', requrl = '$requrl', regdt = NOW()");
		}
		else { //Success or Warning
			switch(trim($json_decode->Ack)) {
				case "Success" :
					echo "0||상품이 등록되었습니다.||".date("Y-m-d H:i:s");
					break;
				case "Warning" :
					echo "0||상품이 등록되었습니다.||".date("Y-m-d H:i:s");
					break;
			}

			$db->query("INSERT INTO ".GD_GOODS_OPENMARKET." SET goodsno = '".$row['goodsno']."', rescode = '".$json_decode->Ack."', requrl = '$requrl', regdt = NOW()");
		}
	}
?>
