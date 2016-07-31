<?
/**
 * @Path : 샵루트/lib/selly.class.php
 * @Description : 셀리 연동 클래스 - ( db.class.php와 parsexml.class.php 필요 )
 * @Since : 2011.04.28 WED
 */

// 셀리 < 이나무
class selly {

	// 기본 설정 값
	var $reqPath = array();				// 요청 URL / Path
	var $type = "1";					// 진행중인 작업의 종류 ( 1=인증; 2=카테고리전송; 3=상품전송; )
	var $encoding = "utf-8";			// 쓰거나 읽을 xml의 인코딩 방법 ( 셀리는 UTF-8의 인코딩을 지원 )
	var $mode = "INSERT";				// 요청 보낼시 삽입인지 수정인지 설정 ( INSERT=추가; UPDATE=수정 )
	var $sXml = "";						// 전송할 xml 임시저장
	var $rXml = "";						// 리턴받은 xml 임시저장

	// 인증관련 설정 값
	var $shop_cd;						// 쇼핑몰 고유값
	var $cust_cd;						// 인증코드 - 초기 인증시 부여받는 cust_cd 값 [SELECT value FROM gd_env WHERE category = 'selly' AND name = 'cust_cd']
	var $cust_seq;						// 인증키 - 초기 인증시 부여받는 cust_seq 값 [SELECT value FROM gd_env WHERE category = 'selly' AND name = 'cust_seq']
	var $mhost = "stdev24.godo.co.kr";	// 셀리 인증을 위해 요청할 host
	var $domain;						// 상품이나 카테고리 전송 요청할 host 값 [SELECT value FROM gd_env WHERE category = 'selly' AND name = 'host']

	// 상품전송 설정 값
	var $origin = "001";				// 국가코드 ( 001=대한민국; 002=중국; 003=일본; 004=미국; 005=이탈리아; ...; 243=헤어드도 맥도널드군도 )
	var $delivery_type = "1";			// 배송타입 ( 1=무료; 2=선결제가능착불; 3=착불만가능; 4=선결제만가능 )
	var $delivery_price = 0;		// 배송비

	// 디버깅 관련
	var $resCode;						// xml 전송 후 리턴받은 code값
	var $resMsg;						// xml 전송 후 리턴받은 msg값
	var $errMsg = array();				// class 자체에서 처리 중 입력된 메세지


	// main - 변수 설정 및 초기화
	function selly() {
		$this->encoding = "utf-8";
		$this->mode = "INSERT";
		$this->errMsg = array();
		$this->resCode = "";
		$this->resMsg = "";
		$this->reqPath = array("", "/enamooAPI/STCustomerSeq.gm", "/enamooAPI/STShopCategory.gm", "/enamooAPI/STShopGoods.gm");

		$db = &$GLOBALS['db'];
		list($this->cust_cd) = $db->fetch("SELECT value FROM gd_env WHERE category = 'selly' AND name = 'cust_cd'");
		list($this->cust_seq) = $db->fetch("SELECT value FROM gd_env WHERE category = 'selly' AND name = 'cust_seq'");
		list($this->domain) = $db->fetch("SELECT value FROM gd_env WHERE category = 'selly' AND name = 'domain'");
	}


	// 에러 메세지 보기
	function history() {
		echo "<br />\n";

		if(!count($this->errMsg)) echo "[function.history] 저장된 에러 메세지가 없습니다.<br />\n";
		else for($i = 0, $imax = count($this->errMsg); $i < $imax; $i++) echo $this->errMsg[$i]."<br />\n";

		if($this->resCode || $this->resMsg) echo "(".$this->resCode.") ".$this->resMsg."<br />\n";
	}


	// xml 전송 및 결과 값 리턴
	function curlXML($xml="", $host="", $path="", $enc="") {
		if(!$xml) $xml = $this->sXml;
		if(!$host) $host = $this->domain;
		if(!$path) $path = $this->reqPath[$this->type];
		if(!$enc) $enc = $this->encoding;

		if(!$xml) { $this->errMsg[] = "[function.curlXML] 전송할 xml에 값이 없습니다."; return false; }
		if(!$host) { $this->errMsg[] = "[function.curlXML] 요청할 HOST가 지정되지 않았습니다."; return false; }
		if(!$path) { $this->errMsg[] = "[function.curlXML] 요청할 페이지가 지정되지 않았습니다."; return false; }

		$uri = "http://$host$path";
		$params = array("xml_data" => iconv("euc-kr", $enc, $xml));
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $uri);
		curl_setopt($curl, CURLOPT_HEADER,  0);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
		curl_setopt($curl, CURLOPT_TIMEOUT, 30);
		$result = curl_exec($curl);
		curl_close($curl);

		$result = iconv($enc, "euc-kr", $result);
		return $result;
	}


	// 문자열 데이터 검사
	function chkChar($str) {
		if(!ctype_alnum($str)) return "<![CDATA[".$str."]]>";
		else return $str;
	}


	// 헤더값 생성
	function makeHeader($cd="", $seq="", $enc="", $mode="") {
		if(!$cd) $cd = $this->cust_cd;
		if(!$seq) $seq = $this->cust_seq;
		if(!$enc) $enc = $this->encoding;
		if(!$mode) $mode = $this->mode;

		if(!$cd) { $this->errMsg[] = "[function.makeHeader] 인증코드가 없습니다."; return false; }
		if(!$seq) { $this->errMsg[] = "[function.makeHeader] 인증키가 없습니다."; return false; }
		if(!$enc) { $this->errMsg[] = "[function.makeHeader] 인코딩 방식이 정해지지 않았습니다."; return false; }
		if(!$mode) { $this->errMsg[] = "[function.makeHeader] 요청이 삽입인지 수정인지 정해지지 않았습니다."; return false; }

		// 셀리 요청에 필요한 헤더부분 생성
		$addXml = "<?xml version=\"1.0\" encoding=\"$enc\"?>\n";
		$addXml .= "<data>\n";
		$addXml .= "	<header>\n";
		if($this->type == 3) $addXml .= "		<mode>".$this->chkChar($mode)."</mode>\n";
		$addXml .= "		<cust_cd>".$this->chkChar($cd)."</cust_cd>\n";
		$addXml .= "		<cust_seq>".$this->chkChar($seq)."</cust_seq>\n";
		$addXml .= "	</header>\n";

		return $addXml;
	}


	// 인증 받기
	function idShop($shop_cd="") {
		if(!$shop_cd) { $this->errMsg[] = "[function.idShop] 상점코드가 없습니다."; return false; }

		$this->type = "1";
		$this->sXml .= "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
		$this->sXml .= "	<data>\n";
		$this->sXml .= "		<login_data>\n";
		$this->sXml .= "			<shop_cd>$shop_cd</shop_cd>\n";
		$this->sXml .= "		</login_data>\n";
		$this->sXml .= "	</data>\n";

		$this->reqPath[1] = "/enamooAPI/STCustomerSeq.gm";

		$this->rXml = $this->curlXML($this->sXml, $this->mhost, $this->reqPath[1]);

		if(!$this->rXml) { $this->errMsg[] = "[function.idShop] 결과 값이 없습니다."; return false; }

		$xmlParser = &$GLOBALS['xmlParser']; // xml Parser 클래스 ( 미리 지정해줘야 사용 가능 : 샵루트/lib/parsexml.class.php )
		$xmlParser->parse($this->rXml);
		$resArr = $xmlParser->parseOut();

		$db = &$GLOBALS['db']; // DB 클래스 ( 미리 지정해줘야 사용 가능 )

		for($i = 0, $imax = count($resArr); $i < $imax; $i++) {
			if(is_array($resArr[$i])) {
				$tmpTag = strtolower($resArr[$i]['tag']);
				$tmpVal = $resArr[$i]['val'];

				// 인증코드 및 인증키 받기
				if($tmpTag == "cust_cd" || $tmpTag == "cust_seq" || $tmpTag == "domain") {
					list($tmpCnt) = $db->fetch("SELECT COUNT(*) AS cnt FROM gd_env WHERE category = 'selly' AND name = '".$tmpTag."'");
					if($tmpCnt) {
						$db->query("UPDATE gd_env SET value = '".$tmpVal."' WHERE CONVERT( gd_env.category USING utf8 ) = 'selly' AND CONVERT( gd_env.name USING utf8 ) = '".$tmpTag."'");
						$this->{$tmpTag} = $tmpVal;
					}
					else {
						$db->query("INSERT gd_env SET category = 'selly', name = '".$tmpTag."', value = '".$tmpVal."'");
						$this->{$tmpTag} = $tmpVal;
					}
				}

				// 결과 코드 및 결과 메세지 저장
				if($tmpTag == "code") $this->resCode = $tmpVal;
				if($tmpTag == "msg") $this->resMsg = $tmpVal;
			}
		}

		if(!$this->cust_cd) { $this->errMsg[] = "[function.idShop] 인증코드 값이 없습니다."; return false; }
		if(!$this->cust_seq) { $this->errMsg[] = "[function.idShop] 인증키 값이 없습니다."; return false; }
		if($this->resCode != "000") return false;
		else return true;
	}


	function sendCategory() {
		if(!$this->shop_cd) { $this->errMsg[] = "[function.sendCategory] 상점 고유값(shop_cd)이 없습니다."; return false; }

		$this->type = 2;

		$this->sXml = $this->makeHeader();
		$this->sXml .= "	<category_data>\n";

		$db = &$GLOBALS['db'];
		$query = "SELECT * FROM gd_category ORDER BY LENGTH(category) ASC, category ASC";
		$rs = $db->query($query);

		for($i = 0; $row = $db->fetch($rs); $i++) {
			$this->sXml .= "		<item>\n";
			$this->sXml .= "			<seq>".($i + 1)."</seq>\n";
			$this->sXml .= "			<shop_cd>".$this->shop_cd."</shop_cd>\n";
			$this->sXml .= "			<category_cd1>".$this->chkChar(substr($row['category'], 0, 3))."</category_cd1>\n";
			if(strlen($row['category']) > 3) $this->sXml .= "			<category_cd2>".$this->chkChar(substr($row['category'], 0, 6))."</category_cd2>\n";
			if(strlen($row['category']) > 6) $this->sXml .= "			<category_cd3>".$this->chkChar(substr($row['category'], 0, 9))."</category_cd3>\n";
			if(strlen($row['category']) > 9) $this->sXml .= "			<category_cd4>".$this->chkChar(substr($row['category'], 0, 12))."</category_cd4>\n";
			$this->sXml .= "			<category_nm>".$this->chkChar($row['catnm'])."</category_nm>\n";
			$this->sXml .= "			<category_cd>".$this->chkChar($row['category'])."</category_cd>\n";
			$this->sXml .= "			<sort>".$this->chkChar($row['sort'])."</sort>\n";
			$this->sXml .= "		</item>\n";
		}

		$this->sXml .= "	</category_data>\n";
		$this->sXml .= "</data>\n";

		// XML 전송 및 리턴
		$this->rXml = $this->curlXML($this->sXml, $this->domain, $this->reqPath[$this->type]);

		if(!$this->rXml) { $this->errMsg[] = "[function.sendCategory] 결과 값이 없습니다."; return false; }

		// 리턴받은 XML -> Array
		$xmlParser = &$GLOBALS['xmlParser']; // xml Parser 클래스 ( 미리 지정해줘야 사용 가능 : 샵루트/lib/parsexml.class.php )
		$xmlParser->parse($this->rXml);
		$resArr = $xmlParser->parseOut();

		// 헤더 결과 값 저장 및 오류 발생 시 처리
		for($i = 0; $i < 2; $i++) $resHeader[strtolower($resArr[$i]['tag'])] = $resArr[$i]['val'];
		if($resHeader['code'] != "000") { $this->errMsg[] = "[function.sendCategory] [".$resHeader['code']."] ".$resHeader['msg']; return false; }

		// 리턴 받은 목록 검사
		$tmpArr = array();			// 파싱된 배열을 임시로 재정리해서 넣을 배열
		$tmpStarter = false;		// 처음 헤더의 결과 값을 무시하기 위한 tmpArr 사용 시작 값
		$codeMsg = array();			// 코드별 메세지 저장 ( 예 : $codePerMsg['000'] = "성공" )
		$errCate = array();			// 전송실패한 코드별 카테고리 ( 예 : $codePerCate['803'] = "023001" )
		$rtnMsg = "";				// 실패 유무를 리턴할 때 사용할 값

		for($i = 0, $imax = count($resArr); $i < $imax; $i++) {
			if(!$tmpStarter) if($resArr[$i]['tag'] == "SEQ") $tmpStarter = true;

			if($tmpStarter) {
				$tmpArr[$resArr[$i]['tag']] = $resArr[$i]['val'];
				if($resArr[$i]['tag'] == "MSG" && $tmpArr['CODE'] != "000") $codeMsg[$tmpArr['CODE']] = $tmpArr['MSG'];
				if($resArr[$i]['tag'] == "CODE" && $resArr[$i]['val'] != "000") list($errCate[$tmpArr['CODE']][]) = $db->fetch("SELECT catnm FROM gd_category WHERE category = '".$tmpArr['CATEGORY_CD']."'");
			}
		}

		// 에러 메세지 생성
		if(count($codeMsg)) {
			$rtnMsg .= "전송 중 전송 실패한 카테고리가 존재합니다.\\n\\n";

			foreach($codeMsg as $k => $v) {
				$rtnMsg .= $v."\\n";

				for($i = 0, $imax = count($errCate[$k]); $i < $imax; $i++) $rtnMsg .= "  ".$errCate[$k][$i]."\\n";

				$rtnMsg .= "\\n";
			}

			$rtnMsg .= "위의 카테고리를 확인 후 다시 전송해 주시기 바랍니다.";
		}
		else $rtnMsg = "카테고리 전송 및 수정 요청을 완료했습니다.";

		return $rtnMsg;
	}


	// 상품전송 XML 만들기
	function makeGoods($row) {
		if(!$this->shop_cd) { $this->errMsg[] = "[function.makeGoods] 상점 고유값(shop_cd)이 없습니다."; return false; }

		// 배송비 설정
		if($this->delivery_type == 1) $this->delivery_price = 0;

		$db = &$GLOBALS['db'];
		$this->sXml .= "		<item>\n";

		$this->sXml .= "			<seq>".($i + 1)."</seq>\n";
		$this->sXml .= "			<shop_cd>".$this->shop_cd."</shop_cd>\n";
		$this->sXml .= "			<goods>\n";
		$this->sXml .= "				<goods_cd_cust>".$this->chkChar($row['goodsno'])."</goods_cd_cust>\n";
		$this->sXml .= "				<goods_nm>".$this->chkChar($row['goodsnm'])."</goods_nm>\n";
		$this->sXml .= "				<category_cust>".$this->chkChar($row['category'])."</category_cust>\n";
		$this->sXml .= "				<brand_nm>".$this->chkChar($row['brandnm'])."</brand_nm>\n";
		$this->sXml .= "				<keyword>".$this->chkChar($row['keyword'])."</keyword>\n";
		$this->sXml .= "				<maker>".$this->chkChar($row['maker'])."</maker>\n";
		$this->sXml .= "				<model_nm>".$this->chkChar($row['model_name'])."</model_nm>\n";
		$this->sXml .= "				<make_date>".$this->chkChar($row['manufacture_date'])."</make_date>\n";
		$this->sXml .= "				<tax>".(($row['tax'] == "0") ? "2" : "1")."</tax>\n";
		$this->sXml .= "				<origin>".$this->chkChar($this->origin)."</origin>\n";
		$this->sXml .= "				<delivery_type>".$this->chkChar($this->delivery_type)."</delivery_type>\n";
		$this->sXml .= "				<delivery_price>".$this->chkChar($this->delivery_price)."</delivery_price>\n";
		list($row['market_price'], $row['sale_price'], $row['buy_price']) = $db->fetch("SELECT consumer, price, supply FROM gd_goods_option WHERE goodsno = '".$row['goodsno']."' AND price > 0 ORDER BY price ASC LIMIT 1");
		$this->sXml .= "				<market_price>".$this->chkChar($row['market_price'])."</market_price>\n";
		$this->sXml .= "				<sale_price>".$this->chkChar($row['sale_price'])."</sale_price>\n";
		$this->sXml .= "				<buy_price>".$this->chkChar($row['buy_price'])."</buy_price>\n";
		$this->sXml .= "				<str_price>".$this->chkChar($row['strprice'])."</str_price>\n";
		$tmpArr = explode("|", $row['optnm']); // 옵션명
		$this->sXml .= "				<opt_nm1>".$this->chkChar($tmpArr[0])."</opt_nm1>\n";
		$this->sXml .= "				<opt_nm2>".$this->chkChar($tmpArr[1])."</opt_nm2>\n";
		$this->sXml .= "				<desc>".$this->chkChar(str_replace("
", "", $row['longdesc']))."</desc>\n";
		$this->sXml .= "				<desc_short>".$this->chkChar($row['shortdesc'])."</desc_short>\n";

		$tmpImg = explode("|", $row['img_l']);
		$tmpNum = 1;
		$GLOBALS['cfg']['shopUrl'] = $_SERVER['HTTP_HOST'];
		for($j = 0, $jmax = count($tmpImg); $j < $jmax; $j++) {
			if($tmpNum > 5) break;

			if(preg_match("/^http\:\/\//", $tmpImg[$j])) { // URL 직접입력
				$this->sXml .= "				<img{$tmpNum}>".$this->chkChar($tmpImg[$j])."</img{$tmpNum}>\n";
				$tmpNum++;
			}
			else { // 상점 업로드 이미지
				$tmpImgPath = $GLOBALS['cfg']['rootDir']."/data/goods/".$tmpImg[$j];

				if(file_exists($_SERVER['DOCUMENT_ROOT'].$tmpImgPath) && is_file($_SERVER['DOCUMENT_ROOT'].$tmpImgPath)) {
					$this->sXml .= "				<img{$tmpNum}>".$this->chkChar("http://".$GLOBALS['cfg']['shopUrl'].$tmpImgPath)."</img{$tmpNum}>\n";
					$tmpNum++;
				}
			}
		}

		$this->sXml .= "			</goods>\n";

		// 옵션
		$query = "SELECT opt1, opt2, price, supply, stock FROM gd_goods_option WHERE goodsno = '".$row['goodsno']."' AND go_is_deleted <> '1' AND go_is_display = '1' ORDER BY opt1 ASC, opt2 ASC";
		if (!$row['use_option']) {
			$query = "SELECT '' AS opt1, '' AS opt2, price, supply, stock FROM gd_goods_option WHERE goodsno = '".$row['goodsno']."' AND go_is_deleted <> '1' AND go_is_display = '1' AND link = '1' ORDER BY opt1 ASC, opt2 ASC";
		}
		$rs_o = $db->query($query);
		if($db->count_($rs_o)) {
			$this->sXml .= "			<option>\n";

			for($j = 0; $row_o = $db->fetch($rs_o); $j++) {
				$this->sXml .= "				<opt>\n";
				$this->sXml .= "					<opt_value1>".$this->chkChar($row_o['opt1'])."</opt_value1>\n";
				$this->sXml .= "					<opt_value2>".$this->chkChar($row_o['opt2'])."</opt_value2>\n";
				$this->sXml .= "					<add_price>".$this->chkChar($row_o['price'] - $row['sale_price'])."</add_price>\n";
				$this->sXml .= "					<add_buy_price>".$this->chkChar($row_o['supply'] - $row['buy_price'])."</add_buy_price>\n";
				$this->sXml .= "					<stock>".$this->chkChar($row_o['stock'])."</stock>\n";
				$this->sXml .= "				</opt>\n";
			}

			$this->sXml .= "			</option>\n";
		}

		$this->sXml .= "		</item>\n";
	}


	// AJAX용 상품 전송 - 결과코드( 0:성공; 1:실패; )||리턴메세지||등록일(없으면 미표시)
	function ajaxGoods($goodsno="") {
		// 상품 번호 정리
		if(!$goodsno) {
			$this->errMsg[] = "[function.ajaxGoods] 상품번호가 넘어오지 않았습니다.";
			echo "1||상품번호가 넘어오지 않았습니다.||";
			return false;
		}
		if(is_array($goodsno)) {
			$this->errMsg[] = "[function.ajaxGoods] 개별상품만 입력이 가능합니다.";
			echo "1||개별상품만 입력이 가능합니다.||";
			return false;
		}

		// 생성 시작
		$this->type = 3;
		$this->sXml = $this->makeHeader();
		$this->sXml .= "	<goods_data>\n";

		// 선택된 상품 읽어오기
		$db = &$GLOBALS['db'];
		$query = "SELECT G.*, ".getCategoryLinkQuery('L.category', null, 'max').", B.brandnm FROM gd_goods AS G LEFT JOIN gd_goods_link AS L ON L.goodsno = G.goodsno LEFT JOIN gd_goods_brand AS B ON B.sno = G.brandno WHERE G.goodsno = '$goodsno'";
		$rs = $db->query($query);
		$row = $db->fetch($rs);
		$this->makeGoods($row); // XML 상품부분 생성

		$this->sXml .= "	</goods_data>\n";
		$this->sXml .= "</data>\n";
		// 생성 마침

		// XML 전송 및 리턴
		$this->rXml = $this->curlXML($this->sXml, $this->domain, $this->reqPath[$this->type]);
		$this->rXml = str_replace(array("><![CDATA[", "]]><"), array(">", "<"), $this->rXml); // <![CDATA[]]> 때문에 파싱중 오류가 나는 부분이 있어서 제거;

		// 리턴받은 XML -> Array
		$xmlParser = &$GLOBALS['xmlParser']; // xml Parser 클래스 ( 미리 지정해줘야 사용 가능 : 샵루트/lib/parsexml.class.php )
		$xmlParser->parse($this->rXml);
		$resArr = $xmlParser->parseOut();

		if(!$this->rXml) {
			$this->errMsg[] = "[function.ajaxGoods] 결과 값이 없습니다.";
			echo "1||결과 값이 없습니다.||";
			return false;
		}

		// 헤더 결과 값 저장 및 오류 발생 시 처리
		for($i = 0; $i < 2; $i++) $resHeader[strtolower($resArr[$i]['tag'])] = $resArr[$i]['val'];

		if($resHeader['code'] != "000") {
			echo "1||인증에 실패했습니다.||";
			return false;
		}

		// 상품 로그저장
		$tmpArr = array();
		$tmpNow = date("Y-m-d H:i:s");
		for($i = 2, $imax = count($resArr); $i < $imax; $i++) {
			$tmpArr[$resArr[$i]['tag']] = $resArr[$i]['val'];

			// MSG일 경우 각 상품의 마지막이므로 이 시점에서 로그 처리
			if($resArr[$i]['tag'] == "MSG") {
				if(!$tmpArr['GOODS_CD_CUST']) $tmpArr['GOODS_CD_CUST'] = $goodsno;
				$sql_log = "INSERT INTO ".GD_GOODS_STLOG." SET goodsno = '".$tmpArr['GOODS_CD_CUST']."', code = '".$tmpArr['CODE']."', msg = '".$tmpArr['MSG']."', requrl = '".$this->domain."', regdt = '$tmpNow'";
				$db->query($sql_log);
				$this->errMsg[] = "[function.ajaxGoods] 로그 저장 ( 오류 아님 ) : ".$sql_log;
			}
		}

		// 입력 실패 로그 읽어오기 & 메세지처리
		$qrMsg = "SELECT L.code, L.msg, G.goodsnm FROM ".GD_GOODS_STLOG." AS L INNER JOIN gd_goods AS G ON L.goodsno = G.goodsno WHERE L.regdt = '$tmpNow' AND L.code != '000' AND L.goodsno = '$goodsno' ORDER BY L.code ASC";
		$rsMsg = $db->query($qrMsg);
		$trMsg = $db->count_($rsMsg);

		// 입력 완료 로그 읽어오기
		$qrSMsg = "SELECT L.code, L.msg, G.goodsnm FROM ".GD_GOODS_STLOG." AS L INNER JOIN gd_goods AS G ON L.goodsno = G.goodsno WHERE L.regdt = '$tmpNow' AND L.code = '000' AND L.goodsno = '$goodsno' ORDER BY L.code ASC";
		$rsSMsg = $db->query($qrSMsg);
		$trSMsg = $db->count_($rsSMsg);

		if($trMsg) {
			$tmpCode = "";			// 임시로 현재 코드를 저장 할 변수
			$tmpAlert = "";			// alert으로 띄우 메세지 저장..
			$tmpLimitCount = 0;		// 각 오류마다 최대 5개씩만 보여 주기 위한 카운팅
			for($i = 0; $row = $db->fetch($rsMsg); $i++) {
				if($tmpCode != $row['code']) {
					if($tmpCode) $tmpAlert .= "\\n";
					$tmpCode = $row['code'];
					$tmpAlert .= $row['msg'];
					$tmpLimitCount = 0;
				}

				$tmpLimitCount++;
			}
		}
		else {
			if($trSMsg) {
				echo "0||요청을 완료했습니다.||$tmpNow";
				return true;
			}
			else {
				echo "1||프로그램 상에서 해당 상품입력을 받아들이지 못했습니다.||$tmpNow";
				return true;
			}
		}

		echo "1||".$tmpAlert."||";
		return false;
	}
}



// 이나무 < 셀리
class sellyRec {

	var $reqData = array();			// 요청들어온 xml문자열을 정리
	var $rXml;						// 결과값으로 리턴해줄 xml형식의 내용
	var $type = "3";				// 진행중인 작업의 종류 ( 1=인증; 2=카테고리전송; 3=상품전송; )

	var $errMsg = array();			// class 자체에서 처리 중 입력된 메세지
	var $requiredFields = array();	// 필수입력 값
	var $resCodeList = array();		// 오류코드 별 메세지


	function sellyRec() {
		$this->rXml = "";

		$this->required['g'] = array("goodsnm|상품명|str|255", "tax|과세정보|enum|0;1", "delivery_type|배송정책|enum|0;1;2;3", "longdesc|상세설명|str|", "img1|이미지|str|");
		$this->required['o'] = array("price|판매가|int|10", "stock|재고량|int|8");

		$this->resCodeList = array(
			"999" => "상점 관리자 아이디(cust_id) 값이 전송되지 않았습니다.",		// 인증관련
			"998" => "상점 관리자 비밀번호(cust_pw) 값이 전송되지 않았습니다.",
			"997" => "쇼핑몰 사용기간이 만료됐습니다.",
			"996" => "DB접속에 실패했습니다.",
			"995" => "상점 관리자 계정 정보가 맞지 않습니다.",						// /인증관련
			"899" => "옵션이 존재하지 않습니다.",									// 상품관련
			"898" => "필수 항목이 없습니다.",
			"897" => "옵션의 필수 항목이 없습니다.",
			"896" => "항목 형태가 맞지 않습니다.",
			"895" => "옵션의 항목 형태가 맞지 않습니다.",
			"894" => "상점에 존재하지 않는 카테고리입니다.",						// /상품관련
		);
	}


	// 에러 메세지 보기
	function history() {
		echo "<br />\n";

		if(!count($this->errMsg)) echo "[function.history] 저장된 에러 메세지가 없습니다.<br />\n";
		else for($i = 0, $imax = count($this->errMsg); $i < $imax; $i++) echo $this->errMsg[$i]."<br />\n";

		if($this->resCode || $this->resMsg) echo "(".$this->resCode.") ".$this->resMsg."<br />\n";
	}


	// 에러코드만 보이기
	function onlyErr($code="", $addmsg="") {
		if(!$code) { $this->errMsg[] = "[function.onlyErr] 출력할 오류코드가 없습니다.."; return false; }

		$this->rXml = "<?xml version=\"1.0\" encoding=\"euc-kr\"?>\n";
		$this->rXml .= "<data>\n";
		$this->rXml .= "	<header>\n";
		$this->rXml .= "		<code>$code</code>\n";
		$this->rXml .= "		<msg><![CDATA[".$this->resCodeList[$code].$addmsg."]]></msg>\n";
		$this->rXml .= "	</header>\n";
		$this->rXml .= "</data>\n";
		return $this->rXml;
	}


	// 상품결과코드 추가
	function addResMsg($seq, $code, $addmsg="") {
		$this->rXml .= "		<item>\n";
		$this->rXml .= "			<seq>".$seq."</seq>\n";
		$this->rXml .= "			<code>".$code."</code>\n";
		$this->rXml .= "			<msg><![CDATA[".$this->resCodeList[$code].$addmsg."]]></msg>\n";
		$this->rXml .= "		</item>\n";
	}


	function idCheck() {
		$db = &$GLOBALS['db']; // DB 연결 클래스
		$godo = &$GLOBALS['godo']; // 상점 설정

		/*
		// db 연결 link 에 직접 접근할 수 없음
		if(!$db->db_conn) {
			echo $this->onlyErr("996");
			return false;
		}*/

		if(!$this->reqData['h']['cust_id']) {
			$this->errMsg[] = "[function.idCheck] 상점 관리자 아이디 값이 없습니다..";
			echo $this->onlyErr("999");
			return false;
		}
		if(!$this->reqData['h']['cust_pw']) {
			$this->errMsg[] = "[function.idCheck] 상점 관리자 비밀번호 값이 없습니다..";
			echo $this->onlyErr("998");
			return false;
		}

		if(betweenDate($godo['today'],$godo['edate']) < 0) {
			echo $this->onlyErr("997");
			return false;
		}

		$sql = "SELECT m_id FROM gd_member WHERE m_id = '".$this->reqData['h']['cust_id']."' AND password = password('".$this->reqData['h']['cust_pw']."')";
		$rs = $db->query($sql);
		if($db->count_($rs)) {
			return true;
		}
		else {
			echo $this->onlyErr("995");
			return false;
		}
	}


	// parsexmlstruc.class.php로 파싱한 배열을 다시 저장
	function convertArray($ar) {
		$this->checkType($ar); // 상품인지 카테고리인지 확인

		// 상품요청일 경우
		switch($this->type) {
			case 3 :
				$reqGood = $ar['DATA'][0]['child']['GOODS_DATA'];

				for($i = 0, $imax = count($reqGood[0]['child']['ITEM']); $i < $imax; $i++) {
					$this->reqData['g'][$i]['stock'] = 0;

					$tmpGoods = $ar['DATA'][0]['child']['GOODS_DATA'][0]['child']['ITEM'][$i]['child']; // 상품;
					$tmpOption = $tmpGoods['OPTION'][0]['child']['OPT']; // 옵션;
					$this->reqData['g'][$i]['seq'] = $tmpGoods['SEQ'][0]['data']; // 요청 고유값

					// 상품 필드부분 저장
					foreach($tmpGoods['GOODS'][0]['child'] as $k => $v) {
						$this->reqData['g'][$i][strtolower($k)] = $v[0]['data'];
					}

					// 옵션부분 - 옵션이 없는 경우도 재고량과 가격을 적기 위해 하나는 존재해야함..
					for($j = 0, $jmax = count($tmpOption); $j < $jmax; $j++) {
						foreach($tmpOption[$j]['child'] as $k => $v) {
							$this->reqData['g'][$i]['option'][$j][strtolower($k)] = $v[0]['data'];

							if($k == "STOCK") $this->reqData['g'][$i]['stock'] += $v[0]['data']; // 총 재고량
						}
					}
				}
			break;
		}

		return $this->reqData;
	}


	// 현재 요청된 타입을 검사 ( 2=카테고리전송; 3=상품전송; )
	function checkType($ar) {
		if(isset($ar['DATA'][0]['child']['GOODS_DATA'])) $this->type = 3;

		return $this->type;
	}


	// 헤더 생성
	function makeHeader() {
		$this->rXml = "<?xml version=\"1.0\" encoding=\"euc-kr\"?>\n";
		$this->rXml .= "<data>\n";
		$this->rXml .= "	<header>\n";
		$this->rXml .= "		<code>000</code>\n";
		$this->rXml .= "		<msg><![CDATA[성공]]></msg>\n";
		$this->rXml .= "	</header>\n";
	}

	// 썸네일 함수 ( 참조 : 최신버전 /ROOT_PATH/lib/lib.func.php )
	function thumbnail($src, $folder, $sizeX=100, $sizeY=100, $fix=0) {
		if ( !eregi('http://',$src) ){
			if(!is_file($src)) return;
		}else{
			$result = $this->imgage_check($src);
			if(!$result) return;
		}
		$size = getimagesize($src);

		switch ($size[2]){
			case 1:	$image = @ImageCreatefromGif($src); break;
			case 2:	$image = ImageCreatefromJpeg($src); break;
			case 3:	$image = ImageCreatefromPng($src);  break;
		}

		if ($fix){
			$gap = abs($size[0]-$size[1]);
			switch ($fix){
				case 1:		# 설정된 크기에 따라 비율을 조정
					$reSize = ImgSizeSet($src,$sizeX,$sizeY,$size[0],$size[1]);
					$g_width = 0;
					$g_height = 0;
					$newSizeX = $reSize[0];
					$newSizeY = $reSize[1];
					break;
				case 2:		# 사용되지 않음
					if ($size[0]>$size[1]) $g_width  = $gap / 2;
					else $g_height = $gap / 2;
					$newSizeX = $sizeX;
					$newSizeY = $sizeX;
					if ($size[0]>$size[1]) $size[0] = $size[1];
					else $size[1] = $size[0];
					break;
				case 3:		# 사용되지 않음
					if ($size[0]>$size[1]) $g_width  = $gap;
					else $g_height = $gap;
					$newSizeX = $sizeX;
					$newSizeY = $sizeX;
					if ($size[0]>$size[1]) $size[0] = $size[1];
					else $size[1] = $size[0];
					break;
				case 4:
					$newSizeX = $sizeX;
					$newSizeY = $sizeY;
					break;
			}

			$dst = ImageCreateTruecolor($newSizeX,$newSizeY);
			Imagecopyresampled($dst,$image,0,0,$g_width,$g_height,$newSizeX,$newSizeY,$size[0],$size[1]);
		} else {
			$width = $sizeX;
			$height = $size[1] / $size[0] * $sizeX;
			$dst = ImageCreateTruecolor($width,$height);
			Imagecopyresampled($dst,$image,0,0,0,0,$width,$height,$size[0],$size[1]);
		}
		ImageJpeg($dst,$folder,100);
		ImageDestroy($dst);
		@chmod($folder,0707); // 업로드된 파일 권한 변경
	}

	// 외부 호스팅 이미지 유효성 체크 ( 참조 : 최신버전 /ROOT_PATH/lib/lib.func.php )
	function imgage_check($src) {
		$url = parse_url($src);

		$fp = fsockopen($url[host],80,$errno,$errstr,10);

		if($fp){
			socket_set_timeout($fp, 3);
			if(fputs($fp,"POST ".$url[path]." HTTP/1.0\r\n"."Host: ".$url[host]."\r\n"."User-Agent: Web 0.1\r\n"."\r\n")){
				while(!feof($fp)){
					$data .= fread($fp,1024);
				}
				if(stristr($data,"Content-Type: image")){
					return true;
				}
			}
			fclose($fp);
		}
		return false;
	}

	// 이미지 저장 - 4가지 이미지로 저장
	function imgSaver($imgUrl, $imgType) {
		$cfg = &$GLOBALS['cfg'];													// 상점 설정
		$imgPath = $_SERVER['DOCUMENT_ROOT'].$cfg['rootDir']."/data/goods/";		// 이미지
		$thumbPath = $_SERVER['DOCUMENT_ROOT'].$cfg['rootDir']."/data/goods/t/";	// 썸네일

		$imgInfo = getimagesize($imgUrl);
		$imgFormat = array("", ".gif", ".jpg", ".png");

		for($i = 0; $i < 100; $i++) {
			$fileName = time()."_".substr($imgType, -1, 1)."_".$i.$imgFormat[$imgInfo[2]];
			if(!file_exists($imgPath.$fileName) && !file_exists($thumbPath.$fileName)) {
				break;
			}
		}

		$this->thumbnail($imgUrl, $imgPath.$fileName, $imgInfo[0], $imgInfo[1]);
		$this->thumbnail($imgUrl, $thumbPath.$fileName, $cfg[$imgType]);

		if(file_exists($imgPath.$fileName) || file_exists($thumbPath.$fileName)) {
			@chmod($imgPath.$fileName, 0707);
			@chmod($thumbPath.$fileName, 0707);
		}

		return $fileName;
	}


	// 필수 항목 및 항목 형식 검사
	function requiredCheck($arData, $arRequ) {
		$requErr = ""; // 필수 항목 오류
		$typeErr = ""; // 항목 형식 오류

		for($j = 0, $jmax = count($arRequ); $j < $jmax; $j++) {
			$requInfo = explode("|", $arRequ[$j]);

			if(!$arData[$requInfo[0]] && $arData[$requInfo[0]] != "0") {
				if($requErr) $requErr .= ", ";
				$requErr .= $requInfo[1];
			}
			else {
				switch($requInfo[2]) {
					case "enum" :
						$tmpEnumList = explode(";", $requInfo[3]);
						if(!in_array($arData[$requInfo[0]], $tmpEnumList)) {
							if($typeErr) $typeErr .= ", ";
							$typeErr .= $requInfo[1];
						}
					break;
					case "int" :
						if(!is_numeric($arData[$requInfo[0]])) {
							if($typeErr) $typeErr .= ", ";
							$typeErr .= $requInfo[1];
							break;
						}
					case "str" :
						if(strlen($arData[$requInfo[0]]) > $requInfo[3] && $requInfo[3]) {
							if($typeErr) $typeErr .= ", ";
							$typeErr .= $requInfo[1];
						}
					break;
				}
			}
		}

		return $rtnVal = array($requErr, $typeErr);
	}


	// 상품 개수별 결과 값 생성
	function makeGoodResult() {
		$db = &$GLOBALS['db']; // DB 연결 클래스

		$this->rXml .= "	<return>\n";

		// 필수항목 & 데이터형식 & 데이터길이 검사
				$tmpRequiredList = ""; // 필수항목 누락 리스트
				$tmpDTypeErrList = ""; // 데이터 형식 및 길이 오류 리스트
		for($i = 0, $imax = count($this->reqData['g']); $i < $imax; $i++) {

			$arFieldsError = $this->requiredCheck($this->reqData['g'][$i], $this->required['g']);
			if($arFieldsError[0]) {
				$this->addResMsg($this->reqData['g'][$i]['seq'], "898", "(".$arFieldsError[0].")"); // 필수 항목 누락
				continue;
			}
			if($arFieldsError[1]) {
				$this->addResMsg($this->reqData['g'][$i]['seq'], "896", "(".$arFieldsError[1].")"); // 항목 형식 이상
				continue;
			}

			if($this->reqData['g'][$i]['category']) {
				$sqlc = "SELECT category FROM gd_category WHERE category = '".$this->reqData['g'][$i]['category']."'";
				$rsc = $db->query($sqlc);
				if(!$db->count_($rsc)) {
					$this->addResMsg($this->reqData['g'][$i]['seq'], "894"); // 없는 카테고리 지정
					continue;
				}
			}

			if(count($this->reqData['g'][$i]['option']) > 1) $use_option = '1';
			else $use_option = '0';

			$sql = "INSERT INTO gd_goods SET
				goodsnm = '".addslashes($this->reqData['g'][$i]['goodsnm'])."',
				goods_price = '".$this->reqData['g'][$i]['sale_price']."',
				keyword = '".$this->reqData['g'][$i]['keyword']."',
				maker = '".$this->reqData['g'][$i]['maker']."',
				tax = '".(($this->reqData['g'][$i]['tax'] == "2") ? "0" : "1")."',
				origin = '".$this->reqData['g'][$i]['origin']."',
				delivery_type = '".$this->reqData['g'][$i]['delivery_type']."',
				goods_delivery = '".$this->reqData['g'][$i]['goods_delivery']."',
				strprice = '".$this->reqData['g'][$i]['strprice']."',
				use_option = '".$use_option."',
				optnm = '".$this->reqData['g'][$i]['optnm']."',
				longdesc = '".addslashes($this->reqData['g'][$i]['longdesc'])."',
				shortdesc = '".addslashes($this->reqData['g'][$i]['shortdesc'])."',
				extra_info = '".$this->reqData['g'][$i]['extra_info']."',
				open = '0',
				regdt = NOW()";

			for($j = 0; $j < 5; $j++) {
				if($this->reqData['g'][$i]['img'.$j]) {
					if(!$tmpImg_i && $tmpImg_i == '') $tmpImg_i = $this->imgSaver($this->reqData['g'][$i]['img'.$j], "img_i");
					if(!$tmpImg_s && $tmpImg_s == '') $tmpImg_s = $this->imgSaver($this->reqData['g'][$i]['img'.$j], "img_s");
					if($tmpImg_m) $tmpImg_m .= "|";
					$tmpImg_m .= $this->imgSaver($this->reqData['g'][$i]['img'.$j], "img_m");
					if($tmpImg_l) $tmpImg_l .= "|";
					$tmpImg_l .= $this->imgSaver($this->reqData['g'][$i]['img'.$j], "img_l");
				}
			}
			if($tmpImg_i) $sql .= ", img_i = '".$tmpImg_i."'";
			if($tmpImg_s) $sql .= ", img_s = '".$tmpImg_s."'";
			if($tmpImg_m) $sql .= ", img_m = '".$tmpImg_m."'";
			if($tmpImg_l) $sql .= ", img_l = '".$tmpImg_l."'";

			if(!count($this->reqData['g'][$i]['option'])) {
				$this->addResMsg($this->reqData['g'][$i]['seq'], "899"); // 옵션 없음
				continue;
			}

			$totstock = 0;
			for($j = 0, $jmax = count($this->reqData['g'][$i]['option']); $j < $jmax; $j++) {
				$tmpOption = $this->reqData['g'][$i]['option'][$j];

				$arOptionFieldsError = $this->requiredCheck($this->reqData['g'][$i]['option'][$j], $this->required['o']);
				if($arOptionFieldsError[0]) {
					$this->addResMsg($this->reqData['g'][$i]['seq'], "897", "(".$arOptionFieldsError[0].")"); // 옵션 필수 항목 누락
					break;
				}
				if($arOptionFieldsError[1]) {
					$this->addResMsg($this->reqData['g'][$i]['seq'], "895", "(".$arOptionFieldsError[1].")"); // 옵션 항목 형식 이상
					break;
				}


				$sqlo[$j] = "INSERT INTO gd_goods_option SET
					goodsno = 'ENReplaceGoodsNo',
					opt1 = '".$tmpOption['opt1']."',
					opt2 = '".$tmpOption['opt2']."',
					price = '".$tmpOption['price']."',
					consumer = '".$tmpOption['consumer']."',
					supply = '".$tmpOption['supply']."',
					reserve = '".$tmpOption['reserve']."',
					stock = '".$tmpOption['stock']."'";

				if($j == 0) $sqlo[$j] .= ", link = 1";

				$totstock = $totstock + $tmpOption['stock'];
			}
			if($arOptionFieldsError[0]) {
				$this->addResMsg($this->reqData['g'][$i]['seq'], "897", "[".$arOptionFieldsError[0]."]"); // 옵션 필수 항목 누락
				continue;
			}
			if($arOptionFieldsError[1]) {
				$this->addResMsg($this->reqData['g'][$i]['seq'], "895", "[".$arOptionFieldsError[1]."]"); // 옵션 항목 형태 이상
				continue;
			}

			if($totstock) $sql .= ", totstock = '".$totstock."'"; // 상품 총 재고량

			// 모든 검사에 통과하면 이하 실행
			$rs = $db->query($sql);
			$tmpThisSeq = $db->lastID();

			// 상품분류 연결방식 전환 여부에 따른 처리
			foreach (getHighCategoryCode($this->reqData['g'][$i]['category']) as $val) {
				$db->query("INSERT INTO gd_goods_link SET goodsno='".$tmpThisSeq."', category='".$val."', hidden='0', sort=-UNIX_TIMESTAMP()"); // 카테고리 등록
			}

			foreach($sqlo as $k => $v) {
				$v = str_replace("ENReplaceGoodsNo", $tmpThisSeq, $v);
				$db->query($v);
			}

			$this->rXml .= "		<item>\n";
			$this->rXml .= "			<seq>".$this->reqData['g'][$i]['seq']."</seq>\n";
			$this->rXml .= "			<goodsno>".$tmpThisSeq."</goodsno>\n";
			$this->rXml .= "			<code>000</code>\n";
			$this->rXml .= "			<msg><![CDATA[성공]]></msg>\n";
			$this->rXml .= "		</item>\n";

		}

		$this->rXml .= "	</return>\n";
		$this->rXml .= "</data>\n";
	}


	// xml 생성
	function makeXml($ar) {
		$this->reqData['h']['cust_id'] = $ar['DATA'][0]['child']['HEADER'][0]['child']['CUST_ID'][0]['data'];
		$this->reqData['h']['cust_pw'] = $ar['DATA'][0]['child']['HEADER'][0]['child']['CUST_PW'][0]['data'];
		if(!$this->idCheck()) {
			$this->errMsg[] = "[function.convertArray] 인증에 실패했습니다..";
			return false;
		}
		else $this->makeHeader();

		$this->convertArray($ar);

		switch($this->type) {
			case 3 :
				$this->makeGoodResult();
			break;
		}

		echo $this->rXml;
	}
}
?>
