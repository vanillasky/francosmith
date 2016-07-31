<?php
include "../_header.popup.php";

$memberGroups = Clib_Application::getCollectionClass('member_group');
$memberGroups->load();

if (Clib_Application::request()->get('str')) {
	$levels = Clib_Application::request()->get('str');
}
else {

	for ($i=1;$i<=100;$i++) {
		$levels .= '0';
	}

	foreach($memberGroups as $memberGroup) {
		$levels = substr_replace ( $levels, '1', $memberGroup->getLevel() - 1, 1);
	}
}

?>
<link rel="stylesheet" type="text/css" href="./css/css.css">
<script type="text/javascript">
var levels = '<?=$levels?>';

function __close() {
	if ($$('input[name="buyable_member_group[]"]:checked').size() > 0)
	{
		parent.nsAdminForm.dialog.close();
	}
	else {
		alert('하나도 체크된게 없으면 안됨!');
		return false;
	}
}

function __generate(el)
{
	var level = el.value;
	var flag = el.checked ? '1' : '0';

	levels = levels.substr(0, level - 1) + flag + levels.substr(level);
	parent.nsAdminGoodsForm.buyable.setMemberGroup(levels);

}
</script>

<table class="admin-list-table">
<thead>
<tr>
	<th>그룹명</th>
	<th>그룹레벨</th>
	<th>회원수</th>
	<th>선택</th>
</tr>
</thead>
<tbody>
<?
foreach ($memberGroups as $memberGroup) {
	$checked = $levels[$memberGroup->getLevel() - 1] ? 'checked' : '';
?>
<tr class="ac">
	<td><?=$memberGroup->getGrpnm()?></td>
	<td><?=$memberGroup->getLevel()?></td>
	<td><?=number_format($memberGroup->getMemberCount())?></td>
	<td><input type="checkbox" name="buyable_member_group[]" value="<?=$memberGroup->getLevel()?>" <?=$checked?> onclick="__generate(this);"></td>
</tr>
<? } ?>
</tbody>
</table>

<div class="button-container">
	<a href="javascript:void(0);" onclick="__close();return false;"><img src="../img/buttons/btn_popup_confirm.gif"></a>
</div>
<?
include "../_footer.popup.php";
?>
