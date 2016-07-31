<?
include "../lib.php";
include "../../conf/config.php";

$integrate_order = Core::loader('integrate_order');

$step = isset($_POST['step']) ? (int) $_POST['step'] : false;

if ($integrate_order->doManualSync($step))
    echo 'ok';
else
    echo 'complete';
?>
