<?
include "../lib.php";
$arTarget = isset($_POST['target']) ? $_POST['target'] : '';
$arRunout = isset($_POST['runout']) ? $_POST['runout'] : '';

$_instr['show'] = '';
$_instr['hide'] = '';

if (is_array($arTarget)) {

	// where 절 만들기.
	foreach ($arTarget as $key => $goodsno) {

		$isRunout = ($arRunout[$goodsno] == 1) ? true : false;
		
		// 다음 요약 EP
		$ar_update['runout'] = $isRunout;
		daum_goods_diff($goodsno,$ar_update);

		if ($isRunout) {
			$_instr['true'] .= $goodsno.',';
		}
		else {
			$_instr['false'] .= $goodsno.',';
		}
	}

	// 끝 콤마 제거 및 쿼리..
	foreach ($_instr as $s => $in) {
		if ($in == '') continue;
		$db->query( "update ".GD_GOODS." set runout='".($s == 'true' ? 1 : 0)."' where goodsno IN (".( preg_replace('/,$/','',$in) ).")" );
	}

}

// 페이지캐시 초기화
$templateCache = Core::loader('TemplateCache');
$templateCache->clearCacheByClass('goods');

echo '
<script>
alert("저장되었습니다.");
parent.location.reload();
</script>
';
