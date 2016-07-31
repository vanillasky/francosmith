<?
$location = "택배연동 서비스 > 우체국택배 송장번호발급(1단계)";
include "../_header.php";
include "../../lib/page.class.php";
include "../../conf/config.pay.php";
include "../../lib/godopost.class.php";

$godopost = new godopost();

if(!$godopost->linked) {
	msg("우체국택배 연동을 신청하셔야 사용 하실 수 있습니다");
	go("post_admin.php");
	exit;
}

if (get_magic_quotes_gpc()) {
	stripslashes_all($_POST);
	stripslashes_all($_GET);
}

$period = $cfg['orderPeriod'];


$page = ((int)$_GET['page']?(int)$_GET['page']:1);
$sword = $_GET['sword'];
$skey = ($_GET['skey']?$_GET['skey']:'all');
$dvcodeflag = $_GET['dvcodeflag'];
$reserved = $_GET['reserved'];
$arStep = (array)$_GET['step'];
$settlekind = $_GET['settlekind'];
$regdt_start = (int)$_GET['regdt'][0];
$regdt_end = (int)$_GET['regdt'][1];


// 검색 배열 만들기
$arWhere=array();

// 키워드 검색
if($sword) {
	$sword = $db->_escape($sword);
	switch($skey) {
		case 'all':
			$arWhere[] = "(
				o.ordno = '{$sword}' or
				o.nameOrder = '{$sword}' or
				o.bankSender = '{$sword}'
			)";
			break;
		case 'ordno':
			$arWhere[] = "o.ordno = '{$sword}'";
			break;
		case 'nameOrder':
			$arWhere[] = "o.nameOrder = '{$sword}'";
			break;
		case 'bankSender':
			$arWhere[] = "o.bankSender = '{$sword}'";
			break;
	}
}

// 송장번호 발급상태
if($dvcodeflag=='yes') {
	$arWhere[] = 'o.deliverycode <> ""';
}
elseif($dvcodeflag=='no') {
	$arWhere[] = 'o.deliverycode = ""';
}
elseif($dvcodeflag=='error') {
	$arWhere[] = 'TRIM(o.mobileReceiver) NOT REGEXP \'^([0-9]{3,4})-?([0-9]{3,4})-?([0-9]{4})$\'';
}

// 주문상태 검색
if(count($arStep)) {
	foreach($arStep as $k=>$v) {
		$arStep[$k]=(int)$v;
	}
	$arWhere[] = 'o.step in ('.implode(',',$arStep).')';
	$arWhere[] = 'o.step2 = 0';
}

// 주문일 검색
if($regdt_start && $regdt_end) {
	$tmp_start = date("Y-m-d 00:00:00",strtotime($regdt_start));
	$tmp_end = date("Y-m-d 23:59:59",strtotime($regdt_end));
	$arWhere[] = "o.orddt between '{$tmp_start}' and '{$tmp_end}'";
}
elseif($regdt_start) {
	$tmp_start = date("Y-m-d 00:00:00",strtotime($regdt_start));
	$arWhere[] = "o.orddt >= '{$tmp_start}'";
}
elseif($regdt_end) {
	$tmp_end = date("Y-m-d 23:59:59",strtotime($regdt_end));
	$arWhere[] = "o.orddt <= '{$tmp_end}'";
}

// 결제방법 검색
if($settlekind) {
	$settlekind = $db->_escape($settlekind);
	$arWhere[] = "o.settlekind = '{$settlekind}'";
}

// 예약상태 검색관련
if($reserved=='yes') {
	$strJoinReserve = ' left join gd_godopost_reserved as p on o.deliverycode=p.deliverycode and o.deliveryno="100"';
	$arWhere[] = 'p.deliverycode';
}
elseif($reserved=='no') {
	$strJoinReserve = ' left join gd_godopost_reserved as p on o.deliverycode=p.deliverycode and o.deliveryno="100"';
	$arWhere[] = 'isnull(p.deliverycode)';
}

if(count($arWhere)) {
	$strWhere = 'where '.implode(" and ",$arWhere);
}


$query = "
	select 
		o.ordno,
		o.orddt,
		group_concat(concat_ws(',',cast(i.goodsno as char),cast(i.ea as char),i.goodsnm) SEPARATOR '\n\n') as goodsinfo,
		o.nameOrder,
		o.settlekind,
		o.settleprice,
		o.phoneReceiver,
		o.mobileReceiver,
		o.delivery,
		o.deli_type,
		o.deliveryno,
		o.deliverycode,
		o.step,
		o.step2
	from
		gd_order as o 
		left join gd_order_item as i on o.ordno=i.ordno
		{$strJoinReserve}
	{$strWhere}
	group by
		o.ordno
	order by
		o.ordno asc
";

$result = $db->_select_page(10,$page,$query);

foreach($result['record'] as $k=>$v) {
	$ar_line = explode("\n\n",$v['goodsinfo']);
	$ar_goods = array();
	foreach($ar_line as $each_line) {
		preg_match('/^([0-9]+),([0-9]+),(.+)$/',$each_line,$matches);
		$ar_goods[]=array(
			'goodsno'=>$matches[1],
			'ea'=>$matches[2],
			'goodsnm'=>$matches[3]
		);
	}
	unset($result['record'][$k]['goodsinfo']);
	$result['record'][$k]['goods']=$ar_goods;
}


// 송장번호 미발급 주문 갯수 알아내기
$arWhere[] = 'o.deliverycode = ""';
$strWhere = 'where '.implode(" and ",$arWhere);
$query = "
	select
		count(*) as unassign_count
	from
		gd_order as o
	{$strWhere}
";
$tmp = $db->_select($query);
$unassign_count = $tmp[0]['unassign_count'];



// 배송업체 정보
$query = "select deliveryno,deliverycomp from gd_list_delivery where useyn='y' order by deliverycomp asc";
$delivery_list = $db->_select($query);


?>
<script language='javascript'> 
function checkAll() {
	var ar_checkbox = $$(".sel_checkbox");
	if(ar_checkbox[0]) {
		var action=!ar_checkbox[0].checked;
	}
	ar_checkbox.each(function(item){
		item.checked=action;
		boxClick(item);
	});
}

function boxClick(obj) {
	var ar_checkbox = $$(".sel_checkbox");
	var checked_number=0;
	ar_checkbox.each(function(item){
		if(item.checked) checked_number++;
	});
	$('checked_number').innerHTML=checked_number;
}

function assign_deliverycode() {
	if(document.fmList.ps_method[0].checked) {
		var ar_checkbox = $$(".sel_checkbox");
		var checked_number=0;
		ar_checkbox.each(function(item){
			if(item.checked) checked_number++;
		});
		if(checked_number==0) {
			alert('선택된 주문이 없습니다');
		}
		else {
			document.fmList.submit();
		}
	}
	else {
		<? if($unassign_count): ?>
			document.fmList.submit();
		<? else: ?>
			alert('송장번호 미발급 주문이 없습니다');
		<? endif; ?>
	}
}

function popupGodoPostItemAssign(ordno) {
	popupLayer('popup.godopost.itemassign.php?ordno='+ordno,780,500);
}

</script>
<div class="title title_top">우체국택배 송장번호발급<span>우체국택배 시스템에서 송장번호를 자동으로 발급받습니다</span>
<a href="javascript:manual('<?=$guideUrl?>board/view.php?id=order&no=13')"><img src="../img/btn_q.gif" border=0 hspace=2 align=absmiddle></a>
</div>

<form name="fm" method='get'>

<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td><font class="small1">쇼핑몰 송장번호 할당셋팅</font></td>
	<td>
		<? if($set['delivery']['basis'] == 0): ?>
			주문별 배송처리
		<? else: ?>
			상품별 배송처리
		<? endif; ?>
	</td>
</tr>
<tr>
	<td><font class="small1">키워드검색</font></td>
	<td>
		<select name="skey">
			<option value="all" <?=frmSelected($skey,'all')?>> 통합검색
			<option value="a.ordno" <?=frmSelected($skey,'a.ordno')?>> 주문번호
			<option value="nameOrder" <?=frmSelected($skey,'nameOrder')?>> 주문자명
			<option value="bankSender" <?=frmSelected($skey,'bankSender')?>> 입금자명
			<option value="m_id" <?=frmSelected($skey,'m_id')?>> 아이디
		</select>
		<input type="text" name="sword" value="<?=$_GET[sword]?>" class="line">
	</td>
</tr>
<tr>
	<td><font class="small1">송장번호 발급상태</font></td>
	<td class="noline">
	<table>
		<tr>
			<td><input type="radio" name="dvcodeflag" value="" <?=frmChecked($dvcodeflag,'')?>><font class="small1" color="#5C5C5C">전체</font></td>
			<td><input type="radio" name="dvcodeflag" value="yes" <?=frmChecked($dvcodeflag,'yes')?>><font class="small1" color="#5C5C5C">발급</font></td>
			<td><input type="radio" name="dvcodeflag" value="no" <?=frmChecked($dvcodeflag,'no')?>><font class="small1" color="#5C5C5C">미발급</font></td>
			<td><input type="radio" name="dvcodeflag" value="error" <?=frmChecked($dvcodeflag,'error')?>><font class="small1" color="#DB5200">연락처오류</font></td>
		</tr>
	</table>
	</td>
</tr>
<tr>
	<td><font class="small1">예약상태</font></td>
	<td class="noline">
	<table>
		<tr>
			<td><input type="radio" name="reserved" value="" <?=frmChecked($reserved,'')?>><font class="small1" color="#5C5C5C">전체</font></td>
			<td><input type="radio" name="reserved" value="no" <?=frmChecked($reserved,'no')?>><font class="small1" color="#5C5C5C">예약 전</font></td>
			<td><input type="radio" name="reserved" value="yes" <?=frmChecked($reserved,'yes')?>><font class="small1" color="#5C5C5C">예약 후</font></td>
		</tr>
	</table>
	</td>
</tr>
<tr>
	<td><font class="small1">주문상태</font></td>
	<td class="noline">
	<table>
		<tr>
			<td><input type="checkbox" name="step[0]" value="0" <?=frmChecked($arStep[0],'0')?>><font class="small1" color="#5C5C5C">주문접수</font></td>
			<td><input type="checkbox" name="step[1]" value="1" <?=frmChecked($arStep[1],'1')?>><font class="small1" color="#5C5C5C">입금확인</font></td>
			<td><input type="checkbox" name="step[2]" value="2" <?=frmChecked($arStep[2],'2')?>><font class="small1" color="#5C5C5C">배송준비중</font></td>
		</tr>
	</table>
	</td>
</tr>
<tr>
	<td><font class=small1>주문일</td>
	<td>
	<input type=text name=regdt[] value="<?=$_GET[regdt][0]?>" onclick="calendar(event)" class=cline> -
	<input type=text name=regdt[] value="<?=$_GET[regdt][1]?>" onclick="calendar(event)" class=cline>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd")?>,<?=date("Ymd")?>)"><img src="../img/sicon_today.gif" align=absmiddle></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-7 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_week.gif" align=absmiddle></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-15 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twoweek.gif" align=absmiddle></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-1 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_month.gif" align=absmiddle></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-2 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twomonth.gif" align=absmiddle></a>
	<a href="javascript:setDate('regdt[]')"><img src="../img/sicon_all.gif" align=absmiddle></a>
	</td>
</tr>
<tr>
	<td><font class="small1">결제방법</font></td>
	<td class="noline"><font class="small1" color="#5C5C5C">
	<input type="radio" name="settlekind" value="" <?=frmChecked($settlekind,'')?>> 전체
	<input type="radio" name="settlekind" value="a" <?=frmChecked($settlekind,'a')?>> 무통장
	<input type="radio" name="settlekind" value="c" <?=frmChecked($settlekind,'c')?>> 카드
	</td>
</tr>
</table>

<table width="100%">
<tr>
	<td align="center">
	<input type="image" src="../img/btn_search2.gif" border="0" style="border:0px">
	</td>
</tr>
</table>
</form>

<br>


<form name="fmList" method="post" action="indb.godopost.assign.php" target="ifrmHidden">
<input type="hidden" name="mode" value="order_assign">

<input type="hidden" name="skey" value="<?=$skey?>">
<input type="hidden" name="sword" value="<?=$sword?>">
<input type="hidden" name="dvcodeflag" value="<?=$dvcodeflag?>">
<input type="hidden" name="reserved" value="<?=$reserved?>">
<input type="hidden" name="regdt[0]" value="<?=$regdt[0]?>">
<input type="hidden" name="regdt[1]" value="<?=$regdt[1]?>">
<input type="hidden" name="settlekind" value="<?=$settlekind?>">

<? foreach($arStep as $k=>$v): ?>
	<input type="hidden" name="step[<?=$k?>]" value="<?=$v?>"/>
<? endforeach; ?>



<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr><td class="rnd" colspan="8"></td></tr>
<tr class="rndbg">
	<th><span onclick="checkAll()" style="cursor:pointer">선택</span></th>
	<th>주문일</th>
	<th>주문번호</th>
	<th>상품명</th>
	<th>주문자명</th>
	<th>결제가</th>
	<th>운송장정보</th>
	<th>상태</th>
</tr>
<tr><td class="rnd" colspan="8"></td></tr>

<col align="center" width="40"/>
<col align="center" width="110" />
<col align="center" width="100" />
<col align="left" />
<col align="center" width="70" />
<col align="center" width="90" />
<col align="center" width="210" />
<col align="center" width="80" />

<? foreach($result['record'] as $k=>$data): ?>
<?
$data['orddt'] = date("Y-m-d H:i",strtotime($data['orddt']));
if(count($data['goods'])>1) {
	$data['goodsnm']=$data['goods'][0]['goodsnm'].' 외 '.(count($data['goods'])-1).'건';
}
else {
	$data['goodsnm']=$data['goods'][0]['goodsnm'];
}
?>


<tr><td height="4" colspan="8"></td></tr>
<tr height="25">
	<td class="noline"><input type="checkbox" name="sel_ordno[]" value="<?=$data['ordno']?>" class="sel_checkbox" onclick="boxClick(this)"></td>
	<td><font class="ver81" color="#616161"><?=$data['orddt']?></font></td>
	<td><a href="view.php?ordno=<?=$data[ordno]?>"><font class=ver81 color=0074BA><b><?=$data[ordno]?></b></font></a></td>
	<td><font class="ver81" color="#616161"><?=$data['goodsnm']?></font></td>
	<td><font class="ver81" color="#616161"><?=$data['nameOrder']?></font></td>
	<td><font class="ver81" color="#616161"><?=number_format($data['settleprice'])?>원</font></td>
	<td><font class="ver81" color="#616161">
	<div>
	<? if (getPurePhoneNumber($data[mobileReceiver]) == '' && $data[deliveryno] == '0' && $data[deliverycode] == '') { ?>
	<font class="small1" color="#DB5200">연락처오류</font>
	<? } else { ?>
		<? foreach($delivery_list as $each_delivery): ?>
			<? if($each_delivery['deliveryno']==$data['deliveryno']):?>
				<?=$each_delivery['deliverycomp']?>
			<? endif;?>
		<? endforeach; ?>
		<?=$data['deliverycode']?>
	<? } ?>
	</div>
	<div>
		<? if($set['delivery']['basis'] == '1'): ?>
			<input type="button" value=" 상품별 송장 번호 입력 " onclick="popupGodoPostItemAssign('<?=$data['ordno']?>')">
		<? endif; ?>
	</div>
	</font></td>
	<td><font class="ver81" color="#616161"><?=getStepMsg($data['step'],$data['step2'])?></font></td>
</tr>


<? endforeach; ?>

<tr><td height="4"></td></tr>
<tr><td colspan="8" class="rndline"></td></tr>
</table>


<? $pageNavi = &$result['page']; ?>
<div align="center" class="pageNavi ver8">
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
</div>


<div class="noline" style="border:1px solid #cccccc;padding:10px">

<table>
<tr>
<td>
	<input type="radio" name="ps_method" value="selected" checked> 
	선택된 <span id="checked_number">0</span>개의 주문에 대해서 새로운 우체국택배 송장번호를 발급합니다<br>
	<input type="radio" name="ps_method" value="searched"> 
	검색된 주문 <?=$result['page']['totalcount']?>개의 중,
	송장번호 미발급 주문 (<?=$unassign_count?>)개에 일괄 발급합니다.
</td>
<td style="padding-left:30px">
	<img src="../img/btn_postoffice_number.gif" style="cursor:pointer" onclick="assign_deliverycode()">
</td>
</table>

</div>




</form>

<br>


<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>

<tr><td><font class=def1 color=ffffff><strong>1.선택된 주문건별 송장번호 발급</strong></td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">리스트에서 송장번호를 발급 받고자 하는 주문건을 선택하여 발급받을 수 있습니다.</td></tr>
<tr><td height=8></td></tr>
<tr><td><font class=def1 color=ffffff><strong>2.송장번호 일괄 발급</strong></td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">검색된 주문건에 대해 일괄로 송장 번호를 발급 받을 수 있습니다.</td></tr>
<tr><td height=8></td></tr>
<tr><td><font class=def1 color=ffffff><strong>3.송장번호 발급 누락건 처리</strong></td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">상품 수신자의 연락처 정보가 잘못된 형식인 경우(ex : 특수문자,영문 등)의 주문건은 송장번호가 발급되지 않습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">발급이 되지 않은 건 운송장정보 영역에 '연락처 오류'로 표기됩니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">연락처 오류의 주문건은 연락처를 수정 하신 후 재 발급 받으시길 바랍니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">주문번호를 클릭하면 주문 정보를 수정하실 수 있습니다.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

<script>window.onload = function(){ UNM.inner();};</script>


<? include "../_footer.php"; ?>