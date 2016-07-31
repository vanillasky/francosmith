
function Request(boxdiv){

	var ajax = new Ajax.Request(
		"divmove_table_proc.php?boxdiv="+boxdiv ,
		{
		method : 'get',
		onComplete : setResponse
		}
	);

}

function setResponse(req){
	var re_ajax = req.responseText;
	document.getElementById('setResponseID').value = 'y';
	//startMove();
	//alert("값"+re_ajax);
}

//----- table 이동 start kwons 2007-02-20 ----- //

var xx= '';
var tmp = 5;
function move(tget_ID,move_ID,uppx,downpx,ud_type)
{

	var obj_up = document.getElementById(tget_ID);
	var obj_down = document.getElementById(move_ID);




	if( obj_up.style.top == "0pt" || obj_down.style.top == "0pt" ){
		if( obj_up.style.top == "0pt" ) obj_up_Size = 0;
		if( obj_down.style.top == "0pt" ) obj_down_Size = 0;
	}else{
		var obj_up_Size = eval(obj_up.style.top.replace(/px/gi,''));
		var obj_down_Size = eval(obj_down.style.top.replace(/px/gi,''));
	}

	if( ud_type == "up" ){	
		if( obj_up_Size < eval(downpx) ){ obj_up.style.top = obj_up_Size + eval(tmp);	}
		if( obj_down_Size > eval(uppx) ){ obj_down.style.top =  obj_down_Size - eval(tmp);}

	}else{
		if( obj_up_Size > eval(downpx) ){ obj_up.style.top = obj_up_Size - eval(tmp);}
		if( obj_down_Size < eval(uppx) ){ obj_down.style.top = obj_down_Size + eval(tmp);}
	}

	
}

function loop(obj){
	move(obj[0],obj[1],obj[2],obj[3],obj[4]);
}

var obj_array = new Array();

function startMove(tget_ID,move_ID,uppx,downpx,ud_type){
	
	obj_array[0] = tget_ID;
	obj_array[1] = move_ID;
	obj_array[2] = uppx.replace(/pt/gi,'');
	obj_array[3] = downpx.replace(/pt/gi,'');
	obj_array[4] = ud_type;

	var appname = navigator.appName.charAt(0);
	if( appname == "M" ){ 
		xx = window.setInterval("loop(obj_array)",20);
	}
	else{ 
		xx = window.setInterval(loop,20,obj_array);
	}

}

function stopMove(){
	window.clearInterval(xx);
}


var boxdiv = new Array();

function Targetpoint(tget,type){
	//초기화!!
	window.clearInterval(xx);

	if( document.getElementById('setResponseID').value != 'y' ){
	var Cookie_val = document.getElementById('Getboxdiv').value;
		if( Cookie_val ){
			var Cookie_val_Arr = Cookie_val.split(',');
			var Cookie_val_Cnt = Cookie_val_Arr.length;
			for( c = 0; c < Cookie_val_Cnt; c++ ){
				boxdiv[c] = Cookie_val_Arr[c];
			}
		}
	}

	if (boxdiv.length == 0)
	{
		var divs = document.getElementById('box').childNodes;
		for ( var i = 0; i < divs.length; i++ )
		{
			if ( divs[i].tagName == 'DIV')
			{
			boxdiv.push( divs[i].id  );
			}
		}
	}

	var tget_ID = document.getElementById("move_" + tget);

	var pixID='';
	for ( var i = 0; i < boxdiv.length; i++ )
	{
		if ( boxdiv[i] == tget_ID.id)
		{
			if( type == "up" ){
				if ( i > 0 )
				{
					pixID = eval( boxdiv[i-1] );
					boxdiv[i] = boxdiv[i-1];
					boxdiv[i-1] = tget_ID.id;
				}
				else {
					pixID='';
				}
			}else if( type == "down" ){
				if ( i != boxdiv.length - 1 )
				{
					pixID = eval( boxdiv[i+1] );
					boxdiv[i] = boxdiv[i+1];
					boxdiv[i+1] = tget_ID.id;
				}
				else {
					pixID='';
				}
			}
			break;
		}
	}

	if( type == "up" ) var err_type = "down";
	else var err_type = "up";

	if( !pixID ) err_refunc(tget,err_type);
	else{
		var move_ID = pixID;	
		var uppx = tget_ID.style.top.replace(/px/gi,'');
		var downpx = pixID.style.top.replace(/px/gi,'');
		Request(boxdiv);
		startMove(tget_ID.id,move_ID.id,uppx,downpx,err_type);
	}

}

function err_refunc(tget,type){
	Targetpoint(tget,type);
}


function table_tg(){
	var Cookie_val = document.getElementById('Getboxdiv').value;
	var thisID = '';
	//alert(Cookie_val);
	if( Cookie_val ){
		var Cookie_val_Arr = Cookie_val.split(',');
		var Cookie_val_Cnt = Cookie_val_Arr.length;
		for( c = 0; c < Cookie_val_Cnt; c++ ){
			thisID = document.getElementById(Cookie_val_Arr[c]);
//			var thisSize = eval(obj_up.style.top.replace(/px/gi,''));
//			thisID.style.pixelTop = c * 160;
			thisID.style.top = c * 160;
		}
	}
}




//----- table 이동 start kwons 2007-02-20 ----- //