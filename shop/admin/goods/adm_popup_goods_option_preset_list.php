<?
include "../_header.popup.php";

$_keyword = Clib_Application::request()->get('keyword');
$_target  = Clib_Application::request()->get('target');

$where = array();

if ($_keyword) {
	switch($_target) {
		case 'title':
			$where[] = sprintf("title like '%%%s%%'", $_keyword);
			break;
		case 'value':
			$where[] = sprintf("(opt1 like '%%%s%%' or opt2 like '%%%s%%')", $_keyword, $_keyword);
			break;
		case 'name':
		default :
			$where[] = sprintf("(optnm1 like '%%%s%%' or optnm2 like '%%%s%%')", $_keyword, $_keyword);
			break;
	}
}

$pg = new Page($_GET[page],10);
$pg->setQuery(GD_DOPT,$where,'sno');
$pg->exec();
$res = $db->query($pg->query);
?>
<script type="text/javascript">
var __presetList = function() {
	return {
		presets : [],
		add : function(data)
		{
			this.presets.push(data);
		},
		set:function(idx)
		{
			var data = this.presets[idx];
			parent.nsAdminGoodsForm.option.preset.set(data);
			parent.nsAdminForm.dialog.close();
		}
	}
}();
</script>

<form name="fm" method="get" class="admin-form">
<div class="inline-search">
	<select name="target">
		<option value="name" <?=$_target == 'name' ? 'selected' : ''?>>可记疙</option>
		<option value="value" <?=$_target == 'value' ? 'selected' : ''?>>可记蔼</option>
		<option value="title" <?=$_target == 'title' ? 'selected' : ''?>>可记技飘疙</option>
	</select>

	<span>
		<input type="text" name="keyword" value="<?=$_keyword?>">
	</span>

	<input type="image" src="../img/buttons/btn_popup_search.gif" >
</div>
</form>

<table class="admin-list-table" style="margin-top:10px;">
<colgroup>
	<col style="width:100px;">
	<col>
	<col style="width:80px;">
	<col style="width:50px;">
</colgroup>
<thead>
<tr>
	<th>可记 技飘疙</th>
	<th>可记疙:可记蔼</th>
	<th>殿废/荐沥老</th>
	<th>急琶</th>
</tr>
</thead>
<tbody>
<?
$i = 0;
while ($data=$db->fetch($res)) {
	$values = array();
	if ($data['optnm1']) {
		$values[$data['optnm1']] = str_replace('^',',',$data['opt1']);
	}

	if ($data['optnm2']) {
		$values[$data['optnm2']] = str_replace('^',',',$data['opt2']);
	}
?>

<tr class="ac">
	<td><?=$data['title']?></td>
	<td class="al">
		<? foreach($values as $name=>$value) { ?>
		<?=$name?> : <?=$value?> <br />
		<? } ?>
	</td>
	<td><?=Core::helper('date')->format($data['regdt'],'Y-m-d')?></td>
	<td>
		<a href="javascript:void(0);" onclick="__presetList.set(<?=$i++?>);return false;"><img src="../img/buttons/btn_popup_select.gif"></a>
		<script type="text/javascript">
		__presetList.add(<?=gd_json_encode($values)?>);
		</script>
	</td>
</tr>
<? } ?>
</tbody>
</table>

<div class="admin-list-toolbar">
	<div class="paging"><?=$pg->page['navi']?></div>
</div>

</body>
</html>
