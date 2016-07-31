/*** INTERPARK : DISPLAY FORM OF ORDER (IDFO) ***/
IDFO = {
	use : "",
	inpk_ordno : "",
	inpk_regdt : "",

	display : function ()
	{
		if (this.use == 'Y'){
			this.killFunc(); // 인터파크와 무관한 기능 제거
			this.infoDisplay(); // 상품정보(상품번호 등) 출력
			this.itemDisplay(); // 주문상품별 인터파크 데이터 출력
			this.noteDisplay(); // 유의사항 출력
			this.claimReqDisplay();	// 인터파크 클레임요청 출력
			this.claimDisplay();	// 인터파크 클레임 출력
		}
	},

	killFunc : function ()
	{
		if (this.inpk_ordno == '') return;

		imgObj = document.frmOrder.getElementsByTagName('img');
		imgLen = imgObj.length - 1;
		for (i = imgLen; i >= 0; i--)
		{
			if (imgObj[i].src.match(/btn_cancelorder.gif|btn_exchangeorder.gif|btn_cancel_manual.gif|btn_delete_order.gif/)){
				imgObj[i].parentNode.removeChild(imgObj[i]);
			}
			else if (imgObj[i].src.match(/btn_cashreceipt_app.gif/)){
				imgObj[i].parentNode.parentNode.parentNode.parentNode.removeChild(imgObj[i].parentNode.parentNode.parentNode);
			}
		}
	},

	infoDisplay : function ()
	{
		if (this.inpk_ordno == '') return;
		obj = _ID('orderInfoBox');
		if(obj == null) return;

		div = document.createElement('DIV');
		div.className = 'def';
		div.style.marginTop = '2px';
		div.innerHTML = '인터파크 주문번호: ' + this.inpk_ordno + ', 등록일: ' + this.inpk_regdt;
		obj.appendChild(div);

		div = document.createElement('DIV');
		div.className = 'def';
		div.style.marginTop = '2px';
		div.innerHTML = '<font color=EA0095><b>이 주문은 인터파크로부터 접수된 주문입니다!</b></font>';
		obj.appendChild(div);
	},

	itemDisplay : function ()
	{
		if (this.inpk_ordno == '') return;
		var clsThis = this;
		var urlStr = "../interpark/indb.php?mode=getOrderItem&ordno=" + this.ordno + "&dummy=" + new Date().getTime();
		var ajax = new Ajax.Request( urlStr,
		{
			method: "get",
			onComplete:  function ()
			{
				var req = ajax.transport;
				if ( req.status == 200 )
				{
					var response = eval( '(' + req.responseText + ')' );
					var chkObj = document.getElementsByName('chk[]');
					var chkLen = chkObj.length;
					for (i = 0; i < chkLen; i++)
					{
						sno = chkObj[i].value;
						if (response[sno] != null)
						{
							gitem = response[sno];
							trObj = chkObj[i].parentNode.parentNode;
							if (gitem['inpk_compdt'] != '' && gitem['inpk_compdt'] != '0000-00-00') // 구매확정일표기
							{
								div = document.createElement('DIV');
								div.innerHTML = '<font class=small1 color=6d6d6d>인터파크 구매확정일 : ' + gitem['inpk_compdt'] + '</font>';
								trObj.cells[3].appendChild(div);
							}

							if (gitem['istep'] == '2' && gitem['outOfStock'] == 0) // 품절주문취소요청 버튼 출력
							{
								var d = trObj.cells[10].appendChild(document.createElement('DIV'));
								d.setAttribute('id', statId='orditem' + gitem['sno']);
								var ipt = document.createElement('input');
								ipt.setAttribute('type', 'button');
								var btn = d.appendChild(ipt);
								with (btn.style){
									font = "8pt Dotum";
									letterSpacing = "-1px";
									backgroundColor = "#E97D00";
									color = "white";
									border = 0;
									height = "15px";
									textAlign = 'center';
									padding = "2px 0 0 0";
								}
								btn.style.width = "66px";
								btn.value = '품절취소요청';
								btn['onclick'] = new Function('e', "popupLayer('../interpark/popup.openstyle_respCnclOutOfStock.php?ordno=" + gitem['ordno'] + "&sno=" + gitem['sno'] + "&statId=" + statId + "')");
							}
						}
					}
				}
				else {
					var msg = req.getResponseHeader("Status");
					if ( msg == null || msg.length == null || msg.length <= 0 )
						alert( "Error! Request status is " + req.status );
					else
						alert( msg );
				}
			}
		} );
	},

	noteDisplay : function ()
	{
		if (this.inpk_ordno == '') return;
		obj = document.frmOrder;
		if(obj == null) return;
		if (document.location.href.indexOf('_paper.php') !=-1) return;

		div = document.createElement('DIV');
		div.align = 'left';
		str = '<div style="padding:8px 13px;background:#f7f7f7;border:3px solid #C6C6C6;margin-bottom:18px;">';
    	str += '<div style="font-family:굴림;font-size:12px;padding:2 0 8 0"><b>※ 필독! 수정버튼 누르기전에 꼭 읽어보세요!</b></div>';
		str += '<div style="font-family:굴림;font-size:12px;padding-top:4">※ 주의 1 : 인터파크로부터 접수된 주문은 <font color=EA0095>이미 결제를 확인한 상태</font>입니다. 배송을 준비하세요.</div>';
		str += '<div style="font-family:굴림;font-size:12px;padding-top:4">※ 주의 2 : 주문상태를 <font color=EA0095>"배송중"으로 전환하면 </font> 인터파크 주문상태가 <font color=0074BA>출고완료(발송완료)</font>로 변경됩니다.</div>';
		str += '<div style="font-family:굴림;font-size:12px;padding-top:4; padding-left:62px">주문상태를 <font color=EA0095>"배송중"으로 전환 시</font> <font color=0074BA>송장번호,배송사</font>를 반드시 입력하셔야 합니다.</div>';
		str += '<div style="font-family:굴림;font-size:12px;padding-top:4">※ 주의 3 : 인터파크로 <font color=0074BA>"품절취소요청"을 하면</font> 인터파크에 등록된 <font color=EA0095>상품이 품절처리되어 이후 판매되지 않습니다.</font></div>';
		str += '<div style="font-family:굴림;font-size:12px;padding-top:4">※ 주의 4 : "배송중" 처리는 안하고 <font color=EA0095>물리적 배송만 한 상태에서 주문취소요청을 승인하면</font> 이후 배송 프로세스를 진행할 수 없게 됩니다.</div>';
		str += '<div style="font-family:굴림;font-size:12px;padding-top:4; padding-left:62px">이에 대해서는 판매자에게 책임이 전가되므로 <font color=0074BA>주문취소요청에 대해 승인하기전 배송여부를 반드시 체크</font>하시기 바랍니다.</font></div>';
		str += '<div style="font-family:굴림;font-size:12px;padding-top:4">※ 주의 5 : [영수증 발행 안내] 현금영수증은 인터파크에서 발행되며, <font color=0074BA>세금계산서는 업체(상점)별로 발행</font>됩니다. <a href="http://www.interpark.com/gate/html/HelpTaxAccount.html" target="_blank"><img src="../img/btn_detailsview.gif" align="absmiddle"></a></div>';
		str += '<div style="font-family:굴림;font-size:12px;padding-top:4">※ 주의 6 : 구매자가 인터파크에서 발행한 <font color=EA0095>쿠폰으로 주문했을 경우에</font> 판매자가 제시한 <font color=EA0095>판매가와 결제가격이 다를 수</font> 있습니다.</div>';
		str += '</div>';
		div.innerHTML = str;
		obj.appendChild(div);
	},

	claimReqDisplay : function ()
	{
		if (this.inpk_ordno == '') return;
		obj = _ID('interpark_claim');
		if(obj == null) return;

		var clsThis = this;
		var urlStr = "../interpark/indb.php?mode=getClaimReqList&ordno=" + this.ordno + "&dummy=" + new Date().getTime();
		var ajax = new Ajax.Request( urlStr,
		{
			method: "get",
			onComplete:  function ()
			{
				var req = ajax.transport;
				if ( req.status == 200 )
				{
					var response = eval( '(' + req.responseText + ')' );
					if (response.length)
					{
						div = document.createElement('DIV');
						div.style.marginBottom = '20px';
						obj.appendChild(div);

						str = '\
							<div class=title2>&nbsp;<img src="../img/icon_process.gif" align=absmiddle><font color=494949>인터파크 클레임요청내역</font> <font class=small1 color=6d6d6d>아래는 인터파크로부터 접수된 클레임요청(출고전주문취소/반품/교환) 내역입니다</font></div>\
							<table border=2 bordercolor=#F43400 style="border-collapse:collapse" width=100%><tr><td>\
							<table class=tb cellpadding=4 cellspacing=0 id="claimReq">\
							<tr>\
								<td width=5% align=center bgcolor=#F6F6F6><font class=small1 color=444444><b>번호</td>\
								<td width=13% align=center bgcolor=#F6F6F6><font class=small1 color=444444><b>클레임요청구분</td>\
								<td width=0% align=center bgcolor=#F6F6F6><font class=small1 color=444444><b>상품명</td>\
								<td width=9% align=center bgcolor=#F6F6F6><font class=small1 color=444444><b>요청수량</td>\
								<td width=13% align=center bgcolor=#F6F6F6><font class=small1 color=444444><b>요청사유</td>\
								<td width=8% align=center bgcolor=#F6F6F6><font class=small1 color=444444><b>요청일</td>\
								<td width=8% align=center bgcolor=#F6F6F6><font class=small1 color=444444><b>처리일</td>\
								<td width=16% align=center bgcolor=#F6F6F6><font class=small1 color=444444><b>요청상태</td>\
							</tr>\
							</table>\
							</td></tr></table>\
							';
						div.innerHTML = str;
						table_design_load();

						var tblObj = document.getElementById('claimReq');
						clsThis.listing(response, tblObj);
					}
				}
				else {
					var msg = req.getResponseHeader("Status");
					if ( msg == null || msg.length == null || msg.length <= 0 )
						alert( "Error! Request status is " + req.status );
					else
						alert( msg );
				}
			}
		} );
	},

	claimDisplay : function ()
	{
		if (this.inpk_ordno == '') return;
		obj = _ID('interpark_claim');
		if(obj == null) return;

		var clsThis = this;
		var urlStr = "../interpark/indb.php?mode=getClaimList&ordno=" + this.ordno + "&dummy=" + new Date().getTime();
		var ajax = new Ajax.Request( urlStr,
		{
			method: "get",
			onComplete:  function ()
			{
				var req = ajax.transport;
				if ( req.status == 200 )
				{
					var response = eval( '(' + req.responseText + ')' );
					if (response.length)
					{
						div = document.createElement('DIV');
						div.style.marginBottom = '20px';
						obj.appendChild(div);

						str = '\
							<div class=title2>&nbsp;<img src="../img/icon_process.gif" align=absmiddle><font color=494949>인터파크 클레임처리내역</font> <font class=small1 color=6d6d6d>아래는 인터파크로부터 접수된 클레임(입금후출고전취소/반품/고객교환) 내역입니다</font></div>\
							<table border=2 bordercolor=#F43400 style="border-collapse:collapse" width=100%><tr><td>\
							<table class=tb cellpadding=4 cellspacing=0 id="claim">\
							<tr>\
								<td width=5% align=center bgcolor=#F6F6F6><font class=small1 color=444444><b>번호</td>\
								<td width=13% align=center bgcolor=#F6F6F6><font class=small1 color=444444><b>클레임유형</td>\
								<td width=0% align=center bgcolor=#F6F6F6><font class=small1 color=444444><b>상품명</td>\
								<td width=9% align=center bgcolor=#F6F6F6><font class=small1 color=444444><b>클레임수량</td>\
								<td width=13% align=center bgcolor=#F6F6F6><font class=small1 color=444444><b>클레임사유</td>\
								<td width=8% align=center bgcolor=#F6F6F6><font class=small1 color=444444><b>접수일</td>\
								<td width=8% align=center bgcolor=#F6F6F6><font class=small1 color=444444><b>처리일</td>\
								<td width=16% align=center bgcolor=#F6F6F6><font class=small1 color=444444><b>처리상태</td>\
							</tr>\
							</table>\
							</td></tr></table>\
							';
						div.innerHTML = str;
						table_design_load();

						var tblObj = document.getElementById('claim');
						clsThis.listing(response, tblObj);
					}
				}
				else {
					var msg = req.getResponseHeader("Status");
					if ( msg == null || msg.length == null || msg.length <= 0 )
						alert( "Error! Request status is " + req.status );
					else
						alert( msg );
				}
			}
		} );
	},

	listing : function (response, tblObj)
	{
		var len = response.length;
		for ( n = 0; n < len; n++ )
		{
			l_row = response[n];
			ilen = l_row.item.length;

			newTr = tblObj.insertRow(-1);
			newTr.height='25';
			newTr.align='center';

			newTd = newTr.insertCell(-1);
			newTd.rowSpan = ilen;
			newTd.innerHTML = (len - n);

			newTd = newTr.insertCell(-1);
			newTd.rowSpan = ilen;
			newTd.className = 'small';
			newTd.innerHTML = l_row.clm_tpnm;


			for ( i = 0; i < ilen; i++ )
			{
				if (i > 0){
					newTr = tblObj.insertRow(-1);
					newTr.height='25';
					newTr.align='center';
				}

				i_row = l_row.item[i];

				newTd = newTr.insertCell(-1);
				newTd.className = 'small';
				newTd.innerHTML = i_row.goodsnm;
				newTd.align='left';

				newTd = newTr.insertCell(-1);
				newTd.className = 'small';
				newTd.innerHTML = i_row.clm_qty;

				newTd = newTr.insertCell(-1);
				newTd.className = 'small';
				newTd.innerHTML = i_row.clm_rsn_tpnm;
				if (i_row.clm_rsn_dtl)
				{
					newTd.style.cursor = 'default';
					newTd.innerHTML = '<font color="#0074BA">' + newTd.innerHTML + '</font>';
					{ // 상세사유 레이어
						d = newTd.insertBefore(document.createElement('DIV'), newTd.firstChild);
						d.style.position = 'relative';
						d.style.display = 'none';
						var d = d.appendChild(document.createElement('DIV'));
						d.innerHTML =i_row.clm_rsn_dtl;
						with (d.style) {
							position = 'absolute';
							backgroundColor = '#eeeeee';
							border = 'solid 1px #dddddd';
							filter = "Alpha(Opacity=90)";
							opacity = "0.9";
							padding = 5;
							left = 0;
							top = 10;
							width = '200px';
							textAlign = 'left';
						}
					}
					newTd['onmouseover'] = function(e) {
						if (this.getElementsByTagName('div').length){
							this.getElementsByTagName('div')[0].style.display='block';
						}
					};
					newTd['onmouseout'] = function(e) {
						if (this.getElementsByTagName('div').length){
							this.getElementsByTagName('div')[0].style.display='none';
						}
					};
				}

				newTd = newTr.insertCell(-1);
				newTd.className = 'small';
				newTd.innerHTML = i_row.clm_dt.substr(2,8);

				newTd = newTr.insertCell(-1);
				newTd.className = 'small';
				if (i_row.clm_statnm != '요청' && i_row.clm_statnm != '클레임접수'){
					newTd.innerHTML = i_row.latedt.substr(2,8);
				}
				newTd.innerHTML = '<a href="javascript:popupLayer(\'../interpark/popup.log.php?itmsno=' + i_row.itmsno + '\')"><font color="#0074BA"><u>' + newTd.innerHTML + '</u></font></a>';

				if (l_row.step == 'r' && l_row.clm_tpnm != '출고전주문취소' && i != 0);
				else {
					newTd = newTr.insertCell(-1);
					newTd.className = 'small';
					newTd.innerHTML = '<font color=0074BA><b>' + i_row.clm_statnm + '</b></font>';
				}

				if (l_row.step == 'r')
				{
					if (l_row.clm_tpnm != '출고전주문취소' && i == 0){
						newTd.rowSpan = ilen;
					}

					if (l_row.clm_tpnm != '출고전주문취소' && i != 0);
					else if (i_row.clm_statnm == '요청'){
						newTd.setAttribute('id', statId='clmReq' + n + '_' + i);
						var d = newTd.appendChild(document.createElement('DIV'));
						var ipt = document.createElement('input');
						ipt.setAttribute('type', 'button');
						var btn = d.appendChild(ipt);
						with (btn.style){
							font = "8pt Dotum";
							letterSpacing = "-1px";
							backgroundColor = "#E97D00";
							color = "white";
							border = 0;
							height = "15px";
							textAlign = 'center';
							padding = "2px 0 0 0";
						}
						if (l_row.clm_tpnm == '출고전주문취소'){
							btn.style.width = "66px";
							btn.value = '요청승인하기';
							btn['onclick'] = new Function('e', "popupLayer('../interpark/popup.openstyle_respClaimReqBeforeCancel.php?clmsno=" + i_row.clmsno + "&itmsno=" + i_row.itmsno + "&statId=" + statId + "',0,420)");
						}
						else {
							btn.style.width = "68px";
							btn.value = '승인/거절하기';
							btn['onclick'] = new Function('e', "popupLayer('../interpark/popup.openstyle_respClaimReqAfterCancel.php?clmsno=" + i_row.clmsno + "&statId=" + statId + "')");
						}
					}
				}
				else if (l_row.step == 'c')
				{
					if (i_row.clm_statnm == '반품/교환수거지시'){
						newTd.setAttribute('id', statId='clmReq' + n + '_' + i);
						var d = newTd.appendChild(document.createElement('DIV'));
						var ipt = document.createElement('input');
						ipt.setAttribute('type', 'button');
						var btn = d.appendChild(ipt);
						with (btn.style){
							font = "8pt Dotum";
							letterSpacing = "-1px";
							backgroundColor = "#E97D00";
							color = "white";
							border = 0;
							height = "15px";
							textAlign = 'center';
							padding = "2px 0 0 0";
						}
						btn.style.width = "89px";
						btn.value = '입고(회수)확정하기';
						btn['onclick'] = new Function('e', "popupLayer('../interpark/popup.openstyle_respClaimStoreComp.php?clmsno=" + i_row.clmsno + "&itmsno=" + i_row.itmsno + "&statId=" + statId + "')");
					}
					else if (i_row.clm_statnm == '교환/재배송출고지시'){
						newTd.setAttribute('id', statId='clmReq' + n + '_' + i);
						var d = newTd.appendChild(document.createElement('DIV'));
						var ipt = document.createElement('input');
						ipt.setAttribute('type', 'button');
						var btn = d.appendChild(ipt);
						with (btn.style){
							font = "8pt Dotum";
							letterSpacing = "-1px";
							backgroundColor = "#E97D00";
							color = "white";
							border = 0;
							height = "15px";
							textAlign = 'center';
							padding = "2px 0 0 0";
						}
						btn.style.width = "99px";
						btn.value = '교환(재배송)확정하기';
						btn['onclick'] = new Function('e', "popupLayer('../interpark/popup.openstyle_respExchangeComp.php?clmsno=" + i_row.clmsno + "&itmsno=" + i_row.itmsno + "&statId=" + statId + "')");
					}
				}

			} // end for
		} // end for
	}
}