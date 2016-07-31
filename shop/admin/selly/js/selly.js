function ajaxGoods(goodsno) {
	var originVal = $('origin' + goodsno).value;
	var deliveryTypeVal = $('delivery_type' + goodsno).value;
	var deliveryPriceVal = $('delivery_price' + goodsno).value;
	var url = "./indb.ajax.php?goodsno=" + goodsno + "&origin=" + originVal + "&delivery_type=" + deliveryTypeVal + "&delivery_price=" + deliveryPriceVal;

	new Ajax.Request(url, {
		method: "get",
		onSuccess: function(transport) {
			var rtnFullStr = transport.responseText;
			var resMsg;

			if(!rtnFullStr) {
				resMsg = "오류..결과값이 없음..";
			}
			else {
				rtnStr = rtnFullStr.split("||");
				resMsg = rtnStr[1];
			}

			if(rtnStr[0] == "0") {
				$('logBoard' + goodsno).style.color = "#0033FF";
				$('logBoard' + goodsno).innerHTML = "등록일 : " + rtnStr[2] + " ";
				$('resBoard' + goodsno).innerHTML = " <span class=\"small\" style=\"color:#1D8E0D;\">" + resMsg + "</span>";
			}
			else {
				$('resBoard' + goodsno).innerHTML = " <span class=\"small\" style=\"color:#FF6C68;\">" + resMsg + "</span>";
			}
			
			return true;
		},
		OnError: function() {
			return false;
		}
	});
}

function ajaxMultiGoods() {
	var sendStatus = false;
	var chkList = document.getElementsByName("chk[]");

	$('STButton').style.display = "block";
	$('STButton').innerHTML = "처리중입니다.";

	for(i = 0; i < chkList.length; i++) {
		if(chkList[i].checked) {
			ajaxGoods(chkList[i].value);
			sendStatus = true;
		}
	}

	if(!sendStatus) {
		$('STButton').innerHTML = "<a href=\"javascript:ajaxMultiGoods();\"><img src=\"../img/btn_selly_input.gif\" title=\"일괄 전송\" /></a>";
		alert("일괄전송 하실 상품을 체크해주세요.");
	}
	else {
		$('STButton').innerHTML = "<a href=\"javascript:ajaxMultiGoods();\"><img src=\"../img/btn_selly_input.gif\" title=\"일괄 전송\" /></a>";
	}
}

function iciSelect(obj) {
	var row = obj.parentNode.parentNode;
	row.style.background = (obj.checked) ? "#F0FFD9" : "#FFFFFF";
}

function chkBoxAll(El, mode) {
	if(!El || !El.length) return;
	for(i = 0; i < El.length; i++) {
		if(El[i].disabled) continue;
		El[i].checked = (mode == 'rev') ? !El[i].checked : mode;
		iciSelect(El[i]);
	}
}

function sort(sort) {
	var fm = document.frmList;
	fm.sort.value = sort;
	fm.submit();
}

function sort_chk(sort) {
	if(!sort) return;
	sort = sort.replace(" ","_");
	var obj = document.getElementsByName('sort_' + sort);
	if(obj.length) {
		div = obj[0].src.split('list_');
		for(i = 0; i < obj.length; i++) {
			chg = (div[1] == "up_off.gif") ? "up_on.gif" : "down_on.gif";
			obj[i].src = div[0] + "list_" + chg;
		}
	}
}

var tmpobj;
function STdivOpener(idVal, obj) {
	tmpobj = obj;
	divLayer = document.getElementById(idVal);

	STdivCloser("STdivOrigin");
	STdivCloser("STdivDeliveryType");

	divLayer.style.visibility = "hidden";
	divLayer.style.display = "none";

	divLayer.style.top = event.clientY + document.body.scrollTop;
	divLayer.style.left = event.clientX + document.body.scrollLeft;

	divLayer.style.visibility = "visible";
	divLayer.style.display = "block";
}

function STdivCloser(idVal) {
	divCloseLayer = document.getElementById(idVal);

	divCloseLayer.style.visibility = "hidden";
	divCloseLayer.style.display = "none";
}

function STinsertOrigin() {
	tmpSel = document.getElementById('originInsert');
	rtnKey = tmpSel.options[tmpSel.selectedIndex].value;
	rtnVal = tmpSel.options[tmpSel.selectedIndex].text;

	if(!rtnKey) alert("원산지를 선택해주세요.");
	else {
		tmpobj.value = rtnVal;
		STdivCloser('STdivOrigin');
	}
}

function STinsertDeliveryType() {
	tmpSel = document.getElementById('delivery_typeInsert');
	rtnKey = tmpSel.options[tmpSel.selectedIndex].value;
	rtnVal = tmpSel.options[tmpSel.selectedIndex].text;

	if(!rtnKey) alert("배송정책을 선택해주세요.");
	else {
		tmpobj.value = rtnVal;
		STdivCloser('STdivDeliveryType');
	}
}

// varibale, value, period day
function SetCookie(cKey, cValue, cPeriod) {
	var date = new Date();

	date.setDate(date.getDate() + cPeriod);
	document.cookie = cKey + '=' + escape(cValue) + ';expires=' + date.toGMTString();
}

function createOption(obj, data) {
	var json_data = eval( '(' + data + ')' );
	var arr_data = json_data;
	var cate_type = arr_data[0]['category_type'];
	var idx;

	if(cate_type == 'L') idx = 0;
	if(cate_type == 'M') idx = 1;
	if(cate_type == 'S') idx = 2;
	if(cate_type == 'D') idx = 3;

	if(idx > 0 && !arr_data[0]['category_nm'] && !arr_data[0]['category_cd']) {
		document.getElementsByName('last_cate')[0].value = 'Y';
	}
	else {
		document.getElementsByName('last_cate')[0].value = 'N';
	}

	for(var k = idx; k < 4; k++) {
		var tmp_opt_length = obj[k].options.length;
		for(var o = 0; o < tmp_opt_length; o++) {	
			obj[k].remove(0);
		}
		obj[k].options[0] = new Option('= ' + (k+1) + '차분류 =', '');
	}

	for(var i = 0; i < arr_data.length; i++) {
		if(arr_data[i]['category_nm'] && arr_data[i]['category_nm'] != '' && arr_data[i]['category_nm'] != null) {
			obj[idx].options[i+1] = new Option(arr_data[i]['category_nm'], arr_data[i]['category_cd']);
		}
	}
}

var sellyLink =  {
	linkGoods: function(mall_cd, set_cd, mall_login_id, mall_category_cd, mall_category_nm, goods_no, price, delivery_price) {
		var param_arr = [
			{"key":"mode","value":"linkgoods"},
			{"key":"mall_cd","value":mall_cd},
			{"key":"set_cd","value":set_cd},
			{"key":"mall_login_id","value":mall_login_id},
			{"key":"mall_category_cd","value":mall_category_cd},
			{"key":"mall_category_nm","value":mall_category_nm},
			{"key":"goods_no","value":goods_no},
			{"key":"price","value":price},
			{"key":"delivery_price","value":delivery_price}
		];
		this.connectSellyAPI(param_arr);
	},
	scrapOrder: function(minfo_idx, scrap_order_status) {
		var param_arr = [
			{"key":"mode","value":"scraporder"},
			{"key":"minfo_idx","value":minfo_idx},
			{"key":"scrap_order_status","value":scrap_order_status}
		];
		
		this.connectSellyAPI(param_arr);
	},
	sendOrder: function(order_idx, send_status) {
		var param_arr = [
			{"key":"mode","value":"sendorder"},
			{"key":"order_idx","value":order_idx},
			{"key":"send_status","value":send_status}
		];

		this.connectSellyAPI(param_arr);
	},
	ajaxMallCategory: function(obj, mall_cd, mall_login_id, category_type, category_cd) {
		if(!category_type) {
			document.getElementsByName('last_cate')[0].value = 'Y';
			return;
		}
		var param_arr = [
			{"key":"mode","value":"getcategory"},
			{"key":"mall_cd","value":mall_cd},
			{"key":"mall_login_id","value":mall_login_id},
			{"key":"category_type","value":category_type},
			{"key":"category_cd","value":category_cd}
		];
		this.connectSellyAPI(param_arr);
	},
	linkModifyGoods: function(glink_idx, price, delivery_price) {
		var param_arr = [
			{"key":"mode","value":"linkmidifygoods"},
			{"key":"glink_idx","value":glink_idx},
			{"key":"price","value":price},
			{"key":"delivery_price","value":delivery_price}
		];
		this.connectSellyAPI(param_arr);
	},
	linkGoodsStatus: function(glink_idx, sale_status) {
		var param_arr = [
			{"key":"mode","value":"linkgoodsstatus"},
			{"key":"glink_idx","value":glink_idx},
			{"key":"sale_status","value":sale_status}
		];
		this.connectSellyAPI(param_arr);
	},
	linkGoodsExtend: function(glink_idx, extend_term, extend_set, sale_term_start, sale_term_end, mall_cd) {

		var param_arr = [
			{"key":"mode","value":"linkgoodsextend"},
			{"key":"glink_idx","value":glink_idx},
			{"key":"extend_term","value":extend_term},
			{"key":"extend_set","value":extend_set},
			{"key":"sale_term_start","value":sale_term_start},
			{"key":"sale_term_end","value":sale_term_end},
			{"key":"mall_cd","value":mall_cd}
		];
		this.connectSellyAPI(param_arr);
	},
	checkMallLogin: function(mall_cd, mall_login_id, mall_login_pwd, security_ticket, api_id) {
		var param_arr = [
			{"key":"mode","value":"checkmalllogin"},
			{"key":"mall_cd","value":mall_cd},
			{"key":"mall_login_id","value":mall_login_id},
			{"key":"mall_login_pwd","value":mall_login_pwd},
			{"key":"security_ticket","value":security_ticket},
			{"key":"api_id","value":api_id}
		];
		this.connectSellyAPI(param_arr);
	},
	insMall: function(mall_cd, mall_login_id, mall_login_pwd, security_ticket, status, type, minfo_idx, api_id, store_address) {
		var param_arr = [
			{"key":"mode","value":"insmall"},
			{"key":"mall_cd","value":mall_cd},
			{"key":"mall_login_id","value":mall_login_id},
			{"key":"mall_login_pwd","value":mall_login_pwd},
			{"key":"security_ticket","value":security_ticket},
			{"key":"status","value":status},
			{"key":"type","value":type},
			{"key":"minfo_idx","value":minfo_idx},
			{"key":"api_id","value":api_id},
			{"key":"store_address","value":store_address}
		];
		this.connectSellyAPI(param_arr);
	},
	setDelete: function(set_cd) {
		var param_arr = [
			{"key":"mode","value":"setdelete"},
			{"key":"set_cd","value":set_cd}
		];
		this.connectSellyAPI(param_arr);
	},
	connectSellyAPI: function(param_arr) {
		var api_url = "./sellyApiProc.php";
		var param_data = "?";
		for (var i=0; i<param_arr.length; i++) {
			param_data += "&" + param_arr[i].key + "=" + param_arr[i].value;
		}
		
		var api_ajax = new Ajax.Request(api_url, {
			method: 'post',
			parameters: param_data,
			onSuccess: function(req) {
				
				var response = req.responseText;
				
				successAjax(response);
			},
			OnError: function() {
				return false;
			}
		});
	}
}

function sellyPop(ret_url) {
	
	var api_url = "./indb.php";
	var param_data = "?";
	param_data += "&mode=selly_domain";
	
	ret_url = encodeURIComponent(ret_url);
	var selly_domain = "";

	var api_ajax = new Ajax.Request(api_url, {
		method: 'post',
		parameters: param_data,
		async: true,
		onSuccess: function (req) {
			var response = req.responseText;
			selly_domain = "http://"+response;
			window.open(selly_domain + "/STLoginProcShop.gm?ret_url="+ret_url, "","width=915,height=520,scrollbars=yes");
		},
		onError: function() {
			return false;
		}
	});

	
}