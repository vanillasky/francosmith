<?
require_once('../lib/todayshop_cache.class.php');
$cache = new todayshop_cache();

include "../_header.php";
include "../lib/page.class.php";

$todayshop = Core::loader('todayshop');


$pg = new Page($_GET[page],$_GET[page_num]);

$where[] = "TK.notice=0";

$db_table = "
		".GD_TODAYSHOP_TALK." AS TK
		LEFT JOIN ".GD_TODAYSHOP_GOODS_MERGED." AS TG
		ON TK.tgsno = TG.tgsno
";


$pg = new Page($_GET[page],$_GET[page_num]);
$pg->cntQuery = "SELECT COUNT(ttsno) FROM ".GD_TODAYSHOP_TALK;
$pg->field = "
			TK.*,
			TG.goodsnm, TG.img_s
";
$pg->setQuery($db_table,$where,'TK.gid DESC, HEX(TK.thread)','');
$pg->exec();


$res = $db->query($pg->query);
while ($data=$db->fetch($res)){
	$data['idx'] = $pg->idx--;
	foreach($data as $key => $val) {
		$tmp[$key] = $val;
	}
	$tmp['step'] = strlen($tmp['thread']) / 2;
	$tmp['auth'] = ($member['level'] >= 80 || $tmp['m_no'] == $member['m_no'])? 'y' : 'n';
	$arRow[] = $tmp;
}


$tpl->assign(array(
			pg		=> $pg,
			data	=> $arRow
			));

$_html = $tpl->fetch('tpl');

// Ä³½Ã
$cache->setCache($_html);
?>
