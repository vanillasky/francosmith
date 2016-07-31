// 파라미터 Parsing
var GET = (function(){
	var
	scriptParam = new Object(),
	scriptTag = document.getElementById("naver-mileage-script"),
	queryIndex = scriptTag.src.indexOf("?");
	if (queryIndex > -1) {
		var scriptTagParam = scriptTag.src.substr(scriptTag.src.indexOf("?")+1).split(/&/gi);
		for (var a = 0; a<scriptTagParam.length; a++) {
			var
			splitIndex = scriptTagParam[a].indexOf("="),
			paramName = (splitIndex<0)?scriptTagParam[a]:scriptTagParam[a].substr(0, splitIndex),
			paramValue = (splitIndex<0)?null:scriptTagParam[a].substr(splitIndex+1);
			if (paramName.match(/(.+)\[(.*)\]$/)) {
				if (RegExp.$2 && RegExp.$2.trim().length > 0) {
					if (scriptParam[RegExp.$1] === undefined) scriptParam[RegExp.$1] = new Object();
					scriptParam[RegExp.$1][RegExp.$2] = paramValue;
				}
				else {
					if (scriptParam[RegExp.$1] === undefined) scriptParam[RegExp.$1] = new Array(paramValue);
					else scriptParam[RegExp.$1].push(paramValue);
				}
			}
			else {
				scriptParam[paramName] = paramValue;
			}
		}
	}
	return scriptParam;
})();

var NaverMileage = new function()
{

	var self = this;

	self.initExecCount = 0;
	self.elementRegisted = false;
	self.moduleLoaded = false;
	self.maxUseAmount = 0;
	self.enabled = undefined;

	/*
	 * @name registElement
	 * @return void
	 * @description 페이지 로드 이후 네이버 마일리지 관련 엘리먼트를 메모리에 적재
	 */
	self.registElement = function()
	{
		self.reqTxid = document.getElementById("reqTxId"+GET.ApiId);
		self.mileageUseAmount = document.getElementById("mileageUseAmount"+GET.ApiId);
		self.cashUseAmount = document.getElementById("cashUseAmount"+GET.ApiId);
		self.totalUseAmount = document.getElementById("totalUseAmount"+GET.ApiId);
		self.baseAccumRate = document.getElementById("baseAccumRate");
		self.addAccumRate = document.getElementById("addAccumRate");
		self.orderSettlePrice = document.getElementById("paper_settlement");
		self.elementRegisted = true;
	};

	/*
	 * @name initializeStatus
	 * @return void
	 * @description 적재된 네이버 마일리지 엘리먼트들의 값을 초기화
	 */
	self.initializeStatus = function()
	{
		self.reqTxid.value = "";
		self.mileageUseAmount.value = 0;
		self.cashUseAmount.value = 0;
		self.totalUseAmount.value = 0;
		self.baseAccumRate.value = "";
		self.addAccumRate.value = "";
	};

	/*
	 * @name isUsed
	 * @return boolean 네이버 마일리지를 적립/사용하였다면 true 아니면 false
	 * @description 네이버 마일리지를 적립/사용하였는지 여부를 반환
	 */
	self.isUsed = function()
	{
		if (this.reqTxid.value.trim().length) return true;
		else return false;
	};

	/*
	 * @name useMileageCash
	 * @param rsd {
	 *   resultCode : 결과코드(OK, CANCEL, ERROR),
	 *   reqTxId : 결제승인 API에서 사용될 요청트랜잭션 아이디,
	 *   baseAccumRate : 기본적립률,
	 *   addAccumRate : 추가적립률,
	 *   mileageUseAmount : 마일리지사용금액,
	 *   cashUseAmount : 캐쉬사용금액,
	 *   totalUseAmount : 전체 사용금액(마일리지사용금액 + 캐쉬사용금액),
	 *   balanceAmount : 사용자의 마일리지 잔액,
	 *   sig : sig 값을 제외한 파라미터 값을 가맹점 인증키를 서명키로 사용하여 생성된 HMAC-SHA1 Signature
	 * }
	 * @return void
	 * @description 적립/사용된 네이버 마일리지를 주문서에 적용
	 */
	self.useMileageCash = function(rsd)
	{
		self.reqTxid.value = rsd.reqTxId;
		self.mileageUseAmount.value = rsd.mileageUseAmount;
		self.cashUseAmount.value = rsd.cashUseAmount;
		self.totalUseAmount.value = rsd.totalUseAmount;
		self.baseAccumRate.value = rsd.baseAccumRate;
		self.addAccumRate.value = rsd.addAccumRate;

		if (GET.SaveMode === "ncash") {
			document.getElementsByName("save_mode").item(0).value = "ncash";
		}

		calcu_settle();
	};

	/*
	 * @name cancelMileageCash
	 * @return void
	 * @description 적립/사용된 네이버 마일리지를 주문서상에서 제거
	 */
	self.cancelMileageCash = function()
	{
		self.initializeStatus();
		if (self.enabled === true) self.disable();
		calcu_settle();
	};

	/*
	 * @name getSaveMode
	 * @return string "ncash"는 네이버 마일리지만 적립, "both"는 네이버 마일리지와 쇼핑몰 적립금 모두 적립, "unused"는 네이버 마일리지와 쇼핑몰 적립금 모두 적립하지 않음, ""은 쇼핑몰 적립금으로 적립
	 * @description 적립금이 적립되는 위치를 반환
	 */
	self.getSaveMode = function()
	{
		var saveMode = document.getElementsByName("save_mode");
		if (saveMode.length) {
			for (var index = 0; index < saveMode.length; index++) {
				if (saveMode[index].getAttribute("type")=="radio") {
					if (saveMode[index].checked) return saveMode[index].value;
				}
				else {
					return saveMode[index].value;
				}
			}
			return null;
		}
		else {
			return saveMode.value;
		}
	};

	/*
	 * @name enable
	 * @return void
	 * @description 네이버 마일리지 모듈을 활성화
	 */
	self.enable = function()
	{
		self.enabled = true;
		if (GET.SaveMode === "choice" && self.getSaveMode() === "ncash") {
			self.reqTxid.setAttribute("required", "required");
			self.reqTxid.setAttribute("msgR", "네이버 마일리지를 적립 또는 사용하여 주시기 바랍니다.");
			nbp.mileage.active();
		}
	};

	/*
	 * @name disable
	 * @return void
	 * @description 네이버 마일리지 모듈을 비활성화 시키고, 주문서에 적용된 마일리지 적용상태를 초기화
	 */
	self.disable = function()
	{
		self.initializeStatus();
		self.enabled = false;
		if (GET.SaveMode === "choice") {
			self.reqTxid.removeAttribute("required");
			self.reqTxid.removeAttribute("msgR");
			nbp.mileage.inactive();
		}
	};

	/*
	 * @name getMaxUseAmount
	 * @return int 네이버 마일리지의 최대 사용금액
	 * @description 네이버 마일리지 모듈에서 사용할 수 있는 마일리지, 캐쉬의 최대 사용금액을 반환
	 */
	self.getMaxUseAmount = function()
	{
		return parseInt(self.maxUseAmount);
	};

	/*
	 * @name setMaxUseAmount
	 * @param int maxUseAmount 네이버 마일리지의 최대 사용금액
	 * @return void
	 * @description 네이버 마일리지 모듈에서 사용할 수 있는 마일리지, 캐쉬의 최대사용금액을 설정
	 */
	self.setMaxUseAmount = function(maxUseAmount)
	{
		self.maxUseAmount = maxUseAmount;
		if (self.elementRegisted) {
			if (self.moduleLoaded === true) {
				nbp.mileage.setMaxUseAmount(maxUseAmount);
			}
		}
	};

	/*
	 * @name getTotalUseAmount
	 * @return int totalUseAmount
	 * @description 주문서에 사용한 네이버 마일리지와 캐쉬의 총합을 반환
	 */
	self.getTotalUseAmount = function()
	{
		if (self.elementRegisted) {
			var totalUseAmount = parseInt(self.totalUseAmount.value);
			if (isNaN(totalUseAmount)) totalUseAmount = 0;
			return totalUseAmount;
		}
		else {
			return 0;
		}
	};

	/*
	 * @name loadModule
	 * @return void
	 * @description 네이버 마일리지 모듈을 주문서에 로드함
	 */
	self.loadModule = function()
	{
		self.moduleLoaded = nbp.mileage.initWithWcs({
			"sId" : GET.ElId,
			"sApiId" : GET.ApiId,
			"sDoneUrl" : GET.DoneUrl,
			"nMaxUseAmount" : self.getMaxUseAmount(),
			"sSig" : GET.Signature,
			"nTimestamp" : parseInt(GET.Timestamp),
			"nBaseAccumRate" : parseFloat(GET.BaseAccumRate),
			"event" : {
				"accum" : function(rsd)
				{
					if (rsd.resultCode === "OK") self.useMileageCash(rsd);
					else if (rsd.resultCode === "CANCEL") self.cancelMileageCash();
				},
				"beforeAccum" : function(rsd)
				{
					if (rsd.bActive === false) {
						if (rsd.nMaxUseAmount > 0) {
							alert("적립금 적립 위치를 네이버 마일리지로 선택하여주시기 바랍니다.");
						}
						else {
							alert("네이버 마일리지로 결제할 수 있는 금액이 없습니다.\r\n(배송비를 제외한 금액만 네이버 마일리지로 결제가능 합니다.)");
						}
					}

					if(self.checkSettlekind() === false){
						self.cancelSettlekind();
						return false;	
					}
				}
			}
		});
	};

	/*
	 * @name load
	 * @return void
	 * @description 주문서에 배송비가 로드된 시점까지 기다린 후 네이버 마일리지모듈을 준비시켜주고 적립금 적립위치 설정에 따라 모듈을 초기화
	 */
	self.load = function()
	{
		self.registElement();
		
		/*
		 * 배송비의 경우 스크립트파일을 사용하기 때문에 로드가 완료될때까지 체크해야함.
		 * 배송비 스크립트의 대기시간이 3초를 초과하면 에러처리.
		 */
		if (self.initExecCount>30) {
			alert("네이버 마일리지 로드에 실패하였습니다.\r\n주문서를 새로고침 하여주시기 바랍니다.");
			return false;
		}
		self.initExecCount++;
		if (_ID("paper_delivery").innerHTML.trim().length<1) return setTimeout(self.load, 100);

		var naverMileageAccum = document.createElement("div");
		naverMileageAccum.id = "_mileage_acc";
		document.getElementById("naver-mileage-container").appendChild(naverMileageAccum);

		self.loadModule();

		if (GET.SaveMode === "choice") self.disable();
		else self.enable();

		calcu_settle();
	};

	self.checkSettlekind = function ()
	{
		var settlekindObj = document.getElementsByName("settlekind");
		var settlekind = '';
		for(var i=0; i<settlekindObj.length; i++){
			if(settlekindObj[i].checked === true){
				settlekind = settlekindObj[i].value;
				break;
			}
		}

		if (GET.SaveMode === 'choice') {
			if(document.getElementById("save-mode-ncash").checked == true && settlekind == 't') {
				return false;
			}
		}
		else {
			if(settlekind == 't') return false;
		}
		return true;
	};

	self.cancelSettlekind = function ()
	{
		if (GET.SaveMode === 'choice') {
			document.getElementById("save-mode-default").checked = true;			
		}
		else {
			nbp.mileage.inactive();
			nbp.mileage.active();
		}
		self.cancelMileageCash();

		alert('PAYCO 결제수단은 네이버 마일리지 및 캐쉬 사용이 불가합니다.');
	}
};

(function(NMSL){
	if (document.attachEvent) window.attachEvent("onload", NMSL);
	else window.addEventListener("load", NMSL, false);
})
(function(){
	if (GET.Controller === "ord" && GET.Action === "order.php") {
		NaverMileage.load();
		if (GET.SaveMode === "choice") {
			document.getElementById("save-mode-default").onclick = function()
			{
				if(NaverMileage.checkSettlekind() === false){
					NaverMileage.cancelSettlekind();
				}
				if (NaverMileage.isUsed()) {
					if (this.checked && confirm("쇼핑몰 적립으로 변경하시면 네이버 마일리지의\r\n사용 및 적립이 취소됩니다.\r\n계속하시겠습니까?")) {
						NaverMileage.disable();
						calcu_settle();
					}
					else {
						document.getElementById("save-mode-ncash").checked = true;
					}
				}
				else {
					NaverMileage.disable();
					calcu_settle();
				}
			};
			document.getElementById("save-mode-ncash").onclick = function()
			{
				if(NaverMileage.checkSettlekind() === false){
					NaverMileage.cancelSettlekind();
				}
				NaverMileage.enable();
				calcu_settle();
			};
		}
		if(document.getElementsByName("settlekind").length > 0){
			for(var i=0; i<document.getElementsByName("settlekind").length; i++){
				document.getElementsByName("settlekind")[i].onclick = function ()
				{
					if(NaverMileage.reqTxid.value){
						if(NaverMileage.checkSettlekind() === false){
							NaverMileage.cancelSettlekind();
						}
					}
					
				};
			}
		}	
	}
});