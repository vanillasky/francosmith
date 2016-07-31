<?

@include_once dirname(__FILE__) . "/../lib/httpSock.class.php";
@include_once dirname(__FILE__) . "/../lib/parsexmlstruc.class.php";
@include_once dirname(__FILE__) . "/../lib/xmlWriter.class.php";
@include_once dirname(__FILE__) . "/../lib/putLog.class.php";

class i2e_order_api extends putLog
{
	var $interfaceid;	# 인터페이스 ID

	function i2e_order_api($isTest=false)
	{
		$this->putLog($_GET['mode']);
		$this->log("START");

		## 인터페이스 ID 정의
		$this->interfaceid = $_GET['mode'];

		## API START
		$this->chkGetParameter();
		$strXml = $this->socketGodo($_GET['dataUrl']);
		$arrXml = $this->parseXml($strXml);

		switch ($this->interfaceid)
		{
			case "orderListForMulti": # 주문내역조회
			case "orderListDelvForMulti": # 주문내역조회2
				$this->orderListForMulti($arrXml);
				break;

			case "cnclNClmReqListForMulti": # 주문취소/반품/교환요청리스트
				$this->cnclNClmReqListForMulti($arrXml);
				break;

			case "clmListForMulti": # 클레임리스트
				$this->clmListForMulti($arrXml);
				break;

			case "orderCompListForMulti": # 구매확정조회
				$this->orderCompListForMulti($arrXml);
				break;
		}

		$this->log("END");
	}

	### Check Get Parameter
	function chkGetParameter()
	{
		$this->log("Check_Get_Parameter START");
		if ($_GET['dataUrl'] == ''){
			$msg = array(
				"log" => array('Empty GET[dataUrl]', 'Check_Get_Parameter END'),
				"header" => "파라미터인 데이터경로가 존재하지 않습니다.."
				);
			$this->endHeader($msg, 400);
		}
		$this->log("Check_Get_Parameter END");
	}

	### Socket to Godo
	function socketGodo($dataUrl)
	{
		$this->log("Connection_Godo START");
		$this->log(sprintf("URL : %s", preg_replace("/^(http|https):\/\/[^\/]*\//", "/", $dataUrl)));

		ob_start();
		if (class_exists('httpSock'))
		{
			$httpSock = new httpSock($dataUrl);
			$httpSock->send(true);
		}
		else $this->log("Non-existent class httpSock");
		$this->err( ob_get_clean() );

		$this->log("Connection_Godo END");
		return $httpSock->resContent;
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

		return $arrXml;
    }

    ### 주문내역조회(Insert Order)
    function orderListForMulti($arrXml)
    {
		if (is_array($arrXml['ORDER_LIST']['ORDER']) === false){
			$this->log("Empty-arrXml[ORDER_LIST][ORDER] (Insert_Order Before)");
			return;
		}
		$this->log("Insert_Order START");

		$resParent = array('TOT' => count($arrXml['ORDER_LIST']['ORDER']), 'T' => 0);
		$resChild = array('TOT' => 0, 'T' => 0);
		foreach($arrXml['ORDER_LIST']['ORDER'] as $order)
		{
			## 특수문자 컨트롤
			foreach ($order as $k => $v){
				if (is_array($v) === false) $order[$k] = addslashes( stripslashes($v) );
			}

			## 동일주문 존재하는지 체크, 없으면 Insert
			$gOrder = $GLOBALS['db']->fetch("select ordno from ".GD_ORDER." where inpk_ordno='{$order['ORD_NO']}'");
			if ($gOrder['ordno'] != ''){
				unset($msg);
				$msg[] = "Fail : Existent order of openstyle [";
				$msg[] = sprintf("\t인터파크 주문번호 = %s", $order['ORD_NO']);
				$msg[] = sprintf("\t이나무 주문번호 = %s", $gOrder['ordno']);
				$msg[] = "]";
				$this->log($msg);
				continue;
			}
			else {
				ob_start();

				## 주문번호 생성
				$ordno = getordno();

				## 배송비 및 주문금액 계산
				$delivery = $order['DELIVERY']['DELV'][0]['DEL_AMT'];
				$settleprice = $delivery;
				foreach($order['PRODUCT']['PRD'] as $k => $item){
					$settleprice += $item['ORD_AMT'];
				}
				if ($delivery) $deli_type = '선불';

				## 우편번호 형식 맞춤
				$order['DEL_ZIP'] = substr($order['DEL_ZIP'],0,3) . '-' . substr($order['DEL_ZIP'],3,3);

				## 결제로그
				$settlelog =  sprintf("[%s] 인터파크오픈스타일 입금확인된 주문\n", date('y-m-d H:i:s'));

				## 주문일자형식에 맞춰 저장
				if($order['ORDER_DT']) $order['ORDER_DT'] = date("Y-m-d H:i:s",$order['ORDER_DT']);

				## 주문정보 저장
				$query = "
				insert into ".GD_ORDER." set
					ordno			= '{$ordno}',
					nameOrder		= '{$order['ORD_NM']}',
					email			= '{$order['EMAIL']}',
					phoneOrder		= '{$order['TEL']}',
					mobileOrder		= '{$order['MOBILE_TEL']}',
					nameReceiver	= '{$order['RCVR_NM']}',
					phoneReceiver	= '{$order['DELI_TEL']}',
					mobileReceiver	= '{$order['DELI_MOBILE']}',
					zipcode			= '{$order['DEL_ZIP']}',
					address			= '{$order['DELI_ADDR1']} {$order['DELI_ADDR2']}',
					settlekind		= '',
					settleprice		= '{$settleprice}',
					prn_settleprice	= '{$settleprice}',
					deli_title		= '',
					deli_type		= '{$deli_type}',
					delivery		= '{$delivery}',
					ip				= '{$_SERVER['REMOTE_ADDR']}',
					memo			= '{$order['DELI_COMMENT']}',
					inflow			= 'openstyle',
					orddt			= '{$order['ORDER_DTS']}',
					settlelog		= '{$settlelog}',
					inpk_ordno		= '{$order['ORD_NO']}',
					inpk_regdt 		= now()
				";
				$res = $GLOBALS['db']->query($query);

				$this->err($err=ob_get_clean());
				if ($res && $err == '') $resParent['T']++;
				unset($msg);
				$msg[] = "Insert_Result (".GD_ORDER.") [";
				$msg[] = sprintf("\t결과 = %s", ($res && $err == '' ? 'True' : 'False'));
				$msg[] = sprintf("\t인터파크 주문번호 = %s", $order['ORD_NO']);
				$msg[] = sprintf("\t이나무 주문번호 = %s", $ordno);
				$msg[] = "]";
				$this->log($msg);
			}

			## 주문상품
			$resChild['TOT'] += count($order['PRODUCT']['PRD']);
			foreach($order['PRODUCT']['PRD'] as $k => $item)
			{
				## 동일상품 존재하는지 체크, 없으면 Insert
				$gOrderItem = $GLOBALS['db']->fetch("select sno, goodsno from ".GD_ORDER_ITEM." where ordno='{$ordno}' and inpk_prdno='{$item['PRD_NO']}' and inpk_ordseq='{$item['ORD_SEQ']}'");
				if ($gOrderItem['sno'] != ''){
					unset($msg);
					$msg[] = "Fail : Existent orderItem of openstyle [";
					$msg[] = sprintf("\t인터파크 상품코드 = %s", $item['PRD_NO']);
					$msg[] = sprintf("\t인터파크 주문순번 = %s", $item['ORD_SEQ']);
					$msg[] = sprintf("\t이나무 고유번호 = %s", $gOrderItem['goodsno']);
					$msg[] = sprintf("\t이나무 주문순번 = %s", $gOrderItem['sno']);
					$msg[] = "]";
					$this->log($msg);
					continue;
				}
				else {
					ob_start();

					## 상품정보 가져오기
					$data = $GLOBALS['db']->fetch("select goodsno, goodsnm, maker, tax, brandno from ".GD_GOODS." where inpk_prdno='{$item['PRD_NO']}'");
					$data['goodsnm'] = addslashes(strip_tags($data['goodsnm']));
					$data['maker'] = addslashes($data['maker']);
					list($brandnm) = $GLOBALS['db'] -> fetch("select brandnm from ".GD_GOODS_BRAND." where sno='{$data['brandno']}'");
					$brandnm = addslashes($brandnm);

					## 상품 테이블에서 공급가 가져오기
					list ($item['supply'], $opt[0], $opt[1]) = $GLOBALS['db']->fetch("select supply, opt1, opt2 from ".GD_GOODS_OPTION." where goodsno='{$data['goodsno']}' and optno='{$item['OPT_NO']}' and go_is_deleted <> '1' and go_is_display = '1'");

					## 추가옵션 (색상/블랙|사이즈/100)
					if ($opt[0] == '' && $opt[1] == ''){
						$opt = array();
						$tmp = explode("|", $item['OPT_NM']);
						foreach($tmp as $v){
							$opt[] = trim(array_pop(explode("/", $v)));
						}
					}

					$price = $item['ORD_AMT'] / $item['ORD_QTY'];
					$deli_msg = ($item['IS_COLLECTED'] == 'Y' ? '착불배송' : '');
					$query = "
					insert into ".GD_ORDER_ITEM." set
						ordno			= '{$ordno}',
						goodsno			= '{$data['goodsno']}',
						goodsnm			= '{$data['goodsnm']}',
						opt1			= '{$opt[0]}',
						opt2			= '{$opt[1]}',
						price			= '{$price}',
						supply			= '{$item['supply']}',
						ea				= '{$item['ORD_QTY']}',
						maker			= '{$data['maker']}',
						brandnm			= '{$brandnm}',
						tax				= '{$data['tax']}',
						optno			= '{$item['OPT_NO']}',
						inpk_prdno		= '{$item['PRD_NO']}',
						inpk_ordseq		= '{$item['ORD_SEQ']}',
						deli_msg		= '{$deli_msg}'
					";
					$res = $GLOBALS['db']->query($query);
					$item_sno = $GLOBALS['db']->lastID();

					$this->err($err=ob_get_clean());
					if ($res && $err == '') $resChild['T']++;
					unset($msg);
					$msg[] = "Insert_Result (".GD_ORDER_ITEM.") [";
					$msg[] = sprintf("\t결과 = %s", ($res && $err == '' ? 'True' : 'False'));
					$msg[] = sprintf("\t인터파크 상품코드 = %s", $item['PRD_NO']);
					$msg[] = sprintf("\t인터파크 주문순번 = %s", $item['ORD_SEQ']);
					$msg[] = sprintf("\t이나무 고유번호 = %s", $data['goodsno']);
					$msg[] = sprintf("\t이나무 주문순번 = %s", $item_sno);
					$msg[] = "]";
					$this->log($msg);
				}
			}

			## 진행상황별 처리
			ctlStep($ordno,2,'stock'); // 배송준비중 및 재고차감
		}

		## 처리결과출력
		unset($msg);
		$msg[] = sprintf("Insert_Cnt (resParent) : Total[%s], True[%s], False[%s]", $resParent['TOT'], $resParent['T'], ($resParent['TOT'] - $resParent['T']));
		$msg[] = sprintf("Insert_Cnt (resChild) : Total[%s], True[%s], False[%s]", $resChild['TOT'], $resChild['T'], ($resChild['TOT'] - $resChild['T']));
		echo implode("\n", $msg); // godo read
		$this->log($msg);
		$this->log("Insert_Order END");
    }

    ### 주문취소/반품/교환요청리스트
    function cnclNClmReqListForMulti($arrXml)
    {
		if (is_array($arrXml['ORDER_LIST']['ORDER']) === false){
			$this->log("Empty-arrXml[ORDER_LIST][ORDER] (Insert_Cancel&Claim&ExchangeRequest Before)");
			return;
		}
		$this->log("Insert_Cancel&Claim&ExchangeRequest START");

		$resParent = array('TOT' => count($arrXml['ORDER_LIST']['ORDER']), 'T' => 0);
		$resChild = array('TOT' => 0, 'T' => 0);
		foreach($arrXml['ORDER_LIST']['ORDER'] as $order)
		{
			## 동일요청 존재하는지 체크, 없으면 Insert
			$gClaim = $GLOBALS['db']->fetch("select clmsno, ordno, clm_tpnm from ".INPK_CLAIM." where inpk_ordno='{$order['ORD_NO']}' and clm_no='' and clm_seq='{$order['CLMREQ_SEQ']}'");
			if ($gClaim['clmsno'] == '')
			{
				ob_start();

				list($gClaim['ordno']) = $GLOBALS['db']->fetch("select ordno from ".GD_ORDER." where inpk_ordno='{$order['ORD_NO']}'");
				$query = "
				insert into ".INPK_CLAIM." set
					step				= 'r',
					ordno				= '{$gClaim['ordno']}',
					inpk_ordno			= '{$order['ORD_NO']}',
					clm_seq				= '{$order['CLMREQ_SEQ']}',
					clm_tpnm			= '{$order['CLMREQ_TPNM']}',
					return_mthd_tpnm	= '{$order['RETURN_MTHD_TPNM']}',
					regdt 				= now()
				";
				$res = $GLOBALS['db']->query($query);
				$gClaim['clmsno'] = $GLOBALS['db']->lastID();

				$this->err($err=ob_get_clean());
				if ($res && $err == '') $resParent['T']++;
				unset($msg);
				$msg[] = "Insert_Result (".INPK_CLAIM.") [";
				$msg[] = sprintf("\t결과 = %s", ($res && $err == '' ? 'True' : 'False'));
				$msg[] = sprintf("\t인터파크 주문번호 = %s", $order['ORD_NO']);
				$msg[] = sprintf("\t인터파크 주문취소요청순번 = %s", $order['CLMREQ_SEQ']);
				$msg[] = sprintf("\t이나무 주문번호 = %s", $gClaim['ordno']);
				$msg[] = sprintf("\t이나무 클레임번호 = %s", $gClaim['clmsno']);
				$msg[] = sprintf("\t클레임요청구분 = %s", $order['CLMREQ_TPNM']);
				$msg[] = "]";
				$this->log($msg);
			}
			else {
				unset($msg);
				$msg[] = "Existent (".INPK_CLAIM.") [";
				$msg[] = sprintf("\t인터파크 주문번호 = %s", $order['ORD_NO']);
				$msg[] = sprintf("\t인터파크 주문취소요청순번 = %s", $order['CLMREQ_SEQ']);
				$msg[] = sprintf("\t이나무 주문번호 = %s", $gClaim['ordno']);
				$msg[] = sprintf("\t이나무 클레임번호 = %s", $gClaim['clmsno']);
				$msg[] = sprintf("\t클레임요청구분 = %s", $gClaim['clm_tpnm']);
				$msg[] = "]";
				$this->log($msg);
			}

			## 주문상품
			$resChild['TOT'] += count($order['PRODUCT']['PRD']);
			foreach($order['PRODUCT']['PRD'] as $k => $item)
			{
				## 동일상품 존재하는지 체크, 없으면 Insert
				$gClaimItem = $GLOBALS['db']->fetch("select itmsno, item_sno, goodsno, clm_statnm from ".INPK_CLAIM_ITEM." where clmsno='{$gClaim['clmsno']}' and inpk_prdno='{$item['PRD_NO']}' and inpk_ordseq='{$item['ORD_SEQ']}'");
				if ($gClaimItem['itmsno'] == '')
				{
					ob_start();

					list($gClaimItem['item_sno'], $gClaimItem['goodsno'], $istep) = $GLOBALS['db']->fetch("select sno, goodsno, istep from ".GD_ORDER_ITEM." where ordno='{$gClaim['ordno']}' and inpk_prdno='{$item['PRD_NO']}' and inpk_ordseq='{$item['ORD_SEQ']}'");
					$query = "
					insert into ".INPK_CLAIM_ITEM." set
						clmsno			= '{$gClaim['clmsno']}',
						item_sno		= '{$gClaimItem['item_sno']}',
						goodsno			= '{$gClaimItem['goodsno']}',
						inpk_prdno		= '{$item['PRD_NO']}',
						clm_qty			= '{$item['CLMREQ_QTY']}',
						inpk_ordseq		= '{$item['ORD_SEQ']}',
						clm_statnm		= '{$item['CLMREQ_STATNM']}',
						clm_dt			= '{$order['CLMREQ_DT']}',
						clm_rsn_tpnm	= '{$item['CLMREQ_RSN_TPNM']}',
						clm_rsn_dtl		= '{$item['CLMREQ_RSN_DTL']}',
						latedt			= '{$order['CLMREQ_DT']}'
					";
					$res = $GLOBALS['db']->query($query);
					$gClaimItem['itmsno'] = $GLOBALS['db']->lastID();

					$this->err($err=ob_get_clean());
					if ($res && $err == '') $resChild['T']++;
					unset($msg);
					$msg[] = "Insert_Result (".INPK_CLAIM_ITEM.") [";
					$msg[] = sprintf("\t결과 = %s", ($res && $err == '' ? 'True' : 'False'));
					$msg[] = sprintf("\t인터파크 상품코드 = %s", $item['PRD_NO']);
					$msg[] = sprintf("\t인터파크 주문순번 = %s", $item['ORD_SEQ']);
					$msg[] = sprintf("\t이나무 고유번호 = %s", $gClaimItem['item_sno']);
					$msg[] = sprintf("\t이나무 주문순번 = %s", $gClaimItem['goodsno']);
					$msg[] = sprintf("\t이나무 클레임순번 = %s", $gClaimItem['itmsno']);
					$msg[] = sprintf("\t클레임요청상태 = %s", $item['CLMREQ_STATNM']);
					$msg[] = "]";
					$this->log($msg);

					## 주문상태 체크(거부처리)
					if ($order['CLMREQ_TPNM'] == "출고전주문취소" && $item['CLMREQ_STATNM'] == "요청" && in_array($istep, array('3', '4'))){
						$item['CLMREQ_STATNM'] = '거부';
					}
				}
				else {
					unset($msg);
					$msg[] = "Existent (".INPK_CLAIM_ITEM.") [";
					$msg[] = sprintf("\t인터파크 상품코드 = %s", $item['PRD_NO']);
					$msg[] = sprintf("\t인터파크 주문순번 = %s", $item['ORD_SEQ']);
					$msg[] = sprintf("\t이나무 고유번호 = %s", $gClaimItem['goodsno']);
					$msg[] = sprintf("\t이나무 주문순번 = %s", $gClaimItem['item_sno']);
					$msg[] = sprintf("\t이나무 클레임순번 = %s", $gClaimItem['itmsno']);
					$msg[] = sprintf("\t클레임요청상태 = %s", $gClaimItem['clm_statnm']);
					$msg[] = "]";
					$this->log($msg);
				}

				## 클레임요청상태 업데이트
				if ($gClaimItem['clm_statnm'] != $item['CLMREQ_STATNM'])
				{
					if ($gClaimItem['clm_statnm'] == ''){
						$memo = $item['CLMREQ_STATNM'];
						$regdt = $order['CLMREQ_DT'];
					}
					else {
						$memo = "{$gClaimItem['clm_statnm']} > {$item['CLMREQ_STATNM']}";
						$regdt = $item['CLMREQ_CNCL_DT'];
					}

					## 상태수정
					ob_start();
					$query = "update ".INPK_CLAIM_ITEM." set clm_statnm='{$item['CLMREQ_STATNM']}', latedt='{$regdt}' where itmsno='{$gClaimItem['itmsno']}'";
					$res = $GLOBALS['db']->query($query);

					$this->err($err=ob_get_clean());
					if ($res && $err == '') $resChild['stat_T']++;
					unset($msg);
					$msg[] = "Update_Result (".INPK_CLAIM_ITEM.") [";
					$msg[] = sprintf("\t결과 = %s", ($res && $err == '' ? 'True' : 'False'));
					$msg[] = sprintf("\t클레임요청상태 = %s", $item['CLMREQ_STATNM']);
					$msg[] = "]";
					$this->log($msg);

					## 로그추가
					$query = "insert into ".INPK_CLAIM_ITEM_LOG." values ('', '{$gClaimItem['itmsno']}', '{$memo}', '{$regdt}')";
					$GLOBALS['db']->query($query);
				}
			}
		}

		## 처리결과출력
		unset($msg);
		$msg[] = sprintf("Insert_Cnt (resParent) : Total[%s], True[%s], False[%s]", $resParent['TOT'], $resParent['T'], ($resParent['TOT'] - $resParent['T']));
		$msg[] = sprintf("Insert_Cnt (resChild) : Total[%s], True[%s], False[%s]", $resChild['TOT'], $resChild['T'], ($resChild['TOT'] - $resChild['T']));
		$msg[] = sprintf("Update_Stat_Cnt (resChild) : Total[%s], True[%s], False[%s]", $resChild['TOT'], $resChild['stat_T'], ($resChild['TOT'] - $resChild['stat_T']));
		echo implode("\n", $msg); // godo read
		$this->log($msg);
		$this->log("Insert_Cancel&Claim&ExchangeRequest END");
    }

    ### 클레임리스트
    function clmListForMulti($arrXml)
    {
		if (is_array($arrXml['ORDER_LIST']['ORDER']) === false){
			$this->log("Empty-arrXml[ORDER_LIST][ORDER] (Insert_ClaimList Before)");
			return;
		}
		$this->log("Insert_ClaimList START");

		$resParent = array('TOT' => count($arrXml['ORDER_LIST']['ORDER']), 'T' => 0);
		$resChild = array('TOT' => 0, 'T' => 0);
		foreach($arrXml['ORDER_LIST']['ORDER'] as $order)
		{
			## 동일요청 존재하는지 체크, 없으면 Insert
			$gClaim = $GLOBALS['db']->fetch("select clmsno, ordno, clm_tpnm from ".INPK_CLAIM." where inpk_ordno='{$order['ORD_NO']}' and clm_no='{$order['CLM_NO']}' and clm_seq='{$order['CLM_SEQ']}'");
			if ($gClaim['clmsno'] == '')
			{
				ob_start();

				list($gClaim['ordno']) = $GLOBALS['db']->fetch("select ordno from ".GD_ORDER." where inpk_ordno='{$order['ORD_NO']}'");
				$query = "
				insert into ".INPK_CLAIM." set
					step				= 'c',
					ordno				= '{$gClaim['ordno']}',
					inpk_ordno			= '{$order['ORD_NO']}',
					clm_no				= '{$order['CLM_NO']}',
					clm_seq				= '{$order['CLM_SEQ']}',
					clm_tpnm			= '{$order['CLM_CRT_TPNM']}',
					regdt 				= now()
				";
				$res = $GLOBALS['db']->query($query);
				$gClaim['clmsno'] = $GLOBALS['db']->lastID();

				$this->err($err=ob_get_clean());
				if ($res && $err == '') $resParent['T']++;
				unset($msg);
				$msg[] = "Insert_Result (".INPK_CLAIM.") [";
				$msg[] = sprintf("\t결과 = %s", ($res && $err == '' ? 'True' : 'False'));
				$msg[] = sprintf("\t인터파크 주문번호 = %s", $order['ORD_NO']);
				$msg[] = sprintf("\t인터파크 클레임번호 = %s", $order['CLM_NO']);
				$msg[] = sprintf("\t인터파크 클레임순번 = %s", $order['CLM_SEQ']);
				$msg[] = sprintf("\t이나무 주문번호 = %s", $gClaim['ordno']);
				$msg[] = sprintf("\t이나무 클레임번호 = %s", $gClaim['clmsno']);
				$msg[] = sprintf("\t클레임상태유형 = %s", $order['CLM_CRT_TPNM']);
				$msg[] = "]";
				$this->log($msg);
			}
			else {
				unset($msg);
				$msg[] = "Existent (".INPK_CLAIM.") [";
				$msg[] = sprintf("\t인터파크 주문번호 = %s", $order['ORD_NO']);
				$msg[] = sprintf("\t인터파크 클레임번호 = %s", $order['CLM_NO']);
				$msg[] = sprintf("\t인터파크 클레임순번 = %s", $order['CLM_SEQ']);
				$msg[] = sprintf("\t이나무 주문번호 = %s", $gClaim['ordno']);
				$msg[] = sprintf("\t이나무 클레임번호 = %s", $gClaim['clmsno']);
				$msg[] = sprintf("\t클레임상태유형 = %s", $gClaim['clm_tpnm']);
				$msg[] = "]";
				$this->log($msg);
			}

			## 주문상품
			$resChild['TOT'] += count($order['PRODUCT']['PRD']);
			foreach($order['PRODUCT']['PRD'] as $k => $item)
			{
				## 동일상품 존재하는지 체크, 없으면 Insert
				$gClaimItem = $GLOBALS['db']->fetch("select itmsno, item_sno, goodsno, clm_statnm from ".INPK_CLAIM_ITEM." where clmsno='{$gClaim['clmsno']}' and inpk_prdno='{$item['PRD_NO']}' and inpk_ordseq='{$item['ORD_SEQ']}'");
				if ($gClaimItem['itmsno'] == '')
				{
					ob_start();

					list($gClaimItem['item_sno'], $gClaimItem['goodsno']) = $GLOBALS['db']->fetch("select sno, goodsno from ".GD_ORDER_ITEM." where ordno='{$gClaim['ordno']}' and optno='{$item['OPT_NO']}' and inpk_prdno='{$item['PRD_NO']}' and inpk_ordseq='{$item['ORD_SEQ']}'");
					$query = "
					insert into ".INPK_CLAIM_ITEM." set
						clmsno			= '{$gClaim['clmsno']}',
						item_sno		= '{$gClaimItem['item_sno']}',
						goodsno			= '{$gClaimItem['goodsno']}',
						inpk_prdno		= '{$item['PRD_NO']}',
						clm_qty			= '{$item['CLM_QTY']}',
						inpk_ordseq		= '{$item['ORD_SEQ']}',
						clm_statnm		= '{$item['CURRENT_CLMPRD_STATNM']}',
						clm_dt			= '{$item['CLM_DT']}',
						clm_rsn_tpnm	= '{$item['CLM_RSN_TPNM']}',
						clm_rsn_dtl		= '{$item['CLM_RSN_DTL']}',
						latedt			= '{$item['CLM_DT']}'
					";
					$res = $GLOBALS['db']->query($query);
					$gClaimItem['itmsno'] = $GLOBALS['db']->lastID();

					$this->err($err=ob_get_clean());
					if ($res && $err == '') $resChild['T']++;
					unset($msg);
					$msg[] = "Insert_Result (".INPK_CLAIM_ITEM.") [";
					$msg[] = sprintf("\t결과 = %s", ($res && $err == '' ? 'True' : 'False'));
					$msg[] = sprintf("\t인터파크 상품코드 = %s", $item['PRD_NO']);
					$msg[] = sprintf("\t인터파크 주문순번 = %s", $item['ORD_SEQ']);
					$msg[] = sprintf("\t이나무 고유번호 = %s", $gClaimItem['goodsno']);
					$msg[] = sprintf("\t이나무 주문순번 = %s", $gClaimItem['item_sno']);
					$msg[] = sprintf("\t이나무 클레임순번 = %s", $gClaimItem['itmsno']);
					$msg[] = sprintf("\t클레임상태 = %s", $item['CURRENT_CLMPRD_STATNM']);
					$msg[] = "]";
					$this->log($msg);
				}
				else {
					unset($msg);
					$msg[] = "Existent (".INPK_CLAIM_ITEM.") [";
					$msg[] = sprintf("\t인터파크 상품코드 = %s", $item['PRD_NO']);
					$msg[] = sprintf("\t인터파크 주문순번 = %s", $item['ORD_SEQ']);
					$msg[] = sprintf("\t이나무 고유번호 = %s", $gClaimItem['goodsno']);
					$msg[] = sprintf("\t이나무 주문순번 = %s", $gClaimItem['item_sno']);
					$msg[] = sprintf("\t이나무 클레임순번 = %s", $gClaimItem['itmsno']);
					$msg[] = sprintf("\t클레임상태 = %s", $gClaimItem['clm_statnm']);
					$msg[] = "]";
					$this->log($msg);
				}

				## 클레임상태 업데이트
				if ($gClaimItem['clm_statnm'] != $item['CURRENT_CLMPRD_STATNM'])
				{
					## 이나무주문처리
					if ($gClaimItem['clm_statnm'] == '')
					{
						ob_start();
						$arr = array('sno' => $gClaimItem['item_sno'], 'ea' => $item['CLM_QTY']);
						$this->eNamooClmReceipt($gClaim['ordno'],$arr); // 클레임-클레임접수
						$this->err($err=ob_get_clean());
						$this->log(sprintf("클레임접수 처리상태 = %s", ($err == '' ? 'True' : 'False')));
					}

					switch ($item['CURRENT_CLMPRD_STATNM'])
					{
						case "클레임확정_환불완료":
							if ($order['CLM_CRT_TPNM'] != '고객교환'){
								ob_start();
								$this->eNamooClmFinal($gClaimItem['item_sno']); // 클레임-클레임확정_환불완료
								$this->err($err=ob_get_clean());
								$this->log(sprintf("클레임확정_환불완료 처리상태 = %s", ($err == '' ? 'True' : 'False')));
							}
							break;

						case "클레임취소":
							ob_start();
    						$this->eNamooClmCancel($gClaimItem['item_sno']); // 클레임-클레임취소
							$this->err($err=ob_get_clean());
							$this->log(sprintf("클레임취소 처리상태 = %s", ($err == '' ? 'True' : 'False')));
							break;
					}

					switch ($gClaimItem['clm_statnm'])
					{
						case "반품/교환수거지시":
						case "반품/교환입고확정전송":
							if ($item['CURRENT_CLMPRD_STATNM'] == '반품/교환입고완료' || $item['CURRENT_CLMPRD_STATNM'] == '클레임확정_환불완료'){
								ob_start();
	    						$this->eNamooClmStoreComp($ordno,$gClaimItem['item_sno']); // 클레임-반품/교환입고완료
								$this->err($err=ob_get_clean());
								$this->log(sprintf("반품/교환입고완료 처리상태 = %s", ($err == '' ? 'True' : 'False')));
							}
							break;

						case "교환/재배송출고지시":
						case "교환확정전송":
							if ($item['CURRENT_CLMPRD_STATNM'] == '교환/재배송출고완료' || $item['CURRENT_CLMPRD_STATNM'] == '클레임확정_환불완료'){
								ob_start();
	    						$this->eNamooExchangeComp($ordno,$gClaimItem['item_sno'],$gClaimItem['itmsno']); // 클레임-교환/재배송출고완료
								$this->err($err=ob_get_clean());
								$this->log(sprintf("교환/재배송출고완료 처리상태 = %s", ($err == '' ? 'True' : 'False')));
							}
							break;
					}

					## 상태수정
					ob_start();
					if ($order['CLM_CRT_TPNM'] == '고객교환' && $item['CURRENT_CLMPRD_STATNM'] == '클레임확정_환불완료') $item['CURRENT_CLMPRD_STATNM'] = '클레임확정';
					$query = "update ".INPK_CLAIM_ITEM." set clm_statnm='{$item['CURRENT_CLMPRD_STATNM']}', latedt='{$item['CLM_DT']}' where itmsno='{$gClaimItem['itmsno']}'";
					$res = $GLOBALS['db']->query($query);

					$this->err($err=ob_get_clean());
					if ($res && $err == '') $resChild['stat_T']++;
					unset($msg);
					$msg[] = "Update_Result (".INPK_CLAIM_ITEM.") [";
					$msg[] = sprintf("\t결과 = %s", ($res && $err == '' ? 'True' : 'False'));
					$msg[] = sprintf("\t클레임상태 = %s", $item['CURRENT_CLMPRD_STATNM']);
					$msg[] = "]";
					$this->log($msg);

					## 로그추가
					if ($gClaimItem['clm_statnm'] == ''){
						$memo = $item['CURRENT_CLMPRD_STATNM'];
					}
					else {
						$memo = "{$gClaimItem['clm_statnm']} > {$item['CURRENT_CLMPRD_STATNM']}";
					}
					$query = "insert into ".INPK_CLAIM_ITEM_LOG." values ('', '{$gClaimItem['itmsno']}', '{$memo}', '{$item['CLM_DT']}')";
					$GLOBALS['db']->query($query);
				}
			}
		}

		## 처리결과출력
		unset($msg);
		$msg[] = sprintf("Insert_Cnt (resParent) : Total[%s], True[%s], False[%s]", $resParent['TOT'], $resParent['T'], ($resParent['TOT'] - $resParent['T']));
		$msg[] = sprintf("Insert_Cnt (resChild) : Total[%s], True[%s], False[%s]", $resChild['TOT'], $resChild['T'], ($resChild['TOT'] - $resChild['T']));
		$msg[] = sprintf("Update_Stat_Cnt (resChild) : Total[%s], True[%s], False[%s]", $resChild['TOT'], $resChild['stat_T'], ($resChild['TOT'] - $resChild['stat_T']));
		echo implode("\n", $msg); // godo read
		$this->log($msg);
		$this->log("Insert_ClaimList END");
    }

    ### 클레임-클레임접수
    function eNamooClmReceipt($ordno,$arr)
    {
		$data = $GLOBALS['db']->fetch("select * from ".GD_ORDER_ITEM." where sno='{$arr['sno']}'", 1);
		$istep = ($data[cyn]=="n" && $data[dyn]=="n") ? 44 : 41;

		### 주문수량과 취소수량이 불일치할 경우 주문서 분리
		$gap = $data['ea'] - $arr['ea'];
		if ($gap){
			unset($tmp);
			foreach ($data as $k=>$v)
			{
				if ($k == 'sno');
				else if ($k == 'ea') $tmp[] = "`$k`='{$gap}'";
				else $tmp[] = "`$k`='".addslashes($v)."'";
			}
			$tmp = implode(",",$tmp);
			$query = "insert into ".GD_ORDER_ITEM." set $tmp";
			$GLOBALS['db']->query($query);
			$GLOBALS['db']->query("update ".GD_ORDER_ITEM." set ea='{$arr['ea']}', istep='$istep' where sno='{$arr['sno']}'");
		} else $GLOBALS['db']->query("update ".GD_ORDER_ITEM." set istep='$istep' where sno='{$arr['sno']}'");

		$pre = $GLOBALS['db']->fetch("select * from ".GD_ORDER." where ordno='$ordno'");

		### 주문취소 여부 체크
		$cnt = 0;
		$query = "select * from ".GD_ORDER_ITEM." where ordno='$ordno'";
		$res = $GLOBALS['db']->query($query);
		while ($data=$GLOBALS['db']->fetch($res))	if ($data[istep]>40) $cnt++;

		if ($GLOBALS['db']->count_($res)==$cnt){
			list($step2) = $GLOBALS['db']->fetch("select min(istep) from ".GD_ORDER_ITEM." where ordno='$ordno'");
			$GLOBALS['db']->query("update ".GD_ORDER." set step2=$step2 where ordno='$ordno'");
		}

		### 재고조정
		setStock($ordno);
		set_prn_settleprice($ordno);
    }

    ### 클레임-반품/교환입고완료
    function eNamooClmStoreComp($ordno,$item_sno)
    {
		### 주문아이템 처리
		$query = "update ".GD_ORDER_ITEM." set istep=42,dyn='r' where sno='{$item_sno}'";
		$GLOBALS['db']->query($query);

		### 주문 일괄 처리
		list($ordno) = $GLOBALS['db']->fetch("select ordno from ".GD_ORDER_ITEM." where sno='{$item_sno}'");
		$query = "update ".GD_ORDER." set step2=42,dyn='r' where ordno='$ordno' and step2=41";
		$GLOBALS['db']->query($query);

		### 재고조정
		setStock($ordno);
    }

    ### 클레임-교환/재배송출고완료
    function eNamooExchangeComp($ordno,$item_sno,$itmsno)
    {
		### 주문아이템 처리
		$query = "update ".GD_ORDER_ITEM." set istep=44,dyn='e',cyn='e' where sno='{$item_sno}'";
		$GLOBALS['db']->query($query);

		### 주문 일괄 처리
		list($ordno) = $GLOBALS['db']->fetch("select ordno from ".GD_ORDER_ITEM." where sno='{$item_sno}'");
		$query = "update ".GD_ORDER." set step2=44,dyn='e',cyn='e' where ordno='$ordno' and step2=41";
		$GLOBALS['db']->query($query);

		### 재주문
		$cancel = time();
		$GLOBALS['db']->query("update ".GD_ORDER_ITEM." set cancel='{$cancel}' where sno='{$item_sno}'");
		$newOrdno = reorder($ordno,$cancel);
		$GLOBALS['db']->query("update ".GD_ORDER_ITEM." set cancel='' where sno='{$item_sno}'");

		### 배송완료처리
		$gClaimItem = $GLOBALS['db']->fetch("select exchangeDvno, exchangeDvcode from ".INPK_CLAIM_ITEM." where itmsno='{$itmsno}'");
		$query = "update ".GD_ORDER." set
				step='4',
				cyn='y',
				cdt=now(),
				dyn='y',
				ddt=now(),
				confirm='admin',
				confirmdt=now(),
				deliveryno='{$gClaimItem['exchangeDvno']}',
				deliverycode='{$gClaimItem['exchangeDvcode']}'
				where ordno='{$newOrdno}'";
		$GLOBALS['db']->query($query);
		$query = "update ".GD_ORDER_ITEM." set
				istep='4',
				cyn='y',
				dyn='y',
				dvno='{$gClaimItem['exchangeDvno']}',
				dvcode='{$gClaimItem['exchangeDvcode']}'
				where ordno='{$newOrdno}'";
		$GLOBALS['db']->query($query);
    }

    ### 클레임-클레임확정_환불완료
    function eNamooClmFinal($item_sno)
    {
		### 주문아이템 처리
		$query = "update ".GD_ORDER_ITEM." set istep=44,cyn='r' where sno='{$item_sno}'";
		$GLOBALS['db']->query($query);
		### 주문 일괄 처리
		list($ordno) = $GLOBALS['db']->fetch("select ordno from ".GD_ORDER_ITEM." where sno='{$item_sno}'");
		$query = "update ".GD_ORDER." set step2=44,cyn='r' where ordno='$ordno' and step2 in (41,42)";
		$GLOBALS['db']->query($query);

		### 재고조정
		setStock($ordno);
    }

    ### 클레임-클레임취소
    function eNamooClmCancel($item_sno)
    {
		$query = "select * from ".GD_ORDER." a,".GD_ORDER_ITEM." b where a.ordno=b.ordno and b.sno='{$item_sno}'";
		$data = $GLOBALS['db']->fetch($query,1);

		$data['opt1'] = addslashes($data['opt1']);
		$data['opt2'] = addslashes($data['opt2']);
		$data['addopt'] = addslashes($data['addopt']);

		### 복원시 동일단계의 아이템이 존재하는지 체크
		$query = "
		select sno from
			".GD_ORDER_ITEM."
		where
			ordno = '$data[ordno]'
			and istep = '$data[step]'
			and goodsno = '$data[goodsno]'
			and opt1 = '$data[opt1]'
			and opt2 = '$data[opt2]'
			and addopt = '$data[addopt]'
			and price = '$data[price]'
		";
		list ($sno) = $GLOBALS['db']->fetch($query);

		if ($sno){
			$GLOBALS['db']->query("update ".GD_ORDER_ITEM." set ea=ea+$data[ea] where sno='$sno'");
			$GLOBALS['db']->query("delete from ".GD_ORDER_ITEM." where sno='$data[sno]'");
		} else {
			$GLOBALS['db']->query("update ".GD_ORDER_ITEM." set istep=$data[step],cancel=0 where sno='$data[sno]'");
		}

		### 전체 주문단계가 취소단계시 일반 주문단계로 단계복원
		if ($data[step2]){
			$query = "update ".GD_ORDER." set step2='' where ordno='$data[ordno]'";
			$GLOBALS['db']->query($query);
		}

		### 재고조정
		setStock($data[ordno]);
		set_prn_settleprice($data[ordno]);
    }

    ### 구매확정조회
    function orderCompListForMulti($arrXml)
    {
		if (is_array($arrXml['ORDER_LIST']['ORDER']) === false){
			$this->log("Empty-arrXml[ORDER_LIST][ORDER] (Update_OrderItem_Compdt Before)");
			return;
		}
		$this->log("Update_OrderItem_Compdt START");

		$resParent = array('TOT' => count($arrXml['ORDER_LIST']['ORDER']), 'T' => 0);
		$resChild = array('TOT' => 0, 'T' => 0);
		foreach($arrXml['ORDER_LIST']['ORDER'] as $order)
		{
			## 동일주문 존재하는지 체크, 있으면 Update
			$gOrder = $GLOBALS['db']->fetch("select ordno from ".GD_ORDER." where inpk_ordno='{$order['ORD_NO']}'");
			if ($gOrder['ordno'] == ''){
				unset($msg);
				$msg[] = "Fail : Non-existent order of openstyle [";
				$msg[] = sprintf("\t인터파크 주문번호 = %s", $order['ORD_NO']);
				$msg[] = "]";
				$this->log($msg);
				continue;
			}
			else {
				unset($msg);
				$msg[] = "Existent (".GD_ORDER.") [";
				$msg[] = sprintf("\t인터파크 주문번호 = %s", $order['ORD_NO']);
				$msg[] = sprintf("\t이나무 주문번호 = %s", $gOrder['ordno']);
				$msg[] = "]";
				$this->log($msg);
			}

			## 주문상품
			$resChild['TOT'] += count($order['PRODUCT']['PRD']);
			foreach($order['PRODUCT']['PRD'] as $k => $item)
			{
				## 동일상품 존재하는지 체크, 있으면 Update
				$gOrderItem = $GLOBALS['db']->fetch("select sno, goodsno from ".GD_ORDER_ITEM." where ordno='{$gOrder['ordno']}' and inpk_prdno='{$item['PRD_NO']}' and inpk_ordseq='{$item['ORD_SEQ']}'");
				if ($gOrderItem['sno'] == ''){
					unset($msg);
					$msg[] = "Fail : Non-existent orderItem of openstyle [";
					$msg[] = sprintf("\t인터파크 상품코드 = %s", $item['PRD_NO']);
					$msg[] = sprintf("\t인터파크 주문순번 = %s", $item['ORD_SEQ']);
					$msg[] = "]";
					$this->log($msg);
					continue;
				}
				else {
					ob_start();

					$query = "update ".GD_ORDER_ITEM." set inpk_compdt='{$item['ORD_COMP_DT']}' where ordno='{$gOrder['ordno']}' and inpk_prdno='{$item['PRD_NO']}' and inpk_ordseq='{$item['ORD_SEQ']}'";
					$res = $GLOBALS['db']->query($query);

					$this->err($err=ob_get_clean());
					if ($res && $err == '') $resChild['T']++;
					unset($msg);
					$msg[] = "Update_Result (".GD_ORDER_ITEM.") [";
					$msg[] = sprintf("\t인터파크 상품코드 = %s", $item['PRD_NO']);
					$msg[] = sprintf("\t인터파크 주문순번 = %s", $item['ORD_SEQ']);
					$msg[] = sprintf("\t이나무 고유번호 = %s", $gOrderItem['goodsno']);
					$msg[] = sprintf("\t이나무 주문순번 = %s", $gOrderItem['sno']);
					$msg[] = "]";
					$this->log($msg);
				}
			}
		}

		## 처리결과출력
		unset($msg);
		$msg[] = sprintf("Update_Cnt (resParent) : Total[%s], True[%s], False[%s]", $resParent['TOT'], '-', '-');
		$msg[] = sprintf("Update_Cnt (resChild) : Total[%s], True[%s], False[%s]", $resChild['TOT'], $resChild['T'], ($resChild['TOT'] - $resChild['T']));
		echo implode("\n", $msg); // godo read
		$this->log($msg);
		$this->log("Update_OrderItem_Compdt END");
    }
}

?>
