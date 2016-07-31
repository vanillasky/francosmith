<?php
include '../../_header.php';
$jQueryPath = $cfg['rootDir'] . '/lib/js/jquery-1.11.3.min.js';
$jQueryDisplayPath = $cfg['rootDir'] . '/lib/js/jquery.insgoWidgetDisplay.js';
?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<title>insgoWidget</title>
<script type="text/javascript" src="<?php echo $jQueryPath; ?>"></script>
<script type="text/javascript" src="<?php echo $jQueryDisplayPath; ?>"></script>
<style>
body { margin:0px 0px 0px 0px}
</style>
<div id="insgoWidgetLayout"></div>

<script type="text/javascript">
$(document).ready(function(){
	$('#insgoWidget').insgoWidgetDisplay({
		queryString : '<?php echo $_SERVER[QUERY_STRING]; ?>'
	});
});
</script>
</body>
</html>