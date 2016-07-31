<?

@include_once dirname(__FILE__) . "/../lib/httpSock.class.php";
@include_once dirname(__FILE__) . "/../lib/parsexmlstruc.class.php";
@include_once dirname(__FILE__) . "/../lib/xmlWriter.class.php";
@include_once dirname(__FILE__) . "/../lib/putLog.class.php";
if(class_exists("Services_JSON")===false) require dirname(__FILE__) . "/../lib/json.class.php";

class e2i_goods_openstyle_api extends putLog
{
	/*상품당 하나의 xml 파일로 생성 해야한다.*/
	var $interfaceurl;	# 인터페이스 URL
	//var $connPath = "http://ipss1.interpark.com/openapi/product/ProductAPIService.do?_method=%s&citeKey=%s&secretKey=%s&dataUrl=%s";
	//var $testPath = "http://ipss1.interpark.com/openapi/product/ProductAPIService.do?_method=%s&citeKey=%s&secretKey=%s&dataUrl=%s";
	var $connPath = "http://ipss1.interpark.com/openapi/product/ProductAPIService.do?_method=%s&citeKey=%s&secretKey=%s&dataUrl=%s";
	var $testPath = "http://ipss1.interpark.com/openapi/product/ProductAPIService.do?_method=%s&citeKey=%s&secretKey=%s&dataUrl=%s";
	var $errMsg;	# 에러메시지
	var $citeKey;	# 상품등록/수정 인증키
	var $secretKey;	# 상품등록/수정 비밀키
	var $interfaceId;	#등록:InsertProductAPIData; 수정 : UpdateProductAPIData


	function e2i_goods_openstyle_api($glist, $part='', $isTest=false)
	{
		$this->putLog('prdInfo');//임시주석
		$this->log("START");//임시주석

		if ($isTest) $this->interfaceurl = $this->testPath;
		else $this->interfaceurl = $this->connPath;

		## 호스트 정의
		$tmp = parse_url($_SERVER['HTTP_HOST']);
		$this->host = ($tmp['host'] ? $tmp['host'] : $_SERVER['HTTP_HOST']);
		## API START
		$this->chkInpkOSCfg();//인증키,비밀키 확인



		//--- 상품 갯수만큼 반복 ---//
		$goodsnoArray = array();
		$p=0;
		foreach ($glist as $goodsno){
			$goodsnoArray[0] = $goodsno;
			switch ($part)
			{
				case "stock": # 재고관련정보만 전송
					$gXml = $this->formatStock($goodsnoArray);
					break;

				default: # 상품전체정보 전송
					$gXml = $this->formatGoods($goodsnoArray);
					break;
			}
			$xmlFile = $this->createXml($gXml);


			$this->strXml .= $this->socketInterpark($xmlFile);//임시주석
			## redefine strXml
			$pattern = array('<?xml version="1.0" encoding="euc-kr"?>', '<result>', '</result>');
			$this->strXml = str_replace($pattern, "", $this->strXml);
			$this->strXml = '<?xml version="1.0" encoding="euc-kr"?><result>' . "\n" . $this->strXml . '</result>';

			if (empty($this->errMsg) === false)
			{
				if ($part == 'stock') $this->log($this->errMsg["log"]);
				else $this->endHeader($this->errMsg);
			}
			$p++;

		}

		$this->log("END");//임시주석
	}

	### load and check to interpark conf
	function chkInpkOSCfg()
	{
		## load interpark conf
		$this->log("Load Interpark_OpenStyle_Conf START");
		if (class_exists('interpark'))
		{
			$interpark = new interpark();//인터파크 정보
			$interpark->getInpkOS();//오픈스타일 정보 불러오기
			$this->rootDir = $interpark->rootDir;
			$this->inpkOSCfg = $interpark->inpkOSCfg;
		}
		else $this->log("Non-existent class interpark");
		$this->log("Load Interpark_OpenStyle_Conf END");

		## check interpark conf
		$this->log("Check Interpark_OpenStyle_Conf START");

		if($this->inpkOSCfg['regiAuthKey']=="" || $this->inpkOSCfg['regiSecKey']==""){

			$this->errMsg = array(
				"log" => array('Empty regiAuthKey', 'Check Interpark_OpenStyle_Conf END'),
				"header" => "인터파크 인증키가 존재하지 않습니다. 인터파크로 문의하세요."
			);
			return;

		}else if($this->inpkOSCfg['modiAuthKey']=="" || $this->inpkOSCfg['modiSecKey']==""){

			$this->errMsg = array(
				"log" => array('Empty regiAuthKey', 'Check Interpark_OpenStyle_Conf END'),
				"header" => "인터파크 인증키가 존재하지 않습니다. 인터파크로 문의하세요."
			);
			return;

		}

		$this->log("Check Interpark_OpenStyle_Conf END");//임시주석
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
					$this->interfaceId="InsertProductAPIData";
				}
				else {
					$mode = 'modify';
					$item = &$xmlMod['item'][];
					$item['sidx'] = count($xmlMod['item']);	# 일련번호
					$this->interfaceId="UpdateProductAPIData";
					$item['prdNo'] = $data['inpk_prdno'];	# 인터파크 상품번호]
				}

				### e나무상품의 필수옵션
				list($price, $stock, $prdOption) = $this->getGoodsOption($data[goodsno], $data[optnm], $data['usestock'], $data['use_option']);//(상품번호, 옵션제목|옵션제목, 재고)

				### e나무상품의 추가옵션
				//$prdAddOption = $this->getGoodsAddOption($data['goodsno'], $data['addoptnm']);//(상품번호,옵션제목)

				/*--------------- 필수 요청 데이터 변경 : Start ---------------*/

				$item['prdStat']="01";																	//상품상태
				$item['shopNo']="0000100000";															//인터파크 상점번호 (default - 0000100000)
				$item['omDispNo']=$data['inpk_dispno'];													//인터파크 전시코드
				$item['prdNm']=strcut($data['goodsnm'],80);
				if(!$data['maker']) $data['maker']='없음';//상품명 - 최대 80byte
				$item['hdelvMafcEntrNm']=$data['maker'];												//제조업체명
				if(!$data['origin']) $data['origin']='없음';
				$item['prdOriginTp']=$data['origin'];													//원산지
				$item['taxTp']=($data['tax'] == '1' ? '01' : '02');										//부가면세상품 - 과세상품:01, 면세상품:02, 영세상품:03
				$item['ordAgeRstrYn']="N";																//고도 고정값..성인용품여부 - 성인용품:Y, 일반용품:N

				//$item['saleStatTp']="";																//판매상태 - 판매중:01, 품절:02, 판매중지:03, 일시품절:05
				{ // 판매상태
					if ($data['usestock'] && $stock==0) $data['runout'] = 1;							//실재고에 따른 자동 품절 처리

					if (!$data['open']) $item['saleStatTp'] = '03';										//판매중지
					else if ($data['runout'] == 1) $item['saleStatTp'] = '02';							//품절
					else $item['saleStatTp'] = '01';													//판매중
					if ($mode == 'register' && $item['saleStatTp'] == '02') $item['saleStatTp'] = '03';
				}

				$item['saleUnitcost']=$price;															//판매가
				$item['saleLmtQty']=($data['usestock'] ? $stock : 99999);								//판매수량 - 99999 개 이하로 입력

				if ($this->interfaceId == 'InsertProductAPIData'){
					$item['saleStrDts'] = date('Ymd');													//판매시작일 yyyyMMdd => 호출당시 날짜
					$item['saleEndDts'] = '99991231';													//판매종료일 (고정값)
				}

				$item['proddelvCostUseYn']="N";															//고도 고정값 상품배송비사용여부 - 상품배송비사용:Y, 업체배송비정책사용:N
				$item['prdrtnCostUseYn']="N";															//상품 반품택배비 사용여부 - 상품반품택배비사용:Y, 업체반품택배비사용:N
				//$item['rtndelvCost']="";																//상품 반품택배비. prdrtnCostUseYn 가 'Y' 일 경우 필수임

				//상품설명
				$item['prdBasisExplanEd']=sprintf("<style type=\"text/css\"><!-- #godoContents {font:12px dotum; color:#000000;} --></style>\n<div id=\"godoContents\">\n%s\n</div>", $data['longdesc']);

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

				//$item['prdPostfix']="";																//뒷문구 - 최대 80byte [임시제거]
				{//키워드
					$keywords = array();
					$tmp = explode(",", $data['keyword']);
					for ($k = 0; $k < count($tmp); $k++){
						if ($k < 4) $keywords[] = $tmp[$k];
					}
					$item['prdKeywd']=implode(",", $keywords);											//쇼핑태그 - 최대 4개까지, 콤마로 구분
				}

				//$item['brandNm']="";//브랜드명
				if ($data['brandno']){
					list($item['brandNm']) = $GLOBALS['db']->fetch("select brandnm from ".GD_GOODS_BRAND." where Sno='{$data[brandno]}'");
				}
				else $item['brandNm'] = '';

				//$item['entrPoint']="";																//업체POINT - 포인트 금액 입력,판매가의 최대 10%까지 가능
				//$item['minOrdQty']="";																//최소구매수량 - 1개 이상 입력 [임시제거]
				//$item['perordRstrQty']="";															//1회당 주문 제한 수량 [임시제거]

				//선택형 옵션 이름을 지정할때 사용. 선택1을 타입, 선택2를 색상으로 하는 경우 타입,사이즈 로 태그설정.지정 하지 않은 경우 선택1,선택2로 기본 지정됨[임시제거]
				$optnmArray = implode(",",explode("|",$data['optnm']));//A타입,B타입
				$item['selOptName']='';//$optnmArray;														//선택형 옵션 이름을 지정

				//선택형 옵션노출 정렬 유형. 옵션이 있을 경우 필수.01-등록순, 02-가나다순. 선택형 옵션만 적용됨.[임시제거]
				$item['optPrirTp']="02";																//선택형 옵션노출 정렬 유형

				$item['prdOption']=$prdOption;															//주문시 선택 사항
				$item['addOption']=$prdAddOption;														//주문시 추가 옵션

				//$item['addQtyUseYn']="";																//상품페이지 추가형 옵션 수량 입력 필드 사용여부 [임시제거]
				//$item['inOpt']="";																	//입력형 옵션. ex) 사은품을 입력하세요. [임시제거]

				//$item['delvCost']="";																	//배송비 -상품 배송비 선택일때 필수, 0이면 무료배송 [임시제거]
				//$item['delvAmtPayTpCom']="";															//배송비 결제 방식 -
				//$item['delvCostApplyTp']="";															//배송비 적용 방식 - 개당:01, 무조건:02 [임시제거]
				//$item['freedelvStdCnt']="";															//무료배송기준 수량 - 기준수량 입력 사용하지 않을 경우 0 [임시제거]
				$item['spcaseEd']=$spcaseEd;															//특이사항
				//$item['intfreeInstmStrDts']="";														//무이자할부시작일 - yyyyMMdd [임시제거]
				//$item['intfreeInstmEndDts']="";														//무이자할부종료일 - yyyyMMdd [임시제거]
				//$item['listInstmMonths']="";															//무이자할부개월수 - 3,6,10,12 개월 선택 입력 [임시제거]
				$item['pointmUseYn']="N";																//포인트몰등록여부 - 포인트몰상품:Y,일반상품:N 500원 미만 상품은 등록 불가
				//$item['ippSubmitYn']="";																//가격비교 등록 여부 -등록함:Y, 등록안함:N
				if ($this->interfaceId == 'InsertProductAPIData'){
					$item['ippSubmitYn'] = ($this->inpkOSCfg['ippSubmitYn'] == 'Y' ? 'Y' : 'N');		// 가격비교 등록 여부
				}

				$item['originPrdNo']=$data['goodsno'];													//원상품번호(제휴업체상품코드)
				//$item['shopDispInfo']="전시타입<2>상점번호<0000100000>전시번호<001410059>";				//전시구성정보 [임시제거]
				$item['detailImg']="";																	//상세이미지 - 상세이미지 URL
				if ($mode == 'modify') $item['imgUpdateYn']="Y"; else $item['imgUpdateYn']="N";   //이미지 update 여부
				/*--------------- 필수 요청 데이터 변경 : End ---------------*/

				$item = array_map("trim", $item);														// 데이터 앞뒤 space 값 들어가지 않도록 주의 필요

				$json = new Services_JSON();
				$data['extra_info'] = $json->decode($data['extra_info']);
				$extra_info_set = array();
				foreach($data['extra_info'] as $extra_info)
				{
					if(isset($extra_info->inpk_code) && strlen($extra_info->inpk_type))
					{
						$extra_info_set[$extra_info->inpk_code] = array(
							'infoCd' => $extra_info->inpk_type,
							'infoTx' => $extra_info->desc
						);
					}
				}
				ksort($extra_info_set);
				$item['asInfo'] = $extra_info_set['INFOAS']['infoTx'];
				unset($extra_info_set['INFOAS']);
				$item['prdinfoNoti'] = array();
				$item['prdinfoNoti']['info'] = array();
				foreach($extra_info_set as $infoSubNo => $extra_info)
				{
					$item['prdinfoNoti']['info'][] = array(
						'infoSubNo' => $infoSubNo,
						'infoCd' => $extra_info['infoCd'],
						'infoTx' => $extra_info['infoTx']
					);
				}
			}//if ($data['goodsno'])
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
				list($price, $stock, $prdOption) = $this->getGoodsOption($data[goodsno], $data[optnm], $data['usestock'], $data['use_option']);

				### e나무상품의 추가옵션
				//$prdAddOption = $this->getGoodsAddOption($data['goodsno'], $data['addoptnm']);//(상품번호,옵션제목)

				/*--------------- 필수 요청 데이터 : Start ---------------*/
				$item['prdNo'] = $data['inpk_prdno'];	# 인터파크 상품번호]
				$item['supplyEntrNo'] = $this->inpkOSCfg['entrNo'];	# 업체번호
				$item['supplyCtrtSeq'] = $this->inpkOSCfg['ctrtSeq'];	# 공급계약일련번호
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

				$optnmArray = implode(",",explode("|",$data['optnm']));//A타입,B타입
				$item['selOptName']="";//$optnmArray;														//선택형 옵션 이름을 지정

				$item['saleUnitcost'] = $price;	# 판매가
				$item['saleLmtQty'] = ($data['usestock'] ? $stock : 99999);	# 판매수량
				$item['prdOption'] = $prdOption; # 주문선택사항
				$item['addOption']=$prdAddOption; # 주문추가옵션
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
	function getGoodsOption($goodsno, $optnm, $usestock, $use_option)
	{

		$rData = array('price'=>'', 'stock'=>'', 'prdOption'=>'');//리턴될 데이터 초기화
		$optnm = explode("|",$optnm);//A타입,B타입

		$query = "select * from ".GD_GOODS_OPTION." where goodsno='{$goodsno}' and go_is_deleted <> '1' and go_is_display = '1'";
		if (!$use_option) {
			$query .= " and link = '1' ";
		}
		$res = $GLOBALS['db']->query($query);
		while ($tmp=$GLOBALS['db']->fetch($res,1)){

			if (!$use_option) {
				$tmp['opt1'] = $tmp['opt2'] = '';
			}

			$opt1[] = $tmp['opt1'];//색상1,색상2
			$opt2[] = $tmp['opt2'];//대,중,소

			if ($tmp['optno'] == ''){ // 옵션코드
				$tmp['optno'] = $tmp['sno'];
				$GLOBALS['db']->query("update ".GD_GOODS_OPTION." set optno=sno where sno='{$tmp[sno]}'");
			}
			$opt[$tmp['opt1']][$tmp['opt2']] = array('price'=>$tmp['price'],'stock'=>$tmp['stock'],'optno'=>$tmp['optno']);
			## 총재고량 계산
			$rData['stock'] += $tmp['stock'];
		}
		if ($opt1) $opt1 = array_unique($opt1);//A타입,B타입
		if ($opt2) $opt2 = array_unique($opt2);//대,중
		if (!$opt) $opt1 = $opt2 = array('');

		## 기본 가격 할당
		$rData['price']	  = $opt[$opt1[0]][$opt2[0]]['price'];

		## 주문선택사항
		if(count($opt)>1 || $opt1[0] != null || $opt2[0] != null)
		{
			$op2=$opt2[0];

			foreach ($opt1 as $op1){

				$tmp = &$rData['prdOption'];
				$tmp .= '{'.$op1.'<';
				$opArray=array();
				$oStock=array();
				$oPrice=array();
				$oOptno=array();
				$useYn = array();

				foreach ($opt2 as $op2){
					if(!$usestock) $oStock[] = '999'; else $oStock[] = $opt[$op1][$op2]['stock'];
					$total_price = $opt[$op1][$op2]['price'] - $rData['price'];
					if($total_price==0) $oPrice[] = '0'; else $oPrice[] = $opt[$op1][$op2]['price'] - $rData['price'];
					$oOptno[] = $opt[$op1][$op2]['optno'];
					//$opArray[] = $op2;
					if($op2) $opArray[] = $op2; else $opArray[]="선택";
					if(!$usestock){
						$useYn[] = "Y";
					}else if(!$opt[$op1][$op2]['stock'] || $total_price<0 || !$opt[$op1][$op2]['optno']){
						$useYn[] = "N";
					}else{
						$useYn[] = "Y";
					}

				}

				$tmp .=implode(',',$opArray).'>';

				if ($usestock && $oStock) $tmp .= '수량<'.implode(',',$oStock).'>';
				else $tmp .= '수량<'.implode(',',$oStock).'>';
				//if (array_sum($oPrice)) $tmp .= '추가금액<'.implode(',',$oPrice).'>';
				$tmp .= '추가금액<'.implode(',',$oPrice).'>';
				$tmp .= '옵션코드<'.implode(',',$oOptno).'>사용여부<'.implode(',',$useYn).'>}';

			}
		}
		return array_merge(array_values($rData), $rData);
	}

	### Option of goods

	function getGoodsAddOption($goodsno,$goodsAddNm)
	{
		global $db;
		### 추가옵션
		$r_addoptnm = explode("|",$goodsAddNm);
		for ($i=0;$i<count($r_addoptnm);$i++){
			list ($addoptnm[],$addoptreq) = explode("^",$r_addoptnm[$i]);
		}

		$query = "select * from ".GD_GOODS_ADD." where goodsno='$goodsno' order by sno";
		$res = $db->query($query);

		while ($tmp=$db->fetch($res)){
			$addopt[$tmp[step]][] = $tmp;
		}

		$returnValue="";
		foreach ($addopt as $k=>$v){

			$returnValue .="{".$addoptnm[$k];
			$opt=array();
			$addprice=array();
			$optcode=array();

			//세부항목
			foreach ($v as $v2){
				$opt[] = $v2['opt'];
				$addprice[] = $v2['addprice'];
				$optcode[]  = $v2['sno'];
			}

			if($opt) $returnValue .= "<".implode(",",$opt).">";
			if($addprice) $returnValue .= "금액<".implode(",",$addprice).">";
			if($optcode) $returnValue .= "옵션코드<".implode(",",$optcode).">";
			$returnValue .= "}";

		}

		return $returnValue;

	}


	function getmicrotime()
	{
		$microtimestmp = split(" ",microtime());
        return str_replace(".","",$microtimestmp[0]+$microtimestmp[1]);

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
					$getmicrotime = explode(" ", microtime());
					$xmlFile[$k] = sprintf("interpark_%s_%s_.xml", $k, date('ymdHis')."_".$this->getmicrotime());
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
			$this->log("Empty-xmlFile (Connection_INTERPARK_OpenStyle Before)");
			return;
		}
		$this->log("Connection_INTERPARK_OpenStyle START");

		$strXml = '';
		foreach ($xmlFile as $k => $filenm)
		{
			if ($filenm != '')
			{
				if (file_exists($this->logPath . $filenm) === false){
					$this->errMsg = array(
						"log" => array('Non-existent file : ' . $filenm, 'Connection_INTERPARK_OpenStyle_xml_fail END'),
						"header" => "XML 파일이 생성되지 않았습니다. log 폴더 퍼미션을 확인하신 후 다시 시도하세요."
						);
					return;
				}

				## Socket
				if($k == 'xmlReg'){
					$method ='InsertProductAPIData';
					$this->citeKey = $this->inpkOSCfg['regiAuthKey'];
					$this->secretKey = $this->inpkOSCfg['regiSecKey'];

				}else{

					$method ='UpdateProductAPIData';
					$this->citeKey = $this->inpkOSCfg['modiAuthKey'];
					$this->secretKey = $this->inpkOSCfg['modiSecKey'];
				}

				//필요한 정보 : ?_method=%s&citeKey=%s&secretKey=%s&dataUrl=%s";
				$interfaceurl = sprintf($this->interfaceurl, $this->interfaceId,$this->citeKey,$this->secretKey, ("http://{$this->host}{$this->rootDir}/log/" . $filenm));
				$this->log(sprintf("URL : %s", preg_replace("/^(http|https):\/\/[^\/]*\//", "/", $interfaceurl)));

				ob_start();
				if (class_exists('httpSock'))
				{
					$this->log("interpark_url = ".$interfaceurl." - interpark_id = ".$this->interfaceId);
					$httpSock = new httpSock($interfaceurl);//[임시주석]
					$httpSock->send(true);//[임시주석]
				}
				else $this->log("Non-existent class httpSock");
				$this->err( ob_get_clean() );

				## Delete XML-File
				$this->delFile($this->logPath . $filenm);//[임시주석]

				$strXml .= $httpSock->resContent;
				if (strpos($httpSock->resContent, "Interpark Partner Support System - 시스템 오류") !== false){
					$this->errMsg = array(
						"log" => array('Interpark Partner Support System - 시스템 오류', 'Connection_INTERPARK_connect_OpenStyle END'),
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
				if ($result[SUCCESS])
				{
					$items = &$result[SUCCESS];
					foreach ($items as $item){
						$field = $item[child];
						if ($field[PRDNO][0][data]){

							$query = "update ".GD_GOODS." set inpk_prdno='" . $field[PRDNO][0][data] . "', inpk_regdt=if(inpk_regdt='0000-00-00 00:00:00', now(), inpk_regdt), inpk_moddt=now() where goodsno='" . $field[ORIGINPRDNO][0][data] . "'";
							$res = $GLOBALS[db]->query($query);
							$this->log(sprintf("Update_Result : goodsno[%s], inpk_prdno[%s] %s", $field[ORIGINPRDNO][0][data], $field[PRDNO][0][data], ($res ? 'True' : 'False')));
						}
					}

					$this->log("StrucXMLParser success");

				}else{
					$items = &$result[ERROR];
					foreach ($items as $item){
						$field = $item[child];
						$this->log("StrucXMLParser fail code = ".$field[CODE][0][data]);
						$this->log("StrucXMLParser fail message = ".$field[MESSAGE][0][data]);
						$this->log("StrucXMLParser fail message = ".$field[EXPLANATION][0][data]);
					}
					$this->log("StrucXMLParser fail");
				}

				$this->err( $err=ob_get_clean() );
				$this->log("StrucXMLParser end");

				$this->log("Update_Result : " . ($err == '' ? 'True' : 'False'));
			}
		}

		$this->log("Connection_INTERPARK_OpenStyle END");
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
