<?php ob_start(); ?>
<?php if ($data['ncash_emoney']) { ?>
<tr>
	<th>네이버<br/>마일리지</th>
	<td><?php echo number_format($data['ncash_emoney']); ?>원</td>
</tr>
<?php } ?>
<?php if ($data['ncash_cash']) { ?>
<tr>
	<th>네이버 캐쉬</th>
	<td><?php echo number_format($data['ncash_cash']); ?>원</td>
</tr>
<?php } ?>
<?php return ob_get_clean(); ?>