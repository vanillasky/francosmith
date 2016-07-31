<?
$location = "기본관리 > 보안서버 인증서비스 신청/관리";
include "../_header.php";

// ssl 사용 체크

function check_freeSSL($basicDomain,$rootdir){
	global $godo;
	$fp = @fsockopen("ssl://".$basicDomain, 443,$errno,$errstr,2);
	if ($fp){
		fwrite($fp, "GET $rootdir/freessl.php HTTP/1.0\r\nHost: $basicDomain\r\n\r\n");
		while (!feof($fp)){
		    $out .= fread($fp, 1024);
		}
		fclose($fp);
		$out = explode("\r\n\r\n",$out);
		array_shift($out);
		$out = implode("",$out);
		if(trim($out) == $godo['sno']){
		    return true;
		}
	}
	return false;
}

// 무료보안서버정보 저장하기
$useFreeSSL = false;
if(!in_array($godo['webCode'],array('webhost_outside','webhost_server'))){
    $freedomain_result = readurl("http://gongji.godo.co.kr/userinterface/get.basicdomain.php?sno=".$godo['sno']);
    if(trim($freedomain_result)) $useFreeSSL = check_freeSSL($freedomain_result,$cfg[rootDir]);
}

### 초기값 세팅
if($cfg['ssl']== 1 && $cfg['ssl_port'] && !$cfg['ssl_type']) $cfg['ssl_type'] = 'godo';

if(!$cfg['ssl_seal']) $cfg['ssl_seal'] = '0';
if(!$cfg['free_ssl_seal']) $cfg['free_ssl_seal'] = '0';

if($cfg['ssl_type'] == 'free' && !$useFreeSSL) $cfg['ssl_type'] = "";
if( $cfg['ssl_type']=='free' && in_array($godo['webCode'],array('webhost_outside','webhost_server')) ){
    $cfg['ssl_type'] = "";
}else if( $cfg['ssl_type']=='godo' && $godo['webCode']=='webhost_outside' ){
    $cfg['ssl_type'] = "";
}else if( $cfg['ssl_type']=='direct' && $godo['webCode']!='webhost_outside' ){
    $cfg['ssl_type'] = "";
}

// 유료보안서버
if($cfg['ssl_domain'])
{
	$today = (int)date('Ymd');
	$ssl_sdate = (int)$cfg['ssl_sdate'];
	$ssl_edate = (int)$cfg['ssl_edate'];
	if($ssl_sdate <= $today && $ssl_edate >= $today)
	{
		if($cfg['ssl_type']=='godo')
		{
			$sslStep='used';
		}
		else
		{
			$sslStep='use';
		}
	}
	else
	{
		if($cfg['ssl_step']=='wait')
		{
			$sslStep='wait';
		}
		elseif($cfg['ssl_step']=='process')
		{
			$sslStep='process';
		}
		else
		{
			$sslStep='renewrequest';
		}
	}
}
else
{
	if($cfg['ssl_step']=='wait')
	{
		$sslStep='wait';
	}
	elseif($cfg['ssl_step']=='process')
	{
		$sslStep='process';
	}
	else
	{
		$sslStep='request';
	}
}
$arSslStep=array(
	'used'=>'사용중',
	'use'=>'사용가능',
	'request'=>'신청대기',
	'renewrequest'=>'연장신청대기',
	'wait'=>'입금대기중',
	'process'=>'처리중',
);


?>
<script>

function clickDirect() {
	var dr=document.getElementById('tableDirect');
	if(dr) dr.style.display='block';
	var fr=document.getElementById('tableFree');
	if(fr) fr.style.display='none';
	var gd=document.getElementById('tableGodo');
	if(gd) gd.style.display='none';
	var nn=document.getElementById('tableNone');
	if(nn) nn.style.display='none';

}
function clickNone() {
	var dr=document.getElementById('tableDirect');
	if(dr) dr.style.display='none';
	var fr=document.getElementById('tableFree');
	if(fr) fr.style.display='none';
	var gd=document.getElementById('tableGodo');
	if(gd) gd.style.display='none';
	var nn=document.getElementById('tableNone');
	if(nn) nn.style.display='block';
}
function clickFree() {
	var dr=document.getElementById('tableDirect');
	if(dr) dr.style.display='none';
	var fr=document.getElementById('tableFree');
	if(fr) fr.style.display='block';
	var gd=document.getElementById('tableGodo');
	if(gd) gd.style.display='none';
	var nn=document.getElementById('tableNone');
	if(nn) nn.style.display='none';
}
function clickGodo() {
	var dr=document.getElementById('tableDirect');
	if(dr) dr.style.display='none';
	var fr=document.getElementById('tableFree');
	if(fr) fr.style.display='none';
	var gd=document.getElementById('tableGodo');
	if(gd) gd.style.display='block';
	var nn=document.getElementById('tableNone');
	if(nn) nn.style.display='none';
}


</script>

<? if($godo['webCode'] == 'webhost_server'): ?>
	<form name=form method=post action="indb.php" >
	<input type=hidden name=mode value="ssl">
	<div class="title title_top">보안서버 인증서비스 신청/관리<span></span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=25')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a></div>

	<table class=tb>
	<col class=cellC><col class=cellL>
	<tr>
		<td>보안서버 사용여부</td>
		<td>
		<input type="radio" name="ssl_type" value="" style='border:0px' onclick="clickNone()" <?if($cfg['ssl_type']==''){?>checked<?}?>>
		사용안함
		&nbsp;
		<input type="radio" name="ssl_type" value="godo" style='border:0px' onclick="clickGodo()" <?if($cfg['ssl_type']=='godo'){?>checked<?}?>>
		유료 보안서버 사용
		</td>
	</tr>
	</table>

	<table class=tb style='border-top:none;display:none' id="tableNone">
	<col class=cellC><col class=cellL>
	<tr>
		<td style='border-top:none'>보안서버 사용기간</td>
		<td style='border-top:none'></td>
	</tr>
	<tr>
		<td>보안서버 도메인</td>
		<td></td>
	</tr>
	<tr>
		<td>보안서버 포트</td>
		<td></td>
	</tr>
	</table>



	<table class=tb style='border-top:none;display:none' id="tableGodo">
	<col class=cellC><col class=cellL>
	<tr>
	<td style='border-top:none'>보안서버 사용여부</td>
	<td style='border-top:none'><font color=0a9a14><b>
	<?=$arSslStep[$sslStep]?>
	</b></font>

	<? if(in_array($sslStep,array('request','renewrequest'))) : ?>
	&nbsp;&nbsp;<font class=extext>(보안서버 안내 및 사용신청은 <a href="http://hosting.godo.co.kr/valueadd/ssl_service.php" target=_blank><font class=extext_l>[여기]</font></a> 를 클릭하세요)</font>
	<? endif; ?>
	</td>
	</tr>
	<tr>
		<td>보안서버 사용기간</td>
		<td><?if($cfg[ssl_sdate] && $cfg[ssl_edate]){?><?=$cfg[ssl_sdate]?> - <?=$cfg[ssl_edate]?><?}?></td>
	</tr>
	<tr>
		<td>보안서버 도메인</td>
		<td><font class=ver8><b>https://<?=$cfg[ssl_domain]?></b></font></td>
	</tr>
	<tr>
		<td>보안서버 포트</td>
		<td><font class=ver8><b><?=$cfg[ssl_port]?></b></font></td>
	</tr>
	<tr>
		<td>보안서버 적용범위</td>
		<td>
		로그인, 회원가입, 회원정보수정, 주문, 일대일문의, 제휴문의, 게시판 등의 페이지에서
		이용자의 개인정보 데이터가 암호화되어 전송됨<br>
		(솔루션내의 폴더 중 member, order 폴더 페이지들에 접근할 경우 SSL 보안이 적용)<br>
		<font style='color:red'>※주의 : 보안서버 사용이 적용되면 반드시 주문(결제포함) 테스트로
		정상적으로 주문이 이뤄지는지 확인하시기 바랍니다.</font>
		</td>
	</tr>
	</table>

	<div class="button">
	<? if(in_array($sslStep,array('request','renewrequest'))) { ?>
	<a href="http://hosting.godo.co.kr/valueadd/ssl_service.php" target=_blank><img src="../img/btn_register.gif"></a>
	<? }else{ ?>
	<input type=image src="../img/btn_register.gif">
	<? } ?>
	</div>

	</form>
	<?if($cfg['ssl_type']==''){?><script>clickNone()</script><?}?>
	<?if($cfg['ssl_type']=='godo'){?><script>clickGodo()</script><?}?>
	<?if($cfg['ssl_type']=='free'){?><script>clickFree()</script><?}?>
<? elseif(!in_array($godo['webCode'],array('webhost_outside','webhost_server'))): ?>
	<script>
	function check_request(obj){
	    var free = '<?=$useFreeSSL?>';
	    var godo = '<?=$sslStep?>';
	    if(obj.ssl_type[1].checked == true && !free){
		alert("무료SSL설치요청을 먼저 해주시기 바랍니다.\n무료SSL이 설치된 후 무료 보안서버 사용을 설정하실 수 있습니다.");
		return false;
	    }
	    if(obj.ssl_type[2].checked == true && (godo == 'request' || godo == 'renewrequest')){
		window.open("http://hosting.godo.co.kr/valueadd/ssl_service.php");
		return false;
	    }
	    return true;
	}
	</script>
	<form name=form method=post action="indb.php" onsubmit="return check_request(this);">
	<input type=hidden name=mode value="ssl">
	<input type=hidden name='ssl_freedomain' value="<?=$freedomain_result?>">
	<div class="title title_top">보안서버 인증서비스 신청/관리<span></span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=25')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a></div>

	<table class=tb>
	<col class=cellC><col class=cellL>
	<tr>
		<td>보안서버 사용여부</td>
		<td>
		<input type="radio" name="ssl_type" value="" style='border:0px' onclick="clickNone()" <?if($cfg['ssl_type']==''){?>checked<?}?>>
		사용안함
		&nbsp;
		<input type="radio" name="ssl_type" value="free" style='border:0px' onclick="clickFree()" <?if($cfg['ssl_type']=='free'){?>checked<?}?>>
		무료 보안서버 사용
		<input type="radio" name="ssl_type" value="godo" style='border:0px' onclick="clickGodo()" <?if($cfg['ssl_type']=='godo'){?>checked<?}?>>
		유료 보안서버 사용
		</td>
	</tr>
	</table>

	<table class=tb style='border-top:none;display:none' id="tableNone">
	<col class=cellC><col class=cellL>
	<tr>
		<td style='border-top:none'>보안서버 사용기간</td>
		<td style='border-top:none'></td>
	</tr>
	<tr>
		<td>보안서버 도메인</td>
		<td></td>
	</tr>
	<tr>
		<td>보안서버 포트</td>
		<td></td>
	</tr>
	</table>

	<table class=tb style='border-top:none;display:none' id="tableFree">
	<col class=cellC><col class=cellL>
	<tr>
	<td style='border-top:none'>보안서버 사용여부</td>
	<td style='border-top:none'><font color=0a9a14><b>
	<?if($cfg['ssl_type']=='free'){?>사용중<?}?>
	<?if($cfg['ssl_type']!='free' && $useFreeSSL){?>사용가능(등록버튼을 누르세요)<?}?>
	<?if($cfg['ssl_type']!='free' && !$useFreeSSL){?><a href="freessl.php" target="ifrmHidden">[무료SSL설치요청]</a><?}?>
	</b></font> </td>
	</tr>
	<tr>
		<td>보안서버 사용기간</td>
		<td>제한없음</td>
	</tr>
	<tr>
		<td>보안서버 적용범위</td>
		<td>
		로그인, 회원가입, 회원정보수정 페이지에서 이용자의 개인정보 데이터가 암호화되어 전송됨
		</td>
	</tr>
	<tr>
		<td>적용방법 <a href="<?=$guideUrl?>board/view.php?id=basic&no=25" target='_blank'><img src="../img/btn_q.gif" align="absmiddle"></a></td>
		<td>
			<div>회원 로그인,회원가입 form의 action태그의 값을 제공하는 치환코드로 변경하여야 합니다.</div>
		    <div class="small extext">
		    <div style="padding-top:5"><b>적용위치 : </b>회원 로그인 form, 회원가입 form</div>
		    <div><b>로그인 form 치환코드 : </b>{loginActionUrl}</div>
		    <div><b>회원가입 form 치환코드 : </b>{memActionUrl}</div>
		    </div>
		</td>
	</tr>
	<!-- 무료보안서버 인증마크표시 -->
	<tr>
		<td rowspan="2">인증마크 표시</td>
		<td>
			<input type="radio" name="free_ssl_seal" value="0" style='border:0px' <?if($cfg['free_ssl_seal']=='0'){?>checked<?}?>>표시하지않음&nbsp;
			<input type="radio" name="free_ssl_seal" value="1" style='border:0px' <?if($cfg['free_ssl_seal']=='1'){?>checked<?}?>>표시함
		</td>
	</tr>
	<tr>
		<td>
			<div class="small extext">
				<div style="padding-top:5"><b>* 스킨 디자인 수정 및 변경에 따른 수동으로 인증마크 표시 적용방법</b></div>
				<div>- 스킨 소스를 변경하였거나, 스킨을 구매했을 경우, 또는 새로 스킨을 만든 경우를 위한 표시 방법입니다.</div>
				<div>- 스킨에 따라 하단소스의 Table구조가 다르니, 이 부분 유의해서 원하는 위치에 치환코드를 넣어주세요.</div>
				<div>- 위에서 인증마크 표시여부를 '표시함'으로 설정 후, </div>
				<div width=70% style='padding-left:10px'><font class=extext><a href='../design/codi.php?design_file=outline/footer/main_footer.htm' target=_blank><font class=extext><b>[디자인관리 > 전체레이아웃 디자인 > 하단디자인 > 메인용 하단파일 > html소스 직접수정]</b></font></a> 을 눌러<br> 치환코드 <font class=ver8 color=000000><b>{=displaySSLSeal()}</b></font> 를 삽입하세요. <a href='../design/codi.php?design_file=outline/footer/main_footer.htm' target=_blank><font class=extext_l>[바로가기]</font></a></font></div>
				<div width=70% style='padding-left:10px'><font class=extext><a href='../design/codi.php?design_file=outline/footer/standard.htm' target=_blank><font class=extext><b>[디자인관리 > 전체레이아웃 디자인 > 하단디자인 > html소스 직접수정]</b></font></a> 을 눌러<br> 치환코드 <font class=ver8 color=000000><b>{=displaySSLSeal()}</b></font> 를 삽입하세요. <a href='../design/codi.php?design_file=outline/footer/standard.htm' target=_blank><font class=extext_l>[바로가기]</font></a></font></div>
			</div>
		</td>
	</tr>
	</table>

	<table class=tb style='border-top:none;display:none' id="tableGodo">
	<col class=cellC><col class=cellL>
	<tr>
	<td style='border-top:none'>보안서버 사용여부</td>
	<td style='border-top:none'><font color=0a9a14><b>
	<?=$arSslStep[$sslStep]?>
	</b></font>

	<? if(in_array($sslStep,array('request','renewrequest'))) : ?>
	&nbsp;&nbsp;<font class=extext>(보안서버 안내 및 사용신청은 <a href="http://hosting.godo.co.kr/valueadd/ssl_service.php" target=_blank><font class=extext_l>[여기]</font></a> 를 클릭하세요)</font>
	<? endif; ?>
	</td>
	</tr>
	<tr>
		<td>보안서버 사용기간</td>
		<td><?if($cfg[ssl_sdate] && $cfg[ssl_edate]){?><?=$cfg[ssl_sdate]?> - <?=$cfg[ssl_edate]?><?}?></td>
	</tr>
	<tr>
		<td>보안서버 도메인</td>
		<td><font class=ver8><b>https://<?=$cfg[ssl_domain]?></b></font></td>
	</tr>
	<tr>
		<td>보안서버 포트</td>
		<td><font class=ver8><b><?=$cfg[ssl_port]?></b></font></td>
	</tr>
	<tr>
		<td>보안서버 적용범위</td>
		<td>
		로그인, 회원가입, 회원정보수정, 주문, 일대일문의, 제휴문의, 게시판 등의 페이지에서
		이용자의 개인정보 데이터가 암호화되어 전송됨<br>
		(솔루션내의 폴더 중 member, order 폴더 페이지들에 접근할 경우 SSL 보안이 적용)<br>
		<font style='color:red'>※주의 : 보안서버 사용이 적용되면 반드시 주문(결제포함) 테스트로
		정상적으로 주문이 이뤄지는지 확인하시기 바랍니다.</font>
		</td>
	</tr>
	<!-- 유료보안서버 인증마크표시 -->
	<tr>
		<td rowspan="5">인증마크 표시</td>
		<td>
			<input type="radio" name="ssl_seal" value="0" style='border:0px' onclick="seal_view(this.value)" <?if($cfg['ssl_seal']=='0'){?>checked<?}?>>표시하지않음
		</td>
	</tr>
	<tr>
		<td>
			<div style="width:100px;float:left;">
				<input type="radio" name="ssl_seal" value="g" style='border:0px' onclick="seal_view(this.value)"  <?if($cfg['ssl_seal']=='g'){?>checked<?}?>>GlobalSign : 
			</div>
			<div style="width:100px;float:left;">
				<select name="globalsign" id="globalsign" onchange="globalsign_sel(this.value)" disabled>
					<option value="a" <?if($cfg['globalsign']=='a' || $cfg['globalsign']==''){?>selected<?}?>>Alpha SSL</option>
					<option value="q" <?if($cfg['globalsign']=='q'){?>selected<?}?>>Quick SSL</option>
				</select>
			</div>
			<div id="globalsign_1" style="display:none;">
				<img src="../../lib/ssl/GlobalSign/alpha.jpg" alt="globalsign" width="100px" height="50px" />
			</div>
			<div id="globalsign_2" style="display:none;">
				<img src="../../lib/ssl/GlobalSign/quick.jpg" alt="globalsign" width="100px" height="50px" />
			</div>
		</td>
	</tr>
	<tr>
		<td>
			<div style="width:100px;float:left;">
				<input type="radio" name="ssl_seal" value="c" style='border:0px' onclick="seal_view(this.value)"  <?if($cfg['ssl_seal']=='c'){?>checked<?}?>>Comodo :
			</div>
			<div style="width:100px;float:left;">
				<select name="comodo" id="comodo" disabled>
					<option value="b" <?if($cfg['comodo']=='b'){?>selected<?}?>>베이직</option>
					<option value="g" <?if($cfg['comodo']=='g'){?>selected<?}?>>글로벌</option>
					<option value="p" <?if($cfg['comodo']=='p'){?>selected<?}?>>프로</option>
					<option value="a" <?if($cfg['comodo']=='a'){?>selected<?}?>>프리미엄</option>
					<option value="w" <?if($cfg['comodo']=='w'){?>selected<?}?>>와일드카드</option>
				</select>
			</div>
			<div id="comodo_1" style="display:none;">
				<img src="../../lib/ssl/Comodo/standard_logo/comodo_seal_52x63.png" alt="comodo" width="52px" height="63px" />
			</div>
		</td>
	</tr>
	<tr>
		<td>
			<div class="small extext">
				<div style="padding-top:5">쇼핑몰 하단 사업자정보 영역에 선택한 인증마크가 표시됩니다.</div>
				<div>*사용하시는 보안서버 서비스 상품이 표시하고자 하는 보안서버 인증마크와 같은지 확인하신 후, 등록해주세요.</div>
				<div style="padding-top:5">서비스상품 확인 및 수정은 <a href='http://www.godo.co.kr/mygodo/my_godo_secure_list.php' target="_blank"><font class="extext"><b>[마이고도 > 쇼핑몰관리 > 보안서버관리]</b></font></a>에서 가능합니다.</div>
				<div>서비스중이 아닌 인증마크 표시 등록시 문제가 발생 할 수 있습니다.</div>
				
			</div>
		</td>
	</tr>
	<tr>
		<td>
			<div class="small extext">
				<div style="padding-top:5"><b>* 스킨 디자인 수정 및 변경에 따른 수동으로 인증마크 표시 적용방법</b></div>
				<div>- 스킨 소스를 변경하였거나, 스킨을 구매했을 경우, 또는 새로 스킨을 만든 경우를 위한 표시 방법입니다.</div>
				<div>- 스킨에 따라 하단소스의 Table구조가 다르니, 이 부분 유의해서 원하는 위치에 치환코드를 넣어주세요.</div>
				<div>- 위에서 인증마크 표시여부를 '표시함'으로 설정 후, </div>
				<div width=70% style='padding-left:10px'><font class=extext><a href='../design/codi.php?design_file=outline/footer/main_footer.htm' target=_blank><font class=extext><b>[디자인관리 > 전체레이아웃 디자인 > 하단디자인 > 메인용 하단파일 > html소스 직접수정]</b></font></a> 을 눌러<br> 치환코드 <font class=ver8 color=000000><b>{=displaySSLSeal()}</b></font> 를 삽입하세요. <a href='../design/codi.php?design_file=outline/footer/main_footer.htm' target=_blank><font class=extext_l>[바로가기]</font></a></font></div>
				<div width=70% style='padding-left:10px'><font class=extext><a href='../design/codi.php?design_file=outline/footer/standard.htm' target=_blank><font class=extext><b>[디자인관리 > 전체레이아웃 디자인 > 하단디자인 > 하단기본타입 > html소스 직접수정]</b></font></a> 을 눌러<br> 치환코드 <font class=ver8 color=000000><b>{=displaySSLSeal()}</b></font> 를 삽입하세요. <a href='../design/codi.php?design_file=outline/footer/standard.htm' target=_blank><font class=extext_l>[바로가기]</font></a></font></div>
			</div>
		</td>
	</tr>
	</table>

	<div class="button">
	<input type=image src="../img/btn_register.gif">
	</div>

	</form>
	<?if($cfg['ssl_type']==''){?><script>clickNone()</script><?}?>
	<?if($cfg['ssl_type']=='godo'){?><script>clickGodo()</script><?}?>
	<?if($cfg['ssl_type']=='free'){?><script>clickFree()</script><?}?>
<? elseif($godo['webCode'] == 'webhost_outside'): ?>
	<form name=form method=post action="indb.php" >
	<input type=hidden name=mode value="ssl">

	<div class="title title_top">보안서버 인증서비스 신청/관리<span></span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=25')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a></div>

	<table class=tb>
	<col class=cellC><col class=cellL>
	<tr>
		<td>보안서버 사용여부</td>
		<td>
		<input type="radio" name="ssl_type" value="" style='border:0px' onclick="clickNone()"
		<?if($cfg['ssl_type']==''){?>checked<?}?>>사용안함
		&nbsp;
		<input type="radio" name="ssl_type" value="direct" style='border:0px' onclick="clickDirect()"	<?if($cfg['ssl_type']=='direct'){?>checked<?}?>>직접설정
		</td>
	</tr>
	</table>

	<table class=tb style='border-top:none;display:none' id="tableNone">
	<col class=cellC><col class=cellL>
	<tr>
		<td style='border-top:none'>사용기간</td>
		<td style='border-top:none'></td>
	</tr>
	<tr>
		<td>보안서버 도메인</td>
		<td></td>
	</tr>
	<tr>
		<td>보안서버 포트</td>
		<td></td>
	</tr>
	</table>

	<table class=tb style='border-top:none;display:none' id="tableDirect">
	<col class=cellC><col class=cellL>
	<tr>
		<td style='border-top:none'>사용기간</td>
		<td style='border-top:none'><input type=text name=ssl_sdate value="<?=$cfg['ssl_sdate']?>" onclick="calendar(event)" class=line> - <input type=text name=ssl_edate value="<?=$cfg['ssl_edate']?>" onclick="calendar(event)" class=line></td>
	</tr>
	<tr>
		<td>보안서버 도메인</td>
		<td><input type=text name='ssl_domain' value="<?=$cfg['ssl_domain']?>" class=line></td>
	</tr>
	<tr>
		<td>보안서버 포트</td>
		<td><input type=text name='ssl_port' value="<?=$cfg['ssl_port']?>" class=line></td>
	</tr>
	<tr>
		<td>보안서버 적용범위</td>
		<td>
		로그인, 회원가입, 회원정보수정, 주문 등의 페이지에서
		이용자의 개인정보 데이터가 암호화되어 전송됨<br>
		(솔루션내의 폴더 중 member, order, admin/login, admin/order, admin/member 폴더 페이지들에 접근할 경우 SSL 보안이 적용)<br>
		<font style='color:red'>※주의 : 보안서버 사용이 적용되면 반드시 주문(결제포함) 테스트로
		정상적으로 주문이 이뤄지는지 확인하시기 바랍니다.</font>
		</td>
	</tr>
	</table>

	<div class="button">
	<input type=image src="../img/btn_register.gif">
	</div>

	</form>

	<?if($cfg['ssl_type']==''){?><script>clickNone()</script><?}?>
	<?if($cfg['ssl_type']=='direct'){?><script>clickDirect()</script><?}?>
<? endif;?>


<table border="4" bordercolor="#dce1e1" style="border-collapse:collapse;margin-bottom:20px;" width="100%">
<tr><td style="padding:7px 0 10px 10px; color:#666666;">
<div style="padding-top:5px;"><font class="g9" color="#0074BA"><b>※ 보안서버(SSL)구축 의무 강화 안내</b></font></div>
<div style="padding-top:7px;">정보통신망법 개정에 따라서 보안서버(SSL) 구축 의무사항을 위반시 민원이 발생할 경우 <u>사전경고 없이 최고 3,000만원의 과태료가 부과됩니다.</u></div>
<ul style="margin:7px 0 0 25px;">
<li>시행일자 : 2012년 8월 18일</li>
<li>개정내용 : 보안서버 미구축 웹사이트에 대해 사전경고 없이 최대 3천만원의 벌금 부과</li>
<li>적용대상 : 온라인 쇼핑몰 및 회원가입/로그인/주문/결제/게시판 등의 이용과정에서 이름, 주민등록번호, 연락처 등을 취급하는 웹사이트</li>
</ul>
<div style="padding-top:7px;">운영하는 사이트의 개인정보 취급 여부를 반드시 확인하시고, 의무사항 적용 대상에 해당하는 사이트는 보안서버 사용을 필수로 설정하여 주세요.</div>
<div style="padding-top:10px;">"[무료보안서버 사용]으로 설정 시 주문/결제 등의 일부 페이지에서는 보안서버가 적용되지 않습니다.<br />
무료 보안서버를 이용중인 고객께서는 반드시 [유료 보안서버 사용] 으로 전환하시어, 불이익을 받지 않도록 조치해 주시기 바랍니다. 유료 보안서버는 설치기간이 다소(2일~7일) 소요될 수 있습니다."</div>
<div style="padding-top:5px;"><a href="http://www.godo.co.kr/news/notice_view.php?board_idx=722&page=2" target="_blank">[자세히보기]</a></div>
</table>


<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
    <tr><td><img src="../img/icon_list.gif" align="absmiddle">무료 보안서버(SSL) 서비스 설치시간은 요청 후 최대 하루 입니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">유료 보안서버(SSL) 서비스의 처리단계는 '신청대기' → '입금대기중' → '처리중' → '사용중' 입니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">유료 보안서버(SSL) 서비스는 인증서버의 세팅이 완료되면 '사용중'으로 변경됩니다.</td></tr>
</table>
</div>

<script type="text/javascript">
<!--
function seal_view(type) {

	var type2 = document.getElementById('globalsign').value;

	if (type == "g")	{
		document.getElementById('globalsign').disabled = false;
		document.getElementById('comodo').disabled = true;
		if(type2 == "a") {
			document.getElementById('globalsign_1').style.display = "";
			document.getElementById('globalsign_2').style.display = "none";
		} else if(type2 == "q") {
			document.getElementById('globalsign_1').style.display = "none";
			document.getElementById('globalsign_2').style.display = "";
		} else {
			
		}		
		document.getElementById('comodo_1').style.display = "none";
	} else if (type == "c")	{
		document.getElementById('comodo').disabled = false;
		document.getElementById('globalsign').disabled = true;
		document.getElementById('globalsign_1').style.display = "none";
		document.getElementById('globalsign_2').style.display = "none";
		document.getElementById('comodo_1').style.display = "";
		
	} else if (type == "0")	{
		document.getElementById('comodo').disabled = true;
		document.getElementById('globalsign').disabled = true;
		document.getElementById('globalsign_1').style.display = "none";
		document.getElementById('globalsign_2').style.display = "none";
		document.getElementById('comodo_1').style.display = "none";
	}
}	
function globalsign_sel(type) {
	if(type == "a" || type == "") {
		document.getElementById('globalsign_1').style.display = "";
		document.getElementById('globalsign_2').style.display = "none";
	} else {
		document.getElementById('globalsign_1').style.display = "none";
		document.getElementById('globalsign_2').style.display = "";
	}
}

//-->
</script>
<script>cssRound('MSG01')</script>
<? if ($cfg['ssl_seal'] == "g" && $cfg['globalsign'] == "a") { ?><script>seal_view('g');globalsign_sel("a");</script><? } ?>
<? if ($cfg['ssl_seal'] == "g" && $cfg['globalsign'] == "q") { ?><script>seal_view('g');globalsign_sel("q");</script><? } ?>
<? if ($cfg['ssl_seal'] == "c") { ?><script>seal_view('c');</script><? } ?>

<? include "../_footer.php"; ?>