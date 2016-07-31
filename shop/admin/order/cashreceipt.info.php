<?

include '../_header.popup.php';
include '../../lib/cashreceipt.class.php';
$cashreceipt = new cashreceipt();

$data = $db->fetch("select * from ".GD_CASHRECEIPT." where crno='{$_GET['crno']}'");

$pgs = array('inicis' => 'KG이니시스', 'inipay' => 'KG이니시스', 'allat' => '삼성올앳', 'allatbasic' => '삼성올앳', 'dacom' => 'LG U+', 'lgdacom' => 'LG U+', 'kcp'=>'KCP', 'agspay'=>'올더게이트', 'easypay'=>'이지페이', 'settlebank'=>'세틀뱅크');
$pgCompany = $pgs[ $data['pg'] ];
if ($pgCompany == '') $pgCompany = strtoupper($data['pg']);

# 주문상태
if ($data['singly'] == 'Y')
{
	$step = '개별발급';
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
	$status = '<span class="red hand" onclick="alert(\''.$data['errmsg'].'\')">'.$status.' -> 발급실패</span>';
}

?>

<div class="title title_top">현금영수증 신청정보</div>

<table class="tb">
<col class="cellC"><col class="cellL" width="150"><col class="cellC"><col class="cellL">
<tr>
	<td>전자지불(PG)</td>
	<td colspan="3"><?=$pgCompany?></span></td>
</tr>
<tr>
	<td>처리상태</td>
	<td><?=$status?></span></td>
	<td>승인번호</td>
	<td><?=$data['receiptnumber']?></span></td>
</tr>
<tr>
	<td>처리일자</td>
	<td><?=$data['moddt']?></span></td>
	<td>거래번호</td>
	<td><?=($data['tid'] ? $data['tid'] : '―')?></span></td>
</tr>
<tr>
	<td>주문상태</td>
	<td><?=$step?></span></td>
	<td>신청일자</td>
	<td><?=$data['regdt']?></span></td>
</tr>
<tr>
	<td>주문번호</td>
	<td><?=$data['ordno']?></span></td>
	<td>신청 IP</td>
	<td><?=$data['ip']?></span></td>
</tr>
<tr>
	<td>주문자명</td>
	<td><?=$data['buyername']?></span></td>
	<td>연락처</td>
	<td>
		<? if ($data['buyeremail']){ ?>&#149; 이메일 <font color="white">---</font> <?=$data['buyeremail']?><br><? } ?>
		<? if ($data['buyerphone']){ ?>&#149; 전화번호&nbsp; <?=$data['buyerphone']?><? } ?>
	</td>
</tr>
<tr>
	<td>상품명</td>
	<td colspan="3"><?=$data['goodsnm']?></td>
</tr>
<tr>
	<td>상품가격</td>
	<td style="padding:5px;">
	발행액 : <span style="width:70px;text-align:right;"><?=number_format($data['amount'])?></span><br>
	공급액 : <span style="width:70px;text-align:right;"><?=number_format($data['supply'])?></span><br>
	부가세 : <span style="width:70px;text-align:right;"><?=number_format($data['surtax'])?></span>
	</td>
	<td>발행용도</td>
	<td style="padding:5px;">
		<?=($data['useopt'] == '0' ? '개인소득공제용' : '사업자지출증빙용');?>
		<div style="border:solid 1px #dddddd; padding:5px; background-color:#F6F6F6; margin-top:5px;">
			인증정보 <?=$data['certno'];?><?=($data['certno_encode'] ? '-xxxxxxx' : '')?>
		</div>
	</td>
</tr>
<tr>
	<td>로그</td>
	<td colspan="3"><textarea style="width:100%;height:160px;overflow:visible;font:9pt 굴림체;padding:10px 0 0 8px"><?=trim($data['receiptlog'])?></textarea></td>
</tr>
</table>

<script>table_design_load();</script>