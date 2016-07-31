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
			/** ¼¼¾×ÃÑ¾×, Ç°¸ñº°¼¼¾×, ÇÕ°è±Ý¾× °è»ê **/
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

		if (this.fobj.t_price.value=="") var msg = "ÇÕ°è±Ý¾×À» ÀÔ·ÂÇÏ¼¼¿ä";
		else if (isNaN(this.fobj.t_price.value)) var msg = "ÇÕ°è±Ý¾×Àº ¼ýÀÚ·Î ÀÔ·ÂÇØ¾ß ÇÕ´Ï´Ù.";
		else if (tmp_count>4) var msg = "4°³ÀÇ Ç×¸ñ¸¸ ÀÔ·Â °¡´ÉÇÕ´Ï´Ù.";

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
		/** name=°ø±Þ°¡¾×ÃÑ¾× : °ø¶õ °è»ê **/
		if (obj.name == 'TotalMoa') this.fobj.blankCnt.value = 11 - extUncomma(obj.value).length;

		obj.value = extComma(obj.value);
		if (obj.name.indexOf("Lin") >= 0 && obj.value == 0) obj.value = '';

		if (!(this.fobj.chkCal[0].checked || this.fobj.chkCal[1].checked || calLinMoa == true)) return;

		/** name=°ø±Þ°¡¾× : ¼¼¾×, °ø±Þ°¡¾×ÃÑ¾×, ¼¼¾×ÃÑ¾×, ÇÕ°è±Ý¾× °è»ê **/
		if (obj.name.indexOf("LinMoa") >= 0){
			totMoa = totTax = 0;

			/** ¼¼¾× °è»ê(¼¼±Ý°è»ê¼­ °æ¿ì) **/
			if (this.fobj.TaxType[0].checked){
				if (this.fobj.chkCal[2].checked)
					this.fobj["LinTax" + obj.name.substring(6, 7)].value = (obj.value == '' ? '' : extComma((this.fobj.t_price.value - Math.round((this.fobj.t_price.value / 11 *10)))+ ""));
				else
					this.fobj["LinTax" + obj.name.substring(6, 7)].value = (obj.value == '' ? '' : extComma((Math.round((extUncomma(obj.value) / 10) - 0.5))+ ""));
			}

			/** °ø±Þ°¡¾×ÃÑ¾× °è»ê **/
			totMoa = extUncomma(this.fobj.LinMoa1.value) * 1;
			totMoa = totMoa + extUncomma(this.fobj.LinMoa2.value) * 1;
			totMoa = totMoa + extUncomma(this.fobj.LinMoa3.value) * 1;
			totMoa = totMoa + extUncomma(this.fobj.LinMoa4.value) * 1;

			totMoa = Math.round(totMoa - 0.5) ;
			this.fobj.TotalMoa.value = extComma(totMoa + "");
			this.fobj.blankCnt.value = 11 - (totMoa+"").length;

			/** ¼¼¾×ÃÑ¾× °è»ê(¼¼±Ý°è»ê¼­ °æ¿ì) **/
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

			/** ÇÕ°è±Ý¾× °è»ê **/
			if (this.fobj.TaxType[0].checked == this.fobj.chkCal[2].checked) this.fobj.MoaTax.value = extComma((totMoa + totTax) + "");
			else if (this.fobj.TaxType[0].checked) this.fobj.MoaTax.value = extComma((totMoa+(Math.round((totMoa / 10) - 0.5))) + "");
			else this.fobj.MoaTax.value = extComma((totMoa) + "");
			if (this.fobj.MoaTax.value == 0) this.fobj.MoaTax.value = '';
		}

		/** name=¼ö·® || name=´Ü°¡ : °ø±Þ°¡¾× °è»ê **/
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
		else if (flag == 0 && (indata < 1 || indata > 12)) var msg = "¿ùÀ» Àß¸øÀÔ·ÂÇÏ¼Ì½À´Ï´Ù.";
		else if (indata < 1 || indata > 31) var msg = "ÀÏÀ» Àß¸øÀÔ·ÂÇÏ¼Ì½À´Ï´Ù.";

		if (msg != null){
			alert("[ÀÔ·Â¿À·ù] " + msg);
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
			msgDiv.innerHTML = '¼¼±Ý°è»ê¼­ ¹ßÇà¿äÃ» ÁßÀÔ´Ï´Ù. Àá½Ã¸¸ ±â´Ù·ÁÁÖ¼¼¿ä.';
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
		/** °ø±ÞÀÚ & °ø±Þ¹Þ´ÂÀÚ Ã¼Å© **/
		if (chkForm(fobj) === false) return false;

		/** ÀÛ¼ºÀÏÀÚ Ã¼Å© **/
		if ((fobj.TaxYear.value + fobj.TaxMonth.value + fobj.TaxDay.value) > this.nowdate()){
			if (!confirm("ÀÛ¼ºÀÏÀÚ°¡ ±ÝÀÏº¸´Ù Å®´Ï´Ù \n\n °è¼Ó ÁøÇàÇÏ½Ã·Á¸é 'È®ÀÎ' ¹öÆ°À» ´©¸£½Ã°í \n\n ÁøÇàÀ» ±×¸¸µÎ½Ã·Á¸é 'Ãë¼Ò' ¹öÆ°À» ´©¸£¼¼¿ä! ")) return false;
		}

		/** Ç°¸ñ Ã¼Å© **/
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

			if (itemnm.length > 0 && !chkPatten(fobj["LinItem"+i],'/^[°¡-ÆRa-zA-Z0-9]+$/','Æ¯¼ö±âÈ£¸¦ &,>,<,%Àº Ç°¸ñ¶õ¿¡ »ç¿ëÇÒ ¼ö ¾ø½À´Ï´Ù')) return false;
			if (remark.length > 0 && !chkPatten(fobj["LinRemark"+i],'/^[°¡-ÆRa-zA-Z0-9]+$/','Æ¯¼ö±âÈ£¸¦ &,>,<,%Àº ºñ°í¶õ¿¡ »ç¿ëÇÒ ¼ö ¾ø½À´Ï´Ù')) return false;

			if (lindata.length == 0){
				flag_2 = 0;
			}
			else {
				if	(flag_2 == 0) {
					alert("Ç°¸ñÁ¤º¸´Â ºó¶óÀÎ ¾øÀÌ ¼ø¼­´ë·Î ±âÀçÇÏ¼Å¾ß ÇÕ´Ï´Ù.");
					fobj["LinMonth"+i].focus();
					return false;
				}
				flag_1 = 1;
				if (month.length == 0) {
					alert("¶óÀÎÀÇ ¿ùÀº ÇÊ¼öÀÔ´Ï´Ù");
					fobj["LinMonth"+i].focus();
					return false;
				}
				else if ((month*1) != (fobj.TaxMonth.value*1)) {
					alert("ÀÛ¼º¿ù°ú ¶óÀÎÀÇ ¿ùÀº °°¾Æ¾ßÇÕ´Ï´Ù");
					fobj["LinMonth"+i].focus();
					return false;
				}
				else if (day.length == 0) {
					alert("¶óÀÎÀÇ ÀÏÀº ÇÊ¼öÀÔ´Ï´Ù");
					fobj["LinDay"+i].focus();
					return false;
				}
				else if (itemnm.length == 0) {
					alert("¶óÀÎÀÇ Ç°¸ñ¸íÀº ÇÊ¼öÀÔ´Ï´Ù");
					fobj["LinItem"+i].focus();
					return false;
				}
				else if (moa.length == 0) {
					alert("¶óÀÎÀÇ °ø±Þ°¡¾×Àº ÇÊ¼öÀÔ´Ï´Ù");
					fobj["LinMoa"+i].focus();
					return false;
				}
			}
		}
		if (flag_2 == 0 && flag_1 == 0) {
			alert("Ç°¸ñÁ¤º¸´Â ÇÏ³ªÀÌ»ó ¼ø¼­´ë·Î ±âÀçÇÏ¼Å¾ß ÇÕ´Ï´Ù.");
			fobj.LinMonth1.focus();
			return false;
		}

		/** ÇÕ°è±Ý¾× Ã¼Å© **/
		totMoa = extUncomma(fobj.LinMoa1.value) * 1;
		totMoa = totMoa + extUncomma(fobj.LinMoa2.value) * 1;
		totMoa = totMoa + extUncomma(fobj.LinMoa3.value) * 1;
		totMoa = totMoa + extUncomma(fobj.LinMoa4.value) * 1;

		totTax = extUncomma(fobj.LinTax1.value) * 1;
		totTax = totTax + extUncomma(fobj.LinTax2.value) * 1;
		totTax = totTax + extUncomma(fobj.LinTax3.value) * 1;
		totTax = totTax + extUncomma(fobj.LinTax4.value) * 1;

		if (fobj.MoaTax.value == ''){
			alert("ÇÕ°è±Ý¾×À» ÀÔ·ÂÇÏ¼¼¿ä!");
			fobj.MoaTax.focus();
			return false;
		}
		else if (totMoa != extUncomma(fobj.TotalMoa.value)){
			totMoa = Math.round(totMoa - 0.5) ;
			if (totMoa != extUncomma(fobj.TotalMoa.value)){
				alert("°ø±Þ°¡¾× ÇÕ°è¿À·ùÀÔ´Ï´Ù!");
				fobj.TotalMoa.focus();
				return false;
			}
		}
		else if (totTax != extUncomma(fobj.TotalTax.value)){
			if (Math.round((totMoa / 10) - 0.5 -1)  <= extUncomma(fobj.TotalTax.value));
			else if (extUncomma(fobj.TotalTax.value) <= Math.round((totMoa / 10) - 0.5 + 1));
			else {
				alert("¼¼¾× ÇÕ°è¿À·ùÀÔ´Ï´Ù!");
				fobj.TotalMoa.focus();
				return false;
			}
		}

		var totMoaTax = extUncomma(fobj.MoaTax.value);  // ÇÕ°è±Ý¾×
		var LiaPlus = extUncomma(fobj.TotalMoa.value)*1+extUncomma(fobj.TotalTax.value)*1; // °ø±Þ°¡¾×ÃÑ¾× + ¼¼¾×ÃÑ¾×
		if ( totMoaTax != LiaPlus ) {
			alert("ÇÕ°è±Ý¾× ÇÕ°è¿À·ùÀÔ´Ï´Ù!");
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
			msg = "         === ¸ÅÃâ ¼¼±Ý°è»ê¼­ ==\n";
		}
		else if (fobj.TaxType[1].checked){
			taxType = "FRE";
			msg = "         === ¸ÅÃâ °è»ê¼­ ==\n";
		}
		else if (fobj.TaxType[2].checked){
			taxType = "RCP";
			msg = "         === ¸ÅÃâ ¿µ¼öÁõ ==\n";
		}

		msg = msg + "\n======================================";
		msg = msg + "\n  >> °ø ±Þ ÀÚ";
		msg = msg + "\n\tµî·Ï¹øÈ£ : " + fobj.SupNo.value;
		msg = msg + "\n\t  »ó  È£   : " + fobj.SupComp.value;

		msg = msg + "\n  >> °ø±Þ¹Þ´ÂÀÚ";
		msg = msg + "\n\tµî·Ï¹øÈ£ : " + fobj.BuyNo.value;
		msg = msg + "\n\t  »ó  È£   : " + fobj.BuyComp.value;

		msg = msg + "\n--------------------------------------";
		msg = msg + "\n   ÀÛ¼ºÀÏÀÚ : " + fobj.TaxYear.value + "/" + fobj.TaxMonth.value + "/" + fobj.TaxDay.value;
		if(taxType =="RCP")
			msg = msg + "\n   °ø±Þ±Ý¾× : " + fobj.TotalMoa.value;
		else
			msg = msg + "\n   °ø±Þ°¡¾× : " + fobj.TotalMoa.value;

		if (taxType =="VAT")
			msg = msg + "\n     ¼¼  ¾×   : " + fobj.TotalTax.value;
		msg = msg + "\n--------------------------------------";
		msg = msg + "\n   ÇÕ°è±Ý¾× : " + fobj.MoaTax.value;
		msg = msg + "\n======================================              ";
		msg = msg + "\n\n¢¼ ÀÛ¼ºÇÑ ³»¿ªÀ» È®ÀÎÇÏ½Ã±â ¹Ù¶ø´Ï´Ù.";
		msg = msg + "\n\n    ÀÛ¼ºÇÑ ³»¿ëÀ» ÀÔ·ÂÇÏ½Ã°Ú½À´Ï±î?";

		if (!confirm(msg)){
	        alert ( " ¹ßÇà¿äÃ»ÀÌ Ãë¼ÒµÇ¾ú½À´Ï´Ù...." );
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
		/** Ä¿¹ö È°¼ºÈ­ **/
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

		/** ¿¹¿ÜÃ³¸® : ¸ðÁú¶ó **/
		if ( !document.all ) this.listcoverObj.parentNode.style.height = '';

		/** ¸®½ºÆÃ ¿µ¿ªÁ¤ÀÇ **/
		this.listingObj		= document.getElementById('listing');

		/** ÆäÀÌÂ¡Á¤º¸ ¿µ¿ªÁ¤ÀÇ **/
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

		// AJAX ½ÇÇà
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

					// ¸®½ºÆÃ ½ÇÇà
					try{
						TLM.listing( jsonData.lists );
					}
					catch(err)
					{
						TLM.list_msg( '<span style="color:#FF6600; font-weight:bold; font-size:10pt;">¿äÃ»ÇÏ½Å ¸ñ·ÏÀÌ Á¤»óÀûÀ¸·Î Ãâ·ÂµÇÁö ¾Ê¾Ò½À´Ï´Ù. ´Ù½Ã ½ÃµµÇÏ¼¼¿ä.</span>' );
						TLM.listcoverObj.style.display = "none";
						return;
					}

					// ÆäÀÌÂ¡Á¤º¸ Ãâ·Â
					try{
						for ( var n in TLM.pageObj )
							if ( TLM.pageObj[n] && n == 'navi' )
							{
								var navi = jsonData.page[n];
								var len = navi[0].length;
								var pageHtml = new Array();

								for ( i = 0; i < len; i++ )
									if ( navi[0][i] == '' ) // ÇöÀç ÆäÀÌÁö¹øÈ£
										pageHtml.push( '<b>' + navi[1][i] + '</b>' );
									else  // ÀÌµ¿ÇÒ ÆäÀÌÁö¹øÈ£
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

	/** ¸®½ºÆ® ÃÊ±âÈ­ ÇÔ¼ö **/
	list_init : function ()
	{
		if ( this.listingObj )
			while ( this.listingObj.rows.length > 2 ) this.listingObj.deleteRow( this.listingObj.rows.length - 1); // °á°ú rows ÃÊ±âÈ­

		for ( var n in this.pageObj )
			if ( this.pageObj[n] && n == 'navi' ) this.pageObj[n].innerHTML =' ';
			else if ( this.pageObj[n] ) this.pageObj[n].innerHTML = '0';
	},

	/** ¸®½ºÆÃ ÇÔ¼ö **/
	listing : function (lists)
	{
		var len = lists.length;
		for ( n = 0; n < len; n++ )
		{
			l_row = lists[n];

			// Ã¹Â° ¶óÀÎ
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
				»ç¾÷ÀÚ¹øÈ£ : ' + l_row.buy_regnum + '&nbsp;&nbsp;\
				È¸»ç¸í : ' + l_row.buy_company + '<br>\
				´ëÇ¥ÀÚ¸í : ' + l_row.buy_employer + '&nbsp;&nbsp;\
				¾÷ÅÂ : ' + l_row.buy_bizcond + '&nbsp;&nbsp;\
				Á¾¸ñ : ' + l_row.buy_bizitem + '<br>\
				»ç¾÷ÀåÁÖ¼Ò : ' + l_row.buy_address + '</font>';

			newTd = newTr.insertCell(-1);
			newTd.innerHTML = '<font class=ver8 color=444444>' + l_row.gen_tm + '</font>';

			newTd = newTr.insertCell(-1);
			newTd.innerHTML = '<font class=small color=444444>' + l_row.doc_number + '</font>';

			newTd = newTr.insertCell(-1);
			newTd.innerHTML = (l_row.mtsid != null ? '<font class=small color=444444>' + l_row.mtsid + '</font>' : '¡ª');

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

			// µÑÂ° ¶óÀÎ
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
				<tr><td><font class=small color=444444>¹ßÇà¾× :</td><td style="text-align:right;"><font class=ver8 color=444444>' + extComma(l_row.pay_totalprice) + '</td></tr>\
				<tr><td><font class=small color=444444>°ø±Þ¾× :</td><td style="text-align:right;"><font class=ver8 color=444444>' + extComma(l_row.tax_supprice) + '</td></tr>\
				<tr><td><font class=small color=444444>ºÎ°¡¼¼ :</td><td style="text-align:right;"><font class=ver8 color=444444>' + extComma(l_row.tax_taxprice) + '</td></tr>\
				</table>\
				';

			newTd = newTr.insertCell(-1);
			newTd.innerHTML = l_row.tax_text;

			newTd = newTr.insertCell(-1);
			newTd.innerHTML = l_row.bill_text;

			newTd = newTr.insertCell(-1);
			newTd.innerHTML = '<font class=ver8 color=444444>' + l_row.sbm_tm + '</font>';

			newTd = newTr.insertCell(-1);
			newTd.innerHTML = (l_row.act_tm != null ? '<font class=small color=444444>' + l_row.act_tm.replace(/ /,'<br>') + '</font>' : '¡ª');
		}
	},

	/** ¸®½ºÆÃ ¸Þ½ÃÁöÃâ·Â ÇÔ¼ö **/
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





/** ¹ßÇàÃë¼Ò **/
function appCancel(obj, status, doc_number)
{
	if (status == 'SND' || status == 'RCV'){
		var msg = '¹ßÇàÀ» Ãë¼ÒÇÏ½Ã°Ú½À´Ï±î?';
	}
	else if (status == 'ACK'){
		var msg = '½ÂÀÎÀ» Ãë¼Ò¿äÃ»ÇÏ½Ã°Ú½À´Ï±î?'+"\n"+'°ø±Þ¹Þ´ÂÀÚ°¡ Ãë¼Ò È®ÀÎÀ» ÇÒ¶§±îÁö ½Ã°£ÀÌ ¼Ò¿äµË´Ï´Ù.';
	}
	else {
		var msg = 'Ãë¼ÒÇÏ½Ã°Ú½À´Ï±î?';
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