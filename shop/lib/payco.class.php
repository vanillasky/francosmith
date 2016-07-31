<?php
/*
 * payco CLASS
 *
 * @author payco.class.php workingby <bumyul2000@godo.co.kr>
 * @version 1.0
 * @date 2015-06-09
 */
class payco {
	//설정값
	var $paycoCfg, $cfg, $set, $cfgMobileShop, $screenType;

	//button display area
	var $paycoDisplayID = 'paycoDisplayID';

	// payco script plugin url
	var $pluginUrl = 'https://static-bill.nhnent.com/payco/checkout/js/payco.js';

	// payco script plugin url - mobile
	var $pluginMobileUrl = 'https://static-bill.nhnent.com/payco/checkout/js/payco_mobile.js';

	// payco button image url
	var $buttonImgUrl = 'https://payco.godo.co.kr/image/btn/';

	function payco()
	{
		global $paycoCfg, $cfg, $set, $cfgMobileShop;

		if(!$cfg) $cfg = Core::loader('config')->load('config');
		if(!$set) $set = Core::loader('config')->load('configpay');
		if(!$cfgMobileShop && is_file(dirname(__FILE__) . '/../conf/config.mobileShop.php')){
			include dirname(__FILE__) . '/../conf/config.mobileShop.php';
		}
		//백업DB payco.config.php 파일로 복구
		if(!$paycoCfg && !is_file(dirname(__FILE__) . '/../conf/payco.cfg.php')){
			Core::loader('paycoConfig')->restorePaycoBackupData();
		}
		if(!$paycoCfg){
			@include dirname(__FILE__) . '/../conf/payco.cfg.php';
		}

		$this->screenType	= $this->getScreenType();
		$this->paycoCfg		= $paycoCfg;
		$this->setDefaultPaycoCfg();
		$this->cfg			= $cfg;
		$this->set			= $set;
		$this->cfgMobileShop= $cfgMobileShop;
	}

	/*
	* '페이코 서비스 상품 설정', '페이코 서비스 이용 설정' 초기값 셋팅
	* @param
	* @return
	* @date 2015-06-09
	*/
	function setDefaultPaycoCfg()
	{
		if(!$this->paycoCfg['useYn']) $this->paycoCfg['useYn'] = 'all';
		if(!$this->paycoCfg['button_checkout']) $this->paycoCfg['button_checkout'] = 'A';
		if(!$this->paycoCfg['button_checkoutDetail_A']) $this->paycoCfg['button_checkoutDetail_A'] = 'A1';
		if(!$this->paycoCfg['button_checkoutDetail_B']) $this->paycoCfg['button_checkoutDetail_B'] = 'B1';
		if(!$this->paycoCfg['button_checkoutDetail_C']) $this->paycoCfg['button_checkoutDetail_C'] = 'C1';
		switch($this->paycoCfg['button_checkout']){
			case 'A':
				$this->paycoCfg['button_checkoutDetail'] = $this->paycoCfg['button_checkoutDetail_A'];
			break;

			case 'B':
				$this->paycoCfg['button_checkoutDetail'] = $this->paycoCfg['button_checkoutDetail_B'];
			break;

			case 'C':
				$this->paycoCfg['button_checkoutDetail'] = $this->paycoCfg['button_checkoutDetail_C'];
			break;
		}
		if(!$this->paycoCfg['button_easypay']) $this->paycoCfg['button_easypay'] = 'A1';
	}

	/*
	* HTML 버튼
	* @param string [type : CHECKOUT-간편구매(체크아웃)형, EASYPAY-간편결제형], [mobile : true-모바일샵, false-PC샵], [checkoutType : goodsView-상품상세페이지, goodsCart-장바구니]
	* @return string
	* @date 2015-06-09
	*/
	function getButtonHtmlCode($type, $mobile, $checkoutType='')
	{
		//사용설정 여부 체크
		if($this->check_useAble($type, $mobile) == true && $this->check_level($type, $GLOBALS['sess']) == true){
			$buttonHtml = $this->getButtonPlugin($type, $mobile, $checkoutType);

			return $buttonHtml;
		}

		return false;
	}

	/*
	* 버튼 plugin
	* @param string [type : CHECKOUT-간편구매(체크아웃)형, EASYPAY-간편결제형], [mobile : true-모바일샵, false-PC샵], [checkoutType : goodsView-상품상세페이지, goodsCart-장바구니]
	* @return string
	* @date 2015-06-09
	*/
	function getButtonPlugin($type, $mobile, $checkoutType='')
	{
		if($type != 'CHECKOUT' && $type != 'EASYPAY') return false;
		if($type == 'CHECKOUT'){
			if($checkoutType != 'goodsView' && $checkoutType != 'goodsCart') return false;
		}

		if($mobile){
			$url = $this->pluginMobileUrl;
		}
		else {
			$url = $this->pluginUrl;
		}

		if($mobile){
			//모바일샵 버튼
			if($type == 'CHECKOUT') $styleLayout = 'width: 100%;';

			$button = '
				<div id="'.$this->paycoDisplayID.'" style="display: inline-block; text-align: center; '.$styleLayout.'"></div>
				<script type="text/javascript" src="'.$url.'" charset="UTF-8"></script>
			';

			if($type == 'CHECKOUT') {
				//간편구매(체크아웃)형
				if($checkoutType == 'goodsView'){
					//상품상세페이지
					$button .= $this->getCheckoutScriptMobile_view();
				}
				else {
					//장바구니페이지
					$button .= $this->getCheckoutScriptMobile_cart();
				}
			}
			else {
				//간편결제형
				$button .= $this->getEasypayScript();
			}
		}
		else {
			//PC샵 버튼
			$button = '
				<div id="'.$this->paycoDisplayID.'" style="display: inline;"></div>
				<script type="text/javascript" src="'.$url.'" charset="UTF-8"></script>
			';

			if($type == 'CHECKOUT') {
				//간편구매(체크아웃)형
				if($checkoutType == 'goodsView'){
					//상품상세페이지
					$button .= $this->getCheckoutScript_view();
				}
				else if ($checkoutType == 'goodsCart'){
					//장바구니페이지
					$button .= $this->getCheckoutScript_cart();
				}
			}
			else {
				//간편결제형
				$button .= $this->getEasypayScript();
			}
		}

		return $button;
	}

	/**
	 * CHECKOUT형 script - 상품상세페이지
	 * @param
	 * @return string
	 * @date 2015-06-09
	 * ORDER_METHOD			- 주문유형 : CHECKOUT / EASYPAY
	 * BUTTON_TYPE			- 버튼타입 선택 - default : A1
	 * BUTTON_HANDLER		- 구매하기 버튼 이벤트 Handler 함수 등록
	 * BUTTON_HANDLER_ARG	- handler 함수 argument
	 * DISPLAY_PROMOTION	- 이벤트 문구 출력 여부
	 * DISPLAY_ELEMENT_ID	- 노출될 element id
	 */
	function getCheckoutScript_view()
	{
		$script = '
			<script type="text/javascript">
			//<![CDATA[
				var paycoPopup;
				function paycoCheckoutSubmit()
				{
					var f = window.document.frmView;
					var _chk = false;
					var screenType = "'.$this->screenType.'";
					var submitTarget = "";

					if(!f || f == undefined){
						alert("Payco 서비스 사용이 불가능합니다.\n스킨을 확인하여 주세요.");
						return false;
					}

					//form 원본 정보
					var oriAction	= f.action;
					var oriTarget	= f.target;
					var oriMode		= f.mode.value;

					// 멀티옵션 사용中
					if (typeof nsGodo_MultiOption == "object") {

						var opt_cnt = 0, data;

						nsGodo_MultiOption.clearField();
						for (var k in nsGodo_MultiOption.data) {
							data = nsGodo_MultiOption.data[k];
							if (data && typeof data == "object") {
								nsGodo_MultiOption.addField(data, opt_cnt);
								opt_cnt++;
							}
						}

						if (opt_cnt > 0) _chk = true;
					}

					if (_chk || (typeof chkGoodsForm == "undefined" ? chkForm(f) : chkGoodsForm(f))) {

						if(screenType == "MOBILE"){
							submitTarget = "ifrmHidden";
						}
						else {
							'.$this->getVoidPopupOpenScript().'

							submitTarget = "paycoPopup";
						}

						f.action		= "paycoCheckout.php";
						f.mode.value	= "goodsView";
						f.target		= submitTarget;
						f.submit();

						//원본정보 복구
						f.action		= oriAction;
						f.mode.value	= oriMode;
						f.target		= oriTarget;
					}
				}

				Payco.Button.register({
					SELLER_KEY			: "'.$this->paycoCfg['paycoSellerKey'].'",
					ORDER_METHOD		: "CHECKOUT",
					BUTTON_TYPE			: "'.$this->paycoCfg['button_checkoutDetail'].'",
					BUTTON_HANDLER		: paycoCheckoutSubmit,
					BUTTON_HANDLER_ARG  : [""],
					DISPLAY_PROMOTION	: "Y",
					DISPLAY_ELEMENT_ID	: "'.$this->paycoDisplayID.'"
				});
				//]]>
			</script>
			';

			return $script;
	}

	/**
	 * CHECKOUT형 script - 장바구니 페이지
	 * @param
	 * @return string
	 * @date 2015-06-09
	 * ORDER_METHOD			- 주문유형 : CHECKOUT / EASYPAY
	 * BUTTON_TYPE			- 버튼타입 선택 - default : A1
	 * BUTTON_HANDLER		- 구매하기 버튼 이벤트 Handler 함수 등록
	 * BUTTON_HANDLER_ARG	- handler 함수 argument
	 * DISPLAY_PROMOTION	- 이벤트 문구 출력 여부
	 * DISPLAY_ELEMENT_ID	- 노출될 element id
	 */
	function getCheckoutScript_cart()
	{
		$script = '
			<script type="text/javascript">
			//<![CDATA[
				var paycoPopup;
				function paycoCheckoutSubmit()
				{
					var screenType = "'.$this->screenType.'";
					var idxs = document.getElementsByName("idxs[]");
					var param = "";
					var submitTarget = "";

					if(idxs.length < 1){
						alert("구매할 상품이 없습니다.");
						return false;
					}

					for (var i=0,m=idxs.length;i<m;i++) {
						if (idxs[i].checked == true) param += "&idxs[]="+idxs[i].value;
					}

					if(screenType == "MOBILE"){
						submitTarget = window;
					}
					else {
						'.$this->getVoidPopupOpenScript().'

						submitTarget = paycoPopup;
					}

					submitTarget.location.href="./paycoCheckout.php?mode=goodsCart"+param;
				}

				Payco.Button.register({
					SELLER_KEY			: "'.$this->paycoCfg['paycoSellerKey'].'",
					ORDER_METHOD		: "CHECKOUT",
					BUTTON_TYPE			: "'.$this->paycoCfg['button_checkoutDetail'].'",
					BUTTON_HANDLER		: paycoCheckoutSubmit,
					BUTTON_HANDLER_ARG  : [""],
					DISPLAY_PROMOTION	: "Y",
					DISPLAY_ELEMENT_ID	: "'.$this->paycoDisplayID.'"
				});
				//]]>
			</script>
			';

			return $script;
	}

	/**
	 * MOBILE CHECKOUT 형 script - 상품상세페이지
	 * @param
	 * @return string
	 * @date 2015-06-09
	 * ORDER_METHOD			- 주문유형 : CHECKOUT / EASYPAY
	 * BUTTON_TYPE			- 버튼타입 선택 - default : A1
	 * BUTTON_HANDLER		- 구매하기 버튼 이벤트 Handler 함수 등록
	 * BUTTON_HANDLER_ARG	- handler 함수 argument
	 * DISPLAY_PROMOTION	- 이벤트 문구 출력 여부
	 * DISPLAY_ELEMENT_ID	- 노출될 element id
	 */
	function getCheckoutScriptMobile_view()
	{
		$script = '
			<script type="text/javascript">
			//<![CDATA[
				function paycoCheckoutSubmit()
				{
					var f = window.document.frmView;
					var _chk = false;
					var screenType = "'.$this->screenType.'";
					var submitTarget = "";

					if(!f || f == undefined){
						alert("Payco 서비스 사용이 불가능합니다.\n스킨을 확인하여 주세요.");
						return false;
					}

					//form 원본 정보
					var oriAction	= f.action;
					var oriTarget	= f.target;
					var oriMode		= f.mode.value;

					// 멀티옵션 사용中
					if (typeof nsGodo_MultiOption == "object") {

						var opt_cnt = 0, data;

						nsGodo_MultiOption.clearField();
						for (var k in nsGodo_MultiOption.data) {
							data = nsGodo_MultiOption.data[k];
							if (data && typeof data == "object") {
								nsGodo_MultiOption.addField(data, opt_cnt);
								opt_cnt++;
							}
						}

						if (opt_cnt > 0) _chk = true;
						if (_chk === false && (typeof chkEAForm != "undefined" ? chkEAForm("") : true) === false) { //멀티옵션 선택하지 않았는데도 결제창 호출 프로세스가 진행되어 검증로직 추가
							return false;
						}
					}
					else{
						var f = window.document.frmView;

						if(!f || f == undefined){
							alert("Payco 서비스 사용이 불가능합니다.\n스킨을 확인하여 주세요.");
							return false;
						}

						if (window.m2CheckForm2) {
							if(!window.m2CheckForm2("goodsorder-hide")){
								return false;
							}
						}
						else if (window.m2CheckForm) {
							if(!window.m2CheckForm("goodsorder-hide")){
								return false;
							}
						}
						else {
							// 입력옵션 필드값 설정
							if (chkForm(f)) {
								var addopt_inputable = window.document.getElementsByName("addopt_inputable[]");
								for (var i=0,m=addopt_inputable.length;i<m ;i++ ) {

									if (typeof addopt_inputable[i] == "object") {
										var v = addopt_inputable[i].value.trim();
										if (v) {
											var tmp = addopt_inputable[i].getAttribute("option-value").split("^");
											tmp[2] = v;
											v = tmp.join("^");
										}
										else {
											v = "";
										}
										window.document.getElementsByName("_addopt_inputable[]")[i].value = v;
									}
								}
							}
							else {
								return false;
							}
						}
					}

					f.action		= "'.$this->cfg['rootDir'].'/goods/paycoCheckout.php?isMobile=Y";
					f.mode.value	= "goodsView";
					f.target		= "ifrmHidden";
					f.submit();

					//원본정보 복구
					f.action		= oriAction;
					f.mode.value	= oriMode;
					f.target		= oriTarget;
				}

				Payco.Button.register({
					SELLER_KEY			: "'.$this->paycoCfg['paycoSellerKey'].'",
					ORDER_METHOD		: "CHECKOUT",
					BUTTON_TYPE			: "'.$this->paycoCfg['button_checkoutDetail'].'",
					BUTTON_HANDLER		: paycoCheckoutSubmit,
					BUTTON_HANDLER_ARG  : [""],
					DISPLAY_PROMOTION	: "Y",
					DISPLAY_ELEMENT_ID	: "'.$this->paycoDisplayID.'"
				});
				//]]>
			</script>
		';

		return $script;
	}

	/**
	 * MOBILE CHECKOUT 형 script - 장바구니 페이지
	 * @param
	 * @return string
	 * @date 2015-06-09
	 * ORDER_METHOD			- 주문유형 : CHECKOUT / EASYPAY
	 * BUTTON_TYPE			- 버튼타입 선택 - default : A1
	 * BUTTON_HANDLER		- 구매하기 버튼 이벤트 Handler 함수 등록
	 * BUTTON_HANDLER_ARG	- handler 함수 argument
	 * DISPLAY_PROMOTION	- 이벤트 문구 출력 여부
	 * DISPLAY_ELEMENT_ID	- 노출될 element id
	 */
	function getCheckoutScriptMobile_cart()
	{
		global $cfg;

		$script = '
			<script type="text/javascript">
			//<![CDATA[
				function paycoCheckoutSubmit()
				{
					var idx = document.getElementsByName("idx[]");
					if(idx.length > 0){
						var idxs = idx;
					}
					else {
						var idxs = document.getElementsByName("idxs[]");
					}

					var param = "";

					if(idxs.length < 1){
						alert("구매할 상품이 없습니다.");
						return false;
					}

					for (var i=0,m=idxs.length;i<m;i++) {
						if (idxs[i].checked == true) param += "&idxs[]="+idxs[i].value;
					}

					ifrmHidden.location.href="'.$this->cfg['rootDir'].'/goods/paycoCheckout.php?mode=goodsCart&isMobile=Y&"+param;
				}

				Payco.Button.register({
					SELLER_KEY			: "'.$this->paycoCfg['paycoSellerKey'].'",
					ORDER_METHOD		: "CHECKOUT",
					BUTTON_TYPE			: "'.$this->paycoCfg['button_checkoutDetail'].'",
					BUTTON_HANDLER		: paycoCheckoutSubmit,
					BUTTON_HANDLER_ARG  : [""],
					DISPLAY_PROMOTION	: "Y",
					DISPLAY_ELEMENT_ID	: "'.$this->paycoDisplayID.'"
				});
				//]]>
			</script>
		';

		return $script;
	}

	/**
	 * EASYPAY 형 script
	 * @param
	 * @return string
	 * @date 2015-06-09
	 * ORDER_METHOD			- 주문유형 : CHECKOUT / EASYPAY
	 * BUTTON_TYPE			- 버튼타입 선택 - default : A1
	 * DISPLAY_PROMOTION	- 이벤트 문구 출력 여부
	 * DISPLAY_ELEMENT_ID	- 노출될 element id
	 */
	function getEasypayScript()
	{
		$script = '
			<script type="text/javascript">
			//<![CDATA[
				Payco.Button.register({
					SELLER_KEY			: "'.$this->paycoCfg['paycoSellerKey'].'",
					ORDER_METHOD		: "EASYPAY",
					BUTTON_TYPE			: "'.$this->paycoCfg['button_easypay'].'",
					DISPLAY_PROMOTION	: "Y",
					DISPLAY_ELEMENT_ID	: "'.$this->paycoDisplayID.'"
				});
				//]]>
			</script>
		';

		return $script;
	}

	/**
	 * admin page - ID validation check
	 * @param array
	 * @return string - 필수값누락,  false - 필수값누락되지않음
	 * @date 2015-06-09
	 */
	function check_paycoPostData($apiStartType)
	{
		global $_POST;

		if(!$_POST['useType'])			return '이용할 페이코 서비스 종류를 선택해 주세요.';
		if(!$_POST['paycoSellerKey'])	return '페이코 가맹점 코드를 선택해 주세요.';
		if(!$_POST['paycoCpId'])		return '페이코 상점ID를 선택해 주세요.';
		if(!$_POST['testYn'])			return '사용 설정을 선택해 주세요.';

		return false;
	}

	/**
	 * 주문가능여부 체크
	 * @param string, int, string [type : 버튼노출타입 CHECKOUT / EASYPAY],[goodsno : 상품번호], [isMobile : Y - 모바일샵]
	 * @return string
	 * @date 2015-06-09
	 */
	function check_paycoOrderAble($type, $goodsno, $isMobile='')
	{
		//사용여부 설정 체크
		if(!$this->check_useAble($type, $isMobile)){
			$errorMsg = '사용설정이 되어있지 않거나 가맹점코드가 존재하지 않습니다.';
		}
		//가격대체문구 체크
		if($this->check_strPrice($goodsno)){
			$errorMsg = '페이코 서비스로 구매할 수 없는 상품이 포함되어있습니다.';
		}
		//재고체크
		if($this->check_runout($goodsno)){
			$errorMsg = '품절된 상품이 포함되어 있습니다.';
		}
		//예외 카테고리 체크
		if($this->check_exception_category($goodsno)){
			$errorMsg = '페이코 서비스로 구매할 수 없는 상품이 포함되어있습니다.';
		}
		//예외 상품 체크
		if($this->check_exception_goods($goodsno)){
			$errorMsg = '페이코 서비스로 구매할 수 없는 상품이 포함되어있습니다.';
		}
		//구매가능 회원그룹, 진열여부, 판매여부
		if($this->check_goodsOpen($goodsno, $isMobile)){
			$errorMsg = '페이코 서비스로 구매할 수 없는 상품이 포함되어있습니다.';
		}
		//구매가능 회원등급
		$goodsBuyable = getGoodsBuyable($goodsno);
		if($goodsBuyable === "buyable2")
		{
			$errorMsg = "회원 전용 구매 상품입니다. 로그인 후 시도해주세요.";
		}
		else if($goodsBuyable === "buyable3") {
			$errorMsg = "특정 회원 전용 구매 상품입니다.";
		}

		if($errorMsg) {
			return $errorMsg;
		}

		return false;
	}

	/**
	 * 결제완료 가능여부 체크
	 * @param string, int, string [type : 버튼노출타입 CHECKOUT / EASYPAY],[goodsno : 상품번호], [isMobile : Y - 모바일샵]
	 * @return string
	 * @date 2015-06-09
	 */
	function check_paycoOrderAbleComplet($type, $goodsno, $isMobile='')
	{
		//사용여부 설정 체크
		if(!$this->check_useAble($type, $isMobile)){
			$errorMsg = '사용설정이 되어있지 않거나 가맹점코드가 존재하지 않습니다.';
		}
		//가격대체문구 체크
		if($this->check_strPrice($goodsno)){
			$errorMsg = '페이코 서비스로 구매할 수 없는 상품이 포함되어있습니다.';
		}
		//재고체크
		if($this->check_runout($goodsno)){
			$errorMsg = '품절된 상품이 포함되어 있습니다.';
		}
		//예외 카테고리 체크
		if($this->check_exception_category($goodsno)){
			$errorMsg = '페이코 서비스로 구매할 수 없는 상품이 포함되어있습니다.';
		}
		//예외 상품 체크
		if($this->check_exception_goods($goodsno)){
			$errorMsg = '페이코 서비스로 구매할 수 없는 상품이 포함되어있습니다.';
		}
		//구매가능 회원그룹, 진열여부, 판매여부
		if($this->check_goodsOpen($goodsno, $isMobile)){
			$errorMsg = '페이코 서비스로 구매할 수 없는 상품이 포함되어있습니다.';
		}

		if($errorMsg) {
			return $errorMsg;
		}

		return false;
	}

	/**
	 * 구매가능 회원그룹, 진열여부, 판매여부
	 * @param int 상품번호
	 * @return blooean true - 주문불가능상품, false - 주문가능상품
	 * @date 2015-06-09
	 */
	function check_goodsOpen($goodsno, $isMobile='')
	{
		$goodsName = rtnOpenYn($goodsno, 'D', $isMobile);

		if(count($goodsName) > 0){
			return true;
		}

		return false;
	}

	/**
	 * 사용여부설정 체크
	 * @param boolean or string [type : CHECKOUT-간편구매(체크아웃)형, EASYPAY-간편결제형], [mobile : true or Y - 모바일샵, false or '' - PC샵 ]
	 * @return boolean true - 사용가능, false - 사용불가능
	 * @date 2015-06-09
	 */
	function check_useAble($type, $mobile='')
	{
		global $sess, $_SERVER;

		if(!$type) return false;

		if($mobile){
			//모바일샵사용여부
			if($this->paycoCfg['useYn'] != 'mobile' && $this->paycoCfg['useYn'] != 'all') return false;
		}
		else {
			//PC샵사용여부
			if($this->paycoCfg['useYn'] != 'pc' && $this->paycoCfg['useYn'] != 'all') return false;
		}

		if($type == 'CHECKOUT'){
			//간편구매(체크아웃) 사용여부
			if($this->paycoCfg['useType'] != 'CE') return false;
		}
		else if($type == 'EASYPAY'){
			//간편결제 사용여부
			if($this->paycoCfg['useType'] != 'CE' && $this->paycoCfg['useType'] != 'E') return false;
		}

		return true;
	}

	/**
	 * 버튼노출 사용권한 체크
	 * @param
	 * @return boolean true - 노출가능, false - 노출불가능
	 * @date 2015-06-09
	 */
	function check_level($type, $sess='')
	{
		if(!$type) return false;

		//관리자는 테스트, 실제사용 모두 노출
		if($sess['level'] > 79){
			return true;
		}

		if($this->paycoCfg['testYn'] == 'N'){
			//실제사용모드
			if($type == 'CHECKOUT'){
				//간편구매 - 비회원만 구매가능
				if(!$sess){
					return true;
				}
			}
			else {
				//간편결제 - 회원, 비회원 결제가능
				return true;
			}
		}

		return false;
	}

	/**
	 * 가격대체문구 체크
	 * @param
	 * @return boolean true - 가격대체문구0, false - 가격대체문구X
	 * @date 2015-06-09
	 */
	function check_strPrice($goodsno)
	{
		global $goodsModel;

		if(!$goodsModel){
			$goodsModel = Clib_Application::getModelClass('Goods_Goods');
			$goodsModel->load($goodsno);
		}

		if($goodsModel){
			if(strlen($goodsModel->getData('strprice')) > 0){
				return true;
			}
		}
		else {
			list($strprice) = $GLOBALS[db]->fetch("SELECT strprice FROM ".GD_GOODS." WHERE goodsno='".$goodsno."' LIMIT 1");
			if(strlen($strprice) > 0){
				return true;
			}
		}

		return false;
	}

	/**
	 * 재고체크
	 * @param
	 * @return boolean true - 품절O, false - 품절X
	 * @date 2015-06-09
	 */
	function check_runout($goodsno)
	{
		global $goodsModel;

		if(!$goodsModel){
			$goodsModel = Clib_Application::getModelClass('Goods_Goods');
			$goodsModel->load($goodsno);
		}

		if($goodsModel){
			if($goodsModel->getRunout()) return true;
		}
		else {
			list($runout, $usestock, $totstock) = $GLOBALS[db]->fetch("SELECT runout, usestock, totstock FROM ".GD_GOODS." WHERE goodsno='".$goodsno."' LIMIT 1");
			if($runout || ($usestock && $totstock < 1)) return true;
		}

		return false;
	}

	/**
	 * 예외 상품 체크
	 * @param int 상품번호
	 * @return boolean true - 예외상품 포함, false - 예외상품 미포함
	 * @date 2015-06-09
	 */
	function check_exception_goods($goodsno)
	{
		global $validation;

		if(!$validation){
			include_once(dirname(__FILE__) . '/validation.class.php');
			$validation = new Validation();
		}

		if($validation->check_exception_goods($goodsno, $this->paycoCfg['e_exceptions'])){
			return true;
		}

		return false;
	}

	/**
	 * 예외 카테고리 체크
	 * @param int 상품번호
	 * @return boolean true - 예외상품 포함, false - 예외상품 미포함
	 * @date 2015-06-09
	 */
	function check_exception_category($goodsno)
	{
		global $validation;

		if(!$validation){
			include_once(dirname(__FILE__) . '/validation.class.php');
			$validation = new Validation();
		}

		if($validation->check_exception_category($goodsno, $this->paycoCfg['e_category'])){
			return true;
		}

		return false;
	}

	/**
	 * 재고삭감
	 * @param int 주문번호
	 * @return
	 * @date 2015-06-09
	 */
	function adjustStock($ordno)
	{
		setStock($ordno);
	}

	/**
	 * API 통신
	 * @param string api 타입
	 * @return array  response data  or blooean false
	 * @date 2015-06-09
	 */
	function apiExecute($apiType)
	{
		global $paycoApi, $cart, $_POST;

		if(!$paycoApi){
			$paycoApi = Core::loader('paycoApi');
		}

		$param = array();
		$param['cart']	= $cart;
		$param['post']	= $_POST;

		switch($apiType){
			case 'auth':
				if(!$this->paycoCfg['crypt_key']){
					$this->paycoCfg['crypt_key'] = $param['post']['crypt_key'];
				}
				$requestData	= $this->setAuthData($param);
			break;

			case 'reserve' :
				$requestData	= $this->setReverseData($param);
			break;

			default :
				return false;
			break;
		}

		$responseData	= $paycoApi->request($apiType, $requestData);
		$responseData	= gd_json_decode($responseData);

		return $responseData;
	}

	/**
	 * Info API data 반환
	 * @param  array  param data
	 * @return array api request data
	 * @date 2015-06-09
	 */
	function setAuthData($param)
	{
		$request['crypt_key'] = $this->paycoCfg['crypt_key'];
		$request['enc'] = $this->setAuth_EncData($param);

		return $request;
	}

	/**
	 * crypt_key 생성
	 * @param void
	 * @return string
	 * @date 2015-06-09
	 */
	function setAuth_secretKeyData()
	{
		return substr(md5(microtime().rand(1, 1000)), 0, 10);
	}

	/**
	 * ID설정 데이터 가공
	 * @param  array  param data
	 * @return array
	 * @date 2015-06-09
	 */
	function setAuth_EncData($param)
	{
		global $godo;

		if(!$godo){
			$config = Core::loader('config');
			$godo = $config->load('godo');
		}

		$postData = $param['post'];

		//페이코 api key
		$encData['seller_key']	= $param['post']['paycoSellerKey'];
		//페이코 상점 ID
		$encData['cp_id']		= $postData['paycoCpId'];
		//쇼핑몰 고유번호
		$encData['sno']			= $godo['sno'];
		//쇼핑몰 도메인
		$encData['shop_domain']	= ProtocolPortDomain();
		//테스트 여부
		$encData['test']		= $postData['testYn'];

		return $encData;
	}

	/**
	 * reverse API data 반환
	 * @param  array  param data
	 * @return array api request data
	 * @date 2015-06-09
	 */
	function setReverseData($param)
	{
		$request					= $this->setReverse_EncData($param);
		$request['orderProducts']	= $this->setReverse_OrderItemData($param);
		$request['extraData']		= $this->setReverse_extraData($param);
		if($param['post']['paycoType'] == 'CHECKOUT'){
			$orderItemDelivery = $this->setReverse_OrderItemDeliveryData($param['post']['ordno']);
			$request['orderProducts'] = array_merge((array)$request['orderProducts'], (array)$orderItemDelivery);
		}

		return $request;
	}

	/**
	 * 설정, 주문데이터 가공
	 * @param  array  param data
	 * @return array
	 * @date 2015-06-09
	 */
	function setReverse_EncData($param)
	{
		global $sess, $_SERVER, $db, $order;

		if(!is_object($order)) {
			$order = Core::loader('order');
			$order->load($param['post']['ordno']);
		}

		$inflowPath = $orderMethod = $orderChannel = '';

		//주문유형
		if($param['post']['paycoType'] == 'CHECKOUT'){
			$orderMethod = $param['post']['paycoType'];
			//유입경로
			if($param['post']['paycoCheckoutType'] == 'goodsView'){
				$inflowPath = 'PRODUCT_DETAIL';
			}
			else {
				$inflowPath = 'CART';
			}
		}
		else {
			if($sess){
				$orderMethod = 'EASYPAY_F';
			}
			else {
				$orderMethod = 'EASYPAY';
			}
		}

		//주문채널 - 디바이스중심
		if($this->screenType == 'MOBILE' || $param['post']['isMobile'] == 'Y'){
			$orderChannel = 'MOBILE';
		}
		else {
			$orderChannel = 'PC';
		}

		$orderData = $db->fetch("SELECT * FROM ".GD_ORDER." WHERE ordno='".$param['post']['ordno']."'");

		list($delivery_price) = $db->fetch("SELECT delivery_price FROM ".GD_ORDER_ITEM_DELIVERY." WHERE ordno='".$param['post']['ordno']."' and delivery_type='100' LIMIT 1");
		if(!$delivery_price){
			$delivery_price = 0;
		}

		//return parameter
		$returnUrlParam['ordno']	= $orderData['ordno'];			//주문번호
		$returnUrlParam['isMobile'] = $param['post']['isMobile'];	//모바일샵여부
		$returnUrlParam['paycoType']= $param['post']['paycoType'];	//페이코 타입

		//encdata
		$encData['seller_key']		= $this->paycoCfg['paycoSellerKey'];	//가맹점코드
		$encData['orderMethod']		= $orderMethod;							//주문유형 (CHECKOUT=간편구매(체크아웃) 형, EASYPAY=간편결제 형 (비로그인), EASYPAY_F=간편결제 형 (로그인))
		$encData['orderChannel']	= $orderChannel;						//주문채널(PC or MOBILE)
		$encData['inflowPath']		= $inflowPath;							//유입경로 - orderMethod가 CHECKOUT인 경우(PRODUCT_DETAIL=상품상세, CART=장바구니)
		$encData['returnUrl']		= ProtocolPortDomain() . $this->cfg['rootDir'] . '/order/card/payco/card_result.php'; //결제성공여부 분기페이지
		if ($param['post']['paycoType'] == 'CHECKOUT' && $this->cfg['ssl'] === '1' && $this->cfg['ssl_type'] === 'godo' && $_SERVER['HTTPS'] != 'on') {
			if ($this->cfg['ssl_port'] != '' && ($this->cfg['ssl_port'] != 443)) {
				$encData['returnUrl'] = ProtocolPortDomain() . ':' . $this->cfg['ssl_port'] . $this->cfg['rootDir'] . '/order/card/payco/card_result.php';
			}
			$encData['returnUrl'] = str_replace('http://', 'https://', $encData['returnUrl']);
		}
		$encData['returnUrlParam']	= gd_json_encode($returnUrlParam);		//결제완료페이지 return param
		$encData['ordno']			= $orderData['ordno'];					//주문번호
		$encData['settleprice']		= $orderData['settleprice'];			//최초결제금액
		$encData['prn_settleprice'] = $orderData['prn_settleprice'];		//결제금액(취소반영)
		$encData['goodsprice']		= $orderData['goodsprice'];				//최초상품금액
		$encData['delivery']		= $orderData['delivery'];				//총배송비
		$encData['step']			= $orderData['step'];					//주문상태
		$encData['step2']			= $orderData['step2'];					//주문상태
		$encData['orddt']			= $orderData['orddt'];					//주문일
		$encData['area_delivery']	= $delivery_price;						//총 지역별 배송비
		$encData['totalTaxfreeAmt'] = $order->getTaxFreeAmount();			//면세금액(면세상품의 공급가액 합)
		$encData['totalTaxableAmt'] = $order->getTaxAmount();				//과세금액(과세상품의 공급가액 합)
		$encData['totalVatAmt']		= $order->getVatAmount();				//부가세(과세상품의 부가세 합)

		return $encData;
	}

	/**
	 * order item 데이터 가공
	 * @param  array  param data
	 * @return array
	 * @date 2015-06-09
	 */
	function setReverse_OrderItemData($param)
	{
		global $db;

		$data_orderItem = array();

		$rootPath = $this->getShopRootPath();
		$mobileRootPath = $this->getMobileRootPath();
		$goodsPath = ($param['post']['isMobile']) ? $mobileRootPath . '/goods/view.php?goodsno=' : $rootPath . '/goods/goods_view.php?goodsno=';

		$result = $db->query("SELECT * FROM ".GD_ORDER_ITEM." WHERE ordno='".$param['post']['ordno']."'");

		$i = 0;
		while($orderItem = $db->fetch($result)){
			if($orderItem['goodsno']){
				$goods = $db->fetch("SELECT * FROM ".GD_GOODS." WHERE goodsno='".$orderItem['goodsno']."' LIMIT 1");
			}

			$data_orderItem[$i]['cpId']				= $this->paycoCfg['paycoCpId'];						//CPID
			$data_orderItem[$i]['productInfoUrl']	= $goodsPath . $orderItem['goodsno'];				//상품정보URL (배송비/수수료가 아닌 경우는 필수)
			$data_orderItem[$i]['orderConfirmUrl']	= $rootPath . '/goods/goods_view.php?goodsno=' . $orderItem['goodsno']; //주문완료 후 주문상품을 확인할 수 있는 url
			$data_orderItem[$i]['orderConfirmMobileUrl'] = $mobileRootPath . '/goods/view.php?goodsno=' . $orderItem['goodsno']; //주문완료 후 주문상품을 확인할 수 있는 모바일 url
			if($goods['img_s']){
				$data_orderItem[$i]['productImageUrl']	= $rootPath . '/data/goods/'.$goods['img_s'];		//이미지URL (배송비/수수료가 아닌 경우는 필수)
			}
			$data_orderItem[$i]['sno']				= $orderItem['sno'];								//order item sno
			$data_orderItem[$i]['ordno']			= $param['post']['ordno'];							//주문번호
			$data_orderItem[$i]['goodsno']			= $orderItem['goodsno'];							//상품번호
			$data_orderItem[$i]['goodsnm']			= iconv('euc-kr', 'utf-8', $goods['goodsnm']);		//상품명
			$data_orderItem[$i]['opt1']				= iconv('euc-kr', 'utf-8', $orderItem['opt1']);		//옵션1
			$data_orderItem[$i]['opt2']				= iconv('euc-kr', 'utf-8', $orderItem['opt2']);		//옵션2
			$data_orderItem[$i]['addopt']			= iconv('euc-kr', 'utf-8', $orderItem['addopt']);	//추가옵션
			$data_orderItem[$i]['price']			= $orderItem['price'];								//판매가격
			$data_orderItem[$i]['ea']				= $orderItem['ea'];									//수량
			$data_orderItem[$i]['istep']			= $orderItem['istep'];								//주문상태
			$data_orderItem[$i]['oi_delivery_idx']	= $orderItem['oi_delivery_idx'];					//배송비테이블 idx
			$data_orderItem[$i]['taxationType']		= $this->getTaxationType($goods['tax']);			//과세타입

			$i++;
		}

		return $data_orderItem;
	}

	/**
	 * reverse api extradata 가공
	 * @param  array  param data
	 * @return array
	 * @date 2015-06-09
	 */
	function setReverse_extraData($param)
	{
		global $db;

		$goodsno = $viewPage = $rootPath = '';
		$extraData = array();

		if($param['post']['isMobile'] == 'Y' || $this->screenType == 'MOBILE'){
			if($param['post']['isMobile'] == 'Y'){
				$rootPath = $this->getMobileRootPath();
				$viewPage = 'view';
			}
			else {
				$rootPath = $this->getShopRootPath();
				$viewPage = 'goods_view';
			}

			$result = $db->query("SELECT * FROM ".GD_ORDER_ITEM." WHERE ordno='".$param['post']['ordno']."'");
			$cnt = $db->count_($result);

			while($orderItem = $db->fetch($result)){
				$goodsno = $orderItem['goodsno'];
				if($goodsno) break;
			}

			if((int)$cnt == 1){
				$cancelMobileUrl = $rootPath . '/goods/' . $viewPage . '.php?goodsno=' . $goodsno;
			}
			else {
				$cancelMobileUrl = $rootPath . '/goods/cart.php';
			}

			$extraData['cancelMobileUrl'] = $cancelMobileUrl;
		}

		return $extraData;
	}

	/**
	 * 배송 데이터 가공
	 * @param  int 주문번호
	 * @return array
	 * @date 2015-06-09
	 */
	function setReverse_OrderItemDeliveryData($ordno)
	{
		global $db;

		$orderItemDeliveryData = array();
		$result = $db->query("SELECT oi_delivery_idx FROM ".GD_ORDER_ITEM." WHERE ordno='".$ordno."' GROUP BY oi_delivery_idx");
		$i=0;
		while($orderItem = $db->fetch($result)){
			if($orderItem['oi_delivery_idx'] > 0) {
				$_orderItemDelivery = array();
				$_orderItemDelivery = $db->fetch("SELECT prn_delivery_price, delivery_type FROM ".GD_ORDER_ITEM_DELIVERY." WHERE oi_delivery_idx='".$orderItem['oi_delivery_idx']."' and ordno='".$ordno."' LIMIT 1");

				$orderItemDeliveryData[$i]['cpId']					= $this->paycoCfg['paycoCpId'];				// 상점ID
				$orderItemDeliveryData[$i]['oi_delivery_idx']		= $orderItem['oi_delivery_idx'];			//배송비 테이블 idx
				$orderItemDeliveryData[$i]['ordno']					= $ordno;									//주문번호
				$orderItemDeliveryData[$i]['prn_delivery_price']	= $_orderItemDelivery['prn_delivery_price'];//배송비
				$orderItemDeliveryData[$i]['delivery_type']			= $_orderItemDelivery['delivery_type'];		//배송타입
				$orderItemDeliveryData[$i]['taxationType']			= 'TAXATION';		//복합과세(배송비는 과세처리)
			}

			$i++;
		}

		return $orderItemDeliveryData;
	}

	/**
	 * 페이코주문번호 저장
	 * @param  int,string 주문번호, 팝업url
	 * @return
	 * @date 2015-06-09
	 */
	function saveReserveOrderNo($ordno, $returnUrl)
	{
		global $db;

		$returnUrlArr = explode("/", $returnUrl);
		$payco_reserve_order_no = array_pop($returnUrlArr);

		if($payco_reserve_order_no){
			$result = $db->query("UPDATE ".GD_ORDER." SET payco_reserve_order_no='".$payco_reserve_order_no."' WHERE ordno='".$ordno."'");
		}

		if(!$payco_reserve_order_no || !$result) msg("페이코 주문번호를 찾을 수 없습니다.", -1);
	}

	/*
	 * 관리자페이지 버튼이미지
	 * @param  string 이미지명, 이미지확장자
	 * @return string url
	 * @date 2015-06-09
	 */
	function getAdminBtnImageUrl($name, $type)
	{
		return $this->buttonImgUrl . $name . '.' . $type;
	}

	/**
	 * 페이코 페이지 노출
	 * @param  string page url
	 * @return
	 * @date 2015-06-09
	 */
	function locateSettlePopupPage($url, $isMobile='')
	{
		if($this->screenType == 'MOBILE' || $isMobile == 'Y'){
			echo '
			<script type="text/javascript">
				parent.window.location.replace("'.$url.'");
			</script>
			';
		}
		else {
			echo '
			<script type="text/javascript">
				window.location.replace("'.$url.'");
			</script>
			';
		}
		exit;
	}

	/*
	페이코 결제취소 성공 결과처리
	*/
	function paycoCancel($arr_data, $cancel_sno='', $part='N', $msg=array(), $payco_coupon_data=array())
	{
		global $db;
		//pgcancel	pg취소 여부( r = 부분취소, n = 취소안함, y=취소완료 )

		$msg[] = $arr_data['ordno'].' ('.date('Y:m:d H:i:s').')';
		$msg[] = '-----------------------------------';
		$msg[] = '취소금액	: '.number_format($arr_data['cancelTotalAmt']).'원';
		$msg[] = '환불수수료	: '.number_format($arr_data['cancelTotalFeeAmt']).'원';
		$msg[] = '최종취소금액	: '.number_format($arr_data['cancelTotalAmt'] - $arr_data['cancelTotalFeeAmt']).'원';
		$msg[] = '-----------------------------------';

		if($part == 'Y') {//부분취소
			$order_query = "update ".GD_ORDER." set ";
			$order_query .= " pgcancel = 'r' , repayprice = ifnull(repayprice, 0) + ".$arr_data['cancelTotalAmt'];
			$order_query .= " , settlelog=concat(ifnull(settlelog,''),'\n\n".implode("\n",$msg)."') ";
			if($payco_coupon_data['payco_firsthand_refund'] === 'Y') $order_query .= " , payco_firsthand_refund='Y' ";
			if($payco_coupon_data['payco_coupon_repay'] === 'Y') $order_query .= " , payco_coupon_repay='Y' ";
			$order_query .= " where ordno='".$arr_data['ordno']."'";

			$db->query($order_query);

			$query2 = "update ".GD_ORDER_CANCEL." set rprice = '".$arr_data['cancelTotalAmt']."', rfee = '".$arr_data['cancelTotalFeeAmt']."', ccdt = '".date('Y:m:d H:i:s')."', pgcancel = 'r' where sno = '".$cancel_sno."'";
			$db->query($query2);

		}
		else {//전체취소
			$db -> query("update ".GD_ORDER." set pgcancel='y', repayprice = repayprice + ".$arr_data['cancelTotalAmt']." , settlelog=concat(ifnull(settlelog,''),'\n\n".implode("\n",$msg)."') where ordno='".$arr_data['ordno']."'");
			$db -> query("update ".GD_ORDER_CANCEL." set rprice = '".$arr_data['cancelTotalAmt']."', rfee = '".$arr_data['cancelTotalFeeAmt']."', ccdt = '".date('Y:m:d H:i:s')."', pgcancel='y' where ordno='".$arr_data['ordno']."'");
		}
	}

	/*
	페이코 결제취소 실패 로그저장
	*/
	function paycoCancelFailLog($ordno, $msg=Array())
	{
		if(empty($msg) === false) {
			global $db;
			$db -> query("update ".GD_ORDER." set settlelog=concat(ifnull(settlelog,''),'"."\n\n".implode("\n",$msg)."') where ordno='".$ordno."'");
		}
	}


	/**
	 * 스크린타입 - checkout 사용
	 * @param
	 * @return
	 * @date 2015-06-09
	 */
	function getScreenType()
	{
		$screenType = 'PC';
		if(is_file(dirname(__FILE__) . '/../lib/nScreenPayment.php')) $nScreenPayment = Core::Loader('nScreenPayment');

		if($nScreenPayment){
			$screenType = $nScreenPayment->getScreenType();
		}
		else {
			$mobileAgentArr = array(
				'iPhone',
				'Mobile',
				'UP.Browser',
				'Android',
				'BlackBerry',
				'Windows CE',
				'Nokia',
				'webOS',
				'Opera Mini',
				'SonyEricsson',
				'opera mobi',
				'Windows Phone',
				'IEMobile',
				'POLARIS',
				'lgtelecom',
				'NATEBrowser',
			);

			foreach($mobileAgentArr as $mobileAgent) {
				if(strstr(strtolower($_SERVER['HTTP_USER_AGENT']), strtolower($mobileAgent))) {
					$screenType = 'MOBILE';
					break;
				}
			}
		}

		return $screenType;
	}


	/**
	 * 관리자 설정 유효성체크 반환
	 * @param
	 * @return  $responseData 는 사용불가 항목만 반환됨
	 * @date 2015-06-09
	 */
	function setAdminValidateCheck($responseData)
	{
		$returnData = '';
		$_returnData = array();

		//사용불가 가맹점코드
		if(preg_match('/seller\_key/', $responseData)){
			$_returnData[] = 'seller_key@N'; //사용불가 노출
			$_returnData[] = 'cp_id@'; //메시지 미노출
		}

		//사용불가 상점ID
		if(preg_match('/cp\_id/', $responseData)){
			$_returnData[] = 'seller_key@Y'; //사용가능 노출
			$_returnData[] = 'cp_id@N'; //사용불가 노출
		}

		$returnData = @implode("^", $_returnData);

		return $returnData;
	}

	/**
	 * admin page indb return msg
	 * @param string
	 * @return
	 * @date 2015-06-09
	 */
	function returnAdminMsg($result, $msg)
	{
		echo $result . '|' . $msg;
		exit;
	}

	/**
	 * get mobile root path
	 * @param
	 * @return string
	 * @date 2015-06-09
	 */
	function getMobileRootPath()
	{
		return ProtocolPortDomain() . $this->cfgMobileShop['mobileShopRootDir'];
	}

	/**
	 * get pc shop root path
	 * @param
	 * @return string
	 * @date 2015-06-09
	 */
	function getShopRootPath()
	{
		return ProtocolPortDomain() . $this->cfg['rootDir'];
	}

	/*
	 * 주문 취소번호 조회
	 * $ordno int 주문번호
	 * $cancel_no int 제외취소번호
	*/
	function getOrderCancelNo($ordno, $cancel_no)
	{
		global $db;

		$cancel_no_query = $db->_query_print("SELECT i.cancel FROM ".GD_ORDER_ITEM." i LEFT JOIN ".GD_ORDER_CANCEL." c ON i.cancel=c.sno WHERE i.ordno=[s] ", $ordno);
		if($cancel_no) $cancel_no_query .= $db->_query_print(" AND NOT i.cancel=[i]", $cancel_no);
		$cancel_no_query .= $db->_query_print(" AND i.cancel > 0 AND (c.pgcancel='r' OR c.pgcancel='y') group by i.cancel");

		$cancel_no = $db->_select($cancel_no_query);
		return $cancel_no;
	}

	/**
	 * 빈팝업 노출
	 * @param
	 * @return
	 * @date 2015-06-09
	 */
	function getVoidPopupOpenScript()
	{
		$script = '
			var bodyW = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
			var bodyH = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;
			var posX = (bodyW - 635) / 2;
			var posY = (bodyH - 546) / 2;

			paycoPopup = window.open("about:blank", "paycoPopup", "width=635px, height=546px, top="+posY+"px, left="+posX+"px ");
			window.name = "payco_parent";
			if(paycoPopup) paycoPopup.focus();
		';

		return $script;
	}

	/**
	 * 페이코 결제타입 반환
	 * @param string
	 * @return string
	 * @date 2015-06-09
	 */
	function getPaycoSettleType($type)
	{
		$paycoSettleType = '';
		$paycoSettleType = (strtoupper($type) == 'CHECKOUT') ? '페이코 간편구매' : '페이코 간편결제';

		return $paycoSettleType;
	}

	/**
	 * qrTmp 반환
	 * @param string
	 * @return string
	 * @date 2015-06-09
	 */
	function getqrTmp($paycoType)
	{
		$payco_settle_type = $returnField = '';
		if(strtoupper($paycoType) == 'CHECKOUT'){
			$payco_settle_type = 'CHECKOUT';
		}
		else {
			$payco_settle_type = 'EASYPAY';
		}

		$returnField =  "pg='payco', settleInflow='payco', payco_settle_type='".$payco_settle_type."',";

		return $returnField;
	}

	function getOrderData($ordno, $table, $query_type, $arr_coulms=Array(), $desc='')
	{
		global $db;

		if(empty($arr_coulms)) $coulms = '*';
		else $coulms = implode(', ', $arr_coulms);

		$query = $db->_query_print("SELECT ".$coulms." FROM ".$table." WHERE ordno=[i] ".$desc, $ordno);

		switch($query_type) {
			case 'fetch_true' :
				$order_data = $db->fetch($query, true);
				break;
			case 'fetch_false' :
				$order_data = $db->fetch($query);
				break;
			case 'query' :
				$order_data = $db->query($query);
				break;
		}
		return $order_data;
	}

	/**
	 * 빈팝업 닫기
	 * @param
	 * @return
	 * @date 2015-06-09
	 */
	function closeVoidPopup()
	{
		echo '
		<script type="text/javascript">
		if(window.parent.paycoPopup){
			window.parent.paycoPopup.close();
		}
		</script>
		';
	}

	/**
	 * msg alert
	 * @param string 메시지, 페이코타입, 모바일여부, 페이지타입
	 * @return
	 * @date 2015-06-09
	 */
	function msgLocate($msg, $paycoType, $isMobile='', $pageType = '')
	{
		$secondFactor = $thirdFactor = '';
		if($paycoType == 'CHECKOUT'){
			if($this->screenType == 'MOBILE') {
				//간편구매 모바일 디바이스
				$secondFactor = -1;
			}
			else {
				//간편구매 PC 디바이스
				$secondFactor = 'close';
			}
		}
		else {
			if($isMobile == 'Y'){
				//간편결제 모바일샵
				$secondFactor = '../../../..'.$this->cfgMobileShop['mobileShopRootDir'].'/ord/order.php';
			}
			else {
				//간편결제 PC샵

				$secondFactor = '../../../..'.$this->cfg['rootDir'].'/order/order.php';
				if($this->screenType != 'MOBILE') {
					switch($pageType){
						case 'card_gate':
							msg($msg);
							echo "
							<script type='text/javascript'>
							opener.location.replace('".$secondFactor."');
							window.close();
							</script>
							";
							exit;
						break;

						default:
							$this->closeVoidPopup();
						break;
					}
				}
			}
			if(!$thirdFactor) $thirdFactor = 'parent';
		}

		msg($msg, $secondFactor, $thirdFactor);
		exit;
	}

	/**
	 * 적립금 유효성 체크 (사용적립금 과 보유적립금 체크)
	 * @param  array
	 * @return boolean  true - 사용가능, false - 사용불가
	 * @date 2015-06-09
	 */
	function checkEmoney($orderData)
	{
		global $db;

		$memberEmoney = 0;

		$res = $db->query("SELECT emoney FROM ".GD_MEMBER." WHERE m_no='".$orderData['m_no']."' LIMIT 1");
		list($memberEmoney) = $db->fetch($res);

		if ($orderData['emoney'] <= $memberEmoney) return true;

		return false;
	}

	/**
	 * 페이코 취소상태 체크
	 * @param  integer
	 * @return boolean  true - 페이코 취소완료, false - 취소가능
	 * @date 2015-06-09
	 */
	function checkCancelYn($cancel_sno)
	{
		if($cancel_sno) {
			global $db;
			$res = $db->query("SELECT pgcancel FROM ".GD_ORDER_CANCEL." WHERE sno='".$cancel_sno."'");
			list($pgcancel) = $db->fetch($res);
			if($pgcancel == 'n') return false;
			else return true;
		}
	}

	/**
	 * 페이코 결제시 네이버 마일리지&캐쉬 사용불가
	 * @param  string, integer
	 * @return boolean  true - 네이버 마일리지&캐쉬 시도중, false - 네이버 마일리지&캐쉬 사용안함
	 * @date 2015-06-09
	 */
	function checkNcash($useyn, $totalUseAmount)
	{
		if($useyn == 'Y' && $totalUseAmount > 0){
			return true;
		}

		return false;
	}

	/**
	 * 면세, 과세, 결합 종류
	 * @param  inteager
	 * @return
	 * @date 2015-06-09
	 */
	function getTaxationType($tax)
	{
		$returnTaxation = '';
		switch((int)$tax){
			case 0:
				$returnTaxation = 'DUTYFREE';
			break;

			case 1: default:
				$returnTaxation = 'TAXATION';
			break;
		}

		return $returnTaxation;
	}

	/**
	 * 취소된 주문상품번호 조회
	 * @param  str, int, int, array
	 * @return
	 * @date 2015-06-09
	 */
	function getCancelItem($payco_settle_type, $ordno, $sno, $item_data)
	{
		global $db;

		if(strtoupper($payco_settle_type) != 'CHECKOUT') {//간편결제인 경우 모든 item이 취소되었을때 상태변경 전송

			$item_query = "SELECT * FROM ".GD_ORDER_ITEM." WHERE ordno='".$ordno."' group by opt1, opt2 order by sno desc ";
			$order_res = $db->_select($item_query, true);

			foreach($order_res as $arr_item) {
				if($arr_item['cancel'] < 1) {//취소접수가 되지 않은 item이 있는 경우 상태변경 전송 제외
					$cancel_item_sno = Array();
					break;
				}

				if($sno != $arr_item['cancel']) {//현재 취소건이 아닌 취소건 중 취소처리되지 않은 주문이 있는 경우 상태변경 전송 제외
					$cancel_query = "SELECT pgcancel FROM ".GD_ORDER_CANCEL." WHERE sno='".$arr_item['cancel']."'";
					$cancel_res = $db->fetch($cancel_query, true);
					if($cancel_res['pgcancel'] === 'n') {
						$cancel_item_sno = Array();
						break;
					}
				}

				$cancel_item_sno[] = $arr_item['sno'];//간편결제 주문취소인 경우 모든 item의 sno를 반환한다
			}
		}
		else {// 간편구매일때 취소하는 item별로 취소되었는지 체크
			// 페이코 주문상태변경 (취소)
			foreach($item_data as $arr_item) {
				if(!$arr_item['delivery']) {
					//item의 모든 수량이 취소되는지 확인
					$item_query = "SELECT goodsno, opt1, opt2, addopt FROM ".GD_ORDER_ITEM." WHERE sno='".$arr_item['sno']."'";
					$order_res = $db->fetch($item_query, true);

					$cancel_query = 'SELECT sum(ea) as ea from '.GD_ORDER_ITEM.' i LEFT JOIN '.GD_ORDER_CANCEL.' c ON i.cancel=c.sno WHERE i.ordno='.$ordno.' AND i.goodsno='.$order_res['goodsno'].' AND i.opt1="'.$order_res['opt1'].'" AND i.opt2="'.$order_res['opt2'].'" AND i.addopt="'.$order_res['addopt'].'" AND i.istep<42 AND NOT cancel=0 AND (c.pgcancel="n" OR c.pgcancel="p")';
					$cancel_res = $db->fetch($cancel_query);

					$none_cancel_query = 'SELECT sum(ea) as ea from '.GD_ORDER_ITEM.' WHERE sno='.$arr_item['sno'].' AND goodsno='.$order_res['goodsno'].' AND opt1="'.$order_res['opt1'].'" AND opt2="'.$order_res['opt2'].'" AND addopt="'.$order_res['addopt'].'" AND cancel=0';
					$none_cancel_res = $db->fetch($none_cancel_query);
					$ea = $cancel_res['ea'] + $none_cancel_res['ea'];

					// item의 모든 수량이 취소되는 경우에만 취소상태로 변경함
					if($arr_item['ea'] == $ea) {
						$cancel_item_sno[] = $arr_item['sno'];
					}
				}
			}
		}

		return $cancel_item_sno;
	}

	/**
	 * 페이코 쿠폰금액
	 * @param  array
	 * @return int
	 * @date 2015-06-09
	 */
	function getPaycoCouponAmount($data)
	{
		$paycoCouponAmount = 0;
		if($data['payco_coupon_use_yn'] == 'Y' && $data['payco_coupon_repay']){
			$paycoCouponAmount = $data['payco_coupon_price'];
		}

		return $paycoCouponAmount;
	}
}
?>