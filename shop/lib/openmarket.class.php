<?

class openmarket
{
	### 상점정보
	function getGodo()
	{
		ob_start();
		$file = dirname(__FILE__) . "/../conf/godomall.cfg.php";
		if (!is_file($file)) return false;
		$file = file($file);
		$this->godo = decode($file[1],1);
		ob_end_clean();
	}

	### hashdata 생성 (데이터 무결성을 검증하는 데이터로 요청시 필수 항목)
	function hashdata(&$data)
	{
		$data[godosno]	= $this->godo[sno];					# 상점번호
		$data[hashdata]	= md5($data[godosno]);				# hashdata 생성
	}

	### 상점검증
	function isExists()
	{
		$this->getGodo();
		if ($this->godo[sno] == '' || $this->godo[sno] == '0') return array('600', "false - 쇼핑몰 환경정보에 상점아이디가 비어 있습니다.^고도로 문의하세요.");

		$data=array();
		$this->hashdata($data);
		$out = readurl("http://godosiom.godo.co.kr/sock_isExists.php?godosno={$data[godosno]}&hashdata={$data[hashdata]}");

		### 상점검증결과 메시지정의
		if ($out == 'false-need:join') $this->isExistsMsg = "파워오픈마켓을 신청하셔야 사용이 가능합니다.";
		else if ($out == 'false-need:extension') $this->isExistsMsg = "파워오픈마켓을 연장하셔야 사용이 가능합니다.";

		return array('400', $out);
	}

	### 필수데이터 검증
	function verifyData($data)
	{
		$needs = array();
		if ($data['category'] == '') $needs[] = '오픈마켓 표준분류를 입력해주세요.';
		if ($data['goodsnm'] == '') $needs[] = '상품명을 입력해주세요.';
		if ($data['goodscd'] == '') $needs[] = '모델명을 입력해주세요.';
		if ($data['maker'] == '') $needs[] = '제조사명을 입력해주세요.';
		if ($data['origin_kind'] == '') $needs[] = '원산지를 입력해주세요.';
		if ($data['brandnm'] == '') $needs[] = '브랜드를 입력해주세요.';
		if ($data['price'] == '') $needs[] = '판매가를 입력해주세요.';
		if ($data['consumer'] == '') $needs[] = '정가를 입력해주세요.';
		if ($data['tax'] == '') $needs[] = '과세/비과세를 입력해주세요.';

		### 상품설명
		if ($data['longdesc'] == '') $needs[] = '상품상세정보를 입력해주세요.';
		if ($this->imgStatus($data['longdesc'])) $needs[] = '상품상세정보에 이미지호스팅으로 전환이 필요한 이미지가 있습니다.';

		### 상품이미지
		if ($data['img_m'] == '') $needs[] = '상품이미지(상세이미지)를 입력해주세요.';
		$imgs = explode("|",$data['img_m']);
		if (!preg_match('/\.(jpg|png)$/i', $imgs[0])) $needs[] = '상품이미지(상세이미지) 중에서 첫번째 이미지는 JPG나 PNG 파일만 등록할 수 있습니다.('. $imgs[0] . ')';

		### 배송ㆍA/S
		if ($data['noSameShipAS'] != 'o')
		{
			@include dirname(__FILE__) . "/../conf/openmarket.php";
			if (isset($omCfg) === true) $data = array_merge($data, $omCfg);
		}
		if ($data['ship_type'] == '3'); // 무료배송
		else if ($data['ship_type'] == '' || $data['ship_price'] == '') $needs[] = '배송비를 설정해주세요.';
		else if ($data['ship_type'] == '5' && $data['ship_base'] == '' ) $needs[] = '가격조건부 무료에서 가격기준을 설정해주세요.';
		else if ($data['ship_type'] == '4' && $data['ship_base'] == '' ) $needs[] = '수량조건부 무료에서 수량기준을 설정해주세요.';
		if ($data['ship_pay'] == '') $needs[] = '배송비 선결제여부를 설정해주세요.';

		return $needs;
	}

	### Array imgStatus(): 이미지경로 현황
	function imgStatus($source)
	{
		$inCnt = 0;
		if (is_string($source) === true) $split = $this->_split($source);
		else $split = $source;
		for ($i=1,$s=count($split); $i < $s; $i += 2)
		{
			if (preg_match('@^http:\/\/@ix', $split[$i]));
			else $inCnt++;
		}
		return $inCnt;
	}

	### Array _split(): 이미지경로 기준으로 분할
	function _split($source)
	{
		$cnt = array();
		$Ext = 'gif|jpg|jpeg|png';
		$Ext = '(?<=src\=")(?:[^"])*[^"]+\.(?:'. $Ext .')(?=")'.
			"|(?<=src\=')(?:[^'])*[^']+\.(?:". $Ext .")(?=')".
			'|(?<=src\=\\\\")(?:[^"])*[^"]+\.(?:'. $Ext .')(?=\\\\")'.
			"|(?<=src\=\\\\')(?:[^'])*[^']+\.(?:". $Ext .")(?=\\\\')";
		$pattern = '@('. $Ext .')@ix';
		$split = preg_split($pattern, $source, -1, PREG_SPLIT_DELIM_CAPTURE);
		return $split;
	}
}



/***************************************************************************************************
*  openmarketSend 클래스
*    - 오픈마켓판매관리 Temp_DB에 Insert 하는 클래스.
***************************************************************************************************/
class openmarketSend extends openmarket
{
	### 상품등록
	function putGoods($goodsno, $mode)
	{
		$this->getGodo();
		if ($this->godo[sno] == '' || $this->godo[sno] == '0') return array('600', "false - 쇼핑몰 환경정보에 상점아이디가 비어 있습니다.^고도로 문의하세요.");

		$data = $GLOBALS['db']->fetch("select * from ".GD_OPENMARKET_GOODS." where goodsno='{$goodsno}'", 1);
		if ($data['goodsno'] == '') return array('400', "false - 전송할 상품번호가 지정되지 않았습니다.");
		$data['mode'] = $mode;

		# 상품설명
		$data['longdesc'] = sprintf("<style type=\"text/css\"><!-- #godoContents {font:12px dotum; color:#000000;} --></style>\n<div id=\"godoContents\">\n%s\n</div>", $data['longdesc']);

		# 필수옵션
		$tmp = $this->mixOption($data);
		if ($tmp != '') $data['option'] = $tmp;

		# 이미지
		$cnt = 0;
		$imgs = explode("|",$data['img_m']);
		foreach ( $imgs as $filenm ){
			if ( $filenm != '' && file_exists( dirname(__FILE__) . "/../data/goods/{$filenm}") )
			{
				$cnt++;
				$tmp = "http://{$_SERVER[HTTP_HOST]}" . str_replace($_SERVER['DOCUMENT_ROOT'], "", realpath(dirname(__FILE__) . "/../data/goods/{$filenm}"));
				if ($cnt == 1) $data['fix_image'] = $tmp;
				$data['image' . $cnt] = $tmp;
			}
		}

		### 배송ㆍA/S
		if ($data['noSameShipAS'] != 'o')
		{
			@include dirname(__FILE__) . "/../conf/openmarket.php";
			if (isset($omCfg) === true) $data = array_merge($data, $omCfg);
		}
		$data['ship_price'] = sprintf("%0d", $data['ship_price']);
		$data['ship_base'] = sprintf("%0d", $data['ship_base']);
		if ($data['ship_type'] == '0'){
			$data['ship_type'] = ($data['ship_pay'] == 'Y' ? '2' : '1');
		}
		else if ($data['ship_type'] == '3') $data['ship_pay']='N';

		### 데이터전송
		unset($data['img_m'], $data['optnm'], $data['noSameShipAS'], $data['regdt'], $data['moddt']);
		$data = array_map("trim", $data); // 데이터 앞뒤 space 값 들어가지 않도록 주의 필요
		$this->hashdata($data);

		$out = readpost("http://godosiom.godo.co.kr/sock_putGoods.php", $data);
		return array('400', $out);
	}

	function mixOption(&$data)
	{
		$optnm = explode("|",$data['optnm']);
		$query = "select * from ".GD_OPENMARKET_GOODS_OPTION." where goodsno='{$data['goodsno']}'";
		$res = $GLOBALS['db']->query($query);
		while ($tmp=$GLOBALS['db']->fetch($res, 1)){
			$opt[$tmp['opt1']][$tmp['opt2']] = $tmp['stock'];
			$opt1[] = $tmp['opt1'];
			$opt2[] = $tmp['opt2'];

			### 총재고량 계산
			$stock += $tmp['stock'];
		}

		$data['stock'] = $this->reStock($stock, $data);
		if ($opt1) $opt1 = array_unique($opt1);
		if ($opt2) $opt2 = array_unique($opt2);
		if (!$opt){
			$opt1 = array('');
			$opt2 = array('');
		}

		if(count($opt)>1 || $opt1[0] != null || $opt2[0] != null)
		{
			### 옵션명변수 재정의(사이즈/색상)
	        $ctrim = create_function('$n', 'return str_replace(" ", "", $n);');
			$optnm = array_map($ctrim, $optnm);
			$optnm = array_map("strtolower", $optnm);

			$synSize = array('사이즈', 'size', '크기');
			$synColor = array('색상', 'color', '컬러');

			if (in_array($optnm[0], $synSize) === true){
				$optnm[0] = '사이즈';
				$optnm[1] = '색상';
			}
			else if (in_array($optnm[0], $synColor) === true){
				$optnm[0] = '색상';
				$optnm[1] = '사이즈';
			}
			else {
				$optnm[0] = '사이즈';
				$optnm[1] = '색상';
			}

			### 주문선택사항 값 Format
			$op2=$opt2[0];
			foreach ($opt2 as $op2){
				foreach ($opt1 as $op1){
					$oStock[] = $this->reStock($opt[$op1][$op2], $data);
				}
			}
			$tmp = '';
			$opt1v = implode(',',$opt1);
			$opt2v = implode(',',$opt2);
			$tmp .= $optnm[0].'<'.($opt1v ? $opt1v : ' ').'>';
			$tmp .= $optnm[1].'<'.($opt2v ? $opt2v : ' ').'>';
			$tmp .= '수량<'.implode(',',$oStock).'>';
		}
		return $tmp;
	}

	function reStock($stock, &$data)
	{
		if ($data['runout'] == 1) return 0;
		else if ($data['usestock'] != 'o') return 9999;
		else return sprintf("%0d", $stock);
	}
}

?>