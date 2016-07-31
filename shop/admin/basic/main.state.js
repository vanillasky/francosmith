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
		//alert(req.responseText);   //json으로 배열된 값 확인시
		var re_data = eval( '(' + req.responseText + ')' );

		document.getElementById('Main_State_DisplayID').innerHTML = re_data.returnForm;
	}
}