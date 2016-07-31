$(document).observe("dom:loaded", function(){

	// ���̹� �������Խ�ũ��Ʈ ������ ��Ʈ�� ��ü
	var CommonScriptConfigure = new function()
	{
		var
		self = this,
		commonInflowScriptConfigureForm = $("common-inflow-script-configure-form"),
		naverServiceConfigure = document.getElementById("naver-service-configure"),	// �ΰ����� ���� ������ Prototype�� ������ ��ġ�� ���ϵ��� document.getElementById ���
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
				alert("������ ���̹���������Ű�� ������ �� �����ϴ�.");
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
							alert("ó������ ��ſ����� �߻��Ͽ����ϴ�.\r\n��� �� �ٽ� �õ����ֽñ� �ٶ��ϴ�.");
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
						alert("���̹���������Ű�� �ߺ�Ȯ���� �ʿ��մϴ�.");
						checkDuplicateButton.focus();
						return false;
					}
					if(confirm("[����] \"���̹���������Ű\"�� �ѹ� �Է��Ͻø� �����Ͻ� �� �����ϴ�.\r\n�Է��Ͻ� ���̹���������Ű�� \""+DATA.accountId+"\"��(��) �½��ϱ�?")===false) return;
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
							alert("���������� �����ϴ��߿� ������ �߻��Ͽ����ϴ�.\r\n��� �� �ٽ� �õ����ֽñ� �ٶ��ϴ�.");
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

	// ȭ��Ʈ����Ʈ ������ ��Ʈ�� ��ü
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

	// �������Խ�ũ��Ʈ �ߺ�üũ
	if(CommonScriptConfigure.checkDuplicateButton)
	{
		$("account-id-check-duplicate").onclick = function(){
			CommonScriptConfigure.checkDuplicateAccountId();
		};
	}

	// �������Խ�ũ��Ʈ ������ ����
	if(CommonScriptConfigure.commonInflowScriptConfigureForm)
	{
		CommonScriptConfigure.commonInflowScriptConfigureForm.onsubmit = function(){
			CommonScriptConfigure.submit();
			return false;
		};
	}

	// ��������Ű�� Ȱ��ȭ �Ǿ������� �ΰ����� �������� Ȱ��ȭ��Ŵ
	if(CommonScriptConfigure.isEnabled) CommonScriptConfigure.enable();
	else CommonScriptConfigure.disable();

	// ȭ��Ʈ����Ʈ ������ �ʱ�ȭ
	WhiteList.initialize();

});