/*** TAXSUGI CALCULATE METHOD (TCM) ***/
TCM = {
	init_set : function ()
	{
		this.fobj = document.forms['form'];
		this.fobj['SupComp'].value = param['compName'];
		this.fobj['SupEmployer'].value = param['ceoName'];
		this.fobj['SupNo'].value = param['compSerial'];
		this.fobj['SupCond'].value = param['service'];
		this.fobj['SupItem'].value = param['item'];
		this.fobj['SupEmail'].value = param['email'];
		this.fobj['SupAddr'].value = param['address'];
		this.fobj['SupPhone'].value = param['smsAdmin'];
		this.layout(this.fobj['TaxType'][0]);
	},

	layout: function (cobj)
	{
		if (cobj.value == 'VAT'){
			/** �����Ѿ�, ǰ�񺰼���, �հ�ݾ� ��� **/
			moaTot = taxTot = 0;
			moaTot = extUncomma(this.fobj.TotalMoa.value) * 1;
			for (j = 1; j <= 4; j++){
				if (this.fobj.chkCal[2].checked && this.fobj['LinTax'+j].value);
				else {
					moa = extUncomma(this.fobj['LinMoa'+j].value);
					if (moa != 0) this.fobj['LinTax'+j].value = extComma((Math.round(moa/10 - 0.5)) + "");
				}
				taxTot += extUncomma(this.fobj['LinTax'+j].value) * 1;
			}
			this.fobj.TotalTax.value = extComma(taxTot + "");
			if (taxTot != 0) this.fobj.MoaTax.value = extComma((moaTot+taxTot) + "");

			_ID('isTax').style.visibility="visible";
			for (j = 1; j <= 4; j++) _ID('isTax_l'+j).style.visibility="visible";
			_ID('display_title1').style.display = '';
			_ID('display_title2').style.display = 'none';
			_ID('display_title3').style.display = '';
			_ID('display_title4').style.display = 'none';
		}
		else if (cobj.value == 'FRE' || cobj.value == 'RCP'){
			if ( (this.fobj.TotalMoa.value * 1) == 0 ) this.fobj.MoaTax.value = "";
			else this.fobj.MoaTax.value = this.fobj.TotalMoa.value;

			this.fobj.TotalTax.value = "";
			for (j = 1; j <= 4; j++) this.fobj['LinTax'+j].value="";

			_ID('isTax').style.visibility="hidden";
			for (j = 1; j <= 4; j++) _ID('isTax_l'+j).style.visibility="hidden";
			if (cobj.value == 'FRE'){
				_ID('display_title1').style.display = '';
				_ID('display_title2').style.display = 'none';
				_ID('display_title3').style.display = '';
				_ID('display_title4').style.display = 'none';
			}
			else {
				_ID('display_title1').style.display = 'none';
				_ID('display_title2').style.display = '';
				_ID('display_title3').style.display = 'none';
				_ID('display_title4').style.display = '';
			}
		}
		for (j = 0; j < 4; j++) if (this.fobj.chkCal[j].checked) this.cal_state(this.fobj.chkCal[j].value, false);
	},

	cal_state: function (o_value, init)
	{
		if (o_value == 0){
			this.fobj.TotalMoa.readOnly = true;
			this.fobj.TotalTax.readOnly = true;
			this.fobj.t_price.readOnly = true;
			this.fobj.MoaTax.readOnly = true;

			for (j = 1; j <= 4; j++){
				this.fobj['LinQty'+j].readOnly = false;
				this.fobj['LinPri'+j].readOnly = false;
				this.fobj['LinMoa'+j].readOnly = true;
				this.fobj['LinTax'+j].readOnly = true;
			}
			if (init != false) this.ClearDoc();
			this.fobj.count_item.value=1;
			this.fobj.t_price.value="";
			this.fobj.LinQty1.focus();
		}
		else if (o_value == 1){
			this.fobj.TotalMoa.readOnly = true;
			this.fobj.TotalTax.readOnly = true;
			this.fobj.t_price.readOnly = true;
			this.fobj.MoaTax.readOnly = true;

			for (j = 1; j <= 4; j++){
				this.fobj['LinQty'+j].readOnly = true;
				this.fobj['LinPri'+j].readOnly = true;
				this.fobj['LinMoa'+j].readOnly = false;
				this.fobj['LinTax'+j].readOnly = true;
			}
			if (init != false) this.ClearDoc();
			this.fobj.count_item.value=1;
			this.fobj.t_price.value="";
			this.fobj.LinMoa1.focus();
		}
		else if (o_value == 2){
			this.fobj.TotalMoa.readOnly = true;
			this.fobj.TotalTax.readOnly = true;
			this.fobj.t_price.readOnly = false;
			this.fobj.MoaTax.readOnly = true;

			for (j = 1; j <= 4; j++){
				this.fobj['LinQty'+j].readOnly = true;
				this.fobj['LinPri'+j].readOnly = true;
				this.fobj['LinMoa'+j].readOnly = true;
				this.fobj['LinTax'+j].readOnly = true;
			}
			if (init != false) this.ClearDoc();
			this.fobj.count_item.value=1;
			this.fobj.t_price.value="";
			this.fobj.t_price.focus();
		}
		else if (o_value == 3){
			this.fobj.TotalMoa.readOnly = false;
			this.fobj.TotalTax.readOnly = false;
			this.fobj.t_price.readOnly = true;
			this.fobj.MoaTax.readOnly = false;

			for (j = 1; j <= 4; j++){
				this.fobj['LinQty'+j].readOnly = false;
				this.fobj['LinPri'+j].readOnly = false;
				this.fobj['LinMoa'+j].readOnly = false;
				this.fobj['LinTax'+j].readOnly = false;
			}
			if (init != false) this.ClearDoc();
			this.fobj.count_item.value=1;
			this.fobj.t_price.value="";
		}
	},

	ClearDoc: function ()
	{
		this.fobj.blankCnt.value = "10";
		this.fobj.TotalMoa.value = "0";
		this.fobj.TotalTax.value = "0";
		for (j = 1; j <= 4; j++ ){
			this.fobj['LinMonth'+j].value = "";
			this.fobj['LinDay'+j].value = "";
			this.fobj['LinItem'+j].value = "";
			this.fobj['LinUnit'+j].value = "";
			this.fobj['LinQty'+j].value = "";
			this.fobj['LinPri'+j].value = "";
			this.fobj['LinMoa'+j].value = "";
			this.fobj['LinTax'+j].value = "";
			this.fobj['LinRemark'+j].value = "";
		}
		this.fobj.MoaTax.value = "";
	},

	cal_Sum: function ()
	{
		var tmp_count = this.fobj.count_item.value;

		if (this.fobj.t_price.value=="") var msg = "�հ�ݾ��� �Է��ϼ���";
		else if (isNaN(this.fobj.t_price.value)) var msg = "�հ�ݾ��� ���ڷ� �Է��ؾ� �մϴ�.";
		else if (tmp_count>4) var msg = "4���� �׸� �Է� �����մϴ�.";

		if (msg != null){
			alert(msg);
			this.fobj.t_price.value="";
			this.fobj.t_price.focus();
			return;
		}

		if (this.fobj.TaxType[0].checked)
			this.fobj['LinMoa' + tmp_count].value = Math.round(parseInt(this.fobj.t_price.value , 10) / 11 * 10);
		else
			this.fobj['LinMoa' + tmp_count].value = this.fobj.t_price.value;
		this.calculate(this.fobj['LinMoa' + tmp_count], true);
		this.fobj.count_item.value=++tmp_count;
	},

	calculate: function (obj, calLinMoa)
	{
		/** name=���ް����Ѿ� : ���� ��� **/
		if (obj.name == 'TotalMoa') this.fobj.blankCnt.value = 11 - extUncomma(obj.value).length;

		obj.value = extComma(obj.value);
		if (obj.name.indexOf("Lin") >= 0 && obj.value == 0) obj.value = '';

		if (!(this.fobj.chkCal[0].checked || this.fobj.chkCal[1].checked || calLinMoa == true)) return;

		/** name=���ް��� : ����, ���ް����Ѿ�, �����Ѿ�, �հ�ݾ� ��� **/
		if (obj.name.indexOf("LinMoa") >= 0){
			totMoa = totTax = 0;

			/** ���� ���(���ݰ�꼭 ���) **/
			if (this.fobj.TaxType[0].checked){
				if (this.fobj.chkCal[2].checked)
					this.fobj["LinTax" + obj.name.substring(6, 7)].value = (obj.value == '' ? '' : extComma((this.fobj.t_price.value - Math.round((this.fobj.t_price.value / 11 *10)))+ ""));
				else
					this.fobj["LinTax" + obj.name.substring(6, 7)].value = (obj.value == '' ? '' : extComma((Math.round((extUncomma(obj.value) / 10) - 0.5))+ ""));
			}

			/** ���ް����Ѿ� ��� **/
			totMoa = extUncomma(this.fobj.LinMoa1.value) * 1;
			totMoa = totMoa + extUncomma(this.fobj.LinMoa2.value) * 1;
			totMoa = totMoa + extUncomma(this.fobj.LinMoa3.value) * 1;
			totMoa = totMoa + extUncomma(this.fobj.LinMoa4.value) * 1;

			totMoa = Math.round(totMoa - 0.5) ;
			this.fobj.TotalMoa.value = extComma(totMoa + "");
			this.fobj.blankCnt.value = 11 - (totMoa+"").length;

			/** �����Ѿ� ���(���ݰ�꼭 ���) **/
			if (this.fobj.TaxType[0].checked) {
				totTax = extUncomma(this.fobj.LinTax1.value) * 1;
				totTax = totTax + extUncomma(this.fobj.LinTax2.value) * 1;
				totTax = totTax + extUncomma(this.fobj.LinTax3.value) * 1;
				totTax = totTax + extUncomma(this.fobj.LinTax4.value) * 1;

				if (this.fobj.chkCal[2].checked)
					this.fobj.TotalTax.value = totTax;
				else
					this.fobj.TotalTax.value = extComma((Math.round((totMoa / 10) - 0.5)) + "");
			}

			/** �հ�ݾ� ��� **/
			if (this.fobj.TaxType[0].checked == this.fobj.chkCal[2].checked) this.fobj.MoaTax.value = extComma((totMoa + totTax) + "");
			else if (this.fobj.TaxType[0].checked) this.fobj.MoaTax.value = extComma((totMoa+(Math.round((totMoa / 10) - 0.5))) + "");
			else this.fobj.MoaTax.value = extComma((totMoa) + "");
			if (this.fobj.MoaTax.value == 0) this.fobj.MoaTax.value = '';
		}

		/** name=���� || name=�ܰ� : ���ް��� ��� **/
		if (obj.name.indexOf("LinQty") >= 0 || obj.name.indexOf("LinPri") >= 0){
			if ( obj.name.indexOf("LinQty") >= 0 ) nametmp = "LinPri" + obj.name.substring(6);
			else nametmp = "LinQty" + obj.name.substring(6);
			if (this.fobj[nametmp].value != ""){
				var nameMoa = "LinMoa" + obj.name.substring(6);
				d1 = extUncomma(obj.value, ".") * 1;
				d2 = extUncomma(this.fobj[nametmp].value, ".") * 1;
				this.fobj[nameMoa].value = d1 * d2;
				this.calculate(this.fobj[nameMoa]);
			}
		}
	},

	chkDate: function (obj, flag)
	{
		var indata = String(obj.value).replace(/(,)*/g,"");
		if (indata == '' || indata == '0') indata = '';
		else if (flag == 0 && (indata < 1 || indata > 12)) var msg = "���� �߸��Է��ϼ̽��ϴ�.";
		else if (indata < 1 || indata > 31) var msg = "���� �߸��Է��ϼ̽��ϴ�.";

		if (msg != null){
			alert("[�Է¿���] " + msg);
			obj.focus();
			return "";
		}
		else if(indata.toString().length==1) return "0" + indata;
		else return indata;
	}
}





/*** TAXSUGI TRANSMIT METHOD (TTM) ***/
TTM = {

	init_set : function ()
	{
		if (document.getElementById('avoidSubmit') && !document.getElementById('avoidMsg') )
		{
			sendDiv = document.getElementById('avoidSubmit');
			msgDiv = sendDiv.parentNode.insertBefore( sendDiv.cloneNode(true), sendDiv );
			msgDiv.id = 'avoidMsg';
			msgDiv.style.letterSpacing = '0px';
			msgDiv.innerHTML = '���ݰ�꼭 �����û ���Դϴ�. ��ø� ��ٷ��ּ���.';
		}
		sendDiv.style.display = 'none';
		msgDiv.style.display = 'block';
	},

	nowdate : function ()
	{
		var nowdate = new Date().getDate().toString();
		if (nowdate.length < 2) nowdate = '0' + nowdate;
		nowdate = (new Date().getMonth() + 1).toString() + nowdate;
		if (nowdate.length < 4) nowdate = '0' + nowdate;
		nowdate = new Date().getFullYear().toString() + nowdate;
		return nowdate;
	},

	chk : function (fobj)
	{
		/** ������ & ���޹޴��� üũ **/
		if (chkForm(fobj) === false) return false;

		/** �ۼ����� üũ **/
		if ((fobj.TaxYear.value + fobj.TaxMonth.value + fobj.TaxDay.value) > this.nowdate()){
			if (!confirm("�ۼ����ڰ� ���Ϻ��� Ů�ϴ� \n\n ��� �����Ͻ÷��� 'Ȯ��' ��ư�� �����ð� \n\n ������ �׸��ν÷��� '���' ��ư�� ��������! ")) return false;
		}

		/** ǰ�� üũ **/
		flag_1 = 0, flag_2 = 1;
		for (i = 1;i < 5;i++){
			month	= String(fobj["LinMonth"+i].value).replace(/(,)*/g,"") == 0 ? '' : String(fobj["LinMonth"+i].value).replace(/(,)*/g,"");
			day		= String(fobj["LinDay"+i].value).replace(/(,)*/g,"") == 0 ? '' : String(fobj["LinDay"+i].value).replace(/(,)*/g,"");
			itemnm	= fobj["LinItem"+i].value;
			unit	= fobj["LinUnit"+i].value;
			qty		= extUncomma(fobj["LinQty"+i].value) == 0 ? '' : extUncomma(fobj["LinQty"+i].value);
			pri		= extUncomma(fobj["LinPri"+i].value) == 0 ? '' : extUncomma(fobj["LinPri"+i].value);
			moa		= extUncomma(fobj["LinMoa"+i].value) == 0 ? '' : extUncomma(fobj["LinMoa"+i].value);
			tax		= extUncomma(fobj["LinTax"+i].value) == 0 ? '' : extUncomma(fobj["LinTax"+i].value);
			remark	= fobj["LinRemark"+i].value;
			lindata	= month + day + itemnm + unit + qty + pri + moa + tax + remark;

			if (itemnm.length > 0 && !chkPatten(fobj["LinItem"+i],'/^[��-�Ra-zA-Z0-9]+$/','Ư����ȣ�� &,>,<,%�� ǰ����� ����� �� �����ϴ�')) return false;
			if (remark.length > 0 && !chkPatten(fobj["LinRemark"+i],'/^[��-�Ra-zA-Z0-9]+$/','Ư����ȣ�� &,>,<,%�� ������ ����� �� �����ϴ�')) return false;

			if (lindata.length == 0){
				flag_2 = 0;
			}
			else {
				if	(flag_2 == 0) {
					alert("ǰ�������� ����� ���� ������� �����ϼž� �մϴ�.");
					fobj["LinMonth"+i].focus();
					return false;
				}
				flag_1 = 1;
				if (month.length == 0) {
					alert("������ ���� �ʼ��Դϴ�");
					fobj["LinMonth"+i].focus();
					return false;
				}
				else if ((month*1) != (fobj.TaxMonth.value*1)) {
					alert("�ۼ����� ������ ���� ���ƾ��մϴ�");
					fobj["LinMonth"+i].focus();
					return false;
				}
				else if (day.length == 0) {
					alert("������ ���� �ʼ��Դϴ�");
					fobj["LinDay"+i].focus();
					return false;
				}
				else if (itemnm.length == 0) {
					alert("������ ǰ����� �ʼ��Դϴ�");
					fobj["LinItem"+i].focus();
					return false;
				}
				else if (moa.length == 0) {
					alert("������ ���ް����� �ʼ��Դϴ�");
					fobj["LinMoa"+i].focus();
					return false;
				}
			}
		}
		if (flag_2 == 0 && flag_1 == 0) {
			alert("ǰ�������� �ϳ��̻� ������� �����ϼž� �մϴ�.");
			fobj.LinMonth1.focus();
			return false;
		}

		/** �հ�ݾ� üũ **/
		totMoa = extUncomma(fobj.LinMoa1.value) * 1;
		totMoa = totMoa + extUncomma(fobj.LinMoa2.value) * 1;
		totMoa = totMoa + extUncomma(fobj.LinMoa3.value) * 1;
		totMoa = totMoa + extUncomma(fobj.LinMoa4.value) * 1;

		totTax = extUncomma(fobj.LinTax1.value) * 1;
		totTax = totTax + extUncomma(fobj.LinTax2.value) * 1;
		totTax = totTax + extUncomma(fobj.LinTax3.value) * 1;
		totTax = totTax + extUncomma(fobj.LinTax4.value) * 1;

		if (fobj.MoaTax.value == ''){
			alert("�հ�ݾ��� �Է��ϼ���!");
			fobj.MoaTax.focus();
			return false;
		}
		else if (totMoa != extUncomma(fobj.TotalMoa.value)){
			totMoa = Math.round(totMoa - 0.5) ;
			if (totMoa != extUncomma(fobj.TotalMoa.value)){
				alert("���ް��� �հ�����Դϴ�!");
				fobj.TotalMoa.focus();
				return false;
			}
		}
		else if (totTax != extUncomma(fobj.TotalTax.value)){
			if (Math.round((totMoa / 10) - 0.5 -1)  <= extUncomma(fobj.TotalTax.value));
			else if (extUncomma(fobj.TotalTax.value) <= Math.round((totMoa / 10) - 0.5 + 1));
			else {
				alert("���� �հ�����Դϴ�!");
				fobj.TotalMoa.focus();
				return false;
			}
		}

		var totMoaTax = extUncomma(fobj.MoaTax.value);  // �հ�ݾ�
		var LiaPlus = extUncomma(fobj.TotalMoa.value)*1+extUncomma(fobj.TotalTax.value)*1; // ���ް����Ѿ� + �����Ѿ�
		if ( totMoaTax != LiaPlus ) {
			alert("�հ�ݾ� �հ�����Դϴ�!");
			if (fobj.TaxType[0].checked && fobj.chkCal[3].checked) fobj.MoaTax.focus();
			else fobj.TotalTax.focus();
			return false;
		}

		return true;
	},

	dispConf : function (fobj)
	{
		var msg = "";
		var taxType = "";

		if (fobj.TaxType[0].checked){
			taxType = "VAT";
			msg = "         === ���� ���ݰ�꼭 ==\n";
		}
		else if (fobj.TaxType[1].checked){
			taxType = "FRE";
			msg = "         === ���� ��꼭 ==\n";
		}
		else if (fobj.TaxType[2].checked){
			taxType = "RCP";
			msg = "         === ���� ������ ==\n";
		}

		msg = msg + "\n======================================";
		msg = msg + "\n  >> �� �� ��";
		msg = msg + "\n\t��Ϲ�ȣ : " + fobj.SupNo.value;
		msg = msg + "\n\t  ��  ȣ   : " + fobj.SupComp.value;

		msg = msg + "\n  >> ���޹޴���";
		msg = msg + "\n\t��Ϲ�ȣ : " + fobj.BuyNo.value;
		msg = msg + "\n\t  ��  ȣ   : " + fobj.BuyComp.value;

		msg = msg + "\n--------------------------------------";
		msg = msg + "\n   �ۼ����� : " + fobj.TaxYear.value + "/" + fobj.TaxMonth.value + "/" + fobj.TaxDay.value;
		if(taxType =="RCP")
			msg = msg + "\n   ���ޱݾ� : " + fobj.TotalMoa.value;
		else
			msg = msg + "\n   ���ް��� : " + fobj.TotalMoa.value;

		if (taxType =="VAT")
			msg = msg + "\n     ��  ��   : " + fobj.TotalTax.value;
		msg = msg + "\n--------------------------------------";
		msg = msg + "\n   �հ�ݾ� : " + fobj.MoaTax.value;
		msg = msg + "\n======================================              ";
		msg = msg + "\n\n�� �ۼ��� ������ Ȯ���Ͻñ� �ٶ��ϴ�.";
		msg = msg + "\n\n    �ۼ��� ������ �Է��Ͻðڽ��ϱ�?";

		if (!confirm(msg)){
	        alert ( " �����û�� ��ҵǾ����ϴ�...." );
	        return false;
	    }
	    return true;
	},

	register : function (fobj)
	{
		if (this.chk(fobj) === false) return false;
		if (this.dispConf(fobj) === false) return false;

		this.init_set();

		var urlStr = "../order/tax_indb.php?mode=putSugiTaxbill&dummy=" + new Date().getTime();
		var ajax = new Ajax.Request( urlStr,
		{
			method: "post",
			parameters: decodeURIComponent( Form.serialize(fobj) ),
			onComplete: function ()
			{
				var req = ajax.transport;
				if ( req.status == 200 )
				{
					msgDiv.innerHTML = req.responseText;
					parent.setTimeout('TLM.list()', 100);
					parent.closeLayer();
				}
				else {
					var msg = req.getResponseHeader("Status");
					if ( msg == null || msg.length == null || msg.length <= 0 ) msg = 'Error! Request status is ' + req.status;
					msgDiv.innerHTML = msg;
					sendDiv.style.display = 'block';
				}
			}
		} );
		return false;
	}
}





/*** TAXSUGI LIST METHOD (TLM) ***/
TLM = {

	init_set : function ()
	{
		/** Ŀ�� Ȱ��ȭ **/
		this.listcoverObj = document.getElementById('listcover');
		with (this.listcoverObj.style)
		{
			display = "block";
			border = "solid 1px blue";
			backgroundColor = "#000000";
			filter = "Alpha(Opacity=20)";
			opacity = "0.2";
			textAlign = "center";
		}

		/** ����ó�� : ������ **/
		if ( !document.all ) this.listcoverObj.parentNode.style.height = '';

		/** ������ �������� **/
		this.listingObj		= document.getElementById('listing');

		/** ����¡���� �������� **/
		this.pageObj		= new Array();
		this.pageObj['rtotal']	= document.getElementById('page_rtotal');
		this.pageObj['recode']	= document.getElementById('page_recode');
		this.pageObj['now']	= document.getElementById('page_now');
		this.pageObj['total']	= document.getElementById('page_total');
		this.pageObj['navi']	= document.getElementById('page_navi');
	},

	list : function (query)
	{
		this.init_set();

		// Create Query
		if ( query == undefined )
		{
			var tmp = new Array();
			var fObj = document.frmList;
			var eleLen = fObj.length;
			for ( i = 0; i < eleLen; i++ )
				if ( fObj[i].value != '' )
					tmp.push( fObj[i].name + "=" + fObj[i].value );
			var query = tmp.join("&");
		}

		// AJAX ����
		var urlStr = "../order/tax_indb.php?mode=getTaxsugiList&" + query + "&dummy=" + new Date().getTime();
		var ajax = new Ajax.Request( urlStr,
		{
			method: "get",
			onComplete: function()
			{
				var req = ajax.transport;
				if ( req.status == 200 )
				{
					var response = req.responseText;
					var jsonData = eval( '(' + response + ')' );
					TLM.list_init();

					// ������ ����
					try{
						TLM.listing( jsonData.lists );
					}
					catch(err)
					{
						TLM.list_msg( '<span style="color:#FF6600; font-weight:bold; font-size:10pt;">��û�Ͻ� ����� ���������� ��µ��� �ʾҽ��ϴ�. �ٽ� �õ��ϼ���.</span>' );
						TLM.listcoverObj.style.display = "none";
						return;
					}

					// ����¡���� ���
					try{
						for ( var n in TLM.pageObj )
							if ( TLM.pageObj[n] && n == 'navi' )
							{
								var navi = jsonData.page[n];
								var len = navi[0].length;
								var pageHtml = new Array();

								for ( i = 0; i < len; i++ )
									if ( navi[0][i] == '' ) // ���� ��������ȣ
										pageHtml.push( '<b>' + navi[1][i] + '</b>' );
									else  // �̵��� ��������ȣ
										pageHtml.push( '<a href="javascript:TLM.list(\'' + navi[0][i] + '\');">' + navi[1][i] + '</a>' );
								TLM.pageObj[n].innerHTML = pageHtml.join('&nbsp;');
							}
							else if ( TLM.pageObj[n] ) TLM.pageObj[n].innerHTML = comma(jsonData.page[n]);
					}
					catch(err){
						TLM.listcoverObj.style.display = "none";
						return;
					}
				}
				else {
					var msg = req.getResponseHeader("Status");
					if ( msg == null || msg.length == null || msg.length <= 0 ) msg = 'Error! Request status is ' + req.status;
					TLM.list_init();
					TLM.list_msg( '<span style="color:#FF6600; font-weight:bold; font-size:10pt;">' + msg + '</span>' );
				}

				TLM.listcoverObj.style.display = "none";
			}
		} );
	},

	/** ����Ʈ �ʱ�ȭ �Լ� **/
	list_init : function ()
	{
		if ( this.listingObj )
			while ( this.listingObj.rows.length > 2 ) this.listingObj.deleteRow( this.listingObj.rows.length - 1); // ��� rows �ʱ�ȭ

		for ( var n in this.pageObj )
			if ( this.pageObj[n] && n == 'navi' ) this.pageObj[n].innerHTML =' ';
			else if ( this.pageObj[n] ) this.pageObj[n].innerHTML = '0';
	},

	/** ������ �Լ� **/
	listing : function (lists)
	{
		var len = lists.length;
		for ( n = 0; n < len; n++ )
		{
			l_row = lists[n];

			// ù° ����
			newTr = this.listingObj.insertRow(-1);
			newTr.height = '25';
			newTr.align = 'center';

			newTd = newTr.insertCell(-1);
			newTd.rowSpan = 2;
			newTd.innerHTML = '<font class=ver81 color=444444>' + l_row.no + '</font>';

			newTd = newTr.insertCell(-1);
			newTd.colSpan = 3;
			newTd.align = 'left';
			newTd.style.padding = '5px 0 5px 7px';
			newTd.innerHTML = '<font class=small color=444444>\
				����ڹ�ȣ : ' + l_row.buy_regnum + '&nbsp;&nbsp;\
				ȸ��� : ' + l_row.buy_company + '<br>\
				��ǥ�ڸ� : ' + l_row.buy_employer + '&nbsp;&nbsp;\
				���� : ' + l_row.buy_bizcond + '&nbsp;&nbsp;\
				���� : ' + l_row.buy_bizitem + '<br>\
				������ּ� : ' + l_row.buy_address + '</font>';

			newTd = newTr.insertCell(-1);
			newTd.innerHTML = '<font class=ver8 color=444444>' + l_row.gen_tm + '</font>';

			newTd = newTr.insertCell(-1);
			newTd.innerHTML = '<font class=small color=444444>' + l_row.doc_number + '</font>';

			newTd = newTr.insertCell(-1);
			newTd.innerHTML = (l_row.mtsid != null ? '<font class=small color=444444>' + l_row.mtsid + '</font>' : '��');

			newTd = newTr.insertCell(-1);
			newTd.rowSpan = 2;
			newTd.style.lineHeight = '15pt';
			if (l_row.err_msg == null || l_row.status != 'ERR')
			{
				newTd.innerHTML = '<font color="#EA0095"><b>' + l_row.status_txt + '</b></font>';
				if (l_row.status == 'RDY' || l_row.status == 'SND' || l_row.status == 'RCV' || l_row.status == 'ACK'){
					newTd.innerHTML += '<div style="margin-top:5px;"><img src="../img/i_cancel.gif" style="cursor:pointer;" onclick="appCancel(this, \''+ l_row.status +'\', \''+ l_row.doc_number +'\')"></div>';
				}
			}
			else {
				newTd.innerHTML = '<a href="javascript:alert(\'' + l_row.err_msg + '\')"><font color="#EA0095"><b>' + l_row.status_txt + '</b></font></a>';
				newTd.title = l_row.err_msg;
			}

			// ��° ����
			newTr = this.listingObj.insertRow(-1);
			newTr.height = '25';
			newTr.align = 'center';

			newTd = newTr.insertCell(-1);
			newTd.align = 'left';
			newTd.style.padding = '5px 0 5px 7px';
			newTd.innerHTML = '<font class=small color=444444>' + l_row.item + '</font>';

			newTd = newTr.insertCell(-1);
			newTd.style.padding = '5px 0 5px 0px';
			newTd.innerHTML = '\
				<table width=92% border=0 cellspacing=0 cellpadding=0 style="line-height:15pt;">\
				<col width=44%>\
				<tr><td><font class=small color=444444>����� :</td><td style="text-align:right;"><font class=ver8 color=444444>' + extComma(l_row.pay_totalprice) + '</td></tr>\
				<tr><td><font class=small color=444444>���޾� :</td><td style="text-align:right;"><font class=ver8 color=444444>' + extComma(l_row.tax_supprice) + '</td></tr>\
				<tr><td><font class=small color=444444>�ΰ��� :</td><td style="text-align:right;"><font class=ver8 color=444444>' + extComma(l_row.tax_taxprice) + '</td></tr>\
				</table>\
				';

			newTd = newTr.insertCell(-1);
			newTd.innerHTML = l_row.tax_text;

			newTd = newTr.insertCell(-1);
			newTd.innerHTML = l_row.bill_text;

			newTd = newTr.insertCell(-1);
			newTd.innerHTML = '<font class=ver8 color=444444>' + l_row.sbm_tm + '</font>';

			newTd = newTr.insertCell(-1);
			newTd.innerHTML = (l_row.act_tm != null ? '<font class=small color=444444>' + l_row.act_tm.replace(/ /,'<br>') + '</font>' : '��');
		}
	},

	/** ������ �޽������ �Լ� **/
	list_msg : function (msg)
	{
		if ( this.listingObj == undefined ) return;

		newTr = this.listingObj.insertRow(-1);
		newTr.align='center';

		newTd = newTr.insertCell(-1);
		newTd.style.padding='20px 0 20px 0';
		newTd.colSpan = 12;
		newTd.innerHTML = msg;
	}
}





/** ������� **/
function appCancel(obj, status, doc_number)
{
	if (status == 'SND' || status == 'RCV'){
		var msg = '������ ����Ͻðڽ��ϱ�?';
	}
	else if (status == 'ACK'){
		var msg = '������ ��ҿ�û�Ͻðڽ��ϱ�?'+"\n"+'���޹޴��ڰ� ��� Ȯ���� �Ҷ����� �ð��� �ҿ�˴ϴ�.';
	}
	else {
		var msg = '����Ͻðڽ��ϱ�?';
	}
	if (confirm(msg) === false) return;

	var urlStr = "../order/tax_indb.php?mode=ccrTaxbill&doc_number=" + doc_number + "&dummy=" + new Date().getTime();
	var ajax = new Ajax.Request( urlStr,
	{
		method: "get",
		onComplete: function ()
		{
			var req = ajax.transport;
			if (req.status == 200)
			{
				obj.parentNode.parentNode.innerHTML = '<font color="#EA0095"><b>' + req.responseText.substr(4,2) + '</b></font>';
			}
			else {
				var msg = req.getResponseHeader("Status");
				alert(msg);
			}
		}
	} );
}