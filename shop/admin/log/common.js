function bar_flash(src,width,height,day,mode)
{
	document.write('<OBJECT classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"\
codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0"\
WIDTH="' + width + '" HEIGHT="' + height + '" id="FC_2_3_Column3D" ALIGN="">\
<PARAM NAME=movie VALUE="' + src + '">\
<PARAM NAME=FlashVars VALUE="&dataURL=Data.xml.php?day=' + day + '%26mode=' + mode + '">\
<PARAM NAME=quality VALUE=high>\
<PARAM NAME=bgcolor VALUE=#FFFFFF>\
<PARAM NAME=wmode VALUE=transparent>\
<EMBED src="' + src + '" FlashVars="&dataURL=Data.xml.php?day=' + day + '%26mode=' + mode + '" quality=high bgcolor=#FFFFFF  WIDTH="' + width + '" HEIGHT="' + height + '" NAME="FC_2_3_Column3D" ALIGN=""\
TYPE="application/x-shockwave-flash" PLUGINSPAGE="http://www.macromedia.com/go/getflashplayer" wmode=transparent></EMBED>\
</OBJECT>');

}

function flash_chart(param)
{
	var time = new Date().getTime();

	var _param = param || {};
	_param.foo = time;

	var param_str = Object.toQueryString(_param);

	document.write('<OBJECT classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0" WIDTH="' + _param.width + '" HEIGHT="' + _param.height + '" id="'+ _param.id +'" name="'+_param.id+'" ALIGN="">\
	<PARAM NAME=movie VALUE="../../lib/ofc/open-flash-chart.swf">\
	<PARAM NAME=FlashVars VALUE="'+ param_str +'">\
	<PARAM NAME=quality VALUE=high>\
	<PARAM NAME=bgcolor VALUE=#FFFFFF>\
	<PARAM NAME=wmode VALUE=transparent>\
	<EMBED TYPE="application/x-shockwave-flash" PLUGINSPAGE="http://www.macromedia.com/go/getflashplayer" src="../../lib/ofc/open-flash-chart.swf" FlashVars="'+ param_str +'" quality=high wmode=transparent bgcolor=#FFFFFF  WIDTH="' + _param.width + '" HEIGHT="' + _param.height + '" id="'+ _param.id +'" NAME="'+ _param.id +'" ALIGN=""></EMBED>\
	</OBJECT>');

}
