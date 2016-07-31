<?

class eTax
{
	var $godo, $gPath, $tPath;

	function eTax()
	{
		$this->gPath = dirname(__FILE__)."/../conf/godomall.cfg.php";
		$this->tPath = dirname(__FILE__)."/../conf/tax.cfg.php";
		$this->getGodo();
	}

	function getGodo()
	{
		global $godo;

		if (isset($godo) === true) $this->godo = $godo;
		else {
			if (!is_file($this->gPath)) return false;
			$file = file($this->gPath);
			$this->godo = decode($file[1],1);
		}

		if (file_exists($this->tPath))
		{
			$file = file($this->tPath);
			$this->godo[tax] = decode($file[1],1);
			if (isset($godo) === true){
				$godo['tax'] = $this->godo['tax'];
			}
		}
		else {
			$this->updateTaxPoint();

			$tmp = $this->godo;
			unset($tmp[tax]);
			$ret = encode($tmp,1);
			if(is_file($this->gPath)) unlink($this->gPath);
			$fp = fopen($this->gPath,"w");
			fwrite($fp,"<?/* \n");
			fwrite($fp,$ret."\n");
			fwrite($fp,"*/?>");
			fclose($fp);
		}
	}

	function hashdata(&$data)
	{
		/***************************************************************************************************
		*  hashdata 생성
		*    - 데이터 무결성을 검증하는 데이터로 요청시 필수 항목.
		*    - godosno 를 조합한후 md5 방식으로 생성한 해쉬값.
		***************************************************************************************************/

		$data[godosno]	= $this->godo[sno];					# 상점번호
		$data[hashdata]	= md5($data[godosno]);				# hashdata 생성
	}

	function isExists($args)
	{
		if ($this->godo[sno] == '' || $this->godo[sno] == '0') return array('600', "false - 쇼핑몰 환경정보에 상점아이디가 비어 있습니다.^고도몰로 문의하세요.");
		if ($this->godo[sno] != $args[godosno]) return array('600', "false - 상점아이디가 환경정보의 상점아이디와 일치하지 않습니다.^고도몰로 문의하세요.");

		$data=array();
		$this->hashdata($data);
		return array('400', readurl("http://godotax.godo.co.kr/sock_isExists.php?godosno={$data[godosno]}&hashdata={$data[hashdata]}"));
	}

	function putMerchant($args)
	{
		if ($this->godo[sno] == '' || $this->godo[sno] == '0') return array('600', "false - 쇼핑몰 환경정보에 상점아이디가 비어 있습니다.^고도몰로 문의하세요.");
		if ($this->godo[sno] != $args[godosno]) return array('600', "false - 상점아이디가 환경정보의 상점아이디와 일치하지 않습니다.^고도몰로 문의하세요.");

		$data=array();
		$this->hashdata($data);
		$data = array_merge($data, $args);
		if ($data[userid] != '') $data[userid] = 'CGO_' . $data[userid];	# 위탁자 아이디
		unset($data[mode]);
		unset($data[dummy]);
		return array('400', readpost("http://godotax.godo.co.kr/sock_putMerchant.php", $data));
	}

	function putTaxbill($args)
	{
		if ($this->godo[sno] == '' || $this->godo[sno] == '0') return array('600', "false - 쇼핑몰 환경정보에 상점아이디가 비어 있습니다.^ 고도몰로 문의하세요.");
		if ($this->godo[tax] == '' || $this->godo[tax] == '0') return array('600', "false - 전자세금계산서 잔여포인트가 부족합니다.^추가 충전하셔서 사용하세요.");

		$data=array();
		$this->hashdata($data);

		ob_start();
		$taxData = $GLOBALS[db]->fetch("select * from ".GD_TAX." where `sno`='{$args[chk]}'", "assoc");
		$ordData = $GLOBALS[db]->fetch("select email, mobileOrder, cashreceipt from ".GD_ORDER." where ordno='{$taxData[ordno]}'");
		$data[method]			= "ORD";					# 발행방법
		$data[doc_number]		= "ORD{$taxData[ordno]}_{$taxData[sno]}";	# 고유관리번호

		### 공급업체정보
		include dirname(__FILE__)."/../conf/config.php";
		$regnum = str_replace("-", "", trim($cfg[compSerial]));
		$data[sup_regnum]		= sprintf("%s-%s-%s", substr($regnum,0,3), substr($regnum,3,2), substr($regnum,5,5));	# 공급업체사업자번호(XXX-XX-XXXXX)
		$data[sup_company]		= $cfg[compName];			# 공급업체회사명
		$data[sup_employer]		= $cfg[ceoName];			# 공급업체대표자명
		$data[sup_address]		= $cfg[address];			# 공급업체주소
		$data[sup_bizcond]		= $cfg[service];			# 공급업체업태
		$data[sup_bizitem]		= $cfg[item];				# 공급업체종목
		$data[sup_empemail]		= $cfg[adminEmail];			# 공급업체담당자이메일
		$data[sup_empmobile]	= $cfg[smsAdmin];			# 공급업체담당자핸드폰번호

		### 수요업체정보
		$regnum = str_replace("-", "", trim($taxData[busino]));
		$data[buy_regnum]		= sprintf("%s-%s-%s", substr($regnum,0,3), substr($regnum,3,2), substr($regnum,5,5));	# 수요업체사업자번호(XXX-XX-XXXXX)
		$data[buy_company]		= $taxData[company];		# 수요업체회사명
		$data[buy_employer]		= $taxData[name];			# 수요업체대표자명
		$data[buy_address]		= $taxData[address];		# 수요업체주소
		$data[buy_bizcond]		= $taxData[service];		# 수요업체업태
		$data[buy_bizitem]		= $taxData[item];			# 수요업체종목
		$data[buy_empemail]		= $ordData[email];			# 수요업체담당자이메일
		$data[buy_empmobile]	= $ordData[mobileOrder];	# 수요업체담당자핸드폰번호

		### 금액
		$data[tax_supprice]		= $taxData[supply];			# 공급가액총액
		$data[tax_taxprice]		= $taxData[surtax];			# 세액총액
		$data[pay_totalprice]	= $taxData[price];			# 합계금액
		$data[gen_tm]			= str_replace("-", "", $taxData[issuedate]) . '000000';	# 작성일자

		### 내역
		$data[tax_gendates][0]	= str_replace("-", "", $taxData[issuedate]);		# 품목공급일자
		$data[item_names][0]	= $taxData[goodsnm];		# 품목명
		$data[tax_supprices][0]	= $taxData[supply];			# 공급가액
		$data[tax_taxprices][0]	= $taxData[surtax];			# 세액
		ob_end_clean();

		if ($ordData[cashreceipt] != '') return array('400', "false - 현금영수증이 발행된 내역입니다.^현금영수증과 세금계산서는 동시에 발급되어 질 수 없습니다.");

		$out = readpost("http://godotax.godo.co.kr/sock_putTaxbill.php", $data);

		if ($out == 'true'){
			$GLOBALS[db]->query("update ".GD_TAX." set step=3, agreedt = now(), doc_number = '{$data[doc_number]}' where `sno`='{$args[chk]}'");
			$this->godo[tax]--;
			$this->updateTaxPoint();
		}

		return array('400', $out);
	}

	function putSugiTaxbill($args)
	{
		if ($this->godo[sno] == '' || $this->godo[sno] == '0') return array('600', "false - 쇼핑몰 환경정보에 상점아이디가 비어 있습니다.^ 고도몰로 문의하세요.");
		if ($this->godo[tax] == '' || $this->godo[tax] == '0') return array('600', "false - 전자세금계산서 잔여포인트가 부족합니다.^추가 충전하셔서 사용하세요.");

		$headers = apache_request_headers();
		foreach ( $args as $k => $v ){
			if (is_array($v) === true)
				foreach ( $v as $sk => $sv ) $args[$k][$sk] = iconv("UTF-8","EUC-KR",$sv);
			else $args[$k] = iconv("UTF-8","EUC-KR",$v);
		}
		

		/** 공급자 & 공급받는자 체크 **/
		if (strlen($args[SupNo]) == 0) return array('600', "false - 공급자 등록번호는 필수입니다.");
		if (strlen($args[SupComp]) == 0) return array('600', "false - 공급자 상호는 필수입니다.");
		if (strlen($args[SupEmployer]) == 0) return array('600', "false - 공급자 성명은 필수입니다.");
		if (strlen($args[SupAddr]) == 0) return array('600', "false - 공급자 주소는 필수입니다.");
		if (strlen($args[SupCond]) == 0) return array('600', "false - 공급자 업태는 필수입니다.");
		if (strlen($args[SupItem]) == 0) return array('600', "false - 공급자 종목는 필수입니다.");
		if (strlen($args[BuyNo]) == 0) return array('600', "false - 공급받는자 등록번호는 필수입니다.");
		if (strlen($args[BuyComp]) == 0) return array('600', "false - 공급받는자 상호는 필수입니다.");
		if (strlen($args[BuyEmployer]) == 0) return array('600', "false - 공급받는자 성명은 필수입니다.");
		if (strlen($args[BuyAddr]) == 0) return array('600', "false - 공급받는자 주소는 필수입니다.");

		$data=array();
		$this->hashdata($data);

		$data[method]			= "WRI";					# 발행방법
		$data[doc_number]		= "WRI" . date('YmdHis');	# 고유관리번호
		$data[tax_type]			= $args[TaxType];			# 과세종류 (VAT:과세(세금계산서), FRE:면세(계산서), RCP:영수증)
		$data[bill_type]		= $args[Indicator];			# 청구서종류 (T01:영수함, T02:청구함)
		$data[ref_volume]		= $args[Volume];			# 책번호 권
		$data[ref_number]		= $args[Number];			# 책번호 호
		$data[ref_serial]		= $args[SerialNo];			# 일련번호

		### 공급업체정보
		$regnum = str_replace("-", "", trim($args[SupNo]));
		$data[sup_regnum]		= sprintf("%s-%s-%s", substr($regnum,0,3), substr($regnum,3,2), substr($regnum,5,5));	# 공급업체사업자번호(XXX-XX-XXXXX)
		$data[sup_company]		= $args[SupComp];			# 공급업체회사명
		$data[sup_employer]		= $args[SupEmployer];		# 공급업체대표자명
		$data[sup_address]		= $args[SupAddr];			# 공급업체주소
		$data[sup_bizcond]		= $args[SupCond];			# 공급업체업태
		$data[sup_bizitem]		= $args[SupItem];			# 공급업체종목
		$data[sup_empsector]	= $args[SupSector];			# 공급업체담당부서
		$data[sup_employee]		= $args[SupEmployee];		# 공급업체담당자명
		$data[sup_empemail]		= $args[SupEmail];			# 공급업체담당자이메일
		$data[sup_empmobile]	= $args[SupPhone];			# 공급업체담당자핸드폰번호

		### 수요업체정보
		$regnum = str_replace("-", "", trim($args[BuyNo]));
		$data[buy_regnum]		= sprintf("%s-%s-%s", substr($regnum,0,3), substr($regnum,3,2), substr($regnum,5,5));	# 수요업체사업자번호(XXX-XX-XXXXX)
		$data[buy_company]		= $args[BuyComp];			# 수요업체회사명
		$data[buy_employer]		= $args[BuyEmployer];		# 수요업체대표자명
		$data[buy_address]		= $args[BuyAddr];			# 수요업체주소
		$data[buy_bizcond]		= $args[BuyCond];			# 수요업체업태
		$data[buy_bizitem]		= $args[BuyItem];			# 수요업체종목
		$data[buy_empsector]	= $args[BuySector];			# 수요업체담당부서
		$data[buy_employee]		= $args[BuyEmployee];		# 수요업체담당자명
		$data[buy_empemail]		= $args[BuyEmail];			# 수요업체담당자이메일
		$data[buy_empmobile]	= $args[BuyPhone];			# 수요업체담당자핸드폰번호

		### 금액
		$data[tax_supprice]		= str_replace(",", "", $args[TotalMoa]);		# 공급가액총액(공급금액)
		$data[tax_taxprice]		= str_replace(",", "", $args[TotalTax]);		# 세액총액
		$data[pay_totalprice]	= str_replace(",", "", $args[MoaTax]);			# 합계금액
		$data[gen_tm]			= sprintf("%04d%02d%02d000000", $args[TaxYear], $args[TaxMonth], $args[TaxDay]);	# 작성일자

		### 내역
		for ($i = 1; $i < 5; $i++){
			$month		= str_replace(",", "", $args["LinMonth{$i}"]) == 0 ? '' : str_replace(",", "", $args["LinMonth{$i}"]);
			$day		= str_replace(",", "", $args["LinDay{$i}"]) == 0 ? '' : str_replace(",", "", $args["LinDay{$i}"]);
			$itemnm		= $args["LinItem{$i}"];
			$unit		= $args["LinUnit{$i}"];
			$qty		= str_replace(",", "", $args["LinQty{$i}"]) == 0 ? '' : str_replace(",", "", $args["LinQty{$i}"]);
			$pri		= str_replace(",", "", $args["LinPri{$i}"]) == 0 ? '' : str_replace(",", "", $args["LinPri{$i}"]);
			$moa		= str_replace(",", "", $args["LinMoa{$i}"]) == 0 ? '' : str_replace(",", "", $args["LinMoa{$i}"]);
			$tax		= str_replace(",", "", $args["LinTax{$i}"]) == 0 ? '' : str_replace(",", "", $args["LinTax{$i}"]);
			$remark		= $args["LinRemark{$i}"];

			if (strlen($month . $day . $itemnm . $unit . $qty . $pri . $moa . $tax . $remark) == 0) continue;
			if (strlen($month) == 0) return array('600', "false - 라인의 월은 필수입니다");
			else if (($month*1) != ($args[TaxMonth]*1)) return array('600', "false - 작성월과 라인의 월은 같아야합니다");
			else if (strlen($day) == 0) return array('600', "false - 라인의 일은 필수입니다");
			else if (strlen($itemnm) == 0) return array('600', "false - 라인의 품목명은 필수입니다");
			else if (strlen($moa) == 0) return array('600', "false - 라인의 공급가액은 필수입니다");

			$data[tax_gendates][]	= sprintf("%04d%02d%02d", $args[TaxYear], $month, $day);		# 품목공급일자
			$data[item_names][]		= $itemnm;		# 품목명
			$data[item_units][]		= $unit;		# 품목규격
			$data[item_nums][]		= $qty;			# 품목수량
			$data[item_dangas][]	= $pri;			# 품목단가
			$data[tax_supprices][]	= $moa;			# 공급가액
			$data[tax_taxprices][]	= $tax;			# 세액
			$data[item_bigos][]		= $remark;		# 품목비고
		}
		if (count($data[tax_gendates]) == 0) return array('600', "false - 품목정보는 하나이상 순서대로 기재하셔야 합니다.");

		$out = readpost("http://godotax.godo.co.kr/sock_putTaxbill.php", $data);

		if ($out == 'true'){
			$this->godo[tax]--;
			$this->updateTaxPoint();
		}

		return array('400', $out);
	}

	function getTaxbill($args)
	{
		if ($this->godo[sno] == '' || $this->godo[sno] == '0') return array('600', "false - 쇼핑몰 환경정보에 상점아이디가 비어 있습니다.^고도몰로 문의하세요.");

		$data=array();
		$this->hashdata($data);
		return array('400', readurl("http://godotax.godo.co.kr/sock_getTaxbill.php?godosno={$data[godosno]}&hashdata={$data[hashdata]}&doc_number={$args[doc_number]}"));
	}

	function getTaxsugiList($args)
	{
		if ($this->godo[sno] == '' || $this->godo[sno] == '0') return array('600', "false - 쇼핑몰 환경정보에 상점아이디가 비어 있습니다.^고도몰로 문의하세요.");

		$data=array();
		$this->hashdata($data);
		$data = array_merge($data, $args);
		return array('400', readpost("http://godotax.godo.co.kr/sock_getTaxsugiList.php", $data));
	}

	function ccrTaxbill($args)
	{
		if ($this->godo[sno] == '' || $this->godo[sno] == '0') return array('600', "false - 쇼핑몰 환경정보에 상점아이디가 비어 있습니다.^고도몰로 문의하세요.");

		$data=array();
		$this->hashdata($data);
		$out = readurl("http://godotax.godo.co.kr/sock_ccrTaxbill.php?godosno={$data[godosno]}&hashdata={$data[hashdata]}&doc_number={$args[doc_number]}");
		if (preg_match('/DEL:/', $out)){
			$this->godo[tax]++;
			$this->updateTaxPoint();
		}
		return array('400', $out);
	}

	function updateTaxPoint()
	{
		$ret = encode($this->godo[tax],1);
		if(is_file($this->tPath)) unlink($this->tPath);
		$fp = fopen($this->tPath,"w");
		fwrite($fp,"<?/* \n");
		fwrite($fp,$ret."\n");
		fwrite($fp,"*/?>");
		fclose($fp);
	}

}

?>