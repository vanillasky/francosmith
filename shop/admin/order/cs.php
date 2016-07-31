<?php
$location = "주문관리 > 주문취소리스트 [조회]";
include "../_header.php";
include "../../lib/page.class.php";

$bgcolor[44] = "#E6FBFE";
$disabled[44] = "disabled";

// $_GET으로 받는 모든 값 정의
$search = array(
	'sword'=>(string)$_GET['sword'],
	'skey'=>(string)$_GET['skey'],
	'regdt_start'=>(string)$_GET['regdt'][0],
	'regdt_end'=>(string)$_GET['regdt'][1],
	'settlekind'=>(string)$_GET['settlekind'],
	'type'=>(array)$_GET['type']
);
$page = (int)$_GET['page'] ? (int)$_GET['page'] : 1;

$arWhere=array();
$arWhere[] = 'oi.istep between 40 and 49';

if(count($search['type'])) {
	$subWhere = array();
	foreach($search['type'] as $v) {
		switch($v) {
			case '1': $subWhere[] = '(oi.cyn="n" and oi.dyn="n")'; break;
			case '2': $subWhere[] = 'oi.cyn="y"'; break;
			case '3': $subWhere[] = 'oi.cyn="r"'; break;
			case '4': $subWhere[] = 'oi.dyn="y"'; break;
			case '5': $subWhere[] = '(oi.dyn="r" and oi.cyn="y")'; break;
			case '6': $subWhere[] = '(oi.dyn="r" and oi.cyn="r")'; break;
			case '7': $subWhere[] = '(oi.dyn="e" and oi.cyn="e")'; break;
		}
	}
	if(count($subWhere)) {
		$arWhere[] = '('.implode(' or ',$subWhere).')';
	}
}
if($search['sword'] && $search['skey']) {
	$es_sword = $db->_escape($search['sword']);
	switch($search['skey']) {
		case 'all':
			$arWhere[] = "(
				o.ordno = '{$es_sword}' or
				o.nameOrder like '%{$es_sword}%' or
				o.bankSender like '%{$es_sword}%' or
				m.m_id = '{$es_sword}'
			)"; break;
		break;
		case 'ordno': $arWhere[] = "o.ordno = '{$es_sword}'"; break;
		case 'nameOrder': $arWhere[] = "o.nameOrder like '%{$es_sword}%'"; break;
		case 'bankSender': $arWhere[] = "o.bankSender like '%{$es_sword}%'"; break;
		case 'm_id': $arWhere[] = "m.m_id = '{$es_sword}'"; break;
	}
}
if($search['regdt_start']) {
	if(!$search['regdt_end']) $search['regdt_end'] = date('Ymd');
	$tmp_start = substr($search['regdt_start'],0,4).'-'.substr($search['regdt_start'],4,2).'-'.substr($search['regdt_start'],6,2).' 00:00:00';
	$tmp_end = substr($search['regdt_end'],0,4).'-'.substr($search['regdt_end'],4,2).'-'.substr($search['regdt_end'],6,2).' 23:59:59';
	$arWhere[] = $db->_query_print('o.orddt between [s] and [s]',$tmp_start,$tmp_end);
}
if($search['settlekind']) {
	$arWhere[] = $db->_query_print('o.settlekind = [s]',$search['settlekind']);
}


if(count($arWhere)) {
	$strWhere = 'where '.implode(' and ',$arWhere);
}

// 쿼리 실행
@include './checkout._order_cs.php'; // Checkout include
if($isEnableAdminCheckoutOrder !== true) {
	$query = '
		select
			o.orddt as orddt,
			oc.regdt as canceldt,
			oc.code as code,
			o.ordno as ordno,
			o.nameOrder as nameOrder,
			o.settlekind as settlekind,
			o.settleInflow as settleInflow,
			m.m_id as m_id,
			m.dormant_regDate as dormant_regDate,
			o.m_no as m_no,
			oi.goodsnm as goodsnm,
			oi.goodsno as goodsno,
			count(oi.goodsno) as count_goods,
			sum(oi.ea) as sea,
			sum(
				CAST((oi.price-oi.memberdc-oi.coupon-oi.oi_special_discount_amount)*oi.ea AS SIGNED)
			) as pay,
			o.step as step,
			oi.istep as istep,
			oi.sno as itemsno
		from
			gd_order_cancel as oc
			inner join gd_order_item as oi on oc.sno = oi.cancel and oc.ordno = oi.ordno
			inner join gd_order as o on oi.ordno=o.ordno
			left join gd_member as m on o.m_no=m.m_no
		'.$strWhere.'
		group by
			oc.sno
		order by
			oc.regdt desc
	';

	$cancelResult = $db->_select_page(20,$page,$query);
}

### 취소사유 배열생성
$r_cancel = codeitem("cancel");
?>

<div class="title title_top">주문취소리스트 [조회]<span>주문취소접수/주문취소완료, 교환접수/재주문, 반품접수/반품완료, 환불접수/환불완료 주문건을 조회합니다.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=order&no=3')"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2"/></a></div>

<form>
<input type="hidden" name="mode" value="<?=$_GET['mode']?>"/>

<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td><span class="small1">키워드검색</span></td>
	<td>
	<select name="skey">
	<option value="all" <?=frmSelected($search['skey'],'all');?>> 통합검색</option>
	<option value="ordno" <?=frmSelected($search['skey'],'ordno');?>> 주문번호</option>
	<option value="nameOrder" <?=frmSelected($search['skey'],'nameOrder');?>> 주문자명</option>
	<option value="bankSender" <?=frmSelected($search['skey'],'bankSender');?>> 입금자명</option>
	<option value="m_id" <?=frmSelected($search['skey'],'m_id');?>> 아이디</option>
	</select>
	<input type="text" name="sword" value="<?=htmlspecialchars($search['sword'])?>" class="line"/>
	</td>
</tr>
<tr>
	<td><span class="small1">주문상태</span></td>
	<td class="noline">

	<table>
	<tr>
		<td><input type="checkbox" name="type[]" value="1" <?=(in_array('1',$search['type'])?'checked':'')?>><span class="small1" style="color:#5C5C5C">취소완료</span></input></td>
		<td><input type="checkbox" name="type[]" value="2" <?=(in_array('2',$search['type'])?'checked':'')?>><span class="small1" style="color:#5C5C5C">환불접수</span></input></td>
		<td><input type="checkbox" name="type[]" value="3" <?=(in_array('3',$search['type'])?'checked':'')?>><span class="small1" style="color:#5C5C5C">환불완료</span></input></td>
		<td><input type="checkbox" name="type[]" value="4" <?=(in_array('4',$search['type'])?'checked':'')?>><span class="small1" style="color:#5C5C5C">반품접수</span></input></td>
		<td><input type="checkbox" name="type[]" value="6" <?=(in_array('6',$search['type'])?'checked':'')?>><span class="small1" style="color:#5C5C5C">반품완료</span></input></td>
		<td><input type="checkbox" name="type[]" value="7" <?=(in_array('7',$search['type'])?'checked':'')?>><span class="small1" style="color:#5C5C5C">교환완료</span></input></td>
	</tr>
	</table>

	</td>
</tr>
<tr>
	<td><span class="small1">주문일</span></td>
	<td>
	<input type="text" name="regdt[]" value="<?=$search['regdt_start']?>" onclick="calendar(event)" class="cline"/> -
	<input type="text" name="regdt[]" value="<?=$search['regdt_end']?>" onclick="calendar(event)" class="cline"/>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd")?>,<?=date("Ymd")?>)"><img src="../img/sicon_today.gif" align="absmiddle"/></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-7 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_week.gif" align="absmiddle"/></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-15 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twoweek.gif" align="absmiddle"/></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-1 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_month.gif" align="absmiddle"/></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-2 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twomonth.gif" align="absmiddle"/></a>
	<a href="javascript:setDate('regdt[]')"><img src="../img/sicon_all.gif" align="absmiddle"/></a>
	</td>
</tr>
<tr>
	<td><span class="small1">결제방법</span></td>
	<td class="noline"><span class="small1" style="color:#5C5C5C">
	<input type="radio" name="settlekind" value="" <?=frmChecked('',$search['settlekind'])?>> 전체</input>
	<input type="radio" name="settlekind" value="a" <?=frmChecked('a',$search['settlekind'])?>> 무통장</input>
	<input type="radio" name="settlekind" value="o" <?=frmChecked('o',$search['settlekind'])?>> 계좌이체</input>
	<input type="radio" name="settlekind" value="v" <?=frmChecked('v',$search['settlekind'])?>> 가상계좌</input>
	<input type="radio" name="settlekind" value="c" <?=frmChecked('c',$search['settlekind'])?>> 신용카드</input>
	<input type="radio" name="settlekind" value="h" <?=frmChecked('h',$search['settlekind'])?>> 휴대폰</input>
	<? if ($cfg['settlePg'] == "inipay") { ?>
	<input type="radio" name="settlekind" value="y" <?=frmChecked('y',$search['settlekind'])?>>옐로페이</input>
	<? } ?>
	</span>
	</td>
</tr>
</table>
<div class="button_top">
<input type="image" src="../img/btn_search2.gif"/>
</div>
</form>

<div style="padding-top:15px"></div>


<table width="100%" cellpadding="2" cellspacing="0">
<tr><td class="rnd" colspan="15"></td></tr>
<tr class="rndbg">
	<th width="25"><span class="small1"><b>번호</b></span></th>
	<th><span class="small1"><b>주문일</b></span></th>
	<th><span class="small1"><b>취소일</b></span></th>
	<th><span class="small1"><b>취소사유</b></span></th>
	<th><span class="small1"><b>주문번호</b></span></th>
	<th width="50"><span class="small1"><b>주문자명</b></span></th>
	<th><span class="small1"><b>상품명</b></span></th>
	<th><span class="small1"><b>수량</b></span></th>
	<th><span class="small1"><b>결제방법</b></span></th>
	<th><span class="small1"><b>판매가</b></span></th>
	<th><span class="small1"><b>상태</b></span></th>
</tr>
<?php
foreach($cancelResult['record'] as $data):
	$stepMsg = getStepMsg($data['step'],$data['istep'],$data['ordno'],$data['itemsno']);

	if($data['_order_type']=='checkout'):
?>

<tr bgcolor="<?=$bgcolor[$data['istep']]?>" height="25">
	<td align="center"><span class="ver71" style="color:#444444"><?=$data['_rno']?></span></td>
	<td align="center"><span class="ver71" style="color:#444444"><?=substr($data['orddt'],0,10)?></span></td>
	<td align="center"><span class="ver71" style="color:#444444"><?=substr($data['canceldt'],0,10)?></span></td>
	<td align="center"><span class="small1" style="color:#444444"><?=$data['code']?></span></td>
	<td align="center">
	<a href="checkout.orderdetail.php?OrderID=<?=$data['ordno']?>"><span class="ver811" style="color:#0074BA"><b><?=$data['ordno']?></b></span></a>
	</td>
	<td align="center">
	<span class="small1"><?=$data['nameOrder']?></span>
	</td>
	<td style="padding-left:10px">
	<span class="small1" style="color:#444444">
	<div style="float:left;text-overflow:ellipsis;overflow:hidden;width:90px" nowrap><a href="javascript:popup('../goods/popup.register.php?mode=modify&goodsno=<?=$data['goodsno']?>',825,600)"><?=$data['goodsnm']?></a></div><?if($data['count_goods']-1)echo"외".($data['count_goods']-1)."건";?>
	</span>
	</td>
	<td align="center"><span class="ver71" style="color:#444444"><?=$data['sea']?></span></td>
	<td align="center"><span class="small1" style="color:#444444"><?=settleIcon($data['settleInflow']);?><?=$data['settlekind']?></span></td>
	<td align="center"><span class="ver71"><b><?=number_format($data['pay'])?><b></span></td>
	<td align="center"><span class="small1" style="color:#0074BA"><?=$data['step']?></span></td>
</tr>
<tr><td colspan="15" class="rndline"></td></tr>

<?php
	else:
?>

<tr bgcolor="<?=$bgcolor[$data['istep']]?>" height="25">
	<td align="center"><span class="ver71" style="color:#444444"><?=$data['_rno']?></span></td>
	<td align="center"><span class="ver71" style="color:#444444"><?=substr($data['orddt'],0,10)?></span></td>
	<td align="center"><span class="ver71" style="color:#444444"><?=substr($data['canceldt'],0,10)?></span></td>
	<td align="center"><span class="small1" style="color:#444444"><?=$r_cancel[$data['code']]?></span></td>
	<td align="center">
	<a href="javascript:popup('popup.order.php?ordno=<?=$data['ordno']?>',800,600)"><span class="ver811" style="color:#0074BA"><b><?=$data['ordno']?></b></span></a>
	</td>
	<td align="center">
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
	<td style="padding-left:10px">
	<span class="small1" style="color:#444444">
	<div style="float:left;text-overflow:ellipsis;overflow:hidden;width:90px" nowrap><a href="javascript:popup('../goods/popup.register.php?mode=modify&goodsno=<?=$data['goodsno']?>',825,600)"><?=$data['goodsnm']?></a></div><?if($data['count_goods']-1)echo"외".($data['count_goods']-1)."건";?>
	</span>
	</td>
	<td align="center"><span class="ver71" style="color:#444444"><?=$data['sea']?></span></td>
	<td align="center"><span class="small1" style="color:#444444"><?=settleIcon($data['settleInflow']);?><?=$r_settlekind[$data['settlekind']]?></span></td>
	<td align="center"><span class="ver71"><b><?=number_format($data['pay'])?><b></span></td>
	<td align="center">
	<a href="javascript:popup('popup.order.php?ordno=<?=$data['ordno']?>',800,600)"><span class="small1" style="color:#0074BA"><?=$stepMsg?></span></a></td>
</tr>
<tr><td colspan="15" class="rndline"></td></tr>

<?php
	endif;
endforeach;
?>
</table>

<?php $pageNavi = &$cancelResult['page']; ?>
<div align="center" class="pageNavi ver8">
	<? if($pageNavi['first']): ?>
		<a href="?<?=getvalue_chg('page',$pageNavi['first'])?>">[1]</a>
	<? endif; ?>
	<? if($pageNavi['prev']): ?>
		<a href="?<?=getvalue_chg('page',$pageNavi['prev'])?>">◀ </a>
	<? endif; ?>
	<? foreach($pageNavi['page'] as $v): ?>
		<? if($v==$pageNavi['nowpage']): ?>
			<a href="?<?=getvalue_chg('page',$v)?>"><?=$v?></a>
		<? else: ?>
			<a href="?<?=getvalue_chg('page',$v)?>">[<?=$v?>]</a>
		<? endif; ?>
	<? endforeach; ?>
	<? if($pageNavi['next']): ?>
		<a href="?<?=getvalue_chg('page',$pageNavi['next'])?>">▶</a>
	<? endif; ?>
	<? if($pageNavi['last']): ?>
		<a href="?<?=getvalue_chg('page',$pageNavi['last'])?>">[<?=$pageNavi['last']?>]</a>
	<? endif; ?>
</div>


<script>window.onload = function(){ UNM.inner();};</script>

<? include "../_footer.php"; ?>