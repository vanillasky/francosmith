/*** 전시카테고리명 출력 ***/
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
			this.cateDisplay();	// 인터파크 카테고리 폼 출력
			this.infoDisplay(); // 상품정보(상품번호 등) 출력
			this.noteDisplay(); // 유의사항 출력
		}
	},

	infoDisplay : function ()
	{
		obj = _ID('inpterpark_product');
		if(obj == null) return;

		if (this.inpk_prdno){
			div = document.createElement('DIV');
			div.className = 'def';
			div.innerHTML = '인터파크 상품번호: ' + this.inpk_prdno + ', 등록일: ' + this.inpk_regdt + ', 수정일: ' + this.inpk_moddt;
			obj.appendChild(div);
		}

		div = document.createElement('DIV');
		div.className = 'def';
		div.style.marginTop = '2px';
		if(this.inpk_prdno){
			div.innerHTML = '<font color=EA0095><b>이 상품은 인터파크로 전송된 상품입니다!</b></font>&nbsp;&nbsp;<a target="_blank" href="http://www.interpark.com/product/MallDisplay.do?_method=detail&sc.shopNo=0000100000&sc.dispNo=' + this.inpk_dispno + '&sc.prdNo=' + this.inpk_prdno + '"><img src="../img/btn_interpark_goodsview.gif" align="absmiddle" style="margin-bottom:3"></a>';
		}else {
			div.innerHTML = '<font color=EA0095><b>이 상품은 인터파크로 전송되지 않은 상품입니다!</b></font>';
		}
		obj.appendChild(div);
	},

	cateDisplay : function ()
	{
		obj = _ID('interpark_category');
		if(obj == null) return;

		str = '\
			<div class=title>인터파크 상품등록<span>인터파크 샵플러스에 입점한 상점은 상품을 인터파크로 등록해주세요</div>\
			<div id="inpterpark_product" style="margin-top: -8px;margin-bottom: 5px;"></div>\
			<div><font class=small1 color=444444>① 상품설명 이미지가 이미지호스팅을 이용하여 연결되어 있는지 체크하세요.</font></div>\
			<div><font class=small1 color=444444>② 인터파크 카테고리를 매칭하세요.</font></div>\
			<table width=790 cellpadding=0 cellspacing=1 border=1 bordercolor=#cccccc style="border-collapse:collapse">\
			<tr>\
			<td style="padding:7 7 7 10" bgcolor=f8f8f8>\
				<table width=100% cellpadding=0 cellspacing=0 border=0>\
				<col width=90%><col width=10%>\
				<tr height=20>\
				<td style="padding-left:10px;" id="inpk_dispnm"><span class="code_null"><font color=444444>매칭필요</font></span></td>\
				<td align=center><a href="javascript:popupLayer(\'../interpark/popup.category.php?spot=inpk_dispno\',650,500);"><img src="../img/btn_open_catematch.gif"></a></td>\
				</tr>\
				</table>\
			</td>\
			</tr>\
			</table>\
			<div style="padding-top:3px"><font class=small1 color=444444>③ 본 페이지 하단의 [' + (this.mode == 'modify' ? '수정' : '등록') + '] 버튼을 클릭하여 저장하면 인터파크로 상품이 등록됩니다.</font></div>\
			<div><font color=E6008D>※</font> <font class=small1 color=E6008D>주의: 인터파크로 상품을 전송한 후에는 수정이 불가능합니다. 신중히 선택해주세요.</font></div>\
			<input type=hidden name=inpk_dispno value="' + this.inpk_dispno + '">\
			';
		if (this.inpk_prdno != ''){
			str = str.replace('<a href="javascript:popupLayer(\'../interpark/popup.category.php?spot=inpk_dispno\',650,500);"><img src="../img/btn_open_catematch.gif"></a>', '');
		}
		if (this.inpk_dispno){
			str = str.replace('<span class="code_null"><font color=444444>매칭필요</font></span>', '');
			str = str.replace('btn_open_catematch.gif', 'btn_open_cateedit.gif');
		}
		obj.innerHTML = str;

		if (this.inpk_dispno) getDispNm(this.inpk_dispno,'inpk_dispnm');
	},

	noteDisplay : function ()
	{
		obj = document.fm;
		if(obj == null) return;

		goodsMode = (this.mode == 'modify' ? '수정' : '등록');
		apiMode = (this.mode == 'modify' && this.inpk_prdno != '' ? '수정' : '등록');

		div = document.createElement('DIV');
		div.align = 'left';
		str = '<div style="padding:8px 13px;background:#f7f7f7;border:3px solid #C6C6C6;margin-bottom:18px;">';
    	str += '<div style="font-family:굴림;font-size:12px;padding:2 0 8 0"><b>※ 필독! 수정버튼 누르기전에 꼭 읽어보세요!</b></div>';
		str += '<div style="font-family:굴림;font-size:12px;padding-top:2">※ 주의 1 : 이 상품을 ' + goodsMode + '하면 <font color=EA0095>인터파크에도 실시간으로 자동전송(' + apiMode + ')</font>됩니다.</div>';
		str += '<div style="font-family:굴림;font-size:12px;padding-top:4">※ 주의 2 : 인터파크로 이 상품을 전송한 후에는 <font color=EA0095>인터파크 카테고리 수정이 불가능</font>합니다. 인터파크 분류는 신중히 매칭하세요.</div>';
		str += '<div style="font-family:굴림;font-size:12px;padding-top:4">※ 주의 3 : 인터파크로 이 상품을 전송한 후에는 <font color=EA0095>상품삭제가 불가능</font>합니다. <font color=EA0095>판매중지</font>하려면 <font color=EA0095>"상품출력여부(보이기)"</font>를 체크해제한 후 저장하세요.</div>';
		str += '<div style="font-family:굴림;font-size:12px;padding-top:4">※ 주의 4 : 이 상품이 <font color=EA0095>품절</font>일 경우에는 <font color=EA0095>"품절상품"</font>을 꼭 체크하고 수정하세요.</div>';
		str += '<div style="font-family:굴림;font-size:12px;padding-top:4">※ 주의 5 : 이 상품을 <font color=EA0095>판매중지</font>하려면 <font color=EA0095>"상품출력여부(보이기)"</font>를 체크해제한 후 수정하세요.</div>';
		str += '<div style="font-family:굴림;font-size:12px;padding-top:4">※ 주의 6 : <font color=EA0095>확대이미지</font>는 인터파크에 전송되므로 반드시 입력하세요.';
		str += '<div style="font-family:굴림;font-size:12px;padding-top:4">※ 주의 7 : 상품설명 이미지는 고도정책(외부링크차단)에 따라 <font color=EA0095>이미지호스팅</font>을 이용하셔야 인터파크에서 정상적으로 출력됩니다.';
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
		noticeElement.innerHTML = "※ 인터파크 상품연동시 인터파크측 정책에 따라 상품 필수 정보 입력란이 제한될 수 있습니다.";
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
	 * 현재 생성되어있는 상품 필수 정보 항목들을 초기화 후 처리된 레코드는 Priv_beforeRow에 저장됨
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
	 * 상품 필수 정보 레코드 형태로 변환
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
	 * 상품 필수 정보를 추가(기존에 같은 명의의 정보가 있으면 추가하지않고 해당 정보를 인터파크정보로 치환)
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
				+ '<label for="extra_info_inpk_type['+RegExp.$1+']-Y">예</label>'
				+ '<input type="radio" name="extra_info_inpk_type['+RegExp.$1+']" id="extra_info_inpk_type['+RegExp.$1+']-N" value="N" style="border: none; margin-left: 5px;"/>'
				+ '<label for="extra_info_inpk_type['+RegExp.$1+']-N">아니오</label>'
				+ '<input type="radio" name="extra_info_inpk_type['+RegExp.$1+']" id="extra_info_inpk_type['+RegExp.$1+']-I" value="I" style="border: none; margin-left: 5px;"/>'
				+ '<label for="extra_info_inpk_type['+RegExp.$1+']-I">직접입력</label>'
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

				// onclick이벤트에서 클로저 스코프 제한을 위해서 익명함수 사용
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
				= '<input type="hidden" name="'+descField.name+'" value="의약품아님"/>'
				+ '<input type="hidden" name="extra_info_inpk_type['+RegExp.$1+']" value="N"/>'
				+ "의약품아님(인터파크 정책에 의하여 의약품여부 항목은 수정하실수 없습니다.)";
			case "M1":
				break;
		}
		deleteField.innerHTML = "-";
	};

	/*
	 * 중분류 번호를 통해 상품군 지정 및 상품 필수 정보 셋팅
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
					Name : "AS가능여부",
					Group : "INFO",
					Code : "AS",
					Type : "R1",
					IsRequire : true,
					Description : "* AS정보를 입력하세요"
				});
			},
			onException : function(xhr, exception)
			{
				alert(exception.message);
				//alert("[시스템 에러]\r\n고객센터로 문의하여 주시기 바랍니다.");
			},
			onFailure : function(xhr, exception)
			{
				alert("[데이터 요청 에러]\r\n고객센터로 문의하여 주시기 바랍니다.");
			}
		});
	};
};