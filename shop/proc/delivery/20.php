<? 
	### 하나로택배 http://www.hanarologis.com/branch/chase/listbody.html?a_gb=center&a_cd=4&a_item=0&f_slipno=
	$out = split_betweenStr($out,'<table width="600" border="0" cellspacing="0" cellpadding="2">','<table width="600" border="0" cellspacing="0" cellpadding="0">');	
	$out[0] = str_replace(array('images/end_st3.gif'),array('http://www.hanarologis.com/branch/chase/images/end_st3.gif'),$out[0]);
	
?>

<p><table border=0 style="border-collapse:collapse" width="100%">
<col style="background:#f7f7f7"><col><col style="padding-left:10px">
<?=$out[0]?>
</table>