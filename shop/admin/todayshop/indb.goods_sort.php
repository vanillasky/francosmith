<?php
@require "../lib.php";
require_once("../../lib/todayshop_cache.class.php");
$todayShop = &load_class('todayshop', 'todayshop');
$tsCfg = $todayShop->cfg;

$tsCfg['sortOrder'] = $_POST['sortOrder'];

$todayShop->saveConfig($tsCfg);

if ($_POST['sortOrder'] == 'admin') {
	if (is_array($_POST['tgsno']) && empty($_POST['tgsno']) === false) {
		foreach($_POST['tgsno'] as $key => $tgsno) {
			$sortSql = "
			UPDATE ".GD_TODAYSHOP_GOODS." AS TG
				INNER JOIN ".GD_TODAYSHOP_GOODS_MERGED." AS TGM ON TG.tgsno = TGM.tgsno
			SET
			TG.sort=".($key+1).",
			TGM.sort=".($key+1)."
			WHERE TG.tgsno=".$tgsno;

			$db->query($sortSql);
		}
	}
}
// ĳ�� ����
todayshop_cache::truncate();

msg('������ ����Ǿ����ϴ�.');
?>
<script type="text/javascript">parent.location.reload()</script>