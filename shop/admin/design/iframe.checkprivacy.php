<?

$scriptLoad='<script src="../design/codi/_codi.js"></script>';
include "../_header.popup.php";

$dir = str_replace( $_SERVER['SCRIPT_NAME'], "", $_SERVER['SCRIPT_FILENAME'] ) . '/w3c/';
if (is_dir($dir)){
	# 퍼미션 체크
	$dirperms	= decoct(fileperms($dir) - octdec(40000));	// 화일의 경우는 decoct(fileperms($dir) - octdec(100000)); 로 체크
	if($dirperms == "707" || $dirperms == "777"){
		$w3cDirChk	= "";
	}else{
		$w3cDirChk	= "<p /><div><font color=\"#FF0000\"><b>/w3c/폴더의 퍼미션에 문제가 있습니다. 707로 변경을 하시거나, 고도 고객센터로 문의 주시기 바랍니다.</b></font><div>";
	}
	# 전자적 표시 화일 체크
	$od = opendir($dir);
	while ($rd=readdir($od)){
		if (!ereg("\.$",$rd)) $fls[w3c][] = $rd;
	}
}else{
	$w3cDirChk	= "<p /><div><font color=\"#FF0000\"><b>/w3c/폴더가 존재하지 않습니다. 고도 고객센터로 문의 주시기 바랍니다.</b></font><div>";
}

?>

<table cellpadding="0" cellspacing="0" border="0" style="border: 2px solid #dddddd;" width="100%">
<tr>
	<td style="font-size:15px; padding: 5px 5px 15px 5px; color: #0080FF; font-weight: bold;">* 새로운 이용약관, 개인정보취급방침 등 설정 기능 안내</td>
</tr>
<tr>
	<td style="padding: 5px 5px 5px 5px;">이용약관, 개인정보취급방침 등 쇼핑몰 운영 정책에 관련된 안내 사항을 간편하게 등록할 수 있는 기능이 배포되었습니다. 현재와 같이 디자인관리의 각 페이지에 HTML 형태로 내용을 입력하여 사용하여도 무방하지만 새로운 기능을 이용하면 이후 스킨 교체와 관계없이 입력된 내용을 그대로 사용할 수 있으므로 가급적 새로운 기능을 이용해 주시기 바랍니다. <a href="javascript:;" onclick="javascript:parent.document.location.href='../basic/terms.php';" style="color: #0080FF;"><u>[기본설정 > 약관/개인정보 설정 바로가기]</u></a>
	</td>
</tr>
<tr>
	<td style="padding: 5px 5px 5px 5px;">※ <span style="color: red; font-weight: bold;">2014년 07월 31일 이전 제작 무료 스킨</span>을 사용하시는 경우 <span style="font-weight: bold; text-decoration: underline;">반드시 스킨패치</span>를 적용해야 기능 사용이 가능합니다. <a href="http://www.godo.co.kr/customer_center/patch.php?sno=2064" target="_blank" style="color: #0080FF;"><u>[패치 바로가기]</u></a></td>
</tr>
</table>

<div class="title title_top">개인정보취급방침 안내 및 설정</div>

<table cellpadding="0" cellspacing="0" bgcolor="#fafafa">
<tr><td style="padding: 15px 15px 15px 15px; text-align: justify">
<div><font color="#EA0095">&#149; 개인정보취급방침이란?</font><div>
<div style="padding-top:3px" class="small1">'정보통신망 이용촉진 및 정보보호 등에 관한 법률'은 국민의 프라이버시 보호를 위해 사업자의 개인정보 수집, 이용, 제공에 따른 제반 사항 등을 규정하고 있습니다.</div>
<div style="padding-top:3px" class="small1">기업에게는 합리적인 정보보호 준수의무를 부여하고 국민에게는 부당한 개인정보 침해로 인한 피해를 최소화하기 위해 정보통신망법을 개정하여 2007년 7월 27일부터 시행합니다.</div>

<div style="padding-top:3px" class="small1">'개인정보취급방침'은 홈페이지 첫화면, 점포/사무실의 보기 쉬운 장소, 정기 배포 간행물 등에 게재 또는 비치하는 방법으로 이용자가 언제든지 용이하게 확인할 수 있도록 공개하여야 합니다. (시행규칙 제3조의2 제1항)</div>
<div style="padding-top:3px" class="small1">개정법에서는 그동안 웹사이트에서 관행적으로 사용해오던 '개인정보보호정책', '개인정보보호방침' 등의 용어를 '개인정보취급방침'으로 정하여 사용하도록 하였습니다. (시행규칙 제3조의2 제1항)</div>

<!--<div style="padding-top:12">기존의 '개인정보보호정책' 이라는 메뉴가 '개인정보취급방침'으로 변경되었습니다.</div>
<div style="padding-top:3">고객님의 쇼핑몰 하단을 보면 '개인정보보호정책' 이라고 되어있다면, '개인정보취급방침'으로 변경해 놓아야 합니다.</div>-->

<!--<div style="padding-top:12px"><font class=main>※</font> <font color="ea0095">아래 한국정보보호진흥원에서 제공한 가이드를 꼭 읽어보시고, 참조하시기 바랍니다.</font></div>

<div style="padding:5px 0 0 14px"><a href="http://guide.godo.co.kr/shop/sample.pdf" target="_blank"><font color="#0074BA"><u>개인정보취급방침작성예시</u></b></font></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="http://guide.godo.co.kr/shop/action.pdf" target="_blank"><font color="#0074BA"><u>법률개정에 따른 개인정보보호 조치사항</u></b></font></a></div>-->

<div style="padding-top:12px"><font color="ea0095">&#149; 한국정보보호진흥원의 사이트를 참고하시고, 제공하는 가이드를 참조하시기 바랍니다. </font></div>
<div style="padding-top:3px" class="small1"> - 한국정보보호진흥원 : <a href="http://www.1336.or.kr" target="_blank"><font color="#0074BA"><b><u>http://www.1336.or.kr</u></b></font></a></div>
<div style="padding-top:3px" class="small1"> - 가이드 및 작성예시 : <a href="http://www.i-privacy.kr/servlet/command.user.board.BoardCommand?select_cat1=4&select_cat2=1" target="_blank"><font color="#0074BA"><b><u>http://www.i-privacy.kr/servlet/command.user.board.BoardCommand?select_cat1=4&select_cat2=1</u></b></font></a></div>
<div style="padding-top:3px" class="small1"> - 개인정보 보호 조치 : <a href="http://guide.kisa.or.kr/" target="_blank"><font color="#0074BA"><b><u>http://guide.kisa.or.kr</u></b></font></a></div>

</td></tr></table>

<div style="padding-top:12px"></div>

<div class="title title_top">개인정보취급방침 설정<span><font class="small">개인정보취급방침의 내용을 작성하고, 이용자동의사항내용 및 제3자제공,취급업무 위탁 내용에 대해 설정 합니다.</span></div>

<table width="100%" cellpadding="0" cellspacing="0" bgcolor="#fafafa">
<tr><td style="padding: 15px 15px 15px 15px; text-align: justify">

<div><font color="#EA0095">&#149; 사용여부란</font><div>
<div style="padding-top:3px" class="small1"> - 회원가입시 이용자 동의 사항 이외에, 제3자에게 개인정보 제공 및 취급위탁의 여부에 따라 별도로 고지후 동의 여부를 받는지를 설정하는 것입니다.</div>
<div style="padding-top:3px" class="small1"> - 개인정보를 제공 하거나 취급위탁을 하지 않는경우 사용안함으로 체크를 하시면 됩니다.</div>
<div style="padding-top:3px" class="small1"> - 상품 배송, 민원 상담 등 서비스 이행을 위해 반드시 필요한 범위의 위탁 업무의 경우 별도 동의를 받지 않아도 됩니다.</div>

<div style="padding-top:12px"><font color="ea0095">&#149; 개인정보취급방침 내용 </font></div>
<div style="padding-top:3px" class="small1"> - '개인정보취급방침 및 전자적표시 작성방법'에서 12개의 작성 단계 이후의 개인정보취급방침의 내용을 넣으시면 됩니다.</div>
<div style="padding-top:3px" class="small1"> - 내용을 누락을 해서는 안되며, 해당 내용을 토대로 디자인 수정만 가능합니다.</div>

<div style="padding-top:12px"><font color="ea0095">&#149; 개인정보 이용자 동의사항 내용 </font></div>
<div style="padding-top:3px" class="small1"> - '개인정보취급방침 내용'에서 개인정보의 수집·이용목적,수집하는 개인정보의 항목,개인정보의 보유 및 이용 기간만 입력을 합니다.</div>
<div style="padding-top:3px" class="small1"> - 회원가입시에 나오며, 이용자가 동의를 하지 않는 경우 가입이 되지 않습니다.</div>
<div style="padding-top:3px" class="small1"> - 주의 : 3가지 사항의 누락이나 개인정보취급방침 전문 게재를 통한 일괄 동의는 위반사항입니다.</div>

<div style="padding-top:12px"><font color="ea0095">&#149; 개인정보 제3자 제공관련 내용 </font></div>
<div style="padding-top:3px" class="small1"> - '개인정보취급방침 내용'에서 개인정보를 제공받는 자,개인정보를 제공받는 자의 개인정보 이용목적,제공하는 개인정보의 항목,개인정보를 제공받는 자의 개인정보 보유 및 이용기간 만 입력을 합니다.</div>
<div style="padding-top:3px" class="small1"> - 개인정보를 제3자에게 제공하거나 취급위탁을 하는 경우에는 위 개인정보 수집·이용에 대한 동의와는 별도로 아래 사항에 대해 고지하고 동의를 받아야 합니다.</div>
<div style="padding-top:3px" class="small1"> - 회원가입시에 나오며, 이용자가 동의를 하지 않는 경우에도 가입을 할수 있습니다.</div>

<div style="padding-top:12px"><font color="ea0095">&#149; 개인정보 취급업무 위탁관련 내용 </font></div>
<div style="padding-top:3px" class="small1"> - '개인정보취급방침 내용'에서 개인정보취급위탁을 받는 자,개인정보취급위탁을 하는 업무의 내용만 입력을 합니다.</div>
<div style="padding-top:3px" class="small1"> - 개인정보를 제3자에게 제공하거나 취급위탁을 하는 경우에는 위 개인정보 수집·이용에 대한 동의와는 별도로 아래 사항에 대해 고지하고 동의를 받아야 합니다.</div>
<div style="padding-top:3px" class="small1"> - 회원가입시에 나오며, 이용자가 동의를 하지 않는 경우에도 가입을 할수 있습니다.</div>

<div style="padding-top:12px"><font color="ea0095">&#149; 비회원 개인정보 취급방침 내용 </font></div>
<div style="padding-top:3px" class="small1"> - '개인정보취급방침 내용'에서 개인정보의 수집·이용목적,수집하는 개인정보의 항목,개인정보의 보유 및 이용 기간만 입력을 합니다.</div>
<div style="padding-top:3px" class="small1"> - 위 내용중 &quot;개인정보 이용자 동의사항 내용&quot;과 동일하게 작성 하시거나 조금 달리 작성 하셔도 됩니다.</div>
<div style="padding-top:3px" class="small1"> - 비회원 주문시 주문서 페이지에 나오며, 구매자가 동의를 하지 않는 경우 주문이 되지 않습니다.</div>
<div style="padding-top:3px" class="small1"> - 주의 : 3가지 사항의 누락이나 개인정보취급방침 전문 게재를 통한 일괄 동의는 위반사항입니다.</div>

</td></tr></table>

<form method="post" action="../design/indb.php" enctype="multipart/form-data" onsubmit="return chkForm(this)">
<input type="hidden" name="mode" value="checkprivacy" />

<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td width="200">개인정보취급방침 내용</td>
	<td>
	<img src="../img/i_edit.gif" align="absmiddle" class="hand" onclick="popup_return( 'iframe.codi.php?design_file=service/_private.txt&', 'private', 900, 650, 100, 100, 1 );"/>
	</td>
</tr>
<tr>
	<td width="200">개인정보 이용자 동의사항 내용</td>
	<td>
	<img src="../img/i_edit.gif" align="absmiddle" class="hand" onclick="popup_return( 'iframe.codi.php?design_file=service/_private1.txt&', 'private', 900, 650, 100, 100, 1 );"/>
	</td>
</tr>
<tr>
	<td width="200">개인정보 제3자 제공관련 내용</td>
	<td>
	<table cellpadding=0 cellspacing=0 border=0>
	<tr>
		<td align=center><img src="../img/i_edit.gif" align="absmiddle" class="hand" onclick="popup_return( 'iframe.codi.php?design_file=service/_private2.txt&', 'private', 900, 650, 100, 100, 1 );"/></td>
		<td width=40></td>
		<td align=center>사용여부 : </td>
		<td class=noline align=center>
		<input type=radio name=private2YN value='Y' <?=( $cfg['private2YN'] == 'Y' ? 'checked' : '' )?>>사용함</td>
		<td width=10></td>
		<td class=noline align=center>
		<input type=radio name=private2YN value='N' <?=( $cfg['private2YN'] == 'N' ? 'checked' : '' )?>>사용안함</td></tr>
	</table>
	</td>
</tr>
<tr>
	<td width="200">개인정보 취급업무 위탁관련 내용</td>
	<td>
	<table cellpadding=0 cellspacing=0 border=0>
	<tr>
		<td align=center><img src="../img/i_edit.gif" align="absmiddle" class="hand" onclick="popup_return( 'iframe.codi.php?design_file=service/_private3.txt&', 'private', 900, 650, 100, 100, 1 );"/></td>
		<td width=40></td>
		<td align=center>사용여부 : </td>
		<td class=noline align=center>
		<input type=radio name=private3YN value='Y' <?=( $cfg['private3YN'] == 'Y' ? 'checked' : '' )?>>사용함</td>
		<td width=10></td>
		<td class=noline align=center>
		<input type=radio name=private3YN value='N' <?=( $cfg['private3YN'] == 'N' ? 'checked' : '' )?>>사용안함</td></tr>
	</table>
	</td>
</tr>
<tr>
	<td width="200">비회원 개인정보 취급방침 내용</td>
	<td>
	<img src="../img/i_edit.gif" align="absmiddle" class="hand" onclick="popup_return( 'iframe.codi.php?design_file=service/_private_non.txt&', 'private', 900, 650, 100, 100, 1 );"/>
	</td>
</tr>
</table>

<div class="button">
<input type="image" src="../img/btn_save.gif">
<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a>
</div>

</form>



<script>
table_design_load();
setHeight_ifrmCodi();
</script>