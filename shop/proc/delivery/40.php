<?
# 현대택배 http://global.e-hlc.com/servlet/Tracking_View_DLV_ALL
?>

<script language="javascript">
window.onload = function () {
	window.open('','re_popup',"width=830,height=500,scrollbars=yes");
	document.form2.submit();
	self.close();
}
</script>
<form name="form2" method="post" action="<?=$url?>" target="re_popup">
<input name="DvlInvNo" type="hidden" value="<?=$deliverycode?>"/>
</form>
<br><b>Now Loading...</b><br>