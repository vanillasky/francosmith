<?
$location = "주문관리 > 주문취소 관리 > 환불 리스트";
include "../_header.php";
include "../../lib/page.class.php";
$r_bank = codeitem("bank");

if (get_magic_quotes_gpc()) {
	stripslashes_all($_POST); stripslashes_all($_GET); stripslashes_all($_COOKIE);
}

// 통함 주문 설정
	@include(dirname(__FILE__).'/_cfg.integrate.php');

// 기본 값
	$now = time();
	$today = mktime(0,0,0,date('m',$now), date('d',$now), date('Y',$now));

	if (empty($_GET)) {
		$loc = $sess['m_id'].preg_replace('/[^a-zA-Z0-9]/','_',($_SERVER['PHP_SELF']));
		list($_data) = $db->fetch("SELECT `value` FROM gd_env WHERE `category` = 'form_helper' AND `name` = 'searchCondition'");
		$data = $_data ? unserialize($_data) : array();

		if (!empty($data[$loc])) {
			parse_str( iconv('utf-8','euc-kr',urldecode($data[$loc])),$_GET );
		}
		// 기간 조정
		if (!empty($_GET['regdt'][0]) && !empty($_GET['regdt'][1])) {
			$gap = abs( strtotime($_GET['regdt'][1]) - strtotime($_GET['regdt'][0]) );
			$_GET['regdt'][0] = date('Ymd',$now - $gap);
			$_GET['regdt'][1] = date('Ymd',$now);
		}
		unset($loc,$data,$_data);
	}

// 통합 데이터 수집
	$integrate_order = Core::loader('integrate_order');
	$integrate_order -> doSync();

// get 파라미터 처리 및 기본값 적용
	unset($_GET['x'],$_GET['y']);


	$_GET['sort']			= !empty($_GET['sort']) ? $_GET['sort'] : 'a.regdt desc';		// 정렬
	$_GET['mode']			= !empty($_GET['mode']) ? $_GET['mode'] : '';					// 뷰 형식
	$_GET['ord_status']		= !empty($_GET['ord_status']) ? $_GET['ord_status'] : array();	// 처리상태
	$_GET['settlekind']		= !empty($_GET['settlekind']) ? $_GET['settlekind'] : '';		// 결제수단
	$_GET['ord_type']		= !empty($_GET['ord_type']) ? $_GET['ord_type'] : '';		// 접수유형
	$_GET['skey']			= !empty($_GET['skey']) ? $_GET['skey'] : '';					// 주문검색 조건
	$_GET['sword']			= !empty($_GET['sword']) ? trim($_GET['sword']) : '';					// 주문검색 키워드
	$_GET['dtkind']			= !empty($_GET['dtkind']) ? $_GET['dtkind'] : 'orddt';				// 날짜 조건
	$_GET['regdt']			= !empty($_GET['regdt']) ? $_GET['regdt'] : array(date('Ymd',strtotime('-'.(int)$cfg['orderPeriod'].' day',$now)), date('Ymd',$now));					// 날짜
	$_GET['regdt_range']	= !empty($_GET['regdt']) ? $_GET['regdt'] : '';					// 날짜 기간 ( regdt[0] 부터 며칠 )
	$_GET['regdt_time']		= !empty($_GET['regdt_time']) ? $_GET['regdt_time'] : array(-1,-1);		// 시간
	$_GET['sgkey']			= !empty($_GET['sgkey']) ? $_GET['sgkey'] : '';					// 상품검색 조건
	$_GET['sgword']			= !empty($_GET['sgword']) ? trim($_GET['sgword']) : '';				// 상품검색 키워드

	$_GET['eggyn']			= !empty($_GET['eggyn']) ? $_GET['eggyn'] : '';					// 전자보증보험

	$_GET['escrowyn']		= !empty($_GET['escrowyn']) ? $_GET['escrowyn'] : '';			// 에스크로
	$_GET['cashreceipt']	= !empty($_GET['cashreceipt']) ? $_GET['cashreceipt'] : '';		// 현금영수증
	$_GET['flg_coupon']		= !empty($_GET['flg_coupon']) ? $_GET['flg_coupon'] : '';			// 쿠폰사용
	$_GET['about_coupon_flag']	= !empty($_GET['about_coupon_flag']) ? $_GET['about_coupon_flag'] : '';		// 어바웃쿠폰
	$_GET['pay_method_p']	= !empty($_GET['pay_method_p']) ? $_GET['pay_method_p'] : '';	// 적립금(포인트)
	$_GET['cbyn']			= !empty($_GET['cbyn']) ? $_GET['cbyn'] : '';					// ok캐시백 적립

	$_GET['chk_inflow']		= !empty($_GET['chk_inflow']) ? $_GET['chk_inflow'] : array();	// 홍보채널 (유입경로)
	$_GET['page']			= !empty($_GET['page']) ? $_GET['page'] : 1;						// 페이지
	$_GET['page_num']		= !empty($_GET['page_num']) ? $_GET['page_num'] : ($cfg['orderPageNum'] ? $cfg['orderPageNum'] : 20);	// 페이지당 레코드수

	$_GET['bankcode']		= !empty($_GET['bankcode']) ? $_GET['bankcode'] : '';



// 검색절 만듦

	#0. 초기화
		$arWhere = array();

	#1. 판매 채널


	#2. 주문 상태
		// 환불 관련 고정
		$arWhere[] = "oi.istep between 40 and 49";

		if ($_GET['ord_status'] == 20) {
			$arWhere[] = "oi.cyn = 'y'";
			$arWhere[] = "oi.dyn in ('n','r')";
		}
		elseif ($_GET['ord_status'] == 21) {
			$arWhere[] = "oi.cyn = 'r'";
			$arWhere[] = "oi.dyn in ('n','r')";
		}
		else {
			$arWhere[] = "(oi.cyn = 'y' or oi.cyn = 'r')";
			$arWhere[] = "oi.dyn in ('n','r')";

		}



	#3. 결제 수단
		if($_GET['settlekind']) {
			if($_GET['settlekind'] == 'payco') $arWhere[] = $db->_query_print('o.settleInflow= [s]',$_GET['settlekind']);
			else $arWhere[] = $db->_query_print('o.settlekind= [s]',$_GET['settlekind']);
		}

	#4. 통합 검색
		if($_GET['sword'] && $_GET['skey']) {
			$es_sword = $db->_escape($_GET['sword']);
			switch($_GET['skey']) {
				case 'all':
					$_where = array();

					// 처리담당자 추가
					$integrate_cfg['skey'][] = array(
						'field'=>'a.name',
						'condition'=>'like',
						'pattern'=>'/.{4,}/',
					);

					// 이나무의 데이터를 가져오므로, 필드를 매핑해줌.
					$_skey_map = array(
						'o.ord_name' => 'o.nameOrder',
						'o.rcv_name' => 'o.nameReceiver',
						'o.pay_bank_name' => 'o.bankSender',
						'm.m_id' => 'd.m_id',
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
				case 'm_id': $arWhere[] = "d.m_id = '{$es_sword}'"; break;
				case 'phoneOrder': $arWhere[] = "o.phoneOrder like '%{$es_sword}%'"; break;
				case 'phoneReceiver': $arWhere[] = "o.phoneReceiver like '%{$es_sword}%'"; break;
				case 'address': $arWhere[] = "o.address like '%{$es_sword}%'"; break;
				case 'deliverycode': $arWhere[] = "o.deliverycode like '%{$es_sword}%'"; break;
				case 'name': $arWhere[] = "a.name like '%{$es_sword}%'"; break;
			}
		}
	#5. 처리일자
		if($_GET['regdt'][0]) {
			if(!$_GET['regdt'][1]) $_GET['regdt'][1] = date('Ymd',$now);

			$tmp_start = substr($_GET['regdt'][0],0,4).'-'.substr($_GET['regdt'][0],4,2).'-'.substr($_GET['regdt'][0],6,2);
			$tmp_end = substr($_GET['regdt'][1],0,4).'-'.substr($_GET['regdt'][1],4,2).'-'.substr($_GET['regdt'][1],6,2);

			if ((int)$_GET['regdt_time'][0] !== -1 && (int)$_GET['regdt_time'][1] !== -1) {

				$tmp_start .= ' '.sprintf('%02d',$_GET['regdt_time'][0]).':00:00';
				$tmp_end .= ' '.sprintf('%02d',$_GET['regdt_time'][1]).':59:59';
			}
			else {
				$tmp_start .= ' 00:00:00';
				$tmp_end .= ' 23:59:59';
			}

			//$arWhere[] = $db->_query_print('o.orddt between [s] and [s]',$tmp_start,$tmp_end);

			switch($_GET['dtkind']) {
				case 'orddt': $arWhere[] = $db->_query_print('o.orddt between [s] and [s]',$tmp_start,$tmp_end); break;
				case 'cs_regdt': $arWhere[] = $db->_query_print('a.regdt between [s] and [s]',$tmp_start,$tmp_end); break;
				//case 'ddt': $arWhere[] = $db->_query_print('o.ddt between [s] and [s]',$tmp_start,$tmp_end); break;
				//case 'confirmdt': $arWhere[] = $db->_query_print('o.confirmdt between [s] and [s]',$tmp_start,$tmp_end); break;
			}
		}

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

	#7. 전자보증보험
		if($_GET['eggyn']) {
			$arWhere[] = $db->_query_print('o.eggyn = [s]',$_GET['eggyn']);
		}

	#8. 결제시 적용
		$tmp_arWhere = array();

		if($_GET['escrowyn']) {
			$tmp_arWhere[] = $db->_query_print('o.escrowyn = [s]',$_GET['escrowyn']);
		}

		if($_GET['cashreceipt']) {
			$tmp_arWhere[] = 'o.cashreceipt != ""';
		}
		if($_GET['flg_coupon']) {
			$tmp_arWhere[] = 'co.ordno is not null';
			$join_GD_COUPON_ORDER='left join '.GD_COUPON_ORDER.' as co on o.ordno=co.ordno';
		}
		else {
			$join_GD_COUPON_ORDER='';
		}

		if($_GET['about_coupon_flag']=='1') {
			$tmp_arWhere[] = 'o.about_coupon_flag = "Y"';
		}

		if($_GET['pay_method_p']=='1') {
			$tmp_arWhere[] = 'o.settlekind= "p"';
		}

		if($_GET['cbyn']=='Y') {
			$tmp_arWhere[] = 'o.cbyn = "Y"';
		}

		if (sizeof($tmp_arWhere) > 0) {
			$arWhere[] = '('.implode(' OR ',$tmp_arWhere).')';
			unset($tmp_arWhere);
		}


	#9. 홍보채널
		if(count($_GET['chk_inflow'])) {
			$es_inflow = array();
			foreach($_GET['chk_inflow'] as $v) {
				if($v == 'naver_price') {
					$es_inflow[] = '"naver_elec"';
					$es_inflow[] = '"naver_bea"';
					$es_inflow[] = '"naver_milk"';
				}
				else {
					$es_inflow[] = '"'.$db->_escape($v).'"';
				}
			}
			$arWhere[] = 'o.inflow in ('.implode(',',$es_inflow).')';
		}

	#10. 접수유형 (별도 필드가 없고, inflow 값이 sugi 인 레코드)
		if($_GET['ord_type'] == 'offline') {
			$arWhere[] = 'o.inflow = \'sugi\'';
		}
		else if ($_GET['ord_type'] == 'online') {
			$arWhere[] = 'o.inflow <> \'sugi\'';
		}

	#xx. 페이징 query 생성
		$_paging_query = http_build_query($_GET);	// php5 전용함수. but! lib.func.php 안에 php4용 있음.


	# 환불계좌
	if(strlen($_GET['bankcode']) == '1'){
		$_GET['bankcode'] = "0".$_GET['bankcode'];
	}


	if($_GET['bankcode']){
		$es_sword = $db->_escape($_GET['bankcode']);
		if($es_sword != 'all'){
			$arWhere[] = "a.bankcode = '{$es_sword}'";
		}
	}

	$arWhere[] = "oi.ordno=o.ordno";


$db_table = "
".GD_ORDER_CANCEL." a
left join ".GD_ORDER_ITEM." oi on a.sno=oi.cancel and a.ordno = oi.ordno
,".GD_ORDER." o
left join ".GD_MEMBER." d on o.m_no=d.m_no
";

$orderby = $_GET['sort'];

$pg = new Page($_GET[page],$_GET[page_num]);
$pg->field = "
a.sno,a.regdt canceldt,a.name nameCancel,a.bankcode,AES_DECRYPT(UNHEX(a.bankaccount), a.ordno) AS bankaccount,a.bankuser,
sum((oi.price-oi.memberdc-oi.coupon-oi.oi_special_discount_amount)*oi.ea)  as repay,count(*) cnt,
o.ordno,o.orddt,o.nameOrder,o.settlekind,o.pg,o.step2,o.settleprice,o.m_no,o.ncash_emoney,o.ncash_cash,a.rncash_emoney,a.rncash_cash,
o.settleprice, o.goodsprice, o.delivery, o.coupon, o.emoney, o.memberdc,o.enuri,o.eggFee,o.escrowyn,o.pgcancel,o.inflow,d.m_no,d.m_id, d.dormant_regDate,
oi.istep, oi.cyn, o.settleInflow
";
$pg->setQuery($db_table,$arWhere,$orderby,"group by a.sno");
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
	if(confirm('카드결제를 취소하시겠습니까?')){
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

//페이코 카드전체/부분, 핸드폰 결제취소
function paycoCancel(idx,part,vbank) {
	var ordno = document.getElementsByName('ordno[]')[idx].value;
	var sno = document.getElementsByName('sno[]')[idx].value;
	var lastRepay = document.getElementsByName('repay[]')[idx].value;//환불예정금액

	if(document.getElementsByName('repayfee[]')[idx]) var repayfee = document.getElementsByName('repayfee[]')[idx].value;//환불수수료
	else var repayfee = 0;
	var repay = parseInt(lastRepay) + parseInt(repayfee);//최종 환불금액
	var remoney = document.getElementsByName('remoney[]')[idx].value;//돌려줄 적립금

	if(vbank) {
		if(part == 'Y') file = 'paycoPartCancelVbank.php';//가상계좌 취소처리
		else file = 'paycoCancelVbank.php';//가상계좌 취소처리
	}
	else file = 'paycoCancel.php';//신용카드,페이코포인트,휴대폰결제,계좌이체 취소처리

	popupLayer("./"+file+"?ordno="+ordno+"&sno="+sno+"&part="+part+"&repay="+repay+"&lastRepay="+lastRepay+"&repayfee="+repayfee+"&remoney="+remoney,600,400);
}
</script>
<div class="title title_top">환불 리스트 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=order&no=33')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<form name="frmSearch" id="frmSearch" method="get" action="">
	<input type="hidden" name="mode" value="<?=$_GET['mode']?>"/>	<!-- 주문일 or 주문처리흐름 -->

	<table class="tb">
	<col class="cellC"><col class="cellL" style="width:250px">
	<col class="cellC"><col class="cellL">
	<tr>
		<td><span class="small1">주문검색</span></td>
		<td colspan="3">

			<select>
				<option>환불관련</option>
			</select>

			<select name="settlekind">
				<option value=""> = 결제수단 = </option>
				<? foreach ($integrate_cfg['pay_method'] as $k=>$v) { ?>
				<? if ($k == 'p') continue; ?>
				<option value="<?=$k?>" <?=$_GET['settlekind'] == $k ? 'selected' : ''?>><?=$v?></option>
				<? } ?>
				<option value="payco" <?=$_GET['settlekind'] == 'payco' ? 'selected' : ''?>>페이코</option>
			</select>

			<select name="ord_type">
				<option value=""> = 접수유형 = </option>
				<option value="online" <?=$_GET['ord_type'] == 'online' ? 'selected' : ''?>>온라인접수</option>
				<option value="offline" <?=$_GET['ord_type'] == 'offline' ? 'selected' : ''?>>수기접수</option>
			</select>

			<select name="bankcode">
			<option value="all"> = 환불계좌 은행 = </option>
			<? foreach ( $r_bank as $k=>$v){ ?>
			<option value="<?=$k?>"<?if(trim($k)==$_GET[bankcode])echo" selected";?>><?=$v?>
			<? } ?>
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
				<option value="name"	<?=($_GET['skey'] == 'name') ? 'selected' : ''?>	>처리담당자</option>

			</select>

			<input type="text" name="sword" value="<?=htmlspecialchars($_GET['sword'])?>" class="line" />

		</td>
	</tr>
	<tr>
		<td><span class="small1">처리일자</span></td>
		<td colspan="3">

			<select name="dtkind">
				<option value="orddt"		<?=($_GET['dtkind'] == 'orddt' ? 'selected' : '')?>		>주문일</option>
				<option value="cs_regdt"			<?=($_GET['dtkind'] == 'cs_regdt' ? 'selected' : '')?>			>환불요청일</option>
				<!--option value="ddt"			<?=($_GET['dtkind'] == 'ddt' ? 'selected' : '')?>			>배송일</option>
				<option value="confirmdt"	<?=($_GET['dtkind'] == 'confirmdt' ? 'selected' : '')?>	>배송완료일</option-->
			</select>

			<input type="text" name="regdt[]" value="<?=$_GET['regdt'][0]?>" onclick="calendar(event)" size="12" class="line"/>

			<select name="regdt_time[]">
			<option value="-1">---</option>
			<? for ($i=0;$i<24;$i++) {?>
			<option value="<?=$i?>" <?=($_GET['regdt_time'][0] === $i ? 'selected' : '')?>><?=sprintf('%02d',$i)?>시</option>
			<? } ?>
			</select>
			-
			<input type="text" name="regdt[]" value="<?=$_GET['regdt'][1]?>" onclick="calendar(event)" size="12" class="line"/>
			<select name="regdt_time[]">
			<option value="-1">---</option>
			<? for ($i=0;$i<24;$i++) {?>
			<option value="<?=$i?>" <?=($_GET['regdt_time'][1] === $i ? 'selected' : '')?>><?=sprintf('%02d',$i)?>시</option>
			<? } ?>
			</select>

			<a href="javascript:setDate('regdt[]',<?=date("Ymd")?>,<?=date("Ymd")?>)"><img src="../img/sicon_today.gif" align="absmiddle"/></a>
			<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-7 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_week.gif" align="absmiddle"/></a>
			<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-15 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twoweek.gif" align="absmiddle"/></a>
			<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-1 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_month.gif" align="absmiddle"/></a>
			<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-2 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twomonth.gif" align="absmiddle"/></a>
			<a href="javascript:setDate('regdt[]')"><img src="../img/sicon_all.gif" align="absmiddle"/></a>

		</td>
	</tr>
	<tr class="blindable">
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
		<td><span class="small1">전자보증보험</span> <a href="../basic/egg.intro.php"><img src="../img/btn_question.gif"/></a></td>
		<td class="noline">
			<select name="eggyn">
				<option value=""	<?=($_GET['eggyn'] == '') ? 'selected' : ''?>	>전체</option>
				<option value="n"	<?=($_GET['eggyn'] == 'n') ? 'selected' : ''?>>미발급</option>
				<option value="f"	<?=($_GET['eggyn'] == 'f') ? 'selected' : ''?>>발급실패</option>
				<option value="y"	<?=($_GET['eggyn'] == 'y') ? 'selected' : ''?>>발급완료</option>
			</select>
		</td>
	</tr>
	<tr class="blindable">
		<td><span class="small1">결제시적용</span></td>
		<td colspan="3" class="noline">
			<input type="checkbox" name="escrowyn" value="y" <?=frmChecked('y',$_GET['escrowyn'])?>>에스크로 <img src="../img/btn_escrow.gif" align="absmiddle"/></input>
			<input type="checkbox" name="cashreceipt" value="1" <?=frmChecked('1',$_GET['cashreceipt'])?>>현금영수증 <img src="../img/icon_cash_receipt.gif"/></input>
			<input type="checkbox" name="flg_coupon" value="1" <?=frmChecked('1',$_GET['flg_coupon'])?>>쿠폰사용</input>
			<input type="checkbox" name="about_coupon_flag" value="1" <?=frmChecked('1',$_GET['about_coupon_flag'])?>>어바웃쿠폰</input>
			<input type="checkbox" name="pay_method_p" value="1" <?=frmChecked('1',$_GET['pay_method_p'])?>>적립금(포인트)</input>
			<input type="checkbox" name="cbyn" value="Y" <?=frmChecked('Y',$_GET['cbyn'])?>><img src="../img/icon_okcashbag.gif" align="absmiddle"/>OK캐시백적립</input>
		</td>
	</tr>
	<tr class="blindable">
		<td><span class="small1">홍보채널<br>(유입경로)</span></td>
		<td colspan="3" class="noline">
			<? foreach ($integrate_cfg['inflows'] as $k=>$v) { ?>
			<label class="small1"><input type="checkbox" name="chk_inflow[]" value="<?=$k?>" <?=(in_array($k,$_GET['chk_inflow']) ? 'checked' : '')?> /><img src="../img/inflow_<?=$k?>.gif" align="absmiddle"/> <?=$v?></label>
			<? } ?>
		</td>
	</tr>
	</table>

	<div class="button_top">
	<table width="100%">
	<tr>
		<td width="35%" align="left">&nbsp;</td>
		<td width="30%" align="center"><input type="image" src="../img/btn_search2.gif"/></td>
		<td width="35%" align="right">
		<a href="javascript:void(0);" onClick="nsGodoFormHelper.save();"><img src="../img/btn_search_form_save.gif"></a>
		<a href="javascript:void(0);" onClick="nsGodoFormHelper.reset();"><img src="../img/btn_search_form_reset.gif"></a>
		<a href="javascript:void(0);" onClick="nsGodoFormHelper.toggle();"><img src="../img/btn_search_form_toggle_open.gif" id="el-godo-form-helper-toggle-btn"></a>
		</td>
	</tr>
	</table>
	</div>

	<div style="padding-top:15px"></div>

	<table width="100%" cellpadding="0" cellspacing="0">
	<tr>
		<td align="left">
		<a href="?<?=$_paging_query?>&ord_status="><img src="../img/btn_list_order_list<?=empty($_GET['ord_status']) ? '_on' : '' ?>.gif"></a>
		<a href="?<?=$_paging_query?>&ord_status=20"><img src="../img/btn_int_order_list_refund_req<?=($_GET['ord_status'] == 20) ? '_on' : ''?>.gif"></a>
		<a href="?<?=$_paging_query?>&ord_status=21"><img src="../img/btn_int_order_list_refund_fin<?=($_GET['ord_status'] == 21) ? '_on' : ''?>.gif"></a>
		</td>

		<td align="right">
		<select name="sort" onchange="this.form.submit();">
			<option value="a.regdt desc" <?=$_GET['sort'] == 'a.regdt desc' ? 'selected' : '' ?>> 취소일순↑</option>
			<option value="a.regdt asc" <?=$_GET['sort'] == 'a.regdt asc' ? 'selected' : '' ?>>취소일순↓</option>

			<option value="o.orddt desc" <?=$_GET['sort'] == 'o.orddt desc' ? 'selected' : '' ?>>주문일순↑</option>
			<option value="o.orddt asc" <?=$_GET['sort'] == 'o.orddt asc' ? 'selected' : '' ?>>주문일순↓</option>

			<option value="o.cdt desc" <?=$_GET['sort'] == 'o.cdt desc' ? 'selected' : '' ?>>입금일순↑</option>
			<option value="o.cdt asc" <?=$_GET['sort'] == 'o.cdt asc' ? 'selected' : '' ?>>입금일순↓</option>

			<option value="o.settleprice desc" <?=$_GET['sort'] == 'o.settleprice desc' ? 'selected' : '' ?>>결제액순↑</option>
			<option value="o.settleprice asc" <?=$_GET['sort'] == 'o.settleprice asc' ? 'selected' : '' ?>>결제액순↓</option>
		</select>&nbsp;

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

<form method=post action="indb.php">
<input type=hidden name=mode value="repay">

<table width=100% cellpadding=2 cellspacing=0>
<tr><td class=rnd colspan=10></td></tr>
<tr class=rndbg>
	<th><a href="javascript:chkBox(document.getElementsByName('chk[]'),'rev')" class=white>선택</a></th>
	<th><font class=small1><b>주문일</th>
	<th><font class=small1><b>주문취소일</th>
	<th><font class=small1><b>주문번호</th>
	<th><font class=small1><b>홍보채널</th>
	<th><font class=small1><b>주문자</th>
	<th><font class=small1><b>처리자</th>
	<th><font class=small1><b>취소수량/주문수량</th>
	<th><font class=small1><b>결제수단</th>

</tr>
<col align=center span=10>
<?
$i=0;
while ($data=$db->fetch($res)){
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

	// % 할인
	list($data[percentCoupon], $data[special_discount]) = $db->fetch("select sum(coupon * ea), sum(oi_special_discount_amount * ea) from gd_order_item where ordno = '$data[ordno]'");

	if($data[settleprice] >= $data[repay]){
		$repay = $data[repay];
		$repaymsg = "상품결제단가";
		if($ccnt == $cnt){
			$repaymsg = "상품결제단가 + 배송료 - 에누리 - 적립금 - 쿠폰 - 상품별 할인 + 보증보험수수료";
			$repay = $repay + $data[delivery] - $data[enuri] - $data[emoney] - $data['ncash_emoney'] - $data['ncash_cash'] - ($data[coupon] - $data[percentCoupon]) + $data[eggFee];


		}
		if((int)$total_use_naver_mileage) $repaymsg .= " - 네이버마일리지";
		if((int)$total_use_naver_cash) $repaymsg .= " - 네이버캐쉬";
	}else $repay = $data[settleprice];

	if($data['settleInflow'] == 'payco') {
		$orderDeliveryItem = &load_class('orderDeliveryItem','orderDeliveryItem');
		$orderDeliveryItem->ordno = $data['ordno'];//반복문으로 주문번호가 변경될 수 있어 별도 선언

		if($data['istep'] !== "44") {
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
			$cancel_delivery = $orderDeliveryItem->getCancelCompletedDeliverFeeWithSno($data['sno'], true);
		}
	}

	if($data[cnt] == $cnt) $repaymsg = "총 결제금액";
	if($repay < 0) $repay = 0;
	$repayfee = getRepayFee($repay);
	if($data[settleprice] < $data[repay]) $remoney = $data[repay] - $repay;
?>
<tr><td colspan=10 class=rndline></td></tr>
<tr>
	<td class=noline><input type=checkbox name=chk[] value="<?=$i?>" <?=$data[istep] == 44 ? 'disabled' : ''?>></td>
	<td><font class=ver71 color=444444><?=$data[orddt]?></td>
	<td><font class=ver71 color=444444><?=$data[canceldt]?></td>
	<td><a href="javascript:popup('popup.order.php?ordno=<?=$data[ordno]?>',800,600)"><font class=ver71 color=<?=$data['inflow'] == 'sugi' ? 'ED6C0A' : '0074BA'?>><b><?=$data[ordno]?><?=$data['inflow'] == 'sugi' ? '<span class="small1">(수기)</span>' : ''?></a></td>
	<td><? if ($data['inflow']!="" && $data['inflow']!="sugi"){ ?><img src="../img/inflow_<?=$data['inflow']?>.gif" align="absmiddle" alt="<?=$integrate_cfg['inflows'][$data['inflow']]?>" /><? } ?></td>
	<td>
		<?php if($data[m_id]){ ?>
			<?php if($data['dormant_regDate'] == '0000-00-00 00:00:00'){ ?>
				<span id="navig" name="navig" m_id="<?=$data['m_id']?>" m_no="<?=$data['m_no']?>"><span class="small1" style="color:#0074BA;"><?php echo $data['nameOrder']; ?></span></span>
			<?php } else { ?>
				<span class="small1" style="color:#0074BA;"><?php echo $data['nameOrder']; ?> (휴면회원)</span>
			<?php } ?>
		<?php } else { ?>
			<span class="small1"><?=$data[nameOrder]?></span>
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
			<td rowspan="<?=$cancel_delivery['view'][$item['delivery_type']][$item['goodsno']]['cnt']?>">
				<font class=ver7 color=0074BA>
					<b><?=number_format($cancel_delivery['view'][$item['delivery_type']][$item['goodsno']]['area_delivery_price'] + $cancel_delivery['view'][$item['delivery_type']][$item['goodsno']]['delivery_price'])?></b>
				</font>
			</td>
				<?unset($cancel_delivery['view'][$item['delivery_type']][$item['goodsno']]);?>
				<?}?>
			<?} else if($item['delivery_type'] == '5') {?>
			<td><font class=ver7 color=0074BA><b><?=number_format($cancel_delivery['view'][$item['delivery_type']][$item['optno']]['area_delivery_price'] + $cancel_delivery['view'][$item['delivery_type']][$item['optno']]['delivery_price'])?></b></font></td>
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
	<? if ($data['istep'] == 44 && $data['cyn'] == 'r') { ?>
		<div align=center>
			<b>환불 계좌 :</b>
			<? foreach ( $r_bank as $k=>$v){  if(trim($k)==$data[bankcode]) echo $v; } ?>
			<?=$data[bankaccount]?>
			&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 예금주 :  <?=$data[bankuser]?>
		</div>
	<? } else { ?>
		<div align=center><b>환불 계좌 : </b><select name=bankcode[] required><option value="">==선택==
			<? foreach ( $r_bank as $k=>$v){ ?>
			<option value="<?=$k?>"<?if(trim($k)==$data[bankcode])echo" selected";?>><?=$v?>
			<? } ?>
			</select>
			<input type=text name='bankaccount[]' value='<?=$data[bankaccount]?>'>
			<font class=ver71 color=444444>예금주</font> <input type=text name='bankuser[]' value='<?=$data[bankuser]?>'>
		</div>
	<? } ?>
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
		<? if ($data['istep'] == 44 && $data['cyn'] == 'r') { ?>
		<td><font class=ver7 color=424242><?=number_format($cancel_info['rfee'])?>원</td>
		<td bgcolor=E9FFB3><input type=hidden name='repay[]' style='background:#DEFD33' value='<?=$cancel_info['rprice']?>' style='text-align=right' readonly><div style="font-weight:bold;color:#FD3C00;" id='viewrepay<?=$i?>'><?=number_format($cancel_info['rprice'])?>원</div></td>
		<? } else { ?>
		<td><font class=ver7 color=424242><input type=text name='repayfee[]' class=noline value='<?=$repayfee?>' <?=$readonly['repayfee'][$data['sno']]?> onchange="cal_repay(this.value,<?=$repay?>,<?=$data['settleprice']?>,<?=$before_refund_amount?>,<?=$i?>)" onkeydown="onlynumber()" style='text-align=right;background:#E9FFB3'>원</td>
		<td bgcolor=E9FFB3><input type=hidden name='repay[]' style='background:#DEFD33' value='' style='text-align=right' readonly><div style="font-weight:bold;color:#FD3C00;" id='viewrepay<?=$i?>'></div></td>
		<? } ?>
		<td bgcolor=E9FFB3>
		<span id="canceltype<?=$i?>">
		<?
			### 취소처리 부분
			if($data['settlekind'] == 'c' || $data['settlekind'] == 'u' || ($data['settlekind'] == 'e' && $data['settleInflow'] == 'payco') ){		// 카드결제/페이코 일때만 취소 부분 출력
				if( $repay == $data['settleprice'] ){	// 전체취소 ( 결제금액과 환불금액이 같을 때 )
					if( $data['pgcancel'] == 'y' ){
						echo "<strong>카드결제취소</strong>된 주문입니다";
						if ($data['istep'] != 44 && $data['cyn'] != 'r') {
							echo "<br/>환불완료처리해주시기바랍니다.";
						}
					} else if ($data['pgcancel'] == 'r' && (int)$cancel_info['rfee'] > 0) {
						echo "<strong style='color: #ff0000;'>환불수수료</strong>를 제외하고 <strong>카드결제취소</strong>된 주문입니다.<br/>환불완료처리해주시기바랍니다.";
					}else{
						if($data['pg'] == 'payco') echo "<a href=\"javascript:paycoCancel(".$i.", 'N', 0)\"><img src='../img/payco_cancel_btn.gif' ></a>";//페이코 결제 전체취소(카드/계좌이체)
						else echo "<a href=\"javascript:cardSettleCancel('".$data[ordno]."','".$data[sno]."',".$i.")\"><img src='../img/cardcancel_btn.gif'></a>";
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
						if($data['istep'] !== "44") {
							if ((int)$cancel_info['rfee'] > 0) {
								echo "<strong style='color: #ff0000;'>환불수수료</strong>를 제외하고 <strong>부분취소</strong>된 주문입니다.<br/>환불완료처리해주시기바랍니다.";
							}
							else {
								echo "<strong>부분취소</strong>된 주문입니다.<br/>환불완료처리해주시기바랍니다.";
							}
						}
					}
				}
			}
			else if ($data['settlekind'] == 'h' && in_array($data['pg'], array('mobilians', 'payco', 'danal'))) {
				if ($repay == $data['settleprice']) {
					if ($data['pgcancel'] == 'y') {
						echo "<strong>결제취소</strong>된 주문입니다.";
						if ($data['istep'] != 44 && $data['cyn'] != 'r') {
							echo "<br/>환불완료처리해주시기바랍니다.";
						}
					}
					else {
						if($data['settleInflow'] == 'payco') {//페이코 핸드폰 결제 취소
							if(substr($data['cdt'], 5, 2) == date('m')) echo "<a href=\"javascript:paycoCancel(".$i.", 'N', 0)\"><img src='../img/payco_cancel_btn.gif'></a>";//페이코 결제 취소(휴대폰)
							else echo '결제월이 지난 휴대폰 결제건은 취소가 불가능 합니다.';
						}
						else if ($data['pg'] === 'danal') {
							echo '<img src="../img/payment_cancel_btn.jpg" onclick="ifrmHidden.location.href=\''.$cfg['rootDir'].'/order/card/danal/card_cancel.php?ordno='.$data['ordno'].'\';" style="cursor: pointer;"/>';
						}
						else {
							echo '<img src="../img/payment_cancel_btn.jpg" onclick="ifrmHidden.location.href=\''.$cfg['rootDir'].'/order/card/mobilians/card_cancel.php?ordno='.$data['ordno'].'\';" style="cursor: pointer;"/>';
						}
					}
				}
				else {
					if($data['settleInflow'] == 'payco') {
						if($cancel_info['pgcancel'] != 'r') echo "<a href=\"javascript:paycoCancel(".$i.", 'Y', 0)\"><img src='../img/payco_partcancel_btn.gif'></a>";//페이코 결제 부분취소(휴대폰)
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
						if($data['istep'] !== "44") {
							if ((int)$cancel_info['rfee'] > 0) {
								echo "<strong style='color: #ff0000;'>환불수수료</strong>를 제외하고 <strong>부분취소</strong>된 주문입니다.<br/>환불완료처리해주시기바랍니다.";
							}
							else {
								echo "<strong>부분취소</strong>된 주문입니다.<br/>환불완료처리해주시기바랍니다.";
							}
						}
					}
				}
			}
			else{
				echo "카드결제건이 아닙니다.";
			}
		?>
		</span>
		</td>
	</tr>
	</table>

	<? if ($data['istep'] != 44 && $data['cyn'] != 'r') { ?>
	<div align=center style="margin:3px;padding:5px;color:red;border:2px dotted red;" id="el-over-refund-message<?=$i?>">
	※중요!  총 환불금액이 총 결제금액을 초과하였습니다. 환불하시고자 하는 금액이 맞는지 다시 한번 확인해 주세요.
	</div>

	<script>cal_repay(<?=$repayfee?>,<?=$repay?>,<?=$data['settleprice']?>,<?=$before_refund_amount?>,<?=$i?>);</script>
	<? } ?>

	<? if ($data['istep'] != 44 && $data['cyn'] != 'r') { ?>
		<div align=center style="padding-top:5">이 주문결제시 사용한 적립금은 총 <font color=0074BA><b><?=number_format($data[emoney])?>원</b></font> 입니다.&nbsp;&nbsp;결제시 사용한 적립금 중 <input type=text name='remoney[]' style='text-align=right;background:#E9FFB3' onkeydown='onlynumber();' value='0'>원을 되돌려줍니다.</div>
	<? } ?>
	<?
	if($agoemoney){
	?>
	<div align=center style="padding-top:5">이 취소주문으로 되돌려준 적립금은 <font color=0074BA><b><?=number_format($agoemoney)?>원</b></font> 입니다.</div>
	<?}?>
	</td>
</tr>
<tr><td colspan=10 bgcolor='#616161' height=3></td></tr>
<tr><td colspan=10 height=10></td></tr>
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
