/*** INTERPARK : DISPLAY FORM OF ORDER (IDFO) ***/
IDFO = {
	use : "",
	inpk_ordno : "",
	inpk_regdt : "",

	display : function ()
	{
		if (this.use == 'Y'){
			this.killFunc(); // ������ũ�� ������ ��� ����
			this.infoDisplay(); // ��ǰ����(��ǰ��ȣ ��) ���
			this.itemDisplay(); // �ֹ���ǰ�� ������ũ ������ ���
			this.noteDisplay(); // ���ǻ��� ���
			this.claimReqDisplay();	// ������ũ Ŭ���ӿ�û ���
			this.claimDisplay();	// ������ũ Ŭ���� ���
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
		div.innerHTML = '������ũ �ֹ���ȣ: ' + this.inpk_ordno + ', �����: ' + this.inpk_regdt;
		obj.appendChild(div);

		div = document.createElement('DIV');
		div.className = 'def';
		div.style.marginTop = '2px';
		div.innerHTML = '<font color=EA0095><b>�� �ֹ��� ������ũ�κ��� ������ �ֹ��Դϴ�!</b></font>';
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
							if (gitem['inpk_compdt'] != '' && gitem['inpk_compdt'] != '0000-00-00') // ����Ȯ����ǥ��
							{
								div = document.createElement('DIV');
								div.innerHTML = '<font class=small1 color=6d6d6d>������ũ ����Ȯ���� : ' + gitem['inpk_compdt'] + '</font>';
								trObj.cells[3].appendChild(div);
							}

							if (gitem['istep'] == '2' && gitem['outOfStock'] == 0) // ǰ���ֹ���ҿ�û ��ư ���
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
								btn.value = 'ǰ����ҿ�û';
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
    	str += '<div style="font-family:����;font-size:12px;padding:2 0 8 0"><b>�� �ʵ�! ������ư ���������� �� �о����!</b></div>';
		str += '<div style="font-family:����;font-size:12px;padding-top:4">�� ���� 1 : ������ũ�κ��� ������ �ֹ��� <font color=EA0095>�̹� ������ Ȯ���� ����</font>�Դϴ�. ����� �غ��ϼ���.</div>';
		str += '<div style="font-family:����;font-size:12px;padding-top:4">�� ���� 2 : �ֹ����¸� <font color=EA0095>"�����"���� ��ȯ�ϸ� </font> ������ũ �ֹ����°� <font color=0074BA>���Ϸ�(�߼ۿϷ�)</font>�� ����˴ϴ�.</div>';
		str += '<div style="font-family:����;font-size:12px;padding-top:4; padding-left:62px">�ֹ����¸� <font color=EA0095>"�����"���� ��ȯ ��</font> <font color=0074BA>�����ȣ,��ۻ�</font>�� �ݵ�� �Է��ϼž� �մϴ�.</div>';
		str += '<div style="font-family:����;font-size:12px;padding-top:4">�� ���� 3 : ������ũ�� <font color=0074BA>"ǰ����ҿ�û"�� �ϸ�</font> ������ũ�� ��ϵ� <font color=EA0095>��ǰ�� ǰ��ó���Ǿ� ���� �Ǹŵ��� �ʽ��ϴ�.</font></div>';
		str += '<div style="font-family:����;font-size:12px;padding-top:4">�� ���� 4 : "�����" ó���� ���ϰ� <font color=EA0095>������ ��۸� �� ���¿��� �ֹ���ҿ�û�� �����ϸ�</font> ���� ��� ���μ����� ������ �� ���� �˴ϴ�.</div>';
		str += '<div style="font-family:����;font-size:12px;padding-top:4; padding-left:62px">�̿� ���ؼ��� �Ǹ��ڿ��� å���� �����ǹǷ� <font color=0074BA>�ֹ���ҿ�û�� ���� �����ϱ��� ��ۿ��θ� �ݵ�� üũ</font>�Ͻñ� �ٶ��ϴ�.</font></div>';
		str += '<div style="font-family:����;font-size:12px;padding-top:4">�� ���� 5 : [������ ���� �ȳ�] ���ݿ������� ������ũ���� ����Ǹ�, <font color=0074BA>���ݰ�꼭�� ��ü(����)���� ����</font>�˴ϴ�. <a href="http://www.interpark.com/gate/html/HelpTaxAccount.html" target="_blank"><img src="../img/btn_detailsview.gif" align="absmiddle"></a></div>';
		str += '<div style="font-family:����;font-size:12px;padding-top:4">�� ���� 6 : �����ڰ� ������ũ���� ������ <font color=EA0095>�������� �ֹ����� ��쿡</font> �Ǹ��ڰ� ������ <font color=EA0095>�ǸŰ��� ���������� �ٸ� ��</font> �ֽ��ϴ�.</div>';
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
							<div class=title2>&nbsp;<img src="../img/icon_process.gif" align=absmiddle><font color=494949>������ũ Ŭ���ӿ�û����</font> <font class=small1 color=6d6d6d>�Ʒ��� ������ũ�κ��� ������ Ŭ���ӿ�û(������ֹ����/��ǰ/��ȯ) �����Դϴ�</font></div>\
							<table border=2 bordercolor=#F43400 style="border-collapse:collapse" width=100%><tr><td>\
							<table class=tb cellpadding=4 cellspacing=0 id="claimReq">\
							<tr>\
								<td width=5% align=center bgcolor=#F6F6F6><font class=small1 color=444444><b>��ȣ</td>\
								<td width=13% align=center bgcolor=#F6F6F6><font class=small1 color=444444><b>Ŭ���ӿ�û����</td>\
								<td width=0% align=center bgcolor=#F6F6F6><font class=small1 color=444444><b>��ǰ��</td>\
								<td width=9% align=center bgcolor=#F6F6F6><font class=small1 color=444444><b>��û����</td>\
								<td width=13% align=center bgcolor=#F6F6F6><font class=small1 color=444444><b>��û����</td>\
								<td width=8% align=center bgcolor=#F6F6F6><font class=small1 color=444444><b>��û��</td>\
								<td width=8% align=center bgcolor=#F6F6F6><font class=small1 color=444444><b>ó����</td>\
								<td width=16% align=center bgcolor=#F6F6F6><font class=small1 color=444444><b>��û����</td>\
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
							<div class=title2>&nbsp;<img src="../img/icon_process.gif" align=absmiddle><font color=494949>������ũ Ŭ����ó������</font> <font class=small1 color=6d6d6d>�Ʒ��� ������ũ�κ��� ������ Ŭ����(�Ա�����������/��ǰ/����ȯ) �����Դϴ�</font></div>\
							<table border=2 bordercolor=#F43400 style="border-collapse:collapse" width=100%><tr><td>\
							<table class=tb cellpadding=4 cellspacing=0 id="claim">\
							<tr>\
								<td width=5% align=center bgcolor=#F6F6F6><font class=small1 color=444444><b>��ȣ</td>\
								<td width=13% align=center bgcolor=#F6F6F6><font class=small1 color=444444><b>Ŭ��������</td>\
								<td width=0% align=center bgcolor=#F6F6F6><font class=small1 color=444444><b>��ǰ��</td>\
								<td width=9% align=center bgcolor=#F6F6F6><font class=small1 color=444444><b>Ŭ���Ӽ���</td>\
								<td width=13% align=center bgcolor=#F6F6F6><font class=small1 color=444444><b>Ŭ���ӻ���</td>\
								<td width=8% align=center bgcolor=#F6F6F6><font class=small1 color=444444><b>������</td>\
								<td width=8% align=center bgcolor=#F6F6F6><font class=small1 color=444444><b>ó����</td>\
								<td width=16% align=center bgcolor=#F6F6F6><font class=small1 color=444444><b>ó������</td>\
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
					{ // �󼼻��� ���̾�
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
				if (i_row.clm_statnm != '��û' && i_row.clm_statnm != 'Ŭ��������'){
					newTd.innerHTML = i_row.latedt.substr(2,8);
				}
				newTd.innerHTML = '<a href="javascript:popupLayer(\'../interpark/popup.log.php?itmsno=' + i_row.itmsno + '\')"><font color="#0074BA"><u>' + newTd.innerHTML + '</u></font></a>';

				if (l_row.step == 'r' && l_row.clm_tpnm != '������ֹ����' && i != 0);
				else {
					newTd = newTr.insertCell(-1);
					newTd.className = 'small';
					newTd.innerHTML = '<font color=0074BA><b>' + i_row.clm_statnm + '</b></font>';
				}

				if (l_row.step == 'r')
				{
					if (l_row.clm_tpnm != '������ֹ����' && i == 0){
						newTd.rowSpan = ilen;
					}

					if (l_row.clm_tpnm != '������ֹ����' && i != 0);
					else if (i_row.clm_statnm == '��û'){
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
						if (l_row.clm_tpnm == '������ֹ����'){
							btn.style.width = "66px";
							btn.value = '��û�����ϱ�';
							btn['onclick'] = new Function('e', "popupLayer('../interpark/popup.openstyle_respClaimReqBeforeCancel.php?clmsno=" + i_row.clmsno + "&itmsno=" + i_row.itmsno + "&statId=" + statId + "',0,420)");
						}
						else {
							btn.style.width = "68px";
							btn.value = '����/�����ϱ�';
							btn['onclick'] = new Function('e', "popupLayer('../interpark/popup.openstyle_respClaimReqAfterCancel.php?clmsno=" + i_row.clmsno + "&statId=" + statId + "')");
						}
					}
				}
				else if (l_row.step == 'c')
				{
					if (i_row.clm_statnm == '��ǰ/��ȯ��������'){
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
						btn.value = '�԰�(ȸ��)Ȯ���ϱ�';
						btn['onclick'] = new Function('e', "popupLayer('../interpark/popup.openstyle_respClaimStoreComp.php?clmsno=" + i_row.clmsno + "&itmsno=" + i_row.itmsno + "&statId=" + statId + "')");
					}
					else if (i_row.clm_statnm == '��ȯ/�����������'){
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
						btn.value = '��ȯ(����)Ȯ���ϱ�';
						btn['onclick'] = new Function('e', "popupLayer('../interpark/popup.openstyle_respExchangeComp.php?clmsno=" + i_row.clmsno + "&itmsno=" + i_row.itmsno + "&statId=" + statId + "')");
					}
				}

			} // end for
		} // end for
	}
}