function ajaxGoods(goodsno) {
	var url = "./indb.php?goodsno=" + goodsno;
	$('singleButton' + goodsno + "_a").blur();
	if($('singleButton' + goodsno).src == window.location.protocol + "//" + window.location.host + "/shop/admin/img/loading20.gif") return;
	$('singleButton' + goodsno).src = "../img/loading20.gif";

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
				$('resBoard' + goodsno).innerHTML = " <span class=\"small\" style=\"color:#1D8E0D;\">" + resMsg + "</span> <a href=\"./waitlist.php\"><img src=\"../img/btn_link.gif\" align=\"absmiddle\" /></a>";
			}
			else {
				$('resBoard' + goodsno).innerHTML = " <span class=\"small\" style=\"color:#FF6C68;\">" + resMsg + "</span>";
			}

			$('singleButton' + goodsno).src = "../img/btn_openmarket_indiregist.gif";
			$('chk' + goodsno).checked = false;
			iciSelect($('chk' + goodsno));

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

	for(i = 0; i < chkList.length; i++) {
		if(chkList[i].checked) {
			ajaxGoods(chkList[i].value);
			sendStatus = true;
		}
	}

	if(!sendStatus) alert("일괄전송 하실 상품을 체크해주세요.");
}

function iciSelect(obj) {
	var row = obj.parentNode.parentNode;
	row.style.background = (obj.checked) ? "#F0FFD9" : "#FFFFFF";
}

function chkBoxAll(allObj, El) {
	if(!El || !El.length) return;

	for(i = 0; i < El.length; i++) {
		if(El[i].disabled) continue;
		else El[i].checked = allObj.checked;
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

// varibale, value, period day
function SetCookie(cKey, cValue, cPeriod) {
	var date = new Date();

	date.setDate(date.getDate() + cPeriod);
	document.cookie = cKey + '=' + escape(cValue) + ';expires=' + date.toGMTString();
}
