<?

include "../_header.popup.php";
@include_once dirname(__FILE__) . "/webftp/webftp.class.php";
$webftp = new webftp;


### 스킨 및 이미지파일 정의
$easySkin = "../../skin/easy";
$easyImg = $easySkin . $_GET[imgpath];


### 경고 체크
$errMsg = array();
if ( !file_exists( $easySkin ) ) $errMsg[] = 'easy 스킨이 존재하지 않습니다.';
if ( !file_exists( $easyImg ) || empty( $_GET[imgpath] ) ) $errMsg[] = basename( $_GET[imgpath] ) . ' 파일이 스킨내에 존재하지 않습니다.';


### 파일정보
if ( !count( $errMsg ) ){
	$path_parts = @pathinfo( $easyImg );
	$path_parts['extension'] = strtolower( $path_parts['extension'] );

	$f_time = date( 'y-m-d H:i:s', @filemtime( $easyImg ) ); # 날짜

	## 파일크기
	$f_size = @filesize( $easyImg );

	if ( $f_size > 1024 ) $f_size = round( $f_size / 1024, 2 ) . ' Kb';	# KB
	else $f_size = $f_size . ' Byte';	# B

	## 그림크기
	$p_size = $p_view = '';

	if ( $webftp->chkSheet( $easyImg, $webftp->img_ext_str ) == true ){
		$tmp = @getimagesize( $easyImg );
		$p_size = $tmp[0] . '픽셀'.' × ' . $tmp[1] . '픽셀';
		$p_view = $webftp->ConfirmImage( $easyImg, $WSize="300", $HSize="200", $BorderSize=0, $IDName="", $vspace="0", $hspace="0" );
	}

	## 종류
	$f_kind = $webftp->ext_name[ $f_type ];
	if ( $webftp->chkSheet( $easyImg, $webftp->app_ext_str ) == true ) $f_kind = $webftp->ext_name[ $path_parts['extension'] ];
}
?>

<div style="margin-bottom:10px;">


<div class="title title_top">이미지교체<span>선택하신 이미지를 교체합니다</span></div>

<? if ( count( $errMsg ) ){ ?>
	<div id=warning style="color:#FF0000; margin-bottom:10px;"><li><?=implode( "</li><li>", $errMsg )?></li></div><!-- 경고메시지 -->
<? } ?>

<table cellpadding=0 cellspacing=1 border=0 bgcolor=EBEBEB>
<tr><td bgcolor=E8E8E8>
<table cellpadding=3 cellspacing=1 border=0 bgcolor=E8E8E8>
<col width=160><col width=400>
<tr>
  <td bgcolor=F6F6F6 align=center>파일명 및 경로</td>
  <td bgcolor=white><?=$_GET[imgpath]?></td>
</tr>
<tr>
  <td bgcolor=F6F6F6 align=center>파일정보</td>
  <td bgcolor=white>
    타입 : <?=$f_kind;?><br>
    이미지사이즈 : <?=$p_size;?><br>
    용량 : <?=$f_size;?><br>
    수정일 : <?=$f_time;?>
  </td>
</tr>
<? if ( $p_view ){?>
<tr>
  <td bgcolor=F6F6F6 align=center>이미지보기</td>
  <td bgcolor=white><?=$p_view?></td>
</tr>
<? } ?>
</table>
</td></tr></table>

<div style="padding-top:20"></div>

<form name=frmMember method=post action="./popup.easy_indb.php" enctype="multipart/form-data" onsubmit="return chkForm(this)">
<input type=hidden name=mode value="modify">
<input type=hidden name=imgpath value="<?=$_GET[imgpath]?>">

<table cellpadding=0 cellspacing=0 border=0 bgcolor=EBEBEB>
<tr><td bgcolor=E8E8E8>
<table cellpadding=2 cellspacing=1 border=0 bgcolor=E8E8E8>
<tr>
  <td bgcolor=F6F6F6 width=160 align=center><b>이미지교체</b></td>
  <td bgcolor=white width=400><input type=file name=userfile required label='이미지'</td>
</tr>
</table>
</td></tr></table>

<div style="margin-bottom:10px;padding-top:10;" class=noline align=center>
<input type="image" src="../img/btn_confirm_s.gif">
</div>
</form>

<script>table_design_load();</script>

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_tip>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">새로 교체할 <font color=EA0095>이미지의 파일명</font>은 반드시 <font color=EA0095>'영문 또는 숫자'</font>로 되어야 합니다. (한글파일명 안됨)</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">한번 변경된 이미지는 <font color=EA0095>원본이미지로 복구되지 않습니다.</font></td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">원본이미지는 고도몰의 <a href="http://enamoofreefix.godo.co.kr" target=blank><font color=EA0095><u>e나무 무료200 데모사이트</u></font></a>에서 해당 이미지를 다운로드 받아 다시 변경하세요.</td></tr>
</table>
</div>
<script>cssRound('MSG01','#F7F7F7')</script>
