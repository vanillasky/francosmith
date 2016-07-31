<? ### 고려택배 (http://www.gologis.com/delivery/s_search.asp?f_slipno=)
$out = split_betweenStr($out,'<table width="630" border="0" cellpadding="0" cellspacing="0">','<td valign="top" background="../common/body_bg02.jpg">');
$out[0] = str_replace('<font size="2">','',$out[0]);
//debug($out);

?>
<base href="http://www.gologis.com/delivery/">
<p><?=$out[0]?>
<script>
var tb = document.getElementsByTagName('table');
tb[0].style.width = "100%";
tb[1].style.width = "100%";

function StcdSearch(stcd)
{
	window.open("http://www.gologis.com/delivery/stcd_search.asp?stcd="+stcd,"new","width=300,height=270,left=340,top=300") ;
}
function StcdtradSearch(stcd_trad)
{
	window.open("http://www.gologis.com/delivery/trad_search.asp?stcd_trad="+stcd_trad,"new","width=300,height=270,left=340,top=300") ;
}
</script>
<base href="<?=$_SERVER[SERVER_NAME].dirname($_SERVER[PHP_SELF])?>/">