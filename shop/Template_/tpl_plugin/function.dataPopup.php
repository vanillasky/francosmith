<?php

/* Return Popup Data Function */

function dataPopup(){

	global $db,$cfg;

	@include dirname(__FILE__) . "/../../conf/design_skin_" . $cfg['tplSkin'] . ".php";

	$popup = array();

	$tmp = array_keys( $design_skin );
	$keys = array_ereg( "'^popup/[^/]*$'si", $tmp );

	foreach ( $keys as $filenm ){

		$file = $design_skin[$filenm];

		# 팝업창 미사용이 경우
		if ( $file['popup_use'] != 'Y' ) continue;

		# 팝업창 기간 및 일자 설정
		if ( $file['popup_sdt'] && $file['popup_sdt'] != "00000000" && $file['popup_edt'] && $file['popup_edt'] != "00000000" ){
			if( $file['popup_sdt'] > date("Ymd") || $file['popup_edt'] < date("Ymd") ){
				continue;
			}else{
				# 시간 설정이 되어있는 경우 체크
				if ( $file['popup_stime'] && $file['popup_stime'] != "0000" && $file['popup_edt'] && $file['popup_edt'] != "0000" ){
					# 종속인경우
					if( $file['popup_dt2tm'] == "Y" ){
						if( ($file['popup_sdt'].$file['popup_stime']) > date("YmdHi") || ($file['popup_edt'].$file['popup_etime']) < date("YmdHi") ) continue;
					}else{
						if( $file['popup_stime'] > date("Hi") || $file['popup_etime'] < date("Hi") ) continue;
					}
				}
			}
		}

		$popup[] = array( 'file'=>$filenm, 'code'=>preg_replace( array( "'^popup/'si","'.htm$'si","'[^0-9a-zA-Z]+'si" ), "", $filenm ), 'name'=>$file[text], 'width'=>$file[popup_sizew], 'height'=>$file[popup_sizeh], 'top'=>$file[popup_spotw], 'left'=>$file[popup_spoth], 'type'=>$file[popup_type] );
	}

	return $popup;
}
?>