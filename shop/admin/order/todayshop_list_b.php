<?
if ($_GET['goodstype']=='coupon') $loc_detail = '쿠폰(일괄발급)';
else $loc_detail = '실물(일괄발송)';

$location = "주문관리 > ".$loc_detail." 주문리스트";
include "../_header.php";
@include "../../conf/config.pay.php";
include "../../lib/page.class.php";
@include "../../conf/phone.php";
$_arStats = array('','판매대기','판매중','판매실패','판매완료',);
$todayShop = &load_class('todayshop', 'todayshop');
if (!$todayShop->auth()) {
	msg('신청후에 사용가능한 서비스입니다.', -1);
}

// 기본값 설정
if($_GET['first'] && $cfg['orderPeriod'])$_GET['period'] = $cfg['orderPeriod'];
if ($_GET[period] != ''){
	$_GET[regdt][0] = date("Ymd",strtotime("-$_GET[period] day"));
	$_GET[regdt][1] = date("Ymd");
}
$_GET['list'] = isset($_GET['list']) ? $_GET['list'] : 'goods';
$_GET['processtype'] = 'b';
$_GET['todaygoods'] = 'y';

$style['cyn']['y'] = "<b style='color:#0A246A'>";

if (!$_GET[dtkind]) $_GET[dtkind] = 'orddt'; # 처리일
$checked[dtkind][$_GET[dtkind]] = $checked[settlekind][$_GET[settlekind]] = "checked";

$checked['goodstype'][$_GET[goodstype]] = 'checked';
$checked['stats'][$_GET[stats]] = 'checked';

$selected[skey][$_GET[skey]] = "selected";
$selected[sgkey][$_GET[sgkey]] = "selected";
$selected['company'][$_GET['company']] = "selected";

// 공급업체 가져오기
$res = $db->query("SELECT cp_sno, cp_name FROM ".GD_TODAYSHOP_COMPANY);
while($tmpData = $db->fetch($res, 1)) $cpData[] = array('cp_sno'=>$tmpData['cp_sno'], 'cp_name'=>$tmpData['cp_name']);
unset($res);

// 쿼리 생성
	// 셀렉트 필드.
		$_SQL['FIELD'] = "
			a.*,
			b.m_id, b.m_no,
			G.goodsno, G.goodsnm,
			TG.tgsno, TG.goodsno, TG.encor, TG.visible, TG.startdt, TG.enddt, TG.regdt, TG.buyercnt, TG.fakestock, TG.limit_ea, TG.goodstype,

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
			) AS stats,

			(SELECT count(*) cntDv FROM ".GD_ORDER_ITEM." WHERE ordno=a.ordno and dvcode != '' and dvno != '') AS cntDv
		";



	// 대상 테이블
		$_SQL['TABLE'] = "".
			GD_ORDER." AS a
			LEFT JOIN ".GD_MEMBER." AS b
			ON a.m_no=b.m_no
			INNER JOIN ".GD_ORDER_ITEM." AS OI
			ON OI.ordno = a.ordno
			INNER JOIN ".GD_GOODS." AS G
			ON OI.goodsno = G.goodsno
			INNER JOIN ".GD_TODAYSHOP_GOODS." AS TG
			ON G.goodsno = TG.goodsno
		";

	// GROUP 절
		$_SQL['GROUP'] = '';

	// ORDER 절
		if ($_GET['list']=="goods"){
			$_SQL['ORDER'] = "step2*10+step,dyn,a.ordno DESC";
		}
		else {
			$_SQL['ORDER'] = "a.ordno desc";	// 기본 정렬
		}

		//$_SQL['ORDER'] = "a.orddt DESC";	// 기본 상품별 주문 그룹 정렬
		$_SQL['ORDER'] = "TG.goodsno DESC, a.orddt DESC";	// 기본 상품별 주문 그룹 정렬


	// WHERE 절 (각 배열 항목당 and 연산임)
		// 상품처리시점
		if ($_value = trim($_GET['processtype'])) {
			$_SQL['WHERE'][] = "TG.processtype = '$_value'";
		}

		// 상품처리시점
		if ($_value = trim($_GET['todaygoods'])) {
			$_SQL['WHERE'][] = "G.todaygoods = '$_value'";
		}

		// 공급업체
		if ($_value = trim($_GET[company])) {
			$_SQL['WHERE'][] = "TG.company = '$_value'";
		}

		// 결제 수단
		if ($_value = trim($_GET[settlekind])) {
			$_SQL['WHERE'][] = "settlekind = '$_value'";
		}

		// 통합 검색
		if ($_value = trim($_GET[sword])) {
			$_SQL['WHERE'][] = (($_GET[skey]=="all") ? "CONCAT( a.ordno, nameOrder, nameReceiver, bankSender, ifnull(m_id,'') )" : $_GET[skey])." LIKE '%$_value%'";
		}

		// 상품 검색
		if ($_value = trim($_GET[sgword])) {
			$_SQL['WHERE'][] = $_GET[sgkey]." LIKE '%$_value%'";

			// 주문 상품 테이블 추가 JOIN
			$_SQL['TABLE'] .= "
					LEFT JOIN ".GD_ORDER_ITEM." AS c
					ON a.ordno=c.ordno
					";

			$_SQL['GROUP'] = "GROUP BY a.ordno";
		}

		// 처리일자
		if ($_GET[regdt][0]){
			if (!$_GET[regdt][1]) $_GET[regdt][1] = date("Ymd");
			$_SQL['WHERE'][] = $_GET[dtkind]." BETWEEN DATE_FORMAT(".$_GET[regdt][0].",'%Y-%m-%d 00:00:00') AND DATE_FORMAT(".$_GET[regdt][1].",'%Y-%m-%d 23:59:59')";
		}

		// 상품구분
		if ($_value = trim($_GET[goodstype])) {
			$_SQL['WHERE'][] = " TG.goodstype = '".$_value."'";
		}

		// 판매상태
		if ($_value = trim($_GET[stats])) {
			switch ($_value) {
				case 2:
					$_SQL['WHERE'][] = " ((NOW() <= TG.enddt OR TG.enddt IS NULL) AND G.runout = 0) ";	// 판매중
					break;
				case 3:
					$_SQL['WHERE'][] = " (NOW() >= TG.enddt OR G.runout = 1) AND (TG.limit_ea <> 0 AND TG.buyercnt+TG.fakestock < TG.limit_ea) ";	// 판매 실패
					break;
				case 4:
					$_SQL['WHERE'][] = " (NOW() >= TG.enddt OR G.runout = 1) AND (TG.limit_ea = 0 OR TG.buyercnt+TG.fakestock >= TG.limit_ea) ";	// 판매종료(=판매완료)
					break;
			}
		}

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
					case "1": $_SQL['WHERE']['OR'][] = "(step=0 and step2 between 1 and 49)"; break;
					case "2": $_SQL['WHERE']['OR'][] = "(step in (1,2) and step2!=0) OR (a.cyn='r' and step2='44' and a.dyn!='e')"; break;
					case "3": $_SQL['WHERE']['OR'][] = "(step in (3,4) and step2!=0)"; break;
					case "60" :
						$_SQL['WHERE']['OR'][] = "(c.dyn='e' and c.cyn='e')";
						$_SQL['TABLE'] .= " left join ".GD_ORDER_ITEM." c on a.ordno=c.ordno";
						$_SQL['GROUP'] = "group by a.ordno";
					break; //교환완료
					case "61" : $_SQL['WHERE']['OR'][] = "oldordno != ''";break; //재주문
					default:
						$_SQL['WHERE']['OR'][] = "step2=$v";
					break;
				}
				$checked[step2][$v] = "checked";
			}
		}

		if (!empty($_SQL['WHERE']['OR'])) $_SQL['WHERE'][] = "(".implode(" OR ",$_SQL['WHERE']['OR']).")";
		unset($_SQL['WHERE']['OR']);

	// 페이징 및 쿼리
		if(!$cfg['orderPageNum'])$cfg['orderPageNum'] = 15;
		$pg = new Page($_GET[page],$cfg['orderPageNum']);
		if ($_GET[mode]=="group") $pg->nolimit = 1;
		$pg->field = $_SQL['FIELD'];
		$pg->setQuery($_SQL['TABLE'],$_SQL['WHERE'],$_SQL['ORDER'],$_SQL['GROUP']);
		$pg->exec();
		$res = $db->query($pg->query);
		unset($_SQL);


// 리스팅 방법에 따른 레코드 묶기
	$arRow = array();

	if ($_GET['list'] == 'goods') {
		// 상품별 그룹
		while ($row = $db->fetch($res)) {
			$goodsno = $row['goodsno'];
			$arRow[$goodsno][] = $row;
		}
	}
	else {
		// 주문건별
		while ($row = $db->fetch($res)) {
			$arRow[] = $row;
		}
	}

	$idx = 0;
?>

<script type="text/javascript" src="../todayshop/todayshop.js"></script>
<script>

function fnChangeList(m) {
	window.location.href = '<?=$_SERVER['PHP_SELF']?>?list='+m;
}

function iciSelect(obj)
{
	var row = obj.parentNode.parentNode;
	row.style.background = (obj.checked) ? "#F9FFA1" : row.getAttribute('bg');

//	var c_table = row.lastChild.firstChild;
//	c_table.style.background = (obj.checked) ? "#F9FFA1" : row.getAttribute('bg');

	return;
	var row = obj.parentNode.parentNode;
	row.style.background = (obj.checked) ? "#F9FFA1" : row.getAttribute('bg');
}

function chkBoxAll(El,mode)
{
	if (!El || !El.length) return;
	for (i=0;i<El.length;i++){
		if (El[i].disabled) continue;
		El[i].checked = (mode=='rev') ? !El[i].checked : mode;
		iciSelect(El[i]);
	}
}

function chkBox2(El,s,e,mode)
{
	if (!El || !El.length) return;
	for (i=s;i<e;i++){
		if (El[i].disabled) continue;
		El[i].checked = (mode=='rev') ? !El[i].checked : mode;
		iciSelect(El[i]);
	}
}

function dnXls(mode)
{
	var fm = document.frmDnXls;
	fm.mode.value = mode;
	fm.target = "ifrmHidden";
	fm.action = "dnXls.php";
	fm.submit();
}

</script>

<div class="title title_top" style="position:relative;padding-bottom:15px">투데이샵 <?=$loc_detail?> 주문리스트<span>투데이샵의 주문을 확인하고 주문상태를 변경합니다</span>
<a href="javascript:manual('<?=$guideUrl?>board/view.php?id=todayshop&no=5')"><img src="../img/btn_q.gif" border=0 hspace=2 align=absmiddle></a>
<!--div style="position:absolute;left:100%;width:231px;height:44px;margin-left:-240px;margin-top:-15px"><a href="../order/post_introduce.php"><img src="../img/btn_postoffic_reserve_go.gif"></a></div-->
</div>
<form>
	<input type="hidden" name="list" value="<?=$_GET['list']?>">
	<input type="hidden" name="goodstype" value="<?=$_GET['goodstype']?>">
	<table class=tb>
	<col class=cellC><col class=cellL style="width:250">
	<col class=cellC><col class=cellL>
	<tr>
		<td><font class=small1>공급업체</font></td>
		<td>
			<select name="company">
				<option value="">= 공급업체 선택 =</option>
				<? for ($i = 0; $i < count($cpData); $i++){ ?>
				<option value="<?=$cpData[$i]['cp_sno']?>" <?=$selected['company'][$cpData[$i]['cp_sno']]?>><?=$cpData[$i]['cp_name']?></option>
				<? } ?>
			</select>
		</td>
	</tr>
	<tr>
		<td><font class=small1>주문검색 (통합)</td>
		<td>
		<select name=skey>
		<option value="all"> = 통합검색 =
		<option value="a.ordno" <?=$selected[skey][a.ordno]?>> 주문번호
		<option value="a.nameOrder" <?=$selected[skey][nameOrder]?>> 주문자명
		<option value="a.nameReceiver" <?=$selected[skey][nameReceiver]?>> 수령자명
		<option value="a.bankSender" <?=$selected[skey][bankSender]?>> 입금자명
		<option value="b.m_id" <?=$selected[skey][m_id]?>> 아이디
		</select>
		<input type=text name=sword value="<?=$_GET[sword]?>" class=line>
		</td>
		<td><font class=small1>상품검색 (선택)</td>
		<td>
		<select name=sgkey>
		<option value="G.goodsnm" <?=$selected[sgkey][goodsnm]?>> 상품명
		<option value="G.brandnm" <?=$selected[sgkey][brandnm]?>> 브랜드
		<option value="G.maker" <?=$selected[sgkey][maker]?>> 제조사
		</select>
		<input type=text name=sgword value="<?=$_GET[sgword]?>" class=line>
		</td>
	</tr>
	<tr>
		<td><font class=small1>주문상태</td>
		<td colspan=3 class=noline>
		<?
			foreach ($r_step as $k=>$v){
				if ($_GET['goodstype'] == 'coupon' && in_array($k, array('0','2','3'))) continue;
		?>
		<div style="float:left; padding-right:10px"><font class=small1 color=5C5C5C><input type=checkbox name=step[] value="<?=$k?>" <?=$checked[step][$k]?>><?=$v?></div>
		<? } ?>
		<div style="float:left; padding-right:10px"><input type=checkbox name=step2[] value="1" <?=$checked[step2][1]?>><font class=small1 color=5C5C5C>주문취소</div>
		<div style="clear:both;"></div>
		<div style="float:left; padding-right:10px"><input type=checkbox name=step2[] value="2" <?=$checked[step2][2]?>><font class=small1 color=5C5C5C>환불관련</div>
		<div style="float:left; padding-right:10px"><input type=checkbox name=step2[] value="3" <?=$checked[step2][3]?>><font class=small1 color=5C5C5C>반품관련</div>
		<div style="float:left; padding-right:10px"><input type=checkbox name=step2[] value="60" <?=$checked[step2][60]?>><font class=small1 color=5C5C5C>교환완료</div>
		<div style="float:left; padding-right:10px"><input type=checkbox name=step2[] value="61" <?=$checked[step2][61]?>><font class=small1 color=5C5C5C>재주문</div>
		<div style="float:left; padding-right:10px"><input type=checkbox name=step2[] value="50" <?=$checked[step2][50]?>><font class=small1 color=5C5C5C>결제시도</div>
		<div style="float:left; padding-right:10px"><input type=checkbox name=step2[] value="54" <?=$checked[step2][54]?>><font class=small1 color=5C5C5C>결제실패</div>
		<div style="float:left; padding-right:10px"><input type=checkbox name=step2[] value="51" <?=$checked[step2][51]?>><font class=small1 color=5C5C5C>PG확인요망</div>
		</td>
	</tr>
	<tr>
		<td><font class=small1>판매상태</td>
		<td colspan=3 class="noline">
			<span class="small1" style="color:5C5C5C; margin-right:20px;">
			<label><input type="radio" name="stats" value="" <?=$checked[stats]['']?>>전체</label>
			<label><input type="radio" name="stats" value="2" <?=$checked[stats][2]?>>판매중</label>
			<label><input type="radio" name="stats" value="4" <?=$checked[stats][4]?>>판매완료</label>
			<label><input type="radio" name="stats" value="3" <?=$checked[stats][3]?>>판매실패</label>
			</span>
		</td>
	</tr>
	<tr>
		<td><font class=small1>처리일자</td>
		<td colspan=3>
		<span class="noline small1" style="color:5C5C5C; margin-right:20px;">
		<input type=radio name=dtkind value="orddt" <?=$checked[dtkind]['orddt']?>>주문일
		<input type=radio name=dtkind value="cdt" <?=$checked[dtkind]['cdt']?>>결제확인일
		<input type=radio name=dtkind value="ddt" <?=$checked[dtkind]['ddt']?>>배송일
		<input type=radio name=dtkind value="confirmdt" <?=$checked[dtkind]['confirmdt']?>>배송완료일
		</span>
		<input type=text name=regdt[] value="<?=$_GET[regdt][0]?>" onclick="calendar(event)" size=12 class=line> -
		<input type=text name=regdt[] value="<?=$_GET[regdt][1]?>" onclick="calendar(event)" size=12 class=line>
		<a href="javascript:setDate('regdt[]',<?=date("Ymd")?>,<?=date("Ymd")?>)"><img src="../img/sicon_today.gif" align=absmiddle></a>
		<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-7 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_week.gif" align=absmiddle></a>
		<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-15 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twoweek.gif" align=absmiddle></a>
		<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-1 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_month.gif" align=absmiddle></a>
		<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-2 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twomonth.gif" align=absmiddle></a>
		<a href="javascript:setDate('regdt[]')"><img src="../img/sicon_all.gif" align=absmiddle></a>
		</td>
	</tr>
	<tr>
		<td><font class=small1>결제방법</td>
		<td colspan=3 class=noline><font class=small1 color=5C5C5C>
		<input type=radio name=settlekind value="" <?=$checked[settlekind]['']?>>전체
		<input type=radio name=settlekind value="c" <?=$checked[settlekind]['c']?>>신용카드
		<input type=radio name=settlekind value="o" <?=$checked[settlekind]['o']?>>계좌이체
		<input type=radio name=settlekind value="v" <?=$checked[settlekind]['v']?>>가상계좌
		<input type=radio name=settlekind value="h" <?=$checked[settlekind]['h']?>>핸드폰
		<input type=radio name=settlekind value="d" <?=$checked[settlekind]['d']?>>전액할인
		<? if ($cfg['settlePg'] == "inipay") { ?>
		<input type=radio name=settlekind value="y" <?=$checked[settlekind]['y']?>>옐로페이
		<? } ?>
		</td>
	</tr>
	</table>
	<div class="button_top">
	<input type=image src="../img/btn_search2.gif">
	</div>
</form>

<div style="padding-top:15px"></div>

<form name=frmList method=post action="./indb.todayshop_list.php" onsubmit="return chkForm(this)">
<input type=hidden name=mode value="chgAll">
<table width=100% cellpadding=0 cellspacing=0>
<tr>
	<td align=right>
	<img src="../img/today_list01<?=($_GET['list']!='order') ? 'on' : ''?>.gif" onMouseOver='this.src="../img/today_list01on.gif";' onMouseOut='this.src="../img/today_list01<?=($_GET['list']!='order') ? 'on' : ''?>.gif";' border=0 align=absmiddle onClick="fnChangeList('goods')" class="hand">
	<img src="../img/today_list02<?=($_GET['list']=='order') ? 'on' : ''?>.gif" onMouseOver='this.src="../img/today_list02on.gif";' onMouseOut='this.src="../img/today_list02<?=($_GET['list']=='order') ? 'on' : ''?>.gif";' border=0 align=absmiddle onClick="fnChangeList('order')" class="hand">
	</td>
</tr>
<tr><td height=3></td></tr>
</table>
<? if ($_GET['list'] == 'goods') { ?>
<!-- 상품별 리스트-->
	<table width=100% cellpadding=0 cellspacing=0 border=0>
	<colgroup>
		<col align="left">
		<col width="150">
		<col width="80">
		<col width="80">
		<col width="40">
		<col width="120">
		<col width="150">
		<col width="100">
		<col width="100">
		<col width="80">
		<col width="100">
		<col width="60">
	</colgroup>
	<tr><td class=rnd colspan=12></td></tr>
	<tr class=rndbg>
		<th style="padding-left:10px;">상품명</th>
		<th>진행기간</th>
		<th>판매량</th>
		<th>판매상태</th>
		<th>번호</th>
		<th>주문일</th>
		<th>주문번호</th>
		<th>주문자</th>
		<th>받는분</th>
		<th>결제</th>
		<th>금액</th>
		<th>주문상태</th>
	</tr>
	<tr><td class=rnd colspan=12></td></tr>
	<?
	$arRow_keys = array_keys($arRow);
	for ($i=0,$max=sizeof($arRow_keys);$i<$max;$i++) {
		unset($supply); unset($selected);

		$data = $arRow[ $arRow_keys[$i] ];
		$data_size = sizeof($data);

		$item = $data[0];

		// 주문번호 긁기
		$ordnos = array();
		for ($j=0;$j<$data_size;$j++) {
			$ordnos[] = $data[$j]['ordno'];
		}
	?>
	<tr align=center bg="">
		<td class="noline small4" valign="top" style="padding:9px">
			<?=$item[goodsnm]?>
			<p>
			<img src="../img/today_list03.gif" onMouseOver='this.src="../img/today_list03on.gif";' onMouseOut='this.src="../img/today_list03.gif";' border=0 onClick="<?=($item['stats'] > 1) ? 'nsTodayshopControl.order.view('.$item['goodsno'].');' : 'nsTodayshopControl.order.notAvail();'?>" class="hand">
			</p>
		</td>
		<td class="noline small4" valign="top" style="padding:9px"><?=$item['startdt']?><br/> ~ <br/><?=$item['enddt']?></td>
		<td class="noline small4" valign="top" style="padding:9px"><?=number_format($item['buyercnt'] + $item['fakestock'])?> (<?=number_format($item['buyercnt'])?> + <?=number_format($item['fakestock'])?>) / <?=($item['limit_ea'] > 0) ? number_format($item['limit_ea']) : '미지정'?></td>
		<td class="noline small4" valign="top" style="padding:9px"><?=$_arStats[$item['stats']]?></td>
		<td colspan="8">
			<table width="100%">
			<colgroup>
			<col width="40" align="center">
			<col width="120" align="center">
			<col width="150" align="center">
			<col width="100" align="center">
			<col width="100" align="center">
			<col width="80" align="center">
			<col width="100" align="center">
			<col width="60" align="center">
			</colgroup>
			<? for ($j=0;$j<$data_size;$j++) { ?>
			<?
				$row = $data[$j];

				$bgcolor = ($row[step2]) ? "#F0F4FF" : "#ffffff";
				$disabled = ($row[step2]) ? "disabled" : "";

				$stepMsg = $step = getStepMsg($row[step],$row[step2],$row[ordno]);
				if(strlen($step) > 10) $step = substr($step,10);

				if ( $row[deliverycode] || $row[cntDv] ) {
					$step = "<a href=\"javascript:popup('popup.delivery.php?ordno=$row[ordno]',650,500)\"><font color=0074BA><b><u>$step</u></b></font></a>";
				}

				$grp[settleprice][''] += $row[prn_settleprice];
			?>
			<tr height="30">
				<td class=noline valign="top" style="padding-top:7px;"><span class="ver8" style="color:#616161"><?=($pg->idx--)?></span></td>
				<td><font class=ver81 color=616161><?=substr($row[orddt],0,-3)?></font></td>
				<td>
					<a href="view.php?ordno=<?=$row[ordno]?>"><font class=ver81 color=0074BA><b><?=$row[ordno]?></b></font></a>
					<a href="javascript:popup('popup.order.php?ordno=<?=$row[ordno]?>',800,600)"><img src="../img/btn_newwindow.gif" border=0 align=absmiddle></a>
				</td>
				<td>
					<? if ($row[m_id]) { ?><span id="navig" name="navig" m_id="<?=$row[m_id]?>" m_no="<?=$row[m_no]?>"><? } ?><font class=small1 color=0074BA>
					<b><?=$row[nameOrder]?></b><? if ($row[m_id]){ ?> (<?=$row[m_id]?>)</font><? if ($row[m_id]) { ?></span><? } ?>
					<? } ?>
				</td>
				<td><font class=small1 color=444444><?=$row[nameReceiver]?></td>
				<td class=small4><?=$r_settlekind[$row[settlekind]]?></td>
				<td class=ver81><b><?=number_format($row[prn_settleprice])?></b></td>
				<td class=small4><?=($row[goodstype] == 'coupon') ? str_replace("배송","발급",$step) : $step;?></td>
			</tr>
			<? if ($j < $data_size-1) {?><tr><td colspan=9 bgcolor=E4E4E4></td></tr><? } ?>
			<? } ?>
			</table>
		</td>
	</tr>
	<tr><td colspan=12 bgcolor=E4E4E4></td></tr>

	<? } ?>
	<?
		$cnt = $pr * ($idx+1);
		$s = $idx_grp - $cnt;
	?>
	<tr>
		<td align=right height=30 colspan=12 style=padding-right:8>합계: <!--(<?=$cnt?>건)--> <font class=ver9><b><?=number_format($grp[settleprice][$preStepMsg])?></font>원</b></td>
		<td></td>
	</tr>
	<tr bgcolor=#f7f7f7 height=30>
		<td colspan=12 align=right style=padding-right:8>전체합계 : <span class=ver9><b><?=number_format(@array_sum($grp[settleprice]))?>원</b></span></td>
		<td></td>
	</tr>
	<tr><td height=4 colspan="12"></td></tr>
	<tr><td colspan=12 class=rndline></td></tr>
	</table>

<!-- //상품별 리스트-->
<? } else { ?>
<!-- 주문별 리스트-->
	<table width=100% cellpadding=0 cellspacing=0 border=0>
	<colgroup>
		<col width="40">
		<col width="100">
		<col width="130">
		<col align="left">
		<col width="80">
		<col width="80">
		<col width="150">
		<col width="80">
		<col width="100">
		<col width="60">
	</colgroup>
	<tr><td class=rnd colspan=12></td></tr>
	<tr class=rndbg>
		<th>번호</th>
		<th>주문일</th>
		<th>주문번호</th>
		<th>상품명</th>
		<th>판매상태</th>
		<th>주문자</th>
		<th>받는분</th>
		<th>결제</th>
		<th>금액</th>
		<th>주문상태</th>
	</tr>
	<tr><td class=rnd colspan=12></td></tr>
	<?
	$arRow_keys = array_keys($arRow);
	for ($i=0,$max=sizeof($arRow_keys);$i<$max;$i++) {
		unset($supply); unset($selected);
		$data = $row = $arRow[ $arRow_keys[$i] ];

		$item = $data;

		// 주문번호 긁기
		$ordnos = array();

		$bgcolor = ($row[step2]) ? "#F0F4FF" : "#ffffff";
		$disabled = ($row[step2]) ? "disabled" : "";

		$stepMsg = $step = getStepMsg($row[step],$row[step2],$row[ordno]);
		if(strlen($step) > 10) $step = substr($step,10);

		if ( $row[deliverycode] || $row[cntDv] ) {
			$step = "<a href=\"javascript:popup('popup.delivery.php?ordno=$row[ordno]',650,500)\"><font color=0074BA><b><u>$step</u></b></font></a>";
		}

		$grp[settleprice][''] += $row[prn_settleprice];
	?>
	<tr height="35" align=center bg="">
		<td class=noline><font class=ver8 color=616161><?=($pg->idx--)?></span></td>
		<td><font class=ver81 color=616161><?=substr($row[orddt],0,-3)?></font></td>
		<td>
			<a href="view.php?ordno=<?=$row[ordno]?>"><font class=ver81 color=0074BA><b><?=$row[ordno]?></b></font></a>
			<a href="javascript:popup('popup.order.php?ordno=<?=$row[ordno]?>',800,600)"><img src="../img/btn_newwindow.gif" border=0 align=absmiddle></a>
		</td>
		<td class="noline small4"><?=$item[goodsnm]?></td>
		<td class="noline small4"><?=$_arStats[$item['stats']]?></td>
		<td>
			<? if ($row[m_id]) { ?><span id="navig" name="navig" m_id="<?=$row[m_id]?>" m_no="<?=$row[m_no]?>"><? } ?><font class=small1 color=0074BA>
			<b><?=$row[nameOrder]?></b><? if ($row[m_id]){ ?> (<?=$row[m_id]?>)</font><? if ($row[m_id]) { ?></span><? } ?>
			<? } ?>
		</td>
		<td><font class=small1 color=444444><?=$row[nameReceiver]?></td>
		<td class=small4><?=$r_settlekind[$row[settlekind]]?></td>
		<td class=ver81><b><?=number_format($row[prn_settleprice])?></b></td>
		<td class=small4><?=($row[goodstype] == 'coupon') ? str_replace("배송","발급",$step) : $step;?></td>
	</tr>
	<tr><td colspan=12 bgcolor=E4E4E4></td></tr>
	<? } ?>
	<?
		$cnt = $pr * ($idx+1);
		$s = $idx_grp - $cnt;
	?>
	<tr>
		<td align=right height=30 colspan=12 style=padding-right:8>합계: <!--(<?=$cnt?>건)--> <font class=ver9><b><?=number_format($grp[settleprice][$preStepMsg])?></font>원</b></td>
		<td></td>
	</tr>
	<tr bgcolor=#f7f7f7 height=30>
		<td colspan=12 align=right style=padding-right:8>전체합계 : <span class=ver9><b><?=number_format(@array_sum($grp[settleprice]))?>원</b></span></td>
		<td></td>
	</tr>
	<tr><td height=4 colspan="13"></td></tr>
	<tr><td colspan=12 class=rndline></td></tr>
	</table>
<!-- //주문별 리스트-->
<? } ?>
<div class=pageNavi align=center><font class=ver8><?=$pg->page[navi]?></font></div>
<div class=button>
<input type=image src="../img/btn_modify.gif">
<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a>
</div>

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">주문일 또는 주문처리흐름 방식으로 주문내역을 정렬하실 수 있습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">주문상태를 변경하시려면 주문건 선택 - 처리단계선택 후 수정버튼을 누르세요.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">주문상태변경을 통해 각 주문처리단계 (주문접수, 입금확인, 배송준비, 배송중, 배송완료) 로 빠르게  처리하실 수 있습니다.</td></tr>
<tr><td height=8></td></tr>
<tr><td><font class=def1 color=ffffff><b>- 카드결제주문은 아래와 같은 경우가 발생할 수 있습니다. (필독하세요!) -</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">해당 PG사 관리자모드에는 승인이 되었으나, 주문리스트에서 주문상태가 '입금확인'이 아닌 '결제시도'로 되어 있는 경우가 발생될 수 있습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">이는 중간에 통신상의 문제로 리턴값을 제대로 받지 못해 주문상태가 변경이 되지 않은 것입니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">즉, 이와같이 승인이 되었지만 주문상태가 '결제시도'인 경우 해당주문건의 주문상세내역 페이지에서 "결제시도, 실패 복원" 처리를 하시면 주문처리상태가 "입금확인"으로 수정됩니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">그러나 정상적인 리턴값을 받아 주문처리상태가 변경된 건이기에 이에 대해서는 정확한 결제로그를 주문상세내역페이지에서 확인을 할 수 없습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">또한 고객이 카드결제로 주문을 1건 결제했는데 간혹 PG사 쪽에서는 2건이 승인(중복승인)되는 경우가 있습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">이 경우는 해당 PG사의 관리자모드로 가서 중복승인된 2건중에 1건을 승인취소 해주시면 됩니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">중복승인건을 체크해서 바로 승인취소처리하지 않으면 미수금이 발생되어 쌓이게 되고, 해당 PG사로부터 거래중지요청 등의 불이익을 받을 수 있습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">결제승인건의 주문상태와 중복승인건 처리는 세심하게 체크해야 하며 이에 대한 책임은 쇼핑몰 운영자에게 있습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">항상 카드결제건은 이곳 주문리스트와 PG사에서 제공하는 관리페이지의 결제승인건과 비교하면서 주의깊게 체크하여 처리하시기 바랍니다.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

<script>window.onload = function(){ UNM.inner();};</script>
<? @include dirname(__FILE__) . "/../interpark/_order_list.php"; // 인터파크_인클루드 ?>

<? include "../_footer.php"; ?>
