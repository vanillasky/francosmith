<?php

class NaverCheckoutMobile
{
	var $checkoutCfg, $banWord, $db;
	var $msg = '';

	function NaverCheckoutMobile()
	{
		@include dirname(__FILE__).'/../conf/naverCheckout.cfg.php';
		@include dirname(__FILE__).'/../conf/naverCheckout.banWords.php';

		$this->checkoutCfg = $checkoutCfg;
		$this->banWord = $checkoutBan;
		$this->db = &load_class('db', 'db', dirname(__FILE__).'/../conf/db.conf.php');

		$this->checkoutCfg['mobileImgType'] = isset($this->checkoutCfg['mobileImgType']) ? $this->checkoutCfg['mobileImgType'] : 'MA';
		$this->checkoutCfg['mobileImgColor'] = isset($this->checkoutCfg['mobileImgColor']) ? $this->checkoutCfg['mobileImgColor'] : '1';
	}

	/*
	 * @name isAvailable
	 * @param Array memberStatus
	 * @return boolean
	 * @descriptoin 접속자의 체크아웃서비스 사용가능여부를 반환
	 */
	function isAvailable()
	{
		$memberStatus = isset($_SESSION['sess'])?$_SESSION['sess']:null;
		if($this->checkoutCfg['useYn']!='y') return false;
		if($this->checkoutCfg['testYn']=='y' && $memberStatus['level']<=79) return false;
		elseif($this->checkoutCfg['testYn']=='y' && $memberStatus['level']>=80) return true;
		if($this->checkoutCfg['ncMemberYn']=='n' && $memberStatus['m_id']) return false; // !(체크아웃 부가서비스 이용) AND (Login)
		return true;
	}

	/*
	 * @name checkGoods
	 * @param int goodsno, String goodsnm
	 * @return boolean { true:판매가능, false:판매불가 }
	 * @description 전달받은 상품번호와 상품명을 통하여 판매가능여부를 반환
	 */
	function checkGoods($goodsno, $goodsnm)
	{
		if($this->checkoutCfg['e_exceptions'])
		{
			if(in_array($goodsno, $this->checkoutCfg['e_exceptions'])) return false;
		}

		if($this->checkoutCfg['e_category'])
		{
			$resultSet = $this->db->query("SELECT `category` FROM `".GD_GOODS_LINK."` WHERE `goodsno`=".$goodsno." AND `category`");
			while($goodsLink = $this->db->fetch($resultSet, true))
			{
				foreach ($this->checkoutCfg['e_category'] as $e_category) {
					if (preg_match('/^'.$e_category.'/', $goodsLink['category'])) return false;
				}
			}
		}

		foreach((array)$this->banWord as $word)
		{
			if(strlen($word)>0 && preg_match('/'.$word.'/', $goodsnm)) return false;
		}

		$goodsBuyable = getGoodsBuyable($goodsno);
		if($goodsBuyable === "buyable2")
		{
			$this->msg = "회원 전용 구매 상품입니다. 로그인 후 시도해주세요.";
		}
		else if($goodsBuyable === "buyable3") {
			$this->msg = "특정 회원 전용 구매 상품입니다.";
		}
		return true;
	}

	/*
	 * @name checkCart
	 * @param Cart cart
	 * @return boolean { true:판매가능, false:판매불가 }
	 * @description 장바구니안에 상품들 중에 판매가 불가능한 상품이 있는지 체크하여 판매가능여부 확인
	 */
	function checkCart($cart)
	{
		if(count($cart->item)<1) return false;
		foreach($cart->item as $goods)
		{
			if($this->checkGoods($goods['goodsno'], $goods['goodsnm'])===false) return false;
		}
		return true;
	}

	/*
	 * @name getButtonTag
	 * @param Enum type { CART, GOODS_VIEW }   <-- CART일때는 구매하기버튼만 출력되고, GOODS_VIEW일때는 구매하기완 찜하기버튼이 함께 출력 -->
	 * @param boolean enable   <-- true일때는 활성화 false일때는 비활성화 -->
	 * @return String
	 * @description 체크아웃 버튼태그를 반환
	 */
	function getButtonTag($type, $enable)
	{
	    if (class_exists('Services_JSON', false)===false)
		  require dirname(__FILE__).'/json.class.php';

		$json = new Services_JSON();
		$buttonData = array(
			'BUTTON_KEY'         => $this->checkoutCfg['imageId'],  // 체크아웃에서 제공받은 버튼 인증 키 입력
			'TYPE'               => $this->checkoutCfg['mobileImgType'],  // 버튼 모음 종류 설정
			'COLOR'              => $this->checkoutCfg['mobileImgColor'],  // 버튼 모음의 색 설정
			'COUNT'              => ($type==='CART'?'1':'2'), // 장바구니 페이지는 1, 상품 상세 페이지는 2
			'ENABLE'             => $enable?'Y':'N'  // 버튼 활성화는"Y", 비활성화는"N"
		);
		return '
		<section id="checkout_area">
		<script type="text/javascript" charset="UTF-8" src="http://'.($this->checkoutCfg['testYn']=='y'?'test-':'').'checkout.naver.com/customer/js/mobile/checkoutButton.js"></script>
		<script type="text/javascript">
		(function(){
			var buttonTarget = "'.($this->checkoutCfg['mobileButtonTarget']=='new'?'new':'self').'";
			var targetWindow = '.($type==='MULTI_GOODS_VIEW' ? 'parent.window' : 'window').';
			var checkoutParam = '.$json->encode($buttonData).';
			var checkFormCheckout = function(form)
			{
				var _chk = false;

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
					if (targetWindow.m2CheckForm2) {
						return targetWindow.m2CheckForm2("goodsorder-hide");
					}
					else if (targetWindow.m2CheckForm) {
						return targetWindow.m2CheckForm("goodsorder-hide");
					}
					else {
						var res = targetWindow.chkForm(form);
						// 입력옵션 필드값 설정
						if (res) {
							var addopt_inputable = targetWindow.document.getElementsByName("addopt_inputable[]");
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
									targetWindow.document.getElementsByName("_addopt_inputable[]")[i].value = v;
								}
							}
							return true;
						}
						else {
							return false;
						}
					}
				}
				return true;
			};
			checkoutParam["BUY_BUTTON_HANDLER"] = function()
			{
				if("'.$this->msg.'"!=""){
					alert("'.$this->msg.'");
					return;
				}
				var form = targetWindow.document.frmView;
				if(checkoutParam.ENABLE==="Y")
				{
					if(checkoutParam.COUNT==2)
					{
						if(checkFormCheckout(form))
						{
							var oriAction = form.action, oriMode = form.mode.value, oriTarget = form.target;
							if(buttonTarget=="new")
							{
								targetWindow.open("","naverCheckoutPurchase","");
								form.target = "naverCheckoutPurchase";
							}
							else
							{
								form.target = "_self";
							}
							form.action = "/shop/goods/naverCheckout.php?isMobile=true";
							form.mode.value = "buy";
							form.submit();
							form.action = oriAction;
							form.mode.value = oriMode;
							form.target = oriTarget;
						}
					}
					else
					{
						var param = new Array(), idxs = document.getElementsByName("idxs[]");
						for (var i=0,m=idxs.length;i<m;i++) {
							if (idxs[i].checked == true) param += "&idxs[]="+idxs[i].value;
						}
						if(buttonTarget=="new")
						{
							var naverCheckoutWin = window.open("/shop/goods/naverCheckout.php?mode=cart&isMobile=true"+param,"naverCheckoutPurchase");
							location.reload();
						}
						else
						{
							location.href = "/shop/goods/naverCheckout.php?mode=cart&isMobile=true"+param;
						}
					}
				}
				else
				{
					alert("품절 등의 사유로 인하여 구매를 할 수 없습니다.");
					return false;
				}
			};
			if(checkoutParam.COUNT==2)
			{
				checkoutParam["WISHLIST_BUTTON_HANDLER"] = function()
				{
					if("'.$this->msg.'"!=""){
						alert("'.$this->msg.'");
						return;
					}
					if(checkoutParam.ENABLE==="Y")
					{
						var form = targetWindow.document.frmView;
						var oriAction = form.action, oriMode = form.mode.value, oriTarget = form.target;
						if(buttonTarget=="new")
						{
							targetWindow.naverCheckoutWin = targetWindow.open("","naverCheckoutWish","width=100,height=100,scrollbars=0");
							form.target = "naverCheckoutWish";
						}
						else
						{
							form.target = "_self";
						}
						form.action = "/shop/goods/naverCheckout_wish.php?isMobile=true";
						form.mode.value="wish";
						form.submit();
						form.action = oriAction;
						form.mode.value = oriMode;
						form.target = oriTarget;
					}
					else
					{
						alert("품절 등의 사유로 인하여 찜하기를 할 수 없습니다.");
						return false;
					}
				};
			}
			nhn.CheckoutButton.apply(checkoutParam);
		})();
		if(window.jQuery)
		{
			$(document).ready(function(){
				var goodsorderSection = $("section#goodsorder-hide");
				if(goodsorderSection)
				{
					goodsorderSection.css({
						"height" : $("section#goodsorder-hide").height()+20,
						"padding" : "0% 1% 0% 1%"
					});
					$("section#goodsorder-hide .pop_back").css({
						"width" : "98%"
					});
					$("section#goodsorder-hide .pop_back .pop_effect .pop_body #checkout_area").css({
						"margin" : "15px -15px 15px -15px",
						"z-index" : "100"
					});
				}
			});
		}
		</script>
		</section>
		';
	}
}

?>