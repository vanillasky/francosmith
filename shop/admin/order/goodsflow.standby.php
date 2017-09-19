<?php
$location = "택배연동 서비스 > 굿스플로 대기 리스트";
include "../_header.php";
include "../../lib/page.class.php";
include "../../conf/config.pay.php";

$gf = Core::loader('goodsflow_v2');

if (get_magic_quotes_gpc()) {
	stripslashes_all($_POST); stripslashes_all($_GET); stripslashes_all($_COOKIE);
}

// 통함 주문 설정
	@include(dirname(__FILE__).'/_cfg.integrate.php');

// 기본 값
	$now = time();
	$today = mktime(0,0,0,date('m',$now), date('d',$now), date('Y',$now));

// get 파라미터 처리 및 기본값 적용
	unset($_GET['x'],$_GET['y']);

	$_GET['sort']			= !empty($_GET['sort']) ? $_GET['sort'] : 'o.orddt desc';		// 정렬
	$_GET['ord_status']		= !empty($_GET['ord_status']) ? $_GET['ord_status'] : -1;	// 처리상태
	$_GET['settlekind']		= !empty($_GET['settlekind']) ? $_GET['settlekind'] : '';		// 결제수단
	$_GET['ord_type']		= !empty($_GET['ord_type']) ? $_GET['ord_type'] : '';		// 접수유형
	$_GET['skey']			= !empty($_GET['skey']) ? $_GET['skey'] : '';					// 주문검색 조건
	$_GET['sword']			= !empty($_GET['sword']) ? trim($_GET['sword']) : '';					// 주문검색 키워드

	$_GET['sgkey']			= !empty($_GET['sgkey']) ? $_GET['sgkey'] : '';					// 상품검색 조건
	$_GET['sgword']			= !empty($_GET['sgword']) ? trim($_GET['sgword']) : '';				// 상품검색 키워드

	$_GET['page']			= !empty($_GET['page']) ? $_GET['page'] : 1;						// 페이지
	$_GET['page_num']		= !empty($_GET['page_num']) ? $_GET['page_num'] : ($cfg['orderPageNum'] ? $cfg['orderPageNum'] : 20);	// 페이지당 레코드수

// 배송업체 정보
	$query = "select * from ".GD_LIST_DELIVERY."";
	$res = $db->query($query);
	while ($data=$db->fetch($res)){
		$_delivery[] = $data;
		$r_delivery[$data[deliveryno]] = $data[deliverycomp];
	}

// 연동상태
	$r_type = array(
	'casebyorder' => '주문번호별',
	'casebygoods' => '상품별',
	'package' => '합포장',
	'partial' => '부분배송',
	);

// 검색절 만듦

	#0. 초기화
		$arWhere   = array();
		$arWhere[] = " GF.status = 'print_invoice' ";

	#1. 판매 채널

	#2. 주문 상태
		$arWhere[] = " o.step2 < 40 ";

		if ($_GET['ord_status'] > -1) {
			$arWhere[] = " o.step = ".(int)$_GET['ord_status'];
		}
		else {
			$arWhere[] = " (o.step = 1 OR  o.step = 2) ";
		}

	#3. 결제 수단
		if($_GET['settlekind']) {
			$arWhere[] = $db->_query_print('o.settlekind= [s]',$_GET['settlekind']);
		}

	#4. 통합 검색
		if($_GET['sword'] && $_GET['skey']) {
			$es_sword = $db->_escape($_GET['sword']);
			switch($_GET['skey']) {
				case 'all':
					$_where = array();

					// 이나무의 데이터를 가져오므로, 필드를 매핑해줌.
					$_skey_map = array(
						'o.ord_name' => 'o.nameOrder',
						'o.rcv_name' => 'o.nameReceiver',
						'o.pay_bank_name' => 'o.bankSender',
						'm.m_id' => 'm.m_id',
						'o.ord_phone' => 'o.phoneOrder',
						'o.rcv_phone' => 'o.phoneReceiver',
						'o.rcv_address' => 'o.address',
						'o.dlv_no' => 'o.deliverycode',
					);

					foreach($integrate_cfg['skey'] as $cond) {
						if (preg_match($cond['pattern'],$es_sword)) {

							$_condition = isset($_skey_map[$cond['field']]) ? $_skey_map[$cond['field']] : $cond['field'];

							if ($cond['condition'] == 'like') $_condition .= ' like \'%'.$es_sword.'%\'';
							else if ($cond['condition'] == 'equal') $_condition .= ' = \''.$es_sword.'\'';
							else continue;

							$_where[] = $_condition;
						}
					}

					if (sizeof($_where) > 0) $arWhere[] = "(".implode(' OR ',$_where).")";
					break;
				case 'ordno': $arWhere[] = "o.ordno = '{$es_sword}'"; break;
				case 'nameOrder': $arWhere[] = "o.nameOrder like '%{$es_sword}%'"; break;
				case 'nameReceiver': $arWhere[] = "o.nameReceiver like '%{$es_sword}%'"; break;
				case 'bankSender': $arWhere[] = "o.bankSender like '%{$es_sword}%'"; break;
				case 'm_id': $arWhere[] = "m.m_id = '{$es_sword}'"; break;
				case 'phoneOrder': $arWhere[] = "o.phoneOrder like '%{$es_sword}%'"; break;
				case 'phoneReceiver': $arWhere[] = "o.phoneReceiver like '%{$es_sword}%'"; break;
				case 'address': $arWhere[] = "o.address like '%{$es_sword}%'"; break;
				case 'deliverycode': $arWhere[] = "o.deliverycode like '%{$es_sword}%'"; break;
				case 'name': $arWhere[] = "a.name like '%{$es_sword}%'"; break;
			}
		}

	#5. 처리일자

	#6. 상품검색
		$join_GD_PURCHASE = '';
		if($_GET['sgword'] && $_GET['sgkey']) {
			$es_sgword = $db->_escape($_GET['sgword']);
			switch($_GET['sgkey']) {
				case 'goodsnm': $arWhere[] = "oi.goodsnm like '%{$es_sgword}%'"; break;
				case 'brandnm': $arWhere[] = "EXISTS (SELECT ordno FROM ".GD_ORDER_ITEM." AS _oi WHERE _oi.brandnm like '%{$es_sgword}%' AND _oi.ordno = o.ordno) "; break;
				case 'maker': $arWhere[] = "EXISTS (SELECT ordno FROM ".GD_ORDER_ITEM." AS _oi WHERE _oi.maker like '%{$es_sgword}%' AND _oi.ordno = o.ordno) "; break;
				case 'goodsno': $arWhere[] = "oi.goodsno like '%{$es_sgword}%'"; break;
				case 'purchase': $arWhere[] = "pch.comnm like '%{$es_sgword}%'"; $join_GD_PURCHASE = 'INNER JOIN '.GD_PURCHASE_GOODS.' AS pchg ON pchg.goodsno = oi.goodsno INNER JOIN '.GD_PURCHASE.' AS pch ON pchg.pchsno = pch.pchsno'; break;
			}
		}

	#7. 소비자피해보상보험

	#8. 결제시 적용

	#9. 홍보채널

	#10. 접수유형 (별도 필드가 없고, inflow 값이 sugi 인 레코드)
		if($_GET['ord_type'] == 'offline') {
			$arWhere[] = 'o.inflow = \'sugi\'';
		}
		else if ($_GET['ord_type'] == 'online') {
			$arWhere[] = 'o.inflow <> \'sugi\'';
		}

	#xx. 페이징 query 생성
		$_paging_query = http_build_query($_GET);	// php5 전용함수. but! lib.func.php 안에 php4용 있음.

// 쿼리 실행
	$db_table = "
			".GD_GOODSFLOW." AS GF

			INNER JOIN ".GD_GOODSFLOW_ORDER_MAP." AS OD
			ON GF.sno = OD.goodsflow_sno

			LEFT JOIN ".GD_ORDER." as o
			ON OD.ordno = o.ordno

			LEFT JOIN ".GD_ORDER_ITEM." as oi
			ON OD.ordno = oi.ordno AND OD.item_sno = oi.sno

			LEFT JOIN ".GD_MEMBER." AS m
			ON o.m_no=m.m_no
			";
	$db_table .= $join_GD_COUPON_ORDER;
	$db_table .= $join_GD_PURCHASE;
	$orderby = $_GET['sort'];
	$pg = new Page($_GET['page'],$_GET['page_num']);
	$pg->vars['page']= $_paging_query;

	$pg->cntQuery = "SELECT COUNT(DISTINCT GF.UniqueCd) FROM ".$db_table." WHERE ".implode(' AND ',$arWhere);

	$pg->field = "
		GF.TransUniqueCd
	";

	$pg->setQuery($db_table,$arWhere,$orderby,'GROUP BY GF.TransUniqueCd, o.ordno');
	$pg->exec();
	$res = $db->query($pg->query);

	$TransUniqueCds = array();

	while ($row = $db->fetch($res,1)) {
		$TransUniqueCds[] = "'".$row['TransUniqueCd']."'";
	}

	$query = "
	SELECT
		GF.TransUniqueCd,
		GF.UniqueCd,
		GF.type,
		GF.status,
		o.*,

		m.m_id,
		m.m_no,

		oi.goodsnm,
		oi.goodsno,
		oi.sno,
		oi.istep

	FROM
		".$db_table."

	WHERE
		GF.TransUniqueCd IN (".implode(',',$TransUniqueCds).")

	/*GROUP BY GF.TransUniqueCd*/

	ORDER BY
		".$orderby."
	";
	$res = $db->query($query);

	$arRows = array();
	// 배송번호 > 주문번호 > 주문정보 순으로 담는다.
	while ($row = $db->fetch($res,1)) {
		$arRows[$row['UniqueCd']][] = $row;
	}
?>
<script language='javascript'>
/**
* 라인색상 활성화
*/
function iciSelect(obj) {
	var row = obj.parentNode.parentNode;
	row.style.background = (obj.checked) ? "#F9FFA1" : row.getAttribute('bg');
	if($('tr_'+obj.value)) {
		$('tr_'+obj.value).style.background=row.style.background;
	}
}

/**
* 전체선택
*/
var chkBoxAll_flag=true;
function chkBoxAll() {
	$$(".chk_TransUniqueCd").each(function(item){
		if(item.disabled==true) return;
		item.checked=chkBoxAll_flag;
		//iciSelect(item);
	});
	chkBoxAll_flag=!chkBoxAll_flag;
}

function fnGoodsflowDelivery() {

	var f = document.fmList;

	if (f.target_type.value == 'choice') {
		var chk_size = $$('input[name="TransUniqueCd[]"]:checked').size();

		if (chk_size < 1) {
			alert('선택된 주문건이 없습니다.');
		}
		else {
			if (confirm('선택된 주문 ' + chk_size + '건을 배송처리 합니다.')) {
				f.submit();
			}
		}
	}
	else {
		if (confirm('검색된 주문 <?=$pg->recode['total']?>건을 배송처리 합니다.')) {
			f.submit();
		}
	}

	return false;

}

function __anchor(url) {
	var a = document.createElement("a");
	a.style.display = "none";
	a.setAttribute("href", url);
	document.body.appendChild(a);

	if (!a.click) {
		window.location = url;
	}
	else {
		a.click();
	}
}

function fnCancelGFinvoice(TransUniqueCd) {
	if (confirm('선택한 주문건의 송장번호 발급을 취소하시겠습니까?')) {
		var f = document.fmList;
		__anchor(f.action + '?process=cancel&TransUniqueCd='+TransUniqueCd);
	}
}

function fnGoodsflowReinvoice(TransUniqueCd) {
	if (confirm('선택한 주문건의 송장번호를 재출력 하시겠습니까?')) {
		var f = document.fmList;
		popup_return(f.action + '?process=reinvoice&TransUniqueCd='+TransUniqueCd,'GODO_GF_WIN',800,650,0,0,1);
	}
}

</script>

<div class="title title_top">굿스플로 배송대기리스트<span>굿스플로 연동서비스를 통해 송장번호를 발급 받은 주문리스트 입니다.</span>
<a href="javascript:manual('<?=$guideUrl?>board/view.php?id=order&no=37')"><img src="../img/btn_q.gif" border=0 hspace=2 align=absmiddle></a>
</div>

<form name="frmSearch" id="frmSearch" method="get" action="">

	<table class="tb">
	<col class="cellC"><col class="cellL">
	<tr>
		<td><span class="small1">주문검색</span></td>
		<td>

			<select name="ord_status">
				<option value="-1"> = 주문상태 = </option>
				<option value="1" <?=$_GET['ord_status'] == 1 ? 'selected' : ''?>>입금확인</option>
				<option value="2" <?=$_GET['ord_status'] == 2 ? 'selected' : ''?>>배송준비중</option>
			</select>

			<select name="settlekind">
				<option value=""> = 결제수단 = </option>
				<? foreach ($integrate_cfg['pay_method'] as $k=>$v) { ?>
				<? if ($k == 'p') continue; ?>
				<option value="<?=$k?>" <?=$_GET['settlekind'] == $k ? 'selected' : ''?>><?=$v?></option>
				<? } ?>
			</select>

			<select name="ord_type">
				<option value=""> = 접수유형 = </option>
				<option value="online" <?=$_GET['ord_type'] == 'online' ? 'selected' : ''?>>온라인접수</option>
				<option value="offline" <?=$_GET['ord_type'] == 'offline' ? 'selected' : ''?>>수기접수</option>
			</select>

			<select name="skey">
				<option value="all"> = 통합검색 = </option>
				<option value="ordno"			<?=($_GET['skey'] == 'ordno') ? 'selected' : ''?>			>주문번호</option>
				<option value="nameOrder"		<?=($_GET['skey'] == 'nameOrder') ? 'selected' : ''?>		>주문자명</option>
				<option value="m_id"			<?=($_GET['skey'] == 'm_id') ? 'selected' : ''?>			>주문자ID</option>
				<option value="phoneOrder"			<?=($_GET['skey'] == 'phoneOrder') ? 'selected' : ''?>			>주문자연락처</option>
				<option value="bankSender"		<?=($_GET['skey'] == 'bankSender') ? 'selected' : ''?>	>입금자명</option>
				<option value="nameReceiver"	<?=($_GET['skey'] == 'nameReceiver') ? 'selected' : ''?>	>수령자명</option>
				<option value="phoneReceiver"	<?=($_GET['skey'] == 'phoneReceiver') ? 'selected' : ''?>	>수령자연락처</option>
				<option value="address"	<?=($_GET['skey'] == 'address') ? 'selected' : ''?>	>배송지주소</option>
				<option value="deliverycode"	<?=($_GET['skey'] == 'deliverycode') ? 'selected' : ''?>	>송장번호</option>
			</select>

			<input type="text" name="sword" value="<?=htmlspecialchars($_GET['sword'])?>" class="line" />

		</td>
	</tr>
	<tr>
		<td><span class="small1">상품검색</span></td>
		<td>
			<select name="sgkey">
				<option value="goodsnm"	<?=($_GET['sgkey'] == 'goodsnm') ? 'selected' : ''?>	>상품명</option>
				<option value="goodsno"	<?=($_GET['sgkey'] == 'goodsno') ? 'selected' : ''?>	>상품번호</option>
				<option value="brandnm"	<?=($_GET['sgkey'] == 'brandnm') ? 'selected' : ''?>	>브랜드</option>
				<option value="maker"	<?=($_GET['sgkey'] == 'maker') ? 'selected' : ''?>		>제조사</option>
				<option value="purchase"	<?=($_GET['sgkey'] == 'purchase') ? 'selected' : ''?>	>사입처(공급처)</option>
			</select>
			<input type=text name="sgword" value="<?=htmlspecialchars($_GET['sgword'])?>" class="line"/>
		</td>
	</tr>
	</table>

	<div class="button_top">
	<table width="100%">
	<tr>
		<td width="30%" align="center"><input type="image" src="../img/btn_search2.gif"/></td>
	</tr>
	</table>
	</div>

	<div style="padding-top:15px"></div>

	<table width="100%" cellpadding="0" cellspacing="0">
	<tr>
		<td align="left">&nbsp;
		</td>

		<td align="right">

		<select name="page_num" onchange="this.form.submit();">
			<?
			$r_pagenum = array(10,20,40,60,100);
			if ((int)$cfg['orderPageNum'] > 0 && !in_array((int)$cfg['orderPageNum'] ,$r_pagenum)) {
				$r_pagenum[] = (int)$cfg['orderPageNum'];
				sort($r_pagenum);
			}
			foreach ($r_pagenum as $v){
			?>
			<option value="<?=$v?>" <?=$_GET['page_num'] == $v ? 'selected' : ''?>><?=$v?>개 출력</option>
			<? } ?>
		</select>
		</td>
	</tr>
	</table>
</form>

<br>

<form name="fmList" method="post" action="indb.goodsflow.php" target="_self">
<input type="hidden" name="process" value="delivery">
<input type="hidden" name="mode" value="">
<input type="hidden" name="query" value="<?=base64_encode($query)?>">

<table width="100%" cellpadding="0" cellspacing="0" border="0">

<col align="center" width="40"/>
<col align="center" width="50" />
<col align="center" width="110" />
<col align="center" width="130" />
<col align="left" />
<col align="center" width="100" />
<col align="center" width="120" />
<col align="center" width="70" />
<col align="center" width="70" />
<col align="center" width="60" />
<tr><td class="rnd" colspan="10"></td></tr>
<tr class="rndbg">
	<th><span onclick="chkBoxAll()" style="cursor:pointer">선택</span></th>
	<th>번호</th>
	<th>주문일시</th>
	<th>주문번호</th>
	<th>주문상품</th>
	<th>주문자</th>
	<th>송장번호</th>
	<th>연동상태</th>
	<th>처리상태</th>
	<th>취소</th>
</tr>
<tr><td class="rnd" colspan="10"></td></tr>
</table>
<table width="100%" cellpadding="0" cellspacing="0" border="1" bordercolor="#E4E4E4" style="border-collapse:collapse;">
<col align="center" width="40"/>
<col align="center" width="50" />
<col align="center" width="110" />
<col align="center" width="130" />
<col align="left" />
<col align="center" width="100" />
<col align="center" width="120" />
<col align="center" width="70" />
<col align="center" width="70" />
<col align="center" width="60" />
<?
$pg->idx++;
foreach ($arRows as $rows) {

	$rowspan = sizeof($rows);
	$rowskip = 0;

	foreach($rows as $row) {
		$pg->idx = $rowskip == 0 ? $pg->idx - 1 : $pg->idx;
?>
<tr height="25" bgcolor="#ffffff" bg=""#ffffff" align=center>

	<? if ($rowskip == 0) { ?>
		<td <?=$rowspan > 1 ? 'rowspan="'.$rowspan.'"' : ''?> class="noline"><input type="checkbox" name="TransUniqueCd[]" value="<?=$row['TransUniqueCd']?>" class="chk_TransUniqueCd"></td>
	<? } ?>

	<? if ($rowskip == 0) { ?>
		<td <?=$rowspan > 1 ? 'rowspan="'.$rowspan.'"' : ''?>><font class="ver81" color="#616161"><?=$pg->idx?></font></td>
	<? } ?>

	<td><font class="ver81" color="#616161"><?=$row['orddt']?></font></td>
	<td>
		<a href="view.php?ordno=<?=$row['ordno']?>"><font class=ver81 color=<?=$row['flg_inflow'] == 'sugi' ? 'ED6C0A' : '0074BA'?>><b><?=$row['ordno']?><?=$row['flg_inflow'] == 'sugi' ? '<span class="small1">(수기)</span>' : ''?></b></font></a>
		<a href="javascript:popup('popup.order.php?ordno=<?=$row['ordno']?>',800,600)"><img src="../img/btn_newwindow.gif" border=0 align=absmiddle></a>
	</td>
	<td align="left">
		<div style="padding-left:3px;">
			<? if (!empty($row[old_ordno])){	?><a href="javascript:popup('popup.order.php?ordno=<?=$row['ordno']?>',800,600)"><img src="../img/icon_twice_order.gif"></a><? } ?>
			<? if ($row['flg_escrow']=="y"){	?><a href="javascript:popup('popup.order.php?ordno=<?=$row['ordno']?>',800,600)"><img src="../img/btn_escrow.gif"></a><? } ?>
			<? if ($row['flg_egg']=="y"){		?><a href="javascript:popup('popup.order.php?ordno=<?=$row['ordno']?>',800,600)"><img src="../img/icon_guar_order.gif"></a><? } ?>
			<? if (!empty($row['flg_cashreceipt'])){	?><img src="../img/icon_cash_receipt.gif"><? } ?>
			<? if ($row['flg_cashbag']=="Y"){		?><a href="javascript:popup('popup.order.php?ordno=<?=$row['ordno']?>',800,600)"><img src="../img/icon_okcashbag.gif" align=absmiddle></a><? } ?>
			<font class=small1 color=444444><?=$row['goodsnm']?></font>
		</div>
	</td>
	<td><? if ($row['m_id']) { ?><span id="navig" name="navig" m_id="<?=$row['m_id']?>" m_no="<?=$row['m_no']?>"><? } ?><font class=small1 color=0074BA>
		<b><?=$row['nameOrder']?></b><? if ($row['m_id']){ ?> (<?=$row['m_id']?>)</font><? if ($row['m_id']) { ?></span><? } ?>
		<? } ?>
	</td>

	<? if ($rowskip == 0) { ?>
		<td <?=$rowspan > 1 ? 'rowspan="'.$rowspan.'"' : ''?>>
		<?=$row['type'] == 'casebyorder' ? '' : ''?>
		<?=$r_delivery[$row['deliveryno']]?>
		/
		<?=$row['deliverycode']?>
		<a href="javascript:void(0);" onClick="fnGoodsflowReinvoice('<?=$row['TransUniqueCd']?>');return false;"><img src="../img/btn_reprint.gif"></a>
		</td>
	<? } ?>

	<? if ($rowskip == 0) { ?>
		<td <?=$rowspan > 1 ? 'rowspan="'.$rowspan.'"' : ''?>>
		<?=$r_type[$row['type']]?>
		</td>
	<? } ?>

	<td><font class="small1" color="#444444"><?=getStepMsg($row['step'],$row['step2'])?></font></td>

	<? if ($rowskip == 0) { ?>
		<td <?=$rowspan > 1 ? 'rowspan="'.$rowspan.'"' : ''?>><a href="javascript:void(0);" onClick="fnCancelGFinvoice('<?=$row['TransUniqueCd']?>');return false;"><img src="../img/btn_cancel_s01.gif"></a></td>
	<? } ?>
</tr>
<?
		$rowskip++;
		}
	}
?>
</table>

<div class=pageNavi align=center>
	<font class=ver8><?=$pg->page[navi]?></font>
</div>

<div class="noline" style="border:1px solid #cccccc;padding:10px">

	<select name="target_type" align="absmiddle">
		<option value="choice">선택된 주문건을</option>
		<option value="query">검색된 주문건을</option>
	</select>

	<button class="default-btn" onClick="fnGoodsflowDelivery();">배송중 처리</button>
</div>

</form>
<br>

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">굿스플로 연동서비스를 통해 송장번호를 발급 받은 내역입니다. 발급 받은 내역 및 예치금액 등의 사용정보는 굿스플로를 통해 자세히 확인하실 수 있습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">운송장 번호가 채번된 주문건은 배송준비중 상태입니다.</td></tr>
<tr><td height=8></td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">택배사에서 상품을 수거하게 되면 운송 정보가 연동되어 자동으로 배송중 상태로 변경됩니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">기존의 방식처럼 수동으로 상태를 변경할 수도 있으며, 수동으로 변경된 주문건은 자동연동 정보에서 제외됩니다.</td></tr>
</table>
</div>

<script>cssRound('MSG01')</script>

<script>window.onload = function(){ UNM.inner();};</script>

<? include "../_footer.php"; ?>
