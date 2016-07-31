<?

$location = '현금영수증 서비스 > 현금영수증 발급/조회';
include '../_header.php';
include '../../lib/page.class.php';
include '../../lib/cashreceipt.class.php';
$cashreceipt = new cashreceipt();

### 공백 제거
$_GET['sword'] = trim($_GET['sword']);

list ($total) = $db->fetch("select count(*) from ".GD_CASHRECEIPT);

$selected['skey'][$_GET['skey']] = 'selected';
$checked['singly'][$_GET['singly']] = 'checked';

$db_table = GD_CASHRECEIPT;

if ($_GET['skey'] == 'certno' && strlen($_GET['sword']) == 13)
{
	$certno_encode = encode(substr($_GET['sword'],6,7),1);
	$certno = substr($_GET['sword'],0,6);
	$where[] = "certno = '{$certno}' and certno_encode = '{$certno_encode}'";
}
else if ($_GET['sword']) $where[] = "{$_GET['skey']} like '%{$_GET['sword']}%'";
if ($_GET['regdt'][0] && $_GET['regdt'][1]) $where[] = "regdt between date_format({$_GET['regdt'][0]},'%Y-%m-%d 00:00:00') and date_format({$_GET['regdt'][1]},'%Y-%m-%d 23:59:59')";
if ($_GET['status']){
	foreach ($_GET['status'] as $v) {
		if ($v == 'FFF'){
			$whereAdd = "errmsg != ''";
		}
	}
	if ($whereAdd) {
		$where[] = "(status in ('".implode("','",$_GET['status'])."') OR ".$whereAdd.")";
	} else {
		$where[] = "status in ('".implode("','",$_GET['status'])."')";
		$where[] = "errmsg = ''";
	}
	foreach ($_GET['status'] as $v) $checked['status'][$v] = "checked";
}
if ($_GET['singly'] == 'Y') $where[] = "singly = 'Y'";
else if ($_GET['singly'] == 'N') $where[] = "singly != 'Y'";

$pg = new Page($_GET['page']);
$pg->field = "*";
$pg->setQuery($db_table,$where,'crno desc');
$pg->exec();

$res = $db->query($pg->query);

?>
<script>
function fnRequestReceipt() {

	var frm	= document.frmReceiptList;

	// 처리할 주문건 체크.
	var cnt = 0, chk = document.getElementsByName('chk[]');

	for (var i =0;i<chk.length ;i++)
		if (chk[i].checked == true) cnt++;

	if (cnt == 0) {
		alert('처리할 주문건을 선택해 주세요.');
		return;
	} else {
		if (confirm('선택한 '+ cnt + ' 건의 주문에 대해서 현금영수증을 일괄 발급 하시겠습니까?') ) {
			frm.submit();
		} else {
			return;
		}
	}
}

function iciSelect(obj)
{
	var row = obj.parentNode.parentNode;
	row.style.background = (obj.checked) ? "#F9FFA1" : "#ffffff";
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
</script>
<form name="frmList">

<div class="title title_top">현금영수증 발급/조회<span>현금영수증 발급신청내역 조회 및 현금영수증을 발급할 수 있습니다.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=order&no=18')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a></div>
<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td>검색어</td>
	<td>
	<select name="skey">
	<option value="buyername" <?=$selected['skey']['buyername']?>>주문자
	<option value="ordno" <?=$selected['skey']['ordno']?>>주문번호
	<option value="receiptnumber" <?=$selected['skey']['receiptnumber']?>>승인번호
	<option value="certno" <?=$selected['skey']['certno']?>>인증정보
	<option value="goodsnm" <?=$selected['skey']['goodsnm']?>>상품명
	<option value="buyerphone" <?=$selected['skey']['buyerphone']?>>전화번호
	<option value="buyeremail" <?=$selected['skey']['buyeremail']?>>이메일
	</select>
	<input type="text" name="sword" class="lline" value="<?=$_GET[sword]?>">
	<span class="small4" style="color:#6d6d6d">* 인증정보 - 핸드폰번호/사업자번호</span>
	</td>
</tr>
<tr>
	<td>발급신청일</td>
	<td>
	<input type="text" name="regdt[]" value="<?=$_GET['regdt'][0]?>" onclick="calendar()" onkeydown="onlynumber()"> -
	<input type="text" name="regdt[]" value="<?=$_GET['regdt'][1]?>" onclick="calendar()" onkeydown="onlynumber()">
	<a href="javascript:setDate('regdt[]',<?=date("Ymd")?>,<?=date("Ymd")?>)"><img src="../img/sicon_today.gif" align="absmiddle"></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-7 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_week.gif" align="absmiddle"></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-15 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twoweek.gif" align="absmiddle"></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-1 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_month.gif" align="absmiddle"></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-2 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twomonth.gif" align="absmiddle"></a>
	<a href="javascript:setDate('regdt[]')"><img src="../img/sicon_all.gif" align="absmiddle"></a>
	</td>
</tr>
<tr>
	<td>처리상태</td>
	<td class="noline">
	<? $idx = 0; foreach ($cashreceipt->r_status as $k=>$v){ ?>
	<div style="float:left; padding-right:10px"><font class="small1" color="#5C5C5C"><input type="checkbox" name="status[]" value="<?=$k?>" <?=$checked['status'][$k]?>><?=$v?></div>
	<? } ?>
	<div style="float:left; padding-right:10px"><font class="small1" color="#5C5C5C"><input type="checkbox" name="status[]" value="FFF" <?=$checked['status']['FFF']?>>발급실패</div>
	</td>
</tr>
<tr>
	<td>개별발급</td>
	<td class="noline">
		<div style="float:left; padding-right:10px"><font class="small1" color="#5C5C5C"><input type="radio" name="singly" value="" <?=$checked['singly']['']?>>전체</div>
		<div style="float:left; padding-right:10px"><font class="small1" color="#5C5C5C"><input type="radio" name="singly" value="Y" <?=$checked['singly']['Y']?>>개별발급</div>
		<div style="float:left; padding-right:10px"><font class="small1" color="#5C5C5C"><input type="radio" name="singly" value="N" <?=$checked['singly']['N']?>>주문발급</div>
	</td>
</tr>
</table>
<div class="button_top"><input type="image" src="../img/btn_search2.gif"></div>

<div class="pageInfo ver8">총 <b><?=$total?></b>개, 검색 <b><?=$pg->recode['total']?></b>개, <b><?=$pg->page['now']?></b> of <?=$pg->page['total']?> Pages</div>
</form>

<form name="frmReceiptList" method="post" action="./cashreceipt.indb.php">
<input type="hidden" name="mode" value="chgAllApproval" />
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr><td class="rnd" colspan="12"></td></tr>
<tr class="rndbg">
	<th><a href="javascript:void(0)" onClick="chkBoxAll(document.getElementsByName('chk[]'),'rev')" class=white>선택</a></th>
	<th>번호</th>
	<th>신청일자</th>
	<th>처리일자</th>
	<th>주문번호</th>
	<th>주문자</th>
	<th>발급금액</th>
	<th>주문상태</th>
	<th>처리상태</th>
	<th>정보</th>
	<th>영수증</th>
	<th>비고</th>
</tr>
<tr><td class="rnd" colspan="12"></td></tr>
<col width="4%" align="center">
<col width="4%" align="center">
<col width="10%" align="center">
<col width="10%" align="center">
<col width="17%" align="center">
<col width="10%" align="center">
<col width="10%" align="center">
<col width="10%" align="center">
<col width="10%" align="center">
<col width="5%" align="center">
<col width="5%" align="center">
<col width="5%" align="center">
<?
while ($data=$db->fetch($res))
{
	// 일괄발급 체크박스 처리
	$disabled	= 'disabled="disabled"';

	# 주문상태
	if ($data['singly'] == 'Y')
	{
		$step = '개별발급';
		$disabled	= '';
	}
	else
	{
		$order = $db->fetch("select ordno, step, step2 from ".GD_ORDER." where ordno='{$data['ordno']}'");
		if ($order['ordno'] == '') $step = '삭제주문서';
		else $step = getStepMsg($order['step'],$order['step2'],$order['ordno']);
		if(strlen($step) > 10) $step = substr($step,10);
	}

	# 처리상태
	$status = $cashreceipt->r_status[ $data['status'] ];
	if ($data['errmsg']){
		$status = '<span class="red hand" onclick="alert(\''.addslashes($data['errmsg']).'\')">발급실패</span>';
	}

	// 영수증
	$receipt_button	= '';
	if ($data['receiptnumber'] != '')
	{
		$receipturl =  $cashreceipt->getReceipturl($data['crno']);
		if ($receipturl == '')
			$receipt_button	= '<span class="hand" onclick="alert(\'영수증 출력을 지원하지 않습니다.\');"><img src="../img/i_receipt_off.gif"></span>';
		else
			$receipt_button	= '<span class="hand" onclick="window.open(\''.$receipturl.'\',\'\',\'width=400,height=600,scrollbars=0\');"><img src="../img/i_receipt_on.gif"></span>';
	}

	# 비고
	$button		= array();
	if ($data['status'] == 'RDY')
	{
		if ($order['step2'] == 0){
			$button[] = '<span class="hand" onclick="if(confirm(\'발급하시겠습니까?\')) location.replace(\'./cashreceipt.indb.php?mode=approval&crno='.$data['crno'].'\');"><img src="../img/i_approval.gif"></span>';
			if ($order['step'] > 0){
				$disabled	= '';
			}
		}
		$button[] = '<a href="./cashreceipt.indb.php?mode=refuse&crno='.$data['crno'].'" onclick="return confirm(\'정말로 거절하시겠습니까?\')"><img src="../img/i_refuse.gif"></a>';
	}

	if ($data['status'] == 'ACK')
	{
		$button[] = '<span class="hand" onclick="if(confirm(\'취소하시겠습니까?\')) location.replace(\'./cashreceipt.indb.php?mode=cancel&crno='.$data['crno'].'\');"><img src="../img/i_cancel.gif"></span>';
	}

	if (in_array($data['status'], array('CCR', 'RFS')))
	{
		$existOrder = 'N';
		if ($data['singly'] != 'Y'){
			list($cnt) = $db->fetch("select count(ordno) from ".GD_ORDER." where ordno='{$data['ordno']}' and cashreceipt='{$data['cashreceipt']}'");
			if ($cnt) $existOrder = 'Y';
		}
		$button[] = '<a href="./cashreceipt.indb.php?mode=del&crno='.$data['crno'].'"><img src="../img/i_del.gif" onclick="return del(this, \''.$existOrder.'\');"></a>';
	}
?>
<tr><td height="4" colspan="12"></td></tr>
<tr height="25">
	<td align="center" class=noline><input type="checkbox" name="chk[]" value="<?=$data['ordno']?>" onclick="iciSelect(this)" required label="선택사항이 없습니다" <?=$disabled?>></td>
	<td align="center"><font class="ver8"><?=$pg->idx--?></font></td>
	<td align="center"><font class="ver81"><?=substr($data['regdt'],0,-3)?></font></td>
	<td align="center"><font class="ver811" style="color:#0074BA"><? if ($data['regdt'] != $data['moddt']) { echo substr($data['moddt'],0,-3); }?></font></td>
	<td>
	<? if ($data['singly'] == 'Y'){ ?>
	<font class="ver811 bluep"><b><?=$data['ordno']?></b></font>
	<? } else { ?>
	<a href="view.php?ordno=<?=$data['ordno']?>"><font class="ver811 blue"><b><?=$data['ordno']?></b></font></a>
	<a href="javascript:popup('popup.order.php?ordno=<?=$data['ordno']?>',800,600)"><img src="../img/btn_newwindow.gif" border="0" align="absmiddle"></a>
	<? } ?>
	</td>
	<td align="center"><?=$data['buyername']?></td>
	<td align="center"><font class="ver8 blue"><b><?=number_format($data['amount'])?></b></font></td>
	<td align="center"><?=$step?></td>
	<td align="center"><?=$status?></td>
	<td align="center"><span onclick="popupLayer('./cashreceipt.info.php?crno=<?=$data['crno']?>',650,500)" class="hand"><img src="../img/btn_vieworder.gif" alert="자세히 보기" /></span></td>
	<td align="center"><?=$receipt_button?></td>
	<td align="center"><?=implode(' ', $button)?></td>
</tr>
<tr><td colspan=12 class=rndline></td></tr>
<? } ?>
<tr>
	<td height=20 align="center"><a href="javascript:chkBoxAll(document.getElementsByName('chk[]'),'rev')"><img src="../img/btn_allchoice.gif" border=0></a></td>
	<td colspan=11 height=20></td>
</tr>

</table>
<div align="center" class="pageNavi ver8"><?=$pg->page['navi']?></div>

<div style="height:30px;"><span class="hand" onclick="fnRequestReceipt();"><img src="../img/btn_allapproval.gif" border="0" align="absmiddle" alert="선택 주문 일괄 발급" /></span></div>

</form>

<div id="MSG01">
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">케이스별 처리방법 (주문상태/처리상태)
<ol type="a" style="margin:0px 0px 0px 40px;">
<li>입금확인 + 발급요청 : 영수증을 <b>[발급]</b> 합니다.</li>
<li>입금확인 + 발급완료 : 발급된 <b>[영수증]</b> 을 확인할 수 있습니다.</li>
<li>취소완료 + 발급요청 : 발급요청을 <b>[거절]</b> 합니다.</li>
<li>취소완료 + 발급거절 : 신청내역이 필요없다면 <b>[삭제]</b> 할 수 있습니다.</li>
<li>환불완료 + 발급완료 : 발급된 영수증을 <b>[취소]</b> 합니다.</li>
<li>환불완료 + 발급취소 : 신청내역이 필요없다면 <b>[삭제]</b> 할 수 있습니다.</li>
</ol>
</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">처리상태 안내
<ol type="a" style="margin:0px 0px 0px 40px;">
<li><b>[발급요청]</b> : 현금영수증 발급을 신청한 내역입니다.</li>
<li><b>[발급완료]</b> : 현금영수증이 발급된 내역입니다.</li>
<li><b>[발급취소]</b> : 현금영수증 발급 후 취소한 내역입니다.</li>
<li><b>[발급거절]</b> : 현금영수증 발급 신청을 거절한 내역입니다.</li>
<li><b>[발급실패]</b> : 현금영수증 발급 신청하였으나 실패한 내역입니다.</li>
</ol>
</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">기능 안내
<ol type="a" style="margin:0px 0px 0px 40px;">
<li><b>[발급]</b> : 주문상태가 입금확인 상태일때 발급버튼을 눌러 현금영수증을 발급합니다.</li>
<li><b>[거절]</b> : 거절시에 거절버튼을 눌러주시면 됩니다.</li>
<li><b>[취소]</b> : 취소시에 취소버튼을 눌러 현금영수증을 취소합니다.</li>
<li><b>[삭제]</b> : 거절이나 취소건에 한해 내역을 삭제할 수 있습니다.</li>
<li><b>[선택항목 일괄발급]</b> : 여러 개의 발급요청 항목을 선택하여 일괄발급합니다.</li>

</ol>
</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">※ '보기' 버튼을 클릭하여 해당 현금영수증의 신청정보 및 처리결과(로그)를 확인할 수 있습니다.

</td></tr>

</table>
</div>
<script>cssRound('MSG01')</script>

<script language="javascript"><!--
function del(obj, existOrder)
{
	var orderinit = false;
	if (existOrder == 'Y' && confirm("발급신청내역을 삭제하면서 주문의 현금영수증번호도 초기화하시겠습니까?\n초기화하면 구매자가 마이페이지에서 재신청할 수 있습니다.")) orderinit = true;
	if (confirm("정말로 삭제하시겠습니까?"))
	{
		if (orderinit == true) obj.parentNode.href += '&orderinit=Y';
		return true;
	}
	return false;
}
//--></script>

<? include '../_footer.php'; ?>