<?php ob_start(); ?>
<?php if ($load_config_ncash['ncash_emoney'] > 0) { ?>
<dt>네이버<br/>마일리지</dt>
<dd><?php echo number_format($load_config_ncash['ncash_emoney']); ?>원</dd>
<?php } ?>
<?php if ($load_config_ncash['ncash_cash'] > 0) { ?>
<dt>네이버 캐쉬</dt>
<dd><?php echo number_format($load_config_ncash['ncash_cash']); ?>원</dd>
<?php } ?>
<?php return ob_get_clean(); ?>