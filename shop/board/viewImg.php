<script>
function fitwin()
{
	window.resizeTo(150,150);

	var borderX = 150 - document.body.clientWidth;
	var borderY = 150 - document.body.clientHeight;

	if(document.body.clientWidth > 150)borderX = 50;
	if(document.body.clientHeight > 150)borderY = 50;

	width	= document.images[0].width + borderX;
	height	= document.images[0].height + borderY;
	windowX = (window.screen.width-width)/2;
	windowY = (window.screen.height-height)/2;

	if(width>screen.width){
		width = screen.width;
		windowX = 0;
	}
	if(height>screen.height-50){
		height = screen.height-50;
		windowY = 0;
	}

	window.moveTo(windowX,windowY);
	window.resizeTo(width,height);
}

function move()
{
	x_per = (document.images[0].width-document.body.clientWidth)/document.body.clientWidth;
	y_per = (document.images[0].height-document.body.clientHeight)/document.body.clientHeight;
	window.scroll(window.event.clientX*x_per,window.event.clientY*y_per);
}

</script>

<title>이미지 자세히 보기</title>

<body style="margin:0" scroll=no onLoad=fitwin()>
<div align=center><img src="<?=$_GET[src]?>" onClick="window.close();" style="cursor:hand" onmousemove='move()' usemap=""></div>
</body>