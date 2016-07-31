<?
	### 일양택배 http://www.ilyanglogis.com/functionality/tracking_result.asp?hawb_no=

	$out = split_betweenStr($out,'<table width="600" border="0" cellspacing="0" cellpadding="1" align="center">',"<br>  *");
?>

<?=$out[0]?>