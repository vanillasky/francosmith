NowMainDisplay = {
	inData : function(sno)
	{
		var ajax = new Ajax.Request(
		"./main.state.php?dummy="+new Date().getTime(),
			{
			method : 'get',
			onComplete : this.outData
			}
		);
	},
	
	outData : function(req)
	{
		//alert(req.responseText);   //json���� �迭�� �� Ȯ�ν�
		var re_data = eval( '(' + req.responseText + ')' );

		document.getElementById('Main_State_DisplayID').innerHTML = re_data.returnForm;
	}
}