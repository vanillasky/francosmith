<script language='javascript'>
/*** 레이어 팝업창 띄우기 ***/
function customPopupLayer(s,w,h)
{
	if (!w) w = 600;
	if (!h) h = 400;

	var pixelBorder = 3;
	var titleHeight = 12;
	w += pixelBorder * 2;
	h += pixelBorder * 2 + titleHeight;

	var bodyW = document.body.clientWidth;
	var bodyH = document.body.clientHeight;

	var posX = (bodyW - w) / 2;
	var posY = (bodyH - h) / 2;

	hiddenSelectBox('hidden');

	/*** 백그라운드 레이어 ***/
	var obj = document.createElement("div");
	with (obj.style){
		position = "absolute";
		left = 0;
		top = 0;
		width = "100%";
		height = document.body.scrollHeight;
		backgroundColor = "#000000";
		filter = "Alpha(Opacity=80)";
		opacity = "0.5";
	}
	obj.id = "objPopupLayerBg";
	document.body.appendChild(obj);

	/*** 내용프레임 레이어 ***/
	var obj = document.createElement("div");
	with (obj.style){
		position = "absolute";
		left = posX + document.body.scrollLeft;
		top = posY + document.body.scrollTop;
		width = w;
		height = h;
		backgroundColor = "#ffffff";
		border = "3px solid #000000";
	}
	obj.id = "objPopupLayer";
	document.body.appendChild(obj);

	/*** 타이틀바 레이어 ***/
	var bottom = document.createElement("div");
	with (bottom.style){
		position = "absolute";
		width = w - pixelBorder * 2;
		height = titleHeight;
		left = 0;
		top = h - titleHeight - pixelBorder * 3;
		padding = "4px 0 0 0";
		textAlign = "center";
		backgroundColor = "#000000";
		color = "#ffffff";
		font = "bold 8pt tahoma; letter-spacing:0px";

	}
	bottom.innerHTML = "<a href='javascript:closeLayer()' class='white'>X close</a>";
	obj.appendChild(bottom);

	/*** 아이프레임 ***/
	var ifrm = document.createElement("<iframe name='processLayerForm'></iframe>");
	with (ifrm.style){
		width = w - 6;
		height = h - pixelBorder * 2 - titleHeight - 3;
		//border = "3 solid #000000";
	}
	ifrm.frameBorder = 0;
	ifrm.src = s;
	//ifrm.className = "scroll";
	obj.appendChild(ifrm);
}

var chkAllOrderNOFlag=true;
function chkAllOrderNO() {
	$$('input[name="orderNo[]"]').each(function(item){
		item.checked=chkAllOrderNOFlag;
	});
	chkAllOrderNOFlag=!chkAllOrderNOFlag;
}
</script>