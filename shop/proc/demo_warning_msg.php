<?
/*------------------------------------------------------------------------------
ⓒ Copyright 2005, Flyfox All right reserved.
@파일내용: 데모경고메시지
@수정내용/수정자/수정일:
------------------------------------------------------------------------------*/

{ // 데모 경고 이미지
	$demo_site = array( 'dev2.godo.co.kr', 'enamoofix.godo.co.kr', 'enamooself.godo.co.kr', 'enamooselffix.godo.co.kr', 'enamoofree.godo.co.kr', 'enamoofreefix.godo.co.kr' );
	if ( in_array( $_SERVER['HTTP_HOST'], $demo_site ) ){
		$_COOKIE['shop_authenticate'] = 'Y';
		$demo_img = (strpos($_SERVER['HTTP_HOST'], 'fix') !== false) ? "<img src=\"../admin/img/demo_warning.gif\" />" : "<a href=\"http://" . str_replace('.godo', 'fix.godo', $_SERVER['HTTP_HOST']) . "\" target=\"_blank\"><img src=\"../admin/img/demo_warning2.gif\" border=\"0\" /></a>";
		echo "<div align='center'>$demo_img</div>";
	}
}

if ( $_COOKIE['shop_authenticate'] != 'Y' ) { // 운영 컨트롤

	$shop_authenticate = 'Y'; // 인증 초기값

	### 고도몰 환경코드
	$file = dirname(__FILE__)."/../conf/godomall.cfg.php";
	if (!is_file($file)) msg("고도몰 설정파일을 등록하세요",1);
	$file = file($file);
	$godo = decode($file[1],1);

	if ( preg_match( "/^rental_mx/i", $godo['ecCode'] ) ){ // 임대형
		if ( $godo['freeType'] == "y" ){
			$g_shopstop_day		= 1;		# 사용자화면 제한 개시일 - 무료사용자
		}else{
			$g_shopstop_day		= 30;		# 사용자화면 제한 개시일 - 임대형사용자
		}
	}

	### 운영 체크
	if ( preg_match( "/^rental_mxfree/i", $godo['ecCode'] ) ){ // 무료형
		$nowDay = betweenDate(date('Ymd'),$godo['edate']);

		if ( $godo['edate'] != 0 && $nowDay <= 0 ){ # 종료회원 & 사용자화면 제한 개시
			$shop_authenticate = 'N'; // 인증여부
		}
	}else if ( preg_match( "/^rental_mx/i", $godo['ecCode'] ) ) { // 임대형
		$nowDay = betweenDate(date('Ymd'),$godo['edate']);

		if ( $nowDay <= ( 0 - $g_shopstop_day ) ){ # 사용자화면 제한 개시
			$shop_authenticate = 'N'; // 인증여부
		}
	}

	### 쿠키 저장
	setCookie('shop_authenticate',$shop_authenticate,0,'/');

	### 접속 오류 메시지 출력
	if ( $shop_authenticate == 'N' ){

		$err_msg = "<br>쇼핑몰 이용에 관한 문의는 ";
		if ( $cfg['adminEmail'] ) $err_msg .= "<a href='mailto:" . $cfg['adminEmail'] . "'>" . $cfg['adminEmail'] . "</a>,<br>";
		if ( $cfg['compPhone'] ) $err_msg .= $cfg['compPhone'];
		$err_msg .= " 으로 해주시기 바랍니다.<br><br>";
?>
<style><!--
body {margin:0}
body,table {font:12px dotum}
img	{border:0}

a	{text-decoration:none;color:#000000}
a:hover {text-decoration:none;color:#007FC8}
--></style>

<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td>
	  <table width="100%" height="501" border="0" cellpadding="0" cellspacing="0">
	    <tr>
	  	  <td>&nbsp;</td>
	    </tr>
	  </table>
	</td>
	<td width="501">
	  <table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
	    <tr>
	  	  <td>
		    <table width="100%" border="0" cellpadding="0" cellspacing="0">
		      <tr>
		    	<td></td>
		      </tr>
			  <tr>
		    	<td height="231" align="center" valign="top" style="padding-top:20px;">현재 쇼핑몰 연결이 원할하지 않습니다.<br>
				<?=$err_msg?>
				</td>
		      </tr>
		    </table>
		  </td>
	    </tr>
	  </table>
	<td>
	  <table width="100%" height="501" border="0" cellpadding="0" cellspacing="0">
	    <tr>
	  	  <td>&nbsp;</td>
	    </tr>
	  </table>
	</td>
  </tr>
</table>

<?
		exit();
	}
}
?>