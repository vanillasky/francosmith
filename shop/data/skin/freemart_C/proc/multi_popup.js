/*!
 * 멀티 팝업 출력 관련 스크립트
 * @author cjb3333 , artherot @ godosoft development team.
 */
var _path;

function multiPopup(_skin)
{
	_path	=  '../data/skin/' + _skin ;
	gd_ajax({
		url : '../proc/multipopup_data.php',
		success : function(rst)
		{
			try
			{
				var multiPopup	= eval('('+rst+')');

				if (multiPopup)
				{
					showPopup(multiPopup)
					return;
				}
			}
			catch (e)
			{}
		}
	});
}

function showPopup(data)
{
	for(var i =0; i< data.length; i++)
	{
		getContent(data[i]);
	}
}

function getContent(data)
{
	if(!data.popup_sizew) data.popup_sizew = 0;
	if(!data.popup_sizeh) data.popup_sizeh = 0;
	if(!data.popup_spotw) data.popup_spotw = 0;
	if(!data.popup_spoth) data.popup_spoth = 0;
	if(!data.popup_type) data.popup_type = 'window';

	if(data.popup_type == 'layer' && !getCookie('mlpCookie_' + data.code))
	{
		popupLayer(data);
	}
	else if(data.popup_type == 'layerMove' && !getCookie('mlpCookie_' + data.code))
	{
		popupLayerMove(data);
	}
	else if(data.popup_type == 'window' && !getCookie('mlpCookie_' + data.code))
	{
		winOpen(data);
	}
}

function layerContent(data)
{
	var _content = '';
	_content += '<TABLE cellpadding="0" cellspacing="0" border="0">';
	_content += '<TR>';
	_content += 	'<TD style="background: URL(' + _path + '/img/main/popup_top_left.gif) no-repeat;" nowrap width="12" height="33"></TD>';
	_content += 	'<TD style="background: URL(' + _path + '/img/main/popup_top_bg.gif) repeat-x;" align="right"><img src="' + _path + '/img/main/popup_bu_close.gif" onclick="_ID(\'mlpCookie_' + data.code + '\').style.display=\'none\'" style="cursor:pointer; display:block; user-drag: none; -moz-user-select: none; -webkit-user-drag: none;" ondragstart="return false" /></TD>';
	_content += 	'<TD style="background: URL(' + _path + '/img/main/popup_top_right.gif) no-repeat;" nowrap width="12" height="33"></TD>';
	_content += '</TR>';
	_content += '<TR>';
	_content += 	'<TD style="background: URL(' + _path + '/img/main/popup_left_bg.gif) repeat-y;" nowrap width="12"></TD>';
	_content += 	'<TD>';
	_content += 	'<TABLE cellpadding="0" cellspacing="0" border="0">';
	_content += 	'<TR>';
	_content += 		'<TD><iframe id=multipopup name=multipopup src="../proc/multipopup_content.php?code=' + data.code + '" style="width:' + data.popup_sizew + 'px; height:' + data.popup_sizeh + 'px;" frameborder=0 scrolling=no></iframe></TD>';
	_content += 	'</TR>';
	_content += 	'</TABLE>';
	_content += 	'</TD>';
	_content += 	'<TD style="background: URL(' + _path + '/img/main/popup_right_bg.gif) repeat-y;" nowrap width="12"></TD>';
	_content += '</TR>';
	_content += '<TR>';
	_content += 	'<TD style="background: URL(' + _path + '/img/main/popup_bottom_left.gif) no-repeat;" nowrap width="12" height="12"></TD>';
	_content += 	'<TD style="background: URL(' + _path + '/img/main/popup_bottom_bg.gif) repeat-x;"></TD>';
	_content += 	'<TD style="background: URL(' + _path + '/img/main/popup_bottom_right.gif) no-repeat;" nowrap width="12" height="12"></TD>';
	_content += '</TR>';
	_content += '</TABLE>';

	return _content;
}

function popupLayer(data)
{
	var _layout = '';
	_layout += '<div id="mlpCookie_' + data.code + '" STYLE="position:absolute; width:' + data.popup_sizew + 'px; height:' + data.popup_sizeh + 'px; left:' + data.popup_spoth + 'px; top:' + data.popup_spotw + 'px; z-index:200;">';
	_layout += layerContent(data);
	_layout += '</div>';
	jQuery('body').append(_layout);
}

function popupLayerMove(data)
{
	var _layout = '';
	_layout +='<div id="mlpCookie_' + data.code + '" STYLE="position:absolute; width:' + data.popup_sizew + 'px; height:' + data.popup_sizeh + 'px; left:' + data.popup_spoth + 'px; top:' + data.popup_spotw + 'px; z-index:200;">';
	_layout +='<div onmousedown="Start_move(event,\'mlpCookie_' + data.code + '\');" onmouseup="Moveing_stop();" style=cursor:move;>';
	_layout +='<table border="0" cellspacing="0" cellpadding="0">';
	_layout +='<tr>';
	_layout +='<td>';
	_layout += layerContent(data);
	_layout +='</td>';
	_layout +='</tr>';
	_layout +='</table>';
	_layout +='</div>';
	_layout +='</div>';
	jQuery('body').append(_layout);
}

function winOpen(data)
{
	var property	= 'width=' + data.popup_sizew + ', height=' + data.popup_sizeh + ', top=' + data.popup_spoth + ', left=' + data.popup_spotw + ', scrollbars=no, toolbar=no';
	var win = window.open( '../proc/multipopup_content.php?code=' + data.code , data.code  , property );
	if(win) win.focus();
}