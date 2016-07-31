<?php
include "../_header.popup.php";

$ini = parse_ini_file('./adm_goods_option_preset_sample.ini', true);

$tab = array_keys($ini);
?>
<script type="text/javascript" src="./js/goods_register.js"></script>
<script type="text/javascript" src="../js/adm_tab.js"></script>
<script type="text/javascript" src="../js/adm_form.js"></script>
<script type="text/javascript">
function __toggleSection(o)
{

	var checked = o.checked;

	o.up('table').select('input[type=checkbox]').each(function(el){

		if (checked == null) {
			checked = !el.checked;
		}

		el.checked = checked;

	});

}

function __putOptionValue()
{
	var values = [];
	$$('input[name="option_value[]"]:checked').each(function(el){
		if (el.up('div').getStyle('display') != 'none')
		{
			values.push(el.value);
		}
	});

	if (values.length > 0) {
		try {
			parent.$$('input[name="option_value[]"]:last')[0].value = values.join(',');
		}
		catch (e) {}
	}

	parent.nsAdminForm.dialog.close();
	return false;
}
</script>

<form class="admin-form" onsubmit="return __putOptionValue();">
	<div id="el-tab" class="tab">

		<ol class="navigation">
			<? for ($i=0,$m=sizeof($tab);$i<$m;$i++) { ?>
			<li><span class="head"></span><a href="#<?=$tab[$i]?>"><span><?=$tab[$i]?></span></a><span class="tail"></span></li>
			<? } ?>
		</ol>

		<? for ($i=0;$i<$m;$i++) { ?>
		<div id="container_<?=$tab[$i]?>" class="container">
			<table class="nude">
			<tr>
			<?
			$sections = array_keys($ini[$tab[$i]]);
			for ($j=0,$n = sizeof($sections);$j<$n;$j++) {
			?>
				<td class="vt">
					<table class="admin-list-table">
					<thead>
					<tr>
						<th class="al" style="padding-left:5px;">
							<label><input type="checkbox" onclick="__toggleSection(this);"> <?=$sections[$j]?></label>
						</th>
					</tr>
					</thead>
					<tbody>
					<? foreach(explode(',',$ini[$tab[$i]][$sections[$j]]) as $item) { ?>
					<tr>
						<td><label><input type="checkbox" name="option_value[]" value="<?=$item?>" > <?=$item?></label></td>
					</tr>
					<? } ?>
					</tbody>
					</table>
				</td>
				<?
				if (($j+1) % 5 == 0) {
					echo '</tr><tr>';
				}
			}
			?>
			</tr>
			</table>
		</div>
		<? } ?>
	</div>

	<hr>

	<div class="button-container">
		<input type="image" src="../img/buttons/btn_popup_ok.gif" >
		<a href="javascript:void(0);" onclick="parent.nsAdminForm.dialog.close();"><img src="../img/buttons/btn_popup_cancel.gif" ></a>
	</div>

</form>
<script type="text/javascript">
// onload events
Event.observe(document, 'dom:loaded', function(){
	nsAdminTab.init('el-tab');
});
</script>
<?php include "../_footer.popup.php"; ?>
