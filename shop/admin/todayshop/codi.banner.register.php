<?

if (strpos(basename($_SERVER['HTTP_REFERER']), 'codi.banner.php') !== false || strpos(basename($_GET['returnUrl']), 'codi.banner.php') !== false){
	include "../_header.popup.php";
	$popupWin = true;
}
else {
	$location = "디자인관리 > 로고/배너 관리";
	include "../_header.php";
}

# 로고/배너위치 정의파일
if ( file_exists( $tmp = dirname(__FILE__) . "/../../conf/config.todayshop.banner_".$cfg['tplSkinTodayWork'].".php" ) ) @include $tmp;
else @include dirname(__FILE__) . "/../../conf/config.banner.php";

if(!$b_loccd['90']) $b_loccd['90']	= "메인로고";
if(!$b_loccd['91']) $b_loccd['91']	= "하단로고";
if(!$b_loccd['92']) $b_loccd['92']	= "메일로고";
if(!$b_loccd['93']) $b_loccd['93']	= "로고위치입력";
if(!$b_loccd['94']) $b_loccd['94']	= "로고위치입력";
if(!$b_loccd['95']) $b_loccd['95']	= "로고위치입력";

$returnUrl = ($_GET[returnUrl]) ? $_GET[returnUrl] : $_SERVER[HTTP_REFERER];

$parseUrl = parse_url( $returnUrl );
$listUrl = ( $returnUrl ? $parseUrl[query] : $_SERVER['QUERY_STRING'] );
$listUrl = ($popupWin === true ? 'codi.banner.php?' : 'codi.banner.php?') . preg_replace( "'(mode|sno)=[^&]*(&|)'is", '', $listUrl );

if (!$_GET[mode]) $_GET[mode] = "register";

if ($_GET[mode]=="modify"){
	$data = $db->fetch("select * from ".GD_BANNER." where sno='" . $_GET['sno'] . "'",1);

	# WebFTP 선언
	include dirname(__FILE__) . "/../design/webftp/webftp.class.php";
	$webftp = new webftp;
	$webftp->ftp_path = str_replace( $_SERVER['SCRIPT_NAME'], "", $_SERVER['SCRIPT_FILENAME'] ) . $cfg['rootDir'] . '/data/skin_today/' . $cfg['tplSkinTodayWork']; # 스킨경로
}
?>
<SCRIPT language=javascript><!--
/*-------------------------------------
 설정폼 체크
 fobj : form object
-------------------------------------*/
function fm_save( fobj ){

	if ( fobj.mode.value!="modify" && fobj['img_up'].value == "" ){

		alert( "로고/배너이미지가 입력되지 않았습니다." );
		fobj['img_up'].focus();
		return false;
	}

	if (!chkForm(fobj)) return false;
}
//--></SCRIPT>



<div id=goods_form>

<form method=post action="indb.codi.banner.php" enctype="multipart/form-data" onsubmit="return fm_save(this)">
<input type=hidden name=mode value="<?=$_GET[mode]?>">
<input type=hidden name=sno value="<?=$_GET[sno]?>">
<input type=hidden name=returnUrl value="<?=$returnUrl?>">

<div class="title title_top">로고/배너정보<span>연결주소를 사용 안하려면 "nolink" 라고 입력, 또는 빈공간으로 두세요. &nbsp;&nbsp;&nbsp;<a href="javascript:manual('<?=$guideUrl?>board/view.php?id=design&no=6')"><img src="../img/btn_bn_manual.gif" align=absmiddle></a></span></div>
<? include "../proc/warning_disk_msg.php"; # not_delete  ?>
<?=$workSkinTodayStr?>
<table class=tb>
<col class=cellC><col class=cellL>

<tr>
	<td nowrap>로고/배너위치 설정</td>
	<td nowrap>
	<SELECT NAME="loccd" required label="로고/배너위치">
	<option value="">↓ 로고/배너위치를 선택하세요.</option>
	<optgroup label="-- 로고위치 --"></optgroup>
	<?
	# 로고용
	foreach ( $b_loccd as $lKey => $lVal ){
		if( $lKey < 90 ) continue;
	?>
	<option value="<?=$lKey?>" <?=$lKey==$data['loccd']?" selected":""?>><?=$lVal?></option>
	<?}?>
	<optgroup label="-- 베너위치 --"></optgroup>
	<?
	# 베너용
	foreach ( $b_loccd as $k => $v ){
		if( $k >= 90 ) continue;
	?>
	<option value="<?=$k?>" <?=$k==$data['loccd']?" selected":""?>><?=$v?></option>
	<?}?>
	</SELECT>
	<a href="javascript:popupLayer('../todayshop/codi.banner.loccd.php',780,600);"><img src="../img/btn_bangroup.gif" border=0 align=absmiddle></a> <font class=extext>지금 등록할 배너의 배너위치를 아직 안잡았다면 배너위치를 잡으세요</font>
	</td>
</tr>
<tr>
	<td nowrap>연결주소(링크)</td>
	<td nowrap>
	<input name="linkaddr" type="text" value="<?=$data['linkaddr']?>" style="width:300;" class="line">
	<select name="target">
	  <option value="">↓ 타겟을 선택하세요.</option>
	  <option value="_blank" <?if($data['target'] == "_blank") echo"selected";?>>새창</option>
	  <option value="" <?if($data['target'] == "") echo"selected";?>>현재창</option>
	</select>
	</td>
</tr>
<tr>
	<td nowrap>이미지</td>
	<td nowrap>
	<input type="file" name="img_up" class="line"><input type="hidden" name="img" value="<?=$data['img']?>">
	<a href="javascript:webftpinfo( '<?=( $data['img'] != '' ? '/data/skin_today/' . $cfg['tplSkinTodayWork'] . '/img/banner/' . $data['img'] : '' )?>' );"><img src="../img/codi/icon_imgsizeview.gif" border="0" alt="이미지 보기" align="absmiddle"></a>
	<? if ( $data['img'] != '' ){ ?>&nbsp;&nbsp;<span class="noline"><input type="checkbox" name="img_del" value="Y">삭제</span><? } ?>
	<? if ( $data['img'] != '' ){ echo '<div style="margin-top:3px;">' . $webftp->confirmImage( "../../data/skin_today/" . $cfg['tplSkinTodayWork'] . "/img/banner/" . $data['img'],300,100,"0") . '</div>'; }?> </td>
</tr>
</table>

<div style="padding: 3 0 0 132"><font class=extext>* 이미지 파일명은 반드시 <b>영문과 숫자만 가능</b>합니다. <b>한글</b>이 들어가면 안됩니다! 예) <font class=ver811 color=627dce><b>abc.jpg</b> 또는 <b>abc123.gif</b> 또는 <b>123.jpg</b></font></font></div>
<div style="padding: 3 0 0 132" class="extext">* 단, 플래쉬 배너를 등록할 경우 이미지확장자는 <b>jpg</b>로만 등록하셔야 합니다.</div>
<div class=button>
<input type=image src="../img/btn_<?=$_GET[mode]?>.gif">
<a href="<?=$listUrl?>"><img src='../img/btn_list.gif'></a>
</div>

</form>
</div>


<table cellpadding=0 cellspacing=0 bgcolor=fafafa width=100%>
<tr><td style="padding: 5 15 15 15; text-align: justify">
<div><font color=EA0095><b>***</b> 위 연결주소란에 주소를 입력할 때 유의하실 점! (<b>필독</b>) <b>***</b></font></div>
<font class=small1 color=555555>
<div style="padding-top:8">- 내 쇼핑몰이 아닌 <font color=0098a2>다른 사이트로 이동</font> 할 때에는 <font class=ver811 color=333333><b>http://www.naver.com</b></font> 이렇게 반드시 모든 주소를 넣어야 합니다. 이것을 <font color=0098a2>절대경로</font>라고 합니다.</div>
<div style="padding-top:5">- 그러나 <font color=0098a2>내 쇼핑몰 안에 있는 페이지로 이동</font> 할 때에는 가고자 하는 페이지의 주소에서 <font color=0098a2>도메인을 제외한 나머지 주소를 복사</font>하여 넣습니다. 이것이 <font color=0098a2>상대경로</font>입니다.</div>
<div style="padding-top:5">- 예를 들어 배너를 눌러서 <font color=0098a2>회사소개페이지</font>로 가고자 할 때, 그 주소는 <font class=ver811 color=333333><b>http://www.test.co.kr/shop/service/company.php</b></font> 가 되고,</div>
<div style="padding:3 0 0 8">여기서 도메인을 제외한 나머지주소는 <font class=ver811 color=333333><b>/shop/service/company.php</b></font> 이 됩니다. <font color=0098a2>이 부분만 연결주소란에 입력</font>하면 됩니다.</div>
<div style="padding-top:5">- 하나더 예를 들면, 로고/배너클릭시 특정한 <font color=0098a2>카테고리페이지</font>로 이동하고자 하면, 입력할 주소는 <font class=ver811 color=333333><b>/shop/goods/goods_list.php?category=001</b></font> 이렇게 됩니다. </div>
<div style="padding-top:5">- 다시 설명하면, <font color=0098a2>내 쇼핑몰의 다른 페이지로 이동</font>할때에는 반드시 <font color=0098a2>상대경로</font>, 즉 <font color=0098a2>도메인을 제외한 나머지주소를 복사</font>해서 입력하세요! </div>
<div style="padding-top:5">- <font color=0098a2>다른 사이트</font>로 가려면 <font color=0098a2>모든 주소 (절대경로)</font> 를 넣고, <font color=0098a2>내 쇼핑몰페이지</font>로 이동하려면 꼭 <font color=0098a2>도메인을 제외한 나머지주소 (상대경로)</font> 를 복사해서 입력하세요.</div>
<div style="padding-top:5">- 이렇게 절대경로와 상대경로를 구분하여 링크주소를 넣는 방법은 소스에서 HTML 코딩으로 링크를 걸 때에도 마찬가지입니다.</div>
</td></tr></table>


<div style="padding-top:5"></div>

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align=absmiddle>이미지 파일명은 한글파일명은 안되며, 반드시 영문/숫자로 이루어져야 합니다. 예) abc123.jpg</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>




<SCRIPT LANGUAGE="JavaScript" SRC="../proc/warning_disk_js.php"><!-- not_delete --></SCRIPT>
<?
if ($popupWin === true){
	echo '<script>table_design_load();</script>';
}
else {
	include "../_footer.php";
}
?>