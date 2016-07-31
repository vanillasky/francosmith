<?
	# ACI Express http://acieshop.com/pod.html?OrderNo=

	$out = str_replace('utf-8', 'euc-kr', $out);
	$full_html = explode("\n", $out);

	$out = '';
	foreach($full_html as $line) {
		$out .= iconv('utf-8', 'euc-kr', $line) . "\n";
	}

	echo $out;
?>