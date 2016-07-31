<?
include "../_header.popup.php";

$formatter = & load_class('stringFormatter','stringFormatter');
$couponGenerator = & load_class('couponGenerator','couponGenerator');

$_arStats = array('','판매대기','판매중','판매실패','판매완료',);

// {{{ 임시
		// 쿠폰 번호 발급
		$couponGenerator->max = 1;
		$couponGenerator->length = 12;			// 자리수 (prefix 포함)
//		$couponGenerator->prefix = 'GD';		// prefix
// }}}


$goodsno = isset($_GET['goodsno']) ? $_GET['goodsno'] : '';


// 상품정보
$query = "
	SELECT
		G.goodsno, G.goodsnm, G.goodscd, G.maker, G.runout,
		TG.goodstype, TG.limit_ea, TG.buyercnt, TG.fakestock, TG.startdt, TG.enddt, TG.processtype,
		TC.cp_name, TC.cp_sno,
		IF (TG.processtype = 'i',
		4,
			IF (
				NOW() < TG.startdt,
				1,	/* 판매대기 */
				IF (
					(NOW() <= TG.enddt OR TG.enddt IS NULL) AND G.runout = 0,
					2,	/* 판매중 */
					IF (
						TG.fakestock2real = 1,
							IF (TG.limit_ea <> 0 AND (TG.buyercnt + TG.fakestock) < TG.limit_ea,
							3,	/* 판매실패 */
							4	/* 판매완료 = 판매종료 */
							)
							,
							IF (TG.limit_ea <> 0 AND TG.buyercnt < TG.limit_ea,
							3,	/* 판매실패 */
							4	/* 판매완료 = 판매종료 */
							)
					)
				)
			)
		) AS stats

	FROM ".GD_GOODS." AS G
	INNER JOIN ".GD_TODAYSHOP_GOODS." AS TG
	ON G.goodsno = TG.goodsno
	LEFT JOIN ".GD_TODAYSHOP_COMPANY." AS TC
	ON TG.company = TC.cp_sno
	WHERE G.goodsno = '$goodsno'
";
$goods = $db->fetch($query, 1);

// 주문상태 (다중 선택이 가능하므로 OR 연산)
$_SQL['WHERE']['OR'] = array();
if ($_GET[step]){
	$_SQL['WHERE']['OR'][] = "
			(step IN (".implode(",",$_GET[step]).") AND step2 = '')
			";
	foreach ($_GET[step] as $v) $checked[step][$v] = "checked";
}

if ($_GET[step2]) {
	foreach ($_GET[step2] as $v) {
		switch ($v){
			case "1": $_SQL['WHERE']['OR'][] = "(O.step=0 and O.step2 between 1 and 49)"; break;
			case "2": $_SQL['WHERE']['OR'][] = "(O.step in (1,2) and O.step2!=0) OR (O.cyn='r' and O.step2='44' and O.dyn!='e')"; break;
			case "3": $_SQL['WHERE']['OR'][] = "(O.step in (3,4) and O.step2!=0)"; break;
			case "60" :
				$_SQL['WHERE']['OR'][] = "(OI.dyn='e' and OI.cyn='e')";
			break; //교환완료
			case "61" : $_SQL['WHERE']['OR'][] = "oldordno != ''";break; //재주문
			default:
				$_SQL['WHERE']['OR'][] = "O.step2=$v";
			break;
		}
		$checked[step2][$v] = "checked";
	}
}

if (!empty($_SQL['WHERE']['OR'])) $_SQL['WHERE'][] = "(".implode(" OR ",$_SQL['WHERE']['OR']).")";
unset($_SQL['WHERE']['OR']);

// 주문 정보
$query = "
	SELECT
		O.*, MB.m_id,
		OI.ea,
		O.deliverycode, CP.cp_num, CP.cp_publish
	FROM ".GD_ORDER." AS O
	INNER JOIN ".GD_ORDER_ITEM." AS OI
	ON OI.ordno = O.ordno
	INNER JOIN ".GD_GOODS." AS G
	ON G.goodsno = OI.goodsno /* AND G.todaygoods = 'y' */
	LEFT JOIN ".GD_TODAYSHOP_ORDER_COUPON." AS CP
	ON O.ordno = CP.ordno
	LEFT JOIN ".GD_LIST_DELIVERY." AS LD
	ON OI.dvno = LD.deliveryno
	LEFT JOIN ".GD_MEMBER." AS MB
	ON O.m_no=MB.m_no
	WHERE
		G.goodsno = '$goodsno'
";

if (empty($_SQL['WHERE'])===false) {
	$query .= ' AND '.implode(' AND ', $_SQL['WHERE']);
}

$rs = $db->query($query);
?>
<script type="text/javascript" src="../todayshop/todayshop.js"></script>
<script type="text/javascript">
function fnViewOrder(n) {
	opener.location.href = './view.php?ordno='+n;
}

function changeStatus() {
	<? if ($goods['processtype']=='b' && $goods['stats']<4) { ?>
	alert("일괄처리 상품은 판매가 종료되어야만 상태 변경이 가능합니다.");
	return false;
	<? } else { ?>
	var f = document.frmStatus;
	if (f.status.selectedIndex == 0) {
		alert("변경할 상태를 선택하세요.");
		return false;
	}
	f.action = "indb.todayshop_buyer_list.php";
	return true;
	<? } ?>
}
</script>

<form name="frmList">
<input type="hidden" name="goodsno" value="<?=$_GET['goodsno']?>" />
<div class="title title_top">상품정보<span></div>
<table class="tb">
<col class=cellC><col class=cellL><col class=cellC><col class=cellL>
<tr>
	<td>상품명</td>
	<td><?=$goods[goodsnm]?></td>
	<td>판매상태</td>
	<td><?=$_arStats[ $goods[stats] ]?></td>
</tr>
<tr>
	<td>상품유형</td>
	<td><?=($goods[goodstype]=='coupon')? '쿠폰' : '실물'?> (<?=($goods[processtype]=='i')? '즉시' : '일괄'?>)</td>
	<td>상품코드</td>
	<td><?=$goods[goodscd]?></td>
</tr>
<tr>
	<td>제조사</td>
	<td><?=$goods[maker]?></td>
	<td>공급업체</td>
	<td><?=$goods[cp_name]?></td>
</tr>
<tr>
	<td>목표량</td>
	<td><?=$goods[limit_ea]?></td>
	<td>총판매량</td>
	<td><?=$goods[buyercnt]+$goods[fakestock]?>(<?=$goods[buyercnt]?>+<?=$goods[fakestock]?>)</td>
</tr>
<tr>
	<td>판매기간</td>
	<td colspan="3"><?=$goods[startdt]?> ~ <?=$goods[enddt]?></td>
</tr>
<tr>
	<td>주문상태</td>
	<td colspan="3" class="noline">
		<? $idx = 0; foreach ($r_step as $k=>$v){ ?>
		<label><input type=checkbox name=step[] value="<?=$k?>" <?=$checked[step][$k]?>><font class=small1 color=5C5C5C><?=$v?></font></label>
		<? } ?>
		<label><input type=checkbox name=step2[] value="1" <?=$checked[step2][1]?>><font class=small1 color=5C5C5C>주문취소</font></label>
		<label><input type=checkbox name=step2[] value="2" <?=$checked[step2][2]?>><font class=small1 color=5C5C5C>환불관련</font></label>
		<label><input type=checkbox name=step2[] value="3" <?=$checked[step2][3]?>><font class=small1 color=5C5C5C>반품관련</font></label>
		<label><input type=checkbox name=step2[] value="60" <?=$checked[step2][60]?>><font class=small1 color=5C5C5C>교환완료</font></label>
		<label><input type=checkbox name=step2[] value="61" <?=$checked[step2][61]?>><font class=small1 color=5C5C5C>재주문</font></label>
		<label><input type=checkbox name=step2[] value="50" <?=$checked[step2][50]?>><font class=small1 color=5C5C5C>결제시도</font></label>
		<label><input type=checkbox name=step2[] value="54" <?=$checked[step2][54]?>><font class=small1 color=5C5C5C>결제실패</font></label>
		<label><input type=checkbox name=step2[] value="51" <?=$checked[step2][51]?>><font class=small1 color=5C5C5C>PG확인요망</font></label>
	</td>
</tr>
</table>
<div class="noline" style="text-align:center;"><input type="image" src="../img/btn_search2.gif" /></div>
</form>

<form name="frmTemp" action="" method="post" target="ifrmHidden">
<input type="hidden" name="mode" value="">
<input type="hidden" name="goodsno" value="">
<input type="hidden" name="ordno" value="">
</form>

<?
	// 리스트를 별도록 관리.
	include('./todayshop_buyer_list.inc.'.$goods['goodstype'].'.php');
?>

<script>
linecss();
table_design_load();
</script>
</body>
</html>