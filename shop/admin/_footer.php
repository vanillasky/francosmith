<? if ($mainpage){ // 메인인경우?>
		</td>
	</tr>
	</table>
	
	</td>
</tr>
<tr>
	<td colspan="10">
	<table width="100%" height="73" cellpadding="0" cellspacing="0" border="0" bgcolor="#EDEDED">
	<tr>
		<td style="padding:10px 10px 10px 0px"><img src="../img/copyright.gif" /><a href="javascript:panel('maxlicense', 'header');"><img src="../img/btn_clause.gif" border=0></a></td>
		<td align="right" style="padding:10px 10px 10px 0px"><img src="../img/godo_logo_bottom.gif" /></td>
	</tr>
	</table>
<? } ?>

<? if (!$mainpage){ ?>
			</td>
		</tr>
		</table>

		</td>
	</tr>
	<tr>
		<td id="leftfooter" style="background:url('../img/footer_left.gif') no-repeat; height:40px;"></td>
		<td></td>
		<td></td>
	</tr>
	</table>

	</td>
</tr>
<tr>
	<td colspan="10">
	<table width="100%" height="73" cellpadding="0" cellspacing="0" border="0" bgcolor="#EDEDED">
	<tr>
		<td style="padding:10px 10px 10px 0px"><img src="../img/copyright.gif" /><a href="javascript:panel('maxlicense', 'header');"><img src="../img/btn_clause.gif" border=0></a></td>
		<td align="right" style="padding:10px 10px 10px 0"><img src="../img/godo_logo_bottom.gif" /></td>
	</tr>
	</table>
<? } ?>

	</td>
</tr>
</table>
<div id="maxlicense" style="display:none;"></div>
<script>
linecss();
table_design_load();
</script>
<? if ($hiddenLeft){ ?><script>hiddenLeft()</script><? } ?>