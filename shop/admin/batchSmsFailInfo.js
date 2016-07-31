function setSmsFailInfo(form)
{
	var condition = false;
	var func = document.getElementsByName('func');
	for(var i=0; i<func.length; i++){
		if(func[i].checked == true && func[i].value == 'sms'){
			condition = true;
			break;
		}
	}

	if(condition == true){
		var type = document.getElementsByName('type')[0];
		var chknum = 0;
		mNo = new Array();

		if (type.value == 'select'){
			var checkbox = document.getElementsByName('chk[]');
			if (checkbox.length > 0){
				for (var i=0; i<checkbox.length; i++){
					if (checkbox[i].checked == true) {
						mNo[i] = checkbox[i].value;
						chknum++;
					}
				}
			}

			if(mNo.length > 0){
				getSmsFailInfo('select', mNo.join("|"), chknum);
			}
			else {
				getSmsFailInfo('reset', '', '');
			}
		}
		else if(type.value == 'query'){
			getSmsFailInfo('query', '', document.failListForm.total.value);
		}
	}
}

function getSmsFailInfo(mode, m_no, total)
{
	if(mode == 'reset'){
		smsFailInfoDisplay('none', 'none', 'none');
		document.getElementById("includeFail1").disabled = true;
		document.getElementById("includeFail2").disabled = true;
	}
	else if (mode == 'select' || mode == 'query'){
		var ajax = new Ajax.Request("./smsFailNumberInfo.php",
		{
			method: "post",
			parameters: "mode=" + mode + "&m_no=" + m_no + "&query=" + encodeURIComponent(document.fmList.query.value),
			onComplete: function (req)
			{
				returnVal = new Array();
				returnVal = req.responseText.split(",");
				var smsFailCnt = returnVal[0];
				var smsFailSnoList = returnVal[1];
				var errorType = returnVal[2];

				if(smsFailCnt > 0){
					if(total == 1){
						smsFailInfoDisplay('block', 'block', 'none');
						document.getElementById("smsFailListInfoErrorType").innerHTML = errorType;
						document.getElementById("includeFail1").disabled = true;
						document.getElementById("includeFail2").disabled = true;
					}
					else if(total > 1){
						smsFailInfoDisplay('block', 'none', 'block');
						document.getElementById("smsFailListInfoCnt").innerHTML = smsFailCnt;
						document.failListForm.smsFailSnoList.value = smsFailSnoList;
						document.fmList.smsFailSnoList.value = smsFailSnoList;
						document.getElementById("includeFail1").disabled = false;
						document.getElementById("includeFail2").disabled = false;
					}
				}
				else {
					getSmsFailInfo('reset', '', '');
				}
			}
		});
	}
}

function smsFailInfoDisplay(val1, val2, val3)
{
	var info1 = document.getElementById("smsFailListInfo1");
	var info2 = document.getElementById("smsFailListInfo2");
	var info3 = document.getElementById("smsFailListInfo3");

	info1.style.display = val1;
	info2.style.display = val2;
	info3.style.display = val3;
}