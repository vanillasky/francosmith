<?
	### Á¶ÀÌÀÍ½ºÇÁ·¹½º http://72.54.245.5/inc/tracking.asp?hawb_no=

	$out = split_betweenStr($out,"<body>","</body>");

	$out[0] = str_replace('../images/','http://72.54.245.5/images/',$out[0]);
	//$out[0] = str_replace(array('images/parcels_tracking.gif'),array('http://72.54.245.5/images/parcels_tracking.gif'),$out[0]);
?>

<style type="text/css">
<!--
.style1 {
	font-family: "µ¸¿ò";
	font-size: 12px;
	font-weight: bold;
}
.style2 {
	font-family: "µ¸¿ò";
	font-size: 12px;
	font-weight: bold;
	color: #990000;
}
.style3 {
	font-family: "µ¸¿ò";
	font-size: 12px;
	color: #666666;
}
-->
</style>

<?=$out[0]?>

<script language="javascript">
<!----
	function CloseWin(){

		window.close();
		//	window.resizeBy (0, 25);
	}

function showdetail(bl_no){
	var obj = document.getElementById("cj_detail");
	if (document.track.advance.value == "Show Details>>>"){
		document.track.advance.value = "Hide Details<<<";
		obj.style.display = "block";
		window.scroll(0, 600);
	}
	else{
		document.track.advance.value = "Show Details>>>"
		obj.style.display = "none";
	}
}
//-------->
</script>