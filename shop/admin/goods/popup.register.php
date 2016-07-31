<?
// deprecated. redirect to new page;
header('location: ./adm_popup_goods_form.php?'.$_SERVER['QUERY_STRING'].'&popup=1');
exit;
include "../_header.popup.php";
include "_form.php";
?>
<script>table_design_load();</script>
