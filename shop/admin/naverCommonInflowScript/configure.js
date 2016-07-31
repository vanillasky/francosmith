$(document).observe("dom:loaded", function(){

	// 네이버 공통유입스크립트 설정폼 컨트롤 객체
	var CommonScriptConfigure = new function()
	{
		var
		self = this,
		commonInflowScriptConfigureForm = $("common-inflow-script-configure-form"),
		naverServiceConfigure = document.getElementById("naver-service-configure"),	// 부가서비스 설정 폼에는 Prototype이 영향을 미치지 못하도록 document.getElementById 사용
		checkDuplicateButton = $("account-id-check-duplicate");

		self.isExists = null;
		self.isEnabled = document.getElementById("set-account-id")?true:false;
		self.checkDuplicateButton = checkDuplicateButton;
		self.commonInflowScriptConfigureForm = commonInflowScriptConfigureForm;

		self.getAccountId = function()
		{
			return commonInflowScriptConfigureForm.serialize(true).accountId.trim();
		}

		self.setMode = function(mode)
		{
			commonInflowScriptConfigureForm.mode.value = mode;
		}

		self.getMode = function()
		{
			return commonInflowScriptConfigureForm.mode.value;
		}

		self.checkDuplicateAccountId = function(event)
		{
			if(self.isEnabled!==false)
			{
				alert("설정된 네이버공통인증키는 수정할 수 없습니다.");
				return false;
			}

			if(chkForm(commonInflowScriptConfigureForm))
			{
				self.setMode("checkDuplicateAccountId");
				new Ajax.Request(commonInflowScriptConfigureForm.action, {
					method    : "POST",
					postBody  : commonInflowScriptConfigureForm.serialize(),
					onSuccess : function(response)
					{
						var result = eval("("+response.responseText+")");
						if(result.code && result.message)
						{
							self.isExists = (result.code!=='IS_NOT_EXISTS');
							alert(result.message);
						}
						else
						{
							alert("처리도중 통신에러가 발생하였습니다.\r\n잠시 후 다시 시도해주시기 바랍니다.");
						}
					}
				});
			}
		}

		self.submit = function()
		{
			if(chkForm(commonInflowScriptConfigureForm))
			{
				var DATA = commonInflowScriptConfigureForm.serialize(true);
				if(self.isEnabled===false)
				{
					if(self.isExists!==false)
					{
						alert("네이버공통인증키의 중복확인이 필요합니다.");
						checkDuplicateButton.focus();
						return false;
					}
					if(confirm("[주의] \"네이버공통인증키\"는 한번 입력하시면 변경하실 수 없습니다.\r\n입력하신 네이버공통인증키가 \""+DATA.accountId+"\"이(가) 맞습니까?")===false) return;
				}

				self.setMode("saveConfigure");
				new Ajax.Request(commonInflowScriptConfigureForm.action, {
					method    : "POST",
					postBody  : commonInflowScriptConfigureForm.serialize(),
					onSuccess : function(response)
					{
						var result = eval("("+response.responseText+")");
						if(result.code && result.message)
						{
							if(result.code==='SUCCESS')
							{
								if(DATA.accountId.trim().length>0)
								{
									if(self.isEnabled===false) self.confirm();
									self.enable();
								}
								else
								{
									self.disable();
								}
							}
							alert(result.message);
						}
						else
						{
							alert("설정정보를 저장하는중에 에러가 발생하였습니다.\r\n잠시 후 다시 시도해주시기 바랍니다.");
						}
					}
				});
			}
		}

		self.confirm = function()
		{
			var
			accountIdDisplay = document.createElement("div"),
			commonInflowScriptConfigure = commonInflowScriptConfigureForm.serialize(true);
			accountIdDisplay.innerHTML = commonInflowScriptConfigure.accountId;
			accountIdDisplay.className = "confirmed-account-id";
			commonInflowScriptConfigureForm.accountId.parentNode.replaceChild(accountIdDisplay, commonInflowScriptConfigureForm.accountId);
			$("account-id-check-duplicate").remove();
			accountIdDisplay.parentNode.innerHTML += '<input type="hidden" name="accountId" value="'+commonInflowScriptConfigure.accountId+'" class="line" required="required"/>';
			self.isEnabled = true;
		}

		self.enable = function()
		{
			if(naverServiceConfigure)
			{
				naverServiceConfigure.disabled = false;
				for(var input=naverServiceConfigure, a=0; a<input.length; a++)
				{
					input[a].disabled = false;
				}
			}
		};

		self.disable = function()
		{
			if(naverServiceConfigure)
			{
				naverServiceConfigure.disabled = true;
				for(var input=naverServiceConfigure, a=0; a<input.length; a++)
				{
					input[a].disabled = true;
				}
			}
		};
	};

	// 화이트리스트 설정폼 컨트롤 객체
	var WhiteList = new function()
	{
		var
		self = this,
		whiteListContainer = document.getElementById("white-list-container"),
		initWhiteList = whiteListContainer.getAttribute("data-initialize").split("|"),
		whiteListControll = document.createElement("div"),
		appendButton = document.createElement("img");

		// Initialize
		this.initialize = function(){
			var httpText = document.createElement("span");
			httpText.innerHTML = "http://";
			appendButton.src = "../img/i_add.gif";
			appendButton.style.verticalAlign = "middle";
			appendButton.style.cursor = "pointer";
			appendButton.onclick = function()
			{
				self.appendWhiteList();
			};
			whiteListControll.appendChild(httpText);
			whiteListControll.innerHTML += '<input type="text" name="whiteList[]" value="'+(initWhiteList instanceof Array?initWhiteList.shift():"")+'" style="vertical-align: middle; width: 200px;"/>';
			whiteListControll.appendChild(appendButton);
			whiteListContainer.appendChild(whiteListControll);
			self.processPointer = whiteListControll.childNodes[1];
			for(var a=0; a<initWhiteList.length; a++)
			{
				if(initWhiteList[a].trim().length>0) self.appendWhiteList(initWhiteList[a]);
			}
		}

		this.appendWhiteList = function(value)
		{
			var
			whiteList = document.createElement("div"),
			httpText = document.createElement("span"),
			whiteListDelImage = document.createElement("img");
			whiteListDelImage.src = "../img/i_del.gif";
			whiteListDelImage.style.verticalAlign = "middle";
			whiteListDelImage.style.cursor = "pointer";
			whiteListDelImage.onclick = function()
			{
				whiteListContainer.removeChild(whiteList);
			}
			httpText.innerHTML = "http://";
			whiteList.appendChild(httpText);
			whiteList.innerHTML += '<input type="text" name="whiteList[]" value="'+(value?value:"")+'" style="vertical-align: middle; width: 200px;"/>';
			whiteList.appendChild(whiteListDelImage);
			whiteListContainer.appendChild(whiteList);
		}
	};

	// 공통유입스크립트 중복체크
	if(CommonScriptConfigure.checkDuplicateButton)
	{
		$("account-id-check-duplicate").onclick = function(){
			CommonScriptConfigure.checkDuplicateAccountId();
		};
	}

	// 공통유입스크립트 설정폼 전송
	if(CommonScriptConfigure.commonInflowScriptConfigureForm)
	{
		CommonScriptConfigure.commonInflowScriptConfigureForm.onsubmit = function(){
			CommonScriptConfigure.submit();
			return false;
		};
	}

	// 공통인증키가 활성화 되어있으면 부가서비스 설정폼도 활성화시킴
	if(CommonScriptConfigure.isEnabled) CommonScriptConfigure.enable();
	else CommonScriptConfigure.disable();

	// 화이트리스트 설정폼 초기화
	WhiteList.initialize();

});