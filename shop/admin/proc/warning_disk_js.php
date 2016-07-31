function file_disabled(){
	var f_len = document.forms.length;

	for ( f = 0; f < f_len; f++  ){
		var e_len = document.forms[f].length;

		for ( e = 0; e < e_len; e++  ){
			if ( document.forms[f][e].type != 'file' ) continue;
			document.forms[f][e].parentNode.disabled = true;
			document.forms[f][e].disabled = true;
		}
	}
}


if ( typeof(call_file_disabled) == "undefined" );
else file_disabled();