<?
	# 스피드익스프레스	http://www.speedyexpress.net/byspeedy_traking_quary.php?tracking_no=SPE121121770116

	$out = split_betweenStr($out,'<TABLE class=ListTable>','<TBODY><TR>');
	$out[0] = str_replace('core_images/','http://www.speedyexpress.net/core_images/',$out[0]);
?>

<LINK title="Global CSS" href="http://www.speedyexpress.net/core_images/looks.css" type=text/css rel=StyleSheet>
<link href="http://www.speedyexpress.net/core_images/style.css" rel="stylesheet" type="text/css">
<style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
-->
</style>

<TABLE class=ListTable>
<?=$out[0]?>

<TABLE>
</TABLE>