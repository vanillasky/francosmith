<?

$location = "기본관리 > 기본정보관리";
include "../_header.php";
@include '../../conf/config.company.php'; //회사소개

if(!$cfg['counterYN']) $cfg['counterYN'] = 1;
$checked['counterYN'][$cfg['counterYN']] = 'checked';

$cfgCompany = @array_map('stripslashes', (array)$cfgCompany);
$cfg['customerHour'] = preg_replace("/<br \/>/","\r\n",$cfg['customerHour']);
?>
<script type="text/javascript" src="../../lib/meditor/mini_editor.js"></script>
<script>
function chkForm2(fm)
{
	if(form.adminEmail.value==''){
		alert('관리자 Email 필수 입력');
		form.adminEmail.focus();
		return false;
	}
}
</script>

<form name=form method=post action="indb.php" onsubmit="return chkForm2(this)">
<input type=hidden name=mode value="config">

<div class="title title_top">기본정보<span>쇼핑몰 기본정보를 입력해주세요</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=2')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a></div>
<table class=tb>
<col class=cellC><col class=cellL><col class=cellC><col class=cellL>
<tr>
	<td>쇼핑몰이름</td>
	<td><input type=text name=shopName value="<?=$cfg[shopName]?>" class=lline style="width:200"></td>
	<td>영문이름</td>
	<td><input type=text name=shopEng value="<?=$cfg[shopEng]?>" class=lline style="width:200"></td>
</tr>
<tr>
	<td>관리자 Email</td>
	<td><input type=text name=adminEmail  class=lline value="<?=$cfg[adminEmail]?>" style="width:200"></td>
	<td>쇼핑몰 URL</td>
	<td colspan=3>http://<input type=text name=shopUrl style="width:163px" value="<?=$cfg[shopUrl]?>"  class=lline></td>
</tr>
</table>
<div style="margin-top:5px"><span class=small><font class=extext>※관리자 Email은 필수항목입니다. 누락시 [회원관리>자동메일설정] 메일이 발송되지 않습니다.</font></span></div>

<div class=title>회사정보<span>쇼핑몰 화면하단의 카피라이트 부분에 표시됩니다</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=2')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a></div>
<table class=tb>
<col class=cellC><col class=cellL><col class=cellC><col class=cellL>
<tr>
	<td>상호명</td>
	<td colspan=3><input type=text name=compName  class=lline value="<?=$cfg[compName]?>" style="width:200"></td>
</tr>
<tr>
	<td>업태</td>
	<td><input type=text name=service value="<?=$cfg[service]?>"  class=line></td>
	<td>종목</td>
	<td><input type=text name=item value="<?=$cfg[item]?>"  class=line></td>
</tr>
<tr>
	<td>사업장우편번호</td>
	<td colspan=3>
	<input type=text name=zipcode[] id="zipcode0" size=3 readonly value="<?=substr($cfg[zipcode],0,3)?>"  class=line> -
	<input type=text name=zipcode[] id="zipcode1" size=3 readonly value="<?=substr($cfg[zipcode],3)?>"  class=line>
	<a href="javascript:popup('../../proc/popup_address.php',500,432)"><img src="../img/btn_zipcode.gif" align=absmiddle></a>
	</td>
</tr>
<tr>
	<td>사업장주소</td>
	<td colspan=3>
	지번	　: <input type=text name=address id="address" style="width:60%" value="<?=$cfg[address]?>"  class=line> <br />
	도로명	 : <input type="text" name="road_address" id="road_address" style="width:60%" value="<?=$cfg['road_address']?>" class="line">
	</td>
</tr>
<tr>
	<td>사업자번호</td>
	<td><input type=text name=compSerial value="<?=$cfg[compSerial]?>"  class=line ></td>
	<td>통신판매신고번호</td>
	<td><input type=text name=orderSerial value="<?=$cfg[orderSerial]?>"  class=line></td>
</tr>
<tr>
	<td>대표자명</td>
	<td><input type=text name=ceoName value="<?=$cfg[ceoName]?>"  class=line></td>
	<td>관리자명<br>(개인정보관리자)</td>
	<td><input type=text name=adminName value="<?=$cfg[adminName]?>"  class=line></td>
</tr>
<tr>
	<td>전화번호</td>
	<td><input type=text name=compPhone value="<?=$cfg[compPhone]?>"  class=line></td>
	<td>팩스번호</td>
	<td><input type=text name=compFax value="<?=$cfg[compFax]?>"  class=line></td>
</tr>
</table>

<div class=title>회사소개<span>쇼핑몰의 회사소개 페이지에 적용됩니다.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=2')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a></div>
<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td colspan="2" style="background-color:white;"><textarea name="compIntroduce" style="width:100%;height:150px" type="editor"><?=$cfgCompany['compIntroduce']?></textarea></td>
</tr>
<tr>
	<td>약도 소스 코드 삽입</td>
	<td>
		<div class="pdv3"><a href="http://blog.daum.net/daummaps/482" target="_blank" onclick="javascript:alert('다음 약도만들기 API 서비스가 종료되었습니다. 다음지도의 약도만들기 서비스를 이용하여 우리회사의 약도를 넣어주세요');"><img src="../img/btn_map.gif" border="0"></a></div>
		<div class="pdv5"><textarea name="compMap" class="line" style="width:100%; height:50px;"><?=$cfgCompany['compMap']?></textarea></div>
		<div class="pdv3 extext">약도만들기 기능을 이용하여 회사소개 하단에 회사 약도를 추가할 수 있습니다.<br>‘약도만들기’ 완료 후 만들어진 ‘태그 코드’를 복사하여 등록해주세요.</div>
	</td>
</tr>
</table>

<div class="title">
	고객센터운영정보<span>쇼핑몰의 메인 및 고객센터 페이지에 표시됩니다.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=2')"><img src="../img/btn_q.gif" border="0" align="absmiddle" /></a>
	<span class="small" style="vertical-align:bottom;"><input type="checkbox" onclick="javascript:copy_field(this.checked);" style="border:0px;"> 기본정보, 회사정보와 동일합니다.</span>
</div>
<table class="tb">
<col class="cellC" /><col class="cellL" /><col class="cellC" /><col class="cellL" />
<tr>
	<td>상담전화번호</td>
	<td><input type="text" name="customerPhone" class="line" value="<?=$cfg['customerPhone']?>" /></td>
	<td>팩스번호</td>
	<td><input type="text" name="customerFax" class="line" value="<?=$cfg['customerFax']?>" /></td>
</tr>
<tr>
	<td>이메일주소</td>
	<td colspan="3"><input type="text" name="customerEmail" class="line" style="width:100%;" value="<?=$cfg['customerEmail']?>" /></td>
</tr>
<tr>
	<td>운영시간</td>
	<td colspan="3"><textarea name="customerHour" class="line" style="width:100%; height:70px;"><?=$cfg['customerHour']?></textarea></td>
</tr>
</table>

<div class=title>상단타이틀<span>브라우저 상단틀에 나오는 타이틀과 검색사이트에서 유용한 키워드를 입력하세요</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=2')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a></div>
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>쇼핑몰타이틀</td>
	<td><input type=text name=title value="<?=$cfg[title]?>"  class=lline></td>
</tr>
<tr>
	<td>검색엔진 키워드</td>
	<td><input type=text name=keywords value="<?=$cfg[keywords]?>" style="width:100%"  class=line></td>
</tr>
</table>

<div class=title>기타설정<span>로그아웃타임 및 기타설정을 관리합니다.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=2')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a></div>
<table class=tb>
<col class=cellC><col class=cellL>
<!--
<tr>
	<td>솔루션폴더명</td>
	<td><input type=text name=rootDir value="<?=$cfg[rootDir]?>">
	<span class=small><font color=#5B5B5B>원하시는 폴더명으로 변경하신후 반드시 그 폴더를 새로 생성하세요</font></span>
	</td>
</tr>
-->
<tr>
	<td>자동로그아웃</td>
	<td>
	로그인 후 <input type=text name=sessTime value="<?=$cfg[sessTime]?>" size=6 onkeydown="onlynumber()"  class="right line"> 분간 클릭이 없으면 자동로그아웃됩니다.
	<span class=small><font class=extext>공란으로 두면 시간제한없음</font></span>
	</td>
</tr>
<tr>
	<td>방문분석 사용여부</td>
	<td class="noline">
		<input type="radio" name="counterYN" value="1" <?=$checked['counterYN'][1]?> />사용 <input type="radio" name="counterYN" value="2" <?=$checked['counterYN'][2]?> />미사용
		<span class="small"><font class="extext">통계/데이터관리의 방문분석 사용여부를 설정합니다.</font></span>
	</td>
</tr>
<tr>
	<td>이미지/컨텐츠<br/>복사방지 설정</td>
	<td class="noline">
		<input type="radio" name="preventContentsCopy" value="1" <?=($cfg[preventContentsCopy] == '1') ? 'checked' : ''?> />사용 <input type="radio" name="preventContentsCopy" value="0" <?=($cfg[preventContentsCopy] != '1') ? 'checked' : ''?> />미사용

		<div class="extext" style="margin:3px 0 0 3px;line-height:150%">
		쇼핑몰에 등록된 이미지 및 컨텐츠 복사방지 사용여부를 설정합니다. <br>
		쇼핑몰 화면에서 이미지 및 컨텐츠에 마우스를 대고 오른쪽 클릭시 contextmenu 가 뜨지 않습니다.<br>
		쇼핑몰 화면에서 이미지 및 컨텐츠에 마우스 드래그시 키보드 Ctrl+C 사용이 안됩니다.<br>
		쇼핑몰 화면에서 이미지 및 컨텐츠 끌어담기 사용이 안됩니다.<br>
		</div>
	</td>
</tr>
</table>


<div class="button">
<input type=image src="../img/btn_register.gif">
<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a>
</div>

</form>


<div id="MSG01">
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">회사정보 : 홈페이지 카피라이트 및 이메일 전송 등에 사용되는 내용입니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">쇼핑몰타이틀 : 윈도우 브라우저의 상단줄에 나타나는 제목입니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">검색키워드 : 사이트 검색 키워드로 사이트 정보를 검색키워드에 입력합니다.</td></tr>
</table>
</div>
<script>
cssRound('MSG01');
mini_editor("../../lib/meditor/", false);
function copy_field(checkValue)
{
	var form = document.form;

	if(checkValue){
		form.customerFax.value		= form.compFax.value;
		form.customerEmail.value	= form.adminEmail.value;
		form.customerPhone.value	= form.compPhone.value;
	} else {
		form.customerFax.value		= "<?=$cfg['customerFax']?>";
		form.customerEmail.value	= "<?=$cfg['customerEmail']?>";
		form.customerPhone.value	= "<?=$cfg['customerPhone']?>";
	}
}
</script>
<? include "../_footer.php"; ?>