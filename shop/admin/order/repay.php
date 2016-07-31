<?
$location = "주문관리 > 환불접수리스트";
include "../_header.php";
include "../../lib/page.class.php";
$r_bank = codeitem("bank");

if (get_magic_quotes_gpc()) {
	stripslashes_all($_POST);
	stripslashes_all($_GET);
}

// $_GET으로 받는 모든 값 정의
$search=array(
	'regdt_start'=>(string)$_GET['regdt'][0], // 처리일자 시작
	'regdt_end'=>(string)$_GET['regdt'][1], // 처리일자 끝
	'dtkind'=>(string)($_GET['dtkind'] ? $_GET['dtkind'] : 'orddt'), // 처리일자 종류
	'sword'=>trim((string)$_GET['sword']), // 검색어
	'skey'=>($_GET['skey'] ? (string)$_GET['skey'] : 'all'), // 검색필드
	'settlekind'=>(string)$_GET['settlekind'], // 결제방법
	'bankcode'=>(string)$_GET['bankcode'], // 환불 계좌 은행
);

if(strlen($search['bankcode']) == '1'){
	$search['bankcode'] = "0".$search['bankcode'];
}

// 변수검증
if(!in_array($search['dtkind'],array('orddt','cdt'))) { exit; }
if(!in_array($search['skey'],array('all','ordno','nameOrder','goodsnm','name'))) { exit; }
if(!in_array($search['settlekind'],array('','a','c','o','v','h','u','y'))) { exit; }
if(!array_key_exists($search['bankcode'],$r_bank) && $search['bankcode'] != 'all' && $search['bankcode']) { exit; }

// 쿼리문을 위한 검색조건 만들기
$where = array();
if($search['regdt_start']) {
	if(!$search['regdt_end']) $search['regdt_end'] = date('Ymd');
	$tmp_start = substr($search['regdt_start'],0,4).'-'.substr($search['regdt_start'],4,2).'-'.substr($search['regdt_start'],6,2).' 00:00:00';
	$tmp_end = substr($search['regdt_end'],0,4).'-'.substr($search['regdt_end'],4,2).'-'.substr($search['regdt_end'],6,2).' 23:59:59';
	switch($search['dtkind']) {
		case 'orddt': $where[] = $db->_query_print('c.orddt between [s] and [s]',$tmp_start,$tmp_end); break;
		case 'cdt': $where[] = $db->_query_print('a.regdt between [s] and [s]',$tmp_start,$tmp_end); break;
	}
}
if($search['settlekind']) {
	$where[] = $db->_query_print('c.settlekind = [s]',$search['settlekind']);
}
if($search['sword'] && $search['skey']) {
	$es_sword = $db->_escape($search['sword']);
	switch($search['skey']) {
		case 'all':
			$where[] = "(
				c.ordno = '{$es_sword}' or
				c.nameOrder like '%{$es_sword}%' or
				b.goodsnm like '%{$es_sword}%' or
				a.name like '%{$es_sword}%'
			)"; break;
		case 'ordno': $where[] = "c.ordno = '{$es_sword}'"; break;
		case 'nameOrder': $where[] = "c.nameOrder like '%{$es_sword}%'"; break;
		case 'goodsnm': $where[] = "b.goodsnm like '%{$es_sword}%'"; break;
		case 'name': $where[] = "a.name like '%{$es_sword}%'"; break;
	}
}
if($search['bankcode']){
	$es_sword = $db->_escape($search['bankcode']);
	if($es_sword != 'all'){
		$where[] = "a.bankcode = '{$es_sword}'";
	}
}

$db_table = "
".GD_ORDER_CANCEL." a
left join ".GD_ORDER_ITEM." b on a.sno=b.cancel and a.ordno = b.ordno
,".GD_ORDER." c
left join ".GD_MEMBER." d on c.m_no=d.m_no
";

$where[] = "b.ordno=c.ordno";
$where[] = "istep > 40";
$where[] = "b.cyn = 'y'";
$where[] = "b.dyn in ('n','r')";

$pg = new Page($_GET[page]);
$pg->field = "
a.sno,a.regdt canceldt,a.name nameCancel,a.bankcode,AES_DECRYPT(UNHEX(a.bankaccount), a.ordno) AS bankaccount,a.bankuser,
sum((b.price-b.memberdc -b.coupon - b.oi_special_discount_amount)*b.ea)  as repay,count(*) cnt,
c.ordno,c.orddt,c.nameOrder,c.settlekind,c.step2,c.settleprice,c.m_no,c.ncash_emoney,c.ncash_cash,a.rncash_emoney,a.rncash_cash,
c.settleprice, c.goodsprice, c.delivery, c.coupon, c.emoney, c.memberdc,c.enuri,c.eggFee,c.escrowyn,c.pgcancel,c.pg,d.m_no,d.m_id,c.settleInflow,c.cdt, d.dormant_regDate
";
$pg->setQuery($db_table,$where,"a.sno desc","group by a.sno");
$pg->exec();

$res = $db->query($pg->query);

// 부분취소지원여부체크
$cardPartCancelable = false;
if (empty($cfg['settlePg']) === false) {
	include "../../lib/cardCancel.class.php";
	$cardPartCancelable = in_array('partcancel_'.$cfg['settlePg'], get_class_methods('cardCancel'));
}
?>
<script>
function cal_repay(repayfee,repay,settleprice,before_refund_amount,i){
	if(!repay) var tmp = 0;
	else var tmp = repay - repayfee;

	if(tmp < 0){
		alert('실결재금액보다 환불수수료가 큰 환불건이 있습니다.');
		document.getElementsByName('repayfee[]')[i].value='<?=$cfg[minrepayfee]?>';
		return;
	}

	document.getElementsByName('repay[]')[i].value=tmp;
	document.getElementById('viewrepay'+i).innerHTML=comma(tmp)+'원';

	// 현재 입력된 환불 금액과, 환불 완료 금액의 합이 최초 결제금액을 초과하는 경우 안내 메시지 출력
	document.getElementById('el-over-refund-message' + i).style.display = (tmp + before_refund_amount > settleprice) ? 'block' : 'none';
}
// 카드전체취소
function cardSettleCancel(ordno,sno,idx){
	var obj = document.ifrmHidden;
	var repayfee = parseInt(document.getElementsByName('repayfee[]')[idx].value);
	if (repayfee && parseInt(repayfee) > 0) {
		cardPartCancel(idx);
	}
	else if (confirm('카드결제를 취소하시겠습니까?')) {
		obj.location.href = "cardCancel.php?ordno="+ordno+"&sno="+sno+"&idx="+idx;
		document.getElementById("canceltype"+idx).innerHTML="<img src='../img/ajax-loader.gif' />";
	}
}
// 카드부분취소
function cardPartCancel(idx) {
	var ordno = document.getElementsByName('ordno[]')[idx].value;
	var sno = document.getElementsByName('sno[]')[idx].value;
	var lastRepay = document.getElementsByName('repay[]')[idx].value;
	var repayfee = document.getElementsByName('repayfee[]')[idx].value;
	var repay = parseInt(lastRepay) + parseInt(repayfee);
	popupLayer('./cardPartCancel.php?ordno='+ordno+'&sno='+sno+'&repay='+repay+'&lastRepay='+lastRepay,600,300);
}

//페이코 결제수단 부분/전체 취소
function paycoCancel(idx,part,vbank) {
	var ordno = document.getElementsByName('ordno[]')[idx].value;
	var sno = document.getElementsByName('sno[]')[idx].value;
	var lastRepay = document.getElementsByName('repay[]')[idx].value;//환불예정금액
	var repayfee = document.getElementsByName('repayfee[]')[idx].value;//환불수수료
	var repay = parseInt(lastRepay) + parseInt(repayfee);//최종 환불금액
	var remoney = document.getElementsByName('remoney[]')[idx].value;//돌려줄 적립금

	if(vbank) {
		if(part == "Y") file = 'paycoPartCancelVbank.php';//가상계좌 부분취소처리
		else file = 'paycoCancelVbank.php';//가상계좌 취소처리
	}
	else file = 'paycoCancel.php';//신용카드,페이코포인트,휴대폰결제,계좌이체 취소처라

	popupLayer("./"+file+"?ordno="+ordno+"&sno="+sno+"&part="+part+"&repay="+repay+"&lastRepay="+lastRepay+"&repayfee="+repayfee+"&remoney="+remoney,600,350);
}
</script>
<div class="title title_top">환불접수리스트 <span>환불접수된 주문건을 조회하고 환불완료처리합니다</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=order&no=5')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<form>
<input type="hidden" name="mode" value="<?=$search['mode']?>"/>

<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td><span class="small1">키워드 검색 (통합)</span></td>
	<td>
	<select name="skey">
	<option value="all"> = 통합검색 = </option>
	<option value="ordno" <?=frmSelected($search['skey'],'ordno');?>> 주문번호</option>
	<option value="nameOrder" <?=frmSelected($search['skey'],'nameOrder');?>> 주문자명</option>
	<option value="goodsnm" <?=frmSelected($search['skey'],'goodsnm');?>> 상품명</option>
	<option value="name" <?=frmSelected($search['skey'],'name');?>> 처리자</option>
	</select>
	<input type="text" name="sword" value="<?=htmlspecialchars($search['sword'])?>" class="line"/>
	</td>
</tr>
<tr>
	<td><span class="small1">결제방법</span></td>
	<td colspan="3" class="noline"><span class="small1" style="color:#5C5C5C;">
	<input type="radio" name="settlekind" value="" <?=frmChecked('',$search['settlekind'])?>>전체</input>
	<input type="radio" name="settlekind" value="a" <?=frmChecked('a',$search['settlekind'])?>>무통장</input>
	<input type="radio" name="settlekind" value="c" <?=frmChecked('c',$search['settlekind'])?>>신용카드</input>
	<input type="radio" name="settlekind" value="o" <?=frmChecked('o',$search['settlekind'])?>>계좌이체</input>
	<input type="radio" name="settlekind" value="v" <?=frmChecked('v',$search['settlekind'])?>>가상계좌</input>
	<input type="radio" name="settlekind" value="h" <?=frmChecked('h',$search['settlekind'])?>>핸드폰</input>
	<input type="radio" name="settlekind" value="u" <?=frmChecked('u',$search['settlekind'])?>>신용카드(중국)</input>
	<? if ($cfg['settlePg'] == "inipay") { ?>
	<input type="radio" name="settlekind" value="y" <?=frmChecked('y',$search['settlekind'])?>>옐로페이</input>
	<? } ?>
	</span>
	</td>
</tr>
<tr>
	<td><span class="small1">환불 계좌 은행</span></td>
	<td colspan="3" class="noline">
	<select name="bankcode">
	<option value="all"> == 선택 == </option>
	<? foreach ( $r_bank as $k=>$v){ ?>
	<option value="<?=$k?>"<?if(trim($k)==$search[bankcode])echo" selected";?>><?=$v?>
	<? } ?>
	</select>
	</td>
</tr>
<tr>
	<td><span class="small1">기간검색</span></td>
	<td colspan="3">
	<span class="noline small1" style="color:5C5C5C; margin-right:20px;">
	<input type="radio" name="dtkind" value="orddt" <?=frmChecked($search['dtkind'],'orddt')?>>주문일</input>
	<input type="radio" name="dtkind" value="cdt" <?=frmChecked($search['dtkind'],'cdt')?>>주문취소일</input>
	</span>
	<input type="text" name="regdt[]" value="<?=$search['regdt_start']?>" onclick="calendar(event)" size="12" class="line"/> -
	<input type="text" name="regdt[]" value="<?=$search['regdt_end']?>" onclick="calendar(event)" size="12" class="line"/>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd")?>,<?=date("Ymd")?>)"><img src="../img/sicon_today.gif" align="absmiddle"/></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-7 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_week.gif" align="absmiddle"/></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-15 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twoweek.gif" align="absmiddle"/></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-1 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_month.gif" align="absmiddle"/></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-2 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twomonth.gif" align="absmiddle"/></a>
	<a href="javascript:setDate('regdt[]')"><img src="../img/sicon_all.gif" align="absmiddle"/></a>
	</td>
</tr>
</table>
<div class="button_top">
<input type="image" src="../img/btn_search2.gif"/>
</div>
</form>

<div style="padding-top:15px"></div>

<form method=post action="indb.php">
<input type=hidden name=mode value="repay">

<table width=100% cellpadding=2 cellspacing=0>
<tr><td class=rnd colspan=10></td></tr>
<tr class=rndbg>
	<th><a href="javascript:chkBox(document.getElementsByName('chk[]'),'rev')" class=white>선택</a></th>
	<th><font class=small1><b>주문일</th>
	<th><font class=small1><b>주문취소일</th>
	<th><font class=small1><b>주문번호</th>
	<th><font class=small1><b>주문자</th>
	<th><font class=small1><b>처리자</th>
	<th><font class=small1><b>취소수량/주문수량</th>
	<th><font class=small1><b>결제수단</th>

</tr>
<col align=center span=10>
<?
$i=0;
while ($data=$db->fetch($res,1)){
	//'repayfee' => '10', 'minrepayfee' => '5000', 'minpos' => '4',
	$repay=0; $pemoney=0;$tot = 0;

	list($cnt,$ccnt,$pcnt) = $db->fetch("
	select count(*)
		,ifnull(sum(case when cancel != '' && cancel <= '$data[sno]' then 1 end),'0') as ccnt
	FROM ".GD_ORDER_ITEM." WHERE ordno=$data[ordno]");

	// 취소된 네이버 마일리지나 캐쉬가 있는경우 환불금에서 제외
	if((int)$data['rncash_emoney'] || (int)$data['rncash_cash'])
	{
		$data['repay'] -= $data['rncash_emoney'] + $data['rncash_cash'];
	}

	$total_use_naver_mileage = $data['rncash_emoney']+$data['ncash_emoney'];
	$total_use_naver_cash = $data['rncash_cash']+$data['ncash_cash'];

	// % 할인, 상품별 할인
	list($data[percentCoupon], $data[special_discount]) = $db->fetch("select sum(coupon * ea), sum(oi_special_discount_amount * ea) from gd_order_item where ordno = '$data[ordno]'");

	if($data[settleprice] >= $data[repay]){
		if($data['settleInflow'] == 'payco') {
			$orderDeliveryItem = &load_class('orderDeliveryItem','orderDeliveryItem');
			$orderDeliveryItem->ordno = $data['ordno'];//반복문으로 주문번호가 변경될 수 있어 별도 선언
			$cancel_delivery = $orderDeliveryItem->cancel_delivery($data['sno']);

			$repay = $cancel_delivery['total_cancel_price'] + $cancel_delivery['total_cancel_delivery_price'];
			$repaymsg = '상품결제단가 + 배송료 - 상품별 할인';

			if($orderDeliveryItem->checkLastCancel($data['sno']) === true) {
				//마지막 취소건인 경우 정액쿠폰, 적립금 사용액 차감
				$repay -= ($cancel_delivery['coupon']['m'] - $cancel_delivery['coupon']['f']) + $cancel_delivery['emoney'];
				$repaymsg = '상품결제단가 + 배송료 - 에누리 - 적립금 - 쿠폰 - 상품별 할인';
			}
		}
		else {
			$repay = $data[repay];
			$repaymsg = "상품결제단가";
			if($ccnt == $cnt){
				$repaymsg = "상품결제단가 + 배송료 - 에누리 - 적립금 - 쿠폰 - 상품별 할인 + 보증보험수수료";
				$repay = $repay + $data[delivery] - $data[enuri] - $data[emoney] - $data['ncash_emoney'] - $data['ncash_cash'] - ($data[coupon] - $data[percentCoupon]) + $data[eggFee];


			}
			if((int)$total_use_naver_mileage) $repaymsg .= " - 네이버마일리지";
			if((int)$total_use_naver_cash) $repaymsg .= " - 네이버캐쉬";
		}
	}else $repay = $data[settleprice];

	if($data[cnt] == $cnt) $repaymsg = "총 결제금액";
	if($repay < 0) $repay = 0;
	$repayfee = getRepayFee($repay);
	if($data[settleprice] < $data[repay]) $remoney = $data[repay] - $repay;



?>

<tr><td colspan=10 class=rndline></td></tr>
<tr>
	<td class=noline><input type=checkbox name=chk[] value="<?=$i?>"></td>
	<td><font class=ver71 color=444444><?=$data[orddt]?></td>
	<td><font class=ver71 color=444444><?=$data[canceldt]?></td>
	<td><a href="javascript:popup('popup.order.php?ordno=<?=$data[ordno]?>',800,600)"><font class=ver71 color=0074BA><b><?=$data[ordno]?></a></td>
	<td>
		<?php if($data[m_id]){ ?>
			<?php if($data['dormant_regDate'] == '0000-00-00 00:00:00'){ ?>
				<span id="navig" name="navig" m_id="<?=$data['m_id']?>" m_no="<?=$data['m_no']?>"><span class="small1" style="color:#0074BA"><?=$data['nameOrder']?></span></span>
			<?php } else { ?>
				<span class="small1" style="color:#0074BA"><?=$data['nameOrder']?>(휴면회원)</span>
			<?php } ?>
		<?php } else { ?>
			<span class="small1"><?=$data['nameOrder']?></span>
		<?php } ?>
	</td>
	<td><font class=small1 color=444444><?=$data[nameCancel]?></td>
	<td><font class=ver7 color=444444><?=$data[cnt]?>/<?=$cnt?></td>
	<td><font class=small1 color=444444><?=settleIcon($data['settleInflow']);?><?=$r_settlekind[$data[settlekind]]?></td>
</tr>
<tr>
	<td colspan=10 style="padding:5px 10px" align=left>
	<table width=100% border=1 bordercolor=#dedede style="border-collapse:collapse">
		<tr bgcolor=#f7f7f7 height=20>
			<th width=14%><font class=small1 color=444444><b>주문금액</th>
			<th width=14% nowrap><font class=small1 color=444444><b>배송료</th>
			<th width=14% nowrap><font class=small1 color=444444><b>상품할인</th>
			<th width=14% nowrap><font class=small1 color=444444><b>회원할인</th>
			<? if ($total_use_naver_mileage > 0){ ?><th width=14% nowrap><font class=small1 color=444444><b>네이버마일리지사용</th><?}?>
			<? if ($total_use_naver_cash > 0){ ?><th width=14% nowrap><font class=small1 color=444444><b>네이버캐쉬사용</th><?}?>
			<th width=14% nowrap><font class=small1 color=444444><b>에누리</th>
			<th width=14% nowrap><font class=small1 color=444444><b>쿠폰</th>
			<th width=14% nowrap><font class=small1 color=444444><b>결제시 사용한 적립금</th>
			<th width=14% nowrap><font class=small1 color=444444><b>보증보험수수료</th>
			<th width=16% nowrap><font class=small1 color=444444><b>총 결제금액</th>
		</tr>
		<col align=center span=10>
		<tr>
			<td><font class=ver7 color=444444><?=number_format($data[goodsprice])?>원</td>
			<td><font class=ver7 color=444444><?=number_format($data[delivery])?>원</td>
			<td><font class=ver7 color=444444><?=number_format($data[special_discount])?>원</td>
			<td><font class=ver7 color=444444><?=number_format($data[memberdc])?>원</td>
			<? if ($total_use_naver_mileage > 0){ ?><td><font class=ver7 color=444444><?=number_format($total_use_naver_mileage)?>원</td><?}?>
			<? if ($total_use_naver_cash > 0){ ?><td><font class=ver7 color=444444><?=number_format($total_use_naver_cash)?>원</td><?}?>
			<td><font class=ver7 color=444444><?=number_format($data[enuri])?>원</td>
			<td><font class=ver7 color=444444><?=number_format($data[coupon])?>원 (%할인 <?=number_format($data[percentCoupon])?>원 + 금액할인 <?=number_format($data[coupon] - $data[percentCoupon])?>원)</td>
			<td><font class=ver7 color=444444><?=number_format($data[emoney])?>원</td>
			<td><font class=ver7 color=444444><?=number_format($data[eggFee])?>원</td>
			<td><font class=ver7 color=444444><?=number_format($data[settleprice])?>원</td>
		</tr>
	</table>
	</td>
</tr>
<tr><td colspan=10 style="height:20px;">
<div style="height:5px;text-align:center;border-bottom:1px dotted #4A7EBB;margin-bottom:5px;">
	<span style="display:inline-block;position:relative;top:8px;background:#fff;padding:0 5px;color:#627dce;">환 불 내 역</span>
</div></td></tr>

<tr>
	<td colspan=10 style="padding:5px 10px" align=left>
	<table width=100% border=1 bordercolor=#dedede style="border-collapse:collapse">
	<tr bgcolor=#f7f7f7 height=20>
		<th><font class=small1 color=444444><b>상품명</th>
		<th width=80 nowrap><font class=small1 color=444444><b>판매가격</th>
		<th width=80 nowrap><font class=small1 color=444444><b>상품할인</th>
		<th width=80 nowrap><font class=small1 color=444444><b>회원할인</th>
		<th width=80 nowrap><font class=small1 color=444444><b>쿠폰할인</th>
		<th width=80 nowrap><font class=small1 color=444444><b>상품결제단가</th>
		<th width=50 nowrap><font class=small1 color=444444><b>수량</th>
		<?if($data['settleInflow'] == 'payco') {?><th width=80 nowrap><font class=small1 color=444444><b>배송비</th><?}?>
	</tr>
	<col><col align=center span=10>
	<?
	if($data['settleInflow'] == 'payco') {
		$query = "
		select b.*,a.*,tg.tgsno from
			".GD_ORDER_ITEM." a
			left join ".GD_GOODS." b on a.goodsno=b.goodsno
			left join ".GD_TODAYSHOP_GOODS." tg on a.goodsno=tg.goodsno
			left join ".GD_ORDER_ITEM_DELIVERY." oid on a.oi_delivery_idx=oid.oi_delivery_idx
		where
			a.cancel='$data[sno]'
			and a.ordno='$data[ordno]'
		order by oid.delivery_type, a.goodsno
		";
	}
	else {
		$query = "
		select b.*,a.*,tg.tgsno from
			".GD_ORDER_ITEM." a
			left join ".GD_GOODS." b on a.goodsno=b.goodsno
			left join ".GD_TODAYSHOP_GOODS." tg on a.goodsno=tg.goodsno
		where
			a.cancel='$data[sno]'
			and a.ordno='$data[ordno]'
		";
	}
	$res2 = $db->query($query);
	while ($item=$db->fetch($res2)){
	?>
	<tr>
		<td>
		<table>
		<tr>
			<td style="padding-left:3px"><a href="<?=($item['tgsno'])? '../../todayshop/today_goods.php?tgsno='.$item['tgsno'] : '../../goods/goods_view.php?goodsno='.$item[goodsno]?>" target=_blank><font class=small color=0074BA>
			<?=$item[goodsnm]?>
			<? if ($item[opt1]){ ?>[<?=$item[opt1]?><? if ($item[opt2]){ ?>/<?=$item[opt2]?><? } ?>]<? } ?>
			<? if ($item[addopt]){ ?><div>[<?=str_replace("^","] [",$item[addopt])?>]</div><? } ?><font class=small1 color=0074BA><b>[보기]</b></font></a>
			</td>
		</tr>
		</table>
		</td>
		<td><font class=ver7 color=444444><?=number_format($item[price])?></td>
		<td><font class=ver7 color=444444><?=number_format($item[oi_special_discount_amount])?></td>
		<td><font class=ver7 color=444444><?=number_format($item[memberdc])?></td>
		<td><font class=ver7 color=444444><?=number_format($item[coupon])?></td>
		<td><font class=ver7 color=0074BA><b><?=number_format($item[price]-$item[memberdc]-$item[coupon]-$item[oi_special_discount_amount])?></td>
		<td><font class=ver7 color=444444><?=number_format($item[ea])?></td>
		<?if($data['settleInflow'] == 'payco') {?>
			<?if($item['delivery_type'] == '0' || $item['delivery_type'] == '1') {?>
				<?if(isset($cancel_delivery['view'][$item['delivery_type']])) {?>
		<td rowspan="<?=$cancel_delivery['view'][$item['delivery_type']]['cnt']?>">
			<font class=ver7 color=0074BA>
				<b><?=number_format($cancel_delivery['view'][$item['delivery_type']]['area_delivery_price'] + $cancel_delivery['view'][$item['delivery_type']]['delivery_price'])?></b>
			</font>
		</td>
				<?unset($cancel_delivery['view'][$item['delivery_type']]);?>
				<?}?>
			<?} else if($item['delivery_type'] == '4') {?>

				<?if(isset($cancel_delivery['view'][$item['delivery_type']][$item['goodsno']])) {?>

					<?if($cancel_delivery['view'][$item['delivery_type']][$item['goodsno']]['cnt'] > 1) {?><td rowspan="<?=$cancel_delivery['view'][$item['delivery_type']][$item['goodsno']]['cnt']?>">
					<?} else {?>
		<td>
					<?}?>

			<font class=ver7 color=0074BA>
				<b><?=number_format($cancel_delivery['view'][$item['delivery_type']][$item['goodsno']]['area_delivery_price'] + $cancel_delivery['view'][$item['delivery_type']][$item['goodsno']]['delivery_price'])?></b>
			</font>
		</td>
				<?unset($cancel_delivery['view'][$item['delivery_type']][$item['goodsno']]);?>
				<?}?>

			<?} else if($item['delivery_type'] == '5') {?>
		<td><font class=ver7 color=0074BA><b><?=number_format($cancel_delivery['view'][$item['delivery_type']][$item['optno']]['area_delivery_price'] + $cancel_delivery['view'][$item['delivery_type']][$item['optno']]['delivery_price'])?></font></b></td>
			<?} else {?>
		<td><font class=ver7 color=0074BA><b><?=number_format($cancel_delivery['view'][$item['delivery_type']][$item['goodsno']]['delivery_price'])?></font></b></td>
			<?}?>
		<?}?>
	</tr>
	<? } ?>
	</table>
	<?
	$data[bankcode] = sprintf("%02d",$data[bankcode]);
	?>
    <div style="padding-top:3px;"></div>
   	<div align=center><b>환불 계좌 : </b><select name=bankcode[]><option value="">==선택==
		<? foreach ( $r_bank as $k=>$v){ ?>
		<option value="<?=$k?>"<?if(trim($k)==$data[bankcode])echo" selected";?>><?=$v?>
		<? } ?>
		</select>
		<input type=text name='bankaccount[]' value='<?=$data['bankaccount']?>'>
		<font class=ver71 color=444444>예금주</font> <input type=text name='bankuser[]' value='<?=$data[bankuser]?>'>
	</div>
    <div style="padding-top:3px"></div>
	<table width=100% border=1 bordercolor=#dedede style="border-collapse:collapse">
	<tr bgcolor=#f7f7f7 height=20>
		<th width=25% nowrap><font class=small1 color=444444><b>환불예정금액(<?=$repaymsg?>)</th>
		<th width=25% nowrap><font class=small1 color=444444><b>환불수수료</b> <a href="javascript:popupLayer('../basic/popup.emoney.php',600,300)"><img src="../img/btn_repay_price.gif" border=0></a></th>
		<th width=25% nowrap><font class=small1 color=444444><b>최종 환불금액</b> ( = 실결제금액 - 환불수수료)</th>
		<th width=25% nowrap>취소처리</th>
	</tr>
	<col><col align=center span=10>
	<?
	if($repay-$repayfee < 0) $pre = 0;
	else $pre = $repay-$repayfee;

	## 부분취소 여부
	$query = "select rprice, rfee, pgcancel from ".GD_ORDER_CANCEL." where sno = '".$data['sno']."'";
	$cancel_info = $db->fetch($query);

	if($cancel_info['pgcancel'] == 'r'){
		$repayfee = $cancel_info['rfee'];
	} else if($cancel_info['pgcancel'] == 'y'){
		$repayfee = 0;
	}

	if($cancel_info['pgcancel'] != 'n'){
		$readonly['repayfee'][$data['sno']] = "readonly";
	}

	$query = "select sum(remoney), sum(rprice) from ".GD_ORDER_CANCEL."  where ordno='$data[ordno]' and sno != '".$data['sno']."'";
	list($agoemoney, $before_refund_amount) = $db->fetch($query);
	$before_refund_amount = intval($before_refund_amount);
	?>
	<input type=hidden name='m_no[]' style='background:#e3e3e3' value='<?=$data[m_no]?>' readonly>
	<input type=hidden name='sno[]' style='background:#e3e3e3' value='<?=$data[sno]?>' readonly>
	<input type=hidden name='ordno[]' style='background:#e3e3e3' value='<?=$data[ordno]?>' readonly>
	<tr>
		<td align=center><font class=ver7 color=0074BA><b><?=number_format($repay)?>원</b></td>
		<td><font class=ver7 color=424242><input type=text name='repayfee[]' class=noline value='<?=$repayfee?>' <?=$readonly['repayfee'][$data['sno']]?> onchange="cal_repay(this.value,<?=$repay?>,<?=$data['settleprice']?>,<?=$before_refund_amount?>,<?=$i?>)" onkeydown="onlynumber()" style='text-align=right;background:#E9FFB3'>원</td>
		<td bgcolor=E9FFB3><input type=hidden name='repay[]' style='background:#DEFD33' value='' style='text-align=right' readonly><div style="font-weight:bold;color:#FD3C00;" id='viewrepay<?=$i?>'></div></td>
		<td bgcolor=E9FFB3>
		<span id="canceltype<?=$i?>">
		<?
			### 취소처리 부분
			if($data['settlekind'] == 'c' || $data['settlekind'] == 'u' || ($data['settlekind'] == 'e' && $data['settleInflow'] == 'payco') ){		// 카드결제/페이코 일때만 취소 부분 출력
				if( $repay == $data['settleprice'] ){	// 전체취소 ( 결제금액과 환불금액이 같을 때 )
					if( $data['pgcancel'] == 'y' ){
						echo "<strong>카드결제취소</strong>된 주문입니다.<br/>환불완료처리해주시기바랍니다.";
					} else if ($data['pgcancel'] == 'r' && (int)$cancel_info['rfee'] > 0) {
						echo "<strong style='color: #ff0000;'>환불수수료</strong>를 제외하고 <strong>카드결제취소</strong>된 주문입니다.<br/>환불완료처리해주시기바랍니다.";
					}else{
						if($data['settleInflow'] == 'payco') echo "<a href=\"javascript:paycoCancel(".$i.", 'N', 0)\"><img src='../img/payco_cancel_btn.gif' ></a>";//페이코 결제 전체취소(카드/계좌이체)
						else echo "<a href=\"javascript:cardSettleCancel('".$data[ordno]."','".$data[sno]."',".$i.")\"><img src='../img/cardcancel_btn.gif' ></a>";
					}
				}else{									// 부분취소
					if($cancel_info['pgcancel'] != 'r' && $cardPartCancelable === false && $data['settleInflow'] != 'payco'){
						echo '카드부분취소 지원안됨';
					}else if($cancel_info['pgcancel'] != 'r'){	// 이미 부분취소된 건이 아닐 때
						if($data['settleInflow'] == 'payco') {
							echo "<a href=\"javascript:paycoCancel(".$i.", 'Y', 0)\"><img src='../img/payco_partcancel_btn.gif'></a>";//페이코 결제 부분취소(카드/계좌이체)
						}
						else if( $cfg['settlePg'] == 'inicis' && $data['escrowyn'] != 'n' ){	// 이니시스일 때 에스크로 결제건은 제외
							echo "이니시스 에스크로 부분취소불가";
						}
						else if ($cfg['settlePg'] == 'lgdacom' && $data['settlekind'] == 'u') {// 유플러스 중국카드 결제 제외
							echo "LG U+ CUP 결제 부분취소불가";
						}
						else{
							echo "<a href=\"javascript:cardPartCancel(".$i.")\"><img src='../img/cardpartcancel_btn.gif'></a>";
						}
					}else{
						if ((int)$cancel_info['rfee'] > 0) {
							echo "<strong style='color: #ff0000;'>환불수수료</strong>를 제외하고 <strong>부분취소</strong>된 주문입니다.<br/>환불완료처리해주시기바랍니다.";
						}
						else {
							echo "<strong>부분취소</strong>된 주문입니다.<br/>환불완료처리해주시기바랍니다.";
						}
					}
				}
			}
			else if ($data['settlekind'] == 'h' && in_array($data['pg'], array('mobilians', 'payco', 'danal'))) {
				if ($repay == $data['settleprice']) {
					if ($data['pgcancel'] == 'y') {
						echo "<strong>결제취소</strong>된 주문입니다.<br/>환불완료처리해주시기바랍니다.";
					}
					else {
						if($data['settleInflow'] == 'payco') {
							if(substr($data['cdt'], 5, 2) == date('m')) echo "<a href=\"javascript:paycoCancel(".$i.", 'N', 0)\"><img src='../img/payco_cancel_btn.gif'></a>";//페이코 결제 취소(휴대폰)
							else echo '결제월이 지난 휴대폰 결제건은 취소가 불가능 합니다.';
						}
						else if ($data['pg'] == 'danal') {
							echo '<img src="../img/payment_cancel_btn.jpg" onclick="ifrmHidden.location.href=\''.$cfg['rootDir'].'/order/card/danal/card_cancel.php?ordno='.$data['ordno'].'\';" style="cursor: pointer;"/>';
						}
						else echo '<img src="../img/payment_cancel_btn.jpg" onclick="ifrmHidden.location.href=\''.$cfg['rootDir'].'/order/card/mobilians/card_cancel.php?ordno='.$data['ordno'].'\';" style="cursor: pointer;"/>';
					}
				}
				else {
					if($data['settleInflow'] == 'payco') {
						if($cancel_info['pgcancel'] != 'r') echo "<a href=\"javascript:paycoCancel(".$i.", 'Y', 0)\"><img src='../img/payco_partcancel_btn.gif'></a>";//페이코 결제 부분취소
						else echo "<strong>부분취소</strong>된 주문입니다.<br/>환불완료처리해주시기바랍니다.";
					}
					else echo '휴대폰 결제건으로<br/>부분취소가 불가능합니다.';
				}
			}
			else if($data['pg'] == 'payco' && ($data['settlekind'] == 'v' || $data['settlekind'] == 'o')) {				//페이코 가상계좌 취소
				if ($data['pgcancel'] == 'y') {
					echo "<strong>결제취소</strong>된 주문입니다.<br/>환불완료처리해주시기바랍니다.";
				}
				else {
					if($repay == $data['settleprice']) echo "<a href=\"javascript:paycoCancel(".$i.", 'N', 1)\"><img src='../img/payco_cancel_btn.gif' ></a>";//페이코 결제 전체취소
					else if($cancel_info['pgcancel'] != 'r') echo "<a href=\"javascript:paycoCancel(".$i.", 'Y', 1)\"><img src='../img/payco_partcancel_btn.gif'></a>";//페이코 결제 부분취소
					else {
						if ((int)$cancel_info['rfee'] > 0) {
							echo "<strong style='color: #ff0000;'>환불수수료</strong>를 제외하고 <strong>부분취소</strong>된 주문입니다.<br/>환불완료처리해주시기바랍니다.";
						}
						else {
							echo "<strong>부분취소</strong>된 주문입니다.<br/>환불완료처리해주시기바랍니다.";
						}
					}
				}
			}
			else {
				echo "카드결제건이 아닙니다.";
			}
		?>
		</span>
		</td>
	</tr>
	</table>

	<div align=center style="margin:3px;padding:5px;color:red;border:2px dotted red;" id="el-over-refund-message<?=$i?>">
	※중요!  총 환불금액이 총 결제금액을 초과하였습니다. 환불하시고자 하는 금액이 맞는지 다시 한번 확인해 주세요.
	</div>

	<div align=center style="padding-top:5">이 주문결제시 사용한 적립금은 총 <font color=0074BA><b><?=number_format($data[emoney])?>원</b></font> 입니다.&nbsp;&nbsp;결제시 사용한 적립금 중 <input type=text name='remoney[]' style='text-align=right;background:#E9FFB3' onkeydown='onlynumber();' value='0'>원을 되돌려줍니다.</div>
	<?
	if($agoemoney){
	?>
	<div align=center style="padding-top:5">현재까지 이 취소주문으로 되돌려준 적립금은 총 <font color=0074BA><b><?=number_format($agoemoney)?>원</b></font> 입니다.</div>
	<?}?>
	</td>
</tr>
<tr><td colspan=10 bgcolor='#616161' height=3></td></tr>
<tr><td colspan=10 height=10></td></tr>
<script>cal_repay(<?=$repayfee?>,<?=$repay?>,<?=$data['settleprice']?>,<?=$before_refund_amount?>,<?=$i?>);</script>
<?
	$i++;
}
?>
</table>

<div class=pageNavi align=center><?=$pg->page[navi]?></div>

<div class=button>
<input type=image src="../img/btn_refund.gif" onclick="return isChked(document.getElementsByName('chk[]'),'정말로 환불처리를 하시겠습니까?')">
<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a>
</div>

</form>

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"><span class="color_ffe" style="font-weight:bold;">입금한 상태에서 주문을 취소</span>하거나 이미 배송되어 <span class="color_ffe" style="font-weight:bold;">반품시</span> 발생하는 <span class="color_ffe" style="font-weight:bold;">환불건에 대해 완료처리</span>하는 영역입니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">주문취소와 반품완료처리를 통해 <span class="color_ffe" style="font-weight:bold;">환불접수된 주문건</span>이 환불접수리스트에 보입니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">환불접수건을 확인하고 <span class="color_ffe" style="font-weight:bold;">고객의 통장으로 환불금액을 입금</span>합니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">환불입금이 완료된 해당 주문건을 선택한 후 <span class="color_ffe" style="font-weight:bold;">환불완료처리</span>합니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"><span class="color_ffe" style="font-weight:bold;">최종 환불금액</span>이란 <span class="color_ffe" style="font-weight:bold;">실결제금액</span>에서 <span class="color_ffe" style="font-weight:bold;">환불수수료</span>를 제한 금액을 말합니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"><span class="color_ffe" style="font-weight:bold;">환불수수료</span>란 반품으로 인해 발생된 <span class="color_ffe" style="font-weight:bold;">반송비용 및 기타 수수료</span>를 의미하며, <span class="color_ffe" style="font-weight:bold;">기본값을 설정</span>할 수 있습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"><span class="color_ffe" style="font-weight:bold;">적립금으로 결제</span>한 경우 <span class="color_ffe" style="font-weight:bold;">환불적립금</span>을 정산하여 <span class="color_ffe" style="font-weight:bold;">재적립</span>해주어야 합니다.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>


<script>window.onload = function(){ UNM.inner();};</script>

<? include "../_footer.php"; ?>
