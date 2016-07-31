/*** ����ī�װ��� ��� ***/
function getDispNm( dispno, idnm )
{
	var urlStr = "../interpark/ajaxSock.php?mode=getDispNm&dispno=" + dispno + "&dummy=" + new Date().getTime();
	var ajax = new Ajax.Request( urlStr,
	{
		method: "get",
		onComplete:  function ()
		{
			var req = ajax.transport;
			if ( req.status == 200 )
			{
				var response = req.responseText;
				if (_ID(idnm).tagName == 'INPUT') _ID(idnm).value = response;
				else _ID(idnm).innerHTML = response;
			}
		}
	} );
}

/*** INTERPARK : DISPLAY FORM OF GOODS (IDFG) ***/
IDFG = {
	mode : "",
	use : "",
	inpk_prdno : "",
	inpk_regdt : "",
	inpk_moddt : "",
	inpk_dispno : "",

	display : function ()
	{

		if (this.use == 'Y'){
			this.cateDisplay();	// ������ũ ī�װ� �� ���
			this.infoDisplay(); // ��ǰ����(��ǰ��ȣ ��) ���
			this.noteDisplay(); // ���ǻ��� ���
		}
	},

	infoDisplay : function ()
	{
		obj = _ID('inpterpark_product');
		if(obj == null) return;

		if (this.inpk_prdno){
			div = document.createElement('DIV');
			div.className = 'def';
			div.innerHTML = '������ũ ��ǰ��ȣ: ' + this.inpk_prdno + ', �����: ' + this.inpk_regdt + ', ������: ' + this.inpk_moddt;
			obj.appendChild(div);
		}

		div = document.createElement('DIV');
		div.className = 'def';
		div.style.marginTop = '2px';
		if(this.inpk_prdno){
			div.innerHTML = '<font color=EA0095><b>�� ��ǰ�� ������ũ�� ���۵� ��ǰ�Դϴ�!</b></font>&nbsp;&nbsp;<a target="_blank" href="http://www.interpark.com/product/MallDisplay.do?_method=detail&sc.shopNo=0000100000&sc.dispNo=' + this.inpk_dispno + '&sc.prdNo=' + this.inpk_prdno + '"><img src="../img/btn_interpark_goodsview.gif" align="absmiddle" style="margin-bottom:3"></a>';
		}else {
			div.innerHTML = '<font color=EA0095><b>�� ��ǰ�� ������ũ�� ���۵��� ���� ��ǰ�Դϴ�!</b></font>';
		}
		obj.appendChild(div);
	},

	cateDisplay : function ()
	{
		obj = _ID('interpark_category');
		if(obj == null) return;

		str = '\
			<div class=title>������ũ ��ǰ���<span>������ũ ���÷����� ������ ������ ��ǰ�� ������ũ�� ������ּ���</div>\
			<div id="inpterpark_product" style="margin-top: -8px;margin-bottom: 5px;"></div>\
			<div><font class=small1 color=444444>�� ��ǰ���� �̹����� �̹���ȣ������ �̿��Ͽ� ����Ǿ� �ִ��� üũ�ϼ���.</font></div>\
			<div><font class=small1 color=444444>�� ������ũ ī�װ��� ��Ī�ϼ���.</font></div>\
			<table width=790 cellpadding=0 cellspacing=1 border=1 bordercolor=#cccccc style="border-collapse:collapse">\
			<tr>\
			<td style="padding:7 7 7 10" bgcolor=f8f8f8>\
				<table width=100% cellpadding=0 cellspacing=0 border=0>\
				<col width=90%><col width=10%>\
				<tr height=20>\
				<td style="padding-left:10px;" id="inpk_dispnm"><span class="code_null"><font color=444444>��Ī�ʿ�</font></span></td>\
				<td align=center><a href="javascript:popupLayer(\'../interpark/popup.category.php?spot=inpk_dispno\',650,500);"><img src="../img/btn_open_catematch.gif"></a></td>\
				</tr>\
				</table>\
			</td>\
			</tr>\
			</table>\
			<div style="padding-top:3px"><font class=small1 color=444444>�� �� ������ �ϴ��� [' + (this.mode == 'modify' ? '����' : '���') + '] ��ư�� Ŭ���Ͽ� �����ϸ� ������ũ�� ��ǰ�� ��ϵ˴ϴ�.</font></div>\
			<div><font color=E6008D>��</font> <font class=small1 color=E6008D>����: ������ũ�� ��ǰ�� ������ �Ŀ��� ������ �Ұ����մϴ�. ������ �������ּ���.</font></div>\
			<input type=hidden name=inpk_dispno value="' + this.inpk_dispno + '">\
			';
		if (this.inpk_prdno != ''){
			str = str.replace('<a href="javascript:popupLayer(\'../interpark/popup.category.php?spot=inpk_dispno\',650,500);"><img src="../img/btn_open_catematch.gif"></a>', '');
		}
		if (this.inpk_dispno){
			str = str.replace('<span class="code_null"><font color=444444>��Ī�ʿ�</font></span>', '');
			str = str.replace('btn_open_catematch.gif', 'btn_open_cateedit.gif');
		}
		obj.innerHTML = str;

		if (this.inpk_dispno) getDispNm(this.inpk_dispno,'inpk_dispnm');
	},

	noteDisplay : function ()
	{
		obj = document.fm;
		if(obj == null) return;

		goodsMode = (this.mode == 'modify' ? '����' : '���');
		apiMode = (this.mode == 'modify' && this.inpk_prdno != '' ? '����' : '���');

		div = document.createElement('DIV');
		div.align = 'left';
		str = '<div style="padding:8px 13px;background:#f7f7f7;border:3px solid #C6C6C6;margin-bottom:18px;">';
    	str += '<div style="font-family:����;font-size:12px;padding:2 0 8 0"><b>�� �ʵ�! ������ư ���������� �� �о����!</b></div>';
		str += '<div style="font-family:����;font-size:12px;padding-top:2">�� ���� 1 : �� ��ǰ�� ' + goodsMode + '�ϸ� <font color=EA0095>������ũ���� �ǽð����� �ڵ�����(' + apiMode + ')</font>�˴ϴ�.</div>';
		str += '<div style="font-family:����;font-size:12px;padding-top:4">�� ���� 2 : ������ũ�� �� ��ǰ�� ������ �Ŀ��� <font color=EA0095>������ũ ī�װ� ������ �Ұ���</font>�մϴ�. ������ũ �з��� ������ ��Ī�ϼ���.</div>';
		str += '<div style="font-family:����;font-size:12px;padding-top:4">�� ���� 3 : ������ũ�� �� ��ǰ�� ������ �Ŀ��� <font color=EA0095>��ǰ������ �Ұ���</font>�մϴ�. <font color=EA0095>�Ǹ�����</font>�Ϸ��� <font color=EA0095>"��ǰ��¿���(���̱�)"</font>�� üũ������ �� �����ϼ���.</div>';
		str += '<div style="font-family:����;font-size:12px;padding-top:4">�� ���� 4 : �� ��ǰ�� <font color=EA0095>ǰ��</font>�� ��쿡�� <font color=EA0095>"ǰ����ǰ"</font>�� �� üũ�ϰ� �����ϼ���.</div>';
		str += '<div style="font-family:����;font-size:12px;padding-top:4">�� ���� 5 : �� ��ǰ�� <font color=EA0095>�Ǹ�����</font>�Ϸ��� <font color=EA0095>"��ǰ��¿���(���̱�)"</font>�� üũ������ �� �����ϼ���.</div>';
		str += '<div style="font-family:����;font-size:12px;padding-top:4">�� ���� 6 : <font color=EA0095>Ȯ���̹���</font>�� ������ũ�� ���۵ǹǷ� �ݵ�� �Է��ϼ���.';
		str += '<div style="font-family:����;font-size:12px;padding-top:4">�� ���� 7 : ��ǰ���� �̹����� ����å(�ܺθ�ũ����)�� ���� <font color=EA0095>�̹���ȣ����</font>�� �̿��ϼž� ������ũ���� ���������� ��µ˴ϴ�.';
		str += '</div>';
		div.innerHTML = str;
		obj.appendChild(div);
	}
}

window.InterparkProductRequireInfo = function()
{
	var
	self = this,
	Priv_extraInfoTable = $('el-extra-info-table'),
	Priv_beforeRow = new Array();

	self.displayRequireInfoNotice = function()
	{
		var noticeElement = document.createElement("div");
		noticeElement.innerHTML = "�� ������ũ ��ǰ������ ������ũ�� ��å�� ���� ��ǰ �ʼ� ���� �Է¶��� ���ѵ� �� �ֽ��ϴ�.";
		noticeElement.style.color = "#e6008d";
		for(var currentElement=Priv_extraInfoTable.previousSibling; currentElement; currentElement=currentElement.previousSibling)
		{
			if(currentElement.nodeType==1)
			{
				Priv_extraInfoTable.parentNode.insertBefore(noticeElement, currentElement);
				break;
			}
		}
	};

	/*
	 * ���� �����Ǿ��ִ� ��ǰ �ʼ� ���� �׸���� �ʱ�ȭ �� ó���� ���ڵ�� Priv_beforeRow�� �����
	 */
	self.normalizeRows = function()
	{
		for(var i=1; i<Priv_extraInfoTable.rows.length; i++)
		{
			Priv_beforeRow.push(Priv_extraInfoTable.rows[i]);
			var extraInfoInput = Priv_extraInfoTable.rows[i].getElementsByTagName("input");
			for(var t=0; t<extraInfoInput.length; t++)
			{
				if(/^(extra_info_title|extra_info_desc)/.test(extraInfoInput[t].name))
				{
					extraInfoInput[t].parentNode.innerHTML = '<input type="text" name="'+extraInfoInput[t].name+'" value="'+extraInfoInput[t].value+'" style="width: 100%;"/>';
				}
			}
			Priv_extraInfoTable.rows[i].children[Priv_extraInfoTable.rows[i].children.length-1].innerHTML = '<a href="javascript:void(0);" onClick="nsInformationByGoods.delrow();"><img src="../img/i_del.gif"></a>';
		}
	};

	/*
	 * ��ǰ �ʼ� ���� ���ڵ� ���·� ��ȯ
	 */
	self.parseRequireRowInterface = function(reqInfoRow)
	{
		return {
			getTitleInput : function()
			{
				return reqInfoRow.children[0].children[0];
			},
			getDescriptionInput : function()
			{
				return reqInfoRow.children[1].children[0];
			},
			getDeleteField : function()
			{
				return reqInfoRow.children[reqInfoRow.children.length-1];
			}
		};
	};

	/*
	 * ��ǰ �ʼ� ������ �߰�(������ ���� ������ ������ ������ �߰������ʰ� �ش� ������ ������ũ������ ġȯ)
	 */
	self.appendRequireInfoField = function(reqInfoSet)
	{
		for(var i=0; i<Priv_beforeRow.length; i++)
		{
			var currentRow = self.parseRequireRowInterface(Priv_beforeRow[i]);
			if(currentRow.getTitleInput().value==reqInfoSet.Name)
			{
				var targetRow = currentRow;
				break;
			}
		}
		if(targetRow===undefined)
		{
			nsInformationByGoods.add2row();
			var targetRow = self.parseRequireRowInterface(Priv_extraInfoTable.rows[Priv_extraInfoTable.rows.length-1]);
		}
		var
		itemField = targetRow.getTitleInput(),
		descField = targetRow.getDescriptionInput(),
		deleteField = targetRow.getDeleteField();

		/^.+\[(\d+)\]$/.test(itemField.name);
		itemField.parentNode.style.textAlign = "left";
		itemField.parentNode.innerHTML
		= '<input type="hidden" name="extra_info_title['+RegExp.$1+']" value="'+reqInfoSet.Name+'"/>'
		+ '<input type="hidden" name="extra_info_inpk_code['+RegExp.$1+']" value="'+reqInfoSet.Group+reqInfoSet.Code+'"/>'
		+ reqInfoSet.Name;
		switch(reqInfoSet.Type)
		{
			case "R1":
				if(reqInfoSet.IsRequire)
				{
					descField.required = true;
					descField.label = reqInfoSet.Name;
				}
				descField.placeholder = reqInfoSet.Description;
				descField.parentNode.innerHTML += '<input type="hidden" name="extra_info_inpk_type['+RegExp.$1+']" value="I"/>';
				break;
			case "R2":
				descField.parentNode.innerHTML
				= '<input type="radio" name="extra_info_inpk_type['+RegExp.$1+']" id="extra_info_inpk_type['+RegExp.$1+']-Y" value="Y" style="border: none;" label="'+reqInfoSet.Name+'" required="required"/>'
				+ '<label for="extra_info_inpk_type['+RegExp.$1+']-Y">��</label>'
				+ '<input type="radio" name="extra_info_inpk_type['+RegExp.$1+']" id="extra_info_inpk_type['+RegExp.$1+']-N" value="N" style="border: none; margin-left: 5px;"/>'
				+ '<label for="extra_info_inpk_type['+RegExp.$1+']-N">�ƴϿ�</label>'
				+ '<input type="radio" name="extra_info_inpk_type['+RegExp.$1+']" id="extra_info_inpk_type['+RegExp.$1+']-I" value="I" style="border: none; margin-left: 5px;"/>'
				+ '<label for="extra_info_inpk_type['+RegExp.$1+']-I">�����Է�</label>'
				+ '<input type="text" name="'+descField.name+'" id="'+descField.name+'" style="margin-left: 5px; visibility: hidden;" label="'+reqInfoSet.Name+'" required="required"/>';

				var radioYes = $("extra_info_inpk_type["+RegExp.$1+"]-Y"), radioNo = $("extra_info_inpk_type["+RegExp.$1+"]-N"), radioInsert = $("extra_info_inpk_type["+RegExp.$1+"]-I");

				$(descField.name).value = descField.value;
				switch(descField.value)
				{
					case "":
						break;
					case "Y":
						radioYes.checked = true;
						$(descField.name).setAttribute("data-value", "");
						break;
					case "N":
						radioNo.checked = true;
						$(descField.name).setAttribute("data-value", "");
						break;
					default:
						radioInsert.checked = true;
						$(descField.name).style.visibility = "visible";
						$(descField.name).setAttribute("data-value", descField.value);
						break;
				}

				// onclick�̺�Ʈ���� Ŭ���� ������ ������ ���ؼ� �͸��Լ� ���
				(function(){
					var
					description = $(descField.name);
					radioYes.onclick = function()
					{
						if(description.getAttribute("data-value")===null) description.setAttribute("data-value", description.value);
						description.value = "Y";
						description.style.visibility = "hidden";
					};
					radioNo.onclick = function()
					{
						if(description.getAttribute("data-value")===null) description.setAttribute("data-value", description.value);
						description.value = "N";
						description.style.visibility = "hidden";
					};
					radioInsert.onclick = function()
					{
						if(description.getAttribute("data-value")!==null) description.value = description.getAttribute("data-value");
						description.removeAttribute("data-value");
						description.style.visibility = "visible";
					};
				})();
				break;
			case "R3":
				descField.parentNode.innerHTML
				= '<input type="hidden" name="'+descField.name+'" value="�Ǿ�ǰ�ƴ�"/>'
				+ '<input type="hidden" name="extra_info_inpk_type['+RegExp.$1+']" value="N"/>'
				+ "�Ǿ�ǰ�ƴ�(������ũ ��å�� ���Ͽ� �Ǿ�ǰ���� �׸��� �����ϽǼ� �����ϴ�.)";
			case "M1":
				break;
		}
		deleteField.innerHTML = "-";
	};

	/*
	 * �ߺз� ��ȣ�� ���� ��ǰ�� ���� �� ��ǰ �ʼ� ���� ����
	 */
	self.setProductGroup = function(inpkMidDispNo)
	{
		new Ajax.Request("../interpark/ajax.getProductReqInfo.php", {
			method : "post",
			parameters : "inpkMidDispNo="+inpkMidDispNo,
			onComplete : function(xhr)
			{
				var reqInfo = eval("("+xhr.responseText+")");
				self.normalizeRows();
				for(var i=0; i<reqInfo.length; i++)
				{
					self.appendRequireInfoField(reqInfo[i]);
				}
				inpkPrdReqInfo.appendRequireInfoField({
					Name : "AS���ɿ���",
					Group : "INFO",
					Code : "AS",
					Type : "R1",
					IsRequire : true,
					Description : "* AS������ �Է��ϼ���"
				});
			},
			onException : function(xhr, exception)
			{
				alert(exception.message);
				//alert("[�ý��� ����]\r\n�����ͷ� �����Ͽ� �ֽñ� �ٶ��ϴ�.");
			},
			onFailure : function(xhr, exception)
			{
				alert("[������ ��û ����]\r\n�����ͷ� �����Ͽ� �ֽñ� �ٶ��ϴ�.");
			}
		});
	};
};