<HTML>
<HEAD>
	<TITLE>하나은행 매매보호 서비스 구매확인/거절 데모</TITLE>
	<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
	<style type="text/css">
		BODY{font-size:9pt; line-height:160%}
		TD{font-size:9pt; line-height:160%}
		A {color:blue;line-height:160%; background-color:#E0EFFE}
		INPUT{font-size:9pt;}
		SELECT{font-size:9pt;}
		.emp{background-color:#FDEAFE;}
	</style>
	
	<!-- 하나은행 에스크로 테스트용  플러그인을 위한 JS 파일 -->
	<!--<SCRIPT language=javascript src="http://211.32.31.131:7001/js/cpconfirm.js" ></SCRIPT>-->
	
	<!-- 하나은행 에스크로 실상점용  플러그인을 위한 JS 파일 -->
	<!-- 테스트가 완료되신후 상위 테스트용을 삭제하시고 아래 부분 주석을 제거하셔서 사용하시기 바랍니다. -->
	<SCRIPT language=javascript src="http://www.hanaescrow.com/js/cpconfirm.js"></SCRIPT>


	<!-- 플러그인 종료 후 호출되는 함수 : 플러그인의 결과를 획득하는 코드 삽입 -->
	<SCRIPT language="javascript">
	function UserDefine()
	{
		if(status_cd == "SUCCESS")
		{
			document.cporder.result_value.value= "SUCCESS";
			alert("구매확인/거절이 성공적으로 완료되었습니다.");
			document.cporder.submit();
		}
		else
		if (status_cd == "CANCEL")
		{
			document.cporder.result_value.value= "CANCEL";
			alert("구매확인/거절이 취소되었습니다.");
			document.cporder.submit();
		}
		else
	 	{
			alert(status_cd);
		}
	}
	</SCRIPT>
</HEAD>
<BODY>
	<!-- 구매확인을 위한 폼 : 이름 변경 불가 -->
	<form name=cporder action="escrow_confirm.php">
	<table border=0 width=500>
		
		
	<tr>
	<td>
	<hr noshade size=1>
	<b>하나은행 매매보호 서비스 구매확인/거절</b>
	<hr noshade size=1>
	</td>
	</tr>
	
	<tr>
	<td>
	<font color=gray>
	이 페이지는 고객이 하나은행 에스크로 구매확인/거절을 할 수 있도록 구현한 예시입니다.
	귀사의 요구에 맞게 적절히 수정하여 사용하십시오.
	</font>
	</td>
	</tr>	
	
	<tr>
        <td>
        <br>반드시 플러그인의 설치를 완료한 후에 "확인"을 누르십시오.<br> 플러그인은 자동으로 다운로드되어 설치됩니다.<br> 
다운로드에 다소 시간이 걸리는 수도 있으니 보안경고창이 나타날 때까지 잠시 기다려 주시기 바랍니다.<br>

플러그인이 정상적으로 설치되지않을 시에는 <b>하나은행 콜센터</b>로 문의바랍니다.
        </td>
        </tr>
        
	<tr>
	<td>
	<br>지불시에 얻게 되는 거래번호(TID)를 입력하여 고객이 구매확인/거절 
	 할 수 있도록 구성하십시오. 
	</td>
	</tr>
	</table>
	<br>
	
	
	
	<table border=0 width=500>
<tr>
<td align=center>
<table width=400 cellspacing=0 cellpadding=0 border=0 bgcolor=#6699CC>
<tr>
<td>
<table width=100% cellspacing=1 cellpadding=2 border=0>
<tr bgcolor=#BBCCDD height=25>
<td align=center>
정보를 기입하신 후 확인버튼을 눌러주십시오
</td>
</tr>
<tr bgcolor=#FFFFFF>
<td valign=top>
<table width=100% cellspacing=0 cellpadding=2 border=0>
<tr>
<td align=center>
<br>
<table>

	<tr>
	<td>거래번호 : </td>
	<td><input type=text name=tid size=45 value="<?=$_GET[tid]?>"></td>
	</tr>
	
	<!--
	확인 유형을 선택 
		- 구매확인 : CFRM
		- 구매거절 : CNCL
		- 이용하지 않음 : NULL
	-->
	<tr>
	<td>확인유형 : </td>
	<td>
	<select name=ctype>
	<option value="" selected>선택하십시오
	<option value="CFRM">구매확인
	<option value="CNCL">구매거절
	</select>
	</td>
	</tr>
	
	<tr>
	<td colspan=2 align=center>
	<br>
	<input type="button" value=" 확 인 " onClick=javascript:approve()>
	<br><br>
	</td>
	</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
</table>
<br>

<table border=0 width=500>
	<tr>
	<td><hr noshade size=1></td>
	</tr>
</table>

<!-- rfnd_amt는 부분거절이라는 특별한 환불처리를 위한 옵션이기 때문에, 항상 value를 NULL 로 세팅 -->
<input type="hidden" name="rfnd_amt" value="">
<input type="hidden" name="result_value" value="">		
</form>
</BODY>
</HTML>
