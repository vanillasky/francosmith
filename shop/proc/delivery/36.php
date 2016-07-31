<?
	# CVSnet http://www.doortodoor.co.kr/jsp/cmn/TrackingCVS.jsp?pTdNo=

	$out = str_replace('/css/etc.css','http://www.doortodoor.co.kr/css/etc.css',$out);
	$out = str_replace('/doortodoor.do','http://www.doortodoor.co.kr/doortodoor.do',$out);

	echo iconv('utf-8', 'euc-kr', $out);
?>