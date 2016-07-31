<?

include "../_header.popup.php";

$query = "select a.*,b.step,`b`.`pg` from ".GD_ORDER_ITEM." a left join ".GD_ORDER." b on a.ordno=b.ordno where a.sno in ($_GET[chk])";
$res = $db->query($query);

if(!$_GET[m]) $tmsg = " 취소처리하기 / 반품처리";
else $tmsg = "맞교환";
?>
<body style="margin:0" scroll=no>

<form method=post action="indb.php" onsubmit="return chkForm2(this)">
<input type=hidden name=mode value="chkCancel">
<input type=hidden name=ordno value="<?=$_GET[ordno]?>">

<div style="padding-bottom:5px">&nbsp;<img src="../img/icon_process.gif" align=absmiddle><b style="color:494949">주문상품<?=$tmsg?>하기</b></div>

<table border=4 bordercolor=#000000 style="border-collapse:collapse" width=100%>
<tr><td>
<table class=tb>
<col class=cellC><col class=cellL><col class=cellC><col class=cellL>
<tr>
	<td><font class=small1 color=434343><b>상품정보</td>
	<td style="padding:0" colspan=3>

	<table width=100% cellpadding=0 cellspacing=0>
	<tr bgcolor=#f7f7f7 height=22>
		<th width=80><font class=small1 color=434343><b>주문상태</th>
		<th><font class=small1 color=434343><b>상품명</th>
		<th width=150><font class=small1 color=434343><b>옵션</th>
		<th width=150><font class=small1 color=434343><b>수량</th>
	</tr>
	<? 
	while ($data=$db->fetch($res)){ 
		$step = $data[step];
		$pg = $data['pg'];
	?>
	<input type=hidden name=sno[] value="<?=$data[sno]?>">
	<tr>
		<td align=center><font class=small1 color=ED00A2><b><?=$r_istep[$data[istep]]?></b></font></td>
		<td style="padding-left:10px"><font class=small1><?=$data[goodsnm]?></td>
		<td></td>
		<td align=center><input type=text name=ea[] value="<?=$data[ea]?>" size=3 class="rline"<?php if($data['pg']==='ipay') echo ' readonly style="background:#e3e3e3;"'; ?>><font class=small1>개</td>
	</tr>
	<? } ?>
	</table>

	</td>
</tr>
<tr>
	<td width=130 nowrap><font class=small1 color=434343><b>처리담당자</td>
	<td width=50%><input type=text name=name value="<?=$_COOKIE[member][name]?>" required class="line"></td>
	<td width=130 nowrap><font class=small1 color=434343><b>사유</td>
	<td width=50%>
	<select name=code required>
	<option value="">= 선택하세요 =
	<? foreach ( codeitem("cancel") as $k=>$v){ ?>
	<option value="<?=$k?>"><?=$v?>
	<? } ?>
	</select>
	</td>
</tr>
<tr>
	<td><font class=small1 color=434343><b>상세사유</td>
	<td colspan=3>
	<textarea name=memo style="width:100%;height:65px" required  class="tline"></textarea>
	</td>
</tr>
<?
if($step >= 1 && !$_GET[m]){
?>
<tr>
	<td height=26><font class=small1 color=434343><b>환불계좌정보</td>
	<td colspan=3>
		<div>
			<font class=small1 color=434343>은행 <select name=bankcode >
			<option value="" style="font: 8pt 돋움;">= 선택하세요 =
			<? foreach ( codeitem("bank") as $k=>$v){ ?>
			<option value="<?=$k?>"><?=$v?>
			<? } ?>
			</select>&nbsp;&nbsp;<font class=small1>계좌번호 <input type=text name=bankaccount value=''  class="line">&nbsp;&nbsp;
		<font class=small1>예금주 <input type=text name=bankuser value=''  class="line"></div>
	</td>
</tr>
<?
	}			
?>
<tr>
	<td colspan=4 class=noline align=left>
	<div align=center><input type=image src="../img/btn_confirm_o.gif"></div>
	<div style="padding:8 0 6 67"><font color=black><b>- 상품취소인 경우 -</b></font> &nbsp;&nbsp;<a href="javascript:manual('<?=$guideUrl?>board/view.php?id=order&no=3')"><img src="../img/btn_cancel_manual.gif" border=0 align=absmiddle></a></div>
	<div style="padding:3 0 0 67"><font class=small1 color=444444>현 주문상태가 <font color=ED00A2>주문접수</font>인 경우 바로 <font color=ED00A2>주문취소</font>처리됩니다. 입금한 금액이 없기 때문입니다.</font></div>
	<div style="padding:3 0 0 67"><font class=small1 color=444444>현 주문상태가 <font color=ED00A2>입금확인</font>인 경우 바로 <font color=ED00A2>환불접수리스트</font>에 접수되어 <font color=ED00A2>환불완료</font>를 처리하여야 합니다.</font></div>
	<div style="padding:3 0 0 67"><font class=small1 color=444444>현 주문상태가 <font color=ED00A2>배송중 또는 배송완료</font>인 경우 <font color=ED00A2>반품/교환접수리스트</font>에서 완료처리 후 <font color=ED00A2>환불접수리스트</font>에서 최종 <font color=ED00A2>환불완료</font>를 처리하여야 합니다.</font></div>

    <div style="padding:10 0 0 67"><font class=small1 color=444444><b>1) 무통장, 계좌이체, 가상계좌로 결제한 주문을 취소하는 경우</b></div>
    <div style="padding:3 0 0 82">환불해줄 고객계좌가 필요하므로, 고객으로부터 받은 환불계좌정보를 입력하세요.</div>
	<div style="padding:10 0 0 67"><b>2) 카드로 결제한 주문을 취소하는 경우</b></div>
	<div style="padding:3 0 0 67"><font class=def color=444444>①</font> 카드승인취소를 해야하므로 환불계좌정보는 입력하지 않습니다.</div>
	<div style="padding:3 0 0 82">그러나 '상세사유'란에는 '카드승인취소할것'등의 고객님께서 관리할 수 있는 메모는 남겨두세요.</div>
	<div style="padding:3 0 0 67"><font class=def color=444444>②</font> 카드승인취소는 해당 PG사(전자지불 카드결제사)의 회원사 관리자페이지에 접속하여 카드승인취소를 해야 합니다.</div>
	<div style="padding:3 0 0 67"><font class=def color=444444>※</font> 카드결제 주문건은 반드시 주문리스트에서도 취소처리하고, PG사 관리자페이지에도 접속하여 승인취소처리를 해야 합니다.</div>
	<div style="padding:3 0 10 67"><font class=def color=444444>③</font> PG사의 관리자페이지에서 카드승인 취소를 완료한 후, '환불접수리스트'에서 환불완료처리를 합니다.</div>
	<div style="padding:3 0 0 67"><b>3) 휴대폰으로 결제한 주문을 취소하는 경우</b></div>
	<div style="padding:3 0 0 67"><font class=def color=444444>①</font> 휴대폰 결제 취소를 해야하므로 환불계좌정보는 입력하지 않습니다.</div>
	<div style="padding:3 0 0 82">그러나 '상세사유'란에는 '카드승인취소할것'등의 고객님께서 관리할 수 있는 메모는 남겨두세요.</div>
	<div style="padding:3 0 0 67"><font class=def color=444444>②</font> 휴대폰 결제 취소는 환불 접수 리스트에서 취소를 처리해야 합니다.</div>
	<div style="padding:3 0 0 82"><font class=def color=444444>※</font> 휴대폰 결제 주문건은 반드시 주문리스트에서 취소처리 해야 환불 접수 리스트 결제 취소 가능합니다.</div>

   <table cellpadding=0 cellspacing=0 width=88% align=center>
   <tr><td bgcolor=cccccc width=100% height=1></td></tr></table>

	<div style="padding:13 0 6 67"><font color=black><b>- 상품교환인 경우 -</b></font> &nbsp;&nbsp;<a href="javascript:manual('<?=$guideUrl?>board/view.php?id=order&no=3')"><img src="../img/btn_cancel_manual.gif" border=0 align=absmiddle></a></div>
	<div style="padding:3 0 0 67"><font class=small1 color=444444>현 주문상태가 <font color=ED00A2>배송중 또는 배송완료</font>일때만 <font color=ED00A2>상품교환처리가 가능</font>합니다.</font></div>
	<div style="padding:3 0 0 67"><font class=small1 color=444444><font color=ED00A2>주문접수, 입금확인 상태</font>에서는 <font color=ED00A2>배송되지 않은 상태</font>이기 때문에 교환처리가 아닌 <font color=ED00A2>바로  주문취소</font>가 됩니다.</font></div>
	<div style="padding:3 0 0 67"><font class=small1 color=444444>배송중, 배송완료 상태에서 교환요청을 한 경우, 이곳에서 교환접수 후 <font color=ED00A2>반품/교환접수리스트</font>에서 <font color=ED00A2>교환완료후 재주문넣기</font>를 처리하여야 합니다.</font></div>
	<div style="padding:3 0 5 67"><font class=small1 color=444444><font color=ED00A2>같은 상품으로의 교환</font>만 가능하며 <font color=ED00A2>(맞교환)</font>, 재주문을 자동생성하여 고객에게 다시 배송해야하기 때문입니다.</font></div>
	</td>
</tr>
</table>

</td></tr></table>

</form>

<script>
function chkForm2(f)
{
	var
	step = <?php echo $step; ?>,
	isIpay = <?php echo $pg=='ipay'?'true':'false'; ?>;
	if(isIpay && (step==1 || step==2))
	{
		if(chkForm(f)) return confirm("iPay PG 주문건중 입금확인, 배송준비중 단계의 주문건은 취소시\r\n별도의 환불처리 처리없이 바로 취소완료로 처리됩니다.\r\n취소하시겠습니까?");
		else return false;
	}
	else
	{
		return chkForm(f);
	}
}

table_design_load();
window.onload = function(){
	parent.document.getElementById('ifrmCancel').style.height = document.body.scrollHeight + "px";
}
</script>