<?

@include_once dirname(__FILE__) . "/../lib/httpSock.class.php";
@include_once dirname(__FILE__) . "/../lib/parsexmlstruc.class.php";
@include_once dirname(__FILE__) . "/../lib/putLog.class.php";

class e2i_order_api extends putLog
{
	var $callCnt = 0;	# API 호출 횟수
	var $recallMax = 2;	# API 재호출 최대횟수
	var $interfaceid;	# 인터페이스 ID
	var $interfaceurl = 'https://godointerpark.godo.co.kr/e2i.OpenStyle_orderApi.php?method=%s';	# 인터페이스 URL

	function e2i_order_api($interfaceid, $data)
	{

		if (is_array($data)) $data = array_map("trim",$data);
		$this->putLog('e2iOrderApi');
		$this->log("START");

		## 인터페이스 ID 정의
		//$this->interfaceid = $interfaceid;
		$this->interfaceid = str_replace("openstyle_","",$interfaceid);

		## 인터페이스 URL 정의
		$this->interfaceurl = sprintf($this->interfaceurl, $interfaceid);

		## API START
		switch ($this->interfaceid)
		{
			case "delvCompForComm": # 배송시작
				$this->delvCompForComm($data);
				break;

			case "cnclOutOfStockReqForComm": # 품절주문취소요청
				$this->cnclOutOfStockReqForComm($data);
				break;

			case "cnclReqAcceptForComm": # 주문취소요청승인
				$this->cnclReqAcceptForComm($data);
				break;

			case "rtnReqAcceptForComm": # 반품요청승인
				$this->rtnReqAcceptForComm($data);
				break;

			case "exchReqAcceptForComm": # 교환요청승인
				$this->exchReqAcceptForComm($data);
				break;

			case "rtnReqRefuseForComm": # 반품요청거부
				$this->rtnReqRefuseForComm($data);
				break;

			case "exchReqRefuseForComm": # 교환요청거부
				$this->exchReqRefuseForComm($data);
				break;

			case "clmEnterWhCompForComm": # 반품/교환입고확정
				$this->clmEnterWhCompForComm($data);
				break;

			case "exchOutWhCompForComm": # 교환확정
				$this->exchOutWhCompForComm($data);
				break;
		}

		$this->log("END");
	}

	### Parse XML
    function parseXml($strXml)
    {
		if (trim($strXml) == ''){
			$this->log("Empty-strXml (Parsing Before)");
			return;
		}
		$this->log("Parse_XML START");

		if (class_exists('StrucXMLParser'))
		{
			$xml = new StrucXMLParser();
			$xml->detailStruc = false;
			$xml->parse($strXml);
			$arrXml = $xml->parseOut();
		}
		else $this->log("Non-existent class StrucXMLParser");

		$this->log("Parse_XML END");
		if (is_array($arrXml) === false || count($arrXml) == 0){
			$this->log("Empty-arrXml (Parsing After)");
			return;
		}

		if ($arrXml['ORDER_LIST']['RESULT']){
			$this->log(sprintf("INTERPARK Result : [CODE] %s", $arrXml['ORDER_LIST']['RESULT']['CODE']));
			$this->log(sprintf("INTERPARK Result : [MESSAGE] %s", $arrXml['ORDER_LIST']['RESULT']['MESSAGE']));
			$this->log(sprintf("INTERPARK Result : [LOG_SEQ] %s", $arrXml['ORDER_LIST']['RESULT']['LOG_SEQ']));
		}

		return $arrXml;
    }

	### 배송시작
	function delvCompForComm($data)
	{
		$data['ordno'] = trim($data['ordno']);
		$this->log("openstyle delvCompForComm START");

		$gOrder = $GLOBALS['db']->fetch("select ordno, inflow, step, inpk_ordno, ddt, deliveryno, deliverycode, settlelog from ".GD_ORDER." where ordno='{$data['ordno']}'");
		if ($gOrder['ordno'] == ''){
			$this->log(sprintf("Fail : Non-existent order [%s]", $data['ordno']));
		}
		else if ($gOrder['inflow'] != 'openstyle'){
			$this->log("Fail : Not order of openstye");
		}
		else if ($gOrder['step'] != '3'){
			$this->log("Fail : 주문상태 != 배송중(3)");
		}
		else if (class_exists('httpSock') === false){
			$this->log("Non-existent class httpSock");
		}
		else {
			## 전송
			$settlelog = array();
			$gOrder['ddt'] = substr(str_replace("-", "", $gOrder['ddt']), 0, 8);

			$res = $GLOBALS['db']->query("select sno, goodsno, inpk_ordseq, inpk_prdno, dvno, dvcode from ".GD_ORDER_ITEM." where ordno='{$data['ordno']}' and istep='{$gOrder['step']}'");
			$resChild = array('TOT' => $GLOBALS['db']->count_($res), 'T' => 0);
			while ($gItem = $GLOBALS['db']->fetch($res))
			{
				// orderclm_no=주문번호&ord_seq=주문순번&delv_dt=출고완료일자&delv_comp=택배사코드&delv_no=운송장번호
				// sc.ordclmNo=주문번호&sc.ordSeq=주문순번&&sc.delvDt=yyyymmdd출고완료일자&sc.delvEntrNo=택배사코드&sc.invoNo=운송장번호&optPrdTp=옵션상품유형&sc.optOrdSeqList=주문순번리스트

				$gItem['dvno'] = ($gItem['dvno'] ? $gItem['dvno'] : $gOrder['deliveryno']);
				$gItem['dvcode'] = trim($gItem['dvcode'] ? $gItem['dvcode'] : $gOrder['deliverycode']);

				//일반 단품상품일 경우
				$optPrdTp = "01";
				$url = sprintf("%s&orderclm_no=%s&ord_seq=%s&delv_dt=%s&delv_comp=%s&delv_no=%s&optPrdTp=%s&optOrdSeqList=%s", $this->interfaceurl, $gOrder['inpk_ordno'], $gItem['inpk_ordseq'], $gOrder['ddt'], $gItem['dvno'], $gItem['dvcode'],'01',$gItem['inpk_ordseq']);
				$this->log(sprintf("URL : %s", preg_replace("/^(http|https):\/\/[^\/]*\//", "/", $url)));

				ob_start();
				$httpSock = new httpSock($url);
				$httpSock->send(true);
				$this->err( ob_get_clean() );

				$arrXml = $this->parseXml(str_replace("-- End --", "", $httpSock->resContent));
				if (in_array($arrXml['ORDER_LIST']['RESULT']['CODE'], array('000', '015')) === true){
					$resChild['T']++;
					$settlelog[] = sprintf("- True : 상품 [e:%s] [i:%s]", $gItem['goodsno'], $gItem['inpk_prdno']);
					unset($msg);
					$msg[] = "Result [";
					$msg[] = sprintf("\t결과 = %s", 'True');
					$msg[] = sprintf("\t인터파크 주문번호 = %s", $gItem['inpk_ordno']);
					$msg[] = sprintf("\t인터파크 주문순번 = %s", $gItem['inpk_ordseq']);
					$msg[] = sprintf("\t인터파크 상품코드 = %s", $gItem['inpk_prdno']);
					$msg[] = sprintf("\t이나무 주문번호 = %s", $data['ordno']);
					$msg[] = sprintf("\t이나무 주문순번 = %s", $gItem['sno']);
					$msg[] = sprintf("\t이나무 고유번호 = %s", $gItem['goodsno']);
					$msg[] = "]";
					$this->log($msg);

					## 클레임요청리스트 체크(거부처리)
					list($itmsno) = $GLOBALS['db']->fetch("select itmsno from ".INPK_CLAIM." c left join ".INPK_CLAIM_ITEM." i on c.clmsno=i.clmsno where c.step='r' and c.clm_tpnm='출고전주문취소' and i.clm_statnm='요청' and i.item_sno='{$gItem['sno']}'");
					if ($itmsno){
						# 클레임요청상태 업데이트
						$query = "update ".INPK_CLAIM_ITEM." set clm_statnm='거부', latedt=now() where itmsno='{$itmsno}'";
						$GLOBALS['db']->query($query);
						# 로그추가
						$query = "insert into ".INPK_CLAIM_ITEM_LOG." values ('', '{$itmsno}', '요청 > 거부', now())";
						$GLOBALS['db']->query($query);
					}
				}
				else {
					$settlelog[] = sprintf("- False : 상품 [e:%s] [i:%s]", $gItem['goodsno'], $gItem['inpk_prdno']);
					unset($msg);
					$msg[] = "Result [";
					$msg[] = sprintf("\t결과 = %s", 'False');
					$msg[] = sprintf("\t인터파크 주문번호 = %s", $gItem['inpk_ordno']);
					$msg[] = sprintf("\t인터파크 주문순번 = %s", $gItem['inpk_ordseq']);
					$msg[] = sprintf("\t인터파크 상품코드 = %s", $gItem['inpk_prdno']);
					$msg[] = sprintf("\t이나무 주문번호 = %s", $data['ordno']);
					$msg[] = sprintf("\t이나무 주문순번 = %s", $gItem['sno']);
					$msg[] = sprintf("\t이나무 고유번호 = %s", $gItem['goodsno']);
					$msg[] = sprintf("\t결과코드 = %s", $arrXml['ORDER_LIST']['RESULT']['CODE']);
					$msg[] = sprintf("\t결과메세지 = %s", $arrXml['ORDER_LIST']['RESULT']['MESSAGE']);
					$msg[] = "]";
					$this->log($msg);
				}
			}
			if ($resChild['T'] == 0) ctlStep($data['ordno'],$data['preStep'],$data['stock']);

			## 처리결과출력
			$this->log(sprintf("Result_Cnt (resChild) : Total[%s], True[%s], False[%s]", $resChild['TOT'], $resChild['T'], ($resChild['TOT'] - $resChild['T'])));

			## 결제로그저장
			if (count($settlelog)){
				$settlelog = sprintf("%s\n[%s] 인터파크 배송시작처리\n%s\n", $gOrder['settlelog'], date('y-m-d H:i:s'), implode("\n", $settlelog));
				$GLOBALS['db']->query("update ".GD_ORDER." set settlelog='{$settlelog}' where ordno='{$data['ordno']}'");
			}
		}

		$this->log("delvCompForComm END");
	}

	### 품절주문취소요청
	function cnclOutOfStockReqForComm($data)
	{
		$this->callCnt++;
		$data['ordno'] = trim($data['ordno']);
		$this->log("cnclOutOfStockReqForComm START");

		$gOrder = $GLOBALS['db']->fetch("select o.ordno, o.inpk_ordno, o.inflow, o.step, i.sno, i.inpk_ordseq, i.goodsno, i.inpk_prdno, i.ea from ".GD_ORDER." o left join ".GD_ORDER_ITEM." i on o.ordno=i.ordno where i.ordno='{$data['ordno']}' and i.sno='{$data['sno']}'");
		if ($gOrder['inpk_ordno'] == ''){
			$this->log(sprintf("Fail : Non-existent order [ordno:%s, sno:%s]", $data['ordno'], $data['sno']));
		}
		else if ($gOrder['inflow'] != 'openstyle'){
			$this->log("Fail : Not order of openstyle");
		}
		else if ($gOrder['step'] != '2'){
			$this->log("Fail : 주문상태 != 배송준비중(2)");
		}
		else if (class_exists('httpSock') === false){
			$this->log("Non-existent class httpSock");
		}
		else {
			## 전송
			$settlelog = array();
			$resChild = array('TOT' => 1, 'T' => 0);

			// orderclm_no=주문번호&ord_seq=주문순번
			$url = sprintf("%s&orderclm_no=%s&ord_seq=%s", $this->interfaceurl, $gOrder['inpk_ordno'], $gOrder['inpk_ordseq']);
			$this->log(sprintf("URL : %s", preg_replace("/^(http|https):\/\/[^\/]*\//", "/", $url)));

			ob_start();
			$httpSock = new httpSock($url);
			$httpSock->send(true);
			$this->err( ob_get_clean() );

			$arrXml = $this->parseXml(str_replace("-- End --", "", $httpSock->resContent));
			if (in_array($arrXml['ORDER_LIST']['RESULT']['CODE'], array('000', '005')) === true){
				## 클레임요청으로 추가
				$query = "
				insert into ".INPK_CLAIM." set
					step				= 'r',
					ordno				= '{$gOrder['ordno']}',
					inpk_ordno			= '{$gOrder['inpk_ordno']}',
					clm_tpnm			= '출고전품절주문취소',
					regdt 				= now()
				";
				$res = $GLOBALS['db']->query($query);
				$clmsno = $GLOBALS['db']->lastID();
				$query = "
				insert into ".INPK_CLAIM_ITEM." set
					clmsno			= '{$clmsno}',
					item_sno		= '{$gOrder['sno']}',
					goodsno			= '{$gOrder['goodsno']}',
					inpk_prdno		= '{$gOrder['inpk_prdno']}',
					clm_qty			= '{$gOrder['ea']}',
					inpk_ordseq		= '{$gOrder['inpk_ordseq']}',
					clm_statnm		= '요청완료',
					clm_dt			= now(),
					clm_rsn_tpnm	= '판매자품절',
					clm_rsn_dtl		= '판매자(상점)측에서 품절주문취소를 요청하였습니다.',
					latedt			= now()
				";
				$res = $GLOBALS['db']->query($query);

				$settlelog[] = sprintf("- True : 상품 [e:%s] [i:%s]", $gOrder['goodsno'], $gOrder['inpk_prdno']);
				$resChild['T']++;
				unset($msg);
				$msg[] = "Result [";
				$msg[] = sprintf("\t결과 = %s", 'True');
				$msg[] = sprintf("\t인터파크 주문번호 = %s", $gOrder['inpk_ordno']);
				$msg[] = sprintf("\t인터파크 주문순번 = %s", $gOrder['inpk_ordseq']);
				$msg[] = sprintf("\t인터파크 상품코드 = %s", $gOrder['inpk_prdno']);
				$msg[] = sprintf("\t이나무 주문번호 = %s", $gOrder['ordno']);
				$msg[] = sprintf("\t이나무 주문순번 = %s", $gOrder['sno']);
				$msg[] = sprintf("\t이나무 고유번호 = %s", $gOrder['goodsno']);
				$msg[] = "]";
				$this->log($msg);
			}
			else {
				$settlelog[] = sprintf("- False : 상품 [e:%s] [i:%s]", $gOrder['goodsno'], $gOrder['inpk_prdno']);
				unset($msg);
				$msg[] = "Result [";
				$msg[] = sprintf("\t결과 = %s", 'False');
				$msg[] = sprintf("\t인터파크 주문번호 = %s", $gOrder['inpk_ordno']);
				$msg[] = sprintf("\t인터파크 주문순번 = %s", $gOrder['inpk_ordseq']);
				$msg[] = sprintf("\t인터파크 상품코드 = %s", $gOrder['inpk_prdno']);
				$msg[] = sprintf("\t이나무 주문번호 = %s", $gOrder['ordno']);
				$msg[] = sprintf("\t이나무 주문순번 = %s", $gOrder['sno']);
				$msg[] = sprintf("\t이나무 고유번호 = %s", $gOrder['goodsno']);
				$msg[] = sprintf("\t결과코드 = %s", $arrXml['ORDER_LIST']['RESULT']['CODE']);
				$msg[] = sprintf("\t결과메세지 = %s", $arrXml['ORDER_LIST']['RESULT']['MESSAGE']);
				$msg[] = "]";
				$this->log($msg);

				# API 재호출여부
				if (in_array($arrXml['ORDER_LIST']['RESULT']['CODE'], array('001', '002'))){
					$isRecall = true;
				}
			}

			## 처리결과출력
			$this->log(sprintf("Result_Cnt (resChild) : Total[%s], True[%s], False[%s]", $resChild['TOT'], $resChild['T'], ($resChild['TOT'] - $resChild['T'])));
			if ($isRecall != true || $this->callCnt == $this->recallMax){
				echo ($resChild['T'] ? 'succeed' : 'fail:' . $arrXml['ORDER_LIST']['RESULT']['MESSAGE']); // ajax read
			}

			## 결제로그저장
			if (count($settlelog)){
				$settlelog = sprintf("\n[%s] 인터파크 품절주문취소요청\n%s\n", date('y-m-d H:i:s'), implode("\n", $settlelog));
				$GLOBALS['db']->query("update ".GD_ORDER." set settlelog=concat(settlelog, '{$settlelog}') where ordno='{$gOrder['ordno']}'");
			}
		}

		$this->log("cnclOutOfStockReqForComm END");

		## API 재호출
		if ($isRecall == true && $this->callCnt < $this->recallMax){
			$this->cnclOutOfStockReqForComm($data);
		}
	}

	### 주문취소요청승인
	function cnclReqAcceptForComm($data)
	{
		$this->callCnt++;
		$this->log("cnclReqAcceptForComm START");

		$gClaim = $GLOBALS['db']->fetch("select c.clm_seq, c.inpk_ordno, c.ordno, i.* from ".INPK_CLAIM." c left join ".INPK_CLAIM_ITEM." i on c.clmsno=i.clmsno where i.clmsno='{$data['clmsno']}' and i.itmsno='{$data['itmsno']}'");
		if ($gClaim['clm_seq'] == ''){
			$this->log(sprintf("Fail : Non-existent claim [clmsno:%s, itmsno:%s]", $data['clmsno'], $data['itmsno']));
		}
		else if ($gClaim['clm_statnm'] != '요청'){
			$this->log("Fail : 클레임요청상태 != 요청");
		}
		else if (class_exists('httpSock') === false){
			$this->log("Non-existent class httpSock");
		}
		else {
			## 전송
			$settlelog = array();
			$resChild = array('TOT' => 1, 'T' => 0);

			// orderclm_no=주문번호&ord_seq=주문순번&clmreq_seq=주문취소요청순번
			$url = sprintf("%s&orderclm_no=%s&ord_seq=%s&clmreq_seq=%s", $this->interfaceurl, $gClaim['inpk_ordno'], $gClaim['inpk_ordseq'], $gClaim['clm_seq']);
			$this->log(sprintf("URL : %s", preg_replace("/^(http|https):\/\/[^\/]*\//", "/", $url)));

			ob_start();
			$httpSock = new httpSock($url);
			$httpSock->send(true);
			$this->err( ob_get_clean() );

			$arrXml = $this->parseXml(str_replace("-- End --", "", $httpSock->resContent));
			if (in_array($arrXml['ORDER_LIST']['RESULT']['CODE'], array('000', '006')) === true){
				# 클레임요청상태 업데이트
				$query = "update ".INPK_CLAIM_ITEM." set clm_statnm='승인', latedt=now() where clmsno='{$data['clmsno']}' and itmsno='{$data['itmsno']}'";
				$GLOBALS['db']->query($query);
				# 로그추가
				$query = "insert into ".INPK_CLAIM_ITEM_LOG." values ('', '{$data['itmsno']}', '요청 > 승인', now())";
				$GLOBALS['db']->query($query);

				$settlelog[] = sprintf("- True : 상품 [e:%s] [i:%s]", $gClaim['goodsno'], $gClaim['inpk_prdno']);
				$resChild['T']++;
				unset($msg);
				$msg[] = "Result [";
				$msg[] = sprintf("\t결과 = %s", 'True');
				$msg[] = sprintf("\t주문취소요청순번 = %s", $gClaim['clm_seq']);
				$msg[] = sprintf("\t인터파크 주문번호 = %s", $gClaim['inpk_ordno']);
				$msg[] = sprintf("\t인터파크 주문순번 = %s", $gClaim['inpk_ordseq']);
				$msg[] = sprintf("\t인터파크 상품코드 = %s", $gClaim['inpk_prdno']);
				$msg[] = sprintf("\t이나무 주문번호 = %s", $gClaim['ordno']);
				$msg[] = sprintf("\t이나무 주문순번 = %s", $gClaim['item_sno']);
				$msg[] = sprintf("\t이나무 고유번호 = %s", $gClaim['goodsno']);
				$msg[] = "]";
				$this->log($msg);
			}
			else {
				$settlelog[] = sprintf("- False : 상품 [e:%s] [i:%s]", $gClaim['goodsno'], $gClaim['inpk_prdno']);
				unset($msg);
				$msg[] = "Result [";
				$msg[] = sprintf("\t결과 = %s", 'False');
				$msg[] = sprintf("\t주문취소요청순번 = %s", $gClaim['clm_seq']);
				$msg[] = sprintf("\t인터파크 주문번호 = %s", $gClaim['inpk_ordno']);
				$msg[] = sprintf("\t인터파크 주문순번 = %s", $gClaim['inpk_ordseq']);
				$msg[] = sprintf("\t인터파크 상품코드 = %s", $gClaim['inpk_prdno']);
				$msg[] = sprintf("\t이나무 주문번호 = %s", $gClaim['ordno']);
				$msg[] = sprintf("\t이나무 주문순번 = %s", $gClaim['item_sno']);
				$msg[] = sprintf("\t이나무 고유번호 = %s", $gClaim['goodsno']);
				$msg[] = sprintf("\t결과코드 = %s", $arrXml['ORDER_LIST']['RESULT']['CODE']);
				$msg[] = sprintf("\t결과메세지 = %s", $arrXml['ORDER_LIST']['RESULT']['MESSAGE']);
				$msg[] = "]";
				$this->log($msg);

				# API 재호출여부
				if (in_array($arrXml['ORDER_LIST']['RESULT']['CODE'], array('001', '002'))){
					$isRecall = true;
				}

				# 결과코드가 004 이면 요청철회로 처리
				if ($arrXml['ORDER_LIST']['RESULT']['CODE'] == '004'){
					# 클레임요청상태 업데이트
					$query = "update ".INPK_CLAIM_ITEM." set clm_statnm='요청철회', latedt=now() where clmsno='{$data['clmsno']}' and itmsno='{$data['itmsno']}'";
					$GLOBALS['db']->query($query);
					# 로그추가
					$query = "insert into ".INPK_CLAIM_ITEM_LOG." values ('', '{$data['itmsno']}', '요청 > 요청철회', now())";
					$GLOBALS['db']->query($query);
				}
			}

			## 처리결과출력
			$this->log(sprintf("Result_Cnt (resChild) : Total[%s], True[%s], False[%s]", $resChild['TOT'], $resChild['T'], ($resChild['TOT'] - $resChild['T'])));
			if ($isRecall != true || $this->callCnt == $this->recallMax){
				echo ($resChild['T'] ? 'succeed' : 'fail:' . $arrXml['ORDER_LIST']['RESULT']['MESSAGE']); // ajax read
			}

			## 결제로그저장
			if (count($settlelog)){
				$settlelog = sprintf("\n[%s] 인터파크 주문취소요청승인\n%s\n", date('y-m-d H:i:s'), implode("\n", $settlelog));
				$GLOBALS['db']->query("update ".GD_ORDER." set settlelog=concat(settlelog, '{$settlelog}') where ordno='{$gClaim['ordno']}'");
			}
		}

		$this->log("cnclReqAcceptForComm END");

		## API 재호출
		if ($isRecall == true && $this->callCnt < $this->recallMax){
			$this->cnclReqAcceptForComm($data);
		}
	}

	### 반품요청승인
	function rtnReqAcceptForComm($data)
	{
		$this->callCnt++;
		$this->log("rtnReqAcceptForComm START");

		$gClaim = $GLOBALS['db']->fetch("select clm_seq, inpk_ordno, ordno from ".INPK_CLAIM." where clmsno='{$data['clmsno']}'");
		list($gClaim['clm_statnm']) = $GLOBALS['db']->fetch("select clm_statnm from ".INPK_CLAIM_ITEM." where clmsno='{$data['clmsno']}' limit 1");
		if ($gClaim['clm_seq'] == ''){
			$this->log(sprintf("Fail : Non-existent claim [clmsno:%s]", $data['clmsno']));
		}
		else if ($gClaim['clm_statnm'] != '요청'){
			$this->log("Fail : 클레임요청상태 != 요청");
		}
		else if (class_exists('httpSock') === false){
			$this->log("Non-existent class httpSock");
		}
		else {
			## 전송
			$settlelog = array();
			$resChild = array('TOT' => 1, 'T' => 0);

			// orderclm_no=주문번호&clmreq_seq=주문취소요청순번&return_mthd_tp=수거방법코드
			$url = sprintf("%s&orderclm_no=%s&clmreq_seq=%s&return_mthd_tp=%s", $this->interfaceurl, $gClaim['inpk_ordno'], $gClaim['clm_seq'], $data['return_mthd_tp']);
			$this->log(sprintf("URL : %s", preg_replace("/^(http|https):\/\/[^\/]*\//", "/", $url)));

			ob_start();
			$httpSock = new httpSock($url);
			$httpSock->send(true);
			$this->err( ob_get_clean() );

			$arrXml = $this->parseXml(str_replace("-- End --", "", $httpSock->resContent));
			if (in_array($arrXml['ORDER_LIST']['RESULT']['CODE'], array('000', '008')) === true){
				$GLOBALS['db']->query("update ".INPK_CLAIM." set accept_return_mthd_tp='{$data['return_mthd_tp']}' where clmsno='{$data['clmsno']}'");
				$resItem = $GLOBALS['db']->query("select * from ".INPK_CLAIM_ITEM." where clmsno='{$data['clmsno']}'");
				while ($item = $GLOBALS['db']->fetch($resItem))
				{
					$settlelog[] = sprintf("- True : 상품 [e:%s] [i:%s]", $item['goodsno'], $item['inpk_prdno']);
					# 클레임요청상태 업데이트
					$query = "update ".INPK_CLAIM_ITEM." set clm_statnm='승인', latedt=now() where itmsno='{$item['itmsno']}'";
					$GLOBALS['db']->query($query);
					# 로그추가
					$query = "insert into ".INPK_CLAIM_ITEM_LOG." values ('', '{$item['itmsno']}', '요청 > 승인', now())";
					$GLOBALS['db']->query($query);
				}

				$resChild['T']++;
				unset($msg);
				$msg[] = "Result [";
				$msg[] = sprintf("\t결과 = %s", 'True');
				$msg[] = sprintf("\t주문취소요청순번 = %s", $gClaim['clm_seq']);
				$msg[] = sprintf("\t인터파크 주문번호 = %s", $gClaim['inpk_ordno']);
				$msg[] = sprintf("\t이나무 주문번호 = %s", $gClaim['ordno']);
				$msg[] = "]";
				$this->log($msg);
			}
			else {
				$settlelog[] = sprintf("- False : 상품 [e:%s] [i:%s]", $gClaim['goodsno'], $gClaim['inpk_prdno']);
				unset($msg);
				$msg[] = "Result [";
				$msg[] = sprintf("\t결과 = %s", 'False');
				$msg[] = sprintf("\t주문취소요청순번 = %s", $gClaim['clm_seq']);
				$msg[] = sprintf("\t인터파크 주문번호 = %s", $gClaim['inpk_ordno']);
				$msg[] = sprintf("\t이나무 주문번호 = %s", $gClaim['ordno']);
				$msg[] = sprintf("\t결과코드 = %s", $arrXml['ORDER_LIST']['RESULT']['CODE']);
				$msg[] = sprintf("\t결과메세지 = %s", $arrXml['ORDER_LIST']['RESULT']['MESSAGE']);
				$msg[] = "]";
				$this->log($msg);

				# API 재호출여부
				if (in_array($arrXml['ORDER_LIST']['RESULT']['CODE'], array('001', '002'))){
					$isRecall = true;
				}

				# 결과코드가 004 이면 요청철회로 처리
				if ($arrXml['ORDER_LIST']['RESULT']['CODE'] == '004'){
					$resItem = $GLOBALS['db']->query("select * from ".INPK_CLAIM_ITEM." where clmsno='{$data['clmsno']}'");
					while ($item = $GLOBALS['db']->fetch($resItem))
					{
						# 클레임요청상태 업데이트
						$query = "update ".INPK_CLAIM_ITEM." set clm_statnm='요청철회', latedt=now() where itmsno='{$item['itmsno']}'";
						$GLOBALS['db']->query($query);
						# 로그추가
						$query = "insert into ".INPK_CLAIM_ITEM_LOG." values ('', '{$item['itmsno']}', '요청 > 요청철회', now())";
						$GLOBALS['db']->query($query);
					}
				}
			}

			## 처리결과출력
			$this->log(sprintf("Result_Cnt (resChild) : Total[%s], True[%s], False[%s]", $resChild['TOT'], $resChild['T'], ($resChild['TOT'] - $resChild['T'])));
			if ($isRecall != true || $this->callCnt == $this->recallMax){
				echo ($resChild['T'] ? 'succeed' : 'fail:' . $arrXml['ORDER_LIST']['RESULT']['MESSAGE']); // ajax read
			}

			## 결제로그저장
			if (count($settlelog)){
				$settlelog = sprintf("\n[%s] 인터파크 반품요청승인\n%s\n", date('y-m-d H:i:s'), implode("\n", $settlelog));
				$GLOBALS['db']->query("update ".GD_ORDER." set settlelog=concat(settlelog, '{$settlelog}') where ordno='{$gClaim['ordno']}'");
			}
		}

		$this->log("rtnReqAcceptForComm END");

		## API 재호출
		if ($isRecall == true && $this->callCnt < $this->recallMax){
			$this->rtnReqAcceptForComm($data);
		}
	}

	### 교환요청승인
	function exchReqAcceptForComm($data)
	{
		$this->callCnt++;
		$this->log("exchReqAcceptForComm START");

		$gClaim = $GLOBALS['db']->fetch("select clm_seq, inpk_ordno, ordno from ".INPK_CLAIM." where clmsno='{$data['clmsno']}'");
		list($gClaim['clm_statnm']) = $GLOBALS['db']->fetch("select clm_statnm from ".INPK_CLAIM_ITEM." where clmsno='{$data['clmsno']}' limit 1");
		if ($gClaim['clm_seq'] == ''){
			$this->log(sprintf("Fail : Non-existent claim [clmsno:%s]", $data['clmsno']));
		}
		else if ($gClaim['clm_statnm'] != '요청'){
			$this->log("Fail : 클레임요청상태 != 요청");
		}
		else if (class_exists('httpSock') === false){
			$this->log("Non-existent class httpSock");
		}
		else {
			## 전송
			$settlelog = array();
			$resChild = array('TOT' => 1, 'T' => 0);

			// orderclm_no=주문번호&clmreq_seq=주문취소요청순번&return_mthd_tp=수거방법코드
			$url = sprintf("%s&orderclm_no=%s&clmreq_seq=%s&return_mthd_tp=%s", $this->interfaceurl, $gClaim['inpk_ordno'], $gClaim['clm_seq'], $data['return_mthd_tp']);
			$this->log(sprintf("URL : %s", preg_replace("/^(http|https):\/\/[^\/]*\//", "/", $url)));

			ob_start();
			$httpSock = new httpSock($url);
			$httpSock->send(true);
			$this->err( ob_get_clean() );

			$arrXml = $this->parseXml(str_replace("-- End --", "", $httpSock->resContent));
			if (in_array($arrXml['ORDER_LIST']['RESULT']['CODE'], array('000', '010')) === true){
				$GLOBALS['db']->query("update ".INPK_CLAIM." set accept_return_mthd_tp='{$data['return_mthd_tp']}' where clmsno='{$data['clmsno']}'");
				$resItem = $GLOBALS['db']->query("select * from ".INPK_CLAIM_ITEM." where clmsno='{$data['clmsno']}'");
				while ($item = $GLOBALS['db']->fetch($resItem))
				{
					$settlelog[] = sprintf("- True : 상품 [e:%s] [i:%s]", $item['goodsno'], $item['inpk_prdno']);
					# 클레임요청상태 업데이트
					$query = "update ".INPK_CLAIM_ITEM." set clm_statnm='승인', latedt=now() where itmsno='{$item['itmsno']}'";
					$GLOBALS['db']->query($query);
					# 로그추가
					$query = "insert into ".INPK_CLAIM_ITEM_LOG." values ('', '{$item['itmsno']}', '요청 > 승인', now())";
					$GLOBALS['db']->query($query);
				}

				$resChild['T']++;
				unset($msg);
				$msg[] = "Result [";
				$msg[] = sprintf("\t결과 = %s", 'True');
				$msg[] = sprintf("\t주문취소요청순번 = %s", $gClaim['clm_seq']);
				$msg[] = sprintf("\t인터파크 주문번호 = %s", $gClaim['inpk_ordno']);
				$msg[] = sprintf("\t이나무 주문번호 = %s", $gClaim['ordno']);
				$msg[] = "]";
				$this->log($msg);
			}
			else {
				$settlelog[] = sprintf("- False : 상품 [e:%s] [i:%s]", $gClaim['goodsno'], $gClaim['inpk_prdno']);
				unset($msg);
				$msg[] = "Result [";
				$msg[] = sprintf("\t결과 = %s", 'False');
				$msg[] = sprintf("\t주문취소요청순번 = %s", $gClaim['clm_seq']);
				$msg[] = sprintf("\t인터파크 주문번호 = %s", $gClaim['inpk_ordno']);
				$msg[] = sprintf("\t이나무 주문번호 = %s", $gClaim['ordno']);
				$msg[] = sprintf("\t결과코드 = %s", $arrXml['ORDER_LIST']['RESULT']['CODE']);
				$msg[] = sprintf("\t결과메세지 = %s", $arrXml['ORDER_LIST']['RESULT']['MESSAGE']);
				$msg[] = "]";
				$this->log($msg);

				# API 재호출여부
				if (in_array($arrXml['ORDER_LIST']['RESULT']['CODE'], array('001', '002'))){
					$isRecall = true;
				}

				# 결과코드가 004 이면 요청철회로 처리
				if ($arrXml['ORDER_LIST']['RESULT']['CODE'] == '004'){
					$resItem = $GLOBALS['db']->query("select * from ".INPK_CLAIM_ITEM." where clmsno='{$data['clmsno']}'");
					while ($item = $GLOBALS['db']->fetch($resItem))
					{
						# 클레임요청상태 업데이트
						$query = "update ".INPK_CLAIM_ITEM." set clm_statnm='요청철회', latedt=now() where itmsno='{$item['itmsno']}'";
						$GLOBALS['db']->query($query);
						# 로그추가
						$query = "insert into ".INPK_CLAIM_ITEM_LOG." values ('', '{$item['itmsno']}', '요청 > 요청철회', now())";
						$GLOBALS['db']->query($query);
					}
				}
			}

			## 처리결과출력
			$this->log(sprintf("Result_Cnt (resChild) : Total[%s], True[%s], False[%s]", $resChild['TOT'], $resChild['T'], ($resChild['TOT'] - $resChild['T'])));
			if ($isRecall != true || $this->callCnt == $this->recallMax){
				echo ($resChild['T'] ? 'succeed' : 'fail:' . $arrXml['ORDER_LIST']['RESULT']['MESSAGE']); // ajax read
			}

			## 결제로그저장
			if (count($settlelog)){
				$settlelog = sprintf("\n[%s] 인터파크 교환요청승인\n%s\n", date('y-m-d H:i:s'), implode("\n", $settlelog));
				$GLOBALS['db']->query("update ".GD_ORDER." set settlelog=concat(settlelog, '{$settlelog}') where ordno='{$gClaim['ordno']}'");
			}
		}

		$this->log("exchReqAcceptForComm END");

		## API 재호출
		if ($isRecall == true && $this->callCnt < $this->recallMax){
			$this->exchReqAcceptForComm($data);
		}
	}

	### 반품요청거부
	function rtnReqRefuseForComm($data)
	{
		$this->callCnt++;
		$this->log("rtnReqRefuseForComm START");

		$gClaim = $GLOBALS['db']->fetch("select clm_seq, inpk_ordno, ordno from ".INPK_CLAIM." where clmsno='{$data['clmsno']}'");
		list($gClaim['clm_statnm']) = $GLOBALS['db']->fetch("select clm_statnm from ".INPK_CLAIM_ITEM." where clmsno='{$data['clmsno']}' limit 1");
		if ($gClaim['clm_seq'] == ''){
			$this->log(sprintf("Fail : Non-existent claim [clmsno:%s]", $data['clmsno']));
		}
		else if ($gClaim['clm_statnm'] != '요청'){
			$this->log("Fail : 클레임요청상태 != 요청");
		}
		else if (class_exists('httpSock') === false){
			$this->log("Non-existent class httpSock");
		}
		else {
			## 전송
			$settlelog = array();
			$resChild = array('TOT' => 1, 'T' => 0);

			// orderclm_no=주문번호&clmreq_seq=주문취소요청순번&refuse_rsn=거부사유
			$url = sprintf("%s&orderclm_no=%s&clmreq_seq=%s&refuse_rsn=%s", $this->interfaceurl, $gClaim['inpk_ordno'], $gClaim['clm_seq'], urlencode($data['refuse_rsn']));
			$this->log(sprintf("URL : %s", preg_replace("/^(http|https):\/\/[^\/]*\//", "/", $url)));

			ob_start();
			$httpSock = new httpSock($url);
			$httpSock->send(true);
			$this->err( ob_get_clean() );

			$arrXml = $this->parseXml(str_replace("-- End --", "", $httpSock->resContent));
			if (in_array($arrXml['ORDER_LIST']['RESULT']['CODE'], array('000', '009')) === true){
				$GLOBALS['db']->query("update ".INPK_CLAIM." set refuse_rsn='{$data['refuse_rsn']}' where clmsno='{$data['clmsno']}'");
				$resItem = $GLOBALS['db']->query("select * from ".INPK_CLAIM_ITEM." where clmsno='{$data['clmsno']}'");
				while ($item = $GLOBALS['db']->fetch($resItem))
				{
					$settlelog[] = sprintf("- True : 상품 [e:%s] [i:%s]", $item['goodsno'], $item['inpk_prdno']);
					# 클레임요청상태 업데이트
					$query = "update ".INPK_CLAIM_ITEM." set clm_statnm='거부', latedt=now() where itmsno='{$item['itmsno']}'";
					$GLOBALS['db']->query($query);
					# 로그추가
					$query = "insert into ".INPK_CLAIM_ITEM_LOG." values ('', '{$item['itmsno']}', '요청 > 거부', now())";
					$GLOBALS['db']->query($query);
				}

				$resChild['T']++;
				unset($msg);
				$msg[] = "Result [";
				$msg[] = sprintf("\t결과 = %s", 'True');
				$msg[] = sprintf("\t주문취소요청순번 = %s", $gClaim['clm_seq']);
				$msg[] = sprintf("\t인터파크 주문번호 = %s", $gClaim['inpk_ordno']);
				$msg[] = sprintf("\t이나무 주문번호 = %s", $gClaim['ordno']);
				$msg[] = "]";
				$this->log($msg);
			}
			else {
				$settlelog[] = sprintf("- False : 상품 [e:%s] [i:%s]", $gClaim['goodsno'], $gClaim['inpk_prdno']);
				unset($msg);
				$msg[] = "Result [";
				$msg[] = sprintf("\t결과 = %s", 'False');
				$msg[] = sprintf("\t주문취소요청순번 = %s", $gClaim['clm_seq']);
				$msg[] = sprintf("\t인터파크 주문번호 = %s", $gClaim['inpk_ordno']);
				$msg[] = sprintf("\t이나무 주문번호 = %s", $gClaim['ordno']);
				$msg[] = sprintf("\t결과코드 = %s", $arrXml['ORDER_LIST']['RESULT']['CODE']);
				$msg[] = sprintf("\t결과메세지 = %s", $arrXml['ORDER_LIST']['RESULT']['MESSAGE']);
				$msg[] = "]";
				$this->log($msg);

				# API 재호출여부
				if (in_array($arrXml['ORDER_LIST']['RESULT']['CODE'], array('001', '002'))){
					$isRecall = true;
				}

				# 결과코드가 004 이면 요청철회로 처리
				if ($arrXml['ORDER_LIST']['RESULT']['CODE'] == '004'){
					$resItem = $GLOBALS['db']->query("select * from ".INPK_CLAIM_ITEM." where clmsno='{$data['clmsno']}'");
					while ($item = $GLOBALS['db']->fetch($resItem))
					{
						# 클레임요청상태 업데이트
						$query = "update ".INPK_CLAIM_ITEM." set clm_statnm='요청철회', latedt=now() where itmsno='{$item['itmsno']}'";
						$GLOBALS['db']->query($query);
						# 로그추가
						$query = "insert into ".INPK_CLAIM_ITEM_LOG." values ('', '{$item['itmsno']}', '요청 > 요청철회', now())";
						$GLOBALS['db']->query($query);
					}
				}
			}

			## 처리결과출력
			$this->log(sprintf("Result_Cnt (resChild) : Total[%s], True[%s], False[%s]", $resChild['TOT'], $resChild['T'], ($resChild['TOT'] - $resChild['T'])));
			if ($isRecall != true || $this->callCnt == $this->recallMax){
				echo ($resChild['T'] ? 'succeed' : 'fail:' . $arrXml['ORDER_LIST']['RESULT']['MESSAGE']); // ajax read
			}

			## 결제로그저장
			if (count($settlelog)){
				$settlelog = sprintf("\n[%s] 인터파크 반품요청거부\n%s\n", date('y-m-d H:i:s'), implode("\n", $settlelog));
				$GLOBALS['db']->query("update ".GD_ORDER." set settlelog=concat(settlelog, '{$settlelog}') where ordno='{$gClaim['ordno']}'");
			}
		}

		$this->log("rtnReqRefuseForComm END");

		## API 재호출
		if ($isRecall == true && $this->callCnt < $this->recallMax){
			$this->rtnReqRefuseForComm($data);
		}
	}

	### 교환요청거부
	function exchReqRefuseForComm($data)
	{
		$this->callCnt++;
		$this->log("exchReqRefuseForComm START");

		$gClaim = $GLOBALS['db']->fetch("select clm_seq, inpk_ordno, ordno from ".INPK_CLAIM." where clmsno='{$data['clmsno']}'");
		list($gClaim['clm_statnm']) = $GLOBALS['db']->fetch("select clm_statnm from ".INPK_CLAIM_ITEM." where clmsno='{$data['clmsno']}' limit 1");
		if ($gClaim['clm_seq'] == ''){
			$this->log(sprintf("Fail : Non-existent claim [clmsno:%s]", $data['clmsno']));
		}
		else if ($gClaim['clm_statnm'] != '요청'){
			$this->log("Fail : 클레임요청상태 != 요청");
		}
		else if (class_exists('httpSock') === false){
			$this->log("Non-existent class httpSock");
		}
		else {
			## 전송
			$settlelog = array();
			$resChild = array('TOT' => 1, 'T' => 0);

			// orderclm_no=주문번호&clmreq_seq=주문취소요청순번&refuse_rsn=거부사유
			$url = sprintf("%s&orderclm_no=%s&clmreq_seq=%s&refuse_rsn=%s", $this->interfaceurl, $gClaim['inpk_ordno'], $gClaim['clm_seq'], urlencode($data['refuse_rsn']));
			$this->log(sprintf("URL : %s", preg_replace("/^(http|https):\/\/[^\/]*\//", "/", $url)));

			ob_start();
			$httpSock = new httpSock($url);
			$httpSock->send(true);
			$this->err( ob_get_clean() );

			$arrXml = $this->parseXml(str_replace("-- End --", "", $httpSock->resContent));
			if (in_array($arrXml['ORDER_LIST']['RESULT']['CODE'], array('000', '011')) === true){
				$GLOBALS['db']->query("update ".INPK_CLAIM." set refuse_rsn='{$data['refuse_rsn']}' where clmsno='{$data['clmsno']}'");
				$resItem = $GLOBALS['db']->query("select * from ".INPK_CLAIM_ITEM." where clmsno='{$data['clmsno']}'");
				while ($item = $GLOBALS['db']->fetch($resItem))
				{
					$settlelog[] = sprintf("- True : 상품 [e:%s] [i:%s]", $item['goodsno'], $item['inpk_prdno']);
					# 클레임요청상태 업데이트
					$query = "update ".INPK_CLAIM_ITEM." set clm_statnm='거부', latedt=now() where itmsno='{$item['itmsno']}'";
					$GLOBALS['db']->query($query);
					# 로그추가
					$query = "insert into ".INPK_CLAIM_ITEM_LOG." values ('', '{$item['itmsno']}', '요청 > 거부', now())";
					$GLOBALS['db']->query($query);
				}

				$resChild['T']++;
				unset($msg);
				$msg[] = "Result [";
				$msg[] = sprintf("\t결과 = %s", 'True');
				$msg[] = sprintf("\t주문취소요청순번 = %s", $gClaim['clm_seq']);
				$msg[] = sprintf("\t인터파크 주문번호 = %s", $gClaim['inpk_ordno']);
				$msg[] = sprintf("\t이나무 주문번호 = %s", $gClaim['ordno']);
				$msg[] = "]";
				$this->log($msg);
			}
			else {
				$settlelog[] = sprintf("- False : 상품 [e:%s] [i:%s]", $gClaim['goodsno'], $gClaim['inpk_prdno']);
				unset($msg);
				$msg[] = "Result [";
				$msg[] = sprintf("\t결과 = %s", 'False');
				$msg[] = sprintf("\t주문취소요청순번 = %s", $gClaim['clm_seq']);
				$msg[] = sprintf("\t인터파크 주문번호 = %s", $gClaim['inpk_ordno']);
				$msg[] = sprintf("\t이나무 주문번호 = %s", $gClaim['ordno']);
				$msg[] = sprintf("\t결과코드 = %s", $arrXml['ORDER_LIST']['RESULT']['CODE']);
				$msg[] = sprintf("\t결과메세지 = %s", $arrXml['ORDER_LIST']['RESULT']['MESSAGE']);
				$msg[] = "]";
				$this->log($msg);

				# API 재호출여부
				if (in_array($arrXml['ORDER_LIST']['RESULT']['CODE'], array('001', '002'))){
					$isRecall = true;
				}

				# 결과코드가 004 이면 요청철회로 처리
				if ($arrXml['ORDER_LIST']['RESULT']['CODE'] == '004'){
					$resItem = $GLOBALS['db']->query("select * from ".INPK_CLAIM_ITEM." where clmsno='{$data['clmsno']}'");
					while ($item = $GLOBALS['db']->fetch($resItem))
					{
						# 클레임요청상태 업데이트
						$query = "update ".INPK_CLAIM_ITEM." set clm_statnm='요청철회', latedt=now() where itmsno='{$item['itmsno']}'";
						$GLOBALS['db']->query($query);
						# 로그추가
						$query = "insert into ".INPK_CLAIM_ITEM_LOG." values ('', '{$item['itmsno']}', '요청 > 요청철회', now())";
						$GLOBALS['db']->query($query);
					}
				}
			}

			## 처리결과출력
			$this->log(sprintf("Result_Cnt (resChild) : Total[%s], True[%s], False[%s]", $resChild['TOT'], $resChild['T'], ($resChild['TOT'] - $resChild['T'])));
			if ($isRecall != true || $this->callCnt == $this->recallMax){
				echo ($resChild['T'] ? 'succeed' : 'fail:' . $arrXml['ORDER_LIST']['RESULT']['MESSAGE']); // ajax read
			}

			## 결제로그저장
			if (count($settlelog)){
				$settlelog = sprintf("\n[%s] 인터파크 교환요청거부\n%s\n", date('y-m-d H:i:s'), implode("\n", $settlelog));
				$GLOBALS['db']->query("update ".GD_ORDER." set settlelog=concat(settlelog, '{$settlelog}') where ordno='{$gClaim['ordno']}'");
			}
		}

		$this->log("exchReqRefuseForComm END");

		## API 재호출
		if ($isRecall == true && $this->callCnt < $this->recallMax){
			$this->exchReqRefuseForComm($data);
		}
	}

	### 반품/교환입고확정
	function clmEnterWhCompForComm($data)
	{
		$this->callCnt++;
		$this->log("clmEnterWhCompForComm START");

		$gClaim = $GLOBALS['db']->fetch("select c.clm_no, c.clm_seq, c.inpk_ordno, c.ordno, c.clm_tpnm, i.* from ".INPK_CLAIM." c left join ".INPK_CLAIM_ITEM." i on c.clmsno=i.clmsno where i.clmsno='{$data['clmsno']}' and i.itmsno='{$data['itmsno']}'");
		if ($gClaim['clm_seq'] == ''){
			$this->log(sprintf("Fail : Non-existent claim [clmsno:%s, itmsno:%s]", $data['clmsno'], $data['itmsno']));
		}
		else if ($gClaim['clm_statnm'] != '반품/교환수거지시'){
			$this->log("Fail : 클레임상태 != 반품/교환수거지시");
		}
		else if (class_exists('httpSock') === false){
			$this->log("Non-existent class httpSock");
		}
		else {
			## 전송
			$settlelog = array();
			$resChild = array('TOT' => 1, 'T' => 0);

			// 반품일 경우: 	orderclm_no=주문번호&ord_seq=주문순번&clm_no=클레임번호&clm_seq=클레임순번&delv_comp=택배사코드&delv_no=운송장번호&clm_crt_tpnm=반품&supply_entr_no=업체번호&supply_ctrt_seq=공급계약일련번호
			// 고객교환일 경우:	orderclm_no=주문번호&ord_seq=주문순번&clm_no=클레임번호&clm_seq=클레임순번&delv_comp=택배사코드&delv_no=운송장번호&clm_crt_tpnm=고객교환
			$url = sprintf("%s&orderclm_no=%s&ord_seq=%s&clm_no=%s&clm_seq=%s&delv_comp=%s&delv_no=%s&clm_crt_tpnm=%s",
					$this->interfaceurl, $gClaim['inpk_ordno'], $gClaim['inpk_ordseq'], $gClaim['clm_no'], $gClaim['clm_seq'], $data['deliveryno'], $data['deliverycode'], $gClaim['clm_tpnm']);
			if ($gClaim['clm_tpnm'] == '반품')
			{
				## load openstye conf
				$this->log("Load openstye_Conf START");
				if (class_exists('interpark'))
				{
					$interpark = new interpark();
					$interpark->getInpkOS();
					$this->inpkOSCfg = $interpark->inpkOSCfg;
				}
				else $this->log("Non-existent class interpark");
				$this->log("Load Interpark_Conf END");

				// 반품일 경우: &supply_entr_no=업체번호&supply_ctrt_seq=공급계약일련번호
				$url .= sprintf("&supply_entr_no=%s&supply_ctrt_seq=%s", $this->inpkOSCfg['entrNo'], $this->inpkOSCfg['ctrtSeq']);
			}
			$this->log(sprintf("URL : %s", preg_replace("/^(http|https):\/\/[^\/]*\//", "/", $url)));

			ob_start();
			$httpSock = new httpSock($url);
			$httpSock->send(true);
			$this->err( ob_get_clean() );

			$arrXml = $this->parseXml(str_replace("-- End --", "", $httpSock->resContent));
			if (in_array($arrXml['ORDER_LIST']['RESULT']['CODE'], array('000', '012', '013')) === true){
				# 클레임상태 업데이트
				$query = "update ".INPK_CLAIM_ITEM." set clm_statnm='반품/교환입고확정전송', latedt=now(), storeDvno='{$data['deliveryno']}', storeDvcode='{$data['deliverycode']}' where clmsno='{$data['clmsno']}'";
				$GLOBALS['db']->query($query);
				# 로그추가
				$res = $GLOBALS['db']->query("select itmsno from ".INPK_CLAIM_ITEM." where clmsno='{$data['clmsno']}'");
				while ($row = $GLOBALS['db']->fetch($res))
				{
					$query = "insert into ".INPK_CLAIM_ITEM_LOG." values ('', '{$row['itmsno']}', '반품/교환수거지시 > 반품/교환입고확정전송', now())";
					$GLOBALS['db']->query($query);
				}

				$settlelog[] = sprintf("- True : 상품 [e:%s] [i:%s]", $gClaim['goodsno'], $gClaim['inpk_prdno']);
				$resChild['T']++;
				unset($msg);
				$msg[] = "Result [";
				$msg[] = sprintf("\t결과 = %s", 'True');
				$msg[] = sprintf("\t주문취소요청순번 = %s", $gClaim['clm_seq']);
				$msg[] = sprintf("\t인터파크 주문번호 = %s", $gClaim['inpk_ordno']);
				$msg[] = sprintf("\t인터파크 주문순번 = %s", $gClaim['inpk_ordseq']);
				$msg[] = sprintf("\t인터파크 상품코드 = %s", $gClaim['inpk_prdno']);
				$msg[] = sprintf("\t이나무 주문번호 = %s", $gClaim['ordno']);
				$msg[] = sprintf("\t이나무 주문순번 = %s", $gClaim['item_sno']);
				$msg[] = sprintf("\t이나무 고유번호 = %s", $gClaim['goodsno']);
				$msg[] = "]";
				$this->log($msg);
			}
			else {
				$settlelog[] = sprintf("- False : 상품 [e:%s] [i:%s]", $gClaim['goodsno'], $gClaim['inpk_prdno']);
				unset($msg);
				$msg[] = "Result [";
				$msg[] = sprintf("\t결과 = %s", 'False');
				$msg[] = sprintf("\t주문취소요청순번 = %s", $gClaim['clm_seq']);
				$msg[] = sprintf("\t인터파크 주문번호 = %s", $gClaim['inpk_ordno']);
				$msg[] = sprintf("\t인터파크 주문순번 = %s", $gClaim['inpk_ordseq']);
				$msg[] = sprintf("\t인터파크 상품코드 = %s", $gClaim['inpk_prdno']);
				$msg[] = sprintf("\t이나무 주문번호 = %s", $gClaim['ordno']);
				$msg[] = sprintf("\t이나무 주문순번 = %s", $gClaim['item_sno']);
				$msg[] = sprintf("\t이나무 고유번호 = %s", $gClaim['goodsno']);
				$msg[] = sprintf("\t결과코드 = %s", $arrXml['ORDER_LIST']['RESULT']['CODE']);
				$msg[] = sprintf("\t결과메세지 = %s", $arrXml['ORDER_LIST']['RESULT']['MESSAGE']);
				$msg[] = "]";
				$this->log($msg);

				# API 재호출여부
				if (in_array($arrXml['ORDER_LIST']['RESULT']['CODE'], array('001', '002'))){
					$isRecall = true;
				}
			}

			## 처리결과출력
			$this->log(sprintf("Result_Cnt (resChild) : Total[%s], True[%s], False[%s]", $resChild['TOT'], $resChild['T'], ($resChild['TOT'] - $resChild['T'])));
			if ($isRecall != true || $this->callCnt == $this->recallMax){
				echo ($resChild['T'] ? 'succeed' : 'fail:' . $arrXml['ORDER_LIST']['RESULT']['MESSAGE']); // ajax read
			}

			## 결제로그저장
			if (count($settlelog)){
				$settlelog = sprintf("\n[%s] 인터파크 반품/교환입고확정\n%s\n", date('y-m-d H:i:s'), implode("\n", $settlelog));
				$GLOBALS['db']->query("update ".GD_ORDER." set settlelog=concat(settlelog, '{$settlelog}') where ordno='{$gClaim['ordno']}'");
			}
		}

		$this->log("clmEnterWhCompForComm END");

		## API 재호출
		if ($isRecall == true && $this->callCnt < $this->recallMax){
			$this->clmEnterWhCompForComm($data);
		}
	}

	### 교환확정
	function exchOutWhCompForComm($data)
	{
		$this->callCnt++;
		$this->log("exchOutWhCompForComm START");

		$gClaim = $GLOBALS['db']->fetch("select c.clm_no, c.clm_seq, c.inpk_ordno, c.ordno, i.* from ".INPK_CLAIM." c left join ".INPK_CLAIM_ITEM." i on c.clmsno=i.clmsno where i.clmsno='{$data['clmsno']}' and i.itmsno='{$data['itmsno']}'");
		if ($gClaim['clm_seq'] == ''){
			$this->log(sprintf("Fail : Non-existent claim [clmsno:%s, itmsno:%s]", $data['clmsno'], $data['itmsno']));
		}
		else if ($gClaim['clm_statnm'] != '교환/재배송출고지시'){
			$this->log("Fail : 클레임상태 != 교환/재배송출고지시");
		}
		else if (class_exists('httpSock') === false){
			$this->log("Non-existent class httpSock");
		}
		else {
			## 전송
			$settlelog = array();
			$resChild = array('TOT' => 1, 'T' => 0);

			// orderclm_no=주문번호&ord_seq=주문순번&clm_no=클레임번호&clm_seq=클레임순번&delv_comp=택배사코드&delv_no=운송장번호
			$url = sprintf("%s&orderclm_no=%s&ord_seq=%s&clm_no=%s&clm_seq=%s&delv_comp=%s&delv_no=%s", $this->interfaceurl, $gClaim['inpk_ordno'], $gClaim['inpk_ordseq'], $gClaim['clm_no'], $gClaim['clm_seq'], $data['deliveryno'], $data['deliverycode']);
			$this->log(sprintf("URL : %s", preg_replace("/^(http|https):\/\/[^\/]*\//", "/", $url)));

			ob_start();
			$httpSock = new httpSock($url);
			$httpSock->send(true);
			$this->err( ob_get_clean() );

			$arrXml = $this->parseXml(str_replace("-- End --", "", $httpSock->resContent));
			if (in_array($arrXml['ORDER_LIST']['RESULT']['CODE'], array('000', '014')) === true){
				# 클레임상태 업데이트
				$query = "update ".INPK_CLAIM_ITEM." set clm_statnm='교환확정전송', latedt=now(), exchangeDvno='{$data['deliveryno']}', exchangeDvcode='{$data['deliverycode']}' where clmsno='{$data['clmsno']}'";
				$GLOBALS['db']->query($query);
				# 로그추가
				$res = $GLOBALS['db']->query("select itmsno from ".INPK_CLAIM_ITEM." where clmsno='{$data['clmsno']}'");
				while ($row = $GLOBALS['db']->fetch($res))
				{
					$query = "insert into ".INPK_CLAIM_ITEM_LOG." values ('', '{$row['itmsno']}', '교환/재배송출고지시 > 교환확정전송', now())";
					$GLOBALS['db']->query($query);
				}

				$settlelog[] = sprintf("- True : 상품 [e:%s] [i:%s]", $gClaim['goodsno'], $gClaim['inpk_prdno']);
				$resChild['T']++;
				unset($msg);
				$msg[] = "Result [";
				$msg[] = sprintf("\t결과 = %s", 'True');
				$msg[] = sprintf("\t주문취소요청순번 = %s", $gClaim['clm_seq']);
				$msg[] = sprintf("\t인터파크 주문번호 = %s", $gClaim['inpk_ordno']);
				$msg[] = sprintf("\t인터파크 주문순번 = %s", $gClaim['inpk_ordseq']);
				$msg[] = sprintf("\t인터파크 상품코드 = %s", $gClaim['inpk_prdno']);
				$msg[] = sprintf("\t이나무 주문번호 = %s", $gClaim['ordno']);
				$msg[] = sprintf("\t이나무 주문순번 = %s", $gClaim['item_sno']);
				$msg[] = sprintf("\t이나무 고유번호 = %s", $gClaim['goodsno']);
				$msg[] = "]";
				$this->log($msg);
			}
			else {
				$settlelog[] = sprintf("- False : 상품 [e:%s] [i:%s]", $gClaim['goodsno'], $gClaim['inpk_prdno']);
				unset($msg);
				$msg[] = "Result [";
				$msg[] = sprintf("\t결과 = %s", 'False');
				$msg[] = sprintf("\t주문취소요청순번 = %s", $gClaim['clm_seq']);
				$msg[] = sprintf("\t인터파크 주문번호 = %s", $gClaim['inpk_ordno']);
				$msg[] = sprintf("\t인터파크 주문순번 = %s", $gClaim['inpk_ordseq']);
				$msg[] = sprintf("\t인터파크 상품코드 = %s", $gClaim['inpk_prdno']);
				$msg[] = sprintf("\t이나무 주문번호 = %s", $gClaim['ordno']);
				$msg[] = sprintf("\t이나무 주문순번 = %s", $gClaim['item_sno']);
				$msg[] = sprintf("\t이나무 고유번호 = %s", $gClaim['goodsno']);
				$msg[] = sprintf("\t결과코드 = %s", $arrXml['ORDER_LIST']['RESULT']['CODE']);
				$msg[] = sprintf("\t결과메세지 = %s", $arrXml['ORDER_LIST']['RESULT']['MESSAGE']);
				$msg[] = "]";
				$this->log($msg);

				# API 재호출여부
				if (in_array($arrXml['ORDER_LIST']['RESULT']['CODE'], array('001', '002'))){
					$isRecall = true;
				}
			}

			## 처리결과출력
			$this->log(sprintf("Result_Cnt (resChild) : Total[%s], True[%s], False[%s]", $resChild['TOT'], $resChild['T'], ($resChild['TOT'] - $resChild['T'])));
			if ($isRecall != true || $this->callCnt == $this->recallMax){
				echo ($resChild['T'] ? 'succeed' : 'fail:' . $arrXml['ORDER_LIST']['RESULT']['MESSAGE']); // ajax read
			}

			## 결제로그저장
			if (count($settlelog)){
				$settlelog = sprintf("\n[%s] 인터파크 교환확정\n%s\n", date('y-m-d H:i:s'), implode("\n", $settlelog));
				$GLOBALS['db']->query("update ".GD_ORDER." set settlelog=concat(settlelog, '{$settlelog}') where ordno='{$gClaim['ordno']}'");
			}
		}

		$this->log("exchOutWhCompForComm END");

		## API 재호출
		if ($isRecall == true && $this->callCnt < $this->recallMax){
			$this->exchOutWhCompForComm($data);
		}
	}
}

?>
