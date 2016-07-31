<?
	# AirBoyExpress http://www.airboyexpress.com/Tracking/TrackInfo.aspx?ShippingNumber=

	$out = str_replace('<iframe', '<!--<iframe', $out);
	$out = str_replace('/iframe>', '/iframe>-->', $out);
	$out = str_replace('../Scripts/jquery-1.4.1.min.js', '', $out);
	$out = str_replace('height="0"', '', $out);
	$out = str_replace('$(function () {', '/*$(function () {', $out);
	$out = str_replace('</script></head>', '*/</script></head>', $out);
	echo iconv('utf-8', 'euc-kr', $out);

	//$out에 있는 iframe으로 내용을 가져올 경우 필요없는 okcashback url이 노출되어 추가로 readurl함
	$url2 = 'http://www.airboyexpress.com/Tracking/Tracking.asp?Shipping='.$tail;
	$port2 = 80;

	$out2 = readurl($url2,$port2);
	$out2 = str_replace('<a href="javascript:gocashbag();"><img src="img/btn_okCash_new.png" width="386" height="19" border="0"></a>', '', $out2);
	echo iconv('utf-8', 'euc-kr', $out2);
?>