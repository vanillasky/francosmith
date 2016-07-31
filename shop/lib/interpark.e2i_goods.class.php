<?

@include_once dirname(__FILE__) . "/../lib/httpSock.class.php";
@include_once dirname(__FILE__) . "/../lib/parsexmlstruc.class.php";
@include_once dirname(__FILE__) . "/../lib/xmlWriter.class.php";
@include_once dirname(__FILE__) . "/../lib/putLog.class.php";

class e2i_goods_api extends putLog
{
	var $interfaceurl;	# 인터페이스 URL

	//http://ipss1.interpark.com/openapi/product/ProductAPIService.do?_method=InsertProductAPIData&citeKey=[인증키]&secretKey=[비밀키]&dataUrl=[XML데이터URL]
	var $connPath = "http://ipss1.interpark.com/openapi/product/PrdService.do?_method=%s&dataUrl=%s";
	var $testPath = "http://sptest.interpark.com/openapi/product/PrdService.do?_method=%s&dataUrl=%s"; //sptest.interpark.com
	var $errMsg;	# 에러메시지

	function e2i_goods_api($glist, $part='', $isTest=false)
	{
		$this->putLog('prdInfo');
		$this->log("START");

		## 인터페이스 URL 정의
		if ($isTest) $this->interfaceurl = $this->testPath;
		else $this->interfaceurl = $this->connPath;

		## 호스트 정의
		$tmp = parse_url($_SERVER['HTTP_HOST']);
		$this->host = ($tmp['host'] ? $tmp['host'] : $_SERVER['HTTP_HOST']);

		## API START
		//$this->chkInpkCfg();
		$this->chkInpkOSCfg();
		switch ($part)
		{
			case "stock": # 재고관련정보만 전송
				$gXml = $this->formatStock($glist);
				break;

			default: # 상품전체정보 전송
				$gXml = $this->formatGoods($glist);
				break;
		}
		$xmlFile = $this->createXml($gXml);
		$this->strXml = $this->socketInterpark($xmlFile);

		## redefine strXml
		$pattern = array('<?xml version="1.0" encoding="euc-kr"?>', '<result>', '</result>');
		$this->strXml = str_replace($pattern, "", $this->strXml);
		$this->strXml = '<?xml version="1.0" encoding="euc-kr"?><result>' . "\n" . $this->strXml . '</result>';

		if (empty($this->errMsg) === false)
		{
			if ($part == 'stock') $this->log($this->errMsg["log"]);
			else $this->endHeader($this->errMsg);
		}

		$this->log("END");
	}

	### load and check to interpark conf
	function chkInpkCfg()
	{
		## load interpark conf
		$this->log("Load Interpark_Conf START");
		if (class_exists('interpark'))
		{
			$interpark = new interpark();
			$interpark->getInpk();
			$this->rootDir = $interpark->rootDir;
			$this->inpkCfg = $interpark->inpkCfg;
		}
		else $this->log("Non-existent class interpark");
		$this->log("Load Interpark_Conf END");

		## check interpark conf
		$this->log("Check Interpark_Conf START");
		if ($this->inpkCfg['entrNo'] == '')
		{
			$this->errMsg = array(
				"log" => array('Empty entrNo', 'Check Interpark_Conf END'),
				"header" => "인터파크 업체번호가 존재하지 않습니다. 고도로 문의하세요."
				);
			return;
		}
		if ($this->inpkCfg['ctrtSeq'] == '')
		{
			$this->errMsg = array(
				"log" => array('Empty ctrtSeq', 'Check Interpark_Conf END'),
				"header" => "인터파크 공급계약일련번호가 존재하지 않습니다. 고도로 문의하세요."
				);
			return;
		}
		$this->log("Check Interpark_Conf END");
	}

	### load and check to interpark conf
	function chkInpkOSCfg()
	{
		## load interpark conf
		$this->log("Load Interpark_OpenStyle_Conf START");
		if (class_exists('interpark'))
		{
			$interparkOS = new interpark();
			$interparkOS->getInpkOS();
			$this->rootDir = $interparkOS->rootDir;
			$this->inpkOSCfg = $interparkOS->inpkOSCfg;
		}
		else $this->log("Non-existent class interpark");
		$this->log("Load Interpark_OpenStyle_Conf END");

		## check interpark conf
		$this->log("Check Interpark_OpenStyle_Conf START");
		## 상품등록인증키
		if ($this->inpkOSCfg['regiAuthKey'] == '')
		{
			$this->errMsg = array(
				"log" => array('Empty regiAuthKey', 'Check Interpark_OpenStyle_Conf END'),
				"header" => "인터파크 상품등록 인증키가 존재하지 않습니다. 인터파크로 문의하세요."
				);
			return;
		}
		## 상품등록비밀키
		if ($this->inpkOSCfg['regiSecKey'] == '')
		{
			$this->errMsg = array(
				"log" => array('Empty regiSecKey', 'Check Interpark_OpenStyle_Conf END'),
				"header" => "인터파크 상품등록 비밀키가 존재하지 않습니다. 인터파크로 문의하세요."
				);
			return;
		}
		## 상품수정인증키
		if ($this->inpkOSCfg['modiAuthKey'] == '')
		{
			$this->errMsg = array(
				"log" => array('Empty modiAuthKey', 'Check Interpark_OpenStyle_Conf END'),
				"header" => "인터파크 상품수정 인증키가 존재하지 않습니다. 인터파크로 문의하세요."
				);
			return;
		}
		## 상품수정비밀키
		if ($this->inpkOSCfg['modiSecKey'] == '')
		{
			$this->errMsg = array(
				"log" => array('Empty modiSecKey', 'Check Interpark_OpenStyle_Conf END'),
				"header" => "인터파크 상품수정 비밀키가 존재하지 않습니다. 인터파크로 문의하세요."
				);
			return;
		}

		$this->log("Check Interpark_OpenStyle_Conf END");
	}



	### Format goods
	function formatGoods($glist)
	{
		if (empty($this->errMsg) === false) return;
		if (is_array($glist) === false || count($glist) == 0){
			$this->log("Empty-glist (Format_goods Before)");
			return;
		}
		$this->log("Format_goods START");

		## 등록
		$xmlReg = array();
		$xmlReg['title'] = "상품정보 데이터";
		$xmlReg['description'] = "상품정보 등록을 위한 Open Api 데이터";
		$xmlReg['item'] = array();

		## 수정
		$xmlMod = array();
		$xmlMod['title'] = "상품정보 데이터";
		$xmlMod['description'] = "상품정보 수정을 위한 Open Api 데이터";
		$xmlMod['item'] = array();

		## 특이사항
		ob_start();
		@include dirname(__FILE__) . "/../conf/interpark_spcaseEd.php";
		$spcaseEd = ob_get_contents();
		ob_end_clean();

		ob_start();
		foreach ($glist as $goodsno)
		{
			$data = $GLOBALS['db']->fetch("select * from ".GD_GOODS." where goodsno='{$goodsno}'");
			if ($data['goodsno'])
			{
				if ($data['inpk_prdno'] == ''){
					$mode = 'register';
					$item = &$xmlReg['item'][];
					$item['sidx'] = count($xmlReg['item']);	# 일련번호
				}
				else {
					$mode = 'modify';
					$item = &$xmlMod['item'][];
					$item['sidx'] = count($xmlMod['item']);	# 일련번호
				}

				### e나무상품의 필수옵션
				list($price, $stock, $prdOption) = $this->getGoodsOption($data[goodsno], $data[optnm], $data['usestock']);

				/*--------------- 필수 요청 데이터 : Start ---------------*/
				$item['supplyEntrNo'] = $this->inpkCfg['entrNo'];	# 업체번호
				$item['supplyCtrtSeq'] = $this->inpkCfg['ctrtSeq'];	# 공급계약일련번호

				if ($mode == 'modify'){
					$item['prdNo'] = $data['inpk_prdno'];	# 인터파크 상품번호
				}
				else {
					$item['prdStat'] = '01';	# 상품상태 (고도고정값)
					$item['shopNo'] = '0000100000';	# 인터파크 상점번호 (고도고정값)
					$item['omDispNo'] = $data['inpk_dispno'];	# 인터파크 전시코드
				}

				$item['prdNm'] = strcut($data['goodsnm'],80);	# 상품명 (80bybte)
				$item['hdelvMafcEntrNm'] = $data['maker'];	# 제조사
				$item['prdOriginTp'] = $data['origin'];	# 원산지
				$item['taxTp'] = ($data['tax'] == '1' ? '01' : '02');	# 부가면세상품
				$item['ordAgeRstrYn'] = 'N';	# 성인용품여부 (고도고정값)

				{ // 판매상태
					if ($data['usestock'] && $stock==0) $data['runout'] = 1; # 실재고에 따른 자동 품절 처리

					if (!$data['open']) $item['saleStatTp'] = '03'; # 판매중지
					else if ($data['runout'] == 1) $item['saleStatTp'] = '02'; # 품절
					else $item['saleStatTp'] = '01'; # 판매중
					if ($mode == 'register' && $item['saleStatTp'] == '02') $item['saleStatTp'] = '03';
				}

				$item['saleUnitcost'] = $price;	# 판매가
				$item['saleLmtQty'] = ($data['usestock'] ? $stock : 99999);	# 판매수량

				if ($mode == 'register'){
					$item['saleStrDts'] = date('Ymd');	# 판매시작일
					$item['saleEndDts'] = '99991231';	# 판매종료일 (고정값)
				}

				$item['proddelvCostUseYn'] = 'N';	# 상품배송비사용여부 (고도고정값)
				$item['prdBasisExplanEd'] = sprintf("<style type=\"text/css\"><!-- #godoContents {font:12px dotum; color:#000000;} --></style>\n<div id=\"godoContents\">\n%s\n</div>", $data['longdesc']);	# 상품설명

				{ // 대표이미지
					$imgs = $image_url = array();
					$imgs = array_merge( $imgs, explode("|",$data['img_l']) );
					$imgs = array_merge( $imgs, explode("|",$data['img_m']) );
					foreach ( $imgs as $filenm ){
						if (!$filenm) continue;

						if (preg_match('/^http(s)?:\/\/.+(jpg|gif|jpeg|png)$/i',$filenm)) {
								$image_url[] = $filenm;
						}
						else {
							if (file_exists( dirname(__FILE__) . "/../data/goods/{$filenm}") )
								$image_url[] = "http://{$this->host}{$this->rootDir}/data/goods/{$filenm}";
						}
						if ( count($image_url) >=1 ) break;
					}
					$item['zoomImg'] = str_replace(' ', '%20', $image_url[0]);
				}
				/*--------------------------------------------------------*/
				if ($mode == 'modify') $item['imgUpdateYn']="Y"; else $item['imgUpdateYn']="N";

				/*--------------- 선택 요청 데이터 : Start ---------------*/
				//$item['prdPrefix'] = '';	# 앞문구
				//$item['prdPostfix'] = '';	# 뒷문구

				$keywords = array();
				$tmp = explode(",", $data['keyword']);
				for ($k = 0; $k < count($tmp); $k++){
					if ($k < 4) $keywords[] = $tmp[$k];
				}
				$item['prdKeywd'] = implode(",", $keywords);	# 쇼핑태그 최대 4개 콤마구분

				// 브랜드
				if ($data['brandno']){
					list($item['brandNm']) = $GLOBALS['db']->fetch("select brandnm from ".GD_GOODS_BRAND." where Sno='{$data[brandno]}'");
				}
				else $item['brandNm'] = '';

				//$item['entrPoint'] = '';	# 업체POINT
				//$item['minOrdQty'] = '';	# 최소구매수량
				$item['prdOption'] = $prdOption; # 주문선택사항
				//$item['delvCost'] = '';	# 배송비
				//$item['delvAmtPayTpCom'] = '';	# 배송비 결제 방식
				//$item['delvCostApplyTp'] = '';	# 배송비 적용 방식
				//$item['freedelvStdCnt'] = '';	# 무료배송비기준 수량
				$item['spcaseEd'] = $spcaseEd;	# 특이사항
				//$item['intfreeInstmStrDts'] = '';	# 무이자할부시작일
				//$item['intfreeInstmEndDts'] = '';	# 무이자할부종료일
				//$item['listInstmMonths'] = '';	# 무이자할부개월수
				$item['pointmUseYn'] = 'N';	# 포인트몰등록여부
				if ($mode == 'register'){
					$item['ippSubmitYn'] = ($this->inpkCfg['ippSubmitYn'] == 'Y' ? 'Y' : 'N');	# 가격비교 등록 여부
				}
				$item['shopDispInfo'] = '전시타입<2>상점번호<0000100000>전시번호<001410059>';	# 전시구성정보
				$item['originPrdNo'] = $data['goodsno'];	# 원상품번호
				/*--------------------------------------------------------*/

				$item = array_map("trim", $item); // 데이터 앞뒤 space 값 들어가지 않도록 주의 필요
			}
		}
		$this->err( ob_get_clean() );
		$this->log("Format_goods END");

		return array('xmlReg' => $xmlReg, 'xmlMod' => $xmlMod);
	}

	### Format stock of goods
	function formatStock($glist)
	{
		if (empty($this->errMsg) === false) return;
		if (is_array($glist) === false || count($glist) == 0){
			$this->log("Empty-glist (Format_stock Before)");
			return;
		}
		$this->log("Format_stock START");

		$xmlMod = array();
		$xmlMod['title'] = "상품정보 데이터";
		$xmlMod['description'] = "상품정보 수정을 위한 Open Api 데이터";
		$xmlMod['item'] = array();

		ob_start();
		foreach ($glist as $goodsno)
		{
			$data = $GLOBALS['db']->fetch("select * from ".GD_GOODS." where goodsno='{$goodsno}'");
			if ($data['goodsno'])
			{
				if ($data['inpk_prdno'] == '') continue;
				$item = &$xmlMod['item'][];
				$item['sidx'] = count($xmlMod['item']);	# 일련번호

				### e나무상품의 필수옵션
				list($price, $stock, $prdOption) = $this->getGoodsOption($data[goodsno], $data[optnm], $data['usestock']);

				/*--------------- 필수 요청 데이터 : Start ---------------*/
				$item['supplyEntrNo'] = $this->inpkCfg['entrNo'];	# 업체번호
				$item['supplyCtrtSeq'] = $this->inpkCfg['ctrtSeq'];	# 공급계약일련번호
				$item['prdNo'] = $data['inpk_prdno'];	# 인터파크 상품번호
				/*--------------------------------------------------------*/

				/*--------------- 선택 요청 데이터 : Start ---------------*/
				$item['prdNm'] = strcut($data['goodsnm'],80);	# 상품명 (80bybte)
				{ // 판매상태
					if ($data['usestock'] && $stock==0) $data['runout'] = 1; # 실재고에 따른 자동 품절 처리

					if (!$data['open']) $item['saleStatTp'] = '03'; # 판매중지
					else if ($data['runout'] == 1) $item['saleStatTp'] = '02'; # 품절
					else $item['saleStatTp'] = '01'; # 판매중
				}

				$item['saleUnitcost'] = $price;	# 판매가
				$item['saleLmtQty'] = ($data['usestock'] ? $stock : 99999);	# 판매수량
				$item['prdOption'] = $prdOption; # 주문선택사항
				$item['originPrdNo'] = $data['goodsno'];	# 원상품번호
				/*--------------------------------------------------------*/

				$item = array_map("trim", $item); // 데이터 앞뒤 space 값 들어가지 않도록 주의 필요
			}
		}
		$this->err( ob_get_clean() );
		$this->log("Format_stock END");

		return array('xmlMod' => $xmlMod);
	}

	### Option of goods
	function getGoodsOption($goodsno, $optnm, $usestock)
	{
		$rData = array('price'=>'', 'stock'=>'', 'prdOption'=>'');
		$optnm = explode("|",$optnm);
		$res = $GLOBALS['db']->query("select * from ".GD_GOODS_OPTION." where goodsno='{$goodsno}' and go_is_deleted <> '1' and go_is_display = '1' ");
		while ($tmp=$GLOBALS['db']->fetch($res,1)){
			$opt1[] = $tmp['opt1'];
			$opt2[] = $tmp['opt2'];
			if ($tmp['optno'] == ''){ // 옵션코드
				$tmp['optno'] = $tmp['sno'];
				$GLOBALS['db']->query("update ".GD_GOODS_OPTION." set optno=sno where sno='{$tmp[sno]}'");
			}
			$opt[$tmp['opt1']][$tmp['opt2']] = array('price'=>$tmp['price'],'stock'=>$tmp['stock'],'optno'=>$tmp['optno']);

			## 총재고량 계산
			$rData['stock'] += $tmp['stock'];
		}
		if ($opt1) $opt1 = array_unique($opt1);
		if ($opt2) $opt2 = array_unique($opt2);
		if (!$opt) $opt1 = $opt2 = array('');

		## 기본 가격 할당
		$rData['price']	  = $opt[$opt1[0]][$opt2[0]]['price'];

		## 주문선택사항
		if(count($opt)>1 || $opt1[0] != null || $opt2[0] != null)
		{
			$op2=$opt2[0];
			foreach ($opt1 as $op1){
				foreach ($opt2 as $op2){
					$oStock[] = $opt[$op1][$op2]['stock'];
					$oPrice[] = $opt[$op1][$op2]['price'] - $rData['price'];
					$oOptno[] = $opt[$op1][$op2]['optno'];
				}
			}

			$tmp = &$rData['prdOption'];
			if ($optnm[0]) $tmp .= $optnm[0].'<'.implode(',',$opt1).'>';
			if ($optnm[1]) $tmp .= $optnm[1].'<'.implode(',',$opt2).'>';
			if ($usestock && $oStock) $tmp .= '수량<'.implode(',',$oStock).'>';
			if (array_sum($oPrice)) $tmp .= '추가금액<'.implode(',',$oPrice).'>';
			$tmp .= '옵션코드<'.implode(',',$oOptno).'>';
		}

		return array_merge(array_values($rData), $rData);
	}

	### Create XML
	function createXml($gXml)
	{
		if (empty($this->errMsg) === false) return;
		if (is_array($gXml) === false || count($gXml) == 0){
			$this->log("Empty-gXml (Create_XML Before)");
			return;
		}
		$this->log("Create_XML START");

		if (class_exists('XmlWriter_py'))
		{
			$xmlFile = array('xmlReg'=>'', 'xmlMod'=>'');
			foreach ($xmlFile as $k => $filenm)
			{
				if (count($gXml[$k]['item']))
				{
					## Format XML
					$xmlWrite = new XmlWriter_py();
					$xmlWrite->act('result', $gXml[$k]);
					$buffer = $xmlWrite->getXml();
					$this->log(sprintf("method : [%s] Format XML", $k));

					## Create XML-File
					ob_start();
					$xmlFile[$k] = sprintf("interpark_%s_%s.xml", $k, date('ymdHis'));
					$file = $this->logPath . $xmlFile[$k];
					$fp = fopen($file,"w");
					fwrite($fp,$buffer);
					fclose($fp);
					$this->err( ob_get_clean() );
					$this->log(sprintf("method : [%s] Create XML-File", $k));
				}
			}
		}
		else $this->log("Non-existent class XmlWriter_py");

		$this->log("Create_XML END");
		return $xmlFile;
	}

	### Socket to INTERPARK(put Goods)
	function socketInterpark($xmlFile)
	{
		if (empty($this->errMsg) === false) return;
		if (is_array($xmlFile) === false || count($xmlFile) == 0){
			$this->log("Empty-xmlFile (Connection_INTERPARK Before)");
			return;
		}
		$this->log("Connection_INTERPARK START");

		$strXml = '';
		foreach ($xmlFile as $k => $filenm)
		{
			if ($filenm != '')
			{
				if (file_exists($this->logPath . $filenm) === false){
					$this->errMsg = array(
						"log" => array('Non-existent file : ' . $filenm, 'Connection_INTERPARK END'),
						"header" => "XML 파일이 생성되지 않았습니다. log 폴더 퍼미션을 확인하신 후 다시 시도하세요."
						);
					return;
				}

				## Socket
				$method = ($k == 'xmlReg' ? 'registerPrdInfo' : 'updatePrdInfo');
				$interfaceurl = sprintf($this->interfaceurl, $method, ("http://{$this->host}{$this->rootDir}/log/" . $filenm));
				$this->log(sprintf("URL : %s", preg_replace("/^(http|https):\/\/[^\/]*\//", "/", $interfaceurl)));

				ob_start();
				if (class_exists('httpSock'))
				{
					$httpSock = new httpSock($interfaceurl);
					$httpSock->send(true);
				}
				else $this->log("Non-existent class httpSock");
				$this->err( ob_get_clean() );

				## Delete XML-File
				$this->delFile($this->logPath . $filenm);

				$strXml .= $httpSock->resContent;
				if (strpos($httpSock->resContent, "Interpark Partner Support System - 시스템 오류") !== false){
					$this->errMsg = array(
						"log" => array('Interpark Partner Support System - 시스템 오류', 'Connection_INTERPARK END'),
						"header" => "Interpark Partner Support System - 시스템 오류"
						);
					return;
				}

				## Read XML
				unset($xml);
				if (class_exists('StrucXMLParser'))
				{
					$xml = new StrucXMLParser();
					$xml->parse($httpSock->resContent);
					$data = $xml->parseOut();
				}
				else $this->log("Non-existent class StrucXMLParser");

				## Update Result
				ob_start();
				$result = &$data[RESULT][0][child];
				if ($result[TOTALSUCCESSCNT][0][data] > 0)
				{
					$items = &$result[ITEM];
					foreach ($items as $item){
						$field = $item[child];
						if ($field[SUCCESS][0][data] == 'true'){
							$query = "update ".GD_GOODS." set inpk_prdno='" . $field[PRDNO][0][data] . "', inpk_regdt=if(inpk_regdt='0000-00-00 00:00:00', now(), inpk_regdt), inpk_moddt=now() where goodsno='" . $field[ORIGINPRDNO][0][data] . "'";
							$res = $GLOBALS[db]->query($query);
							$this->log(sprintf("Update_Result : goodsno[%s], inpk_prdno[%s] %s", $field[ORIGINPRDNO][0][data], $field[PRDNO][0][data], ($res ? 'True' : 'False')));
						}
					}
				}
				$this->err( $err=ob_get_clean() );
				$this->log("Update_Result : " . ($err == '' ? 'True' : 'False'));
			}
		}

		$this->log("Connection_INTERPARK END");
		return $strXml;
	}

	### Delete XML-File
	function delFile($filePath)
	{
		$res = @unlink($filePath);
		$this->log(sprintf("Delete file : [%s] %s", ($res ? 'True' : 'False'), basename($filePath)));
	}
}

?>