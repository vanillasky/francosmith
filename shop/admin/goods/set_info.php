<?PHP
include "../_header.php";
$fn = $_GET['fn'];
if($fn)$src="../../../shop/setGoods/admin/goods/?fn=".$fn;
else $src ="/shop/blank.txt";
?>
<script type="text/javascript">
	// iframe resize
	function autoResize(thisiframe){
		var iframeHeight= thisiframe.contentWindow.document.body.scrollHeight;

		thisiframe.height=iframeHeight+20;
	}
</script>
<iframe id="ifrmHidden" name="ifrmHidden" frameborder="0" onload="autoResize(this)" src="<?=$src?>" style="width:100%;"></iframe>
<?
include "../_footer.php";

?>