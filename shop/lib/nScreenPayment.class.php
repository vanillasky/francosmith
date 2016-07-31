<?php
class nScreenPayment
{
	private $_mobile_agents = array(
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

	private $_screen = 'PC';
	private $_pg_config;
	private $_pg_mobile_config;
	private $_settle_pg = '';
	private $_settleprice;
	private $_tax;
	private $_vat;
	private $_taxfree;

	public function __construct()
	{
		$this->getScreenType();

		$this->_setPgConfig();

	}

	public function executeCardGate($settleprice, $settlekind, $is_mobile=false, $tax=0, $vat=0, $taxfree=0)
	{
		global $config;
		$cfg = $config->load('config');

		$this->_settleprice = $settleprice;
		$this->_tax = $tax;
		$this->_vat = $vat;
		$this->_taxfree = $taxfree;

		$mobilians = Core::loader('Mobilians');
		$danal = Core::loader('Danal');
		
		if ($settlekind == 'i')
		{
			$this->_settle_pg = 'ipay';
		}
		else if ($settlekind == 'h' && $cfg['settleCellPg'] === 'mobilians' && $mobilians->isEnabled()) {
			$this->_settle_pg = 'mobilians';
		}
		else if ($settlekind == 'h' && $cfg['settleCellPg'] === 'danal' && $danal->isEnabled()) {
			$this->_settle_pg = 'danal';
		}
		else if ($settlekind == 't'){
			$this->_settle_pg = 'payco';
		}

		if($is_mobile) {
			$this->executeCardGateByPgMobile($settlekind);
		}
		else {
			if($this->_screen == 'MOBILE') {
				if($this->_settle_pg == 'mobilians') {
					$this->executeCardGateByPg();
				}
				else {
					$this->executeCardGateByPgMobile($settlekind, $this->_screen);
				}
			}
			else {
				$this->executeCardGateByPg($settlekind);
			}
		}
	}

	public function executeCardGateByPg($settlekind)
	{
		global $config;
		$cfg = $config->load('config');

		switch($this->_settle_pg)
		{
			case "allat":
				echo "<script>
					if(parent.document.getElementsByName('allat_amt')[0].value == '".$this->_settleprice."'){
						parent.ftn_app();
					}else{
						alert('결제금액이 올바르지 않습니다.');
						parent.location.replace('order.php');
					}
					</script>";
				exit;
				break;
			case "allatbasic":
				echo "<script>
					if(parent.document.getElementsByName('allat_amt')[0].value == '".$this->_settleprice."'){
						parent.ftn_approval();
					}else{
					alert('결제금액이 올바르지 않습니다.');
					parent.location.replace('order.php');
					}
					</script>";
				exit;
				break;
			case "inipay":
				// 이니시스 5.0 은 복합과세 시 부가세와 면세를 전달  **주의!! 필드명 tax가 부과세임
				$_SESSION['INI_TAX']		= $this->_vat;		// 부가세
				$_SESSION['INI_TAXFREE']	= $this->_taxfree;	// 면세
				echo "<script>
					if(parent.document.getElementsByName('INISettlePrice')[0].value == '".$this->_settleprice."'){
						var fm=parent.document.ini; if (parent.pay(fm)) fm.submit();
					}else{
						alert('결제금액이 올바르지 않습니다.');
						parent.location.replace('order.php');
					}
					</script>";
				exit;
				break;
			case "inicis":
				// 이니시스 4.1 은 복합과세 시 부가세와 면세를 전달  **주의!! 필드명 tax가 부과세임
				echo "<script>
					if(parent.document.getElementsByName('price')[0].value == '".$this->_settleprice."'){
						if(typeof parent.document.getElementsByName('tax')[0] == 'undefined') {
							var form = parent.document.getElementsByName('ini')[0];
							var taxinput = document.createElement('input');
							taxinput.setAttribute('type', 'hidden');
							taxinput.setAttribute('name', 'tax');
							taxinput.setAttribute('value', '".$this->_vat."');
							form.appendChild(taxinput);
						} else {
							parent.document.getElementsByName('tax')[0].value = '".$this->_vat."';
						}
						if(typeof parent.document.getElementsByName('taxfree')[0] == 'undefined') {
							var form = parent.document.getElementsByName('ini')[0];
							var taxfreeinput = document.createElement('input');
							taxfreeinput.setAttribute('type', 'hidden');
							taxfreeinput.setAttribute('name', 'taxfree');
							taxfreeinput.setAttribute('value', '".$this->_taxfree."');
							form.appendChild(taxfreeinput);
						} else {
							parent.document.getElementsByName('taxfree')[0].value = '".$this->_taxfree."';
						}
						var fm=parent.document.ini; if (parent.pay(fm)) fm.submit();
					}else{
						alert('결제금액이 올바르지 않습니다.');
						parent.location.replace('order.php');
					}
					</script>";
				exit;
				break;
			case "agspay":
				echo "<script>
					if(parent.document.getElementsByName('Amt')[0].value == '".$this->_settleprice."'){
						var fm=parent.document.frmAGS_pay; if (parent.Pay(fm)) parent.Pay(fm);
					}else{
						alert('결제금액이 올바르지 않습니다.');
						parent.location.replace('order.php');
					}
					</script>";
				exit;
				break;
			case "dacom":
				echo "<script>
					if(parent.document.getElementsByName('amount')[0].value == '".$this->_settleprice."'){
						parent.openWindow();
					}else{
						alert('결제금액이 올바르지 않습니다.');
						parent.location.replace('order.php');
					}
					</script>";
				exit;
				break;
			case "lgdacom":
				// 엘지데이콤 은 복합과세 시 면세를 전달
				if ($settlekind == 'u') {
					// cups
					echo "<script>
						if(parent.document.getElementsByName('LGD_AMOUNT')[0].value == '".$this->_settleprice."'){
							if(typeof parent.document.getElementsByName('LGD_TAXFREEAMOUNT')[0] == 'undefined') {
								var form = parent.document.getElementById('LGD_PAYINFO');
								var taxfreeinput = document.createElement('input');
								taxfreeinput.setAttribute('type', 'hidden');
								taxfreeinput.setAttribute('name', 'LGD_TAXFREEAMOUNT');
								taxfreeinput.setAttribute('value', '".$this->_taxfree."');
								form.appendChild(taxfreeinput);
							} else {
								parent.document.getElementsByName('LGD_TAXFREEAMOUNT')[0].value = '".$this->_taxfree."';
							}
							parent.doPay_CUPS();
						}else{
							alert('결제금액이 올바르지 않습니다.');
							parent.location.replace('order.php');
						}
						</script>";
				}
				else {
					// 기존 결제 방식
					echo "<script>
						if(parent.document.getElementsByName('LGD_AMOUNT')[0].value == '".$this->_settleprice."'){
							if(typeof parent.document.getElementsByName('LGD_TAXFREEAMOUNT')[0] == 'undefined') {
								var form = parent.document.getElementById('LGD_PAYINFO');
								var taxfreeinput = document.createElement('input');
								taxfreeinput.setAttribute('type', 'hidden');
								taxfreeinput.setAttribute('name', 'LGD_TAXFREEAMOUNT');
								taxfreeinput.setAttribute('value', '".$this->_taxfree."');
								form.appendChild(taxfreeinput);
							} else {
								parent.document.getElementsByName('LGD_TAXFREEAMOUNT')[0].value = '".$this->_taxfree."';
							}
							parent.doPay_ActiveX();
						}else{
							alert('결제금액이 올바르지 않습니다.');
							parent.location.replace('order.php');
						}
						</script>";
				}
				exit;
				break;
			case "kcp":
				echo "<script>
					if(parent.document.getElementsByName('good_mny')[0].value == '".$this->_settleprice."'){
						var fm=parent.document.order_info; if(parent.jsf__pay(fm))fm.submit();
					}else{
						alert('결제금액이 올바르지 않습니다.');
						parent.location.replace('order.php');
					}
					</script>";
				exit;
				break;
			case "easypay":
				echo "<script>
					if(parent.document.getElementsByName('EP_product_amt')[0].value == '".$this->_settleprice."'){
						var fm=parent.document.frm_pay; if(parent.f_submit(fm))fm.submit();
					}else{
						alert('결제금액이 올바르지 않습니다.');
						parent.location.replace('order.php');
					}
					</script>";
				exit;
				break;
			case "ipay":
				exit("
				<script type='text/javascript'>
				var idxs = parent.document.getElementsByName('idxs[]');
				var param = '';
				for (var i=0,m=idxs.length;i<m;i++) {
					if (idxs[i].checked == true) param += '&idxs[]='+idxs[i].value;
				}

				var f = parent.document.frmSettle;
				f.action = '../goods/auctionIpay.pg.php?ipay_pg=y&mode=cart'+param;
				f.target = 'ifrmHidden';
				f.submit();
				</script>
				");
				break;
			case "settlebank":
				echo "<script>
					if(parent.document.getElementsByName('PAmt')[0].value == '".$this->_settleprice."'){
						parent.submitSettleFormPopup();
					}else{
						alert('결제금액이 올바르지 않습니다.');
						parent.location.replace('order.php');
					}
					</script>";
				exit;
				break;
			case 'mobilians' :
				exit('
				<script type="text/javascript">
				var f = parent.document.frmSettle;
				f.action = "'.$cfg['rootDir'].'/order/card/mobilians/card_gate.php";
				f.target = "ifrmHidden";
				f.submit();
				</script>
				');
				break;
			case 'danal' :
				exit('
				<script type="text/javascript">
				var f = parent.document.frmSettle;
				f.action = "'.$cfg['rootDir'].'/order/card/danal/card_gate.php";
				f.target = "ifrmHidden";
				f.submit();
				</script>
				');
				break;
			case 'payco' :
				if($_POST['paycoType'] == 'CHECKOUT'){
					include '../order/card/payco/card_gate.php';
				}
				else {
					echo '
					<script type="text/javascript">
					var f = parent.document.frmSettle;

					//form 원본 정보
					var oriAction	= f.action;
					var oriTarget	= f.target;

					f.action = "'.$cfg['rootDir'].'/order/card/payco/card_gate.php";
					f.target = "paycoPopup";
					f.submit();

					f.action = oriAction;
					f.target = oriTarget;
					</script>
					';
				}
				exit;

				break;
		}
	}

	public function executeCardGateByPgMobile($settlekind, $screen_type=null)
	{
		global $config;
		$cfg = $config->load('config');

		switch($this->_settle_pg)
		{
			case "allat":
				echo "<script>
					if(parent.document.getElementsByName('allat_amt')[0].value == '".$this->_settleprice."'){
						parent.approval();
					}else{
						alert('결제금액이 올바르지 않습니다.');
						parent.location.replace('order.php');
					}
					</script>";
				exit;
				break;
			case "allatbasic":
				echo "<script>
					if(parent.document.getElementsByName('allat_amt')[0].value == '".$this->_settleprice."'){
						parent.approval();
					}else{
						alert('결제금액이 올바르지 않습니다.');
					parent.location.replace('order.php');
					}
					</script>";
				exit;
				break;
			case "inicis":
				// 이니시스 4.1 은 복합과세 시 부가세와 면세를 전달  **주의!! 필드명 tax가 부과세임
				echo "<script>
					if(parent.document.getElementsByName('P_AMT')[0].value == '".$this->_settleprice."'){
						parent.document.getElementsByName('P_TAX')[0].value = '".$this->_vat."';
						parent.document.getElementsByName('P_TAXFREE')[0].value = '".$this->_taxfree."';
						parent.on_card();
					}else{
						alert('결제금액이 올바르지 않습니다.');
						parent.location.replace('order.php');
					}
					</script>";
				exit;
				break;
			case "inipay":
				// 이니시스 5.0 은 복합과세 시 부가세와 면세를 전달  **주의!! 필드명 tax가 부과세임
				echo "<script>
					if(parent.document.getElementsByName('P_AMT')[0].value == '".$this->_settleprice."'){
						parent.document.getElementsByName('P_TAX')[0].value = '".$this->_vat."';
						parent.document.getElementsByName('P_TAXFREE')[0].value = '".$this->_taxfree."';
						parent.on_card();
					}else{
						alert('결제금액이 올바르지 않습니다.');
						parent.location.replace('order.php');
					}
					</script>";
				exit;
				break;
			case "lgdacom":
				// 엘지데이콤 은 복합과세 시 면세를 전달
				echo "<script>
					if(parent.document.getElementsByName('LGD_AMOUNT')[0].value == '".$this->_settleprice."'){
						parent.document.getElementsByName('LGD_TAXFREEAMOUNT')[0].value = '".$this->_taxfree."';
						parent.launchCrossPlatform();

					}else{
						alert('결제금액이 올바르지 않습니다.');
						parent.location.replace('order.php');
					}
					</script>";
				exit;
				break;
			case "agspay":
				echo "<script>
					if(parent.document.getElementsByName('Amt')[0].value == '".$this->_settleprice."'){
						parent.Pay();
					}else{
						alert('결제금액이 올바르지 않습니다.');
						parent.location.replace('order.php');
					}
					</script>";
				exit;
				break;
			case "easypay":
				echo "<script>
					if(parent.document.getElementsByName('sp_pay_mny')[0].value == '".$this->_settleprice."'){
						parent.f_submit();
					}else{
						alert('결제금액이 올바르지 않습니다.');
						parent.location.replace('order.php');
					}
					</script>";
				exit;
				break;
			case "settlebank":
				echo "<script>
					if(parent.document.getElementsByName('PAmt')[0].value == '".$this->_settleprice."'){
						parent.submitForm();
					}else{
						alert('결제금액이 올바르지 않습니다.');
						parent.location.replace('order.php');
					}
					</script>";
				exit;
				break;
			case 'mobilians' :
				if($screen_type == 'MOBILE') {
					exit('
					<script type="text/javascript">
					var f = parent.document.frmSettle;
					f.action = "'.$cfg['rootDir'].'/order/card/mobilians/card_gate.php";
					f.target = "ifrmHidden";
					f.submit();
					</script>
					');
				}
				else {
					exit('
					<script type="text/javascript">
					var f = parent.document.frmSettle;
					f.action = "'.$cfg['rootDir'].'/order/card/mobilians/card_gate.php?pc=true&isMobile=true";
					f.target = "ifrmHidden";
					f.submit();
					</script>
					');
				}
				break;
			case 'danal' :
				exit('
				<script type="text/javascript">
				var f = parent.document.frmSettle;
				f.action = "'.$cfg['rootDir'].'/order/card/danal/card_gate.php?pc=true&isMobile=true";
				f.target = "ifrmHidden";
				f.submit();
				</script>
				');
				break;
			case "kcp":
				echo "<script>
					if(parent.document.getElementsByName('good_mny')[0].value == '".$this->_settleprice."'){
						parent.kcp_AJAX();
					}else{
						alert('결제금액이 올바르지 않습니다.');
						parent.location.replace('order.php');
					}
					</script>";
				exit;
				break;
			case 'payco' :
				if($_POST['paycoType'] == 'CHECKOUT'){
					include '../order/card/payco/card_gate.php';
				}
				else {
					echo '
					<script type="text/javascript">
					var f = parent.document.frmSettle;

					//form 원본 정보
					var oriAction	= f.action;
					var oriTarget	= f.target;

					f.action = "'.$cfg['rootDir'].'/order/card/payco/card_gate.php";
					f.target = "ifrmHidden";
					f.submit();

					f.action = oriAction;
					f.target = oriTarget;
					</script>
					';
				}
				exit;

				break;
		}
	}


	//card_gate 페이지 가져오기(디바이스 별 분기)
	public function getCardGate($tpl, $cart, $is_mobile=false)
	{
		global $config;
		$cfg = $config->load('config');
		if($is_mobile === true) {

			if ((is_array($this->_pg_mobile_config) && !empty($this->_pg_mobile_config))&& isset($this->_pg_config['id']) && strlen($this->_pg_config['id']) > 0) {
				ob_start();
				include (SHOPROOT.'/order/card/'.$this->_settle_pg.'/mobile/card_gate.php');
				$card_gate = ob_get_contents();
				ob_end_clean();
				$tpl->assign('card_gate',$card_gate);
			}
		}
		else {

			@include(SHOPROOT.'/conf/config.nscreenPayment.php');

			if($this->_screen == 'MOBILE' && $config_nscreen_payment['use']) {

				//nscreen_settle.htm 파일 체크하여 파일이 없을 경우 생성한다.
				$key_file = 'order/nscreen_settle.htm';

				$key_file_path = SHOPROOT.'/data/skin/'.$cfg['tplSkin'].'/'.$key_file;

				// 스킨에 order/nscreen_settle.htm 이 없거나, pg가 dacom 일 경우 nscreen 결제 하지 않음
				if(!file_exists($key_file_path) || $this->_settle_pg=='dacom' ) {

					include (SHOPROOT.'/order/card/'.$this->_settle_pg.'/card_gate.php');
					$tpl->assign('pg',$this->_pg_config);
					$tpl->define('card_gate','order/card/'.$this->_settle_pg.'.htm');

				}
				else {
					$tpl->define( array(
						'tpl'			=> $key_file,
					) );

					if (isset($this->_pg_config['id']) && strlen($this->_pg_config['id']) > 0) {
						ob_start();
						include (SHOPROOT.'/order/card/'.$this->_settle_pg.'/mobile/nscreen_card_gate.php');
						$card_gate = ob_get_contents();
						ob_end_clean();

						$tpl->assign('card_gate',$card_gate);
					}
				}
			}
			else {

				include (SHOPROOT.'/order/card/'.$this->_settle_pg.'/card_gate.php');
				$tpl->assign('pg',$this->_pg_config);
				$tpl->define('card_gate','order/card/'.$this->_settle_pg.'.htm');
			}
		}
	}

	public function getScreenType()
	{
		foreach($this->_mobile_agents as $mobile_agent) {

			if(strstr(strtolower($_SERVER['HTTP_USER_AGENT']), strtolower($mobile_agent))) {
				$this->_screen = 'MOBILE';
				break;
			}
		}
		return $this->_screen;
	}

	private function _setPgConfig()
	{
		global $config;
		$cfg = $config->load('config');

		$this->_settle_pg = $cfg['settlePg'];

		@include(SHOPROOT.'/conf/pg.'.$this->_settle_pg.'.php');
		@include(SHOPROOT.'/conf/pg_mobile.'.$this->_settle_pg.'.php');

		$this->_pg_config = $pg;
		$this->_pg_mobile_config = $pg_mobile;
	}
}
