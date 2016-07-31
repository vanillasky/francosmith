<?
$scriptLoad='<link rel="styleSheet" href="./_inc/style.css">';
include "../_header.popup.php";
require_once ('./_inc/config.inc.php');

$shople = Core::loader('shople');
$shopleCfg = $shople->cfg;

$action = isset($_POST['action']) ? $_POST['action'] : '';

$request = $shople->subscribe->cancel($action);
?>
<script type="text/javascript" src="./_inc/common.js"></script>
<!-- * -->

<?=$request?>

<!-- eof * -->
<script type="text/javascript">
linecss();
table_design_load();
</script>
</body>
</html>
