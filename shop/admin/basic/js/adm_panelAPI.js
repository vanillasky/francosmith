/**
 * 고도서버와 API 통신
 * @author bumyul2000
 * @date 2014-04-08, 2014-04-08
 * @param {String} type
 */
function adm_panelAPI( type, selector, limit )
{
	if ( type == null || selector == null) return;
	jQuery.ajax({
		url		:  "../proc/adm_panel_API.php",
		type	: "post",
		async	: true,
		data	: { type: type, limit: limit } ,
		success	: function ( jsonData ) {
			var obj = eval("("+jsonData+")");
			var html = "";

			switch( type )
			{
				case 'panelAPI' : 
					jQuery.each(obj, function(i, data){
						if( jQuery("#panel_" + data.panelCode ) ) jQuery("#panel_" + data.panelCode ).html( data.panelData );
					});
				break;
	
				default :
					jQuery.each(obj, function(i, data){
						html += "<li><a href=" + data.board_link + " target=\"_blank\">" + strCut(data.board_title, 60) + " </a><span>" + data.board_date + "</span></li>";
						jQuery( selector ).html( html );
					});
				break;
			}
		}
	});
}