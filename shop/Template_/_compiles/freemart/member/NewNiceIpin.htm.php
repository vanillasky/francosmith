<?php /* Template_ 2.2.7 2014/07/30 21:43:01 /www/francotr3287_godo_co_kr/shop/data/skin/freemart/member/NewNiceIpin.htm 000002014 */ ?>
<script language="javascript">
	function validate(fm)
	{
		if (chkRadioSelect(fm,'agree','y','[회원가입 이용약관]에 동의를 하셔야 회원가입이 가능합니다.') === false) return false;
		if (chkRadioSelect(fm,'private1','y','[개인정보보호를 위한 이용자 동의사항]에 동의를 하셔야 회원가입이 가능합니다.') === false) return false;
		return true;
	}

	function goIDCheckIpin()
	{
		var fm ;
		fm = document.getElementById("form");
		if (!validate(fm)) {
			return;
		} else {
			var popupWindow = window.open( "", "popupCertKey", "width=450, height=550, top=100, left=100, fullscreen=no, menubar=no, status=no, toolbar=no, titlebar=yes, location=no, scrollbar=no" );
			ifrmRnCheck.location.href="<?php echo url("member/ipin/IPINMain.php?")?>&callType=joinmember";
		}
		return;
	}

</script>

<div id="div_RnCheck_ipin" style="width:100%;display:none;">

<div style="border:1px solid #DEDEDE;" class="hundred">
<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr>
	<td style="border:5px solid #F3F3F3;padding:10">

	<table align=center>
	<col width=80%>
	<tr>
		<td align=left style="padding-right:20px"><font color='5d5d5d'>
			<img src="/shop/data/skin/freemart/img/ipin/Regist_box_icon.gif"/> 아이핀(i-PIN)은 방송통신위원회에서 주관하는 주민등록번호 대체수단으로 회원님의 주민등록번호 대신<br>&nbsp;&nbsp;&nbsp;&nbsp;아이핀 ID를 NICE신용평가정보(주)
			로부터 발급받아 본인확인을 하는 서비스입니다.<br><br>
			<img src="/shop/data/skin/freemart/img/ipin/Regist_box_icon.gif"/> i-PIN 인증으로 가입시 i-PIN 인증기관을 통해 실명인증을 받게 되므로<br>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $TPL_VAR["shopName"]?>에는 회원님의 주민등록번호가 저장되지 않습니다.
			</font>
		</td>
	</tr>
	</table>
	</td>
</tr>
</table>
<iframe id="ifrmRnCheck" name="ifrmRnCheck" style="width:500px;height:500px;display:none;"></iframe>
</div>

</div>