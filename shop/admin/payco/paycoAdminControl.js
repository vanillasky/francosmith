function exec_add()
{
	var ret;
	var str = new Array();
	var obj = document.forms['paycoForm']['cate[]'];
	for (i=0;i<obj.length;i++){
		if (obj[i].value){
			str[str.length] = obj[i][obj[i].selectedIndex].text;
			ret = obj[i].value;
		}
	}
	if (!ret){
		alert('ī�װ��� �������ּ���');
		return;
	}
	if(!chk_add_category(ret)){
		alert('�ߺ��� ī�װ� �Դϴ�.');
		return;
	}
	var obj = document.getElementById('objCategory');
	oTr = obj.insertRow();
	oTd = oTr.insertCell();
	oTd.id = "currPosition";
	oTd.innerHTML = str.join(" > ");
	oTd = oTr.insertCell();
	oTd.innerHTML = "\<input type=text name=e_category[] value='" + ret + "' style='display:none'>";
	oTd = oTr.insertCell();
	oTd.innerHTML = "<a href='javascript:void(0)' onClick='javascript:cate_del(this.parentNode.parentNode);'><img src='../img/i_del.gif' align=absmiddle></a>";
}

function cate_del(el)
{
	idx = el.rowIndex;
	var obj = document.getElementById('objCategory');
	obj.deleteRow(idx);
}

function chk_add_category(cate)
{
	var i=0;
	var j=0;
	var category = document.getElementsByName('e_category[]');
	for(i=0;i<category.length;i++){
		for(j=3;j<=cate.length;j=j+3){
			if(cate.substring(0,j)==category[i].value)return false;
		}
	}
	return true;
}

function copy_txt(val)
{
	if (window.clipboardData) {
		alert("�ڵ带 �����Ͽ����ϴ�. \n���ϴ� ���� �ٿ��ֱ�(Ctrl+V)�� �Ͻø� �˴ϴ�.");
		window.clipboardData.setData("Text", val);
	} 
	else {
		prompt("�ڵ带 Ŭ������� ����(Ctrl+C) �Ͻð�. \n���ϴ� ���� �ٿ��ֱ�(Ctrl+V)�� �Ͻø� �˴ϴ�.", val);
	}
}

function changeButtonType(buttonType)
{
	var buttonTypeArray = new Array('A', 'B', 'C');
	for(var i=0; i<buttonTypeArray.length; i++){
		var displayElement = document.getElementById('buttonType' + buttonTypeArray[i]);
		if(buttonTypeArray[i] == buttonType){
			displayElement.style.display = 'block';
		}
		else {
			displayElement.style.display = 'none';
		}
	}
}

function defaultTextValSetting(setType, el)
{
	var textIDArr = new Array('paycoSellerKey', 'paycoCpId');
	var textMsgArr = new Array('�������ڵ�', '����ID');
	switch(setType){
		case 'focus':
			el.style.color = '#000000';
			for(var i=0; i<textIDArr.length; i++){
				if(el.id == textIDArr[i] && el.value == textMsgArr[i]){
					el.value = '';
					break;
				}
			}
		break;

		case 'blur':
			if(el.value == ''){
				for(var i=0; i<textIDArr.length; i++){
					if(el.id == textIDArr[i]){
						el.value = textMsgArr[i];
						el.style.color = '#A6A6A6';
						break;
					}
				}
			}
		break;

		case 'set':
			for(var i=0; i<textIDArr.length; i++){
				var textElement = document.getElementById(textIDArr[i]);
				if(textElement.value == ''){
					textElement.value = textMsgArr[i];
					textElement.style.color = '#A6A6A6';
				}
			}
		break;

		case 'submit':
			for(var i=0; i<textIDArr.length; i++){
				var textElement = document.getElementById(textIDArr[i]);
				if(textElement.value == textMsgArr[i]) textElement.value = '';
			}
		break;
	}
}

function submitSaveService()
{
	nsGodoLoadingIndicator.init({
		psObject : document.getElementById('ifrmHidden')
	});
	nsGodoLoadingIndicator.show();
	return true;
}

function submitSaveID()
{
	document.getElementById("saveId").style.display = "none";

	var f = document.paycoServiceForm;

	defaultTextValSetting('submit', '');

	if(!chkForm(f)) {
		document.getElementById("saveId").style.display = "block";
		return false;
	}

	if(!confirm("����� ���θ��� ������ ���� ��ư�� ����˴ϴ�.\n����Ͻðڽ��ϱ�?")) {
		document.getElementById("saveId").style.display = "block";
		return false;
	}

	nsGodoLoadingIndicator.init({});
	var param = getSaveIdParam(f);
	var ajax = new Ajax.Request( "./paycoIndb.php",
	{
		method: "post",
		parameters: "mode=saveID" + param,
		onFailure: function ()
		{
			nsGodoLoadingIndicator.hide();
			alert("�˼��մϴ�. ����� ���������� �ʽ��ϴ�.\n�ٽ��ѹ� �õ��Ͽ� �ּ���.");
			document.getElementById("saveId").style.display = "block";
			return false;
		},
		onLoaded : function()
		{
			nsGodoLoadingIndicator.hide();
		},
		onLoading : function()
		{
			nsGodoLoadingIndicator.show();
		},
		onComplete: function (req)
		{
			if (req.status != 200 || req.responseText ==''){
				alert("�˼��մϴ�. ����� ���������� �ʽ��ϴ�.\n�ٽ��ѹ� �õ��Ͽ� �ּ���.");
				document.getElementById("saveId").style.display = "block";
				return false;	
			}

			//result[0] - ��������, result[1] - �޽���
			var result = _returnValidateData = new Array();
			var elNameArr = {
				seller_key : 'validateCheckMsg_paycoSellerKey',
				cp_id : 'validateCheckMsg_paycoCpId'
			};
			var msgNameArr = {
				seller_key : '�������ڵ�',
				cp_id : '����ID'
			};

			result = req.responseText.split("|");
			if(result[0] == 'success'){
				alert(result[1]);
				window.document.location.reload();
			}
			else if(result[0] == 'validateFail'){
				_returnValidateData = result[1].split("^");
				for(var i in _returnValidateData){
					var returnValidateData = new Array();
					returnValidateData = _returnValidateData[i].split("@");
					if(returnValidateData[1] == 'Y'){
						document.getElementById(elNameArr[returnValidateData[0]]).innerHTML = '��밡���� ' + msgNameArr[returnValidateData[0]] + ' �Դϴ�';
						document.getElementById(elNameArr[returnValidateData[0]]).style.color = '#627dce';
					}
					else if(returnValidateData[1] == 'N'){
						document.getElementById(elNameArr[returnValidateData[0]]).innerHTML = '��ȿ�� ' + msgNameArr[returnValidateData[0]] + '�� �ƴմϴ�';
						document.getElementById(elNameArr[returnValidateData[0]]).style.color = 'red';
					}
					else {
						document.getElementById(elNameArr[returnValidateData[0]]).innerHTML = '';
					}
				}
				document.getElementById("saveId").style.display = "block";
			}
			else {
				alert(result[1]);
				document.getElementById("saveId").style.display = "block";
				return false;
			}
		}
	});
}

function getSaveIdParam(f)
{
	var param = '';
	var formUseType = '';
	var formTestYn = '';

	//���� ����
	for(var i=0; i<f.useType.length; i++){
		if(f.useType[i].checked == true){
			formUseType = f.useType[i].value;
			param += "&useType=" + f.useType[i].value;
			break;
		}
	}

	//��� ����
	for(var i=0; i<f.testYn.length; i++){
		if(f.testYn[i].checked == true){
			formTestYn = f.testYn[i].value;
			param += "&testYn=" + f.testYn[i].value;
			break;
		}
	}

	if(formUseType != 'N'){
		//�������ڵ�
		param += "&paycoSellerKey=" + f.paycoSellerKey.value;
		//����ID
		param += "&paycoCpId=" + f.paycoCpId.value;
	}
	
	return param;
}