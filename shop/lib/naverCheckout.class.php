<?php
class NaverCheckout {
	var $checkoutCfg;
	var $imgTags;
	var $fpath;

	function NaverCheckout()
	{
		global $checkoutCfg;
		$this->fpath = dirname(__FILE__);
		if(!$checkoutCfg):
			if(file_exists($fpath."../conf/naverCheckout.cfg.php"))
				require $fpath."../conf/naverCheckout.cfg.php";
		endif;
		if(!$checkoutBan):
			if(file_exists($fpath."../conf/naverCheckout.banWords.php"))
				require $fpath."../conf/naverCheckout.banWords.php";
		endif;
		$this->checkoutCfg = $checkoutCfg;
		$this->banWords = $checkoutBan;
	}

	function get_imgTags($mode,$active,$msg=''){

		$imgTags = "<script type=\"text/javascript\" src=\"http://checkout.naver.com/customer/js/checkoutButton2.js\" charset=\"UTF-8\"></script>";
		//$imgTags = "<script type=\"text/javascript\" src=\"http://alpha-checkout.naver.com/customer/js/checkoutButton.js\" charset=\"UTF-8\"></script>";

		if($mode == 2){
			if($active == 'Y'){
				$imgTags .= '
				<script type="text/javascript">
				function naverCheckout(){
					var f = document.frmView;

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

					}

					f.action = "naverCheckout.php";
					f.mode.value="buy";
					f.target = "ifrmHidden";

					if (_chk || (typeof chkGoodsForm == "undefined" ? chkForm(f) : chkGoodsForm(f))) {
						f.submit();
					}

					f.mode.value="addItem";
					f.target = "";
					f.action = "";
				}
				var naverCheckoutWin="";
				function wishCheckout(){
					var f = document.frmView;
					naverCheckoutWin = window.open("","naverCheckoutWish","width=100,height=100,scrollbars=0");
					f.action = "naverCheckout_wish.php";
					f.mode.value="wish";
					f.target = "ifrmHidden";
					f.submit();
					f.mode.value="addItem";
					f.target = "";
					f.action = "";
				}
				</script>';
			}else{
				$imgTags .= '
				<script type="text/javascript">
				function naverCheckout(){
					alert("'.$msg.'");
				}
				function wishCheckout(){
					alert("'.$msg.'");
				}
				</script>
				';
			}
		}else{
			$imgTags .= '
			<script type="text/javascript">
			function naverCheckout(){
				var idxs = document.getElementsByName("idxs[]");
				var param = "";
				for (var i=0,m=idxs.length;i<m;i++) {
					if (idxs[i].checked == true) param += "&idxs[]="+idxs[i].value;
				}
				ifrmHidden.location.href="naverCheckout.php?mode=cart"+param;
			}
			</script>';
		}
		$imgTags .= '
		<script type="text/javascript" >//<![CDATA[
			nhn.CheckoutButton.apply({
		        BUTTON_KEY: "'.$this->checkoutCfg['imageId'].'", // 체크아웃에서 제공받은 버튼 인증 키 입력
		        TYPE: "'.$this->checkoutCfg['imgType'].'", // 버튼 모음 종류 설정
		        COLOR: '.$this->checkoutCfg['imgColor'].', // 버튼 모음의 색 설정
		        COUNT: '.$mode.', // 버튼 개수 설정. 구매하기 버튼만 있으면(장바구니 페이지) 1, 찜하기 버튼도 있으면(상품 상세 페이지) 2를 입력.
		        ENABLE: "'.$active.'", // 품절 등의 이유로 버튼 모음을 비활성화할 때에는 "N" 입력
		        BUY_BUTTON_HANDLER: naverCheckout,';
		if($mode == 2){
			$imgTags .= '
        		WISHLIST_BUTTON_HANDLER: wishCheckout,';
		}

		$imgTags .= '
		        "":""
			});
		//]]></script>';
		return $imgTags;
	}

	function check_exceptions($goodsno)
	{
		$db = & $GLOBALS['db'];
		if($this->checkoutCfg['e_exceptions'] && in_array($goodsno,$this->checkoutCfg['e_exceptions']) ) return false;
		$res = $db->query("select category from ".GD_GOODS_LINK." where goodsno='$goodsno' and category");
		while($data = $db->fetch($res)){
			for($i=3;$i<=strlen($data['category']);$i=$i+3)
			{
				$category = substr($data['category'],0,$i);
				if($this->checkoutCfg['e_category'] && in_array($category,$this->checkoutCfg['e_category']) ) return false;
			}
		}
		return true;
	}

	function check_use(){
		global $sess;
		if($this->checkoutCfg['useYn'] != 'y') return false;
		if($this->checkoutCfg['testYn'] == 'y' && $sess['level']<=79) return false;
		elseif($this->checkoutCfg['testYn'] == 'y' && $sess['level']>=80) return true;
		if($this->checkoutCfg['ncMemberYn'] == "n" && $sess['m_id']) return false; // !(체크아웃 부가서비스 이용) AND (Login)
		return true;
	}

	function get_GoodsViewTag($goodsno,$goodsnm,$on=true,$msg='')
	{
		if(!$this->check_use())	return false;
		if(!$this->check_exceptions($goodsno)){
			$on = false;
			$msg = "죄송합니다. 네이버 체크아웃으로 구매가 불가한 상품입니다.";
		}
		if(!$this->check_banWords($goodsnm)){
			$on = false;
			$msg = "죄송합니다. 네이버 체크아웃으로 구매가 불가한 상품입니다.";
		}

		$goodsBuyable = getGoodsBuyable($goodsno);
		if($goodsBuyable === "buyable2")
		{
			$on = false;
			$msg = "회원 전용 구매 상품입니다. 로그인 후 시도해주세요.";
		}
		else if($goodsBuyable === "buyable3") {
			$on = false;
			$msg = "특정 회원 전용 구매 상품입니다.";
		}

		$script  = '';
		$script .= $this->get_cts_script();
		$script .= $on ? $this->get_imgTags(2,'Y') : $this->get_imgTags(2,'N',$msg);

		return $script;

	}

	function get_GoodsCartTag($item)
	{
		if(!$this->check_use()) return false;
		if(!$item) return false;
		$on = true;
		if($item)foreach($item as $goods)
		{
			if(!$this->check_exceptions($goods['goodsno'])) $on = false;
			if(!$this->check_banWords($goods['goodsnm'])) $on = false;
		}

		$script  = '';
		$script .= $this->get_cts_script();
		$script .= $on ? $this->get_imgTags(1,'Y') : $this->get_imgTags(1,'N');

		return $script;
	}

	function check_banWords($goodsnm)
	{
		if($this->banWords && $goodsnm)foreach($this->banWords as $word)
		{
			if(preg_match('/'.$word.'/',$goodsnm) && $word) return false;
		}
		return true;
	}

	function get_oneclickJoin($arrData)
	{
		$resData = array('mode'=>'');
		if ($arrData['join_inflow'] != 'NCOneClick' && (isset($arrData['NCUserNo']) === true || isset($_SESSION['NCOneClickInfo']) === true)) { // 이용약관
			// 네이버에서 전달 받아온 값을 session에 저장
			if (isset($arrData['NCUserNo']) === true && isset($arrData['NCUserName']) === true && isset($arrData['NCUserSSN']) === true) {
				session_register("NCOneClickInfo");
				$_SESSION['NCOneClickInfo']['NCUserNo']			= trim($arrData['NCUserNo']);			// 네이버 회원 고유 번호
				$_SESSION['NCOneClickInfo']['NCUserName']		= trim($arrData['NCUserName']);		// 네이버 회원 이름
				$_SESSION['NCOneClickInfo']['NCUserSSN']		= trim($arrData['NCUserSSN']);		// 네이버 회원 주민등록번호(13자리)
				$_SESSION['NCOneClickInfo']['NCUserPhoneNo']	= trim($arrData['NCUserPhoneNo']);	// 네이버 휴대폰 번호(숫자만)
				$_SESSION['NCOneClickInfo']['NCUserEmail']		= trim($arrData['NCUserEmail']);		// 네이버 메일 주소
				$_SESSION['NCOneClickInfo']['DeliveryZipCode']	= trim($arrData['DeliveryZipCode']);	// 네이버 회원 배송지 우편번호(숫자만)
				$_SESSION['NCOneClickInfo']['DeliveryAddress1']	= trim($arrData['DeliveryAddress1']);	// 네이버 회원 배송지 주소 1
				$_SESSION['NCOneClickInfo']['DeliveryAddress2']	= trim($arrData['DeliveryAddress2']);	// 네이버 회원 배송지 주소 2
				$_SESSION['NCOneClickInfo']['RecieverTelNo1']	= trim($arrData['RecieverTelNo1']);	// 네이버 회원 배송지 연락처 1(숫자만)
				$_SESSION['NCOneClickInfo']['RecieverTelNo2']	= trim($arrData['RecieverTelNo2']);	// 네이버 회원 배송지 연락처 2(숫자만)
				$_SESSION['NCOneClickInfo']['RecieverName']		= trim($arrData['RecieverName']);		// 네이버 회원 배송지 수령인 이름
				$_SESSION['NCOneClickInfo']['NCMallID']			= trim($arrData['NCMallID']);			// 네이버 가맹점 아이디
				$_SESSION['NCOneClickInfo']['Timestamp']		= trim($arrData['Timestamp']);		// 서비스 요청 시각
			}

			$resData['mode'] = 'agreement';
			$resData['stepHtml'] = '
			<input type="hidden" name="join_inflow" value="NCOneClick">
			<input type="hidden" name="NCUserNo" value="'.$_SESSION['NCOneClickInfo']['NCUserNo'].'">
			<script language="javascript">
			document.frmAgree.action = "";
			document.frmAgree.target = "";
			</script>
			<link href="../proc/naver_material/css/checkout_store_join.css" type="text/css" rel="STYLESHEET"/>
			<!---- nbp 제공 공통 UI 삽입 ---->
			<div class="chk_malljoin" style="width:100%; margin-bottom:15px;">
				<div class="line"></div>
				<div class="malltitle_section">
					<div class="mall_sub">
					<div class="logo">
						<a href="http://www.naver.com" target="_blank" class="l_naver"><span class="blind"></span></a><a href="http://checkout.naver.com" target="_blank" class="l_checkout"><span class="blind"></span></a>
					</div>
					<h1 class="blind">네이버 체크아웃 원클릭 쇼핑몰 가입 서비스</h1>
					<p class="blind">체크아웃 회원은 가맹점 쇼핑몰에 편리하고 빠르게 회원 가입 할 수 있습니다!</p>
					</div>
					<div class="img_cart">
						<img src="../proc/naver_material/img/store/bg_malltitle2.gif" width="150" height="161" alt="">
					</div>
				</div>
				<ol class="join_list">
				<li><img src="../proc/naver_material/img/store/text_joinlist.gif" width="121" height="15" alt="01. 개인정보 제공 동의"></li>
				<li class="on">
					<img src="../proc/naver_material/img/store/text_joinlist2_on.gif" width="135" height="15" alt="02. 쇼핑몰 회원가입 신청">
					<div class="bg_left"></div>
					<div class="bg_right"></div>
				</li>
				<li class="end"><img src="../proc/naver_material/img/store/text_joinlist3.gif" width="134" height="15" alt="03. 쇼핑몰 회원가입 완료"></li>
				</ol>
			</div>
			<!---- nbp 제공 공통 UI 삽입 ---->
			';
		} else if ($arrData['join_inflow'] == 'NCOneClick' && isset($_SESSION['NCOneClickInfo']) === true && $_SESSION['NCOneClickInfo']['NCUserNo'] == $arrData['NCUserNo']) { // 가입폼작성
			$resData['mode'] = 'form';
			$resData['stepHtml'] = '
			<input type="hidden" name="join_inflow" value="NCOneClick">
			<input type="hidden" name="NCUserNo" value="'.$_SESSION['NCOneClickInfo']['NCUserNo'].'">
			<link href="../proc/naver_material/css/checkout_store_join.css" type="text/css" rel="STYLESHEET"/>
			<!---- nbp 제공 공통 UI 삽입 ---->
			<div class="chk_malljoin" style="width:100%; margin-bottom:15px;">
				<div class="line"></div>
				<div class="malltitle_section">
					<div class="mall_sub">
					<div class="logo">
						<a href="http://www.naver.com" target="_blank" class="l_naver"><span class="blind"></span></a><a href="http://checkout.naver.com" target="_blank" class="l_checkout"><span class="blind"></span></a>
					</div>
					<h1 class="blind">네이버 체크아웃 원클릭 쇼핑몰 가입 서비스</h1>
					<p class="blind">체크아웃 회원은 가맹점 쇼핑몰에 편리하고 빠르게 회원 가입 할 수 있습니다!</p>
					</div>
					<div class="img_cart">
						<img src="../proc/naver_material/img/store/bg_malltitle2.gif" width="150" height="161" alt="">
					</div>
				</div>
				<ol class="join_list">
				<li><img src="../proc/naver_material/img/store/text_joinlist.gif" width="121" height="15" alt="01. 개인정보 제공 동의"></li>
				<li class="on">
					<img src="../proc/naver_material/img/store/text_joinlist2_on.gif" width="135" height="15" alt="02. 쇼핑몰 회원가입 신청">
					<div class="bg_left"></div>
					<div class="bg_right"></div>
				</li>
				<li class="end"><img src="../proc/naver_material/img/store/text_joinlist3.gif" width="134" height="15" alt="03. 쇼핑몰 회원가입 완료"></li>
				</ol>
			</div>
			<!---- nbp 제공 공통 UI 삽입 ---->
			';

			// 데이터 정의
			$data = array(
				'name' => $_SESSION['NCOneClickInfo']['NCUserName'],				// 네이버 회원 이름
				'resno' => $_SESSION['NCOneClickInfo']['NCUserSSN'],				// 네이버 회원 주민등록번호(13자리)
				'email' => $_SESSION['NCOneClickInfo']['NCUserEmail'],				// 네이버 메일 주소
				'zipcode' => $_SESSION['NCOneClickInfo']['DeliveryZipCode'],		// 네이버 회원 배송지 우편번호(숫자만)
				'address' => $_SESSION['NCOneClickInfo']['DeliveryAddress1'],		// 네이버 회원 배송지 주소 1
				'address_sub' => $_SESSION['NCOneClickInfo']['DeliveryAddress2'],	// 네이버 회원 배송지 주소 2
				'phone' => $_SESSION['NCOneClickInfo']['RecieverTelNo1'],			// 네이버 회원 배송지 연락처 1(숫자만)
				'mobile' => $_SESSION['NCOneClickInfo']['NCUserPhoneNo'],			// 네이버 휴대폰 번호(숫자만)
			);

			// 데이터 복호화
			$naverCheckoutAPI = Core::loader('naverCheckoutAPI');
			foreach ($data as $k => $v) {
				if ($v != '') {
					$temp_ar = explode('|||', $naverCheckoutAPI->ncCrypt('decrypt',$v,$_SESSION['NCOneClickInfo']['Timestamp']));
					if($temp_ar[0] == 'ERRO') $data[$k] = '';
					else $data[$k] = $temp_ar[1];
				}
			}

			// 데이터 chracset변환(UTF-8 -> EUC-KR)
			foreach ($data as $k => $v) {
				$data[$k] = iconv('UTF-8', 'EUC-KR', $v);
			}

			// 데이터 대입
			$resData['data'] = $data;
			$resData['data']['resno'] = array(substr($data['resno'], 0, 6), substr($data['resno'], 6, 7));
			$dy = (substr($data['resno'], 6, 1) == 1 || substr($data['resno'], 6, 1) == 2 ? '19' : '20');
			$resData['data']['birth_year'] = $dy.substr($data['resno'], 0, 2);
			$resData['data']['birth'] = array(substr($data['resno'], 2, 2), substr($data['resno'], 4, 2));
			$resData['data']['zipcode'] = array(substr($data['zipcode'], 0, 3), substr($data['zipcode'], 3, 3));
			if (substr($data['phone'], 0, 2) == '02') {
				$resData['data']['phone'] = array(substr($data['phone'], 0, 2), substr($data['phone'], 2, -4), substr($data['phone'], -4));
			} else {
				$resData['data']['phone'] = array(substr($data['phone'], 0, 3), substr($data['phone'], 3, -4), substr($data['phone'], -4));
			}
			$resData['data']['mobile'] = array(substr($data['mobile'], 0, 3), substr($data['mobile'], 3, 4), substr($data['mobile'], 7));
		}
		return $resData;
	}

	function get_oneclickJoinOk($mode)
	{
		$resData = array('mode'=>'');
		if ($mode == 'nc') {
			$resData['mode'] = 'ok';
			$resData['stepHtml'] = '
			<link href="../proc/naver_material/css/checkout_store_join.css" type="text/css" rel="STYLESHEET"/>
			<!---- nbp 제공 공통 UI 삽입 ---->
			<div class="chk_malljoin" style="width:100%; margin-bottom:15px;">
				<div class="line"></div>
				<div class="malltitle_section">
					<div class="mall_sub">
					<div class="logo">
						<a href="http://www.naver.com" target="_blank" class="l_naver"><span class="blind"></span></a><a href="http://checkout.naver.com" target="_blank" class="l_checkout"><span class="blind"></span></a>
					</div>
					<h1 class="blind">네이버 체크아웃 원클릭 쇼핑몰 가입 서비스</h1>
					<p class="blind">체크아웃 회원은 가맹점 쇼핑몰에 편리하고 빠르게 회원 가입 할 수 있습니다!</p>
					</div>
					<div class="img_cart">
						<img src="../proc/naver_material/img/store/bg_malltitle2.gif" width="150" height="161" alt="">
					</div>
				</div>
				<ol class="join_list">
				<li><img src="../proc/naver_material/img/store/text_joinlist.gif" width="121" height="15" alt="01. 개인정보 제공 동의"></li>
				<li>
					<img src="../proc/naver_material/img/store/text_joinlist2.gif" width="133" height="15"  alt="02. 쇼핑몰 회원가입 신청">
					<div class="bg_left"></div>
				</li>
				<li class="on end">
					<img src="../proc/naver_material/img/store/text_joinlist3_on.gif" width="136" height="15" alt="03. 쇼핑몰 회원가입 완료">
					<div class="bg_left"></div>
				</li>
				</ol>
				<div class="clause_section">
					<div class="join_end">
						<dl class="blind">
						<dt>원클릭 쇼핑몰 가입이 완료 되었습니다</dt>
						<dd>체크아웃 원클릭 쇼핑몰 가입 내역은 체크아웃 홈 > 가맹점 리스트에서확인 하실 수 있습니다.
						체크아웃 쇼핑몰 회원 구매 서비스를 이용하시면 체크아웃 혜택뿐만 아니라 가맹점의 혜택도 받으실 수 있습니다.</dd>
						</dl>
						<a href="http://checkout.naver.com/customer/mall.nhn" target="_blank"><span class="blind">체크아웃 홈&gt;가맹점 리스트</span></a>
					</div>
					<div class="btn_right">
						<a href="http://checkout.naver.com" target="_blank"><img src="../proc/naver_material/img/store/btn_chkhome.gif" width="67" height="12" alt="체크아웃 홈"></a>
					</div>
				</div>
			</div>
			<!---- //nbp 제공 공통 UI 삽입 ---->
			';
		}
		return $resData;
	}

	function get_cts_script() {

		$script = '
		<script type="text/javascript">
		if (typeof _TGV == \'undefined\') {
			function _TGV(b,a,c,d){ var f = b.split(c);for(var i=0;i<f.length; i++){ if( _TIX(f[i],(a+d))==0) return f[i].substring(_TIX(f[i],(a+d))+(a.length+d.length),f[i].length); }	return \'\'; };
		}
		try {
		var _CKNVADID = _TGV(unescape(_TGV(document.cookie,\'CTSCKURL\',\'; \',\'=\')),\'NVADID\',\'&\',\'=\');
		document.cookie = "NVADID=" + _CKNVADID +"; path=/; domain="+document.domain+"; ";
		}
		catch (e) { }
		</script>
		';

		return $script;
	}
}
?>
