<?
/**
 * @Path : 샵루트/lib/smartSearch.class.php
 * @Description : 스마트 검색
 * @Since : 2012.01.04 WED
 */

define(_OPT_PIPE_ , '|^');	// 검색 조건을 붙일 pipe

// 스마트 검색
class smartSearch {

	// 기본 설정 값
	var $search = array();		// 설정 값
	var $category = "";			// 카테고리
	var $themeno = "";			// 테마 idx값
	var $searchList = array();	// 스마트 검색 옵션
	var $menuName = "";			// 더보기 옵션 이름
	var $searchID = "";			// 더보기 옵션 출력 종류
	var $ssMoreOption = "";		// 더보기 옵션 출력물
	var $tmpArray = array();	// 임시 배열

	var $errMsg = array();	// class 자체에서 처리 중 입력된 메세지

	// main - 변수 설정
	function smartSearch() {
		global $_GET, $db;

		$this->category = $_GET['category'];
		$this->searchList = array();

		if($_GET['category']) {
			$query = 'SELECT themeno FROM '.GD_CATEGORY.' WHERE category = \''.$this->category.'\'';
			list($this->themeno) = $db->fetch($query);

		}
		else $this->errMsg[] = "[function.smartSearch] 카테고리 값이 없습니다.";
	}


	// 에러 메세지 보기
	function history() {
		echo "<br />\n";

		if(!count($this->errMsg)) echo "[function.history] 저장된 에러 메세지가 없습니다.<br />\n";
		else for($i = 0, $imax = count($this->errMsg); $i < $imax; $i++) echo $this->errMsg[$i]."<br />\n";

		if($this->resCode || $this->resMsg) echo "(".$this->resCode.") ".$this->resMsg."<br />\n";
	}


	// 테마 데이터 로드
	function loadTheme() {
		global $db;

		if($this->themeno) $sql = "SELECT * FROM ".GD_GOODS_SMART_SEARCH." WHERE sno = '".$this->themeno."'";
		else $sql = "SELECT * FROM ".GD_GOODS_SMART_SEARCH." WHERE basic = 'y'";
		$rs = $db->query($sql);
		$data = $db->fetch($rs);
		if(!$data) { $this->errMsg[] = "[function.loadTheme] 설정된 테마 및 기본테마가 없습니다."; return false; }

		$this->convData($data); // 데이터 가공
		$this->callList($data); // 검색옵션 불러오기

		return $this->searchList;
	}


	function html_encode($str) {

		$str = smartSearch::html_decode($str);

		$from	= array('&',	'"',	'<',	'>',	'\'');
		$to		= array('&amp;','&quot;','&lt;','&gt;','&#039;');
		return str_replace($from, $to, $str);

	}

	function html_decode($str) {

		$from	= array('&',	'"',	'<',	'>',	'\'');
		$to		= array('&amp;','&quot;','&lt;','&gt;','&#039;');

		return str_replace($to, $from, $str);
	}

	// 테마의 데이터를 변수로 저장
	function convData($data) {
		global $db;

		if($data['maker']) $this->search['maker'] = $this->getThemeValueArray($data['maker'], _OPT_PIPE_);
		if($data['origin']) $this->search['origin'] = $this->getThemeValueArray($data['origin'], _OPT_PIPE_);
		if($data['brandno']) {
			$this->tmpArray = $this->getThemeValueArray($data['brandno'], _OPT_PIPE_);
			for($i = 0, $imax = count($this->tmpArray); $i < $imax; $i++) {
				list($brandnm) = $db->fetch("SELECT brandnm FROM ".GD_GOODS_BRAND." WHERE sno = '".$this->tmpArray[$i]."'");
				if($brandnm) $this->search['brand'][] = $brandnm;
			}
		}
		if($data['ex']) {
			$tmpArr1 = explode("\n", $data['ex']); // 추가옵션별로 나눔
			for($i =0 , $imax = count($tmpArr1); $i < $imax; $i++) {
				$tmpArr2 = explode(_OPT_PIPE_._OPT_PIPE_, $tmpArr1[$i]); // 추가옵션명과 값으로 나눔
				if($tmpArr2[0] && $tmpArr2[1]) $this->search['ex'][$tmpArr2[0]] = $this->getThemeValueArray($tmpArr2[1], _OPT_PIPE_);
			}
		}
		if($data['opt']) {
			$tmpArr1 = explode("\n", $data['opt']); // 추가옵션별로 나눔
			for($i =0 , $imax = count($tmpArr1); $i < $imax; $i++) {
				$tmpArr2 = explode(_OPT_PIPE_._OPT_PIPE_, $tmpArr1[$i]); // 추가옵션명과 값으로 나눔
				if($tmpArr2[0] && $tmpArr2[1]) $this->search['opt'][$tmpArr2[0]] = $this->getThemeValueArray($tmpArr2[1], _OPT_PIPE_);
			}
		}
	}


	// 리스트 불러오기
	function callList($data) {
		for($i = 0; $i < 3; $i++) $this->makeList(substr($data['ssOrder'], $i, 1), $data);
	}


	// 리스트 생성
	function makeList($type, $data) {
		global $_COOKIE, $_GET, $db;

		if (get_magic_quotes_gpc()) {
			stripslashes_all($_GET);
		}

		if(!$type) { $this->errMsg[] = "[function.makeList] type이 정의되지 않았습니다."; return false; }
		if(!$data) { $this->errMsg[] = "[function.makeList] data이 정의되지 않았습니다."; return false; }

		switch($type) {
			case "b" : // 상품 기본 정보 - basic

				// 가격
				if($data['price'] == 'y') {
				// 최대가격, 최소가격
					list($maxPrice, $minPrice) = $db->fetch("SELECT MAX(b.price), MIN(b.price) FROM ".GD_GOODS_LINK." AS a INNER JOIN ".GD_GOODS_OPTION." AS b ON a.goodsno = b.goodsno AND b.link = 1 and go_is_deleted <> '1' and go_is_display = '1' WHERE ".getCategoryLinkQuery('a.category', $this->category, 'where'));

					if(($minPrice || $minPrice == "0") && ($maxPrice || $maxPrice == "0") && $minPrice != $maxPrice) $this->searchList[] = array(
						'id' => 'price',
						'name' => '가격',
						'display' => (($_COOKIE[$this->category]['price']) ? 1 : 0),
						'min' => $minPrice,
						'max' => $maxPrice
					);
				}

				// 색상
				if($data['color'] == 'y') if($data['color']) {
					$this->searchList[] = array(
						'id' => 'color',
						'name' => '색상',
						'display' => (($_COOKIE[$this->category]['color']) ? 1 : 0),
						'color_useyn' => $data['color'],
						'colorList' => $this->getColor()
					);
				}

				// 제조사
				if($data['maker']) {

					if(is_array($_GET['maker'])) foreach($this->search['maker'] as $k2 => $v2) if(in_array($this->html_decode($v2), $_GET['maker'])) $chked['maker'][$k2] = " checked";


					$this->searchList[] = array(
						'id' => 'maker',
						'name' => '제조사',
						'display' => (($_COOKIE[$this->category]['maker']) ? 1 : 0),
						'list' => $this->search['maker'],
						'chked' => $chked['maker']
					);
				}

				// 원산지
				if(count($this->search['origin'])) {

					if(is_array($_GET['origin'])) foreach($this->search['origin'] as $k2 => $v2) if(in_array($this->html_decode($v2), $_GET['origin'])) $chked['origin'][$k2] = " checked";

					$this->searchList[] = array(
						'id' => 'origin',
						'name' => '원산지',
						'display' => (($_COOKIE[$this->category]['origin']) ? 1 : 0),
						'list' => $this->search['origin'],
						'chked' => $chked['origin']
					);
				}

				// 브랜드
				if(count($this->search['brand'])) {

					if(is_array($_GET['brand'])) foreach($this->tmpArray as $k2 => $v2) if(in_array($this->html_decode($v2), $_GET['brand'])) $chked['brand'][$k2] = " checked";

					$this->searchList[] = array(
						'id' => 'brand',
						'name' => '브랜드',
						'display' => (($_COOKIE[$this->category]['brand']) ? 1 : 0),
						'list' => $this->search['brand'],
						'valList' => $this->tmpArray,
						'chked' => $chked['brand']
					);
				}

				break;

			case "e" : // 상품 추가 정보 - extend fields

				$tmpNo = 0;
				if(is_array($this->search['ex'])) foreach($this->search['ex'] as $k => $v) {
					if(is_array($_GET['ex'.$tmpNo])) foreach($v as $k2 => $v2) if(in_array($this->html_decode($v2), $_GET['ex'.$tmpNo])) $chked['ex'.$tmpNo][$k2] = " checked";

					$this->searchList[] = array(
						'id' => 'ex'.$tmpNo,
						'name' => $this->html_encode($k),
						'display' => (($_COOKIE[$this->category]['ex'.$tmpNo]) ? 1 : 0),
						'list' => $v,
						'chked' => $chked['ex'.$tmpNo]
					);
					$tmpNo++;
				}

				break;

			case "o" : // 옵션 - option

				$tmpNo = 0;
				if(is_array($this->search['opt'])) foreach($this->search['opt'] as $k => $v) {
					if(is_array($_GET['opt'.$tmpNo])) foreach($v as $k2 => $v2) if(in_array($this->html_decode($v2), $_GET['opt'.$tmpNo])) $chked['opt'.$tmpNo][$k2] = " checked";

					$this->searchList[] = array(
						'id' => 'opt'.$tmpNo,
						'name' => $this->html_encode($k),
						'display' => (($_COOKIE[$this->category]['opt'.$tmpNo]) ? 1 : 0),
						'list' => $v,
						'chked' => $chked['opt'.$tmpNo]
					);
					$tmpNo++;
				}

				break;
		}
	}


	function getOption() {
		global $_GET, $db;

		$search = $this->search;

		if(!$this->searchID) {
			$this->errMsg[] = "[function.getOption] this->searchID 값이 없습니다.";
			return "목록을 불러오는데 오류가 발생했습니다.<br />페이지를 '새로고침' 후 다시 시도해 주세요.";
		}
		else {
			switch(strtolower(substr($this->searchID, 0, 2))) {
				case "op" :
					$k = $v = $search["opt"][$this->menuName];
					break;

				case "ex" :
					$k = $v = $search["ex"][$this->menuName];
					break;
				case "br" :
					$k = $this->tmpArray;
					$v = $search[$this->searchID];
					break;
				default :
					$k = $v = $search[$this->searchID];
					break;
			}

			$targetList = @array_combine($k , $v);
		}

		if(!is_array($targetList)) {
			$this->errMsg[] = "[function.getOption] search[this->searchID] 값이 없습니다.";
			return "목록을 불러오는데 오류가 발생했습니다.<br />페이지를 '새로고침' 후 다시 시도해 주세요.";
		}

		//sort($targetList);

		$tmpNo = 0;
		$this->ssMoreOption = '<ul>';
		foreach($targetList as $k => $v) {

			$_el_id = $this->searchID.'_more_'.$tmpNo;
			$_el_id2 = $this->searchID.'_'.$tmpNo;
			$_el_name = $this->searchID.'_more[]';

			$_el_label = $v;
			$_el_value = $k;

			$_el_checked = (is_array($_GET[$this->searchID]) && in_array(($_el_value), $_GET[$this->searchID])) ? " checked" : "";

			$this->ssMoreOption .= '
			<li>
			<label><input type="checkbox" name="'.$_el_name.'" id="'.$_el_id.'" value="'.$_el_value.'" '.$_el_checked.' onclick="ssCheckMoreOption(\''.$_el_id2.'\', this.checked);" />'.$_el_label.'</label>
			</li>
			';

			$tmpNo++;
		}
		$this->ssMoreOption .= '</ul>';

		return $this->ssMoreOption;
	}


	// 스마트 검색 쿼리
	function ssQuery() {
		global $_GET, $db;
		$rtnQuery = array();

		if(is_array($_GET)) {

			// 가격
			if($_GET['ssOriMinPrc'] != $_GET['ssMinPrice'] || $_GET['ssOriMaxPrc'] != $_GET['ssMaxPrice']) $rtnQuery[] = sprintf(' goods.goods_price BETWEEN %s AND %s', $db->_escape($_GET['ssMinPrice']),  $db->_escape($_GET['ssMaxPrice']) );

			foreach($_GET as $k => $v) {
				$tmpArr[$k] = array();

				// 추가 옵션
				if(preg_match("/^ex/", $k)) {
					// 선택된 추가옵션들을 하나로 정리
					for($i = 0, $imax = count($v); $i < $imax; $i++) $tmpArr[$k]['word'][] = $db->_escape($v[$i]);
					$tmpArr[$k]['allWord'] = implode("', '", $tmpArr[$k]['word']);

					// 추가옵션들을 모든 항목에 대입
					for($i = 1; $i <= 6; $i++) $tmpArr[$k]['list'][] = "ex".$i." IN ('".$tmpArr[$k]['allWord']."')";

					$rtnQuery[] = "(".implode(" OR ", $tmpArr[$k]['list']).")";
				}

				// 가격 옵션
				else if(preg_match("/^opt/", $k)) {
					// 선택된 추가옵션들을 하나로 정리
					for($i = 0, $imax = count($v); $i < $imax; $i++) $tmpArr[$k]['word'][] = $db->_escape($v[$i]);
					$tmpArr[$k]['allWord'] = implode("', '", $tmpArr[$k]['word']);

					// 추가옵션들을 모든 항목에 대입
					for($i = 1; $i <= 2; $i++) $tmpArr[$k]['list'][] = "opt".$i." IN ('".$tmpArr[$k]['allWord']."')";

					$rtnQuery[] = "EXISTS (SELECT sno FROM gd_goods_option WHERE goodsno =  goods_link.goodsno and go_is_deleted <> '1' and go_is_display = '1' AND ( ".implode(" OR ", $tmpArr[$k]['list'])."))";
				}

				// 제조사, 원산지, 브랜드
				else if($k == "maker" || $k == "origin" || $k == "brand") {
					for($i = 0, $imax = count($v); $i < $imax; $i++) $tmpArr[$k][] = " goods.$k".(($k == "brand") ? "no" : "")." = '".$db->_escape($v[$i])."'";
					$rtnQuery[] = "(".implode(" OR ", $tmpArr[$k]).")";
				}

				// 색상
				else if($k == "ssColor") {

					$colors = explode('#',$v);
					$tmp = array();

					foreach ($colors as $color) {
						if ($color = trim($color)) $tmp[] = " goods.color like '%".$db->_escape($color)."%'";
					}

					if (sizeof($tmp) > 0) $rtnQuery[] = "(".implode(" OR ", $tmp).")";

				}

			}

		}

		if(count($rtnQuery)) return implode(" AND ", $rtnQuery);
	}


	// 검색
	function setState() {
		if(count($this->searchList)) return 'y';
		else return 'n';
	}

	// 색 목록
	function getColor() {
		global $db;
		$colorList = array();

		$query = "SELECT itemnm FROM ".GD_CODE." WHERE groupcd = 'colorList' ORDER BY sort";
		$result = $db->query($query);
		while($data = $db->fetch($result)) $colorList[] = $data['itemnm'];

		return $colorList;
	}

	// getSmartList 정렬 순서와 동일하게 맞추기 위해 usort 의 비교 함수로 사용
	function usort_cmp($a, $b) {

		if (preg_match('/^[0-9]/',$a)) {
			$type_a = 0;
		}
		elseif (preg_match('/^[a-zA-Z]/',$a)) {
			$type_a = 2;
		}
		else {
			// 그외 (한글 등..)
			$type_a = 1;
		}

		if (preg_match('/^[0-9]/',$b)) {
			$type_b = 0;
		}
		elseif (preg_match('/^[a-zA-Z]/',$b)) {
			$type_b = 2;
		}
		else {
			// 그외 (한글 등..)
			$type_b = 1;
		}

		if ($type_a === $type_b) {
			if (is_numeric($a) && is_numeric($b)) {
				if ($a == $b) {
					return 0;
				}
				return ($a < $b) ? -1 : 1;
			}
			else return strcmp($a, $b);
		}
		else if ($type_a < $type_b) return -1;
		else return 1;
	}


	function getThemeValueArray(&$str, $pipe) {

		$arr = explode($pipe, $str);
		$arr = array_map(array($this,'html_encode'), $arr);

		return $arr;
	}


	function getThemeValueString(&$var,$pipe,$sort=true) {

		// 중복 된 값 제거
		$var = array_unique($var);

		// 정렬
		if ($sort === true)
			usort ($var, array("smartSearch", "usort_cmp"));

		// 500개로 자름
		if (sizeof($var) > 500) {
			$var = array_slice ($var, 0, 500);
		}

		$str = implode($pipe, $var);

		return $str;
	}

}
?>